
<?php

  /**
   * WMhistory: Производный класс конструктора запросов и обработчика ответов 
   * интерфейса истории курсов обмена сервиса Exchanger
   */  
  class WMhistory extends WMinterface {
     private $querydate;
     private $extype;

     /**
      * @param object  $querydate   Дата, для которой запрашивается история курсов обмена
      * @param integer $extype      Тип обмена 
      */
     protected function __construct ($querydate, $extype) {
        $this->url       = "https://wm.exchanger.ru/asp/XMLQuerysStats.asp?exchtype=";
        $this->querydate = $querydate;
        $this->extype    = $extype;
     }
     /**
      * Функция отправки запроса и обработки ответа 
      *
      * @param integer $extype       Тип обмена 
      * @param string  $sourceArray  Массив полей данных, значения которых нужно получить
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($querydate, $extype) {
        self::$instance = new self($querydate, $extype);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор запроса
      *
      * @return string              URL для передачи запроса 
      */
     protected function ConstructRequest() {
         $year      = date_format($this->querydate, 'Y');
         $mon       = date_format($this->querydate, 'm');
         $mday      = date_format($this->querydate, 'd');
         $this->url = $this->url . $this->extype . "&grouptype=4&yearstats=" .
                      $year . "&monthstats=" . $mon . "&daystats="  . $mday;
         return $this->url;
     }
     /**
      * Обработчик ответа
      *
      * @param string  $response  Ответ сервиса в формате XML
      *
      * @return array             Массив полученных значений
      */
     protected function ParseReponse($response) {
        $response = parent::ParseReponse($response);
        foreach ($response->row as $row) {
           $output[] = array("timestamp"=>(string) strtotime($row["mindateid"]), "avgrate"=>(string) $row["avgrate"], "sumall"=>(string) $row["sumall"]);
        }
        return $output;
     }
 }
?>

