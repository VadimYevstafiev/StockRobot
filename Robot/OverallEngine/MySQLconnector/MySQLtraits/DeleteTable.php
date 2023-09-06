<?php
  /**
   * DeleteTable: Трейт, определяющий функцию удаления таблицы
   */
  trait DeleteTable {
     /**
      * Функция удаления таблицы
      *
      * @param string    $tablename   Имя удаляемой таблицы
      */
     public function DeleteTable($tablename) {
        try {
           $query = DropQuery::Create($tablename);
        } catch (Exception $e) {
           throw new MySQLexception("Не удалось создать конструктор запроса.", $e);
        }
        $output = $this->SendQuery($query);
        unset($query);  
        return $output;
     }
  }
?>