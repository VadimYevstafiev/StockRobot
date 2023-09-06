<?php
  /**
   * Configurations: Класс установки конфигурации ядра приложения
   */
  class Configurations {
     use ConnectConfigurations, TimeframeFunctions, TableDataConfigurations,  
         DateTimesFunctions, RefrashInterfaceTime, IndexisArrays, ServiceConfigurations,
         ListenerConfigurations, ProtocolConfigurations, JournalConfigurations, 
         ChartsConfigurations;
     /**
      * @param object  $instance    Экземпляр модуля
      * @param string  $direction   Идентификатор торговой пары
      * @param array   $indexis     Массив индексов торговой пары
      *
      * @param array   $timeframes  Массив параметров таймфреймов
      *                             "general" - номенклатура таймфреймов
      *                             "base" - таймфрейм базовых данных о курсе
      *                             "refresh" - таймфрейм обновления данных Эксперта
      *                             "trade"  - таймфрейм результатов Эксперта, по которым ведется торговля
      *
      * @param array   $tabledata   Массив конфигурации таблиц баз данных и их обработчиков
      *
      * @param array   $datetimes   Массив дат и времени
      *
      * @param array   $tables      Исходный массив конфигурации таблиц баз данных и их обработчиков
      *
      * @param integer $gorizont    Количество периодов, данные которых обрабатываются сценарием
      */
     static protected $instance = NULL;
     static public $direction;
     protected $indexis         = array();
     protected $timeframes      = array();
     protected $tabledata       = array();
     protected $сhartsdata      = array();
     protected $datetimes       = array();
     protected $tables          = array(
                                         "Simple"   => array(
                                                              array("Type"=>"RateData",            "charts"=>0),
                                                              array("Type"=>"ADX",                 "charts"=>2,  "period"  =>14),
                                                              array("Type"=>"EMA",                               "period"  =>14),
                                                              array("Type"=>"Convert",             "charts"=>0),
                                                              array("Type"=>"Stochastic",          "charts"=>3,  "period1" =>14,   "period2" =>3, "period3" =>3, ),
                                                              array("Type"=>"ASI",                               "Limit"   =>1),
                                                              array("Type"=>"impSAR",              "charts"=>0,  "AF"      =>0.02, "maxAF"   =>0.2),
                                                              array("Type"=>"PrimarySignals",      "charts"=>1)
                                                            ),
                                         "Summary"  => array(
                                                              array("Type"=>"SummaryData",         "charts"=>0),
                                                              array("Type"=>"SummaryDivergence",   "charts"=>2),
                                                              array("Type"=>"SummarySignals",      "charts"=>1),
                                                              array("Type"=>"SummaryConverts",     "charts"=>0),
                                                              array("Type"=>"Results",                        )
                                                            )
                                       );
     protected $gorizont        = 240;
     /**
      * Функция установки конфигурации ядра приложения
      */
     static public function SetConfigurations() {
        if (empty(self::$instance)) {
           self::$instance = new static();
        }
     }
     /**
      * Функция получения массива индексов торговой пары
      *
      * @return array        Массив индексов торговой пары
      */
     static public function GetIndexis() {
        self::SetConfigurations();
        return self::$instance->indexis;
     }
     /**
      * Функция получения массива параметров таймфреймов
      *
      * @return array        Массив параметров таймфреймов
      */
     static public function GetTimeframes() {
        self::SetConfigurations();
        return self::$instance->timeframes;
     }
     /**
      * Функция получения массива конфигурации таблиц баз данных и их обработчиков
      *
      * @return array        Массив конфигурации таблиц баз данных и их обработчиков
      */
     static public function GetTabledata() {
        self::SetConfigurations();
        return self::$instance->tabledata;
     }
     /**
      * Функция получения массива дат и времени
      *
      * @return array        Массив дат и времени
      */
     static public function GetDatetimes() {
        self::SetConfigurations();
        return self::$instance->datetimes;
     }
     /**
      * Функция корректировки массива дат и времени
      *
      * @param integer $start Начальная метка времени данных таблицы слушателя
      *
      * @return array         Массив дат и времени
      */
     static public function CorrectDatetimes($start) {
        self::SetConfigurations($start);
        self::$instance->datetimes = self::CorrectStartDate($start, self::$instance->datetimes);
     }

     /**
      * Конструктор
      */
     protected function __construct () {
        $result = $this::CreateIndexisArray(DIRECTION);
        $this->indexis = $result["indexis"];
        $this->timeframes = $result["timeframes"];
        $this->tabledata = $this::CreateTableData($this->tables, 
                                                  $this->timeframes["general"], 
                                                  $this->indexis);
        $this->datetimes = $this::SetDateTimes($this->tabledata, $this->gorizont);
        $this::CreateListenerConfiguration($result["listener"]);
        $this::CreateChartsConfigurations();
     }
   }
?>