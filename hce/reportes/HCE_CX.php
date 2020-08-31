<html>
<head>
  	<title>MATRIX Programa de Impresion de Historia Clinica Hospitalaria HCE</title>
	<link type='text/css' href='../procesos/HCE.css' rel='stylesheet'> 
<!--	<link rel="stylesheet" type="text/css" media="print" href="HCE.css" /> -->
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
    <script type='text/javascript' src='../procesos/HCE_Seguridad.js' ></script> 
    <script type="text/javascript">
    
	alturaPagina = 24/0.026458333;
    paginas = 0;
    restoPaginas = 0;
      
    /*********************************************************************************
     * Encuentra la posicion en X de un elemento
     *********************************************************************************/
     function paginar( campo, principal, data )
     {
          if( campo ){
               if( campo.tagName ){

                   //var cabecera = document.getElementById('hiPaciente').value;
                   var cabecera = "";

                    switch( campo.tagName )
                    {
                    
                        case 'TABLE':
                            var aux = document.createElement( "div" );
                            aux.innerHTML = "<table border=1 cellpadding=5 width='712' cellspacing=0 class=tipoTABLE></table>";

                            tabla = campo.cloneNode(true);
                            tabla = campo;

                            var sumaAltura = 3/0.026458333;
                            var formulario = "";
                            var data1 = "";
							
                            for( var i = 0; i < campo.rows.length; i++ )
                            {
								var paso = 0;
								
								if(campo.rows[i].cells[0].innerHTML.substring(0,2) == "F=")
								{
									//alert(campo.rows[i].cells[0].innerHTML);
									formulario = campo.rows[i].cells[0].innerHTML;
									paso=1;
								}
								posFila = findPosY( campo.rows[i] );

								sumaAltura = sumaAltura + campo.rows[i].clientHeight;
								
								posFila = posFila+campo.rows[i].clientHeight;

								if( sumaAltura > alturaPagina ){

									restoPaginas = restoPaginas+(alturaPagina+paginas*alturaPagina-posFila+campo.rows[i].clientHeight );
									paginas++;

									sumaAltura = campo.rows[i].clientHeight;
									if(paginas > 1)
									{
										data1 = data + " " + formulario;
										var aux2 = document.createElement( "div" );
										aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
										aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac>"+data1+"</td><td class=tipoPac1>P&aacute;gina: "+parseInt( paginas )+"</td></tr></table><br><br></a>"
										principal.appendChild( aux2.firstChild );
									}
									else
									{
										var aux2 = document.createElement( "div" );
										aux2.innerHTML = "<a><br><br></a>";
										aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac></td><td class=tipoPac1></td></tr></table><br><br></a>"
										principal.appendChild( aux2.firstChild );
									}
									
									principal.appendChild( aux.firstChild );

									aux.innerHTML = "<div class='saltopagina'></div>";
									principal.appendChild( aux.firstChild );

									aux.innerHTML = "<table border=1 cellpadding=5 width='712' cellspacing=0 class=tipoTABLE></table>";
								}
								
                                var fila = aux.firstChild.insertRow( aux.firstChild.rows.length );
                                var numCeldas = campo.rows[i].cells.length
                                for( var  j = 0; j < numCeldas ; j++){
									if(paso == 0)
									{
										fila.appendChild( tabla.rows[ i ].cells[0] );
									}
                                }
                            }

                            paginas++;
                            if(paginas > 1)
							{
								data1 = data + " " + formulario;
								var aux2 = document.createElement( "div" );
								aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
								aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac>"+data1+"</td><td class=tipoPac1>P&aacute;gina: "+parseInt( paginas )+"</td></tr></table><br><br></a>"
								principal.appendChild( aux2.firstChild );
							}
							else
							{
								var aux2 = document.createElement( "div" );
								aux2.innerHTML = "<a><br><br></a>";
								aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac></td><td class=tipoPac1></td></tr></table><br><br></a>"
								principal.appendChild( aux2.firstChild );
							}

                            campo.style.display = 'none';
                            //debugger;
                            //principal.removeChild(campo);
                            principal.appendChild( aux.firstChild );
                            
                         break;
                    }
               }
          }
     }
     
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
		//alert("entre a pintar divs");
		//alert("valores"+document.getElementById('Hgraficas').value);
		var elements = document.getElementsByTagName('img'); 
		
		if(document.getElementById('Hgraficas'))
		{
			var G = document.getElementById('Hgraficas').value;
			GT = G.split('|');
			nimg = elements.length -1;
			for(var x = 0; x < GT.length; x++)
			{ 
				var textG = "";
			    var varable = GT[x];
			    nximg = 0;
			    wsw = -1;
			    while (nximg <= nimg)
			    {
					if(elements[nximg].id.substring(0,1) == "G" && elements[nximg].id.substring(1) == x.toString())
					{
						wsw = nximg;
						//alert("Encontro : "+nximg+" "+x+" "+elements[nximg].id.substring(0,1)+" "+elements[nximg].id.substring(1));
					}
					nximg++;
				};
			    var ID = 1;
			    if(GT[x] != "" && wsw != -1)
			    {
					if(varable.length > 0 && elements[wsw].id.substring(0,1) == "G")
					{
						frag1 = varable.split('^');
						div=document.createElement('div');
						for (i=1;i<frag1.length;i++)
						{
							var div=document.createElement('div');
							frag2 = frag1[i].split('~');  
							div.id=frag2[0];
							document.HCE_Impresion.appendChild(div);
							posdivs(div,frag2[1],frag2[2],frag2[3],frag2[4],frag2[0],elements[wsw].id);
							textG = textG + frag2[0]+". "+frag2[5]+"<br>";
						}
					}
				}
		    }
	    }
      }

    </script>
