
<?php

  /**
   * WMinterface: Базовый класс конструктора запросов и обработчика ответов 
   * интерфейсов сервиса Exchanger
   */  
  class WMinterface {
     use CURLfunctions {CURLfunctions::SendQuery as SendCURLquery;}
     protected $url;
     static protected $instance = NULL;

     protected function __construct () {
     }
     /**
      * Функция отправки запроса и обработки ответа 
      *
      * @param object  $query        Экземпляр конструктора
      *
      * @return array                Массив полученных значений
      */
     static protected function SendQuery($query) {
        $url = $query->ConstructRequest();
        if (!is_array($url)) {
           $response = self::SendCURLquery($url, NULL);
        } else {
           $response = self::SendCURLquery($url["URL"], $url["PostData"]);
        }
        if (!$response) {
           $output = NULL;
        } else {
           $output = $query->ParseReponse($response);
        }
        return $output;
     }
     /**
      * Конструктор запроса
      */
     protected function ConstructRequest() {
     }
     /**
      * Обработчик ответа
      *
      * @param  string  $response  Ответ сервиса в формате XML
      *
      * @return object             Объект SimpleXMLElement
      */
     protected function ParseReponse($response) {
        $response = new SimpleXMLElement($response);
        return $response;
     }
 }
?>

