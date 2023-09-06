<?php
  /**
   * ShowTablesQuery: Производный класс функций запросов SHOW TABLES
   */
  class ShowTablesQuery extends QueryConstructor {
     /**
      * Функция создания запроса SHOW TABLES
      *
      * @return object                  Экземпляр запроса
      */
     static public function Create() {
        $instance = new static(self::CreateQuery());
        return $instance;
     }
     /**
      * Функция создания строки запроса SHOW TABLES 
      *
      * @return string                  Строка запроса
      */
     protected function CreateQuery() {
        $output = "SHOW TABLES";
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