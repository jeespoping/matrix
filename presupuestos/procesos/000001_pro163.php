<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function sumar(num)
	{
		document.getElementById('wsuma').value = 0;
		for (i=0;i<num;i++)
		{
			if(document.getElementById('wp'+i).value != '')
			{
				document.getElementById('wsuma').value  = (document.getElementById('wsuma').value * 1) + (document.getElementById('wp'+i).value * 1);
			}
		}
	}
	function validar(num,item)
	{
		if(item != 0)
		{
			for (i=0;i<item;i++)
			{
				if(document.getElementById('se'+i).value == document.getElementById('se'+item).value && i != item)
				{
					document.getElementById('wp'+item).readOnly  = true ;
					break;
				}
				else
				{
					document.getElementById('wp'+item).readOnly  = false ;
				}
			}
		}
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Reclasificacion de Centros de Costos Para un Empleado</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro163.php Ver. 2017-04-06</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro163.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wsuma) or $wsuma != 100 or !isset($wemp1) or $wemp1 == "Seleccione")
		{
			if(!isset($wemp1) or $wemp1 == "Seleccione")
			{
				echo "<center><table border=0>";
				echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
				echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
				echo "<tr><td align=center colspan=2>RECLASIFICACION DE CENTROS DE COSTOS PARA UN EMPLEADO</td></tr>";
				echo "<tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wemp1'>";
					echo "<option>Seleccione</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			else
			{
				echo "<input type='HIDDEN' name= 'wemp1' value='".$wemp1."'>";
				if(!isset($wanop))
				{
					echo "<center><table border=0>";
					echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Codigo Empleado</td>";
					echo "<td bgcolor=#cccccc align=center>";
					$query = "SELECT Nomcod,Nomnom  from ".$empresa."_000038 where Nomemp = '".substr($wemp1,0,2)."' order by Nomcod";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wemp'>";
						for ($j=0;$j<$num;$j++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Numero de Centros de Costos</td>";
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' id=wnum size=2 maxlength=2></td></tr>";
				}
				else
				{
					echo "<center><table border=0>";
					echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4 value=".$wanop."></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2 value=".$wmesi."></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesf' size=2 maxlength=2 value=".$wmesf."></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Codigo Empleado</td>";
					echo "<td bgcolor=#cccccc align=center>";
					$query = "SELECT Nomcod,Nomnom  from ".$empresa."_000038 where Nomemp = '".substr($wemp1,0,2)."' order by Nomcod";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wemp'>";
						for ($j=0;$j<$num;$j++)
						{
							$row = mysql_fetch_array($err);
							if(substr($wemp,0,strpos($wemp,"-")) == $row[0])
								echo "<option selected>".$row[0]."-".$row[1]."</option>";
							else
								echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Numero de Centros de Costos</td>";
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnum' size=2 maxlength=2 value=".$wnum."></td></tr>";
					echo "<td bgcolor=#999999 align=center colspan=2>CENTROS DE COSTOS A DISTRIBUIR</td>";
					for ($i=0;$i<$wnum;$i++)
					{
						echo "<tr><td bgcolor=#cccccc align=center>";
						$query = "SELECT ccocod,cconom  from ".$empresa."_000161 where Ccoemp = '".substr($wemp1,0,2)."' order by ccocod";
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						if ($num>0)
						{
							echo "<select name='wcco[".$i."]' id=se".$i.">";
							for ($j=0;$j<$num;$j++)
							{
								$row = mysql_fetch_array($err);
								echo "<option>".$row[0]."-".$row[1]."</option>";
							}
							echo "</select>";
						}
						echo "</td>";
						echo "<td bgcolor=#cccccc align=center><input type='TEXT' id=wp".$i." name='wpor[".$i."]' size=1 onBlur='sumar(".$wnum.")' onFocus='validar(".$wnum.",".$i.")' size=3 maxlength=3></td></tr>";
					}
					echo "<tr><td bgcolor=#cccccc align=center>PROCENTAJE TOTAL</td><td bgcolor=#cccccc align=center><input type='TEXT' id=wsuma name='wsuma' size=3 maxlength=3></td></tr>";
				}
			}
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wemp1 = substr($wemp1,0,2);
			$query = "select Meccco, lpad(Mecmes,2,'0'), Meccpr, sum(Mecval)  ";
			$query .= " from ".$empresa."_000026 ";
			$query .= "   where Mecemp = '".$wemp1."' ";
			$query .= "     and Mecano = ".$wanop;
			$query .= " 	and Mecmes between '".$wmesi."' and '".$wmesf."'";
			$query .= " 	and Meccpr in ('201','301','801','515') "; 
			$query .= " group by 1,2,3 "; 
			$query .= " order by 1,2,4 desc "; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$datacpr=array();
			$keycrp = "";
			$kn = -1;
			if($num > 0)
			{
				$numcpr = $num;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($keycpr != $row[0].$row[1])
					{
						$keycpr = $row[0].$row[1];
						$kn++;
						$datacpr[$kn][0] = $keycpr;
						$datacpr[$kn][1] = $row[2];
					}
				}
			}
			echo "<center><table border=0>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2><b>RECLASIFICACION DE CENTROS DE COSTOS PARA UN EMPLEADO</b></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2>A&Ntilde;O DE PROCESO ".$wanop."</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2>MES INICIAL DE PROCESO ".$wmesi."</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2>MES FINAL DE PROCESO ".$wmesf."</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2>EMPLEADO ".$wemp."</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2>EMPRESA ".$wemp1."</td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2><b>CENTROS DE COSTOS A DISTRIBUIR</b></td></tr>";
			for ($i=0;$i<$wnum;$i++)
				echo "<tr><td bgcolor=#cccccc>".$wcco[$i]."</td><td bgcolor=#cccccc align=center>".$wpor[$i]."%</td></tr>";
			echo "</table><br>";
			for ($M=$wmesi;$M<=$wmesf;$M++)
			{
				//                  0     1      2          3            4    
				$query = "select Norcco,Norpre,Norrec,sum(Normon),sum(Norhor)  ";
				$query .= " from ".$empresa."_000036 ";
				$query .= "   where Norano = ".$wanop;
				$query .= " 	and Norper = ".$M;
				$query .= " 	and Noremp = '".substr($wemp,0,strpos($wemp,"-"))."' "; 
				$query .= "     and norfil = '".$wemp1."' ";
				$query .= " group by 1,2,3 "; 
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$k1=0;
				$data=array();
				if($num > 0)
				{
					$wsumam = 0;
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$data[$i][0]=$row[0];
						$data[$i][1]=$row[1];
						$data[$i][2]=$row[2];
						$data[$i][3]=$row[3];
						$data[$i][4]=$row[4];
						$wsumam += $data[$i][3];
					}
					for ($i=0;$i<$num;$i++)
					{
						$data[$i][5] = $data[$i][3] / $wsumam;
					}
					$wfactor=0;
					$wfactor1=0;
					for ($i=0;$i<$num;$i++)
					{
						$wfactor += $data[$i][1]*$data[$i][5];
						$wfactor1 += $data[$i][2]*$data[$i][5];
					}
				}
				//echo "Factor Pre : ".$wfactor."<br>";
				//echo "Factor Rec : ".$wfactor1."<br>";
				$query = "select Norcod, Norcar,sum(Normon),sum(Norhor)  ";
				$query .= " from ".$empresa."_000036 ";
				$query .= "   where Norano = ".$wanop;
				$query .= " 	and Norper = ".$M;
				$query .= " 	and Noremp = '".substr($wemp,0,strpos($wemp,"-"))."' "; 
				$query .= "     and norfil = '".$wemp1."' ";
				$query .= " group by 1,2 "; 
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$numT = $num;
				$k1=0;
				$data=array();
				if($numT > 0)
				{
					$wsumam = 0;
					for ($i=0;$i<$numT;$i++)
					{
						$row = mysql_fetch_array($err);
						$data[$i][0]=$row[0];
						$data[$i][1]=$row[2];
						$data[$i][2]=$row[3];
						$wcargo=$row[1];
					}
				}
				$data1=array();
				for ($i=0;$i<$wnum;$i++)
				{
					for ($j=0;$j<$numT;$j++)
					{
						$data1[$i][$j][0] = $wcco[$i];
						$data1[$i][$j][1] = $data[$j][0];
						$data1[$i][$j][2] = $data[$j][1] * ($wpor[$i] / 100);
						$data1[$i][$j][3] = $data[$j][2] * ($wpor[$i] / 100);
						$data1[$i][$j][4] = $wfactor;
						$data1[$i][$j][5] = $wfactor1;
					}
				}
				
				$waux="";
				$data2=array();	
				for ($i=0;$i<$wnum;$i++)
				{
					for ($j=0;$j<$numT;$j++)
					{
						if($waux != $data1[$i][$j][0])
						{
							$data2[$i][0] = $data1[$i][$j][0];
							$data2[$i][1] = 0;
							$waux = $data1[$i][$j][0];
						}
						$data2[$i][1] += $data1[$i][$j][2] * (1 + $data1[$i][$j][4]);
					}
				}
				$count = 0;
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				echo "<center><table border=1>";
				echo "<tr><td bgcolor=#999999 colspan=2>DATOS TABLA 26</td></tr>";
				echo "<tr><td bgcolor=#dddddd>CENTRO DE COSTOS</td><td bgcolor=#dddddd align=right>VALOR</td></tr>";
				$query = "select Norcco,sum(Normon * (1 + Norpre))  ";
				$query .= " from ".$empresa."_000036 ";
				$query .= "   where Norano = ".$wanop;
				$query .= " 	and Norper = ".$M;
				$query .= " 	and Noremp = '".substr($wemp,0,strpos($wemp,"-"))."' "; 
				$query .= "     and Norfil = '".$wemp1."' ";
				$query .= " group by 1 "; 
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$k1=0;
				$data=array();
				$wsuma1=0;
				$wsuma2=0;
				if($num > 0)
				{
					$wsumam = 0;
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						//$query = "select Rcncpr ";
						//$query .= " from ".$empresa."_000158 ";
						//$query .= "   where Rcncco = '".$row[0]."'";
						//$query .= "     and Rcnemp = '".$wemp1."' ";
						//$err1 = mysql_query($query,$conex);
						//$row1 = mysql_fetch_array($err1);
						
						$pos=bi($datacpr,$kn,$row[0].str_pad($M, 2, "0", STR_PAD_LEFT));
						if($pos != -1)
						{
							$wtipo = $datacpr[$pos][1];
						}
						else
							$wtipo = "N/A";
						$wvalor = $row[1] * (-1);
						echo "<tr><td>".$row[0]."</td><td align=right>".number_format((double)$row[1],2,'.',',')."</td></tr>";
						$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,Mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp1."','".$row[0]."','".$wtipo."',".$wanop.",".$M.",'".$wtipo."',".$wvalor.",'C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						$count++;
						$wsuma1 += $row[1];
					}
					echo "<tr><td bgcolor=#dddddd>SUBTOTAL</td><td bgcolor=#dddddd align=right>".number_format((double)$wsuma1,2,'.',',')."</td></tr>";
				}
				for ($i=0;$i<$wnum;$i++)
				{
					//$query = "select Rcncpr ";
					//$query .= " from ".$empresa."_000158 ";
					//$query .= "   where Rcncco = '".$row[0]."'";
					//$query .= "     and Rcnemp = '".$wemp1."' ";
					//$err1 = mysql_query($query,$conex);
					//$row1 = mysql_fetch_array($err1);
					//$wtipo = $row1[0]
					
					$pos=bi($datacpr,$kn,$row[0].str_pad($M, 2, "0", STR_PAD_LEFT));
					if($pos != -1)
					{
						$wtipo = $datacpr[$pos][1];
					}
					else
						$wtipo = "N/A";
					$wvalor = $data2[$i][1];
					echo "<tr><td>".$data2[$i][0]."</td><td align=right>".number_format((double)$data2[$i][1],2,'.',',')."</td></tr>";
					$query = "insert ".$empresa."_000026 (medico,fecha_data,hora_data,mecemp,meccco,meccpr,mecano,mecmes,meccue,mecval,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp1."','".substr($data2[$i][0],0,strpos($data2[$i][0],"-"))."','".$wtipo."',".$wanop.",".$M.",'".$wtipo."',".$wvalor.",'C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					$wsuma2 += $data2[$i][1];
					$count++;
				}
				echo "<tr><td bgcolor=#dddddd>SUBTOTAL</td><td bgcolor=#dddddd align=right>".number_format((double)$wsuma2,2,'.',',')."</td></tr>";
				$wsuma3 = $wsuma1 - $wsuma2;
				echo "<tr><td bgcolor=#999999>DIFERENCIA</td><td bgcolor=#999999 align=right>".number_format((double)$wsuma3,2,'.',',')."</td></tr>";
				echo "<tr><td bgcolor=#C3D9FF>TOTAL REGISTROS INSERTADOS EN TABLA 26 PARA EL MES ".$M."</td><td bgcolor=#C3D9FF align=right>".$count."</td></tr>";
				echo "</table><br><br>";

				$query  = "Delete from ".$empresa."_000036 ";
				$query .= "   where Norano = ".$wanop;
				$query .= " 	and Norper = ".$M;
				$query .= " 	and Noremp = '".substr($wemp,0,strpos($wemp,"-"))."' "; 
				$query .= "     and Norfil = '".$wemp1."' ";
				$err = mysql_query($query,$conex)  or die("ERROR BORRANDO TABLA 36 : ".mysql_errno().":".mysql_error());

				$count = 0;
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				echo "<br><br><br><br><center><table border=1>";
				echo "<tr><td bgcolor=#999999 colspan=6><b>DATOS TABLA 36 MES DE PROCESO : ".$M."</b></td></tr>";
				echo "<tr><td bgcolor=#dddddd>CENTRO DE COSTOS</td><td bgcolor=#dddddd>CONCEPTO</td><td bgcolor=#dddddd>MONTO</td><td bgcolor=#dddddd>HORAS</td><td bgcolor=#dddddd>FACTOR<BR>PRESTACIONAL</BR></td><td bgcolor=#dddddd>FACTOR<BR>DE RECARGO</td></tr>";
				for ($i=0;$i<$wnum;$i++)
				{
					for ($j=0;$j<$numT;$j++)
					{
						echo "<tr><td>".$data1[$i][$j][0]."</td><td>".$data1[$i][$j][1]."</td><td align=right>".number_format((double)$data1[$i][$j][2],2,'.',',')."</td><td align=right>".number_format((double)$data1[$i][$j][3],2,'.',',')."</td><td>".$data1[$i][$j][4]."</td><td>".$data1[$i][$j][5]."</td></tr>";
						$query = "insert ".$empresa."_000036 (medico,fecha_data,hora_data,norfil,norano,norper,norcco,norcar,noremp,norcod,normon,norhor,norpre,norrec,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp1."',".$wanop.",".$M.",'".substr($data1[$i][$j][0],0,strpos($data1[$i][$j][0],"-"))."','".$wcargo."','".substr($wemp,0,strpos($wemp,"-"))."','".$data1[$i][$j][1]."',".$data1[$i][$j][2].",".$data1[$i][$j][3].",".$data1[$i][$j][4].",".$data1[$i][$j][5].",'C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la T36");
						$count++;
					}
				}
				echo "<tr><td bgcolor=#C3D9FF colspan=5><b>NUMERO DE REGISTROS INSERTADOS EN LA TABLA 36 PARA EL MES ".$M."</b></td><td bgcolor=#C3D9FF align=right>".$count."</tr>";
				echo "</table><br><br>";
				echo "<hr>";
			}
		}
}
?>
</body>
</html>
