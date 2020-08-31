<html>

<head>
  <title>LECTURA MEDICINA NUCLEAR</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
   /*****************************************************
	* 		REPORTE DE LECTURAS DE MEDICINA NUCLEAR     *
	*			PARA MEDICOS EXTERNOS E INTERNOS		*
	*					CONEX, FREE => OK				*
	*****************************************************/
   session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	

	if($key == "nuclear" )
	{
		
		/* SI ES UN USUARIO AUTORIZADO*/
		if( !isset($pac) or !isset($estudio) or !isset($fecha))
		{
			echo "<form action='000003_nu03.php' method=post>";
			echo "<center><table border=0 width=400>";
			echo "<tr><td align=center colspan=3><font color=#000066><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
			echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</td></tr>";
			echo "<tr></tr>";
			echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </td>";	
					
			
			if(isset($pac) )
			{
				/* Si el paciente no esta set construir el drop down */
				echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
				/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
				$query="select distinct Paciente from nuclear_000003 where  Paciente like '%".trim($pac)."%' order by Paciente";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						if($row[0]==$pac)
							echo "<option selected>".$row[0]."</option>";
						else
							echo "<option>".$row[0]."</option>";
					}
				}	// fin $num>0
				echo "</select>";
			}	//fin isset pac
			else
				echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac'>";
			
			echo "</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>ESTUDIO: </td>";	
			echo "</td><td bgcolor=#cccccc colspan=2><select name='estudio'>";
	
			if(isset($pac))
			{
				/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
				 construir el drop down */	
				$query = "select distinct(Estudio)  from nuclear_000003 where Paciente='".$pac."'  ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<=$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						if($row[0]==$estudio and $estudio != "")
							echo "<option selected>".$row[0]."</option>";
						else
							echo "<option>".$row[0]."</option>";
					}
				}
			}
			echo "</select></td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </td>";	
			echo "</td><td bgcolor=#cccccc colspan=2><select name='fecha'>";
			if(isset($estudio))
			{
				/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
				 construir el drop down */	
				$query = "select Fecha  from nuclear_000003 where Paciente='".$pac."' and Estudio='".$estudio."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<=$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."</option>";
					}
				}
			}
			echo"</select></td></tr>";
			echo "<input type='hidden' name='key' value='".$key."'>";
			echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
		}// if de los parametros no estan set
		else
		{
			$query = "select *  from nuclear_000003 where Paciente='".$pac."' and Estudio='".$estudio."' and Fecha='".$fecha."' ";//
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
				$row = mysql_fetch_row($err);
			$medico=	$row[11];
			$inio=strpos($medico,"-");
			echo "<table align=center border=1 width=500><tr><td rowspan=3 align='center' colspan='0'>";
			echo "<img SRC='/MATRIX/images/medical/root/americas10.jpg'  size=150 width=128></td>";
			echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
			echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>MEDICINA NUCLEAR</b></font>";
			echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".substr($medico,$inio+1)."";
			echo "<br>MEDICO NUCLEAR<BR>Reg.:</b>".substr($medico,0,$inio)."</b></font>";
			echo "</tr></table>";
			$ini1=strpos($pac,"-");
			$n1=substr($pac,0,$ini1);
			$ini2=strpos($pac,"-",$ini1+1);
			$n2=substr($pac,$ini1+1,$ini2-$ini1-1);
			$ini3=strpos($pac,"-",$ini2+1);
			$ap1=substr($pac,$ini2+1,$ini3-$ini2-1);
			$ini4=strpos($pac,"-",$ini3+1);
			$ap2=substr($pac,$ini3+1,$ini4-$ini3-1);
			$ini5=strpos($pac,"-",$ini4+1);
			$id=substr($pac,$ini4+1,strlen($pac)-$ini4-1);
			$ini1=strpos($medico,"-");
		
				
			echo "<table align=center border=1 width=500><td colspan=2><font size=2 face='arial'>PACIENTE: <font size=3 face='arial'>".$n1."  ".$n2." ".$ap1." ".$ap2." </td>"; 
			echo "<td><font size=2  face='arial'>DOCUMENTO: ".$id."</td></tr>";
			echo "<tr><td><font size=2  face='arial'>EDAD: ".$row[4]."</td>";
			echo "<td><font size=2  face='arial'>SEXO: ".$row[5]."</td>";
			echo "<td><font size=2  face='arial'>ENTIDAD: ".substr($row[7],3)."</td></tr>";
			echo "<td colspan=2><font size=2 face='arial'>FECHA: <font size=2 face='arial'>".$row[10]." </td>"; 
			echo "<td><font size=2  face='arial'>INGRESO: ".$row[9]."</td></tr>";
			echo "<tr><td colspan=3><font size=3  face='arial'><B>ESTUDIO: ".substr($estudio,3)."</b></td></tr>";
			echo "<tr><td colspan=3><font size=3  face='arial'>".$row[14]."</td></tr>";
			echo "<tr><td colspan=3><font size=3  face='arial'><b>OPINION: ".strtoupper($row[15])."</b></td></tr>";
			for($l =16;$l <= 19;$l++)
				if($row[$l] != "NO APLICA")
				{
					echo "<tr><td colspan=3><font size=2  face='arial'><b>IMAGEN ".($l-15).":  </b><A HREF='000003_nu04.php?Graph=".$row[$l]."' target = '_blank'>Ver imagen - ";
					echo "<A HREF='000003_nu05.php?imagen=".$row[$l]."&amp; user=".substr($row[19],2)."' target = '_blank'>Guardar Imagen</a></td></tr>";
				}
			echo "</table>";
			//echo "<center><input type='submit' name='IMPRESION' value='IMPRESION'>";
			//for ($l=0;$l<=20;$l ++)
				//echo "row[".$l."]=".$row[$l]."<br>";
			echo "<form action='000003_nu01.php' method=post>";
			echo "<input type='hidden' name='medico' value='".substr($medico,$inio+1)."'>";
			echo "<input type='hidden' name='fecha' value='".$row[10]."'>";
			echo "<input type='hidden' name='ingreso' value='".$row[9]."'>";
			echo "<input type='hidden' name='entidad' value='".substr($row[7],3)."'>";
			echo "<input type='hidden' name='paciente' value='".$n1."  ".$n2." ".$ap1." ".$ap2."'>";
			echo "<input type='hidden' name='id' value='".$id."'>";
			echo "<input type='hidden' name='estudio' value='".substr($estudio,3)."'>";
			echo "<input type='hidden' name='plantilla' value='".$row[14]."'>";
			echo "<input type='hidden' name='opinion' value='".strtoupper($row[15])."'>";
			echo "<center><input type='submit' name='IMPRESION' value='IMPRESION'>";	
		}
	}
	else
	{
		/*SI NO ES UN USUARIO AUTORIZADO*/
		if( !isset($pac) or !isset($estudio) or !isset($fecha))
		{
			echo "<form action='000003_nu03.php' method=post>";
			echo "<center><table border=0 width=400>";
			echo "<tr><td align=center colspan=3><font color=#000066><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
			echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</td></tr>";
			echo "<tr></tr>";
			if(isset($doc) )
			{
				/* Si YA HA SIDO INGRESADO EL DOCUMENTO DEL PACIENTE */
				echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </td>";	
				echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
				$query="select * from nuclear_000001 where  Documento='".$doc."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					/* Si el medico  ya esta set traer los pacientes los cuales  TIENEN LECTURA*/
					$query="select distinct Paciente from nuclear_000003 where  Paciente like '%".trim($doc)."%' order by Paciente";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num>0)
					{
						for ($j=0;$j<$num;$j++)
						{	
							$row = mysql_fetch_array($err);
							if($row[0]==$pac)
								echo "<option selected>".$row[0]."</option>";
							else
								echo "<option>".$row[0]."</option>";
						}
					}	// fin $num>0
					echo "</select>";
					echo "<input type='hidden' name='doc' value='".$doc."' >";
					
				}
				else
				{
					$WARNNING="<IMG align='center' SRC='/matrix/images/medical/root/cabeza.gif' >";
					$WARNNING=$WARNNING."<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>NO EXISTE UN PACIENTE CON ESE DOCUMENTO VUELVA A ATRAS !!!!</MARQUEE></FONT>";
				}
			}	//fin isset pac
			else
			{
				echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DOCUMENTO: </td>";	
				echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='doc'>";
			}
			
			echo "</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>ESTUDIO: </td>";	
			echo "</td><td bgcolor=#cccccc colspan=2><select name='estudio'>";
	
			if(isset($pac))
			{
				/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
				 construir el drop down */	
				$query = "select distinct Estudio  from nuclear_000003 where Paciente='".$pac."'  ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<=$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."</option>";
					}
				}
			}
			echo "</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </td>";	
			echo "</td><td bgcolor=#cccccc colspan=2><select name='fecha'>";
			if(isset($estudio))
			{
				/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
				 construir el drop down */	
				$query = "select Fecha  from nuclear_000003 where Paciente='".$pac."' and Estudio='".$estudio."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<=$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."</option>";
					}
				}
			}
			echo"</select></td></tr>";
			echo "<input type='hidden' name='key' value='".$key."'>";
			echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
			if(isset($WARNNING))
				echo $WARNNING;
		}// if de los parametros no estan set
		else
		{
			$query = "select *  from nuclear_000003 where Paciente='".$pac."' and Estudio='".$estudio."' and Fecha='".$fecha."' ";//
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
				$row = mysql_fetch_row($err);
			$medico=	$row[11];
			$ini1=strpos($medico,"-");
			echo "<table align=center border=1 width=500><tr><td rowspan=3 align='center' colspan='0'>";
			echo "<img SRC='/MATRIX/images/medical/root/americas10.jpg'  size=150 width=128></td>";
			echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
			echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>MEDICINA NUCLEAR</b></font>";
			echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".substr($medico,$ini1+1,strlen($medico))."";
			echo "<br>MEDICO NUCLEAR<BR>Reg.:</b>".substr($medico,0,$ini1)."</b></font>";
			echo "</tr></table>";
			$ini1=strpos($pac,"-");
			$n1=substr($pac,0,$ini1);
			$ini2=strpos($pac,"-",$ini1+1);
			$n2=substr($pac,$ini1+1,$ini2-$ini1-1);
			$ini3=strpos($pac,"-",$ini2+1);
			$ap1=substr($pac,$ini2+1,$ini3-$ini2-1);
			$ini4=strpos($pac,"-",$ini3+1);
			$ap2=substr($pac,$ini3+1,$ini4-$ini3-1);
			$ini5=strpos($pac,"-",$ini4+1);
			$id=substr($pac,$ini4+1,strlen($pac)-$ini4-1);
			$ini1=strpos($medico,"-");
		
				
			echo "<table align=center border=1 width=500><td colspan=2><font size=2 face='arial'>PACIENTE: <font size=3 face='arial'>".$n1."  ".$n2." ".$ap1." ".$ap2." </td>"; 
			echo "<td><font size=2  face='arial'>DOCUMENTO: ".$id."</td></tr>";
			echo "<tr><td><font size=2  face='arial'>EDAD: ".$row[4]."</td>";
			echo "<td><font size=2  face='arial'>SEXO: ".$row[5]."</td>";
			echo "<td><font size=2  face='arial'>ENTIDAD: ".substr($row[7],3)."</td></tr>";
			echo "<td colspan=2><font size=2 face='arial'>FECHA: <font size=2 face='arial'>".$row[10]." </td>"; 
			echo "<td><font size=2  face='arial'>INGRESO: ".$row[9]."</td></tr>";
			echo "<tr><td colspan=3><font size=3  face='arial'><B>ESTUDIO: ".substr($estudio,3)."</b></td></tr>";
			echo "<tr><td colspan=3><font size=3  face='arial'>".$row[14]."</td></tr>";
			echo "<tr><td colspan=3><font size=3  face='arial'><b>OPINION: ".strtoupper($row[15])."</b></td></tr>";
			for($l =16;$l <= 19;$l++)
				if($row[$l] != "NO APLICA")
				{
					echo "<tr><td colspan=3><font size=2  face='arial'><b>IMAGEN ".($l-15).":  </b><A HREF='000003_nu04.php?Graph=".$row[$l]."' target = '_blank'>Ver imagen - ";
					echo "<A HREF='000003_nu05.php?imagen=".$row[$l]."&amp; user=".substr($row[19],2)."' target = '_blank'>Guardar Imagen</a></td></tr>";
				}
			echo "</table>";
		}
	}
	include_once("free.php");

}
?>
</body>
</html>