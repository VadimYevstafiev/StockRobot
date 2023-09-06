
<?php

  /**
   * WMdivideNewBid:Класс вызова интерфейса для автоматического разделения существующей новой заявки на две 
   * с возвратом остатка не обменянных средств
   */  
  class WMdivideNewBid extends WMsecureInterface {
     private $operid;
     private $exchtype;
     private $outpurse;
     private $inamount;
     private $outamount;

     /**
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $operid          Номер, выставленной идентификатором wmid, новой заявки, которую необходимо разделить на две заявки
      * @param integer $exchtype        Тип новой заявки, которая появится после разделения
      * @param string  $outpurse        Номер кошелька идентификатора wmid, на который будут поступать средства в новой заявке, которая появится после разделения
      * @param double  $inamount        Сумма, которая будет автоматически убрана из существующей заявки с номером operid и перенесена в новую заявку
      * @param double  $outamount       Сумма, которую необходимо перевести на кошелек outpurse в новой заявке, которая появится после разделения
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $operid, $exchtype, $outpurse, $inamount, $outamount, $capitallerwmid) {
        $this->url               = "https://wm.exchanger.ru/asp/XMLTransDivide.asp";
        $this->operid            = $operid;
        $this->exchtype          = $exchtype;
        $this->outpurse          = $outpurse;
        $this->inamount          = $inamount;
        $this->outamount         = $outamount;
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
      * @param integer $operid          Номер, выставленной идентификатором wmid, новой заявки, которую необходимо разделить на две заявки
      * @param integer $exchtype        Тип новой заявки, которая появится после разделения
      * @param string  $outpurse        Номер кошелька идентификатора wmid, на который будут поступать средства в новой заявке, которая появится после разделения
      * @param integer $inamount        Сумма, которая будет автоматически убрана из существующей заявки с номером operid и перенесена в новую заявку
      * @param integer $outamount       Сумма, которую необходимо перевести на кошелек outpurse в новой заявке, которая появится после разделения
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($wmid, $signer, $operid, $exchtype, $outpurse, $inamount, $outamount, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $operid, $exchtype, $outpurse, $inamount, $outamount, $capitallerwmid);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор цифровой подписи
      *
      * @return string                  Цифровая подпись
      */
     protected function ConstructSignature() {
        $data = $this->wmid . $this->operid . $this->exchtype . $this->outpurse . $this->inamount . $this->outamount; 
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
        $output["exchtype"]          = $this->exchtype;
        $output["outpurse"]          = $this->outpurse;
        $output["inamount"]          = $this->inamount;
        $output["outamount"]         = $this->outamount;
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
        $output["divideid"]       = (string) $response->retval["divideid"];  //номер новой заявки, в которую будет перенесена сумма inamount
        if ($this->capitallerwmid) {
           $output["capitallerwmid"] = (string) $response->capitallerwmid;  
        }  
        return $output;
     }
  }
?>

