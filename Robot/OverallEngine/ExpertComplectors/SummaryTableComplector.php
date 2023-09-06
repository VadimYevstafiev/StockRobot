<?php
  /**
   * SummaryTableComplector: Базовый класс комплектатора сводных таблиц по типам обмена
   */
  class SummaryTableComplector extends BaseSummaryTableComplector {
     use PropertySERVICETIMESTAMP;
     /**
      * Общая функция комплектации таблицы
      *
      * @return array               Массив начальных меток времени, с которой надо дописать данные,
      *                             по типам обмена
      */
     public function Complete() {
        $this::ServiceComplete();
        return $this->timefactor;
     }
     /**
      * Служебная функция определения начальной метки времени, с которой надо дописать данные
      *
      * @param array   $input         Массив результатов валидации
      *
      * @return double                Начальная метка времени, с которой надо удалить данные и дописать новые
      */
     protected function ServiceTimefactor($input) {
        if (!$this->new) {
           for ($i = 0; $i < count($this->tabledata["indexis"]); $i++) {
              $data[$i] = parent::ServiceTimefactor($input, $this->tabledata["indexis"][$i]);
           }
           $output = min($data);
           $this->serviceTimestamp = $output - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
        } else {
           $output = $input[1];
        }
        return $output;
     }
     /**
      * Функция обработки данных
      *
      * @param integer $input[0]   Код результата поствалидации
      * @param double  $input[1]   Начальная метка времени, с которой надо дописать данные
      * @param double  $input[2]   Конечная метка времени, до которой надо дописать данные
      * @param string  $tablename  Имя таблицы
      *
      * @return array              Массив значений для записи в таблицу
      */
     protected function PrepaireData($input, $tablename) {
        $this->timefactor = $input[1];
        $result = $this::ExtractData($this::DefineTimefactor($input), 
                                     $this->tabledata["sourceArray"], 
                                     $this->tabledata["valueArray"]);
        $output = $this::СalculateData($result, $tablename);
        return $output;
     }
     /**
      * Функция извлечения данных
      *
      * @param double  $timefactor   Начальная метка времени, с которой нужно извлечь данные
      * @param array   $sourceArray  Массив имен таблиц, из которых нужно извлечь данные
      * @param array   $valueArray   Массив имен столбцов таблиц, из которых нужно извлечь данные
      *
      * @return array                Массив данных, извдеченных из таблиц
      */
     protected function ExtractData($timefactor, $sourceArray, $valueArray) { 
        for ($i = 0; $i < count($this->tabledata["indexis"]); $i++) {
           $data = parent::ExtractData($timefactor, 
                                       $sourceArray, 
                                       $valueArray,
                                       $this->tabledata["indexis"][$i]);
           $result[$i] = $data[0];
        }
        for ($i = 0; $i < count($result[0]); $i++) { 
           if ($result[0][$i][0] == $result[1][$i][0]) {
              $output[$i][0] = $result[0][$i][0];
           } else {
              throw new Exception("Данные по типам обмена не соответствуют друг другу");
           }
           $counter = 1;
           for ($j = 1; $j < count($result[0][0]); $j++) {
              for ($z = 0; $z < count($result); $z++) { 
                 $output[$i][$counter] = $result[$z][$i][$j];
                 $counter++;
              }
           }
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
     }
  }
?>