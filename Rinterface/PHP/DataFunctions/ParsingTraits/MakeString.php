<?php
  /**
   * MakeString: Трейт, определяющий функцию преобразования части текста протокола в блок HTML
   */
  trait MakeString {
     /**
      * Функция подготовки преобразования части текста протокола в блок HTML
      *
      * @param  string   $value         Часть текста в протоколе
      *
      * @return string                  Блок HTML
      */
     private function MakeString($value) {
        $output = "<p>" . $value . "</p>";
        return $output;
     }
  }
?>