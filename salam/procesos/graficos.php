<html>

<head>
  <title>GRAFICOS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");PHP

	/************************************************************
	 *   GRAFICO DE VARIABLES HEMODINAMICAS DE 000002_pro1.php  *
	 *					CONEX, FREE => OK				 		*
	 ************************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<meta http-equiv='refresh' content='20;url=graficos.php?med=".$med."&amp;fecha=".$fecha."&amp;paciente=".$paciente."&amp;hi=".$hi."&amp;horas=".$horas."'>";
	$height = 430; 
	$width =( $horas * 30 ) +70; 
	$im = ImageCreate($width, $height);
	$bck = ImageColorAllocate($im, 225,225,225);
	$blanco = ImageColorAllocate($im, 255, 255, 255); 
	$negro = ImageColorAllocate($im, 0, 0, 0);
	ImageFill($im, 0, 0, $bck); 

	
	$im1 = ImageCreate(70, 270);
	$bck1 = ImageColorAllocate($im1, 225,225,225);
	$blanco1 = ImageColorAllocate($im1, 255, 255, 255); 
	$negro1 = ImageColorAllocate($im1, 0, 0, 0);
	ImageFill($im1, 0, 0, $bck); 

	$P=0;
	for($i=0;$i<=400;$i=$i+20) 
	{ 
		if($i != 420)
		ImageTTFText ($im, 12,0,10, $i+13, $negro, '/WINDOWS/Fonts/verdana.ttf', 200-$i/2 ); 
		ImageLine($im, 40, $i+10,$width, $i+10, $negro); 
		$P++;
	}
	ImageLine($im, 50, 0,50,$height, $negro); 
	$ini1=strpos($paciente,"-");  
	$V=array();
	$query = "select hora,count(*) from salam_000002 where anestesiologo='".$med."' and fecha='".$fecha."' and paciente='".substr($paciente,0,$ini1)."' and hora_inicio='".$hi."' group by hora order by hora";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		for ($j=0;$j<$num;$j++)
		{	
			$row = mysql_fetch_array($err);
			$V[$j+1]=$row[0];
		}
		$NC=$num;
		$P=1;
		for($i=80;$i<=$width;$i=$i+30) 
	{ 
		ImageLine($im,$i,$height-20  , $i,$height-10, $negro); 
		ImageTTFText ($im, 8,0,$i-10,$height-2, $negro, '/WINDOWS/Fonts/verdana.ttf', $V[$P] );
		$P++; 
	}

	$query = "select Codigo_n,Codigo_a,Color  from salam_000001 where Tipo='01-GRAFICABLE'";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	if($num1>0)
	{
		for ($g=0 ; $g < $num1 ; $g++)
		{	
			$rowo = mysql_fetch_row($err1);
			$col1=strpos($rowo[2],',');
			$col2=strpos($rowo[2],',',$col1+1);
			$color= ImageColorAllocate($im,substr($rowo[2],0,$col1),substr($rowo[2],$col1+1,$col2-$col1-1),substr($rowo[2],$col2+1,strlen($rowo[2])));
			$color1= ImageColorAllocate($im1,substr($rowo[2],0,$col1),substr($rowo[2],$col1+1,$col2-$col1-1),substr($rowo[2],$col2+1,strlen($rowo[2])));
			ImageTTFText ($im1,10,0,5,$g * 20 +20, $negro, '/WINDOWS/Fonts/verdana.ttf', $rowo[1]);
			ImageLine($im1,40,$g *20 + 15,60,$g *20 + 15,$color1);// $col1[(integer)$rowo[0]]); 
			ImageLine($im1,40,$g *20 + 16,60,$g *20 + 16, $color1);//$col1[(integer)$rowo[0]]); 
			//echo $g." = ".$rowo[0]." ".$rowo[1]."<br>";
			
			$query = "select hora,valor from salam_000002 where Anestesiologo='".$med."' and Fecha='".$fecha."' and Paciente='".substr($paciente,0,$ini1)."' and Hora_inicio='".$hi."'  and parametro='".$rowo[0]."'   order by hora";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$valor=array(); $r=0;
			$hora=array();
			if($num>0)
			{
				for($i=0;$i<=$num;$i++)
				{
					$row=mysql_fetch_row($err);
					for ($m=1;$m<=$NC;$m++)
					{
						if($row[0] == $V[$m])
							$nc=$m;
					}
					if($row[1] > 200)
						$row[1]=200;
					if($row[1] < 0 )
						$row[1] = 0;
					$hora[$r]=$nc * 30 +50;
					$valor[$r]=400 -( $row[1] * 2);
					//echo "(".$hora[$r].",".$valor[$r].")= (".$V[$nc].",".$row[1].")<br>";
					if($r != 0 and $row[1] != "")   
					{
						ImageLine($im, $hora[$r-1],$valor[$r-1],$hora[$r],$valor[$r], $color); 
						ImageLine($im, $hora[$r-1],$valor[$r-1]+1,$hora[$r],$valor[$r]+1, $color); 
					}
					if($row[1] != "")   
						imagerectangle($im,$hora[$r]-2,$valor[$r]-2,$hora[$r]+2,$valor[$r]+2,$negro);//$col[(integer)$rowo[0]]);
					$r++;
				}
			}
		}
	}
	$ini1=strpos($paciente,"-");
	ImagePng ($im,"c:\inetpub\wwwroot\MATRIX\images\medical\salam\\".substr($paciente,0,$ini1).".png");
	ImagePng ($im1,"c:\inetpub\wwwroot\MATRIX\images\medical\salam\grafica1.png");
	echo "<center><table border=0>";
	echo "<tr><td align=center rowspan=7><img SRC='\MATRIX\images\medical\salam\cirugia.jpg' size=60% width=60%></td>";
	echo "<tr><td colspan=2  align=center><font  size=3 face='verdana'><b>REGISTRO ANESTESICO AUTOMATICO CONTINUO<br> LINEAS DE TENDENCIA</b></font></td></tr>";
	echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>ANESTESIOLOGO : </td ><td bgcolor=#cccccc><font  size=2 face='arial'>".substr($med,3,strlen($med))."</font></td></tr>";
	echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>PACIENTE : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".strtoupper($paciente)."</font></td></tr>";
	echo "<tr><td bgcolor=#cccccc><font  size=2 face='arial'>FECHA : </td><td bgcolor=#cccccc><font  size=2 face='arial'>".$fecha."</font></td></tr></table><br><br>";
	echo "<center><table border=0> <tr valign=top><td align=center><IMG SRC='\MATRIX\images\medical\salam\grafica1.png'></td>"; 
	echo "<td align=center><IMG SRC='\MATRIX\images\medical\salam\\".substr($paciente,0,$ini1).".png'></td></tr></table></center>"; 

	echo "<form action='graficos.php' method=post>";
	echo "<input type='HIDDEN' name= 'horas' value='".$horas."'>";
	include_once("free.php");

				

}

?>
</body>
</html>
