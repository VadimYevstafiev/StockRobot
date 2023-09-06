<?php
  /**
   * InterfaceData: Класс функций заполнения таблиц данных интерфейса
   */  
  class InterfaceData extends MainModule {
     /**
      * @param array   $objects       Массив комплектаторов опций интерфейса графиков
      */
     protected $objects = array("BoxOptionsTableComplector", "ChartsOptionsTableComplector");
     /**
      * Конструктор
      */
     protected function __construct () {
        $this->flag      = "interface";
        $connect         = MySQLconnector::GetConnect();
        $this->dbc       = $connect;
     }
     /**
      * Служебная функция обновления данных эксперта
      *
      * @param object  $protocol    Комплектатор таблицы протокола
      *
      */
     protected function ServiceComplete($protocol) {
        for ($i = 0; $i < count($this->objects); $i++) {
           $object = new $this->objects[$i]($protocol);
           $object->Complete();
        }
        $datetimes = Configurations::GetDatetimes();
        $options = Configurations::GetChartsDataConfigurations();
        $indexis = Configurations::GetIndexis();
        $indexis = $indexis["general"];
        $refrashtime = Configurations::GetRefrashInterfaceTime();
        if (!isset($refrashtime)) {
           throw new Exception("Отсутствует массив начальных меток времени, с которых нужно извлечь данные.");
        }
        foreach ($options as $flag => $mainTable) {
           foreach ($mainTable as $timeframe => $table) {
              for ($i = 0; $i < count($table); $i++) {
                 $service = $this::CompleteServiceDatetimes($timeframe, $datetimes);
                 $object = new ChartsDataTableComplector($this->dbc["work"], 
                                                         $this->dbc["interface"], 
                                                         $protocol,
                                                         $table[$i], 
                                                         $service, 
                                                         $refrashtime[$flag][$timeframe][$i]);
                 $object->Complete($flag);
              }
           }
        }
     }
  }
?>