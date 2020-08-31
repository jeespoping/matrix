<HTML LANG="es">

<HEAD>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <TITLE>RESULTADOS DE LA BUSQUEDA</TITLE>
    <link href="estilos.css" rel="stylesheet">
</HEAD>

<BODY>
<div class="container" style="margin-top: -30px; margin-left: 10px">
        <div id="loginbox" style="margin-top:50px; width: 1500px">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title">Resultados de la busqueda</div>
                </div>
                <div style="padding-top:30px" class="panel-body" >

<P>Estos son los datos introducidos:</P>

<?php
include_once("conex.php");
 session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  
  
  include_once("root/magenta.php");
  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  
  $conex = obtenerConexionBD("matrix");
   
   $usuario = $_REQUEST['usuario'];
   $nombre = $_REQUEST['nombre'];
   $ccostos = $_REQUEST['ccostos'];
   $estado = $_REQUEST['estado'];
   
   print ("<UL>\n");
   print ("   <LI>Usuario Ingresado: $usuario\n");
   print ("   <LI>Nombre: $nombre\n");
   print ("   <LI>Centro de Costos: $ccostos\n");
   print ("   <LI>Estado: $estado\n");
   print ("</UL>\n");
  
   $nombre1=str_replace(" ","%",$nombre);
   $query = "SELECT codigo,Password,descripcion,Ccostos,Cconom,Activo,Feccap FROM usuarios left join costosyp_000005 on (Ccostos = Ccocod and Ccoemp = '01' )
             Where codigo like '%".$usuario."%' and descripcion like '%".$nombre1."%' and Ccostos like '%".$ccostos."%' and Activo like '%".$estado."%' 
			 order by Activo"; 
   
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  echo "<b>Codigo:</b>&nbsp&nbsp".$registroB[0]."<b>&nbsp&nbsp&nbsp&nbspClave:</b>&nbsp&nbsp".$registroB[1]."<b>&nbsp&nbsp&nbsp&nbspNombre:</b>&nbsp&nbsp".$registroB[2]."<b>&nbsp&nbsp&nbsp&nbsp CCOSTOS:</b>&nbsp&nbsp".$registroB[3]."<b>&nbsp&nbsp&nbsp&nbsp DESCRIPCION:</b>&nbsp&nbsp".$registroB[4]." <b>&nbsp&nbsp&nbsp&nbsp ACTIVO:</b>&nbsp&nbsp".$registroB[5]." <b>&nbsp&nbsp&nbsp&nbsp FECHA:</b>&nbsp&nbsp".$registroB[6]; 
	  echo "<br>";
	  $i++; 
   }
}
?>

[ <A HREF='rep_sop01.php'>Pagina Principal</A> ]
			  </div>
		</div>
	</div>
</div>
</BODY>
</HTML>
