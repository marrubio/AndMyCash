<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  session_start();
  header('Content-type: text/html; charset=utf-8');
  include('includes/bdcon.php'); 

  // condiciones de autentificacion
  $logged=false; 
  if(isset($_SESSION['login'])){
	$logged=true;
  }else{	header('Location:AndMyCash.php'); 
  }  
  
  // calculo del tiempo de carga de la página
  $mtime = microtime(); 
  $mtime = explode(" ",$mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $tiempoinicial = $mtime; 
  $arrMeses = array( "Enero", "Febrero", "Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
  $arrAnios = array("2010","2011","2012","2013","2014","2015","2016","2017","2018","2019");
  $arrPeriodicidades = array("Manual","Mensual","Bimensual","Trimestral","Cuatrimestral","Cada 5 meses","Semestral","Cada 7 meses","Cada 8 meses","Cada 9 meses","Cada 10 meses","Cada 11 meses","Anual");
   
  //$db_selected= mysql_select_db($database_bdcon,$bdcon);

   //mysql_query("SET character_set_client = 'utf8';");
   //mysql_query("SET character_set_result = 'utf8';");
 
  //Preparamos la consulta para la extracción de apuntes / transacciones
  $queApuntes = "SELECT AP.ID, DATE_FORMAT(AP.FECHA,'%d/%m/%Y') FECHA,AP.TIPO_FK TIPO_FK, TP.NOMBRE TIPO,TP.TIPO GTIPO,CO.NOMBRE ORIGEN,CD.NOMBRE DESTINO,PE.NOMBRE BENEFICIARIO,AP.COMENTARIO,FORMAT(AP.IMPORTE,2) IMPORTE,AP.CONCILIADO ";
  $queApuntes .= "FROM MYC_APUNTE AP INNER JOIN MYC_TIPOAPUNTE TP ON TP.ID = AP.TIPO_FK INNER JOIN MYC_CUENTA CO ON CO.ID = AP.CUENTA_ORG_FK ";
  $queApuntes .= "INNER JOIN MYC_CUENTA CD ON CD.ID = AP.CUENTA_DEST_FK LEFT OUTER JOIN MYC_PERSONA PE ON PE.ID = AP.PERSONA_FK ";
  
  $mesACargar=intval(date("m"));
  $anyoACargar=intval(date("Y"));
  $tipoACargar=0;
  
  if(isset($_POST['lsa_mes'])){	
	$mesACargar=$_POST['lsa_mes'];
  }
  if(isset($_POST['lsa_anyo'])){	
	$anyoACargar=$_POST['lsa_anyo'];
  }
  if(isset($_POST['lsa_cuentas'])){	
	$tipoACargar=$_POST['lsa_cuentas'];
  }
  $queApuntes .= "WHERE MONTH(FECHA) = ".$mesACargar;
  $queApuntes .= " AND YEAR(FECHA) = ".$anyoACargar;
  
  if($tipoACargar!=0){
    $queApuntes .= " AND (CUENTA_ORG_FK= ".$tipoACargar." OR CUENTA_DEST_FK=".$tipoACargar.")";  
  }   
  $queApuntes .= " AND BORRADO=0 ORDER BY DATE_FORMAT(AP.FECHA,'%Y%m%d') DESC,ID DESC; ";
  
  //CONSULTA PARA OBTENER LOS APUNTES PERIODICOS
  $queApuntesPer = "SELECT AP.ID, DATE_FORMAT(AP.PROXIMA_FECHA,'%d/%m/%Y') FECHA, DATE_FORMAT(AP.FECHA_FIN,'%d/%m/%Y') FECHA_FIN,AP.TIPO_FK TIPO_FK, TP.NOMBRE TIPO,";
  $queApuntesPer .= "TP.TIPO GTIPO,CO.NOMBRE ORIGEN,CD.NOMBRE DESTINO,PE.NOMBRE BENEFICIARIO,FORMAT(AP.IMPORTE,2) IMPORTE , AP.PERIODO, AP.ACTIVO, DATE_FORMAT(AP.PROXIMA_FECHA,'%Y%m%d') FECHA_C ";
  $queApuntesPer .= "FROM MYC_PLANIFICADO AP INNER JOIN MYC_TIPOAPUNTE TP ON TP.ID = AP.TIPO_FK INNER JOIN MYC_CUENTA CO ON CO.ID = AP.CUENTA_ORG_FK ";
  $queApuntesPer .= "INNER JOIN MYC_CUENTA CD ON CD.ID = AP.CUENTA_DEST_FK LEFT OUTER JOIN MYC_PERSONA PE ON PE.ID = AP.PERSONA_FK ";  
  $queApuntesPer .= "WHERE MONTH(PROXIMA_FECHA) <= ".$mesACargar;
  $queApuntesPer .= " AND YEAR(PROXIMA_FECHA) <= ".$anyoACargar;
  $queApuntesPer .= " AND ACTIVO = 1";
  
  if($tipoACargar!=0){
    $queApuntesPer .= " AND (CUENTA_ORG_FK= ".$tipoACargar." OR CUENTA_DEST_FK=".$tipoACargar.")";  
  }     
  $queApuntesPer .= " ORDER BY PROXIMA_FECHA ASC,ID DESC";
  
  
  $title= $app_name . " " . $app_version ."  - Listado de apuntes V2";
  include('includes/pagetop.php');   	
?>

<h2>Listado de transacciones
<?php echo $arrMeses[$mesACargar-1]." de ".$anyoACargar;?>
</h2>


<form action="ApunteListado.php" method="post"> 
<table border=0  cellspacing="0">
<tr>
<td>
<select name="lsa_cuentas"> 
<option value=0>Todas las cuentas</option>
<?php
//Cargamos el combo de tipos de apunte
$queCuentaOrg = "SELECT CU.ID,CU.NOMBRE FROM MYC_CUENTA CU INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK ORDER BY CA.TIPO_FK,CU.NOMBRE ASC";
$resCuentaOrg = $bdcon->query($queCuentaOrg) or die(mysql_error());
$totCuentaOrg = $resCuentaOrg->num_rows;

if ($totCuentaOrg> 0) {
   while ($rowCuentaOrg = $resCuentaOrg->fetch_assoc()) {
   if($tipoACargar==$rowCuentaOrg['ID']) echo "<option value=".$rowCuentaOrg['ID']." selected>".$rowCuentaOrg['NOMBRE']."</option>";          
    else echo "<option value=".$rowCuentaOrg['ID'].">".$rowCuentaOrg['NOMBRE']."</option>";          
   }
}
?>
</select>
</td>
<td>
<select name="lsa_mes">
<?php
//Cargamos el combo de meses y seleccionamos el mes a buscar
$arrlength=count($arrMeses);
for($x=0;$x<$arrlength;$x++)
  {
	if($mesACargar==$x+1) echo "<option value=".($x+1)." selected>".$arrMeses[$x]."</option>";  
	else echo "<option value=".($x+1).">".$arrMeses[$x]."</option>";  
  }
?>
</select>
</td>
<td>
<select name="lsa_anyo">
<?php
//Cargamos el combo de años y seleccionamos el año a buscar
$arrlength=count($arrAnios);
for($y=0;$y<$arrlength;$y++)
  {
	if($anyoACargar==$arrAnios[$y]) echo "<option value=".$arrAnios[$y]." selected>".$arrAnios[$y]."</option>";  
	else echo "<option value=".$arrAnios[$y].">".$arrAnios[$y]."</option>";  
  }
?>
</select>
</td>
<td>
<input type="submit" value="Buscar" name="btnbuscar">
</td>
</tr>
</table>
</form>

<h3>Transacciones periodicas pendientes</h3>

<table border=1  cellspacing="0">
<thead>
<tr>
<th>I</th><th>FECHA</th><th>FIN</th><th>PER.</th><th width="150px">TIPO</th><th width="150px">ORIGEN</th><th width="150px">DESTINO</th><th width="150px">BENEFICIARIO</th><th width="85px">IMPORTE</th><th width="40px">ACC.</th>
</tr>
</thead>
<?php


$resApuntesPer = $bdcon->query($queApuntesPer) or die(mysql_error());
$totApuntesPer = $resApuntesPer->num_rows;


if ($totApuntesPer> 0) {
   while ($rowApunte = $resApuntesPer->fetch_assoc()) {
      $importe = str_replace(",","",$rowApunte['IMPORTE']);      
	  $tipoTrans="G";	 

	  $fhoy = date("Ymd");    
    $fcompar = $rowApunte['FECHA_C'];	 
	  
    //Si la fecha del proximo apunte es anterior a la actula remarcamos el registro
    if($fhoy > $fcompar){
       echo "<tr class='FondoRojo'>";
    }else{
       echo "<tr class='colorNegro'>";
    }

    /* echo "<td><img src='images/tap_".$rowApunte['GTIPO'].".png' /></td>";    */

    echo "<td width='18'><svg xmlns='http://www.w3.org/2000/svg' version='1.1' height='15' width='15'>";
    echo " <circle cx='7' cy='7' r='6' stroke='black' stroke-width='2' fill='yellow' /></td>";

    echo "<td>".$rowApunte['FECHA']."</td>";
    echo "<td>".$rowApunte['FECHA_FIN']."</td>";   
    echo "<td>".$arrPeriodicidades[$rowApunte['PERIODO']]."</td>";    	  
    echo "<td>".$rowApunte['TIPO']."</td>";
	  echo "<td>".$rowApunte['ORIGEN']."</td>";
	  echo "<td>".$rowApunte['DESTINO']."</td>";
	  echo "<td>".$rowApunte['BENEFICIARIO']."</td>";	  
	  echo "<td><div align='right'><strong>".$rowApunte['IMPORTE']." &euro;</span></strong></div></td>";
	  	 
	  echo "<td><a href='ApuntePeriodicoGenerar.php?id=".$rowApunte['ID']."' alt='Generar apunte'><img src='images/masM.svg' width='15' width='15' ></a>";
	  echo "<a href='ApuntePeriodicoEditar.php?id=".$rowApunte['ID']."'><img src='images/pencillM.svg' width='15' width='15'></a></td>";
	  echo "</tr>";	
	    

   }
}
?>
<tr>
</table>
<table border=1  cellspacing="0">
<thead>
<tr>
<th>I</th><th>FECHA</th><th width="150px">TIPO</th><th width="150px">ORIGEN</th><th width="150px">DESTINO</th><th width="150px">BENEFICIARIO</th><th width="100px">COMENTARIO</th><th width="85px">IMPORTE</th><th>C</th><th width="40px">ACC</th>
</tr>
</thead>
<?php
$resApuntes = $bdcon->query($queApuntes) or die(mysql_error());
$totApuntes = $resApuntes->num_rows;
$importeGastos=0;
$importeIngresos=0;
$color="yellow";

if ($totApuntes> 0) {
   while ($rowApunte = $resApuntes->fetch_assoc()){
      $importe = str_replace(",","",$rowApunte['IMPORTE']);
      echo "<tr>";      
	  $tipoTrans="G";
	  
   switch ($rowApunte['GTIPO']) {
     case "I":
        $importeIngresos+=(double)$importe;
        $color="#5f9b0a";
        break;
    case "G":
        $importeGastos-=(double)$importe;
        //$color="#820000";
        $color="#FF0000";
        break;
    case "C":
        //$importeGastos-=(double)$importe;
        $color="#008EAB";        
        break;
    case "X":        
        $color="yellow";
        break;        
	}
	  	  	 
	  /* echo "<td><img src='images/tap_".$rowApunte['GTIPO'].".png' /></td>";*/

    echo "\n<td width='22'><svg xmlns='http://www.w3.org/2000/svg' version='1.1' height='15' width='15'> <polygon points='1,5 7,5 7,1 15,7 7,15 7,10 1,10' style='fill:".$color.";stroke:#1C2224;stroke-width:2;fill-rule:evenodd;''></td>\n";

	  echo "<td>".$rowApunte['FECHA']."</td>";
    echo "<td>".$rowApunte['TIPO']."</td>";
	  echo "<td>".$rowApunte['ORIGEN']."</td>";
	  echo "<td>".$rowApunte['DESTINO']."</td>";
	  echo "<td>".$rowApunte['BENEFICIARIO']."</td>";
	  echo "<td>".$rowApunte['COMENTARIO']."</td>";
	  echo "<td><div align='right'><strong><span class=c".$rowApunte['GTIPO'].">".$rowApunte['IMPORTE']." &euro;</span></strong></div></td>";
	  if($rowApunte['CONCILIADO']==0) echo "<td><div align='right'><input type='checkbox' name='".$rowApunte['ID']."cta_conciliado' disabled='disabled'></div>";
	  else echo "<td><div align='right'><input type='checkbox' name='".$rowApunte['ID']."cta_conciliado' disabled='disabled' checked></div>";
	  echo "<td><a href='ApunteEditar.php?id=".$rowApunte['ID']."'><img src='images/pencillM.svg' width='15' width='15'></a>";
	  echo "<a href='ApunteBorrar.php?id=".$rowApunte['ID']."'><img src='images/basuraM.svg' width='15' width='15'></a></td>";
	  echo "</tr>";	
	  echo "\n";

   }
}
?>
<tr>
<td></td><td></td><td></td><td></td><td></td><td></td><td>INGRESOS</td><td colspan="2"><div align='right'><strong><span class=cI><?php echo number_format($importeIngresos,2);?> &euro;</span></strong></div></td><td></td>
</tr>
<tr>
<td></td><td></td><td></td><td></td><td></td><td></td><td>GASTOS</td><td colspan="2"><div align='right'><strong><span class=cG><?php echo number_format($importeGastos,2);?> &euro;</span></strong></div></td><td></td>
</tr>
<tr>
<td></td><td></td><td></td><td></td><td></td><td></td><td><strong>TOTAL</strong></td><td colspan="2"><div align='right'><strong><?php echo number_format($importeIngresos+$importeGastos,2);?> &euro;</strong></div></td><td></td>
</tr>
</table>

<p>
  <a href="ApunteNuevo.php"> <img src="images/mas.svg" width="19px">
  Nueva transacci&oacute;n</a>
</p>

<p><?php 
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$tiempofinal = $mtime; 
$tiempototal = ($tiempofinal - $tiempoinicial); 

echo $totApuntes. " transacciones listadas en ".$tiempototal." segundos";

?>
</p>
<?php
 include('includes/pagebotton.php');
?>			