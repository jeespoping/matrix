<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
include_once("funciones_talhuma.php");

if($woperacion=='crearformulas')
{

	echo "<table ><tr><td>Crear agrupaciones</td><td><input type='button' value='ok'></td></tr>
				 <tr><td>Visualizar resulatados</td><td>
						<select id='tipo_de_visualizacion'>
							<option value=''>Seleccione</option>
							<option value='porcentaje'>porcentaje</option>
							<option value='promedio'>promedio</option>
						</select>
				     </td>
				 </tr>
				 <tr><td>No tener encuenta</td>
					<td>
						<select id='tener_encuenta'>
							<option value=''>Seleccione</option>
							<option value='0'>valores en cero</option>
						</select>
					</td>
				 </tr>
		  </table>";


	return;
}

if ($operacion=='eliminardescriptor')
{
	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q = "UPDATE ".$wbasedato."_000005 "
		."   SET Desest = 'off' "
		." WHERE Descod = '".$wcodigo."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}
if ($woperacion=='ponercheck')
{

	

	include_once("root/comun.php");
	


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$qrespuestas = "  SELECT Notcod , Notdes ,id,Notval"
					."  FROM ".$wbasedato."_000047 "
			   ."      WHERE Notgru = '".$wcodigocompetencia."' ";


	$resrespuestas = mysql_query($qrespuestas,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qrespuestas." - ".mysql_error());
	$numresrespuestas = mysql_num_rows($resrespuestas);

	if ($numresrespuestas>0)
	{

	$tableaux = "<table>";
	$chequeado = '';
	while($rowrespuestas = mysql_fetch_array($resrespuestas))
	{
		if($rowrespuestas['Notval'] == '1')
		{
			$chequeado = "checked='checked'";
		}
		$tableaux .= "<tr><td>".$rowrespuestas['Notcod']."</td><td><textarea  id='resp".$rowrespuestas['id']."-".$wcodigocompetencia."'  onblur='guardadrespuesta(\"".$wcodigocompetencia."\",\"".$rowrespuestas['Notcod']."\", \"resp".$rowrespuestas['id']."-".$wcodigocompetencia."\")' rows='2' cols='25'>".$rowrespuestas['Notdes']."</textarea></td><td><input  type='checkbox' id= '".$row1['Descod']."-".$rowrespuestas['Notcod']."'  name='respuestas' ".$chequeado." onclick='cambiarokpregunta(\"".$wcodigocompetencia."\",\"".$rowrespuestas['Notcod']."\",this)'  ></td></tr>";
		$chequeado = '';
	}
	$tableaux .= "</table>";

	echo $tableaux;
	}

return;
}



if ($woperacion=='cambiarokpregunta')
{

	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q = "UPDATE ".$wbasedato."_000047 "
		."   SET Notval = 0 "
		." WHERE Notgru = '".$wnpregunta."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());



	$q = "UPDATE ".$wbasedato."_000047 "
		."   SET Notval = 1 "
		." WHERE Notgru = '".$wnpregunta."' "
		."  AND  Notcod = '".$wcodpregunta."'";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}

if ($operacion=='eliminarcompetencia')
{
	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q = "UPDATE ".$wbasedato."_000004 "
		."   SET Comest = 'off' "
		." WHERE Comcod = '".$wcodigo."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$q = "UPDATE ".$wbasedato."_000005 "
		."   SET Desest = 'off' "
		." WHERE Descom = '".$wcodigo."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	return;
}

if ($operacion=='eliminargcompetencia')
{
	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q = "UPDATE ".$wbasedato."_000003 "
		."   SET gcoest = 'off' "
		." WHERE Gcocod = '".$wcodigo."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	$q = "UPDATE ".$wbasedato."_000004 "
		."   SET Comest = 'off' "
		." WHERE Comgco= '".$wcodigo."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$q= " SELECT Comcod "
		."  FROM ".$wbasedato."_000004 "
		." WHERE Comgco= '".$wcodigo."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	while($row =mysql_fetch_array($res))
	{
		$q1 = "UPDATE ".$wbasedato."_000005 "
			."   SET Desest = 'off' "
			." WHERE Descom = '".$wcodigo."' ";

		$res1 = mysql_query($q1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());

	}


	return;
}



if( $operacion=='asignaOrdenGCompetencia')
{
	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$welemento = explode ("-" , $welemento);


	$q = "UPDATE ".$wbasedato."_000006 "
		."   SET  forogc = '".$wdato."' "
		." WHERE Forfor = '".$welemento[1]."' "
		."   AND Forgco = '".$welemento[2]."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}


if( $operacion=='asignaOrdenCompetencia')
{
	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$welemento = explode ("-" , $welemento);


	$q = "UPDATE ".$wbasedato."_000006 "
		."   SET  foroco = '".$wdato."' "
		." WHERE Forfor = '".$welemento[1]."' "
		."   AND Forgco = '".$welemento[2]."' "
		."   AND Forcom = '".$welemento[3]."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;


}

if( $operacion=='traeOrdenGcompetencia')
{
	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$welemento = explode ("-" , $welemento);


	$q = "SELECT  forogc "
		."  FROM  ".$wbasedato."_000006 "
		." WHERE Forfor = '".$welemento[1]."' "
		." AND Forgco = '".$welemento[2]."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);


	echo $row['forogc'];
	return;


}

if( $operacion=='traeOrdencompetencia')
{
	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$welemento = explode ("-" , $welemento);


	$q = "SELECT  foroco "
		."  FROM  ".$wbasedato."_000006 "
		." WHERE Forfor = '".$welemento[1]."' "
		." AND Forgco = '".$welemento[2]."' "
		." AND Forcom = '".$welemento[3]."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);


	echo $row['foroco'];
	return;

}

if( $operacion=='traeOrden')
{
	

	include_once("root/comun.php");
	

	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$welemento = explode ("-" , $welemento);


	$q = "SELECT  Forord "
		."  FROM  ".$wbasedato."_000006 "
		." WHERE Fordes = '".$welemento[4]."' "
		." AND Forfor = '".$welemento[1]."' "
		." AND Forgco = '".$welemento[2]."' "
		." AND Forcom = '".$welemento[3]."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);


	echo $row['Forord'];
	return;
}

if( $operacion=='asignaOrden')
{
	

	include_once("root/comun.php");
	


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$welemento = explode ("-" , $welemento);


	$q = "UPDATE ".$wbasedato."_000006 "
		." SET  Forord = '".$wdato."' "
		." WHERE Fordes = '".$welemento[4]."' "
		." AND Forfor = '".$welemento[1]."' "
		." AND Forgco = '".$welemento[2]."' "
		." AND Forcom = '".$welemento[3]."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;

}

if(isset($selectcarganotas) AND $selectcarganotas!='')
{
	

	include_once("root/comun.php");
	




	$user_session = explode('-',$_SESSION['user']);
	$user_session = $user_session[1];


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q  =  "SELECT Notdes,Notval,Notima "
		.  "  FROM   ".$wbasedato."_000047 "
		.  " WHERE Notgru = '".$selectcarganotas."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$i=0;
	while($row =mysql_fetch_array($res))
	{
		$descriptor[$i] = $row['Notdes'];
		$valores[$i] = $row['Notval'];
		$caracteristicas[$i] = $row['Notima'];
		$i++;
	}

	echo"<table align='center' id='Notas'>";




	echo"<tr class='fila1'>";
	$j=0;
	while ($j<$i)
	{
		echo"<td class='fila2'>&nbsp;&nbsp;</td><td>".$descriptor[$j]."</td>";
		$j++;
	}

	echo "</tr>";
	echo "<tr class='fila1'>";
	$j=0;
	while ($j<$i)
	{
		echo"<td class='fila2'>&nbsp;&nbsp;</td><td>".$valores[$j]."</td>";
		$j++;
	}

	echo "</tr>";
	echo "<tr>";
	$j=0;
	while ($j<$i)
	{
		echo"<td class='fila2'>&nbsp;&nbsp;</td><td><img width='32' height='33' src='".$caracteristicas[$j]."' /></td>";
		$j++;
	}

	echo "</tr>";
	echo"</table>";
	echo"<table align='center' id='respuestas'>";
	echo"</table>";
	return;


}
if(isset($wagregarRespuestas) and $wagregarRespuestas=='si')
{
echo"<br>";
echo"<table align='center'>";
$i=1;
$vectoralfabeto[1]='A';
$vectoralfabeto[2]='B';
$vectoralfabeto[3]='C';
$vectoralfabeto[4]='D';
$vectoralfabeto[5]='E';
echo"<tr class='encabezadoTabla'>";
echo"<td></td>";
echo"<td>Respuesta</td>";
echo"<td>Correcta</td>";
echo"</tr>";
while($i<=4)
{
	echo"<tr>";
	echo"<td id='nomemclatura-".$i."'>".$vectoralfabeto[$i].")</td>";
	echo"<td><textarea rows='3'  cols='40' id='textarearespuestas-".$i."'></textarea></td>";
	echo"<td align='center'><input type='radio' name='respuesta'  id='checkrespuesta-".$i."'/></td>";
	echo"</tr>";
	$i++;
}
echo"</table>";

return;

}


if(isset($wgrupodescriptores) and $wgrupodescriptores!='')
{


include_once("root/comun.php");




$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];


$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$q = " SELECT  DISTINCT(Notgru) "
    ."  FROM ".$wbasedato."_000047"
	." WHERE Nottde='".$wgrupodescriptores."'";

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

// si el grupo de descriptores es diferente a texto  muestre 03
// si el grupo de descriptores es diferente a texto  fecha con cierre 07
// si el grupo de descriptores es diferente a texto  fecha 08
// si el grupo de descriptores es diferente a texto  valor no tenido en cuenta en los informes  09
if($wgrupodescriptores !='03' ||  $wgrupodescriptores !='07' ||  $wgrupodescriptores !='08'  ||  $wgrupodescriptores !='09'  )
{
echo "<br><table align='Left'><tr><td>Grupo de Notas</td><td><select id='selectcarganotas' tipo='obligatorio' onchange='carganotas()'>";
echo"<option value='' >seleccionar</option>";
while($row =mysql_fetch_array($res))
{
	echo"<option value='".$row['Notgru']."' >".$row['Notgru']."</option>";
}
echo "</select></td></tr></table>";
}
return;

}

if(isset($wdescriptortipo) and $wdescriptortipo =='06')
{


include_once("root/comun.php");




$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];


$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

echo "<table align='center'>";
echo "<tr><td><input type='button' value='Agregar Respuestas' onclick='generarRespuestas()'></td></tr>";

echo "</table>";
return;

}


