<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<html>
<head>
  	<title>MATRIX  Registro Diario de Pacientes</title>
</head>

<body onload=ira()>

<script type="text/javascript">
function enter()
{
	
	document.forms.reg_dia_pac.submit();
}

</script>

<?php

/* **********************************************************
   *     PROGRAMA PARA LA IMPRESION DEL REGISTRO            *
   *                 DIARIO DE PACIENTES                    *
   **********************************************************/

//==================================================================================================================================
//PROGRAMA                   : reg_dia_pac.php
//AUTOR                      : Juan David Jaramillo R.
//FECHA CREACION             : Febreo 14 de 2007
//FECHA ULTIMA ACTUALIZACION : Marzo 06 de 2007
$wactualiz="(2012-12-03)";
//DESCRIPCION
//================================================================================================================================\\
//Este programa permite imprimir el registro diario de pacientes con la informacion basica de la consulta y los campos nece-      \\
//sarios para complementar la informacion del rips.																				  \\
//================================================================================================================================\\

//================================================================================================================================\\
//================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                 \\
//================================================================================================================================\\
//2012-12-03: Se eliminan las cajas de texto donde se ingresaban el codigo del medico y el nombre del medico, se coloco un select
//			  para seleccionar el medico. Viviana Rodas
//2012-09-26: Se agrega la funcion campo fecha defecto para que el usuario no digite la fecha y solo la seleccione del calendario
//				Viviana Rodas																									  \\ 
//
//2012-09-25: Se agrega en el reporte la historia clinica del paciente, tambien se agregan los nuevos estilos. Viviana Rodas     \\
//________________________________________________________________________________________________________________________________\\
//2007-03-06: Se puso el nombre y el cosigo del medico en la parte de la impresion                                                \\
//________________________________________________________________________________________________________________________________\\
//2007-05-08: Hecha por Juan David Londoño. Se cambio recidencia por residencia																				  \\
//                                                                                                                                \\
//________________________________________________________________________________________________________________________________\\

// COLORES
$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro  
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
$wclcy="#A4E1E8"; //COLOR DE TITULOS  -- Cyan
$color="#999999";


// FUNCIONES

function traer_nombre($wcod)
{
 echo "entro";
	global $wbasedato;
	global $conex;
	global $wemp2;

	 $q= " SELECT codigo, descripcion ".
		"   FROM ".$wemp2."_000010 ".
		"  WHERE codigo = '".$wcod."'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	echo "<td align=center><select name='causa' onchange='javascript: llamarAjax();'>";
				echo "<option></option>";
				
				for( $i = 0; $rows5 = mysql_fetch_array( $res5 ); $i++ )
				{ 
					
					if( $causa != trim( $rows5['Caucod'] )." - ".trim( $rows5['Caudes'] ) )
					{
						echo "<option>".$rows5['Caucod']." - ".$rows5['Caudes']."</option>";
					}
					else
					{
						echo "<option selected>".$rows5['Caucod']." - ".$rows5['Caudes']."</option>";
					}
					
					echo "</td></tr>";
					
				}
				

	return;
}

function traer_entidad($wnit)
{
	global $wbasedato;
	global $conex;
	global $wemp2;

	 $q= " SELECT descripcion ".
		"   FROM ".$wemp2."_000002 ".
		"  WHERE nit = '".$wnit."'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	$row = mysql_fetch_array($res);

	return $row[0];
}

function traer_adm($wced)
{
	global $wbasedato;
	global $conex;

	 $q= " SELECT pacfna,pacsex,paciu,Pachis ".
		"   FROM ".$wbasedato."_000100 ".
		"  WHERE pacdoc = '".$wced."'";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);

	$row1 = mysql_fetch_array($res1);

	return array($row1[0],$row1[1],$row1[2],$row1[3]);
}

function traer_edad($wfecha)
{
	//fecha actual

	$dia=date("d");
	$mes=date("m");
	$ano=date("Y");

	//fecha de nacimiento

	$anonaz=intval(substr($wfecha,0,4));
	$mesnaz=intval(substr($wfecha,5,6));
	$dianaz=intval(substr($wfecha,8,10));

	//si el mes es el mismo pero el dia inferior aun no ha cumplido años, le quitaremos un año al actual

	if (($mesnaz == $mes) && ($dianaz > $dia))
		$ano=($ano-1);

	//si el mes es superior al actual tampoco abra cumplido años, por eso le quitamos un año al actual

	if ($mesnaz > $mes)
		$ano=($ano-1);

	//ya no habria mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad

	$edad=($ano-$anonaz);

	return $edad;
}

function traer_mun($wcod)
{
	global $wbasedato;
	global $conex;

	 $q= " SELECT nombre ".
		"   FROM root_000006 ".
		"  WHERE codigo = '".$wcod."'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	$row = mysql_fetch_array($res);

	return $row[0];
}

function solucionCitas( $codEmp ){
	
	global $conex;
	
	$solucionCitas = '';
	
	 $sql = "SELECT
				detval
			FROM
				root_000051
			WHERE
				detapl = 'citas'
				AND detemp = '$codEmp'
			";
	
	$res = mysql_query( $sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$solucionCitas = $rows[0];
	}
	
	return $solucionCitas;
}
	


