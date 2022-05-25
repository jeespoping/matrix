<html>
<head>
  	<title>MATRIX Registros de Formularios Tipo3 Historia Clinica Hospitalaria HCE</title>
	<link type='text/css' href='HCE.css' rel='stylesheet'>
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    


<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
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
	
	function firmas()
	{
		firma =document.getElementById('firma').value;
		if (firma != "" && firma.substring(0,3) != "HCE")
		{
			firma = "HCE "+hex_sha1(document.getElementById('firma').value);
			document.getElementById('firma').value=firma;
		}
	}
	
	function enter()
	{
		document.forms.HCE_Tipo3.submit();
	}	
	
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("root/comun.php");

function buscartitulo(&$t,$ord,$n,$f)
{
	for ($w=0;$w<$n;$w++)
	{
		$w1=$n-$w-1;
		if($t[$w1][0] < $ord and $t[$w1][3] == 1 and $t[$w1][2] == $f)
			return -1;
		elseif($t[$w1][0] < $ord and $t[$w1][3] == 0 and $t[$w1][2] == $f)
		{
			$t[$w1][3] = 1;
			return $w1;
		}
		
		
	}
	return -1;
}

function calcularspan($s)
{
	if(strlen($s) < 51)
		return 1;
	else
		return 8;
}

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
	{
		if(substr_count($chain, '-') == 1)
			return substr($chain,strpos($chain,"-")+1);
		else
			return $chain;
	}
}

