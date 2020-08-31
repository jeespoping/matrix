<html>
<head>
<title>Afinidad</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
</head>
<body >
<?php
include_once("conex.php");

/************************************************************************************************************************
 * Actualizaciones por persona
 * 
 *	Muuestra la cantidad de actualizaciones que ha realizado cada persona
 *
 * 
 * @name matrix\magenta\procesos\reportePer.php
 * @author Carolina Castaño protilla
 * @created 2006-01-07
 * @version 2007-01-24
 * 
 * @modified 2007-01-24  documentacion, Carolina Castaño
 * 
 * @table magenta_00008, select 
 * @table magenta_00007, select 
 * @table noper, select, 
 * 
 * @wvar $color varia el color de la presentacion en tabla
 * @wvar $perActualiza persona que asctualiza (codigo y nombre)
 * @wvar $perCod codigo del empleado
 * @wvar $perDoc clave de la persona que actualiza
 */
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-4 (Arleyda Insignares C.)
						-Se cambia encabezado, titulo y tabla con ultimo formato 
*************************************************************************************************************************/
$wautor   ="Carolina Castano P.";
$wversion ='2007-01-25';
$wactualiz='2016-05-02';

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	include_once("root/comun.php");

    $titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";

    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");  

	echo "<table align='center'>\n" ;
	echo "<tr>" ;
	echo "<td><img src='/matrix/images/medical/Magenta/aaa.gif'  height='61' width='113'></td>";
	echo "</tr>" ;
	echo "</table></br>" ;

	echo "<div align='center'><div align='center' class='fila1' style='width:480px;'><font size='4'><b>CONSULTA E INGRESO DE COMENTARIOS</b></font></div></div></BR>";
	//echo "<center><b><font size=\"4\"><A HREF='reportePer.php'><font color=\"#D02090\">NUMERO DE ACTUALIZACIONES POR EMPLEADO</font></a></b></font></center>\n" ;
	//echo "<center><b><font size=\"2\"><font color=\"#D02090\">reportePer.php</font></font></center></br></br></br>\n" ;
	echo "\n" ;

	/**
	 * conexion a base de datos
	 */
	

	


	//busco las personas registradas como usuarias del programa de afinidad
	$query ="SELECT Codigo_nomina,  Documento  FROM magenta_000007";
	$result = mysql_query($query);

	$i=0;
	While ( $resulta = mysql_fetch_row($result) )
	{
		$perCod[$i]=$resulta[0];
		$perDoc[$i]=$resulta[1];
		$i++;
	}

	echo "<table border=0 align=center>";
	echo "<tr><td align=center bgcolor='#cccccc'><font size=4>Empleado:</font></td>";
	echo "<td align=center bgcolor='#cccccc'><font size=4>Nº actualizaciones:</font></td></tr>";

	$conex_o = odbc_connect('facturacion','','');

	//a cada persona le saco el nombre segun su codigo de nomina
	$color='#EOFFFF';
	$vfilacol='1';
	for($i=0;$i<count($perCod); $i++)
	{
		$query_o="select percco,perno1,perno2,perap1,perap2  from noper where percod='$perCod[$i]' and peretr='A'";
		$err_o=odbc_do($conex_o,$query_o);
		if(odbc_fetch_row($err_o))
		{
			//El usuario puede actualizar, se hace la actualización
			$perActualiza=$perCod[$i]."-".odbc_result($err_o,2)." ".odbc_result($err_o,3)." ".odbc_result($err_o,4)." ".odbc_result($err_o,5);

			// a cada uno le busco cuantas actualizaciones ha hecho y muestro el resultado
			$query ="SELECT Clidoc FROM magenta_000008 where Clipac='$perActualiza'";
			$result = mysql_query($query);
			$num=mysql_num_rows($result );

			if ($color=='#cccccc')
			$color='#EOFFFF';
			else
			$color='#cccccc';
            
            //Asignar color de fila
            if ($vfilacol =='1')
            {
				   $wcf="fila1";  
				   $vfilacol ='2' ;} 
			else{
				   $wcf="fila2";
				   $vfilacol ='1' ;}

			if($num>=0)
			{
				echo "<tr class='".$wcf."'><td align=rigth ><font size=3>$perActualiza</font></td>";
				echo "<td align=center ><font size=3>$num</font></td></tr>";
			}else
			{
				echo "<tr class='".$wcf."'><td align=center ><font size=3>$perCod[$i]</font></td>";
				echo "<td align=rigth ><font size=3>$perActualiza</font></td>";
				echo "<td align=center ><font size=3>0</font></td></tr>";
			}
		}
	}
	echo "</table></br>";	
	odbc_close($conex_o);
	odbc_close_all();
}
?>