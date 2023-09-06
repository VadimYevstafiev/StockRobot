
<?php

  /**
   * WMchangeBidsRate:Класс вызова интерфейса для автоматического изменения курса обмена своей новой заявки
   */  
  class WMchangeBidsRate extends WMsecureInterface {
     private $operid;
     private $cursamount;
     private $curstype;
     private $capitallerwmid;

     /**
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $operid          Номер, выставленной идентификатором wmid, новой заявки, курс обмена которой необходимо изменить 
      * @param integer $curstype        Тип курса в тэге cursamount
      * @param string  $cursamount      Новое числовое значение курса обмена заявки operid
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $operid, $curstype, $cursamount, $capitallerwmid) {
        $this->url               = "https://wm.exchanger.ru/asp/XMLTransIzm.asp";
        $this->operid            = $operid;
        $this->cursamount        = $cursamount;
        $this->curstype          = $curstype;
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
      * @param integer $operid          Номер, выставленной идентификатором wmid, новой заявки, курс обмена которой необходимо изменить 
      * @param integer $curstype        Тип курса в тэге cursamount
      * @param string  $cursamount      Новое числовое значение курса обмена заявки operid
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($wmid, $signer, $operid, $curstype, $cursamount, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $operid, $curstype, $cursamount, $capitallerwmid);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор цифровой подписи
      *
      * @return string                  Цифровая подпись
      */
     protected function ConstructSignature() {
        $data = $this->wmid . $this->operid . $this->curstype . $this->cursamount; 
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
        $output["operid"]            = $this->operid;
        $output["cursamount"]        = $this->cursamount;
        $output["curstype"]          = $this->curstype;
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
        $output["AmountRestIn"]  = (string) $response->AmountRestIn;           //сумма, выставленная к обмену 
        $output["AmountRestOut"] = (string) $response->AmountRestOut;          //сумма, которая будет получена после обмена по измененному курсу
        return $output;
     }
  }
?>

