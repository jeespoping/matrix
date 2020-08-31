<?php
include_once("conex.php");
   /*******************************************************************************************************************
   *                                 FACTURACION ESTUDIOS DIRECCION CIENTIFICA                                        *
   *                                                                                                                  *
   **************************************        DESCRIPCIÓN       ****************************************************
   * Consultar la Facturación generada por Rango de Fecha, Número de factura, Paciente, Laboratorio, Estudio, Concepto
   * y facturas vencidas, mostrando fecha probable y estado de la factura. El programa permite registrar el pago de la
   * factura (pago único) y anularla.
   ********************************************************************************************************************
   * Autor: Arleyda Insignares C.                                                                                     *
   * Fecha creacion: 2017-02-20                                                                                       *
   *                                                                                                                  *
   *********************************************** Modificaciones *****************************************************
   * 2017-06-09 - Arleyda Insignares Ceballos. Se adicionan decimales
   ********************************************************************************************************************/
 
   $wactualiz = "2017-06-09";
   if(!isset($_SESSION['user'])){
     echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
      <tr><td>Error, inicie nuevamente</td></tr>
      </table></center>";
     return;
   }

   header('Content-type: text/html;charset=ISO-8859-1');

  //********************************** Inicio  ***********************************************************
  
  

  include_once("root/comun.php");

  $wbasedato  = consultarAliasPorAplicacion($conex, $wemp_pmla, "dircie");
  $wfecha     = date("Y-m-d");
  $whora      = (string)date("H:i:s");
  $pos        = strpos($user,"-");
  $wusuario   = substr($user,$pos+1,strlen($user));

  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************

     //Función Ajax para mostrar el detalle de la factura
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarDetalle"){

        $q  = "  SELECT Mopcol, Mopcoe, Mopcoc, Moptid, Mopide, Mopfac,Mopvfr, Mopcan, Labnom, Condes, 
                        Lecval, concat (E.Pacno1,' ',E.Pacno2,' ',E.Pacap1,' ',E.Pacap2) as Nompac        
                 From ".$wbasedato."_000008 A
                 Inner Join ".$wbasedato."_000001 B on  A.Mopcol = B.Labnit
                 Inner Join ".$wbasedato."_000003 C on  A.Mopcoc = C.Concod
                 Inner Join ".$wbasedato."_000005 D on  A.Mopcoc = D.Leccoc                 
                       AND A.Mopcol = D.Leccol AND A.Mopcoe=D.Leccoe
                 Inner Join ".$wbasedato."_000011 E on  A.Moptid = E.Pactid
                       AND A.Mopide = E.Pacced
                 Where trim(A.Mopfac) = '".trim($numfactura)."'
                       AND trim(A.Mopcol) = '".trim($labfactura)."' " ;

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        $vfila    = 0;
        $detalle  = '';
        $consulta = '';

        $detalle = "<table border='1' id='tbldetallefac' name='tbldetallefac' style='1400px'><thead><tr><td colspan='12' align='center'>Factura Numero ".$numfactura."</td></tr><tr class='encabezadoTabla'>                    
                    <td align='center' width='10%'> NIT LABORATORIO</td>
                    <td align='center' width='15%'> DESCRIPCION LABORATORIO</td>
                    <td align='center' width='5%'> ESTUDIO </td>
                    <td align='center' width='5%'> CODIGO CONCEPTO</td>
                    <td align='center' width='15%'> DESCRIPCION CONCEPTO</td>
                    <td align='center' width='5%'> TIPO IDE.</td>
                    <td align='center' width='5%'> IDENTIFICACION </td>
                    <td align='center' width='20%'> NOMBRE_PACIENTE </td>
                    <td align='center' width='5%'> FRECUENCIA </td>
                    <td align='center' width='5%'> CANT. </td>
                    <td align='center' width='5%'> VALOR <BR> UNITARIO</td>
                    <td align='center' width='5%'> VALOR <BR> TOTAL</td>
                    </tr>";
       
        if  ($num > 0)
        {

            // Consultar todos los Pacientes asignados al Estudio
            while($row = mysql_fetch_assoc($res))
            { 
              
                  $vfila ++;

                  if (is_int ($vfila/2))
                      
                      $wcolor="fila1";
                  else
                      $wcolor="fila2";

                  $valortot = round($row['Mopcan']* $row['Lecval'],0); 

                  $detalle .= "<tr class='".$wcolor."' id=".$row['Facnum']."|".$row['Faccol']." ondblclick='CargarFactura(this)';>
                               <td align='center'>".$row['Mopcol']."</td>
                               <td align='center'>".$row['Labnom']."</td>
                               <td align='center'>".$row['Mopcoe']."</td>
                               <td align='center'>".$row['Mopcoc']."</td>
                               <td align='center'>".$row['Condes']."</td>
                               <td align='center'>".$row['Moptid']."</td>
                               <td align='center'>".$row['Mopide']."</td>
                               <td align='center'>".$row['Nompac']."</td>
                               <td align='center'>".$row['Mopvfr']."</td>
                               <td align='center'>".$row['Mopcan']."</td>
                               <td align='center'>".number_format($row['Lecval'],2,'.',',')."</td>
                               <td align='center'>".number_format($valortot,2,'.',',')."</td>
                             </tr>";

            }
            
            $detalle .= "</thead></table><br><center>
                        <input type='button' id='btnimprimir' name='btnimprimir' class='button' value='Imprimir' onclick='ImprimirModal()'>
                        <input type='button' id='btnconsultar' name='btnconsultar' class='button' value='Regresar' onclick='CerrarModal()'>
                        </center>";
         }
         else
           $detalle = "N"; 

         echo $detalle;
         return;

     }


     // Función ajax para registrar la factura como pagada con su respectiva fecha
     if (isset($_POST["accion"]) && $_POST["accion"] == "RegistrarPago"){

        $q = " UPDATE ".$wbasedato."_000014
               SET Facpag = 'on', Facfpa = '".$fecfactura."'
               WHERE Facnum = '".$numfactura."'
                 AND Faccol = '".$labfactura."' "; 

        $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        echo $resp;
        return;

     }


     //Consulta principal para mostrar las facturas según filtros seleccionados
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarFactura"){

        $condicion = '';
        $condigral = '';
        $ingreso   = 0;
        
        // Filtrar por Laboratorio
        if ($wlaborato != ''){
            
            if ($ingreso > 0)
                $condicion .= " AND ";    
            $condicion .= "Faccol ='".$wlaborato."'";
            $ingreso ++;
        }    

        // Filtrar por Estudio
        if ($westudio  != ''){

            if ($ingreso > 0)
                $condicion .= " AND ";
            $condicion .= "Faccoe ='".$westudio."'";
            $ingreso ++;
        }   
  
        // Filtrar por Numero de Factura
        if ($wnumfac   != ''){    

            if ($ingreso > 0)
                $condicion .= " AND ";
            $condicion .= "Facnum ='".$wnumfac."'";  
            $ingreso ++;
        }   

        // Filtrar por Fecha Inicial
        if ($wfecini   != ''){ 

            if ($ingreso > 0)
                $condicion .= " AND ";
            $condicion .= "Facfec >='".$wfecini."'";         
            $ingreso ++;
        }   

        // Filtrar por Fecha Final
        if ($wfecfin   != ''){   

            if ($ingreso > 0)
                $condicion .= " AND ";
            $condicion .= "Facfec <='".$wfecfin."'";
            $ingreso ++;
        }   

        // Filtro para mostrar solamente las facturas vencidas
        if ($wfraven  == true){
          
            if ($ingreso > 0)
                $condicion .= " AND ";
            
            $condicion .= "(CURDATE() - (Facfec + INTERVAL 1 MONTH)) > 0 AND Facpag='off' "; 
            $ingreso ++;
        }

        // Filtrar por Concepto
        if ($wconcepto != ''){   

            if ($ingreso > 0)
                $condicion .= " AND ";
            $condicion .= "Mopcoc ='".$wconcepto."'";            
            $ingreso ++;
        } 


        // Filtrar por Paciente
        if ($wpaciente != ''){

            list($tipoidenti, $idpaciente) = explode('-', $wpaciente);
            
            if ($ingreso > 0)
                  $condicion .= " AND ";
              $condicion .= "UPPER(Mopide) = UPPER('".$idpaciente."') AND UPPER(Moptid) =UPPER('".$tipoidenti."') ";
              $ingreso ++;
        }

        //Agregar la condición al where
        if ($ingreso >= 1)
            $condigral = "Where ".$condicion;

        
        $q  = "  SELECT Facnum,Facfec,Faccos,Faccol,Faccoe,Facpag,Facfpa,
                        Labnom,Faciva,Facest
                 From ".$wbasedato."_000014 A
                 Inner Join ".$wbasedato."_000001 B on  A.Faccol = B.Labnit
                 Left Join ".$wbasedato."_000008 C on  A.Faccol = C.Mopcol
                       AND A.Facnum = C.Mopfac
                 ".$condigral."
                 Group by Facnum"; 

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        $vfila      = 0;
        $detalle    = '';
        $consulta   = '';
        $habilitado = '';

        $titulo = "<thead><tr class='encabezadoTabla'>                    
                    <td align='center'> NUMERO DE <br> FACTURA</td>
                    <td align='center'> FECHA </td>
                    <td align='center'> NIT </td>
                    <td align='center'> DESCRIPCION <br> LABORATORIO</td>
                    <td align='center'> ESTUDIO </td>
                    <td align='center'> VALOR <br> COSTO BRUTO</td>
                    <td align='center'> VALOR <br> IVA</td>
                    <td align='center'> VALOR <br> COSTO TOTAL </td>
                    <td align='center'> FECHA <br> PAGO </td>
                    <td align='center'> REGISTRAR <br> PAGO</td>
                    <td align='center'> ANULAR</td>
                    </tr></thead>";
       
        if  ($num > 0)
        {

            $detalle = "<tbody>";
            // Consultar todos los Pacientes asignados al Estudio
            while($row = mysql_fetch_assoc($res))
            { 
              
                  $vfila    ++;
                  $anucolor ='';

                  if (is_int ($vfila/2))
                      $wcolor="fila1";
                  else
                      $wcolor="fila2";

                  // Deshabilitar pago y anulación para las facturas que estén pagadas.
                  if ($row['Facpag'] == 'on')
                      $habilitado = 'disabled'; 
                  else
                      $habilitado = '';  

                  $fabono = $row['Facfpa'];
                  $total  = $row['Faccos'] + $row['Faciva'];

                  $anucolor = "class='fondoAzul'";  

                  if ($row['Facpag'] == 'off')
                      $anucolor = "class='fondoVerde'"; 

                  if ($row['Facest'] == 'off')
                      $anucolor = "class='fondoAmarillo'";

                                 
                  $detalle .= "<tr class='".$wcolor."' id=".$row['Facnum']."|".$row['Faccol']." ondblclick='CargarFactura(this)';>
                               <td align='center'>".$row['Facnum']."</td>
                               <td align='center'>".$row['Facfec']."</td>
                               <td align='center'>".$row['Faccol']."</td>
                               <td align='center'>".$row['Labnom']."</td>
                               <td align='center'>".$row['Faccoe']."</td>
                               <td align='right' >".number_format($row['Faccos'],2,'.',',')."</td>
                               <td align='right' >".number_format($row['Faciva'],2,'.',',')."</td>
                               <td align='right' >".number_format($total,2,'.',',')."</td>
                               <td align='center'>
                               <a id='abo_".$vfila."' style='cursor:pointer;display:none;text-align:center;'>".$fabono."</a><a id='refb_".$vfila."'><input type='text' id='fepa_".$vfila."' class='txtfecpag' ".$habilitado." style='text-align:center' value='".$fabono."''></a>
                               </td>
                               <td align='center'>
                               <input type=checkbox id='pag_".$row['Facnum']."|".$row['Faccol']."|".$vfila."' name='chkpagar' value=".$row['Facnum']."|".$row['Faccol']."|".$vfila." onclick='RegistrarPago(this)' ".$habilitado.">
                               </td>
                               <td align='center' ".$anucolor.">
                               <input type=checkbox id='chk_".$row['Facnum']."|".$row['Faccol']."' name='chkseleccion' value=".$row['Facnum']."|".$row['Faccol']." onclick='Anular(this.value)' ".$habilitado.">
                               </td>
                              </tr>";

            }

            $consulta = $titulo.$detalle.'</tbody>';

        }
        else
            $consulta = 'N'; 

        echo $consulta;
        return ;     
     } 


     if (isset($_POST["accion"]) && $_POST["accion"] == "AnularFactura"){

        list($numfactura, $labfactura) = explode('|', $wcodigo);
        
        // Libero los conceptos que se encontraban facturados en el Estudio
        $q = " UPDATE ".$wbasedato."_000008
               SET  Mopfac = ''
              WHERE Mopfac = '".$numfactura."'
                AND Mopcol = '".$labfactura."' ";

        $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

        // Actualizo el estado de la factura
        $q = " UPDATE ".$wbasedato."_000014
               SET Facest = 'off'
              WHERE Facnum = '".$numfactura."'
                AND Faccol = '".$labfactura."' ";

        $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        echo $resp;
        return;

     }


     function codificar($concepto_dec){

        return str_replace(".","__",$concepto_dec);
     }


     function decodificar($concepto_cod){

        return str_replace("__",".",$concepto_cod);
     }


    function consultarLaboratorios($wbasedato,$conex,$wemp_pmla){ 

      $strtipvar = array();
      $q  = " SELECT Labnit, Labnom"
           ."  From ".$wbasedato."_000001 ";

      $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
      
      while($row = mysql_fetch_assoc($res))
           {
             $strtipvar[$row['Labnit']] = utf8_encode($row['Labnom']);
           }
      return $strtipvar;

    }


    function consultarEstudios($wbasedato,$conex,$wemp_pmla){ 

      $strtipvar = array();
      $q  = " SELECT Estcod, Estnom"
           ."  From ".$wbasedato."_000002 ";

      $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
      
      while($row = mysql_fetch_assoc($res))
           {
             $strtipvar[$row['Estcod']] = utf8_encode($row['Estnom']);
           }
      return $strtipvar;

    }


    function consultarConceptos($wbasedato,$conex,$wemp_pmla){ 

      $strtipvar = array();
      $q  = " SELECT Concod, Condes"
           ."  From ".$wbasedato."_000003 ";

      $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
      
      while($row = mysql_fetch_assoc($res))
           {
             $strtipvar[$row['Concod']] = utf8_encode($row['Condes']);
           }
      return $strtipvar;

    }


    function consultarPacientes($wbasedato,$conex,$wemp_pmla){ 

      $strtipvar = array();

      $q  = "  Select concat (Pactid,'-',Pacced) as Idepac, 
               concat (Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) as Nompac "
           ."  From ".$wbasedato."_000011 ";

      $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
      
      while($row = mysql_fetch_assoc($res))
           {
             $strtipvar[$row['Idepac']] = utf8_encode($row['Nompac']);
           }
      return $strtipvar;

    }

    // *****************************************         FIN PHP         ********************************************

  ?>
  <html>
  <head>
    <title>Pacientes por estudio</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />   
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>  
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js"></script>    
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>     
    <script type='text/javascript' src='../../../include/root/jquery.stickytableheaders.js'></script>      

    <!--< Plugin para el select con buscador > -->
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
    <script type="text/javascript" src="../../../include/root/prettify.js"></script>
    <script src="../../../include/root/jquery-ui-timepicker-addon.js" type="text/javascript" ></script>
    <script src="../../../include/root/jquery-ui-sliderAccess.js" type="text/javascript" ></script>       
    <script type="text/javascript">

      var celda_ant="";
      var celda_ant_clase="";

      $(document).ready(function(){ 

           //Configurar campos multiselect
           $('#sellaboratorio,#selestudio,#selpaciente,#selconcepto').multiselect({
             numberDisplayed: 1,
             selectedList:1,
             multiple:false
           }).multiselectfilter();

           
           $("#txtfecini,#txtfecfin").datepicker({
                closeText: 'Cerrar',
                prevText: 'Antes',
                nextText: 'Despues',
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                'Jul','Ago','Sep','Oct','Nov','Dic'],
                dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
                dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
                dayNamesMin: ['D','L','M','M','J','V','S'],
                weekHeader: 'Sem.',
                dateFormat: 'yy-mm-dd',
                yearSuffix: '',
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
           });

           $("#divdetalle").dialog({
              autoOpen: false,
              height: 500,
              width: 1200,
              position: ['left+60', 'top+30'],
              modal: true
           });

           $(".accordionFiltros").accordion({
              collapsible: true,
              heightStyle: "content"
           });

       });


       //Listado de todas las facturas según los filtros seleccionados
       function Consultar(){

         // Activar div que muestra el tiempo de proceso
         document.getElementById("divcargando").style.display   = "";

         var wemp_pmla  = $("#wemp_pmla").val();
         var wlaborato  = $("#sellaboratorio").val(); 
         var westudio   = $("#selestudio").val();
         var wpaciente  = $("#selpaciente").val();     
         var wconcepto  = $("#selconcepto").val();
         var wnumfac    = $("#txtnumfac").val();
         var wfecini    = $("#txtfecini").val();
         var wfecfin    = $("#txtfecfin").val();
         var wfraven    = $('input:checkbox[name=chkfraven]:checked').val();

         $.post("facturacion_direccion_cientifica.php",
               {
                  consultaAjax:   true,
                  accion:         'ConsultarFactura',
                  wemp_pmla:      wemp_pmla,
                  wlaborato:      wlaborato.toString(),
                  westudio :      westudio.toString(),
                  wpaciente:      wpaciente.toString(),
                  wconcepto:      wconcepto.toString(),
                  wnumfac  :      wnumfac,
                  wfecini  :      wfecini,
                  wfecfin  :      wfecfin,
                  wfraven  :      wfraven
               }, function(respuesta){

                  if  (respuesta=='N'){
                      $("#tblmensaje").show();
                      $("#tblpaciente").hide();
                      $("#tblconvencion").hide();
                  }
                  else{
                      $("#tblconvencion").show();
                      $("#tblmensaje").hide();
                      $("#tblpaciente").show();
                      $("#tblpaciente").empty();
                      $("#tblpaciente").append(respuesta);

                      $(".txtfecpag").datepicker({
                        closeText: 'Cerrar',
                        prevText: 'Antes',
                        nextText: 'Despues',
                        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                        'Jul','Ago','Sep','Oct','Nov','Dic'],
                        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
                        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
                        dayNamesMin: ['D','L','M','M','J','V','S'],
                        weekHeader: 'Sem.',
                        dateFormat: 'yy-mm-dd',
                        yearSuffix: '',
                        showOn: "button",
                        buttonImage: "../../images/medical/root/calendar.gif",
                        buttonImageOnly: true,
                      });
                  }
                  
                  document.getElementById("divcargando").style.display   = "none";

               });

       }

       //Muestra el Detalle de una factura seleccionada en la consulta principal
       function CargarFactura(obj){

         var ide    = $(obj).attr('id');
         var valor  = ide.split('|');

         $.post("facturacion_direccion_cientifica.php",
               {
                  consultaAjax:  true,
                  accion      :  'ConsultarDetalle',
                  wemp_pmla   :  $("#wemp_pmla").val(),
                  numfactura  :  valor[0],
                  labfactura  :  valor[1],
               }, function(respuesta){

                  if  (respuesta=='N'){

                      $("#tblmensaje").show();
                  }
                  else{  
                      $("#tblmensaje").hide();
                      $("#divdetalle").show();
                      $("#divdetalle").empty();
                      $("#divdetalle").append(respuesta);
                      $("#divdetalle").dialog("open"); 
                  }

               });

       }

       // Imprimir tabla que contiene el detalle de la factura
       function ImprimirModal(){

          var divToPrint = document.getElementById('tbldetallefac');
          newWin = window.open("");
          newWin.document.write(divToPrint.outerHTML);
          newWin.print();
          newWin.close();
       }


       function CerrarModal(){

          $("#divdetalle").dialog("close");
       }


       function RegistrarPago(obj){

         var valor   = obj.value.split('|');
         var campo   = 'fepa_'+valor[2];
         var campag  = 'pag_'+obj.value;
         
         if(document.getElementById(campo).value == '0000-00-00'){

            jAlert('Debe Ingresar la fecha de pago');
            document.getElementById(campag).checked = false;
            return;
         }


         if (document.getElementById(campag).checked = true){   

            jConfirm("Esta seguro de registrar el pago?","Confirmar", function(respuesta){

               if (respuesta == true){                
              
                       var fecfac  = document.getElementById(campo).value;

                       $.post("facturacion_direccion_cientifica.php",
                             {
                                consultaAjax:  true,
                                accion      :  'RegistrarPago',
                                wemp_pmla   :  $("#wemp_pmla").val(),
                                numfactura  :  valor[0],
                                labfactura  :  valor[1],
                                fecfactura  :  fecfac
                             }, function(respuesta){
                                
                                if (respuesta == 1){
                                   jAlert('El pago ha sido registrado');
                                   Consultar();
                                }

                             });
                     
                }
                else{

                    document.getElementById(campag).checked = false;
                }
              
           });

        }
        else{

             jAlert('Factura pagada no se puede modificar');
        }

      } 

       
      
       function Anular(codigo){

              var vcamanu='chk_'+codigo;

              jConfirm("Esta seguro de Anular la Factura?","Confirmar", function(respuesta){

                   if (respuesta == true)
                   {                   
                     
                       $.post("facturacion_direccion_cientifica.php",
                       {
                          consultaAjax:   true,
                          accion   :      'AnularFactura',
                          wemp_pmla:      $("#wemp_pmla").val(),
                          wcodigo  :      codigo
                       }, function(respuesta){
                          
                          if (respuesta == 1){ 
                              jAlert('La Factura ha sido Anulada');
                              Consultar();
                          }

                       });

                   }
                   else{
                        document.getElementById(vcamanu).checked = false;
                   }    
              });

       }


       function justNumbers(e){
         var keynum = window.event ? window.event.keyCode : e.which;

         if ((keynum == 8) || (keynum == 46) || (keynum == 0))
              return true;

         return /\d/.test(String.fromCharCode(keynum));
       }

       
       function mostrarTooltip( celda ){
          if( !celda.tieneTooltip ){
            $( "*", celda ).tooltip();
            celda.tieneTooltip = 1;
          }
       }


       function alerta(txt){

         $("#textoAlerta").text( txt );
         $.blockUI({ message: $('#msjAlerta') });
           setTimeout( function(){
                   $.unblockUI();
                }, 1800 );
       }


       function cerrarVentana()
       { 
          if(confirm("Esta seguro de salir?") == true)
             window.close();
          else
             return false;
       }


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


      function iluminacolumna(celda,columna)
      {
         $("td.fondoAmarillo").removeClass('fondoAmarillo');
         $("."+columna).addClass("fondoAmarillo");       
      }


           // **************************************   Fin Funciones Javascript   ********************************************
      </script> 
      <style type="text/css">

        .button{
          color: #1b2631;
          font-weight: normal;
          font-size: 12,75pt;
          width: 100px; height: 27px;
          background: rgb(199,199,199);
          background: -moz-linear-gradient(top,  rgba(199,199,199,1) 0%, rgba(193,193,193,1) 50%, rgba(184,184,184,1) 51%, rgba(224,224,224,1) 100%);
          background: -webkit-linear-gradient(top,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
          background: linear-gradient(to bottom,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
          filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c7c7c7', endColorstr='#e0e0e0',GradientType=0 );
          border: 1px solid #ccc;
          border-radius: 8px;
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
         }

        .button:hover {background-color: #3e8e41}

        .button:active {
           background-color: rgb(169,169,169);
           box-shadow: 0 5px #666;
           transform: translateY(4px);
         }

        .ui-multiselect { height:20px; overflow-x:hidden; padding:2px 0 2px 4px; text-align:left;font-size: 10pt; } 
        
        .color1{
            background-color:#5F7E6F;}
        
        .color2{
            background-color:#2471a3;}

        .fondoAmarillo {
           background-color: #fcf3cf;
           color: #000000;
           font-size: 10pt;
         }

        .fondoVerde {
           background-color: #a9dfbf;
           font-size: 10pt;
         }

        .fondoAzul {
           background-color: #7fb3d5;
           font-size: 10pt;
         }    

      </style>  
 </head>
  <body >   
    <?php 
      echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
      $wtitulo  ="FACTURACION DIRECCION CIENTIFICA";
      encabezado($wtitulo, $wactualiz, 'clinica');
      $arr_lab  = consultarLaboratorios ($wbasedato,$conex,$wemp_pmla);
      $arr_pac  = consultarPacientes ($wbasedato,$conex,$wemp_pmla);
      $arr_est  = consultarEstudios ($wbasedato,$conex,$wemp_pmla);
      $arr_con  = consultarConceptos ($wbasedato,$conex,$wemp_pmla);
    ?>
    <center>
    <fieldset style="border: 0.5px solid #999999;width:500px;font-size:8pt">    
    <table  width='700px'>
    <tr >
        <td class='fila1' width='200px' ><b>N&uacute;mero de Factura</b></td> 
        <td class='fila2' width='500px'>&nbsp;
        <input type='text' id='txtnumfac' name='txtnumfac' size='20'> 
        </td>
    </tr>
    <tr >
        <td class='fila1'><b>Fecha Inicial</b></td> 
        <td class='fila2'>&nbsp;&nbsp;<input type='text' id='txtfecini' name='txtfecini' size='20' readonly></td>
    </tr>
    <tr>  
        <td class='fila1'><b>Fecha Final</b></td> 
        <td class='fila2'>&nbsp;&nbsp;<input type='text' id='txtfecfin' name='txtfecfin' size='20' readonly></td>
    </tr>
    <tr>
        <td class='fila1'><font size="2"><b>Paciente</b></font></td>
        <td class='fila2'>&nbsp;<select id='selpaciente' name='selpaciente' multiple='multiple' style='width: 100px;'>
           <?php
              echo '<option selected></option>';
              foreach( $arr_pac as $key => $val){
                echo '<option value="' . $key .'">'.$val.'</option>';
            }
           ?>
           </select>
        </td>
     </tr>
     <tr>
         <td class='fila1'><font size="2"><b>Laboratorio</b></font></td>
         <td class='fila2'>&nbsp;<select id='sellaboratorio' name='sellaboratorio' multiple='multiple' style='width: 100px;' >
           <?php
              echo '<option selected></option>';
              foreach( $arr_lab as $key => $val){
                echo '<option value="' . $key .'">'.$val.'</option>';
            }
           ?>
         </select>
         </td>
     </tr>
     <tr>
         <td class='fila1'><font size="2"><b>Estudio</b></font></td>
         <td class='fila2'>&nbsp;<select id='selestudio' name='selestudio' multiple='multiple' style='width: 100px;' >
           <?php
            echo '<option selected></option>';
            foreach( $arr_est as $key => $val){
              echo '<option value="' . $key .'">'.$key.'</option>';
            }
           ?>
         </select>
         </td>
     </tr>
     <tr>  
       <td class='fila1'><font size="2"><b>Concepto</b></font></td>
       <td class='fila2'>&nbsp;<select id='selconcepto' name='selconcepto' multiple='multiple' style="width: 100px;">
         <?php
          echo '<option selected></option>';
          foreach( $arr_con as $key => $val){
            echo '<option value="' . $key .'">'.$val.'</option>';
          }
         ?>
       </select>
       </td>
     </tr>
     <tr>
        <td class='fila1'><font size="2"><b>Facturas Vencidas</b></font></td>
        <td class='fila2'>&nbsp;<input type='checkbox' id='chkfraven' name='chkfraven'> </td>
     </tr>
     </table>     
     </fieldset>
     </center>
     <br><br>
     <center>
     <table>
      <tr>
         <td>&nbsp;&nbsp;<input type='button' id='btnConsultar' name='btnConsultar' class='button' value='Consultar'  onclick='Consultar()'></td>      
         <td>&nbsp;&nbsp;<input type='button' id='btnSalir'     name='btnSalir'     class='button' value='Salir'      onclick='cerrarVentana()'></td>
      </tr>
    </table>
    </center>
    <br><br>
    <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center></div>
    <left>
    <table id='tblconvencion' border=0 align=center style="font-size:15px;display:none;width:500px">    
      <tr><td align='center' class='fondoAzul'     style="height:20px; width:20px; border-radius: 10px;">Pagado</td>
          <td align='center' class='fondoVerde'    style="height:20px; width:20px; border-radius: 10px;">Pendiente</td>
          <td align='center' class='fondoAmarillo' style="height:20px; width:20px; border-radius: 10px;">Anulado</td>
      </tr>    
    </table>
    </left>
    <center>
    <table id='tblpaciente' name='tblpaciente' style="border: 0.5px solid #999999;display:none;width:1400px">
    </table> 
    <div id='divdetalle' name='divdetalle' class='divdetalle' align='center' style='border: 0.5px solid #999999;display:none;' title='Detalle Factura'>
    <br><br>
    <table>
    </table>
    </div>
    <br>
    <table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
      <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
    </table>
  </body>
  </html>