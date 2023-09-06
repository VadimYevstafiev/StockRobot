<?php
  /**
   * ParseWMbids: Трейт, определяющий функцию подготовки текста протоколов управления заявками 
   */
  trait ParseWMbids {
     /**
      * Функция подготовки текста протоколов текста протоколов управления заявками
      *
      * @param  string   $data          Содержание записи в протоколе
      *
      * @return string                  Текст протокола
      */
     private function ParseWMbids($data) {
        if (is_array($data)) {
           $output = "";
           foreach ($data as $key => $value) {
              switch ($key) {
                 case "isu":
                    $output .= self::MakeString("Заявка № " . $value[0] . " успешно присоединена к заявке № " . $value[1] . ".");
                    break;
                 case "nu":
                    $output .= self::MakeString("Не удалось присоединить заявку № " . $value[0] . " к заявке № " . $value[1] . ".");
                    break;
                 case "div":
                    $output .= self::MakeItalic("Заявка № " . $value . " успешно разделена.");
                    break;
                 case "ndiv":
                    switch ($value[0]) {
                       case "bs":
                          $service = "опор";
                          break;
                       case "sr":
                          $service = "служеб";
                          break;
                    }
                    $output .= self::MakeString("В заявке № " . $value[1] . " недостаточно средств, чтобы создать новую " . $service . "ную заявку.");
                    break;
                 case "fdiv":
                    $output .= self::MakeString("Не удалось разделить заявку № " . $value . ".");
                    break;
                 case "del":
                    $output .= self::MakeItalic("Заявка № " . $value . " успешно удалена.");
                    break;
                 case "ndel":
                    $output .= self::MakeString("Не удалось удалить заявку № " . $value . ".");
                    break;
                 case "emb":
                    $output .= self::MakeItalic("Проверяем сумму в опорной заявке.");
                    switch ($value) {
                       case 0:
                          $output .= self::MakeString("Сумма в опорной заявке минимальная.");
                          break;
                       case 1:
                          $output .= self::MakeString("Сумма в опорной заявке больше минимальной.");
                          $output .= self::MakeString("Разделяем опорную заявку и создаем новую.");
                          break;
                    }
                    break;
                 case "mac":
                    $output .= self::MakeString("Остаток переопределяем как новую активную заявку.");
                    $output .= self::MakeItalic("Заявка № " . $value . " переопределена как активная.");
                    break;
                 case "chr":
                    $output .= self::MakeItalic("Изменяем курс заявки.");
                    $output .= self::MakeString("Рекомендованное значение курса = " . $value . ".");
                    break;
                 case "pcr":
                    $output .= self::MakeItalic("Предварительно установливаем курс = " . $value . ".");
                    break;
                 case "fchr":
                    $output .= self::MakeString("Не удалось изменить курс заявки № " . $value . ".");
                    break;
                 case "ichr":
                    $output .= self::MakeString("Курс заявки № " . $value . " успешно изменен.");
                    break;
                 case "mcr":
                    $output .= self::MakeItalic("Окончательно установливаем курс = " . $value . ".");
                    break;
                 case "ald":
                    $output .= self::MakeItalic("Скупка встречной заявки № " . $value[0] . ".");
                    $output .= self::MakeString("Дата последнего изменения во встречной заявке: " . $value[1] . ".");
                    $output .= self::MakeString("Сумма, которую осталось обменять во встречной заявке: " . $value[2] . ".");
                    $output .= self::MakeString("Сумма, которую хочет получить после обмена респондент выставивший заявку: " . $value[3] . ".");
                    break;
                 case "iba":
                    $output .= self::MakeString("Операция скупки встречной заявки № " . $value . " выполнена успешно.");
                    break;
                 case "nba":
                    $output .= self::MakeString("Операция скупки встречной заявки № " . $value . " выполнена неудачно.");
                    break;
                 case "rst":
                    $output .= self::MakeString("Остаток в заявке № " . $value[0] . " = " . $value[1] . ".");
                    break;
                 case "err":
                    $output .= self::MakeString("Код ошибки: " . $value[0] . ". Описание ошибки: ");
                    $output .= self::MakeString($value[1]);
                    break;
              }
           }
        } else {
           switch ($data) {
              case "ift":
                 $output = self::MakeString("Заявка занимает первое место в списке заявок.");
                 $output .= self::MakeString("Изменения курса не требуется.");
                 break;
              case "nft":
                 $output = self::MakeString("Заявка не занимает первое место в списке заявок.");
                 break;
              case "ncr":
                 $output = self::MakeString("Новое значение курса равно текущему.");
                 $output .= self::MakeString("Заявка остается без изменений.");
                 break;
              case "rat":
                 $output = self::MakeString("Для изменения курса текущее значение должно быть изменено более чем на 1 %.");
                 $output .= self::MakeString("Изменение будет производиться двумя последовательными шагами.");
                 break;
              case "mac":
                 $output = self::MakeString("Обновляем свою заявку путем изменения ее курса.");
                 break;
           }
        }
        return $output;
     }
  }
?>