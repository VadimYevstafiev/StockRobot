<?php
  /**
   * ADXTableConfigurator: Производный класс конструктора конфигурации таблиц индекса средней направленности ADX
   */
  class ADXTableConfigurator extends TableConfigurator {
     /**
      * Функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]         = "таблицы " . $output["Definition"] . "ого индекса средней направленности ADX, тип обмена: ";
        $output["serviceperiod"]      = $this->tabletypology["period"] * 2 + 1;
        $output["columnName"]         = array("timestamp", "ADMplus", "ADMminus", "DIplus", "DIminus", "ADX", "ATR", "position", 
                                        "integralFactor");
        $output["columnType"]         = array("INT(11)", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE", "TINYINT(1)", 
                                        "TINYINT");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("RateData"));
        $output["valueArray"]         = array(array("timestamp", "close", "min", "max"));
        $output["printFields"]        = array("timestamp", "ADX", "DIplus", "DIminus", 25, "integralFactor");
        $output["chartsTypology"]     = array(1, 1, 1, 1, 1, 1);
        $output["AxesTypology"]       = array(0, 0, 0, 0, 0, 3);
        $output["chartsOptions"]      = array(array("Время"), array("ADX", "line"), array("DIplus","line", "green"), array("DIminus", "line", "red"), 
                                        array("", "line", "black"), array("integralFactor", "steppedArea", "saddlebrown"));
        $output["boxItems"]           = array("ADX", "DIplus", "DIminus", "LowBord", "integralFactor");
        $output["boxValues"]          = array(1, 1, 1, 1, 1);
        return $output;
     }
  }   
?>