/**
 * Created by W.Atehortua on 2018-01-22.
 */
function mostrarop1(etiqueta1,etiqueta2,etiqueta3,etiqueta4,divInstruccion)
{
    document.getElementById(divInstruccion).style.display = 'none';
    document.getElementById(etiqueta1).style.display = 'block';
    document.getElementById(etiqueta2).style.display = 'none';
    document.getElementById(etiqueta3).style.display = 'none';
    document.getElementById(etiqueta4).style.display = 'none';
}

function mostrarop2(etiqueta1,etiqueta2,etiqueta3,etiqueta4,divInstruccion)
{
    document.getElementById(divInstruccion).style.display = 'none';
    document.getElementById(etiqueta2).style.display = 'block';
    document.getElementById(etiqueta1).style.display = 'none';
    document.getElementById(etiqueta3).style.display = 'none';
    document.getElementById(etiqueta4).style.display = 'none';
}

function mostrarop3(etiqueta1,etiqueta2,etiqueta3,etiqueta4,divInstruccion)
{
    document.getElementById(divInstruccion).style.display = 'none';
    document.getElementById(etiqueta3).style.display = 'block';
    document.getElementById(etiqueta1).style.display = 'none';
    document.getElementById(etiqueta2).style.display = 'none';
    document.getElementById(etiqueta4).style.display = 'none';
}

function mostrarop4(etiqueta1,etiqueta2,etiqueta3,etiqueta4,divInstruccion)
{
    document.getElementById(divInstruccion).style.display = 'none';
    document.getElementById(etiqueta4).style.display = 'block';
    document.getElementById(etiqueta1).style.display = 'none';
    document.getElementById(etiqueta2).style.display = 'none';
    document.getElementById(etiqueta3).style.display = 'none';
}

function imprimir(etq1Nombre,etq1Cirujano,etq1Casa,etq1NomSistema,etq1NumCajas,etq1Lote,etq1Fv,etq1Metodo,etq1Responsable,etq1Fp,etq1Hora,idE)
{
    var valorId = idE.value;

    ancho = 500;   alto = 200;
    var miPopup = null;
    var winl = (screen.width - ancho) / 2;
    var wint = 300;
    settings = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=no, toolbar=no';

    miPopup = window.open("stkcenestPrint.php?" +
        "etq1Nombre="+etq1Nombre.value+
        "&etq1Cirujano="+etq1Cirujano.value+
        "&etq1Casa="+etq1Casa.value+
        "&etq1NomSistema="+etq1NomSistema.value+
        "&etq1NumCajas="+etq1NumCajas.value+
        "&etq1Lote="+etq1Lote.value+
        "&etq1Fv="+etq1Fv.value+
        "&etq1Metodo="+etq1Metodo.value+
        "&etq1Responsable="+etq1Responsable.value+
        "&etq1Fp="+etq1Fp.value+
        "&etq1Hora="+etq1Hora.value+
        "&idEtiqueta="+valorId,
        "miwin",settings);
    miPopup.focus();
}

function imprimir2(etq2Unidad,etq2Equipo,etq2Metodo,etq2Fv,etq2Lote,etq2Responsable,etq2Fp,etq2Hora,etq2NReproceso,idE2,etq2Codigo)
{
    var valorId = idE2.value;

    ancho = 500;   alto = 200;
    var miPopup = null;
    var winl = (screen.width - ancho) / 2;
    var wint = 300;
    settings = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=no, toolbar=no';

    miPopup = window.open("stkcenestPrint.php?" +
        "etq2Unidad="+etq2Unidad.value+
        "&etq2Equipo="+etq2Equipo.value+
        "&etq2Metodo="+etq2Metodo.value+
        "&etq2Fv="+etq2Fv.value+
        "&etq2Lote="+etq2Lote.value+
        "&etq2Responsable="+etq2Responsable.value+
        "&etq2Fp="+etq2Fp.value+
        "&etq2Hora="+etq2Hora.value+
        "&etq2NReproceso="+etq2NReproceso.value+
        "&idEtiqueta="+valorId+
        "&etq2Codigo="+etq2Codigo.value,
        "miwin",settings);
    miPopup.focus();
}

function imprimir3(etq3Detergente,etq3Fv,etq3Fp,etq3Hora,etq3Responsable,idE3)
{
    var valorId = idE3.value;

    ancho = 500;   alto = 200;
    var miPopup = null;
    var winl = (screen.width - ancho) / 2;
    var wint = 300;
    settings = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=no, toolbar=no';

    miPopup = window.open("stkcenestPrint.php?" +
        "etq3Detergente="+etq3Detergente.value+
        "&etq3Fv="+etq3Fv.value+
        "&etq3Fp="+etq3Fp.value+
        "&etq3Hora="+etq3Hora.value+
        "&etq3Responsable="+etq3Responsable.value+
        "&idEtiqueta="+valorId,
        "miwin",settings);
    miPopup.focus();
}

function imprimir4(etq4Unidad,etq4Equipo,etq4Fv,etq4Fp,etq4Hora,etq4Responsable,etq4NReproceso,idE4)
{
    var valorId = idE4.value;

    ancho = 500;   alto = 200;
    var miPopup = null;
    var winl = (screen.width - ancho) / 2;
    var wint = 300;
    settings = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=no, toolbar=no';

    miPopup = window.open("stkcenestPrint.php?" +
        "etq4Unidad="+etq4Unidad.value+
        "&etq4Equipo="+etq4Equipo.value+
        "&etq4Fv="+etq4Fv.value+
        "&etq4Fp="+etq4Fp.value+
        "&etq4Hora="+etq4Hora.value+
        "&etq4Responsable="+etq4Responsable.value+
        "&etq4NReproceso="+etq4NReproceso.value+
        "&idEtiqueta="+valorId,
        "miwin",settings);
    miPopup.focus();
}

