<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  session_start();
  include('includes/bdcon.php');   
  $logged=false; 
  if(isset($_SESSION['login'])){
	$logged=true;
  }else{	header('Location:AndMyCash.php'); 
  }    
  
  if(isset($_POST['per_id'])){
	
	  $importe = str_replace(",",".",$_POST['tra_importe']);
  
  	$updApuntePer ="UPDATE MYC_PLANIFICADO SET CUENTA=".$_POST['per_cuenta']." , PROXIMA_FECHA=STR_TO_DATE('".$_POST['per_proxima_fecha']."','%d/%m/%Y') ";
  	$updApuntePer .=" WHERE ID=".$_POST['per_id']." ";
    echo $updApuntePer;
    $resUpdApuntePer = $bdcon->query($updApuntePer) or die(mysql_error());
  	
    
    if($_POST['tra_conciliado']=="1") $conciliado = "true";
    else $conciliado = "false";
    
    $importe = str_replace(",",".",$_POST['tra_importe']);

    $insertApunte = "INSERT INTO MYC_APUNTE (FECHA,TIPO_FK,CUENTA_ORG_FK,CUENTA_DEST_FK,PERSONA_FK,COMENTARIO,IMPORTE,CONCILIADO,PLANIFICADO_FK) VALUES";
    $insertApunte .= " (STR_TO_DATE('".$_POST['tra_fecha']."','%d/%m/%Y'),".$_POST['tra_tipo'].",".$_POST['tra_origen'].",".$_POST['tra_destino'];
    $insertApunte .= ",".$_POST['tra_beneficiario'].",'".$_POST['tra_comentario']."','".$importe."',".$conciliado.",".$_POST['per_id'].");";
    echo $insertApunte;
    $resInsertApunte = $bdcon->query($insertApunte);    

	header('Location: ApunteListado.php');   

  }
  
  if(isset($_GET['id'])){
    	$queApunte = "SELECT ID, TIPO_FK, PERIODO, CUENTA, ACTIVO, DATE_FORMAT(FECHA_INICIO,'%d/%m/%Y') FECHA_INICIO, DATE_FORMAT(FECHA_FIN,'%d/%m/%Y') FECHA_FIN, DATE_FORMAT(PROXIMA_FECHA,'%d/%m/%Y') PROXIMA_FECHA,";
    	$queApunte .= " DATE_FORMAT(DATE_ADD(PROXIMA_FECHA,INTERVAL PERIODO MONTH),'%d/%m/%Y') PROXIMA_FECHA_CALC, ";
      $queApunte .= " CUENTA_ORG_FK, CUENTA_DEST_FK, PERSONA_FK, COMENTARIO, IMPORTE FROM MYC_PLANIFICADO WHERE ID=".$_GET['id']." ";
      $resApunte = $bdcon->query($queApunte);
      $totApunte = $resApunte->num_rows;
      $rowApunte = $resApunte->fetch_assoc();
      $intCuenta = $rowApunte['CUENTA']; 

  }  
  
  $title= $app_name . " " . $app_version ."  - Generar transacción periodica";
  include('includes/pagetop.php');
?>

<h2>Generar transacción periodica Nº 
<?php 
echo $intCuenta;
?></h2>

<form action="ApuntePeriodicoGenerar.php" method="post"> 
<input name="per_id" type="hidden" value="<?php echo $_GET['id'];?>" />
<input name="per_cuenta" type="hidden" value="<?php echo $intCuenta+1;?>"/>

<table border=0  cellspacing="0">
<tr><td>FECHA APUNTE:</td><td><input name="tra_fecha" type="text" value="<?php echo $rowApunte['PROXIMA_FECHA'];?>"/></td></tr>
<tr><td>PROXIMA FECHA PERIODICA:</td><td><input name="per_proxima_fecha" type="text" value="<?php echo $rowApunte['PROXIMA_FECHA_CALC'];?>"/></td></tr>

<tr><td>TIPO: </td>
<td><select name="tra_tipo"> 
<?php
$queTipoApunte = "SELECT ID,NOMBRE FROM MYC_TIPOAPUNTE ORDER BY ID ASC";
   $resTipoApunte = $bdcon->query($queTipoApunte);
   $totTipoApunte = $resTipoApunte->num_rows;

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
$resCuentaOrg = $bdcon->query($queCuentaOrg);
$totCuentaOrg = $resCuentaOrg->num_rows;

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
$totCuentaDest = $resCuentaDest->num_rows;

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
$totBeneficiario = $resBeneficiario->num_rows;

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
<tr><td>CONCILIADO:</td><td><input type="checkbox" name="tra_conciliado" value="1" <?php if($rowApunte['ACTIVO']) echo "checked";?>></td></tr>
<tr><td>COMENTARIO:</td><td>
<textarea name="tra_comentario" rows="4" cols="50">
<?php echo $rowApunte['COMENTARIO'];?>
</textarea>
</td></tr>
</table>
<input type="submit" value="Insertar" name="submitInsert">

</form>
<?php
 include('includes/pagebotton.php');
?>
