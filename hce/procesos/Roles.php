<?php
include_once("conex.php"); header('Content-type: text/html;charset=ISO-8859-1'); ?>

<?php
/* ****************************************************************
   * PROGRAMA PARA ADMINISTRAR ROLES DE HCE
   ****************************************************************/
//==================================================================================================================================
//PROGRAMA                   : Roles.php
//AUTOR                      : Frederick Aguirre S.
//FECHA CREACION             : 2013-11-28
$wactualiz="2015-02-03";


//Para que en las solicitudes ajax no imprima <html><head> etc
if( isset($consultaAjax) == false ){
?>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<title>Detalle Protocolos</title>
	<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>	
	<script src="../../../include/root/toJson.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
	
	<style>
		.caja_flotante_query{
			position: absolute;
			top:0;
		}
	
		#lista_elementos .fila1, #lista_elementos .fila2 {
			cursor: pointer;
		}
	</style>

<script type="text/javascript">
	
	var editando_item = false;
	var ejeY = 0;
	
	$(document).ready(function() {		
		//Cuando presionen la tecla Enter dentro del input "buscar"
		/*$("#buscador").on("keyup", function(e) {
			if(e.which == 13){
				filtrarConBusqueda( $(this).val() );				
			}
		});*/
		
		//
		
		
		$(".caja_flotante_query").hide();
		var posicion_query = $(".caja_flotante_query").offset();
		var posicion_query_ori1 = $(".caja_flotante_query_ori1").offset();
		if( posicion_query != undefined ){
			var html_text = "<table width=100% id='tb1'><tr class='encabezadoTabla'>";
			html_text+=  $(".caja_flotante_query_ori1").html();
			html_text += "</tr></table>";
			$(".caja_flotante_query").html( html_text );
			$(".caja_flotante_query").css('background-color','white');
			
			var tamanos = new Array();
			$(".caja_flotante_query_ori1").find('td').each(function(){
				 tamanos.push( $(this).width() );
			});

			html_text = "<tr>";
			$('.caja_flotante_query_ori1').parent().find('tr:nth-child(3) td').each(function () {
				html_text+="<td style='width:"+$(this).width()+"px;'></td>";
			});
			html_text += "</tr>";
			$('#tb1').append(html_text);
		
			$(".caja_flotante_query").width( $('.caja_flotante_query_ori1').width() );
			$(".caja_flotante_query").css('marginLeft', posicion_query_ori1.left  );
			$(".caja_flotante_query").css('position', "absolute");
			
			
			$(window).scroll(function() {
				if ($(window).scrollTop() > posicion_query_ori1.top) {
					$(".caja_flotante_query").show();
					$(".caja_flotante_query").css('marginTop', $(window).scrollTop() );
				} else {
					$(".caja_flotante_query").css('marginTop', posicion_query_ori1.top );
					$(".caja_flotante_query").hide();
				};
			});
		}
		$('#buscador').quicksearch('#lista_elementos .buscarr');
	});
	
	function filtrarEmpresa(){
		var texto = $("#lista_empresas_buscar option:selected").text();
		texto = texto.split("-");
		if( texto.lenght < 2 ) texto[1] = "";
		filtrarConBusqueda( texto[1] );
	}
	
	function filtrarEnfMed(obj){
		obj = jQuery(obj);
		var valor = obj.val();
		
		if( valor == '' ){
			$("#lista_elementos tr").show();
			return;
		}
		
		$("#lista_elementos tbody tr:not(:first)").hide();
		$("#lista_elementos tbody tr:not(:first)").each(function(){
			if( $(this).attr(valor) == 'on' )
				$(this).show();
		});	
	}
	
	function filtrarConBusqueda( valor ){
		valor = $.trim( valor );
		if( valor == "" ){
			$("#lista_elementos tr").show();
			return;
		}
		valor = valor.toUpperCase();
		if( valor.length < 4 ){
			alert("Ingrese al menos 4 caracteres para realizar la busqueda");
			return;
		}
		$("#lista_elementos tbody tr:not(:first)").hide();
		var patt1 = new RegExp( valor , "g" );
		$('.parabuscar').each(function(){
			texto = $(this).text();
			//texto = $.trim(texto);		
			if( patt1.test( texto ) ){				
				$(this).parent().show();
			}
		});		
	}
	
	function nuevoRol( cod_formulario ){
		//Consultamos el nuevo consecutivo
		editando_item = false;
		ejeY = 0;
		cerrarConfigurarItem();
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Roles.php', { wemp_pmla: wemp_pmla, action: "consultarUltimoConsecutivo", consultaAjax: ''} ,
			function(data) {
				data = $.trim( data );
				if( (/^\d+$/).test(data) ){
					//valores por defecto
					$("#wrol").val( data );				
					$("#west").attr("checked",true);
					$("#div_configuracion_rol").show();
				}else{
					alert("Error "+data);				
				}
				$.unblockUI();
			});
	}
	
	function retornar(){
		ejeY = 0;
		cerrarConfigurarItem();
	}
	
	function cerrarConfigurarItem(){
		$("#wrol").val('');
		$("#wdesrol").val('');		
		$("#wgruemp").val('');
		$("#watr").attr("checked",false);
		$("#west").attr("checked",false);
		$("#div_configuracion_rol").hide();
		//Llevar al ppio de la pagina
		ejeY = ejeY - 50;
		//Para que vaya al tr donde estaba
		$('html, body').animate({
			scrollTop: ejeY+'px',
			scrollLeft: '0px'
		},0);
	}

	function mostrarRol( ele, codigo, descripcion, atribuciones, empresa, estado, esenfermera, esmedico ){
		var wdetpro = $("#wdetpro_global").val();
		var wemp_pmla = $("#wemp_pmla").val();
		editando_item = true;
		
		$("#wrol").val(codigo);
		$("#wdesrol").val(descripcion);		
		$("#wgruemp").val(empresa);
		( atribuciones == 'on' )? $("#watr").attr("checked",true) : $("#watr").attr("checked",false);
		( estado == 'on' )? $("#west").attr("checked",true) : $("#west").attr("checked",false);
		( esenfermera == 'on' )? $("#wesenfermera").attr("checked",true) : $("#wesenfermera").attr("checked",false);
		( esmedico == 'on' )? $("#wesmedico").attr("checked",true) : $("#wesmedico").attr("checked",false);
		
		//Guardar la distancia Y donde esta el tr, para que cuando le den click en guardar o cerrar se ubique donde estaba
		ele = jQuery( ele );
		posicion = ele.offset();
		ejeY = posicion.top;
		$("#div_configuracion_rol").show();
		//Para que suba al inicio de la pagina
		$('html, body').animate({
			scrollTop: '0px',
			scrollLeft: '0px'
		},0);
	}
	
	function guardarCambios(){
		var codigo = $("#wrol").val();
		var descripcion = $("#wdesrol").val();		
		var empresa = $("#wgruemp").val();
		var atribuciones = 'off';		
		if( $("#watr").is(":checked") ) atribuciones = 'on';		
		var estado = 'off';		
		if( $("#west").is(":checked") ) estado = 'on';
		var esenfermera = 'off';		
		if( $("#wesenfermera").is(":checked") ) esenfermera = 'on';
		var esmedico = 'off';		
		if( $("#wesmedico").is(":checked") ) esmedico = 'on';
		descripcion = $.trim(descripcion);
		if( codigo == '' ) return;
		if( descripcion == '' ){
			alert("Por favor ingrese la descripcion del rol"); return;
		}

		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Roles.php', { wemp_pmla: wemp_pmla, action: "guardarCambios", editando: editando_item, codigo: codigo, 
										descripcion: descripcion, empresa: empresa, atribuciones: atribuciones, estado: estado, esenfermera: esenfermera, esmedico: esmedico, consultaAjax: ''} ,
			function(data) {
				$.unblockUI();
				data = $.trim( data );
				if( data == 'OK' ){					
					crearEditarFilaItem( editando_item );
									
					ejeY = ejeY - 50;
					//Para que vaya al tr donde estaba
					$('html, body').animate({
						scrollTop: ejeY+'px',
						scrollLeft: '0px'
					},0);
				}else{
					alert("Error "+data);
					location.reload();
				}
			});
	}

	function crearEditarFilaItem( editando ){
		var clase_rojo = "";
		if ( $("#west").is(':checked') == false ){
			clase_rojo = "fondorojo";
		}
		$("#lista_elementos tr").show(); //Mostrar todos los tr
		$("#buscador").val("");
		$("#lista_empresas_buscar").val("");
		
		var codigo = $("#wrol").val();
		var descripcion = $("#wdesrol").val();		
		var empresa = $("#wgruemp").val();
		var atribuciones = 'off';		
		if( $("#watr").is(":checked") ) atribuciones = 'on';		
		var estado = 'off';		
		if( $("#west").is(":checked") ) estado = 'on';
		var esenfermera = 'off';		
		if( $("#wesenfermera").is(":checked") ) esenfermera = 'on';
		var esmedico = 'off';		
		if( $("#wesmedico").is(":checked") ) esmedico = 'on';
		
		var clase_fila = "";
		if( editando == false ){
			( $("#lista_elementos tr:last").hasClass("fila1") ) ? clase_fila = 'fila2' : clase_fila = 'fila1';			
		}else{
			( $("#filarol_"+codigo).hasClass("fila1") ) ? clase_fila = 'fila1' : clase_fila = 'fila2';
		}
		
		//Obtener la descripcion de la empresa del input select
		var empdes = $("#wgruemp option[value='"+empresa+"']").text();
		empdes = empdes.split("-");
		if( empdes[1] == undefined ) empdes[1] = "";
		var empresades = empdes[1];
		
		var html_code = "<tr id='filarol_"+codigo+"' class='"+clase_fila+" "+clase_rojo+"' ondblclick='mostrarRol(this, \""+codigo+"\",\""+descripcion+"\",\""+atribuciones+"\",\""+empresa+"\",\""+estado+"\",\""+esenfermera+"\",\""+esmedico+"\")' wesenfermera='"+esenfermera+"' wesmedico='"+esmedico+"'>";		
			html_code+="<td class='parabuscar' align='center' title='Codigo' >"+codigo+"</td>";
			html_code+="<td class='parabuscar' title='Descripcion' >"+descripcion+"</td>";
			html_code+="<td align='center' title='Atribuciones' >"+atribuciones+"</td>";
			html_code+="<td align='center' title='Es enfermera' >"+esenfermera+"</td>";
			html_code+="<td align='center' title='Es medico' >"+esmedico+"</td>";
			html_code+="<td class='parabuscar' title='Empresa' >"+empresades+"</td>";
		html_code+="</tr>";
		
		if( editando == false ){
			$("#lista_elementos tbody").append(html_code);			
			ele = $("#lista_elementos tr:last");
			posicion = ele.offset();
			ejeY = posicion.top;
		}else{
			var fila_antes = $("#filarol_"+codigo);
			fila_antes.after( html_code );
			fila_antes.remove();
		}
		
		$("#wrol").val('');
		$("#wdesrol").val('');		
		$("#wgruemp").val('');
		$("#watr").attr("checked",false);
		$("#west").attr("checked",false);
		$("#wesenfermera").attr("checked",false);
		$("#wesmedico").attr("checked",false);
		
		$("#div_configuracion_rol").hide();
	}
	
