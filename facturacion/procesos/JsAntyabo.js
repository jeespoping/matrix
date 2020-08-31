function imprimir()
{
    var fuente = document.getElementById('fuente').value;  var factura = document.getElementById('factura').value;

    ancho = 500;   alto = 200;
    var miPopup = null;
    var winl = (screen.width - ancho) / 2;
    var wint = 300;
    settings = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=no, toolbar=no';

    miPopup = window.open("antyaboPrint.php?"+"fuente="+fuente+"&factura="+factura,"miwin",settings);

    miPopup.focus();
}