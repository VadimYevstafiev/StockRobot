<?php
  /**
   * JournalComplector: Базовый класс комплектатора журналов
   */
  class JournalComplector {
     use JournalComplectorFunctions {
     JournalComplectorFunctions::GetData as serviceGetData;}
     /**
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $keyarray      Массив значений в столбце таблицы - ключе
      * @param  array    $legend        Массив легенды
      * @param  object   $copier        Экземпляр комплектатора копии журнала
      * @param  string   $name          Строка с именем журнала
      * @param  string   $pid           Идентификатор для протокола
      * @param  bool     $cloned        Индикатор копирования журнала
      */
     protected $protocol;
     protected $keyarray;
     protected $legend;
     protected $copier;
     protected $name;
     protected $pid;
     protected $cloned;
     /**
     /**
      * Конструктор
      *
      * @param  string   $key           Тип журнала
      *                                 "trade" - журнал торгов
      *                                 "bids"  - журнал заявок
      * @param  object   $protocol      Комплектатор таблицы протокола
      */
     protected function __construct ($key, $protocol) {
        $dbc             = MySQLconnector::GetConnect();
        $this->dbc       = $dbc["work"];
        $this->tabledata = Configurations::GetJournalConfiguration($key);
        $this->protocol  = $protocol;
        $this->legend    = array();
        $this->keycolumn = $this->tabledata["keyColumn"];
        $this->name      = $this->tabledata["name"];
        $this->pid       = $this->tabledata["pid"];
        $this->copier    = new JournalCopier($dbc["work"], 
                                             $dbc["interface"], 
                                             $this->tabledata);
        $this->cloned    = FALSE;
        $this->Validation($this->tabledata["Tablename"], 
                          $this->tabledata["columnName"],
                          $this->tabledata["columnType"], 
                          $this->tabledata["keyColumn"]);
     }
     /**
      * Функция извлечения данных журнала 
      *
      * @param  string   $input         Значение в столбце таблицы - ключе
      *                                 (в случае извлечения данных конкретной строки)
      *
      * @return array                   Массив извлеченных значений 
      */
     public function GetData($input = NULL) {
        $output = $this->serviceGetData($this->dbc, $input); 
        return $output;
     }
     /**
      * Функция копирования журнала
      */
     public function CloneJournal() {
        if (!$this->cloned) {
           $this->protocol->AddMessage($this->pid, "cj");
           $this->copier->Complete($this->keyarray, array_keys($this->legend), $this->protocol);
           $this->cloned = TRUE;
        }
     }
  }
?>

