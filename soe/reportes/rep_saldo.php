<html>

<head>
  <title>PRESUPUESTO SALDO ACTUAL</title>
</head>
<body BGCOLOR="">
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /**************************************************
	*	REPORTE DEL SALDO ACTUAL 					*
	*  PARA LOS PACIENTES DE LA  UNIDAD ODONTOLOGICA*
	*************************************************/
//==================================================================================================================================
//PROGRAMA						:Reporte del saldo actual
//AUTOR							:Juan David Londoño
//FECHA CREACION				:MAYO 2006
//FECHA ULTIMA ACTUALIZACION 	:15 de Mayo de 2006
//DESCRIPCION					:
//
//==================================================================================================================================

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$empresa='facsoe';
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
			$query = "SELECT Subcodigo,Descripcion 
					  FROM `det_selecciones` 
					  WHERE medico = 'soe1' 
					  AND codigo = '008'  
					  ORDER by Subcodigo ";
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
		
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/
			
			$query="SELECT distinct Identificacion 
					FROM soe1_000008 
					WHERE Identificacion like '%".$pac1."%' 
					AND Odontologo='$medico' 
					ORDER by Identificacion";
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
			}	
		echo "</select></td></tr>";
		echo "</td></tr><input type='hidden' name='pac1' value='".$pac1."'>";
		}	
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
			$query = "SELECT distinct Codigo  
					  FROM soe1_000008  
					  WHERE Identificacion='".$pac."' 
					  AND Odontologo='$medico' 
					  ORDER by Codigo";
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
		
		//echo $medico;
		$query="SELECT fecha, Valor_presupuesto 
				FROM soe1_000012 
				WHERE Identificacion like '%".$pac."%' 
				AND Odontologo='$medico' 
				AND Codigo='".$codigo."'";
			$err = mysql_query($query,$conex);
			$fec = mysql_fetch_row($err);

		if ($fec[0]=='')
		echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF3300 LOOP=-1><font size=4 color='#FFFFFF'><b>DEBE DILIGENCIAR EL FORMULARIO DE ACEPTACION DE PRESUPUESTO</MARQUEE></FONT>";	
		echo "<br>";
		echo "<br>";
		echo "<br>";		
		
			
		echo "<table align=center border=1 width=725 ><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='\MATRIX\images\medical\SOE\SOE1.JPG' width='242' height='133'></td>";
		echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=3 color='#000080' face='arial'><B>TORRE MEDICA</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$nomed."";
		echo "<br> ODONTOLOGO<BR>";
		echo "</tr></table>";
		/*TABLA DEL REPORTE DEL SALDO*/
		echo "<table align=center border=1 width=725 BGcolor='#C0C0C0'>";
		echo "<tr><td nowrap><font size=3 face='arial' ><B>PACIENTE: </b>".$exp[1]." ".$exp[2]." ".$exp[3]." ".$exp[4]."</td>"; 
		echo "<td><font size=3  face='arial'><b>ODONTOLOGO:</b> ".$nomed."</td></tr>";
		echo "<tr><td><font size=3  face='arial'><b>FECHA:</b> ".$fec[0]." </td>";
		echo "<td><font size=3  face='arial'><b>CODIGO:</b> ".$codigo." </td></tr>";
		echo "<tr BGcolor='#99CCFF'><td align=center colspan=1><font size=3 face='arial' ><b>VALOR TOTAL DEL PRESUPUESTO</b></td>"; 
		echo "<td align=right><font size=3 face='arial' ><B>$".number_format($fec[1],0,'',".")."</b></td></tr>";
		echo "</table>";
		echo "<br>";
		
		
		$fecact=date("Y-m-d");
		
		$query = "SELECT Fenfac, Fenval, Fenfec, Fdefac, Vmpmed, Vmpvta   
				  FROM ".$empresa."_000018, ".$empresa."_000019 , ".$empresa."_000050  
				  WHERE (Fenfec between '".$fec[0]."'  and '$fecact') 
				  AND Fennit='".$exp[0]."'
				  AND Fdefac=Fenfac
				  AND Vmpvta=Fdenve
				  AND Vmpmed='".$medico."'
				  ";
		$err = mysql_query($query,$conex);
		//echo mysql_errno() ."=". mysql_error();
		$tot = mysql_num_rows($err);
		//echo $query;
		echo "<table align=center border=1 width=725  BGcolor='#C0C0C0'>";
		echo "<tr><td align=center ><b>FECHA DEL ABONO</td><td align=center ><b>VALOR DEL ABONO</td></tr>";
		if ($tot>0)
		 	{
			 $totpre=0;
			 	for ($i=0;$i<$tot;$i++)
			 			{
			 				if (is_int ($i/2))
							$wcf="CCFFFF";
							else
							$wcf="DDDDDD";
							
				 			$row=mysql_fetch_row($err);
				 			$totpre=$totpre+$row[1];
				 			echo "<tr><td align=center bgcolor=".$wcf."><font size=3 face='arial' >".$row[2]."</td>";
				 			echo "<td align=right bgcolor=".$wcf." ><font size=3 face='arial' >$".number_format($row[1],0,'',".")."</td></tr>";			    
				 			}
				 			
				 			echo "<tr BGcolor='#99CCFF'><td align=center colspan=1><font size=3  face='arial'><b>VALOR TOTAL ABONADO</td>";		
						    echo "<td align=right colspan=2><font size=3  face='arial'><b>$".number_format($totpre,0,'',".")."</b></td>";
						    echo "</tr>";	 				
    		}
							
		echo "</table>";
		echo "<br>";
		
		if(!isset ($totpre))
		$totpre=0;
		
		
		$salact=$fec[1]-$totpre;
		
		echo "<table align=center border=1 width=725 BGcolor='#C0C0C0'>";
		echo "<tr BGcolor='#99CCFF'><td align=center><b>SALDO ACTUAL</td><td align=right><b>$".number_format($salact,0,'',".")."</td></tr>";
		echo "</table>";
				
			} //si num y es mayor que cero es decir si hay historias
		}// el for que recorre las fechas del arreglo fec	
	// todos los datos estan set
	include_once("free.php");
?>