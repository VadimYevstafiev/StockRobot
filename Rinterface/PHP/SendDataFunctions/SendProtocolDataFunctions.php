<?php
  /**
   * SendProtocolDataFunctions: Производный класс функций отправки данных интерфейсу протоколов
   */
  class SendProtocolDataFunctions extends SendDataFunctions {
     /**
      * @param string  $protype       Тип протокола
      * @param integer $timestamp     Метка времени данных
      */
     protected $protype;
     protected $timestamp;
     /**
      * Функция загрузки модулей
      */
     protected function IncludeModules() {
        parent::IncludeModules();
        require_once("PHP/DataFunctions/IncludeProtocolData.php");
     }
     /**
      * Функция получения исходных данных
      */
     protected function GetData() {
        parent::GetData();
        switch ($_GET["data"]) {
           case 0:
              $this->data = 0;
              break;
           case 1:
              $this->data = 1;
              break;
           case 2:
              $this->data = 2;
              break;
        }
        switch ($_GET["protype"]) {
           case 0:
              $this->protype = "listener";
              break;
           case 1:
              $this->protype = "expert";
              break;
           case 2:
              $this->protype = "trader";
              break;
           case 3:
              $this->protype = "interface";
              break;
        }
        if ($this->data != 0) {
           $this->timestamp = $_GET["time"];
           $this->timestamp = strip_tags($this->timestamp);
           $this->timestamp = htmlspecialchars($this->timestamp);
        }
     }
     /**
      * Функция получения исходных данных
      *
      * @param array   $connect     Идентификатор соединения с базой данных 
      *
      * @return string              Строка JSON с данными
      * 
      */
     protected function GetContent($connect) {
        $tablename = $this->id . $this->protype . "Protocol" . $this->para;
        switch ($this->data) {
           case 0:
              $output = ProtocolData::boxJSON($connect, $tablename);
              break;
           case 1:
              $output = ProtocolData::tableJSON($connect, $tablename, $this->timestamp);
              break;
           case 2:
              $output = ProtocolData::dataJSON($connect, $tablename, $this->timestamp);
              $output = ProtocolData::ParseData($output, $this->protype);
              break;
        }
        return $output;
     }
  }
?>
