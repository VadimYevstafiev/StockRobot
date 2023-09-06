<?php
  /**
   * SummaryConvertsTableComplector: Производный класс комплектатора  сводной таблицы конвертов по типам обмена
   */
  class SummaryConvertsTableComplector extends SummaryTableComplector {
     /**
      * Служебная функция определения начальной метки времени, с которой надо дописать данные
      *
      * @param array   $input         Массив результатов валидации
      *
      * @return double                Начальная метка времени, с которой надо удалить данные и дописать новые
      */
     protected function ServiceTimefactor($input) {
        if (!$this->new) {
           $output = $this::FindStartPoint($input[1], 
                                           $this::DeterminateTablename($this->tabledata["keyType"][0]), 
                                           $this->tabledata["keyValue"][0]);
        } else {
           $output = $input[1];
        }
        return $output;
     }
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
        $output[0] = $this::ExtractSelectedData ($this->dbc,
                                                 $this::DeterminateTablename($sourceArray[0]),
                                                 $valueArray[0], 
                                                 $timefactor); 
        for ($i = 1; $i < count($sourceArray); $i++) {
           for ($j = 0; $j < count($this->tabledata["indexis"]); $j++) {
              $output[$i][$j] = $this::ExtractSelectedData ($this->dbc,
                                                            $this::DeterminateTablename($sourceArray[$i], $this->tabledata["indexis"][$j]),
                                                            $valueArray[$i], 
                                                            $timefactor);        
           }  
        }
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
        for ($i = 0; $i < count($input[0]); $i++) { 
           $output[$i][0] = $input[0][$i][0];         //"SummarySignals" => "timestamp"
           if ($input[0][$i][1] > 0) {                //"SummarySignals" => "Summary" > 0
              $output[$i][1] = "null";
              $output[$i][2] = "null";
              $output[$i][3] = $input[1][1][$i][0];   //"impSAR" => "Ask" => "SAR" 
           } else if ($input[0][$i][1] < 0) {         //"SummarySignals" => "Summary" < 0
              $output[$i][1] = "null";
              $output[$i][2] = "null";
              $output[$i][3] = $input[1][0][$i][0];   //"impSAR" => "Bid" => "SAR"
           } else {                                   //"SummarySignals" => "Summary" = 0
              $output[$i][1] = $input[2][0][$i][1];   //"Convert" => "Bid" => "high"
              $output[$i][2] = $input[2][1][$i][0];   //"Convert" => "Ask" => "low"
              $output[$i][3] = "null";
           } 
        }
        return $output;
     }
  }
?>