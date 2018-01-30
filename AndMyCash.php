<?php
error_reporting(E_ALL);
session_start();
ini_set("display_errors", 1);
include('includes/bdcon.php');
$error=false;
$logged=false;
if($_POST)
{
  $logged = ObtenerUsuario($_POST['log_name'],$_POST['log_pwd'],$bdcon);
  if($logged){
      $_SESSION['login']=$_POST['log_name'];
  }else{
    $error=true;
  }
}
else{
   if(isset($_SESSION['login'])){
    $logged=true;
  }
}
$title= $app_name . " " . $app_version ."  - Aplicacion de contabilidad domestica";
include('includes/pagetop.php');
?>
<body>
<div class="container">
<h1>AndMyCash</h1>
<hr>
<p>Bienvenido a AndMyCash, la aplicaci&oacute;n de contabilidad domestica por MaRuGi 2018</p>
<?php
if($error){
    echo "<div class='error'>Error al iniciar sesion</div>";
}
?>
<?php
//if(isset($_SESSION('login'))){
//}
if($logged){
?>
<table border="0" cellspacing="0" cellpadding="0">
        <caption>Seleccione una opcion del listado para continuar</caption>
        <thead>
          <tr>
            <th class="span-6">Opci&oacute;n</th>
            <th class="span-10">Descrici&oacute;n</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><a href="ApunteNuevo.php">Nueva Transacci&oacute;n</a></td>
            <td>Crear nueva transacci&oacute;n</td>
          </tr>
      <tr>
            <td><a href="ApunteListado.php">Listado Transacciones</a></td>
            <td>Listado de transacciones</td>
          </tr>
      <tr>
            <td><a href="CuentaListado.php">Listado de cuentas</a></td>
            <td>Listado de cuentas</td>
          </tr>
          <tr>
            <td><a href="PersonaListado.php">Listado de personas</a></td>
            <td>Listado de personas</td>
          </tr>
      <tr>
            <td><a href="ApuntePeriodicoListado.php">Transacciones Periodicas</a></td>
            <td>Transacciones programadas</td>
          </tr>
          <tr>
            <td><a href="Balance.php">Balance</a></td>
            <td>Balance de cuentas</td>
          </tr>
          <tr>
            <td>Estadisticas</td>
            <td>Estadisticas de cuentas</td>
          </tr>
          <tr>
            <td><a href="Administracion.php">Administración</a></td>
            <td>Opciones de administración</td>
          </tr>
          <tr>
            <td><a href="Acerca.php">Acerca de</td>
            <td>Acerca de AndMyCash</td>
          </tr>
       </tbody>
       </table>

<?php
}else{
?>
<form action="AndMyCash.php" method="post">
<table border=0 cellspacing="0">
<tr><td>USUARIO:</td><td><input name="log_name" type="text" /></td></tr>
<tr><td>CONTRASEÑA:</td><td><input name="log_pwd" type="password" /></td></tr>
</table>
<input type="submit" value="Acceder" name="login_frm">
</form>
<?php
}
?>
</div>
</body>
</html>
