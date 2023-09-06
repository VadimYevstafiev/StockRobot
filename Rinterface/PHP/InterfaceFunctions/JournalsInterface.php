<?
  /**
   * JournalsInterface: Производный класс функций отображения интерфейса журналов
   */
  class JournalsInterface extends InterfaceFunctions {

     protected function __construct () {
        $this->name  = "Журналы";
     }
     /**
      * Функция загрузки скриптов JavaScript
      */
     protected function AddScriptToHead() {
        $output = '
              <script type="text/javascript" src="JavaScript/GetData.js"></script>
              <script type="text/javascript" src="JavaScript/ChangeSelect.js"></script>
              <script type="text/javascript" src="JavaScript/Journals/ChangeSidebar.js"></script>
              <script type="text/javascript" src="JavaScript/Journals/MainProcess.js"></script>';
        return $output;
     }
     /**
      * Служебная функция отображения заголовка интерфейса
      */
     protected function ServiceShowHeader() {
        $output = parent::ServiceShowHeader();
        $output .= '
                          <td>
                             <input type="radio" name="protype" value="0" onchange="ChangeSidebar()">Журнал торгов
                          </td>
                          <td>
                             <input type="radio" name="protype" checked value="1" onchange="ChangeSidebar()">Журнал заявок
                          </td>';
        return $output;
     }
  }
?>