
<?php

  /**
   * WMsetNewBid:Класс вызова интерфейса для автоматической постановки на обмен новой заявки
   */  
  class WMsetNewBid extends WMsecureInterface {
     private $inpurse;
     private $outpurse;
     private $inamount;
     private $outamount;
     private $capitallerwmid;

     /**
      * @param string  $wmid            WMID, чьи новые заявки необходимо вернуть в результате запроса
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param string  $inpurse         Номер кошелька идентификатора wmid, с которого необходимо взять сумму к обмену для постановки заявки 
      * @param string  $outpurse        Номер кошелька идентификатора wmid, на который будут поступать средства по мере обмена
      * @param double  $inamount        Сумма, которая будет выставлена к обмену
      * @param double  $outamount       Сумма, которую необходимо перевести на кошелек outpurse по завершению обмена
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected function __construct ($wmid, $signer, $inpurse, $outpurse, $inamount, $outamount, $capitallerwmid) {
        $this->url               = "https://wm.exchanger.ru/asp/XMLTrustPay.asp";
        $this->inpurse           = $inpurse;
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
      * @param string  $wmid            WMID, чьи новые заявки необходимо вернуть в результате запроса
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param string  $inpurse         Номер кошелька идентификатора wmid, с которого необходимо взять сумму к обмену для постановки заявки 
      * @param string  $outpurse        Номер кошелька идентификатора wmid, на который будут поступать средства по мере обмена
      * @param double  $inamount        Сумма, которая будет выставлена к обмену
      * @param double  $outamount       Сумма, которую необходимо перевести на кошелек outpurse по завершению обмена
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      *
      * @return array                Массив полученных значений
      */
     static public function SendQuery($wmid, $signer, $inpurse, $outpurse, $inamount, $outamount, $capitallerwmid) {
        self::$instance = new self($wmid, $signer, $inpurse, $outpurse, $inamount, $outamount, $capitallerwmid);
        $output = parent::SendQuery(self::$instance);
        return $output;
     }
     /**
      * Конструктор цифровой подписи
      *
      * @return string                  Цифровая подпись
      */
     protected function ConstructSignature() {
        $data = $this->wmid . $this->inpurse . $this->outpurse . $this->inamount . $this->outamount; 
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
        $output["inpurse"]           = $this->inpurse;
        $output["outpurse"]          = $this->outpurse;
        $output["inamount"]          = $this->inamount;
        $output["outamount"]         = $this->outamount;
        if ($this->capitallerwmid) {
           $output["capitallerwmid"] = $this->capitallerwmid;
        }
        return $output;
     }
  }
?>

