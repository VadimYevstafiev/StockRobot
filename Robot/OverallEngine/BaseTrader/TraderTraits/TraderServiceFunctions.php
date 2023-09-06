<?php
  /**
   * TraderServiceFunctions: Трейт, определяющий служебные функции торговца
   */
  trait TraderServiceFunctions {
     /**
      * Функция вычисления курса 
      *
      * @param  double   $direction     Направление заявки 
      * @param  double   $amountin      Cумма, выставленная на обмен в заявке
      * @param  double   $amountout     Сумма, которую ожидается получить в данной заявке
      *
      * @return double                  Курс заявки
      */
     protected function CalculateRate($direction, $amountin, $amountout) {
        switch ($direction) {
           case "Bid":
              $output = round(($amountout / $amountin), 4);
              break;
           case "Ask":
              $output = round(($amountin / $amountout), 4);
              break;
        }
        return $output;
     }
     /**
      * Функция вычисления курса 
      *
      * @param  array    $direction     Направление заявки 
      *
      * @return double                  Курс заявки
      */
     protected function CalculateSleepRate($direction) {
        $rate = $this->listenerdata[Configurations::GetTradingtype("Trader")][$direction];
        $output = round((Configurations::GetSleepRateRatio($direction) * $rate), 4);
        return $output;
     }
     /**
      * Функция вычисления курса 
      *
      * @param  double   $direction     Направление заявки 
      * @param  double   $amountin      Cумма, выставленная на обмен в заявке
      * @param  double   $rate          Значение курса
      *
      * @return double                  Курс заявки
      */
     protected function SmartCalculateRate($direction, $amountin, $rate) {
        switch ($direction) {
           case "Bid":
              $service = floor($amountin * 100 * $rate) / 100 - 0.001;
              $output = round(($service / $amountin), 4);
              break;
           case "Ask":
              $service = floor($amountin * 100 / $rate) / 100 - 0.001;
              $output = round(($amountin / $service), 4);
              break;
        }
        return $output;
     }
     /**
      * Функция вычисления суммы, которую ожидается получить в данной заявке
      *
      * @param  array    $data          Массив данных записи 
      * @param  double   $rate          Значение курса
      *
      * @return double                  Сумма, которую ожидается получить в данной заявке
      */
     protected function CalculateAmountout($data, $rate) {
        switch ($data["Direction"]) {
           case "Bid":
              $output = ceil($data["Amountin"] * 100 * $rate) / 100;
              break;
           case "Ask":
              $output = ceil($data["Amountin"] * 100 / $rate) / 100;
              break;
        }
        return $output;
     }
     /**
      * Функция поиска заявки с экстремальным курсом среди записей одного направления
      *
      * @param array   $journalData   Массив извлеченных из журнала записей одного направления
      *
      * @return integer $output[0]    Номер заявки с экстремальным курсом
      * @return double  $output[1]    Массив номеров остальных заявок
      */
     protected function FindExtremeBid($journalData) {
        $data = array($journalData[0]["Rate"], 0);
        for ($i = 1; $i < count($journalData); $i++) {
           switch ($journalData[$i]["Direction"]) {
              case "Bid":
                 if ($journalData[$i]["Rate"] > $data[0]) {
                    $data[0] = $journalData[$i]["Rate"];
                    $data[1] = $i;
                 }
                 break;
              case "Ask":
                 if ($journalData[$i]["Rate"] < $data[0]) {
                    $data[0] = $journalData[$i]["Rate"];
                    $data[1] = $i;
                 }
                 break;
           }
        }
        $output[0] = $journalData[$data[1]]["BidID"];
        $output[1] = array();
        for ($i = 0; $i < count($journalData); $i++) {
           if ($i != $data[1]) {
              $output[1][] = $journalData[$i]["BidID"];
           }
        }
        return $output;
     }
     /**
      * Функция получения противоположного направления
      *
      * @param  string  $direction      Значение направления
      *
      * @return string                  Значение противоположного направления
      */
     protected function GetOppositeDirection($direction) {
        switch ($direction) {
           case "Bid":
              $output = "Ask";
              break;
           case "Ask":
              $output = "Bid";
              break;
        }
        return $output;
     }
     /**
      * Функция проверки, имеет ли опорная заявка максимально допустимое значение
      *
      * @param  array    $data          Массив данных записи опорной заявки
      * @param  double   $rate          Значение курса
      * @param  double   $baseLimit     Максимально допустимое значение опорной заявки
      *
      * @return bool                    TRUE, если опорная заявка меньше или равна максимально 
      *                                 допустимому значению или FALSE в противном случае
      */
     protected function CheckEmptyBase($data, $rate, $baseLimit) {
        $check = (float) $baseLimit;
        switch ($data["Direction"]) {
           case "Bid":
              $value = (float) $data["Amountin"];
              break;
           case "Ask":
              if ($rate > 1) {
                 $value = (float) $data["Amountout"];
              } else {
                 $value = (float) $data["Amountin"];
              }
              break;
        }
        if ($value <= $check) {
           $output = TRUE;
        } else {
           $output = FALSE;
        }
        return $output;
     }
  }
?>

