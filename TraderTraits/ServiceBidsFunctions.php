<?php
  /**
   * ServiceBidsFunctions: Трейт, определяющий операции с служебными заявками
   */
  trait ServiceBidsFunctions {
     /**
      * Функция создания новой служебной заявки
      *
      * @param array   $bidsdata       Массив данных о заявке, на основе которой создается служебная заявка
      * @param double  $sourceamount   Сумма, эквивалент которой будет убран из существующей заявки и перенесен в новую заявку
      * @param double  $ratevalue      Значение курса
      * @param integer $state          Код состояния новой служебной заявки
      * @param string  $service        Служебная строка
      *
      * @return array                  Массив результатов операции
      */
     protected function CreateNewServiceBid($bidsdata, $sourceamount, $ratevalue, $state = 30, $service = "служеб") {
        switch ($this->indexis["valueArray"][$bidsdata["Direction"]][0]) {
           case "inoutrate":
              $outamount = $sourceamount;
              $inamount = round((0.8 * $outamount * $ratevalue), 2);
              break;
           case "outinrate":
              $inamount = $sourceamount;
              $outamount = round((1.25 * $inamount * $ratevalue), 2);
              break;
        }
        $operid = $bidsdata["BidID"];
        $exchtype = $this->indexis["general"][$bidsdata["Direction"]];
        $outpurse = constant ($this->indexis["purses"][$bidsdata["Direction"]]["outpurse"]);
        $amountin = (float) $bidsdata["Amountin"];
        if ($amountin <= $inamount) {
           $output = FALSE;
           $message = "В заявке № " . $operid . " недостаточно средств, чтобы создать новую " . $service . "ную заявку.";
           $this->protocol->AddMessage($message);
           $this::AddToLegend($data[$operid]["BidID"], array($message));
           return $output;
        }
        $result = WMdivideNewBid::SendQuery($this->wmid, $this->signer, $operid, $exchtype, $outpurse, $inamount, $outamount);
        if ($result["retval"] == 0) {
           $output = TRUE;
           $id = $result["divideid"];
           $data = $this::GetMyBidsList("trade", $id);
           $value = $data[$id];
           $value["ParentID"] = $operid;
           $value["State"] = $state;
           $value["Revnumber"] = 0;
           $value["Legend"] = "";
           $value["Rate"] = $this::CalculateRate($value["Direction"], $value["Amountin"], $value["Amountout"]);
           $result = $this->bidsJournal->AddScript($value);
           $message = "Создана новая " . $service . "ная заявка № " . $id . ".";
           $this->protocol->AddMessage($message);
           $this::AddToLegend($id, array($message,
                                         "Сумма, выставленная на обмен: " . $value["Amountin"],
                                         "Сумма, которую ожидается получить: " . $value["Amountout"],
                                         "Сумма, фактически выставленная на обмен: " . $value["Exchamountin"],
                                         "Сумма, фактически полученная: " . $value["Exchamountin"]));
           $data = $this::GetMyBidsList("trade", $operid);
           $this::CorrectScript($data[$operid]["BidID"], array ("Amountin"      => $data[$operid]["Amountin"],
                                                                "Amountout"     => $data[$operid]["Amountout"],
                                                                "Exchamountin"  => $data[$operid]["Exchamountin"],
                                                                "Exchamountout" => $data[$operid]["Exchamountout"]));
           $message = "Заявка № " . $operid . " успешно разделена.";
           $this->protocol->AddMessage($message);
           $this::AddToLegend($data[$operid]["BidID"], array($message,
                                                             "Сумма, выставленная на обмен: " . $data[$operid]["Amountin"],
                                                             "Сумма, которую ожидается получить: " . $data[$operid]["Amountout"],
                                                             "Сумма, фактически выставленная на обмен: " . $data[$operid]["Exchamountin"],
                                                             "Сумма, фактически полученная: " . $data[$operid]["Exchamountin"]));
        } else {
           $output = FALSE;
           $message = "Не удалось разделить заявку № " . $operid . ". Код ошибки: " . $result["retval"] . ". Описание ошибки: " . $result["retdesc"];
           $this->protocol->AddMessage($message);
           $this::AddToLegend($data[$operid]["BidID"], array($message));
        }
        return $output;
     }
     /**
      * Функция удаления служебной заявки
      *
      * @param array   $operid         Номер удаляемой служебной заявки
      *
      * @return array                  Массив результатов операции
      */
     protected function DeleteServiceBid($operid) {
        $result = WMdeleteNewBid::SendQuery($this->wmid, $this->signer, $operid);
        if ($result["retval"] == 0) {
           $output = TRUE;
           $data = $this::GetMyBidsList("delete", $operid);
           $this::CorrectScript($data[$operid]["BidID"], array ("Amountin"      => $data[$operid]["Amountin"],
                                                                "Amountout"     => $data[$operid]["Amountout"],
                                                                "Exchamountin"  => $data[$operid]["Exchamountin"],
                                                                "Exchamountout" => $data[$operid]["Exchamountout"],
                                                                "State"         => $data[$operid]["State"],
                                                                "Closetime"     => $data[$operid]["Closetime"],
                                                                "Closetype"     => $data[$operid]["Closetype"]));
           $message = "Заявка № " . $operid . " успешно удалена.";
        } else {
           $output = FALSE;
           $message = "Не удалось удалить заявку № " . $operid. ". Код ошибки:" . $result["retdesc"];

        }
        $this->protocol->AddMessage($message);
        $this::AddToLegend($operid, array($message));  
        return $output;   
     }
  }
?>