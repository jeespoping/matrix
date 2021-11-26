function buscarDat(accion,nitCed,tipoResp)
{
    if(nitCed !== '')
    {
        // definimos la anchura y altura de la ventana
        var altura=10; var anchura=10;
        // calculamos la posicion x e y para centrar la ventana
        var y=parseInt((window.screen.height/2)-(altura/2));
        var x=parseInt((window.screen.width/2)-(anchura/2));
        // mostramos la ventana centrada

        window.open("facSer_process.php?accion="+accion+'&nitCed='+nitCed+'&tipoResp='+tipoResp.value,
            target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
    }
}

//ABRIR MAESTRO DE CONCEPTOS O DE CENTROS DE COSTOS UNIX
function openConc(accion,field_id,numRows)
{
    var altura=400; var anchura=700;
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));
    // mostramos la ventana centrada

    window.open("facSer_process.php?accion="+accion+'&field_id='+field_id+'&numRows='+numRows,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
}

//OBTENER VALOR PORCENTAJE
function percent(f_origen, f_destino,numRows,f_destino2)
{
    var numRow = numRows.value;
    var val_Origen = document.getElementById(f_origen).value;
    var val_Destino = document.getElementById(f_destino).value;
    var percent = (val_Origen * val_Destino)/100;

    document.getElementById(f_destino2).value = percent.toLocaleString();
    document.getElementById(5+'-'+numRow).value = '';
}

