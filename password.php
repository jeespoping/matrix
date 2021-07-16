<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>MATRIX Ver. 2020-07-03</title>
	<script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
	<link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script type="text/javascript">
	
	var hexcase=0;var b64pad="";
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
	
	function encriptar(variable)
	{
		variable.value = hex_sha1(variable.value);
	}
	$(document).on('ready', function(){
		$('#mostrar').on('click', function(e){
			e.preventDefault();
			var current = $(this).attr('action');
			if (current == 'hide'){
					$(this).prev().attr('type','text');
					$(this).removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close').attr('action','show');
			}
			if (current == 'show'){
					$(this).prev().attr('type','password');
					$(this).removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open').attr('action','hide');
			}
		});
		
		$('#mostrar2').on('click', function(e){
			e.preventDefault();
			var current2 = $(this).attr('action');
			if (current2 == 'hide'){
				$(this).prev().attr('type','text');
				$(this).removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close').attr('action','show');
			}
			if (current2 == 'show'){
				$(this).prev().attr('type','password');
				$(this).removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open').attr('action','hide');
			}
		});
	});
	</script>
	
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
//****************************************MODIFICACIONES:****************************************
//POR: 									DIDIER OROZCO CARMONA.
//FECHA DE MODIFICACION:				2020/07/01
//1. SE AMPLIA EL TAMAÑO Y RECIBIR MAS CARACTERES HASTA MAXIMO 8 CARACTERES, EL INPUT PARA EL CAMBIO DE LA CONTRASEÑA DE LOS DOS CAMPOS.
//2. SE AGREGA ICONO Y SE AGREGA FUNCION JQUERY PARA MOSTRAR LO QUE ESTAN DIGITANDO LOS USUARIOS EN LOS CAMPOS DE CONTRASEÑA.
//3. SE AGREGAN LIBRERIAS JQUERY, JAVASCRIPT, BOOSTRAP EN EL ARCHIVO PARA EL MANEJO DE ESTOS.
//4. SE AGREGA VALIDACION DEL PASSWORD DIGITADO, QUE CUMPLA CON UN MINIMO DE 8 Y MAXIMO 12 CARACTERES
//5. SE AGREGA VALIDACION DEL PASSWORD DIGITADO, QUE CUMPLA COMO MINIMO 3 DE LAS SIGUIENTES CARACTERISTICAS:
//											- un caracter especial, - una mayuscula, - un numero, una minusculas.
//*************************************************************************************************** */
// POR: JULIAN MEJIA
//FECHA DE MODIFICACION: 				2021/06/24
// 1. SE AMPLIA LA CONTRASEÑA A MINIMO 12 CARACTERES Y CON EL CONDICIONAL DE QUE DEBE CUMPLIR TODAS LAS CARACTERISTICAS
//											- un caracter especial, - una mayuscula, - un numero, una minuscula.
// 2. SE MODIFICA EL TEXTO DE CONDICIONES DEL PASSWORD
// 3. SE CAMBIA EL TIEMPO DE CADUCIDAD DE LA CONTRASEÑA A DOS MESES Y SE DEJA PARAMETRIZABLE
// 4. SE REALIZA EL HISTORIAL DE LAS 5 ULTIMAS PASSWORD CON SU RESPECTIVA VALIDACION

	 
include_once("conex.php");
include_once("root/comun.php");

/************************* FUNCIONES PHP ************************************ */
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}

function findRepNumPass($conex, $codigo, $wpassSha)
{
	$isRepeated = false;
	$num = 0;
	$q = " SELECT Password FROM
					historial_password
				WHERE 
					Codigo = '$codigo'";
	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);
	if ($num > 0){
		while( $info = mysql_fetch_assoc( $res ) ){
 			if ($info['Password'] == $wpassSha) $isRepeated = true;
		}
	}else return array($isRepeated,$num);
	
	return array($isRepeated,$num);
}

function deleteLastPass($conex, $codigo)
{

	$q = " DELETE FROM historial_password
				WHERE 
					Codigo = '$codigo'
					ORDER by Fecha_data, Hora_data 
					LIMIT 1";
	mysql_query($q, $conex);
	// return mysql_affected_rows($conex);
}

