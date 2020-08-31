<html>
<head>
  	<title>MATRIX Programa de Egreso de Pacientes</title>
  <!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

<!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo4{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo5{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo6{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    </style>
	<?
	include_once("conex.php");
    include_once("root/comun.php");
	?>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function calendario(id,vrl)
	{
		if (vrl == "1")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfee",button:"trigger1",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
		if (vrl == "2")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfei",button:"trigger2",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
		if (vrl == "3")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfia",button:"trigger3",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
		if (vrl == "4")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfta",button:"trigger4",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
	}

	function nuevoAjax()
	{
		var xmlhttp=false;
		try
		{
			xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e)
		{
			try
			{
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(E) { xmlhttp=false; }
		}
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); }

		return xmlhttp;
	}

	function ajaxquery(fila,id,wdat,empresa,querys,wpos,numero,row)
	{
		var x = new Array();
		var s,st,j,st1;
		querys=unescape(querys);


		// VARIABLES TIPO TEXTO
		x[1]  = document.getElementById("w1").value;  // whis
		x[2]  = document.getElementById("w2").value;  // wdoc
		x[3]  = document.getElementById("w3").value;  // wmeiw
		x[4]  = document.getElementById("w4").value;  // wdxiw
		x[5]  = document.getElementById("w5").value;  // wap1
		x[6]  = document.getElementById("w6").value;  // wap2
		x[7]  = document.getElementById("w7").value;  // wno1
		x[8]  = document.getElementById("w8").value;  // wno2
		x[9]  = document.getElementById("w9").value;  // wnin
		x[10] = document.getElementById("w10").value; // wfei
		x[11] = document.getElementById("w11").value; // whin
		x[12] = document.getElementById("wfee").value; // wfee
		x[13] = document.getElementById("w13").value; // wheg
		x[14] = document.getElementById("w14").value; // west
		x[15] = document.getElementById("w15").value; // wmeew
		x[44] = document.getElementById("wfia").value; // wfia
		x[45] = document.getElementById("wfta").value; // wfta


		//VARIABLES DROP-DOWN
		s= document.forms.egreso.wtdo;
		x[16] = s.options[s.selectedIndex].value;
		s= document.forms.egreso.wmei;
		x[17] = s.options[s.selectedIndex].value;
		s= document.forms.egreso.wdxi;
		x[18] = s.options[s.selectedIndex].value;
		s= document.forms.egreso.wcae;
		x[19] = s.options[s.selectedIndex].value;
		s= document.forms.egreso.wmee;
		x[20] = s.options[s.selectedIndex].value;
		s= document.forms.egreso.wcex;
		x[42] = s.options[s.selectedIndex].value;
		s= document.forms.egreso.wtdp;
		x[43] = s.options[s.selectedIndex].value;


		//VARIABLES RADIO
		for (i=0;i<document.forms.egreso.ok.length;i++)
		{
			if (document.forms.egreso.ok[i].checked==true)
			{
				x[21]=document.forms.egreso.ok[i].value;
				break;
			}
		}


		//for (i=0;i<42;i++)
		//{
			//j=i+1;
			//if (j > 0){
			//alert("i="+j+" : "+x[i+1])};
		//}

		//OTRAS VARIABLES
		x[22]="UNCHECKED";
		if (document.forms.egreso.wcom)
		{
			if (document.forms.egreso.wcom.checked)
			{
				x[22]="CHECKED"
			}
			else
			{
				x[22]="UNCHECKED"
			}
		}
		else
		{
			x[22]="UNCHECKED"
		}
		//alert("id="+id+" ok="+x[21]);
		if (x[21] == 3)
		{
			if (document.forms.egreso.wb)
			{
				for (i=0;i<document.forms.egreso.wb.length;i++)
				{
					if (document.forms.egreso.wb[i].checked==true)
					{
						x[40]=document.forms.egreso.wb[i].value;
						break;
					}
				}
			}
			if (document.getElementById("w22"))
			{
				x[41] = document.getElementById("w22").value; // wposs
			}
			switch(id)
			{
				case "5":
					x[23] = document.getElementById("w16").value; // wBdxw
				break;
				case "6":
					x[23] = document.getElementById("w16").value; // wBdxw
					s= document.forms.egreso.wBdx;
					x[24] = s.options[s.selectedIndex].value;
					x[25] = document.getElementById("w17").value; // wBdxps
				break;
				case "7":
					x[23] = document.getElementById("w16").value; // wBdxw
					s= document.forms.egreso.wBdx;
					x[24] = s.options[s.selectedIndex].value;
					x[25] = document.getElementById("w17").value; // wBdxps
					x[26]="UNCHECKED";
					if (document.forms.egreso.elements['deldx['+row+']'])
					{
						x[26]="CHECKED"
					}
					x[27] = document.forms.egreso.elements['wBdxa['+row+']'].value;
				break;
				case "8":
					x[28] = document.getElementById("w18").value; // wBprw
				break;
				case "9":
					x[28] = document.getElementById("w18").value; // wBprw
					s= document.forms.egreso.wBpr;
					x[29] = s.options[s.selectedIndex].value;
				break;
				case "10":
					x[28] = document.getElementById("w18").value; // wBprw
					s= document.forms.egreso.wBpr;
					x[29] = s.options[s.selectedIndex].value;
					x[30]="UNCHECKED";
					if (document.forms.egreso.elements['delpr['+row+']'])
					{
						x[30]="CHECKED"
					}
					x[31] = document.forms.egreso.elements['wBpra['+row+']'].value;
				break;
				case "11":
					x[32] = document.getElementById("w19").value; // wBesw
				break;
				case "12":
					x[32] = document.getElementById("w19").value; // wBesw
					s= document.forms.egreso.wBes;
					x[33] = s.options[s.selectedIndex].value;
				break;
				case "13":
					x[32] = document.getElementById("w19").value; // wBesw
					s= document.forms.egreso.wBes;
					x[33] = s.options[s.selectedIndex].value;
					x[34]="UNCHECKED";
					if (document.forms.egreso.elements['deles['+row+']'])
					{
						x[34]="CHECKED"
					}
					x[35] = document.forms.egreso.elements['wBesa['+row+']'].value;
				break;
				case "14":
					x[36] = document.getElementById("w20").value; // wBsew
				break;
				case "15":
					x[36] = document.getElementById("w20").value; // wBsew
					s= document.forms.egreso.wBse;
					x[37] = s.options[s.selectedIndex].value;
				break;
				case "16":
					x[36] = document.getElementById("w20").value; // wBsew
					s= document.forms.egreso.wBse;
					x[37] = s.options[s.selectedIndex].value;
					x[38]="UNCHECKED";
					if (document.forms.egreso.elements['delse['+row+']'])
					{
						x[38]="CHECKED"
					}
					x[39] = document.forms.egreso.elements['wBsea['+row+']'].value;
				break;
			}
		}

		//st="Egreso.php?empresa="+empresa+"&wdat="+wdat;
		st="empresa="+empresa+"&wdat="+wdat;
		st=st+"&whis="+x[1]+"&wdoc="+x[2]+"&wmeiw="+x[3]+"&wdxiw="+x[4]+"&wap1="+x[5]+"&wap2="+x[6]+"&wno1="+x[7]+"&wno2="+x[8]+"&wnin="+x[9]+"&wfei="+x[10]+"&whin="+x[11]+"&wfee="+x[12]+"&wheg="+x[13]+"&west="+x[14]+"&wmeew="+x[15];
		st=st+"&wtdo="+x[16]+"&wmei="+x[17]+"&wdxi="+x[18]+"&wcae="+x[19]+"&wmee="+x[20]+"&ok="+x[21]+"&wcom="+x[22]+"&wcex="+x[42]+"&wtdp="+x[43]+"&wfia="+x[44]+"&wfta="+x[45];
		st=st+"&querys="+querys+"&wpos="+wpos+"&numero="+numero+"&okx="+id;

		if (x[21] == 3)
		{
			switch(id)
			{
				case "5":
					st=st+"&wBdxw="+x[23];
				break;
				case "6":
					st=st+"&wBdxw="+x[23]+"&wBdx="+x[24]+"&wBdxps="+x[25];
				break;
				case "7":
					st=st+"&wBdxw="+x[23]+"&wBdx="+x[24]+"&wBdxps="+x[25]+"&deldxw="+x[26]+"&wBdxaw="+x[27];
				break;
				case "8":
					st=st+"&wBprw="+x[28];
				break;
				case "9":
					st=st+"&wBprw="+x[28]+"&wBpr="+x[29];
				break;
				case "10":
					st=st+"&wBprw="+x[28]+"&wBpr="+x[29]+"&delprw="+x[30]+"&wBpraw="+x[31];
				break;
				case "11":
					st=st+"&wBesw="+x[32];
				break;
				case "12":
					st=st+"&wBesw="+x[32]+"&wBes="+x[33];
				break;
				case "13":
					st=st+"&wBesw="+x[32]+"&wBes="+x[33]+"&delesw="+x[34]+"&wBesaw="+x[35];
				break;
				case "14":
					st=st+"&wBsew="+x[36];
				break;
				case "15":
					st=st+"&wBsew="+x[36]+"&wBse="+x[37];
				break;
				case "16":
					st=st+"&wBsew="+x[36]+"&wBse="+x[37]+"&delsew="+x[38]+"&wBseaw="+x[39];
				break;
			}
			st=st+"&wb="+x[40];
			if (document.getElementById("w22"))
			{
				st=st+"&wposs="+x[41];
			}
		}

		st1="";
		for (i=0;i<st.length;i++)
		{
			if (st.substr (i, 1) != "#")
			{
				st1=st1+st.substr (i, 1);
			}
			else
			{
				st1=st1+" ";
			}
		}
		st=st1;
		//alert("st="+st);
		ajax=nuevoAjax();
		//ajax.open("GET", st, true);
		ajax.open("POST", "Egreso.php",true);
   		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
   		ajax.send(st);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				//alert(ajax.responseText);
				document.getElementById(+fila).innerHTML=ajax.responseText;
			}
		}
		ajax.send(null);
	}

	function enter()
	{
		document.forms.egreso.submit();
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
<?php


echo "<div id='1'>";
/**********************************************************************************************************************
	   PROGRAMA : egreso.php
	   Fecha de Liberacion : 2006-07-05
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2012-02-06

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite grabar el egreso hospitalario
	   o de consulta de pacientes que interactuen con la IPS. se detalla los datos propios del egreso, los diagnosticos,
	   los procedimientos, las especialidades y los servicios que intervinieron en el proceso de la atencion.


	   REGISTRO DE MODIFICACIONES :
	   .2016-03-16 camilo zz
			Se modifica para dar el alta definitiva en la tabla mhoscs_18. en caso de requerir buscar "_000018"
	   .2015-11-06
			Se modifica para guardar el codigo del usuario que realiza el egreso en vez de clisur en el campo seguridad.

	   .2012-02-06
			Se modifico el programa para incluir los campos de Fecha de Inicio de Tratamiento y Terminacion de Tratamiento.
			(Egrfia y Egrfta) necesario para evitar glosa y devoluciones en los procesos de facturacion.

	   .2008-01-31
	   		Se modifico la base de datos y el programa para incluir los campos de Causa Externa "Egrcex" y Tipo de Diagnostico
	   		Principal "Egrtdp" en el archivo 108 de Registro de Egreso de Pacientes.

	   .2007-06-25
	   		Se modifico en el programa la busqueda del ultimo ingreso no se hiciera por este campo ya que el campo al ser
	   		alfanumerico el 9 es mayor que el 10.
	   		por tanto en el query la ordenacion se cambio al ID del archivo 101 Ingreso de Pacientes para
	   		que la informacion llegue en el orden en que se ingreso que corresponde al mismo orden de ingreso.

	   .2007-06-19
	   		Se modifico el programa para incluir la subrrutina de validacion de año bisiesto que no habia sido incluida.

	   .2006-12-15
	   		Se cambia en el programa los tipos de de los campos de historia y nro de ingreso de integer a varchar.

	   .2006-11-22
	   		Se cambia el Metodo del AJAX de GET a POS para evitar problemas con caracteres especiales como la ñ.

	   .2006-08-22
	   		Se adiciona a la seleccion de servicios de ingreso los centros de costos tipo 'A' o de Admision de pacientes
	   		y tipo 'H' o Hospitalarios.
	   		Se adiciono sort por ID a los detalles de diagnosticos, Procedimientos, Especialidades y Servcicios.

	   .2006-08-19
	   		Se cambia al programa a la tecnologia AJAX.

	   .2006-07-11
	   		Se corrigio el bloqueo de la tabla de pacientes para inactivarlo.

	   .2006-07-05
	   		Release de Versi�n Beta.

***********************************************************************************************************************/
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}

