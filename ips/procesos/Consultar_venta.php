<?php
include_once("conex.php");
//VAlidación de usuario
$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}

if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado and !isset($peticionAjax) )
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";
}
if (!$usuarioValidado and isset($peticionAjax) )
{
	echo "session die";
}
// Fin IF si el usuario no es válido
require_once("conex.php");


function anular_factura($wffue, $wffac)
{
	global $wbasedato;
	global $wemp_pmla;
	global $conex;
	global $wuser;
	global $wfecha;
	global $hora;
	global $wusuario;
	global $tablaConceptos;

	///aca hay que seguir anulando en la tabla 66 y 65 y en la 106 pero sabiendo si es el responsable principal o no, para saber en que campo
	// de la tabla 106 se devuelve la plata o el valor.

	//Verificar que no tenga recibo de caja, que no halla sido enviada o radicada.
	//Verificar que no tenga nota credito o debito

	//BUSCO SI LA FACTURA TIENE ALGUN DOCUMENTO ANEXO DESPUES DE HECHA
	$q = " SELECT count(*) "
	 ."   FROM ".$wbasedato."_000021 "
	 ."  WHERE rdeffa  = '".$wffue."'"
	 ."    AND rdefac  = '".$wffac."'"
	 ."    AND rdeest  = 'on' "
	 ."    AND rdereg  = '' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	if ($row[0] == 0)
	{
	 //BUSCO QUE LA FACTURA TENGA EL ESTADO GENERADA, QUE ES EL UNICO EN QUE SE PUEDE ANULAR
	 $q = " SELECT count(*) "
	     ."   FROM ".$wbasedato."_000018 "
	     ."  WHERE fenffa = '".$wffue."'"
	     ."    AND fenfac = '".$wffac."'"
	     ."    AND fenest = 'on' "
	     ."    AND fenesf ='GE' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $num = mysql_num_rows($res);
	 $row = mysql_fetch_array($res);
	 if (($row[0] > 0))
	    {
	     //ACA BUSCO QUE LA FACTURA CORRESPONDA AL PERIODO CONTABLE ACTUAL
	     //DE LO CONTRARIO NO SE DEJA ANULAR
	     $q = " SELECT count(*) "
		     ."   FROM ".$wbasedato."_000018 "
		     ."  WHERE fenffa = '".$wffue."'"			//fenffa=fuente factura
		     ."    AND fenfac = '".$wffac."'"			//fenfac=Numero factura
		     ."    AND fenest = 'on' "
		     ."    AND fenesf ='GE' "
		     ."    AND year(fenfec) = '".date("Y")."'"
		     ."    AND month(fenfec) = '".date("m")."'";
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $num = mysql_num_rows($res);
		 $row = mysql_fetch_array($res);

		 if ($row[0]>0)
	        {
		     $q = "UPDATE ".$wbasedato."_000018 "
			     ."   SET fenest = 'off' "
			     ." WHERE fenffa = '".$wffue."'"
			     ."   AND fenfac = '".$wffac."'"
			     ."   AND fenest = 'on' ";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 $q = "UPDATE ".$wbasedato."_000065 "
			     ."   SET fdeest = 'off' "
			     ." WHERE fdefue = '".$wffue."'"
			     ."   AND fdedoc = '".$wffac."'"
			     ."   AND fdeest = 'on' ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 //ELIMINO LA RELACION CON LOS ABONOS
			 $q = " UPDATE ".$wbasedato."_000021 "
		         ."    SET rdeffa  = '', "
		         ."        rdefac  = '' "
			     ."  WHERE rdeffa  = '".$wffue."'"
			     ."    AND rdefac  = '".$wffac."'"
			     ."    AND rdeest  = 'on' "
			     ."    AND rdereg != '' ";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 $q = "SELECT rcfffa, rcffac, rcfreg, rcfval, fencod, fenhis, fening, rcftip, gruabo, tcarfex, tcarfre "
			     ."  FROM ".$wbasedato."_000066, ".$wbasedato."_000018, ".$tablaConceptos.", ".$wbasedato."_000106 "
			     ." WHERE rcfffa     = '".$wffue."'"
			     ."   AND rcffac     = '".$wffac."'"
			     ."   AND rcfest     = 'on' "
			     ."   AND rcfffa     = fenffa "
			     ."   AND rcffac     = fenfac "
			     ."   AND rcfreg     = ".$wbasedato."_000106.id "
			     ."   AND tcarconcod = grucod "
			     ." GROUP BY 1,2,3,4,5,6,7,8,9 ";
			 $res_fac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $num = mysql_num_rows($res_fac);

			 if ($num > 0)
			    {
			     for ($i=1;$i<=$num;$i++)
			        {
				     $row = mysql_fetch_array($res_fac);

				     //ACA ACTIVO LOS CARGOS DE LA FACTURA QUE SE ESTA ANULANDO, EN LA PROPORCION QUE SE FACTURO
				     if ($row[7] == "E")           //Excedente. Este dato viene del campo 'rcftip' tipo de factura
				        {
					     if ($row[8] == "off")     //Si no es un abono
					        {
						     if ($row[9] > 0)      //Si si se facturo como excedente
						        {
						         $q = " UPDATE ".$wbasedato."_000106 "
							         ."    SET tcarfex  = tcarfex - ".$row[3]
							         ."  WHERE id       = ".$row[2]
							         ."    AND tcarest  = 'on' "
							         ."    AND tcarfex >= ".$row[3];
						        }
						       else                //Se facturo como excedente pero siendo reconocido -- Abril 16 de 2008
						          {
							       $q = " UPDATE ".$wbasedato."_000106 "
						         	   ."    SET tcarfre  = tcarfre - ".$row[3]
						               ."  WHERE id       = ".$row[2]
						               ."    AND tcarest  = 'on' "
						               ."    AND tcarfre >= ".$row[3];
							      }
						     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					        }
					       else                           //Si es Abono
					          {
						       if (abs($row[9]) > 0)      //Si si se facturo como excedente -- Abril 16 de 2008
						          {
								   $q = " UPDATE ".$wbasedato."_000106 "
								       ."    SET tcarfex       = tcarfex - ".$row[3]
								       ."  WHERE id            = ".$row[2]
								       ."    AND tcarest       = 'on' "
								       ."    AND abs(tcarfex) >= ".abs($row[3]);
							      }
							     else
							        {
								     $q = " UPDATE ".$wbasedato."_000106 "
								         ."    SET tcarfre       = tcarfre - ".$row[3]
								         ."  WHERE id            = ".$row[2]
								         ."    AND tcarest       = 'on' "
								         ."    AND abs(tcarfre) >= ".abs($row[3]);
								    }
							   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							  }
						}

				     if ($row[7] == "R")          //Reconocido. Este dato viene del campo 'rcftip' tipo de factura
				        {
					     if ($row[8] == "off")    //Si no es un abono
					        {
					         $q = " UPDATE ".$wbasedato."_000106 "
						         ."    SET tcarfre = tcarfre - ".$row[3]
						         ."  WHERE id                                  = ".$row[2]
						         ."    AND tcarest                             = 'on' "
						         ."    AND tcarfre                            >= ".$row[3];
						     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						    }
					       else                  //Si es Abono
					          {
							   $q = " UPDATE ".$wbasedato."_000106 "
							       ."    SET tcarfre = tcarfre - ".$row[3]
							       ."  WHERE id                                  = ".$row[2]
							       ."    AND tcarest                             = 'on' "
							       ."    AND abs(tcarfre)                        >= ".abs($row[3]);
							   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							  }
						}

					//Anulo el registro en la tabla 000066
					 $q = " UPDATE ".$wbasedato."_000066 "
				         ."    SET rcfest = 'off' "
				         ."  WHERE rcfreg = ".$row[2]
				         ."    AND rcfffa = '".$row[0]."'"
				         ."    AND rcffac = '".$row[1]."'";
				         //."    AND rcfest = 'on' ";
				     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				     //**************************
			         //Aca grabo la auditoria
			         //**************************
			         $q= " INSERT INTO ".$wbasedato."_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis    ,   auding    ,   audreg                , audacc         ,   audusu      , Seguridad) "
			            ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$row[5]."','".$row[6]."','".$row[0]."-".$row[1]."', 'Anulo Factura','".$wusuario."', 'C-".$wusuario."')";
			         $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				    }
				}
	         } //Fin del if ($num > 1) que indica que la factura es del mismo periodo al actual
	        else
	          {
			    return('LA FACTURA NO CORRESPONDE AL PERIODO ACTUAL, NO SE PUEDE ANULAR');
			  }
	    } //Fin del if ($num > 1)
	   else
	      {
		      return ("LA FACTURA YA ESTA ANULADA O YA HA SIDO ENVIADA NO SE PUEDE ANULAR");
		  }
	}
	else
	  {
	       return (" LA FACTURA TIENE UN DOCUMENTO ANEXO (Recibo o Nota), NO SE PUEDE ANULAR ");
	  }
	   return("ok");
}
if( $peticionAjax  == "anularFactura" ){
	$wfecha    = date('Y-m-d');
	$hora      = date("H:i:s");
	$wusuario  = $wuser;
	$anulacion = str_replace("\\", "", $anulacion);
	$anulacion = json_decode( $anulacion, true );

	$respuesta = anular_factura($anulacion['fuenteFactura'], $anulacion['numeroFactura']);
	if( $respuesta == "ok" ){
		foreach( $anulacion['causas'] as $key=>$codigoCausa ){

			$q = "INSERT INTO {$wbasedato}_000071 (`Medico`,`Fecha_data`,`Hora_data`,`Docfue`,`Docnum`,`Doccau`,`Docest`,`Seguridad`)
					  VALUES ('{$wbasedato}', '{$wfecha}', '{$hora}', '{$anulacion['fuenteFactura']}', '{$anulacion['numeroFactura']}', '{$codigoCausa}', 'on', 'C-{$wuser}')";
			$res = mysql_query( $q, $conex );
		}

		if( trim($anulacion['observacion']) != "" ){
			$q = "UPDATE {$wbasedato}_000018
				     SET Fenobs = '".utf8_decode($anulacion['observacion'])."'
				   WHERE Fenffa = '{$anulacion['fuenteFactura']}'
				     AND Fenfac = '{$anulacion['numeroFactura']}'";
			$res = mysql_query( $q, $conex );
		}
	}
	echo $respuesta;
	return;
}
?>

