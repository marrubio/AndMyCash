<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  session_start();

  include('includes/redbiz_bdcon.php');   

$db_selected= mysql_select_db("redbiz_bd",$redbiz_bdcon);
?>

<html>
<head>
<title>AndMyCash 0.08b - Listado de cuentas</title>
</head>

<body>
<h2>Listado de cuentas</h2>

<table border=1  cellspacing="0">
<tr>
<th>ID</th><th>CATEGORIA</th><th>NOMBRE</th><th>COMENTARIO</th><th>APERTURA</th><th>ACCIONES</th>
</tr>
<?php

$queCuentas = "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA FROM MYC_CUENTA CU 
INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK
ORDER BY ID ASC";
$resCuentas = mysql_query($queCuentas, $redbiz_bdcon) or die(mysql_error());
$totCuentas = mysql_num_rows($resCuentas);

if ($totCuentas> 0) {
   while ($rowCuenta = mysql_fetch_assoc($resCuentas)) {
      echo "<tr>";
      echo "<td>".$rowCuenta['ID']."</td>";
      echo "<td>".$rowCuenta['CATEGORIA']."</td>";
	  echo "<td>".$rowCuenta['NOMBRE']."</td>";
	  echo "<td>".$rowCuenta['COMENTARIO']."</td>";
	  echo "<td><div align='right'><strong>".$rowCuenta['APERTURA']." €</strong></div></td>";
	  echo "<td><a href='EditarCuenta.php?id=".$rowCuenta['ID']."'>Editar</a> / ";
	  echo "<a href='BorrarCuenta.php?id=".$rowCuenta['ID']."'>Borrar</a></td>";
	  echo "</tr>";
	  
   }
}

?>
</table>
</body>
</html>