<?php
  /**
   * DerivedDataTableComplector: Базовый класс комплектатора таблиц производных данных
   */
  class DerivedDataTableComplector extends DataTableComplector {
     use ConsructTypeFunction, ConsructDefinitionFunction, ExtractDataFunction;
     /**
      * @param  double   $timefactor    Массив начальных меток времени, с которой надо дописать данные,
      *                                 по типам обмена
      */
     protected $timefactor;
     /**
      * Общая функция комплектации таблицы
      *
      * @return array                   Массив начальных меток времени, с которой надо дописать данные,
      *                                 по типам обмена
      */
     public function Complete() {
        parent::Complete();
        return $this->timefactor;
     }
     /**
      * Функция определения начальной метки времени, с которой надо дописать данные
      *
      * @param  array    $input         Массив результатов валидации
      *
      * @return double                  Начальная метка времени, с которой надо дописать данные
      */
     protected function DefineTimefactor($input) {
        $output = $input[1];
        return $output;
     }
     /**
      * Функция обработки данных
      *
      * @param  integer  $input[0]      Код результата поствалидации
      * @param  double   $input[1]      Начальная метка времени, с которой надо дописать данные
      * @param  double   $input[2]      Конечная метка времени, до которой надо дописать данные
      * @param  string   $tablename     Имя таблицы
      * @param  integer  $index         Тип обмена
      *
      * @return array                   Массив значений для записи в таблицу
      */
     protected function PrepaireData($input, $tablename, $index) {
        $this->timefactor[$index] = $input[1];
        $result = $this::ExtractData($this::DefineTimefactor($input), 
                                     $this->tabledata["sourceArray"], 
                                     $this->tabledata["valueArray"], 
                                     $index);
        $output = $this::СalculateData($result, $tablename);
        return $output;
     }
     /**
      * Функция извлечения данных
      *
      * @param  double   $timefactor    Начальная метка времени, с которой нужно извлечь данные
      * @param  array    $sourceArray   Массив имен таблиц, из которых нужно извлечь данные
      * @param  array    $valueArray    Массив имен столбцов таблиц, из которых нужно извлечь данные
      * @param  integer  $index         Тип обмена
      *
      * @return array                   Массив данных, извдеченных из таблиц
      */
     protected function ExtractData($timefactor, $sourceArray, $valueArray, $index) { 
        for ($i = 0; $i < count($sourceArray); $i++) {
           $output[$i] = $this::ExtractSelectedData ($this->dbc,
                                                     $this::DeterminateTablename($sourceArray[$i], $index),
                                                     $valueArray[$i], 
                                                     $timefactor);           
        }
        return $output;
     }
     /**
      * Вспомогательная функция обработки данных
      *
      * @param  array    $input         Массив извлеченных данных
      * @param  string   $tablename     Имя таблицы
      *
      * @return array                   Массив значений для записи в таблицу
      */
     protected function СalculateData($input, $tablename) { 
     }
  }
?>