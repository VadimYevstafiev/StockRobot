<?
  $filename = ucfirst(basename(__FILE__, ".php"));
  define("MARKETID", substr($filename, 0, 2));
  define("DIRECTION", substr($filename, -6));
  require_once(dirname(dirname(__FILE__)) . "/Include" . MARKETID . ".php");
  MainProcess::Execute(FALSE);
?>
