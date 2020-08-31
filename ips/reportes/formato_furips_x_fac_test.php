<?php
include_once("conex.php"); header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<?php

function ejecutar_consulta_furips ($query_campos, $query_from, $query_where, $llaves, $conexUnix, $order = " ")
{
	global $conexUnix;
	global $increment;
	
	$increment++;
	$aleatorio = rand(1, 1000);
	if ($query_where == NULL)              
		$query =   " SELECT $query_campos FROM $query_from ";
	else
		$query =   " SELECT $query_campos FROM $query_from WHERE $query_where";

	$table= date("Mdhis").$aleatorio.$increment;                //nombre de la tabla temporal	
	$query=$query." into temp $table";              //creo la temporal con los resultados de la consulta que enviaron
	odbc_do($conexUnix,$query);
	
	$query1= "select * from $table";
	$err_o1 = odbc_do($conexUnix,$query1); // Consulto la tabla temporal
	
	$p=0;  //ooojjjjjjjooooooo temporal
	while (odbc_fetch_row($err_o1)) //RECORRO CADA REGISTRO DE LA TEMPORAL 
	{
		$n_llaves=count($llaves); 
		for ($x=0; $x<$n_llaves ;$x++)
		{
			$pk_valor[$x]=odbc_result($err_o1, $llaves[$x]); 
			$pk_nombre[$x]=odbc_field_name($err_o1, $llaves[$x]);
		}
		for($i=1;$i<=odbc_num_fields($err_o1);$i++)
		{
			$campo=odbc_field_name($err_o1,$i);
			$valor=odbc_result($err_o1,$i);
			validar_nulos_furips($query_from, $query_where, $campo, $valor, $pk_nombre, $pk_valor, $conexUnix, &$table);//ESTA FUNCION ACTUALIZA EL VALOR DEL CAMPO DE LA TEMPORAL, DEPENDIENDO DEL VALOR DE LA ORGINAL
		}
		$p++;
	}
	$query1="select * from $table $order";
	$err_o1 = odbc_do($conexUnix,$query1); // retornar la consulta sin null
	return $err_o1; 
}

function validar_nulos_furips($query_from, $query_where, $campo, $valor, $pk_nombre, $pk_valor, $conexUnix, &$table)
{
	if ($query_where == NULL)
	{
		$query_no_null = " SELECT $campo FROM $query_from"; 
		$query_no_null = $query_no_null." Where $campo is not null  ";
	} 
	else
	{
		$query_no_null = " SELECT $campo FROM $query_from WHERE $query_where";
		$query_no_null = $query_no_null." AND ($campo is not null or $campo != '')  ";
	}
	
	for($y=0; $y<count($pk_valor); $y++ )
	{
		$query_no_null = $query_no_null." AND ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
	}
	//echo $query_no_null;
	$res_no_null = odbc_do($conexUnix, $query_no_null);
	
	
	if (odbc_fetch_row($res_no_null))
	{	
		$valor_no_null = odbc_result($res_no_null, 1);
		$query4="update $table ";
		$query4=$query4."set $campo = '$valor_no_null'  ";
		$query4=$query4." WHERE ";   
		for($y=0; $y<count($pk_valor); $y++ )
			{
				if ($y==0)
					$query4=$query4." ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
				else
					$query4=$query4." AND ".$pk_nombre[$y]." = '".$pk_valor[$y]."' ";
			}
		$err_o4 = odbc_do($conexUnix,$query4);
	}
	 
}


function crear_archivo($filename,$content,$cont)
{
	if($cont==1)
	{
	  if (file_exists($filename))
		unlink($filename);
	  $modo1 = 'w';
	  $modo2 = 'a';
	}
	else
	{
	  $modo1 = 'w+';
	  $modo2 = 'a';
	}
	
	if (!file_exists($filename))
		$reffichero = fopen($filename, $modo1);

	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) 
	{

		// In our example we're opening $filename in append mode.
		// The file pointer is at the bottom of the file hence
		// that's where $content will go when we fwrite() it.
		if (!$handle = fopen($filename, $modo2)) 
		{
			 //echo "Cannot open file ($filename)";
			 exit;
		}

		// Write $content to our opened file.
		if (fwrite($handle, $content) === FALSE) 
		{
			//echo "Cannot write to file ($filename)";
			exit;
		}

		//echo "Success, wrote ($content) to file ($filename)";

		fclose($handle);

	} 
	else 
	{
		//echo "The file $filename is not writable";
	}
}

function obtener_consecutivo($archivo)
{
	// $archivo contiene el numero que actualizamos
	$contador = 0;

	//Abrimos el archivo y leemos su contenido
	$fp = fopen($archivo,"r"); 
	$contador = fgets($fp, 26); 
	fclose($fp);

	//Incrementamos el contador
	++$contador;

	//Actualizamos el archivo con el nuevo valor
	$fp = fopen($archivo,"w+"); 
	fwrite($fp, $contador, 26); 
	fclose($fp); 

	return $contador;
}

function obtener_nombre_archivo($codHabilitacion,$cont)
{
	$nombre = "FURIPS1".$codHabilitacion.date('dmY')."-".$cont.".txt";
	return $nombre;
}

