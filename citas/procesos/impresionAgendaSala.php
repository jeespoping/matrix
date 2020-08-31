<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>


<html>
<head>
<title>Impresion Agenda</title>

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>        <!-- Acordeon -->
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>



<script>
  
	idRadio = '';
	accion = '';
	est = '';
	func = '';
	campoCancela = '';

	function abrirVentana( adicion, citas, solucion, wdoc ){
		
		//alert(wdoc);
		var ancho=screen.width;
		var alto=screen.availHeight;
		// var v = window.open( 'admision.php?ok=9&empresa='+solucion+'&idCita='+adicion+'&wemp2='+citas,'','scrollbars=1, width='+ancho+', height='+alto );  //original
		var v = window.open( '../../IPS/Procesos/admision.php?wdoc='+wdoc+'&wtdo=CC-CEDULA DE CIUDADANIA&ok=1&empresa='+solucion+'&idCita='+adicion+'&wemp2='+citas,'','scrollbars=1, width='+ancho+', height='+alto ); //con cambios
		v.moveTo(0,0);
	}


	function imprimir()
	{
		//alert("entro");
		//var v =window.open('impresionAgenda.php', 'noimporta', 'width=300, height=300, scrollbars=NO')"
		// document.all.item("no_imprimir").style.display='none'
		// window.print()
		// document.all.item("no_imprimir").style.display=''
	}



