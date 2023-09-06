<?php
  /**
   * ProtocolTableComplector: Производный класс комплектатора таблицы протоколов
   */
  class ProtocolTableComplector {
     use SingletonPattern, ValidatorFunctions;
     /**
      * @param  resource $dbc           Идентификатор соединения
      * @param  array    $tabledata     Массив конфигурации таблицы 
      * @param  object   $sdate         Стартовое время
      * @param  object   $curtime       Метка текущего времени
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      * @param  string   $status        Идентификатор текущего состояния протокола
      * @param  array    $message       Массив записей в протокол
      */
     private $dbc;
     private $tabledata; 
     private $sdate;
     private $curtime;
     private $keycolumn;
     private $status;
     private $message;
     /**
      * Функция инициализации комплектатора таблицы протоколов
      *
      * @param  string   $flag          Индикатор типа протокола
      *                                 "listener"  - протокол слушателя
      *                                 "expert"    - протокол эксперта
      *                                 "trader"    - протокол торговца
      *                                 "interface" - протокол комплектатора интерфейса
      *
      * @return object                  Экземпляр комплектатора
      */
     static public function Initialize($flag) {
        $dbc = MySQLconnector::GetConnect();
        $tabledata = Configurations::GetProtocolConfiguration($flag);
        $instance = self::ServiceInitialize(array($dbc["interface"], $tabledata));
        return $instance;
     }
     /**
      * Конструктор
      *
      * @param array     $arguments     Массив аргументов конструктора 
      */
     private function __construct($arguments) {
        $this->dbc       = $arguments[0];
        $this->tabledata = $arguments[1];
        $this->status    = 1; 
        $this->message   = array();
        $this->keycolumn = "timestamp";
        $datetimes       = Configurations::GetDatetimes();
        $service         = new DateTime($datetimes["currenttime"]->format("Y-m-d"));
        $this->sdate     = $service->sub(new DateInterval(("P" . $this->tabledata["gorizont"] . "D")));
        $service         = new DateTime($datetimes["currenttime"]->format("Y-m-d H:i:s"));
        $this->curtime   = $service->getTimestamp();
        unset($service);
        $this->Validation($this->tabledata["Tablename"], 
                          $this->tabledata["columnName"],
                          $this->tabledata["columnType"], 
                          $this->keycolumn);
     }
     /**
      * Служебная функция, выполняемая при закрытии экземпляра объекта
      *
      * @param  string   $message       Содержание записи об ошибке (в случае "аварийного" закрытия комплектатора)  
      */
     private function ClosingProcedure($message) {
        if ($this->status == 1) {
           if (empty($message)) {
              $message = "Выполненние внезапно прервано из-за неидентифицированной ошибки.";
           }
           $this->dbc->DeleteRow($this->tabledata["Tablename"], $this->keycolumn, "=", $this->curtime);
           $this::AddMessage("err", $message);
           $input = array($this->curtime, 
                          json_encode($this->message, JSON_UNESCAPED_UNICODE), 
                          0);
           $this->dbc->AddRow($this->tabledata["Tablename"], $this->tabledata["columnName"], $input);
        }
     }
     /**
      * Функция стартовой записи в массив записей в протокол
      */
     public function StartMessage() {
        $content = (new DateTime())->setTimestamp($this->curtime)->format("d.m.Y H:i:s");
        $this::AddMessage("start", $content);
        $input = array($this->curtime, json_encode($this->message, JSON_UNESCAPED_UNICODE), $this->status);
        $this->dbc->AddRow($this->tabledata["Tablename"], $this->tabledata["columnName"], $input);
     } 
     /**
      * Функция дополнения массива записей в протокол новой записью
      *
      * @param  string   $type          Тип записи
      * @param  string   $content       Содержание записи
      */
     public function AddMessage($type, $content) {
        $data[0] = $type;
        if (!empty($content)) {
           $data[1] = $content;
        }
        $this->message[] = $data;
     }
     /**
      * Функция дополнения массива записей в протокол новой записью-массивом
      *
      * @param  string   $type          Тип записи
      * @param  array    $content       Содержание записи
      */
     public function AddArray($type, $content) {
        foreach ($content as $row) {
           $this->message[] = array($type, $row);
        }
     }
     /**
      * Функция финальной записи в протокол
      *
      * @param  string   $status        Идентификатор текущего состояния протокола
      */
     public function FinishMessage ($status) {
        $this->status = $status; 
        $colnames     = array($this->tabledata["columnName"][1], $this->tabledata["columnName"][2]);
        $colvalues    = array(json_encode($this->message, JSON_UNESCAPED_UNICODE), $this->status);
        $this->dbc->CorrectRow($this->tabledata["Tablename"], $colnames, $colvalues, $this->keycolumn, "=", $this->curtime);
     }
     /**
      * Функция упрощенной проверки таблиц баз данных
      *
      * @param  string   $tablename     Имя таблицы
      * @param  array    $colnames      Массив имен столбцов таблицы
      * @param  array    $coltypes      Массив типов данных в столбцах таблицы
      * @param  string   $keycolumn     Имя столбца таблицы - ключа
      */
     private function Validation($tablename, $colnames, $coltypes, $keycolumn) {
        $result[0] = $this->CheckTable($tablename, $colnames, $coltypes);
        if ($result[0] > 3) {
           $this->TableCreator($result[0], $tablename, $colnames, $coltypes);
        } else {
           $result = $this->CheckData($tablename, $keycolumn);
        }
        $this->CheckOutdated($result, $tablename, $keycolumn);
     }
  }
?>

