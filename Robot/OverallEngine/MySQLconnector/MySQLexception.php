<?php
  /**
   * MySQLexception: Пользовательский класс исключений класса функций соединения и запросов к базе данных
   */
  class MySQLexception extends UsersException {
     /**
      * Конструктор
      *
      * @param  string   $message       Текст исключения
      * @param  object   $instance      Экземпляр предыдущего исключения
      *
      * @return object                  Экземпляр исключения
      */
     public function __construct ($message, $previous) {
        parent::__construct ("функций соединения и запросов к базе данных", $message, $previous);
     }
  }
?>