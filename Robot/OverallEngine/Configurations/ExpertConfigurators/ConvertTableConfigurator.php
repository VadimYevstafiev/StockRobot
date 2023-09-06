<?php
  /**
   * ConvertTableConfigurator: Производный класс конструктора конфигурации таблиц конверта
   */
  class ConvertTableConfigurator extends TableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]        .= "ых таблиц конверта, тип обмена: ";
        $output["columnName"]         = array("timestamp", "high", "low", "middle");
        $output["columnType"]         = array("INT(11)", "DOUBLE", "DOUBLE", "DOUBLE");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("EMA", "ADX"));
        $output["valueArray"]         = array(array("timestamp", "value"), array("ATR"));
        $output["printFields"]        = array("high", "low", "middle");
        $output["chartsTypology"]     = array(1, 1, 1);
        $output["AxesTypology"]       = array(0, 0, 0);
        $output["chartsOptions"]      = array(array("high", "line", "green"), array("low", "line", "red"), array("middle", "line", "orangered"));
        $output["boxItems"]           = array("high", "low", "middle");
        $output["boxValues"]          = array(1, 1, 1);
        return $output;
     }
  }  
?>