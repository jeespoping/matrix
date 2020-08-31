<html>
<head>
<title>Reporte facturas por Entidad
</title>
</head>
<script type="text/javascript">
	function cerrarVentana()
	 {
      top.close();
     }
</script>

<body>
<?php
include_once("conex.php");
/*
//REPORTE DE FACTURAS POR ENTIDAD RESPONSABLE
// ===========================================================================================================================================
// PROGRAMA				      :Reporte de Facturas por Entidad Responsable                                             			 			 |
// AUTOR				      :Ing. Luis Haroldo Zapata Arismendy                                                                        	 |
// FECHA CREACION			  :Enero-2-2012                                                                                       			 |
// FECHA ULTIMA ACTUALIZACION :Enero-24-2012                                                                                     			 |
// DESCRIPCION			      :Reporte que muestra las facturas realizadas por paciente y agrupadas por la Entidad responsable
//							   												 |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// CLISUR_000018		 	  :Tabla que contiene los campos de facturacion solicitados como fecha, fuente, numero de factura
//							   y datos del paciente
// CLISUR_000024			  :Tabla que contiene el nit y el nombre del responsable
//                                                                                         													 |
// ==========================================================================================================================================
// 2014-01-13 (Camilozz) : Se modificó el programa para corregir la suma de los saldos teniendo en cuenta los recibos, adicionalmente se agrega
//                         una columna que presenta la información de la suma de los recibos asociados a la factura.
//========================================================================================================================================\\
// ==========================================================================================================================================
// 2013-12-17 (Camilozz) : se modificó el script para que tenga en cuenta tambien las notas débito que se le hayan hecho a las facturas.
//						   ademas se modificó el query para que agrupe por todos los campos ya que estaba agregando las facturas varias veces, haciendo
//						   así la suma de manera equivocada.
//========================================================================================================================================\\
//Modificacion: Febrero 20 de 2012
//========================================================================================================================================\\
//Se modifica la consulta que trae la informacion de las facturas por entidad, realizando 3 consultas iguales pero filtradas por diferentes parametros
//y unidas en la misma por empnit en la primera consulta y fendpa en las dos siguientes, esto permitira traer toda la informacion de las facturas.
//========================================================================================================================================\\
*/
$wactualiz = "2014-01-13";

//================================================================================
include_once("root/comun.php");




if(!isset($_SESSION['user']))
	exit("error session no abierta");


$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla))
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$institucion = consultarInstitucionPorcodigo($conex, $wemp_pmla);
$wbasedato = strtolower($institucion->baseDeDatos);

Encabezado("FACTURACION CON SALDO NETO", $wactualiz  ,"clinica");

if( !isset($mostrar) )
{
	$mostrar = 'off';
}

echo "<form action='rep_SaldoFacturasxEntidad.php?wemp_pmla=$wemp_pmla' method='post'>";

