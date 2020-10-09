<html>
<head>
  <title>MATRIX</title>
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
	
	function verificar()
	{
		if(confirm("ESTA SEGURO DE REALIZAR LA PUBLICACION. ESTA OPERACION GENERA CAMBIOS EN LOS SCRIPTS???"))
		{
			document.getElementById('tipo').value='1';
		}
		else
		{
			document.getElementById('tipo').value='0';
		}
	}
	</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Publicacion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> publicar.php Ver. 2011-02-23</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");

function validarClave($conex,$usuario,$clave)
{
	$query = "SELECT Usuario FROM root_000060 where codigo = '".$usuario."' and Clave='".$clave."' ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num > 0)
		return true;
	else
		return false;
}

function grabarLog($conex,$usuario,$ruta,$archivo)
{
	$empresa="root";
	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");
	$query = "insert root_000059 (medico,fecha_data,hora_data,Codigo,Ruta,Archivo,Seguridad) values ('";
	$query .=  $empresa."','";
	$query .=  $fecha."','";
	$query .=  $hora."','";
	$query .=  $usuario."','";
	$query .=  $ruta."','";
	$query .=  $archivo."',";
	$query .=  "'C-".$empresa."')";
	$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO LOG DE PUBLICACIONES : ".mysql_errno().":".mysql_error());
}

@session_start();

if(!isset($_SESSION['user']))
	echo "error";
