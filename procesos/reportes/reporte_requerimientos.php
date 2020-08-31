<?php
include_once("conex.php");
 /**********************************************************************************************************
 *
 * Programa				    :	reporte_requerimientos.php
 * Fecha de Creación 	:	2020-06-15
 * Autor				      :	Arleyda Insignares Ceballos
 * Descripcion		    :	Reporte solicitud de servicios el cual muestra los datos de registro del incidente 
 *                      y su respectivo seguimiento.
 *                      
 ***************************************   Modificaciones  *************************************************/
              
 
 $wactualiz = "2020-06-15";
 if(!isset($_SESSION['user'])){
	 echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	 return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');

  //**************************************    Inicio   ***********************************************************  

  include_once("root/comun.php");
  

  $conex         = obtenerConexionBD("matrix");
  $wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $wfecha        = date("Y-m-d");
  $whora         = (string)date("H:i:s");
  $pos           = strpos($user,"-");
  $wusuario      = substr($user,$pos+1,strlen($user));

  // *************************************    FUNCIONES AJAX  Y PHP  **********************************************

   
      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarDetalle"){

          $detalle   ='';
          $excel     ='';
          $condicion ='';
          $respuesta = array();
   
    
          // Consultar las tablas root_000040
          $qcon =  " SELECT  A.Reqnum, A.Reqtip, A.Reqfec, A.Requso, A.Requrc, A.Reqdes,
                             A.Reqpurs, A.Reqpri, A.Reqest, A.Reqcla, A.Reqsed, 
                             A.Reqtir, A.Reqfal, A.Reqcan, A.Reqtpn
                     From root_000040 A
                     WHERE Fecha_data >= '".$wfechaini."' AND Fecha_data <= '".$wfechafin."'                         
                     ORDER BY Fecha_data desc";


          $res_con = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
          $numcon  = mysql_num_rows($res_con);

          $cont1     = 0;

          if  ($numcon > 0)
          {
              // Consultar todos los Pacientes asignados al Estudio
              while($row = mysql_fetch_assoc($res_con))
              { 
                    $wreqpri = '';
                    $wreqcla = '';
                    $wreqfal = '';
                    $wreqtir = '';
                    $wreqest = '';
                    $wseguimiento ='';

                    // Descripcion Prioridad
                    $q =  " SELECT Subcodigo,  Descripcion 
                             FROM det_selecciones 
                             WHERE Medico='root' 
                               AND Codigo='16' 
                               AND Activo = 'A' 
                               AND Subcodigo = '".$row['Reqpri']."' ";

                    $respri = mysql_query($q,$conex);
                    $rowpri = mysql_fetch_array($respri);
                    $wreqpri= $rowpri['Descripcion'];


                    // Clasificacion
                    $q =  " SELECT Clades 
                            FROM root_000043 
                            WHERE Claest = 'on' 
                              AND Clacod = '".$row['Reqcla']."' ";
                    
                    $rescla = mysql_query($q,$conex);
                    $rowcla = mysql_fetch_array($rescla);
                    $wreqcla= $rowcla['Clades'];
            
            

                    // Tipo de requerimiento
                    $q =  " SELECT Descripcion 
                             FROM det_selecciones 
                           WHERE Medico='root' 
                             AND Codigo='20' 
                             AND Activo = 'A' 
                             AND Subcodigo = '".$row['Reqtir']."' ";

                    $res2 = mysql_query($q,$conex);
                    $row2 = mysql_fetch_array($res2);
                    $wreqtir=$row2['Descripcion'];

                    //Tipo de fallas
                    $q =  " SELECT Descripcion 
                              FROM det_selecciones 
                            WHERE Medico='root' 
                              AND Codigo='21' 
                              AND Activo = 'A' 
                              AND Subcodigo = '".$row['Reqfal']."' ";
                  
                    $res3 = mysql_query($q,$conex);
                    $row3 = mysql_fetch_array($res3);
                    $wreqfal=$row3['Descripcion'];


                    //consulto estado
                    $q =  " SELECT Estnom, Estosg"
                    ."        FROM root_000049 "
                    ."      WHERE Estest = 'on' "
                    ."      and Estcod = '".$row['Reqest']."' ";

                    $resest  = mysql_query($q,$conex);
                    $rowest  = mysql_fetch_array($resest);
                    $wreqest = $rowest['Estnom'];

                    //Tipo de canal
                    $q =  " SELECT Descripcion 
                            FROM det_selecciones 
                          WHERE Medico='root' 
                            AND Codigo='22' 
                            AND Activo = 'A' 
                            AND Subcodigo = '".$row['Reqcan']."' ";
                  
                    $res4 = mysql_query($q,$conex);
                    $row4 = mysql_fetch_array($res4);     
                    $wreqcan=$row4['Descripcion'];                 


                    // Consultar datos del usuario
                    $q= "SELECT Usucod, Usucco, Usuext, Usuema, Usucar, Ususup, Usused, Descripcion 
                         FROM root_000039, usuarios 
                        WHERE Usucod = '".$row['Requso']."' 
                          AND Codigo = usucod 
                        ORDER BY Usuest DESC 
                        LIMIT 1;";
                    
                    $resusu  = mysql_query($q,$conex);
                    $rowusu  = mysql_fetch_array($resusu);
                    $wrequsu = $rowusu['Descripcion'];


                    //Consultar seguimiento
                    $q= " SELECT Segfec, Segnum, Segpcu, Segtxt, Segusu, Segest, Segpri, Segtir, 
                                 Segfal, Segcan, Descripcion, Hora_data, seg.id AS id_seg 
                           FROM root_000045 AS seg, usuarios 
                          WHERE Segtpn = '".$row['Reqtpn']."' 
                            AND Segusu = Codigo 
                            AND Segtxt <> '' 
                        ORDER BY Segfec DESC, Hora_data DESC ";

                    $reseg = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         
                    $num = mysql_num_rows($reseg);
                   
                    if ($num > 0)
                    {
                       while($rowseg = mysql_fetch_assoc($reseg)){
                             $wseguimiento .= '<br>'.$rowseg['Segfec'].' - '.$rowseg['Segtxt'];
                       }
                    }
                    else
                    {
                       $wseguimiento= '<br>Sin Seguimiento';
                    }    


                    $cont1 % 2 == 0 ? $fondo = "fila1" : $fondo = "fila2";
                    $cont1 ++;

                    $detalle .= "<tr class='".$fondo."' onclick='ilumina(this,\"".$fondo."\")'>
                                 <td >".utf8_encode($row["Reqtpn"])."</td>
                                 <td >".$row["Reqfec"]."</td>
                                 <td >".$row["Requso"].'-'.$wrequsu."</td>
                                 <td >".utf8_encode($row["Reqdes"])."</td>
                                 <td >".utf8_encode($wreqpri)."</td>
                                 <td >".utf8_encode($wreqcla)."</td>
                                 <td >".utf8_encode($wreqtir)."</td>
                                 <td >".utf8_encode($wreqfal)."</td>
                                 <td >".utf8_encode($wreqcan)."</td>
                                 <td >".utf8_encode($wreqest)."</td>
                                 <td >".utf8_encode($wseguimiento)."</td>
                                </tr>";

                    $excel .= "<tr >
                                 <td >".utf8_encode($row["Reqtpn"])."</td>
                                 <td >".$row["Reqfec"]."</td>
                                 <td >".$row["Requso"].'-'.$wrequsu."</td>
                                 <td >".utf8_encode($row["Reqdes"])."</td>
                                 <td >".utf8_encode($wreqpri)."</td>
                                 <td >".utf8_encode($wreqcla)."</td>
                                 <td >".utf8_encode($wreqtir)."</td>
                                 <td >".utf8_encode($wreqfal)."</td>
                                 <td >".utf8_encode($wreqcan)."</td>
                                 <td >".utf8_encode($wreqest)."</td>
                                 <td >".utf8_encode($wseguimiento)."</td>
                              </tr>";
              }                
          }

          if ($cont1>0){

              $respuesta['titulo1'] = $detalle;
              $respuesta['titulo2'] = 'S';
              $respuesta['titulo3'] = $excel;
          }
          else{
              $respuesta['titulo1'] = 'N';
              $respuesta['titulo2'] = 'N';
              $respuesta['titulo3'] = 'N';
          }
         
          echo json_encode($respuesta);
          return;
      }


   
     // Consultar los usuarios para el campo autocompletar
     function consultarUsuarios($wbasedato,$conex,$wemp_pmla){     
        
        $strtipvar = array();
        $q  = " SELECT codigo, descripcion"
        ."   From usuarios A "      
        ."   where activo ='A'";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res))
        {
               $strtipvar[$row['codigo']] = $row['codigo'].'-'.utf8_encode($row['descripcion']);
        }
        return $strtipvar;

      }


      // Consultar los centros de costos en movhos_000011 para el campo multiselect
      function consultarServicios($wbasedato,$wbasemovhos,$conex,$wemp_pmla){

        $strtipvar = array();
        $q  = " SELECT Ccocod, Cconom
                From ".$wbasemovhos."_000011 
                Where Ccoing = 'on' ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res))
             {
               $strtipvar[$row['Ccocod']] = utf8_encode($row['Cconom']);
             }
        return $strtipvar;
      }


      function edad($fecha){

          $fecha = str_replace("/","-",$fecha);
          $fecha = date('Y/m/d',strtotime($fecha));
          $hoy = date('Y/m/d');
          $edad = $hoy - $fecha;
          return $edad;

      }


     // Consultar todos los Centros de Costos para el campo autocompletar
      function consultarCentros($wbasemovhos,$conex,$wemp_pmla){
          $strtipvar = array();

          $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                      FROM    ".$wbasemovhos."_000011 AS tb1                                    
                      GROUP BY    tb1.Ccocod
                      ORDER BY    tb1.Cconom";

          $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
          while($row = mysql_fetch_assoc($res))
          {
               $strtipvar[$row['codigo']] = $row['codigo'].'-'.utf8_encode($row['nombre']);
          }

         return $strtipvar;
      }

     // *****************************************         FIN PHP         ********************************************
  ?>
  <html>
  <head>
    <title>Reporte de Solicitudes</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js"></script>
    <script type="text/javascript">

      var celda_ant="";
      var celda_ant_clase="";
      
      $(document).ready(function(){

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

 
   

      }); // Finalizar Ready()



      function Consultar(){

          // Validar que el rango de fecha esté diligenciado
          if (  $("#txtfecini").val()=='' || $("#txtfecfin").val()=='')
          {
                jAlert('Falta diligenciar Informaci\u00f3n');
                return;
          }

          if ($("#autcodusu").val() == '')            
              $("#wcodusuario").val('');

          //Limpiar el campo de filtro
          $("#id_search_pacientes").val('');          

          var wemp_pmla   = $("#wemp_pmla").val();          
          var wfechaini   = $("#txtfecini").val();
          var wfechafin   = $("#txtfecfin").val();          

 
          // Activar div que muestra el tiempo de proceso
          document.getElementById("divcargando").style.display   = "";

          $.post("reporte_requerimientos.php",
               {
                consultaAjax:  true,
                accion   :     'ConsultarDetalle',
                wemp_pmla:     wemp_pmla,
                wfechaini:     wfechaini,
                wfechafin:     wfechafin
               }, function(data){

                  // Ocultar div que muestra el tiempo de proceso
                  document.getElementById("divcargando").style.display    = "none";

                  // En caso de no encontrar registros
                  if (data.titulo2 == 'N'){

                    $("#tbldetalle").hide();
                    $("#tblbuscar").hide();
                    $("#divcargando").hide();
                    $("#tblmensaje").show();

                  } 
                  // Mostrar tabla con registros consultados
                  else{
                    
                    $("#tblmensaje").hide();                    
                    $("#tbldetalle tbody ").remove(0);
                    $("#tblexcel tbody ").remove(0);
                    $("#tblbuscar").show();
                    $("#tbldetalle").show();
                    $("#tbldetalle").append(data.titulo1);
                    $("#tblexcel").append(data.titulo3);
                    $('input#id_search_pacientes').quicksearch('table#tbldetalle tbody tr');

                  }                  

               },"json");
               
       }


       // Función para exportar la tabla 'tblexcel'
       function Exportar(){
     
            //Creamos un Elemento Temporal en forma de enlace
            var tmpElemento = document.createElement('a');
            var data_type = 'data:application/vnd.ms-excel'; //Formato anterior xls

            // Obtenemos la información de la tabla
            var tabla_div = document.getElementById('tblexcel');
            var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');
            
            tmpElemento.href = data_type + ', ' + tabla_html;
            //Asignamos el nombre a nuestro EXCEL
            tmpElemento.download = 'reporte_requerimientos.xls';
            // Simulamos el click al elemento creado para descargarlo
            tmpElemento.click();

       }

 
      // Iluminar las filas de una tabla donde se ubique el mouse
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


      // Ilumina toda la columna donde se ubique el mouse 
      function iluminacolumna(celda,columna)
      {
         $("td.fondoAmarillo").removeClass('fondoAmarillo');
         $("."+columna).addClass("fondoAmarillo");       
      }

      
      // Funcion para restringir permitiendo digitar solo numeros
      function justNumbers(e)
      {
         var keynum = window.event ? window.event.keyCode : e.which;

         if ((keynum == 8) || (keynum == 46) || (keynum == 0))
              return true;

          return /\d/.test(String.fromCharCode(keynum));
      }


       // Sacar un mensaje de alerta con formato predeterminado
       function alerta(txt){
         $("#textoAlerta").text( txt );
         $.blockUI({ message: $('#msjAlerta') });
           setTimeout( function(){
                   $.unblockUI();
                }, 1800 );
       }


       function cerrarVentana()
       {
          if(confirm("Esta seguro de salir?") == true){
            window.close();}
          else
            return false;
       }

      </script>

      <style type="text/css">
        .button{
          color: #1b2631;
          font-weight: normal;
          font-size: 12,75pt;
          width: 90px; height: 27px;
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
           
         BODY {
            font-family: verdana;
            font-size: 10pt;
            width: auto;
            height:auto;
         }

         .tbldetalle tr {
            height: 50px;
         }


    </style>
    </head>
    <body >
      <?php
        echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
        $wtitulo  = "REPORTE REGISTRO DE INCIDENCIAS";
        encabezado($wtitulo, $wactualiz, 'clinica');
      ?>
      <table align='center' width="50%" style='border: 1px solid gray'>
          <tr height='30px'>
          <td class='fila1'><b>Fecha Inicial</b></td>
          <td class='fila2'><input type='text' id='txtfecini' name='txtfecini' size='15'  readonly placeholder="Ingrese Fecha Inicial">
          </td>
          </tr>
          <tr>
          <td class='fila1'><b>Fecha Final</b></td>
          <td class='fila2'><input type='text' id='txtfecfin' name='txtfecfin' size='15'  readonly placeholder="Ingrese Fecha Final">
          </td>
      </table>
      <br>
      <center>
      <table> 
          <tr>
          <td>&nbsp;&nbsp;<input type='button' id='btnConsultar' name='btnConsultar' class='button' value='Consultar' onclick='Consultar()'></td>
          <td>&nbsp;&nbsp;<input type='button' id='btnExportar'  name='btnExportar'  class='button' value='Exportar'  onclick='Exportar()'></td>
          <td>&nbsp;&nbsp;<input type='button' id='btnSalir'     name='btnSalir'     class='button' value='Salir'     onclick='cerrarVentana()'></td>
          </tr>
      </table>
      </center>
      <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center></div>
      <br>&nbsp;&nbsp;
      <div>
      <table align='left' style='display:none;' id='tblbuscar' name='tblbuscar'>
          <tr><td></td><td class='fila1'>&nbsp;Filtrar listado:&nbsp;&nbsp;<input id="id_search_pacientes" type="text" value="" size="20" name="id_search_pacientes" placeholder="Buscar en listado">&nbsp;&nbsp;</td></tr>
          </table>
      </div>
      <br>&nbsp;
      <center>
      <table width='100%' id='tbldetalle' name='tbldetalle' class='tbldetalle' style='border: 1px solid blue;display:none;' >
          <thead>
            <tr class='encabezadotabla'>
              <th align='center'>NUMERO</th>
              <th align='center'>FECHA</th>
              <th align='center'>USUARIO</th>
              <th align='center'>SOLICITUD</th>
              <th align='center'>PRIORIDAD</th>
              <th align='center'>CLASIFICACION</th>
              <th align='center'>TIPO DE REQUERIMIENTO</th>
              <th align='center'>TIPO DE FALLA</th>
              <th align='center'>CANAL</th>
              <th align='center'>ESTADO</th>
              <th align='center'>SEGUIMIENTO</th>
            </tr>
          </thead>
      </table>
      <table width='1650px' id='tblexcel' name='tblexcel' class='tblexcel' style='border: 1px solid blue;display:none;' >
          <thead>
            <tr class='encabezadotabla'>
              <th align='center'>NUMERO</th>
              <th align='center'>FECHA</th>
              <th align='center'>USUARIO</th>
              <th align='center'>SOLICITUD</th>
              <th align='center'>PRIORIDAD</th>
              <th align='center'>CLASIFICACION</th>
              <th align='center'>TIPO DE REQUERIMIENTO</th>
              <th align='center'>TIPO DE FALLA</th>
              <th align='center'>CANAL</th>
              <th align='center'>ESTADO</th>
              <th align='center'>SEGUIMIENTO</th>
            </tr>
          </thead>
      </table>
      </center>
      <center>
      <table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
        <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
      </table>
      </center>
      <input type="HIDDEN" name="wcodusuario" id="wcodusuario">
      <input type="HIDDEN" name="wcodcentro"  id="wcodcentro">
    </body>
    </html>