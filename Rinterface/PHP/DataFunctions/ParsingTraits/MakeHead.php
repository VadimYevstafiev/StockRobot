<?php
  /**
   * MakeHead: Трейт, определяющий функцию преобразования части текста протокола в заголовок HTML
   */
  trait MakeHead {
     /**
      * Функция подготовки преобразования части текста протокола в заголовок HTML
      *
      * @param  string   $value         Часть текста в протоколе
      *
      * @return string                  Заголовок HTML
      */
     private function MakeHead($value) {
        $output = "<h3>" . $value . "</h3>";
        return $output;
     }
  }
?>