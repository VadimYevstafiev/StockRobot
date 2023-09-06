<?php
  /**
   * AddORDER: Трейт, определяющий функцию дополнения строки запроса выражением ORDER
   */
  trait AddORDER {
     /**
      * Функция дополнения строки запроса выражением ORDER
      *
      * @param  string   $condition     Строка с именем поля, по значениям которого сортируются данные
      * @param  bool     $inverse       Индикатор порядка сортировки данных (FALSE - прямой, TRUE - обратный)
      *
      */
     public function AddORDER($condition, $inverse = FALSE) {
        $this->query .= " ORDER BY " . $condition;
        if ($inverse) {
           $this->query .= " DESC";
        }
     }
  }
?>