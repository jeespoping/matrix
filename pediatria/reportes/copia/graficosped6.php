<html>
<head>
  <title>GRAFICOS V1.01</title>
</head>
<body BGCOLOR="">
<?php
include_once("conex.php");PHP

	/************************************************************
	 *   GRAFICO DE PERCENTILES DE CRECIMIENTO PARA NIÑOS Y NIÑAS *
	 *					CONEy, FREE => OK		v1.01		 		*
	 ************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	//$kex = substr($user,2,strlen($user));
	

	

	//echo "isset=".!isset($nombre)."<br>";
	if(!isset($nombre) or !isset($pac) or !isset($medico))
	{
		echo "<form action='graficosped6.php' method=post>";
		echo "<center><table border=0 width=400>";
//		echo sqrt(4);
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
		echo "<tr><td bgcolor=#cccccc colspan=1>MEDICO: </td>";	

		/* Si el medico no ha sido escogido Buscar a los pediatras registrados para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
		$query = "select Subcodigo,Descripcion from det_selecciones where medico='pediatra' and codigo='004'order by Descripcion";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				$med=$row[0]."-".$row[1];
				if($med==$medico)
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}	// fin del if $num>0
		
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1>PACIENTE: </td>";	
		
		/* Si el paciente no esta set construir el drop down */
		echo "</td><td bgcolor=#cccccc colspan=2>";
		//if(isset($medico))
		//{
			echo "<select name='pac'>";
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			$query = "select distinct Paciente from pediatra_000002  order by Paciente ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
				if(isset($pac)	and $pac==$row[0])
					echo "<option selected>".$row[0]."</option>";
				else
					echo "<option>".$row[0]."</option>";
						
				}
			}	// fin $num>0
		//}	//fin isset medico
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
		}		// fin del if $num>0
		//echo $nombre."<br>";
		switch ($nombre)
		{
			case "Peso por Edad de 0 a 36 meses.":
				$buscar="Fecha,Peso_kilos";
				$bentre="";
			break;
			case "Talla por Edad de 0 a 36 meses":
				$buscar="Fecha,Talla";
				$bentre="";
			break;
			case "Perimetro Cefalico por Edad de 0 a 36 meses":
				$buscar="Fecha,Perimetro_cefalico";
				$bentre="";
			break;
			case "Talla por edad 2 a 20 Anos":
				$buscar="Fecha,Talla";
				$bentre=" and Meses>24";
			break;
			case "Indice de Masa Corporal (IMC) por Edad de 2 a 20 Anos":
				$buscar="Fecha,Peso_kilos,Talla";
				$bentre=" and Meses>24";
			break;
			case "Peso por Talla":
				$buscar="Talla,Peso_kilos";
				$bentre="";
			break;
			
		}
		
		$ini1=strpos($pac,"-");
		$ap1=substr($pac,0,$ini1);
		$ini2=strpos($pac,"-",$ini1+1);
		$ap2=substr($pac,$ini1+1,$ini2-$ini1-1);
		$ini3=strpos($pac,"-",$ini2+1);
		$n1=substr($pac,$ini2+1,$ini3-$ini2-1);
		$ini4=strpos($pac,"-",$ini3+1);
		$n2=substr($pac,$ini3+1,$ini4-$ini3-1);
		$id=substr($pac,$ini4+1,strlen($pac));
		$pacn=$n1." ".$n2." ".$ap1." ".$ap2;
		echo "<table border=0 align=center><tr><td><font size='4' color='#888888' face='arial'>PERCENTILES DE CRECIMIENTO: ".$nombre."<tr><td><font size='4' color='#888888' face='arial'>".strtoupper($pacn);
		/*query a la historia clinica*/
		$query="select Sexo,Fecha_nacimiento from pediatra_000001 where Nombre1='".$n1."' and Nombre2='".$n2."'  and ";
		$query=$query."Apellido1='".$ap1."' and Apellido2='".$ap2."' and Identificacion='".$id."' and Pediatra='".$medico."' ";
		//echo $query;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0) 
		{
				$row = mysql_fetch_row($err);
				$sexo=substr($row[0],3,1);
				$fecha1=$row[1];
		}
		
		$da=substr($fecha1,0,4);
		$dm=substr($fecha1,5,2);
		$dd=substr($fecha1,8,2);
		
		
		$query = "select max(P97) from root_000005 where ".$entre." and tabla='".$tabla."' and sexo='".$sexo."' order bY Meses";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0) 
		{
			$row = mysql_fetch_row($err);
			//echo "y97= ".$row[0]."<br>";
			$row[0]=($row[0]-$yd)*$ey;
			$height=($row[0])+60+($ys * $ey); 
			//$height =($y97[$num1 -1])+60+($ys * $ey); 
		
			$im = ImageCreateFromPng('c:\inetpub\wwwroot\MATRIX\images\medical\pediatra\\'.$ruta.'_'.$sexo.'.png');
			$bck = ImageColorAllocate($im, 200,200,200);
			$gris = ImageColorAllocate($im, 225,225,225);
			$blanco = ImageColorAllocate($im, 255, 255, 255); 
			$amarillo = ImageColorAllocate($im, 255, 237, 13);
			$negro = ImageColorAllocate($im, 0, 0, 0);
			//echo "height= ".$height."<br>";
	
			$i=0;
			$query = "select ".$buscar." from pediatra_000002 where  Paciente='".$pac."' and  Pediatra='".$medico."'  order by Fecha";
			//echo $query;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_row($err);
					//echo $row[0]."-".$row[1]."-".$row[2];
					if(($row[1] != 0 and (!isset($row[2]) or $row[2] != 0) and $buscar != "Talla,Peso_kilos") or ($buscar == "Talla,Peso_kilos" and $row[0] != 0 and $row[1] != 0))
					{
						
						if(is_int(strpos($buscar,"Fecha")))
						{
							$dias=((integer)$da - (integer)substr($row[0],0,4) );
							$ann=(integer)substr($row[0],0,4)*360 +(integer)substr($row[0],5,2)*30 + (integer)substr($row[0],8,2);
							$aa=(integer)$da*360 +(integer)$dm*30 + (integer)$dd;
							$edad=((($ann - $aa)/30)-$xd )*$ex;// Esta es la edad en meses
//							echo $fecha1."-".$row[0]." // ".$aa."-".$ann."=".(($valorx[$i]/$ex)+$xd)."  //".$buscar."=".(($valory[$i]/$ey)+$yd)."<br>";
							if($edad>=24 or ($nombre != "Talla por edad 2 a 20 Años" and $nombre != "Indice de Masa Corporal (IMC) por Edad de 2 a 20 años"))
							{
								$i++;
								$valorx[$i]=$edad;
								if(isset($row[2]))
								{
									$valory[$i]=$row[1]/pow(($row[2] * 0.01),2);
									//echo "<br> valory= ".$valory[$i];
									$valory[$i]=($valory[$i] - $yd)*$ey;
								}
								else
									$valory[$i]=($row[1]-$yd)*$ey;
							}
							
						}
						else
						{
							
							$i++;
							$valorx[$i]=($row[0]-$xd)*$ex;
							if(isset($row[2]))
							{									
								$valory[$i]=$row[1]/pow(($row[2] * 0.01),2);
								//echo "<br> valory= ".$valory[$i];
								$valory[$i]=($valory[$i] - $yd)*$ey;
							}
							else
							{
								$valory[$i]=($row[1]-$yd)*$ey;
							
							}
						}
						if($i > 1)
						{
							imageline($im,$valorx[$i-1]+71,$height -$valory[$i-1]-49,$valorx[$i]+71,$height -$valory[$i]-49,$negro);
							imageline($im,$valorx[$i-1]+70,$height -$valory[$i-1]-50,$valorx[$i]+70,$height -$valory[$i]-50,$amarillo);
							imageline($im,$valorx[$i-1]+69,$height -$valory[$i-1]-51,$valorx[$i]+69,$height -$valory[$i]-51,$negro);
							imagefilledrectangle($im,$valorx[$i-1] +69,$height-$valory[$i-1]-51,$valorx[$i-1]+71,$height - $valory[$i-1] -49,$amarillo);
							imagerectangle($im,$valorx[$i-1] +68,$height-$valory[$i-1]-52,$valorx[$i-1]+72,$height - $valory[$i-1] -48,$negro);
						}
						
						if ($num == 1)
						{
						
							imagefilledrectangle($im,$valorx[$i] +69,$height-$valory[$i]-51,$valorx[$i]+71,$height - $valory[$i] -49,$amarillo);
							imagerectangle($im,$valorx[$i] +68,$height-$valory[$i]-52,$valorx[$i]+72,$height - $valory[$i] -48,$negro);
						}
						
					}
					
				}
				if($i > 1)
				{
				imagefilledrectangle($im,$valorx[$i] +69,$height-$valory[$i]-51,$valorx[$i]+71,$height - $valory[$i] -49,$amarillo);
				imagerectangle($im,$valorx[$i] +68,$height-$valory[$i]-52,$valorx[$i]+72,$height - $valory[$i] -48,$negro);
				}
			}

		ImagePng ($im,"c:\inetpub\wwwroot\MATRIX\images\medical\pediatra\laimagen.png");
		echo "<tr><td><center><IMG SRC='\MATRIX\images\medical\pediatra\laimagen.png'></center></td></tr></table>"; 
	}
}
}
?>
</body>
</html> 