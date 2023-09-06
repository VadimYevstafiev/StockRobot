<?php
  /**
   * RateDataTableComplector: Производный класс комплектатора рабочих таблиц данных о курсе 
   */
  class RateDataTableComplector extends DataTableComplector  {
     /**
      * Конструктор
      *
      * @param  object   $dbc           Идентификатор соединения
      * @param  object   $protocol      Комплектатор таблицы протокола
      * @param  array    $tabledata     Массив конфигурации таблицы
      * @param  array    $datetimes     Массив дат и времени
      */
     public function __construct ($dbc, $protocol, $tabledata, $datetimes) {
        parent::__construct($dbc, $protocol, $tabledata, $datetimes);
        $this->sdate   = $datetimes["servicestartdate"];
     }
     /**
      * Функция комплектации таблицы
      *
      * @param  array    $input         Массив результатов валидации
      */
     protected function ExecuteComplete($input, $tablename, $index) {
        switch ($input[0]) {
           case 0: 
              $count = 1;
              break;
           case 1: 
              $count = 2;
        }
        for ($i = 0; $i < $count; $i++) {
           $start = $input[(1 + $i * 2)];
           $finisch = $input[(2 + $i * 2)];
           while ($start < $finisch) {
              $start = $this::PrepaireData($tablename, $start, $finisch, $index);
           }
        } 
     }
     /**
      * Функция обработки данных
      *
      * @param  double   $start         Начальная метка времени, с которой надо дописать данные
      * @param  double   $finisch       Конечная метка времени, до которой надо дописать данные
      * @param  string   $index         Тип обмена
      *
      * @return double                  Следующую начальную метку времени, с которой надо дописать данные
      */
     private function PrepaireData($tablename, $start, $finisch, $index) {
        //Значение временного интервала данных таблицы 
        $interval = $this->tabledata["Timeframe"] * $this->tabledata["timemodule"];
        //Переменная-критерий группировки данных относящихся к одному временному интервалу
        //присваиваем ей начальное значение, равное начальной метке времени, с которой надо дописать данные
        $criteria = $start;
        $i = 0;  
        $j = 0;
   
        //Запрашиваем данные слушателя (не более 5000 строк)
        $data = Listener::ExtractData($index, $start, ">=", 5000);
        //Проверяем, есть ли в запрашиваемом диапазоне данные слушателя
        if (empty($data)) {
           //Если нет, проверяем, есть ли в базе данных слушателя данные за более ранний период
           $service = Listener::ExtractData($index, $start, "<", 1, TRUE);
           if (empty($service)) {
              //Если таких нет, прерываем выполнение функции и возвращаем значение конечной метки
              return $finisch;
           } else {
              //Если такие есть
              $prev = $service[0][1];
              //Дописываем данные до $finisch
              while ($criteria < $finisch) {
                 $result[$i] = $this->UsePreviousData($criteria, $prev);
                 $criteria = $criteria + $interval;
                 $i++;
              }  
              $this::AddData($result, $tablename);
              return $finisch;
           } //end if (!empty($service))        
        } //end if (empty($data))


        //Если !(empty($data)), 
        //проверяем, меньше ли начальная метка времени, с которой надо дописать данные,
        //начальной метки времени полученных данных слушателя
        if ($start < $data[0][0]) {
           //Если да, проверяем, есть ли в базе данных слушателя данные за более ранний период
           $service = Listener::ExtractData($index, $start, "<", 1, TRUE);
           if (!empty($service)) {
              //Если такие есть
              //Дописываем данные до $start 
              $criteria = $start - floor(($start - $service[0][0]) / $interval) * $interval; 
              $prev = $service[0][1];
              while ($criteria < $start) {
                 $result[$i] = $this->UsePreviousData($criteria, $prev);
                 $criteria = $criteria + $interval;
                 $i++;
              } 
           } //end if (!empty($service))
           $criteria = $start;
           //Проверяем, является ли начальная метка времени полученных данных слушателя 
           //больше конечной метке времени, до которой надо дописать данные
           if ($data[0][0] > $finisch) {
              //Если да
              if (!empty($prev)) {
                 //Дописываем данные до $finisch
                 while ($criteria < $finisch) {
                    $result[$i] = $this->UsePreviousData($criteria, $prev);
                    $criteria = $criteria + $interval;
                    $i++;
                 }
                 $this::AddData($result, $tablename);
              }
              //и возвращаем значение конечной метки
              return $finisch;
           } //end if ($data[0][0] > $finisch) 
           //Если нет
           //Увеличиваем значение переменной-критерия на величину временного интервала,
           //пока оно не станет больше начальной метки времени полученных данных слушателя
           $z = 0;
           while ($criteria < $data[0][0]) { 
              $criteria = $criteria + $interval;
              $z++;
           }
           //Проверяем, увеличивалась ли переменная-критерий больше одного раза
           if ($z > 1) { 
              if (!empty($prev)) {
                 //Если да, дописываем данные, используя последнее значение курса, до $criteria 
                 $control = $criteria - $interval;
                 $criteria = $start;
                 while ($criteria < $control) {
                    $result[$i] = $this->UsePreviousData($criteria, $prev);
                    $criteria = $criteria + $interval;
                    $i++;
                 }
                 $criteria = $control + $interval;
              }
           }
           //Отсеиваем те строки из полученных данных слушателя, где метка времени меньше $criteria
           $service = array();
           while (($data[$j][0] < $criteria) && ($j < count($data))) {
              $service[] = $data[$j][1];
              $j++; 
           }
           if (!empty($service)) {  
              $result[$i] = $this->СalculateIntervalData(($criteria - $interval), $service);
              $i++;
           } 
        } //end if ($start < $data[0][0])
        $count = count($data) - 1;
        if ($data[$count][0] >= $finisch) {
           $control = $finisch;
        } else {
           $control = $data[$count][0];
        }
        while ($criteria < $control) {
           $service = array();
           $timestamp = $criteria;
           $criteria = $criteria + $interval;
           while (($j <= $count) && ($data[$j][0] < $criteria))  {
              $service[] = $data[$j][1];
              $j++;
           }
           if (!empty($service)) {   
              $result[$i] = $this->СalculateIntervalData($timestamp, $service);       
           } else {
              $prev = $result[$i - 1][3];
              $result[$i] = $this->UsePreviousData($timestamp, $prev);
          }
           $i++;
        }
        if ($control != $finisch) { 
           $service = array_pop($result);
           $output = $service[0];
        } else {
           $output = $finisch;
        }
        $this::AddData($result, $tablename);
        return $output;
     }
     /**
      * Функция вычисления данных одного временного интервала
      *
      * @param  double   $timestamp     Метка времени интервала
      * @param  array    $input         Массив исходных данных
      *
      * @return array                   Массив данных одного временного интервала
      */
     private function СalculateIntervalData($timestamp, $input) {
        $output[0] = $timestamp;
        $output[1] = min($input);
        $output[2] = $input[0];
        $output[3] = $input[count($input) - 1];
        $output[4] = max($input);
        return $output;
     }
     /**
      * Функция вычисления данных одного временного интервала, используя предыдущее значение курса
      *
      * @param  double   $timestamp     Метка времени интервала
      * @param  double   $input         Предыдущее значение курса
      *
      * @return array                   Массив данных одного временного интервала
      */
     private function UsePreviousData($timestamp, $input) {
        $output[0] = $timestamp;
        $output[1] = $input;
        $output[2] = $input;
        $output[3] = $input;
        $output[4] = $input;
        return $output;
     }
  }  
?>