</script>

</head>

<body BGCOLOR="#ffffff">
<!-- Programa en PHP -->
<?php
	
} //--Si no hay consulta ajax





include_once("root/magenta.php");
include_once("root/comun.php");

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
$wdbmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wfecha=date("Y-m-d");
$whora = (string)date("H:i:s");
$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

//=================================================================================================================================
//***************************************** D E F I N I C I O N   D E   F U N C I O N E S *****************************************
//================================================================================================================================

if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == 'guardarCambios' ){							
		guardarRol( $_REQUEST['editando'], $_REQUEST['codigo'], $_REQUEST['descripcion'], $_REQUEST['empresa'], $_REQUEST['atribuciones'], $_REQUEST['estado'], $_REQUEST['esenfermera'], $_REQUEST['esmedico'] );		
	}else if(  $action == 'consultarUltimoConsecutivo' ){
		$ultimo_consecutivo = consultarUltimoConsecutivo();
		echo $ultimo_consecutivo;
	}
	return;
}

	function consultarUltimoConsecutivo(){
		global $conex;
		global $wbasedato;
		
		$q = " SELECT MAX(Rolcod) "
			."   FROM ".$wbasedato."_000019 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		$cod = 1;
		if ($num > 0){
			$row = mysql_fetch_array($res);
			$cod = $row[0]+1;
		}
		
		while ( strlen($cod) < 3 ){
			$cod = "0".$cod;			
		}
		return $cod;
	}	

	function guardarRol($editando, $wcod, $wdes, $wemp, $watr, $west, $wesenfermera, $wesmedico){

		global $conex;
		global $wbasedato;
		
		//Si se esta actualizando un campo
		if( $editando == 'true' ){
				$q= " UPDATE ".$wbasedato."_000019 SET Roldes='".$wdes."', Rolatr='".$watr."', Rolemp='".$wemp."', Rolest='".$west."', Rolenf='".$wesenfermera."', Rolmed='".$wesmedico."'"
					 ."   WHERE Rolcod= '".$wcod."'";	

				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				echo "OK";				
		}else{
			$wfecha = date('Y-m-d');
			$whora = date('H:i:s');
			//Se esta creando un campo nuevo
			$q= " INSERT ".$wbasedato."_000019 (   Medico       ,   fecha_data,   hora_data,    Rolcod    ,    Roldes    ,    Rolatr    ,    Rolemp    ,    Rolest    ,    		Rolenf     ,   		 Rolmed   ,     Seguridad       ) "
				 ."                     VALUES ('".$wbasedato."','".$wfecha."',  '".$whora."', '".$wcod."',   '".$wdes."',   '".$watr."',   '".$wemp."',  '".$west."',  '".$wesenfermera."',  '".$wesmedico."',   'C-".$wbasedato."') ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			
			if( mysql_insert_id() ){
				echo "OK";
			}else{
				echo "NO";
			}
		}		
	}

	function mostrar_grilla(){
		global $conex;
		global $wbasedato;
		global $wemp_pmla;		
		   
	   //Traigo todos los campos del formulario wdetpro
		$q = " SELECT Rolcod as codigo, Roldes as nombre, Rolatr as atribuciones, Rolemp as empresa, Rolest as estado,
					  Rolenf as esenfermera, Rolmed as esmedico, Empdes as nom_empresa "
			."   FROM ".$wbasedato."_000019 LEFT JOIN ".$wbasedato."_000025 ON ( Empcod = Rolemp )"
			."  ORDER BY Rolcod ";
		
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
	   
		echo "<div style='align:left;'><font size=4>Lista de Roles</font>&nbsp;&nbsp;<input type='button' value='Nuevo' onclick='nuevoRol()' /></div>";
		
		echo "<table id='lista_elementos'>";
		echo "<tr class='encabezadoTabla caja_flotante_query_ori1'>
				<td align='center'>Código</td>
				<td align='center'>Descripción</td>
				<td align='center'>Atribuciones</td>
				<td align='center'>Es Enfermera</td>
				<td align='center'>Es médico</td>
				<td align='center'>Empresa</td>
			 </tr>";
        $wclass = "fila1";
		
		if ($num > 0){
			while($row = mysql_fetch_assoc($res)){
				( $wclass == "fila1" )? $wclass="fila2" : $wclass="fila1";
				$wclassFila="";
				if( $row['esenfermera'] != 'on' ) $row['esenfermera'] = 'off';
				if( $row['esmedico'] != 'on' ) $row['esmedico'] = 'off';
				if( $row['estado'] == 'off' ) $wclassFila = "fondorojo";
				echo "<tr ondblclick='mostrarRol(this, \"".$row['codigo']."\",\"".$row['nombre']."\",\"".$row['atribuciones']."\",\"".$row['empresa']."\",\"".$row['estado']."\",\"".$row['esenfermera']."\",\"".$row['esmedico']."\")' class='".$wclass." ".$wclassFila." buscarr' id='filarol_".$row['codigo']."' wesenfermera='".$row['esenfermera']."' wesmedico='".$row['esmedico']."'>"; 					
				echo "<td class='parabuscar' align='center' title='Codigo'>".$row['codigo']."</td>";
				echo "<td class='parabuscar' title='Descripcion'>".$row['nombre']."</td>";
				echo "<td align='center' title='Atribuciones'>".$row['atribuciones']."</td>";
				echo "<td align='center' title='Es enfermera'>".$row['esenfermera']."</td>";
				echo "<td align='center' title='Es medico'>".$row['esmedico']."</td>";
				echo "<td class='parabuscar' title='Empresa'>".$row['nom_empresa']."</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
		echo "</div>";   		
	}  
	  	  
	//=================================================================================================================================
	//***************************** T E R M I N A   L A   D E F I N I C I O N   D E   F U N C I O N E S *******************************
	//=================================================================================================================================

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$empresa = strtolower($institucion->baseDeDatos);
	encabezado("ADMINISTRACION DE ROLES HCE", $wactualiz, $empresa);

	$q = " SELECT empcod, empdes "
		."   FROM ".$wbasedato."_000025 "
		."  WHERE empest = 'on' ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);  
	
	$width_sel = " width: 95%; ";
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/MSIE/i',$u_agent))
		$width_sel = "";
	//------------TABLA DE PARAMETROS-------------
	echo '<table align="center">';
	echo "<tr class='encabezadotabla'><td colspan=2 align='center'><font size=4>Filtrar</font></td></tr>";
	echo "<tr>";
	echo '<td class="encabezadotabla" width="80px">Grupo de empresas</td>';
	//LISTA DE ENTIDADES
	echo '<td class="fila1" width="auto">';
	echo "<div align='center'>";
	echo "<select id='lista_empresas_buscar' class='ui-corner-all' align='center' style='".$width_sel." margin:5px;' onclick='filtrarEmpresa()'>";
	echo "<option value=''></option>";
	while( $row = mysql_fetch_array($res) ){
		echo "<option value='".$row[0]."'>".$row[0]."-".$row[1]." </option>";
	}
	echo '</select>';
	echo '</div>';
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadotabla'>Filtrar</td>";
	echo "<td class='fila1' align='center'>";
	echo "<select id='filtrar' style='".$width_sel." margin:5px;' onclick='filtrarEnfMed(this)'>
			<option value=''>TODOS</option>
			<option value='wesenfermera'>Enfermeras</option>
			<option value='wesmedico'>Medicos</option>
		</select>
	";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='encabezadotabla'>Buscar</td>";
	echo "<td class='fila1' align='center'>";
	echo "<input type='text' id='buscador' style='".$width_sel." margin:5px;'/>";
	echo "</td>";
	echo "</tr>";
	echo "</table><br>";
	//FIN LISTA ENTIDADES		
	//------------FIN TABLA DE PARAMETROS-------------
		
		
	echo "<center><div id='div_configuracion_rol' style='display:none'>";
	echo "<table>";
	echo "<tr class='encabezadotabla'>";
	echo "<td colspan=5 align='center'><font size=4>CONFIGURACIÓN DEL ROL</font></td>";
	echo "</tr>";
	//Código
	echo "<tr class=fila1>";
	echo "<td><b>Código Rol</b></td>";
	echo "<td colspan=4><input type='text' id='wrol' style='".$width_sel." margin:5px;' disabled></td>"; 
	echo "</tr>";

	//Descripción
	echo "<tr class=fila1>"; 
	echo "<td><b>Nombre</b></td>";
	echo "<td colspan=4><input type='text' id='wdesrol' style='".$width_sel." margin:5px;'></td>"; 
	echo "</tr>";

	//Atribuciones y estado
	echo "<tr class=fila1>";
	echo "<td><b>Tiene Atribuciones</b></td>";
	echo "<td align='center'>";
	echo "<input type='checkbox' id='watr'>";
	echo "</td>";
	echo "<td width='30%'>&nbsp;</td>";
	echo "<td><b>Estado</b></td>";
	echo "<td align='center'>";
	echo "<input type='checkbox' id='west'>";
	echo "</td>";
	echo "</tr>";
	
	//Es enferma o es medico
	echo "<tr class=fila1>";
	echo "<td><b>Es enfermera</b></td>";
	echo "<td align='center'>";
	echo "<input type='checkbox' id='wesenfermera'>";
	echo "</td>";
	echo "<td width='30%'>&nbsp;</td>";
	echo "<td><b>Es medico</b></td>";
	echo "<td align='center'>";
	echo "<input type='checkbox' id='wesmedico'>";
	echo "</td>";
	echo "</tr>";

	//EMPRESA	
	echo "<tr class=fila1>";
	echo "<td><b>G. de Empresas: </b></td>";
	echo "<td colspan=4>";
	echo "<SELECT class='ui-corner-all' align='center' style='".$width_sel." margin:5px;' id='wgruemp'>";
	mysql_data_seek($res, 0) ;//reset result set
	echo "<option value=''></option>";
	while( $row = mysql_fetch_array($res) ){
		echo "<option value='".$row[0]."'>".$row[0]."-".$row[1]." </option>";
	}
	echo "</SELECT>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<input type='button' value='Guardar' onclick='guardarCambios()' />&nbsp;&nbsp;&nbsp;<input type='button' value='Cancelar' onclick='cerrarConfigurarItem()' />";
	echo "</div></center>";
	echo "<br><br>";
	
	echo "<center><a href='#' onclick='retornar()'>RETORNAR</a></center>";
	echo "<br><br>";
	
	echo "<center>";
	mostrar_grilla();
	echo "</center>";
	
	echo "<br><br>";
	echo "<center><a href='#' onclick='retornar()'>RETORNAR</a></center>";
 
   //Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/ajax-loader5.gif'/>";
	echo "<br><br> Por favor espere un momento ... <br><br>";
	echo '</div>';
	echo '<div class="caja_flotante_query"></div>';
	echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."' />";
?>
</form>
</body>
</html>
