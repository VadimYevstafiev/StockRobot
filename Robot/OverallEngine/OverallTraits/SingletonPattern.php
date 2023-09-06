<?php
  /**
   * SingletonPattern: Трейт, определяющий шаблон Singleton
   */
  trait SingletonPattern {
     /**
      * @param  object   $instance      Экземпляр объекта
      */
     static private $instance;
     /**
      * Служебная функция инициализации экземпляра объекта 
      *
      * @param array     $arguments     Массив аргументов конструктора 
      *
      * @return object                  Экземпляр объекта
      */
     static private function ServiceInitialize($arguments) {
        if (empty(self::$instance)) {
           self::$instance = new static($arguments);
        }
        return self::$instance;
     }
     /**
      * Конструктор
      *
      * @param array     $arguments     Массив аргументов конструктора  
      */
     private function __construct ($arguments) {
     }
     /**
      * Функция закрытия экземпляра объекта
      *
      * @param array     $arguments     Массив аргументов 
      */
     static public function Delete($arguments) {
        if (!empty(self::$instance)) {
           self::$instance->ClosingProcedure($arguments);
           self::$instance = NULL;
        }
     }

     /**
      * Служебная функция, выполняемая при закрытии экземпляра объекта
      *
      * @param array     $arguments     Массив аргументов  
      */
     private function ClosingProcedure ($arguments) {
     }
  }
?>