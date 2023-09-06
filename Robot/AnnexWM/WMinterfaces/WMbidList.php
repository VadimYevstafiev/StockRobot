
<?php

  /**
   * WMbidList: Производный класс конструктора запросов и обработчика ответов 
   * интерфейса для автоматического получения информации о текущих заявках
   */  
  class WMbidList extends WMinterface {
     private $extype;
     /**
      *
      * @param integer $extype       Тип обмена 
      */
     protected function __construct ($extype) {
        $this->url         = "https://wm.exchanger.ru/asp/XMLWMList.asp?exchtype=";
        $this->extype = $extype;
     }
     /**
      * Функция отправки запроса и обработки ответа 
      *
      * @param integer $extype       Тип обмена 
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($extype) {
        self::$instance = new self($extype);
        $output = parent::SendQuery(self::$instance);
        self::$instance = NULL;
        return $output;
     }
     /**
      * Конструктор запроса
      *
      * @return string              URL для передачи запроса 
      */
     protected function ConstructRequest() {
         $output = $this->url . $this->extype;
         return $output;
     }

     /**
      * Обработчик ответа
      *
      * @param string  $response     Ответ сервиса в формате XML
      *
      * @return array                Массив полученных значений
      */
     protected function ParseReponse($response) {
        $response = parent::ParseReponse($response);
        $output = array();
        foreach ($response->WMExchnagerQuerys->query as $row) {
           $output[] = array(
                  "id"              => (string) $row["id"],              //номер новой выставленной на обмен заявки
                  "amountin"        => (string) $row["amountin"],        //сумма WM, которую осталось обменять в данной заявке
                  "amountout"       => (string) $row["amountout"],       //сумма WM, которую хочет получить после обмена респондент выставивший заявку
                  "inoutrate"       => (string) $row["inoutrate"],       //прямой курс выставленной заявки 
                  "outinrate"       => (string) $row["outinrate"],       //обратный курс выставленной заявки 
                  "procentbankrate" => (string) $row["procentbankrate"], //процент отличия данной заявки от текущего курса ЦБ (НБУ)
                  "allamountin"     => (string) $row["allamountin"],     //сумма WM выставленная на обмен во всех предыдущих и текущей заявке
                  "querydate"       => (string) $row["querydate"],       //дата последнего изменения в заявке
           );
        }
        return $output;
     }

 }
?>

