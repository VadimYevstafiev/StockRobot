<?php
  /**
   * CreateTableQuery: Производный класс функций запросов CREATE TABLE
   */
  class CreateTableQuery extends QueryConstructor {
     /**
      * Функция создания запроса CREATE TABLE  
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      * @param  array    $colnames      Массив имен столбцов таблицы
      * @param  array    $coltypes      Массив типов данных в столбцах таблицы
      *
      * @return object                  Экземпляр запроса
      */
     static public function Create($tablename, $colnames, $coltypes) {
        $instance = new static(self::CreateQuery($tablename, $colnames, $coltypes));
        return $instance;
     }
     /**
      * Функция создания строки запроса CREATE TABLE 
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      * @param  array    $fieids        Массив имен колонок, в которые записывается данные
      * @param  array    $values        Массив значений записываемых данных
      *
      * @return string                  Строка запроса
      */
     protected function CreateQuery($tablename, $colnames, $coltypes) {
        $output = "CREATE TABLE " . $tablename . " (";
        if ((!is_array($colnames)) || (!is_array($coltypes)) || (count($colnames) != count($coltypes))) {
           throw new QueryException("Ошибка при создании строки запроса CREATE TABLE. Некорректные параметры.");
        } else {
           $output .= $colnames[0] . " " . $coltypes[0];
           for ($i = 1; $i < count($colnames); $i++) {
              $output .= ", ";
              $output .= $colnames[$i] . " " . $coltypes[$i];
           }
        }
        $output .= ")";
        return $output;
     }
  }
?>