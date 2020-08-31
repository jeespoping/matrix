<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Dinamica de Tablas - innodb</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> newtable.php Ver. 2006-05-04</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
/**********************************************************************************************************************  
     Programa :  newtable.php
     Fecha de Liberación : 2003-09-30
   	 Realizado por : Pedro Ortiz Tamayo
     Version Actual : 2006-01-04
    
    OBJETIVO GENERAL : Este programa permite la creacion de tablas en la base de datos MySql MATRIX.
    
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
	$key = substr($user,2,strlen($user));
	echo "<form action='detform.php' method=post>";
	

	

	$query = "select * from det_formulario where medico='".$key."' and codigo='".$pos2."' order by posicion";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	$query = "create table IF NOT EXISTS ".$pos1."_".$pos2." ( Medico varchar(8) not null,Fecha_data date not null ,Hora_data time not null ,";
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);
		switch ($row[4])
		{
			case "0":
				$query=$query.$row[3]." VARCHAR(80) not null, ";
				break;
			case "1":
				$query=$query.$row[3]." INT not null, ";
				break;	
			case "2":
				$query=$query.$row[3]." Double not null,";
				break;
			case "3":
				$query=$query.$row[3]." date not null, ";
				break;
			case "4":
				$query=$query.$row[3]." longtext not null, ";
				break;
			case "5":
				$query=$query.$row[3]." text not null,";
				break;
			case "6":
				$query=$query.$row[3]." double not null,";
				break;
			case "7":
				$query=$query.$row[3]." text not null,";
				break;
			case "8":
				$query=$query.$row[3]." INT not null,";
				break;
			case "9":
				$query=$query.$row[3]." text not null,";
				break;
			case "10":
				$query=$query.$row[3]." char(3) not null,";
				break;
			case "11":
				$query=$query.$row[3]." time not null,";
				break;
			case "12":
				$query=$query.$row[3]." text not null,";
				break;
			case "13":
				//$query=$query.$row[3]." char(1) not null,";
				break;
			case "14":
				$query=$query.$row[3]." text not null,";
				break;
			case "15":
				$query=$query.$row[3]." text not null,";
				break;
			case "16":
				$query=$query.$row[3]." char(8) not null,";
				break;
			case "17":
				//$query=$query.$row[3]." char(1) not null,";
				break;
			case "18":
				$query=$query.$row[3]." VARCHAR(80) not null, ";
				break;
		}
		if($i>=$num-1)
			$query=$query." Seguridad varchar(10) not null, id bigint not null auto_increment, primary key(id))";
	}	
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	echo "<center>";
	if($err !=1)
	{
		echo "ERROR EN LA CREACION DE LA TABLA"."<BR><BR>";
		echo mysql_errno().":".mysql_error()."<br>";
		echo $query."<br>";
	}
	else
		echo "TABLA : ".$pos1."_".$pos2." CREADA SATISFACTORIAMENTE"."<BR><BR>";
	echo "<input type='submit' value='RETORNAR'>";
	mysql_close($conex);
  }
?>
</body>
</html>
