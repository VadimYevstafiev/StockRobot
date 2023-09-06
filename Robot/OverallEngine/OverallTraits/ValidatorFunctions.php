<?php
  /**
   * ValidatorFunctions: Трейт, определяющий служебные функции проверки таблиц баз данных
   */
  trait ValidatorFunctions {
     /**
      * Функция упрощенной проверки таблиц баз данных
      *
      * @param  string   $tablename     Имя таблицы
      * @param  array    $colnames      Массив имен столбцов таблицы
      * @param  array    $coltypes      Массив типов данных в столбцах таблицы
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      *
      * @return array                   Массив результатов редактирования
      */
     protected function Validation($tablename, $colnames, $coltypes, $keycolumn) {
        $result[0] = $this::CheckTable($tablename, $colnames, $coltypes);
        if ($result[0] > 3) {
           $output = $this::TableCreator($result[0], $tablename, $colnames, $coltypes);
        } else {
           $result = $this::CheckData($tablename, $keycolumn);
        }
        $output = $this::CheckOutdated($result, $tablename, $keycolumn);
        return $output;
     }
     /**
      * Функция проверки таблиц
      *
      * @param  string   $tablename      Имя таблицы
      * @param  array    $colnames       Массив имен столбцов таблицы
      * @param  array    $coltypes       Массив типов данных в столбцах таблицы
      *
      * @return integer                  Код результата проверки таблиц
      */
     protected function CheckTable($tablename, $colnames, $coltypes) {
        $output = 0;
        $query[0] = ShowTablesQuery::Create(); //Запрос проверки, содержит ли база данных какие-либо таблицы
        $query[1] = ShowColumnsQuery::Create($tablename); //Запрос проверки, соответствует ли список колонок в таблице заданному
        for ($i = 0; $i < count($query); $i++) {
           $data = $this->dbc->SendQuery($query[$i]);
           if (count($data) == 0) {
              //"0" - база данных пуста (для $i = 0)
              //"0" - в таблице нет колонок (для $i = 1)
              $output = 7 - $i * 2;
              //$output = 7 (для $i = 0)
              //$output = 5 (для $i = 1)
              return $output;
           }
           switch ($i) {
              case 0:
                 $bool = FALSE;
                 foreach ($data as $row) {
                    if ($row[0] == $tablename) {
                       $bool = TRUE;
                    }
                 }
                 break;
              case 1:
                 $bool = TRUE;
                 if ((count($data) != count($colnames)) || (count($data) != count($coltypes))) {
                    $bool = FALSE;
                 } else {
                    for ($j = 0; $j < count($colnames); $j++) {
                       if ($colnames[$j] != $data[$j][0]) {
                          $bool = FALSE; 
                       }
                    }
                    for ($j = 0; $j < count($coltypes); $j++) {
                       if (strncasecmp($coltypes[$j], $data[$j][1], strlen($coltypes[$j])) != 0) {
                          $bool = FALSE; 
                       }
                    }

                 }
                 break;
           }

           if (!$bool) {
              //"Нет" - таблица не существует (для $i = 0)
              //"Нет" - список колонок в таблице не соответствует заданному (для $i = 1)
              $output = 6 - $i * 2;
              //$output = 6 (для $i = 0)
              //$output = 4 (для $i = 1)
              return $output;
           }
        }
        return $output;
     }
     /**
      * Функция проверки данных в таблице
      *
      * @param  string   $tablename      Имя таблицы
      * @param  string   $keycolumn      Имя столбца таблицы - ключа
      *
      * @return array    $output[0]     Код результата проверки данных в таблицу
      *                  $output[1]     Начальная метка времени, с которой надо дописать данные
      *                  $output[2]     Конечная метка времени, до которой надо дописать данные
      */
     protected function CheckData($tablename, $keycolumn) {
        $timestamp = $this::GetKeycolumnArray($tablename, $keycolumn);
        if (!$timestamp) {
           //"Нет" - в таблице нет данных  
           $output[0] = 3;
        }  else {
           $output[0] = 0;
           $output[1] = $timestamp[0];
           $output[2] = $timestamp[count($timestamp) - 1];
           unset($timestamp);
        }
        return $output;
     }
     /**
      * Функция получения массива данных в таблице - ключе
      *
      * @param  string   $tablename     Имя таблицы
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      *
      * @return bool                    Возвращает FALSE (если таблица пустая) или массив в столбце таблицы - ключе
      */
     protected function GetKeycolumnArray($tablename, $keycolumn) {
        $query = SelectQuery::Create($tablename, array($keycolumn)); //Запрос проверки сплошности данных
        $query->AddORDER($keycolumn); 
        $data = $this->dbc->SendQuery($query);
        if (count($data) == 0) {
           //"0" - в таблице нет данных  
           $output = FALSE;
        } else {
           //Извлекаем элементы $key из таблицы
           $i = 0;
           foreach ($data as $row) {
              $output[$i] = $row[0];
              $i++;
           }
        }
        return $output;
     }
     /**
      * Функция блока создания и удаления таблиц
      *
      * @param  array    $input         Массив результатов валидации
      * @param  string   $tablename     Имя таблицы
      * @param  array    $colnames      Массив имен столбцов таблицы
      * @param  array    $coltypes      Массив типов данных в столбцах таблицы
      *
      * @return array                   Код результата исполнения блока 
      *                                 "deltab" - сообщение об удалении существующей таблицы
      *                                 "cretab" - сообщение о создании новой таблицы
      */
     protected function TableCreator($input, $tablename, $colnames, $coltypes) {
        $output = array();
        if ($input < 6) {
           //Удаляем существующую таблицу
           $this->dbc->DeleteTable($tablename);
           $output[] = "deltab";
        }
        //Создаем таблицу
        $this->dbc->CreateTable($tablename, $colnames, $coltypes);
        $output[] = "cretab";
        return $output;
     }
     /**
      * Функция проверки таблицы на наличие устаревших данных
      *
      * @param  array    $input         Массив результатов валидации
      * @param  string   $tablename     Имя таблицы
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      *
      * @return array                   Массив результатов проверки
      *                                 "delout" - сообщение об удалении участи данных
      */
     protected function CheckOutdated($input, $tablename, $keycolumn) {
        //Проверяем, младше ли начальная дата имеющихся данных начальной даты заданного временного диапазона
        if ((!empty($input[1])) && ($input[1] < $this->sdate->getTimestamp())) {
           //Если да
           //Часть данных в таблице устарела и может быть удалена
           //Удаляем устаревшие данные
           $this->dbc->DeleteRow($tablename, $keycolumn, "<", $this->sdate->getTimestamp());
           $output[0] = "delout";
        }
        return $output;
     }
  }
?>