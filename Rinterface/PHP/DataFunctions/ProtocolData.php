<?php
  /**
   * ProtocolData: Статический класс функций отправки данных интерфейсу
   */  
  class ProtocolData {
     use MakeHead, MakeItalic, MakeString, ParseStartData, ParseErrorData, ParseListenerData,
         ParseValidateData, ParseBidsJournalData, ParseComplectorData, ParseBidsData, ParseActiveBidsData,
         ParseWMbids, ParseTradeJournalData, ParseTraderData;
     /**
      * @param array   $colnanes   Массив колонок таблицы 
      */
     static private $colnanes = array("timestamp", "Protocol", "Status");
     /**
      * Функция отправки данных боковой панели интерфейса
      *
      * @param         $dbc         Идентификатор соединения
      * @param string  $tablename   Имя таблицы
      *
      * @return string              Строка JSON с данными
      */
     static public function boxJSON ($dbc, $tablename) {
        $query = "SELECT " . self::$colnanes[0] . ", "  . self::$colnanes[2] . " FROM " . $tablename . " ORDER BY timestamp";
        $data = $dbc->SendQuery($query)->fetch_all();
        $i = 0;
        $j = 0;
        $servicedate = new DateTime();
        while ($j < count($data)) {
           $servicedate = new DateTime($servicedate->setTimestamp($data[$j][0])->format("Y-m-d"));
           $condition = $servicedate->getTimestamp();
           $box[0][$i] = array(); 
           $box[0][$i][0] = $servicedate->setTimestamp($condition)->format("d.m.Y");
           $box[0][$i][1] = array(); 
           $box[1][$i] = array();
           for ($z = 0; $z < 24; $z++) {
              $box[0][$i][1][$z][0] = $servicedate->setTimestamp($condition + $z * 3600)->format("H:i") . "-" . 
                                      $servicedate->setTimestamp($condition + ($z + 1) * 3600 - 60)->format("H:i");
              $box[0][$i][1][$z][1] = $condition + $z * 3600;
           }
           $condition = $condition + 24 * 3600;
           while (($data[$j][0] < $condition) && $j < count($data)) {
              $box[1][$i][] = array($data[$j][0], $data[$j][1]);
              $j++;            
           }
           $i++;
        }
        $output = json_encode($box, JSON_UNESCAPED_UNICODE);
        return $output;
     }
     /**
      * Функция отправки данных таблице главной панели интерфейса
      *
      * @param         $dbc         Идентификатор соединения
      * @param string  $tablename   Имя таблицы
      * @param integer $timestamp   Метка времени
      *
      * @return string              Строка JSON с данными
      */
     static public function tableJSON ($dbc, $tablename, $timestamp) {
        $query = "SELECT " . self::$colnanes[0] . ", "  . self::$colnanes[2] . " FROM " . $tablename . 
                  " WHERE timestamp BETWEEN " . $timestamp . " AND ". ($timestamp + 3599)  . " ORDER BY timestamp";
        $data = $dbc->SendQuery($query)->fetch_all();
        $servicedate = new DateTime();
        $table[0][0] = $servicedate->setTimestamp($timestamp)->format("d.m.Y");
        $table[0][1] = $servicedate->setTimestamp($timestamp)->format("H:i") . " - " . 
                       $servicedate->setTimestamp($timestamp + 3540)->format("H:i");
        for ($i = 0; $i < count($data); $i++) {
           $table[1][$i][0] = $servicedate->setTimestamp($data[$i][0])->format("d.m.Y H:i");
           $table[1][$i][1] = $data[$i][1];
           $table[1][$i][2] = $data[$i][0];
        }
        $output = json_encode($table, JSON_UNESCAPED_UNICODE);
        return $output;
     }
     /**
      * Функция получения текста протокола
      *
      * @param         $dbc         Идентификатор соединения
      * @param string  $tablename   Имя таблицы
      * @param integer $timestamp   Метка времени
      *
      * @return string              Текст протокола
      */
     static public function dataJSON ($dbc, $tablename, $timestamp) {
        $query = "SELECT " . self::$colnanes[1] . " FROM " . $tablename . 
                 " WHERE timestamp = " . $timestamp;
        $data = $dbc->SendQuery($query)->fetch_all();
        return $data[0][0];
     }
     /**
      * Функция подготовки текста протокола
      *
      * @param  string   $data          Строка с массивом данных
      * @param  string   $id            Идентификатор типа протокола
      *
      * @return string                  Текст протокола
      */
     static public function ParseData($data, $id) {
     echo '<p>$data</p>';
     print_r($data);
        $data = json_decode($data, TRUE);
     echo '<p>$data</p>';
     print_r($data);
        $output ="";
        foreach ($data as $value) {
           switch ($value[0]) {
              case "start":
                 $output .= self::ParseStartData($id, $value[1]);
                 break;
              case "valid":
                 $output .= self::ParseValidateData($value[1]);
                 break;
              case "err":
                 $output .= self::ParseErrorData($id, $value[1]);
                 break;
              case "txt":
                 $output .= self::MakeString($value[1]);
                 break;
              default:
                 switch ($id) {
                    case "listener":
                       $output .= self::ParseListenerData($value[0]);
                       break;
                    case "expert":
                    case "interface":
                       $output .= self::ParseComplectorData($value);
                       break;
                    case "trader":
                       $output .= self::ParseTraderData($value);
                       break;
                 }
                 break;
           }
        }
        return $output;
     }  
  }
?>