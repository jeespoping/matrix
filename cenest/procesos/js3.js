/**
 * Created by Will on 01/05/2016.
 */
/*---------------------------PREGUNTA 1 ------------------------------*/
function seleccionar1_1()
{
    var valor1 = document.getElementById('f1_1').value;
    if(valor1 == "0")
    {
        document.getElementById('f1_1').src = "/matrix/images/medical/cenest/1-1.png";
        document.getElementById('f1_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f1_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f1_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f1_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f1_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f1_1').value = '1';
        document.getElementById('f1_2').value = '0';
        document.getElementById('f1_3').value = '0';
        document.getElementById('f1_4').value = '0';
        document.getElementById('f1_5').value = '0';
        document.getElementById('f1_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f1_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f1_1').value = '0'
    }
}

function seleccionar1_2()
{
    var valor1 = document.getElementById('f1_2').value;
    if(valor1 == "0")
    {
        document.getElementById('f1_2').src = "/matrix/images/medical/cenest/2-1.png";
        document.getElementById('f1_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f1_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f1_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f1_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f1_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f1_2').value = '1';
        document.getElementById('f1_1').value = '0';
        document.getElementById('f1_3').value = '0';
        document.getElementById('f1_4').value = '0';
        document.getElementById('f1_5').value = '0';
        document.getElementById('f1_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f1_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f1_2').value = '0'
    }
}

function seleccionar1_3()
{
    var valor1 = document.getElementById('f1_3').value;
    if(valor1 == "0")
    {
        document.getElementById('f1_3').src = "/matrix/images/medical/cenest/3-1.png";
        document.getElementById('f1_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f1_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f1_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f1_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f1_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f1_3').value = '1';
        document.getElementById('f1_1').value = '0';
        document.getElementById('f1_2').value = '0';
        document.getElementById('f1_4').value = '0';
        document.getElementById('f1_5').value = '0';
        document.getElementById('f1_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f1_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f1_3').value = '0'
    }
}

function seleccionar1_4()
{
    var valor1 = document.getElementById('f1_4').value;
    if(valor1 == "0")
    {
        document.getElementById('f1_4').src = "/matrix/images/medical/cenest/4-1.png";
        document.getElementById('f1_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f1_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f1_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f1_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f1_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f1_4').value = '1';
        document.getElementById('f1_1').value = '0';
        document.getElementById('f1_2').value = '0';
        document.getElementById('f1_3').value = '0';
        document.getElementById('f1_5').value = '0';
        document.getElementById('f1_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f1_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f1_4').value = '0'
    }
}

function seleccionar1_5()
{
    var valor1 = document.getElementById('f1_5').value;
    if(valor1 == "0")
    {
        document.getElementById('f1_5').src = "/matrix/images/medical/cenest/5-1.png";
        document.getElementById('f1_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f1_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f1_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f1_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f1_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f1_5').value = '1';
        document.getElementById('f1_1').value = '0';
        document.getElementById('f1_2').value = '0';
        document.getElementById('f1_3').value = '0';
        document.getElementById('f1_4').value = '0';
        document.getElementById('f1_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f1_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f1_5').value = '0'
    }
}

function seleccionar1_6()
{
    var valor1 = document.getElementById('f1_6').value;
    if(valor1 == "0")
    {
        document.getElementById('f1_6').src = "/matrix/images/medical/cenest/6-1.png";
        document.getElementById('f1_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f1_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f1_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f1_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f1_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f1_6').value = '1';
        document.getElementById('f1_1').value = '0';
        document.getElementById('f1_2').value = '0';
        document.getElementById('f1_3').value = '0';
        document.getElementById('f1_4').value = '0';
        document.getElementById('f1_5').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f1_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f1_6').value = '0'
    }
}

/*---------------------------PREGUNTA 2 ------------------------------*/

function seleccionar2_1()
{
    var valor1 = document.getElementById('f2_1').value;
    if(valor1 == "0")
    {
        document.getElementById('f2_1').src = "/matrix/images/medical/cenest/1-1.png";
        document.getElementById('f2_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f2_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f2_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f2_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f2_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f2_1').value = '1';
        document.getElementById('f2_2').value = '0';
        document.getElementById('f2_3').value = '0';
        document.getElementById('f2_4').value = '0';
        document.getElementById('f2_5').value = '0';
        document.getElementById('f2_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f2_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f2_1').value = '0'
    }
}

