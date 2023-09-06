<?php
  /**
   * ChartsData: Статический класс функций отправки данных интерфейсу
   */  
  class ChartsData {
     /**
      * Функция отправки данных боковой панели интерфейса
      *
      * @param         $dbc        Идентификатор соединения
      * @param string  $id         Идентификатор рынка 
      * @param string  $para       Идентификатор торговой пары
      * @param string  $type       Тип графика (Сводный, Bid или Ask)
      * @param string  $timeframe  Идентификатор таймфрейма 
      *
      * @return string             Строка JSON с данными
      */
     static public function boxJSON ($dbc, $id, $para, $type, $timeframe) {
        switch ($type) {
           case "0":
              $rowtype = "Summary";
              break;
           case "1":
           case "2":
              $rowtype = "Simple";
              break;
        }
        $tablename = $id . "boxOpt" . $para;
        $query = 'SELECT ' . $timeframe . ' FROM ' . $tablename . ' WHERE type = "' . $rowtype . '"';
        $output = $dbc->SendQuery($query)->fetch_assoc();
        return $output[$timeframe];
     }
     /**
      * Функция отправки данных боковой панели интерфейса
      *
      * @param         $dbc           Идентификатор соединения
      * @param string  $id            Идентификатор рынка 
      * @param string  $para          Идентификатор торговой пары
      * @param string  $type          Тип графика (Сводный, Bid или Ask)
      * @param string  $timeframename Идентификатор таймфрейма 
      * @param integer $position      Горизонт представления данных на графике
      *
      * @return string       Строка JSON с данными
      */
     static public function dataJSON ($dbc, $id, $para, $type, $timeframename, $position) {
        switch ($type) {
           case "0":
              $dataname = $para;
              $optname = "Summary" . $para;
              break;
           case "1":
              $dataname = $para . "Bid";
              $optname = "Simple" . $para;
              break;
           case "2":
              $dataname = $para . "Ask";
              $optname = "Simple" . $para;
              break;
        }
        $timeframe = self::ConvertTimeframe($timeframename);
        $timemodule = self::ConvertTimemodule($timeframename);
        $value = $timeframe * $timemodule;
        $timepart = self::DetectTimeframe($timeframe, $timemodule);
        $tablename = $id . $timepart . "chOpt" . $optname;
        $options = self::ReadOptions($dbc, $tablename);
        $timestamp = intval((new DateTime)->getTimestamp()  / $value) * $value - $position * $value;

        for ($i = 0; $i < count($options); $i++) {
           $tablename = $id . $timepart . "chData_" . $i . "_" . $dataname;
           $data[$i] = self::ReadData($dbc, $tablename, $timestamp);
        }
        $output = json_encode(array($options, $data), JSON_UNESCAPED_UNICODE);
        return $output;
     }
     /**
      * Функция чтения опций графика
      *
      * @param         $dbc         Идентификатор соединения
      * @param string  $tablename   Имя таблицы опций графика
      *
      * @return array               Массив с данными
      */
     static private function ReadOptions($dbc, $tablename) {
        $query = 'SELECT * FROM ' . $tablename;
        $options = $dbc->SendQuery($query)->fetch_all();
        for ($i = 0; $i < count($options); $i++) {
           for ($j = 1; $j < count($options[$i]); $j++) {
              $output[$i][$j - 1] = json_decode($options[$i][$j], true);
           }
        }
        return $output;
     }
     /**
      * Функция чтения данных, отображаемых на графике
      *
      * @param         $dbc         Идентификатор соединения
      * @param string  $tablename   Имя таблицы опций графика
      * @param integer $timestamp   Горизонт данных
      *
      * @return array               Массив с данными
      */   
     static private function ReadData($dbc, $tablename, $timestamp) {
        $query = 'SELECT * FROM ' . $tablename . ' WHERE timestamp >= ' . $timestamp . ' ORDER BY timestamp';
        $servicedate = new DateTime();
        $data = $dbc->SendQuery($query)->fetch_all();
        for ($i = 0; $i < count($data); $i++) {
           $servicedate->setTimestamp($data[$i][0]);
           $output[$i][0] = $servicedate->format("d.m H:i");
           for ($j = 1; $j < count($data[$i]); $j++) {
              if ($data[$i][$j] == "null") {
                 $output[$i][$j] = "null";
              } else {
                 $output[$i][$j] = (float) $data[$i][$j];
              }
           }
        }
        return $output;
     }
     /**
      * Функция представления таймфрейма в имени таблицы
      *
      * @param integer $timeframe      Таймфрейм
      * @param integer $timemodule     Величина единицы таймфрейма в секундах
      *
      * @return string                 Представление таймфрейма в имени таблицы
      */
     protected function DetectTimeframe ($timeframe, $timemodule) {
        switch ($timemodule) {
           case 1:
              $output    =$timeframe . "second";
              break;
           case 60:
              $output    = $timeframe . "minute";
              break;
           case 3600:
              $output    = $timeframe . "hour";
              break;
           case 86400:
              if ($timeframe != 1) {
                 $output = $timeframe;
              }
                 $output = "day";
              break;
        }
        return $output;
     }
     /**
      * Функция получения численного значения таймфрейма
      *
      * @param integer $timeframe      Таймфрейм
      *
      * @return integer                Значение таймфрейма
      */
     static private function ConvertTimeframe ($timeframe) {
        $output = intval(substr($timeframe, 0, -1));
        return $output;
     }
     /**
      * Функция получения значения модуля времени (Величины единицы таймфрейма в секундах)
      *
      * @param integer $timeframe      Таймфрейм
      *
      * @return integer                Величина таймфрейма в секундах
      */
     static private function ConvertTimemodule ($timeframe) {
        switch (substr($timeframe, -1)) {
           case "s":
              $output = 1;
              break;
           case "i":
              $output = 60;
              break;
           case "H":
              $output = 3600;
              break;
           case "d":
              $output = 86400;
              break;
        }
        return $output;
     }
  }
?>