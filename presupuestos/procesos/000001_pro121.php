<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de Costos Promedios Historicos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro121.php Ver. 2015-11-17</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function validar($chain)
{
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	if (ereg($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro121.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok) or (!isset($ok) and isset($wanop) and isset($wemp) and (isset($wcco1) or isset($wccof)) and !isset($wrub)))
	{
		if(isset($wemp) and strlen($wemp) > 2)
			$wemp=substr($wemp,0,strpos($wemp,"-"));
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>ACTUALIZACION DE COSTOS PROMEDIOS HISTORICOS</b></td></tr>";
		if(isset($ok))
		{
			for ($i=0;$i<$num;$i++)
			{
				if(validar($data[$i][1]))
				{
					$query = "update ".$empresa."_000122 set Gahval=".$data[$i][1].", Gahdes='".$data[$i][2]."' where Gahano=".$wanop." and Gahmes=".$data[$i][3]." and Gahcco='".$wcco1."' and Gahcpr='".substr($wrub,0,3)."' and Gahemp='".$wemp."'";
					echo $query."<br>";
					$err = mysql_query($query,$conex) or die("Error en la Insercion");
				}
				else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ERROR EN LOS DATOS DEL MES ".$data[$i][3]."  REVISE !!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
			}
			echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>COSTOS ACTUALIZADOS</b></td></tr>";
			unset($ok);
		}
		if(isset($wccof))
		{
			$ini=strpos($wccof,"-");
			$wcco1=substr($wccof,0,$ini);
		}
		$query = "select Gahcpr,Mganom from ".$empresa."_000122,".$empresa."_000028  ";
		$query = $query."  where gahano = ".$wanop;
		$query = $query."    and Gahemp = '".$wemp."' ";
		$query = $query."    and gahcpr = mgacod  ";
		$query = $query."    and gahcco = '".$wcco1."'"; 
		$query = $query."  group by gahcpr,mganom  ";
		$query = $query."  order by gahcpr	 ";
		$err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		if ($num2>0)
		{
			echo "<tr><td bgcolor=#cccccc colspan=8 align=center><select name='wrub'>";
			for ($j=0;$j<$num2;$j++)
			{
				$row2 = mysql_fetch_array($err2);
				echo "<option>".$row2[0]."_".$row2[1]."</option>";
			}
			echo "</select></td></tr>";
		}
		echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
		echo "<tr><td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td></tr>";
		echo"</table>";
		echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
		echo "<input type='HIDDEN' name= 'wcco1' value='".$wcco1."'>";
	}
	else
	{
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or (!isset($wcco1) and !isset($wccof)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ACTUALIZACION DE COSTOS PROMEDIOS HISTORICOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Ccocod ,Cconom  from ".$empresa."_000005 order by Ccocod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wccof'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wemp'>";
				echo "<option>Seleccione</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if(strlen($wemp) > 2)
				$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "SELECT count(*) from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and Cierre_ppto =   'on' ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($row[0] > 0)
			{
				$meses=array();
				$meses[1]="ENERO";
				$meses[2]="FEBRERO";
				$meses[3]="MARZO";
				$meses[4]="ABRIL";
				$meses[5]="MAYO";
				$meses[6]="JUNIO";
				$meses[7]="JULIO";
				$meses[8]="AGOSTO";
				$meses[9]="SEPTIEMBRE";
				$meses[10]="OCTUBRE";
				$meses[11]="NOVIEMBRE";
				$meses[12]="DICIEMBRE";
				$query = "SELECT cconom from ".$empresa."_000005 ";
				$query = $query."  where ccocod = ".$wcco1;
				$query = $query."    and ccoemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					$cconom=$row[0];
				}
				else
					$cconom="";
				$cconom = $wcco1."-".$cconom;
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' ></td><td  align=center bgcolor=#cccccc colspan=3><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=4 align=center><b>ACTUALIZACION DE COSTOS PROMEDIOS HISTORICOS</b></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=4 align=center><b>UNIDAD : ".$cconom."</b></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=4 align=center><b>RUBRO : ".$wrub."</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>MES </b></td><td bgcolor=#CCCCCC align=right><b>MONTO ACTUAL</b></td><td bgcolor=#CCCCCC align=right><b>NUEVO MONTO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION </b></td></tr>";
				$query = "SELECT Gahmes, Gahval, Gahdes from ".$empresa."_000122 ";
				$query = $query."  where Gahano = ".$wanop;
				$query = $query."    and Gahemp = '".$wemp."' ";
				$query = $query."    and Gahcco = '".$wcco1."'";
				$query = $query."    and Gahcpr = '".substr($wrub,0,3)."'";
				$query = $query."    and Gahtip = '0' ";
				$query = $query." order by Gahmes";
				
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$data=array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$data[$i][0]=$meses[$row[0]];
						$data[$i][1]=$row[1];
						$data[$i][2]=$row[2];
						$data[$i][3]=$row[0];
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#cccccc";
						echo "<tr><td bgcolor=".$color." align=center>".$data[$i][0]."</td><td bgcolor=".$color." align=center>".number_format((double)$data[$i][1],2,'.',',')."</td><td bgcolor=".$color." align=right><input type='TEXT' name='data[".$i."][1]' size=12 maxlength=12 value=".number_format((double)$data[$i][1],2,'.','')."></td><td bgcolor=".$color."><textarea name='data[".$i."][2]' cols=40 rows=3>".$data[$i][2]."</textarea></tr>";
					}
					echo "<tr><td bgcolor=#cccccc colspan=4 align=center>DATOS OK!! <input type='checkbox' name='ok'></td></tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'wcco1' value='".$wcco1."'>";
					echo "<input type='HIDDEN' name= 'wrub' value='".$wrub."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
					for ($i=0;$i<$num;$i++)
					{
						echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][3]' value='".$data[$i][3]."'>";
					}
				}
				echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				echo "<tr><td bgcolor=#999999 colspan=4 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
				echo"</table>";
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PROCESO DE PRESUPUESTACION YA ESTA CERRADO</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
	}
}
?>
</body>
</html>