function seleccionar2_2()
{
    var valor1 = document.getElementById('f2_2').value;
    if(valor1 == "0")
    {
        document.getElementById('f2_2').src = "/matrix/images/medical/cenest/2-1.png";
        document.getElementById('f2_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f2_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f2_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f2_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f2_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f2_2').value = '1';
        document.getElementById('f2_1').value = '0';
        document.getElementById('f2_3').value = '0';
        document.getElementById('f2_4').value = '0';
        document.getElementById('f2_5').value = '0';
        document.getElementById('f2_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f2-2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f2_2').value = '0'
    }
}

function seleccionar2_3()
{
    var valor1 = document.getElementById('f2_3').value;
    if(valor1 == "0")
    {
        document.getElementById('f2_3').src = "/matrix/images/medical/cenest/3-1.png";
        document.getElementById('f2_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f2_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f2_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f2_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f2_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f2_3').value = '1';
        document.getElementById('f2_1').value = '0';
        document.getElementById('f2_2').value = '0';
        document.getElementById('f2_4').value = '0';
        document.getElementById('f2_5').value = '0';
        document.getElementById('f2_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f2_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f2_3').value = '0'
    }
}

function seleccionar2_4()
{
    var valor1 = document.getElementById('f2_4').value;
    if(valor1 == "0")
    {
        document.getElementById('f2_4').src = "/matrix/images/medical/cenest/4-1.png";
        document.getElementById('f2_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f2_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f2_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f2_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f2_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f2_4').value = '1';
        document.getElementById('f2_1').value = '0';
        document.getElementById('f2_2').value = '0';
        document.getElementById('f2_3').value = '0';
        document.getElementById('f2_5').value = '0';
        document.getElementById('f2_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f2_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f2_4').value = '0'
    }
}

function seleccionar2_5()
{
    var valor1 = document.getElementById('f2_5').value;
    if(valor1 == "0")
    {
        document.getElementById('f2_5').src = "/matrix/images/medical/cenest/5-1.png";
        document.getElementById('f2_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f2_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f2_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f2_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f2_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f2_5').value = '1';
        document.getElementById('f2_1').value = '0';
        document.getElementById('f2_2').value = '0';
        document.getElementById('f2_3').value = '0';
        document.getElementById('f2_4').value = '0';
        document.getElementById('f2_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f2_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f2_5').value = '0'
    }
}

function seleccionar2_6()
{
    var valor1 = document.getElementById('f2_6').value;
    if(valor1 == "0")
    {
        document.getElementById('f2_6').src = "/matrix/images/medical/cenest/6-1.png";
        document.getElementById('f2_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f2_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f2_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f2_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f2_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f2_6').value = '1';
        document.getElementById('f2_1').value = '0';
        document.getElementById('f2_2').value = '0';
        document.getElementById('f2_3').value = '0';
        document.getElementById('f2_4').value = '0';
        document.getElementById('f2_5').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f2_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f2_6').value = '0'
    }
}

/*---------------------------PREGUNTA 3 ------------------------------*/

function seleccionar3_1()
{
    var valor1 = document.getElementById('f3_1').value;
    if(valor1 == "0")
    {
        document.getElementById('f3_1').src = "/matrix/images/medical/cenest/1-1.png";
        document.getElementById('f3_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f3_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f3_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f3_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f3_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f3_1').value = '1';
        document.getElementById('f3_2').value = '0';
        document.getElementById('f3_3').value = '0';
        document.getElementById('f3_4').value = '0';
        document.getElementById('f3_5').value = '0';
        document.getElementById('f3_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f3_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f3_1').value = '0'
    }
}

function seleccionar3_2()
{
    var valor1 = document.getElementById('f3_2').value;
    if(valor1 == "0")
    {
        document.getElementById('f3_2').src = "/matrix/images/medical/cenest/3-1.png";
        document.getElementById('f3_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f3_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f3_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f3_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f3_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f3_2').value = '1';
        document.getElementById('f3_1').value = '0';
        document.getElementById('f3_3').value = '0';
        document.getElementById('f3_4').value = '0';
        document.getElementById('f3_5').value = '0';
        document.getElementById('f3_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f3_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f3_2').value = '0'
    }
}

