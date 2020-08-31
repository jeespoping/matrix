<style>
<!--
.saltopagina{
	page-break-after: always
}

.centrar {  
     position:absolute;  
     background-color:#FFFFFF;  
     width:700px;  
     height:500px;  
     top:50%;  
     margin-top:-250px;  
 }
 
 .centrar2 {  
     position:absolute;  
     top:50%;
     margin-top: expression( document.body.clientHeight-document.getElementById( 'dvFormulas').clientHeight)/2 ;
     
 }
 
-->
</style>

 <script type="text/javascript">

 	/**
 	 * PARA PAGINAR
 	 */
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

 	function paginar( campo, principal ){

 	 	if( campo ){
 	 	 	if( campo.tagName ){

 	 	 		var cabecera = document.getElementById('hiPaciente').value;

 	 	 	 	switch( campo.tagName ){
 	 	 	 	
 	 	 	 		case 'TABLE':
 	 	 	 			var aux = document.createElement( "div" );
 	 	 	 			aux.innerHTML = "<table align='center' style='border: 1px solid black; border-collapse: collapse; font-size: 8pt; width: 100%;'></table>";

 	 	 	 			tabla = campo.cloneNode(true);

 	 	 	 			var sumaAltura = 0;

 	 	 	 			for( var i = 0; i < campo.rows.length; i++ ){

							posFila = findPosY( campo.rows[i] );

							sumaAltura = sumaAltura + campo.rows[i].clientHeight;
							
							posFila = posFila+campo.rows[i].clientHeight;


							if( sumaAltura > alturaPagina ){

								restoPaginas = restoPaginas+(alturaPagina+paginas*alturaPagina-posFila+campo.rows[i].clientHeight );
								paginas++;

								sumaAltura = campo.rows[i].clientHeight;

								var aux2 = document.createElement( "div" );
								aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
								aux2.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( paginas )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>"
								principal.appendChild( aux2.firstChild );
								
								principal.appendChild( aux.firstChild );

								aux.innerHTML = "<div class='saltopagina'></div>";
								principal.appendChild( aux.firstChild );

								aux.innerHTML = "<table align='center' style='border: 1px solid black; border-collapse: collapse; font-size: 8pt; width: 100%;'></table>";
							}

							var fila = aux.firstChild.insertRow( aux.firstChild.rows.length );
		 	 	 	 		
	 	 	 				for( var  j = 0; j < tabla.rows[i].cells.length ; j++){
	 	 	 					fila.appendChild( tabla.rows[ i ].cells[j] );
	 	 	 				}
 	 	 	 			}

 	 	 	 			paginas++;
	 	 	 	 		var aux2 = document.createElement( "div" );
						aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
						aux2.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( paginas )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>"
						principal.appendChild( aux2.firstChild );

 	 	 	 			campo.style.display = 'none';
 	 	 	 			principal.appendChild( aux.firstChild );
 	 	 	 			
 	 	 	 	 	break;

 	 	 	 	 	case 'DIV':{
 	 	 	 	 		inicial = findPosY( campo );

 	 	 	 	 		var aux = document.createElement( "div" );	
	 	 	 			aux.innerHTML = "<a>Página: "+parseInt( paginas+1 )+"<br><br></a>";
	 	 	 			aux.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( paginas+1 )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>"

	 	 	 			campo.insertBefore( aux.firstChild, campo.childNodes[0] );

// 	 	 	 	 		if( campo.clientHeight > alturaPagina ){
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

//		 	 	 	 	 	 			i--;
//		 	 	 	 	 	 			i--;
	
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
 	/**
 	 * FIN DE PAGINAR
 	 */

 	/***********************************************************************************
 	 * Marca todas las opciones como on cuando la Opcion Todas esta en on
 	 ************************************************************************************/
	function marcarOpciones( campo ){

		if( campo.checked == true ){
			for( var i = 0; i < 5; i++ ){
				document.forms[0].elements[ 'cbOpt['+i+']' ].checked = true;
			}
		}
		else{
			for( var i = 0; i < 5; i++ ){
				document.forms[0].elements[ 'cbOpt['+i+']' ].checked = false;
			}
		}
	}

	/***********************************************************************************
	 * Desmarca la opcion de Todas en caso de que alguna de las opciones este en false
	 ***********************************************************************************/
	function desmarcarTodas( campo ){

		if( campo.checked == false ){
			document.forms[0].elements['cbTodas'].checked = false;
		}
	}

	function agregarOpt( campo ){

		var add = '';

		if( document.forms[0].elements['cbOpt[0]'].checked == true ){
			add = add + '&cbOpt[0]=on'
		}
		else{
			add = add + '&cbOpt[0]=off'
		}
		
		if( document.forms[0].elements['cbOpt[1]'].checked == true ){
			add = add + '&cbOpt[1]=on'
		}
		else{
			add = add + '&cbOpt[1]=off'
		}

		if( document.forms[0].elements['cbOpt[2]'].checked == true ){
			add = add + '&cbOpt[2]=on'
		}
		else{
			add = add + '&cbOpt[2]=off'
		}

		if( document.forms[0].elements['cbOpt[3]'].checked == true ){
			add = add + '&cbOpt[3]=on'
		}
		else{
			add = add + '&cbOpt[3]=off'
		}

		if( document.forms[0].elements['cbOpt[4]'].checked == true ){
			add = add + '&cbOpt[4]=on'
		}
		else{
			add = add + '&cbOpt[4]=off'
		}

//		for( var i = 0; i < document.forms[0].elements['optHis'].length; i++ ){
//
//			if( document.forms[0].elements['optHis'][i].checked == true ){
//
////				alert( "Hola....."+document.forms[0].elements['optHis'][i].value );
//
//				switch( document.forms[0].elements['optHis'][i].value*1 ){
//					case 1:
////						alert( "Hola2.....:"+document.forms[0].elements['optHis'][i].value*1 );
//						add = add + '&uing=on';
//						break;
//
//					case 2:
//						add = add + '&ing2=%';
//						break;
//
//					case 3:
//						add = add + '&ing2=';
//						break;
//
//					default: break;
//				}
//				
//			}
//		}

		campo.href = campo.href + add; 
	}

	function pordefectoTodas(){

		document.getElementById( 'idTodas' ).checked = true;
		marcarOpciones( document.getElementById( 'idTodas' ) );
		
	}

	window.onload = function(){
		
//		if( document.getElementById( 'dvFormulas' ) ){
//			document.getElementById( 'dvFormulas' ).style.marginTop = document.getElementById('bdFormulas').clientHeight/2-document.getElementById( 'dvFormulas' ).clientHeight/2;
//		}
//
//		paginar( document.getElementById( 'dvNotas' ), document.getElementById( 'dvNotas' ) );
//		paginar( document.getElementById( 'dvFormulas1' ), document.getElementById( 'dvFormulas1' ) );
//
//		//paginando procedimientos
////		var dvPro = paginar( document.getElementById( 'dvProcedimiento' ), document.getElementById( 'dvProcedimiento' ) );
//		var dvPro = document.getElementById( 'dvProcedimiento' );
//
//		for( var i = 0; i < dvPro.childNodes.length; i++ ){
//
//			if( dvPro.childNodes[i].className != "saltopagina" ){
//				if( true || dvPro.childNodes[i].tagName == "div" &&  dvPro.childNodes[i].tagName == "DIV"  ){
//					paginar( dvPro.childNodes[i], dvPro.childNodes[i] );
//				}
//			}
//		}
//
//		//CTC
//		paginar( document.getElementById( 'dvCTC' ), document.getElementById( 'dvCTC' ) );
		
		//Fin de paginar procedimientos
		
		paginar( document.getElementById( 'tbHC' ), document.getElementById( 'dvHC' ) );

//		if( document.getElementById( 'dvProcedimiento' ) ){
//			document.getElementById( 'dvProcedimiento' ).style.marginTop = document.getElementById('bdProcedimiento').clientHeight/2-document.getElementById( 'dvProcedimiento' ).clientHeight/2;
//		}
	}
</script>
<body width='718px' style='margin-left:10mm; margin-right:10mm;width:718px'>

<?php
include_once("conex.php");

/*********************************************************************************************************
 * 										FUNCIONES
 *********************************************************************************************************/

function informacionPaciente( $his){
	
	global $conex;
	global $wbasedato;
	global $wbdempresas;
	
//	$emp = $exp[0];
//	$tab = $exp[1];
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000002
			WHERE
				pachis = '$his'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows;
	}
	
	return '';
}

function impresionCTC( $nit ){
	
	global $conex;
	global $wbasedato;
	global $wbdempresas;
	
	$exp = explode( "-", $wbdempresas );
	
	$emp = $exp[0];
	$tab = $exp[1];
	
	$ctc = '';
	
	if( $emp == $wbasedato ){
		
		$sql = "SELECT
					Empimp
				FROM
					{$emp}_{$tab}
				WHERE
					empnit = '$nit'
					AND empest = 'on'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$ctc = $rows['Empimp'];
		}
		else{
			$ctc = "impresionCTCInstituto.php";
		}
		
		return $ctc;
	}
	else{
		
		$sql = "SELECT
					impresion
				FROM
					{$emp}_{$tab}
				WHERE
					nit = '$nit'
					AND activo = 'A'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$ctc = $rows['impresion'];
		}
//		else{
//			$ctc = "impresionCTCInstituto.php";
//		}
		
		return $ctc;
		
	}
}


function buscarCTCImprimir( $fecha, $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$ctc = '';
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000001
			WHERE
				hclfec = '$fecha'
				AND hclhis = '$his'
				AND hcling = '$ing'
			";
				
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000003
			WHERE
				ingfin = '$fecha'
				AND inghis = '$his'
				AND inging = '$ing'
			";
					
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$nit = substr( $rows['Ingemp'], 0, strpos( $rows['Ingemp'], '-' ) );
		
		$ctc = impresionCTC( $nit );
		
		return "./".$ctc; 
	}
	
	return '';

}

/*********************************************************************************************************
 * 										INCIO DEL PROGRAMA
 *********************************************************************************************************/

if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
	exit;
}

