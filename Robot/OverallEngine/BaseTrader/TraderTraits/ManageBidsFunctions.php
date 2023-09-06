<?php
  /**
   * ManageBidsFunctions: Трейт, определяющий функции управления заявками торговца
   */
  trait ManageBidsFunctions {
     /**
      * Функция управления заявками торговца
      */
     protected function ManageBids() {
        $output = FALSE;
        $change = TRUE;
        $union  = FALSE;
        $close  = FALSE;
        while ($change) {
           $this->protocol->AddMessage("mb", "st1");
           $this->protocol->AddMessage("mb", "gd");
           while ($change) {
              $change = FALSE;
              $data = $this->bidsJournal->GetData();
              $directionData = $this->bidsJournal->SortByDirection($data);
              foreach ($directionData as $direction => $values) {
                 if (!empty($values)) {
                    $this->protocol->AddMessage("mb", array("dir" => $direction));
                    $journalData[$direction] = $this->bidsJournal->SortByState($values);
                    $result = $this::UnionOneDirectionBids($journalData[$direction]);
                    if ($result) {
                       $change = TRUE;
                    }
                 }
              } //end foreach
              if ($change) {
                 $this->protocol->AddMessage("mb", "dgd");
              }
           } //end while
           $change = FALSE;
           $this->protocol->AddMessage("mb", "st2");
           foreach ($journalData as $direction => $values) {
              $this->protocol->AddMessage("mb", array("dir" => $direction));
              if (!empty($journalData[$this::GetOppositeDirection($direction)]["active"])) {
                 $this->protocol->AddMessage("mb", "isac");
              } else {
                 $result = $this::CreateNewBaseBid($values["base"][0]);
                 if ($result) {
                    $change = TRUE;
                 }
              }
              if (count($values["service"]) > 0) {
                 $change = TRUE;
                 $this->protocol->AddMessage("mb", "isac");
                 foreach ($values["service"] as $servicevalues) {
                    $this->DeleteServiceBid($servicevalues["BidID"]);
                 }
              }
              if ($change) {
                 $this->protocol->AddMessage("mb", "st3");
              }
           }
        } //end while
     }
     /**
      * Функция объединения заявок одного направления
      *
      * @param  array    $journalData   Массив записей о заявках одного направления
      *
      * @return bool     $output[0]     TRUE, если заявки объединялись
      *                                 или FALSE, если заявки не объединялись
      */
     protected function UnionOneDirectionBids($journalData) {
        $this->protocol->AddMessage("bj", array("scr" => array(count($journalData["new"]),
                                                               count($journalData["base"]),
                                                               count($journalData["active"]),
                                                               count($journalData["service"]))));
        $output = FALSE;
        $result = $this::UnionOneStateBids($journalData["active"], NULL, "ac"); 
        if ($result[0]) {
           $output = $result[0];
        }
        $activeBid = $result[1];

        $result = $this::UnionOneStateBids($journalData["base"], $activeBid, "bs"); 
        if ($result[0]) {
           $output = $result[0];
        }
        $baseBid = $result[1];

        if ($activeBid) {
           $mainBid = $activeBid;
        } else if ($baseBid) {
           $mainBid = $baseBid;
        } else {
           $mainBid = NULL;
        }

        $result = $this::UnionOneStateBids($journalData["new"], $mainBid, "nw"); 
        if ($result[0]) {
           $output = $result[0];
        }
        if (($result[1]) && (!$mainBid)) {
           $output = TRUE;
           $baseBid = $result[1];
           $this->protocol->AddMessage("mb", "sn");
           $this->bidsJournal->CorrectScript($baseBid, array("State" => 10));
           $this->bidsJournal->AddToLegend($baseBid, array("sn" => $baseBid));
        }
        return $output;
     }
     /**
      * Функция объединения заявок одного направления и одного состояния
      *
      * @param  array    $journalData   Массив записей о заявках
      * @param  string   $mainBid       Номер заявки, к которой следует присоединять
      * @param  string   $key           Индикатор состояния заявок
      *
      * @return bool     $output[0]     TRUE, если заявки объединялись
      *                                 или FALSE, если заявки не объединялись
      * @return array    $output[1]     Номер объединенной заявки
      */
     protected function UnionOneStateBids($journalData, $mainBid, $key) {
        switch (count($journalData)) {
           case 0:
              $output[0] = FALSE;
              $output[1] = FALSE;
              break;
           case 1:
              $output[0] = FALSE;
              $output[1] = $journalData[0]["BidID"];
              break;
           default:
              $output[0] = TRUE;
              $this->protocol->AddMessage("mb", array("mr" => $key));
              $bids = $this::FindExtremeBid($journalData);
              $output[1] = $bids[0];
              if ($mainBid) {
                 $this->protocol->AddMessage("mb", array("ad" => $key));
                 $service = $mainBid;
              } else {
                 $this->protocol->AddMessage("mb", array("un" => $key));
                 $service = $output[1];
              }
              for ($i = 0; $i < count($bids[1]); $i++) {
                 $this::UnionBids($service, $bids[1][$i]);
              }
              break;
        }
        return $output;
     }
  }
?>

