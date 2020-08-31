<?php
include_once("conex.php");
/***********************************************************************************************************
 *
 * Programa				    :	Ficha de Autoevaluación Normativa
 * Fecha de Creación 	:	2016-09-12
 * Autor				      :	Arleyda Insignares Ceballos
 * Descripcion			  :	Diligenciamiento de la Ficha de Evaluación del documento jurídico, dicho documento 
 *	                    es complementario al Normograma.
 *                     
 ****************************** Modificaciones *************************************************************
 * 2020-04-17  Arleyda Insignares C.
 *             Se adiciona función utf8_encode() para correcta visualización de texto.
 */
 
 $wactualiz = "2020-04-17";

 if(!isset($_SESSION['user']))
 {
	  echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	  return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');

  //********************************** Inicio  ***********************************************************
  
  include_once("root/comun.php");

  $wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'juridica');
  $wbasetalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');  
  $wfecha        = date("Y-m-d");
  $whora         = (string)date("H:i:s");
  $pos           = strpos($user,"-");
  $wusuario      = substr($user,$pos+1,strlen($user));
  $wcencos       ='';

  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************

  //Consultar el usuario de la unidad para mostrarlo en pantalla
     
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarCodusuario"){
        
        $rescodigo='N';

        if ($codigouni != '')
        {
            $rescodigo='S';
            $q  =" SELECT Uniusu from root_000113 "
                ."  where Unicod =".$codigouni;

            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            
            $num = mysql_num_rows($res);
            
            if ($num > 0)
               {
                  $row      = mysql_fetch_assoc($res);     
                  $rescodigo  = $row['Uniusu']; 
               }   
        }
        return $rescodigo;
     }

  // Proceso de Grabado
     if (isset($_POST["accion"]) && $_POST["accion"] == "GrabarUnidad"){
          
          // Verificar si es un nuevo registro (seleccionar insert o update)
          if ($wnumuni == 0)
          {
             // Seleccionar Maximo Codigo 
             $q  ="SELECT MAX(CAST(Unicod AS UNSIGNED)) as maximo From root_000113" ;
             $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
             $num = mysql_num_rows($res);
             if ($num > 0)
             {
                    $row         = mysql_fetch_assoc($res);     
                    $vmaximo     = $row['maximo']; 
                    $wmaxcodigo  = $vmaximo +1;
             }    

             // Insertar registro nueva Unidad
             $q = " INSERT INTO root_000113 (Medico,Fecha_data,Hora_data,Unicod,Unides,Unitem,Uniusu,Uniema,Uniest,Seguridad) 
                    VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wmaxcodigo."','".$wdesuni."','".$wtema."',
                            '".$wcodusu."','".$wemail."','on','C-".$wusuario."') ";                    
             $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                          
             //Insertar Detalle - Centros de Costos relacionados
             for ($x=1;$x<count($wuniarray); $x++){
                  
                  $q = " INSERT INTO root_000114 (Medico,Fecha_data,Hora_data,Cencod,Cenuni,Cenest,Seguridad) 
                         VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wuniarray[$x]."','".$wmaxcodigo."','on','C-".$wusuario."') ";         
             
                  $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());     
             }

             echo $wmaxcodigo.'|I';
          }
          else
          {    
              // Actualizar la tabla de Unidades
              $q = " UPDATE root_000113 
                   SET Unides = '".$wdesuni."',
                       Unitem = '".$wtema."',
                       Uniusu = '".$wcodusu."',
                       Uniema = '".$wemail."',
                       Uniest = '".$westado."' 
                   WHERE Unicod = '".$wnumuni."' " ;

              $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
              
              // Borrar Detalle - Centros de Costos relacionados para que se ingresen nuevamente
              $q = "DELETE FROM root_000114 WHERE Cenuni = '".$codigo_uni."'" ; 
              $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

              // Grabar Detalle - Centros de Costos relacionados
              for ($x=1;$x<count($wuniarray); $x++){
                  
                  $q = " INSERT INTO root_000114 (Medico,Fecha_data,Hora_data,Cencod,Cenuni,Cenest,Seguridad) 
                         VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wuniarray[$x]."','".$wnumuni."','on','C-".$wusuario."') ";         
             
                  $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());                    
              }
              echo $resp.'|M';
          }
          
          return;
     }

     // Eliminar la Relación de Unidad con Centro de Costos
     if (isset($_POST["accion"]) && $_POST["accion"] == "EliminarCentrouni"){
        
        $q ="DELETE FROM root_000114 
             WHERE Cenuni = '".$codigo_uni."' AND 
                   Cencod = '".$codigo_cen."' "; 

        $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        echo $resp;
        return;  
     }

     // Consultar por unidad   
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarUnidad"){

        // Consultar Normograma
        $respuesta  = array();
        $vresultado = 'N';

        $q      = " Select Unicod,Unides,Unitem,Uniusu,Uniema,Uniest,Codigo,Descripcion 
                    From root_000113       
                    Inner join usuarios on Uniusu = Codigo 
                    WHERE Unicod = '".$codigo_uni."' AND Activo='A' " ;
         
        $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num    = mysql_num_rows($res);
        $comusu = 0;

        if ($num > 0){
            $row        = mysql_fetch_assoc($res);      
            $vresultado = $row['Unicod'] . "|" . ($row['Unides']) . "|" . $row['Unitem'] . "|" .
                          ($row['Uniusu']) . "|" .$row['Uniema'] . "|" .$row['Uniest'] . "|" .
                          ($row['Descripcion']);
            
            $respuesta['vresultado'] = $vresultado;
        }

        //Consultar Detalle - Centros de Costos relacionados
        $q      = " Select Cenuni,Cencod,Cenest From root_000114
                    WHERE  Cenuni = '".$codigo_uni."' " ;
        
        $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num    = mysql_num_rows($res);
        $comusu = 0;

        if ($num > 0){
            $array_     = unserialize(base64_decode($array_cen));
            $cencos     = explode(',', $centrosel);
            $vresultado = '';
            $cont1      = 0;
            $vdetalle   = '';
            while($row = mysql_fetch_assoc($res))
             {
                foreach ($array_ as $centros => $nombre) {                 
                   if ($centros == $row['Cencod'])
                   {
                       $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
                       $cont1++;
                       $vdetalle .= '<tr class="'.$clase.'"><td>'.$centros.'</td><td>'.$nombre.'</td><td align="center"><input type="button" class="button" value="Eliminar" onclick="EliminarCentro(this,'.$centros.');"></td></tr>';
                   }             
                }
            }
            $respuesta['vdetalle'] = $vdetalle;
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
               $strtipvar[$row['codigo']] = utf8_encode($row['descripcion']);
           }
        return $strtipvar;

      }


  // Consultar los usuarios para el campo autocompletar
     function consultarUnidades($wbasedato,$conex,$wemp_pmla){
        
        $strtipvar = array();
        $q  = " SELECT Unicod, Unides"
          ."   From root_000113 "      
          ."   where Uniest ='on'";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res))
             {
               $strtipvar[$row['Unicod']] = utf8_encode($row['Unides']);
             }
        return $strtipvar;
      }


  // Consultar los Temas para el campo option   
     function consultarTemas($wbasedato,$conex,$wemp_pmla){
       
        $strtipvar = array();
        $q  = " SELECT Temcod, Temdes"
          ."   From root_000115 "      
          ."   where Temest ='on'";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res))
             {
               $strtipvar[$row['Temdes']] = utf8_encode($row['Temdes']);
             }
        return $strtipvar;
     }


  // Consultar todos los Centros de Costos para el campo autocompletar
     function consultarCentros($wbasetalhuma,$conex,$wemp_pmla){     
        
        $strtipvar = array();
        $q = "  SELECT  Empdes,Emptcc
                FROM    root_000050
                WHERE   Empcod = '".$wemp_pmla."'";
        $res = mysql_query($q,$conex);

            if($row = mysql_fetch_array($res))
            {
                $tabla_CCO = $row['Emptcc'];
                $wcencos = $tabla_CCO;
                switch ($tabla_CCO)
                {
                    case "clisur_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    clisur_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";

                            break;
                    case "farstore_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    farstore_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    case "costosyp_000005":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                        FROM    costosyp_000005 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Cconom";
                            break;
                    case "uvglobal_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    uvglobal_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    default:
                            $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                        FROM    costosyp_000005 AS tb1
                                                INNER JOIN
                                                ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Cconom";
                }

          $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
          while($row = mysql_fetch_assoc($res))
          {
                $strtipvar[$row['codigo']] = utf8_encode($row['nombre']);
          }
        }

        return $strtipvar;
      }
     
     // *****************************************         FIN PHP         ********************************************

  ?>
  <html>
  <head>
    <title>Normograma</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />   
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>  
    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
    <script type="text/javascript">
      $(document).ready(function(){

      // Asignar array al campo autocompletar centro de costos
      var arr_cen  = eval('(' + $('#arr_cen').val() + ')');
      var centros = new Array();
      var index   = -1;
      for (var cod_cen in arr_cen)
      {
              index++;
              centros[index]                = {};
              centros[index].value          = cod_cen;
              centros[index].label          = cod_cen+'-'+arr_cen[cod_cen];
              centros[index].codigo         = cod_cen;
      }            

      $("#autcodcen").autocomplete({
      source: centros,          
      autoFocus: true,            
      select:     function( event, ui ){
              var cod_sel = ui.item.codigo;
              var nom_sel = ui.item.label;
              $("#autcodcen").attr("codigo",cod_sel);
              SeleccionCentro(cod_sel,nom_sel);
      }
      });   
      
      
      // Asignar array al campo autocompletar Unidades
      var arr_uni  = eval('(' + $('#arr_uni').val() + ')');
      var unidades = new Array();
      var index    = -1;
      for (var cod_uni in arr_uni)
      {
              index++;
              unidades[index]                =  {};
              unidades[index].value          =  cod_uni;
              unidades[index].label          =  cod_uni+'-'+arr_uni[cod_uni];
              unidades[index].codigo         =  cod_uni;
              unidades[index].nombre         =  arr_uni[cod_uni];
      }            

      $("#autcoduni").autocomplete({
      source: unidades,          
      autoFocus: true,            
      select:     function( event, ui ){
              $( "#autcoduni" ).val(ui.item.nombre);
              $( "#autcoduni" ).attr('valor', ui.item.value);
              $( "#autcoduni" ).attr('label', ui.item.label);       
              $( "#wcodunidad" ).val(ui.item.codigo);                        
              return false;
      }
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
              $( "#autcoduni" ).attr('codigo',ui.item.codigo);
              $( "#autcoduni" ).attr('nombre',ui.item.nombre);
              $( "#wcodusuario" ).val(ui.item.codigo);
              $( "#wusunidad" ).val(ui.item.codigo);
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
                    $( "#wusunidad" ).val('');
                }
                else
                {
                    $(this).val($(this).attr("label"));
                }
            }
      });

    });  // Finalizar Ready()


     function Nuevo()
     {
       if ($("#wedicion").val() == 'S'){                         
            jConfirm("Desea Cancelar La Edici\u00F3n del documento Activo?","Confirmar", function(respuesta){
               if (respuesta == false) {
                  return;
               }
               else{
                Limpiar();
               }

            });
          }
       else{
           Limpiar();
       }
     } 

     
     function Limpiar()
     {
        $("input[type=text]").val('');
        $("#txtnumuni").val(''); 
        $("#txtdesuni").val('');
        $("#autcodusu").val('');
        $("#wcodunidad").val('');
        $("#wcodusuario").val('');
        $("#txtemail").val('');
        $("#optestado").val('on');
        $("#wedicion").val('S');
        $("#wideuni").val(0);
        $('#txtnumuni').focus();
        $("#optestado,#opttema").attr("readonly", false);
        $("#optestado,#opttema").css("background-color", "white");
        $("input[type=text]").attr("readonly", false);
        $("#wusunidad").attr("readonly", true);
        $("input[type=text]").css("background-color", "white");
        $("#txtnumuni").attr("readonly", true);
        $("#txtnumuni").css("background-color", "lightgray");
        $("#txtdesuni").focus();
        $("#tbllistacentro  tbody ").remove(0);
     }

      
     function Grabar()
     {
         var wemp_pmla  = $("#wemp_pmla").val();         
         var codigo_ant = $("#wcodunidad").val();
         var codigo_env = codigo_ant.split('-');
         var codigo_uni = codigo_env[0];
         var wemail     = $("#txtemail").val();
         
         resemail = validarEmail(wemail);

         if (resemail == '1')
         {
            alerta('El Email digitado es incorrecto');
            return;
         }

         if ($("#txtdesuni").val() =='' || $("#autcodusu").val() =='' || $("#opttema").val() =='')
         {
              alerta('Falta diligenciar informaci\u00F3n');
              return;
         }  

         var wuniarray = [];
         var conuni    = 0;
         $('#tbllistacentro tr:gt(0)').each(function() {
             
             if (conuni>0){
                wuniarray[conuni] = $(this).eq(0).find("td:eq(0)").text();
             }
             conuni++;
         });

         $.post("maestrounidades.php",
         {
            consultaAjax: true,
            accion    :  'GrabarUnidad',
            wemp_pmla :   wemp_pmla,
            codigo_uni:   codigo_uni,
            wnumuni   :   $("#txtnumuni").val(),
            wdesuni   :   $("#txtdesuni").val(),
            wcodusu   :   $("#wcodusuario").val(),
            wemail    :   $("#txtemail").val(),
            wtema     :   $("#opttema").val(),
            westado   :   $("#optestado").val(),
            wuniarray :   wuniarray

         }, function(respuesta){
            var vrespu = respuesta.split('|');
            if (respuesta>0);
               alerta('Grabado Exitoso');
               $("#wedicion").val('N');
            if (vrespu[1] == 'I')
               $("#txtnumuni").val(vrespu[0]);

         });
     }

     function Consultar()
     {
         var wemp_pmla  = $("#wemp_pmla").val();

         if ($("#wcodunidad").val() == '' || $("#autcoduni").val() == '')
         {
           alerta('Debe ingresar una Unidad para consultar');
           return;
         }

         // Verificar que si esta en modo Edición, no se cancele sin autorización del Usuario
         if ($("#wedicion").val() == 'S')
         {               
            jConfirm("Desea Cancelar La Edici\u00F3n del documento Activo?","Confirmar", function(respuesta){
                if (respuesta == true) {
                    Cargar($("#wcodunidad").val(),wemp_pmla);
                }
                else{
                    return;
                }
            });
         }
         else
            Cargar($("#wcodunidad").val(),wemp_pmla);

     } 


    function Cargar(codigo_uni,wemp_pmla)
    {

      var array_cen   = $("#arr_cen2").val();
      
      $.post("maestrounidades.php",
             {
              consultaAjax:   true,
              accion:         'ConsultarUnidad',
              wemp_pmla :     wemp_pmla,
              codigo_uni:     codigo_uni,
              array_cen :     array_cen 
             }, function(respuesta){
                vcampos = respuesta.vresultado.split('|');
                $("#txtnumuni").val(vcampos[0]);
                $("#txtdesuni").val(vcampos[1]);
                $("#opttema").val(vcampos[2]);
                $("#autcodusu").val(vcampos[6]);
                $("#txtemail").val(vcampos[4]);              
                $("#txtestado").val(vcampos[5]);
                $("#wedicion").val('S');
                $("#wcodusuario").val(vcampos[3]);
                $("#wusunidad").val(vcampos[3]);
                $("input[type=text]").attr("readonly", false);
                $("input[type=text]").css("background-color", "white");
                $("#optestado").attr("readonly", false);
                $("#optestado").css("background-color", "white");
                $("#opttema").attr("readonly", false);
                $("#opttema").css("background-color", "white");
                $("#txtnumuni").attr("readonly", true);
                $("#txtnumuni").css("background-color", "lightgray");
                
                //Cargar lista de Centros de Costos Relacionados
                $("#tbllistacentro  tbody ").remove(0);
                $("#tbllistacentro").append(respuesta.vdetalle);
                $("#tbllistacentro").show();

           },"json");
    }


    function SeleccionCentro(codigo,nombre)
    {
      var wemp_pmla  = $("#wemp_pmla").val();
      var codigo_env = nombre.split('-');
      var nombrecen  = codigo_env[1];
      var vencoduni  = '0';

      var table     = document.getElementById('tbllistacentro');
      var colCount  = document.getElementById('tbllistacentro').rows[1].cells.length; 
      var rowCount  = table.rows.length;
            
      $("#autcodcen").html('');
      respuesta = '<tr class="fila1"><td width="50">'+codigo+'</td><td width="150">'+nombrecen+'</td><td align="center">&nbsp;<input type="button" class="button" value="Eliminar" onclick="EliminarCentro(this,'+codigo+');"></td></tr>';
      $("#tbllistacentro").show();
      $("#tbllistacentro").append(respuesta);
    }


    function EliminarCentro(obj,codigo_cen)
    {
      var wemp_pmla  = $("#wemp_pmla").val();
      var codigo_ant = $("#wcodunidad").val(); 
      var codigo_env = codigo_ant.split('-');
      var codigo_uni = codigo_env[0]; 

      $.post("maestrounidades.php",
             {
              consultaAjax:   true,
              accion:         'EliminarCentrouni',
              wemp_pmla:      wemp_pmla,
              codigo_cen:     codigo_cen,
              codigo_uni:     codigo_uni
             }, function(respuesta){
                if (respuesta==1){
                   var row  = obj.parentNode.parentNode;
                   row.parentNode.removeChild(row);}
             });
    }


    function validarEmail(email)
    {
      var res='';
      if (email !== '')
      {  
          var re=/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/;
          
          if (!re.exec(email)) {
              alerta('El Email digitado es incorrecto');
              res= '1';
          }    
      }
      return res;
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
              box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }        

        .button:active {
          background-color: #3e8e41;
          box-shadow: 0 5px #666;
          transform: translateY(4px);      
    </style>  
  </head>
  <body>    
    <?php 
      echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
      $wtitulo  ="UNIDADES";
      encabezado($wtitulo, $wactualiz, 'clinica');
      $arr_cen  = consultarCentros  ($wbasetalhuma,$conex,$wemp_pmla);
      $arr_uni  = consultarUnidades ($wbasedato,$conex,$wemp_pmla);
      $arr_tem  = consultarTemas    ($wbasedato,$conex,$wemp_pmla);
      $arr_usu  = consultarUsuarios ($wbasedato,$conex,$wemp_pmla);?>
    <table align ='center' width='1000px'>
    <tr class='encabezadotabla'>
    <td>Consultar Unidad</td>
    <td width="15px"><input type='text' id='autcoduni' name='autcoduni' codigo='' nombre='' size=120 ></td>
    </tr>
    </table>
    </br></br>
    <center><table>
    <tr>
    <td>&nbsp;&nbsp;<input type='submit' name='Consultar' class='button' value='Consultar' onclick='Consultar()'></td>
    <td>&nbsp;&nbsp;<input type='submit' name='Nuevo' class='button' value='Nuevo' onclick='Nuevo()'></td>
    <td>&nbsp;&nbsp;<input type='submit' name='Grabar' class='button' value='Grabar' onclick='Grabar()'></td>
    <td>&nbsp;&nbsp;<input type='submit' name='Salir' class='button' value='Salir' onclick='cerrarVentana()'></td>
    </tr>
    </table>
    </br></br>
    <CENTER>       
    <table width='1000px' style='border: 1px solid blue'>
    <tr class=fila1>
    <input type='hidden' id='txtcodart' name='txtcodart' class='fila2' size=119 readonly >
    </tr>
    <tr class=fila1>
    <td width="50px"><b>C&oacute;digo </b></td>
    <td width="950px"><input type='text' id='txtnumuni' name='txtnumuni'  size=50 readonly STYLE="background-color: lightgray;"></td>
    </tr>
    <tr class=fila1>
    <td ><b>Nombre Unidad </b></td>
    <td ><input type='text' id='txtdesuni' name='txtdesuni'  size=119 readonly STYLE="background-color: lightgray;text-transform:uppercase;" ></td>
    </tr>
    <tr class=fila1>
    <td><b>Persona Responsable </b></td><td><input type='text' id='autcodusu' name='autcodusu' size=50  codigo='' nombre='' readonly STYLE="background-color: lightgray;" >    
    <b>&nbsp;&nbsp; Usuario &nbsp;&nbsp;</b><input type='text' id='wusunidad' name='wusunidad'  size=20 readonly STYLE="background-color: lightgray;"></td>
    </tr>
    <tr class=fila1>
    <td ><b>Email </b></td>
    <td ><input type='text' id='txtemail' name="txtemail" size=50  readonly STYLE="background-color: lightgray;" onChange="validarEmail(this.value);" > </td>
    </tr>
    <tr class=fila1>
    <td ><b>Tema </b></td>
    <td ><select id='opttema' name='opttema' readonly STYLE="background-color: lightgray;">
    <?php
      echo '<option ></option>';
      foreach( $arr_tem as $key => $val){
           echo '<option value="' . $key .'" >'.$val.'</option>';
      }
    ?>
    </select></td>
    </tr>
    <tr class=fila1>
    <td><b>Estado </b></td><td><select id='optestado' name='optestado' readonly STYLE="background-color: lightgray;"><option>on</option><option>off</option></select></td>
    </tr>
    </table>
    <br></br>    
    <table border=1 width='1000px' id='tblconsultar' name='tblconsultar'>
      <tr class=fila1 style="background-color: lightyellow">
      <td width="100px" align='center' colspan="3"><b>Adicionar Centro de Costos</b></td><td width="300px" align='center'><input type='text' id='autcodcen' name='autcodcen' size='60'></td>
      </tr> 
    </table> 
    <table border=1 width='1000px' id='tbllistacentro' name='tbllistacentro' style='display:none;'>
      <thead><tr class=fila1 style="background-color: lightyellow">
      </tr>   
      <tr class=encabezadotabla>
      <td width="50px" align="center">Codigo</td><td width="50px" align="center">Descripci&oacute;n</td><td width="50px" align="center">Eliminaci&oacute;n</td>
      </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
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
    <input type="HIDDEN" name="arr_usu" id="arr_usu" value='<?=json_encode($arr_usu)?>'>
    <input type="HIDDEN" name="arr_uni" id="arr_uni" value='<?=json_encode($arr_uni)?>'>
    <input type="HIDDEN" name="arr_cen" id="arr_cen" value='<?=json_encode($arr_cen)?>'>
    <input type="HIDDEN" name="arr_cen2" id="arr_cen2" value='<?=base64_encode(serialize($arr_cen))?>'>
    <input type="HIDDEN" name="wideuni" id="wideuni" value='0'>
    <input type="HIDDEN" name="wedicion" id="wedicion" value="N" onblur='Limpiar();'>
    <input type="HIDDEN" name="wnueusuario" id="wnueusuario">
    <input type="HIDDEN" name="wcodunidad"  id="wcodunidad">
    <input type="HIDDEN" name="wcodusuario" id="wcodusuario">
  </body>
  </html>  
