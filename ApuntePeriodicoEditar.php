﻿<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  session_start();
  include('includes/bdcon.php');   
  $logged=false; 
  if(isset($_SESSION['login'])){
	$logged=true;
  }else{	header('Location:AndMyCash.php'); 
  }  
    
  if(isset($_POST['tra_id'])){
	//Realizamos el insert	
	if($_POST['tra_activo']=="1") $activo = "1";
	else $activo = "0";	
	
	$importe = str_replace(",",".",$_POST['tra_importe']);

   if($_POST['tra_fecha_fin']=="") $fecha_fin = "NULL";
  else $fecha_fin = "STR_TO_DATE('".$_POST['tra_fecha_fin']."','%d/%m/%Y')";
	
	$updApunte ="UPDATE MYC_PLANIFICADO SET PERIODO=".$_POST['tra_periodo']." , FECHA_INICIO=STR_TO_DATE('".$_POST['tra_fecha_inicio']."','%d/%m/%Y') ,";
	$updApunte .= "FECHA_FIN=".$fecha_fin.", PROXIMA_FECHA=STR_TO_DATE('".$_POST['tra_proxima_fecha']."','%d/%m/%Y') ,";
	$updApunte .= "CUENTA=".$_POST['tra_cuenta'].", TIPO_FK=".$_POST['tra_tipo'].", CUENTA_ORG_FK=".$_POST['tra_origen'].", CUENTA_DEST_FK=".$_POST['tra_destino'];
	$updApunte .= ", PERSONA_FK=".$_POST['tra_beneficiario'].", COMENTARIO='".$_POST['tra_comentario']."', IMPORTE='".$importe."', ACTIVO=".$activo;
	$updApunte .=" WHERE ID=".$_POST['tra_id']." ";
	$resUpdApunte = $bdcon->query($updApunte) or die(mysql_error());	
	header('Location: ApuntePeriodicoListado.php');   	
  }
  
  if(isset($_GET['id'])){
	$queApunte = "SELECT ID, TIPO_FK, PERIODO, CUENTA, ACTIVO, DATE_FORMAT(FECHA_INICIO,'%d/%m/%Y') FECHA_INICIO, DATE_FORMAT(FECHA_FIN,'%d/%m/%Y') FECHA_FIN, DATE_FORMAT(PROXIMA_FECHA,'%d/%m/%Y') PROXIMA_FECHA,";
	$queApunte .= " CUENTA_ORG_FK, CUENTA_DEST_FK, PERSONA_FK, COMENTARIO, IMPORTE FROM MYC_PLANIFICADO WHERE ID=".$_GET['id']." ";
	//echo $queApunte;

$resApunte = $bdcon->query($queApunte) or die(mysql_error());
$rowApunte = $resApunte->num_rows;
$rowApunte = $resApunte->fetch_assoc();
  }  
  
  $title= $app_name . " " . $app_version ."  - Modificar transacción periodica";
  include('includes/pagetop.php');
?>


<h2>Modificar transacci&oacute;n periodica</h2>


<form action="ApuntePeriodicoEditar.php" method="post"> 
<table border=0  cellspacing="0">
<tr><td>ID:</td><td><input name="tra_id" type="text" value="<?php echo $_GET['id'];?>" readonly/></td></tr>
<tr><td>PERIODICIDAD:</td><td><input name="tra_periodo" type="text" value="<?php echo $rowApunte['PERIODO'];?>"/> Meses</td></tr>
<tr><td>FECHA INICIO:</td><td><input name="tra_fecha_inicio" type="text" value="<?php echo $rowApunte['FECHA_INICIO'];?>"/></td></tr>
<tr><td>FECHA FIN:</td><td><input name="tra_fecha_fin" type="text" value="<?php echo $rowApunte['FECHA_FIN'];?>"/></td></tr>
<tr><td>PROXIMA FECHA:</td><td><input name="tra_proxima_fecha" type="text" value="<?php echo $rowApunte['PROXIMA_FECHA'];?>"/></td></tr>
<tr><td>CUENTA:</td><td><input name="tra_cuenta" type="text" value="<?php echo $rowApunte['CUENTA'];?>"/></td></tr>

