<?
  /**
   * ChartsInterface: Производный класс функций отображения интерфейса графиков
   */
  class ChartsInterface extends InterfaceFunctions {

     protected function __construct () {
        $this->name  = "Графики";
     }
     /**
      * Функция загрузки скриптов JavaScript
      */
     protected function AddScriptToHead() {
        $output = '
              <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
              <script type="text/javascript" src="JavaScript/GetData.js"></script>
              <script type="text/javascript" src="JavaScript/ChangeSelect.js"></script>
              <script type="text/javascript" src="JavaScript/Charts/Charts.js"></script>
              <script type="text/javascript" src="JavaScript/Charts/MainProcess.js"></script>
              <script type="text/javascript" src="JavaScript/Charts/ChangeSidebar.js"></script>';
        return $output;
     }
     /**
      * Служебная функция отображения заголовка интерфейса
      */
     protected function ServiceShowHeader() {
        $output = parent::ServiceShowHeader();
        $output .= '
                          <td>Тип обмена:
                             <input type="radio" name="type" checked value="0" onchange="ChangeSidebar()"> Сводный
                             <input type="radio" name="type" value="1" onchange="ChangeSidebar()"> Bid
                             <input type="radio" name="type" value="2" onchange="ChangeSidebar()"> Ask
                          </td>
                          <td>Таймфрейм:
                             <input type="radio" name="timeframe" value="01H" onchange="ChangeSidebar()"> 1 час
                             <input type="radio" name="timeframe" checked value="06H" onchange="ChangeSidebar()"> 6 часов
                           </td>
                           <td>Горизонт:
                             <input type="radio" name="period" checked value="0" onchange="ChangeSidebar()"> 60 периодов
                             <input type="radio" name="period" value="1" onchange="ChangeSidebar()"> 120 периодов
                             <input type="radio" name="period" value="2" onchange="ChangeSidebar()"> 240 периодов
                          </td>';
        return $output;
     }
  }
?>