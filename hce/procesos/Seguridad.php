<?php
include_once("conex.php"); header('Content-type: text/html;charset=ISO-8859-1'); ?>

<?php
//Para que en las solicitudes ajax no imprima <html><head> etc
if( isset($consultaAjax) == false ){	
?>
<html>
<head>
<title>Esquemas de Seguridad</title>
<script type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
</head>

<body class='fila2'>
<script type="text/javascript">
    function hex_sha1(a){return rstr2hex(rstr_sha1(str2rstr_utf8(a)))}
	function hex_hmac_sha1(a,b){return rstr2hex(rstr_hmac_sha1(str2rstr_utf8(a),str2rstr_utf8(b)))}
	function sha1_vm_test(){return hex_sha1("abc").toLowerCase()=="a9993e364706816aba3e25717850c26c9cd0d89d"}
	function rstr_sha1(a){return binb2rstr(binb_sha1(rstr2binb(a),a.length*8))}
	function rstr_hmac_sha1(c,f){var e=rstr2binb(c);if(e.length>16){e=binb_sha1(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binb_sha1(a.concat(rstr2binb(f)),512+f.length*8);return binb2rstr(binb_sha1(d.concat(g),512+160))}
	function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}
	function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}
	function rstr2binb(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(24-c%32)}return a}
	function binb2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(24-c%32))&255)}return a}function binb_sha1(v,o){v[o>>5]|=128<<(24-o%32);v[((o+64>>9)<<4)+15]=o;var y=Array(80);var u=1732584193;var s=-271733879;var r=-1732584194;var q=271733878;var p=-1009589776;for(var l=0;l<v.length;l+=16){var n=u;var m=s;var k=r;var h=q;var f=p;for(var g=0;g<80;g++){if(g<16){y[g]=v[l+g]}else{y[g]=bit_rol(y[g-3]^y[g-8]^y[g-14]^y[g-16],1)}var z=safe_add(safe_add(bit_rol(u,5),sha1_ft(g,s,r,q)),safe_add(safe_add(p,y[g]),sha1_kt(g)));p=q;q=r;r=bit_rol(s,30);s=u;u=z}u=safe_add(u,n);s=safe_add(s,m);r=safe_add(r,k);q=safe_add(q,h);p=safe_add(p,f)}return Array(u,s,r,q,p)}
	function sha1_ft(e,a,g,f){if(e<20){return(a&g)|((~a)&f)}if(e<40){return a^g^f}if(e<60){return(a&g)|(a&f)|(g&f)}return a^g^f}function sha1_kt(a){return(a<20)?1518500249:(a<40)?1859775393:(a<60)?-1894007588:-899497514}
	function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}
	function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};
	
	$(document).ready(function() {

            $('#wcodusu').focus();
 			var arr_wrol = eval('(' + $('#arr_wrol').val() + ')');
			var roles    = new Array();
            var index    = -1;
            for (var cod_rol in arr_wrol)
            {
                index++;
                roles[index]                = {};
                roles[index].value          = cod_rol+'-'+arr_wrol[cod_rol];
                roles[index].label          = cod_rol+'-'+arr_wrol[cod_rol];
                roles[index].codigo         = cod_rol;
                roles[index].nombre         = cod_rol+'-'+arr_wrol[cod_rol];
            }

            var arr_usu  = eval('(' + $('#arr_usu').val() + ')');
            var usuarios = new Array();
			var index   = -1;
            for (var cod_usu in arr_usu)
            {
                index++;
                usuarios[index]                = {};
                usuarios[index].value          = cod_usu;
                usuarios[index].label          = cod_usu+'-'+arr_usu[cod_usu];
                usuarios[index].codigo         = cod_usu;
            }            

            $( "#wcodusu" ).autocomplete({
		      source: usuarios,
		      select:     function( event, ui ){
                    var cod_sel = ui.item.codigo;
                    $("#wcodusu").attr("codigo",cod_sel);
                }
		    });

			$( "#wrol" ).autocomplete({
		      source: roles,
		      select:     function( event, ui ){
                    var cod_sel = ui.item.codigo;
                    var nom_sel = ui.item.nombre;
                    $("#wrol").attr("codigo",cod_sel);
                    $("#wrol").attr("nombre",nom_sel);
                }
		    });

		    $('#wrol').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });

		indice_viejo = $("#windmen").val();
		$(".solonumeros").keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]|[\%]/g, ""));
		});
	});
	
    function completarwrol (codigo)
    {    	
        var campos=codigo.split('-');
    	$("#wrol").attr("codigo",campos[0]);
        $("#wrol").attr("nombre",codigo);
        $("#wrol").val(codigo);
    }

	function confirmarRol(rolConsultado){
		var rolActualizar = $("#wrol").val();		
		var rolCons = rolConsultado.split("-");
		var rolAct = rolActualizar.split("-");
		
		if($.trim(rolCons[0]) == $.trim(rolAct[0]))
		{
			//Submit
			$("#Actualizar").val("Actualizar");
			$("#Actualizar").attr("name","Actualizar");			
			document.forms.seguridad.submit();
		}
		else
		{
			// Cambiar de Rol
			var respuesta = confirm("El rol consultado ("+rolConsultado+") es diferente al rol a actualizar ("+rolActualizar+"), Desea continuar?");
			if (respuesta == true) {
			    $("#Actualizar").val("Actualizar");
				$("#Actualizar").attr("name","Actualizar");
				document.forms.seguridad.submit();
			} else {
			    //Cancela
			}			
		}
				
	}

	function consultar_alumnos(){
		var codigo = $("#buscar_codigo_alumno").val();
		if( codigo == '')
			return;
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		
		$.post('Seguridad.php', { wemp_pmla: wemp_pmla, codigo_alumno: codigo, action: "consultarAlumnos", consultaAjax: aleatorio} ,
			function(data) {
				data = $.trim(data);
				//Si recibo codigo html que comienze con "<option"
				if( (/^<option/).test(data) ){
						data = "<option value=''>&nbsp;</option>"+data;
						$("#select_alumnos").html( data );
						if( $("#select_alumnos option").length == 2 ){  //Si solo trajo un alumno, que lo agregue en la lista
							$("#select_alumnos option:last").attr('selected', true);
							agregar_alumnos();
						}
				}else if( data == "NO"){
					alert("No hay usuarios con el codigo|nombre ingresado");
				}else{
					alert("Error");					
				}
				$.unblockUI();
			}
		);		
	}
	
	function quitarAlumno(obj){
		obj = jQuery( obj );
		obj.parent().remove();
		//obj.remove();
		var indice = 0;
		$(".alumno").each(function(){
			indice++;
			$(this).attr("name", "walumno"+indice);			
		});
		$("#wnumero_alumnos").val( indice );
	}

	function agregar_alumnos(){
		var codigo_elegido = $("#select_alumnos").val();
		var texto_elegido = $("#select_alumnos option:selected").text();
		if(codigo_elegido == "")
			return;
		var indice_alumno = $(".alumno").length;
		indice_alumno++;
		
		var existe = false;
		$(".alumno").each(function(){
			if( $(this).val() == codigo_elegido ){
				existe = true;
			}
		});
		if( existe == true )	
			return;
		
		
		$("#wnumero_alumnos").val( indice_alumno );
		var html_code = "<div><input type='checkbox' class='alumno' onclick='quitarAlumno(this)' name='walumno"+indice_alumno+"' value='"+codigo_elegido+"' checked /><span>"+texto_elegido+"</span><br></div>";
		$("#lista_alumnos_agregados").append( html_code );
	}
	
	function checkearColumna( elemento, tipo, sufijo_id ){
		elemento = jQuery( elemento );
		
		var sufijo = tipo+"_"+sufijo_id;		
		var radios = $("input[id^="+sufijo+"]"); //Se escojen todos los input cuyo id COMIENZE por el sufijo
		var chekeado = true;
		
		if( elemento.is(":checked") ){
			radios.attr("checked",true);
		}else{
			radios.attr("checked",false);
			chekeado = false;
		}
		
		if( tipo == "wopc"){ //Si quitan este checked, se debe quitar el de grabar e imprimir
			if( chekeado == false ){
				sufijo = "wgra"+"_"+sufijo_id;
				radios = $("input[id^="+sufijo+"]");
				radios.attr("checked",false);
				
				sufijo = "wimp"+"_"+sufijo_id;
				radios = $("input[id^="+sufijo+"]");
				radios.attr("checked",false);
			}
		}else if( tipo == "wgra" || tipo == "wimp"){ //Si lo ponen en checked, Seleccionar tambien debe ser checked
			if( chekeado == true){
				sufijo = "wopc"+"_"+sufijo_id;
				radios = $("input[id^="+sufijo+"]");
				radios.attr("checked",true);			
			}
		}	
	}
	
	function verificaSel( obj ){
		//Si le quitan el checked, debe quitar el de grabar e imprimir
		if( obj.checked == false){
			var identi = obj.id;
			var arreglo = identi.split('_');
			var id_graba = "wgra_"+arreglo[1];
			var id_imp = "wimp_"+arreglo[1];
			
			document.getElementById(id_graba).checked = false;
			document.getElementById(id_imp).checked = false;
		}		
	}
	function verificaGra( obj ){
		//Si lo ponen en checked, Seleccionar tambien debe ser checked
		if( obj.checked == true){
			var identi = obj.id;
			var arreglo = identi.split('_');
			var id_opc = "wopc_"+arreglo[1];			
			document.getElementById(id_opc).checked = true;
		}	
	}
	function verificaImp( obj ){
	//Si lo ponen en checked, Seleccionar tambien debe ser checked
		if( obj.checked == true){
			var identi = obj.id;
			var arreglo = identi.split('_');
			var id_opc = "wopc_"+arreglo[1];			
			document.getElementById(id_opc).checked = true;
		}	
	}
	
	function clave()
	{
		wclave =document.getElementById('wclave').value;
		if (wclave != "")
		{
			wclave = hex_sha1(document.getElementById('wclave').value);
			document.getElementById('wclave').value=wclave;
		}
	}

	function enter()
	{
	   document.forms.seguridad.submit();
	}
	
	function cerrarVentana()
	 {
      window.close();		  
     } 
     
    
	function iniciar(k)
	{
		if (k > 1)
		{
			if(  document.getElementById("wopc_"+k) != null ){
				document.getElementById("wopc_"+k).checked = false;
				document.getElementById("wopc_"+k).value = 'off';
			}
			if(  document.getElementById("wgra_"+k) != null ){
				document.getElementById("wgra_"+k).checked = false;
				document.getElementById("wgra_"+k).value = 'off';
			}
			if(  document.getElementById("wimp_"+k) != null ){
				document.getElementById("wimp_"+k).checked = false;
				document.getElementById("wimp_"+k).value = 'off';
			}
		}
	} 
       
     function iniciar_pgm(k)
       {
		if( document.getElementById("wopc_"+k) != null ){
			document.getElementById("wopc_"+k).checked = false;
			document.getElementById("wopc_"+k).value = 'off';
	    }
		if( document.getElementById("wgra_"+k) != null ){
			document.getElementById("wgra_"+k).checked = false;
			document.getElementById("wgra_"+k).value = 'off';
		}
	   }   