<html>
<head>
<title>CONSULTAR VENTA IPS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style>
	.botona{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			border:0px;
			margin-left: 1%;
			cursor: pointer;
		 }
	// --> Estilo
	fieldset{
		border: 2px solid #e2e2e2;
	}

	legend{
		border: 2px solid #e2e2e2;
		border-top: 0px;
		font-family: Verdana;
		background-color: #e6e6e6;
		font-size: 11pt;
	}
</style>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script src="../../../include/root/toJson.js" type="text/javascript"></script>
<script type="text/javascript">
	function enter()
	{
	   document.forms.consultar_venta.submit();
	}

	function enter1()
	{
	   document.forms.consultar_venta.submit();
	   alert ("Pulse de nuevo la tecla ENTER");
	}

	function anular()
   	  {
		/*if(confirm('¿Está seguro que desea ** ANULAR ** la Factura?')) {
			location.href=url;
	   	}*/
	   	$("#tr_causas").toggle();
	   	/*if($("#tr_causas").is(":visible") && $("tr[tipo='causa']").size() <= 0 ){
	   		alerta( "Para realizar la anulación \n debe Seleccionar la(s) Causa(s)" );
	   	}*/

   	  }
	function valida_enviar(form)
	{
		//Inicio de validaciones
		if(form.wfue.value!='' && (form.wfac.value=='' || form.wfac.value=='%'))
			 {
			   alert("Debe entrar tambien un valor para la factura");
			   form.wfue.focus();
			   return false;
			 }

		form.submit();
	}

	function agregarCausa( select ){
		var select = jQuery( select );
		var causaSeleccionada = select.find("option:selected");
		var codigoCausa = $( causaSeleccionada ).val();
		var descripcion = $( causaSeleccionada ).html();
		if( $("tr[tipo='causa']").size() % 2 == 1 )
			clase = 'fila1';
			else
				clase = 'fila2';
		var fila = "<tr tipo='causa' codigoCausa='"+codigoCausa+"' texto='"+descripcion+"' class='"+clase+"' colspan='2'><td><div align='left' style='width:100%;'><font size='3' color='blue' style='cursor:pointer;' onclick='eliminarCausa(this)'>Eliminar</font>&nbsp;&nbsp;&nbsp;"+codigoCausa+"&nbsp;&nbsp;"+descripcion+"</div></td></tr>";
		$("#tr_slc").after( fila );
		$(causaSeleccionada).remove();
	}

	function eliminarCausa(eliminar){
		var fila = $(eliminar).parent().parent().parent();
		var codigoCausa = $( fila ).attr("codigoCausa");
		var descripcion = $( fila ).attr("texto");
		var opcion = "<option value='"+codigoCausa+"'>"+descripcion+"</option>";
		$("#cauSel").append(opcion);
		$(fila).remove();
	}

	function alerta( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
							$.unblockUI();
						}, 2000 );
	}

	function anularFactura( url ){

		var fuenteFactura = $("#fuenteFactura").val();
		var numeroFactura = $("#numeroFactura").val();
		var observacion   = $("#wobs").val();
		var anulacion     = new Object();
		var wbasedato     = $("#wbasedato").val();
		var wusuario      = $("#wusuario").val();
		var tablaConceptos= $("#tablaConceptos").val();
		if( $("tr[tipo='causa']").size() == 0 ){
			alerta("Debe seleccionar al menos una Causa");
			return;
		}
		anulacion.fuenteFactura = fuenteFactura;
		anulacion.numeroFactura = numeroFactura;
		anulacion.observacion   = observacion;
		anulacion.causas        = new Array();

		$("tr[tipo='causa']").each(function (x){
			anulacion.causas.push( $(this).attr('codigoCausa') );
		});

		anulacion = $.toJSON( anulacion );

		$.ajax({
                 url: "Consultar_venta.php",
                type: "POST",
               async: false,
              before: $.blockUI({ message: $('#msjEspere') }),
                data: {
                      peticionAjax: "anularFactura",
				   		 wemp_pmla: $("#wemp_pmla").val(),
			             anulacion: anulacion,
			             wbasedato: wbasedato,
						 tablaConceptos: tablaConceptos
                      },
                success: function(data)
                {
                	$.unblockUI();
                	if( data != "ok" ){
                		alerta( data );
                	}else{
                		location.href=url;
                	}
                }
            });
	}
	function retornar( url ){
		window.location = url;
	}

