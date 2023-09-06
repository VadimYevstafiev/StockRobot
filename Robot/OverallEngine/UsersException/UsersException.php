<?php
  /**
   * UsersException: Пользовательский класс исключений 
   */
  class UsersException extends Exception {
     /**
      * Конструктор
      *
      * @param  string   $definition    Строка объявления имени исключения
      * @param  string   $message       Текст исключения
      * @param  object   $instance      Экземпляр предыдущего исключения
      *
      * @return object                  Экземпляр исключения
      */
     public function __construct ($definition, $message, $previous) {
        $message = "<p>Ошибка класса " .  $definition . ":</p><p>" . $message . "</p>";
        if (!empty($previous)) {
           $message .= "<p>=></p>" . $previous->getMessage() . "</p>";
        }
        throw new Exception($message);
     }
  }
?>