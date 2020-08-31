<?php
include_once("conex.php");
 /**********************************************************************************************************
 *
 * Programa				    :	Maestro de Temas
 * Fecha de Creación 	:	2016-09-12
 * Autor				      :	Arleyda Insignares Ceballos
 * Descripcion			  :	Diligenciamiento de los temas con su respectivo correo electrónico y responsable.
 *                      El correo electrónico será el Remitente cuando se envíe un Email automático en el
 *                      momento de grabar un documento.
 **********************************************************************************************************/
 
  $wactualiz = "2017-01-24";

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
  $wfecha        = date("Y-m-d");
  $whora         = (string)date("H:i:s");
  $pos           = strpos($user,"-");
  $wusuario      = substr($user,$pos+1,strlen($user));
  $wcencos       = '';

  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************

     //Consultar el usuario de la unidad para mostrarlo en pantalla
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarTemaxUsuario"){
        
        $resultado='';

        $q =" SELECT Temdes,Temres,Temcar,Temema,Temcla from ".$wbasedato."_000013 Where Temcod ='".$wvalortem."'";

        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        $num = mysql_num_rows($res);
        
        if ($num > 0)
           {
              $row        = mysql_fetch_assoc($res);     
              $resultado  = $row['Temdes'] . "|" .
                            $row['Temres'] . "|" .
                            $row['Temcar'] . "|" .
                            $row['Temema'] . "|" .
                            $row['Temcla'] ; 
           }   

        echo $resultado;
        return;
     }


      // Consultar el Usuario del Tema para comparar con el usuario logueado
      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarPermisos"){

           $respuesta   = 'S';

           $q      = " Select Temusu From ".$wbasedato."_000013
                       WHERE Temusu = '".$wusuario."' " ;
        
           
           $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
           $num    = mysql_num_rows($res);
           
           if ($num > 0)
           {
              $respuesta = 'S'; 
           } 
           else
           {
              $respuesta = 'N';             
           }       

           echo $respuesta;
           return;          
     }   


     // Proceso de Grabado
     if (isset($_POST["accion"]) && $_POST["accion"] == "GrabarTema"){
                   
              // Actualizar la tabla de Unidades
              $q = " UPDATE ".$wbasedato."_000013 
                   SET Temres = '".$wresponsable."',
                       Temcar = '".$wcargo."',
                       Temema = '".$wemail."',
                       Temcla = '".base64_encode($wpassword1)."'
                   WHERE Temusu = '".$wusuario ."' 
                     AND Temcod = '".$wcodigo."' " ;

              $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
              
              echo $resp;
          
          return;
     }


      // Consultar los Temas Normativos
     function consultartemas($wbasedato,$conex,$wemp_pmla,$wusuario){
        
        $strtipvar = array();
        $q  = " SELECT Temcod, Temdes"
          ."   From ".$wbasedato."_000013 "
          ."   Where Temusu = '".$wusuario."' and Temest ='on'"; 

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

        $num = mysql_num_rows($res);
            
        if ($num > 0){        
            
            while($row = mysql_fetch_assoc($res))
                 {
                   $strtipvar[$row['Temcod']] = $row['Temdes'];
                 }
        }    
        
        return $strtipvar;
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

          $("#divAcceso").dialog({
              autoOpen: false,
              closeOnEscape: false,                
              top: 100,
              height: 200,
              width: 300,
              position: ['left+250', 'top+80'],
              modal: true
          });       

          $("#divAcceso").dialog("widget").find(".ui-dialog-titlebar").hide();

          //Consultar si el usuario tiene acceso y seleccionar el Tema
          $.post("maestrotemas.php",
          {
               consultaAjax:   true,
               accion      :   'ConsultarPermisos',
               wemp_pmla   :   $("#wemp_pmla").val(),
               wusuario    :   $("#wusuario").val()
          }, function(respuesta){

               if (respuesta=='N')
                  $("#divAcceso").dialog("open");
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


    function ConsultaTemaxUsuario(valortem)
    {
      var wemp_pmla  = $("#wemp_pmla").val();

      if ($("#wedicion").val()=='1'){
      
         jConfirm("Desea Cancelar La Edici\u00F3n del documento Activo?","Confirmar", function(respuesta){
                  if (respuesta == true) {
                      Cargar(valortem);
                  }
                  else{
                      return;
                  }
         });
      }       
      else{
        
        Cargar(valortem);
      }                 

    }

    function Cargar(valortem){

      // Colocar el campo en modo edición
      $("#wedicion").val('1');

      // Consultar los datos según el tema seleccionado
      $.post("maestrotemas.php",
            {
               consultaAjax :  true,
               accion       :  'ConsultarTemaxUsuario',
               wemp_pmla    :  $("#wemp_pmla").val(),
               wvalortem    :  valortem
            }, function(respuesta){
               var vconsulta = respuesta.split('|');
               $("#txtresponsable").val(vconsulta[1]);
               $("#txtcargo").val(vconsulta[2]);
               $("#txtemail").val(vconsulta[3]);
               $("#txtpassword1").val('');
               $("#txtpassword2").val('');
               $("#txtpassword1").focus();
            });
    }


    function Grabar()     
    {

      if ( $("#txtresponsable").val() =='' || $("#txtcargo").val() =='' || $("#txtemail").val() =='' 
           || $("#txtpassword1").val() =='' || $("#txtpassword2").val() =='')
      {
            jAlert('Falta diligenciar informaci\u00F3n');
            return;
      } 

      if ( $("#txtpassword1").val() !==  $("#txtpassword2").val() )
      {  
            jAlert('Debe verificar la contrase\u00f1a');
            return;
      }

      var clave_usu1 = $("#txtpassword1").val();
      var clave_usu2 = $("#txtpassword2").val();

      $.post("maestrotemas.php",
            {
               consultaAjax :  true,
               accion       :  'GrabarTema',
               wemp_pmla    :  $("#wemp_pmla").val(),
               wusuario     :  $("#wusuario").val(),
               wcodigo      :  $("#optcodigo").val(),
               wresponsable :  $("#txtresponsable").val(),
               wcargo       :  $("#txtcargo").val(),
               wemail       :  $("#txtemail").val(),
               wpassword1   :  clave_usu1,
               wpassword2   :  clave_usu2
            }, function(respuesta){

               if (respuesta == 1){
                  jAlert('El Tema ha sido grabado');
                  $("#wedicion").val('0');
               }   
            });

    }

    // Funcion para verificar que el usuario digite correctamente el Correo electrónico
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

    //  FUNCION Sacar un mensaje de alerta con formato predeterminado  
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
              box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; 
        }

        .button:active {
          background-color: #3e8e41;
          box-shadow: 0 5px #666;
          transform: translateY(4px); 
        }       

        legend { 
            background-color: white;      
            display: block;
            padding-left: 2px;
            padding-right: 2px;
            border: none;
        }  
    </style>  
  </head>
  <body>    
    <?php 
      echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
      $wtitulo  ="ADMINISTRACION TEMAS";
      encabezado($wtitulo, $wactualiz, 'clinica');
      $arr_tem  = consultarTemas    ($wbasedato,$conex,$wemp_pmla,$wusuario);
      $arr_usu  = consultarUsuarios ($wbasedato,$conex,$wemp_pmla);?>      
    </br></br>

    <CENTER>  
    <fieldset STYLE="background-color: lightgray;">  
      <table width='1000px' style='border: 1px solid black'>
      <tr >
      <td width="35%" class='fila1'><b>Seleccione el Tema</b></td>
      <td width="65%" class='fila2'><select id='optcodigo' name='optcodigo' onChange="ConsultaTemaxUsuario(this.value)">
      <option></option>
      <?php      
        foreach( $arr_tem as $key => $val){
          echo '<option value="' . $key .'">'.$val.'</option>';
        }
      ?>
      </select>
      </td>
      </tr>
      </table>
    </fieldset>

    <fieldset STYLE="background-color: lightgray;">
      <legend>DATOS RESPONSABLE DEL TEMA</legend>    
      <table width='1000px' style='border: 1px solid black'>
      <tr class=fila1>
      <td width="35%" class='fila1'><b>Persona Responsable </b></td><td width="65%" class='fila2'><input type='text' id='txtresponsable' name='txtresponsable' size=80 placeholder="Digite Nombre del Responsable"> 
      </tr>
      <tr class=fila1>   
      <td width="35%" class='fila1' ><b>Cargo del Responsable</b></td><td width="65%" class='fila2'><input type='text' id='txtcargo' name='txtcargo' size=80 placeholder="Digite Cargo del Responsable"></td>
      </tr>
      <tr class=fila1>
      <td ><b>Email </b></td><td><input type='text' id='txtemail' name="txtemail" size=80 onChange="validarEmail(this.value);" placeholder="Digite Email del Responsable"></td></tr>
      <tr class=fila1>
      <td width="35%" class='fila1'><b>Contrase&#241;a </b></td><td width="65%" class='fila2'><input type='password' id='txtpassword1' name='txtpassword1' size=80 placeholder="Digite Nueva contrase&#241;a"></td>
      </tr>
      <tr class=fila1>
      <td width="35%" class='fila1'><b>Digite nuevamente la contrase&#241;a </b></td><td width="65%" class='fila2'><input type='password' id='txtpassword2' name='txtpassword2' size=80 placeholder="Digite Nueva contrase&#241;a"></td></tr>
      </table>
    </fieldset>      
    <br></br>
    <center>
    <table>
      <tr>
      <td>&nbsp;&nbsp;<input type='submit' name='Grabar' class='button' value='Grabar' onclick='Grabar()'></td>
      <td>&nbsp;&nbsp;<input type='submit' name='Salir' class='button' value='Salir' onclick='cerrarVentana()'></td>
      </tr>
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
    <div id='divAcceso' style='display:none;'>
      <br><br>
      <table align='center' id='tblmensaje2' name='tblmensaje2' style='border: 1px solid blue;'>
      <tr><td align='center'>El usuario no tiene acceso</td></tr>    
      </table>
      <br><br>
      <center><input type='button' name='btnregresar' class='button' value='Salir' onclick='window.close();'></center>  
    </div>
    <input type="HIDDEN" name="arr_usu"      id="arr_usu"     value='<?=json_encode($arr_usu)?>'>
    <input type="HIDDEN" name="wusuario"     id="wusuario"    value='<?=$wusuario?>'>    
    <input type="HIDDEN" name="wdicion"      id="wedicion"    value='0'>    
  </body>
  </html>  
