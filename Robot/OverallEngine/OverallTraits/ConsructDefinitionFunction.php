<?php
  /**
   * ConsructDefinitionFunction: Трейт, определяющий служебную функцию установки стартового объявления процедуры
   */
  trait ConsructDefinitionFunction {
     /**
      * служебная функция установки стартового объявления процедуры
      *
      * @param integer $timeframe      Таймфрейм
      * @param integer $timemodule     Величина единицы таймфрейма в секундах
      *
      * @return string                 Часть стартового объявления процедуры
      */
     protected function ConsructDefinition ($timeframe, $timemodule) {
        $output = $timeframe . "-";
        switch ($timemodule) {
           case 1:
              $output .= "секундн";
              break;
           case 60:
              $output .= "минутн";
              break;
           case 3600:
              $output .= "часов";
              break;
           case 86400:
              $output .= "дневн";
              break;
        }
        return $output;
     }
  }
?>