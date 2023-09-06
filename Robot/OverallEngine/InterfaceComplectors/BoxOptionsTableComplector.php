<?php
  /**
   * BoxOptionsTableComplector: Класс комплектатора таблицы опций боковой панели графиков
   */
  class BoxOptionsTableComplector extends OptionsTableComplector {
     /**
      * @param object  $protocol      Комплектатор таблицы протоколов
      */
     public function __construct ($protocol) {
        parent::__construct($protocol);
        $this->options = Configurations::GetBoxOptionsConfigurations();
        $this->definition = " опций боковой панели графиков";
     }
     /**
      * Функция исполнения комплектации таблиц опций боковой панели графиков
      */
     protected function ExecuteComplete() {
        $result = array();
        foreach ($this->structure as $key => $tables) {
           $result[] = $this::CompleteRow($key, $tables);
        }
        $this::Validation($this->options["Tablename"], 
                          $this->options["columnName"],
                          $this->options["columnType"]);
        $this::AddData($this->options["Tablename"], $this->options["columnName"], $result);
     }
     /**
      * Функция комплектации содержимого строки данных
      *
      * @param string  $key        Кюч подмассива массива структуры графиков "Simple" или "Summary"
      * @param array   $tables     Подмассив массива структуры графиков (с ключом "Simple" или "Summary")
      *
      * @return array              Массив строк JSON с данными для боковой панели интерфейса
      */
     private function CompleteRow($key, $tables) {
        $output[0] = $key;
        for ($i = 1; $i < count($this->options["columnName"]); $i++) {
           $j = $this->options["columnName"][$i];
           $output[$i] = $this::CompleteBoxSell($tables[$j], $this->tabledata[$key], $j);
        }
        return $output;
     }
     /**
      * Функция комплектации содержимого одной ячейки данных для боковой панели графиков
      *
      * @param array   $structureItem  Элемент подмассива массива структуры графиков (с ключом "Simple" или "Summary"),
      *                                соответствующий таймфрейму, данные для которого записываются
      * @param array   $tabledataItem  Подмассив массива конфигурации таблиц баз данных и их обработчиков,
      *                                соответствующий таймфрейму, данные для которого записываются
      * @param array   $timeframe      Таймфрейм, данные для которого записываются
      *
      * @return string                 Строка JSON с данными для боковой панели интерфейса
      */
     private function CompleteBoxSell($structureItem, $tabledataItem, $timeframe) {
        $output[0] = array();
        $output[1] = array();
        for ($i = 0; $i < count($structureItem); $i++) {
           $output[0][$i] = array();
           $output[1][$i] = array();
           $counter = 0;
           for ($j = 0; $j < count($structureItem[$i]); $j++) {
              $service = array();
              $value = $structureItem[$i][$j];
              $source = $tabledataItem[$value][$timeframe];
              for ($z = 0; $z < count($source["boxValues"]); $z++) {
                 if (is_array($source["boxValues"][$z])) { 
                    $str = "";
                    for ($y = 0; $y < count($source["boxValues"][$z]); $y++) {
                       $counter++;
                       $str .= strval($counter) . ", ";
                    }
                    $service[] = substr($str, 0, -2);
                 } else {
                    $counter++;
                    $service[] = strval($counter);
                 }
              }
              $output[0][$i] = array_merge($output[0][$i], $source["boxItems"]);
              $output[1][$i] = array_merge($output[1][$i], $service);
           }
        }
        $output = json_encode($output, JSON_UNESCAPED_UNICODE);
        return $output;
     }
  }
?>

