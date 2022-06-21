<html>
<head>
  	<title>MATRIX Graficacion de Variables Numericas Historia Clinica Hospitalaria HCE</title>
	<link type='text/css' href='HCE.css' rel='stylesheet'>
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    
<!-- BEGIN: load jqplot -->
	 <script language="javascript" type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script> 
	<script language="javascript" type="text/javascript" src="../../../include/root/excanvas.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	
	 <!--[if IE 6]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot.css" /> 
	 <![endif]--> 
	 <!--[if IE 7]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	 <![endif]--> 
	 <!--[if IE 8]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	 <![endif]--> 
  
  	<script language="javascript" type="text/javascript" src="../../../include/root/jquery.jqplot.min.js"></script> 
  	<script language="javascript" type="text/javascript" src="../../../include/root/jqplot.dateAxisRenderer.min.js"></script>
  	<!--<script type="text/javascript" src="../plugins/jqplot.barRenderer.min.js"></script>-->  
  	
  	<script type="text/javascript" src="../../../include/root/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.pointLabels.min.js"></script>
	
	<script type="text/javascript" src="../../../include/root/jqplot.dateAxisRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.highlighter.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.cursor.min.js"></script>
	<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
  	
<!-- END: load jqplot -->  
	<style>
		.button {
		   border-top: 1px solid #96d1f8;
		   background: #65a9d7;
		   background: -webkit-gradient(linear, left top, left bottom, from(#0e4970), to(#65a9d7));
		   background: -webkit-linear-gradient(top, #0e4970, #65a9d7);
		   background: -moz-linear-gradient(top, #0e4970, #65a9d7);
		   background: -ms-linear-gradient(top, #0e4970, #65a9d7);
		   background: -o-linear-gradient(top, #0e4970, #65a9d7);
		   padding: 5.5px 11px;
		   -webkit-border-radius: 3px;
		   -moz-border-radius: 3px;
		   border-radius: 3px;
		   -webkit-box-shadow: rgba(0,0,0,1) 0 1px 0;
		   -moz-box-shadow: rgba(0,0,0,1) 0 1px 0;
		   box-shadow: rgba(0,0,0,1) 0 1px 0;
		   text-shadow: rgba(0,0,0,.4) 0 1px 0;
		   color: #ffffff;
		   font-size: 15px;
		   font-family: Helvetica, Arial, Sans-Serif;
		   text-decoration: none;
		   vertical-align: middle;
		   }
		.button:hover {
		   border-top-color: #28597a;
		   background: #28597a;
		   color: #cccccc;
		   }
		.button:active {
		   border-top-color: #1b435e;
		   background: #1b435e;
		   }
	</style>
<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    <script type='text/javascript' src='HCE.js' ></script>
    
    <script language="javascript" type="text/javascript"> 
	    // nice unique id function generator which I don't use anymore.
	    
	    var uID = (function() 
	    {
	      var id = 1;
	      return function(){return id++};
	    })();
	 
	    function run() 
	    {
	      eval(document.getElementById('grafica').value);
	    }
    
	</script> 
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--

	function enter()
	{
		document.forms.HCE_Historico.submit();
	}	
	
	function enterOK()
	{
		document.getElementById('ok').checked=true;
		document.forms.HCE_Historico.submit();
	}
	
//-->
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("hce/funcionesHCE.php");

function biG($d,$n,$k,$i)
{
	//$n--;
	if($n >= 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			//echo " Medio : ".$lm." valor: ".$d[$lm][$i]."<br>";
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][$i]),20);
			//if(strtoupper($k) == strtoupper($d[$lm][$i]))
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
			//echo $k." ".$d[$li][$i]." ".$d[$ls][$i]." ".$d[$lm][$i]." ".$li." ".$ls." ".$lm."<br>";
		}
		if(strtoupper($k) == strtoupper($d[$li][$i]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][$i]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}

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
	   PROGRAMA : HCE_Historico.php
	   Fecha de Liberación : 2010-07-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2019-08-13
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica con los datos historicos de tipo numerico que han sido
	   grabados en los diferentes formularios de la HCE. Tambien permite graficar las tendencias de dichos datos.
	   
	   REGISTRO DE MODIFICACIONES :
	   	.16/03/2022 - Brigith Lagares: Se realiza estadarización del wemp_pmla.
		   
	     .2019-08-13
              Se agrega el include a funcionesHCE.php con la función calcularEdadPaciente() y se reemplaza en el script el cálculo 
			  de la edad del paciente por dicha función, ya que el cálculo se realizaba con 360 días, es decir, no se tenían en 
			  cuenta los meses de 31 días y para los pacientes neonatos este dato es fundamental.
           
	     .2015-07-15
              Se cambia el query de visualizacion de formulario diligenciados para notas optimizando el acceso a la
              tabal 36 de HCE
           
	     .2013-11-05
              Se ponen dinamicas las tablas de hce y movhos al igual que la empresa origen.
              Solo se muestran los formularios efectivamente diligenciados.
              
	     .2013-06-12
              Se cambia la forma de presentacion de la grafica ya que al cambiar el orden de los datos la grafica
              habia quedado alreves.
              
		 .2013-04-15
              La fecha y hora del registro se cambia de la de grabacion a la de los datos.
              Se organiza la informacion en orden descendente de fecha y hora.
              
	     .2012-02-23
              Se cambia la fecha de ingreso asignandole la de la tabla movhos 16.
              
         .2012-01-17
              Se cambia la forma de almacenamiento de valores numericos en los arreglos para impresion, ya que estaba
              funcionando de forma inadecuada.
 
	    .2011-02-07
			Ultima Version Beta.
			
	   	.2010-07-01
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_Historico' action='HCE_Historico.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	if(isset($wservicio))
		echo "<input type='HIDDEN' name= 'wservicio' value='".$wservicio."'>";
	if(!isset($ok))
	{
		$query = "select Orihis,Oriing from root_000037 ";
		$query .= " where oriced = '".$wcedula."'";
		$query .= "   and oritid = '".$wtipodoc."'";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		if(!isset($wing))
			$wing=$row[1];
			
		if(!isset($whis))
			$whis=$row[0];
		
		$query = "select Fecha_data from ".$wdbmhos."_000016 ";
		$query .= "  where Inghis='".$row[0]."'";
		$query .= "    and Inging='".$row[1]."'";
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
		echo "<tr><td id=tipoTI01 colspan=8>GRAFICACION DE DATOS NUMERICOS DE LA HISTORIA CLINICA ELECTRONICA Version 2022-03-16</td></tr>";
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
		if(!isset($dta))
 		{
			$vistas=array();
			$numvistas=0;
			$query  = "select ".$empresa."_000021.Rararb from ".$empresa."_000020,".$empresa."_000021,".$empresa."_000009 ";
			$query .= "   where ".$empresa."_000020.Usucod = '".$key."' ";
			$query .= " 	and ".$empresa."_000020.Usurol = ".$empresa."_000021.rarcod ";
			$query .= " 	and ".$empresa."_000021.rararb = ".$empresa."_000009.precod "; 
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
			
			$win = "(";
			$query = "select Firpro from ".$empresa."_000036 WHERE Firhis = '".$whis."' and Firing = '".$wing."' group BY Firpro ";
			$err = mysql_query($query,$conex) or die("aqui ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($i > 0)
						$win .= ",".chr(34).$row[0].chr(34);
					else
						$win .= chr(34).$row[0].chr(34);
				}
				$win .= ")";
			}
			else
				$win .= "'')";
			
	 		$dta=0;
			$query = "select ".$empresa."_000009.Precod,".$empresa."_000009.Preurl,".$empresa."_000009.Predes,".$empresa."_000009.prenod from ".$empresa."_000009 "; 
			$query .= " where ".$empresa."_000009.prenod = 'on' ";
			$query .= "   and ".$empresa."_000009.preest = 'on' ";
			$query .= " union all ";
			$query .= " select ".$empresa."_000009.Precod,".$empresa."_000009.Preurl,".$empresa."_000009.Predes,".$empresa."_000009.prenod from ".$empresa."_000009,".$empresa."_000020,".$empresa."_000021,".$empresa."_000037,".$empresa."_000001 "; 
			$query .= " where ".$empresa."_000009.prenod = 'off' ";
			$query .= "   and mid(".$empresa."_000009.Preurl,1,1) = 'F' ";
			$query .= "   and Preurl = CONCAT( 'F=', Encpro ) ";
			$query .= "   and ".$empresa."_000009.preest = 'on' ";
			$query .= "   and ".$empresa."_000020.Usucod = '".$key."' ";
			$query .= "   and ".$empresa."_000020.Usurol = ".$empresa."_000021.Rarcod ";
			$query .= "   and ".$empresa."_000021.Rarcon = 'on' ";
			$query .= "   and ".$empresa."_000021.Rararb = ".$empresa."_000009.precod "; 
			$query .= "	  and ".$empresa."_000009.precod = ".$empresa."_000037.Forcod ";
			$query .= "	  and ".$empresa."_000037.Forser = '".$wservicio."' ";
			$query .= "	  and Encpro IN ".$win." ";
			$query .= " order by 1 ";
			$err = mysql_query($query,$conex) or die("aqui ".mysql_errno().":".mysql_error());
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
					$data[$i][5]= 0;
					$pos=bi($vistas,$numvistas,$row[0]);
					if($pos != -1)
						$data[$i][3]=1;
					else
						$data[$i][3]=0;
					$data[$i][4]=$row[0];
				}
				for ($i=0;$i<$num;$i++)
				{
					$wb = $num - ($i + 1);
					$wbaux = $wb;
					if($data[$wb][2] == "on")
					{
						while($data[$wbaux][2] == "on" and $wbaux < ($num - 1))
							$wbaux++;
						if(($wbaux < ($num - 1) and strpos(substr($data[$wbaux][4],0,strlen($data[$wb][4])),$data[$wb][4]) === false) or $wbaux == ($num - 1))
							$data[$wb][0] = "NO";
					}
				}
				$numFinal=-1;
				$dataaux=array();
				for ($i=0;$i<$num;$i++)
				{
					if($data[$i][0] != "NO")
					{
						$numFinal++;
						$dataaux[$numFinal][0]=$data[$i][0];
						$dataaux[$numFinal][1]=$data[$i][1];
						$dataaux[$numFinal][2]=$data[$i][2];
						$dataaux[$numFinal][3]=$data[$i][3];
						$dataaux[$numFinal][4]=$data[$i][4];
						$dataaux[$numFinal][5]=$data[$i][5];
					}
				}
				$fil=ceil(($numFinal+1) / 4);
				$data=array();
				for ($i=0;$i<=$numFinal;$i++)
				{
					$data[$i][0]=$dataaux[$i][0];
					$data[$i][1]=$dataaux[$i][1];
					$data[$i][2]=$dataaux[$i][2];
					$data[$i][3]=$dataaux[$i][3];
					$data[$i][4]=$dataaux[$i][4];
					$data[$i][5]=$dataaux[$i][5];
				}
			}
		}
		if($dta == 1)
		{
			$wsw=0;
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
								if(!isset($not) and $wsw == 0 and $j == 0)
								{
									echo "<td id=".$color."><input type='RADIO' name='not' value=".$exp." checked></td>";
									$wsw=1;
								}
								elseif(isset($not) and $not == $exp)
										echo "<td id=".$color."><input type='RADIO' name='not' value=".$exp." checked></td>";
									else
										echo "<td id=".$color."><input type='RADIO' name='not' value=".$exp."></td>";
							}
							else
								echo "<td id=".$color."></td>";
							echo "<td id=".$color.">".$data[$exp][1]."</td>";
							echo "<input type='HIDDEN' name= 'data[".$exp."][0]' value=".$data[$exp][0].">";
							echo "<input type='HIDDEN' name= 'data[".$exp."][1]' value=".$data[$exp][1].">";
							echo "<input type='HIDDEN' name= 'data[".$exp."][5]' value=".$data[$exp][5].">";
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
			echo "<tr><td class=tipo3GRID colspan=8>CONSULTAR<input type='checkbox' id='ok' name='ok'></font></td></tr>";
			echo "<tr><td id=tipoTI01  colspan=8><IMG SRC='/matrix/images/medical/HCE/consultar.png' id='logook' style='vertical-align:middle;' OnClick='enterOK()'></td>";
			echo "</table><br><br>"; 
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
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$wemp_pmla.".jpg'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>".$fechal."<input type='text' name='reloj' size='10' readonly='readonly' class=tipo3R></td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-".$row[7]."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table><br><br>";
		$whis=$row[6];
		$wing=$row[7];
		$wformulario=substr($data[$not][0],2);
		$wtitulo=$data[$not][1]." Desde ".$wfechai." Hasta ".$wfechaf;
		echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
		echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
		echo "<input type='HIDDEN' name= 'wfechai' value=".$wfechai.">";
		echo "<input type='HIDDEN' name= 'wfechaf' value=".$wfechaf.">";
		echo "<input type='HIDDEN' name= 'data[".$not."][0]' value='".$data[$not][0]."'>";
		echo "<input type='HIDDEN' name= 'data[".$not."][1]' value='".$data[$not][1]."'>";
		echo "<input type='HIDDEN' name= 'not' value=".$not.">";
		echo "<input type='HIDDEN' name= 'ok' value=".$ok.">";
		
		$queryI  = " select ".$empresa."_000002.Detcon,".$empresa."_000002.Detdes from ".$empresa."_000002 ";
		$queryI .= " where ".$empresa."_000002.detpro = '".$wformulario."' ";
		$queryI .= "   and ".$empresa."_000002.detest='on' "; 
		$queryI .= "   and ".$empresa."_000002.Dettip in ('Numero','Formula') "; 
		$queryI .= "  order by 1 ";
		$err = mysql_query($queryI,$conex);
		$num = mysql_num_rows($err);
		$totcol=$num + 3;
		if ($num > 0)
		{
			$var=array();
			$label=array();
			echo "<center><table border=1 cellpadding=5 id='mitablita' cellspacing=0>";
			echo "<tr><td colspan=".$totcol." id=tipoH01>".$data[$not][1]." - DATOS HISTORICOS</td></tr>";
			echo "<tr><td id=tipoH02>Numero<BR>Evento</td>";
			echo "<td id=tipoH02>Fecha Y Hora</td><td id=tipoH02>Notas</td>";
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				echo "<td id=tipoH02>".$row[1]."</td>";
				$var[$j]=$row[0];
				$LL=str_replace(" ","<br>",$row[1]);
				$label[$j]="{label:'".$LL."'}";
			}
			//echo "<td id=tipoH02>Fecha Y Hora</td>";
			echo "</tr>";
			echo "<tr><td id=tipoH05>Items a <br>Graficar</td>";
			echo "<td id=tipoH05>&nbsp;</td><td id=tipoH05>&nbsp;</td>";
			for ($j=0;$j<$num;$j++)
			{
				if(isset($GRA[$j]))
					echo "<td id=tipoH05><input type='checkbox' name='GRA[".$j."]' checked></td>";
				else
					echo "<td id=tipoH05><input type='checkbox' name='GRA[".$j."]'></td>";
			}
			//echo "<td id=tipoH05>&nbsp;</td>";
			echo "</tr>";
		}
		$kidx=-1;
		$FECHAS=array();
		$klave="";
		$index=0;
		$queryI  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Detcon,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".id from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
		$queryI .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".fecha_data between '".$wfechai."' and  '".$wfechaf."' "; 
		$queryI .= "   and ".$empresa."_".$wformulario.".movpro=".$empresa."_000002.detpro ";
		$queryI .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.detcon ";
		$queryI .= "   and ".$empresa."_000002.detest='on' "; 
		$queryI .= "   and ".$empresa."_000002.Dettip in ('Fecha','Hora') "; 
		$queryI .= "  order by 1,2,5 ";
		$err1 = mysql_query($queryI,$conex);
		$num1 = mysql_num_rows($err1);
		if ($num1>0)
		{
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if($index == 1)
				{
					$FECHAS[$kidx][1] .= $row1[3];
					$index=2;
				}
				if($klave != $row1[0].$row1[1])
				{
					$kidx++;
					$FECHAS[$kidx][0]=$row1[0]." ".$row1[1];
					$FECHAS[$kidx][1]=$row1[3]." ";
					$klave = $row1[0].$row1[1];	
					$index=1;
				}
			}
		}
		$kidy=-1;
		$NOTAS=array();
		$klave="";
		$queryI  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".id from ".$empresa."_".$wformulario." ";
		$queryI .= " where ".$empresa."_".$wformulario.".movpro = '".$wformulario."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".movtip = 'Nota' "; 
		$queryI .= "   and ".$empresa."_".$wformulario.".fecha_data between '".$wfechai."' and  '".$wfechaf."' "; 
		$queryI .= "  order by 1,2,4 ";
		$err1 = mysql_query($queryI,$conex);
		$num1 = mysql_num_rows($err1);
		if ($num1>0)
		{
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if($klave != $row1[0].$row1[1])
				{
					$kidy++;
					$NOTAS[$kidy][0]=$row1[0]." ".$row1[1];
					$NOTAS[$kidy][1]=str_replace("<br>"," ",$row1[2]).chr(10).chr(13);
					$klave = $row1[0].$row1[1];	
				}
				else
					$NOTAS[$kidy][1] .= str_replace("<br>"," ",$row1[2]).chr(10).chr(13);
			}
		}
		$knum=-1;
		$matrix=array();
		$klave="";
		$queryI  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Detcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
		$queryI .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
		$queryI .= "   and ".$empresa."_".$wformulario.".fecha_data between '".$wfechai."' and  '".$wfechaf."' "; 
		$queryI .= "   and ".$empresa."_".$wformulario.".movpro=".$empresa."_000002.detpro ";
		$queryI .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.detcon ";
		$queryI .= "   and ".$empresa."_000002.detest='on' "; 
		$queryI .= "   and ".$empresa."_000002.Dettip in ('Numero','Formula') "; 
		$queryI .= "  order by 1 desc,2 desc,3 ";
		$err1 = mysql_query($queryI,$conex);
		$num1 = mysql_num_rows($err1);
		if ($num1>0)
		{
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if($klave != $row1[0].$row1[1])
				{
					$knum++;
					//$matrix[$knum][0]=$row1[0]." ".$row1[1];
					$matrix[$knum][0]=$knum+1;
					for ($h=0;$h<$num;$h++)
						$matrix[$knum][$var[$h]]="";
					$pos=biG($FECHAS,$kidx,$row1[0]." ".$row1[1],0);
					if($pos != -1)
						$matrix[$knum]['fechahora']=$FECHAS[$pos][1];
					else
						$matrix[$knum]['fechahora']=$row1[0]." ".$row1[1];
					$pos=biG($NOTAS,$kidy,$row1[0]." ".$row1[1],0);
					if($pos != -1)
						$matrix[$knum]['notas']=$NOTAS[$pos][1];
					else
						$matrix[$knum]['notas']="";
					$klave = $row1[0].$row1[1];				
				}
				//$matrix[$knum][$row1[2]]=number_format((double)$row1[3],2,'.',',');
                $matrix[$knum][$row1[2]]=(double)$row1[3];
			}
			$line=array();
			for ($h=0;$h<$num;$h++)
				$line[$h]="line=[";
			for ($j=0;$j<=$knum;$j++)
			{
				if($j % 2 == 0)
					$color="tipoH03";
				else
					$color="tipoH04";
				echo "<tr><td id=".$color.">".$matrix[$knum-$j][0]."</td>";
				echo "<td id=".$color."R>".$matrix[$j]['fechahora']."</td>";
				if($matrix[$j]['notas'] != "")
					echo "<td class=".$color."N id='NOT[".$j."]'  title='NOTAS : ".chr(10).chr(13).strip_tags($matrix[$j]['notas'])."' onMouseMove='tooltipnotas(".$j.")'><IMG SRC='/matrix/images/medical/hce/notas.png'></td>";
				else
					echo "<td class=".$color."N>&nbsp;</td>";
				for ($h=0;$h<$num;$h++)
					if($j == 0)
					{
						//$line[$h] .= "['".substr($matrix[$j][0],0,10)."',";
						$line[$h] .= "[".$matrix[$j][0].",";
					}
					else
					{
						//$line[$h] .= ",['".substr($matrix[$j][0],0,10)."',";
						$line[$h] .= ",[".$matrix[$j][0].",";
					}
				for ($h=0;$h<$num;$h++)
				{
					$line[$h] .=$matrix[$knum-$j][$var[$h]]."]";
					//$line[$h] .=$matrix[$j][$var[$h]].",'".$matrix[$j]['fechahora']."']";
					if($matrix[$j][$var[$h]] == "")
						echo "<td id=".$color."R><IMG SRC='/matrix/images/medical/hce/nodata.png'></td>";
					else
						echo "<td id=".$color."R>".number_format((double)$matrix[$j][$var[$h]],2,'.',',')."</td>";
				}
				//echo "<td id=".$color."R>".$matrix[$j]['fechahora']."</td>";
				echo "</tr>";
			}
			for ($h=0;$h<$num;$h++)
				$line[$h] .= "];";
			echo "<tr><td colspan=".$totcol." id=tipoH01><A HREF='#' class=tipo3V onClick='enter()'>GRAFICAR</A>&nbsp;&nbsp;<A HREF='/MATRIX/HCE/Procesos/HCE_Historico.php?empresa=hce&wcedula=".$wcedula."&wemp_pmla=".$wemp_pmla."&wdbmhos=".$wdbmhos."&wtipodoc=".$wtipodoc."&wfechai=".$wfechai."&wfechaf=".$wfechaf."&wservicio=".$wservicio."' class=tipo3V>RETORNAR</a></td></tr>";
			echo "</table></center>";
			echo "<br><center><table border=0 cellspacing=0><tr><td>";
			$LINEAS="";
			$DATOS="";
			$LABELS="";
			$NL=0;
			for ($h=0;$h<$num;$h++)
			{
				if(isset($GRA[$h]))
				{
					$NL++;
					if($NL == 1)
					{
						$LINEAS .= "line".$NL;
						$LABELS .= $label[$h];
					}
					else
					{
						$LINEAS .= ",line".$NL;
						$LABELS .= ",".$label[$h];
					}
					$line[$h]=substr($line[$h],0,4).$NL.substr($line[$h],4);
					$DATOS .= $line[$h];
				}
			}
			$XAL=$NL * 70;
			if($XAL < 350)
				$XAL=350;
				
			$XAN=$knum * 60;
			if($knum < 1200)
				$XAN=1200;
			
			echo "<div class='jqPlot' id='chart1' style='height:".$XAL."px; width:".$XAN."px;'></div>";
			echo "</td></tr></table></center>";
			
			$blanco="&nbsp";
			echo $blanco."<br>";
			if($NL > 0)
			{
				//echo $DATOS."<br>";
				//echo "<input type=hidden id='grafica' value=\"".$DATOS." plot10 = $.jqplot('chart1', [".$LINEAS."], { title:'Grafica Comparativa', gridPadding:{right:35}, seriesDefaults: {showMarker:true},series:[".$LABELS."],axes:{ xaxis:{renderer:$.jqplot.DateAxisRenderer, rendererOptions:{tickRenderer:$.jqplot.CanvasAxisTickRenderer},tickOptions:{formatString:'%d', fontSize:'8pt',fontFamily:'Arial',angle:-30} }, yaxis:{tickOptions:{fontSize:'8pt',fontFamily:'Arial'},label:''}},highlighter: {sizeAdjust: 7.5},cursor: {show: false},legend:{show:true, location: 'ne',xoffset: -110 }});\"> ";
				echo "<input type=hidden id='grafica' value=\"".$DATOS." plot10 = $.jqplot('chart1', [".$LINEAS."], { title:'".$wtitulo."', seriesDefaults: {showMarker:true},series:[".$LABELS."],axes:{ xaxis:{tickOptions:{fontSize:'8pt',fontFamily:'Arial'} }, yaxis:{tickOptions:{fontSize:'8pt',fontFamily:'Arial'}}},highlighter: {sizeAdjust: 7.5},cursor: {show: true},legend:{show:true, location: 'ne',xoffset: -110 }});\"> ";
				echo "<script>run();</script>";
			}
		}
		else
		{
			echo "<tr><td colspan=".$totcol." id=tipoH01><A HREF='#' class=tipo3V onClick='enter()'>GRAFICAR</A>&nbsp;&nbsp;<A HREF='/MATRIX/HCE/Procesos/HCE_Historico.php?empresa=hce&wcedula=".$wcedula."&wemp_pmla=".$wemp_pmla."&wdbmhos=".$wdbmhos."&wtipodoc=".$wtipodoc."&wfechai=".$wfechai."&wfechaf=".$wfechaf."&wservicio=".$wservicio."' class=tipo3V>RETORNAR</a></td></tr>";
			echo "</table></center>";
		}
	}
}
?>
