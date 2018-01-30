<!doctype html>
<html lang="es">
<head>
<link href='http://fonts.googleapis.com/css?family=Lilita+One' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>	
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">  
<link rel="stylesheet" href="css/screen.css" type="text/css">
  <title><?php 
echo $title; 
?></title>
</head>
<body>
<script>
  $(function() {            
     $("#datepicker").datepicker({ 
        constrainInput: true,         
        firstDay: 1,
        dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
        monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
        dateFormat: "dd/mm/yy" 
      }).val()        
  });
</script>
<div class="container">