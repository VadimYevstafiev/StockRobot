<?php
  /**
   * ParseTraderData: Трейт, определяющий функцию подготовки текста протоколов торговца 
   */
  trait ParseTraderData {
     /**
      * Функция подготовки текста протоколов торговца 
      *
      * @param  string   $value         Содержание записи в протоколе
      *
      * @return string                  Текст протокола
      */
     private function ParseTraderData($value) {
        switch ($value[0]) {
           case "list":
              $output = self::WriteCurrentPrice($value[1]);
              break;
           case "exp":
              $output = self::WriteExpertData($value[1]);
              break;
           case "cbj":
              $output = self::MakeHead("Проверка соответствия данных журнала открытым заявкам.");
              break;
           case "opbid":
              $output = self::MakeString("Запрашиваем данные системы об открытых заявках.");
              $output .= self::MakeString("Количество открытых заявок: " . $value[1] . ".");
              break;
           case "dcbj":
              $output = self::MakeItalic("Заявки преобразовывались.");
              $output .= self::MakeString("Повторно запускаем проверку соответствия данных журнала.");
              break;
           case "bj":
              $output = self::ParseBidsJournalData($value[1]);
              break;
           case "tj":
              $output = self::ParseTradeJournalData($value[1]);
              break;
           case "mb":
              $output = self::ParseBidsData($value[1]);
              break;
           case "mab":
              $output = self::ParseActiveBidsData($value[1]);
              break;
           case "wm":
              $output = self::ParseWMbids($value[1]);
              break;
        }
        return $output;
     }
     /**
      * Функция подготовки текста данных о текущих ценах на рынке
      *
      * @param  string   $value         Содержание записи в протоколе
      *
      * @return string                  Текст протокола
      */
     private function WriteCurrentPrice($value) {
        $output = self::MakeItalic("Текущая цена:");
        $output .= self::MakeString("Bid (квалифицированная): " . $value["quality"]["Bid"] . ".");
        $output .= self::MakeString("Bid (неквалифицированная): " . $value["zero"]["Bid"] . ".");
        $output .= self::MakeString("Ask (неквалифицированная): " . $value["zero"]["Ask"] . ".");
        $output .= self::MakeString("Ask (квалифицированная): " . $value["quality"]["Ask"] . ".");
        return $output;
     }
     /**
      * Функция подготовки текста данных о текущих рекомендациях эксперта
      *
      * @param  string   $value         Содержание записи в протоколе
      *
      * @return string                  Текст протокола
      */
     protected function WriteExpertData($value) {
        $output = self::MakeItalic("Текущие рекомендации эксперта:");
        switch ($value["Summary"]) {
           case 2:
              $output .= self::MakeString("Закрыть длинную позицию (заявку Bid).");
              $output .= self::MakeString("Стоп: " . $value["Stop"] . ".");
              break;
           case 1:
              $output .= self::MakeString("Держать длинную позицию (заявку Bid).");
              $output .= self::MakeString("Стоп: " . $value["Stop"] . ".");
              break;
           case 0:
              $output .= self::MakeString("Флэт.");
              $output .= self::MakeString("Bid (покупка): " . $value["Bid"] . ".");
              $output .= self::MakeString("Ask (продажа): " . $value["Ask"] . ".");
              break;
           case -1:
              $output .= self::MakeString("Держать короткую позицию (заявку Ask).");
              $output .= self::MakeString("Стоп: " . $value["Stop"] . ".");
              break;
           case -2:
              $output .= self::MakeString("Закрыть короткую позицию (заявку Ask).");
              $output .= self::MakeString("Стоп: " . $value["Stop"] . ".");
              break;
        } 
        return $output;
     }
  }
?>