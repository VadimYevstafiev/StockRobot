<?php
  /**
   * MySQLconnector: Класс фунций соединения и запросов к базе данных
   */
  class MySQLconnector {
     use AddRow, AddToSell, CorrectRow, CreateTable, DeleteAll, DeleteRow, DeleteTable, ExtractRow;
     /**
      * @param  object   $instance      Экземпляр соединения
      * @param  resource $dbc           Идентификатор соединения
      */
     static private $instance;
     private $dbc;
     /**
      * Функция установки соединения с базой данных
      */
     static public function SetConnect() {
        if (empty(self::$instance)) {
           $configuration = Configurations::GetConnectConfiguration();
           foreach ($configuration as $key => $value) {
              self::$instance[$key] = new self($value["host"], $value["user"], $value["password"], $value["name"]);
           }
        }
     }
     /**
      * Функция получения соединения с базой данных
      */
     static public function GetConnect() {
        self::SetConnect();
        return self::$instance;
     }
     /**
      * Функция закрытия соединения с базой данных
      */
     static public function UnsetConnect() {
        if (!empty(self::$instance)) {
           self::$instance = NULL;
        }
     }
     /**
      * Конструктор
      *
      * @param  string   $host          Имя хоста или IP-адресом
      * @param  string   $user          Имя пользователя
      * @param  string   $password      Пароль
      * @param  string   $name          Имя базы данных
      *
      * @return resource                Идентификатор соединения
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
      * @param resource  $dbc           Идентификатор соединения
      */
     public function __destruct() {
        $this->dbc->close();
     }
     /**
      * Функция запроса
      *
      * @param  object   $query         Экземпляр конструктора SQL-запросов
      *
      * @return                         Результат запроса
      */
     public function SendQuery($query) {
        try {
           $data = $this->dbc->query($query->GetQuery());
           $output = $query->ParseReponse($data);
        } catch (Exception $e) {
           throw new MySQLexception("Не удалось выполнить запрос.", $e);
        }
        return $output;
     }
  }
?>