function paginar( campo, principal ){

 	 	if( campo ){
 	 	 	if( campo.tagName ){

 	 	 		var cabecera = document.getElementById('hiPaciente').value;

 	 	 	 	switch( campo.tagName ){
 	 	 	 	
 	 	 	 		case 'TABLE':
 	 	 	 			var aux = document.createElement( "div" );
 	 	 	 			aux.innerHTML = "<table border= '1px' style='border: 1px solid black; border-collapse: collapse; font-size: 8pt; width: 19cm;'></table>";

						tableAux = aux.firstChild;
						principal.appendChild( aux.firstChild );
						
 	 	 	 			tabla = campo.cloneNode(true);

 	 	 	 			var sumaAltura = 51;

 	 	 	 			for( var i = 0; i < campo.rows.length; i++ ){

							var fila = tableAux.insertRow( tableAux.rows.length );
		 	 	 	 		
	 	 	 				while( tabla.rows[i].cells.length > 0 ){
	 	 	 					fila.appendChild( tabla.rows[ i ].cells[0] );
	 	 	 				}

							sumaAltura = sumaAltura + fila.clientHeight;
							
							if( sumaAltura > alturaPagina ){	//Crea una nueva pagina

								paginas++;

								sumaAltura = fila.clientHeight;

								//ESto crea el encabezado de cada página
								var aux2 = document.createElement( "div" );
								aux2.innerHTML = "<a>Página: "+paginas+"</a>";
								aux2.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td width='100%'>Página: "+parseInt( paginas )+"</td></tr></table><br></a>"
								
								//Sumo la altura del encabezado de las paginas								
								var aux3 = aux2.firstChild
								principal.appendChild( aux2.firstChild );
								
								sumaAltura = sumaAltura + aux3.clientHeight;
								
								principal.appendChild( tableAux );

								aux.innerHTML = "<div class='saltopagina'></div>";
								principal.appendChild( aux.firstChild );

								aux.innerHTML = "<table style='border: 1px solid black; border-collapse: collapse; font-size: 8pt; width: 19cm;' border= '1px'></table>";
								
								//Si hay salto de pagina la ultima fila agragada esta por fuera de la pagina
								//por tanto agrego la ultima fila a la nueva tabla de la pagina nueva
								fila.parentNode.parentNode.tBodies[0].appendChild( fila );
								
								tableAux = aux.firstChild;
								principal.appendChild( aux.firstChild );
								
								//Esto agrega el encabezado de la tabla nueva
								if( i > 0 ){
									var cloneFila = campo.rows[0].cloneNode( true );
									try{
										tableAux.tBodies[0].appendChild( cloneFila );
										
										sumaAltura = sumaAltura + cloneFila.clientHeight;
									}
									catch(e){}
								}
							}
							
 	 	 	 			}

 	 	 	 			paginas++;
	 	 	 	 		var aux2 = document.createElement( "div" );
						aux2.innerHTML = "<a>Página: "+paginas+"</a>";
						aux2.innerHTML = "<a><table width='100%' style='font-size:8pt;'><tr><td width='100%'>Página: "+parseInt( paginas )+"</td></tr></table><br></a>"
						principal.appendChild( aux2.firstChild );

 	 	 	 			campo.style.display = 'none';
 	 	 	 			principal.appendChild( tableAux );
 	 	 	 			
 	 	 	 	 	break;

 	 	 	 	 	case 'DIV':{
 	 	 	 	 		inicial = findPosY( campo );

 	 	 	 	 		var aux = document.createElement( "div" );	
	 	 	 			aux.innerHTML = "<a>Página: "+parseInt( paginas+1 )+"</a>";
	 	 	 			aux.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( paginas+1 )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>"

	 	 	 			campo.insertBefore( aux.firstChild, campo.childNodes[0] );
						
 	 	 	 	 		if( campo.scrollHeight > alturaPagina ){

 	 	 	 	 			var Pagina2 = 0;
	 	 	 	 	 		sumaAltura = 0;

	 	 	 	 	 		var totalPaginaCampo = parseInt( campo.clientHeight/alturaPagina );
	 	 	 	 	 		var totalPaginaCampo = parseInt( campo.scrollHeight/alturaPagina );

	 	 	 	 	 		Pagina2 = parseInt( paginas+totalPaginaCampo );

	 	 	 	 	 		paginas = paginas + parseInt( totalPaginaCampo )+1;

	 	 	 	 	 		for( var j = 0; j < totalPaginaCampo; j++ ){

		 	 	 	 	 	 	for( var i = campo.childNodes.length-1; i >= 0; i-- ){
		
		 	 	 	 	 	 		posFila = findPosY( campo.childNodes[i] );
									
		 	 	 	 	 	 		if( posFila-inicial < alturaPagina+alturaPagina*(totalPaginaCampo-j-1) && posFila > 0 ){

			 	 	 	 	 	 		var aux = document.createElement( "div" );
			 	 	 	 	 	 		aux.innerHTML = "<div class='saltopagina'></div>";
	
			 	 	 	 	 	 		campoReferencia = campo.childNodes[i];

			 	 	 	 	 			if( campoReferencia ){
			 	 	 	 	 	 			campo.insertBefore( aux.firstChild, campoReferencia );
			 	 	 	 	 			}
			 	 	 	 	 			else{
				 	 	 	 	 			campo.appendChild( aux.firstChild );
			 	 	 	 	 			}

			 	 	 	 	 	 		var aux = document.createElement( "div" );	
		 	 	 	 	 	 			aux.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( Pagina2+1 )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>";

		 	 	 	 	 	 			if( campoReferencia ){
		 	 	 	 	 	 				campo.insertBefore( aux.firstChild, campoReferencia );
		 	 	 	 	 	 			}
		 	 	 	 	 	 			else{
		 	 	 	 	 	 				campo.appendChild( aux.firstChild );
		 	 	 	 	 	 			}

		 	 	 	 	 	 			Pagina2--;


	
		 	 	 	 	 	 			break;
		 	 	 	 	 	 		}
		 	 	 	 	 	 	}
	 	 	 	 	 		}
 	 	 	 	 		}
 	 	 	 	 		else{
 	 	 	 	 	 		paginas++;
 	 	 	 	 		}
 	 	 	 	 	 	
 	 	 	 	 	} break;
 	 	 	 	}
 	 	 	}
 	 	}
 	}

	//0.026458333 valor siempre fijo y el numerador son los cm 
	alturaPagina = 24/0.026458333;
	paginas = 0;
	restoPaginas = 0;
 	 
 	function findPosY(obj)
 	{
 		var curtop = 0;
 		if(obj.offsetParent)
 	    	while(1)
 	        {
 	          curtop += obj.offsetTop;
 	          if(!obj.offsetParent)
 	            break;
 	          obj = obj.offsetParent;
 	        }
 	    else if(obj.y)
 	        curtop += obj.y;
 	    return curtop;
 	 }
 	 
 	function agregarFila( tabla ){
		
		try{
	
			var fila = tabla.insertRow( tabla.rows.length );
	
			for( var  i = 0; i < tabla.rows[0].cells.length ; i++){
				fila.appendChild( fila.insertCell(i) );
			}
			
			return fila;
		
		}
		catch(e){
			alert( "Error en agregar Fila: "+e );
		}

	}

	window.onload = function()
	{
		paginar( document.getElementById( "pacientes" ), document.getElementById( "imprimir" ) );
	
		window.print();
	}
	
