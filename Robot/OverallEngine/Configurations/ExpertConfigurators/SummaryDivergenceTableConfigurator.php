<?php
  /**
   * SummaryDivergenceTableConfigurator: Производный класс конструктора конфигурации таблицы расхождений сводных данных по типам обмена
   */
  class SummaryDivergenceTableConfigurator extends SummaryTableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]        .= "ой таблицы расхождений сводных данных по типам обмена";
        $output["columnName"]         = array("timestamp", "min", "open", "close", "max", "Summary");
        $output["columnType"]         = array("INT(11)", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("SummaryData"));
        $output["valueArray"]         = array(array("timestamp", "minBid", "minAsk", "openBid", "openAsk", "closeBid", "closeAsk", "maxBid", "maxAsk"));
        $output["printFields"]        = array("timestamp", "min", "open", "close", "max", "Summary");
        $output["chartsTypology"]     = array(1, 1, 1, 1, 1, 1);
        $output["AxesTypology"]       = array(0, 0, 0, 0, 0, 0);
        $output["chartsOptions"]      = array(array("Время"), 
                                              array("Минимум", "line", "red"), 
                                              array("Открытие", "line", "saddlebrown"),
                                              array("Закрытие", "line", "orangered"),
                                              array("Максимум", "line", "green"),
                                              array("Summary", "line", "navy"));
        $output["boxItems"]           = array("minDiv", "openDiv", "closeDiv", "maxDiv", "Summary");
        $output["boxValues"]          = array("1" , "2", "3", "4", "5");
        return $output;
     }
  }
?>