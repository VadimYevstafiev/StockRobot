<?php
  /**
   * TimeframeFunctions: Трейт, определяющий функции вычисления таймфреймов
   */
  trait TimeframeFunctions {
     /**
      * Функция получения численного значения таймфрейма
      *
      * @param integer $timeframe      Таймфрейм
      *
      * @return integer                Значение таймфрейма
      */
     protected function ConvertTimeframe ($timeframe) {
        $output = intval(substr($timeframe, 0, -1));
        return $output;
     }
     /**
      * Функция получения значения модуля времени (Величины единицы таймфрейма в секундах)
      *
      * @param integer $timeframe      Таймфрейм
      *
      * @return integer                Величина таймфрейма в секундах
      */
     protected function ConvertTimemodule ($timeframe) {
        switch (substr($timeframe, -1)) {
           case "s":
              $output = 1;
              break;
           case "i":
              $output = 60;
              break;
           case "H":
              $output = 3600;
              break;
           case "d":
              $output = 86400;
              break;
        }
        return $output;
     }
  }
?>