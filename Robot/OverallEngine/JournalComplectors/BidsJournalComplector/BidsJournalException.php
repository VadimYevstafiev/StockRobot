<?php
  /**
   * BidsJournalException: Пользовательский класс исключений класса комплектатора журнала заявок
   */
  class BidsJournalException extends UsersException {
     /**
      * Конструктор
      *
      * @param  string   $message       Текст исключения
      * @param  object   $instance      Экземпляр предыдущего исключения
      *
      * @return object                  Экземпляр исключения
      */
     public function __construct ($message, $previous) {
        parent::__construct ("комплектатора журнала заявок", $message, $previous);
     }
  }
?>