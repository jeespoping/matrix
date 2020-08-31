<?php
include_once("conex.php");
 /**********************************************************************************************************
 *
 * Programa				    :	ReporteCargos_detallado.php
 * Fecha de Creación 	:	2017-04-28
 * Autor				      :	Arleyda Insignares Ceballos
 * Descripcion		    :	Reporte de Cargos x Tipo de Movimiento Detallado: Es un listado de todos los movimientos
 *                 	    de grabado, regrabado y anulación. Permite visualizar los procedimientos realizados
 *                      al paciente y filtrar por varios centros de costos, fecha y usuario que realiza el
 *                      movimiento.                      
 *                      
 ***************************************   Modificaciones  *************************************************/
              
 
 $wactualiz = "2017-04-28";
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
  $wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
  $wbasetalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma'); 
  $wbasemovhos   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos'); 
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

          // Seleccionar filtro según el tipo de movimiento
          switch ($woptmovto)
          {
                  case "GRABAR":
                       $condicion .= ' Audacc = "GRABO" ';
                       break;

                  case "REGRABAR":
                       $condicion .= ' Audacc like "%REGRABA%" ';
                       break;

                  case "ANULAR":
                       $condicion .= ' Audacc = "ANULO"';
                       break;
          }


          if ($wcodcentro  != ''  &&  !is_null($wcodcentro)){

              for ($pos = 0; $pos < count($wcodcentro); $pos++ ){

                  if ($pos == 0)
                     $condicion .= ' AND (Tcarser like "%'.$wcodcentro[$pos].'%"';
                  else 
                     $condicion .= ' OR  Tcarser like "%'.$wcodcentro[$pos].'%"'; 

                  if ($pos == (count($wcodcentro)-1) )

                     $condicion .= ')';

              }     

          }


          if ($wcodusuario != '')
              $condicion .= ' AND Audusu = "'.$wcodusuario.'" ';


          // Filtrar la tabla movhos_000107 con una tabla temporal y obtener mayor rendimiento en la consulta
          $q1 = " CREATE TEMPORARY TABLE T1 (select * From ".$wbasedato."_000107 
                     Where Fecha_data >= '".$wfechaini."' AND Fecha_data <= '".$wfechafin."')";  

          $res = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());


          // Consultar las tablas cliame_000106 y cliame_000107
          $qcon =  " SELECT A.Fecha_data, A.Hora_data, A.Audhis, A.Auding, A.Audreg, A.Audusu,
                             A.Audcau, A.Audjus, B.Tcarcan, B.Tcarvun, B.Tcarser, B.Tcarvto,B.Tcarconcod,
                             B.Tcarconnom,B.Tcarpronom,C.Caudes, U.Descripcion as Nomusuario,D.Pacdoc,
                             concat (D.Pacno1,' ',D.Pacno2,' ', D.Pacap1, ' ',D.Pacap2) as Nompac, 
                             D.Pactdo,B.Tcarres, E.Empnom,F.Cconom,G.Grudes
                     From T1 A
    	                       Inner Join ".$wbasedato."_000106 B  on A.Audreg  = B.Id   
    	                       Inner Join ".$wbasedato."_000024 E  on E.Empcod  = B.Tcarres                    
    	                       Left  Join ".$wbasedato."_000268 C  on C.Caucod  = A.Audcau
                             Inner Join ".$wbasedato."_000100 D  on D.Pachis  = B.Tcarhis
                             Inner Join ".$wbasedato."_000200 G  on G.Grucod  = B.Tcarconcod
                             Inner Join ".$wbasemovhos."_000011 F on F.Ccocod = B.Tcarser
    	                       Inner Join usuarios U on U.Codigo = A.Audusu
                     WHERE " .$condicion. "                        
                     ORDER BY Fecha_data desc";


          $res_con = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
          $numcon  = mysql_num_rows($res_con);

          $cont1     = 0;

          if  ($numcon > 0)
          {
              // Consultar todos los Pacientes asignados al Estudio
              while($row = mysql_fetch_assoc($res_con))
              { 

                 $cont1 % 2 == 0 ? $fondo = "fila1" : $fondo = "fila2";
                 $cont1 ++;

                 $detalle .= "<tr class='".$fondo."' onclick='ilumina(this,\"".$fondo."\")'>
                               <td >".$row["Fecha_data"]."</td>
                               <td >".$row["Hora_data"]."</td>
                               <td >".$row["Audhis"].'-'.$row["Auding"]."</td>
                               <td >".$row["Pactdo"].'-'.$row["Pacdoc"]."</td>
                               <td >".utf8_encode($row["Nompac"])."</td>
                               <td >".utf8_encode($row["Tcarres"])."</td>
                               <td >".utf8_encode($row["Empnom"])."</td>
                               <td >".utf8_encode($row["Cconom"])."</td>
                               <td >".utf8_encode($row["Tcarpronom"])."</td>
                               <td >".utf8_encode($row["Tcarconcod"])."</td>
                               <td >".utf8_encode($row["Grudes"])."</td>
                               <td >".$row["Audreg"]."</td>
                               <td align='center'>".$row["Tcarcan"]."</td>
                               <td align='right'>".number_format($row["Tcarvun"])."</td>
                               <td align='right'>".number_format($row["Tcarvto"])."</td>
                               <td >".utf8_encode($row["Audusu"])."</td>
                               <td >".utf8_encode($row["Nomusuario"])."</td>
                             </tr>";

                 $excel .= "<tr class='".$fondo."' onclick='ilumina(this,\"".$fondo."\")'>
                               <td >".$row["Fecha_data"]."</td>
                               <td >".$row["Hora_data"]."</td>
                               <td >".$row["Audhis"].'-'.$row["Auding"]."</td>
                               <td >".$row["Pactdo"].'-'.$row["Pacdoc"]."</td>
                               <td >".utf8_encode($row["Nompac"])."</td>
                               <td >".utf8_encode($row["Tcarres"])."</td>
                               <td >".utf8_encode($row["Empnom"])."</td>
                               <td >".utf8_encode($row["Cconom"])."</td>
                               <td >".utf8_encode($row["Tcarpronom"])."</td>
                               <td >".utf8_encode($row["Tcarconcod"])."</td>
                               <td >".utf8_encode($row["Grudes"])."</td>                           
                               <td >".$row["Audreg"]."</td>
                               <td >".$row["Tcarcan"]."</td>
                               <td >".round($row["Tcarvun"])."</td>
                               <td >".round($row["Tcarvto"])."</td>
                               <td >".utf8_encode($row["Audusu"])."</td>
                               <td >".utf8_encode($row["Nomusuario"])."</td>
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

      function codificar($concepto_dec){

        return str_replace("#","nro",$concepto_dec);
      }


      function decodificar($concepto_cod){

        return str_replace("__",".",$concepto_cod);
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
    <title>Reporte de Cargos detallado</title>
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
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />    
    <script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
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

 
          // Asignar array al campo autocompletar usuario responsable
          var arr_usu  = eval('(' + $('#arr_usu').val() + ')');
          var usuarios = new Array();
          var index   = -1;
          for (var cod_usu in arr_usu)
              {
                  index++;
                  usuarios[index]                = {};
                  usuarios[index].value          = cod_usu;
                  usuarios[index].label          = arr_usu[cod_usu];
                  usuarios[index].codigo         = cod_usu;
                  usuarios[index].nombre         = arr_usu[cod_usu];
              }            

              $("#autcodusu").autocomplete({
              source: usuarios,
              autoFocus: true,
              select:     function( event, ui ){
                      $( "#autcodusu" ).val(ui.item.nombre);
                      $( "#autcodusu" ).attr('valor', ui.item.value);
                      $( "#autcodusu" ).attr('label', ui.item.label);
                      $( "#wcodusuario" ).val(ui.item.codigo);
                      return false;
              },          
          });   


          $('#autcodusu').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                        $( "#wcodusuario" ).val('');
                    }
                    else
                    {
                        $(this).val($(this).attr("label"));
                    }
                }
          }); 

 
          $('#optservicio').multiselect({
               position: {
                                    my: 'left bottom',
                                    at: 'left top'
                         },
               selectedText: "# of # seleccionados"
          }).multiselectfilter();  


      }); // Finalizar Ready()



      function Consultar(){

          // Validar que el rango de fecha esté diligenciado
          if (  $("#txtfecini").val()=='' || $("#txtfecfin").val()=='' || $("#optmovto").val()==''){

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
          var wcodusuario = $("#wcodusuario").val();

          if ( $("#optservicio").val()==null )
             var wcodcentro ='';
          else
             var wcodcentro  = $("#optservicio").val();

          var woptmovto   = $("#optmovto").val();

          // Activar div que muestra el tiempo de proceso
          document.getElementById("divcargando").style.display   = "";

          $.post("ReporteCargos_detallado.php",
               {
                consultaAjax:  true,
                accion   :     'ConsultarDetalle',
                wemp_pmla:     wemp_pmla,
                wfechaini:     wfechaini,
                wfechafin:     wfechafin,
                wcodusuario :  wcodusuario,
                wcodcentro  :  wcodcentro,
                woptmovto   :  woptmovto
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


       // Cambiar el titulo de la primera columna de la tabla para asignar el tipo de movimiento 
       function CambiarTitulomovimiento(objeto){
           
           $("#tbldetalle").hide();
           $("#tblmensaje").hide();
           $("#tblbuscar").hide();
           document.getElementById('titmovim').innerHTML='MOVIMIENTO '+objeto.value;
           document.getElementById('titmovimex').innerHTML='MOVIMIENTO '+objeto.value;
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
            tmpElemento.download = 'reportecargos_detallado.xls';
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

        .ui-multiselect { height:25px; overflow-x:hidden; padding:2px 0 2px 4px; text-align:left;font-size: 10pt; }
          
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
        $wtitulo  = "REPORTE DE CARGOS DETALLADO";
        encabezado($wtitulo, $wactualiz, 'clinica');
        $arr_cen      = consultarCentros  ($wbasemovhos,$conex,$wemp_pmla);
        $arr_servicio = consultarServicios ($wbasedato,$wbasemovhos,$conex,$wemp_pmla);
        $arr_usu      = consultarUsuarios ($wbasedato,$conex,$wemp_pmla);
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
          </tr>
          <tr height='30px'><td class='fila1'><b>Centro de Costos</b></td>
          <td class='fila2'>&nbsp;&nbsp;<select id='optservicio' name='optservicio' multiple='multiple'>
          <?php
            foreach( $arr_servicio as $key => $val){
              echo '<option value="' . $key .'">'.$val.'</option>';
            }
          ?>
          </select>
          </td>
          </tr>
          <tr height='30px'><td class='fila1'><b>Servicio</b></td>
          <td class='fila2'><select id='optmovto' name='optmovto' style='width:130px;font-size: 10pt;' onchange='CambiarTitulomovimiento(this)'>
          <option value=''>Seleccione</option>
          <option value='GRABAR'>GRABAR</option>
          <option value='REGRABAR'>REGRABAR</option>
          <option value='ANULAR'>ANULAR</option>
          </select>
          </td>
          </tr> 
          <tr>
          <td class='fila1'><b>Usuario que realiza el movimiento</b></td>
          <td class='fila2'><input type='text' id='autcodusu' name='autcodusu' size=70 placeholder="Ingrese Usuario"></td>
          </tr>  
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
              <th align='center' colspan='2' id='titmovim'>MOVIMIENTO</th>
              <th align='center' rowspan='2'>HIS-ING</th>
              <th align='center' colspan='2' width='20%'>PACIENTE</th>
              <th align='center' colspan='2'>ENTIDAD</th>
              <th align='center' rowspan='2'>SERVICIO</th>
              <th align='center' rowspan='2'>PROCEDIMIENTOS</th>
              <th align='center' colspan='2'>CONCEPTO</th>
              <th align='center' rowspan='2'>REG.</th>
              <th align='center' rowspan='2'>CANTIDAD</th>
              <th align='center' rowspan='2'>VALOR UNI</th>
              <th align='center' rowspan='2'>VALOR TOTAL</th>
              <th align='center' colspan='2'>USUARIO</th>
            </tr>
            <tr class='encabezadotabla'>
              <th align='center'>FECHA</th>
              <th align='center'>HORA</th>
              <th align='center'>IDENTIFICACION</th>
              <th align='center'>NOMBRE</th>
              <th align='center'>CODIGO</th>
              <th align='center'>NOMBRE</th>
              <th align='center'>CODIGO</th>
              <th align='center'>NOMBRE</th>
              <th align='center'>CODIGO</th>
              <th align='center'>NOMBRE</th>
            </tr>
          </thead>
      </table>
      <table width='1650px' id='tblexcel' name='tblexcel' class='tblexcel' style='border: 1px solid blue;display:none;' >
          <thead>
            <tr class='encabezadotabla'>
              <th align='center' colspan='2' id='titmovimex'>MOVIMIENTO</th>
              <th align='center' rowspan='2'>HIS-ING</th>
              <th align='center' colspan='2'>PACIENTE</th>
              <th align='center' colspan='2'>ENTIDAD</th>
              <th align='center' rowspan='2'>SERVICIO</th>
              <th align='center' rowspan='2'>PROCEDIMIENTOS</th>    
              <th align='center' colspan='2'>CONCEPTO</th>          
              <th align='center' rowspan='2'>REG.</th>
              <th align='center' rowspan='2'>CANTIDAD</th>
              <th align='center' rowspan='2'>VALOR UNI</th>
              <th align='center' rowspan='2'>VALOR TOTAL</th>
              <th align='center' colspan='2'>USUARIO</th> 
            </tr>
            <tr class='encabezadotabla'>
              <th align='center'>FECHA</th>
              <th align='center'>HORA</th>
              <th align='center'>IDENTIFICACION</th>
              <th align='center'>NOMBRE</th>
              <th align='center'>CODIGO</th>
              <th align='center'>NOMBRE</th>
              <th align='center'>CODIGO</th>
              <th align='center'>NOMBRE</th>
              <th align='center'>CODIGO</th>
              <th align='center'>NOMBRE</th>
            </tr>
          </thead>
      </table>
      </center>
      <center>
      <table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
        <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
      </table>
      </center>
      <input type="HIDDEN" name="arr_usu" id="arr_usu" value='<?=json_encode($arr_usu)?>'>
      <input type="HIDDEN" name="arr_cen" id="arr_cen" value='<?=json_encode($arr_cen)?>'>
      <input type="HIDDEN" name="wcodusuario" id="wcodusuario">
      <input type="HIDDEN" name="wcodcentro"  id="wcodcentro">
    </body>
    </html>