</script>

<style>
td{
	font-size: 8pt;
}

.saltopagina{
       page-break-after: always
}

</style>

</head>

<?php
/**
 * Programa:	impresionAgendaSala.php
 * Fecha:		2012-12-07
 * Descripcion:	Este programa imprime las citas que hay en la agenda ya sean todas las del dia o si se filtraron por medico o equipo.
 */

/**
 * Variables del sistema
 * 
 * $slDoctor		Filtro por Doctor. Contiene el nombre del doctor por el que esta filtrado la lista
 * $idCita			Identificador unico de la cita que se le hace la admision
 * $filtro			Codigo del doctor por el que es filtrado el paciente
 */
 
 /*
 Modificaciones:
 2013-01-18: Se reestructura la funcion de paginacion Viviana Rodas
 2013-01-18: Se agrega la entidad responsable a a tabla Viviana Rodas
 2013-01-02: Se agrega funcion para paginacion Viviana Rodas
 2012-12-19: Se agrega el usuario y el tipo de atencion a la tabla de impresion
 2012-12-17: Se le pone borde sencillo a la tabla, de impresion, tambien se quitan los codigos al medico y al examen. Viviana Rodas
 */



/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/
echo "<body>";

echo "<INPUT type='hidden' id='hiPaciente'>";

include_once("root/comun.php");


if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}



echo "<input type='HIDDEN' name= 'caso' id= 'caso' value='".$caso."'>";
echo "<input type='HIDDEN' name= 'wsw' id= 'wsw' value='".@$wsw."'>";
echo "<input type='HIDDEN' name= 'solucionCitas' id= 'solucionCitas' value='".$solucionCitas."'>";
echo "<input type='HIDDEN' name= 'valCitas' id= 'valCitas' value='".@$valCitas."'>";

//El usuario se encuentra registrado


