﻿<?php
  include "PHP/Authorize.php";
  Authorize::Check();
  include "PHP/SendDataFunctions/SendDataFunctions.php";
  include "PHP/SendDataFunctions/SendJournalsDataFunctions.php";
  SendJournalsDataFunctions::SendData();
?>
