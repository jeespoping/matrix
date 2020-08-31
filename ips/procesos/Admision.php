<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>

<html>
<head>
<title>MATRIX Programa de Admision de Pacientes</title>
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
<script src="../../../include/root/jquery-1.3.2.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
     <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo4{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo5{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo6{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo7{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    </style>

</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--

	function actualizarPantallaAdmision(){
		if( window.parent ){
			window.parent.document.forms[0].submit();
		}
	}

	function calendario(id,vrl)
	{
		if (vrl == "1")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfna",button:"trigger1",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
		if (vrl == "2")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfei",button:"trigger2",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
	}

	function reemplazarTodo( text, busca, reemplaza )
	{
		while (text.toString().indexOf(busca) != -1)
		{
			text = text.toString().replace(busca,reemplaza);
		}
		return text;
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

	function ajaxquery(fila,id,wdat,wdep,wnin,weda,empresa,querys,querysR,wpos,wposR,numero,numeroR,wmaxing,wact,idCita,wemp2)
	{

		var x = new Array();
		var s,st,j,st1,contenedor;
		var ajax;
		contenedor = document.getElementById('contenedor');
		querys=unescape(querys);
		querysR=unescape(querysR);

		wdep=reemplazarTodo(wdep,".", " ");

		// VARIABLES TIPO TEXTO
		x[1]  = document.getElementById("w1").value;  // whis
		x[3]  = document.getElementById("w2").value;  // wdoc
		x[5]  = document.getElementById("w3").value;  // wap1
		x[6]  = document.getElementById("w4").value;  // wap2
		x[7]  = document.getElementById("w5").value;  // wno1
		x[8]  = document.getElementById("w6").value;  // wno2
		//x[9]  = document.getElementById("w7").value;  // wfna
		x[9]  = document.getElementById("wfna").value;  // wfna
		x[12] = document.getElementById("w8").value;  // wdir
		x[13] = document.getElementById("w9").value;  // wtel
		x[15] = document.getElementById("w10").value; // wmunw
		x[20] = document.getElementById("w11").value; // wcea
		x[21] = document.getElementById("w12").value; // wnoa
		x[22] = document.getElementById("w13").value; // wtea
		x[23] = document.getElementById("w14").value; // wdia
		x[24] = document.getElementById("w15").value; // wpaa
		//x[25] = document.getElementById("w16").value; // wfei
		x[25] = document.getElementById("wfei").value; // wfei
		x[26] = document.getElementById("w17").value; // whin
		x[32] = document.getElementById("w18").value; // wnitw
		x[36] = document.getElementById("w19").value; // went
		x[37] = document.getElementById("w20").value; // wdie
		x[38] = document.getElementById("w21").value; // wtee
		x[39] = document.getElementById("w22").value; // word
		x[40] = document.getElementById("w23").value; // wpol
		x[41] = document.getElementById("w24").value; // wnco
		x[43] = document.getElementById("w25").value; // wnin
		x[44] = document.getElementById("w26").value; // wdep
		x[45] = document.getElementById("w27").value; // weda
		x[46] = document.getElementById("w28").value; // wofiw
		x[47] = document.getElementById("w29").value; // wbarw
		//agregados campos
		x[50] = document.getElementById("w30").value; // wceu
		x[51] = document.getElementById("w31").value; // wnou
		x[52] = document.getElementById("w32").value; // wteu
		x[53] = document.getElementById("w33").value; // wdiu
		x[54] = document.getElementById("w34").value; // wpau
		x[55] = document.getElementById("w35").value; // wcor


		//VARIABLES DROP-DOWN
		s= document.forms.admision.wtdo;
		x[2] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wtat;
		x[4] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wsex;
		x[10] = s.options[s.selectedIndex].value;
		s= document.forms.admision.west;
		x[11] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wmun;
		x[14] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wbar;
		x[16] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wzon;
		x[17] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wtus;
		x[18] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wofi;
		x[19] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wsei;
		x[27] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wtin;
		x[28] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wcai;
		x[29] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wtpa;
		x[30] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wnit;
		x[31] = s.options[s.selectedIndex].value;
		s= document.forms.admision.wtar;
		x[42] = s.options[s.selectedIndex].value;

		try{
			s= document.forms.admision.wser;
			x[56] = s.options[s.selectedIndex].value;
		}
		catch(e){
			x[56] = '';
		}


		//VARIABLES RADIO
		for (i=0;i<document.forms.admision.ok.length;i++)
		{
			if (document.forms.admision.ok[i].checked==true)
			{
				x[33]=document.forms.admision.ok[i].value;
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
		x[34]=-1;
		x[35]="UNCHECKED";
		if (document.forms.admision.wb)
		{
			for (i=0;i<document.forms.admision.wb.length;i++)
			{
				if (document.forms.admision.wb[i].checked==true)
				{
					x[34]=document.forms.admision.wb[i].value;
					break;
				}
			}
			if (document.forms.admision.change)
			{
				if (document.forms.admision.change.checked)
				{
					x[35]="CHECKED"
				}
				else
				{
					x[35]="UNCHECKED"
				}
			}
			else
			{
				x[35]="UNCHECKED"
			}
		}
		if (document.forms.admision.amb)
		{
			if (document.forms.admision.amb.checked)
			{
				x[48]="CHECKED"
			}
			else
			{
				x[48]="UNCHECKED"
			}
		}
		else
		{
			x[48]="UNCHECKED"
		}
		//st="admision.php?empresa="+empresa+"&ok="+x[33]+"&wdir="+x[12]+"&wtel="+x[13]+"&wmun="+x[14]+"&wmunw="+x[15]+"&wfei="+x[25]+"&whin="+x[26]+"&wdat="+wdat+"&wdep="+x[44]+"&wnin="+x[43];
		st="empresa="+empresa+"&idCita="+idCita+"&wemp2="+wemp2+"&ok="+x[33]+"&wdir="+x[12]+"&wtel="+x[13]+"&wmun="+x[14]+"&wmunw="+x[15]+"&wfei="+x[25]+"&whin="+x[26]+"&wdat="+wdat+"&wdep="+x[44]+"&wnin="+x[43];
		st=st+"&whis="+x[1]+"&wtdo="+x[2]+"&wdoc="+x[3]+"&wtat="+x[4]+"&wap1="+x[5]+"&wap2="+x[6]+"&wno1="+x[7]+"&wno2="+x[8]+"&wfna="+x[9]+"&wsex="+x[10]+"&west="+x[11]+"&wbar="+x[16];
		st=st+"&wzon="+x[17]+"&wtus="+x[18]+"&wofi="+x[19]+"&wcea="+x[20]+"&wnoa="+x[21]+"&wtea="+x[22]+"&wdia="+x[23]+"&wpaa="+x[24]+"&wsei="+x[27];
		st=st+"&wtin="+x[28]+"&wcai="+x[29]+"&wtpa="+x[30]+"&wnit="+x[31]+"&wnitw="+x[32]+"&okx="+id+"&wofiw="+x[46]+"&wbarw="+x[47];
		st=st+"&went="+x[36]+"&wdie="+x[37]+"&wtee="+x[38]+"&wtar="+x[42]+"&word="+x[39]+"&wpol="+x[40]+"&wnco="+x[41]+"&weda="+x[45]+"&amb="+x[48]+"&wceu="+x[50]+"&wnou="+x[51]+"&wteu="+x[52]+"&wdiu="+x[53]+"&wpau="+x[54]+"&wcor="+x[55]+"&wser="+x[56];  //agregados campos
		//st="admision.php?empresa="+empresa+"&t="+t+"&okx="+id+"&wdat="+wdat;
		if (document.forms.admision.wb)
		{
			st=st+"&wb="+x[34];
		}
		if (document.forms.admision.change)
		{
			st=st+"&change="+x[35];
		}
		st=st+"&querys="+querys+"&querysR="+querysR+"&wpos="+wpos+"&wposR="+wposR+"&numero="+numero+"&numeroR="+numeroR+"&wmaxing="+wmaxing+"&wact="+wact;
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
		try{
			ajax.open("POST", "Admision.php",true);
	   		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	   		ajax.send(st);
				//alert("Entra a try");

			ajax.onreadystatechange=function()
			{
				//alert("Entra a onready");
				if (ajax.readyState==4 && ajax.status==200)
				{
					//alert("Entra a readystate");
					//alert(ajax.responseText);
					document.getElementById(+fila).innerHTML=ajax.responseText;
				}
			}
			ajax.send(null);
		}catch(e){}
	}

	function enter()
	{
		document.forms.admision.submit();
	}

	function ira(){document.admision.wdoc.focus();}

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

	function cambiar()
	{

	 var index=document.forms.admision.wtat.value;

	 div = document.getElementById('div_servicio');
	 //alert(index);
		if(index=='M-MEDICINA DOMICILIARIA')
		{
			div.style.display = 'block';
		}
		else
		{
			div.style.display = 'none';
		}

	}

	function validarCampos()
	{

		var cedula1 = $('#w2').val();
		var cedula2 = $('#w11').val();

		if (cedula1 == cedula2)
		{

			var n1=$('#w5').val();
			var n2=$('#w6').val();
			var a1=$('#w3').val();
			var a2=$('#w4').val();


			$('#w11').val($('#w2').val()); //cedula
			$('#w12').val(n1+" "+n2+" "+a1+" "+a2); //nombre
			$('#w13').val($('#w9').val()); //telefono
			$('#w14').val($('#w8').val()); //direccion
			$('#w15').val('NINGUNO'); //parentesco
		}
		// else
		// {

			// $('#w11').val(''); //cedula
			// $('#w12').val(''); //nombre
			// $('#w13').val(''); //telefono
			// $('#w14').val(''); //direccion
			// $('#w15').val(''); //parentesco
		// }
	}

	function validarCampos1()
	{

		var cedula1 = $('#w2').val();
		var cedula2 = $('#w30').val();


		if (cedula1 == cedula2)
		{

			var n1=$('#w5').val();
			var n2=$('#w6').val();
			var a1=$('#w3').val();
			var a2=$('#w4').val();

			$('#w30').val($('#w2').val()); //cedula
			$('#w31').val(n1+" "+n2+" "+a1+" "+a2); //nombre
			$('#w32').val($('#w9').val()); //telefono
			$('#w33').val($('#w8').val()); //direccion
			$('#w34').val('NINGUNO'); //parentesco

		}
		// else
		// {

			// $('#w30').val(''); //cedula
			// $('#w31').val(''); //nombre
			// $('#w32').val(''); //telefono
			// $('#w33').val(''); //direccion
			// $('#w34').val(''); //parentesco

		// }
	}

//-->
</script>
<?php
echo "<div id='1'>";
/**********************************************************************************************************************
	   PROGRAMA : admision.php
	   Fecha de Liberación : 2006-04-08
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2012-11-22

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite grabar la admision de pacientes
	   hospitalarios para los procesos de facturacion y la generacion de RIPS

	   REGISTRO DE MODIFICACIONES :

	   .2016-04-11 camilo zz se adiciona la función que guarda log cuando se hace un egreso por este programa buscar logAdmsiones
	   .2013-03-01 Se inicializan las variables fecha y hora del ingreso para que las saque cuando la historia este vacia.
	   .2012-11-22 Se hace verificacion del campo pactam que es el tipo de atencion cuando la admision es para atencion domiciliaria.
	   .2012-11-16 Se organiza la validacion del correo electronico para que permita - en los correos electronicos. Viviana Rodas
	   .2012-09-03 Se modifica la funcion validarCampos para que no se borren los datos de responsable del usuario y datos del acompañante cuando
					la cedula no sea igual.
	   .2012-09-03 Se cambia responsable del usuario y datos del acompañante para que solo se llenen cuando la cedula ingresada sea igual
					a la cedula del paciente con las funciones validarCampos y validarCampos1. Viviana Rodas
	   .2012-08-30 Se agrega el campo paccor de correo electronico del paciente. Viviana Rodas
	   .2012-08-29 Se agregan los campos pacnou, pacpau, pacdiu, pacteu que son los datos del responsable del usuario. Viviana Rodas
	   .2012-08-26 Se agrega entidad responsable y datos del acompañante, tambien se agranda la letra. Viviana Rodas
	   .2012-08-16
			Se modifica el programa para que solo tenga en cuenta las empresas activas.

	   .2010-08-09
	   		Se modifica el programa para que el llamado del ajaxquery no cancele cuando el nombre del departamento tiene espacios
	   		en blanco. El llamado ajax se coloco dentro de un bloque try catch.

	   .2010-01-20
	   		Se modifica el programa para que al momento de hacer el ingreso a un paciente, el nro de documento aperezca en la cita
	   		en el caso de que el paciente tenga una. Ademas si el paciente fue llamado desde la pantalla de admision, aparece en rojo
	   		el nombre del paciente que tiene la cita para que el usuario pueda verificar los datos ingresados

	   .2008-07-30
	   		Se modifico el programa para corregir la validacion de pacientes con cartera particular pendiente solo tuviera en cuenta
			las facturas activas es decir con Fenest='on'. Una de las validaciones NO contemplaba el estado de la factura.

	   .2008-07-16
	   		Se modifico el programa para que la validacion de pacientes con cartera particular pendiente solo tuviera en cuenta
			las facturas activas es decir con Fenest='on'.

	   .2008-07-14
	   		Se modifico el programa para que validara en la tabla 18 (Encabezado de Facturas) si el paciente tiene cartera particular
			pendiente. La validacion se hace despues de digitar la cedula y se repite en el momento de la grabacion.

	   .2008-03-11
	   		Se modifico el programa para que validara que la fecha del ingreso no fuera posterior a la fecha del sistema.

	   .2007-10-03
	   		Se modifico el programa para incluir en la funcion valgen de validacion de datos, la revision de la historia clinica
	   		del paciente si este ya existe en la base de datos e impedir que el usuario la cambie, generando ingresos y/o Egresos
	   		ambulatorios con otro numero de historia que no corresponde con los datos originales del paciente.

	   .2007-07-16
	   		Se modifico el programa para incluir una funcion que verificara los datos del paciente en el nucleo del sistema
	   		y los ingresara o actualizara.

	   .2007-07-05
	   		Se modifico el programa para organizar la consulta de ingresos x ID y no por numero de ingreso ya que al ser char
	   		no organiza en orden numerico.
	   		Se cambian las lineas de codigo 887 - 1897 - 1903 - 2016 - 2018.

	   .2007-06-19
	   		Se modifico el programa para incluir la subrrutina de validacion de año bisiesto que no habia sido incluida.

	   .2007-03-05
	   		Se modifico el programa para que los pacientes particulares pudieran ingresarse con cualquier tarifa diferente de la
	   		particular, dejando esta como default.

	   .2006-02-12
	   		Se modifico el programa ya que cuando el paciente ingresaba por primera vez no estaba operando la opcion de ambulatorio.

	   .2006-12-26
	   		Se cambia en el programa la validacion del numero historia clinica para impedir que se graben registros en la tabla 101
	   		con este numero en nulo.

	   .2006-12-15
	   		Se cambia en el programa los tipos de de los campos de historia y nro de ingreso de integer a varchar.

	   .2006-12-05
	   		Se cambia el metodo de validacion para el Nro. de Orden, Nro. de poliza y Nro. de Contrato de las variables del programa
	   		a los nombres de los campos de la tabla 101 (Ingord Ingpol Ingnco)

	   .2006-11-30
	   		Se amplia el algoritmo de validacion pra verificar los campos de Nro. de Orden, Nro. de poliza y Nro. de Contrato
	   		cuando estos sean obligatorios dependiendo de la empresa. Estos parametros se deben validar de acuerdo a la informacion
	   		almacenada en la tabla 121 de CAMPOS OBLIGATORIOS X EMPRESA.

	   .2006-11-29
	   		Se cambia el Metodo de llenado de informacion del acudiente para que no borre la ya digitada cuando el paciente
	   		ingresa por primera vez a la clinica.

	   .2006-11-22
	   		Se cambia el Metodo del AJAX de GET a POS para evitar problemas con caracteres especiales como la Ñ.

	   .2006-11-20
	   		Se adiciono el archivo 120 de Parametros de Ingreso Ambulatorio x Centro de Costos con el proposito de parametrizar
	   		la admision ambulatoria e independizarla del codigo.
	   		Se cambio la ubicacion del HREF "Hipervinculo" para impresion de hoja de Ingreso - Egreso en Consulta, ya que la anterior
	   		no funcionaba correctamente.

	   .2006-11-09
	   		Se modifica el programa para que se especifique si un ingreso es Ambulatorio. Para esto se agrega un CheckBox
	   		de nombre $amb. Si el usuario prende esta variable el programa automaticamente graba informacion en las tablas
	   		108 Egreso de Pacientes : #Hist, #Ing, Med_Ing=07, Dx=Z008, Fecha, Hora, Estancia=0, Tipo=A, Med_Egr=07, Est=on
	   		109 Detalle de Diagnosticos : #Hist, #Ing, Dx=Z008, Tipo=P, Nuevo=N
	   		110 Detalle de Procedimientos : #Hist, #Ing, Procedimiento=890305
	   		111 Detalle de Especialidades : #Hist, #Ing, Especialidad=033
	   		112 Detalle de Servicios : #Hist, #Ing, Servicio=1200

	   .2006-09-25
	   		Se modifica el programa para que asocie la tarifa correspondiente a la empresa seleccionada o "01"
	   		si es particular.

	   .2006-09-19
	   		Se modifica el programa para NO volver a a buscar el municipio y el barrio si el criterio de busqueda no ha
	   		cambiado.

	   .2006-09-18
	   		Se adiciona a la tabla 105 el campo de Prioridad Selpri para mostrar en este orden las distintas selecciones.
	   		Se modifica el calculo de la edad para hacerlo mas exacto.

	   .2006-08-22
	   		Se adiciona a la seleccion de servicios de ingreso los centros de costos tipo 'A' o de Admision de pacientes.
	   		Se cambia el orden de tipo de paciente por codigo.

	   .2006-07-12
	   		Se colocan tags <br> en los datos del paciente par mejor organizacion de la pantalla.

	   .2006-05-30
	   		Se coloca en la seleccion de empresa la opcion (0-NO APLICA) para escoger la adecuada.

	   .2006-05-30
	   		En el ingreso se eliminaron los campos de medico tratante y diagnostico de ingreso y se adicionaron los
	   		campos de Numero de Orden(ord), Numero de Poliza(pol) y Numero de Contrato(nco).

	   .2006-04-08
	   		Release de Versión Beta.

***********************************************************************************************************************/

//MUESTRA LA INFORMACION DEL PACIENTE CON CITA PREVIA 2010-01-19
function pintarInfoPaciente( $id ){

	global $conex;
	global $wemp2;
	global $cedula;

	$sql = "SELECT
				 *
			FROM
				{$wemp2}_000009
			WHERE
				id = '$id'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){

		echo "<table align='center'>";
		echo "<tr align=center>";
		echo "<td colspan='3'><font color='red'><b>Paciente con Cita:</b></font></td>";
		echo "</tr>";
		echo "<tr align=center>";
		echo "<td><font color='red'><b>{$rows['Cedula']}&nbsp;</b></font></td>";
		echo "<td><font color='red'><b> - </b></font></td>";
		echo "<td><font color='red'><b>&nbsp;{$rows['Nom_pac']}</b></font></td>";
		echo "</tr>";
		echo "</table>";
		$cedula=$rows['Cedula'];
	}
}

//RUTINA CREADA EN MODIFICACION 2010-01-15
function actualizarCita( $emp, $conex, $nrodoc ){

	global $idCita;


	if( !empty($emp) && !empty($idCita) ){

		$sql = "UPDATE
					{$emp}_000009
				SET
		 			cedula = '$nrodoc',
		 			atendido = 'on',
					asistida = 'on'

		 		WHERE
		 			id = '$idCita'";

		 $res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );

		 if( mysql_affected_rows() > 0 ){
		 	echo "<script>actualizarPantallaAdmision();</script>";
		 	return true;
		 }
		 else{
		 	return false;
		 }
	}
}

// RUTINA CREADA EN MODIFICACION 2007-07-12
function nucleo($conex,$wtdox,$wdocx,$wno1x,$wno2x,$wap1x,$wap2x,$wfnax,$wsexx,$whisx,$wninx)
{
	global $empresa;
	$query = "select Empcod from  root_000050 where Empbda='".$empresa."' ";
	$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO EMPRESAS DEL GRUPO : ".mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$row = mysql_fetch_array($err);
		$codemp = $row[0];			//tabla unica de pacientes
		$query = "select Pacced  from  root_000036 where Pactid='".$wtdox."' and Pacced='".$wdocx."' ";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO NUCLEO DE PACIENTES : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$query =  " update root_000036 set ";
			$query .=  "  Pacap1='".$wap1x."',";
			$query .=  "  Pacap2='".$wap2x."',";
			$query .=  "  Pacno1='".$wno1x."',";
			$query .=  "  Pacno2='".$wno2x."',";
			$query .=  "  Pacnac='".$wfnax."',";
			$query .=  "  Pacsex='".$wsexx."' ";
			$query .=  "  where Pactid='".$wtdox."' and Pacced='".$wdocx."'";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO NUCLEO DE PACIENTES : ".mysql_errno().":".mysql_error());
				//origen historia del paciente
			$query = "select Oriced from root_000037 where Oritid='".$wtdox."' and Oriced='".$wdocx."' and Oriori='".$codemp."'";
			$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO NUCLEO DE PACIENTES : ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$query =  " update root_000037 set ";
				$query .=  "  Orihis='".$whisx."',";
				$query .=  "  Oriing='".$wninx."' ";
				$query .=  "  where Oritid='".$wtdox."' and Oriced='".$wdocx."' and Oriori='".$codemp."'";
				$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO NUCLEO DE HISTORIAS : ".mysql_errno().":".mysql_error());
			}
			else
			{
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert root_000037 (medico,fecha_data,hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, seguridad) values ('root','".$fecha."','".$hora."','".$wdocx."','".$wtdox."','".$whisx."','".$wninx."','".$codemp."','C-root')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN NUCLEO DE HISTORIAS : ".mysql_errno().":".mysql_error());
			}
		}
		else //no esta en la tabla unica de pacientes
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert root_000036 (medico,fecha_data,hora_data, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, seguridad) values ('root','".$fecha."','".$hora."','".$wdocx."','".$wtdox."','".$wno1x."','".$wno2x."','".$wap1x."','".$wap2x."','".$wfnax."','".$wsexx."','C-root')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN ARCHIVO NUCLEO DE PACIENTES : ".mysql_errno().":".mysql_error());
			$query = "insert root_000037 (medico,fecha_data,hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, seguridad) values ('root','".$fecha."','".$hora."','".$wdocx."','".$wtdox."','".$whisx."','".$wninx."','".$codemp."','C-root')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN NUCLEO DE HISTORIAS : ".mysql_errno().":".mysql_error());
		}
		return true;
	}
	else
		return false;
}
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$/";
	if (preg_match($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar2($chain)
{
	// Funcion que permite validar la estructura de un numero Entero
	$regular="/^(\+|-)?([[:digit:]]+)$/";
	if (preg_match($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
function validar3($chain)
{
	// Funcion que permite validar la estructura de una fecha
	$fecha="/^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$/";
	if(preg_match($fecha,$chain,$occur))
	{
		if($occur[2] < 0 or $occur[2] > 12)
			return false;
		if(($occur[3] < 0 or $occur[3] > 31) or
		  ($occur[2] == 4 and  $occur[3] > 30) or
		  ($occur[2] == 6 and  $occur[3] > 30) or
		  ($occur[2] == 9 and  $occur[3] > 30) or
		  ($occur[2] == 11 and $occur[3] > 30) or
		  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or
		  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
			return false;
		return true;
	}
	else
		return false;
}

function validar7($chain)
{

		// Funcion que permite validar la estructura de un correo electronico
		$regular="/^[^0-9][a-zA-Z0-9_-]+([.][a-zA-Z0-9_-]+)*[@][a-zA-Z0-9_-]+([.][a-zA-Z0-9_-]+)*[.][a-zA-Z]{2,4}$/";
		return (preg_match($regular,$chain));

}

function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="/^([=a-zA-Z0-9' 'ñÑ@\/#-.;_<>])+$/";
	return (preg_match($regular,$chain));
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
	if(preg_match($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >23 or $occur[2]<0 or $occur[2]>59)
			return false;
		else
			return true;
	else
		return false;
}
//agregados campos
function valgen($conex,$wtdox,$wdocx,$wap1x,$wno1x,$wfnax,$whisx,$whinx,$wdirx,$wtelx,$wmunx,$wbarx,$wdepx,$wofix,$wceax,$wnoax,$wteax,$wdiax,$wpaax,$wninx,$wfeix,$wtpax,$wnitx,$wentx,$wordx,$wpolx,$wncox,$wdiex,$wteex,&$werr,&$e,$wceux,$wnoux,$wteux,$wdiux,$wpaux,$wcorx)
{

	global $empresa;
	//VALIDACION DE DATOS GENERALES
	if(!validar4($wdocx))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  NUMERO DE DOCUMENTO";
	}
	if($e == -1)
	{
		$query = "select Pachis  from  ".$empresa."_000100 where Pactdo='".substr($wtdox,0,strpos($wtdox,"-"))."' and Pacdoc='".$wdocx."' ";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PACIENTES : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			if($row[0] != $whisx)
			{
				$e=$e+1;
				$werr[$e]="ERROR O NO PUEDE CAMBIAR EL NUMERO DE HISTORIA CLINICA";
			}
		}
	}
	if(!validar4($wap1x))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO   PRIMER APELLIDO";
	}
	if(!validar4($wno1x))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO   PRIMER NOMBRE";
	}
	if(!validar3($wfnax))
	{
		$e=$e+1;
		$werr[$e]="FECHA DE NACIMIENTO INCORRECTA";
	}
	if(!validar4($wdirx))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  DIRECCION DEL PACIENTE";
	}
	if(!validar4($wtelx))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  TELEFONO DEL PACIENTE";
	}
	if(!validar4($wmunx) or $wmunx == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  MUNICIPIO";
	}
	if(!validar4($wbarx) or $wbarx == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  BARRIO";
	}
	if(!validar4($wdepx) or $wdepx == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  MUNICIPIO Y DEPARTAMENTO";
	}
	if(!validar4($wofix) or $wofix == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO OFICIO U OCUPACION + ".$wofix." -->";
	}
	if(!validar4($wceax))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  CEDULA DEL ACOMPA&Ntilde;ANTE";
	}
	if(!validar4($wnoax))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  NOMBRE DEL ACOMPA&Ntilde;ANTE";
	}
	if(!validar4($wteax))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  TELEFONO DEL ACOMPA&Ntilde;ANTE";
	}
	if(!validar4($wdiax))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  DIRECCION DEL ACOMPA&Ntilde;ANTE";
	}
	if(!validar4($wpaax))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  PARENTESCO DEL ACOMPA&Ntilde;ANTE";
	}
	//agregados campos
	if(!validar4($wceux))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  CEDULA DEL RESPONSABLE DEL USUARIO";
	}
	if(!validar4($wnoux))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  NOMBRE DEL RESPONSABLE DEL USUARIO";
	}
	if(!validar4($wteux))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  TELEFONO DEL RESPONSABLE DEL USUARIO";
	}
	if(!validar4($wdiux))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  DIRECCION DEL RESPONSABLE DEL USUARIO";
	}
	if(!validar4($wpaux))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  PARENTESCO DEL RESPONSABLE DEL USUARIO";
	}
	//validacion de correo electronico

	if ($wcorx)
	{
		if(!validar7($wcorx))
		{
			$e=$e+1;
			$werr[$e]="ERROR EN EL CORREO ELECTRONICO";
		}
	}
	else
	{
		$wcorx="";
	}
	//VALIDACION DE DATOS DE INGRESO
	// MODIFICACION DE VALIDACION DE LA FECHA  NO MAYOR QUE LA FECHA DEL SISTEMA
	if(!validar3($wfeix) or $wfeix > date("Y-m-d"))
	{
		$e=$e+1;
		$werr[$e]="FECHA DE INGRESO INCORRECTA O MAYOR QUE LA FECHA DEL SISTEMA";
	}
	if(!validar6($whinx))
	{
		$e=$e+1;
		$werr[$e]="HORA DE INGRESO INCORRECTA";
	}
	if(!validar4($wtpax) or $wtpax == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  EL TIPO DE PACIENTE";
	}
	if(!validar4($wnitx) or $wnitx == "0-NO APLICA")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  NIT DEL RESPONSABLE";
	}
	if(!validar4($wentx))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  NOMBRE DEL RESPONSABLE";
	}
	if(!validar4($wdiex))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  DIRECCION DEL RESPONSABLE";
	}
	if(!validar4($wteex))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO  TELEFONO DEL RESPONSABLE";
	}							//campos obligatorios por empresa
	$query = "select *  from  ".$empresa."_000121 where Cobcod='".substr($wnitx,0,strpos($wnitx,"-"))."' and Cobnta='Ingord' and Cobest = 'on' ";
	$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE CAMPOS OBLIGATORIOS X EMPRESA : ".mysql_errno().":".mysql_error());
	$num1 = mysql_num_rows($err1);
	if ($num1 > 0 and strlen($wordx) == 1)
	{
		$e=$e+1;
		$werr[$e]="NO DIGITO EL NRo. DE ORDEN Y ES OBLIGATORIO PARA ESTA EMPRESA";
	}
	$query = "select *  from  ".$empresa."_000121 where Cobcod='".substr($wnitx,0,strpos($wnitx,"-"))."' and Cobnta='Ingpol' and Cobest = 'on' ";
	$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE CAMPOS OBLIGATORIOS X EMPRESA : ".mysql_errno().":".mysql_error());
	$num1 = mysql_num_rows($err1);
	if ($num1 > 0 and strlen($wpolx) == 1)
	{
		$e=$e+1;
		$werr[$e]="NO DIGITO EL NRo. DE POLIZA Y ES OBLIGATORIO PARA ESTA EMPRESA";
	}
	$query = "select *  from  ".$empresa."_000121 where Cobcod='".substr($wnitx,0,strpos($wnitx,"-"))."' and Cobnta='Ingnco' and Cobest = 'on' ";
	$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE CAMPOS OBLIGATORIOS X EMPRESA : ".mysql_errno().":".mysql_error());
	$num1 = mysql_num_rows($err1);
	if ($num1 > 0 and strlen($wncox) == 1)
	{
		$e=$e+1;
		$werr[$e]="NO DIGITO EL NRo. DE CONTRATO Y ES OBLIGATORIO PARA ESTA EMPRESA";
	}
	if($e == -1)
		return true;
	else
		return false;
}
//agregados los campos
function MOD_ING($conex,$whisx,$wtdox,$wdocx,$wtatx,$wap1x,$wap2x,$wno1x,$wno2x,$wfnax,$wsexx,$westx,$wdirx,$wtelx,$wmunx,$wbarx,$wdepx,$wzonx,$wtusx,$wofix,$wceax,$wnoax,$wteax,$wdiax,$wpaax,$wninx,$wfeix,$whinx,$wseix,$wtinx,$wcaix,$wtpax,$wnitx,$wentx,$wordx,$wpolx,$wncox,$wdiex,$wteex,$wtarx,&$werr,&$e,$wceux,$wnoux,$wteux,$wdiux,$wpaux,$wcorx,$wserx)
{
	global $empresa;
	$query = "select Pachis  from  ".$empresa."_000100 where Pactdo='".substr($wtdox,0,strpos($wtdox,"-"))."' and Pacdoc='".$wdocx."' and Pacact = 'on' ";
	$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PACIENTES : ".mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$query = "select Inghis  from  ".$empresa."_000101 where Inghis='".$whisx."' and Ingnin='".$wninx."'";
		$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO DETALLE DE INGRESOS : ".mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			//actualizacion tabla clisur_000100 pacientes activos
			$query =  " update ".$empresa."_000100 set Pactat = '".substr($wtatx,0,strpos($wtatx,"-"))."',";
			$query .=  "  Pacap1='".$wap1x."',";
			$query .=  "  Pacap2='".$wap2x."',";
			$query .=  "  Pacno1='".$wno1x."',";
			$query .=  "  Pacno2='".$wno2x."',";
			$query .=  "  Pacfna='".$wfnax."',";
			$query .=  "  Pacsex='".substr($wsexx,0,strpos($wsexx,"-"))."',";
			$query .=  "  Pacest='".substr($westx,0,strpos($westx,"-"))."',";
			$query .=  "  Pacdir='".$wdirx."',";
			$query .=  "  Pactel='".$wtelx."',";
			$query .=  "  Paciu='".substr($wmunx,0,strpos($wmunx,"-"))."',";
			$query .=  "  Pacbar='".substr($wbarx,0,strpos($wbarx,"-"))."',";
			$query .=  "  Pacdep='".substr($wdepx,0,strpos($wdepx,"-"))."',";
			$query .=  "  Paczon='".substr($wzonx,0,strpos($wzonx,"-"))."',";
			$query .=  "  Pactus='".substr($wtusx,0,strpos($wtusx,"-"))."',";
			$query .=  "  Pacofi='".substr($wofix,0,strpos($wofix,"-"))."',";
			$query .=  "  Paccea='".$wceax."',";
			$query .=  "  Pacnoa='".$wnoax."',";
			$query .=  "  Pactea='".$wteax."',";
			$query .=  "  Pacdia='".$wdiax."',";
			$query .=  "  Pacpaa='".$wpaax."',";
			$query .=  "  Pacact='on',";
			$query .=  "  Paccru='".$wceux."',"; //agregado
			$query .=  "  Pacnru='".$wnoux."',";
			$query .=  "  Pactru='".$wteux."',";
			$query .=  "  Pacdru='".$wdiux."',";
			$query .=  "  Pacpru='".$wpaux."',";
			$query .=  "  Paccor='".$wcorx."',";
			$query .=  "  Pactam='".$wserx."' ";
			$query .=  "  where Pactdo='".substr($wtdox,0,strpos($wtdox,"-"))."' and Pacdoc='".$wdocx."'";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO PACIENTES : ".mysql_errno().":".mysql_error());
			//actualizacion tabla clisur_000101 tabla ingresos
			$query =  " update ".$empresa."_000101 set Ingfei = '".$wfeix."',";
			$query .=  "  Inghin ='".$whinx."',";
			$query .=  "  Ingsei ='".substr($wseix,0,strpos($wseix,"-"))."',";
			$query .=  "  Ingtin ='".substr($wtinx,0,strpos($wtinx,"-"))."',";
			$query .=  "  Ingcai ='".substr($wcaix,0,strpos($wcaix,"-"))."',";
			$query .=  "  Ingtpa ='".substr($wtpax,0,strpos($wtpax,"-"))."',";
			$query .=  "  Ingcem ='".substr($wnitx,0,strpos($wnitx,"-"))."',";
			$query .=  "  Ingent ='".$wentx."',";
			$query .=  "  Ingord ='".$wordx."',";
			$query .=  "  Ingpol ='".$wpolx."',";
			$query .=  "  Ingnco ='".$wncox."',";
			$query .=  "  Ingdie ='".$wdiex."',";
			$query .=  "  Ingtee ='".$wteex."',";
			$query .=  "  Ingtar ='".substr($wtarx,0,strpos($wtarx,"-"))."' ";
			$query .=  "  where Inghis='".$whisx."' and Ingnin='".$wninx."'";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO INGRESO : ".mysql_errno().":".mysql_error());
			$e=$e+1;
			$werr[$e]="OK! PACIENTE ACTUALIZADO E INGRESO ACTUALIZADO -HISTORIA : ".$whisx." -INGRESO : ".$wninx;
			// RUTINA CREADA EN MODIFICACION 2007-07-12
			if(!Nucleo($conex,substr($wtdox,0,strpos($wtdox,"-")),$wdocx,$wno1x,$wno2x,$wap1x,$wap2x,$wfnax,substr($wsexx,0,strpos($wsexx,"-")),$whisx,$wninx))
			{
				$e=$e+1;
				$werr[$e]="NUCLEO NO SE ACTUALIZO PARA : HISTORIA : ".$whisx." INGRESO : ".$wninx;
			}
			return true;
		}  	//no tiene ingreso
		else
		{
			$e=$e+1;
			$werr[$e]="INGRESO NO EXISTE ";
			return false;
		}

	}   //no esta activo
	else
	{
		$e=$e+1;
		$werr[$e]="EL PACIENTE FUE INACTIVADO";
		return false;
	}
}
 //agregados los campos
