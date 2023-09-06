<?php
  /**
   * DeleteQuery: Производный класс функций запросов DELETE
   */
  class DeleteQuery extends QueryConstructor {
     use AddAND, AddLIMIT, AddORDER, AddWHERE;
     /**
      * Функция создания запроса DELETE  
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
      * Функция создания строки запроса DELETE 
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      *
      * @return string                  Строка запроса
      */
     protected function CreateQuery($tablename) {
        $output = "DELETE FROM " . $tablename;
        return $output;
     }
  }
?>