if( $mostrar == 'off' )				//si no hay rango de fechas entonces  pedirlos al usuario
{

	if( !isset( $fechafin ) )
	{
		$fechafin = date("Y-m-d");
	}

	if( !isset( $fechaini ) )
	{
		$fechaini = date("Y-m-01");
	}

	//El usuario selecciona el rango de fechas

	echo "<br><br><table align='center'>";
	echo "<tr class='encabezadotabla'>";
	echo "<td align='center' style='width:200'>Fecha inicial</td>";
	echo "<td align='center' style='width:200'>Fecha final</td>";
	echo "</tr><tr class='fila1'>";
	echo "<td align='center'>";
	campoFechaDefecto( "fechaini", $fechaini );
	echo "</td>";
	echo "<td align='center'>";
	campoFechaDefecto( "fechafin", "$fechafin" );
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	//Seleccionamos la Entidad o empresa responsable

	echo "<br><br><table align= center class = encabezadotabla>";
	echo "<tr align='center'>";
	echo "<td>Responsable</td>";
	echo "</tr>";
	echo "<tr align='center'>";
	echo "<td>";
	echo "<select name= 'wemp'>";

	$sql= " SELECT  empnit,empnom "				//De la tabla del maestro de empresas traemos el nit y el nombre
		 ."   FROM ".$wbasedato."_000024 "		//donde el codigo de la empresa sea igual al codigo de la empresa responsable
		 ."  WHERE empcod= empres "				//agrupamos por nit para mostrar un solo nit por empresa y ordenamos por nombre
		 ."  GROUP BY empnit "
		 ."  ORDER BY 2 ";

	$res=mysql_query($sql);

	$num= mysql_num_rows($res);
	$wemp='% - Todas las empresas';
	if ($num >0)
	{
		if ($wemp!='%-Todas las empresas')

			echo "<option>%-Todas las empresas</option>";
		for($i=1;$i<=$num;$i++)
		{
			$row=mysql_fetch_array($res);
			echo "<option> ".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	//Agregamos los botones de ver y cerrar
	echo "<br><table align='center'>";
	echo  "<tr>";
	echo  "<td align='center' width='150'><INPUT type='submit' value='Ver' style='width:100' name='btVer'></INPUT></td>";
	echo  "<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></INPUT></td>";
	echo  "</tr>";
	echo  "</table>";

	echo "<INPUT type='hidden' name='mostrar' value='on'>";
	echo"</form>";
}
	else
	{
		//informacion ingresada por el usuario
		echo "<br><table align='center'>";
		echo "<tr align='left'>";
		echo "<td width='150' class='fila1'>Fecha inicial</td>";
		echo "<td width='150' class='fila2'>$fechaini</td>";
		echo "</tr>";
		echo "<tr class='fila1' align='left'>";
		echo "<td class='fila1'>Fecha final</td>";
		echo "<td class='fila2'>$fechafin</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='fila1'>Empresa:</td>";
		echo "<td class='fila2'>$wemp</td>";
		echo "</tr>";
		echo "</table><br><br>";

		$wemp_str= explode("-",$wemp);
		$wemp=$wemp_str[0];

		//Boton para retornar a los campos de fechas o para cerrar el formulario
		echo "<br>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td align=center width='150'>";
		echo "<INPUT type='submit' value='Retornar' style='width:100'>";
		echo "</td>";
		echo "<td align= center width='150'>";
		echo "<INPUT type='button' value='Cerrar Ventana' onClick='javascript:cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<INPUT type='hidden' name='mostrar' value='off'>";
		echo "<INPUT type='hidden' name='fechaini' value='$fechaini'>";
		echo "<INPUT type='hidden' name='fechafin' value='$fechafin'>";

		//Esta consulta me trae las facturas por paciente ordenadas por el nit del responsable, se realizan varias uniones para permitir que se listen
                //todas las facturas por fennit en la primera, fendpa en la segunda y fenres en la tercera.

			 	/*$q = " SELECT * FROM (
                	SELECT    fenffa,fenfac,fenfec,fendpa,fennpa,(fenval+fenabo+fencmo+fencop+fendes) as valorfact,fennit,empnit,empnom,
                                fenvnc,fendes,(fenval+fenabo+fencmo+fencop-fenvnc+fenvnd) as neto, fenvnd
                        FROM    ".$wbasedato."_000018 A, ".$wbasedato."_000024 B
                       WHERE    A.fenfec BETWEEN '$fechaini'
                         AND    '$fechafin'
                         AND    A.fennit LIKE '".$wemp."'
                         AND    A.fencod = B.empcod
                         AND    B.empcod = B.empres
                         AND    (fenval + fenabo + fencmo + fencop + fendes) > 0
                         AND    A.fenest = 'on'
                   UNION ALL
                      SELECT    fenffa,fenfac,fenfec,fendpa,fennpa,(fenval+fenabo+fencmo+fencop+fendes) as valorfact,fennit,fendpa,empnom,
                                fenvnc,fendes,(fenval+fenabo+fencmo+fencop-fenvnc+fenvnd) as neto, fenvnd
                        FROM    ".$wbasedato."_000018 A, ".$wbasedato."_000024 B
                       WHERE    A.fenfec BETWEEN '$fechaini'
                         AND    '$fechafin'
                         AND    A.fendpa LIKE '".$wemp."'
                         AND    A.fencod = B.empcod
                         AND    B.empcod = B.empres
                         AND    (fenval + fenabo + fencmo + fencop + fendes) > 0
                         AND    A.fenest = 'on'
                   UNION ALL
                      SELECT    fenffa,fenfac,fenfec,fendpa,fennpa,(fenval+fenabo+fencmo+fencop+fendes) as valorfact,fennit,fendpa,empnom,
                                fenvnc,fendes,(fenval+fenabo+fencmo+fencop-fenvnc+fenvnd) as neto, fenvnd
                        FROM    ".$wbasedato."_000018 A, ".$wbasedato."_000024 B
                       WHERE    A.fenfec BETWEEN '$fechaini'
                         AND    '$fechafin'
                         AND    A.fenres LIKE '".$wemp."'
                         AND    A.fencod = B.empcod
                         AND    B.empcod = B.empres
                         AND    (fenval + fenabo + fencmo + fencop + fendes) > 0
                         AND    A.fenest = 'on' ) a
                    GROUP BY    fenffa,fenfac,fenfec,fendpa,fennpa,valorfact,fennit,fendpa,empnom,
                                fenvnc,fendes,neto, fenvnd
                    ORDER BY    fennit";

					*/

				$q = " SELECT * FROM (
                	SELECT    fenffa,fenfac,fenfec,fendpa,fennpa,fenval as valorfact,fennit,empnit,empnom,
                                fenvnc,fendes,(fenval-fenvnc+fenvnd-fenrbo) as neto, fenvnd, fenrbo
                        FROM    ".$wbasedato."_000018 A, ".$wbasedato."_000024 B
                       WHERE    A.fenfec BETWEEN '$fechaini'
                         AND    '$fechafin'
                         AND    A.fennit LIKE '".$wemp."'
                         AND    A.fencod = B.empcod
                         AND    B.empcod = B.empres
                         AND    (fenval + fenabo + fencmo + fencop + fendes) > 0
                         AND    A.fenest = 'on'
                   UNION ALL
                      SELECT    fenffa,fenfac,fenfec,fendpa,fennpa,fenval as valorfact,fennit,fendpa,empnom,
                                fenvnc,fendes,(fenval-fenvnc+fenvnd-fenrbo) as neto, fenvnd, fenrbo
                        FROM    ".$wbasedato."_000018 A, ".$wbasedato."_000024 B
                       WHERE    A.fenfec BETWEEN '$fechaini'
                         AND    '$fechafin'
                         AND    A.fendpa LIKE '".$wemp."'
                         AND    A.fencod = B.empcod
                         AND    B.empcod = B.empres
                         AND    (fenval + fenabo + fencmo + fencop + fendes) > 0
                         AND    A.fenest = 'on'
                   UNION ALL
                      SELECT    fenffa,fenfac,fenfec,fendpa,fennpa,fenval as valorfact,fennit,fendpa,empnom,
                                fenvnc,fendes,(fenval-fenvnc+fenvnd-fenrbo) as neto, fenvnd, fenrbo
                        FROM    ".$wbasedato."_000018 A, ".$wbasedato."_000024 B
                       WHERE    A.fenfec BETWEEN '$fechaini'
                         AND    '$fechafin'
                         AND    A.fenres LIKE '".$wemp."'
                         AND    A.fencod = B.empcod
                         AND    B.empcod = B.empres
                         AND    (fenval + fenabo + fencmo + fencop + fendes) > 0
                         AND    A.fenest = 'on' ) a
                    GROUP BY    fenffa,fenfac,fenfec,fendpa,fennpa,valorfact,fennit,fendpa,empnom,
                                fenvnc,fendes,neto, fenvnd, fenrbo
                    ORDER BY    fennit";
		$res=mysql_query($q,$conex) or die ("ERROR EN QUERY $q - ".mysql_error());
		$num=mysql_num_rows($res);

                //return false;

		$row= mysql_fetch_array($res);
		$wgrantotalfac  =0;
		$wgrantotalnc   =0;
		$wgrantotalnd   =0;
		$wgrantotaldesc =0;
		$wgrantotalneto =0;
		for($i=0;$i<$num; )
		{
			$wtotalfac=0;
			$wtotalnc=0;
            $wtotalnd=0;
			$wtotalrbo=0;
			$wtotaldesc=0;
			$wtotalneto=0;

			$wfennit=$row['fennit'];
			$wempnom=$row['empnom'];

			//Encabezados de la tabla
			echo "<table style='width: 100%;' align=center >";
			echo "<tr><td align='left' colspan='11' class='titulo'>" .$wfennit." - " .$wempnom. "</td></tr>";
			echo "<br>";
			echo "<tr class='encabezadoTabla'>";
			echo "<td align=center>&nbsp;Fuente<br>Factura&nbsp;</td>";
			echo "<td align=center>&nbsp;Nro<br>Factura&nbsp;</td>";
			echo "<td align=center>&nbsp;Fecha <br>Factura&nbsp</td>";
			echo "<td align=center>&nbsp;Identificacion <br> Paciente&nbsp;</td>";
			echo "<td align=center >&nbsp;Nombre paciente&nbsp;</td>";
			echo "<td align=center >&nbsp;Vlr Factura&nbsp;</td>";
			echo "<td align=center >&nbsp;Vlr <br> Nota Credito&nbsp;</td>";
            echo "<td align=center >&nbsp;Vlr <br> Nota D&eacute;bito&nbsp;</td>";
			echo "<td align=center >&nbsp;Vlr <br>Recibos</td>";
			echo "<td align=center >&nbsp;Descuento&nbsp;</td>";
			echo "<td align=center 	>&nbsp;Saldo <br>Factura&nbsp;</td>";
			echo "</tr>";

			while($wfennit==$row['fennit']) // mientras sea el mismo nit, que me traiga la informacion de los pacientes
                        {								// que posee ese nit

			if (is_int ($i/2))
			  {
			   $wclass="fila1"; 					 // color de fondo= sentencia para colocar color a los datos
			  }
			 else
			  {
			   $wclass="fila2";
			  }
			  $wfenffa=$row['fenffa'];				//Declaro las variables que voy a mostrar en el informe
			  $wfenfac=$row['fenfac'];
			  $wfenfec=$row['fenfec'];
			  $wfendpa=$row['fendpa'];
			  $wfennpa=$row['fennpa'];
			  $wvalorfact=$row['valorfact'];
			  $wfenvnc=$row['fenvnc'];
              $wfenvnd=$row['fenvnd'];
			  $wfenrbo=$row['fenrbo'];
			  $wfendes=$row['fendes'];
			  $wneto=$row['neto'];

			  //Muestro los datos seleccionados por el usuario

			echo "<tr class=".$wclass.">";
			echo "<td align=center> $wfenffa </td>";				//Muestro la informacion por factura y por paciente
			echo "<td align=center> $wfenfac </td>";
			echo "<td align=center> $wfenfec </td>";
			echo "<td align=right>  $wfendpa</td>";
			echo "<td align=left>   $wfennpa </td>";
			echo "<td align=right>".number_format($wvalorfact). "</td>";
			echo "<td align=right>".number_format($wfenvnc)."</td>";
            echo "<td align=right>".number_format($wfenvnd)."</td>";
			echo "<td align=right>".number_format($wfenrbo)."</td>";
			echo "<td align=right>".number_format($wfendes)." </td>";
			echo "<td align=right>".number_format($wneto)." </td>";
			echo "</tr>";

			$wtotalfac=$wtotalfac+$row['valorfact'];				//Estas son los variables para totalizar por Entidad
			$wtotalnc=$wtotalnc+$row['fenvnc'];
            $wtotalnd=$wtotalnd+$row['fenvnd'];
			$wtotalrbo=$wtotalrbo+$row['fenrbo'];
			$wtotaldesc=$wtotaldesc+$row['fendes'];
			$wtotalneto=$wtotalfac-$wtotalnc-$wtotaldesc+$wtotalnd-$wtotalrbo;

			$row= mysql_fetch_array($res);
			$i++;
                        }
		  echo "<tr class='encabezadoTabla'>";
		  echo"<td align=center colspan=5>Total</td><br>";
		  echo "<td align=right><font size=2><strong>" .number_format($wtotalfac)." </strong></font></td>";
		  echo "<td align=right><font size=2><strong>" .number_format($wtotalnc)." </strong></font></td>";
          echo "<td align=right><font size=2><strong>" .number_format($wtotalnd)." </strong></font></td>";
		  echo "<td align=right><font size=2><strong>" .number_format($wtotalrbo)." </strong></font></td>";
		  echo "<td align=right><font size=2><strong>" .number_format($wtotaldesc)." </strong></font></td>";
		  echo "<td align=right><font size=2><strong>" .number_format($wtotalneto)." </strong></font></td>";

		  $wgrantotalfac=$wgrantotalfac + $wtotalfac;
		  $wgrantotalnc=$wgrantotalnc+$wtotalnc;
          $wgrantotalnd=$wgrantotalnd+$wtotalnd;
		  $wgrantotalrbo=$wgrantotalrbo+$wtotalrbo;
		  $wgrantotaldesc=$wgrantotaldesc+$wtotaldesc;
		  $wgrantotalneto=$wgrantotalneto+$wtotalneto;

		}
		if($wgrantotalfac!=0)
		{
			echo "<tr class='encabezadotabla'>";
			echo "<td align=center colspan=5>Total Empresas</td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotalfac)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotalnc)." </strong></font></td>";
            echo "<td align=right><font size=2><strong>" .number_format($wgrantotalnd)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotalrbo)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotaldesc)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotalneto)." </strong></font></td>";
			echo "</table>";

		}
		else
		{
			echo "<center><b>No se encontraron resultados</b></center>";
		}
		//Botones de retornar y cerrar nuevamente para que queden tambien al final y facilitar el retorno de datos para el usuario
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td align=center width='150'>";
		echo "<INPUT type='submit' value='Retornar' style='width:100'>";
		echo "</td>";
		echo "<td align= center width='150'>";
		echo "<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<INPUT type='hidden' name='mostrar' value='off'>";
		echo "<INPUT type='hidden' name='fechaini' value='$fechaini'>";
		echo "<INPUT type='hidden' name='fechafin' value='$fechafin'>";
}

?>
</body>
</html>
