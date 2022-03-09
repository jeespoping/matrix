<html>
<head>
  	<title>MATRIX Programa de Notas Confirmatorias en la HCE</title>
	<link type='text/css' href='HCE.css' rel='stylesheet'>
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    <script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>
	<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
    <!-- <script type='text/javascript' src='HCE_Seguridad.js' ></script> -->
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
		
		function firma()
		{
			debugger;
			firma1 =document.getElementById('firma1').value;
			if (firma1 != "" && firma1.substring(0,3) != "HCE")
			{
				firma1 = "HCE "+hex_sha1(document.getElementById('firma1').value);
				document.getElementById('firma1').value=firma1;
			}
		}
	</script>
	 <script type="text/javascript">
    /*********************************************************************************
     * Encuentra la posicion en X de un elemento
     *********************************************************************************/
    function findPosX(obj)
      {
        var curleft = 0;
        if(obj.offsetParent)
            while(1)
            {
              curleft += obj.offsetLeft;
              if(!obj.offsetParent)
                break;
              obj = obj.offsetParent;
            }
        else if(obj.x)
            curleft += obj.x;
        return curleft;
      }

    /************************************************************************************
     * encuentra la posicion Y de un elemento
     ************************************************************************************/
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
      function posdivs(auxdivpix,X,Y,An,Al,ID,grafica)
      {
	    X=parseInt(X);
	    Y=parseInt(Y);
		auxdivpix.style.position = "absolute";
		auxdivpix.style.zIndex = "200";
		auxdivpix.style.top = parseInt( Y + findPosY(document.getElementById(grafica)))+"px";
		auxdivpix.style.left = parseInt( X + findPosX(document.getElementById(grafica)))+"px";
		auxdivpix.style.width = An+"px";
		auxdivpix.style.height = Al+"px";
		auxdivpix.style.border='solid';
		auxdivpix.innerHTML="<table><tr><td bgcolor=white><font size=2em><b>"+ID+"</b></font></td></tr></table>";
		
      }
      function pintardivs()
      {
		var elements = document.getElementsByTagName('img'); 
		if(document.getElementById('Hgraficas'))
		{
			var G = document.getElementById('Hgraficas').value;
			//alert('Valor de las Graficas : '+G+" "+elements.length);
			GT = G.split('|');
			for(var x = 0; x < elements.length; x++)
			{ 
				var textG = "";
			    varable = GT[x];
			    //alert(elements[x].id);
			    var ID = 1;
			    if(varable.length > 0 && elements[x].id.substring(0,1) == "G")
			    {
					frag1 = varable.split('^');
					div=document.createElement('div');
					for (i=1;i<frag1.length;i++)
					{
						var div=document.createElement('div');
						frag2 = frag1[i].split('~');  
						div.id=frag2[0];
						//tipmage.setTooltip(frag2[1],frag2[2],frag2[3],frag2[4],frag2[5],frag2[0]);
						document.HCE_NotasC.appendChild(div);
						posdivs(div,frag2[1],frag2[2],frag2[3],frag2[4],frag2[0],elements[x].id);
						textG = textG + frag2[0]+". "+frag2[5]+"<br>";
				    }
			    }
			   	var divt=document.createElement('div');
				document.HCE_NotasC.appendChild(divt);
				divt.style.position = "absolute";
				divt.style.top = parseInt(findPosY(elements[x]))+"px";
				divt.style.left = (parseInt(findPosX(elements[x]))+parseInt(elements[x].offsetWidth)+10)+"px";
				divt.innerHTML="<font size=2em>"+textG+"</font>"; 
				//alert(textG+" "+divt.style.top+" "+divt.style.left+elements[x].id);
		    }
	    }
      }
    </script>
