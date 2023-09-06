<?
  /**
   * ProtocolInterface: Производный класс функций отображения интерфейса протоколов
   */
  class ProtocolInterface extends InterfaceFunctions {

     protected function __construct () {
        $this->name  = "Протоколы";
     }
     /**
      * Функция загрузки скриптов JavaScript
      */
     protected function AddScriptToHead() {
        $output = '
              <script type="text/javascript" src="JavaScript/GetData.js"></script>
              <script type="text/javascript" src="JavaScript/ChangeSelect.js"></script>
              <script type="text/javascript" src="JavaScript/Protocols/ChangeSidebar.js"></script>
              <script type="text/javascript" src="JavaScript/Protocols/MainProcess.js"></script>';
        return $output;
     }
     /**
      * Служебная функция отображения заголовка интерфейса
      */
     protected function ServiceShowHeader() {
        $output = parent::ServiceShowHeader();
        $output .= '
                          <td>
                             <input type="radio" name="protype" value="0" onchange="ChangeSidebar()">Слушатель
                          </td>
                          <td>
                             <input type="radio" name="protype" checked value="1" onchange="ChangeSidebar()">Эксперт
                          </td>
                          <td>
                             <input type="radio" name="protype" value="2" onchange="ChangeSidebar()">Торговец
                          </td>
                          <td>
                             <input type="radio" name="protype" value="3" onchange="ChangeSidebar()">Комплектатор интерфейса
                          </td>';
        return $output;
     }
  }
?>