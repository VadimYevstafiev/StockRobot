
<?php

  /**
   * WMunionBids:Класс вызова интерфейса для автоматического объединения двух новых заявок у конкретного WMID
   */  
  class WMunionBids extends WMsecureInterface {
     private $operid;
     private $unionoperid;
     private $capitallerwmid;

     /**
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $operid          Номер новой заявки, к которой необходимо присоединить заявку unionoperid 
      * @param integer $unionoperid     Номер новой заявки, которую необходимо присоединить к заявке operid
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $operid, $unionoperid, $capitallerwmid) {
        $this->url               = "https://wm.exchanger.ru/asp/XMLTransUnion.asp";
        $this->operid            = $operid;
        $this->unionoperid       = $unionoperid;
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
      * @param integer $operid          Номер новой заявки, к которой необходимо присоединить заявку unionoperid 
      * @param integer $unionoperid     Номер новой заявки, которую необходимо присоединить к заявке operid
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($wmid, $signer, $operid, $unionoperid, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $operid, $unionoperid, $capitallerwmid);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор цифровой подписи
      *
      * @return string                  Цифровая подпись
      */
     protected function ConstructSignature() {
        $data = $this->wmid . $this->operid . $this->unionoperid; 
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
        $output["unionoperid"]       = $this->unionoperid;
        if ($this->capitallerwmid) {
           $output["capitallerwmid"] = $this->capitallerwmid;
        }
        return $output;
     }
  }
?>