else
{
		if(!isset($key))
			$key = substr($user,2,strlen($user));
		
		


		echo "<form action='publicar.php' enctype='multipart/form-data' method=post>";
		echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
		if(!isset($files))
		{
			echo "<center><input type='HIDDEN' name= 'tipo' id='tipo'>";
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PUBLICACION DE ARCHIVOS EN LA INTRANET Ver. 2012-02-22</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
			echo "<td bgcolor=#cccccc><input type='file' name='files'  size=60 maxlength=60 /></td></tr>";
			
			if($key != "root")
				echo "<tr><td bgcolor=#cccccc align=center colspan=2><INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 1 CHECKED> Archivo Plano Publico<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 2> Archivo Plano Privado<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 3> Imagen";
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Grupos de Trabajo</td>";
				echo "<td bgcolor=#cccccc>";
				$query = "SELECT descripcion from det_selecciones where codigo='grupos' order by descripcion";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wgrp'>";
					echo "<option>Ninguno</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Persona Que Publica</td>";
				echo "<td bgcolor=#cccccc>";
				$query = "SELECT subcodigo,descripcion from det_selecciones where medico='root' and codigo='17' order by subcodigo";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wpub'>";
					echo "<option>Ninguno</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				echo "<td bgcolor=#cccccc align=center>Clave de Publicacion</td>";
				echo "<td bgcolor=#cccccc><input type='password' name='wpass' id='wpass' size=40 maxlength=80 onBlur='javascript:encriptar(this);'></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center  colspan=2><INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 1 CHECKED> Archivo Plano Publico<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 2> Archivo Plano Privado<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 3> Programa General<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 4> Proceso<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 5> Reporte<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 6> Include <INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 7> Imagen</td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center  colspan=2><input type='checkbox' name='expo' id='expo'> IMPORTAR</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'  onClick='javascript:verificar();'></td></tr></table>";
			echo "<input type='HIDDEN' name= 'grupo' value='".$grupo."'>";
		}
		else
		{
			$real= $_FILES['files']['name'];
			$files=$_FILES['files']['tmp_name'];
			if($wpass == "")
				$wpass="1111";
			if($key != "root")
				switch ($radio1)
				{
					case 1:
						$ruta="/var/www/matrix/planos/";
					break;
					case 2:
						$ruta="/var/www/matrix/planos/".$grupo."/";
						$dh=@opendir($ruta);
						if(@readdir($dh) == false)
							mkdir($ruta,0777);
					break;
					case 3:
						$ruta="/var/www/matrix/images/medical/".$grupo."/";
						$dh=@opendir($ruta);
						if(@readdir($dh) == false)
							mkdir($ruta,0777);
					break;
				}
			else
			{
				$especial=0;
				switch ($radio1)
				{
					case 1:
						$ruta="/var/www/matrix/planos/";
					break;
					case 2:
						if($wgrp != "Ninguno")
						{
							$ruta="/var/www/matrix/planos/".$wgrp."/";
							$dh=@opendir($ruta);
							if(@readdir($dh) == false)
								mkdir($ruta,0777);
						}
						else
							echo "NO HA SELECIONADO EL GRUPO<BR>";	
					break;
					case 3:
						if(!isset($expo))
						{
							$sections=explode("-",$wpub);
							//if(validarClave($conex,$sections[0],$wpass))
							//{
								$especial=1;
								$ruta="/var/www/matrix/";
								$rutac="/var/www/matrix/copia/";
								$dhc=@opendir($rutac);
								if(@readdir($dhc) == false)
									mkdir($rutac,0777);
							//}
							//else
							//{
							//	echo "USUARIO O CLAVE ERRONEOS<BR>";
							//}
						}
						else
						{
							$ruta="/var/www/matrix/";
							$rutac="/var/www/matrix/planos/";
						}
					break;
					case 4:
						if($wgrp != "Ninguno")
						{
							if(!isset($expo))
							{
								$sections=explode("-",$wpub);
								if(validarClave($conex,$sections[0],$wpass))
								{
									$especial=1;
									$ruta="/var/www/matrix/".$wgrp."/";
									$dh=@opendir($ruta);
									if(@readdir($dh) == false)
										mkdir($ruta,0777);
									$ruta="/var/www/matrix/".$wgrp."/procesos/";
									$dh=@opendir($ruta);
									if(@readdir($dh) == false)
										mkdir($ruta,0777);
									$rutac="/var/www/matrix/".$wgrp."/procesos/copia/";
									$dhc=@opendir($rutac);
									if(@readdir($dhc) == false)
										mkdir($rutac,0777);
								}
								else
								{
									echo "USUARIO O CLAVE ERRONEOS<BR>";
								}
							}
							else
							{
								$ruta="/var/www/matrix/".$wgrp."/procesos/";
								$rutac="/var/www/matrix/planos/";
							}
						}
						else
							echo "NO HA SELECIONADO EL GRUPO<BR>";
					break;
					case 5:
						if($wgrp != "Ninguno")
						{
							if(!isset($expo))
							{
								$sections=explode("-",$wpub);
								if(validarClave($conex,$sections[0],$wpass))
								{
									$especial=1;
									$ruta="/var/www/matrix/".$wgrp."/";
									$dh=@opendir($ruta);
									if(@readdir($dh) == false)
										mkdir($ruta,0777);
									$ruta="/var/www/matrix/".$wgrp."/reportes/";
									$dh=@opendir($ruta);
									if(@readdir($dh) == false)
										mkdir($ruta,0777);
									$rutac="/var/www/matrix/".$wgrp."/reportes/copia/";
									$dhc=@opendir($rutac);
									if(@readdir($dhc) == false)
										mkdir($rutac,0777);
								}
								else
								{
									echo "USUARIO O CLAVE ERRONEOS<BR>";
								}
							}
							else
							{
								$ruta="/var/www/matrix/".$wgrp."/reportes/";
								$rutac="/var/www/matrix/planos/";
							}
						}
						else
							echo "NO HA SELECIONADO EL GRUPO<BR>";
					break;
					case 6:
						if($wgrp != "Ninguno")
						{
							if(!isset($expo))
							{
								$sections=explode("-",$wpub);
								if(validarClave($conex,$sections[0],$wpass))
								{
									$especial=1;
									$ruta="/var/www/include/".$wgrp."/";
									$dh=@opendir($ruta);
									if(@readdir($dh) == false)
										mkdir($ruta,0777);
									$rutac="/var/www/include/".$wgrp."/copia/";
									$dhc=@opendir($rutac);
									if(@readdir($dhc) == false)
										mkdir($rutac,0777);
								}
								else
								{
									echo "USUARIO O CLAVE ERRONEOS<BR>";
								}
							}
							else
							{
								$ruta="/var/www/include/".$wgrp."/";
								$rutac="/var/www/matrix/planos/";
							}
						}
						else
							echo "NO HA SELECIONADO EL GRUPO<BR>";
					break;
					case 7:
						if($wgrp != "Ninguno")
						{
							$ruta="/var/www/matrix/images/medical/".$wgrp."/";
							$dh=@opendir($ruta);
							if(@readdir($dh) == false)
								mkdir($ruta,0777);
						}
						else
							echo "NO HA SELECIONADO EL GRUPO<BR>";
					break;
				}
			}
			if($tipo == "1")
			{
				if(!isset($expo))
				{
					if ($especial == 1 and (!isset($rutac) or !@copy($ruta.$real, $rutac.$real)))
					{
						echo "ERROR LA COPIA DE RESPALDO NO PUDO HACERSE<br>";
						if(file_exists($ruta.$real))
							unset($ruta);
					}
					if (!isset($ruta) or !@copy($files, $ruta.$real)) 
					{
						echo "ERROR LA COPIA NO PUDO HACERSE<br>";
					}
					else
					{
						echo "<table border=0 align=center>";
						echo "<tr><td align=center bgcolor=#DDDDDD>LA PUBLICACION EXITOSA</td></tr>";
						echo "<tr><td align=center bgcolor=#DDDDDD>ARCHIVO: <B>".$real."</B></td></tr>";
						echo "<tr><td align=center bgcolor=#DDDDDD>RUTA :<B>".$ruta."</B></td></tr></table>";
						if($especial == 1)
							grabarLog($conex,$sections[0],$ruta,$real);
					}
				}
				else
				{
					$partes=explode(".",$real);
					$realI=$partes[0].".zip";
					if (!copy($ruta.$real, $rutac.$realI))
					{
						echo "ERROR LA IMPORTACION NO PUDO HACERSE<br>";
					}
					else
					{
						$datafile="/matrix/planos/".$realI;
						echo "<table border=0 align=center>";
						echo "<tr><td align=center bgcolor=#DDDDDD>LA IMPORTACION FUE EXITOSA</td></tr>";
						echo "<tr><td align=center bgcolor=#DDDDDD>ARCHIVO: <B>".$real."</B></td></tr>";
						echo "<tr><td align=center bgcolor=#DDDDDD>RUTA :<B>".$ruta."</B></td></tr>";
						echo "<tr><td align=center bgcolor=#DDDDDD><A href=".$datafile.">Click Derecho Para Bajar el Archivo : ".$realI."</A></td></tr></table>";
					}
				}
			}
			else
			{
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor=#DDDDDD>LA PUBLICACION DE</td></tr>";
				echo "<tr><td align=center bgcolor=#DDDDDD>ARCHIVO: <B>".$real."</B></td></tr>";
				echo "<tr><td align=center bgcolor=#DDDDDD>RUTA :<B>".$ruta."</B></td></tr>";
				echo "<tr><td align=center bgcolor=#DDDDDD>NO SE REALIZO. USTED CANCELO LA OPERACION</td></tr></table>";
			}
		}
}
?>
</body>
</html>
