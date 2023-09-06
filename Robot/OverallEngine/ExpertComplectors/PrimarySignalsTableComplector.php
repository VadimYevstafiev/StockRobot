<?php
  //Производный класс комплектатора таблиц предварительных индикаторов 
  /**
   * PrimarySignalsTableComplector: Производный класс комплектатора таблиц предварительных индикаторов
   */
  class PrimarySignalsTableComplector extends BaseSummaryTableComplector {
     /**
      * Функция определения начальной метки времени, с которой надо дописать данные
      *
      * @param array $input         Массив результатов валидации
      *
      * @return double              Начальная метка времени, с которой надо дописать данные
      */
     protected function DefineTimefactor($input) {
        $output = $input[1];
        if (!$this->new) {
           $output += - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
        }
        return $output;
     }
     /**
      * Вспомогательная функция обработки данных
      *
      * @param array   $result     Массив извлеченных данных
      * @param string  $tablename  Имя таблицы
      *
      * @return array              Массив значений для записи в таблицу
      */
     protected function СalculateData($result, $tablename) {
        if ($this->new) {
           $j = 0;
           $result[1][-1][0] = $result[1][0][0];
        } else {
           $j = 1;
        }
        for ($i = $j; $i < count($result[0]); $i++) {
           $counter = $i - $j;
           $output[$counter][0] = $result[0][$i][0];       //Позиция "timestamp" таблицы "impSAR"
           if ($result[0][$i][1] == 1) {                   //Позиция "signalvalue" таблицы "impSAR" 
              if ($result[0][$i][2] > $result[2][$i][0]) { //Сравнение позиции "SAR" таблицы "impSAR" и позиции "middle" таблицы "Convert"
                 $output[$counter][1] = 2;    
              } else {
                 $output[$counter][1] = 1; 
              } 
           } else {
              if ($result[0][$i][2] < $result[2][$i][0]) { //Сравнение позиции "SAR" таблицы "impSAR" и позиции "middle" таблицы "Convert"
                 $output[$counter][1] = - 2;    
              } else {
                 $output[$counter][1] = - 1; 
              }
           } 
           $output[$counter][2] = $result[1][$i - 1][0];   //Позиция "integralFactor" таблицы "ADX"
           if (abs($output[$counter][2]) == 2) {           //Если "integralFactor" таблицы "ADX" == 2
              $output[$counter][3] = $output[$counter][1];
           } else {
              $output[$counter][3] = 0;
           }
        }
        return $output;
     }
  }
?>