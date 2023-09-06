<?php
  /**
   * ParseBidsData: Трейт, определяющий функцию подготовки текста протоколов управления заявками 
   */
  trait ParseBidsData {
     /**
      * Функция подготовки текста протоколов текста протоколов управления заявками
      *
      * @param  string   $data          Содержание записи в протоколе
      *
      * @return string                  Текст протокола
      */
     private function ParseBidsData($data) {
        if (is_array($data)) {
           $output = "";
           foreach ($data as $key => $value) {
              switch ($key) {
                 case "dir":
                    $output .= self::WriteBidsDirection($value);
                    break;
                 case "mr":
                    $output .= self::WriteMorePattern($value);
                    break;
                 case "ad":
                    $output .= self::WriteAddPattern($value);
                    break;
                 case "un":
                    $output .= self::WriteUnionPattern($value);
                    break;
              }
           }
        } else {
           switch ($data) {
              case "st1":
                 $output = self::MakeHead("Общее управление заявками.");
                 break;
              case "st2":
                 $output = self::MakeHead("Управление опорными и служебными заявками.");
                 break;
              case "st3":
                 $output = self::MakeItalic("Заявки преобразовывались.");
                 $output .= self::MakeString("Повторно запускаем процедуры управления заявками.");
                 break;
              case "gd":
                 $output = self::MakeString("Запрашиваем данные журнала заявок.");
                 break;
              case "dgd":
                 $output = self::MakeItalic("Заявки преобразовывались.");
                 $output .= self::MakeString("Повторно запрашиваем данные журнала заявок.");
                 break;
              case "sn":
                 $output = self::MakeItalic("Записываем ранее неучтенную заявку как опорную.");
                 break;
              case "isac":
                 $output = self::MakeString("Есть незакрытые активные заявки противоположного направления.");
                 $output .= self::MakeString("Разделять опорную заявку нельзя.");
                 break;
              case "issr":
                 $output = self::MakeItalic("Есть служебные заявки.");
                 $output .= self::MakeString("Удаляем служебные заявки.");
                 break;
           }
        }
        return $output;
     }
     /**
      * Функция подготовки текста о направлении заявки
      *
      * @param  string   $direction     Направление заявки

      */
     protected function WriteBidsDirection($direction) {
        switch ($direction) {
           case "Bid":
              $output = self::MakeItalic("Направление заявок: Bid (покупка).");
              break;
           case "Ask":
              $output = self::MakeItalic("Направление заявок: Ask (продажа).");
              break;
        }
        return $output;
     }
     /**
      * Функция подготовки текста о количестве заявок
      *
      * @param  string   $type          Тип заявок
      *                                 "nw" - ранее неучтенные заявки
      *                                 "ac" - активные заявки
      *                                 "bs" - опорные заявки
      */
     protected function WriteMorePattern($type) {
        switch ($type) {
           case "nw":
              $output = self::MakeItalic("Есть ранее неучтенные заявки.");
              break;
           case "ac":
              $output = self::MakeItalic("Открытых активных заявок больше одной.");
              break;
           case "bs":
              $output = self::MakeItalic("Открытых опорных заявок больше одной.");
              break;
        }
        return $output;
     }
     /**
      * Функция подготовки текста о присоединении заявок
      *
      * @param  string   $type          Тип заявок
      */
     protected function WriteAddPattern($type) {
        switch ($type) {
           case "bs":
              $output = self::MakeString("Присоединяем лишние опорные заявки к активной.");
              break;
           case "nw":
              $output = self::MakeString("Присоединяем ранее неучтенные заявки к существующим.");
              break;
        }
        return $output;
     }
     /**
      * Функция подготовки текста об объединении заявок
      *
      * @param  string   $type          Тип заявок
      */
     protected function WriteUnionPattern($type) {
        switch ($type) {
           case "ac":
              $output = self::MakeString("Объединяем активные заявки.");
              break;
           case "bs":
              $output = self::MakeString("Объединяем опорные заявки.");
              break;
           case "nw":
              $output = self::MakeString("Объединяем ранее неучтенные заявки.");
              break;
        }
        return $output;
     }
  }
?>