if( isset($grabanuevoelemento) && $grabanuevoelemento =='si' )
{
   

   include_once("root/comun.php");

   


   $user_session = explode('-',$_SESSION['user']);
   $user_session = $user_session[1];
   $wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
   $fecha =date("Y-m-d");
   switch($elemento)
       {
               //Si $nomFila vale Formato se agrega el titulo del formato
			   case 'tema':
				{
				  //  miro cuantos hay, de esta manera agrego bien  su id en el menu de temas (tabletemas), asegurando el correcto
				  //  funcionamiento sin recargar la pagina
				  $q = "SELECT COUNT(Fordes) AS Cuantos "
				     . "  FROM ".$wbasedato."_000042 "
					 . " WHERE Forest = 'on' ";

				  $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row =mysql_fetch_array($res);

				  $nuevoidtabla=$row['Cuantos'];

				  $q = "SELECT  MAX( CONVERT(Forcod, DECIMAL)) AS Cuantos "
				     . "  FROM ".$wbasedato."_000042 "
					 . " WHERE Forest = 'on' ";

				  $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row =mysql_fetch_array($res);

				  $codigonuevoelemento=($row['Cuantos']) + 1;


				  $q = "INSERT INTO ".$wbasedato."_000042 "
                      ."        ( Forcod,Fordes,Fortip,Fecha_data,Seguridad,Medico,Forper,Formpe,Fortes,Fortco) "
				      ." VALUES ( '".$codigonuevoelemento."', '".$descripcionelemento."' , '".$tipo."','".$fecha."','C-".$wbasedato."','".$wbasedato."','".$wperiodicidad."','".$wmaxperiodicidad."','".$wtemsiguiente."','".$wtipocontrato."') ";

				  $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				  echo "<tr  class='fila1' align='left'  id='fila-temas-".$nuevoidtabla."' class= 'fila1' ><td colspan='2'><input type='checkbox'  id='temas-".$codigonuevoelemento."-".$nuevoidtabla."' onclick='removerFilas(\"temas\",this, \"".$nuevoidtabla."\",".$nuevoidtabla.",\"".($descripcionelemento)."\" )' />".($descripcionelemento)."</td></tr>";
				}
				  break;

				case 'nivel':
				{

				  $q = "SELECT COUNT(Nivcod) AS Cuantos "
				     . "  FROM ".$wbasedato."_000001 "
					 . " WHERE Nivfor ='".$tipo."' ";

				  $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row =mysql_fetch_array($res);

				  $nuevoidtabla=$row['Cuantos'];

				  $q = " SELECT MAX( CONVERT( Nivcod, DECIMAL ) ) AS Cuantos2 "
					  ."   FROM ".$wbasedato."_000001 " ;

				  $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row =mysql_fetch_array($res);




				  $codigonuevoelemento = ($row['Cuantos2']) + 1;

				  $q = "INSERT INTO ".$wbasedato."_000001 "
                       ."        ( Nivcod,Nivdes,Nivfor,Fecha_data,Seguridad,Medico) "
				       ." VALUES ( '".$codigonuevoelemento."', '".$descripcionelemento."' , '".$tipo."','".$fecha."','C-".$user_session."' , '".$wbasedato."' ) ";

				  $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());



				  echo "<tr  class='fila1' align='left'  id='fila-niveles-".$nuevoidtabla."'  class= 'fila1' ><td colspan='2'><input type='checkbox'  id='niveles-".$codigonuevoelemento."-".$nuevoidtabla."' onclick='removerFilas(\"niveles\",this, \"".$nuevoidtabla."\",".$nuevoidtabla.",\"".($descripcionelemento)."\" )' />".$descripcionelemento."</td></tr>";
				}
				break;
				case 'formato':
				{


					$q = "SELECT COUNT(Forcod) AS Cuantos "
				     . "  FROM ".$wbasedato."_000002 " ;

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					$nuevoidtabla=$row['Cuantos'];

					$q = "SELECT  MAX( CONVERT( Forcod, DECIMAL ) ) AS Cuantos "
				     . "  FROM ".$wbasedato."_000002 " ;

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					$codigonuevoelemento= ($row['Cuantos']) + 1;

					$q = "INSERT INTO ".$wbasedato."_000002 "
                       ."        ( Forcod,Fordes,Forniv,Fortip,Fecha_data,Seguridad,Medico) "
				       ." VALUES ( '".$codigonuevoelemento."', '".$descripcionelemento."' , '".$tipo."','".$temaevaluacion."','".$fecha."','C-".$user_session."' , '".$wbasedato."' ) ";

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


					echo "<tr align='left'  id='fila-formatos-".$nuevoidtabla."' class= 'fila1' ><td colspan='2'><input type='checkbox'  id='formatos-".$codigonuevoelemento."' onclick='removerFilas(\"formatos\",this, \"".$nuevoidtabla."\",".$nuevoidtabla.",\"".($descripcionelemento)."\" )' />".$descripcionelemento."</td></tr>";

				}
				break;
				case 'gcompetencia':
				{
				    $q = " SELECT COUNT(Gcocod) AS Cuantos "
						. "  FROM ".$wbasedato."_000003 " ;

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					$nuevoidtabla=$row['Cuantos'];

					$q = "SELECT  MAX( CONVERT( Gcocod, DECIMAL ) )  AS Cuantos "
				       . "  FROM ".$wbasedato."_000003 " ;

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					$codigonuevoelemento= ($row['Cuantos']) + 1;

					$q = "INSERT INTO ".$wbasedato."_000003 "
                       ."        ( Gcocod,Gcodes,Gcopes,Fecha_data, Gcoapl, gcoest ,Seguridad,Medico) "
				       ." VALUES ( '".$codigonuevoelemento."', '".$descripcionelemento."','".$wgporcentaje."','".$fecha."' ,'".$waplica."' ,'on','C-".$user_session."' , '".$wbasedato."' ) ";

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    echo "<tr align='left'  id='fila-gcompetencias-".$codigonuevoelemento."' class= 'fila1' ><td colspan='2'><input type='checkbox'  id='gcompetencias-".$codigonuevoelemento."' onclick='removerFilas(\"gcompetencias\",this, \"".$nuevoidtabla."\",".$nuevoidtabla.",\"".($descripcionelemento)."\" )' />".$descripcionelemento."</td></tr>";

				}
				  break;
				case 'competencia':
				{

				   $q = "SELECT COUNT(Comcod) AS Cuantos "
				     . "  FROM ".$wbasedato."_000004 " ;

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					$nuevoidtabla=$row['Cuantos'];

					$q = "SELECT  MAX( CONVERT( Comcod, DECIMAL ) )  AS Cuantos "
				     . "  FROM ".$wbasedato."_000004 " ;

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					$codigonuevoelemento= ($row['Cuantos']) + 1;

					$q = "INSERT INTO ".$wbasedato."_000004 "
                       ."             ( Comcod,Comdes,Comgco,Fecha_data,Seguridad,Medico) "
				       ."      VALUES ( '".$codigonuevoelemento."', '".$descripcionelemento."','".$tipo."','".$fecha."','C-".$user_session."' , '".$wbasedato."' ) ";

				    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					echo "<tr align='left' id='fila-competencias-".$codigonuevoelemento."' class= 'fila1' ><td colspan='2'><input type='checkbox'  id='competencias-".$codigonuevoelemento."' onclick='removerFilas(\"competencias\",this, \"".$nuevoidtabla."\",".$nuevoidtabla.",\"".($descripcionelemento)."\" )' />".$descripcionelemento."</td></tr>";

				  }
				  break;
				case 'descriptor':
				{
				  $q = "SELECT COUNT(Descod) AS Cuantos "
				     . "  FROM ".$wbasedato."_000005 "
					 . " WHERE Desest != 'off' ";

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					$nuevoidtabla=$row['Cuantos'];

					$q = "SELECT  MAX( CONVERT( Descod, DECIMAL ) )  AS Cuantos "
				       . "  FROM ".$wbasedato."_000005 " ;

					$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row =mysql_fetch_array($res);

					$codigonuevoelemento= ($row['Cuantos']) + 1;
					if($wtipodescriptor=='06')
					{
						$wgrupodescriptor=$codigonuevoelemento;
					}

					$q = "INSERT INTO ".$wbasedato."_000005 "
                       ."             ( Descod,Desdes,Fecha_data,Descom,Destip,Desngr,Seguridad,Medico) "
				       ."      VALUES ( '".$codigonuevoelemento."', '".utf8_decode($descripcionelemento)."','".$fecha."','".$tipo."','".$wtipodescriptor."','".$wgrupodescriptor."','C-".$user_session."' , '".$wbasedato."' ) ";

				    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					if($wtipodescriptor=='06')
					{

						$respuestas = explode("||",($wrespuestas));
						$nomenclatura = explode("||",$wnomenclatura);
						$correcto = explode("||",$wcorrecto);
						$k=0;
						while($k < count($respuestas))
						{
							$q = "INSERT INTO ".$wbasedato."_000047 "
							   . "             (Medico,Fecha_data,Notgru,Notcod,Notdes,Notval,Notcar,Notord,Nottde,Notest,Seguridad)"
							   . "      VALUES ('".$wbasedato."','".$fecha."','".$codigonuevoelemento."','".$nomenclatura[$k]."','".$respuestas[$k]."','".$correcto[$k]."','04','".($k+1)."','".$wtipodescriptor."','on','C-".$wbasedato."')";


							$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$k++;

						}
					}
				    echo "<tr align='left'  id='fila-descriptores-".$codigonuevoelemento."' class= 'fila1' ><td><input type='checkbox'  id='descriptores2-".$codigonuevoelemento."'  value='".$codigonuevoelemento."'  /></td><td>".utf8_decode($descripcionelemento)."</td></tr>";


				}
				  break;

		}
 return;
}


if(isset($inicio) && $inicio =='si')
{
	

	include_once("root/comun.php");
	



	echo "<table id='tabla_inicio'  width='100%' >
	<tr class='encabezadoTabla'><td>Seleccione un Tema</td><td align='right' width='10' ><input   type='button' value='+' onClick='fnMostrar2( \"agregartema\" )' ></td></tr>";

	$i=0;
	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

    $q="SELECT Forcod, Fordes,Fortip "
      ."  FROM ".$wbasedato."_000042 "
	  ." WHERE Forest='on' ";

    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $numrowcom = mysql_num_rows($res);
	while ($row =mysql_fetch_array($res))
	{
		  if (is_int ($i/2))
		  {
			$wcf="fila1";  // color de fondo de la fila
		  }
		  else
		  {
			$wcf="fila2"; // color de fondo de la fila
		  }
		  echo "<tr align='left'  id='fila-temas-".$i."' class= 'fila1' ><td colspan='2'><input type='checkbox'  id='temas-".$row['Forcod']."-".$i."' onclick='removerFilas(\"temas\",this, \"".$numrowcom."\",".$i.",\"".($row['Fordes'])."\" ,\"".$row['Fortip']."\")' />".($row['Fordes'])."</td></tr>";

		  $i++;


	}
	//echo "<tr  id='trnuevotema'></tr>";

	echo "</table>";

	return;

}

if(isset($niveles) && $niveles =='si')
{
	

	include_once("root/comun.php");
	



	echo "<table id='tableniveles' width='100%' >
	<tr class='encabezadoTabla'><td>Seleccione un Nivel</td><td align='right' width='10' ><input   type='button' value='+' onClick='fnMostrar2( \"agregarnivel\" )' ></td></tr>";
	$i=0;


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

    $q="SELECT Nivdes, Nivcod "
      ."  FROM ".$wbasedato."_000001 "
	  ." WHERE Nivfor='".$wtemaevaluacion."' ";

    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $numrowcom = mysql_num_rows($res);
	while ($row =mysql_fetch_array($res))

	{
		  if (is_int ($i/2))
		  {
			$wcf="fila1";  // color de fondo de la fila
		  }
		  else
		  {
			$wcf="fila2"; // color de fondo de la fila
		  }
		  echo "<tr align='left'  id='fila-niveles-".$i."' class= '".$wcf."' ><td colspan='2'><input type='checkbox'  id='niveles-".$row['Nivcod']."-".$i."' onclick='removerFilas(\"niveles\",this, \"".$numrowcom."\",".$i.",\"".$row['Nivdes']."\" )' />".($row['Nivdes'])."</td></tr>";
		  $i++;


	}
	echo "</table>";


   return;
}

