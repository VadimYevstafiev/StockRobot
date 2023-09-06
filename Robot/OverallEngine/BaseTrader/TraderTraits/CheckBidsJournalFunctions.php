<?php
  /**
   * CheckBidsJournalFunctions: Трейт, определяющий служебные функции проверки журнала заявок
   */
  trait CheckBidsJournalFunctions {
     /**
      * Общая функция проверки журнала заявок 
      *
      */
     protected function CheckBidsJournal() {
        $change = TRUE;
        while ($change) {
           $this->protocol->AddMessage("cbj");
           $bidsList = $this::GetMyBidsList("trade");
           $this->protocol->AddMessage("opbid", count($bidsList));
           $result = $this->bidsJournal->CheckOpenBids($bidsList);
           switch ($result[0]) {
              case 0:
                 $change = FALSE;
                 break;
              case 1:
                 $change = TRUE;
                 break;
              case 2:
                 $change = TRUE;
                 foreach ($result[1] as $value) {
                    $bid = $this::GetMyBidsList("all", $value);
                    $this->bidsJournal->CheckClosedBid($bid[$value]);
                 }
                 break;
           }
           if ($change) {
              $this->protocol->AddMessage("dcbj");
           }
        }
     }
  }
?>

