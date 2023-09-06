<?php
  /**
   * AddWHERE: Трейт, определяющий функцию дополнения строки запроса выражением WHERE
   */
  trait AddWHERE {
     /**
      * Функция дополнения строки запроса выражением WHERE
      *
      * @param  string   $condition     Строка с именем условия
      * @param  string   $relate        Логическое отношение
      * @param  string   $value         Строка со значением условия
      *
      */
     public function AddWHERE($condition, $relate, $value) {
        $this->query .= " WHERE " . $condition . " " . $relate . " " . $value;
     }
  }
?>