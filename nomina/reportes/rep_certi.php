<?php
include_once("conex.php");
{
    header("Content-Type: text/html;charset=ISO-8859-1");
    $empresa='root';
    include_once("root/comun.php");
    

    include_once("root/montoescrito.php");  //para llamar la funcion de monto escrito.
    

    $fecha= date("Y-m-d");
    $hora = date("H:i:s");

    global $wnomina ;
    global $wemp_pmla;
    global $wtalhuma ;

    $wtalhuma = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

    $wnomina  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nomina');

    $tradministrador ='';
    if (isset($accion) && $accion == 'load')
    {
        $wodbc=consultarAliasPorAplicacion($conex, $wemp_pmla, 'q7_odbc_nomina');

        //$conexi = odbc_connect($wodbc,'informix','sco')
        $conexi = odbc_connect($wodbc,'','') or die("No se realizo Conexion con el Unix");

        $buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($id_padre))));
        $buscaNombre = strtoupper(strtolower($buscaNombre));

        $query1="SELECT percod,perap1,perap2,perno1,perno2"
              ."  FROM noper"
              ." WHERE peretr='A' "
              ."   AND ((perno1||' '||perno2||' '||perap1||' '||perap2) "
              ."  LIKE '%".$buscaNombre."%') "
              ."    OR percod LIKE '".$buscaNombre."' "
              ."ORDER BY  perno1,perno2,perap1,perap2";

        $tradministrador ='';
        $err_o = odbc_do($conexi,$query1);
        echo "<option value=''>seleccione</option>";
        while (odbc_fetch_row($err_o))
        {
           $tradministrador .=  "<option value='".odbc_result($err_o,1)."'>".odbc_result($err_o,1)."-".odbc_result($err_o,4)." ".odbc_result($err_o,5)." ".odbc_result($err_o,2)." ".odbc_result($err_o,3)."<br>";
        }
        echo $tradministrador;
        return;
    }

    if(isset($woperacion) && $woperacion=='verificarexplicacion')
    {
        $q = "SELECT Explicacion "
            ."  FROM  ".$wnomina."_000001 "
            ." WHERE  Codigo = '".$wmotivo."'";
        $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row =mysql_fetch_array($res);

        if ($row['Explicacion']=='on')
        {
            echo "<tr class='fila1' id='trotro'><td align='center'><b>Ingrese el motivo por el cual va a generar <br> su certificado (no mayor a 20 caracteres)<b></td><td ><textarea  onKeyDown='cuenta()' onKeyUp='cuenta()' id='textareaotros' rows='1' cols='45'></textarea>";
            echo "</td></tr>";
        }
        else
            echo "no";

         return;
    }
    elseif (isset($woperacion) && $woperacion=='Generacertificado')
    {
        $emp=explode('-',$empre);

        $codemp = $wemp_pmla;

        $query2 =" SELECT Encabezado,Piepag1,Piepag2,Piepag3,Contacto,Cargo"
                 ."  FROM ".$wnomina."_000001 "
                 ." WHERE  Codigo = '".$wmotivo."' ";
        //echo mysql_errno() ."=". mysql_error();

        $err2 = mysql_query($query2,$conex);
        $num2 = mysql_num_rows($err2);
        $row2 = mysql_fetch_array($err2);
        $Carta=ucfirst($row2[0]);
        $Cartap=explode('-',$row2[1]);
        $Cartap1=ucfirst($Cartap[0]);
        $Cartap2=$Cartap[1];
        $CartaL=ucfirst($row2[2]);
        $CartaA=ucfirst($row2[3]);
        $contacto=ucfirst($row2['Contacto']);
        $CargoquehaceConstar=($row2['Cargo']);

        $wodbc=consultarAliasPorAplicacion($conex, $codemp, 'q7_odbc_nomina');

        //$conexi = odbc_connect($wodbc,'informix','sco')
        $conexi = odbc_connect($wodbc,'','') or die("No se ralizo Conexion con el Unix");
        ///////////////////////////////////////////////////////////////////////////////////////// codigo del empleado dependiendo la empresa

        $cod=explode('-',$user); //Aca traigo de la variable global $user el codigo del empleado por el cual ingreso al matrix.

        if($wcodigonuevo !='')
        { $cod[1] = $wcodigonuevo; }

        if (strlen($cod[1])==6 or strlen($cod[1])==8)
		   $cod[1] = ( strlen($cod[1]) > 5) ? substr($cod[1],1,5): $cod[1]; // Esto para los usuarios del laboratorio que tienen 6 u 8 caracteres en el usuario
		  else
             $cod[1] = ( strlen($cod[1]) > 5) ? substr($cod[1],-5): $cod[1]; // Siempre validar que el código de usuario sea de cinco dígitos, sino entonces recortarlo a cinco digitos.

        $query1="SELECT perap1,perap2,perno1,perno2,perced,perfin,ofinom,perhco,cotnom,perbme,peruni"
              ."  FROM noper,noofi,nocot"
              ." WHERE percod='".$cod[1]."'"
              ."   AND perofi=oficod"
              ."   AND peretr='A'"
              ."   AND percot=cotcod";
        $err1 = odbc_do($conexi,$query1);
        $num1 = odbc_num_fields($err1);

        $row1=array();
        for ($i=1;$i<=$num1;$i++)
        {
            $row1[$i-1] = odbc_result($err1,$i);
        }

        $ced=$row1[4];
        $fin=$row1[5];
        $ofin=$row1[6];
        $hco=$row1[7];
        //$hco=$row1[7]*1*30;
        $cnom=$row1[8];
        $bme=$row1[9];

        if($row1[10]== '02')
        {
            $bme = ($bme / 0.65);
        }

        $nombreempl=$row1[2]." ".$row1[3]." ".$row1[0]." ".$row1[1];

        $hoy=date("Y-m-d");
        $dia=date("d");
        $mes=date("m");
        $ano=date("Y");

        $vectormes[] = array();
        $vectormes['01']='Enero';
        $vectormes['02']='Febrero';
        $vectormes['03']='Marzo';
        $vectormes['04']='Abril';
        $vectormes['05']='Mayo';
        $vectormes['06']='Junio';
        $vectormes['07']='Julio';
        $vectormes['08']='Agosto';
        $vectormes['09']='Septiembre';
        $vectormes['10']='Octubre';
        $vectormes['11']='Noviembre';
        $vectormes['12']='Diciembre';

        $mes = $vectormes[$mes];
        $CartaL = str_replace("dd",$dia,$CartaL);
        $CartaL = str_replace("mm",$mes,$CartaL);
        $CartaL = str_replace("yy",$ano,$CartaL);
        //$formato = '<input type="button" name="imprimir" value="Imprimir" onclick="window.print();">';
        // $formato = "<div style='width : 1000px; font-size: 12pt; '>";
        //encabezado($titulo,$wactualiz, $wemp_pmla);

        $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

        $wbasedato1  = $institucion->baseDeDatos;
        $wnit        = $institucion->nit;

        $wlogo = consultarAliasPorAplicacion($conex, $wemp_pmla, 'logo_certificado');
		

        $formato = "<table border=0>";  //border=0 no muestra la cuadricula en 1 si
        $formato .= "<tr><td colspan='3' align=center><IMG SRC='/MATRIX/images/medical/root/".$wlogo."' WIDTH=300 HEIGHT=120></td>"; //trae el logo de la promotora.
        $formato .= "<tr><td style='font-size : 10pt;' align=center colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000066;'><b>Nit. ".$wnit."</b></td></tr>";
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td style='font-size : 10pt; font-color: #000000;' align=left colspan='3' bgcolor='#FFFFFF'>".vfecha($hoy)."</td></tr>";
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td align=center colspan='3' class='titulocertificado' bgcolor='#FFFFFF' style='font-size : 14pt; font-color: #000000; '><b>".$CargoquehaceConstar."</b></td></tr>";
        $formato .= "<tr><td align=center colspan='3' class='titulocertificado' bgcolor='#FFFFFF'  style='font-size : 14pt; font-color: #000000;'><b>HACE CONSTAR</b></td></tr>";
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td align=left colspan='3' bgcolor=#FFFFFF  style='font-size : 10pt; font-color: #000000;'>".$Carta."</td></tr>";
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td align=left bgcolor=#FFFFFF  style='font-size : 10pt; font-color: #000000;'>Empleado</td><td align=center style='font-size : 10pt; font-color: #000000;'>:</td><td align=left style='font-size : 10pt;  font-color: #000000;' ><b>".$nombreempl."</b></td></tr>";
        $formato .= "<tr><td align=left bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>Cédula de Ciudadanía</td><td align=center style='font-size : 10pt; font-color: #000000;'>:</td><td align=left style='font-size : 10pt; font-color: #000000;'><b>".$ced."</b></td></tr>";
        $formato .= "<tr><td align=left bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>Fecha de Inicio del Contrato Actual</td><td align=center style='font-size : 10pt; font-color: #000000;' >:</td><td align=left style='font-size : 10pt; font-color: #000000;'><b>".vfecha($fin)."</b></td></tr>";
        $formato .= "<tr><td align=left bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>Cargo Actual</td><td align=center style='font-size : 10pt; font-color: #000000;'>:</td><td align=left style='font-size : 10pt; font-color: #000000;'><b>".$ofin."</b></td></tr>";
        $formato .= "<tr><td align=left bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>Tipo de Contrato</td><td align=center style='font-size : 10pt; font-color: #000000;'>:</td><td align=left style='font-size : 10pt; font-color: #000000;'><b>".$cnom."</b></td></tr>";
		
		if( $wmotivo != '06' )
			$formato .= "<tr><td align=left bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>Horas Laboradas al Mes</td><td align=center style='font-size : 10pt; font-color: #000000;'>:</td><td align=left style='font-size : 10pt; font-color: #000000;'><b>".$hco."</b></td></tr>";
        
		if( $wmotivo != '06' )
			$formato .= "<tr><td align=left bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>Remuneración Mensual</td><td style='font-size : 10pt; font-color: #000000;' align=center>:</td><td align=left style='font-size : 10pt; font-color: #000000;'><b>".number_format($bme,2,'.',',')."</b></td></tr>";
        
		if( $wmotivo != '06' )
			$formato .= "<tr><td align=left colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'><b>".montoescrito($bme)."</td></tr>";
		
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td align=left colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>".$Cartap1."</td></tr>";
        $formato .= "<tr><td align=left colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>".$Cartap2."</td></tr>";
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td align=left colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;' >".$CartaL."</td></tr>";
        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td align=left colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000000;'>".$CartaA."</td></tr>";

        $wfirma = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Archivofirmanomina');

        $formato .= "<tr><td colspan='3' align=left><IMG SRC='../../images/medical/nomina/".$wfirma."' WIDTH=350 HEIGHT=120></td>"; //trae una imagen de firma digitalizada.

        $formato .= "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
        $formato .= "<tr><td align=center colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000066;'><b>".$contacto."</b></td></tr>";
        //$formato .= "<tr><td align=center colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000066;'><b>Diagonal 75B No. 2A-120 Oficina 309· Teléfono: 342 10 10· Fax: 341 05 04· Apartado 5455</b></td></tr>";
        $formato .= "<tr><td align=center colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000066;'><b>www.lasamericas.com.co· e-mail: nomina@lasamericas.com.co</b></td></tr>";
        $formato .= "<tr><td align=center colspan='3' bgcolor=#FFFFFF style='font-size : 10pt; font-color: #000066;' ><b>Medellín - Colombia</b></td></tr>";
        $formato .= "</table>";

        echo "
                ".$formato."
            ";
       return;
       // cierre del else donde empieza la impresión
    }
    elseif (isset($woperacion) && $woperacion=='Guardarestadistica')
    {
        $cod=explode('-',$user);

		if (strlen($cod[1])==6 or strlen($cod[1])==8)
		   $cod[1] = ( strlen($cod[1]) > 5) ? substr($cod[1],-6): $cod[1]; // Esto para los usuarios del laboratorio que tienen 6 u 8 caracteres en el usuario
		  else
             $cod[1] = ( strlen($cod[1]) > 5) ? substr($cod[1],-5): $cod[1]; // Siempre validar que el código de usuario sea de cinco dígitos, sino entonces recortarlo a cinco digitos.

        if(isset($wcodigonuevo) && $wcodigonuevo !='')
        { $cod[1] = $wcodigonuevo; }

        if((isset($wcodigonuevo) && $wcodigonuevo != '') || $cod[1] != '') // Solo insertar si realmente viene un código.
        {
             $q= "INSERT INTO ".$wnomina."_000008
                         (Medico,Fecha_data, Hora_data,Ceruse,Cermot,Cerest,Ceremp,Cerexp,Seguridad) "
             ."   VALUES ('".$wnomina."','".$fecha."', '".$hora."' , '".$cod[1]."','".$wmotivo."' , 'on', '".$wempresa."','".utf8_decode($wotro)."', 'C-".$cod[1]."')";
        }

        $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

        return;
    }
}
?>
<html>
<head>
<title>Certificado Laboral</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script type="text/javascript">
function parpadearDiv(ele)
{
    if ( $("#"+ele).is(":visible")) {
        $("#"+ele).hide();
        $("#"+ele).css("color","#f2f2f2");
    }
    else {
        $("#"+ele).show();
        $("#"+ele).css("color","red");
    }
}

