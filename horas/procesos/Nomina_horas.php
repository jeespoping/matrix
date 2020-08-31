<?php
include_once("conex.php");

 if(!isset($consultaAjax)) { ?>
<head>
  <title>REPORTE DE RECARGOS Y HORAS EXTRAS DEL PERSONAL</title>
</head>
<body>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript">
  // Funciones para la Encriptación de claves
  function hex_sha1(a){return rstr2hex(rstr_sha1(str2rstr_utf8(a)))}
  function hex_hmac_sha1(a,b){return rstr2hex(rstr_hmac_sha1(str2rstr_utf8(a),str2rstr_utf8(b)))}
  function sha1_vm_test(){return hex_sha1("abc").toLowerCase()=="a9993e364706816aba3e25717850c26c9cd0d89d"}
  function rstr_sha1(a){return binb2rstr(binb_sha1(rstr2binb(a),a.length*8))}
  function rstr_hmac_sha1(c,f){var e=rstr2binb(c);if(e.length>16){e=binb_sha1(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binb_sha1(a.concat(rstr2binb(f)),512+f.length*8);return binb2rstr(binb_sha1(d.concat(g),512+160))}
  function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}
  function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}
  function rstr2binb(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(24-c%32)}return a}
  function binb2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(24-c%32))&255)}return a}function binb_sha1(v,o){v[o>>5]|=128<<(24-o%32);v[((o+64>>9)<<4)+15]=o;var y=Array(80);var u=1732584193;var s=-271733879;var r=-1732584194;var q=271733878;var p=-1009589776;for(var l=0;l<v.length;l+=16){var n=u;var m=s;var k=r;var h=q;var f=p;for(var g=0;g<80;g++){if(g<16){y[g]=v[l+g]}else{y[g]=bit_rol(y[g-3]^y[g-8]^y[g-14]^y[g-16],1)}var z=safe_add(safe_add(bit_rol(u,5),sha1_ft(g,s,r,q)),safe_add(safe_add(p,y[g]),sha1_kt(g)));p=q;q=r;r=bit_rol(s,30);s=u;u=z}u=safe_add(u,n);s=safe_add(s,m);r=safe_add(r,k);q=safe_add(q,h);p=safe_add(p,f)}return Array(u,s,r,q,p)}
  function sha1_ft(e,a,g,f){if(e<20){return(a&g)|((~a)&f)}if(e<40){return a^g^f}if(e<60){return(a&g)|(a&f)|(g&f)}return a^g^f}function sha1_kt(a){return(a<20)?1518500249:(a<40)?1859775393:(a<60)?-1894007588:-899497514}
  function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}
  function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};

  var celda_ant  ="";
  var celda_ant_clase="";

  $(document).ready(function(){
     $('#txtsdaclave').focus();
     // Se configura la pantalla para segunda clave como modal
     $("#pancrearclave").dialog({
        autoOpen: false,
        top: 100,
        height: 400,
        width: 600,
        position: ['left+170', 'top+80'],
        modal: true
    });

    $("#btncrearclave")
          .button()
          .click(function() {
            //Limpiamos el panel.
            $("#pancrearclave").dialog("open");
            //event.preventDefault();
          });

     $('.blink').effect("pulsate", {times:120}, 120000);

  });

function cerrar()
{
  window.close();
}

function ingresar()
{
  var wemp_pmla = document.getElementById('wemp_pmla').value;
  var vano= document.getElementById('wano').value;
  var vmes= document.getElementById('wmes').value;
  var vqui= document.getElementById('wqui').value;
  segclave=true;
  document.location.href= 'Nomina_horas.php?wemp_pmla='+wemp_pmla+'&segclave='+segclave+'&wano='+vano+'&wmes='+vmes+'&wqui='+vqui;
}

// Valida las claves ingresadas y graba la segunda clave en rephor_000001
function GrabarsdaClave(vusuario)
{
  var wemp_pmla  = document.getElementById('wemp_pmla').value;
  var clave_usu1 = hex_sha1(document.getElementById('txtsdaclave1').value);
  var clave_usu2 = hex_sha1(document.getElementById('txtsdaclave2').value);
  var clave_ante = hex_sha1(document.getElementById('txtclaveanterior').value);
  if (clave_usu1 != clave_usu2)
    {
      alerta('La Claves no coinciden favor verificar');
      return;
    }
  if (document.getElementById('txtsdaclave1').value == '' || document.getElementById('txtsdaclave2').value == '')
    {
      alerta('Las claves no pueden estar en blanco');
      return;
    }

  $.post("Nomina_horas.php",
          {
            consultaAjax:   true,
            accion:         'crearClave',
            wemp_pmla:      wemp_pmla,
            codigo_usu:     vusuario,
            clave_usu1:     clave_usu1,
            clave_usu2:     clave_usu2,
            clave_ante:     clave_ante
          }, function(respuesta){
             if (respuesta !== '')
              {
                if (respuesta == 'M' || respuesta == 'I')
                    alerta('Grabado Exitoso');
                if (respuesta == 'A')
                    alerta('La Clave anterior no concuerda');
                if (respuesta == 'N')
                    alerta('El Usuario no se encuentra registrado en Nomina');
              }
             else
                alerta('El Usuario no tiene acceso');

             $("#pancrearclave").dialog("close");
          });
}

function Retornar()
{
  var wemp_pmla = document.getElementById('wemp_pmla').value;
  segclave = true;
  document.location.href= 'Nomina_horas.php?wemp_pmla='+wemp_pmla+'&segclave='+segclave;
}

function ValidarClave(vusuario)
{
  var clave_usu = '';
  var wemp_pmla = document.getElementById('wemp_pmla').value;
  if (document.getElementById('txtsdaclave').value != '')
     {
      clave_usu = hex_sha1(document.getElementById('txtsdaclave').value);
      document.getElementById('txtsdaclave').value = clave_usu;
     }

  $.post("Nomina_horas.php",
          {
            consultaAjax:   true,
            accion:         'validarUsuario',
            wemp_pmla:      wemp_pmla,
            codigo_usu:     vusuario,
            clave_usu:      clave_usu
          }, function(respuesta){
             if (respuesta == '')

                alerta('El Usuario no tiene acceso');

             else
              {
                acceso   = respuesta.split('-');
                usuario  = acceso[0];
                carne    = acceso[1];
                ingreso  = acceso[2];
                segclave = true;
                if (ingreso == 'N')
                   {alerta('La Clave es invalida');}
                else
                   {document.location.href= 'Nomina_horas.php?wemp_pmla='+wemp_pmla+'&segclave='+segclave;}
              }
          });
}

