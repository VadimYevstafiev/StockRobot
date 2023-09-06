
<?php

  /**
   * ResultsTableComplector: Производный класс комплектатора рабочих таблиц таблицы рекомендаций
   */
  class ResultsTableComplector extends SummaryConvertsTableComplector {
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
        for ($i = 0; $i < count($sourceArray); $i++) { 
           $output[$i] = $this::ExtractSelectedData ($this->dbc,
                                                     $this::DeterminateTablename($sourceArray[$i]),
                                                     $valueArray[$i], 
                                                     $timefactor); 
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
           $output[$i] = $input[0][$i]; 
           $output[$i][] = $input[1][$i][0];
        }
        return $output;
     }
      /**
      * Функция извлечения данных  
      *
      * @return array                Массив извлеченных значений 
      */
     public function GetResults() {
        $tablename  = $this::DeterminateTablename($this->tabledata["Type"]);
        $query = SelectQuery::Create($tablename); 
        $query->AddORDER("timestamp", TRUE);
        $query->AddLIMIT(1);
        $data = $this->dbc->SendQuery($query);
        for ($i = 0; $i < count($this->tabledata["columnName"]); $i++) {
           $output[$this->tabledata["columnName"][$i]] = $data[0][$i];
        }
        return $output;
     }
  }
?>