function seleccionar3_3()
{
    var valor1 = document.getElementById('f3_3').value;
    if(valor1 == "0")
    {
        document.getElementById('f3_3').src = "/matrix/images/medical/cenest/3-1.png";
        document.getElementById('f3_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f3_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f3_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f3_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f3_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f3_3').value = '1';
        document.getElementById('f3_1').value = '0';
        document.getElementById('f3_2').value = '0';
        document.getElementById('f3_4').value = '0';
        document.getElementById('f3_5').value = '0';
        document.getElementById('f3_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f3_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f3_3').value = '0'
    }
}

function seleccionar3_4()
{
    var valor1 = document.getElementById('f3_4').value;
    if(valor1 == "0")
    {
        document.getElementById('f3_4').src = "/matrix/images/medical/cenest/4-1.png";
        document.getElementById('f3_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f3_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f3_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f3_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f3_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f3_4').value = '1';
        document.getElementById('f3_1').value = '0';
        document.getElementById('f3_2').value = '0';
        document.getElementById('f3_3').value = '0';
        document.getElementById('f3_5').value = '0';
        document.getElementById('f3_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f3_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f3_4').value = '0'
    }
}

function seleccionar3_5()
{
    var valor1 = document.getElementById('f3_5').value;
    if(valor1 == "0")
    {
        document.getElementById('f3_5').src = "/matrix/images/medical/cenest/5-1.png";
        document.getElementById('f3_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f3_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f3_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f3_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f3_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f3_5').value = '1';
        document.getElementById('f3_1').value = '0';
        document.getElementById('f3_2').value = '0';
        document.getElementById('f3_3').value = '0';
        document.getElementById('f3_4').value = '0';
        document.getElementById('f3_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f3_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f3_5').value = '0'
    }
}

function seleccionar3_6()
{
    var valor1 = document.getElementById('f3_6').value;
    if(valor1 == "0")
    {
        document.getElementById('f3_6').src = "/matrix/images/medical/cenest/6-1.png";
        document.getElementById('f3_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f3_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f3_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f3_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f3_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f3_6').value = '1';
        document.getElementById('f3_1').value = '0';
        document.getElementById('f3_2').value = '0';
        document.getElementById('f3_3').value = '0';
        document.getElementById('f3_4').value = '0';
        document.getElementById('f3_5').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f3_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f3_6').value = '0'
    }
}

/*---------------------------PREGUNTA 4 ------------------------------*/

function seleccionar4_1()
{
    var valor1 = document.getElementById('f4_1').value;
    if(valor1 == "0")
    {
        document.getElementById('f4_1').src = "/matrix/images/medical/cenest/1-1.png";
        document.getElementById('f4_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f4_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f4_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f4_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f4_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f4_1').value = '1';
        document.getElementById('f4_2').value = '0';
        document.getElementById('f4_3').value = '0';
        document.getElementById('f4_4').value = '0';
        document.getElementById('f4_5').value = '0';
        document.getElementById('f4_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f4_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f4_1').value = '0'
    }
}

function seleccionar4_2()
{
    var valor1 = document.getElementById('f4_2').value;
    if(valor1 == "0")
    {
        document.getElementById('f4_2').src = "/matrix/images/medical/cenest/2-1.png";
        document.getElementById('f4_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f4_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f4_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f4_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f4_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f4_2').value = '1';
        document.getElementById('f4_1').value = '0';
        document.getElementById('f4_3').value = '0';
        document.getElementById('f4_4').value = '0';
        document.getElementById('f4_5').value = '0';
        document.getElementById('f4_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f4_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f4_2').value = '0'
    }
}

function seleccionar4_3()
{
    var valor1 = document.getElementById('f4_3').value;
    if(valor1 == "0")
    {
        document.getElementById('f4_3').src = "/matrix/images/medical/cenest/3-1.png";
        document.getElementById('f4_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f4_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f4_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f4_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f4_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f4_3').value = '1';
        document.getElementById('f4_1').value = '0';
        document.getElementById('f4_2').value = '0';
        document.getElementById('f4_4').value = '0';
        document.getElementById('f4_5').value = '0';
        document.getElementById('f4_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f4_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f4_3').value = '0'
    }
}

function seleccionar4_4()
{
    var valor1 = document.getElementById('f4_4').value;
    if(valor1 == "0")
    {
        document.getElementById('f4_4').src = "/matrix/images/medical/cenest/4-1.png";
        document.getElementById('f4_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f4_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f4_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f4_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f4_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f4_4').value = '1';
        document.getElementById('f4_1').value = '0';
        document.getElementById('f4_2').value = '0';
        document.getElementById('f4_3').value = '0';
        document.getElementById('f4_5').value = '0';
        document.getElementById('f4_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f4_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f4_4').value = '0'
    }
}

