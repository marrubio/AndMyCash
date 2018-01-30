<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  session_start();
  include('includes/bdcon.php');   	
  $error=false;
  $logged=false;  


/* backup the db OR just a table */
//function backup_tables($host,$user,$pass,$name,$tables = '*')
function backup_tables($host,$user,$pass,$name)
{
	
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($name,$link);
	$return = "";
	
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			//echo $row[0] ." <br>";
			$tables[] = $row[0];
		}

	//cycle through
	foreach($tables as $table)
	{		
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					//$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
		
	//save file
	$filename = 'Backup/AndMyCash-db-backup-'.date("Ymd").'-'.time().'.zip';
	//$handle = fopen($filename,'w+');
	//fwrite($handle,$return);
	//fclose($handle);	

	$zip = new ZipArchive();	
	if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
	    exit("cannot open <$filename>\n");
	}
	$zip->addFromString("bbdd.sql", $return);	
	$zip->close();

	$mm_type="application/octet-stream";	
	header("Cache-Control: public, must-revalidate");
	header("Pragma: hack");
	header("Content-Type: " . $mm_type);	
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header("Content-Transfer-Encoding: binary\n");
	header('Location: '.$filename);
	exit;
}

  $logged=false; 
  if(isset($_SESSION['login'])){
	$logged=true;
  }else{	header('Location:AndMyCash.php'); 
  }  

if($logged && isset($_GET['op'])){
    	if($_GET['op']=="bd"){
    		backup_tables($hostname_bdcon, $username_bdcon, $password_bdcon, $database_bdcon);
    	}
}

$title= $app_name . " " . $app_version ."  - Administración";
include('includes/pagetop.php');   	
?>
<body>
<div class="container">
<h1>AndMyCash - Administración</h1>
<hr>
<h3>Opciones administrativas</h3>
<ul>
	<li><a href="Administracion.php?op=bd">Copia de seguridad BBD</a></li>
	<li>Permisos de usuarios</li>
</ul>


<h3>Consultas BBDD - Sin resultado</h3>
<div>
<?php
// Cuando recibimos consulta a ejecutar
 if(isset($_POST['sql'])){ 		
	$result_consulta = $bdcon->query($_POST['sql']) or die(mysql_error());	

	if($result_consulta){
		echo "<div class='success'>Consulta ejecutada correctamente [".$result_consulta."]</div>";				
	}else{
		echo "<div class='error'>Fallo al ejecutar la consulta [".$result_consulta."]</div>";		
	}
	
  }
 ?>
<form action="Administracion.php" method="POST">
<!--    <input type="text" name="sql" id="sql" size=100> -->
   <textarea rows="7" cols="75" name="sql" id="sql"></textarea >
    <br />
    <input type="submit" name="submit" value="Ejecutar">
</form>
</div>
<?php include('includes/pagebotton.php');
?>

</div>

</body>
</html>			