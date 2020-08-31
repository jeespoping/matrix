<html>

<head>
  <title>PRESUPUESTO</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /**********************************************
	*	REPORTE DE PRESUPUESTO 						 *
	*  PARA ODONTOLOGOS DE LA  UNIDAD ODONTOLOGICA   *
	**********************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente,  medico y coigo )
	if(!isset($medico)  or !isset($pac) or !isset($codigo)  )
	{
		echo "<form action='' method='post'>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b><font color=#000066>PROMOTORA MEDICA LAS AMERICAS </font></b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>UNIDAD ODONTOLOGICA</font></td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ODONTOLOGO: </font></td>";	
			/* Si el ODONTOLOGO no ha sido escogido Buscar a los odontologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'soe1' AND codigo = '008'  order by Subcodigo ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if (($row[0]."-".$row[1]) == $medico)
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}	// fin del if $num>0
	
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>PACIENTE: </font></td>";	
	
		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			//$query="select DISTINCT Identificacion from soe1_000004 where  Identificacion like '%".$pac1."%' order by Identificacion";V1.03
			$query="select DISTINCT Identificacion from soe1_000008 where Identificacion like '%".$pac1."%' and Odontologo='$medico' order by Identificacion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "</td><td bgcolor=#cccccc colspan=2><select name='pac'>";
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
		echo "</select></td></tr>";
		echo "</td></tr><input type='hidden' name='pac1' value='".$pac1."'>";
		}	//fin isset medico
		else 
		{
			echo "</td><td bgcolor=#cccccc colspan=2><input type='text' name='pac1'>";
			echo "</td></tr>";
		}
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CODIGO: </font></td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='codigo'>";

		if(isset($pac))
		{
			/*Si ya se tiene el paciente buscar las fechas de los seguimiento para este y
			 construir el drop down */	
			$query = "select DISTINCT Codigo  from soe1_000008  where Identificacion='".$pac."' and Odontologo='$medico' order by Codigo";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=1;$j<=$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					$fec[$j]=$row[0];
					echo "<option>".$row[0]."</option>";
				}
				
			}
			
		}
		//echo"</select>
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	/***************************************************************
		  APARTIR DE AQUI EMPIEZA LA IMPRESION DE LA HISTORIA		*/
	{
		$ini1=strpos($medico,"-");
		$nomed=substr($medico,$ini1+1);
		
		$exp=explode("-",$pac);
		
		$query="select Fecha from soe1_000008 where Identificacion like '%".$pac."%' and Odontologo='$medico' and Codigo='".$codigo."'";
			$err = mysql_query($query,$conex);
			$fec = mysql_fetch_row($err);
				
		
		echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='\MATRIX\images\medical\SOE\SOE1.JPG' width='242' height='133'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		echo "<br> ODONTOLOGO<BR>";
		echo "</tr></table>";
		/*DATOS GENERALES ENCONTRADOS EN LA TABLA PACIENTE*/
		echo "<table align=center border=1 width=725 BGcolor='#99FFFF'>";
		echo "<tr><td nowrap><font size=3 face='arial' ><B>PACIENTE: </b>".$exp[1]." ".$exp[2]." ".$exp[3]." ".$exp[4]."</td>"; 
		echo "<td><font size=3  face='arial'><b>ODONTOLOGO:</b> ".$nomed."</td></tr>";
		echo "<tr><td><font size=3  face='arial'><b>FECHA:</b> ".$fec[0]." </td>";
		echo "<td><font size=3  face='arial'><b>CODIGO:</b> ".$codigo." </td></tr>";
		echo "</table>";
			
			
		echo "<table align=center border=1 width=725 >";
		echo "<tr BGcolor='#99CCFF'><td align=center colspan=2><font size=3 face='arial' ><B>PROCEDIMIENTO</b></td>"; 
		echo "<td align=center><font size=3 face='arial' ><B>CANTIDAD</b></td>"; 
		echo "<td align=center><font size=3 face='arial' ><B>VALOR UNITARIO</b></td>"; 
		echo "<td align=center><font size=3  face='arial'><b>VALOR TOTAL</b></td></tr>";
		
		 $query = "select Procedimiento, Cantidad, Valor_unitario, Valor_total, id  from soe1_000008  where Codigo='$codigo' and Identificacion like '%$pac%' and Odontologo='$medico'";
		$err = mysql_query($query,$conex);
		$tot = mysql_num_rows($err);
		
		if ($tot>0)
		 	{
			 $totpre=0;
			 	for ($i=0;$i<$tot;$i++)
			 			{
				 			$row=mysql_fetch_row($err);
				 			$totpre=$totpre+$row[3];
				 			//$total=$row[0];
				 			echo "<tr>";
							$hyper1="<A HREF='/matrix/det_registro.php?id=".$row[4]."&pos1=soe1&pos2=2006-03-01&pos3=15:39:26&pos4=000008&pos5=0&pos6=soe1&tipo=P&Valor=&Form=000008-soe1-C-Presupuesto&call=2&change=0&key=soe1&Pagina=1'>Editar</a>";
							echo "<td align=left colspan=1><font size=3  face='arial'>".$hyper1."</td>";
				 			echo "<td align=left colspan=1><font size=3  face='arial'>".substr($row[0],5)."</td>";
						    echo "<td align=center colspan=1><font size=3  face='arial'>".$row[1]."</td>";
						    echo "<td align=right colspan=1><font size=3  face='arial'>$".number_format($row[2],0,'',".")."</td>";
						    echo "<td align=right colspan=1><font size=3  face='arial'>$".number_format($row[3],0,'',".")."</td>";
						    echo "</tr>";			    
				 			}
				 			
				 			echo "<td align=center colspan=3><font size=3  face='arial'><b>VALOR TOTAL DEL PRESUPUESTO</td>";		
						    echo "<td align=right colspan=2><font size=3  face='arial'><b>$".number_format($totpre,0,'',".")."</b></td>";
						    echo "</tr>";	 				
    		}
		
		
								
		echo "</table>";
				
			} //si num y es mayor que cero es decir si hay historias
		}// el for que recorre las fechas del arreglo fec	
	// todos los datos estan set
	include_once("free.php");
?>