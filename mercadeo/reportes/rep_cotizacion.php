<html>

<head>
  <title>REPORTE PRESUPUESTO DE SERVICIOS 2015-05-22</title>
  <style type="text/css">
		.tipoTABLE1{font-family:Arial;border-style:solid;border-collapse:collapse;border-width:2px;width: 85%}
    	#tipo01{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	#tipo02{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;}
    	#tipo03{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;vertical-align: middle;width:30em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo04{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;vertical-align: middle;width:30em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo05{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;vertical-align: middle;width:30em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo06{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;vertical-align: middle;width:60em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo07{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;vertical-align: middle;width:60em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo08{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;vertical-align: middle;width:60em;border-style:solid;border-collapse:collapse;border-width:2px;}



    	#tipoG001{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:left;height:3em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:left;height:3em;}
    	#tipoG11{color:#000066;background:#99CCFF;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG51{color:#FF0000;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG52{color:#FFFFFF;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG53{color:#000066;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG61{color:#FF0000;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG62{color:#FFFFFF;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG63{color:#000066;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	#tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("root/comun.php");
/*
*********************************************************************************************************************
[DOC]
	   PROGRAMA : rep_cotizacion.php
	   Fecha de Liberacion : 2015-05-22
	   Autor : Ing. Gabriel Agudelo Zapata
	   Version Actual : 2015-05-22

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface Grafica pra la impresion del presupuesto de
	   servicios (Cotizacion)

	   REGISTRO DE MODIFICACIONES :

[*DOC]
**********************************************************************************************************************
*/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));




	echo "<form action='rep_cotizacion.php?empresa=".$empresa."&wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	if(!isset($pac))
	{
		
		$wactualiz = "2022-04-28";
		$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
		encabezado( "Paciente", $wactualiz, $institucion->baseDeDatos );
		$wlogemp = $institucion->baseDeDatos;

		if(isset($pac1))
		{
			//$query="select concat(Fecha,"-",Tip_documento,"-",Documento,"-",Paciente,"-",Historia) from ".$empresa."_000001 where Paciente like '%".$pac1."%' order by 1";
			$query="select Fecha,Documento,paciente,Nro_cotizacion from ".$empresa."_000001 where Paciente like '%".$pac1."%' or Fecha like '%".$pac1."%' or Documento like '%".$pac1."%' order by 1";
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$pac)
						echo "<option selected>".$row[0]."/".$row[1]."/".$row[2]."/".$row[3]."</option>";
					else
						echo "<option>".$row[0]."/".$row[1]."/".$row[2]."/".$row[3]."</option>";
				}
			}
			echo "</select></td></tr>";
			echo "</td></tr><input type='hidden' name='pac1' value='".$pac1."'>";
		}
		else
		{
			echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
			echo "</td></tr>";
		}
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	{
		//$pac1=substr($pac,0,strpos($pac,"-"));
		//$pac1=substr($pac,"-");
		$paciente = explode("/",$pac);

		//            0        1         2          3          4        5       6     7     8     9     10         11          12           13                     14
		//    Nro_cotizacion,Fecha,Tip_documento,Documento,Paciente,Historia,Doctor,Cups1,Cups2,Cups3,Entidad,Tipo_proced,Requiere_ctc,Honorarios_medicos,Honorarios_ayudantia,
        //             15               16          17          18              19        20        21            22           23          24         25       26            27          28          29       30       31         32
		//    Honorarios_anestesia,Honorarios2,honorarios3,Instrumentadora,Derecho_sala,Tiempo,Suministros,Det_suministros,Uso_equipos,Det_equipos,Ayudas_dx,Det_ayudas,Recuperacion,Otros_gastos,Det_otros,Total,Realizada_por,Valoriva

		$query="select Nro_cotizacion,Fecha,Tip_documento,Documento,Paciente,Historia,Doctor,Cups1,Cups2,Cups3,Entidad,Tipo_proced,Requiere_ctc,Honorarios_medicos,Honorarios_ayudantia,
                       Honorarios_anestesia,Honorarios2,Honorarios3,Instrumentadora,Derecho_sala,Tiempo,Suministros,Det_suministros,Uso_equipos,Det_equipos,Ayudas_dx,Det_ayudas,Recuperacion,Otros_gastos,Det_otros,Total,Seguridad,Valoriva
					   from ".$empresa."_000001 where Fecha = '".$paciente[0]."' and Documento='".$paciente[1]."' and Nro_cotizacion='".$paciente[3]."' ";
		//echo $query."<br>";
		$wactualiz = "2022-04-04";
		$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
		encabezado( "PRESUPUESTO DE SERVICIOS", $wactualiz, $institucion->baseDeDatos );
		$wlogemp = $institucion->baseDeDatos;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
			$row = mysql_fetch_array($err);
		echo "<table border=0 class='tipoTABLE1'>";
			echo "<tr><td id='tipo04'><center><table border=0>";
				//echo "<img SRC='/MATRIX/images/medical/mercadeo/logoclinica.JPG' width='127' height='65'></td>";
				echo "<tr><td id='tipo01'>FA-GC-01-04</td></tr>";
				echo "<tr><td id='tipo01'>V-2</td></tr>";
				echo "<tr><td id='tipo01'>".$institucion->nombre." NIT ".$institucion->nit."</td></tr>";
				echo "<tr><td id='tipo01'>PRESUPUESTO DE SERVICIOS</td></tr>";
			echo "</center></table>";
		//echo "<td align=center><img SRC='/MATRIX/images/medical/mercadeo/logoclinica.JPG' width='127' height='65'></td>";
		echo "<td align=center> <img src='../../images/medical/root/".$wlogemp.".jpg' width=120 heigth=76> </td>";
		$line1 = "FECHA:&nbsp".$row[1];
		$tipdoc = explode("-",$row[2]);
		$line2 = "DOCUMENTO:&nbsp".$tipdoc[0]."-".$row[3];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "PACIENTE:&nbsp".$row[4];
		$line2 = "HISTORIA CLINICA:&nbsp".$row[5];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "MEDICO:&nbsp ".$row[6];
		if ($row['Entidad'] != "NO APLICA")
			{
				$line2 = "ENTIDAD:&nbsp ".$row[10];
			}
		else
			{
				$line2 = "ENTIDAD: PARTICULAR";
			}
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "CUPS:&nbsp ".$row[7];
		if ($row['Cups1'] != "NO APLICA")
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
		$line1 = "CUPS:&nbsp ".$row[8];
		if ($row['Cups2'] != "NO APLICA")
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
		$line1 = "CUPS:&nbsp ".$row[9];
		if ($row['Cups3'] != "NO APLICA")
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
		$tiproc = explode("-",$row[11]);
		$reqctc = explode("-",$row[12]);
		$line1 = "TIPO DE PROCEDIMIENTO:&nbsp&nbsp".$tiproc[1];
		$line2 = "REQUIERE CTC:&nbsp&nbsp".$reqctc[1];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "DESCRIPCION DE SERVICIOS ";
		$line2 = "VALOR";
		echo "<tr><td id='tipoG001'>".$line1."</td><td id='tipoG001'>".$line2."</td></tr>";
		$line1 = "HONORARIOS MEDICOS ";
		//$line2 = "$".$row[13];
		if ($row['13'] != 'DATO NO ENCONTRADO')
			$line2 = $row[13];
		else
			$line2 = 0;
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		$line1 = "HONORARIOS AYUDANTIA ";
		$line2 = $row[14];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		$line1 = "HONORARIOS ANESTESIA ";
		$line2 = $row[15];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		if ($row['Honorarios2'] != 0)
		{
			$line1 = "HONORARIOS 2";
			$line2 = $row[16];
			echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		}
		if ($row['Honorarios3'] != 0)
		{
			$line1 = "HONORARIOS 3";
			$line2 = $row[17];
			echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		}
		$line1 = "INSTRUMENTADORA ";
		$line2 = $row[18];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		$tiempo = explode("-",$row[20]);
		$line1 = "DERECHOS DE SALA - TIEMPO: ".$tiempo[1];
		$line2 = $row[19];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		$line1 = "SUMINISTROS(MEDICAMENTOS E INSUMOS)";
		$line2 = $row[21];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		$line1 = "USO DE EQUIPOS";
		$line2 = $row[23];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		$line1 = "AYUDAS DIAGNOSTICAS";
		$line2 = $row[25];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		$line1 = "RECUPERACION";
		$line2 = $row[27];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		$line1 = "OTROS GASTOS";
		$line2 = $row[28];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
		if ($row['Valoriva'] != 0)
			{
				$line1 = "VALOR IVA";
				$line2 = $row[32];
				echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
			}
		if ($row['Entidad'] != "NO APLICA")
			{
				$line1 = "VALOR APROXIMADO ";
				$line2 = $row[13]+$row[14]+$row[15]+$row[16]+$row[17]+$row[18]+$row[19]+$row[21]+$row[23]+$row[25]+$row[27]+$row[28]+$row[32];
				echo "<tr><td id='tipoG001'>".$line1."</td><td id='tipoG001'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
			}
		else
			{
				$line1 = "VALOR APROXIMADO CLINICA SIN VALOR ANESTESIA";
				$line2 = $row[13]+$row[14]+$row[16]+$row[17]+$row[18]+$row[19]+$row[21]+$row[23]+$row[25]+$row[27]+$row[28]+$row[32];
				echo "<tr><td id='tipoG001'>".$line1."</td><td id='tipoG001'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
				$line1 = "EL VALOR DE LA ANESTESIA SE CANCELA EN SALAM ";
				$line2 = $row[15];
				echo "<tr><td id='tipoG001'>".$line1."</td><td id='tipoG001'>$&nbsp".number_format($line2,0,'.',',')."</td></tr>";
			}
		$line1 = "DETALLE SUMINISTROS:&nbsp ".$row[22];
		if ($row['Det_suministros'] != ".")
			echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
		$line1 = "DETALLE USO DE EQUIPOS:&nbsp ".$row[24];
		if ($row['Det_equipos'] != ".")
			echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
		$line1 = "DETALLE AYUDAS DIAGNOSTICAS:&nbsp ".$row[26];
		if ($row['Det_ayudas'] != ".")
			echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
		$line1 = "DETALLE OTROS GASTOS:&nbsp ".$row[29];
		if ($row['Det_otros'] != ".")
			echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
		$line1 = "SERVICIOS QUE NO INCLUYE:&nbsp Gastos hospitalarios segun requerimientos del paciente, complicaciones,uso de sangre y/o hemoderivados, examenes prequirurgicos, interconsultas con otros especialistas, servicios adicionales y/o insumos no especificados en este presupuesto ";
			echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
		$line1 = "SOBRE CIRUGIAS ESTETICAS:&nbsp De acuerdo con el Art. 10 de la Ley 1943 de 2018.  Las cirugías estéticas diferentes de aquellas cirugías plásticas reparadoras o funcionales, de conformidad con las definiciones adoptadas por el Ministerio de Salud y Protección Social, quedan sometidas al impuesto sobre las ventas –IVA.";
			echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
		$line1 = "OBSERVACIONES GENERALES:";
			echo "<tr><td id='tipoG001' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
		if ($row['Entidad'] != "NO APLICA")
			{
				$line1 = "Primera: Los excedentes por estancias y servicios no relacionados en este presupuesto, en caso de requerirse, se facturaran a tarifa institucional, hasta el alta del paciente. estas tarifas tienen conceptos propios y no corresponden a ningun manual tarifario.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Segunda: Es importante que el reponsable del pago, tenga claridad sobre este documento en lo relacionado con la informacion que aqui se suministra. Esto es, el presupuesto es un documento guia sobre el cual se pueden presentar cambios. En lo relacionado con los medicamentos y dispositivos medicos, las cantidades presupuestadas estan sujetas a cambios, segun hallazgos intraoperatorios.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Tercera: Los valores adicionalesa este presupuesto que se generen en la prestacion del servicio, deberan ser asumidos integralmente por la Entidad responsable de la aceptacion de esta";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Cuarta: En caso de ser aceptado el presente presupuesto, se debera enviar orden de servicio a Nombre de Promotora Medica Las Americas, especificando que se aceptan los terminos del presupuesto y adjuntando copia de este debidamente firmado. Ambos documentos deben ser firmados por un funcionario con facultades para comprometer y obligar a la Entidad.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Quinta: Este presupuesto tiene validez de un (1) mes despues de haber sido entregado a la Entidad pagadora, para ser emitida la orden de servicio y notificada a la clinica y  tres meses para hacerse efectivo el servicio, a partir de esta fecha, se debera realizar nuevo presupuesto, que estaria sujeto a renegociacion y aceptacion entre las partes.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Sexta: En caso de no tener contrato con Clinica Las Americas, se debera consignar con anticipacion el valor total de este presupuesto, en la cuenta de ahorros BANCOLOMBIA # 1023-2521708 a nombre de Promotora Medica Las Americas y enviar el documento de consignacion al Fax 3420936 o escaneado al correo electronico presupuestosdeservicios@lasamericas.com.co, indicando los datos del paciente que fue autorizado.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
			}
		else
			{
				$line1 = "Primera: Los servicios NO relacionados en este presupuesto, en caso de requerirse, se facturaran a Tarifa Institucional. Es importante que tenga presente que LOS HONORARIOS MEDICOS, se le deben cancelar directamente al especialista, por tal razon no se encuentran determinados en este presupuesto.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Segunda: Es importante que el responsable del pago, tenga claridad sobre este documento en lo relacionado con la informacion que aqui se suministra. Esto es, el presupuesto es un documento guia sobre el cual se puede presentar cambios. En lo relacionado con los medicamentos y dispositivos medicos, las cantidades presupuestadas estan sujetas a cambios, segun hallazgos intraoperatorios.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Tercera: Este presupuesto tiene validez de un (1) mes despues de haber sido entregado, a partir de esta fecha, se debera realizar nuevo presupuesto.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Cuarta: Se debera consignar con anticipacion el valor correspondiente al TOTAL CLINICA de este presupuesto, en la cuenta de Ahorros BANCOLOMBIA N° 1023-2521708  a nombre de Promotora Medica Las Americas y enviar el documento de consignacion al Fax 342-09-36 o escaneado al correo electronico presupuestosdeservicios@lasamericas.com.co, indicando los datos completos del paciente.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Quinta:  El valor correspondiente a la Anestesia, debera ser cancelado directamente en la oficina de SALAM, primer piso de la Clinica las Americas o en el numero de cuenta BANCOLOMBIA AHORROS 10232557166.";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";
				$line1 = "Sexta: Este presupuesto NO es valido para ninguna entidad de planes de beneficio, como EPS,MP,Aseguradora, Etc. En caso de que el pagador final sea una de estas entidades, debera comunicarse con el area de convenios y definir nuevos acuerdos. Correo electronico presupuestosdeservicios@lasamericas.com.co";
					echo "<tr><td id='tipo08' colspan=2>".str_replace( "\n", "<br>", ($line1)  )."</td></tr>";

			}
		$realizada = explode("-",$row[31]);
		$query1="select Descripcion
					   from usuarios where Codigo = '".$realizada[1]."' ";
		//echo $query."<br>";
		$err1 = mysql_query($query1,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1>0)
			$row1 = mysql_fetch_array($err1);
		$line1 = "REALIZADA POR:&nbsp ".$row1[Descripcion];
			echo "<tr><td id='tipoM01' colspan=2>".$line1."</td></tr>";
	echo "</table>";
	}
	include_once("free.php");
}
?>
