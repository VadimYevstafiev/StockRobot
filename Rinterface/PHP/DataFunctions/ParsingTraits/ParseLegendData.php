<?php
  /**
   * ParseLegendData: Трейт, определяющий функцию подготовки текста легенды 
   */
  trait ParseLegendData {
     /**
      * Функция подготовки текста легенды 
      *
      * @param  string   $data          Содержание записи в легенде
      *
      * @return string                  Текст легенды 
      */
     private function ParseLegendData($data) {
        $data = json_decode($data, TRUE);
     echo '<p>$data</p>';
     print_r($data);
        $output = "";
        foreach ($data as $value) {
           $output .= self::MakeHead($value[0]);
           $output .= self::WriteCurrentPrice($value[1]);
           $output .= self::WriteExpertData($value[2]);
           for ($i = 3; $i < count($value); $i++) {
              $output .= self::ParseLegendItem($value[$i]);
           }
        }
        return $output;
     }
     /**
      * Функция подготовки текста элемента легенды 
      *
      * @param  string   $data          Содержание записи в легенде
      *
      * @return string                  Текст элемента легенды 
      */
     private function ParseLegendItem($data) {
        if (is_array($data)) {
           $output = "";
           foreach ($data as $key => $value) {
              switch ($key) {
                 case "wm":
                    $output .= self::ParseWMbids($value);
                    break;
                 case "bj":
                    $output .= self::ParseBidsJournalData($value);
                    break;
                 case "una":
                    $output .= self::MakeItalic("К заявке присоединена заявка № " . $value . ".");
                    break;
                 case "unp":
                    $output .= self::MakeItalic("Заявка присоединена к заявке № " . $value . ".");
                    break;
                 case "sn":
                    $output .= self::MakeItalic("Заявка № " . $value . " переопределена как опорная.");
                    break;
              }
           }
        }
        return $output;
     }
  }
?>