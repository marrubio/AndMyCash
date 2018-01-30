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

  
  if(isset($_GET['id'])){
    	$queApunte = "SELECT ID,TIPO_FK,DATE_FORMAT(FECHA,'%d/%m/%Y') FECHA,CUENTA_ORG_FK, CUENTA_DEST_FK, PERSONA_FK, COMENTARIO, IMPORTE, CONCILIADO FROM MYC_APUNTE WHERE ID=".$_GET['id']." ";
      $resApunte = $bdcon->query($queApunte) or die(mysql_error());
      $totApunte = $resApunte->num_rows;
      $rowApunte = $resApunte->fetch_assoc();

  }else{

    if(isset($_POST['tra_id'])){
        //Realizamos el insert  
        if($_POST['tra_conciliado']=="1") $conciliado = "1";
        else $conciliado = "0";               
        $importe = str_replace(",",".",$_POST['tra_importe']);        
        $updApunte ="UPDATE MYC_APUNTE SET FECHA=STR_TO_DATE('".$_POST['tra_fecha']."','%d/%m/%Y') , TIPO_FK=".$_POST['tra_tipo'].", CUENTA_ORG_FK=".$_POST['tra_origen'].", CUENTA_DEST_FK=".$_POST['tra_destino'].", PERSONA_FK=".$_POST['tra_beneficiario'].", COMENTARIO='".$_POST['tra_comentario']."', IMPORTE='".$importe."', CONCILIADO=".$conciliado;
        $updApunte .=" WHERE ID=".$_POST['tra_id']." ";
        $resUpdApunte = $bdcon->query($updApunte) or die(mysql_error());        
        header('Location: ApunteListado.php');    
    }

  }
  
  $title= $app_name . " " . $app_version ."  - Modificar Apunte";
  include('includes/pagetop.php');
?>


<h2>Editar transacci&oacute;n</h2>

<form action="ApunteEditar.php" method="post"> 
<table border=0  cellspacing="0">
<tr><td>ID:</td><td><strong><?php echo $_GET['id'];?>
<input name="tra_id" type="hidden" value="<?php echo $_GET['id'];?>">
</strong></td></tr>
<tr><td>FECHA:</td><td>
  
  <input name="tra_fecha" id="datepicker" class="text" type="text" value="<?php echo $rowApunte['FECHA'];?>" 
   maxlength="10" size="10" style="text-align:right;padding-right:5px;font-weight:bold;"/> [DD/MM/AAAA]

<tr><td>TIPO: </td>
<td><select name="tra_tipo"> 
<?php
$queTipoApunte = "SELECT ID,NOMBRE FROM MYC_TIPOAPUNTE ORDER BY ID ASC";
$resTipoApunte = $bdcon->query($queTipoApunte) or die(mysql_error());
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
$resCuentaOrg = $bdcon->query($queCuentaOrg) or die(mysql_error());
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
$queBeneficiario = "SELECT ID,NOMBRE FROM MYC_PERSONA ORDER BY NOMBRE ASC";
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

<tr><td>IMPORTE:</td><td><input name="tra_importe" type="text" value="<?php echo $rowApunte['IMPORTE'];?>" style="text-align:right;padding-right:5px;font-weight:bold;font-size:125%"/> €</td></tr>
<tr><td>CONCILIADO:</td><td><input type="checkbox" name="tra_conciliado" value="1" <?php if($rowApunte['CONCILIADO']) echo "checked";?>></td></tr>
<tr><td>COMENTARIO:</td><td>
<textarea name="tra_comentario" rows="4" cols="50">
<?php echo $rowApunte['COMENTARIO'];?>
</textarea>
</td></tr>

<tr><td>ADJUNTOS:</td><td>
<?php
$queAdjuntos = "SELECT ID,DESCRIPCION, FICHERO FROM MYC_APUNTEFICHERO WHERE APUNTE_FK=".$_GET['id']." ORDER BY ID ASC";
$resAdjuntos = $bdcon->query($queAdjuntos) or die(mysql_error());
$totAdjuntos = $resAdjuntos->num_rows;

if ($totAdjuntos > 0) {
  echo "<ul>";
   while ($rowAdjunto = $resAdjuntos->fetch_assoc()) {
      
      echo "<li><a href='".PATH_FILES_GET . $rowAdjunto['FICHERO']."'>".$rowAdjunto['DESCRIPCION']."</a></li>";
         
   }
   echo "</ul>";
}
?>


<a href="ApunteFicheroNuevo.php?apunte_fk=<?php echo $_GET['id']; ?>">Adjuntar Ficheros</a>
</td></tr>

</table>
<input type="submit" value="Modificar" name="submitUpdate">
<input type="submit" value="Insertar" name="submitInsert">
</form>



<?php
 include('includes/pagebotton.php');
?>