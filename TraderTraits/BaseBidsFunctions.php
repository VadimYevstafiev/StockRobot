<?php
  /**
   * BaseBidsFunctions: Трейт, определяющий операции с базовыми заявками
   */
  trait BaseBidsFunctions {
     /**
      * Функция создания новой базовой заявки
      *
      * @param array   $bidsdata       Массив данных о активной заявке
      * @param array   $listenerdata   Массив данных слушателя
      *
      * @return array                  Массив результатов операции
      */
     protected function CreateNewBaseBid($bidsdata, $listenerdata) {
        $ratevalue = $listenerdata[$bidsdata["Direction"]];
        $sourceamount = $this->bidsJournal->GetBaseLimit();
        $output = $this->CreateNewServiceBid($bidsdata, $sourceamount, $ratevalue, 10, "опор");
        return $output;
     }
  }
?>

