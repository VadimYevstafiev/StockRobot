<?php
  /**
   * PropertySERVICETIMESTAMP: Трейт, определяющий свойство $serviceTimestamp комплектаторов таблиц
   */
  trait PropertySERVICETIMESTAMP {
     /**
      * @param double  $serviceTimestamp     Метка времени, с которой нужно извлечь данные при дозаписи в таблицу
      */
     protected $serviceTimestamp;
     /**
      * Функция извлечения строки данных, соответствующих метке времени $serviceTimestamp
      *
      * @param string  $tablename    Имя таблицы
      *
      * @return array                Массив данных, извдеченных из таблиц
      */
     protected function ExtractRow($tablename) {
        $data = $this::ExtractSelectedData ($this->dbc,
                                            $tablename,
                                            $this->tabledata["columnName"], 
                                            $this->serviceTimestamp,
                                            "=");
        for ($i = 0; $i < count($this->tabledata["columnName"]); $i++) {
           $output[$this->tabledata["columnName"][$i]] = $data[0][$i];
        }
        return $output;
     }
  }
?>

