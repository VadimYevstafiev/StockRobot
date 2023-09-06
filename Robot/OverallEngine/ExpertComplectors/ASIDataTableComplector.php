<?php
  /**
   * ASIDataTableComplector: Производный класс комплектатора таблиц Кумулятивного индекса колебаний (ADX)
   */
  class ASIDataTableComplector extends DerivedDataTableComplector {
     use PropertyNEW, PropertySERVICETIMESTAMP;
     /**
      * Функция определения начальной метки времени, с которой надо дописать данные
      * Дополнительно определяет значения свойств $new и $serviceTimestamp
      *
      * @param integer $input[0]   Код результата поствалидации
      * @param double  $input[1]   Начальная метка времени, с которой надо дописать данные
      * @param double  $input[2]   Конечная метка времени, до которой надо дописать данные
      *
      * @return double             Начальная метка времени, с которой надо дописать данные
      */
     protected function DefineTimefactor($input) {
        $this->new = $this::SetNEW($input);
        $output = $input[1];
        if ($this->new) {
           $output += - $this->tabledata["Timeframe"] * $this->tabledata["serviceperiod"] * $this->tabledata["timemodule"];
        }else {
           $output += - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
           $this->serviceTimestamp = $input[1] - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
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
        $j = 0;
        $data = $input[0];
        unset($input);
        if ($this->new) {
           $ASI[-1] = 0;
        } else {
           $value = $this->ExtractRow($tablename);
           $ASI[-1] = $value["ASI"];
        }
        for ($i = 1; $i < count($data); $i++) {
           $output[$i - 1][0] = $data[$i][0];
           $K = round(max(abs($data[$i][4] - $data[$i - 1][2]), abs($data[$i][3] - $data[$i - 1][2])), 4);

           $x0 = round(max(($data[$i][4] - $data[$i][3]), abs($data[$i][4] - $data[$i - 1][2]), abs($data[$i][3] - $data[$i - 1][2])), 4);
           $x1 = round(abs($data[$i][4] - $data[$i - 1][2]), 4);
           $x2 = round(abs($data[$i][3] - $data[$i - 1][2]), 4);
           $x3 = round(($data[$i][4] - $data[$i][3]), 4);
           $x4 = round(abs($data[$i - 1][2] - $data[$i - 1][1]), 4);

           switch ($x0) {
              case $x3:
                 $R = $x3 + 0.25 * $x4; 
                 break;
              case $x1:
                 $R = $x1 - 0.5 * $x2 + 0.25 * $x4;
                 break;
              case $x2:
                 $R = $x2 - 0.5 * $x1 + 0.25 * $x4;
                 break;
           } // end switch

           $N = round(($data[$i][2] - $data[$i - 1][2]), 4) + round(0.5 * ($data[$i][2] - $data[$i][1]), 4) + round(0.25 * ($data[$i - 1][2] - $data[$i - 1][1]), 4);
           $SI = round(50 * $N * $K / ($R * $this->tabledata["Limit"]), 2);
           $ASI[$j] = round(($ASI[$j - 1] + $SI), 2);
           $output[$j][1] = $ASI[$j];
           $j++;
        }
        return $output;
     }
  }
?>