<?php
  /**
   * ParseListenerData: Трейт, определяющий функцию подготовки текста протокола слушателя
   */
  trait ParseListenerData {
     /**
      * Функция подготовки текста протокола слушателя 
      *
      * @param  string   $value         Содержание записи в протоколе
      *
      * @return string                  Текст протокола
      */
     private function ParseListenerData($value) {
        switch ($value) {
           case "data":
              $output = self::MakeString("Получение данных.");
              break;
           case "get":
              $output = self::MakeString("Данные успешно получены.");
              break;
           case "prep":
              $output = self::MakeString("Данные успешно преобразованы.");
              break;
           case "save":
              $output = self::MakeString("Данные успешно записаны.");
              break;
        }
        return $output;
     }
  }
?>