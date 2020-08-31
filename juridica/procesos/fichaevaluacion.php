<?php
include_once("conex.php");
/**********************************************************************************************************
 *
 * Programa				    :	Ficha de Autoevaluación Normativa
 * Fecha de Creación 	:	2016-07-25
 * Autor				      :	Arleyda Insignares Ceballos
 * Descripcion			  :	Diligenciamiento de la Ficha por parte del Juridico, dicho documento es complementario
 *	                    al Normograma.
 ***********************************************  Modificaciones  *****************************************
 * 2020-06-04   Arleyda Insignares C.
 *              Se adiciona la función utf8_encode y en el HTML <meta http-equiv="Content-type" content="text/
 *              html;charset=utf-8" /> 
 * 2017-04-25   Arleyda Insignares C.
 *              Se adiciona campo tipo 'file' para subir un archivo adjunto en formato: pdf, Word o Excel
 **********************************************************************************************************/

 $wactualiz = "2017-04-25";


 if(!isset($_SESSION['user']))
 {
	  echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	  return;
 }


  //********************************** Inicio  *********************************************************** 
  

  include_once("root/comun.php");

  $wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'juridica');
  $wbasetalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
  $wfecha        = date("Y-m-d");
  $whora         = (string)date("H:i:s");
  $pos           = strpos($user,"-");
  $wusuario      = substr($user,$pos+1,strlen($user));

  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************
      
      // Verificar si el Normograma se encuentra asociado a otra Ficha (Campo Ficaso)
      if (isset($_POST["accion"]) && $_POST["accion"] == "Verificarasociado"){
         
         $vresultado = 'N';

         $q      = " Select Ficcod,Ficart,Ficaso From ".$wbasedato."_000010       
                     Where Ficaso = '".$codigo_aso."' " ;
         
         $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num    = mysql_num_rows($res);
         $comusu = 0;

         if ($num > 0){
             $vresultado  = 'S' ;
         } 

         echo $vresultado;
         return;
      }


      // Grabar la Ficha y adicionar el archivo adjunto en caso de existir uno.
      if (isset($_POST["accion"]) && $_POST["accion"] == "GrabarFicha"){     
          
          $params     = $_POST; 
          
          if (trim($params["tardesart"]) == '' || trim($params["autasociar"]) == '' || trim($params["txtnumart"]) == ''){

              echo 'F';
              return;
          }    

          $arrFile    = isset($_FILES["filepdf"]) ? $_FILES["filepdf"] : "";
          $resp       = '';
          $vupload    = 'N';
          $numasociar = explode('-', $params["autasociar"]);
          $path_info  = pathinfo($arrFile["name"]);   

          if ($arrFile["name"] != '')
              $nomadjunto = trim($numasociar[1]) .'_'. trim($numasociar[2]) . '.' . $path_info['extension'];
          else
              $nomadjunto = '';  


          if  ($params["txtcodart"] > 0)
          { 
              
              $q = "  UPDATE ".$wbasedato."_000010
                      SET Ficart = '".$params["txtnumart"]."',
                          Ficdes = '".$params["tardesart"]."',
                          Ficaso = '".$numasociar[0]."',
                          Ficobs = '".$params["tarobserva"]."',
                          Ficarc = '".$nomadjunto."'                   
                      WHERE Ficcod = '".$params["txtcodart"]."' ";

              $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

          }
          else
          {
              
              $vresultado = 0; 
              $q=" SELECT MAX(CAST(Ficcod AS UNSIGNED)) as maximo From ".$wbasedato."_000010" ;
              $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
              $num = mysql_num_rows($res);

              if ($num > 0){

                  $row         = mysql_fetch_assoc($res);
                  $vmaximo     = $row['maximo']; 
                  $wmaxcodigo  = $vmaximo +1;
              } 
              else{

                  $wmaxcodigo  = 1;
              }                

              $q = " INSERT INTO ".$wbasedato."_000010 
                  (Medico,Fecha_data,Hora_data,Ficcod,Ficart,Ficdes,Ficaso,Ficobs,Ficarc,Ficest,Seguridad) 
                  VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wmaxcodigo."','".$params["txtnumart"]."', 
                  '".$params["tardesart"]."' ,'".$numasociar[0]."','".$params["tarobserva"]."','".$nomadjunto."',
                  'on','C-".$wusuario."') ";
             
              $resp = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
              
          }

          if ($resp>0){

              // Subir archivo al directorio de documentos
              if (isset($arrFile["name"]) && $arrFile["name"] != ""){

                     $vupload = 'S';

                     $rutaImagenes     = "../../juridica/documentos/" ;
                     $ruta_provisional = $arrFile["tmp_name"];
                     
                     $newRuta = $rutaImagenes . $nomadjunto;
                    
                     if (file_exists($newRuta)) {
                         unlink($newRuta);
                     }
                    
                     move_uploaded_file($ruta_provisional, $newRuta);
              }
          }


          echo $resp.'|'.$wmaxcodigo.'|'.$vupload.'|'.$nomadjunto;
          return;
      }    


      // Consulta de Centros de Costos Responsables con sus respectivos nombres      
      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultaResponsable"){  

          $q = " SELECT * From ".$codigo_cen." WHERE Ccocod in (".$codigo_res.")" ;        
          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $num = mysql_num_rows($res);
          $cont1 = 0;
          if ($num > 0)
          {   
               while($row = mysql_fetch_row($res)){ 
                  $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
                  $cont1++;                       
                  $asignado='<input type="checkbox" id="chkasignado" name="chkasignado" CHECKED>';
                  $vresultado .= '<tr class="'.$clase.'"><td>'.$row[3].'</td><td>'.$row[4].'</td><td align="center">'.$asignado.'</td><td><input type="hidden" id="txtcodcentro" name="txtcodcentro" value="'.$row[3].'"></td></tr>';
               }
          }
          echo $vresultado;
          return;
      }


      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarFicha"){             
         
         $q=" SELECT A.Ficcod, A.Ficart, A.Ficdes, A.Ficaso, A.Ficobs,
                     A.Ficarc, A.Ficest, B.Nornum, B.Nordes, C.Docdes  
              FROM ".$wbasedato."_000010 A 
                    Left join ".$wbasedato."_000008 B on A.Ficaso = B.Norcod
                    Left join ".$wbasedato."_000003 C on B.Nortdo = C.Doccod
              WHERE Ficcod = '".$codigo_art."' " ;

         $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         
         $num = mysql_num_rows($res);
         if ($num > 0)
            {
               $row        = mysql_fetch_assoc($res);      
               $vresultado = $row['Ficcod'] . "|" . $row['Ficart'] . "|" . utf8_decode($row['Ficdes']) . "|" .
                             $row['Ficaso'] . "|" . utf8_decode($row['Ficobs']) . "|" . $row['Ficest'] . "|" .
                             utf8_decode($row['Docdes']) . "|" . $row['Nornum'] ."|" . $row['Ficarc'] . "|" . utf8_decode($row['Nordes']);
            }
            
         echo $vresultado;
         return;            
      }


      // Inactivar Articulo cambiando el estado
      if (isset($_POST["accion"]) && $_POST["accion"] == "InactivarFicha"){             
         
         $q =" UPDATE ".$wbasedato."_000010
               SET Ficest = 'off'
               WHERE Ficcod = '".$codigo_art."' " ;
         $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         
         echo $resp;   
         return;         
      }


      //Consultar el Código para incrementarlo      
      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarCodigo"){                
         
         $vresultado = 0; 
         $q=" SELECT MAX(Ficcod) as maximo From ".$wbasedato."_000010" ;
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num = mysql_num_rows($res);
         if ($num > 0)
            {
               $row         = mysql_fetch_assoc($res);      
               $vresultado  = $row['maximo'];
            }   
         echo $vresultado;   
         return;
      }


      // Consultar las Fichas para el campo inicial de autocompletar
      function consultarFichaAut($wbasedato,$conex,$wemp_pmla){     
        
        $strtipvar = array();
        $q  = " SELECT Ficcod, Ficart, Ficdes"
            ."   From ".$wbasedato."_000010 "    
            ."   where Ficest ='on'";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res))
             {
               $strtipvar[$row['Ficcod']] = $row['Ficart'].'-'.($row['Ficdes']);
             }

        return $strtipvar;
      }  


      // Consultar los usuarios para el campo autocompletar
      function consultarNormograma($wbasedato,$conex,$wemp_pmla){     
        
        $strtipvar = array();
        $q  = " SELECT A.Norcod, A.Nornum, A.Nordes, A.Nortdo, A.Noruni, B.Docdes, C.Unides 
               From ".$wbasedato."_000008 A    
               Inner Join ".$wbasedato."_000003 B on  A.Nortdo = B.Doccod
               Inner Join root_000113 C on  A.Noruni = C.Unicod
               where A.Norest ='on' ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

        while($row = mysql_fetch_assoc($res)){
             $strtipvar[$row['Norcod']] =utf8_encode($row['Docdes']) .' - '. $row['Nornum'];
        }
        return $strtipvar;
      }
    
     // *****************************************         FIN PHP         ********************************************

	?>
  <!DOCTYPE html>
  <html lang="es-ES">
	<head>
		<title>Normograma - Ficha de Evaluación</title>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>	
		<script type="text/javascript" src="../../../include/root/jqueryalert.js"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />    
    <script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>

		<script type="text/javascript">
		  $(document).ready(function(){

          Autocompletar();

          $(".mostrartooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });

          $("#Consultar").on("click", function(){

               var wemp_pmla  = $("#wemp_pmla").val();
               var codigo_ant = $("#autcodart").val(); 
               var codigo_env = codigo_ant.split('-');
               var codigo_art = codigo_env[0]; 

               if ($("#autcodart").val()=='')
               {
                   alerta('Debe ingresar un Articulo para consultar');
                   return;
               }
               $.post("fichaevaluacion.php",
               {
                    consultaAjax:   true,
                    accion:         'ConsultarFicha',
                    wemp_pmla:      wemp_pmla,
                    codigo_art:     codigo_art
               }, function(respuesta){

                    vcampos = respuesta.split('|');
                    $("#txtcodart").val(vcampos[0]);
                    $("#txtnumart").val(vcampos[1]);
                    $("#tardesart").val(vcampos[2]);
                    if (vcampos[3]!==''){
                        $("#autasociar").val(vcampos[3]+' - '+vcampos[6]+' - '+vcampos[7]+' - '+vcampos[9]);
                        $("#autasociar").attr("nombre",vcampos[3]+' - '+vcampos[6]+' - '+vcampos[7]+' - '+vcampos[9]); 
                    }    
                    else
                        $("#autasociar").val('');

                    $("#tarobserva").val(vcampos[4]);
                    $("#txtestado").val(vcampos[5]);
                    $("#txtfile").val(vcampos[8]);
                    $("#filepdf").attr('disabled',false);
                    $("#txtnumart,#tardesart,#autasociar,#tarobserva,#filepdf").attr("readonly", false);
                    $("#txtnumart,#tardesart,#autasociar,#tarobserva,#txtfile,#filepdf").css("background-color", "white");
               });

          });


         $("#Nuevo").on("click", function(){

           // Verificar que si esta en modo Edición, no se cancele sin autorización del Usuario
           if ($("#wedicion").val() == 'S')
           {               
              jConfirm("Desea Cancelar La Edici\u00F3n del documento Activo?","Confirmar", function(respuesta){
                  if (respuesta == true) {
                      Limpiar();
                  }
                  else{
                      return;
                  }
              });
           }
           else
           {
              Limpiar();
           }
            
         });

         
         $("#Inactivar").on("click", function(){

            if ($("#txtcodart").val()=='')
               {
                alerta('Debe ingresar un Articulo');
                return;
               }
               
               jConfirm("Esta seguro de Anular la Ficha?","Confirmar", function(respuesta){  
                   if (respuesta == true)
                   { 
                       var wemp_pmla  = $("#wemp_pmla").val();
                       var codigo_art = $("#txtcodart").val();
                       $.post("fichaevaluacion.php",
                       {
                          consultaAjax:   true,
                          accion:         'InactivarFicha',
                          wemp_pmla:      wemp_pmla,
                          codigo_art:     codigo_art
                       }, function(respuesta){
                          if (respuesta=1){ 
                              alerta('La Ficha ha sido Anulada');
                          }
                       });
                   }    
               });
         });



         $("#Salir").on("click", function(){

          if(confirm("Esta seguro de salir?") == true)
             window.close();
          else
             return false;

         });


         $("#frmficha").on("submit", function(e){
               
                e.preventDefault();
                var f = $(this);
                var formData  = new FormData(document.getElementById("frmficha"));
                var inputFile = document.getElementById("filepdf");
                var campodes  = document.getElementById("tardesart");
        
                formData.append("filepdf", inputFile.files[0]);
                formData.append("consultaAjax", "on");
                formData.append("accion", "GrabarFicha");
            
                $.ajax({
                    url         : "fichaevaluacion.php",
                    type        : "post",
                    dataType    : "html",
                    data        : formData,
                    cache       : false,
                    contentType : false,
                    processData : false
                }).done(function(respuesta){

                    if  (respuesta == 'F'){

                         alerta('Falta diligenciar informaci\u00F3n');
                    }      

                    else{  

                        var resul = respuesta.split('|');

                        if ($("#txtcodart").val() == ''){
                            $("#txtcodart").val(resul[0]);
                        }
                        if (resul[0] > 0){ 
                            
                            alerta('La Ficha ha sido Grabada');
                            
                            $("#wedicion").val('N');                        

                            if (resul[2] == 'S')
                               $("#txtfile").val(resul[3]);
                            else 
                               $("#txtfile").val('');
                        }
                    }
                });

          });


	  	});  // Finalizar Ready()

       // **************************************   Inicio Funciones Javascript   *****************************************

      function Limpiar(){

          $("input[type=text]").val('');
          $("#tardesart").val(''); 
          $("#tarobserva").val('');
          $("#filepdf").val(''); 
          $("#txtfile").val('');
          $('#txtnumart').focus();
          $("#filepdf").attr('disabled',false);
          $("#txtnumart,#tardesart,#autasociar,#tarobserva").attr("readonly", false);
          $("#txtnumart,#tardesart,#autasociar,#txtfile,#filepdf,#tarobserva").css("background-color", "white");
          $("#wedicion").val('S');
      }

      function Autocompletar(){

          //  *****    Asignar busqueda Autocompletar Fichas      
          var arr_fic = eval('(' + $('#arr_fic').val() + ')');
          var ficha   = new Array();
          var index   = -1;
          for (var cod_fic in arr_fic)
          {
              index++;
              ficha[index]           = {};
              ficha[index].value     = cod_fic+'-'+arr_fic[cod_fic];
              ficha[index].label     = cod_fic+'-'+arr_fic[cod_fic];
              ficha[index].codigo    = cod_fic;
              ficha[index].nombre    = cod_fic+'-'+arr_fic[cod_fic];
          }            

          $("#autcodart").autocomplete({ 
          source: ficha,         
          autoFocus: true,            
          select:     function( event, ui ){
              var cod_sel = ui.item.codigo;
              var nom_sel = ui.item.nombre;
              $("#autcodart").attr("codigo",cod_sel);                
              $("#autcodart").attr("nombre",nom_sel);                          
          }
          }); 

          $('#autcodart').on({
              focusout: function(e) { 
                  $(this).val($(this).attr("nombre"));
              }
          });

          //  *************      Asignar busqueda Autocompletar Normograma      
          var arr_nor  = eval('(' + $('#arr_nor').val() + ')');
          var normograma = new Array();
          var index   = -1;
          for (var cod_nor in arr_nor)
          {
              index++;
              normograma[index]           = {};
              normograma[index].value     = cod_nor+'-'+arr_nor[cod_nor];  
              normograma[index].label     = cod_nor+'-'+arr_nor[cod_nor];  
              normograma[index].codigo    = cod_nor;
              normograma[index].nombre    = cod_nor+'-'+arr_nor[cod_nor];
          }            

          $("#autasociar").autocomplete({ 
          source: normograma,         
          autoFocus: true,            
          select:     function( event, ui ){
              var cod_sel = ui.item.codigo;
              var nom_sel = ui.item.nombre;
              $("#autasociar").attr("codigo",cod_sel);
              $("#autasociar").attr("nombre",nom_sel);    
              Validaraso(cod_sel);
          }

          });   


          $('#autasociar').on({
              focusout: function(e) { 

                  if (($(this).val() !== $(this).attr("nombre")) && $(this).val() !=='')
                     $(this).val($(this).attr("nombre"));
              }
          });

         }


       // validar si el documento ya tiene una ficha de evaluación asignada
       function Validaraso(codigoaso)
       {
         var wemp_pmla = $("#wemp_pmla").val();
         
         var wnumdoc   = codigoaso.split('-');

         $.post("fichaevaluacion.php",
             {
                consultaAjax:   true,
                accion:         'Verificarasociado',
                wemp_pmla:      wemp_pmla,
                codigo_aso:     wnumdoc[0]
             }, function(respuesta){

                if (respuesta=='S'){ 
                    alerta('El Normograma se encuentra asociado a otra Ficha');
                    $("#autasociar").val('');
                }
           });
       }  

       function mostrarTooltip( celda ){
          if( !celda.tieneTooltip ){
            $( "*", celda ).tooltip();
            celda.tieneTooltip = 1;
          }
       }

	     // ********************************  FUNCION Sacar un mensaje de alerta con formato predeterminado  *************
			 function alerta(txt){
				$("#textoAlerta").text( txt );
				$.blockUI({ message: $('#msjAlerta') });
					setTimeout( function(){
								   $.unblockUI();
								}, 1800 );
			 }

          // **************************************   Fin Funciones Javascript   ********************************************
	    </script>	
      <style type="text/css">

        input [type=text], textarea {
             font-family: arial;font-size: 14;
        }

        .button{
          color:#2471A3;
          font-weight: bold;
          font-size: 12,75pt;
          width: 90px; height: 27px;
          background: rgb(240,248,252);
          background: -moz-linear-gradient(top,  rgba(240,248,252,1) 0%, rgba(236,246,254,1) 50%, rgba(219,238,251,1) 51%, rgba(220,237,254,1) 100%); 
          background: -webkit-linear-gradient(top,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%); 
          background: linear-gradient(to bottom,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%);
          filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f8fc', endColorstr='#dcedfe',GradientType=0 );
          border: 1px solid #ccc; 
              border-radius: 8px;
              box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; 
        }        

        .button:active {
          background-color: #3e8e41;
          box-shadow: 0 5px #666;
          transform: translateY(4px); 
        }      
      </style>	
  </head>
	<body>		
		<?php 		  
		  $wtitulo  ="FICHA DE EVALUACION";
		  encabezado($wtitulo, $wactualiz, 'clinica');
		  $arr_nor  = consultarNormograma  ($wbasedato,$conex,$wemp_pmla);
      $arr_fic  = consultarFichaAut  ($wbasedato,$conex,$wemp_pmla);    
      $mensaje="<a title='Subir un archivo adjunto en Formato PDF, Word o Excel'>";
		?>
    <form action='' id='frmficha' accept-charset="UTF-8">
      <table align ='center' width='1000px'>
        <tr class='encabezadotabla'>
        <td>Consultar Ficha</td>
        <td width="15px"><input type='text' id='autcodart' name='autcodart' size=120 ></td>
        </tr>
      </table>
      </br></br>
      <center>
      <table>
        <tr>
        <td>&nbsp;&nbsp;<input type='button' id='Consultar' class='button' value='Consultar'></td>
        <td>&nbsp;&nbsp;<input type='button' id='Nuevo'     class='button' value='Nuevo' ></td>
        <td>&nbsp;&nbsp;<input type='submit' id='Grabar'    class='button' value='Grabar'></td>
        <td>&nbsp;&nbsp;<input type='button' id='Inactivar' class='button' value='Anular'></td>
        <td>&nbsp;&nbsp;<input type='button' id='Salir'     class='button' value='Salir' ></td>
        </tr>
      </table>
      </br></br>
      <CENTER>   
      </table>   
      <table width='1000px' style='border: 1px solid blue'>
        <tr class=fila1>
          <input type='hidden' id='txtcodart' name='txtcodart' class='fila2' size=130 readonly maxlength="200">
        </tr>
        <tr class=fila1>
      		<td width="50px"><b>Art&iacute;culo: </b></td>
      		<td width="950px" colspan=2><input type='text' id='txtnumart' name='txtnumart'  size=130 readonly placeholder="Ingrese Numero de uno o varios art&iacute;culos" STYLE="background-color: lightgray;"></td>
        </tr>
        <tr class=fila1>
          <td ><b>Descripci&oacute;n: </b></td>
          <td colspan=2><textarea id='tardesart' name="tardesart" cols=127 rows=5 readonly placeholder="Ingrese descripci&oacute;n de la Ficha" STYLE="background-color: lightgray;"></textarea></td>
        </tr>
        <tr class=fila1>
      		<td><b>Asociado a: </b></td>
          <td colspan=2><input type='text' id='autasociar' name='autasociar' size=130 onblur="Validaraso" readonly placeholder="Ingrese palabra relacionada al documento del Normograma" STYLE="background-color: lightgray;"></td>
        </tr>
        <tr class=fila1>
          <td ><b>Observaciones del Jur&iacute;dico: </b></td>
          <td colspan=2><textarea id='tarobserva' name="tarobserva" cols=127 rows=10 readonly placeholder="Ingrese observaciones" STYLE="background-color: lightgray;"></textarea>  </td>
        </tr>
        <tr class=fila1>
          <td onMouseover='mostrarTooltip(this);'><?=$mensaje?><b>Subir Archivo </b></td>
          <td ><input type='file' id='filepdf' name="filepdf"  size=40 accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" disabled STYLE="background-color: lightgray;"></td>
          <td ><b>Nombre Adjunto </b><input type='text' id='txtfile' name="txtfile" size=53 readonly STYLE="background-color: lightgray;"></td>
        </tr>
        <tr class=fila1 style='display:none;'>
        <td><b>Estado </b></td><td><input type='hidden' id='txtestado' class='fila2' name='txtestado' size=119 readonly></td>
        </tr>
      </table>
      </CENTER>
      <center>
      <br><br>    
  		</br></br> 
  		<table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
  		  <tr><td>No hay usuarios con el codigo ingresado</td></tr>
  		</table>
  		<div id='msjAlerta' style='display:none;'>
    		<br><img src='../../images/medical/root/Advertencia.png'/>
    		<br><br><div id='textoAlerta'></div><br><br>
  		</div>
  		<div style="display:none;" id="img_bus">Actualizando en Matrix.. <img width="13" height="13" border="0" src="../../images/medical/ajax-loader9.gif">
  		</div>
  		</center>
      <input type="HIDDEN" name="arr_nor"   id="arr_nor"  value='<?=json_encode($arr_nor)?>'>
      <input type="HIDDEN" name="arr_fic"   id="arr_fic"  value='<?=json_encode($arr_fic)?>'>
      <input type="HIDDEN" name="wedicion"  id="wedicion" value="N"> 
      <input type="HIDDEN" name="wideusu"   id="wideusu">
      <input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value="<?php echo $wemp_pmla;?>">
    </Form>
	</body>
	</html>