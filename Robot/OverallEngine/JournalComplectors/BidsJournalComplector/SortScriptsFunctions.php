<?php
  /**
   * SortScriptsFunctions: Трейт, определяющий служебные функции сортировки извлеченных из журнала записей 
   */
  trait SortScriptsFunctions {
     /**
      * Функция сортировки извлеченных из журнала записей по состоянию 
      *
      * @param array  $data              Массив извлеченных из журнала записей 
      *
      * @return array $output["new"]     Массив записей о ранее неучтенных заявках
      * @return array $output["base"]    Массив записей об опорных заявках
      * @return array $output["active"]  Массив записей об активных заявках
      * @return array $output["service"] Массив записей о служебных заявках
      * @return array $output["close"]   Массив записей о закрытых заявках
      */
     public function SortByState($data) {
        $output["new"]     = array();
        $output["base"]  = array();
        $output["active"]  = array();
        $output["service"] = array();
        $output["close"]   = array();
        for ($i = 0; $i < count($data); $i++) {
           if ($data[$i]["State"] < 10) {
              $output["new"][] = $data[$i];
           } else if ($data[$i]["State"] < 20) {
              $output["base"][] = $data[$i];
           } else if ($data[$i]["State"] < 30) {
              $output["active"][] = $data[$i];
           } else if ($data[$i]["State"] < 40) {
              $output["service"][] = $data[$i];
           } else {
              $output["close"][] = $data[$i];
           }
        }
        return $output;
     }
      /**
      * Функция сортировки извлеченных из журнала записей по направлению заявки 
      *
      * @param array  $data           Массив извлеченных из журнала записей
      *
      * @return array $output["Bid"]  Массив записей о заявках на покупку
      * @return array $output["Ask"]  Массив записей о заявках на продажу
      */
     public function SortByDirection($data) {
        $output["Bid"] = array();
        $output["Ask"]  = array();
        for ($i = 0; $i < count($data); $i++) {
           switch ($data[$i]["Direction"]) {
              case "Bid":
                 $output["Bid"][] = $data[$i];
                 break;
              case "Ask":
                 $output["Ask"][] = $data[$i];
                 break;
           }
        }
        return $output;
     }
  }
?>

