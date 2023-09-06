<?php
  /**
   * AddAND: Трейт, определяющий функцию дополнения строки запроса выражением AND
   */
  trait AddAND {
     /**
      * Функция дополнения строки запроса выражением AND
      *
      * @param  string   $condition     Строка с именем условия и оператором сравнения
      * @param  string   $value         Строка со значением условия
      *
      */
     public function AddAND($condition, $value) {
        $this->query .= " AND " . $condition . " " . $value;
     }
  }
?>