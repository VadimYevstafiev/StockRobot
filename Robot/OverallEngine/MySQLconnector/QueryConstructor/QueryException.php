<?php
  /**
   * QueryException: Пользовательский класс исключений модуля QueryConstructor
   */
  class QueryException extends UsersException {
     /**
      * Конструктор
      *
      * @param  string   $message       Текст исключения
      *
      * @return object                  Экземпляр исключения
      */
     public function __construct ($message) {
        parent::__construct ("конструктора SQL-запросов", $message);
     }
  }
?>