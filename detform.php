<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle de Formularios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> detform.php Ver. 2006-01-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
/**********************************************************************************************************************  
     Programa :  detform.php
     Fecha de Liberación : 2003-09-30
   	 Realizado por : Pedro Ortiz Tamayo
     Version Actual : 2006-01-04
    
    OBJETIVO GENERAL : Este programa muestra los campos que componen un formulario y permite crearlos, modificarlos o borrarlos a traves del programa det_detform.php 
    siempre que la tabla no haya sido creada. 
    
    La creacion de tablas se hace a traves del programa newtable.php
    
    Los tipos de datos que se manejan son :
  						0   - Alfanumerico
  						1   - Entero
  						2   - Real
  						3   - Fecha
  						4   - Texto
  						5   - Seleccion
  						6   - Formula
  						7   - Grafico
  						8   - Automatico
  						9   - Relacion
  						10 - Booleano
  						11 - Hora
  						12 - Algoritmico si la variable wswalg = on la salida es controlada x el usuario
  						13 - Titulo  *** No se Almacena en Matrix ***
  						14 - Hipervinculo
  						15 - Algoritmico_M (Modificable)
  						16 - Protegido o Password
  						17 - Auxiliar *** No se Almacena en Matrix *** Permite ejecucion de algoritmos com salida variable
  						18 - Relacion_NE Campos de relacion NO Especifica. Su funcion es identica ala campo de Relacion pero solo almacena la primera relacion

  					
   REGISTRO DE MODIFICACIONES :
   
   .2006-01-04
   		Se crea el tipo de Relacion_NE para otimizar la grabacion  de los campos relacionados con otros formularios. En estos campos solo se almacena
   		el primer campo relacionado.
  						
    .2003-09-30
    	Ultima Modificacion Registrada.
***********************************************************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}

	$key = substr($user,2,strlen($user));
	echo "<form action='detform.php' method=post>";
	

	

	$query = "select * from formulario where medico='".$key."' order by codigo";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if(!isset($Form))
		$Form="0";
	$ini = strpos($Form,"-");
	echo "<table border=0 align=center cellpadding=3>";
	echo "<tr>";
	echo "<td bgcolor='#cccccc'><font size=2><b>Formularios :</b></font></td>";
	echo "<td bgcolor='#cccccc'><select name='Form'>";
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);
		if ($row[1] == substr($Form,0,$ini))
			echo "<option selected>".$row[1]."-".$row[2]."</option>";
		else
			echo "<option>".$row[1]."-".$row[2]."</option>";
	}
	echo "<td bgcolor='#cccccc'><input type='submit' value='IR'>";
	echo "</td></tr></table><br>";
	
	echo "<table border=0 align=left>";
	if($Form !="0")
	{
		$query = "show tables ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wsw=0;
		if (is_int($ini))
			$Form1 = $key."_".substr($Form,0,$ini);
		else
			$Form1 = $key."_".$Form;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($row[0]==$Form1)
				$wsw=1;
		}
		if($wsw==0)
		{
			echo "<tr><td align=center><A HREF='det_detform.php?pos1=".$key."&amp;pos2=".$Form."&amp;pos3=0&amp;call=1'>Nuevo</td></tr>";
			echo "<tr><td align=center><A HREF='newtable.php?pos1=".$key."&amp;pos2=".substr($Form,0,$ini)."'>Crear Tabla</td></tr>";
		}
	}
	echo "</table><BR><BR>";
	if ($ini == 0)
		$query = "select * from det_formulario where medico='".$key."' and codigo='".$Form."' order by posicion";
	else
		$query = "select * from det_formulario where medico='".$key."' and codigo='".substr($Form,0,$ini)."' order by posicion";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr>";
  		echo "<td bgcolor=".$color."><font size=2><b>Campo</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Descripcion</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Tipo</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Posicion</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Comentarios</b></font></td>";  		  
  		echo "<td bgcolor=".$color."><font size=2><b>Activo</b></font></td>";	
  		echo "<td bgcolor=".$color."><font size=2><b>Seleccion</b></font></td>";
		echo "</tr>";
		$r = 0;
		for ($i=0;$i<$num;$i++)
		{
			$r = $i/2;
			if ($r*2 === $i)
				$color="#CCCCCC";
			else
				$color="#999999";
			$row = mysql_fetch_array($err);
			echo "<tr>";
			echo "<td bgcolor=".$color."><font size=2>".$row[2]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[3]."</font></td>";
			switch ($row[4])
			{
				case "0":
					echo "<td bgcolor=".$color." align=center><font size=2>Caracteres</font></td>";
					break;
				case "1":
					echo "<td bgcolor=".$color." align=center><font size=2>Entero</font></td>";
					break;
				case "2":
					echo "<td bgcolor=".$color." align=center><font size=2>Real</font></td>";
					break;
				case "3":
					echo "<td bgcolor=".$color." align=center><font size=2>Fecha</font></td>";
					break;
				case "4":
					echo "<td bgcolor=".$color." align=center><font size=2>Texto</font></td>";
					break;
				case "5":
					echo "<td bgcolor=".$color." align=center><font size=2>Seleccion</font></td>";
					break;
				case "6":
					echo "<td bgcolor=".$color." align=center><font size=2>Formula</font></td>";
					break;
				case "7":
					echo "<td bgcolor=".$color." align=center><font size=2>Grafico</font></td>";
					break;
				case "8":
					echo "<td bgcolor=".$color." align=center><font size=2>Automatico</font></td>";
					break;
				case "9":
					echo "<td bgcolor=".$color." align=center><font size=2>Relacion</font></td>";
					break;
				case "10":
					echo "<td bgcolor=".$color." align=center><font size=2>Booleano</font></td>";
					break;
				case "11":
					echo "<td bgcolor=".$color." align=center><font size=2>Hora</font></td>";
					break;
				case "12":
					echo "<td bgcolor=".$color." align=center><font size=2>Algoritmico</font></td>";
					break;
				case "13":
					echo "<td bgcolor=".$color." align=center><font size=2>Titulo</font></td>";
					break;
				case "14":
					echo "<td bgcolor=".$color." align=center><font size=2>Hipervinculo</font></td>";
					break;
				case "15":
					echo "<td bgcolor=".$color." align=center><font size=2>Algoritmico_M</font></td>";
					break;
				case "16":
					echo "<td bgcolor=".$color." align=center><font size=2>Protegido</font></td>";
					break;
				case "17":
					echo "<td bgcolor=".$color." align=center><font size=2>Auxiliar</font></td>";
					break;
				case "18":
					echo "<td bgcolor=".$color." align=center><font size=2>Relacion_NE</font></td>";
					break;
			}
			echo "<td bgcolor=".$color." align=center><font size=2>".$row[5]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[6]."</font></td>";
			switch ($row[7])
			{
				case "A":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
					break;
				case "I":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/inactivo.gif' ></td>";
					break;
				case "P":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/protegido.gif' ></td>";
					break;
				default:
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
					break;
			}
			echo "<td bgcolor=".$color." align=center><font size=2><A HREF='det_detform.php?pos1=".$key."&amp;pos2=".$Form."&amp;pos3=".$row[2]."&amp;wsw=".$wsw."&amp;call=1'>Editar</font></td>";
			echo "</tr>";
		}
		echo "</tabla>";
		echo "<table border=0 align=center>";
		echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";	
	}
	else
	{
		echo " Tabla Vacia";
	}
	mysql_free_result($err);
	mysql_close($conex);
}
?>
</body>
</html>