function selectadelante(vectorphp,wemp_pmla,wano,wmes,wqui)
{
   var vector = vectorphp.split("_v_");
   var combo = document.forms["nomina"].wempleado;
   var actual = combo.options[combo.selectedIndex].value;
   var cantidad = vector.length;

   var nuevocombo = combo.value.split('-');
   nuevocombo= nuevocombo[0];

   for (i = 0; i < cantidad; i++)
   {

	 if (nuevocombo == vector[i].substring(0,5))
	     {

			if (i <= ((cantidad)-2))
			{

				document.forms["nomina"]["wempleado"].value = vector[i+1];
				i=cantidad;
				pintatabla(wemp_pmla,combo,'3',wano,wmes,wqui);
			}
		 }
   }
}

function selectatras(vectorphp,wemp_pmla,wano,wmes,wqui)
{
   var vector = vectorphp.split("_v_");
   var combo = document.forms["nomina"].wempleado;
   var actual = combo.options[combo.selectedIndex].value;
   var cantidad = vector.length;

   for (i = 0; i < cantidad; i++)
   {
    if(i>0)
       {
		if (combo.value == vector[i])
			{

				document.forms["nomina"]["wempleado"].value = vector[i-1];
				i=cantidad;
				pintatabla(wemp_pmla,combo,'3',wano,wmes,wqui);
			}

    }

   }

}

function tomar_valor_actual(campo){
document.getElementById('valor_foco').value = campo.value;

}

function grabadato(vfecha,vhora,vano,vmes,vqui,vcco,vempleado,vnomcasilla,vnumero,vusuario,vemp_pmla,vvalor)
{

    var ccopersona= (document.getElementById('wempleado').value).split('-');
	  ccopersona=ccopersona[5];
	  var parametros = "";
    parametros = "consultaAjax=guardaValor&wfecha="+vfecha+"&whora="+vhora+"&wano="+vano+"&wmes="+vmes+"&wqui="+vqui+"&wcco="+ccopersona+"&wempleado="+vempleado+"&wnomcasilla="+vnomcasilla+"&wnumero="+vnumero+"&wusuario="+vusuario+"&wemp_pmla="+vemp_pmla+"&wvalor="+vvalor.value+"&wnombreobj="+vvalor.name;
	  //alert(parametros);

	    try
		  {
		    var ajax = nuevoAjax();
  			ajax.open("POST", "Nomina_horas.php",false);
  			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  			ajax.send(parametros);
		  }catch(e){ alert(e) }

   //si actualizan el area de texto no se hace la suma de los totales
   if (vvalor.name!='wobser' &&  vvalor.name!='checkborrar')
   {
       // se trae el valor nuevo
	   var cantidad = vvalor.value*1;
	   //se tiene el valor anterior
	   var valor_anterior = document.getElementById('valor_foco').value*1;
	   cantidad = cantidad - valor_anterior;
	   var div = vnomcasilla.split ('-');
	   var totalconcepto = (document.getElementById(div[0]).innerHTML)*1;

	   totalconcepto = totalconcepto + cantidad;
	   // se actualiza el valor del total en la tabla
	   document.getElementById(div[0]).innerHTML =(totalconcepto);
   }
   if(vvalor.name=='checkborrar')
   {
    radio1=3;
    segclave=true;
    document.location.href= 'Nomina_horas.php?wemp_pmla='+vemp_pmla+'&wempleado='+vempleado+'&radio1='+radio1+'&wano='+vano+'&wmes='+vmes+'&wqui='+vqui+'&segclave='+segclave;
   }
}

function pintatabla(wemp_pmla,ctl,radio1,wano,wmes,wqui)
{
   var wempleado = ctl.options[ctl.selectedIndex].value;
   segclave=true;
   document.location.href= 'Nomina_horas.php?wemp_pmla='+wemp_pmla+'&wempleado='+wempleado+'&radio1='+radio1+'&wano='+wano+'&wmes='+wmes+'&wqui='+wqui+'&segclave='+segclave;

}

// Función para iluminar toda la fila donde se ubique el mouse
function ilumina(celda,clase){
      if (celda_ant=="")
      {
         celda_ant = celda;
         celda_ant_clase = clase;
      }
      celda_ant.className = celda_ant_clase;
      celda.className = 'fondoAmarillo';
      celda_ant = celda;
      celda_ant_clase = clase;
  }


  // Función que Ilumina toda la columna donde se ubique el mouse
  function iluminacolumna(celda,columna)
  {
     $("td.fondoAmarillo").removeClass('fondoAmarillo');
     $("."+columna).addClass("fondoAmarillo");
  }

 // ********************************  FUNCION Sacar un mensaje de alerta con formato predeterminado  *************
function alerta(txt){
  $("#textoAlerta").text( txt );
  $.blockUI({ message: $('#msjAlerta') });
    setTimeout( function(){
             $.unblockUI();
          }, 1800 );
}

