/**
 * Created by Will on 01/05/2016.
 */
/*---------------------------PREGUNTA 1 ------------------------------*/
function seleccionar1()
{
    var valor1 = document.getElementById('comfama').value;
    if(valor1 == "0")
    {
        document.getElementById('comfama').src = "/matrix/images/medical/nomina/comf1-1.png";
        document.getElementById('comfenalco').src = "/matrix/images/medical/nomina/comfe1.png";
        document.getElementById('comfama').value = '1';
        document.getElementById('comfenalco').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('comfama').src = "/matrix/images/medical/nomina/comf1.png";
        document.getElementById('comfama').value = '0'
    }
}

function seleccionar2()
{
    var valor1 = document.getElementById('comfenalco').value;
    if(valor1 == "0")
    {
        document.getElementById('comfenalco').src = "/matrix/images/medical/nomina/comfe1-1.png";
        document.getElementById('comfama').src = "/matrix/images/medical/nomina/comf1.png";
        document.getElementById('comfenalco').value = '1';
        document.getElementById('comfama').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('comfenalco').src = "/matrix/images/medical/nomina/comfe1.png";
        document.getElementById('comfenalco').value = '0'
    }
}

/*-----------------------------------------------------------------*/

function validarCampos()
{
    window.onload = function ()
    {
        document.encuestaform.addEventListener('submit', validarFormulario);
    }

    function validarFormulario(evObject)
    {
        evObject.preventDefault();
        var pregunta1 = parseInt(document.getElementById('comfama').value);
        var pregunta2 = parseInt(document.getElementById('comfenalco').value);
        var total = pregunta1+pregunta2;

        if (total < 1)
        {
            window.alert('Debe marcar una opcion');
            formulario.focus();
        }
        else
        {
            miPopup = window.open("encuestacc_guardar.php?comfama="+comfama.value+"&comfenalco="+comfenalco.value,"miwin","width=500,height=150");
            miPopup.focus()

            document.encuestaform.submit();
        }
    }
}