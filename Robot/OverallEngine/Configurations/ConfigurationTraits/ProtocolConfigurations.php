<?php
  /**
   * ProtocolConfigurations: Трейт, определяющий функции установки конфигурации протоколов
   */
  trait ProtocolConfigurations {
     /**
      * @param array $protocol     Массив конфигурации протоколов
      */
     private $protocol =  array( 
                                 "columnName"  => array("timestamp", "Protocol", "Status"),
                                 "columnType"  => array("INT(11)", "TEXT", "TINYINT"),
                               );
     /**
      * Функция определения конфигурации протокола
      *
      * @param string  $flag  Индикатор типа протокола
      *                       "listener"  - протокол слушателя
      *                       "expert"    - протокол эксперта
      *                       "trader"    - протокол торговца
      *                       "interface" - протокол комплектатора интерфейса
      *
      * @return array         Массив конфигурации протокола
      */
     static public function GetProtocolConfiguration($flag) {
        self::SetConfigurations();
        $output = self::$instance->protocol;
        $output["Tablename"] = self::CreateProtocolTablename($flag, self::$instance->indexis["direction"]);
        if (($flag == "expert") || ($flag == "interface")) {
           $output["columnType"][1] = "MEDIUMTEXT";
        }
        $output["gorizont"] = 10;
        return $output;
     }
     /**
      * Функция определения имени таблицы протокола
      *
      * @return string        Имя таблицы протокола
      */
     private function CreateProtocolTablename($flag, $index) {
        $output = MARKETID . $flag . "Protocol" . $index;
        return $output;
     }
   }
?>