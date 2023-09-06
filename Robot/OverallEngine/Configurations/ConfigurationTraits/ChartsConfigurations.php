<?php
  /**
   * ChartsConfigurations: Трейт, определяющий функции установки конфигурации таблиц графиков
   */
  trait ChartsConfigurations {
     use ConsructTypeFunction, ConsructDefinitionFunction;
     /**
      * @param array $boxOptions        Массив конфигурации таблицы опций боковой панели интерфейса
      * @param array $сhartsOptions     Массив конфигурации таблиц опций графиков
      * @param array $сhartsData        Массив конфигурации таблиц данных графиков
      * @param array $сhartsStructure   Массив структуры графиков
      */
      private $boxOptions      = array();
      private $chartsOptions   = array();
      private $chartsData      = array();
      private $chartsStructure = array();

     /**
      * Функция получения массива конфигурации таблицы опций боковой панели интерфейса
      *
      * @return array        Массив конфигурации таблицы опций боковой панели интерфейса
      */
     public function GetBoxOptionsConfigurations() {
        self::SetConfigurations();
        return self::$instance->boxOptions;
     }
     /**
      * Функция получения массива конфигурации таблицы опций графиков
      *
      * @return array        Массив конфигурации таблицы опций графиков
      */
     public function GetChartsOptionsConfigurations() {
        self::SetConfigurations();
        return self::$instance->сhartsOptions;
     }
     /**
      * Функция получения массива конфигурации таблиц данных графиков
      *
      * @return array        Массив конфигурации таблиц данных графиков
      */
     public function GetChartsDataConfigurations() {
        self::SetConfigurations();
        return self::$instance->chartsData;
     }
     /**
      * Функция получения массива структуры графиков
      *
      * @return array        Массив конфигурации структуры графиков
      */
     public function GetChartsStructure() {
        self::SetConfigurations();
        return self::$instance->сhartsStructure;
     }
     /**
      * Функция определения массивов конфигурации 
      *
      */
     private function CreateChartsConfigurations() {
        $this::CreateChartsStructure();
        $this::CreateBoxOptions();
        $this::CreateChartsOptions();
     }
     /**
      * Функция создания массива конфигурации структуры графиков
      */
     private function CreateChartsStructure () {
        foreach ($this->tables as $key => $value) {
           $service = array();
           for ($i = 0; $i < count($this->timeframes["general"]); $i++) {
              $j = $this->timeframes["general"][$i];
              $service [$j] = array();
              foreach ($value as $data) {
                 if (isset($data["charts"])) { 
                    $service[$j][$data["charts"]][] = $data["Type"];
                 }
              }
              ksort($service[$j]);
           }
           $this->сhartsStructure[$key] = $service;
           
        }
     }
     /**
      * Функция определения массива конфигурации таблицы опций боковой панели интерфейса
      */
     private function CreateBoxOptions() {
        $this->boxOptions["Tablename"] = MARKETID . "boxOpt" . $this->indexis["direction"];
        $this->boxOptions["Definition"] = "опций боковой панели интерфейса";
        $this->boxOptions["columnName"] = array("type");
        $this->boxOptions["columnType"] = array("TEXT");
        for ($i = 0; $i < count($this->timeframes["general"]); $i++) {
           $this->boxOptions["columnName"][] = $this->timeframes["general"][$i];
           $this->boxOptions["columnType"][] = "TEXT";
        }
     }
     /**
      * Функция определения массива конфигурации таблиц опций графиков и массива конфигурации таблиц данных графиков
      */
     private function CreateChartsOptions() {
        foreach ($this->сhartsStructure as $type => $mainTable) {
           $this->сhartsOptions[$type] = array();
           $this->chartsData[$type]    = array();
           foreach ($mainTable as $timeframe => $table) {
              $this->сhartsOptions[$type][$timeframe] = $this::CreateChartsOptionsItem($type, $timeframe);
              $this->chartsData[$type][$timeframe]    = array();
              for ($i = 0; $i < count($table); $i++) {
                 $this->chartsData[$type][$timeframe][] = $this::CreateChartsDataItem($table[$i], $timeframe, $i, $type);
              }
           }
        }
     }
     /**
      * Функция определения элемента массива конфигурации таблиц опций графиков
      *
      * @param string  $key            Ключ подмассива массива структуры графиков ("Simple" или "Summary")
      * @param string  $timeframe      Таймфрейм
      *
      * @return array                  Элемент массива конфигурации таблиц опций графиков
      */
     private function CreateChartsOptionsItem($key, $timeframe) {
        $output = array();
        $output["Tablename"] = MARKETID . $this::DetectTimeframe ($this::ConvertTimeframe($timeframe), 
                               $this::ConvertTimemodule($timeframe)) . "chOpt" . $key . $this->indexis["direction"];
        $output["Definition"] = "опций " . $this::ConsructDefinition($this::ConvertTimeframe($timeframe),
                                $this::ConvertTimemodule($timeframe)) . "ых ";
        if ($key == "Summary") {
           $output["Definition"] .= "сводных графиков";
        } else {
           $output["Definition"] .= "графиков по типам обмена";
        }
        $output["columnName"] = array("chartNumber", "chartsTypology", "chartsOptions", "AxesTypology");
        $output["columnType"] = array("TEXT", "TEXT", "TEXT" , "TEXT");
        return $output;
     }
     /**
      * Функция определения элемента массива конфигурации данных графиков
      *
      * @param array   $structureItem  Элемент соответствующий номеру графика, в подмассиве
      *                                массива структуры графиков (с ключом "Simple" или "Summary"),
      *                                соответствующем таймфрейму, данные для которого записываются
      * @param string  $timeframe      Таймфрейм
      * @param integer $chartNumber    Номер графика в подмассиве массива структуры графиков (с ключом ""Simple" или "Summary"),
      *                                соответствующем таймфрейму, данные для которого записываются
      * @param string  $key            Ключ подмассива массива структуры графиков ("Simple" или "Summary")
      *
      * @return array                  Элемент массива конфигурации таблиц данных графиков
      */
     private function CreateChartsDataItem($structureItem, $timeframe, $chartNumber, $key) {
        $output = array();
        $service = array();
        $output["Type"]           = MARKETID;
        $output["Definition"]     = "данных " . ($chartNumber + 1) .  "-ого ";
        $output["Timeframe"]      = $this::ConvertTimeframe($timeframe);
        $output["timemodule"]     = $this::ConvertTimemodule($timeframe);
        $output["Type"]          .= $this::DetectTimeframe ($output["Timeframe"], 
                                    $output["timemodule"]) . "chData_" . 
                                    $chartNumber . "_" . $this->indexis["direction"]; 
        $output["Definition"]    .= $this::ConsructDefinition($output["Timeframe"],
                                    $output["timemodule"]);
        if ($flag == "Summary") {
           $output["Definition"] .= "ого сводного графика";
        } else {
           $output["Definition"] .= "ого графика, тип обмена: ";
        }
        $output["indexis"]        = array_keys($this->indexis["general"]);
        $output["columnName"]     = array();
        $output["columnType"]     = array();
        $output["sourceArray"]    = array();
        $output["valueArray"]     = array();
        for ($i = 0; $i < count($structureItem); $i++) {
           $identifier  = $this->tabledata[$key][$structureItem[$i]][$timeframe]["ParentType"];
           $printfields = $this->tabledata[$key][$structureItem[$i]][$timeframe]["printFields"];
           $output["sourceArray"][$i] = $this::ConsructType($identifier, 
                                                            $output["Timeframe"], 
                                                            $output["timemodule"], 
                                                            $this->indexis["direction"]);
           $output["valueArray"][$i] = array();
           for ($j = 0; $j < count($printfields); $j++) {
              if ($printfields[$j] == "timestamp") {
                 $output["columnName"][] = $printfields[$j];
                 $output["columnType"][] = "INT(11)";
              } else {
                 $output["columnName"][] = $identifier . "_" . $printfields[$j];
                 $output["columnType"][] = "TINYTEXT";
              }
              $output["valueArray"][$i][$j] = $printfields[$j];
           }
        }
        return $output;
     }
   }
?>