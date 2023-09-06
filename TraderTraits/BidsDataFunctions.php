
<?php
  /**
   * BidsDataFunctions: Трейт, определяющий служебные функции получения информации о заявках торговца
   */
  trait BidsDataFunctions {
     /**
      * Функция получения инфорации о заявках торговца
      *
      * @param string  $type            Тип запроса:
      *                                 "all"    - вернуть все заявки независимо от состояния
      *                                 "open"   - вернуть только неоплаченные заявки
      *                                 "trade"  - вернуть оплаченные заявки, но еще не погашенные (по которым еще идет обмен)
      *                                 "close"  - вернуть только уже завершенные (обменяные) заявки
      *                                 "union"  - вернуть только объединенные заявки
      *                                 "delete" - вернуть только удаленные заявки
      * @param string  $queryid         Номер (id) новой заявки, информацию по которой необходимо вернуть (необязательный)
      *
      * @return array                   Массив данных о заявках торговца
      */
     protected function GetMyBidsList($type, $queryid) {
        switch ($type) {
           case "all":
              $key = 3;
              break;
           case "open":
              $key = 0;
              break;
           case "trade":
              $key = 1;
              break;
           case "close":
              $key = 2;
              break;
           case "union":
              $key = 4;
              break;
           case "delete":
              $key = 5;
              break;
        }
        $data = WMmyBidList::SendQuery($this->wmid, $this->signer, $key, $queryid);
        $indexis = $this->indexis["general"];
        foreach ($data["queries"] as $row) {
           if (($row["exchtype"] == $indexis["Bid"]) || ($row["exchtype"] == $indexis["Ask"])) {
              $key = $row["id"];
              $output[$key]["Opentime"] = DateTime::createFromFormat("d.m.Y H:i:s", $row["querydatecr"])->getTimestamp();
              $output[$key]["BidID"] = $key;
              switch ($row["exchtype"]) {
                 case $indexis["Bid"]:
                    $output[$key]["Direction"] = "Bid";
                    break;
                 case $indexis["Ask"]:
                    $output[$key]["Direction"] = "Ask";
                    break;
              }
              $output[$key]["Initamountin"] = $this::ConvertData($row["initamountin"]);
              $output[$key]["Amountin"] = $this::ConvertData($row["amountin"]);
              $output[$key]["Amountout"] = $this::ConvertData($row["amountout"]);
              switch ($row["state"]) {
                 case 0:
                    $output[$key]["State"] = NULL;
                    break;
                 case 1:
                    $output[$key]["State"] = NULL;
                    break;
                 case 2:
                    $output[$key]["State"] = 40;
                    break;
                 case 3:
                    $output[$key]["State"] = 41;
                    break;
                 case 4:
                    $output[$key]["State"] = 43;
                    break;
                 case 5:
                    $output[$key]["State"] = 42;
                    break;
              }
              $output[$key]["Exchamountin"] = $this::ConvertData($row["exchamountin"]);
              $output[$key]["Exchamountout"] = $this::ConvertData($row["exchamountout"]);
              if ($row["state"]  >= 2) {
                 $output[$key]["Closetime"] = DateTime::createFromFormat("d.m.Y H:i:s", $row["querydate"])->getTimestamp();
                 if ($row["state"]  > 2) {
                    $output[$key]["Closetype"] = $row["state"];
                 }
              }
              $counter++;
           }
        }
        return $output;
     }
  }
?>

