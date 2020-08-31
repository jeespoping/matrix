<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function framingham($fnac,$sexo,$fumador,$sistole_s,$sistole_p,$hdl,$colesterol,$hiper_t,$diabetes,$glicemia)
{
	$hoy=date("Y-m-d");
	$ayer=$fnac;
	$dias1=(integer)substr($hoy,0,4)*365+(integer)substr($hoy,5,2)*30+(integer)substr($hoy,8,2);
	$dias2=(integer)substr($ayer,0,4)*365+(integer)substr($ayer,5,2)*30+(integer)substr($ayer,8,2);
	$edad = (integer)(($dias1-$dias2)/365);
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
			if ($edad >=35 and $edad <=39)
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
			if($fumador=="on")
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
			if ($edad >=35 and $edad <=39)
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
			#echo "fra colesterol:".$fra."<br>";
			$data=array();
			$data[0]=9;
			$data[1]=7;
			$data[2]=4;
			$data[3]=2;
			$data[4]=1;
			if($fumador=="on")
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
			#echo "fra sistole:".$fra."<br>";
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
	if ($diabetes=="on" or $glicemia >= 126)
		$framingham=30;
	#echo "FRAMINGHAM  ".$framingham."<br>";	
	return $framingham;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rep2.php' method=post>";
		if(!isset($cedula_i) or !isset($cedula_f))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>JORNADA CARDIOVASCULAR</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Digite los Numeros de la Cedula Inicial  y Final a Imprimir</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><input type='TEXT' name='cedula_i' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><input type='TEXT' name='cedula_f' size=12 maxlength=12></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Digite el Rango Inicial y Final de Pacientes</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><input type='TEXT' name='LI' size=5 maxlength=5></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><input type='TEXT' name='LS' size=5 maxlength=5></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><input type='submit' value='GRABAR'></td></tr></table>";
		}
		else
		{
			$query = "select identificacion,nombres,f_nacimiento,sexo  from cardio_000002 where identificacion between'".$cedula_i."' and '".$cedula_f."' order by nombres";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$paciente=0;
				for ($i=0;$i<$num;$i++)
				{
				$row = mysql_fetch_array($err);
				$query = "select paciente,fumador,imc,sistole_sentado,sistole_pie,diastole_sentado,diastole_pie,glicemia,hdl,ldl,trigliceridos,colesterol,hipertension_tratada,cintura,Hvi,peso,estatura,diabetes,glicemia  ";
				$query = $query." from cardio_000001 where paciente='".$row[0]."-".$row[1]."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					$paciente++;
					if( $paciente >= $LI and $paciente <= $LS)
					{
					echo "<table border=1>";
					echo "<tr><td colspan=4 align=center><IMG SRC='/MATRIX/images/medical/cardio/jornada.jpg' ></td></tr>";
					$row1 = mysql_fetch_array($err1);
					echo "<tr><td bgcolor=#cccccc colspan=1><B>CEDULA: ".$row[0]."</B></td><td  bgcolor=#cccccc colspan=2><B>NOMBRES: ".$row[1]."</B></td><td  bgcolor=#cccccc>NRO:".$paciente."</tr>";
					echo "<tr><td colspan=4><B>INTRODUCCION:</B></td></tr>";
					echo "<tr><td colspan=4><font size=3><P><tt>LA MOTIVACION DE ESTA JORNADA ES EL VALORAR EN USTED EL RIESGO ABSOLUTO DE LA ENFERMEDAD CORONARIA, LO CUAL SIGNIFICA QUE ESTAMOS CALCULANDO SEGUN EL SCORE DE FRAMINGHAM LA PROBABILIDAD DE QUE USTED DESARROLLE UNA ENFERMEDAD SEVERA CARDIOVASCULAR (INFARTO DE CORAZON O MUERTE CARDIACA) EN LOS PROXIMOS 10 ANOS</tt></P></FONT></td></tr>";
					echo "<tr><td colspan=4><B>1) EXAMENES DE LABORATORIO E INTERPRETACION IMC (Indice de Masa Corporal)</B></td></tr>";
					echo "<tr><td  bgcolor=#ffffff colspan=1>GLICEMIA : </td><td bgcolor=#cccccc colspan=1 align=right><B>".$row1[7]." mg/dl</B> </td><td  bgcolor=#ffffff colspan=1>HDL : </td><td bgcolor=#cccccc colspan=1 align=right><B>".$row1[8]." mg/dl </b></td></tr>";
					echo "<tr><td  bgcolor=#ffffff colspan=1>LDL : </td><td bgcolor=#cccccc colspan=1 align=right><b>".$row1[9]." mg/dl </b></td><td  bgcolor=#ffffff colspan=1>TRIGLICERIDOS : </td><td bgcolor=#cccccc colspan=1 align=right><b>".$row1[10]." mg/dl </b></td></tr>";
					$col_hdl=$row1[11]/$row1[8];
					echo "<tr><td  bgcolor=#ffffff colspan=1>COLESTEROL : </td><td bgcolor=#cccccc colspan=1 align=right><b>".$row1[11]." mg/dl </b></td><td  bgcolor=#ffffff colspan=1>COLESTEROL/HDL : </td><td bgcolor=#cccccc colspan=1 align=right><b>".number_format((double)$col_hdl,2,'.',',')." </b></td></tr>";
					echo "<tr><td colspan=4 align=center><B>CIFRAS TENSIONALES</B></td></tr>";
					$depie=$row1[4]."/".$row1[6];
					$sentado=$row1[3]."/".$row1[5];
					echo "<tr><td  bgcolor=#ffffff colspan=1>SENTADO : </td><td bgcolor=#cccccc colspan=1 align=center><b>".$sentado." </b></td><td  bgcolor=#ffffff colspan=1>DE PIE : </td><td bgcolor=#cccccc colspan=1 align=center><b>".$depie." </b></td></tr>";
					if ($row1[14]=="on")
						$hvi="SI";
					else
						$hvi="NO";
					echo "<tr><td colspan=2>HIPERTROFIA VENTRICULAR IZQUIERDA</td><td colspan=2 align=center bgcolor=#cccccc><B>".$hvi."</b></td></tr>";
					echo "<tr><td colspan=2>PERIMETRO DE CINTURA</td><td colspan=2 align=center bgcolor=#cccccc><B>".$row1[13]."</b></td></tr>";
					$imc=$row1[15]/pow($row1[16],2);
					echo "<tr><td colspan=4 align=center><B>INDICE DE MASA CORPORAL</B></td></tr>";
					echo "<tr><td  bgcolor=#ffffff rowspan=4 colspan=1>VALOR: </td><td bgcolor=#cccccc  rowspan=4 colspan=1 align=center><b>".number_format((double)$imc,2,'.',',')." Kg/Mts<sup>2</sup></b></td><td  bgcolor=#ffffff colspan=1>NORMAL : </td><td bgcolor=#cccccc colspan=1 align=center><b>18.5 - 24.99</b></td></tr>";
					echo "<tr><td  bgcolor=#ffffff colspan=1>SOBREPESO GRADO I : </td><td bgcolor=#cccccc colspan=1 align=center><b>25 - 29.99</b></td></tr>";
					echo "<tr><td  bgcolor=#ffffff colspan=1>SOBREPESO GRADO II : </td><td bgcolor=#cccccc colspan=1 align=center><b>30 - 39.99</b></td></tr>";
					echo "<tr><td  bgcolor=#ffffff colspan=1>SOBREPESO GRADO III : </td><td bgcolor=#cccccc colspan=1 align=center><b>> 40</b></td></tr>";
					echo "<tr><td colspan=4><B>2) SCORE DE FRAMINGHAM</B></td></tr>";
					$score=framingham($row[2],$row[3],$row1[1],$row1[3],$row1[4],$row1[8],$row1[11],$row1[12],$row1[17],$row1[18]);
					if ($score < 11)
						$color = "#009933";
					else
						if ($score > 10 and $score < 20)
							$color = "#FFFF00";	
						else
							$color = "#FF0000";
					echo "<tr><td  bgcolor=#cccccc colspan=2>NIVEL DE RIESGO ALCANZADO EN PORCENTAJE : </td><td bgcolor=".$color." colspan=2 align=center><b>".$score."%</b></td></tr>";
					echo "<tr><td colspan=4><B>3) INTERPRETACION DEL SCORE DE FRAMINGHAM SEGUN CODIGO DE COLORES</B></td></tr>";
					echo "<tr><td  bgcolor=#ffffff colspan=1>COLOR VERDE: </td><td bgcolor=#009933 colspan=1 align=center><b>0% - 10%</b></td><td  bgcolor=#ffffff colspan=2><P><TT>SE INTERPRETA COMO UN RIESGO BAJO PARA PRESENTAR ENFERMEDAD CORONARIA (INFARTO DE CORAZON O MUERTE POR PROBLEMAS DE CORAZON) EN LOS PROXIMOS 10 ANOS</TT></P></td></tr>";
					echo "<tr><td  bgcolor=#ffffff colspan=1>COLOR AMARILLO: </td><td bgcolor=#FFFF00 colspan=1 align=center><b>>10% - <20%</b></td><td  bgcolor=#ffffff colspan=2><P><TT>SE INTERPRETA COMO UN RIESGO MODERADO PARA PRESENTAR ENFERMEDAD CORONARIA (INFARTO DE CORAZON O MUERTE POR PROBLEMAS DE CORAZON) EN LOS PROXIMOS 10 ANOS</TT></P></td></tr>";
					echo "<tr><td  bgcolor=#ffffff colspan=1>COLOR ROJO: </td><td bgcolor=#FF0000 colspan=1 align=center><b>> 20%</b></td><td  bgcolor=#ffffff colspan=2><P><TT>SE INTERPRETA COMO UN RIESGO MUY ALTO PARA PRESENTAR ENFERMEDAD CORONARIA (INFARTO DE CORAZON O MUERTE POR PROBLEMAS DE CORAZON) EN LOS PROXIMOS 10 ANOS</TT></P></td></tr>";
					echo "<tr><td colspan=4><B>PAUTAS GENERALES</B></td></tr>";
					if ($score < 11)
						$color = "VERDE";
					else
						if ($score > 10 and $score < 20)
							$color = "AMARILLO";	
						else
							$color = "ROJO";
					echo "<tr><td colspan=4>RECOMENDACIONES SEGUN COLOR : ".$color."</td></tr>";
					$query = "select recomendacion from cardio_000003 where color='".$color."'  ";
					$err2 = mysql_query($query,$conex);
					$row2 = mysql_fetch_array($err2);
					echo "<tr><td  bgcolor=#ffffff colspan=4><p><tt>".$row2[0]."</tt></p></td></tr>";
					echo "</table>";	
					echo "<div style='page-break-before: always'>";	
					}
				}
				else
				{
					//echo "<center><table border=0 aling=center>";
					//echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					//echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL ESTUDIO CARDIOVASCULAR !!!!</MARQUEE></FONT>";
					//echo "<br><br>";
				}
			}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL PACIENTE !!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
	}
?>
</body>
</html>