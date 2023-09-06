<?php
  /**
   * TableDataConfigurations: Трейт, определяющий функции создания массива конфигурации таблиц данных эксперта и их обработчиков
   */
  trait TableDataConfigurations {
     /**
      * Функция создания массива конфигурации таблиц данных эксперта и их обработчиков
      *
      * @param array   $tables      Исходный массив конфигурации таблиц данных эксперта и их обработчиков
      * @param array   $timeframes  Массив таймфреймов
      * @param array   $indexis     Массив типов обмена
      *
      * @return array               Массив конфигурации таблиц баз данных и их обработчиков
      */
     private function CreateTableData ($tables, $timeframes, $indexis) {
        $output = array();
        foreach ($tables as $key => $value) {
           foreach ($value as $data) {
              for ($i = 0; $i < count($timeframes); $i++){
                 $result = self::Select($data, $timeframes[$i], $indexis, $timeframes);
                 $output[$key][$data["Type"]][$timeframes[$i]] = $result->Complete();
              }
           }
        }
        return $output;
     }
     /**
      * Функция выбора конструктора конфигурации таблиц
      *
      * @param array   $table          Исходный массив конфигурации
      * @param integer $timeframe      Таймфрейм
      * @param array   $indexis        Массив типов обмена
      * @param array   $timeframes     Массив таймфреймов
      *
      * @return object                 Конструктор конфигурации таблицы
      */
     private function Select ($table, $timeframe, $indexis, $timeframes) {
        switch ($table["Type"]) {
           case "RateData":
              $output = new RateDataTableConfigurator($table, $timeframe, $indexis);
              break;
           case "impSAR":
              $output = new impSARTableConfigurator($table, $timeframe, $indexis);
              break;
           case "ADX":
              $output = new ADXTableConfigurator($table, $timeframe, $indexis);
              break;
           case "EMA":
              $output = new EMATableConfigurator($table, $timeframe, $indexis);
              break;
           case "Convert":
              $output = new ConvertTableConfigurator($table, $timeframe, $indexis);
              break;
           case "Stochastic":
              $output = new StochasticTableConfigurator($table, $timeframe, $indexis);
              break;
           case "ASI":
              $output = new ASITableConfigurator($table, $timeframe, $indexis);
              break;
           case "PrimarySignals":
              $output = new PrimarySignalsTableConfigurator($table, $timeframe, $indexis);
              break;
           case "SummaryData":
              $output = new SummaryDataTableConfigurator($table, $timeframe, $indexis, $timeframes);
              break;
           case "SummaryDivergence":
              $output = new SummaryDivergenceTableConfigurator($table, $timeframe, $indexis, $timeframes);
              break;
           case "SummarySignals":
              $output = new SummarySignalsTableConfigurator($table, $timeframe, $indexis, $timeframes);
              break;
           case "SummaryConverts":
              $output = new SummaryConvertsTableConfigurator($table, $timeframe, $indexis, $timeframes);
              break;
           case "Results":
              $output = new ResultsTableConfigurator($table, $timeframe, $indexis);
              break;
        }
        return $output;
     }
  }
?>