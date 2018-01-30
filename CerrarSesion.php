<?php
  error_reporting(E_ALL);
  session_start();
  ini_set("display_errors", 1);
  session_unset();
  error_reporting(E_ALL);
  session_destroy();
  unset($_SESSION);  
  header('Location:AndMyCash.php');
 ?>