<html>
<head>
  	<title>MATRIX Programa de Impresion de Historia Clinica Hospitalaria HCE</title>
	<link type='text/css' href='HCE.css' rel='stylesheet'> 
<!--	<link rel="stylesheet" type="text/css" media="print" href="HCE.css" /> -->
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
<!-- Loading Calendar JavaScript files -->
	<style>
	.saltopagina{page-break-after: always}
	</style>
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    <script type="text/javascript">
    
	document.onkeydown = mykeyhandler; 
	function mykeyhandler(event) 
	{
		 //keyCode 116 = F5
		 //keyCode 122 = F11
		 //keyCode 8 = Backspace
		 //keyCode 37 = LEFT ROW
		 //keyCode 78 = N
		 //keyCode 39 = RIGHT ROW
		 //keyCode 67 = C
		 //keyCode 86 = V
		 //keyCode 85 = U
		 //keyCode 45 = Insert
			
		
		 event = event || window.event;
		 if (navigator.appName == "Netscape")
		 {
			 var tgt = event.target || event.srcElement;
			 if ((event.ctrlKey && event.which==37) || (event.ctrlKey && event.which==39) ||
				 (event.ctrlKey && event.which==78) || (event.ctrlKey && event.which==67) ||
				 (event.ctrlKey && event.which==86) || (event.ctrlKey && event.which==85) ||
				 (event.ctrlKey && event.which==45) || (event.ctrlKey && event.which==45))
			 {
				 event.cancelBubble = true;
				 event.returnValue = false;
				 alert("Funcion no permitida");
				 return false;
			 }

			if(event.which==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
			{
				return false;
			}
			
			if (event.which == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
			{
				return false;
			}
			
			if ((event.which == 116) || (event.which == 122)) 
			{
				return false;
			}
		 }
		 else
		 {
			 var tgt = event.target || event.srcElement;
			 if((event.altKey && event.keyCode==37) || (event.altKey && event.keyCode==39) ||
			 (event.ctrlKey && event.keyCode==78)|| (event.ctrlKey && event.keyCode==67)||
			 (event.ctrlKey && event.keyCode==86)|| (event.ctrlKey && event.keyCode==85)||
			 (event.ctrlKey && event.keyCode==45)|| (event.shiftKey && event.keyCode==45))
			 {
				 event.cancelBubble = true;
				 event.returnValue = false;
				 alert("Funcion no permitida");
				 return false;
			 }

			if(event.keyCode==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
			{
				return false;
			}
			
			if (event.keyCode == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
			{
				return false;
			}
			
			if ((event.keyCode == 116) || (event.keyCode == 122)) 
			{
				if (navigator.appName == "Microsoft Internet Explorer")
				{
					window.event.keyCode=0;
				}
				return false;
			}
		}
	}

	function mouseDown(e) 
	{
		var ctrlPressed=0;
		var altPressed=0;
		var shiftPressed=0;
		if (parseInt(navigator.appVersion)>3) 
		{
			if (navigator.appName=="Netscape") 
			{
				var mString =(e.modifiers+32).toString(2).substring(3,6);
				shiftPressed=(mString.charAt(0)=="1");
				ctrlPressed =(mString.charAt(1)=="1");
				altPressed =(mString.charAt(2)=="1");
				self.status="modifiers="+e.modifiers+" ("+mString+")"
			}
			else
			{
				shiftPressed=event.shiftKey;
				altPressed =event.altKey;
				ctrlPressed =event.ctrlKey;
			}
			if (shiftPressed || altPressed || ctrlPressed)
			alert ("Función no permitida");
		}
		return true;
	}

	if (parseInt(navigator.appVersion)>3) 
	{
		document.onmousedown = mouseDown;
		if (navigator.appName=="Netscape")
		document.captureEvents(Event.MOUSEDOWN);
	}

	var message="";

	function clickIE() 
	{
		if (document.all)
		{
			(message);
			return false;
		}
	}

	function clickNS(e) 
	{
		if(document.layers||(document.getElementById&&!document.all)) 
		{
			if (e.which==2||e.which==3) 
			{
				(message);return false;
			}
		}
	}

	if (document.layers)
	{
		document.captureEvents(Event.MOUSEDOWN);
		document.onmousedown=clickNS;
	}
	else
	{
		document.onmouseup=clickNS;document.oncontextmenu=clickIE;
	}

	document.oncontextmenu=new Function("return false");

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

                            for( var i = 0; i < campo.rows.length; i++ )
                            {

								posFila = findPosY( campo.rows[i] );

								sumaAltura = sumaAltura + campo.rows[i].clientHeight;
								
								posFila = posFila+campo.rows[i].clientHeight;

								if( sumaAltura > alturaPagina ){

									restoPaginas = restoPaginas+(alturaPagina+paginas*alturaPagina-posFila+campo.rows[i].clientHeight );
									paginas++;

									sumaAltura = campo.rows[i].clientHeight;
									if(paginas > 1)
									{
										var aux2 = document.createElement( "div" );
										aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
										aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac>"+data+"</td><td class=tipoPac1>P&aacute;gina: "+parseInt( paginas )+"</td></tr></table><br><br></a>"
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
                                    fila.appendChild( tabla.rows[ i ].cells[0] );
                                }
                            }

                            paginas++;
                            if(paginas > 1)
							{
								var aux2 = document.createElement( "div" );
								aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
								aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac>"+data+"</td><td class=tipoPac1>P&aacute;gina: "+parseInt( paginas )+"</td></tr></table><br><br></a>"
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

    </script>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.HCE_Impresion.submit();
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
		//alert("Entre "+titulo);
		//document.title = titulo;
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
	   PROGRAMA : HCE_Impresion.php
	   Fecha de Liberación : 2010-06-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2011-03-15
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar una impresion parametrizada de los registros
	   de la Historia Clinica Electronica HCE.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	    .2011-03-15
			Ultima Version Beta.
			
	    .2011-02-24
			Ultima Version Beta.
			
	    .2011-02-22
			Ultima Version Beta.
			
	    .2011-02-14
			Ultima Version Beta.
			
	   	.2010-06-01
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_Impresion' action='HCE_Impresion.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($ok))
	{
		//                 0      1      2      3      4      5      6      7      8      9      10     11
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,movhos_000016,movhos_000018,movhos_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and  pacced = oriced ";
		$query .= "   and  pactid = oritid ";
		$query .= "   and oriori = '01' ";
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
			$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		}
		$wpac = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		$color="#dddddd";
		$color1="#C3D9FF";
		$color2="#E8EEF7";
		$color3="#CC99FF";
		$color4="#99CCFF";
		echo "<center><table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/HCE/clinica.png' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=5 id=tipoL04>".$wpac."</td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-".$row[7]."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table></center><br>";
		
		if(!isset($wfechai))
		{
			$wfechai=date("Y-m-d");
			$wfechaf=date("Y-m-d");
		}
		echo "<table border=0 align=center>";
		echo "<tr><td id=tipoTI01 colspan=8>IMPRESION HISTORIA CLINICA ELECTRONICA Ver. 2011-03-15</td></tr>";
		echo "<tr><td id=tipoTI05 colspan=8>Fecha Inicial <input type='TEXT' name='wfechai' size=10 maxlength=10 id='wfechai' readonly='readonly' value=".$wfechai." class=tipo6>&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfechai',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "&nbsp;&nbsp;&nbsp;Fecha Final <input type='TEXT' name='wfechaf' size=10 maxlength=10 id='wfechaf' readonly='readonly' value=".$wfechaf." class=tipo6>&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfechaf',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "</td></tr>";
		echo "<tr><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td></tr>";
 		echo "<tr><td id=tipoTI05 colspan=8>Marcar Todos<input type='checkbox' name='all'  onclick='enter()'></td></tr>";
		if(!isset($dta))
 		{
			$vistas=array();
			$numvistas=0;
			$query  = "select hce_000021.Rararb from hce_000020,hce_000021,hce_000009 ";
			$query .= "   where hce_000020.Usucod = '".$key."' ";
			$query .= " 	and hce_000020.Usurol = hce_000021.rarcod ";
			$query .= " 	and hce_000021.rararb = hce_000009.precod "; 
			$query .= "   order by 1";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$vistas[$i] = $row[0];
				}
			}
			$numvistas=$num;
				
	 		$dta=0;
			$query = "select ".$empresa."_000009.Precod,".$empresa."_000009.Preurl,".$empresa."_000009.Predes,".$empresa."_000009.prenod from ".$empresa."_000009"; 
			$query .= " where ".$empresa."_000009.prenod = 'on' ";
			$query .= "   and ".$empresa."_000009.preest = 'on' ";
			$query .= " union all ";
			$query .= " select ".$empresa."_000009.Precod,".$empresa."_000009.Preurl,".$empresa."_000009.Predes,".$empresa."_000009.prenod from ".$empresa."_000009 ";
			$query .= " where ".$empresa."_000009.prenod = 'off' ";
			$query .= "   and mid(".$empresa."_000009.Preurl,1,1) = 'F' ";
			$query .= "   and ".$empresa."_000009.preest = 'on' ";
			$query .= " order by 1 ";
			
			/*
			$query = "select Preurl, Predes, Precod from  ".$empresa."_000009 ";
			$query .= " where Prenod = 'off' ";
			$query .= "   and Preest = 'on' ";
			$query .= "   and SUBSTRING(Preurl,1,1) = 'F' ";
			$query .= "   Order by 1 ";
			*/
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$fil=ceil($num / 4);
				$data=array();
				$dta=1;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$data[$i][0]=$row[1];
					$data[$i][1]=$row[2];
					$data[$i][2]=$row[3];
					$pos=bi($vistas,$numvistas,$row[0]);
					if($pos != -1)
						$data[$i][3]=1;
					else
						$data[$i][3]=0;
				}
			}
		}
		if($dta == 1)
		{
			for ($i=0;$i<$fil;$i++)
			{
				echo "<tr>";
				for ($j=0;$j<4;$j++)
				{
					$exp=$i+($fil*$j);
					if(isset($data[$exp][0]))
					{
						if($data[$exp][2] == "off")
						{
							$color="tipoTI04";
							if($data[$exp][3] == 1)
							{
								if(isset($imp[$exp]) or isset($all))
									echo "<td id=".$color."><input type='checkbox' name='imp[".$exp."]' checked></td>";
								else
									echo "<td id=".$color."><input type='checkbox' name='imp[".$exp."]'></td>";
							}
							else
								echo "<td id=".$color."></td>";
							echo "<td id=".$color.">".$data[$exp][1]."</td>";
							echo "<input type='HIDDEN' name= 'data[".$exp."][0]' value=".$data[$exp][0].">";
							echo "<input type='HIDDEN' name= 'data[".$exp."][1]' value=".$data[$exp][1].">";
						}
						else
						{
							$color="tipoTI03";
							echo "<td id=".$color."></td>";
							echo "<td id=".$color.">".$data[$exp][1]."</td>";
						}
					}
					else
					{
						$color="tipoTI04";
						echo "<td id=".$color."></td>";
						echo "<td id=".$color."></td>";
					}
				}
				echo "</tr>";
			}
			echo "<input type='HIDDEN' name= 'num' value=".$num.">";
			echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
			echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";			
			echo "<tr><td id=tipoTI05 colspan=8>IMPRIMIR<input type='checkbox' name='ok'></font></td></tr>";
			echo "<tr><td id=tipoTI01 colspan=8><input type='submit' value='ENTER'></td></tr>"; 
			echo "</table><br><br>"; 
		}
	}
	else
	{
		//                 0      1      2      3      4      5      6      7      8      9      10     11
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,movhos_000016,movhos_000018,movhos_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and  pacced = oriced ";
		$query .= "   and  pactid = oritid ";
		$query .= "   and oriori = '01' ";
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
			$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		}
		$wpac = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		$whis=$row[6];
		if(!isset($wing))
			$wing="N ".$row[7];
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
		echo "<table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/HCE/clinica.png' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table>";
		//                 0      1   
		/*
		$query = "select Orihis,Oriing from root_000036,root_000037 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and  pacced = oriced ";
		$query .= "   and  pactid = oritid ";
		$query .= "   and oriori = '01' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		*/
		
		
		$en="";
		$queryI="";
		$nrofor=-1;
		for ($i=0;$i<$num;$i++)
		{
			if(isset($imp[$i]))
			{
				$nrofor++;
				if($nrofor > 0)
					$en .= ",";
				$en .= "'".substr($data[$i][0],2)."'";
				if($nrofor > 0)
					$queryI .= " UNION ALL ";
				//                                        0                                              1                          2                                                3                                                  4                           5                          6                           7                          8                          9    
				$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".substr($data[$i][0],2).".movdat,".$empresa."_000002.Detorp,".$empresa."_".substr($data[$i][0],2).".fecha_data,".$empresa."_".substr($data[$i][0],2).".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc from ".$empresa."_".substr($data[$i][0],2).",".$empresa."_000002,".$empresa."_000001 ";
				$queryI .= " where ".$empresa."_".substr($data[$i][0],2).".movpro='".substr($data[$i][0],2)."' ";
				$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movhis='".$whis."' ";
				$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".moving='".$wing."' ";
				$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".fecha_data between '".$wfechai."' and '".$wfechaf."' "; 
				$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movpro=".$empresa."_000002.detpro ";
				$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movcon = ".$empresa."_000002.detcon ";
				$queryI .= "   and ".$empresa."_000002.detest='on' "; 
				$queryI .= "   and ".$empresa."_000002.Dettip not in ('Titulo','Subtitulo') "; 
				$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro "; 
			}
		}
		if($queryI != "")
		{
			$queryI .= "  order by 4,5,8,3 ";
			$titulos=array();
			//                                      0                           1                         2  
			$query  = "select ".$empresa."_000002.Detorp,".$empresa."_000002.Detdes,".$empresa."_000002.detpro from ".$empresa."_000002 ";
			$query .= " where ".$empresa."_000002.detpro in (".$en.") ";
			$query .= "   and ".$empresa."_000002.dettip = 'Titulo' "; 
			$query .= "   and ".$empresa."_000002.detest='on' ";  
			$query .= "  order by 3,1 ";
			$err1 = mysql_query($query,$conex);
			$numt = mysql_num_rows($err1);
			if ($numt>0)
			{
				for ($j=0;$j<$numt;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					$titulos[$j][0]=$row1[0];
					$titulos[$j][1]=$row1[1];
					$titulos[$j][2]=$row1[2];
					$titulos[$j][3]=0;
				}
			}
			$filanterior=0;
			$spana=0;
			$kcolor=0;
			$tcolor=1;
			$wfor="";
			$wforant="";
			$wfecant="";
			$whorant="";
			$numimg=0;
			$wsimagenes="";
			echo "<table border=1 cellpadding=5 id='mitablita' width='712' cellspacing=0 class=tipoTABLE>";
			$err1 = mysql_query($queryI,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num1>0)
			{
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					if($wfor != $row1[7].$row1[3].$row1[4])
					{
						$datfirma="";
						if($wforant != "")
						{
							//                                                 0                                   1                   2                                  3                                 4
							$queryJ  = " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,usuarios.Descripcion,".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat from ".$empresa."_".$wforant.",".$empresa."_000020, usuarios ";
							$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
							$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
							$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
							$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
							$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
							$queryJ .= "   and ".$empresa."_".$wforant.".movcon = 1000 ";
							$queryJ .= "   and ".$empresa."_".$wforant.".movdat = ".$empresa."_000020.Usucla ";
							$queryJ .= "   and ".$empresa."_000020.Usucod = usuarios.Codigo ";
							$queryJ .= "  UNION ALL ";
							$queryJ .= " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,'',".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat from ".$empresa."_".$wforant." ";
							$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
							$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
							$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
							$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
							$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
							$queryJ .= "   and ".$empresa."_".$wforant.".movcon > 1000 ";
							$queryJ .= "  order by 3 ";
							$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
							$num = mysql_num_rows($err);
							if($num > 0)
							{	
								$notas=array();
								$kn=-1;
								for ($h=0;$h<$num;$h++)
								{
									$row = mysql_fetch_array($err);
									if($row[3] == 1000)
										$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$row[2]." Fecha : ".$row[0]." Hora : ".$row[1];
									else
									{
										$kn++;
										$notas[$kn]=$row[4];
									}
								}
							}
						}
						for ($z=0;$z<$numt;$z++)
							$titulos[$z][3]=0;
						$wfor=$row1[7].$row1[3].$row1[4];
						$wforant=$row1[7];
						$wfecant=$row1[3];
						$whorant=$row1[4];
					}
					if($filanterior > $row1[2])
					{
						if($spana < 8)
						{
							$spanf=8 - $spana;
							$WIDTH=$spanf*89;
							echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
						}
						if($datfirma != "")
						{
							$WIDTH=8*89;
							echo "<tr><td  colspan=8 id=tipoIL02H width='".$WIDTH."'>".$datfirma."</td></tr>";
							for ($h=0;$h<count($notas);$h++)
								echo "<tr><td  colspan=8 id=tipoIL02I width='".$WIDTH."'>NOTA REALIZADA EN : ".$notas[$h]."</td></tr>";
						}
						else
						{
							$WIDTH=8*89;
							echo "<tr><td  colspan=8 id=tipoIL02N width='".$WIDTH."'>SIN FIRMAR</td></tr>";
						}
						$WIDTH=8*89;
						echo "<tr><td id=tipoIL02Y colspan=8 width='".$WIDTH."'>&nbsp;</td></tr>";
						echo "<tr><td id=tipoIL02U colspan=8 width='".$WIDTH."'><b>*** ".strtoupper(htmlentities($row1[8]))." ***</b></td></tr>";
						$spana = 0;
					}elseif($filanterior == 0)
						{
							$WIDTH=8*89;
							echo "<tr><td id=tipoIL02U colspan=8 width='".$WIDTH."'><b>*** ".strtoupper(htmlentities($row1[8]))." ***</b></td></tr>";
						}
					if(strlen($row1[1]) > 0)
						$sit=buscartitulo(&$titulos,$row1[2],$numt,$row1[7]);
					else
						$sit=-1;
					if($sit > -1)
					{
						if($spana < 8)
						{
							$spanf=8 - $spana;
							$WIDTH=$spanf*89;
							echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
						}
						$WIDTH=8*89;
						echo "<tr><td id=tipoIL02Z colspan=8 width='".$WIDTH."'><b><i>".htmlentities($titulos[$sit][1])."</i></b></td></tr>";
						if($kcolor % 2 == 0)
							$tcolor="1";
						else
							$tcolor="2";
						$kcolor++;
						$spana = 0;
					}
					$filanterior=$row1[2];
					if($row1[5] == "Seleccion" and $row1[1] == "undefined")
						$row1[1] = "";
					if(strlen($row1[1]) > 0 and $row1[5] != "Label" and $row1[5] != "Link")
					{
						if($row1[5] == "Booleano")
							if($row1[1] == "CHECKED")
								$row1[1] = "SI";
							else
								$row1[1] = "NO";
						if($row1[5] == "Tabla")
						{
							$def="";
							$tablas=explode("<option value=",$row1[1]);
							for ($z=1;$z<count($tablas);$z++)
							{
								$tablas[$z]=str_replace("="," ",$tablas[$z]);
								$tablas[$z]=substr($tablas[$z],0,strpos($tablas[$z],"."));
								
								$fields=explode("-",$tablas[$z]);
								$def .= "(".$z.") ";
								if($fields[0] == "P")
									$def .= "Primario ";
								else
									$def .= "Secundario ";
								if($fields[1] == "C")
									$def .= "Confirmado ";
								else
									$def .= "Presuntivo ";
								for ($z1=2;$z1<count($fields);$z1++)
								{
									$def .= $fields[$z1];
								}
								$def .= "   ";
							}
							$row1[1]=$def;
						}
						if($row1[5] == "Formula")
							$wsstring = "<b>".htmlentities($row1[0])."</b> : ".number_format((double)$row1[1],2,'.',',')." ";
						elseif($row1[5] == "Seleccion")
								$wsstring = "<b>".htmlentities($row1[0])."</b> : ".ver(htmlentities($row1[1]))." ";
							else
								$wsstring = "".htmlentities($row1[0])." :<br><b>".htmlentities($row1[1])."</b> ";
						$spanw=calcularspan($wsstring);
						if($spanw == 1)
							if($row1[5] == "Formula")
								$wsstring = "".htmlentities($row1[0])." :<br><b>".number_format((double)$row1[1],2,'.',',')."</b> ";
							elseif($row1[5] == "Seleccion")
									$wsstring = "".htmlentities($row1[0])." :<br><b>".ver(htmlentities($row1[1]))."</b> ";
								else
									$wsstring = "".htmlentities($row1[0])." :<br><b>".htmlentities($row1[1])."</b> ";
						if($row1[5] == "Imagen")
						{
							$TEXTO=explode("^",$row1[1]);
							$TEXTOD="";
							for ($zx=0;$zx<count($TEXTO);$zx++)
							{
								if($TEXTO[$zx] != "")
								{
									$TEXTOV=explode("~",$TEXTO[$zx]);
									$TEXTOD .= "<BR>".$zx.":".$TEXTOV[5];
								}
							}
							//echo "<input type='HIDDEN' name= 'Hgraficas' id='Hgraficas' value='".$Hgraficas."'>";
							$wsstring = "IMAGEN: ".htmlentities($row1[0])." <br>INFORMACION IMAGEN:<BR>".$TEXTOD."";
							$spanw=8;
						}	
						if(($spana + $spanw) < 9)
						{
							if($spana == 0)
							{
								if($spanw == 1)
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02J".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
								else
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
							}
							else
							{
								if($spanw == 1)
								{
									$WIDTH=$spanw*89;
									echo "<td colspan=".$spanw." id=tipoIL02J".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
								else
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
							}
							$spana += $spanw;
						}
						else
						{
							if($spana == 0)
							{
								if($spanw == 1)
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02J".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
								else
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
							}
							else
							{
								if($spana < 8)
								{
									$spanf=8 - $spana;
									$WIDTH=$spanf*89;
									echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
								}
								if($kcolor % 2 == 0)
									$tcolor="1";
								else
									$tcolor="2";
								$kcolor++;
								if($spanw == 1)
								{
									$WIDTH=$spanw*89;
									echo "</tr><tr><td colspan=".$spanw." id=tipoIL02J".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
								else
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
							}	
							$spana = $spanw;
						}
					}
				}
				echo "<input type='HIDDEN' name= 'Hgraficas' id='Hgraficas' value='".$Hgraficas."'>";
				if($spana < 8)
				{
					$spanf=8 - $spana;
					$WIDTH=$spanf*89;
					echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
				}
				if($wforant != "")
				{
					$datfirma="";
					$queryJ  = " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,usuarios.Descripcion,".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat from ".$empresa."_".$wforant.",".$empresa."_000020, usuarios ";
					$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
					$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".movcon = 1000 ";
					$queryJ .= "   and ".$empresa."_".$wforant.".movdat = ".$empresa."_000020.Usucla ";
					$queryJ .= "   and ".$empresa."_000020.Usucod = usuarios.Codigo ";
					$queryJ .= "  UNION ALL ";
					$queryJ .= " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,'',".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat from ".$empresa."_".$wforant." ";
					$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
					$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".movcon > 1000 ";
					$queryJ .= "  order by 3 ";
					$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if($num > 0)
					{
						$notas=array();
						$kn=-1;
						for ($h=0;$h<$num;$h++)
						{
							$row = mysql_fetch_array($err);
							if($row[3] == 1000)
								$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$row[2]." Fecha : ".$row[0]." Hora : ".$row[1];
							else
							{
								$kn++;
								$notas[$kn]=$row[4];
							}
						}
					}
				}
				if($datfirma != "")
				{
					$WIDTH=8*89;
					echo "<tr><td  colspan=8 id=tipoIL02H width='".$WIDTH."'>".$datfirma."</td></tr>";
					for ($h=0;$h<count($notas);$h++)
						echo "<tr><td  colspan=8 id=tipoIL02I width='".$WIDTH."'>NOTA REALIZADA EN : ".$notas[$h]."</td></tr>";
				}
				else
				{
					$WIDTH=8*89;
					echo "<tr><td  colspan=8 id=tipoIL02N width='".$WIDTH."'>SIN FIRMAR</td></tr>";
				}
			}
			
			$WIDTH=8*89;
			echo "<tr><td colspan=8 id=tipoIL02Q width='".$WIDTH."'><input type='button' value='FIN IMPRESION' class=tipoFinI onclick='salto1(\"".$wintitulo."\")'></td></tr>";
			echo "</table>";
			$wintitulo .= "&nbsp;&nbsp;".date("Y-m-d")."&nbsp;&nbsp;".(string)date("H:i:s");
			echo '<script language="Javascript">';
			echo '	   paginar(document.getElementById("mitablita"),document.forms[0],"'.$wintitulo.'");';
			echo '</script>';
		}
		else
		{
			echo "<center><table border=0 aling=center><tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' id='cabeza'></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>NO SELECCIONO FORMULARIOS PARA IMPRESION !!!</MARQUEE></FONT>";
		}
	}
}
?>
