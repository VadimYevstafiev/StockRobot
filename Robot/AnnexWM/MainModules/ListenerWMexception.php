<?php
  /**
   * ListenerWMexception: Пользовательский класс исключений класса функций слушателя 
   */
  class ListenerWMexception extends UsersException {
     /**
      * Конструктор
      *
      * @param  string   $message       Текст исключения
      * @param  object   $instance      Экземпляр предыдущего исключения
      *
      * @return object                  Экземпляр исключения
      */
     public function __construct ($message, $previous) {
        parent::__construct ("функций слушателя", $message, $previous);
     }
  }
?>