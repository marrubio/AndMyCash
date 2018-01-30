<?php  
error_reporting(E_ALL);
session_start();
ini_set("display_errors", 1);

  include('includes/bdcon.php');   
  
  $logged=false; 
  if(isset($_SESSION['login'])){
	$logged=true;
  }else{	header('Location:AndMyCash.php'); 
  }  
  $title= $app_name . " " . $app_version ."  - Listado cuentas";  
  include('includes/pagetop.php');   
  
    $mtime = microtime(); 
 $mtime = explode(" ",$mtime); 
 $mtime = $mtime[1] + $mtime[0]; 
 $tiempoinicial = $mtime;  
?>
<h2>Listado de cuentas</h2>

<table border=0  cellspacing="0">
<thead>
<tr>
<th>ID</th><th>CATEGORIA</th><th>NOMBRE</th><th>COMENTARIO</th><th>APERTURA</th><th>ACCIONES</th>
</tr>
</thead>
<?php

$queCuentas = "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA FROM MYC_CUENTA CU 
INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK
ORDER BY ID ASC";
$resCuentas = $bdcon->query($queCuentas) or die(mysql_error());
$totCuentas = $resCuentas->num_rows;

if ($totCuentas> 0) {
   while ($rowCuenta = $resCuentas->fetch_assoc()) {
      echo "<tr>";
      echo "<td>".$rowCuenta['ID']."</td>";
      echo "<td>".$rowCuenta['CATEGORIA']."</td>";
	  echo "<td>".$rowCuenta['NOMBRE']."</td>";
	  echo "<td>".$rowCuenta['COMENTARIO']."</td>";
	  echo "<td><div align='right'><strong>".$rowCuenta['APERTURA']." &euro;</strong></div></td>";
	  echo "<td><a href='CuentaEditar.php?id=".$rowCuenta['ID']."'>Editar</a> / ";
	  echo "<a href='CuentaBorrar.php?id=".$rowCuenta['ID']."'>Borrar</a></td>";
	  echo "</tr>";	  
   }
}
?>
</table>

<p><?php 
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$tiempofinal = $mtime; 
$tiempototal = ($tiempofinal - $tiempoinicial); 

echo $totCuentas. " cuentas listadas en ".$tiempototal." segundos";

?>
</p>
<?php
 include('includes/pagebotton.php');
?>