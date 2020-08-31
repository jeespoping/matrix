<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR=''>
<BODY TEXT='#000066'>
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.citas.submit();
	}
	function historia(variable1,variable2)
   {
   		window.open('/matrix/soe/procesos/odontograma.php?empresa='+variable2+'&paciente='+variable1+'&DIV=1','','fullscreen=1,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0');
   }
//-->
</script>
<?php
include_once("conex.php");
echo "<center>";
if (!isset($wfec))
	$wfec=date("Y-m-d");
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=6><b>Citas Medicos</b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> 000001_prx5.php Ver. 2006-11-20</b></font></tr></td></table>";
echo "</center>";
echo "<form name='citas' action='000001_prx5.php' method=post>";
@session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($wesp))
		echo "<input type='HIDDEN' name= 'wesp' value='".$wesp."'>";
	if(isset($wsw1))
		echo "<input type='HIDDEN' name= 'wsw1' value='".$wsw1."'>";
	if(isset($wemp_pmla))
		echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";

	$manejaSedes = false;
	if($empresa == "citasoe")
	{
		$query = " SELECT detval
		             FROM root_000051
		            WHERE detapl = 'manejaSedes'
		              AND detemp = '{$wemp_pmla}'";
		$rs    = mysql_query( $query, $conex );
		$row   = mysql_fetch_assoc( $rs );
		if( $row['detval'] == "on"){
			$manejaSedes = true;
		}
		$IPOK=0;
		$query = "select Dipnip, Dipusu from root_000095 ";
		$query .= " where Dipest = 'on'";
		$err_ip = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num_ip = mysql_num_rows($err_ip);
		if ($num_ip>0)
		{
			for ($h=0;$h<$num_ip;$h++)
			{
				$rowip = mysql_fetch_array($err_ip);
				if(($rowip[0] == substr($IIPP,0,strlen($rowip[0])) and $key == $rowip[1]) or ($rowip[0] == substr($IIPP,0,strlen($rowip[0])) and $rowip[1] == "*"))
				{
					$IPOK=1;
					$i=$num_ip+1;
				}
			}
		}
	}
	else
		$IPOK = 1;

	if($IPOK > 0)
	{
		if (isset($Ano) and isset($Mes) and isset($Dia))
		{
			if ($Mes < "10")
				$Mes = "0".$Mes;
			if ($Dia < "10")
				$Dia = "0".$Dia;
			$numDaysInMonth = date("t", mktime(0, 0, 0, (integer)$Mes, 1, (integer)$Ano));
			if((integer)$Dia > $numDaysInMonth)
				$Dia = $numDaysInMonth;
			$wfec = $Ano."-".$Mes."-".$Dia;
		}
		else
		{
			$wfec1 = (string)$wfec;
			$Ano = substr($wfec1,0,4);
			$Mes = substr($wfec1,5,2);
			$Dia = substr($wfec1,8,2);
		}
		$year = (integer)substr($wfec,0,4);
		$month = (integer)substr($wfec,5,2);
		$day = (integer)substr($wfec,8,2);
		$nomdia=mktime(0,0,0,$month,$day,$year);
		$nomdia = strftime("%w",$nomdia);
		$wsw=0;
		switch ($nomdia)
		{
			case 0:
				$diasem = "DOMINGO";
				break;
			case 1:
				$diasem = "LUNES";
				break;
			case 2:
				$diasem = "MARTES";
				break;
			case 3:
				$diasem = "MIERCOLES";
				break;
			case 4:
				$diasem = "JUEVES";
				break;
			case 5:
				$diasem = "VIERNES";
				break;
			case 6:
				$diasem = "SABADO";
				break;
		}
		$wccoUrl      = ( isset( $wcco ) ) ? "&wcco=".$wcco : "";
		$wemp_pmlaUrl = ( isset( $wemp_pmla ) ) ? "&wemp_pmla=".$wemp_pmla : "";
		echo "<table border=0 align=center cellpadding=6>";
		echo "<tr><td align=center colspan=9><IMG SRC='/matrix/images/medical/Citas/logo_".$empresa.".png'></td></tr>";
		if( $manejaSedes or isset( $wcco ) ){
			if( isset( $wemp_pmla ) ){
				$sql = " SELECT Detval
						   FROM root_000051
						  WHERE detemp = '{$wemp_pmla}'
						    AND detapl = 'facturacion'";
				$rs  = mysql_query( $sql, $conex );
				$row = mysql_fetch_assoc( $rs );
				$wbasedato = $row['Detval'];

				$queryCcoUsuario = " SELECT usscco
				                       FROM {$wbasedato}_000318
				                      WHERE usscod = '{$key}'";
	            $rsCcoUsuario  = mysql_query( $queryCcoUsuario, $conex );
                $rowCcoUsuario = mysql_fetch_assoc($rsCcoUsuario);
                if( $rowCcoUsuario['usscco'] != "" ){
                    $wccoUsuario = $rowCcoUsuario['usscco'];
                }

				echo "<tr><td  align=center bgcolor='#cccccc' center colspan=8><b>Sede : </b>";
				$query = " SELECT Ccocod as cco, ccodes as descripcion
				             FROM {$wbasedato}_000003
				            WHERE Ccotip = 'A'";
				$rs    = mysql_query( $query, $conex );
				echo "<select name='wcco'>";
				while( $row2 = mysql_fetch_assoc( $rs ) ){
					$selected = ( ( !isset($wcco) and $wccoUsuario == $row2['cco'] ) or (isset($wcco) and $wcco == $row2['cco'] ) ) ? "selected" : "";
					echo "<option value='{$row2['cco']}' {$selected}>".$row2['cco']."-".preg_replace('/\FACTURACION \b/u', '', $row2['descripcion'])."</option>";
				}
				echo "</select>";
				echo "</td><td align=center bgcolor='#cccccc'>&nbsp;</td></tr>";
			}
		}
		echo "<tr><td  align=center bgcolor='#cccccc' center colspan=8><b>Medico : </b>";
		if(isset($wesp))
			$query = "select Codigo, Descripcion from ".$empresa."_000010,".$empresa."_000015 where Activo = 'A'  and Codigo = Relcom and Relmat = '".$key."' Group by Codigo, Descripcion Order by Descripcion";
		else
			$query = "select Codigo, Descripcion from ".$empresa."_000010 where Activo = 'A' Group by Codigo, Descripcion Order by Descripcion";

		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			echo "<select name='wequ' onchange='enter()'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if (isset($wequ) and $wequ == $row[0]."-".$row[1])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select><td bgcolor='#cccccc'  align=center><b>BUSQUEDA : </b><A HREF='/MATRIX/Citas/Reportes/000001_rrx4.php?empresa=".$empresa."' target='_blank'><IMG SRC='/MATRIX/images/medical/Citas/find.gif'></A></td></tr>";
		echo "<tr><td bgcolor='#cccccc'><b>Fecha :</b></td>";
		echo "<td bgcolor='#cccccc'><font size=3><b>".$diasem."  ".$wfec."</b></font></td>";
		echo "<td bgcolor='#cccccc'><b>A&ntilde;o :</b><td bgcolor='#cccccc'>";
		echo "<select name='Ano' onchange='enter()'>";
		for ($i=2003;$i<2051;$i++)
		{
			if (isset($Ano) and $Ano == (string)$i)
			{
				echo "<option selected>".$i."</option>";
			}
			else
				echo "<option>".$i."</option>";
		}
		echo "</td><td bgcolor='#cccccc'><b>Mes :</b><td bgcolor='#cccccc'>";
		echo "<select name='Mes' onchange='enter()'>";
		for ($i=1;$i<13;$i++)
		{
			if (isset($Mes) and $Mes == (string)$i)
				echo "<option selected>".$i."</option>";
			else
				echo "<option>".$i."</option>";
		}
		echo "</td><td bgcolor='#cccccc'><b>Dia :</b><td bgcolor='#cccccc'>";
		echo "<select name='Dia' onchange='enter()'>";
		for ($i=1;$i<32;$i++)
		{
			if (isset($Dia) and $Dia == (string)$i)
				echo "<option selected>".$i."</option>";
			else
				echo "<option>".$i."</option>";
		}
		echo "<input type='HIDDEN' name= 'wfec' value='".$wfec."'>";
		echo "<td bgcolor='#cccccc'><input type='submit' value='IR'>";
		echo "</td></tr></table><br>";
		if(isset($wequ))
		{
			$condicionCco = ( $manejaSedes ) ? "and centroCostos = '$wcco'": "";
			$query = " select Uni_hora, Hi, Hf, Consultorio  from ".$empresa."_000012 ";
			$query .= " where Fecha_I <= '".$wfec."'";
			$query .= "   and Fecha_F >= '".$wfec."'";
			$query .= "   and Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
			$query .= "   {$condicionCco}";
			$query .= "   and Activo = 'A' ";

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$wk=0;
			if ($num > 0)
			{
				$wk=1;
				$row = mysql_fetch_array($err);
			}
			if($num == 0)
			{
				$query = "select Uni_hora, Hi, Hf, Consultorio
				            from ".$empresa."_000014 ";
				$query .= "where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
				$query .= "  and Fecha = '".$wfec."'
				             {$condicionCco}";
				$query .= "  Group by Codigo ";

				$err = mysql_query($query,$conex);

				$num = mysql_num_rows($err);
				if($num == 0)
				{
					$wk=2;
					$query = "select Uni_hora, Hi, Hf, Consultorio
					            from ".$empresa."_000010 ";
					$query .= "where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
					$query .= "  and Dia = ".$nomdia;
					$query .= "  and Activo = 'A'
								 {$condicionCco}";
					$query .= "  Group by Codigo ";

					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
				}
			}
			if($wk == 1 and $row[0] == 0)
				$num=0;
			if ($num > 0)
			{
				if($wk==0 or $wk==2)
					$row = mysql_fetch_array($err);
				if($wk==2)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$campoCco = ( $manejaSedes ) ? " centroCostos, " : "";
					$valorCco = ( $manejaSedes ) ? " '{$wcco}', " : "";
					$query = "insert ".$empresa."_000014 (medico,fecha_data,hora_data, Codigo, Fecha, Uni_hora, Hi, Hf, Consultorio , {$campoCco} seguridad) values ('".$empresa."','".$fecha."','".$hora."','".substr($wequ,0,strpos($wequ,"-"))."','".$wfec."',".$row[0].",'".$row[1]."','".$row[2]."','".$row[3]."',{$valorCco}'C-".$empresa."')";

					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				}
				$whi = $row[1];
				$wul = $row[2];
				$inc = $row[0];
				$part1 = (int)substr($whi,0,2);
				$part2 = (int)substr($whi,2,2);
				$part2 = $part2 + $inc;
				while ($part2 >= 60)
				{
					$part2 = $part2 - 60;
					$part1 = $part1 + 1;
				}
				$whf = (string)$part1.(string)$part2;
				if ($part1 < 10)
					$whf = "0".$whf;
				if ($part2 < 10)
					$whf = substr($whf,0,2)."0".substr($whf,2,1);
				if(isset($wesp))
					$ncol=10;
				else
					$ncol=9;
				$color="#999999";
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=".$color." colspan=".$ncol." align=center><font size=3><b>CONSULTORIO : ".$row[3]."</b></font></td></tr>";
				echo "<tr><td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Hora Final</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Edad</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Estado</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Seleccion</b></font></td>";
				if(isset($wesp))
					echo "<td bgcolor=".$color."><font size=2><b>Historia</b></font></td></tr>";
				$r = 0;
				$i = 0;
				$fila = 0;
				$query = "select cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_res,telefono,edad,comentario,usuario,activo,Asistida,Cedula from ".$empresa."_000009 where fecha='".$wfec."' and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."' {$condicionCco} order by hi";

				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num > 0)
					$row = mysql_fetch_array($err);
				while ($whi < $wul)
				{
					$r = $i/2;
					if ($r*2 === $i)
						$color="#CCCCCC";
					else
						$color="#999999";
					if(strlen($row[0]) == 1 and $row[0] == "0"and $num > 0 and $row[3] == $whi)
							$color="#99ccff";
					echo "<tr>";
					if(substr($whi,0,2) > "12")
					{
						$hr1 ="". (string)((integer)substr($whi,0,2) - 12).":".substr($whi,2,2). " pm ";
						echo "<td bgcolor=".$color." align=center><font size=2>".$hr1."</font></td>";
					}
					else
						echo "<td bgcolor=".$color." align=center><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></td>";
					if ($num > 0 and $row[3] == $whi)
						$whf=$row[4];
					if(substr($whf,0,2) > "12")
					{
						$hr2 ="". (string)((integer)substr($whf,0,2) - 12).":".substr($whf,2,2). " pm ";
						echo "<td bgcolor=".$color." align=center><font size=2>".$hr2."</font></td>";
					}
					else
						echo "<td bgcolor=".$color." align=center><font size=2>".substr($whf,0,2).":".substr($whf,2,2)."</font></td>";
					if ($num > 0 and $row[3] == $whi)
					{
						$query = "select codigo,descripcion,preparacion,cod_equipo,activo from ".$empresa."_000011 where codigo='".$row[1]."'";

						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$row2 = mysql_fetch_array($err1);
						echo "<td bgcolor=".$color."><font size=2>".$row2[1]."</font></td>";
						echo "<td bgcolor=".$color."><font size=2>".$row[5]."</font></td>";
						$query = "select nit,descripcion from ".$empresa."_000002 where nit='".$row[6]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$row3 = mysql_fetch_array($err1);
						echo "<td bgcolor=".$color."><font size=2>".$row3[0]."-".$row3[1]."</font></td>";
						echo "<td bgcolor=".$color."><font size=2>".$row[7]."</font></td>";
						echo "<td bgcolor=".$color."><font size=2>".$row[8]."</font></td>";
						switch ($row[11])
						{
							case "A":
								if($row[12] == "on")
									echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/asistida1.gif' ></td>";
								else
									echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
							break;
							case "I":
								echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/inactivo.gif' ></td>";
							break;
							default:
								echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
							break;
						}
						if(isset($wsw1))
							if(isset($wesp))
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx6.php?pos2=".$wequ."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$row[3]."&amp;pos6=".$row[4]."&amp;pos7=".$wul."&amp;pos8=".$row[8]."&amp;pos9=".$inc."&amp&amp;empresa=".$empresa."&amp;wsw1=".$wsw1."&amp;wesp=".$wesp."{$wccoUrl}{$wemp_pmlaUrl}'>Editar</font></td>";
							else
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx6.php?pos2=".$wequ."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$row[3]."&amp;pos6=".$row[4]."&amp;pos7=".$wul."&amp;pos8=".$row[8]."&amp;pos9=".$inc."&amp&amp;empresa=".$empresa."&amp;wsw1=".$wsw1."{$wccoUrl}{$wemp_pmlaUrl}'>Editar</font></td>";
						else
							if(isset($wesp))
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx6.php?pos2=".$wequ."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$row[3]."&amp;pos6=".$row[4]."&amp;pos7=".$wul."&amp;pos8=".$row[8]."&amp;pos9=".$inc."&amp&amp;empresa=".$empresa."&amp;wesp=".$wesp."{$wccoUrl}{$wemp_pmlaUrl}'>Editar</font></td>";
							else
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx6.php?pos2=".$wequ."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$row[3]."&amp;pos6=".$row[4]."&amp;pos7=".$wul."&amp;pos8=".$row[8]."&amp;pos9=".$inc."&amp&amp;empresa=".$empresa."{$wccoUrl}{$wemp_pmlaUrl}'>Editar</font></td>";
						if(isset($wesp))
						{
							$query = "select count(*) from ".$wesp."_000100 where Pacdoc='".$row[13]."' and Pacact='on' ";
							$err3 = mysql_query($query,$conex);
							$row3 = mysql_fetch_array($err3);
							if($row3[0] > 0)
							{
								$enlace='historia("'.$row[13].'","'.$wesp.'")';
								echo "<td bgcolor=".$color." align=center><button type='button' onclick='".$enlace."'>Ir</button></td></tr>";
							}
							else
								echo "<td bgcolor=".$color." align=center>Sin Ingreso</td></tr>";
						}
						else
							echo "</tr>";
						$row = mysql_fetch_array($err);
						$fila = $fila + 1;
					}
					else
					{
						$wsw=0;
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
						if(isset($wsw1))
							if(isset($wesp))
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx6.php?pos2=".$wequ."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=0&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;wsw1=".$wsw1."&amp;wesp=".$wesp."{$wccoUrl}{$wemp_pmlaUrl}'>Editar</font></td>";
							else
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx6.php?pos2=".$wequ."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=0&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;wsw1=".$wsw1."{$wccoUrl}{$wemp_pmlaUrl}'>Editar</font></td>";
						else
							if(isset($wesp))
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx6.php?pos2=".$wequ."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=0&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;wesp=".$wesp."{$wccoUrl}{$wemp_pmlaUrl}'>Editar</font></td>";
							else
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='000001_prx6.php?pos2=".$wequ."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=0&amp;pos9=".$inc."&amp;empresa=".$empresa."{$wccoUrl}{$wemp_pmlaUrl}'>Editar</font></td>";
						if(isset($wesp))
							echo "<td bgcolor=".$color." align=center></td></tr>";
					}
					$whi = $whf;
					$part1 = (int)substr($whi,0,2);
					$part2 = (int)substr($whi,2,2);
					$part2 = $part2 + $inc;
					while ($part2 >= 60)
					{
						$part2 = $part2 - 60;
						$part1 = $part1 + 1;
					}
					$whf = (string)$part1.(string)$part2;
					if ($part1 < 10)
						$whf = "0".$whf;
					if ($part2 < 10)
						$whf = substr($whf,0,2)."0".substr($whf,2,1);
					$i = $i + 1;
				}
				echo "</tabla>";
			}
			else
			{
				$mensajeNoEncontrado = ($manejaSedes) ? "PARA DICHA SEDE " : "";
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>EL MEDICO NO ESTA DISPONIBLE ESE DIA DE LA SEMANA {$mensajeNoEncontrado}-- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
		echo "</body>";
		echo "</html>";
		include_once("free.php");
	}
	else
	{
		echo "<table border=0 align=center id=tipo5>";
		echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;LAS CITAS NO PUEDEN SER ASIGNADAS FUERA DE LA INSTITUCION !!!</td></tr>";
		echo "</table></center>";
	}

}
?>
