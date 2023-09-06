<?php
  /**
   * ActiveBidsFunctions: Трейт, определяющий операции с активными заявками
   */
  trait ActiveBidsFunctions {
      /**
      * Функция изменения курса заявки 
      *
      * @param array    $bidsdata     Массив данных о активной заявке
      * @param double   $rate         Новое значение курса
      *
      * @return string  $output       Код решения по торгам:
      *                               0 - Заявка остается без изменений
      *                               1 - Курс заявки изменен
      */
     protected function ChangeBidsRate($bidsdata, $rate) {
        $rate = $this::SmartCalculateRate($bidsdata["Direction"], $bidsdata["Amountin"], $rate);
        $message = array("chr" => $rate);
        $this->protocol->AddMessage("wm", $message);
        $currents = WMbidList::SendQuery($this->indexis["general"][$bidsdata["Direction"]]);
        if ($currents[0]["id"] == $bidsdata["BidID"]) {
           $this->protocol->AddMessage("wm", "ift");
           $output = 0;
        } else {
           $this->protocol->AddMessage("wm", "nft");
           if ($bidsdata["Amountout"] == $this::CalculateAmountout($bidsdata, $rate)) {
              $this->protocol->AddMessage("wm", "ncr");
              $output = 0;
           } else {
              $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("wm" => $message));
              switch ($bidsdata["Direction"]) {
                 case "Bid":
                    $ratio = (($bidsdata["Rate"] / $rate) - 1) * 100;      
                    break;
                 case "Ask":
                    $ratio = (($rate / $bidsdata["Rate"]) - 1) * 100;
                    break;
              }
              if ($ratio > 1) {
                 $this->protocol->AddMessage("wm", "rat");
                 $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("wm" => "rat"));
                 $this::PreviousChange($bidsdata, $rate);
              }
              $message = array("mcr" => $rate);
              $this->protocol->AddMessage("wm", $message);
              $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("wm" => $message));
              $this::ExecuteChange($bidsdata, $rate);
              $output = 1;
           }
        }
        return $output;
     }
      /**
      * Функция предварительного изменения курса заявки 
      *
      * @param array    $bidsdata     Массив данных о активной заявке
      * @param double   $rate         Новое значение курса
      */
     protected function PreviousChange($bidsdata, $rate) {
        $service = $this::CalculateAmountout($bidsdata, $rate) + 0.01;
        $service = $this::CalculateRate($bidsdata["Direction"], $bidsdata["Amountin"], $service);
        $message = array("pcr" => $service);
        $this->protocol->AddMessage("wm", $message);
        $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("wm" => $message));
        $this::ExecuteChange($bidsdata, $service);
     }
      /**
      * Служебная функция изменения курса заявки 
      *
      * @param array    $bidsdata     Массив данных о активной заявке
      * @param double   $rate         Новое значение курса
      *
      * @return string  $output       Код решения по торгам:
      *                               0 - Заявка остается без изменений
      *                               1 - Курс заявки изменен
      */
     protected function ExecuteChange($bidsdata, $rate) {
        switch ($bidsdata["Direction"]) {
           case "Bid":
              $curstype = "1";
              break;
           case "Ask":
              $curstype = "0";
              break;
        }
        $operid = $bidsdata["BidID"];
        $result = WMchangeBidsRate::SendQuery($this->wmid, $this->signer, $operid, $curstype, $rate);
        if ($result["retval"] == 0) {
           $data = $this::GetMyBidsList("trade", $operid);
           $message = array("ichr" => $operid);
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
           $this->bidsJournal->ChangeScript($data[$operid]);
        } else {
           $message = array("fchr" => $bidsdata["BidID"],
                            "err" => array($result["retval"], $result["retdesc"]));
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
        }
     }
     /**
      * Функция скупки встречных заявок
      *
      * @param integer $operid          Номер новой заявки, c которой будет производиться скупка встречных заявок
      * @param string  $direction       Тип обмена в заявке $isxtrid
      * @param double  $amountin        Сумма выставленная к обмену в заявке, c которой будет производиться скупка встречных заявок
      *
      * @return array                   Массив результатов операции
      */
     protected function BuyAlienBid($operid, $direction, $amountin) {
        $exchtype = $this->indexis["general"][$this::GetOppositeDirection($direction)];
        $criteria = FALSE;
        $used = array();
        while (!$criteria) {
           $aliens = WMbidList::SendQuery($exchtype);
           $data = $this::GetMyBidsList("all", $operid);
           $i = 0;
           $bool = TRUE;
           while (($bool) && (!$criteria)) {
              foreach ($used as $values) {
                 if ($aliens[$i]["id"] == $values) {
                    $bool = FALSE;
                 }
              }
              if ($bool) {
                 $message = array("ald" => array($aliens[$i]["id"], 
                                                 $aliens[$i]["querydate"],
                                                 $this::ConvertData($aliens[$i]["amountin"]),
                                                 $this::ConvertData($aliens[$i]["amountout"])));
                 $this->protocol->AddMessage("wm", $message);
                 $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
                 $this->protocol->AddMessage("wm", "mac");
                 $this->bidsJournal->AddToLegend($operid, array("wm" => "mac"));
                 $this::PreviousChange($data[$operid], $data[$operid]["Rate"]);
                 $result = WMbuyAlienBid::SendQuery($this->wmid, $this->signer, $operid, $aliens[$i]["id"]);
                 if ($result["retval"] == 0) {
                    $message = array("iba" => $aliens[$i]["id"]);
                    $this->protocol->AddMessage("wm", $message);
                    $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
                    $used[] = $aliens[$i]["id"];
                 } else {
                    $bool = FALSE;
                    $message = array("nba" => $aliens[$i]["id"],
                                     "err" => array($result["retval"], $result["retdesc"]));
                    $this->protocol->AddMessage("wm", $message);
                    $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
                 }
                 $data = $this::GetMyBidsList("all", $operid);
                 if ($data[$operid]["State"] == 40) {
                    $criteria = TRUE;
                 }
                 $message = array("rst" => array($operid, $data[$operid]["Amountin"]));
                 $this->protocol->AddMessage("wm", $message);
                 $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
              }
              $i++;
           }
        }
        $data = $this::GetMyBidsList("all", $operid);
        $value = $data[$operid];
        $value["Closetime"] = (string) (new DateTime)->getTimestamp();
        $value["Closetype"] = 2;
        $this->bidsJournal->CloseScript($value);
     }
  }
?>

