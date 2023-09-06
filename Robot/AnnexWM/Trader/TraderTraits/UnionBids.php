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
           $this->protocol->AddMessage("wm", array("isu" => array($unionoperid, $operid)));
           $this->bidsJournal->AddToLegend($operid, array("una" => $unionoperid));
           $this->bidsJournal->ChangeScript($data[$operid]);

           $data = $this::GetMyBidsList("union", $unionoperid);
           $this->bidsJournal->CloseScript($data[$unionoperid]);
           $this->bidsJournal->AddToLegend($unionoperid, array("unp" => $operid));
        } else {
           $message = array("nu" => array($unionoperid, $operid),
                            "err" => array($result["retval"], $result["retdesc"]));
           $this->protocol->AddMessage("wm", $message);
           $this->bidsJournal->AddToLegend($operid, array("wm" => $message));
           $this->bidsJournal->AddToLegend($unionoperid, array("wm" => $message));
        }
     }
  }
?>

