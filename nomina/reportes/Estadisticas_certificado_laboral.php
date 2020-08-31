<?php
include_once("conex.php");
/*
PROGRAMA                    : Estadisticas_certificado_laboral.php.
AUTOR                       : --
FECHA CREACION              : .
DESCRIPCION                 : --

Actualizaciones:
* 27 Enero 2014 : Juna C. :
  Se adiciona al reporte el campo idecec que indica si las cesantias estan congeladas o no y además con la posilidad de actualizar este
  dato desde este reporte. (este campo esta en la tabla "talhuma"_000013
   
* 15 Agosto 2013   : * Se modifica group by ( LEFT(Ideuse, 5) ) para consultar los certificados generados por los empleados, con el fin de que los registros para un usuario
                       que aparezca con cinco o seis dígitos en su código, entonces los sume y solo aparezca un resultado en el reporte por cada usuario.
                     * En la operación "muestradetalle" se recorta código del usuario a cinco dígitos.
                     * Si existe y llega por parámetro la variable "$wcodigo" se recorta a cinco digitos.
*/
header("Content-Type: text/html;charset=ISO-8859-1");


include_once("root/comun.php");



global $wemp_pmla;

$fecha= date("Y-m-d");
$hora = date("H:i:s");

global $wnomina ;
global $wcostos ;
global $wtalhuma ;

 $wnomina = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nomina');
 $wcostos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'costoscer');
 $wtalhuma = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

if(isset($wcodigo))
{
	$wcodigo = ( strlen($wcodigo) > 5) ? substr($wcodigo,-5): $wcodigo;
}
else
{
	$wcodigo = '';
}

if(isset($woperacion) && $woperacion=='puedegenerarcertificado')
{
	$q= "UPDATE ".$wtalhuma."_000013 SET Idecer='".$westado."'  WHERE Ideuse='".$wcodigoempleado."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	return;
}

if(isset($woperacion) && $woperacion=='cesantiascongeladas')
{
	$q= "UPDATE ".$wtalhuma."_000013 SET Idecec='".$westado."'  WHERE Ideuse='".$wcodigoempleado."'";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	return;
}

