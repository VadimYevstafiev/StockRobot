<?php
  /**
   * BaseSummaryTableComplector: Базовый класс комплектатора сводных таблиц
   */
  class BaseSummaryTableComplector extends DerivedDataTableComplector {
     use PropertyNEW;
     /**
      * Функция проверки конечной даты данных таблицы
      *
      * @param  array    $input         Массив результатов валидации
      * @param  string   $tablename     Имя таблицы
      * @param  integer  $criteria      Значение таймфрейма таблицы в секундах
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      * @param  integer  $index         Тип обмена
      *
      * @return array                   Массив результатов проверки конечной даты данных таблицы
      *                                 "delall" - сообщение об удалении всех строк в таблице
      *                                 "delone" - сообщение об удалении данных за последний период (временного диапазона таблицы)
      *                                 "deltwo" - сообщение об удалении данных за два последних периода (заданного временного диапазона)
      *                                 "delkey" - сообщение об удалении данных, начиная с уточненной начальной метки времени
      */
     protected function CheckFinishDate($input, $tablename, $criteria, $keycolumn, $index) {
        $input = parent::CheckFinishDate($input, $tablename, $criteria, $keycolumn, $index);
        $output[0] = $input[0];
        $this->new = $this::SetNEW($input);
        $result = $this::ServiceTimefactor($input, $index);
        if ($input[1] > $result) {
           if (!$this->new) {
              $this->dbc->DeleteRow($tablename, $keycolumn, ">=", $result);
              $this->protocol->AddMessage("valid", "delkey");
           }
           $output[1] = $result;
        } else {
           $output[1] = $input[1];
        } 
        $output[2] = $this->fdate->getTimestamp();
        return $output;
     }
     /**
      * Служебная функция уточнения начальной метки времени, с которой надо дописать данные
      *
      * @param  array    $input         Массив результатов валидации
      * @param  integer  $index         Тип обмена
      *
      * @return double                  Начальная метка времени, с которой надо удалить данные и дописать новые
      */
     protected function ServiceTimefactor($input, $index) {
        if (!$this->new) {
           for ($i = 0; $i < count($this->tabledata["keyType"]); $i++) {
              $data[$i] = $this::FindStartPoint($input[1], 
                                                $this::DeterminateTablename($this->tabledata["keyType"][$i], $index), 
                                                $this->tabledata["keyValue"][$i]);
           }
           $output = min($data);
        } else {
           $output = $input[1];
        }
        return $output;
     }
     /**
      * Служебная функция определения начальной метки времени, с которой надо дописать данные
      *
      * @param  integer  $timefactor    Начальная метка времени, с которой надо удалить данные и дописать новые
      * @param  string   $tablename     Имя таблицы
      * @param  array    $valueArray    Массив имен столбцов, из которых нужно извлечь данные
      *
      * @return double                  Начальная метка времени, с которой надо удалить данные и дописать новые

      */
     protected function FindStartPoint($timefactor, $tablename, $valueArray) {
        $result = $this::ExtractSelectedData ($this->dbc,
                                              $tablename,
                                              $valueArray, 
                                              $timefactor, 
                                              "<", 
                                              TRUE);
        $curposition = $result[0][1];
        $i = 1;
        while ($result[$i][1] == $curposition) {
           $i++;
        }
        $output = $result[$i - 1][0];
        return $output;
     }
    /**
      * Вспомогательная функция обработки данных
      *
      * @param  array    $input         Массив извлеченных данных
      * @param  string   $tablename     Имя таблицы
      *
      * @return array                   Массив значений для записи в таблицу
      */
     protected function СalculateData($input, $tablename) { 
     }
  }
?>