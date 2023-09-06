<?
  include 'PHP/Authorize.php';
  Authorize::Check();
  $filename = ucfirst(basename(__FILE__, ".php"));
  echo $filename;
?>