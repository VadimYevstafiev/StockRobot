<?php
  /**
   * PrimarySignalsTableConfigurator: Производный класс конструктора конфигурации таблиц предварительных индикаторов  
   */
  class PrimarySignalsTableConfigurator extends TableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]        .= "ых таблиц предварительных индикаторов, тип обмена: ";
        $output["keyType"]            = $this::ConsructSourceArray(array("impSAR"));
        $output["keyValue"]           = array(array("timestamp", "position"));
        $output["columnName"]         = array("timestamp", "target", "ADX", "Composite");
        $output["columnType"]         = array("INT(11)", "TINYINT", "TINYINT", "TINYINT");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("impSAR", "ADX", "Convert"));
        $output["valueArray"]         = array(array("timestamp", "signalvalue", "SAR", "shadow"), array("integralFactor"), array("middle"));
        $output["printFields"]        = array("timestamp", "Composite", "target", "ADX");
        $output["chartsTypology"]     = array(1, 1, 1, 1);
        $output["AxesTypology"]       = array(0, 0, 0, 0);
        $output["chartsOptions"]      = array(array("Время"), array("Composite", "steppedArea", "saddlebrown"), array("target", "bars", "orangered"), array("ADX", "bars", "navy"));
        $output["boxItems"]           = array("Composite", "target", "ADX");
        $output["boxValues"]          = array(1, 1, 1);
        return $output;
     }
  }  
?>