<?php
  /**
   * DataTableComplector: Базовый класс комплектатора таблиц данных
   */
  class DataTableComplector {
     use Validator;
     /**
      * @param  resource $dbc           Идентификатор соединения
      * @param  object   $protocol      Комплектатор таблицы протоколов
      * @param  integer  $index         Тип обмена
      * @param  array    $tabledata     Массив конфигурации таблицы 
      * @param  object   $sdate         Стартовое время
      * @param  object   $fdate         Финальное время
      */
     protected $dbc;
     protected $protocol;
     protected $index;
     protected $tabledata;
     protected $sdate;
     protected $fdate;
     /**
      * Конструктор
      *
      * @param  resource $dbc           Идентификатор соединения
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $tabledata     Массив конфигурации таблицы
      * @param  array    $datetimes     Массив дат и времени
      */
     public function __construct ($dbc, $protocol, $tabledata, $datetimes) {
        $this->dbc       = $dbc;
        $this->protocol  = $protocol;
        $this->tabledata = $tabledata;
        $this->indexis   = $tabledata["indexis"];
        $this->sdate     = $datetimes["startdate"];
        $this->fdate     = $datetimes["finishdate"];
     }
     /**
      * Общая функция комплектации таблицы
      */
     public function Complete() {
        for ($i = 0; $i < count($this->indexis); $i++) {
           $this::ServiceComplete($this->indexis[$i]);
        }
     }
     /**
      * Служебная функция комплектации таблицы
      *
      * @param  string   $index         Тип обмена
      *
      * @return bool                    Результат комплектации таблицы
      */
     protected function ServiceComplete($index) {
        $this::StartDeclaration($index);
        $tablename = $this::DeterminateTablename($this->tabledata["Type"], $index);
        $result = $this->Validation($tablename, 
                                    ($this->tabledata["Timeframe"] * $this->tabledata["timemodule"]), 
                                    $this->tabledata["columnName"],
                                    $this->tabledata["columnType"], 
                                    $this->tabledata["columnName"][0],
                                    $index);
        $this::ExecuteComplete($result, $tablename, $index);
     }
     /**
      * Функция сообщения о начале комплектации
      *
      * @param  string   $index         Тип обмена
      *
      */
     protected function StartDeclaration($index) {
        $defin = $this->tabledata["Definition"];
        if ($index) {
           $defin .= " " . $index;
        }
        $this->protocol->AddMessage("declar", array("defin" => $defin,
                                                    "sdate" => $this->sdate->format("d.m.Y H:i"),
                                                    "fdate" => $this->fdate->format("d.m.Y H:i")));
     }
     /**
      * Функция комплектации таблицы
      *
      * @param  array    $input         Массив результатов поствалидации
      * @param  string   $tablename     Имя таблицы
      * @param  integer  $index         Тип обмена
      */
     protected function ExecuteComplete($input, $tablename, $index) {
        $result = $this::PrepaireData($input, $tablename, $index);
        $this::AddData($result, $tablename);
     }
     /**
      * Функция определения имени таблицы
      *
      * @param  string   $type          Тип таблицы
      * @param  integer  $index         Тип обмена
      *
      * @return string                  Имя таблицы
      */
     protected function DeterminateTablename($type, $index) {
        $output = $type . $index;
        return $output;
     }
     /**
      * Функция записи данных в строку таблицы
      *
      * @param  array    $input         Массив записываемых значений
      * @param  string   $tablename     Имя таблицы
      */
     protected function AddData($input, $tablename) {
        $servicedate = new DateTime();
        for ($i = 0; $i < count($input); $i++) {
           $bool = $this->dbc->AddRow($tablename, $this->tabledata["columnName"], $input[$i]);
           $key = $servicedate->setTimestamp($input[$i][0])->format("d.m.Y H:i:s");
           if (!$bool) {
              $message = "Не удалось записать данные на " . $key . ".";
              throw new Exception($message);            
           } else {
              $this->protocol->AddMessage("save", $key);
           }     
        }
        unset($servicedate);        
     }
  }
?>

