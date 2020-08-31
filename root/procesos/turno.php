<?php
include_once("conex.php");
echo "<html>";
//echo "<meta http-equiv='refresh' content='20;url=http://132.1.18.8/rx/turno.php?wfec=".$wfec."'>";
echo "<head>";
echo "<title>ASIGNACION DE TURNOS X EQUIPO</title>";
echo "</head>";
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";
echo "<center>";
$wfec=$wfec=date("Y-m-d");
$wequ=substr($pos4,0,3);
$wtit=substr($pos4,4,strlen($pos4));
echo "<A NAME='Arriba'><h1>Turnos x Equipo :".$wtit."</h1></a>";
echo "</center>";
echo "<form action='000001_prx1.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
	{		
	

	

	$query = "select * from usuarios where codigo = '".substr($user,2,strlen($user))."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	$prioridad = $row[2];
	if (isset($wpar))
	{
		if (substr($Estado,0,1) == "A")
		{
			//**** VALIDACIONES ****
			$tiperr =0;
			switch ($wpar)
			{
				case 2:
				{	
				// Verificacion de Disponibilidad de Espacio
				$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from radio_000001 where cod_equ = '".substr($Codequ,0,3)."'";
				$query = $query." and fecha = '".$wfec."'";
				$query = $query." and hi = '".$Hi."'";
				$query = $query." and hf = '".$Hf."'";			
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num > 0 ) 
					$tiperr = 5;
				else
					if($tiperr == 0)
						$tiperr = 0;
				//Validacion de radio_000006 Especiales
				$query = "select medico,examen,activo from radio_000005 where examen = '".substr($Codexa,0,6)."'";
				$query = $query." and activo = 'A'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num > 0 )
				{
					$query = "select medico,examen,activo from radio_000005 where examen = '".substr($Codexa,0,6)."' and medico = '".substr($Codmed,0,5)."'";
					$query = $query." and activo = 'A'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err); 
					if ($num == 0 ) 
						$tiperr = 4;
					else
						if($tiperr == 0)
							$tiperr = 0;
				}
				else
					$tiperr = 0;
				// Disponibilidad del Medico
				$query = "select codigo,dia,hi,hf,activo,ndia from radio_000007 where codigo = '".substr($Codmed,0,5)."'";
				$query = $query." and dia = '".substr($Diasem,0,2)."'";
				$query = $query." and hi <= '".$Hi."'";
				$query = $query." and hf >= '".$Hf."'";
				$query = $query." and activo = 'A'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num == 0 ) 
					$tiperr = 1;
				else
					if($tiperr == 0)
						$tiperr = 0;					
				// Verificacion de la Ocupacion del Medico
				$query = "select hi from radio_000001,radio_000008 where cod_med = '".substr($Codmed,0,5)."'";
				$query = $query." and fecha = '".$wfec."'";
				$query = $query." and ((hi <= '".$Hi."' and hf <= '".$Hf."' and hf > '".$Hi."')";
				$query = $query."  or  (hi >= '".$Hi."' and hf >= '".$Hf."' and hi < '".$Hf."')";
				$query = $query."  or  (hi <= '".$Hi."' and hf >= '".$Hf."'))";
				$query = $query." and radio_000001.activo = 'A'";
				$query = $query." and cod_med = codigo";
				$query = $query." and tipo = 'S'";				
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num > 0 ) 
					$tiperr = 2;
				else
					if($tiperr == 0)
						$tiperr = 0;
				// Verificacion de los datos de texto
				if (strlen($Nompac)==0 or strlen($Tel)==0 or $Edad==0)
					$tiperr = 3;
				else
					if($tiperr == 0)
						$tiperr = 0;
				break;
				}
				case 1:
				if (strlen($Nompac)==0 or strlen($Tel)==0 or $Edad==0)
					$tiperr = 3;
				else
					if($tiperr == 0)
						$tiperr = 0;
				break;
			}
		}
		else
		{
			$tiperr = 0;
			$wpar = 3;	
		}
		if ($tiperr == 0)
		{
			$posicion = strpos($Nitres,'-');
			$Nitres = substr($Nitres,0,$posicion);
			switch ($wpar)
			{				
				case 1:
				$Nompac = strtoupper($Nompac);
				$query = "update radio_000001 set nom_pac='".ucwords($Nompac)."', nit_resp='".$Nitres."', telefono='".$Tel."', edad=".$Edad.", comentarios='".$Coment."',activo='".substr($Estado,0,1)."' where cod_med='".$Codmed."' and cod_equ='".$Codequ."' and cod_exa='".$Codexa."' and fecha='".$Fecha."' and hi='".$Hi."' and hf='".$Hf."'";
				$err = mysql_query($query,$conex);
				break;
				case 2:
				$Nompac = strtoupper($Nompac);
				$Nompac = ucwords($Nompac);
				$query = "insert radio_000001 values ('".substr($Codmed,0,5)."','".substr($Codequ,0,3)."','".substr($Codexa,0,6)."','".$Fecha."','".$Hi."','".$Hf."','".ucwords($Nompac)."','".$Nitres."','".$Tel."',".$Edad.",'".$Coment."','".substr($user,2,strlen($user))."','".substr($Estado,0,1)."')";
				$err = mysql_query($query,$conex);
				if ($err != 1)
				{
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>TURNO ASIGNADO EN OTRA ESTACION -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				else
				{
					$query = "select  codigo,descripcion,uni_hora,hi,hf,activo from radio_000003 where codigo='".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$row = mysql_fetch_array($err);				
					$query = "select fecha,equipo,uni_hora,hi,hf,activo from radio_000004 where fecha = '".$Fecha."' and equipo = '".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex);	
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$query = "insert radio_000004 values ('".$Fecha."','".substr($Codequ,0,3)."',".$row[2].",'".$row[3]."','".$row[4]."')";
						$err = mysql_query($query,$conex);	
					}	
				}
				break;
				case 3:
				$query = "delete from radio_000001 where cod_med='".$Codmed."' and cod_equ='".$Codequ."' and cod_exa='".$Codexa."' and fecha='".$Fecha."' and hi='".$Hi."' and hf='".$Hf."'";
				$err = mysql_query($query,$conex);
				break;
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			switch ($tiperr)
			{
				case 1:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>EL MEDICO NO ESTA DISPONIBLE EN ESE HORARIO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 2:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33cc33 LOOP=-1>EL MEDICO YA TIENE UNA CITA ASIGNADA INCOMPATIBLE CON LA QUE USTED DESEA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 3:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 4:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>EXAMEN ESPECIAL NO REALIZADO POR ESTE MEDICO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 5:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>TURNO ASIGNADO EN OTRA ESTACION -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
			}
		}
	}
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
	echo "<table border=0 align=center cellpadding=6>";
	echo "<tr>";
	echo "<td bgcolor='#cccccc'><b>Fecha :</b></td>";
	echo "<td bgcolor='#cccccc'><font size=3><b>".$wfec."</b></font></td>";
	echo "<td bgcolor='#cccccc'><b>Año :</b><td bgcolor='#cccccc'>";
	echo "<select name='Ano'>";
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
	echo "<select name='Mes'>";
	for ($i=1;$i<13;$i++)
	{
		if (isset($Mes) and $Mes == (string)$i)
			echo "<option selected>".$i."</option>";
		else
			echo "<option>".$i."</option>";
	}
	echo "</td><td bgcolor='#cccccc'><b>Dia :</b><td bgcolor='#cccccc'>";
	echo "<select name='Dia'>";
	for ($i=1;$i<32;$i++)
	{
		if (isset($Dia) and $Dia == (string)$i)
			echo "<option selected>".$i."</option>";
		else
			echo "<option>".$i."</option>";
	}
	echo "<input type='HIDDEN' name= 'wfec' value='".$wfec."'>";
	echo "<input type='HIDDEN' name= 'wequ' value='".$wequ."'>";
	echo "<input type='HIDDEN' name= 'wtit' value='".$wtit."'>";
	echo "<input type='HIDDEN' name= 'pos4' value='".$pos4."'>";
	echo "<td bgcolor='#cccccc'><input type='submit' value='IR'>";
	echo "</td></tr></table><br>";
	$query = "select fecha,equipo,uni_hor,hi,hf from radio_000004 where fecha = '".$wfec."' and equipo = '".$wequ."'";
	$err = mysql_query($query,$conex);	
	$num = mysql_num_rows($err);
	if ($num == 0)
	{
		//echo "no";
		$query = "select codigo,descripcion,uni_hora,hi,hf,activo from radio_000003 where codigo='".$wequ."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);

	}
	else
	{
		//echo "si";
		$row = mysql_fetch_array($err);
	}
	$whi = $row[3];
	$wul = $row[4];
	$inc = $row[2];
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
	//$query = "select * from radio_000001 where fecha='".$wfec."'";
	//$err = mysql_query($query,$conex);
	//$num = mysql_num_rows($err);
	$color="#999999";
	echo "<table border=0 align=center>";
	echo "<tr>";
	echo "<td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Hora Final</b></font></td>"; 
  	echo "<td bgcolor=".$color."><font size=2><b>Medico</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";	
  	echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";	
  	echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Edad</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Estado</b></font></td>";
  	echo "<td bgcolor=".$color."><font size=2><b>Seleccion</b></font></td>"; 
	echo "</tr>";
	$r = 0;
	$i = 0;
	$fila = 0;
	$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from radio_000001 where fecha='".$wfec."' and cod_equ='".$wequ."' order by hi";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$row = mysql_fetch_array($err);
	}
	while ($whi < $wul)
	{
		$r = $i/2;
		if ($r*2 === $i)
			$color="#CCCCCC";
		else
			$color="#999999";
		echo "<tr>";
		echo "<td bgcolor=".$color." align=center><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></td>";
		echo "<td bgcolor=".$color." align=center><font size=2>".substr($whf,0,2).":".substr($whf,2,2)."</font></td>";
		if ($num > 0 and $row[4] == $whi)
		{	
			$query = "select codigo,nombre,oficio,tipo,edad_pac,activo from radio_000008 where codigo='".$row[0]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row1 = mysql_fetch_array($err1);		
			echo "<td bgcolor=".$color."><font size=2>".$row1[1]."</font></td>";
			$query = "select codigo,descripcion,preparacion,cod_equipo,activo,especial from radio_000006 where codigo='".$row[2]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row2 = mysql_fetch_array($err1);
			echo "<td bgcolor=".$color."><font size=2>".$row2[1]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[6]."</font></td>";
			$query = "select nit,nombre,activo from radio_000002 where nit='".$row[7]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			$row3 = mysql_fetch_array($err1);
			echo "<td bgcolor=".$color."><font size=2>".$row3[1]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[8]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[9]."</font></td>";
			switch ($row[12])
			{
				case "A":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
					break;
				case "I":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/inactivo.gif' ></td>";
					break;
				default:
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
					break;
			}
			if ($prioridad > 0)
				echo "<td bgcolor=".$color." align=center><font size=2><A HREF='ent1_turno.php?pos1=".$row1[0]."&amp;pos2=".$wequ."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=0&amp;pos8=".$row[9]."&amp;pos9=".$wtit."'>Editar</font></td>";
			else
				echo "<td bgcolor=".$color." align=center><font size=2>Sin Edicion</font></td>";
			echo "</tr>";
			$row = mysql_fetch_array($err);
			$fila = $fila + 1;
		}
		else
		{
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color."></td>";
			echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
			if ($prioridad > 0)
				echo "<td bgcolor=".$color." align=center><font size=2><A HREF='ent1_turno.php?pos1=0&amp;pos2=".$wequ."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=0&amp;pos8=0&amp;pos9=".$wtit."'>Editar</font></td>";
			else
				echo "<td bgcolor=".$color." align=center><font size=2>Sin Edicion</font></td>";
			echo "</tr>";
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
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";	
	echo "</body>";
	echo "</html>";
	mysql_free_result($err);
	if (isset($err1))
		mysql_free_result($err1);
	mysql_close($conex);
	}
?>