// INICIALIZACION DE VARIABLES
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");
$valido=1;
session_start();
if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedato = strtolower( $institucion->baseDeDatos );
	echo "<form name='reg_dia_pac' action='' method=''>";
	echo " <INPUT TYPE='hidden' NAME='wemp_pmla' VALUE='".$wemp_pmla."' >";
	
	encabezado("Registro diario de pacientes ",$wactualiz, "logo_".$wbasedato);
    
	$wemp2 = solucionCitas( $wemp_pmla );
	
	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));

    $wfec=date("Y-m-d");
    $whora = (string)date("H:i:s");

    if (!isset($medicos) or !isset($resultado) )
	{
		// Inicio de captura de datos en formulario

		echo "<br><br>";
		echo "<table border=0 ALIGN=CENTER >";
		echo "<tr><td colspan=2 align=center class='encabezadotabla'>Datos de la Consulta</td></tr>";
		// if (!isset($wcodmed))
			// $wcodmed="";
		// else $wnommed=traer_nombre($wcodmed);

		// if (!isset($wnommed))
			// $wnommed="";
			// <INPUT TYPE='text' NAME='wcodmed' VALUE='".$wcodmed."' size=10 maxlength=20 onBlur='javascript:consultar();'>
			  // <INPUT TYPE='text' NAME='wnommed' VALUE='".$wnommed."' size=30 maxlength=30 noentry></font></b></td>
		
		$q= " SELECT codigo, descripcion ".
		     "FROM ".$wemp2."_000010 ".
		     "WHERE activo='A'
			 group by descripcion order by codigo";

		$res = mysql_query($q,$conex)or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($res);
		if ($num>0)
		{
			echo "<tr><td class='fila1'>Medico:&nbsp</td>";
			echo "<td align=center class='fila2'><select name='medicos' onchange=''>";
			echo "<option>Seleccione...</option>";
				
				for( $i = 0; $rows5 = mysql_fetch_array( $res ); $i++ )
				{ 
					
					if( $medicos != trim( $rows5['codigo'] )." - ".trim( $rows5['descripcion'] ) )
					{
						echo "<option>".$rows5['codigo']." - ".$rows5['descripcion']."</option>";
					}
					else
					{
						echo "<option selected>".$rows5['codigo']." - ".$rows5['descripcion']."</option>";
					}
					
					
					
				}
				echo "</td></tr>";
		}	  
		echo "<tr>";
	    echo "<td class='fila1' align=center valign='top'>Fecha de Consulta</td>";
	    echo "<td class='fila2' align='left'>";
		if(isset($wfecini) && !empty($wfecini))
		{
			campoFechaDefecto("wfecini",$wfecini);
		} else 
		{
			campoFecha("wfecini");
		}
		echo "</td>";
		echo "</tr>";

		echo "<input type='HIDDEN' NAME= 'wemp2' value='".$wemp2."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

		//echo "<tr><td class='fila2' align='center' colspan=2>Generar la Consulta<input type='checkbox' name='wgen'></td></tr>";
		echo "<tr><td class='fila2' align=center colspan=2><input type='button' value='Generar' onclick='javascript:enter();'>";
		echo "<input type='button' value='Cerrar' style='width:70' onclick='javascript: cerrarVentana();'></td></tr>";
		echo "</table>";
		
		
	}
	else
	{
		echo " <INPUT TYPE='hidden' NAME='wemp_pmla' VALUE='".$wemp_pmla."' >";
		echo "<table align=center >";
		if ($medicos=="Seleccione...")
		{
			$medicos1="";
		}
		else
		{
			$medicos1=$medicos;
		}
		echo "<tr><td class='encabezadotabla' align='center'>Medico: ".$medicos1."</td></tr>";
		echo "</table>";
		echo "<table align='right'>";
		echo "<tr><td align=right><font size=2><A href='reg_dia_pac.php?medico=".$medicos."&amp;wbasedato=".$wbasedato."&amp;&wemp2=".$wemp2."&wemp_pmla=".$wemp_pmla."&bandera='1'>VOLVER</A></font></td></tr>";
		echo "</table>";
		
		
		
		echo "<br>";
		echo "<table align='center' border='0'>";
		echo "<tr><td align=right><font text size=2><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		FECHA: </b>".$wfecini."</font></td></tr>";
		echo "</table>";

		echo "<input type='HIDDEN' NAME= 'wcodmed' value='".$wcodmed."'>";
		echo "<input type='HIDDEN' NAME= 'wnommed' value='".$wnommed."'>";
		echo "<input type='HIDDEN' NAME= 'wfecha' value='".$wfecini."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		//if(!isset($medico) or ($wcodmed == "Seleccione..."))
		if(!isset($medicos)or ($medicos == "Seleccione..."))
		{
			echo "<table align='center' border=0 width=500 >";
			echo "<tr class='fila1'><td align='center'><font size=2 face='arial'>No Ingreso un Medico Valido</td><tr>";
			echo "</table>";
		}
		else
		{
			
			$med=explode(" - ", $medicos);
			$wcodmed=$med[0];
			$wnommed=$med[1];
			  $query = " SELECT hi, cedula, nom_pac, edad, nit_res ".
					 "   FROM ".$wemp2."_000009 ".
			      	 " 	WHERE cod_equ= '".$wcodmed."'".
			 		 "    AND fecha = '".$wfecini."'".
			 		 "  ORDER by hi ";

			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);

			if ($num>0)
			{
				echo "<br>";
				echo "<table align=center border=0 width='100%'>";
				echo "<tr class='encabezadotabla'>";
				echo "<td align=center  rowspan=3><font size=2><b>No.</b></font></td>";
				echo "<td align=center  rowspan=3><font size=2><b>Hora</b></font></td>";
				echo "<td align=center  rowspan=3><font size=2><b>Historia</b></font></td>";
				echo "<td align=center  rowspan=3><font size=2><b>Nombre del Paciente</b></font></td>";
				echo "<td align=center  rowspan=3><font size=2><b>Edad</b></font></td>";
				echo "<td align=center  colspan=2><font size=2><b>Sexo</b></font></td>";
				echo "<td align=center  rowspan=3><font size=2><b>Residencia Habitual</b></font></td>";// 2007-05-08
				echo "<td align=center  rowspan=3><font size=2><b>Nombre de la Entidad</b></font></td>";
				echo "<td align=center  colspan=2><font size=2><b>Tipo de Consulta</b></font></td>";
				echo "<td align=center  colspan=3><font size=2><b>Nuevo</b></font></td>";
				echo "<td align=center  rowspan=3><font size=2><b>Nombre de Diagnostico</b></font></td>";
				echo "<td align=center rowspan=3><font size=2><b>Codigo de Diagnostico</b></font></td>";
				echo "</tr>";

				echo "<tr class='encabezadotabla'>";
				echo "<td align=center  rowspan=2><font size=2><b>M</b></font></td>";
				echo "<td align=center  rowspan=2><font size=2><b>F</b></font></td>";
				echo "<td align=center  rowspan=2><font size=2><b>Ugente</b></font></td>";
				echo "<td align=center  rowspan=2><font size=2><b>Electiva</b></font></td>";
				echo "<td align=center  rowspan=2><font size=2><b>Si</b></font></td>";
				echo "<td align=center  colspan=2><font size=2><b>No</b></font></td>";
				echo "</tr>";

				echo "<tr class='encabezadotabla'>";
				echo "<td align=center><font size=2><b>1a.</b></font></td>";
				echo "<td align=center><font size=2><b>Rev</b></font></td>";
				echo "</tr>";


				for ($i=1;$i<=$num;$i++)
	        	{

				    //Definiendo la clase por cada fila
					if( $i%2 == 0 ){
						$class = "class='fila1'";
					}
					else{
						$class = "class='fila2'";
					}
					
	        		$row = mysql_fetch_array($err);

					$whoract=$row[0];
	        		$wcedpac=$row[1];
	        		$wnompac=$row[2];
					$wedadpa=$row[3];
					$wnitres=$row[4];

					$wentpac=traer_entidad($wnitres);
					list ($wedapac,$wsexo,$wdirpac,$whis)=traer_adm($wcedpac);

					echo "<tr $class>";
					echo "<td><font size=2>".$i."</font></td>";
					echo "<td><font size=2>".$whoract."</font></td>";
					echo "<td><font size=2>".$whis."</font></td>";
					echo "<td><font size=2>".$wnompac."</font></td>";
					if(!isset($wedapac) or ($wedapac==""))
						echo "<td><font size=2>".$wedadpa."</font></td>";
					else
					{
						$wedapac=traer_edad($wedapac);
						echo "<td><font size=2>".$wedapac."</font></td>";
					}

					if ($wsexo=="M")
					{
						echo "<td><font size=2>X</font></td>";
						echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
					}
					else
					{
						if ($wsexo=="F")
						{
							echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
							echo "<td><font size=2>X</font></td>";
						}
						else
						{
							echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
							echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
						}
					}
					if(!isset($wdirpac) or ($wdirpac==""))
						echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
					else
					{
						$wdirpac=traer_mun($wdirpac);
						echo "<td><font size=2>".$wdirpac."</font></td>";
					}

					echo "<td><font size=2>".$wentpac."</font></td>";
					echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
					echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
					echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
					echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
					echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
					echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
					echo "<td><font size=2>&nbsp&nbsp&nbsp&nbsp</font></td>";
	        	}
				echo "</table>";

			}
			else
			{
				echo "<br>";
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>No se encontro Informacion para la Consulta</td><tr>";
				echo "</table>";
			}
		}
		echo "<div align='center'><br /><br /><input type='button' value='Cerrar' style='width:100' onclick='javascript: cerrarVentana();'></div>";
	}
	

}
echo "</body>";
echo "</html>";

?>