session_start();
if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
}
else
{
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower( $institucion->baseDeDatos );
$wentidad = $institucion->nombre;
 echo "<div id='no_imprimir' style='Display:none'>";
 
	
	if (!isset($wfec))
	{
		$wfec = date("Y-m-d");
	}
	
	if (!isset($valCitas))
	{
		$valCitas = "off";
	}
	
	if ($caso ==1 and $solucionCitas=='citasca')
	{
		$tipoAtencion='on';
	}
	else
	{
		$tipoAtencion='off';
	}
	
	if( isset($asistida) ){
		marcarAsistida( $asistida );
	}
	
	//Buscando el doctor por el que fue filtrado
	if( !isset( $slDoctor ) ){
		$nmFiltro = "% - Todos";
		$filtro = '%';
		$slDoctor = "% - Todos";
	}
	else{
		$nmFiltro = $slDoctor;
		$exp = explode( " - ", $slDoctor);
		$filtro = $exp[0];
	}
	
	echo "<form name='pantalla' method=post width='750'>";
	
	echo "<br><br>";
	
	
	if ($caso == 2 and $valCitas=="on")
	{
		$sql = "SELECT
			Mednom, Medcod
		FROM
			{$wbasedato}_000051
		WHERE
			Medcid != ''
			AND Medest = 'on'
		ORDER BY Mednom";
	}
	else if ($caso == 3 or $caso == 1)
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000003 where activo='A' ";
	}
	
	else if ($caso == 2 and $valCitas!="on")
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000010 where activo='A' group by descripcion order by descripcion";
	}
	
				
	$res1 = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	
	//Filtro por doctor o equipo
	echo "<table align=center>";
	echo "	<tr>";
	if ($caso == 2)
	{		
		echo "	<td class='encabezadotabla' align=center>Filtro por Profesional</td>";
	}
	else
	{
		echo "	<td class='encabezadotabla' align=center>Filtro por Sala</td>";
	}
	echo "	</tr>";
	echo "	<tr>";
	echo "	<td class='fila1'><select name='slDoctor' onchange='javascript: document.forms[0].submit();'>";
	echo "	<option>% - Todos</option>";
	
	for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ ){
	
		if ($caso == 2 and $valCitas=="on")
		{
			//if( $slDoctor != "{$rows['Medcod']} - {$rows['Mednom']}" )
			$rows['Medcod'] = trim( $rows['Medcod'] );
			$rows['Mednom'] = trim( $rows['Mednom'] );
			
			if( $slDoctor != trim( $rows['Medcod'] )." - ".trim( $rows['Mednom'] ) )
			{
				echo "<option>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
		}
		else if ($caso == 1 or $caso == 3)
		{
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );
			
			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			{
				echo "<option>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		else if ($caso == 2 and $valCitas!= "on")
		{
			//if( $slDoctor != "{$rows['codigo']} - {$rows['descripcion']}" )
			
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );
			
			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			
			{
				echo "<option>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		
	}//for
	
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	
	echo "</div>"; //no imprimir
	//Aqui comienza la lista de pacientes
	
	//Buscando los pacientes que tienen cita
	//y no van para interconsulta
	if ($caso == 2 and $valCitas =="on")
	{
	   $sql = "SELECT
				fecha, 
				cod_equ, 
				TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi, 
				hf, 
				nom_pac, 
				mednom, 
				b.id,
				b.tipoA,
				b.tipoS,
				b.cedula,
				b.usuario,
				b.Nit_res,
				IF(cedula IN ((SELECT pacdoc FROM {$wbasedato}_000100 WHERE pacdoc = cedula AND pacact = 'on' )),'on','off') as act
			FROM
				{$wbasedato}_000051 a, 
				{$solucionCitas}_000009 b
			WHERE
				medcid = cod_equ
				AND medcod like '$filtro'
				AND fecha = '".$wfec."'
				AND atendido != 'on'
				AND asistida != 'on'
				AND Causa = ''
				AND nom_pac != 'CANCELADA'
				AND activo = 'A'
				AND cedula NOT IN (SELECT espdoc FROM {$wbasedato}_000141 WHERE espdoc = cedula AND esphor = TIME_FORMAT( CONCAT(hi,'00'), '%H:%i:%s') AND espmed = medcod )
			ORDER BY hi, mednom, nom_pac
			";

	} //AND asistida != ''  falta atendido
	else if ($caso == 3 or $caso == 1)
	{
		$sql = "select cod_med,cod_equ,cod_exa,fecha,TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,TIME_FORMAT( CONCAT(hf,'00'), '%H:%i') as hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,tipoA,cedula,Asistida,id from ".$solucionCitas."_000001 where fecha='".$wfec."' and cod_equ like '".$filtro."' and Atendido != 'on' and Asistida != 'on' and Activo='A' and Causa='' order by hi, cod_equ";
		//and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."'  validar el equipo  like '$filtro'
		
	}
	else if ($caso == 2 and $solucionCitas != "on")
	{
		$sql = "select cod_equ,cod_exa,fecha,TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,hf,nom_pac,nit_res,telefono,edad,comentario,usuario,activo,Asistida,id from ".$solucionCitas."_000009 where fecha='".$wfec."' and cod_equ like '".$filtro."' and Atendido != 'on' and Asistida != 'on' and Activo='A' and Causa='' order by hi, cod_equ";
		//and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."'   validar medico
	}
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	//echo "<br><br>";
	
	echo "<div id='imprimir'>";
	
	echo "<table border='1' style='border-collapse: collapse' WIDTH='19cm' style='font-size:10pt' id='pacientes'>";
	
	
	if( $num > 0 )
	{
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
		{
						
			$j=$i+1;
			//Definiendo la clase por cada fila
			if( $i%2 == 0 )
			{
				$class = "";
			}
			else
			{
				$class = "";
			}
			
			//para mostrar el nombre del equipo, del medico y del examen
			if ($caso == 3 or $caso == 1)
			{
				$sql1 = "select Codigo,Descripcion from ".$solucionCitas."_000003 where Codigo = '{$rows['cod_equ']}' and activo = 'A'";
				$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
				$rows1 = mysql_fetch_array( $res1 );
			
			
				$sql2 ="select Codigo, Nombre from ".$solucionCitas."_000008 where Codigo = '{$rows['cod_med']}' and activo = 'A'";
				$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
				$rows2 = mysql_fetch_array( $res2 );
			
			
				$sql3 ="select Codigo, Descripcion from ".$solucionCitas."_000006 where Codigo = '{$rows['cod_exa']}' and Cod_equipo = '{$rows['cod_equ']}' and activo = 'A'";
				$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
				$rows3 = mysql_fetch_array( $res3 );
				
				$sql4 ="select descripcion,nit from ".$solucionCitas."_000002 where nit = '{$rows['nit_resp']}' and activo = 'A'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				$rows4 = mysql_fetch_array( $res4 );
			}
			
			if ($caso == 2 and $valCitas != "on")
			{
				$sql4="select Codigo, Descripcion  from ".$solucionCitas."_000010 where codigo='{$rows['cod_equ']}' and activo = 'A' group by Codigo, Descripcion";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				$rows4 = mysql_fetch_array( $res4 );
				
				$sql5="select nit, Descripcion  from ".$solucionCitas."_000002 where nit='{$rows['nit_res']}' and activo = 'A'";
				$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
				$rows5 = mysql_fetch_array( $res5 );
			}
			
			if ($caso == 2 and $valCitas == "on")
			{
				$sql5="select nit, Descripcion  from ".$solucionCitas."_000002 where nit='{$rows['Nit_res']}' and activo = 'A'";
				$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
				$rows5 = mysql_fetch_array( $res5 );
				
			}
			
			//mostrar el encabezado de la tabla
			//citas caso 1 o caso 3
			if( $i == 0  and ($caso ==1 or $caso==3)){
				echo "	<tr class=''  align=center>";
				echo "		<td style='width:8%' align='center'>";
				echo "			<b>Nro</b>";
				echo "		</td>";
				echo "		<td style='width:8%' align='center'>";
				echo "			<b>Fecha</b>";
				echo "		</td>";
				echo "		<td style='width:8%' align='center'>";
				echo "			<b>Hora Inicial</b>";
				echo "		</td>";
				echo "		<td style='width:8%' align='center'>";
				echo "			<b>Hora Final</b>";
				echo "		</td>";
				echo "		<td style='width:8%' align='center'>";
				echo "			<b>Codigo</b>";
				echo "		</td>";
				echo "		<td style='width:12%' align='center'>";
				echo "			<b>Nombre</b>";
				echo "		</td>";
				/*echo "		<td style='width:12%' align='center'>";
				echo "			<b>Centro Costos</b>";
				echo "		</td>";
				echo "		<td style='width:12%' align='center'>";
				echo "			<b>Administrador</b>";
				echo "		</td>";*/
				echo "		<td style='width:12%' align='center'>";
				echo "			<b>Sala</b>";
				echo "		</td>";
				echo "		<td style='width:12%' align='center'>";
				echo "			<b>Solicitud</b>";
				echo "		</td>";
				if ($tipoAtencion=='on')
				{
					echo "		<td style='width:10%' align='center'>";
					echo "			<b>Tipo Atencion</b>";
					echo "		</td>";
				}
				// echo "		<td style='width:8%'>";
				// echo "			<b>Usuario</b>";
				// echo "		</td>";
				
				echo "	</tr>";
			}	
				
			if ($i == 0 and $caso == 2 and $valCitas == "on")  //citas clinica del sur
			{
				echo "	<tr class=''  align=center>";
				echo "		<td style='width:90'>";
				echo "			<b>Numero</b>";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			<b>Fecha</b>";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			<b>Hora</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Nombre</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Centro Costos</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Administrador</b>";
				echo "		</td>";
				echo "    <td>";
				echo "			<b>Servicio</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Tipo Servicio</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Usuario</b>";
				echo "		</td>";
				echo "	</tr>";
			}
				
			if ($i == 0 and @$valCitas != "on" and $caso==2)   //citas caso 2 diferentes a clinica del sur
			{	
				echo "	<tr class=''  align=center>";
				echo "		<td style='width:90'>";
				echo "			<b>Numero</b>";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			<b>Fecha</b>";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			<b>Hora</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Nombre</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Centro de Costos</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Administrador</b>";
				echo "		</td>";
				echo "		<td>";
				echo "			<b>Usuario</b>";
				echo "		</td>";
				
				echo "	</tr>";
			}
			
			//mostrar la informacion de las citas de esa fecha
			if ($caso ==1 or $caso==3)
			{
				echo "	<tr $class>";
				echo "		<td align=center>";
				echo "			".$j."";   
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['fecha']}";   
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['hi']}";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['hf']}";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['cedula']}";
				echo "		</td>";
				echo "		<td>";
				echo "			{$rows['nom_pac']}";
				echo "		</td>";
				/*echo "		<td>";
				echo "			{$rows4['descripcion']}";
				echo "		</td>";
				echo "		<td>";
				echo "			".$rows2['Nombre']."";
				echo "		</td>";*/
				echo "		<td bgcolor='".$color_fondo."'>";
				echo "			".$rows1['Descripcion']."";
				echo "		</td>";
				echo "		<td>";
				echo "			".$rows3['Descripcion']."";
				echo "		</td>";
				if ($tipoAtencion=='on')
				{
					echo "		<td>";
					echo "		{$rows['tipoA']}";
					echo "		</td>";
				}
				// echo "		<td align=center>";
				// echo "			{$rows['usuario']}";
				// echo "		</td>";
				
				
			}	
			
			//clinica del sur
			if (@$valCitas == "on" and $caso == 2)
			{
				$atencion=$rows['tipoA'];
				$atencion=explode("-", $atencion);
				@$atencion=$atencion[1];
				$servicio=$rows['tipoS'];
				$servicio=explode("-", $servicio);
				@$servicio=$servicio[1];
				
				echo "	<tr $class>";
				echo "		<td align=center>";
				echo "			".$j."";   
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['fecha']}";   
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['hi']}";
				echo "		</td>";
				echo "		<td>";
				echo "			{$rows['nom_pac']}";
				echo "		</td>";
				echo "		<td>";
				echo "			{$rows5['Descripcion']}";
				echo "		</td>";
				echo "		<td>";
			    echo "			{$rows['mednom']}";
			    echo "		</td>";
				echo "		<td>";
				echo "			$atencion&nbsp;";
				echo "		</td>";
				echo "		<td>";
				echo "			$servicio&nbsp;";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['usuario']}";
				echo "		</td>";
				
			}
			
			
			
			     //****************************revisar lo de asiste *************************
				if ($caso==2 and $valCitas!="on")
				{
					echo "	<tr $class>";
					echo "		<td align=center>";
					echo "			".$j."";   
					echo "		</td>";
					echo "		<td align=center>";
					echo "			{$rows['fecha']}";   
					echo "		</td>";
					echo "		<td align=center>";
					echo "			{$rows['hi']}";
					echo "		</td>";
					echo "		<td>";
					echo "			{$rows['nom_pac']}";
					echo "		</td>";
					echo "		<td>";
					echo "			{$rows5['Descripcion']}";
					echo "		</td>";
					echo "		<td>";
					echo "			".$rows4['Descripcion']."";
					echo "		</td>";
					echo "		<td align=center>";
					echo "			{$rows['usuario']}";   
					echo "		</td>";
					
				}
				
			echo "	</tr>";
		} //for
	}
	else{
		echo "<center>NO HAY TURNOS ASIGNADOS PARA HOY</center>";
	}
	
	
	echo "</table>";
	
	echo "</div>";
	
	//echo "<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' />";
	echo "</div>";
	
	echo "</form>";
	echo "</body>";
	echo "</html>";
	
	
	
}
?>


