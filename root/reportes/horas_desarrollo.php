<?php
include_once("conex.php");
session_start();
?>
<head>
  <title>REPORTE HORAS DE DESARROLLO</title>
<style type="text/css">
  .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
  .tipo3V:hover {color: #000066; background: #999999;}
</style>
<style type="text/css">
    /* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
    .ui-datepicker {font-size:12px;}

    /* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
    .ui-datepicker-cover {
        display: none; /*sorry for IE5*/
        display/**/: block; /*sorry for IE5*/
        position: absolute; /*must have*/
        z-index: -1; /*must have*/
        filter: mask(); /*must have*/
        top: -4px; /*must have*/
        left: -4px; /*must have*/
        width: 100px; /*must have*/
        height: 100px; /*must have*/
    }
</style>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script>
    $.datepicker.regional['esp'] = {
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
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['esp']);
</script>
<script type="text/javascript">
    $( document ).ready( function(){

        $(".inputFechas").datepicker({
             showOn: "button",
             buttonImage: "../../images/medical/root/calendar.gif",
             buttonImageOnly: true,
             changeYear:true,
             reverseYearRange: true,
             changeMonth: true,
             maxDate: $("#wfechahoy").val()
        });
    } );

    function fnMostrar( celda ){

    	if( $("div", celda ) ){

    		$.blockUI({ message: $("div", celda ),
    						css: { left: ( $(window).width() - 600 )/2 +'px',
    							    top: ( $(window).height() - $("div", celda ).height() )/2 +'px',
    							  width: '600px'
    							 }
    				  });

    	}
    }

    function cerrarVentana(){
      window.close()
    }


   function enter()
   {
    document.forms.hoja.submit();
   }

   function intercalar(idElemento){
		 var $mostrar

		 if(document.getElementById(idElemento).style.display=='')
			{
				$mostrar='no';
			}
		 else
			{
				$mostrar='si';
			}

		 //ocultar todos los detalles que esten pintados
		 var todos_Tr = document.getElementById("matriz").getElementsByTagName("tr");
		 var num = todos_Tr.length;
		 for (y=0; y<num; y++)
		 {
			if ( todos_Tr[y].id != '' )
			{
				todos_Tr[y].style.display='none';
			}
		 }
		 //fin ocultar
		 if ($mostrar=='si'){
			  document.getElementById(idElemento).style.display='';
			}
   }

   function cambiarTipoReporte( obj ){
      var valor = $(obj).attr("valor");
      $("#wtipoReporte").val( valor );
   }

   function cambiarFormato( obj ){
      var valor = $(obj).attr("valor");
      $("#wformatoTiempo").val( valor );
   }
</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
/*
// ==========================================================================================================================================
// D E S C R I P C I O N
// ==========================================================================================================================================

    NOMBRE =   REPORTE HORAS DE DESARROLLO
    AUTOR=     JERSON ANDRES TRUJILLO
    FECHA=     29-MAYO-2012
    OBJETIVO = Conocer la horas de trabajo utilizadas por cada desarrollador, en cada uno de los requerimientos asignados.
               Y segun un determinado periodo seleccionado. Agrupandolas por centro de costos



// ==========================================================================================================================================
// M O D I F I C A C I O N E S
// ==========================================================================================================================================
// 06-MAYO-2014 camilo zz: se modifica el query que construye el detalle de los centros de costos para que muestre al usuario responsable y no al usuario generador
//                        del requerimiento, adicionalmente se corrigió el programa para que no muestre todos los desarrolladores en el resumen del centro de costos, sino
//                         solo aquellos que cumplan con la característica del requerimiento
// 15-ABR-2014 camilo zz:Se modificó para que busque el usuario receptor en la tabla de perfiles de usuario, adicionalmente se empieza a calcular las horas de
// este usuario sumando los requerimientos activos y la participación en los proyectos de los demas desarrolladores.
// Finalmente se modifica el script para que realice los cálculos de hora teniendo en cuenta la concurrencia de proyectos de distinto tipo.
// ==========================================================================================================================================
  10-ABR-2014 camilo zz: Se modifica para que se seleccione el formato de horas:minutos y/o horas.fraccion, s
  e modificó para que encuentre los centros de costos en las tablas correspondientes y no salga("dato no encontrado") durante la generación del reporte, Finalmente se agregaron
  las opciones que permiten elegir los tipos de requerimientos que se van a reportar(Aplicacion, Otros o todos )
  ======================================================================================================================================
// ==========================================================================================================================================
	08-NOV-2013=	Se modifica para que tambien traiga los requerimientos colocados por usuarios que sean receptores
					Juan C. Hernández.
// ==========================================================================================================================================
// ==========================================================================================================================================
	22-JUNIO-2012=	Al visualizar el detalle, se agrego una columna para conocer el estado actual del requerieminto. Se cambio la
					visualizacion de los detalles, para mostrar todo el detalle del Centro de Costos o solo el detalle del desarrollador.
					JERSON ANDRES TRUJILLO.
//==========================================================================================================================================
*/

