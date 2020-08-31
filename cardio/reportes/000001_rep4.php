<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function framingham($fnac,$sexo,$fumador,$sistole_s,$sistole_p,$hdl,$colesterol,$hiper_t)
{
	$hoy=date("Y-m-d");
	$ayer=$fnac;
	$dias1=(integer)substr($hoy,0,4)*365+(integer)substr($hoy,5,2)*30+(integer)substr($hoy,8,2);
	$dias2=(integer)substr($ayer,0,4)*365+(integer)substr($ayer,5,2)*30+(integer)substr($ayer,8,2);
	$edad = ($dias1-$dias2)/365;
	#echo "EDAD CALCULADA  ".$edad."<br>";
	#echo "FUMADOR  ".$fumador."<br>";
	#echo "SISTOLE_SEN  ".$sistole_s."<br>";
	#echo "SISTOLE_PIE  ".$sistole_p."<br>";
	#echo "HDL  ".$hdl."<br>";
	#echo "COLESTEROL  ".$colesterol."<br>";
	#echo "HIPERTENSION_TR  ".$hiper_t."<br>";
	$fra=0;
	$ini=strpos($sexo,"-");
	$sexo=substr($sexo,$ini+1,strlen($sexo));
	#echo "SEXO  ".$sexo."<br>";
	switch ($sexo)
	{
		case "MASCULINO":
			if ($edad < 20)
			{
				$fra=-9;
				$per=1;
			}
			if ($edad >=20 and $edad <=34)
			{
				$fra=-9;
				$per=1;
			}
			if ($edad >=36 and $edad <=39)
			{
				$fra=-4;
				$per=1;
			}
			if ($edad >=40 and $edad <=44)
			{
				$fra=0;
				$per=2;
			}
			if ($edad >=45 and $edad <=49)
			{
				$fra=3;
				$per=2;
			}
			if ($edad >=50 and $edad <=54)
			{
				$fra=6;
				$per=3;
			}
			if ($edad >=55 and $edad <=59)
			{
				$fra=8;
				$per=3;
			}
			if ($edad >=60 and $edad <=64)
			{
				$fra=10;
				$per=4;
			}
			if ($edad >=65 and $edad <=69)
			{
				$fra=11;
				$per=4;
			}
			if ($edad >=70 and $edad <=74)
			{
				$fra=12;
				$per=5;
			}
			if ($edad >=75 and $edad <=79)
			{
				$fra=13;
				$per=5;
			}
			if ($edad >79)
			{
				$fra=13;
				$per=5;
			}
			#echo "fra edad:".$fra."<br>";
			$data=array();
			$data[0][0]=0;
			$data[0][1]=0;
			$data[0][2]=0;
			$data[0][3]=0;
			$data[0][4]=0;
			$data[1][0]=4;
			$data[1][1]=3;
			$data[1][2]=2;
			$data[1][3]=1;
			$data[1][4]=0;
			$data[2][0]=7;
			$data[2][1]=5;
			$data[2][2]=3;
			$data[2][3]=1;
			$data[2][4]=0;
			$data[3][0]=9;
			$data[3][1]=6;
			$data[3][2]=4;
			$data[3][3]=2;
			$data[3][4]=1;
			$data[4][0]=11;
			$data[4][1]=8;
			$data[4][2]=5;
			$data[4][3]=3;
			$data[4][4]=1;
			if($colesterol < 160)
				$fra=$fra+$data[0][$per-1];
			if($colesterol >= 160 and $colesterol <= 199)
				$fra=$fra+$data[1][$per-1];
			if($colesterol >=200 and $colesterol <= 239)
				$fra=$fra+$data[2][$per-1];
			if($colesterol >=240 and $colesterol <= 279)
				$fra=$fra+$data[3][$per-1];
			if($colesterol >= 280)
				$fra=$fra+$data[4][$per-1];
			#echo "fra colesterol:".$fra."<br>";
			$data=array();
			$data[0]=8;
			$data[1]=5;
			$data[2]=3;
			$data[3]=1;
			$data[4]=1;
			if($fumador="on")
				$fra=$fra+$data[$per-1];
			#echo "fra fumador:".$fra."<br>";
			if($hdl >=60)
				$fra=$fra-1;
			if($hdl >=50 and $hdl <= 59)
				$fra=$fra;
			if($hdl >=40 and $hdl <=49)
				$fra=$fra+1;
			if($hdl < 40)
				$fra=$fra+2;
			#echo "fra hdl:".$fra."<br>";
			$sistole=($sistole_s+$sistole_p)/2;
			$per=0;
			if($hiper_t=="on")
				$per=1;
			$data=array();
			$data[0][0]=0;
			$data[0][1]=0;
			$data[1][0]=0;
			$data[1][1]=1;
			$data[2][0]=1;
			$data[2][1]=2;
			$data[3][0]=1;
			$data[3][1]=2;
			$data[4][0]=2;
			$data[4][1]=3;
			if($sistole < 120)
				$fra=$fra+$data[0][$per];
			if($sistole >= 120 and $sistole <= 129)
				$fra=$fra+$data[1][$per];
			if($sistole >= 130 and $sistole <= 139)
				$fra=$fra+$data[2][$per];
			if($sistole >= 140 and $sistole <= 159)
				$fra=$fra+$data[3][$per];
			if($sistole >= 160)
				$fra=$fra+$data[4][$per];
			#echo "fra sistole:".$fra."<br>";
			if($fra < 0)
				$framingham=0;
			if($fra == 0)
				$framingham=1;
			if($fra == 1)
				$framingham=1;
			if($fra == 2)
				$framingham=1;
			if($fra == 3)
				$framingham=1;
			if($fra == 4)
				$framingham=1;
			if($fra == 5)
				$framingham=2;
			if($fra == 6)
				$framingham=2;
			if($fra == 7)
				$framingham=3;
			if($fra == 8)
				$framingham=4;
			if($fra == 9)
				$framingham=5;
			if($fra == 10)
				$framingham=6;
			if($fra== 11)
				$framingham=8;
			if($fra == 12)
				$framingham=10;
			if($fra == 13)
				$framingham=12;
			if($fra == 14)
				$framingham=16;
			if($fra == 15)
				$framingham=20;
			if($fra == 16)
				$framingham=25;
			if($fra >= 17)
				$framingham=30;
			break;
		case "FEMENINO":
			if ($edad < 20)
			{
				$fra=-7;
				$per=1;
			}
			if ($edad >=20 and $edad <=34)
			{
				$fra=-7;
				$per=1;
			}
			if ($edad >=36 and $edad <=39)
			{
				$fra=-3;
				$per=1;
			}
			if ($edad >=40 and $edad <=44)
			{
				$fra=0;
				$per=2;
			}
			if ($edad >=45 and $edad <=49)
			{
				$fra=3;
				$per=2;
			}
			if ($edad >=50 and $edad <=54)
			{
				$fra=6;
				$per=3;
			}
			if ($edad >=55 and $edad <=59)
			{
				$fra=8;
				$per=3;
			}
			if ($edad >=60 and $edad <=64)
			{
				$fra=10;
				$per=4;
			}
			if ($edad >=65 and $edad <=69)
			{
				$fra=12;
				$per=4;
			}
			if ($edad >=70 and $edad <=74)
			{
				$fra=14;
				$per=5;
			}
			if ($edad >=75 and $edad <=79)
			{
				$fra=16;
				$per=5;
			}
			if ($edad >79)
			{
				$fra=16;
				$per=5;
			}
			$data=array();
			$data[0][0]=0;
			$data[0][1]=0;
			$data[0][2]=0;
			$data[0][3]=0;
			$data[0][4]=0;
			$data[1][0]=4;
			$data[1][1]=3;
			$data[1][2]=2;
			$data[1][3]=1;
			$data[1][4]=0;
			$data[2][0]=8;
			$data[2][1]=6;
			$data[2][2]=4;
			$data[2][3]=2;
			$data[2][4]=1;
			$data[3][0]=11;
			$data[3][1]=8;
			$data[3][2]=5;
			$data[3][3]=3;
			$data[3][4]=2;
			$data[4][0]=13;
			$data[4][1]=10;
			$data[4][2]=7;
			$data[4][3]=4;
			$data[4][4]=2;
			if($colesterol < 160)
				$fra=$fra+$data[0][$per-1];
			if($colesterol >= 160 and $colesterol <= 199)
				$fra=$fra+$data[1][$per-1];
			if($colesterol >=200 and $colesterol <= 239)
				$fra=$fra+$data[2][$per-1];
			if($colesterol >=240 and $colesterol <= 279)
				$fra=$fra+$data[3][$per-1];
			if($colesterol >= 280)
				$fra=$fra+$data[4][$per-1];
			$data=array();
			$data[0]=9;
			$data[1]=7;
			$data[2]=4;
			$data[3]=2;
			$data[4]=1;
			if($fumador="on")
				$fra=$fra+$data[$per-1];
			if($hdl >=60)
				$fra=$fra-1;
			if($hdl >=50 and $hdl <= 59)
				$fra=$fra;
			if($hdl >=40 and $hdl <=49)
				$fra=$fra+1;
			if($hdl < 40)
				$fra=$fra+2;
			$sistole=($sistole_s+$sistole_p)/2;
			$per=0;
			if($hiper_t=="on")
				$per=1;
			$data=array();
			$data[0][0]=0;
			$data[0][1]=0;
			$data[1][0]=1;
			$data[1][1]=3;
			$data[2][0]=2;
			$data[2][1]=4;
			$data[3][0]=3;
			$data[3][1]=5;
			$data[4][0]=4;
			$data[4][1]=6;
			if($sistole < 120)
				$fra=$fra+$data[0][$per];
			if($sistole >= 120 and $sistole <= 129)
				$fra=$fra+$data[1][$per];
			if($sistole >= 130 and $sistole <= 139)
				$fra=$fra+$data[2][$per];
			if($sistole >= 140 and $sistole <= 159)
				$fra=$fra+$data[3][$per];
			if($sistole >= 160)
				$fra=$fra+$data[4][$per];
			if($fra < 9)
				$framingham=0;
			if($fra == 9)
				$framingham=1;
			if($fra == 10)
				$framingham=1;
			if($fra == 11)
				$framingham=1;
			if($fra == 12)
				$framingham=1;
			if($fra == 13)
				$framingham=2;
			if($fra == 14)
				$framingham=2;
			if($fra == 15)
				$framingham=3;
			if($fra == 16)
				$framingham=4;
			if($fra == 17)
				$framingham=5;
			if($fra == 18)
				$framingham=6;
			if($fra == 19)
				$framingham=8;
			if($fra == 20)
				$framingham=11;
			if($fra == 21)
				$framingham=14;
			if($fra == 22)
				$framingham=17;
			if($fra == 23)
				$framingham=22;
			if($fra == 24)
				$framingham=27;
			if($fra >= 25)
				$framingham=30;
			break;
		}
	#echo "fra : ".$fra."<br>";
	#echo "FRAMINGHAM  ".$framingham."<br>";	
	return $framingham;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$paginas=0;
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rep2.php' method=post>";
		echo "<center><table border=1>";
		echo "<tr><td align=center colspan=4><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=4>JORNADA CARDIOVASCULAR</td></tr>";
		echo "<tr><td>Identificacion</td><td>Nombres</td><td>Telefonos</td><td>Encuesta</td></tr>";
		$query = "select identificacion,nombres,telefonos  from cardio_000002  order by nombres";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$query = "select nro_encuesta  from cardio_000001 where paciente='".$row[0]."-".$row[1]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row1[0]."</td></tr>";
				}
				else
					echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>SIN ENCUESTA</td></tr>";
				$paginas++;
				if ($paginas == 38)
				{
					echo "</table>";
					echo "<div style='page-break-before: always'>";
					echo "<center><table border=1>";
					echo "<tr><td align=center colspan=4><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=4>JORNADA CARDIOVASCULAR</td></tr>";
					echo "<tr><td>Identificacion</td><td>Nombres</td><td>Telefonos</td><td>Encuesta</td></tr>";
					$paginas=0;
				}
			}
			echo "</table>";	
		}
	}
?>
</body>
</html>