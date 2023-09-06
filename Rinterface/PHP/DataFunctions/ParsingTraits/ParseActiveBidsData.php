<?php
  /**
   * ParseActiveBidsData: Трейт, определяющий функцию подготовки текста протоколов управления активными заявками 
   */
  trait ParseActiveBidsData {
     /**
      * Функция подготовки текста протоколов текста протоколов управления активными заявками 
      *
      * @param  string   $data          Содержание записи в протоколе
      *
      * @return string                  Текст протокола
      */
     private function ParseActiveBidsData($data) {
        if (is_array($data)) {
           $output = "";
           foreach ($data as $key => $value) {
              switch ($key) {
                 case "dir":
                    $output .= self::WriteBidsDirection($value);
                    break;
              }
           }
        } else {
           switch ($data) {
              case "st":
                 $output = self::MakeHead("Управление активными заявками торговца.");
                 break;
              case "nac":
                 $output = self::MakeString("Нет активных заявок.");
                 break;
              case "nch":
                 $output = self::MakeString("Заявка переведена в активное состояние после получения текущей рекомендации эксперта.");
                 $output .= self::MakeString("Изменение заявки невозможно.");
                 break;
           }
        }
        return $output;
     }
  }
?>