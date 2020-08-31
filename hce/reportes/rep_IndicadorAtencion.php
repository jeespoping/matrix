<?php
include_once("conex.php");
if(isset($cajax))
{
 include_once("root/comun.php");
 

    // buscamos los ingresos que sean tipo soat y por centro de costos
    $q = " SELECT A.mtrftr,A.mtrhtr,A.mtrfco,A.mtrhco,A.mtrhis, A.mtring "
        ."	  FROM ".$whce."_000022 A,".$wmovhos."_000018 B, ".$wmovhos."_000016 C "
        ."	 WHERE A.mtrftr = '$fecha'" 
		."     AND Mtrcci    		=  '".$cco."' "
		."	   AND mtrfco    != '0000-00-00' "
		."	   AND A.mtrftr  != '0000-00-00' "
        ."	   AND A.mtrhis  = B.ubihis "
        ."	   AND A.mtring  = B.ubiing "
        ."     AND A.mtrhis  = C.inghis "
        ."     AND A.mtring  = C.inging "
        ."     AND C.ingtip  = '".$wdetval."' " // para que muestre los ingresos que sean SOAT
        ."     AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )" // para solo los pacientes que fueron atentidos
        ."   ORDER BY A.mtrhis, A.mtring  ";
	
    $result = mysql_query($q,$conex) or die("ERROR EN QUERY $q - ".mysql_error() );
    $num = mysql_num_rows($result );
    $numacc = 0;
    $whis = "";

    //echo $q."<br/>";

    if($num>0)
    {
        //si hay ingresos creamos una tabla temporal para guardar los ingresos de accidente
        conexionOdbc($conex, $wmovhos, $conexUnix, 'facturacion');
        $q1=" CREATE TEMPORARY TABLE IF NOT EXISTS ingreacc "
           ."(INDEX cod_idx( mtrftr,mtrhtr ),"
           ."mtrftr date,mtrhtr time,Mtrfco date,mtrhco time,Mtrhis varchar(80),Mtring varchar(80))";

        $result1 = mysql_query($q1,$conex) or die("ERROR EN QUERY $q1 - ".mysql_error() );

        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($result);
            $wfin = $row['mtrftr'];
            $whin = $row['mtrhtr'];
            $wfco = $row['mtrfco'];
            $whco = $row ['mtrhco'];
            $whis = $row['mtrhis'];
            $wing = $row['mtring'];

            //preguntamos en unix si el ingreso es de accidente
            $q = "SELECT *
                    FROM inaccdet
                   WHERE accdethis = '".$whis."'
                     AND accdetnum = '".$wing."'";

            $res1 = odbc_do($conexUnix,$q);
            $num1 = odbc_fetch_row($res1);

            if($num1>0)
            {
                $q2 = "INSERT INTO ingreacc (mtrftr,mtrhtr,Mtrfco,Mtrhco,Mtrhis,Mtring) VALUES ('".$wfin."','".$whin."','".$wfco."','".$whco."','".$whis."','".$wing."')";
                $result2 = mysql_query($q2,$conex) or die("ERROR EN QUERY $q2 - ".mysql_error() );
            }
        }
    }

 //==============acá busco en la tabla 22 los pacientes atendidos en la fecha enviada por ajax
 
	$q1= " SELECT	C.inging,A.mtrhis, SEC_TO_TIME( TIMESTAMPDIFF(SECOND, CONCAT(A.mtrftr,' ',A.mtrhtr), CONCAT(Mtrfco,' ',Mtrhco)) ) as diferencia, A.mtrftr, A.mtrhtr, A.mtrfco, A.mtrhco"
        ."  FROM    ".$whce."_000022 A,".$wmovhos."_000018 B, ".$wmovhos."_000016 C " 
        ."  WHERE   A.fecha_data ='$fecha'
			  AND Mtrcci     =  '".$cco."' 
			  AND mtrfco    != '0000-00-00'
			  AND A.mtrftr  != '0000-00-00' 
              AND A.mtrhis  = B.ubihis
              AND A.mtring  = B.ubiing
              AND A.mtrhis  = C.inghis
              AND A.mtring  = C.inging "
        ."    AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )" // para solo los pacientes que fueron atentidos
        ."    AND C.ingtip != '".$wdetval."' "; // para que muestre los ingresos que no sean SOAT
	
    $q2 = '';
    if($num>0)
    {
        $q2.="UNION"
		   ." 	SELECT	mtring, mtrhis, SEC_TO_TIME( TIMESTAMPDIFF( SECOND, CONCAT(mtrftr,' ',mtrhtr), CONCAT(Mtrfco,' ',Mtrhco))) as diferencia, mtrftr, mtrhtr, mtrfco, mtrhco"
           ."    FROM ingreacc "
		   ."	 WHERE mtrftr='$fecha'";
    }

    $q = "
            SELECT  *
            FROM
            (
                $q1
                $q2
            ) as t
            ORDER BY diferencia DESC
        ";
