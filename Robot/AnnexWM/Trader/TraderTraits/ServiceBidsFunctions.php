<?php
  /**
   * ServiceBidsFunctions: Трейт, определяющий операции с служебными заявками
   */
  trait ServiceBidsFunctions {
     /**
      * Функция создания новой служебной заявки
      *
      * @param  array    $bidsdata      Массив данных о заявке, на основе которой создается служебная заявка
      * @param  double   $sourceamount  Сумма, эквивалент которой будет убран из существующей заявки и перенесен в новую заявку
      * @param  string   $type          Тип новой заявки (по умолчанию - служебной)
      *                                 "bs" - опорная заявка
      *                                 "sr" - служебная заявка
      *
      * @return array                   Массив результатов операции
      */
     protected function CreateNewServiceBid($bidsdata, $sourceamount, $type = "sr") {
        $rate = $this->CalculateSleepRate($bidsdata["Direction"]);
        switch ($bidsdata["Direction"]) {
           case "Bid":
              $inamount = $sourceamount;
              $outamount = round(($sourceamount * $rate), 2);
              break;
           case "Ask":
              if ($rate > 1) {
                 $inamount = round(($sourceamount * $rate), 2);
                 $outamount = $sourceamount;
              } else {
                 $inamount = $sourceamount;
                 $outamount = round(($sourceamount * (1/$rate)), 2);
              }
              break;
        }

        $amountin = (float) $bidsdata["Amountin"];
        $operid = $bidsdata["BidID"];
        if ($amountin <= $inamount) {
           $output = FALSE;
           $message = array("ndiv" => array($type, $operid));
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
           return $output;
        }
        $exchtype = $this->indexis["general"][$bidsdata["Direction"]];
        $outpurse = $this->indexis["purses"][$bidsdata["Direction"]]["outpurse"];
        $result = WMdivideNewBid::SendQuery($this->wmid, $this->signer, $operid, $exchtype, $outpurse, $inamount, $outamount);
        if ($result["retval"] == 0) {
           $output = TRUE;
           $id = $result["divideid"];

           $data = $this::GetMyBidsList("trade", $id);
           $this->bidsJournal->NewScript($data[$id], $type, $operid);

           $data = $this::GetMyBidsList("trade", $operid);
           $message = array("div" => $operid);
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
           $this->bidsJournal->ChangeScript($data[$operid]);
        } else {
           $output = FALSE;
           $message = array("fdiv" => $operid,
                            "err" => array($result["retval"], $result["retdesc"]));
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
        }
        return $output;
     }
     /**
      * Функция удаления служебной заявки
      *
      * @param  array    $operid        Номер удаляемой служебной заявки
      *
      * @return array                   Массив результатов операции
      */
     protected function DeleteServiceBid($operid) {
        $result = WMdeleteNewBid::SendQuery($this->wmid, $this->signer, $operid);
        if ($result["retval"] == 0) {
           $output = TRUE;
           $data = $this::GetMyBidsList("delete", $operid);
           $message = array("del" => $operid);
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
           $this->bidsJournal->CloseScript($data[$operid]);
        } else {
           $output = FALSE;
           $message = array("ndel" => $operid,
                            "err" => array($result["retval"], $result["retdesc"]));
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($operid, array("wm" => $message));

        }
        return $output;   
     }
  }
?>