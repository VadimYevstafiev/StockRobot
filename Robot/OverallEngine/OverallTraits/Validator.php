<?php
  /**
   * Validator: Трейт, определяющий функции полной проверки таблиц баз данных
   */
  trait Validator {
     use ValidatorFunctions;
     /**
      * Функция проверки таблицы
      *
      * @param  object   $protocol      Комплектатор таблицы протоколов
      * @param  string   $tablename     Имя таблицы
      * @param  integer  $criteria      Значение таймфрейма таблицы в секундах
      * @param  array    $colmames      Массив имен столбцов таблицы
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      * @param  string   $index         Тип обмена
      *
      * @return integer  $output[0]     Код результата проверки
      * @return double   $output[1]     Начальная метка времени, с которой надо дописать данные
      * @return double   $output[2]     Конечная метка времени, до которой надо дописать данные
      * @return double   $output[3]     Начальная метка времени, с которой надо дописать данные за период более ранний, чем в таблице
      * @return double   $output[4]     Конечная метка времени, до которой надо дописать данные за период более ранний, чем в таблице
      */
     protected function Validation($tablename, $criteria, $colnames, $coltypes, $keycolumn, $index) {
        $result[0] = $this::CheckTable($tablename, $colnames, $coltypes);
        if ($result[0] > 3) {
           $this->protocol->AddMessage("valid", array("res" => $result[0]));
           $data = $this::TableCreator($result[0], $tablename, $colnames, $coltypes);
           $this->protocol->AddArray("valid", $data);
        } else {
           $result = $this::CheckData($tablename, $criteria, $keycolumn);
           $this->protocol->AddMessage("valid", array("res" => $result[0]));
        }
        $output = $this::RedactionData($result, $tablename, $criteria, $keycolumn, $index);
        return $output;
     }
     /**
      * Функция проверки данных таблицы
      *
      * @param  string   $tablename     Имя таблицы
      * @param  integer  $criteria      Значение таймфрейма таблицы в секундах
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      *
      * @return array    $output[0]     Код результата второго блока валидации
      *                  $output[1]     Начальная метка времени, с которой надо дописать данные
      *                  $output[2]     Конечная метка времени, до которой надо дописать данные
      */
     protected function CheckData($tablename, $criteria, $keycolumn) {
        $timestamp = $this::GetKeycolumnArray($tablename, $keycolumn);
        if (!$timestamp) {
           //"Нет" - в таблице нет данных  
           $output[0] = 3;
        }  else {
           if (!self::CheckFirstRow($timestamp[0], $criteria)) {
              //"Нет" - временная отметка 1-й строки таблицы некорректна
              $output[0] = 2;
           } else {
              if (!self::CheckСontinuity($timestamp, $criteria)) {
                 //"Нет" - в данных таблицы есть пробелы
                 $output[0] = 1;
              } else {
                 //"Да" - пробелов в данных нет
                 $output[0] = 0;
                 $output[1] = $timestamp[0];
                 $output[2] = $timestamp[count($timestamp) - 1];
              }
           }
           unset($timestamp);
        }
        return $output;
     }
     /**
      * Функция проверки корректности временной отметки 1-й строки в таблице
      *
      * @param string  $firsttimestamp Временная отметка 1-й строки
      * @param integer $criteria       Значение таймфрейма таблицы в секундах
      *
      * @return bool                   Результат проверки
      */
     protected function CheckFirstRow($firsttimestamp, $criteria) {
        $timeOffset = (new DateTime)->setTimestamp($firsttimestamp)->getOffset();
        $value = fmod(($firsttimestamp + $timeOffset), $criteria);
        if ($value != 0) { 
           //"Нет" - временная отметка 1-й строки таблицы некорректна
           $output = FALSE;
        } else {
           $output = TRUE;
        }
        return $output;
     }
     /**
      * Функция проверки, нет ли разрывов в данных
      *
      * @param string  $timestamp      Массив временных отметок строк
      * @param integer $criteria       Значение таймфрейма таблицы в секундах
      *
      * @return bool                   Результат проверки
      */
     protected function CheckСontinuity($timestamp, $criteria) {
        $output = TRUE;
        for ($i = 1; $i <count($timestamp); $i++) {
           if (($timestamp[$i] - $timestamp[$i - 1]) != $criteria) { 
              $output = FALSE;
           }
        }
        //"Нет" - в данных таблицы есть пробелы
        //"Да" - пробелов в данных нет
        return $output;
     }
     /**
      * Функция редактирования данных таблицы
      *
      * @param  array    $input         Массив результатов валидации
      * @param  string   $tablename     Имя таблицы
      * @param  integer  $criteria      Значение таймфрейма таблицы в секундах
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      * @param  string   $index         Тип обмена
      *
      * @return integer  $output[0]     Код результата проверки
      * @return double   $output[1]     Начальная метка времени, с которой надо дописать данные
      * @return double   $output[2]     Конечная метка времени, до которой надо дописать данные
      * @return double   $output[3]     Начальная метка времени, с которой надо дописать данные за период более ранний, чем в таблице
      * @return double   $output[4]     Конечная метка времени, до которой надо дописать данные за период более ранний, чем в таблице
      */
     protected function RedactionData($input, $tablename, $criteria, $keycolumn, $index) {
        $output = $this::CheckFinishDate($input, $tablename, $criteria, $keycolumn, $index);
        $data = $this::CheckOutdated($input, $tablename, $keycolumn);
        $this->protocol->AddArray("valid", $data);
        //Проверяем, старше ли начальная дата имеющихся данных начальной даты заданного временного диапазона
        if ((!empty($input[1])) && ($input[1] > $this->sdate->getTimestamp())) {
           //Если да
           $this->protocol->AddMessage("valid", "early");
           $output[0] = 1;
           $output[3] = $this->sdate->getTimestamp();
           $output[4] = $input[1];
        }
        $this->protocol->AddMessage("valid", array("post" => $output[0]));
        return $output;
     }
     /**
      * Функция проверки конечной даты данных таблицы
      *
      * @param  array    $input         Массив результатов валидации
      * @param  string   $tablename     Имя таблицы
      * @param  integer  $criteria      Значение таймфрейма таблицы в секундах
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      * @param  string   $index         Тип обмена
      *
      * @return array                   Массив результатов проверки конечной даты данных таблицы
      *                                 "delall" - сообщение об удалении всех строк в таблице
      *                                 "delone" - сообщение об удалении данных за последний период (временного диапазона таблицы)
      *                                 "deltwo" - сообщение об удалении данных за два последних периода (заданного временного диапазона)
      */
     protected function CheckFinishDate($input, $tablename, $criteria, $keycolumn, $index) {
        $output[0] = 0;
        //Если конечная дата имеющихся данных находится вне заданного временного диапазона
        if ($input[2] <= $this->sdate->getTimestamp()) {
           if ($input[0] != 3) {
              $this->dbc->DeleteAll($tablename);
              $this->protocol->AddMessage("valid", "delall");
           }
           $output[1] = $this->sdate->getTimestamp();
           $output[2] = $this->fdate->getTimestamp();
           return $output;
        }
        //Проверяем, младше ли конечная дата имеющихся данных конечной даты заданного временного диапазона - 1 период
        if ($input[2] < $this->fdate->getTimestamp()) {
           //Если младше
           $this->protocol->AddMessage("valid", "delone");
           $value = $input[2];
        } else {
           $this->protocol->AddMessage("valid", "deltwo");
           $value = $this->fdate->getTimestamp() - $criteria;
        }
        $this->dbc->DeleteRow($tablename, $keycolumn, ">=", $value);
        $output[1] = $value;
        $output[2] = $this->fdate->getTimestamp();
        return $output;
     }
  }
?>