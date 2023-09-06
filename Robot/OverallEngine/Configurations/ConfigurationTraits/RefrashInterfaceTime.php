<?php
  /**
   * RefrashInterfaceTime: Трейт, определяющий  массив начальных меток времени, с которых нужно извлечь данные
   *                       в таблицы данных графиков, и функции его установки и получения
   */
  trait RefrashInterfaceTime {
     /**
      * @param array  $refrashInterfaceTime    Массив начальных меток времени, с которых нужно извлечь данные
      *                                        в таблицы данных графиков
      */
     private $refrashInterfaceTime;
     /**
      * Функция установки массива начальных меток времени, с которых нужно извлечь данные
      * в таблицы данных графиков
      *
      * @param array   $input   Массив начальных меток времени, с которых нужно извлечь данные
      *                         в таблицы данных графиков
      */
     static public function SetRefrashInterfaceTime($input) {
        self::SetConfigurations();
        self::$instance->refrashInterfaceTime = $input;
     }     /**
      * Функция получения массива начальных меток времени, с которых нужно извлечь данные
      * в таблицы данных графиков
      *
      * @return array        Массив начальных меток времени, с которых нужно извлечь данные
      *                      в таблицы данных графиков
      */
     static public function GetRefrashInterfaceTime() {
        self::SetConfigurations();
        return self::$instance->refrashInterfaceTime;
     }
  }
?>