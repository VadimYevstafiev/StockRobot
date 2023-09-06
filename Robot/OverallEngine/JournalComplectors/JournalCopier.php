<?php
  /**
   * JournalCopier: Базовый класс комплектатора копий журналов
   */
  class JournalCopier {
     use JournalComplectorFunctions; 
     /**
      * @param  resource $dbc           Идентификатор соединения с базой данных интерфейсов
      * @param  resource $dbcread       Идентификатор соединения с базой данных - источником данных
      * @param  array    $tabledata     Массив конфигурации таблицы 
      * @param  array    $keyarray      Массив значений в столбце таблицы - ключе
      * @param  string   $name          Строка с именем журнала
      * @param  string   $pid           Идентификатор для протокола
      */
     protected $dbc;
     protected $dbcread;
     protected $tabledata; 
     protected $keyarray;
     protected $name;
     protected $pid;
     /**
      * Конструктор
      *
      * @param  resource $dbc           Идентификатор соединения с базой данных интерфейсов
      * @param  resource $dbcread       Идентификатор соединения с базой данных - источником данных
      * @param  array    $tabledata     Массив конфигурации таблицы
      */
     public function __construct ($dbcread, $dbc, $tabledata) {
        $this->dbcread   = $dbcread;
        $this->dbc       = $dbc;
        $this->tabledata = $tabledata;
        $this->name      = $tabledata["name"];
        $this->pid       = $tabledata["pid"];
     }
     /**
      * Функция обновления копии журнала
      *
      * @param  array    $all           Массив номеров строк копируемой таблицы
      * @param  array    $active        Массив номеров строк копируемой таблицы,
      *                                 в которые вносились изменения
      * @param  object   $protocol      Комплектатор таблицы протокола
      */
     public function Complete($all, $active, $protocol) {
        $change  = FALSE;
        $this->Validation($this->tabledata["Tablename"], 
                          $this->tabledata["columnName"],
                          $this->tabledata["columnType"], 
                          $this->tabledata["keyColumn"]);
        if (!$all) {
           $all = array();
        }
        if (!$this->keyarray) {
           $this->keyarray = array();
        }
        //Проверяем, есть ли в таблице-копии строки,
        //отсутствующие в таблице-оригинале
        $deleted = array_diff($this->keyarray, $all);
        if (!empty($active)) {
           $deleted = array_diff($deleted, $active);
        }
        //Проверяем, есть ли в таблице-оригинале строки,
        //отсутствующие в таблице-копии
        $new = array_diff($all, $this->keyarray);
        if (!empty($active)) {
           $service = array_diff($active, $this->keyarray);
           $service = array_diff($service, $new);
           $new = array_merge($new, $service);
           $corrected = array_diff($active, $new);
        }
        if (!empty($deleted)) {
           foreach ($deleted as $value) {
              $result = $this->dbc->DeleteRow($this->tabledata["Tablename"], $this->tabledata["keyColumn"], "=", $value);
              $result = $this->DeleteScript($value, $result);
              $protocol->AddMessage($this->pid, $result);
              $change  = TRUE;
           }
        }
        if (!empty($corrected)) {
           foreach ($corrected as $value) {
              $data = $this->GetData($this->dbcread, $value);
              $result = $this->CorrectScript($value, $data[0]);
              $protocol->AddMessage($this->pid, $result);
              $change  = TRUE;
           }
        }
        if (!empty($new)) {
           foreach ($new as $value) {
              $data = $this->GetData($this->dbcread, $value);
              $result = $this->AddScript($data[0]);
              $protocol->AddMessage($this->pid, $result);
              $change  = TRUE;
           }
        }
        if (!$change) {
           $protocol->AddMessage($this->pid, "ncj");
        }
     }
  }
?>