</script>
<style type="text/css">
    .submit {
      color:#2471A3;
      font-weight: bold;
      font-size: 12,75pt;
      width: 100px; height: 30px;
      background: rgb(240,248,252);
      background: -moz-linear-gradient(top,  rgba(240,248,252,1) 0%, rgba(236,246,254,1) 50%, rgba(219,238,251,1) 51%, rgba(220,237,254,1) 100%);
      background: -webkit-linear-gradient(top,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%);
      background: linear-gradient(to bottom,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%);
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f8fc', endColorstr='#dcedfe',GradientType=0 );
      border: 1px solid #ccc;
          border-radius: 8px;
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
    .button{
      color:#2471A3;
      font-weight: bold;
      font-size: 12,75pt;
      width: 100px; height: 30px;
      background: rgb(240,248,252);
      background: -moz-linear-gradient(top,  rgba(240,248,252,1) 0%, rgba(236,246,254,1) 50%, rgba(219,238,251,1) 51%, rgba(220,237,254,1) 100%);
      background: -webkit-linear-gradient(top,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%);
      background: linear-gradient(to bottom,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%);
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f8fc', endColorstr='#dcedfe',GradientType=0 );
      border: 1px solid #ccc;
          border-radius: 8px;
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
</style>

<?php
}
/*****************************************************************************************************************************************
Programa    : Programa para Reportar las horas extras trabajadas por los empleados
Descripción : Se relaciona un encargado con un empleado, del empleado se extrae el centro de costos de esta manera se accede
              a reportar las horas en todo el centro de costo . Las horas se reportan por quincenas y son abiertas o cerradas
              por el administrador del sistema  Rephor

*   MODIFICACIONES
*
* 2017-11-17 - Arleyda Insignares C.
*              Se Modifica consulta de empleados para seleccionarlos de la tabla rephor_000006, en caso de que el coordinador
*              tenga acceso por empleado.
*
* 2017-10-03 - Arleyda Insignares C.
*              Se adiciona clase para iluminar fila y columna, en el momento en que se habilita la planilla para ingresar las horas extras.
*
* 2017-05-16 - Arleyda Insignares C.
*              Se modifica el query de conceptos con consulta multiempresa (utilizando wemp_pmla).
*
* 2017-05-02 - Arleyda Insignares C.
*              Se cambia ODBC para el nuevo programa de nomina 'SQL Software'.
*
* 2016-07-21 - Arleyda Insignares C.
*              Se coloca una segunda clave para permitir el ingreso, en caso de que el usuario pertenezca a Nomina pero no tenga la clave, podrá
*              diligenciarla por primera vez, o cambiarla.
*
* Felipe Alvarez (2013-02-27)
*              Se cambio que el campo Carne_nomina funcione no con un carne de un usuario sino con Centros de costos , pueden ir varios al tiempo separados
*              por coma, asi puede un usuario reportar a varios centros de costos

/*****************************************************************************************************************************************/



include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");



// ********************************************      FUNCIONES PHP         ************************************************************* //

// Validar que el usuario exista en Nomina para permitirle cambiar o crear la segunda clave

if (isset($_POST["accion"]) && $_POST["accion"] == "crearClave"){

    $wbasedato    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
    $wrespuesta ='';

    $q = " SELECT Usuario_matrix,Carne_nomina,Clave_nomina "
       ."   FROM ".$wbasedato."_000001 "
       ."   WHERE Usuario_matrix = '".$codigo_usu."' ";

    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0){
        $row      = mysql_fetch_assoc($res);
        $clavebd  = $row['Clave_nomina'];

        if (strlen($row['Clave_nomina'])>1)
        {
          if ( $clavebd == $clave_ante )
          {
            $q=" UPDATE ".$wbasedato."_000001
               SET Clave_nomina = '".$clave_usu1."'
               WHERE Usuario_matrix = '".$codigo_usu."'";

            $resp = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            if ($resp > 0)
               {$wrespuesta = 'M';}
          }
         else
          {$wrespuesta='A';}
        }
        else // Crear 2da clave por primera vez
        {
           $q=" UPDATE ".$wbasedato."_000001
              SET Clave_nomina = '".$clave_usu1."'
              WHERE Usuario_matrix = '".$codigo_usu."' ";

           $resp = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
           if ($resp > 0)
              {$wrespuesta = 'I';}
        }
    }
    else{
        $wrespuesta = 'N';
    }
    echo $wrespuesta;
    return;
}

// Validar la Clave del Usuario en Reporte de Horas
if (isset($_POST["accion"]) && $_POST["accion"] == "validarUsuario"){

    $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
    $wusuario  = '';

    $q = " SELECT Usuario_matrix,Carne_nomina,Clave_nomina "
       ."   FROM ".$wbasedato."_000001 "
       ."   WHERE Usuario_matrix = '".$codigo_usu."' ";

    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);
    if ($num > 0){
        $row       = mysql_fetch_assoc($res);
        if ($clave_usu == $row['Clave_nomina'])
        { $validar = 'S'; }
        else
        { $validar = 'N'; }
        $wusuario  = $row['Usuario_matrix']."-".$row['Carne_nomina']."-".$validar;
    }

    echo $wusuario;
    return;
}

function grabar_dato($wfecha,$whora,$wano,$wmes,$wqui,$wcco,$wempleado,$wnomcasilla,$wnumero,$wusuario,$wemp_pmla,$conex,$wvalor)
{
  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
  if($wvalor > 0)
  {

    $q ="SELECT COUNT(*) FROM ".$wbasedato."_000003 "
    . " WHERE empleado = '".$wempleado."' "
    . "   AND ano='".$wano."' "
    . "   AND mes= '".$wmes."' "
    . "   AND quincena= ".$wqui." "
    . "   AND Tipo_hora_dia= '".$wnomcasilla."' ";


   $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $row = mysql_fetch_array($res2);
   if($row[0]==0)
   {

     $q =" INSERT INTO ".$wbasedato."_000003 (Medico  , Fecha_data,   Hora_data,   ano     ,   mes,        quincena,   cco     ,   empleado   ,   Tipo_hora_dia ,  cantidad, Seguridad) "
              ."VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$wano."','".$wmes."','".$wqui."','".$wcco."','".$wempleado."','".$wnomcasilla."',".number_format($wvalor,1,'.', '').", 'C-".$wusuario."')";



     $res2 = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   }
  else
   {
     $q = "UPDATE ".$wbasedato."_000003 "
        . "   SET cantidad= ".number_format($wvalor,1,'.', '')."  "
        . " WHERE empleado = '".$wempleado."' "
        . "   AND ano='".$wano."' "
        . "   AND mes= '".$wmes."' "
        . "   AND quincena= ".$wqui." "
        . "   AND Tipo_hora_dia= '".trim($wnomcasilla)."' ";


     $res2 = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   }

  }
  else
  {

    $q ="SELECT COUNT(*) FROM ".$wbasedato."_000003 "
    . " WHERE empleado = '".$wempleado."' "
    . "   AND ano='".$wano."' "
    . "   AND mes= '".$wmes."' "
    . "   AND quincena= ".$wqui." "
    . "   AND Tipo_hora_dia= '".$wnomcasilla."' ";


   $res2 = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $row = mysql_fetch_array($res2);

   if($row[0]!=0)
   {
	   $q ="DELETE FROM ".$wbasedato."_000003 "
		. " WHERE empleado = '".$wempleado."' "
		. "   AND ano='".$wano."' "
		. "   AND mes= '".$wmes."' "
		. "   AND quincena= ".$wqui." "
		. "   AND Tipo_hora_dia= '".$wnomcasilla."' ";


	   $res2 = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   }
  else
   {

   }
  }
}

