<?php
  /**
   * RateDataTableConfigurator: Производный класс конструктора конфигурации таблиц данных о курсе 
   */
  class RateDataTableConfigurator extends TableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]         = "таблицы " . $output["Definition"] . "ых данных, тип обмена: ";
        $output["columnName"]         = array("timestamp", "min", "open", "close", "max");
        $output["columnType"]         = array("INT(11)", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE");
        $output["printFields"]        = array("timestamp", "min", "open", "close", "max");
        $output["chartsTypology"]     = array(1, array(1, 1, 1, 1));
        $output["AxesTypology"]       = array(0, 0);
        $output["chartsOptions"]      = array(array("Время"), array(array("Минимум", "Открытие", "Закрытие", "Максимум"), "candlesticks"));
        $output["boxItems"]           = array("Курс");
        $output["boxValues"]          = array(1);
        return $output;
     }
  }
?>