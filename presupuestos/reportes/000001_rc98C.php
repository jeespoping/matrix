<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
//-->
</script>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc98C.php' method=post>";
		$wtp=strtoupper ($wtp);
		$wtable= date("YmdHis");
		$wtable=" temp_".$wtable;
		$query = "Create table  IF NOT EXISTS ".$wtable." as ";
		$query = $query."select  Mprcco,Mprpro, Mprnom, Mprgru, Mprpor, Mprtip from costosyp_000095 ";
		$query = $query."  where Mprcco  between '".$wcco1."' and '".$wcco2."'";
		$query = $query."    and Mprpro  between '".$wpro1."' and '".$wpro2."'";
		if($wtp != "T")
			$query = $query."      and Mprpri = '".$wtp."'";
		$err = mysql_query($query,$conex);
		$windex="temp_".date("His");
		$query = "CREATE UNIQUE INDEX ".$windex." on ".$wtable."(Mprcco(4),Mprpro(10),Mprgru(3),Mprpor)";
		$err = mysql_query($query,$conex) or die("Error en la creacion del index ".$query);
		//                  0       1       2       3       4       5         6        7       8       9       10
		$query = "select  Mprcco, cconom, Mprgru, grudes, Mprpro, Mprnom ,  Mprpor,  Pcames, Pcactp, Pcapro, Mprtip from costosyp_000097,".$wtable.",costosyp_000005,costosyp_000088  ";
		$query = $query."  where Pcaano  = ".$wanop;
		$query = $query."      and Pcames between ".$wper1." and ".$wper2;
		$query = $query."      and Pcacco between '".$wcco1."' and '".$wcco2."'";
		$query = $query."      and Pcacco = Mprcco ";
		$query = $query."      and Pcacod = Mprpro ";
		$query = $query."      and Pcagru = Mprgru ";
		$query = $query."      and Pcacco = ccocod ";
		$query = $query."      and Mprgru = Grucod ";
		$query = $query."   order by Mprcco, Mprgru, Mprpro, Pcames ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<table border=1>";
		$seg=-1;
		$segn="";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($row[0].$row[2].$row[4].(string)$row[6] != $segn)
			{
				$seg++;
				$segn=$row[0].$row[2].$row[4].(string)$row[6];
				$wdat[$seg][0]=$row[0];
				$wdat[$seg][1]=$row[1];
				$wdat[$seg][2]=$row[2];
				$wdat[$seg][3]=$row[3];
				$wdat[$seg][4]=$row[4];
				$wdat[$seg][5]=$row[5];
				$wdat[$seg][6]=$row[6] * 100;
				for ($j=7;$j<19;$j++)
					$wdat[$seg][$j]=0;
			}
			$wdat[$seg][$row[7]+6]+=$row[8];
			$wdat[$seg][19] = $row[9];
			$wdat[$seg][20] = $row[9] / (1 - $row[6]);
			$wdat[$seg][21] = $row[10];
		}
		if($num > 0)
		{
			$wcco = "";
			$wlin="";
			for ($i=0;$i<=$seg;$i++)
			{
				if ($wcco != $wdat[$i][0])
				{
					echo"<tr><td bgcolor=#99CCFF colspan=17>".$wdat[$i][0]."-".$wdat[$i][1]."</td></tr>";
					$wcco = $wdat[$i][0];
				}
				if ($wlin != $wdat[$i][2])
				{
					echo"<tr><td bgcolor=#FFCC66 colspan=17>".$wdat[$i][2]."-".$wdat[$i][3]."</td></tr>";
					$wlin = $wdat[$i][2];
				}
				echo"<tr><td>".$wdat[$i][4]."</td><td>".$wdat[$i][5]."</td><td  align=right>".number_format((double)$wdat[$i][6],2,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][19],2,'.',',')."</td><td  align=right>".number_format((double)$wdat[$i][20],2,'.',',')."</td>";
				for ($j=$wper1;$j<=$wper2;$j++)
					if($j==$wper1 or $wdat[$i][$j+6-1] != 0)
					{
						if( $j > $wper1 and abs((($wdat[$i][$j+6] / $wdat[$i][$j+6-1]) - 1) * 100) > $wpv)
						{
							if($wdat[$i][21] == "P")
							{
								$path="/matrix/presupuestos/reportes/000001_rc89.php?wanop=".$wanop."&wper1=".$j."&wcco1=".$wcco."&wpro=".$wdat[$i][4]."&wgru=".$wlin."&wserv=S";
								echo "<td align=right bgcolor=#FF9900 onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
							}
							else
							{
								$path="/matrix/presupuestos/reportes/000001_rc92.php?wanop=".$wanop."&wper1=".$j."&wcco1=".$wcco."&wpro=".$wdat[$i][4]."&wgru=".$wlin."&wserv=S";
								echo "<td align=right bgcolor=#FF9900 onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
							}
						}
						else
						{
							if($wdat[$i][21] == "P")
							{
								$path="/matrix/presupuestos/reportes/000001_rc89.php?wanop=".$wanop."&wper1=".$j."&wcco1=".$wcco."&wpro=".$wdat[$i][4]."&wgru=".$wlin."&wserv=S";
								echo "<td align=right bgcolor=#ffffff onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
							}
							else
							{
								$path="/matrix/presupuestos/reportes/000001_rc92.php?wanop=".$wanop."&wper1=".$j."&wcco1=".$wcco."&wpro=".$wdat[$i][4]."&wgru=".$wlin."&wserv=S";
								echo "<td align=right bgcolor=#ffffff onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";
							}
						}	
					}	
					else
							echo "<td align=right bgcolor=#ffffff>".number_format((double)$wdat[$i][$j+6],0,'.',',')."</td>";	
				echo "</tr>";	
			}
			echo "</tr></table>";				
		}
		$query = "DROP table ".$wtable;
		$err = mysql_query($query,$conex);
	}
?>
</body>
</html>
