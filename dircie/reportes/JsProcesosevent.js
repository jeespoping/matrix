/**
 * Created by Will on 08/06/2016.
 */
function foco()
{
    $(window).ready(function(){
        $("body").animate({ scrollTop: $(document).height()}, 850);
    });
}

/*////////////////////////////////////////////////////////////////////////////////////////*/

function mostrarop1(idpaciente,fechas,protocolo)
{
    document.getElementById(idpaciente).style.display = 'block';
    document.getElementById(fechas).style.display = 'none';
    document.getElementById(protocolo).style.display = 'none';
    //document.getElementById(contenido).style.marginTop = '5px';
}

function mostrarop2(idpaciente,fechas,protocolo)
{
    document.getElementById(idpaciente).style.display = 'none';
    document.getElementById(fechas).style.display = 'block';
    document.getElementById(protocolo).style.display = 'none';
    //document.getElementById(contenido).style.marginTop = '5px';
}

function mostrarop3(idpaciente,fechas,protocolo)
{
    document.getElementById(idpaciente).style.display = 'none';
    document.getElementById(fechas).style.display = 'none';
    document.getElementById(protocolo).style.display = 'block';
    //document.getElementById(contenido).style.marginTop = '5px';
}

function imprimir()
{
    document.getElementById('divInforme').style.marginTop = '1';
    document.getElementById('loginform').style.display = 'none';
    document.getElementById('selector').style.display = 'none';
    document.getElementById('imprimir').style.visibility = 'hidden';
    window.print();
}