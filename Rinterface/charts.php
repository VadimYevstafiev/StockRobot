<?
  include "PHP/Authorize.php";
  Authorize::Check();
  include "PHP/InterfaceFunctions/InterfaceFunctions.php";
  include "PHP/InterfaceFunctions/ChartsInterface.php";
  ChartsInterface::Show();
?>