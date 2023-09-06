<?php
  /**
   * Expert: Класс функций вызова эксперта
   */
  class Expert extends MainModule {
     /**
      * @param  object   $dbc           Идентификатор соединения
      * @param  array    $tabledata     Массив конфигурации таблиц баз данных и их обработчиков
      * @param  array    $datetimes     Массив дат и времени
      * @param  array    $timedata      Массив начальных меток времени, с которых нужно извлечь данные
      *                                 в таблицы данных графиков
      */
     private $dbc;
     private $tabledata;
     private $datetimes;
     static private $timedata = array();
     /**
      * Функция обновления данных эксперта
      */
     static public function Refresh() {
        parent::Refresh();
        Configurations::SetRefrashInterfaceTime(self::$timedata);
     }
     /**
      * Функция контроля времени обновления данных 
      *
      * @param  integer  $time          Метка текущего времени
      * @param  integer  $time          Контрольная метка времени
      *
      * @return bool                    TRUE - если необходимо обновлять данные
      *                                 FALSE - если нет необходимости обновлять данные
      */
     static public function CheckRefreshTimeframe($time) {
        $datetimes = Configurations::GetDatetimes();
        $factor    = min($datetimes["module"]);
        $output    = parent::CheckRefreshTimeframe($time, $factor);
        return $output;
     }
     /**
      * Функция извлечения рекомендаций эксперта
      *
      * @return array                   Массив извлеченных значений 
      */
     static public function GetResults() {
        $tabledata  = Configurations::GetTabledata();
        $datetimes  = Configurations::GetDatetimes();
        $timeframes = Configurations::GetTimeframes();
        $connect    = MySQLconnector::GetConnect();
        $dbc        = $connect["work"];
        $service    = self::CompleteServiceDatetimes($timeframes["trade"], $datetimes);
        $result     = new ResultsTableComplector ($dbc, NULL, $tabledata["Summary"]["Results"][$timeframes["trade"]], $service); 
        $output     = $result->GetResults();
        return $output;
     }
     /**
      * Конструктор
      * @param  array    $dbc           Массив идентификаторов соединения с базой данных 
      * @param  object   $configuration Объект конфигурации ядра приложения
      */
     protected function __construct () {
        $this->flag      = "expert";
        $connect         = MySQLconnector::GetConnect();
        $this->dbc       = $connect["work"];
        $this->tabledata = Configurations::GetTabledata();
        $this->datetimes = Configurations::GetDatetimes();
     }
     /**
      * Служебная функция обновления данных эксперта
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      *
      */
     protected function ServiceComplete($protocol) {
        foreach ($this->tabledata as $key => $type) {
           foreach ($type as $typeItem) {
              foreach ($typeItem as $timeframe => $timeframeItem) {
                 $service = $this::CompleteServiceDatetimes($timeframe, $this->datetimes);
                 $result = $this::Select($this->dbc, $protocol, $timeframeItem, $service);
                 $data = $result->Complete();
                 if (isset($timeframeItem["chartsNumber"])) {
                    $this::CompleteTimedata($key, $timeframe, $timeframeItem["chartsNumber"], $data);
                 }
              }
           }
        }
        $this::CompleteBidsData($protocol);
     }
     /**
      * Функция выбора комплектатора таблицы
      *
      * @param  object   $dbc           Идентификатор соединения
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $tabledata     Массив конфигурации таблицы
      * @param  array    $datetimes     Массив дат и времени
      *
      * @return object                  Комплектатор таблицы
      */
     protected function Select($dbc, $protocol, $tabledata, $datetimes) {
        switch ($tabledata["ParentType"]) {
           case "BaseData":
              $output = new BaseDataTableComplector ($dbc, $protocol, $tabledata, $datetimes);
              break;
           case "RateData":
              $output = new RateDataTableComplector ($dbc, $protocol, $tabledata, $datetimes);
              break;
           case "impSAR":
              $output = new impSARDataTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "ADX":
              $output = new ADXDataTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "EMA":
              $output = new EMADataTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "Convert":
              $output = new ConvertDataTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "Stochastic":
              $output = new StochasticDataTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "ASI":
              $output = new ASIDataTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "PrimarySignals":
              $output = new PrimarySignalsTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "SummaryData":
              $output = new SummaryDataTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "SummaryDivergence":
              $output = new SummaryDivergenceTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "SummarySignals":
              $output = new SummarySignalsTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "SummaryConverts":
              $output = new SummaryConvertsTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
           case "Results":
              $output = new ResultsTableComplector ($dbc, $protocol, $tabledata, $datetimes);  
              break;
        } 
        return $output;
     }
     /**
      * Служебная функция комплектации массива начальных меток времени, с которых нужно извлечь данные
      * в таблицы данных графиков
      *
      * @param  string   $key           Ключ подмассива массива структуры графиков ("Simple" или "Summary")
      * @param  string   $timeframe     Ключ подмассива таймфрейма подмассива массива структуры графиков
      * @param  integer  $number        Номер графика в подмассиве таймфрейма подмассива массива структуры графиков
      * @param           $data          Данные для записи в массив начальных меток времени
      *
      */
     protected function CompleteTimedata($key, $timeframe, $number, $data) {
        if (!isset(self::$timedata[$key])) {
           self::$timedata[$key] = array();
        }
        if (!isset(self::$timedata[$key][$timeframe])) {
           self::$timedata[$key][$timeframe] = array();
        }
        if (!isset(self::$timedata[$key][$timeframe][$number])) {
           self::$timedata[$key][$timeframe][$number] = $data;
        } else if (is_array(self::$timedata[$key][$timeframe][$number])){
           foreach (self::$timedata[$key][$timeframe][$number] as $index => $value) {
              if ($data[$index] < $value) {
                 self::$timedata[$key][$timeframe][$number][$index] = $data[$index];
              }
           }
        } else {
           if ($data < self::$timedata[$key][$timeframe][$number]) {
              self::$timedata[$key][$timeframe][$number] = $data;
           }
        }
     }
     /**
      * Служебная функция комплектации таблицы заявок
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      */
     protected function CompleteBidsData($protocol) {
        $timeframes = Configurations::GetTimeframes();
        $indexis = Configurations::GetIndexis();
        $tabledata =  (new BidsDataTableConfigurator($timeframes["trade"], $indexis))->Complete();
  echo '<p> $tabledata</p>';
  print_r($tabledata);
        $service = $this::CompleteServiceDatetimes($timeframes["trade"], $this->datetimes);
        (new BidsDataTableComplector($this->dbc, $protocol, $tabledata, $service))->Complete();
     }
  }
?>