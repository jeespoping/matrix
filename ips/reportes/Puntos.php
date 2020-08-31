<html>
<head>
	<title>Reporte de tarjeta de puntos</title>

	<script type="text/javascript">

	// Vuelve a la página anterior llevando sus parámetros
	function retornar(wemp_pmla,wvalor,wtipo)
		{
			location.href = "Puntos.php?wemp_pmla="+wemp_pmla+"&wvalor="+wvalor+"&wtipo="+wtipo+"&bandera=1";
		}
		
	// Cierra la ventana
	function cerrar_ventana(cant_inic)
		{
			window.close();
		}
		 
	</script>
</head>
<body >
<body>

<?php
include_once("conex.php");

/********************************************************
*     REPORTE DE TARJETA DE PUNTOS						*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de tarjeta de puntos 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:MARZO 2006
//FECHA ULTIMA ACTUALIZACION 	:07 de Marzo de 2006
//DESCRIPCION					:
//								 
//==================================================================================================================================
//---------------------------------------------MODIFICACIONES-----------------------------------------------------------------------
// 2011-11-10 - Adaptación de hojas del estilos al css actual de Matrix. Uso de parámetro wemp_pmla para definir la empresa y el 
//				prefijo de la base de datos - Mario Cadavid
//==================================================================================================================================

   
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
  

  
  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 
  echo "<br>";				
  echo "<br>";

  
  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtengo los datos de la empresa y de la base de datos
  $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
  $wbasedato = $institucion->baseDeDatos;
  $winstitucion = $institucion->nombre;

  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " Nov. 10 de 2011";
                                                   
  // Obtener titulo de la página con base en el concepto
  $titulo = " REPORTE DE TARJETA DE PUNTOS ";
	
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

  echo "<form name='form_puntos' action='' method=post>";

  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";
	
  if (!isset($envio))
    {
		//Parámetros de consulta del informe
		if (!isset ($bandera))
		{  			
			$wvalor="";
			$wtipo="";
		}
		else
		{
			$wvalor=$wvalor;
			$wtipo=$wtipo;
		}

		echo "<table align='center' cellspacing='2' width='350'>";

		//Petición de valor a buscar
		echo "<tr>";
		echo "<td align='enter' height='27' colspan='2'>";
		echo "<div align='left'> <b>Ingrese los parámetros a consultar</b> </div>";
		echo "</td>";
		echo "</tr>";
		echo "<tr class=fila2>";
		echo "<td align='enter' colspan='2'>";
		echo "<div align='center'><input type='text' name='wvalor' size='30' value='$wvalor' maxlength='30'></div>";
		echo "</td>";
		echo "</tr>";

		$ced_check = "";
		$tar_check = "";
		$nom_check = "";
		$tel_check = "";
		$all_check = "";

		if($wtipo=='ced')
			$ced_check = " checked";
		elseif($wtipo=='tarpu')
			$tar_check = " checked";
		elseif($wtipo=='nom')
			$nom_check = " checked";
		elseif($wtipo=='tel')
			$tel_check = " checked";
		else
			$all_check = " checked";
		
		echo "<tr class=fila2>";
		echo "<td width='50%' height='11'><input type='radio' name='wtipo' value='ced'".$ced_check."> Cédula</td>";
		echo "<td width='50%' height='11'><input type='radio' name='wtipo' value='tarpu'".$tar_check."> Número de tarjeta</td>";
		echo "</tr>";
		echo "<tr class=fila2>";
		echo "<td width='50%' height='11'><input type='radio' name='wtipo' value='nom'".$nom_check."> Nombre</td>";
		echo "<td width='50%' height='11'><input type='radio' name='wtipo' value='tel'".$tel_check."> Teléfono</td>";
		echo "</tr>";
		echo "<tr class=fila2>";
		echo "<td height='21' colspan='2'><input type='radio' name='wtipo' value='all'".$all_check."> Todos los clientes</td>";
		echo "</tr>";

		echo "</table>";	  

		// Botones Aceptar y Cerrar Ventana
		echo "<br><table align='center'>";
		echo "<tr><td align='center'><input type='submit' value='Consultar'> &nbsp; &nbsp; &nbsp; &nbsp; <input type='button' value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";	  

		echo "<input type='hidden' name='envio' id='envio' value='1'>";
	  
    } 
	else	// ACA INICIA LA IMPRESION DEL INFORME SEGUN LOS PARAMETROS DE CONSULTA
	{
		////////////////////////////////////////////////////////////////////////////////
		if ($wtipo=='ced')
		{
			$query = "	SELECT Clidoc, Clinom, Climai, Clite1, Clipun, Salcau, Salred, Saldev, Salsal 
						  FROM ".$wbasedato."_000041,".$wbasedato."_000060 
						 WHERE Clidoc like'".$wvalor."'
						   AND Saldto=Clidoc";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numero = mysql_num_rows($err);
		}
		else if ($wtipo=='tarpu')
		{
			$query = "	SELECT Clidoc, Clinom, Climai, Clite1, Clipun, Salcau, Salred, Saldev, Salsal 
						  FROM ".$wbasedato."_000041,".$wbasedato."_000060 
						 WHERE Clipun like'".$wvalor."'
						   AND Saldto=Clidoc";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numero = mysql_num_rows($err);
		}
		else if ($wtipo=='tel')
		{
			$query = "	SELECT Clidoc, Clinom, Climai, Clite1, Clipun, Salcau, Salred, Saldev, Salsal 
						  FROM ".$wbasedato."_000041,".$wbasedato."_000060 
						 WHERE Clite1 like '".$wvalor."'
						   AND Saldto=Clidoc";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numero = mysql_num_rows($err);
		}
		else if ($wtipo=='nom')
		{
			$query = "	SELECT Clidoc, Clinom, Climai, Clite1, Clipun, Salcau, Salred, Saldev, Salsal 
						  FROM ".$wbasedato."_000041,".$wbasedato."_000060 
						 WHERE Clinom like '%".$wvalor."%'
						   AND Saldto=Clidoc";
			$err = mysql_query($query,$conex);
			$numero = mysql_num_rows($err);
		}
		
		///////////// IMPRESION ///////////////////////////////////////////////////////////
		
		echo "<table align='center' cellspacing='2'>";
		echo "<tr class=fila2>";
		echo "<td align='center' height='27px'><b> &nbsp; Tipo de búsqueda: ";
		if($wtipo=='ced')
			echo "por cédula";
		elseif($wtipo=='tarpu')
			echo "por número de tarjeta";
		elseif($wtipo=='nom')
			echo "por nombre";
		elseif($wtipo=='tel')
			echo "por teléfono";
		else
			echo "todos los clientes";
		echo " &nbsp; </b>";
        echo "</td>";
		echo "</tr>";
		if($wtipo!="all" && $wvalor!="" && $wvalor!=" ")
		{
			echo "<tr class=fila2>";
			echo "<td align='center' height='27px'><b> &nbsp; Valor a buscar: ";
			echo $wvalor." &nbsp; ";
			echo "</b></td>";
			echo "</tr>";
		}
		echo "<tr><td align='center' height='11px'> &nbsp; </td></tr>";
		echo "</table>";
		
		// Tabla principal con los datos del informe
		echo "<table align='center' cellspacing='2'>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='9'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wvalor\",\"$wtipo\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";

		// Espacio entre los botones superiores y las filas con los datos
		echo "<tr><td colspan='9' height='21'>&nbsp;</td></tr>";
		
		// Encabezado de la tabla
		echo "<tr class='encabezadoTabla'>
				<td>Cédula</td>
				<td>Nombre</td>
				<td>E-mail</td>
				<td>Teléfono</td>
				<td>Número de Tarjeta</td>
				<td>Puntos Causados</td>
				<td>Puntos Redimidos</td>
				<td>Puntos Devueltos</td>
				<td>Puntos Acumulados</td>
			</tr>";

		if ($wtipo=='nom' or $wtipo=='ced'or $wtipo=='tarpu' or $wtipo=='tel')
		{
			
			for ($i=0;$i<$numero;$i++)
				{
					if (is_int ($i/2))
						$wcf="fila2";
					else
						$wcf="fila1";
					
					$datgr = mysql_fetch_row($err);
					echo "<tr class=".$wcf.">
							<td align=left>$datgr[0]</td>
							<td align=left>$datgr[1]</td>
							<td align=left>$datgr[2]</td>
							<td align=left>$datgr[3]</td>
							<td align=left>$datgr[4]</td>
							<td align=right>$datgr[5]</td>
							<td align=right>$datgr[6]</td>
							<td align=right>$datgr[7]</td>
							<td align=right>$datgr[8]</td>
						  </tr>";
				}
			echo "<tr class='encabezadoTabla'>
					<td colspan=4>TOTAL CLIENTES</td>
					<td colspan=5 align=right>$numero</td>
				  </tr>";
		}
		else if ($wtipo=='all'){
			$query = "	SELECT *
						  FROM ".$wbasedato."_000041, ".$wbasedato."_000060 
						 WHERE  Saldto=Clidoc
						  AND Clipun<>'' and Clipun<>'000000'";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($err);
			
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					if (is_int ($i/2))
						$wcf="fila2";
					else
						$wcf="fila1";
					
					$datgr = mysql_fetch_array($err);
					echo "<tr class=".$wcf.">
							<td align=left>$datgr[Clidoc]</td>
							<td align=left class=".$wcf.">$datgr[Clinom]</td>
							<td align=left class=".$wcf.">$datgr[Climai]</td>
							<td align=left class=".$wcf.">$datgr[Clite1]</td>
							<td align=left class=".$wcf.">$datgr[Clipun]</td>
							<td align=right class=".$wcf.">$datgr[Salcau]</td>
							<td align=right class=".$wcf.">$datgr[Salred]</td>
							<td align=right class=".$wcf.">$datgr[Saldev]</td>
							<td align=right class=".$wcf.">$datgr[Salsal]</td>
						  </tr>";

				}
			}
			echo "<tr class='encabezadoTabla'>
					<td colspan=4>TOTAL CLIENTES</td>
					<td colspan=5 td align=right>$num</td>
				  </tr>";
		}
		// Espacio entre las filas con los datos y los botones inferiores
		echo "<tr><td colspan='9' height='21'>&nbsp;</td></tr>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='9'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wvalor\",\"$wtipo\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";	  
	}	          
	   
  echo "<br>";
  echo "</form>";
}
?>	
</body>
</html>