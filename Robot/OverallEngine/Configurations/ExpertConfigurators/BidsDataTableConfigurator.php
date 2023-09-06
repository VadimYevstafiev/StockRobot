<?php
  /**
   * BidsDataTableConfigurator: Производный класс конструктора конфигурации таблиц заявок
   */
  class BidsDataTableConfigurator extends TableConfigurator {
     /**
      * @param integer $timeframe      Значение таймфрейма
      * @param integer $timemodule     Величина единицы таймфрейма в секундах
      * @param array   $indexis        Массив индексов торговой пары
      */
     public function __construct ($timeframe, $indexis) {
        $this->timemodule    = $this::ConvertTimemodule($timeframe);
        $this->timeframe     = $this::ConvertTimeframe($timeframe);
        $this->indexis       = $indexis;
     }
     /**
      * Статическая функция создания массива конфигурации таблицы заявок
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output["Timeframe"]          = $this->timeframe;
        $output["timemodule"]         = $this->timemodule;
        $output["Type"]               = $this::ConsructType("BidsData", 
                                                            $this->timeframe, 
                                                            $this->timemodule, 
                                                            $this->indexis["direction"]);
        $output["Definition"]         = $this::ConsructDefinition($this->timeframe, $this->timemodule);
        $output["Definition"]        .= "ой таблицы заявок";
        $output["columnName"]         = array("timestamp", "ActiveBids", "CloseMaker", "CloseTaker");
        $output["columnType"]         = array("INT(11)", "VARCHAR(8)", "VARCHAR(8)", "VARCHAR(8)");
        $output["printFields"]        = array("ActiveBids", "CloseMaker", "CloseTaker");
        $output["chartsTypology"]     = array(1, 1, 1);
        $output["AxesTypology"]       = array(0, 0, 0);
        $output["chartsOptions"]      = array(array("Bids", "line", "orangered", 0, "diamond", 5), array("Maker", "line", "orangered", 0, "star", 8), 
                                              array("Taker", "line", "orangered", 0, "circle", 8));
        $output["chartsOptionsItems"] = array("columnname", "type", "color", "lineWidth", "pointShape", "pointSize");
        $output["boxItems"]           = array("Bids");
        $output["boxValues"]          = array(array(1, 1, 1));
        return $output;
     }
  }   
?>