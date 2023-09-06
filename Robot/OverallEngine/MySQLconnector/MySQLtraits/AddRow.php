<?php
  /**
   * AddRow: Трейт, определяющий функцию добавления новой строки или ячейки в таблицу
   */
  trait AddRow {
     /**
      * Функция добавления новой строки или ячейки в таблицу
      *
      * @param string  $tablename       Имя таблицы, в которую добавляется строка
      * @param array   $colnames        Массив имен столбцов таблицы, в которые вносятся данные
      * @param array   $colvalues       Массив значений, которые вносятся
      */
     public function AddRow($tablename, $colnames, $colvalues) {
        try {
           $query = InsertQuery::Create($tablename, $colnames, $colvalues);
 
        } catch (Exception $e) {
           throw new MySQLexception("Не удалось создать конструктор запроса.", $e);
        }
        $output = $this->SendQuery($query);
        unset($query); 
        return $output;
     }
  }
?>