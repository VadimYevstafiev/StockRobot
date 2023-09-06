<?php
  /**
   * SummarySignalsTableConfigurator: Производный класс конструктора конфигурации сводной таблицы сигналов по типам обмена
   */
  class SummarySignalsTableConfigurator extends SummaryTableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]        .= "ой сводной таблицы сигналов по типам обмена";
        $output["columnName"]         = array("timestamp", "CompositeBid", "CompositeAsk", "Summary");
        $output["columnType"]         = array("INT(11)", "TINYINT", "TINYINT", "TINYINT");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("PrimarySignals"));
        $output["valueArray"]         = array(array("timestamp", "Composite"));
        $output["printFields"]        = array("timestamp", "Summary", "CompositeBid", "CompositeAsk");
        $output["chartsTypology"]     = array(1, 1, 1, 1);
        $output["AxesTypology"]       = array(0, 0, 0, 0);
        $output["chartsOptions"]      = array(array("Время"), array("Summary", "steppedArea", "saddlebrown"), array("CompositeBid", "bars", "green"), 
                                        array("CompositeAsk", "bars", "red"));
        $output["boxItems"]           = array("Summary", "CompositeBid", "CompositeAsk" );
        $output["boxValues"]          = array(1, 1, 1);
        return $output;
     }
  }
?>