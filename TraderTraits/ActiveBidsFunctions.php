<?php
  /**
   * ActiveBidsFunctions: Трейт, определяющий операции с активными заявками
   */
  trait ActiveBidsFunctions {
      /**
      * Функция перевода заявки в режим закрытия 
      *
      * @param array    $bidsdata     Массив данных о активной заявке
      * @param double   $rate         Новое значение курса
      *
      * @return string  $output       Код решения по торгам:
      *                               0 - Заявка остается без изменений
      *                               1 - Курс заявки изменен
      */
     protected function ChangeBidsRate($bidsdata, $rate) {
        $smartRate = $this::SmartCalculateRate($bidsdata["Direction"], $bidsdata["Amountin"], $rate);
        $this->protocol->AddMessage('$smartRate = ' . $smartRate);
        switch ($bidsdata["Direction"]) {
           case "Bid":
              $ratio = (($smartRate / $bidsdata["Rate"]) - 1) * 100;
              break;
           case "Ask":
              $ratio = (($bidsdata["Rate"] / $smartRate) - 1) * 100;      
              break;
        }
        $this->protocol->AddMessage('$bidsdata["Direction"] = ' . $bidsdata["Direction"]);
        $this->protocol->AddMessage('$ratio = ' . $ratio);





        if ($ratio > 1) {
           $service = $this::CalculateAmountout($bidsdata, $smartRate) + 0.01;
           $serviceRate = $this::CalculateRate($bidsdata["Direction"], $bidsdata["Amountin"], $service);

           $this->protocol->AddMessage('$service = ' . $service);
           $this->protocol->AddMessage('$serviceRate = ' . $serviceRate);
           $output = $this::ExecuteChange($bidsdata, $serviceRate);


        }
        $currents = WMbidList::SendQuery($this->indexis["general"][$bidsdata["Direction"]]);
        if ($currents[0]["id"] == $bidsdata["BidID"]) {
           $this->protocol->AddMessage("Заявка № " . $bidsdata["BidID"] . " занимает первое место в списке заявок.");
           $this->protocol->AddMessage("Изменение курса не требуется.");
        } else {
           $this->protocol->AddMessage("Заявка № " . $bidsdata["BidID"] . "не занимает первое место в списке заявок.");
           $output =$this::ExecuteChange($bidsdata, $smartRate);
        }
        return $output;
     }

      /**
      * Функция перевода заявки в режим закрытия 
      *
      * @param array    $bidsdata     Массив данных о активной заявке
      * @param double   $rate         Новое значение курса
      *
      * @return string  $output       Код решения по торгам:
      *                               0 - Заявка остается без изменений
      *                               1 - Курс заявки изменен
      */
     protected function ExecuteChange($bidsdata, $rate) {

        if ($bidsdata["Amountout"] != $this::CalculateAmountout($bidsdata, $rate)) {
           $toLegend = array();
           $this->protocol->AddMessage("Установливаемый курс заявки = " . $rate);
           array_push($toLegend, "Установливаемый курс заявки = " . $rate);
           $toJournal["Direction"] = $bidsdata["Direction"];
           switch ($bidsdata["Direction"]) {
              case "Bid":
                 $curstype = "0";
                 break;
              case "Ask":
                 $curstype = "1";
                 break;
           }
           $result = WMchangeBidsRate::SendQuery($this->wmid, $this->signer, $bidsdata["BidID"], $curstype, $rate);

           if ($result["retval"] == 0) {
              $toJournal["Amountin"] = $this::ConvertData($result["AmountRestIn"]);

              $toJournal["Amountout"] =  $this::ConvertData($result["AmountRestOut"]);

              $toJournal["Rate"] = $this::CalculateRate($toJournal["Direction"], $toJournal["Amountin"], $toJournal["Amountout"]);
 
              array_push($toLegend, "Установлен курс заявки = " . $toJournal["Rate"]);
              $this->protocol->AddMessage("Установлен курс заявки = " . $toJournal["Rate"]);
              array_push($toLegend, "Сумма, выставленная на обмен: " . $toJournal["Amountin"],
                                    "Сумма, которую ожидается получить: " . $toJournal["Amountout"]);
           } else {
               $message = "Не удалось изменить значение курса  заявки № " . $bidsdata["BidID"] . ". Код ошибки: " . $result["retval"] . ". Описание ошибки: " . $result["retdesc"];
               $this->protocol->AddMessage($message);
               array_push($toLegend, $message);
           }
           $this::CorrectScript($bidsdata["BidID"], $toJournal);
           $this::AddToLegend($bidsdata["BidID"], $toLegend);
           $output = 1;
        } else {
           $this->protocol->AddMessage("Заявка остается без изменений.");
           $output = 0;
        }

        return $output;
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
        switch ($direction) {
           case "Bid":
              $exchtype = $this->indexis["general"]["Ask"];
              break;
           case "Ask":
              $exchtype = $this->indexis["general"]["Bid"];
              break;
        }
        $criteria = 0;
        $used = array();
        while ($criteria < 2) {
           $aliens = WMbidList::SendQuery($exchtype);
           $i = 0;
           $bool = TRUE;
           while (($bool) && ($criteria < 2)) {
              $this->protocol->AddMessage('$i = ' . $i . ', $aliens[$i]["id"] = ' . $aliens[$i]["id"]);
              foreach ($used as $values) {
                 if ($aliens[$i]["id"] == $values) {
                    $bool = FALSE;
                 }
                 $this->protocol->AddMessage('$values = ' . $values);
              }
              if ($bool) {              
                 $toLegend = array("Скупка встречной заявки № " . $aliens[$i]["id"]);
                 array_push($toLegend, "дата последнего изменения во встречной заявке: " . $aliens[$i]["querydate"]);
                 array_push($toLegend, "сумма, которую осталось обменять во встречной заявке: " . $this::ConvertData($aliens[$i]["amountin"]));
                 array_push($toLegend, "cумма, которую хочет получить после обмена респондент выставивший заявку: " . $this::ConvertData($aliens[$i]["amountout"]));
                 $result = WMbuyAlienBid::SendQuery($this->wmid, $this->signer, $operid, $aliens[$i]["id"]);
                 $this->protocol->AddMessage('$values = ' . $values);
                 if ($result["retval"] == 0) {
                    array_push($toLegend, "Операция скупки встречной заявки № " . $aliens[$i]["id"] . " выполнена успешно.");
                    $used[] = $aliens[$i]["id"];
                 } else {
                    $bool = FALSE;
                    array_push($toLegend, "Операция скупки встречной заявки № " . $aliens[$i]["id"] . " выполнена неудачно.",
                                          "Код ошибки:" . $result["retdesc"] );
                 }
                 $result = WMnewBidAscList::SendQuery($this->wmid, $this->signer, $operid);
                 $this->protocol->AddMessage('$result WMnewBidAscLis = ' . $result);


                 $data = WMmyBidList::SendQuery($this->wmid, $this->signer, 3, $operid);
                 $criteria = $data["queries"][0]["state"];
                 array_push($toLegend, '$criteria = ' . $criteria);
                 array_push($toLegend, "Остаток в заявке № " . $data["queries"][0]["id"] . " = " . $data["queries"][0]["amountin"]);
                 $this::AddToLegend($operid, $toLegend);
                 foreach ($toLegend as $values) {
                    $this->protocol->AddMessage($values);
                 }
              }
              $i++;
           }
        }
        $data = $this::GetMyBidsList("all", $operid);
        $rate = $this::CalculateRate($data[$operid]["Direction"], $data[$operid]["Amountin"], $data[$operid]["Amountout"]);
        $toJournal = array("State"         => $data[$operid]["State"],
                           "Amountin"      => $data[$operid]["Amountin"],
                           "Amountout"     => $data[$operid]["Amountout"],
                           "Exchamountin"  => $data[$operid]["Exchamountin"],
                           "Exchamountout" => $data[$operid]["Exchamountout"],
                           "Rate"          => $rate,
                           "Closetime"     => $data[$operid]["Closetime"],
                           "Closetype"     => 2);
        $this::CorrectScript($data[$operid]["BidID"], $toJournal);
     }
  }
?>