</head>
<body onLoad= 'pintardivs();' BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.HCE_Impresion.submit();
	}
	
	function enterOK()
	{
		document.getElementById('ok').checked=true;
		document.forms.HCE_Impresion.submit();
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
	
	function salto1(titulo)
	{
		window.print();
	}
	
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

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


function validar_formulario($clave,$num,&$data)
{
	$numero=0;
	for ($i=0;$i<$num;$i++)
	{
		if(isset($data[$i][0]) and $clave == $data[$i][0] and $data[$i][5] == 0)
		{
			$numero++;
			$data[$i][5] = 1;
		}
	}
	if($numero > 0)
		return true;
	else
		return false;
}

include_once("hce/HCE_print_function.php");

/**********************************************************************************************************************  
	   PROGRAMA : HCE_Impresion.php
	   Fecha de Liberación : 2014-11-10
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2015-07-09
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar una impresion parametrizada de los registros
	   de Descripcion Operatoria de la Historia Clinica Electronica HCE.
	   
	   
	   REGISTRO DE MODIFICACIONES 
		.2015-07-09
			Se cambia la variable Detvim de on/off a (A/I/C/N)
	   	.2014-11-10
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_CX' action='HCE_CX.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'origen' value='".$origen."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'protocolos' value='".$protocolos."'>";
	echo "<input type='HIDDEN' name= 'CLASE' value='".$CLASE."'>";
	if(isset($BC))
		echo "<input type='HIDDEN' name= 'BC' value='".$BC."'>";
	if(isset($wing))
		echo "<input type='HIDDEN' name= 'wing' value='".$wing."'>";
	if(isset($wservicio))
		echo "<input type='HIDDEN' name= 'wservicio' value='".$wservicio."'>";
		
	$query = "select count(*) from root_000037 ";
	$query .= " where oriced = '".$wcedula."'";
	$query .= "   and oritid = '".$wtipodoc."'";
	$query .= "   and oriori = '".$origen."'";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	if($row[0] == 0)
	{
		echo "<center><table border=0>";
		echo "<tr><td id=tipoL09 colspan=".$span."><IMG SRC='/matrix/images/medical/HCE/Triste.png' style='vertical-align:middle;'>NO EXISTE INFORMACION EN LA HCE PARA ESTE PACIENTE</td></tr>";
		echo "</table></center>";
	}
	//                 0      1      2      3      4      5      6      7      8      9      10     11
	$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
	$query .= " where pacced = '".$wcedula."'";
	$query .= "   and pactid = '".$wtipodoc."'";
	$query .= "   and pacced = oriced ";
	$query .= "   and pactid = oritid ";
	$query .= "   and oriori = '".$origen."'";
	$query .= "   and inghis = orihis ";
	$query .= "   and inging = oriing ";
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
		$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
	}
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
	if(!isset($wing))
		$wintitulo="Historia:".$row[6]." Ingreso:".$row[7]." Paciente:".$wpac;
	else
		$wintitulo="Historia:".$row[6]." Ingreso:".$wing." Paciente:".$wpac;
	$Hgraficas=" |";
	$en = "'000077'";
	echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
	echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
	echo "<center><table border=1 width='712' class=tipoTABLE1>";
	echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$origen.".jpg' id='logo'></td>";	
	echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
	echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
	echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
	echo "</table></center><br>";
	//                                        0                           1                          2                             3                             4                           5                          6                          7                         8                          9                          10                        11                          12                         13                         14                         15                         16                         17                         18                         19
	$queryI  = " select ".$empresa."_000002.Detdes,".$empresa."_000077.movdat,".$empresa."_000002.Detorp,".$empresa."_000077.fecha_data,".$empresa."_000077.Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_000077.movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir,".$empresa."_000002.Detimc,".$empresa."_000002.Detccu from ".$empresa."_000077,".$empresa."_000002,".$empresa."_000001 ";
	$queryI .= " where ".$empresa."_000077.movpro = '000077' "; 
	$queryI .= "   and ".$empresa."_000077.movhis = '".$whis."' ";
	$queryI .= "   and ".$empresa."_000077.movpro = ".$empresa."_000002.detpro ";
	$queryI .= "   and ".$empresa."_000077.movcon = ".$empresa."_000002.detcon ";
	$queryI .= "   and ".$empresa."_000002.detvim in ('A','C') "; 
	$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' "; 
	$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro "; 
	if($CLASE == "C")
		imprimir($conex,$empresa,$wdbmhos,$origen,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,0);
						
}
?>
