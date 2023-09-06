<?php
  /**
   * WMsecureInterface: Базовый класс конструктора запросов и обработчика ответов 
   * защищенных интерфейсов сервиса Exchanger
   */  
  class WMsecureInterface extends WMinterface {
     protected $wmid;
     protected $signer;
     protected $signstr;

     /**
      * @param string  $wmid            WMID, данные которого запрашиваются
      * @param object  $signer          Экземпляр модуля аутентификации WMSigner
      * @param string  $signstr         Цифровая подпись
      */
     protected function __construct ($wmid, $signer) {
        $this->wmid     = $wmid;
        $this->signer   = $signer;
        $this->signstr  = $this->ConstructSignature();
     }
     /**
      * Конструктор запроса
      *
      * @return array               Массив ключей для отправки запроса
      */
     protected function ConstructRequest() {
        $output = array();
        $output["URL"] = $this->url;
        $output["PostData"] = $this->ConstructXML($this->ConstructDataArray());
        return $output;
     }
     /**
      * Конструктор XML запроса
      *
      * @return object          Объект XML
      */
     protected function ConstructXML($array, $xml = false) {
        if($xml === false){
           $xml = new SimpleXMLElement('<wm.exchanger.request/>');
        }
        foreach($array as $key => $value){
           if(is_array($value)){
              $this->ConstructXML($value, $xml->addChild($key));
           }else{
              $xml->addChild($key, $value);
           }
        }
        return $xml->asXML();
     }
     /**
      * Конструктор цифровой подписи
      */
     protected function ConstructSignature() {
     }
     /**
      * Конструктор массива данных для передачи в запросе
      *
      * @return array            Массив данных для передачи в запросе
      */
     protected function ConstructDataArray() {
        $output = array(
                        "wmid"         => $this->wmid,
                        "signstr"      => $this->signstr
        );
        return $output;
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
        $response = self::SendCURLquery($url["URL"], $url["PostData"]);
        if (!$response) {
           $output = NULL;
        } else {
           $output = $query->ParseReponse($response);
        }
        return $output;
     }
     /**
      * Обработчик ответа
      *
      * @param string  $response  Ответ сервиса в формате XML
      *
      * @return array             Массив полученных данных
      */
     protected function ParseReponse($response) {
        $response = parent::ParseReponse($response);
        $output = array();
        $output["retval"] = (string) $response->retval;                    //код выполнения
        if ($output["retval"] == 0) { 
           $output = $this->DescribeResults($output, $response); 
        } else {
           $output = $this->DescribeErrors($output, $response); 
        }
        return $output;
     }
     /**
      * Конструктор массива для передачи полученных данных в случае успешного выполнения запроса
      *
      * @param  array  $output   Массив для передачи полученных данных
      * @param  object $response Объект SimpleXMLElement
      *
      * @return array            Массив для передачи полученных данных
      */
     protected function DescribeResults($output, $response) {
        return $output;
     }
     /**
      * Конструктор массива для передачи полученных данных в случае неуспешного выполнения запроса
      *
      * @param  array  $output   Массив для передачи полученных данных
      * @param  object $response Объект SimpleXMLElement
      *
      * @return array            Массив для передачи полученных данных
      */
     protected function DescribeErrors($output, $response) {
        $output["retdesc"] = (string) $response->retdesc;                  //расшифровка кода выполнения
        return $output;
     }
  }
?>

