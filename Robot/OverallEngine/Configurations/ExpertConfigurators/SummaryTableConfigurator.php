<?php
  /**
   * SummaryTableConfigurator: Базовый класс конструктора конфигурации сводных таблиц по типам обмена
   */
  class SummaryTableConfigurator extends TableConfigurator {
     /**
      * @param array   $timeframes  Массив таймфреймов
      */
     protected $timeframes;

     public function __construct ($tabletypology, $timeframe, $indexis, $timeframes) {
        parent::__construct($tabletypology, $timeframe, $indexis);
        $this->timeframes = $timeframes;
        $this->summary    = TRUE;
     }
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["timeframes"] = $this->timeframes;
        $output["keyType"]    = $this::ConsructSourceArray(array("PrimarySignals"));
        $output["keyValue"]   = array(array("timestamp", "Composite"));
        return $output;
     }
  }
?>