<?php
  /**
   * SummaryConvertsTableConfigurator: Производный класс конструктора конфигурации сводной таблицы конвертов по типам обмена
   */
  class SummaryConvertsTableConfigurator extends SummaryTableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]        .= "ой сводной таблицы конвертов по типам обмена";
        $output["keyType"]            = $this::ConsructSourceArray(array("SummarySignals"));
        $output["keyValue"]           = array(array("timestamp", "Summary"));
        $output["columnName"]         = array("timestamp", "Bid", "Ask", "Stop");
        $output["columnType"]         = array("INT(11)", "VARCHAR(8)", "VARCHAR(8)", "VARCHAR(8)");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("SummarySignals", "impSAR", "Convert"));
        $output["valueArray"]         = array(array("timestamp", "Summary"), array("SAR"), array("low", "high"));
        $output["printFields"]        = array("Bid", "Ask", "Stop");
        $output["chartsTypology"]     = array(1, 1, 1);
        $output["AxesTypology"]       = array(0, 0, 0);
        $output["chartsOptions"]      = array(array("Покупка", "line", "navy", 0, "diamond", 10), array("Продажа", "line", "navy", 0, "diamond", 10),
                                        array("Стоп", "line", "magenta", 0, "diamond", 10));
        $output["chartsOptionsItems"] = array("columnname", "type", "color", "lineWidth", "pointShape", "pointSize");
        $output["boxItems"]           = array("Конверт");
        $output["boxValues"]          = array(array(1, 1, 1));
        return $output;
     }
  }
?>