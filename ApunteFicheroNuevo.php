<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  session_start();

  include('includes/bdcon.php');   
  
  $logged=false; 
  if(isset($_SESSION['login'])){
	 $logged=true;
  }else{	header('Location:AndMyCash.php');  }        
  
  if(isset($_POST['submitMe'])){    
      
      //$uploadfile = $uploaddir . md5($_FILES['file']['name']);
      $filename = md5($_FILES['file']['name']).time();
      $uploadfile = PATH_FILES .  $filename;     

      if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
          
          $mimetype = $_FILES['file']['type'];
          $extension =end(explode(".", $_FILES['file']['name']));
          //$apunte_fk = "1";
          $apunte_fk  = $_POST['apunte_fk'];
          $descripcion = $_POST['description'];

          $insertFile = "INSERT INTO MYC_APUNTEFICHERO (APUNTE_FK, DESCRIPCION, FICHERO, EXTENSION, TIPOMIME) VALUES";
          $insertFile .= " ( '".$apunte_fk."','".$descripcion."','".$filename."','".$extension."','".$mimetype."');";            
         
          $resInsertApunte = $bdcon->query($insertFile) or die(mysql_error());        
	   header('Location: ApunteEditar.php?id='.$_POST['apunte_fk']);   	

      } else {
          echo "¡Posible ataque de carga de archivos!\n";
      }
  
  }

  $title= $app_name . " " . $app_version ."  - Adjuntar Fichero apunte";

  include('includes/pagetop.php');  
?>
<body>
<div class="container">
<h2>Nuevo Fichero de Apunte</h2>



<form enctype="multipart/form-data" action="ApunteFicheroNuevo.php" method="post"> 
    
    <!-- MAX_FILE_SIZE debe preceder el campo de entrada de archivo -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />           

<table border=0  cellspacing="0">

<tr><td>Fichero: </td>
<td> <input name="file" type="file" /> </td></tr>

<tr><td>Descripcion:</td>
<td><input name="description" type="text" size="50"/></td></tr>

<tr><td>ID Apunte:</td>
<td><input name="apunte_fk" type="text" size="8" value=<?php echo $_GET['apunte_fk']; ?> /></td></tr>

</table>
<input type="submit" value="Adjuntar Fichero" name="submitMe">

</form>
</div>
<?php
 include('includes/pagebotton.php');
?>