<?php
  /**
   * DeleteAll: Трейт, определяющий функцию удаления всех строк из таблицы
   */
  trait DeleteAll {
     /**
      * Функция удаления всех строк из таблицы
      *
      * @param string    $tablename   Имя таблицы, из которой удаляются строки
      */
     public function DeleteAll($tablename) {
        try {
           $query = DeleteQuery::Create($tablename);
        } catch (Exception $e) {
           throw new MySQLexception("Не удалось создать конструктор запроса.", $e);
        }
        $output = $this->SendQuery($query);
        unset($query);  
        return $output;
     }
  }
?>