function recargarLista(id_padre, id_hijo)
{
    val = $("#"+id_padre).val();
    if(val != '*')
    {
        $('#'+id_hijo).load(
                "rep_certi.php",
                {
                    consultaAjax:   '',
                    accion:     'load',
                    id_padre:   val,
                    wemp_pmla    : $('#wemp_pmla').val()
                });
    }
}

function enterBuscar(ele,hijo,op,form,e)
{
    tecla = (document.all) ? e.keyCode : e.which;
    if(tecla==13) { $("#"+hijo).focus(); }
    else { return true; }
    return false;
}


function cambioImagen(img1, img2)
{
    $('#'+img1).hide(1000);
    $('#'+img2).show(1000);
}
function cuenta()
{
$("#textareaotros").keyup(function(){
                       if ( (/.{20}/).test( $(this).val() ) ){
                               var text = $(this).val().substring(0, 20 );
                               $(this).val(text);
                       }
               });

}
function Pedirexplicacion()
{
    var motivo = $('#idmotivo').val();
    $.post("rep_certi.php",
    {
        consultaAjax: '',
        woperacion    : 'verificarexplicacion',
        wemp_pmla    : $('#wemp_pmla').val(),
        wmotivo        :    motivo

    },function(data) {

    if(data=='no')
        { $('#trotro').remove();}
    else{
        $(data).insertAfter( $('#trencabezado') );
    }
    });


}
function disableselect(e){
    return false
}

