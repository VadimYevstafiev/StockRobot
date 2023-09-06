
<?php

  /**
   * EMADataTableComplector: Производный класс комплектатора таблиц экспоненциальной скользящей средней
   */
  class EMADataTableComplector extends DerivedDataTableComplector {
     use PropertyNEW, PropertySERVICETIMESTAMP, EMADataFunctions;
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
        if ($this->new) {
           $output += - $this->tabledata["Timeframe"] * $this->tabledata["period"] * $this->tabledata["timemodule"];
        } else {
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
        $data = $input[0];
        unset($input);
        for ($i = 0; $i < count($data); $i++) {
           $timestamp[$i] = $data[$i][0];
           $value[$i] = $data[$i][1];
        }
        if ($this->new) {
           $startvalue = $this::newStartvalueEMA($value, $this->tabledata["period"]);
           $timestamp = array_slice ($timestamp, $this->tabledata["period"]);
           $value = array_slice ($value, $this->tabledata["period"]);
        } else {
           $startvalue = $this->ExtractRow($tablename);
           $startvalue = $startvalue["value"];
        }   
        $output = $this::createEMA($timestamp, $value, $this->tabledata["period"], $startvalue);
        return $output;
     }
  }
?>