</head>
<body BGCOLOR="FFFFFF"  onLoad='pintardivs()' oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--



	function enter()
	{
		document.forms.HCE_NotasC.submit();
	}
	function salto()
	{
		var contador=0;
		var table = document.getElementById("mitablita"); 
		var cells = table.getElementsByTagName("tr");   
		for (var i = 0; i < cells.length; i++) 
		{
			contador = contador + cells[i].clientHeight;
			
			index = cells[i].rowIndex;
			
			if(contador > 800)
			{
				contador=cells[i].clientHeight;
				var aux = document.createElement( "div" );
				aux.innerHTML = "<h1 style='page-break-after: always'></h1>";
				cells[i].parentNode.rows[index-1].cells[0].appendChild( aux.firstChild );
				fila = table.insertRow( index );
				fila.appendChild( cells[i].insertCell(0) );
				aux.innerHTML = "<div>NOMBRE PACIENTE</div>";
				cells[i].parentNode.rows[index].cells[0].appendChild( aux.firstChild );
			}
		}
		alert(contador); 
		window.print();
	}
	function salto1(titulo)
	{
		alert("Entre "+titulo);
		document.title = titulo;
		window.print();
	}
	function activarModalIframe(titulo,nombre,url,alto,ancho)
	{
		var Sialto="no";
		var Siancho="no";
		var Sboton="si";
		if(alto == '-1')
		{
			alto='0';
			Sboton="no";
		}
		if(alto == '0')
		{
			Sialto="si";
			alto=screen.availHeight;
		}
		if(ancho == '0')
		{
			Siancho="si";
			ancho=screen.availWidth;
		}
		if(Sboton == "si")
		{
			var html = "" +
			"<table cellpadding=1 cellspacing=1 width='100%' style='cursor:default'>" +
			"<tr height='10' class='encabezadoTabla'>" +
			"<td >" +
			"<b>"+titulo+"</b>" +
			"</td>"+    
			"<td align='center'>" +
			"<img src='../../images/medical/HCE/button.gif' title='Cerrar' onclick='javascript:cerrarModal();' style='cursor:hand; cursor: pointer;'>" +
			"</td></tr>" +    
			"<tr><td colspan=2 class='textoNormal'>";
		}
		else
		{
			var html = "" +
			"<table cellpadding=1 cellspacing=1 width='100%' style='cursor:default'>" +
			"<tr height='10' class='encabezadoTabla'>" +
			"<td >" +
			"<b>"+titulo+"</b>" +
			"</td>"+    
			"<td align='center'>" +
			"" +
			"</td></tr>" +    
			"<tr><td colspan=2 class='textoNormal'>";
		}
		if(Sialto == 'si' && Siancho == 'si')
		{
			html = html + "<iframe name='" + nombre + "' src='" + url + "' height='" + (parseInt(alto,10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>";
		}
		else
		{
	    	html = html + "<iframe name='" + nombre + "' src='" + url + "' width='100%' height='" + (parseInt(alto,10) - 30) + "' width='" + ancho + "' frameborder='0'></iframe>";
    	}
	    
	    html = html + "</td></tr></table>";
	    
	   
	    //var pare = window.parent.parent;
	    var pare = window.parent;
	    
	    if(Sialto == 'si' && Siancho == 'si')
	    {
			$.blockUI({ message: html, css: { width: ancho + 'px',left: '0px',top: '0px'  },centerX: false,centerY: false});	
	    }
	    else
	    {
			$.blockUI({ message: html, css: { width: ancho + 'px',left: '20px',top: '20px'  },centerX: false,centerY: false});	
		}
	}
	
	function cerrarModal()
	{
		$.unblockUI();
	}
	
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("hce/HCE_print_function.php");
include_once("hce/funcionesHCE.php");

function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}


/**********************************************************************************************************************  
	   PROGRAMA : HCE_NotasC.php
	   Fecha de Liberación : 2012-07-18
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2019-08-13
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite grabar notas de confirmatorias que avalan los
	   registros clinicos realizados por estudiantes a cargo de un medico especialista o de una enfermera jefe.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	    .2019-08-13
	        Se agrega el include a funcionesHCE.php con la función calcularEdadPaciente() y se reemplaza en el script el 
			cálculo de la edad del paciente por dicha función, ya que el cálculo se realizaba con 360 días, es decir, no se 
			tenían en cuenta los meses de 31 días y para los pacientes neonatos este dato es fundamental.
	    .2016-11-22
	        Se cambia el query sobre los datos para traer registros de campos que ha sido inactivados.
	        
	    .2015-10-20
			Se modifica el programa para acceder a la tabla hce 50 y registrar el docente que esta haciendo la 
			confirmacion del formulario.
		
	    .2015-01-15
			Se adiciona la actualizacion del campo Firfir de la Tabla 36 en "on"
			
	    .2013-12-19
			Se cambia la forma de grabacion de la nota confirmatoria para realizar mas validaciones.
			
	    .2013-11-05
			Se ponen dinamicas las tablas de hce y movhos al igual que la empresa origen.
              
	    .2013-05-02
			Se modifico el programa para incluir la funcion de impresion estandar HCE/HCE_print_function.php.
			
	    .2012-12-17
			Se empaqueto la seguridad contra copy+paste en el archivo HCE_Seguridad.js
              
	   	.2012-08-09
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_NotasC' action='HCE_NotasC.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
	echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
	if(isset($wing))
		echo "<input type='HIDDEN' name= 'wing' value='".$wing."'>";
	if(isset($wservicio))
		echo "<input type='HIDDEN' name= 'wservicio' value='".$wservicio."'>";
	if(isset($ok1))
	{
		if(strlen($wcomen) > 0)
		{
			$POK=0;
			$query = "SELECT count(*)  from ".$empresa."_000020 ";
			$query .= " where Usucod = '".$key."' ";
			$query .= "   and Usucla = '".substr($firma1,4)."' ";
			$query .= "   and Usuest = 'on' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			if ($row[0] > 0)
			{
				$query = "lock table ".$empresa."_".$wformulario." LOW_PRIORITY WRITE, ".$empresa."_000020 LOW_PRIORITY WRITE, ".$empresa."_000036 LOW_PRIORITY WRITE, ".$empresa."_000050 LOW_PRIORITY WRITE ";
				$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE DATOS DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
				$queryJ  = " select movcon from ".$empresa."_".$wformulario." ";
				$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
				$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
				$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
				$queryJ .= "   and ".$empresa."_".$wformulario.".fecha_data='".$wfecha_data."' ";
				$queryJ .= "   and ".$empresa."_".$wformulario.".Hora_data='".$whora_data."' ";
				$queryJ .= "   and ".$empresa."_".$wformulario.".movcon > 999 ";
				$queryJ .= " order by movcon desc ";
				$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
				$numN = mysql_num_rows($err);
				if($numN == 0)
				{
					$wultimo=1001;
					$query = "SELECT Usucla from ".$empresa."_000020 ";
					$query .= " where Usucod = '".$usuarioPP."' ";
					$query .= "   and Usuest = 'on' ";
					$query .= "   and (Usures = 'on' ";
					$query .= "    or  Usudep = 'on') ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$numE = mysql_num_rows($err);
					if($numE > 0)
					{
						$row = mysql_fetch_array($err);
						$wcomen=date("Y-m-d")." ".(string)date("H:i:s")." <b><u>NOTA CONFIRMATORIA</u></b> <br>".$wcomen;
						$query = "insert ".$empresa."_".$wformulario." (medico, fecha_data, hora_data, movpro, movcon, movhis, moving, movtip, movdat, movusu, Seguridad) values ('";
						$query .=  $empresa."','";
						$query .=  $wfecha_data."','";
						$query .=  $whora_data."','";
						$query .=  $wformulario."',1000";
						$query .=  ",'";
						$query .=  $whis."','";
						$query .=  $wing."','";
						$query .=  "Firma','";
						$query .=  $row[0]."','";
						$query .=  $usuarioPP."',";
						$query .=  "'C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS DE HISTORIA CLINICA (NOTAS) : ".mysql_errno().":".mysql_error());
						
						$query = " Update ".$empresa."_".$wformulario." set movtip='Nota', movdat='".$wcomen."', movcon=".$wultimo.", movusu='".$key."' where ".$empresa."_".$wformulario.".movpro='".$wformulario."' and ".$empresa."_".$wformulario.".movhis='".$whis."' and ".$empresa."_".$wformulario.".moving='".$wing."' and ".$empresa."_".$wformulario.".fecha_data='".$wfecha_data."'  and ".$empresa."_".$wformulario.".Hora_data='".$whora_data."' and ".$empresa."_".$wformulario.".movcon = 999 ";
						$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA (NOTAS) : ".mysql_errno().":".mysql_error());
						
						$query = " Update ".$empresa."_000036 set Firfir='on' where Firpro='".$wformulario."' and Firhis='".$whis."' and Firing='".$wing."' and fecha_data='".$wfecha_data."'  and Hora_data='".$whora_data."' and Firusu = '".$usuarioPP."' ";
						$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO HCE 36 : ".mysql_errno().":".mysql_error());
						
						$query = "insert ".$empresa."_000050 (medico, fecha_data, hora_data, fcopro, fcohis, fcoing, fcousu, fcodoc, Seguridad) values ('";
						$query .=  $empresa."','";
						$query .=  $wfecha_data."','";
						$query .=  $whora_data."','";
						$query .=  $wformulario."','";
						$query .=  $whis."','";
						$query .=  $wing."','";
						$query .=  $usuarioPP."','";
						$query .=  $key."',";
						$query .=  "'C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS DE RELACION DOCENTE - ESTUDIANTE (HCE 50) : ".mysql_errno().":".mysql_error());
						
						$query = " UNLOCK TABLES";
						$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());	
						echo "<table><tr><td id=tipoLGOK><IMG SRC='/matrix/images/medical/root/felizH.png'> NOTA NRo. ".$wultimo." GRABADA OK!!!!</td><tr></table>";
						unset($ok);
						unset($notas);
						$POK=1;
					}
					else
						echo "<table><tr><td id=tipoLGOL><IMG SRC='/matrix/images/medical/root/MALO.png'> ESTUDIANTE NO EXISTE O ESTA INACTIVO. CONSULTE CON SISTEMAS. LA NOTA NO HA SIDO GRABADA!!!!! </td><tr></table>";
				}
				else
					echo "<table><tr><td id=tipoLGOL><IMG SRC='/matrix/images/medical/root/MALO.png'> EXISTEN REGISTROS POSTERIORES A LA FIRMA DEL ESTUDIANTE POR FAVOR CONSULTE CON SISTEMAS O LAS EFERMERAS DE HISTORIA!!!!! </td><tr></table>";
			}
			else
				echo "<table><tr><td id=tipoLGOL><IMG SRC='/matrix/images/medical/root/MALO.png'> FIRMA ELECTRONICA ERRONEA. LA NOTA NO HA SIDO GRABADA!!!!! </td><tr></table>";
			if($POK == 0)
			{
				echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
				echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
				echo "<input type='HIDDEN' name= 'wfecha_data' value=".$wfecha_data.">";
				echo "<input type='HIDDEN' name= 'whora_data' value=".$whora_data.">";
				echo "<input type='HIDDEN' name= 'wformulario' value=".$wformulario.">";
				echo "<input type='HIDDEN' name= 'usuarioPP' value=".$usuarioPP.">";
				echo "<input type='HIDDEN' name= 'whis' value=".$whis.">";
				echo "<input type='HIDDEN' name= 'wing' value=".$wing.">";
				echo "<input type='HIDDEN' name= 'fec' value=".$fec.">";
				echo "<input type='HIDDEN' name= 'fechas[".$fec."][0]' value='".$fechas[$fec][0]."'>";
				echo "<input type='HIDDEN' name= 'fechas[".$fec."][1]' value='".$fechas[$fec][1]."'>";
				if(isset($notas))
					echo "<input type='HIDDEN' name= 'notas' value=".$notas.">";
			}
		}
		else
		{
			echo "<table><tr><td id=tipoLGOL><IMG SRC='/matrix/images/medical/root/MALO.png'> CONFIRMACION VACIA !!!!! </td><tr></table>";
			unset($ok);
			unset($notas);
		}
	}
	if(!isset($notas))
	{
		//                 0      1      2      3      4      5      6      7      8      9      10     11      12
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom, Ubiald from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and  pacced = oriced ";
		$query .= "   and  pactid = oritid ";
		$query .= "   and oriori = '".$wemp_pmla."'";
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
		// $ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
		// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		// $ann1=($aa - $ann)/360;
		// $meses=(($aa - $ann) % 360)/30;
		// if ($ann1<1)
		// {
			// $dias1=(($aa - $ann) % 360) % 30;
			// $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
		// }
		// else
		// {
			// $dias1=(($aa - $ann) % 360) % 30;
			// $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		// }
		$wedad = calcularEdadPaciente($row[4]);
		$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		$whis=$row[6];
		if(!isset($wing))
			$wing=$row[7];
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
		$wintitulo="Historia:".$row[6]." Ingreso:".$wing." Paciente:".$wpac;
		echo "<table border=1 width='1200'>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$wemp_pmla.".jpg'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>".$fechal."<input type='text' name='reloj' size='10' readonly='readonly' class=tipo3R></td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table><br><br>";
		
		$queryI = "";
		$fechas=array();
		$notas=1;
		$query = "SELECT Usualu from ".$empresa."_000020 ";
		$query .= " where Usucod = '".$key."' ";
		$query .= "   and Usuest = 'on' ";
		$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row2 = mysql_fetch_array($err2);
		$Estudiantes = $row2[0];
		
		$query = "select ".$empresa."_000036.Firpro from ".$empresa."_000036 ";
		$query .= " where ".$empresa."_000036.firhis='".$whis."' "; 
		$query .= "   and ".$empresa."_000036.firing='".$wing."' ";  
		$query .= " group by 1 ";
		$query .= " order by 1 ";
		$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			for ($j=0;$j<$num2;$j++)
			{
				$row2 = mysql_fetch_array($err2);
				if($j > 0)
					$queryI .= " UNION ALL ";
				//                                                 0                                   1                                 2                                3                                4                  5                             6   
				$queryI .= " select ".$empresa."_".$row2[0].".fecha_data,".$empresa."_".$row2[0].".Hora_data,".$empresa."_".$row2[0].".movcon,".$empresa."_".$row2[0].".movusu,".$empresa."_".$row2[0].".movpro, usuarios.Descripcion,".$empresa."_000001.Encdes from ".$empresa."_".$row2[0].", usuarios,".$empresa."_000001 ";
				$queryI .= " where ".$empresa."_".$row2[0].".movpro = '".$row2[0]."' ";
				$queryI .= "   and ".$empresa."_".$row2[0].".movhis = '".$whis."' ";
				$queryI .= "   and ".$empresa."_".$row2[0].".moving = '".$wing."' ";
				$queryI .= "   and ".$empresa."_".$row2[0].".movcon = 999 "; 
				$queryI .= "   and ".$empresa."_".$row2[0].".movusu = usuarios.Codigo "; 
				$queryI .= "   AND ".$empresa."_".$row2[0].".movpro = ".$empresa."_000001.Encpro ";
			}
			if($queryI != "")
			{
				$queryI .= "  order by 4,3,1,2 ";
				$err1 = mysql_query($queryI,$conex);
				$num1 = mysql_num_rows($err1);
			}
			else
				$num1=0;
		}
		
		
		if ($num1 > 0)
		{
			echo "<table border=0 width='1200'>";
			echo "<tr><td colspan=6 id=tipoN04>NOTAS CONFIRMATORIAS</td></tr>";
			echo "<tr><td colspan=6 id=tipoN04>Seleccione El Item Para Registrar La Nota</td></tr>";
			echo "<tr><td id=tipoN03>ITEM</td><td id=tipoN03>ESTUDIANTE</td><td id=tipoN03>REGISTRO CLINICO</td><td id=tipoN03>FECHA</td><td id=tipoN03>HORA</td><td id=tipoN03>SELECCIONE</td></tr>";
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if(strpos($Estudiantes,$row1[3]) !== false and $row1[3] != $key)
				{
					if($j % 2 == 0)
						$color="tipoN01";
					else
						$color="tipoN02";
					$fechas[$j][0]=$row1[0];
					$fechas[$j][1]=$row1[1];
					$fechas[$j][2]=$row1[4];
					$fechas[$j][3]=$row1[3];
					echo "<input type='HIDDEN' name= 'fechas[".$j."][0]' value='".$fechas[$j][0]."'>";
					echo "<input type='HIDDEN' name= 'fechas[".$j."][1]' value='".$fechas[$j][1]."'>";
					echo "<input type='HIDDEN' name= 'fechas[".$j."][2]' value='".$fechas[$j][2]."'>";
					echo "<input type='HIDDEN' name= 'fechas[".$j."][3]' value='".$fechas[$j][3]."'>";
					$k=$j+1;
					echo "<tr><td id=".$color.">".$k."</td><td id=".$color.">".$row1[5]."</td><td id=".$color.">".$row1[6]."</td><td id=".$color.">".$row1[0]."</td><td id=".$color.">".$row1[1]."</td><td id=".$color."><input type='RADIO' name='fec' value=".$j." OnClick='enter()'></td></tr>";
				}
			}
			echo "</table>";
			echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
			echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
			echo "<input type='HIDDEN' name= 'notas' value=".$notas.">";
		}
	}
	else
	{
		//                 0      1      2      3      4      5      6      7      8      9      10     11
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and  pacced = oriced ";
		$query .= "   and  pactid = oritid ";
		$query .= "   and oriori = '".$wemp_pmla."'";
		$query .= "   and inghis = orihis ";
		$query .= "   and  inging = oriing ";
		$query .= "   and ubihis = inghis "; 
		$query .= "   and ubiing = inging ";
		$query .= "   and ccocod = ubisac ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$wsex="M";
		$sexo="MASCULINO";
		if($row[5] == "F")
		{
			$sexo="FEMENINO";
			$wsex="F";
		}
		// $ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
		// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		// $ann1=($aa - $ann)/360;
		// $meses=(($aa - $ann) % 360)/30;
		// if ($ann1<1)
		// {
			// $dias1=(($aa - $ann) % 360) % 30;
			// $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
		// }
		// else
		// {
			// $dias1=(($aa - $ann) % 360) % 30;
			// $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		// }
		$wedad = calcularEdadPaciente($row[4]);
		$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
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
		$Hgraficas=" |";
		$whis=$row[6];
		if(!isset($wing))
			$wing=$row[7];
		
		echo "<table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$wemp_pmla.".jpg' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table>";
		
		echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
		echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
		if(isset($wfecha_data))
			echo "<input type='HIDDEN' name= 'wfecha_data' value=".$wfecha_data.">";
		else
			echo "<input type='HIDDEN' name= 'wfecha_data' value=".$fechas[$fec][0].">";
		if(isset($whora_data))
			echo "<input type='HIDDEN' name= 'whora_data' value=".$whora_data.">";
		else
			echo "<input type='HIDDEN' name= 'whora_data' value=".$fechas[$fec][1].">";
		if(isset($wformulario))
			echo "<input type='HIDDEN' name= 'wformulario' value=".$wformulario.">";
		else
			echo "<input type='HIDDEN' name= 'wformulario' value=".$fechas[$fec][2].">";
		if(isset($usuarioPP))
			echo "<input type='HIDDEN' name= 'usuarioPP' value=".$usuarioPP.">";
		else
			echo "<input type='HIDDEN' name= 'usuarioPP' value=".$fechas[$fec][3].">";
		echo "<input type='HIDDEN' name= 'whis' value=".$whis.">";
		echo "<input type='HIDDEN' name= 'wing' value=".$wing.">";
		echo "<input type='HIDDEN' name= 'fec' value=".$fec.">";
		echo "<input type='HIDDEN' name= 'fechas[".$fec."][0]' value='".$fechas[$fec][0]."'>";
		echo "<input type='HIDDEN' name= 'fechas[".$fec."][1]' value='".$fechas[$fec][1]."'>";
		echo "<input type='HIDDEN' name= 'notas' value=".$notas.">";
		
		if(isset($wformulario))
			echo "<input type='HIDDEN' name= 'wformulario' value=".$wformulario.">";
		else
			$wformulario = $fechas[$fec][2];
		
		if(!isset($usuarioPP))
			$usuarioPP = $fechas[$fec][3];
		echo "<input type='HIDDEN' name= 'usuarioPP' value=".$usuarioPP.">";
			
		$en="";
		$en .= "'".$wformulario."'";
		$CLASE="C";
		
		$queryI  = " select ".$empresa."_000002.Detdes,".$empresa."_".$wformulario.".movdat,".$empresa."_000002.Detorp,".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$wformulario.".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir from ".$empresa."_".$wformulario.",".$empresa."_000002,".$empresa."_000001 ";
		$queryI .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' "; 
		$queryI .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$fechas[$fec][0]."' "; 
		$queryI .= "   and ".$empresa."_".$wformulario.".hora_data  = '".$fechas[$fec][1]."' "; 
		$queryI .= "   and ".$empresa."_".$wformulario.".movpro=".$empresa."_000002.detpro ";
		$queryI .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.detcon ";
		//$queryI .= "   and ".$empresa."_000002.detest='on' "; 
		$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' "; 
		$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro "; 
		
		imprimir($conex,$empresa,$wdbmhos,$wemp_pmla,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,0);

		if(!isset($firma1))
			$firma1="";
		if(!isset($wcomen))
			$wcomen="";
		else
			$firma1="";
			
		echo "<table border=1 cellpadding=5 width='712' cellspacing=0 class=tipoTABLE>";
		echo "<tr><td colspan=8 id=tipoN05>NOTA CONFIRMATORIA</td></tr>";
		echo "<tr><td colspan=8 id=tipoN06><textarea name='wcomen' cols=80 rows=8 class=tipoN07>".$wcomen."</textarea></td></tr>";
		echo "<tr><td colspan=8 id=tipoN08 >Firma Digital : <input type='password' name='firma1' size=40 maxlength=80 id='firma1' value='".$firma1."' class=tipo3 OnBlur='javascript:firma();'></td></tr>";
		echo "<tr><td colspan=8 id=tipoN05>Datos OK?? <input type='checkbox' name='ok1'></td></tr>";
		echo "<tr><td colspan=8 align=center><input type='button' value='GRABAR'  onclick='enter()'></td></tr>";
		echo "</table>";
			

	}
}
?>