//REALIZAR GRABACION TEMPORAL CONCEPTO A CONCEPTO:
function copiarVal2(numRows)
{
    var numRow = numRows.value;
	
	if (document.getElementById(1+'-'+numRow).disabled) // NO procesar líneas registradas
		return;
		
	//alert('registrar');
		
    //var numRow = numRows;
    var fuente = document.getElementById('fuente').value;           var fechafac = document.getElementById('fechafac').value;
    var plazo = document.getElementById('plazo').value;             var docPac = document.getElementById('docPac').value;
    var nomPac = document.getElementById('nomPac').value;           var tipoResp = document.getElementById('tipoResp').value;
    var nitResp = document.getElementById('nitResp').value;         var descResp = document.getElementById('descResp').value;
    var tarifa = document.getElementById('tarifa').value;           var tipoServ = document.getElementById('tipoServ').value;
    var concepto = document.getElementById(1+'-'+numRow).value;     var ccostos = document.getElementById(2+'-'+numRow).value;
    var valconc = document.getElementById(3+'-'+numRow).value;      var valdesc = document.getElementById(4+'-'+numRow).value;
    var valneto = document.getElementById(5+'-'+numRow).value;      var numFac = document.getElementById('numFactu').value;
    valconc = quita_comas2(valconc);
    valdesc = quita_comas2(valdesc);
    valneto = quita_comas2(valneto);

    /// MANDAR LOS DATOS PARA GRABAR:

    if(concepto !== '' && ccostos !== '' && valconc !== '' && valdesc !== '' && valneto !== '')
    {
        //alert('ENTRA AL IF');
        document.getElementById(1+'-'+numRow).disabled = true;  document.getElementById(1+'-'+numRow).classList.add('inpSaved');
        document.getElementById(2+'-'+numRow).disabled = true;  document.getElementById(2+'-'+numRow).classList.add('inpSaved');
        document.getElementById(3+'-'+numRow).disabled = true;  document.getElementById(3+'-'+numRow).classList.add('inpSaved');
        document.getElementById(4+'-'+numRow).disabled = true;  document.getElementById(4+'-'+numRow).classList.add('inpSaved');
        document.getElementById(5+'-'+numRow).disabled = true;  document.getElementById(5+'-'+numRow).classList.add('inpSaved');

        var altura = 10;    var anchura = 10;
        var y=parseInt((window.screen.height/2)-(altura/2));    var x=parseInt((window.screen.width/2)-(anchura/2));

        //CONCEPTOS QUE VAN SIN IVA:
        if(concepto == 2002 || concepto == 2023 || concepto == 2088)
        {
            //alert('SAVE TEMPO');
            window.open("facSer_process.php?accion="+'saveTempo'+
                '&fuente='+fuente+'&fechafac='+fechafac+'&plazo='+plazo+'&docPac='+docPac+
                '&nomPac='+nomPac+'&tipoResp='+tipoResp+'&nitResp='+nitResp+'&descResp='+descResp+
                '&tarifa='+tarifa+'&tipoServ='+tipoServ+'&concep='+concepto+'&ccosto='+ccostos+
                '&valcon='+valconc+'&valdesc='+valdesc+'&valneto='+valneto+'&numFac='+numFac+'&numRows='+numRow,
                target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
        }
        //CONCEPTOS CON IVA:
        else
        {
            //alert('SAVE TEMPO2');
            window.open("facSer_process.php?accion="+'saveTempo2'+
                '&fuente='+fuente+'&fechafac='+fechafac+'&plazo='+plazo+'&docPac='+docPac+
                '&nomPac='+nomPac+'&tipoResp='+tipoResp+'&nitResp='+nitResp+'&descResp='+descResp+
                '&tarifa='+tarifa+'&tipoServ='+tipoServ+'&concep='+concepto+'&ccosto='+ccostos+
                '&valcon='+valconc+'&valdesc='+valdesc+'&valneto='+valneto+'&numFac='+numFac+'&numRows='+numRow,
                target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
        }
        //DESHABILITAR LIMPIAR CAMPOS LINEA SUPERIOR
        numRowsToDel = parseInt(numRow) - 1;
        if(numRowsToDel > 0)
        {
            document.getElementById('clean'+numRowsToDel).style.pointerEvents = 'none';
            document.getElementById('clean'+numRowsToDel).style.backgroundColor = 'gray';
        }
    }
    else
    {
        alert('TODOS LOS CAMPOS SON OBLIGATORIOS');
    }
}

function copiarVal3(numRows)
{
    var numRow = numRows.value;
    var fuente = document.getElementById('fuente').value;       var fechafac = document.getElementById('fechafac').value;
    var plazo = document.getElementById('plazo').value;         var docPac = document.getElementById('docPac').value;
    var nomPac = document.getElementById('nomPac').value;       var tipoResp = document.getElementById('tipoResp').value;
    var nitResp = document.getElementById('nitResp').value;     var descResp = document.getElementById('descResp').value;
    var tarifa = document.getElementById('tarifa').value;       var tipoServ = document.getElementById('tipoServ').value;
    var concepto = document.getElementById(1+'-'+numRow).value; var ccostos = document.getElementById(2+'-'+numRow).value;
    var valconc = document.getElementById(3+'-'+numRow).value;  var valdesc = document.getElementById(4+'-'+numRow).value;
    var valneto = document.getElementById(5+'-'+numRow).value;  var numFac = document.getElementById('numFactu').value;

    /// MANDAR LOS DATOS PARA GRABAR:
    if(concepto != '' && ccostos != '' && valconc != 0 && valdesc != '' && valneto != 0)
    {
        document.getElementById(1+'-'+numRow).disabled = true;  document.getElementById(1+'-'+numRow).classList.add('inpSaved');
        document.getElementById(2+'-'+numRow).disabled = true;  document.getElementById(2+'-'+numRow).classList.add('inpSaved');
        document.getElementById(3+'-'+numRow).disabled = true;  document.getElementById(3+'-'+numRow).classList.add('inpSaved');
        document.getElementById(4+'-'+numRow).disabled = true;  document.getElementById(4+'-'+numRow).classList.add('inpSaved');
        document.getElementById(5+'-'+numRow).disabled = true;  document.getElementById(5+'-'+numRow).classList.add('inpSaved');

        document.getElementById('add').style.pointerEvents = 'none';
        document.getElementById('add').style.backgroundColor = 'grey';
        document.getElementById('less').style.pointerEvents = 'none';
        document.getElementById('less').style.backgroundColor = 'grey';

        var altura = 10;    var anchura = 10;
        var y=parseInt((window.screen.height/2)-(altura/2));    var x=parseInt((window.screen.width/2)-(anchura/2));

        window.open("facSer_process.php?accion="+'saveTempo'+
            '&fuente='+fuente+'&fechafac='+fechafac+'&plazo='+plazo+'&docPac='+docPac+
            '&nomPac='+nomPac+'&tipoResp='+tipoResp+'&nitResp='+nitResp+'&descResp='+descResp+
            '&tarifa='+tarifa+'&tipoServ='+tipoServ+'&concep='+concepto+'&ccosto='+ccostos+
            '&valcon='+valconc+'&valdesc='+valdesc+'&valneto='+valneto+'&numFac='+numFac+'&numRows='+numRow,
            target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");

    }
    else
    {
        alert('TODOS LOS CAMPOS SON OBLIGATORIOS');
    }
}

function sumValCon(valcon)
{
    var numFac = document.getElementById('numFactu').value;
    var altura = 10;    var anchura = 10;
    var y=parseInt((window.screen.height/2)-(altura/2));    var x=parseInt((window.screen.width/2)-(anchura/2));

    window.open("facSer_Process.php?accion="+'sumValCon'+'&numFac='+numFac+'&valcon='+valcon,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");


}

function sumValDes(valdes)
{
    var numFac = document.getElementById('numFactu').value;
    var altura = 10;    var anchura = 10;
    var y=parseInt((window.screen.height/2)-(altura/2));    var x=parseInt((window.screen.width/2)-(anchura/2));

    window.open("facSer_Process.php?accion="+'sumValDes'+'&numFac='+numFac+'&valdesc='+valdes,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
}

function sumValNet(valnet)
{
    var numFac = document.getElementById('numFactu').value;
    var altura = 10;    var anchura = 10;
    var y=parseInt((window.screen.height/2)-(altura/2));    var x=parseInt((window.screen.width/2)-(anchura/2));

    window.open("facSer_Process.php?accion="+'sumValNet'+'&numFac='+numFac+'&valneto='+valnet,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
}

function actiSave(totValcon,totValnet,numRows)
{
    var numRow = numRows.value;
    //var totValco = totValcon.value;    var totValne = totValnet.value;
    var numRowsW = document.getElementById('numRows').value;
    var concepto = document.getElementById(1+'-'+numRowsW).value;

    if(numRow === 1)
    {
        if(concepto === 2002 || concepto === 2023 || concepto === 2088)
        {
            document.getElementById('divListo').style.display = 'none';
            document.getElementById('divSave').style.display = 'block';
            document.getElementById('add').style.pointerEvents = 'none';
            document.getElementById('add').style.backgroundColor = 'gray';
        }
        else
        {
            //alert('concepto ='+concepto);
            //document.getElementById('divListo').style.display = 'block';
            //document.getElementById('btnListo').disabled = true;
            //document.getElementById('divSave').style.display = 'none';
            //document.getElementById('add').style.pointerEvents = 'none';
            //document.getElementById('add').style.backgroundColor = '#5BC0DE';
            alert('EL CONCEPTO INGRESADO REQUIRE INGRESAR UN CONCEPTO DE IVA');
        }
    }
    else
    {
        if(concepto != 2002 || concepto != 2023 || concepto != 2088)
        {
            document.getElementById('divListo').style.display = 'none';
            document.getElementById('divSave').style.display = 'block';
            document.getElementById('add').style.pointerEvents = 'none';
            document.getElementById('add').title = 'Boton Deshabilitado';
            document.getElementById('add').style.backgroundColor = 'gray';
        }
        else
        {
            document.getElementById('btnListo').enabled = true;
        }
    }

    /*
    if(totValco != 0 && totValne != 0)
    {
        if(numRows === 1)
        {
            if(concepto === 2002 || concepto === 2023 || concepto === 2088)
            {
                document.getElementById('divListo').style.display = 'none';
                document.getElementById('divSave').style.display = 'block';
                document.getElementById('add').style.pointerEvents = 'none';
                document.getElementById('add').title = 'Boton Deshabilitado';
                document.getElementById('add').style.backgroundColor = 'gray';
            }
            else
            {

                document.getElementById('add').style.pointerEvents = 'auto';
                document.getElementById('add').style.backgroundColor = '#5BC0DE';
                document.getElementById('btnListo').disabled = true;
                alert('EL CONCEPTO INGRESADO REQUIRE INGRESAR UN CONCEPTO DE IVA');
            }
            document.getElementById(1+'-'+numRows).readOnly = 'true';
            document.getElementById(2+'-'+numRows).readOnly = 'true';
            document.getElementById(3+'-'+numRows).readOnly = 'true';
            document.getElementById(4+'-'+numRows).readOnly = 'true';
            document.getElementById(5+'-'+numRows).readOnly = 'true';
        }
        else
        {
            if(concepto == 2002 || concepto == 2023 || concepto == 2088)
            {
                document.getElementById('divListo').style.display = 'none';
                document.getElementById('divSave').style.display = 'block';
                document.getElementById('add').style.pointerEvents = 'none';
                document.getElementById('add').title = 'Boton Deshabilitado';
                document.getElementById('add').style.backgroundColor = 'gray';
            }
            else
            {
                document.getElementById('add').style.pointerEvents = 'auto';
                document.getElementById('add').style.backgroundColor = '#5BC0DE';
                document.getElementById('btnListo').disabled = true;
                alert('EL CONCEPTO INGRESADO REQUIRE INGRESAR UN CONCEPTO DE IVA');
            }

            var numRow = numRows.value;
            document.getElementById(1+'-'+numRow).readOnly = 'true';
            document.getElementById(2+'-'+numRow).readOnly = 'true';
            document.getElementById(3+'-'+numRow).readOnly = 'true';
            document.getElementById(4+'-'+numRow).readOnly = 'true';
            document.getElementById(5+'-'+numRow).readOnly = 'true';
        }

    }
    */
}

function setFoco(numRows)
{
    var numRow = numRows.value;
    document.getElementById(4+'-'+numRow).focus();
}

//asignar valor a VALOR NETO (valor concepto - valor descuento)
function setFoco2(numRows)
{
    var numRow = numRows;
    var concepto = document.getElementById(1+'-'+numRow).value;
    var ccostos = document.getElementById(2+'-'+numRow).value;
    var valorConc = document.getElementById(3+'-'+numRow).value;    valorConc = quita_comas2(valorConc);
    var valorDesc = document.getElementById(4+'-'+numRow).value;    valorDesc = quita_comas2(valorDesc);

    var valorNeto = parseInt(valorConc - valorDesc);
    var totalValNeto = document.getElementById('totValnet').value;

    document.getElementById(5+'-'+numRow).focus();
    document.getElementById(5+'-'+numRow).value = valorNeto.toLocaleString();
    document.getElementById(5+'-'+numRow).readOnly = 'true';

    if(totalValNeto != 0)
    {
        document.getElementById(1+'-'+numRow).classList.add('inpSaved');
        //document.getElementById(1+'-'+numRow).disabled = 'true';
        document.getElementById(2+'-'+numRow).classList.add('inpSaved');
        //document.getElementById(2+'-'+numRow).disabled = 'true';
        document.getElementById(3+'-'+numRow).classList.add('inpSaved');
        //document.getElementById(3+'-'+numRow).disabled = 'true';
        document.getElementById(4+'-'+numRow).classList.add('inpSaved');
        //document.getElementById(4+'-'+numRow).disabled = 'true';
    }
}

//asignar valor a VALOR NETO (valor concepto - valor descuento) PARA LAS FILAS CREADAS DINAMICAMENTE
function setFoco3(numRows)
{
    var concepto = document.getElementById(1+'-'+numRows).value;
    var ccostos = document.getElementById(2+'-'+numRows).value;
    var valorConc = document.getElementById(3+'-'+numRows).value;
    var valorDesc = document.getElementById(4+'-'+numRows).value;
    var valorNeto = parseInt(valorConc - valorDesc);

    document.getElementById(5+'-'+numRows).focus();
    document.getElementById(5+'-'+numRows).value = valorNeto;
    document.getElementById(5+'-'+numRows).readOnly = 'true';

    //si los campos concepto, centro costos y valor concepto NO SON NULL:
    if(concepto != '' && ccostos != '' && valorConc != '')
    {
        document.getElementById(1+'-'+numRows).classList.add('inpSaved');
        document.getElementById(1+'-'+numRows).disabled = 'true';
        document.getElementById(2+'-'+numRows).classList.add('inpSaved');
        document.getElementById(2+'-'+numRows).disabled = 'true';
        document.getElementById(3+'-'+numRows).classList.add('inpSaved');
        document.getElementById(3+'-'+numRows).disabled = 'true';
        document.getElementById(4+'-'+numRows).classList.add('inpSaved');
        document.getElementById(4+'-'+numRows).disabled = 'true';
    }
}

function consCcosto(numRows)
{
    document.getElementById(2+'-'+numRows).value = document.getElementById('2-1').value;
    document.getElementById(2+'-'+numRows).readOnly = 'true';
}

function checkConcepto(concepto,numRows,numFact)
{
    var altura = 10;    var anchura = 10;
    var y=parseInt((window.screen.height/2)-(altura/2));    var x=parseInt((window.screen.width/2)-(anchura/2));

    window.open("facSer_Process.php?accion="+'chkConcepto'+'&chkConcept='+concepto+'&numRows='+numRows+'&numFac='+numFact,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
}

function cleanVal(numRows)
{
    var numRow = numRows.value;
    document.getElementById(1+'-'+numRow).value = '';   document.getElementById(1+'-'+numRow).classList.remove('inpSaved');
    document.getElementById(2+'-'+numRow).value = '';   document.getElementById(2+'-'+numRow).classList.remove('inpSaved');
    document.getElementById(3+'-'+numRow).value = '';   document.getElementById(3+'-'+numRow).classList.remove('inpSaved');
    document.getElementById(4+'-'+numRow).value = '0';  document.getElementById(4+'-'+numRow).classList.remove('inpSaved');
    document.getElementById(5+'-'+numRow).value = '';  document.getElementById(5+'-'+numRow).classList.remove('inpSaved');
    document.getElementById('totValcon').value = '0';
    document.getElementById('totValdes').value = '0';
    document.getElementById('totValnet').value = '0';

    document.getElementById(1+'-'+numRow).disabled = false;
    document.getElementById(2+'-'+numRow).disabled = false;
    document.getElementById(3+'-'+numRow).disabled = false;
    document.getElementById(4+'-'+numRow).disabled = false;

    if(numRow == 1)
    {
        document.getElementById('add').style.pointerEvents = 'none';
        document.getElementById('add').style.backgroundColor = 'grey';
    }
}

function cleanVal2(numRows)
{
    var numRow = numRows.value;
    var valcon = 0; var valdes = 0;
    document.getElementById(1+'-'+numRow).value = '';   document.getElementById(1+'-'+numRow).classList.remove('inpSaved');
    /*document.getElementById(2+'-'+numRow).value = '';*/   document.getElementById(2+'-'+numRow).classList.remove('inpSaved');
    document.getElementById(3+'-'+numRow).value = '0';   document.getElementById(3+'-'+numRow).classList.remove('inpSaved');
    document.getElementById(4+'-'+numRow).value = '0';  document.getElementById(4+'-'+numRow).classList.remove('inpSaved');
    document.getElementById(5+'-'+numRow).value = '0';  document.getElementById(5+'-'+numRow).classList.remove('inpSaved');

    document.getElementById(1+'-'+numRow).disabled = false;
    document.getElementById(2+'-'+numRow).disabled = false;
    document.getElementById(3+'-'+numRow).disabled = false;
    document.getElementById(4+'-'+numRow).disabled = false;

    if(numRow == 1)
    {
        document.getElementById('add').style.pointerEvents = 'none';
        document.getElementById('add').style.backgroundColor = 'grey';
    }

    var numFac = document.getElementById('numFactu').value;
    var altura = 10;    var anchura = 10;
    var y=parseInt((window.screen.height/2)-(altura/2));    var x=parseInt((window.screen.width/2)-(anchura/2));

    window.open("facSer_Process.php?accion="+'sumValCon2'+'&numFac='+numFac+'&valcon='+valcon+'&valdesc='+valdes,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
}

function validarDato(dato,parametro,numRows)
{
    //var numRow = numRows.value;
    var numRow = numRows;
    /*if(numRow == 0){numRow = document.getElementById('numRows'+numRows).value;}*/
    var altura=10; var anchura=10;
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));

    window.open("facSer_process.php?accion="+'validarDato'+'&dato='+dato+'&parametro='+parametro+'&numRows='+numRow,
    target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
}

function respxNom(nombreResp,tipoResp)
{
    var altura=400; var anchura=1000;
    var y=parseInt((window.screen.height/2)-(altura/2));
    var x=parseInt((window.screen.width/2)-(anchura/2));

    if(nombreResp != '')
    {
        window.open("facSer_process.php?accion="+'respxNom'+'&nomresp='+nombreResp+'&tipoResp='+tipoResp.value,
            target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
    }
}

function cleanFields()
{
    document.getElementById('nitResp').value = '';
    document.getElementById('descResp').value = '';
}

// Valida que todos los registros de la factura estén grabados 
// (que se les haya dado click en el botón ok de la derecha a cada uno)

function ValidarDatosActualizados()
{
	var regs = "";
    if (document.getElementById('1-1').value != "" && !document.getElementById('1-1').disabled)
		regs = regs + document.getElementById('1-1').value + ",";
    if (document.getElementById('1-2').value != "" && !document.getElementById('1-2').disabled)
		regs = regs + document.getElementById('1-2').value + ",";
    if (document.getElementById('1-3').value != "" && !document.getElementById('1-3').disabled)
		regs = regs + document.getElementById('1-3').value + ",";
    if (document.getElementById('1-4').value != "" && !document.getElementById('1-4').disabled)
		regs = regs + document.getElementById('1-4').value + ",";
    if (document.getElementById('1-5').value != "" && !document.getElementById('1-5').disabled)
		regs = regs + document.getElementById('1-5').value + ",";

	if (regs != ""){
		alert ("Registrar el concepto: " + regs + " para continuar.");
		return false;
	}
	else
	  return true;
}


// Llamado por el botón verde totalizar del final de la factura
function totalizar()
{
	//alert("totalizar");
	if (!ValidarDatosActualizados()) return;
	  
    var valor1 = document.getElementById('3-1').value;  if(valor1 == ''){valor1 = 0} else{valor1 = quita_comas2(valor1);}
    var valor2 = document.getElementById('3-2').value;  if(valor2 == ''){valor2 = 0} else{valor2 = quita_comas2(valor2);}
    var valor3 = document.getElementById('3-3').value;  if(valor3 == ''){valor3 = 0} else{valor3 = quita_comas2(valor3);}
    var valor4 = document.getElementById('3-4').value;  if(valor4 == ''){valor4 = 0} else{valor4 = quita_comas2(valor4);}
    var valor5 = document.getElementById('3-5').value;  if(valor5 == ''){valor5 = 0} else{valor5 = quita_comas2(valor5);}

    var valor6 = document.getElementById('4-1').value;  if(valor6 == ''){valor6 = 0} else{valor6 = quita_comas2(valor6);}
    var valor7 = document.getElementById('4-2').value;  if(valor7 == ''){valor7 = 0} else{valor7 = quita_comas2(valor7);}
    var valor8 = document.getElementById('4-3').value;  if(valor8 == ''){valor8 = 0} else{valor8 = quita_comas2(valor8);}
    var valor9 = document.getElementById('4-4').value;  if(valor9 == ''){valor9 = 0} else{valor9 = quita_comas2(valor9);}
    var valor10 = document.getElementById('4-5').value;  if(valor10 == ''){valor10 = 0} else{valor10 = quita_comas2(valor10);}

    var valor11 = document.getElementById('5-1').value;  if(valor11 == ''){valor11 = 0} else{valor11 = quita_comas2(valor11);}
    var valor12 = document.getElementById('5-2').value;  if(valor12 == ''){valor12 = 0} else{valor12 = quita_comas2(valor12);}
    var valor13 = document.getElementById('5-3').value;  if(valor13 == ''){valor13 = 0} else{valor13 = quita_comas2(valor13);}
    var valor14 = document.getElementById('5-4').value;  if(valor14 == ''){valor14 = 0} else{valor14 = quita_comas2(valor14);}
    var valor15 = document.getElementById('5-5').value;  if(valor15 == ''){valor15 = 0} else{valor15 = quita_comas2(valor15);}

    valorTotalConcepto = parseInt(valor1) + parseInt(valor2) + parseInt(valor3) + parseInt(valor4) + parseInt(valor5);
    valorTotalDescuento = parseInt(valor6) + parseInt(valor7) + parseInt(valor8) + parseInt(valor9) + parseInt(valor10);
    valorTotalNeto = parseInt(valor11) + parseInt(valor12) + parseInt(valor13) + parseInt(valor14) + parseInt(valor15);

    document.getElementById('totValcon2').value = valorTotalConcepto.toLocaleString();
    document.getElementById('totValdes2').value = valorTotalDescuento.toLocaleString();
    document.getElementById('totValnet2').value = valorTotalNeto.toLocaleString();
}

function format(input)
{
    var num = input.value.replace(/\./g,"");
    if(!isNaN(num)){
        num = num.toString().split("").reverse().join("").replace(/(?=\d*\.?)(\d{3})/g,"$1.");
        num = num.split("").reverse().join("").replace(/^[\.]/,"");
        input.value = num;
    }else{
        input.value = input.value.replace(/[^\d\.]*/g,"");
    }
}

function quita_comas(numero,campo)
{
    var cadenas = numero.split(".");
    cadena_sin_comas = "";
    for(i = 0; i < cadenas.length;i++){
        cadena_sin_comas = cadena_sin_comas+cadenas[i];
    }
    //return cadena_sin_comas;
    document.getElementById(campo).value = cadena_sin_comas;
}

function quita_comas2(numero)
{
    var cadenas = numero.split(".");
    cadena_sin_comas = "";
    for(i = 0; i < cadenas.length;i++){
        cadena_sin_comas = cadena_sin_comas+cadenas[i];
    }
    return cadena_sin_comas;
}

//DESHABILITAR Y ELIMINAR REGISTROS DE amefactmp CUANDO SE LE DA LIMPIAR CAMPO:
function cleanField(numRows)
{
    if(numRows > 1){var btn_Anterior = parseInt(numRows) - 1;} else{btn_Anterior = 1;}
    var btn_Proximo = parseInt(numRows) + 1;
    var factTemporal = document.getElementById('numFactu').value;           //campo FACTURA TEMPORAL
    var field_concepToDel = document.getElementById(1+'-'+numRows);         //campo CONCEPTO
    var field_ccostoToDel = document.getElementById(2+'-'+numRows);         //campo CENTRO DE COSTOS
    var field_valconcepToDel = document.getElementById(3+'-'+numRows);      //campo VALOR CONCEPTO
    var field_valdescueToDel = document.getElementById(4+'-'+numRows);      //campo VALOR DESCUENTO
    var field_valNetoToDel = document.getElementById(5+'-'+numRows);        //campo VALOR NETO
    var field_TotConcepto = document.getElementById('totValcon2');          //campo TOTAL CONCEPTOS
    var field_TotDesc = document.getElementById('totValdes2');              //campo TOTAL DESCUENTOS
    var field_TotNeto = document.getElementById('totValnet2');              //campo TOTAL NETOS
    var btn_Limpiar = document.getElementById('clean'+btn_Anterior);        //boton limpiar FILA SUPERIOR
    var btn_LimpiarInf = document.getElementById('clean'+btn_Proximo);      //boton limpiar FILA INFERIOR
    var field_desConcep = document.getElementById('detConcepto'+numRows);   //campo DESCRIPCION CONCEPTO
    var field_desCcosto = document.getElementById('detCcosto'+numRows);     //campo DESCRIPCION CONCEPTO

    //ALMACENAR CAMPOS PARA IR A BORRAR (amefactmp), LIMPIAR Y HABILITAR CAMPOS PARA EDITAR
    concepToDel = field_concepToDel.value;          field_concepToDel.value = '';       field_concepToDel.disabled = false;     field_concepToDel.style.backgroundColor = '#FFFFFF';
    ccostoToDel = field_ccostoToDel.value;          field_ccostoToDel.value = '';       field_ccostoToDel.disabled = false;     //field_ccostoToDel.style.backgroundColor = '#FFFFFF';
    valconcepToDel = field_valconcepToDel.value;    field_valconcepToDel.value = '';    field_valconcepToDel.disabled = false;  field_valconcepToDel.style.backgroundColor = '#FFFFFF';
    valDescToDel = field_valdescueToDel.value;      field_valdescueToDel.value = 0;     field_valdescueToDel.disabled = false;  field_valdescueToDel.style.backgroundColor = '#FFFFFF';
    valnetoToDel = field_valNetoToDel.value;        field_valNetoToDel.value = '';      field_valNetoToDel.disabled = false;    field_valNetoToDel.style.backgroundColor = '#FFFFFF';
    field_TotConcepto.value = 0;                    field_TotDesc.value = 0;            field_TotNeto.value = 0;    //TOTALES   field_concepToDel.style.backgroundColor = 'white';
    btn_LimpiarInf.style.pointerEvents = 'none';    btn_LimpiarInf.style.backgroundColor = '#808080';
    document.getElementById('divListo').style.display = 'block';
    document.getElementById('divSave').style.display = 'none';

    valconcepToDel = quita_comas2(valconcepToDel);
    valDescToDel = quita_comas2(valDescToDel);
    valnetoToDel = quita_comas2(valnetoToDel);

    //habilitar boton LIMPIAR CAMPOS de la fila inmediatamente superior
    btn_Limpiar.style.pointerEvents = 'auto';   btn_Limpiar.style.backgroundColor = '#D84F4E';
    field_desConcep.value = '';                 field_desCcosto.value = '';

    //deshabilitar campos fila siguiente:
    //alert('linea siguiente = '+btn_Proximo);
    var field_concepToDes = document.getElementById(1+'-'+btn_Proximo); field_concepToDes.style.backgroundColor = '#CFD0CD';  field_concepToDes.readOnly = 'true';    field_concepToDes.value = '';
    var field_ccostoToDes = document.getElementById(2+'-'+btn_Proximo); field_ccostoToDes.style.backgroundColor = '#CFD0CD';  field_ccostoToDes.readOnly = 'true';    field_ccostoToDes.value = '';
    var field_valconToDes = document.getElementById(3+'-'+btn_Proximo); field_valconToDes.style.backgroundColor = '#CFD0CD';  field_valconToDes.readOnly = 'true';    field_valconToDes.value = '';
    var field_valdesToDes = document.getElementById(4+'-'+btn_Proximo); field_valdesToDes.style.backgroundColor = '#CFD0CD';  field_valdesToDes.readOnly = 'true';    field_valdesToDes.value = 0;
    var field_valnetToDes = document.getElementById(5+'-'+btn_Proximo); field_valnetToDes.style.backgroundColor = '#CFD0CD';  field_valnetToDes.readOnly = 'true';    field_valnetToDes.value = '';


    //ABRIR VENTANA DE PROCESOS Y ENVIAR DATOS PARA EL BORRADO:
    var altura = 10;    var anchura = 10;   var y=parseInt((window.screen.height/2)-(altura/2));    var x=parseInt((window.screen.width/2)-(anchura/2));

    window.open("facSer_process.php?accion="+'borrarReg'+'&concepToDel='+concepToDel+'&ccostoToDel='+ccostoToDel+'&valconcepToDel='+valconcepToDel+
                '&valDescToDel='+valDescToDel+'&valnetoToDel='+valnetoToDel+'&numRows='+numRows+'&factTemporal='+factTemporal,
        target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
}

//LOS CAMPOS DEL FORMULARIO ESTAN INICIALMENTE INACTIVOS
function dishabledFields(numRows)
{
    fieldConcepto = document.getElementById('1-'+numRows);      fieldCcosto = document.getElementById('2-'+numRows);
    fieldValConce = document.getElementById('3-'+numRows);      fieldValDes = document.getElementById('4-'+numRows);
    fieldValNeto = document.getElementById('5-'+numRows);       btn_Limpiar = document.getElementById('clean'+numRows);    //boton limpiar FILA SIGUIENTE
    btnBusConcep = document.getElementById('chkConc'+numRows);  btnBusCcost = document.getElementById('chkCcos'+numRows);
    btnPercent = document.getElementById('chkPorcent'+numRows);

    fieldConcepto.style.backgroundColor = '#CFD0CD';    fieldConcepto.readOnly = 'true';    //btn_Limpiar.style.pointerEvents = 'none';
    fieldCcosto.style.backgroundColor = '#CFD0CD';      fieldCcosto.readOnly = 'true';
    fieldValConce.style.backgroundColor = '#CFD0CD';    fieldValConce.readOnly = 'true';
    fieldValDes.style.backgroundColor = '#CFD0CD';      fieldValDes.readOnly = 'true';
    fieldValNeto.style.backgroundColor = '#CFD0CD';     fieldValNeto.readOnly = 'true';
    btn_Limpiar.style.pointerEvents = 'none';           btn_Limpiar.style.backgroundColor = 'gray';
    btnBusConcep.style.pointerEvents = 'none';          btnBusConcep.style.backgroundColor = 'gray';
    btnBusCcost.style.pointerEvents = 'none';           btnBusCcost.style.backgroundColor = 'gray';
    btnPercent.style.pointerEvents = 'none';            btnPercent.style.backgroundColor = 'gray';
}

//HACER VALIDACION FINAL DE TODOS LOS CAMPOS
function validarTodo()
{
	if (!ValidarDatosActualizados())
	  return;
	  
    var valTercero = document.getElementById('nitResp').value;  var valTotReg1 = document.getElementById('5-1').value;
    var totNeto = document.getElementById('totValnet2').value;
    var conc1 = document.getElementById('1-1').value;   conc1 = evalConc(conc1);
    if(conc1 != '2002' || conc1 != '2023' || conc1 != '2088'){concepto1 = 'noIva'} else{concepto1 = 'iva'}

    var conc2 = document.getElementById('1-2').value;   conc2 = evalConc(conc2);
    if(conc2 != '2002' || conc2 != '2023' || conc2 != '2088'){concepto2 = 'noIva'}  else{concepto2 = 'iva'}

    var conc3 = document.getElementById('1-3').value;   conc3 = evalConc(conc3);
    if(conc3 != '2002' || conc3 != '2023' || conc3 != '2088'){concepto3 = 'noIva'}  else{concepto3 = 'iva'}

    var conc4 = document.getElementById('1-4').value;   conc4 = evalConc(conc4);
    if(conc4 != '2002' || conc4 != '2023' || conc4 != '2088'){concepto4 = 'noIva'}  else{concepto4 = 'iva'}

    var conc5 = document.getElementById('1-5').value;   conc5 = evalConc(conc5);
    if(conc5 != '2002' || conc5 != '2023' || conc5 != '2088'){concepto5 = 'noIva'}  else{concepto5 = 'iva'}

    if(isNaN(conc1)){conc1 = 0}  if(isNaN(conc2)){conc2 = 0}  if(isNaN(conc3)){conc3 = 0}  if(isNaN(conc4)){conc4 = 0}  if(isNaN(conc5)){conc5 = 0}
    var contConceptoIva = parseInt(conc1) + parseInt(conc2) + parseInt(conc3) + parseInt(conc4) + parseInt(conc5);

    if(contConceptoIva < 2)
    {
        if(concepto1 == 'iva' || concepto2 == 'iva' || concepto3 == 'iva' || concepto4 == 'iva' || concepto5 == 'iva')
        {
            alert('DEBE INGRESAR UN CONCEPTO DE IVA');
        }
        else
        {
            if(concepto1 == 'noIva' || concepto2 == 'noIva' || concepto3 == 'noIva' || concepto4 == 'noIva' || concepto5 == 'noIva')
            {
                if(valTercero != '' && valTotReg1 != '' && totNeto != '0')
                {
                    document.getElementById('divListo').style.display = 'none';
                    document.getElementById('divSave').style.display = 'block';
                }
                else
                {
                    alert('NO PUEDE GRABAR, TIENE CAMPOS OBLIGATORIOS SIN LLENAR');
                }
            }
        }
    }
    if(contConceptoIva >= 2)
    {
        if(valTercero != '' && valTotReg1 != '' && totNeto != '0')
        {
            document.getElementById('divListo').style.display = 'none';
            document.getElementById('divSave').style.display = 'block';
        }
        else
        {
            alert('NO PUEDE GRABAR, TIENE CAMPOS OBLIGATORIOS SIN LLENAR');
        }
    }

    function evalConc(concepto)
    {
        switch (concepto)
        {
            case '2089':
            case '2001':
            case '2021':
            case '2022':
            case '2025':
            case '2078':
            case '2079':
            case '4216':
            case '9819':
                concepto = 1;
                return concepto;
            break;
        }
    }
}

//VALIDAR QUE AL DIGITAR CONCEPTO 9819 YA EXISTA UN CONCEPTO DE IVA PREVIO AL CUAL APLICARSELO
function validar9819(concDigitado,numRow)
{
    //OBTENER TODOS LOS VALORES DIGITADOS EN CAMPO CONCEPTO:
    var fieldConcepto1 = document.getElementById('1-1');
    var fieldConcepto2 = document.getElementById('1-2');
    var fieldConcepto3 = document.getElementById('1-3');
    var fieldConcepto4 = document.getElementById('1-4');
    var fieldConcepto5 = document.getElementById('1-5');

    var conc1 = fieldConcepto1.value;
    if(conc1 == '2089' || conc1 == '2002' || conc1 == '2023' || conc1 == '2088' || conc1 == '2001' || conc1 == '2021' || conc1 == '2022' || conc1 == '2025' || conc1 == '2078' || conc1 == '2079' || conc1 == '4216')
    {conc1 = 'iva'}
    var conc2 = fieldConcepto2.value;
    if(conc2 == '2089' || conc2 == '2002' || conc2 == '2023' || conc2 == '2088' || conc2 == '2001' || conc2 == '2021' || conc2 == '2022' || conc2 == '2025' || conc2 == '2078' || conc2 == '2079' || conc2 == '4216')
    {conc2 = 'iva'}
    var conc3 = fieldConcepto3.value;
    if(conc3 == '2089' || conc3 == '2002' || conc3 == '2023' || conc3 == '2088' || conc3 == '2001' || conc3 == '2021' || conc3 == '2022' || conc3 == '2025' || conc3 == '2078' || conc3 == '2079' || conc3 == '4216')
    {conc3 = 'iva'}
    var conc4 = fieldConcepto4.value;
    if(conc4 == '2089' || conc4 == '2002' || conc4 == '2023' || conc4 == '2088' || conc4 == '2001' || conc4 == '2021' || conc4 == '2022' || conc4 == '2025' || conc4 == '2078' || conc4 == '2079' || conc4 == '4216')
    {conc4 = 'iva'}
    var conc5 = fieldConcepto5.value;
    if(conc5 == '2089' || conc5 == '2002' || conc5 == '2023' || conc5 == '2088' || conc5 == '2001' || conc5 == '2021' || conc5 == '2022' || conc5 == '2025' || conc5 == '2078' || conc5 == '2079' || conc5 == '4216')
    {conc5 = 'iva'}

    if(concDigitado == '9819')
    {
        if(conc1 != '')
        {
            if(conc1 != 'iva')
            {
                alert('NO HA INGRESADO UN CONCEPTO DE IVA, POR FAVOR VERIFIQUE');
                document.getElementById('1-'+numRow).value = '';
                document.getElementById('detConcepto'+numRow).value = '';
                document.getElementById('2-'+numRow).value = '';
                document.getElementById('detCcosto'+numRow).value = '';
                document.getElementById('3-'+numRow).value = '';
            }
        }

        if(conc2 != '' && conc2 != '9819')
        {
            if(conc2 != 'iva')
            {
                alert('NO HA INGRESADO UN CONCEPTO DE IVA, POR FAVOR VERIFIQUE');
                document.getElementById('1-'+numRow).value = '';
                document.getElementById('detConcepto'+numRow).value = '';
                document.getElementById('2-'+numRow).value = '';
                document.getElementById('detCcosto'+numRow).value = '';
                document.getElementById('3-'+numRow).value = '';
            }
        }

        if(conc3 != '' && conc3 != '9819')
        {
            if(conc3 != 'iva')
            {
                alert('NO HA INGRESADO UN CONCEPTO DE IVA, POR FAVOR VERIFIQUE');
                document.getElementById('1-'+numRow).value = '';
                document.getElementById('detConcepto'+numRow).value = '';
                document.getElementById('2-'+numRow).value = '';
                document.getElementById('detCcosto'+numRow).value = '';
                document.getElementById('3-'+numRow).value = '';
            }
        }

        if(conc4 != '' && conc4 != '9819')
        {
            if(conc4 != 'iva')
            {
                alert('NO HA INGRESADO UN CONCEPTO DE IVA, POR FAVOR VERIFIQUE');
                document.getElementById('1-'+numRow).value = '';
                document.getElementById('detConcepto'+numRow).value = '';
                document.getElementById('2-'+numRow).value = '';
                document.getElementById('detCcosto'+numRow).value = '';
                document.getElementById('3-'+numRow).value = '';
            }
        }

        if(conc5 != '' && conc5 != '9819')
        {
            if(conc5 != 'iva')
            {
                alert('NO HA INGRESADO UN CONCEPTO DE IVA, POR FAVOR VERIFIQUE');
                document.getElementById('1-'+numRow).value = '';
                document.getElementById('detConcepto'+numRow).value = '';
                document.getElementById('2-'+numRow).value = '';
                document.getElementById('detCcosto'+numRow).value = '';
                document.getElementById('3-'+numRow).value = '';
            }
        }
    }
}