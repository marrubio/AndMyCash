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
   
 if(isset($_POST['submitMe'])){
	
	//Realizamos el insert		
	$updtPersona = "UPDATE MYC_PERSONA SET NOMBRE='".$_POST['per_nombre']."', DIRECCION='".$_POST['per_direccion']."', POBLACION='".$_POST['per_poblacion']."', PROVINCIA='".$_POST['per_provincia']."',  ";	
	$updtPersona .= " CODIGO_POSTAL='".$_POST['per_codigopostal']."' , PAIS='".$_POST['per_pais']."', TELEFONO='".$_POST['per_telefono']."',  COMENTARIO='".$_POST['per_comentario']."', TIPO_FK=".$_POST['per_tipo']." ";	
	$updtPersona .=" WHERE ID=".$_POST['per_id'];
	
	$resModApunte = $bdcon->query($updtPersona);

	header('Location: PersonaListado.php');   	
  }
  
  if(isset($_GET['id'])){
  
	$quePersona = "SELECT ID, NOMBRE, DIRECCION, POBLACION, PROVINCIA, CODIGO_POSTAL, PAIS, TELEFONO, EMAIL, COMENTARIO, TIPO_FK FROM MYC_PERSONA WHERE ID =".$_GET['id'];

    $resPersona = $bdcon->query($quePersona);
    $totPersona = $resPersona->num_rows;

	if ($totPersona> 0) {
	   $rowPersona = $resPersona->fetch_assoc();
	}			
  }
    
  $title= $app_name . " " . $app_version ."  - Modificar persona";
  include('includes/pagetop.php');  
?>
<h2>Modificar Persona</h2>

<form action="PersonaEditar.php" method="post"> 
<table border=0  cellspacing="0">
<tr><td>TIPO: </td>
<td><select name="per_tipo"> 
<?php
$queTipoPersona = "SELECT ID,NOMBRE FROM MYC_TIPOPERSONA ORDER BY ID ASC";
$resTipoPersona = $bdcon->query($queTipoPersona);
$totTipoPersona = $resTipoPersona->num_rows;

if ($totTipoPersona> 0) {
   while ($rowTipoPersona = $resTipoPersona->fetch_assoc()) {
      echo "<option value=".$rowTipoPersona['ID'].">".$rowTipoPersona['NOMBRE']."</option>";          
   }
}

?>
</select></td></tr>

<tr><td>ID:</td><td><input name="per_id" type="text" value="<?php echo $rowPersona['ID'];?>"/></td></tr>
<tr><td>NOMBRE:</td><td><input name="per_nombre" type="text" value="<?php echo $rowPersona['NOMBRE'];?>" size="50"/></td></tr>
<tr><td>DIRECCION:</td><td><input name="per_direccion" type="text" value="<?php echo $rowPersona['DIRECCION'];?>" size="50" /></td></tr>
<tr><td>POBLACION:</td><td><input name="per_poblacion" type="text" value="<?php echo $rowPersona['POBLACION'];?>" size="50" /></td></tr>
<tr><td>PROVINCIA:</td><td><input name="per_provincia" type="text" value="<?php echo $rowPersona['PROVINCIA'];?>" size="50" /></td></tr>
<tr><td>CODIGO POSTAL:</td><td><input name="per_codigopostal" value="<?php echo $rowPersona['CODIGO_POSTAL'];?>" type="text" /></td></tr>
<tr><td>PAIS:</td><td><input name="per_pais" type="text" value="<?php echo $rowPersona['PAIS'];?>" /></td></tr>
<tr><td>TELEFONO:</td><td><input name="per_telefono" value="<?php echo $rowPersona['TELEFONO'];?>" type="text" /></td></tr>
<tr><td>EMAIL:</td><td><input name="per_email" type="text" value="<?php echo $rowPersona['EMAIL'];?>" size="50" /></td></tr>

<tr><td>COMENTARIO:</td><td>
<textarea name="per_comentario" rows="4" cols="50">
<?php echo $rowPersona['COMENTARIO'];?>
</textarea>
</td></tr>
</table>
<input type="submit" value="Modificar" name="submitMe">

</form>
<?php
 include('includes/pagebotton.php');
?>