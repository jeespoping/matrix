<head>
  <title>PROGRAMA DE COMENTARIOS Y SUGERENCIAS</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>  
<script src="efecto.php"></script>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
  
    	.titulo1{color:#FFFFFF;background:#006699;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;}	
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo5{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.titulo6{color:#003366;background:#FFCC66;font-size:12pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.texto1{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;text-align:center;}
    	.texto2{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Arial;text-align:center;}
    	.texto3{color:#ffffff;background:#336699;font-size:9pt;font-weight:bold;font-family:Arial;}
    	.texto4{background:#FFFFFF;font-size:9pt;font-family:Arial;text-align:left;}
    	.texto5{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Arial;text-align:right;}
    	.texto6{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Arial;text-align:center;}
    	.texto7{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Arial;text-align:right;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado3{color:#003366;background:#57C8D5;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.acumulado4{color:#003366;background:#57C8D5;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
    	.acumulado5{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.acumulado6{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.borde{border-style:solid; border-color:#336699;}
   </style>
  
  
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->

function Seleccionar()
{
	document.forma.bandera.value=2;
	document.forma.submit();
}

function Seleccionar2()
{
	document.forma.bandera.value=7;
	document.forma.submit();
}

function Seleccionar3()
{
	document.forma.bandera.value=8;
	document.forma.submit();
}

function Escoger()
{
	document.forma.bandera.value=3;
	document.forma.submit();
}

function Escoger2()
{
	document.forma.bandera.value=4;
	document.forma.submit();
}

function Escoger3()
{
	document.forma.bandera.value=5;
	document.forma.submit();
}

function Escoger4()
{
	document.forma.bandera.value=6;
	document.forma.submit();
}

</SCRIPT>

</head>

<?php
include_once("conex.php");

/**
 * Scripts del Programa de comentarios y sugerencias:
 * 
 * pagina1.php                Ingreso de paciente
 * comentario.php             Lista los comentarios de un paciente y permite ingresarle uno nuevo
 * detalle comentario.php     Permite ingresar el comentario
 * asignacion.php             Permite asignar los motivos del comentario a las diferentes areas e ingresar implicados
 * listaMagnta.php            Lista los ceomentarios en un rango de fecha por estado
 * coordinador.php            Lista los momentarios a ser investigados por un coordinador especifico
 * investigacion.php          Se escriben las investigaciones y causas deun motivo por el coordinador
 * investigacion2.php         Se escriben las investigaciones y causas deun motivo por magenta
 * respuesta.php              Se cierra un comentario y se da respuesta al usuario  
 * auditoria.php              Informe a talento humano o a desarrollo orgacional de implicados o acciones respectivamente
 * recom.php                  Permite utilizar la numeracion de un comentario en otro paciente
 * informeComentarios.php     Todo el reporte de indicadores de comentarios de Magenta
 * semaforización.php:  	  manda correos a los coordinadores cuyos comentarios cambiaran de color, es una tarea programada
 * 
 * Reportes:
 * rep_volveria.php          Reporte de personas que volverian a la clinica por entidad y maternas
 * 
 * Includes del programa
 * semaforo.php  Indica de que color esta cada motivo segun la fecha de envio y de retor5no por el coordinador
 * 
 */

/**
 * INGRESO DE COMENTARIOS
 * 
 * Este programa permite el ingreso u busqueda de pacientes con comentarios, en conectividad con unix, para buscar por combinación de
 * cedula, nombre o apellidos, los pacientes en matrix, activos, hospitalizados los ultimos 31 dias o en ayudas dianosticas en los ultimos 31 dias.
 * Una vez seleccionado el paciente de cualquiera de las búsquedas, permite la actualización de sus datos en Matrix.
 * 
 * @name pagina1.php
 * @author ccastano
 * @created 2006-04-03
 * @version 2006-01-03
 * 
 * @modified 2006-01-03  Se realiza casi de nuevo el programa para que tenga la conectividad con Unix
 * @modified 2006-01-31  Se cambia el tamaño de los drop down para que salgan en computadores pequeños de letra grande, Carolina Castaño
 *
 *  Actualizacion: 2012-06-12 Se agrega la funcion que valida los nulos en las consultas a inpac, inpaci y aymov.  Viviana Rodas
 *  Actualizacion: 2012-06-13 Se agrega la funcion que valida los nulos en las consultas a inpac, inpaci y aymov donde se muestran los datos de los *  pacientes.
 * 
 * @table det_selecciones, select del tipo de documento
 * @table magenta_000016 select, update, insert sobre tabla de pacientes con comentarios
 * @table inpac, select
 * @table inmtra, select
 * @table inser, select
 * @table inpaci, select
 * @table inmegr, select
 * @table aymov, select
 * @table inemp, select
 * 
 *  
 * @var $activos, nombre y valor del drop down de la busqueda de hoispitalizados actualmente
 * @var $ayudas,  nombre y valor del drop down de la busqueda  visitas a ayudas diagnosticas en los ultimos 31 dias
 * @var $bandera, lleva el hilo de ejecución, indica que opcion se ha seleccionado y por ende que debe realizarce despues del submit 
 * @var $dir, direccion del paciente seleccionado
 * @var $doc, documento del paciente seleccionado
 * @var $egresados, nombre y valor del drop down de la busqueda de hospitalizados egresados en los ultimos 31 dias
 * @var $ema, email del paciente seleccionado
 * @var $fecha1, 31 dias desde la fecha actual
 * @var $fecha2, fecha actual
 * @var $his, historia clinica del paciente seleccionado
 * @var $matrix, nombre y valor del drop down de la busqueda  de pacientes en bd de matrix
 * @var $ndoc, dato de busqueda por numero de documento
 * @var $pape, dato de busqueda por primer apellido
 * @var $pnom, dato de busqueda por primer nombre
 * @var $priApe, primer apellido del paciente seleccionado
 * @var $priNom, primer nombre del paciente seleccionado
 * @var $sape, dato de busqueda por segundo apellido
 * @var $segApe, segundo apellido del paciente seleccionado
 * @var $segNom, segundo nombre del paciente seleccionado
 * @var $senal, indica que botones deben ser presentados en la interfaz de acuerdo a las operaciones realizadas
 * @var $ser, servicio que utiliza o utilizo el paciente seleccionado
 * @var $snom, dato de busqueda por segundo nombre
 * @var $tDoc, dato de busqueda por tipo de documento
 * @var $tDocS, nombre del drop down de tipo de documento para dato de busqueda
 * @var $tel, telefono del paciente seleccionado
 * @var $tiempo, para calcular la fecha 1, es decir resta 31 dias a la actual
 * @var $tipDoc, tipo de documento del paciente seleccionado
 * @var $tipDocS, drop down del tipo de documento del paciente seleccionado
 * @var $vol, nombre del radio button para busqueda de datos actualizados o en matrix
 * @var $wfecfin, fecha actual es la fecha final para la busqueda entre dos fechas
 * @var $wfecini, fecha ainicial de busqueda, 31 dias menos que la actual
 * 
**/
/************************************************************************************************************************* 
  Actualizaciones:
            2016-05-02 (Arleyda Insignares C.)
                        Se Modifica solo diseño: Titulos, Encabezado del script y clase de la tabla superior 'fila1'

*************************************************************************************************************************/


$wautor="Carolina Castano P.";
$wversion='2006-01-31';
$wactualiz='2016-05-03';

include_once("root/comun.php");

/////////////////////////////////////////////////encabezado general///////////////////////////////////
$titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";

// Se muestra el encabezado del programa
encabezado($titulo,$wactualiz, "clinica");  

//=================================================================================================================================

/*
* Funcion que hace la validacion y actualizacion de los campos nulos que traen las consultas de unix
* Comprende las dos funciones ejecutar_consulta() y validar_nulos()
*/

function ejecutar_consulta ($query_campos, $query_from, $query_where, $llaves, $conexUnix )
    {
	
            global $increment;
            $increment++;
            if ($query_where == NULL)              
                $query =   " SELECT $query_campos FROM $query_from ";
            else
                $query =   " SELECT $query_campos FROM $query_from WHERE $query_where";

            $table= date("Mdhis").$increment;                //nombre de la tabla temporal	
            $query=$query." into temp $table";              //creo la temporal con los resultados de la consulta que enviaron
            odbc_do($conexUnix,$query) or die( odbc_error()." - $query - ".odbc_errormsg() );
            
            $query1= "select * from $table";
            $err_o1 = odbc_do($conexUnix,$query1) or die( odbc_error()." - $query1 - ".odbc_errormsg() ); // Consulto la tabla temporal
            
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
                    validar_nulos($query_from, $query_where, $campo, $valor, $pk_nombre, $pk_valor, $conexUnix, $table);//ESTA FUNCION ACTUALIZA EL VALOR DEL CAMPO DE LA TEMPORAL, DEPENDIENDO DEL VALOR DE LA ORGINAL
                }
                
            }
            $query1="select * from $table ";
            $err_o1 = odbc_do($conexUnix,$query1) or die( odbc_error()." - $query1 - ".odbc_errormsg() ); // retornar la consulta sin null
            unset($llaves);
            return $err_o1;                  
    }
			
function validar_nulos($query_from, $query_where, $campo, $valor, $pk_nombre, $pk_valor, $conexUnix, &$table)
    {
	//echo"<br> entro4";
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
        $res_no_null = odbc_do($conexUnix, $query_no_null)or die( odbc_error()." - query no null: - ".odbc_errormsg() );
        
        
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
            $err_o4 = odbc_do($conexUnix,$query4)or die( odbc_error()." - UPDATE TEMP con valor original: - ".odbc_errormsg() );
        }                  
    }

/**
 * CONVIERTE LAS INCIALES DEL TIPO DE DOCUMENTO QUE  VIENE DE UNIX EN EL NOMBRE COMPELTO
 *
 * @param unknown_type $tipDoc1 INICIALES DEL TIPO DE DOCUMENTO DE UNIX
 * @return unknown
 */function engrandar($tipDoc1)
{
	switch ($tipDoc1)
	{
		case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
		break;
		case "TI": $tipDoc="TI-TARJETA DE IDENTIDAD";
		break;
		case "MS": $tipDoc="MS-MENOR SIN IDENTIFICACION";
		break;
		case "AS": $tipDoc="AS-ADULTO SIN IDENTIFICACION";
		break;
		case "CE": $tipDoc="CE-CEDULA DE EXTRANJERIA";
		break;
		case "RC": $tipDoc="RC-REGISTRO CIVIL";
		break;
		case "PA": $tipDoc="PA-PASAPORTE";
		break;
		case "NU": $tipDoc="NU-NRO UNICO DE IDENTIFICAC.";
		break;
		case "NI": $tipDoc="NI-NIT";
		break;
		default:$tipDoc="AS-ADULTO SIN IDENTIFICACION";
		break;
	}

	return $tipDoc;
}

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	////////////inicialización de variables////////////////////////////////////////////

	$wbasedato='magenta';
	$senal=0; //se inicializa para saber que botones se van a desplegar

	/**
	 * include de conexión a base de datos Matrix
	 *
	 */
	


	

	$bd='facturacion';

	/**
	 * Include de conexion a base de datos Unix
	 *
	 */
	include_once("socket.php");
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//inicializamos todo el formulario del programa
	echo "<form action='pagina1.php' method=post name='forma'>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' NAME= 'bandera' value='0'>"; //indica si el programa debe cargar los datos de inicio o el la consulta

	///////////////////tabla de ingreso de datos para la busqueda del paciente////////////////////////////////////
	echo "<center><table border=2>";
	echo "<tr>";
    echo "<br></br>";
	//documento de identidad para busqueda del paciente
	
	if (!isset($ndoc))
	{   
		echo "<td align=center class='fila1' COLSPAN=2 ><b>DOCUMENTO DE IDENTIDAD: </font></b><INPUT TYPE='text' NAME='ndoc' VALUE='' size='10'></td>"; 
	}
	else
	{
		echo "<td align=center class='fila1' COLSPAN=2 ><b>DOCUMENTO DE IDENTIDAD: </font></b><INPUT TYPE='text' NAME='ndoc' VALUE='".$ndoc."' size='10'></td>"; 
	}

	if (!isset($tDoc))
	{
		$tDoc='CC-CEDULA DE CIUDADANIA';
	}

	echo "<td align=center class='fila1' COLSPAN=2 >TIPO DE DOCUMENTO: <select name='tDoc'>" ;
	//query para dorp down de seleccion del tipo del documento, para busqueda del paciente
	$query="select Subcodigo, Descripcion from det_selecciones where medico='magenta' and codigo='01'";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	if  ($num >0)
	{
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_row($err);
			$tDocS[$i]=$row[0]."-".$row[1];
			if($tDocS[$i] == strtoupper($tDoc))
			echo "<option selected>".$tDocS[$i]."</option>";
			else
			echo "<option>".$tDocS[$i]."</option>";
		}

	}
	echo "</tr>";

	// primer nombre del paciente para busqueda
	echo "<tr>";
	if (!isset($pnom))
	{
		echo "<td align=center class='fila1'><b>PRIMER NOMBRE: </font></b><INPUT TYPE='text' NAME='pnom' VALUE='' size='10'></td>";
	}
	else
	{
		echo "<td align=center class='fila1'><b>PRIMER NOMBRE: </font></b><INPUT TYPE='text' NAME='pnom' VALUE='".$pnom."' size='10'></td>";
	}

	// segundo nombre del paciente para busqueda
	if (!isset($snom))
	{
		echo "<td align=center class='fila1'><b>SEGUNDO NOMBRE: </font></b><INPUT TYPE='text' NAME='snom' VALUE='' size='10'></td>";
	}
	else
	{
		echo "<td align=center class='fila1'><b>SEGUNDO NOMBRE: </font></b><INPUT TYPE='text' NAME='snom' VALUE='".$snom."' size='10'></td>";

	}

	// primer apellido del paciente para busqueda
	if (!isset($pape))
	{
		echo "<td align=center class='fila1'><b>PRIMER APELLIDO: </font></b><INPUT TYPE='text' NAME='pape' VALUE='' size='10'></td>";
	}
	else
	{
		echo "<td align=center class='fila1'><b>PRIMER APELLIDO: </font></b><INPUT TYPE='text' NAME='pape' VALUE='".$pape."' size='10'></td>";
	}

	// segundo apellido del paciente para busqueda
	if (!isset($sape))
	{
		echo "<td align=center class='fila1'><b>SEGUNDO APELLIIDO: </font></b><INPUT TYPE='text' NAME='sape' VALUE='' size='10'></td>";
	}
	else
	{
		echo "<td align=center class='fila1'><b>SEGUNDO APELLIIDO: </font></b><INPUT TYPE='text' NAME='sape' VALUE='".$sape."' size='10'></td>";
	}
	echo "</tr>";

	// busqueda en matrix unicamente, o unix y matrix
	echo "<tr>";
	echo "<td align=center class='fila1' colspan=4> ";
	echo "<input type='radio' name='vol' value='2' onclick='Seleccionar()' >BUSCAR DATOS MAS ACTUALIZADOS&nbsp;&nbsp;";
	echo "<input type='radio' name='vol' value='1' onclick='Seleccionar()' >BUSCAR PACIENTE EN MATRIX&nbsp;&nbsp;&nbsp;&nbsp;";

	///////////////////////////////////////////////////////////////////////////////////////////////////////


	////////////segun el valor de bandera se sabe que accion //////////////////////////////////////

	// busqueda de datos
	if (isset ($bandera) and $bandera==2)
	{

		//busqueda en matrix
		//tomar los datos enviados para organizar el query de búqueda
		$query=' ';
		if (isset ($ndoc) and $ndoc!='')
		{
			$query=$query."cpedoc='".$ndoc."' and ";
			$query=$query."cpetdoc='".$tDoc."' and ";
		}
		if (isset ($pnom) and $pnom!='')
		$query=$query."cpeno1='".strtoupper($pnom)."' and ";
		if (isset ($snom) and $snom!='' and $snom !='- -')
		$query=$query."cpeno2='".strtoupper($snom)."' and ";
		else if (!isset($snom))
		$snom='- -';
		if (isset ($pape) and $pape!='')
		$query=$query."cpeap1='".strtoupper($pape)."' and ";
		if (isset ($sape) and $sape!='' and $sape !='- -')
		$query=$query."cpeap2='".strtoupper($sape)."' and ";
		else if (!isset($sape))
		$sape ='- -';

		if (!isset ($tel) or $tel == '')
		$tel='- - ';
		if (!isset ($dir) or $dir == '')
		$dir='- -';
		if (!isset ($ema) or $ema == '')
		$ema= '- -';

		if ($query != ' ')
		{
			$query=substr($query,0,-4);
			$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, fecha_data  FROM " .$wbasedato."_000016 where".$query;

			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if  ($num >0) //se llena los valores y un vector con los resultados
			{
				echo "<tr>";

				echo "<td align=left class='texto3' COLSPAN=4 >RESULTADOS DE BUSQUEDA EN MATRIX: <BR><select name='matrix' STYLE='font-family : ariel; font-size : 75%' onchange='Escoger()'>" ;
				echo "<option selected> </option>";

				for($i=0;$i<$num;$i++)
				{
					$row=mysql_fetch_row($err);
					echo "<font size=1><option>".$row[0]."-".$row[1]." - ".$row[2]." ".$row[3]." ".$row[4]." ".$row[5]." - ACTUALIZADO: ".$row[6]."</option></font>";
				}


				echo "</td></tr>";

			}
			else if ($vol==1)
			{
				echo "<tr>";
				echo "<td align=CENTER class='texto3' COLSPAN=4 >NO SE ENCONTRARON REGISTROS EN MATRIX" ;
				echo "</td></tr>";
			}
		}

		// si se escogio buscar los datos tambien en unix
		if ($vol==2)
		{
			//cuadro la fecha de busqueda en una rango de 31 dias partir del dia
			$fecha2=date('Y/m/d');
			$tiempo=mktime(0,0,0,substr($fecha2,5,2),substr($fecha2,8,2),substr($fecha2,0,4));
			$tiempo=$tiempo-(31*60*60*24);
			$fecha1=date('Y/m/d', $tiempo);


			//ahora voy a buscar los pacientes con las caraterisiticas ingresadas pero activos
			//tomar los datos enviados para organizar el query de búqueda
			$query=' ';
			$exp=explode('-', $tDoc);
			if (isset ($ndoc) and $ndoc!='')
			{
				$query=$query."pacced='".$ndoc."' and ";
				$query=$query."pactid='".$exp[0]."'  and ";
				
			}
			if (isset ($pnom) and $pnom!='')
			{
				if (!isset ($snom) or $snom=='')
				{
					$query=$query."pacnom like '%".strtoupper($pnom)."%' and ";
				}
				else
				{
					$query=$query."pacnom like '%".strtoupper($pnom)." ".strtoupper($snom)."%' and ";
				}
			}

			if (isset ($snom) and $snom!='' and $snom !='- -' and (!isset ($pnom) or $pnom=='' or $pnom=='- -'))
			{
				$query=$query."pacnom like '%".strtoupper($snom)."%' and ";
			}

			if (isset ($pape) and $pape!='')
			$query=$query."pacap1='".strtoupper($pape)."' and ";

			if (isset ($sape) and $sape!='' and $sape !='- -')
			$query=$query."pacap2='".strtoupper($sape)."' and ";
			else if (!isset($sape))
			$sape ='- -';

			$long='                         ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
			$increment=0;
			$increment++;
			$table=date("Mdhis").$increment;
			
			if ($query != ' ')
			{
				
				$query=substr($query,0,-4);
				$query2="select pacced, pactid,  pacnom, pacap1, pacap2, pacfec, sernom  "
				."from inpac,  inmtra  , inser   "
				."where pacfec between '".$fecha1."' and '".$fecha2."' and ".$query. " and trahis=pachis and tranum=pacnum and traegr is null and sercod=traser into temp $table ";//inpac-> casi toda la información

				//echo"<br> query inpac <BR>".$query2;     
				$err_o = odbc_exec($conex_o,$query2);
				
				
				$select= " pacced, pactid, pacnom, pacap1, '".$long."' as pacap2, pacfec, sernom";
				$from= " $table "; 
				$llaves[0]=1;
				$where=NULL; 
				
				$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o );	
				$j=0;


				while (odbc_fetch_row ($err_o))
				{
					if ($j==0)
					{
						echo "<tr>";

						echo "<td align=left class='texto3' COLSPAN=4 >RESULTADOS DE BUSQUEDA DE PACIENTES ACTIVOS: <BR><select name='activos' STYLE='font-family : ariel; font-size : 75%' onchange='Escoger2()'>" ;
						echo "<option selected> </option>";
						$j++;
					}

					echo "<option>".odbc_result($err_o,1)."-".odbc_result($err_o,2)." - ".odbc_result($err_o,3)." ".odbc_result($err_o,4)." ".odbc_result($err_o,5)." - ACTUALIZADO: ".odbc_result($err_o,6)." - SERVICIO: ".odbc_result($err_o,7)."</option>";
				}
				echo "</td></tr>";
			}


			//ahora vamos a buscar los pacientes que se encuentran en inpaci, es decir que ya salieron pero cumplen con las fechas rango.
			
			$increment++;
			$table2=date("Mdhis").$increment;
			
			if ($query != ' ')
			{
				$query="select pacced, pactid,  pacnom, pacap1, pacap2, pacing, egregr, egrseg, sernom  "
				."from inpaci,  inmegr  , inser   "
				."where pacing between '".$fecha1."' and '".$fecha2."' and ".$query. " and egrhis=pachis and egrnum=pacnum and sercod=egrseg ORDER BY sernom, egregr into temp $table2 ";//inpac-> casi toda la información

				
				//echo"<br> query inpaci <BR>".$query;     
				$err_o = odbc_exec($conex_o,$query);
				
			
				$select= " pacced, pactid, pacnom, pacap1, '".$long."' as pacap2, pacing, '".$long."' as egregr, egrseg, sernom";
				$from= " $table2 "; 
				$llaves[0]=1;
				$where=NULL;  
				$j=0;
	
				$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o );

				while (odbc_fetch_row ($err_o))
				{
					if ($j==0)
					{
						echo "<tr>";

						echo "<td align=left class='texto3' COLSPAN=4 >RESULTADOS DE BUSQUEDA DE PACIENTES EGRESADOS: <BR><select name='egresados' STYLE='font-family : ariel; font-size : 75%' onchange='Escoger3()'>" ;
						echo "<option selected> </option>";
						$j++;
					}

					echo "<option>".odbc_result($err_o,1)."-".odbc_result($err_o,2)." - ".odbc_result($err_o,3)." ".odbc_result($err_o,4)." ".odbc_result($err_o,5)." - INGRESADO: ".odbc_result($err_o,6)." - EGRESADO: ".odbc_result($err_o,7)." - SERVICIO: ".odbc_result($err_o,9)."</option>";
				}
				echo "</td></tr>";
			}

			// POR ULTIMO BUSCAMOS LOS DE AYUDAS DIAGNOSTICAS

			
			
			$query=' ';
			$exp=explode('-', $tDoc);
			if (isset ($ndoc) and $ndoc!='')
			{
				$query=$query."movced='".$ndoc."' and ";
				$query=$query."movtid='".$exp[0]."'  and ";
			}
			if (isset ($pnom) and $pnom!='')
			{
				if (!isset ($snom) or $snom=='')
				{
					$query=$query."movnom like '%".strtoupper($pnom)."%' and ";
				}
				else
				{
					$query=$query."movnom like '%".strtoupper($pnom)." ".strtoupper($snom)."%' and ";
				}
			}

			if (isset ($snom) and $snom!='' and $snom !='- -' and (!isset ($pnom) or $pnom=='' or $pnom=='- -'))
			{
				$query=$query."movnom like '%".strtoupper($snom)."%' and ";
			}

			if (isset ($pape) and $pape!='')
			$query=$query."movape='".strtoupper($pape)."' and ";

			if (isset ($sape) and $sape!='' and $sape !='- -')
			$query=$query."movap2='".strtoupper($sape)."' and ";
			else if (!isset($sape))
			$sape ='- -';


			$increment++;
			$table3=date("Mdhis").$increment;
			
			if ($query != ' ')
			{
				$query=substr($query,0,-4);
				$query2="select movced, movtid,  movnom, movape, movap2, movfec, sernom  "
				."from aymov, inser   "
				."where movfec between '".$fecha1."' and '".$fecha2."' and ".$query. " and sercod=movsin order by sernom into temp $table3";//inpac-> casi toda la información

				//echo"<br> query aymov <br>".$query2;    
				$err_o = odbc_exec($conex_o,$query2);
				
				$select= " movced, movtid, movnom, movape, '".$long."' as movap2, movfec, sernom";
				$from= " $table3 "; 
				$llaves[0]=1;
				$where=NULL;  
				
				$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o );
				$j=0;


				while (odbc_fetch_row ($err_o))
				{
					if ($j==0)
					{
						echo "<tr>";

						echo "<td align=left class='texto3' COLSPAN=4 >RESULTADOS DE BUSQUEDA DE PACIENTES EN AYUDAS DIAGNOSTICAS: <BR><select name='ayudas' STYLE='font-family : ariel; font-size : 75%' onchange='Escoger4()'>" ;
						echo "<option selected> </option>";
						$j++;
					}

					echo "<option>".odbc_result($err_o,1)."-".odbc_result($err_o,2)." - ".odbc_result($err_o,3)." ".odbc_result($err_o,4)." ".odbc_result($err_o,5)." - INGRESADO: ".odbc_result($err_o,6)." - SERVICIO: ".odbc_result($err_o,7)."</option>";
				}
				echo "</td></tr>";
			}

		}

	}

	echo "</tr></table></br>";

	//primera vez que se ingresa o no se ha seleccionado aun un paciente, inicializamos datos
	if (!isset ($bandera) or $bandera==0 or $bandera==2 )
	{
		$doc='';
		$tipDoc='CC-CEDULA DE CIUDADANIA';
		$his='';
		$priNom='';
		$segNom='';
		$priApe='';
		$segApe='';
		$dir='';
		$tel='';
		$ema='';
	}

	//se ha escogido un dato de Matrix, buscamos todos los datos del paciente seleccionado en matrix
	if (isset($bandera) and $bandera==3)
	{
		$exp=explode('-', $matrix);

		if (isset($exp[7]))
		{
			$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpetel, cpedir, cpeema  FROM " .$wbasedato."_000016 where cpedoc='".$exp[0]."-".$exp[1]."' and cpetdoc='".$exp[2]."-".$exp[3]."' ";
		}
		else
		{
			$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpetel, cpedir, cpeema  FROM " .$wbasedato."_000016 where cpedoc='".$exp[0]."' and cpetdoc='".$exp[1]."-".$exp[2]."' ";
		}

		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_row($err);
			$doc=$row[0];
			$tipDoc=$row[1];
			$priNom=$row[2];
			$segNom=$row[3];
			$priApe=$row[4];
			$segApe=$row[5];
			$dir=$row[7];
			$tel=$row[6];
			$ema=$row[8];

			//se inciializan en vacio en caso de que no tengan ningun valor
			if (!isset($his) or $his=='')
			{
				$his='-';
			}
			if (!isset($res) or $res=='')
			{
				$res='-';
			}
			if (!isset($ser) or $ser=='')
			{
				$ser='-';
			}
			if (!isset($priNom) or $priNom=='')
			{
				$priNom='-';
			}
			if (!isset($segNom) or $segNom=='')
			{
				$segNom='-';
			}
			if (!isset($priApe) or $priApe=='')
			{
				$priApe='-';
			}
			if (!isset($segApe) or $segApe=='')
			{
				$segApe='-';
			}
			if (!isset($dir) or $dir=='')
			{
				$dir='-';
			}
			if (!isset($tel) or $tel=='')
			{
				$tel='-';
			}
			if (!isset($ema) or $ema=='')
			{
				$ema='-';
			}
		}
		$senal=1;
	}

	//se ha escogido un dato de inpac, buscamos todos los datos del paciente seleccionado en matrix
	if (isset($bandera) and $bandera==4)
	{
		$exp=explode('-', $activos);
		
		$long='                         ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
		$increment=0;
		$increment++;
		
		if (isset($exp[7]))
		{
			$increment++;
			$table8=date("Mdhis").$increment;
			//se agrega funcion que valida los nulos
			$exp2=explode(':',$exp[4]);
			$query="select pacced, pactid,  pacnom, pacap1, pacap2, pacdir, pactel, pachis,  pacres, sernom "
			."from inpac,  inmtra  , outer inser   "
			."where pacced='".trim($exp[0]). "-".trim($exp[1]). "' and pactid='".trim($exp[2]). "' and pacfec='".trim($exp2[1]). "/".trim($exp[5]). "/".trim($exp[6]). "' and trahis=pachis and tranum=pacnum and traegr is null and sercod=traser into temp $table8";//inpac-> casi toda la información
			
			$err_o = odbc_exec($conex_o,$query);
			
			$select= " pacced, pactid,  pacnom, pacap1, '".$long."' as pacap2, pacdir, pactel, pachis,  '".$long."' as pacres, sernom";
			$from= " $table8 "; 
			$llaves[0]=1;
			$where=NULL;  
				
			$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o );
		}
		else
		{
			$increment++;
			$table7=date("Mdhis").$increment;
			//se agrega funcion que valida los nulos
			$exp2=explode(':',$exp[3]);
			
			$query="select pacced, pactid,  pacnom, pacap1, pacap2, pacdir, pactel, pachis,  pacres, sernom "
			."from inpac,  inmtra  , outer inser   "
			."where pacced='".trim($exp[0]). "' and pactid='".trim($exp[1]). "' and pacfec='".trim($exp2[1]). "/".trim($exp[4]). "/".trim($exp[5]). "' and trahis=pachis and tranum=pacnum and traegr is null and sercod=traser into temp $table7";//inpac-> casi toda la información
			
			
			$err_o = odbc_exec($conex_o,$query);
			
			$select= " pacced, pactid, pacnom, pacap1, '".$long."' as pacap2, pacdir, pactel, pachis, '".$long."' as pacres, sernom";
			$from= " $table7 "; 
			$llaves[0]=1;
			$where=NULL;  
				
			$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o ); 
		}
		

		while (odbc_fetch_row ($err_o))
		{
			$doc=odbc_result($err_o,1);
			$tipDoc=engrandar(trim(odbc_result($err_o,2)));
			$exp=explode(' ',odbc_result($err_o,3) );
			$priNom=$exp[0];
			$segNom=$exp[1];
			$priApe=odbc_result($err_o,4);
			$segApe=odbc_result($err_o,5);
			$dir=odbc_result($err_o,6);
			$tel=odbc_result($err_o,7);
			$ema='';
			$his=odbc_result($err_o,8);
			$res=odbc_result($err_o,9);
			$ser=odbc_result($err_o,10);
		}

		//se inciializan en vacio en caso de que no hayan sido enviados por otros programas
		if (!isset($his) or $his=='')
		{
			$his='-';
		}
		if (!isset($res) or $res=='')
		{
			$res='-';
		}
		if (!isset($ser) or $ser=='')
		{
			$ser='-';
		}
		if (!isset($priNom) or $priNom=='')
		{
			$priNom='-';
		}
		if (!isset($segNom) or $segNom=='')
		{
			$segNom='-';
		}
		if (!isset($priApe) or $priApe=='')
		{
			$priApe='-';
		}
		if (!isset($segApe) or $segApe=='')
		{
			$segApe='-';
		}
		if (!isset($dir) or $dir=='')
		{
			$dir='-';
		}
		if (!isset($tel) or $tel=='')
		{
			$tel='-';
		}
		if (!isset($ema) or $ema=='')
		{
			$ema='-';
		}
	}

	//se ha escogido un dato de inpaci, buscamos todos los datos del paciente seleccionado en matrix
	if (isset($bandera) and $bandera==5)
	{
		$exp=explode('-', $egresados);
				
		$long='                         ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
		$increment=0;
		$increment++;
		
		if (isset($exp[10]) and $exp[10]!='OBSTETRICIA')
		{
			$exp2=explode(':',$exp[4]);
			
			//se agrega la funcion de nulos
			$increment++;
			$table5=date("Mdhis").$increment;
			
			$query="select pacced, pactid,  pacnom, pacap1, pacap2, pacdir, pactel, pachis, sernom, empnom "
			."from inpaci,  inmegr  , inser, outer inemp  "
			."where pacced='".trim($exp[0]). "-".trim($exp[1]). "' and pactid='".trim($exp[2]). "' and pacing='".trim($exp2[1]). "-".trim($exp[5]). "-".trim($exp[6]). "' and egrhis=pachis and egrnum=pacnum and sercod=egrseg and empcod=egrcer into temp $table5";//inpac-> casi toda la información
			
			$err_o = odbc_exec($conex_o,$query);
			
			$select= " pacced, pactid,  pacnom, pacap1, '".$long."' as pacap2, pacdir, pactel, pachis, sernom, '".$long."' as empnom ";
				$from= " $table5 "; 
				$llaves[0]=1;
				$where=NULL;  
				
				$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o );
			
		}
		else
		{
			$exp2=explode(':',$exp[3]);
			//se agrega la funcion para nulos
			
			$increment++;
			$table4=date("Mdhis").$increment;
			
			$query="select pacced, pactid,  pacnom, pacap1, pacap2, pacdir, pactel, pachis, sernom, empnom "
			."from inpaci,  inmegr  , inser, outer inemp  "
			."where pacced='".trim($exp[0]). "' and pactid='".trim($exp[1]). "' and pacing='".trim($exp2[1]). "-".trim($exp[4]). "-".trim($exp[5]). "' and egrhis=pachis and egrnum=pacnum and sercod=egrseg and empcod=egrcer into temp $table4";//inpac-> casi toda la información
			
			$err_o = odbc_exec($conex_o,$query);
			
					
			$select= " pacced, pactid, pacnom, pacap1, '".$long."' as pacap2, pacdir, pactel, pachis, sernom, '".$long."' as empnom ";
				$from= " $table4 "; 
				$llaves[0]=1;
				$where=NULL;  
				
				$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o );
		}

		
		while (odbc_fetch_row ($err_o))
		{
			$doc=odbc_result($err_o,1);
			$tipDoc=engrandar(trim(odbc_result($err_o,2)));
			$exp=explode(' ',odbc_result($err_o,3) );
			$priNom=$exp[0];
			$segNom=$exp[1];
			$priApe=odbc_result($err_o,4);
			$segApe=odbc_result($err_o,5);
			$dir=odbc_result($err_o,6);
			$tel=odbc_result($err_o,7);
			$ema='';
			$his=odbc_result($err_o,8);
			$res=odbc_result($err_o,10);
			$ser=odbc_result($err_o,9);

		}

		//se inciializan en vacio en caso de que no hayan sido enviados por otros programas
		if (!isset($his) or $his=='')
		{
			$his='-';
		}
		if (!isset($res) or $res=='')
		{
			$res='-';
		}
		if (!isset($ser) or $ser=='')
		{
			$ser='-';
		}
		if (!isset($priNom) or $priNom=='')
		{
			$priNom='-';
		}
		if (!isset($segNom) or $segNom=='')
		{
			$segNom='-';
		}
		if (!isset($priApe) or $priApe=='')
		{
			$priApe='-';
		}
		if (!isset($segApe) or $segApe=='')
		{
			$segApe='-';
		}
		if (!isset($dir) or $dir=='')
		{
			$dir='-';
		}
		if (!isset($tel) or $tel=='')
		{
			$tel='-';
		}
		if (!isset($ema) or $ema=='')
		{
			$ema='-';
		}
	}

	//se ha escogido un dato de aymov, buscamos todos los datos del paciente seleccionado en matrix
	if (isset($bandera) and $bandera==6)
	{
		$exp=explode('-', $ayudas);
		
		
		$long='                         ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
		$increment=0;
		$increment++;
		
		if (isset($exp[7]))
		{
		$increment++;
		$table6=date("Mdhis").$increment;
		
			$exp2=explode(':',$exp[4]);
			$query="select movced, movtid,  movnom, movape, movap2, movdir, movtel, movdoc, sernom, empnom "
			."from aymov,  outer inser, outer inemp   "
			."where movced='".trim($exp[0]). "-".trim($exp[1]). "' and movtid='".trim($exp[2]). "' and movfec='".trim($exp2[1]). "-".trim($exp[5]). "-".trim($exp[6]). "' and sercod=movsin and empcod=movcer into temp $table6";//inpac-> casi toda la información
			
			$err_o = odbc_exec($conex_o,$query);
			
			$select= " movced, movtid,  movnom, movape, '".$long."' as movap2, movdir, movtel, movdoc, sernom, '".$long."' as empnom";
				$from= " $table6 "; 
				$llaves[0]=1;
				$where=NULL;  
				
				$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o ); 

		}
		else
		{
		$increment++;
		$table5=date("Mdhis").$increment;
		
			$exp2=explode(':',$exp[3]);
			$query="select movced, movtid,  movnom, movape, movap2, movdir, movtel, movdoc, sernom, empnom "
			."from aymov,  outer inser, outer inemp   "
			."where movced='".trim($exp[0]). "' and movtid='".trim($exp[1]). "' and movfec='".trim($exp2[1]). "-".trim($exp[4]). "-".trim($exp[5]). "' and sercod=movsin and empcod=movcer into temp $table5";//inpac-> casi toda la información
			
			
			$err_o = odbc_exec($conex_o,$query);
			
			$select= " movced, movtid,  movnom, movape, '".$long."' as movap2, movdir, movtel, movdoc, sernom, '".$long."' as empnom";
				$from= " $table5 "; 
				$llaves[0]=1;
				$where=NULL;  
				
				$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o ); 
		}

		

		while (odbc_fetch_row ($err_o))
		{
			$doc=odbc_result($err_o,1);
			$tipDoc=engrandar(trim(odbc_result($err_o,2)));
			$exp=explode(' ',odbc_result($err_o,3) );
			$priNom=$exp[0];
			$segNom=$exp[1];
			$priApe=odbc_result($err_o,4);
			$segApe=odbc_result($err_o,5);
			$dir=odbc_result($err_o,6);
			$tel=odbc_result($err_o,7);
			$ema='';
			$his=odbc_result($err_o,8);
			$res=odbc_result($err_o,10);
			$ser=odbc_result($err_o,9);

		}

		//se inciializan en vacio en caso de que no hayan sido enviados por otros programas
		if (!isset($his) or $his=='')
		{
			$his='-';
		}
		if (!isset($res) or $res=='')
		{
			$res='-';
		}
		if (!isset($ser) or $ser=='')
		{
			$ser='-';
		}
		if (!isset($priNom) or $priNom=='')
		{
			$priNom='-';
		}
		if (!isset($segNom) or $segNom=='')
		{
			$segNom='-';
		}
		if (!isset($priApe) or $priApe=='')
		{
			$priApe='-';
		}
		if (!isset($segApe) or $segApe=='')
		{
			$segApe='-';
		}
		if (!isset($dir) or $dir=='')
		{
			$dir='-';
		}
		if (!isset($tel) or $tel=='')
		{
			$tel='-';
		}
		if (!isset($ema) or $ema=='')
		{
			$ema='-';
		}
	}

	//se ha seleccionado ingresar los datos de un paciente seleccionado a matrix
	if (isset($bandera) and $bandera==7)
	{
		//el sistema busca en matrix si ya esta guardada una persona con el documento
		$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpetel, cpedir, cpeema  FROM " .$wbasedato."_000016 where cpedoc='".$doc."' and cpetdoc='".$tipDoc."' ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		//si ya existe en matrix, se muestran los datos anteriores para que el usuario compare
		if($num>0)
		{
			$row=mysql_fetch_row($err);

			echo "<center><font color=#336699><b>EL PACIENTE YA EXISTE EN MATRIX CON LOS SIGUIENTES DATOS:   </font></b></center></br>";
			echo "<table align='center' class=borde >\n";
			echo "</select></td>";
			echo "</tr>";
			echo "<tr>\n";
			echo "<td>PRIMER NOMBRE: ".$row[2]."</td>" ;
			echo "</tr>\n" ;
			echo "<tr>\n";
			echo "<td>SEGUNDO NOMBRE: ".$row[3]."</td>" ;
			echo "</tr>\n" ;
			echo "<tr>\n";
			echo "<td>PRIMER APELLIDO: ".$row[4]."</td>" ;
			echo "</tr>\n" ;
			echo "<tr>\n";
			echo "<td>SEGUNDO APELLIDO: ".$row[5]."</td>" ;
			echo "</tr>\n" ;
			echo "<tr>\n";
			echo "<td>DIRECCION: ".$row[7]."</td>" ;
			echo "</tr>\n" ;
			echo "<tr>\n";
			echo "<td>TELEFONO: ".$row[6]."</td>" ;
			echo "</tr>\n" ;
			echo "<tr>\n";
			echo "<td>EMAIL: ".$row[8]."</td>" ;
			echo "</tr>\n" ;
			echo "</table></br> ";
			if (!isset ($his))
			{
				$his='-';
			}
			if (!isset ($ser))
			{
				$ser='-';
			}
			if (!isset ($res))
			{
				$res='-';
			}
			$senal=2;


		}
		//si no existe en matrix, se realiza el ingreso a la bd
		else
		{

			$query= " INSERT INTO  " .$wbasedato."_000016 (medico, Fecha_data, Hora_data, cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpetel, cpedir, cpeema, seguridad)";
			$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".strtoupper($doc)."', '".strtoupper($tipDoc)."','".strtoupper($priNom)."', '".strtoupper($segNom)."','".strtoupper($priApe)."','".strtoupper($segApe)."','".strtoupper($tel)."', '".strtoupper($dir)."', '".$ema."', 'A-magenta') ";
			//echo $query;
			$err=mysql_query($query,$conex);

			echo "<font size=3 color=#000080><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffcc99 LOOP=-1>EL PACIENTE HA SIDO INGRESADO CORRECTAMENTE</MARQUEE></FONT></br></br>";
			$senal=1; //indicara que un paciente fue ingresado y que se le puede crear un comentario
		}
	}

	//se ha seleccionado actualizar los datos del paciente seleccionado
	if (isset($bandera) and $bandera==8)
	{
		//SE ACTUALIZAN LOS DATOS DEL PACIENTE
		$query= " UPDATE " .$wbasedato."_000016 SET Fecha_data='".date("Y-m-d")."',  Hora_data='".date("h:i:s")."', cpeno1='".strtoupper($priNom)."', cpeno2='".strtoupper($segNom)."', cpeap1='".strtoupper($priApe)."', cpeap2='".strtoupper($segApe)."', cpetel='".strtoupper($tel)."', cpedir='".strtoupper($dir)."', cpeema='".$ema."' ";
		$query= $query. " where cpedoc='".strtoupper($doc)."'AND cpetdoc='".strtoupper($tipDoc)."' ";

		//echo $query;
		$err=mysql_query($query,$conex);

		echo "<font size=3 color=#000080><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffcc99 LOOP=-1>EL PACIENTE HA SIDO ACTUALIZADO CORRECTAMENTE</MARQUEE></FONT></br></br>";
		$senal=3; //indicara que un paciente fue ingresado y que se le puede crear un comentario
	}

	////////////////tabla que muestra los datos del paciente seleccionado/////////////////////////////////////////////

	echo "<center><font color=#336699><b>DATOS DEL PACIENTE PARA INGRESAR:   </font></b></center></br>";
	echo "<table align=\"center\">\n";
	echo "<tr>\n";
	echo "<td>*DOCUMENTO:</td>" ;
	echo "<td><input type='text' name='doc' value='$doc'></td>" ;
	echo "</tr>\n" ;
	echo "<tr>" ;
	echo "<td>*TIPO DE DOCUMENTO:</td>";
	echo "<td><select name='tipDoc'>" ;

	$query="select Subcodigo, Descripcion from det_selecciones where medico='magenta' and codigo='01'";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	if  ($num >0)
	{
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_row($err);
			$tipDocS[$i]=$row[0]."-".$row[1];
			if($tipDocS[$i] == strtoupper($tipDoc))
			echo "<option selected>".$tipDocS[$i]."</option>";
			else
			echo "<option>".$tipDocS[$i]."</option>";
		}

	}else
	{
		DisplayError;
	}

	echo "</select></td>";
	echo "</tr>";
	echo "<tr>\n";
	echo "<td>*PRIMER NOMBRE:</td>" ;
	echo "<td><input type='text' name='priNom' value='$priNom'></td>" ;
	echo "</tr>\n" ;
	echo "<tr>\n";
	echo "<td>SEGUNDO NOMBRE:</td>" ;
	echo "<td><input type='text' name='segNom' value='$segNom'></td>" ;
	echo "</tr>\n" ;
	echo "<tr>\n";
	echo "<td>*PRIMER APELLIDO:</td>" ;
	echo "<td><input type='text' name='priApe' value='$priApe'></td>" ;
	echo "</tr>\n" ;
	echo "<tr>\n";
	echo "<td>SEGUNDO APELLIDO:</td>" ;
	echo "<td><input type='text' name='segApe' value='$segApe'></td>" ;
	echo "</tr>\n" ;
	echo "<tr>\n";
	echo "<td>DIRECCION:</td>" ;
	echo "<td><input type='text' name='dir' value='$dir'></td>" ;
	echo "</tr>\n" ;
	echo "<tr>\n";
	echo "<td>TELEFONO:</td>" ;
	echo "<td><input type='text' name='tel' value='$tel'></td>" ;
	echo "</tr>\n" ;
	echo "<tr>\n";
	echo "<td>EMAIL:</td>" ;
	echo "<td><input type='text' name='ema' value='$ema'></td>" ;
	echo "</tr>\n" ;

	//en caso de que no sean datos de matrix pueden mostrarse el servicio y la historia de unix
	if (isset ($bandera) and $bandera>3 and isset($his) and isset($ser) and isset($res))
	{

		echo "<tr>\n";
		echo "<td colspan=2 align=center >&nbsp;</td>" ;
		echo "</tr>\n" ;

		echo "<tr>\n";
		echo "<td colspan=2 align=center ><font color=#336699><b>DATOS DEL COMENTARIO<font></td>" ;
		echo "</tr>\n" ;

		echo "<tr>\n";
		echo "<td>HISTORIA CLINICA:</td>" ;
		echo "<td><input type='text' name='his' value='$his'></td>" ;
		echo "</tr>\n" ;
		echo "<tr>\n";
		echo "<td>SERVICIO:</td>" ;
		echo "<td><input type='text' name='ser' value='$ser' size='30'></td>" ;
		echo "</tr>\n" ;
		echo "<tr>\n";
		echo "<td>ENTIDAD:</td>" ;
		echo "<td><input type='text' name='res' value='$res' size='30'></td>" ;
		echo "</tr>\n" ;
	}
	if (!isset($bandera) or $bandera<=3)
	{
		if (!isset ($his))
		{
			$his='-';
		}
		if (!isset ($ser))
		{
			$ser='-';
		}
		if (!isset ($res))
		{
			$res='-';
		}
		echo "<td><input type='hidden' name='his' value='$his'></td>" ;
		echo "<td><input type='hidden' name='res' value='$res'></td>" ;
		echo "<td><input type='hidden' name='ser' value='$ser'></td>" ;
	}

	echo "<tr>\n" ;
	echo "<td>\n" ;
	echo "&nbsp;\n" ;
	echo "</td>\n" ;
	echo "<td>\n" ;
	echo "&nbsp;\n" ;
	echo "</td>\n" ;
	echo "</tr> \n" ;
	echo "<tr>\n" ;
	echo "<input type='hidden' name='bandera1' value='1' />";

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//segun la ejecucion del programa, si el paciente existe o no en bd de Matrix se muestran las opciones; Ingresar, actualizar y comentarios
	switch ($senal)
	{
		case 0:
		{
			echo "<td colspan=2 align=\"center\"><input type=\"button\" name=\"ingresar\" value=\"INGRESAR\" onclick='Seleccionar2()'/></td>" ;
			break;

		}

		case 1:
		{
			echo "<td  align=\"center\"><input type=\"button\" name=\"actualizar\" value=\"ACTUALIZAR\" onclick='Seleccionar3()'/></td>" ;
			echo "<td><b><font size=\"4\"><A HREF='comentario.php?doc=".$doc."&tipDoc=".$tipDoc."&his=".$his."&res=".urlencode($res)."&ser=".$ser."'  target='_self'><font color=\"#336699\">COMENTARIOS</a></b></font></font></td>";
			break;
		}

		case 2:
		{
			echo "<td  align=\"center\"><input type=\"button\" name=\"actualizar\" value=\"ACTUALIZAR\" onclick='Seleccionar3()'/></td>" ;
			echo "<td><b><font size=\"4\"><A HREF='comentario.php?doc=".$doc."&tipDoc=".$tipDoc."&his=".$his."&res=".urlencode($res)."&ser=".$ser."'   target='_self'><font color=\"#336699\">COMENTARIOS</a></b></font></font></td>";
			break;
		}
		case 3:
		{
			echo "<td  align=\"center\"><input type=\"button\" name=\"actualizar\" value=\"ACTUALIZAR\" onclick='Seleccionar3()'/></td>" ;
			echo "<td><b><font size=\"4\"><A HREF='comentario.php?doc=".$doc."&tipDoc=".$tipDoc."&his=".$his."&res=".urlencode($res)."&ser=".$ser."'   target='_self'><font color=\"#336699\">COMENTARIOS</a></b></font></font></td>";
			break;
		}
	}

	echo "</form>\n" ;
}
?>