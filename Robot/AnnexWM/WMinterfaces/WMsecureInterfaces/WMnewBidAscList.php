<?php
  /**
   * WMnewBidAscList: Класс вызова интерфейса для автоматического получения 
   * встречных заявок по конкретной новой заявке конкретного WMID
   */  
  class WMnewBidAscList extends WMsecureInterface {
     private $type;
     private $queryid;
     private $capitallerwmid;

     /**
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param string  $queryid         Номер (id) новой заявки, информацию по которой необходимо вернуть
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $queryid, $capitallerwmid) {
        $this->url               = "https://wm.exchanger.ru/asp/XMLWMList3Det.asp";
        if ($type) {
           $this->type           = $type;
        }
        if ($queryid) {
           $this->queryid        = $queryid;
        }
        parent::__construct ($wmid, $signer);
        if ($capitallerwmid) {
           $this->capitallerwmid = $capitallerwmid;
        }
     }
     /**
      * Функция отправки запроса и обработки ответа 
      *
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param string  $queryid         Номер (id) новой заявки, информацию по которой необходимо вернуть
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($wmid, $signer, $queryid, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $queryid, $capitallerwmid);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор цифровой подписи
      *
      * @return string                  Цифровая подпись
      */
     protected function ConstructSignature() {
        $data = $this->wmid; 
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
        if ($this->queryid) {
           $output["queryid"] = $this->queryid;
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
                  "id"            => (string) $row["id"],                      //номер встречной выставленной на обмен заявки
                  "exchtype"      => (string) $row["exchtype"],                //направление обмена
                  "state"         => (string) $row["state"],                   //текущее состояние данной заявки
                  "amountin"      => (string) $row["amountin"],                //сумма WM, которая получает wmid в данной встречной заявке
                  "amountout"     => (string) $row["amountout"],               //сумма WM, которую отдает wmid в данной встречной заявке
                  "inoutrate"     => (string) $row["inoutrate"],               //прямой курс выставленной заявки 
                  "outinrate"     => (string) $row["outinrate"],               //обратный курс выставленной заявки 
                  "querydatecr"   => (string) $row["querydatecr"],             //дата постановки заявки 
                  "querydate"     => (string) $row["querydate"],               //дата последнего изменения в заявке
                  "direction"     => (string) $row["direction"],               //направление обмена в заявке
                  "newtrid"       => (string) $row["newtrid"],                 //если данная встречная заявка была превращена в новую (выплаты на кошелек не было), 
                                                                               //то в данном атртибуте номер этой новой заявки
                  "state"         => (string) $row["state"]);                  //текущее состояние данной заявки
                                                                               //0 - заявка еще не оплачена
                                                                               //1 - оплачена, идет обмен
                                                                               //2 - оплачена и обмен по ней произведен, но выплаты на кошелек еще не завершены
                                                                               //4 - часть новой заявки превращена во встречную 
                                                                               //    (часть исходной новой заявки была потрачена на скупку чужой новой из списка,
                                                                               //    противоположного по направлению к данной)
                                                                               //5 - часть новой заявки была превращена в другую новую заявку (путем разделения на две)
        }
        return $output;
     }
  }
?>

