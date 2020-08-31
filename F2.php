<html>
<head>
<title>MATRIX</title>
<style type="text/css">
 .fondo	{background-image: url(/matrix/images/medical/root/Logo_Matrix.jpg);
         background-repeat: no-repeat;
         background-position: top center;
        }    
         #tipo1{color:#000066;font-size:12pt;font-family:Courier New;font-weight:bold;}
</style>
</head>
<?php
include_once("conex.php");
	function GetIP()
	{
		$IP_REAL = " ";
		$IP_PROXY = " ";
		if (@getenv("HTTP_X_FORWARDED_FOR") != "") 
		{ 
			$IP_REAL = getenv("HTTP_X_FORWARDED_FOR"); // Muestra la IP real del usuario, es decir, la Pública 
			$IP_PROXY = getenv("REMOTE_ADDR"); // Muestra la IP de un posible Proxy 
		} 
		else 
		{ 
			$IP_REAL = getenv("REMOTE_ADDR"); // En caso de que no exista un Proxy solo mostrara la IP Publica del visitante 
		}
		$IPS=$IP_REAL."|".$IP_PROXY;
		return $IPS;
	}
	@session_start();
	if (!isset($user))
	{
		if(!isset($_SESSION['user']))
		{
			session_register("user","usera","IIPP");
			$user="1-".strtolower($codigo);
			$usera=strtolower($codigo);
			$ipdir=explode("|",GetIP());
			$IIPP=$ipdir[0];
		}
	}
	if (substr($user,0,1) == "2")
	{
		echo "<body bgcolor=#FFFFFF class='fondo'>";
		//echo "<table border=0 align=center><tr><td align=center><IMG SRC='/matrix/images/medical/root/matrix.gif'></td></tr></table>";
		echo "<table  border=0 align=center>";
		echo "<tr><td id=tipo1><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>";
		echo "<tr><td id=tipo1><IMG SRC='/matrix/images/medical/root/pirate1.gif' BORDER=0></td>";
		echo "<td id=tipo1><A HREF='f1.php'>ENTRADA ILEGAL AL PROGRAMA</a></td></tr></table></body>";
	}
	else
	{
		

		mysql_select_db("matrix") or die ("ERROR AL CONECTARSE A MATRIX");
		$query = "select codigo,prioridad,grupo from usuarios where codigo='".$codigo."' and password='".$password."' and activo = 'A'";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$prioridad=$row[1];
		$codigo=$row[0];
		$grupo=$row[2];
		mysql_free_result($err);
		mysql_close($conex);
		if ($num > 0)
		{
		      switch($grupo)
			{
				case 'pda':
			 		echo "<center><table border=0>";
					echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center>INGRESO DE MEDICAMENTOS AL SISTEMA</td></tr>";
					if(isset($user)){
						unset($user);
					}
					echo "<tr><td align=center><A HREF='pda/procesos/pda_ingreso.php'>Haga Click Para Ingresar Al Sistema</A></td></tr></table>";
				break;
				
				case 'pdadevol':
			 		echo "<center><table border=0>";
					echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center>DEVOLUCIONES DE MEDICAMENTOS AL SISTEMA</td></tr>";
					if(isset($user)){
						unset($user);
					}
					echo "<tr><td align=center><A HREF='pda/procesos/devolucion.php'>Haga Click Para Ingresar Al Sistema</A></td></tr></table>";
				break;
								
				case 'fidelidad':
			 		echo "<center><table border=0>";
					echo "<tr><td align=center><b>CLINICA LAS AMERICAS <b></td></tr>";
					echo "<tr><td align=center>INFORMACI�N CLIENTES</td></tr>";
					echo "<tr><td align=center><A HREF='Magenta/procesos/Magenta.php?user=".$user."'>Haga Click Para Ingresar Al Sistema</A></td></tr></table>";
				break;
				
				case 'ayudaenl':
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					

					mysql_select_db("MATRIX");
        				$query = "insert root_000004 (medico,fecha_data,hora_data,usuario,seguridad) values ('root','".$fecha."','".$hora."','".$codigo."','C-root')";
					$err1 = mysql_query($query,$conex);
					mysql_close($conex);
					echo "<BODY TEXT='#000066'>";
					echo "<font size=9>";
			 		echo "<center><table border=0>";
					echo "<tr><td align=center><h1>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
					echo "<tr><td align=center><h2>SISTEMA DE AYUDA EN LINEA PARA FACTURACION</td></tr>";
					echo "<tr><td align=center><h2>DIRECCION DE MERCADEO</td></tr>";
					echo "<tr><td align=center><h2>ADVERTENCIA</td></tr>";
					echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/prohibido.gif'></td></tr>";
					echo "<tr><td>TODA  ACTIVIDAD QUE &nbsp&nbspUSTED REALICE &nbsp&nbspEN ESTA  PAGINA  SERA  REGISTRADA EN EL &nbsp&nbspSISTEMA</td></tr>";
					echo "<tr><td>QUEDA TOTALMENTE PROHIBIDO LA  IMPRESION,  COPIA O  REPRODUCCION PARCIAL O TOTAL</td></tr>";
					echo "<tr><td>DE LA INFORMACION  QUE  AQUI SE  DESPLIEGA SIN LA AUTORIZACION  DE LA GERENCIA DE LA</td></tr>";
					echo "<tr><td>CLINICA. </td></tr>";
					echo "<tr><td>CUALQUIER TRANSGRECION DE ESTA NORMA SERA SANCIONADA &nbsp&nbspSEGUN LO CONTEMPLADO </td></tr>";
					echo "<tr><td>EN EL REGLAMENTO INTERNO DE TRABAJO DE LA INSTITUCION.</td></tr>";
					echo "<tr><td align=center><A HREF='/ayuda/indexAY.html'><b>HAGA CLICK PARA COMENZAR AYUDA EN LINEA</b></A></td></tr>";
					echo "<tr><td align=center><A HREF='salida.php'>Haga Click Para Salir del Programa</A></td></tr></table>";
				break;
					
				default:
					echo "<frameset cols=20%,80% frameborder=0 framespacing=2>";
					echo "<frame src='options.php?grupo=".$grupo."&amp;prioridad=".$prioridad."' name='options' marginwidth=0 marginheiht=0>";
					echo "<frameset rows='80,*' frameborder=0 framespacing=0>";
					echo "<frame src='titulos.php?grupo=".$grupo."' name='titulos' marginwidth=0 marginheiht=0>";
					echo "<frame src='general.php?grupo=".$grupo."' name='main' marginwidth=0 marginheiht=0>";
				break;	
			}
		}
		else
		{
			echo "<body bgcolor=#FFFFFF class='fondo'>";
			echo "<BODY TEXT='#000066'>";
			echo "<center>";
			//echo "<IMG SRC='/matrix/images/medical/root/matrix.gif'>";
			//echo "<HR>";
			echo "</center>";
			echo "<table  border=0 align=center>";
			echo "<tr><td id=tipo1><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>";
			echo "<tr><td id=tipo1><IMG SRC='/matrix/images/medical/root/denegado.jpg' BORDER=0></td>";
			echo "<td id=tipo1><A HREF='f1.php'>USUARIO NO EXISTE O ESTA INACTIVO</a></td></tr></table></body>";
		}
	}

?>






</html>