<tr><td>TIPO: </td>
<td><select name="tra_tipo"> 
<?php
$queTipoApunte = "SELECT ID,NOMBRE FROM MYC_TIPOAPUNTE ORDER BY ID ASC";
$resTipoApunte = $bdcon->query($queTipoApunte) or die(mysql_error());
$totTipoApunte = $resTipoApunte->fetch_assoc();


if ($totTipoApunte> 0) {
   while ($rowTipoApunte = $resTipoApunte->fetch_assoc()) {
   
   if($rowTipoApunte['ID']==$rowApunte['TIPO_FK']){
	   echo "<option value=".$rowTipoApunte['ID']." selected>".$rowTipoApunte['NOMBRE']."</option>";     
	 }else{	 
       echo "<option value=".$rowTipoApunte['ID'].">".$rowTipoApunte['NOMBRE']."</option>";     
	 }	      
   }
}
?>
</select></td></tr>

<tr><td>CUENTA ORIGEN: </td>
<td><select name="tra_origen"> 
<?php
$queCuentaOrg = "SELECT CU.ID,CU.NOMBRE FROM MYC_CUENTA CU INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK ORDER BY CA.TIPO_FK,CU.NOMBRE ASC";
$resCuentaOrg = $bdcon->query($queCuentaOrg) or die(mysql_error());
$totCuentaOrg = $resCuentaOrg->fetch_assoc();

if ($totCuentaOrg> 0) {
   while ($rowCuentaOrg = $resCuentaOrg->fetch_assoc()) {
     if($rowCuentaOrg['ID']==$rowApunte['CUENTA_ORG_FK']) echo "<option value=".$rowCuentaOrg['ID']." selected>".$rowCuentaOrg['NOMBRE']."</option>";          
	 else echo "<option value=".$rowCuentaOrg['ID'].">".$rowCuentaOrg['NOMBRE']."</option>";          	          
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
$totCuentaDest = $resCuentaDest->fetch_assoc();


if ($totCuentaDest > 0) {
   while ($rowCuentaDest = $resCuentaDest->fetch_assoc()) {
   if($rowCuentaDest['ID']==$rowApunte['CUENTA_DEST_FK']) echo "<option value=".$rowCuentaDest['ID']." selected>".$rowCuentaDest['NOMBRE']."</option>";            
	 else echo "<option value=".$rowCuentaDest['ID'].">".$rowCuentaDest['NOMBRE']."</option>";          
      
   }
}
?>
</select></td></tr>

<tr><td>BENEFICIARIO: </td>
<td><select name="tra_beneficiario"> 
<?php
$queBeneficiario = "SELECT ID,NOMBRE FROM MYC_PERSONA ORDER BY ID ASC";
$resBeneficiario = $bdcon->query($queBeneficiario) or die(mysql_error());
$totBeneficiario = $resBeneficiario->fetch_assoc();


echo "<option value=0></option>";          
if ($totBeneficiario > 0) {
   while ($rowBeneficiario = $resBeneficiario->fetch_assoc()) {
   
    if($rowBeneficiario['ID']==$rowApunte['PERSONA_FK']) echo "<option value=".$rowBeneficiario['ID']." selected>".$rowBeneficiario['NOMBRE']."</option>";          
	 else echo "<option value=".$rowBeneficiario['ID'].">".$rowBeneficiario['NOMBRE']."</option>";          
         
   }
}
?>
</select></td></tr>

<tr><td>IMPORTE:</td><td><input name="tra_importe" type="text" value="<?php echo $rowApunte['IMPORTE'];?>" /> €</td></tr>
<tr><td>ACTIVO:</td><td><input type="checkbox" name="tra_activo" value="1" <?php if($rowApunte['ACTIVO']) echo "checked";?>></td></tr>
<tr><td>COMENTARIO:</td><td>
<textarea name="tra_comentario" rows="4" cols="50">
<?php echo $rowApunte['COMENTARIO'];?>
</textarea>
</td></tr>
</table>
<input type="submit" value="Modificar" name="submitUpdate">
<input type="submit" value="Insertar" name="submitInsert">

</form>
<?php
 include('includes/pagebotton.php');
?>
