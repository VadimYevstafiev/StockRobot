<?php
  /**
   * BaseTrader: Базовый класс торговца
   */  
  class BaseTrader {
     use ConvertDataFunction, TraderServiceFunctions, CheckBidsJournalFunctions,
     ManageBidsFunctions, ManageActiveBidsFunctions, TradeJournalFunctions;
     /**
      * @param  object   $bidsJournal   Экземпляр комплектатора журнала заявок
      * @param  object   $tradeJournal  Экземпляр комплектатора журнала сделок
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  object   $configuration Объект конфигурации ядра приложения
      * @param  integer  $makecount     Максимальное количество попыток развернуть заявку
      *                                 в режиме мейкера
      * @param  integer  $summary       Результат выполнения процедуры торговли
      *                                 0 - Не удалось выполнить процедуру торгов
      *                                 1 - Процедура торгов выполняется
      *                                 2 - Процедура торгов выполнена успешно.
      *                                     Активных заявок нет.
      *                                 3 - Процедура торгов выполнена успешно.
      *                                     Активная заявка в режиме ожидания позиции.
      *                                 4 - Процедура торгов выполнена успешно.
      *                                     Активная заявка в режиме удержания позиции.
      *                                 5 - Процедура торгов выполнена успешно.
      *                                     Закрытие активной заявки в режиме мейкера.
      *                                 6 - Процедура торгов выполнена успешно.
      *                                     Закрытие активной заявки в режиме тейкера.
      */
     protected $protocol;
     protected $bidsJournal;
     protected $tradeJournal;
     protected $configuration;
     protected $makecount;
     protected $summary;
     /**
      * Общая функция процедуры торговли
      *
      * @param array   $listenerdata  Массив данных слушателя
      * @param array   $expertdata    Массив рекомендаций эксперта 
      */
     static public function ExecuteTrading($listenerdata, $expertdata) {
        $protocol = ProtocolTableComplector::Initialize("trader");
        $protocol->StartMessage();
        try {
           $summary = self::TradingProcedure($protocol, $listenerdata, $expertdata);
        } catch (Exception $e) {
           $summary = 0;
           $message = $e->getMessage();
           $protocol->AddMessage("err", $message);
        }
        BidsJournalComplector::Delete();
        TradeJournalComplector::Delete();
        $protocol->FinishMessage($summary);
        ProtocolTableComplector::Delete();
     }
     /**
      * Конструктор
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $listenerdata  Массив данных слушателя
      * @param  array    $expertdata    Массив рекомендаций эксперта 
      */
     protected function __construct ($protocol, $listenerdata, $expertdata) {
        $this->protocol     = $protocol;
        $this->summary      = 0;
        $this->bidsJournal  = BidsJournalComplector::Initialize($protocol, $listenerdata, $expertdata);
        $this->tradeJournal = TradeJournalComplector::Initialize($protocol);
        $this->makecount    = Configurations::GetMakecount();
        $this->listenerdata = $listenerdata;
        $this->expertdata   = $expertdata;
     }
     /**
      * Функция процедуры торговли
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $listenerdata  Массив данных слушателя
      * @param  array    $expertdata    Массив рекомендаций эксперта 
      */
     static protected function TradingProcedure($protocol, $listenerdata, $expertdata) {
        if (empty($listenerdata)) {
           throw new Exception("Данные слушателя не получены. Процедура торгов должна быть прервана.");
        } 
        $protocol->AddMessage("list", $listenerdata);
        if (empty($expertdata)) {
           throw new Exception("Рекомендации эксперта не получены. Процедура торгов должна быть прервана.");
        }
        $protocol->AddMessage("exp", $expertdata);

        $instance = new static($protocol, $listenerdata, $expertdata);

        $instance->CheckBidsJournal();
        $instance->ManageBids();
        $instance->ManageActiveBids();
        $instance->RefreshTradeJournal();
        $output = $instance->CloneJournals();
        return $output;
     }
     /**
      * Функция копирования журналов
      */
     public function CloneJournals() {
        $this->bidsJournal->CloneJournal();
        $this->tradeJournal->CloneJournal();
        return $this->summary;
     }
     /**
      * Функция получения инфорации о заявках торговца
      *
      * @param string  $type            Тип запроса:
      *                                 "all"    - вернуть все заявки независимо от состояния
      *                                 "open"   - вернуть только неоплаченные заявки
      *                                 "trade"  - вернуть оплаченные заявки, но еще не погашенные (по которым еще идет обмен)
      *                                 "close"  - вернуть только уже завершенные (обменяные) заявки
      *                                 "union"  - вернуть только объединенные заявки
      *                                 "delete" - вернуть только объединенные заявки
      * @param string  $queryid         Номер (id) новой заявки, информацию по которой необходимо вернуть (необязательный)
      *
      * @return array                   Массив данных о заявках торговца
      */
     protected function GetMyBidsList($type, $queryid) {
     }
  }
?>

