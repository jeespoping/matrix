<?php
include_once("conex.php");
if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
?>
<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USUARIOS - PAGINA PRINCIPAL</title>
    <link href="estilos.css" rel="stylesheet">
</head>

<body>
    <div class="container" style="margin-top: -30px; margin-left: 10px">
        <div id="loginbox" style="margin-top:50px; width: 580px">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title">BUSQUEDA USUARIOS Y CONTRASEÑAS</div>
                </div>
                <div style="padding-top:30px" class="panel-body" >
                    
					<FORM CLASS="borde" ACTION="rep_sop01form.php" METHOD="POST">
					<P><LABEL>Codigo Usuario:</LABEL>
					<INPUT TYPE="TEXT" SIZE="10" NAME="usuario"></P>
					<P><LABEL>Nombre:</LABEL>
					<INPUT TYPE="TEXT" SIZE="40" NAME="nombre"></P>
					<P><LABEL>Ccostos:</LABEL>
					<INPUT TYPE="TEXT" SIZE="5" NAME="ccostos"></P>
					<P><LABEL>Estado(A=Activos/I=Inactivos):</LABEL>
					<INPUT TYPE="TEXT" SIZE="1" NAME="estado"></P>
					
					
					<P><INPUT TYPE="SUBMIT" NAME="buscar" VALUE="Enviar"></P>

					</FORM>


                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}
?>