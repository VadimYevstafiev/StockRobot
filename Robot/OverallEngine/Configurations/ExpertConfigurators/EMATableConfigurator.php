<?php
  /**
   * EMATableConfigurator: Производный класс конструктора конфигурации таблиц экспоненциальной скользящей средней
   */
  class EMATableConfigurator extends TableConfigurator {
     /**
      * Статическая функция создания массива конфигурации таблицы
      *
      * @return array                  Массив конфигурации таблицы
      */
     public function Complete() {
        $output = parent::Complete();
        $output["Definition"]         = "таблицы " . $output["Definition"] . "ой экспоненциальной скользящей средней, тип обмена: ";
        $output["columnName"]         = array("timestamp", "value");
        $output["columnType"]         = array("INT(11)", "DOUBLE");
        $output["sourceArray"]        = $this::ConsructSourceArray(array("RateData"));
        $output["valueArray"]         = array(array("timestamp", "close"));
        return $output;
     }
  }  
?>