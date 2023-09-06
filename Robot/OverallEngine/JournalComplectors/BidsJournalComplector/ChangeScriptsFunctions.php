<?php
  /**
   * ChangeScriptsFunctions: Трейт, определяющий функции изменения записей журнала
   */
  trait ChangeScriptsFunctions {
     /**
      * Функция создания новой записи в журнале заявок 
      *
      * @param  array    $data          Массив данных заявки
      * @param  string   $type          Тип заявки
      *                                 "nw" - ранее неучтенная заявка
      *                                 "bs" - опорная заявка
      *                                 "sr" - служебная заявка
      * @param  string   $parent        Номер "родителя" заявки
      */
     public function NewScript($data, $type, $parent) {
        switch ($type) {
           case "nw":
              $data["State"] = 0;
              break;
           case "bs":
              $data["State"] = 10;
              break;
           case "sr":
              $data["State"] = 30;
              break;
        }
        if ($parent) {
           $data["ParentID"] = $parent;
        }
        $data["Revnumber"] = 0;
        $result = $this->AddScript($data);
        $message = array("new" => array($type => $data["BidID"]),
                         "ob"  => array($data["Amountin"],
                                        $data["Amountout"],
                                        $data["Exchamountin"],
                                        $data["Exchamountout"],
                                        $data["Rate"]));
        $this->protocol->AddMessage($this->pid, $message);
        $this->protocol->AddMessage($this->pid, $result);
        $this->AddToLegend($data["BidID"], array($this->pid => $message));
     }
     /**
      * Функция изменения данных заявки
      *
      * @param  array    $data         Массив данных изменяемой заявки
      */
     public function ChangeScript($data) {
        $result = array ("Amountin"      => $data["Amountin"],
                         "Amountout"     => $data["Amountout"],
                         "Exchamountin"  => $data["Exchamountin"],
                         "Exchamountout" => $data["Exchamountout"],
                         "Rate"          => $data["Rate"]);
        $this->CorrectScript($data["BidID"], $result);
        $message = array("ob"  => array_values($result));
        $this->AddToLegend($data["BidID"], array($this->pid => $message));
     }
     /**
      * Функция закрытия заявки
      *
      * @param  array    $data         Массив данных закрываемой заявки
      */
     public function CloseScript($data) {
        $message = array("cli" => $data["BidID"]);
        $this->protocol->AddMessage($this->pid, $message);
        if (!isset($data["Closetime"])) {
           $data["Closetime"] = (string) (new DateTime)->getTimestamp();
        }
        $result = array ("Amountin"      => $data["Amountin"],
                         "Amountout"     => $data["Amountout"],
                         "Exchamountin"  => $data["Exchamountin"],
                         "Exchamountout" => $data["Exchamountout"],
                         "Rate"          => $data["Rate"],
                         "State"         => $data["State"],
                         "Closetime"     => $data["Closetime"],
                         "Closetype"     => $data["Closetype"]);
        $this->CorrectScript($data["BidID"], $result);
        $this->AddToLegend($data["BidID"], array($this->pid => $message));
      }
  }
?>