/************************* FIN FUNCIONES PHP ************************************ */
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='password.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'wtipo' value='".$wtipo."'>";
		if(!isset($wpass) or !isset($wpassr))
		{
			if(isset($wtipo) and $wtipo == "N")
			{
				echo "<center><table border=0>";
				echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
				echo "<tr><td align=center colspan=2>ADMINISTRACION DE CONTRASE&Ntilde;AS</td></tr>";
				echo "<tr><td align=center colspan=2><p>&nbsp;</p></td></tr>";
				echo "<tr><td align=center colspan=2>POR FAVOR TENER PRESENTE AL ACTUALIZAR LA CLAVE:</td></tr>";
				echo "<tr><td align=center colspan=2>POLITICA DE INFORMATICA.</td></tr>";
				echo "<tr><td align=center colspan=2>1. La clave debe contar con una longitud M&iacute;nima de 12 caracteres.</td></tr>";
				echo "<tr><td align=center colspan=2>2. Que tenga las siguientes condiciones como m&iacute;nimo:";
				echo "<ul>";
				echo "<li>&nbsp;Una May&uacute;scula</li>";
				echo "<li>&nbsp;Una Min&uacute;scula</li>";
				echo "<li>&nbsp;Un Caracter especial</li>";
				echo "<li>&nbsp;Entre 1 y 4 n&uacute;meros</li></ul></td></tr>";
				echo "<tr><td align=center colspan=2>3. La clave no debe ser igual a las 5 &uacute;ltimas claves registradas.</td></tr>";
				//echo "<tr><td align=center colspan=2><b>NOTA:</b> La combinaci&oacute;n debe cumplir con al menos 3 de las caracter&iacute;sticas del paso 2.</td></tr>";
				echo "<tr><td align=center colspan=2><p>&nbsp;</p></td></tr>";
				echo "<tr><td align=center colspan=2>CAMBIO DE PASSWORD</td></tr>";
				echo "<td bgcolor=#cccccc align=center>Password Nuevo</td>";
				echo "<td bgcolor=#cccccc align=center><input type='password' name='wpass' size=25 minlength=12>
						<span class='glyphicon glyphicon-eye-open' action='hide' id='mostrar'></span></td></tr>";
				echo "<td bgcolor=#cccccc align=center>Redigite Password</td>";
				echo "<td bgcolor=#cccccc align=center><input type='password' name='wpassr' size=25 minlength=12>
						<span class='glyphicon glyphicon-eye-open' action='hide' id='mostrar2'></span></td></tr>";		
				echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
			}
			elseif(isset($wtipo) and $wtipo == "P")
				{
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>ADMINISTRACION DE CONTRASE&Ntilde;AS DE PUBLICACION DE SCRIPTS</td></tr>";
					echo "<tr><td align=center colspan=2>CAMBIO O ASIGNACION DE PASSWORD</td></tr>";
					echo "<td bgcolor=#cccccc align=center>Password Nuevo</td>";
					echo "<td bgcolor=#cccccc align=center><input type='password' name='wpass' id='wpass' size=40 maxlength=80 onBlur='javascript:encriptar(this);'></td></tr>";
					echo "<td bgcolor=#cccccc align=center>Redigite Password</td>";
					echo "<td bgcolor=#cccccc align=center><input type='password' name='wpassr' id='wpassr' size=40 maxlength=80 onBlur='javascript:encriptar(this);'></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>Persona Que Publica</td>";
					echo "<td bgcolor=#cccccc>";
					$query = "SELECT subcodigo,descripcion from det_selecciones, root_000060 where det_selecciones.medico='root' and det_selecciones.codigo='17' and Usuario='".$key."' and root_000060.codigo = subcodigo order by subcodigo";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<select name='wpub'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
					echo "</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
				}
				else
				{
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>ADMINISTRACION DE CONTRASE&Ntilde;AS</td></tr>";
					echo "<tr><td align=center colspan=2>CAMBIO O ASIGNACION DE PASSWORD</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center>ERROR EN CONFIGURACION - COMUNIQUESE CON LA DIRECCION DE INFORMATICA</td></tr></table>";
				}
			$codigo=$key;
			echo "<input type='HIDDEN' name= 'codigo' value='".$codigo."'>";
		}
		else
		{  
			if($wtipo == "N")
			{
				$diasExp = consultarAliasPorAplicacion($conex, '*', 'tiempoExpiracionPassword');
				$strDays = ($diasExp == 1) ? 'day' : 'days';
				$query = "select password,feccap from usuarios where codigo='".$codigo."'";
				$err = mysql_query($query,$conex); 
				$row = mysql_fetch_array($err);
				$wfecw=date("Y-m-d");
				$wfecw=date_create($wfecw);
				date_add($wfecw, date_interval_create_from_date_string($diasExp . ' ' . $strDays));	
				$wfecw1=date_format($wfecw, 'Y-m-d');
				
				if($wpass != "" and $wpass==$wpassr and $wpass != $row[0] and strlen($wpass) >= 12 ) //and strlen($wpass) <= 8
				{
					$contador =0;
					//validar minusculas
					if(preg_match('`[a-z]`',$wpass)){
						$contador = $contador + 1;
					}
					//validar mayusculas
					if(preg_match('`[A-Z]`',$wpass)){
						$contador = $contador + 1;
					}
					//validar numeros
					if(preg_match('`[0-9]`',$wpass)){
						$contador = $contador + 1;
					}
					//validar cacteres especiales
					if(preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/',$wpass)){
						$contador = $contador + 1;
					}
					if ($contador >= 4) {

						$cntPassHis = 0;
						$esPassRepetida = false; 
						$wpassSha = sha1( $wpass );
						list($esPassRepetida, $cntPassHis) = findRepNumPass($conex, $codigo, $wpassSha); // funcion que valida si el password esta repetido y el numero de pass
						if (!$esPassRepetida){
							if ($cntPassHis >= 5) deleteLastPass($conex, $codigo); // se debe eliminar el ultimo registro para insertar el nuevo (fn)
							$fechaActual = date( "Y-m-d" );
							$horaActual = date( "H:i:s" );
							$queryHis = "INSERT INTO historial_password 
													(Codigo, Password, Fecha_data, Hora_data) 
											VALUES 
													('{$codigo}', SHA('".$wpass."'),'{$fechaActual}','{$horaActual}')";
							$errHis = mysql_query($queryHis,$conex);

							$query = "UPDATE usuarios 
										SET password=SHA('".$wpass."'), 
											feccap='".$wfecw1."'  
									WHERE codigo='".$codigo."'";
									
							$err = mysql_query($query,$conex);
							if($err !=1)
							{
								echo "<center><table border=0 aling=center>";
								echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
								echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PASSWORD NO SE PUDO CAMBIAR !!!!</MARQUEE></FONT>";
								echo "<br><br>";
							}
							else
							{
								echo "<center><table border=0 aling=center>";
								echo "<tr><td><IMG SRC='/matrix/images/medical/laboratorio/mario.gif' ></td><tr></table></center>";
								echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>EL PASSWORD SE CAMBIO SATISFACTORIAMENTE !!!!</MARQUEE></FONT>";
								echo "<br><br>";
							}
						}
						else
						{

							echo "<center><table border=0 aling=center>";
							echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
							echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffff00 LOOP=-1>LA CLAVE NUEVA NO PUEDE SER IGUAL A LAS ULTIMAS 5 CLAVES !!!!</MARQUEE></FONT>";
							echo "<br><br>";							

						}
					}
					else
					{
						echo "<center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffff00 LOOP=-1>POR FAVOR VERIFICAR REQUISITOS DE LA CLAVE DEL PUNTO 2 !!!!</MARQUEE></FONT>";
						echo "<br><br>";
					}
				}
				else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffff00 LOOP=-1>ERROR EN LA DIGITACION O LONGITUD ES DE (12 CARACTERES) aaaa!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
			}
			else
			{
				$keys=explode("-",$wpub);
				$query = "select Usuario,Codigo from root_000060 where codigo='".$keys[0]."'";
				$err = mysql_query($query,$conex); 
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					if($codigo == $row[0] and $keys[0] == $row[1] and $wpass == $wpassr)
					{
						$query = "update root_000060 set clave='".$wpass."' where Usuario= '".$codigo."' and Codigo='".$keys[0]."'";
						$err = mysql_query($query,$conex);
						if($err !=1)
						{
							echo "<center><table border=0 aling=center>";
							echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
							echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PASSWORD DE PUBLICACION NO SE PUDO CAMBIAR !!!!</MARQUEE></FONT>";
							echo "<br><br>";
						}
						else
						{
							echo "<center><table border=0 aling=center>";
							echo "<tr><td><IMG SRC='/matrix/images/medical/laboratorio/mario.gif' ></td><tr></table></center>";
							echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>EL PASSWORD DE PUBLICACION SE CAMBIO SATISFACTORIAMENTE !!!!</MARQUEE></FONT>";
							echo "<br><br>";
						}
					}
					else
					{
						echo "<center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffff00 LOOP=-1>ERROR EN LA DIGITACION DE LA CLAVE O USUARIOS NO EXISTE EN TABLA DE CODIGOS DE PUBLICACION (60) !!!!</MARQUEE></FONT>";
						echo "<br><br>";
					}
				}
			}
	}
}
?>
</body>
</html>
