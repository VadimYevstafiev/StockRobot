<?php
  /**
   * ConvertDataFunction: Трейт, определяющий служебную функцию преобразования
   * строкового представления чисел с разделителем в виде запятой
   */
  trait ConvertDataFunction {
     /**
      * Функция преобразования строкового представления чисел с разделителем в виде запятой
      *
      * @param  string $data Строковое представление числа
      *
      * @return float        Число с плавающей точкой
      */
     protected function ConvertData($data) {
        $serv = explode(",",$data);
        $output = (float) ($serv[0] . ".". $serv[1]) ;
        return $output;
     }
  }
?>