function verificarNulos()
{
    var nombre = document.getElementById('etq1Nombre').value;
    var cirujano = document.getElementById('etq1Cirujano').value;
    var casa = document.getElementById('etq1Casa').value;
    var sistema = document.getElementById('etq1NomSistema').value;
    var cajas = document.getElementById('etq1NumCajas').value;
    var lote = document.getElementById('etq1Lote').value;
    var fechaV1 = document.getElementById('etq1Fv').value;
    var metodo = document.getElementById('etq1Metodo').value;

    if(nombre == '') {alert('El campo NOMBRE no debe estar vacio')} else(nombre = 1);
    if(cirujano == '') {alert('El campo CIRUJANO no debe estar vacio')} else(cirujano = 1);
    if(casa == '') {alert('El campo CASA COMERCIAL no debe estar vacio')} else(casa = 1);
    if(sistema == '') {alert('El campo NOMBRE DEL SISTEMA no debe estar vacio')} else(sistema = 1);
    if(cajas == '') {alert('El campo NUMERO DE CAJAS no debe estar vacio')} else(cajas = 1);
    if(lote == '') {alert('El campo LOTE no debe estar vacio')} else(lote = 1);
    if(fechaV1 == '') {alert('El campo FECHA VENCIMIENTO no debe estar vacio')} else(fechaV1 = 1);
    if(metodo == '') {alert('El campo METODO no debe estar vacio')} else(metodo = 1);

    var totalcamposEtq1 = nombre+cirujano+casa+sistema+cajas+lote+fechaV1+metodo;

    if(totalcamposEtq1 == 8)
    {
        imprimir(etq1Nombre,etq1Cirujano,etq1Casa,etq1NomSistema,etq1NumCajas,etq1Lote,etq1Fv,etq1Metodo,etq1Responsable,etq1Fp,etq1Hora,idE);
    }
}

function verificarNulos2()
{
    var unidad = document.getElementById('etq2Unidad').value;
    var equipo = document.getElementById('etq2Equipo').value;
    var metodo2 = document.getElementById('etq2Metodo').value;
    var fechaV2 = document.getElementById('etq2Fv').value;
    var lote2 = document.getElementById('etq2Lote').value;
    var reproceso2 = document.getElementById('etq2NReproceso').value;
    var codigo = document.getElementById('etq2Codigo').value;

    if(unidad == '') {alert('El campo UNIDAD no debe estar vacio')} else(unidad = 1);
    if(equipo == '') {alert('El campo EQUIPO no debe estar vacio')} else(equipo = 1);
    if(metodo2 == '') {alert('El campo METODO no debe estar vacio')} else(metodo2 = 1);
    if(fechaV2 == '') {alert('El campo FECHA VENCIMIENTO no debe estar vacio')} else(fechaV2 = 1);
    if(lote2 == '') {alert('El campo LOTE no debe estar vacio')} else(lote2 = 1);
    if(reproceso2 == '') {alert('El campo NUMERO DE REPROCESO no debe estar vacio')} else(reproceso2 = 1);

    var totalcamposEtq2 = unidad+equipo+metodo2+fechaV2+lote2+reproceso2;

    if(totalcamposEtq2 == 6)
    {
        imprimir2(etq2Unidad,etq2Equipo,etq2Metodo,etq2Fv,etq2Lote,etq2Responsable,etq2Fp,etq2Hora,etq2NReproceso,idE2,etq2Codigo);
    }
}

function verificarNulos3()
{
    var detergente = document.getElementById('etq3Detergente').value;
    var fechaV3 = document.getElementById('etq3Fv').value;

    if(detergente == '') {alert('El campo DETERGENTE no debe estar vacio')} else(detergente = 1);
    if(fechaV3 == '') {alert('El campo FECHA VENCIMIENTO no debe estar vacio')} else(fechaV3 = 1);

    var totalcamposEtq3 = detergente+fechaV3;

    if(totalcamposEtq3 == 2)
    {
        imprimir3(etq3Detergente,etq3Fv,etq3Fp,etq3Hora,etq3Responsable,idE3);
    }
}

function verificarNulos4()
{
    var unidad4 = document.getElementById('etq4Unidad').value;
    var equipo4 = document.getElementById('etq4Equipo').value;
    var fechaV4 = document.getElementById('etq4Fv').value;
    var reproceso4 = document.getElementById('etq4NReproceso').value;

    if(unidad4 == '') {alert('El campo UNIDAD no debe estar vacio')} else(unidad4 = 1);
    if(equipo4 == '') {alert('El campo EQUIPO no debe estar vacio')} else(equipo4 = 1);
    if(fechaV4 == '') {alert('El campo FECHA VENCIMIENTO no debe estar vacio')} else(fechaV4 = 1);
    if(reproceso4 == '') {alert('El campo NUMERO DE REPROCESO no debe estar vacio')} else(reproceso4 = 1);

    var totalcamposEtq4 = unidad4+equipo4+fechaV4+reproceso4;

    if(totalcamposEtq4 == 4)
    {
        imprimir4(etq4Unidad,etq4Equipo,etq4Fv,etq4Fp,etq4Hora,etq4Responsable,etq4NReproceso,idE4);
    }
}

function printStiker()
{
    window.print();
    window.close();
}
