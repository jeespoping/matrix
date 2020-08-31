<html>
<head>
  	<title>MATRIX Diccionario de Datos</title>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Dictionary.submit();
	}
//-->
</script>
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Diccionario de Datos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Dictionary.php Ver. 2006-04-04</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");


/**********************************************************************************************************************  
	   PROGRAMA : Dictionary.php
	   Fecha de Liberación : 2006-04-04
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2006-01-08
	   
	   OBJETIVO GENERAL : 
	   Este programa permite documentar las tablas en el archivo nro 30 de root (Diccionario de Datos).
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   
	   .2006-04-04
	  	 	Se Coloco especifico el tipo de campo.
	  	 	
	   .2006-04-03
	  	 	Se Introdujo el campo de descripcion (Texto) en la tabla root_000030 (Diccionario de Datos)
	  	 	
	   .2006-01-08
	  	 	Se eliminaron los Submit y se colocaron con JavaScript en los CheckBox.
	  	 	
	   .2006-01-07
	  	 	Liberacion del programa
	  	 	
	  	
	   
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Dictionary' action='Dictionary.php' method=post>";
	

	

	if(isset($ok))
	{
		for ($i=0;$i<$num;$i++)
		{ 
			if($data[$i][3] != "")
			{
				$query = "select Dic_Descripcion  from  root_000030 ";
		 		$query .= " where Dic_Usuario = '".substr($wusu,0,strpos($wusu,"-"))."'";
		 		$query .= "     and  Dic_Formulario = '".substr($wform,0,strpos($wform,"-"))."'";
		 		$query .= "     and  Dic_Campo = '".$data[$i][0]."'";
				$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO DICCIONARIO");
				$num1 = mysql_num_rows($err1);
				if($num1 == 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert root_000030 (medico,fecha_data,hora_data, Dic_Usuario, Dic_Formulario, Dic_Campo, Dic_Descripcion, Dic_Comentario, seguridad) values ('root','".$fecha."','".$hora."','".substr($wusu,0,strpos($wusu,"-"))."','".substr($wform,0,strpos($wform,"-"))."','".$data[$i][0]."','".$data[$i][3]."','".$data[$i][4]."','C-root')";
					$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				}
				else
				{
					$query =  " update root_000030 set Dic_Descripcion = '".$data[$i][3]."', Dic_Comentario = '".$data[$i][4]."' where Dic_Usuario = '".substr($wusu,0,strpos($wusu,"-"))."' and Dic_Formulario = '".substr($wform,0,strpos($wform,"-"))."' and Dic_Campo = '".$data[$i][0]."'";
					$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				}
			}
		}
		unset($ok);
		unset($ok1);
	}
	if (!isset($ok1))
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX</font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>DICCIONARIO DE DATOS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2006-01-07</font></b></font></td></tr>";
		$query =  " SELECT medico,descripcion FROM formulario,usuarios where medico=usuarios.codigo  GROUP  BY medico,Descripcion ORDER  BY medico";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=".$wcolor.">Usuario : </td><td bgcolor=".$wcolor."><select name='wusu' onchange='enter()'>";
		for ($i=0;$i<$num;$i++)
		{
	      $row = mysql_fetch_array($err); 
	      if(isset($wusu) and $wusu == $row[0]."-".$row[1])
	      	echo "<option selected>".$row[0]."-".$row[1]."</option>";
	      else
	      	echo "<option>".$row[0]."-".$row[1]."</option>";
		 }
		 echo "</select></td></tr>";
		 if(isset($wusu))
		 {
			 $query =  " SELECT codigo,nombre  FROM formulario where medico='".substr($wusu,0,strpos($wusu,"-"))."' ORDER BY codigo ";
			 $err = mysql_query($query,$conex);
			 $num = mysql_num_rows($err);
			 echo "<tr><td bgcolor=".$wcolor.">Formulario : </td><td bgcolor=".$wcolor."><select name='wform'>";
			 for ($i=0;$i<$num;$i++)
			 {
				$row = mysql_fetch_array($err); 
				 if(isset($wform) and $wform == $row[0]."-".$row[1])
		      		echo "<option selected>".$row[0]."-".$row[1]."</option>";
		      	else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			 }
			 echo "</select></td></tr>";
			 echo "<tr><td align=center bgcolor=#dddddd colspan=13><font color=#000066 size=5><b>Datos OK</b><input type='checkbox' name='ok1'  onclick='enter()'></font></td></tr>";
	 	}
		echo "</table>";  
	}
	else
	{
		$wcolor="#cccccc";
		echo "<table border=0 align=center>";
		echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>DICCIONARIO DE DATOS</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 2006-01-07</font></b></font></td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Usuario</td><td bgcolor=".$wcolor.">".$wusu."</td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Formulario</td><td bgcolor=".$wcolor.">".$wform."</td></tr>";
		echo "</table><br><br>";  
		echo "<table border=0 align=center>";
		echo "<tr><td align=center bgcolor=#999999 colspan=13><font color=#000066 size=5><b>DETALLE DE CAMPOS</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >NUMERO<BR> CAMPO</font></td><td align=center bgcolor=#000066><font color=#ffffff >NOMBRE<BR>CAMPO</font></td><td align=center bgcolor=#000066><font color=#ffffff >TIPO</font></td><td align=center bgcolor=#000066><font color=#ffffff >DESCRIPCION</font></td><td align=center bgcolor=#000066><font color=#ffffff >COMENTARIOS</font></td></tr>";
		if(!isset($dta))
 		{
	 		$dta=0;
			$query = "select Campo, Descripcion, Tipo from  det_formulario where medico='".substr($wusu,0,strpos($wusu,"-"))."' and codigo='".substr($wform,0,strpos($wform,"-"))."' order by posicion ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$data=array();
				$dta=1;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$data[$i][0]=$row[0];
					$data[$i][1]=$row[1];
					switch ($row[2])
					{
						case "0":
							$data[$i][2]="Alfanumerico";
							break;
						case "1":
							$data[$i][2]="Entero";
							break;	
						case "2":
							$data[$i][2]="Real";;
							break;
						case "3":
							$data[$i][2]="Fecha";
							break;
						case "4":
							$data[$i][2]="Texto";
							break;
						case "5":
							$data[$i][2]="Seleccion";
							break;
						case "6":
							$data[$i][2]="Formula";
							break;
						case "7":
							$data[$i][2]="Grafico";
							break;
						case "8":
							$data[$i][2]="Automatico";
							break;
						case "9":
							$data[$i][2]="Relacion";
							break;
						case "10":
							$data[$i][2]="Booleano";
							break;
						case "11":
							$data[$i][2]="Hora";
							break;
						case "12":
							$data[$i][2]="Algoritmico";
							break;
						case "13":
							$data[$i][2]="Titulo";
							break;
						case "14":
							$data[$i][2]="Hipervinculo";
							break;
						case "15":
							$data[$i][2]="Algoritmico_M";
							break;
						case "16":
							$data[$i][2]="Protegido";
							break;
						case "17":
							$data[$i][2]="Auxiliar";
							break;
						case "18":
							$data[$i][2]="Relacion_NE";
							break;
					}
					$query = "select Dic_Descripcion,Dic_Comentario  from  root_000030 ";
			 		$query .= " where Dic_Usuario = '".substr($wusu,0,strpos($wusu,"-"))."'";
			 		$query .= "     and  Dic_Formulario = '".substr($wform,0,strpos($wform,"-"))."'";
			 		$query .= "     and  Dic_Campo = '".$row[0]."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$data[$i][3]=$row1[0];
						$data[$i][4]=$row1[1];
					}
					else
					{
						$data[$i][3]="";
						$data[$i][4]="";
					}
				}
			}
		}
		if($dta == 1)
		{
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color="#dddddd";
				else
					$color="#cccccc";
				echo "<tr>";
				echo "<td bgcolor=".$color.">".$data[$i][0]."</td>";	
				echo "<td bgcolor=".$color.">".$data[$i][1]."</td>";	
				echo "<td bgcolor=".$color.">".$data[$i][2]."</td>";
				echo "<td bgcolor=".$color."><INPUT TYPE='text' NAME='data[".$i."][3]' value = '".$data[$i][3]."'></td>";
				echo "<td><textarea name='data[".$i."][4]' cols=40 rows=3>".$data[$i][4]."</textarea>";
				echo "</tr>";
				echo "<input type='HIDDEN' name= 'wusu' value='".$wusu."'>";
				echo "<input type='HIDDEN' name= 'wform' value='".$wform."'>";
				echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
				echo "<input type='HIDDEN' name= 'dta' value=".$dta.">";
				echo "<input type='HIDDEN' name= 'ok1' value=".$ok1.">";
				echo "<input type='HIDDEN' name= 'data[".$i."][0]' value=".$data[$i][0].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][1]' value=".$data[$i][1].">";
				echo "<input type='HIDDEN' name= 'data[".$i."][2]' value=".$data[$i][2].">";
			}
			echo "<tr><td align=center bgcolor=#dddddd colspan=13><font color=#000066 size=5><b>Datos OK</b><input type='checkbox' name='ok'  onclick='enter()'></font></td></tr>";
		}
		echo "</table><br><br>";
	}
}
?>