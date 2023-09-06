<?php
  /**
   * AddToSell: Трейт, определяющий функцию добавления текста к существующей записи в ячейке таблицы
   */
  trait AddToSell {
     /**
      * Функция добавления текста к существующей записи в ячейке таблицы
      *
      * @param  string   $tablename     Имя таблицы, в ячейку которой дописывается текст
      * @param  string   $colname       Имя столбца, в ячейку которого дописывается текст
      * @param  string   $message       Текст, который необходимо добавить к существующей записи
      * @param  string   $condition     Строка с именем условия
      * @param  string   $relate        Логическое отношение
      * @param  string   $value         Строка со значением условия
      */
     public function AddToSell($tablename, $colname, $message, $condition, $relate, $value) {
        try {
           $query = UpdateQuery::Create($tablename);
           $query->AddSET(array($colname), array($message), FALSE);
           $query->AddWHERE($condition, $relate, $value);
        } catch (Exception $e) {
           throw new MySQLexception("Не удалось создать конструктор запроса.", $e);
        }
        $this->SendQuery($query);
     }
  }
?>