<?php
  /**
   * ParseStartData: Трейт, определяющий функцию подготовки текста стартовой записи протокола
   */
  trait ParseStartData {
     /**
      * Функция подготовки текста стартовой записи протокола 
      *
      * @param  string   $id            Идентификатор типа протокола
      * @param  string   $value         Содержание стартовой записи протокола
      *
      * @return string                  Текст стартовой записи протокола
      */
     private function ParseStartData($id, $value) {
        $output = "Обновление данных ";
        switch ($id) {
           case "listener":
              $output .= "слушателя";
              break;
           case "expert":
              $output .= "эксперта";
              break;
           case "trader":
              $output = "Запуск процедуры торгов";
              break;
           case "interface":
              $output .= "комплектатора интерфейса";
              break;
        }
        $output .= ". Стартовое время: " . $value;
        $output = self::MakeHead($output);
        return $output;
     }
  }
?>