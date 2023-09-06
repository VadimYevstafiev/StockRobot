<?php
  /**
   * WMbuyAlienBid:Класс вызова интерфейса для автоматической покупки из своей новой заявки чужой новой,
   * противоположной по направлению обмена
   */  
  class WMbuyAlienBid extends WMsecureInterface {
     private $isxtrid;
     private $desttrid;
     private $deststamp;
     private $capitallerwmid;

     /**
      * @param string  $wmid            WMID, чьи новые заявки необходимо вернуть в результате запроса
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param integer $isxtrid         Номер новой заявки c которой будет производиться покупка чужой заявки
      * @param string  $desttrid        Номер чужой заявки, которую необходимо купить
      * @param integer $deststamp       Число равное сумме часа, минуты и секунды из даты заявки, которую необходимо купить.
      *                                 Для совместимости в данном параметре можно ничего не передавать или передавать число 1001
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $isxtrid, $desttrid, $deststamp, $capitallerwmid) {
        parent::__construct ($wmid, $signer);
        $this->url               = "https://wm.exchanger.ru/asp/XMLQrFromTrIns.asp";
        $this->isxtrid           = $isxtrid;
        $this->desttrid          = $desttrid;
        if ($deststamp) {
           $this->deststamp      = $deststamp;
        } else {
           $this->deststamp      = 1001;
        }
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
     static public function SendQuery($wmid, $signer, $isxtrid, $desttrid, $deststamp, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $isxtrid, $desttrid, $deststamp, $capitallerwmid);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор цифровой подписи
      *
      * @return string                  Цифровая подпись
      */
     protected function ConstructSignature() {
        $data = $this->wmid . $this->isxtrid . $this->desttrid; 
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
        $output["isxtrid"]           = $this->isxtrid;
        $output["desttrid"]          = $this->desttrid;
        $output["deststamp"]         = $this->deststamp;
        if ($this->capitallerwmid) {
           $output["capitallerwmid"] = $this->capitallerwmid;
        }
        return $output;
     }
  }
?>

