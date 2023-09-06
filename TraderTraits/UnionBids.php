<?php
  /**
   * UnionBids: Трейт, определяющий функцию объединения заявок
   */
  trait UnionBids {
     /**
      * Функция объединения заявок
      *
      * @param array   $operid         Номер заявки, к которой присоединяют
      * @param array   $unionoperid    Номер заявки, которая присоединяется
      *
      * @return array                  Массив результатов операции
      */
     protected function UnionBids($operid, $unionoperid) {
        $result = WMunionBids::SendQuery($this->wmid, $this->signer, $operid, $unionoperid);
        if ($result["retval"] == 0) {
           $data = $this::GetMyBidsList("trade", $operid);
           $this::CorrectScript($data[$operid]["BidID"], array ("Amountin"      => $data[$operid]["Amountin"],
                                                                "Amountout"     => $data[$operid]["Amountout"],
                                                                "Exchamountin"  => $data[$operid]["Exchamountin"],
                                                                "Exchamountout" => $data[$operid]["Exchamountout"],
                                                                "Rate"          => $this::CalculateRate(
                                                                                                        $data[$operid]["Direction"], 
                                                                                                        $data[$operid]["Amountin"], 
                                                                                                        $data[$operid]["Amountout"])));
           $this->protocol->AddMessage("Заявка № " . $unionoperid . " успешно присоединена к заявке № " . $operid . ".");
           $this::AddToLegend($data[$operid]["BidID"], array("К заявке присоединена заявка № " . $unionoperid . ".",
                                                              "Сумма, выставленная на обмен: " . $data[$operid]["Amountin"],
                                                              "Сумма, которую ожидается получить: " . $data[$operid]["Amountout"],
                                                              "Сумма, фактически выставленная на обмен: " . $data[$operid]["Exchamountin"],
                                                              "Сумма, фактически полученная: " . $data[$operid]["Exchamountin"],
                                                              "Заданное значение курса: " . $data[$operid]["Rate"]));
           $data = $this::GetMyBidsList("union", $unionoperid);
           $this::CorrectScript($data[$unionoperid]["BidID"], array ("Amountin"      => $data[$unionoperid]["Amountin"],
                                                                     "Amountout"     => $data[$unionoperid]["Amountout"],
                                                                     "Exchamountin"  => $data[$unionoperid]["Exchamountin"],
                                                                     "Exchamountout" => $data[$unionoperid]["Exchamountout"],
                                                                     "State"         => $data[$unionoperid]["State"],
                                                                     "Closetime"     => $data[$unionoperid]["Closetime"],
                                                                     "Closetype"     => $data[$unionoperid]["Closetype"]));
           $this::AddToLegend($data[$unionoperid]["BidID"], array("Заявка присоединена к заявке № " . $operid . "."));
        } else {
           $message = "Не удалось присоединить заявку № " . $unionoperid . " к заявке № " . $operid . ". Код ошибки: " . $result["retval"] . ". Описание ошибки: " . $result["retdesc"];
           $this->protocol->AddMessage($message);
           $this::AddToLegend($data[$operid]["BidID"], array($message));
           $this::AddToLegend($data[$unionoperid]["BidID"], array($message));
        }
     }
  }
?>