function consultarAplicacion( $conexion, $wemp_pmla, $nombreApl ){
	$query = "SELECT detval
				FROM root_000051
			   WHERE detemp = '$wemp_pmla'
			     AND detapl = '$nombreApl'";
	$rs    = mysql_query( $query, $conexion );
	$row   = mysql_fetch_array($rs);
	return( $row['detval'] );
}

function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="/^-?[0-9]{1,4}$/";
	if (preg_match($decimal,$chain))
		return true;
	else
		return false;
}

function validar2($chain, $origen = "")
{
	// Funcion que permite validar la estructura de un numero Entero
    echo $origen;
    $respuesta = false;
	$regular="/^[1-9][0-9]*$/";
	if (preg_match($regular,$chain)){
		return true;
    }
    if( $origen != "" ){
        echo preg_match($regular,$chain);
    }
}
function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
function validar3($chain)
{
	// Funcion que permite validar la estructura de una fecha
	$fecha="/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/";
	if (preg_match($fecha,$chain))
		return true;
	else
		return false;
}
function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="/[A-Za-z0-9]/i";
	if (preg_match($regular,$chain))
		return true;
	else
		return false;
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="/^([0-9:])+$/";
	return (preg_match($regular,$chain));
}
function validar6($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="/^([[:digit:]]{1,2}):([[:digit:]]{1,2}):([[:digit:]]{1,2})$/";
	if(preg_match($hora,$chain))
		return true;
	else
		return false;
}
function valgen($whisx,$wninx,$wmeix,$wdxix,$wfeex,$whegx,$westx,$wcaex,$wmeex,$wcexx,$wtdpx,$wfiax,$wftax,&$werr,&$e)
{
	global $empresa;
	//VALIDACION DE DATOS GENERALES
	if(!validar2($whisx))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  NUMERO DE HISTORIA";
	}
	if(!validar2($wninx))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO O NO EXITE NUMERO DE INGRESO";
	}
	if(!validar4($wmeix))
		echo 'noexiste';
	if ($wmeix == "0-NO APLICA")
		echo 'noaplica';
	if ($wmeix == "-")
		echo 'guion';
	if ($wmeix == "SELECCIONE")
		echo 'seleccione';
	if(!validar4($wmeix) or $wmeix == "0-NO APLICA" or $wmeix == "-" or $wmeix == "SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO MEDICO DE INGRESO";
	}
	if(!validar4($wdxix) or $wdxix == "0-NO APLICA"  or $wdxix == "-" or $wdxix == "SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO DIAGNOSTICO DE INGRESO";
	}
	if(!validar3($wfeex))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO FECHA DE EGRESO";
	}
	if(!validar3($wfiax))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO FECHA DE INICIO DE ATENCION";
	}
	if(!validar3($wftax))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO FECHA DE TERMINACION DE ATENCION";
	}
	if(!validar6($whegx))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO HORA DE EGRESO";
	}
	if(!validar2($westx, "estancia"))
	{
        if( $westx != 0 ){
    		$e=$e+1;
    		$werr[$e]="ERROR O NO CALCULO ESTANCIA";
        }
	}
	if(!validar4($wcaex) or $wcaex == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO CAUSA DE EGRESO";
	}
	if(!validar4($wcexx) or $wcexx == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO CAUSA DE EGRESO";
	}
	if(!validar4($wtdpx) or $wtdpx == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO CAUSA DE EGRESO";
	}
	if(!validar4($wmeex) or $wmeex == "0-NO APLICA" or $wmeex == "-" or $wmeex == "SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO MEDICO DE EGRESO";
	}
	if($e == -1)
		return true;
	else
		return false;
}

function MOD_EGR($conex,$whisx,$wninx,$wmeix,$wdxix,$wfeex,$whegx,$westx,$wcaex,$wmeex,$wcexx,$wtdpx,$wcomx,$wfiax,$wftax,&$werr,&$e)
{
	global $empresa;
	$query = "select Egrhis  from  ".$empresa."_000108 where Egrhis='".$whisx."' and Egring='".$wninx."'";
	$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE EGRESOS : ".mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$query =  " update ".$empresa."_000108 set Egrmei = '".substr($wmeix,0,strpos($wmeix,"-"))."',";
		$query .=  "  Egrdxi='".substr($wdxix,0,strpos($wdxix,"-"))."',";
		$query .=  "  Egrfee='".$wfeex."',";
		$query .=  "  Egrhoe='".$whegx."',";
		$query .=  "  Egrest=".$westx.",";
		$query .=  "  Egrcae='".substr($wcaex,0,strpos($wcaex,"-"))."',";
		$query .=  "  Egrcex='".substr($wcexx,0,strpos($wcexx,"-"))."',";
		$query .=  "  Egrtdp='".substr($wtdpx,0,strpos($wtdpx,"-"))."',";
		$query .=  "  Egrmee='".substr($wmeex,0,strpos($wmeex,"-"))."',";
		$query .=  "  Egrfia='".$wfiax."',";
		$query .=  "  Egrfta='".$wftax."',";
		$query .=  "  Egrcom='".$wcomx."' ";
		$query .=  "  where Egrhis='".$whisx."' and Egring='".$wninx."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO EGRESO : ".mysql_errno().":".mysql_error());
		$e=$e+1;
		$werr[$e]="OK! EGRESO ACTUALIZADO ";
		return true;
	}
	else
	{
		$e=$e+1;
		$werr[$e]="EL EGRESO PARA ESTE NUMERO DE HISTORIA E INGRESO NO EXISTE";
		return false;
	}
}

