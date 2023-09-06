<?php
  /**
   * SummarySignalsTableComplector: Производный класс комплектатора  сводной таблицы сигналов по типам обмена
   */
  class SummarySignalsTableComplector extends SummaryTableComplector {
     /**
      * Вспомогательная функция обработки данных
      *
      * @param array   $input      Массив извлеченных данных
      * @param string  $tablename  Имя таблицы
      *
      * @return array              Массив значений для записи в таблицу
      */
     protected function СalculateData($input, $tablename) {
        if ($this->new) {
           $prevSummary = 0;
        } else {
           $value = $this->ExtractRow($tablename);
           $prevSummary = $value["Summary"];
        }
        for ($i = 0; $i < count($input); $i++) { 
           for ($j = 0; $j < count($input[$i]); $j++) {
              $output[$i][$j] = $input[$i][$j];
           }
           if ($output[$i][2] == $output[$i][1]) {                        //CompositeAsk == CompositeBid
              $summary = $output[$i][2];
           } else if (($output[$i][2] > 0) && ($output[$i][1] > 0)) {     //CompositeAsk > 0 && CompositeBid > 0
              $summary = 1;                                                           
           } else if (($output[$i][2] > 0) && ($output[$i][1] == 0)) {    //CompositeAsk > 0 && CompositeBid == 0 
              if ($prevSummary > 0) {                                               //prevSummary > 0
                 $summary = 1;
              } else {
                 $summary = 0;
              }
           } else if (($output[$i][2] > 0) && ($output[$i][1] < 0)) {     //CompositeAsk > 0 && CompositeBid < 0
              if ($prevSummary > 0) {                                               //prevSummary > 0
                 $summary = 1;
              } else if ($prevSummary < 0) {                                        //prevSummary < 0
                 $summary = - 1;
              } else {                                                              //prevSummary == 0
                 $summary = 0;
              }
           } else if (($output[$i][2] == 0) && ($output[$i][1] > 0)) {    //CompositeAsk == 0 && CompositeBid > 0
              $summary = 0; 
           } else if (($output[$i][2] == 0) && ($output[$i][1] < 0)) {    //CompositeAsk == 0 && CompositeBid < 0
              if ($prevSummary < 0) {                                               //prevSummary < 0
                 $summary = - 1;
              } else {
                 $summary = 0;
              } 
           } else if (($output[$i][2] < 0) && ($output[$i][1] > 0)) {     //CompositeAsk < 0 && CompositeBid > 0
              $summary = 0;
           } else if (($output[$i][2] < 0) && ($output[$i][1] == 0)) {    //CompositeAsk < 0 && CompositeBid == 0
              if ($prevSummary < 0) {                                               //prevSummary < 0
                 $summary = - 1;
              } else {
                 $summary = 0;
              }
           } else {                                                       //CompositeAsk < 0 && CompositeBid < 0 
              $summary = - 1; 
           }
           $output[$i][3] = $summary;
           $prevSummary = $summary;
        }
        return $output;
     }
  }
?>