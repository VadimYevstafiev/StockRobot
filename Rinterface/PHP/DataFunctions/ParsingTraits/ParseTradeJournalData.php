<?php
  /**
   * ParseTradeJournalData: Трейт, определяющий функцию подготовки текста протоколов журнала сделок
   */
  trait ParseTradeJournalData {
     /**
      * Функция подготовки текста протоколов журнала сделок
      *
      * @param  string   $data          Строка с массивом данных
      *
      * @return string                  Текст протокола
      */
     private function ParseTradeJournalData($data) {
        if (is_array($data)) {
           $output = "";
           foreach ($data as $key => $value) {
              switch ($key) {
                 case "ont":
                    $output = self::MakeString("Открытие новой сделки по заявке № " . $value . ".");
                    break;
                 case "ncl":
                    $output = self::MakeString("Нормальное закрытие сделки по заявке № " . $value . ".");
                    break;
                 case "tcl":
                    $output = self::MakeString("Критическое несоответствие в сделке по заявке № " . $value . ".");
                    break;
                 case "save":
                    $output .= self::MakeString("Данные сделки по заявке № " . $value . " успешно записаны в журнал.");
                    break;
                 case "cor":
                    $output .= self::MakeString("Данные сделки по заявке № " . $value . " успешно отредактированы.");
                    break;
                 case "del":
                    $output .= self::MakeString("Данные сделки по заявке №  " . $value . " успешно удалены.");
                    break;
              }
           }
        } else {
           switch ($data) {
              case "rj":
                 $output .= self::MakeHead("Обновление журнала сделок.");
                 break;
              case "gd":
                 $output = self::MakeString("Запрашиваем данные активных заявок.");
                 break;
              case "nac":
                 $output .= self::MakeString("Активных заявок нет.");
                 break;
              case "mac":
                 $output .= self::MakeString("Активных заявок больше одной.");
                 break;
              case "nrj":
                 $output .= self::MakeString("Обновление журнала невозможно.");
                 break;
              case "gt":
                 $output = self::MakeString("Запрашиваем данные открытой сделки.");
                 break;
              case "ngt":
                 $output = self::MakeString("Текущих открытых сделок нет.");
                 break;
              case "int":
                 $output = self::MakeString("Данные сделки записаны в журнал.");
                 break;
              case "nnt":
                 $output = self::MakeString("Направление открытой сделки не соответствует главному направлению торгов.");
                 $output .= self::MakeString("Открытие новой сделки невозможно.");
                 break;
              case "icl":
                 $output = self::MakeString("Текущая сделка закрыта.");
                 break;
              case "otr":
                 $output = self::MakeString("Открытая сделка соответствует текущей активной заявке.");
                 break;
              case "fp":
                 $output = self::MakeString("Нарастающий итог превысил максимальное значение.");
                 $output .= self::MakeString("Создается служебная заявка для вывода средств.");
                 break;
              case "cj":
                 $output .= self::MakeHead("Копирование журнала сделок.");
                 break;
              case "ncj":
                 $output = self::MakeString("В журнал не вносились изменения.");
                 $output .= self::MakeString("Копировать журнал нет необходимости.");
                 break;
           }
        }
        return $output;
     }
  }
?>