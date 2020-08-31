<html>
<head>
<title>MATRIX - [POLLA SISTEMAS]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='polla_sistemas.php'; 
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
	
</script>
</head>
<body>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             PROGRAMA PARA LA POLLA DE SISTEMAS                                                           *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Polla para los partidos del mundial o eliminatorias.                                                         |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JUNIO 16 DE 2014.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :6 de OCTUBRE de 2016.                                                                                        |
//MODIFICACION :                                                                                                                            |
//              6 DE OCTUBRE SE ADICIONA BLANCO EN EL NOMBRE DE JUGADORES                                                                   |
//TABLAS UTILIZADAS :                                                                                                                       |
//equipos_000005    : Tabla de la polla.                                                                                                    |
//det_selecciones   : Campos Medico=equipo, Codigo= '10' o '11' o '12',subcodigos                                                           |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.1 Octubre 6 de 2016 - Ing-Gustavo Avendaño";

$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();

// ---------------------------
// --> FUNCIONES
// ---------------------------
function marcadorDisponible($busc, &$marcador)
{
	global $conex;
	 //BUSCA LOS MARCADORES
  $querym = " SELECT descripcion"
            ."   FROM det_selecciones"
            ."  WHERE medico='equipos'"
			."    AND codigo='11'"
			."    AND subcodigo='".$busc."'"
			."    AND activo='A'";
           
   $errm = mysql_query($querym,$conex);
   $numm = mysql_num_rows($errm);
   //echo $query."<br>";

   IF ($numm>0)
   {
		$marcador = mysql_fetch_array($errm);
		$marcador = $marcador['descripcion'];
		return true;
   }
   ELSE
		return false;
}

function asignarMarcador()
{
	global $local;
	global $visitante;
	global $juga;
	global $conex;

	$busc 		= rand(1,24);
	$marcador	= '';
	$disponible = marcadorDisponible($busc, $marcador);
	if($disponible)
	{
		// --> inactivar el marcador
		
		$sqlInac = " UPDATE det_selecciones 
						SET activo = 'I'
					WHERE medico = 'equipos'
			          AND codigo = '11'
			          AND subcodigo = '".$busc."'
			          AND activo='A'";           
		$errm = mysql_query($sqlInac,$conex);
		
		// --> Asignar variable al participante $marcador
		
		$sqlInser = " INSERT equipos_000005 (Fecha_data, nombre,local,marcador,visitante)
                                   VALUES   ('".date('Y-m-d')."', '".$juga."','".$local."','".$marcador."','".$visitante."')";		
						           
		$errm = mysql_query($sqlInser,$conex);
		
		// --> inactivar el JUGADOR
		
		$sqlInacj = " UPDATE det_selecciones 
						SET activo = 'I'
					WHERE medico = 'equipos'
			          AND codigo = '12'
			          AND descripcion = '".$juga."'
			          AND activo='A'";           
		$errm = mysql_query($sqlInacj,$conex);
		
		return ;
	}
	else
	{
		asignarMarcador();		
	}
}

//Encabezado
encabezado("POLLA SISTEMAS",$wactualiz,"clinica");


