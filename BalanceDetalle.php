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
  
  $title= $app_name . " " . $app_version ."  - Detalle Balance";
  include('includes/pagetop.php');   
  
  $mtime = microtime(); 
  $mtime = explode(" ",$mtime); 
  $mtime = $mtime[1] + $mtime[0]; 
  $tiempoinicial = $mtime;  
 
  $mesACargar=intval(date("m"));
  $anyoACargar=intval(date("Y"));
  $arrMeses = array( "Enero", "Febrero", "Marzo","Abril","Mayo","Junio","Julio","Agosto","Septimebre","Octubre","Noviembre","Diciembre");
  
 ?>
<h2>Balance</h2>

<table border=1  cellspacing="0">
<thead>
<tr>
<th>CATEGORIA</th><th>NOMBRE</th><th>APERTURA</th><th>MES ACTUAL</th><th>AÑO ACTUAL</th><th>BALANCE</th><th>ACCIONES</th>
</tr>
</thead>
<?php

  $queCuentas = "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA FROM MYC_CUENTA CU INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK";
  if(isset($_GET['id'])){
	$queCuentas .= " WHERE CU.ID = ".$_GET['id'];
  }
  $queCuentas .= " ORDER BY CA.ID ASC";

  $resCuentas = $bdcon->query($queCuentas) or die(mysql_error());
  $totCuentas = $resCuentas->num_rows;


  
