
<?php

  /**
   * PropertyNEW: Трейт, определяющий свойство $new комплектаторов таблиц
   */
  trait PropertyNEW {
     /**
      * @param bool    $new                  Флаг, показывающий, заполняется ли таблица заново
      */
     protected $new;
     /**
      * Функция определения свойства $new комплектаторов таблиц
      *
      * @param integer $input[0]   Код результата поствалидации
      * @param double  $input[1]   Начальная метка времени, с которой надо дописать данные
      * @param double  $input[2]   Конечная метка времени, до которой надо дописать данные
      *
      * @return bool               Значения свойства $new
      */
     protected function SetNEW($input) {
        if ($input[1] == $this->sdate->getTimestamp()) {
           $output = TRUE;
        } else {
           $output = FALSE;
        }
        return $output;
     }
  }
?>