if(isset($formatos) && $formatos == 'si')
{



include_once("root/comun.php");



echo "<table id='tableformatos' width='100%'>
	  <tr class='encabezadoTabla'><td>Seleccione un Formato</td><td align='right' width='10' ><input   type='button' value='+' onClick='fnMostrar2( \"agregarformato\" )' ></td></tr>";

	$i=0;


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

    $q="  SELECT Fordes, Forcod "
      ."    FROM ".$wbasedato."_000002 "
	  ."   WHERE Forniv='".$wnivel."' "
	 //."     AND Fortip='01'  "
	  ."ORDER BY Fordes";

    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $numrowcom = mysql_num_rows($res);
	while ($row =mysql_fetch_array($res))

	{
		  if (is_int ($i/2))
		  {
			$wcf="fila1";  // color de fondo de la fila
		  }
		  else
		  {
			$wcf="fila2"; // color de fondo de la fila
		  }
		  echo "<tr align='left'  id='fila-formatos-".$i."' class= '".$wcf."' ><td colspan='2'><input type='checkbox'  id='formatos-".$row['Forcod']."' onclick='removerFilas(\"formatos\",this, \"".$numrowcom."\",".$i.",\"".($row['Fordes'])."\" )' />".($row['Fordes'])."</td></tr>";
		  $i++;


	}
	echo "</table>";
 return;
}

if(isset($gcompetencias) && $gcompetencias == 'si')
{



include_once("root/comun.php");



$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
echo "<table id='tablegcompetencias' width='100%'>
			 <tr class='encabezadoTabla'><td>Seleccione Grupo de Competencias (Fac. critico servicio)</td><td align='right' width='10' ><input  type='button' value='+' value='+' onClick='fnMostrar2( \"agregargcompetencia\" )'  ></td></tr>";
	$i=0;


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

    $q="   SELECT Gcodes, Gcocod "
      ."     FROM ".$wbasedato."_000003 "
	  ."    WHERE gcoest != 'off' "
	  ." ORDER BY Gcodes";

    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $numrowcom = mysql_num_rows($res);
	while ($row =mysql_fetch_array($res))

	{
		  if (is_int ($i/2))
		  {
			$wcf="fila1";  // color de fondo de la fila
		  }
		  else
		  {
			$wcf="fila2"; // color de fondo de la fila
		  }
		  echo "<tr align='left'  id='fila-gcompetencias-".$i."' class= '".$wcf."' ><td colspan='2'><input type='checkbox'  id='gcompetencias-".$row['Gcocod']."' onclick='removerFilas(\"gcompetencias\",this, \"".$numrowcom."\",".$i.",\"".($row['Gcodes'])."\" )' />".($row['Gcodes'])."<a onclick='desactivargcompetencia(\"".$row['Gcocod']."\",\"fila-gcompetencias-".$i."\")' style='cursor:pointer; color: gray; float: right;'>eliminar</a></td></tr>";
		  $i++;


	}
	echo "</table>";

 return;
}

if(isset($competencias) && $competencias == 'si')
{



include_once("root/comun.php");



$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
echo "<table id='tablecompetencias' width='100%' >
	  <tr class='encabezadoTabla'><td width='100%'>Seleccione Competencia (variable de servicio)</td><td align='right' width='10' ><input  type='button' value='+'  onClick='fnMostrar2( \"agregarcompetencia\" )'  ></td></tr>";
	$i=0;


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

    $q="   SELECT Comdes, Comcod "
      ."     FROM ".$wbasedato."_000004 "
	  ."    WHERE  Comgco = '".$wgcompetencia."' "
	  ."      AND Comest != 'off' "
	  ." ORDER BY  Comdes";

    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $numrowcom = mysql_num_rows($res);
	while ($row =mysql_fetch_array($res))

	{
		  if (is_int ($i/2))
		  {
			$wcf="fila1";  // color de fondo de la fila
		  }
		  else
		  {
			$wcf="fila2"; // color de fondo de la fila
		  }
		  echo "<tr align='left' id='fila-competencias-".$i."' class= '".$wcf."'  ><td colspan='2'><input type='checkbox'  id='competencias-".$row['Comcod']."' onclick='removerFilas(\"competencias\",this, \"".$numrowcom."\",".$i.",\"".($row['Comdes'])."\" )' />".($row['Comdes'])." ".($row['Desdes'])." <a onclick='desactivarcompetencias(\"".$row['Comcod']."\",\"fila-competencias-".$i."\")' style='cursor:pointer; color: gray; float: right;'>eliminar</a></td></tr>";
		  $i++;

	}
	echo "</table>";


 return;
}

if(isset($descriptores) && $descriptores == 'si')
{

	

	include_once("root/comun.php");
	


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q="SELECT Fordes "
      ."  FROM ".$wbasedato."_000006 "
	  ." WHERE Forfor='".$wformato."' "
	  ."   AND Forcom='".$wcompetencia."'";

	$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $numrowcom = mysql_num_rows($res);

	$i=0;
	$vectorDesExistentes= array();
	while ($row =mysql_fetch_array($res))
	{

		  $vectorDesExistentes[$row['Fordes']]=$row['Fordes'];

	}

    $q="   SELECT Desdes, Descod "
      ."     FROM ".$wbasedato."_000005 "
	  ."    WHERE Descom='".$wcompetencia."' "
	  ."      AND Desest != 'off' "
	  ." ORDER BY Desdes";

    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $numrowcom = mysql_num_rows($res);

    echo "<table id='tabledescriptores' width='100%' >

			 <tr class='encabezadoTabla'><td colspan='2'>Seleccione Descriptores (preguntas)<input  type='button' value='+'  onClick='fnMostrar2( \"agregardescriptores\" )'  ></td></tr>

			 <tr class='encabezadoTabla'><td align='right' colspan='2'><input type='button' value='Grabar' onclick='grabarDescriptores(); '  /><input type='button' value='Cancelar' onclick='cancelar();'  /></td></tr>";
	$i=0;
	$n=0;

	while ($row =mysql_fetch_array($res))

	{
		  if (is_int ($n/2))
		  {
			$wcf="fila1";  // color de fondo de la fila
		  }
		  else
		  {
			$wcf="fila2"; // color de fondo de la fila
		  }
		  if (!array_key_exists($row['Descod'],$vectorDesExistentes))
		  {
			  echo "<tr align='left'  id='fila-descriptores-".$i."' class= '".$wcf."' ><td><input type='checkbox'  id='descriptores2-".$i."'  value='".$row['Descod']."'  /></td><td>".($row['Desdes'])." <a onclick='desactivardescriptores(\"".$row['Descod']."\",\"fila-descriptores-".$i."\")' style='cursor:pointer; color: gray; float: right;'>eliminar</a></td></tr>";
			  $i++;
		  }
		  else
		  {
			echo "<tr align='left'  id='fila-descriptores-".$i."' class= '".$wcf."' ><td></td><td>".($row['Desdes'])." <a onclick='desactivardescriptores(\"".$row['Descod']."\",\"fila-descriptores-".$i."\")' style='cursor:pointer; color: gray; float: right;'>eliminar</a></td></tr>";
		  }
		  $n++;
	}
	echo "</table>";
    return;
}

