<html>

<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Imforme De Procedimientos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_au01.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
	/***********************************************
	 * IMFORME DE PROCEDIMIENTOS EN AUDIOLOGIA CLINICA *
	 ***********************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_au01.php' method=post>";
		if(!isset($whis) or !isset($wfechae))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>OIR - AUDIOLOGIA CLINICA</td></tr>";
			echo "<tr><td align=center colspan=2>IMFORME DE PROCEDIMIENTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Numero de Historia Clinica</td>";
			if(!isset($whis))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=8 maxlength=8></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=8 maxlength=8 value=".$whis."></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Fecha del procedimiento</td>";
			echo "<td bgcolor=#cccccc align=center>";
			echo "<select name='wfechae'>";
			if(isset($whis))
			{
				$query = "select 'AUTO',fecha_examen  from oir_000002 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'NIVE',fecha_examen  from oir_000003 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'BEPA',fecha_examen  from oir_000004 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'WEBE',fecha_examen  from oir_000005 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'TONE',fecha_examen  from oir_000006 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'AUDI',fecha_examen  from oir_000007 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'AUGE',fecha_examen  from oir_000008 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'INMI',fecha_examen  from oir_000009 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'FUNC',fecha_examen  from oir_000010 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'SPAR',fecha_examen  from oir_000011 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'DECA',fecha_examen  from oir_000012 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				$query = "select 'ACUF',fecha_examen  from oir_000013 where  paciente like '%".$whis."%' order by fecha_examen desc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					for($i=0;$i<$num;$i++)
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select></td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr>";
			echo "</table>";
		}
		else
		{
			$ruta="/matrix/images/medical/audiologia/";
			$query = "select Historia, Documento, Nombre, Fecha_nac  from oir_000001 where  Historia='".$whis."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				#****************************************************************************
				#Impresion de titulos                                                                                                                                         *
				#****************************************************************************

				$row = mysql_fetch_array($err);
				$ann=(integer)substr($row[3],0,4)*360 +(integer)substr($row[3],5,2)*30 + (integer)substr($row[3],8,2);
				$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
				$ann1=($aa - $ann)/360;
				$meses=(($aa - $ann) % 360)/30;
				if ($ann1<1)
				{
					$dias1=(($aa - $ann) % 360) % 30;
					$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
				}
				else
				{
					$dias1=(($aa - $ann) % 360) % 30;
					$wedad=(string)(integer)$ann1." Años ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
				}
				$ini=strpos($wfechae,"-");
				$wfechae=substr($wfechae,$ini+1);
				#****************************************************************************
				#                                                                       AUDIOMETRIA TONAL                                                                 *
				#****************************************************************************
				$query = "select Paciente,  Remitido, Entidad, Fecha_Examen,  Vo_od_250, Vo_od_500, Vo_od_1000, Vo_od_2000, Vo_od_3000, Vo_od_4000, Va_od_250, Va_od_500, Va_od_1000, Va_od_2000, Va_od_3000, Va_od_4000, Va_od_6000, Va_od_8000, Vo_oi_250, Vo_oi_500, Vo_oi_1000, Vo_oi_2000, Vo_oi_3000, Vo_oi_4000, Va_oi_250, Va_oi_500, Va_oi_1000, Va_oi_2000, Va_oi_3000, Va_oi_4000, Va_oi_6000, Va_oi_8000   from oir_000002 where  Paciente like'%".$whis."%' and Fecha_Examen='".$wfechae."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><img SRC=".$ruta."/oir.jpg size=60% width=60%></td></tr>";
					echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>PACIENTE : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".strtoupper($row[2])."</font></td></tr>";
					echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>FECHA EXAMEN: </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$wfechae."</font></td></tr>";
					echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>EDAD DEL PACIENTE: </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$wedad."</font></td></tr></table><br><br>";
					$row1 = mysql_fetch_array($err1);
					$vaod=array();
					$vaoi=array();
					$va=array();
					$vo=array();
					for($i=0;$i<8;$i++)
						for($j=0;$j<23;$j++)
							$vaod[$j][$i]="&nbsp";
					for($i=10;$i<18;$i++)
						if(is_numeric($row1[$i]))
							$vaod[(integer)($row1[$i]/5)][$i-10]="O";
					for($i=4;$i<10;$i++)
						if(is_numeric($row1[$i]))
							$vaod[(integer)($row1[$i]/5)][$i-4]=$vaod[(integer)($row1[$i]/5)][$i-4]."<";
					for($i=0;$i<8;$i++)
						for($j=0;$j<23;$j++)
							$vaoi[$j][$i]="&nbsp";
					for($i=18;$i<24;$i++)
						if(is_numeric($row1[$i]))
							$vaoi[(integer)($row1[$i]/5)][$i-18]=">";
					for($i=24;$i<32;$i++)
						if(is_numeric($row1[$i]))
							$vaoi[(integer)($row1[$i]/5)][$i-24]=$vaoi[(integer)($row1[$i]/5)][$i-24]."X";
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>AUDIOMETRIA TONAL</b></td></tr></table><br>";
					echo "<center><table border=0>";
					echo "<tr><td colspan=9 align=center bgcolor=#999999><b>OIDO DERECHO</b></td><td align=center bgcolor=#999999 rowspan=2><b>Ansi <br> 96</b></td><td colspan=9 align=center bgcolor=#999999><b>OIDO IZQUIERDO</b></td></tr>";
					echo "<tr><td bgcolor=#999999>dB/Hz</td><td bgcolor=#999999>&nbsp250</td><td bgcolor=#999999>&nbsp500</td><td bgcolor=#999999>1000</td><td bgcolor=#999999>2000</td><td bgcolor=#999999>&nbsp3k</td><td bgcolor=#999999>4000</td><td bgcolor=#999999>&nbsp6k</td><td bgcolor=#999999>8000</td><td bgcolor=#999999>&nbsp250</td><td bgcolor=#999999>&nbsp500</td><td bgcolor=#999999>1000</td><td bgcolor=#999999>2000</td><td bgcolor=#999999>&nbsp3k</td><td bgcolor=#999999>4000</td><td bgcolor=#999999>&nbsp6k</td><td bgcolor=#999999>8000</td><td bgcolor=#999999>dB/Hz</td></tr>";
					for($i=0;$i<23;$i++)
					{
						$c=$i*5;
						switch ($c)
						{
							case 0:
								$color="#FFFFFF";
							break;
							case 15:
								$color="#CCFFFF";
							break;
							case 30:
								$color="#99FFFF";
							break;
							case 45:
								$color="#66FFFF";
							break;
							case 60:
								$color="#33FFFF";
							break;
							case 80:
								$color="#66CCFF";
							break;
							case 95:
								$color="#0099CC";
							break;
						}
						echo "<tr><td bgcolor=#999999 align=right>".$c."</td>";
						for($j=0;$j<8;$j++)
							echo "<td bgcolor=#cccccc align=center><font  color='#CC0000'><b>".$vaod[$i][$j]."</b></td>";
						switch ($c)
						{
							case 0:
								echo "<td bgcolor=".$color." align=center rowspan=3><font size=2>NORMAL</font></td>";
							break;
							case 15:
								echo "<td bgcolor=".$color." align=center rowspan=3><font size=2>MINIMO</font></td>";
							break;
							case 30:
								echo "<td bgcolor=".$color." align=center rowspan=3><font size=2>MEDIO LEVE</font></td>";
							break;
							case 45:
								echo "<td bgcolor=".$color." align=center rowspan=3><font size=2>MODERADO</font></td>";
							break;
							case 60:
								echo "<td bgcolor=".$color." align=center rowspan=4><font size=2>MODERADO <br> SEVERO</font></td>";
							break;
							case 80:
								echo "<td bgcolor=".$color." align=center rowspan=3><font size=2>SEVERO</font></td>";
							break;
							case 95:
								echo "<td bgcolor=".$color." align=center rowspan=4><font size=2>PROFUNDO</font></td>";
							break;
						}
						for($j=0;$j<8;$j++)
							echo "<td bgcolor=#cccccc align=center><font  color='#000099'><b>".$vaoi[$i][$j]."</b></td>";
						echo "<td bgcolor=#999999 align=left>".$c."</td></tr>";
					}
				}
				echo "</table><div style='page-break-before: always'>";
				#****************************************************************************
				#                                                NIVELES EFECTIVOS DE ENMASCARAMIENTO                                                *
				#****************************************************************************
				$wtil=0;
				$query = "select  Remitido, Entidad, Va_oi_250, Va_oi_500, Va_oi_1000, Va_oi_2000, Va_oi_3000, Va_oi_4000, Va_oi_6000, Va_oi_8000, Vo_oi_250, Vo_oi_500, Vo_oi_1000, Vo_oi_2000, Vo_oi_3000, Vo_oi_4000, Va_od_250, Va_od_500, Va_od_1000, Va_od_2000, Va_od_3000, Va_od_4000, Va_od_6000, Va_od_8000, Vo_od_250, Vo_od_500, Vo_od_1000, Vo_od_2000, Vo_od_3000, Vo_od_4000    from oir_000003 where  Paciente like'%".$whis."%' and Fecha_Examen='".$wfechae."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					for($i=0;$i<8;$i++)
					{
						$va[$i][1]="";
						$va[$i][2]="";
						$vo[$i][1]="";
						$vo[$i][2]="";
					}
					$row1 = mysql_fetch_array($err1);
					for($i=16;$i<24;$i++)
						$va[$i-16][1]=$row1[$i];
					for($i=24;$i<30;$i++)
						$vo[$i-24][1]=$row1[$i];
					for($i=10;$i<16;$i++)
						$vo[$i-10][2]=$row1[$i];
					for($i=2;$i<10;$i++)
						$va[$i-2][2]=$row1[$i];
					$wtil=1;
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><img SRC=".$ruta."/oir.jpg size=60% width=60%></td></tr>";
					echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>PACIENTE : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".strtoupper($row[2])."</font></td></tr>";
					echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>FECHA EXAMEN: </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$wfechae."</font></td></tr>";
					echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>EDAD DEL PACIENTE: </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$wedad."</font></td></tr></table><br><br>";
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>NIVELES EFECTIVOS DE ENMASCARAMIENTO</b></td></tr></table><br>";
					echo "<center><table border=0>";
					echo "<tr><td bgcolor=#999999>Hz</td><td bgcolor=#999999>Oido</td><td bgcolor=#999999>&nbsp250</td><td bgcolor=#999999>&nbsp500</td><td bgcolor=#999999>1000</td><td bgcolor=#999999>2000</td><td bgcolor=#999999>3000</td><td bgcolor=#999999>4000</td><td bgcolor=#999999>6000</td><td bgcolor=#999999>8000</td></tr>";
					echo "<tr><td bgcolor=#999999 rowspan=2>Via Aerea</td>";
					echo "<td bgcolor=#999999>Derecho</td>";
					for($i=0;$i<8;$i++)
						echo "<td bgcolor=#cccccc align=center><font  color='#CC0000'>".$va[$i][1]."</td>";
					echo "</tr>";
					echo "<td bgcolor=#999999>Izquierdo</td>";
					for($i=0;$i<8;$i++)
						echo "<td bgcolor=#cccccc align=center><font  color='#000099'>".$va[$i][2]."</td>";
					echo "</tr>";
					echo "<tr><td bgcolor=#999999 rowspan=2>Via Osea</td>";
					echo "<td bgcolor=#999999>Derecho</td>";
					for($i=0;$i<8;$i++)
						echo "<td bgcolor=#cccccc align=center><font  color='#CC0000'>".$vo[$i][1]."</td>";
					echo "</tr>";
					echo "<td bgcolor=#999999>Izquierdo</td>";
					for($i=0;$i<8;$i++)
						echo "<td bgcolor=#cccccc align=center><font  color='#000099'>".$vo[$i][2]."</td>";
					echo "</tr></table><br><br>";
				}
				#****************************************************************************
				#                                                                     TONE DECAY TEST                                                                          *
				#****************************************************************************
				$query = "select Od_500, Od_1000, Od_2000, Od_4000, Oi_500, Oi_1000, Oi_2000, Oi_4000   from oir_000006 where  Paciente like'%".$whis."%' and Paciente like '%".$wfechae."%'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					if($wtil == 0)
					{
						$wtil=1;
						echo "<center><table border=0>";
						echo "<tr><td align=center colspan=2><img SRC=".$ruta."/oir.jpg size=60% width=60%></td></tr>";
						echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>PACIENTE : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".strtoupper($row[2])."</font></td></tr>";
						echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>FECHA EXAMEN: </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$year."-".$month."-".$day."</font></td></tr>";
						echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>EDAD DEL PACIENTE: </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$wedad."</font></td></tr></table><br><br>";
					}
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>TONE DECAY TEST</b></td></tr></table><br>";
					echo "<center><table border=0>";
					echo "<tr><td bgcolor=#999999>Oido</td><td bgcolor=#999999>&nbsp500</td><td bgcolor=#999999>1000</td><td bgcolor=#999999>2000</td><td bgcolor=#999999>4000</td></tr>";
					echo "<tr><td bgcolor=#999999>Derecho</td>";
					for($i=0;$i<4;$i++)
					{
						$ini=strpos($row1[$i],"-");
						echo "<td bgcolor=#cccccc align=center>".substr($row1[$i],$ini+1)."</td>";
					}
					echo "</tr>";
					echo "<tr><td bgcolor=#999999>Izquierdo</td>";
					for($i=4;$i<8;$i++)
					{
						$ini=strpos($row1[$i],"-");
						echo "<td bgcolor=#cccccc align=center>".substr($row1[$i],$ini+1)."</td>";
					}
					echo "</tr></table><br><br>";
				}
			}
			else
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>LA HISTORIA NO EXISTE  -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
		}
		include_once("free.php");
}
?>
</body>
</html>
