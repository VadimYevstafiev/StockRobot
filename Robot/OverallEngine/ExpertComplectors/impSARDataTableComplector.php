<?php
  /**
   * impSARDataTableComplector: Производный класс комплектатора таблиц параболического SAR
   */
  class impSARDataTableComplector extends BaseSummaryTableComplector {
     use PropertySERVICETIMESTAMP;
     /**
      * Функция определения начальной метки времени, с которой надо дописать данные
      * Дополнительно определяет значения свойства $serviceTimestamp
      *
      * @param array $input         Массив результатов валидации
      *
      * @return double              Начальная метка времени, с которой надо дописать данные
      */
     protected function DefineTimefactor($input) {
        $output = $input[1] - $this->tabledata["Timeframe"] * $this->tabledata["serviceperiod"] * $this->tabledata["timemodule"];
        if (!$this->new) {
           $this->serviceTimestamp = $input[1] - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
        }
        return $output;
     }
     /**
      * Функция извлечения данных
      *
      * @param double  $timefactor   Начальная метка времени, с которой нужно извлечь данные
      * @param array   $sourceArray  Массив имен таблиц, из которых нужно извлечь данные
      * @param array   $valueArray   Массив имен столбцов таблиц, из которых нужно извлечь данные
      * @param integer $index        Тип обмена
      *
      * @return array                Массив данных, извдеченных из таблиц
      */
     protected function ExtractData($timefactor, $sourceArray, $valueArray, $index) { 
        $data = parent::ExtractData($timefactor, $sourceArray, $valueArray, $index);
        for ($i = 0; $i < count($data[0]); $i++) {
           for ($j = 0; $j < count($data[0][$i]); $j++) {
              $output[$j][$i] = $data[0][$i][$j];
           }
        }
        return $output;
     }
     /**
      * Вспомогательная функция обработки данных
      *
      * @param array   $input      Массив извлеченных данных
      * @param string  $tablename  Имя таблицы
      *
      * @return array              Массив значений для записи в таблицу
      */
     protected function СalculateData($input, $tablename) {
        $i = $this->tabledata["serviceperiod"];
        $controlnumber = $this->tabledata["serviceperiod"];
        if ($this->new) {
           $new        = TRUE;
           $position   = TRUE;
           $startSAR   = $this::FindStartSAR($position, $input, (0));
           $startEP    = $this::FindStartEP($position, $input, $i);
           $startAF    = $this->tabledata["AF"];
           $total      = 0;
           $true       = 0;
        } else {
           $new        = FALSE;
           $value      = $this->ExtractRow($tablename);
           $startSAR   = $value["SAR"];
           $startEP    = $value["EP"];
           $startAF    = $value["curAF"];
           $position   = $value["position"];
        }
        while ($i < count($input[0])) {
           $result = $this::CreateParabola($new, $input, $i, $position, $startSAR, $startEP, $startAF);
           // $result[0][0] = 0 - стартовое значение $position неверное, нужно пересчитать с разворотом $position
           // $result[0][0] = 1 - парабола SAR досчитана до конца, начинаем новую с разворотом $position
           // $result[0][0] = 2 - парабола SAR не досчитана до конца

           if ($result[0][0] == 0) {
              if ($position) {
                 $position = FALSE;
              } else {
                 $position = TRUE;
              }
              $startSAR = $this::FindStartSAR($position, $input, ($i - $this->tabledata["serviceperiod"]));
              $startEP = $this::FindStartEP($position, $input, $i);
              $startAF = $this->tabledata["AF"];
              if ($position) {
                 if ($startSAR > $input[1][$i]) {
                    $startSAR = $input[1][$i] - 0.0001; 
                 }
              } else {
                 if ($startSAR < $input[2][$i]) {
                    $startSAR = $input[2][$i] + 0.0001; 
                 }
              }
              $new = TRUE;
           } else {
              $countResult = count($result[1]);
              $endnumber = $result[1][($countResult - 1)][0];

              if ($result[1][0][2]) {
                 $servicePosition = FALSE;
              } else {
                 $servicePosition = TRUE;
                  
              }
              if ($new) {
                 $i = $result[1][0][1] + 1;               // $numberEP + 1
                 $AF = $result[1][($countResult - 1)][6];
              } else {
                 $i = $result[1][0][1];                   // $numberEP
              }
              $new = TRUE;

              $servicestart = $result[1][0][1] - $this->tabledata["serviceperiod"] + 1;
              $servicecount = $endnumber - $servicestart + 2;
              if ($servicecount > 3) {
                 $service[0] = array_slice ($input[0], $servicestart, $servicecount);
                 $service[1] = array_slice ($input[1], $servicestart, $servicecount);
                 $service[2] = array_slice ($input[2], $servicestart, $servicecount);
                 $j = $this->tabledata["serviceperiod"];
                 while ($j < count($service[0])) {
                    $serviceStartSAR = $this::FindStartSAR($servicePosition, $service, ($j - $this->tabledata["serviceperiod"]));
                    $serviceStartEP = $this::FindStartEP($servicePosition, $service, $j);
                    $serviceStartAF = $this->tabledata["AF"];

                    $serviceresult = $this::CreateParabola(TRUE, $service, $j, $servicePosition, $serviceStartSAR, $serviceStartEP, $serviceStartAF);
 
                    if ($serviceresult[0][0] == 0) {
                       $j += 1;
                       $i += 1;
                    } else {
                       $j += count($serviceresult[1]) + 1;
                       if ($serviceresult[0][0] == 1) {
                          $i = $result[1][0][1] + count($serviceresult[1]) + 1;
                       }
                    } 
                 }
              } // end if ($servicecount > 3)
              if ($position) { 
                 $signal = 1;
              } else {
                 $signal = - 1;
              }

              if ($result[1][0][2]) {
                 $position = FALSE;
              } else {
                 $position = TRUE;
              }

              $startSAR = $this::FindStartSAR($position, $input, ($i - 1));
              $startEP = $this::FindStartEP($position, $input, $i);
              $startAF = $this->tabledata["AF"];

              for ($j = 0; $j < $countResult; $j++) {
                 $number = $result[1][$j][0] - $this->tabledata['serviceperiod'];
                 $output[$number][0] = $result[1][$j][3];          //"timestamp"
                 if ($result[1][$j][0] >= $controlnumber) {
                    $output[$number][1] = $result[1][$j][4];       //"SAR"
                 }
                 $output[$number][2] = $result[1][$j][4];          //"shadow"
                 $output[$number][3] = $result[1][$j][5];          //"EP"
                 $output[$number][4] = $result[1][$j][6];          //"curAF" 
                 $output[$number][5] = $result[1][$j][2];          //"position"
                 if ($result[1][$j][0] >= $controlnumber) {
                    $output[$number][6] = $signal;                 //"signal"
                 }
              }

              $controlnumber = $endnumber + 1; 
              if ($result[0][0] == 2) {
                 $i = $result[1][($countResult - 1)][0] + 1; 
                 $output[$number + 1][0] = $output[$number][0] + $this->tabledata['Timeframe'] * $this->tabledata["timemodule"]; //"timestamp"
                 $output[$number + 1][1] = $result[0][1];                                                                        //"SAR"
                 $output[$number + 1][2] = $result[0][1];                                                                        //"shadow" 
                 $output[$number + 1][5] = $output[$number][5];                                                                  //"position"
                 $output[$number + 1][6] = $output[$number][6];                                                                  //"signal"
              }
           }
        }
        return $output;
     }
     /**
      * Функция построения единичной параболической последовательности
      *
      * @param bool    $new           Индикатор, строится ли последовательность заново или достаивается существующая
      * @param array   $data          Массив значений, по которым строится последовательность
      * @param integer $startnumber   Номер строки в масиве $data, с которого начинается (продолжается) строиться последовательность
      * @param bool    $position      Индикатор, направления параболической последовательности (рост/падение)
      * @param double  $startSAR      Начальное значение SAR
      * @param double  $startEP       Начальное значение EP
      * @param double  $startAF       Начальное значение AF
      *
      * @return array                 Массив значений единичной параболической последовательности
      */
     private function CreateParabola($new, $data, $startnumber, $position, $startSAR, $startEP, $startAF) {
        $AF = $this->tabledata["AF"];
        $maxAF = $this->tabledata["maxAF"];
        $curAF[-1] = $startAF;
        $EP[-1] = $startEP;
        if ($new) {
           $SARvalue = $startSAR;
        } else {
           $SARvalue = $this::CalculateSARvalue($startSAR, $curAF[-1], $EP[-1]);
        }
        $numberEP = $startnumber;
        $reverse = FALSE;
        $i = 0;
        $count = count($data[0]) - $startnumber;
        while ((!$reverse) && ($i < $count)) {
           $todayMin = $data[1][$i + $startnumber]; //Минимум дня $i + $startnumber ("сегодня")
           $todayMax = $data[2][$i + $startnumber]; //Максимум дня $i + $startnumber ("сегодня")
           if ($position) { // Если позиция длинная
              if ($SARvalue > $todayMin) {
                 $reverse = TRUE;
              } else {
                 $SAR[$i] = $SARvalue;
                 $curAF[$i] = $curAF[$i - 1];
                 if ($todayMax > $EP[$i - 1]) {
                    $EP[$i] = $todayMax;
                    $numberEP = $i + $startnumber;
                    if (intval($curAF[$i - 1] * 100) != intval($maxAF * 100)) {
                       $curAF[$i] = $curAF[$i - 1] + $AF;
                    }
                 } else {
                    $EP[$i] = $EP[$i - 1];
                 }
              }
           } else { // Если позиция короткая
              if ($SARvalue < $todayMax) {
                 $reverse = TRUE;
              } else {
                 $SAR[$i] = $SARvalue;
                 $curAF[$i] = $curAF[$i - 1];
                 if ($todayMin < $EP[$i - 1]) {
                    $EP[$i] = $todayMin;
                    $numberEP = $i + $startnumber;
                    if (intval($curAF[$i - 1] * 100) != intval($maxAF * 100)) {
                       $curAF[$i] = $curAF[$i - 1] + $AF;
                    }
                 } else {
                    $EP[$i] = $EP[$i - 1];
                 }
              }
           }  // end if ($position)
           if (!$reverse) {
              $Min = min(array_slice ($data[1], ($i + $startnumber - 1), 2)); //Меньший из минимумов дней $i + $startnumber - 1 и $i + $startnumber  ("сегодня" и "вчера")
              $Max = max(array_slice ($data[2], ($i + $startnumber - 1), 2)); //Больший из максимумов дней $i + $startnumber - 1 и $i + $startnumber ("сегодня" и "вчера")
              $SARvalue = $this::CalculateSARvalue($SAR[$i], $curAF[$i], $EP[$i]);
              $i++;
              if ($position) { // Если позиция длинная
                 if ($SARvalue > $Min) {
                    $SARvalue = $Min;
                 }
              } else { // Если позиция короткая
                 if ($SARvalue < $Max) {
                    $SARvalue = $Max;
                 }
              }
           }
        } // end while

        if ($i > 0) {
           if (!$reverse) {
              $output[0][0] = 2;
              $output[0][1] = $SARvalue;
           } else {
              $output[0][0] = 1;
           }
           for ($j = 0; $j < $i; $j++) {
              $output[1][$j][0] = $startnumber + $j;
              $output[1][$j][1] = $numberEP;
              $output[1][$j][2] = $position;
              $output[1][$j][3] = $data[0][$startnumber + $j];
              $output[1][$j][4] = $SAR[$j];
              $output[1][$j][5] = $EP[$j];
              $output[1][$j][6] = $curAF[$j];
           }
        } else {
           $output[0][0] = 0;
        }
        return $output;
     }
     /**
      * Функция определения текущего значения SAR
      *
      * @param double  $prevSARvalue  Предыдущее значение SAR
      * @param double  $AF            Текущее значение AF
      * @param double  $EP            Текущее значение EP
      *
      * @return double                Текущее значение SAR
      */
     private function CalculateSARvalue($prevSARvalue, $AF, $EP) {
        $output = round(($prevSARvalue + $AF * ($EP - $prevSARvalue)), 4);
        return $output;
     }
     /**
      * Функция  определения начального значения SAR
      *
      * @param bool    $position      Индикатор, направления параболической последовательности (рост/падение)
      * @param array   $data          Массив значений, по которым строится последовательность
      * @param integer $startnumber   Номер строки в масиве $data, с которого начинается (продолжается) строиться последовательность
      *
      * @return double                Начальное значение SAR
      */
     private function FindStartSAR($position, $data, $startnumber) {
        if ($position) {
           $output = min(array_slice ($data[1], $startnumber, $this->tabledata["serviceperiod"]));
        } else {
           $output = max(array_slice ($data[2], $startnumber, $this->tabledata["serviceperiod"]));
        }
        return $output;
     }
     /**
      * Функция  определения начального значения EP
      *
      * @param bool    $position      Индикатор, направления параболической последовательности (рост/падение)
      * @param array   $data          Массив значений, по которым строится последовательность
      * @param integer $startnumber   Номер строки в масиве $data, с которого начинается (продолжается) строиться последовательность
      *
      * @return double                Начальное значение EP
      */
     private function FindStartEP($position, $data, $startnumber) {
        if ($position) {
           $output = $data[2][$startnumber];
        } else {
           $output = $data[1][$startnumber];
        }
        return $output;
     }
  }
?>