if (isset($woperacion) && $woperacion=='muestrareporte')
{
	if($wcostos=='costosyp_000005')
	{ $campo="Cconom"; }
	else
	{ $campo="Ccodes"; }

	echo "<br><br><br>";
	$q = " 	SELECT 	Ceruse,Ideno1,Ideno2,Ideap1,Ideap2,".$campo.",Cardes,COUNT(Ceruse) as Cantidad,Idecer,Ideuse,Ccocod,Carcod,Codigo,Motivo,Cerexp, Idecec
			FROM   	".$wnomina."_000008, ".$wtalhuma."_000013, ".$wcostos." ,root_000079,".$wnomina."_000001
			WHERE  	LEFT(Ideuse, 5) = RIGHT(Ceruse,5)
					AND  Ccocod = Idecco
					AND  Codigo = Cermot
					AND  Carcod = Ideccg ";

	if($wcodigo!='' )
	{ $q .= "
			AND RIGHT(Ceruse,5) = '".$wcodigo."'  "; }

	if ($wcco !='seleccione')
	{ $q .= "
			AND Idecco = '".$wcco."'"; }

	if($wcargo !='seleccione')
	{ $q .= "
			AND Ideccg = '".$wcargo."'"; }

	if($wfechainicial!='' && $wfechafinal!='')
	{ $q .= "
			AND ".$wnomina."_000008.Fecha_data BETWEEN  '".$wfechainicial."' AND '".$wfechafinal."' "; }

	if($wmot!='seleccione')
	{ $q.= "
			AND Codigo='".$wmot."'"; }

	$qxcco = 	$q;
	$qxcargo =  $q;
	$qxmot =	$q;

	$q .= "
			GROUP BY LEFT(Ideuse, 5)
			ORDER BY Cantidad DESC, Motivo, Cerexp";

	$qxcco .= "
			GROUP BY Ccocod
			ORDER BY Cantidad DESC, Motivo, Cerexp";

	$qxcargo .= "
			GROUP BY Carcod
			ORDER BY Cantidad DESC, Motivo, Cerexp";

	$qxmot .= "
			GROUP BY Codigo,Cerexp
			ORDER BY Cantidad DESC, Motivo, Cerexp";

	// echo "<pre>"; print_r($q); echo "</pre>";
	$res = mysql_query($q,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$cuantos = mysql_num_rows($res);

	$resqxcco = mysql_query($qxcco,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$qxcco." - ".mysql_error());
	$resqxcargo= mysql_query($qxcargo,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$qxcargo." - ".mysql_error());
	$resqxmot= mysql_query($qxmot,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$qxmot." - ".mysql_error());

	if ($cuantos ==0)
	{
		echo "<br><br>";
		echo "<table align='center'><tr><td>No Hay Datos Para Mostrar</td></tr></table>";
	}
	else
	{
		echo "<br><br>";
		echo "<table align='center'><tr><td style='font-size : 12pt  '><b>CENTRO DE COSTOS</b></td></tr></table>";
		echo "<table id='table_resultadosxcco' align='center' width='900'>";
		echo "<tr class='encabezadoTabla'><td>Centro de Costos</td><td  width='50px'>Cantidad</td></tr>";
		$cantidadcco = 0;
		$k=0;
		while($rowcco= mysql_fetch_array($resqxcco))
		{
			if (is_int ($k/2))
		   {
			$wcf="fila1";  // color de fondo de la fila
		   }
		else
		   {
			$wcf="fila2"; // color de fondo de la fila
		   }

			$k++;
			echo "<tr id='trcco-".$rowcco['Ccocod']."' class='".$wcf."' >";
			echo "<td  style='cursor : pointer' onclick='muestradetallecco( \"".$rowcco['Ccocod']."\" )' >".$rowcco[$campo]."</td><td onclick='muestradetallecco( \"".$rowcco['Ccocod']."\" )' align='center' style='cursor : pointer' width='50px'>".$rowcco['Cantidad']."</td>";
			echo "</tr>";
			$cantidadcco = $cantidadcco + $rowcco['Cantidad'];

		}
		echo "<tr class='encabezadoTabla'><td>Total</td><td align='center' width='50px'>".$cantidadcco."</td></tr>";
		echo "</table>";

		echo "<br><br>";
		echo "<table align='center'><tr><td style='font-size : 12pt  '><b>CARGO</b></td></tr></table>";
		echo "<table id='table_resultadosxcargo' align='center' width='900'>";
		echo "<tr class='encabezadoTabla'><td>Cargo</td><td  width='50px'>Cantidad</td></tr>";
		$cantidadcargo = 0;
		$k=0;
		while($rowcargo= mysql_fetch_array($resqxcargo))
		{
			if (is_int ($k/2))
			   {
				$wcf="fila1";  // color de fondo de la fila
			   }
			else
			   {
				$wcf="fila2"; // color de fondo de la fila
			   }

				$k++;
				echo "<tr id='trcargo-".$rowcargo['Carcod']."' class='".$wcf."' >";
				echo "<td  style='cursor : pointer' onclick='muestradetallemotivo( \"".$rowcargo['Carcod']."\" )' >".$rowcargo['Cardes']."</td><td onclick='muestradetallemotivo( \"".$rowcargo['Carcod']."\" )' align='center' style='cursor : pointer'>".$rowcargo['Cantidad']."</td>";
				echo "</tr>";
				$cantidadcargo = $cantidadcargo + $rowcargo['Cantidad'];
		}
		echo "<tr class='encabezadoTabla'><td  >Total</td><td align='center'>".$cantidadcargo."</td></tr>";
		echo "</table>";


		echo "<br><br>";
		echo "<table align='center'><tr><td style='font-size : 12pt  '><b>MOTIVO</b></td></tr></table>";
		echo "<table id='table_resultadosxcco' align='center' width='900'>";
		echo "<tr class='encabezadoTabla'><td>Motivo</td><td  width='50px'>Cantidad</td></tr>";
		$cantidadcco = 0;
		$k=0;
		while($rowmot= mysql_fetch_array($resqxmot))
		{
			if (is_int ($k/2))
		   {
			$wcf="fila1";  // color de fondo de la fila
		   }
			else
		   {
			$wcf="fila2"; // color de fondo de la fila
		   }

			$k++;
			if($rowmot['Cerexp']!="")
			{
				$mmmtexto = $rowmot['Motivo']." (".$rowmot['Cerexp'].")";
			}
			else
			{
				$mmmtexto =  $rowmot['Motivo'];
			}

			echo "<tr id='trmot-".$rowmot['Codigo']."-".str_replace(" ","_",$rowmot['Cerexp'])."' class='".$wcf."' >";
			echo "<td style='cursor : pointer' onclick='muestradetallemotivoempleado( \"".$rowmot['Codigo']."\",\"".$rowmot['Cerexp']."\" )' >".$mmmtexto."</td><td onclick='muestradetallemotivoempleado( \"".$rowmot['Codigo']."\",\"".$rowmot['Cerexp']."\" )' align='center' style='cursor : pointer'>".$rowmot['Cantidad']."</td>";
			echo "</tr>";
			$cantidadcco = $cantidadcco + $rowmot['Cantidad'];
			$mmmtexto ="";
		}
		echo "<tr class='encabezadoTabla'><td>Total</td><td align='center'>".$cantidadcco."</td></tr>";
		echo "</table>";

		echo "<br><br>";
		echo "<table align='center'><tr><td style='font-size : 12pt  '><b>EMPLEADO</b></td></tr></table>";
		echo "<table id='table_resultados' align='center'>";
		echo "<tr class='encabezadoTabla'><td>Codigo</td><td>Nombre</td><td>Centro de costos</td><td>Cargo</td><td>Cantidad</td><td>Permiso</td><td>Cesantias<br>Congeladas</td></tr>";
		$k=0;
		while($row = mysql_fetch_array($res))
		{
				if (is_int ($k/2))
			   {
				$wcf="fila1";  // color de fondo de la fila
			   }
			else
			   {
				$wcf="fila2"; // color de fondo de la fila
			   }

			$k++;
			echo "<tr id='tr-".$row['Ceruse']."' class='".$wcf."' >";
			echo "<td onclick='muestradetalle( \"".$row['Ceruse']."\" )' style='cursor : pointer' >".substr($row['Ceruse'],-5)."</td>";
			echo "<td onclick='muestradetalle( \"".$row['Ceruse']."\" )' style='cursor : pointer'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";
			echo "<td onclick='muestradetalle( \"".$row['Ceruse']."\" )' style='cursor : pointer'>".$row[$campo]."</td>";
			echo "<td onclick='muestradetalle( \"".$row['Ceruse']."\" )' style='cursor : pointer'>".$row['Cardes']."</td>";
			echo "<td align='center' onclick='muestradetalle( \"".$row['Ceruse']."\" )' style='cursor : pointer'>".$row['Cantidad']."</td>";
			if ($row['Idecer']=='on')
				echo"<td align='center'><input type='checkbox' value='ok' id='certificado-".$row['Ideuse']."' onclick='puedegenerarcertificado(\"".$row['Ideuse']."\")'  checked></td>";
			else
				echo "<td align='center'><input type='checkbox' value='ok' id='certificado-".$row['Ideuse']."' onclick='puedegenerarcertificado(\"".$row['Ideuse']."\")' ></td>";
			
			if ($row['Idecec']=='on')
				echo"<td align='center'><input type='checkbox' value='ok' id='cesantias-".$row['Ideuse']."' onclick='cesantiascongeladas(\"".$row['Ideuse']."\")' checked></td>";
			else
				echo "<td align='center'><input type='checkbox' value='ok' id='cesantias-".$row['Ideuse']."' onclick='cesantiascongeladas(\"".$row['Ideuse']."\")' ></td>";

			echo "</tr>";
		}
		echo "</table>";
		echo "<br><br><br>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td align='center'><input type='button' value='Cerrar ventana' onclick='javascript:window.close();'></td>";
		echo "<tr>";
		echo "</table>";
	}

	// $wfechainicial;
	// $wfenafinal;
	// $wcargo;
	// $wcodigo;
	// $wcco;
	return;
}

if(isset($woperacion) && $woperacion=='muestradetallemotivo'){

 if($wcostos=='costosyp_000005')
	   $campo="Cconom";
  else
	   $campo="Ccodes";


$q = " SELECT Ceruse,Ideno1,Ideno2,Ideap1,Ideap2,".$campo.",Cardes,COUNT(Ceruse) as Cantidad,Idecer,Ideuse,Ccocod ,Carcod,Cermot,Motivo,Cerexp, Idecec "
	."   FROM   ".$wnomina."_000008, ".$wtalhuma."_000013, ".$wcostos." ,root_000079,".$wnomina."_000001"
	."  WHERE  LEFT(Ideuse, 5) = RIGHT(Ceruse,5)"
	."    AND  Ccocod = Idecco"
	."    AND  Carcod = Ideccg "
	."	  AND  Codigo = Cermot "
	."    AND Carcod = '".$wcargo."' ";

if($wfechainicial!='' && $wfechafinal!='')
	$q .= " AND ".$wnomina."_000008.Fecha_data BETWEEN  '".$wfechainicial."' AND '".$wfechafinal."' ";

if ($wcodigo!='')
{
	$q.= " AND RIGHT(Ceruse,5)='".$wcodigo."'";
}

if($wmot!='seleccione')
{
	$q.= " AND Codigo='".$wmot."'";
}

$q .= "Group by Cermot,Cerexp ";
$q .= " ORDER by Cantidad DESC,Motivo,Cerexp";
$res = mysql_query($q,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo "<tr class = 'xxxcargo-".$wcargo."'><td><div class='encabezadoTabla' style='margin-left:10%; background-color : #cccccc;'>Motivo</td></div><td class='encabezadoTabla' align='center' style='background-color : #cccccc'  width='50px'>Cantidad</td></tr>";
$cantidadmot=0;
$k=0;
while($row = mysql_fetch_array($res))
{
	if (is_int ($k/2))
   {
	$wcf="fila1";  // color de fondo de la fila
   }
	else
   {
	$wcf="fila2"; // color de fondo de la fila
   }
	$k++;
	if($row['Cerexp']!='')
	{
		$mmtexto = $row['Motivo']." (".$row['Cerexp'].")";
	}
	else
	{
	 $mmtexto = $row['Motivo'];
	}

	echo "<tr style='cursor : pointer' id='trcargo-".$wcargo."-".$row['Cermot']."-".str_replace(" ","_",$row['Cerexp'])."' class = 'xxxcargo-".$wcargo."'><td  style='cursor : pointer'  onclick='muestradetalleempleadocargo( \"".$row['Carcod']."\" ,\"".$row['Cermot']."\",\"".$row['Cerexp']."\")' ><div class='".$wcf."' style='margin-left:10%; '>".$mmtexto."</div></td><td class='".$wcf."' onclick='muestradetalleempleadocargo( \"".$row['Carcod']."\" ,\"".$row['Cermot']."\",\"".$row['Cerexp']."\")' align='center'>".$row['Cantidad']."</td></tr>";
	$cantidadmot = $cantidadmot +  $row['Cantidad'];
	$mmtexto ="";
}
echo "<tr class = 'xxxcargo-".$wcargo."'><td ><div class='encabezadoTabla' style='margin-left:10%; background-color : #cccccc;' >TOTAL</div></td><td class='encabezadoTabla' style='background-color : #cccccc' align='center'>".$cantidadmot."</td></tr>";


return;
}

if(isset($woperacion) && $woperacion=='muestradetallecargo'){

 if($wcostos=='costosyp_000005')
	   $campo="Cconom";
  else
	   $campo="Ccodes";

$q = " SELECT Ceruse,Ideno1,Ideno2,Ideap1,Ideap2,".$campo.",Cardes,COUNT(Ceruse) as Cantidad,Idecer,Ideuse,Ccocod ,Carcod,Cermot,Motivo,Cerexp, Idecec "
	."   FROM   ".$wnomina."_000008, ".$wtalhuma."_000013, ".$wcostos.",root_000079,".$wnomina."_000001"
	."  WHERE  LEFT(Ideuse, 5) = RIGHT(Ceruse,5)"
	."    AND  Ccocod = Idecco"
	."    AND  Carcod = Ideccg "
	."    AND  idecco = '".$wcco."' "
	."	  AND  Codigo = Cermot "
	."    AND Carcod = '".$wcargo."' ";

if($wfechainicial!='' && $wfechafinal!='')
	$q .= " AND ".$wnomina."_000008.Fecha_data BETWEEN  '".$wfechainicial."' AND '".$wfechafinal."' ";

if ($wcodigo!='')
{
	$q.= " AND Ceruse='".$wcodigo."'";
}
if($wmot!='seleccione')
{
	$q.= " AND Codigo='".$wmot."'";
}


$q .= "Group by Cermot,Cerexp ";
$q .= " ORDER by Cantidad DESC,Motivo,Cerexp ";
$res = mysql_query($q,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$q." - ".mysql_error());


echo "<tr class = 'xxxcco-".$wcco." xxxcco-".$wcco."-".$wcargo."'><td><div class='encabezadoTabla' style='margin-left:20%; background-color: #cccccc;'>Motivo</div></td><td class='encabezadoTabla' align='center' style='background-color : #cccccc' width='50px'>Cantidad</td></tr>";
$cantidadmot=0;
$k=0;
while($row = mysql_fetch_array($res))
{
	if (is_int ($k/2))
	   {
		$wcf="fila1";  // color de fondo de la fila
	   }
	else
	   {
		$wcf="fila2"; // color de fondo de la fila
	   }
	$k++;
	if ($row['Cerexp']!='')
	{
	$mtexto =$row['Motivo']." (".$row['Cerexp'].")";
	}
	else
	$mtexto = $row['Motivo'];
	echo "<tr style='cursor : pointer' id='trcco-".$wcco."-".$row['Carcod']."-".$row['Cermot']."-".str_replace(" ","_",$row['Cerexp'])."' class = 'xxxcco-".$wcco." xxxcco-".$wcco."-".$row['Carcod']." xxxcco-".$wcco."-".$row['Carcod']."-".$row['Cermot']."-".str_replace(" ","_",$row['Cerexp'])."'><td style='cursor : pointer' onclick='muestradetalleempleado( \"".$row['Carcod']."\" ,\"".$wcco."\",\"".$row['Cermot']."\",\"".$row['Cerexp']."\")' ><div class='".$wcf."' style='margin-left:20%;'>".$mtexto."</div></td><td class='".$wcf."' onclick='muestradetalleempleado( \"".$row['Carcod']."\" ,\"".$wcco."\",\"".$row['Cermot']."\",\"".$row['Cerexp']."\")' align='center' width='50px'>".$row['Cantidad']."</td></tr>";
	$cantidadmot = $cantidadmot +  $row['Cantidad'];
	$mtexto = '';
}
echo "<tr class = 'xxxcco-".$wcco." xxxcco-".$wcco."-".$wcargo."'><td><div class='encabezadoTabla' style='margin-left:20%; background-color : #cccccc'>TOTAL</div></td><td class='encabezadoTabla' align='center'  style='background-color : #cccccc' width='50px'>".$cantidadmot."</td></tr>";



return;

}


if(isset($woperacion) && $woperacion=='muestradetallecco'){

 if($wcostos=='costosyp_000005')
	   $campo="Cconom";
  else
	   $campo="Ccodes";

$q = " SELECT Ceruse,Ideno1,Ideno2,Ideap1,Ideap2,".$campo.",Cardes,COUNT(Ceruse) as Cantidad,Idecer,Ideuse,Ccocod ,Carcod, Idecec "
	."   FROM   ".$wnomina."_000008, ".$wtalhuma."_000013, ".$wcostos.",root_000079,".$wnomina."_000001"
	."  WHERE  LEFT(Ideuse, 5) = RIGHT(Ceruse,5)"
	."    AND  Ccocod = Idecco"
	."    AND  Carcod = Ideccg "
	."    AND  Codigo = Cermot "
	."    AND  Idecco = '".$wccoseleccionado."' ";

if($wfechainicial!='' && $wfechafinal!='')
	$q .= " AND ".$wnomina."_000008.Fecha_data BETWEEN  '".$wfechainicial."' AND '".$wfechafinal."' ";

if ($wcodigo!='')
{
	$q.= " AND Ceruse='".$wcodigo."'";
}

if($wcargo!='seleccione')
{
	$q.= " AND Carcod='".$wcargo."'";
}

if($wmot!='seleccione')
{
	$q.= " AND Codigo='".$wmot."'";
}

$q .= "Group by Carcod ";
$q .= " ORDER by Cantidad DESC ";
$res = mysql_query($q,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$q." - ".mysql_error());


echo "<tr class = 'xxxcco-".$wccoseleccionado."'><td><div class='encabezadoTabla' style='margin-left:10%; background-color: #999999;'>Cargo</div></td><td class='encabezadoTabla'  style='background-color: #999999' width='50px'>Cantidad</td></tr>";
$cantidadcargo = 0;
$k =0;
while($row = mysql_fetch_array($res))
{
	if (is_int ($k/2))
	   {
		$wcf="fila1";  // color de fondo de la fila
	   }
	else
	   {
		$wcf="fila2"; // color de fondo de la fila
	   }
	$k++;
	echo "<tr  id='trcco-".$wccoseleccionado."-".$row['Carcod']."' class = 'xxxcco-".$wccoseleccionado." '><td  style='cursor : pointer' onclick='muestradetallecargo( \"".$row['Carcod']."\" ,\"".$wccoseleccionado."\")' ><div class='".$wcf."' style='margin-left:10%;'>".$row['Cardes']."</div></td><td align='center' class='".$wcf."' style='cursor : pointer' onclick='muestradetallecargo( \"".$row['Carcod']."\" ,\"".$wccoseleccionado."\")' width='50px' >".$row['Cantidad']."</td></tr>";
	$cantidadcargo = $cantidadcargo + $row['Cantidad'] ;

}
echo "<tr class = 'xxxcco-".$wccoseleccionado."'><td  ><div  class='encabezadoTabla'  style='margin-left:10%; background-color: #999999;'>Total</div></td><td class='encabezadoTabla' align='center' style='background-color: #999999' width='50px'>".$cantidadcargo."</td></tr>";

return;

}
if (isset($woperacion) && $woperacion=='muestradetalle')
{
$wcodigoesp_aux = ( strlen($wcodigoesp) > 5) ? substr($wcodigoesp,-5): $wcodigoesp;
$q= " SELECT ".$wnomina."_000008.Fecha_data,Motivo,Cerexp"
	."  FROM  ".$wnomina."_000008, ".$wnomina."_000001 "
	." WHERE  RIGHT(Ceruse,5) = '".$wcodigoesp_aux."' "
	."   AND  Cermot =  Codigo ";

if($wfechainicial!='' && $wfechafinal!='')
	$q .= " AND ".$wnomina."_000008.Fecha_data BETWEEN  '".$wfechainicial."' AND '".$wfechafinal."' ";

if (isset($wcodigo) && $wcodigo!='')
{
	$q.= " AND Ceruse='".$wcodigo."'";
}

if($wmot!='seleccione')
{
	$q.= " AND Codigo='".$wmot."'";
}

$q .= "Order BY Codigo,nomina_000008.Fecha_data ";
echo "<tr class = 'xxx-".$wcodigoesp."' >";
echo "<td></td><td colspan='4' class='encabezadoTabla'  style='background-color : #cccccc;' >Motivo</td><td class='encabezadoTabla'  style='background-color : #cccccc;'>Fecha</td>";
echo "</tr>";

$res = mysql_query($q,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$k=0;
while($row = mysql_fetch_array($res))
{
	if (is_int ($k/2))
   {
	$wcf="fila1";  // color de fondo de la fila
   }
	else
   {
	$wcf="fila2"; // color de fondo de la fila
   }
   $k++;
   if($row['Cerexp']!="")
   {
	$pmotivo = $row['Motivo']." (".$row['Cerexp'].")";
   }else
   $pmotivo = $row['Motivo'];
   echo "<tr class = 'xxx-".$wcodigoesp."'><td></td><td colspan='4' class='".$wcf."'>". $pmotivo."</td><td class='".$wcf."'>".$row['Fecha_data']."<td></tr>";
   $pmotivo = "";

}
return;
}

if(isset($woperacion) && $woperacion=='muestradetallemotivoempleado')
{

  if($wcostos=='costosyp_000005')
	   $campo="Cconom";
  else
	   $campo="Ccodes";


$wexp = utf8_decode($wexp);
$q = " SELECT Ceruse,Ideno1,Ideno2,Ideap1,Ideap2,".$campo.",Cardes,COUNT(Ceruse) as Cantidad,Idecer,Ideuse,Ccocod ,Carcod, Idecec "
	."   FROM   ".$wnomina."_000008, ".$wtalhuma."_000013, ".$wcostos.",root_000079"
	."  WHERE  LEFT(Ideuse, 5) = RIGHT(Ceruse,5)"
	."    AND  Ccocod = Idecco"
	."    AND  Carcod = Ideccg "
	."    AND  Cermot = '".$wmotivo."'"
	."    AND  Cerexp = '".$wexp."'";

if($wfechainicial!='' && $wfechafinal!='')
	$q .= " AND ".$wnomina."_000008.Fecha_data BETWEEN  '".$wfechainicial."' AND '".$wfechafinal."' ";

if($wcodigo!='')
{
$q .=" AND  Ceruse = '".$wcodigo."' ";
}

if($wcargo!='seleccione')
{
$q .=" AND  Carcod = '".$wcargo."' ";
}

if($wcargo!='seleccione')
{
$q .=" AND  Carcod = '".$wcargo."' ";
}
if($wcco!='seleccione')
{
$q .=" AND  Ccocod = '".$wcco."' ";
}

$q .= "Group by Ceruse ";
$q .= " ORDER by Cantidad DESC,Ideno1,Ideno2,Ideap1,Ideap2 ";
$res = mysql_query($q,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo "<tr class = 'xxxmot-".$wmotivo."-".str_replace(" ","_",$wexp)."'><td ><div  class='encabezadoTabla' style='margin-left:10%; background-color : #cccccc' >Nombre</div></td><td class='encabezadoTabla' width='50px' style='background-color : #cccccc'>Cantidad</td></tr>";
$k=0;
while($row = mysql_fetch_array($res))
{
	if (is_int ($k/2))
	   {
		$wcf="fila1";  // color de fondo de la fila
	   }
	else
	   {
		$wcf="fila2"; // color de fondo de la fila
	   }
	$k++;
	echo "<tr   class = 'xxxmot-".$wmotivo."-".str_replace(" ","_",$wexp)."'><td  style='cursor : pointer' ><div  class='".$wcf."' class='encabezadoTabla' style='margin-left:10%' >".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</div></td><td class='".$wcf."' style='cursor : pointer' align='center' >".$row['Cantidad']."</td></tr>";
}

return;
}

if(isset($woperacion) && $woperacion=='muestradetalleempleadocargo')
{

$wexp = utf8_decode($wexp);

 if($wcostos=='costosyp_000005')
	   $campo="Cconom";
  else
	   $campo="Ccodes";

$q = " SELECT Ceruse,Ideno1,Ideno2,Ideap1,Ideap2,".$campo.",Cardes,COUNT(Ceruse) as Cantidad,Idecer,Ideuse,Ccocod ,Carcod, Idecec "
	."   FROM   ".$wnomina."_000008, ".$wtalhuma."_000013, ".$wcostos.",root_000079"
	."  WHERE  LEFT(Ideuse, 5) = RIGHT(Ceruse,5)"
	."    AND  Ccocod = Idecco"
	."    AND  Carcod = Ideccg "
	."    AND  Cermot = '".$wmotivo."'"
	."    AND Carcod = '".$wcargo."' "
	."    AND Cerexp = '".$wexp."' ";


if($wfechainicial!='' && $wfechafinal!='')
	$q .= " AND ".$wnomina."_000008.Fecha_data BETWEEN  '".$wfechainicial."' AND '".$wfechafinal."' ";

$q .= "Group by Ceruse ";
$q .= " ORDER by Cantidad DESC,Ideno1,Ideno2,Ideap1,Ideap2 ";
$res = mysql_query($q,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo "<tr class = 'xxxcargo-".$wcargo."-".$wmotivo."-".str_replace(" ","_",$wexp)." xxxcargo-".$wcargo."'><td   ><div class='encabezadoTabla' style='margin-left:20%; background-color: #999999'>Nombre</td><td class='encabezadoTabla' style='background-color: #999999' width='50px'>Cantidad</td></tr>";
$k=0;
while($row = mysql_fetch_array($res))
{

	if (is_int ($k/2))
	   {
		$wcf="fila1";  // color de fondo de la fila
	   }
	else
	   {
		$wcf="fila2"; // color de fondo de la fila
	   }
	$k++;
	echo "<tr   class = 'xxxcargo-".$wcargo."-".$wmotivo."-".str_replace(" ","_",$wexp)." xxxcargo-".$wcargo."'><td style='cursor : pointer' ><div class='".$wcf."' style='margin-left:20%;'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td></div><td class='".$wcf."'  style='cursor : pointer' align='center' >".$row['Cantidad']."</td></tr>";
}

return;

}

if (isset($woperacion) && $woperacion=='muestradetalleempleado')
{
$wexp = utf8_decode($wexp);
 if($wcostos=='costosyp_000005')
	   $campo="Cconom";
  else
	   $campo="Ccodes";

$q = " SELECT Ceruse,Ideno1,Ideno2,Ideap1,Ideap2,".$campo.",Cardes,COUNT(Ceruse) as Cantidad,Idecer,Ideuse,Ccocod ,Carcod,Cermot,Cerexp, Idecec "
	."   FROM   ".$wnomina."_000008, ".$wtalhuma."_000013, ".$wcostos." ,root_000079"
	."  WHERE LEFT(Ideuse, 5) = RIGHT(Ceruse,5)"
	."    AND Ccocod = Idecco"
	."    AND Carcod = Ideccg "
	."    AND idecco = '".$wcco."' "
	."    AND Cermot = '".$wmotivo."'"
	."    AND Carcod = '".$wcargo."' "
	."    AND Cerexp = '".$wexp."' ";

if($wfechainicial!='' && $wfechafinal!='')
	$q .= " AND ".$wnomina."_000008.Fecha_data BETWEEN  '".$wfechainicial."' AND '".$wfechafinal."' ";

$q .= "Group by Ceruse ";
$q .= " ORDER by Cantidad DESC,Ideno1,Ideno2,Ideap1,Ideap2";
$res = mysql_query($q,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo "<tr class = 'xxxcco-".$wcco." xxxcco-".$wcco."-".$wcargo."-".$wmotivo."-".str_replace(" ","_",$wexp)." xxxcco-".$wcco."-".$wcargo."'><td><div class='encabezadoTabla' style='margin-left:30%; background-color : #999999	'>Nombre</div></td><td class='encabezadoTabla' width='50px' style='background-color: #999999'>Cantidad</td></tr>";
$k=0;
while($row = mysql_fetch_array($res))
{
	if (is_int ($k/2))
   {
	$wcf="fila1";  // color de fondo de la fila
   }
	else
   {
	$wcf="fila2"; // color de fondo de la fila
   }
   $k++;
	echo "<tr   class = 'xxxcco-".$wcco." xxxcco-".$wcco."-".$wcargo."-".$wmotivo."-".str_replace(" ","_",$wexp)." xxxcco-".$wcco."-".$wcargo."'><td  style='cursor : pointer' ><div class='".$wcf."'  style='margin-left:30%;'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</div></td><td class='".$wcf."' style='cursor : pointer' align='center' width='50px'>".$row['Cantidad']."</td></tr>";
}

return;

}

?>
<html>
<head>
<title>Estadistica de Certificados</title>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />


<script type='text/javascript'>

function muestradetallemotivo (ccargo)
{
var fechainicial;
var fenafinal;
var cargo;
var codigo;
var cco ;
salir = false;


	fechainicial = $('#datepicker1').val();
	fenafinal = $('#datepicker2').val();

	cargo =  $('#selectcargo').val();
	cco = $('#selectcco').val();
	codigo =  $('#inputcodigo').val();
	mot = $('#selectmotivo').val();



	if (  $('#trcargo-'+ccargo).siblings().hasClass('xxxcargo-'+ccargo)  ){
		$('#trcargo-'+ccargo).siblings('.xxxcargo-'+ccargo).remove();
		salir = true;
	}


	if(salir)
		return;


	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'muestradetallemotivo',
					wfechainicial : fechainicial,
					wfechafinal	: fenafinal,
					wcargo		: ccargo,
					wcco		: cco,
					wcodigo		: codigo,
					wmot	: mot,
					wemp_pmla	: $('#wemp_pmla').val()


				},function(data){
					$(data).insertAfter( $('#trcargo-'+ccargo) );
				});

}
function  muestradetallemotivoempleado(motivo, exp)
{
var fechainicial;
	var fenafinal;
	var cargo;
	var codigo;
	var cco ;
	salir = false;

	var nexp = exp.replace(/ /gi,"_");
	fechainicial = $('#datepicker1').val();
	fenafinal = $('#datepicker2').val();
	cargo =  $('#selectcargo').val();
	cco = $('#selectcco').val();
	codigo =  $('#inputcodigo').val();

	if (  $('#trmot-'+motivo+'-'+nexp).siblings().hasClass('xxxmot-'+motivo+'-'+nexp)  ){
		$('#trmot-'+motivo+'-'+nexp).siblings('.xxxmot-'+motivo+'-'+nexp).remove();
		salir = true;
	}


	if(salir)
		return;


	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'muestradetallemotivoempleado',
					wfechainicial : fechainicial,
					wfechafinal	: fenafinal,
					wcargo		: cargo,
					wmotivo		: motivo,
					wcodigo		: codigo,
					wcco		: cco,
					wemp_pmla	: $('#wemp_pmla').val(),
					wexp		: exp

				},function(data){

					$(data).insertAfter( $('#trmot-'+motivo+'-'+nexp) );

				});

}

function muestradetalleempleadocargo (codigocargo,motivo,exp)
{
var fechainicial;
	var fenafinal;
	var cargo;
	var codigo;
	var cco ;
	salir = false;
	var nexp = exp.replace(/ /gi,"_");

	fechainicial = $('#datepicker1').val();
	fenafinal = $('#datepicker2').val();
	cargo =  $('#selectcargo').val();
	cco = $('#selectcco').val();
	codigo =  $('#inputcodigo').val();

	if (  $('#trcargo-'+codigocargo+'-'+motivo+'-'+nexp).siblings().hasClass('xxxcargo-'+codigocargo+'-'+motivo+'-'+nexp)  ){
		$('#trcargo-'+codigocargo+'-'+motivo+'-'+nexp).siblings('.xxxcargo-'+codigocargo+'-'+motivo+'-'+nexp).remove();
		salir = true;
	}


	if(salir)
		return;


	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'muestradetalleempleadocargo',
					wfechainicial : fechainicial,
					wfechafinal	: fenafinal,
					wcargo		: codigocargo,
					wmotivo		: motivo,
					wcodigo		: codigo,
					wemp_pmla	: $('#wemp_pmla').val(),
					wexp		: exp

				},function(data){

					$(data).insertAfter( $('#trcargo-'+codigocargo+'-'+motivo+'-'+nexp) );

				});

}

function muestradetallecco(ccoseleccionado)
{
var fechainicial;
var fenafinal;
var cargo;
var codigo;
var cco ;
salir = false;


	fechainicial = $('#datepicker1').val();
	fenafinal = $('#datepicker2').val();
	cargo =  $('#selectcargo').val();
	cco = $('#selectcco').val();
	codigo =  $('#inputcodigo').val();
	mot = $('#selectmotivo').val();



	if (  $('#trcco-'+ccoseleccionado).siblings().hasClass('xxxcco-'+ccoseleccionado)  ){
		$('#trcco-'+ccoseleccionado).siblings('.xxxcco-'+ccoseleccionado).remove();
		salir = true;
	}


	if(salir)
		return;


	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'muestradetallecco',
					wfechainicial : fechainicial,
					wfechafinal	: fenafinal,
					wcargo		: cargo,
					wccoseleccionado	: ccoseleccionado,
					wcco		: cco,
					wcodigo	: codigo,
					wmot	: mot,
					wemp_pmla	: $('#wemp_pmla').val()

				},function(data){
					$(data).insertAfter( $('#trcco-'+ccoseleccionado) );
					});

}

function muestradetallecargo (codigocargo, codigocco)
{
	var fechainicial;
	var fenafinal;
	var cargo;
	var codigo;
	var cco ;
	salir = false;


	fechainicial = $('#datepicker1').val();
	fenafinal = $('#datepicker2').val();
	cargo =  $('#selectcargo').val();
	cco = $('#selectcco').val();
	codigo =  $('#inputcodigo').val();
	mot = $('#selectmotivo').val();


	if (  $('#trcco-'+codigocco+'-'+codigocargo).siblings().hasClass('xxxcco-'+codigocco+'-'+codigocargo)  ){
		$('#trcco-'+codigocco+'-'+codigocargo).siblings('.xxxcco-'+codigocco+'-'+codigocargo).remove();
		salir = true;
	}


	if(salir)
		return;


	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'muestradetallecargo',
					wfechainicial : fechainicial,
					wfechafinal	: fenafinal,
					wcargo		: codigocargo,
					wcco		: codigocco,
					wcodigo		: codigo,
					wmot	: mot,
					wemp_pmla	: $('#wemp_pmla').val()


				},function(data){

					$(data).insertAfter( $('#trcco-'+codigocco+'-'+codigocargo) );

				});

}
function muestradetalleempleado(codigocargo, codigocco, motivo,exp)
{
	var fechainicial;
	var fenafinal;
	var cargo;
	var codigo;
	var cco ;
	salir = false;
	var nexp = exp.replace(/ /gi,"_");

	fechainicial = $('#datepicker1').val();
	fenafinal = $('#datepicker2').val();
	cargo =  $('#selectcargo').val();
	cco = $('#selectcco').val();
	codigo =  $('#inputcodigo').val();

	if (  $('#trcco-'+codigocco+'-'+codigocargo+'-'+motivo+'-'+nexp).siblings().hasClass('xxxcco-'+codigocco+'-'+codigocargo+'-'+motivo+'-'+nexp)  ){
		$('#trcco-'+codigocco+'-'+codigocargo+'-'+motivo+'-'+nexp).siblings('.xxxcco-'+codigocco+'-'+codigocargo+'-'+motivo+'-'+nexp).remove();
		salir = true;
	}


	if(salir)
		return;


	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'muestradetalleempleado',
					wfechainicial : fechainicial,
					wfechafinal	: fenafinal,
					wcargo		: codigocargo,
					wcco		: codigocco,
					wmotivo		: motivo,
					wcodigo		: codigo,
					wemp_pmla	: $('#wemp_pmla').val(),
					wexp 		: exp

				},function(data){

					$(data).insertAfter( $('#trcco-'+codigocco+'-'+codigocargo+'-'+motivo+'-'+nexp) );

				});

}