function ACT_ING($conex,$whisx,$wtdox,$wdocx,$wtatx,$wap1x,$wap2x,$wno1x,$wno2x,$wfnax,$wsexx,$westx,$wdirx,$wtelx,$wmunx,$wbarx,$wdepx,$wzonx,$wtusx,$wofix,$wceax,$wnoax,$wteax,$wdiax,$wpaax,$wninx,$wfeix,$whinx,$wseix,$wtinx,$wcaix,$wtpax,$wnitx,$wentx,$wordx,$wpolx,$wncox,$wdiex,$wteex,$wtarx,$wambx,&$werr,&$e,$wceux,$wnoux,$wteux,$wdiux,$wpaux,$wcorx,$wserx)
{
	global $empresa;
	global $wemp2;	//2010-01-15
	global $key;	//2010-01-19

	//se buscan los inactivos
	$query = "select Pachis  from  ".$empresa."_000100 where Pactdo='".substr($wtdox,0,strpos($wtdox,"-"))."' and Pacdoc='".$wdocx."' and Pacact = 'off' ";
	$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PACIENTES : ".mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		if($wambx == "SI")
		{				//parametros ingreso paciente ambulatorio
			$query = "select Parmei, Pardxi, Pareta, Parcae, Parmee, Partdx, Pardxn, Parpro, Paresp, Parser from ".$empresa."_000120 where Parcco='".substr($wseix,0,strpos($wseix,"-"))."' and parest='on' ";
			$errA = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PARAMETROS INGRESO AMBULATORIO : ".mysql_errno().":".mysql_error());
			$numA = mysql_num_rows($errA);
			if ($numA > 0)
				$rowA = mysql_fetch_array($errA);
		}
			//si no es ambulatorio, se busca en ingresos
		$query = "select Inghis  from  ".$empresa."_000101 where Inghis='".$whisx."' and Ingnin='".$wninx."'";
		$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO DETALLE DE INGRESOS : ".mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if(($num1 == 0 and $wambx != "SI" and $whisx != "") or ($num1 == 0 and $wambx == "SI" and $numA > 0 and $whisx != ""))
		{
			//actualizacion tabla clisur_000100 pacientes activos
			$query =  " update ".$empresa."_000100 set Pactat = '".substr($wtatx,0,strpos($wtatx,"-"))."',";
			$query .=  "  Pacap1='".$wap1x."',";
			$query .=  "  Pacap2='".$wap2x."',";
			$query .=  "  Pacno1='".$wno1x."',";
			$query .=  "  Pacno2='".$wno2x."',";
			$query .=  "  Pacfna='".$wfnax."',";
			$query .=  "  Pacsex='".substr($wsexx,0,strpos($wsexx,"-"))."',";
			$query .=  "  Pacest='".substr($westx,0,strpos($westx,"-"))."',";
			$query .=  "  Pacdir='".$wdirx."',";
			$query .=  "  Pactel='".$wtelx."',";
			$query .=  "  Paciu='".substr($wmunx,0,strpos($wmunx,"-"))."',";
			$query .=  "  Pacbar='".substr($wbarx,0,strpos($wbarx,"-"))."',";
			$query .=  "  Pacdep='".substr($wdepx,0,strpos($wdepx,"-"))."',";
			$query .=  "  Paczon='".substr($wzonx,0,strpos($wzonx,"-"))."',";
			$query .=  "  Pactus='".substr($wtusx,0,strpos($wtusx,"-"))."',";
			$query .=  "  Pacofi='".substr($wofix,0,strpos($wofix,"-"))."',";
			$query .=  "  Paccea='".$wceax."',";
			$query .=  "  Pacnoa='".$wnoax."',";
			$query .=  "  Pactea='".$wteax."',";
			$query .=  "  Pacdia='".$wdiax."',";
			$query .=  "  Pacpaa='".$wpaax."',";
			$query .=  "  Pacact='on', ";
			$query .=  "  Paccru='".$wceux."',"; //agregado
			$query .=  "  Pacnru='".$wnoux."',";
			$query .=  "  Pactru='".$wteux."',";
			$query .=  "  Pacdru='".$wdiux."',";
			$query .=  "  Pacpru='".$wpaux."',";
			$query .=  "  Paccor='".$wcorx."', ";
			$query .=  "  Pactam='".$wserx."' ";
			$query .=  "  where Pactdo='".substr($wtdox,0,strpos($wtdox,"-"))."' and Pacdoc='".$wdocx."'";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO PACIENTES : ".mysql_errno().":".mysql_error());
			//inserta en la tabla clisur_000101 ingresos
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000101 (medico,fecha_data,hora_data, Inghis, Ingnin, Ingfei, Inghin, Ingsei, Ingtin, Ingcai, Ingtpa, Ingcem, Ingent, Ingord, Ingpol, Ingnco, Ingdie, Ingtee, Ingtar, Ingusu, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$wfeix."','".$whinx."','".substr($wseix,0,strpos($wseix,"-"))."','".substr($wtinx,0,strpos($wtinx,"-"))."','".substr($wcaix,0,strpos($wcaix,"-"))."','".substr($wtpax,0,strpos($wtpax,"-"))."','".substr($wnitx,0,strpos($wnitx,"-"))."','".$wentx."','".$wordx."','".$wpolx."','".$wncox."','".$wdiex."','".$wteex."','".substr($wtarx,0,strpos($wtarx,"-"))."', '$key','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO INGRESO : ".mysql_errno().":".mysql_error());
			if($wambx == "SI")
			{
				$query = "insert ".$empresa."_000108 (medico,fecha_data,hora_data, Egrhis, Egring, Egrmei, Egrdxi, Egrfee, Egrhoe, Egrest, Egrcae, Egrmee, Egrcom, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[0]."','".$rowA[1]."','".$fecha."','".$hora."',".$rowA[2].",'".$rowA[3]."','".$rowA[4]."','off','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 108 EGRESOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
				$query = "insert ".$empresa."_000109 (medico,fecha_data,hora_data, Diahis, Diaing, Diacod, Diatip, Dianue, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[1]."','".$rowA[5]."','".$rowA[6]."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 DIAGNOSTICOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
				$query = "insert ".$empresa."_000110 (medico,fecha_data,hora_data, Prohis, Proing, Procod, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[7]."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 PROCEDIMIENTOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
				$query = "insert ".$empresa."_000111 (medico,fecha_data,hora_data, Esphis, Esping, Espcod, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[8]."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 ESPECIALIDADES AMBULATORIAS : ".mysql_errno().":".mysql_error());
				$query = "insert ".$empresa."_000112 (medico,fecha_data,hora_data, Serhis, Sering, Sercod, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[9]."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 SERVICIOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
				$query = "UPDATE ".$empresa."_000100 set Pacact='off' where Pachis = '".$whisx."'";
				$err1 = mysql_query($query,$conex) or die("ERROR INACTIVANDO PACIENTE EN ARCHIVO 100 : ".mysql_errno().":".mysql_error());
			}
			$e=$e+1;
			$werr[$e]="OK! PACIENTE ACTUALIZADO E INGRESO GRABADO -HISTORIA : ".$whisx." -INGRESO : ".$wninx;
			//2010-01-15

			actualizarCita( $wemp2, $conex, $wdocx  );
			// RUTINA CREADA EN MODIFICACION 2007-07-12
			if(!Nucleo($conex,substr($wtdox,0,strpos($wtdox,"-")),$wdocx,$wno1x,$wno2x,$wap1x,$wap2x,$wfnax,substr($wsexx,0,strpos($wsexx,"-")),$whisx,$wninx))
			{
				$e=$e+1;
				$werr[$e]="NUCLEO NO SE ACTUALIZO PARA : HISTORIA : ".$whisx." INGRESO : ".$wninx;
			}
			$query = "select count(*),SUM(Fensal) from  ".$empresa."_000018 where Fendpa='".$wdocx."' and  Fensal > 0 and Fentip='01-PARTICULAR'  and Fenest='on' ";
			$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO TABLA 18 : ".mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			if($row[0] > 0)
			{
				$e=$e+1;
				$werr[$e]="EL PACIENTE CON IDENTIFICACION : ".$wdocx." TIENE UN SALDO DE $".$row[1]." EN CARTERA PARTICULAR PENDIENTE. FAVOR REVISAR!!!!";
			}
			return true;
		}
		else
		{
			$e=$e+1;
			$werr[$e]="INGRESO YA EXISTE O FUE GRABADO X OTRA PANTALLA<br> O EL INGRESO ES AMBULATORIO Y EL SERVICIO DE INGRESO NO TIENE PARAMETROS<br> O LA CEDULA DEL PACIENTE EXISTE Y EL NUMERO DE HISTORIA ESTA EN NULO";
			return false;
		}

	}
	else
	{
		$query = "select Pachis  from  ".$empresa."_000100 where Pactdo='".substr($wtdox,0,strpos($wtdox,"-"))."' and Pacdoc='".$wdocx."'";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PACIENTES : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num == 0)
		{
			if($wambx == "SI")
			{
				$query = "select Parmei, Pardxi, Pareta, Parcae, Parmee, Partdx, Pardxn, Parpro, Paresp, Parser from ".$empresa."_000120 where Parcco='".substr($wseix,0,strpos($wseix,"-"))."' and parest='on' ";
				$errA = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PARAMETROS INGRESO AMBULATORIO : ".mysql_errno().":".mysql_error());
				$numA = mysql_num_rows($errA);
				if ($numA > 0)
					$rowA = mysql_fetch_array($errA);
			}
			$query = "select Inghis  from  ".$empresa."_000101 where Inghis='".$whisx."' and Ingnin='".$wninx."'";
			$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO DETALLE DE INGRESOS : ".mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 == 0)
			{
				$query = "select Carcon from ".$empresa."_000040 where Carfue='01' ";
				$err2 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
				$row2 = mysql_fetch_array($err2);
				$query =  " update ".$empresa."_000040 set Carcon = Carcon + 1 where Carfue='01' ";
				$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
				$whisx=$row2[0] + 1;
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s"); //agregados los campos a la consulta
				$query = "insert ".$empresa."_000100 (medico,fecha_data,hora_data, Pachis, Pactdo, Pacdoc, Pactat, Pacap1, Pacap2, Pacno1, Pacno2, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Paciu, Pacbar, Pacdep, Paczon, Pactus, Pacofi, Paccea, Pacnoa, Pactea, Pacdia, Pacpaa,  Pacact, Paccru, Pacnru, Pactru, Pacdru, Pacpru, Paccor, Pactam, seguridad) values ('";
				$query .=  $empresa."','";
				$query .=  $fecha."','";
				$query .=  $hora."','";
				$query .=  $whisx."','";
				$query .=  substr($wtdox,0,strpos($wtdox,"-"))."','";
				$query .=  $wdocx."','";
				$query .=  substr($wtatx,0,strpos($wtatx,"-"))."','";
				$query .=  $wap1x."','";
				$query .=  $wap2x."','";
				$query .=  $wno1x."','";
				$query .=  $wno2x."','";
				$query .=  $wfnax."','";
				$query .=  substr($wsexx,0,strpos($wsexx,"-"))."','";
				$query .=  substr($westx,0,strpos($westx,"-"))."','";
				$query .=  $wdirx."','";
				$query .=  $wtelx."','";
				$query .=  substr($wmunx,0,strpos($wmunx,"-"))."','";
				$query .=  substr($wbarx,0,strpos($wbarx,"-"))."','";
				$query .=  substr($wdepx,0,strpos($wdepx,"-"))."','";
				$query .=  substr($wzonx,0,strpos($wzonx,"-"))."','";
				$query .=  substr($wtusx,0,strpos($wtusx,"-"))."','";
				$query .=  substr($wofix,0,strpos($wofix,"-"))."','";
				$query .=  $wceax."','";
				$query .=  $wnoax."','";
				$query .=  $wteax."','";
				$query .=  $wdiax."','";
				$query .=  $wpaax."','on','";
				$query .=  $wceux."','";  //agregado
				$query .=  $wnoux."','";
				$query .=  $wteux."','";
				$query .=  $wdiux."','";
				$query .=  $wpaux."','";
				$query .=  $wcorx."','";
				$query .=  $wserx."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO PACIENTES : ".mysql_errno().":".mysql_error());
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000101 (medico,fecha_data,hora_data, Inghis, Ingnin, Ingfei, Inghin, Ingsei, Ingtin, Ingcai, Ingtpa, Ingcem, Ingent, Ingord, Ingpol, Ingnco, Ingdie, Ingtee, Ingtar, Ingusu, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$wfeix."','".$whinx."','".substr($wseix,0,strpos($wseix,"-"))."','".substr($wtinx,0,strpos($wtinx,"-"))."','".substr($wcaix,0,strpos($wcaix,"-"))."','".substr($wtpax,0,strpos($wtpax,"-"))."','".substr($wnitx,0,strpos($wnitx,"-"))."','".$wentx."','".$wordx."','".$wpolx."','".$wncox."','".$wdiex."','".$wteex."','".substr($wtarx,0,strpos($wtarx,"-"))."','$key','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO INGRESO : ".mysql_errno().":".mysql_error());
				if($wambx == "SI")
				{
					$query = "insert ".$empresa."_000108 (medico,fecha_data,hora_data, Egrhis, Egring, Egrmei, Egrdxi, Egrfee, Egrhoe, Egrest, Egrcae, Egrmee, Egrcom, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[0]."','".$rowA[1]."','".$fecha."','".$hora."',".$rowA[2].",'".$rowA[3]."','".$rowA[4]."','off','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 108 EGRESOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
					$query = "insert ".$empresa."_000109 (medico,fecha_data,hora_data, Diahis, Diaing, Diacod, Diatip, Dianue, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[1]."','".$rowA[5]."','".$rowA[6]."','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 DIAGNOSTICOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
					$query = "insert ".$empresa."_000110 (medico,fecha_data,hora_data, Prohis, Proing, Procod, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[7]."','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 PROCEDIMIENTOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
					$query = "insert ".$empresa."_000111 (medico,fecha_data,hora_data, Esphis, Esping, Espcod, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[8]."','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 ESPECIALIDADES AMBULATORIAS : ".mysql_errno().":".mysql_error());
					$query = "insert ".$empresa."_000112 (medico,fecha_data,hora_data, Serhis, Sering, Sercod, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$whisx."','".$wninx."','".$rowA[9]."','C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 SERVICIOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
					$query = "UPDATE ".$empresa."_000100 set Pacact='off' where Pachis = '".$whisx."'";
					$err1 = mysql_query($query,$conex) or die("ERROR INACTIVANDO PACIENTE EN ARCHIVO 100 : ".mysql_errno().":".mysql_error());
					//--> grabar log de egreso por este programa.
					logAdmsiones( 'Historia egresada por Admision.php', $whisx, $wninx, "");
				}
				$e=$e+1;
				$werr[$e]="OK! PACIENTE INGRESADO E INGRESO GRABADO -HISTORIA : ".$whisx." -INGRESO : ".$wninx;
				//2010-01-15
				actualizarCita( $wemp2, $conex, $wdocx  );
				// RUTINA CREADA EN MODIFICACION 2007-07-12
				if(!Nucleo($conex,substr($wtdox,0,strpos($wtdox,"-")),$wdocx,$wno1x,$wno2x,$wap1x,$wap2x,$wfnax,substr($wsexx,0,strpos($wsexx,"-")),$whisx,$wninx))
				{
					$e=$e+1;
					$werr[$e]="NUCLEO NO SE ACTUALIZO PARA : HISTORIA : ".$whisx." INGRESO : ".$wninx;
				}
				$query = "select count(*),SUM(Fensal) from  ".$empresa."_000018 where Fendpa='".$wdocx."' and  Fensal > 0 and Fentip='01-PARTICULAR' and Fenest='on' ";
				$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO TABLA 18 : ".mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err);
				if($row[0] > 0)
				{
					$e=$e+1;
					$werr[$e]="EL PACIENTE CON IDENTIFICACION : ".$wdocx." TIENE UN SALDO DE $".$row[1]." EN CARTERA PARTICULAR PENDIENTE. FAVOR REVISAR!!!!";
				}
				return true;
			}
			else
			{
				$e=$e+1;
				$werr[$e]="INGRESO YA EXISTE O FUE GRABADO X OTRA PANTALLA";
				return false;
			}
		}
		else
		{
			$e=$e+1;
			$werr[$e]="EL PACIENTE YA ESTA ACTIVO";
			return false;
		}
	}
}

