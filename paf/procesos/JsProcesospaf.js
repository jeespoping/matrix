/**
 * Created by Will on 08/06/2016.
 */
function servicio(divServicio,divEspecialista,divBloque){
    document.getElementById(divServicio).style.display = 'block';
    document.getElementById(divEspecialista).style.display = 'none';
    document.getElementById(divBloque).style.display = 'none';
}

function especialista(divServicio,divEspecialista,divBloque){
    document.getElementById(divServicio).style.display = 'none';
    document.getElementById(divEspecialista).style.display = 'block';
    document.getElementById(divBloque).style.display = 'none';
}

function contartotal(){
    document.forms.fservicio.t.value = parseInt(document.forms.fservicio.c1.value) + parseInt(document.forms.fservicio.c2.value)+
                                       parseInt(document.forms.fservicio.c3.value) + parseInt(document.forms.fservicio.c4.value)+
                                       parseInt(document.forms.fservicio.c5.value) + parseInt(document.forms.fservicio.c6.value)+
                                       parseInt(document.forms.fservicio.c7.value) + parseInt(document.forms.fservicio.c8.value)+
                                       parseInt(document.forms.fservicio.c9.value) + parseInt(document.forms.fservicio.c10.value)+
                                       parseInt(document.forms.fservicio.c11.value)+ parseInt(document.forms.fservicio.c12.value);
}