if ($totCuentas> 0) {
   while ($rowCuenta = $resCuentas->fetch_assoc()) {
   
      $apertura = $rowCuenta['APERTURA'];
   
	  echo "<tr>";      
      echo "<td>".$rowCuenta['CATEGORIA']."</td>";
	  echo "<td><strong>".$rowCuenta['NOMBRE']."</strong></td>";	  
	  echo "<td><div align='right'>".number_format($apertura,2, ',', '.')." &euro;</div></td>";	  	  
   
      // OBTENEMOS EL BALANCE DEL MES ACTUAL
      $MesActual = "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA,'G' TIPO,SUM(-AP.IMPORTE) IMPORTE ";
	  $MesActual .= "FROM MYC_CUENTA CU INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK ";
	  $MesActual .= "INNER JOIN MYC_APUNTE AP ON AP.CUENTA_ORG_FK = CU.ID  ";
	  $MesActual .= "INNER JOIN MYC_TIPOAPUNTE TP ON AP.TIPO_FK = TP.ID ";
	  $MesActual .= "WHERE CU.ID = ".$rowCuenta['ID']." AND MONTH(FECHA)=".$mesACargar." AND YEAR(FECHA)=".$anyoACargar." AND AP.CONCILIADO=1 AND AP.BORRADO=0 ";
	  //$MesActual .= "AND (TP.TIPO = 'G' OR (TP.TIPO IN ('T','C') AND AP.CUENTA_ORG_FK = CU.ID)) GROUP BY CU.ID,CA.NOMBRE,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA ";
	  $MesActual .= "AND AP.CUENTA_ORG_FK = CU.ID GROUP BY CU.ID,CA.NOMBRE,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA ";
	  $MesActual .= "UNION ";
	  $MesActual .= "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA,'I' TIPO,SUM(AP.IMPORTE) IMPORTE FROM MYC_CUENTA CU ";
	  $MesActual .= "INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK INNER JOIN MYC_APUNTE AP ON AP.CUENTA_DEST_FK = CU.ID ";
	  $MesActual .= "INNER JOIN MYC_TIPOAPUNTE TP ON AP.TIPO_FK = TP.ID WHERE CU.ID = ".$rowCuenta['ID']." AND MONTH(FECHA)=".$mesACargar." AND YEAR(FECHA)=".$anyoACargar." ";
	  $MesActual .= "AND AP.CONCILIADO=1 AND AP.BORRADO=0 ";
	  //$MesActual .= "AND TP.TIPO IN ('I','T','C') ";
	  $MesActual .= "GROUP BY CU.ID,CA.NOMBRE,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA ";
	  
	  $resMesActual = $bdcon->query($MesActual) or die(mysql_error());
	  $totMesActual = $resMesActual->num_rows;

	  $importeIngMA = 0;
	  $importeMA = 0;
	  $importeGasMA = 0;
	  	  
      while ($rowMesActual = $resMesActual->fetch_assoc()) {
	    if($rowMesActual['TIPO']!='I') $importeGasMA += (double)$rowMesActual['IMPORTE'];
		else $importeIngMA += (double)$rowMesActual['IMPORTE'];
	  }
	  $importeMA = $importeIngMA + $importeGasMA;
	  
	  // OBTENEMOS EL BALANCE DEL AÑO ACTUAL
	  $AnyoActual = "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA,'G' TIPO,SUM(-AP.IMPORTE) IMPORTE ";
	  $AnyoActual .= "FROM MYC_CUENTA CU INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK ";
	  $AnyoActual .= "INNER JOIN MYC_APUNTE AP ON AP.CUENTA_ORG_FK = CU.ID  ";
	  $AnyoActual .= "INNER JOIN MYC_TIPOAPUNTE TP ON AP.TIPO_FK = TP.ID ";
	  $AnyoActual .= "WHERE CU.ID = ".$rowCuenta['ID']." AND YEAR(FECHA)=".$anyoACargar." AND AP.CONCILIADO=1 AND AP.BORRADO=0 ";	  
	  $AnyoActual .= "AND AP.CUENTA_ORG_FK = CU.ID GROUP BY CU.ID,CA.NOMBRE,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA ";
	  $AnyoActual .= "UNION ";
	  $AnyoActual .= "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA,'I' TIPO,SUM(AP.IMPORTE) IMPORTE FROM MYC_CUENTA CU ";
	  $AnyoActual .= "INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK INNER JOIN MYC_APUNTE AP ON AP.CUENTA_DEST_FK = CU.ID ";
	  $AnyoActual .= "INNER JOIN MYC_TIPOAPUNTE TP ON AP.TIPO_FK = TP.ID WHERE CU.ID = ".$rowCuenta['ID']." AND YEAR(FECHA)=".$anyoACargar." ";
	  $AnyoActual .= "AND AP.CONCILIADO=1 AND AP.BORRADO=0 ";	  
	  $AnyoActual .= "GROUP BY CU.ID,CA.NOMBRE,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA ";
	  
	  $resAnyoActual = $bdcon->query($AnyoActual) or die(mysql_error());
	  $totAnyoActual = $resAnyoActual->num_rows;

	  $importeIngAA = 0;
	  $importeAA = 0;
	  $importeGasAA = 0;
	  	  
      while ($rowAnyoActual = $resAnyoActual->fetch_assoc()) {
	    if($rowAnyoActual['TIPO']!='I') $importeGasAA += (double)$rowAnyoActual['IMPORTE'];
		else $importeIngAA += (double)$rowAnyoActual['IMPORTE'];
	  }
	  $importeAA = $importeIngAA + $importeGasAA;
	        	 
	  $colorImporte = "cG";
	  if($importeMA>=0) $colorImporte = "cI";		 
	  if($importeMA==0) $colorImporte = "cT";		 
	  echo "<td><div align='right'><span class=".$colorImporte.">".number_format($importeMA,2, ',', '.')." &euro;</span></div></td>";
	  
	  $colorImporte = "cG";
	  if($importeAA>=0) $colorImporte = "cI";		  
	  if($importeMA==0) $colorImporte = "cT";		 
	  echo "<td><div align='right'><span class=".$colorImporte.">".number_format($importeAA,2, ',', '.')." &euro;</span></div></td>";
	  
	  // OBTENEMOS EL BALANCE COMPLETO
	  $balance = "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA,'G' TIPO,SUM(-AP.IMPORTE) IMPORTE ";
	  $balance .= "FROM MYC_CUENTA CU INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK ";
	  $balance .= "INNER JOIN MYC_APUNTE AP ON AP.CUENTA_ORG_FK = CU.ID  ";
	  $balance .= "INNER JOIN MYC_TIPOAPUNTE TP ON AP.TIPO_FK = TP.ID ";
	  $balance .= "WHERE CU.ID = ".$_GET['id']." AND AP.CONCILIADO=1 AND AP.BORRADO=0 ";	  
	  $balance .= "AND AP.CUENTA_ORG_FK = CU.ID GROUP BY CU.ID,CA.NOMBRE,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA ";
	  $balance .= "UNION ";
	  $balance .= "SELECT CU.ID,CA.NOMBRE CATEGORIA,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA,'I' TIPO,SUM(AP.IMPORTE) IMPORTE FROM MYC_CUENTA CU ";
	  $balance .= "INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK INNER JOIN MYC_APUNTE AP ON AP.CUENTA_DEST_FK = CU.ID ";
	  $balance .= "INNER JOIN MYC_TIPOAPUNTE TP ON AP.TIPO_FK = TP.ID WHERE CU.ID = ".$_GET['id']." ";
	  $balance .= "AND AP.CONCILIADO=1 AND AP.BORRADO=0 ";	  
	  $balance .= "GROUP BY CU.ID,CA.NOMBRE,CU.NOMBRE,CU.COMENTARIO,CU.APERTURA ";
	  $resBalance = $bdcon->query($balance) or die(mysql_error());
	  $totBalance = $resBalance->num_rows;


	  $importeIng = 0;
	  $importeBalance = 0;
	  $importeGas = 0;
	  	  
      while ($rowBalance = $resBalance->fetch_assoc()) {
	    if($rowBalance['TIPO']!='I') $importeGas += (double)$rowBalance['IMPORTE'];
		else $importeIng += (double)$rowBalance['IMPORTE'];
	  }
	  $importeBalance = $importeIng + $importeGas + $apertura;
	  
	  $colorImporte = "cG";
	  if($importeBalance>=0) $colorImporte = "cI";		  
	  if($importeBalance==0) $colorImporte = "cT";		 
	  echo "<td><div align='right'><strong><span class=".$colorImporte.">".number_format($importeBalance,2, ',', '.')." &euro;</span></strong></div></td>";
	  	  
	  echo "<td> - </td>";
	  echo "</tr>";	  
   }
}
?>
</table>

