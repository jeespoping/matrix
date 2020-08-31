<html>
<head>
  	<title>MATRIX Tablero de Formularios Pendientes de Firma</title>  	

    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo4A(color:#000066;background:#99CCFF;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18A{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo19A{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo20A{color:#000066;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo19{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipoDCK{color:#000066;background:#E8EEF7;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	#tipoLCK{color:#000066;background:#C3D9FF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	
    	#tipoT00{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipoT01{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:2em;}
    	#tipoT02{color:#000000;background:#C3D9FF;font-size:12pt;font-family:Arial;font-weight:bold;width:110em;text-align:left;height:2em;}
    	#tipoT03{color:#000000;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:normal;width:110em;text-align:right;height:1em;}
    	#tipoT04{color:#FFFFFF;background:#003366;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	
    	#tipoT05{color:#000066;background:#99CCFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:3em;width:100em;}
    	#tipoT06{color:#000066;background:#CC99FF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:3em;width:100em;}
    	
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG11{color:#FFFFFF;background:#99CCFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	
    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:3em;}
    	#tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:3em;}
    	
    </style>    

    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

    <script type="text/javascript">
	<!--  
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
	function ejecutar(path)
	{
		window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
	}
	function enter()
	{
		document.forms.Firmas.submit();
	}
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function teclado2()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13) event.returnValue = false;
	}
	function teclado3()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13 & event.keyCode != 45) event.returnValue = false;
	}
	//-->
</script>
 
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : Firmas.php
	   Fecha de Liberación : 2011-04-26
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2011-04-26
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite Ingresar las firmas a los
	   formularios que se encuentren pendientes de firmar.
	   
	   
	   REGISTRO DE MODIFICACIONES :  
	   .2011-04-26
	   		Release de Versión Beta.
	   
	   		
