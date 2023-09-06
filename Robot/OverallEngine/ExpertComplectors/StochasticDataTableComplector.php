<?php
  /**
   * StochasticDataTableComplector: Производный класс комплектатора таблиц стохастического осциллятора
   */
  class StochasticDataTableComplector extends DerivedDataTableComplector {
     use PropertyNEW, PropertySERVICETIMESTAMP, EMADataFunctions;
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
        } else {
           $output += - ($this->tabledata["periodKfast"] - 1) * $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
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
        $z = 0;
        $start = $this->tabledata["periodKfast"] - 1;
        for ($i = $start; $i < count($data); $i++) {
           $min = $data[$z][2];
           $max = $data[$z][3];
           for ($j = 1; $j < $this->tabledata["periodKfast"]; $j++) {
              $k = $z + $j;
              if ($data[$k][2] < $min) {
                 $min = $data[$k][2];
              }
              if ($data[$k][3] > $max) {
                 $max = $data[$k][3];
              }
           }
           $timestamp[$z] = $data[$i][0];
           $Kfast[$z] = round((100 * ($data[$i][1] - $min) / ($max - $min)), 4);
           $z++;
        }
        if ($this->new) {
           $timestamp = array_slice ($timestamp, $this->tabledata["periodKfull"]);
           $startvalue = $this::newStartvalueEMA($Kfast, $this->tabledata["periodKfull"]);
           $Kfast = array_slice ($Kfast, $this->tabledata["periodKfull"]);
        } else {
           $previousRow = $this->ExtractRow($tablename);
           $startvalue = $previousRow["Kfull"];
        }
        $service = $this::createEMA($timestamp, $Kfast, $this->tabledata["periodKfull"], $startvalue);
        for ($i = 0; $i < count($timestamp); $i++) {
           $Kfull[$i] = $service[$i][1];
        }
        if ($this->new) {
           $timestamp = array_slice ($timestamp, $this->tabledata["periodDfull"]);
           $startvalue = $this::newStartvalueEMA($Kfull, $this->tabledata["periodDfull"]);
           $Kfull = array_slice ($Kfull, $this->tabledata["periodDfull"]);
        } else {
           $startvalue = $previousRow["Dfull"];
        }
        $service = $this::createEMA($timestamp, $Kfull, $this->tabledata["periodDfull"], $startvalue);
        for ($i = 0; $i < count($timestamp); $i++) {
           $Dfull[$i] = $service[$i][1];
           if ($Dfull[$i] < $Kfull[$i]) {
              $position[$i] = 1;
           } else {
              $position[$i] = - 1;
           } 
        }
        for ($i = 0; $i < count($timestamp); $i++) {
           $output[$i][0] = $timestamp[$i];
           $output[$i][1] = $Kfast[$i];
           $output[$i][2] = $Kfull[$i];
           $output[$i][3] = $Dfull[$i];
           $output[$i][4] = $position[$i];
        }
        return $output;
     }
  }
?>