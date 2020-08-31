<?php
include_once("conex.php");
 /**********************************************************************************************************
 *
 * Programa				: rep_horasextras.php
 * Fecha de Creación 	: 2017-10-10
 * Autor				: Arleyda Insignares Ceballos
 * Descripcion			: Reporte Horas Extras en formato .csv para exportar al programa SQL Software.
 *                                                              
 **********************************************************************************************************/
 
 $wactualiz = "2017-10-10";

 if(!isset($_SESSION['user'])){
	 echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	 return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');

  //***********************************   Inicio  ***********************************************************

  
  include_once("root/comun.php");
  
  $conex                  = obtenerConexionBD("matrix");
  $minimo_dias_vacaciones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'minimo_dias_vacaciones');
  $WMAXIMO_DIAS           = 15;
  $WMINIMO_DIAS           = $minimo_dias_vacaciones;
  $wbasedato              = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rephor');
  $wbasetalhuma           = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
  $arr_servicio           = consultarCentros ($wbasetalhuma,$conex,$wemp_pmla);
  $arr_conceptos          = consultarConceptos ($wbasedato,$conex,$wemp_pmla);
  $whora         	        = (string)date("H:i:s");
  $pos           	        = strpos($user,"-");
  $wusuario      		      = substr($user,$pos+1,strlen($user));

  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************

  if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarDetalle"){
           
        $arr_resultado  = array();

        $arr_detalle    = array();

   
	    // Hago la consulta con la tabla temporal y talhuma_000013	
	    $condicion = '';

	    if ($wservicio !='')
	    	$condicion = " And A.Cco = '".$wservicio."' ";
          
       
        $con = " SELECT   concat(B.Ideno1,' ',B.Ideno2,' ',B.Ideap1,' ',B.Ideap2) as Nomemp, B.Idecco,
                          A.Ano, A.Mes, A.Quincena, A.Cco, A.Empleado, A.Tipo_hora_dia,A.Cantidad
	             FROM     ".$wbasedato."_000003 A
	                      Inner join ".$wbasetalhuma."_000013 B on substr(B.Ideuse,1,5) = A.Empleado
	             WHERE    A.Ano = '".$wano."' And A.Mes = '".$wmes."' 
	                      And A.Quincena = '".$wquincena."' ".$condicion."
	             ORDER BY A.Cco, A.Empleado";

        $res = mysql_query($con,$conex) or die (mysql_errno()." - en el query: ".$con." - ".mysql_error());

        $num = mysql_num_rows($res);

        if  ($num > 0)
        {                  	    	    
			    while($row = mysql_fetch_assoc($res))
			    {

			    	$codigo_empleado  =  $row['Ideuse'];

			    	$nombre_empleado  =  $row['Nomemp'];

			    	if ($row['Quincena']=='1'){

			    	    $fecha_periodo   =  $row['Ano'].$row['Mes'].'16';
			    	}    
			    	else{

			    		$mes_empleado    =  intval($row['Mes'])+1;
			    		
			    		if ($row['Mes']=='12')

                            $fecha_periodo   =  $row['Ano'].'01'.'01'; 
                        else
                        	$fecha_periodo   =  $row['Ano'].$mes_empleado.'01'; 
                    }

			    	list($codigo_concepto, $horacon)  = explode('-', $row['Tipo_hora_dia']);

			        $centro_empleado = (array_key_exists($row['Idecco'],$arr_servicio)) ? $arr_servicio[$row['Idecco']]:'vacio';     

					$nombre_concepto = (array_key_exists($codigo_concepto,$arr_conceptos)) ? $arr_conceptos[$codigo_concepto]:'vacio';     


			        $arr_detalle[] = array( "codigo_empleado"   => $row['Empleado'],
                                            "nombre_empleado"   => $row['Nomemp'],
                                            "codigo_concepto"   => $codigo_concepto, 
                                            "nombre_concepto"   => $nombre_concepto,
                                            "cantidad_hora"     => $row['Cantidad'],
                                            "fecha_liquidacion" => $fecha_periodo,
                                            "codigo_centro"     => $row['Cco'],
                                            "nombre_centro"     => $centro_empleado );

			        $codigo_anterior = $codigo_empleado;

			      }         
	      }   

	      $arr_resultado['resultado'] = $arr_detalle;

	      $arr_resultado['total']     = $num;

	      echo json_encode($arr_resultado);

          return;          
      }
	

 // Consultar los usuarios para el campo autocompletar
 function consultarUsuarios($wbasetalhuma,$conex,$wemp_pmla){     
        
        $strtipvar = array();

        $q  = " SELECT Ideuse, concat(Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) as Nomemp
                From ".$wbasetalhuma."_000013 
                Where Ideest ='on'
                      And Ideap1 !=''
                Order by Nomemp";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

        while($row = mysql_fetch_assoc($res))
        {
               $strtipvar[$row['Ideuse']] = $row['Ideuse'].'-'.utf8_encode($row['Nomemp']);
        }
        return $strtipvar;

 }


 // Consultar los Conceptos para reporte de horas
 function consultarConceptos($wbasedato,$conex,$wemp_pmla){

    $strtipvar = array();

 	$q=" SELECT subcodigo, descripcion
	       FROM det_selecciones 
		  WHERE lcase(medico) = 'rephor'   
		    AND codigo = '".$wemp_pmla."'
		    AND activo = 'A' 
		  ORDER BY subcodigo ";

    $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

    while($row = mysql_fetch_assoc($res))
    {
          $strtipvar[$row['subcodigo']] = utf8_encode($row['descripcion']);
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
    <title>Exportar Horas Extras </title>
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

          $('#optservicio').multiselect({
              numberDisplayed: 1,
              selectedList:1,
              multiple:false
          }).multiselectfilter();


      }); // Finalizar Ready()

     
       /* Generar el reporte segun filtros seleccionados */
       function Consultar(opcion){

          var cont =0;

          $("input[class^=validacion]").each(function(){
              if ($(this).val() == ""){ 
                  cont++
              }
          }); 


          if (cont>0){
             jAlert('Falta ingresar Quincena');
             return;
          }
          
          /* Validar el campo multiselect para centros de costos */
          if ( $("#optservicio").val() == null || $("#optservicio").val()=='')
               var wservicio = '';
         
          else
               var wservicio = $("#optservicio").val();


          /* Consultar historico horas extras en rephor_000003 */         

		  $("#tblhorasdet > tbody:last").children().remove();

	      $.post("rep_horasextras.php",
          {
                consultaAjax : true,
                accion       : 'ConsultarDetalle',
                wemp_pmla    : $("#wemp_pmla").val(),
                wano         : $("#optano option:selected").text(),
                wmes         : $("#optmes option:selected").text(),
                wquincena    : $("#optquincena option:selected").text(),
                wservicio    : wservicio.toString()

          }, function(respuesta){

          	   document.getElementById("divcargando").style.display   = "";

               /* En caso de no encontrar registros */
              if (respuesta.total == 0){

                    $("#tblbuscar,#tblhorasdet").hide();
                    $("#tblmensaje").show();
              } 
              /* Mostrar tabla con registros consultados */
              else{

          	        var cont     = 0;
                    var fila     = "fila1";
                    var filanom  = "fila1";
                    var stringTr = ""; 
                    var strcol   = " "; 
                    var stringEx = "CodigoEmpleado;"
                                  +"CodigoConcepto;"
                                  +"Valor;"
                                  +"Unidades;"
                                  +"Fechadeliquidacion;"
                                  +"Proceso;"
                                  +"C.Costo;\n";


              	    jQuery.each(respuesta.resultado, function(){  

              	            if (this.codigo_empleado !== ''){
	                        	
	                           filanom = filanom == "fila1" ? "fila2" : "fila1";
	                        } 

	                        stringTr = stringTr + '<tr class="'+fila+'">'
	                                            + '<td class="'+filanom+'" align="center">'+pad(this.codigo_empleado,5)+'</td>'
	                                            + '<td class="'+filanom+'" >'+this.nombre_empleado+'</td>'
	                                            + '<td align="center">'+this.codigo_concepto+'</td>'
	                                            + '<td align="center">'+this.nombre_concepto+'</td>'
	                         					+ '<td align="center"></td>'
	                         					+ '<td align="center">'+this.cantidad_hora+'</td>'
	                         					+ '<td align="center">'+this.fecha_liquidacion+'</td>'
	                         					+ '<td align="center"></td>'
	                         					+ '<td align="center">'+this.codigo_centro+'</td>'
	                         					+ '<td align="center">'+this.nombre_centro+'</td></tr>';

                            if (opcion==2)	
                            {

                            	stringEx = stringEx +pad(this.codigo_empleado,5)
	                                                +';'+this.codigo_concepto
	                                                +';'+strcol
	                         					    +';'+this.cantidad_hora
	                         					    +';'+this.fecha_liquidacion
	                         					    +';'+strcol
	                         					    +';'+this.codigo_centro+'\n';
                            }                         					
	                                      
	                        fila    = fila == "fila1" ? "fila2" : "fila1";
	                        
	                        cont ++;

                    });
           
                    $("#tblmensaje").hide();

                    $('#tblhorasdet > tbody:last').append(stringTr);
                    
                    $("#tblhorasdet,#tblbuscar").show();

                    $('input#id_search_pacientes').quicksearch('table#tblhorasdet tbody tr');

                    if (opcion==2){
         
                    	var usu = document.createElement('a');
					    usu.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(stringEx));
						usu.setAttribute('download','reportehoras.csv');

						if (document.createEvent) {
							var event = document.createEvent('MouseEvents');
							event.initEvent('click', true, true);
							usu.dispatchEvent(event);
						}
						else {
							usu.click();
						}
                    }
                   
                }
                document.getElementById("divcargando").style.display   = "none";

            },"json");
   
       }

       function pad(input, length) {
            return Array(length - Math.floor(Math.log10(input))).join('0') + input;
       }
      
       function cerrarVentana()
       {
          if(confirm("Esta seguro de salir?") == true)
            window.close();

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

        .ui-multiselect { height:20px; width:350px; overflow-x:hidden; text-align:left;font-size: 10pt;} 

         BODY {
            font-family: verdana;
            font-size: 10pt;
            width: auto;
            height:auto;
         }

		#myProgress {
		  position: relative;
		  width: 30%;
		  height: 25px;
		  align: center;
		  background-color: #ddd;
		}

		#myBar {
		  position: absolute;
		  width: 1%;
		  height: 100%;
		  background-color: lightblue;
		}

		#label {
		  text-align: center;
		  line-height: 30px;
		  color: white;
		}

    </style>
    </head>
    <body >
      <?php
        echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
        $wtitulo  = "REPORTE HORAS EXTRAS Y RECARGOS";
        encabezado($wtitulo, $wactualiz, 'clinica');
        $arr_usu  = consultarUsuarios ($wbasetalhuma,$conex,$wemp_pmla);
        $wfecha_actual = date("Y-m-d");
        $wano_actual   = date("Y",strtotime($wfecha_actual)); 
        $wmes_actual   = date("m",strtotime($wfecha_actual)); 
      ?>
      <table align='center' width='550px' >
          <tr height='25px'>
            <td class='fila1'><b>Fecha</b></td>
            <td class='fila2'>&nbsp;&nbsp;
            <b>A&ntilde;o:</b>
            &nbsp;&nbsp;
                <select id='optano' name='optano' class='validacion'>";
		      	<?php 
		      	   for($f=2004;$f<2051;$f++)
			       {
			            if ($f == $wano_actual)
			                echo "<option selected>".$f."</option>";
			            else
			                echo "<option>".$f."</option>";
			       }
		         ?>
	            </select>
            
     			<b>Mes :</b><select id='optmes' name='optmes' class='validacion'>";
			     <?php
			       for ($f=1;$f<13;$f++)
			       {
				        if ($f == $wmes_actual)
				           if ($f < 10)
				              echo "<option selected>0".$f."</option>";
				           else 
				              echo "<option selected>".$f."</option>";
					    else
					       if ($f < 10)
					            echo "<option>0".$f."</option>";
					       else
					            echo "<option>".$f."</option>";
				   }
				 ?>  
				 </select>
	   
                 <b>Quincena :</b><select id='optquincena' name='optquincena' class='validacion'>";
                 <?php
			     for($f=1;$f<3;$f++)
			       {
			        echo "<option>".$f."</option>";
			       } 
				   echo "</td></select></td></tr>";
				 ?>  
            </td>
          </tr>
          <tr height='30px'>
            <td class='fila1'><b>Servicio</b></td>
            <td class='fila2'>&nbsp;&nbsp;
            <select id='optservicio' name='optservicio' multiple='multiple'>
            <?php
              echo '<option ></option>';
              foreach( $arr_servicio as $key => $val){
                  echo '<option value="' . $key .'">'.$val.'</option>';
              }
            ?>
            </select>
            </td>
          </tr>
      </table>
      </br>
      <center>
      <table>
        <tr>
	        <td>&nbsp;&nbsp;<input type='button' id='btnConsultar' name='btnConsultar' class='button' value='Consultar' onclick='Consultar(1)'></td>
	        <td>&nbsp;&nbsp;<input type='button' id='btnExportar'  name='btnExportar'  class='button' value='Exportar'  onclick='Consultar(2)'></td>
	        <td>&nbsp;&nbsp;<input type='button' id='btnSalir'     name='btnSalir'     class='button' value='Salir'     onclick='cerrarVentana()'></td>
        </tr>
      </table>
      <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center>
      </div>
      <br>
      <br>
      <center>
      <div align=center id="divhorasdet">
      	    <table align='left' width='300px' style='display:none;' id='tblbuscar' name='tblbuscar'>
	        <tr><td></td><td class='fila1'>Filtrar listado:&nbsp;&nbsp;<input id="id_search_pacientes" type="text" value="" size="20" name="id_search_pacientes" placeholder="Buscar en listado">&nbsp;&nbsp;</td></tr>
	        </table>
            <table id="tblhorasdet" width='100%' style='display:none;border:0px;'>
	            <thead>
	            <tr align="center" class="encabezadoTabla">
	                <td align="center">C&oacute;digo empleado</td>
	                <td align="center">Nombre empleado</td>
	                <td align="center">C&oacute;digo concepto</td>
	                <td align="center">Nombre concepto</td>
	                <td align="center">Valor</td>
	                <td align="center">Cantidad</td>
	                <td align="center">Fecha liquidaci&oacute;n</td>
	                <td align="center">Proceso</td>
	                <td align="center">Servicio</td>
	                <td align="center">Nombre Servicio</td>
	            </tr>
	            </thead>
	            <tbody>
	            </tbody>
            </table>
      </div>
      <div align=center id="divhorasexp">
            <table id="tblhorasexp" width='100%' style='display:none;'>
	            <thead>
	            <tr align="center" class="encabezadoTabla">
	                <td align="center">C&oacute;digo empleado</td>
	                <td align="center">C&oacute;digo concepto</td>
	                <td align="center">Valor</td>
	                <td align="center">Cantidad</td>
	                <td align="center">Fecha liquidaci&oacute;n</td>
	                <td align="center">Proceso</td>
	                <td align="center">Servicio</td>
	            </tr>
	            </thead>
	            <tbody>
	            </tbody>
            </table>
      </div>
      </center>
      <table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
        <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
      </table>
    </body>
    </html>