function ACT_EGR($conex,$whisx,$wninx,$wmeix,$wdxix,$wfeex,$whegx,$westx,$wcaex,$wmeex,$wcexx,$wtdpx,$wcomx,$wfiax,$wftax,&$werr,&$e)
{
	global $empresa;
	global $key;

		$query = "select Egrhis  from  ".$empresa."_000108 where Egrhis='".$whisx."' and Egring='".$wninx."'";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE EGRESOS : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num == 0)
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000108 (medico,fecha_data,hora_data, Egrhis, Egring, Egrmei, Egrdxi, Egrfee, Egrhoe, Egrest, Egrcae, Egrmee, Egrcex, Egrtdp, Egrcom, Egrfia, Egrfta, seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."','";
			$query .=  $whisx."','";
			$query .=  $wninx."','";
			$query .=  substr($wmeix,0,strpos($wmeix,"-"))."','";
			$query .=  substr($wdxix,0,strpos($wdxix,"-"))."','";
			$query .=  $wfeex."','";
			$query .=  $whegx."',";
			$query .=  $westx.",'";
			$query .=  substr($wcaex,0,strpos($wcaex,"-"))."','";
			$query .=  substr($wmeex,0,strpos($wmeex,"-"))."','";
			$query .=  substr($wcexx,0,strpos($wcexx,"-"))."','";
			$query .=  substr($wtdpx,0,strpos($wtdpx,"-"))."','";
			// $query .=  $wcomx."','".$wfiax."','".$wftax."','C-".$empresa."')";
			$query .=  $wcomx."','".$wfiax."','".$wftax."','C-".$key."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EGRESO : ".mysql_errno().":".mysql_error());
			$e=$e+1;
			$werr[$e]="OK! EGRESO GRABADO";
			return true;
		}
		else
		{
			$e=$e+1;
			$werr[$e]="EL EGRESO YA FUE GRABADO";
			return false;
		}
}

function consultarWemp_pmla( $conex, $empresa ){
	$query = " SELECT Empcod
				 FROM root_000050
			    WHERE empbda = '$empresa'";
	$rs    = mysql_query( $query, $conex ) or die( mysql_error() );
	$row   = mysql_fetch_array($rs);
	return( $row['Empcod'] );
}

