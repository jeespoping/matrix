<html>
<head>
<title>MATRIX - [REPORTE DOCUMENTOS ESCANEADOS POR HISTORIA E INGRESO]</title>

<meta http-equiv="expires" content="0">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Pragma" CONTENT="no-cache">

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_admdoc.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_admdoc.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                     REPORTE DOCUMENTOS ESCANEADOS POR HISTORIA E INGRESO                                                                 *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : REPORTE DOCUMENTOS ESCANEADOS POR HISTORIA E INGRESO                                                        |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Oct 30 de 2013.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : Oct 30 de 2013.                                                                                             |
//DESCRIPCION			      : Este reporte sirve para buscar los documentos escaneados por historia e ingreso                             |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 30-Nov-2013";

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
//Encabezado
encabezado("REPORTE DOCUMENTOS ESCANEADOS POR HISTORIA E INGRESO",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 $key = substr($user,2,strlen($user));
	

	

	$conexUnix = odbc_connect('facturacion','','')
			or die("No se pudo lograr conexion");
		
	if(!isset($hist) || $hist == ""  || $hist == " ")
	{
		echo "<form action='rep_admdoc.php' method=post>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=2><b>CLÍNICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=2>DOCUMENTOS ESCANEADOS POR HISTORIA E INGRESO</td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc >HISTORIA-INGRESO:</TD><td align=center bgcolor=#cccccc ><input type='input' name='hist'></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
  
 else // Cuando ya estan todos los datos escogidos
  {	
	$historia = explode("-",$hist);
	$query = " SELECT Pactid,Pacced,concat(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) ".
			     " FROM root_000037,root_000036 ".
			     " WHERE Orihis = '".$historia[0]."'".
				 "  and  Oriori = '01' and Oritid = Pactid and Oriced = Pacced";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
	////////////////ACA COMIENZA LA IMPRESION
			echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
			echo "<tr>";
			echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>DOCUMENTOS ESCANEADOS POR HISTORIA E INGRESO</b></font></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>Historia-ingreso:".$hist."</i></b></font></b></font></td>";
			echo "<tr>";
			echo "<tr>";
			echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>Documento:&nbsp&nbsp".$row[0]."-".$row[1]."&nbsp&nbspPaciente:&nbsp&nbsp".$row[2]."</i></b></font></b></font></td>";
			echo "<tr>";
			echo "</table>";
			echo "<br>";			
			
	$cadena_buscada   = '-';
	$pos_coincidencia = strpos($hist, $cadena_buscada);
	if ($pos_coincidencia == false )
		{    
			$query = "	SELECT egrhis his,egrnum num "
				 ." 	FROM inmegr Where egrhis = ".$hist
				 ."  	 union "
				 ."  	SELECT pachis his,pacnum num "
				 ." 	FROM inpac Where pachis = ".$hist
				 ." 	order  by 1,2 Desc ";
		
			$resultado = odbc_do($conexUnix,$query);
						
					$i = 0;
					echo "<center><table border=0 width=300>";
					while (odbc_fetch_row($resultado))
						{
							$i++;
							
							if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
							 $wcf="DDDDDD";  
							else
							 $wcf="CCFFFF";
							 
							echo "<td colspan=2 align=left bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,1)."</td>"; 
							echo "<td colspan=2 align=left bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result($resultado,2)."</td>";
							echo "<td colspan=2 align=center bgcolor=".$wcf."><A HREF='rep_admdoc.php?hist=".odbc_result($resultado,1)."-".odbc_result($resultado,2)."'>Procesar</td>";
							echo "<tr>";	  		  
							 
						}  
					echo "</table>";
					
					

		} 
	else 
		{
				//echo "	<iframe width=100% height=100% align=center src='http://bart.lasamericas.com.co/scan/".$hist."'></iframe>";		
				echo "        <iframe width=100% height=100% align=center src='http://mtx.lasamericas.com.co/scan/".$hist."'></iframe>";
			
	    }
  }// cierre del else donde empieza la impresión
	
	odbc_close($conexUnix);
	odbc_close_all();

} 

?>
