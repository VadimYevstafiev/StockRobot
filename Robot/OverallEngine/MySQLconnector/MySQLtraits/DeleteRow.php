<?php
  /**
   * DeleteRow: Трейт, определяющий функцию определенных строк из таблицы
   */
  trait DeleteRow {
     /**
      * Функция удаления определенных строк из таблицы
      *
      * @param  string   $tablename     Имя таблицы, из которой удаляется строки
      * @param  string   $condition     Строка с именем условия
      * @param  string   $relate        Логическое отношение
      * @param  string   $value         Строка со значением условия
      */
     public function DeleteRow($tablename, $condition, $relate, $value) {
        try {
           $query = DeleteQuery::Create($tablename);
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