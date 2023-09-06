<?php
  /**
   * SendDataFunctions: Базовый класс функций отправки данных интерфейсам
   */
  class SendDataFunctions {
     /**
      * @param string  $id            Идентификатор рынка 
      * @param string  $para          Идентификатор торговой пары
      * @param integer $data          Тип данных
      */
     protected $id;
     protected $para;
     protected $data;
     /**
      * Функция отправки данных интерфейсам
      */
     static public function SendData() {
        $instance = new static();
        $instance->IncludeModules();
        $instance->GetData();
        $connect = new InterSQLconnector(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $content = $instance->GetContent($connect);
        unset($connect);
        header("Content-type: application/json; charset=utf-8");
        header("Access-Control-Allow-Origin: http://www.kvartquest.info");
        print $content;
     }

     protected function __construct () {

     }
     /**
      * Функция загрузки модулей
      */
     protected function IncludeModules() {
        require_once("PHP/SetConstants.php");
        include "PHP/InterSQLconnector.php";
        date_default_timezone_set("Europe/Moscow");
     }
     /**
      * Функция получения исходных данных
      */
     protected function GetData() {
        switch ($_GET["id"]) {
           case "bf":
              $this->id = "BF";
              switch ($_GET["para"]) {
                 case "btcusd":
                    $this->para = "BtcUsd";
                    break;
                 case "ltcusd":
                    $this->para = "LtcUsd";
                    break;
              };   
              break;
           case "wm":
              $this->id = "WM";
              switch ($_GET["para"]) {
                 case "WmzWmr":
                    $this->para = "WmzWmr";
                    break;
                 case "WmzWme":
                    $this->para = "WmzWme";
                    break;
                 case "WmeWmr":
                    $this->para = "WmeWmr";
                    break;
                 case "WmzWmx":
                    $this->para = "WmzWmx";
                    break;
                 case "WmrWmx":
                    $this->para = "WmrWmx";
                    break;
              }; 
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

     }
  }
?>
