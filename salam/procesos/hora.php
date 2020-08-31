<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
	echo "<form action='hora.php' method=post>";
	$key = substr($user,2,strlen($user));
	

	

	if (isset($ok) and $ok=="on")
	{
		$query = "select tipo from salam_000001 where codigo_n='".substr($item1,0,2)."'";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_array($err);
		$wtip=substr($row[0],0,2);
		if(($wpar != "" and $wpar>= "0" and $wpar <= "200" and $wtip == "01") or ($wpar > "0" and $wtip == "02") or ($wpar == "1" and $wtip == "03") )
		{
			$query ="select *  from salam_000002  where hora_inicio='".$horai."' and fecha='".$fechad."' and paciente='".$paciente."' and hora='".$hora."' and parametro='".substr($item1,0,2)."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1>0)
			{
				$query = "update salam_000002 set valor='".$wpar."' where hora_inicio='".$horai."' and fecha='".$fechad."' and paciente='".$paciente."' and hora='".$hora."' and parametro='".substr($item1,0,2)."'";
				$err1 = mysql_query($query,$conex);
			}
			else
			{
				$fecha = date("Y-m-d");
				$hora1 = (string)date("H:i:s");
				$seguridad="C-salam";
				$query = "insert  into salam_000002";
				$query = $query." (medico,fecha_data,hora_data,anestesiologo,fecha,hora_inicio,paciente,hora,parametro,codigo,valor,seguridad)";
				$query = $query." values('".$key."','".$fecha."','".$hora1."','".$medico."','".$fechad."','".$horai."','".$paciente."','".$hora."','".substr($item1,0,2)."','0','".$wpar."','".$seguridad."')";
				#echo $query;
				$err1 = mysql_query($query,$conex);
			}
		}
		if($wliq != "")
		{
			$query ="select *  from salam_000002  where hora_inicio='".$horai."' and fecha='".$fechad."' and paciente='".$paciente."' and hora='".$hora."'  and parametro='99' and codigo='".$item2."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1>0)
			{
				$query = "update salam_000002 set valor='".$wliq."' where hora_inicio='".$horai."' and fecha='".$fechad."' and paciente='".$paciente."' and hora='".$hora."'   and parametro='99' and codigo='".$item2."'";
				$err1 = mysql_query($query,$conex);
			}
			else
			{
				$fecha = date("Y-m-d");
				$hora1 = (string)date("H:i:s");
				$seguridad="C-salam";
				$query = "insert  into salam_000002";
				$query = $query." (medico,fecha_data,hora_data,anestesiologo,fecha,hora_inicio,paciente,hora,parametro,codigo,valor,seguridad)";
				$query = $query." values('".$key."','".$fecha."','".$hora1."','".$medico."','".$fechad."','".$horai."','".$paciente."','".$hora."','99','".$item2."','".$wliq."','".$seguridad."')";
				#echo $query;
				$err1 = mysql_query($query,$conex);
			}
		}
		if($wmed != "")
		{
			$query ="select *  from salam_000002  where hora_inicio='".$horai."' and fecha='".$fechad."' and paciente='".$paciente."' and hora='".$hora."'  and parametro='99' and codigo='".$item3."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1>0)
			{
				$query = "update salam_000002 set valor='".$wmed."' where hora_inicio='".$horai."' and fecha='".$fechad."' and paciente='".$paciente."' and hora='".$hora."'   and parametro='99' and codigo='".$item3."'";
				$err1 = mysql_query($query,$conex);
			}
			else
			{
				$fecha = date("Y-m-d");
				$hora1 = (string)date("H:i:s");
				$seguridad="C-salam";
				$query = "insert  into salam_000002";
				$query = $query." (medico,fecha_data,hora_data,anestesiologo,fecha,hora_inicio,paciente,hora,parametro,codigo,valor,seguridad)";
				$query = $query." values('".$key."','".$fecha."','".$hora1."','".$medico."','".$fechad."','".$horai."','".$paciente."','".$hora."','99','".$item3."','".$wmed."','".$seguridad."')";
				#echo $query;
				$err1 = mysql_query($query,$conex);
			}
		}
	}
	$ruta="/matrix/images/medical/salam/";
	echo "<input type='HIDDEN' name= 'medico' value='".$medico."'>";
	echo "<input type='HIDDEN' name= 'fechad' value='".$fechad."'>";
	echo "<input type='HIDDEN' name= 'paciente' value='".$paciente."'>";
	echo "<input type='HIDDEN' name= 'horai' value='".$horai."'>";
	echo "<input type='HIDDEN' name= 'hora' value='".$hora."'>";
	$query = "select codigo_a,descripcion,valor,label,codigo,codigo_n,tipo from salam_000002,salam_000001 where hora_inicio='".$horai."' and fecha='".$fechad."' and paciente='".$paciente."' and hora='".$hora."' and parametro=codigo_n  order by parametro";
	#echo $query."<br>";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	echo "<table border=0 cellpadding=2>";
	echo "<tr><td align=center colspan=4 bgcolor=#cccccc><font  face='arial'><b>DATOS REGISTRADOS A LAS : ".$hora."</b></font></td></tr>";
	echo "<tr><td bgcolor=#cccccc><font  face='arial'><B>ETIQUETA</B></font></TD><td bgcolor=#cccccc><font  face='arial'><B>CODIGO</B></font></TD><TD bgcolor=#cccccc><font  face='arial'><B>DESCRIPCION</B></font></TD><TD bgcolor=#cccccc><font  face='arial'><B>VALOR</B></font></TD>";
	for ($k=0;$k<$num;$k++)
	{
		$row = mysql_fetch_array($err);
		if($row[5] != "99")
			if(substr($row[6],0,2) != "02")
 				echo "<tr><td  align=center bgcolor=#cccccc><img src=".$ruta.$row[3]."></td><td bgcolor=#cccccc><font  face='arial'>".$row[0]."</td><td bgcolor=#cccccc><font  face='arial'>".$row[1]."</td><td bgcolor=#cccccc align=center><font  face='arial'><b>".$row[2]."</font></b></td></tr>";
 			else
 				echo "<tr><td  align=center bgcolor=#cccccc>*</td><td bgcolor=#cccccc><font  face='arial'>".$row[0]."</font></td><td bgcolor=#cccccc><font  face='arial'>".$row[1]."</font></td><td bgcolor=#cccccc align=center><font  face='arial'><b>".$row[2]."</font></b></td></tr>";
 		else
 			echo "<tr><td  align=center bgcolor=#cccccc>*</td><td bgcolor=#cccccc><font  face='arial'>".$row[0]."</font></td><td bgcolor=#cccccc><font  face='arial'>".$row[4]."</font></td><td bgcolor=#cccccc align=center><font  face='arial'><b>".$row[2]."</font></b></td></tr>";
 	}
 	echo "</table><br>";
 	echo "<table border=0 cellpadding=2>";
 	echo "<tr><td align=center colspan=3 bgcolor=#cccccc><font  face='arial'><b>DATOS</b></font></td></tr>";
 	echo "<tr><td   bgcolor=#cccccc><font  face='arial'><B>ITEM</B></font></TD><TD bgcolor=#cccccc><font  face='arial'><B>DESCRIPCION</B></font></TD><TD bgcolor=#cccccc><font  face='arial'><B>VALOR</B></font></TD>";
 	echo "<tr><td bgcolor=#cccccc ><font  face='arial'>Parametro</font></td>";
	$query = "SELECT codigo_n,descripcion from salam_000001 where codigo_n != '99' ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		echo "<td bgcolor='#cccccc'><select name='item1'>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</td><td bgcolor='#cccccc'><input type='TEXT' name='wpar' size=5 maxlength=5></td></tr>";
	}
	echo "<tr><td bgcolor=#cccccc ><font  face='arial'>Liquidos</font></td>";
	$query = "SELECT subcodigo,descripcion from det_selecciones where medico='salam' and codigo= '005' ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		echo "<td bgcolor='#cccccc'><select name='item2'>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</td><td bgcolor='#cccccc'><input type='TEXT' name='wliq' size=5 maxlength=5></td></tr>";
	}
	echo "<tr><td bgcolor=#cccccc ><font  face='arial'>Medicamentos</font></td>";
	$query = "SELECT subcodigo,descripcion from det_selecciones where medico='salam' and codigo= '006' ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		echo "<td bgcolor='#cccccc'><select name='item3'>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</td><td bgcolor='#cccccc'><input type='TEXT' name='wmed' size=5 maxlength=5></td></tr>";
	}	
	echo "<tr><td bgcolor=#cccccc colspan=3 align=center>Datos Completos<input type='checkbox' name='ok'></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=3 align=center><input type='submit' value='ENTER'></td></tr>";
	echo "</table><br>";
 ?>
 </body>
</html>