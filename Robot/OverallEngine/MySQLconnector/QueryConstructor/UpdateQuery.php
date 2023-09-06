<?php
  /**
   * UpdateQuery: Производный класс функций запросов UPDATE
   */
  class UpdateQuery extends QueryConstructor {
     use AddSET, AddLIMIT, AddWHERE;
     /**
      * Функция создания запроса UPDATE  
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
      * Функция создания строки запроса UPDATE 
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      *
      * @return string                  Строка запроса
      */
     protected function CreateQuery($tablename) {
        $output = "UPDATE " . $tablename;
        return $output;
     }
  }
?>