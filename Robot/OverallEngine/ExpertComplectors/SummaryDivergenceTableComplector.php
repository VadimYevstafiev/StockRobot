<?php
  /**
   * SummaryDivergenceTableComplector: Производный класс комплектатора таблицы расхождений сводных данных по типам обмена
   */
  class SummaryDivergenceTableComplector extends SummaryDataTableComplector {
     /**
      * Функция извлечения данных
      *
      * @param double  $timefactor   Начальная метка времени, с которой нужно извлечь данные
      * @param array   $sourceArray  Массив имен таблиц, из которых нужно извлечь данные
      * @param array   $valueArray   Массив имен столбцов таблиц, из которых нужно извлечь данные
      *
      * @return array                Массив значений для записи в таблицу
      */
     protected function ExtractData($timefactor, $sourceArray, $valueArray) { 
        $output = $this::ExtractSelectedData ($this->dbc,
                                              $this::DeterminateTablename($sourceArray[0]),
                                              $valueArray[0], 
                                              $timefactor); 
        return $output;
     }
     /**
      * Вспомогательная функция обработки данных
      *
      * @param array   $input      Массив извлеченных данных
      * @param string  $tablename  Имя таблицы
      *
      * @return array              Массив значений для записи в таблицу
      */
     protected function СalculateData($input, $tablename) { 
        for ($i = 0; $i < count($input); $i++) {
           $output[$i][0] = $input[$i][0];
           $output[$i][1] = round((100 * ($input[$i][1] - $input[$i][2]) / $input[$i][1]), 2);
           $output[$i][2] = round((100 * ($input[$i][3] - $input[$i][4]) / $input[$i][3]), 2);
           $output[$i][3] = round((100 * ($input[$i][5] - $input[$i][6]) / $input[$i][5]), 2);
           $output[$i][4] = round((100 * ($input[$i][7] - $input[$i][8]) / $input[$i][7]), 2);
           $output[$i][5] = $output[$i][1] + $output[$i][2] + $output[$i][3] + $output[$i][4];
        }
        return $output;
     }
  }
?>