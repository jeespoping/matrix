<html>

<head>
  <title>CITAS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066"><?php
include_once("conex.php");
   /***************************************************
	*	REPORTE DE CITAS SEGUN FORMULARIO DE CITAS	  *
	*				CONEX, FREE => OK				  *
	***************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

		// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
		
		
	$query= "select grupo from usuarios where codigo='".$key."'";
//	echo "query encontrar grupo por codigo".$query."<br>";
	$err = mysql_query($query,$conex);
	$grupo = mysql_fetch_row($err);
	$query= "select codigo from usuarios where grupo='".$grupo[0]."'and activo='A'";
//	echo "query encontrar codigo por grupo".$query."<br>";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
//	echo "num= ".$num."<br>";
	$c=0;
	while ($c<$num  and !isset($cod) )
	{
//		echo "entro al while <br>";
		$c++;
		$codigo = mysql_fetch_row($err);
		$query = "select medico from reportes where medico='".$codigo[0]."' and formulario='".$form."' and nombre like 'citas.php?%'";
//		echo "query de cada codigo= ".$query."<br>";
		$err1 = mysql_query($query,$conex);
		$numrep = mysql_num_rows($err1);
//		echo "numrep= ".$numrep."<br>";
		if ($numrep>0)
		{
			$doi = mysql_fetch_row($err1);
			$cod=$doi[0];
			$c = "ok";
		}
	}
			
	if(!isset($medico)  or !isset($hi) or !isset($year) or !isset($hf) or !isset($int) or $hi=='00' or $hf=='00' )
	{
		echo "<form action='citas.php' method=post>";
		//echo "<input type='hidden' name='cod' value='".$cod."'>";
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><font color=#000066><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3><font color=#000066>TORRE MÉDICA</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>MEDICO: </td>";	
			
			/* Si el medico no ha sido escogido Buscar a los oftalmologos de la seleccion para 
				construir el drop down*/
			echo "<td bgcolor=#cccccc colspan=2><select name='medico'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = '".$cod."' AND codigo = '".$sel_med."'  order by Descripcion ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					$med=$row[0]."-".$row[1];
					if ($med==$medico)
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}	// fin del if $num>0
		echo "</select></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>FECHA: </td>";	
		if (!isset($month))
			$month=date("m");
		if (!isset($day))
			$day=date("d");
		
		/* Crear los drop down de ayuda de fecha para elusuario */
		echo "</td><td bgcolor=#cccccc colspan=2>";//<input type='text' name='fecha'>";
		echo "<select name='year'>";
				for($f=2004;$f<2051;$f++)
				{
					if($f == $year)
						echo "<option selected>".$f."</option>";
					else
						echo "<option>".$f."</option>";
				}
				echo "</select><select name='month'>";
				for($f=1;$f<13;$f++)
				{
					if($f == $month)
						if($f < 10)
							echo "<option selected>0".$f."</option>";
						else
							echo "<option selected>".$f."</option>";
					else
						if($f < 10)
							echo "<option>0".$f."</option>";
						else
							echo "<option>".$f."</option>";
				}
				echo "</select><select name='day'>";
				for($f=1;$f<32;$f++)
				{
				if($f == $day)
					if($f < 10)
						echo "<option selected>0".$f."</option>";
					else
						echo "<option selected>".$f."</option>";
				else
					if($f < 10)
						echo "<option>0".$f."</option>";
					else
							echo "<option>".$f."</option>";
				}
				echo "</select></td></tr>";
				/* Crear los drop down de ayuda de hora de inicio para elusuario */
		echo"</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>HORA INICIO: </td>";	
		echo "</td><td bgcolor=#cccccc colspan=2>";
		echo "<select name='hi'>";
		for($p=0;$p<24;$p++)
		{
			if($p == $hi)
				if($p < 10)
					echo "<option selected>0".$p."</option>";
				else
					echo "<option selected>".$p."</option>";
			else
				if($p < 10)
					echo "<option>0".$p."</option>";
				else
					echo "<option>".$p."</option>";
		}
		echo "</select><select name='mi'>";
		for($p=0;$p<60;$p++)
		{
			if($p == $mi)
				if($p < 10)
					echo "<option selected>0".$p."</option>";
				else
					echo "<option selected>".$p."</option>";
			else
				if($p < 10)
					echo "<option>0".$p."</option>";
				else
					echo "<option>".$p."</option>";
		}
		echo "</select>";
				/* Crear los drop down de ayuda de hora limite para elusuario */
		echo"</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>HORA FIN: </td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='hf'>";
		for($p=0;$p<24;$p++)
		{
			if($p == $hf)
				if($p < 10)
					echo "<option selected>0".$p."</option>";
				else
					echo "<option selected>".$p."</option>";
			else
				if($p < 10)
					echo "<option>0".$p."</option>";
				else
					echo "<option>".$p."</option>";
		}
		echo "</select><select name='mf'>";
		for($p=0;$p<60;$p++)
		{
			if($p == $mf)
				if($p < 10)
					echo "<option selected>0".$p."</option>";
				else
					echo "<option selected>".$p."</option>";
			else
				if($p < 10)
					echo "<option>0".$p."</option>";
				else
					echo "<option>".$p."</option>";
		}
		echo "</select>";
		echo"</td></tr><tr><td bgcolor=#cccccc colspan=1><font color=#000066>INTERVALO (min): </td>";	
		echo "<td bgcolor=#cccccc colspan=2><select name='int'>";
		$query = "SELECT Subcodigo FROM `det_selecciones` WHERE medico = '".$cod."' AND codigo = '".$sel_dur."'  order by Subcodigo ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					if ($row[0]==$medico)
						echo "<option selected>".$row[0]."</option>";
					else
						echo "<option>".$row[0]."</option>";
				}
			}	// fin del if $num>0
		echo "</select></td></tr>";
		//ECHO $nom_med;
		echo "<input type='hidden' name='cod' value='".$cod."'>";
		echo "<input type='hidden' name='form' value='".$form."'>";
		echo "<input type='hidden' name='nom_med' value='".$nom_med."'>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}// if de los parametros no estan set
	else
	{
		/***********************************
			TODOS LOS PARAMETROS ESTAN SET		*/
		
		$Hi=$hi.":".$mi.":00";
		$Hf=$hf.":".$mf.":00";
		$query="select * from ".$cod."_".$form." where ".$nom_med."='".$medico."' and Fecha='".$year."-".$month."-".$day."'  and Hora_inicio>='".$hi.":".$mi.":00' and Hora_inicio<='".$hf.":".$mf.":00' order by Hora_inicio";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$hora=array();					//arreglo de todas las horas
		$data=array();					// arreglo de los datos asociados a las horas
		$hora_c=$Hi;
		$pos=-1;
		$cont=0;
		$row = mysql_fetch_row($err);
	//echo $query;
		/*Creación de el arreglo que contiene las horas de las citas y las horas segun la duración, y la hora inicial y la final*/
		while($hora_c <= $Hf)
		{
			if($row[6]<= $hora_c and $cont<$num)  	
			{
				/*Si la hora de inicio de la cita es menor que la hora de la grilla ,
				la cita va antes que el siguiente intervalo definido*/
				$hora[$pos+1]=$row[6];			// hora inicio de la cita
				$hora[$pos+2]=$row[8];		// hora fin de la cita
				$data[$pos+2][2]="";
				$data[$pos+2][1]="";
				$data[$pos+2][3]="";
				$data[$pos+2][4]="";
				$ini1=strpos($row[3],"-");
				$ini2=strpos($row[3],"-",$ini1+1);
				$ini3=strpos($row[3],"-",$ini2+1);
				$ini4=strpos($row[3],"-",$ini3+1);
				$ini5=strpos($row[3],"-",$ini4+1);
				$data[$pos+1][2]=substr($row[3],$ini5+1,strlen($row[3])-$ini5+1);	// telefono del paciente de la cita
				$data[$pos+1][1]=substr($row[3],0,$ini4);		//Nombre del paciente
				$data[$pos+1][3]=$row[9];			//Entidad
				$data[$pos+1][4]=$row[10];		// Motivo
				$pos+=2;
				$hc=(integer)substr($row[8],0,2);
				$mc=(integer)substr($row[8],3,2)+ $int;
				$row = mysql_fetch_row($err);
				$cont++;
			}
			else
			{
				/*No hay cita dentro del intervalo */
				$hora[$pos+1]=$hora_c;
				$data[$pos+1][2]="";
				$data[$pos+1][1]="";
				$data[$pos+1][3]="";
				$data[$pos+1][4]="";
				$pos++;
				$hc=(integer)substr($hora_c,0,2);
				$mc=(integer)substr($hora_c,3,2) + $int;
			}
			/*Control de horas minutos y segundos*/
			while($mc>=60)
			{
				$mc=$mc-60;
				$hc++;
				if($hc==24)
					$hc=0;
			}
			if($hc< 10)
				if($mc<10)
					$hora_c="0".$hc.":0".$mc.":00";
				else
					$hora_c="0".$hc.":".$mc.":00";
			else
				if($mc<10)
					$hora_c=$hc.":0".$mc.":00";
				else
					$hora_c=$hc.":".$mc.":00";
		}	
		$m=0;
		$ino=strpos($medico,"-");
		echo "<table  align=center border=0 ><tr><td align=center><font size=4 face='arial' color=336699><b>CLÍNICA MÉDICA LAS AMERICAS</b></font></tr>";
		echo"<tr><td align=center><font size=3 face='arial' color=336699><b>TORRE MÉDICA</b></font></td></tr>";
		echo "<tr><td align=center><font size=2 face='arial' color=336699><b>AGENDA : ".strtoupper(substr($medico,$ino+1))." M.D.</b></font></tr>";
		echo "<tr><td align=center><font size=2 face='arial' color=336699><b>FECHA: ".$year."-".$month."-".$day."</b></font></tr>";
		echo "</table><br><table align=center border=0 ><tr align='center'><b><td bgcolor=0099CC><font size=2 face='arial' color=ffffff><b>HORA</font></td>";
		echo "<td bgcolor=0099CC ><font size=2 face='arial' color=ffffff><b>DURACIÓN</td><td bgcolor=0099CC><font size=2 face='arial' color=ffffff><b>PACIENTE</td>";
		echo "<td bgcolor=0099CC><font size=2 face='arial' color=ffffff><b>TELEFONO</td><td bgcolor=0099CC><font size=2 face='arial' color=ffffff><b>ENTIDAD</td>";
		echo "<td bgcolor=0099CC><font size=2 face='arial' color=ffffff><b>MOTIVO</td></tr>";
		/*IMPRESION DE LDE LOS ARREGLOS DE HORAS Y DATOS*/
		for($k=1;$k<=$pos;$k++)
		{
			if(($m%2)==0)
				$color="99CCFF";
			else
				$color="";	
			$t1=(integer)substr($hora[$k-1],0,2);
			$t2=(integer)substr($hora[$k],0,2);
			if ($t1 > $t2)
			{
				$t1=60; $t2=0;
			}
			else if ($t2 > $t1)		
			{
				$t2=60; $t1=0;
			}
			$t1=$t1+(integer)substr($hora[$k-1],3,2);
			$t2=(integer)substr($hora[$k],3,2)+ $t2;
			$t2=abs($t2-$t1);
			if($t2 > 0)	
			{
				echo "<tr><td align= center bgcolor=".$color."><font size=2 face='arial'>".substr($hora[$k-1],0,5)." - ".substr($hora[$k],0,5)."</font></td>";
				echo "<td align=center bgcolor=".$color."><font size=2 face='arial'>".$t2." min. </font></td>";
				echo "<td bgcolor=".$color."><font size=2 face='arial'>".$data[$k-1][1]." &nbsp</td>";
				echo "<td bgcolor=".$color."><font size=2 face='arial'>".$data[$k-1][2]."  &nbsp</td>";
				echo "<td bgcolor=".$color."><font size=2 face='arial'>".$data[$k-1][3]." &nbsp</td>";
				echo "<td bgcolor=".$color."><font size=2 face='arial'>".$data[$k-1][4]." &nbsp</td>";	
				$m++;
			}
		}
		echo "</table>";	
	}
	include_once("free.php");
}
?>
</body>
</HTML>	