if(isset($grabardescriptores) && $grabardescriptores == 'si')
{

	

	include_once("root/comun.php");
	


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
    if ($graba=='si')
	{
		$wdescriptor = explode("-", $wdescriptor);
		$numveces=count($wdescriptor);
		$i=1;
		while($i<$numveces)
		{
			$q="INSERT INTO  ".$wbasedato."_000006 "
			  ."      		(Forfor,Forgco,Forcom,Fordes )"
			  ."     VALUES('".$wformato."','".$wgcompetencia."' , '".$wcompetencia."', '".$wdescriptor[$i]."') ";

			$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$q;
		  $i++;
		}

	}

    if($graba=='elimina')
    {
		$q= "DELETE  "
		   ."  FROM  ".$wbasedato."_000006 "
		   ." WHERE  Forfor='".$wformato."' "
		   ."   AND  Forgco='".$wgcompetencia."' "
		   ."   AND  Forcom='".$wcompetencia."' "
		   ."   AND  Fordes='".$wdescriptor."' ";

		$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    }

    if($graba=='eliminacompetencia')
    {

		$q= "DELETE  "
		   ."  FROM  ".$wbasedato."_000006 "
		   ." WHERE  Forfor='".$wformato."' "
		   ."   AND  Forgco='".$wgcompetencia."' "
		   ."   AND  Forcom='".$wcompetencia."' ";

		$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    }
	$tipo='01';

	$q=	" 	SELECT Forfor, Forgco, Forcom, ".$wbasedato."_000006.Fordes "
		."  FROM ".$wbasedato."_000006,".$wbasedato."_000002,".$wbasedato."_000003,".$wbasedato."_000004,".$wbasedato."_000005  "
		." 	WHERE Forfor= '".$wformato."' "
		."	  AND Forfor = Forcod "
		."	  AND Forgco = Gcocod "
		."	  AND Forcom = Comcod "
		."    AND Descod = ".$wbasedato."_000006.Fordes "
		."    AND Desest != 'off' "
		."    AND Comest != 'off' "
		."    AND gcoest != 'off' "
		."	ORDER BY forogc,Forgco,foroco,Forcom,".$wbasedato."_000006.Fordes,Forord";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow1 = mysql_num_rows($res);


	$i = 0;
	$ig = 0;
	$ic = 0;
	$auxGcompetencia = 0;
	$auxCompetencia = 0;
	$auxDescriptor= 0;
	// se llena los vectores: arr_gcompetencia, arr_competencia, arr_descriptor y arr_conTotal
	while ($row =mysql_fetch_array($res))
	{
	//echo "auxgcompetencia:  ".$auxGcompetencia."      array".$i.":  ".$arr_gcompetencia['Forgco'];
		if($auxGcompetencia != $row['Forgco'] )
		{
			$arr_gcompetencia[$ig] = $row['Forgco'];
			$ig++;
		}
		if($auxCompetencia != $row['Forcom'] )
		{

			$arr_competencia[$ic] = $row['Forcom'];
			$ic++;
		}
		$arr_descriptor[$i] = $row['Fordes'];
		$arr_conTotal[$row['Forfor']][$row['Forgco']][$row['Forcom']][] = $row['Fordes'];
		$auxGcompetencia=$row['Forgco'];
		$auxCompetencia=$row['Forcom'];
		$auxDescriptor=$row['Fordes'];
		$i++;
	}
	$rowspan[]=0;
	for($j=0;$j<count($arr_gcompetencia);$j++)
	{
		//echo count($arr_conTotal["".$arr_gcompetencia[$i].""]["".$arr_competencia[$i].""]);

		$numcompetencias[$j] = count($arr_conTotal["".$wformato.""]["".$arr_gcompetencia[$j].""]);
		$numdescriptoresxcomp[$j] = 0;
		$numdescriptores[$j] = 0;

		for($i=0;$i<$numrow1;$i++)
		{

			$numdescriptores[$j] = $numdescriptores[$j]  + count($arr_conTotal["".$wformato.""]["".$arr_gcompetencia[$j].""]["".$arr_competencia[$i].""]);
			$numdescriptoresxcomp[$j]=count($arr_conTotal["".$wformato.""]["".$arr_gcompetencia[$j].""]["".$arr_competencia[$i].""]);
		}
	}
	$q=	" 	SELECT ".$wbasedato."_000002.Fordes, Gcodes, Comdes, Desdes,Gcopes "
		."  FROM ".$wbasedato."_000006,".$wbasedato."_000002,".$wbasedato."_000003,".$wbasedato."_000004,".$wbasedato."_000005 "
		." 	WHERE Forfor= '".$wformato."' "
		."	  AND Forfor = Forcod "
		."	  AND Forgco = Gcocod "
		."	  AND Forcom = Comcod "
		."    AND Desest != 'off' "
		."    AND Comcod != 'off' "
		."    AND gcoest != 'off' "
		."	  AND ".$wbasedato."_000006.Fordes = Descod "
		."	ORDER BY forogc,Forgco,foroco,Forcom,Fordes,Forord";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$i = 0;
	$ig = 0;
	$ic = 0;

	$auxGcompetencia = "";
	$auxCompetencia = "";
	$auxDescriptor= "";

	// se llena los vectores: arr_gcompetencia, arr_competencia, arr_descriptor y arr_conTotal
	while ($row =mysql_fetch_array($res))
	{
		//echo "auxgcompetencia:  ".$auxGcompetencia."      array".$i.":  ".$arr_gcompetencia['Forgco'];
		if($auxGcompetencia != $row['Gcodes'] )
		{
			$Narr_gcompetencia[$ig][0] = $row['Gcodes'];

			$Narr_gcompetencia[$ig][1] = $row['Gcopes'];
			$ig++;
		}
		if($auxCompetencia != $row['Comdes'] )
		{

			$Narr_competencia[$ic] = $row['Comdes'];
			$ic++;

		}

		$Narr_descriptor[$i] = $row['Desdes'];
		$Narr_conTotal[$row['Fordes']][$row['Gcodes']][$row['Comdes']][] = $row['Desdes'];

		$auxGcompetencia=$row['Gcodes'];
		$auxCompetencia=$row['Comdes'];
		$auxDescriptor=$row['Desdes'];
		$i++;
		$nomformulario = $row['Fordes'];
	}

	$q=	"  SELECT  SUM(Evacal), Evafor, Forcom"
		."   FROM ".$wbasedato."_000006,".$wbasedato."_000007"
		." 	WHERE ".$wbasedato."_000006.id = Evafor "
		."    AND Evaevo = '".$wempleado."' "
		."    AND Evaevr = '".$wcalificador."' "
		."    AND Evaano= '".$wano."'"
		."    AND Evaper= '".$wperiodo."'"
		." GROUP BY foroco,Forcom ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);

	while($row =mysql_fetch_array($res))
	{
		$arr_calificacionesxcom[$row['Forcom']] = $row['SUM(Evacal)'] ;
	}


	$q=	"  SELECT  Evacal, Evafor"
		."   FROM ".$wbasedato."_000006,".$wbasedato."_000007"
		." 	WHERE ".$wbasedato."_000006.id = Evafor "
		."    AND Evaevo = '".$wempleado."' "
		."    AND Evaevr = '".$wcalificador."' "
		// ."    AND Evanup = '".$wnumprueba."' "
		// ."    AND Evaano= '".$wano."'"
		// ."    AND Evaper= '".$wperiodo."'"
		." ORDER BY forogc,Forgco,foroco,Forcom,Fordes,Forord";


	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);

	while($row =mysql_fetch_array($res))
	{
	 $arr_calificaciones[$row['Evafor']] = $row['Evacal'] ;
	}

	$q=	    "   SELECT  Ajeuco, Ajecco, Ajefor,Ideno1,Ideno2,Ideap1,Ideap2,Cconom,Fordes"
		   ."     FROM ".$wbasedato."_000008, costosyp_000005,".$wbasedato."_000002,talhuma_000013"
		   ."    WHERE Ajeucr ='".$wuse."'"
		   ."      AND Ajecco=Ccocod "
		   ."      AND Ideuse=Ajeuco "
		   ."      AND Ajefor=Forcod "
		   ."      AND Forabr='on' "
		   ."      AND ".$wbasedato."_000002.Forest='on' "
		   ." ORDER BY Cconom,Ideno1,Ideno2,Ideap1,Ideap2 ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrowcom = mysql_num_rows($res);

			$q= 	 "SELECT Fordes "
					."  FROM ".$wbasedato."_000002 "
					." WHERE Forcod ='".$wformato."'";
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$nombreformato=$row['Fordes'];

			$tipo='01';

			$q =" SELECT Mcaano, Mcaper, Ideno1, Ideno2, Ideap1, Ideap2,Mcaucr
					FROM ".$wbasedato."_000032, ".$wbasedato."_000009, talhuma_000013
				   WHERE Mcauco ='".$wempleado."'
					 AND Mcafor ='".$wformato."'
					 AND Mcaano = Perano
					 AND Mcaper = Perper
					 AND Perest = 'off'
					 AND Mcaucr = Ideuse
					 AND Perfor = '".$tipo."'
				ORDER BY Mcaano, Mcaper, Ideno1,Ideno2,Ideap1,Ideap2";


			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			echo 	"<br><table width='90%' class='encabezadoTabla' align='center'><tr><td>FORMATO: ".($nombreformato)."</td></tr></table>";

			echo 	"<table width='600' align='center'>";

			$i=0;
			$c=0;

			//While de grupo de competencias
			While($i < count($arr_gcompetencia))
			{

				echo"<tr><td><td></tr>
					<tr align='left'  class='encabezadoTabla'>
						<td colspan='3'><div align='center'><strong><font style='text-transform: uppercase;'>COMPETENCIAS ".($Narr_gcompetencia[$i][0])."</font></strong></div></td>
					</tr>
					<tr align='left'  class='fila2'>
						<td width='100' rowspan='".(($numcompetencias[$i] + $numdescriptores[$i]) + 1) ."'><div align='center'><strong>".$Narr_gcompetencia[$i][1]."%</strong></div></td>
						<td colspan='2' class='encabezadoTabla' nowrap='nowrap'><div align='center'><strong>NIVEL DE EJECUCI&Oacute;N</strong></div><input style='float : right' id='boton-".$wformato."-".$arr_gcompetencia[$i]."' type='button' value='Orden' onclick='darOrdenGcompetencia(this)'/></td>
					</tr>";


				$j=0;
				$q=	" 	SELECT  DISTINCT (Comcod),Comdes,Comsig "
						."  FROM ".$wbasedato."_000006,".$wbasedato."_000004 "
						." 	WHERE Forfor= '".$wformato."' AND Forgco = '".$arr_gcompetencia[$i]."'"
						."	  AND Forcom = Comcod "
						."    AND Comest != 'off' "
						."	ORDER BY  foroco,Comcod";

				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$numrowcom = mysql_num_rows($res);

				// While que establece las competencias con sus respectivos descriptores
				while ($row =mysql_fetch_array($res))
				{
						//Encabezado de la competencia
						echo"<tr style='Background: #999999; Color: #FFFFFF'  align='left'  class='fila2'>
						     <td align='justify' colspan='1' onClick='fnMostrar( this )'>";

						// div con el contenido de la competencia, se pone en div para actualizar y no recargar toda la pagina
						echo"<div id='divcom".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'><strong>".($row['Comdes']).": </strong>".($row['Comsig'])."</div>";

						// Ventana Modal que contiene la competencia y la operacionalizacion de esta.
						echo"<center><div class='fila2' align='middle' style='display:none;width:100%;cursor:default;'>";

						// Area de texto para la competencia
						echo "<b>Nombre de la competencia</b>";
						echo "<br><textarea  id='textcom".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'  rows='2'  cols='50'>".($row['Comdes'])."</textarea>";

						// Area de texto para el descriptor
						echo "<br><br><b>Operacionalizacion de la competencia</b>";
						echo "<br><textarea  id='textcomop".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'  rows='5'  cols='50'>".($row['Comsig'])."</textarea>";
						echo "<br><br><br><INPUT TYPE='button' value='Cerrar' onClick='guardacompetencia(\"".$row['Comcod']."\", \"textcom".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."\" , \"textcomop".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."\" , \"divcom".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."\") ; $.unblockUI();' style='width:100'><br><br>";
						echo "</div></center>
								</td>
								<td align='center' nowrap='nowrap'><input type='button' id='boton-".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'  value='Eliminar' onclick='EliminarCompetencia(this)' /><input type='button' id='boton-".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."'  value='Orden' onclick='Ordencompetencia(this)'/></td>
							 </tr>";
						//---------------------------

						$k=0;
						$controlTotal=1;


						$q=	" 	SELECT  Descod,Desdes,".$wbasedato."_000006.id "
							."    FROM ".$wbasedato."_000006,".$wbasedato."_000005"
							." 	 WHERE Forfor= '".$wformato."' AND Forgco = '".$arr_gcompetencia[$i]."' AND Forcom = '".$row['Comcod']."'"
							."	   AND Fordes = Descod "
							."     AND Desest != 'off' "
							."	 ORDER BY Forord,Descod";

						$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$numrow = mysql_num_rows($res1);

						//While de los descriptores
						while($row1 =mysql_fetch_array($res1))
						{
								//Estilo alternado de los tr
								if (is_int ($k/2))
								   {
									$wcf="fila1";  // color de fondo de la fila
								   }
								else
								   {
									$wcf="fila2"; // color de fondo de la fila
								   }
								echo"<tr align='left'  class='".$wcf."'>";
								//------------------------------------

								//si la evaluacion esta cerrada o no, Habilita los inputs o no
								if ($westado=='1')
								   {
									$habilitado="disabled";  // color de fondo de la fila
								   }
								else
								   {
									$habilitado=""; // color de fondo de la fila
								   }
								//------------------------------------

								//Si la calificacion del descripor es menor a dos pone en naranja el estilo de este
								//Tambien se guarda en un vector los compromisos que se deben pintar luego en la seccion de compromisos
								if($arr_calificaciones[$row1['id']]<= $wcalmal AND $arr_calificaciones[$row1['id']] > 0 )
								{
									$estilo= 'background-color:orange;';

									//vector compromisos [0][$c] id de la div que lo contendra;
									$vectorcompromisos[0][$c]=$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod'];
									//vector compromisos[1][$c] codigo del decriptor;
									$vectorcompromisos[1][$c]=($row1['Descod']);
									//vector compromisos [2][$c] descripcion del decriptor
									$vectorcompromisos[2][$c]=($row1['Desdes']);
									//vector compromisos [2][$c] descripcion de la competencia
									$vectorcompromisos[3][$c]=($row['Comdes']);

									$c++;
									//-------------------------------------
								}
								else
								{
									$estilo="";
								}
								//------------------------------------

								echo"<td align='justify' id='tdd".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' width='800' align='justify' onClick='ponercheck(\"".$row1['Descod']."\"); fnMostrar( this )' >";

								// div que contiene el descriptor, se hace para cambiarl solo ese elemento sin recurrir a cargar la pagina
								echo "<div id='divdes".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."'>".($row1['Desdes'])."</div>";
								echo "<center>";

								// div de la ventana modal
								echo "<div class='fila2' align='middle' style='display:none;width:100%;cursor:default;'>";

									$q = "   SELECT ".$wbasedato."_000002.Fordes "
									   . "     FROM ".$wbasedato."_000002, "
									   . "          ".$wbasedato."_000006  "
									   . "    WHERE  Forfor = Forcod     "
									   . "      AND  ".$wbasedato."_000006.Fordes = '".$row1['Descod']."' "
									   . "      AND  Forfor != '".$wformato."'"
									   . " ORDER BY   ".$wbasedato."_000002.Fordes ";

								$res2 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								$numfor2 = mysql_num_rows($res2);

								if ($numfor2>0)
								{


										echo "<br><table><tr class='encabezadoTabla' align='left'><td colspan='2'><b>Este descriptor hace parte tambien de los siguientes formatos<br>si es editado cambiara para todos ellos.<b></td></tr>";
										$n=1;
										while($row2 =mysql_fetch_array($res2))
										{
											echo "<tr align='left' class='fila1'>
											      <td width='10'>".$n."</td>
												  <td><b>
												  ".($row2['Fordes'])."</b>
												  </td>
												  </tr>";
											$n++;
										}

										echo"</table>";

								}
									// $qrespuestas = " SELECT Notcod , Notdes ,id,Notval"
												   // ."  FROM ".$wbasedato."_000047 "
												   // ." WHERE Notgru = '".$row1['Descod']."' ";

									// $resrespuestas = mysql_query($qrespuestas,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qrespuestas." - ".mysql_error());
									// $numresrespuestas = mysql_num_rows($resrespuestas);
									// if ($numresrespuestas>0)
									// {


										// $tableaux = "<table>";
										// $chequeado = '';
										// while($rowrespuestas = mysql_fetch_array($resrespuestas))
										// {
											// if($rowrespuestas['Notval'] == '1')
											// {
												// $chequeado = "checked='checked'";
											// }
											// $tableaux .= "<tr><td>".$rowrespuestas['Notcod']."</td><td><textarea  id='resp".$rowrespuestas['id']."-".$row1['Descod']."'  onblur='guardadrespuesta(\"".$row1['Descod']."\",\"".$rowrespuestas['Notcod']."\", \"resp".$rowrespuestas['id']."-".$row1['Descod']."\")' rows='2' cols='25'>".$rowrespuestas['Notdes']."</textarea></td><td><div id= 'checkboxpreguntas".$row1['Descod']."-".$rowrespuestas['Notcod']."' ></div></td></tr>";
											// $chequeado = '';
										// }
										// $tableaux .= "</table>";
									// }
								echo "<br><br><textarea  id='text".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."'  rows='5' onblur='guardadescriptor(\"".$row1['Descod']."\", this,\"divdes".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."\")' cols='50'>".($row1['Desdes'])."</textarea>";
						        echo "<br><br><div align='center' id='respuestasdepreguntas".$row1['Descod']."'></div><br><br><br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'><br><br>";
							    echo "</div></center>";
							    echo"</td>
									 <td align='center'><input id='boton-".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' type='button' value='Eliminar' onclick='EliminarDescriptor(this)'/><input id='boton-".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."' type='button' value='Orden' onclick='darOrden(this)'/></td>";
								//------------------------------------------------

								$vectorcampos= $vectorcampos."*".$wformato."-".$arr_gcompetencia[$i]."-".$row['Comcod']."-".$row1['Descod']."";


								echo "</tr>";


							$k++;
						}
						//----------------------------------------------

						$j++;
				}
				//---------------------------------

			   $total=$total + $totalgco;

				$i++;

			}
			echo"</table><br><br><br><br>";

		return;
}