function reEnable(){
    return true
}

function backhome(e){
window.clipboardData.clearData();
}

function click(){
    if(event.button){
    window.clipboardData.clearData();
    }
}

function Generar()
{
    if($('#idmotivo').val() == 'seleccionar')
    {
        alert("Debe seleccionar una razon para generar el certificado");
    }else if($('#textareaotros').val() == '')
    {
        alert("Debe de escribir el motivo por el cual va a sacar el certificado");
    }else if($('#textareaotros').val()!=undefined && $('#textareaotros').val().length < 5)
    {
        alert("el motivo no puede ser menor de 5 caracteres");
    }
    else
    {
        var wcodigonuevo = "";
        if($('#wuse_pfls').length > 0)
        {
            wcodigonuevo = $('#wuse_pfls').val();
            if(wcodigonuevo == '')
            {
                alert("Debe seleccionar un usuario");
                return;
            }
        }

        var otrocmp = "";
        if($('#textareaotros').length > 0)
        {
            otrocmp = $('#textareaotros').val();
        }

        //Evita usar el boton derecho del ratón
        document.oncontextmenu = function(){return false}
        //No permite seleccionar el contenido de una página

        document.onselectstart=new Function ("return false")
        if (window.sidebar){
            document.onmousedown=disableselect
            document.onclick=reEnable
        }

        //Borra el Portapapeles con el uso del teclado
        if (document.layers)
        document.captureEvents(Event.KEYPRESS)

        //Borra el Portapapeles con el uso del mouse
        document.onkeydown=backhome
        var empresa = $('#codigoempresa').val();
        var motivo = $('#idmotivo').val();
        var otro = otrocmp;

        $.post("rep_certi.php",
            {
                consultaAjax : '',
                empre        : empresa,
                woperacion   : 'Generacertificado',
                wmotivo      : motivo,
                wemp_pmla    : $('#wemp_pmla').val(),
                wcodigonuevo : wcodigonuevo
            }
            ,function(data) {

            $('#div_ppal').empty();
            $('#div_ppal').html(data);
            if ( $.browser.msie ) {
                $('body td').css("font-size", "8pt");
                $('.titulocertificado').css("font-size", "12pt");
                $('body').css("margin-left", "100px");
                $('body').css("margin-right", "100px");
                $('body').css("width", 900);
                $('body').css("position",'relative');
            }
            else
            {
                $('body').css("width", 900);
                $('body').css("position",'relative');
            }

            }).done(function(){ window.print();});

            $.post("rep_certi.php",
            {
                consultaAjax : '',
                woperacion   : 'Guardarestadistica',
                wmotivo      : motivo,
                wempresa     : empresa,
                wemp_pmla    : $('#wemp_pmla').val(),
                wotro        : otro,
                wcodigonuevo : wcodigonuevo
            },function(data) {

            });
    }
}
//Evita usar el boton derecho del ratón
//document.oncontextmenu = function(){return false}
//No permite seleccionar el contenido de una página
function disableselect(e){
return false
}
function reEnable(){
return true
}


