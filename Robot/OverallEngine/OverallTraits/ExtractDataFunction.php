<?php
  /**
   * ExtractDataFunction: Трейт, определяющий служебную функцию извлечения данных из базы
   */
  trait ExtractDataFunction {
     /**
      * Функция извлечения данных из базы
      *
      * @param resource  $dbc           Идентификатор соединения
      * @param string    $tablename     Имя таблицы
      * @param array     $columnArray   Массив имен столбцов таблиц, из которых нужно извлечь данные
      * @param double    $timefactor    Начальная метка времени, с которой нужно извлечь данные
      * @param string    $relate        Логическое отношение
      * @param bool      $inverse       Индикатор порядка отображения данных (FALSE - прямой, TRUE - обратный)
      *
       @return array                    Массив данных, извлеченных из таблицы
      */
     protected function ExtractSelectedData ($dbc, $tablename, $columnArray, $timefactor, $relate = ">=", $inverse = FALSE) {
        $query = SelectQuery::Create($tablename, $columnArray); 
        $query->AddWHERE("timestamp", $relate, $timefactor); 
        $query->AddORDER("timestamp", $inverse); 
        $output = $dbc->SendQuery($query);
        return $output;
     }
  }
?>