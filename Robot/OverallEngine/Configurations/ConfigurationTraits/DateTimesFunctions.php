<?php
  /**
   * DateTimesFunctions: Трейт, определяющий функции установки дат и времени
   */
  trait DateTimesFunctions {
     /**
      * Функция установки массива дат и времени
      *
      * @param array   $tabledata      Массив конфигурации таблицы
      * @param integer $gorizont       Количество периодов, данные которых обрабатываются сценарием
      *
      * @return array  $datetimes      Массив дат и времени
      */
     private function SetDateTimes ($tabledata, $gorizont) {
        date_default_timezone_set("Europe/Moscow");
        $servicearray = self::SetServiceperiod ($tabledata);
        $output = array();
        $output["currenttime"] = new DateTime();
        $output["currentdate"] = new DateTime($output["currenttime"]->format("Y-m-d"));
        $output["getOffset"] = $output["currenttime"]->getOffset();
        foreach ($servicearray as $key => $item) {
           if ($output["getOffset"] < $item["module"]) {
              $deviator = $output["getOffset"];
           } else {
              $deviator = 0;
           } 
           $finishdate = intval(($output["currenttime"]->getTimestamp() + $deviator)  / $item["module"]) * $item["module"] - $deviator;
           $output["finishdate"][$key] = (new DateTime)->setTimestamp($finishdate);
           $startdate = $finishdate - $item["module"] * $gorizont;
           $output["startdate"][$key] = (new DateTime)->setTimestamp($startdate);
           $deviation = $item["module"] * $item["value"];
           $servicestartdate = $startdate - $deviation;
           $output["servicestartdate"][$key] = (new DateTime)->setTimestamp($servicestartdate);
           $output["deviation"][$key] = $deviation;
           $output["module"][$key] = $item["module"];
        }
        return $output;
     }
     /**
      * Служебная функция корректировки массива дат и времени
      *
      * @param integer $start          Начальная метка времени данных таблицы слушателя
      * @param array   $datetimes      Массив дат и времени
      *
      * @return array                  Массив дат и времени
      */
     private function CorrectStartDate ($start, $datetimes) {
        $output = $datetimes;
        foreach ($output["module"] as $key => $item) {
           if ($output["servicestartdate"][$key]->getTimestamp() < $start) {
              if ($output["getOffset"] < $item) {
                 $deviator = $output["getOffset"];
              } else {
                 $deviator = 0;
              }
              $servicestartdate = intval(($start + $deviator) / $item) * $item - $deviator;
              $startdate = $servicestartdate + $output["deviation"][$key];
              $output["startdate"][$key] = (new DateTime)->setTimestamp($startdate);
              $output["servicestartdate"][$key] = (new DateTime)->setTimestamp($servicestartdate);
           }
        }
        return $output;
     }
     /**
      * Функция определения служебного периода
      *
      * @param array   $tabledata      Массив конфигурации таблицы
      *
      * @return array                  Общее количество периодов, данные которых обрабатываются сценарием
      */
     private function SetServiceperiod ($tabledata) {
        foreach ($tabledata["Simple"] as $value) {
           foreach ($value as $key => $valueItem) {
              if (!isset($output[$key])) {
                 $output[$key]["value"] = 0;
                 $output[$key]["module"] = $valueItem["timemodule"] * $valueItem["Timeframe"];
              }
              if ((isset($valueItem["serviceperiod"])) && ($valueItem["serviceperiod"] > $output[$key]["value"])) {
                 $output[$key]["value"] = $valueItem["serviceperiod"];
              }
           }
        }
        return $output;
     }
  }
?>