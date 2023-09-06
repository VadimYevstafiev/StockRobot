<?php
  /**
   * TableConfigurator: Базовый класс конструктора конфигурации таблиц 
   */
  class TableConfigurator {
     use TimeframeFunctions, ConsructTypeFunction,
         ConsructDefinitionFunction;
     /**
      * @param array   $tabletypology  Исходный массив конфигурации таблицы 
      * @param integer $timeframe      Значение таймфрейма
      * @param integer $timemodule     Величина единицы таймфрейма в секундах
      * @param array   $indexis        Массив индексов торговой пары
      */
     protected $tabletypology;
     protected $timeframe;
     protected $timemodule;
     protected $indexis;

     public function __construct ($tabletypology, $timeframe, $indexis) {
        $this->tabletypology = $tabletypology;
        $this->timemodule    = $this::ConvertTimemodule($timeframe);
        $this->timeframe     = $this::ConvertTimeframe($timeframe);
        $this->indexis       = $indexis;
     }
     /**
      * Функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output["Timeframe"]             = $this->timeframe;
        $output["timemodule"]            = $this->timemodule;
        if (isset($this->tabletypology["period"])) {
           $output["period"]             = $this->tabletypology["period"];
           $output["serviceperiod"]      = $this->tabletypology["period"];
        }
        $output["ParentType"]            = $this->tabletypology["Type"];
        $output["Type"]                  = $this::ConsructType(
                                                               $this->tabletypology["Type"], 
                                                               $this->timeframe, 
                                                               $this->timemodule, 
                                                               $this->indexis["direction"]);
        $output["Definition"]            = $this::ConsructDefinition($this->timeframe, $this->timemodule);
        if (isset($this->tabletypology["charts"])) { 
           $output["chartsNumber"]       = $this->tabletypology["charts"];
           $output["chartsOptionsItems"] = array("columnname", "type", "color");
        }
        $output["indexis"]               = array_keys($this->indexis["general"]);
        return $output;
     }
     /**
      * Функция создания массива имен таблиц, из которых нужно извлечь данные
      *
      * @param array   $input          Массив родительских типов данных, которые нужно извлечь
      *
      * @return array                  Массив имен таблиц, из которых нужно извлечь данные
      */
     protected function ConsructSourceArray($input) {
        for ($i = 0; $i < count($input); $i++) {
           $output[$i] = $this::ConsructType($input[$i], 
                                             $this->timeframe, 
                                             $this->timemodule, 
                                             $this->indexis["direction"]);
        }
        return $output;
     }
  }
?>