function puedegenerarcertificado(codigoempleado)
{
	var estado = 'off';
    if(  $('#certificado-'+codigoempleado).is(':checked') )
		estado= 'on';

	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'puedegenerarcertificado',
					wcodigoempleado : codigoempleado,
					westado			: estado,
					wemp_pmla	: $('#wemp_pmla').val()
				});
}


function cesantiascongeladas(codigoempleado)
{
	var estado = 'off';
    if(  $('#cesantias-'+codigoempleado).is(':checked') )
		estado= 'on';

	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'cesantiascongeladas',
					wcodigoempleado : codigoempleado,
					westado			: estado,
					wemp_pmla	: $('#wemp_pmla').val()
				});
}



function esperar (  )
{

$.blockUI({ message: '<img src="../../images/medical/ajax-loader5.gif" >',
                               css:         {
                                           width:   'auto',
                                           height: 'auto',
										   align : 'center'
                                       }
                       });
}

function muestrareporte()
{
	var fechainicial;
	var fenafinal;
	var cargo;
	var codigo;
	var cco ;

	fechainicial = $('#datepicker1').val();
	fenafinal = $('#datepicker2').val();
	cargo =  $('#selectcargo').val();
	codigo =  $('#inputcodigo').val();
	cco = $('#selectcco').val();
	mot = $('#selectmotivo').val();

$.blockUI({ message: $('#msjEspere') });

	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax: '',
					woperacion	: 'muestrareporte',
					wfechainicial : fechainicial,
					wfechafinal	: fenafinal,
					wcargo		: cargo,
					wcodigo		: codigo,
					wcco		: cco,
					wmot	: mot,
					wemp_pmla	: $('#wemp_pmla').val()

				},function(data){
					$('#idresultados').html(data);
					$.unblockUI();
				});


}

