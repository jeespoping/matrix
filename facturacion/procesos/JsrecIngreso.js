/**
 * Created by informatica on 2020-02-07.
 */
function imprimir(fuente,factura)
{
    ancho = 500;   alto = 200;
    var miPopup = null;
    var winl = (screen.width - ancho) / 2;
    var wint = 300;
    settings = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=no, toolbar=no';

    miPopup = window.open("recIngresoPrint.php?"+"fuente="+fuente.value+"&factura="+factura.value,"miwin",settings);

    miPopup.focus();
}