if (!$usuarioValidado)
{
	
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	//terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 if (!isset($juga) or !isset($juga))
  {
  	echo "<form name='polla_sistemas' action='polla_sistemas.php' method=get>";
	 echo "<input type='HIDDEN' NAME= 'wuser' value='".$wuser."'/>";
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Jugadores
	
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Jugadores:</B><br></font></b><select name='juga' id='searchinput' >";

    $query = " SELECT subcodigo,descripcion"
            ."   FROM det_selecciones"
            ."  WHERE medico='equipos'"
			."    AND codigo='12'"
			."    AND activo='A'";
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
	
	echo "<option></option>";
	
    for ($i=1;$i<=$num3;$i++)
	{
	 $row3 = mysql_fetch_array($err3);
	 echo "<option value='".$row3[1]."'>".$row3[0]."-".$row3[1]."</option>";
	}
   echo "</select></td></tr>";
   echo "<tr><td align=center colspan=4><input type='submit' value='JUGAR'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</form>';
   
   $hoy=date('Y-m-d');

   IF ($num3==0)
   {
   
    //TOTAL x CONEXION
    $query4 = " SELECT nombre,local,marcador,visitante"
             ."   FROM equipos_000005"
             ."  WHERE fecha_data='".$hoy."'";
			           
    $err4 = mysql_query($query4,$conex);
    $num4 = mysql_num_rows($err4);

   
    // Acá la tabla para la impresión
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
    echo "<tr>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>JUGADOR</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>EQUIPO LOCAL</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>MARCADOR</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>EQUIPO VISITANTE</b></td>";
    ECHO "</TR>";
   
    IF ($num4>0)
    {
     for ($i=1;$i<=$num4;$i++)
	 {
	 $row4 = mysql_fetch_array($err4);
   
	  echo "<tr>";
	  echo "<td bgcolor=#FFFFFF align=center><font text color=#006699 size=2><b>".$row4[0]."</b></td>";
	  echo "<td bgcolor=#FFFFFF align=center><font text color=#006699 size=2><b>".$row4[1]."</b></td>";
	  echo "<td bgcolor=#FFFFFF align=center><font text color=#006699 size=2><b>".$row4[2]."</b></td>";
	  echo "<td bgcolor=#FFFFFF align=center><font text color=#006699 size=2><b>".$row4[3]."</b></td>";
	  ECHO "</TR>";
     }
    }
    ELSE
    {
	 echo "<tr><td bgcolor=#FFFFFF colspan='1'><font text color=#006699 size=2>Sin marcador asignado</td></tr>";
	}
   
   }
   ELSE
   {
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";
   	echo "<tr><td bgcolor=#FFFFFF colspan='4'><font text color=#006699 size=2>TODOS LOS JUGADORES TIENEN QUE ESCOGER SU MARCADOR</td></tr>";
    echo "</table>";
   }
   
   echo "</table>";
   
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
 
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>POLLA SISTEMAS</b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   //Inicializo las variables
   $totevento=0;
   $totdiascum=0;
   $totupp=0;
   $totpupp=0;
   $totliii=0;
   $totliv=0;
   
   //EQUIPO LOCAL
   $query1 = " SELECT descripcion"
            ."   FROM det_selecciones"
            ."  WHERE medico='equipos'"
			."    AND codigo='10'"
			."    AND subcodigo='L'"
			."    AND activo='A'";
           
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   //echo $query."<br>";

   $row1 = mysql_fetch_array($err1);
   
   $local=$row1[0];

   //EQUIPO VISITANTE
   $queryv = " SELECT descripcion"
            ."   FROM det_selecciones"
            ."  WHERE medico='equipos'"
			."    AND codigo='10'"
			."    AND subcodigo='V'"
			."    AND activo='A'";
           
   $errv = mysql_query($queryv,$conex);
   $numv = mysql_num_rows($errv);
   //echo $query."<br>";

   $rowv = mysql_fetch_array($errv);
   
   $visitante=$rowv[0];
   
  asignarMarcador();
  
   $hoy=date('Y-m-d');

   //TOTAL x CONEXION
   $query3 = " SELECT nombre,local,marcador,visitante"
            ."   FROM equipos_000005"
            ."  WHERE nombre = '".$juga."'"
			."    AND fecha_data='".$hoy."'";
			           
   $err3 = mysql_query($query3,$conex);
   $num3 = mysql_num_rows($err3);

   
   // Acá la tabla para la impresión
   echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "<tr>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>JUGADOR</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>EQUIPO LOCAL</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>MARCADOR</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>EQUIPO VISITANTE</b></td>";
   ECHO "</TR>";
   
   IF ($num3>0)
   {
    for ($i=1;$i<=$num3;$i++)
	{
	 $row3 = mysql_fetch_array($err3);
   
	  echo "<tr>";
	  echo "<td bgcolor=#FFFFFF align=center><font text color=#006699 size=2><b>".$row3[0]."</b></td>";
	  echo "<td bgcolor=#FFFFFF align=center><font text color=#006699 size=2><b>".$row3[1]."</b></td>";
	  echo "<td bgcolor=#FFFFFF align=center><font text color=#006699 size=2><b>".$row3[2]."</b></td>";
	  echo "<td bgcolor=#FFFFFF align=center><font text color=#006699 size=2><b>".$row3[3]."</b></td>";
	  ECHO "</TR>";
    }
   }
   ELSE
   {
		echo "<tr><td colspan='4'>Sin marcador asignado</td></tr>";
   }
   
   echo "</table>";
   
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Devolver' onclick='VolverAtras();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
 
 }// cierre del else donde empieza la impresión

}

?>
</body>
</html>