//Borra el Portapapeles con el uso del mouse
// document.onkeydown=backhome
function click(){
if(event.button){
//window.clipboardData.clearData();
}
}
//document.onmousedown=click
//-->
</script>

</head>

<style>

</style>
<!--<body TEXT="#000066" BGCOLOR="ffffff" onMouseOut="window.clipboardData.clearData(); return false" onMouseOver="window.clipboardData.clearData(); return false" >-->
<body>
<!--<font face='arial'>-->




<?php
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false" >
// onkeydown="return false" deja inaviñitado el teclado.
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

function vfecha($efecha)    // Funcion para sacar el escrito de la fecha Ej: 15 de Julio de 2007
{
	$mes=array();

	$mes[1]='Enero';
	$mes[2]='Febrero';
	$mes[3]='Marzo';
	$mes[4]='Abril';
	$mes[5]='Mayo';
	$mes[6]='Junio';
	$mes[7]='Julio';
	$mes[8]='Agosto';
	$mes[9]='Septiembre';
	$mes[10]='Octubre';
	$mes[11]='Noviembre';
	$mes[12]='Diciembre';

	$vfecha=substr($efecha,8,2)." de ".$mes[(integer)substr($efecha,5,2)]." de ". substr($efecha,0,4);

	return $vfecha;
}