function grabar_obser ($wano,$wmes,$wqui,$wempleado,$wemp_pmla,$wfecha,$whora,$wcco,$wvalor,$wusuario,$conex)
{
      $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
	  if($wvalor!='')
	  {

		  $q= " DELETE FROM ".$wbasedato."_000005 "
				 ."   WHERE ano           = '".$wano."'"
				 ."     AND mes           = '".$wmes."'"
				 ."     AND quincena      = '".$wqui."'"
				 ."     AND empleado      = '".$wempleado."'";
		  $res2 = mysql_query($q,$conex);

		   $q=    "  INSERT INTO ".$wbasedato."_000005 (Medico  ,   Fecha_data,   Hora_data,   Ano     ,   Mes,        Quincena,   Cco     ,   Empleado   ,   Observacion  ,    Seguridad) "
				  ."      VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$wano."','".$wmes."','".$wqui."','".$wcco."','".$wempleado."','".$wvalor.   "',"."'C-".$wusuario."')";
		   $res2 = mysql_query($q,$conex);
      }
	  else
	  {
	      $q= " DELETE FROM ".$wbasedato."_000005 "
				 ."   WHERE ano           = '".$wano."'"
				 ."     AND mes           = '".$wmes."'"
				 ."     AND quincena      = '".$wqui."'"
				 ."     AND empleado      = '".$wempleado."'";
		  $res2 = mysql_query($q,$conex);
	  }
}
// ******* FUNCIONES**********************************//
  /***************************************************
   *	          IMPRIMIR LAS LECTURAS               *
   *	  REALIZADAS EN LA UNIDAD DE IMAGINOLOGIA	  *
   *				CONEX, FREE => OK				  *
   ***************************************************/
session_start();

if (!isset($user))
	{
		if(!isset($_SESSION['user']))
			session_register("user");
	}

if(!isset($_SESSION['user']))
	echo "error";
