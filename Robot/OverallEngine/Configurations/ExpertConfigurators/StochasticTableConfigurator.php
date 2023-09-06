<?php
  /**
   * StochasticTableConfigurator: Производный класс конструктора конфигурации таблиц стохастического осциллятора
   */
  class StochasticTableConfigurator extends TableConfigurator {
     /**
      * Функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]         = "таблицы " . $output["Definition"] . "ого стохастического осциллятора, тип обмена: ";
        $output["periodKfast"]        = $this->tabletypology["period1"];
        $output["periodKfull"]        = $this->tabletypology["period2"];
        $output["periodDfull"]        = $this->tabletypology["period3"];
        $output["serviceperiod"]      = $output["periodKfast"] + $output["periodKfull"] + $output["periodDfull"] - 1;
        $output["columnName"]         = array("timestamp", "Kfast", "Kfull", "Dfull", "position");
        $output["columnType"]         = array("INT(11)", "DOUBLE", "DOUBLE", "DOUBLE", "TINYINT(1)");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("RateData"));
        $output["valueArray"]         = array(array("timestamp", "close", "min", "max"));
        $output["printFields"]        = array("timestamp", "Kfull", "Dfull", 20, 80, "position");
        $output["chartsTypology"]     = array(1, 1, 1, 1, 1, 1);
        $output["AxesTypology"]       = array(0, 0, 0, 0, 0, 3);
        $output["chartsOptions"]      = array(array("Время"), array("Kfull", "line"), array("Dfull", "line", "red"), array("", "line", "black"),
                                        array("", "line", "black"), array("position", "steppedArea", "saddlebrown"));
        $output["boxItems"]           = array("K%", "D%", "Bords", "position");
        $output["boxValues"]          = array(1, 1, array(1, 1), 1);
        return $output;
     }
  }   
?>