function obtener_nombre_archivo2($codHabilitacion,$cont)
{
	$nombre = "FURIPS2".$codHabilitacion.date('dmY')."-".$cont.".txt";
	return $nombre;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>FURIPS</title>
  <script language="JavaScript">
	function Abrir_ventana (pagina) {
		var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=670, height=470, top=20, left=40";
		window.open(pagina,"",opciones);
	}
  </script>
  <style type="text/css">
	html, body, table, td {
		font-family: Arial;
		font-size: 7pt;
		width: 640px;
	} 
	table, td {
		font-family: Arial;
		font-size: 6.5pt;
	}
	.parte {
		width: 580px;
		text-align: right;
		font-size: 6pt;
	}
	.resolucion {
		position: inherit;
		float: right;
		right: 1px;
		font-size: 6pt;
	}
	.encabezado {
		font-size: 6.5pt;
		font-weight: bold;
	} 
	.tituloSeccion
	{
		font-size: 6.5pt;
		font-weight: bold;
		border-top: 1px solid rgb(51, 51, 51);
		border-bottom: 1px solid rgb(51, 51, 51);
		text-align:center;
	} 
	.descripcion
	{
		font-size: 5.5pt;
		text-align:justify;
	} 
 	.total
	{
		font-size: 6.5pt;
		height: 27px;
		text-align:right;
		text-valign:bottom;
	} 

	/* Campos para valores mostrados */
	.campoFecha
	{
		width: 80px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
	.campoCodigo
	{
		width: 140px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
	.campoChecked
	{
		width: 20;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
	.campoNombre
	{
		width: 281px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
	.campoTexto
	{
		width: 470px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
	.campoCod
	{
		width: 21px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 

	.campoCta
	{
		width: 121px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
	.campoCta1
	{
		width: 71px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
	.campoCta2
	{
		width: 210px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
	.campoCta3
	{
		width: 270px;
		font-size: 7pt;
		text-align: center;
		border-bottom: 1px solid #000000;
		padding: 0 0 0 4px;
	} 
  </style>
</head>


<body>

<?php
  /******************************************************************
   * 	  FORMULARIO �NICO DE RECLAMACI�N IPS - FURIPS				*
   * -------------------------------------------------------------- *
   * Este script genera el Formulario �nico de Reclamaci�n de los 	*
   * Prestadores de Servicios de Salud por Servicios Prestados a	*
   * V�ctimas de Eventos Catastr�ficos y Accidentes de Tr�nsito 	*
   * (Personas Jur�dicas - FURIPS) 									*
   * Los datos son consultados desde UNIX conectando a la Base de 	*
   * Datos de Servinte.												*
   * Las fuentes, tam�os, dem�s estilos y la distribuaci�n de los	*
   * formularios, se hizo para que su impresi�n f�sica se pueda		*
   * hacer similar a como viene impreso el formato f�sico que antes *
   * se llenaba manualmente.										*
   ******************************************************************/
	/*
	 * Autor: John M. Cadavid. G.
	 * Fecha creacion: 2011-09-23
	 * Modificado: 
	 * 2012-10-31 - Se adicion� el filtro AND Fennac != '' en la Consulta de facturas de modo que se valide que la factura es de accidente
	 * y no se muestren dobles las facturas que estan repetidas en la tabla $wbasedato_farm_000018 - Mario Cadavid
	 * 2012-06-05 - Se adicion� condicionales para mostrar el nombre del conductor de modo que si es un nombre como Maria del Carmen, Pedro de las Casas
	 * no vaya a tomar "del" o "de las" como el segundo nombre o apellido - Mario Cadavid
	 * 2012-05-02 - Se pasa el documento del paciente asociado a la factura ($empresa_000018.Fendpa) a may�cula con strtoupper
	 * para asegurar la correcta comparaci�n del query en Unix
	 * 2012-04-27 - Se cambia el campo que obtiene el dato del medico, ya no se trae de la tabla inmegr.egrmed sino de
	 * inpacinf.pacinfmed, ya que este es el m�dico tratante - Mario Cadavid
	 * 2012-02-27 - Se modific� el condicional donde se obtienen los datos del m�dico tratante ya que estaba dentro del condicional
	 * de los datos del propietario del veh�culo - Mario Cadavid
	 * 2012-01-19 - Se permite modificar el campo de fecha de ingreso del paciente en el formato furips
	 * petici�n realizada por Sandra de farmastore - Mario Cadavid
	 * 2012-01-03 - Se permite modificar el campo de fecha de ingreso del paciente, petici�n realizada por Sandra de farmastore - Mario Cadavid
	 * 2011-12-13 - Se agreg� la funci�n define_muncod y se defini� la variable $Ctid que determina 
	 * el tipo de documento del conductor. Se adicion� el uso de la funci�n establecer_geo siempre
	 * que se va a mostrar departamento y municipio de un registro - Mario Cadavid
	 * 2011-12-02 - Se le asign� valor incial a la variable $estado_aseguramiento de modo
	 * que si la variable $Aase no trae dato v�lido no se genere warning de variable no definida - Mario Cadavid
	 * 2011-11-24 - El nombre de la seguradora estatal estaba quemado por lo que 
	 * se cambia para que consulte en root_000051 la empresa actual encargada - Mario Cadavid
	 * 2012-01-31 - Se cambio la asignaci�n $Amuncod=$array['accdetmuc'] y $Cmun=$array['accdetmun'], ya que estaban trocados 
	 * estaba asignado el codigo de municipio del conductor como codigo de municipio de ocurrencia del accidente y vicerversa
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

	// Permite separar el c�digo del municipio cuando tiene dentro el del departamento
	function define_muncod($munic,$depar)
	{
		for ($i=0;$i<strlen($depar);$i++)
		{
			if($depar[$i]==$munic[$i])
				$munic[$i] = NULL;
		} 		
		return $munic;
	}

	function establecer_geo($muncod)
	{
		$datosGeo = array();
		global $conexUnix;
		global $long;
		
		//Busco datos del municipio
		$select = " muncod, '".$long."' as munnom, depcod, '".$long."' as depnom ";
		$from =   " inmun, indep ";
		$where =  " muncod='".trim($muncod)."' AND mundep=depcod";
		//$err_o_geo = odbc_exec($conexUnix,$query_geo);

		unset($llaves);
		$llaves[0]=1;
		 
		$err_o_geo = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );
		
		if (odbc_fetch_row($err_o_geo))
		{
			for($i=1;$i<=odbc_num_fields($err_o_geo);$i++)
			{
				$array[odbc_field_name($err_o_geo,$i)]=odbc_result($err_o_geo,$i);
			}
			if($array['munnom'])
			{
				$datosGeo['Mnombre']=$array['munnom'];
				$datosGeo['Mcod']=define_muncod(trim($array['muncod']),trim($array['depcod']));
				$datosGeo['Dnombre']=$array['depnom'];
				$datosGeo['Dcod']=$array['depcod'];
			}
			else
			{
				$datosGeo['Mnombre']='';
				$datosGeo['Mcod']='';
				$datosGeo['Dnombre']='';
				$datosGeo['Dcod']='';
			}
		}
		else
		{
				$datosGeo['Mnombre']='';
				$datosGeo['Mcod']='';
				$datosGeo['Dnombre']='';
				$datosGeo['Dcod']='';
		}
		return $datosGeo;
	}
	
	function establecer_dpto($dpto)
	{
		global $conexUnix;
		global $long;
		
		//Busco datos del municipio
		$select = " depcod, '".$long."' as depnom ";
		$from =   " indep ";
		$where =  " depcod='".trim($dpto)."'";
		//$err_o_geo = odbc_exec($conexUnix,$query_geo);

		unset($llaves);
		$llaves[0]=1;
		 
		$err_o_geo = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );
		
		if (odbc_fetch_row($err_o_geo))
		{
			for($i=1;$i<=odbc_num_fields($err_o_geo);$i++)
			{
				$array[odbc_field_name($err_o_geo,$i)]=odbc_result($err_o_geo,$i);
			}
			if($array['depnom'])
			{
				$nomdpto=$array['depnom'];
				return $nomdpto;
			}
			else
			{
				return '';
			}
		}
	}
	
	function establecer_mun($mun,$dep)
	{
		global $conexUnix;
		global $long;
		
		//Busco datos del municipio
		$select = " muncod, '".$long."' as munnom ";
		$from =   " inmun ";
		$where =  " muncod='".trim($dep).trim($mun)."'";
		//$err_o_geo = odbc_exec($conexUnix,$query_geo);

		unset($llaves);
		$llaves[0]=1;
		 
		$err_o_geo = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );
		
		if (odbc_fetch_row($err_o_geo))
		{
			for($i=1;$i<=odbc_num_fields($err_o_geo);$i++)
			{
				$array[odbc_field_name($err_o_geo,$i)]=odbc_result($err_o_geo,$i);
			}
			if($array['munnom'])
			{
				$nommun=$array['munnom'];
				return $nommun;
			}
			else
			{
				return '';
			}
		}
	}

	session_start();

// Inicia la sessi�n del usuario
if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
// Si el usuario no est� registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
	echo "error";
else	// Si el usuario est� registrado inicia el programa
{	            
  include_once("root/comun.php");
  include_once("root/montoescrito.php");

  $conex = obtenerConexionBD("matrix");
  
  datos_empresa($wemp_pmla);
  $titulo = "FURIPS";
  
  // Obtengo los datos de la empresa y de la base de datos
  $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
  $wbasedato_farm = $institucion->baseDeDatos;
  $winstitucion = $institucion->nombre;

  conexionOdbc($conex, $wbasedato, &$conexUnix, 'facturacion');
  
  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 
  // Aca se coloca la ultima fecha de actualizaci�n
  $wactualiz = " Octubre 31 de 2012";

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Titulo de la p�gina
  $titulo = "FURIPS";

  $furips = "";
  $tbpac = "";
  $contsist = 0;
  $cont = 1;

  $increment = 1;
	
  //Busco registro de accidentes entre las fechas seleccionadas
  /*
  $query =   "SELECT accdethis, accdetnum, accdetced, accdetfec, accdetfin, accdetffi, accdetacc "
			."  FROM inaccdet "
			." WHERE accdetfec BETWEEN '$wfecha_ini' "
			."	 AND '$wfecha_fin' "
			." ORDER BY accdetfec ASC ";
  $err_acc = odbc_exec($conexUnix,$query);
  */


// Consulto las facturas
if(isset($wgrupo) && $wgrupo=="1")
{
  $q =   " SELECT Fendpa, Fenfac, Fennac "
	    ."   FROM ".$wbasedato_farm."_000018 "
	    ." 	WHERE Fendpa = '".$wnumero."' "
	    ."	  AND Fendpa != '' ";
//	    ."	  AND Fennac != '' ";	// Se comenta porque al incluir eventos catastr�ficos, este campo puede ir vacio
}
else
{
  $q =   " SELECT Fendpa, Fenfac, Fennac "
	    ."   FROM ".$wbasedato_farm."_000018 "
	    ." 	WHERE Fenfac = '".$wnumero."' "
	    ."	  AND Fendpa != '' ";
//	    ."	  AND Fennac != '' ";	// Se comenta porque al incluir eventos catastr�ficos, este campo puede ir vacio
}

$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

if($conexUnix)
{
// Ciclo que recorre las facturas y muestra el formato furips para cada una  

///25, espacios para que no se limite el tama�o de los campos en el update que se hace a la temporal
$long='                                                                                              ';

// Variable que se activar� en caso de que el accidente sea un evento catastr�fico
$catastrofico = false;
$conttemp = 0;	// Contador para agregar en el nombre de la tabla temporal y garantizar que no se repita

while($row = mysql_fetch_array($res))
{
  // Incremento el contador para agregar al nombre de la tabla temporal
  $conttemp++;

  $factura = $row['Fenfac'];
  $ide = strtoupper($row['Fendpa']);
  $acc = $row['Fennac'];
  if($ide=='' || $ide==' ')
	$ide = -1;

	// Consulta de datos Unix
    $query="SELECT pacced 
			  FROM inpac
			 WHERE pacced='".trim($ide)."'
			   AND pacced is not null
			 UNION
			SELECT ' ' 
			  FROM inpac
			 WHERE pacced='".trim($ide)."'
			   AND pacced is null ";
	$err_o = odbc_do($conexUnix,$query);

	if (odbc_fetch_row($err_o))
	{
		$tbpac = "inpac";
	}
	else
	{
		$query="SELECT pacced 
				  FROM inpaci
				 WHERE pacced='".trim($ide)."'
				   AND pacced is not null
				 UNION
				SELECT ' '  
				  FROM inpaci
				 WHERE pacced='".trim($ide)."'
			   AND pacced is null ";
		$err_1 = odbc_do($conexUnix,$query);
		if (odbc_fetch_row($err_1))
		{
			$tbpac = "inpaci";
		}
		else
		{
			$tbpac = "";
		}
	}	
  
  if(isset($tbpac) && $tbpac!="")  
  {
	
	// Si vienes dado el accidente se consulta los registros de �ste	
	if(isset($acc) && $acc!="" && $acc!=" " && $acc!="0")
	{
		$select = " accdethis, accdetnum, '".$long."' as accdetfec, accdetacc ";
		$from =	  " inaccdet, $tbpac ";
		$where =  "     pacced = '".trim($ide)."' "
				 ." AND accdethis = pachis "
				 ." AND accdetacc = '".trim($acc)."' ";
		$order = " ORDER BY 3 DESC ";

		unset($llaves);
		$llaves[0]=1;
		$llaves[1]=4;
		 
		$err_acc = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
	}
	// Si no viene dado el accidente se busca el �ltimo
	else
	{
		$select = " accdethis, accdetnum, '".$long."' as accdetfec, accdetacc ";
		$from =	  " inaccdet, $tbpac ";
		$where =  "     pacced = '".trim($ide)."' "
				 ." AND accdethis = pachis ";
		$order = " ORDER BY 3 DESC ";

		unset($llaves);
		$llaves[0]=1;
		 
		$err_acc = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
	}
  //$err_acc = odbc_exec($conexUnix,$query);

  // Si no se encuentran datos consultando por accidente
  // Consulto si es un evento catastr�fico
  if(odbc_fetch_row($err_acc))
  {
	$his = odbc_result($err_acc,1);
	$ing = odbc_result($err_acc,2);
	$fec = odbc_result($err_acc,3);
	$acc = odbc_result($err_acc,4);
  }
  else
  {
	$select = " pacevchis, pacevcnum, '".$long."' as evcfec ";
	$from =	  " inpacevc, inevc, $tbpac ";
	$where =  "     pacced = '".trim($ide)."' "
			 ." AND pacevchis = pachis "
			 ." AND pacevcevc = evccod ";
		$order = " ORDER BY 2 DESC ";

	unset($llaves);
	$llaves[0]=1;
	 
	$err_acc = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );
	
	// Activo el indicador de evento catastr�fico
	$catastrofico = true;
	
	odbc_fetch_row($err_acc);

	$his = odbc_result($err_acc,1);
	$ing = odbc_result($err_acc,2);
	$fec = odbc_result($err_acc,3);
	$acc = '';
  }

  // Consulto datos de la factura
  $q =   "SELECT Venvto-Vendes AS Facturado, Fenfac, Fenfec, Vennum, Fennit "
		."  FROM ".$wbasedato_farm."_000016, ".$wbasedato_farm."_000018 "
		." WHERE Fenfac = '".$factura."' "
		."   AND Fenfac = Vennfa "
		."   AND Fenffa = Venffa ";
  $res_fact = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
  $row_fact = mysql_fetch_array($res_fact);
  $num=mysql_num_rows($res_fact);

  $num_factura = $row_fact['Fenfac'];
  $ctaNum = $row_fact['Vennum'];
  $nitEmpresa = $row_fact['Fennit'];
  if(isset($row_fact['Facturado']) && $row_fact['Facturado']!=NULL)
	$facturado = $row_fact['Facturado'];
  else
	$facturado = 0;

  $facturadoTex = ucwords(strtolower(montoescrito($facturado)));

	// Consulto c�digo actual de la aseguradora estatal seg�n root_000051
	$q =   "SELECT Detval "
		."    FROM root_000051 "
		."   WHERE Detemp = '".$wemp_pmla."' "
		."     AND Detapl = 'aseguradoraEstatal' ";
	$res_ase_est = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$row_ase_est = mysql_fetch_array($res_ase_est);
	$ase_est = explode("-",$row_ase_est['Detval']);
	// Consulto el NIT de la aseguradora estatal
	$nit_ase_est = $ase_est[0];
	// Consulto el nombre de la aseguradora estatal
	$nom_ase_est = $ase_est[1];

  // Consulto datos de la empresa
  $q =   "SELECT Empcod, Empnit, Empnom "
		."  FROM ".$wbasedato_farm."_000024 "
		." WHERE Empnit = '".$nitEmpresa."' "
		."   AND Empest = 'on' ";
  $res_emp = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
  $row_emp = mysql_fetch_array($res_emp);
  $codigo_emp = $row_emp['Empcod'];
  $nit_emp = $row_emp['Empnit'];
  $nombre_emp = $row_emp['Empnom'];

  if($num>0)
  {
	
	if($tbpac!="")
	{
		
		// Defino el nombre de la tabla temporal
		$randomnum = rand(1, 1000);
		$temptable = "tmp_".date("his").$randomnum.$conttemp;                //nombre de la tabla temporal	
				
		// Si el indicador de evento catastr�fico no est� activo se conulta normalmente como accidente
		if(!$catastrofico)
		{
			//Busco detalles de accidente
			$query = " ( SELECT accdethis, accdetnum, accdethor, accdetmun, accdetlug,  accdetasn, accdetpol, accdetmar, accdetpla, accdettip, accdetnom, accdetced, accdetdir, accdettel, accdetfin, accdetffi, accdetob1, accdetob2, accdetocu, accdetzon, accdetase, accdetmuc, accdetaut, accdetacc, accdetfec, accdetres, accdetcas "
					 ."    FROM inaccdet "
					 ."   WHERE accdethis='".trim($his)."' "
					 ."     AND accdetnum = '".trim($ing)."' "
					 ."     AND accdetacc = '".trim($acc)."'"
					 ."	    AND accdetob1 is not null  "
					 ."	    AND accdetob2 is not null ) "
					 
					 ."	  UNION "
					 
					 ." ( SELECT accdethis, accdetnum, accdethor, accdetmun, accdetlug,  accdetasn, accdetpol, accdetmar, accdetpla, accdettip, accdetnom, accdetced, accdetdir, accdettel, accdetfin, accdetffi, accdetob1, ' ' accdetob2, accdetocu, accdetzon, accdetase, accdetmuc, accdetaut, accdetacc, accdetfec, accdetres, accdetcas "
					 ."     FROM inaccdet "
					 ."    WHERE accdethis='".trim($his)."' "
					 ."      AND accdetnum = '".trim($ing)."' "
					 ."      AND accdetacc = '".trim($acc)."'"
					 ."	     AND accdetob1 is not null  "
					 ."	     AND accdetob2 is null )"
					 
					 ."	   UNION "
					 
					 ." ( SELECT accdethis, accdetnum, accdethor, accdetmun, accdetlug,  accdetasn, accdetpol, accdetmar, accdetpla, accdettip, accdetnom, accdetced, accdetdir, accdettel, accdetfin, accdetffi, ' ' accdetob1, accdetob2, accdetocu, accdetzon, accdetase, accdetmuc, accdetaut, accdetacc, accdetfec, accdetres, accdetcas "
					 ."     FROM inaccdet "
					 ."    WHERE accdethis='".trim($his)."' "
					 ."      AND accdetnum = '".trim($ing)."' "
					 ."      AND accdetacc = '".trim($acc)."'"
					 ."	     AND accdetob1 is null  "
					 ."	     AND accdetob2 is not null )"

					 ."	   UNION "
					 
					 ." ( SELECT accdethis, accdetnum, accdethor, accdetmun, accdetlug,  accdetasn, accdetpol, accdetmar, accdetpla, accdettip, accdetnom, accdetced, accdetdir, accdettel, accdetfin, accdetffi, ' ' accdetob1, ' ' accdetob2, accdetocu, accdetzon, accdetase, accdetmuc, accdetaut, accdetacc, accdetfec, accdetres, accdetcas "
					 ."     FROM inaccdet "
					 ."    WHERE accdethis='".trim($his)."' "
					 ."      AND accdetnum = '".trim($ing)."' "
					 ."      AND accdetacc = '".trim($acc)."'"
					 ."	     AND accdetob1 is null  "
					 ."	     AND accdetob2 is null )"

					 ."    into temp ".$temptable." ";
			$err_o = odbc_exec($conexUnix,$query);

			
			$select = "  accdethis, accdetnum, '".$long."' as accdethor, '".$long."' as accdetmun, '".$long."' as accdetlug,  '".$long."' as accdetasn, '".$long."' as accdetpol, '".$long."' as accdetmar, '".$long."' as accdetpla, '".$long."' as accdettip, '".$long."' as accdetnom, '".$long."' as accdetced, '".$long."' as accdetdir, '".$long."' as accdettel, '".$long."' as accdetfin, '".$long."' as accdetffi, accdetob1, accdetob2, '".$long."' as accdetocu, '".$long."' as accdetzon, '".$long."' as accdetase, '".$long."' as accdetmuc, '".$long."' as accdetaut, accdetacc, '".$long."' as accdetfec, '".$long."' as accdetres, '".$long."' as accdetcas ";
			$from =   "  ".$temptable." ";
			$where =  "  accdethis='".trim($his)."' "
					 ."  AND accdetnum = '".trim($ing)."' "
					 ."  AND accdetacc = '".trim($acc)."'";
			$order = " ORDER BY 24 DESC ";

			unset($llaves);
			$llaves[0]=1;
			$llaves[1]=2;
			$llaves[2]=24;
			 
			$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );

			if (odbc_fetch_row($err_o))
			{
				for($i=1;$i<=odbc_num_fields($err_o);$i++)
				{
					$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);
				}
				$historia=$array['accdethis'];
				$ingreso=$array['accdetnum']; 
				$accidente=$array['accdetacc'];
				$fechaAccidente=$array['accdetfec'];
				$hora=$array['accdethor'];
				$Alug=$array['accdetlug'];
				$aseguradora=$array['accdetres'];
				$aseguradora_cod=$array['accdetcas'];
				$poliza=$array['accdetpol'];
				$marca=$array['accdetmar'];
				$placa=$array['accdetpla'];
				$Atipo=$array['accdettip'];
				$Cnombre=$array['accdetnom'];
				$Cdoc=$array['accdetced'];
				$Cdir=$array['accdetdir'];
				$Cmun=$array['accdetmuc'];
				$Ctel=$array['accdettel'];
				$Pinicio=$array['accdetfin'];
				$Pven=$array['accdetffi'];
				$informe1=$array['accdetob1'];
				$informe2=$array['accdetob2'];
				$ocupante=$array['accdetocu'];
				$Azon=$array['accdetzon'];
				$Aase=$array['accdetase'];
				$Amuncod=$array['accdetmun'];
				$Ainteraut=$array['accdetaut'];

				$codAsegura=$array['accdetcas'];
				$soatNum=$array['accdetpol'];
				$soatNum = str_replace(" ","",$soatNum);
				$soatNum = str_replace(trim($codAsegura),"",$soatNum);
				
				$select = " '".$long."' munnom, '".$long."' mundep, muncod ";
				$from =   " inmun ";
				$where =  " muncod = '".trim($Amuncod)."'";
				unset($llaves);
				$llaves[0]=3;
				 
				$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );

				//$err_o = odbc_exec($conexUnix,$query);
				if (odbc_fetch_row($err_o))
				{
					$Amun=odbc_result($err_o,1);
					$Amundep=odbc_result($err_o,2);
				}
				else
				{
					$Amun='';
					$Amundep='';
				}
			
				$select = " '".$long."' depnom, depcod ";
				$from =   " indep ";
				$where =  " depcod = '".trim($Amundep)."'";
				unset($llaves);
				$llaves[0]=2;
				 
				$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );

				//$err_o = odbc_exec($conexUnix,$query);
				if (odbc_fetch_row($err_o))
				{
					$Adep=odbc_result($err_o,1);
				}
				else
				{
					$Adep='';
				}
			}

			//Busco datos del propietario y del conductor
			$select =  " '".$long."' as accprono1, '".$long."' as accprono2, '".$long."' as accproap1, '".$long."' as accproap2, '".$long."' as accprotid, '".$long."' as accproide, '".$long."' as accprodir, '".$long."' as accprodep, '".$long."' as accpromun, '".$long."' as accprotel, '".$long."' as accprotic, accprohis, accproacc, '".$long."' as accpronc1, '".$long."' as accpronc2, '".$long."' as accproac1, '".$long."' as accproac2  ";
			$from =    " inaccpro ";
			$where = " accprohis='$historia' AND accproacc='$accidente' ";

			unset($llaves);
			$llaves[0]=12;
			$llaves[1]=13;
			 
			$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );
			
			if (odbc_fetch_row($err_o))
			{
				for($i=1;$i<=odbc_num_fields($err_o);$i++)
				{
					$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);
				}
				if($array['accprono1'])
				{
					$Pnombre1=$array['accprono1'];
					$Pnombre2=$array['accprono2'];
					$Papellido1=$array['accproap1'];
					$Papellido2=$array['accproap2'];
					$Ptipoide=$array['accprotid'];
					$Pidentificacion=$array['accproide'];
					$Pdireccion=$array['accprodir'];
					$Pciudad='';
					$Pciudadcod=$array['accpromun'];
					$Pdepartamento='';
					$Pdepartamentocod=$array['accprodep'];
					$Ptelefono=$array['accprotel'];
					$Ctid=$array['accprotic'];
					$Cnombre1=$array['accpronc1'];
					$Cnombre2=$array['accpronc2'];
					$Capellido1=$array['accproac1'];
					$Capellido2=$array['accproac2'];
				}
				else
				{
					$Pnombre1='';
					$Pnombre2='';
					$Papellido1='';
					$Papellido2='';
					$Ptipoide='';
					$Pidentificacion='';
					$Pdireccion='';
					$Pciudad='';
					$Pciudadcod='';
					$Pdepartamento='';
					$Pdepartamentocod='';
					$Ptelefono='';
					$Ctid='';
					$Cnombre1='';
					$Cnombre2='';
					$Capellido1='';
					$Capellido2='';
				}
			}
			else
			{
				$Pnombre1='';
				$Pnombre2='';
				$Papellido1='';
				$Papellido2='';
				$Ptipoide='';
				$Pidentificacion='';
				$Pdireccion='';
				$Pciudad='';
				$Pciudadcod='';
				$Pdepartamento='';
				$Pdepartamentocod='';
				$Ptelefono='';
				$Ctid='';
				$Cnombre1='';
				$Cnombre2='';
				$Capellido1='';
				$Capellido2='';
			}
		}
		// Si no, el indicador de evento catastr�fico est� activo, se consultan las tablas de este tipo de eventos
		else
		{
			//Busco detalles de accidente
			$query = " ( SELECT pacevchis, pacevcnum, evchor, evcmun, evcdir, evcdes, evczon, evcfec "
					 ."    FROM inpacevc, inevc "
					 ."   WHERE pacevchis ='".trim($his)."' "
					 ."     AND pacevcnum = '".trim($ing)."' "
					 ."     AND pacevcevc = evccod "
					 ."	    AND evcdes is not null  ) "
					 
					 ."	  UNION "
					 
					 ." ( SELECT pacevchis, pacevcnum, evchor, evcmun, evcdir, evcdes, evczon, evcfec "
					 ."     FROM inpacevc, inevc "
					 ."    WHERE pacevchis ='".trim($his)."' "
					 ."      AND pacevcnum = '".trim($ing)."' "
					 ."      AND pacevcevc = evccod "
					 ."	     AND evcdes is null )"

					 ."    into temp ".$temptable." ";
			$err_o = odbc_exec($conexUnix,$query);

			
			$select = "  pacevchis, pacevcnum, '".$long."' as evchor, '".$long."' as evcmun, '".$long."' as evcdir, evcdes, '".$long."' as evczon, '".$long."' as evcfec ";
			$from =   "  ".$temptable." ";
			$where =  "  pacevchis='".trim($his)."' "
					 ."  AND pacevcnum = '".trim($ing)."' ";
			$order = " ORDER BY 7 DESC ";

			unset($llaves);
			$llaves[0]=1;
			$llaves[1]=2;
			 
			$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix, $order );

			if (odbc_fetch_row($err_o))
			{
				for($i=1;$i<=odbc_num_fields($err_o);$i++)
				{
					$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);
				}
				$historia=$array['pacevchis'];
				$ingreso=$array['pacevcnum']; 
				$accidente='';
				$fechaAccidente=$array['evcfec'];
				$hora=$array['evchor'];
				$Alug=$array['evcdir'];
				$aseguradora=$nom_ase_est;
				$aseguradora_cod=$nit_ase_est;
				$poliza='';
				$marca='';
				$placa='';
				$Atipo='';
				$Cnombre='';
				$Cdoc='';
				$Cdir='';
				$Cmun='';
				$Ctel='';
				$Pinicio='';
				$Pven='';
				$informe1=$array['evcdes'];
				$informe2='';
				$ocupante='';
				$Azon=$array['evczon'];
				$Aase='';
				$Amuncod=$array['evcmun'];
				$Ainteraut='S';

				$codAsegura=$nit_ase_est;
				$soatNum='';
				$soatNum = str_replace(" ","",$soatNum);
				$soatNum = str_replace(trim($codAsegura),"",$soatNum);

				
				$select = " '".$long."' munnom, '".$long."' mundep, muncod ";
				$from =   " inmun ";
				$where =  " muncod = '".trim($Amuncod)."'";
				unset($llaves);
				$llaves[0]=3;
				 
				$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );

				//$err_o = odbc_exec($conexUnix,$query);
				if (odbc_fetch_row($err_o))
				{
					$Amun=odbc_result($err_o,1);
					$Amundep=odbc_result($err_o,2);
				}
				else
				{
					$Amun='';
					$Amundep='';
				}
			
				$select = " '".$long."' depnom, depcod ";
				$from =   " indep ";
				$where =  " depcod = '".trim($Amundep)."'";
				unset($llaves);
				$llaves[0]=2;
				 
				$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );

				//$err_o = odbc_exec($conexUnix,$query);
				if (odbc_fetch_row($err_o))
				{
					$Adep=odbc_result($err_o,1);
				}
				else
				{
					$Adep='';
				}
			}

			$Pnombre1='';
			$Pnombre2='';
			$Papellido1='';
			$Papellido2='';
			$Ptipoide='';
			$Pidentificacion='';
			$Pdireccion='';
			$Pciudad='';
			$Pciudadcod='';
			$Pdepartamento='';
			$Pdepartamentocod='';
			$Ptelefono='';
			$Ctid='';
			$Cnombre1='';
			$Cnombre2='';
			$Capellido1='';
			$Capellido2='';
		}
		
		//busco m�ximo ingreso
		/*
		$query="SELECT max(accnum) FROM inacc";
		$query= $query." WHERE acchis='$historia' AND accacc='$accidente'";
		$err_o = odbc_exec($conexUnix,$query);
		$maximoingreso=odbc_result($err_o,1);
		*/

		//Busco si todav�a est� hospitalizado est� en inpac
		$select= " '".$long."' as pacced, '".$long."' as pactid, '".$long."' as pacap1, '".$long."' as pacap2, '".$long."' as pacnom, '".$long."' as pacsex, '".$long."' as paclug, '".$long."' as pacnac, '".$long."' as pacdir, '".$long."' as pactel, '".$long."' as pacmun, '".$long."' as pachor, '".$long."' as pacdin, '".$long."' as pacfec, '".$long."' as pacmed, pachis, pacnum ";
		$from =  " inpac ";
		$where = " pachis='".trim($historia)."' ";
				//." AND pacnum='".trim($ingreso)."' "; --2012/08/17 ya que no busca en inpac bien
		
		unset($llaves);
		$llaves[0]=16;
		$llaves[1]=17;
		 
		$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );

		//$err_o = odbc_exec($conexUnix,$query);
		
		// Si aun est� hospitalizado consulto los datos generales del paciente en la tabla inpac
		if (odbc_fetch_row($err_o))
		{
			for($i=1;$i<=odbc_num_fields($err_o);$i++)
			{
				$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);
			}
			$doc=$array['pacced'];
			$tipo=$array['pactid'];
			$apellido1=$array['pacap1'];
			$apellido2=$array['pacap2'];
			$nombre=$array['pacnom'];
			$sexo=$array['pacsex'];
			$lugar=$Amun;
			$depmto=$Adep;
			$fechanac=$array['pacnac'];
			$direccion=$array['pacdir'];
			$telefono=$array['pactel'];
			$municipio=$array['pacmun'];
			$medico=$array['pacmed'];
			$fechaing=$array['pacfec'];
			$horaing=$array['pachor'];
			$diaing=$array['pacdin'];
			$diadef='';
			$fechaegr='';
			$horaegr='';

			$idePac=$array['pacced'];
			$tipPac=$array['pactid'];
			$hisPac=$historia;
			$paciente = $array['pacnom'];
			$paciente .= " ".$array['pacap1'];
			$paciente .= " ".$array['pacap2'];
			$ingFec=$array['pacfec'];
			$horaing=$array['pachor'];
			$egrFec='';
			$horaegr='';
			$diasEst=$ingFec-$egrFec;
			$fecha=date("d/m/Y");
		}
		// Si no est� hospitalizado consulto los datos generales del paciente en las tablas inpaci, inpacinf e inmegr
		else
		{

			$conttemp++;
			
			// Defino el nombre de la tabla temporal
			$randomnum = rand(1, 1000);
			$temptableegr = "tmp_".date("his").$randomnum.$conttemp;                //nombre de la tabla temporal	
					
			$query= "(SELECT egring, egrhoi, egregr,  egrhoe, egrdia, egrhis, egrnum, egrdin "
					."  FROM inmegr "
					." WHERE egrhis='".trim($historia)."' "
					."   AND egrnum='".trim($ingreso)."' "
					."   AND egrdia is not null "
					."   AND egrdin is not null "
					." UNION "
					."SELECT egring, egrhoi, egregr,  egrhoe, ' ' egrdia, egrhis, egrnum, egrdin "
					."  FROM inmegr "
					." WHERE egrhis='".trim($historia)."' "
					."   AND egrnum='".trim($ingreso)."' "
					."   AND egrdia is null "
					."   AND egrdin is not null "
					." UNION "
					."SELECT egring, egrhoi, egregr,  egrhoe, egrdia, egrhis, egrnum, ' ' egrdin "
					."  FROM inmegr "
					." WHERE egrhis='".trim($historia)."' "
					."   AND egrnum='".trim($ingreso)."' "
					."   AND egrdia is not null "
					."   AND egrdin is null "
					." UNION "
					."SELECT egring, egrhoi, egregr,  egrhoe, ' ' egrdia, egrhis, egrnum, ' ' egrdin "
					."  FROM inmegr "
					." WHERE egrhis='".trim($historia)."' "
					."   AND egrnum='".trim($ingreso)."' "
					."   AND egrdia is null  "
					."   AND egrdin is null ) "
					."	into temp ".$temptableegr." ";
			$err_e = odbc_exec($conexUnix,$query);

			//si no esta en hospital Busco en inpaci datos de del paciente y en inmgr datos del ingreso
			$select= " pacced, pactid, '".$long."' as pacap1, '".$long."' as pacap2, '".$long."' as pacnom, '".$long."' as pacsex, "
					."        '".$long."' as paclug, '".$long."' as pacnac, '".$long."' as pacdir, '".$long."' as pacest, '".$long."' as pactel, "
					."        '".$long."' as pacmun, '".$long."' as pacinfmed, egring, '".$long."' as egrhoi, '".$long."' as egregr, "
					."        '".$long."' as egrhoe, '".$long."' as egrdia, egrhis, egrnum, '".$long."' as egrdin ";
			$from =  " inpaci, inpacinf, ".$temptableegr." ";
			$where = " pachis='".trim($historia)."' "
					."   AND egrhis='".trim($historia)."' "
					."   AND egrnum='".trim($ingreso)."' "
					."   AND egrhis=pacinfhis "
					."   AND egrnum=pacinfnum ";
			//$err_o = odbc_exec($conexUnix,$query);

			unset($llaves);
            $llaves[0]=19;
            $llaves[1]=20;
			 
			$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );
			
			if ($err_o)
			{

				for($i=1;$i<=odbc_num_fields($err_o);$i++)
				{
					$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);
				}
				$doc=$array['pacced'];
				$tipo=$array['pactid'];
				$apellido1=$array['pacap1'];
				$apellido2=$array['pacap2'];
				$nombre=$array['pacnom'];
				$sexo=$array['pacsex'];
				$depmto=$Adep;
				$lugar=$Amun;
				$fechanac=$array['pacnac'];
				$direccion=$array['pacdir'];
				$telefono=$array['pactel'];
				$municipio=$array['pacmun'];
				$medico=$array['pacinfmed'];
				$fechaing=$array['egring'];
				$fechaegr=$array['egregr'];
				$horaing=$array['egrhoi'];
				$horaegr=$array['egrhoe'];
				$diaing=$array['egrdia'];
				$diadef=$array['egrdin'];

				$idePac=$array['pacced'];
				$tipPac=$array['pactid'];
				$hisPac=$historia;
				$paciente = $array['pacnom'];
				$paciente .= " ".$array['pacap1'];
				$paciente .= " ".$array['pacap2'];
				$ingFec=$array['egring'];
				$egrFec=$array['egregr'];
				$horaing=$array['egrhoi'];
				$horaegr=$array['egrhoe'];
				$diasEst=$ingFec-$egrFec;
				$fecha=date("d/m/Y");

			}
			else
			{
				//no se pueden encontrar los datos de esa historia
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5>FORMULARIO DE RECLAMACI�N DE ACCIDENTES</font></a></tr></td>";
				echo "</table></br></BR></BR>";
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor='#cccccc'></td></tr>";
				echo "<form NAME='rechazo' ACTION='' METHOD='POST'>";
				echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>No existen datos de ingreso asociados a este accidente</td><tr>";
				echo "<tr><td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='submit' name='regresar2' value='ACEPTAR' ></td></tr></form>";	
				$paro =1;
			}

		}

	    // Calculo el total facturado en farpmla en el �ltimo ingreso
		/*
		$q = " SELECT SUM(Venvto)-SUM(Vendes) AS Facturado "
		  ."     FROM ".$wbasedato_farm."_000016, ".$wbasedato_farm."_000018 "
		  ."  	WHERE Fendpa = '".$doc."' "
		  ."	  AND Fenfac = Vennfa "
		  ."	  AND Fentip LIKE '%SOAT%' "
		  ."	  AND Venfec >= '".$fechaing."' ";
		$res_fact = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row_fact = mysql_fetch_array($res_fact);
		$num=mysql_num_rows($res_fact);
		*/

		  // Se comenta porque pierde el total facturado cuando alguno de los documentos de propietario, conductor y accidentado  es diferente
		  /*
		  if(isset($row_fact['Facturado']) && $row_fact['Facturado']!=NULL)
			$facturado = $row_fact['Facturado'];
		  else
			$facturado = 0;
			//*/

	
		// Consulto datos de facturaci�n en cl�nica
		/* Se comenta porque solo se tiene en cuenta el valor de la factura no el facturado en la cl�nica
		$query = "SELECT SUM(carval) "
				."  FROM cacar, famov, sifue "
				." WHERE movhis = '$historia'"
				."   AND movnum = '$ingreso'"
				."   AND movanu = '0'"
				."	 AND movfec >= '".$fechaing."'"
				."   AND carfue = movfue"
				."   AND cardoc = movdoc"
				."   AND caranu = '0'"
				."   AND movfue = fuecod"
				."   AND fueabr = 'FA'";
		$err_acc2 = odbc_exec($conexUnix,$query);
		odbc_fetch_row($err_acc2);
		$facturado_clinica = odbc_result($err_acc2,1);
		*/
		
		//$total = $facturado+$facturado_clinica;
		$total = $facturado;
		$total_facturado = $facturado;
		if($nit_emp==$nit_ase_est)
			$total_ase_est = $facturado;
		else
			$total_ase_est = 0;

	    // Obtengo los topes actuales para la aseguradora del paciente y aseguradora estatal
		$q =   " SELECT cfgcco, Cfgtas, Cfgtfo, Cfgnit, Cfgres "
			  ."   FROM ".$wbasedato_farm."_000049 "
			  ."  GROUP BY Cfgnit ";
		$res_tope = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row_tope = mysql_fetch_array($res_tope);  
		$tope_aseguradora = $row_tope['Cfgtas'];
		$tope_ase_est = $row_tope['Cfgtfo'];
		$responsable = $row_tope['Cfgres'];
			
		/*
		  if($total > $tope_aseguradora)
		  {
			$total_facturado = $total;

			  if($total > ($tope_aseguradora+$tope_ase_est))
			  {
				$total_ase_est = $tope_ase_est;
			  }
			  else
			  {
				$total_ase_est = $total - $tope_aseguradora;
			  }

		  }
		  else
		  {
			$total_facturado = $total;
			$total_ase_est = 0;
		  }
		*/
			
		//Busco datos del m�dico
		$select = " '".$long."' as medno1, '".$long."' as medno2, '".$long."' as medap1, '".$long."' as medap2, '".$long."' as mednom, '".$long."' as medtid, '".$long."' as medced, '".$long."' as medreg, medcod ";
		$from =   " inmed ";
		$where =  " medcod='".trim($medico)."' ";

		unset($llaves);
		$llaves[0]=9;
		 
		$err_o = ejecutar_consulta_furips ($select, $from, $where, $llaves, $conexUnix );

		if (odbc_fetch_row($err_o))
		{

			for($i=1;$i<=odbc_num_fields($err_o);$i++)
			{
				$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);
			}
			if($array['medno1'])
			{
				$Mednombre1=$array['medno1'];
				$Mednombre2=$array['medno2'];
				$Medapellido1=$array['medap1'];
				$Medapellido2=$array['medap2'];
				$Medtipoide=$array['medtid'];
				$Medidentificacion=$array['medced'];
				$Medregistro=$array['medreg'];
			}
			else
			{
				$Mednombre1='';
				$Mednombre2='';
				$Medapellido1='';
				$Medapellido2='';
				$Medtipoide='';
				$Medidentificacion='';
				$Medregistro='';
			}
		}
		else
		{
			$Mednombre1='';
			$Mednombre2='';
			$Medapellido1='';
			$Medapellido2='';
			$Medtipoide='';
			$Medidentificacion='';
			$Medregistro='';
		}

	if (!isset ($paro))
	{
		//se encontraron los dato se pasan a mostrar
		$query = "SELECT Cfgnit, Cfgnom, Cfgtel, Cfgdir, Cfgcpr, Cfgrip FROM ".$wbasedato_farm."_000049 GROUP BY Cfgnit";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		
		if($num >=1)
		{
			$row=mysql_fetch_array($err);
			$nit= str_replace("-","",$row['Cfgnit']);
			$direccion2=$row['Cfgdir'];
			$telefono2=$row['Cfgtel'];
			$cod_habilitacion=$row['Cfgcpr'];
			$razon_social=$row['Cfgnom'];

			$nitEmp=str_replace("-","",$row['Cfgnit']);
			$dirEmp=$row['Cfgdir'];
			$telEmp=$row['Cfgtel'];
			$nomEmp=$row['Cfgnom'];
			$ciuEmp='Medell&iacute;n';
		}

		$contsist++;

		echo '

  <!--------------------------------------------------------------------------------
  ------------------------------------ PAGINA 1 ------------------------------------
  --------------------------------------------------------------------------------->


<div style="page-break-after: always;">

<div class="parte">PARTE A</div>

<table style="border: 2px solid rgb(51, 51, 51); width: 640px;" cellpadding="2" cellspacing="2">

    <tbody>
    <tr>

      <td>
  
      <table style="border: 0pt none ; width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="width: 8%;">
			<img style="width: 44px; height: 51px;" alt="Escudo de Colombia" src="../../images/medical/ips/escudo-colombia.jpg">
		  </td>

		  <td style="text-align: center; width: 92%;" class="encabezado"><div class="resolucion">Resoluci&oacute;n 01915 &nbsp;&nbsp;&nbsp;&nbsp; 28 MAY 2008</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;REP&Uacute;BLICA DE COLOMBIA<br>
MINISTERIO DE LA PROTECCI&Oacute;N SOCIAL<br>
FORMULARIO &Uacute;NICO DE RECLAMACI&Oacute;N DE LOS PRESTADORES DE SERVICIOS DE SALUD POR SERVICIOS<br>PRESTADOS A V&Iacute;CTIMAS DE EVENTOS CATASTR&Oacute;FICOS Y ACCIDENTES DE TR&Aacute;NSITO<br>
PERSONAS JUR&Iacute;DICAS - FURIPS
		  </td>

		</tr>
   
        </tbody>
 
      </table>
      
  
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; width: 175px;">Fecha
radicaci&oacute;n
		  </td>

		  <td style="width: 160px;"><input type="text" value="" name="fecrad" id="fecrad" size="14" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /> ';

		echo		
		'&nbsp; &nbsp; &nbsp; &nbsp; RG <input type="text" value="" name="resglo" id="resglo" size="4" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" />
		  </td>

		  <td style="width: 146px;">
			No. Radicado
		  </td>

		  <td style="width: 141px; vertical-align: bottom;"><input type="text" value="" name="numrad" id="numrad" size="24" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>

		</tr>

		<tr>

		  <td style="width: 175px;">N&uacute;mero de radicado anterior
		  </td>

		  <td style="vertical-align: bottom; width: 160px;"><input type="text" value="" name="numradant" id="numradant" size="27" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" />
		  </td>

		  <td style="width: 146px; vertical-align: bottom;">No. Factura / Cuenta de cobro
		  </td>

		  <td style="width: 141px; vertical-align: bottom;"><div class="campoCodigo">'.$num_factura.'</div></td>

		</tr>
   
        </tbody>
 
      </table>

</td>

  </tr>

   
	  
  <tr>

	<td class="tituloSeccion">
	
		I. DATOS DE LA INSTITUCI&Oacute;N PRESTADORA DE SERVICIOS DE SALUD

	</td>

  </tr>

   
  <tr>

	<td>
 <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; width: 89px; white-space: nowrap;">Raz&oacute;n Social
		  </td>

		  <td colspan="3" rowspan="1" style="width: 34px;"><div class="campoTexto">'.$razon_social.'</div></td>

		</tr>

		<tr>

		  <td style="width: 89px; white-space: nowrap;">C&oacute;digo Habilitaci&oacute;n&nbsp; </td>

		  <td style="vertical-align: bottom; width: 248px;"><div class="campocodigo">'.$cod_habilitacion.'</div>
		  </td>

		  <td style="white-space: nowrap; width: 34px;">Nit
		  </td>

		  <td style="width: 251px;">
		  <div class="campocodigo">'.$nit.'</div></td>

		</tr>
   
        </tbody>
 
      </table>
	</td>

  </tr>

   
	  
  <tr>

	<td class="tituloSeccion">II. DATOS DE LA V&Iacute;CTIMA DEL EVENTO CATASTR&Oacute;FICO O ACCIDENTE DE TR&Aacute;NSITO

	</td>

  </tr>

   
  <tr>

	<td>

      
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="1" rowspan="1" style="text-align: center; width: 293px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$apellido1.'</div></td>

		  <td style="width: 40px;"></td>
         <td colspan="1" rowspan="1" style="text-align: center; width: 297px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$apellido2.'</div></td>


		</tr>

		<tr>

		  <td style="text-align: center; width: 293px; vertical-align: top;" colspan="1" rowspan="1">1er Apellido</td>


		  <td style="width: 40px;"></td>
         <td style="text-align: center; width: 297px; vertical-align: top;" colspan="1" rowspan="1">2do Apellido</td>


		</tr>
   
        </tbody>
 
      </table>

      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>';

	$nombres = explode(" ", trim($nombre));
	if(isset($nombres[0]))
		$nombre1 = $nombres[0];
	else
		$nombre1="";

	if(isset($nombres[1]))
		$nombre2 = $nombres[1];
	else
		$nombre2="";

	if(isset($nombres[2]))
		$nombre3 = " ".$nombres[2];
	else
		$nombre3="";
	
	if(isset($nombres[3]))
		$nombre4 = " ".$nombres[3];
	else
		$nombre4="";

	echo '
		  <td colspan="1" rowspan="1" style="text-align: center; width: 293px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$nombre1.'</div></td>

		  <td style="width: 40px;"></td>
         <td colspan="1" rowspan="1" style="text-align: center; width: 297px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$nombre2.''.$nombre3.''.$nombre4.'</div></td>


		</tr>

		<tr>

		  <td style="text-align: center; width: 293px; vertical-align: top;" colspan="1" rowspan="1">1er Nombre</td>


		  <td style="width: 40px;"></td>
         <td style="text-align: center; width: 297px; vertical-align: top;" colspan="1" rowspan="1">2do Nombre</td>


		</tr>
   
        </tbody>
 
      </table>

      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 135px;">Tipo Documento</td>

		  <td style="width: 177px;"><div class="campoFecha">'.$tipo.'</div></td>

		  <td style="white-space: nowrap; width: 75px;">No Documento</td>

		  <td style="width: 235px;"><div class="campoCodigo">'.$doc.'</div></td>

		</tr>

		<tr>

		  <td style="white-space: nowrap; width: 135px;">Fecha de Nacimiento</td>

		  <td style="vertical-align: bottom; width: 177px;"><div class="campoFecha">'.$fechanac.'</div></td>

		  <td style="white-space: nowrap; width: 75px;" align="right">Sexo</td>

		  <td style="width: 235px;"><div class="campoCod">'.$sexo.'</div></td>

		</tr>		
		<tr>

		  <td style="white-space: nowrap; width: 135px;">Direcci&oacute;n de Residencia</td>

		  <td style="vertical-align: bottom; white-space: nowrap;"><div class="campoNombre">'.$direccion.'</div></td>
         <td style="white-space: nowrap; width: 41px;" align="right">Tel&eacute;fono</td>
         <td style="width: 111px;"><div class="campoCodigo">'.$telefono.'</div></td>

		</tr>   
        </tbody>
 
      </table> ';

	  $geoVictima = array();
	  $geoVictima = establecer_geo(trim($municipio));
	  
      echo '
	  <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 199px;">Departamento</td>

		  <td style="width: 428px;"><div class="campoCodigo">'.$geoVictima['Dnombre'].'</div></td>

		  <td style="white-space: nowrap; width: 37px;">Cod.</td>

		  <td style="width: 59px;"><div class="campoCod">'.$geoVictima['Dcod'].'</div></td>
         <td style="width: 41px;"></td>
         <td style="width: 111px;"></td>

		</tr>
		
		<tr>

		  <td style="white-space: nowrap; width: 199px;">Municipio</td>

		  <td style="vertical-align: bottom; width: 428px;"><div class="campoCodigo">'.$geoVictima['Mnombre'].'</div></td>

		  <td style="white-space: nowrap; width: 37px;">Cod.</td>

		  <td style="width: 59px;"><div class="campoCod">'.$geoVictima['Mcod'].'</div></td>

		</tr>		';
		
	$cond_accidentado = '____ Conductor&nbsp; &nbsp; &nbsp; &nbsp;____ Peat&oacute;n&nbsp; &nbsp; &nbsp;&nbsp;____&nbsp;Ocupante&nbsp; &nbsp;&nbsp; &nbsp;____ Ciclista';
	if (trim($ocupante)=="O")
		$cond_accidentado = '____ Conductor&nbsp; &nbsp; &nbsp; &nbsp;____ Peat&oacute;n&nbsp; &nbsp; &nbsp;&nbsp;<span class="campoCod"> &nbsp; X &nbsp; </span>&nbsp;Ocupante&nbsp; &nbsp;&nbsp; &nbsp;____ Ciclista';
	if (trim($ocupante)=="C")
		$cond_accidentado = '<span class="campoCod"> &nbsp; X &nbsp; </span> Conductor&nbsp; &nbsp; &nbsp; &nbsp;____ Peat&oacute;n&nbsp; &nbsp; &nbsp;&nbsp;____&nbsp;Ocupante&nbsp; &nbsp;&nbsp; &nbsp;____ Ciclista';
	if (trim($ocupante)=="P")
		$cond_accidentado = '____ Conductor&nbsp; &nbsp; &nbsp; &nbsp;<span class="campoCod"> &nbsp; X &nbsp; </span> Peat&oacute;n&nbsp; &nbsp; &nbsp;&nbsp;____&nbsp;Ocupante&nbsp; &nbsp;&nbsp; &nbsp;____ Ciclista';
	if (trim($ocupante)=="I")
		$cond_accidentado = '____ Conductor&nbsp; &nbsp; &nbsp; &nbsp;____ Peat&oacute;n&nbsp; &nbsp; &nbsp;&nbsp;____&nbsp;Ocupante&nbsp; &nbsp;&nbsp; &nbsp;<span class="campoCod"> &nbsp; X&nbsp;  </span> Ciclista';
	
	
	echo '
		<tr>

		  <td style="white-space: nowrap; width: 199px;">Condici&oacute;n del Accidentado</td>

		  <td colspan="5" rowspan="1" style="vertical-align: bottom; width: 428px;">'.$cond_accidentado.'</td>

		</tr>   
        </tbody>
 
      </table>

  
	  </td>

    </tr>

  <tr>

	<td class="tituloSeccion">III. DATOS DEL SITIO DONDE OCURRI&Oacute;&nbsp;EL EVENTO CATASTR&Oacute;FICO O EL ACCIDENTE DE TR&Aacute;NSITO

	</td>

  </tr>

   
  <tr>

	<td>

      
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="9" rowspan="1" valign="bottom" style="text-align: left; white-space: nowrap;">Naturaleza del Evento&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Accidente de Tr&aacute;nsito &nbsp; &nbsp;<span class="campoCod">&nbsp;&nbsp;';
		  
		  if(!$catastrofico)
			echo 'X';
		  
		  echo '&nbsp;&nbsp;&nbsp;</span>
         </td>
		</tr>';

		if(!$catastrofico)
		{
			echo '<tr>         <td style="width: 106px;">Naturales</td>         <td style="white-space: nowrap; width: 91px;">Sismo</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 91px;">Maremoto</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 101px;">Erupciones Volc&aacute;nicas</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 74px;">Hurac&aacute;n</td>         <td style="width: 27px;">__</td>       </tr>       <tr>         <td style="width: 106px;"></td>         <td style="white-space: nowrap; width: 91px;">Inundaciones</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 91px;">Avanlancha</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 101px;">Deslizamiento de Tierra</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 74px;">Incendio Natural</td>         <td style="width: 27px;">__</td>       </tr>       <tr>         <td style="width: 106px;">Terroristas</td>         <td style="white-space: nowrap; width: 91px;">Explosi&oacute;n</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 91px;">Masacre</td>         <td style="width: 27px;">__</td>         <td style="width: 101px;">Mina Antipersonal</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 74px;">Combate</td>         <td style="width: 27px;">__</td>       </tr>       <tr>         <td style="width: 106px;"></td>         <td style="white-space: nowrap; width: 91px;">Incendio</td>         <td style="width: 27px;">__</td>         <td style="white-space: nowrap; width: 91px;">Ataques a Municipios</td>         <td style="width: 27px;">__</td>         <td style="width: 101px;"></td>         <td style="width: 27px;"></td>         <td style="width: 74px;"></td>         <td style="width: 27px;"></td>       </tr>       <tr>         <td style="width: 106px;">Otros &nbsp;______</td>         <td rowspan="1" colspan="8" style="white-space: nowrap;">Cual? &nbsp;____________________________________________________________________</td>       </tr>  ';
		}
		else
		{
			echo '<tr>         <td style="width: 106px;">Naturales</td>         <td style="white-space: nowrap; width: 61px;">Sismo</td>         <td style="width: 27px;"><input type="text" value="" name="sismo" id="sismo" size="1" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 91px;">Maremoto</td>         <td style="width: 27px;"><input type="text" value="" name="maremoto" id="maremoto" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 101px;">Erupciones Volc&aacute;nicas</td>         <td style="width: 27px;"><input type="text" value="" name="erupcion" id="erupcion" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 74px;">Hurac&aacute;n</td>         <td style="width: 27px;"><input type="text" value="" name="huracan" id="huracan" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>       </tr>       <tr>         <td style="width: 106px;"></td>         <td style="white-space: nowrap; width: 61px;">Inundaciones</td>         <td style="width: 27px;"><input type="text" value="" name="inundaciones" id="inundaciones" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 91px;">Avanlancha</td>         <td style="width: 27px;"><input type="text" value="" name="avalancha" id="avalancha" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 101px;">Deslizamiento de Tierra</td>         <td style="width: 27px;"><input type="text" value="" name="deslizamiento" id="deslizamiento" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 74px;">Incendio Natural</td>         <td style="width: 27px;"><input type="text" value="" name="incendio" id="incendio" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>       </tr>       <tr>         <td style="width: 106px;">Terroristas</td>         <td style="white-space: nowrap; width: 61px;">Explosi&oacute;n</td>         <td style="width: 27px;"><input type="text" value="" name="explosion" id="explosion" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 91px;">Masacre</td>         <td style="width: 27px;"><input type="text" value="" name="masacre" id="masacre" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="width: 101px;">Mina Antipersonal</td>         <td style="width: 27px;"><input type="text" value="" name="mina" id="mina" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 74px;">Combate</td>         <td style="width: 27px;"><input type="text" value="" name="combate" id="combate" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>       </tr>       <tr>         <td style="width: 106px;"></td>         <td style="white-space: nowrap; width: 61px;">Incendio</td>         <td style="width: 27px;"><input type="text" value="" name="incendio" id="incendio" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="white-space: nowrap; width: 91px;">Ataques a Municipios</td>         <td style="width: 27px;"><input type="text" value="" name="ataque" id="ataque" size="2" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>         <td style="width: 101px;"></td>         <td style="width: 27px;"></td>         <td style="width: 74px;"></td>         <td style="width: 27px;"></td>       </tr>       <tr>         <td style="width: 106px;">Otros &nbsp;______</td>         <td rowspan="1" colspan="8" style="white-space: nowrap;">Cual? &nbsp;____________________________________________________________________</td>       </tr>  ';

		}
		
       echo '</tbody>
 
      </table><table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 135px;">Direcci&oacute;n de la ocurrencia<br>         </td>

		  <td colspan="3" rowspan="1" style="width: 273px;"><div class="campoNombre">'.$Alug.'</div></td>

		  	  	</tr>

		<tr>

		  <td style="white-space: nowrap; width: 135px;">Fecha Evento / Accidente</td>

		  <td style="vertical-align: bottom; width: 177px;"><div class="campoFecha">'.$fechaAccidente.'</div></td>

		  <td style="width: 37px; white-space: nowrap;">Hora</td>

		  <td style="width: 273px;"><div class="campoFecha">'.str_replace(".",":",$hora).' hrs</div><br>




            </td>

		</tr>		
          




   
        </tbody>
 
      </table>';

	  $geoSitio = array();
	  $geoSitio = establecer_geo(trim($Amuncod));

      echo '
	  
	  <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 135px;">Departamento</td>

		  <td style="width: 233px;"><div class="campoCodigo">'.$geoSitio['Dnombre'].'</div></td>

		  <td style="white-space: nowrap; width: 40px;">Cod.</td>

		  <td style="width: 83px;"><div class="campoCod">'.$geoSitio['Dcod'].'</div></td>




            <td style="width: 41px;"></td>




            <td style="width: 75px;"></td>

		</tr>

		<tr>

		  <td style="white-space: nowrap; width: 135px;">Municipio</td>

		  <td style="vertical-align: bottom; width: 233px;"><div class="campoCodigo">'.$geoSitio['Mnombre'].'</div></td>

		  <td style="white-space: nowrap; width: 40px;">Cod.</td>

		  <td style="width: 83px;"><div class="campoCod">'.$geoSitio['Mcod'].'</div></td>
            <td style="width: 41px;">Zona</td>
            <td style="width: 75px;"><div class="campoCod">'.$Azon.'</div></td>

		</tr>		<tr>
		  <td colspan="6" rowspan="1">
		  Descripci&oacute;n Breve del Evento Catastr&oacute;fico o Accidente de Tr&aacute;nsito<br>
			Enuncia las principales caracter&iacute;sticas: '.$informe1.'. '.$informe2.' 
            </td>

		</tr>   
        </tbody>
 
      </table>

	  </td>

    </tr>

  <tr>

	<td class="tituloSeccion">IV. DATOS DEL VEH&Iacute;CULO DEL ACCIDENTE DE TR&Aacute;NSITO

	</td>

  </tr>

  ';
   
   $estado_aseguramiento = 'Asegurado&nbsp;__&nbsp;&nbsp;&nbsp; No Asegurado&nbsp;__&nbsp;&nbsp;&nbsp; Veh&iacute;culo Fantasma&nbsp;__&nbsp;&nbsp; &nbsp; P&oacute;liza Falsa __&nbsp;&nbsp;&nbsp; &nbsp; Veh&iacute;culo en Fuga&nbsp;__&nbsp;';
	if (trim($Aase)=="A")
	{	
		$estado_aseguramiento = 'Asegurado <span class="campoCod"> &nbsp; X &nbsp; </span>&nbsp; &nbsp; &nbsp;No Asegurado&nbsp;__&nbsp;&nbsp;&nbsp; Veh&iacute;culo Fantasma&nbsp;__&nbsp;&nbsp; &nbsp; P&oacute;liza Falsa __&nbsp;&nbsp;&nbsp; &nbsp; Veh&iacute;culo en Fuga&nbsp;__&nbsp;';
	}
	if (trim($Aase)=="N")
	{	
		$estado_aseguramiento = 'Asegurado __&nbsp; &nbsp; &nbsp;No Asegurado&nbsp;<span class="campoCod"> &nbsp; X &nbsp; </span>&nbsp;&nbsp;&nbsp; Veh&iacute;culo Fantasma&nbsp;__&nbsp;&nbsp; &nbsp; P&oacute;liza Falsa __&nbsp;&nbsp;&nbsp; &nbsp; Veh&iacute;culo en Fuga&nbsp;__&nbsp;';
	}
	if (trim($Aase)=="F")
	{	
		$estado_aseguramiento = 'Asegurado __&nbsp; &nbsp; &nbsp;No Asegurado&nbsp;__&nbsp;&nbsp;&nbsp; Veh&iacute;culo Fantasma&nbsp;<span class="campoCod"> &nbsp; X &nbsp; </span>&nbsp;&nbsp; &nbsp; P&oacute;liza Falsa __&nbsp;&nbsp;&nbsp; &nbsp; Veh&iacute;culo en Fuga&nbsp;__&nbsp;';
	}
	if (trim($Aase)=="L")
	{	
		$estado_aseguramiento = 'Asegurado __&nbsp; &nbsp; &nbsp;No Asegurado&nbsp;__&nbsp;&nbsp;&nbsp; Veh&iacute;culo Fantasma&nbsp;__&nbsp;&nbsp; &nbsp; P&oacute;liza Falsa <span class="campoCod"> &nbsp; X &nbsp; </span>&nbsp;&nbsp;&nbsp; &nbsp; Veh&iacute;culo en Fuga&nbsp;__&nbsp;';
	}
	if (trim($Aase)=="V")
	{	
		$estado_aseguramiento = 'Asegurado __&nbsp; &nbsp; &nbsp;No Asegurado&nbsp;__&nbsp;&nbsp;&nbsp; Veh&iacute;culo Fantasma&nbsp;__&nbsp;&nbsp; &nbsp; P&oacute;liza Falsa __&nbsp;&nbsp;&nbsp; &nbsp; Veh&iacute;culo en Fuga&nbsp;<span class="campoCod"> &nbsp; X &nbsp; </span>&nbsp;';
	}

  echo'

  <tr>

	<td>

      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="4" rowspan="1" style="text-align: left; white-space: nowrap;">Estado de Aseguramiento&nbsp;&nbsp;
&nbsp; &nbsp; '.$estado_aseguramiento.'</td>

		  

		  
	  
	</tr>

		<tr>

		  <td style="white-space: nowrap; width: 121px;">Marca</td>

		  <td style="vertical-align: bottom; width: 250px;"><div class="campoCodigo">'.$marca.'</div></td>

		  <td style="white-space: nowrap; width: 50px;">Placa</td>

		  <td style="width: 172px;"><div class="campoCodigo">'.$placa.'</div></td>

		</tr>		<tr>

		  <td style="white-space: nowrap; vertical-align: top; width: 121px;">Tipo de Servicio</td>

		  <td colspan="3" rowspan="1" style="vertical-align: bottom;"><div class="campoCodigo">'.$Atipo.'</div></td>

		</tr>   
        </tbody>
 
      </table>';

	/*
	echo '
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">
		<tbody>
          <tr>
		  <td style="text-align: left; white-space: nowrap; width: 120px;">C&oacute;digo de la Aseguradora</td>
		  <td colspan="3" rowspan="1" style="width: 87px;"><div class="campoNombre">'.$aseguradora.'</div></td>
	</tr> ';
	*/

	$intervencion_autoridad = 'Si __&nbsp; &nbsp;&nbsp; No __';
	if (trim($Ainteraut)=="S")
	{	
		$intervencion_autoridad = 'Si <span class="campoCod"> &nbsp; X &nbsp; </span>&nbsp; &nbsp;&nbsp; No __';
	}
	if (trim($Ainteraut)=="N")
	{	
		$intervencion_autoridad = 'Si __ &nbsp; &nbsp;&nbsp; No <span class="campoCod"> &nbsp; X &nbsp; </span>';
	}

	/*
	$poliza_ase = substr($poliza, 0, 6);
	$poliza_num = substr($poliza, 6, 11);
	*/
	$poliza_num = str_replace(" ","",$poliza);
	$poliza_num = str_replace(trim($aseguradora_cod),"",$poliza_num);
	
	echo '
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">
		<tbody>
          <tr>
		  <td style="text-align: left; white-space: nowrap; width: 120px;">C&oacute;digo de la Aseguradora</td>
		  <td colspan="3" rowspan="1" style="width: 87px;"><div class="campoNombre">'.$aseguradora_cod.'</div></td>
	</tr> ';
	
	echo '
		<tr>
		  <td style="white-space: nowrap; width: 120px;">No. de la Poliza</td>
		  <td style="vertical-align: bottom; width: 284px;"><div class="campoCodigo">'.$poliza_num.'</div></td>
		  <td style="white-space: nowrap; width: 131px;">Intervenci&oacute;n de Autoridad</td>
          <td style="width: 87px; white-space: nowrap;">'.$intervencion_autoridad.'</td>
		</tr>';

	/*
	echo '
		<tr>
		  <td style="white-space: nowrap; width: 120px;">No. de la Poliza</td>
		  <td style="vertical-align: bottom; width: 284px;"><div class="campoCodigo">'.$poliza.'</div></td>
		  <td style="white-space: nowrap; width: 131px;">Intervenci&oacute;n de Autoridad</td>
          <td style="width: 87px; white-space: nowrap;">'.$intervencion_autoridad.'</td>
		</tr>';
	*/
	
	if($total_ase_est>0 && trim($Aase)=="A")
		$cobro_excedente = 'Si <span class="campoCod"> &nbsp; X &nbsp; </span>&nbsp; &nbsp;&nbsp; No __';
	else
		$cobro_excedente = 'Si __&nbsp; &nbsp;&nbsp; No <span class="campoCod"> &nbsp; X &nbsp; </span>';
		
	echo '
		<tr>

            <td style="width: 120px;">Vigencia &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Desde</td>



            <td style="width: 284px;"><span class="campoFecha"> &nbsp; '.$Pinicio.' &nbsp; </span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Hasta <span class="campoFecha"> &nbsp; '.$Pven.' &nbsp; </span></td>



            <td style="width: 131px;">Cobro Excedente P&oacute;liza</td>



            <td style="width: 87px; white-space: nowrap;">'.$cobro_excedente.'</td>



          </tr>   
        </tbody>
 
      </table>


   
      
      

	  
	  </td>

    </tr>

   
	  
  <tr>

	<td class="tituloSeccion">V. DATOS DEL PROPIETARIO DEL VEH&Iacute;CULO

	</td>

  </tr>

   
  <tr>

	<td>

      
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="1" rowspan="1" style="text-align: center; width: 293px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Papellido1.'</div></td>

		  <td style="width: 40px;"></td>
         <td colspan="1" rowspan="1" style="text-align: center; width: 297px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Papellido2.'</div></td>


		</tr>

		<tr>

		  <td style="text-align: center; width: 293px; vertical-align: top;" colspan="1" rowspan="1">1er Apellido o Raz&oacute;n Social</td>


		  <td style="width: 40px;"></td>
         <td style="text-align: center; width: 297px; vertical-align: top;" colspan="1" rowspan="1">2do Apellido</td>


		</tr>
   
        </tbody>
 
      </table>


      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="1" rowspan="1" style="text-align: center; width: 293px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Pnombre1.'</div></td>

		  <td style="width: 40px;"></td>
         <td colspan="1" rowspan="1" style="text-align: center; width: 297px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Pnombre2.'</div></td>


		</tr>

		<tr>

		  <td style="text-align: center; width: 293px; vertical-align: top;" colspan="1" rowspan="1">1er Nombre</td>


		  <td style="width: 40px;"></td>
         <td style="text-align: center; width: 297px; vertical-align: top;" colspan="1" rowspan="1">2do Nombre</td>


		</tr>
   
        </tbody>
 
      </table>
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 135px;">Tipo Documento</td>

		  <td style="width: 177px;"><div class="campoFecha">'.$Ptipoide.'</div></td>

		  <td style="white-space: nowrap; width: 75px;">No. Documento</td>

		  <td style="width: 235px;"><div class="campoCodigo">'.$Pidentificacion.'</div></td>

		</tr>

			<tr>

		  <td style="white-space: nowrap; width: 135px;">Direcci&oacute;n de Residencia</td>

		  <td rowspan="1" style="vertical-align: bottom; width: 235px;"><div class="campoNombre">'.$Pdireccion.'</div></td>
         <td style="width: 48px; white-space: nowrap;" align="right">Tel&eacute;fono</td>
         <td style="width: 103px;"><div class="campoCodigo">'.$Ptelefono.'</div></td>

		</tr>   
        </tbody>
 
      </table> ';
	  
	  $geoPropietario = array();
	  $geoPropietario = establecer_geo(trim($Pdepartamentocod).trim($Pciudadcod));
	  
	  echo '
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 120px;">Departamento</td>

		  <td style="width: 248px;"><div class="campoCodigo">'.$geoPropietario['Dnombre'].'</div></td>

		  <td style="width: 30px; white-space: nowrap;">Cod.</td>

		  <td style="width: 57px;"><div class="campoCod">'.$geoPropietario['Dcod'].'</div></td>
         <td style="width: 48px;"></td>
         <td style="width: 103px;"></td>

		</tr>

		<tr>

		  <td style="white-space: nowrap; width: 120px;">Municipio</td>

		  <td style="vertical-align: bottom; width: 248px;"><div class="campoCodigo">'.$geoPropietario['Mnombre'].'</div></td>

		  <td style="width: 30px; white-space: nowrap;">Cod.</td>

		  <td style="width: 57px;"><div class="campoCod">'.$geoPropietario['Mcod'].'</div></td>
         <td style="width: 48px;"></td>
         <td style="width: 103px;"></td>

		</tr>		

        </tbody>
 
      </table>

  
	  </td>

    </tr>




</tbody>
</table>
<table style="width: 640px;" cellpadding="2" cellspacing="2">


  <tbody>

    <tr>


	<td class="total">Total Folios &nbsp;______________</td>


  </tr>
  
  
  
  
  
  </tbody>
</table>


</div>  

';


echo '

<!--------------------------------------------------------------------------------
  ------------------------------------ PAGINA 2 ------------------------------------
  --------------------------------------------------------------------------------->
<div style="page-break-after: always;">

<div class="parte">PARTE B</div>

<table style="border: 2px solid rgb(51, 51, 51); width: 640px;" cellpadding="2" cellspacing="2">

    <tbody>
    <tr>

      <td>
  
      <table style="border: 0pt none ; width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="width: 8%;">
			<img style="width: 44px; height: 51px;" alt="Escudo de Colombia" src="../../images/medical/ips/escudo-colombia.jpg">		  </td>

		  <td style="text-align: center; width: 92%;" class="encabezado"><div class="resolucion">Resoluci&oacute;n 01915 &nbsp;&nbsp;&nbsp;&nbsp; 28 MAY 2008</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;REP&Uacute;BLICA DE COLOMBIA<br>
MINISTERIO DE LA PROTECCI&Oacute;N SOCIAL<br>
FORMULARIO &Uacute;NICO DE RECLAMACI&Oacute;N DE LOS PRESTADORES DE SERVICIOS DE SALUD POR SERVICIOS<br>PRESTADOS A V&Iacute;CTIMAS DE EVENTOS CATASTR&Oacute;FICOS Y ACCIDENTES DE TR&Aacute;NSITO<br>
PERSONAS JUR&Iacute;DICAS - FURIPS
		  </td>

		</tr>
   
        </tbody>
 
      </table>
   </td>

  </tr>

   
	  
  
      
  
      
	  
  <tr>

	<td class="tituloSeccion">VI. DATOS DEL CONDUCTOR DEL VEH&Iacute;CULO INVOLUCRADO EN EL ACCIDENTE DE TR&Aacute;NSITO

	</td>

  </tr>

   
  <tr>

	<td>

    ';

    $nomb_cond = explode(' ',trim($Cnombre));
	
	// 2012-06-05	
	// Se comenta por que en la tabla inaccpro se encuentra los nombres y apellidos del conductor separados
	/*
	$Cnombre1 = "";
	$Cnombre2 = "";
	$Capellido1 = "";
	$Capellido2 = "";
	
	$icon=0;
	$Cnombre1 = $nomb_cond[0];
	$icon++;
	
	if(isset($nomb_cond[$icon]))
	{
		if(trim($nomb_cond[$icon])=="DE" || trim($nomb_cond[$icon])=="DEL" || trim($nomb_cond[$icon])=="DE LA" || trim($nomb_cond[$icon])=="DE LAS" || trim($nomb_cond[$icon])=="DE LOS")
		{
			$Cnombre2 = $nomb_cond[$icon]." ";
			$icon++;
		}
		$Cnombre2 .= $nomb_cond[$icon];
	}
	$icon++;
	
	if(isset($nomb_cond[$icon]))
	{
		if(trim($nomb_cond[$icon])=="DE" || trim($nomb_cond[$icon])=="DEL" || trim($nomb_cond[$icon])=="DE LA" || trim($nomb_cond[$icon])=="DE LAS" || trim($nomb_cond[$icon])=="DE LOS")
		{
			$Capellido1 = $nomb_cond[$icon]." ";
			$icon++;
		}
		$Capellido1 .= $nomb_cond[$icon];
	}
	$icon++;

	if(isset($nomb_cond[$icon]))
	{
		if(trim($nomb_cond[$icon])=="DE" || trim($nomb_cond[$icon])=="DEL" || trim($nomb_cond[$icon])=="DE LA" || trim($nomb_cond[$icon])=="DE LAS" || trim($nomb_cond[$icon])=="DE LOS")
		{
			$Capellido2 .= $nomb_cond[$icon]." ";
			$icon++;
		}
		$Capellido2 = $nomb_cond[$icon];
	}
	$icon++;
	*/
	
	echo '
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="1" rowspan="1" style="text-align: center; width: 293px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Capellido1.'</div></td>

		  <td style="width: 40px;"></td>
         <td colspan="1" rowspan="1" style="text-align: center; width: 297px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Capellido2.'</div></td>


		</tr>

		<tr>

		  <td style="text-align: center; width: 293px; vertical-align: top;" colspan="1" rowspan="1">1er Apellido</td>


		  <td style="width: 40px;"></td>
         <td style="text-align: center; width: 297px; vertical-align: top;" colspan="1" rowspan="1">2do Apellido</td>


		</tr>
   
        </tbody>
 
      </table>

      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="1" rowspan="1" style="text-align: center; width: 293px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Cnombre1.'</div></td>

		  <td style="width: 40px;"></td>
         <td colspan="1" rowspan="1" style="text-align: center; width: 297px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Cnombre2.'</div></td>


		</tr>

		<tr>

		  <td style="text-align: center; width: 293px; vertical-align: top;" colspan="1" rowspan="1">1er Nombre</td>


		  <td style="width: 40px;"></td>
         <td style="text-align: center; width: 297px; vertical-align: top;" colspan="1" rowspan="1">2do Nombre</td>


		</tr>
   
        </tbody>
 
      </table>

      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 129px;">Tipo Documento</td>

		  <td style="width: 167px;"><div class="campoCod">'.$Ctid.'</div></td>

		  <td style="white-space: nowrap; width: 75px;">No Documento</td>

		  <td style="width: 235px;"><div class="campoCodigo">'.$Cdoc.'</div></td>

		</tr>

		


		<tr>

		  <td style="white-space: nowrap; width: 129px;">Direcci&oacute;n de Residencia</td>

		  <td rowspan="1" style="vertical-align: bottom; white-space: nowrap; width: 167px;"><div class="campoNombre">'.$Cdir.'</div></td>
         <td style="white-space: nowrap; width: 41px; vertical-align: bottom;" align="right">Tel&eacute;fono</td>
         <td style="width: 111px;"><div class="campoFecha">'.$Ctel.'</div></td>

		</tr>   
        </tbody>
 
      </table> ';
	  
	  $geoConductor = array();
	  $geoConductor = establecer_geo(trim($Cmun));

	  echo '
	  
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 127px;">Departamento</td>

		  <td style="width: 211px; white-space: nowrap; vertical-align: bottom;"><div class="campoCodigo">'.$geoConductor['Dnombre'].'</div></td>

		  <td style="white-space: nowrap; width: 29px;">Cod.</td>

		  <td style="width: 60px; white-space: nowrap; vertical-align: bottom;"><span class="campoCod">&nbsp;'.$geoConductor['Dcod'].'&nbsp;</span></td>
         <td style="width: 41px;"></td>
         <td style="width: 111px;"></td>

		</tr>

		<tr>

		  <td style="white-space: nowrap; width: 127px;">Municipio de Residencia</td>

		  <td style="vertical-align: bottom; width: 211px;"><div class="campoCodigo">'.$geoConductor['Mnombre'].'</div></td>

		  <td style="white-space: nowrap; width: 29px;">Cod.</td>

		  <td style="width: 60px; white-space: nowrap; vertical-align: bottom;"><span class="campoCod">&nbsp;'.$geoConductor['Mcod'].'&nbsp;</span></td>
         <td style="width: 41px;"></td>
         <td style="width: 111px;"></td>

		</tr>		


   
        </tbody>
 
      </table>

  
	  </td>

    </tr>

  <tr>

	<td class="tituloSeccion">VII. DATOS DE REMISI&Oacute;N

	</td>

  </tr>

   
  <tr>

	<td>
      
      
      
      
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 127px;">Tipo de Referencia<br>         </td>

		  <td colspan="3" rowspan="1" style="width: 306px;">Remisi&oacute;n&nbsp;___ &nbsp; &nbsp; &nbsp;&nbsp; Orden de Servicio&nbsp;___</td>

		  	  	</tr>

		<tr>

		  <td style="white-space: nowrap; width: 127px;">Fecha de Remisi&oacute;n</td>

		  <td style="vertical-align: bottom; width: 128px;">__________________ </td>

		  <td style="white-space: nowrap; width: 31px;">a las</td>

		  <td style="width: 306px;">__________<br>




            </td>

		</tr>
       <tr>
         <td style="width: 127px;">Prestador que Remite</td>
         <td style="width: 306px;" colspan="3" rowspan="1">
		  __________________________________________________</td>
       </tr>
	
          <tr>
         <td style="width: 127px;">C&oacute;digo de Inscripci&oacute;n</td>
         <td style="width: 306px;" colspan="3" rowspan="1">
		  __________________________________________________</td>
       </tr>	
          <tr>
         <td style="width: 127px;">Profesional que Remite</td>
         <td style="white-space: nowrap;" colspan="3" rowspan="1">
		  ___________________________________________________&nbsp; &nbsp;&nbsp; Cargo &nbsp; &nbsp;________________</td>
       </tr>          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 127px;"></td>

		  <td colspan="3" rowspan="1" style="width: 306px;"></td>

		  	  	</tr>

		<tr>

		  <td style="white-space: nowrap; width: 127px;">Fecha de Aceptaci&oacute;n</td>

		  <td style="vertical-align: bottom; width: 128px;">__________________ </td>

		  <td style="white-space: nowrap; width: 31px;">a las</td>

		  <td style="width: 306px;">_________


            </td>

		</tr>
       <tr>
         <td style="width: 127px;">Prestador que Recibe</td>
         <td style="width: 306px;" colspan="3" rowspan="1">
		  __________________________________________________</td>
       </tr>
	
          <tr>
         <td style="width: 127px;">C&oacute;digo de Inscripci&oacute;n</td>
         <td style="width: 306px;" colspan="3" rowspan="1">
		  __________________________________________________</td>
       </tr>	
          <tr>
         <td style="width: 127px;">Profesional que Recibe</td>
         <td style="white-space: nowrap;" colspan="3" rowspan="1">
		  ___________________________________________________&nbsp; &nbsp;&nbsp; Cargo &nbsp; &nbsp;________________</td>
       </tr>
		
          




   
        </tbody>
 
      </table>
   </td>

    </tr>

  <tr>

	<td class="tituloSeccion">VIII. AMPARO DE TRANSPORTE Y MOVILIZACI&Oacute;N

	</td>

  </tr>

   
  <tr>

	<td>

      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="white-space: nowrap;" colspan="4" rowspan="1" class="descripcion">Diligenciar
&uacute;nicamente para el transporte desde el sitio del evento hasta la
primera IPS (transporte primario) y cuando se realiza en ambulancias de
la misma IPS</td>

		  

		  
	  
	</tr>

		<tr>

		  <td style="white-space: nowrap; width: 171px;">Datos del Veh&iacute;culo</td>

		  <td colspan="3" rowspan="1" style="vertical-align: bottom; white-space: nowrap;">Placa No. &nbsp;________________________________ </td>





		</tr>		<tr>         <td style="width: 171px;">Transport&oacute; la V&iacute;ctima desde</td>         <td style="width: 198px;">__________________________</td>         <td style="width: 44px;">Hasta</td>         <td style="width: 199px;">__________________________</td>       </tr>       <tr>

		  <td style="white-space: nowrap; vertical-align: top; width: 171px;">Tipo de Transporte</td>

		  <td colspan="3" rowspan="1" style="vertical-align: bottom; white-space: nowrap;">Ambulancia B&aacute;sica __&nbsp;&nbsp; Ambulancia Medicalizada __&nbsp;&nbsp; Lugar donde recoge la v&iacute;ctima __&nbsp;
&nbsp;&nbsp; Zona _<span style="text-decoration: underline;">U</span>_ &nbsp; _<span style="text-decoration: underline;">R</span>_&nbsp;</td>

		</tr>   
        </tbody>
 
      </table>   </td>

    </tr>

   
	  
  <tr>

	<td class="tituloSeccion">IX. CERTIFICACI&Oacute;N DE LA ATENCI&Oacute;N M&Eacute;DICA DE LA V&Iacute;CTIMA COMO PRUEBA DEL ACCIDENTE O EVENTO

	</td>

  </tr>

   
  <tr>

	<td>
      
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">



        <tbody>



          <tr>



            <td style="width: 85px;">Fecha de Ingreso</td>



            <td style="width: 90px;"><input type="text" value="'.$fechaing.'" name="fecent" id="fecent" size="14" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>



            <td style="width: 113px;">a las &nbsp; &nbsp;<span class="campoFecha">&nbsp;'.str_replace(".",":",$horaing).' hrs&nbsp;</span></td>



            <td style="width: 85px;">Fecha de Egreso</td>



            <td style="width: 90px;"><input type="text" value="'.$fechaegr.'" name="fecent" id="fecent" size="14" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>



            <td style="width: 113px;">a las &nbsp; &nbsp;<span class="campoFecha">&nbsp;'.str_replace(".",":",$horaegr).' hrs&nbsp;</span></td>



          </tr>



        
        
        
        </tbody>
      
      
      
      </table>



      
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">



        <tbody>



          <tr>



            <td style="width: 180px;">C&oacute;digo Diagn&oacute;stico Principal de Ingreso</td>



            <td style="width: 117px;"><div class="campoFecha">'.$diaing.'</div></td>



            <td style="width: 183px;">C&oacute;digo Diagn&oacute;stico Principal de Egreso</td>



            <td style="width: 112px;"><div class="campoFecha">'.$diadef.'</div></td>



          </tr>



          <tr>



            <td style="width: 180px;">Otro C&oacute;digo Diagn&oacute;stico de Ingreso</td>



            <td style="width: 117px;"><div class="campoFecha"> &nbsp; </div></td>



            <td style="width: 183px;">Otro C&oacute;digo Diagn&oacute;stico de Egreso</td>



            <td style="width: 112px;"><div class="campoFecha"> &nbsp; </div></td>



          </tr>



          <tr>



            <td style="width: 180px;">Otro C&oacute;digo Diagn&oacute;stico de Ingreso</td>



            <td style="width: 117px;"><div class="campoFecha"> &nbsp; </div></td>



            <td style="width: 183px;">Otro C&oacute;digo Diagn&oacute;stico de Egreso</td>



            <td style="width: 112px;"><div class="campoFecha"> &nbsp; </div></td>



          </tr>



        
        
        
        </tbody>
      
      
      
      </table>



      
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="1" rowspan="1" style="text-align: center; width: 293px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Medapellido1.'</div></td>

		  <td style="width: 40px;"></td>
         <td colspan="1" rowspan="1" style="text-align: center; width: 297px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Medapellido2.'</div></td>


		</tr>

		<tr>

		  <td style="text-align: center; width: 293px; vertical-align: top;" colspan="1" rowspan="1">1er Apellido del M&eacute;dico o Profesional Tratante</td>


		  <td style="width: 40px;"></td>
         <td style="text-align: center; width: 297px; vertical-align: top;" colspan="1" rowspan="1">2do Apellido del M&eacute;dico o Profesional Tratante</td>


		</tr>
   
        </tbody>
 
      </table>


      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="1" rowspan="1" style="text-align: center; width: 293px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Mednombre1.'</div></td>

		  <td style="width: 40px;"></td>
         <td colspan="1" rowspan="1" style="text-align: center; width: 297px; white-space: nowrap; vertical-align: bottom;"><div class="campoNombre">'.$Mednombre2.'</div></td>


		</tr>

		<tr>

		  <td style="text-align: center; width: 293px; vertical-align: top;" colspan="1" rowspan="1">1er Nombre del M&eacute;dico o Profesional Tratante</td>


		  <td style="width: 40px;"></td>
         <td style="text-align: center; width: 297px; vertical-align: top;" colspan="1" rowspan="1">2do Nombre del M&eacute;dico o Profesional Tratante</td>


		</tr>
   
        </tbody>
 
      </table>   
      
      
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td style="text-align: left; white-space: nowrap; width: 91px;">Tipo Documento</td>

		  <td style="width: 108px;"><div class="campoFecha">'.$Medtipoide.'</div><br>         </td>

		  <td style="white-space: nowrap; width: 133px;">No. Documento</td>

		  <td style="width: 260px;"><div class="campoNombre">'.$Medidentificacion.'</div></td>
         
            





		</tr>

		<tr>

		  <td style="white-space: nowrap; width: 91px;"><br>         </td>

		  <td style="vertical-align: bottom; width: 108px;"><br>         </td>

		  <td style="white-space: nowrap; width: 133px;">N&uacute;mero de Registro M&eacute;dico</td>

		  <td style="width: 260px;"><div class="campoNombre">'.$Medregistro.'</div></td>
         
            





		</tr>		

        </tbody>
 
      </table>

  
	  </td>

    </tr>

  <tr>

	<td class="tituloSeccion">X. AMPAROS QUE RECLAMA

	</td>

  </tr>

   
  <tr>

	<td>
      
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">

		<tbody>
          <tr>

		  <td colspan="1" rowspan="1" class="descripcion">
			
            
            <table style="border: 1px solid rgb(51, 51, 51); width: 91%;" cellpadding="2" cellspacing="2">


              <tbody>


                <tr>


                  <td style="width: 50%;" class="descripcion"></td>


                  <td style="width: 25%;" class="descripcion">VALOR TOTAL FACTURADO</td>


                  <td style="width: 25%;" class="descripcion">VALOR RECLAMADO AL '.$nom_ase_est.'</td>


                </tr>


                <tr>


                  <td class="descripcion">GASTOS M&Eacute;DICOS QUIR&Uacute;RGICOS</td>


                  <td class="descripcion" align="center"><input type="text" value="'.number_format($total_facturado,0,',','.').'" name="facmed" id="facmed" size="11" readonly style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>


                  <td class="descripcion" align="center"><input type="text" value="'.number_format($total_ase_est,0,',','.').'" name="fosmed" id="fosmed" size="11" readonly style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>


                </tr>


                <tr class="descripcion">


                  <td class="descripcion">GASTOS DE TRANSPORTE Y MOVILIZACI&Oacute;N DE LA V&Iacute;CTIMA</td>


                  <td class="descripcion"><input type="text" value="0" name="facmov" id="facmov" size="11" readonly style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>


                  <td class="descripcion"><input type="text" value="0" name="fosmov" id="fosmov" size="11" readonly style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>


                </tr>


              
              
              </tbody>
            
            
            </table>



El total facturado y reclamado descrito en este numeral se debe
detallar y hacer descripci&oacute;n de las actividades, procedimientos,
medicamentos, insumos, suministros y materiales, dentro del anexo
t&eacute;cnico n&uacute;mero 2.</td>
	  </tr>





		
        
        
        
        
        </tbody>
      
      
      
      
      </table>





	  </td>





	</tr>


   
  <tr>


	<td class="tituloSeccion">
	  
      
      <div style="text-align: center;">XI. DECLARACI&Oacute;N DE LA INSTITUCI&Oacute;N PRESTADORA DE SERVICIOS DE SALUD</div>


	</td>


  </tr>


  <tr>


	<td>
		
      
      <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">


		  <tbody>


			<tr>


			  <td colspan="1" rowspan="1" class="descripcion">Como
		representante legal o Gerente de la Instituci&oacute;n Prestadora de
		Servicios de Salud, declar&oacute; bajo la gravedad de juramento que
		toda la informaci&oacute;n contenida en este formulario es cierta y
		podr&aacute; ser verfificada por la Direcci&oacute;n General de
		Financiamiento del Ministerio de la Protecci&oacute;n Social, por el
		Administrador Fidusiario del '.$nom_ase_est.', por la Superintendencia Nacional de Salud o la
		Contralor&iacute;a General de la Rep&uacute;blica con la IPS y las
		aseguradoras, de no ser as&iacute;, acepto todas las consecuencias
		legales que produzca esta situaci&oacute;n.<br>


			  <br>


			  
            
            <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">


				<tbody>


				  <tr>


					<td><div class="campoCodigo"> &nbsp; '.$responsable.' &nbsp; </div></td>


					<td>_________________________________________</td>


				  </tr>


				  <tr>


					<td class="descripcion">NOMBRE</td>


					<td class="descripcion">FIRMA DEL REPRESENTANTE LEGAL, GERENTE O SU DELEGADO</td>


				  </tr>


				
              
              </tbody>
			  
            
            </table>


			  <br>


			  </td>
			  </tr>





				
				
		  
		  
        
        </tbody>
			  
		
      
      </table>





	  

	


  
  
        
	</td>


  </tr>


    
  
  
  
  </tbody>
</table>

</div>

';

/******************************************************************************************/
//IMPRESI�N DE LA CUENTA DE COBRO
echo '
<div style="page-break-after: always;">
<br>
<table style="border: 2px solid rgb(51, 51, 51); width: 570px;" cellpadding="2" cellspacing="2">
  <tbody>
    <tr>
      <td>
		  <table style="border: 0pt none ; width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="2" cellspacing="2">
			<tbody>
			  <tr>
				<td style="text-align: left; width: 17%;">Cuenta de Cobro No. </td>
				<td style="width: 25%;"><div class="campoCta">'.$num_factura.'</div></td>
				<td style="width: 18%;"><div align="right">POLIZA SOAT No. </div></td>
				<td style="width: 25%;"><div class="campoCta">'.$soatNum.'</div></td>
				<td style="width: 15%;"><div align="right">P&aacute;gina: 1 </div></td>
			  </tr>
			</tbody>
		  </table>  
		  <br />		  
	  </td>
    </tr>

    <tr>
	  <td>
		<table border="0" cellpadding="2" cellspacing="2">
		  <tbody>
			  <tr>
				<td> &nbsp; </td>
				<td><div class="campoCta3">'.$nombre_emp.'</div></td>
				<td> &nbsp; </td>
				<td align="right"> &nbsp; NIT. &nbsp; </td>
				<td><div class="campoCta">'.$nit_emp.'</div></td>
			  </tr>
			  <tr>
				<td colspan="6"> &nbsp; </td>
			  </tr>
			  <tr>
				<td>DEBE A: </td>
				<td><div class="campoCta3">'.$nomEmp.'</div></td>
				<td> &nbsp;&nbsp;&nbsp;&nbsp; </td>
				<td align="right"> &nbsp; NIT. &nbsp; </td>
				<td><div class="campoCta">'.$nitEmp.'</div></td>
			  </tr>
		  </tbody>
		</table>
		<br />
	  </td>
    </tr>
    <tr>
	  <td>
		<table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">
		  <tbody>
			<tr>
			  <td style="text-align: left; width: 10%;">Direcci&oacute;n: </td>
			  <td style="width: 25%;"><div class="campoCta2">'.$dirEmp.'</div>
				  <br />          </td>
			  <td style="white-space: nowrap; width: 15%;"><div align="right">Ciudad: </div></td>
			  <td style="width: 20%;"><div class="campoCta">'.$ciuEmp.'</div></td>
			  <td style="width: 15%;"><div align="right">Tel&eacute;fono</div></td>
			  <td style="width: 15%;"><div align="left"><span class="campoCta">'.$telEmp.'</span></div></td>
			</tr>
		  </tbody>
		</table>
	  </td>
    </tr>
    <tr>
	  <td>
	  <table widht="100%" style="border="0" cellpadding="2" cellspacing="2">
        <tbody>
          <tr>
            <td style="width: 10%;" align="left">La suma de: </td>
            <td style="width: 75%;" align="left"> '.$facturadoTex.'</td>
            <td style="width: 15%;" align="left"> &nbsp; &nbsp; $ '.number_format($facturado,0,',','.').'</td>
          </tr>
        </tbody>
      </table>
	  <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">
        <tbody>
          <tr>
            <td style="text-align: left; white-space: nowrap; width: 15%;">&nbsp;</td>
          </tr>
          <tr>
            <td style="text-align: left; white-space: nowrap; width: 15%;">Por concepto de servicios de salud correspondientes al Seguro Obligatorio de Accidentes de Tr&aacute;nsito </td>
            </tr>
        </tbody>
      </table>
	  <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">
        <tr>
          <td style="width: 10%;">Prestado a: </td>
          <td style="width: 35%;"><div class="campoCta3">'.$paciente.'</div></td>
          <td style="white-space: nowrap; width: 25%;"><div align="right">Documento de Identidad: </div></td>
          <td style="width: 30%;"><div class="campoCta">'.$idePac.'</div></td>
        </tr>
        <tbody>
        </tbody>
      </table>
	  </td>
    </tr>
	<tr>
	  <td>&nbsp;</td>
	</tr>
    <tr>

	  <td>
	  <table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">
	    <tbody>
		  <tr>
			<td align="left">Historia: </td>
			<td><div class="campoCta">'.$hisPac.'</div></td>
			<td align="right">Fecha de Entrega </td>
			<td colspan="3"><div class="campoCta">'.$fecha.'</div></td>
			</tr>
			<tr>
			  <td style="width: 18%;"><div align="left">Fecha de ingreso </div></td>
			  <td style="width: 20%;"><input type="text" value="'.$ingFec.'" name="fecing" id="fecing" size="17" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>
			  <td style="width: 17%;"><div align="right">Fecha de egreso  </div></td>
			  <td style="width: 20%;"><input type="text" value="'.$egrFec.'" name="fecent" id="fecent" size="17" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></td>
			  <td style="width: 15%;"><div align="right">D&iacute;as Estancia </div></td>
			  <td style="width: 10%;"><div align="left" class="campoCta"><input type="text" value="'.$diasEst.'" name="fecent" id="fecent" size="1" style="border-top: 0px;border-right: 0px;border-left: 0px;border-bottom: 1px #333333 solid; text-align:center; font-size: 7pt !important; font-size: 6.5pt;" /></div></td>
			</tr>
		</tbody>
      </table>
	  </td>
    </tr>
	  
    <tr>
	  <td>&nbsp;</td>
    </tr>


    <tr>
	  <td>
		  <table style="width: 100%;" align="center" border="0" cellpadding="2" cellspacing="2">
		    <tbody>
			  <tr>
				<td colspan="5"><div style="width: 100%; border-bottom: 1px solid #000000; border-top: 1px solid #000000;  padding: 0 0 0 4px; text-align: center;">DESCRIPCI&Oacute;N DE SERVICIOS </div></td>
			  </tr>
			  <tr>
				<td style="text-align: left; width: 20%;"><div align="center" class="campoCta">CODIGO</div></td>
				<td style="width: 40%;"><div align="center" class="campoCta3">PROCEDIMIENTO</div>            </td>
				<td style="width: 10%;"><div align="center" class="campoCta1">CANTIDAD </div></td>
				<td style="width: 15%;"><div align="center" class="campoCta1">VALOR</div></td>
				<td style="width: 15%;"><div align="center" class="campoCta1">VALOR TOTAL</div></td>
			  </tr> ';

	/* Ya no se maneja consecutivo por txt, sino que se toma el n�mero del documento
	  if($cont==1)
		$consecutivo = obtener_consecutivo("../../planos/FURIPS/farpmla/consecutivo.txt");
	*/
	
	  // Consulto detalle de la venta para listar los productos
	  $q =   "SELECT Vdeart,Vdeart,Vdecan,Vdevun "
			."  FROM ".$wbasedato_farm."_000017 "
			." WHERE Vdenum = '".$ctaNum."' "
			."	 AND Vdeest = 'on' ";
	  $res_vde = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $numvde=mysql_num_rows($res_vde);

	  while($row_vde=mysql_fetch_array($res_vde))
	  {
		  // Consulto detalle de la venta para listar los productos
		  $qart =    "SELECT Artnom "
					."  FROM ".$wbasedato_farm."_000001 "
					." WHERE Artcod = '".$row_vde['Vdeart']."' "
					."	 AND Artest	= 'on' ";
		  $res_art = mysql_query($qart,$conex) or die (mysql_errno()." - ".mysql_error());
		  $row_art=mysql_fetch_array($res_art);

		  $totalvde = $row_vde['Vdevun']*$row_vde['Vdecan'];
		  echo '
		  <tr>
			<td><div align="left">'.$row_vde['Vdeart'].'</div></td>
			<td>'.$row_art['Artnom'].'</td>
			<td align="center">'.$row_vde['Vdecan'].'</td>
			<td align="right">'.number_format($row_vde['Vdevun'],0,',','.').'</td>
			<td align="right">'.number_format($totalvde,0,',','.').'</td>
		  </tr>';
	  }

	  echo '
			    <tr>
				  <td colspan="5">&nbsp;</td>
				</tr>
				<tr>
				  <td colspan="3" style="white-space: nowrap;"><div align="left">TOTAL EN LETRAS:  &nbsp; '.$facturadoTex.'</div></td>
				  <td align="right">TOTAL &nbsp; </td>
				  <td align="right">$ '.number_format($facturado,0,',','.').'</td>
				</tr>
		    </tbody>
		  </table>
	  </td>
	</tr>
   
	<tr>
	  <td height="41">&nbsp;</td>
	</tr>
	<tr>
	  <td>
		<table style="width: 100%;" border="0" cellpadding="2" cellspacing="2">
		  <tbody>
			<tr>
			  <td style="width:70%">&nbsp;  </td>
			  <td>_________________________________________</td>
			</tr>
			<tr>
			  <td>&nbsp;</td>
			  <td><div align="center">FIRMA Y SELLO</div></td>
			</tr>
		  </tbody>
		</table>
	  </td>
	</tr>
  </tbody>
</table>
<p>&nbsp;</p>
</div>
';

		$cont++;
	 }
   }
   else
   {
		//echo "No se ha encontrado informaci�n del paciente con la historia especificada";
   }
  }
 }
}
 if($contsist==0)
  {
	echo "<table border=0 width='570'>";
	echo "<tr>";
	echo "<td align=center style='font-size:14px'>";
	encabezado($titulo,$wactualiz, "clinica");  
	echo "<br />NO SE ENCONTRARON REGISTROS PARA LOS DATOS CONSULTADOS<br /><br /><input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
	echo "</td></tr></table>";
  }
}
else
{
	echo "<table border=0 width='570'>";
	echo "<tr>";
	echo "<td align=center style='font-size:14px'>";
	encabezado($titulo,$wactualiz, "clinica");  
	echo "<br />NO SE HA ESTABLECIDO CONEXI�N CON LA BASE DE DATOS. <br> INTENTELO DE NUEVO MAS TARDE.<br /><br /><input type=button value='Cerrar ventana' onclick='javascript:window.close();'>";
	echo "</td></tr></table>";
	
}
} 

//echo "<script language='JavaScript'>Abrir_ventana('formato_cta_cobro.php?wemp_pmla=".$wemp_pmla."&cco=".$cco."&wfecha_ini=".$wfecha_ini."&wfecha_fin=".$wfecha_fin."');</script>";


echo '</body>
</html>';
?>
