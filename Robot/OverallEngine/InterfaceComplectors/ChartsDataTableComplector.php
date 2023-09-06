<?php
  /**
   * ChartsDataTableComplector: Производный класс комплектатора таблиц данных графиков
   */
  class ChartsDataTableComplector extends BaseSummaryTableComplector {
     /**
      * @param  resource $dbcread       Идентификатор соединения с базой данных - источником данных
      * @param  array    $refrashtime   Массив начальных меток времени, с которых нужно извлечь данные
      */
     private $dbcread;
     private $refrashtime;
     /**
      * @param  resource $dbc           Идентификатор соединения с базой данных интерфейсов
      * @param  resource $dbcread       Идентификатор соединения с базой данных - источником данных
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $tabledata     Массив конфигурации таблицы
      * @param  array    $datetimes     Массив дат и времени
      * @param  array    $refrashtime   Массив начальных меток времени, с которых нужно извлечь данные
      */
     public function __construct ($dbcread, $dbc, $protocol, $tabledata, $datetimes, $refrashtime) {
        $this->dbcread      = $dbcread;
        $this->refrashtime  = $refrashtime;
        parent::__construct($dbc, $protocol, $tabledata, $datetimes);
     }
     /**
      * Общая функция комплектации таблицы
      *
      * @param  string   $flag          Ключ подмассива массива структуры графиков ("Simple" или "Summary")
      *
      * @return bool                    Результат комплектации таблицы
      */
     public function Complete($flag) {
        switch ($flag) {
           case "Simple":
             parent::Complete();
             break;
           case "Summary":
             $this::ServiceComplete();
             break;
        }
     }
     /**
      * Служебная функция определения начальной метки времени, с которой надо дописать данные
      *
      * @param  array    $input         Массив результатов валидации
      * @param  integer  $index         Тип обмена
      *
      * @return double                Начальная метка времени, с которой надо удалить данные и дописать новые
      */
     protected function ServiceTimefactor($input, $index) {
        if (!$this->new) {
           if (is_array($this->refrashtime)) {
              $output = $this->refrashtime[$index];
           } else {
              $output = $this->refrashtime;
           }
        } else {
           $output = $input[1];
        }

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
           $output[$i] = $this::ExtractSelectedData ($this->dbcread,
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
        $counter = 0;
        for ($i = 0; $i < count($input); $i++) {
           for ($j = 0; $j < count($input[$i]); $j++) { 
              for ($z = 0; $z < count($input[$i][$j]); $z++) {            
                 $output[$j][$counter + $z] = $input[$i][$j][$z];
              }
           }
           $counter += $z;
        }
        for ($i = 0; $i < count($output); $i++) {
           if (!isset($output[$i][0])) {
              $output[$i][0] = $output[$i - 1][0] + $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
           }
           for ($j = 1; $j < count($output[$i]); $j++) {
              if (!isset($output[$i][$j])) {
                 $output[$i][$j] = "null";
              }
           }
        }
        return $output;
     }
  }
?>