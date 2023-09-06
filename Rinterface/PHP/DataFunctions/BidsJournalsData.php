<?php
  /**
   * BidsJournalsData: Статический класс функций отправки данных журнала заявок
   */  
  class BidsJournalsData {
     use MakeHead, MakeItalic, MakeString, ParseBidsJournalData, ParseTraderData, 
         ParseWMbids, ParseLegendData;
     /**
      * @param array   $boxItems   Массив элементов боковой панели
      * @param array   $columns    Массив колонок таблицы 
      */
     static private $boxItems = array("all"     => "Все", 
                                      "active"  => "Активные", 
                                      "base"    => "Опорные", 
                                      "service" => "Служебные", 
                                      "close"   => "Закрытые");
     static private $columns  = array("BidID"        => array(80, "Номер заявки"), 
                                      "ParentID"      => array(80, "Номер родителя заявки"), 
                                      "Direction"     => array(100, "Направление заявки"), 
                                      "Initamountin"  => array(100, "Первона-чальная сумма"),
                                      "Amountin"      => array(100, "Сумма, выставленная на обмен"), 
                                      "Amountout"     => array(100, "Сумма, которую ожидается получить"),
                                      "Exchamountin"  => array(100, "Сумма, фактически выставленная на обмен"), 
                                      "Exchamountout" => array(100, "Сумма, фактически полученая при обмене"), 
                                      "Rate"          => array(100, "Заданное значение курса"),
                                      "Opentime"      => array(80, "Время открытия"), 
                                      "Scripttime"    => array(80, "Время последнего изменения"),
                                      "State"         => array(100, "Состояние"), 
                                      "Closetime"     => array(80, "Время закрытия"),
                                      "Closetype"     => array(100, "Тип закрытия"),
                                      "Legend"        => array(100, "Легенда"));

     /**
      * Функция отправки данных боковой панели интерфейса
      *
      * @return string              Строка JSON с данными
      */
     static public function boxJSON () {
        $output = json_encode(self::$boxItems, JSON_UNESCAPED_UNICODE);
        return $output;
     }
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
        $query = "SELECT * FROM " . $tablename . " ORDER BY BidID  DESC";
        $data = self::SelectData(self::GetData ($dbc, $query));
        array_shift(self::$boxItems);
        $output[0]["items"] = self::$boxItems;
        foreach (self::$columns as $key => $value) {
           $output[0]["head"][$key] = $value[1];
           $output[0]["size"][$key] = $value[0];
        }
        if ($type == "all") {
           foreach ($data as $key => $value) {
             $output[1][$key] = $data[$key];
           }
        } else {
           $output[1][$type] = $data[$type];
        }
        $output = json_encode($output, JSON_UNESCAPED_UNICODE);
        return $output;
     }
     /**
      * Функция отправки данных легенды
      *
      * @param         $dbc         Идентификатор соединения
      * @param string  $tablename   Имя таблицы
      * @param integer $number      Номер заявки
      *
      * @return string              Строка JSON с данными
      */
     static public function legendJSON ($dbc, $tablename, $number) {
        $query = "SELECT Legend FROM " . $tablename . " WHERE BidID = " . $number;
        $data = self::GetData($dbc, $query);
        $output = self::ParseLegendData($data[0]["Legend"]);
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
      * @return array  $output["active"]   Массив записей об активных заявках
      * @return array  $output["base"]     Массив записей об опорных заявках
      * @return array  $output["service"]  Массив записей о служебных заявках
      * @return array  $output["close"]    Массив записей о закрытых заявках
      */
     static private function SelectData ($input) {
        $output["active"]  = array();
        $output["base"]    = array();
        $output["close"]   = array();
        $output["service"] = array();
        $service           = array();
        for ($i = 0; $i < count($input); $i++) {
           $data = self::PrepaireData ($input[$i]);
           if ($input[$i]["State"] < 20) {
              $output["base"][] = $data;
           } else if ($input[$i]["State"] < 30) {
              $output["active"][] = $data;
           } else if ($input[$i]["State"] < 40) {
              $output["service"][] = $data;
           } else {
              $output["close"][] = $data;
              if ($input[$i]["Closetype"] > 3) {
                 $service[] = $data;
              }
           }
        }
        foreach ($service as $value) {
           $output["service"][] = $value;
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
              case "Scripttime":
              case "Opentime":
              case "Closetime":
                 if ($input[$key] == 0) {
                    $data = "-";
                 } else {
                    $data = $servicedate->setTimestamp($input[$key])->format("d.m.Y H:i");
                 }
                 break;
              case "ParentID":
                 if ($input[$key] == 0) {
                    $data = "-";
                 } else {
                    $data = $input[$key];
                 }
                 break;
              case "Legend":
                 unset($data);
                 break;
              case "State":
                 $data = self::WriteStateBid(array($input["State"], 
                                                   $input["Revnumber"]));
                 break;
              case "Closetype":
                 switch ($input["Closetype"]) {
                    case 0:
                       $data = "-";
                       break;
                    case 1:
                       $data = "Закрыта в режиме мейкера";
                       break;
                    case 2:
                       $data = "Закрыта в режиме тейкера";
                       break;
                    case 3:
                       $data = "Объединена с другой";
                       break;
                    case 4:
                       $data = "Не удалось вывести средства";
                       break;
                    case 5:
                       $data = "Средства выведены успешно";
                       break;
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