if(isset($editardescriptores) && $editardescriptores == 'si')
{
    

	include_once("root/comun.php");
	


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q=  "   UPDATE ".$wbasedato."_000005 "
		."      SET	Desdes = '".$desdes." '"
        ."    WHERE Descod = ".$descod." ";

	$res =  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;

}

if(isset($editarrespuestas ) && $editarrespuestas == 'editarrespuestas')
{
	

	include_once("root/comun.php");
	


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q=  "   UPDATE ".$wbasedato."_000047 "
		."      SET	Notdes = '".utf8_decode($wcontenido)."'"
        ."    WHERE Notgru = '".$wcodigo."' "
		."      AND Notcod = '".$wcodigorespuesta."'" ;

	$res =  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;

}

if(isset($editarcompetencias) && $editarcompetencias == 'si')
{
    

	include_once("root/comun.php");
	


	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	$q=  "   UPDATE ".$wbasedato."_000004 "
		."      SET	Comdes = '".$descom."',"
		."          Comsig = '".$sigcom."' "
        ."    WHERE Comcod = ".$codcompetencia." ";

	$res =  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;

}

?>
<html>
<head>
<title>Creacion Formularios</title>

<!-- JQUERY para los tabs -->
<!--link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" /-->


<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<!-- Fin JQUERY para los tabs -->

<!-- Include de codigo javascript propio de mensajeria Kardex -->
<!-- <script type="text/javascript" src="../../../include/movhos/mensajeriaKardex.js"></script>-->

<script type="text/javascript">

function carganotas()
{

var empleado=document.getElementById('wemp_pmla').value;
var tipodescriptor=$('#tipodescriptor').val();
var selectcarganotas=$('#selectcarganotas').val();

var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&wgrupodescriptores='+tipodescriptor+'&selectcarganotas='+selectcarganotas;
	$.post(params, function(data) {
		  $('#contenedornotas').html(data);

	});
}

function crear_formula()
{

	$.post("creacionformularios.php",
		{
			consultaAjax:   	'',
			woperacion:     	'crearformulas',
			inicial:			'no',
			wtema:				$('#wtema').val(),
			wemp_pmla:			$('#wemp_pmla').val()

		}, function(data){
			$("#div_crearformulas").html(data);
		});


}

// funcion que analiza si aplica o no porcentaje , si aplica pone el campo habilitado , si no aplica lo pone deshabilitado y borra
// el valor que haya sido digitado
function analizaAplicaPorcentaje(campo)
{
	var elemento = jQuery(campo);
	var checkeado = 0;
	if( elemento.prop('checked') ) {
		alert("estoy chequeado");
		checkeado = 1;
		$("#campoporcientocompetencia").val("");
		$("#campoporcientocompetencia").prop("disabled" , true);

	}
	else
	{
		alert("no estoy chequeado");
		$("#campoporcientocompetencia").prop("disabled" , false);
	}


}

function generarRespuestas()
{
var empleado=document.getElementById('wemp_pmla').value;
var tipodescriptor=$('#tipodescriptor').val();
var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&wagregarRespuestas=si';
	$.post(params, function(data) {
		  $('#respuestas').html('');
		  $('#grupodescriptores').html(data);

	});

}

function cargagrupodescriptores()
{

$('#grupodescriptores').html('');

var empleado=document.getElementById('wemp_pmla').value;
var tipodescriptor=$('#tipodescriptor').val();

if(tipodescriptor=='')
{

}
else
{

	if(tipodescriptor == '06')
	{

		var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&wdescriptortipo='+tipodescriptor;
		$.post(params, function(data) {
			  $('#Notas').html('');
			  $('#grupodescriptores').html(data);

		});
	}
	else
	{

	 // si es de tipo texto no deberia pedir select
	 //- 03 tipo texto.
	 //- 07 tipo fecha con cierre de evaluacion.
	 //- 08 tipo fecha sin cierre de evaluacion.
	 //- 09 numerico no tenido en cuenta en informes.
	 if (tipodescriptor == '03' ||  tipodescriptor == '07' ||  tipodescriptor == '08' ||  tipodescriptor == '09' )
	 {

	 }
	 else
	 {

		var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&wgrupodescriptores='+tipodescriptor;
		$.post(params, function(data) {
			  $('#Notas').html('');
			  $('#grupodescriptores').html(data);

		});
	 }

	}
}

}
function borrartextareas()
{

$('#grupodescriptores').html('');
$('#tipodescriptor option:first').attr('selected', true);

}
function grabarDescriptores()
{
    var empleado=document.getElementById('wemp_pmla').value;
	var cont1 = 0;
	var vector = '';

	$('input[id^=descriptores2]').each(function(){
		if($(this).is(':checked'))
	   {
			vector = vector+'-'+$(this).val();
			//alert (vector);
	   }
	});

	var formato = document.getElementById("wformato").value
    formato = formato.split("-");

	var gcompetencia = document.getElementById("wgcompetencia").value
    gcompetencia = gcompetencia.split("-");

	var competencia = document.getElementById("wcompetencia").value
    competencia = competencia.split("-");

	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabardescriptores=si&graba=si&wformato='+formato[1]+'&wgcompetencia='+gcompetencia[1]+'&wcompetencia='+competencia[1]+'&wdescriptor='+vector;
	$.post(params, function(data) {
	$("#formato").html(data);
	});
	$("#tabledescriptores").remove();
	instrucciones=$("<br><br><br><table id='tableinstrucciones' style='border:orange solid 1px;' align='center' width='85%' ><tr style='background:orange;' ><td><b>Nota<b></td></tr><tr><td>&nbsp;</td></tr><tr><td>Para agregar otro grupo de competencias de click en el checkbox Grupo de competencias</td></tr><tr><td>&nbsp;</td></tr><tr><td>Para agregar otra competencias de click en la casilla Competencias</td></tr></table>");
	$("#instrucciones").html(instrucciones);

}

function removerFilas (nombre,elemento,numerodeindices,indice,descriptor,tipoevaluacion)
{


var empleado=document.getElementById('wemp_pmla').value;

switch (nombre) {
    case 'temas':
       var aux2='Temas';
	   document.getElementById("wtemaevaluacion").value=elemento.id+'-'+tipoevaluacion;
	   var espacios = '';
       break
	case 'niveles':
       var aux2='Nivel';
	   document.getElementById("wnivel").value=elemento.id;
	   var espacios = '&nbsp;';
       break
    case 'formatos':
       var aux2='Formato';
	   document.getElementById("wformato").value=elemento.id;
	   var espacios = '&nbsp;&nbsp;';
       break
    case 'gcompetencias':
       var aux2='Grupo de Competencias';
	   document.getElementById("wgcompetencia").value=elemento.id;
	   var espacios = '&nbsp;&nbsp;&nbsp;&nbsp;';
       break
	case 'competencias':
       var aux2='Competencias';
	   document.getElementById("wcompetencia").value=elemento.id;
	    var espacios = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
       break
    default:

}

if ( elemento=='' || document.getElementById(elemento.id).checked==true )
{
	// Elimina filas


	picker = $("<div id='table"+nombre+"' width='100%' class='fila1'>"+espacios+"<input type='checkbox'  id='"+nombre+"' onclick='despleganivel(this);' checked />"+aux2+": <b> "+descriptor+"</b></div>");
	//$("#"+nombre).hide(300);

	$("#"+nombre).show(300);
	$('#'+nombre).html(picker);



}
if(nombre=='inicio')
{
 	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&inicio=si';
	$.post(params, function(data) {
		  $('#temas').html(data);


	});

}


if(nombre=='temas')
{

	var tema = document.getElementById("wtemaevaluacion").value
    tema = tema.split("-");

	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&niveles=si&wtemaevaluacion='+tema[1];
	$.post(params, function(data) {
		  $('#niveles').append(data);


	});
}


if(nombre=='niveles')
{

	var nivel = document.getElementById("wnivel").value
    nivel = nivel.split("-");

	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&formatos=si&wnivel='+nivel[1];
	$.post(params, function(data) {
		  $('#formatos').append(data);


	});
}

if(nombre=='formatos')
{
	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&gcompetencias=si';
	$.post(params, function(data) {
		  $('#gcompetencias').append(data);


	});
	var formato = elemento.id
    formato = formato.split("-");

	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabardescriptores=si&graba=no&wformato='+formato[1];
	$.post(params, function(data) {
	$("#formato").html(data);
	});
}

if(nombre=='gcompetencias')
{
    var gcompetencia = document.getElementById("wgcompetencia").value
    gcompetencia = gcompetencia.split("-");
	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&competencias=si&wgcompetencia='+gcompetencia[1];
	$.post(params, function(data) {
		  $('#competencias').append(data);


	});
}

if(nombre=='competencias')
{
	var competencia = document.getElementById("wcompetencia").value
    competencia = competencia.split("-");
	var formato = document.getElementById("wformato").value
    formato = formato.split("-");
	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&descriptores=si&wcompetencia='+competencia[1]+'&wformato='+formato[1];
	$.post(params, function(data) {
		  $('#descriptores').append(data);


	});
}

}

