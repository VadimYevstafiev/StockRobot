<?php
  /**
   * TradeJournalsData: Статический класс функций отправки интерфейсу данных журнала торгов
   */  
  class TradeJournalsData {
     /**
      * @param array   $boxItems   Массив элементов боковой панели
      * @param array   $columns    Массив колонок таблицы 
      */
     static private $columns   = array("OpenID"        => array(80, "Номер заявки, которой открыта сделка"), 
                                       "CloseID"       => array(80, "Номер заявки, которой закрыта сделка"), 
                                       "Startamount"   => array(100, "Стартовая сумма в сделке"),
                                       "ServiceStart"  => array(100, "Первона-чальная сумма заявки, которой открыта сделка"),
                                       "Finishamount"  => array(100, "Финальная сумма в сделке"), 
                                       "ServiceFinish" => array(100, "Первона-чальная сумма заявки, которой закрыта сделка"), 
                                       "Profit"        => array(100, "Прибыль/убыток"),
                                       "preAccum"      => array(100, "Значение нарастающего итога по предыдущей сделке"),
                                       "Accum"         => array(100, "Значение нарастающего итога"),
                                       "Opentime"      => array(80, "Время открытия сделки"), 
                                       "Closetime"     => array(80, "Время закрытия сделки"));
     /**
      * Функция отправки данных таблице главной панели интерфейса
      *
      * @param         $dbc         Идентификатор соединения
      * @param string  $tablename   Имя таблицы
      * @param string  $type        Тип данных
      *
      * @return string              Строка JSON с данными
      */
     static public function tableJSON ($dbc, $tablename, $type) {
        $query = "SELECT * FROM " . $tablename . " ORDER BY OpenID  DESC";
        $data = self::SelectData(self::GetData ($dbc, $query));
        $output[0]["items"] = array("open"  => "Открытые", 
                                    "close" => "Закрытые");
        foreach (self::$columns as $key => $value) {
           $output[0]["head"][$key] = $value[1];
           $output[0]["size"][$key] = $value[0];
        }
        foreach ($data as $key => $value) {
           $output[1][$key] = $data[$key];
        }
        $output = json_encode($output, JSON_UNESCAPED_UNICODE);
        return $output;
     }

     /**
      * Функция получения записей в журнале
      *
      * @param         $dbc         Идентификатор соединения
      * @param string  $query       Текст запроса
      *
      * @return array               Массив записей в журнале
      */
     static private function GetData ($dbc, $query) {
        $result = $dbc->SendQuery($query);
        for ($i = 0; $i < $result->num_rows; $i++) {
           $result->field_seek($i);
           $output[$i] = $result->fetch_array(MYSQLI_ASSOC);
        }
        return $output;
     }
     /**
      * Функция сортировки записей в журнале
      *
      * @param array   $input              Массив записей в журнале
      *
      * @return array  $output["open"]     Массив записей об открытых сделках
      * @return array  $output["close"]    Массив записей о закрытых сделках
      */
     static private function SelectData ($input) {
        $output["open"]  = array();
        $output["close"] = array();
        for ($i = 0; $i < count($input); $i++) {
           $data = self::PrepaireData ($input[$i]);
           if ($input[$i]["State"] == 0) {
              $output["open"][] = $data;
           } else {
              $output["close"][] = $data;
           }
        }
        return $output;
     }
     /**
      * Функция преобразования данных одной записи для отображения в интерфейсе
      *
      * @param array   $input              Массив данных одной записи в журнале
      *
      * @return array                      Массив данных одной записи для отображения в интерфейсе
      */
     static private function PrepaireData ($input) {
        $servicedate = new DateTime();
        foreach (self::$columns as $key => $value) {
           switch ($key) {
              case "Opentime":
              case "Closetime":
                 if ($input[$key] == 0) {
                    $data = "-";
                 } else {
                    $data = $servicedate->setTimestamp($input[$key])->format("d.m.Y H:i");
                 }
                 break;
              case "CloseID":
                 if ($input[$key] == 0) {
                    $data = "-";
                 } else {
                    $data = $input[$key];
                 }
                 break;
              default:
                 $data = $input[$key];
                 break;
           }
           $output[$key] = $data;
        }
        return $output;
     }
  }
?>