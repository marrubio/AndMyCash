<?php
error_reporting(E_ALL);
session_start();
ini_set("display_errors", 1);
header('Content-type: text/html; charset=utf-8');

include('includes/bdcon.php');   
  
  $logged=false; 
  if(isset($_SESSION['login'])){
	$logged=true;
  }else{	header('Location:AndMyCash.php'); 
  }  

  $title= $app_name . " " . $app_version ."  - Listado de personas";
  include('includes/pagetop.php');  
?>
<h2>Listado de personas</h2>

<table border=1  cellspacing="0">
<thead>
<tr>
<th>ID</th><th>TIPO</th><th>NOMBRE</th><th>COMENTARIO</th><th>TRANSACCIONES</th><th>ACCIONES</th>
</tr>
</thead>
<?php

//mysql_query('ALTER TABLE MYC_PERSONA CHARACTER SET utf8 COLLATE utf8_general_ci;  ', $bdcon) or die(mysql_error()); 
//mysql_query('ALTER TABLE MYC_PERSONA CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;', $bdcon) or die(mysql_error()); 

$quePersonas = "SELECT * FROM MYC_PERSONA ORDER BY ID ASC";
$resPersonas = $bdcon->query($quePersonas) or die(mysql_error());
$totPersonas = $resPersonas->num_rows;

if ($totPersonas> 0) {
   while ($rowPersona = $resPersonas->fetch_assoc()) {
      echo "<tr>";
      echo "<td>".$rowPersona['ID']."</td>";
      echo "<td>".$rowPersona['TIPO_FK']."</td>";
	  echo "<td>".$rowPersona['NOMBRE']."</td>";
	  echo "<td>".$rowPersona['COMENTARIO']."</td>";
	  echo "<td>N/D €</strong></div></td>";
	  echo "<td><a href='PersonaEditar.php?id=".$rowPersona['ID']."'>Editar</a> / ";
	  echo "<a href='PersonaBorrar.php?id=".$rowPersona['ID']."'>Borrar</a></td>";
	  echo "</tr>";
	  
   }
}

?>
</table>
<p> <a href="PersonaNuevo.php">Nueva Persona</a></p>
<?php
 include('includes/pagebotton.php');
?>