function seleccionar4_5()
{
    var valor1 = document.getElementById('f4_5').value;
    if(valor1 == "0")
    {
        document.getElementById('f4_5').src = "/matrix/images/medical/cenest/5-1.png";
        document.getElementById('f4_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f4_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f4_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f4_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f4_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f4_5').value = '1';
        document.getElementById('f4_1').value = '0';
        document.getElementById('f4_2').value = '0';
        document.getElementById('f4_3').value = '0';
        document.getElementById('f4_4').value = '0';
        document.getElementById('f4_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f4_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f4_5').value = '0'
    }
}

function seleccionar4_6()
{
    var valor1 = document.getElementById('f4_6').value;
    if(valor1 == "0")
    {
        document.getElementById('f4_6').src = "/matrix/images/medical/cenest/6-1.png";
        document.getElementById('f4_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f4_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f4_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f4_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f4_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f4_6').value = '1';
        document.getElementById('f4_1').value = '0';
        document.getElementById('f4_2').value = '0';
        document.getElementById('f4_3').value = '0';
        document.getElementById('f4_4').value = '0';
        document.getElementById('f4_5').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f4_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f4_6').value = '0'
    }
}

/*---------------------------PREGUNTA 5 ------------------------------*/

function seleccionar5_1()
{
    var valor1 = document.getElementById('f5_1').value;
    if(valor1 == "0")
    {
        document.getElementById('f5_1').src = "/matrix/images/medical/cenest/1-1.png";
        document.getElementById('f5_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f5_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f5_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f5_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f5_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f5_1').value = '1';
        document.getElementById('f5_2').value = '0';
        document.getElementById('f5_3').value = '0';
        document.getElementById('f5_4').value = '0';
        document.getElementById('f5_5').value = '0';
        document.getElementById('f5_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f5_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f5_1').value = '0'
    }
}

function seleccionar5_2()
{
    var valor1 = document.getElementById('f5_2').value;
    if(valor1 == "0")
    {
        document.getElementById('f5_2').src = "/matrix/images/medical/cenest/2-1.png";
        document.getElementById('f5_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f5_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f5_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f5_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f5_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f5_2').value = '1';
        document.getElementById('f5_1').value = '0';
        document.getElementById('f5_3').value = '0';
        document.getElementById('f5_4').value = '0';
        document.getElementById('f5_5').value = '0';
        document.getElementById('f5_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f5_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f5_2').value = '0'
    }
}

function seleccionar5_3()
{
    var valor1 = document.getElementById('f5_3').value;
    if(valor1 == "0")
    {
        document.getElementById('f5_3').src = "/matrix/images/medical/cenest/3-1.png";
        document.getElementById('f5_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f5_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f5_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f5_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f5_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f5_3').value = '1';
        document.getElementById('f5_1').value = '0';
        document.getElementById('f5_2').value = '0';
        document.getElementById('f5_4').value = '0';
        document.getElementById('f5_5').value = '0';
        document.getElementById('f5_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f5_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f5_3').value = '0'
    }
}

function seleccionar5_4()
{
    var valor1 = document.getElementById('f5_4').value;
    if(valor1 == "0")
    {
        document.getElementById('f5_4').src = "/matrix/images/medical/cenest/4-1.png";
        document.getElementById('f5_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f5_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f5_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f5_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f5_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f5_4').value = '1';
        document.getElementById('f5_1').value = '0';
        document.getElementById('f5_2').value = '0';
        document.getElementById('f5_3').value = '0';
        document.getElementById('f5_5').value = '0';
        document.getElementById('f5_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f5_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f5_4').value = '0'
    }
}

function seleccionar5_5()
{
    var valor1 = document.getElementById('f5_5').value;
    if(valor1 == "0")
    {
        document.getElementById('f5_5').src = "/matrix/images/medical/cenest/5-1.png";
        document.getElementById('f5_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f5_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f5_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f5_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f5_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f5_5').value = '1';
        document.getElementById('f5_1').value = '0';
        document.getElementById('f5_2').value = '0';
        document.getElementById('f5_3').value = '0';
        document.getElementById('f5_4').value = '0';
        document.getElementById('f5_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f5_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f5_5').value = '0'
    }
}

