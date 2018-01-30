<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  session_start();

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
  $arrAnios = array("2010","2011","2012","2013","2014","2015");
  $arrPeriodicidades = array("Manual","Mensual","Bimensual","Trimestral","Cuatrimestral","Cada 5 meses","Semestral","Cada 7 meses","Cada 8 meses","Cada 9 meses","Cada 10 meses","Cada 11 meses","Anual");
     
 
  //Preparamos la consulta para la extracción de apuntes / transacciones
  $queApuntes = "SELECT AP.ID, DATE_FORMAT(AP.FECHA_INICIO,'%d/%m/%Y') FECHA_INICIO, DATE_FORMAT(AP.FECHA_FIN,'%d/%m/%Y') FECHA_FIN,AP.TIPO_FK TIPO_FK, ";
  $queApuntes .= "DATE_FORMAT(AP.PROXIMA_FECHA,'%d/%m/%Y') PROXIMA_FECHA,TP.NOMBRE TIPO,TP.TIPO GTIPO,CO.NOMBRE ORIGEN,CD.NOMBRE DESTINO,";
  $queApuntes .= "PE.NOMBRE BENEFICIARIO,FORMAT(AP.IMPORTE,2) IMPORTE , AP.PERIODO, AP.ACTIVO ";
  $queApuntes .= "FROM MYC_PLANIFICADO AP INNER JOIN MYC_TIPOAPUNTE TP ON TP.ID = AP.TIPO_FK INNER JOIN MYC_CUENTA CO ON CO.ID = AP.CUENTA_ORG_FK ";
  $queApuntes .= "INNER JOIN MYC_CUENTA CD ON CD.ID = AP.CUENTA_DEST_FK LEFT OUTER JOIN MYC_PERSONA PE ON PE.ID = AP.PERSONA_FK ";
  
  $mesACargar=intval(date("m"));
  $anyoACargar=intval(date("Y"));
  $tipoACargar=0;
    
  $queApuntes .= " ORDER BY ACTIVO DESC,DATE_FORMAT(AP.PROXIMA_FECHA,'%Y%m%d') ASC; ";
  
  $title= $app_name . " " . $app_version ."  - Listado de apuntes periodicos";
  include('includes/pagetop.php');   	
    
?>
<h2>Listado de transacciones periodicas</h2>

<table border=1  cellspacing="0">
<thead>
<tr>
<th>I</th><th>PROXIMA</th><th>FIN</th><th>PER.</th><th width="150px">TIPO</th><th width="150px">ORIGEN</th><th width="150px">DESTINO</th><th width="150px">BENEFICIARIO</th><th width="95px">IMPORTE</th><th>E</th><th>ACC.</th>
</tr>
</thead>
<?php

$resApuntes = $bdcon->query($queApuntes) or die(mysql_error());
$totApuntes = $resApuntes->num_rows;

$importeGastos=0;
$importeIngresos=0;

if ($totApuntes> 0) {
   while ($rowApunte = $resApuntes->fetch_assoc()) {
      $importe = str_replace(",","",$rowApunte['IMPORTE']);
      echo "<tr>";      
	  $tipoTrans="G";
	  
   switch ($rowApunte['GTIPO']) {
     case "I":
        $importeIngresos+=(double)$importe;
        break;
    case "G":
        $importeGastos-=(double)$importe;
        break;
    case "T":
        $importeGastos-=(double)$importe;
        break;
	}
	  	  	 
	  echo "<td><img src='images/tap_".$rowApunte['GTIPO'].".png' /></td>";
	  echo "<td>".$rowApunte['PROXIMA_FECHA']."</td>";
	  echo "<td>".$rowApunte['FECHA_FIN']."</td>";
	  echo "<td>".$arrPeriodicidades[$rowApunte['PERIODO']]."</td>";	  
    echo "<td>".$rowApunte['TIPO']."</td>";
	  echo "<td>".$rowApunte['ORIGEN']."</td>";
	  echo "<td>".$rowApunte['DESTINO']."</td>";
	  echo "<td>".$rowApunte['BENEFICIARIO']."</td>";	  
	  echo "<td><div align='right'><strong><span class=c".$rowApunte['GTIPO'].">".$rowApunte['IMPORTE']." &euro;</span></strong></div></td>";
	  
	  if($rowApunte['ACTIVO']==0) echo "<td><div align='right'><input type='checkbox' disabled='disabled'></div></td>";
	  else echo "<td><div align='right'><input type='checkbox' disabled='disabled' checked></div></td>";	  

	  //	  echo "<td></td>";	  
	  echo "<td><a href='ApuntePeriodicoEditar.php?id=".$rowApunte['ID']."'>Editar</a>";
	  //echo "<a href='ApunteBorrar.php?id=".$rowApunte['ID']."'>B</a></td>";
	  echo "</td></tr>";	
	    

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
<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><strong>TOTAL</strong></td><td colspan="2"><div align='right'><strong><?php echo number_format($importeIngresos+$importeGastos,2);?> &euro;</strong></div></td><td></td><td></td>
</tr>
</table>

<p><a href="ApuntePeriodicoNuevo.php">Nueva transacci&oacute;n periodica</a></p>
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