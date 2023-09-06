
<?php

  /**
   * WMmyBidList: Класс вызова интерфейса для автоматического получения 
   * информации о новых заявках конкретного WMID
   */  
  class WMmyBidList extends WMsecureInterface {
     private $type;
     private $queryid;
     private $pursetype_id;
     private $capitallerwmid;

     /**
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $type            Тип запроса 
      * @param string  $queryid         Номер (id) новой заявки, информацию по которой необходимо вернуть (необязательный)
      * @param integer $pursetype_id    Тип кошелька (необязательный)
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $type, $queryid, $pursetype_id, $capitallerwmid) {
        $this->url               = "https://wm.exchanger.ru/asp/XMLWMList2.asp";
        if ($type) {
           $this->type           = $type;
        }
        if ($queryid) {
           $this->queryid        = $queryid;
        }
        parent::__construct ($wmid, $signer);
        if ($pursetype_id) {
           $this->pursetype_id   = $pursetype_id;
        }
        if ($capitallerwmid) {
           $this->capitallerwmid = $capitallerwmid;
        }
     }
     /**
      * Функция отправки запроса и обработки ответа 
      *
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $type            Тип запроса 
      * @param string  $queryid         Номер (id) новой заявки, информацию по которой необходимо вернуть (необязательный)
      * @param integer $pursetype_id    Тип кошелька (необязательный)
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($wmid, $signer, $type, $queryid, $pursetype_id, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $type, $queryid, $pursetype_id, $capitallerwmid);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор цифровой подписи
      *
      * @return string                  Цифровая подпись
      */
     protected function ConstructSignature() {
        $data = $this->wmid . $this->type; 
        if ($this->queryid) {
           $data = $data . $this->queryid;
        }
        $output = $this->signer->sign($data);
        return $output;
     }
     /**
      * Конструктор массива данных для передачи в запросе
      *
      * @return array            Массив данных для передачи в запросе
      */
     protected function ConstructDataArray() {
        $output = parent::ConstructDataArray();
        if ($this->type) {
           $output["type"] = $this->type;
        }
        if ($this->queryid) {
           $output["queryid"] = $this->queryid;
        }
        if ($this->pursetype_id) {
           $output["pursetype_id"] = $this->pursetype_id;
        }
        if ($this->capitallerwmid) {
           $output["capitallerwmid"] = $this->capitallerwmid;
        }
        return $output;
     }
     /**
      * Конструктор массива для передачи полученных данных в случае успешного выполнения запроса
      *
      * @param  array  $output   Массив для передачи полученных данных
      * @param  object $response Объект SimpleXMLElement
      *
      * @return array            Массив для передачи полученных данных
      */
     protected function DescribeResults($output, $response) {
        $output["wmid"]    = (string) $response->WMExchnagerQuerys["wmid"];    //WMID, выставивший заявки
        $output["type"]    = (string) $response->WMExchnagerQuerys["type"];    //тип запроса
        $output["queries"] =  array();
        foreach ($response->WMExchnagerQuerys->query as $row) {
           $output["queries"][] = array(
                  "id"            => (string) $row["id"],                      //номер новой выставленной на обмен заявки
                  "exchtype"      => (string) $row["exchtype"],                //направление обмена
                  "state"         => (string) $row["state"],                   //текущее состояние данной заявки
                  "amountin"      => (string) $row["amountin"],                //сумма WM, которую осталось обменять в данной заявке
                  "amountout"     => (string) $row["amountout"],               //сумма WM, которую осталось получить в данной заявке
                  "inoutrate"     => (string) $row["inoutrate"],               //прямой курс выставленной заявки 
                  "outinrate"     => (string) $row["outinrate"],               //обратный курс выставленной заявки 
                  "initamountin"  => (string) $row["initamountin"],            //первоначальная сумма WM, с которой была создана данная заявка 
                  "inpurse"       => (string) $row["inpurse"],                 //кошелек с которого была произведена оплата заявки
                  "outpurse"      => (string) $row["outpurse"],                //кошелек на который переводятся обменянные средства
                  "querydatecr"   => (string) $row["querydatecr"],             //дата постановки заявки 
                  "querydate"     => (string) $row["querydate"],               //дата последнего изменения в заявке
                  "direction"     => (string) $row["direction"],               //направление обмена в заявке
                  "exchamountin"  => (string) $row["exchamountin"],            //сумма WM, выставленная на обмен в данной заявке
                  "exchamountout" => (string) $row["exchamountout"]);          //сумма WM, полученная в результате обмена в данной заявке
        }
        return $output;
     }
  }
?>