function despleganivel (elemento)
{
var nombre = elemento.id;
var empleado=document.getElementById('wemp_pmla').value;

if(nombre=='temas')
{


	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&inicio=si';
	$.post(params, function(data) {
		  $('#tableniveles').remove();
		  //$('#agregarniveles').remove();
		  $('#tabledescriptores').remove();
		 // $('#agregardescriptores').remove();
		  $('#tablecompetencias').remove();
		  //$('#agregarcompetencias').remove();
		  $('#tablegcompetencias').remove();
		  $('#tableformatos').remove();
		  //$('#agregarformato').remove();
		  $('#temas').hide();
		  $('#temas').html(data);
		  $('#temas').show(500);
		  $('#tableinstrucciones').remove();


	});
}


if(nombre=='niveles')
{


	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&niveles=si';
	$.post(params, function(data) {
		  $('#tabledescriptores').remove();
		  //$('#agregardescriptores').remove();
		  $('#tablecompetencias').remove();
		  //$('#agregarcompetencias').remove();
		  $('#tablegcompetencias').remove();
		  //$('#agregargcompetencias').remove();
		  $('#tableformatos').remove();
		  //$('#agregarformato').remove();
		  $('#niveles').hide();
		  $('#niveles').html(data);
		  $('#niveles').show(500);
		  $('#tableinstrucciones').remove();


	});
}

if(nombre=='formatos')
{

	var nivel = document.getElementById("wnivel").value
    nivel = nivel.split("-");

	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&formatos=si&wnivel='+nivel[1];
	$.post(params, function(data) {
		  $('#tabledescriptores').remove();
		  //$('#agregardescriptores').remove();
		  $('#tablecompetencias').remove();
		 // $('#agregarcompetencias').remove();
		  $('#tablegcompetencias').remove();
		  //$('#agregargcompetencias').remove();
		  $('#formatos').hide();
		  $('#formatos').html(data);
		  $('#formatos').show(500);
		  $('#tableinstrucciones').remove();


	});
}

if(nombre=='gcompetencias')
{
	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&gcompetencias=si';
	$.post(params, function(data) {
		  $('#tabledescriptores').remove();
		  //$('#agregardescriptores').remove();
		  $('#tablecompetencias').remove();
		  //$('#agregarcompetencias').remove();
		  $('#tableformatos').remove();
		  //$('#agregarformato').remove();
		 // $('#agregargcompetencias').remove();
		  $('#gcompetencias').hide();
		  $('#gcompetencias').html(data);
		  $('#gcompetencias').show(500);
		  $('#tableinstrucciones').remove();

	});
}

if(nombre=='competencias')
{
    var gcompetencia = document.getElementById("wgcompetencia").value
    gcompetencia = gcompetencia.split("-");
	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&competencias=si&wgcompetencia='+gcompetencia[1];
	$.post(params, function(data) {
		  //$('#agregardescriptores').remove();
		  $('#tabledescriptores').remove();
		  $('#competencias').hide();
		  $('#competencias').html(data);
		  $('#competencias').show(500);
		  $('#tableinstrucciones').remove();


	});
}

}

//nombreUl = 	nombre del elemento ul, se utiliza para saber si existe y asi saber si se crea o se adiciona
//				este elemento (parte derecha de la pantalla)
function EliminarCompetencia(elemento)
{
   if(confirm("Realmente quiere eliminar esta competencia de este formato?"))
   {
    var empleado=document.getElementById('wemp_pmla').value;
	var formato = elemento.id.split("-");
	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabardescriptores=si&graba=eliminacompetencia&wformato='+formato[1]+'&wgcompetencia='+formato[2]+'&wcompetencia='+formato[3];


	$.post(params, function(data) {
	$("#formato").html(data);
	});
   }
}

function cambiarokpregunta (numpregunta,codpregunta,elemento){
elemento = jQuery(elemento);

$('input[name=respuestas]').attr('checked', false);
elemento.attr('checked', true);

	$.post("creacionformularios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				woperacion		: 'cambiarokpregunta',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wnpregunta		: numpregunta,
				wcodpregunta	: codpregunta

			});


}

function EliminarDescriptor(elemento)
{
    if(confirm("Realmente quiere eliminar el descriptor de este formato?"))
	{
		var empleado=document.getElementById('wemp_pmla').value;
		var formato = elemento.id.split("-");
		var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabardescriptores=si&graba=elimina&wformato='+formato[1]+'&wgcompetencia='+formato[2]+'&wcompetencia='+formato[3]+'&wdescriptor='+formato[4];



		//alert (elemento.id);
		$.post(params, function(data) {
		$("#formato").html(data);
		});
	}
}

function cancelar ()
{

	$("#tabledescriptores").remove();
    instrucciones=$("<br><br><br><table id='tableinstrucciones' style='border:orange solid 1px;' align='center' width='85%' ><tr style='background:orange;' ><td><b>Nota<b></td></tr><tr><td>&nbsp;</td></tr><tr><td>Para agregar otro grupo de competencias de click en el checkbox Grupo de competencias</td></tr><tr><td>&nbsp;</td></tr><tr><td>Para agregar otra competencias de click en la casilla Competencias</td></tr></table>");
    //instrucciones="hola";
	$("#instrucciones").html(instrucciones);

}



function fnMostrar( celda ){
		if( $("div", celda ).eq(1) ){

			$.blockUI({ message: $("div", celda ).eq(1),
							css: { left: ( $(window).width() - 600 )/2 +'px',
								    top: '200px',
								  width: '600px'
								 }
					  });

		}
	}

function fnMostrar2( celda ){
		if( $('#'+celda ) ){

			$.blockUI({ message: $('#'+celda ),
							css: { left: ( $(window).width() - 600 )/2 +'px',
								    top: '200px',
								  width: '600px'
								 }
					  });

		}

		if(celda =='agregardescriptores')
		{
			$("#grupodescriptores").html('');
			$("#contenedornotas").html('');
		}
	}

function Ordencompetencia (elemento)
{
	celda = 'Darordencompetencia';
	fnMostrar2(celda);
	var codigo = elemento.id;

	$.post("creacionformularios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeOrdencompetencia',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				welemento		: codigo
			}
			, function(data) {
			$('#txt_ordencompetencia').val(data);
			$('#txt_ordencompetencia_oculto').val(codigo);
			});
}
function darOrdenGcompetencia (elemento)
{
	celda = 'Darordengcompetencia';
	fnMostrar2(celda);
	var codigo = elemento.id;

	$.post("creacionformularios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeOrdenGcompetencia',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				welemento		: codigo
			}
			, function(data) {
			$('#txt_ordengcompetencia').val(data);
			$('#txt_ordengcompetencia_oculto').val(codigo);
			});
}

function ponercheck(codigocompetencia)
{
  $.post("creacionformularios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				woperacion		: 'ponercheck',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wcodigocompetencia	: codigocompetencia
			}
			, function(data) {
				$('#respuestasdepreguntas'+codigocompetencia).html(data);
			});

}
function darOrden (elemento)
{

	celda = 'Darorden';
	fnMostrar2(celda);
	var codigo = elemento.id;

	$.post("creacionformularios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeOrden',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				welemento		: codigo
			}
			, function(data) {
			$('#txt_orden').val(data);
			$('#txt_orden_oculto').val(codigo);
			});
}

function Grabarorden()
{


	var codigo = $('#txt_orden_oculto').val();
	dato = $('#txt_orden').val();

	$.post("creacionformularios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'asignaOrden',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wdato			: dato,
				welemento		: codigo
			}
			, function(data) {
			empleado=$('#wemp_pmla').val();
			codigo = codigo.split('-');
			var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabardescriptores=si&graba=no&wformato='+codigo[1];
			$.post(params, function(data) {
			$("#formato").html(data);
			});
			});

}


function Grabarordencompetencia()
{
	var codigo = $('#txt_ordencompetencia_oculto').val();
	dato = $('#txt_ordencompetencia').val();

	$.post("creacionformularios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'asignaOrdenCompetencia',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wdato			: dato,
				welemento		: codigo
			}
			, function(data) {
			empleado=$('#wemp_pmla').val();
			codigo = codigo.split('-');
			var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabardescriptores=si&graba=no&wformato='+codigo[1];
			$.post(params, function(data) {
			$("#formato").html(data);
			});
			});

}

function GrabarordenGcompetencia()
{
	var codigo = $('#txt_ordengcompetencia_oculto').val();
	dato = $('#txt_ordengcompetencia').val();

	$.post("creacionformularios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'asignaOrdenGCompetencia',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wdato			: dato,
				welemento		: codigo
			}
			, function(data) {
			empleado=$('#wemp_pmla').val();
			codigo = codigo.split('-');
			var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabardescriptores=si&graba=no&wformato='+codigo[1];
			$.post(params, function(data) {
			$("#formato").html(data);
			});
			});

}

function desactivardescriptores(codigo,td)
{

	if(confirm("Realmente quiere eliminar la competencia?"))
	{
		$.post("creacionformularios.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'eliminardescriptor',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wcodigo			: codigo
				}
				, function(data) {
					$('#'+td).remove();
				});
	}

}

function desactivarcompetencias(codigo,td)
{

	if(confirm("Realmente quiere eliminar la competencia?"))
	{
		$.post("creacionformularios.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'eliminarcompetencia',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wcodigo			: codigo
				}
				, function(data) {
					$('#'+td).remove();
				});
	}

}

function desactivargcompetencia(codigo,td)
{

	if(confirm("Realmente quiere eliminar el grupo de competencia?"))
	{
		$.post("creacionformularios.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'eliminargcompetencia',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wcodigo			: codigo
				}
				, function(data) {
					$('#'+td).remove();
				});
	}

}

function guardadescriptor (codigo, descripcion,td)
{

	var empleado=document.getElementById('wemp_pmla').value;
	var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&editardescriptores=si&desdes='+descripcion.value+'&descod='+codigo;
	$.post(params, function(data) {
	document.getElementById(td).innerHTML =(descripcion.value);
	});

}

function guardadrespuesta (codigo,codigorespuesta,textarea)
{
	$.post("creacionformularios.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					editarrespuestas : 'editarrespuestas',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wcodigo			: codigo,
					wcodigorespuesta : codigorespuesta,
					wcontenido		: $('#'+textarea).val()
				});



}

function guardacompetencia ( codigocompetencia, descripcion, significado, td)
{

 var empleado=document.getElementById('wemp_pmla').value;
 var descompetencia =document.getElementById(descripcion).value;
 var sigcompetencia =document.getElementById(significado).value;
 var contenido= '<b>'+descompetencia+': </b>'+sigcompetencia;


 var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&editarcompetencias=si&descom='+descompetencia+'&codcompetencia='+codigocompetencia+'&sigcom='+sigcompetencia;
	$.post(params, function(data) {
	document.getElementById(td).innerHTML =contenido;
	});
}