//======================================================
    $result = mysql_query($q,$conex) or die("ERROR EN QUERY $q - ".mysql_error() );
    $num = mysql_num_rows($result );
	
	
 echo "<div>";
 $wclass="";
	echo "<center><table>";
	echo "<tr class='encabezadotabla' align='center'><td colspan=7>DETALLE OPORTUNIDAD DE ATENCI&Oacute;N EN LA FECHA: ".$fecha."</td></tr>";
	echo "<tr class='encabezadotabla' align='center'>";
		echo "<td align='center'rowspan=2>Historia</td>";
		echo "<td align='center' rowspan=2>Paciente</td>";
		echo "<td colspan=2 align='center'>Realizaci&oacute;n Triage</td>";
		echo "<td colspan=2 align='center'>Atenci&oacute;n</td>";
		echo "<td align='center' rowspan=2>Diferencia<br>(HH:mm:ss)</td>";
		echo "<tr class='encabezadotabla'><td align='center'>Fecha<br>(AAAA-MM-DD)</td>";
		echo "<td align='center'>Hora<br>(HH:mm:ss)</td>";
		echo "<td align='center'>Fecha<br>(AAAA-MM-DD)</td>";
		echo "<td align='center'>Hora<br>(HH:mm:ss)</td></tr>";
	echo "<tr align='center'>";
	for($i=0; $i < $num; $i++)
	{	
		$row=mysql_fetch_array($result);
		//datos del paciente
		$qpac = "SELECT Pacno1, Pacno2, Pacap1, Pacap2 ".
						 "  FROM root_000036, root_000037 "
						." WHERE Orihis='".$row['mtrhis']."' "
						//."	 AND Oriing='".$rowpro['inging']."'"
						."	 AND Pacced = Oriced "
						."	 AND Pactid = Oritid";
				 $rspac= mysql_query($qpac, $conex);
				 $rowpac= mysql_fetch_array($rspac);
		//datos del paciente
		if (is_integer($i/2))
					  $wclass="fila1";
						else
							$wclass="fila2";
		echo "<tr class='$wclass'>";
			echo "<td align='center'>".$row['mtrhis']."</td>";
			echo "<td align='left'>".$rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3]."</td>";
			echo "<td align='center'>".$row[3]."</td>";
			echo "<td align='center'>".$row[4]."</td>";
			echo "<td align='center'>".$row[5]."</td>";
			echo "<td align='center'>".$row[6]."</td>";
			echo "<td align=center>".(($row[2] < '00:00:00')? '00:00:00' : $row[2])."</td>";
		echo "</tr>";
	}
	echo "<tr class='encabezadotabla'><td colspan=7>FINAL DETALLE PARA LA FECHA: ".$fecha."</td></tr>";
	echo "</table></center>";
	echo "</div>";
 return;
}

