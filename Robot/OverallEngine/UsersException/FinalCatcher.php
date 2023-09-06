<?php
  /**
   * FinalCatcher: Пользовательский класс перехватчика ошибок
   */
  class FinalCatcher {
     /**
      * @param  object   $instance      Экземпляр соединения
      */
     static private $instance;
     /**
      * Конструктор
      */
    static public function Start() {
        // перехват критических ошибок
        register_shutdown_function("FinalCatcher::Catcher");

        // создание буфера вывода
        ob_start();
     }
     /**
      * Функция закрытия соединения с базой данных
      *
      * @param resource  $dbc           Идентификатор соединения
      */
     static public function Catcher() {
        $error = error_get_last();
        ProtocolTableComplector::Delete();
        MySQLconnector::UnsetConnect();
        if (isset($error)) {
           //ob_end_clean();
           ob_end_flush()
        } else {
           ob_end_flush();
        }
     }
  }
?>