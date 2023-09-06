<?php
  /**
   * SummaryDataTableConfigurator: Производный класс конструктора конфигурации сводной таблицы данных по типам обмена
   */
  class SummaryDataTableConfigurator extends SummaryTableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]        .= "ой сводной таблицы данных по типам обмена";
        $output["columnName"]         = array("timestamp", "minBid", "minAsk", "openBid", "openAsk", "closeBid", "closeAsk", "maxBid", "maxAsk");
        $output["columnType"]         = array("INT(11)", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE",  "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("RateData"));
        $output["valueArray"]         = array(array("timestamp", "min", "open", "close", "max"));
        $output["printFields"]        = array("timestamp", "minBid", "openBid", "closeBid", "maxBid", "minAsk", "openAsk", "closeAsk", "maxAsk");
        $output["chartsTypology"]     = array(1, array(1, 1, 1, 1), array(1, 1, 1, 1));
        $output["AxesTypology"]       = array(0, 0, 0);
        $output["chartsOptions"]      = array(array("Время"),
                                              array(array("Минимум", "Открытие", "Закрытие", "Максимум"), "candlesticks", "green"),
                                              array(array("Минимум", "Открытие", "Закрытие", "Максимум"), "candlesticks", "red"));
        $output["boxItems"]           = array("Bid", "Ask");
        $output["boxValues"]          = array("1" , "2");
        return $output;
     }
  }
?>