function grabanuevoelemento(elemento,tiponuevoelemento,nombrenuevoelemento,campoporcientocompetencia)
{
	var empleado=document.getElementById('wemp_pmla').value;
	var nombre = document.getElementById(nombrenuevoelemento).value;
	var permitirGuardar = true;

	if(elemento =='tema')
	{
		$('#tableagregartema .campoObligatorio').removeClass('campoObligatorio');
		$("#tableagregartema").find("[tipo=obligatorio]").each(function(){
		if($(this).val() == '')
		{
			$(this).addClass('campoObligatorio');
			permitirGuardar = false;
			mensaje = 'Faltan campos por llenar';
		}
		});
		if( !permitirGuardar)
		{
			return;
		}
	}

	if(elemento =='nivel')
	{
		$('#tableagregarnivel .campoObligatorio').removeClass('campoObligatorio');


		$("#tableagregarnivel").find("[tipo=obligatorio]").each(function(){
		if($(this).val() == '')
		{
			$(this).addClass('campoObligatorio');
			permitirGuardar = false;
			mensaje = 'Faltan campos por llenar';
		}
		});
		if( !permitirGuardar)
		{
			return;
		}
	}

	if(elemento =='formato')
	{
		$('#tableagregarformato .campoObligatorio').removeClass('campoObligatorio');


		$("#tableagregarformato").find("[tipo=obligatorio]").each(function(){
		if($(this).val() == '')
		{
			$(this).addClass('campoObligatorio');
			permitirGuardar = false;
			mensaje = 'Faltan campos por llenar';
		}
		});
		if( !permitirGuardar)
		{
			return;
		}
	}

	if(elemento =='gcompetencia')
	{
		$('#tableagregargcompetencia .campoObligatorio').removeClass('campoObligatorio');


		$("#tableagregargcompetencia").find("[tipo=obligatorio]").each(function(){
		if($(this).val() == '')
		{
			$(this).addClass('campoObligatorio');
			permitirGuardar = false;
			mensaje = 'Faltan campos por llenar';
		}
		});
		if( !permitirGuardar)
		{
			return;
		}
	}

	if(elemento =='competencia')
	{
		$('#tableagregarcompetencia .campoObligatorio').removeClass('campoObligatorio');


		$("#tableagregarcompetencia").find("[tipo=obligatorio]").each(function(){
		if($(this).val() == '')
		{
			$(this).addClass('campoObligatorio');
			permitirGuardar = false;
			mensaje = 'Faltan campos por llenar';
		}
		});
		if( !permitirGuardar)
		{
			return;
		}
	}

	if(elemento =='descriptor')
	{
		$('#tableagregardescriptor .campoObligatorio').removeClass('campoObligatorio');


		$("#tableagregardescriptor").find("[tipo=obligatorio]").each(function(){
		if($(this).val() == '')
		{
			$(this).addClass('campoObligatorio');
			permitirGuardar = false;
			mensaje = 'Faltan campos por llenar';
		}
		});
		if( !permitirGuardar)
		{
			return;
		}
		
		//alert($("#nombrenuevoelementodescriptor").val());
	}



	switch (elemento)
	{
    case 'tema':
		var tipo = document.getElementById(tiponuevoelemento).value;
		var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabanuevoelemento=si&elemento='+elemento+'&descripcionelemento='+nombre+'&tipo='+tipo+'&wtipocontrato='+$('#stipocontrato').val()+'&wperiodicidad='+$('#periodicidad').val()+'&wmaxperiodicidad='+$('#maxperiodicidad').val()+'&wtemsiguiente='+$('#temsiguiente').val()+'&wtipocontrato='+$('#stipocontrato').val();
		$.post(params, function(data) {
		$("#tabla_inicio").append(data);
		document.getElementById(nombrenuevoelemento).value = '';
		});

       break
	case 'nivel':
		var nombre = document.getElementById(nombrenuevoelemento).value;
		var tipo = document.getElementById('wtemaevaluacion').value;
		tipo= tipo.split('-');
		tipo = tipo[1];
		var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabanuevoelemento=si&elemento='+elemento+'&descripcionelemento='+nombre+'&tipo='+tipo;
		$.post(params, function(data) {
		//alert (data);
		$("#tableniveles").append(data);
		document.getElementById(nombrenuevoelemento).value = '';
		});

       break

	case 'formato':

	    var nombre = document.getElementById(nombrenuevoelemento).value;
		var tipo = document.getElementById('wnivel').value;
		tipo= tipo.split('-');
		tipo = tipo[1];
		var temaevaluacion = document.getElementById('wtemaevaluacion').value;
		temaevaluacion= temaevaluacion.split('-');
		temaevaluacion = temaevaluacion[1];
		var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabanuevoelemento=si&elemento='+elemento+'&descripcionelemento='+nombre+'&tipo='+tipo+'&temaevaluacion='+temaevaluacion;
		$.post(params, function(data) {
		$("#tableformatos").append(data);
		document.getElementById(nombrenuevoelemento).value = '';
		});
       break
    case 'gcompetencia':

		porcentaje = document.getElementById(campoporcientocompetencia).value;
		var waplica = 'on';
		if( $("#noaplicaporcentaje").prop('checked') ) {waplica = 'off';}
		var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabanuevoelemento=si&elemento='+elemento+'&descripcionelemento='+nombre+'&wgporcentaje='+porcentaje+'&waplica='+waplica;
		$.post(params, function(data) {
		$("#tablegcompetencias").append(data);
		document.getElementById(nombrenuevoelemento).value = '';
		});
       break
    case 'competencia':
		var nombre = document.getElementById(nombrenuevoelemento).value;
		var tipo = document.getElementById('wgcompetencia').value;
		tipo= tipo.split('-');
		tipo = tipo[1];

		var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabanuevoelemento=si&elemento='+elemento+'&descripcionelemento='+nombre+'&tipo='+tipo;
		$.post(params, function(data) {
		$("#tablecompetencias").append(data);
		document.getElementById(nombrenuevoelemento).value = '';
		});
       break
    case 'descriptor':
		var nombre = document.getElementById(nombrenuevoelemento).value;
		var tipodescriptor = document.getElementById(tiponuevoelemento).value;
		var grupodescriptor = $('#selectcarganotas').val();

		var tipo = document.getElementById('wcompetencia').value;

		tipo= tipo.split('-');
		tipo = tipo[1];
		if(tipodescriptor=='06')
		{
			var j=1;
			var respuestas='';
			var nomenclatura='';
			var correcto='';
			var separado='**||';
			var validacioncorrecta = 'no';
			var validacionrespuesta = 'no';

			while(j <=4 )
			{

				nomenclatura = nomenclatura + separado + $('#nomemclatura-'+j).text();
				if($('#checkrespuesta-'+j).attr('checked')=='checked')
				{
				correcto1='1';
				validacioncorrecta='si';

				}
				else
				{
				correcto1='0';
				}
				correcto = correcto + separado +correcto1 ;

				if($('#textarearespuestas-'+j).val().length != 0 )
				{
				 validacionrespuesta ='si';
				}
				respuestas = respuestas + separado + $('#textarearespuestas-'+j).val();

				separado='||';

				j++;
			}
			respuestas = respuestas.replace('**||', '');
			alert(respuestas);
			nomenclatura = nomenclatura.replace('**||', '');
			correcto = correcto.replace('**||', '');

			if(validacionrespuesta=='no')
			{
				alert('No puede grabar sin llenar ninguna respuesta');
			}

			if(validacioncorrecta=='no')
			{
				alert('Esta grabando sin elegir una respuesta como correcta');
			}

			var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabanuevoelemento=si&elemento='+elemento+'&tipo='+tipo+'&wtipodescriptor='+tipodescriptor+'&wgrupodescriptor='+grupodescriptor+'&wrespuestas='+respuestas+'&wcorrecto='+correcto+'&wnomenclatura='+nomenclatura;

			$.post(params,{descripcionelemento : nombre}, function(data) {
			$("#tabledescriptores").append(data);
			document.getElementById(nombrenuevoelemento).value = '';
			});
		}
		else
		{
			var params = 'creacionformularios.php?consultaAjax=&wtema='+$('#wtema').val()+'&wemp_pmla='+empleado+'&grabanuevoelemento=si&elemento='+elemento+'&tipo='+tipo+'&wtipodescriptor='+tipodescriptor+'&wgrupodescriptor='+grupodescriptor;
			$.post(params, {descripcionelemento : nombre}, function(data) {
			$("#tabledescriptores").append(data);
			document.getElementById(nombrenuevoelemento).value = '';
			});
		}
       break
	 default:

	}

	$.unblockUI();
	borrartextareas();

}
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
	.campoObligatorio{
	border-style:solid;
	border-color:red;
	border-width:1px;
	}
}
</style>
</head>

<body>

<?php
/*BS'D
 * CONSULTA Y GENERACION DE KARDEX
 * Autor:

 */
$usuarioValidado = true;
$wactualiz = " 1.0 Noviembre 4 de 2011";

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else
{
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}
/*****************************
 * INCLUDES
 ****************************/




include_once("root/comun.php");

