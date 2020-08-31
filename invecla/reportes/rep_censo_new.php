<?php
include_once("conex.php"); header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>Reporte para control del censo</title>
</head>

<script type="text/javascript">
// Vuelve a la página anterior llevando sus parámetros
function retornar(wemp_pmla,fec1,fec2,serv,tipo)
	{
		location.href = "rep_censo_new.php?wemp_pmla="+wemp_pmla+"&fec1="+fec1+"&fec2="+fec2+"&serv="+serv+"&tipo="+tipo;
	}
	
// Cierra la ventana
function cerrar_ventana()
	{
		window.close();
    }
    
</script>
<body>

<?php

/********************************************************
*     REPORTE PARA EL CONTROL DEL CENSO DIARIO			*
*********************************************************/
/*

 *************************************************************************************************
 PROGRAMA						:Reporte para el control del censo diario
 AUTOR							:Juan David Londoño
 FECHA CREACION					:ENERO 2007
 FECHA ULTIMA ACTUALIZACION 	:12 de Abril de 2010
 DESCRIPCION					:Este reporte sirve para llevar el control del censo diario
 *************************************************************************************************

 *************************************************************************************************
 * MODIFICACIONES
 *************************************************************************************************
 * 2012-04-24 - Se agregaron las sentencias GROUP BY a los querys principales del reporte de modo 
 *				que no se repitieran registros por tener datos duplicados en alguna tabla, por ejemplo:
 *				cuando habia dos campos Pacced en la tabla root_000036 iguales, porque tenia Pactid=NU
 *				y luego cambi{o a RC pero con el mismo n{umero, esto hacia que se duplicaran registros 
 *				en el reporte
 *				Se crean las tablas temporales tmp_ingresos y tmp_egresos para agilizar el tiempo de
 *				generación del reporte
 *************************************************************************************************
 * 2011-11-01 - Se modifica estilos adaptando a la hoja de estilo actual del sistema.
 *				Se adicionó calendario para selección de fecha.
 *				Se modificó el query principal incluyendo la tabla movhos_000011 para solo tener en
 *				cuenta los centros de costo hospitalarios (ccohos='on')
 *************************************************************************************************

*/

  // Consulta los datos de las aplicaciones
  function datos_empresa($wemp_pmla)
    {  
	  global $user;   
	  global $conex;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	     
	  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
	  $q = " SELECT detapl, detval, empdes "
	      ."   FROM root_000050, root_000051 "
	      ."  WHERE empcod = '".$wemp_pmla."'"
	      ."    AND empest = 'on' "
	      ."    AND empcod = detemp "; 
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res); 
	  
	  if ($num > 0 )
	     {
		  for ($i=1;$i<=$num;$i++)
		     {   
		      $row = mysql_fetch_array($res);
		      
		      if ($row[0] == "movhos")
		         $wbasedato=$row[1];

			  if ($row[0] == "tabcco")
		         $wtabcco=$row[1];

			 }  
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	  
	  $winstitucion=$row[2];
	      
    }
	
session_start();

// Inicia la sessión del usuario
if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
// Si el usuario no está registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
	echo "error";
