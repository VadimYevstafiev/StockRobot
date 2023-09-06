<?php
  /**
   * TraderWM:  Производный класс торговца Webmoney
   */  
  class Trader extends BaseTrader {
     use ActiveBidsFunctions, ServiceBidsFunctions, BaseBidsFunctions, BidsDataFunctions, UnionBids;
     /**
      * @param string  $wmid            WMID, от имени которого ведется торговля
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param array   indexis          Массив типов обмена
      * @param string  $capitallerwmid  WMID капиталлера (необязательный)
      */
     protected $wmid;
     protected $signer;
     protected $indexis;
     protected $capitallerwmid;
     /**
      * Конструктор
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $listenerdata  Массив данных слушателя
      * @param  array    $expertdata    Массив рекомендаций эксперта 
      */
     protected function __construct ($protocol, $listenerdata, $expertdata) {
        parent::__construct($protocol, $listenerdata, $expertdata);
        $this->wmid    = WMID;
        $this->signer  = new Signer(WMID, KEY, PASSWORD);
        $this->indexis = Configurations::GetIndexis();
     }
  }
?>

