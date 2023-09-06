<?php
  /**
   * ServiceScriptsFunctions: Трейт, определяющий служебные функции изменения записей журнала
   */
  trait ServiceScriptsFunctions {
     /**
      * Функция редактирования записи в журнале
      *
      * @param  string   $bidID         Номер заявки, запись которой редактируется
      * @param  array    $input         Ассоциативный массив записываемых значений
      *
      * @return string                  Результат выполнения процедуры
      */
     public function CorrectScript($bidID, $input) {
        $result = parent::CorrectScript($bidID, $input);
        $this->protocol->AddMessage($this->pid, $result);
     }
     /**
      * Функция удаления записи в журнале
      *
      * @param  string   $bidID         Номер заявки, запись которой удаляется
      * @param  string   $time          Время изменения записи, которая удаляется
      *
      * @return string                  Результат выполнения процедуры
      */
     public function DeleteScript($bidID, $time) {
        $query = DeleteQuery::Create($this->tabledata["Tablename"]);
        $query->AddWHERE("BidID", "=", $bidID);
        $query->AddAND("Scripttime =", $time);
        $result = $this->dbc->SendQuery($query);
        $output = parent::DeleteScript($bidID, $result);
        $this->protocol->AddMessage($this->pid, $output);
     }
     /**
      * Функция подготовки данных для записи в журнал
      *
      * @param  array    $input         Ассоциативный массив записываемых значений
      *
      * @return array                   Данные для записи
      */
     protected function PrepaireScriptData ($input) {
        $input["Scripttime"] = $this->scripttime; 
        $output = parent::PrepaireScriptData($input);
        return $output;
     }
  }
?>

