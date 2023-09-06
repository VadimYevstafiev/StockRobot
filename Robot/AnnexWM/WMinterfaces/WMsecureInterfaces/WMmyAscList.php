
<?php

  /**
   * WMmyAscList: Класс вызова интерфейса для автоматического получения 
   * информации о встречных заявках конкретного WMID
   */  
  class WMmyAscList extends WMsecureInterface {
     private $type;
     private $queryid;
     private $capitallerwmid;

     /**
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $type            Тип запроса, зарезервировано для будущих применений 
      * @param string  $queryid         Номер (id) новой заявки, информацию по которой необходимо вернуть (необязательный)
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $type, $queryid, $capitallerwmid) {
        $this->url               = "https://wm.exchanger.ru/asp/XMLWMList3.asp";
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
      * @param integer $type            Тип запроса, зарезервировано для будущих применений 
      * @param string  $queryid         Номер (id) новой заявки, информацию по которой необходимо вернуть (необязательный)
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($wmid, $signer, $type, $queryid, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $type, $queryid, $capitallerwmid);
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
        if ($this->type) {
           $data = $data . $this->type;
        }
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
                  "isxid"         => (string) $row["isxid"],                   //номер новой заявки, по которой выставлена данная встречная заявка
                  "exchtype"      => (string) $row["exchtype"],                //направление обмена
                  "state"         => (string) $row["state"],                   //текущее состояние данной заявки
                  "amountin"      => (string) $row["amountin"],                //сумма WM, которая обменивается в данной встречной заявке
                  "amountout"     => (string) $row["amountout"],               //сумма WM, которую получает wmid в данной встречной заявке
                  "inoutrate"     => (string) $row["inoutrate"],               //прямой курс выставленной заявки 
                  "outinrate"     => (string) $row["outinrate"],               //обратный курс выставленной заявки 
                  "inpurse"       => (string) $row["inpurse"],                 //кошелек с которого была произведена оплата заявки
                  "outpurse"      => (string) $row["outpurse"],                //кошелек на который переводятся обменянные средства
                  "querydatecr"   => (string) $row["querydatecr"],             //дата постановки заявки 
                  "querydate"     => (string) $row["querydate"],               //дата последнего изменения в заявке
                  "direction"     => (string) $row["direction"],               //направление обмена в заявке
                  "newtrid"       => (string) $row["newtrid"],                 //если данная встречная заявка была превращена в новую (выплаты на кошелек не было), 
                                                                               //то в данном атртибуте номер этой новой заявки
                  "state"         => (string) $row["state"]);                  //текущее состояние данной заявки
                                                                               //0 - заявка еще не оплачена
                                                                               //1 - оплачена, идет обмен
                                                                               //2 - обменяна полностью
                                                                               //3 - объединена с другой новой
                                                                               //4 - удалена, средства не возвращены
                                                                               //5 - удалена, средства возвращены
        }
        return $output;
     }
  }
?>

