<html>
<head>
  <title>GRAFICOS</title>
</head>
<body BGCOLOR="">
<?php
include_once("conex.php");PHP

	/*************************************************************
	 *GRAFICO DE PERCENTILES DE CRECIMIENTO DE PEDIATRIA Y UNIADO*
	 *					CONEX, FREE => OK				 		 *
	 *************************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$kex = substr($user,2,strlen($user));
	

	

	if(!isset($nombre) and !isset($sexo))
	{
		echo "<form action='000010_rt01.php' method=post>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>TORRE MÉDICA</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1>GRAFICA: </td>";	
		/* Si el medico no ha sido escogido Buscar a los pediatras registrados para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><select name='nombre'>";
		$query = "select distinct Nombre from root_000010";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."</option>";
			}
		}	// fin del if $num>0
		echo "<tr><td bgcolor=#cccccc colspan=1>SEXO: </td>";
		echo "<td bgcolor=#cccccc colspan=2><input type='radio' name='sexo' value='M'>M <input type='radio' name='sexo' value='F'>F";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input tYpe='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	{
		
		$query = "select * from root_000010 where Nombre='".$nombre."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			$row = mysql_fetch_array($err);
			$tabla=$row[4];
			$nx=$row[5];
			$ny=$row[6];
			$entre=$row[7];
			$ex=$row[8];
			$ey=$row[9];
			$ix=$row[10];
			$iy=$row[11];
			$xd=$row[12];
			$yd=$row[13];
			$ys=$row[14];
			$ruta=$row[15];
		}
		echo "<center><font size='4' color='#888888' face='arial'>PERCENTILES DE CRECIMIENTO<BR>".$nombre."<br>";
		$query = "select Meses, L, M, S,P3, P5, P10, P25, P50, P75, P90, P95, P97 from root_000005 where ".$entre." and tabla='".$tabla."' and sexo='".$sexo."' order bY Meses";
		//echo "<br>".$query."<br>";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1>0)
		{
			for ($g=0 ; $g < $num1 ; $g++)
			{
				$row = mysql_fetch_row($err1);
				
				$x[$g]=($row[0]-$xd)*$ex;
				$y3[$g]=($row[4] - $yd)*$ey;
				$y5[$g]=($row[5] - $yd)*$ey;
				$y10[$g]=($row[6] - $yd)*$ey;
				$y25[$g]=($row[7] - $yd)*$ey;
				$y50[$g]=($row[8] - $yd)*$ey;
				$y75[$g]=($row[9] - $yd)*$ey;
				$y90[$g]=($row[10] - $yd)*$ey;
				$y95[$g]=($row[11] - $yd)*$ey;
				$y97[$g]=($row[12] - $yd)*$ey;
				
				/*echo "x=".$row[0]."  /".$x[$g]."   y3=".$row[4]."  /".$y3[$g]."   g=".$g."<br>";
				echo "x=".$row[0]."  /".$x[$g]."   y97=".$row[12]."  /".$y97[$g]."   g=".$g."<br>";*/
			}
		}
		
		//echo "num1= ".$num1."<br>";
		$width =$x[$num1 -1]+80; 
		//echo "y97= ".$y97[$num1 - 1]."<br>";

		$height =($y97[$num1 -1])+60+($ys * $ey); //lo may=y97 + 60=>50 pata titulo x 10 de arriba, x ys lo que va por encima de y97
		//echo $height;
		$im = ImageCreate($width, $height);
		$bck = ImageColorAllocate($im, 200,200,200);
		$gris = ImageColorAllocate($im, 225,225,225);
		$blanco = ImageColorAllocate($im, 255, 255, 255); 
		$negro = ImageColorAllocate($im, 0, 0, 0);
		
		if($sexo=="M")
		{
			$color = ImageColorAllocate($im, 0, 0, 174);
			$color1= ImageColorAllocate($im, 37, 37, 255);
			$color2= ImageColorAllocate($im, 17, 17, 255);
			$color3= ImageColorAllocate($im,22, 170, 250);
		}
		else
		{
			//$color= ImageColorAllocate($im, 210, 0, 105);
			$color1= ImageColorAllocate($im, 255, 17, 166);
			$color2= ImageColorAllocate($im, 255, 60, 160);
			$color3= ImageColorAllocate($im, 255, 128, 192);
			$color= ImageColorAllocate($im, 255, 0, 128);/*
			$color1= ImageColorAllocate($im, 255, 32, 160);
			$color2= ImageColorAllocate($im, 255, 60, 160);
			$color3= ImageColorAllocate($im, 255, 128, 192);*/
		}
		ImageFill($im, 0, 0, $bck); 
		
		for($i=0;$i<=$width;$i=$i+$ix) 
		{ // esta estatica la y dibujando la grilla ede lineas verticales
			IF(($i+50)< $width)
			{
				
				if(($i+50) <= ($width-10))
				{
					ImageLine($im,$i+70 , 10, $i+70, $height-50, $blanco); 
					if($i !=0 and ($width -$i-70)>15)
						ImageTTFTeXt($im, 11,0,$i+65, $height - 35, $blanco, '/WINDOWS/Fonts/verdana.ttf',(integer)round((($i/$ex))+$xd)); 
				}
			}
		}
		ImageLine($im,$width -10 , 10, $width -10, $height-50, $blanco); 
		ImageTTFText($im,20,0,$width/2 - 50, $height - 8, $blanco, '/WINDOWS/Fonts/verdana.ttf',$nx); 
		
		for($i=0;$i<=$height-60;$i=$i+$iy) 
		{ 
			//echo $height -50 -$i."<br>";
			// grilla de lineas horiyontales
			if(($height-$i-50)>10)
			{
				ImageLine($im, 70, $height-$i-50,$width-10, $height-$i-50,  $blanco); 
				if($i !=0)
					ImageTTFTeXt ($im, 12,0,46, $height-$i-50+5, $blanco, '/WINDOWS/Fonts/verdana.ttf', (integer)round(($i/$ey)+$yd) ); 
			}
			if(($height-$i-50-($iy/2))>10)
				ImageLine($im, 70, $height-$i-50-($iy/2),$width-10, $height-$i-50-($iy/2),  $gris);
			
			
		}
		ImageLine($im, 70, 10,$width-10, 10,  $blanco); 
		ImageTTFTeXt($im,20,90,30, $height/2 +50, $blanco, '/WINDOWS/Fonts/verdana.ttf',$ny ); 
		for ($g=1 ; $g < $num1 ; $g++)
		{
			imageline($im,$x[$g-1]+70,$height -$y3[$g-1]-50,$x[$g]+70,$height -$y3[$g]-50,$color3);
			imageline($im,$x[$g-1]+70,$height -$y5[$g-1]-50,$x[$g]+70,$height -$y5[$g]-50,$color2);
			imageline($im,$x[$g-1]+70,$height -$y10[$g-1]-50,$x[$g]+70,$height -$y10[$g]-50,$color1);
			imageline($im,$x[$g-1]+70,$height -$y25[$g-1]-50,$x[$g]+70,$height -$y25[$g]-50,$color);
			imageline($im,$x[$g-1]-1+70,$height -$y50[$g-1]-1-50,$x[$g]-1+70,$height -$y50[$g]-1-50,$color);
			imageline($im,$x[$g-1]+70,$height -$y50[$g-1]-50,$x[$g]+70,$height -$y50[$g]-50,$color);
			imageline($im,$x[$g-1]+1+70,$height -$y50[$g-1]+1-50,$x[$g]+1+70,$height -$y50[$g]+1-50,$color);
			imageline($im,$x[$g-1]+70,$height -$y75[$g-1]-50,$x[$g]+70,$height -$y75[$g]-50,$color);
			imageline($im,$x[$g-1]+70,$height -$y90[$g-1]-50,$x[$g]+70,$height -$y90[$g]-50,$color1);
			imageline($im,$x[$g-1]+70,$height -$y95[$g-1]-50,$x[$g]+70,$height -$y95[$g]-50,$color2);
			imageline($im,$x[$g-1]+70,$height -$y97[$g-1]-50,$x[$g]+70,$height -$y97[$g]-50,$color3);
		}
		
		imagefilledrectangle($im,$x[$num1-5]+69,$height -$y3[$num1-5]-56,$x[$num1-5]+78,$height -$y3[$num1-5]-46,$bck);
		imagefilledrectangle($im,$x[$num1-4]+69,$height -$y5[$num1-4]-56,$x[$num1-4]+78,$height -$y5[$num1-4]-46,$bck);
		imagefilledrectangle($im,$x[$num1-4]+69,$height -$y10[$num1-4]-56,$x[$num1-4]+84,$height -$y10[$num1-4]-46,$bck);
		imagefilledrectangle($im,$x[$num1-4]+69,$height -$y25[$num1-4]-56,$x[$num1-4]+84,$height -$y25[$num1-4]-46,$bck);
		imagefilledrectangle($im,$x[$num1-4]+69,$height -$y50[$num1-4]-56,$x[$num1-4]+84,$height -$y50[$num1-4]-46,$bck);
		imagefilledrectangle($im,$x[$num1-4]+69,$height -$y75[$num1-4]-56,$x[$num1-4]+84,$height -$y75[$num1-4]-46,$bck);
		imagefilledrectangle($im,$x[$num1-4]+69,$height -$y90[$num1-4]-56,$x[$num1-4]+84,$height -$y90[$num1-4]-46,$bck);
		imagefilledrectangle($im,$x[$num1-4]+69,$height -$y95[$num1-4]-56,$x[$num1-4]+84,$height -$y95[$num1-4]-46,$bck);
		imagefilledrectangle($im,$x[$num1-5]+69,$height -$y97[$num1-5]-56,$x[$num1-5]+84,$height -$y97[$num1-5]-46,$bck);
		
		ImageTTFText($im,10,0,$x[$num1-5]+70,$height -$y3[$num1-5]-47,$color3,'/WINDOWS/Fonts/verdana.ttf','3');
		ImageTTFText($im,10,0,$x[$num1-4]+70,$height -$y5[$num1-4]-47,$color2,'/WINDOWS/Fonts/verdana.ttf','5');
		ImageTTFText($im,10,0,$x[$num1-4]+70,$height -$y10[$num1-4]-47,$color1,'/WINDOWS/Fonts/verdana.ttf','10');
		ImageTTFText($im,10,0,$x[$num1-4]+70,$height -$y25[$num1-4]-47,$color,'/WINDOWS/Fonts/verdana.ttf','25');
		ImageTTFText($im,10,0,$x[$num1-4]+70,$height -$y50[$num1-4]-47,$color,'/WINDOWS/Fonts/verdana.ttf','50');
		ImageTTFText($im,10,0,$x[$num1-4]+70,$height -$y75[$num1-4]-47,$color,'/WINDOWS/Fonts/verdana.ttf','75');
		ImageTTFText($im,10,0,$x[$num1-4]+70,$height -$y90[$num1-4]-47,$color1,'/WINDOWS/Fonts/verdana.ttf','90');
		ImageTTFText($im,10,0,$x[$num1-4]+70,$height -$y95[$num1-4]-47,$color2,'/WINDOWS/Fonts/verdana.ttf','95');
		ImageTTFText($im,10,0,$x[$num1-5]+70,$height -$y97[$num1-5]-47,$color3,'/WINDOWS/Fonts/verdana.ttf','97');

		
	ImagePng ($im,"c:\inetpub\wwwroot\MATRIX\images\medical\pediatra\\".$ruta."_".$sexo.".png");
	echo "<center><IMG SRC='\MATRIX\images\medical\pediatra\\".$ruta."_".$sexo.".png'></center>"; 
	}
}

?>
</body>
</html>


  