else
{


  $wbasedato    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
  $wbasetalhuma = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
  $wtitulo1     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ley1846nomina1');
  $wtitulo2     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ley1846nomina2');




  // if ($conexunix == FALSE)
  //    echo "Fallo la conexión UNIX";

	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="2017-11-17";                                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  global $wempleado;

  if(!isset($consultaAjax)){

  $titulo = "REPORTE DE RECARGOS Y HORAS EXTRAS DEL PERSONAL";
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");

  //$wemp_pmla='02';

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

if (!isset($segclave))
{
    echo "<br>";
    echo "<br>";
    echo "<div id='msjacceso' align='center'>";
    echo "<table>";
    echo "<tr height='40'><td align=center class=encabezadoTabla colspan=5 ><font><b>ACCESO</b></font></td></tr>";
    echo "<tr class='fila1'><td width='300' height='40'><b>Favor Digitar Segunda Clave </b></td><td width='100'><input type='password' id='txtsdaclave' name='txtsdaclave' maxlength='10'></td></tr> ";
    echo "<tr class='fila1'><td align=center colspan=2 ><input type='button' class='button' value='Ingresar' onclick='ValidarClave(\"".$wusuario."\");'>&nbsp;&nbsp;<input type='button' class='button' name='Salir' value='Salir' onclick='window.close();'></td></tr>";
    echo "</table>";
    echo "<input type='HIDDEN' id='wemp_pmla' name= 'wemp_pmla' value='".$wemp_pmla."'>";
    echo "<br>";
    echo "<table>";
    echo "<tr class='fila1'><td width='350' height='40'><b> Si no posee Segunda clave o desea cambiarla ingrese aqui </b></td><td width='100'><input type='button' class='button' id='btncrearclave' name='btncrearclave' value='Clave'></td></tr> ";
    echo "</table>";
    echo "</div>";
    echo "<div id='pancrearclave' align='center' style='display:none;'>";
    echo "<table>";
    echo "<tr><td align=center class=encabezadoTabla colspan=5 ><font><b>CREAR SEGUNDA CLAVE</b></font></td></tr>";
    echo "<tr class='fila1'><td width='300' height='40'><b>Favor Digitar Clave anterior </b></td><td width='100'><input type='password' id='txtclaveanterior' name='txtclaveanterior' maxlength='10'></td></tr> ";
    echo "<tr class='fila1'><td width='300' height='40'><b>Favor Digitar Segunda Clave </b></td><td width='100'><input type='password' id='txtsdaclave1' name='txtsdaclave1' maxlength='10'></td></tr> ";
    echo "<tr class='fila1'><td width='300' height='40'><b>Ingrese nuevamente Segunda Clave </b></td><td width='100'><input type='password' id='txtsdaclave2' name='txtsdaclave2' maxlength='10'></td></tr> ";
    echo "<tr class='fila1'><td align=center colspan=2 ><input type='submit' id='btningresar' name='btningresar' value='GRABAR' onclick='GrabarsdaClave(\"".$wusuario."\");'></td></tr> ";
    echo "</table>";
    echo "<div id='msjAlerta' style='display:none;'> ";
    echo "<br><img src='../../images/medical/root/Advertencia.png'/>";
    echo "<br><br><div id='textoAlerta'></div><br><br>";
    echo "</div>";
    echo "</div>";
}
else
{
  echo "<br>";
  echo "<br>";
  echo "<div id='msjAlerta' style='display:none;'> ";
  echo "<br><img src='../../images/medical/root/Advertencia.png'/>";
  echo "<br><br><div id='textoAlerta'></div><br><br>";
  echo "</div>";

  echo "<form action='Nomina_horas.php' id='nomina' method=post>";
  echo "<center><tr class=fila1><td align=center ><font size='3' color='red'><div class='blink'><b>! ATENCION !</b></div></font></td></tr></center>";
  echo "<table border=1><tr><td><font size='2'>".$wtitulo1."</font></td></tr>";
  echo "<tr><td><font size='2'>".$wtitulo2."</font></td></tr></table>";
  echo "<br>";
  echo "<br>";
  echo "<table align=left><tr><td align=left ><font size=2><A href=Nomina_horas.php?wemp_pmla=".$wemp_pmla."&segclave=true>Retornar</A></font></td><td>&nbsp;&nbsp</td><td align=left><font size=2><A href='javascript:onClick=window.close();' target='_top' >Cerrar</A></font></td></tr></table>";

  echo "<input type='HIDDEN' id='wemp_pmla' name= 'wemp_pmla' value='".$wemp_pmla."'>";
  echo "<br>";
  echo "<center><table>";
  echo "<tr><td align=center class=encabezadoTabla colspan=5 ><font><b>REPORTE DE RECARGOS Y HORAS EXTRAS DEL PERSONAL</b></font></td></tr>";
  $wactualiz;
  $wfecha_actual = date("Y-m-d");
  $wano_actual   = date("Y",strtotime($wfecha_actual));
  $wmes_actual   = date("m",strtotime($wfecha_actual));

  if(!isset($wano) or !isset($wmes) or !isset($wqui))
    {
	   //AÑO
     echo "<tr class=Fila1><td><font ><b>A&ntilde;o:</b></font><select id='wano' name='wano'>";
     for($f=2004;$f<2051;$f++)
     {
        if($f == $wano_actual)
           echo "<option selected>".$f."</option>";
        else
           echo "<option>".$f."</option>";
     }
	   echo "</select>";

	   //MES
     echo "<td><font><b>Mes :</b></font><select id='wmes' name='wmes'>";
     for($f=1;$f<13;$f++)
     {
        if($f == $wmes_actual)
          if($f < 10)
              echo "<option selected>0".$f."</option>";
           else
              echo "<option selected>".$f."</option>";
	      else
	         if($f < 10)
	            echo "<option>0".$f."</option>";
	         else
	            echo "<option>".$f."</option>";
	   }
	   echo "</select>";

     //QUINCENA
     echo "<td><font ><b>Quincena :</b></font><select id='wqui' name='wqui'>";
     for($f=1;$f<3;$f++)
     {
        echo "<option>".$f."</option>";
     }
	   echo "</td></select></td></tr>";

     echo"<tr class=Fila2><td align=center colspan=3 ><input type='button' class='button' value='ACEPTAR' onclick='ingresar();'></td></tr></form>";
    }
   else
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Ya estan todos los campos setiados o iniciados ===================================================================================
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      {
	     //AÑO

       echo "<tr class=Fila1><td align=center class=Fila1><b>A&ntilde;o: </b>".$wano."</td>";
       echo "<input type='HIDDEN' name= 'wano' value='".$wano."'>";

	     //MES
       echo "<td align=center ><b>Mes: </b>".$wmes."</td>";
       echo "<input type='HIDDEN' name= 'wmes' value='".$wmes."'>";

       //QUINCENA
       echo "<td align=center ><b>Quincena: </b>".$wqui."</td>";
       echo "<input type='HIDDEN' name= 'wqui' value='".$wqui."'></tr>";

	     //EMPLEADO
	       $q= "    SELECT count(*) AS can ";
         $q= $q."   FROM ".$wbasedato."_000002 ";
         $q= $q."  WHERE ano      = '".$wano."'";
         $q= $q."    AND mes      = '".$wmes."'";
         $q= $q."    AND quincena = '".$wqui."'";
         $q= $q."    AND cerrado  = 'off' ";

         $res = mysql_query($q,$conex);
         $row = mysql_fetch_array($res);

    		 $pos = strpos($user,"-");
    		 $wusuario = substr($user,$pos+1,strlen($user));

         if ($row[0] >= 1 or $wusuario=="rephor")
         {   // if quincena abierta MATRIX

         //Aca selecciono los empleados que pertenecen al centro de costo de acuerdo al centro de costo que tiene asignado el usuario
			   //autorizado para ingresar a este proceso.

			   //cambiar por ahora se va a manejar una sola tabla de rephor_000001
			   $q  = "  SELECT Carne_nomina ";
			   $q .= "  FROM ".$wbasedato."_000001 ";
			   $q .= "  WHERE Usuario_matrix = '".$wusuario."'";

			   $res = mysql_query($q,$conex);
         $rown = mysql_fetch_array($res);

          if ($rown[0] <> "" )   //Si es diferente de null, es porque el usuario esta autorizado a ingresar al proceso
	        {

				   // en el campo Carne_nomina viene los centros  de costos  separados por coma en los que el usuario puede reportar
				   $wcco = $rown[0];
			     $arr_cco = explode(",",$wcco);
				   $nwcco= "('".implode("','",$arr_cco)."')";

           /* Verificar la tabla rephor_000006 para saber si tiene excepción para registro en el reporte de horas
              En caso contrario debe seleccionar de la tabla noper de SQL Software */

           $q1  = "  SELECT Repemp ";
           $q1 .= "  FROM ".$wbasedato."_000006 ";
           $q1 .= "  WHERE substr(Repcoo,1,5) = '".$wusuario."' ";

           $res1 = mysql_query($q1,$conex);
           $num  = mysql_num_rows($res1);

           if ($num > 0 )
           {

                $query = " SELECT  A.Ideuse, A.Ideno1, A.Ideno2, A.Ideap1, A.Ideap2, A.Idecco
                          From  ".$wbasetalhuma."_000013 A
                          Inner  join  ".$wbasedato."_000006 B
                                 on A.Ideuse = B.Repemp
                          Where substr(B.Repcoo,1,5)  = '".$wusuario."' ";

                $res2 = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
                $res3 = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
                $k=0;

                while($row = mysql_fetch_array($res2))
                {
                      $vector[$k]="".trim(substr($row[0],0,5))."-".trim($row[1])."-".trim($row[2])."-".trim($row[3])."-".trim($row[4])."-".trim($row[5])."";
                      $k++;
                }

                if (count($vector) != 0)
                {

                   $vec = implode("_v_" ,$vector);

                   echo "<tr class=Fila2><td></td><td><font><b>Empleado: </b><input type='button' class='button' value='<' onclick='selectatras(\"".$vec."\",\"".$wemp_pmla."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\")' ></font><SELECT name='wempleado' id='wempleado' onchange='pintatabla(\"".$wemp_pmla."\",this,3,\"".$wano."\",\"".$wmes."\",\"".$wqui."\")'>";
                   echo "<option value= ------</option>";

                   while($row = mysql_fetch_array($res3))
                   {

                         if (isset($wempleado))
                         {
                             $pos = strpos($wempleado,"-");
                             $wcodemp = substr($wempleado,0,$pos);
                             $wemple= substr($row[0],0,5);

                             if (trim($wcodemp) == trim($wemple))
                                 echo "<option value ='".trim(substr($row[0],0,5))."-".trim($row[1])."-".trim($row[2])."-".trim($row[3])."-".trim($row[4])."-".trim($row[5])."' selected>".$row[1]." ".$row[2]." ".$row[3]." ".$row[4]."-".$row[0]."</option>";
                             else
                                 echo "<option value ='".trim(substr($row[0],0,5))."-".trim($row[1])."-".trim($row[2])."-".trim($row[3])."-".trim($row[4])."-".trim($row[5])."' >".$row[1]." ".$row[2]." ".$row[3]." ".$row[4]."-".$row[0]."</option>";
                         }
                         else
                             echo "<option value ='".trim(substr($row[0],0,5))."-".trim($row[1])."-".trim($row[2])."-".trim($row[3])."-".trim($row[4])."-".trim($row[5])."' >".$row[1]." ".$row[2]." ".$row[3]." ".$row[4]."-".$row[0]."</option>";

                   }
                   echo "</SELECT><input type='button' class='button' value='>' onclick='selectadelante(\"".$vec."\",\"".$wemp_pmla."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\")'></td><td></td></tr>";
                }else
                {
                    echo "<tr class=Fila2><td colspan='3' align='center'><b><blink>El centro de costos (".$wcco." ) no tiene empleados asignados</blink></b></td></tr> ";
                }
           } // Fin traer empleados de talhuma_000013 y rephor_000006
           else{

              //Traigo los nombres de los empleados de la tabla noper según centro de costos seleccionado
              $query  = " SELECT percod, perno1, perno2, perap1, perap2 ,percco";
              $query .= "   FROM noper ";
              $query .= "  WHERE peretr = 'A' ";
              $query .= "    AND percco IN ".$nwcco." ";
              $query .= "  ORDER BY percco,perno1, perno2,perap1,perap2 ";


			  $wodbc        = consultarAliasPorAplicacion($conex, $wemp_pmla, 'q7_odbc_nomina');
			  $conexunix  = odbc_connect($wodbc,'','') or die("No se realizo Conexion con el Unix");

              $res = odbc_do($conexunix,$query);
              $res2 = odbc_do($conexunix,$query);
  				    $k=0;
  				    while(odbc_fetch_row($res2))
  	          {

  							   $vector[$k]="".trim(odbc_result($res2,1))."-".trim(odbc_result($res2,2))."-".trim(odbc_result($res2,3))."-".trim(odbc_result($res2,4))."-".trim(odbc_result($res2,5))."-".trim(odbc_result($res2,6))."";
  							   $k++;


  		        }

    					if (count($vector) != 0)
    					{
    						   $vec = implode("_v_" ,$vector);

    						   echo "<tr class=Fila2><td></td><td><font><b>Empleado: </b><input type='button' class='button' value='<' onclick='selectatras(\"".$vec."\",\"".$wemp_pmla."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\")' ></font><SELECT name='wempleado' id='wempleado' onchange='pintatabla(\"".$wemp_pmla."\",this,3,\"".$wano."\",\"".$wmes."\",\"".$wqui."\")'>";
    						   echo "<option value= ------</option>";

    						   while(odbc_fetch_row($res))
    						   {

    							 if (isset($wempleado))
    							 {
    								   $pos = strpos($wempleado,"-");
    								   $wcodemp = substr($wempleado,0,$pos);
    								   $wemple= odbc_result($res,1);

    								   if (trim($wcodemp) == trim($wemple))
    									     echo "<option value ='".trim(odbc_result($res,1))."-".trim(odbc_result($res,2))."-".trim(odbc_result($res,3))."-".trim(odbc_result($res,4))."-".trim(odbc_result($res,5))."-".trim(odbc_result($res,6))."' selected>".odbc_result($res,2)." ".odbc_result($res,3)." ".odbc_result($res,4)." ".odbc_result($res,5)."-".odbc_result($res,1)."</option>";
    								   else
    									     echo "<option value ='".trim(odbc_result($res,1))."-".trim(odbc_result($res,2))."-".trim(odbc_result($res,3))."-".trim(odbc_result($res,4))."-".trim(odbc_result($res,5))."-".trim(odbc_result($res,6))."' >".odbc_result($res,2)." ".odbc_result($res,3)." ".odbc_result($res,4)." ".odbc_result($res,5)."-".odbc_result($res,1)."</option>";
    							 }
    						   else
    								   echo "<option value ='".trim(odbc_result($res,1))."-".trim(odbc_result($res,2))."-".trim(odbc_result($res,3))."-".trim(odbc_result($res,4))."-".trim(odbc_result($res,5))."-".trim(odbc_result($res,6))."' >".odbc_result($res,2)." ".odbc_result($res,3)." ".odbc_result($res,4)." ".odbc_result($res,5)."-".odbc_result($res,1)."</option>";

    						   }
    				   	   echo "</SELECT><input type='button' class='button' value='>' onclick='selectadelante(\"".$vec."\",\"".$wemp_pmla."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\")'></td><td></td></tr>";
    					}else
    					{
    							echo "<tr class=Fila2><td colspan='3' align='center'><b><blink>El centro de costos (".$wcco." ) no tiene empleados asignados</blink></b></td></tr> ";
    					}

						odbc_close($conexunix);
						odbc_close_all();

            }//Fin else, tabla noper

					echo "</table><br><br>";

	               ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	              if ($wqui == "1")
	                  if ($wmes == '01')          //Si es Enero entonces el año cambia y el mes anterior es 12
	                  {
	                      $wanofec = ($wano-1);
	                      $wmesfec = 12;
                    }
                    else                     //Si no es Enero, siempre me devuelvo un mes si es la primera quincena
                    {
	                      $wanofec = $wano;
	                      $wmesfec = $wmes-1;
                    }
                else                        //Para la segunda quincena nunca cambio ni el año ni el mes
                {
                    $wanofec = $wano;
                    $wmesfec = $wmes;
                }

               //Aca averiguo el ultimo dia del mes anterior al período digitado
		           $fecha = date("1-".($wmesfec)."-".$wanofec, (int)"%d-%m-%Y");
		           $q   ="SELECT dayofmonth(last_day(str_to_date('".$fecha."','%d-%m-%Y')))";
		           $err = mysql_query($q,$conex);
		           $row = mysql_fetch_array($err);
		           $wultdia = $row[0];

		           //Para la primera quincena
		           if ($wqui == "2")
		           {
		               $wultdia = 15;
		               $k=1;
	             }
	             else //Para la segunda quincena se inicia k=16 y wultdia se deja como venia
	                 $k=16;

	             echo "<center><table>";

		           //Aca coloco los numeros de los dias en la fila de la tabla
		           echo "<tr class=encabezadoTabla>";

		           echo "<td >C&oacute;digo</td>";
		           echo "<td >Descripci&oacute;n</td>";
               $numcol = 0;


		           for ($j=$k;$j<=$wultdia;$j++)
		           {
			            ////////////////////////////////////////////////////////////////////////////////

      						$fecha  = date($j."-".$wmesfec."-".$wanofec,(int)"%d-%m-%Y");
                  $q      = "SELECT DAYNAME(str_to_date('".$fecha."','%d-%m-%Y'))";
                  $err    = mysql_query($q,$conex);
                  $row    = mysql_fetch_array($err);
      						$fecha2 = date($wanofec."-".$wmesfec."-".$j,(int)"%Y-%m-%d");
			            $wcf    = "encabezadoTabla";
                  $numcol ++;
                  $varcelcal   = 'columna'.$numcol;

			            if ($row[0] == "Sunday")        //Averiguo si el dia es dominical
			                $wcf ="fondoRojo";
			            else
			            {
				              //Averiguo si el dia es festivo
      							  $q="   SELECT count(*) "
      				          ."    FROM root_000063 "
      				          ."   WHERE Fecha='".$fecha2."'";

      				        $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      			          $row = mysql_fetch_array($err);

      			          if ($row[0] > 0)   //Si es dia festivo
        	            {
      								   $wcf ="fondoRojo";
      							  }

			            }
			            ////////////////////////////////////////////////////////////////////////////////

		   		        echo "<td align=center class='".$wcf." ".$varcelcal."' onclick ='iluminacolumna(this,\"".$varcelcal."\")'>".$j."</td>";
	   	         }
	   	         echo "<td class=encabezadoTabla>Total</td>";
		           echo "</tr>";

		           //Aca traigo todos los conceptos que existen
		           $q=" SELECT subcodigo, descripcion "
		            ."    FROM det_selecciones "
		            ."   WHERE lcase(medico) = 'rephor' "   //Nombre del usuario dueño de la seleccion
		            ."     AND codigo = '".$wemp_pmla."' "  //Codigo de la seleccion en la tabla det_selecciones
					      ."     AND activo = 'A' "
		            ."   ORDER BY subcodigo ";


		           $res = mysql_query($q,$conex);
		           $num = mysql_num_rows($res);

		           if ($num > 0)
		           {
			           $fecha = date("Y-m-d");
				         $hora = (string)date("H:i:s");

			           //Aca comienzo a crear la cuadricula de captura de horas
			           $sw=0;


			           for ($t=0;$t<$num;$t++)
			               {
			                $row = mysql_fetch_array($res);
			                $cod = $row[0];    //Codigo
			                $des = $row[1];    //Descripcion
                      $numcol = 0;
                      $t % 2 == 0 ? $fondo = "fila1" : $fondo = "fila2";

			                //Esto lo hago para intercambiar colores entre líneas

        							if (is_int ($t/2))
        							   {
        								$wcf="fila1";  // color de fondo de la fila
        							   }
        							else
        							   {
        								$wcf="fila2"; // color de fondo de la fila
        							   }
                        echo "<input type='hidden' name='valor_foco' id='valor_foco' value=''>";
		                    echo "<tr class=".$wcf." onclick='ilumina(this,\"".$fondo."\")'>";

		                    //===================================================================================================================================
		                    //Aca comienzo la evaluacion del radio buton, con el cual se determina la accion a seguir
		                    //===================================================================================================================================
		                    if (isset($radio1))
		                       {
		                        switch ($radio1)
		                           {
					                   //========================================================================================================================
					                   //CONSULTAR ==============================================================================================================
					                   case 3 :
					                      {
  						                    $totcod=0;

    							                echo "<td>".$cod."</td>";
    							                echo "<td>".$des."</td>";
    							                //Aca se crean los cuadros de captura de datos hasta final de la quincena
  						                    for ($j=$k;$j<=$wultdia;$j++)
  						                        {

  							                       $nomcasilla  = $cod."-".$j;
  							                       $$nomcasilla = 0;
                                       $numcol ++;
                                       $varcelcal   = 'columna'.$numcol;
  							                       if (isset($wempleado))  //Si selecciono ya el empleado
  							                       {
      							                         $q= "     SELECT Cantidad ";
      							                         $q= $q."    FROM ".$wbasedato."_000003 ";
      							                         $q= $q."   WHERE ano           = '".$wano."'";
      							                         $q= $q."     AND mes           = '".$wmes."'";
      				    			                     $q= $q."     AND quincena      = '".$wqui."'";
      					     		                     $q= $q."     AND empleado      = '".$wcodemp."'";
      						     	                     $q= $q."     AND Tipo_hora_dia = '".$nomcasilla."'";
      							                         $res1 = mysql_query($q,$conex);
      						                           $num1 = mysql_num_rows($res1);

      						                           if ($num1 > 0)
      						                           {
      							                             $row1 = mysql_fetch_array($res1);
      							                             $$nomcasilla=$row1[0];
      							                         }
  						                         } //fin del then si esta setiado wempleado

  						                         if (isset($$nomcasilla))
  						                         {
    				     		                        //Si el valor es cero, no lo muestre en la grilla
    					     		                      if ($$nomcasilla > 0)
    						     		                         echo "<td class='".$varcelcal."' onclick ='iluminacolumna(this,\"".$varcelcal."\")' ><INPUT TYPE='text' NAME='".$nomcasilla."' id='".$nomcasilla."' VALUE = ".$$nomcasilla."  SIZE = 1 maxlength=4  onblur='grabadato(\"".$fecha."\",\"".$hora."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\",\"".$wcco."\",\"".$wcodemp."\",\"".$nomcasilla."\",\"".number_format($$nomcasilla,1,'.', '')."\",\"".$wusuario."\",\"".$wemp_pmla."\",this)'  onFocus='tomar_valor_actual(this)'></td>";
    						     		                    else
    						   	    	                       echo "<td class='".$varcelcal."' onclick ='iluminacolumna(this,\"".$varcelcal."\")'><INPUT TYPE='text' NAME='".$nomcasilla."' id=".$nomcasilla." SIZE = 1 maxlength=4 onblur='grabadato(\"".$fecha."\",\"".$hora."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\",\"".$wcco."\",\"".$wcodemp."\",\"".$nomcasilla."\",\"".number_format($$nomcasilla,1,'.', '')."\",\"".$wusuario."\",\"".$wemp_pmla."\",this)'  onFocus='tomar_valor_actual(this)'></td>";

    						   		                      if (isset($nomcasilla) and ($$nomcasilla > 0))
  				                                  {
  						   		                            $totcod=$totcod+$$nomcasilla;
  						   		                        }
  				   		                        }
  				   		                        else
  				   		                            echo "<td class='".$varcelcal."' onclick ='iluminacolumna(this,\"".$varcelcal."\")'><INPUT TYPE='text' NAME='".$nomcasilla."' id='".$nomcasilla."' SIZE = 1 maxlength=4 onblur='grabadato(\"".$fecha."\",\"".$hora."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\",\"".$wcco."\",\"".$wcodemp."\",\"".$nomcasilla."\",\"".number_format($$nomcasilla,1,'.', '')."\",\"".$wusuario."\",\"".$wemp_pmla."\",this)'  onFocus='tomar_valor_actual(this)'></td>";
  					   		                    }
                                      $nombrediv=explode('-',$nomcasilla);

					   		                echo "<td><div id=".$nombrediv[0]." name=".$nombrediv[0].">$totcod</div></td>";
						                    echo "</tr>";

						                    break;
					                      } // Fin del case 3 // Consultar
					                   //========================================================================================================================

		                           } //Fin del switch Radio1
                             } //fin then del if isset(radio1)
		                   } //fin del for

		                   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		                   /// ACA HAGO TODO LO RELACIONADO CON EL CAMPO DE OBSERVACIONES, tambien evaluo el radio button para este campo ////////////////////////
		                   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		                   if (isset($radio1))
		                       {
		                        switch ($radio1)
		                           {
		                               //========================================================================================================================
					                   //CONSULTAR ==============================================================================================================
		                            case 3 :
					                      {
    						                   //======= ACA TRAIGO LO QUE ESTE GRABADO PARA EL EMPLEADO EN OBSERVACIONES =============================================
          									       $q= "     SELECT Observacion ";
          									       $q= $q."    FROM ".$wbasedato."_000005 ";
          									       $q= $q."   WHERE ano           = '".$wano."'";
          									       $q= $q."     AND mes           = '".$wmes."'";
          									       $q= $q."     AND quincena      = '".$wqui."'";
          									       $q= $q."     AND empleado      = '".$wcodemp."'";

          									       $res1 = mysql_query($q,$conex);
          									       $num1 = mysql_num_rows($res1);

          									       if ($num1 > 0)
          									       {
          									           $row1 = mysql_fetch_array($res1);
          									           $wobser=$row1[0];
          									       }
          									       else
          									           $wobser="";

        									        if (isset($wobser))
        									        {
        										          echo "<tr><td ALIGN=CENTER colspan=2 class=Fila2><B>OBSERVACIONES : </B></td>";
        				                              echo "<td colspan=16 ><CENTER><TEXTAREA NAME='wobser' id='wobser' ROWS='3' COLS='75' onblur='grabadato(\"".$fecha."\",\"".$hora."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\",\"".$wcco."\",\"".$wcodemp."\",\"".$nomcasilla."\",\"".number_format($$nomcasilla,1,'.', '')."\",\"".$wusuario."\",\"".$wemp_pmla."\",this)'>".$wobser."</TEXTAREA></td></tr>";
        				                              //echo "<input type='HIDDEN' name= 'wobser' value='".$wobser."'>";
        									        }
        									        else
        									        {
        									            echo "<tr><td ALIGN=RIGHT colspan=2 bgcolor=".$color."><B>OBSERVACIONES : </B></td>";
        				                                echo "<td colspan=16 bgcolor=".$color."><CENTER><TEXTAREA NAME='wobser' id='wobser' ROWS='3' COLS='75' onblur='grabadato(\"".$fecha."\",\"".$hora."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\",\"".$wcco."\",\"".$wcodemp."\",\"".$nomcasilla."\",\"".number_format($$nomcasilla,1,'.', '')."\",\"".$wusuario."\",\"".$wemp_pmla."\",this)'></TEXTAREA></td></tr>";
        				                  }
				                          break;
			                            }
			                           //========================================================================================================================

				   		         }
			   		           }
		               }    // fin del then de $num > 0

	               ////////////////////////////////////////////////////
	               if (isset($radio1))
		             {
	                  switch ($radio1)
	                     {
	                      case 2 :
	                       {
		                    echo "<tr><td  colspan=".($wultdia+3)."><CENTER>** REGISTRO ACTUALIZADO **</td></tr>";
		                    break;
	                       }
	                      case 3 :
	                       {
		                    echo "<tr><td colspan=".($wultdia+3)."><CENTER></td></tr>";
		                    break;
	                       }
	                      case 4 :
	                       {
		                    echo "<tr><td  colspan=".($wultdia+3)."><CENTER>** REGISTRO ELIMINADO **</tr>";
		                    break;
	                       }
                         }
                     }
                   ////////////////////////////////////////////////////
                   echo "<tr><td colspan=".($wultdia+2)."><CENTER>";

                   echo "<B>";
                    echo "<INPUT CENTER TYPE = 'checkbox' NAME = 'checkborrar'  id='checkborrar'  onclick=' if(confirm(\"¿Desea eliminar todos los registros de la quincena asociados a este empleado?\")){ grabadato(\"".$fecha."\",\"".$hora."\",\"".$wano."\",\"".$wmes."\",\"".$wqui."\",\"".$wcco."\",\"".$wempleado."\",\"1\",\"1\",\"".$wusuario."\",\"".$wemp_pmla."\",this)}else{this.checked=false}'> Borrar";
                   echo "</B></td></tr>";

		           echo "<table><tr><td><font size=3><A href=/matrix/HORAS/reportes/000003_rh01.php?wemp_pmla=".$wemp_pmla."> &nbsp;&nbsp;&nbsp;Imprimir Detallado &nbsp;&nbsp;&nbsp; </A></font></td>";
		           echo "<td><font size=3><A href=/matrix/HORAS/reportes/000003_rh02.php?wemp_pmla=".$wemp_pmla."> &nbsp;&nbsp;&nbsp;  Imprimir Resumido &nbsp;&nbsp;&nbsp; </A></font></td></tr>";
	              }  //Fin del then del if del usuario autorizado a entrar
		        else // else del usuario autorizado
		           echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>EL USUARIO NO ESTA AUTORIZADO PARA INGRESAR A ESTE PROCESO</font></b></TD></TR></TABLE>";
	          }  // Fin del then quincena abierta en MATRIX
	         else
                echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>LA QUINCENA DIGITADA YA ESTA CERRADA EN MATRIX o NO EXISTE </font></b></TD></TR></TABLE>";
     ///   }     // Fin del then quincena abierta en UNIX
	   ///  else  // else del if quincena abierta en UNIX
	   ///     echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>LA QUINCENA DIGITADA YA ESTA CERRADA EN NOMINA o NO EXISTE</font></b></TD></TR></TABLE>";
      } // else de todos los campos setiados
	   }
    }
	  else
	  {





	  if($wnombreobj=='wobser')
	  {
	    grabar_obser($wano,$wmes,$wqui,$wempleado,$wemp_pmla,$wfecha,$whora,$wcco,$wvalor,$wusuario,$conex);
	  }
	  if ($wnombreobj=='checkborrar')
	  {

		$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
		$codemp=  explode('-',$wempleado);

		$q ="DELETE FROM ".$wbasedato."_000003 "
		  . " WHERE empleado = '".$codemp[0]."' "
		  . "   AND ano='".$wano."' "
		  . "   AND mes= '".$wmes."' "
		  . "   AND quincena= ".$wqui." ";

		$res2 = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  }
	  if($wnombreobj!='wobser' && $wnombreobj!='checkborrar' )
	  {
		  grabar_dato($wfecha,$whora,$wano,$wmes,$wqui,$wcco,$wempleado,$wnomcasilla,$wnumero,$wusuario,$wemp_pmla,$conex,$wvalor);
	  }

	  }


} // if de register

unset($wano);
unset($wmes);
unset($wqui);
unset($wempleado);

if(!isset($consultaAjax)) {
echo "<br><br><br><br>";

}

?>