function seleccionar5_6()
{
    var valor1 = document.getElementById('f5_6').value;
    if(valor1 == "0")
    {
        document.getElementById('f5_6').src = "/matrix/images/medical/cenest/6-1.png";
        document.getElementById('f5_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f5_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f5_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f5_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f5_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f5_6').value = '1';
        document.getElementById('f5_1').value = '0';
        document.getElementById('f5_2').value = '0';
        document.getElementById('f5_3').value = '0';
        document.getElementById('f5_4').value = '0';
        document.getElementById('f5_5').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f5_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f5_6').value = '0'
    }
}

/*---------------------------PREGUNTA 6 ------------------------------*/

function seleccionar6_1()
{
    var valor1 = document.getElementById('f6_1').value;
    if(valor1 == "0")
    {
        document.getElementById('f6_1').src = "/matrix/images/medical/cenest/1-1.png";
        document.getElementById('f6_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f6_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f6_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f6_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f6_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f6_1').value = '1';
        document.getElementById('f6_2').value = '0';
        document.getElementById('f6_3').value = '0';
        document.getElementById('f6_4').value = '0';
        document.getElementById('f6_5').value = '0';
        document.getElementById('f6_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f6_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f6_1').value = '0'
    }
}

function seleccionar6_2()
{
    var valor1 = document.getElementById('f6_2').value;
    if(valor1 == "0")
    {
        document.getElementById('f6_2').src = "/matrix/images/medical/cenest/2-1.png";
        document.getElementById('f6_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f6_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f6_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f6_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f6_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f6_2').value = '1';
        document.getElementById('f6_1').value = '0';
        document.getElementById('f6_3').value = '0';
        document.getElementById('f6_4').value = '0';
        document.getElementById('f6_5').value = '0';
        document.getElementById('f6_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f6_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f6_2').value = '0'
    }
}

function seleccionar6_3()
{
    var valor1 = document.getElementById('f6_3').value;
    if(valor1 == "0")
    {
        document.getElementById('f6_3').src = "/matrix/images/medical/cenest/3-1.png";
        document.getElementById('f6_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f6_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f6_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f6_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f6_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f6_3').value = '1';
        document.getElementById('f6_1').value = '0';
        document.getElementById('f6_2').value = '0';
        document.getElementById('f6_4').value = '0';
        document.getElementById('f6_5').value = '0';
        document.getElementById('f6_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f6_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f6_3').value = '0'
    }
}

function seleccionar6_4()
{
    var valor1 = document.getElementById('f6_4').value;
    if(valor1 == "0")
    {
        document.getElementById('f6_4').src = "/matrix/images/medical/cenest/4-1.png";
        document.getElementById('f6_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f6_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f6_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f6_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f6_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f6_4').value = '1';
        document.getElementById('f6_1').value = '0';
        document.getElementById('f6_2').value = '0';
        document.getElementById('f6_3').value = '0';
        document.getElementById('f6_5').value = '0';
        document.getElementById('f6_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f6_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f6_4').value = '0'
    }
}

function seleccionar6_5()
{
    var valor1 = document.getElementById('f6_5').value;
    if(valor1 == "0")
    {
        document.getElementById('f6_5').src = "/matrix/images/medical/cenest/5-1.png";
        document.getElementById('f6_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f6_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f6_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f6_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f6_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f6_5').value = '1';
        document.getElementById('f6_1').value = '0';
        document.getElementById('f6_2').value = '0';
        document.getElementById('f6_3').value = '0';
        document.getElementById('f6_4').value = '0';
        document.getElementById('f6_6').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f6_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f6_5').value = '0'
    }
}

