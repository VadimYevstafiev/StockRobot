<?php
  /**
   * SelectQuery: Производный класс функций запросов SELECT
   */
  class SelectQuery extends QueryConstructor {
     use AddAND, AddLIMIT, AddORDER, AddWHERE;
     /**
      * Функция создания запроса SELECT  
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      * @param  array    $fieidnames    Массив c именами полей, данные из которых запрашиваются
      *                                 или символ "*", если запрашиваются данные всех полей
      *
      * @return object                  Экземпляр запроса
      */
     static public function Create($tablename, $fieidnames = "*") {
        $instance = new static(self::CreateQuery($tablename, $fieidnames));
        return $instance;
     }
     /**
      * Функция создания строки запроса SELECT  
      *
      * @param  string   $tablename     Имя таблицы, к которой создается запрос
      * @param  array    $fieidnames    Массив c именами полей, данные из которых запрашиваются
      *                                 или символ "*", если запрашиваются данные всех полей
      *
      * @return string                  Строка запроса
      */
     protected function CreateQuery($tablename, $fieidnames) {
        $output = "SELECT ";
        if ($fieidnames == "*") {
           $output .= $fieidnames;
        } else if (is_array($fieidnames)) {
           $output .= $fieidnames[0];
           for ($i = 1; $i < count($fieidnames); $i++) {
              $output .= ", " . $fieidnames[$i];
           }
        } else {
           throw new QueryException("Ошибка при создании строки запроса SELECT. Некорректный параметр.");
        }
        $output .= " FROM " . $tablename;
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

