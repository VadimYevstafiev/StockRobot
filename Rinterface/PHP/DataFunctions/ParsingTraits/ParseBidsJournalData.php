<?php
  /**
   * ParseBidsJournalData: Трейт, определяющий функцию подготовки текста протоколов журнала заявок
   */
  trait ParseBidsJournalData {
     /**
      * Функция подготовки текста протоколов журнала заявок
      *
      * @param  string   $data          Строка с массивом данных
      *
      * @return string                  Текст протокола
      */
     private function ParseBidsJournalData($data) {
        if (is_array($data)) {
           $output = "";
           foreach ($data as $key => $value) {
              switch ($key) {
                 case "counew":
                    $output .= self::MakeItalic("Есть открытые заявки, не имеющие соответствующих записей в журнале.");
                    $output .= self::MakeString("Количество таких заявок: " . $value . ".");
                    break;
                 case "du":
                    $output .= self::ParseDuplicationData($value);
                    break;
                 case "scr":
                    $output .= self::WriteScripts($value);
                    break;
                 case "def":
                    $output .= self::WriteDefinitionScripts($value);
                    break;
                 case "scn":
                    $output .= self::MakeString("Заявка № " . $value . ".");
                    break;
                 case "st1":
                    $output .= self::MakeString("Состояние заявки:");
                    $output .= self::WriteStateBid($value);
                    break;
                 case "st2":
                    $output .= self::MakeString("Устанавливаем заявку в режим:");
                    $output .= self::WriteStateBid($value);
                    break;
                 case "st3":
                    $output .= self::MakeString("Оставляем заявку в режиме:");
                    $output .= self::WriteStateBid($value);
                    break;
                 case "cli":
                    $output .= self::MakeItalic("Заявка  № " . $value . " уже закрыта.");
                    $output .= self::MakeString("Закрываем соответствующую запись в журнале.");
                    break;
                 case "wr":
                    $output .= self::WriteRelevanceResult($value);
                    break;
                 case "new":
                    $output .= self::WriteNewBid($value);
                    break;
                 case "ob":
                    $output .= self::WriteOpenBid($value);
                    break;

                 case "save":
                    $output .= self::MakeString("Данные заявки № " . $value . " успешно записаны в журнал.");
                    break;
                 case "cor":
                    $output .= self::MakeString("Данные заявки № " . $value . " успешно отредактированы.");
                    break;
                 case "del":
                    $output .= self::MakeString("Данные заявки № " . $value . " успешно удалены.");
                    break;
              }
           }
        } else {
           switch ($data) {
              case "deltab":
                 $output = self::MakeString("Удаляем существующую таблицу.");
                 break;
              case "gd":
                 $output = self::MakeItalic("Запрашиваем данные журнала заявок.");
                 break;
              case "dgd":
                 $output = self::MakeItalic("Повторно запрашиваем данные журнала заявок.");
                 break;
              case "opi":
                 $output .= self::MakeString("Есть открытая заявка, соответствующая этой записи в журнале.");
                 break;
              case "opn":
                 $output .= self::MakeString("Нет открытых заявок, соответствующих этой записи в журнале.");
                 break;
              case "rv":
                 $output .= self::MakeString("Разворачиваем заявку.");
                 break;
              case "cs":
                 $output .= self::MakeString("Закрываем заявку по стопу.");
                 break;
              case "savleg":
                 $output .= self::MakeHead("Записываем данные легенды в журнал.");
                 break;
              case "cj":
                 $output .= self::MakeHead("Копирование журнала заявок.");
                 break;
              case "ncj":
                 $output = self::MakeString("В журнал не вносились изменения.");
                 $output .= self::MakeString("Копировать журнал нет необходимости.");
                 break;
           }
        }
        return $output;
     }
     /**
      * Функция подготовки текста протоколов проверки записей журнала заявок на дублирование 
      *
      * @param  string   $data          Строка с массивом данных
      *
      * @return string                  Текст протокола
      */
     private function ParseDuplicationData($data) {
        if (is_array($data)) {
           $output = "";
           foreach ($data as $key => $value) {
              switch ($key) {
                 case "save":
                    $output .= self::MakeString("Существует несколько записей, соответствующих заявке № " . $key . ".");
                    $output .= self::MakeString("Количество таких записей: " . $value . ".");
                    $output .= self::MakeString("Определяем последнюю по времени изменения запись.");
                    break;
              }
           }
        } else {
           switch ($data) {
              case "no":
                 $output = self::MakeString("Проверять записи на дублирование нет необходимости.");
                 break;
              case "ye":
                 $output = self::MakeString("Проверка записей на дублирование.");
                 break;
              case "del":
                 $output = self::MakeString("Удаляем дублирующиеся записи, кроме последней по времени изменения.");
                 break;
              case "ok":
                 $output = self::MakeString("В журнале нет дублирования записей.");
                 break;
           }
        }
        return $output;
     }
     /**
      * Функция подготовки текста протоколов сортировки по состоянию записей в журнале 
      *
      * @param array   $data       Массив записей в журнале
      */
     protected function WriteScripts($data) {
        $output = self::MakeString("Количество записей об открытых заявках: " . ($data[0] + $data[1] + $data[2] + $data[3]) . ".");
        $output .= self::MakeString("В том числе:");
        $output .= self::MakeString("Ранее неучтенных заявок: " . $data[0] . ".");
        $output .= self::MakeString("Опорных заявок: " . $data[1] . ".");
        $output .= self::MakeString("Активных заявок: " . $data[2] . ".");
        $output .= self::MakeString("Служебных заявок: " . $data[3] . ".");
        return $output;
     }
     /**
      * Функция подготовки текста сообщения о начале проверки записей в журнале  одного состояния и одного направления
      *
      * @param array   $data       Массив записей в журнале
      */
     protected function WriteDefinitionScripts($data) {
        switch ($data[0]) {
           case "base":
              $message = "опорн";
              break;
           case "active":
              $message = "активн";
              break;
           case "service":
              $message = "служебн";
              break;
           case "new":
              $message = "ранее неучтенн";
              break;
        }
        $output = self::MakeItalic("Проверка ". $message ."ых заявок (направление: " . $data[1] . ").");
        $output .= self::MakeString("Количество записей об ". $message ."ых заявках (направление: " . $data[1] . "): " . $data[2] . ".");
        return $output;
     }
     /**
      * Функция подготовки текста протоколов проверки актуальности данных журнала
      *
      * @param array   $result     Массив результатов проверки актуальности данных журнала
      */
     protected function WriteRelevanceResult($result) {
        switch ($result[0]) {
           case 0:
              $output = self::MakeString("Данные журнала соответствуют данным заявки.");
              break;
           case 1:
              $output = self::MakeString("Данные журнала не соответствуют данным заявки.");
              break;
           case 2:
              $output = self::MakeString("Критическое несоответствие между данными журнала и данными заявки.");
              break;
        }
        foreach ($result[1] as $key => $value) {
           $output .= self::MakeString(self::SelectColumnName($key));
           if ($key == "Closetime") {
              $message = "Данные журнала = ";
              if ($value["journal"]) {
                 $message .= (new DateTime)->setTimestamp($value["journal"])->format("d.m.Y H:i:s");
              } else {
                 $message .= "null";
              }
              $message .= ", данные системы = ";
              if ($value["bids"]) {
                 $message .= (new DateTime)->setTimestamp($value["bids"])->format("d.m.Y H:i:s");
              } else {
                 $message .= "null";
              }
              $message .= ".";
           } else {
              $message = "Данные журнала = ";
              if ($value["journal"]) {
                 $message .= $value["journal"];
              } else {
                 $message .= "null";
              }
              $message .= ", данные системы = " . $value["bids"];
           }
           $output .= self::MakeString($message);
        }
        return $output;
     }
     /**
      * Функция выбора имени столбца журнала
      *
      * @param string  $name      Имя столбца журнала
      */
     protected function SelectColumnName($name) {
        switch ($name) {
           case "Scriptime":
              $output = "Время последнего изменения записи:";
              break;
           case "Opentime":
              $output = "Время открытия заявки:";
              break;
           case "BidID":
              $output = "Номер выставленной на обмен заявки:";
              break;
           case "Direction":
              $output = "Направление заявки:";
              break;
           case "Amountin":
              $output = "Сумма, которая выставлена на обмен в данной заявке:";
              break;
           case "Amountout":
              $output = "Сумма, которую ожидается получить в данной заявке:";
              break;
           case "Exchamountin":
              $output = "Сумма, которая фактически обменяна в данной заявке:";
              break;
           case "Exchamountout":
              $output = "Сумма, которая фактически получена в данной заявке:";
              break;
           case "Rate":
              $output = "Значение курса обмена заявки:";
              break;
           case "State":
              $output = "Код состояния заявки:";
              break;
           case "Closetime":
              $output = "Время закрытия заявки:";
              break;
           case "Closetype":
              $output = "Тип закрытия заявки:";
              break;
           case "Revnumber":
              $output = "Количество попыток развернуть заявку в режиме мейкера: ";
              break;
        }
        return $output;
     }
     /**
      * Функция подготовки текста об открытии новой заявки
      *
      * @param  array    $data          Массив с данными заявки

      */
     protected function WriteNewBid($data) {
        $output = "";
        foreach ($data as $key => $value) {
           switch ($key) {
              case "nw":
                 $output .= self::MakeItalic("Обнаружена заявка № " . $value . ".");
                 break;
              case "bs":
                 $output .= self::MakeItalic("Создана новая опорная заявка № " . $value . ".");
                 break;
              case "sr":
                 $output .= self::MakeItalic("Создана новая служебная заявка № " . $value . ".");
                 break;
           }
        }
        return $output;
     }
     /**
      * Функция подготовки текста с данными открытой заявки
      *
      * @param  array    $data          Массив с данными заявки

      */
     protected function WriteOpenBid($data) {
        $output  = self::MakeString("Сумма, выставленная на обмен: " . $data[0] . ".");
        $output .= self::MakeString("Сумма, которую ожидается получить: " . $data[1] . ".");
        $output .= self::MakeString("Сумма, фактически выставленная на обмен: " . $data[2] . ".");
        $output .= self::MakeString("Сумма, фактически полученная: " . $data[3] . ".");
        $output .= self::MakeString("Фактическое значение курса: " . $data[4] . ".");
        return $output;
     }
     /**
      * Функция подготовки текста состояния заявки
      *
      * @param  array    $data          Массив с данными заявки
      */
     protected function WriteStateBid($data) {
        switch ($data[0]) {
           case 0:
             $output  = self::MakeString("Ранее неучтенная заявка.");
             break;
           case 10:
             $output  = self::MakeString("Опорная заявка.");
             break;
           case 20:
             $output  = self::MakeString("Ожидание позиции.");
             break;
           case 21:
             $output  = self::MakeString("Удерживание позиции.");
             break;
           case 22:
             $output  = self::MakeString("Закрытие в режиме мейкера.");
             break;
           case 23:
             $output   = self::MakeString("Разворот в режиме мейкера.");
             $output  .= self::MakeString("Количество попыток закрыть заявку: " . $data[1] . "." );
             break;
           case 24:
             $output  = self::MakeString("Закрытие в режиме тейкера.");
             break;
           case 30:
             $output  = self::MakeString("Служебная заявка.");
             break;
           default:
             $output  = self::MakeString("Закрытая заявка.");
             break;
        }
        return $output;
     }
  }
?>