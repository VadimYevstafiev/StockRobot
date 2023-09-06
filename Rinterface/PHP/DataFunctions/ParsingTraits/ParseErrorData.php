<?php
  /**
   * ParseErrorData: Трейт, определяющий функцию подготовки текста сообщения об ошибке
   */
  trait ParseErrorData {
     /**
      * Функция подготовки текста сообщения об ошибке
      *
      * @param  string   $id            Идентификатор типа протокола
      * @param  string   $value         Содержание сообщения об ошибке
      *
      * @return string                  Текст сообщения об ошибке
      */
     private function ParseErrorData($id, $value) {
        $output = "Ошибка при выполнении обновления данных ";
        switch ($id) {
           case "listener":
              $output .= "слушателя.";
              break;
           case "expert":
              $output .= "эксперта.";
              break;
           case "trader":
              $output = "Ошибка при выполнении процедуры торгов.";
              break;
           case "interface":
              $output .= "комплектатора интерфейса.";
              break;
        }
        $output = self::MakeHead($output);
        $output .= $value;
        return $output;
     }
  }
?>