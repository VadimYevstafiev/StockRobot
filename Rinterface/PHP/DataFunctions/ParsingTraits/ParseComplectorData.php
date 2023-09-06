<?php
  /**
   * ParseComplectorData: Трейт, определяющий функцию подготовки текста протоколов 
   *                      эксперта и комплектатора интерфейса
   */
  trait ParseComplectorData {
     /**
      * Функция подготовки текста протоколов эксперта и комплектатора интерфейса 
      *
      * @param  string   $value         Содержание записи в протоколе
      *
      * @return string                  Текст протокола
      */
     private function ParseComplectorData($value) {
        switch ($value[0]) {
           case "declar":
              $output = self::ParseDeclarationData($value[1]);
              break;
           case "save":
              $output = self::MakeString("Данные на " . $value[1] . " успешно записаны.");
              break;
           case "subdec":
              $output = self::MakeString("Обновление таблицы " . $value[1] . ".");
              break;
           case "saveopt":
              $output = self::MakeString("Данные в таблицу " . $value[1] . " успешно записаны.");
              break;
           case "bj":
              $output = self::ParseBidsJournalData($value[1]);
              break;
        }
        return $output;
     }
     /**
      * Функция подготовки текста сообщения о начале комплектации
      *
      * @param  string   $data          Строка с массивом данных
      *
      * @return string                  Текст сообщения о начале комплектации
      */
     private function ParseDeclarationData($data) {
        $output = "";
        foreach ($data as $key => $value) {
           switch ($key) {
              case "defin":
                 $output .= self::MakeHead("Обновление " . $value . ".");
                 break;
              case "sdate":
                 $output .= self::MakeString("Стартовое время = " . $value . ".");
                 break;
              case "fdate":
                 $output .= self::MakeString("Финальное время = " . $value . ".");
                 break;
           }
        }
        return $output;
     }
  }
?>