</script>

</head>
<body>
<?php
  /************************************************
   *     PROGRAMA PARA LA CONSULTA DE VENTAS    *
   ************************************************/

//==========================================================================================================================================
//PROGRAMA				      : CONSULTA DE VENTAS                                                             |
//AUTOR                       : FELIPE ALVAREZ.																						|
//FECHA CREACION              : ENERO 11 de 2016																							|
//FECHA ULTIMA ACTUALIZACION  : ABRIL 06 de 2016.                                                                                       |
//DESCRIPCION			      : El programa permite visualizar informacion asociada a una venta, consultando tablas que afectan solo a las ventas, sin	
//                              estar ligadas a la facturacion. la construccion del script esta basada en Consultar_facturas.php 
//                                                                                                                                          |
//==========================================================================================================================================

//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES
// ABRIL 06 de 2016 Felipe Alvarez Se consulta el descuento por cada articulo asociado a la venta

//========================================================================================================================================\\
//	
include_once("root/comun.php");

$wactualiz="2016-04-06";
session_start();

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("CONSULTA DE VENTAS",$wactualiz,"clinica");

// Función que permite cambiar el texto enviado por URL para evitar conversión de caracteres
function convertir_url ($cadena) {
	$cadena = str_replace("%","___",$cadena);
	return $cadena;
}

// Retorna el valor original de los parámetros de URL que se cambió con la función anterior
function desconvertir_url ($cadena) {
	$cadena = str_replace("___","%",$cadena);
	return $cadena;
}

