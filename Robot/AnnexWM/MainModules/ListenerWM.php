<?php
  /**
   * Listener: Класс функций слушателя 
   */
  class Listener  extends MainModule {
     use ConvertDataFunction, ValidatorFunctions;
     /**
      * @param  resource $dbc           Идентификатор соединения с базой данных слушателя
      * @param  array    $indexis       Массив значений типов обмена
      * @param  array    $data          Массив данных о текущих заявках на рынке 
      */
     private $dbc;
     private $indexis;
     static private $data;
     /**
      * Функция обновления данных слушателя
      *
      * @return array                   Массив данных о текущих заявках на рынке (при успешном исполнении)
      *                                 или FALSE при ошибке при исполнении
      */
     static public function Refresh() {
        parent::Refresh();
        $output = self::$data;
        return $output;
     }
     /**
      * Функция извлечения данных  
      *
      * @param  string   $index         Тип обмена
      * @param  string   $timefactor    Начальная метка времени, с которой нужно извлечь данные
      * @param  string   $relate        Логическое отношение
      * @param  string   $limit         Предельное количество строк, из которых нужно извлечь данные
      * @param  bool     $inverse       Индикатор порядка сортировки данных (FALSE - прямой, TRUE - обратный)
      *
      * @return array                   Массив извлеченных значений 
      */
     static public function ExtractData($index, $timefactor, $relate = ">=", $limit = NULL, $inverse = FALSE) {
        $configuration = Configurations::GetListenerConfiguration();
        if ($index == "Bid") {
           $i = 0;
        } else {
           $i = 1;
        }
        switch (Configurations::GetTradingtype("Expert")) {
           case "zero":
              $j = 1;
              break;
           case "quality":
              $j = 2;
              break;
        }
        $valueArray[0] = $configuration["columnName"][0];
        $valueArray[1] = $configuration["columnName"][$j + $i * 2];
        $query = SelectQuery::Create($configuration["Tablename"], $valueArray); 
        $query->AddWHERE("timestamp", $relate, $timefactor); 
        $query->AddORDER("timestamp", $inverse);
        if (!empty($limit)) {
           $query->AddLIMIT($limit);
        }
        $connect = MySQLconnector::GetConnect();
        $output = $connect["listener"]->SendQuery($query);
        return $output;
     }
     /**
      * Служебная функция обновления данных слушателя
      *
      * @param  object   $protocol      Комплектатор таблицы протоколов
      *
      * @return array                   Массив данных о текущих заявках на рынке
      */
     protected function ServiceComplete($protocol) {
        $protocol->AddMessage("data");
        $datetimes = Configurations::GetDatetimes();
        $result = $this::GetData();
        $protocol->AddMessage("get");

        $result = $this::PrepaireData($result, $datetimes);
        $protocol->AddMessage("prep");

        $this::Validation($protocol, $result[0]);

        $writeresult = $this::WriteData($result);
        $protocol->AddMessage("save");
        self::$data["timestamp"] = $result[0];
        self::$data["zero"]      = array("Bid" => $result[1], "Ask" => $result[3]);
        self::$data["quality"]   = array("Bid" => $result[2], "Ask" => $result[4]);
     }
     /**
      * Конструктор
      */
     protected function __construct () {
        $this->flag          = "listener";
        $connect             = MySQLconnector::GetConnect();
        $this->dbc           = $connect[$this->flag];
        $this->indexis       = Configurations::GetIndexis();
        $this->configuration = Configurations::GetListenerConfiguration();
        $this->sdate         = $this->configuration["startdate"];
      }
     /**
      * Функция получения массива текущих заявок на рынке
      *
      * @return array                   Массив текущих заявок на рынке
      */
     private function GetData() {
        $output = array();
        foreach ($this->indexis["general"] as $key => $value) {
           $output[$key] = WMbidList::SendQuery($value);
           if (empty($output[$key])) {
              throw new ListenerWMexception("Не удалось получить данные.");
           }
        }
        return $output;
     }
     /**
      * Функция преобразования для записи данных о текущих заявках на рынке
      *
      * @param  array    $input         Массив данных о текущих заявках на рынке
      * @param  array    $datetimes     Массив дат и времени
      *
      * @return array                   Массив данных для записи
      */
     private function PrepaireData($input, $datetimes) {
        $service = new DateTime($datetimes["currenttime"]->format("Y-m-d H:i"));
        $output[0] = $service->getTimestamp();
        $i = 0;
        foreach ($this->indexis["valueArray"] as $key => $value) {
           $data = array();
           foreach ($input[$key] as $row) {
              $data[] = array("rate"=>(string) $row[$value[0]], "sumall"=>(string) $row[$value[1]]);
           }
           if (!$data) {
              throw new ListenerWMexception("Не удалось преобразовать данные для записи.");
           }
           $j = 0;
           $sum = 0;
           while ($sum < $this->configuration["quality"]) {
              $sum += $this::ConvertData($data[$j]["sumall"]);
              $j++;
           } 
           $output[2 * $i + 1] = $this::ConvertData($data[0]["rate"]);
           $output[2 * $i + 2] = $this::ConvertData($data[$j - 1]["rate"]);
           $i++;
        }
        return $output;
     }
     /**
      * Функция проверки таблиц баз данных слушателя
      *
      * @param  object   $protocol      Комплектатор таблицы протоколов
      * @param  array    $input         Метка текущего времени
      */
     protected function Validation($protocol, $input) {
        $result[0] = $this::CheckTable($this->configuration["Tablename"], 
                                       $this->configuration["columnName"], 
                                       $this->configuration["columnType"]);
        if ($result[0] > 3) {
           $protocol->AddMessage("valid", array("res" => $result[0]));
           $data = $this::TableCreator($result[0], 
                                       $this->configuration["Tablename"], 
                                       $this->configuration["columnName"], 
                                       $this->configuration["columnType"]);
           $protocol->AddArray("valid", $data);
        } else if ($this::CheckRefreshTimeframe($input)) {
           $result = $this::CheckData($this->configuration["Tablename"], 
                                      $this->configuration["columnName"][0]);
        }
        $this::CheckOutdated($result, 
                             $this->configuration["Tablename"], 
                             $this->configuration["columnName"][0]);
     }
     /**
      * Функция записи данных о текущих заявках на рынке
      *
      * @param  array    $data          Массив данных о текущих заявках на рынке
      *
      * @return bool                    Результат операции записи данных
      */
     private function WriteData($data) {
        try {
            $output = $this->dbc->AddRow($this->configuration["Tablename"], 
                                         $this->configuration["columnName"], 
                                         $data);  
        } catch (Exception $e) {
           throw new ListenerWMexception("Не удалось записать данные.", $e);
        }
        return $output;
     }
  }
?>