if(!isset($_SESSION['user']))
	echo "error";
else
{
	

 	

 	include_once("root/comun.php");
    $key = substr($user,2,strlen($user));

	if (strpos($user,"-") > 0)
          $wuser = substr($user,(strpos($user,"-")+1),strlen($user));

    $wusuario = $wuser;
    $wactualiz = "2014-05-06";

    $wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");
    $tiposRequ = array();

    echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' name='wfechahoy' id='wfechahoy' value='".date('Y-m-d')."'>";

    //=======================================================================================================
    //=======================================================================================================
    //CON ESTO TRAIGO LA EMPRESA Y TODOS LOS CAMPOS NECESARIOS DE LA EMPRESA
    $q = " SELECT empdes "
        ."   FROM root_000050 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' ";
    $res = mysql_query($q,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row = mysql_fetch_array($res);
    $wnominst=$row[0];

    //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
    $q = " SELECT detapl, detval "
        ."   FROM root_000050, root_000051 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' "
        ."    AND empcod = detemp ";
    $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);
    if ($num > 0 )
       {
	    for ($i=1;$i<=$num;$i++)
	       {
	        $row = mysql_fetch_array($res);

	        if ($row[0] == "cenmez")
	           $wcenmez=$row[1];

	        if ($row[0] == "afinidad")
	           $wafinidad=$row[1];

	        if ($row[0] == "movhos")
	           $wbasedato=$row[1];

	        if ($row[0] == "tabcco")
	           $wtabcco=$row[1];

			if (strtoupper($row[0]) == "HCE")
	           $whce=$row[1];
           }
      }
     else
        echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
    //=======================================================================================================
    //=======================================================================================================
  function festivos($conex, $dia, $dias_festivo){ //funcion que me dice si un dia es festivo o no
      $festivo='no';
      if(array_key_exists($dia,$dias_festivo))
          $festivo='si';

      return $festivo;
  }

  function formato_hora($valor1){ //funcion que me formatea un decimal en formato HH:mm
     global $wformatoTiempo;
      $valor= number_format($valor1, 4); //manejar 2 decimales
      $valor_formato_hora= explode('.',$valor);
      $valor_formato_hora[1]='0.'.$valor_formato_hora[1];
      $valor_formato_hora[1]=number_format($valor_formato_hora[1]*60, 0);
      if( $wformatoTiempo == "HH.ff"){
        $valor_formato_hora[1] = $valor_formato_hora[1] / 60 * 100;
        $valor_formato_hora[1]=number_format($valor_formato_hora[1], 0);
      }
      return $valor_formato_hora;
  }

	//funcion que muestra el detalle de al dar click en los <td>
	function pintar_detalle($centro_cost, $colspan, $req_cco, $wclass2, $cod_desar=''){
			global $conex;
      global $gruposRequerimientos;
			echo "<td colspan='".$colspan."' align=center>";
  				  echo "<table align=center>";
  				  echo "<tr class='fondoAmarillo'><td align='middle' colspan='6'><b>Requerimientos Origen</b></td</tr>";
  				  echo "<tr class=encabezadoTabla>";
  				  echo "<td align='middle'>Tipo</td>";
            echo "<td align='middle'>N&deg; Requerimiento</td>";
  				  echo "<td align='middle'>Grupo</td>";
  				  echo "<td align='middle'>Receptor</td>";
  				  echo "<td align='middle'>Estado</td>";
  				  echo "<td  align='middle'>Descripción</td>";
  				  echo "</tr>";
  				  foreach($req_cco as $key_iden_req => $cco3 )//$req_cco[identificador del requerimiento]=centro costo asociado
  				  {
  					if($cco3==$centro_cost)
  					{
  					  $query= " Select A.Segtip, A.Segreq, Reqdes, Reqest, Descripcion, Codigo"
  							." from root_000045 as A, root_000040, usuarios "
  							." where A.id ='".$key_iden_req."' "
  							." AND Reqnum = A.Segreq"
  							." AND Reqtip = A.Segtip"
  							." AND Codigo = Reqpurs "
  							."";
  					  $res = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
  					  $row=mysql_fetch_array($res);
  					  if ($wclass2=="fila1")
  					   $wclass2="fila2";
  					  else
  						 $wclass2="fila1";

  					  if($cod_desar!='') //si la variable esta setiada con algun codigo, es porque eligieron ver el detalle solo del desarrollador
  					  {
  							if($cod_desar!=$row['Codigo']) //solo se mostraran el detalle del desarrollador seleccionado
  								continue;
  					  }

  					  echo "<tr class=".$wclass2.">";
  					  echo "<td align='middle'>".$row['Segtip']."</td>";
              echo "<td align='middle'>".$row['Segreq']."</td>";
  					  echo "<td align='middle'>".$gruposRequerimientos[$row['Codigo']][$row['Segreq']]."</td>";
  					  echo "<td align='middle'>".$row['Descripcion']." </td>";

  						//ESTADO ACTUAL
  							$query_nom_esta="Select Estnom
  											From root_000049
  											Where Estcod='".$row['Reqest']."'
  											And Estest='on'
  											";
  							$res_nom_est = mysql_query($query_nom_esta,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_nom_esta." - ".mysql_error());
  							$row_nom_est=mysql_fetch_array($res_nom_est);

  					  echo "<td align='middle'>".$row_nom_est['Estnom']."</td>";
  						//FIN ESATDO ACTUAL

  					  //pintar la descripcion del requerimiento con jquery
  						  echo "<td style='cursor:pointer;' align='middle' onClick='fnMostrar( this )'>";
  						  echo"<b>Ver</b>";
  						  echo "<center><div class='fondoAmarillo' align='middle' style='display:none;width:100%;cursor:default;'>";
  						  echo '<br><b>Descripción:</b>';
  						  echo '<br>'.$row['Reqdes'];
  						  echo "<br><br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'><br><br>";
  						  echo "</div></center>";
  						  echo "</td>";
  					  //fin pintar descripcion
  					  echo "</tr>";
  					}

  				  }

  				  echo "</table><br><br>";
  			echo "</td>";
  }

    echo "<form name='hoja' action='horas_desarrollo.php' method=post>";
    echo "<center>";
    encabezado("REPORTE HORAS DE DESARROLLO", $wactualiz, 'clinica');
    //===============================================================================================================================================
    // MAIN DEL PROGRAMA
    //===============================================================================================================================================
          $query = "SELECT detval
                      FROM root_000051
                     WHERE detemp = '{$wemp_pmla}'
                       AND detapl = 'tipoRequerimientosReporte'";
          $rs     = mysql_query( $query, $conex );
          while( $row    = mysql_fetch_array( $rs ) ){
            $aux = explode( ",", $row['detval'] );
            foreach( $aux as $i=> $datos ){
              $aux2 = explode( "-", $aux[$i] );
              //el machete tiene el propósito de que aparezca en pantalla los nombres deseados.
              switch ($aux2[0]) {
                case '%':
                  $aux2[1]  = "TODOS";
                  break;

                 case 'otr':
                  $aux2[1]  = "Costos";
                  break;

                default:
                  $aux2[1]  = "Capitalizaci&oacute;n";
                  break;
              }
              $tiposRequ[$aux2[0]] = $aux2[1];
            }
          }
          echo "<center><br><br><table cellspacing=1>";
          echo "<tr class='encabezadoTabla'>";
          echo "<td align='middle' colspan='4'><font size=3>SELECCIONE EL PERIODO</font></td><tr>";
          echo "<tr class='seccion1'>";
          echo "<td align='left'> Fecha inicial: </td><td> <input class='inputFechas' type='text' size='10' id='wfec_i' name='wfec_i' value='{$wfec_i}'> </td>";
          echo "<td align='left'> Fecha final:   </td><td> <input class='inputFechas' type='text' size='10'id='wfec_f' name='wfec_f' value='{$wfec_f}'> </td>";
          echo "</tr>";
          echo "<tr><td align='center' class='seccion1'>Tipo de Reporte:</td><td class='fila2' colspan='3'></b>";

          ( !isset($wtipoReporte ) ) ? $wtipoReporte = "%" : $wtipoReporte = $wtipoReporte;
          echo "<input type='hidden' id='wtipoReporte' name='wtipoReporte' value='{$wtipoReporte}'>";

          foreach( $tiposRequ as $val=>$nombre){
            if( isset( $wtipoReporte) ){
              ( $val == $wtipoReporte ) ? $checked = "checked" : $checked = "";
            }else
              ( $val == "%" ) ? $checked = "checked" : $checked = "";
            echo "<input type='radio' name='chk_tipo_reporte' valor='{$val}' {$checked} onclick='cambiarTipoReporte(this)'>{$nombre} &nbsp;";
          }
          echo "</td></tr>";
          echo "<tr><td class='seccion1' align='left'>Formato:</td><td class='fila2' colspan='3'></b>";
          ( !isset($wformatoTiempo ) ) ? $wformatoTiempo = "HH:MM" : $wformatoTiempo = $wformatoTiempo;
          echo "<input type='hidden' id='wformatoTiempo' name='wformatoTiempo' value='{$wformatoTiempo}'>";

          ( $wformatoTiempo == "HH:MM"  or !isset($wformatoTiempo) ) ? $checked1 = "checked" : $checked1 = "";
          ( $wformatoTiempo == "HH.ff" ) ? $checked2 = "checked" : $checked2 = "";
          echo "<input type='radio' name='chk_formato_reporte' valor='HH:MM' {$checked1} onclick='cambiarFormato(this)'>hh:mm &nbsp;";
          echo "<input type='radio' name='chk_formato_reporte' valor='HH.ff' {$checked2} onclick='cambiarFormato(this)'>H.fracci&oacute;n &nbsp;";
          echo "</td></tr>";
          echo "<tr><td align='center' bgcolor='cccccc' colspan='4'></b><input type='submit' value='CONSULTAR'></b></td></tr></center>";
          echo "</table></center><br><br>";


    /// RESPUESTA DEL REPORTE
    if (isset($wfec_i) or isset($wfec_f)){


        //recordatorio para actualizar la tabla root_000063: dias festivos
        $query_rec=" select max(Fecha) as Fecha from root_000063 ";
        $res_rec = mysql_query($query_rec,$conex) or die ("Error : ".mysql_errno()." - en el query: ".$query_rec." - ".mysql_error());
        $row_rec = mysql_fetch_array($res_rec);
        if($wfec_i >= $row_rec['Fecha'])
        {
            echo "<blink><b class='articuloControl' align=center > ¡¡¡ ALERTA !!! <br></b></blink>";
            echo "<center><table class='articuloControl' WIDTH=30% HEIGHT=10% ><tr><td align='center'>";
            echo "La tabla de los días Festivos(root_000063), no esta lo suficientemente actualizada para el periodo seleccionado, "
                ."por lo que la información presentada a continuación puede no ser totalmente confiable, se recomienda que contacte con el"
                ." área de sistemas para notificar esta alerta</b></blink>";
            echo "</td></tr></table></center><br><br>";
        }
        //fin recordatorio
        if ($wfec_f > date('Y-m-d'))
                $wfec_f=date('Y-m-d');
        $cco_no_existe= date('Y-m-d-s');

        $condicionTipoRequerimiento = "";
        $condicionTipoRequerimiento1 = " LEFT JOIN root_000047 on ( Espreq = Segreq )";
        $arregloAuxiliar = array();
        $arregloAuxiliar2 = array();
        $gruposRequerimientos = array();
        if( $wtipoReporte != "%" ){
          $queryreq = "SELECT detval
                      FROM root_000051
                     WHERE detemp = '{$wemp_pmla}'
                       AND detapl = 'tipoRequerimientosReporte'";
          $rsreq     = mysql_query( $queryreq, $conex );
          while( $rowreq    = mysql_fetch_array( $rsreq ) ){
            $aux = explode( ",", $rowreq['detval'] );
            foreach( $aux as $i=> $datos ){
              $aux2 = explode( "-", $aux[$i] );
              if( $aux2[0] != "%" and $aux2[0] != "otr" )
                array_push($arregloAuxiliar2, "'".$aux2[0]."-".$aux2[1]."'" );
                array_push($arregloAuxiliar, "".$aux2[0]."-".$aux2[1]."" );
            }
          }
          $arregloAuxiliar2 = implode( ",", $arregloAuxiliar2 );
          if( $wtipoReporte != "otr" ){
            $condicionTipoRequerimiento2 = " AND Esptip IN (".$arregloAuxiliar2.") AND Esptip is not null";
            $condicionTipoRequerimiento2 = "";
          }else{
            $condicionTipoRequerimiento2 = " AND ( Esptip NOT IN (".$arregloAuxiliar.") or Esptip is null )";
            $condicionTipoRequerimiento2 = "";
          }
        }

        //consultar el centro de costos y tipo de perfil del usuario coordinador del area de desarrollo >Juan carlos<
        $query  = "select Percco, pertip, Perusu
                     from root_000042
                    where Percco = '(01)1710'
                      and perrec = 'on'
                      and pertip = '03'";
        $rs     = mysql_query( $query, $conex );
        $row    = mysql_fetch_array( $rs );
        $codigoUsuarioReceptor = $row[2];
        $cco=$row[0];
        $tip=$row[1];
        // fin consultar cco

        //Consultar el estado bandera (estado apartir del cual se empezara a calcular las horas de inicio trabajo)
        $query=" select Estcod, Estnom from root_000049 where Estcht = 'on' "; //root_000049:Maestro de los estados de los requerimientos
        $res = mysql_query($query,$conex) or die ("Error : ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
        $row = mysql_fetch_array($res);
		    $cod_esta_band= $row[0];
        $esta_band=$row[0]."-".$row[1];
        //fin

        //consultar dias festivos
        $q ="SELECT Fecha FROM root_000063";//root_000063:dias festivos
            $qres = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

         while($row_festivos=mysql_fetch_array($qres))
         {
            $dias_festivo[$row_festivos['Fecha']]=$row_festivos['Fecha'];
         }
        //fin consultar dias festivos

        //consultar los usuarios (desarrolladores) que apareceran en el reporte
        $query=" select Perusu, Descripcion "
               ." from root_000042, usuarios  "	//root_000042:Maestro de perfiles de usuarios
               ." where perest='on' "           //que esten activos
               //." AND Perrec='off' "            //que no sean receptores
               ." AND Percco= '".$cco."'"       //que pertenezcan al mismo CC del usuario
               ." AND Pertip= '".$tip."'"       //que tengan el mismo tipo de perfil
               ." AND Perusu!='05000' "         //codigo quemado para no mostrar los requerimientos de pedro ortiz
               ." AND Codigo = Perusu";         // hacer el join con usuarios, para conocer el nombre
        $res = mysql_query($query,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());

        while($row = mysql_fetch_array($res))
        {
            $codigo=$row[0];
            $nombre=$row[1];

            //consultar requerimientos por rango de fechas y que esten en el estado 'en proceso' y asociados al desarrollador

            //query anterior a 20-06-2012
            /*$query2=" select max(id) as id, A.Fecha_data, A.Hora_data, Segtip, Segreq  "
                       ." from root_000045 as A "       //root_000045:Seguimiento de los requerimientos
                       ." where Segusu='".$codigo."' "
                       ." AND Segest='".$esta_band."' "
                       ." AND A.Fecha_data <='".$wfec_f."' "
                       ." group by Segtip, Segreq ";*/

            //NUEVO QUERY: 	20-06-2012,	se hace un join con la 40 para filtrar por el estado actual, que sea en proceso(04) o entregado(05)
            $query2=" SELECT max(A.id) as id, A.Fecha_data, A.Hora_data, A.Segtip, A.Segreq, Requso codigoUsuario, Esptip tipo, Espgru grupo "
                   ."   FROM root_000045 as A
                             INNER JOIN
                             root_000040 as B on ( B.Reqnum = A.segreq AND B.Reqtip = A.Segtip ) "       //root_000045:Seguimiento de los requerimientos
                   ."       {$condicionTipoRequerimiento1}"
                   ."  WHERE A.Segusu = '".$codigo."' "
                   ."    AND A.Segest = '".$esta_band."' "
                   ."    AND A.Fecha_data <= '".$wfec_f."' "
                   ."    AND B.Reqcco = A.Segcco "
                   ."    ".$condicionTipoRequerimiento2.""
                   ."    AND (B.Reqest = '".$cod_esta_band."' or B.Reqest = '05') "	//estado = 05 va quemado porque es el estado final hasta donde se calcularan las horas y no existe hasta el momento ningun parametro que me indique esto
                   ."  GROUP BY A.Segtip, A.Segreq ";
            /*if( $codigo == "03636" )
              echo $query2."<br>";*/

            $res2 = mysql_query($query2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query2." - ".mysql_error());
            while($row2 = mysql_fetch_array($res2)) //MIENTRAS HAYA REQUERIMIENTOS
            {
                $id=$row2[0];
                $fech_ini_req=$row2[1];                     //fecha en la que empezo a estar en estado en proceso
                if($fech_ini_req<$wfec_i)                   //si la fecha en la que inicio el requerimiento es menor a la fecha de consulta
                {
                    $fech_ini_req=$wfec_i;
                }
                $hora_ini_req   = $row2[2];
                $tipo_req       = $row2[3];
                $num_req        = $row2[4];
                $codigo_usuario = $row2['codigoUsuario'];
                $id_requer      = $id;                             //Identificador unico de un requerimiento
                $grupo          = $row2['grupo'];

                $query3= " Select Fecha_data, Hora_data "
                        ." from root_000045 "                     //root_000045:seguimiento de los requerimientos
                        ." where Segreq='".$num_req."' "
                        ." AND Segtip='".$tipo_req."' "
                        ." AND Segest='05-Entregado' "
                        ."";
                $res3 = mysql_query($query3,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query3." - ".mysql_error());
                if(mysql_num_rows($res3)>0)
                {
                    $row3 = mysql_fetch_array($res3);

                    if($row3[0]<$wfec_i)    //si el requerimiento se entrego antes de la fecha de inicio de consulta
                        $calcular_horas=0;
                    else
                    {
                        $hacer_foreach[$codigo]=1;
                        $calcular_horas=1;
                        $fech_fin_req=$row3[0];
                        $hora_fin_req=$row3[1];
                    }
                }
                else
                {
                    $hacer_foreach[$codigo]=1;
                    $calcular_horas=1;
                    $fech_fin_req=$wfec_f;
                    $hora_fin_req='23-59-59';
                }

                //SI EL REQUERIMIENTO ESTA O ESTUVO EN ESTADO 'EN PROCESO' DENTRO DEL PERIODO DE CONSULTA
                if($calcular_horas==1 )
                {
                    // BUSCAR EN ESTA CONSULTA EL LA EMPRESA ASOCIADA AL USUARIO Y EL CENTRO DE COSTOS, DESPUES BUSCAR EN LA TABLA CORRESPONDIENTE A LA EMPRESA
                    // EL NOMBRE
                    /*$query4="select Cconom, Ccocod "
                        ." from root_000040, usuarios, costosyp_000005 "    //root_000040:requerimientos, Usuarios:usuarios matrix, costosyp_000005:maestro de centros de costos
                        ." where Reqtip='".$tipo_req."' "
                        ." AND Reqnum='".$num_req."' "
                        ." AND Codigo=Requso"
                        ." AND Ccocod=Ccostos"
                        ." ";*/
                    $query4="SELECT Codigo, Ccostos, Empresa, Emptcc Tabla "
                            ." FROM usuarios, root_000050 "    //root_000050: empresas de matrix, Usuarios:usuarios matrix
                            ."WHERE Codigo='".$codigo_usuario."'
                                AND Empcod= Empresa
                                AND Empest= 'on'";

                    $res4 = mysql_query($query4,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query4." - ".mysql_error());
                    $row4 = mysql_fetch_array($res4);

                    switch ($row4['Tabla'])
                    {
                      case "clisur_000003":
                        $query4 = "select Ccodes, Ccocod from clisur_000003   WHERE Ccocod = '".$row4['Ccostos']."'";
                      break;
                      case "farstore_000003":
                        $query4 = "select Ccodes, Ccocod from farstore_000003 WHERE Ccocod = '".$row4['Ccostos']."'";
                      break;
                      case "costosyp_000005":
                        $query4 = "select Cconom, Ccocod from costosyp_000005 WHERE Ccocod = '".$row4['Ccostos']."'";
                      break;
                      case "uvglobal_000003":
                        $query4 = "select Ccodes, Ccocod from uvglobal_000003 WHERE Ccocod = '".$row4['Ccostos']."'";
                      break;
                      default:
                        $query4 = "select Cconom, Ccocod from costosyp_000005 WHERE Ccocod = '".$row4['Ccostos']."' ";
                    }

                    $res4 = mysql_query($query4,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query4." - ".mysql_error());
                    $row4 = mysql_fetch_array($res4);

                    if(mysql_num_rows($res4)>0)
                    {
                        $cco_nombr=$row4[0];
                        $cco=$row4[1];
                    }
                    else
                    {
                        $cco_nombr='Dato No Encontrado';
                        $cco_no_existe++;
                        $cco=$cco_no_existe;
                    }
                    $req_cco[$id_requer]=$cco;                  //Identificador del requerimiento con su respectivo centro de costos que genero el requerimeinto
                    $cent_costos_arr[$cco]=$cco_nombr;          //Codgo del centro de costos con su respectivo nombre
                    $nom_desar[$codigo]=$nombre;                //Codigo del desarrollador con su respectivo nombre

                    $wfec_in=strtotime($wfec_i);
                   //recorre todo el rango de fechas que selecciono el usuario para consultar
                   for($y=strtotime($wfec_i); $y<=strtotime($wfec_f); $y+=60*60*24)
                    {
                        $y_formato_normal=date( "Y-m-d", $y );
                        $dia=date("l",strtotime($y_formato_normal));
                        $festivo= festivos($conex, $y_formato_normal, $dias_festivo); //Obtengo los dias festivos

                        if($dia!='Saturday' && $dia!='Sunday' && $festivo=='no' )
                        {
                            for($x=strtotime($fech_ini_req); $x<=strtotime($fech_fin_req); $x+=60*60*24)// recorre el rango de fechas en las que estubo en proceso el requerimiento
                                {
                                    if(date( "Y-m-d ", $y )==date( "Y-m-d ", $x )) //si el dia 'y' se trabajo en el requerimiento
                                    {
                                        ${'des'.$codigo}[$y_formato_normal][$id_requer]=8;
                                        $x=strtotime($fech_fin_req);
                                        $x+=60*60*24;
                                        $tiposRequerimientos[$codigo][$id_requer] =  $row2['tipo'];
                                        $gruposRequerimientos[$codigo][$num_req] =  $grupo;
                                    }
                                    else
                                    {
                                        ${'des'.$codigo}[$y_formato_normal][$id_requer]=0;
                                    }
                                }
                        }
                    }
                     $arr = ${'des'.$codigo};
                   //FIN recorre todo el rango de fechas
                }
            }
            //FIN REQUERIMIENTOS

            if (mysql_num_rows($res2)>0 && isset($hacer_foreach[$codigo]) && $hacer_foreach[$codigo]==1)
            {
                //ACTUALIZAR LAS HORAS PORCENTUALMENTE
                $arr = ${'des'.$codigo};
                foreach($arr as  $clav => $fechas) // se recorre por fechas
                {
                    $total_hora_trab=0;
                    foreach($fechas as $id_req => $horas_tra) //recorre por requeriemintos para sumar todas las horas trabajadas
                    {
                        $total_hora_trab+=$horas_tra;
                    }
                    unset($id_req);
                    unset($horas_tra);
                    foreach ($fechas as $id_req => $horas_tra)// recorre nuevamente por requerimientos para asignar el porcentaje de horas que les correspondan
                    {
                        $porcenta=($total_hora_trab==0)? 0 : $horas_tra/$total_hora_trab;
                        $fechas[$id_req]=$horas_tra*$porcenta; //calculo porcentual
                        //** AHORA, LOS REQUERMIENTOS QUE NO CUMPLAN CON LA CONDICIÓN SERAN DESETEADOS**//
                        if( $wtipoReporte != "%" ){
                          if( $wtipoReporte != "otr"){
                            if( !in_array( $tiposRequerimientos[$codigo][$id_req], $arregloAuxiliar ) ){
                              unset( $fechas[$id_req] );
                              unset( $req_cco[$id_req] );
                            }
                          }else{
                            if( in_array( $tiposRequerimientos[$codigo][$id_req], $arregloAuxiliar ) ){
                              unset( $fechas[$id_req] );
                              unset( $req_cco[$id_req] );
                            }
                          }
                        }
                    }
                    $arr[$clav] = $fechas;
                    unset($id_req);
                    unset($horas_tra);
                }
                //FIN ACTUALIZAR

                //CREAR ARRAY CON LA SIGUIENTE ESTRUCURA:  $MAIN[codigo del centro de costos][codigo del desarrollador]= total horas de trabajo
                    foreach($arr as  $clav2 => $fechas2) // se recorre por fechas
                    {
                        foreach($fechas2 as $id_req2 => $horas_tra2) //recorre por requeriemintos
                        {
                            if(array_key_exists($id_req2, $req_cco))
                                {
                                    $arr_cco=$req_cco[$id_req2];
                                    if(isset($main[$arr_cco][$codigo]))
                                    {
                                        $main[$arr_cco][$codigo]=$main[$arr_cco][$codigo]+$horas_tra2;
                                    }
                                    else
                                    {
                                        $main[$arr_cco][$codigo]=$horas_tra2;
                                    }
                                }
                        }
                        unset($id_req2);
                        unset($horas_tra2);
                    }
                //FIN CREAR ARRAY
            }
        }

		//===========================================================================================
		//		PINTAR MATRIZ PRINCIPAL
		//===========================================================================================
        if( !isset($nom_desar) ){
            $nom_desar = array();
        }
        echo "<center><table id='matriz'>";
		echo "<tr class=encabezadoTabla>";
        echo "<td rowspan='2' width='7' align='middle' ><b>CENTRO DE COSTOS</b></td>";
       foreach($nom_desar as $keyCodigo => $nombre_desar) //Pintar nombre de los desarrolladores
        {
              if( $keyCodigo != $codigoUsuarioReceptor){
                 echo "<td width='6' align='middle' >".$nombre_desar."</td>";
              }
        }
        //consultar el nombre del receptor general de los requeriemientos
         $q = " SELECT Descripcion "
            ."   FROM usuarios "
            ."  WHERE Codigo = $codigoUsuarioReceptor";
        $res = mysql_query($q,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row = mysql_fetch_array($res);
        $nombre_receptor=$row[0];
        $n=count($nom_desar);
        echo "<td width='6' align='middle' >".$nombre_receptor."</td>";
        echo "<td width='8' align='middle' ><b>TOTAL CENTRO DE COSTOS<b></td>";
        echo "</tr>";
        echo"<tr class=encabezadoTabla>";
            for($td=1;$td<=$n+1;$td++)
            {
                    ( $wformatoTiempo == "HH:MM" ) ? $formato = "HH:mm" : $formato = "HH.frc";
                    ( $wformatoTiempo == "HH:MM" ) ? $title   = "Hora:mins" : $formato = "Hora.fraccion";
                    echo "<td width='6' align='middle' title='{$title}' >{$formato}</td>";
            }
        echo "</tr>";
        //fin consultar
        $wclass="fila1";
        $wclass2="fila1";
        $total_general=0;
        $total_todos_cc=0;
        //Pintar el reporte de horas
        foreach($main as $centro_cost => $codigo_desarro  ) //recorre por centro de costos
        {
            if ($wclass=="fila1")
			   $wclass="fila2";
	          else
	             $wclass="fila1";
            echo "<tr class=".$wclass.">";
            echo "<td style='cursor:pointer;' onclick=javascript:intercalar('".$centro_cost."')>".$cent_costos_arr[ $centro_cost]."</td>";
            $total_cc=0;
            foreach($nom_desar as $key_desarr => $val_desarr) //recorre por desarrollador
            {
                if( $key_desarr != $codigoUsuarioReceptor){
                  if(array_key_exists($key_desarr, $codigo_desarro) )
                  {
                      $valor_formato_hora= formato_hora($codigo_desarro[$key_desarr]);
                      ($wformatoTiempo == "HH:MM") ? $valorAmostrar = $valor_formato_hora[0].":".$valor_formato_hora[1] : $valorAmostrar = $valor_formato_hora[0].".".$valor_formato_hora[1] ;
                      echo "<td style='cursor:pointer;' onclick=javascript:intercalar('".$centro_cost.$key_desarr."') align='middle'>".$valorAmostrar."</td>";
                      $total_cc=$total_cc+number_format($codigo_desarro[$key_desarr], 4); //totalizar las horas por centro de costos

                      //totalizar las horas por desarrollador
                      if (isset($total_hor_desa[$key_desarr]))
                      $total_hor_desa[$key_desarr]=$total_hor_desa[$key_desarr]+number_format($codigo_desarro[$key_desarr], 4);
                      else
                      $total_hor_desa[$key_desarr]=number_format($codigo_desarro[$key_desarr], 4);
                      //fin totalizar
                  }
                  else
                  {
                      echo "<td  align='middle' > 0 </td>";
                  }
                }
            }

            //pintar el valor de horas del receptor: que se calcula tomando por cada centro de costo, todas la horas de los desarolladores
            // y ese total se divide por el numero de desarrolladores que aparecen en el reporte
            $total_receptor=$total_cc/$n + number_format($codigo_desarro[$codigoUsuarioReceptor], 4);
            //totalizar las horas por desarrollador receptor
            if (isset($total_hor_desa[$codigoUsuarioReceptor]))
            $total_hor_desa[$codigoUsuarioReceptor]=$total_hor_desa[$codigoUsuarioReceptor]+$total_receptor;
            else
            $total_hor_desa[$codigoUsuarioReceptor]=$total_receptor;

            $total_general=$total_general+$total_receptor;
            $valor_formato_hora= formato_hora($total_receptor);
            ($wformatoTiempo == "HH:MM") ? $valorAmostrar     = $valor_formato_hora[0].":".$valor_formato_hora[1] : $valorAmostrar = $valor_formato_hora[0].".".$valor_formato_hora[1] ;
            //echo "<td align='middle'>".$valorAmostrar."  -- </td>";
            echo "<td style='cursor:pointer;' onclick=javascript:intercalar('".$centro_cost.$codigoUsuarioReceptor."') align='middle'>".$valorAmostrar."</td>";
            //fin pintar

            //pintar total del centro de costos
            $total_cc=$total_cc+$total_receptor;
            $total_todos_cc=$total_todos_cc+$total_cc;
            $valor_formato_hora= formato_hora($total_cc);
            ($wformatoTiempo == "HH:MM") ? $valorAmostrar     = $valor_formato_hora[0].":".$valor_formato_hora[1] : $valorAmostrar = $valor_formato_hora[0].".".$valor_formato_hora[1] ;
            echo "<td align='middle'><b>".$valorAmostrar."</b></td>";
            //fin pintar
            unset($key_desarr);
            unset($val_desarr);
            echo "</tr>";
            $colspan=$n+3;

     /* echo "<pre>";
        print_r( $req_cco );
      echo "</pre>";*/
			//pintar el detalle de todos los desarrolladores (osea cuando den click en el nombre del CC)
			echo "<tr id='".$centro_cost."' style='display:none'>";
			pintar_detalle($centro_cost, $colspan, $req_cco, $wclass2);
			echo "</tr>";
			//fin pintar detalle
			//pintar el detalle de un solo desarrollador (osea cuando den click en las horas de un desarrollador)
			foreach($main[$centro_cost] as $cod_desar => $nombre_des) //Pintar nombre de los desarrolladores
			{
				echo "<tr id='".$centro_cost.$cod_desar."' style='display:none'>";
				pintar_detalle($centro_cost, $colspan, $req_cco, $wclass2, $cod_desar);
				echo "</tr>";
			}
            //fin pintar detalle
        }
        unset($codigo_desarro);
        unset($centro_cost);

        echo "<b><tr class=encabezadoTabla>";
        echo "<td width='7' align='middle' >TOTAL HORAS</td>";
        foreach($nom_desar as $key_desarr=>$nombre_desar) //Pintar nombre de los centros de costos
        {
            if( $key_desarr != $codigoUsuarioReceptor ){
              $valor_formato_hora= formato_hora($total_hor_desa[$key_desarr]);
              ($wformatoTiempo == "HH:MM") ? $valorAmostrar     = $valor_formato_hora[0].":".$valor_formato_hora[1] : $valorAmostrar = $valor_formato_hora[0].".".$valor_formato_hora[1] ;
              echo "<td align='middle' >".$valorAmostrar."</td>";
            }
        }

        $valor_formato_hora= formato_hora($total_general); //total usuario receptor
        ($wformatoTiempo == "HH:MM") ? $valorAmostrar     = $valor_formato_hora[0].":".$valor_formato_hora[1] : $valorAmostrar = $valor_formato_hora[0].".".$valor_formato_hora[1] ;
            echo "<td align='middle' >".$valorAmostrar."</td>";
        $valor_formato_hora= formato_hora($total_todos_cc); //total todos los cc
        ($wformatoTiempo == "HH:MM") ? $valorAmostrar     = $valor_formato_hora[0].":".$valor_formato_hora[1] : $valorAmostrar = $valor_formato_hora[0].".".$valor_formato_hora[1] ;
            echo "<td align='middle' >".$valorAmostrar."</td>";

        echo "</tr></b>";

        echo "</table></center>";
    }

    echo "<br><br><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<center>";
	echo "</form>";
	//=======================================================================================================
    //=======================================================================================================
}
include_once("free.php");
?>