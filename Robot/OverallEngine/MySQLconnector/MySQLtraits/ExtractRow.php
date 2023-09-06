<?php
  /**
   * ExtractRow: Трейт, определяющий функцию извлечения содержания определенных строк таблицы
   */
  trait ExtractRow {
     /**
      * Функция извлечения содержания определенных строк таблицы
      *
      * @param  string   $tablename     Имя таблицы, из которой извлекается строки
      * @param  string   $condition     Строка с именем условия
      * @param  string   $relate        Логическое отношение
      * @param  string   $value         Строка со значением условия
      */
     public function ExtractRow($tablename, $condition, $relate, $value) {
        try {
           $query = SelectQuery::Create($tablename);
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