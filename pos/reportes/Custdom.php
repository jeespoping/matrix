<html>
<head>
  <title>MATRIX</title>
  <style>
		.tipoTABLE1{font-family:Arial;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;}
		.tipo1{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo2{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo3{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;}
	    .tipo4{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;}
	    .tipo5{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo6{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:25em;}
	    .tipo7{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo8{color:#000066;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo9{color:#000066;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:25em;}
	    .tipo10{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo11{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:8em;}
	    .tipo12{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo13{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:2em;}
	    .tipo14{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:2em;}
	    .tipo3GRID{color:#E8EEF7;background:#E8EEF7;font-size:1pt;font-family:Arial;font-weight:bold;text-align:center;border-style:none;display:none;}
  	</style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center class=tipo3GRID>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Custodia Domiciliaria</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>Custdom.php Ver. 2016-05-12</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
//================================================================================================================\\
//Cambio Mayo 12 2016 Se cambio la Variable Pacciu(Municipio de Nacimiento) por Pacmuh (Municipio de Residencia)  \\ 
//=================================================================================================================\\
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='Custdom.php' method=post>";
		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wnum) or !isset($whis) or !isset($wing))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CUSTODIA DOMICILIARIA </td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero del Documento</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero de Historia</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero de Ingreso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wing' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$errores="";
			$ent=array();
			//Cambio Mayo 12 2016 Se cambio la Variable Pacciu(Municipio de Nacimiento) por Pacmuh (Municipio de Residencia)
			//                   0       1       2      3       4       5       6       7      8       9       10      11      12
			$query  = "select Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pacdir, Pacmuh, Nombre, Pacbar, Bardes, Ingcem, Ingent from  ".$empresa."_000101,".$empresa."_000100,root_000006,root_000034 ";
			$query .= "  where Inghis = '".$whis."' "; 
			$query .= "    and Ingnin = '".$wing."' ";
			$query .= "    and Inghis = Pachis ";
			$query .= "    and Pacmuh  = Codigo ";
			$query .= "    and Pacbar = Barcod ";
			$query .= "    and Pacmuh  = Barmun ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				echo "<center><table class=tipoTABLE1>";
				echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/logo_clisur.png'></td><td class=tipo3 colspan=7>CUSTODIA DE MEDICAMENTOS Y DISPOSITIVOS MEDICINA DOMICILIARIA</td></tr>";
				echo "<tr><td class='tipo2'>NOMBRE<br>PACIENTE</td><td class='tipo2'>DOCUMENTO</td><td class='tipo2'>NUMERO<br>HISTORIA</td><td class='tipo2'>NUMERO<br>INGRESO</td><td class='tipo2'>DIRECCION</td><td class='tipo2'>BARRIO</td><td class='tipo2'>MUNICIPIO</td><td class='tipo2'>ENTIDAD<br>RESPONSABLE</td></tr>";
				echo "<tr><td class='tipo6'>".$row[4]." ".$row[5]." ".$row[2]." ".$row[3]."</td><td class='tipo6'>".$row[0]." ".$row[1]."</td><td class='tipo5'>".$whis."</td><td class='tipo5'>".$wing."</td><td class='tipo5'>".$row[6]."</td><td class='tipo5'>".$row[10]."</td><td class='tipo5'>".$row[8]."</td><td class='tipo5'>".$row[11]." ".$row[12]."</td></tr>";
				echo "<tr><td class=tipo7 colspan=8>Se&ntilde;or(a) Usuario(a): Usted ha ingresado al Servicio Domiciliario de la Cl&iacute;nica del Sur S.A., por este motivo y con el fin de brindarle una adecuada atenci&oacute;n, le hacemos entrega de los medicamentos e insumos de salud necesarios<br> para su tratamiento, como se relaciona a continuaci&oacute;n:</td></tr>";
				echo "</table></center>";
			}
			else
				$errores="LA HISTORIA E INGRESO NO EXISTE!!!<br>";
			
			if($errores == "")
			{
				echo "<br><center><table class=tipoTABLE1>";
				echo "<tr><td class=tipo14 colspan=3>Solicitud a Pacientes Nro. : ".$wnum."<td class=tipo13 colspan=3>Fecha y Hora de Impresi&oacute;n : ".date("F j, Y, g:i a")."</td></tr>";
				echo "<tr><td class='tipo2'>ITEM</td><td class='tipo2'>NOMBRE COMERCIAL<BR>MEDICAMENTO O INSUMO</td><td class='tipo2'>CANTIDAD<br>ENVIADA</td><td class='tipo2'>CANTIDAD<br>RECIBIDA</td><td class='tipo2'>PENDIENTE</td><td class='tipo2'>OBSERVACION</td></tr>";
				//                  0       1       2  
				$query  = "select Artcod, Artnom, Mdecan ";
				$query .= "from ".$empresa."_000010,".$empresa."_000011,".$empresa."_000001";
				$query .= " where mendoc = '".$wnum."' "; 
				$query .= "   and mencon = '130' "; 
				$query .= "   and mendoc = mdedoc ";
				$query .= "   and mencon = mdecon ";
				$query .= "   and mdeart = artcod ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				$lin=0;
				for ($i=0;$i<$num;$i++)
				{
					if($i % 2 == 0)
					{
						$colorc="tipo5";
						$colorl="tipo6";
					}
					else
					{
						$colorc="tipo8";
						$colorl="tipo9";
					}
					$row = mysql_fetch_array($err);
					$item = $i + 1;
					echo "<tr>";
					echo "<td class=".$colorc.">".$item."</td>";
					echo "<td class=".$colorl.">".$row[1]."</td>";
					echo "<td class=".$colorc.">".$row[2]."</td>";
					echo "<td class=".$colorl.">&nbsp;</td>";
					echo "<td class=".$colorl.">&nbsp;</td>";
					echo "<td class=".$colorl.">&nbsp;</td>";
					echo "</tr>";
				}
				echo "<tr><td class=tipo7 colspan=6>EL USO DE ESTOS PRODUCTOS ES EXCLUSIVO POR EL PERSONAL AUTORIZADO DE LA CL&Iacute;NICA DEL SUR S.A.</td></tr>";
				echo "<tr><td class=tipo7 colspan=6>Responsabilidad: Estos medicamentos e insumos quedan bajo su custodia desde este momento, por lo cual la Cl&iacute;nica del Sur S.A. no se hace responsable por la p&eacute;rdida, da&ntilde;o o deterioro parcial o total de los mismos.</td></tr>";
				echo "<tr><td class=tipo10 colspan=6>Recomendaciones: <br> 1.Guardarlos en un lugar seguro, ventilado, lejos del calor, la luz directa y la humedad, fuera del alcance de los ni&ntilde;os.<br> 2.Cons&eacute;rvelos en el empaque original.<br> 3.Entregar diariamente al personal asistencial los insumos requeridos para la dosis a administrar</td></tr>";
				echo "<tr><td class=tipo7 colspan=6>DEVOLUCI&Oacute;N DE EXCEDENTES: En caso de que por alg&uacute;n motivo no se utilice la totalidad de los medicamentos e insumos, es obligatoria la devoluci&oacute;n del material no utilizado en perfectas condiciones al personal<br> autorizado por la Cl&iacute;nica del Sur S.A.</td></tr>";
				echo "<tr><td class=tipo7 colspan=2>Responsable Servicio Farmaceutico</td><td class=tipo7 colspan=2>Responsable de la entrega</td><td class=tipo7 colspan=2>Responsable de la custodia</td></tr>";
				echo "<tr><td class=tipo11 colspan=2>&nbsp;</td><td class=tipo11 colspan=2>&nbsp;</td><td class=tipo11 colspan=2>&nbsp;</td></tr>";
				echo "<tr><td class=tipo12 colspan=2>FIRMA</td><td class=tipo12 colspan=2>FIRMA</td><td class=tipo12 colspan=2>FIRMA</td></tr>";
				echo "</table></center>";
			}
			else
			{
				echo "<table border=0 align=center id=tipo5>";
				echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;".$errores."</td></tr>";
				echo "</table></center>";
			}
		}
	}
?>
</body>
</html>
