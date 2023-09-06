<?php
  /**
   * InsertQuery: Производный класс функций запросов INSERT
   */
  class InsertQuery extends QueryConstructor {
     /**
      * Функция создания запроса INSERT  
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      * @param  array    $fieids        Массив имен колонок, в которые записывается данные
      * @param  array    $values        Массив значений записываемых данных
      *
      * @return object                  Экземпляр запроса
      */
     static public function Create($tablename, $fieids, $values) {
        $instance = new static(self::CreateQuery($tablename, $fieids, $values));
        return $instance;
     }
     /**
      * Функция создания строки запроса INSERT 
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      * @param  array    $fieids        Массив имен колонок, в которые записывается данные
      * @param  array    $values        Массив значений записываемых данных
      *
      * @return string                  Строка запроса
      */
     protected function CreateQuery($tablename, $fieids, $values) {
        $subcomamnd1 = "";
        $subcomamnd2 = "'";
        for ($i = 0; $i < count($fieids) - 1; $i++) {
           $subcomamnd1 .= $fieids[$i];
           $subcomamnd1 .= ", ";
           $subcomamnd2 .= $values[$i];
           $subcomamnd2 .= "', '";
        }
        $subcomamnd1 .= $fieids[(count($fieids) - 1)];
        $subcomamnd2 .= $values[(count($fieids) - 1)];
        $subcomamnd2 .= "'";
        $output = "INSERT INTO " . $tablename . " (" . $subcomamnd1 . ") VALUE (" . $subcomamnd2 . ")";
        return $output;
     }
  }
?>