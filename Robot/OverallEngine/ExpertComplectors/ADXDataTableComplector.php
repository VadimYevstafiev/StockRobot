<?php
  /**
   * ADXDataTableComplector: Производный класс комплектатора таблиц Индекса средней направленности (ADX)
   */
  class ADXDataTableComplector extends DerivedDataTableComplector {
     use PropertyNEW, PropertySERVICETIMESTAMP, EMADataFunctions;
     /**
      * Функция определения начальной метки времени, с которой надо дописать данные
      * Дополнительно определяет значения свойств $new и $serviceTimestamp
      *
      * @param integer $input[0]   Код результата поствалидации
      * @param double  $input[1]   Начальная метка времени, с которой надо дописать данные
      * @param double  $input[2]   Конечная метка времени, до которой надо дописать данные
      *
      * @return double             Начальная метка времени, с которой надо дописать данные
      */
     protected function DefineTimefactor($input) {
        $this->new = $this::SetNEW($input);
        $output = $input[1];
        if ($this->new) {
           $output += - $this->tabledata["Timeframe"] * $this->tabledata["serviceperiod"] * $this->tabledata["timemodule"];
        } else {
           $output += - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
           $this->serviceTimestamp = $input[1] - $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
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
        $j = 0;
        $data = $input[0];
        unset($input);
        for ($i = 1; $i < count($data); $i++) {
           $timestamp[$j] = $data[$i][0];
           $DMminus[$j] = 0;
           $DMplus[$j] = 0;
           if (!(($data[$i - 1][3] >= $data[$i][3]) && ($data[$i - 1][2] <= $data[$i][2]))) {
              if (!(($data[$i][3] - $data[$i - 1][3]) == ($data[$i - 1][2] - $data[$i][2]))) {
                 if (($data[$i][3] - $data[$i - 1][3]) > ($data[$i - 1][2] - $data[$i][2])) {
                    $DM = round(($data[$i][3] - $data[$i - 1][3]), 4);
                 } else {
                    $DM = round(($data[$i][2] - $data[$i - 1][2]), 4);
                 }
                 if ($DM > 0) {
                    $DMplus[$j] = $DM;
                 } else {
                    $DMminus[$j] = abs($DM);
                 }
              }
           }
           $TR[$j] = round(max(($data[$i][3] - $data[$i][2]), abs($data[$i][3] - $data[$i - 1][1]), abs($data[$i][2] - $data[$i - 1][1])), 4);
           $j++;
        }
        unset($data);

        if ($this->new) {
           $startADMminus = $this::newStartvalueEMA($DMminus, $this->tabledata["period"]);
           $startADMplus = $this::newStartvalueEMA($DMplus, $this->tabledata["period"]);
           $startATR = $this::newStartvalueEMA($TR, $this->tabledata["period"]);

           $timestamp = array_slice ($timestamp, ($this->tabledata["period"]));
           $DMminus = array_slice ($DMminus, $this->tabledata["period"]);
           $DMplus = array_slice ($DMplus, $this->tabledata["period"]);
           $TR = array_slice ($TR, $this->tabledata["period"]);
        } else {
           $value = $this->ExtractRow($tablename);
           $startADMminus = $value["ADMminus"];
           $startADMplus = $value["ADMplus"];
           $DIminus[-1] = $value["DIminus"];
           $DIplus[-1] = $value["DIplus"];
           $startATR = $value["ATR"];
           $startDX = $value["ADX"];
           $position[-1] = $value["position"];
        }

        $ADMminus = $this::createEMA($timestamp, $DMminus, $this->tabledata["period"], $startADMminus);
        $ADMplus = $this::createEMA($timestamp, $DMplus, $this->tabledata["period"], $startADMplus);
        $ATR = $this::createEMA($timestamp, $TR, $this->tabledata["period"], $startATR);

        for ($i = 0; $i < count($timestamp); $i++) {
           $DIminus[$i] = 100 * round(($ADMminus[$i][1] / $ATR[$i][1]), 4);
           $DIplus[$i] = 100 * round(($ADMplus[$i][1] / $ATR[$i][1]), 4);
           $DX[$i] = 100 * round((abs($DIplus[$i] - $DIminus[$i]) / ($DIplus[$i] + $DIminus[$i])), 4);
        }

        if ($this->new) {
           $startDX = $this::newStartvalueEMA($DX, $this->tabledata["period"]);
           $timestamp = array_slice ($timestamp, $this->tabledata["period"]);
           $ADMminus = array_slice ($ADMminus, $this->tabledata["period"]);
           $ADMplus = array_slice ($ADMplus, $this->tabledata["period"]);
           $DIminus = array_slice ($DIminus, $this->tabledata["period"]);
           $DIplus = array_slice ($DIplus, $this->tabledata["period"]);
           $DX = array_slice ($DX, $this->tabledata["period"]);
           $ATR = array_slice ($ATR, $this->tabledata["period"]);
        }

        $ADX = $this::createEMA($timestamp, $DX, $this->tabledata["period"], $startDX);
        $ADX[-1][1] = $startDX;
        if ($this->new) {
           $DIminus[-1] = $DIminus[0];
           $DIplus[-1] = $DIplus[0];
           $position[-1] = $position[0];
        }
        for ($i = 0; $i < count($timestamp); $i++) {
           $output[$i][0] = $timestamp[$i];
           $output[$i][1] = $ADMplus[$i][1];
           $output[$i][2] = $ADMminus[$i][1];
           $output[$i][3] = $DIplus[$i];
           $output[$i][4] = $DIminus[$i];
           $output[$i][5] = $ADX[$i][1];
           $output[$i][6] = $ATR[$i][1];
           if ($output[$i][3] < $output[$i][4]) {
              $position[$i] = FALSE;
           } else {
              $position[$i] = TRUE;
           }

           if ($ADX[$i][1] > $ADX[$i - 1][1]) {
              $deltaADX = 1;
           } else {
              $deltaADX = 0;
           }
           $integralFactor = $this::CheckIntegralFactor($ADX[$i][1], $deltaADX, $position[$i]);

           $output[$i][7] = $position[$i];
           $output[$i][8] = $integralFactor;
        }
        return $output;
     }
     /**
      * Функция определения наличия тренда
      *
      * @param double  $ADX        Текущее значение ADX
      * @param double  $deltaADX   Разность между текщим и предыдущим значениями ADX
      * @param bool    $position   Индикатор направления тренда (рост/падение)
      *
      * @return integer            Индикатор наличия тренда
      */
     private function CheckIntegralFactor($ADX, $deltaADX, $position) {
        if ($ADX > 25) {                                             //Если ADX > 25
           $output = 2;                                              //Есть тренд 
        } else {                                                     //Если ADX <= 25
           if ($deltaADX == 1) {                                     //Если ADX растет
              $output = 1;                                           //Возможно начало нового тренда
           } else {
              $output = 0;                                           //Флэт
           }
        }
        if ($position) {
           $output = $output;
        } else {
           $output = 0 - $output;
        }
        return $output;
     }
  }
?>