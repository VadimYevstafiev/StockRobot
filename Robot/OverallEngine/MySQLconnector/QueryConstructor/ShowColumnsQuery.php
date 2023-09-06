<?php
  /**
   * ShowColumnsQuery: Производный класс функций запросов SHOW COLUMNS 
   */
  class ShowColumnsQuery extends QueryConstructor {
     /**
      * Функция создания запроса SHOW COLUMNS 
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      *
      * @return object                  Экземпляр запроса
      */
     static public function Create($tablename) {
        $instance = new static(self::CreateQuery($tablename));
        return $instance;
     }
     /**
      * Функция создания строки запроса SHOW COLUMNS 
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      *
      * @return string                  Строка запроса
      */
     protected function CreateQuery($tablename) {
        $output = "SHOW COLUMNS FROM " . $tablename;
        return $output;
     }
     /**
      * Функция обработки результата SQL-запроса 
      *
      * @param  object   $response      Объект mysqli_result
      *
      * @return array                   Массив данных, полученных в результате запроса
      */
     public function ParseReponse($response) {
        $output = array();
        while ($row = $response->fetch_row()) {
           $output[] = $row;
        }
        return $output;
     }
  }
?>