<?php
  /**
   * InterSQLconnector: Класс фунций соединения и запросов к базе данных
   */
  class InterSQLconnector {
     private $dbc;

     /**
      * Функция соединения с базой данных
      * @param string  $host        Имя хоста или IP-адресом
      * @param string  $user        Имя пользователя
      * @param string  $password    Пароль
      * @param string  $name        Имя базы данных
      *
      * @return                     Идентификатор соединения
      */
     public function __construct($host, $user, $password, $name) {
        try {
           $this->dbc = new mysqli($host, $user, $password, $name);
           $this->dbc->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
           $this->dbc->query("SET CHARACTER SET 'utf8'");
        } catch (Exception $e) {
           throw new Exception("Не удалось подключиться к базе данных");
        }
     }
     /**
      * Функция закрытия соединения с базой данных
      *
      * @param         $dbc         Идентификатор соединения
      */
     public function __destruct() {
        $this->dbc->close();
     }
     /**
      * Функция запроса
      *
      * @param string  $query        Текст запроса
      *
      * @return                      Результат запроса
      */
     public function SendQuery($query) {
        try {
           $data = $this->dbc->query($query);
        } catch (Exception $e) {
           throw new Exception("Не удалось выполнить запрос");
        }
        return $data;
     }
  }
?>