function logAdmsiones( $des, $historia, $ingreso, $documento )
{
	global $key;
	global $conex;
	global $empresa;

	$data = array('error'=>0,'mensaje'=>'','html'=>'');

	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");

	$sql = "INSERT INTO ".$empresa."_000164 (     medico     ,      fecha_data         ,       hora_data        ,        Logusu         ,         Logdes        ,            Loghis          ,           Loging          ,            Logdoc           , Logest, seguridad )
									   VALUES ('".$empresa."','".utf8_decode($fecha)."','".utf8_decode($hora)."','".utf8_decode($key)."','".utf8_decode($des)."','".utf8_decode($historia)."','".utf8_decode($ingreso)."','".utf8_decode($documento)."',  'on' , 'C-root'  )";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error insertando en la tabla de log admisiones ".$empresa." 164 ".mysql_errno()." - Error en el query $sql - ".mysql_error() ) );
	if (!$res)
	{
		$data[ 'error' ] = 1; //sale el mensaje de error
	}

	return $data;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$cedula="";
	if( !isset($idCita) ){	//2010-01-15
		$idCita = '';
	}

	if( !isset( $wemp2 ) ){
		$wemp2 = '';
	}
	$key = substr($user,2,strlen($user));
	echo "<form name='admision' action='Admision.php' method=post>";




	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'idCita' value='".$idCita."'>";
	echo "<input type='HIDDEN' name= 'wemp2' value='".$wemp2."'>";
	if(isset($change) and $change == "UNCHECKED")
		unset($change);
	if(isset($amb) and $amb == "UNCHECKED")
		unset($amb);

	echo "<table border=0 align=center id=tipo2>";
	echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/ips/logo_".$empresa.".jpg'></td></tr>";

	//2010-01-19
	if( isset($idCita) & !empty($idCita) ){
		echo "<tr><td colspan='5'><br>";
		pintarInfoPaciente( $idCita );
		echo "<br></td></tr>";
	}

	echo "<tr><td align=right colspan=5><font size=2>Powered by :  MATRIX </font></td></tr>";

	//******* INICIALIZACION DEL SISTEMA *********
	if(isset($ok) and $ok == 9)
	{
		session_register("estado");
		$estado="1";
		$ok=0;
	}

	//******* GRABACION DE INFORMACION *********
	if(isset($ok) and $ok == 2)
	{
		$werr=array();
		$e=-1;
		if(substr($wtpa,0,strpos($wtpa,"-")) == "E")
		{
			$query = "SELECT Empcod, Empnom, Empdir, Emptel, Emptar  from ".$empresa."_000024 where Empcod =  '".substr($wnit,0,strpos($wnit,"-"))."'  order by Empnom";
			$err = mysql_query($query,$conex) or die("ERROR GRABANDO linea 1120 : ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			$went=$row[1];
			$wdie=$row[2];
			$wtee=$row[3];
			$wtar=$row[4];
		}
		if($whis == "")
			$wninb= 0;
		else
		{
			$query = "SELECT ingnin  from ".$empresa."_000101 where inghis='".$whis."' order by ID desc";
			$err = mysql_query($query,$conex) or die("ERROR GRABANDO EN linea 1133 : ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				$wninb=$row[0];
			}
			else
				$wninb= 0;
		}
		$wno1=strtoupper($wno1);
		$wno2=strtoupper($wno2);
		$wap1=strtoupper($wap1);
		$wap2=strtoupper($wap2);
		$wdir=strtoupper($wdir);
		$wnoa=strtoupper($wnoa);
		$wpaa=strtoupper($wpaa);
		$went=strtoupper($went);
		$wdia=strtoupper($wdia); //agregados campos
		if(valgen($conex,$wtdo,$wdoc,$wap1,$wno1,$wfna,$whis,$whin,$wdir,$wtel,$wmun,$wbar,$wdep,$wofi,$wcea,$wnoa,$wtea,$wdia,$wpaa,$wnin,$wfei,$wtpa,$wnit,$went,$word,$wpol,$wnco,$wdie,$wtee,$werr,$e,$wceu,$wnou,$wteu,$wdiu,$wpau, $wcor) and ((!isset($change) and $wnin == ($wninb + 1)) or (isset($change) and $wnin == $wninb)))
		{
			if(isset($amb))
			{
				$ambx="SI";
				$query = "lock table ".$empresa."_000100 LOW_PRIORITY WRITE, ".$empresa."_000101 LOW_PRIORITY WRITE, ".$empresa."_000040 LOW_PRIORITY WRITE, root_000036 LOW_PRIORITY WRITE, root_000037 LOW_PRIORITY WRITE, root_000050 LOW_PRIORITY WRITE, ".$empresa."_000018 LOW_PRIORITY WRITE  ";
				$query .= ", ".$empresa."_000108 LOW_PRIORITY WRITE, ".$empresa."_000109 LOW_PRIORITY WRITE, ".$empresa."_000110 LOW_PRIORITY WRITE, ".$empresa."_000111 LOW_PRIORITY WRITE, ".$empresa."_000112 LOW_PRIORITY WRITE, ".$empresa."_000120 LOW_PRIORITY WRITE ";
				if( isset($wemp2) && !empty($wemp2) ){	//2010-01-15
					$query .= ",".$wemp2."_000009 LOW_PRIORITY WRITE ";	//2010-15-01
				}
			}
			else
			{
				$ambx="NO";
				$query = "lock table ".$empresa."_000100 LOW_PRIORITY WRITE, ".$empresa."_000101 LOW_PRIORITY WRITE, ".$empresa."_000040 LOW_PRIORITY WRITE, root_000036 LOW_PRIORITY WRITE, root_000037 LOW_PRIORITY WRITE, root_000050 LOW_PRIORITY WRITE, ".$empresa."_000018 LOW_PRIORITY WRITE   ";
				if( isset($wemp2) && !empty($wemp2) ){	//2010-01-15
					$query .= ",".$wemp2."_000009 LOW_PRIORITY WRITE ";	//2010-01-15
				}
			}
			$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO PACIENTES Y DETALLE DE INGRESOS : ".mysql_errno().":".mysql_error());

			if ($wtat != "M-MEDICINA DOMICILIARIA")
			{
				$wser="";
			}

			if(isset($change))
			{	//agregados los campos
				if(MOD_ING($conex,$whis,$wtdo,$wdoc,$wtat,$wap1,$wap2,$wno1,$wno2,$wfna,$wsex,$west,$wdir,$wtel,$wmun,$wbar,$wdep,$wzon,$wtus,$wofi,$wcea,$wnoa,$wtea,$wdia,$wpaa,$wnin,$wfei,$whin,$wsei,$wtin,$wcai,$wtpa,$wnit,$went,$word,$wpol,$wnco,$wdie,$wtee,$wtar,$werr,$e,$wceu,$wnou,$wteu,$wdiu,$wpau, $wcor,$wser))
				{
					$ok=0;
					$estado="2";
				}
			}
			else
			{   //agregados los campos
				if(ACT_ING($conex,$whis,$wtdo,$wdoc,$wtat,$wap1,$wap2,$wno1,$wno2,$wfna,$wsex,$west,$wdir,$wtel,$wmun,$wbar,$wdep,$wzon,$wtus,$wofi,$wcea,$wnoa,$wtea,$wdia,$wpaa,$wnin,$wfei,$whin,$wsei,$wtin,$wcai,$wtpa,$wnit,$went,$word,$wpol,$wnco,$wdie,$wtee,$wtar,$ambx,$werr,$e,$wceu,$wnou,$wteu,$wdiu,$wpau, $wcor,$wser ))
				{
					$ok=0;
					$estado="2";
				}
			}
			$query = " UNLOCK TABLES";
			$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
			if($ok != 0)
				$ok = 1;
		}
		else
		{
			$e=$e+1;
			$werr[$e]="DATOS INCORRECTOS O NUMERO DE INGRESO INCORRECTO VERIFIQUE!!!";
			$ok=1;
		}
	}

	//******* INICIALIZACION DE CAMPOS *********
	if(isset($ok) and $ok == 0)
	{
		$wdat=0;
		$whis="";
		$wtdo="";
		$wdoc="";
		$wtat="";
		$wser="";
		$wap1="";
		$wap2="";
		$wno1="";
		$wno2="";
		//$wfna=date("Y-m-d");
		if( empty( $wfna ) ){
					$wfna=date("Y-m-d");
				}
		else {$wfna="2012-12-12";}  //AGREGADO1
		$wsex="";
		$west="";
		$wdir="";
		$wtel="";
		$wmun="";
		$wmunw="";
		$wbar="";
		$wbarw="";
		$wdep="";
		$wzon="";
		$wtus="";
		$wofi="";
		$wofiw="";
		$wcea="";
		$wnoa="";
		$wtea="";
		$wdia="";
		$wpaa="";

		$wceu="";//agregado
		$wnou="";
		$wteu="";
		$wdiu="";
		$wpau="";
		$wcor="";
		$wnin="";
		$wfei=date("Y-m-d");
		$whin=date("H:i:s");
		$wsei="";
		$wtin="";
		$wcai="";
		$wtpa="";
		$wnit="";
		$wnitw="";
		$went="";
		$word="0";
		$wpol="0";
		$wnco="0";
		$wdie="";
		$wtee="";
		$wtar="";
		$weda=0;
		$ok=1;
		$wact="off";
		if(isset($amb))
			unset($amb);
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
			$wtdo=ver($wtdo);
			$querys = "select Pachis, Pactat, Pacap1, Pacap2, Pacno1, Pacno2, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Paciu, Pacbar, Paczon, Pactus, Paccea, Pacnoa, Pactea, Pacdia, Pacpaa, Pacact, Pacdoc, Pactdo, Pacofi, Paccru, Pacnru, Pactru, Pacdru, Pacpru, Paccor, Pactam from  ".$empresa."_000100 ";  //agregados los campos nuevos a la consulta
			//$querys .= " where Pactdo='".$wtdo."'";
			$querys .= " where Pachis !='0'";
			if($wdoc != "")
				$querys .= "     and Pacdoc='".$wdoc."'";
			if($whis != "")
				$querys .= "     and Pachis='".$whis."'";
			if($wap1 != "")
				$querys .= "     and Pacap1='".$wap1."'";
			if($wap2 != "")
				$querys .= "     and Pacap2='".$wap2."'";
			if($wno1 != "")
				$querys .= "     and Pacno1='".$wno1."'";
			if($wno2 != "")
				$querys .= "     and Pacno2='".$wno2."'";
			if($wtel != "")
				$querys .= "     and Pactel='".$wtel."'";
			if($wcor != "")
				$querys .= "     and Paccor='".$wcor."'";
			$querys .=" Order by  Pacdoc  ";
			$err = mysql_query($querys,$conex) or die("ERROR consultando EN linea 1288 : ".mysql_errno().":".mysql_error());
			$numero = mysql_num_rows($err);
			$numero=$numero - 1;
		}
		if ($numero>=0)
		{
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
					$wpos = $wpos  - 1;
					if ($wpos < 0)
						$wpos=0;
				}
			}
			else
				$wpos=0;
			$wp=$wpos+1;
			//echo "Registro Nro : ".$wpos."<br>";
			$querys .=  " limit ".$wpos.",1";
			$err = mysql_query($querys,$conex) or die("ERROR GRABANDO EN linea 1322 : ".mysql_errno().":".mysql_error());
			$querys=str_replace(chr(39),chr(34),$querys);
			echo "<input type='HIDDEN' name= 'querys' value='".$querys."'>";
			echo "<input type='HIDDEN' name= 'wpos' value='".$wpos."'>";
			echo "<input type='HIDDEN' name= 'numero' value='".$numero."'>";
			$wdat=0;
			if ($numero >=  0)
			{
				$wdat=1;
				$row = mysql_fetch_array($err);
				if(!isset($change))
				{
					$wdoc=$row[21];
					$wtdo=$row[22];
					$whis=$row[0];
					echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
					$wtat=$row[1];
					$wser=$row[30];
					$wap1=$row[2];
					$wap2=$row[3];
					$wno1=$row[4];
					$wno2=$row[5];
					$wfna=$row[6];
					$wsex=$row[7];
					$west=$row[8];
					$wdir=$row[9];
					$wtel=$row[10];
					$wmun=$row[11];
					$wdep=substr($wmun,0,2);
					$query = "SELECT Nombre from root_000006 where Codigo = '".$wmun."'  order by Nombre";
					$err1 = mysql_query($query,$conex) or die("ERROR consultando EN linea 1351 : ".mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$wmunw=$row1[0];
					$wbar=$row[12];
					$query = "SELECT Barcod, Bardes  from root_000034  where Barmun= '".$wmun."' and Barcod = '".$wbar."' ";
					$err1 = mysql_query($query,$conex) or die("ERROR consultando EN linea 1356 : ".mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$wbarw=$row1[1];
					$wzon=$row[13];
					$wtus=$row[14];
					$wofi=$row[23];
					$query = "SELECT Codigo, Descripcion from root_000003 where Codigo = '".$wofi."'  order by Descripcion";
					$err1 = mysql_query($query,$conex) or die("ERROR consultando EN linea 1363 : ".mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$wofiw=$row1[1];
					$wcea=$row[15];
					$wnoa=$row[16];
					$wtea=$row[17];
					$wdia=$row[18];
					$wpaa=$row[19];

					$wceu=$row[24];//agregado
					$wnou=$row[25];
					$wteu=$row[26];
					$wdiu=$row[27];
					$wpau=$row[28];
					$wcor=$row[29];
				}
				$wact=$row[20];
			}
			else
			{
				if($wdoc == "")
				{
					$whis="";
					$wtdo="";
					$wdoc="";
					$wtat="";
					$wser="";
					$wap1="";
					$wap2="";
					$wno1="";
					$wno2="";

					if( empty( $wfna ) ){
					$wfna=date("Y-m-d");
					}
					else {$wfna="2011-12-12";}
					$wsex="";
					$west="";
					$wdir="";
					$wtel="";
					$wmun="";
					$wmunw="";
					$wbar="";
					$wbarw="";
					$wdep="";
					$wzon="";
					$wtus="";
					$wofi="";
					$wofiw="";
					$wcea="";
					$wnoa="";
					$wtea="";
					$wdia="";
					$wpaa="";
					$wceu=""; //agregado
					$wnou="";
					$wteu="";
					$wdiu="";
					$wpau="";
					$wcor="";
					$wact="off";
					$weda=0;

				}
			}
		}

		if(isset($wp))
		{
			$estado="1";
			$n=$numero +1 ;
			echo "<tr><td align=right colspan=5><font size=2><b>Registro Nro. ".$wp." De ".$n."</b></font></td></tr>";
		}
		else
		{
			$querys="";
			$querysR="";
			echo "<tr><td align=right colspan=5><font size=2 color='#CC0000'><b>Consulta Sin Registros</b></font></td></tr>";
		}
	}
	else
	{
		$querys="";
		$querysR="";
	}

	//*******PROCESO DE INFORMACION *********


	//********************************************************************************************************
	//*                                         DATOS DEL PACIENTE                                           *
	//********************************************************************************************************

	echo "<tr><td align=center bgcolor=#000066 colspan=5><font color=#ffffff size=6><b>ADMISION DE PACIENTES</font><font color=#33CCFF size=4>&nbsp&nbsp&nbsp Ver. 2016-04-11</font></b></font></td></tr>";
	$color="#dddddd";
	$color1="#000099";
	$color2="#006600";
	$color3="#cc0000";
	$color4="#CC99FF";
	$color5="#99CCFF";
	$color6="#FF9966";
	$color7="#cccccc";
	echo "<tr><td align=center bgcolor=#999999 colspan=5><b>DATOS GENERALES</b></td></tr>";
	echo "<tr><td align=center bgcolor=".$color7." colspan=5><b>AMBULATORIO<BR></b>";
	if(isset($amb))
		echo "<input type='checkbox' name='amb' checked></td></tr>";
	else
		echo "<input type='checkbox' name='amb'></td></tr>";
	echo "<tr>";
	?>
	<script>
		function ira(){document.admision.wdoc.focus();}
	</script>
	<?php
	echo "<td bgcolor=".$color." align=center><b>*Tipo Documento : </b><br>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='01' and Selest='on'  order by Selpri";
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
	echo "</td>";
	if( !isset($idCita) ){	//2010-01-15
		$idCita = '';
	}
	if(!isset($weda))
		$weda=0;
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($querysR))
		$querysR="";
	else
		$querysR=str_replace(" ","%20",$querysR);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($wposR))
		$wposR=0;
	if(!isset($numero))
		$numero=0;
	if(!isset($numeroR))
		$numeroR=0;
	if(!isset($wmaxing))
		$wmaxing=0;

	// $wdat=0;
		// $wdep="";
		// $wnin="";
		// $wact="";

	$id="ajaxquery('1','1','".@$wdat."',\"".str_replace(" ",".",@$wdep)."\",'".@$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".@$wact."','".$idCita."','".$wemp2."')";

	if($wdoc == "")
		echo "<td bgcolor=".$color." align=center><b>*Documento : </b><br><input type='TEXT' name='wdoc' size=12 id='w2' maxlength=12 value='".$wdoc."' OnChange=".$id."  class=tipo3></td>";
	else
		echo "<td bgcolor=".$color." align=center><b>*Documento : </b><br><input type='TEXT' name='wdoc' readonly='readonly' size=12 id='w2' maxlength=12 value='".$wdoc."' OnChange=".$id." class=tipo3></td>";
	$wtdo=ver($wtdo);
	if(@$wdat == 0 and $ok != 3)
	{
		if($wdoc != "")
		{
			if(!isset($e))
			{
				$werr=array();
				$e=-1;
			}
			// VALIDACION DE LA CARTERA PARTICULAR ASOCIADA AL NUMERO DE DOCUMENTO
			$query = "select count(*),SUM(Fensal) from  ".$empresa."_000018 where Fendpa='".$wdoc."' and  Fensal > 0 and Fentip='01-PARTICULAR' and Fenest='on' ";
			$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO TABLA 18 : ".mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			if($row[0] > 0)
			{
				$e=$e+1;
				$werr[$e]="EL PACIENTE CON IDENTIFICACION : ".$wdoc." TIENE UN SALDO DE $".$row[1]." EN CARTERA PARTICULAR PENDIENTE. FAVOR REVISAR!!!!";
			}
			//FIN VALIDACION DE LA CARTERA PARTICULAR ASOCIADA AL NUMERO DE DOCUMENTO
		}

		$query = "select Pachis, Pactat, Pacap1, Pacap2, Pacno1, Pacno2, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Paciu, Pacbar, Paczon, Pactus, Paccea, Pacnoa, Pactea, Pacdia, Pacpaa, Pacact, Pacofi, Paccru, Pacnru, Pactru, Pacdru, Pacpru, Paccor, Pactam   from  ".$empresa."_000100 where Pactdo='".$wtdo."' and Pacdoc='".$wdoc."'"; //agregados campos a la consulta
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PACIENTES EN PROCESO : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$wdat=0;
		if ($num >  0)
		{
			$wdat=1;
			$row = mysql_fetch_array($err);
			$whis=$row[0];
			echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
			$wtat=$row[1];
			$wap1=$row[2];
			$wap2=$row[3];
			$wno1=$row[4];
			$wno2=$row[5];
			$wfna=$row[6];
			$wsex=$row[7];
			$west=$row[8];
			$wdir=$row[9];
			$wtel=$row[10];
			$wmun=$row[11];
			$wdep=substr($wmun,0,2);
			$query = "SELECT Nombre from root_000006 where Codigo = '".$wmun."'  order by Nombre";
			$err1 = mysql_query($query,$conex) or die("ERROR validacion cartera EN linea 1552 : ".mysql_errno().":".mysql_error());
			$row1 = mysql_fetch_array($err1);
			$wmunw=$row1[0];
			$wbar=$row[12];
			$query = "SELECT Barcod, Bardes  from root_000034  where Barmun= '".$wmun."' and Barcod = '".$wbar."' ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1557".mysql_error());
			$row1 = mysql_fetch_array($err1);
			$wbarw=$row1[1];
			$wzon=$row[13];
			$wtus=$row[14];
			$wcea=$row[15];
			$wnoa=$row[16];
			$wtea=$row[17];
			$wdia=$row[18];
			$wpaa=$row[19];
			$wact=$row[20];
			$wofi=$row[21];

			$wceu=$row[22];//agregado
			$wnou=$row[23];
			$wteu=$row[24];
			$wdiu=$row[25];
			$wpau=$row[26];
			$wcor=$row[27];
			$wser=$row[28];
			$query = "SELECT Codigo, Descripcion from root_000003 where Codigo = '".$wofi."'  order by Descripcion";
			$err1 = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1570".mysql_error());
			$row1 = mysql_fetch_array($err1);
			$wofiw=$row1[1];
		}
		else
		{
			if($wdoc == "")
			{
				$whis="";
				$wtdo="";
				$wdoc="";
				$wtat="";
				$wser="";
				$wap1="";
				$wap2="";
				$wno1="";
				$wno2="";

				// if( empty( $wfna ) ){
					$wfna=date("Y-m-d");
				// }
				// else {$wfna="2010-12-12";}
				$wsex="";
				$west="";
				$wdir="";
				$wtel="";
				$wmun="";
				$wmunw="";
				$wdep="";
				$wbar="";
				$wbarw="";
				$wzon="";
				$wtus="";
				$wofi="";
				$wofiw="";
				$wcea="";
				$wnoa="";
				$wtea="";
				$wdia="";
				$wpaa="";
				$wceu="";//agregado INICIALIZACION DE CAMPOS
				$wnou="";
				$wteu="";
				$wdiu="";
				$wpau="";
				$wcor="";
				$wact="off";
				$weda=0;
			}
			else
			{
				$whis="";
				/*$wtdo="";
				$wdoc="";
				$wtat="";
				$wap1="";
				$wap2="";
				$wno1="";
				$wno2="";*/
				if( empty( $wfna ) ){
					$wfna=date("Y-m-d");
				}
				//else {$wfna="2009-12-12";}
				/*$wsex="";
				$west="";
				$wdir="";
				$wtel="";
				$wmun="";
				$wmunw="";
				$wdep="";
				$wbar="";
				$wbarw="";
				$wzon="";
				$wtus="";
				$wofi="";
				$wofiw="";
				$wcea="";
				$wnoa="";
				$wtea="";
				$wdia="";
				$wpaa="";
				$wact="off";
				$weda=0;*/

				// $wcea=$wdoc;
				// $wnoa=$wnou=$wno1." ".$wno2." ".$wap1." ".$wap2;
				// $wtea=$wtel;
				// $wdia=$wdir;
				// $wpaa="NINGUNO";
				// $wceu=$wdoc;//agregado
				// $wnou=$wnou=$wno1." ".$wno2." ".$wap1." ".$wap2;
				// $wteu=$wtel;
				// $wdiu=$wdir;
				// $wpau="NINGUNO";
				/*$wact="off";
				$weda=0;*/

				$query = "select Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex  from  root_000036 where Pactid='".$wtdo."' and Pacced='".$wdoc."'";
				$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO NUCLEO DEL SISTEMA : ".mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num >  0)
				{
					$row = mysql_fetch_array($err);
					$wap1=$row[4];
					$wap2=$row[5];
					$wno1=$row[2];
					$wno2=$row[3];
					$wfna=$row[6];
					$wsex=$row[7];
				}
				else //agregado porque los que tiene solo la cita no estan ni en la tabla clisur_100, ni en la de pacientes root_36
				{
					if (isset($wemp2)  && !empty($wemp2))
					{
						$query1="select cedula, Nom_pac, tipoA, tipoS from ".$wemp2."_000009 where cedula='".$wdoc."' ";

						$err1 = mysql_query($query1,$conex) or die("Error en el query $query1: ".mysql_errno().":".mysql_error());
						$num1 = mysql_num_rows($err1);
						if ($num1 >  0)
						{
							$row1 = mysql_fetch_array($err1);
							$wtat=$row1[2];
							if ($wtat=="M-MEDICINA DOMICILIARIA")
							{
								$wser=$row1[3];
							}
							else
							{
								$wtat="C-CONSULTA EXTERNA";
							}
						}
					}
				}
			}
		}
	}
	echo "<input type='HIDDEN' name= 'wdat' value='".$wdat."'>";
	echo "<td bgcolor=".$color." align=center><b>*Historia : </b><br><input type='TEXT' name='whis' id='w1' size=10 maxlength=10 class=tipo3 value='".$whis."'></td>";
	echo "<td bgcolor=".$color." align=center colspan=2>Tipo Atencion : <br>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='02' and Selest='on'  and seldes != 'ATENCION CLINICA' order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1661".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wtat' id=tipo1 onchange='cambiar()'>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtat=ver($wtat);
			if($wtat == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	//*************si se selecciona domiciliaria aparece este select para determinar el tipo de servicio
	if( $wtat != 'M' ){
		echo "	<div id='div_servicio' style='display:none;'>";
	}
	else{
		echo "	<div id='div_servicio'>";
	}


	echo "<br><div id='div_servicio'>Tipo Servicio : <br>";

	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='14' and Selest='on'  order by Selpri";
	$errq = mysql_query($query,$conex) or die(mysql_errno()."error en el query - ".$query." ".mysql_error());
	$numq = mysql_num_rows($errq);
	echo "<select name='wser' id=tipo1>";
	if ($numq>0)
	{
		for ($i=0;$i<$numq;$i++)
		{
			$row = mysql_fetch_array($errq);
			$wser=ver($wser);
			if($wser == $row[0] )
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>
			</div>";
	 //*************
	// if ($whis=="")
	// {

		//$wap1="";
		//$wap2="";
		//$wno1="";
		//$wno2="";
		//$wfna=date("Y-m-d");
		//$wdir="";
		//$wtel="";
		//$wcea="";
		//$wnoa="";
		//$wtea="";
		//$wdia="";
		//$wpaa="";
		//$wmun="";
		//$wofi="";
   // }
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color." align=center><b>*1er Apellido :</b> <br><input type='TEXT' name='wap1' size=20 maxlength=20 id='w3' value='".@$wap1."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center><b>*2do Apellido : </b><br><input type='TEXT' name='wap2' size=20 maxlength=20 id='w4' value='".@$wap2."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center><b>*1er Nombre : </b><br><input type='TEXT' name='wno1' size=20 maxlength=20 id='w5' value='".@$wno1."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center><b>*2do Nombre : </b><br><input type='TEXT' name='wno2' size=20 maxlength=20 id='w6' value='".@$wno2."' class=tipo3></td>";
	if(isset($wact) and $wact=="on")
	{
		echo "<input type='HIDDEN' name= 'wact' value='".$wact."'>";
		echo "<td bgcolor=".$color." align=center>Estado : <br><font color=#009900 face='tahoma' size=3><b>ACTIVO</b></td>";
	}
	else
		echo "<td bgcolor=".$color." align=center>Estado : <br><font color=#FF0000 face='tahoma' size=3><b>INACTIVO</b></td>";
	echo "</tr>";
	echo "<tr>";
	$ann=(integer)substr(@$wfna,0,4)*360 +(integer)substr(@$wfna,5,2)*30 + (integer)substr(@$wfna,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$weda=(integer)(($aa - $ann)/360);
	$weda=number_format((double)$weda,0,'.','');
	$cal="calendario('wfna','1')";
	echo "<td bgcolor=".$color." align=center>Fecha Nacimiento : <br><input type='TEXT' name='wfna' size=10 maxlength=10  id='wfna' class='tipo3' readonly='readonly' value=".@$wfna." ><button id='trigger1' onclick=".$cal.">...</button>";
	?>
	<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfna',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	//]]></script>
	<?php
	echo "<br>Edad : <input type='TEXT' name='weda' size=5 maxlength=5 id='w27' value=".$weda." class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Sexo : <br>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='03' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1705".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wsex' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wsex=ver($wsex);
			if($wsex == $row[0])
				echo "<option selected  value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	echo "<td bgcolor=".$color." align=center>Estado Civil : <br>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='04' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1724".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='west' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$west=ver($west);
			if($west == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";

	echo "<td bgcolor=".$color." align=center colspan=2>Direccion : <br><input type='TEXT' name='wdir' size=30 maxlength=60  id='w8' value='".@$wdir."' class=tipo3></td>";

	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color." align=center><b>*Telefonos : <br></b><input type='TEXT' name='wtel' size=30 maxlength=30  id='w9' value='".@$wtel."' class=tipo3></td>";
	if( !isset($idCita) ){	//2010-01-15
		$idCita = '';
	}


	if(!isset($weda))
		$weda=0;
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($querysR))
		$querysR="";
	else
		$querysR=str_replace(" ","%20",$querysR);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($wposR))
		$wposR=0;
	if(!isset($numero))
		$numero=0;
	if(!isset($numeroR))
		$numeroR=0;
	if(!isset($wmaxing))
		$wmaxing=0;

	$id="ajaxquery('1','2','".$wdat."',\"".str_replace(" ",".",@$wdep)."\",'".@$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".@$wact."','".$idCita."','".$wemp2."')";

	if(isset($wmunw) and $wmunw != "")
		echo"<td bgcolor=".$color." align=center>Municipio  : <br><input type='TEXT' name='wmunw' size=10 maxlength=30 id='w10' value='".$wmunw."' OnChange=".$id." class=tipo3><br>";
	else
		echo"<td bgcolor=".$color." align=center>Municipio  : <br><input type='TEXT' name='wmunw' size=10 maxlength=30 id='w10' OnBlur=".$id." class=tipo3><br>";
	if(isset($okx) and $okx == "2")
	{
		$wbarw="";
		if( !isset($idCita) ){	//2010-01-15
			$idCita = '';
		}
		if(!isset($weda))
			$weda=0;
		if(!isset($querys))
			$querys="";
		else
			$querys=str_replace(" ","%20",$querys);
		if(!isset($querysR))
			$querysR="";
		else
			$querysR=str_replace(" ","%20",$querysR);
		if(!isset($wpos))
			$wpos=0;
		if(!isset($wposR))
			$wposR=0;
		if(!isset($numero))
			$numero=0;
		if(!isset($numeroR))
			$numeroR=0;
		if(!isset($wmaxing))
			$wmaxing=0;

		$id="ajaxquery('1','3','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".$wact."','".$idCita."','".$wemp2."')";

		echo "<select name='wmun' OnChange=".$id." id=tipo1>";
		if(isset($wmunw) and $wmunw != "")
		{
			$query = "SELECT Codigo, Nombre from root_000006 where Nombre like '%".$wmunw."%'  order by Nombre";
			$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1822".mysql_error());
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				echo "<option value='SELECCIONE'>SELECCIONE</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
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
		echo"<select name='wmun' id=tipo1>";
		if(isset($wmun))
		{
			$wmun=ver($wmun);
			$query = "SELECT Codigo, Nombre from root_000006 where Codigo = '".$wmun."'";
			$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1847".mysql_error());
			$row = mysql_fetch_array($err);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			}
			else
			{
				echo "<option value='0-NO APLICA'>0-NO APLICA</option>"; //agregado
			}
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	$wdep="";
	$query = "SELECT Codigo, Descripcion from root_000002 where codigo='".substr(@$wmun,0,2)."' ";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1857".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		//echo "<br>".$row[0]."-".$row[1];
		$wdep=$row[0]."-".$row[1];

		echo "<input type='HIDDEN' name= 'wdep' value='".$row[0]."-".$row[1]."'>";
	}
	else
		echo "&nbsp ";
	echo "<br><input type='TEXT' name='wdep' size=20 maxlength=20 id='w26' value='".$wdep."' class=tipo3>";
	echo "</td>";
	if( !isset($idCita) ){	//2010-01-15
		$idCita = '';
	}
	if(!isset($weda))
		$weda=0;
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($querysR))
		$querysR="";
	else
		$querysR=str_replace(" ","%20",$querysR);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($wposR))
		$wposR=0;
	if(!isset($numero))
		$numero=0;
	if(!isset($numeroR))
		$numeroR=0;
	if(!isset($wmaxing))
		$wmaxing=0;

	$id="ajaxquery('1','6','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".@$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".@$wact."','".$idCita."','".$wemp2."')";

	if(isset($wbarw) and $wbarw != "")
		echo"<td bgcolor=".$color." align=center colspan=2>Barrio : <br><input type='TEXT' name='wbarw' size=10 maxlength=30 id='w29' value='".$wbarw."' OnBlur=".$id." class=tipo3><br>";
	else
		echo"<td bgcolor=".$color." align=center colspan=2>Barrio : <br><input type='TEXT' name='wbarw' size=10 maxlength=30 id='w29' OnBlur=".$id." class=tipo3><br>";
	@$wmun=ver(@$wmun);
	if(isset($okx) and $okx == "2")
	{
		echo "<select name='wbar' id=tipo1>";
		echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";

	}
	else
	{
		if(isset($okx) and $okx == "6")
		{
			echo "<select name='wbar' id=tipo1>";
			if(isset($wbarw) and $wbarw != "")
			{
				$query = "SELECT Barcod, Bardes  from root_000034  where Barmun= '".$wmun."' and Bardes like '%".$wbarw."%'  order by Bardes ";
				$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1917".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$wbar=ver($wbar);
						if($wbar == $row[0])
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
			echo "<select name='wbar' id=tipo1>";
			$wbar=ver($wbar);
			$query = "SELECT Barcod, Bardes  from root_000034  where Barmun= '".$wmun."' and Barcod = '".$wbar."' ";
			$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1943".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($wbar == $row[0])
						echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}
			}
			else
				echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		}
	}
	echo "</td>";
	echo "<td bgcolor=".$color." align=center>Zona : ";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='05' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1963".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wzon' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wzon=ver($wzon);
			if($wzon == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";

	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color." align=center>Tipo  Usuario : <br>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='06' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1985".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wtus' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtus=ver($wtus);
			if($wtus == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	if( !isset($idCita) ){	//2010-01-15
		$idCita = '';
	}
	if(!isset($weda))
		$weda=0;
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($querysR))
		$querysR="";
	else
		$querysR=str_replace(" ","%20",$querysR);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($wposR))
		$wposR=0;
	if(!isset($numero))
		$numero=0;
	if(!isset($numeroR))
		$numeroR=0;
	if(!isset($wmaxing))
		$wmaxing=0;

	$id="ajaxquery('1','5','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".@$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".@$wact."','".$idCita."','".$wemp2."')";

	if(isset($wofiw) and $wofiw != "")
		echo"<td bgcolor=".$color." align=center colspan=2>Oficio/Ocupacion : <br><input type='TEXT' name='wofiw' size=10 maxlength=30 id='w28' value='".$wofiw."' OnBlur=".$id." class=tipo3><br>";
	else
		echo"<td bgcolor=".$color." align=center colspan=2>Oficio/Ocupacion : <br><input type='TEXT' name='wofiw' size=10 maxlength=30 id='w28' OnBlur=".$id." class=tipo3><br>";
	if(isset($okx) and $okx == "5")
	{
		echo "<select name='wofi' id=tipo1>";
		if(isset($wofiw) and $wofiw != "")
		{
			$query = "SELECT Codigo, Descripcion from root_000003 where Descripcion like '%".$wofiw."%'  order by Descripcion";
			$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2038".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wofi=ver($wofi);
					if($wofi == $row[0])
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
		@$wofi=ver(@$wofi);
		$query = "SELECT Codigo, Descripcion from root_000003 where Codigo = '".$wofi."'";
		$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2063".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wofi' id=tipo1>";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($wofi == $row[0])
					echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			}
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	echo "</td>";
	//correo electronico****
	echo"<td bgcolor=".$color." align=center colspan=2>Correo Electronico : <br><input type='TEXT' name='wcor' size=30 maxlength=60 id='w35' value='".@$wcor."' class=tipo3></td>";

	echo "</tr>";
	echo "<tr><td align=center bgcolor=#999999 colspan=5 ><b> <font size='4'>RESPONSABLE DEL USUARIO</font></b></td></tr>";  //2
	echo "<tr>";
	echo "<td bgcolor=".$color." align=center>Documento : <br><input type='TEXT' name='wceu' size=12 maxlength=12  id='w30' value='".@$wceu."' onblur='validarCampos1();' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Nombre : <br><input type='TEXT' name='wnou' size=20 maxlength=40  id='w31' value='".@$wnou."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Telefonos : <br><input type='TEXT' name='wteu' size=20 maxlength=25  id='w32' value='".@$wteu."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Direccion : <br><input type='TEXT' name='wdiu' size=20 maxlength=60  id='w33' value='".@$wdiu."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Parentesco : <br><input type='TEXT' name='wpau' size=20 maxlength=30  id='w34' value='".@$wpau."' class=tipo3></td>";
	echo "</tr>";
	//**************
	echo "</tr>";
	echo "<tr><td align=center bgcolor=#999999 colspan=5 ><b> <font size='4'>DATOS DEL ACOMPA&Ntilde;ANTE</font></b></td></tr>";  //2
	echo "<tr>";
	echo "<td bgcolor=".$color." align=center>Documento : <br><input type='TEXT' name='wcea' size=12 maxlength=12  id='w11' value='".@$wcea."' onblur='validarCampos();' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Nombre : <br><input type='TEXT' name='wnoa' size=20 maxlength=40  id='w12' value='".@$wnoa."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Telefonos : <br><input type='TEXT' name='wtea' size=20 maxlength=25  id='w13' value='".@$wtea."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Direccion : <br><input type='TEXT' name='wdia' size=20 maxlength=60  id='w14' value='".@$wdia."' class=tipo3></td>";
	echo "<td bgcolor=".$color." align=center>Parentesco : <br><input type='TEXT' name='wpaa' size=20 maxlength=30  id='w15' value='".@$wpaa."' class=tipo3></td>";
	echo "</tr>";
	switch ($ok)
	{
		case 1:
			if( !isset( $idCita ) ){	//2010-01-15
				$idCita = '';
			}
			if(!isset($weda))
				$weda=0;
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($querysR))
				$querysR="";
			else
				$querysR=str_replace(" ","%20",$querysR);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($wposR))
				$wposR=0;
			if(!isset($numero))
				$numero=0;
			if(!isset($numeroR))
				$numeroR=0;
			if(!isset($wmaxing))
				$wmaxing=0;

			$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".@$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".@$wact."','".$idCita."','".$wemp2."')";

			echo "<tr><td bgcolor=#999999 align=center><input type='RADIO' name=ok value=0 onclick=".$id."><b>INICIAR</b></td>";
			echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=1 checked onclick=".$id."><b>PROCESO</b></td>";
			echo "<td bgcolor=#999999 align=center colspan=2><input type='RADIO' name=ok value=3 onclick=".$id."><b>CONSULTAR</b>";
		break;
		case 3:
			if( !isset($idCita) ){	//2010-01-15
				$idCita = '';
			}
			if(!isset($weda))
				$weda=0;
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($querysR))
				$querysR="";
			else
				$querysR=str_replace(" ","%20",$querysR);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($wposR))
				$wposR=0;
			if(!isset($numero))
				$numero=0;
			if(!isset($numeroR))
				$numeroR=0;
			if(!isset($wmaxing))
				$wmaxing=0;

			$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".$wact."','".$idCita."','".$wemp2."')";

			echo "<tr><td bgcolor=#999999 align=center><input type='RADIO' name=ok value=0 onclick=".$id."><b>INICIAR</b></td>";
			echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=1 onclick=".$id."><b>PROCESO</b></td>";
			echo "<td bgcolor=#999999 align=center colspan=2><input type='RADIO' name=ok value=3 checked onclick=".$id."><b>CONSULTAR</b>";
		break;
	}
	if(isset($ok) and $ok == 3)
	{
		if( !isset($idCita) ){	//2010-01-15
			$idCita = '';
		}
		if(!isset($weda))
			$weda=0;
		if(!isset($querys))
			$querys="";
		else
			$querys=str_replace(" ","%20",$querys);
		if(!isset($querysR))
			$querysR="";
		else
			$querysR=str_replace(" ","%20",$querysR);
		if(!isset($wpos))
			$wpos=0;
		if(!isset($wposR))
			$wposR=0;
		if(!isset($numero))
			$numero=0;
		if(!isset($numeroR))
			$numeroR=0;
		if(!isset($wmaxing))
			$wmaxing=0;

		$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".$wact."','".$idCita."','".$wemp2."')";

		echo "<input type='RADIO' name=wb value=1 onclick=".$id."> Adelante <input type='RADIO' name=wb value=2 onclick=".$id."> Atras</td>";
		echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=2 onclick=".$id."><b>GRABAR</b></td></tr>";
	}
	else
	{
		if( !isset($idCita) ){	//2010-01-15
			$idCita = '';
		}
		if(!isset($weda))
			$weda=0;
		if(!isset($querys))
			$querys="";
		else
			$querys=str_replace(" ","%20",$querys);
		if(!isset($querysR))
			$querysR="";
		else
			$querysR=str_replace(" ","%20",$querysR);
		if(!isset($wpos))
			$wpos=0;
		if(!isset($wposR))
			$wposR=0;
		if(!isset($numero))
			$numero=0;
		if(!isset($numeroR))
			$numeroR=0;
		if(!isset($wmaxing))
			$wmaxing=0;

		$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".@$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".@$wact."','".$idCita."','".$wemp2."')";

		echo "</td>";
		echo "<td bgcolor=#999999 align=center><input type='RADIO' name=ok value=2 onclick=".$id."><b>GRABAR</b></td></tr>";
	}
	echo "</table><center><b> LA CONSULTA DE PACIENTES PUEDE HACERSE POR LOS CAMPOS MARCADOS CON ASTERISCO (*)</b></center><br>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

	if(isset($werr) and isset($e) and $e > -1)
	{
		echo "<br><br><center><table border=0 aling=center>";
		$ingok=0;
		for ($i=0;$i<=$e;$i++)
			if(substr($werr[$i],0,3) == "OK!")
			{
				$ingok++;
				echo "<tr><td bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color5."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
				$whisI=substr($werr[$i],strpos($werr[$i],"-H")+12,strpos($werr[$i],"-I")-2-(strpos($werr[$i],"-H")+11)+1);
				$wninI=substr($werr[$i],strpos($werr[$i],"-I")+11);
			}
			else
				echo "<tr><td bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
		echo "</table><br><br></center>";
		if($ingok > 0)
			echo "<center><br><br><A HREF='../Reportes/r001-Admision.php?wpachi=".$whisI."&amp;&wingni=".$wninI."&amp;empresa=".$empresa."'target='_blank'>Impresion de Ingreso</a><br><br></center>";
	}

	//********************************************************************************************************
	//*                                         DATOS DEL INGRESO                                            *                                                                 *
	//********************************************************************************************************

	$wfei=date("Y-m-d"); //agregado
	$whin=date("H:i:s");

	echo "<table border=0 align=center id=tipo2>";

	//*******CONSULTA DE INFORMACION  DEL INGRESO*********
	$querysR=str_replace("%20"," ",$querysR);
	//if(isset($ok)  and $ok == 3 and !isset($change))
	if(isset($ok)  and $ok == 3)
	{
		if(isset($n))
		{
			$estado="1";
			if(isset($querysR) and $querysR != "")
			{
				$querysR=stripslashes($querysR);
				$qaR=$querysR;
			}
			else
			{
				$querysR = "select Inghis, Ingnin, Ingfei, Inghin, Ingsei, Ingtin, Ingcai, Ingtpa, Ingcem, Ingent, Ingord, Ingpol, Ingnco, Ingdie, Ingtee, Ingtar from  ".$empresa."_000101 where Inghis='".$whis."' ";
				$querysR .=" Order by  ID ";
				$err = mysql_query($querysR,$conex) or die(mysql_errno()."error en linea 2263".mysql_error());
				$numeroR = mysql_num_rows($err);
				if($numeroR > 0)
				{
					$querysT = "select Ingnin from  ".$empresa."_000101 where Inghis='".$whis."' ORDER BY ID desc ";
					$errT = mysql_query($querysT,$conex) or die(mysql_errno()."error en linea 2268".mysql_error());
					$row = mysql_fetch_array($errT);
					$wmaxing=$row[0];
				}
				$numeroR=$numeroR - 1;
			}
		}
		else
			$numeroR=-1;
		if ($numeroR>=0)
		{
			if(isset($qaR))
			{
				$qaR=str_replace(chr(34),chr(39),$qaR);
				$qaR=substr($qaR,0,strpos($qaR," limit "));
				$querysR=$qaR;
			}
			if(isset($qaR) and $qaR == $querysR)
			{
				if(isset($wb) and $wb == 3)
				{
					$wposR = $wposR  + 1;
					if ($wposR > $numeroR)
						$wposR=$numeroR;
				}
				elseif(isset($wb) and $wb == 4)
				{
					$wposR = $wposR  - 1;
					if ($wposR < 0)
						$wposR=0;
				}
			}
			else
				$wposR=0;
			if(isset($wnin) and $wnin > 0 and $wnin <= $wmaxing and isset($wb) and $wb == 3 and $wposR < $wnin)
				$wposR=$wnin - 1;
			$wpR=$wposR+1;
			$querysR .=  " limit ".$wposR.",1";
			$err = mysql_query($querysR,$conex) or die(mysql_errno()."error en linea 2306".mysql_error());
			$querysR=str_replace(chr(39),chr(34),$querysR);
			echo "<input type='HIDDEN' name= 'querysR' value='".$querysR."'>";
			echo "<input type='HIDDEN' name= 'wposR' value='".$wposR."'>";
			echo "<input type='HIDDEN' name= 'numeroR' value='".$numeroR."'>";
			if ($numeroR >=  0)
			{
				$row = mysql_fetch_array($err);
				if(!isset($change))
				{
					$wnin=$row[1];
					// Impresion del Ingreso en Consulta de Pacientes
					echo "<center><br><br><A HREF='../Reportes/r001-Admision.php?wpachi=".$whis."&amp;wingni=".$wnin."&amp;empresa=".$empresa."'target='_blank'>Impresion de Ingreso</a><br><br></center>";
					$wfei=$row[2];
					$whin=$row[3];
					$wsei=$row[4];
					$wtin=$row[5];
					$wcai=$row[6];
					$wtpa=$row[7];
					$wnit=$row[8];
					if($wtpa == "E")
					{
						$query = "SELECT Empnom  from ".$empresa."_000024 where Empcod =  '".$wnit."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2329".mysql_error());
						$row1 = mysql_fetch_array($err1);
						$wnitw=$row1[0];
					}
					else
						$wnitw=$row[8];
					$went=$row[9];
					$word=$row[10];
					$wpol=$row[11];
					$wnco=$row[12];
					$wdie=$row[13];
					$wtee=$row[14];
					$wtar=$row[15];
				}
			}
			else
			{
				if($whis == "")
				{
					$wnin="";
					$wfei=date("Y-m-d");
					$whin=date("H:i:s");
					$wsei="";
					$wtin="";
					$wcai="";
					$wtpa="";
					$wnit="";
					$wnitw="";
					$went="0";
					$word="0";
					$wpol="0";
					$wnco="";
					$wdie="";
					$wtee="";
					$wtar="";
				}

			}
		}
		if(isset($wpR))
		{
			$estado="1";
			$nR=$numeroR +1 ;
			echo "<tr><td align=right colspan=5><font size=2><b>Registro Nro. ".$wpR." De ".$nR."</b></font></td></tr>";
		}
		else
			echo "<tr><td align=right colspan=5><font size=2 color='#CC0000'><b>Consulta Sin Registros</b></font></td></tr>";
	}
	echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>DATOS DEL INGRESO</b></font></td></tr>";
	if($ok != 3)
	{
		if($whis == "")
			$query = "SELECT ingnin  from ".$empresa."_000101 where inghis='0' order by ID desc";
		else
			$query = "SELECT ingnin  from ".$empresa."_000101 where inghis='".$whis."' order by ID desc";
		$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2383".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$row = mysql_fetch_array($err);
			$wnin=$row[0] + 1;
			echo "<tr><td bgcolor=".$color.">Ingreso Nro. : </td><td bgcolor=".$color."><font size=6><b><input type='TEXT' name='wnin' size=10 maxlength=10  id='w25' class=tipo3 value='".$wnin."'></b></font></td></tr>";
		}
		else
		{
			$wnin= 1;
			echo "<tr><td bgcolor=".$color.">Ingreso Nro. : </td><td bgcolor=".$color."><font size=6><b><input type='TEXT' name='wnin' size=10 maxlength=10  id='w25' class=tipo3 value='".$wnin."'></b></font></td></tr>";
		}
	}
	else
		echo "<tr><td bgcolor=".$color.">Ingreso Nro. : </td><td bgcolor=".$color."><font size=6><b><input type='TEXT' name='wnin' size=10 maxlength=10  id='w25' class=tipo3  value='".$wnin."'></b></font></td></tr>";
	echo "<input type='HIDDEN' name= 'wnin' value='".$wnin."'>";
	$cal="calendario('wfei','2')";

	echo "<tr><td bgcolor=".$color.">Fecha  : </td><td bgcolor=".$color."><input type='TEXT' name='wfei' size=10 maxlength=10  id='wfei' readonly='readonly'  class=tipo3 value='".$wfei."'><button id='trigger2' onclick=".$cal.">...</button>";
	?>
	<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfei',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	//]]></script>
	<?php
	echo "</td></tr>";

	echo "<tr><td bgcolor=".$color.">Hora  : </td><td bgcolor=".$color."><input type='TEXT' name='whin' size=8 maxlength=8  id='w17'  class=tipo3 value='".$whin."'></td></tr>";
	echo "<tr><td bgcolor=".$color.">Servicio : </td>";
	$query = "SELECT Ccocod, Ccodes   from ".$empresa."_000003 where Ccoest='on' and Ccotip='A' order by Ccodes";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2411".mysql_error());
	$num = mysql_num_rows($err);
	echo "<td bgcolor=".$color."><select name='wsei' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wsei=ver($wsei);
			if($wsei == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<tr><td bgcolor=".$color.">Tipo Ingreso : </td>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='07' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2430".mysql_error());
	$num = mysql_num_rows($err);
	echo "<td bgcolor=".$color."><select name='wtin' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtin=ver($wtin);
			if($wtin == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<tr><td bgcolor=".$color.">Causa Ingreso : </td>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='08' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2449".mysql_error());
	$num = mysql_num_rows($err);
	echo "<td bgcolor=".$color."><select name='wcai' id=tipo1>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wcai=ver($wcai);
			if($wcai == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<tr><td bgcolor=".$color.">Tipo Paciente : </td>";
	$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='09' and Selest='on'  order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 1557".mysql_error());
	$num = mysql_num_rows($err);
	if( !isset($idCita) ){	//2010-01-15
		$idCita = '';
	}
	if(!isset($weda))
		$weda=0;
	if(!isset($querys))
		$querys="";
	else
		$querys=str_replace(" ","%20",$querys);
	if(!isset($querysR))
		$querysR="";
	else
		$querysR=str_replace(" ","%20",$querysR);
	if(!isset($wpos))
		$wpos=0;
	if(!isset($wposR))
		$wposR=0;
	if(!isset($numero))
		$numero=0;
	if(!isset($numeroR))
		$numeroR=0;
	if(!isset($wmaxing))
		$wmaxing=0;

	$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".@$wact."','".$idCita."','".$wemp2."')";

	echo "<td bgcolor=".$color."><select name='wtpa' OnChange=".$id." id=tipo1>";
	echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtpa=ver($wtpa);
			if($wtpa == $row[0])
				echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td></tr>";
	echo "<tr><td align=center bgcolor=#999999 colspan=2><b><font size='4'>ENTIDAD RESPONSABLE</font></b></td></tr>";
	if(isset($wnitw) and $wnitw != "" and ver($wtpa) == "E")
	{
		if( !isset($idCita) ){	//2010-01-15
			$idCita = '';
		}
		if(!isset($weda))
			$weda=0;
		if(!isset($querys))
			$querys="";
		else
			$querys=str_replace(" ","%20",$querys);
		if(!isset($querysR))
			$querysR="";
		else
			$querysR=str_replace(" ","%20",$querysR);
		if(!isset($wpos))
			$wpos=0;
		if(!isset($wposR))
			$wposR=0;
		if(!isset($numero))
			$numero=0;
		if(!isset($numeroR))
			$numeroR=0;
		if(!isset($wmaxing))
			$wmaxing=0;

		$id="ajaxquery('1','4','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".$wact."','".$idCita."','".$wemp2."')";

		echo "<tr><td bgcolor=".$color.">Nit/Cedula : </td><td bgcolor=".$color."><input type='TEXT' name='wnitw' size=20 id='w18' maxlength=30  class=tipo3 value='".$wnitw."' OnBlur=".$id.">";
	}
	else
	{
		if( !isset($idCita) ){	//2010-01-15
			$idCita = '';
		}
		if(!isset($weda))
			$weda=0;
		if(!isset($querys))
			$querys="";
		else
			$querys=str_replace(" ","%20",$querys);
		if(!isset($querysR))
			$querysR="";
		else
			$querysR=str_replace(" ","%20",$querysR);
		if(!isset($wpos))
			$wpos=0;
		if(!isset($wposR))
			$wposR=0;
		if(!isset($numero))
			$numero=0;
		if(!isset($numeroR))
			$numeroR=0;
		if(!isset($wmaxing))
			$wmaxing=0;

		$id="ajaxquery('1','4','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".@$wact."','".$idCita."','".$wemp2."')";

		echo "<tr><td bgcolor=".$color.">Nit/Cedula : </td><td bgcolor=".$color."><input type='TEXT' name='wnitw' size=20  id='w18' maxlength=30  class=tipo3 value='".$wdoc."' OnBlur=".$id.">";
	}
	$wsw=0;
	if(isset($okx) and $okx == "4")
	{
		if( !isset($idCita) ){	//2010-01-15
			$idCita = '';
		}
		if(!isset($weda))
			$weda=0;
		if(!isset($querys))
			$querys="";
		else
			$querys=str_replace(" ","%20",$querys);
		if(!isset($querysR))
			$querysR="";
		else
			$querysR=str_replace(" ","%20",$querysR);
		if(!isset($wpos))
			$wpos=0;
		if(!isset($wposR))
			$wposR=0;
		if(!isset($numero))
			$numero=0;
		if(!isset($numeroR))
			$numeroR=0;
		if(!isset($wmaxing))
			$wmaxing=0;

		$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".$wact."','".$idCita."','".$wemp2."')";

		echo "<select name='wnit' Onchange=".$id." id=tipo1>";
		if(isset($wnitw) and $wnitw != "")
		{
			$query = "SELECT Empcod, Empnom, Empdir, Emptel, Emptar  from ".$empresa."_000024 where Empnom like '%".$wnitw."%'  and Empest='on' order by Empnom";
			$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2606".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<option value='SELECCIONE'>SELECCIONE</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				}

			}
			else
				echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		}
		echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select>";
	}
	else
	{
		echo "<select name='wnit' id=tipo1>";
		if(isset($wnit) and ver($wtpa) == "E")
		{
			$wnit=ver($wnit);
			$query = "SELECT Empcod, Empnom from ".$empresa."_000024 where Empcod = '".$wnit."' and Empest='on'";
			$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2631".mysql_error());
			$row = mysql_fetch_array($err);
			echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
		else
		{
			$query = "SELECT Empcod, Empnom from ".$empresa."_000024 where Empnom = 'PARTICULAR' ";
			$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2638".mysql_error());
			$row = mysql_fetch_array($err);
			echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select>";
	}
	echo "</td></tr>";
	if(ver($wtpa) == "E")
	{
		if(!isset($wnit))
			$query = "SELECT Empcod, Empnom, Empdir, Emptel, Emptar  from ".$empresa."_000024 where Empcod =  '".$row[0]."'  and Empest='on' order by Empnom";
		else
			$query = "SELECT Empcod, Empnom, Empdir, Emptel, Emptar  from ".$empresa."_000024 where Empcod =  '".$wnit."'  and Empest='on' order by Empnom";
		$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2651".mysql_error());
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$went=$row[1];
		$wdie=$row[2];
		$wtee=$row[3];
		$wtar=$row[4];
		echo "<tr><td bgcolor=".$color.">Nombre : </td><td bgcolor=".$color."><input type='TEXT' name='went' id='w19' size=20 maxlength=20  class=tipo3 value='".$went."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Direccion :  </td><td bgcolor=".$color."><input type='TEXT' name='wdie' id='w20' size=20 maxlength=20  class=tipo3 value='".$wdie."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Telefonos : </td><td bgcolor=".$color."><input type='TEXT' name='wtee' id='w21' size=20 maxlength=20  class=tipo3 value='".$wtee."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Tarifa : </td>";
		$query = "SELECT Tarcod, Tardes from ".$empresa."_000025 where Tarcod='".substr($wtar,0,strpos($wtar,"-"))."' and Tarest='on'  order by Tardes";
		$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2663".mysql_error());
		$num = mysql_num_rows($err);
		echo "<td bgcolor=".$color."><select name='wtar' id=tipo1>";
		echo "<option value='SELECCIONE'>SELECCIONE</option>";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wtar=ver($wtar);
				if($wtar == $row[0])
					echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td bgcolor=".$color.">Numero de Orden : </td><td bgcolor=".$color."><input type='TEXT' name='word'  id='w22' size=20 maxlength=20  class=tipo3 value='".$word."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Numero de Poliza : </td><td bgcolor=".$color."><input type='TEXT' name='wpol'  id='w23' size=20 maxlength=20  class=tipo3 value='".$wpol."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Numero de Contrato : </td><td bgcolor=".$color."><input type='TEXT' name='wnco'  id='w24' size=20 maxlength=20  class=tipo3 value='".$wnco."'></td></tr>";
	}
	else
	{
		//$went=$wno1." ".$wno2." ".$wap1." ".$wap2;
		$query = "SELECT Empcod, Empnom, Empdir, Emptel, Emptar  from ".$empresa."_000024 where Empnom = 'PARTICULAR'  order by Empnom";
		$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2689".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$row = mysql_fetch_array($err);
			$went=$row[1];
			$wdie=$row[2];
			$wtee=$row[3];
			if(@$wtar == "NO APLICA")
				$wtar=$row[4];
		}
		else
		{
			$went="NO APLICA";
			$wdie="NO APLICA";
			$wtee="NO APLICA";
			$wtar="NO APLICA";
		}
		echo "<tr><td bgcolor=".$color.">Nombre : </td><td bgcolor=".$color."><input type='TEXT' name='went' id='w19' size=20 maxlength=20  class=tipo3 value='".$went."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Direccion : </td><td bgcolor=".$color."><input type='TEXT' name='wdie' id='w20' size=20 maxlength=20  class=tipo3 value='".@$wdir."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Telefonos : </td><td bgcolor=".$color."><input type='TEXT' name='wtee' id='w21' size=20 maxlength=20  class=tipo3 value='".@$wtel."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Tarifa : </td>";
		if(@$wtar == "NO APLICA" or @$wtar == "SELECCIONE")
			$wtar="01-PARTICULAR";
		//$query = "SELECT Tarcod, Tardes from ".$empresa."_000025 where Tarcod='".substr($wtar,0,strpos($wtar,"-"))."' and  Tarest='on'  order by Tardes";
		$query = "SELECT Tarcod, Tardes from ".$empresa."_000025 where Tarest='on'  order by Tarcod";
		$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 2715".mysql_error());
		$num = mysql_num_rows($err);
		echo "<td bgcolor=".$color."><select name='wtar' id=tipo1>";
		echo "<option value='SELECCIONE'>SELECCIONE</option>";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wtar=ver($wtar);
				if($wtar == $row[0])
					echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo "</td></tr>";
		$word=0;
		$wpol=0;
		$wnco=0;
		echo "<tr><td bgcolor=".$color.">Numero de Orden : </td><td bgcolor=".$color."><input type='TEXT' name='word'  id='w22' size=20 maxlength=20  class=tipo3 value='".$word."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Numero de Poliza : </td><td bgcolor=".$color."><input type='TEXT' name='wpol'  id='w23' size=20 maxlength=20  class=tipo3 value='".$wpol."'></td></tr>";
		echo "<tr><td bgcolor=".$color.">Numero de Contrato : </td><td bgcolor=".$color."><input type='TEXT' name='wnco'  id='w24' size=20 maxlength=20  class=tipo3 value='".$wnco."'></td></tr>";
	}
	if(isset($ok) and $ok == 3)
	{
		echo "<input type='HIDDEN' name= 'wmaxing' value='".$wmaxing."'>";
		if($wnin == $wmaxing and isset($wact) and $wact=="on")
		{
			if( !isset($idCita) ){	//2010-01-15
				$idCita = '';
			}
			if( !isset($idCita) ){	//2010-01-15
				$idCita = '';
			}
			if(!isset($weda))
				$weda=0;
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($querysR))
				$querysR="";
			else
				$querysR=str_replace(" ","%20",$querysR);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($wposR))
				$wposR=0;
			if(!isset($numero))
				$numero=0;
			if(!isset($numeroR))
				$numeroR=0;
			if(!isset($wmaxing))
				$wmaxing=0;

			$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".$wact."','".$idCita."','".$wemp2."')";

			echo "<tr><td  bgcolor=#999999 align=center colspan=2><input type='RADIO' name=wb value=3  onclick=".$id."> Adelante <input type='RADIO' name=wb value=4 onclick=".$id."> Atras";
			if( !isset($idCita) ){	//2010-01-15
				$idCita = '';
			}
			if(!isset($weda))
				$weda=0;
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($querysR))
				$querysR="";
			else
				$querysR=str_replace(" ","%20",$querysR);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($wposR))
				$wposR=0;
			if(!isset($numero))
				$numero=0;
			if(!isset($numeroR))
				$numeroR=0;
			if(!isset($wmaxing))
				$wmaxing=0;

			$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",$wdep)."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".$wact."','".$idCita."','".$wemp2."')";

			if(isset($change))
				echo "&nbsp <input type='checkbox' name='change' checked onclick=".$id.">Modificar Ingreso</td></td></tr>";
			else
				echo "&nbsp <input type='checkbox' name='change' onclick=".$id.">Modificar Ingreso</td></td></tr>";
		}
		else
		{
			if( !isset($idCita) ){	//2010-01-15
				$idCita = '';
			}
			if(!isset($weda))
				$weda=0;
			if(!isset($querys))
				$querys="";
			else
				$querys=str_replace(" ","%20",$querys);
			if(!isset($querysR))
				$querysR="";
			else
				$querysR=str_replace(" ","%20",$querysR);
			if(!isset($wpos))
				$wpos=0;
			if(!isset($wposR))
				$wposR=0;
			if(!isset($numero))
				$numero=0;
			if(!isset($numeroR))
				$numeroR=0;
			if(!isset($wmaxing))
				$wmaxing=0;

			$id="ajaxquery('1','1','".$wdat."',\"".str_replace(" ",".",str_replace(" ",".",$wdep))."\",'".$wnin."','".$weda."','".$empresa."','".$querys."','".$querysR."','".$wpos."','".$wposR."','".$numero."','".$numeroR."','".$wmaxing."','".$wact."','".$idCita."','".$wemp2."')";

			echo "<tr><td  bgcolor=#999999 align=center colspan=2><input type='RADIO' name=wb value=3  onclick=".$id."> Adelante <input type='RADIO' name=wb value=4 onclick=".$id."> Atras</td></tr>";
		}
	}
	else
		echo "<tr><td bgcolor=#999999 align=center colspan=2>&nbsp</td></tr>";
	echo"</table>";
	echo"</form>";
}
echo "</div>";
?>
</body>
</html>