<table border=1  cellspacing="0">
<thead>
<tr>
<th>MES</th><th>ENTRADA</th><th>SALIDA</th><th>BALANCE</th>
</tr>
</thead>
<?php

      //$Qry_DesMensual = "SELECT MES, CUENTA, CATEGORIA, NOMBRE, ENTRADA, SALIDA FROM v_cuentasmensual ";
	  //$Qry_DesMensual .= "WHERE CUENTA = ".$_GET['id'];
	  
	  $Qry_DesMensual = " SELECT MONTH(AP.FECHA) MES , YEAR(FECHA) ANYO,CU.ID CUENTA,CA.NOMBRE CATEGORIA,CU.NOMBRE, ";
      $Qry_DesMensual .= " (SELECT IFNULL(SUM(IMPORTE),0) FROM MYC_APUNTE WHERE CUENTA_DEST_FK = CU.ID AND YEAR(FECHA) = YEAR(AP.FECHA) AND MONTH(FECHA) = MONTH(AP.FECHA) AND CONCILIADO=1 AND BORRADO=0 ) ENTRADA, ";
      $Qry_DesMensual .= " (SELECT IFNULL(SUM(IMPORTE),0) FROM MYC_APUNTE WHERE CUENTA_ORG_FK = CU.ID AND YEAR(FECHA) = YEAR(AP.FECHA) AND MONTH(FECHA) = MONTH(AP.FECHA) AND CONCILIADO=1 AND BORRADO=0 ) SALIDA ";
      $Qry_DesMensual .= " FROM MYC_CUENTA CU ";
      $Qry_DesMensual .= " INNER JOIN MYC_CATEGORIA CA ON CA.ID = CU.CATEGORIA_FK ";
      $Qry_DesMensual .= " INNER JOIN MYC_APUNTE AP ON (AP.CUENTA_DEST_FK = CU.ID OR AP.CUENTA_ORG_FK = CU.ID) ";
      $Qry_DesMensual .= " INNER JOIN MYC_TIPOAPUNTE TP ON AP.TIPO_FK = TP.ID ";
      //$Qry_DesMensual .= " WHERE YEAR(FECHA)=".$anyoACargar." AND CU.ID = ".$_GET['id']." ";
	  $Qry_DesMensual .= " WHERE FECHA>= DATE_SUB(curdate(), INTERVAL 1 YEAR) AND CU.ID = ".$_GET['id']." ";
      $Qry_DesMensual .= " GROUP BY MONTH(FECHA),YEAR(FECHA),CU.ID  ORDER BY YEAR(FECHA),MONTH(FECHA)";  	  	  
	  	  
	  $resMeses = $bdcon->query($Qry_DesMensual) or die(mysql_error());
	  $totMeses = $resMeses->num_rows;

	  
	  if ($totMeses> 0) {
        while ($rowMeses = $resMeses->fetch_assoc()) {
		  $importeEnt = (double)$rowMeses['ENTRADA'];
		  $importeSal = (double)$rowMeses['SALIDA'];
		  $importeBal = $importeEnt - $importeSal;
		  $colorImporte = "cG";
	      if($importeBal>=0) $colorImporte = "cI";		  
	      if($importeBal==0) $colorImporte = "cT";		
		  echo "<tr><td>[".$rowMeses['ANYO']."] ".$arrMeses[$rowMeses['MES']-1]." </td><td>".number_format($importeEnt,2, ',', '.')." &euro;</td><td>".number_format($importeSal,2, ',', '.')." &euro;</td><td><strong><span class=".$colorImporte." align='right'>".number_format($importeBal,2, ',', '.')." &euro;</span></strong></td></tr>";
	    }
	  }
	  
	  $importeIngMA = 0;
	  $importeMA = 0;
	  $importeGasMA = 0;
?>
</table>

<p>
<?php 
$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$tiempofinal = $mtime; 
$tiempototal = ($tiempofinal - $tiempoinicial); 

echo $totCuentas. " cuentas listadas en ".$tiempototal." segundos";

?>

</p>
<?php
 include('includes/pagebotton.php'); 
?>