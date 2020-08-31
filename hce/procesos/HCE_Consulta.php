<html>
<head>
  	<title>MATRIX Programa de Consulta de Historia Clinica Hospitalaria HCE</title>
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
    <script type='text/javascript' src='HCE_Seguridad.js' ></script>
    <script type="text/javascript">
     
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
			//alert('Valor de las Graficas : '+G+" "+elements.length);
			GT = G.split('|');
			for(var x = 0; x < GT.length; x++)
			{ 
				var textG = "";
			    var varable = GT[x];
			    //alert(elements[x].id);
			    var ID = 1;
			    if(GT[x] != "")
			    {
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
							document.HCE_Consulta.appendChild(div);
							posdivs(div,frag2[1],frag2[2],frag2[3],frag2[4],frag2[0],elements[x].id);
							textG = textG + frag2[0]+". "+frag2[5]+"<br>";
						}
					}
				}
			   	//var divt=document.createElement('div');
				//document.HCE_Impresion.appendChild(divt);
				//divt.style.position = "absolute";
				//divt.style.top = parseInt(findPosY(elements[x]))+"px";
				//divt.style.left = (parseInt(findPosX(elements[x]))+parseInt(elements[x].offsetWidth)+10)+"px";
				//divt.innerHTML="<font size=2em>"+textG+"</font>"; 
				//alert(textG+" "+divt.style.top+" "+divt.style.left+elements[x].id);
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
		document.forms.HCE_Consulta.submit();
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
	   PROGRAMA : HCE_Consulta.php
	   Fecha de Liberación : 2010-06-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2012-12-17
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar una consulta parametrizada de los registros
	   de la Historia Clinica Electronica HCE.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	     .2012-12-17
              Se empaqueto la seguridad contra copy+paste en el archivo HCE_Seguridad.js
              
         .2012-01-17
              Se adiciona en el programa un cast a entero  para la seleccion de los ingresos de la tabla 36 de HCE.
 
		.2012-01-06
			Se corrige el programa ya que la validacion de la clave no tenia en cuenta el usuario y se presentan inconsistencias
			cuando dos usuarios tienen la misma clave.
		
	    .2011-07-19
			Se adiciona al programa una consulta a la tabla 36 de formularios firmados para dar la posibilidad de consulta a
			datos historicos y acceso a la tabla 16 de movhos para inicializar la fecha de ingreso del paciente.
			
		.2011-05-10
			Se homologa la visualizacion de la consulta con la impresion de la Historia Clinica.
			
	    .2011-03-18
			Se cambio la palabra impresion x Consulta. y se colocaron los datos del paciente en la selección de formularios.
			
	    .2011-02-11
			Ultima Version Beta.
			
	   	.2010-06-01
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!@session_is_registered("user"))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_Consulta' action='HCE_Consulta.php' method=post>";
	

	

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
		//if(!isset($wing))
		//	$wing=$row[7];
		$color="#dddddd";
		$color1="#C3D9FF";
		$color2="#E8EEF7";
		$color3="#CC99FF";
		$color4="#99CCFF";
		echo "<center><table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/HCE/clinica.png' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=5 id=tipoL04>".$wpac."</td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-";
		$query = "select cast(Firing as UNSIGNED) from hce_000036 ";
		$query .= "  where hce_000036.Firhis='".$row[6]."'";
		$query .= " group by 1 ";
		$query .= " order by 1 desc ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			echo "<select name='wing' OnChange='enter()'>";
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if(isset($wing) and $wing == $row1[0])
					echo "<option selected>".$row1[0]."</option>";
				else
					echo "<option>".$row1[0]."</option>";
			}
			echo "</select>";
		}
		else
		{
			$wing=$row[7];
			echo $wing;
			echo "<input type='hidden' id='wing' value='".$wing."'>";
		}
		echo "</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table></center><br>";
		if(!isset($wing))
			$wing=$row[7];
		$query = "select Fecha_data from movhos_000016 ";
		$query .= "  where Inghis='".$row[6]."'";
		$query .= "    and Inging='".$wing."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			$wfechai=$row1[0];
		}
		else
		{
			$wfechai=date("Y-m-d");
		}
		$wfechaf=date("Y-m-d");
		echo "<table border=0 align=center>";
		echo "<tr><td id=tipoTI01 colspan=8>CONSULTA HISTORIA CLINICA ELECTRONICA Ver. 2012-12-17</td></tr>";
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
			echo "<tr><td id=tipoTI05 colspan=8>CONSULTAR<input type='checkbox' name='ok'></font></td></tr>";
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
		echo "<table border=1 width='1200'>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/HCE/clinica.png' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>".$fechal."<input type='text' name='reloj' size='10' readonly='readonly' class=tipo3R></td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table><br>";
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
				//                                        0                                              1                          2                                                3                                                  4                           5                          6                           7                          8                          9                         10                         11                                             12                         13                         14                         15                         16                        17
				$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".substr($data[$i][0],2).".movdat,".$empresa."_000002.Detorp,".$empresa."_".substr($data[$i][0],2).".fecha_data,".$empresa."_".substr($data[$i][0],2).".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".substr($data[$i][0],2).".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir  from ".$empresa."_".substr($data[$i][0],2).",".$empresa."_000002,".$empresa."_000001 ";
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
			echo "<table border=1 cellpadding=5 id='mitablita' width='1200' cellspacing=0 class=tipoTABLE>";
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
							$queryJ .= "   and ".$empresa."_".$wforant.".movusu = ".$empresa."_000020.Usucod ";
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
							$WIDTH=$spanf*94;
							echo "<td colspan=".$spanf." id=tipoL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
						}
						if($datfirma != "")
						{
							$WIDTH=8*94;
							echo "<tr><td  colspan=8 id=tipoL02H width='".$WIDTH."'>".$datfirma."</td></tr>";
							for ($h=0;$h<count($notas);$h++)
								echo "<tr><td  colspan=8 id=tipoL02I width='".$WIDTH."'>NOTA REALIZADA EN : ".$notas[$h]."</td></tr>";
						}
						else
						{
							$WIDTH=8*94;
							echo "<tr><td  colspan=8 id=tipoL02N width='".$WIDTH."'>SIN FIRMAR</td></tr>";
						}
						$WIDTH=8*94;
						echo "<tr><td id=tipoL02Y colspan=8 width='".$WIDTH."'>&nbsp;</td></tr>";
						echo "<tr><td id=tipoL02U colspan=8 width='".$WIDTH."'><b><u>".htmlentities($row1[8])."</u></b></td></tr>";
						$spana = 0;
					}elseif($filanterior == 0)
						{
							$WIDTH=8*94;
							echo "<tr><td id=tipoL02U colspan=8 width='".$WIDTH."'><b><u>".htmlentities($row1[8])."</u></b></td></tr>";
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
							$WIDTH=$spanf*94;
							echo "<td colspan=".$spanf." id=tipoL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
						}
						$WIDTH=8*94;
						echo "<tr><td id=tipoL02Z colspan=8 width='".$WIDTH."'><b><i>".htmlentities($titulos[$sit][1])."</i></b></td></tr>";
						if($kcolor % 2 == 0)
							$tcolor="1";
						else
							$tcolor="2";
						$kcolor++;
						$spana = 0;
					}
					$filanterior=$row1[2];
					if(strlen($row1[1]) > 0 and $row1[5] != "Label" and $row1[5] != "Link")
					{
						$dospuntos=":";
						if($row1[11] == "off")
						{
							$dospuntos="";
							$row1[0]="";
						}
						if($row1[5] == "Booleano")
							if($row1[1] == "CHECKED")
								$row1[1] = "SI";
							else
								$row1[1] = "NO";
						if($row1[5] == "Seleccion")
							if($row1[1] == "undefined")
								$row1[1] = "No Definido";
						if($row1[5] == "Grid")
						{
							$wsgrid="";
							$Gridseg=explode("*",$row1[16]);
							$Gridtit=explode("|",$Gridseg[0]);
							$wsgrid .= "<table align=center border=1 class=tipoTABLEGRID>";
							$wsgrid .= "<tr>";
							$wsgrid .= "<td id=tipoL06GRID>ITEM</td>";
							for ($g=0;$g<count($Gridtit);$g++)
							{
								$wsgrid .= "<td id=tipoL06GRID>".$Gridtit[$g]."</td>";
							}
							$wsgrid .= "</tr>";
							$Gdataseg=explode("*",$row1[1]);
							for ($g=1;$g<=$Gdataseg[0];$g++)
							{
								if($g % 2 == 0)
									$gridcolor="tipoL02GRID1";
								else
									$gridcolor="tipoL02GRID2";
								$Gdatadata=explode("|",$Gdataseg[$g]);
								$wsgrid .= "<tr>";
								$wsgrid .= "<td class=".$gridcolor.">".$g."</td>";
								for ($g1=0;$g1<count($Gdatadata);$g1++)
								{
									$wsgrid .= "<td class=".$gridcolor.">".$Gdatadata[$g1]."</td>";
								}
								$wsgrid .= "</tr>";
							}
							$wsgrid .= "</table><br>";
						}
						if($row1[5] == "Seleccion" and $row1[1] != "" and $row1[15]=="M")
						{
							$row1[1]=str_replace("</OPTION>"," ",$row1[1]);
							$row1[1]=str_replace("</option>"," ",$row1[1]);
							$imp=0;
							$def="";
							$ndiag=0;
							for ($z=0;$z<strlen($row1[1]);$z++)
							{
								if(substr($row1[1],$z,1) == "<")
									$imp=1;
								if(substr($row1[1],$z,1) == ">")
								{
									$imp=0;
									$z++;
									$ndiag++;
									$def .= "(".$ndiag.") ";
								}
								if($imp == 0)
								{
									$def .= substr($row1[1],$z,1);
								}
							}
							$def=str_replace("P-"," Presuntivo ",$def);
							$def=str_replace("C-"," Confirmado ",$def);
							$row1[1]=$def;
						}
						if($row1[5] == "Tabla")
						{
							$row1[1]=str_replace("</OPTION>"," ",$row1[1]);
							$imp=0;
							$def="";
							for ($z=0;$z<strlen($row1[1]);$z++)
							{
								if(substr($row1[1],$z,1) == "<")
									$imp=1;
								if(substr($row1[1],$z,1) == ">")
								{
									$imp=0;
									$z++;
								}
								if($imp == 0)
									$def .= substr($row1[1],$z,1);
							}
							$row1[1]=$def;
						}
						if($row1[5] == "Formula")
								$wsstring = "<b>".htmlentities($row1[0])."</b> ".$dospuntos." ".number_format((double)$row1[1],2,'.',',')." ";
							elseif($row1[5] == "Seleccion" and $row1[15]=="M")
										$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
								elseif($row1[5] == "Seleccion")
										$wsstring = "<b>".htmlentities($row1[0])."</b> ".$dospuntos." ".ver(htmlentities($row1[1]))." ";
									elseif($row1[5] == "Memo")
											$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$row1[1]."</b> ";
										elseif($row1[5] == "Grid")
												$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$wsgrid."</b> ";
											else
												$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
						$spanw=calcularspan($wsstring);
						if($spanw == 1)
							if($row1[5] == "Formula")
								$wsstring = "<b>".htmlentities($row1[0])."</b> ".$dospuntos." ".number_format((double)$row1[1],2,'.',',')." ";
							elseif($row1[5] == "Seleccion" and $row1[15]=="M")
										$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
								elseif($row1[5] == "Seleccion")
										$wsstring = "<b>".htmlentities($row1[0])."</b> ".$dospuntos." ".ver(htmlentities($row1[1]))." ";
									elseif($row1[5] == "Memo")
											$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$row1[1]."</b> ";
										elseif($row1[5] == "Grid")
												$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$wsgrid."</b> ";
											else
												$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
						if($row1[5] == "Imagen")
						{
							$Hgraficas .=  $row1[1]."|";
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
							$numimg++;
							//echo "<input type='HIDDEN' name= 'Hgraficas' id='Hgraficas' value='".$Hgraficas."'>";
							$wsstring = "".htmlentities($row1[0])." :<br><IMG SRC='/matrix/images/medical/HCE/".$row1[9]."' id='G".$numimg."' /><br>".$TEXTOD."";
							$spanw=8;
						}	
						if(($spana + $spanw) < 9)
						{
							if($spana == 0)
							{
								if($spanw == 1)
								{
									$WIDTH=$spanw*94;
									echo "<tr><td colspan=".$spanw." id=tipoL02J".$tcolor." width='".$WIDTH."'>".$wsstring."</td>";
								}
								else
								{
									$WIDTH=$spanw*94;
									echo "<tr><td colspan=".$spanw." id=tipoL02W".$tcolor." width='".$WIDTH."'>".$wsstring."</td>";
								}
							}
							else
							{
								if($spanw == 1)
								{
									$WIDTH=$spanw*94;
									echo "<td colspan=".$spanw." id=tipoL02J".$tcolor." width='".$WIDTH."'>".$wsstring."</td>";
								}
								else
								{
									$WIDTH=$spanw*94;
									echo "<tr><td colspan=".$spanw." id=tipoL02W".$tcolor." width='".$WIDTH."'>".$wsstring."</td>";
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
									$WIDTH=$spanw*94;
									echo "<tr><td colspan=".$spanw." id=tipoL02J".$tcolor." width='".$WIDTH."'>".$wsstring."</td>";
								}
								else
								{
									$WIDTH=$spanw*94;
									echo "<tr><td colspan=".$spanw." id=tipoL02W".$tcolor." width='".$WIDTH."'>".$wsstring."</td>";
								}
							}
							else
							{
								if($spana < 8)
								{
									$spanf=8 - $spana;
									$WIDTH=$spanf*94;
									echo "<td colspan=".$spanf." id=tipoL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
								}
								if($kcolor % 2 == 0)
									$tcolor="1";
								else
									$tcolor="2";
								$kcolor++;
								if($spanw == 1)
								{
									$WIDTH=$spanw*94;
									echo "</tr><tr><td colspan=".$spanw." id=tipoL02J".$tcolor." width='".$WIDTH."'>".$wsstring."</td>";
								}
								else
								{
									$WIDTH=$spanw*94;
									echo "<tr><td colspan=".$spanw." id=tipoL02W".$tcolor." width='".$WIDTH."'>".$wsstring."</td>";
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
					$WIDTH=$spanf*94;
					echo "<td colspan=".$spanf." id=tipoL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
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
					$queryJ .= "   and ".$empresa."_".$wforant.".movusu = ".$empresa."_000020.Usucod ";
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
					$WIDTH=8*94;
					echo "<tr><td  colspan=8 id=tipoL02H width='".$WIDTH."'>".$datfirma."</td></tr>";
					for ($h=0;$h<count($notas);$h++)
						echo "<tr><td  colspan=8 id=tipoL02I width='".$WIDTH."'>NOTA REALIZADA EN : ".$notas[$h]."</td></tr>";
				}
				else
				{
					$WIDTH=8*94;
					echo "<tr><td  colspan=8 id=tipoL02N width='".$WIDTH."'>SIN FIRMAR</td></tr>";
				}
			}
			
			$WIDTH=8*94;
			//echo "<tr><td colspan=8 id=tipoL02Q width='".$WIDTH."'><input type='button' value='IMPRIMIR'  onclick='salto1(\"".$wintitulo."\")'></td></tr>";
			echo "</table>";			
		}
		else
		{
			echo "<center><table border=0 aling=center><tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' id='cabeza'></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>NO SELECCIONO FORMULARIOS PARA IMPRESION !!!</MARQUEE></FONT>";
		}
	}
}
?>
