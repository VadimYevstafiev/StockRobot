<?php
  /**
   * ConsructTypeFunction: Трейт, определяющий функцию установки/определения имени таблицы
   */
  trait ConsructTypeFunction {
     /**
      * Функция установки/определения имени таблицы
      *
      * @param array   $parenttype     Тип данных в исходном массиве конфигурации
      * @param integer $timeframe      Таймфрейм
      * @param integer $timemodule     Величина единицы таймфрейма в секундах
      * @param string  $direction      Направление обмена
      *
      * @return string                 Имя таблицы
      */
     protected function ConsructType ($parenttype, $timeframe, $timemodule, $direction) {
        $output    = MARKETID;
        if ($parenttype == "BaseData") {
           $output .= "BaseData";
        } else {
           $output .= $this::DetectTimeframe ($timeframe, $timemodule);
           $output .= $parenttype;
        }
        $output    .= $direction;
        return $output;
     }
     /**
      * Вспомогательная функция представления таймфрейма в имени таблицы
      *
      * @param integer $timeframe      Таймфрейм
      * @param integer $timemodule     Величина единицы таймфрейма в секундах
      *
      * @return string                 Представление таймфрейма в имени таблицы
      */
     protected function DetectTimeframe ($timeframe, $timemodule) {
        switch ($timemodule) {
           case 1:
              $output    = $timeframe . "second";
              break;
           case 60:
              $output    = $timeframe . "minute";
              break;
           case 3600:
              $output    = $timeframe . "hour";
              break;
           case 86400:
              if ($timeframe != 1) {
                 $output = $timeframe;
              }
                 $output = "day";
              break;
        }
        return $output;
     }
  }
?>