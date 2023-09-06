<?php
  /**
   * BidsJournalComplector: Производный класс комплектатора журнала заявок
   */
  class BidsJournalComplector extends JournalComplector {
     use SingletonPattern, ServiceScriptsFunctions, SortScriptsFunctions,
         CheckScriptsFunctions, ChangeScriptsFunctions, CheckJournalFunctions;
     /**
      * @param  array    $listenerdata  Массив данных слушателя
      * @param  array    $expertdata    Массив рекомендаций эксперта
      * @param  bool     $savelegend    Индикатор записи в легенду в журнале заявок 
      * @param  string   $scripttime    Время изменения записи
      */
     private $listenerdata;
     private $expertdata; 
     private $savelegend;
     private $scripttime;
     /**
      * Функция инициализации комплектатора журнала заявок
      *
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $listenerdata  Массив данных слушателя
      * @param  array    $expertdata    Массив рекомендаций эксперта
      *
      * @return object                  Экземпляр комплектатора
      */
     static public function Initialize($protocol, $listenerdata, $expertdata) {
        self::$instance = self::ServiceInitialize(array($protocol, $listenerdata, $expertdata));
        return self::$instance;
     }
     /**
      * Конструктор
      *
      * @param array     $arguments     Массив аргументов конструктора 
      */
     protected function __construct($arguments) {
        parent::__construct("bids", $arguments[0]);
        $indexis                      = Configurations::GetIndexis();
        $this->tabledata["BaseLimit"] = $indexis["BaseLimit"];
        $datetimes                    = Configurations::GetDatetimes();
        $this->scripttime             = $datetimes["currenttime"]->getTimestamp();
        $this->listenerdata           = $arguments[1];
        $this->expertdata             = $arguments[2];
        $this->savelegend             = FALSE;
     }
     /**
      * Функция получения максимального значения опорной заявки 
      *
      * @return double                Максимальное значение опорной заявки
      */
     public function GetBaseLimit() {
        return $this->tabledata["BaseLimit"];
     }
     /**
      * Функция дополнения легенды в журнале заявок 
      *
      * @param string  $bidID     Номер заявки, в легенде которой дополняется запись  
      * @param string  $input     Содержание дополнения
      */
     public function AddToLegend($bidID, $input) {
        if (empty($this->legend[$bidID])) {
           $this->legend[$bidID][0] = date("d.m.Y H:i");
           $this->legend[$bidID][1] = $this->listenerdata;
           $this->legend[$bidID][2] = $this->expertdata;
        }
        $this->legend[$bidID][] = $input;
     }
     /**
      * Функция записи в легенду в журнале заявок 
      *
      * @param string  $bidID     Номер заявки, в легенде которой дополняется запись  
      * @param string  $input     Содержание дополнения
      */
     public function SaveLegend() {
        if (!empty($this->legend)) {
           $this->protocol->AddMessage($this->pid, "savleg");
           foreach ($this->legend as $key => $value) {
              $journalData = array_shift($this->GetData($key));
              if ($journalData["Legend"]) {
                 $data = json_decode($journalData["Legend"], TRUE);
                 $data[] = $value;
              } else {
                 $data = array($value);
              }
              $this->CorrectScript($key, array("Legend" => json_encode($data, JSON_UNESCAPED_UNICODE))); 
           }
        }
        $this->savelegend = TRUE;
     }
     /**
      * Функция копирования журнала
      */
     public function CloneJournal() {
        if (!$this->savelegend) {
           $this->SaveLegend();
        }
        parent::CloneJournal();
     }
     /**
      * Служебная функция, выполняемая при закрытии экземпляра объекта
      */
     private function ClosingProcedure() {
        $this->CloneJournal();
     }
  }
?>