function muestradetalle(codigoesp)
{

	var fechainicial;
	var fenafinal;
	var cargo;
	var codigo;
	var cco ;
	salir = false;


	fechainicial = $('#datepicker1').val();
	fenafinal = $('#datepicker2').val();
	cargo =  $('#selectcargo').val();
	cco = $('#selectcco').val();
	mot = $('#selectmotivo').val();



	if (  $('#tr-'+codigoesp).siblings().hasClass('xxx-'+codigoesp)  ){
		$('#tr-'+codigoesp).siblings('.xxx-'+codigoesp).remove();
		salir = true;
	}


	if(salir)
		return;


	$.post("Estadisticas_certificado_laboral.php",
				{
					consultaAjax  : '',
					woperacion    : 'muestradetalle',
					wfechainicial : fechainicial,
					wfechafinal   : fenafinal,
					wcargo        : cargo,
					wcodigoesp    : codigoesp,
					wcco          : cco,
					wmot          : mot,
					wemp_pmla     : $('#wemp_pmla').val()

				},function(data){

					$(data).insertAfter( $('#tr-'+codigoesp) );

				});
}


$(function() {
	$( "#datepicker1" ).datepicker({
	showOn: "button",
	buttonImage: "../../images/medical/root/calendar.gif",
	changeMonth: true,
	changeYear: true,
	buttonImageOnly: true,
	dateFormat: "yy-mm-dd"	});




	$( "#datepicker2" ).datepicker({
	showOn: "button",
	buttonImage: "../../images/medical/root/calendar.gif",
	changeMonth: true,
	changeYear: true,
	buttonImageOnly: true,
	dateFormat: "yy-mm-dd"	});


});

 function ocultarBusqueda()
    {
        $('#div_varios_encontrados').hide();
        $('#cont_buscar').hide("slow");
    }

    function recargarLista(id_padre, id_hijo, form)
    {
        val = $("#"+id_padre.id).val();
        url_add_params = addUrlCamposCompartidosTalento();
        $('#'+id_hijo).load(    "buscame.php?"+url_add_params,
                                {
                                    consultaAjax:   '',
                                    //wemp_pmla:  $("#wemp_pmla").val(),
                                    //wtema:      $("#wtema").val(),
                                    accion:     'recarga',
                                    id_padre:   val,
                                    form:       form
                                });
    }

