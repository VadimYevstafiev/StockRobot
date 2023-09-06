<?php
  /**
   * CorrectRow: Трейт, определяющий функцию изменения новой строки или ячейки в таблице
   */
  trait CorrectRow {
     /**
      * Функция изменения новой строки или ячейки в таблице
      *
      * @param  string   $tablename     Имя таблицы, в которой изменяется строка
      * @param  array    $colnames      Массив имен столбцов таблицы, данные которых изменяются 
      * @param  array    $colvalues     Массив значений, которые вносятся
      * @param  string   $condition     Строка с именем условия
      * @param  string   $relate        Логическое отношение
      * @param  string   $value         Строка со значением условия
      */
     public function CorrectRow($tablename, $colnames, $colvalues, $condition, $relate, $value) {
        try {
           $query = UpdateQuery::Create($tablename);
           $query->AddSET($colnames, $colvalues);
           $query->AddWHERE($condition, $relate, $value);
        } catch (Exception $e) {
           throw new MySQLexception("Не удалось создать конструктор запроса.", $e);
        }
        $output = $this->SendQuery($query);
        unset($query);  
        return $output;
     }
  }
?>