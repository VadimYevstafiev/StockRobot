<?php
  /**
   * SummaryDataTableComplector: Производный класс комплектатора сводной таблицы данных по типам обмена
   */
  class SummaryDataTableComplector extends SummaryTableComplector {
     /**
      * Служебная функция определения начальной метки времени, с которой надо дописать данные
      *
      * @param array   $input         Массив результатов валидации
      *
      * @return double                Начальная метка времени, с которой надо удалить данные и дописать новые
      */
     protected function ServiceTimefactor($input) {
        return $input[1];
     }
     /**
      * Вспомогательная функция обработки данных
      *
      * @param array   $input      Массив извлеченных данных
      * @param string  $tablename  Имя таблицы
      *
      * @return array              Массив значений для записи в таблицу
      */
     protected function СalculateData($input, $tablename) { 
        return $input;
     }
  }
?>