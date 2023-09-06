<?php
  /**
   * JournalComplectorFunctions: Трейт, определяющий функции базового класса комплектатора журналов
   */
  trait JournalComplectorFunctions {
     use ValidatorFunctions;
     /**
      * Функция проверки таблицы журналов
      *
      * @param  string   $tablename     Имя таблицы
      * @param  array    $colnames      Массив имен столбцов таблицы
      * @param  array    $coltypes      Массив типов данных в столбцах таблицы
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      *
      * @return bool                    Возвращает FALSE (если таблица только создана или пустая)
      * @return array                   или массив значений в столбце таблицы - ключе
      */
     protected function Validation($tablename, $colnames, $coltypes, $keycolumn) {
        $result[0] = $this::CheckTable($tablename, $colnames, $coltypes);
        if ($result[0] > 3) {
           $this::TableCreator($result[0], $tablename, $colnames, $coltypes);
           $this->keyarray = FALSE;
        } else {
           $this->keyarray = $this::CheckData($tablename, $keycolumn);
        }
     }
     /**
      * Функция проверки данных в таблице
      *
      * @param  string   $tablename     Имя таблицы
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      *
      * @return bool                    Возвращает FALSE (если таблица пустая)
      * @return array                   или массив значений в столбце таблицы - ключе
      */
     protected function CheckData($tablename, $keycolumn) {
        //Проверяем, больше ли количество строк в журнале установленного предела
        $output = $this::GetKeycolumnArray($tablename, $keycolumn);
        if (!$output) {
           return $output;
        }
        $output = array_reverse($output);
        if ($this->tabledata["countRow"] < count($output)) {
           //Если да
           //Часть данных в таблице устарела и может быть удалена
           //Удаляем устаревшие данные
           $output = array_slice($output, 0, $this->tabledata["countRow"]);
           $this->dbc->DeleteRow($tablename, $keycolumn, "<=", $output[count($output) - 1]);
        }
        return $output;
     }
     /**
      * Функция извлечения данных журнала 
      *
      * @param  resource $dbc           Идентификатор соединения
      * @param  string   $input         Значение в столбце таблицы - ключе
      *                                 (в случае извлечения данных конкретной строки)
      *
      * @return array                   Массив извлеченных значений 
      */
     public function GetData($dbc, $input) {
        $query = SelectQuery::Create($this->tabledata["Tablename"]); 
        if (!empty($input)) {
           $query->AddWHERE($this->tabledata["keyColumn"], "=", $input);
        }
        $query->AddORDER($this->tabledata["keyColumn"], TRUE);
        $data = $dbc->SendQuery($query);
        for ($i = 0; $i < count($data); $i++) {
           for ($j = 0; $j < count($this->tabledata["columnName"]); $j++) {
              $output[$i][$this->tabledata["columnName"][$j]] = $data[$i][$j];
           }
        }
        return $output;
     }
     /**
      * Функция добавления записи в журнал
      *
      * @param  array    $input         Массив записываемых значений
      *
      * @return string                  Результат выполнения процедуры
      */
     public function AddScript($input) {
        $data = $this::PrepaireScriptData($input);
        $bool = $this->dbc->AddRow($this->tabledata["Tablename"], $data[0], $data[1]);
        if (!$bool) {
           $message = "Не удалось записать данные " . $this->name . "и № " . $input[$this->tabledata["keyColumn"]] . ".";
           throw new Exception($message); 
        } else {
           $output = array("save" => $input[$this->tabledata["keyColumn"]]);
        }
        return $output;
     }
     /**
      * Функция редактирования записи в журнале
      *
      * @param  string   $bidID         Номер заявки, запись которой редактируется
      * @param  array    $input         Ассоциативный массив записываемых значений
      *
      * @return string                  Результат выполнения процедуры
      */
     public function CorrectScript($bidID, $input) {
        $data = $this::PrepaireScriptData($input);
        $bool = $this->dbc->CorrectRow($this->tabledata["Tablename"], $data[0], $data[1], $this->tabledata["keyColumn"], "=", $bidID);
        if (!$bool) {
           $message = "Не удалось отредактировать данные " . $this->name . "и № " . $bidID . ".";
           throw new Exception($message);
        } else {
           $output = array("cor" => $bidID);
        }
        return $output;
     }
     /**
      * Функция удаления записи в журнале
      *
      * @param  string   $bidID         Номер заявки, запись которой удаляется
      * @param  string   $result        Результат выполнения дочерней процедуры
      *
      * @return string                  Результат выполнения процедуры
      */
     public function DeleteScript($bidID, $result) {
        if (!$result) {
           $message = "Не удалось удалить данные " . $this->name . "и № " . $bidID . ".";
           throw new Exception($message); 
        } else {
           $output = array("del" => $bidID);
        }
        return $output;
     }
     /**
      * Функция подготовки данных для записи в журнал
      *
      * @param  array    $input         Ассоциативный массив записываемых значений
      *
      * @return array                   Данные для записи
      */
     protected function PrepaireScriptData ($input) {
        $output[0] = array();
        $output[1] = array();
        foreach ($input as $key => $value) {
           $output[0][] = $key;
           $output[1][] = $value;
        }
        return $output;
     }
  }
?>

