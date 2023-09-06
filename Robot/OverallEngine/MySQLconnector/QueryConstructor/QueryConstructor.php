<?php
  /**
   * QueryConstructor: Абстрактный класс функций конструктора SQL-запросов
   */
  abstract class QueryConstructor {
     /**
      * @param  string   $query         Строка с текстом запроса
      */
     protected $query;
     /**
      * Конструктор
      *
      * @param  string   $query         Строка с текстом запроса
      *
      */
     protected function __construct ($query) {
        $this->query       = $query;
     }
     /**
      * Функция создания экземпляра модуля
      */
     public static function Create() {
     }
     /**
      * Функция получения строки с текстом запроса
      */
     public function GetQuery() {
        if (!empty($this->query)) {
           $output = $this->query;
        } else {
           throw new QueryException("Ошибка при получении строки запроса. Строка не создана.");
        }
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
        return $response;
     }
  }
?>