[*DOC]
***********************************************************************************************************************/
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Firmas' action='Firmas.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	
	echo "<table border=0 CELLSPACING=0>";
	echo "<tr><td align=center id=tipoT01><IMG SRC='/matrix/images/medical/root/lmatrix.jpg'></td>";
	echo "<td id=tipoT02>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;FORMULARIOS PENDIENTES DE FIRMA HCE&nbsp;&nbsp;<A HREF='/MATRIX/root/Reportes/DOC.php?files=/matrix/HCE/procesos/Firmas.php' target='_blank'>Version 2011-04-26</A></td></tr>";
	echo "<tr><td id=tipoT03 colspan=2></td></tr>";
	echo "</table><br><br>";
	echo "<center><IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'></IMG></center><br>";
	
	$dateA=date("Y-m-d");
	$dateB=strtotime("-1 day");
	$dateC=strftime("%Y-%m-%d",$dateB);
	
	$Semp="";
	$Sesp=array();
	$Sroot="off";
	$numesp=0;
	$SempT=array();
	$numemp=0;
	$Suni=array();
	$numuni=0;
	$Wexiste=0;
	
	if($IIPP == "192.168.0.1" or substr($IIPP,0,5) == "132.1" or $IIPP == "127.0.0.1" or $IIPP == "::1")
	{
		if(isset($num) and $num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				if(isset($CHECK[$i]))
				{
					$mess="";
					$query = " Select ".$empresa."_000036.Fecha_data, ".$empresa."_000036.Hora_data, Firpro, Firhis, Firing, Firusu, Usucla ";
					$query .= " from ".$empresa."_000036,".$empresa."_000020 ";
					$query .= " where ".$empresa."_000036.id = ".$var[$i][0];
					$query .= "   and Firusu = Usucod ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						$row = mysql_fetch_array($err);
						if($firma[$i] == $row[6])
						{
							$query = "insert ".$empresa."_".$row[2]." (medico, fecha_data, hora_data, movpro, movcon, movhis, moving, movtip, movdat, movusu, Seguridad) values ('";
							$query .=  $empresa."','";
							$query .=  $row[0]."','";
							$query .=  $row[1]."','"; 
							$query .=  $row[2]."',";
							$query .=  "1000,'";
							$query .=  $row[3]."','";
							$query .=  $row[4]."','";
							$query .=  "Firma','";
							$query .=  $firma[$i]."','";
							$query .=  $row[5]."',";
							$query .=  "'C-".$empresa."')";
							$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO FIRMA EN HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
							$query =  " update ".$empresa."_000036 set Firfir = 'on' ";
							$query .=  "  where fecha_data='".$row[0]."' and hora_data='".$row[1]."' and Firpro='".$row[2]."' and Firhis='".$row[3]."' and Firing='".$row[4]."' and Firusu='".$row[5]."' ";
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ARCHIVO 36 DE FORMULARIOS FIRMADOS : ".mysql_errno().":".mysql_error());
							$mess="1REGISTRO ".$row[2]." DE ".$row[0]."-".$row[1]." FIRMADO CORRECTAMENTE ";
						}
						else
							$mess="2LA FIRMA DIGITADA NO ES LA RESGISTRADA POR EL USUARIO EN LA BASE DE DATOS !!!";
					}
				}
			}
			echo "<table border=0 align=center id=tipo5>";
			if(substr($mess,0,1) == 1)
				echo "<tr><td id=tipoT05><IMG SRC='/matrix/images/medical/root/FELIZ.PNG' style='vertical-align:middle;'>&nbsp;".substr($mess,1)."</td></tr>";
			else
				echo "<tr><td id=tipoT06><IMG SRC='/matrix/images/medical/root/MALO.PNG' style='vertical-align:middle;'>&nbsp;".substr($mess,1)."</td></tr>";
			echo "</table></center>";
		}
		$tipoac="tipo20A";
		
		//                                         0                              1        2       3       4       5       6          7                    8            9
		$query = " Select ".$empresa."_000036.Fecha_data, ".$empresa."_000036.Hora_data, Firpro, Encdes, Firhis, Firing, Firusu, Descripcion, ".$empresa."_000036.id, Ubiald ";
		$query .= " from ".$empresa."_000036,".$empresa."_000001,usuarios,movhos_000018 ";
		$query .= " where Firfir = 'off'  ";
		$query .= " and Firpro = Encpro ";
		$query .= " and Firusu = Codigo ";
		$query .= " and Firhis = Ubihis ";
		$query .= " and Firing = Ubiing ";
		$query .= "  order by 1,2 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$var=array();
			echo "<table border=0 align=center id=tipo5>";
			echo "<tr><td id=tipoT04 align=center>FECHA<BR>GRABACION</td><td id=tipoT04 align=center>HORA<BR>GRABACION</td><td id=tipoT04 align=center>CODIGO<br>FORMULARIO</td><td id=tipoT04 align=center>DESCRIPCION</td><td id=tipoT04 align=center>HISTORIA</td><td id=tipoT04 align=center>INGRESO</td><td id=tipoT04 align=center>USUARIO</td><td id=tipoT04 align=center>NOMBRE<br>USUARIO</td><td id=tipoT04 align=center>FIRMA</td><td id=tipoT04 align=center>FIRMAR</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$var[$i][0]=$row[8];
				echo "<input type='HIDDEN' name= 'var[".$i."][0]' value='".$var[$i][0]."'>";
				echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
				if($i % 2 == 0)
				{
					$tipo="tipo18";
					$tip="tipoD";
					$class="tipo4A";
				}
				else
				{
					$tipo="tipo19";
					$tip="tipoL";
					$class="tipo4";
				}
				if($row[9] == "on")
					echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$row[2]."</td><td id=".$tipo.">".$row[3]."</td><td id=".$tipo.">".$row[4]."</td><td id=".$tipo.">".$row[5]."</td><td id=".$tipo.">".$row[6]."</td><td id=".$tipo."A>".$row[7]."</td><td id=".$tipo."><input type='password' name='firma[".$i."]' size=40 maxlength=80 id='firma' class=tipo3 onBlur='javascript:encriptar(this);'</td><td id=".$tip."CK><input type='checkbox' name='CHECK[".$i."]' onclick='enter()' class=".$class."></td></tr>";
				else
					echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$row[2]."</td><td id=".$tipo.">".$row[3]."</td><td id=".$tipo.">".$row[4]."</td><td id=".$tipo.">".$row[5]."</td><td id=".$tipo.">".$row[6]."</td><td id=".$tipo."A>".$row[7]."</td><td id=".$tipoac." colspan=2>ACTIVO</td></tr>";
			}
		}
		echo "<tr><td id=tipoT02 colspan=10><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;NUMERO DE FORMULARIOS PENDIENTES DE FIRMA : ".$num."</td></tr>";
		echo "</table></center>";
	}
	else
	{
		echo "<table border=0 align=center id=tipo5>";
		echo "<tr><td id=tipoT02 colspan=10><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;LA HISTORIA CLINICA NO PUEDE SER USADA FUERA DE LA INSTITUCION !!!</td></tr>";
		echo "</table></center>";
	}
}
?>
</body>
</html>
