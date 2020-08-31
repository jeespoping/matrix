<html>
<head>
  <title>STOCK POR CCO</title>
  
  <style type="text/css">
	.txtEnvio {  color: #2A5DB0; }
	.txtPequeno {  }
	.txtMedio { }
	.txtGrande {  }
	.txtMuyGrande { }
	.select { }
	.boton {  } 
	.campo { }
	.check { } 
  </style>
  
</head>

<script type="text/javascript">

// Validacion si el sistema trabaja solo bajo IPOD
function validar_browser()
 {
       var browserName=navigator.appName;
       var code=navigator.appCodeName;
       var agente=navigator.userAgent;
       
       var pos_brow = agente.indexOf("Safari");
       var pos_movi = agente.indexOf("Mobile");
       
       if (pos_brow <= 0 || pos_movi <= 0)
       {
           alert ("ESTE PROGRAMA SOLO PUEDER USUARSE DESDE LOS ** IPOD's **");
           window.close();
       }
 }

// Validación para movimientos con factor 1
function valida_cantidad(i)
  {
	var stock, existencia, cantidad;

	if(isNaN(document.getElementById('wcantidad'+i).value))
	{
		alert(document.getElementById('wcantidad'+i).value+" - no es un valor válido");
		document.getElementById('wcantidad'+i).value = "";
		document.getElementById('wcantidad'+i).focus();
		return false;
	}

	stock = parseInt(document.getElementById('wstock'+i).value);
	existencia = parseInt(document.getElementById('wexistencia'+i).value);
	cantidad = parseInt(document.getElementById('wcantidad'+i).value);
	if((cantidad+existencia)>stock) 
	{
		alert("La existencia no puede ser mayor al stock");
		document.getElementById('wcantidad'+i).value = "";
		document.getElementById('wcantidad'+i).focus();
		return false;
	}
	
	if((cantidad+existencia)<0) 
	{
		alert("La existencia no puede ser menor que cero");
		document.getElementById('wcantidad'+i).value = "";
		document.getElementById('wcantidad'+i).focus();
		return false;
	}
  
	return true;
  }

// Validación para movimientos con factor -1
function valida_existencia(i)
  {
	var stock, existencia, cantidad;

	if(isNaN(document.getElementById('wcantidad'+i).value))
	{
		alert(document.getElementById('wcantidad'+i).value+" - no es un valor válido");
		document.getElementById('wcantidad'+i).value = "";
		document.getElementById('wcantidad'+i).focus();
		return false;
	}

	stock = parseInt(document.getElementById('wstock'+i).value);
	existencia = parseInt(document.getElementById('wcantidad'+i).value);
	if(existencia>stock) 
	{
		alert("La existencia no puede ser mayor al stock");
		document.getElementById('wcantidad'+i).value = "";
		document.getElementById('wcantidad'+i).focus();
		return false;
	}
	
	if(existencia<0)
	{
		alert("La existencia no puede ser menor que cero");
		document.getElementById('wcantidad'+i).value = "";
		document.getElementById('wcantidad'+i).focus();
		return false;
	}
  
	return true;
  }
  
// Envio del formulario
function enter()
	{
		document.forms.stockcco.submit();
	}

// Envio del formulario
function enterGrabar()
	{
		document.forms.stockcco.submit();
	}

function mostrarDiferencia()
	{
		var cont = 0;
		var stock, existencia;
		if(document.getElementById('diferencia').checked == true)
		{
			while(document.getElementById('wcantidad'+cont))
			{
				if(document.getElementById('wcantidad'+cont).value=="" || document.getElementById('wcantidad'+cont).value==" ")
				{
					stock = parseInt(document.getElementById('wstock'+cont).value);
					existencia = parseInt(document.getElementById('wexistencia'+cont).value);
					document.getElementById('wcantidad'+cont).value = stock - existencia;
				}
				cont++;
			}
		}
		else if(document.getElementById('diferencia').checked == false)
		{
			while(document.getElementById('wcantidad'+cont))
			{
				stock = parseInt(document.getElementById('wstock'+cont).value);
				existencia = parseInt(document.getElementById('wexistencia'+cont).value);
				if(document.getElementById('wcantidad'+cont).value == stock - existencia)
				{
					document.getElementById('wcantidad'+cont).value = "";
				}
				cont++;
			}
		}
	}

function mostrarExistencia()
	{
		var cont = 0;
		var existencia;
		if(document.getElementById('diferencia').checked == true)
		{
			while(document.getElementById('wcantidad'+cont))
			{
				if(document.getElementById('wcantidad'+cont).value=="" || document.getElementById('wcantidad'+cont).value==" ")
				{
					existencia = parseInt(document.getElementById('wexistencia'+cont).value);
					document.getElementById('wcantidad'+cont).value = existencia;
				}
				cont++;
			}
		}
		else if(document.getElementById('diferencia').checked == false)
		{
			while(document.getElementById('wcantidad'+cont))
			{
				existencia = parseInt(document.getElementById('wexistencia'+cont).value);
				if(document.getElementById('wcantidad'+cont).value == existencia)
				{
					document.getElementById('wcantidad'+cont).value = "";
				}
				cont++;
			}
		}
	}

function existe_cambios(cant_inic)
{
	var existe = false;
	var valores = cant_inic.split("-");
	var i = 0;
	while(document.getElementById('wcantidad'+i))
	{
		if(document.getElementById('wcantidad'+i).value!=valores[i])
		{
			existe = true;
		}
		i++;
	}

	return existe;
}

// Oculta el mensaje de datos actualizados
function ocultar_msj()
	{
		div = document.getElementById('msjActualiza');
		div.style.display='none';
	}

// Devuelve tru si la varable está declarada
function isset(variable_name) {
    try {
         if (typeof(eval(variable_name)) != 'undefined')
         if (eval(variable_name) != null)
         return true;
     } catch(e) { }
    return false;
   }

// Cambio de ronda
function enter_ronda()
	{
		document.getElementById('envio').value='0';
		document.forms.stockcco.submit();
	}

// Vuelve a la página anterior llevando sus parámetros
function retornar(wemp_pmla,f,cant_inic)
	{
		//alert(cant_inic);
		if(cant_inic)
		{
			if(existe_cambios(cant_inic))
			{
				if(!confirm("Se perderán los últimos cambios realizados. ¿Realmente desea salir?"))
					return false;
			}
		}
		location.href = "stock_cco.php?wemp_pmla="+wemp_pmla+"&f="+f;
	}
	
// Cierra la ventana
function cerrar_ventana(cant_inic)
	{
		//alert(cant_inic);
		if(cant_inic)
		{
			if(existe_cambios(cant_inic))
			{
				if(!confirm("Se perderán los últimos cambios realizados. ¿Realmente desea salir?"))
					return false;
			}
		}
		window.close();
    }
     
</script>
<body>

<?php
include_once("conex.php");
  /**************************************************************
   * 	  STOCK DE PRENDAS POR CADA PISO						*
   * 	   DESPACHO DE PRENDAS A PISOS							*
   * ------------------------------------------------------		*
   * Este script permite realizar dos procesos relacionados 	*
   * con el stock de prendas en cada piso. Estos procesos son: 	*
   * 1. Conteo de prendas por piso que se realiza con IPOD 		*
   * 2. Despacho de prendas a los pisos también mediante IPOD	*
   **************************************************************/
	/*
	 * Autor: John M. Cadavid. G.
	 * Fecha creacion: 2011-04-30
	 * Modificado: 
	 * Aca se ponen los comentarios de las modificaciones del programa
	 */
   
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
 
  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " 1 | Abr. 30 de 2011";
                                                   
  echo "<br>";				
  echo "<br>";

  //**********************************************//
  //********** F U N C I O N E S *****************//
  //**********************************************//

  // Muestra los días del mes como opciones de un select
	function menu_dia($diainp="",$mes,$anio,$diaini,$diafin)
	{
	  // obtiene los días del mes
	  if($diafin=="")
		$diafin = date("d",mktime(0,0,0,$mes+1,0,$anio)); 
	  
	  if($diainp != "")
		$dia = (int) $diainp;
	  else
		$dia = date("j");
		
	  for($i=$diaini;$i<=$diafin;$i++)
	  {
	    $ii = $i;
		  
		if($i == $dia)
			echo "<option value='".$anio."-".$mes."-".$ii."' selected>".mes_texto($mes)." $ii</option>";
		else
			echo "<option value='".$anio."-".$mes."-".$ii."'>".mes_texto($mes)." $ii</option>";
	  }
	}
  
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
  
  // Define si la hora actual se encuentra entre los rangos de horas
  // asignados en las rondas. Se basa en tabla 000102
  function rango_hora ()
	{
		global $conex;
		global $whora;
		global $wbasedato;
		global $codigo_concepto;
		global $ronda;
		global $codigo_ronda;
		global $orden_ronda;
		global $orden_ronda_select;
		
		$rango = false;

		// Seleccion de ronda atoumaticamente
		$q = " SELECT Roncod, Ronhin, Ronhfi, Ronnom, Roncon, Ronord "
			."   FROM ".$wbasedato."_000102 "
			."  WHERE Roncon = '".$codigo_concepto."'"
			."    AND Ronest = 'on' "; 
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res); 
		
		//echo $whora." - ".$q;
		
		for($i=0;$i<$num;$i++) 
		{
			$row = mysql_fetch_array($res);
			if($whora>$row['Ronhin'] && $whora<$row['Ronhfi'])
			  {
				$rango = true;
				$ronda = $row['Ronnom'];
				$codigo_ronda = $row['Roncod'];
				$orden_ronda = $row['Ronord'];
				$orden_ronda_select = $row['Ronord'];	// Solo para usar en el select de recorridos ya que la variable anterior cambia y en el select se necesita el orden según la hora que el que da esta función
			  }
		}

		return $rango;
	}

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtengo los datos de la empresa
  datos_empresa($wemp_pmla);
  
  // Se define el formulario principal de la página
  echo "<form name='stockcco' action='stock_cco.php' method=post>";
  
  // Asignación de fecha y hora actual
  if(isset($wfecmov) && $wfecmov!="")
	$wfecha = $wfecmov;
  else
	$wfecha = date("Y-m-d");

  if(isset($whormov) && $whormov!="")
	$whora = $whormov;
  else
	$whora  = (string)date("H:i:s");

  if($f=='e')
	$factor = '1';
  elseif($f=='s')
	$factor = '-1';
  else
    $factor = '0';
  
  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='f' value='".$f."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";
  

	// Obtengo los datos del concepto actual
	$qcon =  " SELECT Concod, Condes, Conpes, Tcocod, Tconom, Tcoafe, Tcofac, Ronnom "
			." 	 FROM ".$wbasedato."_000100, ".$wbasedato."_000101, ".$wbasedato."_000102 "
			." 	WHERE Rontip = 'Interna'"
			." 	  AND Ronhin <= '".$whora."' "
			." 	  AND Ronhfi > '".$whora."' "
			." 	  AND Ronest = 'on' "
			." 	  AND Roncon = Concod "
			." 	  AND Conest = 'on' "
			." 	  AND Contco = Tcocod "
			." 	  AND Tcofac = '".$factor."' "
			." 	  AND Tcoest = 'on'"; 
	$rescon = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
	$rowcon = mysql_fetch_array($rescon);
	
	// Defino las variables principales del programa
	$codigo_tipo_concepto = $rowcon['Tcocod'];
	$tipo_concepto = $rowcon['Tconom'];
	$concepto = $rowcon['Condes'];
	$codigo_concepto = $rowcon['Concod'];
	$afecta_cantidad = $rowcon['Tcoafe'];
	$factor = $rowcon['Tcofac'];
	$requiere_peso = $rowcon['Conpes'];
	
	// Validación para que solo deje abrir la página desde IPODs
	echo '<script type="text/javascript"> validar_browser(); </script>';

	// Parametro de estilos para IPOD
	$estilo = "Ipods";
	
	// Obtener titulo de la página con base en el concepto
	$titulo = $concepto;
	
    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");  


  // Si no se ha enviado datos muestre el formulario de selección de cco
  if (!isset($wcco))
    {

	  // Consulta de los centros de costos
	  echo "<table align='center'>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND ".$wbasedato."_000011.ccohos = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
		
	  // Campos select de centros de costos
	  echo "<tr class=fila1><td align='center' class='txtGrande".$estilo."'> &nbsp; Seleccione el servicio:  &nbsp; </td></tr>";
	  echo "</table>";
	  echo "<br>";
	  echo "<table align='center'>";
	  echo "<tr><td align=center><select name='wcco' class='select".$estilo."' onchange='enter()'>";
	  echo "<option>&nbsp</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table><br><br>";

	  // Botón Cerrar Ventana
	  echo "<table align='center'>";
	  echo "<tr><td align='center'><input type=button value='Cerrar Ventana' class='boton".$estilo."' onclick='cerrar_ventana()'></td></tr>";
	  echo "</table>";	  
	} 
	else	// ACA INICIA LA IMPRESION DEL STOCK POR EL CCO SELECCIONADO
     {

		// Obtengo código del cco
		$cco = explode("-",$wcco);
		$wcco_cod = $cco['0'];

		// Si se ha enviado el formulario de grabación
		if (isset($envio) && $envio=='1')
		{
			if(isset($wrondasel) && $wrondasel!='')
			{
				$strRonda = explode(' - ',$wrondasel); 
				$wronda = $strRonda['0'];
			}
			
			$i=0;
			// Variables que contendrá el mensaje con los resultados de la grabación
			$txtEnvio = "";

			// Ciclo de grabación de datos en las tablas de stock por cco (000104)
			// y en la tabla de movimientos (000105)
			do 
			{
				// Defino las variables a usar en la grabación
				$prenda = $HTTP_POST_VARS['wprenda'.$i];
				$stock = $HTTP_POST_VARS['wstock'.$i];
				$cantidad = $HTTP_POST_VARS['wcantidad'.$i];
				$existencia = $HTTP_POST_VARS['wexistencia'.$i];

				if($existencia=='') $existencia = 0;
				if($cantidad=='') $cantidad = 0;
				
				// Si el concepto actual afecta cantidad
				if($afecta_cantidad=='on')
				{
					if($factor=='1')
					{
						$calculo_existencia = $existencia + $cantidad;
					}
					elseif($factor=='-1')
					{
						$calculo_existencia = $cantidad;
					}
					else
					{
						$calculo_existencia = $existencia;
					}
						
					// Se ingresan los datos del stock en la tabla 104
					$q = "	UPDATE ".$wbasedato."_000104 
							   SET Stoexi = ".$calculo_existencia."
							 WHERE Stopre = '".$HTTP_POST_VARS['wprenda'.$i]."' 
							   AND Stocco = '".$wcco_cod."' ";
						
					$err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					if($factor=='1')
					{
						// Se actualiza Existencia disponible (Preedi) en  tabla 103
						$q = "	UPDATE ".$wbasedato."_000103 
								   SET Preedi = Preedi-".$cantidad."
								 WHERE Precod = '".$HTTP_POST_VARS['wprenda'.$i]."' ";
					}
					
					$err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					// Se ingresan los datos del movimiento en la tabla 105
					$q = "INSERT INTO ".$wbasedato."_000105
									  (Medico, Fecha_data, Hora_data, Movfec, Movhor, Movpre, Movcco, Movron, Movcan, Movexi, Movsto, Movest, Seguridad)
							   VALUES 
									  ('Movhos', '$wfecha', '$whora', '$wfecmov', '$whormov','".$HTTP_POST_VARS['wprenda'.$i]."', '$wcco_cod', '".$wronda."', ".$cantidad.", ".$existencia.", ".$HTTP_POST_VARS['wstock'.$i].", 'on', 'C-".$wseguridad."' )";
					$err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				}
				else		// Si el concepto actual no afecta cantidad
				{
					$q = "INSERT INTO ".$wbasedato."_000105
									  (Medico, Fecha_data, Hora_data, Movfec, Movhor, Movpre, Movcco, Movron, Movcan, Movexi, Movsto, Movest, Seguridad)
							   VALUES 
									  ('Movhos','$wfecha', '$whora', '$wfecmov','$whormov', '".$HTTP_POST_VARS['wprenda'.$i]."', '$wcco_cod', '".$wronda."', ".$cantidad.", ".$existencia.", ".$HTTP_POST_VARS['wstock'.$i].", 'on', 'C-".$wseguridad."' )";
					$err=mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				}
			
				$i++;
				
			} while(isset($HTTP_POST_VARS['wexistencia'.$i]));
			
			// Si el mensaje de resultado de grabación no tiene ninguna advertencia
			if($txtEnvio=="")
				$txtEnvio = "Se han actualizado los datos correctamente";

			// Se imprime el mensaje con los resultados de la grabación
			echo "<div align='center' id='msjActualiza' class='txtEnvio".$estilo."'><b>".$txtEnvio."</b></div><br><br>";
		}
		
		// Si el rango de hora se encuentra entre las rondas definidas 
		// muestre el formulario de movimiento de stock
		if(rango_hora())
		{
			if(isset($wrondasel) && $wrondasel!='')
			{
				$strRonda = explode(' - ',$wrondasel);
				$ronda = $strRonda['1'];
				$codigo_ronda = $strRonda['0'];

				// Query para obtener la ronda actual
				$qron =  " SELECT Roncod, Ronnom, Ronord, Ronhin, Ronhfi "
						."   FROM ".$wbasedato."_000102 "
						."  WHERE Roncod = '".$codigo_ronda."' "
						."	  AND Ronest = 'on' "; 
				$resron = mysql_query($qron,$conex) or die  ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
				$rowron = mysql_fetch_array($resron);
				
				$orden_ronda = $rowron['Ronord'];

				echo "<input type='hidden' name='wrondasel' value='".$wrondasel."'>";
			}

		    echo "<input type='hidden' name='wcco' value='".$wcco."'>";
		    echo "<input type='hidden' name='wronda' value='".$codigo_ronda."'>";
			$strCantInic = '';
			
			// Query para obtener el stock del centro de costos seleccionado
			$q = " SELECT Stopre, Stocco, Stosto, Stoexi, Precod, Predes, Prepes "
				."   FROM ".$wbasedato."_000104, ".$wbasedato."_000103 "
				."  WHERE Stocco = '".$wcco_cod."'"
				."    AND Stoest = 'on' "
				."    AND Stopre = Precod " 
				."    AND Preest = 'on' "
				."  ORDER BY Precod, Predes  "; 
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			// Query para saber si ya se ha actualizado los datos
			// del servicio en la ronda actual y por el concepto actual
			$qact = "  SELECT a.Fecha_data, a.Hora_data, Movpre, Movcco, Movron, Movcan, Movexi, Movsto, Roncon, Movfec, Movhor "
					."   FROM ".$wbasedato."_000105 a, ".$wbasedato."_000102 b "
					."  WHERE a.Fecha_data = '".$wfecha."' "
					."    AND Movcco = '".$wcco_cod."' " 
					."    AND Movron = '".$codigo_ronda."' " 
					."    AND Movron = Roncod " 
					."    AND Roncon = '".$codigo_concepto."' "
					."    AND Ronest = 'on' "; 
			$resact = mysql_query($qact,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qact." - ".mysql_error());
			$rowact = mysql_fetch_array($resact);
			$wactualizar = mysql_num_rows($resact);

			// Bloqueo de campos si ya se han realizado movimientos
			// por otros conceptos
			if($factor=='1')
				$factor_diferente = '-1';
			elseif($factor=='-1')
				$factor_diferente = '1';
			else
				$factor_diferente = '0';
			$estado = '';
			$mostrar = 'on';
			$msjBloqueo = "";

			if($wactualizar>0 && (!isset($envio) || $envio=='0'))
			{
				// Seleccion de ronda de otros conceptos
				$qotr = "  SELECT  enc.Hora_data hora, Tcofac "
						."   FROM ".$wbasedato."_000105 enc, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100 "
						."  WHERE Tcofac = '".$factor_diferente."'"
						."    AND Tcoest = 'on' "
						."    AND Tcocod = Contco "
						."    AND Conest = 'on' "
						."    AND Concod = Roncon "
						."    AND Rontip = 'Interna' "
						."    AND Ronest = 'on' "
						."    AND Movron = Roncod " 
						."    AND Movcco = '".$wcco_cod."' "
						."    AND Ronhin < '".$whora."' " 
						."    AND Ronhfi > '".$whora."' "
						."	  AND enc.Fecha_data = '".$wfecha."' "
						."    AND Movest = 'on' "; 
				$resotr = mysql_query($qotr,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qotr." - ".mysql_error());
				$numotr = mysql_num_rows($resotr); 
				$rowotr = mysql_fetch_array($resotr);

				if($numotr>0 && $rowotr['hora']>$rowact['Movhor'])
				{
					$estado = " readonly='readonly'";
					$mostrar = "off";
					// Se imprime el mensaje avisando por que no se pueden efectuar cambios
					if($msjBloqueo=="")
						$msjBloqueo = "<div align='center' class='txtPequeno".$estilo."' style='color:#2A5DB0;'><b>Estos valores no se pueden cambiar debido a que ya se han realizado otros movimientos con base en éste</b></div><br><br>";
				}
			}

			// Bloqueo de campos si ya se han actualizado movimientos posteriores
			// Seleccion de ronda de otros conceptos
			$qpos = "  SELECT  enc.Hora_data hora, Tcofac "
					."   FROM ".$wbasedato."_000105 enc, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100 "
					."  WHERE Tcofac = '".$factor."'"
					."    AND Tcoest = 'on' "
					."    AND Tcocod = Contco "
					."    AND Conest = 'on' "
					."    AND Concod = Roncon "
					."    AND Rontip = 'Interna' "
					."    AND Ronest = 'on' "
					."    AND Movron = Roncod " 
					."	  AND Concod = '".$codigo_concepto."' "
					."    AND Movcco = '".$wcco_cod."' "
					."    AND Ronord > ".$orden_ronda." " 
					."	  AND enc.Fecha_data = '".$wfecha."' "
					."    AND Movest = 'on' "; 
			$respos = mysql_query($qpos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qpos." - ".mysql_error());
			$numpos = mysql_num_rows($respos); 

			if($numpos>0)
			{
				$estado = " readonly='readonly'";
				$mostrar = "off";
				if($msjBloqueo=="")
					$msjBloqueo = "<div align='center' class='txtPequeno".$estilo."' style='color:#2A5DB0;'><b>Estos valores no se pueden cambiar debido a que ya se han realizado movimientos en un recorrido posterior a éste</b></div><br><br>";
				// Se imprime el mensaje avisando por que no se pueden efectuar cambios
			}

			// Establece última fecha y orden permitidos para movimientos
			$qult = "  SELECT  Movfec "
					."   FROM ".$wbasedato."_000105, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100 "
					."  WHERE Tcofac = '".$factor."'"
					."    AND Tcoest = 'on' "
					."    AND Tcocod = Contco "
					."    AND Conest = 'on' "
					."    AND Concod = Roncon "
					."    AND Rontip = 'Interna' "
					."    AND Ronest = 'on' "
					."    AND Movron = Roncod " 
					."    AND Roncon = Concod "
					."	  AND Concod = '".$codigo_concepto."' "
					."    AND Movcco = '".$wcco_cod."' "
					."    AND Movest = 'on' "
					."    ORDER BY Movfec DESC " 
					."  LIMIT 0,1 "; 
			$result = mysql_query($qult,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qult." - ".mysql_error());
			$rowult = mysql_fetch_array($result);
			$numult = mysql_num_rows($result); 
			
			if($numult>0)
			{
				echo "<input type='hidden' name='wfeclim' id='wfeclim' value='".$rowult['Movfec']."'>";
			}
			else
			{
				echo "<input type='hidden' name='wfeclim' id='wfeclim' value='".$wfecha."'>";
			}
			
		  echo $msjBloqueo;
			
		  echo "<table align='center'>";

		  // Se muestran los datos a consultar como encabezado
		  echo "<tr class='titulo'>";
		  echo "<td align='center' colspan='5' class='txtMedio".$estilo."'><b>Servicio o Unidad: ".$wcco."</b></td>";
		  echo "</tr>";
		  echo "<tr class='titulo'>";
		  echo "<td align='center' colspan='5' class='txtMedio".$estilo."'><b>Recorrido: ".$codigo_ronda." - ".$ronda."</b></td>";
		  echo "</tr>";

		  if(isset($wfecmov) && $wfecmov < date("Y-m-d"))
		  {
			  $qron = " SELECT Roncod, Ronnom, Ronord "
					 ."	  FROM ".$wbasedato."_000102 "
					 ."  WHERE Roncon = '".$codigo_concepto."'"
					 ."    AND Ronest = 'on' "; 
			  $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
			  $numron = mysql_num_rows($resron); 
		  }
		  else
		  {
			  $qron = " SELECT Roncod, Ronnom, Ronord "
					 ."	  FROM ".$wbasedato."_000102 "
					 ."  WHERE Roncon = '".$codigo_concepto."'"
					 ."    AND Ronord <= ".$orden_ronda_select." "
					 ."    AND Ronest = 'on' "; 
			  $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
			  $numron = mysql_num_rows($resron); 
		  }

		  // Solicitud de ronda, fecha y hora
		  echo "<tr height='34'>";
		  echo "<td align='center' colspan='5'>";
		  echo "<table width='100%' border='0' bordercolor='#ffffff' cellspacing='1' cellpadding='0'><tr class='fila2'><td align='left' colspan='2' class='txtPequeno".$estilo."'> &nbsp; Establecer recorrido:<br> &nbsp; ";
		  echo "<select name='wrondasel' id='wrondasel' onchange='enter_ronda()' class='select".$estilo."'> ";
		  for ($i=1;$i<=$numron;$i++)
			 {
			  $rowron = mysql_fetch_array($resron); 
			  if($codigo_ronda==$rowron[0])
				echo "<option selected>".$rowron[0]." - ".$rowron[1]."</option>";
			  else
				echo "<option>".$rowron[0]." - ".$rowron[1]."</option>";
			 }
		  echo "</select>";
		  echo "</td>";

		  // Asigno valores iniciales para fecha y hora
		  if(isset($rowact['Movfec']) && $rowact['Movfec']!="")
			$whormov = $rowact['Movhor'];
		  else
			$whormov = date("h").":".date("i").":".date("s");

  		  if(!isset($wfecmov) || $wfecmov=="")
			$wfecmov = $wfecha;

		  $wfecdia =  explode("-",$wfecmov);
		  $wdia = $wfecdia['2'];
		  
		  // Ingreso de la fecha
		  echo "<td class='txtPequeno".$estilo."' align='left' nowrap> &nbsp; Fecha<br> &nbsp; ";
		  echo "<span class='campo".$estilo."'>";
		  $fecLim = explode("-",$rowult['Movfec']);

			// Campo select para fecha
			echo "<select name='wfecmov' id='wfecmov' onchange='enter_ronda()' class='select".$estilo."'> ";
			if($fecLim['1']==date("m"))
			{
				menu_dia($wdia,date("m"),date("Y"),$fecLim['2'],date("d"));
			}
			else
			{
				if($fecLim['1']=='12')
					menu_dia($wdia,$fecLim['1'],date("Y-1"),$fecLim['2'],date("d"));
				else
					menu_dia($wdia,$fecLim['1'],date("Y"),$fecLim['2'],"");
				menu_dia($wdia,date("m"),date("Y"),"01",date("d"));
			}
			echo "</select>";
		    echo "<input type='hidden' name='wdia' id='wdia' value='".$wdia."'>";

		  // Campo que contiene la fecha del movmiento
		  /*if(isset($rowact['Movfec']) && $rowact['Movfec']!="")
			echo " &nbsp; <input type='text' size='10' name='wfecmov2' id='wfecmov2' class='campo".$estilo."' value='".$wfecmov."' readonly='readonly'>";
		  */

			echo "</span>";
		  echo " &nbsp; </td><td class='txtPequeno".$estilo."' align='left' nowrap> &nbsp; Hora:<br> &nbsp; <input type='text' size='8' name='whormov' id='whormov' class='campo".$estilo."' value='".$whormov."'".$estado."> &nbsp;&nbsp; </td></tr></table>";
		  echo "</td></tr>";
		  
		  // Encabezado de la tabla de stock para el cco
		  echo "<tr class=encabezadoTabla>";
		  echo "<th class='txtPequeno".$estilo."' width='70%'>Prenda</th>";
		  echo "<th class='txtPequeno".$estilo."' width='10%'>&nbsp;Stock&nbsp;</th>";
		  if($factor=='1')
		  {
		    echo "<th class='txtPequeno".$estilo."' width='10%'>&nbsp;Existencia&nbsp;</th>";
		    echo "<th class='txtPequeno".$estilo."' width='10%'>&nbsp;Pendiente entrega&nbsp;</th>";
			echo "<th class='txtPequeno".$estilo."' width='10%' nowrap>&nbsp;<input type='checkbox' name='diferencia' id='diferencia' onclick='javascript:mostrarDiferencia();' class='check".$estilo."'>Entregados&nbsp;</th>";
		  }
		  else
		  {
		    echo "<th class='txtPequeno".$estilo."' width='10%' nowrap>&nbsp;Existencia&nbsp;</th>";
		  }
		  echo "</tr>";

			// Ciclo para mostrar el stock de cada cco
			for($i=0;$i<$num;$i++) 
			{
				$row = mysql_fetch_array($res);
				$existencia_inicial = $row['Stoexi'];

				// Definición del estilo para las filas
				if (is_integer($i/2))
				  $wclass="fila1";
				else
				  $wclass="fila2";
				
				// Declaro la variable que mostrará la cantidad inicial
				$cantidad_inicial = "";
				
				// Consulto si habia un registro previo de esta prenda en esta ronda
				$qcan =  " SELECT Movcan, Movexi "
						."   FROM ".$wbasedato."_000105 mov, ".$wbasedato."_000102, ".$wbasedato."_000101 "
						."  WHERE mov.Fecha_data = '".$wfecha."' "
						."	  AND Movpre = '".$row['Precod']."' "
						."    AND Movcco = '".$wcco_cod."' " 
						."    AND Movron = '".$codigo_ronda."' " 
						."    AND Movest = 'on' "
						."    AND Movron = Roncod " 
						."    AND Roncon = Concod "
						."  ORDER BY mov.Hora_data DESC, mov.id DESC, mov.Fecha_data DESC"; 
				$rescan = mysql_query($qcan,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcan." - ".mysql_error());
				$numcan = mysql_num_rows($rescan);
				$rowcan = mysql_fetch_array($rescan);

				if($numcan>0)
				{
					$cantidad_inicial = $rowcan['Movcan'];
					$existencia_inicial = $rowcan['Movexi'];
				}

			   if($factor=='-1')
				   // Espacio entre filas
				   echo "<tr><td align='center' colspan='5'>&nbsp;</td></tr>";

			   // Filas donde se muestran los datos del stock del cco
			   echo "<tr class=".$wclass.">";
			   echo "<td align='left' class='txtPequeno".$estilo."'>&nbsp;".$row['Precod']." - ".$row['Predes']."</td>";
			   echo "<td align=center class='txtPequeno".$estilo."'>".$row['Stosto']."</td>";
			   if($factor=='1')
			   {
				   $pendiente = $row['Stosto']-$row['Stoexi'];
				   echo "<td align=center class='txtPequeno".$estilo."'>".$existencia_inicial."</td>";
				   echo "<td align=center class='txtPequeno".$estilo."'>".$pendiente."</td>";
				   echo "<td align=center class='txtPequeno".$estilo."'><input type='text' name='wcantidad".$i."' id='wcantidad".$i."' onKeyDown='ocultar_msj()' size='11' onblur='valida_cantidad(".$i.")' class='campo".$estilo."' value='".$cantidad_inicial."'".$estado."></td>";
				   echo "<input type='hidden' name='wexistencia".$i."' id='wexistencia".$i."' value='".$existencia_inicial."'>";
			   }
			   else 
			   {
				   echo "<td align=center class='txtPequeno".$estilo."'><input type='text' name='wcantidad".$i."' id='wcantidad".$i."' onKeyDown='ocultar_msj()' size='11' onblur='valida_existencia(".$i.")' class='campo".$estilo."' value='".$cantidad_inicial."'".$estado."></td>";
				   echo "<input type='hidden' name='wexistencia".$i."' id='wexistencia".$i."' value='".$existencia_inicial."'>";
			   }
				
			   // Asigno datos a la cadena de cantidad inicial 
			   // para validación al retornar o cerrar ventana
			   $strCantInic .= $cantidad_inicial."-";
				
			   echo "</tr>";

			   // Se definen variables a usar por cada stock en el envio del formulario
			   echo "<input type='hidden' name='wprenda".$i."' id='wprenda".$i."' value='".$row['Precod']."'>";
			   echo "<input type='hidden' name='wstock".$i."' id='wstock".$i."' value='".$row['Stosto']."'>";
		    }
		    echo "<input type='hidden' name='envio' id='envio' value='1'>";

			// Espacio entre filas
			echo "<tr><td align='center' colspan='5' height='37'>&nbsp;</td></tr>";

			// Defino el colspan según el factor
		    if($factor=='-1') 
			{
				$colspan1 = '2';
				$colspan2 = '1';
			}
		    else
			{
				$colspan1 = '4';
				$colspan2 = '1';
			}
			// Botones Retornar, Cerrar Ventana y Grabar
		    echo "<tr><td align='left' colspan='".$colspan1."'>";
		    echo "<input type=button value='Retornar' class='boton".$estilo."' onclick='retornar(\"$wemp_pmla\",\"$f\",\"$strCantInic\")'> &nbsp; &nbsp; &nbsp; &nbsp; <input type=button value='Cerrar Ventana' class='boton".$estilo."' onclick='cerrar_ventana(\"$strCantInic\")'></td><td align='right' colspan='".$colspan2."'>";
		    echo "<input type='button' name='grabar' value='Grabar' onclick='enterGrabar()' class='boton".$estilo."'>";
			echo "</td></tr>";
		    echo "</table>";	  

		} 
		else		// Si el rango de hora no se encuentra entre las rondas definidas 
		{
			// Muestra mensaje de rango de hora no corresponde con rondas existentes
			echo "<br><br>";
		    echo "<table align='center'>";
		    echo "<tr><td align='center' class='txtMedio".$estilo."'><b>La hora actual no corresponde con ninguna ronda existente para realizar la operación</b><br><br><br></td></tr>";
			
		    // Botones Retornar y Cerrar Ventana
			echo "<tr><td align='center'><input type=button value='Retornar' class='boton".$estilo."' onclick='retornar(\"$wemp_pmla\",\"$f\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()' class='boton".$estilo."'></td></tr>";
		    echo "</table>";	  
		}
      }	          
	   
  echo "<br>";
  echo "<input type='hidden' name='wvalidacion' id='wvalidacion' value='".$factor."'>";
  echo "</form>";
  
} 

?>
</body>
</html>