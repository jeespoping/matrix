
    <?php 
        
            $nroOrden = $_GET['nroOrden'];
            $tOrden = $_GET['tOrden'];
            $wemp_pmla = $_GET['wemp_pmla'];
            $item = $_GET['item'];
    
    ?>

<!DOCTYPE html>



<html>



<head>



    <META HTTP-EQUIV="Pragma" CONTENT="no-cache" />
    <meta http-equiv=”Content-Type” content=”text/html; charset=ISO-8859-1″ />


    <meta charset="UTF-8" />



    <title>



    Resultados en L&iacute;nea - Laboratorio M&eacute;dico Las Am&eacute;ricas



    </title>




    <style type="text/css">



    a.linkstabla3 {



        color: black;



        font-size: 10px;



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



        text-decoration: none;



    }







    a.linkstabla4 {



        color: White;



        font-weight: bold;



        font-size: 18px;



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



        text-decoration: none;



    }







    a.linkstabla5 {



        color: black;



        font-weight: bold;



        font-size: 11px;



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



        text-decoration: none;



    }







    a.linkstabla6 {



        color: black;



        font-weight: normal;



        font-size: 11px;



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



        text-decoration: none;



    }







    a.linkstabla7 {



        color: black;



        font-weight: normal;



        font-size: 12px;



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



        text-decoration: none;



    }







    a.navegacion {



        color: white;



        font-style: normal;



        font-weight: normal;



        font-size: 10px;



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



        text-decoration: underline;



    }







    a.navegacion1 {



        color: white;



        font-style: normal;



        font-weight: normal;



        font-size: 10px;



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



        text-decoration: none;



    }







    h1 {



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



    }







    .historicButtons {



        display: flex;



        flex-direction: column;



    }







    .loadFields {



        margin: 10px 0 10px 0;



        font-size: 24px;



        background-color: #0260b1;



        color: white;



        border-radius: 4px;



    }







    .fieldsDropDown {



        font-size: 18px;



        margin: 18px 0 18px 0;



    }







    #loading {



        font-family: Arial, Helvetica, Geneva, Swiss, SunSans-Regular;



        font-size: 18px;



        color: black;



    }







    #chartdiv {



        width: auto;



        height: 500px;



    }







    #closeChart {



        margin: 10px;



        font-size: 16px;



    }



    .radioGroup {



        display: flex;



        justify-content: space-evenly;



        color: black;



    }



    #datesDiv {



        color: black;



        display: flex;



        justify-content: space-around;



        font-size: 14px;



        font-weight: bold;



    }







    #closeChartDiv {



        display: flex;



        justify-content: center;



    }







    label {



        vertical-align: middle;



        font-size: 14px;



        border-radius: 4px;



        margin-bottom: 0px !important;



    }







    #fechaInicial,



    #fechaFinal {



        vertical-align: middle;



        font-size: 14px;



        border-bottom: 2px solid;



        border-top: none;



        border-left: none;



        border-right: none;



    }







    .titulo {



        background-color: #00acc9;



        color: whitesmoke;



        text-align: center;



    }







    .dl_row {



        display: -ms-flexbox;



        display: flex;



        -ms-flex-wrap: wrap;



        flex-wrap: wrap;



        font-size: 14px;



        margin-top: 1rem !important;



    }



    </style>



</head>







<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0">



<input type='hidden' id='nroOrden' name='nroOrden' value='<?php echo $nroOrden ?>'>
<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='<?php echo $wemp_pmla ?>'>
<input type='hidden' id='tOrden' name='tOrden' value='<?php echo $tOrden ?>'>
<input type='hidden' id='item' name='item' value='<?php echo $item ?>'>
    <link



    rel="stylesheet"



    href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"



    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"



    crossorigin="anonymous"



    />



    <link



    rel="stylesheet"



    href="https://www.amcharts.com/lib/3/plugins/export/export.css"



    type="text/css"



    media="all"



    />



    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>



    <script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>





