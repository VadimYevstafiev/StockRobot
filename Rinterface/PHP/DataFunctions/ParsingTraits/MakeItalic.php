<?php
  /**
   * MakeItalic: Трейт, определяющий функцию преобразования части текста протокола в заголовок HTML с жирным курсивом
   */
  trait MakeItalic {
     /**
      * Функция подготовки преобразования части текста протокола в заголовок HTML с жирным курсивом
      *
      * @param  string   $value         Часть текста в протоколе
      *
      * @return string                  Заголовок HTML
      */
     private function MakeItalic($value) {
        $output = "<h4><i>" . $value . "</i></h4>";
        return $output;
     }
  }
?>