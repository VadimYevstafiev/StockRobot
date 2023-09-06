<?php
  /**
   * ConvertDataTableComplector: Производный класс комплектатора таблиц конверта
   */
  class ConvertDataTableComplector extends DerivedDataTableComplector {
     use PropertyNEW;
     /**
      * Функция определения начальной метки времени, с которой надо дописать данные
      * Дополнительно определяет значения свойств $new и $serviceTimestamp
      *
      * @param array $input         Массив результатов валидации
      *
      * @return double              Начальная метка времени, с которой надо дописать данные
      */
     protected function DefineTimefactor($input) {
        $this->new = $this::SetNEW($input);
        $output = $input[1];
        if (!$this->new) {
           $output += - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
        }
        return $output;
     }
     /**
      * Вспомогательная функция обработки данных
      *
      * @param array   $input      Массив извлеченных данных
      * @param string  $tablename  Имя таблицы
      *
      * @return array              Массив значений для записи в таблицу
      */
     protected function СalculateData($input, $tablename) { 
        if ($this->new) {
           $j = 1;
        } else {
           $j = 0;
        }
        for ($i = 0; $i < count($input[0]); $i++) {
           $output[$i + $j][0] = $input[0][$i][0] + $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
           $output[$i + $j][1] = round(($input[0][$i][1] + $input[1][$i][0]), 4);
           $output[$i + $j][2] = round(($input[0][$i][1] - $input[1][$i][0]), 4);  
           $output[$i + $j][3] = $input[0][$i][1]; 
        }
        if ($this->new) {
           $output[0][0] = $input[0][0][0];
           $output[0][1] = $output[1][1];
           $output[0][2] = $output[1][2];
           $output[0][3] = $output[1][3];
        }
        return $output;
     }
  }
?>