/***********Funcion para calcular el indice de oportunidad por especialidad del medico**************/
	function oporEspecialidad($num,$wdetval,$cco)
	{	
		global $conex;
		global $wmovhos;
		global $conexUnix;
		global $whce;
		global $wdetval;
		global $fechaini;
		global $fechafin;
		
		//realizamos la consulta buscando el centro de costos que se relaciona entre movhos y hce
		//y que la historia del paciente en HCE sea igual a la historia del paciente ingresada en MOVHOS
		//y que el ingreso del paciente en HCE sea igual al ingreso del paciente en MOVHOS

		$q = " SELECT A.mtrftr,A.mtrhtr,A.mtrfco,A.mtrhco,A.mtrhis, A.mtring "
			."	  FROM ".$whce."_000022 A,".$wmovhos."_000018 B, ".$wmovhos."_000016 C "
			."	 WHERE A.fecha_data between '$fechaini' AND '$fechafin' "  //del facturaddor para que permita sacar la diferencia en tiempo correctamente
			."     AND Mtrcci    		=  '".$cco."' "
			."	   AND mtrfco   	   != '0000-00-00' " 
			."	   AND A.mtrftr    != '0000-00-00' " 
			."	   AND A.mtrhis  		= B.ubihis "
			."	   AND A.mtring  		= B.ubiing "
			."     AND A.mtrhis  		= C.inghis "
			."     AND A.mtring  		= C.inging "
			."     AND C.ingtip  		= '".$wdetval."' " // para que muestre los ingresos que sean SOAT
			."     AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )" // para solo los pacientes que fueron atentidos
			."   ORDER BY A.mtrhis, A.mtring  ";

		$result = mysql_query($q,$conex) or die("ERROR EN QUERY $q - ".mysql_error() );
		$num = mysql_num_rows($result );
		$numacc = 0;
		$whis = "";

		//echo $q."<br/>";

		if($num>0)
		{
			//si hay ingresos creamos una tabla temporal para guardar los ingresos de accidente
			conexionOdbc($conex, $wmovhos, $conexUnix, 'facturacion');
			$q1=" DROP TABLE IF EXISTS ingreacc ";
			$result1 = mysql_query($q1,$conex) or die("ERROR EN QUERY $q1 - ".mysql_error() );
			
			$q1=" CREATE TEMPORARY TABLE IF NOT EXISTS ingreacc "
			   ."(INDEX cod_idx( mtrftr,mtrhtr ),"
			   ."mtrftr date,mtrhtr time,Mtrfco date,mtrhco time,Mtrhis varchar(80),Mtring varchar(80))";

			$result1 = mysql_query($q1,$conex) or die("ERROR EN QUERY $q1 - ".mysql_error() );

			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($result);
				$wfin = $row['mtrftr'];
				$whin = $row['mtrhtr'];
				$wfco = $row['mtrfco'];
				$whco = $row ['mtrhco'];
				$whis = $row['mtrhis'];
				$wing = $row['mtring'];

				//preguntamos en unix si el ingreso es de accidente
				$q = "SELECT *
						FROM inaccdet
					   WHERE accdethis = '".$whis."'
						 AND accdetnum = '".$wing."'";

				$res1 = odbc_do($conexUnix,$q);
				$num1 = odbc_fetch_row($res1);

				if($num1>0)
				{
					//llenamos la temporal con los ingresos de tipo accidente
					$q2 = "INSERT INTO ingreacc (mtrftr,mtrhtr,Mtrfco,Mtrhco,Mtrhis,Mtring) VALUES ('".$wfin."','".$whin."','".$wfco."','".$whco."','".$whis."','".$wing."')";
					$result2 = mysql_query($q2,$conex) or die("ERROR EN QUERY $q2 - ".mysql_error() );
				}
			}
		}

		// Consulta reposte si tabla temporal - 2012-05-08
		
		 $q1 ="SELECT A.Mtreme, SUM(UNIX_TIMESTAMP(concat(mtrfco,' ',A.mtrhco))- UNIX_TIMESTAMP(concat(A.mtrftr, ' ',A.mtrhtr))) as diferencia, COUNT(*) as pacientes"
			."  FROM ".$whce."_000022 A,".$wmovhos."_000018 B, ".$wmovhos."_000016 C "
			." WHERE UNIX_TIMESTAMP(concat(A.mtrfco,' ',A.mtrhco)) >= UNIX_TIMESTAMP(concat(A.mtrftr, ' ',A.mtrhtr)) "							   //Que la hora de atencion del medico sea mayor que la hora de entrada
			."	 AND A.fecha_data between '$fechaini' AND '$fechafin'
				 AND Mtrcci    		=  '".$cco."'
				 AND mtrfco   	   != '0000-00-00' 
				 AND A.mtrftr  != '0000-00-00' 
				 AND A.mtrhis  		= B.ubihis
				 AND A.mtring  		= B.ubiing
				 AND A.mtrhis  		= C.inghis
				 AND A.mtring  		= C.inging "
			."   AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )" // para solo los pacientes que fueron atentidos
			."   AND C.ingtip != '".$wdetval."' " // para que muestre los ingresos que no sean SOAT
			." GROUP BY 1"
			." UNION "
			."SELECT A.Mtreme, 0 as diferencia, COUNT(*) as pacientes"
			."  FROM ".$whce."_000022 A,".$wmovhos."_000018 B, ".$wmovhos."_000016 C "
			." WHERE UNIX_TIMESTAMP(concat(A.mtrfco,' ',A.mtrhco)) < UNIX_TIMESTAMP(concat(A.mtrftr, ' ',A.mtrhtr)) "
			."	 AND A.fecha_data between '$fechaini' AND '$fechafin'
				 AND Mtrcci    		=  '".$cco."'
				 AND mtrfco   	   != '0000-00-00' 
				 AND A.mtrftr  != '0000-00-00' 
				 AND A.mtrhis  		= B.ubihis
				 AND A.mtring  		= B.ubiing
				 AND A.mtrhis  		= C.inghis
				 AND A.mtring  		= C.inging "
			."   AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )" // para solo los pacientes que fueron atentidos
			."   AND C.ingtip != '".$wdetval."' " // para que muestre los ingresos que no sean SOAT
			." GROUP BY 1";
		$q2 = '';
		
		if($num>0)
		{
			$q2.="UNION"
			   ." SELECT '' as Mtreme, SUM(UNIX_TIMESTAMP(concat(mtrfco,' ',mtrhco))- UNIX_TIMESTAMP(concat(mtrftr, ' ',mtrhtr))) as diferencia, COUNT(*) as pacientes"
			   ."    FROM ingreacc "
			   ." GROUP BY 1";
		}

		$q = "
				SELECT Mtreme, SUM(diferencia), SUM(pacientes)
				FROM
				(
					$q1
					$q2
				) as t
				GROUP BY Mtreme
				ORDER BY Mtreme ASC
			";
		
		$result = mysql_query($q, $conex) or die("ERROR EN QUERY $q - ".mysql_error() );
		$num = mysql_num_rows( $result );
		echo "<input type='hidden' id='totalfilas' value=".$num.">";
		if( $num > 0 )
		{
			
			echo "<table align=center >";
			echo "<th class='encabezadoTabla' colspan='4'>Indice por Especialidad</th>";
			echo "<tr class='encabezadoTabla'>";
			echo "<td align=center>&nbsp;Especialidad</td>";
			echo "<td align=center>&nbsp;Tiempo total de atención <br/> (En minutos)</td>";
			echo "<td align=center>&nbsp;Cantidad <br> de pacientes&nbsp;</td>";
			echo "<td align=center>&nbsp;Tiempo promedio de<br> atenci&oacute;n&nbsp; (HH:mm:ss)</td>";
			echo "</tr>";
			$wclass="fila2";
			$promedio=0;
			$pac=0;
			$totalprom=0;
			$totalDatos=0;
			for($i=0; $i<$num; $i++)
			{
				$rowpro = mysql_fetch_array( $result );
				if ($wclass=="fila1")
					$wclass="fila2";
				else
					$wclass="fila1";
				
				$esp=$rowpro[0];
				if ($esp != '' and $esp != 0)
				{
					// se consulta la tabla movhos_000044 para traer el nombre de la espacialidad
					$query ="select Espcod, Espnom
							 from ".$wmovhos."_000044
							 where Espcod ='".$esp."' ";
					$res = mysql_query($query, $conex) or die("ERROR EN QUERY $query - ".mysql_error() );
					$espNom = mysql_fetch_array( $res );
					$esp=$espNom['Espnom'];
				}
				else if ($esp == '')
				{
					$esp="Sin Especialidad";
				}
				else 
				{
					$esp=$esp;
				}
				$promdia = $rowpro[1]; //tiempo sumado de atención
				$pacdia =  $rowpro[2];	//total de pacientes atendidos
				$promediodia = $promdia/$pacdia; //promedio de atención
				echo "<tr class='$wclass'>";
				echo "<td align=left id='esp".$i."' >".$esp."</td>";
				echo "<INPUT type='hidden' id='fec_da".$i."' value='$esp'>";
				echo "<td align=center id='tiem".$i."'>". number_format($promdia/60,2,'.',',')." </td>";
				echo "<td align=center id='pac".$i."' >".number_format($pacdia,0,'',',')."</td>";
				echo "<td align=center id='dif".$i."' >".date("H:i:s", $promediodia  + strtotime( "1970-01-01 00:00:00"))."</td>";
				echo "</tr>";
				echo "<tr id='trdet".$i."' style='display:none;'>";
				  echo "<td colspan=4>";	
					echo "<div id='det".$i."'></div>";
				  echo "</td>";
				echo "</tr>";
				$totalprom+=$promdia;
				$pac+=$pacdia;
				$totalDatos+=$i;
				
			
			}//for
			echo "<INPUT type='hidden' id='totalDatos' name='totalDatos' value='$totalDatos'>";
			if($pac>0)
			{
				$promedio = $totalprom/$pac; //para sacar el promedio total del rango de fechas
			}
			else
				$promedio= 0;
			//Cuando termina de recorrer el ciclo imprimo el total de todos los pacientes de ese rango de fechas
			//y el promedio total obtenido del mismo.
			echo "<tr class='titulo'>";//este class es para colocarle color al total de los datos
			echo"<td align=center>Total </td>";
			echo "<td align=center> ". number_format(($totalprom/60),2,'.',',')."</td>";
			echo "<td align=center>".number_format($pac,0,'',',')."</td>";
			echo "<td align=center>".date("H:i:s", $promedio + strtotime( "1970-01-01 00:00:00") )."</td>";
			echo "</tr></table>";
			
			echo "<br><br>";
		}
	}//funcion
	/************fin Funcion especialidad por medico************/

