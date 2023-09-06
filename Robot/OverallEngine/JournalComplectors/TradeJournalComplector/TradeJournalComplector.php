<?php
  /**
   * TradeJournalComplector: Производный класс комплектатора журнала сделок
   */
  class TradeJournalComplector extends JournalComplector {
     use SingletonPattern;
     /**
      * Функция инициализации комплектатора журнала заявок
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      *
      * @return object                  Экземпляр комплектатора
      */
     static public function Initialize($protocol) {
        self::$instance = self::ServiceInitialize(array($protocol));
        return self::$instance;
     }
     /**
      * Конструктор
      *
      * @param array     $arguments     Массив аргументов конструктора 
      */
     protected function __construct($arguments) {
        parent::__construct ("trade", $arguments[0]);
     }
      /**
      * Функция извлечения данных открытой сделки
      *
      * @return array                Массив извлеченных значений 
      */
     public function GetOpenTrade() {
        $query = SelectQuery::Create($this->tabledata["Tablename"]); 
        $query->AddWHERE("State", "=", 0);
        $data = $this->dbc->SendQuery($query);
        $output = array();
        if ($data) {
           for ($i = 0; $i < count($this->tabledata["columnName"]); $i++) {
              $output[$this->tabledata["columnName"][$i]] = $data[0][$i];
           }
        }         
        return $output;
     }
     /**
      * Функция удаления записи в журнале
      *
      * @param string  $bidID       Номер сделки, запись которой удаляется
      *
      * @return string              Результат выполнения процедуры
      */
     public function DeleteScript($bidID) {
        $query = DeleteQuery::Create($this->tabledata["Tablename"]);
        $query->AddWHERE("OpenID", "=", $bidID);
        $result = $this->SendQuery($query);
        parent::DeleteScript($result);
        return $output;
     }
     /**
      * Функция дополнения легенды в журнале  
      *
      * @param string  $id        Номер записи, легенда которой дополняется
      * @param string  $input     Содержание дополнения
      */
     public function AddToLegend($id, $input = NULL) {
        if (empty($this->legend[$id])) {
           $this->legend[$id] = array();
        }
        $this->legend[$id][] = $input;
     }
     /**
      * Служебная функция, выполняемая при закрытии экземпляра объекта
      */
     private function ClosingProcedure() {
        $this->CloneJournal();
     }
  }
?>

