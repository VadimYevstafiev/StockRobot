<?
  /**
   * Authorize: Статический класс функций авторизации
   */
  class Authorize {
     /**
     * Функция проверки авторизации
     */
     static public function Check () {
        $status = 1;
        // $status = 1 - сессии не существует, мы заходим в первый раз
        // $status = 2 - сессия существовует, но не корректна
        // $status = 3 - сессия существовует и корректна
        if ((isset($_REQUEST[session_name()])) || (isset($_COOKIE[session_name()]))) {
           session_start();
           if ((isset($_SESSION["start"])) && ($_SESSION["ip"] == $_SERVER["REMOTE_ADDR"]))  {
              if ((time() - $_SESSION["start"]) > 43200)  {
                 $status = 2;
              } else {
                 $status = 3;
               }
           } else {
              $status = 2;
           }
        } 
        if ($status == 2) {
            session_destroy();
            setcookie ("PHPSESSID", "", time() - 3600);
        }

        if ($status == 3) {
        } else if (isset($_POST["ok"])) {
            if (($_POST["login"] == "Владелец") && ($_POST["pass"] == "Eustas")) {
              session_start();
              $_SESSION["start"] = time();
              $_SESSION["ip"] = $_SERVER["REMOTE_ADDR"];
              header("Location: http://". $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
           } else {
              self::PrintForm ($_SERVER["SCRIPT_NAME"]);
           }
        } else {
           self::PrintForm ($_SERVER["SCRIPT_NAME"]);
        }
     }
     /**
     * Функция вывода формы авторизации
     *
     * @param string  $RequestUri  Имя скрипта - обработчика данных формы 
     *
     */
     static private function PrintForm ($RequestUri) {
        echo"
        <form method='POST' action='" . $RequestUri ."'>
        <table width='100%' height='100%'>
        <tr>
        <td align=center>
           <table>
              <tr><td>
                 <table>
                    <tr>
                        <td>Логин:</td>
                        <td><input type='text' name='login' size='15'></td>
                    </tr>
                    <tr>
                       <td>Пароль: </td>
                       <td><input type='password' name='pass' size='15'></td>
                    </tr>
                 </table>
              </td></tr>
              <tr>
                 <td align=center><input type='submit' name='ok' value='Вход'></td>
              </tr>
           </table>
        </td>
        </tr>
        </table>
        </form>
        ";
        exit;
     }
  }
?>