?>
<html>
<head>
<title>Indicador de Oportunidad en la Atención
</title>
</head>
<script type="text/javascript">
function cerrarVentana()
{
      top.close();
     }

function inicio()
{
   document.location.href='rep_IndicadorAtencion.php?wemp_pmla=01'+document.forms.forma.wemp_pmla.value
}

function retornar(wemp_pmla,fechaini,fechafin,bandera,cco)
{
   location.href = "rep_IndicadorAtencion.php?wemp_pmla="+wemp_pmla+"&fechaini="+fechaini+"&fechafin="+fechafin+"&bandera="+bandera+"&cco="+cco;
}

function mostrarDetalle(i)
{
	if(document.getElementById('fec'+i).style.color!="red")
	{
		document.getElementById('fec'+i).style.color="red";
		document.getElementById('tiem'+i).style.color="red";
		document.getElementById('pac'+i).style.color="red";
		document.getElementById('dif'+i).style.color="red";
	}else{
			document.getElementById('fec'+i).style.color="black";
			document.getElementById('tiem'+i).style.color="black";
			document.getElementById('pac'+i).style.color="black";
			document.getElementById('dif'+i).style.color="black";
		  }
	var tr = document.getElementById('trdet'+i);
	var cont = document.getElementById('det'+i);
	var totdat = document.getElementById('totalDatos').value;
	if(tr.style.display=="none")
	{
		if(cont.innerHTML!="")
		{
				tr.style.display="";
		}else
		   {
				var wemp_pmla= document.getElementById("wemp_pmla").value;
				var whce= document.getElementById("whce").value;
				var wmovhos= document.getElementById("wmovhos").value;
				var fec = document.getElementById('fec_da'+i).value;
				var detval = document.getElementById('wdetval').value;
				var cco = document.getElementById('cco').value;
				var parametros = "cajax=s&wemp_pmla="+wemp_pmla+"&whce="+whce+"&wmovhos="+wmovhos+"&fecha="+fec+"&cco="+cco+"&wdetval="+detval;
				try
				{
					var ajax = nuevoAjax();
					ajax.open("POST", "rep_IndicadorAtencion.php",true);
					ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					ajax.send(parametros);
					ajax.onreadystatechange=function() 
					{
							if (ajax.readyState==4)
						{
							cont.innerHTML=ajax.responseText;
							tr.style.display="";
						}
					}
				}catch(e){	}
			
			}
	}else{
			tr.style.display="none";
		 }
	for(var j=0; j<totdat; j++)
	{
		var traux=document.getElementById('trdet'+j);
		if(j!=i)
		{
			if(traux.style.display!="none")
			{
				traux.style.display="none";
				document.getElementById('fec'+j).style.color="black";
				document.getElementById('tiem'+j).style.color="black";
				document.getElementById('pac'+j).style.color="black";
				document.getElementById('dif'+j).style.color="black";
			}
		}
	}
}			 
	
