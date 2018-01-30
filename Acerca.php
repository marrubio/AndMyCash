<?php
  session_start();
  error_reporting(E_ALL);
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
<h1><?php echo $app_name ?> - Acerca de</h1>
<hr>
<h3>Aplicaci&oacute;n de contabilidad domestica. Versión <?php echo $app_version;?></h3>
<p>
Autor: <strong>Mario Rubio</strong><br />
Fecha: <strong>Mayo 2013</strong><br />
Email: <a href="mailto:marugi@gmail.com">marugi@gmail.com</a><br />
Tecnologías: <strong>Apache + PHP + MySQL + Java + Android</strong>	
</p>
<?php
 include('includes/pagebotton.php');
?>
</div>

</body>
</html>