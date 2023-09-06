<?php
  /**
   * CheckJournalFunctions: Трейт, определяющий служебные функции проверки журнала заявок
   */
  trait CheckJournalFunctions {
     /**
      * Функция проверки уже существующих записей в журнале заявок 
      *
      * @param  array    $bidsList      Массив данных системы об открытых заявках
      *
      * @return string   $output[0]     Код сооветствия 
      *                                 если $output[0] == 0 - в журнал не вносились изменения 
      *                                 если $output[0] == 1 - данные журнала были изменены в соответствии
      *                                                        с текущими данными о заявках
      *                                 если $output[0] == 2 - в журнале есть записи, для которых нет 
      *                                                        соответствующих открытых заявок
      * @return array    $output[1]     Массив номеров проверенных записей (если $output[0] == 2),
      *                                 для которых нет соответствующих открытых заявок
      */
     public function CheckOpenBids($bidsList) {
        $this->protocol->AddMessage($this->pid, "gd");
        $i = 0;
        $bool = FALSE;
        while (!$bool) {
           if ($i != 0) {
              $this->protocol->AddMessage($this->pid, "dgd");
           }
           $data = $this->GetData();
           $bool = $this::CheckDuplication($data);
           $i++;
        }
        $journalData = $this->SortByState($data);
        $this->protocol->AddMessage($this->pid, array("scr" => array(count($journalData["new"]),
                                                                     count($journalData["base"]),
                                                                     count($journalData["active"]),
                                                                     count($journalData["service"]))));
        $changed  = FALSE;
        $closed   = array();
        foreach ($journalData as $key => $values) {
           if ((count($values) > 0) && ($key != "close")) {
              $bids = $this->SortByDirection($values);
              foreach ($bids as $direction => $value) {
                 if (count($value) > 0) {
                    $result = $this->CheckOneState($bidsList, $value, $key, $direction);
                    for ($i = 0; $i < count($result[1]); $i++) {
                       unset($bidsList[$result[1][$i]]);
                    }
                    if ($result[0] != 0) {
                       $changed = TRUE;
                       if ($result[0] == 2) {
                          for ($i = 0; $i < count($result[2]); $i++) {
                             unset($bidsList[$result[2][$i]]);
                             $closed[] = $result[2][$i];
                          }
                       }
                    }
                 }
              } //end foreach
           }
        }
        if (count($bidsList) > 0) {
           $changed = TRUE;
           $this->protocol->AddMessage($this->pid, array("counew" => count($bidsList)));
           foreach ($bidsList as $value) {
              $this->NewScript($value, "nw");
           }
        } 
        if (!empty($closed)) {
           $output[0] = 2;
           $output[1] = $closed;
        } else if ($changed) {
           $output[0] = 1;
        } else {
           $output[0] = 0;
        }
        return $output;
     }
     /**
      * Функция закрытия записи в журнале заявок 
      *
      * @param  array    $bidsData      Массив данных системы о закрытой заявке
      * @param  array    $journalData   Массив данных записи в журнале 
      *
      */
     public function CheckClosedBid ($bidsData) {
        if (isset($bidsData)) {
           $this->CloseScript($bidsData);
        } else {
           $message = "В данных системы нет информации о закрытой заявке № " . $bidsData["BidID"] . ".";
           $message .= "Процедура торгов должна быть прервана.";
           throw new BidsJournalException($message);
        }
     }
     /**
      * Функция проверки записей на дублирование
      *
      * @param  array   $journalData  Массив записей в журнале 
      *
      * @return bool    $output       TRUE, в журнале нет дублирования записей
      *                               или FALSE, дублирование есть
      */
     protected function CheckDuplication($journalData) {
        if (count($journalData) <= 1) {
           $this->protocol->AddMessage($this->pid, array("du" => "no"));
           $output = TRUE;
           return $output;
        }
        $this->protocol->AddMessage($this->pid, array("du" => "ye"));
        $service = array();
        for ($i = 0; $i < count($journalData); $i++) {
           $key = $journalData[$i]["BidID"];
           if (isset($service[$key])) {
              $service[$key][] = $i;
           } else {
              $service[$key] = array($i);
           }
        }
        foreach ($service as $key => $value) {
           if (count($value) > 1) {
              $this->protocol->AddMessage($this->pid, array("du" => array($key => count($value))));
              $data = array(0, 0);
              for ($i = 0; $i < count($value); $i++) {
                 if ($journalData[$value[$i]]["Scripttime"] > $data[0]) {
                    $data[0] = $journalData[$value[$i]]["Scripttime"];
                    $data[1] = $value[$i];
                 }
              }     
              unset($value[$data[1]]);
              $this->protocol->AddMessage($this->pid, array("du" => "del"));
              foreach ($value as $item) {
                 $this::DeleteScript($key, $journalData[$item]["Scripttime"]);
              }
              $output = FALSE;
           } else {
              $output = TRUE;
           }
        }
        if ($output) {
           $this->protocol->AddMessage($this->pid, array("du" => "ok"));
        }
        return $output;
     }
  }
?>

