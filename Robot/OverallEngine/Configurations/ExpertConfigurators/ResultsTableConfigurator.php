<?php
  /**
   * ResultsTableConfigurator: Производный класс конструктора конфигурации таблиц рекомендаций
   */
  class ResultsTableConfigurator extends TableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]        .= "ой таблицы рекомендаций";
        $output["keyType"]            = $this::ConsructSourceArray(array("SummarySignals"));
        $output["keyValue"]           = array(array("timestamp", "Summary"));
        $output["columnName"]         = array("timestamp", "Bid", "Ask", "Stop", "Summary");
        $output["columnType"]         = array("INT(11)", "VARCHAR(8)", "VARCHAR(8)", "VARCHAR(8)", "VARCHAR(8)");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("SummaryConverts", "SummarySignals"));
        $output["valueArray"]         = array(array("timestamp", "Bid", "Ask", "Stop"), array("Summary"));
        return $output;
     }
  }   
?>