function seleccionar6_6()
{
    var valor1 = document.getElementById('f6_6').value;
    if(valor1 == "0")
    {
        document.getElementById('f6_6').src = "/matrix/images/medical/cenest/6-1.png";
        document.getElementById('f6_1').src = "/matrix/images/medical/cenest/1.png";
        document.getElementById('f6_2').src = "/matrix/images/medical/cenest/2.png";
        document.getElementById('f6_3').src = "/matrix/images/medical/cenest/3.png";
        document.getElementById('f6_4').src = "/matrix/images/medical/cenest/4.png";
        document.getElementById('f6_5').src = "/matrix/images/medical/cenest/5.png";
        document.getElementById('f6_6').value = '1';
        document.getElementById('f6_1').value = '0';
        document.getElementById('f6_2').value = '0';
        document.getElementById('f6_3').value = '0';
        document.getElementById('f6_4').value = '0';
        document.getElementById('f6_5').value = '0';
    }
    if(valor1 == "1")
    {
        document.getElementById('f6_6').src = "/matrix/images/medical/cenest/6.png";
        document.getElementById('f6_6').value = '0'
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
        var formulario = document.encuestaform.centro_Costos;
        var pregunta1 = parseInt(document.getElementById('f1_1').value)+parseInt(document.getElementById('f1_2').value)+parseInt(document.getElementById('f1_3').value)+parseInt(document.getElementById('f1_4').value)+parseInt(document.getElementById('f1_5').value)+parseInt(document.getElementById('f1_6').value);
        var pregunta2 = parseInt(document.getElementById('f2_1').value)+parseInt(document.getElementById('f2_2').value)+parseInt(document.getElementById('f2_3').value)+parseInt(document.getElementById('f2_4').value)+parseInt(document.getElementById('f2_5').value)+parseInt(document.getElementById('f2_6').value);
        var pregunta3 = parseInt(document.getElementById('f3_1').value)+parseInt(document.getElementById('f3_2').value)+parseInt(document.getElementById('f3_3').value)+parseInt(document.getElementById('f3_4').value)+parseInt(document.getElementById('f3_5').value)+parseInt(document.getElementById('f3_6').value);
        var pregunta4 = parseInt(document.getElementById('f4_1').value)+parseInt(document.getElementById('f4_2').value)+parseInt(document.getElementById('f4_3').value)+parseInt(document.getElementById('f4_4').value)+parseInt(document.getElementById('f4_5').value)+parseInt(document.getElementById('f4_6').value);
        var pregunta5 = parseInt(document.getElementById('f5_1').value)+parseInt(document.getElementById('f5_2').value)+parseInt(document.getElementById('f5_3').value)+parseInt(document.getElementById('f5_4').value)+parseInt(document.getElementById('f5_5').value)+parseInt(document.getElementById('f5_6').value);
        var pregunta6 = parseInt(document.getElementById('f6_1').value)+parseInt(document.getElementById('f6_2').value)+parseInt(document.getElementById('f6_3').value)+parseInt(document.getElementById('f6_4').value)+parseInt(document.getElementById('f6_5').value)+parseInt(document.getElementById('f6_6').value);
        var total = pregunta1+pregunta2+pregunta3+pregunta4+pregunta5+pregunta6;

        if (formulario.value == null || formulario.value.length == 0 || /^\s*$/.test(formulario.value))
        {
            window.alert(formulario.name+ ' no puede estar vacio');
            formulario.focus();
        }
        if (total < 6)
        {
            window.alert('Todas las preguntas deben ser contestadas');
            formulario.focus();
        }
        else
        {
            miPopup = window.open("encuestace_guardar.php?f1-1="+f1_1.value+"&f1-2="+f1_2.value+"&f1-3="+f1_3.value+"&f1-4="+f1_4.value+"&f1-5="+f1_5.value+
                "&f2-1="+f2_1.value+"&f2-2="+f2_2.value+"&f2-3="+f2_3.value+"&f2-4="+f2_4.value+"&f2-5="+f2_5.value+
                "&f3-1="+f3_1.value+"&f3-2="+f3_2.value+"&f3-3="+f3_3.value+"&f3-4="+f3_4.value+"&f3-5="+f3_5.value+
                "&f4-1="+f4_1.value+"&f4-2="+f4_2.value+"&f4-3="+f4_3.value+"&f4-4="+f4_4.value+"&f4-5="+f4_5.value+
                "&f5-1="+f5_1.value+"&f5-2="+f5_2.value+"&f5-3="+f5_3.value+"&f5-4="+f5_4.value+"&f5-5="+f5_5.value+
                "&f6-1="+f6_1.value+"&f6-2="+f6_2.value+"&f6-3="+f6_3.value+"&f6-4="+f6_4.value+"&f6-5="+f6_5.value+
                "&cc="+centro_Costos.value+"&sugest="+sugest.value,"miwin","width=500,height=150");
            miPopup.focus();

            document.encuestaform.submit();
        }
    }
}

function verInformeB(parametro1,parametro2,valor,pregunta)
{
    miPopup = window.open("informeCenest.php?parametro1="+parametro1.value+"&parametro2="+parametro2.value+"&valor="+valor.value+"&pregunta="+pregunta.value, "Google","status=1,toolbar=1");
    miPopup.focus()
}