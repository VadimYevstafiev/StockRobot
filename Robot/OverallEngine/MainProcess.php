<?
  /**
   * MainProcess: Класс главной функции приложения
   */
  class MainProcess {
     /**
      * Главная функция приложения
      *
      * @param bool    $processfull    Тип процесса 
      *                                "FALSE" - режим сбора данных
      *                                "TRUE"  - полный режим 
      */
     static public function Execute($processfull) {
        FinalCatcher::Start();
        Configurations::SetConfigurations();
        MySQLconnector::SetConnect();

        $listenerdata = Listener::Refresh();

        if ($processfull) {
           if ($listenerdata) {
              $factor = Expert::CheckRefreshTimeframe($listenerdata["timestamp"]);
           }

           if ($factor) {
              Expert::Refresh();
           }

           Trader::ExecuteTrading($listenerdata, Expert::GetResults());

           if ($factor) {
              InterfaceData::Refresh();
           }
        }

        MySQLconnector::UnsetConnect();
     }
  }
?>