function cambioImagen(img1, img2)
{
	$('#'+img1).hide(1000);
	$('#'+img2).show(1000);
}

$(document).ready(function(){

$('#ui-datepicker-div').hide();
});
</script>

<style type="text/css">
    .displ{
        display:block;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
	#tooltip
	{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>
</head>
<body >

<?php

$wactualiz = "(Agosto 13 de 2013)";

echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';

//----------------------------
$titulo = "ESTADISTICAS CERTIFICADO LABORAL";
// Se muestra el encabezado del programa


encabezado($titulo,$wactualiz, "clinica");


echo "<br>";

// consulta para llenar el select de centros de costos
 if($wcostos=='costosyp_000005')
	   $campo="Cconom";
  else
	   $campo="Ccodes";

$qcco = 	 " SELECT Ccocod,".$campo."  "
			."   FROM  ".$wcostos."";

$rescco = mysql_query($qcco,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$qcco." - ".mysql_error());
//----------------------------------------------------

$qcargo =  	 "  SELECT Carcod, Cardes  "
			."    FROM  root_000079 "
			."ORDER BY  Cardes";

$rescargo = mysql_query($qcargo,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$qcargo." - ".mysql_error());

echo"<table  width='500' align='center'>";
echo "<tr><td class='encabezadoTabla' colspan='2' align='center' >Criterios de busqueda</td></tr>";
echo "<tr><td class='encabezadoTabla' width='120' >Codigo</td><td class='fila1' ><input type='text' id='inputcodigo' size='50'/></td></tr>";
echo "<tr><td class='encabezadoTabla' width='120'>Cargo</td><td class='fila1'>";
echo "<Select id='selectcargo' style='width:330px' >";
echo "<option value='seleccione'>Todos</option>";
while($rowcargo = mysql_fetch_array($rescargo))
{
	echo "<option value='".$rowcargo['Carcod']."'>".$rowcargo['Cardes']."</option>";
}
echo "</Select>";
echo "</td></tr>";
echo "<tr><td class='encabezadoTabla' width='120'>Centro de Costos</td><td class='fila1'>";
echo "<Select id='selectcco' style='width:330px'>";
echo "<option value='seleccione'>Todos</option>";
while($rowcco = mysql_fetch_array($rescco))
{
	echo "<option value='".$rowcco['Ccocod']."'>".$rowcco['Ccocod']."-".$rowcco[$campo]."</option>";
}

echo '<center>';
echo "</select>";
echo "</td></tr>";
echo "<tr><td class='encabezadoTabla' width='60'>Motivo</td><td class='fila1'>";

$qmotivo = "SELECT Codigo, Motivo "
		."    FROM ".$wnomina."_000001 ";

$resmotivo = mysql_query($qmotivo,$conex) or die ("Error 3:".mysql_errno()." - en el query: ".$qmotivo." - ".mysql_error());

echo "<Select id='selectmotivo'>";
echo "<option value='seleccione'>Todos</option>";

while($rowmotivo = mysql_fetch_array($resmotivo))
{
	echo "<option value='".$rowmotivo['Codigo']."'>".$rowmotivo['Motivo']."</option>";
}

echo "</select>";

echo "</td></tr>";
echo "<tr><td class='encabezadoTabla' width='60'>Fecha inicial</td><td class='fila1'><input type='text' id='datepicker1' /></td></tr>";
echo "<tr><td class='encabezadoTabla' width='60'>Fecha final</td><td class='fila1'><input type='text' id='datepicker2' /></td></tr>";
echo "<tr><td class='fila1' colspan='2' align='center'><input type='button'  value='Generar' onclick='muestrareporte()'></td></tr>";
echo "</table>";

echo "<div id='idresultados'></div>";


echo "<div id='msjEspere' style='display:none;'>";
               echo '<br>';
               echo "<img src='../../images/medical/ajax-loader5.gif'/>";
               echo "<br><br> Por favor espere un momento ... <br><br>";
echo '</div>';
echo '</center>';



?>
</body>
</html>