<?php
  /**
   * BaseBidsFunctions: Трейт, определяющий операции с базовыми заявками
   */
  trait BaseBidsFunctions {
     /**
      * Функция создания новой опорной заявки
      *
      * @param array   $bidsdata       Массив данных о опорной заявке
      *
      * @return array                  Массив результатов операции
      */
     protected function CreateNewBaseBid($bidsdata) {
        $sourceamount = $this->bidsJournal->GetBaseLimit();
        $rate = $this->CalculateSleepRate($bidsdata["Direction"]);
        if (!$this::CheckEmptyBase($bidsdata, $rate, $sourceamount)) {
           $message = array("emb" => 1);
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("wm" => $message));
           $output = $this->CreateNewServiceBid($bidsdata, $sourceamount, "bs");
           if ($output) {
              $message = array("mac" => $bidsdata["BidID"]);
              $this->protocol->AddMessage("wm", $message);
              $this->bidsJournal->CorrectScript($bidsdata["BidID"], array ("Activetime" => (string) (new DateTime)->getTimestamp(),
                                                                           "State"      => 20));
              $this->bidsJournal->AddToLegend($bidsdata["BidID"], array("wm" => $message));
           }
        } else {
           $output = FALSE;
           $this->protocol->AddMessage("wm", array("emb" => 0));
        }
        return $output;
     }
  }
?>