/****************************************************************************************

 ****************************************************************************************/

    $inicio='inicio';
	//----------------------------------------------
	$q = "SELECT Tipcod, Tipdes "
		."  FROM root_000084 ";

    $res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

    $wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

	echo "<div id='agregartema' class='fila2' align='middle'  style='display:none;width:100%;cursor:default' >";
	echo "<br><br>
			<table id='tableagregartema' align= 'center' style='border:#2A5DB0 1px solid'>
				<tr class='encabezadoTabla'>
					<td align='Center' colspan='2'>AGREGAR NUEVO TEMA
					</td>
				</tr>
				<tr align='Left'>
					<td>Nombre:
					</td>
					<td>
						<input type='text' size='40' id='nombrenuevotema' tipo='obligatorio'  />
					</td>
				</tr>
				<tr class='fila1' align='Left' >
					<td>Tipo:
					</td>
					<td><select  name='temanuevo' id='temanuevo'  >";

	while ($row =mysql_fetch_array($res))
	{
				echo"<option value='".$row['Tipcod']."'>".$row['Tipcod']." - ".$row['Tipdes']."</option>";
	}
	echo "</select></td>
				</tr>";
	echo "<tr>";
		echo "<td>Periodicidad:</td>";
		echo "<td>";
		echo "<Select id='periodicidad'>";
		echo "<option value='0'>Sin periodicidad</option>";
		for ( $i=1 ; $i<=12; $i++)
		{
			echo "<option value='".$i."'>cada ".$i." mes</option>";
		}
		echo "</select>";
		echo "</td>";
	echo "</tr>";

	echo "<tr class='fila1'>";
	echo "<td>Maximo de evaluaciones</td>";
	echo "<td>";
	echo "<select id='maxperiodicidad'>";
	echo "<option value='0'>Sin limite</option>";
	for( $i=1 ; $i<=12; $i++)
	{
			echo "<option value='".$i."'>".$i." </option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";

	$querytem = "SELECT Forcod,Fordes "
				." FROM ".$wbasedato."_000042 ";

	$res =  mysql_query($querytem,$conex) or die ("Error 3: ".mysql_errno()." - en el query que trae temas: ".$querytem." - ".mysql_error());
	echo "<tr>";
	echo "<td>Tema siguiente</td>";
	echo "<td>";
	echo "<select id='temsiguiente'>";
	echo "<option value='0'>Sin tema siguiente</option>";
	while ($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Forcod']."'>".$row['Forcod']."-".$row['Fordes']."</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	$vectipo[1]="indefinido";
	$vectipo[2]="definido";
	echo "<tr class='fila1'>";
	echo "<td>Tipo de Contrato:</td>";
	echo "<td>";
	echo "<select id='stipocontrato'>";
	echo "<option value='0'>no aplica</option>";
	for( $i=1 ; $i<=2; $i++)
	{
		echo "<option value='".$i."'>".$i."-".$vectipo[$i]." </option>";
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";

	echo"<tr class='fila2' align='center' >
					<td colspan='2' align='center' >
						<INPUT TYPE='button' value='Grabar' onClick='grabanuevoelemento(\"tema\",\"temanuevo\",\"nombrenuevotema\")' style='width:100'>
						<INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'>
					</td>
					<td>
				</tr>
		</table>";

		echo "<br><br><div id='tema_mensaje_alerta'></div></div>";
   //---------------------------------------------------

	echo "<div id='agregarnivel'  class='fila2' align='middle' style='display:none;width:100%;cursor:default;' >";
	echo "<br><br>
				<table id='tableagregarnivel' style='border:#2A5DB0 1px solid' >
					<tr>
						<td class='encabezadoTabla' align='center' colspan='2' >AGREGAR NUEVO NIVEL
						</td>
					</tr>
					<tr class='fila1'>
						<td><b>Nombre:<b>
						</td>
						<td><input size = '45' type='text' id='nombrenuevoelementonivel' tipo='obligatorio' />
						</td>
					</tr>
					<tr class='fila2'>
						<td colspan='2' align='center'>
							<INPUT TYPE='button' value='Grabar' onClick='grabanuevoelemento(\"nivel\",\"elementonuevo\",\"nombrenuevoelementonivel\")' style='width:100'/>
							<INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100' />
						</td>
					</tr>
				</table><br><br></div>";
    //-----------------------------------------

	echo "<div id='agregarformato' class='fila2' align='middle' style='display:none;width:100%;cursor:default;' >";
	echo "<br><br>
			<table  id='tableagregarformato' style='border:#2A5DB0 1px solid' >
				<tr align='center' class='encabezadoTabla'>
					<td  colspan='2'>AGREGAR NUEVO FORMATO
					</td>
				</tr>
				<tr class='fila1'>
					<td>Nombre:</td>
					<td><input type='text' size='45' id='nombrenuevoelementoformato' tipo='obligatorio'/>
					</td>
				</tr>";



			echo "<tr align='center' class='fila2' >
					<td colspan='2'>
						<INPUT TYPE='button' value='Grabar' onClick='grabanuevoelemento(\"formato\",\"elementonuevo\",\"nombrenuevoelementoformato\")' style='width:100'/>
						<INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'/></td>
					<td>
				  </tr>
			</table>
		 <br><br>
		</div>";
	//------------------------------------------------
	//------------------------------------------------
	echo "<div id='agregarcompetencia' class='fila2' align='middle' style='display:none;width:100%;cursor:default;' >";
	echo "<br><br>
		<table   id='tableagregarcompetencia' style='border:#2A5DB0 1px solid'>
			<tr class='encabezadoTabla' align='center'>
			<td  colspan='2'>Agregar Competencia
			</td>
			</tr>
			<tr>
			<td>Nombre:</td>
			<td><input  SIZE='50' type='text' id='nombrenuevoelementoCompetencia' tipo='obligatorio' />
			</td>
			</tr>";
	echo "<tr align='center' >
			<td colspan='2'>
				<INPUT TYPE='button' value='Grabar' onClick='grabanuevoelemento(\"competencia\",\"elementonuevo\",\"nombrenuevoelementoCompetencia\")' style='width:100' />
				<INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100' />
			</td>
		</tr>
		</table><br><br>
	</div>";
    //----------------------------------------
	//-- Div para agregar un grupo de competencias nueva.
	echo "<div id='agregargcompetencia' class='fila2' align='middle' style='display:none;width:100%;cursor:default;' >";
	echo "<br><br>
			<table id='tableagregargcompetencia' style='border:#2A5DB0 1px solid'>
				<tr>
					<td align='center' colspan='2' class='encabezadoTabla' >AGREGAR GRUPO DE COMPETENCIA
					</td>
				</tr>
				<tr class='fila1'>
					<td>Nombre:</td>
					<td><input type='text' size= '45' id='nombrenuevoelementogcompetencia' tipo='obligatorio' />
				    </td>
				</tr>
				<tr class='fila1'>
					<td>Porcentaje</td>
					<td><table><tr><td><input type='text' size= '10' id='campoporcientocompetencia' /></td>
				    <td><div id='divnoaplicaporcentaje'><input type='checkbox' name='noaplicaporcentaje' onclick='analizaAplicaPorcentaje(this)' id='noaplicaporcentaje' value='Bike'>No aplica<br><div></td></tr></table></td>
				</tr>";
	echo "<tr class='fila2' align='center'>
			<td colspan='2'   align='center' >
				<INPUT TYPE='button' value='Grabar' onClick='grabanuevoelemento(\"gcompetencia\",\"elementonuevo\",\"nombrenuevoelementogcompetencia\",\"campoporcientocompetencia\")' style='width:100' />
				<INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100' />
			</td>
		  </tr>
		</table>
		 <br><br>
		</div>";
    //---------------------
	echo "<div id='agregardescriptores' class='fila2' align='middle' style='display:none;width:100%;cursor:default;' >";
	echo "<br><br>
		<table id='tableagregardescriptor' width='400' style='border:#2A5DB0 1px solid'>
			<tr align='center' class='encabezadoTabla' >
				<td colspan='2'>AGREGAR DESCRIPTOR
				</td>
			</tr>
			<tr align='center'>
				<td>Descriptor:
				</td>
			</tr>
			<tr>
				<td><textarea rows='5'  cols='70' id='nombrenuevoelementodescriptor' tipo='obligatorio'></textarea>
				</td>
			</tr>";

	// En la tabla root 83 se encuentran los tipos de descriptores que hay
	//-- 01-- Numerico
	//-- 02-- Booleano
	//-- 03-- Texto
	//-- 04-- Escala
	//-- 05-- Select
	//-- 06-- Seleccion multiple
	//-- 07-- Campo fecha con cierre evaluacion.
	//-- 08-- campo fecha
	//-- 09-- valor no tenido encuenta para informes
	$q =  " SELECT * "
		 ."	  FROM  root_000083 " ;

	$res =  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo	"<br><tr class='fila1' align='Left' >
					<td>Tipo:
					<select  name='tipodescriptor' id='tipodescriptor'  tipo='obligatorio'  onchange='cargagrupodescriptores()'>";
					echo"<option value=''>Seleccionar</option>";
					while ($row =mysql_fetch_array($res))
					{
								echo"<option value='".$row['Tdecod']."'>".$row['Tdecod']." - ".$row['Tdedes']."</option>";
					}

	echo "</select></td>
				</tr>";
	echo "<tr><td><div id='grupodescriptores'></div></td></tr>";
	echo "<tr><td><div id='contenedornotas'></div></td></tr>";
	echo "  <tr align='center' >
				<td><INPUT TYPE='button' value='Grabar' onClick='grabanuevoelemento(\"descriptor\",\"tipodescriptor\",\"nombrenuevoelementodescriptor\",\"tipodescriptor\")' style='width:100'/><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();borrartextareas();' style='width:100' />
				</td>
			</tr>
		</table><br><br></div>";
    //--------------------


	echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';
	echo "<input type='hidden' name= 'wtema' id='wtema' value='".$wtema."'>";
    echo "<table class='encabezadoTabla' align='center' width='1000'><tr><td align='center' >Creaci&oacute;n de Formatos</td></tr></table><br>";
	echo "<div style='border:#2A5DB0 2px solid; width:1235px '  >";

	echo "<table id='principal' align='center'><tr><td valign='top' width='511px' height='500' style='border-right:#2A5DB0 3px solid;'>";
	echo "<div id='arbol' >";
	echo "<table class='encabezadoTabla' width='100%' ><tr><td> Area Para Crear Formatos </td></tr></table><br><br>";
	echo "<div style='text-align: Left;' id='temas'>";
	echo "<table ><tr><td><a href='#null' onclick='javascript:removerFilas(\"".$inicio."\",\"\" ,\"\" ,\"\" ,\"\" );' > (click para crear o modificar formato)</a></td></tr></table> ";

	echo "</div>";
	echo "<div style='text-align: Left;' id='niveles'></div>";
	echo "<div style='text-align: Left;' id='formatos'></div>";
	echo "<div style='text-align: Left;' id='gcompetencias'></div>";
	echo "<div style='text-align: Left;' id='competencias'></div>";
	echo "<div style='text-align: Left;' id='descriptores'></div>";
	echo "<div style='text-align: Left;' id='instrucciones'></div>";

	//echo arbol($nombreUl,$nomFila,$contenido);

	echo"</div>";
	/*
	<table><tr><td><input type='button' value='crear formula para reportes' onclick='crear_formula()'></td></tr></table>
	<div id='div_crearformulas'>
	</div>
	*/
	echo "</td><td valign='top' width='700'>
	<table width='100%'  id='vformato' class='encabezadoTabla'><tr><td><font>Area de visualizacion de Formato</font></td></tr></table>

	<div id='formato'>

    </div></td></tr>

	</table>
	</div>";

	echo "</td><td valign='top'>";
	echo "<input type='hidden' name= 'wtemaevaluacion' id='wtemaevaluacion'>";
	echo "<input type='hidden' name= 'wnivel' id='wnivel'>";
	echo "<input type='hidden' name= 'wformato' id='wformato'>";
	echo "<input type='hidden' name= 'wgcompetencia' id='wgcompetencia'>";
	echo "<input type='hidden' name= 'wcompetencia' id='wcompetencia'>";

	echo "</td></tr></table>";

	echo "<div id='msjEspere' name='msjEspere' style='display:none;'>";
    echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br /> Por favor espere un momento ... <br /><br />";
    echo "</div>";


	echo "<div id='Darorden' class='fila2' align='middle' style='display:none;width:100%;cursor:default;' >";
	echo "<br><br>
		   <table width='400' style='border:#2A5DB0 1px solid'>
			<tr align='center' class='encabezadoTabla' >
				<td colspan='2'>Asignar Orden</td>
			</tr>";
	echo  "<tr align='center' class='encabezadoTabla' >
				<td >Orden</td><td ><input type='text' id='txt_orden' /><input type='hidden' id='txt_orden_oculto' /></td>
			</tr>";
	echo "  <tr align='center' >
				<td colspan='2'><INPUT TYPE='button' value='Grabar' onClick='Grabarorden(); $.unblockUI()' style='width:100'/><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100' />
				</td>
			</tr>
	</table><br><br></div>";

	echo "<div id='Darordencompetencia' class='fila2' align='middle' style='display:none;width:100%;cursor:default;' >";
	echo "<br><br>
		   <table width='400' style='border:#2A5DB0 1px solid'>
			<tr align='center' class='encabezadoTabla' >
				<td colspan='2'>Asignar Orden</td>
			</tr>";
	echo  "<tr align='center' class='encabezadoTabla' >
				<td >Orden</td><td ><input type='text' id='txt_ordencompetencia' /><input type='hidden' id='txt_ordencompetencia_oculto' /></td>
			</tr>";
	echo "  <tr align='center' >
				<td colspan='2'><INPUT TYPE='button' value='Grabar' onClick='Grabarordencompetencia(); $.unblockUI()' style='width:100'/><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100' />
				</td>
			</tr>
	</table><br><br></div>";

		echo "<div id='Darordengcompetencia' class='fila2' align='middle' style='display:none;width:100%;cursor:default;' >";
	echo "<br><br>
		   <table width='400' style='border:#2A5DB0 1px solid'>
			<tr align='center' class='encabezadoTabla' >
				<td colspan='2'>Asignar Orden</td>
			</tr>";
	echo  "<tr align='center' class='encabezadoTabla' >
				<td >Orden</td><td ><input type='text' id='txt_ordengcompetencia' /><input type='hidden' id='txt_ordengcompetencia_oculto' /></td>
			</tr>";
	echo "  <tr align='center' >
				<td colspan='2'><INPUT TYPE='button' value='Grabar' onClick='GrabarordenGcompetencia(); $.unblockUI()' style='width:100'/><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100' />
				</td>
			</tr>
	</table><br><br></div>";

if(isset($consultaAjax))
{
       switch($consultaAjax)
       {
               case 'pintaFormato': //grabardatos($th_01,$th_02,$conex);
                       echo actualizaFormato($nombreUl,$nomFila,$contenido);
               break;

			   case 'actualizaArbol':
					  echo arbol($nombreUl,$nomFila,$contenido);
               break;

               default :
                       break;
       }

}
// Esta funcion  dependiendo del parametro $nomFila actualiza contenidos en el Formato de Evaluacion
function actualizaFormato ($nombreUl,$nomFila,$contenido)
{
 switch($nomFila)
       {
               //Si $nomFila vale Formato se agrega el titulo del formato
			   case 'Formato':
				{
					echo "<ul><li id='elementos'><a  href='#'><span>".$contenido."</span></a></li></ul>";
				}

               break;

			   case 'Gcompetencia':
					echo "<ul><li id='elementos'><a  href='#'><span>".$contenido."</span></a></li></ul>";
               break;

			   case 'Competencia':
					echo "<ul><li id='elementos'><a  href='#'><span>".$contenido."</span></a></li></ul>";
               break;

			    case 'Descriptor':
					echo "<ul><li id='elementos'><a  href='#'><span>".$contenido."</span></a></li></ul>";
               break;


               default :
                       break;
       }

}
?>
<?php if(!isset($consultaAjax)) { ?>
</body>
</html>
<?php } ?>