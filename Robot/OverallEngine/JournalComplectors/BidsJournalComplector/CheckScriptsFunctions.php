<?php
  /**
   * CheckScriptsFunctions: Трейт, определяющий служебные функции проверки уже существующих записей 
   * одного состояния и одного направления в журнале заявок
   */
  trait CheckScriptsFunctions {
     /**
      * Функция проверки уже существующих записей одного состояния и одного направления 
      *
      * @param  array    $bidsList      Массив данных системы об открытых заявках
      * @param  array    $journalData   Массив записей в журнале 
      * @param  string   $state         Состояние записи
      * @param  string   $direction     Направление заявки
      *
      * @return string   $output[0]     Код сооветствия 
      *                                 если $output[0] == 0 - в журнал не вносились изменения 
      *                                 если $output[0] == 1 - данные журнала были изменены в соответствии
      *                                                        с текущими данными о заявках
      *                                 если $output[0] == 2 - в журнале есть записи, для которых нет 
      *                                                        соответствующих открытых заявок
      * @return array    $output[1]     Массив номеров проверенных записей, для которых есть соответствующие
      *                                 открытые заявки
      * @return array    $output[2]     Массив номеров проверенных записей (если $output[0] == 2),
      *                                 для которых нет соответствующих открытых заявок
      */
     protected function CheckOneState($bidsList, $journalData, $state, $direction) {
        $this->protocol->AddMessage($this->pid, array("def" => array($state, $direction, count($journalData))));
        $changed  = FALSE;
        $critical = FALSE;
        $closed   = array();
        $service  = array();
        for ($i = 0; $i < count($journalData); $i++) {
           if (count($journalData) > 1) {
              $this->protocol->AddMessage($this->pid, array("scn" => ($i + 1)));
           }
           $result = $this::CheckOneScript($bidsList, $journalData[$i]);
           switch ($result[0]) {
              case 0:
                 $service[] = $result[1];
                 break;
              case 1:
                 $changed = TRUE;
                 $service[] = $result[1];
                 break;
              case 2:
                 $closed[] = $result[1];
                 break;
              case 3:
                 $message = "При проверке журнала заявок выявлено критическое несоответствие";
                 $message .= "с данными системы об открытых заявках. Процедура торгов должна быть прервана.";
                 throw new BidsJournalException($message);
           }
        }
        if (!empty($closed)) {
           $output[0] = 2;
           $output[2] = $closed;
        } else if ($changed) {
           $output[0] = 1;
        } else {
           $output[0] = 0;
        }
        $output[1] = $service;
        return $output;
     }
     /**
      * Функция проверки записи в журнале заявок 
      *
      * @param  array    $bidsList      Массив данных системы об открытых заявках
      * @param  array    $journalData   Массив данных записи в журнале 
      *
      * @return string   $output[0]     Код сооветствия
      *                                 если $output[0] == 0 - данные журнала актуальны (соответствуют данным системы о заявке)
      *                                 если $output[0] == 1 - данные журнала были не актуальны (не соответствовали данным системы 
      *                                                        о заявке) и были приведены в соответствие
      *                                 если $output[0] == 2 - нет открытых заявок, соответствующих этой записи в журнале
      *                                 если $output[0] == 3 - критическое несоответствие данных журнала и данных системы о заявке 
      *                                                        (произошла критическая ошибка)
      * @return string   $output[1]     Номер проверенной заявки 
      */
     protected function CheckOneScript($bidsList, $journalData) {
        if (isset($bidsList[$journalData["BidID"]])) {
           $this->protocol->AddMessage($this->pid, "opi");
           $result = $this::CheckRelevance($bidsList[$journalData["BidID"]], $journalData);
           if ($result[0] == 2) {
              $output[0] = 3;
           } else {
              $output[0] = $result[0];
              $output[1] = $result[1];
           }
        } else {
           $this->protocol->AddMessage($this->pid, "opn");
           $output = array(2, $journalData["BidID"]);
        }
        return $output;
     }
     /**
      * Функция проверки актуальности данных журнала
      *
      * @param  array    $bidsData      Массив данных системы  о заявке
      * @param  array    $journalData   Массив данных записи в журнале 
      *
      * @return string   $output[0]     Код сооветствия 
      *                                 если $output[0] == 0 - данные журнала актуальны (соответствуют данным системы о заявке)
      *                                 если $output[0] == 1 - данные журнала были не актуальны (не соответствовали данным системы 
      *                                                        о заявке) и были приведены в соответствие
      *                                 если $output[0] == 2 - критическое несоответствие данных журнала и данных системы о заявке 
      *                                                        (произошла критическая ошибка)
      * @return string   $output[1]     Номер проверенной заявки 
      */
     protected function CheckRelevance($bidsData, $journalData) {
        $result[0] = 0;
        if ((!$bidsData["State"]) && ($journalData["State"] >= 40)) {     //Заявка открыта 
           $output[0] = 2;                                                //при этом в журнал записана как закрытая 
           return $output;                                               //следовательно, критическая ошибка
        }
        foreach ($bidsData as $key => $value) {
           if (($value != $journalData[$key]) && ($key != "State")) {     //Данные, полученные от системы и из журнала, не соответствуют друг другу
              if (($key == "Opentime") || ($key == "Direction")) {        //Это данные о времени открытия заявки или о ее направлении
                 $result[0] = 2;                                          //следовательно, критическая ошибка
              } else {                                                    //Это данные не о времени открытия заявки или о ее направлении
                 $result[0] = 1;                                          //следовательно, надо внести изменения в журнал
              }
              $result[1][$key]["bids"]    = $value;                       //Готовим данные для отчета в протоколе и легенде
              $result[1][$key]["journal"] = $journalData[$key];
           }
        }
        $this->protocol->AddMessage($this->pid, array("wr" => $result));  //Записываем отчет в протокол и легенду
        if ($result[0] != 0) {
           $this->AddToLegend($journalData["BidID"], array("wr" => $result));
           if ($result[0] == 1) {
              foreach ($result[1] as $key => $value) {                    //Готовим данные для записи в журнал
                 $input[$key] = $value["bids"];
              }
              $this->CorrectScript($journalData["BidID"], $input);        //Записываем данные в журнал
           }
        }
        $output[0] = $result[0];
        $output[1] = $journalData["BidID"];
        return $output;
     }
  }
?>