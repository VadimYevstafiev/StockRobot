<?php
  /**
   * ADXTableConfigurator: Производный класс конструктора конфигурации таблиц Кумулятивного индекса колебаний (ASI)
   */
  class ASITableConfigurator extends TableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]        .= "таблицы " . $output["Definition"] . "ого Кумулятивного индекса колебаний ASI, тип обмена: ";
        $output["serviceperiod"]      = 1;
        $output["Limit"]              = $this->tabletypology["Limit"];
        $output["columnName"]         = array("timestamp", "ASI");
        $output["columnType"]         = array("INT(11)", "DOUBLE");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("RateData"));
        $output["valueArray"]         = array(array("timestamp", "open", "close", "min", "max"));
        $output["printFields"]        = array("timestamp", "ASI", "LSP", "HSP");
        $output["chartsTypology"]     = array(1, 1);
        $output["AxesTypology"]       = array(0, 0);
        $output["chartsOptions"]      = array(array("Время"), array("ASI", "line"));
        $output["boxItems"]           = array("ASI");
        $output["boxValues"]          = array(1);
        return $output;
     }
  }   
?>