</script>
<body>
<?php

/**
* REPORTE INDICADOR DE OPORTUNIDAD EN LA ATENCION                                                *
*/
// ===========================================================================================================================================
// PROGRAMA                   :Reporte de tiempo promedio de atencion en urgencias                                                           |
// AUTOR                      :Ing. Luis Haroldo Zapata Arismendy                                                                            |
// FECHA CREACION             :Diciembre 9 de 2011.                                                                                          |
// FECHA ULTIMA ACTUALIZACION :Enero 12 de 2012                                                                                              |
// DESCRIPCION                :Reporte para saber cual es el tiempo promedio de atencion en urgencias, desde que ingresa el paciente
//                             hasta el momento en que es atendido por el médico.                                                            |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// HCE_000022                 :Tabla que contiene la fecha y hora de llegada del paciente y la fecha y hora en que fue atendido por
//                             el médico.
// MOVHOS_000018              :Tabla que contiene los centros de costos con los que se va a hacer la relacion con la tabla HCE_000022
//                                                                                                                                           |
// ==========================================================================================================================================
// M O D I F I C A C I O N E S
//===========================================================================================================================================
// Diciembre 17 de 2012: (Viviana) Se coloca la alineacion a la izquierda en la columna especialidad.
// ==========================================================================================================================================
// Diciembre 14 de 2012: (Viviana) Se crea la funcion oporEspecialidad() para que haga el calculo pero filtrado por especialidad solo para urgencias.
// ==========================================================================================================================================
// Octubre 26 de 2012    :  (Jerson) se modifico el query principal para que compare el centro de costos desde la tabla de hce_000022
// ==========================================================================================================================================
// Junio 20 de 2012      :  (Camilo) Se cambió la lógica del programa para que haga los cálculos directamente desde mysql con el fin de que 
//							mejore el rendimiento, ademas se adicionó funcionalidad ajax para que consulte el detalle de cada fecha.
// ==========================================================================================================================================
// Junio 19 de 2012      :  (Camilo) se agregá la posibilidad de ver el detalle(los pacientes, las fechas de asignación y atención) por cada
//							 dato(fecha) presentado. para esto se agregó la funcion generarDetalle(), la cual llena una tabla con los datos
//							 correspondientes aunque esta permanece oculta hasta que el usuario decida darle click .
// ==========================================================================================================================================
// Mayo 08 de 2018      :  (Edwar) En la consulta del reporte se elimina la creación de la tabla temporal, esta generaba un error
//                          en el que decía que ya existía la tabla temporal.
// ==========================================================================================================================================
// febrero 14 de 2011   :  Santiago Rivera Botero
// ==========================================================================================================================================
// - Se adicionan al promedio los ingresos que hallan sido por accidente
//================================================================================

include_once("root/comun.php");

if(!isset($_SESSION['user']))
    exit("error session no abierta");

