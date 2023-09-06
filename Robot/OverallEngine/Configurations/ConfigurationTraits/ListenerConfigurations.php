<?php
  /**
   * ListenerConfigurations: Трейт, определяющий функции установки конфигурации слушателя
   */
  trait ListenerConfigurations {
     /**
      * @param array $listener     Массив конфигурации слушателя
      */
     private $listener =  array( 
                                 "columnName"  => array("timestamp", "BidZero", "BidQuality", "AskZero", "AskQuality"),
                                 "columnType"  => array("INT(11)", "DOUBLE", "DOUBLE", "DOUBLE", "DOUBLE")
                               );
     /**
      * Функция получения конфигурации слушателя
      *
      * @return array        Массив конфигурации слушателя
      */
     public function GetListenerConfiguration() {
        self::SetConfigurations();
        return self::$instance->listener;
     }
     /**
      * Функция определения конфигурации слушателя
      * @param integer $data           Критерий отбора "квалифицированного курса"
      *
      * @return array                  Массив конфигурации слушателя
      */
     private function CreateListenerConfiguration($data) {
        $this->listener["Tablename"]  = MARKETID . "table" . $this->indexis["direction"];
        $this->listener["quality"]    = $data["quality"];
        $startdate                    = $this->datetimes["currentdate"]->getTimestamp() 
                                        - ($this::ConvertTimemodule($data["gorizont"])
                                        * $this::ConvertTimeframe($data["gorizont"]));
        $this->listener["startdate"]  = (new DateTime)->setTimestamp($startdate);
     }
  }
?>