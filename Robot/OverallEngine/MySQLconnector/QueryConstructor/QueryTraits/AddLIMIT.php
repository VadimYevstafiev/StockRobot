<?php
  /**
   * AddLIMIT: Трейт, определяющий функцию дополнения строки запроса выражением LIMIT
   */
  trait AddLIMIT {
     /**
      * Функция дополнения строки запроса выражением AddLIMIT
      *
      * @param  string   $value         Значение параметра LIMIT
      *
      */
     public function AddLIMIT($value) {
        $this->query .= " LIMIT " . $value;
     }
  }
?>