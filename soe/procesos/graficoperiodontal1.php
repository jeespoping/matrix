<html>

<head>
  <title>REGISTRO PERIODONTAL</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	if(!isset($medico)  or !isset($pac)  )
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
	
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else

	{
		$diente[0]=48;
		$diente[1]=47;
		$diente[2]=46;
		$diente[3]=45;
		$diente[4]=44;
		$diente[5]=43;
		$diente[6]=42;
		$diente[7]=41;
		$diente[8]=31;
		$diente[9]=32;
		$diente[10]=33;
		$diente[11]=34;
		$diente[12]=35;
		$diente[13]=36;
		$diente[14]=37;
		$diente[15]=38;

		$exp=explode("-",$pac);
		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='01'";
		$err = mysql_query($query,$conex);
		
		$numenf = mysql_num_rows($err);
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf[$i]["Diente"]=$row["Num_diente"];
			$Enf[$i]["Arr"]=$row["Arriba"];
			$Enf[$i]["Ab"]=$row["Abajo"];
			$Enf[$i]["Izq"]=$row["Izquierda"];
			$Enf[$i]["Der"]=$row["Derecha"];
		}
		
		for ($i=0; $i<count($diente);$i++)
		{
			$j=0 ;
			$ok=false;
			
			if(isset($Enf))
			{
			while ($j<count($Enf) and $ok == false )
			{
				if ($Enf[$j]["Diente"]==$diente[$i])
				{
					$placa["Placa"][$diente[$i]]['Ar']=$Enf[$j]["Arr"];
					$placa["Placa"][$diente[$i]]['Ab']=$Enf[$j]["Ab"];
					$placa["Placa"][$diente[$i]]['Iz']=$Enf[$j]["Izq"];
					$placa["Placa"][$diente[$i]]['De']=$Enf[$j]["Der"];
					$ok=true;
				}
				$j++;
			}
			}else{$Enf=0;
			}
			if ($ok==false){
				$placa["Placa"][$diente[$i]]['Ar']=" ";
				$placa["Placa"][$diente[$i]]['Ab']=" ";
				$placa["Placa"][$diente[$i]]['Iz']=" ";
				$placa["Placa"][$diente[$i]]['De']=" ";
				
			}

		}

 		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and  MID(Examen,1,2)='02'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf1[$i]["Diente"]=$row["Num_diente"];
			$Enf1[$i]["Arr"]=$row["Arriba"];
			$Enf1[$i]["Ab"]=$row["Abajo"];
			$Enf1[$i]["Izq"]=$row["Izquierda"];
			$Enf1[$i]["Der"]=$row["Derecha"];
		}
		
	
		
		for ($i=0; $i<count($diente);$i++)
		{
			$j=0 ;
			$ok=false;
			
			if(isset($Enf1))
			{
			while ($j<count($Enf1) and $ok == false )
			{
				if ($Enf1[$j]["Diente"]==$diente[$i])
				{
					$sangrado["Sangrado"][$diente[$i]]['Ar']=$Enf1[$j]["Arr"];
					$sangrado["Sangrado"][$diente[$i]]['Ab']=$Enf1[$j]["Ab"];
					$sangrado["Sangrado"][$diente[$i]]['Iz']=$Enf1[$j]["Izq"];
					$sangrado["Sangrado"][$diente[$i]]['De']=$Enf1[$j]["Der"];
					$ok=true;
				}
				$j++;
			}
			}else{$Enf1=0;
			}
			if ($ok==false){
				$sangrado["Sangrado"][$diente[$i]]['Ar']=" ";
				$sangrado["Sangrado"][$diente[$i]]['Ab']=" ";
				$sangrado["Sangrado"][$diente[$i]]['Iz']=" ";
				$sangrado["Sangrado"][$diente[$i]]['De']=" ";
				
			}

		}

		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='03'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf2[$i]["Diente"]=$row["Num_diente"];
			$Enf2[$i]["Arr"]=$row["Arriba"];
			$Enf2[$i]["Ab"]=$row["Abajo"];
			$Enf2[$i]["Izq"]=$row["Izquierda"];
			$Enf2[$i]["Der"]=$row["Derecha"];
		}
	
		for ($i=0; $i<count($diente);$i++)
		{
			
			$j=0 ;
			$ok=false;
			
			if(isset($Enf2))
			{
				while ($j<count($Enf2) and $ok == false )
				{
					if ($Enf2[$j]["Diente"]==$diente[$i])
					{
					$profundidad["Profundidad"][$diente[$i]]['Ar']=$Enf2[$j]["Arr"];
					$profundidad["Profundidad"][$diente[$i]]['Ab']=$Enf2[$j]["Ab"];
					$profundidad["Profundidad"][$diente[$i]]['Iz']=$Enf2[$j]["Izq"];
					$profundidad["Profundidad"][$diente[$i]]['De']=$Enf2[$j]["Der"];
					$ok=true;
					}
				$j++;
				}
			}else{$Enf2=0;
			}
			if ($ok==false){
				$profundidad["Profundidad"][$diente[$i]]['Ar']=" ";
				$profundidad["Profundidad"][$diente[$i]]['Ab']=" ";
				$profundidad["Profundidad"][$diente[$i]]['Iz']=" ";
				$profundidad["Profundidad"][$diente[$i]]['De']=" ";
				
			}

		}
	
		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='04'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf3[$i]["Diente"]=$row["Num_diente"];
			$Enf3[$i]["Arr"]=$row["Arriba"];
			$Enf3[$i]["Ab"]=$row["Abajo"];
			$Enf3[$i]["Izq"]=$row["Izquierda"];
			$Enf3[$i]["Der"]=$row["Derecha"];
		}
	
		for ($i=0; $i<count($diente);$i++)
		{
			
			$j=0 ;
			$ok=false;
			
			if(isset($Enf3))
			{
				while ($j<count($Enf3) and $ok == false )
				{
					if ($Enf3[$j]["Diente"]==$diente[$i])
					{
					$margen["Margen"][$diente[$i]]['Ar']=$Enf3[$j]["Arr"];
					$margen["Margen"][$diente[$i]]['Ab']=$Enf3[$j]["Ab"];
					$margen["Margen"][$diente[$i]]['Iz']=$Enf3[$j]["Izq"];
					$margen["Margen"][$diente[$i]]['De']=$Enf3[$j]["Der"];
					$ok=true;
					}
				$j++;
				}
			}else{$Enf3=0;
			}
			if ($ok==false){
				$margen["Margen"][$diente[$i]]['Ar']=" ";
				$margen["Margen"][$diente[$i]]['Ab']=" ";
				$margen["Margen"][$diente[$i]]['Iz']=" ";
				$margen["Margen"][$diente[$i]]['De']=" ";
				
			}

		}
		
		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and  MID(Examen,1,2)='05'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf4[$i]["Diente"]=$row["Num_diente"];
			$Enf4[$i]["Arr"]=$row["Arriba"];
		}
	
		for ($i=0; $i<count($diente);$i++)
		{
			
			$j=0 ;
			$ok=false;
			
			if(isset($Enf4))
			{
				while ($j<count($Enf4) and $ok == false )
				{
					if ($Enf4[$j]["Diente"]==$diente[$i])
					{
					$movilidad["Movilidad"][$diente[$i]]['Ar']=$Enf4[$j]["Arr"];
					$ok=true;
					}
				$j++;
				}
			}else{$Enf4=0;
			}
			if ($ok==false){
				$movilidad["Movilidad"][$diente[$i]]['Ar']=" ";
			}

		}
		
		$query = "SELECT * FROM soe1_000005 WHERE  Identificacion ='".$exp[0]."'  and MID(Examen,1,2)='06'";
		$err = mysql_query($query,$conex);
		$numenf = mysql_num_rows($err);
		
		for ($i=0;$i<$numenf;$i++)
		{
			$row = mysql_fetch_array($err);
			$Enf5[$i]["Diente"]=$row["Num_diente"];
			$Enf5[$i]["Arr"]=$row["Arriba"];
		}
	
		for ($i=0; $i<count($diente);$i++)
		{
			
			$j=0 ;
			$ok=false;
			
			if(isset($Enf5))
			{
				while ($j<count($Enf5) and $ok == false )
				{
					if ($Enf5[$j]["Diente"]==$diente[$i])
					{
					$bifurcacion["Bifurcacion"][$diente[$i]]['Ar']=$Enf5[$j]["Arr"];
					$ok=true;
					}
				$j++;
				}
			}else{$Enf5=0;
			}
			if ($ok==false){
				$bifurcacion["Bifurcacion"][$diente[$i]]['Ar']=" ";
			}

		}
		
		echo "<form action='' method='post'>";
		echo "<table align=center width=950 table border=1>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '9' align=center><font size=3 face='arial' ><B>FICHADO PERIODONTAL</b></td><td  bgcolor='#99CCFF' colspan= '8' align=center><font size=3 face='arial' ><B>PACIENTE:</b> ".$exp[1]." ".$exp[2]." ".$exp[3]." ".$exp[4]."</td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>EXAMEN INICIAL</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>48</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>47</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>46</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>45</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>44</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>43</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>42</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>41</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>31</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>32</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>33</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>34</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>35</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>36</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>37</b></td><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>38</b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>PLACA</b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['48']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['48']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['48']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['48']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['47']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['47']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['47']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['47']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['46']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['46']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['46']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['46']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['45']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['45']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['45']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['45']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['44']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['44']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['44']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['44']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['43']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['43']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['43']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['43']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['42']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['42']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['42']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['42']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['41']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['41']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['41']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['41']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['31']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['31']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['31']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['31']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['32']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['32']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['32']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['32']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['33']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['33']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['33']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['33']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['34']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['34']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['34']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['34']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['35']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['35']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['35']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['35']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['36']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['36']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['36']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['36']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['37']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['37']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['37']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['37']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$placa['Placa']['38']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$placa['Placa']['38']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['38']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$placa['Placa']['38']['Ab']."</td><td></td></tr></table></b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>SANGRADO</b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['48']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['48']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['48']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['48']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['47']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['47']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['47']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['47']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['46']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['46']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['46']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['46']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['45']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['45']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['45']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['45']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['44']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['44']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['44']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['44']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['43']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['43']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['43']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['43']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['42']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['42']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['42']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['42']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['41']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['41']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['41']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['41']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['31']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['31']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['31']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['31']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['32']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['32']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['32']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['32']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['33']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['33']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['33']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['33']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['34']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['34']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['34']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['34']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['35']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['35']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['35']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['35']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['36']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['36']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['36']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['36']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['37']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['37']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['37']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['37']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['38']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['38']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['38']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$sangrado['Sangrado']['38']['Ab']."</td><td></td></tr></table></b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>PS</b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['48']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['48']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['48']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['48']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['47']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['47']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['47']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['47']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['46']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['46']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['46']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['46']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['45']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['45']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['45']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['45']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['44']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['44']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['44']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['44']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['43']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['43']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['43']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['43']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['42']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['42']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['42']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['42']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['41']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['41']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['41']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['41']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['31']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['31']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['31']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['31']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['32']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['32']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['32']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['32']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['33']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['33']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['33']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['33']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['34']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['34']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['34']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['34']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['35']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['35']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['35']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['35']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['36']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['36']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['36']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['36']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['37']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['37']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['37']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['37']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['38']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['38']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['38']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$profundidad['Profundidad']['38']['Ab']."</td><td></td></tr></table></b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>M-UCA</b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['48']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['48']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['48']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['48']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['47']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['47']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['47']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['47']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['46']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['46']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['46']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['46']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['45']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['45']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['45']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['45']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['44']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['44']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['44']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['44']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['43']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['43']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['43']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['43']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['42']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['42']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['42']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['42']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['41']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['41']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['41']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['41']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['31']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['31']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['31']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['31']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['32']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['32']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['32']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['32']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['33']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['33']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['33']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['33']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['34']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['34']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['34']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['34']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['35']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['35']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['35']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['35']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['36']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['36']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['36']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['36']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['37']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['37']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['37']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['37']['Ab']."</td><td></td></tr></table></b></td><td  bgcolor='#99FFFF' colspan= '1' align=center><font size=3 face='arial' ><B><table align=center  table border=0><tr><td ></td><td bgcolor='#FFFFFF'>".$margen['Margen']['38']['Ar']."</td><td></td></tr><tr><td bgcolor='#FFFFFF'>".$margen['Margen']['38']['Iz']."</td><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['38']['De']."</td></tr><tr><td></td><td bgcolor='#FFFFFF'>".$margen['Margen']['38']['Ab']."</td><td></td></tr></table></b></td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>MOVILIDAD</b></td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['48']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['47']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['46']['Ar']."&nbsp &nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['45']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['44']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['43']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['42']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['41']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['31']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['32']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['33']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['34']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['35']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['36']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['37']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$movilidad['Movilidad']['38']['Ar']."&nbsp</td></tr>";
		echo "<tr><td  bgcolor='#99CCFF' colspan= '1' align=center><font size=3 face='arial' ><B>BIFURCACION</b></td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['48']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['47']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['46']['Ar']."&nbsp &nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['45']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['44']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['43']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['42']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['41']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['31']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['32']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['33']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['34']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['35']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['36']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['37']['Ar']."&nbsp</td><td  bgcolor='#FFFFFF' colspan= '1' align=center><font size=3 face='arial' >".$bifurcacion['Bifurcacion']['38']['Ar']."&nbsp</td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<table align=center width=950 table border=0>";
		$hyper="<A HREF='/matrix/soe/procesos/graficoperiodontal.php?medico=".$medico."&amp;pac=".$pac."'>Dientes 18 a 28</a>";	
		echo "<tr><td  colspan= '4' align=center><font size=3 face='arial' >$hyper</td></tr>";
		echo "</table>";
		echo "</form>";
	}
}
include_once("free.php");
?>