<script>

    $(document).ready(function(){
    $.ajax({
            url: "interoperabilidad_ws.php",
            type: "GET",
            dataType: "json",
            data:{
                accion			: 'consultarOrden',
                wemp_pmla		: $('#wemp_pmla').val(),
                nroOrden		: $('#nroOrden').val(),
                tOrden		: $('#tOrden').val(),
                item		: $('#item').val(),
            
                },
                async: false,
                success:function(res) {
                    console.log(res.paciente.historia);
                    //paciente
                    $("#nroHistoria").html(res.paciente.historia);
                    $("#nombreCompleto").html(res.paciente.nombreCompleto);
                    $("#nroDocumento").html(res.paciente.nroDocumento);
                    $("#fechaNacimiento").html(res.paciente.fechaNacimiento);
                    var genero = res.paciente.genero = "M"? "MASCULINO": "FEMENINO";
                    $("#genero").html(genero);
                    $("#nombreResponsable").html(res.paciente.nombreResponsable);
                    
                    //medico
                    var nomMedico = res.medico.nombre1+" "+res.medico.nombre2+" "+ res.medico.apellido1+" "+res.medico.apellido2;
                    $("#nombreMedico").html(nomMedico);
                    
                    //resultado
                    $("#fechaOrden").html(res.resultado.fechaOrden);
                    $("#fechaRes").html(res.resultado.fechaRes);
                    $("#nomDescripcion").html(res.resultado.info.examen.descripcion);
                    var html = '';
                    if(!$.isArray(res.resultado.info.detallesResultado)){
                    var element = res.resultado.info.detallesResultado;
                    html += '<tr>'+

                                    '<td width="5"></td>'+
                                    '<td width="130" colspan="2">'+
                                        '<a class="linkstabla6">'+	 element.descripcion +'</a>'+
                                    '</td>'+
                                    '<td width="100" colspan="2">';
                                        if(element.resultadoValor!=""){
                                            html +='<a class="linkstabla6">'+ element.resultadoValor +'</a>';
                                        }else{
                                            html +='<a class="linkstabla6">'+ element.resultadoTexto +'</a>';
                                            
                                        }
                                        
                                    html += '</td>'+
                                    '<td width="80" colspan="2">'+
                                        '<a class="linkstabla6">'+element.valorReferencia+'</a>'+
                                    '</td>'+
                                    '<td width="80" colspan="2">'+
                                        '<a class="linkstabla6">'+element.unidades+'</a>'+
                                    '</td>'+

                            '</tr>';
                            $("#tabla").append(html);
                    }else{
                            $.each( res.resultado.info.detallesResultado , (key,element) => { 
                        
                            html += '<tr>'+
                                    '<td width="5"></td>'+
                                    '<td width="130" colspan="2">'+                        
                                        '<a class="linkstabla6">'+	 element.descripcion +'</a>'+                                    
                                    '</td>'+
                                    '<td width="100" colspan="2">';
                                        if(element.resultadoValor!=""){
                                            html +='<a class="linkstabla6">'+ element.resultadoValor +'</a>';
                                        }else{
                                            html +='<a class="linkstabla6">'+ element.resultadoTexto +'</a>';
                                            
                                        }                                        
                                    html += '</td>'+
                                    '<td width="80" colspan="2">'+
                                        '<a class="linkstabla6">'+element.valorReferencia+'</a>'+
                                    '</td>'+
                                    '<td width="80" colspan="2">'+
                                        '<a class="linkstabla6">'+element.unidades+'</a>'+
                                    '</td>'+

                            '</tr>'
                                });
                            $("#tabla").append(html);
                            
                        }
                        //responsable
                        var contentResponsable = '<dt class="col-sm-2 table-sm">Responsable: </dt>';;
                        contentResponsable += '<table class="table table-borderless table-sm">'+
                                                            '<td text-align="center">'+

                                                                '<dl class="dl_row">'+
                                                                    '<dt class="col-sm-12" style="margin-right:-40px !important;">'+ res.resultado.info.examen.responsable.nombre +'</dt>'+
                                                                    '<dt class="col-sm-12" style="margin-right:-40px !important;">'+ "REG. "+res.resultado.info.examen.responsable.registroMedico +'</dt>'+
                                                                        

                                                                '</dl>'+
                                                                
                                                            '</td>'+
                                                        '</table>';
                            $("#responsable").append(contentResponsable);                             
                        if("microorganismos" in res.resultado.info &&  res.resultado.info.microorganismos != ""){
                            var contentMicroorganismo = '';
                            var contentAntibioticos = '';
                            var contentNotas = '';
                            //TABLA DE Microorganismo
                            if(!$.isArray(res.resultado.info.microorganismos.MicroorganismoDTO)){
                                contentMicroorganismo += '<table class="table table-borderless table-sm">'+
                                                '<td text-align="center">'+

                                                    '<dl class="dl_row">'+
                                                        '<dt class="col-sm-2">Microorganismo: </dt>'+
                                                            '<dd id="NumOT" class="col-sm-10" >'+
                                                            res.resultado.info.microorganismos.MicroorganismoDTO.descripcion+
                                                            '</dd>'+
                                                            
                                                        '<dt class="col-sm-2">Cantidad:</dt>'+
                                                            '<dd class="col-sm-10" >'+
                                                                res.resultado.info.microorganismos.MicroorganismoDTO.cantidadTotal+
                                                            '</dd>'+
                                                        '<dt class="col-sm-2">Antibiograma</dt>'+
                                                    '</dl>'+
                                                '</td>'+
                                            '</table>';
                                        
                                //TABLA DE ANTIBIOTICOS
                                if(!$.isArray(res.resultado.info.microorganismos.MicroorganismoDTO.antibioticos.AntibioticoDTO)){
                                    var element = res.resultado.info.microorganismos.MicroorganismoDTO.antibioticos.AntibioticoDTO;
                                        '<table class="table table-striped"  style="width: 50%; margin-left: 5%;">'+
                                                    '<tr>'+
                                                    '<td width="5"></td>'+
                                                        '<td width="130" colspan="2">'+
                                                            '<div align="left"><a class="linkstabla5">ANTIBIOTICOS</a></div>'+

                                                    '</td>'+
                                                        '<td width="100" colspan="2">'+
                                                            '<div align="left"><a class="linkstabla5">SENSIBILIDAD </a></div>'+
                                                        '</td>'+
                                                        '<td width="80" colspan="2">'+
                                                                '<div align="left"><a class="linkstabla5">MIC </a></div>'+
                                                    ' </td>'+                                                                                
                                                    '</tr>'+
                                                    '<tr>'+

                                                        '<td width="5"></td>'+
                                                        '<td width="130" colspan="2">'+
                                                            '<a class="linkstabla6">'+	 element.dsantibiotico +'</a>'+
                                                        '</td>'+
                                                        '<td width="100" colspan="2">'+
                                                            '<a class="linkstabla6">'+	 element.dssensibilidad +'</a>'+
                                                        '</td>'+
                                                        '<td width="80" colspan="2">'+
                                                            '<a class="linkstabla6">'+element.mic_completo+'</a>'+
                                                        '</td>'+
                                                    
                                                    '</tr>' +   


                                                '</table>';
                                }else{
                                    contentAntibioticos += 
                                            '<table class="table table-striped"  style="width: 50%; margin-left: 5%;">'+
                                                    '<tr>'+
                                                    '<td width="5"></td>'+
                                                        '<td width="130" colspan="2">'+
                                                            '<div align="left"><a class="linkstabla5">ANTIBIOTICOS</a></div>'+

                                                    '</td>'+
                                                        '<td width="100" colspan="2">'+
                                                            '<div align="left"><a class="linkstabla5">SENSIBILIDAD </a></div>'+
                                                        '</td>'+
                                                        '<td width="80" colspan="2">'+
                                                                '<div align="left"><a class="linkstabla5">MIC </a></div>'+
                                                    ' </td>'+                                                                                
                                                    '</tr>';
                                    $.each( res.resultado.info.microorganismos.MicroorganismoDTO.antibioticos.AntibioticoDTO , (key,element) => { 
                                    
                                        contentAntibioticos += '<tr>'+

                                                '<td width="5"></td>'+
                                                '<td width="130" colspan="2">'+
                                                    '<a class="linkstabla6">'+	 element.dsantibiotico +'</a>'+
                                                '</td>'+
                                                '<td width="100" colspan="2">'+
                                                    '<a class="linkstabla6">'+	 element.dssensibilidad +'</a>'+
                                                '</td>'+
                                                '<td width="80" colspan="2">'+
                                                    '<a class="linkstabla6">'+element.mic_completo+'</a>'+
                                                '</td>'+
                                            
                                        '</tr>';
                                    });
                                    contentAntibioticos +=  '</table>';
                                }
                            
                            //NOTAS
                                if("notas" in res.resultado.info.microorganismos.MicroorganismoDTO &&  res.resultado.info.microorganismos.MicroorganismoDTO.notas.NotaMicroorganismoDTO != ""){
                                    contentNotas += '<dt class="col-sm-2 table-sm">Notas: </dt>';
                                        if(!$.isArray(res.resultado.info.microorganismos.MicroorganismoDTO.notas.NotaMicroorganismoDTO)){
                                            var element = res.resultado.info.microorganismos.MicroorganismoDTO.notas.NotaMicroorganismoDTO;
                                            contentNotas += '<table class="table table-borderless table-sm">'+
                                                    '<td text-align="center">'+

                                                        '<dl class="dl_row">'+
                                                            '<dt class="col-sm-2" style="margin-right:-40px !important;">'+ element.nombreNota +'</dt>'+
                                                                '<dd id="NumOT" class="col-sm-10" >'+
                                                                element.nota+
                                                                '</dd>'+

                                                        '</dl>'+
                                                    '</td>'+
                                                '</table>';
                                        }else{
                                            $.each( res.resultado.info.microorganismos.MicroorganismoDTO.notas.NotaMicroorganismoDTO , (key,element) => { 
                                                    contentNotas += '<table class="table table-borderless table-sm">'+
                                                            '<td text-align="center">'+

                                                                '<dl class="dl_row">'+
                                                                    '<dt class="col-sm-2" style="margin-right:-40px !important;">'+ element.nombreNota +'</dt>'+
                                                                        '<dd id="NumOT" class="col-sm-10" >'+
                                                                        element.nota+
                                                                        '</dd>'+

                                                                '</dl>'+
                                                            '</td>'+
                                                        '</table>';
                                                    });
                                        }
                                    
                                
                                }
                                $("#microorganismos").append(contentMicroorganismo+contentAntibioticos+contentNotas); 
                            }else{
                            
                                $.each( res.resultado.info.microorganismos.MicroorganismoDTO , (key,element) => { 
                                var contentMicroorganismo = '';
                                var contentAntibioticos = '';
                                var contentNotas = '';
                                
                                        contentMicroorganismo += '<table class="table table-borderless table-sm">'+
                                                    '<td text-align="center">'+

                                                        '<dl class="dl_row">'+
                                                            '<dt class="col-sm-2">Microorganismo: </dt>'+
                                                                '<dd id="NumOT" class="col-sm-10" >'+
                                                                element.descripcion+
                                                                '</dd>'+
                                                                
                                                            '<dt class="col-sm-2">Cantidad:</dt>'+
                                                                '<dd class="col-sm-10" >'+
                                                                    element.cantidadTotal+
                                                                '</dd>'+
                                                            '<dt class="col-sm-2">Antibiograma</dt>'+
                                                        '</dl>'+
                                                    '</td>'+
                                                '</table>';
                                            
                                    //TABLA DE ANTIBIOTICOS
                                    if(!$.isArray(element.antibioticos.AntibioticoDTO)){
                                    
                                        var element2 = element.antibioticos.AntibioticoDTO;
                                            contentAntibioticos += 
                                            '<table class="table table-striped"  style="width: 50%; margin-left: 5%;">'+
                                                    '<tr>'+
                                                    '<td width="5"></td>'+
                                                        '<td width="130" colspan="2">'+
                                                            '<div align="left"><a class="linkstabla5">ANTIBIOTICOS</a></div>'+

                                                    '</td>'+
                                                        '<td width="100" colspan="2">'+
                                                            '<div align="left"><a class="linkstabla5">SENSIBILIDAD </a></div>'+
                                                        '</td>'+
                                                        '<td width="80" colspan="2">'+
                                                                '<div align="left"><a class="linkstabla5">MIC </a></div>'+
                                                    ' </td>'+                                                                                
                                                    '</tr>'+
                                                    '<tr>'+

                                                        '<td width="5"></td>'+
                                                        '<td width="130" colspan="2">'+
                                                            '<a class="linkstabla6">'+	 element2.dsantibiotico +'</a>'+
                                                        '</td>'+
                                                        '<td width="100" colspan="2">'+
                                                            '<a class="linkstabla6">'+	 element2.dssensibilidad +'</a>'+
                                                        '</td>'+
                                                        '<td width="80" colspan="2">'+
                                                            '<a class="linkstabla6">'+element2.mic_completo+'</a>'+
                                                        '</td>'+
                                                    
                                                    '</tr>' +   


                                                '</table>';
                                            
                                    }else{
                                    contentAntibioticos += 
                                            '<table class="table table-striped"  style="width: 50%; margin-left: 5%;">'+
                                                    '<tr>'+
                                                    '<td width="5"></td>'+
                                                        '<td width="130" colspan="2">'+
                                                            '<div align="left"><a class="linkstabla5">ANTIBIOTICOS</a></div>'+

                                                    '</td>'+
                                                        '<td width="100" colspan="2">'+
                                                            '<div align="left"><a class="linkstabla5">SENSIBILIDAD </a></div>'+
                                                        '</td>'+
                                                        '<td width="80" colspan="2">'+
                                                                '<div align="left"><a class="linkstabla5">MIC </a></div>'+
                                                    ' </td>'+                                                                                
                                                    '</tr>';
                                                        $.each( element.antibioticos.AntibioticoDTO , (key2,element2) => { 
                                                        
                                                            contentAntibioticos += '<tr>'+

                                                                    '<td width="5"></td>'+
                                                                    '<td width="130" colspan="2">'+
                                                                        '<a class="linkstabla6">'+	 element2.dsantibiotico +'</a>'+
                                                                    '</td>'+
                                                                    '<td width="100" colspan="2">'+
                                                                        '<a class="linkstabla6">'+	 element2.dssensibilidad +'</a>'+
                                                                    '</td>'+
                                                                    '<td width="80" colspan="2">'+
                                                                        '<a class="linkstabla6">'+element2.mic_completo+'</a>'+
                                                                    '</td>'+
                                                                
                                                            '</tr>';
                                                        });
                                        contentAntibioticos +=  '</table>';
                                    }
                                
                                
                                //NOTAS
                                    if("notas" in element &&  (element.notas.NotaMicroorganismoDTO != "" && element.notas !="")){
                                        contentNotas += '<dt class="col-sm-2 table-sm">Notas: </dt>';
                                            if(!$.isArray(element.notas.NotaMicroorganismoDTO)){
                                            debugger;
                                                var element3 = element.notas.NotaMicroorganismoDTO;
                                                contentNotas += '<table class="table table-borderless table-sm" style="width: 70%;">'+
                                                        '<td text-align="center">'+

                                                            '<dl class="dl_row">'+
                                                                '<dt class="col-sm-2" style="margin-right:-40px !important;">'+ element3.nombreNota +'</dt>'+
                                                                    '<dd id="NumOT" class="col-sm-10" >'+
                                                                    element3.nota+
                                                                    '</dd>'+

                                                            '</dl>'+
                                                        '</td>'+
                                                    '</table>';
                                            }else{
                                                contentNotas += '<table class="table table-borderless table-sm" style="width: 70%;">';
                                                $.each( element.notas.NotaMicroorganismoDTO , (key3,element3) => { 
                                                    
                                                  contentNotas +=  '<tr text-align="center">'+

                                                                '<dl class="row col-sm-12">'+
                                                                    '<dt class="col-sm-2" style="margin-right:-40px !important;">'+ element3.nombreNota +'</dt>'+
                                                                        '<dd id="NumOT" class="col-sm-10" >'+
                                                                        element3.nota+
                                                                        '</dd>'

                                                                '</dl>'+
                                                            '</tr>'+'</br>';
                                                        
                                                    });
                                                    contentNotas += '</table>';
                                            }
                                        
                                        //$("#notas").append(contentNotas);
                                    }
                                    $("#microorganismos").append(contentMicroorganismo+contentAntibioticos+contentNotas); 
                                    //$("#antibioticos").append(contentAntibioticos);
                                    $("#antibioticos").show();
                                });
                                
                        } 
                        }
                }
                
        });
});

