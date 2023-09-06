<?php
  /**
   * OptionsTableComplector: Базовый класс комплектатора таблиц опций графиков
   */
  class OptionsTableComplector {
     use ValidatorFunctions;
     /**
      * @param  resource $dbc           Идентификатор соединения
      * @param  object   $protocol      Комплектатор таблицы протоколов
      * @param  array    $tabledata     Массив конфигурации таблиц баз данных и их обработчиков
      * @param  array    $options       Массив конфигурации таблицы опций 
      * @param  array    $structure     Массив структуры графиков
      * @param  array    $indexis       Массив значений типов обмена 
      * @param  array    $timeframes    Массив номенклатуры таймфреймов
      * @param  array    $definition    Строка объявления для протокола
      */
     protected $dbc;
     protected $protocol; 
     protected $tabledata;
     protected $options;
     protected $structure;
     protected $indexis;
     protected $timeframes;
     protected $definition;

     /**
      * @param  object   $protocol      Комплектатор таблицы протоколов
      */
     public function __construct ($protocol) {
        $connect          = MySQLconnector::GetConnect();
        $this->dbc        = $connect["interface"];
        $this->protocol   = $protocol;
        $this->tabledata  = Configurations::GetTabledata();
        $this->structure  = Configurations::GetChartsStructure();
        $service          = Configurations::GetIndexis();
        $this->indexis    = $service["general"];
        $service          = Configurations::GetTimeframes();
        $this->timeframes = $service["general"];
     }
     /**
      * Общая функция комплектации таблиц графиков
      */
     public function Complete() {
        $this::StartDeclaration();
        $this::ExecuteComplete();
     }
     /**
      * Функция сообщения о начале комплектации таблиц графиков
      */
     protected function StartDeclaration() {
        $this->protocol->AddMessage("declar", array("defin" => ("таблиц" . $this->definition)));
     }
     /**
      * Функция исполнения комплектации таблиц опций графиков
      */
     protected function ExecuteComplete() {
     }
     /**
      * Функция исполнения комплектации содержимого таблиц опций графиков
      */
     protected function CompleteContent() {
     }
     /**
      * Функция проверки таблицы опций графиков
      *
      * @param  string   $tablename     Имя таблицы
      * @param  array    $colnames      Массив имен столбцов таблицы
      * @param  array    $coltypes      Массив типов данных в столбцах таблицы
      */
     protected function Validation($tablename, $colnames, $coltypes) {
        $result[0] = $this::CheckTable($tablename, $colnames, $coltypes);
        $this->protocol->AddMessage("valid", array("res" => $result[0]));
        if ($result[0] > 3) {
           $data = $this::TableCreator($result[0], $tablename, $colnames, $coltypes);
           $this->protocol->AddArray("valid", $data);
        } else {
           $this->dbc->DeleteAll($tablename);
        }
     }
     /**
      * Функция записи данных в таблицы
      *
      * @param  array    $input         Массив записываемых значений
      */
     protected function AddData($tablename, $colnames, $input) {
        $servicedate = new DateTime();
        for ($i = 0; $i < count($input); $i++) {
           $bool = $this->dbc->AddRow($tablename, $colnames, $input[$i]);
           if (!$bool) {
              $message = "Не удалось записать данные в таблицу" . $this->definition;
              throw new Exception($message);            
           }
        }
        $this->protocol->AddMessage("saveopt", $this->definition);
     }
  }
?>

