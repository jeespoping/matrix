<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Costos Promedios Comparativos Entre A&ntilde;os Para Una Entidad x Unidad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc100.php Ver. 2010-05-12</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc100.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop)  or !isset($wpv) or !isset($wcco1) or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>COSTO PROMEDIO PARA UNA ENTIDAD X UNIDAD</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Entidad</td>";
			echo "<td bgcolor=#cccccc align=center>";
			$query = "SELECT Empcin, Empdes  from ".$empresa."_000061  WHERE Empcos='S' group by empcin order by Empdes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wemp'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."_".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";	
			echo "<tr><td bgcolor=#cccccc align=center>Porcentaje de Variacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpv' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wcco2=strtolower ($wcco2);
			$wanopa=$wanop - 1;
			$ini = strpos($wemp,"_");
			$wempm=substr($wemp,$ini+1);
			$wemp=substr($wemp,0,$ini);
			$wtable= date("YmdHis");
			$wtable=" temp_".$wtable;
			$query = "select  max(Mes) as maximo from ".$empresa."_000048 ";
			$query = $query."  where Ano  = ".$wanop;
			$query = $query."      and Cierre_costos = 'on' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$wper1=$row[0];
			$query = "select  max(Mes) as maximo from ".$empresa."_000048 ";
			$query = $query."  where Ano  = ".$wanopa;
			$query = $query."      and Cierre_costos = 'on' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$wper2 =$row[0];
			if($wper1 > 0 and $wper2 > 0)
			{
			$query = "Create table  IF NOT EXISTS ".$wtable." as ";
			$query = $query."select  Mprcco,Mprpro, Mprnom, Mprent , Mprgru, Mprpor from ".$empresa."_000095 ";
			$query = $query."  where Mprcco  between '".$wcco1."' and '".$wcco2."'";
			$query = $query."      and Mprent  = '".$wemp."'";
			$query = $query."    Union   ";
			$query = $query."select  Mprcco,Mprpro, Mprnom, Mprent , Mprgru, Mprpor from ".$empresa."_000095,".$empresa."_000081 ";
			$query = $query."  where Mprcco  between '".$wcco1."' and '".$wcco2."'";
			$query = $query."      and Mprent  = '999' ";
			$query = $query."      and Mprcco = Ceecco ";
			$query = $query."      and Mprpro = Ceepro ";
			$query = $query."      and Ceeent  = '".$wemp."'";
			$err = mysql_query($query,$conex)or die(mysql_errno().":".mysql_error());;
			$query = "select   Mprgru, grudes, Mprpro, Mprnom , Mprpor,sum(Pcapro)   from ".$empresa."_000097,".$wtable.",".$empresa."_000088  ";
			$query = $query."  where Pcaano  = ".$wanopa;
			$query = $query."      and Pcames = ".$wper2;
			$query = $query."      and Pcacco  between '".$wcco1."' and '".$wcco2."'";
			$query = $query."      and Pcacco = Mprcco ";
			$query = $query."      and Pcacod = Mprpro ";
			$query = $query."      and Pcaent = Mprent ";
			$query = $query."      and Mprgru = Grucod ";
			$query = $query."   group by   Mprgru, grudes, Mprpro, Mprnom , Mprpor ";
			$query = $query."   order by  Mprgru, Mprpro , Mprpor ";
			$err1 = mysql_query($query,$conex)or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			$query = "select   Mprgru, grudes, Mprpro, Mprnom , Mprpor,sum(Pcapro)   from ".$empresa."_000097,".$wtable.",".$empresa."_000088  ";
			$query = $query."  where Pcaano  = ".$wanop;
			$query = $query."      and Pcames = ".$wper1;
			$query = $query."      and Pcacco  between '".$wcco1."' and '".$wcco2."'";
			$query = $query."      and Pcacco = Mprcco ";
			$query = $query."      and Pcacod = Mprpro ";
			$query = $query."      and Pcaent = Mprent ";
			$query = $query."      and Mprgru = Grucod ";
			$query = $query."   group by   Mprgru, grudes, Mprpro, Mprnom , Mprpor ";
			$query = $query."   order by  Mprgru, Mprpro , Mprpor ";
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2)or die(mysql_errno().":".mysql_error());
			echo "<table border=1>";
			echo "<tr><td colspan=16 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=16 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=16 align=center>COSTO PROMEDIO PARA UNA ENTIDAD X UNIDAD</td></tr>";
			echo "<tr><td colspan=16 align=center>UNIDADES : ".$wcco1." - ".$wcco2."</td></tr>";
			echo "<tr><td colspan=16 align=center>ENTIDAD : ".$wemp." - ".$wempm."</td></tr>";
			echo "<tr><td colspan=16 align=center> A&Ntilde;O : ".$wanop."</td></tr>";
			echo "<tr><td><b>COD. PROCEDIMIENTO</b></td><td><b>NOM. PROCEDIMIENTO</b></td><td><b>% TERCERO</b></td><td><b>COSTO PROMEDIO : ".$wanopa."</b></td><td><b>TMN PROMEDIO : ".$wanopa."</b></td><td><b>COSTO PROMEDIO : ".$wanop."</b></td><td><b>TMN PROMEDIO : ".$wanop."</b></td></tr>";
			$wdata=array();
			$k1=0;
			$k2=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1=1;
				$kla1="zzzzzzzzzzzzzzzzzzzz";
			}
			else
			{
				$row1 = mysql_fetch_array($err1);
				$kla1=$row1[0].$row1[2].(string)$row1[4];
				$k1++;
			}
			if ($num2 ==  0)
			{
				$k2=1;
				$kla2="zzzzzzzzzzzzzzzzzzzz";
			}
			else
			{
				$row2 = mysql_fetch_array($err2);
				$kla2=$row2[0].$row2[2].(string)$row2[4];
				$k2++;
			}
			while ($k1 <= $num1 or $k2 <= $num2)
			{
				if($kla1 == $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=$row1[4];
					$wdata[$num][5]=$row1[5];
					$wdata[$num][6]=$row1[5] /( 1 - $row1[4]);
					$wdata[$num][7]=$row2[5];
					$wdata[$num][8]=$row2[5] /( 1 - $row2[4]);
					$k1++;
					$k2++;
					if($k1 > $num1)
						$kla1="zzzzzzzzzzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=$row1[0].$row1[2].(string)$row1[4];
					}
					if($k2 > $num2)
						$kla2="zzzzzzzzzzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=$row2[0].$row2[2].(string)$row2[4];
					}
				}
				else if($kla1 < $kla2)
				{
					$num++;
					$wdata[$num][0]=$row1[0];
					$wdata[$num][1]=$row1[1];
					$wdata[$num][2]=$row1[2];
					$wdata[$num][3]=$row1[3];
					$wdata[$num][4]=$row1[4];
					$wdata[$num][5]=$row1[5];
					$wdata[$num][6]=$row1[5] /( 1 - $row1[4]);
					$wdata[$num][7]= 0;
					$wdata[$num][8]= 0;
					$k1++;
					if($k1 > $num1)
						$kla1="zzzzzzzzzzzzzzzzzzzz";
					else
					{
						$row1 = mysql_fetch_array($err1);
						$kla1=$row1[0].$row1[2].(string)$row1[4];
					}
				}
				else
				{
					$num++;
					$wdata[$num][0]=$row2[0];
					$wdata[$num][1]=$row2[1];
					$wdata[$num][2]=$row2[2];
					$wdata[$num][3]=$row2[3];
					$wdata[$num][4]=$row2[4];
					$wdata[$num][5]= 0;
					$wdata[$num][6]= 0;
					$wdata[$num][7]=$row2[5];
					$wdata[$num][8]=$row2[5] /( 1 - $row2[4]);
					$k2++;
					if($k2 > $num2)
						$kla2="zzzzzzzzzzzzzzzzzzzz";
					else
					{
						$row2 = mysql_fetch_array($err2);
						$kla2=$row2[0].$row2[2].(string)$row2[4];
					}
				}
			}
			if($num > 0)
			{
				$wlin="";
				for ($i=0;$i<=$num;$i++)
				{
					if ($wlin != $wdata[$i][0])
					{
						echo"<tr><td bgcolor=#99CCFF colspan=16>".$wdata[$i][0]."-".$wdata[$i][1]."</td></tr>";
						$wlin = $wdata[$i][0];
					}
					if( $wdata[$i][5] != 0 and abs((($wdata[$i][7] / $wdata[$i][5]) - 1) * 100) > $wpv)
						echo"<tr><td>".$wdata[$i][2]."</td><td>".$wdata[$i][3]."</td><td  align=right>".number_format((double)$wdata[$i][4],2,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][6],0,'.',',')."</td><td  align=right bgcolor=#FF9900>".number_format((double)$wdata[$i][7],0,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][8],0,'.',',')."</td></tr>";
					else
						echo"<tr><td>".$wdata[$i][2]."</td><td>".$wdata[$i][3]."</td><td  align=right>".number_format((double)$wdata[$i][4],2,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][5],0,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][6],0,'.',',')."</td><td  align=right bgcolor=#FFFFFF>".number_format((double)$wdata[$i][7],0,'.',',')."</td><td  align=right>".number_format((double)$wdata[$i][8],0,'.',',')."</td></tr>";
				}
				echo "</tr></table>";				
			}
			$query = "DROP table ".$wtable;
			$err = mysql_query($query,$conex);
			}
			 else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>PARA ESTE A&Ntilde;O NO HAY COTOS PROMEDIOS CALCULADOS LLAME A COSTOS Y PRESUPUESTOS</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
