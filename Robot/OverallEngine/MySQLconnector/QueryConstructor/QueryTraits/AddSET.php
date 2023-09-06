<?php
  /**
   * AddSET: Трейт, определяющий функцию дополнения строки запроса выражением SET
   */
  trait AddSET {
     /**
      * Функция дополнения строки запроса выражением SET
      *
      * @param array     $colnames      Массив имен столбцов таблицы, данные которых изменяются 
      * @param array     $colvalues     Массив значений, которые вносятся
      * @param bool      $replace       Индикатор, указывающий заменяют ли новые данные уже имеющиеся
      *                                 или дополняют их (FALSE - дополняют, TRUE - заменяют)
      *
      */
     public function AddSET($colnames, $colvalues, $replace = TRUE) {
        $output = " SET ";
        if ((!is_array($colnames)) || (!is_array($colvalues)) || (count($colnames) != count($colvalues))) {
           throw new QueryException("Ошибка при дополнении строки запроса выражением SET. Некорректные параметры.");
        } else {
           $output .= $colnames[0] . " = ";
           if (!$replace) {
              $output .= "concat(" . $colnames[0] . ", '" . $colvalues[0] . "')";
           } else {
              $output .= "'" . $colvalues[0] . "'";
           }
           for ($i = 1; $i < count($colnames); $i++) {
              $output .= ", " . $colnames[$i] . " = ";
              if (!$replace) {
                 $output .= "concat(" . $colnames[$i] . ", '" . $colvalues[$i] . "')";
              } else {
                 $output .= "'" . $colvalues[$i] . "'";
              }
           }
        }
        $this->query .= $output;
     }
  }
?>