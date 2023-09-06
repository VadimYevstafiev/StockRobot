<?php
  /**
   * ManageActiveBidsFunctions: Трейт, определяющий функции управления активными заявками торговца
   */
  trait ManageActiveBidsFunctions {
      /**
      * Функция управления активными заявками торговца
      */
     protected function ManageActiveBids() {
        $output = 0;
        $this->protocol->AddMessage("mab", "st");
        $this->protocol->AddMessage("mb", "gd");
        $data = $this->bidsJournal->GetData();
        $journalData = $this->bidsJournal->SortByState($data);
        $directionData = $this->bidsJournal->SortByDirection($journalData["active"]);
        foreach ($directionData as $direction => $values) {
           $this->protocol->AddMessage("mb", array("dir" => $direction));
           if (!empty($values)) {
              $message = array("scn"  => $values[0]["BidID"],
                               "st1"  => array($values[0]["State"],
                                               $values[0]["Revnumber"]),
                               "ob"   => array($values[0]["Amountin"],
                                               $values[0]["Amountout"],
                                               $values[0]["Exchamountin"],
                                               $values[0]["Exchamountout"],
                                               $values[0]["Rate"]));
              $this->protocol->AddMessage("bj", $message);
              $output = $this::MakeResolution($values[0]);
           } else {
              $this->protocol->AddMessage("mab", "nac");
           }
        }
        $this->bidsJournal->SaveLegend();
        $this->summary = $output;
     }
      /**
      * Функция принятия решения по торгам
      *
      * @param  array    $bidsdata      Массив данных о активной заявке
      *
      * @return string   $output       Код решения по торгам:
      *                                 3 - Ожидание позиции
      *                                 4 - Удержание позиции
      *                                 5 - Закрытие заявки в режиме мейкера
      *                                 6 - Закрытие заявки в режиме тейкера
      */
     protected function MakeResolution($bidsdata) {
        if ($bidsdata["Activetime"] <= $this->expertdata["timestamp"]) {
           if ($this->expertdata["Summary"] == 0) {
              $output = $this::FlatResolution($bidsdata);
           } else {
              $output = $this::TrendResolution($bidsdata);
           } 
        } else {
           $output = 3;
           $this->protocol->AddMessage("mab", "nch");
        }
        return $output;
     }
      /**
      * Функция принятия решения по торгам (если рекомендация эксперта - флэт) 
      *
      * @param  array    $bidsdata      Массив данных о активной заявке
      *
      * @return string   $output        Код решения по торгам:
      *                                 5 - Закрытие заявки в режиме мейкера
      */
     protected function FlatResolution($bidsdata) {
        $this::DeclareCloseBid($bidsdata["BidID"], $bidsdata["State"]);
        $expert = $this->expertdata[$bidsdata["Direction"]];
        $listener = $this->listenerdata[Configurations::GetTradingtype("Trader")][$bidsdata["Direction"]];
        switch ($bidsdata["Direction"]) {
           case  "Bid": 
              if ($expert < $listener) {
                 $rate = $listener;
              } else {
                 $rate = $expert;
              }
              break;
           case  "Ask": 
              if ($expert > $listener) {
                 $rate = $listener;
              } else {
                 $rate = $expert;
              }
              break;
        }   
        $this::ChangeBidsRate($bidsdata, $rate);
        $output = 5;
        return $output;
     }
      /**
      * Функция принятия решения по торгам (если рекомендация эксперта - тренд) 
      *
      * @param  array    $bidsdata      Массив данных о активной заявке
      *
      * @return string   $output       Код решения по торгам:
      *                                 3 - Ожидание позиции
      *                                 4 - Удержание позиции
      *                                 5 - Закрытие заявки в режиме мейкера
      *                                 6 - Закрытие заявки в режиме тейкера
      */
     protected function TrendResolution($bidsdata) {
        $rate = $this->listenerdata[Configurations::GetTradingtype("Trader")];
        $stop = $this->listenerdata[Configurations::GetTradingtype("Expert")];
        if ($this->expertdata["Summary"] > 0) {            //Рекомендация: закрыть/держать длинную позицию (заявку Bid)
           switch ($bidsdata["Direction"]) {
              case  "Bid":
                 if ($this->expertdata["Summary"] == 2) {          //Рекомендация: закрыть длинную позицию (заявку Bid)
                    $this::DeclareCloseBid($bidsdata["BidID"], $bidsdata["State"]);
                    $this::ChangeBidsRate($bidsdata, $rate["Bid"]);
                    $output = 5;
                 } else {                                          //Рекомендация: держать длинную позицию (заявку Bid)
                    if ($this->expertdata["Stop"] < $stop["Ask"]) {
                       $this::KeepPosition($bidsdata);
                       $output = 4;
                    } else {
                       $this->protocol->AddMessage("bj", "cs");
                       $output = $this::ReverseBid($bidsdata, $rate["Bid"]);
                    }
                 }
                 break;
              case  "Ask":                                                   
                 if (($bidsdata["State"] == "20") && ($this->expertdata["Summary"] == 2)) {
                    $this->protocol->AddMessage("bj", array("st3"  => array(20)));
                    $output = 3;
                 } else {
                    $this->protocol->AddMessage("bj", "rv");
                    $output = $this::ReverseBid($bidsdata, $rate["Ask"]);
                 }
                 break;
           }    // end swith
        } else if ($this->expertdata["Summary"] < 0) {    //Рекомендация: закрыть/держать короткую позицию (заявку Ask)
           switch ($bidsdata["Direction"]) {
              case  "Bid":
                 if (($bidsdata["State"] == "20") && ($this->expertdata["Summary"] == -2))  {  
                    $this->protocol->AddMessage("bj", array("st3"  => array(20)));
                    $output = 3;
                 } else {
                    $this->protocol->AddMessage("bj", "rv");
                    $output = $this::ReverseBid($bidsdata, $rate["Bid"]);
                 }
                 break;
              case  "Ask":
                 if ($this->expertdata["Summary"] == -2) {        //Рекомендация: закрыть короткую позицию (заявку Ask)
                    $this::DeclareCloseBid($bidsdata["BidID"], $bidsdata["State"]);
                    $this::ChangeBidsRate($bidsdata, $rate["Ask"]);
                    $output = 5;
                 } else {                                         //Рекомендация: держать короткую позицию (заявку Ask)
                    if ($this->expertdata["Stop"] > $stop["Bid"]) {
                       $this::KeepPosition($bidsdata);
                       $output = 4;
                    } else {
                       $this->protocol->AddMessage("bj", "cs");
                       $output = $this::ReverseBid($bidsdata, $rate["Ask"]);
                    }
                 }
                 break;
           }   // end swith
        }
        return $output;
     }
      /**
      * Функция объявления о переводе заявки в режим закрытия 
      *
      * @param string   $bidID        Номер заявки
      * @param string   $state        Код состояния заявки
      */
     protected function DeclareCloseBid($bidID, $state) {
        if ($state == "22") { 
           $message = array("st3"  => array(22));
           $this->protocol->AddMessage("bj", $message);
        } else {
           $message = array("st2"  => array(22));
           $this->protocol->AddMessage("bj", $message);
           $this->bidsJournal->AddToLegend($bidID, array("bj" => $message));
           $this->bidsJournal->CorrectScript($bidID, array("State" => "22"));
        }
     }
      /**
      * Функция перевода заявки в режим разворота 
      *
      * @param  array    $bidsdata      Массив данных о активной заявке
      * @param  double   $rate          Значение курса
      *
      * @return string   $output        Код решения по торгам:
      *                                 5 - Закрытие заявки в режиме мейкера
      *                                 6 - Закрытие заявки в режиме тейкера
      */
     protected function ReverseBid($bidsdata, $rate) {
        $message = array("st1"  => array($bidsdata["State"],
                                         $bidsdata["Revnumber"]));
        $this->protocol->AddMessage("bj", $message);
        $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("bj" => $message));
        if ($bidsdata["Revnumber"] < $this->makecount) {
           $message = array("st2"  => array(23, $bidsdata["Revnumber"]));
           $this->protocol->AddMessage("bj", $message);
           $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("bj" => $message));
           $output = $this::ChangeBidsRate($bidsdata, $rate);
           $toJournal = array("State" => 23, "Revnumber" => ($bidsdata["Revnumber"] + 1));
           $this->bidsJournal->CorrectScript($bidsdata["BidID"], $toJournal);
           $output = 5;
        } else {
           $message = array("st2"  => array(24));
           $this->protocol->AddMessage("bj", $message);
           $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("bj" => $message));
           $this->bidsJournal->CorrectScript($bidsdata["BidID"], array("State" => "24"));
           $this::BuyAlienBid($bidsdata["BidID"], $bidsdata["Direction"], $bidsdata["Amountin"]);
           $output = 6;
        }
        return $output;
     }
      /**
      * Функция перевода заявки в режим удерживания позиции
      *
      * @param  array    $bidsdata      Массив данных о активной заявке
      */
     protected function KeepPosition($bidsdata) {
        $rate = $this->CalculateSleepRate($bidsdata["Direction"]);
        if ($bidsdata["State"] == "21") { 
           $message = array("st3"  => array($bidsdata["State"]));
           $this->protocol->AddMessage("bj", $message);
           $this::ChangeBidsRate($bidsdata, $rate);
        } else {
           $message = array("st2"  => array(21));
           $this->protocol->AddMessage("bj", $message);
           $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("bj" => $message));
           $this::ChangeBidsRate($bidsdata, $rate);
           $this->bidsJournal->CorrectScript($bidsdata["BidID"], array("State" => "21"));
        }
     }
  }
?>

