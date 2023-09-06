<?php
  /**
   * EMADataFunctions: Трейт, определяющий функции построения экспоненциальной скользящей средней
   */
  trait EMADataFunctions {
      /**
      * Функция создания начального значения экспоненциальной скользящей средней
      *
      * @param array   $input        Массив данных, по которым строится экспоненциальная скользящая средняя
      * @param integer $period       Период экспоненциальной скользящей средней
      *
      * @return double               Начальное значение экспоненциальной скользящей средней
      */
     private function newStartvalueEMA ($input, $period) {
        $output = 0;
        for ($i = 0; $i < $period; $i++) {
           $output += $input[$i];
        }
        $output = round(($output / $period), 4);
        return $output;
     }  
      /**
      * Функция построения экспоненциальной скользящей средней
      *
      * @param array   $timestamp    Массив данных, по которым строится экспоненциальная скользящая средняя
      * @param array   $input        Массив данных, по которым строится экспоненциальная скользящая средняя
      * @param integer $period       Период экспоненциальной скользящей средней
      * @param double  $startvalue   Начальное значение экспоненциальной скользящей средней
      *
      * @return array                Массив значений экспоненциальной скользящей средней
      */
     private  function createEMA ($timestamp, $input, $period, $startvalue) {
        $a = 2 / ($period + 1);
        $value = $startvalue;
        for ($i = 0; $i < count($timestamp); $i++) {
           $output[$i][0] = $timestamp[$i];
           $output[$i][1] = round(($a * $input[$i] + (1 - $a) * $value), 4);
           $value = $output[$i][1];
        }
        return $output;
     }
  }
?>