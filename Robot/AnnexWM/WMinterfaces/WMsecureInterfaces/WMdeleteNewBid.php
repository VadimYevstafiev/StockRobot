
<?php

  /**
   * WMdeleteNewBid:Класс вызова интерфейса для автоматического удаления своей новой заявки конкретного WMID 
   * с возвратом остатка не обменянных средств
   */  
  class WMdeleteNewBid extends WMsecureInterface {
     private $operid;

     /**
      * @param string  $wmid            WMID, чьи новые заявки необходимо вернуть в результате запроса
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $operid          Номер, выставленной идентификатором wmid, новой заявки,  которую необходимо удалить 
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $operid, $capitallerwmid) {
        $this->url               = "https://wm.exchanger.ru/asp/XMLTransDel.asp";
        $this->operid            = $operid;
        parent::__construct ($wmid, $signer);
        if ($capitallerwmid) {
           $this->capitallerwmid = $capitallerwmid;
        }
     }
     /**
      * Функция отправки запроса и обработки ответа 
      *
      * @param string  $wmid            WMID, чьи новые заявки необходимо вернуть в результате запроса
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $operid          Номер, выставленной идентификатором wmid, новой заявки,  которую необходимо удалить 
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($wmid, $signer, $operid, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $operid, $capitallerwmid);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор цифровой подписи
      *
      * @return string                  Цифровая подпись
      */
     protected function ConstructSignature() {
        $data = $this->wmid . $this->operid; 
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
        if ($this->capitallerwmid) {
           $output["capitallerwmid"] = $this->capitallerwmid;
        }
        return $output;
     }
  }
?>

