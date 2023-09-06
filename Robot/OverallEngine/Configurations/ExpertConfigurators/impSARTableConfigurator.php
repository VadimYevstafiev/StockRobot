<?php
   /**
   * impSARTableConfigurator: Производный класс конструктора конфигурации таблиц параболического SAR
   */
  class impSARTableConfigurator extends TableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]         = "таблицы " . $output["Definition"] . "ого параболического SAR, тип обмена: ";
        $output["serviceperiod"]      = 2;
        $output["AF"]                 = $this->tabletypology["AF"]; 
        $output["maxAF"]              = $this->tabletypology["maxAF"];
        $output["columnName"]         = array("timestamp", "SAR", "shadow", "EP", "curAF", "position", "signalvalue");
        $output["columnType"]         = array("INT(11)", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE", "TINYINT(1)", "TINYINT");
        $output["keyType"]            = $this::ConsructSourceArray(array("impSAR"));
        $output["keyValue"]           = array(array("timestamp", "position"));
        $output["sourceArray"]        = $this::ConsructSourceArray(array("RateData"));
        $output["valueArray"]         = array(array("timestamp", "min", "max"));
        $output["printFields"]        = array("shadow", "SAR");
        $output["chartsTypology"]     = array(1, 1);
        $output["AxesTypology"]       = array(0, 0);
        $output["chartsOptions"]      = array(array("", "line", "gray", 0, "diamond", 5), array("SAR", "line", "red", 0, "diamond", 5));
        $output["chartsOptionsItems"] = array("columnname", "type", "color", "lineWidth", "pointShape", "pointSize");
        $output["boxItems"]           = array("impSAR");
        $output["boxValues"]          = array(array(1, 1));
        return $output;
     }
  }  
?>