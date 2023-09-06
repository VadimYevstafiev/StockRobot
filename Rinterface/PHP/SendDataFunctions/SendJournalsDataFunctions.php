<?php
  /**
   * SendJournalsDataFunctions: Производный класс функций отправки данных интерфейсу журналов
   */
  class SendJournalsDataFunctions extends SendDataFunctions {
     /**
      * @param string  $protype       Тип журнала
      * @param string  $type          Тип данных
      * @param integer $number        Номер заявки
      */
     protected $protype;
     protected $number;
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
              $this->protype = "trade";
              break;
           case 1:
              $this->protype = "bids";
              break;
        }
        switch ($this->data) {
           case 1:
              $this->type = $_GET["type"];
              $this->type = strip_tags($this->type);
              $this->type = htmlspecialchars($this->type);
           case 2:
              $this->number = $_GET["num"];
              $this->number = strip_tags($this->number);
              $this->number = htmlspecialchars($this->number);
              break;
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
        include "PHP/DataFunctions/Include" . ucfirst($this->protype) . "JournalsData.php";
        $tablename = $this->id . $this->protype . "Journal" . $this->para;
        $classname = ucfirst($this->protype) . "JournalsData";
        switch ($this->data) {
           case 0:
              $output = $classname::boxJSON();
              break;
           case 1:
              $output = $classname::tableJSON($connect, $tablename, $this->type);
              break;
           case 2:
              $output = $classname::legendJSON($connect, $tablename, $this->number);
              break;
        }
        return $output;
     }
  }
?>