/*******************************************************************************************************************************************
*                                                CERTIFICADO LABORAL            		                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
/*
PROGRAMA                    :Certicado Laboral.
AUTOR                       :Ing. Gustavo Alberto Avendano Rivera.
FECHA CREACION              :JULIO 4 DE 2007.
FECHA ULTIMA ACTUALIZACION  :15 de Agosto de 2013.
DESCRIPCION                 :Este programa sirve para que cada empleado con su codigo pueda imprimir su certificado laboral.

Actualizaciones:
* 2017-04-18   (Arleyda Insignares): Actualizacion ODBC 
* 12 Mayo 2014 (Felipe Alvarez) : Se agrega  a la validacion de numero de certificados laborales permitidos en un periodo el año  , pues al no estar esto se generaba error
* 18 Febrero 2014 (Juan C. Hdez) : Se modifica la validación del usuarios en talhuma_000013 porque en el laboratorio el codigo es de 6 caracteres o de 8, no 5 o 7
                                   como en la clínica.
* 15 Agosto 2013  : *   Se realiza modificacion en la operación "Guardarestadistica" para que siempre valíde si el código de un usuario es de cinco dígitos, sino
                        entonces convertirlo a cinco dígitos para que siempre se guardé así no a veces de cinco y en otros casos de seis dígitos para el mismo
                        usuario (era de cinco cuando lo seleccionaba el administrador y era de seis cuando el usuario entraba directamente y generaba su certificado).
                    *   Tampoco estaba bloqueando a las personas que tenían registrado en nomina_000008 un código repetido (con cinco y con seis digitos de código),
                        en la consulta para saber cuántas veces por mes ha generado certificado se realiza una modificación para que siempre consulte cinco digitos del código
                        si es que al momento de consultar tiene más de los cinco dígitos.

* 14 Agosto 2013   : Se adiciona un texto de alerta al principio de la página informando que como máximo al mes solo puede descargar dos certificados por el sistema.


TABLAS UTILIZADAS :
nomina_000001     : Tabla de Nomina, donde se encuentra el escrito de la carta laboral.
root_000050       : Tabla de Empresas.
noper             : Tabla de personal.
noofi             : Tabla de oficios de los empleados.
nocot             : Tabla donde esta el nombre de los contratos.
*/
//==========================================================================================================================================
$wactualiz="2017-04-18";

echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';


