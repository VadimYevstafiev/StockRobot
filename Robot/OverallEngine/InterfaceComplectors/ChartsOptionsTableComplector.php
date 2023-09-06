<?php
  /**
   * ChartsOptionsTableComplector: Класс комплектатора таблицы опций графиков
   */
  class ChartsOptionsTableComplector extends OptionsTableComplector {
     /**
      * @param object  $protocol      Комплектатор таблицы протоколов
      */
     public function __construct ($protocol) {
        parent::__construct($protocol);
        $this->options = Configurations::GetChartsOptionsConfigurations();
        $this->definition = " опций графиков";
     }
     /**
      * Функция исполнения комплектации таблиц опций графиков
      */
     protected function ExecuteComplete() {
        foreach ($this->options as $type => $mainTable) {
           foreach ($mainTable as $timeframe => $table) {
              $this::CompleteOneTable($type, $timeframe);
           }
        }
     }
     /**
      * Функция комплектации содержимого одной таблицы опций графиков
      *
      * @param string  $key            Ключ подмассива массива конфигурации таблиц опций графиков ("Simple" или "Summary")
      * @param string  $timeframe      Таймфрейм, данные для которого записываются
      *
      */
     private function CompleteOneTable($key, $timeframe) {
        $options = $this->options[$key][$timeframe];
        $this->definition = $options["Definition"];
        $this->protocol->AddArray("subdec", $this->definition);
        $result = $this::CompleteOneTableContent($key, $timeframe);
        $this::Validation($options["Tablename"], $options["columnName"], $options["columnType"]);
        $this::AddData($options["Tablename"], $options["columnName"], $result);
     }
     /**
      * Функция комплектации содержимого одной таблицы опций графиков
      *
      * @param string  $key            Ключ подмассива массива конфигурации таблиц опций графиков ("Simple" или "Summary")
      * @param string  $timeframe      Таймфрейм, данные для которого записываются
      *
      */
     private function CompleteOneTableContent($key, $timeframe) {
        for ($i = 0; $i < count($this->structure[$key][$timeframe]); $i++) {
           $output[$i] = array($i);
           $result["chartsTypology"] = array();
           $result["chartsOptions"] = array();
           $result["AxesTypology"] = array();
           $structureItem = $this->structure[$key][$timeframe][$i];
           $counter = 0;
           for ($j = 0; $j < count($structureItem); $j++) {
              $value = $structureItem[$j];
              $source = $this->tabledata[$key][$value][$timeframe];
              for ($z = 0; $z < count($source["chartsTypology"]); $z++) {
                 if (is_array($source["chartsTypology"][$z])) {
                    for ($y = 0; $y < count($source["chartsTypology"][$z]); $y++) {
                       $result["chartsTypology"][] = $counter;
                    }
                 } else {
                    $result["chartsTypology"][] = $counter;
                 }
                    $result["chartsOptions"][] = $this::CompleteChartsOptionsItem($source["chartsOptions"][$z],
                                                 $source["chartsOptionsItems"]);
                 $counter++;
              }
              $result["AxesTypology"] = array_merge($result["AxesTypology"], $source["AxesTypology"]);
           }
           foreach ($result as $value) {
              $output[$i][] = json_encode($value, JSON_UNESCAPED_UNICODE);
           }
        }
        return $output;
     }
     /**
      * Функция комплектации значений опций линий графика
      *
      * @param array   $inputarray       Массив значений опций линий графика
      * @param integer $inputitems       Массив имен опций линий графика
      *
      * @return array                    Массив с данными
      */
     private function CompleteChartsOptionsItem($inputarray, $inputitems) {
        for ($i = 0; $i < count($inputarray); $i++) {
           $output[$inputitems[$i]] = $inputarray[$i];
        }
        return $output;
     }
  }
?>