@session_start();
//if(!session_is_registered("user"))
	if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($_SESSION['user'],2,strlen($_SESSION['user']));
	echo "<form name='egreso' action='Egreso.php' method=post>";
	include("conex.php");
	mysql_select_db("matrix");
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<table border=0 align=center id=tipo2>";
	echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png' width=20%></td></tr>";
	echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX </font></td></tr>";
	$wemp_pmla       = consultarWemp_pmla( $conex, $empresa );
	$wbasedatomovhos = consultarAplicacion( $conex, $wemp_pmla, "movhos" );
	//echo "wempmla".$wemp_pmla;
	//echo "base datos movhos".$wbasedatomovhos;
	//******* INICIALIZACION DEL SISTEMA *********
	if(isset($ok) and $ok == 9)
	{
		session_register("estado");
		$estado="1";
		$ok=0;
	}

	//******* VERIFICACION DE INGRESO DE INFORMACION (DIAGNOSTICOS - PROCEDIMIENTOS - ESPECIALIDADES - SERVICIOS) *********
	if(isset($ok)  and $ok == 3)
	{
		$werr=array();
		$e=-1;
		//DIAGNOSTICOS
		if(isset($okx))
		{
			switch ($okx)
			{
				case 7:
					$wsw=0;
					if(isset($deldxw) and $deldxw == "CHECKED")
					{
						$wsw=1;
						$query = "DELETE from ".$empresa."_000109 where Diahis = '".$whis."' and Diaing= '".$wnin."' and Diacod='".$wBdxaw."' ";
						$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO ARCHIVO DE DIAGNOSTICOS : ".mysql_errno().":".mysql_error());
					}
				break;

				case 6:
					$wsw=0;
					$wBdxps=strtoupper($wBdxps);
					if($wBdxps == "P")
					{
						$query = "SELECT Diacod,Diatip from ".$empresa."_000109 where Diahis = '".$whis."' and Diaing= '".$wnin."' and Diatip='P' ";
						$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE DIAGNOSTICOS : ".mysql_errno().":".mysql_error());
						$num = mysql_num_rows($err);
						if ($num > 0)
						{
							$wsw=1;
							$e=$e+1;
							$werr[$e]="YA EXISTE UN DIAGNOSTICO PRIMARIO";
						}
					}
					else
					{
						$query = "SELECT Diacod,Diatip from ".$empresa."_000109 where Diahis = '".$whis."' and Diaing= '".$wnin."' and Diatip='P' ";
						$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE DIAGNOSTICOS : ".mysql_errno().":".mysql_error());
						$num = mysql_num_rows($err);
						if ($num == 0)
							$wBdxps = "P";
						else
							$wBdxps = "S";
					}
					$query = "SELECT Diacod,Diatip from ".$empresa."_000109 where Diahis = '".$whis."' and Diaing= '".$wnin."' and Diacod='".substr($wBdx,0,strpos($wBdx,"-"))."' ";
					$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE DIAGNOSTICOS : ".mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num > 0)
					{
						$wsw=1;
						$e=$e+1;
						$werr[$e]="YA EXISTE ESTE DIAGNOSTICO";
					}
					$query = "SELECT Diacod from ".$empresa."_000109 where Diahis = '".$whis."' and Diacod='".substr($wBdx,0,strpos($wBdx,"-"))."' ";
					$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE DIAGNOSTICOS : ".mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num == 0)
						$wBdxNEW="S";
					else
						$wBdxNEW="N";
					if($wsw == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000109 (medico,fecha_data,hora_data, Diahis, Diaing, Diacod, Diatip, Dianue, seguridad) values ('";
						$query .=  $empresa."','";
						$query .=  $fecha."','";
						$query .=  $hora."','";
						$query .=  $whis."','";
						$query .=  $wnin."','";
						$query .=  substr($wBdx,0,strpos($wBdx,"-"))."','";
						$query .=  $wBdxps."','";
						$query .=  $wBdxNEW;
						$query .=  "','C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DIAGNOSTICO : ".mysql_errno().":".mysql_error());
					}
				break;
				//PROCEDIMIENTOS Prohis Proing Procod
				case 10:
					$wsw=0;
					if(isset($delprw) and $delprw == "CHECKED")
					{
						$wsw=1;
						$query = "DELETE from ".$empresa."_000110 where Prohis = '".$whis."' and Proing= '".$wnin."' and Procod='".$wBpraw."' ";
						$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO ARCHIVO DE PROCEDIMIENTOS : ".mysql_errno().":".mysql_error());
					}
				break;

				case 9:
					$query = "SELECT Procod from ".$empresa."_000110 where Prohis = '".$whis."' and Proing= '".$wnin."' and Procod='".substr($wBpr,0,strpos($wBpr,"-"))."' ";
					$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PROCEDIMIENTOS : ".mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num == 0)
						$wsw=0;
					else
					{
						$wsw=1;
						$e=$e+1;
						$werr[$e]="YA EXISTE ESTE PROCEDIMIENTO";
					}
					if($wsw == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000110 (medico,fecha_data,hora_data, Prohis, Proing, Procod, seguridad) values ('";
						$query .=  $empresa."','";
						$query .=  $fecha."','";
						$query .=  $hora."','";
						$query .=  $whis."','";
						$query .=  $wnin."','";
						$query .=  substr($wBpr,0,strpos($wBpr,"-"));
						$query .=  "','C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO PROCEDIMIENTO : ".mysql_errno().":".mysql_error());
					}
				break;
				//ESPECIALIDADES Esphis Esping Espcod
				case 13:
					$wsw=0;
					if(isset($delesw) and $delesw == "CHECKED")
					{
						$wsw=1;
						$query = "DELETE from ".$empresa."_000111 where Esphis = '".$whis."' and Esping= '".$wnin."' and Espcod='".$wBesaw."' ";
						$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO ARCHIVO DE ESPECIALIDADES : ".mysql_errno().":".mysql_error());
					}
				break;

				case 12:
					$query = "SELECT Espcod from ".$empresa."_000111 where Esphis = '".$whis."' and Esping= '".$wnin."' and Espcod='".substr($wBes,0,strpos($wBes,"-"))."' ";
					$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE ESPECIALIDADES : ".mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num == 0)
						$wsw=0;
					else
					{
						$wsw=1;
						$e=$e+1;
						$werr[$e]="YA EXISTE ESTA ESPECIALIDAD";
					}
					if($wsw == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000111 (medico,fecha_data,hora_data, Esphis, Esping, Espcod, seguridad) values ('";
						$query .=  $empresa."','";
						$query .=  $fecha."','";
						$query .=  $hora."','";
						$query .=  $whis."','";
						$query .=  $wnin."','";
						$query .=  substr($wBes,0,strpos($wBes,"-"));
						$query .=  "','C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO ESPECIALIDAD : ".mysql_errno().":".mysql_error());
					}
				break;
				//SERVICIOS Serhis Sering Sercod
				case 16:
					$wsw=0;
					if(isset($delsew) and $delsew == "CHECKED")
					{
						$wsw=1;
						$query = "DELETE from ".$empresa."_000112 where Serhis = '".$whis."' and Sering= '".$wnin."' and Sercod='".$wBseaw."' ";
						$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO ARCHIVO DE SERVICIOS : ".mysql_errno().":".mysql_error());
					}
				break;

				case 15:
					$query = "SELECT Sercod from ".$empresa."_000112 where Sering = '".$whis."' and Sering= '".$wnin."' and Sercod='".substr($wBse,0,strpos($wBse,"-"))."' ";
					$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE SERVICIOS : ".mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num == 0)
						$wsw=0;
					else
					{
						$wsw=1;
						$e=$e+1;
						$werr[$e]="YA EXISTE ESTE SERVICIO";
					}
					if($wsw == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000112 (medico,fecha_data,hora_data, Serhis, Sering, Sercod, seguridad) values ('";
						$query .=  $empresa."','";
						$query .=  $fecha."','";
						$query .=  $hora."','";
						$query .=  $whis."','";
						$query .=  $wnin."','";
						$query .=  substr($wBse,0,strpos($wBse,"-"));
						$query .=  "','C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO SERVICIO : ".mysql_errno().":".mysql_error());
					}
				break;
			}
		}
	}


	//******* GRABACION DE INFORMACION *********
	if(isset($ok) and ($ok == 2 OR $ok == 4))
	{
		$werr=array();
		$e=-1;
		if(isset($wcom) and $wcom == "UNCHECKED")
			unset($wcom);
		if(isset($wcom))
			$wcom="on";
		else
			$wcom="off";
		if(valgen($whis,$wnin,$wmei,$wdxi,$wfee,$wheg,$west,$wcae,$wmee,$wcex,$wtdp,$wfia,$wfta,$werr,$e))
		{
			$query = "lock table ".$empresa."_000100 LOW_PRIORITY WRITE, ".$empresa."_000108 LOW_PRIORITY WRITE ";
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE EGRESOS : ".mysql_errno().":".mysql_error());
			if($ok == 4)
			{
				if(MOD_EGR($conex,$whis,$wnin,$wmei,$wdxi,$wfee,$wheg,$west,$wcae,$wmee,$wcex,$wtdp,$wcom,$wfia,$wfta,$werr,$e))
				{
					$ok=0;
					$estado="3";
				}
			}
			else
			{
				if(ACT_EGR($conex,$whis,$wnin,$wmei,$wdxi,$wfee,$wheg,$west,$wcae,$wmee,$wcex,$wtdp,$wcom,$wfia,$wfta,$werr,$e))
				{
					$ok=0;
					$estado="3";
					//INCATIVAR PACIENTE DESPUES DE GRABACION DEL EGRESO HOSPITALARIO
					$query = "UPDATE ".$empresa."_000100 set Pacact='off' where Pachis = '".$whis."'";
					$err1 = mysql_query($query,$conex) or die("ERROR INACTIVANDO PACIENTE EN ARCHIVO 100 : ".mysql_errno().":".mysql_error());
					//DAR DE ALTA DEFINITIVA EN LA TABLA 18
					$query = "lock table ".$wbasedatomovhos."_000018 LOW_PRIORITY WRITE, ".$empresa."_000108 LOW_PRIORITY WRITE ";
					/*echo "tabla movhos".$wbasedatomovhos;
					echo "tabla movhos".$query;*/
					$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE UBICACION DE PACIENTES : ".mysql_errno().":".mysql_error());
					$query = "UPDATE ".$wbasedatomovhos."_000018 set Ubiald='on', Ubifad='".date('Y-m-d')."', Ubihad='".date('H:i:s')."' where Ubihis = '".$whis."' and Ubiing='".$wnin."'";
					$err1 = mysql_query($query,$conex) or die("ERROR INACTIVANDO PACIENTE EN MOVIMIENTO HOSPITALARIO 18 : ".mysql_errno().":".mysql_error());

				}
			}
			$query = " UNLOCK TABLES";
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
			if($ok != 0)
				$ok = 1;
		}
		else
			$ok=1;
	}

	//******* INICIALIZACION DE CAMPOS *********
	if(isset($ok) and $ok == 0)
	{
		if(isset($estado) and $estado == "3")
		{
			$wdat=0;
			$wtdo="";
			$wdoc="";
			$wmei="";
			$wmeiw="";
			$wdxi="";
			$wdxiw="";
			$wap1="";
			$wap2="";
			$wno1="";
			$wno2="";
			$wfei=date("Y-m-d");
			$whin=date("H:i:s");
			$wfee="";
			$wfia="0000-00-00";
			$wfta="0000-00-00";
			$wheg=date("H:i:s");
			$west=0;
			$wcae="";
			$wmee="";
			$wcex="";
			$wtdp="";
			$wmeew="";
			unset($wcom);
			$querys="";
			$ok=3;
		}
		else
		{
			$wdat=0;
			$whis="";
			$wtdo="";
			$wdoc="";
			$wmei="";
			$wmeiw="";
			$wdxi="";
			$wdxiw="";
			$wap1="";
			$wap2="";
			$wno1="";
			$wno2="";
			$wnin="";
			$wfei=date("Y-m-d");
			$whin=date("H:i:s");
			//$wfee=date("Y-m-d");
			$wfee="";
			$wfia="0000-00-00";
			$wfta="0000-00-00";
			$wheg=date("H:i:s");
			$west=0;
			$wcae="";
			$wmee="";
			$wmeew="";
			$wcex="";
			$wtdp="";
			unset($wcom);
			unset($qa);
			$wpos=0;
			$ok=1;
			$querys="";
		}
	}

	//*******CONSULTA DE INFORMACION *********
	if(isset($ok)  and $ok == 3)
	{
		$estado="1";
		if(isset($querys) and $querys != "")
		{
			$querys=stripslashes($querys);
			$qa=$querys;
		}
		else
		{
			//                  0       1       2        3      4        5       6       7      8       9       10      11      12      13     14       15      16     17       18      19
            $querys = "select Egrhis, Egring, Egrmei, Egrdxi, Egrfee, Egrhoe, Egrest, Egrcae, Egrmee, Egrcom, Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Egrcex, Egrtdp, Egrfia, Egrfta  from  ".$empresa."_000108,".$empresa."_000100 ";
			$querys .= " where Egrhis !='0'";
			$querys .= "   and Egrhis = Pachis";
			if($whis != "")
				$querys .= "     and Egrhis='".$whis."'";
			if($wnin != "")
				$querys .= "     and Egring='".$wnin."'";
			if($wfee != "")
				$querys .= "     and Egrfee='".$wfee."'";
			if($wdoc != "")
				$querys .= "     and Pacdoc='".$wdoc."'";
			if($wap1 != "")
				$querys .= "     and Pacap1='".$wap1."'";
			if($wap2 != "")
				$querys .= "     and Pacap2='".$wap2."'";
			if($wno1 != "")
				$querys .= "     and Pacno1='".$wno1."'";
			if($wno2 != "")
				$querys .= "     and Pacno2='".$wno2."'";
			$querys .=" Order by  Egrhis, Egring  ";
			$err = mysql_query($querys,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numero = mysql_num_rows($err);
			$numero=$numero - 1;
		}
		if ($numero>=0)
		{
			if(isset($wposs) and $wposs != 0)
			{
				$wpos = $wposs - 1;
				if ($wpos < 0)
					$wpos=0;
				if ($wpos > $numero)
					$wpos=$numero;
				$wposs=0;
			}
			if(isset($qa))
			{
				$qa=str_replace(chr(34),chr(39),$qa);
				$qa=substr($qa,0,strpos($qa," limit "));
				$querys=$qa;
			}
			if(isset($qa) and $qa == $querys)
			{
				if(isset($wb) and $wb == 1)
				{
					unset($querysR);
					$wpos = $wpos  + 1;
					if ($wpos > $numero)
						$wpos=$numero;
				}
				elseif(isset($wb) and $wb == 2)
				{
					unset($querysR);
					$wpos = $wpos - 1;
					if ($wpos < 0)
						$wpos=0;
				}
			}
			else
				$wpos=0;
			$wp=$wpos+1;
			//echo "Registro Nro : ".$wpos."<br>";
			$querys .=  " limit ".$wpos.",1";
			$err = mysql_query($querys,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$querys=str_replace(chr(39),chr(34),$querys);
			echo "<input type='HIDDEN' name= 'querys' value='".$querys."'>";
			echo "<input type='HIDDEN' name= 'wpos' value='".$wpos."'>";
			echo "<input type='HIDDEN' name= 'numero' value='".$numero."'>";
			$wdat=0;
			$wdat=1;
			$row = mysql_fetch_array($err);
			$whis=$row[0];
			$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='01' and Selest='on' and selcod ='".$row[10]."' ";
			$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($err1);
			$wtdo=$row1[0]."-".$row1[1];
			$wdoc=$row[11];
			if($wmeiw == "")
				$wmei=$row[2];
			//$query = "SELECT Mednom from ".$empresa."_000051 where Medcod = '".$wmei."'";
			//$err1 = mysql_query($query,$conex);
			//$row1 = mysql_fetch_array($err1);
			//$wmeiw=$row1[0];
			if($wdxiw == "")
				$wdxi=$row[3];
			//$query = "SELECT Descripcion from root_000011 where Codigo = '".$wdxi."'";
			//$err1 = mysql_query($query,$conex);
			//$row1 = mysql_fetch_array($err1);
			//$wdxiw=$row1[0];
			$wap1=$row[12];
			$wap2=$row[13];
			$wno1=$row[14];
			$wno2=$row[15];
			$wnin=$row[1];
			$query = "SELECT Ingfei, Inghin   from ".$empresa."_000101 where Inghis='".$whis."' and Ingnin='".$wnin."'";
			$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($err1);
			$wfei=$row1[0];
			$whin=$row1[1];
			$wfee=$row[4];
			$wheg=$row[5];
			$west=$row[6];
			$wcae=$row[7];
			if($wmeew == "")
				$wmee=$row[8];
			$wcex=$row[16];
			$wtdp=$row[17];
			$wfia=$row[18];
			$wfta=$row[19];
			//$query = "SELECT Mednom from ".$empresa."_000051 where Medcod = '".$wmee."'";
			//$err1 = mysql_query($query,$conex);
			//$row1 = mysql_fetch_array($err1);
			//$wmeew=$row1[0];
			if($row[9] == "on")
				$wcom="on";
			else
				unset($wcom);
		}
		else
		{
			$wdat=0;
			$whis="";
			$wtdo="";
			$wdoc="";
			$wmei="";
			$wmeiw="";
			$wdxi="";
			$wdxiw="";
			$wap1="";
			$wap2="";
			$wno1="";
			$wno2="";
			$wnin="";
			$wfei=date("Y-m-d");
			$whin=date("H:i:s");
			$wfee="";
			$wfia="0000-00-00";
			$wfta="0000-00-00";
			$wheg=date("H:i:s");
			$west=0;
			$wcae="";
			$wmee="";
			$wmeew="";
			$wcex="";
			$wtdp="";
			unset($wcom);
			unset($qa);
			$wpos=0;
			$ok=1;
			$querys="";
		}
		if(isset($wp))
		{
			$estado="1";
			$n=$numero +1 ;
			echo "<tr><td align=right colspan=5><font size=2><b>Registro Nro. ".$wp." De ".$n."</b></font></td></tr>";
		}
		else
			echo "<tr><td align=right colspan=5><font size=2 color='#CC0000'><b>Consulta Sin Registros</b></font></td></tr>";
	}

	//*******PROCESO DE INFORMACION *********

	//********************************************************************************************************
	//*                                         DATOS DEL PACIENTE                                           *
	//********************************************************************************************************


	echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>EGRESO DE PACIENTES</font><font color=#33CCFF size=4>&nbsp&nbsp&nbsp Ver. 2016-03-16</font></b></font></td></tr>";
	$color="#dddddd";
	$color1="#000099";
	$color2="#006600";
	$color3="#cc0000";
	$color4="#CC99FF";
	$color5="#99CCFF";
	$color6="#FF9966";
	$color7="#cccccc";
	?>
	<script>
		function ira(){document.egreso.whis.focus();}
	</script>
	<?php
	echo "<tr><td align=center bgcolor=#999999 colspan=5><b>DATOS DEL INGRESO</b></td></tr>";
	//PRIMERA LINEA
	echo "<tr>";
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($numero))
		$numero=0;
	if(!isset($wdat))
		$wdat=0;
	$id="ajaxquery('1','1','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
	if($whis == "")
		echo "<td bgcolor=".$color." align=center>*Historia :<br><input type='TEXT' name='whis' size=10 maxlength=10 id='w1' value='".$whis."' OnChange=".$id." class=tipo3></td>";
	else
		echo "<td bgcolor=".$color." align=center>*Historia :<br><input type='TEXT' name='whis'  readonly='readonly' size=10 maxlength=10 id='w1' value='".$whis."' OnChange=".$id." class=tipo3></td>";
	if(isset($ok) and $ok == 1 and $whis !="")
	{
		//MODIFICACION 2007-06-25
		$query = "select Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Ingfei, Inghin, Ingnin from  ".$empresa."_000100,".$empresa."_000101 where Pachis='".$whis."' and Pacact='on' and Pachis=Inghis order by ".$empresa."_000101.id desc ";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PACIENTES EN PROCESO : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$wdat=0;
		if ($num >  0)
		{
			$row = mysql_fetch_array($err);
			$wtdo=$row[0];
			$wdoc=$row[1];
			$wap1=$row[2];
			$wap2=$row[3];
			$wno1=$row[4];
			$wno2=$row[5];
			$wfei=$row[6];
			$whin=$row[7];
			$wnin=$row[8];
		}
		else
		{
			$whis="";
			$wtdo="";
			$wdoc="";
			$wmei="";
			$wmeiw="";
			$wdxi="";
			$wdxiw="";
			$wap1="";
			$wap2="";
			$wno1="";
			$wno2="";
			$wnin="";
			$wfei=date("Y-m-d");
			$whin=date("H:i:s");
		}
	}
	echo "<td bgcolor=".$color." align=center>Tipo Documento : <br>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='01' and Selest='on'";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wtdo' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtdo=ver($wtdo);
			if($wtdo == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo"</td>";
	if ($wdoc == "")
		echo "<td bgcolor=".$color." align=center>*Documento : <br><input type='TEXT' name='wdoc' id='w2' size=12 maxlength=12  value='".$wdoc."' class=tipo3></td>";
	else
		echo "<td bgcolor=".$color." align=center>*Documento : <br><input type='TEXT' name='wdoc' readonly='readonly' id='w2' size=12 maxlength=12  value='".$wdoc."' class=tipo3></td>";
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($numero))
		$numero=0;
	$id="ajaxquery('1','2','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
	if(isset($wmeiw) and $wmeiw != "")
		echo "<td bgcolor=".$color." align=center>Medico Ingreso : <br><input type='TEXT' name='wmeiw' id='w3' size=10 maxlength=30  value='".$wmeiw."' OnBlur=".$id." class=tipo3><br>";
	else
		echo "<td bgcolor=".$color." align=center>Medico Ingreso : <br><input type='TEXT' name='wmeiw' id='w3' size=10 maxlength=30 OnBlur=".$id." class=tipo3><br>";
	if(isset($okx) and $okx == "2")
	{
		echo "<select name='wmei' id=tipo1>";
		if(isset($wmeiw) and $wmeiw != "")
		{
			$query = "SELECT Medcod, Mednom  from ".$empresa."_000051 where Medcod = '".$wmeiw."'  order by Mednom";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($err);
			if ($num == 0)
			{
				$query = "SELECT Medcod, Mednom  from ".$empresa."_000051 where Mednom like '%".$wmeiw."%'  order by Mednom";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($err);
			}
			if ($num>0)
			{
				echo "<option value='SELECCIONE'>SELECCIONE</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wmei=ver($wmei);
					if($wmei == $row[0])
						echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}
			}
			else
				echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	else
	{
		echo "<select name='wmei' id=tipo1>";
		if(isset($wmei))
		{
			$wmei=ver($wmei);
			$query = "SELECT Medcod, Mednom  from ".$empresa."_000051 where Medcod = '".$wmei."'  order by Mednom";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($err);
			echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	echo "</td>";
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($numero))
		$numero=0;
	$id="ajaxquery('1','4','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
	if(isset($wmeew) and $wmeew != "")
		echo "<td bgcolor=".$color." align=center>Medico Egreso : <br><input type='TEXT' name='wmeew'  id='w15' size=10 maxlength=30  value='".$wmeew."' OnBlur=".$id." class=tipo3><br>";
	else
		echo "<td bgcolor=".$color." align=center>Medico Egreso : <br><input type='TEXT' name='wmeew'  id='w15' size=10 maxlength=30 OnBlur=".$id." class=tipo3><br>";
	if(isset($okx) and $okx == "4")
	{
		echo "<select name='wmee' id=tipo1>";
		if(isset($wmeew) and $wmeew != "")
		{
			$query = "SELECT Medcod, Mednom  from ".$empresa."_000051 where Medcod = '".$wmeew."'  order by Mednom";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($err);
			if ($num == 0)
			{
				$query = "SELECT Medcod, Mednom  from ".$empresa."_000051 where Mednom like '%".$wmeew."%'  order by Mednom";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($err);
			}
			if ($num>0)
			{
				echo "<option value='SELECCIONE'>SELECCIONE</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wmee=ver($wmee);
					if($wmee == $row[0])
						echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}
			}
			else
				echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	else
	{
		echo "<select name='wmee' id=tipo1>";
		if(isset($wmee))
		{
			$wmee=ver($wmee);
			$query = "SELECT Medcod, Mednom  from ".$empresa."_000051 where Medcod = '".$wmee."'  order by Mednom";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($err);
			echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	echo "</td>";
	echo "</tr>";
	//SEGUNDA LINEA
	echo "<tr>";
	if($wap1 == "")
		echo "<td bgcolor=".$color." align=center>*1er Apellido : <br><input type='TEXT' name='wap1'  id='w5' size=15 maxlength=20  value='".$wap1."' class=tipo3></td>";
	else
		echo "<td bgcolor=".$color." align=center>*1er Apellido : <br><input type='TEXT' name='wap1' readonly='readonly' id='w5' size=15 maxlength=20  value='".$wap1."' class=tipo3></td>";
	if($wap2 == "")
		echo "<td bgcolor=".$color." align=center>*2do Apellido : <br><input type='TEXT' name='wap2'  id='w6' size=15 maxlength=20  value='".$wap2."' class=tipo3></td>";
	else
		echo "<td bgcolor=".$color." align=center>*2do Apellido : <br><input type='TEXT' name='wap2' readonly='readonly' id='w6' size=15 maxlength=20  value='".$wap2."' class=tipo3></td>";
	if($wno1 == "")
		echo "<td bgcolor=".$color." align=center>*1er Nombre : <br><input type='TEXT' name='wno1' id='w7' size=15 maxlength=20  value='".$wno1."' class=tipo3></td>";
	else
		echo "<td bgcolor=".$color." align=center>*1er Nombre : <br><input type='TEXT' name='wno1' readonly='readonly' id='w7' size=15 maxlength=20  value='".$wno1."' class=tipo3></td>";
	if($wno2 == "")
		echo "<td bgcolor=".$color." align=center>*2do Nombre : <br><input type='TEXT' name='wno2'  id='w8' size=15 maxlength=20  value='".$wno2."' class=tipo3></td>";
	else
		echo "<td bgcolor=".$color." align=center>*2do Nombre : <br><input type='TEXT' name='wno2' readonly='readonly' id='w8' size=15 maxlength=20  value='".$wno2."' class=tipo3></td>";
	if($wnin == "")
		echo "<td bgcolor=".$color." align=center>*Ingreso Nro. : <br><input type='TEXT' name='wnin'  id='w9' size=4 maxlength=4  value='".$wnin."' class=tipo3></td>";
	else
		echo "<td bgcolor=".$color." align=center>*Ingreso Nro. : <br><input type='TEXT' name='wnin' readonly='readonly' id='w9' size=4 maxlength=4  value='".$wnin."' class=tipo3></td>";
	echo "</tr>";
	//TERCERA LINEA
	echo "<tr><td align=center bgcolor=#999999 colspan=5><b>DATOS DEL EGRESO</b></td></tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color." align=center>Fecha Ingreso : <br><input type='TEXT' name='wfei'  readonly='readonly'  id='w10' size=10 maxlength=10  value='".$wfei."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Hora Ingreso : <br><input type='TEXT' name='whin'   readonly='readonly' id='w11' size=8 maxlength=8  value='".$whin."' class=tipo3></td>";
	$cal="calendario('wfee','1')";
	echo "<td bgcolor=".$color." align=center>*Fecha Egreso : <br><input type='TEXT' name='wfee' readonly='readonly' id='wfee' size=10 maxlength=10 value='".$wfee."' class=tipo3><button id='trigger1' onclick=".$cal.">...</button>";
	?>
	<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfee',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	//]]></script>
	<?php
	echo "</td>";
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($numero))
		$numero=0;
	$id="ajaxquery('1','1','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
	echo "<td bgcolor=".$color." align=center>Hora Egreso : <br><input type='TEXT' name='wheg'  id='w13' size=8 maxlength=8 value=".$wheg." OnBlur=".$id." class=tipo3></td>";
	if($wfee != "")
	{
		$ann=(integer)substr($wfei,0,4)*360 +(integer)substr($wfei,5,2)*30 + (integer)substr($wfei,8,2);
		$aa=(integer)substr($wfee,0,4)*360 +(integer)substr($wfee,5,2)*30 + (integer)substr($wfee,8,2);
		$west=($aa - $ann);
	}
	else
		$west=0;
	echo "<td bgcolor=".$color." align=center>Estancia En Dias : <br><input type='TEXT' name='west' readonly='readonly' id='w14' size=5 maxlength=5 value=".number_format((double)$west,0,'.','')."  class=tipo3></td>";
	//CUARTA LINEA
	echo "<tr>";
	echo "<td bgcolor=".$color." align=center>Causa Egreso : <br>";
	echo "<select name='wcae' id=tipo1>";
	echo "<option>0-NO APLICA</option>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='10' and Selest='on'  order by Seldes";
	$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wcae=ver($wcae);
			if($wcae == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($numero))
		$numero=0;
	$id="ajaxquery('1','3','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
	if(isset($wdxiw) and $wdxiw != "")
		echo "<td bgcolor=".$color." colspan=1 align=center>Dx Ingreso : <br><input type='TEXT' name='wdxiw' id='w4' size=10 maxlength=30  value='".$wdxiw."' OnBlur=".$id." class=tipo3><br>";
	else
		echo "<td bgcolor=".$color." colspan=1 align=center>Dx Ingreso : <br><input type='TEXT' name='wdxiw' id='w4' size=10 maxlength=30 OnBlur=".$id." class=tipo3><br>";
	if(isset($okx) and $okx == "3")
	{
		echo "<select name='wdxi' id=tipo1>";
		if(isset($wdxiw) and $wdxiw != "")
		{
			$query = "SELECT Codigo, Descripcion   from root_000011 where Codigo = '".$wdxiw."'  order by Descripcion";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($err);
			if($num == 0)
			{
				$query = "SELECT Codigo, Descripcion   from root_000011 where Descripcion like '%".$wdxiw."%'  order by Descripcion";
				$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($err);
			}
			if ($num>0)
			{
				echo "<option value='SELECCIONE'>SELECCIONE</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wdxi=ver($wdxi);
					if($wdxi == $row[0])
						echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}
			}
			else
				echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	else
	{
		echo "<select name='wdxi' id=tipo1>";
		if(isset($wdxi))
		{
			$wdxi=ver($wdxi);
			$query = "SELECT Codigo, Descripcion   from root_000011 where Codigo = '".$wdxi."'  order by Descripcion";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($err);
			echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	echo "</td>";

	echo "<td bgcolor=".$color." align=center>Causa Externa : <br>";
	echo "<select name='wcex' id=tipo1>";
	echo "<option>0-NO APLICA</option>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='12' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wcex=ver($wcex);
			if($wcex == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";

	echo "<td bgcolor=".$color." align=center>Tipo Diagnostico Ppal : <br>";
	echo "<select name='wtdp' id=tipo1>";
	echo "<option>0-NO APLICA</option>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='13' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtdp=ver($wtdp);
			if($wtdp == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";

	if(isset($wcom) and $wcom == "UNCHECKED")
		unset($wcom);
	if(isset($wcom))
		echo "<td bgcolor=".$color." align=center>Complicaciones : <br><input type='checkbox' name='wcom' checked class=tipo4></td>";
	else
		echo "<td bgcolor=".$color." align=center>Complicaciones : <br><input type='checkbox' name='wcom' class=tipo4></td>";
	echo "</td>";
	echo "</tr>";

	//QUINTA LINEA
	echo "<tr>";
	$cal="calendario('wfia','3')";
	echo "<td bgcolor=".$color." colspan=2 align=center>Fecha Inicio Atencion : <br><input type='TEXT' name='wfia' readonly='readonly' id='wfia' size=10 maxlength=10 value='".$wfia."' class=tipo3><button id='trigger3' onclick=".$cal.">...</button>";
	?>
	<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfia',button:'trigger3',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	//]]></script>
	<?php
	echo "</td>";
	$cal="calendario('wfta','4')";
	echo "<td bgcolor=".$color." colspan=3 align=center>Fecha Terminacion Atencion : <br><input type='TEXT' name='wfta' readonly='readonly' id='wfta' size=10 maxlength=10 value='".$wfta."' class=tipo3><button id='trigger4' onclick=".$cal.">...</button>";
	?>
	<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfta',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	//]]></script>
	<?php
	echo "</td>";
	echo "</tr>";

	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($numero))
		$numero=0;
	$id="ajaxquery('1','1','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
	switch ($ok)
	{
		case 1:
			echo "<tr><td bgcolor=#999999 align=center><input type='RADIO' name=ok value=0 onclick=".$id."><b>INICIAR</b></td>";
			echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=1 checked><b>PROCESO</b></td>";
			echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=4 onclick=".$id."><b>MODIFICAR</b></td>";
			echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=3 onclick=".$id."><b>CONSULTAR</b>";
		break;
		case 3:
			echo "<tr><td bgcolor=#999999 align=center><input type='RADIO' name=ok value=0 onclick=".$id."><b>INICIAR</b></td>";
			echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=1><b>PROCESO</b></td>";
			echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=4 onclick=".$id."><b>MODIFICAR</b></td>";
			echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=3 checked onclick=".$id."><b>CONSULTAR</b>";
		break;
	}
	if(isset($ok) and $ok == 3)
	{
		echo "<input type='RADIO' name=wb value=1  onclick=".$id."> Adelante <input type='RADIO' name=wb value=2 onclick=".$id."> Atras";
		if(!isset($wposs))
			$wposs=0;
		echo "<br> Registro Nro. : <input type='TEXT' name='wposs' id='w22' size=5 maxlength=10  value='".$wposs."' OnBlur=".$id." class=tipo3>";
	}
	echo "</td>";
	echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=2 onclick=".$id."><b>GRABAR</b></td></tr>";
	echo "<tr><td align=center bgcolor=#ffffff colspan=5><b> LA CONSULTA DE PACIENTES PUEDE HACERSE POR LOS CAMPOS MARCADOS CON ASTERISCO (*)</b></td></tr></table><br><br></center>";
	if(isset($werr) and isset($e) and $e > -1)
	{
		echo "<br><br><center><table border=0 aling=center>";
		for ($i=0;$i<=$e;$i++)
			if(substr($werr[$i],0,3) == "OK!")
				echo "<tr><td align=center bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color5."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
			else
				echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
		echo "</table><br><br></center>";
	}
	else
		echo "<tr><td align=center bgcolor=#ffffff colspan=5><b>&nbsp</b></td></tr>";
	if($ok == 3)
	{

		//********************************************************************************************************
		//*                                         DATOS DE LOS DIAGNOSTICOS                                    *
		//********************************************************************************************************
		echo "<table border=0 align=center id=tipo2>";

		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=5 face='arial'><b>DIAGNOSTICOS</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><b>BORRAR</b></td><td align=center bgcolor=#999999><b>CODIGO</b></td><td align=center bgcolor=#999999><b>DESCRIPCION</b></font></td><td align=center bgcolor=#999999><b>PRINCIPAL<BR>SECUNDARIO</b></font></td><td align=center bgcolor=#999999><b>DX<BR>NUEVO</b></font></td></tr>";
		echo "<tr>";
		if($whis != "" and $wnin != "")
		{
			if(!isset($wBdx))
				$wBdx="";
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($numero))
				$numero=0;
			$id="ajaxquery('1','5','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
			if(isset($wBdxw) and $wBdxw != "")
				echo "<td bgcolor=".$color7." align=center colspan=2>Criterio Dx : <input type='TEXT' name='wBdxw'  id='w16' size=10 maxlength=30  value='".$wBdxw."' OnBlur=".$id." class=tipo3></td>";
			else
				echo "<td bgcolor=".$color7." align=center colspan=2>Criterio Dx : <input type='TEXT' name='wBdxw'  id='w16' size=10 maxlength=30 OnBlur=".$id." class=tipo3></td>";
			echo "<td bgcolor=".$color7.">";
			if(isset($okx) and $okx == "5")
			{
				if(!isset($querys))
					$querys="";
				else
					$querys=str_replace(" ","%20",$querys);
				if(!isset($wpos))
					$wpos=0;
				if(!isset($numero))
					$numero=0;
				$id="ajaxquery('1','6','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
				echo "<select name='wBdx' OnChange=".$id." id=tipo1>";
				if(isset($wBdxw) and $wBdxw != "")
				{
					$query = "SELECT Codigo, Descripcion   from root_000011 where Codigo = '".$wBdxw."'  order by Descripcion";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$query = "SELECT Codigo, Descripcion   from root_000011 where Descripcion like '%".$wBdxw."%'  order by Descripcion";
						$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($err);
					}
					if ($num>0)
					{
						echo "<option value='SELECCIONE'>SELECCIONE</option>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$wBdx=ver($wBdx);
							if($wBdx == $row[0])
								echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
							else
								echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
						}
					}
					else
						echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				}
				else
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				echo "</select>";
			}
			else
			{
				echo "<select name='wBdx' id=tipo1>";
				if(isset($wBdx))
				{
					$wBdx=ver($wBdx);
					$query = "SELECT Codigo, Descripcion   from root_000011 where Codigo = '".$wBdx."'  order by Descripcion";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row = mysql_fetch_array($err);
					echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}
				else
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				echo "</select>";
			}
			echo "</td>";
			if(isset($wBdxps) and $wBdxps != "")
				echo "<td bgcolor=".$color7." align=center>(P/S) <input type='TEXT' name='wBdxps'  id='w17' size=1 maxlength=1 value='".$wBdxps."' class=tipo3></td>";
			else
				echo "<td bgcolor=".$color7." align=center>(P/S) <input type='TEXT' name='wBdxps'  id='w17' size=1 maxlength=1 class=tipo3></td>";
			echo "<td bgcolor=".$color7." align=center>(S/N)</td>";
			echo "</tr>";
			$query = "SELECT Diacod,Descripcion,Diatip,Dianue from ".$empresa."_000109,root_000011 where Diahis = '".$whis."' and Diaing= '".$wnin."' and codigo=Diacod ";
			$query .= " Order by ".$empresa."_000109.id ";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numdx = mysql_num_rows($err);
			if ($numdx>0)
			{
				$wBdxa=array();
				echo "<input type='HIDDEN' name= 'numdx' value='".$numdx."'>";
				for ($i=0;$i<$numdx;$i++)
				{
					if($i % 2 == 0)
					{
						$wtipo="tipo5";
						$colorR="#99CCFF";
					}
					else
					{
						$wtipo="tipo6";
						$colorR="#dddddd";
					}
					$row = mysql_fetch_array($err);
					$wBdxa[$i]=$row[0];
					echo "<tr>";
					if(!isset($querys))
						$querys="";
					else
						$querys=str_replace(" ","%20",$querys);
					if(!isset($wpos))
						$wpos=0;
					if(!isset($numero))
						$numero=0;
					$id="ajaxquery('1','7','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',".$i.")";
					echo "<td bgcolor=".$colorR." align=center><input type='checkbox' name='deldx[".$i."]' OnClick=".$id." class=".$wtipo."></td>";
					if($colorR == "#99CCFF")
						echo "<td align=center bgcolor=".$colorR."><input type='TEXT' name='wBdxa[".$i."]'  readonly='readonly' size=12 maxlength=12 value='".$row[0]."' class=tipo3></td>";
					else
						echo "<td align=center bgcolor=".$colorR."><input type='TEXT' name='wBdxa[".$i."]'  readonly='readonly' size=12 maxlength=12 value='".$row[0]."' class=tipo3></td>";
					echo "<td bgcolor=".$colorR.">".$row[1]."</td>";
					echo "<td align=center bgcolor=".$colorR.">".$row[2]."</td>";
					echo "<td align=center bgcolor=".$colorR.">".$row[3]."</td>";
					echo "</tr>";
				}
			}
		}
		echo "<tr><td align=center bgcolor=#999999 colspan=5>&nbsp</td></tr>";


		//********************************************************************************************************
		//*                                         DATOS DE LOS PROCEDIMIENTOS                                  *
		//********************************************************************************************************

		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=5 face='arial'><b>PROCEDIMIENTOS</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><b>BORRAR</b></td><td align=center bgcolor=#999999><b>CODIGO</b></td><td align=center bgcolor=#999999 colspan=3><b>DESCRIPCION</b></font></td></tr>";
		echo "<tr>";
		if($whis != "" and $wnin != "")
		{
			if(!isset($wBpr))
				$wBpr="";
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($numero))
				$numero=0;
			$id="ajaxquery('1','8','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
			if(isset($wBprw) and $wBprw != "")
				echo "<td bgcolor=".$color7." align=center colspan=2>Criterio Proce. : <input type='TEXT' name='wBprw' id='w18' size=10 maxlength=30  value='".$wBprw."' OnBlur=".$id." class=tipo3></td>";
			else
				echo "<td bgcolor=".$color7." align=center colspan=2>Criterio Proce. : <input type='TEXT' name='wBprw' id='w18' size=10 maxlength=30 OnBlur=".$id." class=tipo3></td>";
			echo "<td bgcolor=".$color7." colspan=3>";
			if(isset($okx) and $okx == "8")
			{
				if(!isset($querys))
					$querys="";
				else
					$querys=str_replace(" ","%20",$querys);
				if(!isset($wpos))
					$wpos=0;
				if(!isset($numero))
					$numero=0;
				$id="ajaxquery('1','9','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
				echo "<select name='wBpr'  OnChange=".$id." id=tipo1>";
				if(isset($wBprw) and $wBprw != "")
				{
					$query = "SELECT Codigo, Nombre   from root_000012 where Codigo = '".$wBprw."'  order by Nombre";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$query = "SELECT Codigo, Nombre   from root_000012 where Nombre like '%".$wBprw."%'  order by Nombre";
						$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($err);
					}
					if ($num>0)
					{
						echo "<option value='SELECCIONE'>SELECCIONE</option>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$wBpr=ver($wBpr);
							if($wBpr == $row[0])
								echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
							else
								echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
						}
					}
					else
						echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				}
				else
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				echo "</select>";
			}
			else
			{
				echo "<select name='wBpr' id=tipo1>";
				if(isset($wBpr))
				{
					$wBpr=ver($wBpr);
					$query = "SELECT Codigo, Nombre   from root_000012 where Codigo = '".$wBpr."'  order by Nombre";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row = mysql_fetch_array($err);
					echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}
				else
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				echo "</select>";
			}
			echo "</td>";
			$query = "SELECT Procod,Nombre from ".$empresa."_000110,root_000012 where Prohis = '".$whis."' and Proing = '".$wnin."' and codigo=Procod ";
			$query .= " Order by ".$empresa."_000110.id ";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numpr = mysql_num_rows($err);
			if ($numpr>0)
			{
				$wBpra=array();
				echo "<input type='HIDDEN' name= 'numpr' value='".$numpr."'>";
				for ($i=0;$i<$numpr;$i++)
				{
					if($i % 2 == 0)
					{
						$wtipo="tipo5";
						$colorR="#99CCFF";
					}
					else
					{
						$wtipo="tipo6";
						$colorR="#dddddd";
					}
					$row = mysql_fetch_array($err);
					$wBpra[$i]=$row[0];
					echo "<tr>";
					if(!isset($querys))
						$querys="";
					else
						$querys=str_replace(" ","%20",$querys);
					if(!isset($wpos))
						$wpos=0;
					if(!isset($numero))
						$numero=0;
					$id="ajaxquery('1','10','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',".$i.")";
					echo "<td bgcolor=".$colorR." align=center><input type='checkbox' name='delpr[".$i."]' OnClick=".$id." class=".$wtipo."></td>";
					echo "<td align=center bgcolor=".$colorR."><input type='TEXT' name='wBpra[".$i."]'  readonly='readonly' size=15 maxlength=15 value='".$row[0]."' class=tipo3></td>";
					echo "<td bgcolor=".$colorR." colspan=3>".$row[1]."</td>";
					echo "</tr>";
				}
			}
		}
		echo "<tr><td align=center bgcolor=#999999 colspan=5>&nbsp</td></tr>";


		//********************************************************************************************************
		//*                                         DATOS DE LAS ESPECIALIDADES                                  *
		//********************************************************************************************************

		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=5 face='arial'><b>ESPECIALIDADES</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><b>BORRAR</b></td><td align=center bgcolor=#999999><b>CODIGO</b></td><td align=center bgcolor=#999999  colspan=3><b>DESCRIPCION</b></font></td></tr>";
		echo "<tr>";
		if($whis != "" and $wnin != "")
		{
			if(!isset($wBes))
				$wBes="";
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($numero))
				$numero=0;
			$id="ajaxquery('1','11','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
			if(isset($wBesw) and $wBesw != "")
				echo "<td bgcolor=".$color7." align=center colspan=2>Criterio Espec. : <input type='TEXT' name='wBesw' id='w19' size=10 maxlength=30  value='".$wBesw."' OnBlur=".$id." class=tipo3></td>";
			else
				echo "<td bgcolor=".$color7." align=center colspan=2>Criterio Espec. : <input type='TEXT' name='wBesw' id='w19' size=10 maxlength=30 OnBlur=".$id." class=tipo3></td>";
			echo "<td bgcolor=".$color7." colspan=3>";
			if(isset($okx) and $okx == "11")
			{
				if(!isset($querys))
					$querys="";
				else
					$querys=str_replace(" ","%20",$querys);
				if(!isset($wpos))
					$wpos=0;
				if(!isset($numero))
					$numero=0;
				$id="ajaxquery('1','12','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
				echo "<select name='wBes' OnChange=".$id." id=tipo1>";
				if(isset($wBesw) and $wBesw != "")
				{
					$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Selcod = '".$wBesw."' and Seltip='11' and Selest='on' order by Seldes";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seldes like '%".$wBesw."%' and Seltip='11' and Selest='on' order by Seldes";
						$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($err);
					}
					if ($num>0)
					{
						echo "<option value='SELECCIONE'>SELECCIONE</option>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$wBes=ver($wBes);
							if($wBes == $row[0])
								echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
							else
								echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
						}
					}
					else
						echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				}
				else
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				echo "</select>";
			}
			else
			{
				echo "<select name='wBes' id=tipo1>";
				if(isset($wBes))
				{
					$wBes=ver($wBes);
					$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Selcod = '".$wBes."' and Seltip='11' and Selest='on' order by Seldes";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row = mysql_fetch_array($err);
					echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}
				else
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				echo "</select>";
			}
			echo "</td>";
			$query = "SELECT Espcod,Seldes from ".$empresa."_000111,".$empresa."_000105 where Esphis = '".$whis."' and Esping = '".$wnin."' and Espcod=Selcod and Seltip='11' and Selest='on' ";
			$query .= " Order by ".$empresa."_000111.id ";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numes = mysql_num_rows($err);
			if ($numes>0)
			{
				$wBesa=array();
				echo "<input type='HIDDEN' name= 'numes' value='".$numes."'>";
				for ($i=0;$i<$numes;$i++)
				{
					if($i % 2 == 0)
					{
						$wtipo="tipo5";
						$colorR="#99CCFF";
					}
					else
					{
						$wtipo="tipo6";
						$colorR="#dddddd";
					}
					$row = mysql_fetch_array($err);
					$wBesa[$i]=$row[0];
					echo "<tr>";
					if(!isset($querys))
						$querys="";
					else
						$querys=str_replace(" ","%20",$querys);
					if(!isset($wpos))
						$wpos=0;
					if(!isset($numero))
						$numero=0;
					$id="ajaxquery('1','13','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',".$i.")";
					echo "<td bgcolor=".$colorR." align=center><input type='checkbox' name='deles[".$i."]' OnClick=".$id." class=".$wtipo."></td>";
					echo "<td align=center bgcolor=".$colorR."><input type='TEXT' name='wBesa[".$i."]'  readonly='readonly' size=3 maxlength=3 value='".$row[0]."' class=tipo3></td>";
					echo "<td bgcolor=".$colorR." colspan=3>".$row[1]."</td>";
					echo "</tr>";
				}
			}
		}
		echo "<tr><td align=center bgcolor=#999999 colspan=5>&nbsp</td></tr>";

		//********************************************************************************************************
		//*                                         DATOS DE LOS SERVICIOS                                       *
		//********************************************************************************************************

		echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=5 face='arial'><b>SERVICIOS</b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#999999><b>BORRAR</b></td><td align=center bgcolor=#999999><b>CODIGO</b></td><td align=center bgcolor=#999999 colspan=3><b>DESCRIPCION</b></font></td></tr>";
		echo "<tr>";
		if($whis != "" and $wnin != "")
		{
			if(!isset($wBse))
				$wBse="";
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($numero))
				$numero=0;
			$id="ajaxquery('1','14','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
			if(isset($wBsew) and $wBsew != "")
				echo "<td bgcolor=".$color7." align=center colspan=2>Criterio Servi. : <input type='TEXT' name='wBsew' id='w20' size=10 maxlength=30  value='".$wBsew."' OnBlur=".$id." class=tipo3></td>";
			else
				echo "<td bgcolor=".$color7." align=center colspan=2>Criterio Servi. : <input type='TEXT' name='wBsew' id='w20' size=10 maxlength=30 OnBlur=".$id." class=tipo3></td>";
			echo "<td bgcolor=".$color7." colspan=3>";
			if(isset($okx) and $okx == "14")
			{
				if(!isset($querys))
					$querys="";
				else
					$querys=str_replace(" ","%20",$querys);
				if(!isset($wpos))
					$wpos=0;
				if(!isset($numero))
					$numero=0;
				$id="ajaxquery('1','15','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',0)";
				echo "<select name='wBse' OnChange=".$id." id=tipo1>";
				if(isset($wBsew) and $wBsew != "")
				{
					$query = "SELECT Ccocod, Ccodes   from ".$empresa."_000003 where Ccocod = '".$wBsew."'  and (Ccotip='A' or Ccotip='H') order by Ccodes";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$query = "SELECT Ccocod, Ccodes   from ".$empresa."_000003 where Ccodes like '%".$wBsew."%'  and (Ccotip='A' or Ccotip='H') order by Ccodes";
						$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($err);
					}
					if ($num>0)
					{
						echo "<option value='SELECCIONE'>SELECCIONE</option>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$wBse=ver($wBse);
							if($wBse == $row[0])
								echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
							else
								echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
						}
					}
					else
						echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				}
				else
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				echo "</select>";
			}
			else
			{
				echo "<select name='wBse' id=tipo1>";
				if(isset($wBse))
				{
					$wBse=ver($wBse);
					$query = "SELECT Ccocod, Ccodes   from ".$empresa."_000003 where Ccocod = '".$wBse."' order by Ccodes";
					$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row = mysql_fetch_array($err);
					echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}
				else
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				echo "</select>";
			}
			echo "</td>";
			$query = "SELECT Sercod,Ccodes from ".$empresa."_000112,".$empresa."_000003 where Serhis = '".$whis."' and Sering = '".$wnin."' and Sercod=Ccocod ";
			$query .= " Order by ".$empresa."_000112.id ";
			$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numse = mysql_num_rows($err);
			if ($numse>0)
			{
				$wBsea=array();
				echo "<input type='HIDDEN' name= 'numse' value='".$numse."'>";
				for ($i=0;$i<$numse;$i++)
				{
					if($i % 2 == 0)
					{
						$wtipo="tipo5";
						$colorR="#99CCFF";
					}
					else
					{
						$wtipo="tipo6";
						$colorR="#dddddd";
					}
					$row = mysql_fetch_array($err);
					$wBsea[$i]=$row[0];
					echo "<tr>";
					if(!isset($querys))
						$querys="";
					else
						$querys=str_replace(" ","%20",$querys);
					if(!isset($wpos))
						$wpos=0;
					if(!isset($numero))
						$numero=0;
					$id="ajaxquery('1','16','".$wdat."','".$empresa."','".$querys."','".$wpos."','".$numero."',".$i.")";
					echo "<td bgcolor=".$colorR." align=center><input type='checkbox' name='delse[".$i."]' OnClick=".$id." class=".$wtipo."></td>";
					echo "<td align=center bgcolor=".$colorR."><input type='TEXT' name='wBsea[".$i."]'  readonly='readonly' size=4 maxlength=4 value='".$row[0]."' class=tipo3></td>";
					echo "<td bgcolor=".$colorR." colspan=3>".$row[1]."</td>";
					echo "</tr>";
				}
			}
		}
		echo "<tr><td align=center bgcolor=#999999 colspan=5>&nbsp</td></tr></table>";
	}
	echo"</form>";
}
echo "</div>";
?>
</body>
</html>
