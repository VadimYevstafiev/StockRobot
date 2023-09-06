<?php
  /**
   * BidsDataTableComplector: Производный класс комплектатора таблиц заявок
   */
  class BidsDataTableComplector extends DerivedDataTableComplector {
     use PropertyNEW;
     /**
      * Общая функция комплектации таблицы
      */
     public function Complete() {
        $this::ServiceComplete();
     }
     /**
      * Функция обработки данных
      *
      * @param  integer  $input[0]      Код результата поствалидации
      * @param  double   $input[1]      Начальная метка времени, с которой надо дописать данные
      * @param  double   $input[2]      Конечная метка времени, до которой надо дописать данные
      * @param  string   $tablename     Имя таблицы
      *
      * @return array                   Массив значений для записи в таблицу
      */
     protected function PrepaireData($input, $tablename) {
        $this->new = $this::SetNEW($input);
        $source = Configurations::GetJournalConfiguration("bids");
        $output[0][0] = $input[2] - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
        $query = SelectQuery::Create($source["Tablename"]);
        $query->AddWHERE("Closetime", ">=", $output[0][0]);
        $result = $this::ExtractData($query, $source["columnName"]);
        if ($result) {
           $output[0][1] = "null";
           $output[0][2] = "null";
           $output[0][3] = $input[1][1][$i][0];
        } else {
           $query = SelectQuery::Create($source["Tablename"]);
           $query->AddWHERE("State", ">=", 22);
           $query->AddAND("State <", 30);
           $result = $this::ExtractData($query, $source["columnName"]);
        }
  echo '<p> $result</p>';
  print_r($result);

        //return $output;
     }
     /**
      * Функция извлечения данных
      *
      * @return string   $query         Строка запроса
      * @param  array    $columns       Массив имен столбцов таблицы журнала заявок
      *
      * @return array                   Массив данных заявки
      */
     protected function ExtractData($query, $columns) { 
        $data = $this->dbc->SendQuery($query);
        if (!empty($data)) {
           for ($i = 0; $i < count($columns); $i++) {
              $output[$columns[$i]] = $data[0][$i];
           }
        } else {
           $output = FALSE;
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
  echo '<p> $data</p>';
  print_r($data);
        //return $output;
     }
  }
?>