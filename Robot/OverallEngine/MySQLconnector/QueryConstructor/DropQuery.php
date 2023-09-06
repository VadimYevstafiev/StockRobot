<?php
  /**
   * DropQuery: Производный класс функций запросов DROP
   */
  class DropQuery extends QueryConstructor {
     /**
      * Функция создания запроса DROP 
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      *
      * @return object                  Экземпляр запроса
      */
     static public function Create($tablename) {
        $instance = new static(self::CreateQuery($tablename));
        return $instance;
     }
     /**
      * Функция создания строки запроса DROP 
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      *
      * @return string                  Строка запроса
      */
     protected function CreateQuery($tablename) {
        $output = "DROP TABLE " . $tablename;
        return $output;
     }
  }
?>