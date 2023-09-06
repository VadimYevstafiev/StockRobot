<?php
  /**
   * CreateTable: Трейт, определяющий функцию создания таблицы
   */
  trait CreateTable {
     /**
      * Функция создания таблицы
      *
      * @param  string   $tablename   Имя создаваемой таблицы
      * @param  array    $colmames    Массив имен столбцов таблицы
      * @param  array    $colmames    Массив типов данных в столбцах таблицы
      */
     public function CreateTable($tablename, $colnames, $coltypes) {
        try {
           $query = CreateTableQuery::Create($tablename, $colnames, $coltypes);
        } catch (Exception $e) {
           throw new MySQLexception("Не удалось создать конструктор запроса.", $e);
        }
        $output = $this->SendQuery($query);
        unset($query);  
        return $output;
     }
  }
?>