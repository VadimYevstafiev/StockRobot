<?php
  /**
   * SendChartsDataFunctions: Производный класс функций отправки данных интерфейсу графиков
   */
  class SendChartsDataFunctions extends SendDataFunctions {
     /**
      * @param string  $timeframe      Таймфрейм графика
      * @param integer $type           Тип графика (Сводный, Bid или Ask)
      * @param integer $position       Период, отображаемый на графике
      */
     protected $timeframe;
     protected $type;
     protected $position;
     /**
      * Функция загрузки модулей
      */
     protected function IncludeModules() {
        parent::IncludeModules();
        include "PHP/DataFunctions/ChartsData.php";
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
        }
        switch ($_GET["time"]) {
           case "01H":
              $this->timeframe = "01H";
              break;
           case "06H":
              $this->timeframe = "06H";
              break;
        }
        switch ($_GET["type"]) {
           case "0":
              $this->type = "0";
              break;
           case "1":
              $this->type = "1";
              break;
           case "2":
              $this->type = "2";
              break;
        }
        switch ($_GET["period"]) {
           case 0:
              $this->position = 60;
              break;
           case 1:
              $this->position = 120;
              break;
           case 2:
              $this->position = 240;
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
        switch ($this->data) {
           case 0:
              $output = ChartsData::boxJSON($connect, 
                                            $this->id, 
                                            $this->para, 
                                            $this->type, 
                                            $this->timeframe);
              break;
           case 1:
              $output = ChartsData::dataJSON($connect, 
                                             $this->id, 
                                             $this->para, 
                                             $this->type, 
                                             $this->timeframe, 
                                             $this->position);
              break;
        }
        return $output;
     }
  }
?>
