<html>
<head>
  	<title>MATRIX</title>
</head>
<body onload=ira() BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function bt($lin,$arr,$numl)
{
	$bt=0;
	for ($j=0;$j<$numl;$j++)
		if($lin == $arr[$j][0])
			$bt=$j;
	return $bt;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='iCompinv_uvg' action='iCompinv_uvg.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if( !isset($wano) or !isset($wmes) or !isset($wfec) or !isset($wcons) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N"))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>GENERACION DE COMPROBANTE CONTABLE UNIDAD VISUAL GLOBAL</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Comprobante</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfec' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Consecutivo Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcons' size=6 maxlength=6></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Comprobante Definitivo? (S/N)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wserv=strtoupper($wserv);
		$wfec=substr($wfec,5,2)."/".substr($wfec,8,2)."/".substr($wfec,0,4);
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/FARMASTORE/logo farmastore.png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><font size=6 face='tahoma'><b>GENERACION DE COMPROBANTE CONTABLE</font> Ver 2008-12-17</b></font></td></tr>";
		$color="#dddddd";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Año : </b>".$wano."</td></tr>";
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Mes : </b>".$wmes."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Consecutivo Inicial : </b>".$wcons."</td></tr>";	
		echo "<tr><td bgcolor=".$color." align=center><font face='tahoma'><b>Comprobante Definitivo : </b>".$wserv."</td></tr>";	
		echo "</tr></table><br><br>";
		//                 0       1      2     3      4      5       6         7
		$query = "Select Cinfue,Cincon,Cincco,Cinnit,Cincue,Cinnat, Cinbaj, sum(Cinval) from  ".$empresa."_000056 ";
		$query .= " where cinano = ".$wano; 
		$query .= "     and cinmes = ".$wmes;
		$query .= " group by cincon,cinfue,cincco,cinnit,Cincue,cinnat  ";
		$query .= " order by cincon,cinfue,cincco,cinnit,Cincue,cinnat ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotgd=0;
		$wtotgc=0;
		$wkey="";
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CONCEPTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>FUENTE</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DOCUMENTO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>C. DE C.</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>NIT</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CUENTA</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>DEBITO</b></font></td><td align=center bgcolor=#999999><font face='tahoma' size=2><b>CREDITO</b></font></td></tr>";
		if($wserv == "S")
		{
			$datafile="./../../planos/compinv_uvg.txt"; 
			$file = fopen($datafile,"w+");
			$witem=0;
		}
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			
			$var = $row[1].$row[2].$row[3].$row[4].$row[5];
			if(strcmp($wkey,$var) != 0)
			{
				$cl=0;
				$wc=(string)$wcons;
				while(strlen($wc) < 7)
					$wc = "0".$wc;
				$wkey = $row[1].$row[2].$row[3].$row[4].$row[5];
				$wexis=0;
				if($wserv == "S")
				{
					$witem++;
					$registro  = $wano."!";											//año
					$registro .= str_pad($wmes,2,"0",STR_PAD_LEFT)."!";				//mes
					$registro .= "00010!";											//comprobante
					$registro .= "00000!";											//prefijo
					$registro .= str_pad($wcons,15,"0",STR_PAD_LEFT)."!";			//documento
					$registro .= $wfec."!";											//fecha
					$registro .= str_pad($witem,5,"0",STR_PAD_LEFT)."!";			//item
					$registro .= $row[4]."!";										//cuenta
					$registro .= $row[2]."!";										//centro de costos
					$registro .= "000!";											//moneda
					$registro .= "!";												//activo fijo
					$registro .= "!";												//diferido
					$registro .= $row[3]."!";										//identificador uno
					$registro .= "000!";											//sucursal
					$registro .= $row[3]."!";										//identificador dos
					$registro .= "00000!";											//prefijo referencia
					$registro .= str_pad($wcons,15,"0",STR_PAD_LEFT)."!";			//documento referencia
					$registro .= "COMPROBANTE INVENTARIOS!";						//comentarios
					$registro .= str_pad((integer)$row[7],15,"0",STR_PAD_LEFT)."!";	//valor
					$registro .= "000000000000000!";								//valor base
					$registro .= "000000000000000!";								//valor moneda
					if($row[5] == "1")												//naturaleza
						$registro .= "DNO!";										
					else
						$registro .= "CNO!";
					$registro .= "DIG";												//origen
					$registro=$registro.chr(13).chr(10);
					fwrite ($file,$registro);
				}
				else
					$wexis=1;
			}
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wc."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[4]."</font></td>";	
			if($row[5] == "1")
			{
				$wtotgd += $row[7];
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[7],2,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td></tr>";	
			}
			else
			{
				$wtotgc += $row[7];
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format(0,2,'.',',')."</font></td>";	
				echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[7],2,'.',',')."</font></td></tr>";	
			}
		}
		echo "<tr><td bgcolor=#999999 align=center colspan=6><font face='tahoma' size=2><b>TOTAL COMPROBANTE</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotgd,2,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2><b>$".number_format((double)$wtotgc,2,'.',',')."</b></font></td></tr>";	
		if($wserv == "S")
		{
			fclose ($file);
			echo "<tr><td bgcolor=#dddddd colspan=8 align=center><b><A href=".$datafile.">Click Derecho Para Bajar el Archivo del Comprobante</A></b></td></tr>";
		}
		echo"</table>";
	}
}
?>
</body>
</html>