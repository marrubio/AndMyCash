<?php
error_reporting(E_ALL);
session_start();
ini_set("display_errors", 1);
include('includes/bdcon.php');   	
$error=false;
$logged=false;  
if($_POST)
{	
	$logged = ObtenerUsuario($_POST['log_name'],$_POST['log_pwd'],$bdcon);
	if($logged){
    	$_SESSION['login']=$_POST['log_name'];
	}else{
		$error=true;	
	}
}
else{	  
	 if(isset($_SESSION['login'])){
		$logged=true;
	}
}
$title= $app_name . " " . $app_version ."  - Acerca de";
include('includes/pagetop.php');   	
?>
<body>
<div class="container">
<h1>AndMyCash - Acerca de</h1>
<hr>
<p><strong>Aplicaci&oacute;n de contabilidad domestica</strong></p>
<p>
Autor: Mario Rubio<br />
Fecha: Mayo 2013<br />
Email: <a href="mailto:marugi@gmail.com">marugi@gmail.com</a><br />
Tecnologías: Apache + PHP + MySQL</p>
<?php
include('includes/pagebotton.php');
?>