/**********************************************************************************************************************  
	   PROGRAMA : HCE_Tipo3.php
	   Fecha de Liberacion : 2010-10-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2010-10-01
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface grafica que permite visualizar los registros de formularios Tipo 3
	   Para verificarlos y aplicarles la firma electronica.
	   
	   REGISTRO DE MODIFICACIONES :
	   	.2010-10-01
	   		Release de Version Beta. 
	   	
	   	.2011-04-25
			Se incluyo la grabacion en la tabla 36 de registro de firmas para los formularios Tipo 3.
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	$wemp_pmla = $_REQUEST['wemp_pmla'];
	echo "<form name='HCE_Tipo3' action='HCE_Tipo3.php?wemp_pmla=".$wemp_pmla."' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($ok))
	{
		$wswfirma=0;
		if(substr($firma,0,3) == "HCE")
		{
			$query = "SELECT count(*)  from ".$empresa."_000020 ";
			$query .= " where Usucod = '".$key."' ";
			$query .= "   and Usucla = '".substr($firma,4)."' ";
			$query .= "   and Usuest = 'on' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			if ($row[0] > 0)
				$wswfirma=1;
		}
		if($wswfirma == 1)
		{
			for ($i=0;$i<=$knum;$i++)
			{
				$query = "insert ".$empresa."_".$wformulario." (medico, fecha_data, hora_data, movpro, movcon, movhis, moving, movtip, movdat, movusu, Seguridad) values ('";
				$query .=  $empresa."','";
				$query .=  $fecha[$i]."','";
				$query .=  $hora[$i]."','";
				$query .=  $wformulario."',";
				$query .=  "1000,'";
				$query .=  $whis."','";
				$query .=  $wing."','";
				$query .=  "Firma','";
				$query .=  substr($firma,4)."','";
				$query .=  $key."',";
				$query .=  "'C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS DE HISTORIA CLINICA (FIRMA) : ".mysql_errno().":".mysql_error());
				
				$query = "select count(*) FROM ".$empresa."_000036 where Firpro='".$wformulario."' and fecha_data='".$fecha[$i]."' and hora_data='".$hora[$i]."' and Firhis='".$whis."' and Firing='".$wing."' and Firusu='".$key."' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row1 = mysql_fetch_array($err1);
				if ($row1[0] == 0)
				{
					$query = "insert ".$empresa."_000036 (medico, fecha_data, hora_data, Firpro, Firhis, Firing, Firusu, Firfir, Seguridad) values ('";
					$query .=  $empresa."','";
					$query .=  $fecha[$i]."','";
					$query .=  $hora[$i]."','";
					$query .=  $wformulario."','";
					$query .=  $whis."','";
					$query .=  $wing."','";
					$query .=  $key."','";
					$query .= "on',";
					$query .=  "'C-".$empresa."')";
					//echo $i." ".$DATA[$i][1]." ".$registro[$i][0]."<br>";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO ARCHIVO 36 DE FORMULARIOS FIRMADOS : ".mysql_errno().":".mysql_error());
				}
				else
				{
					$query =  " update ".$empresa."_000036 set Firfir = 'on' ";
					$query .=  "  where fecha_data='".$fecha[$i]."' and hora_data='".$hora[$i]."' and Firpro='".$wformulario."' and Firhis='".$whis."' and Firing='".$wing."' and Firusu='".$key."' ";
					$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ARCHIVO 36 DE FORMULARIOS FIRMADOS : ".mysql_errno().":".mysql_error());
				}
			}
			$FIRMAOK=1;
			$firma="";
		}
	}
	$wbasedatoMovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	//                 0      1      2      3      4      5      6      7      8      9      10     11
	$wbasedatoMovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
	$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wbasedatoMovhos."_000016,".$wbasedatoMovhos."_000018,".$wbasedatoMovhos."_000011 ";
	$query .= " where pacced = '".$wcedula."'";
	$query .= "   and pactid = '".$wtipodoc."'";
	$query .= "   and  pacced = oriced ";
	$query .= "   and  pactid = oritid ";
	$query .= "   and oriori = '".$wemp_pmla."' ";
	$query .= "   and inghis = orihis ";
	$query .= "   and  inging = oriing ";
	$query .= "   and ubihis = inghis "; 
	$query .= "   and ubiing = inging ";
	$query .= "   and ccocod = ubisac ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	$sexo="MASCULINO";
	if($row[5] == "F")
		$sexo="FEMENINO";
	$ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($aa - $ann)/360;
	$meses=(($aa - $ann) % 360)/30;
	if ($ann1<1)
	{
		$dias1=(($aa - $ann) % 360) % 30;
		$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
	}
	else
	{
		$dias1=(($aa - $ann) % 360) % 30;
		$wedad=(string)(integer)$ann1." A�os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
	}
	$wpac = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
	$dia=array();
	$dia["Mon"]="Lun";
	$dia["Tue"]="Mar";
	$dia["Wed"]="Mie";
	$dia["Thu"]="Jue";
	$dia["Fri"]="Vie";
	$dia["Sat"]="Sab";
	$dia["Sun"]="Dom";
	$mes["Jan"]="Ene";
	$mes["Feb"]="Feb";
	$mes["Mar"]="Mar";
	$mes["Apr"]="Abr";
	$mes["May"]="May";
	$mes["Jun"]="Jun";
	$mes["Jul"]="Jul";
	$mes["Aug"]="Ago";
	$mes["Sep"]="Sep";
	$mes["Oct"]="Oct";
	$mes["Nov"]="Nov";
	$mes["Dec"]="Dic";
	$fechal=strftime("%a %d de %b del %Y");
	$fechal=$dia[substr($fechal,0,3)].substr($fechal,3);
	$fechal=substr($fechal,0,10).$mes[substr($fechal,10,3)].substr($fechal,13);
	$color="#dddddd";
	$color1="#C3D9FF";
	$color2="#E8EEF7";
	$color3="#CC99FF";
	$color4="#99CCFF";
	$wintitulo="Historia:".$row[6]." Ingreso:".$row[7]." Paciente:".$wpac;
	echo "<table border=1>";
	echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/HCE/clinica.png'></td>";	
	echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>".$fechal."<input type='text' name='reloj' size='10' readonly='readonly' class=tipo3R></td></tr>";
	echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-".$row[7]."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
	echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
	echo "</table><br><br>";
	$whis=$row[6];
	$wing=$row[7];
	echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
	echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
	echo "<input type='HIDDEN' name= 'wformulario' value=".$wformulario.">";
	echo "<input type='HIDDEN' name= 'whis' value=".$whis.">";
	echo "<input type='HIDDEN' name= 'wing' value=".$wing.">";
	echo "<input type='HIDDEN' name= 'wtitulo' value=".$wtitulo.">";
	
	$queryI  = " select ".$empresa."_000002.Detorp,".$empresa."_000002.Detdes from ".$empresa."_000002 ";
	$queryI .= " where ".$empresa."_000002.detpro = '".$wformulario."' ";
	$queryI .= "   and ".$empresa."_000002.detest='on' "; 
	$queryI .= "   and ".$empresa."_000002.Dettip not in ('Titulo','Subtitulo','Label','Link') "; 
	$queryI .= "  order by 1 ";
	$err = mysql_query($queryI,$conex);
	$num = mysql_num_rows($err);
	$totcol=$num;
	$columnas=array();
	if ($num > 0)
	{
		$var=array();
		$label=array();
		echo "<center><table border=0>";
		echo "<tr><td colspan=".$totcol." id=tipoH01>Registros Pendientes De Firma Electronica : ".$wtitulo."</td></tr>";
		echo "<tr>";
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
			$columnas[$j] = $row[0];
			echo "<td id=tipoH02>".$row[1]."</td>";
		}
		echo "</tr>";
	}
	
	$knum=-1;
	$matrix=array();
	$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".hora_data,max(".$empresa."_".$wformulario.".movcon) as a from ".$empresa."_".$wformulario." "; 
	$query .= " where ".$empresa."_".$wformulario.".movhis='".$whis."' ";
	$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
	$query .= "   and  ".$empresa."_".$wformulario.".movusu='".$key."' "; 
	$query .= " group by 1,2  ";
	$query .= " having a < 1000 ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Detorp,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
			$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
			$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
			$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
			$query .= "   and ".$empresa."_".$wformulario.".movusu = '".$key."' ";
			$query .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$row[0]."' "; 
			$query .= "   and ".$empresa."_".$wformulario.".hora_data = '".$row[1]."' ";
			$query .= "   and ".$empresa."_".$wformulario.".movpro = ".$empresa."_000002.detpro ";
			$query .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.detcon ";
			$query .= "   and ".$empresa."_000002.detest='on' "; 
			$query .= "  order by 3 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num1>0)
			{
				$knum++;
				for ($j=0;$j<$totcol;$j++)
				{
					$matrix[$knum][$columnas[$j]]="";
				}
				echo "<input type='HIDDEN' name= 'fecha[".$knum."]' value='".$row[0]."'>";
				echo "<input type='HIDDEN' name= 'hora[".$knum."]' value='".$row[1]."'>";
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					$matrix[$knum][$row1[2]]=$row1[3];
				}
			}
		}
	}
	echo "<input type='HIDDEN' name= 'knum' value=".$knum.">";
	for ($i=0;$i<=$knum;$i++)
	{
		if($i % 2 == 0)
			$color="tipoH03";
		else
			$color="tipoH04";
		echo "<tr>";
		for ($j=0;$j<$totcol;$j++)
			echo "<td id=".$color.">".$matrix[$i][$columnas[$j]]."</td>";
		echo "</tr>";
	}
	if(!isset($firma))
		$firma="";
	echo "<tr><td id=tipoL06 colspan=".$totcol.">Firma Digital : <input type='password' name='firma' size=40 maxlength=80 id='firma' value='".$firma."' class=tipo3 OnBlur='firmas()'></td></tr>";
	echo "<tr><td id=tipoL09 colspan=".$totcol.">DATOS COMPLETOS ? <input type='checkbox' name='ok' id='ok' OnClick='enter()'></td></tr>";
	if(isset($FIRMAOK))
		echo "<tr><td colspan=".$totcol." id=tipoLGOK><IMG SRC='/matrix/images/medical/root/felizH.png'> REGISTROS FIRMADOS OK!!!!</td></tr>";
	echo "</table></center>";
}
?>
