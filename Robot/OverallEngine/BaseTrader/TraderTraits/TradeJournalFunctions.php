<?php
  /**
   * TradeJournalFunctions: Трейт, определяющий служебные функции обновления журнала сделок
   */
  trait TradeJournalFunctions {
     /**
      * Общая функция обновления журнала сделок 
      *
      */
     protected function RefreshTradeJournal() {
        $this->protocol->AddMessage("tj", "rj");
        $data = $this->GetBidsData();
        if ($data[0] != 0) {
           $this->protocol->AddMessage("tj", "nrj");
           return;
        }
        $bidsData = $data[1];
        unset($data);

        $this->protocol->AddMessage("tj", "gt");
        $tradeData = $this->tradeJournal->GetOpenTrade();
        if (empty($tradeData)) {
           $this->protocol->AddMessage("tj", "ngt");
           $this->NewTrade($bidsData, 0);
           return;
        }

        if (($bidsData["Open"]["BidID"] == $tradeData["OpenID"]) || ($bidsData["Open"]["BidID"] == $tradeData["CloseID"])) {
           $this->protocol->AddMessage("tj", "otr");
        } else if ($bidsData["Open"]["BidID"] == $tradeData["NextID"]) {
           $this->NormalClose($tradeData, $bidsData);
        } else {
           $items = $this->bidsJournal->SortByDirection($data["close"]);
           $criteria = 0;
           $keys = array("OpenID", "CloseID");
           foreach ($keys as $value) {
              for ($i = 0; $i < count($data["close"]); $i++) {
                 if (($tradeData[$value] ==  $data["close"][$i]["BidID"]) && ($data["close"][$i]["Closetype"] < 3)) {
                    $criteria++;
                 }
              }
           }
           if ($criteria != 2) { 
              $this->TroubleClose($tradeData, $bidsData);
           }
        }
     }
     /**
      * Функция получения массива данных о заявках  
      *
      * @return array  $output[0]    Коды сооветствия 
      *                              если $output[0] == 0 - активная заявка одна 
      *                              если $output[0] == 1 - активных заявок нет
      *                              если $output[0] == 2 - активных заявок больше одной
      * @return array  $output[1]    Массив данных о заявках  
      *
      */
     protected function GetBidsData() {
        $this->protocol->AddMessage("tj", "gd");
        $data = $this->bidsJournal->GetData();
        $data = $this->bidsJournal->SortByState($data);

        if (count($data["active"]) == 0) {
           $this->protocol->AddMessage("tj", "nac");
           $output[0] = 1;
           return $output;
        }

        if (count($data["active"]) > 1) {
           $this->protocol->AddMessage("tj", "mac");
           $output[0] = 2;
           return $output;
        }

        $output[0] = 0;
        $output[1]["Open"] = $data["active"][0];
        if ($output[1]["Open"]["Direction"] == $this->indexis["direct"]) {
           foreach ($data["base"] as $item) {
              if ($item["Direction"] == $this->indexis["direct"]) {
                 $output[1]["Next"] = $item;
              } else {
                 $output[1]["Close"] = $item;
              }
           }
        }
        return $output;
     }
     /**
      * Функция записи данных о новой открытой сделке 
      *
      * @param array    $bidsdata     Массив данных о активной заявке
      * @param double   $preAccum     Значение нарастающего итога по предыдущей сделке
      *
      */
     protected function NewTrade($bidsData, $preAccum) {
        if ($bidsData["Open"]["Direction"] == $this->indexis["direct"]) {
           $input["Opentime"]      = (string) (new DateTime)->getTimestamp(); 
           $input["OpenID"]        = $bidsData["Open"]["BidID"];
           $input["CloseID"]       = $bidsData["Close"]["BidID"];
           $input["NextID"]        = $bidsData["Next"]["BidID"];
           $input["Startamount"]   = $bidsData["Open"]["Amountin"];
           $input["ServiceStart"]  = $bidsData["Open"]["Initamountin"];
           $input["preAccum"]      = $preAccum;
           $input["State"]         = 0;
           $this->protocol->AddMessage("tj", array("ont" => $input["OpenID"]));
           $this->tradeJournal->AddScript($input);
           $this->tradeJournal->AddToLegend($input["OpenID"]);
           $this->protocol->AddMessage("tj", "int");
        } else {
           $this->protocol->AddMessage("tj", "nnt");
        }

     }
     /**
      * Функция записи данных о нормальном закрытии сделки 
      *
      * @param array    $tradeData    Массив данных о открытой сделке
      * @param array    $bidsData     Массив данных о активной заявке
      *
      * @return double                Значение нарастающего итога 
      */
     protected function NormalClose($tradeData, $bidsData) {
        $this->protocol->AddMessage("tj", array("ncl" => $tradeData["OpenID"]));
        $input["Finishamount"]  = $bidsData["Open"]["Amountin"];
        $input["ServiceFinish"] = $bidsData["Open"]["Initamountin"];
        $input["Profit"]        = round(($input["Finishamount"] + $input["ServiceFinish"] - 
                                  $tradeData["Startamount"] - $tradeData["ServiceStart"]), 2);
        $input["Accum"]         = round(($tradeData["preAccum"] + $input["Profit"]), 2);
        $input["State"]         = 1;
        $input["Closetime"]     = (string) (new DateTime)->getTimestamp();
        $this->tradeJournal->CorrectScript($tradeData["OpenID"], $input);
        $this->tradeJournal->AddToLegend($tradeData["OpenID"]);
        $this->protocol->AddMessage("tj", "icl");
        if ($input["Accum"] > $this->indexis["ProfitLimit"]) {
           $this->FixProfit($bidsData["Open"]);
           $data = $this->GetBidsData();
           if ($data[0] != 0) {
              $this->protocol->AddMessage("tj", "nrj");
              return;
           }
           $bidsData = $data[1];
           unset($data);
           $preAccum = 0;
        } else {
           $preAccum = $input["Accum"];
        }
        $this->NewTrade($bidsData, $preAccum);
     }
     /**
      * Функция записи данных о проблемном закрытии сделки 
      *
      * @param array    $tradeData    Массив данных о открытой сделке
      * @param array    $bidsData     Массив данных о активной заявке
      *
      * @return double                Значение нарастающего итога 
      */
     protected function TroubleClose($tradeData, $bidsData) {
        $this->protocol->AddMessage("tj", "tcl");
        $input["State"]     = 1;
        $input["Closetime"] = (string) (new DateTime)->getTimestamp();
        $this->tradeJournal->CorrectScript($tradeData["OpenID"], $input);
        $this->tradeJournal->AddToLegend($tradeData["OpenID"]);
        $this->NewTrade($bidsData, $tradeData["preAccum"]);
     }
     /**
      * Функция вывода средств
      *
      * @param array    $bidsData     Массив данных о заявке, из которой выводятся средства
      *
      */
     protected function FixProfit($bidsData) {
        $this->protocol->AddMessage("tj", "fp");
        $sourceamount = round(($this->indexis["RateInterest"] * $this->indexis["ProfitLimit"]), 2);
        $this->CreateNewServiceBid($bidsdata, $sourceamount);
     }
  }
?>

