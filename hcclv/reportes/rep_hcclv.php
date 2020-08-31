<html>

<head>
  <title>HISTORIA GINECOLOGIA ONCOLOGICA 2014-01-21</title>
  <style type="text/css">
		.tipoTABLE1{font-family:Arial;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo01{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;}
    	#tipo02{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;}
    	#tipo03{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;vertical-align: middle;width:30em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo04{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;vertical-align: middle;width:30em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo05{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;vertical-align: middle;width:30em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo06{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;vertical-align: middle;width:60em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	#tipo07{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;vertical-align: middle;width:60em;border-style:solid;border-collapse:collapse;border-width:2px;}
    	
    	
    	
    	
    	#tipoG001{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
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
/*
*********************************************************************************************************************  
[DOC]
	   PROGRAMA : rep_hcclv.php
	   Fecha de Liberacion : 2014-01-21
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2014-01-21
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface Grafica pra la impresion de la Historia Clinica
	   de Ginecologia Oncologica.
	   
	   REGISTRO DE MODIFICACIONES :

	   .2014-01-21
	   		Release de Version Beta.
	   
[*DOC]   		
**********************************************************************************************************************
*/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='rep_hcclv.php?empresa=.".$empresa."' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($medico)  or !isset($pac) or !isset($fecha1) or !isset($year) )
	{
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>TORRE MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>IMPRESION DE HISTORIA CLINICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
		$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = '".$empresa."' AND codigo = '002'  order by Descripcion ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				if (($row[0]."-".$row[1]) == $medico)
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}	
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
	
		if(isset($medico) and isset($pac1))
		{
			$query="select distinct Paciente from ".$empresa."_000002 where Doctor='".$medico."'and Paciente like '%".$pac1."%' order by Paciente";
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if($row[0]==$pac)
						echo "<option selected>".$row[0]."</option>";
					else
						echo "<option>".$row[0]."</option>";
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
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESDE: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='fecha1'>";

		if(isset($pac))
		{
			$query = "select Fecha  from ".$empresa."_000002 where Paciente='".$pac."' and Doctor='".$medico."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			
			if($num>0)
			{
				for ($j=0;$j<=$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					$fec[$j]=$row[0];
					echo "<option>".$row[0]."</option>";
				}
				
				echo "</select></td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>HASTA: </font></td>";	
				echo "<td bgcolor=#cccccc colspan=2>";
				if(!isset($year))
				{
					$year=date('Y');
					$month=date('m');
					$day=date('d');
				}
				echo "<select name='year'>";
				for($f=1980;$f<2051;$f++)
				{
					if($f == $year)
						echo "<option selected>".$f."</option>";
					else
						echo "<option>".$f."</option>";
				}
				echo "</select><select name='month'>";
				for($f=1;$f<13;$f++)
				{
					if($f == $month)
						if($f < 10)
							echo "<option selected>0".$f."</option>";
						else
							echo "<option selected>".$f."</option>";
					else
						if($f < 10)
							echo "<option>0".$f."</option>";
						else
							echo "<option>".$f."</option>";
				}
				echo "</select><select name='day'>";
				for($f=1;$f<32;$f++)
				{
				if($f == $day)
					if($f < 10)
						echo "<option selected>0".$f."</option>";
					else
						echo "<option selected>".$f."</option>";
				else
					if($f < 10)
						echo "<option>0".$f."</option>";
					else
							echo "<option>".$f."</option>";
				}
				echo "</select></td></tr>";
				
			}
			
		}
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	{
		$pac1=substr($pac,0,strpos($pac,"-"));
		//                 0           1       2       3          4        5           6          7         8            9            10     11       12          13               14     15             16                   17             18              19                 20               21        
		$query="select Nro_historia,Nombre1,Nombre2,Apellido1,Apellido2,Documento,Tip_documento,Correo,Estado_civil,Fecha_nacimiento,Sexo,Ocupacion,Direccion,Lugar_residencia,Telefono,Entidad,Cotizante_beneficiario,Nom_acompanante,Tel_acompanante,Persona_responsable,Tel_responsable,Parentesco from ".$empresa."_000001 where Documento='".$pac1."' ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
			$row = mysql_fetch_array($err);
		echo "<table border=0 class='tipoTABLE1'>";
		echo "<tr><td id='tipo04'><center><table border=0>";
		echo "<tr><td id='tipo01'>Dra. Carolina Lopez Vallejo</td></tr>";
		echo "<tr><td id='tipo02'>Gineco Obstetra - CES</td></tr>";
		echo "<tr><td id='tipo02'>Reg.: 05-2540-05</td></tr>";
		echo "</center></table>";
		echo "</td><td id='tipo03'>HISTORIA CLINICA : ".$row[0]."</td></tr>";
		$line1 = "PACIENTE: ".$row[1]." ".$row[2]." ".$row[3]." ".$row[4];
		$line2 = "C.C ".$row[5];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "FECHA NACIMIENTO: ".$row[9];
		$line2 = "OCUPACION: ".$row[11];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "ESTADO CIVIL: ".$row[8];
		$line2 = "SEXO: ".$row[10];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "DIR. PACIENTE: ".$row[12];
		$line2 = "ENTIDAD: ".substr($row[15],strpos($row[15],"-")+1);
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "LUGAR RESIDENCIA: ".$row[13];
		$line2 = "TIPO VINCULACION: ".substr($row[16],strpos($row[16],"-")+1);
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "TELEFONOS: ".$row[14];
		$line2 = "RESPONSABLE: ".$row[19]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TEL: ".$row[20]."&nbsp;&nbsp;PARENTESCO: ".$row[21];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		$line1 = "ACOMPA&Ntilde;ANTE: ".$row[17];
		$line2 = "TEL. ACOMP: ".$row[18];
		echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
		echo "<tr><td id='tipo06' colspan=2>ANTECENDENTES GINECOOBST&Eacute;TRICOS</td></tr>";
		$fecha2 = $year."-".$month."-".$day;
		//               0      1     2        3       4     5      6        7       8        9     10  11    12     13  14  15   16     17      18           19          20        21             22             23          24         25         26              27             28           29     30
		$query="select Fecha,Doctor,Paciente,Edad,Menarquia,Ciclos,Fum,Gravideces,Paridad,Abortos,Cesareas,Pf,Pemb,Fuparto,Lact,Fuc,Trh,Mamog,Fumam,Antec_person,Antec_patolog,Medicam,Antec_quirurg,Antec_alergicos,Tabaquismo,Antec_familiar,Mc_ea,Examen_fisico,Diagnostico_cie,Diagnostico_otros,Conducta from ".$empresa."_000002 where Doctor='".$medico."' and Paciente='".$pac."' and Fecha between '".$fecha1."' and '".$fecha2."' ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$line1 = "EDAD DEL PACIENTE EN EL MOMENTO DE LA CONSULTA: ".$row[3];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Menarquia: ".$row[4]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ciclos: ".$row[5]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>FUM: ".$row[6]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Gravideces: ".$row[7];
			$line2 = "Paridad: ".$row[8]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Abortos: ".$row[9]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;CX: ".$row[10]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>Planificaci&oacute;n Familiar: ".$row[11];
			echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
			$line1 = "Primer Embarazo: ".$row[12]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FU. PARTO: ".$row[13];
			$line2 = "Lactancia: ".$row[14]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;F.U.Citolog&iacute;a: ".$row[15];
			echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
			$line1 = "T.R. HORMONAL: ".$row[16];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Mamograf&iacute;as: ".$row[17];
			$line2 = "F.U. Mamograf&iacute;a: ".$row[18];
			echo "<tr><td id='tipo05'>".$line1."</td><td id='tipo05'>".$line2."</td></tr>";
			$line1 = "ANTECEDENTES PERSONALES: ".$row[19];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Antecedentes Patol&oacute;gicos: ".$row[20];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Medicamentos: ".$row[21];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Antecedentes Quir&uacute;rgicos: ".$row[22];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Antecedentes Alerg&iacute;cos: ".$row[23];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Tabaquismo: ".$row[24];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "ANTECEDENTES FAMILIARES: ".$row[25];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "FECHA: ".$row[0];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Motivo Consulta Y Enfermedad Actual: <br>".$row[26];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Examen F&iacute;sico: <br>".$row[27];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Diagnosticos CIE10: <br>".$row[28];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Otros Diagnosticos: <br>".$row[29];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			$line1 = "Conducta: <br>".$row[30];
			echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
		}
		echo "<tr><td id='tipo06' colspan=2>SEGUIMIENTOS</td></tr>";
		$query="select Fecha,Seguimiento from ".$empresa."_000003 where Doctor='".$medico."' and Paciente='".$pac."' and Fecha between '".$fecha1."' and '".$fecha2."'  ORDER BY Fecha Asc ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$line1 = "FECHA: ".$row[0];
				echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
				$line1 = "DESCRIPCI&Oacute;N ".$row[1];
				echo "<tr><td id='tipo07' colspan=2>".$line1."</td></tr>";
			}
		}
		echo "<br>";
		//echo "<table align=center border=1 width=725>";
		echo "<tr><td colspan='4'><font size=1 face='arial' ><B>FIRMADO ELECTRONICAMENTE POR:<b>DRA. CAROLINA LOPEZ VALLEJO &nbsp&nbsp&nbsp&nbsp REGISTRO:05-2540-05</B></td></tr>";
		//echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/".$key.".png ' width='140' height='90'></td></tr>";
		echo "<tr><td colspan='4'><img SRC='/matrix/images/medical/hce/Firmas/0800338.png ' width='140' height='90'></td></tr>";
		echo "</table>";
	}
	include_once("free.php");
}
?>