include_once("root/comun.php");

include_once( "../../consultorios/procesos/funcionesGenerales.php" );

$conex = obtenerConexionBD("matrix");

$key = substr($user, 2, strlen($user));

$infoMedico = new classMedico( $doc );

$wbasedato = $infoMedico->bdHC;
$wbasecitas = $infoMedico->bdCitas;
$wbdempresas = $infoMedico->bdEmpresas;

/****************************************************************************************
 * Si no existe historia se crea un buscador para encontrar el paciente con todas 
 * las posibles notas creadas a la fecha
 ****************************************************************************************/
if( !isset($his ) ){
	
	echo "<form>";
	
	echo "<INPUT type='hidden' name='doc' value='$doc'>";
	
	$doctorName = $infoMedico->nombre;
	$titulo = "IMPRESIONES DR. ".strtoupper( $doctorName );
	encabezado( $titulo, "2010-01-13", "fmatrix" );
	echo "<br>";
	
	//Seteando txHis
	if( !isset( $txHis ) ){
		$txHis = '';
	}
	else{
		$txFecha = '';	
	}
	
	if( !isset( $txDoc ) ){
		$txDoc = '';
	}
	else{
		$txFecha = '';
	}
	
	if( !isset( $txNom ) ){
		$txNom = '';
	}
	else{
		$txFecha = '';
	}
	
	if( !isset( $txFecha ) ){
		$txFecha = date("Y-m-d");
	}
	
	echo "<table align='center'>";
	echo "<tr class='encabezadotabla' align='center'>";
	echo "<td colspan='2'>Paramétros de Busqueda</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' style='width:100'>Historia</td>";
	echo "<td class='fila2' style='width:100'><INPUT type='text' name='txHis' style='width:100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' style='width:150'>Nro de documento</td>";
	echo "<td class='fila2' style='width:100'><INPUT type='text' name='txDoc' style='width:100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' style='width:100'>Nombre</td>";
	echo "<td class='fila2' style='width:100'><INPUT type='text' name='txNom' style='width:100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align='center' colspan='2'>";
	echo "<br><INPUT type='submit' style='width:100' value='Buscar'>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	
	echo "<br><br>";
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td>";
	echo "<INPUT type='checkbox' name='cbTodas' id='idTodas' onclick='marcarOpciones( this );'> Todas";
	echo "</td>";
	echo "<td>";
	echo "<input type='checkbox' name='cbOpt[0]' checked onclick='javascript: desmarcarTodas( this );'> Historia";
	echo "</td>";
	echo "<td>";
	echo "<input type='checkbox' name='cbOpt[1]' onclick='javascript: desmarcarTodas( this );'> Notas";
	echo "</td>";
	echo "<td>";
	echo "<input type='checkbox' name='cbOpt[2]' onclick='javascript: desmarcarTodas( this );'> Formulas";
	echo "</td>";
	echo "<td>";
	echo "<input type='checkbox' name='cbOpt[3]' onclick='javascript: desmarcarTodas( this );'> Procedimientos";
	echo "</td>";
	echo "<td>";
	echo "<input type='checkbox' name='cbOpt[4]' onclick='javascript: desmarcarTodas( this );'> Justificaciones CTC";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<br>";
	
//	echo "<table align='center'>";
//	echo "<tr>";
//	echo "<td>";
//	echo "<input type='radio' name='optHis' checked value='1'> Ultimo ingreso";
//	echo "</td>";
//	echo "<td>";
//	echo "<input type='radio' name='optHis' value='2'> Toda la Historia";
//	echo "</td>";
//	echo "<td>";
//	echo "<input type='radio' name='optHis' value='3'> Ingreso Elegido";
//	echo "</td>";
//	echo "</tr>";
//	echo "</table>";
	
	$sql = "SELECT
				pachis as his,
				pacnid as doc,
				hclfec as fec,
				pacnpa as nom,
				hcling as ing
			FROM
				{$wbasedato}_000002 b, {$wbasedato}_000001 c 
			WHERE
				pachis like '%$txHis%'
				AND pachis = hclhis
				AND pacnpa like '%$txNom%'
				AND pacnid like '%$txDoc%'
				AND hclfec like '%$txFecha%'
			ORDER BY
				hclhis, hcling desc
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		echo "<br><br>";
		echo "<table align='center'>";
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td style='width:100'>Historia</td>";
		echo "<td style='width:300'>Nombre</td>";
		echo "<td style='width:120'>Nro de<br>Documento</td>";
		echo "<td style='width:120'>Fecha de<br>consulta</td>";
		echo "<td style='width:110'>Impresion</td>";
		echo "<td style='width:110'>Todos<br>los ingresos</td>";
		echo "</tr>";
		
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$fila = "class='fila".($i%2+1)."'";
			
			echo "<tr $fila>";
			echo "<td align='center'>{$rows['his']}-{$rows['ing']}</td>";
			echo "<td>{$rows['nom']}</td>";
			echo "<td align='center'>{$rows['doc']}</td>";
			echo "<td align='center'>{$rows['fec']}</td>";
			echo "<td align='center'><a target='_blank' href='impresion.php?his={$rows['his']}&ing={$rows['ing']}&fecha={$rows['fec']}&doc=$doc' onclick='agregarOpt( this );'>Imprimir</a></td>";
			echo "<td align='center'><a target='_blank' href='impresion.php?his={$rows['his']}&ing={$rows['ing']}&fecha={$rows['fec']}&doc=$doc&ing2=%' onclick='agregarOpt( this );'>Imprimir</a></td>";
			echo "</tr>";
		}
		
		echo "</table>";
	}
	
	echo "<br><br>";
	echo "<center><INPUT type='button' value='Cerrar' onclick='javascript: cerrarVentana();' style='width:100'></center>";
	
	echo "</form>";
}
else{
	
	if( isset($cbOpt) ){

		$auxIng = $ing;
		
		if( $cbOpt[1] == 'on' ){
			include_once("./impresionNotas.php");

			if( !empty($nota) && ( $cbOpt[0] == 'on' || $cbOpt[2] == 'on' || $cbOpt[3] == 'on' || $cbOpt[4] == 'on' ) ){
				echo "<h1 class='saltopagina'></h1>";
			}
		}

		if(  $cbOpt[2] == 'on' ){
			include_once("./impresionFormulasMedicas.php");

			if( true || !empty( $formula ) && ( $cbOpt[0] == 'on' || $cbOpt[3] == 'on' || $cbOpt[4] == 'on' ) ){
				echo "<h1 class='saltopagina'></h1>";
			}
		}

		if( $cbOpt[3] == 'on' ){
			include_once("./impresionProcedimientos.php");
	
			if( !empty( $procedimiento ) && ( $cbOpt[0] == 'on' || $cbOpt[4] == 'on' ) ){
				echo "<h1 class='saltopagina'></h1>";
			}
		}
		
		$ing = $auxIng;

		if( $cbOpt[4] == 'on' ){
			
			$programa = buscarCTCImprimir( $fecha, $his, $ing );
			
			if( !empty($programa) ){
				@include_once( $programa );
				
				if( count( $infoCTC ) > 0 && !empty($infoCTC) ){
					echo "<h1 class='saltopagina'></h1>";
				}
			}
		}
		 
		if( $cbOpt[0] == 'on' ){
			include_once("./impresionHCConsultorio.php");

			if( ( $cbOpt[4] == 'on' ) ){
//				echo "<h1 class='saltopagina'></h1>";
			}
		}
	}
	else{
		
		$auxIng = $ing; 
		
		include_once("./impresionNotas.php");

		if( !empty($nota) ){
			echo "<h1 class='saltopagina'></h1>";
		}


		include_once("./impresionFormulasMedicas.php");

		if( !empty( $formula ) ){
			echo "<h1 class='saltopagina'></h1>";
		}


		include_once("./impresionProcedimientos.php");

		if( !empty( $procedimiento ) ){
			echo "<h1 class='saltopagina'></h1>";
		}

		include_once("./impresionHCConsultorio.php");

		echo "<h1 class='saltopagina'></h1>";
		
		$ing = $auxIng;
		
		$programa = buscarCTCImprimir( $fecha, $his, $auxIng );
			
		if( !empty($programa) ){
			include_once( $programa );
		}

//		include_once("impresionCTCCoomeva.php");
	}
	
	$informacionPaciente = informacionPaciente( $his );
	
	echo "<input type='hidden' value='{$informacionPaciente['Pacnpa']}.   Nro. Identificación: {$informacionPaciente['Pacnid']}' id='hiPaciente'>";
}

if( isset($sala) ){
	echo "<script>pordefectoTodas();</script>";
}
?>
</body>