</script>

<!-- Programa en PHP -->
<?php

}





include_once("root/magenta.php");
include_once("root/comun.php");

// $wactualiz = "2018-08-17";
$wactualiz = "2022-02-09";

session_start();
if(!isset($_SESSION['user']) and !isset($user))
	echo "error usuario no esta registrado";
else
{

	/*  Modificaciones
		2018-08-17: Arleyda Insignares Ceballos
					Se desactiva el codigo de actualización (UPDATE) en la tabla 'hce_000020' campo 'usualu'. La razón obedece a que los cambios en este campo se deben hacer exclusivamente desde el programa de docencia.
		2017-01-25: Jessica Madrid Mejia
					Se agrega indicador a los programas anexos que se imprimen con los formularios de HCE en HCE_Impresion.php y solimp.php
		2016-06-09: Jessica Madrid Mejia
					Se agrega confirmaciÃ³n del rol consultado y rol a actualizar para evitar errores.
					Se corrige en Programas Anexos a la HCE cuando trae opciones seleccionadas de un rol consultado previamente.
		2016-06-08: Arleyda Insignares C. Se Desactiva la tabla de Alumnos debido a que se crea nuevo script 'Docencia' para esta funcion
		2016-05-25: Arleyda Insignares C. Se modifica el campo Rol cambiando un option select por un input text con autocompletacion
					Se agrega un campo check debajo de lista de alumnos para que active un grabado por especialidad: donde toma la lista
					de alumnos y la graba a todos los profesionales con la misma especialidad
					Que muestre los formularios que pertenecen a un solo servicio.
		2013-08-21: Frederick Aguirre
					Se quita el codigo que limitaba un alumno a un solo profesor, puesto que un alumno puede tener varios profesores.
		2013-04-03: Frederick Aguirre
					Se agrega la opcion "imp" que indica si el usuario tiene permiso de imprimir
					Se modifico la funcion "consultar", dado que no restauraba los valores de las opciones si se realizo una consulta previamente
	*/

	//=================================
	//Declaracion de variables globales
	//=================================
	global $wusuario;
	//global $wemp_pmla;
	global $wbasedato;
	global $wbasemovhos;
	global $wfecha;
	global $whora;

	global $wok;
	global $wformulario;
	global $wnompro;
	global $wtipuso;
	global $wtipfor;
	global $wtipimp;
	global $wjerar;
	global $wurl;
	global $walerta;
	global $wenccol;
	global $wencnfi;
	global $wencnco;
	global $windmen;
	global $westado;
	global $usuario_existe;


	if( isset($wcodusu) ){
		$wcodusu = trim($wcodusu);
	}

	//=================================

	$wbasedato   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wbasemovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	$wfecha=date("Y-m-d");   
	$whora = (string)date("H:i:s");

	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); 

	if( isset($_REQUEST['action'] )){
		$action = $_REQUEST['action'];
		if( $action == 'consultarAlumnos' ){
			$respuesta = consultarSelectDeAlumnos ( $_REQUEST['codigo_alumno'] );
			echo $respuesta;
			return;
		}
		if( $action == 'buscarRegistro' ){
			$respuesta = consultarRoles ($wbasedato,$conex,$wemp_pmla);
			echo json_encode($respuesta);
			return;
		}
	}
	//=================================================================================================================================
	//***************************************** D E F I N I C I O N   D E   F U N C I O N E S *****************************************
	//=================================================================================================================================

	function consultarRoles($wbasedato,$conex,$wemp_pmla){
		
		$strtipvar = array();
		$q   = " SELECT rolcod, roldes FROM ".$wbasedato."_000019 WHERE rolest = 'on' ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		while($row = mysql_fetch_assoc($res))
			{
				$strtipvar[$row['rolcod']] = $row['roldes'];
			}
		return $strtipvar;
	}

	function consultarUsuarios($wbasedato,$conex,$wemp_pmla){
		
		$strtipvar = array();
		$q = " SELECT Codigo, Descripcion
			From usuarios  
			WHERE activo ='A'";

		// Se modificar para que no filtre los usuarios en hce_000020
		/*	$q = " SELECT codigo, descripcion"
			."   From usuarios A, ".$wbasedato."_000020 B "
			."   Where A.codigo = B.usucod "			
			."   And activo ='A'";*/

		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		while($row = mysql_fetch_assoc($res))
			{
				$strtipvar[$row['Codigo']] = utf8_encode($row['Descripcion']);
			}
		return $strtipvar;
	}

	function consultarSelectDeAlumnos( $wcodigo_alumno ){
		global $conex;
		global $wbasedato;
		global $wbasemovhos;     
		global $wemp_pmla;
		
		$wcodigo_alumno = trim( $wcodigo_alumno );
		
		$where = "  WHERE A.codigo like '%".$wcodigo_alumno."%'";
		if( is_numeric( $wcodigo_alumno ) == false ){
			$where = "  WHERE A.descripcion like '%".$wcodigo_alumno."%'";
		}
		
		$q = " SELECT codigo, descripcion as nombre "
				."   FROM usuarios A, ".$wbasedato."_000020 B "
				.$where
				."    AND A.codigo = B.usucod "
				."    AND usures = 'on' "
				."    AND activo ='A'";
				//."   AND empresa = '".$wemp_pmla."'";
			
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);   
	
		if ($num > 0 ){
			while( $row= mysql_fetch_array($res) ){
				echo "<option value='".$row['codigo']."'>".$row['codigo']." - ".$row['nombre']."</option>";
			}
		}else{
			echo "NO";
		}
	}
	//======================================================================================================================================
	function grabar()
	{
	global $conex;	  
	global $wbasedato;
	global $wbasemovhos; 
	global $wusuario;
	global $wfecha;
	global $whora;
	
	global $wok;
	global $wcodusu;
	global $wnomusu;
	global $wclave;
	global $wrol;
	global $wfecven;
	global $wresidente;
	global $wactivo;
	
	global $matriz;
	global $matriz_pgm;
	
	global $wtotmatpgm; 
	global $wnumero_alumnos;
	
	$k = sizeof($matriz);
	
	//Definir variables globales con las opciones del arbol
	for ($i=1; $i<=($k); $i++)
		{
		for ($j=1; $j<=4; $j++)
			{
			if ($matriz[$i][$j][3]!='on')
				{
				$wop='wopc_'.$matriz[$i][$j][1];
				$wgr='wgra_'.$matriz[$i][$j][1];
				$wimp='wimp_'.$matriz[$i][$j][1];
					
				global $$wop;
				global $$wgr;
				global $$wimp;
				} 
			}
		}
		
		$usualu = "";
		//Traer los codigos de los alumnos elegidos
		if( isset($wnumero_alumnos)){
			if( $wnumero_alumnos > 0 ){
				for($i=1;$i<=$wnumero_alumnos;$i++){
					$alu = "walumno".$i;
					global $$alu;
					$usualu.= $$alu."|"; //Creo una cadena con los codigos de los alumnos separado por comas
				}	
			}
		}
		//Para quitarle la ultima coma
		if( strlen($usualu) > 0 ){
			$usualu = substr($usualu, 0, -1);  
		}	
		//2013-08-21 comentado el siguiente codigo
		//Borrar en usualu donde sea alumno, ya que solo se puede ser alumno de un maestro
		/*if (isset($wcodusu) and ($wcodusu != "") and $usualu != ''){
			$usualuarray = explode("|",$usualu);//Arreglo con los alumnos
			$usuarluarrayreplace = array();//Arreglo del mismo tamano que el anterior, en cada posicion hay una cadena vacia
			foreach( $usualuarray as $pos=>$usu){
				$usuarluarrayreplace[ $pos ] = "";
			}
			$q = " SELECT usualu "
				."   FROM ".$wbasedato."_000020 "
				."  WHERE usualu regexp '".$usualu."' "
				."    AND usucod != '".trim($wcodusu)."'";

			$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

			if( $num > 0 ){
				while( $row = mysql_fetch_array($res) ){
					$usualuold = $row[0];
					
					//Cadena sin alumnos
					$usualuold = str_replace($usualuarray, $usuarluarrayreplace, $usualuold);
					$usualuold = str_replace(",,", ",", $usualuold); //Para que reemplaze doble coma
					$aux = substr($usualuold, 0, 1); 
					if( $aux == "," ){ //Si el primer caracter es una coma
						$usualuold = substr($usualuold, 1); 
					}
					$aux = substr($usualuold, -1);  //Si el ultimo caracter es una coma
					if( $aux == "," ){
						$usualuold = substr($usualuold, 0, -1); 
					}
					$q = " UPDATE ".$wbasedato."_000020 "
						."    SET usualu = '".$usualuold."' "
						."  WHERE usualu regexp '".$usualu."' "
						."    AND usucod != '".trim($wcodusu)."'";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				}
			}
		}*/	
		
		$wrol1=explode("-",$wrol);	 //ROL
		$usualu = str_replace("|",",",$usualu);
			
		if (isset($wcodusu) and ($wcodusu != "")) 
		{
			$wtodos= $_POST['alumnosall'];
			
			if ($wtodos == 'on' && $usualu != '')
				{
				// 2016-05-24 Se adiciona consulta de Especialidad para el grabado de lista alumnos
				$q = " SELECT Meddoc, Medesp "
					."   FROM ".$wbasemovhos."_000048 "
					."   WHERE Meduma = '".$wcodusu."' ";

				$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num2 = mysql_num_rows($res2);
				if ($num2 > 0)
					{
						$row2 = mysql_fetch_assoc($res2);
						$wespecialidad = $row2['Medesp'];
					}
					else{
						$wespecialidad ="";
					}  
					
					// Consultar todos los usuarios con esta Especialidad
					// 2016-05-24 Se adiciona consulta por Especialidad para el Update campo usualu
					
					/*if ($wespecialidad != '')
					{
						$q = " SELECT Meduma, Medesp "
						."   FROM ".$wbasemovhos."_000048 "
						."   WHERE Medesp = ".$wespecialidad;
						
						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);

						if ($num > 0)
						{	 
							while($row = mysql_fetch_assoc($res)){			         
									//Actualizo el campo usualu del grupo de Usuarios (misma especialidad)
									$qq=" UPDATE ".$wbasedato."_000020 
										SET usualu = '".$usualu."'
										WHERE usucod = '".$row['Meduma']."'";
									$resp= mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							}
						}
					}*/               
				}
			else{		    
					//Borro el Usuario con la configuracion del usuario
					$q = " DELETE FROM ".$wbasedato."_000020 "
						."  WHERE usucod = '".trim($wcodusu)."'";
					$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());				

					//Inserto las OPCIONES con que queda del ROL
					$q= " INSERT INTO ".$wbasedato."_000020 (   Medico       ,   Fecha_data ,   Hora_data,   usucod     ,   usucla     ,   usurol             ,   usuest     ,    usufve     ,    usures        ,    usugra      , usualu,     Seguridad   ) "
					."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$wcodusu."','".$wclave."' ,'".trim($wrol1[0])."' ,'".$wactivo."', '".$wfecven."', '".$wresidente."', '".$wusuario."', '".$usualu."',  'C-".$wusuario."') ";
					$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			}

			/*if( isset($wnumero_alumnos)){
				if( $wnumero_alumnos > 0 ){
					for($i=1;$i<=$wnumero_alumnos;$i++){
						$alu = "walumno".$i;*/
						
						// Borro el Usuario con la configuracion del usuario 
						// $q = " DELETE FROM ".$wbasedato."_000020 "
							// ."  WHERE usucod = '".trim($$alu)."'";
						// $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						
						/*$q = " SELECT * FROM ".$wbasedato."_000020 "
							."  WHERE usucod = '".trim($$alu)."'";
						$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num11 = mysql_num_rows($res1);
						if($num11 == 0 ){
							//Inserto las OPCIONES con que queda del ROL  
							$q= " INSERT INTO ".$wbasedato."_000020 (   Medico       ,   Fecha_data ,   Hora_data,   usucod     ,   usucla     ,   usurol             ,   usuest     ,    usufve     ,    usures        ,    usugra      ,      Seguridad   ) "
							."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".$$alu."','' ,'".trim($wrol1[0])."' ,'".$wactivo."', '".$wfecven."', 'on', '".$wusuario."', 'C-".$wusuario."') ";
							$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						}else{*/
							$qq=" UPDATE ".$wbasedato."_000020 
								SET usures = 'on'
								WHERE usucod = '".trim($$alu)."'";
							$resp= mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						
						/*}*/
					/*}
				}
			}*/
			?>	    
			<script> alert ("Se ha Actualizado o Creado el Usuario y se relacionó con el ROL seleccionado"); </script>
			<?php
		}
		else
		{
			//echo "ACTUALIZO ARBOL ".$wrol1[0]."<BR>";
		//======================================================================================================================================= 	  
		//=======================================================================================================================================    
		//***** A R B O L *****  
		//=======================================================================================================================================
		//=======================================================================================================================================  
		$q = " SELECT Precod, Predes, Prenod "
			."   FROM ".$wbasedato."_000009 "
			."  WHERE Preest = 'on' "  
			."  ORDER BY 1 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		$k=round($num/4);

		//Borro las OPCIONES del ARBOL que tenia el ROL 
		$q = " DELETE FROM ".$wbasedato."_000021 "
			."  WHERE rarcod = '".trim($wrol1[0])."'";
		$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		//=========================================================== 
		//GRABO LAS OPCIONES DEL ARBOL DEL ROL
		//=========================================================== 
		for ($j=1; $j<=$num; $j++)
			{
			$row = mysql_fetch_array($res); 
			
			if( $row[0] != "" ){
					$wvar_opc="wopc_".$row[0];
					$wvar_gra="wgra_".$row[0];
					$wvar_imp='wimp_'.$row[0];
					
					if ( isset($$wvar_opc) ||  isset($$wvar_gra) ||  isset($$wvar_imp) )
					{
						if ( isset($$wvar_opc) == false )
							$$wvar_opc = 'off';
						if ( isset($$wvar_gra) == false )
							$$wvar_gra = 'off';
						if ( isset($$wvar_imp) == false )
							$$wvar_imp = 'off';
					}

					if ( isset($$wvar_opc) ||  isset($$wvar_gra) ||  isset($$wvar_imp))
					{
						if ( $$wvar_opc == 'off' &&  $$wvar_gra == 'off' && $$wvar_imp== 'off' ){
						}else{
							//Inserto las OPCIONES con que queda del ROL  
							$q= " INSERT INTO ".$wbasedato."_000021 (   Medico       ,   Fecha_data ,   Hora_data,   rarcod            ,   rararb     ,      rarcon,            rargra   ,          rarimp     , rarpro, rarest,    rarusu      ,      Seguridad   ) "
							."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".trim($wrol1[0])."','".$row[0]."' ,'".$$wvar_opc."' ,'".$$wvar_gra."' ,'".$$wvar_imp."' , 'off' , 'on'  , '".$wusuario."', 'C-".$wusuario."') ";
							$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
							echo "";
						}
					}
				}
			}
		//======================================================================================================================================= 	  
		// TERMINA GRABACION DEL ARBOL
		//=======================================================================================================================================   
			
		
		//======================================================================================================================================= 	  
		//=======================================================================================================================================    
		//***** O P C I O N E S   D E   P R O G R A M A S *****  
		//=======================================================================================================================================
		//=======================================================================================================================================  
		$q = " SELECT Oprpro, Oprnop, Oprdop, Oprest "
			."   FROM ".$wbasedato."_000024 "
			."  WHERE Oprest = 'on' "  
			."  ORDER BY 1 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		//Definir variables globales con las opciones de los programas
		for ($i=1; $i<=$num; $i++)
			{
			$row = mysql_fetch_array($res);
				
			$wop='wopc_'.$row[0]."_".$row[1];
			$wgr='wgra_'.$row[0]."_".$row[1];
					
			global $$wop;
			global $$wgr;
			global $$wimp;
			}
		
		//Borro los PROGRAMAS que tenia el ROL
		$q = " DELETE FROM ".$wbasedato."_000026 "
			."  WHERE rrprol = '".trim($wrol1[0])."'";
		$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		
		mysql_data_seek ($res,0);
			
		//=========================================================== 
		//Lleno una matriz tal como se debe de mostrar
		//=========================================================== 
		for ($j=1; $j<=$num; $j++)
			{
			$row = mysql_fetch_array($res);
					
			$wvar_opc="wopc_".$row[0]."_".$row[1];
			$wvar_gra="wgra_".$row[0]."_".$row[1];

			if (isset($$wvar_opc) and $$wvar_opc == "on")
				{
				//Inserto los PROGRAMAS del ROL con las nuevas condiciones
				$q= " INSERT INTO ".$wbasedato."_000026 (   Medico       ,   Fecha_data ,   Hora_data,   rrprol            ,   rrppro     ,   rrpopc     ,    rrpgra        , rrpest,    rrpusu      ,    rrpnpe    ,    Seguridad   ) "
					."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".trim($wrol1[0])."','".$row[0]."' ,'".$row[1]."' , '".$$wvar_gra."' , 'on'  , '".$wusuario."', '".$row[2]."', 'C-".$wusuario."') ";
				$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				}
			}
		//======================================================================================================================================= 	  
		// TERMINA GRABACION OPCIONES DE PROGRAMAS
		//=======================================================================================================================================
		}    
	}	  

	//========================================================================================================================================
	//Function para imprimir los programas anexos y a su vez definir las variables INPUT de los programas como globales.
	//========================================================================================================================================
	function mostrar_programas()
	{
		global $matriz_pgm;
		global $wtotmatpgm;
		global $usuario_existe;

		$disabled = '';
		if( $usuario_existe == true)
			$disabled = " disabled ";

		$k=$wtotmatpgm;
	
		//Definir variables globales con las opciones de los programas
		for ($i=1; $i<=$k; $i++)
		{
			$wop='wopc_'.$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3];
			$wgr='wgra_'.$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3];

			global $$wop;
			global $$wgr;
		}
		
	//=================================================================================================================
	//**** O P C I O N E S   D E   P R O G R A M A S   A N E X O S   A   L A   H. C. E. ****
	//=================================================================================================================      
	echo "<br>";
	echo "<center><table style='border: 1px solid blue'>";
	echo "<tr class=fila1><td align=center colspan=12><b><font size=5>PROGRAMAS ANEXOS A LA H.C.E.</font></b></td></tr>";


	echo "<tr class=encabezadoTabla>";
	echo "<th>Sel.</th>";
	echo "<th>Graba</th>";
	echo "<th>Opci&oacute;n</th>";
	echo "<th>Sel.</th>";
	echo "<th>Graba</th>";
	echo "<th>Opci&oacute;n</th>";
	echo "<th>Sel.</th>";
	echo "<th>Graba</th>";
	echo "<th>Opci&oacute;n</th>";
	echo "<th>Sel.</th>";
	echo "<th>Graba</th>";
	echo "<th>Opci&oacute;n</th>";
	echo "</tr>";
		
			
	$wprg = ""; 
	$wini_prg = "";	   
		for ($i=1; $i<=$k; $i++)
		{
			
			$tituloReporte = "";
			$etiquetaImpresionHCE = "";
			$deshabilitarPorImpHCE = "";
			if($matriz_pgm[$i][4]!=="")
			{
				$tituloReporte = "<span style='font-size:10pt'>(Reporte)</span>";
				$etiquetaImpresionHCE = "<span style='background-color: #BCFFC2;border-radius: 5px; font-size: 8pt;vertical-align: middle;'>&nbsp; Impresion HCE &nbsp;</span>";
				$deshabilitarPorImpHCE = " disabled ";
			}
			
			
			if ($matriz_pgm[$i][1] != $wprg)  //Es un nuevo programa
			{
				echo "<tr>";   
				echo "<td colspan=12 bgcolor='dddddd'><b>".$matriz_pgm[$i][1]."</b>&nbsp;".$tituloReporte."</td>";

				$wprg=$matriz_pgm[$i][1];
				$wini_prg="on";

				echo "</tr>";
				echo "<tr class=fila1>";
				$l=1;
			}
			else
			$wini_prg="off";    

			$wvar = "wopc_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]; 

			if ($l > 4)
			{
				echo "</tr>";   
				echo "<tr class=fila1>";
				$l=1;
			}
			
			$colspanProAnex = "colspan='1'";
			if($matriz_pgm[$i][1]!=$matriz_pgm[$i+1][1])
			{
				$cantColumnas = 12-($l*3);
				$colspanProAnex = "colspan='".$cantColumnas."'";
			}
			
			if (isset($$wvar))        //Si esta seleccionada la opcion
			{
				echo "<td><input type='checkbox' ".$deshabilitarPorImpHCE." $disabled name='wopc_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."' id='wopc_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."' CHECKED></td>";

				$wvar = "wgra_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3];
				if ($$wvar=="on")     //Si esta seleccionada la opcion
				{
					echo "<td><input type='checkbox' ".$deshabilitarPorImpHCE." $disabled name='wgra_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."' id='wgra_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."' CHECKED></td>";
				} 
				else
				{
					echo "<td><input type='checkbox' ".$deshabilitarPorImpHCE." $disabled name='wgra_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."' id='wgra_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."'></td>";  
				}
				echo "<td>".$matriz_pgm[$i][2]." ".$etiquetaImpresionHCE."</td>";
			}
			else
			{
				echo "<td><input type='checkbox' ".$deshabilitarPorImpHCE." $disabled name='wopc_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."' id='wopc_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."'></td>";
				echo "<td><input type='checkbox' ".$deshabilitarPorImpHCE." $disabled name='wgra_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."' id='wgra_".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."'></td>";
				echo "<td>".$matriz_pgm[$i][2]." ".$etiquetaImpresionHCE."</td>";
			}
			
			if($colspanProAnex != "colspan='1'")
			{
				echo "<td ".$colspanProAnex.">&nbsp;</td>";
			}
			
			$l++;    
		} 
	}
	
	
	//========================================================================================================================================
	//Function para imprimir el arbol y a su vez definir las variables INPUT del arbol como globales.
	//========================================================================================================================================
	function mostrar_arbol()
	{
	global $matriz;
	global $wini;
	global $usuario_existe;
	
	$disabled = '';
	if( $usuario_existe == true)
		$disabled = " disabled ";
	
	$k = sizeof($matriz);
	
	//Definir variables globales con las opciones del arbol
	for ($i=1; $i<=($k); $i++)                      //Filas 
		{
		for ($j=1; $j<=4; $j++)
			{
			if ($matriz[$i][$j][3]!='on')           //Columnas
				{
				$wop='wopc_'.$matriz[$i][$j][1];
				$wgr='wgra_'.$matriz[$i][$j][1];
				$wimp='wimp_'.$matriz[$i][$j][1];
					
				global $$wop;
				global $$wgr;
				global $$wimp;
				} 
			}
		}
		
	//=================================================================================================================
	//**** O P C I O N E S   D E L   A R B O L   D E   P R E SE N T A C I O N ****
	//=================================================================================================================      
	echo "<br>";
	echo "<center><table style='border: 1px solid blue'>";
	echo "<tr class=fila1><td align=center colspan=12><b><font size=5>ARBOL DE FORMULARIOS HCE</font></b></td></tr>";

	echo "<tr class=encabezadoTabla>";
	echo "<th>Sel.</th>";
	echo "<th>Graba</th>";
	echo "<th>Imp.</th>";
	echo "<th>Opci&oacute;n</th>";
	echo "<th>Sel.</th>";
	echo "<th>Graba</th>";
	echo "<th>Imp.</th>";
	echo "<th>Opci&oacute;n</th>";
	echo "<th>Sel.</th>";
	echo "<th>Graba</th>";
	echo "<th>Imp.</th>";
	echo "<th>Opci&oacute;n</th>";
	echo "<th>Sel.</th>";
	echo "<th>Graba</th>";
	echo "<th>Imp.</th>";
	echo "<th>Opci&oacute;n</th>";
	echo "</tr>";

		if ($wini=="on")
		{
			$wcolor="";
			$wini="off";
		} 
		else 
			$wcolor="FFFF99";
			
		/*
		echo "<td bgcolor='dddddd'>A</td>";
		echo "<td bgcolor='dddddd'>A</td>";
		echo "<td bgcolor='dddddd'>A</td>";
		echo "<td bgcolor='dddddd'>&nbsp;</td>";
		*/
	$fila_color = false;
		for ($i=1; $i<=($k); $i++)
		{
			echo "<tr class=fila1>";   
			for ($j=1; $j<=4; $j++)
			{
				if ($matriz[$i][$j][3]=='on')  //Si es un nodo
				{
					//Sel.	Graba	Imp.
					$filaa = "";
					if( strlen( $matriz[$i][$j][1] ) > 1 ){
						$filaa = "<br><table width='100%'>";
						$filaa.="<tr>";
						$filaa.="<td class='encabezadoTabla' width=24><input type='checkbox' id='wopc_".$matriz[$i][$j][1]."' onclick='checkearColumna(this, \"wopc\", \"".$matriz[$i][$j][1]."\")' /></td>";
						$filaa.="<td class='encabezadoTabla'width=43><input type='checkbox' id='wgra_".$matriz[$i][$j][1]."' onclick='checkearColumna(this, \"wgra\", \"".$matriz[$i][$j][1]."\")' /></td>";
						$filaa.="<td class='encabezadoTabla'width=34><input type='checkbox' id='wimp_".$matriz[$i][$j][1]."' onclick='checkearColumna(this, \"wimp\", \"".$matriz[$i][$j][1]."\")' /></td>";
						$filaa.="<td>&nbsp;</td>";
						$filaa.="</tr></table>";
					}				
					echo "<td colspan=4 bgcolor='dddddd'><b>".$matriz[$i][$j][2]."</b>".$filaa."</td>";	
				}
				else 
				{
					$wvar = "wopc_".$matriz[$i][$j][1]; 
					if (isset($$wvar) and $$wvar=="on")        //Si esta seleccionada la opcion
					{
						echo "<td bgcolor='".$wcolor."'><input type='checkbox' $disabled onclick='verificaSel(this)' name='wopc_".$matriz[$i][$j][1]."' id='wopc_".$matriz[$i][$j][1]."' CHECKED></td>";
					}else{
						echo "<td><input type='checkbox' $disabled onclick='verificaSel(this)' name='wopc_".$matriz[$i][$j][1]."' id='wopc_".$matriz[$i][$j][1]."'></td>";
					}

					$wvar = "wgra_".$matriz[$i][$j][1];
					if ($$wvar=="on")     //Si esta seleccionada la opcion
					{
						echo "<td bgcolor='".$wcolor."'><input type='checkbox' $disabled onclick='verificaGra(this)'name='wgra_".$matriz[$i][$j][1]."' id='wgra_".$matriz[$i][$j][1]."' CHECKED></td>";
					}
					else
					{
						echo "<td><input type='checkbox' $disabled onclick='verificaGra(this)' name='wgra_".$matriz[$i][$j][1]."' id='wgra_".$matriz[$i][$j][1]."'></td>";  
					}

					$wvarimp = "wimp_".$matriz[$i][$j][1]; 
					if(isset($$wvarimp) and $$wvarimp=="on"){
						echo "<td bgcolor='".$wcolor."'><input type='checkbox' $disabled onclick='verificaImp(this)' name='wimp_".$matriz[$i][$j][1]."' id='wimp_".$matriz[$i][$j][1]."'  CHECKED></td>";
					}else{
						echo "<td><input type='checkbox' $disabled onclick='verificaImp(this)' name='wimp_".$matriz[$i][$j][1]."' id='wimp_".$matriz[$i][$j][1]."'></td>";
					}

					echo "<td>".$matriz[$i][$j][2]."</td>";
				}
			}
			echo "</tr>";       
		}
		echo "</table>";      	  	  
	}
	

	function vaciar_matriz()
	{
	//Vaceo la matriz cuando se presiona el boton "Iniciar", es decir coloco todas las opciones del arbol en 'off' 
	//esto porque despues de esta función sigue la función 'imprimir o mostrar el arbol'.	  
	global $matriz;
	
		
	$k = sizeof($matriz);	  
		
	for ($j=1; $j<=4; $j++)
		{
			for ($i=1; $i<=$k; $i++)
			{
				$matriz[$i][$j][1]="off";
			}
		}   
	}	      
	

	//======================================================================================================================================= 
	function llenar_matriz_de_programas()
	{
		global $conex;	  
		global $wbasedato; 
		global $wbasemovhos;         
		global $matriz_pgm;
		global $k;
		global $wtotmatpgm;
		
		
		//Query para traer los programas y sus opciones
		//          Programa, Desc. Opcion, Nro Opcion
		$q = " SELECT pronom, oprdop      , oprnop,Oprscr,Oprqva  "
			."   FROM ".$wbasedato."_000023, ".$wbasedato."_000024 "
			."  WHERE proest = 'on' "
			."    AND pronom = oprpro "
			."    AND oprest = 'on' "
			."  ORDER BY 1 "; 
			
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		
		$k=$num;
		$wtotmatpgm=$num;
		
		//=================================================================================================================================== 
		//Lleno la matriz
		///==================================================================================================================================
		for ($i=1; $i<=$num; $i++)
		{
			$row = mysql_fetch_array($res);
				
			$matriz_pgm[$i][1]=$row[0];
			$matriz_pgm[$i][2]=$row[1];
			$matriz_pgm[$i][3]=$row[2];
			$matriz_pgm[$i][4]=$row[3];
			$matriz_pgm[$i][5]=$row[4];
		}  
	}
	//======================================================================================================================================= 

	
	
	//======================================================================================================================================= 
	function llenar_matriz_con_arbol()
	{
		global $conex;	  
		global $wbasedato; 
		global $wbasemovhos;         
		global $matriz;
		global $k;
		
		$q = " SELECT Precod, Predes, Prenod "
			."   FROM ".$wbasedato."_000009 "
			."  WHERE Preest = 'on' "  
			."  ORDER BY 1 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		$k=round(($num/4),0);
		
		//======================================================================================================================================= 
		//Lleno una matriz tal como se debe de mostrar, es decir se debe mostrar por columnas no por filas
		///======================================================================================================================================= 
		for ($j=1; $j<=4; $j++)
		{
			for ($i=1; $i<=$k+2; $i++)
			{
				$row = mysql_fetch_array($res);
				
				$matriz[$i][$j][1]=$row[0];
				$matriz[$i][$j][2]=$row[1];
				$matriz[$i][$j][3]=$row[2];
			}
		}
	}
	//======================================================================================================================================= 
	

	//=======================================================================================================================================    
	function consultar_detalle_programas($wrol)
	{
	global $conex;	  
	global $wbasedato;
	global $wbasemovhos;        
	global $wusuario;
	global $wfecha;
	global $whora;
	global $matriz_pgm;
	global $wrol;
	global $wtotmatpgm;
	
	
	llenar_matriz_de_programas();
	
	$k=$wtotmatpgm;
	
	//Definir variables globales con las opciones del arbol
	for ($i=1; $i<=$k; $i++)
		{
		$wop='wopc_'.$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3];
		$wgr='wgra_'.$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3];
				
		global $$wop;
		global $$wgr;
		}
		
	$wrol1=explode("-", $wrol);	   
		
		if( $wrol == '' )
			return;
	//Consulto las opciones del programa habilitadas para el ROL
	$q = " SELECT  Rrprol, Rrppro, Rrpopc, Rrpgra, Rrpest "
		."   FROM ".$wbasedato."_000026 "
		."  WHERE rrprol = '".trim($wrol1[0])."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	if ($num > 0)
		{
		for ($i=1;$i<=$num;$i++)
			{
			$row = mysql_fetch_array($res);   
				
			$wvar="wopc_".$row[1]."_".$row[2];
			$$wvar="on";
			
			$wvar="wgra_".$row[1]."_".$row[2];
			$$wvar=$row[3];
			
			}     
		}
		else
		{
			//Si no tiene registros en hce_000026 limpia las variables
			for ($i=1; $i<=$k; $i++)
			{
				$wvar='wopc_'.$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3];
				$$wvar=null;
				$wvar='wgra_'.$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3];
				$$wvar=null;
			}
		}
		
	}
	//=======================================================================================================================================

	
	//=======================================================================================================================================    
	function consultar_detalle_arbol($wrol)
	{
	global $conex;	  
	global $wbasedato;
	global $wbasemovhos;        
	global $wusuario;
	global $wfecha;
	global $whora;
	global $matriz;
	global $wrol;
	
	
	llenar_matriz_con_arbol();
	
	$k = sizeof($matriz);
		
	//Definir variables globales con las opciones del arbol
	for ($i=1; $i<=($k); $i++)
		{
		for ($j=1; $j<=4; $j++)
			{
			if ($matriz[$i][$j][3]!='on')
				{
				$wop='wopc_'.$matriz[$i][$j][1];
				$wgr='wgra_'.$matriz[$i][$j][1];
				$wimp='wimp_'.$matriz[$i][$j][1];
					
				global $$wop;
				global $$wgr;
				global $$wimp;
				
				$$wop = "";
				$$wgr = "";
				$$wimp = "";
				
				} 
			}
		}
		
	$wrol1=explode("-", $wrol);	   
		
		if( $wrol == "" )
			return;
	//Consulto las opciones del arbol habilitadas para el ROL
	$q = " SELECT  Rarcod, Rararb, Rarcon, Rargra, Rarimp, Rarpro, Rarest  "
		."   FROM ".$wbasedato."_000021 "
		."  WHERE rarcod = '".trim($wrol1[0])."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
		{
		for ($i=1;$i<=$num;$i++)
			{
			$row = mysql_fetch_assoc($res);   
				
			$wvar="wopc_".$row['Rararb'];
			$$wvar=$row['Rarcon'];
			
			$wvar="wgra_".$row['Rararb'];
			$$wvar=$row['Rargra'];
			
			$wvar="wimp_".$row['Rararb'];
			$$wvar=$row['Rarimp'];
			}     
		}
	}
	//=======================================================================================================================================  
	


	//=======================================================================================================================================
	function consultar()
	{
	global $conex;
	global $wbasedato;
	global $wbasemovhos;    
	global $wusuario;
	global $wfecha;
	global $whora;
	
	global $wcodusu;
	global $wnomusu;
	global $wclave;
	global $wrol;
	global $wfecven;
	global $wresidente;
	global $wactivo;
	global $wemp_pmla;
	global $usuario_existe;
	
	if (!isset($wcodusu))
		$wcodusu="%";
		
	if (isset($wrol))
		$wrol=trim($wrol);
		
	if (!isset($wrol) or trim($wrol)=="-" or trim($wrol)=="" or trim($wrol)==" " or is_null($wrol) or strlen($wrol)<=1 )
		$wrol="%";   
		
		$usuario_existe = false;
	
	//Aca busco el indice en el menu
	
		$q = " SELECT Usucod, Descripcion, Usucla, Usurol, Usufve, Usures, Usuest "
		."   FROM usuarios A"
		."   Left join ".$wbasedato."_000020 B"
		."   on A.codigo = B.usucod "
		."  WHERE codigo = '".$wcodusu."'";
	
		// Retirar validacion con la tabla hce_000020
	/*   $q = " SELECT Usucod, Descripcion, Usucla, Usurol, Usufve, Usures, Usuest "
		."   FROM ".$wbasedato."_000020, usuarios "
		."  WHERE usucod = '".$wcodusu."'"
		."    AND usucod = codigo ";*/
		// ."    AND empresa = '".$wemp_pmla."'";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
		{
		$row = mysql_fetch_array($res);  
		/*
		echo "<script language='Javascript'>";
			echo "document.seguridad.wcodusu.value    = '".$row[0]."'; ";
			echo "document.seguridad.wnomusu.value    = '".$row[1]."'; ";
			echo "document.seguridad.wclave.value     = '".$row[2]."'; ";
			echo "document.seguridad.wrol.value       = '".$row[3]."'; ";
			echo "document.seguridad.wfecven.value    = '".$row[4]."'; ";
			echo "document.seguridad.wresidente.value = '".$row[5]."'; ";
			echo "document.seguridad.wactivo.value    = '".$row[6]."'; ";
		echo "</script>";
		*/     
			$wnomusu=$row[1];
			$wclave=$row[2];
			$wrol=$row[3];
			$wfecven=$row[4];
			$wresidente=$row[5];
			$wactivo=$row[6];
			$usuario_existe = true;
		}
		else
			{
			$wnomusu="";
			$wclave="";
			//On $wrol="";
			$wfecven=$wfecha;
			$wresidente="";
			$wactivo="";   
			$usuario_existe = false;
			}
		consultar_detalle_arbol($wrol); 
		consultar_detalle_programas($wrol);    
	} 
	
	
	
	function iniciar($wtipo)
	{
	global $conex;	  
	global $wbasedato; 
	global $wbasemovhos; 

	global $wusuario;
	global $wfecha;
	global $whora;
	
	global $wcodusu;
	global $wnomusu;
	global $wclave;
	global $wrol;
	global $wfecven;
	global $wresidente;
	global $wactivo;
	
	global $matriz;
	global $matriz_pgm;
	
	switch ($wtipo)
		{  
		case "Encabezado":
			$wcodusu="";
			$wnomusu="";
			$wclave="";
			$wrol="";
			$wfecven="";
			$wresidente="";
			$wactivo="";
			break;
		
		case "Arbol":
			$k = sizeof($matriz);
			for ($i=1; $i<=($k); $i++)
				{
				for ($j=1; $j<=4; $j++)
					{
						echo "<script language='Javascript'>";   
						echo "iniciar("."\"".$matriz[$i][$j][1]."\"".")";
						echo "</script>";     
					}
				}
			break;    
		
		case "Programas":
			$k = sizeof($matriz_pgm);
			
			for ($i=1; $i<=($k); $i++)
				{
				echo "<script language='Javascript'>";
				echo "iniciar_pgm("."\"".$matriz_pgm[$i][1]."_".$matriz_pgm[$i][3]."\"".")";
				echo "</script>";     
				}
			break;	   
		}
	}  
	
	
	function mostrar_encabezado()
	{	global $conex;	  
		global $wbasedato; 
		global $wbasemovhos; 
		global $wusuario;
		global $wfecha;
		global $whora;
		global $usuario_existe;
	
		global $wcodusu;
		global $wnomusu;
		global $wclave;
		global $wrol;
		global $wfecven;
		global $wresidente;
		global $wactivo;  
		global $wemp_pmla;
		
		echo "<CENTER><table style='border: 1px solid blue'>";
		
		//USUARIO Y CLAVE
		echo "<tr class=fila1>"; 
		echo "<td><b>C&oacute;digo Usuario: </b></td>";
		if (isset($wcodusu))
		echo "<td><input type='text' id='wcodusu' name='wcodusu' value='".$wcodusu."' size=8></td>";
		else
			echo "<td><input type='text' id='wcodusu' name='wcodusu' size=8></td>"; 

		if (isset($wnomusu))
		echo "<td colspan=3><input type='text' name='wnomusu' value='".$wnomusu."' readonly size=60></td>"; 
		else
			echo "<td colspan=3><input type='text' name='wnomusu' readonly size=60></td>";  

		//CLAVE ===================
		if (isset($wclave))
		echo "<td><b>Clave: </b><input id='wclave' type='password' size='8' name='wclave' value='".$wclave."' OnBlur='clave()'></td>";
		else 
			echo "<td><b>Clave: </b><input id='wclave' type='password' size='8' name='wclave' OnBlur='clave()'></td>";


		echo "<tr class=fila1>";
		//ROL  ====================
		$q = " SELECT rolcod, roldes "
			."   FROM ".$wbasedato."_000019 "
			."  WHERE rolest = 'on' ";
		
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res); 

		// Se cambia Campo Option a input para ser usado mediante autocompletar	


	/*	echo "<td colspan=3><b>ROL: </b><SELECT name='wrol'>";*/
		if (isset($wrol) and trim($wrol)<>"" and trim($wrol)<>"-" and trim($wrol)<>" " and strlen(trim($wrol)) > 0)   
		{
			$wrol1 = explode("-",$wrol);
			
			$q = " SELECT rolcod, roldes "
				."   FROM ".$wbasedato."_000019 "
				."   WHERE rolcod = '".$wrol1[0]."'"
				."   AND rolest = 'on' "
				."   ORDER BY 1 ";
			$res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1= mysql_fetch_array($res1);
			
			$cam1 = $row1[0];
			$cam2 = $row1[1];      
			//echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]." </option>";             
			echo "<td colspan=3><b>ROL:</b> <input name='wrol' id='wrol' type='text' size='50'>  </td>";
			echo "<script>";
			echo "completarwrol("."\"".$row1[0]."-".$row1[1]."\"".")";
			echo "</script>";
		}
		else
			{
			echo "<td colspan=3><b>ROL:</b> <input name='wrol' id='wrol' type='text' size='50'>  </td>";
			//echo "<OPTION SELECTED>&nbsp</option>";   
			}
				
		/*for ($j=1;$j<=$num;$j++)
		{ 
			$row = mysql_fetch_array($res);   
			echo "<OPTION>".$row[0]."-".$row[1]." </option>";
		}
		echo "</SELECT></td>";*/

		//RESIDENTE ===================
		echo "<td><b>Residente: </b>";
		if (isset($wresidente) and $wresidente=="on")
		echo "<input type='checkbox' name='wresidente' value='".$wresidente."' CHECKED>";
		else
			echo "<input type='checkbox' name='wresidente'>";
		echo "</td>";

		if (!isset($wfecven))
		$wfecven=$wfecha;
		
		echo "<td colspan=1><b>Fecha de<br>Vencimiento: </b>";
		campoFechaDefecto("wfecven",$wfecven);
		echo "</td>";
			
		//ACTIVO
		echo "<td><b>Activo: </b>";
		if (isset($wactivo) and $wactivo=="on")
		echo "<input type='checkbox' size='10' name='wactivo' CHECKED></td>";
		else
			echo "<input type='checkbox' size='10' name='wactivo'>"; 
		echo "</td>";    
		echo "</tr>";
		if( isset($wcodusu) and $wcodusu != "" && $usuario_existe==true){
			// 2016-06-08 Se Desactiva la tabla de Alumnos debido a que se crea nuevo script 'Docencia' para esta función
			/* if( $wresidente!="on" ){
				echo "<tr class=encabezadotabla>";
				echo "<td colspan=6 align='center'>";
				echo "Agregar Alumnos";
				echo "</td>";
				echo "</tr>";
				echo "<tr class=fila1>";
				echo "<td colspan=6 align='center'>";
				echo "<span>Buscar por código o nombre:</span><input type='text' id='buscar_codigo_alumno' />";
				echo "&nbsp; &nbsp;";
				echo "<input type='button' value='Consultar Residentes' id='btn_consultar_alumnos' onclick='consultar_alumnos()' />";
				echo "<br>";
				echo "<select id='select_alumnos' onchange='agregar_alumnos()'> ";
				echo "<option value=''>Seleccione</option>";
				echo "</select>";
				echo "<br><br>";
				echo "</td>";
				echo "</tr>";
				echo "<tr class=encabezadotabla>";
				echo "<td colspan=6 align='center'>Lista de Alumnos";
				echo "</td>";
				echo "</tr>";
				echo "<tr class=fila1>";
				echo "<td colspan=6 align='center' bgcolor='FFFFCC'>Asociar todos los alumnos a todos los Medicos de la Especialidad<input type='checkbox' id='alumnosall' name='alumnosall' style='display: inline;'>";
				echo "</td>";
				echo "</tr>";
				echo "<tr class=fila1>";
				echo "<td colspan=6 align='center'>";
				echo "<div id='lista_alumnos_agregados' style='background-color:#dddddd'>";
				if( !isset($wnumero_alumnos))
					$wnumero_alumnos = 0;

					$q = " SELECT usualu as alumnos "
						."   FROM ".$wbasedato."_000020 "
						."  WHERE usucod= '".$wcodusu."'";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res); 
					if( $num > 0 ){
						$row = mysql_fetch_assoc($res);   
						$walumos_row = explode(",", $row['alumnos']);

						for($i=0;$i<count($walumos_row);$i++){
							$qq = " SELECT descripcion as nombre "
								."   FROM usuarios "
								."  WHERE codigo= '".$walumos_row[$i]."' ";
								//."    AND empresa = '".$wemp_pmla."'";
							$resq = mysql_query($qq,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$numq = mysql_num_rows($resq); 
							if( $numq > 0 ){
								$rowq = mysql_fetch_assoc($resq);  
								echo "<div>";
								echo "<input type='checkbox' checked value='".$walumos_row[$i]."' name='walumno".($i+1)."' onclick='quitarAlumno(this)' class='alumno' />";
								echo "<span>".$walumos_row[$i]." - ".$rowq['nombre']."</span><br>";
								echo "</div>";
								$wnumero_alumnos++;
							}
						}
					}
					echo "<input type='hidden' name='wnumero_alumnos' id='wnumero_alumnos' value='".$wnumero_alumnos."' />";
				echo "</div>";
				echo "</td>";
				echo "</tr>";
			}else{
					$q = " SELECT usucod, descripcion as nombre"
					."   FROM ".$wbasedato."_000020, usuarios"
					."  WHERE usualu regexp '".$wcodusu."' "
					."    AND codigo = usucod "
					//."    AND empresa = '".$wemp_pmla."'"
					."  LIMIT 1";
			
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res); 
					if( $num > 0 ){
						$row = mysql_fetch_assoc($res);   
						echo "<tr class=fila1>";
						echo "<td colspan=6 align='center'>";
						echo "<span><b>Alumno de ".$row['usucod']." - ".$row['nombre']."</b></span>";
						echo "</td>";
						echo "</tr>";
					}			
			} */
		}
		echo "</table>";
		mostrar_botones($wrol,$usuario_existe);
	}	  

	function mostrar_botones($wrol,$usuario_existe)
	{
		echo "<br><br>";
		echo "<center><table>";
		echo "<input type='submit' name='Consultar' value='Consultar'>";
		echo "&nbsp;&nbsp;|&nbsp;&nbsp";
		$disabled = "";
		if( ($wrol == "" || $wrol == '%') && $usuario_existe == false)
			$disabled = " disabled ";
		
		$rolConsultado = $wrol;

		echo "<input type='hidden' id='Actualizar' value=''>";
		echo "<input type='button' id='botonActualizar' name='botonActualizar' value='Actualizar' $disabled onclick='confirmarRol(\"".$rolConsultado."\");' >";
		// echo "<input type='submit' name='Actualizar' value='Actualizar' $disabled >";
		
		echo "&nbsp&nbsp&nbsp;|&nbsp"; 
		echo "<input type='submit' name='Iniciar' value='Iniciar'>";
		echo "&nbsp&nbsp;|&nbsp"; 
		echo "<input type='submit' name='Salir' value='Salir' onclick='cerrarVentana()'>";
		echo "</table>";	 
	}

	//=================================================================================================================================
	//***************************** T E R M I N A   L A   D E F I N I C I O N   D E   F U N C I O N E S *******************************
	//=================================================================================================================================
	/*
		if( $wnumero_alumnos > 0 ){
			for($i=1;$i<=$wnumero_alumnos;$i++){
				$alu = "walumno".$i;
				echo $$alu."<br>";
			}	
		}
		
		*/

	echo "<form name='seguridad' action='Seguridad.php' method='post'>";

	echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";


	//*******************************************************************************************************************************
	//*********   A C A   C O M I E N Z A   E L   B L O Q U E  **** <<< P R I C I P A L >>> ****  D E L   P R O G R A M A   *********
	//*******************************************************************************************************************************
	//=================================================================================================================
	//E N C A B E Z A D O   U S U A R I O - C L A V E - R O L
	//=================================================================================================================      
	$wtitulo="ADMINISTRACION DE SEGURIDAD Y ACCESO HCE";

	encabezado($wtitulo, $wactualiz, 'clinica');

	global $wini;

	$wini="off";

	//Se evalua el boton presionado
	if (isset($Actualizar) or isset($Consultar) or isset($Iniciar))
	{
		if (isset($Actualizar))
		{
			//validar_campos();
			
			if (isset($wusuario) and $wusuario != "")
			{
				llenar_matriz_con_arbol();
				llenar_matriz_de_programas();
				Grabar();
				consultar();
				
				mostrar_encabezado();
				mostrar_arbol();
				mostrar_programas();
				
				
				
				if ($wok)
				{
					?>	    
					<script> alert ("El Registro fue Actualizado"); </script>
					<?php
				}
			}
			else
				{
				?>	    
					<script> alert ("Debe recargar la pagina o volver a ingresar, porque no se detecto actividad en los últimos 5 minutos"); </script>
					<?php   
				}         
		}	
			
		if (isset($Consultar))
		{ 
			consultar();
			mostrar_encabezado(); 
			mostrar_arbol();
			mostrar_programas();
		}    
		
		
		if (isset($Iniciar))
		{ 
			$wini="on";  
			iniciar("Encabezado");  
			vaciar_matriz();
			llenar_matriz_con_arbol();
			mostrar_encabezado();
			mostrar_arbol();
			iniciar("Arbol");
			llenar_matriz_de_programas();
			mostrar_programas(); 
			iniciar("Programas"); 
		}
		} //fin del if (Grabar or Modificar or Consultar or Borrar)
	else
		{ 
		vaciar_matriz();
		llenar_matriz_con_arbol();
		mostrar_encabezado();
		iniciar("Encabezado");
		mostrar_arbol();
		iniciar("Arbol");
		llenar_matriz_de_programas();
		mostrar_programas();
		iniciar("Programas");
		}
		

	echo "</table>";  
	mostrar_botones($wrol,$usuario_existe);
	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/ajax-loader5.gif'/>";
	echo "<br><br> Por favor espere un momento ... <br><br>";
	echo '</div>';
	$arr_wrol = consultarRoles ($wbasedato,$conex,$wemp_pmla);
	$arr_usu  = consultarUsuarios ($wbasedato,$conex,$wemp_pmla);
	//=================================================================================================================
	?>
	<input type="HIDDEN" name="arr_wrol" id="arr_wrol" value='<?=json_encode($arr_wrol)?>'>
	<input type="HIDDEN" name="arr_usu" id="arr_usu" value='<?=json_encode($arr_usu)?>'>
	<div id="dialog-confirm" title="Rol Modificado">
	<p><span class="ui-icon ui-icon-alert" style="margin-top:2; left: 5;"></span>El rol consultado es diferente al rol a actualizar, Desea continuar?</p>
	</div>
	</form>
	</body>
	</html>

<?php
}
?>