</script>

    <table class="table table-borderless table-sm">



    <tr>



        <td class="titulo" text-align="center">



        <a class="linkstabla4">Informaci&oacute;n del paciente</a>



        </td>



        <td class="titulo" text-align="center">



        <a class="linkstabla4">Informaci&oacute;n del Ex&aacute;men </a>



        </td>



    </tr>



    <tr>



        <td text-align="center">



        <dl class="dl_row">



            <dt class="col-sm-2">No. de O.T.:</dt>



            <dd id="NumOT" class="col-sm-10" >


            <?php echo $nroOrden;?>




            </dd>







            <dt class="col-sm-2">No. Historia:</dt>



            <dd class="col-sm-10" >

            <a id="nroHistoria"></a>

            



            </dd>







            <dt class="col-sm-2">Paciente:</dt>



            <dd class="col-sm-10">

                    <a id="nombreCompleto"></a>

            



            </dd>







            <dt class="col-sm-2">C&eacute;dula:</dt>



            <dd id="CedulaActual" class="col-sm-10">



                    <a id="nroDocumento"></a>



            </dd>



            







            <dt class="col-sm-2">Edad:</dt>



            <dd class="col-sm-10">



            <a id="fechaNacimiento"></a>



            </dd>



            <dt class="col-sm-2">Sexo:</dt>



            <dd class="col-sm-10">



            <a id="genero"></a>



            



            </dd>







            <dt class="col-sm-2">Empresa:</dt>



            <dd class="col-sm-10">



            <a id="nombreResponsable"></a>



            </dd>



        </dl>



        </td>



        <td text-align="center">



        <dl class="dl_row">



            <dt class="col-sm-2">C&oacute;digo:</dt>



            <dd id="CodigoExamen" class="col-sm-10">



            P315



            </dd>







            <dt class="col-sm-2">Servicio:</dt>



            <dd class="col-sm-10">



            



            </dd>







            <dt class="col-sm-2">M&eacute;dico:</dt>



            <dd class="col-sm-10">



            <a id="nombreMedico"></a>



            </dd>







            <dt class="col-sm-2">Recepci&oacute;n:</dt>



            <dd class="col-sm-10">



            Fecha:



            <a id="fechaOrden"></a>





            </dd>







            <dt class="col-sm-2">Muestra:</dt>



            <dd class="col-sm-10">



            Fecha:



            <a id="fechaRes"></a>





            </dd>



            <dt class="col-sm-2"></dt>



            <dd class="col-sm-10">



            Todos los valores de referencia estan ajustados por edad y sexo.



            </dd>



        </dl>



        </td>



    </tr>













    <tr>



        <td colspan="2" class="titulo" text-align="center">



        <a class="linkstabla4">Informaci&oacute;n de los Resultados</a>



        </td>



    </tr>



    <tr>



        <td text-align="center" colspan="2">



        <div text-align="center">



            







            



        <table class="table">



            <tr>



                <td height="20" bgcolor="#C1CE0D" colspan="8">



                    <div align="center">



                    <a class="linkstabla6"><font color="white"><b id="nomDescripcion"></b></font></a></div>



                </td>



            </tr>



            



            


                                                



        </table>



                                                                



    







            







            



            







            

        <table class="table table-striped" id="tabla">

            <tr>

                <td width="5"></td>

                <td width="130" colspan="2">

                    <div align="left"><a class="linkstabla5">Par&aacute;metro: </a></div>

                </td>

                <td width="100" colspan="2">

                    <div align="left"><a class="linkstabla5">Resultado: </a></div>

                </td>

                <td width="80" colspan="2">

                        <div align="left"><a class="linkstabla5">V.de referencias: </a></div>

                </td>
                
                <td width="80" colspan="2">

                        <div align="left"><a class="linkstabla5">Unidad: </a></div>

                </td>

            </tr>



        </table>
        
        <div id="microorganismos"></div>
        
        <div id="responsable"></div>
    <script>



</script>

                                                                



                                                                



    







            



            







            







            



        </div>



        </td>



    </tr>



    </table>







    <p>



    



    </p>





</body>


</html>