else	// Si el usuario está registrado inicia el programa
{	            
 	
  include_once("root/comun.php");

  $conex = obtenerConexionBD("matrix");

  datos_empresa($wemp_pmla);

  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 
  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " Abr. 24 de 2012";

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtener titulo de la página con base en el concepto
  $titulo = "Reporte para el Control del Censo Diario (Nuevo)";

  $wfecha = date("Y-m-d");
	
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

  echo "<form name='rep_censo' action='' method='post'>";
  
  if (!isset($bandera))
  {

	if (!isset($fec1))
		$fec1=$wfecha;
	if (!isset($fec2))
		$fec2=$wfecha;

	echo "<table border=0 align=center>";

	// seleccion para ingreso, egreso
	echo "<tr><td align=left class='fila2' width='190'><b> Tipo: <br></b>";
	echo " <select name='tipo'>";
	if(isset($tipo) && $tipo!="")
		echo "<option>".$tipo."</option>";
	echo "<option>01-INGRESO</option>";
	echo "<option>02-EGRESO</option>";
	echo "</select></td>";
        
	// seleccion para el servicio
	echo "<td align=left class='fila2'><b> Servicio: <br></b>";
	echo " <select name='serv'>";

	/*$query = "SELECT Ccocod, Cconom 
				FROM ".$wtabcco." 
			   WHERE Ccouni = '2H' 
			   ORDER by 1";*/
			   
	//2010-04-12 se modifica este query para que traiga un cc hibrido
	$query = "select Ccocod, Cconom 
			from ".$wbasedato."_000011 
			where Ccourg='off' 
			and Ccohos='on' 
			and Ccoayu='off'"; 
			  
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if(isset($serv) && $serv!="")
		echo "<option>".$serv."</option>";
	echo "<option>9999-TODOS LOS SERVICIOS</option>";
	for ($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_array($err);
		echo "<option>".$row[0]."-".$row[1]."</option>";
	}
	echo "</select></td></tr>";
    

	echo "<tr><td align=left class='fila2'><b> Fecha Inicial </b><br>";
	campoFechaDefecto("fec1", $fec1); 
	echo "</td><td align=left class='fila2'><b>Fecha Final </b><br> ";
	campoFechaDefecto("fec2", $fec2); 
	echo "</td></tr>";
	
	// Espacio entre las filas con los datos y los botones inferiores
	echo "<tr><td colspan='2' height='21'></td></tr>";
	// Botones Retornar y Cerrar Ventana
	echo "<tr><td align='center' colspan='2'><input type='submit' value='Consultar'><input type='hidden' name='bandera' value='1'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
	echo "</table>";

  }
  else
  {

    //ACA COMIENZA LA IMPRESION

	echo "<table border=0 align=center>";
	echo "<tr><td align=center class='fila1'><b>TIPO: ".$tipo."</b></td></tr>";
	echo "<tr><td align=center class='fila1'><b>SERVICIO: ".$serv."</b></td></tr>";
	echo "<tr><td align=center class='fila1'><b>FECHA INICIAL: ".$fec1."&nbsp&nbsp&nbspFECHA FINAL: ".$fec2."</b></td></tr>";
	// Espacio entre las filas con los datos y los botones inferiores
	echo "<tr><td height='21'></td></tr>";
	// Botones Retornar y Cerrar Ventana
	echo "<tr><td align='center'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$fec1\",\"$fec2\",\"$serv\",\"$tipo\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
	echo "</table>";
	echo "<br>";
	
	$tip=explode('-',$tipo);
	/////////////////////////////para el ultimo campo de la fila
	if ($tipo=="01-INGRESO")
	{ 
		$tip1="PROCEDENCIA";
	}
	else if ($tipo=="02-EGRESO")
	{
		$tip1="MOTIVO DE EGRESO";
	}
	echo "<table align='center' border='0' cellspacing='2'>";
	/////////////////////////////////////////////////para cuando son todos los servicios
	if($serv=="9999-TODOS LOS SERVICIOS")
	{
		echo "<tr><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>FECHA ".$tip[1]."</b></font></td><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>HISTORIA CLINICA</b></font></td><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>Nº DE INGRESO</b></font></td>
		<td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>NOMBRE</b></font></td><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>".$tip1."</b></font></td><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>SERVICIO</b></font></td></tr>";
	}
	else
	{  
		echo "<tr><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>FECHA ".$tip[1]."</b></font></td><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>HISTORIA CLINICA</b></font></td><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>Nº DE INGRESO</b></font></td>
		<td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>NOMBRE</b></font></td><td align=center class='encabezadoTabla'><font text color=#FFFFFF><b>".$tip1."</b></font></td></tr>";
	}
		
	if ($tipo=="01-INGRESO") // PARA CUANDO SE SELECCIONA INGRESO
	{ 
		// Creación de la tabla temporal de ingresos
		$qing =  " CREATE TEMPORARY TABLE IF NOT EXISTS tmp_ingresos "
				." ( INDEX idxpac ( Historia_clinica(20),Num_ingreso(10) ), INDEX idxser ( Servicio(10) )   ) "
				." SELECT Fecha_ing, Historia_clinica, Num_ingreso, Procedencia, Servicio
					 FROM ".$wbasedato."_000032 
					WHERE Fecha_ing BETWEEN '".$fec1."' and '".$fec2."' ";
		$resing = mysql_query($qing, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qing . " - " . mysql_error());

		/////////////////////////////////////////////////para cuando son todos los servicios
		if ($serv=="9999-TODOS LOS SERVICIOS")
		{
			$query =  " SELECT Fecha_ing, tmp_ingresos.Historia_clinica, Num_ingreso, concat(pacno1,' ',pacno2), pacap1, pacap2, Procedencia, Servicio
		                  FROM tmp_ingresos, ".$wbasedato."_000011, root_000036, root_000037 
		              	 WHERE Historia_clinica = orihis
		                   AND pacced = oriced 
		                   AND oriori = 01	
		                   AND Servicio = ccocod 
		                   AND ccohos = 'on'	
						 GROUP BY Historia_clinica, Num_ingreso, Procedencia, Servicio
		              	 ORDER BY Servicio ";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        //echo mysql_errno() ."=". mysql_error();
		}
		else
		{
			$cco=explode('-',$serv); // este es el explode para que busque solo por el centro de costos
			
			$query =  " SELECT Fecha_ing, tmp_ingresos.Historia_clinica, Num_ingreso, concat(pacno1,' ',pacno2), pacap1, pacap2, Procedencia, Servicio
		                  FROM tmp_ingresos, ".$wbasedato."_000011, root_000036, root_000037 
		              	 WHERE Historia_clinica = orihis
		                   AND pacced = oriced	
		                   AND oriori = 01
		              	   AND Servicio='".$cco[0]."'
		                   AND Servicio = ccocod 
		                   AND ccohos = 'on' 
						 GROUP BY Historia_clinica, Num_ingreso, Procedencia, Servicio";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        //echo mysql_errno() ."=". mysql_error();
		}
	}
	else if ($tipo=="02-EGRESO") // PARA CUANDO SE SELECCIONA EGRESO
	{ 
			
		// Creación de la tabla temporal de egresos
		$qegr =  " CREATE TEMPORARY TABLE IF NOT EXISTS tmp_egresos "
				." ( INDEX idxpac ( Historia_clinica(20),Num_ingreso(10) ), INDEX idxser ( Servicio(10) )   ) "
				." SELECT Fecha_egre_serv, Historia_clinica, Num_ingreso, Tipo_egre_serv, Servicio
					 FROM ".$wbasedato."_000033 
					WHERE Fecha_egre_serv BETWEEN '".$fec1."' and '".$fec2."' ";
		$resegr = mysql_query($qegr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qegr . " - " . mysql_error());

		/////////////////////////////////////////////////para cuando son todos los servicios
		if ($serv=="9999-TODOS LOS SERVICIOS")
		{
			
			$query =  " SELECT Fecha_egre_serv, tmp_egresos.Historia_clinica, Num_ingreso, concat(pacno1,' ',pacno2), pacap1, pacap2, Tipo_egre_serv, Servicio
		                  FROM tmp_egresos, ".$wbasedato."_000011, root_000036, root_000037 
		              	 WHERE Historia_clinica = orihis
		                   AND pacced = oriced	
		                   AND oriori = 01
		                   AND Servicio !='1130'
		                   AND Servicio = ccocod 
		                   AND ccohos = 'on'	
						 GROUP BY Historia_clinica, Num_ingreso, Tipo_egre_serv, Servicio
		              	 ORDER BY Servicio ";
	        $err = mysql_query($query,$conex);
	        $num = mysql_num_rows($err);
	        //echo mysql_errno() ."=". mysql_error();
		}
		else
		{	
			$cco=explode('-',$serv); // este es el explode para que busque solo por el centro de costos
			
			$query =  " SELECT Fecha_egre_serv, tmp_egresos.Historia_clinica, Num_ingreso, concat(pacno1,' ',pacno2), pacap1, pacap2, Tipo_egre_serv, Servicio
		                  FROM tmp_egresos, ".$wbasedato."_000011, root_000036, root_000037 
		              	 WHERE Historia_clinica = orihis
		                   AND pacced = oriced	
		                   AND oriori = 01
		              	   AND Servicio='".$cco[0]."'
		                   AND Servicio = ccocod 
		                   AND ccohos = 'on' 
						 GROUP BY Historia_clinica, Num_ingreso, Tipo_egre_serv, Servicio";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        //echo mysql_errno() ."=". mysql_error();
		}
	}
		for ($i=1;$i<=$num;$i++)
		    {
		    	if (is_int ($i/2))
                    $wcf="fila1";
                    else
                    $wcf="fila2";
                
                $row = mysql_fetch_array($err);
                  /////////////////////////////////////////////////para cuando son todos los servicios
				if ($serv=="9999-TODOS LOS SERVICIOS")
				{
					// este query es para la procedencia
					$query = "SELECT Ccocod, Cconom 
								FROM ".$wtabcco." 
							   WHERE Ccocod = '".$row[6]."' 
							   ORDER by 1";
				    $err1 = mysql_query($query,$conex);
				    $pro = mysql_fetch_array($err1);
				    
				    //2008-01-24
				    if ($pro[0]=='')
				    {
					    $tipegr=$row[6];
				    }
				    else
				    {
					    $tipegr=$pro[0]."-".$pro[1];
				    }
				    
		    	
				    // este query es para el servicio
				    $query = "SELECT Ccocod, Cconom 
								FROM ".$wtabcco." 
							   WHERE Ccocod = '".$row[7]."' 
							   ORDER by 1";
				    $err2 = mysql_query($query,$conex);
				    $ser = mysql_fetch_array($err2);
				    
				    echo "<tr  class=".$wcf."><td align=center>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=center>".$row[3]." ".$row[4]." ".$row[5]."</td><td align=center>".$tipegr."</td><td align=center>".$ser[0]."-".$ser[1]."</td></tr>";
					
				}
				else
				{  
					// este query es para la procedencia
					$query = "SELECT Ccocod, Cconom 
								FROM ".$wtabcco." 
							   WHERE Ccocod = '".$row[6]."' 
							   ORDER by 1";
				    $err1 = mysql_query($query,$conex);
				    $pro = mysql_fetch_array($err1);
				    
				   
					echo "<tr   class=".$wcf."><td align=center>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=center>".$row[3]." ".$row[4]." ".$row[5]."</td>";
					
					if ($pro != '')// este es para cuando me trae el numero del centro de costos
					{
						echo "<td align=center>".$pro[0]."-".$pro[1]."</td></tr>";
					}
					else // aca es para cuando no me trae el numero del centro de costos, es decir el numero
					{
						echo "<td align=center>".$row[6]."</td></tr>";
					}
				}   
		    }
		    
		if ($serv=="9999-TODOS LOS SERVICIOS")
		{   
			$clp=3;
		}
		else
		{
			$clp=2;
		}
		echo "<tr><td align=left colspan=3 class='encabezadoTabla'><font text color=#FFFFFF><b>NUMERO TOTAL DE PACIENTES: </b></font></td><td align=right colspan=".$clp." class='encabezadoTabla'><font text color=#FFFFFF><b>".$num."</b></font></td></tr>";
		echo "</table>";

		echo "<br>";
		echo "<table border=0 align=center>";
		// Espacio entre las filas con los datos y los botones inferiores
		echo "<tr><td height='21'></td></tr>";
		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$fec1\",\"$fec2\",\"$serv\",\"$tipo\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";
  }
	
	echo "</form>";
}
?>
