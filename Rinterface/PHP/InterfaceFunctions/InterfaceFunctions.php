<?
  /**
   * InterfaceFunctions: Базовый класс функций отображения интерфейса
   */
  class InterfaceFunctions {
     /**
      * @param object  $instance      Экземпляр модуля
      * @param string  $name          Имя интерфейса 
      * @param array   $navItems      Массив элементов секции навигации
      */
     static protected $instance = NULL;
     protected $name;
     protected $navItems = array (
                                   "Главная"   => "index.php",
                                   "Журналы"   => "journals.php",
                                   "Протоколы" => "protocols.php",
                                   "Графики"   => "charts.php"
                                 );  
     /**
      * Функция отображения интерфейса
      */
     static public function Show() {
        self::$instance = new static();
        $content = self::$instance->ShowHead();
        $content .= self::$instance->ShowBody();
        echo $content;
     }

     protected function __construct () {
     }
     /**
      * Функция отображения головы интерфейса
      */
     protected function ShowHead() {
        $output = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
           <head>
              <meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
              <title>' . $this->name . '</title>
              <link type="text/css" rel="stylesheet" href="CSS/stile.css"/>';
        $output .= $this::AddScriptToHead();
        $output .= '
           </head>';
        return $output;
     }
     /**
      * Функция загрузки скриптов JavaScript
      */
     protected function AddScriptToHead() {
     }
     /**
      * Функция отображения тела интерфейса
      */
     protected function ShowBody() {
        $output = '
           <body>';
	$output .= $this::ShowNav();
        $output .= $this::ShowHeader();
        $output .= $this::ShowContent();
        $output .= '
           </body>
        </html>';
        $output .= $this::ShowScript();
        return $output;
     }
     /**
      * Функция отображения секции навигации интерфейса
      */
     protected function ShowNav() {
        $output = '
              <nav>
                 <ul>';
        $output .= $this::ServiceShowNav();
        $output .= '
                 </ul>
              </nav>';
        return $output;
     }
     /**
      * Служебная функция отображения секции навигации интерфейса
      */
     protected function ServiceShowNav() {
        $output = '';
        foreach ($this->navItems as $key => $value) {
           $output .= '
		         <li';
           if ($key == $this->name) {
              $output .= ' class="selected"';
           }
           $output .= '><a href="' . $value . '">';
           $output .= mb_strtoupper($key, "UTF-8");
           $output .= '</a></li>';
        }
        return $output;
     }
     /**
      * Функция отображения заголовка интерфейса
      */
     protected function ShowHeader() {
        $output = '
              <header class="top">
                 <table>
                    <tr>
                       <td>';
        $output .= $this::ServiceShowHeader();
        $output .= '
                       </td>
                    </tr>
                 </table>
              </header>';
        return $output;
     }
     /**
      * Служебная функция отображения заголовка интерфейса
      */
     protected function ServiceShowHeader() {
        $output = '
                          <td>Рынок:
                             <select id="id" size="1"  onchange="ChangeSelect()">
                                <option value="bf">BF</option>
                                <option selected value="wm">WM</option>
                             </select>
                          </td>
                          <td  id="select">Торговая пара:
                          </td>';
        return $output;
     }
     /**
      * Функция отображения содержимого интерфейса
      */
     protected function ShowContent() {
        $output = '
              <div id="sidebarCh">
              </div>
              <div id="mainCh">
              </div>';
        return $output;
     }
     /**
      * Функция отображения скрипта JavaScript
      */
     protected function ShowScript() {
        $output = '
        <script type="text/javascript">
           ChangeSelect();
        </script>';
        return $output;
     }
  }
?>