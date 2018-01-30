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
	$insertApunte = "INSERT INTO MYC_PERSONA (NOMBRE, DIRECCION, POBLACION, PROVINCIA, CODIGO_POSTAL, PAIS, TELEFONO, EMAIL, COMENTARIO, TIPO_FK) VALUES";
	$insertApunte .= " ( '".$_POST['per_nombre']."','".$_POST['per_direccion']."','".$_POST['per_poblacion']."','".$_POST['per_provincia']."','".$_POST['per_codigopostal'];
  $insertApunte .="','".$_POST['per_pais']."','".$_POST['per_telefono']."','".$_POST['per_email']."','".$_POST['per_comentario']."',".$_POST['per_tipo'].");";	

  $resInsertApunte = $bdcon->query($insertApunte);

	header('Location: PersonaListado.php');   	
  }
  $title= $app_name . " " . $app_version ."  - Nueva Persona";
  include('includes/pagetop.php');  
?>
<body>
<div class="container">
<h2>Nueva Persona</h2>

<form action="PersonaNuevo.php" method="post"> 
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

<tr><td>NOMBRE:</td><td><input name="per_nombre" type="text" size="50"/></td></tr>
<tr><td>DIRECCION:</td><td><input name="per_direccion" type="text" size="50" /></td></tr>
<tr><td>POBLACION:</td><td><input name="per_poblacion" type="text" size="50" /></td></tr>
<tr><td>PROVINCIA:</td><td><input name="per_provincia" type="text" size="50" /></td></tr>
<tr><td>CODIGO POSTAL:</td><td><input name="per_codigopostal" type="text" /></td></tr>
<tr><td>PAIS:</td><td><input name="per_pais" type="text" /></td></tr>
<tr><td>TELEFONO:</td><td><input name="per_telefono" type="text" /></td></tr>
<tr><td>EMAIL:</td><td><input name="per_email" type="text" size="50" /></td></tr>

<tr><td>COMENTARIO:</td><td>
<textarea name="per_comentario" rows="4" cols="50">
</textarea>
</td></tr>
</table>
<input type="submit" value="Insertar" name="submitMe">

</form>
</div>
<?php
 include('includes/pagebotton.php');
?>