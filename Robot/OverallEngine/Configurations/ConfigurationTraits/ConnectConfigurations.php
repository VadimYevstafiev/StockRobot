<?php
  /**
   * ConnectConfigurations: Трейт, определяющий функции установки конфигурации соединения с базой данных
   */
  trait ConnectConfigurations {
     /**
      * @param array $connect     Массив конфигурации соединения с базой данных
      */
     private $connect =  array( 
                                "listener"  => array(
                                                      "host"     => LIST_HOST, 
                                                      "user"     => LIST_USER, 
                                                      "password" => LIST_PASSWORD, 
                                                      "name"     => LIST_NAME
                                                    ),
                                "work"      => array(
                                                      "host"     => DB_HOST, 
                                                      "user"     => DB_USER, 
                                                      "password" => DB_PASSWORD, 
                                                      "name"     => DB_NAME
                                                    ),
                                "interface" => array(
                                                      "host"     => RINT_HOST, 
                                                      "user"     => RINT_USER, 
                                                      "password" => RINT_PASSWORD, 
                                                      "name"     => RINT_NAME
                                                    )
                              );
     /**
      * Функция получения массива конфигурации соединения с базой данных
      *
      * @return array        Массив конфигурации соединения с базой данных
      */
     static public function GetConnectConfiguration() {
        self::SetConfigurations();
        return self::$instance->connect;
     }
  }
?>