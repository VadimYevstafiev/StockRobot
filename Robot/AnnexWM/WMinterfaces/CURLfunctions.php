
<?php
   /**
   * CURLfunctions: Трейт, определяющий функции использования библиотеки cURL
   */
   trait CURLfunctions {
      /**
      * Функция исполнения POST и GET запросов 
      *
      * @param string  $url          URL для передачи запроса
      * @param string  $post_data    Строка для передачи  методом POST
      *
      * @return array                Результат запроса
      */
     protected function SendQuery($url, $post_data) {
        $curl = curl_init($url);
        if(!$curl) {
           return FALSE;
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_FAILONERROR, TRUE);
        if($post_data) {
           curl_setopt($curl, CURLOPT_POST, TRUE);
           curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        } else {
           curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
        }
        $output = curl_exec($curl);
        if($output === FALSE) {
           return FALSE;
        }
        curl_close($curl);
        return $output;
     }
  }
?>