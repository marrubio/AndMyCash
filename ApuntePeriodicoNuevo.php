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
  
  if(isset($_POST['submitMe'])){
	//Realizamos el insert

	
	if($_POST['tra_activo']=="true") $activo = "true";
	else $activo = "false";

  if($_POST['tra_fecha_fin']=="") $fecha_fin = "NULL";
  else $fecha_fin = "STR_TO_DATE('".$_POST['tra_fecha_fin']."','%d/%m/%Y')";
	 

	$importe = str_replace(",",".",$_POST['tra_importe']);
	$insertApunte = "INSERT INTO MYC_PLANIFICADO (TIPO_FK,CUENTA_ORG_FK,CUENTA_DEST_FK,PERSONA_FK,COMENTARIO,IMPORTE,FECHA_INICIO,FECHA_FIN,PERIODO,CUENTA,PROXIMA_FECHA,ACTIVO) VALUES";
	$insertApunte .= " (".$_POST['tra_tipo'].",".$_POST['tra_origen'].",".$_POST['tra_destino'].",".$_POST['tra_beneficiario'].",'".$_POST['tra_comentario']."','".$importe;
  $insertApunte .= "',STR_TO_DATE('".$_POST['tra_fecha_inicio']."','%d/%m/%Y'),".$fecha_fin.",".$_POST['tra_periodo'].",".$_POST['tra_cuenta'];
  $insertApunte .= ",STR_TO_DATE('".$_POST['tra_proxima_fecha']."','%d/%m/%Y'),".$activo.");";

  //echo $insertApunte;
  $resInsertApunte = $bdcon->query($insertApunte) or die(mysql_error());

	header('Location: ApuntePeriodicoListado.php');   	
  }
     
  $title= $app_name . " " . $app_version ."  - Nueva Transacción periodica";  
  include('includes/pagetop.php');
?>
<h2>Nueva Transacción periodica</h2>

<form action="ApuntePeriodicoNuevo.php" method="post"> 
<table border=0  cellspacing="0">
<tr><td>PERIODICIDAD:</td><td><input name="tra_periodo" type="text" value="1"/> Meses</td></tr>
<tr><td>FECHA INICIO:</td><td><input name="tra_fecha_inicio" type="text" value="<?php echo date("d/m/Y"); ;?>"/></td></tr>
<tr><td>FECHA FIN:</td><td><input name="tra_fecha_fin" type="text"/></td></tr>
<tr><td>PROXIMA FECHA:</td><td><input name="tra_proxima_fecha" type="text" /></td></tr>
<tr><td>CUENTA:</td><td><input name="tra_cuenta" type="text" value="0"/></td></tr>
<tr><td>TIPO: </td>
<td><select name="tra_tipo"> 
<?php
$queTipoApunte = "SELECT ID,NOMBRE FROM MYC_TIPOAPUNTE ORDER BY ID ASC";

$resTipoApunte = $bdcon->query($queTipoApunte) or die(mysql_error());
$totTipoApunte = $resTipoApunte->num_rows;

if ($totTipoApunte> 0) {
   while ($rowTipoApunte = $resTipoApunte->fetch_assoc()) {
      echo "<option value=".$rowTipoApunte['ID'].">".$rowTipoApunte['NOMBRE']."</option>";          
   }
}
?>
</select></td></tr>

<tr><td>CUENTA ORIGEN: </td>
<td><select name="tra_origen"> 
<?php
$queCuentaOrg = "SELECT CU.ID,CU.NOMBRE FROM MYC_CUENTA CU INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK ORDER BY CA.TIPO_FK,CU.NOMBRE ASC";
$resCuentaOrg = $bdcon->query($queCuentaOrg) or die(mysql_error());
$totCuentaOrg = $resCuentaOrg->num_rows;


if ($totCuentaOrg> 0) {
   while ($rowCuentaOrg =$resCuentaOrg->fetch_assoc()) {
      echo "<option value=".$rowCuentaOrg['ID'].">".$rowCuentaOrg['NOMBRE']."</option>";          
   }
}
?>
</select></td></tr>

<tr><td>CUENTA DESTINO: </td>
<td><select name="tra_destino"> 
<?php
//$queCuentaDest = "SELECT ID,NOMBRE FROM MYC_CUENTA ORDER BY ID ASC";
$queCuentaDest = "SELECT CU.ID,CU.NOMBRE FROM MYC_CUENTA CU INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK ORDER BY CA.TIPO_FK,CU.NOMBRE ASC";
$resCuentaDest = $bdcon->query($queCuentaDest) or die(mysql_error());
$totCuentaDest = $resCuentaDest->num_rows;


if ($totCuentaDest > 0) {
   while ($rowCuentaDest = $resCuentaDest->fetch_assoc()) {
      echo "<option value=".$rowCuentaDest['ID'].">".$rowCuentaDest['NOMBRE']."</option>";          
   }
}
?>
</select></td></tr>

<tr><td>BENEFICIARIO: </td>
<td><select name="tra_beneficiario"> 
<?php
$queBeneficiario = "SELECT ID,NOMBRE FROM MYC_PERSONA ORDER BY ID ASC";
$resBeneficiario = $bdcon->query($queBeneficiario) or die(mysql_error());
$totBeneficiario = $resBeneficiario->num_rows;

echo "<option value=0></option>";          
if ($totBeneficiario > 0) {
   while ($rowBeneficiario = $resBeneficiario->fetch_assoc()) {
      echo "<option value=".$rowBeneficiario['ID'].">".$rowBeneficiario['NOMBRE']."</option>";          
   }
}
?>
</select></td></tr>

<tr><td>IMPORTE:</td><td><input name="tra_importe" class="text" type="text" value="0" /> €</td></tr>
<tr><td>ACTIVO:</td><td><input type="checkbox" name="tra_activo" value="true"></td></tr>
<tr><td>COMENTARIO:</td><td>
<textarea name="tra_comentario" class="text" rows="4" cols="50">
</textarea>
</td></tr>
</table>
<input type="submit" value="Insertar" name="submitMe">

</form>
<?php
 include('includes/pagebotton.php');
?>