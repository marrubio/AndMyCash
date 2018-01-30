<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  session_start();
  include('includes/bdcon.php');   
  
  
  if(isset($_POST['cta_id'])){
	//Realizamos el update
	$updCuenta = "UPDATE MYC_CUENTA SET NOMBRE='".$_POST['cta_nombre']."', COMENTARIO='".$_POST['cta_comentario']."', APERTURA='".$_POST['cta_apertura']."', CATEGORIA_FK=".$_POST['cta_categoria']." WHERE ID=".$_POST['cta_id']." ";
	$resUpdCuenta = $bdcon->query($updCuenta);
	header('Location: CuentaListado.php');   	
  }
  
  if(isset($_GET['id'])){
	$queCuenta = "SELECT * FROM MYC_CUENTA WHERE ID=".$_GET['id']." ";
  $resCuenta = $bdcon->query($queCuenta);
  $totCuenta = $resCuenta->num_rows;
  $rowCuenta = $resCuenta->fetch_assoc();
  }
  $title= $app_name . " " . $app_version ."  - Editar Cuenta";
  include('includes/pagetop.php');   	
?>
<h2>Listado de transacciones

<body>
<h2>Listado de cuentas</h2>

<html><body>
<h4>Edición de cuentas</h4>
<form action="CuentaEditar.php" method="post"> 

<table border=0  cellspacing="0">
<tr><th>CAMPO</th><th>VALOR</th></tr>
<tr><td>ID:</td><td><input name="cta_id" type="text" value="<?php echo $_GET['id'];?>"/></td></tr>
<tr><td>Categoria: </td>
<td><select name="cta_categoria"> 
<?php
$queCategorias = "SELECT ID,NOMBRE FROM MYC_CATEGORIA ORDER BY TIPO_FK ASC";
$resCategorias = $bdcon->query($queCategorias);
$totCategorias = $resCategorias->num_rows;

if ($totCategorias> 0) {
   while ($rowCategorias = $resCategorias->fetch_assoc()) {
     if($rowCategorias['ID']==$rowCuenta['CATEGORIA_FK']){
	   echo "<option value=".$rowCategorias['ID']." selected>".$rowCategorias['NOMBRE']."</option>";     
	 }else{	 
       echo "<option value=".$rowCategorias['ID'].">".$rowCategorias['NOMBRE']."</option>";     
	 }
  }
}
?>
</select>
</td>
</tr>
<tr><td>Nombre:</td><td><input name="cta_nombre" type="text" value="<?php echo $rowCuenta['NOMBRE'];?>"/></td></tr>
<tr><td>Comentario:</td><td><input name="cta_comentario" type="text" value="<?php echo $rowCuenta['COMENTARIO'];?>"/></td></tr>
<tr><td>Apertura:</td><td><input name="cta_apertura" type="text" value="<?php echo $rowCuenta['APERTURA'];?>"/></td></tr>
</table>
<input type="submit" />

</form>
<?php
 include('includes/pagebotton.php');
?>