session_start();
if(!isset($_SESSION['user']))
{
	echo "error";
}
else
{
    $alerta_msj = '
                    <script>setInterval(\'parpadearDiv(\"spn_alerta\")\',600);</script>
                    <div style=" padding: 3px;text-align:justify;border:2px solid #2A5DB0;background-color:#f2f2f2;">
                        <table border="0" cellspacing="0" cellpadding="0" align="center">
                            <tr>
                                <td style="font-size:14pt; font-weight:bold;">&nbsp;</td>
                                <td><span id="spn_alerta" style="font-size:14pt; font-weight:bold;">¡¡¡ Atención leer antes de generar !!!</span></td>
                            </tr>
                        </table>
                        <br><br>
                        Recuerde que los certificados <span id="" style="font-size:10pt; font-weight:bold;">solo pueden ser generados dos veces al mes</span>, por ello antes de hacerlo verifique que:

                        <ul>
                            <li> La impresora esté configurada, tenga tinta o toner, hojas limpias.</li>
                            <li> Ingresar a matrix desde su navegador de internet.</li>
                            <li> Que el certificado lo genere porque realmente lo necesita.</li>
                        </ul>
                        <p>
                        Se recomienda hacer pruebas de impresión antes de generar el certificado, ya que después de dar <span style="font-size:10pt; font-weight:bold;">GENERAR</span>, el sistema inicia el conteo y después de dos veces generado <span style="font-size:10pt; font-weight:bold;">se inactivará</span> esta opción.
                        <br><br>
                        La generación de la carta es un proceso automático, el cual no podrá ser modificado por el área de nómina ni por sistemas,
                        por lo tanto si el usuario bloquea su opción de generar por exceder los dos certificados, debe esperar hasta el mes siguiente.
                        <br><br>
                        Los certificados para trámite de visa debe solicitarse directamente en la oficina de nómina.
                        <br><br>
                        </p>
                        <hr>
                        <div style="font-weight:bold;font-size:12pt;text-align:center;">Hagamos buen uso de esta herramienta que es de gran utilidad para todos, evitemos contratiempos.</div>
                        <hr>
                    </div><br><br>';
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato1 = $institucion->baseDeDatos;


	$titulo = "CERTIFICADO LABORAL";
	// Se muestra el encabezado del programa

	echo "<div id='div_ppal' width='80%' >";
	encabezado($titulo,$wactualiz, "logo_".$wbasedato1);


	$cod=explode('-',$user);//codigo de empleado
    $codigodeempleadoaux = $cod[1];

	if (strlen($cod[1])==6 or strlen($cod[1])==8)
	   {
	    $codigodeempleadoaux = ( strlen($cod[1]) > 5) ? substr($cod[1],-6): $cod[1];
		$codigodeempleado    = ( strlen($cod[1]) > 5) ? substr($cod[1],-6): $cod[1];
	   }
	  else
         {
		  $codigodeempleadoaux = ( strlen($cod[1]) > 5) ? substr($cod[1],-5): $cod[1];
		  $codigodeempleado    = ( strlen($cod[1]) > 5) ? substr($cod[1],-5): $cod[1];
		 }

	$q =  " SELECT Empresa  "
				 ."   FROM  usuarios "
				 ."  WHERE  Codigo = '".$cod[1]."' ";
			//-
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$empresa = $row['Empresa'];

	$emp=explode('-',$empresa);
	$codemp = $emp[0]; //codigo de empresa

	$wadministrador = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Administradorcertificadoslaborales');
	$pos = strpos($wadministrador, $codigodeempleado);

	//echo $pos."------".$wadministrador."--------".$codigodeempleado;
	if($pos !== false)
		$wadministrador = "si";
	else
		$wadministrador = "no";

	//---------consulta para saber cuantos certificados a sacado una persona en el mes
	$mes= date("m");
	//---------consulta para saber cuantos certificados a sacado una persona en el año
	$ano= date("Y");

	$q =  " SELECT COUNT(*) as Cantidad"
		."    FROM ".$wnomina."_000008 "
		."   WHERE Ceruse ='".$codigodeempleadoaux."' "
		."     AND MONTH(Fecha_data)='".$mes."'"
		." 	   AND YEAR (Fecha_data)='".$ano."'";


	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);

	$numerocertificados=$row['Cantidad'];

	//------------------
	//--------------------------

	$wnumerocertificadoxempresa = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Certificadoslaboralesmes');

	if($wadministrador =='si')
	{
		$tradministrador = '<tr>';
		$tradministrador .= '<td class="encabezadoTabla" width="400">&nbsp;Seleccionar usuario:</td>';
		$tradministrador .=  '<td class="fila1" width="300">';
		$tradministrador .=  "<table border='0' cellspacing='0' cellpadding='0' >";

		$tradministrador .=  '<tr>';
		$tradministrador .=  '<td>';
		$tradministrador .=  "<div id='cusel'><img title='Seleccione un usuario' width='10 ' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' /></div>";

		$tradministrador .= "<div id='cuload' style='display:none;' ><img width='10 ' height='10' border='0' src='../../images/medical/ajax-loader9.gif' /></div>";
		$tradministrador .=  "</td>";
		$tradministrador .=  '<td>';
		$tradministrador .= "<table><tr>
<td class='fila1' style='width:450px;' align='center'>&nbsp;
<img title='Busque el nombre o parte del nombre del centro de costo' width='12 ' height='12' border='0' src='../../images/medical/HCE/lupa.PNG' />
<input id='wnomuse_pfls' name='wnomuse_pfls' value='' size='60' onkeypress='return enterBuscar(\"wnomuse_pfls\",\"wuse_pfls\",\"user\",\"load_users\",event);' onfocus='cambioImagen(\"cusel\",\"cuload\");' onBlur='recargarLista(\"wnomuse_pfls\",\"wuse_pfls\"); cambioImagen(\"cuload\",\"cusel\");'/>
</td></tr>";
		$tradministrador .=  '<tr><td><select style="width:430px;" id="wuse_pfls" name="wuse_pfls" >';

  		$wodbc=consultarAliasPorAplicacion($conex, $wemp_pmla, 'q7_odbc_nomina');

        //$conexi = odbc_connect($wodbc,'informix','sco')  Conexion Anterior -Informix
   		$conexi = odbc_connect($wodbc,'','') or die("No se realizo Conexion con el Unix");

		$query1="SELECT percod,perap1,perap2,perno1,perno2"
			  ."  FROM noper"
			  ." WHERE peretr='A'";

		$err_o = odbc_do($conexi,$query1);
		while (odbc_fetch_row($err_o))
		{
		  if ($cod[1] == odbc_result($err_o,1) )
		   $tradministrador .=  "<option  selected value='".odbc_result($err_o,1)."'>".odbc_result($err_o,1)."-".odbc_result($err_o,4)." ".odbc_result($err_o,5)." ".odbc_result($err_o,2)." ".odbc_result($err_o,3)."<br>";
		 else
		  $tradministrador .=  "<option value='".odbc_result($err_o,1)."'>".odbc_result($err_o,1)."-".odbc_result($err_o,4)." ".odbc_result($err_o,5)." ".odbc_result($err_o,2)." ".odbc_result($err_o,3)."<br>";
		}

		$tradministrador .=  '</select>';
		$tradministrador .=  '</td>';
		$tradministrador .=  '</tr></table></td></tr>';

		$tradministrador .=  '</table>';
		$tradministrador .=  '</td>';
		$tradministrador .=  '</tr>';
	}


	if ($wadministrador =='si' )
	{
			// el permiso para el certificado esta en talhuma
			$q=   " SELECT Idecer "
				."    FROM ".$wtalhuma."_000013 "
				."   WHERE LEFT(Ideuse,5) ='".$codigodeempleado."' ";

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			//-Consulta que trae la empresa del usuario
			$q =  " SELECT Empresa  "
				 ."   FROM  usuarios "
				 ."  WHERE  Codigo = '".$cod[1]."' ";
			//-
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$empresa = $row['Empresa'];

			echo "<input type='hidden' id = 'codigoempresa' value='".$empresa."'>";//oculto que contiene el codigo de la empresa del usuario

			echo "<br><br>".$alerta_msj;

			echo "<center><table width='700'>";
			$emp=explode('-',$empresa);
			echo $tradministrador;
			$codemp = $emp[0]; //codigo de empresa
			echo "<tr id='trencabezado'><td width='400'  class='encabezadoTabla' > Seleccione una razón para generar el certificado</td><td width='300' align='center'   class='fila1' ><select   style='width: 400px' id ='idmotivo' onchange='Pedirexplicacion()'>";

			//- consulta que trae las causas para sacar certificado laboral
			$q =  "   SELECT Codigo,Motivo "
			 ."         FROM  ".$wnomina."_000001 "
			 ."		ORDER BY  Explicacion ,Motivo";
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			//-

			echo "<option value='seleccionar'>seleccionar</option>";

			While($row =mysql_fetch_array($res))
			{
			   echo "<option value='".$row['Codigo']."'>".$row['Motivo']."</option>";
			}

			echo "</select>";
			echo "</td></tr>";

			echo "<tr><td colspan='2' align=center class='fila1' ><input type='button' value='Generar' onclick='Generar()'></td>";          //submit osea el boton de Generar o Aceptar
			echo "</tr>";

			echo "</table>";
			echo "</div>";
			echo "<div id='resul'>";
			echo "</div>";

	}
	else if(  $wadministrador =='no'  && $numerocertificados < $wnumerocertificadoxempresa    )
	{
		$validarNominaMatrix = consultarAliasPorAplicacion($conex, $wemp_pmla, 'validarNominaMatrix' ) == 'on';

		if( $validarNominaMatrix ){
			if (strlen($codigodeempleado) == 6 or strlen($codigodeempleado) == 8) //Esto ocurre con los codigos del laboratorio 18/02/2014 Juan C. Hdez
			   {
				// el permiso para el certificado esta en talhuma
				$q = " SELECT Idecer "
					."   FROM ".$wtalhuma."_000013 "
					."  WHERE LEFT(Ideuse,6) ='".$codigodeempleado."' ";
			   }
			  else
				 {
				  // el permiso para el certificado esta en talhuma
				  $q = " SELECT Idecer "
					  ."   FROM ".$wtalhuma."_000013 "
					  ."  WHERE LEFT(Ideuse,5) ='".$codigodeempleado."' ";
				  }

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($res);
		}


		if( $validarNominaMatrix && $row['Idecer']!='on')
		{
			echo"<br><br><table align = 'center' width='800'><tr><td align='center'>COMUNIQUESE CON EL DEPARTAMENTO DE NOMINA <br> NO SE PUEDE GENERAR SU CERTIFICADO</td></tr></table>";
		}
		else
		{
			//-Consulta que trae la empresa del usuario
			$q =  " SELECT Empresa  "
				 ."   FROM usuarios "
				 ."  WHERE Codigo = '".$cod[1]."' ";
			//-
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row =mysql_fetch_array($res);

			$empresa = $row['Empresa'];

			echo "<input type='hidden' id = 'codigoempresa' value='".$empresa."'>";//oculto que contiene el codigo de la empresa del usuario

			echo "<br><br>".$alerta_msj;
			echo "<center><table width='700'>";
			$emp=explode('-',$empresa);
			echo $tradministrador;
			$codemp = $emp[0]; //codigo de empresa
			echo "<tr id='trencabezado'><td width='400'  class='encabezadoTabla' > Seleccione una razón para generar el certificado</td><td width='300' align='center'   class='fila1' ><select   style='width: 400px' id ='idmotivo' onchange='Pedirexplicacion()'>";

			//- consulta que trae las causas para sacar certificado laboral
			$q =  "   SELECT Codigo,Motivo "
			 ."         FROM  ".$wnomina."_000001 "
			 ."		ORDER BY  Explicacion ,Motivo";
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			//-

			echo "<option value='seleccionar'>seleccionar</option>";

			While($row =mysql_fetch_array($res))
			{
			   echo "<option value='".$row['Codigo']."'>".$row['Motivo']."</option>";
			}

			echo "</select>";
			echo "</td></tr>";

			echo "<tr><td colspan='2' align=center class='fila1' ><input type='button' value='Generar' onclick='Generar()'></td>";          //submit osea el boton de Generar o Aceptar
			echo "</tr>";

			echo "</table>";
			echo "</div>";
			echo "<div id='resul'>";
			echo "</div>";
		}
	}
	else if ($wadministrador =='no' && $numerocertificados >= $wnumerocertificadoxempresa   )
	{
			echo"<br><br><table align = 'center'><tr><td align='center'>USTED YA GENERO EL NUMERO MAXIMO <br>   DE CERTIFICADOS  POR MES</td></tr></table>";
	}
}



// Se liberan recursos y se cierra la conexión
//odbc_free_result($err1);
//odbc_close($conexi);
?>


</body>
</html>