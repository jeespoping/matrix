<html>
<head>
  <title>ORDENES DE LABORATORIO</title>
</head>
<body BGCOLOR="">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Consulta de Existencias en Inventario</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> ConsultaWebInv.php Ver. 1.00</b></font></tr></td></table>
</center>

<BODY TEXT="#000066">
<?php
include_once("conex.php");
		session_start();
		if(!isset($_SESSION['user']))
			echo"error";
		else
		{
			$key = substr($user,2,strlen($user));
			

			mysql_select_db("MATRIX");
			echo "<form action='ConsultaWebInv.php' method=post>";
			if (isset($webarticulo) and  isset($webcantidad))
			{
				$webpar=$webarticulo."-".$webcantidad;
				// incluir las clases SOAP
				//require_once('nusoap/lib/nusoap.php');
				require_once('nusoap.php');
				// Definir un arreglo de parametros. En este ejemplo buscaremos el siguiente
				// consecutivo para 'Ordenes'. 
				$param = array();
				$param = array('webpar'=>$webpar);
				//$param = array('webarticulo'=>$webarticulo);
				//$param = array('webcantidad'=>$webcantidad);
				
				// Definir el path del servidor Web, en este ejemplo la IP donde se ejecuta 4D Server
				// es 131.1.18/17 y se activo el puerto 8081 con el servidor web.
				
				$serverpath ='http://132.1.20.30:8080/4DSOAP';
				
				//define method namespace
				$namespace="http://www.lmla.inv.com/namespace/default";
				
				// Crear un nuevo cliente SOAP
				$client = new soapclient($serverpath);
				// Incocar el metodo 4D
				$webres = $client->call('PCONSULTAWEB',$param,$namespace);
				
				// Si ocurre un error informarlo en el explorador
				
				if (isset($fault)) 
					 print "Error: ". $fault;
				else if ($webres['webresultado'] == "1")
				 			{
				     			echo "<table border=0>";
								echo "<tr><td align=center>**** EXISTENCIA CONFIRMADA ****</td></tr>";
								echo "<tr><td align=center>DISPONIBLES : ".$webres['webtotal']."</td></tr>";
								echo "<tr><td align=center><input type='submit' value='ENTER'></td></tr></table>";
							}
						 else 
							{
						        echo "<table border=0>";
								echo "<tr><td align=center>**** EXISTENCIA INSUFICIENTE ****</td></tr>";
								echo "<tr><td align=center>DISPONIBLES : ".$webres['webtotal']."</td></tr>";
								echo "<tr><td align=center><input type='submit' value='ENTER'></td></tr></table>";
						     }
			}
			else
			{	
				echo "<center><table border=0>";
				echo "<tr><td align=center colspan=2><b>LABORATORIO MEDICO LAS AMERICAS<b></td></tr>";
				echo "<tr><td align=center colspan=2>CONSULTA DE EXISTENCIA EN INVENTARIO</td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Articulo</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='webarticulo' size=12 maxlength=12></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Cantidad</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='webcantidad' size=12 maxlength=12></td></tr>";
				echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
			}
		}
?>
</body>
</html>