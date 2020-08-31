<!--
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

function ajaxquery(fila, entrada, id, opeAnt, criAnt)
{
	//alert ('hola');
	var x = new Array();

	//me indica que hacer con el id, segun tipo de entrada

	switch(entrada)
	{

		case "1":
		document.images['status'].src='/matrix/images/medical/reloj.gif';
		x[1] = id;
		st="wope="+x[1];
		document.forms.busqueda.w1.value='';
		break;

		case "2":
		document.images['status'].src='/matrix/images/medical/reloj.gif';
		for (i=0;i<document.forms.busqueda.nume.length;i++)
		{
			if (document.forms.busqueda.nume[i].checked==true)
			{
				x[1]=document.forms.busqueda.nume[i].value;
				x[2]=	document.getElementById("w1").value;
				st="wope="+x[1]+"&wcri="+x[2];
				break;
			}
		}
		break;

		case "3": //para modificar los datos
		x[1] = id;
		x[2] = document.forms['resultados['+fila+']'].elements['ced['+fila+']'].value;
		x[3] = document.forms['resultados['+fila+']'].elements['nom['+fila+']'].value;
		x[4] = document.forms['resultados['+fila+']'].elements['tel['+fila+']'].value;
		x[5] = document.forms['resultados['+fila+']'].elements['tar['+fila+']'].value;
		x[6] = document.forms['resultados['+fila+']'].elements['ide['+fila+']'].value;
		x[11] = document.forms['resultados['+fila+']'].elements['dir['+fila+']'].value;
		x[12] = document.forms['resultados['+fila+']'].elements['emp['+fila+']'].value;
		x[13] = document.forms['resultados['+fila+']'].elements['car['+fila+']'].value;
		x[14] = document.forms['resultados['+fila+']'].elements['ocu['+fila+']'].value;
		x[7]= fila;
		x[8]= opeAnt;
		x[9]= criAnt;
		x[10] = document.forms['resultados['+fila+']'].elements['mai['+fila+']'].value;
		st="wacc="+x[1]+"&wced="+x[2]+"&wnom="+x[3]+"&wtel="+x[4]+"&wtar="+x[5]+"&wide="+x[6]+"&wdato="+x[7]+"&wope="+x[8]+"&wcri="+x[9]+"&wmai="+x[10]+"&wdir="+x[11]+"&wemp="+x[12]+"&wcar="+x[13]+"&wocu="+x[14];
		break;

		case "4": //para eliminar un registro
		document.images['status'].src='/matrix/images/medical/reloj.gif';
		x[1] = id;
		x[2] = document.forms['resultados['+fila+']'].elements['ced['+fila+']'].value;
		x[3] = document.forms['resultados['+fila+']'].elements['cau['+fila+']'].value;
		x[4] = document.forms['resultados['+fila+']'].elements['red['+fila+']'].value;
		x[5] = document.forms['resultados['+fila+']'].elements['dev['+fila+']'].value;
		x[6] = document.forms['resultados['+fila+']'].elements['acu['+fila+']'].value;
		x[7]= fila;
		x[8]= opeAnt;
		x[9]= criAnt;
		x[10] = document.forms['resultados['+fila+']'].elements['ide['+fila+']'].value;
		st="wacc="+x[1]+"&wced="+x[2]+"&wcau="+x[3]+"&wred="+x[4]+"&wdev="+x[5]+"&wacu="+x[6]+"&wdato="+x[7]+"&wope="+x[8]+"&wcri="+x[9]+"&wide="+x[10];
		break;

		case "5": //para eliminar un registro
		document.images['status'].src='/matrix/images/medical/reloj.gif';
		x[1] = id;
		x[2] = document.forms['resultados['+fila+']'].elements['ced['+fila+']'].value;
		x[3] = document.forms['resultados['+fila+']'].elements['cau['+fila+']'].value;
		x[4] = document.forms['resultados['+fila+']'].elements['red['+fila+']'].value;
		x[5] = document.forms['resultados['+fila+']'].elements['dev['+fila+']'].value;
		x[6] = document.forms['resultados['+fila+']'].elements['acu['+fila+']'].value;
		x[7]= fila;
		x[8]= opeAnt;
		x[9]= criAnt;
		x[10] = document.forms['resultados['+fila+']'].elements['ide['+fila+']'].value;
		st="wacc="+x[1]+"&wced="+x[2]+"&wcau="+x[3]+"&wred="+x[4]+"&wdev="+x[5]+"&wacu="+x[6]+"&wdato="+x[7]+"&wope="+x[8]+"&wcri="+x[9]+"&wide="+x[10];
		fila='2';
		break;
	}

	ajax=nuevoAjax();
	ajax.open("POST", "rep_puntos.php",true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(st);
	
	ajax.onreadystatechange=function()
	{

		if (ajax.readyState==4)
		{
			document.images['status'].src='/matrix/images/medical/blanco.png';
			document.getElementById(+fila).innerHTML=ajax.responseText;

		}
	}
	ajax.send(null);
}

function MsgOkCancel(fila, entrada, id, opeAnt, criAnt)
{
	var fRet;

	switch(id)
	{

		case "1":
		fRet = confirm('Estas seguro que desea modificar la cedula o sus puntos?');
		break;
		case "2":
		fRet = confirm('Estas seguro que desea eliminar el registro?');
		break;
	}
	if (fRet==true)
	{
		ajaxquery(fila, entrada, id, opeAnt, criAnt);
	}
}

//-->
