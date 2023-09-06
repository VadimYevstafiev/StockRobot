<?php
  /**
   * WMserviceConfigurations: Трейт, определяющий функции установки конфигурации WM
   */
  trait ServiceConfigurations {
     /**
      * Функция массива используемых типов значений курса
      *
      * @param  array    $indexis       Массив типов значений курса, используемых:
      *                                 "Expert" - экспертом
      *                                            "quality" - "квалифицированный курс"
      *                                            "zero"    - "неквалифицированный курс"
      *                                 "Trader" - торговцем
      *                                            "quality" - "квалифицированный курс"
      *                                            "zero"    - "неквалифицированный курс"
      * @param  integer  $makecount     Максимальное количество попыток развернуть заявку
      *                                 в режиме мейкера
      * @param  array    $sleepratio    Массив коэффициентов для перевода заявки
      *                                 в режим ожидания
      *                                 "Bid" - в длинной позиции
      *                                 "Ask" - в короткой позиции
      */
     protected $tradingtype     = array("Expert" => "quality",
                                        "Trader" => "zero");
     protected $makecount       = 5;
     protected $sleepratio      = array("Bid" => 1.25, 
                                        "Ask" => 0.8);
     /**
      * Функция получения используемого типа значений курса
      *                                 
      * @param  string   $type          Пользователь значений курса
      *                                 "Expert" - экспертом
      *                                 "Trader" - торговцем
      *
      * @return string                  Используемый тип значений курса
      */
     static public function GetTradingtype($type) {
        self::SetConfigurations();
        return self::$instance->tradingtype[$type];
     }
     /**
      * Функция получения максимального количества попыток развернуть заявку
      *
      * @return integer  $makecount     Максимальное количество попыток развернуть заявку
      *                                 в режиме мейкера
      */
     public function GetMakecount() {
        self::SetConfigurations();
        return self::$instance->makecount;
     }
     /**
      * Функция получения максимального количества попыток развернуть заявку
      *                                 
      * @param  string   $direction     Тип позиции
       *                                "Bid" - длинная позиция
      *                                 "Ask" - короткая позиция
      *
      * @return integer  $makecount     Максимальное количество попыток развернуть заявку
      *                                 в режиме мейкера
      */
     public function GetSleepRateRatio($direction) {
        self::SetConfigurations();
        return self::$instance->sleepratio[$direction];
     }
  }
?>