<?php
  /**
   * MainModule: Базовый класс функций основных модулей приложения
   */
  class MainModule {
     /**
      * @param  object   $instance      Экземпляр модуля
      * @param  string   $flag          Индикатор типа протокола
      */
     protected $flag;
     /**
      * Функция обновления данных модуля
      */
     static public function Refresh() {
        $instance = new static();
        $protocol = ProtocolTableComplector::Initialize($instance->flag);
        $instance->ExecuteRefresh($protocol);
        ProtocolTableComplector::Delete();
     }
     /**
      * Функция контроля времени обновления данных 
      *
      * @param  integer  $time          Метка текущего времени
      * @param  integer  $time          Контрольная метка времени
      *
      * @return bool                    TRUE - если необходимо обновлять данные
      *                                 FALSE - если нет необходимости обновлять данные
      */
     static public function CheckRefreshTimeframe($time, $factor) {
        if ((is_int($time)) && (bcmod($time, $factor) == 0)) {
           $output = TRUE;
        } else {
           $output = FALSE;
        }
        return $output;
     }
     /**
      * Конструктор
      */
     protected function __construct () {
     }
     /**
      * Служебная функция обновления данных модуля
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      *
      * @return                         Результат выполнения обновления
      *
      */
     protected function ExecuteRefresh($protocol) {
        $protocol->StartMessage();
        try { 
           $this::ServiceComplete($protocol);
           $summary = 2;
        } catch (Exception $e) {
           $summary = 0;
           $message = $e->getMessage();
           $protocol->AddMessage("err", $message);
        }
        $protocol->FinishMessage($summary);
     }

     /**
      * Служебная функция обновления данных модуля
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      *
      */
     protected function ServiceComplete($protocol) {
     }
     /**
      * Функция определения служебного массива дат и времени
      *
      * @param  string   $timeframe     Таймфрейм 
      * @param  array    $datetimes     Массив дат и времени
      *
      * @return array                   Служебный массив дат и времени
      */
     static protected function CompleteServiceDatetimes($timeframe, $datetimes) {
        foreach ($datetimes as $key => $value) {
           if (is_array($value)) {
              $output[$key] = $value[$timeframe];
           } else {
              $output[$key] = $value;
           }
        }
        return $output;
     }
  }
?>