function porcentajeCI(){
    document.forms.fservicio.p1.value = (parseInt(document.forms.fservicio.c1.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p2.value = (parseInt(document.forms.fservicio.c2.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p3.value = (parseInt(document.forms.fservicio.c3.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p4.value = (parseInt(document.forms.fservicio.c4.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p5.value = (parseInt(document.forms.fservicio.c5.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p6.value = (parseInt(document.forms.fservicio.c6.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p7.value = (parseInt(document.forms.fservicio.c7.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p8.value = (parseInt(document.forms.fservicio.c8.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p9.value = (parseInt(document.forms.fservicio.c9.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p10.value = (parseInt(document.forms.fservicio.c10.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p11.value = (parseInt(document.forms.fservicio.c11.value) / parseInt(document.forms.fservicio.t.value)) * 100;
    document.forms.fservicio.p12.value = (parseInt(document.forms.fservicio.c12.value) / parseInt(document.forms.fservicio.t.value)) * 100;
}

function medicoxprofesion(profesion)
{
    miPopup = window.open("medicos.php?profesion="+profesion.value,"miwin","width=800,height=450,scrollbars=yes");
    miPopup.focus()
}

function copiarValor(nombre){
    opener.document.loginform.nombre_asisten.value = nombre;
    window.close();
}

function validarCampos()
{
    window.onload = function ()
    {
        document.loginform.addEventListener('submit', validarFormulario);
    }

    function validarFormulario(evObject)
    {
        evObject.preventDefault();
        var formulario = document.loginform.servicio;
        var formulario2 = document.loginform.nombre_asisten;

        if (formulario.value == null || formulario.value.length == 0 || /^\s*$/.test(formulario.value))
        {
            alert (formulario.name+ ' no puede estar vacio');
            formulario.focus();
        }
        else if (formulario2.value == null || formulario2.value.length == 0 || /^\s*$/.test(formulario2.value))
        {
            alert (formulario2.name+ 'cial no puede estar vacio');
            formulario2.focus();
        }
        else
        {
            document.loginform.submit();
        }
    }
}

function verInforme(campos,parametros,servicio,mes)
{
    miPopup = window.open("informe.php?campo="+campos.value+"&parametro="+parametros.value+"&servicio="+servicio.value+"&mes="+mes.value, "Google","status=1,toolbar=1");
    miPopup.focus()
}

function foco()
{
    $(window).ready(function(){
        $("body").animate({ scrollTop: $(document).height()}, 850);
    });
}

/*////////////////////////////////////////////////////////////////////////////////////////*/

function totalPac(npn,nps,npst,totalPac)
{
    var valor1 = document.getElementById('npn').value;
    var valor2 = document.getElementById('nps').value;
    var valor3 = document.getElementById('npst').value;

    document.getElementById('totalPac').value = parseInt(valor1) + parseInt(valor2) + parseInt(valor3);
}

function totalFem(sfn,sfs,sfst,totalFem)
{
    var valor1 = document.getElementById('sfn').value;
    var valor2 = document.getElementById('sfs').value;
    var valor3 = document.getElementById('sfst').value;

    document.getElementById('totalFem').value = parseInt(valor1) + parseInt(valor2) + parseInt(valor3);
}

function totalMas(smn,sms,smst,totalMas)
{
    var valor1 = document.getElementById('smn').value;
    var valor2 = document.getElementById('sms').value;
    var valor3 = document.getElementById('smst').value;

    document.getElementById('totalMas').value = parseInt(valor1) + parseInt(valor2) + parseInt(valor3);
}

function totalEdad(en,es,est,totalEdad)
{
    var valor1 = document.getElementById('en').value;
    var valor2 = document.getElementById('es').value;
    var valor3 = document.getElementById('est').value;

    var total = (parseFloat(valor1) + parseFloat(valor2) + parseFloat(valor3))/3;

    document.getElementById('totalEdad').value = total.toFixed(1);
}

function edadMayor(emn,ems,emst,edadMayor)
{
    var valor1 = document.getElementById('emn').value;
    var valor2 = document.getElementById('ems').value;
    var valor3 = document.getElementById('emst').value;

    if(valor1 > valor2)
    {
        if(valor1 > valor3)
        {
            document.getElementById('edadMayor').value = valor1;
        }
        else
        {
            document.getElementById('edadMayor').value = valor3;
        }
    }
    else
    {
        if(valor2 > valor3)
        {
            document.getElementById('edadMayor').value = valor2;
        }
        else
        {
            document.getElementById('edadMayor').value = valor3;
        }
    }
}

function edadMenor(eminn,emins,eminst,edadMenor)
{
    var valor1 = document.getElementById(eminn).value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById(emins).value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById(eminst).value; if(valor3 == ''){valor3 = 0}

    if(valor1 < valor2)
    {
        if(valor1 < valor3)
        {
            document.getElementById(edadMenor).value = valor1;
        }
        else
        {
            document.getElementById(edadMenor).value = valor3;
        }
    }
    else
    {
        if(valor2 < valor3)
        {
            document.getElementById(edadMenor).value = valor2;
        }
        else
        {
            document.getElementById(edadMenor).value = valor3;
        }
    }
}

function totaldiasest(depn,deps,depst,diasEst)
{
    var valor1 = document.getElementById('depn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('deps').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('depst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('diasEst').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalPromEst(diasEst,totalPac,tpromEstancia)
{
    var valor1 = document.getElementById('diasEst').value;
    var valor2 = document.getElementById('totalPac').value;
    total = parseFloat(valor1)/parseFloat(valor2);

    document.getElementById('tpromEstancia').value = total.toFixed(2);
}

function totalEstuci(sumEstn,sumEsts,sumEstst,tsumEst)
{
    var valor1 = document.getElementById('sumEstn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('sumEsts').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('sumEstst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tsumEst').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function tMeu(mayEsun,mayEsus,mayEsust,tmayEst)
{
    var valor1 = document.getElementById('mayEsun').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('mayEsus').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('mayEsust').value; if(valor3 == ''){valor3 = 0}

    if(valor1 > valor2)
    {
        if(valor1 < valor3)
        {
            document.getElementById('tmayEst').value = valor1;
        }
        else
        {
            document.getElementById('tmayEst').value = valor3;
        }
    }
    else
    {
        if(valor2 < valor3)
        {
            document.getElementById('tmayEst').value = valor2;
        }
        else
        {
            document.getElementById('tmayEst').value = valor3;
        }
    }
}

function tMreu(menEsun,menEss,menEsst,tmenEst)
{
    var valor1 = document.getElementById('menEsun').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('menEss').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('menEsst').value; if(valor3 == ''){valor3 = 0}

    if(valor1 < valor2)
    {
        if(valor1 < valor3)
        {
            document.getElementById('tmenEst').value = valor1;
        }
        else
        {
            document.getElementById('tmenEst').value = valor3;
        }
    }
    else
    {
        if(valor2 < valor3)
        {
            document.getElementById('tmenEst').value = valor2;
        }
        else
        {
            document.getElementById('tmenEst').value = valor3;
        }
    }
}

function totalDep(dEpn,dEps,dEpst,tdEp)
{
    var valor1 = document.getElementById('dEpn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('dEps').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('dEpst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tdEp').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalPro(nPron,nPros,nProst,tPro)
{
    var valor1 = document.getElementById('nPron').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('nPros').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('nProst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tPro').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalPpro(tPro,totalPac,tPpro)
{
    var valor1 = document.getElementById('tPro').value;
    var valor2 = document.getElementById('totalPac').value;

    var total = parseFloat(valor1)/parseFloat(valor2);

    document.getElementById('tPpro').value = total.toFixed(1);
}

function totalRei(nRein,nReis,nReist,tRei)
{
    var valor1 = document.getElementById('nRein').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('nReis').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('nReist').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tRei').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalpAmb(pAmbn,pAmbs,pAmbst,tpAmb)
{
    var valor1 = document.getElementById('pAmbn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('pAmbs').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('pAmbst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tpAmb').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalNopro(noPron,noPros,noProst,tnoPro)
{
    var valor1 = document.getElementById('noPron').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('noPros').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('noProst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tnoPro').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalP1cx(p1cxn,p1cxs,p1cxst,tp1cx)
{
    var valor1 = document.getElementById('p1cxn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('p1cxs').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('p1cxst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tp1cx').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalP2cx(p2cxn,p2cxs,p2cxst,tp2cx)
{
    var valor1 = document.getElementById('p2cxn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('p2cxs').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('p2cxst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tp2cx').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalP3cx(p3cxn,p3cxs,p3cxst,tp3cx)
{
    var valor1 = document.getElementById('p3cxn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('p3cxs').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('p3cxst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tp3cx').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalcxhem(cxhemn,cxhems,cxhemst,tcxhem)
{
    var valor1 = document.getElementById('cxhemn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('cxhems').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('cxhemst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tcxhem').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalcxef(cxefn,cxefs,cxefst,tcxef)
{
    var valor1 = document.getElementById('cxefn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('cxefs').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('cxefst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tcxef').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function tcxheef(cxhemefn,cxhemefs,cxhemefst,tcxhemef)
{
    var valor1 = document.getElementById('cxhemefn').value; if(valor1 == ''){valor1 = 0}
    var valor2 = document.getElementById('cxhemefs').value; if(valor2 == ''){valor2 = 0}
    var valor3 = document.getElementById('cxhemefst').value; if(valor3 == ''){valor3 = 0}

    document.getElementById('tcxhemef').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function totalesUci(tsumEst,tpAmb,tesUci)
{
    var valor1 = document.getElementById('tsumEst').value;
    var valor2 = document.getElementById('tpAmb').value;

    var total = parseFloat(valor1)/parseFloat(valor2);

    document.getElementById('tesUci').value = total.toFixed(1);
}

function totesUcinop(tsumEst,tnoPro,tesUciNop)
{
    var valor1 = document.getElementById('tsumEst').value;
    var valor2 = document.getElementById('tnoPro').value;

    var total = parseFloat(valor1)/parseFloat(valor2);

    document.getElementById('tesUciNop').value = total.toFixed(1);
}

function totalEsUciPaf(tsumEst,totalPac,toEsuci)
{
    var valor1 = document.getElementById('tsumEst').value;
    var valor2 = document.getElementById('totalPac').value;

    var total = parseFloat(valor1)/parseFloat(valor2);

    document.getElementById('toEsuci').value = total.toFixed(1);
}

function totalEspre(tPro,tRei,toEspre)
{
    var valor1 = document.getElementById('tPro').value;
    var valor2 = document.getElementById('tRei').value;

    var total = parseFloat(valor1)/parseFloat(valor2);

    document.getElementById('toEspre').value = total.toFixed(1);
}

function totalEstHos(detn,dets,detst,diasEstT)
{
    var valor1 = document.getElementById('detn').value;
    var valor2 = document.getElementById('dets').value;
    var valor3 = document.getElementById('detst').value;

    document.getElementById('diasEstT').value = parseInt(valor1)+parseInt(valor2)+parseInt(valor3);
}

function mostrarop1(fechaini,historia,contenido)
{
    document.getElementById(fechaini).style.display = 'block';
    document.getElementById(historia).style.display = 'none';
    document.getElementById(contenido).style.marginTop = '5px';
}

function mostrarop2(historia,fechaini,contenido)
{
    document.getElementById(fechaini).style.display = 'none';
    document.getElementById(historia).style.display = 'block';
    document.getElementById(contenido).style.marginTop = '5px';
}