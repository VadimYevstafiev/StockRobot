<?php
  /**
   * IndexisArrays: Трейт функции создания массива индексов торговой пары
   */
  trait IndexisArrays {
     /**
      * Функция создания массива индексов торговой пары
      *
      * @param string  $direction   Идентификатор торговой пары
      *
      * @return array               Массив индексов торговой пары
      *                             "direction" - идентификатор торговой пары
      *                             "general" - массив типов обмена
      *                             "purses" - массив номеров кошельков, соотвествующих типам обмена
      *                             "timeframes" - массив параметров таймфреймов
      *                                           "general" - номенклатура таймфреймов
      *                                           "trade"   - номенклатура таймфреймов, по которым ведется торговля
      *                             "listener" - массив параметров Слушателя
      *                                          "quality"  - критерий отбора "квалифицированного курса"
      *                                          "gorizont" - строка с временным интервалом, в течении которого данные
      *                                                       Слушателя должны храниться в базе данных
      *                             "direct"  - главное направление торгов
      *                             "BaseLimit"  - максимальное значение опорной заявки
      *                             "ProfitLimit" - максимальное значение нарастающего итога в журнале сделок
      *                             "RateInterest" - процент нарастающего итога, выводимый из системы
      *                             "valueArray" - массив имен данных, которые нужно извлечь 
      */
     private function CreateIndexisArray ($direction) {
        $indexis["direction"]     = $direction;
        switch ($direction) {
           case "WmzWmr":
              $indexis["general"]      = array("Bid" => 1, "Ask" => 2);
              $indexis["purses"]       = array("Bid" => array("inpurse" => PURSEWMZ, "outpurse" => PURSEWMR),
                                               "Ask" => array("inpurse" => PURSEWMR, "outpurse" => PURSEWMZ));

              $indexis["direct"]       = "Bid";
              $indexis["ProfitLimit"]  = 200;
              $indexis["RateInterest"] = 0.5;
              $indexis["BaseLimit"]    = 0.1;
              $timeframes              = array("general"  => array("06H", "01H"),
                                               "trade"    => "06H");
              $listener                = array("quality"  => 1000, 
                                               "gorizont" => "90d");
              break;
           case "WmzWme":
              $indexis["general"]      = array("Bid" => 4, "Ask" => 3);
              $indexis["purses"]       = array("Bid" => array("inpurse" => PURSEWME, "outpurse" => PURSEWMZ),
                                               "Ask" => array("inpurse" => PURSEWMZ, "outpurse" => PURSEWME));
              $indexis["direct"]       = "Ask";
              $indexis["ProfitLimit"]  = 200;
              $indexis["RateInterest"] = 0.5;
              $indexis["BaseLimit"]    = 0.1;
              $timeframes              = array("general"  => array("06H", "01H"),
                                               "trade"    => "06H");
              $listener                = array("quality"  => 1000, 
                                               "gorizont" => "90d");
              break;
           case "WmeWmr":
              $indexis["general"]      = array("Bid" => 5, "Ask" => 6);
              $indexis["purses"]       = array("Bid" => array("inpurse" => PURSEWME, "outpurse" => PURSEWMR),
                                               "Ask" => array("inpurse" => PURSEWMR, "outpurse" => PURSEWME));
              $indexis["direct"]       = "Ask";
              $indexis["ProfitLimit"]  = 200;
              $indexis["RateInterest"] = 0.5;
              $indexis["BaseLimit"]    = 0.1;
              $timeframes              = array("general"  => array("06H", "01H"),
                                               "trade"    => "06H");
              $listener                = array("quality"  => 1000, 
                                               "gorizont" => "90d");
              break;
           case "WmzWmx":
              $indexis["general"]      = array("Bid" => 34, "Ask" => 33);
              $indexis["purses"]       = array("Bid" => array("inpurse" => PURSEWMX, "outpurse" => PURSEWMZ),
                                               "Ask" => array("inpurse" => PURSEWMZ, "outpurse" => PURSEWMX));
              $indexis["direct"]       = "Ask";
              $indexis["ProfitLimit"]  = 200;
              $indexis["RateInterest"] = 0.5;
              $indexis["BaseLimit"]    = 0.1;
              $timeframes              = array("general"  => array("06H", "01H"),
                                               "trade"    => "06H");
              $listener                = array("quality"  => 1000, 
                                               "gorizont" => "90d");
              break;
           case "WmrWmx":
              $indexis["general"]      = array("Bid" => 38, "Ask" => 37);
              $indexis["purses"]       = array("Bid" => array("inpurse" => PURSEWMX, "outpurse" => PURSEWMR),
                                               "Ask" => array("inpurse" => PURSEWMR, "outpurse" => PURSEWMX));
              $indexis["direct"]       = "Ask";
              $indexis["ProfitLimit"]  = 200;
              $indexis["RateInterest"] = 0.5;
              $indexis["BaseLimit"]    = 0.1;
              $timeframes              = array("general"  => array("06H", "01H"),
                                               "trade"    => "06H");
              $listener                = array("quality"  => 1000, 
                                               "gorizont" => "90d");
              break;
        }
        $indexis["valueArray"]         = array(
                                               "Bid" => array("outinrate", "amountin"), 
                                               "Ask" => array("inoutrate", "amountout")
                                               );
        $output["indexis"]             = $indexis;
        $output["timeframes"]          = $timeframes;
        $output["listener"]            = $listener;
        return $output;
     }
  }
?>