<html>
<head>
  <title>MATRIX Ver. 2013-04-23</title>
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
	function clean(variable)
	{
		variable.value = '';
	}
	function ira()
	{
		document.PassHCE.wpassa.focus();
	}
	</script>
</head>
<body BGCOLOR="" onload=ira()>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name='PassHCE' action='PassHCE.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wpass) or !isset($wpassr) or !isset($wpassa))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2><b>HISTORIA CLINICA ELECTRONICA HCE<b></td></tr>";
			echo "<tr><td align=center colspan=2>ADMINISTRACION DE FIRMAS ELECTRONICA</td></tr>";
			echo "<tr><td align=center colspan=2>ACTUALIZACION DE FIRMA ELECTRONICA</td></tr>";
			echo "<td bgcolor=#cccccc align=center>Firma Anterior</td>";
			echo "<td bgcolor=#cccccc align=center><input type='password' name='wpassa' value='' size=30 maxlength=80 onBlur='javascript:encriptar(this);' onFocus='javascript:clean(this);'></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Firma Nueva</td>";
			echo "<td bgcolor=#cccccc align=center><input type='password' name='wpass' value='' size=30 maxlength=80 onBlur='javascript:encriptar(this);' onFocus='javascript:clean(this);'></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Redigite Firma Nueva</td>";
			echo "<td bgcolor=#cccccc align=center><input type='password' name='wpassr' value='' size=30 maxlength=80 onBlur='javascript:encriptar(this);' onFocus='javascript:clean(this);'></td></tr>";		
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
		}
		else
		{  
			$query = "select Usucla from ".$empresa."_000020 where Usucod='".$key."'";
			$err = mysql_query($query,$conex); 
			$row = mysql_fetch_array($err);
			$wfecw=date("Y-m-d");
			$wfecw=date_create($wfecw);
			date_add($wfecw, date_interval_create_from_date_string('12 months'));	
			$wfecw1=date_format($wfecw, 'Y-m-d');
			
			if($wpass != "" and $wpass==$wpassr and $wpass != $row[0] and ($wpassa == $row[0] or $row[0] == "" or $row[0] == " ") and strlen($wpass) >= 4)
			{
				$query = "update ".$empresa."_000020 set Usucla='".$wpass."', Usufve='".$wfecw1."'  where Usucod='".$key."'";
				$err = mysql_query($query,$conex);
				if($err !=1)
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>LA FIRMA NO SE PUDO CAMBIAR !!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/okf.png' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LA FIRMA SE CAMBIO SATISFACTORIAMENTE !!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ffff00 LOOP=-1>ERROR EN LA DIGITACION O LONGITUD MINIMA DE LA FIRMA (4 CARACTERES) O LA FIRMA ANTERIOR NO COINCIDE !!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
}
?>
</body>
</html>
