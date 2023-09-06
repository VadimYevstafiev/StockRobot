<?php
  /**
   * ParseValidateData: Трейт, определяющий функцию подготовки текста результатов валидации 
   */
  trait ParseValidateData {
     /**
      * Функция подготовки текста результатов валидации 
      *
      * @param  string   $data          Строка с массивом данных
      *
      * @return string                  Текст результатов валидации
      */
     private function ParseValidateData($data) {
        if (is_array($data)) {
           $output = "";
           foreach ($data as $key => $value) {
              switch ($key) {
                 case "res":
                    $output .= self::ParseValidateResults($value);
                    break;
                 case "post":
                    $output .= self::ParseRedactionResults($value);
                    break;
              }
           }
        } else {
           switch ($data) {
              case "deltab":
                 $output = self::MakeString("Удаляем существующую таблицу.");
                 break;
              case "cretab":
                 $output = self::MakeString("Создаем таблицу.");
                 break;
              case "delall":
                 $output = self::MakeString("Конечная дата данных находится вне заданного временного диапазона.");
                 $output .= self::MakeString("Удаляем все строки в таблице.");
                 break;
              case "delone":
                 $output = self::MakeString("Конечная дата данных младше конечной даты заданного временного диапазона.");
                 $output .= self::MakeString("Удаляем из таблицы данные за последний период (временного диапазона таблицы).");
                 break;
              case "deltwo":
                 $output = self::MakeString("Конечная дата данных старше конечной даты заданного временного диапазона.");
                 $output .= self::MakeString("Удаляем из таблицы данные за два последних периода диапазона данных.");
                 break;
              case "delout":
                 $output = self::MakeString("Часть данных в таблице устарела и может быть удалена.");
                 $output .= self::MakeString("Удаляем устаревшие данные.");
                 break;
              case "delkey":
                 $output = self::MakeString("Удаляем из таблицы данные, начиная с уточненной начальной метки времени.");
                 break;
              case "early":
                 $output = self::MakeString("Таблицу необходимо заполнить данными за период более ранний, чем в таблице.");
                 break;
           }
        }
        return $output;
     }
     /**
      * Функция подготовки текста кода валидации 
      *
      * @param  string   $value         Код результата валидации
      *
      * @return string                  Текст кода валидации
      */
     private function ParseValidateResults($value) {
        $output = self::MakeString('Валидация таблицы выполнена с кодом "' . $value . '":');
        switch ($value) {
           case 0:
              $output .= self::MakeString("Таблица есть в базе данных, заполнена, пробелов в данных нет.");
              break;
           case 1:
              $output .= self::MakeString("Таблица есть в базе данных, заполнена, в данных таблицы есть пробелы.");
              break;
           case 2:
              $output .= self::MakeString("Таблица есть в базе данных, заполнена, пробелов в данных нет, временная отметка 1-й строки в таблице некорректна.");
              break;
           case 3:
              $output .= self::MakeString("Таблица есть в базе данных, данных в таблице нет.");
              break;
           case 4:
              $output .= self::MakeString("Таблица есть в базе данных, список колонок в таблице не соответствует заданному.");
              break;
           case 5:
              $output .= self::MakeString("Таблица есть в базе данных, колонок в таблице нет.");
              break;
           case 6:
              $output .= self::MakeString("Таблицы нет в базе данных.");
              break;
           case 7:
              $output .= self::MakeString("База данных пуста.");
              break;
        }
        return $output;
     }
     /**
      * Функция подготовки текста кода редактирования данных 
      *
      * @param  string   $value         Код результата редактирования
      *
      * @return string                  Текст кода редактирования
      */
     private function ParseRedactionResults($value) {
        $output = self::MakeString('Редактирование таблицы выполнено с кодом "' . $value . '":');
        switch ($value) {
           case 0:
              $output .= self::MakeString("Необходимо дописать данные в одном временном диапазоне.");
              break;
           case 1:
              $output .= self::MakeString("Необходимо дописать данные в двух временных диапазонах.");
              break;
        }
        return $output;
     }
  }
?>