if (!$usuarioValidado and !isset($peticionAjax) )
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";
}
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte

 //Conexion base de datos
 



 // Consulto los datos de la empresa actual y los asigno a la variable $empresa
 $consulta = consultarInstitucionPorCodigo($conex, $wemp_pmla);
 $empresa = $consulta->baseDeDatos;

	echo "<form name='consultar_venta' id='consultar_venta' action='Consultar_venta.php' method=post onSubmit='return valida_enviar(this);'>";


    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////
    //Aca traigo las variables necesarias de la empresa
	$q = " SELECT empdes, emphos "
	    ."   FROM root_000050 "
	    ."  WHERE empcod = '".$wemp_pmla."'"
	    ."    AND empest = 'on' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	$wnominst=$row[0];
	$whosp=$row[1];

	/////////////////////////////////////////////////////////////////////////////////////////
	//Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	$q = " SELECT detapl, detval, empdes, empbda, emphos "
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

	      $wbasedato=strtolower($row[3]);   //Base de dato de la empresa
	      $wemphos=$row[4];     //Indica si la facturacion es Hospitalaria o POS

	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];

	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];

	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];

	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];

	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];

	      $winstitucion=$row[2];
	     }
	   }
	  else
	    echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	/////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////

	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	echo "<input type='hidden' id='tablaConceptos' value='".$tablaConceptos."'>";
	//----------------------------------------------------------------------------------------------

  $wcol=6;  //Numero de columnas que se tienen o se muestran en pantalla

  //===========================================================================================================================================
  function ver_factura($wffue, $wffac)
    {

	 global $wbasedato;
	 global $wemp_pmla;
	 global $conex;
	 global $wcf;
	 global $wcf2;
	 global $wclfa;
	 global $wclfg;
	 global $whosp;
	 global $tablaConceptos;
	 global $wuser;

	 $doctores = array();
	 $query = "SELECT meddoc
				 FROM {$wbasedato}_000051";
	 $rs    = mysql_query( $query, $conex );

	 while( $rowmed = mysql_fetch_array( $rs ) ){
		( !isset( $doctores[$rowmed['meddoc']]) ) ? $doctores[$rowmed['meddoc']] = 1 : $doctores[$rowmed['meddoc']] ++;
	 }
	 $doctores['0'] = 1; //artificio para que funcione normal en uvglobal quienes tienen cargos al documento 0

	

		 $q = " SELECT Venffa,Vennum,Empcod ,".$wbasedato."_000016.Fecha_data,Empnit,Empnom, Clinom ,Venvto, Venviv,Vendes, Vencop , Venest ,Venffa , Vennfa  "
         ."   	  FROM ".$wbasedato."_000016 LEFT JOIN ".$wbasedato."_000041 ON Clidoc = Vennit , ".$wbasedato."_000024" //, ".$wbasedato."_000100 "
         ."  	 WHERE Vennum like '".$wffac."'"
         ."    	   AND Vencod = empcod";
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $num = mysql_num_rows($res);
	     $row = mysql_fetch_array($res);

	     $wfac       = $row['Vennum'];
	     $wfec       = $row['Fecha_data'];
	     $wcem       = $row['Empcod'];
	     $wnit       = $row['Empnit'];
	     $wnem       = $row['Empnom'];
	     $wval       = $row['Venvto'];
	     $wiva       = $row['Venviv'];
	     $wcop       = $row['Vencop'];
	     $wdes       = $row['Vendes'];


		 $query_saldo = "SELECT  Rdevta,Rdevca
						   FROM  ".$wbasedato."_000021
						  WHERE  Rdevta='".$row['Vennum']."' AND Rdeest='on'";

						$res2 = mysql_query($query_saldo,$conex);
						$vrecibido = 0;
						while($row3 = mysql_fetch_array($res2))
						{

							$vrecibido =$vrecibido + $row3['Rdevca'] ;

						}

	     $wsal       = $wval - $vrecibido;
	     $whis       = $row[16];
	     $wing       = $row[17];
	     $wcre       = '1';
	     $wrec       = '100';
	     $west       = $row[23];
	     $wnpa       = $row['Clinom'];
	     $wdev       = $row[25];
	     $wrbo       = $vrecibido;
	     $wobservacion= $row[27];
		 $westadovent = $row['Venest'];
		 
		 
		 $vennc = 0 ;
		 $wvnd  = 0 ;
		 if( $row['Vennfa']!='' )
		 {
			 // se mira si tiene factura y se trae los datos para calcular el saldo , Notas credito, (esta consulta es traida de consultar factura)
			 
			 /*
			 SELECT fenffa, fenfac, fenfec, fencod, fennit, empnom, fenval, fenviv, fencop, fencmo, fendes, fenabo, fenvnd, fenvnc, fensal, fenesf, "
					 ."        fenhis, fening, fencre, fenpde, fenrec, fentop, fenrln, fenest, fennpa, fendev, fenrbo, fenobs "
			 */
			  $qfactura = " SELECT  fenvnc , fenvnd"
						 ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000024 " //, ".$wbasedato."_000100 "
						 ."  WHERE fenffa like '".$row['Venffa']."'"
						 ."    AND fenfac like '".$row['Vennfa']."'"
						 ."    AND fencod = empcod ";
			 $resfac = mysql_query($qfactura,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qfactura." - ".mysql_error());
			 $numfac = mysql_num_rows($resfac);
			 $rowfac = mysql_fetch_array($resfac);
			 
			 
			 if(($numfac*1) > 0)
			 {
				 $vennc = $rowfac['fenvnc'];
				 $wvnd  = $rowfac['fenvnd'];
				 $wsal  = $wsal -  $vennc + $wvnd;
				 
			 }
		 }
		//-------------
				 
	     echo "<br>";
	     echo "<center><table border='0'>";

	     //ACA PINTO EL ENCABEZADO DE LA FACTURA
	     echo "<tr>";
		 // echo "<td align=left class='fila2' colspan=1><font text color=".$wclfg."> Fuente:<br><b>".$wfue."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=2><font text color=".$wclfg."> Venta:<br><b>".$wfac."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=1><font text color=".$wclfg."> Historia:<br><b>".$whis."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=1><font text color=".$wclfg."> Ingreso:<br><b>".$wing."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=2><font text color=".$wclfg."> Responsable:<br><b>".$wcem."-".$wnit."-".$wnem."</b></font></td>";
		 //echo "<td align=left class='fila2' colspan=3><font text color=".$wclfg."> Usuario:<br><b>".$wno1." ".$wno2." ".$wap1." ".$wap2."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=3><font text color=".$wclfg."> Usuario:<br><b>".$wnpa."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Cant.Resp.:<br><b>".$wcre."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=2><font text color=".$wclfg."> % Reconoc.:<br><b>".$wrec."</b></font></td>";
		 echo "</tr>";
		 echo "<tr>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Valor Venta:<br><b>".number_format($wval,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Devolución:<br><b>".number_format($wdev,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> IVA:<br><b>".number_format($wiva,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Descuento:<br><b>".number_format($wdes,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> V/r Abonos:<br><b>".number_format($wabo,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> V/r CMO:<br><b>".number_format($wcmo,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> V/r Copago:<br><b>".number_format($wcop,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=2><font text color=".$wclfg."> V/r Recibos:<br><b>".number_format($wrbo,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Vlr Notas Debito:<br><b>".number_format($wvnd,0,'.',',')."</b></font></td>";
	
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Vlr Notas Credito:<br><b>".number_format($vennc,0,'.',',')."</b></font></td>";
		if( $westadovent == 'off'){
				echo "<td align=center class='fila2' colspan=2><font text color=".$wclfg."> Saldo:<br><b>".number_format(0,0,'.',',')."</b></font></td>";
		}
		else
		{	
			echo "<td align=center class='fila2' colspan=2><font text color=".$wclfg."> Saldo:<br><b>".number_format($wsal,0,'.',',')."</b></font></td>";
		} 
		 echo "</tr>";

		 echo "</table>";





		$q = "SELECT Vencco, Ccodes, Grucod, Grudes, '' , '' ,'', Vdevun * Vdecan , Vdedes , Vdepiv
		         FROM ".$wbasedato."_000017 ,  ".$wbasedato."_000001  , ".$wbasedato."_000004 ,   ".$wbasedato."_000003, ".$wbasedato."_000016
				WHERE Vdenum = '".$wfac."'
				  AND Vdenum = Vennum
				  AND Vdeart = Artcod
			      AND mid(Artgru,1,instr(Artgru,'-')-1) =  Grucod
				  AND Vencco = Ccocod ";

		 //echo $q;
	     $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $num1 = mysql_num_rows($res1);

	     if ($num1 > 0)
	        {
	         echo "<br>";
	         echo "<center><table border='0'>";
	         echo "<tr class='encabezadoTabla'>";
	         echo "<th colspan=2>CONCEPTO</th>";
	         echo "<th colspan=2>CENTRO DE COSTO</th>";
	         echo "<th colspan=3>TERCERO</th>";
	         echo "<th colspan=1>VALOR BRUTO</th>";
	         echo "<th colspan=1>DESCUENTO</th>";
	         echo "<th colspan=1>VALOR NETO</th>";
	         echo "</tr>";

	         $wtotcon=0;
	         $wtotdes=0;
	         for ($j=1;$j<=$num1;$j++)
	            {
					 $row1 = mysql_fetch_array($res1);
					 ( $row1[4]=="" or ( $doctores[$row1[4]]*1 == 1 ) ) ? $mostrarDirecto = true : $mostrarDirecto = false;
					 ( $doctores[$row1[4]]*1 > 1 ) ? $filtroMedico = true : $filtroMedico = false;
					 if( ( $mostrarDirecto ) or ( $filtroMedico and ($row1['estado'] == 'on') ) )
					 {
						 if ($j%2==0)
						   $wclass="fila1";
						 else
						   $wclass="fila2";
						 echo "<tr>";
						 echo "<td class='".$wclass."'>".$row1[2]."</td>";	 //CONCEPTO
						 echo "<td class='".$wclass."'>".$row1[3]."</td>";     //CONCEPTO
						 if ($row1[0] == "") 									 //PROCEDIMIENTO
							{
							 echo "<td class='".$wclass."'>&nbsp</td>";
							 echo "<td class='".$wclass."'>&nbsp</td>";
							}
						   else
							  {
							   echo "<td class='".$wclass."'>".$row1[0]."</td>";
							   echo "<td class='".$wclass."'>".$row1[1]."</td>";
							  }
						 if ($row1[4] == "")  									 //TERCERO
							{
							 echo "<td class='".$wclass."'>&nbsp</td>";
							 echo "<td class='".$wclass."'>&nbsp</td>";
							 echo "<td class='".$wclass."'>&nbsp</td>";
							}
						   else
							  {
							   echo "<td class='".$wclass."'>".$row1[4]."</td>";
							   echo "<td class='".$wclass."'>".$row1[5]."</td>";
							   echo "<td class='".$wclass."'>".$row1[6]."%</td>";
							  }
						 echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[7],0,'.',',')."</td>";
						 echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[8],0,'.',',')."</td>";
						 if($row1[9] * 1 != 0)
						 {
						
							$descuentoventa_aux = $row1[8];
							$valorventa_aux = $row1[7];
							$porcentageiva_aux = $row1[9];
							
							//$valorsiniva_aux = $valorventa_aux - ($valorventa_aux * ($porcentageiva_aux /100)) ;
							$valorsiniva_aux = ($valorventa_aux) / (1 + ($porcentageiva_aux /100)) ;
							$valorsinivaCondes_aux = $valorsiniva_aux - $descuentoventa_aux ;
							
							$newvalorsiniva_aux = $valorsinivaCondes_aux +  $valorsinivaCondes_aux * ($porcentageiva_aux /100) ;  
							//$row1[7] = $newvalorsiniva_aux;
							echo "<td align=RIGHT class='".$wclass."'>".number_format($newvalorsiniva_aux,0,'.',',')."</td>";
							
						 }
						 else
						 {
							echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[7]-$row1[8],0,'.',',')."</td>";
							$newvalorsiniva_aux  = ($row1[7] - $row1[8]);
						 }
						 $wtotcon=$wtotcon+$row1[7];
						 $wtotdes=$wtotdes+$row1[8];
						 $wtotneto = $wtotneto + $newvalorsiniva_aux;
					 }
		        }
		     echo "<tr class='encabezadoTabla'>";
		     echo "<td align=RIGHT colspan=7><b>Totales</b></td>";
	         echo "<td align=RIGHT colspan=1><b>".number_format($wtotcon,0,'.',',')."</b></td>";
	         echo "<td align=RIGHT colspan=1><b>".number_format($wtotdes,0,'.',',')."</b></td>";
	         echo "<td align=RIGHT colspan=1><b>".number_format($wtotneto,0,'.',',')."</b></td>";
	         echo "</tr>";

	         if (isset($whosp) and strtoupper($whosp)=="ON")
	            {
	             echo "<input type='hidden' id='fuenteFactura' value='".$wfue."'>";
          		 echo "<input type='hidden' id='numeroFactura' value='".$wfac."'>";

				 $puedeAnular = "off";
				 $queryAnular = " SELECT Peranu
								    FROM {$wbasedato}_000081
								   WHERE Perusu = '{$wuser}'
								     AND Perfue = '{$wfue}'";
				 $rsAnular   = mysql_query( $queryAnular, $conex );
				 while( $rowAnular = mysql_fetch_array( $rsAnular ) ){
					$puedeAnular = $rowAnular['Peranu'];
				 }
	             $causas=consultarCausas( $west, $wfue, $wfac );
				  if( $puedeAnular == "on" ){
					 echo "<tr>";
					 ( $west == "on" ) ? $anularFactura = "<font style='cursor:pointer;' color='blue' size='2' onclick='anular()'> Anular Factura </font>" : $anularFactura = "";
					 echo "<td class='fila1' colspan='10' align=center><b>{$anularFactura}</b></td>";
					 echo "</tr>";
				  }

				 // FILAS PARA LA ANULACIÓN DE FACTURAS.
				 if($west == "on" and $puedeAnular == "on" ){
				 	$visibilidad = "display:none;";
				 	$textHabilitado = "";
				 }else{
				 	$visibilidad = "";
				 	$textHabilitado = "disabled";
				 }
				 if( $puedeAnular == "on" ){
					 echo "<tr id='tr_causas' style='{$visibilidad}'>";
						echo "<td colspan='10'></br><div align='center'>";
							echo pintarCausas( $causas, $west  );
						echo "</div>";
						echo "</br>";
						echo "<div align='center'><table border=0 width='85%'><tr><td class=encabezadoTabla align=center><b>OBSERVACION:</b> </td></tr><tr><td class='fila2' align=center><b><textarea {$textHabilitado} name='wobs' id='wobs' cols='80' rows='3'>{$wobservacion}</textarea></td></tr></table>";
						($west !=  "on") ? $botonVisible = "style='display:none'" : $botonVisible = "";
						echo "<br><input type='button' value='Anular' {$botonVisible} onclick='anularFactura( \"/matrix/ips/procesos/Consultar_venta.php?wfactura=".$wfue."-".$wfac."&amp;wemp_pmla=".$wemp_pmla."&amp;wbasedato=".$wbasedato."&amp;wffue=".$wfue."&amp;tablaConceptos=".$tablaConceptos."&amp;wffac=".$wfac."\");'>";
						echo "</div></br>";
						echo "</td>";
					 echo "</tr>";
				 }
			    }


			 echo "<tr>";
	         $wimpfac="<A href='/matrix/ips/reportes/r003-imp_factura.php?wfactura=".$wfue."-".$wfac."&amp;wemp_pmla=".$wemp_pmla."&amp;wbasedato=".$wbasedato."' TARGET='_blank'> ";
			// echo "<td class='fila1' colspan=5 align=center><b>".$wimpfac." Imprimir Factura</b></td>";
			 $wdetfac="<A href='/matrix/ips/reportes/imp_det_venta.php?wfue=".$wfue."&wfac=".$wfac."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'> ";
			 echo "<td class='fila1' colspan=10 align=center><b>".$wdetfac." Detalle de Cargos</b></td>";
			 echo "</tr>";
	        }
	  echo "</table>";
    }

  //===========================================================================================================================================
  function mov_factura($wffue, $wffac)
    {

	 global $wbasedato;
	 global $wemp_pmla;
	 global $conex;
	 global $wcf;
	 global $wcf2;
	 global $wclfa;
	 global $wclfg;


	 $q = " SELECT renfue, rennum, renfec, rencco, renvca, renobs "
         ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000021 "
         ."  WHERE Rdevta like '".$wffac."'"
         ."    AND rdefue = renfue "
         ."    AND rdenum = rennum "
         ."    AND rdecco = rencco "
         ."    AND rdeest = 'on' "
         ."    AND rdeest = renest "
         ."  ORDER BY 3 desc,1,2 ";
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $num = mysql_num_rows($res);
		// echo $q;
	     if ($num > 0)
	        {
		     echo "<br>";
	         echo "<center><table border='0'>";
	         echo "<tr><td align=center colspan=13 class='titulo'><b>M O V I M I E N T O S &nbsp; A N E X O S &nbsp; A &nbsp; L A &nbsp; V E N T A</b></td></tr>";
	         echo "<tr class='encabezadoTabla'>";
	         echo "<th colspan=2>FUENTE</th>";
	         echo "<th colspan=1>NRO DTO</th>";
	         echo "<th colspan=1>FECHA</th>";
	         echo "<th colspan=1>VALOR</th>";
	         echo "<th colspan=1>CAUSA / OBSERVACION</th>";
	         echo "<th colspan=1>&nbsp</th>";
	         echo "</tr>";

		     for ($i=1;$i<=$num;$i++)
		         {
		         if ($i%2==0)
			       $wclass="fila1";
			     else
			       $wclass="fila2";

			      $row = mysql_fetch_array($res);

				  $wdocfue       = $row[0];
			      $wdocnum       = $row[1];
			      $wdocfec       = $row[2];
			      $wdoccco       = $row[3];
			      $wdocval       = $row[4];
			      $wdocobs       = $row[5];

			      echo "<tr>";
				  echo "<td align=center class='".$wclass."' colspan=1>".$wdocfue."</font></td>";
				  $q = " SELECT cardes "
				      ."   FROM ".$wbasedato."_000040 "
				      ."  WHERE carfue = '".$wdocfue."'";
				  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row1 = mysql_fetch_array($res1);

				  echo "<td align=left class='".$wclass."' colspan=1>".$row1[0]."</td>";
				  echo "<td align=center class='".$wclass."' colspan=1>".$wdocnum."</td>";
				  echo "<td align=center class='".$wclass."' colspan=1>".$wdocfec."</td>";
				  echo "<td align=right class='".$wclass."' colspan=1>".number_format($wdocval,0,'.',',')."</td>";
				  echo "<td align=left class='".$wclass."' colspan=1>";

				  $q=" 	SELECT caucod, caunom
						  FROM ".$wbasedato."_000072, ".$wbasedato."_000071
						 WHERE cauest='on'
						   AND Caudev='on'
						   AND Caucod=Doccau
						   AND Docfue='".$wdocfue."'
						   AND Docnum='".$wdocnum."' ";
				  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $num1 = mysql_num_rows($res1);
				  if($num1>0)
				  {
					echo "Causa:";
					for ($j=1;$j<=$num1;$j++)
					{
						$row1 = mysql_fetch_array($res1);
						echo " - ".strtolower($row1['caunom']);
					}
				  }
				  if(isset($wdocobs) && $wdocobs!="")
					echo "<br>Observaci&oacute;n: ".$wdocobs;
				  echo "</td>";
				  echo "<td class='".$wclass."' align=center><A href='Imp_documento.php?wfuedoc=".$wdocfue."&wnrodoc=".$wdocnum."&wcco=".$wdoccco."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'>Ver</A></td>";
				  //echo "<td bgcolor='".$wclass."' align=center><font size=3><A href='Consultar_Factura.php?wffue  =".$row[0]."&amp;wffac=".$row[1]."&amp;wbasedato=".$wbasedato."&amp;$wver=on'> Ver</A></font></td>";
		          echo "</tr>";
			     }
		     echo "</table>";
	        }

	 }

 function pintarCausas($causas, $estadoFactura )
	{
		$selectCausas = "";
		$numCausas = count( $causas );
		if( $estadoFactura == "on" ){
			$selectCausas = "<table align='center' width='85%' border=1>";
			$selectCausas .= "<tr>";
			$selectCausas .= "<td class='encabezadoTabla' colspan=2 align='center'><b>CAUSAS  DE ANULACI&Oacute;N</b></td>";
			$selectCausas .= "</tr>";
			$selectCausas .= "<tr id='tr_slc'>";
			$selectCausas .= "<td class='botona' colspan='2' align='center'><select name='cauSel' id='cauSel' onchange='agregarCausa( this )'>";
			$selectCausas .= "<option value='--' selected>{$causas['--']}</option>";

			foreach ($causas as $key=>$dato)
			{
				if ($key !="--")
				{
					$selectCausas .= "<option value='$key'>".$causas[$key]."</option>";
				}
			}
				$selectCausas .= "</select></td></tr></table>";
		}else{ // si ya está anulada se va a construir las filas para mostrar las causas

				$selectCausas = "<table align='center' width='85%' border=1>";
				$selectCausas .= "<tr>";
				$selectCausas .= "<td class='encabezadoTabla' colspan=2 align='center'><b>CAUSAS  DE ANULACI&Oacute;N</b></td>";
				$selectCausas .= "</tr>";
				$i = 0;
				foreach ($causas as $key=>$dato)
				{
					( is_int($i/2) ) ? $class= "fila1" : $class = "fila2";
					$i++;
					$selectCausas .= "<tr class='{$class}'><td>";
					if ($key !="--")
					{
						$selectCausas .= $key."&nbsp;&nbsp;".$causas[$key];
					}else{
						if( $numCausas==1 )
							$selectCausas .= $causas[$key];
					}
					$selectCausas .= "</td></tr>";
				}
					$selectCausas .= "</table>";
		}
		return( $selectCausas);
	}
 function consultarCausas( $west, $wfuente="", $wnumDoc="" )
	{
	 	global $conex;
	 	global $wbasedato;
	 	global $wemp_pmla;

	 	if( $west == "on" ){
		 	$q="select caucod, caunom from ".$wbasedato."_000072 where cauest='on' and cauafa='on' order by caunom ";
		 	$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 	$num1 = mysql_num_rows($res1);

		 	while( $row = mysql_fetch_array( $res1 ) ){
		 		$causas[$row['caucod']] = $row['caunom'];
		 	}

		 	$causas['--'] = " Seleccione ";
	    }else{

	    	$q = " SELECT Doccau
	    			 FROM ".$wbasedato."_000071
	    		    WHERE Docfue = '{$wfuente}'
	    		      AND Docnum = '{$wnumDoc}'
	    		      AND Docest = 'on'";
		 	$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 	$num1 = mysql_num_rows($res1);

		 	$aux = array();

		 	while( $row = mysql_fetch_array( $res1 ) ){
		 		array_push($aux, "'".$row[0]."'" );
		 	}
		 	if( count($aux) >0 ){
			 	$aux = implode( ",", $aux );

			 	$q="select caucod, caunom from ".$wbasedato."_000072 where caucod IN (".$aux.") order by caunom ";
			 	$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 	$num1 = mysql_num_rows($res1);
			 	while( $row = mysql_fetch_array( $res1 ) ){
			 		$causas[$row['caucod']] = $row['caunom'];
			 	}
			 }
		 	$causas['--'] = " Sin Especificar ";
	    }
		return( $causas );
	}
  //===========================================================================================================================================
  //INICIO DEL PROGRAMA
  //===========================================================================================================================================

  global $wconfirma_anular;

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' id='wbasedato' value='".$wbasedato."'>";

 /* if (isset($wanular) and $wanular=="on")
     {
     	anular_factura($wffue,$wffac);
     }*/

  if (isset($wffue) and $wffue != "" and isset($wffac) and $wffac !="")
     {
      ver_factura($wffue,$wffac);


      mov_factura($wffue,$wffac);
     }
    else
	  //if (((!isset($wfue) and !isset($wfac)) or (!isset($whis)) or (!isset($wing) and !isset($whis))) and !isset($wval))
	  if ((!isset($wfue) and !isset($wfac)) or ((!isset($whis) or !isset($wing) and !isset($whis) and !isset($wval)) or (!isset($wdoc) or !isset($wnom) and !isset($wdoc) and !isset($wnom))))
	     {
	      echo "<br>";
	      echo "<div align='center'><fieldset id='' style='padding:15px;width:800px'>";
	      echo "<legend class='fieldset'>Par&aacute;metros de busqueda</legend>";
	      echo "<div>";
		  echo "<table border='0' style='width:60%;'>";
		  echo "<tr>";
			 // echo "<td align=left width='10%' class='fila1' colspan=1><b> Fuente:</b></td><td width='40%' class='fila2'><INPUT TYPE='text' NAME='wfue'></td>";
			  echo "<td align=left width='10%' class='fila1' colspan=1><b> Venta:</b></td><td width='40%' class='fila2'><INPUT TYPE='text' NAME='wfac'></td>";
			  echo "<td align=left class='fila1' colspan='1'><b>Identificación: </b></td><td width='40%' class='fila2'> <INPUT TYPE='text' NAME='wdoc'  ></td>";

		  echo "</tr>";
		  echo "<tr>";
			  echo "<td align=left width='10%' class='fila1' colspan=1><b> Historia:</b></td><td width='40%' class='fila2'><INPUT TYPE='text' NAME='whis'></td>";
			  echo "<td align=left width='10%' class='fila1' colspan=1><b> Ingreso:</b></td><td width='40%' class='fila2'><INPUT TYPE='text' NAME='wing'></td>";
		  echo "</tr>";
		  echo "<tr>";
		  	echo "<td align=left class='fila1' colspan='4'><b> Nombre del paciente o persona responsable (comodin=%): &nbsp; </b><INPUT TYPE='text' NAME='wnom' SIZE=71></td>";
		  echo "</tr>";
		  echo "</table></br>";


		  echo "</div>";
		  echo "</fieldset></div>";
		 }
	    else
	       {
		    if ($wfue!="" or $wfac!="" or $whis!="" or $wnom!="" or $wdoc!="")
		       {

			     if (!isset($wfue) or $wfue == "")
			        $wfue="%";
			     if (!isset($wfac) or $wfac == "")
			        $wfac="%";
			     if (!isset($whis) or $whis == "")
			        {
			         $whis_i=0;
			         $whis_f=9999999999;
		            }
		           else
		              {
			           $whis_i=$whis;
			           $whis_f=$whis;
		              }
			     if (!isset($wing) or $wing == "")
			        {
			         $wing_i=0;
			         $wing_f=999;
		            }
			       else
		              {
				       $wing_i=$wing;
				       $wing_f=$wing;
			          }

			     if (!isset($wnom) or $wnom == "")
			        $wnom="%";
			     if (!isset($wdoc) or $wdoc == "")
			        $wdoc="%";

				// Si al pasar las variables por URL tomaron otro valor aca les asigno el valor original
			    if(isset($wfac))
					$wfac = desconvertir_url($wfac);
				if(isset($wnom))
					$wnom = desconvertir_url($wnom);
				if(isset($wfue))
					$wfue = desconvertir_url($wfue);
				if(isset($wdoc))
					$wdoc = desconvertir_url($wdoc);
				if(isset($whis))
					$whis = desconvertir_url($whis);
				if(isset($wing))
					$wing = desconvertir_url($wing);


				 $q=" SELECT Venffa,Vennum,Venfec, Venvto, Vennit , Clinom , Empnit , Empnom , Venest, Venffa, Vennfa
							FROM ".$wbasedato."_000016 LEFT JOIN ".$wbasedato."_000041 ON Clidoc = Vennit , ".$wbasedato."_000024
						   WHERE Vennum like '".$wfac."'
							 AND Vencod = empcod
							 AND Vennit like '".$wdoc."'
							 AND Clinom like '".$wnom."'
							 ORDER BY Venfec DESC";
							 
				




				 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			     $num = mysql_num_rows($res);

			     if ($num > 0)
			        {
					
					
				     echo "<br>";
			         echo "<center><table border='0'>";

			         echo "<tr><td colspan=13 class='fila2'><b>Cantidad de Facturas encontradas: ".$num."</b></td></tr>";

				     echo "<tr class='encabezadoTabla'>";
			         echo "<th>&nbsp</th>";
				 
				     echo "<th>VENTA</th>";
				     echo "<th>FECHA</th>";
				     echo "<th>HISTORIA</th>";
				     echo "<th>DOCUMENTO</th>";
				     echo "<th>PACIENTE</th>";
				     echo "<th>RESPONSABLE</th>";
				     echo "<th>VALOR</th>";
				     echo "<th>SALDO</th>";
				  
				     echo "<th>&nbsp</th>";
				     echo "</tr>";

				     $wver="";

				     for ($i=1;$i<=$num;$i++)
				        {
					     $row = mysql_fetch_array($res);
						 
						 // se mira si tiene factura y se trae los datos para calcular el saldo , Notas credito, (esta consulta es traida de consultar factura)
							$vennc = 0;
							$vennd = 0;
							if( $row['Vennfa']!='' )
							{
							 $qfactura = " SELECT  fenvnc , fenvnd "
								 ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000024 " //, ".$wbasedato."_000100 "
								 ."  WHERE fenffa like '".$row['Venffa']."'"
								 ."    AND fenfac like '".$row['Vennfa']."'"
								 ."    AND fencod = empcod ";
							 $resfac = mysql_query($qfactura,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qfactura." - ".mysql_error());
							 $numfac = mysql_num_rows($resfac);
							 $rowfac = mysql_fetch_array($resfac);
							 
							 
							 if(($numfac*1) > 0)
							 {
								 $vennc = $rowfac['fenvnc'];
								 $vennd = $rowfac['fenvnd'];
								 
							 }
							}
						 
						 
					     if ($i%2==0)
			                $wclass="fila1";
			               else
			                  $wclass="fila2";

						 $westcar = '';
			         
						$query_saldo = "SELECT  Rdevta,Rdevca
										  FROM  ".$wbasedato."_000021
										 WHERE  Rdevta='".$row['Vennum']."' AND Rdeest='on'";

						$res2 = mysql_query($query_saldo,$conex);
						$vrecibido = 0;
						while($row3 = mysql_fetch_array($res2))
						{

							$vrecibido =$vrecibido + $row3['Rdevca'] ;

						}


					     echo "<tr>";
					     echo "<td class='".$wclass."' align=center><A href='Consultar_venta.php?wffue=".$row[0]."&wffac=".$row[1]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&$wver=on&wfue=".convertir_url($wfue)."&wfac=".convertir_url($wfac)."&whis=".convertir_url($whis)."&wnom=".convertir_url($wnom)."&wdoc=".convertir_url($wdoc)."'> Ver</A></td>";
					     // echo "<td class='".$wclass."'>".$row['Venffa']."</td>";                 						//Fuente Factura
					     echo "<td class='".$wclass."'>".$row['Vennum']."</td>";                 						//Numero Factura
					     echo "<td class='".$wclass."'>".$row['Venfec']."</td>";                 						//Fecha
					     echo "<td class='".$wclass."'>".$row['']."/".$row['']."</td>";    						//Historia
					     //echo "<td class='".$wclass."'>".$row[17]."</td>";                						//Documento Responsable Factura
					     //echo "<td class='".$wclass."'>".$row[14]." ".$row[15]." ".$row[12]." ".$row[13]."</td>"; //Nombre Paciente
					     echo "<td class='".$wclass."'>".$row['Vennit']."</td>";                						//Documento Responsable Factura
					     echo "<td class='".$wclass."'>".$row['Clinom']."</td>";                                        //Nombre Paciente
					     echo "<td class='".$wclass."'>".$row['Empnit']." ".$row['Empnom']."</td>";								//Empresa Responsable
					     echo "<td class='".$wclass."' align=right>".number_format($row['Venvto'],0,'.',',')."</td>";	//Valor Neto Factura
					     // echo "<td class='".$wclass."' align=right>".number_format($row[''],0,'.',',')."</td>"; 	//Saldo Factura
					     //---------SALDO
						 if($row['Venest'] == 'off')
						 {
							 echo "<td class='".$wclass."' align=right>".number_format(0,0,'.',',')."(venta anulada)</td>"; 	//Saldo Factura
					    
						 }
						 else
						 {
							 echo "<td class='".$wclass."' align=right>".number_format(($row['Venvto'] - $vrecibido - $vennc + $vennd),0,'.',',')."</td>"; 	//Saldo Factura
					   
						 }
						  // echo "<td class='".$wclass."' align=center><b>".$westcar."</b></td>";											//Estado Factura en Cartera
					     // echo "<td class='".$wclass."' align=center><b>".$westreg."</b></td>";										//Estado Factura
					     echo "<td class='".$wclass."' align=center><A href='Consultar_venta.php?wffue=".$row[0]."&wffac=".$row[1]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&$wver=on&wfue=".convertir_url($wfue)."&wfac=".convertir_url($wfac)."&whis=".convertir_url($whis)."&wnom=".convertir_url($wnom)."&wdoc=".convertir_url($wdoc)."'> Ver</A></td>";
					     echo "</tr>";
				        }
					}
			   }
		   }


  echo "<div align='center'>";
  if(isset($wffue) and isset($wffac))
  	echo "<input type='button' value='Retornar' onclick='retornar(\"Consultar_venta.php?wemp_pmla=".$wemp_pmla."&wfue=".$wfue."&wfac=".$wfac."&whis=".$whis."&wnom=".$wnom."&wdoc=".$wdoc."\")'>";
  elseif(isset($wfue) or isset($wfac) or isset($whis) or isset($wnom) or isset($wdoc))
  	echo "<input type='button' value='Retornar' onclick='retornar(\"Consultar_venta.php?wemp_pmla=".$wemp_pmla."\" )'>";
  else
    echo "<input type='button' value='Buscar' onclick='enter();'></div>";
//Mensaje de espera para generacion
echo "<div id='msjEspere' style='display:none;'>";
echo "<br /><br />
	<img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Anulando</font>
	<br /><br /><br />";
echo "</div>";

//Mensaje de alertas
	echo "<div id='msjAlerta' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/root/Advertencia.png'/>";
	echo "<br><br><div id='textoAlerta'></div><br><br>";
	echo '</div>';

  //echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
}
?>
</body>
</html>