$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla)){
    terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$wactualiz = "2013-02-15";
$whce =      consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wmovhos   = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

Encabezado("INDICADOR DE OPORTUNIDAD EN LA ATENCION", $wactualiz  ,"clinica");

echo "<form action='rep_IndicadorAtencion.php?wemp_pmla=$wemp_pmla' method='post'>";

if(!isset( $fechafin ) && !isset( $fechaini ) || isset($bandera))				//si no hay rango de fechas entonces  pedirlos al usuario
{

    if( !isset( $fechafin ) )
    {
        $fechafin = date("Y-m-d");
    }

    if( !isset( $fechaini ) )
    {
        $fechaini = date("Y-m-d");
    }

    //Buscando los centros de costos

    $sql = "SELECT cconom, ccocod
              FROM ".$wmovhos."_000011
             WHERE ccoest = 'on'
               AND ccoing = 'on'";

    $res = mysql_query( $sql );

    echo "<br><br><table align='center'>";
    echo "<tr class='encabezadotabla'>";
    echo "<td align='center'>Centro de Costos</td>";
    echo "</tr><tr class='fila1'>";
    echo "<td align='center'>";
    echo "<SELECT name='cco'>";
    //echo "	<option value='1130 - URGENCIAS'>1130 - URGENCIAS</option>" ;

    //Muestra los datos centros de costo
    for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
        if(isset($cco) && $rows['ccocod']==$cco)
            echo "<option selected value='".$rows['ccocod']." - ".$rows['cconom']."'>".$rows['ccocod']." - ".$rows['cconom']."</option>";
        else
            echo "<option value='".$rows['ccocod']." - ".$rows['cconom']."'>".$rows['ccocod']." - ".$rows['cconom']."</option>";
    }

    echo "</SELECT>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";

    echo "<br><br><table align='center'>";
    echo "<tr class='encabezadotabla'>";
    echo "<td align='center' style='width:200'>Fecha inicial</td>";
    echo "<td align='center' style='width:200'>Fecha final</td>";
    echo "</tr><tr class='fila1'>";
    echo "<td align='center'>";
    campoFechaDefecto( "fechaini", $fechaini );
    echo "</td>";
    echo "<td align='center'>";
    campoFechaDefecto( "fechafin", $fechafin );
    echo "</td>";
    echo "</tr>";
    echo "</table>";

    //Botones ver y cerrar
    echo "<br><table align='center'>";
    echo  "<tr>";
    echo  "<td align='center' width='150'><INPUT type='submit' value='Ver' style='width:100' name='btVer'></INPUT></td>";
    echo  "<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></INPUT></td>";
    echo  "</tr>";
    echo  "</table>";

    echo "<INPUT type='hidden' name='mostrar' value='on'>";

    echo "</form>";

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
		echo "<td class='fila1'>Centro de costos:</td>";
		echo "<td class='fila2'>$cco</td>";
		echo "</tr>";
		//echo "<tr class='fila1'><td colspan=2 align=center>(** No incluye los ingresos por SOAT)</td></tr>";
		echo "<tr class='fila1'><td colspan=2 align=center>(**NO incluye revisiones por SOAT)</td></tr>";
		echo "</table><br><br>";

		$cco_str = explode(" - ",$cco);
		$cco = $cco_str[0];
	
		//==================================================================================================================
		$q= "SELECT detapl,detval
			   FROM root_000051
			  WHERE detapl='tiposoat'
				AND detemp='$wemp_pmla'";
		$res=mysql_query($q);
		$row=mysql_fetch_array($res);

		$wdetval=$row['detval']; //Esta variable la agregamos en la consulta para condicionar que no incluya los usuarios de tipo SOAT

		//realizamos la consulta buscando el centro de costos que se relaciona entre movhos y hce
		//y que la historia del paciente en HCE sea igual a la historia del paciente ingresada en MOVHOS
		//y que el ingreso del paciente en HCE sea igual al ingreso del paciente en MOVHOS

		// buscamos los ingresos que sean tipo soat y por centro de costos
		$q = " SELECT A.mtrftr,A.mtrhtr,A.mtrfco,A.mtrhco,A.mtrhis, A.mtring "
			."	  FROM ".$whce."_000022 A,".$wmovhos."_000018 B, ".$wmovhos."_000016 C "
			."	 WHERE A.fecha_data between '$fechaini' AND '$fechafin' "  //del facturaddor para que permita sacar la diferencia en tiempo correctamente
			."     AND Mtrcci    		=  '".$cco."' "
			."	   AND mtrfco   	   != '0000-00-00' " 
			."	   AND A.mtrftr    != '0000-00-00' " 
			."	   AND A.mtrhis  		= B.ubihis "
			."	   AND A.mtring  		= B.ubiing "
			."     AND A.mtrhis  		= C.inghis "
			."     AND A.mtring  		= C.inging "
			."     AND C.ingtip  		= '".$wdetval."' " // para que muestre los ingresos que sean SOAT
			."     AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )" // para solo los pacientes que fueron atentidos
			."   ORDER BY A.mtrhis, A.mtring  ";

		$result = mysql_query($q,$conex) or die("ERROR EN QUERY $q - ".mysql_error() );
		$num = mysql_num_rows($result );
		$numacc = 0;
		$whis = "";

		//echo $q."<br/>";

		if($num>0)
		{
			//si hay ingresos creamos una tabla temporal para guardar los ingresos de accidente
			conexionOdbc($conex, $wmovhos, $conexUnix, 'facturacion');
			$q1=" DROP TABLE IF EXISTS ingreacc ";
			$result1 = mysql_query($q1,$conex) or die("ERROR EN QUERY $q1 - ".mysql_error() );
			
			$q1=" CREATE TEMPORARY TABLE IF NOT EXISTS ingreacc "
			   ."(INDEX cod_idx( mtrftr,mtrhtr ),"
			   ."mtrftr date,mtrhtr time,Mtrfco date,mtrhco time,Mtrhis varchar(80),Mtring varchar(80))";

			$result1 = mysql_query($q1,$conex) or die("ERROR EN QUERY $q1 - ".mysql_error() );

			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($result);
				$wfin = $row['mtrftr'];
				$whin = $row['mtrhtr'];
				$wfco = $row['mtrfco'];
				$whco = $row ['mtrhco'];
				$whis = $row['mtrhis'];
				$wing = $row['mtring'];

				//preguntamos en unix si el ingreso es de accidente
				$q = "SELECT *
						FROM inaccdet
					   WHERE accdethis = '".$whis."'
						 AND accdetnum = '".$wing."'";

				$res1 = odbc_do($conexUnix,$q);
				$num1 = odbc_fetch_row($res1);

				if($num1>0)
				{
					//llenamos la temporal con los ingresos de tipo accidente
					$q2 = "INSERT INTO ingreacc (mtrftr,mtrhtr,Mtrfco,Mtrhco,Mtrhis,Mtring) VALUES ('".$wfin."','".$whin."','".$wfco."','".$whco."','".$whis."','".$wing."')";
					$result2 = mysql_query($q2,$conex) or die("ERROR EN QUERY $q2 - ".mysql_error() );
				}
			}
		}

		// Consulta reposte si tabla temporal - 2012-05-08
		
		 $q1 ="SELECT A.mtrftr, SUM(UNIX_TIMESTAMP(concat(mtrfco,' ',A.mtrhco))- UNIX_TIMESTAMP(concat(A.mtrftr, ' ',A.mtrhtr))) as diferencia, COUNT(*) as pacientes"
			."  FROM ".$whce."_000022 A,".$wmovhos."_000018 B, ".$wmovhos."_000016 C "
			." WHERE UNIX_TIMESTAMP(concat(A.mtrfco,' ',A.mtrhco)) >= UNIX_TIMESTAMP(concat(A.mtrftr, ' ',A.mtrhtr)) "							   //Que la hora de atencion del medico sea mayor que la hora de entrada
			."	 AND A.fecha_data between '$fechaini' AND '$fechafin'
				 AND Mtrcci    		=  '".$cco."'
				 AND mtrfco   	   != '0000-00-00' 
				 AND A.mtrftr  != '0000-00-00' 
				 AND A.mtrhis  		= B.ubihis
				 AND A.mtring  		= B.ubiing
				 AND A.mtrhis  		= C.inghis
				 AND A.mtring  		= C.inging "
			."   AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )" // para solo los pacientes que fueron atentidos
			."   AND C.ingtip != '".$wdetval."' " // para que muestre los ingresos que no sean SOAT
			." GROUP BY 1"
			." UNION "
			."SELECT A.mtrftr, 0 as diferencia, COUNT(*) as pacientes"
			."  FROM ".$whce."_000022 A,".$wmovhos."_000018 B, ".$wmovhos."_000016 C "
			." WHERE UNIX_TIMESTAMP(concat(A.mtrfco,' ',A.mtrhco)) < UNIX_TIMESTAMP(concat(A.mtrftr, ' ',A.mtrhtr)) "
			."	 AND A.fecha_data between '$fechaini' AND '$fechafin'
				 AND Mtrcci    		=  '".$cco."'
				 AND mtrfco   	   != '0000-00-00' 
				 AND A.mtrftr  != '0000-00-00' 
				 AND A.mtrhis  		= B.ubihis
				 AND A.mtring  		= B.ubiing
				 AND A.mtrhis  		= C.inghis
				 AND A.mtring  		= C.inging "
			."   AND ( A.Mtrmed!='' AND A.Mtrmed!='NO APLICA' )" // para solo los pacientes que fueron atentidos
			."   AND C.ingtip != '".$wdetval."' " // para que muestre los ingresos que no sean SOAT
			." GROUP BY 1";
		$q2 = '';
		
		if($num>0)
		{
			$q2.="UNION"
			   ." SELECT mtrftr, SUM(UNIX_TIMESTAMP(concat(mtrfco,' ',mtrhco))- UNIX_TIMESTAMP(concat(mtrftr, ' ',mtrhtr))) as diferencia, COUNT(*) as pacientes"
			   ."    FROM ingreacc "
			   ." GROUP BY 1";
		}

		$q = "
				SELECT mtrftr, SUM(diferencia), SUM(pacientes)
				FROM
				(
					$q1
					$q2
				) as t
				GROUP BY mtrftr
				ORDER BY mtrftr ASC
			";
		
		$result = mysql_query($q, $conex) or die("ERROR EN QUERY $q - ".mysql_error() );
		$num = mysql_num_rows( $result );
		echo "<input type='hidden' id='totalfilas' value=".$num.">";
		if( $num > 0 )
		{
			echo "
					<table align='center'>
					<tr>
						<td align='center'>Tiempo calculado desde la llegada del paciente <br>hasta la atención del médico</td>
					</tr>
					</table>
			";
			echo "<table align=center >";
			echo "<tr class='encabezadoTabla'>";
			echo "<td align=center>&nbsp;Fecha</td>";
			echo "<td align=center>&nbsp;Tiempo total de atención <br/> (En minutos)</td>";
			echo "<td align=center>&nbsp;Cantidad <br> de pacientes&nbsp;</td>";
			echo "<td align=center>&nbsp;Tiempo promedio de<br> atenci&oacute;n&nbsp; (HH:mm:ss)</td>";
			echo "</tr>";
			$wclass="fila2";
			$promedio=0;
			$pac=0;
			$totalprom=0;
			$totalDatos=0;
			for($i=0; $i<$num; $i++)
			{
				$rowpro = mysql_fetch_array( $result );
				if ($wclass=="fila1")
					$wclass="fila2";
				else
					$wclass="fila1";
				
				$fecha_data=$rowpro[0];
				$promdia = $rowpro[1]; //tiempo sumado de atención
				$pacdia =  $rowpro[2];	//total de pacientes atendidos
				$promediodia = $promdia/$pacdia; //promedio de atención
				echo "<tr class='$wclass'>";
				echo "<td align=center id='fec".$i."' onclick='mostrarDetalle(".$i.")'>".$fecha_data."</td>";
				echo "<INPUT type='hidden' id='fec_da".$i."' value='$fecha_data'>";
				echo "<td align=center id='tiem".$i."' onclick='mostrarDetalle(".$i.")'>". number_format($promdia/60,2,'.',',')." </td>";
				echo "<td align=center id='pac".$i."' onclick='mostrarDetalle(".$i.")'>".number_format($pacdia,0,'',',')."</td>";
				echo "<td align=center id='dif".$i."' onclick='mostrarDetalle(".$i.")'>".date("H:i:s", $promediodia  + strtotime( "1970-01-01 00:00:00"))."</td>";
				echo "</tr>";
				echo "<tr id='trdet".$i."' style='display:none;'>";
				  echo "<td colspan=4>";	
					echo "<div id='det".$i."'></div>";
				  echo "</td>";
				echo "</tr>";
				$totalprom+=$promdia;
				$pac+=$pacdia;
				$totalDatos+=$i;
				
			
			}//for
			echo "<INPUT type='hidden' id='totalDatos' name='totalDatos' value='$totalDatos'>";
			if($pac>0)
			{
				$promedio = $totalprom/$pac; //para sacar el promedio total del rango de fechas
			}
			else
				$promedio= 0;
			//Cuando termina de recorrer el ciclo imprimo el total de todos los pacientes de ese rango de fechas
			//y el promedio total obtenido del mismo.
			echo "<tr class='titulo'>";//este class es para colocarle color al total de los datos
			echo"<td align=center>Total periodo   </td>";
			echo "<td align=center> ". number_format(($totalprom/60),2,'.',',')."</td>";
			echo "<td align=center>".number_format($pac,0,'',',')."</td>";
			echo "<td align=center>".date("H:i:s", $promedio + strtotime( "1970-01-01 00:00:00") )."</td>";
			echo "</tr></table>";
			
			echo "<br><br>";
			
			if ($cco =='1130')
			{ 
			   
				oporEspecialidad($num,$wdetval,$cco);
			}//if cco=1130
		}
		else
		{
			//si al hacer el recorrido por fechas y al verificar el centro de costos
			// no encuentra datos que imprima el mensaje.
			echo "<center><b>No se encontraron resultados</b></center>";
		}
		$bandera=1;
		
		
		echo "<br><br>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td align=center width='150'>";
		echo "<input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$fechaini."\",\"".$fechafin."\",\"".$bandera."\", \"".$cco_str[0]."\")'/>";
		echo "</td>";
		echo "<td align= center width='150'>";
		echo "<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<INPUT type='hidden' name='mostrar' value='off'>";
		echo "<INPUT type='hidden' name='fechaini' value='$fechaini'>";
		echo "<INPUT type='hidden' name='fechafin' value='$fechafin'>";
		echo "<INPUT type='hidden' id='wemp_pmla' name='wemp_pmla' value='$fechafin'>";
		echo "<INPUT type='hidden' id='wmovhos' name='wmovhos' value='$wmovhos'>";
		echo "<INPUT type='hidden' id='whce' name='whce' value='$whce'>";
		echo "<INPUT type='hidden' id='wdetval' name='wdetval' value='$wdetval'>";
		echo "<INPUT type='hidden' id='cco' name='cco' value='$cco'>";
		echo "</form>";
}
?>
</body>
</html>