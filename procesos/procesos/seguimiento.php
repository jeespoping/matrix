	<?php
include_once("conex.php");
	include_once("../../webservices/procesos/ws_cliente_mantenimiento.php");
	?>
	<head>
	  <title>SISTEMA DE REQUERIMIENTOS</title>

	  <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	  <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	  <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		

	  <style type="text/css">
			//body{background:white url(portal.gif) transparent center no-repeat scroll;}
			.titulo1{color:#FFFFFF;background:#006699;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;}
			.titulo2{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
			.texto1{color:#003366;background:#DDDDDD;font-size:11pt;font-family:Tahoma;}
			.texto2{color:#003366;background:#DDDDDD;font-size:9pt;font-family:Tahoma;}
			.texto4{color:#003366;background:#C0C0C0;font-size:11pt;font-family:Tahoma;}
			.texto3{color:#003366;background:#C0C0C0;font-size:9pt;font-family:Tahoma;}
			.texto6{color:#FFFFFF;background:#006699;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
			.texto5{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
			.texto7{background:#FFFFFF;font-size:9pt;font-family:Arial;}
			.texto8{background:#DDDDDD;font-size:9pt;font-family:Arial;}
			.campoRequerido{
				border: 1px orange solid;
				background-color:lightyellow;
			}
			
			// .AlinearInputs
			// {
				// float:right;
				// // background-color: #F5F6CE;
			// }
	   </style>

	   <script type="text/javascript">
		$(function() {
			$(document).keydown(function(e){
				var code = (e.keyCode ? e.keyCode : e.which);
				if(code == 116) {
					e.preventDefault();
					// jConfirm('¿Deseas recargar la página?', 'Confirmación', function(r) {
					// 	if(r)
						//location.reload();
					// });
				}
			});
		});
		
		function justNumbers(e)
		{
		var keynum = window.event ? window.event.keyCode : e.which;
		if ((keynum == 8) || (keynum == 0))
		return true;
		 
		return /\d/.test(String.fromCharCode(keynum));
		}
		
		function validar_cambio_estado(){
			
			var control_estado = $("#control_estado").val();
			var datos_select_estreq = $("#select_estreq").val();			
			var select_estreq = datos_select_estreq.split("-");		
			var datos_estado_actual = $("#estado_actual").val();
			
			var estado_actual = datos_estado_actual.split("-");
			
			console.log(estado_actual[0]);
			console.log(select_estreq[0]);
					
			
			if(select_estreq[0] != estado_actual[0]){			
			
				if(control_estado == 'ok'){
					
					enter2();
					
				}else{
					
					alert("Debe seleccionar un estado.");
				}
			}else{
				
				alert("Debe seleccionar un estado diferente a: "+estado_actual[1]);
			}
			
		}
		
		function validar_metodos(){
			
			var cont=0;
			var metodo="";
			
			$('table').find('select[id=especiales3]').each(function(){
					
					metodo=$(this).val();
					
					// if(metodo=="" || metodo=="-1")
					if(metodo=="")
					{
						$(this).addClass('campoRequerido');
						cont++;
						
					}
					else
					{
						$(this).removeClass('campoRequerido');
					}
			});
				
				if(cont>0)
				{
					alert("Debe seleccionar un metodo para cada articulo despachado");
				}
				else{
					// enter2();
					validar_cambio_estado()
				}
		}
		
		function validar_CantidadesVacias(){
			
			var cont=0;
			var metodo="";
			
			$('table').find('input[id=cantidades]').each(function(){
					
					metodo=$(this).val();
					
					// if(metodo=="" || metodo=="-1")
					if(metodo=="")
					{
						$(this).addClass('campoRequerido');
						cont++;
						
					}
					else
					{
						$(this).removeClass('campoRequerido');
					}
			});
				
				if(cont>0)
				{
					alert("Debe ingresar una cantidad");
				}
				else{
					// enter2();
					validar_metodos()
				}
		}
		
	   function enter()
	   {
		document.informatica.submit();
	   }

	   function enter1()
	   {
			$("#seg").removeClass('campoRequerido');
			// if(document.informatica.seg.value != '')
			{
				document.informatica.clareq.options[document.informatica.clareq.selectedIndex].text='';
				document.informatica.submit();
			}
			// else
			// {
			// 	alert('Debe escribir un mensaje de seguimiento');
			// 	return false;
			// }
	   }
		
	   function enter2()
	   {
			$("#seg").removeClass('campoRequerido');
			var es_ok = true;
			if($("#seg").hasClass('requerido') && $("#seg").val().replace(/\s/g,'') == '')
			{
				$("#seg").addClass('campoRequerido');
				es_ok = false;
			}

			if(es_ok)
			{
				document.informatica.grabar.value=1;
				document.informatica.submit();
			}
			else
			{
				alert('Debe escribir alguna descripcion en el seguimiento');
				return false;
			}
	   }
	   
	   
		function cambio_estado(){
			
			$("#control_estado").val('ok');
			
		}
		
	   function obligarSeguimiento(ele)
	   {
			var txtDefecto = $('#select_estreq option:selected').attr("txtDefecto");
			if( $.trim(txtDefecto) != "" ){
				$("#seg").html(txtDefecto);
			}
			$("#estreq").val($('#select_estreq option:selected').html());
			$("#seg").removeClass('campoRequerido requerido');
		   var obligar = $("#select_estreq").val();
		   // alert(obligar);
		   if(obligar == 'on')
		   {
				$("#seg").addClass('requerido');
		   }
	   }

	   function autoMensajeSatisfaccion(ele)
	   {
			var chk = ($("#"+ele.id).is(":checked")) ? 'on': 'off';
			if(chk == 'on')
			{
				jConfirm( "Está seguro que desea recibir el requerimiento a satisfacción?", "ALERTA", function( resp ){
					if( resp ){
						$("#seg").val("Recibido a satisfacción, se aprueba cerrar el requerimiento");
						$("#seg").attr("readonly","readonly");
						//alert('Poner el mensajes, recibido a satisfacción e inactivar el text area');
						marcarRecibidoSatisfaccion(true);
					}
					else
					{
						$("#"+ele.id).attr('checked', false);
						$("#"+ele.id).prop('checked', false);
					}
				});
				
				
			}
			else
			{
				$("#seg").val("")
				$("#seg").removeAttr("readonly");
				marcarRecibidoSatisfaccion(false);
			}
	   }
	   
		function marcarRecibidoSatisfaccion(recibidoSatisfactorio)
		{
			document.informatica.grabar.value=1;
			document.informatica.submit();
		}
		</script>

	</head>

	<body >

	<?php
	/*

	Última actualizacion:
    2020-08-06:
    Arleyda Insignares: * Se agregan nuevos campos : tipo de canal, tipo de falla y tipo de requerimiento. Dichos campos
                          se adicionan a las tablas root_000040 y root_000045.

	2019-11-12:
	Jessica Madrid:		* Se modifica update para requerimientos especiales.
						* Para los requerimientos especiales (esterilizacion) si el insumo no tiene método de esterilización no 
						  se debe asignar por defecto el codigo CMS=Sin método de esterilización, le deben configurar el método 
						  en la tabla cenmat_000003.
	2019-09-30:
	Jessica Madrid:		* Para los requerimientos que están configurados con Recibido a satisfacción se modifica validación ya que se 
						  crea un campo nuevo en root_000043 (Clasuc: indica si el usuario que crea requerimiento debe marcarlo como 
						  satisfactorio -on- o por el contrario la marca puede ser registrada por cualquier usuario que sea diferente 
						  al responsable del requerimiento -off-)
						* El campo: Recibido a satisfacción (Cerrar el requerimiento) se guarda sin necesidad de hacer clic en GRABAR, 
						  además se guarda el usuario, fecha y hora en que se marca el recibido a satisfacción.
	2018-01-23
	Jonatan Lopez:		* Se envia un correo electronico al responsable cuando se asigna un requerimiento, el correo se encuentra en la tabla root_000042 (Perema)
							y el estado que envia correo en la tabla root_000049 (Estema).
	2015-10-13:
    Jessica Madrid:     * Se modifica la funcion consultarUsuarioSeg() para traer el centro de costos del usuario solicitante de root_000040 y si no existe lo consulta en root_000039.
	2015-08-18:
		Jessica Madrid:     * Se crea la tabla root_000110 para controlar los estados que se muestran en cada seguimiento en la función pintarRequerimiento()
							* Se modifica la función consultarClases() para que se muestre la clase del requerimiento, se comenta la linea del query que tiene como 
								condición la cadena que se llena en la misma función.
							* Se modifica la función actualizarEspeciales() para que permita según el tipo de requerimiento actualizar los campos especiales en una tabla
								adicional definida en root_000051. 
							* Se modifica la función pintarRequerimiento() para que de acuerdo al tipo de requiermiento se muestren los campos especiales que tengan información,
								estos tipos de requerimientos deben  estar definidos en root_000051 como se mencionó en el punto anterior. Adicionalmente se agregan títulos si estan definidos en root_000051 para que los campos especiales sean más entendibles.
							
		
	2014-06-25
		Edwar Jaramillo:    * Se crea el parámetro "cco_auditoria_corporativa_clinica" para que cuando se creen requerimientos de auditoria, las personas a las que se le
								asignan los requerimientos puedan cambiar los estados del requerimientos y otras opciones, esta variable simula que el usuario que tiene
								el requerimiento en determinado momento pertenezca al centro de costo auditoria médica.

	2014-04-08
		Camilo Zapata:      * Se modifica el programa para que utilice el campo estseg( seguimiento por defecto ) para cuando se cambia el estado a "en proceso",
							  con el objetivo de que guarde de manera correcta el seguimiento en la tabla 45,

	2013-11-05
		Edwar Jaramillo:    * Validación adicional para que al cambiar el tipo de requerimiento no salga un error en la función actualizarEspeciales().

	2013-10-17
		Edwar Jaramillo:    * Problema al cambiar el tipo de requerimiento, para solucionarlo entonces se recibe el id_req que corresponde al id
								del requerimiento, asi es pues que si se cambia el tipo se generará un nuevo código consecutivo según el tipo
								y no se pierde de referencia los demas datos del requerimiento. A veces se perdía datos del creador del requerimiento
								debído a que se creaba un nuevo consecutivo para el requerimiento.

	2013-10-11
		Edwar Jaramillo:    * Se consulta si el tipo de requerimiento al que se envió la solicitud permite que se cierre el caso por completo cuando
								el encargado del requerimiento cambia el estado de requerimiento a entregado o rechazado (o cualquier estado que
								de por terminado el caso). Esto se hace mediante un parámetro que se creó en root_000041 donde indica que los
								requerimientos de ese tipo los cierra el reponsable del caso. Por tanto no necesita comprobación por parte del
								usuario que crea el requerimiento.

	2013-07-02
		Edwar Jaramillo:    * Se realiza mejoras en cuento a hoja de estilos, se incluye nuevo campo Reqtpn para manejar un único ID por cada requerimiento que se cree, pero el funcionamiento
							  del programa sigue siendo el mismo, si se realiza una modificación a los seguimientos que ahora permite crear más de un seguimiento y se puede ver el historial.
							* Se incluye webservice para cambiar el estado a cerrado la solicitud en el software externo mantenimiento, se incluye jquery para permitir realizar llamados
							  por medio de ajax.
							* Si un requerimiento cambia a estado entregado o rechazado, el formulario obliga a escribir un requerimiento para poder cambiar a esos estados.
							* Se cambia el nombre de la función consultarUsuario por consultarUsuarioSeg para evitar conflicto con ese mismo nombre de función en la librería comun.php
							* Adecuación del programa para permitir escribir más de un seguimiento y poder ver el histórico de los mensajes de seguimiento.
							* Si es un requerimiento de mantenimiento y se cambia a estado rechazado se consume un webservice para que cancele tambien la solicitud en el sistema AM de mantenimiento.

	2008-01-04 Carolina Castano Se agregan campos especiales para llenar por el usuario
	2008-01-04 Carolina Castano Se agregan hora aproximada de atencion y hora de entrega*/
	//----------------------------------------------------------funciones de persitencia------------------------------------------------
	function consultarUsuarioSeg($id, $req)
	{
		global $conex;
		global $wbasedato, $id_req;

		$q1= " SELECT Requso,Reqccs "
		."      FROM ".$wbasedato."_000040  "
		."    WHERE id = '".$id_req."'";

		$res1 = mysql_query($q1,$conex);
		$num1 = mysql_num_rows($res1);
		$row1 = mysql_fetch_array($res1);
		
		
		//Si el centro de costos esta vacio, busca los datos para cualquier centro de costos
		//Si tiene un valor busco los datos del usuario para ese centro de costos
		// echo$q= " SELECT Usucod, Usucco, Usuext, Usuema, Usucar, Ususup, Descripcion "
		// ."       FROM ".$wbasedato."_000039, usuarios "
		// ."    WHERE Usucod = '".$row1['Requso']."' "
		// ."       AND Usuest = 'on' "
		// ."       AND Codigo = usucod ";
		// // ."       AND Activo = 'A' ";

		$q= "SELECT Usucod, Usucco, Usuext, Usuema, Usucar, Ususup, Usused, Descripcion 
			   FROM ".$wbasedato."_000039, usuarios 
			   WHERE Usucod = '".$row1['Requso']."' 
			     AND Codigo = usucod 
		    ORDER BY Usuest DESC 
			   LIMIT 1;";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		$row = mysql_fetch_array($res);

		//si lo encuentra cargo el vector de usuario
		//si no lo encuentra la función devuelve falso
		if ($num>0)
		{
			$usuario['cod']=$row['Usucod'];
			
			if($row1['Reqccs']=="")
			{
				$usuario['cco']=$row['Usucco'];
			}
			else
			{
				$ccosto=explode(")",$row1['Reqccs']);
				$query = " SELECT Cconom 
						FROM movhos_000011 
					   WHERE Ccocod='".$ccosto[1]."'
						 AND Ccoest='on';";

				$respuesta = mysql_query($query, $conex);
				$numResultados = mysql_num_rows($respuesta);
				
				
				$numResultados = mysql_num_rows($respuesta);
				
				if($numResultados>0)
				{
					$rowR = mysql_fetch_array($respuesta);

					$usuario['cco'] = $row1['Reqccs']."-".strtoupper($rowR['Cconom']);
				}
				else
				{
					$query2 = " SELECT Cconom 
								FROM costosyp_000005 
							   WHERE Ccocod='".$ccosto[1]."'
								 AND Ccoest='on';";

					$respuesta2 = mysql_query($query2, $conex);
					$rowR2 = mysql_fetch_array($respuesta2);
					
					$usuario['cco']=$row1['Reqccs']."-".strtoupper($rowR2['Cconom']);
				}
			}
			
			$usuario['ext']=$row['Usuext'];
			$usuario['ema']=$row['Usuema'];
			$usuario['car']=$row['Usucar'];
			$usuario['nom']=$row['Descripcion'];			
			$usuario['sup']=$row['Ususup'];

			// Seleccionar sede
			if ($row['Usused'] !== '')
            {

            	$query = "SELECT Sedcod,Sednom 
						  FROM root_000128
						  WHERE Sedest = 'on'
						    AND Sedcod = '".$row['Usused']."'";

						    //echo ' con '.$query;
				 
				$resultado = mysql_query($query,$conex);
				$cantSed   = mysql_num_rows($resultado);
				
				if ($cantSed>0)
				{
					$rowsed = mysql_fetch_array($resultado);
					$usuario['sed'] = $rowsed['Sednom'];
					
			    }
            }			

		}
		else
		{
			$usuario=false;
		}
		return $usuario;
	}

	function consultarClases($cco, $tipo, $clase)
	{
		global $conex;
		global $wbasedato;

		if ($clase!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
		{
			$clases[0]=$clase;
			$cadena="Rctcla != mid('".$clase."',1,instr('".$clase."','-')-1) AND";
			$inicio=1;
		}
		else
		{
			$centros[0]='';
			$cadena='';
			$inicio=0;
		}

		//consulto los conceptos
		$q =  " SELECT Rctcla, Rctesp, Clades "
		."        FROM ".$wbasedato."_000044, ".$wbasedato."_000043 "
		."      WHERE "
		."            rctcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		."        AND rcttip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."        AND rctest = 'on' "
		."        AND rctcla = clacod "
		."        AND claest = 'on' ";

		// //consulto los conceptos
		// $q =  " SELECT Rctcla, Rctesp, Clades "
		// ."        FROM ".$wbasedato."_000044, ".$wbasedato."_000043 "
		// ."      WHERE ".$cadena." "
		// ."            rctcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		// ."        AND rcttip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		// ."        AND rctest = 'on' "
		// ."        AND rctcla = clacod "
		// ."        AND claest = 'on' ";

		
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$clases[$inicio]=$row1['Rctcla'].'-'.$row1['Clades'].'-'.$row1['Rctesp'];
				$inicio++;
			}
		}
		else
		{
			$clases= false;
		}

		return $clases;
	}

	function consultarRequerimiento($req, $cco, &$tipreq, &$clareq, &$temreq, &$resreq, &$desreq, &$fecap, &$horap, &$porcen, &$fecen, &$horen, &$estreq, &$prireq, &$codigo, &$obsreq, &$fecreq, &$recreq, &$ccoreq, &$acureq, &$horreq, $id, &$wcodigo_caso, &$reqtir, &$reqfal, &$reqcan)
	{
		global $conex;
		global $wbasedato, $id_req;


		$q= " SELECT Reqcco, Reqnum, Reqtip, Reqfec, Requso, Requrc, Reqdes, Reqpurs, Reqpri, Reqest, Reqcla, Reqfae, Reqhae, Reqcum, Hora_data, Reqfen, Reqobe, Reqtde, Reqhen, Reqtpn, Reqsat, Reqtir, Reqfal, Reqcan "
		."       FROM ".$wbasedato."_000040 "
		."    WHERE id = '".$id_req."'";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		if ($num>0)
		{
			$row = mysql_fetch_array($res);

			$q =  " SELECT distinct Usucco as Cconom  "
			."         FROM ".$wbasedato."_000041, ".$wbasedato."_000039 "
			."      WHERE Mtrcco = '".$cco."' "
			."         AND Mtrest='on' "
			."         AND mid(Usucco,1,instr(Usucco,'-')-1)=Mtrcco ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$ccoreq=$row1['Cconom'];
			$numreq=$row['Reqnum'];

			$fecen=$row['Reqfen'];
			if ($fecen=='0000-00-00')
			{
				$fecen='';
			}

			$horen=$row['Reqhen'];
			if ($horen=='00:00:00')
			{
				$horen='';
			}

			$q= " SELECT Mtrdes "
			."      FROM ".$wbasedato."_000041 "
			."    WHERE Mtrcco = '".$row['Reqcco']."' "
			."      AND Mtrcod = '".$row['Reqtip']."' "
			."      AND Mtrest = 'on' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$tipreq=$row['Reqtip'].'-'.$row1['Mtrdes'];
			$fecreq=$row['Reqfec'];
			$horreq=$row['Hora_data'];
			$fecap=$row['Reqfae'];
			$horap=$row['Reqhae'];
			$acureq=$row['Reqcum'].'%';
			$porcen=$row['Reqcum'].'%';
			$obsreq=$row['Reqobe'];
			$wcodigo_caso=$row['Reqtpn'];

			$q= " SELECT Descripcion  "
			."       FROM usuarios "
			."    WHERE Codigo = '".$row['Reqpurs']."' ";
			// ."       AND ACTIVO='A' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$resreq=$row['Reqpurs'].'-'.$row1['Descripcion'];

			$q= " SELECT Descripcion  "
			."       FROM usuarios "
			."    WHERE Codigo = '".$row['Requrc']."' ";
			// ."       AND ACTIVO='A' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$recreq=$row['Requrc'].'-'.$row1['Descripcion'];

			$desreq=$row['Reqdes'];

            // Prioridades
			$q =  " SELECT Descripcion "
			."        FROM det_selecciones "
			."      WHERE Medico='".$wbasedato."' "
			."        AND Codigo='16' "
			."        AND Activo = 'A' "
			."        AND Subcodigo = '".$row['Reqpri']."' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$prireq=$row['Reqpri'].'-'.$row1['Descripcion'];

            //Tipo de requerimiento
            $q =  " SELECT Descripcion "
			."        FROM det_selecciones "
			."      WHERE Medico='".$wbasedato."' "
			."        AND Codigo='20' "
			."        AND Activo = 'A' "
			."        AND Subcodigo = '".$row['Reqtir']."' ";

		    $res2 = mysql_query($q,$conex);
			$row2 = mysql_fetch_array($res2);
			$reqtir=$row['Reqtir'].'-'.$row2['Descripcion'];

			//Tipo de fallas
			$q =  " SELECT Descripcion "
			."        FROM det_selecciones "
			."      WHERE Medico='".$wbasedato."' "
			."        AND Codigo='21' "
			."        AND Activo = 'A' "
			."        AND Subcodigo = '".$row['Reqfal']."' ";
			
			$res3 = mysql_query($q,$conex);
			$row3 = mysql_fetch_array($res3);
			$reqfal=$row['Reqfal'].'-'.$row3['Descripcion'];

			//Tipo de canal
		    $q =  " SELECT Descripcion "
			."        FROM det_selecciones "
			."      WHERE Medico='".$wbasedato."' "
			."        AND Codigo='22' "
			."        AND Activo = 'A' "
			."        AND Subcodigo = '".$row['Reqcan']."' ";
			
			$res4 = mysql_query($q,$conex);
			$row4 = mysql_fetch_array($res4);			
			$reqcan=$row['Reqcan'].'-'.$row4['Descripcion'];

			//consulto estado
			$q =  " SELECT Estnom, Estosg"
			."        FROM ".$wbasedato."_000049 "
			."      WHERE Estest = 'on' "
			."      and Estcod = '".$row['Reqest']."' ";

			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			$estreq=array('descripcion'=>$row['Reqest'].'-'.$row1['Estnom'],'obliga_seguimiento'=>$row1['Estosg']);

			$q =  " SELECT Clades "
			."        FROM ".$wbasedato."_000043 "
			."      WHERE Claest = 'on' "
			."      and Clacod = '".$row['Reqcla']."' ";
			$res1 = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res1);

			if ($row['Reqcla']!='')
			{
				$clareq=$row['Reqcla'].'-'.$row1['Clades'];
			}
			else
			{
				$clareq='';
			}

			//consulto tiempo de dllo
			$q =  " SELECT Subcodigo, Descripcion "
			."        FROM det_selecciones "
			."      WHERE Medico='".$wbasedato."' "
			."        AND Codigo='15' "
			."        AND Subcodigo='".$row['Reqtde']."' "
			."        AND Activo = 'A' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);

			if ($num1>0)
			{
				$row1 = mysql_fetch_array($res1);
				$temreq=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			}
			else
			{
				$temreq='';
			}


		}
	}

	function consultarResponsables($usuario, $cco, $tipo)
	{
		global $conex;
		global $wbasedato;

		$responsables= false;

		//Poner de primero el usuario si este esta activado como responsable
		$q =  " SELECT count(*) "
		."        FROM ".$wbasedato."_000042 "
		."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."        AND Perest = 'on' "
		."        AND Perres= 'on' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);
		if ($row1[0]>0)
		{
			$responsables[0]=$usuario;
		}

		//consulto los responsable de esa clase de ese tipo
		$q =  " SELECT Perusu, Descripcion"
		."        FROM ".$wbasedato."_000042, usuarios "
		."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."        AND Perest = 'on' "
		."        AND Perres= 'on' "
		."        AND Perusu<>  mid('".$usuario."',1,instr('".$usuario."','-')-1) "
		."        AND Perusu= Codigo ";


		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$responsables[$i]=$row1['Perusu'].'-'.$row1['Descripcion'];
			}
		}

		return $responsables;
	}


	function consultarReceptores($usuario, $cco, $tipo)
	{
		global $conex;
		global $wbasedato;

		$receptores= false;

		//Poner de primero el usuario si este esta activado como responsable
		$q =  " SELECT count(*) "
		."        FROM ".$wbasedato."_000042  "
		."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."        AND Perest = 'on' "
		."        AND Perrec= 'on' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);
		if ($row1[0]>0)
		{
			$receptores[0]=$usuario;
		}

		//consulto los responsable de esa clase de ese tipo
		$q =  " SELECT Perusu, Descripcion"
		."        FROM ".$wbasedato."_000042, usuarios "
		."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."        AND Perest = 'on' "
		."        AND Perrec= 'on' "
		."        AND Perusu<>  mid('".$usuario."',1,instr('".$usuario."','-')-1) "
		."        AND Perusu= Codigo ";


		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$receptores[$i]=$row1['Perusu'].'-'.$row1['Descripcion'];
			}
		}

		return $receptores;
	}


	function consultarCanales()
	{
	    global $conex;
		global $wbasedato;

		$canales = array();

		//consulto los conceptos
		$q =  " SELECT Subcodigo, Descripcion 
		          FROM det_selecciones 
		        WHERE Medico='".$wbasedato."' 
		          AND Codigo='22' 
		          AND Activo = 'A' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$canales[$i]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			}
		}

		return $canales;	
	}


	function consultarTipoFallas()
	{
	    global $conex;
		global $wbasedato;

		$fallas = array();

		//consulto los conceptos
		$q =  " SELECT Subcodigo, Descripcion 
		          FROM det_selecciones 
		        WHERE Medico='".$wbasedato."' 
		          AND Codigo='21' 
		          AND Activo = 'A' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$fallas[$i]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			}
		}

		return $fallas;	
	}


	function consultarTipoRequerimientos()
	{
	    global $conex;
		global $wbasedato;

		$requerimientos = array();

		//consulto los conceptos
		$q =  " SELECT Subcodigo, Descripcion 
		          FROM det_selecciones 
		        WHERE Medico='".$wbasedato."' 
		          AND Codigo='20' 
		          AND Activo = 'A' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$requerimientos[$i]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
			}
		}

		return $requerimientos;	
	}


	function consultarPrioridades($prioridad)
	{
		global $conex;
		global $wbasedato;


		if ($prioridad!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
		{
			$prioridades[0]=$prioridad;
			$cadena="Subcodigo != mid('".$prioridad."',1,instr('".$prioridad."','-')-1) AND";
			$inicio=1;
		}
		else
		{
			$prioridades[0]='';
			$cadena='';
			$inicio=0;
		}

		//consulto los conceptos
		$q =  " SELECT Subcodigo, Descripcion "
		."        FROM det_selecciones "
		."      WHERE ".$cadena." "
		."        Medico='".$wbasedato."' "
		."        AND Codigo='16' "
		."        AND Activo = 'A' 
		         ORDER BY Subcodigo ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$prioridades[$inicio]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
				$inicio++;
			}
		}
		else
		{
			$prioridades= false;
		}

		return $prioridades;
	}

	function consultarEstados($estado)
	{
		global $conex, $wbasedato, $select_estreq;

		if (is_array($estado) && array_key_exists('descripcion', $estado) && $estado['descripcion'] != '') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
		{
			$estados[0]=$estado;
			$cadena="Estcod != mid('".$estado['descripcion']."',1,instr('".$estado['descripcion']."','-')-1) AND";
			$inicio=1;
		}
		elseif(!is_array($estado) && $estado!= '')
		{
			$estados[0]=array('descripcion'=>$estado, 'obliga_seguimiento'=>$select_estreq);
			$cadena="Estcod != mid('".$estado."',1,instr('".$estado."','-')-1) AND";
			$inicio=1;
		}
		else
		{
			$estados[0]=array('descripcion'=>'', 'obliga_seguimiento' => '');
			$cadena='';
			$inicio=0;
		}

		//consulto los conceptos
		$q =  " SELECT Estcod, Estnom, Estosg, Estseg "
		."        FROM ".$wbasedato."_000049 "
		."      WHERE ".$cadena
		."        Estest = 'on' order by 1";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=0; $i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$estados[$inicio]=array('descripcion'=>$row1['Estcod'].'-'.$row1['Estnom'],'obliga_seguimiento'=>$row1['Estosg'], 'txt_defecto'=>$row1['Estseg']);
				$inicio++;
			}
		}
		else
		{
			$estados= false;
		}
	// echo "<pre>"; print_r($estados); echo "</pre>";
		return $estados;
	}

	function consultarCerrado($estado)
	{
		global $conex;
		global $wbasedato;

		//consulto los conceptos
		$q =  " SELECT Estfin"
		."        FROM ".$wbasedato."_000049 "
		."      WHERE Estcod=mid('".$estado['descripcion']."',1,instr('".$estado['descripcion']."','-')-1)"
		."        and Estest = 'on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		$row1 = mysql_fetch_array($res1);
		return $row1[0];
	}

	function consultarCerradoSatisfaccion($estado, $wcodigo_caso, &$estado_cerrado, &$satisfaccion)
	{
		global $conex;
		global $wbasedato;

		$q = "	SELECT 	Estfin, Reqsat
				FROM 	root_000040 AS r40
						INNER JOIN
						root_000049 AS r49 ON (Estcod = MID('".$estado['descripcion']."',1,instr('".$estado['descripcion']."','-')-1) )
				WHERE 	r40.Reqtpn = '".$wcodigo_caso."'
						AND Reqest = MID('".$estado['descripcion']."',1,instr('".$estado['descripcion']."','-')-1)
						AND Estest = 'on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		$row1 = mysql_fetch_array($res1);
		$satisfaccion = $row1['Reqsat'];
		$cerrado = $row1['Estfin'];
		$estado_cerrado = $cerrado;

		if($satisfaccion == 'on' && $cerrado == 'on')
		{
			$cerrado = 'on';
		}
		else
		{
			$cerrado = 'off';
		}

		return $cerrado;
	}

	function consultarTiempos($clase, $temreq)
	{
		global $conex;
		global $wbasedato;

		//primero consulto si es necesario printar los tiempos de desarrollo
		$q =  " SELECT Clatde "
		."        FROM ".$wbasedato."_000043 "
		."      WHERE clacod=mid('".$clase."',1,instr('".$clase."','-')-1) "
		."        AND Claest='on' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		if ($row1[0]=='on')
		{
			//consulto los conceptos
			$q =  " SELECT Subcodigo, Descripcion "
			."        FROM det_selecciones "
			."      WHERE Medico='".$wbasedato."' "
			."        AND Codigo='15' "
			."        AND Activo = 'A' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			$tiempos[0]=$temreq;
			if ($num1>0)
			{
				for ($i=1;$i<=$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$tiempos[$i]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
				}
			}
			else
			{
				$tiempos= '';
			}
		}
		else
		{
			$tiempos= '';
		}
		return $tiempos;
	}


	function consultarPorcentaje($clase)
	{
		global $conex;
		global $wbasedato;

		//primero consulto si es necesario printar los tiempos de desarrollo
		$q =  " SELECT Claacu "
		."        FROM ".$wbasedato."_000043 "
		."      WHERE clacod=mid('".$clase."',1,instr('".$clase."','-')-1) "
		."        AND Claest='on' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);

		return $row1[0];
	}

	function consultarCco($usucod)
	{
		global $conex;
		global $wbasedato;

		$q= " SELECT Usucco "
		."       FROM ".$wbasedato."_000039"
		."    WHERE Usucod = '".$usucod."' "
		."       AND Usuest = 'on' ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		//si lo encuentra cargo el vector de usuario
		//si no lo encuentra la función devuelve falso
		if ($num>0)
		{
			$row = mysql_fetch_array($res);
			$usucco=$row['Usucco'];
		}
		else
		{
			$usucco=false;
		}
		return $usucco;
	}

	function consultarTipos($cco, $codigo, $tipo)
	{
		global $conex;
		global $wbasedato;

		if ($tipo!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
		{
			$tipos[0]=$tipo;
			$cadena="Mtrcod != mid('".$tipo."',1,instr('".$tipo."','-')-1) AND";

			$contador=1;
		}
		else
		{
			$cadena='';
			$contador=0;
		}


		$exp=explode('-',$cco);
		$cco=$exp[0];
		if ($cco!='')
		{


			$q =  " SELECT Pertip, Mtrdes, Descripcion, Mtrcod "
			."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041, usuarios "
			."      WHERE ".$cadena." "
			."         Percco= '".$cco."' "
			."         AND Perusu='".$codigo."' "
			."         AND Perest='on' "
			."         AND Pervis='on' "
			."         AND Perrec='on' "
			."         AND Mtrcod=Pertip "
			."         AND Mtrcco=percco "
			."         AND Mtrest='on' "
			."         AND Codigo=Perusu ";
			// ."         AND Activo='A' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				for ($i=1;$i<=$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$tipos[$contador]=$row1[0].'-'.$row1[1].'-('.$row1[0].')-'.$row1[3];
					$contador++;
				}
			}


			$q =  " SELECT Pertip, Mtrdes "
			."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041 "
			."      WHERE ".$cadena." "
			."         Percco= '".$cco."' "
			."         AND Perusu='".$codigo."' "
			."         AND Perest='on' "
			."         AND Pervis<>'on' "
			."         AND Perrec='on' "
			."         AND Mtrcod=Pertip "
			."         AND Mtrcco=percco "
			."         AND Mtrest='on' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				for ($i=1;$i<=$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$tipos[$contador]=$row1[0].'-'.$row1[1];
					$contador++;
				}
			}


			$q =  " SELECT Pertip, Mtrdes"
			."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041 "
			."      WHERE ".$cadena." "
			."         Percco= '".$cco."' "
			."         AND Perusu='".$codigo."' "
			."         AND Perest='on' "
			."         AND Perrec<>'on' "
			."         AND Perres='on' "
			."         AND Mtrcod=Pertip "
			."         AND Mtrcco=percco "
			."         AND Mtrest='on' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				for ($i=1;$i<=$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$tipos[$contador]=$row1[0].'-'.$row1[1];
					$contador++;
				}
			}

			$q =  " SELECT Pertip, Pervis, Mtrdes, Descripcion, Mtrcod "
			."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041, usuarios "
			."      WHERE ".$cadena." "
			."         Percco= '".$cco."' "
			."         AND Perusu<>'".$codigo."' "
			."         AND Perest='on' "
			."         AND Pervis='on' "
			."         AND Mtrcod=Pertip "
			."         AND Mtrcco=percco "
			."         AND Mtrest='on' "
			."         AND Codigo=Perusu ";
			// ."         AND Activo='A' ";
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				for ($i=1;$i<=$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$tipos[$contador]=$row1[0].'-'.$row1[2].'-('.$row1[3].')-'.$row1[4];;
					$contador++;
				}
			}

			$q =  " SELECT Mtrcod, Mtrdes "
			."         FROM ".$wbasedato."_000041 "
			."      WHERE  ".$cadena." "
			."         Mtrcco= '".$cco."' "
			."         AND Mtrcod NOT IN (SELECT Pertip from ".$wbasedato."_000042 where percco='".$cco."' and Pervis='on' ) "
			."         AND Mtrcod NOT IN (SELECT Pertip from ".$wbasedato."_000042 where percco='".$cco."' and Perusu='".$codigo."' ) "
			."         AND Mtrest='on' ";

			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				for ($i=1;$i<=$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$tipos[$contador]=$row1[0].'-'.$row1[1];
					$contador++;
				}
			}
		}
		else
		{
			$tipos[0]='';
		}

		return $tipos;
	}

	function almacenarSeguimiento($codigo, $ccoreq, $acureq, $req, $seg, $env, $segnum, $segest, $tipreq, $id, $wusuario_creacaso, $wcodigo_caso, $prireq, $reqtir, $reqfal, $reqcan)
	{
		global $conex;
		global $wbasedato;

		if(trim($seg) != '')
		{
			$q= " SELECT count(*) "
			."       FROM ".$wbasedato."_000045 "
			."    WHERE Segreq = '".$req."' "
			."       AND Segtip =  mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
			."       AND Segcco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) ";

			$res = mysql_query($q,$conex);
			$row = mysql_fetch_array($res);
			$segnum=$row[0]+1;

			$esmsj_creador   = 'off';
			$esmsj_encargado = 'off';
			if ($codigo == $wusuario_creacaso) { $esmsj_encargado   = 'on'; } // Si el creador del caso escribe seguimiento, entonces es un mensaje para el encargado de resolver el requerimiento.
			else { $esmsj_creador   = 'on'; } // Si el usuario que esta resolviendo el requerimiento escribe un seguimiento, entonces el mensaje es para el creador del requerimiento.

			$q= " INSERT INTO ".$wbasedato."_000045 (  Medico,            Fecha_data,                   Hora_data,    Segfec,    Segcco ,   Segreq,	Segtpn,       Segtip,    Segnum,    Segpcu,    Segtxt,  Segenv,  Segusu,  Segest,  Segmcr,   Segmen,    Segpri,  Segtir,  Segfal, Segcan ,Seguridad) "
			."                               VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".date('Y-m-d')."', mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1), '".$req."',
				'".$wcodigo_caso."', mid('".$tipreq."',1,instr('".$tipreq."','-')-1), '".$segnum."', '".$acureq."', '".$seg."','".$env."', '".$codigo."', '".$segest."', '".$esmsj_creador."', '".$esmsj_encargado."', mid('".$prireq."',1,instr('".$prireq."','-')-1), mid('".$reqtir."',1,instr('".$reqtir."','-')-1), mid('".$reqfal."',1,instr('".$reqfal."','-')-1), mid('".$reqcan."',1,instr('".$reqcan."','-')-1),'C-".$codigo."') ";

			$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GUARDAR EL SEGUIMIENTO ".mysql_error());
		}
	}

	function adecuarFecha($estreq, $fecen, &$horen)
	{
		global $conex;
		global $wbasedato;

		$q =  " SELECT Estfin "
		."         FROM ".$wbasedato."_000049 "
		."      WHERE  Estcod= mid('".$estreq."',1,instr('".$estreq."','-')-1) "
		."         AND Estest='on' ";

		$err = mysql_query($q,$conex) ;

		$row = mysql_fetch_array($err);
		if ($row[0]=='on')
		{
			if ($fecen=='')
			{
				$fecen=date('Y-m-d');
			}

			if ($horen=='')
			{
				$horen=date('H:i:s');
			}
		}
		else
		{
			$fecen='';
			$horen='';
		}
		return $fecen;
	}

	function actualizarEspeciales($ccoreq, $claseq, $clareq, $especiales, $reqnum, $codigo, $id, $metodos,$estreq)
	{
		// var_dump($especiales);
		global $conex;
		global $wbasedato;
		
		global $wusuario,$wusuario_creacaso;

		$q =  " SELECT Rcttab "
		."         FROM ".$wbasedato."_000044 "
		."      WHERE  Rctcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
		."         AND Rcttip=mid('".$claseq."',1,instr('".$claseq."','-')-1) "
		."         AND Rctcla=mid('".$clareq."',1,instr('".$clareq."','-')-1) "
		."         AND Rctest='on' ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA TABLA DE ALMACENAMIENTO DE CAMPOS ESPECIALES ".mysql_error());

		$row = mysql_fetch_array($err);
		$tabnum=$row[0];
		
		$clase=explode('-',$clareq);

		if( trim(str_replace("no aplica","",strtolower($tabnum))) != '')
		{
			$requerimientosEspeciales = consultarAliasPorAplicacion($conex, '01', 'ClasesRequerimientosEspeciales');
		
			$requerimientoEspecial=explode(",",$requerimientosEspeciales);
			
			$EsRequerimientoEspecial=0;
			for($p=0;$p<count($requerimientoEspecial);$p++)
			{
				if($requerimientoEspecial[$p]==$clase[0])
				{
					$EsRequerimientoEspecial=1;
					break;
				}
			}
			
			if($EsRequerimientoEspecial==1)
			{
				$estado=explode('-',$estreq);
				
				$estSolDespach = consultarAliasPorAplicacion($conex, '01', 'EstadoSolicitudesDespachadas');
				
				$query="  SELECT Clamov 
							FROM root_000043 
						   WHERE Clacod=".$clase[0].";";
				$respuesta = mysql_query($query,$conex);
				
				$rowResultado = mysql_fetch_array($respuesta);
				
				if($rowResultado[0]!="")
				{
					$qExiste="SELECT COUNT(id) 
								FROM ".$rowResultado[0].";";
					$respuestaExiste = mysql_query($qExiste,$conex);
					
					$rowExiste = mysql_fetch_array($respuestaExiste);
				}
				
				$insert = " SELECT COUNT(id) FROM ".$rowResultado[0]." WHERE Reqcla='".$clase[0]."' AND Reqnum='".$reqnum."';";
				$errinsert = mysql_query($insert,$conex);
				$numinsert = mysql_num_rows($errinsert);
				
				if($wusuario == $wusuario_creacaso)
				{
					
					$qRecept= "SELECT Perusu 
								FROM ".$wbasedato."_000042 
								WHERE Perusu='".$wusuario."'
								AND Percco=mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1)								
								AND Perrec= 'on';";
	
						
					$recpt= mysql_query($qRecept,$conex);
					$receptores = mysql_fetch_array($recpt);
					$recepto = mysql_num_rows($recpt);
					
					if($receptores[0]==$wusuario)
					{
						// if($arrayTipReq[$tbl]['req']!=$clase[0] or $numinsert<=0)
						if($numinsert>0)
						{
							if($rowResultado[0]!="" && $rowExiste[0]>=0)
							{
								foreach ($especiales as $clave => $valor) 
								{
									if($especiales[$clave]['val']!="")
									{
										$insert = "UPDATE ".$rowResultado[0]." 
										  SET Reqcad='".$especiales[$clave]['val']."',
											  Reqmet='".$metodos[$clave]['val']."',
											  ".(($estSolDespach==$estado[0]) ? "Reqdes='on',": "")."
											  Seguridad='C-".$codigo."'
										  WHERE Reqcla='".$clase[0]."' 
										   AND Reqnum='".$reqnum."' 
										   AND Reqpro='".$clave."';";
										   
										$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());	
									}
								}
							}
						}
					}	
					else
					{
						if($numinsert>0)
						{
							if($rowResultado[0]!="" && $rowExiste[0]>=0)
							{
								foreach ($especiales as $clave => $valor) 
								{
									if($especiales[$clave]['val']!="")
									{
										$insert = "UPDATE ".$rowResultado[0]." 
										  SET Reqcas='".$especiales[$clave]['val']."',
											  Seguridad='C-".$codigo."'
										  WHERE Reqcla='".$clase[0]."' 
										   AND Reqnum='".$reqnum."' 
										   AND Reqpro='".$clave."';";
										   
										$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());	
									}
								}
							}
						}
					}
					
				}
				else
				{
					if($numinsert>0)
					{
						if($rowResultado[0]!="" && $rowExiste[0]>=0)
						{
							foreach ($especiales as $clave => $valor) 
							{
								if($especiales[$clave]['val']!="")
								{
									$insert = "UPDATE ".$rowResultado[0]." 
									  SET Reqcad='".$especiales[$clave]['val']."',
										  Reqmet='".$metodos[$clave]['val']."',
										 ".(($estSolDespach==$estado[0]) ? "Reqdes='on',": "")."
										  Seguridad='C-".$codigo."'
									  WHERE Reqcla='".$clase[0]."' 
									   AND Reqnum='".$reqnum."' 
									   AND Reqpro='".$clave."';";
									   
									$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());	
								}
							}
						}
					}
				}
			}
			else
			{
				$exp=explode('_', $tabnum);
				if(isset($exp[1]))
				{
					$insert = " SELECT * from ".$tabnum." WHERE Espreq='".$reqnum."' ";
				}
				else
				{
					$insert = " SELECT * from ".$wbasedato."_".$tabnum." WHERE Espreq='".$reqnum."' ";
					$tabnum=$wbasedato."_".$tabnum;
				}
		 
				$errinsert = mysql_query($insert,$conex);
				$numinsert = mysql_num_rows($errinsert);
				
				$exp=explode('-',$claseq);
				
			
				if($id!=$exp[0] or $numinsert<=0)
				{
					$insert = " INSERT INTO ".$tabnum." (Medico, Fecha_data, Hora_data, Espreq ";

					$q =  " SELECT Cesnom, Rlcpos "
					."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
					."      WHERE Rlccla = mid('".$clareq."',1,instr('".$clareq."','-')-1) "
					."        AND Rlctip = mid('".$claseq."',1,instr('".$claseq."','-')-1) "
					."        AND Rlccco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
					."        AND Rlcest = 'on' "
					."        AND Cescod = Rlccam "
					."        AND Cesest = 'on' "
					."        Order by 2 ";

					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);

					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($res1);
						$nom=strtolower(substr($row1[0],0, 3));
						$insert=$insert. ", Esp".$nom;
						//echo 'hola';
					}

					$insert=$insert. ", Seguridad )VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."', '".$reqnum."' ";

					for ($i=0;$i<count($especiales);$i++)
					{
						$insert = $insert. ", '".$especiales[$i]['val']."'" ;
					}

					$insert = $insert. ", 'C-".$codigo."' )" ;
				}
				else
				{
					$insert = " UPDATE ".$tabnum." SET ";

					$q =  " SELECT Cesnom, Rlcpos "
					."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
					."      WHERE Rlccla = mid('".$clareq."',1,instr('".$clareq."','-')-1) "
					."        AND Rlctip = mid('".$claseq."',1,instr('".$claseq."','-')-1) "
					."        AND Rlccco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
					."        AND Rlcest = 'on' "
					."        AND Cescod = Rlccam "
					."        AND Cesest = 'on' "
					."        Order by 2 ";

					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);

					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($res1);
						$nom=strtolower(substr($row1[0],0, 3));
						$insert=$insert. " Esp".$nom." = '".$especiales[$i]['val']."',";
					}

					$insert=$insert. " Seguridad = 'C".$codigo."' WHERE Espreq='".$reqnum."' ";
					
				}
			}
			
			// echo $insert;exit();
				 
			$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());
		}
	}

	function actualizarRequerimiento(&$req, $ccoreq, $tipreq, $clareq, $temreq, $recreq, $resreq, $prireq, $acureq, &$estreq, $fecen, $horen, $obsreq, $fecap, $horap, &$id, $wusuario_creacaso, $identificador_unico_req,$reqtir,$reqfal, $reqcan)
	{
		global $conex;
		global $wbasedato;
		global $wsatisfaccion, $id_req;

		$exp=explode('-',$tipreq);
		if($id!=$exp[0])
		{
			$q = "LOCK table ".$wbasedato."_000041 LOW_PRIORITY WRITE";
			$err = mysql_query($q,$conex);

			$q="UPDATE ".$wbasedato."_000041 "
			."SET	Mtrcon = Mtrcon+1 "
			."      WHERE  Mtrcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
			."         AND Mtrest='on' "
			."         AND Mtrcod=mid('".$tipreq."',1,instr('".$tipreq."','-')-1) ";
			$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO ".mysql_error());


			$q =  " SELECT Mtrcon "
			."         FROM ".$wbasedato."_000041 "
			."      WHERE  Mtrcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
			."         AND Mtrest='on' "
			."         AND Mtrcod=mid('".$tipreq."',1,instr('".$tipreq."','-')-1) ";
			$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CONSECUTIVO ".mysql_error());

			$row = mysql_fetch_array($err);

			$q = " UNLOCK TABLES";
			$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA DE CAJEROS ".mysql_error());

	
			$q =  " SELECT Rctesp, Rcttab "
			."         FROM ".$wbasedato."_000044 "
			."      WHERE  Rctcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
			."         AND Rcttip='".$id."' "
			."         AND Rctcla=mid('".$clareq."',1,instr('".$clareq."','-')-1) "
			."         AND Rctest='on' ";

			$err = mysql_query($q,$conex);
			$rows = mysql_fetch_array($err);
			if($rows[0]=='on')
			{
				$tabnum=$rows[1];

				$exp=explode('_', $tabnum);
				if(!isset($exp[1]))
				{
					$tabnum=$wbasedato."_".$tabnum;
				}

				$insert = " DELETE from ".$tabnum." WHERE Espreq='".$req."' ";
				$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO ACTUALIZAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());
			}
		}
		else
		{
			$row[0]=$req;
		}

		// Consultar si el Centro de costo debe conectarse con un webservice
		$q = "	SELECT 	Mtrcon, Mtrdws AS wdireccion_ws, Mtrrws AS wenviarpor_ws
				FROM 	".$wbasedato."_000041
				WHERE  	Mtrcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1)
						AND Mtrest='on'
						AND Mtrcod=mid('".$tipreq."',1,instr('".$tipreq."','-')-1) ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CONSECUTIVO ".mysql_error());

		$rowws           = mysql_fetch_array($err);
		$enviar_por_ws = $rowws['wenviarpor_ws'];
		$ruta_ws       = $rowws['wdireccion_ws'];

		$set_filtro = "Reqest= mid('".$estreq."',1,instr('".$estreq."','-')-1),";

		$exp = explode("-", $ccoreq);
		$ccos_r = $exp[0];

		$exp_est = explode("-", $estreq);
		$estado_req = $exp_est[0];

		//consulto estado
		$q = "  SELECT  Esteam AS westado_am, Estfin AS westado_final, Estema
				FROM    ".$wbasedato."_000049
				WHERE   Estcod = '".$estado_req."'";
		$resultam    = mysql_query($q,$conex);
		$rowam       = mysql_fetch_array($resultam);
		$westado_am  = $rowam['westado_am'];
		$westado_fin = $rowam['westado_final'];
		$wenvia_email = $rowam['Estema'];

		/* CONSULTAR SI EL TIPO DE REQUERIMIENTO LO PUEDE CERRAR EL USUARIO RESPONSABLE DEL REQUERIMIENTO SIN AUTORIZACIÓN DEL SOLICITANTE */
		if($westado_fin == 'on')
		{
			// Consultar si el tipo de requerimiento permite cerrar el caso por parte del usuario responsable de resolverlo.
			$qRCC = "	SELECT	r41.Mtrrcc AS reponsable_cierra_caso
						FROM 	root_000041 AS r41
								INNER JOIN
								root_000040 AS r40 ON (r40.Reqtip = r41.Mtrcod AND r40.Reqcco = r41.Mtrcco)
						WHERE	r40.Reqcco = mid('{$ccoreq}',1,instr('{$ccoreq}','-')-1)
								AND r40.Reqtip = '{$id}'
								AND r41.Mtrrcc = 'on'
						GROUP BY r41.Mtrcco, r41.Mtrcod";
			$resulRCC = mysql_query($qRCC,$conex);
			if(mysql_num_rows($resulRCC) > 0)
			{
				$wsatisfaccion = 'on';
			}
		}

		// Si el centro de costo usa un web service entonces consulta la esquivalencia del código del estado
		if($enviar_por_ws == 'on')
		{

			if($westado_fin == 'on')
			{
				// Envía datos a la función que consume el web service
				$error = cancelarRequerimiento($conex, $wbasedato, $ruta_ws, $identificador_unico_req, $westado_am);
				// Si se reportó un error entonces no se actualiza el estado en matrix, pero los demás campos si.
				if($error == 'on')
				{
					$set_filtro = '';
					pintarAlertAMEst('/!\\ El requerimiento '.$ccos_r.'-'.$req.' no pudo ser modificado en EL SISTEMA DE MANTENIMIENTO, No se cambio el estado /!\\');

					$q = " 	SELECT 	Reqest, r49.Estnom
							FROM 	".$wbasedato."_000040
									INNER JOIN
									".$wbasedato."_000049 AS r49 ON (r49.Estcod = Reqest)
							WHERE  	Reqtpn = '".$identificador_unico_req."'";
					$errEst = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO CONSULTAR EL ESTADO ANTERIOR ".mysql_error());
					$rowEst = mysql_fetch_array($errEst);
					$estreq = $rowEst['Reqest'].'-'.$rowEst['Estnom'];
				}
			}
		}

		
		//=================================== Enviar Correo ================================	
		if($wenvia_email  == 'on'){
		
			$array_per = explode("-",$resreq);
			$codigo_per = $array_per[0];
			
			//consultar correo de la persona asginada
			$qper = "  SELECT Perema
						 FROM ".$wbasedato."_000042
						WHERE Perusu = '".$codigo_per."'";
			$res = mysql_query($qper,$conex);
			$row_per = mysql_fetch_array($res);
			$email_per = $row_per['Perema'];
			$email_per = explode(",", $email_per);
			
			//Traer la configuracion para enviar el correo
			$wcorreopmla = consultarAliasPorAplicacion( $conex, '01', "emailpmla");
			$wcorreopmla = explode("--", $wcorreopmla );
			$wpassword   = $wcorreopmla[1];
			$wremitente  = $wcorreopmla[0];
			$datos_remitente = array();
			$datos_remitente['email']	= $wremitente;
			$datos_remitente['password']= $wpassword;
			$datos_remitente['from'] = $wremitente;
			$datos_remitente['fromName'] = $wremitente;
			
			//Consultar datos del requerimiento
			$qreq = "  SELECT Reqdes, Reqcco, Reqnum
						 FROM ".$wbasedato."_000040
						WHERE id = '".$id_req."'";
			$resreq1 = mysql_query($qreq,$conex);
			$rowreq = mysql_fetch_array($resreq1);
			$req_des = $rowreq['Reqdes'];
			$req_cod = $rowreq['Reqcco']." ".$rowreq['Reqnum'];
			
			//Cuerpo del mensaje
			$message  = "<html><body>";
			$message .= "<table width='100%' bgcolor='#e0e0e0' cellpadding='0' cellspacing='0' border='0'>";
			$message .= "<tr><td>";
			$message .= "<table align='center' width='100%' border='0' cellpadding='0' cellspacing='0' style='background-color:#fff; font-family:Verdana, Geneva, sans-serif;'>";
			$message .= "<thead>
							<tr height='80' align='left'>
								<th colspan='4' style='background-color:#ffffff; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:20px;' >
								<img width='110px' height='70px' src='../../images/medical/root/clinica.JPG'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Requerimiento Asignado ".$req_cod."</th>
							</tr>
						 </thead>";	
			$message .= "<tbody>
						   <tr>
						   <td colspan='4' style='padding:15px;'>
						   <p style='font-size:18px;'>".nl2br($req_des)."</p>
						   </tr>
						   <tr height='80'>
						   <hr />
						   <td colspan='4' align='center' style='background-color:#f5f5f5; border-top:dashed #00a2d1 2px; font-size:24px; '>
						   </td>
						   </tr>
						</tbody>";
			$message .= "</table>";
			$message .= "</td></tr>";
			$message .= "</table>";
			$message .= "</body></html>";
		
			$mensaje = $message;		
			
			$wasunto 		= "Asignación de requerimiento Matrix";
			$altbody 		= "<br> \n\n Requerimiento Matrix";
			
			//Envio del correo
			$sendToEmail = sendToEmail($wasunto,$mensaje,$altbody, $datos_remitente, $email_per, "", "" );	
			
			
		}
		//===================================================================================	
		
		$recibidoSatisfactorio = "";
		if($wsatisfaccion=="on")
		{
			$user_session = explode('-',$_SESSION['user']);
			$wuse = $user_session[1];
			$recibidoSatisfactorio = ",Requsa= '".$wuse."'
									  ,Reqfsa= '".date("Y-m-d")."'
									  ,Reqhsa= '".date("H:i:s")."'";
		}
		
		
		$q= "UPDATE ".$wbasedato."_000040
					SET Reqtip= mid('".$tipreq."',1,instr('".$tipreq."','-')-1),
						Reqcla= mid('".$clareq."',1,instr('".$clareq."','-')-1),
						Requrc= mid('".$recreq."',1,instr('".$recreq."','-')-1),
						Reqtir= mid('".$reqtir."',1,instr('".$reqtir."','-')-1),
						Reqfal= mid('".$reqfal."',1,instr('".$reqfal."','-')-1),
						Reqcan= mid('".$reqcan."',1,instr('".$reqcan."','-')-1),
						Reqpurs= mid('".$resreq."',1,instr('".$resreq."','-')-1),
						Reqfae= '".$fecap."',
						Reqhae= '".$horap."',
						$set_filtro
						Reqcum= mid('".$acureq."',1,instr('".$acureq."','%')-1),
						Reqfen= '".$fecen."',
						Reqhen= '".$horen."',
						Reqfen= '".$fecen."',
						Reqobe= '".$obsreq."',
						Reqtde= mid('".$temreq."',1,instr('".$temreq."','-')-1),
						Reqpri= mid('".$prireq."',1,instr('".$prireq."','-')-1),
						Reqnum= '".$row[0]."',
						Reqsat= '".$wsatisfaccion."'
						".$recibidoSatisfactorio."
				WHERE 	id = '".$id_req."'";

		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR EL REQUERIMIENTO ".mysql_error());

		if($row[0]!=$req)
		{
			$req=$row[0];
		}
	}

	function consultarEspeciales($clase, $cco, $tipo, $usucco, $req, $id)
	{
		global $conex;
		global $wbasedato;

		$clasereq=explode("-",$clase);
		
		$requerimientosEspeciales = consultarAliasPorAplicacion($conex, '01', 'ClasesRequerimientosEspeciales');
		
		$requerimientoEspecial=explode(",",$requerimientosEspeciales);
		
		$EsRequerimientoEspecial=0;
		for($p=0;$p<count($requerimientoEspecial);$p++)
		{
			if($requerimientoEspecial[$p]==$clasereq[0])
			{
				$EsRequerimientoEspecial=1;
				break;
			}
		}
		// echo $usucco."-------".$cco;
		if ($usucco==$cco)
		{
			if($EsRequerimientoEspecial==1)
			{
				$query="  SELECT Cladat,Clamov 
							FROM root_000043 
						   WHERE Clacod='".$clasereq[0]."';";
				$respuesta = mysql_query($query,$conex);
				
				$row = mysql_fetch_array($respuesta);
				
				if($row[0]!="" && $row[1]!="")
				{
					$qExiste="SELECT COUNT(id) 
								FROM ".$row[1].";";
					$respuestaExiste = mysql_query($qExiste,$conex);
					
					$rowExiste = mysql_fetch_array($respuestaExiste);
				}
				
				if($row[0]!="" && $row[1]!="" && $rowExiste[0]>=0)
				{
					$q="  SELECT Procod,Prodes,Reqcas,Reqcad,Reqmet 
							FROM ".$row[0].",".$row[1]."
						   WHERE Reqcla='".$clasereq[0]."' 
							 AND Reqnum='".$req."' 
							 AND Procod=Reqpro 
							 AND Procla='".$clasereq[0]."';";
					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);
					
					if ($num1>0)
					{
						for ($i=0;$i<$num1;$i++)
						{
							$row1 = mysql_fetch_array($res1);
							$especiales[$i]['nombre']=$row1['Procod'].'-'.$row1['Prodes'];
							$especiales[$i]['sel']="off";
							$especiales[$i][0]=$row1['Reqcas'];
							// $contador++;
						}
					}
					else
					{
						$especiales= '';
					}
				}
			}
			else
			{
				$q =  " SELECT Rcttab, Rctesp "
				."        FROM ".$wbasedato."_000044 "
				."       WHERE  Rctcco= mid('".$cco."',1,instr('".$cco."','-')-1) "
				."         AND Rcttip='".$id."' "
				."         AND Rctcla=mid('".$clase."',1,instr('".$clase."','-')-1) "
				."         AND Rctest='on' ";

				$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA TABLA DE ALMACENAMIENTO DE CAMPOS ESPECIALES ".mysql_error());

				$row = mysql_fetch_array($err);
				if ($row[1]=='on')
				{
					$tabnum=$row[0];

					$exp=explode('_', $tabnum);
					if(!isset($exp[1]))
					{
						$tabnum =$wbasedato."_".$tabnum;
					}

					$q =  " SELECT * "
					."        FROM ".$tabnum
					."      WHERE Espreq = '".$req."' ";
					$res3 = mysql_query($q,$conex);
					$row3 = mysql_fetch_array($res3);
					$contador=4;

					//consulto los conceptos
					$q =  " SELECT Rlccam, Rlcpos, Cesnom, Cessel, Cescom, Rlcvis "
					."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
					."      WHERE Rlccla = mid('".$clase."',1,instr('".$clase."','-')-1) "
					."        AND Rlctip ='".$id."' "
					."        AND Rlccco = mid('".$cco."',1,instr('".$cco."','-')-1) "
					."        AND Rlcest = 'on' "
					."        AND Cescod = Rlccam "
					."        AND Cesest = 'on' "
					."        Order by 2 ";
					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);

					if ($num1>0)
					{
						for ($i=0;$i<$num1;$i++)
						{
							$row1 = mysql_fetch_array($res1);
							$especiales[$i]['nombre']=$row1['Rlccam'].'-'.$row1['Cesnom'];
							$especiales[$i]['sel']=$row1['Cessel'];
							$especiales[$i]['vis']=$row1['Rlcvis'];
							if ($especiales[$i]['sel']=='on')
							{
								$especiales[$i][0]=$row3[$contador];

								$exp=explode('-', $row1['Cescom'] );

								$q =  " SELECT Subcodigo, Descripcion "
								."        FROM det_selecciones "
								."      WHERE Medico = '".$exp[0]."' "
								."        AND Codigo = '".$exp[1]."' "
								."        AND Subcodigo <> mid('".$row3[$contador]."',1,instr('".$row3[$contador]."','-')-1) "
								."        AND Activo = 'A' "
								."        Order by 2 asc ";

								$res2 = mysql_query($q,$conex);
								$num2 = mysql_num_rows($res2);
								if ($num2>0)
								{
									for ($j=1;$j<=$num2;$j++)
									{
										$row2 = mysql_fetch_array($res2);
										$especiales[$i][$j]=$row2['Subcodigo'].'-'.$row2['Descripcion'];
									}
									$especiales[$i]['num']=$num2+1;
								}
								$contador++;
							}
							else
							{
								$especiales[$i][0]=$row3[$contador];
								$contador++;
							}
						}
					}
				}
				else
				{
					$especiales='';
				}
			}
		}
		else
		{
			if($EsRequerimientoEspecial==1)
			{
				$query="  SELECT Cladat,Clamov 
							FROM root_000043 
						   WHERE Clacod='".$clasereq[0]."';";
				$respuesta = mysql_query($query,$conex);
				
				$row = mysql_fetch_array($respuesta);
				
				if($row[0]!="" && $row[1]!="")
				{
					$qExiste="SELECT COUNT(id) 
								FROM ".$row[1].";";
					$respuestaExiste = mysql_query($qExiste,$conex);
					
					$rowExiste = mysql_fetch_array($respuestaExiste);
				}
				
				if($row[0]!="" && $row[1]!="" && $rowExiste[0]>=0)
				{
					$q="  SELECT Procod,Prodes,Reqcas,Reqcad,Reqmet 
							FROM ".$row[0].",".$row[1]."
						   WHERE Reqcla='".$clasereq[0]."' 
							 AND Reqnum='".$req."' 
							 AND Procod=Reqpro 
							 AND Procla='".$clasereq[0]."';";
					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);
					
					if ($num1>0)
					{
						for ($i=0;$i<$num1;$i++)
						{
							$row1 = mysql_fetch_array($res1);
							$especiales[$i]['nombre']=$row1['Procod'].'-'.$row1['Prodes'];
							$especiales[$i]['sel']="off";
							$especiales[$i][0]=$row1['Reqcas'];
							// $contador++;
						}
					}
					else
					{
						$especiales= '';
					}
				}
			}
			else
			{
				$q =  " SELECT Rcttab, Rctesp "
				."         FROM ".$wbasedato."_000044 "
				."      WHERE  Rctcco= mid('".$cco."',1,instr('".$cco."','-')-1) "
				."         AND Rcttip='".$id."' "
				."         AND Rctcla=mid('".$clase."',1,instr('".$clase."','-')-1) "
				."         AND Rctest='on' ";

				$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA TABLA DE ALMACENAMIENTO DE CAMPOS ESPECIALES ".mysql_error());

				$row = mysql_fetch_array($err);
				if ($row[1]=='on')
				{
					$tabnum=$row[0];

					$exp=explode('_', $tabnum);
					if(!isset($exp[1]))
					{
						$tabnum =$wbasedato."_".$tabnum;
					}

					$q =  " SELECT * "
					."        FROM ".$tabnum
					."      WHERE Espreq = '".$req."' ";
					$res3 = mysql_query($q,$conex);
					$row3 = mysql_fetch_array($res3);
					$contador=4;

					//consulto los conceptos
					$q =  " SELECT Rlccam, Rlcpos, Cesnom, Cessel, Cescom, Rlcvis "
					."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
					."      WHERE Rlccla = mid('".$clase."',1,instr('".$clase."','-')-1) "
					."        AND Rlctip ='".$id."' "
					."        AND Rlccco = mid('".$cco."',1,instr('".$cco."','-')-1) "
					."        AND Rlcest = 'on' "
					."        AND Cescod = Rlccam "
					."        AND Cesest = 'on' "
					."        Order by 2 ";
					$res1 = mysql_query($q,$conex);
					$num1 = mysql_num_rows($res1);

					if ($num1>0)
					{
						for ($i=0;$i<$num1;$i++)
						{
							$row1 = mysql_fetch_array($res1);
							$especiales[$i]['nombre']=$row1['Rlccam'].'-'.$row1['Cesnom'];
							$especiales[$i]['sel']=$row1['Cessel'];
							$especiales[$i]['vis']=$row1['Rlcvis'];
							if ($especiales[$i]['sel']=='on')
							{
								$especiales[$i][0]=$row3[$contador];

								$exp=explode('-', $row1['Cescom'] );

								$q =  " SELECT Subcodigo, Descripcion "
								."        FROM det_selecciones "
								."      WHERE Medico = '".$exp[0]."' "
								."        AND Codigo = '".$exp[1]."' "
								."        AND Subcodigo <> mid('".$row3[$contador]."',1,instr('".$row3[$contador]."','-')-1) "
								."        AND Activo = 'A' "
								."        Order by 2 asc ";

								$res2 = mysql_query($q,$conex);
								$num2 = mysql_num_rows($res2);
								if ($num2>0)
								{
									for ($j=1;$j<=$num2;$j++)
									{
										$row2 = mysql_fetch_array($res2);
										$especiales[$i][$j]=$row2['Subcodigo'].'-'.$row2['Descripcion'];
									}
									$especiales[$i]['num']=$num2+1;
								}
								$contador++;
							}
							else
							{
								$especiales[$i][0]=$row3[$contador];
								$contador++;
							}
						}

					}
				}
				else
				{
					$especiales='';
				}
			}
		}

		return $especiales;
	}

	function consultarSeguimientos($req, $cco, $usucco, $tipreq, $id, $wcodigo_caso)
	{
		global $conex;
		global $wbasedato;

		$exp=explode('-',$usucco);
		if ($exp[0]==$cco)
		{
			$q= " SELECT Segfec, Segnum, Segpcu, Segtxt, Segusu, Segest, Segpri, Segtir, Segfal, Segcan, Descripcion, Hora_data, seg.id AS id_seg "
			."       FROM ".$wbasedato."_000045 AS seg, usuarios "
			."    WHERE Segtpn = '".$wcodigo_caso."' "
			."       AND Segusu=Codigo "
			."       AND Segtxt<>'' "
			."    ORDER BY Segfec DESC, Hora_data DESC ";

		}
		else
		{
			$q= " SELECT Segfec, Segnum, Segpcu, Segtxt, Segusu, Segest, Segpri, Segtir, Segfal, Segcan, Descripcion, Hora_data, seg.id AS id_seg "
			."       FROM ".$wbasedato."_000045 AS seg, usuarios "
			."    WHERE Segtpn = '".$wcodigo_caso."' "
			."       AND Segenv='on' "
			."       AND Segusu=Codigo "
			."    ORDER BY Segfec DESC, Hora_data DESC ";

		}

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($res);

				$seguimientos[$i]['id_seg'] = $row['id_seg'];
				$seguimientos[$i]['fec']    = $row['Segfec'];
				$seguimientos[$i]['hor']    = $row['Hora_data'];
				$seguimientos[$i]['num']    = $row['Segnum'];
				$seguimientos[$i]['acu']    = $row['Segpcu'];
				$seguimientos[$i]['txt']    = $row['Segtxt'];
				$seguimientos[$i]['est']    = $row['Segest'];
				$seguimientos[$i]['usu']    = $row['Segusu'].'-'.$row['Descripcion'];      

				// Prioridades
				$q =  " SELECT Descripcion "
				."        FROM det_selecciones "
				."      WHERE Medico='".$wbasedato."' "
				."        AND Codigo='16' "
				."        AND Activo = 'A' "
				."        AND Subcodigo = '".$row['Segpri']."' ";

				$res1 = mysql_query($q,$conex);
				$row1 = mysql_fetch_array($res1);
				$seguimientos[$i]['pri']  = $row['Segpri'].'-'.$row1['Descripcion'];

			    //Tipo de requerimiento
	            $q =  " SELECT Descripcion "
				."        FROM det_selecciones "
				."      WHERE Medico='".$wbasedato."' "
				."        AND Codigo='20' "
				."        AND Activo = 'A' "
				."        AND Subcodigo = '".$row['Segtir']."' ";

			    $res2 = mysql_query($q,$conex);
				$row2 = mysql_fetch_array($res2);
				$seguimientos[$i]['tir']  = $row['Segtir'].'-'.$row2['Descripcion'];

				//Tipo de fallas
				$q =  " SELECT Descripcion "
				."        FROM det_selecciones "
				."      WHERE Medico='".$wbasedato."' "
				."        AND Codigo='21' "
				."        AND Activo = 'A' "
				."        AND Subcodigo = '".$row['Segfal']."' ";
				
				$res3 = mysql_query($q,$conex);
				$row3 = mysql_fetch_array($res3);
				$seguimientos[$i]['fal']    = $row['Segfal'].'-'.$row3['Descripcion'];

				//Tipo de canal
			    $q =  " SELECT Descripcion "
				."        FROM det_selecciones "
				."      WHERE Medico='".$wbasedato."' "
				."        AND Codigo='22' "
				."        AND Activo = 'A' "
				."        AND Subcodigo = '".$row['Segcan']."' ";
				
				$res4 = mysql_query($q,$conex);
				$row4 = mysql_fetch_array($res4);			
				$seguimientos[$i]['can']    = $row['Segcan'].'-'.$row4['Descripcion'];	

				
			}

		}
		else
		{
			$seguimientos='';
		}

		return $seguimientos;
	}

	//----------------------------------------------------------funciones de presentacion------------------------------------------------

	function pintarVersion()
	{
		$wautor="Carolina Castaño P.";
		$wversion="2007-04-17";
		echo "<table align='right'>" ;
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
		echo "</tr>" ;
		echo "<tr>" ;
		echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
		echo "</tr>" ;
		echo "</table></br></br></br>" ;
	}

	function pintarTitulo($wacutaliza, $titulo_requerimientos)
	{
		echo encabezado("<div class='titulopagina2'>".$titulo_requerimientos."</div>", $wacutaliza, 'clinica');
		echo "<form name='informatica' action='seguimiento.php' method=post>";
		echo "<table ALIGN=CENTER width='50%'>";
		//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
		//echo "<tr><td class='titulo1'>SISTEMA DE REQUERIMIENTOS</td></tr>";
		echo "<tr><td class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";
	}

	function pintarUsuario($usuario)
	{
		echo "  <table border=0 ALIGN=CENTER width=90%>
					<tr class='encabezadoTabla'>
						<td colspan='4' class='' style='font-size:12pt;' align='center'><b>Informacion del Usuario</b></td>
					</tr>
					<tr>
						<td class='encabezadoTabla'>Codigo: </td>
						<td class='fila1'>".$usuario['cod']." <input type='hidden' id='wusuario_creacaso' name='wusuario_creacaso' value='".$usuario['cod']."'></td>
						<td class='encabezadoTabla'>Nombre: </td>
						<td class='fila1'>".$usuario['nom']."</td>
					</tr>
					<tr>
						<td class='encabezadoTabla'>Centro de costos:</td>
						<td class='fila2'>".$usuario['cco']."</td>
						<td class='encabezadoTabla'>Cargo: </td>
						<td class='fila2'>".$usuario['car']."</td>
					</tr>
					<tr>
						<td class='encabezadoTabla'>Email: </td>
						<td class='fila1'>".$usuario['ema']."</td>
						<td class='encabezadoTabla'>Extension: </td>
						<td class='fila1'>".$usuario['ext']."</td>
					</tr>
					<tr>
						<td class='encabezadoTabla'>Sede: </td>
						<td class='fila1'>".$usuario['sed']."</td>
					</tr>	
				</table>
				</br>";
	}

	function pintarAlert1($mensaje)
	{
		echo '<script language="Javascript">';
		echo 'alert ("'.$mensaje.'")';
		echo '</script>';
	}

	function pintarAlert2($mensaje)
	{
		echo "</table>";
		echo"<CENTER>";
		echo "<table align='center' border=0 bordercolor=#000080 width=700>";
		echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>".$mensaje."</td></tr>";
		echo "</table>";
	}

	function pintarAlertAMEst($mensaje)
	{
		echo "</table>";
		echo"<CENTER>";
		echo "<table align='center' border=0 bordercolor=#000080 width=700>";
		echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center style='color:red; font-weight:bold;'><b>".$mensaje."</td></tr>";
		echo "</table>";
	}

	function pintarRequerimiento($usucco, $req, $cco, $tipos, $clases, $tiempos, $responsables, &$desreq, $fecap, $horap, &$porcen, &$fecen, &$horen, $estados, $prioridades, &$codigo, &$obsreq, &$fecreq, $recreq, &$ccoreq, $porcentaje, $wusuario, $receptores, $seguimientos, $especiales, $horreq, $cerrado, $id, $wcodigo_caso, $wusuario_creacaso, &$estado_cerrado, $satisfaccion,$resreq,$tipoRequerimiento,$tipoFalla,$tipoCanal,$reqtir,$reqfal,$reqcan, $prireq)
	{

		
		global $conex;
		global $id_req;
		global $wbasedato;
		global $ids_segs_pte;
		global $msj_para_creador,  $cco_auditoria_corporativa_clinica;

		$clase = explode('-', $clases[0]);
						
		$requerimientosEspeciales = consultarAliasPorAplicacion($conex, '01', 'ClasesRequerimientosEspeciales');
		
		$requerimientoEspecial=explode(",",$requerimientosEspeciales);
		
		$EsRequerimientoEspecial=0;
		for($p=0;$p<count($requerimientoEspecial);$p++)
		{
			if($requerimientoEspecial[$p]==$clase[0])
			{
				$EsRequerimientoEspecial=1;
				break;
			}
		}
		
		$arr_ids_msjs = array();
		if($ids_segs_pte != '')
		{
			$arr_ids_msjs = explode(",", $ids_segs_pte);
			$campo_upd = ($msj_para_creador == 'on') ? 'Segmcr': 'Segmen';

			// Actualizar el estado de los mensajes no leídos
			$q= "	UPDATE  ".$wbasedato."_000045
							SET ".$campo_upd."= 'off',
								Segusl = '".$wusuario."',
								Segflm = '".date("Y-m-d")."',
								Seghlm = '".date("H:m:s")."'
					WHERE   id IN ('".implode("','", $arr_ids_msjs)."')";

			$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ACTUALIZAR SEGUIMIENTOS QUE TENÍAN ESTADO PENDIENTE POR LEER- ".mysql_error());
		}

		echo "<table border=0 ALIGN=CENTER width=90%>";
		echo "<tr class='encabezadoTabla'><td colspan=2 class='' style='font-size:12pt;' align='center'><b>Requerimiento ".$cco."-".$req." (".$fecreq."-".$horreq.")</b></td></tr>";

		echo "<tr class='fila2'><td class='' colspan=2  align='center'>Centro de costos: ".$ccoreq."</td>";

		// Verificar si el centro de costo del requerimiento es de auditoria corporativa
		$exp_audit_corp = explode("-",$ccoreq);
		$cod_cco_audit_corp = $exp_audit_corp[0];

		// Se reasigna el centro de costro de auditoria al usuario que tiene el requerimiento asignado, para poder que ese usuario
		// cambie el estado del requerimiento y otras opciones de seguimiento, si no se hace entonces el usuario que tiene el requerimiento asignado
		// que generalmente pertecene a otro centro de costo diferente al centro de costo donde llegó el requerimiento (Auditoria inicialmente) el programa
		// solo le permite escribir comentarios en el campo de seguimiento pero no tiene posibilidad de entregar el caso.
		// Una característica del programa de requerimientos es que los requerimientos que llegan al responsable de un centro de costo, solo pueden ser asignados
		// a usuarios dentro de ese mismo centro de costos y ellos mismos cambiar los estados del requerimiento, con esta modificación entonces se simula que
		// el responsable al que se le asignó el caso pertenezca al centro de costo auditoría corporativa.
		if($cod_cco_audit_corp == $cco_auditoria_corporativa_clinica)
		{
			$usucco = $ccoreq;
		}

		$exp=explode('-',$recreq);
	
		
		if ($usucco==$ccoreq && $wusuario==$exp[0])
		{


			echo "<tr class='fila1'><td class='' align='center' colspan='2'>Descripcion:</br><textarea cols='80' rows='4' readonly='readonly'> ".$desreq."</textarea></td></tr>";

			if (count($tiempos)>1)
			{

				echo "<tr class='fila1'><td class='' align='center'>Clase de requerimiento:";
				echo "<select name='clareq' onchange='enter()'>";
				for ($i=0;$i<count($clases);$i++)
				{
					$exp=explode('-',$clases[$i]);
					echo "<option>".$exp[0]."-".$exp[1]."</option>";
				}
				echo "</select></td>";

				echo "<td class='' align='center'>Tiempo de desarrollo:";
				echo "<select name='temreq' onchange='enter()'>";
				for ($i=0;$i<count($tiempos);$i++)
				{
					echo "<option>".$tiempos[$i]."</option>";
				}
				echo "</select></td></tr>";
			}
			else
			{
				echo "<tr class='fila1'><td class=''colspan='2' align='center'>Clase de requerimiento:";
				echo "<select name='clareq' onchange='enter()'>";
				for ($i=0;$i<count($clases);$i++)
				{
					$exp=explode('-',$clases[$i]);
					echo "<option>".$exp[0]."-".$exp[1]."</option>";
				}
				echo "</select></td></tr>";
			}

			echo "<tr class='fila1'><td class='' align='center'> Receptor:";
			echo "<select name='recreq' >";

			for ($i=0;$i<count($receptores);$i++)
			{
				echo "<option>".$receptores[$i]."</option>";
			}
			echo "</select></td>";

			echo "<td class='' align='center'> Responsable:";
			echo "<select name='resreq' >";
			for ($i=0;$i<count($responsables);$i++)
			{
				echo "<option>".$responsables[$i]."</option>";
			}
			echo "</select></td></tr>";

			echo "<tr class='fila1'><td class='' align='center'>Prioridad:";
			echo "<select name='prireq' >";
			for ($i=0;$i<count($prioridades);$i++)
			{
				//echo "<option>".$prioridades[$i]."</option>";
				echo "<option ".(($prireq== $prioridades[$i]) ? 'selected' : '').">".$prioridades[$i]."</option>";
			}
			echo "</select></td>";
			echo "<td class='' align='center'>Fecha y hora aproximada de atención: <input type='TEXT' name='fecap' value='".$fecap."' maxLength=10 size=8><input type='TEXT' name='horap' value='".$horap."' maxLength=10 size=8></td></tr>";

			//Campos Tipo de requerimiento Y Clasificación de Tipo de falla
			echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tipo de requerimiento:";
			echo "<select name='reqtir' >";
			for ($i=0;$i<count($tipoRequerimiento);$i++)
			{
				echo "<option ".(($reqtir== $tipoRequerimiento[$i]) ? 'selected' : '').">".$tipoRequerimiento[$i]."</option>";
			}
			echo "</select></td>";

			echo "<td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='2'").">Tipo de falla:";
			echo "<select name='reqfal' >";
			for ($i=0;$i<count($tipoFalla);$i++)
			{

				echo "<option ".(($reqfal== $tipoFalla[$i]) ? 'selected' : '').">".$tipoFalla[$i]."</option>";
			}
			echo "</select></td></tr>";

            //Clasificacion requerimiento
			echo "<tr class='fila2'><td class='' style='font-weight:bold;' align='center'>Clasificación :";
			echo "<select name='tipreq' onchange='enter1()'>";
			for ($i=0;$i<count($tipos);$i++)
			{
				$exp=explode('-',$tipos[$i]);
				$contador=0;
				if (isset($exp[3]))
				{
					for ($j=0;$j<$i;$j++)
					{
						if ($tipos[$i]==$tipos[$j])
						{
							$contador++;
						}
					}
					$tipos[$i]=$exp[0].'-'.$exp[1];
				}
				if ($contador==0)
				{
					echo "<option>".$tipos[$i]."</option>";
				}
			}
			echo "</select></td>";

            //Campo canal
			echo "<td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tipo de canal:";
			echo "<select name='reqcan' onchange=''>";
			for ($i=0;$i<count($tipoCanal);$i++)
			{

				echo "<option ".(($reqcan== $tipoCanal[$i]) ? 'selected' : '').">".$tipoCanal[$i]."</option>";
			}
			echo "</select></td></tr>";

			if (is_array($especiales))
			{
				$par=2;
				$parEntero=2;
				for ($i=0;$i<count($especiales);$i++)
				{
					$exp=explode('-',$especiales[$i]['nombre']);
					if ($especiales[$i]['sel']=='on')
					{
						if (is_int($par/2))
						{
							echo "<tr class='fila1'>";
						}

						echo "<td align=center class=''>".$exp[1].": <select name='especiales[".$exp[0]."][val]'>";
						for ($j=0;$j<$especiales[$i]['num'];$j++)
						{
							echo "<option>".$especiales[$i][$j]."</option>";
						}
						echo "</select></td>";

						if (!is_int($par/2))
						{
							echo "</tr>";
						}
						$par++;
					}
					else
					{
						if($EsRequerimientoEspecial==1)
						{
							//Consulto la tabla de movimientos(cantidad solicitada y despachada)
							$query="  SELECT Clamov 
										FROM root_000043 
									   WHERE Clacod=".$clase[0].";";
							$respuesta = mysql_query($query,$conex);
							
							$rowResultado = mysql_fetch_array($respuesta);
							
							//Valido que la tabla exista
							if($rowResultado[0]!="")
							{
								$qExiste="SELECT COUNT(id) 
											FROM ".$rowResultado[0].";";
								$respuestaExiste = mysql_query($qExiste,$conex);
								
								$rowExiste = mysql_fetch_array($respuestaExiste);
							}
							
							if($rowResultado[0]!="" && $rowExiste[0]>=0)
							{
								$tablaMovimientos=$rowResultado[0];
							}
							
							//Consulto si el requerimiento especial incluye metodo de esterilizacion
							$tipoRequerimientoConMetodo = consultarAliasPorAplicacion($conex, "01", "SolicitudConMetodoEsterilizacion");
							$tiposConMetodo = explode(',', $tipoRequerimientoConMetodo);
							
							$conMetodo=0;
							for($k=0;$k<count($tiposConMetodo);$k++)
							{
								if($clase[0]==$tiposConMetodo[$k])
								{
									$conMetodo=1;
									break;
								}
								
							}
							// var_dump($especiales);
							
							//La primera vez que entra al ciclo pinta los encabezados
							if($i==0)
							{
								$textoEncab  = consultarAliasPorAplicacion($conex, "01", "EncabezadoCamposTextoReqEspeciales");
								
								$encabez=explode(",",$textoEncab);
								$clsreq = explode('-', $clases[0]);
								$contaEnca=count($encabez);
								$enctipo=0;
								for($y=0;$y<$contaEnca;$y++)
								{
									$encab=explode("-",$encabez[$y]);
									
									if($encab[0]==$clsreq[0])
									{
										$enctipo=1;
										$y=$contaEnca+1;
									}
								}
								
								if($enctipo==1)
								{
									if($conMetodo==1)
									{
										if($wusuario==$wusuario_creacaso)
										{
											$qRecept2= "SELECT Perusu 
														FROM ".$wbasedato."_000042 
														WHERE Perusu='".$wusuario."' 
														AND Percco='".$cco."'
														AND Perrec= 'on';";

												
											$recpt2= mysql_query($qRecept2,$conex);
											$receptores2 = mysql_fetch_array($recpt2);
											
											if($receptores2[0]!=$wusuario) // El usuario no es receptor
											{
												unset($encab[count($encab)-1]);
											}
										}
										
										if(count($encab)==3)
										{
											$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='382px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
															</tr>
														</table>";
										}
										elseif(count($encab)==4)
										{
											$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='272px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
																<td width='110px' align='center'>".((isset($encab[3])) ? $encab[3]: "&nbsp;")."</td>
															</tr>
														</table>";
										}
									}
									else
									{
										$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='382px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
															</tr>
														</table>";
									}
									
									$canEspeciales=0;
									foreach($especiales as $clave => $valor)
									{
										if($especiales[$clave][0]!=0)
										{
											$canEspeciales++;
										}
									}
								
									if($canEspeciales>0)
									{
										if($canEspeciales>1)
										{
											echo "<tr class='fila1'>";
											echo "<td class='' style='font-weight:bold;font-size:8px;'>".$tablaEncabezado."</td>";
											echo "<td class='' style='font-weight:bold;font-size:8px;'>".$tablaEncabezado."</td>";
											echo "</tr>";
										}
										else
										{
											echo "<tr class='fila1'>";
											echo "<td style='font-weight:bold;'>".$tablaEncabezado."</td>";
											echo "<td align='right' style='font-weight:bold;'>&nbsp;</td>";
											echo "</tr>";
										}
									}
									
								}
							}
							
							// var_dump($especiales);
							$numcamposesp=0;
							if($especiales[$i][0]!=0) //Muestro los campos especiales con una cantidad diferente a cero
							{
								if (is_int($par/2))
								{
									echo "<tr class='fila1'>";
								}
							
								$qcampos = "SELECT COUNT(*) FROM ".$tablaMovimientos." WHERE Reqnum='".$req."' AND Reqdes='on';";
				 
								$errcampos= mysql_query($qcampos,$conex);
								$numcampos = mysql_fetch_array($errcampos); //Si es mayor a cero indica que ya se despacho
								// echo$numcampos[0];
								//Consulto las cantidades despachadas
								if($numcampos[0]>0)
								{
									$qcamposesp = "SELECT Reqcad,Reqmet 
													 FROM ".$tablaMovimientos." 
													WHERE Reqcla='".$clsreq[0]."' 
													  AND Reqnum='".$req."' 
													  AND Reqpro='".$exp[0]."';";

									$errcamposesp= mysql_query($qcamposesp,$conex);
									$numcamposesp = mysql_num_rows($errcamposesp);
									$camposesp =  mysql_fetch_array($errcamposesp);
									
									//Consulto el metodo del insumo
									$qMetodos= "SELECT Metcod,Metdes 
									FROM cenmat_000002 
									WHERE Metcod = '".$camposesp[1]."';";
																	
									$metod= mysql_query($qMetodos,$conex);
									$rowMet = mysql_fetch_array($metod);
									$cantMetodos = mysql_num_rows($metod);
									
									if($numcamposesp>0)
									{
										$metodos['cod']=$rowMet[0];
										$metodos['des']=$rowMet[1];
									}
								}
								else
								{
									$qcanDesp = "SELECT Reqcad
													 FROM ".$tablaMovimientos." 
													WHERE Reqcla='".$clsreq[0]."' 
													  AND Reqnum='".$req."' 
													  AND Reqpro='".$exp[0]."';";

									$resCanDesp= mysql_query($qcanDesp,$conex);
									// $numcamposesp = mysql_num_rows($errcamposesp);
									$canDesp =  mysql_fetch_array($resCanDesp);
									
									//Consulto los metodos para el insumo
									if($conMetodo==1)
									{
										$qMetodos= "SELECT Metcod,Metdes 
													  FROM cenmat_000002,cenmat_000003
													 WHERE Rimcod='".$exp[0]."' 
													   AND Rimmet=Metcod 
													   AND Rimest='on';";

											
										$metod= mysql_query($qMetodos,$conex);
										$cantMetodos = mysql_num_rows($metod);
										
										$poscMet=0;
										while ($rowMet = mysql_fetch_array($metod)) 
										{
											$metodos[$poscMet]['cod']=$rowMet[0];
											$metodos[$poscMet]['des']=$rowMet[1];
											$poscMet++;
										}
									}
								}
								
									
								if($wusuario==$wusuario_creacaso)
								{
									$qRecept= "SELECT Perusu 
												FROM ".$wbasedato."_000042 
												WHERE Perusu='".$wusuario."' 
												AND Percco='".$cco."'
												AND Perrec= 'on';";

										
									$recpt= mysql_query($qRecept,$conex);
									$receptores = mysql_fetch_array($recpt);
									// $recepto = mysql_num_rows($recpt);
									
									if($receptores[0]==$wusuario) // El usuario es receptor
									{
										// echo "Forma 1-1";
										echo "<td>
											<table >
												<tr style='font-size:8pt;'>
													<td width='".(($conMetodo==1) ? "272px": "382px")."' align='left'>
														".$exp[1].": 
													</td>
													<td width='72px' align='center'>
														<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
														title='Cantidad solicitada'
														onkeypress=\"return justNumbers(event);\"
														readonly='readonly'/>
													</td>
													<td width='72px' align='center'>
														<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
														style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
														title='Cantidad despachada' 
														onkeypress=\"return justNumbers(event);\"
														".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
														
													</td>";
													if($conMetodo==1)
													{
														echo"
														<td width='110px' align='center'>
															<select name='especiales3[".$exp[0]."][val]' id='especiales3'
															style='background:#FFFFFF;width:120px;display:inline-block;'
															title='Seleccionar metodo' 
															".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>";
															
															if($numcampos[0]>0)
															{
																echo "<option value='".$metodos['cod']."' readonly='readonly'>".$metodos['des']."</option>";
															}
															else
															{
																echo "<option value=''>Seleccione...</option>";
																if($cantMetodos>0)
																{
																	if($cantMetodos==1)
																	{
																		echo "<option value='".$metodos[0]['cod']."' selected>".$metodos[0]['des']."</option>";
																	}
																	else
																	{
																		for($r=0;$r<$cantMetodos;$r++)
																		{
																			echo "<option value='".$metodos[$r]['cod']."'>".$metodos[$r]['des']."</option>";
																		}
																	}
																}
															}
															echo"
															</select>
														</td>";
													}
													else
													{
														echo"<input type='hidden' name='especiales3[".$exp[0]."][val]' id='especiales3' value='' > ";
													}
													echo"
												</tr>
											</table>
										</td>";
										
									}	
									else
									{
										// echo "Forma 1-2";
										echo "<td>
												<table >
													<tr style='font-size:8pt;'>
														<td width='382px' align='left'>
															".$exp[1].": 
														</td>
														<td width='72px' align='center'>
																																
															<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
															title='Cantidad solicitada'
															onkeypress=\"return justNumbers(event);\"
															".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
										
														</td>
														<td width='72px' align='center'>
															<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
															style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
															title='Cantidad despachada' 
															onkeypress=\"return justNumbers(event);\"
															readonly='readonly'/>
														</td>
													</tr>
												</table>
											</td>";
									}
								}
								else
								{
									// echo "Forma 1-3";
									
									echo "<td>
										<table >
											<tr style='font-size:8pt;'>
												<td width='".(($conMetodo==1) ? "272px": "382px")."' align='left'>
													".$exp[1].": 
												</td>
												<td width='72px' align='center'>
													<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
													title='Cantidad solicitada'
													onkeypress=\"return justNumbers(event);\"
													readonly='readonly'/>
												</td>
												<td width='72px' align='center'>
													<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
													style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
													title='Cantidad despachada' 
													onkeypress=\"return justNumbers(event);\"
													".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
												</td>";
												if($conMetodo==1)
												{
													echo"
													<td width='110px' align='center'>
														<select name='especiales3[".$exp[0]."][val]' id='especiales3'
														style='background:#FFFFFF;width:120px;display:inline-block;'
														title='Seleccionar metodo' 
														".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>";
														
														if($numcampos[0]>0)
														{
															echo "<option value='".$metodos['cod']."' readonly='readonly'>".$metodos['des']."</option>";
														}
														else
														{
															echo "<option value=''>Seleccione...</option>";
															if($cantMetodos>0)
															{
																if($cantMetodos==1)
																{
																	echo "<option value='".$metodos[0]['cod']."' selected>".$metodos[0]['des']."</option>";
																}
																else
																{
																	for($r=0;$r<$cantMetodos;$r++)
																	{
																		echo "<option value='".$metodos[$r]['cod']."'>".$metodos[$r]['des']."</option>";
																	}
																}
															}
														}
														
														echo"
														</select>
													</td>";
												}
												else
												{
													echo"<input type='hidden' name='especiales3[".$exp[0]."][val]' id='especiales3' value='' > ";
												}
												
											echo"	
											</tr>
										</table>
									</td>";
								}
								
								if (!is_int($par/2))
								{
									echo "</tr>";
								}
								$par++;
							}
						}
						else
						{
							if (is_int($par/2))
							{
								echo "<tr class='fila1'>";
							}

							echo "<td class='' align='center'>".$exp[1].": <input type='TEXT' name='especiales[".$exp[0]."][val]' value='".$especiales[$i][0]."' size=10></td>";

							if (!is_int($par/2))
							{
								echo "</tr>";
							}
							$par++;
						}
						
						
					}
				}
				if (!is_int($par/2))
				{
					echo "<td class='' align='center'>&nbsp;</td></tr>";

				}
			}

			if (is_array($seguimientos))
			{
				$cant_segs = count($seguimientos);
				$cont_regres = count($seguimientos);
				echo '<tr class="encabezadoTabla"><td colspan="2" style="text-align:center;">Mensajes de seguimiento ('.$cant_segs.')</td></tr>
					<tr class="fila2"><td colspan="2"><div style="overflow:scroll;'.(($cant_segs > 2) ? 'height:235px;': '').'">
						<table style="width: 90%;" align="center">';
				for ($i=0;$i<$cant_segs;$i++)
				{
					$css_msj_pte = "";
					if(in_array($seguimientos[$i]['id_seg'], $arr_ids_msjs))
					{
						$css_msj_pte = "border: orange 2px solid;";
					}
					echo "<tr class='fila1'><td class=''  align=center colspan='2' class=code>Seguimiento ".($cont_regres).":  </br><textarea cols='80' rows='4' readonly='readonly' style='".$css_msj_pte."'>";
					echo "Fecha: ".$seguimientos[$i]['fec']." \n";
					echo "Hora: ".$seguimientos[$i]['hor']." \n";
					echo "De: ".$seguimientos[$i]['usu']."  \n";
					echo "Mensaje: ".$seguimientos[$i]['txt']."  \n";
					echo "Porcentaje de cumplimientos: ".$seguimientos[$i]['acu'].'%'."  \n";
					echo "Prioridad: ".$seguimientos[$i]['pri']."  \n";
					echo "Tipo de requerimiento: ".$seguimientos[$i]['tir']."  \n";
					echo "Tipo de falla: ".$seguimientos[$i]['fal']."  \n";
					echo "Canal o medio: ".$seguimientos[$i]['can']."  \n";
					echo "Estado de requerimiento: ".$seguimientos[$i]['est']."</textarea></td></tr>";
					$cont_regres--;
				}
				echo '</table></div></td></tr>';
			}

			if ($cerrado!='on')
			{
				echo "<tr class='fila1'><td class='' align='center' colspan='2'>Nuevo Seguimiento:  </br><textarea name='seg' id='seg' cols='80' rows='4'></textarea></td></tr>";
			}
			if($porcentaje=='on')
			{
				echo "<tr class='fila1'><td class='' align='center'>Porcentaje de cumplimiento: <input type='TEXT' name='porcen' value='".$porcen."' size=10></td>";
				$colspan=1;
			}
			else
			{
				$colspan=2;
				echo "<tr class='fila1'>";
			}
			
			echo "<td class='' align='center' colspan='".$colspan."'> Estado:";
			
			$qEstados = "SELECT Estcod,Estdes FROM root_000110 WHERE Estreq='".$id."';";
							
			$resEst= mysql_query($qEstados,$conex);
			// $resEstados = mysql_fetch_array($resEst);
			$numEstados = mysql_num_rows($resEst);
			
			if($numEstados>0)
			{
				$posc=0;
				while ($row = mysql_fetch_array($resEst)) 
				{
					$resEstados[$posc]=$row[0]."-".$row[1];
					$posc++;
				}
				
				
				echo "	<input type='hidden' id='control_estado' name='control_estado' value=''>";
				echo "	<input type='hidden' id='estado_actual' name='estado_actual' value='".$estados[0]['descripcion']."'>";
				echo "	<input type='hidden' id='estreq' name='estreq' value='".$estados[0]['descripcion']."'>";
				if($wusuario == $wusuario_creacaso)
				{
					
					$qRecept= "SELECT Perusu 
								FROM ".$wbasedato."_000042 
								WHERE Perusu='".$wusuario."' 
								AND Percco=mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1)	
								AND Perrec= 'on';";
	
						
					$recpt= mysql_query($qRecept,$conex);
					$receptores = mysql_fetch_array($recpt);
					$recepto = mysql_num_rows($recpt);
					
					if($receptores[0]==$wusuario) // El usuario es receptor
					{
						echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this); cambio_estado();'>";
					}	
					else
					{
						echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this);'>";
					}
					
					}
				else
				{
					echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this); cambio_estado();'>";
				}
				
				
						
						if(!in_array($estados[0]['descripcion'],$resEstados))
						{
							echo "<option value='".$estados[0]['descripcion']."' txtDefecto='".$estados[$i]['txt_defecto']."'>".$estados[0]['descripcion']."</option>";
						}
						
						$cont_estados = count($resEstados);
						for ($i=0;$i<$cont_estados;$i++)
						{
							$cont_estados2 = count($estados);
							for ($j=0;$j<$cont_estados2;$j++)
							{
								if($resEstados[$i]==$estados[$j]['descripcion'])
								{
									if($resEstados[$i]==$estados[0]['descripcion'])
									{
										echo "<option value='".$estados[$j]['obliga_seguimiento']."' txtDefecto='".$estados[$j]['txt_defecto']."' selected>".$resEstados[$i]."</option>";
									}
									else
									{
										echo "<option value='".$estados[$j]['obliga_seguimiento']."' txtDefecto='".$estados[$j]['txt_defecto']."'>".$resEstados[$i]."</option>";
									}
									
								}
							}
						}
						
				echo "</select></td></tr>";
				
			}
			else
			{
				echo "	<input type='hidden' id='estreq' name='estreq' value='".$estados[0]['descripcion']."'>
						<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this);'>";
						$cont_estados = count($estados);
						for ($i=0;$i<$cont_estados;$i++)
						{
							echo "<option value='".$estados[$i]['obliga_seguimiento']."' txtDefecto='".$estados[$i]['txt_defecto']."'>".$estados[$i]['descripcion']."</option>";
						}
				echo "</select></td></tr>";
			}
			
			
			
			echo "<tr class='fila2'><td class='' align='center'>Fecha y hora de entrega: <input type='TEXT' name='fecen' value='".$fecen."' maxLength=10 size=8><input type='TEXT' name='horen' value='".$horen."' maxLength=10 size=8></td>";
			echo "<td class='' align='center'>observacion:  </br><textarea name='obsreq' cols='40' rows='4'>".$obsreq."</textarea></td></tr>";

		}
		else if ($usucco==$ccoreq)
		{

			echo "<tr class='fila2'><td class='' align='center' colspan='2'>Tipo de requerimiento:";
			echo "<select name='tipreq' onchange='enter1()'>";
			for ($i=0;$i<count($tipos);$i++)
			{
				$exp=explode('-',$tipos[$i]);
				$contador=0;
				if (isset($exp[3]))
				{
					for ($j=0;$j<$i;$j++)
					{
						if ($tipos[$i]==$tipos[$j])
						{
							$contador++;
						}
					}
					$tipos[$i]=$exp[0].'-'.$exp[1];
				}
				if ($contador==0)
				{
					echo "<option>".$tipos[$i]."</option>";
				}
			}
			echo "</select></td></tr>";

			echo "<tr class='fila2'><td class='' align='center' colspan='2'>Descripcion:</br><textarea cols='80' rows='4' readonly='readonly'> ".$desreq."</textarea></td></tr>";

			if (count($tiempos)>1)
			{

				echo "<tr class='fila1'><td class='' align='center'>Clase de requerimiento:";
				echo "<select name='clareq' onchange='enter()'>";
				for ($i=0;$i<count($clases);$i++)
				{
					$exp=explode('-',$clases[$i]);
					echo "<option>".$exp[0]."-".$exp[1]."</option>";
				}
				echo "</select></td>";

				echo "<td class='' align='center'>Tiempo de desarrollo:";
				echo "<select name='temreq'>";
				for ($i=0;$i<count($tiempos);$i++)
				{
					echo "<option>".$tiempos[$i]."</option>";
				}
				echo "</select></td></tr>";
			}
			else
			{
				echo "<tr class='fila1'><td class=''colspan='2' align='center'>Clase de requerimiento:";
				echo "<select name='clareq' onchange='enter()'>";
				for ($i=0;$i<count($clases);$i++)
				{
					$exp=explode('-',$clases[$i]);
					echo "<option>".$exp[0]."-".$exp[1]."</option>";
				}
				echo "</select></td></tr>";
			}

			echo "<tr class='fila1'><td class='' align='center'> Receptor:";
			echo "<select name='recreq' >";

			for ($i=0;$i<count($receptores);$i++)
			{
				echo "<option>".$receptores[$i]."</option>";
			}
			echo "</select></td>";

			echo "<td class='' align='center'> Responsable:";
			echo "<select name='resreq' >";
			for ($i=0;$i<count($responsables);$i++)
			{
				echo "<option>".$responsables[$i]."</option>";
			}
			echo "</select></td></tr>";

			echo "<tr class='fila1'><td class='' align='center'>Prioridad:";
			echo "<select name='prireq' >";
			for ($i=0;$i<count($prioridades);$i++)
			{
				//echo "<option>".$prioridades[$i]."</option>";
				echo "<option ".(($seguimientos[$i]['pri'] == $prioridades[$i]) ? 'selected' : '').">".$prioridades[$i]."</option>";
			}
			echo "</select></td>";
			echo "<td class='' align='center'>Fecha aproximada de atención: <input type='TEXT' name='fecap' value='".$fecap."' maxLength=10 size=8><input type='TEXT' name='horap' value='".$horap."' maxLength=10 size=8></td></tr>";


			//Campos Tipo de requerimiento Y Clasificación de Tipo de falla
			echo "<tr class='fila2'><td style='font-weight:bold; ' class=''  align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tipo de requerimiento:";
			echo "<select name='reqtir' >";
			for ($i=0;$i<count($tipoRequerimiento);$i++)
			{
				//echo "<option>".$tipoRequerimiento[$i]."</option>";
				echo "<option ".(($reqtir == $tipoRequerimiento[$i]) ? 'selected' : '').">".$tipoRequerimiento[$i]."</option>";
			}
			echo "</select></td>";

			echo "<td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='2'").">Tipo de falla:";
			echo "<select name='reqfal' >";
			for ($i=0;$i<count($tipoFalla);$i++)
			{
				//echo "<option>".$tipoFalla[$i]."</option>";
				echo "<option ".(($reqfal == $tipoFalla[$i]) ? 'selected' : '').">".$tipoFalla[$i]."</option>";
			}
			echo "</select></td></tr>";

			//Campo canal 
			echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='2'").">Tipo de canal1:";
			echo "<select name='reqcan' >";
			for ($i=0;$i<count($tipoCanal);$i++)
			{
				//echo "<option>".$tipoCanal[$i]."</option>";
				echo "<option ".(($reqcan == $tipoCanal[$i]) ? 'selected' : '').">".$tipoCanal[$i]."</option>";
			}

			echo "</select></td></tr>";


			if (is_array($especiales))
			{
				$par=2;
				for ($i=0;$i<count($especiales);$i++)
				{
					$exp=explode('-',$especiales[$i]['nombre']);
					if ($especiales[$i]['sel']=='on')
					{
						if (is_int($par/2))
						{
							echo "<tr class='fila1'>";
						}

						echo "<td align=center class=''>".$exp[1].": <select name='especiales[".$exp[0]."][val]'>";
						for ($j=0;$j<$especiales[$i]['num'];$j++)
						{
							echo "<option>".$especiales[$i][$j]."</option>";
						}
						echo "</select></td>";

						if (!is_int($par/2))
						{
							echo "</tr>";
						}
						$par++;
					}
					else
					{
						if($EsRequerimientoEspecial==1)
						{
							//Consulto la tabla de movimientos(cantidad solicitada y despachada)
							$query="  SELECT Clamov 
										FROM root_000043 
									   WHERE Clacod=".$clase[0].";";
							$respuesta = mysql_query($query,$conex);
							
							$rowResultado = mysql_fetch_array($respuesta);
							
							//Valido que la tabla exista
							if($rowResultado[0]!="")
							{
								$qExiste="SELECT COUNT(id) 
											FROM ".$rowResultado[0].";";
								$respuestaExiste = mysql_query($qExiste,$conex);
								
								$rowExiste = mysql_fetch_array($respuestaExiste);
							}
							
							if($rowResultado[0]!="" && $rowExiste[0]>=0)
							{
								$tablaMovimientos=$rowResultado[0];
							}
							
							//Consulto si el requerimiento especial incluye metodo de esterilizacion
							$tipoRequerimientoConMetodo = consultarAliasPorAplicacion($conex, "01", "SolicitudConMetodoEsterilizacion");
							$tiposConMetodo = explode(',', $tipoRequerimientoConMetodo);
							
							$conMetodo=0;
							for($k=0;$k<count($tiposConMetodo);$k++)
							{
								if($clase[0]==$tiposConMetodo[$k])
								{
									$conMetodo=1;
									break;
								}
								
							}
							// var_dump($especiales);
							
							//La primera vez que entra al ciclo pinta los encabezados
							if($i==0)
							{
								$textoEncab  = consultarAliasPorAplicacion($conex, "01", "EncabezadoCamposTextoReqEspeciales");
								
								$encabez=explode(",",$textoEncab);
								$clsreq = explode('-', $clases[0]);
								$contaEnca=count($encabez);
								$enctipo=0;
								for($y=0;$y<$contaEnca;$y++)
								{
									$encab=explode("-",$encabez[$y]);
									
									if($encab[0]==$clsreq[0])
									{
										$enctipo=1;
										$y=$contaEnca+1;
									}
								}
								
								if($enctipo==1)
								{
									if($conMetodo==1)
									{
										if($wusuario==$wusuario_creacaso)
										{
											$qRecept2= "SELECT Perusu 
														FROM ".$wbasedato."_000042 
														WHERE Perusu='".$wusuario."' 
														AND Percco='".$cco."'
														AND Perrec= 'on';";

												
											$recpt2= mysql_query($qRecept2,$conex);
											$receptores2 = mysql_fetch_array($recpt2);
											
											if($receptores2[0]!=$wusuario) // El usuario no es receptor
											{
												unset($encab[count($encab)-1]);
											}
										}
										
										if(count($encab)==3)
										{
											$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='382px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
															</tr>
														</table>";
										}
										elseif(count($encab)==4)
										{
											$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='272px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
																<td width='110px' align='center'>".((isset($encab[3])) ? $encab[3]: "&nbsp;")."</td>
															</tr>
														</table>";
										}
									}
									else
									{
										$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='382px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
															</tr>
														</table>";
									}
									
									$canEspeciales=0;
									foreach($especiales as $clave => $valor)
									{
										if($especiales[$clave][0]!=0)
										{
											$canEspeciales++;
										}
									}
								
									if($canEspeciales>0)
									{
										if($canEspeciales>1)
										{
											echo "<tr class='fila1'>";
											echo "<td class='' style='font-weight:bold;font-size:8px;'>".$tablaEncabezado."</td>";
											echo "<td class='' style='font-weight:bold;font-size:8px;'>".$tablaEncabezado."</td>";
											echo "</tr>";
										}
										else
										{
											echo "<tr class='fila1'>";
											echo "<td style='font-weight:bold;'>".$tablaEncabezado."</td>";
											echo "<td align='right' style='font-weight:bold;'>&nbsp;</td>";
											echo "</tr>";
										}
									}
									
								}
							}
							
							// var_dump($especiales);
							$numcamposesp=0;
							if($especiales[$i][0]!=0) //Muestro los campos especiales con una cantidad diferente a cero
							{
								if (is_int($par/2))
								{
									echo "<tr class='fila1'>";
								}
							
								$qcampos = "SELECT COUNT(*) FROM ".$tablaMovimientos." WHERE Reqnum='".$req."' AND Reqdes='on';";
				 
								$errcampos= mysql_query($qcampos,$conex);
								$numcampos = mysql_fetch_array($errcampos); //Si es mayor a cero indica que ya se despacho
								// echo$numcampos[0];
								//Consulto las cantidades despachadas
								if($numcampos[0]>0)
								{
									$qcamposesp = "SELECT Reqcad,Reqmet 
													 FROM ".$tablaMovimientos." 
													WHERE Reqcla='".$clsreq[0]."' 
													  AND Reqnum='".$req."' 
													  AND Reqpro='".$exp[0]."';";

									$errcamposesp= mysql_query($qcamposesp,$conex);
									$numcamposesp = mysql_num_rows($errcamposesp);
									$camposesp =  mysql_fetch_array($errcamposesp);
									
									//Consulto el metodo del insumo
									$qMetodos= "SELECT Metcod,Metdes 
									FROM cenmat_000002 
									WHERE Metcod = '".$camposesp[1]."';";
																	
									$metod= mysql_query($qMetodos,$conex);
									$rowMet = mysql_fetch_array($metod);
									$cantMetodos = mysql_num_rows($metod);
									
									if($numcamposesp>0)
									{
										$metodos['cod']=$rowMet[0];
										$metodos['des']=$rowMet[1];
									}
								}
								else
								{
									//Consulto los metodos para el insumo
									if($conMetodo==1)
									{
										$qMetodos= "SELECT Metcod,Metdes 
													  FROM cenmat_000002,cenmat_000003
													 WHERE Rimcod='".$exp[0]."' 
													   AND Rimmet=Metcod 
													   AND Rimest='on';";

											
										$metod= mysql_query($qMetodos,$conex);
										$cantMetodos = mysql_num_rows($metod);
										
										$poscMet=0;
										while ($rowMet = mysql_fetch_array($metod)) 
										{
											$metodos[$poscMet]['cod']=$rowMet[0];
											$metodos[$poscMet]['des']=$rowMet[1];
											$poscMet++;
										}
									}
								}

									
									if($wusuario==$wusuario_creacaso)
									{
										$qRecept= "SELECT Perusu 
													FROM ".$wbasedato."_000042 
													WHERE Perusu='".$wusuario."' 
													AND Percco='".$cco."'
													AND Perrec= 'on';";

											
										$recpt= mysql_query($qRecept,$conex);
										$receptores = mysql_fetch_array($recpt);
										// $recepto = mysql_num_rows($recpt);
										
										if($receptores[0]==$wusuario) // El usuario es receptor
										{
											// echo "Forma 2-1";
											echo "<td>
												<table >
													<tr style='font-size:8pt;'>
														<td width='".(($conMetodo==1) ? "272px": "382px")."' align='left'>
															".$exp[1].": 
														</td>
														<td width='72px' align='center'>
															<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
															title='Cantidad solicitada'
															onkeypress=\"return justNumbers(event);\"
															readonly='readonly'/>
														</td>
														<td width='72px' align='center'>
															<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
															style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
															title='Cantidad despachada' 
															onkeypress=\"return justNumbers(event);\"
															".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
															
														</td>";
														if($conMetodo==1)
														{
															echo"
															<td width='110px' align='center'>
																<select name='especiales3[".$exp[0]."][val]' id='especiales3'
																style='background:#FFFFFF;width:120px;display:inline-block;'
																title='Seleccionar metodo' 
																".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>";
																
																if($numcampos[0]>0)
																{
																	echo "<option value='".$metodos['cod']."' readonly='readonly'>".$metodos['des']."</option>";
																}
																else
																{
																	echo "<option value=''>Seleccione...</option>";
																	if($cantMetodos>0)
																	{
																		if($cantMetodos==1)
																		{
																			echo "<option value='".$metodos[0]['cod']."' selected>".$metodos[0]['des']."</option>";
																		}
																		else
																		{
																			for($r=0;$r<$cantMetodos;$r++)
																			{
																				echo "<option value='".$metodos[$r]['cod']."'>".$metodos[$r]['des']."</option>";
																			}
																		}
																	}
																}
																echo"
																</select>
															</td>";
														}
														echo"
													</tr>
												</table>
											</td>";
											
										}	
										else
										{
											// echo "Forma 2-2";
											echo "<td>
													<table >
														<tr style='font-size:8pt;'>
															<td width='382px' align='left'>
																".$exp[1].": 
															</td>
															<td width='72px' align='center'>
																																	
																<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
																title='Cantidad solicitada'
																onkeypress=\"return justNumbers(event);\"
																".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
											
															</td>
															<td width='72px' align='center'>
																<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
																style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
																title='Cantidad despachada' 
																onkeypress=\"return justNumbers(event);\"
																readonly='readonly'/>
															</td>
														</tr>
													</table>
												</td>";
										}
									}
									else
									{
										// echo "Forma 2-3";
										
										echo "<td>
											<table >
												<tr style='font-size:8pt;'>
													<td width='".(($conMetodo==1) ? "272px": "382px")."' align='left'>
														".$exp[1].": 
													</td>
													<td width='72px' align='center'>
														<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
														title='Cantidad solicitada'
														onkeypress=\"return justNumbers(event);\"
														readonly='readonly'/>
													</td>
													<td width='72px' align='center'>
														<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
														style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
														title='Cantidad despachada' 
														onkeypress=\"return justNumbers(event);\"
														".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
													</td>";
													if($conMetodo==1)
													{
														echo"
														<td width='110px' align='center'>
															<select name='especiales3[".$exp[0]."][val]' id='especiales3'
															style='background:#FFFFFF;width:120px;display:inline-block;'
															title='Seleccionar metodo' 
															".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>";
															
															if($numcampos[0]>0)
															{
																echo "<option value='".$metodos['cod']."' readonly='readonly'>".$metodos['des']."</option>";
															}
															else
															{
																echo "<option value=''>Seleccione...</option>";
																if($cantMetodos>0)
																{
																	if($cantMetodos==1)
																	{
																		echo "<option value='".$metodos[0]['cod']."' selected>".$metodos[0]['des']."</option>";
																	}
																	else
																	{
																		for($r=0;$r<$cantMetodos;$r++)
																		{
																			echo "<option value='".$metodos[$r]['cod']."'>".$metodos[$r]['des']."</option>";
																		}
																	}
																}
															}
															
															echo"
															</select>
														</td>";
													}
													
												echo"	
												</tr>
											</table>
										</td>";
									}
								// }
								
								if (!is_int($par/2))
								{
									echo "</tr>";
								}
								$par++;
							}
						}
						else
						{
							if (is_int($par/2))
							{
								echo "<tr class='fila1'>";
							}

							echo "<td class='' align='center'>".$exp[1].": <input type='TEXT' name='especiales[".$exp[0]."][val]' value='".$especiales[$i][0]."' size=10></td>";

							if (!is_int($par/2))
							{
								echo "</tr>";
							}
							$par++;
						}
						
						
						
					}
				}
				if (!is_int($par/2))
				{
					echo "<td class='' align='center'>&nbsp;</td></tr>";

				}
			}

			if (is_array($seguimientos))
			{
				$cant_segs = count($seguimientos);
				$cont_regres = count($seguimientos);
				echo '<tr class="encabezadoTabla"><td colspan="2" style="text-align:center;">Mensajes de seguimiento ('.$cant_segs.')</td></tr>
					<tr class="fila2"><td colspan="2"><div style="overflow:scroll;'.(($cant_segs > 2) ? 'height:235px;': '').'">
						<table style="width: 90%;" align="center">';
				for ($i=0;$i<$cant_segs;$i++)
				{
					$css_msj_pte = "";
					if(in_array($seguimientos[$i]['id_seg'], $arr_ids_msjs))
					{
						$css_msj_pte = "border: orange 2px solid;";
					}
					echo "<tr class='fila1'><td class=''  align=center colspan='2' class=code>Seguimiento ".($cont_regres).":  </br><textarea cols='80' rows='4' readonly='readonly' style='".$css_msj_pte."'>";
					echo "Fecha: ".$seguimientos[$i]['fec']." \n";
					echo "De: ".$seguimientos[$i]['usu']."  \n";
					echo "Mensaje: ".$seguimientos[$i]['txt']."  \n";
					echo "Porcentaje de cumplimientos: ".$seguimientos[$i]['acu'].'%'."  \n";
					echo "Estado de requerimiento: ".$seguimientos[$i]['est']."</textarea></td></tr>";
					$cont_regres--;
				}
				echo '</table></div></td></tr>';
			}

			if ($cerrado!='on')
			{
				echo "<tr class='fila1'><td class='' align='center' colspan='2'>Nuevo Seguimiento:  </br><textarea name='seg' id='seg' cols='80' rows='4'></textarea></td></tr>";
			}
			if($porcentaje=='on')
			{
				echo "<tr class='fila1'><td class='' align='center'>Porcentaje de cumplimiento: <input type='TEXT' name='porcen' value='".$porcen."' size=10></td>";
				$colspan=1;
			}
			else
			{
				$colspan=2;
				echo "<tr class='fila1'>";
			}
			
			echo "<td class='' align='center' colspan='".$colspan."'> Estado:";
			
			$qEstados = "SELECT Estcod,Estdes FROM root_000110 WHERE Estreq='".$id."';";
							
			$resEst= mysql_query($qEstados,$conex);
			// $resEstados = mysql_fetch_array($resEst);
			$numEstados = mysql_num_rows($resEst);
			
			
			
			if($numEstados>0)
			{
				$posc=0;
				while ($row = mysql_fetch_array($resEst)) 
				{
					$resEstados[$posc]=$row[0]."-".$row[1];
					$posc++;
				}
				
				
				echo "	<input type='hidden' id='control_estado' name='control_estado' value=''>";
				echo "	<input type='hidden' id='estado_actual' name='estado_actual' value='".$estados[0]['descripcion']."'>";
				echo "	<input type='hidden' id='estreq' name='estreq' value='".$estados[0]['descripcion']."'>";
				
				if($wusuario == $wusuario_creacaso)
				{
					$qRecept= "SELECT Perusu 
								FROM ".$wbasedato."_000042 
								WHERE Perusu='".$wusuario."' 
								AND Percco=mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1)	
								AND Perrec= 'on';";
	
						
					$recpt= mysql_query($qRecept,$conex);
					$receptores = mysql_fetch_array($recpt);
					// $recepto = mysql_num_rows($recpt);
				
					if($receptores[0]==$wusuario) // El usuario es receptor
					{
						echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this); cambio_estado();'>";
					}	
					else
					{
						echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this);'>";
					}
					
				}
				else
				{
					echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this); cambio_estado();'>";
				}
				
						if(!in_array($estados[0]['descripcion'],$resEstados))
						{
							echo "<option value='".$estados[0]['descripcion']."' txtDefecto='".$estados[$i]['txt_defecto']."'>".$estados[0]['descripcion']."</option>";
						}
						
						$cont_estados = count($resEstados);
						for ($i=0;$i<$cont_estados;$i++)
						{
							$cont_estados2 = count($estados);
							for ($j=0;$j<$cont_estados2;$j++)
							{
								if($resEstados[$i]==$estados[$j]['descripcion'])
								{
									echo "<option value='".$estados[$j]['obliga_seguimiento']."' txtDefecto='".$estados[$j]['txt_defecto']."'>".$resEstados[$i]."</option>";
								}
							}
						}
						
				echo "</select></td></tr>";
			}
			else
			{
				echo "	<input type='hidden' id='estreq' name='estreq' value='".$estados[0]['descripcion']."'>
						<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this);'>";
				$cont_estados = count($estados);
				for ($i=0;$i<$cont_estados;$i++)
				{
					echo "<option value='".$estados[$i]['obliga_seguimiento']."' txtDefecto='".$estados[$i]['txt_defecto']."'>".$estados[$i]['descripcion']."</option>";
				}
				echo "</select></td></tr>";
			}
			
			
			echo "<tr class='fila2'><td class='' align='center'>Fecha y hora de entrega: <input type='TEXT' name='fecen' value='".$fecen."' maxLength=10 size=8><input type='TEXT' name='horen' value='".$horen."' maxLength=10 size=8></td>";
			echo "<td class='' align='center'>observacion:  </br><textarea name='obsreq' cols='40' rows='4'>".$obsreq."</textarea></td></tr>";
		}
		else
		{
			echo "<tr class='fila2'><td class='' align='center' colspan='2'>Clasificacion requerimiento: ".$tipos[0];
			echo "<input type='hidden' name='tipreq' id='tipreq' value='".$tipos[0]."'>";
			echo "</td></tr>";

			echo "<tr class='fila2'><td class='' align='center' colspan='2'>Descripcion:</br><textarea cols='80' rows='4' readonly='readonly'> ".$desreq."</textarea></td></tr>";

			if (count($tiempos)>1)
			{

				echo "<tr class='fila1' style='display: none;'><td class='' align='center'>Clase de requerimiento:";
				echo "<select name='clareq' onchange='enter()'>";
				for ($i=0;$i<count($clases);$i++)
				{
					$exp=explode('-',$clases[$i]);
					echo "<option>".$exp[0]."-".$exp[1]."</option>";
				}
				echo "</select></td>";

				echo "<td class='' align='center'>Tiempo de desarrollo:";
				echo "<select name='temreq'>";
				for ($i=0;$i<count($tiempos);$i++)
				{
					echo "<option>".$tiempos[$i]."</option>";
				}
				echo "</select></td></tr>";
			}
			else
			{
				echo "<tr class='fila1' style='display: none;'><td class=''colspan='2' align='center'>Clase de requerimiento:";
				echo "<select name='clareq' onchange='enter()'>";
				for ($i=0;$i<count($clases);$i++)
				{
					$exp=explode('-',$clases[$i]);
					echo "<option>".$exp[0]."-".$exp[1]."</option>";
				}
				echo "</select></td></tr>";
			}

			echo "<tr class='fila1' style='display: none;'><td class='' align='center'> Receptor:";
			echo "<select name='recreq' >";

			for ($i=0;$i<count($receptores);$i++)
			{
				echo "<option>".$receptores[$i]."</option>";
			}
			echo "</select></td>";

			echo "<td class='' align='center'> Responsable:";
			echo "<select name='resreq' >";
			for ($i=0;$i<count($responsables);$i++)
			{
				echo "<option>".$responsables[$i]."</option>";
			}
			echo "</select></td></tr>";

			echo "<tr class='fila1' style='display: none;'><td class='' align='center'>Prioridad:";
			echo "<select name='prireq' >";
			for ($i=0;$i<count($prioridades);$i++)
			{
				echo "<option ".(($seguimientos[$i]['pri'] == $prioridades[$i]) ? 'selected' : '').">".$prioridades[$i]."</option>";
			}
			echo "</select></td>";
			echo "<td class='' align='center'>Fecha aproximada de atención: <input type='TEXT' name='fecap' value='".$fecap."' maxLength=10 size=8><input type='TEXT' name='horap' value='".$horap."' maxLength=10 size=8></td></tr>";


			//Campos Tipo de requerimiento Y Clasificación de Tipo de falla
			echo "<tr class='fila2' style='display: none;'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tipo de requerimiento:";
			echo "<select name='reqtir' onchange=''>";
			for ($i=0;$i<count($tipoRequerimiento);$i++)
			{
				echo "<option>".$tipoRequerimiento[$i]."</option>";
			}
			echo "</select></td>";

			echo "<td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='2'").">Tipo de falla:";
			echo "<select name='reqfal' onchange=''>";
			for ($i=0;$i<count($tipoFalla);$i++)
			{
				echo "<option>".$tipoFalla[$i]."</option>";
			}
			echo "</select></td></tr>";

			//Campo canal
			echo "<tr class='fila2' style='display: none;'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='2'").">Tipo de canal3:";
			echo "<select name='reqcan' onchange=''>";
			for ($i=0;$i<count($tipoCanal);$i++)
			{
				echo "<option>".$tipoCanal[$i]."</option>";
			}
			echo "</select></td></tr>";


			if (is_array($especiales))
			{
				$par=2;
				for ($i=0;$i<count($especiales);$i++)
				{
					$exp=explode('-',$especiales[$i]['nombre']);
					if ($especiales[$i]['sel']=='on')
					{
						if (is_int($par/2))
						{
							echo "<tr class='fila1' style='display: none;'>";
						}

						echo "<td align=center class=''>".$exp[1].": <select name='especiales[".$exp[0]."][val]'>";
						for ($j=0;$j<$especiales[$i]['num'];$j++)
						{
							echo "<option>".$especiales[$i][$j]."</option>";
						}
						echo "</select></td>";

						if (!is_int($par/2))
						{
							echo "</tr>";
						}
						$par++;
					}
					else
					{
						if($EsRequerimientoEspecial==1)
						{
							//Consulto la tabla de movimientos(cantidad solicitada y despachada)
							$query="  SELECT Clamov 
										FROM root_000043 
									   WHERE Clacod=".$clase[0].";";
							$respuesta = mysql_query($query,$conex);
							
							$rowResultado = mysql_fetch_array($respuesta);
							
							//Valido que la tabla exista
							if($rowResultado[0]!="")
							{
								$qExiste="SELECT COUNT(id) 
											FROM ".$rowResultado[0].";";
								$respuestaExiste = mysql_query($qExiste,$conex);
								
								$rowExiste = mysql_fetch_array($respuestaExiste);
							}
							
							if($rowResultado[0]!="" && $rowExiste[0]>=0)
							{
								$tablaMovimientos=$rowResultado[0];
							}
							
							//Consulto si el requerimiento especial incluye metodo de esterilizacion
							$tipoRequerimientoConMetodo = consultarAliasPorAplicacion($conex, "01", "SolicitudConMetodoEsterilizacion");
							$tiposConMetodo = explode(',', $tipoRequerimientoConMetodo);
							
							$conMetodo=0;
							for($k=0;$k<count($tiposConMetodo);$k++)
							{
								if($clase[0]==$tiposConMetodo[$k])
								{
									$conMetodo=1;
									break;
								}
								
							}
							// var_dump($especiales);
							
							//La primera vez que entra al ciclo pinta los encabezados
							if($i==0)
							{
								$textoEncab  = consultarAliasPorAplicacion($conex, "01", "EncabezadoCamposTextoReqEspeciales");
								
								$encabez=explode(",",$textoEncab);
								$clsreq = explode('-', $clases[0]);
								$contaEnca=count($encabez);
								$enctipo=0;
								for($y=0;$y<$contaEnca;$y++)
								{
									$encab=explode("-",$encabez[$y]);
									
									if($encab[0]==$clsreq[0])
									{
										$enctipo=1;
										$y=$contaEnca+1;
									}
								}
								
								if($enctipo==1)
								{
									if($conMetodo==1)
									{
										if($wusuario==$wusuario_creacaso)
										{
											$qRecept2= "SELECT Perusu 
														FROM ".$wbasedato."_000042 
														WHERE Perusu='".$wusuario."' 
														AND Percco='".$cco."'
														AND Perrec= 'on';";

												
											$recpt2= mysql_query($qRecept2,$conex);
											$receptores2 = mysql_fetch_array($recpt2);
											
											if($receptores2[0]!=$wusuario) // El usuario no es receptor
											{
												unset($encab[count($encab)-1]);
											}
										}
										
										if(count($encab)==3)
										{
											$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='382px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
															</tr>
														</table>";
										}
										elseif(count($encab)==4)
										{
											$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='272px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
																<td width='110px' align='center'>".((isset($encab[3])) ? $encab[3]: "&nbsp;")."</td>
															</tr>
														</table>";
										}
									}
									else
									{
										$tablaEncabezado = "<table>
															<tr style='font-size:7pt;font-weight:bold;'>
																<td width='382px'></td>
																<td width='72px' align='center'>".((isset($encab[1])) ? $encab[1]: "&nbsp;")."</td>
																<td width='72px' align='center'>".((isset($encab[2])) ? $encab[2]: "&nbsp;")."</td>
															</tr>
														</table>";
									}
									
									$canEspeciales=0;
									foreach($especiales as $clave => $valor)
									{
										if($especiales[$clave][0]!=0)
										{
											$canEspeciales++;
										}
									}
								
									if($canEspeciales>0)
									{
										if($canEspeciales>1)
										{
											echo "<tr class='fila1'>";
											echo "<td class='' style='font-weight:bold;font-size:8px;'>".$tablaEncabezado."</td>";
											echo "<td class='' style='font-weight:bold;font-size:8px;'>".$tablaEncabezado."</td>";
											echo "</tr>";
										}
										else
										{
											echo "<tr class='fila1'>";
											echo "<td style='font-weight:bold;'>".$tablaEncabezado."</td>";
											echo "<td align='right' style='font-weight:bold;'>&nbsp;</td>";
											echo "</tr>";
										}
									}
									
								}
							}
							
							// var_dump($especiales);
							$numcamposesp=0;
							if($especiales[$i][0]!=0) //Muestro los campos especiales con una cantidad diferente a cero
							{
								if (is_int($par/2))
								{
									echo "<tr class='fila1'>";
								}
							
								$qcampos = "SELECT COUNT(*) FROM ".$tablaMovimientos." WHERE Reqnum='".$req."' AND Reqdes='on';";
				 
								$errcampos= mysql_query($qcampos,$conex);
								$numcampos = mysql_fetch_array($errcampos); //Si es mayor a cero indica que ya se despacho
								// echo$numcampos[0];
								//Consulto las cantidades despachadas
								if($numcampos[0]>0)
								{
									$qcamposesp = "SELECT Reqcad,Reqmet 
													 FROM ".$tablaMovimientos." 
													WHERE Reqcla='".$clsreq[0]."' 
													  AND Reqnum='".$req."' 
													  AND Reqpro='".$exp[0]."';";

									$errcamposesp= mysql_query($qcamposesp,$conex);
									$numcamposesp = mysql_num_rows($errcamposesp);
									$camposesp =  mysql_fetch_array($errcamposesp);
									
									//Consulto el metodo del insumo
									$qMetodos= "SELECT Metcod,Metdes 
									FROM cenmat_000002 
									WHERE Metcod = '".$camposesp[1]."';";
																	
									$metod= mysql_query($qMetodos,$conex);
									$rowMet = mysql_fetch_array($metod);
									$cantMetodos = mysql_num_rows($metod);
									
									if($numcamposesp>0)
									{
										$metodos['cod']=$rowMet[0];
										$metodos['des']=$rowMet[1];
									}
								}
								else
								{
									//Consulto los metodos para el insumo
									if($conMetodo==1)
									{
										$qMetodos= "SELECT Metcod,Metdes 
													  FROM cenmat_000002,cenmat_000003
													 WHERE Rimcod='".$exp[0]."' 
													   AND Rimmet=Metcod 
													   AND Rimest='on';";

											
										$metod= mysql_query($qMetodos,$conex);
										$cantMetodos = mysql_num_rows($metod);
										
										$poscMet=0;
										while ($rowMet = mysql_fetch_array($metod)) 
										{
											$metodos[$poscMet]['cod']=$rowMet[0];
											$metodos[$poscMet]['des']=$rowMet[1];
											$poscMet++;
										}
									}
								}
								
									if($wusuario==$wusuario_creacaso)
									{
										$qRecept= "SELECT Perusu 
													FROM ".$wbasedato."_000042 
													WHERE Perusu='".$wusuario."' 
													AND Percco='".$cco."'
													AND Perrec= 'on';";

											
										$recpt= mysql_query($qRecept,$conex);
										$receptores = mysql_fetch_array($recpt);
										// $recepto = mysql_num_rows($recpt);
										
										if($receptores[0]==$wusuario) // El usuario es receptor
										{
											// echo "Forma 3-1";
											echo "<td>
												<table >
													<tr style='font-size:8pt;'>
														<td width='".(($conMetodo==1) ? "272px": "382px")."' align='left'>
															".$exp[1].": 
														</td>
														<td width='72px' align='center'>
															<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
															title='Cantidad solicitada'
															onkeypress=\"return justNumbers(event);\"
															readonly='readonly'/>
														</td>
														<td width='72px' align='center'>
															<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
															style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
															title='Cantidad despachada' 
															onkeypress=\"return justNumbers(event);\"
															".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
															
														</td>";
														if($conMetodo==1)
														{
															echo"
															<td width='110px' align='center'>
																<select name='especiales3[".$exp[0]."][val]' id='especiales3'
																style='background:#FFFFFF;width:120px;display:inline-block;'
																title='Seleccionar metodo' 
																".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>";
																
																if($numcampos[0]>0)
																{
																	echo "<option value='".$metodos['cod']."' readonly='readonly'>".$metodos['des']."</option>";
																}
																else
																{
																	echo "<option value=''>Seleccione...</option>";
																	if($cantMetodos>0)
																	{
																		if($cantMetodos==1)
																		{
																			echo "<option value='".$metodos[0]['cod']."' selected>".$metodos[0]['des']."</option>";
																		}
																		else
																		{
																			for($r=0;$r<$cantMetodos;$r++)
																			{
																				echo "<option value='".$metodos[$r]['cod']."'>".$metodos[$r]['des']."</option>";
																			}
																		}
																	}
																}
																echo"
																</select>
															</td>";
														}
														echo"
													</tr>
												</table>
											</td>";
											
										}	
										else
										{
											// echo "Forma 3-2";
											echo "<td>
													<table >
														<tr style='font-size:8pt;'>
															<td width='382px' align='left'>
																".$exp[1].": 
															</td>
															<td width='72px' align='center'>
																																	
																<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
																title='Cantidad solicitada'
																onkeypress=\"return justNumbers(event);\"
																".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
											
															</td>
															<td width='72px' align='center'>
																<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
																style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
																title='Cantidad despachada' 
																onkeypress=\"return justNumbers(event);\"
																readonly='readonly'/>
															</td>
														</tr>
													</table>
												</td>";
										}
									}
									else
									{
										// echo "Forma 3-3";
										
										echo "<td>
											<table >
												<tr style='font-size:8pt;'>
													<td width='".(($conMetodo==1) ? "272px": "382px")."' align='left'>
														".$exp[1].": 
													</td>
													<td width='72px' align='center'>
														<input type='TEXT' name='especiales[".$exp[0]."][val]' id='cantidades' value='".$especiales[$i][0]."' size=2 style='background:#CEECF5;display:inline-block;text-align:right;margin-right:8px;font-weight:bold'
														title='Cantidad solicitada'
														onkeypress=\"return justNumbers(event);\"
														readonly='readonly'/>
													</td>
													<td width='72px' align='center'>
														<input type='TEXT' name='especiales2[".$exp[0]."][val]' id='cantidades' value='".(($numcampos[0]>0) ? $camposesp[0]: (($canDesp[0]==0) ? $especiales[$i][0]: $canDesp[0]))."' size=2
														style='background:#F5F6CE;display:inline-block;text-align:right;margin-right:8px;'
														title='Cantidad despachada' 
														onkeypress=\"return justNumbers(event);\"
														".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>
													</td>";
													if($conMetodo==1)
													{
														echo"
														<td width='110px' align='center'>
															<select name='especiales3[".$exp[0]."][val]' id='especiales3'
															style='background:#FFFFFF;width:120px;display:inline-block;'
															title='Seleccionar metodo' 
															".(($numcampos[0]>0) ? "readonly='readonly'" : "&nbsp;")."/>";
															
															if($numcampos[0]>0)
															{
																echo "<option value='".$metodos['cod']."' readonly='readonly'>".$metodos['des']."</option>";
															}
															else
															{
																echo "<option value=''>Seleccione...</option>";
																if($cantMetodos>0)
																{
																	if($cantMetodos==1)
																	{
																		echo "<option value='".$metodos[0]['cod']."' selected>".$metodos[0]['des']."</option>";
																	}
																	else
																	{
																		for($r=0;$r<$cantMetodos;$r++)
																		{
																			echo "<option value='".$metodos[$r]['cod']."'>".$metodos[$r]['des']."</option>";
																		}
																	}
																}
															}
															
															echo"
															</select>
														</td>";
													}
													
												echo"	
												</tr>
											</table>
										</td>";
									}
								// }
								
								if (!is_int($par/2))
								{
									echo "</tr>";
								}
								$par++;
							}
						}
						else
						{
							if (is_int($par/2))
							{
								echo "<tr class='fila1'>";
							}

							echo "<td class='' align='center'>".$exp[1].": <input type='TEXT' name='especiales[".$exp[0]."][val]' value='".$especiales[$i][0]."' size=10></td>";

							if (!is_int($par/2))
							{
								echo "</tr>";
							}
							$par++;
						}
						
						
						
					}
				}
				if (!is_int($par/2))
				{
					echo "<td class='' align='center'>&nbsp;</td></tr>";

				}
			}

			if (is_array($seguimientos))
			{
				$cant_segs = count($seguimientos);
				$cont_regres = count($seguimientos);
				echo '<tr class="encabezadoTabla"><td colspan="2" style="text-align:center;">Mensajes de seguimiento ('.$cant_segs.')</td></tr>
					<tr class="fila2"><td colspan="2"><div style="overflow:scroll;'.(($cant_segs > 2) ? 'height:235px;': '').'">
						<table style="width: 90%;" align="center">';
				for ($i=0;$i<$cant_segs;$i++)
				{
					$css_msj_pte = "";
					if(in_array($seguimientos[$i]['id_seg'], $arr_ids_msjs))
					{
						$css_msj_pte = "border: orange 2px solid;";
					}
					echo "<tr class='fila1'><td class=''  align=center colspan='2' class=code>Seguimiento ".($cont_regres).":  </br><textarea cols='80' rows='4' readonly='readonly' style='".$css_msj_pte."'>";
					echo "Fecha: ".$seguimientos[$i]['fec']." \n";
					echo "De: ".$seguimientos[$i]['usu']."  \n";
					echo "Mensaje: ".$seguimientos[$i]['txt']."  \n";
					echo "Porcentaje de cumplimientos: ".$seguimientos[$i]['acu'].'%'."  \n";
					echo "Estado de requerimiento: ".$seguimientos[$i]['est']."</textarea></td></tr>";
					$cont_regres--;
				}
				echo '</table></div></td></tr>';
			}

			if ($cerrado!='on')
			{
				echo "<tr class='fila1' ><td class='' align='center' colspan='2'>Nuevo Seguimiento:  </br><textarea name='seg' id='seg' cols='80' rows='4'></textarea></td></tr>";
			}
			if($porcentaje=='on')
			{
				echo "<tr class='fila1' style='display: none;'><td class='' align='center'>Porcentaje de cumplimiento: <input type='TEXT' name='porcen' value='".$porcen."' size=10></td>";
				$colspan=1;
			}
			else
			{
				$colspan=2;
				// echo "<tr class='fila1' style='display: none;'>";
				echo "<tr class='fila1' >";
			}
			
			echo "<td class='' align='center' colspan='".$colspan."'> Estado:";
			
			$qEstados = "SELECT Estcod,Estdes FROM root_000110 WHERE Estreq='".$id."';";
							
			$resEst= mysql_query($qEstados,$conex);
			// $resEstados = mysql_fetch_array($resEst);
			$numEstados = mysql_num_rows($resEst);
			
			if($numEstados>0)
			{
				$posc=0;
				while ($row = mysql_fetch_array($resEst)) 
				{
					$resEstados[$posc]=$row[0]."-".$row[1];
					$posc++;
				}
				
				
				echo "	<input type='hidden' id='control_estado' name='control_estado' value=''>";
				echo "	<input type='hidden' id='estado_actual' name='estado_actual' value='".$estados[0]['descripcion']."'>";
				echo "	<input type='hidden' id='estreq' name='estreq' value='".$estados[0]['descripcion']."'>";
				
				
				if($wusuario == $wusuario_creacaso)
				{
					$qRecept= "SELECT Perusu 
								FROM ".$wbasedato."_000042 
								WHERE Perusu='".$wusuario."' 
								AND Percco=mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1)	
								AND Perrec= 'on';";
	
						
					$recpt= mysql_query($qRecept,$conex);
					$receptores = mysql_fetch_array($recpt);
					// $recepto = mysql_num_rows($recpt);
					
					if($receptores[0]==$wusuario) // El usuario es receptor
					{
						echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this); cambio_estado();'>";
					}	
					else
					{
						echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this);'>";
					}
					
				}
				else
				{
					echo "	<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this); cambio_estado();'>";
				}
						
						if(!in_array($estados[0]['descripcion'],$resEstados))
						{
							echo "<option value='".$estados[0]['descripcion']."' txtDefecto='".$estados[$i]['txt_defecto']."'>".$estados[0]['descripcion']."</option>";
						}
						
						$cont_estados = count($resEstados);
						for ($i=0;$i<$cont_estados;$i++)
						{
							$cont_estados2 = count($estados);
							for ($j=0;$j<$cont_estados2;$j++)
							{
								if($resEstados[$i]==$estados[$j]['descripcion'])
								{
									echo "<option value='".$estados[$j]['obliga_seguimiento']."' txtDefecto='".$estados[$j]['txt_defecto']."'>".$resEstados[$i]."</option>";
								}
							}
						}
						
				echo "</select></td></tr>";
			
			}
			else
			{
				echo "	<input type='hidden' id='estreq' name='estreq' value='".$estados[0]['descripcion']."'>
						<select name='select_estreq' id='select_estreq' onchange='obligarSeguimiento(this);'>";
				$cont_estados = count($estados);
				for ($i=0;$i<$cont_estados;$i++)
				{
					echo "<option value='".$estados[$i]['obliga_seguimiento']."' txtDefecto='".$estados[$i]['txt_defecto']."'>".$estados[$i]['descripcion']."</option>";
				}
				echo "</select></td></tr>";
			}
			
			
			
			// echo "<tr class='fila2' style='display: none;'><td class='' align='center'>Fecha y hora de entrega: <input type='TEXT' name='fecen' value='".$fecen."' maxLength=10 size=8><input type='TEXT' name='horen' value='".$horen."' maxLength=10 size=8></td>";
			echo "<tr class='fila2' ><td class='' align='center'>Fecha y hora de entrega: <input type='TEXT' name='fecen' value='".$fecen."' maxLength=10 size=8><input type='TEXT' name='horen' value='".$horen."' maxLength=10 size=8></td>";
			echo "<td class='' align='center'>observacion:  </br><textarea name='obsreq' cols='40' rows='4'>".$obsreq."</textarea></td></tr>";
		}
	
		// if($wusuario_creacaso == $wusuario && $estado_cerrado == 'on' && $satisfaccion != 'on')
		if($estado_cerrado == 'on' && $satisfaccion != 'on')
		{
			$marcaSatisfactorioUsuarioCrea = consultarUsuarioSatisfactorio($conex,$clsreq[0]);
			$usuarioEncargado = explode("-",$resreq);
			
			if(($marcaSatisfactorioUsuarioCrea && $wusuario_creacaso == $wusuario) || (!$marcaSatisfactorioUsuarioCrea && $usuarioEncargado[0] != $wusuario))
			{
				echo '	<tr class="fila1">
							<td class="" colspan="5" style="text-align: center; font-weight: bold;">
								Recibido a satisfacción (Cerrar el requerimiento) <input type="checkbox" id="wsatisfaccion" name="wsatisfaccion" value="on" onclick="autoMensajeSatisfaccion(this);">
							</td>
						</tr>';
			}
		}
		echo "</table>";

		echo "	<input type='HIDDEN' name= 'desreq' value='".$desreq."'>
				<input type='HIDDEN' name= 'fecreq' value='".$fecreq."'>
				<input type='HIDDEN' name= 'horreq' value='".$horreq."'>
				<input type='HIDDEN' name= 'id' value='".$id."'>
				<input type='HIDDEN' name= 'id_req' value='".$id_req."'>
				<input type='hidden' id='wcodigo_caso' name='wcodigo_caso' value='".$wcodigo_caso."'>";
	}


	function pintarBoton($cco, $req, $ccoreq, $id,$estado_cerrado)
	{
		global $usucco, $ccoreq;
		global $conex;
		global $wusuario,$wusuario_creacaso;
		global $wbasedato;
		
		$qEstados = "SELECT Estcod,Estdes FROM root_000110 WHERE Estreq='".$id."';";						
		$resEst= mysql_query($qEstados,$conex);
		$numEstados = mysql_num_rows($resEst);
				
		echo "<table border=0 ALIGN=CENTER width=90%>";
		echo "<tr class='fila1'><td class='' colspan=5 >&nbsp;</td></tr>";
		echo "<td class='fila1' colspan=5 align='center'><span style='display:none;'><input type='checkbox' name='enviar' checked='checked'>ENVIAR(al usuario)&nbsp&nbsp;</span>";
		echo "<input type='HIDDEN' name= 'cco' value='".$cco."'>";

		if($usucco!=$ccoreq)
		{
			echo "<input type='hidden' name= 'wno_permisos' name= 'wno_permisos' value='wno_permisos'>";
		}

		echo "<input type='HIDDEN' name= 'req' value='".$req."'>";
		echo "<input type='HIDDEN' name= 'ccoreq' value='".$ccoreq."'>";
		echo "<input type='HIDDEN' name= 'grabar' value='0'>";
		if($numEstados > 0){
			
			if($estado_cerrado=="off")
			{
				if($wusuario == $wusuario_creacaso)
				{
					$qRecept= "SELECT Perusu 
								FROM ".$wbasedato."_000042 
								WHERE Perusu='".$wusuario."' 
								AND Percco=mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1)	
								AND Perrec= 'on';";
	
						
					$recpt= mysql_query($qRecept,$conex);
					$receptores = mysql_fetch_array($recpt);
					$recepto = mysql_num_rows($recpt);
					
					if($receptores[0]==$wusuario) // El usuario es receptor
					{
						// echo "<INPUT TYPE='button' NAME='ok' VALUE='GRABAR' onclick='validar_cambio_estado();'></td></tr>";
						echo "<INPUT TYPE='button' NAME='ok' VALUE='GRABAR' onclick='validar_CantidadesVacias();'></td></tr>";
					}	
					else
					{
						echo "<INPUT TYPE='button' NAME='ok' VALUE='GRABAR' onclick='enter2();'></td></tr>";
					}
					
				}
				else
				{
					// echo "<INPUT TYPE='button' NAME='ok' VALUE='GRABAR' onclick='validar_cambio_estado();'></td></tr>";
					echo "<INPUT TYPE='button' NAME='ok' VALUE='GRABAR' onclick='validar_CantidadesVacias();'></td></tr>";
				}
				
			}
			else
			{
				// echo "<INPUT TYPE='button' NAME='ok' VALUE='GRABAR' disabled='disabled' onclick='validar_cambio_estado();'></td></tr>";
				echo "<INPUT TYPE='button' NAME='ok' VALUE='GRABAR' disabled='disabled' onclick='validar_CantidadesVacias();'></td></tr>";
			}
		
		}else{
			echo "<INPUT TYPE='button' NAME='ok' VALUE='GRABAR' onclick='enter2();'></td></tr>";
		}
		echo "</table></br></br>";
		echo "</form>";

	}


	/**
	 * [centroCostoUsuario Consultar el centro de costo del usuario que está en el programa, esto valída por ejemplo si el usuario es del centro de costo de auditoría,
	 * si es así entonces cambia el título del programa]
	 * @return [type] [description]
	 */
	function centroCostoUsuario($conex, $codigo_use)
	{
		$exp = explode("-", $codigo_use);
		$usuario_codigo = $exp[1];
		$cco = "";
		$q = "  SELECT  Ccostos AS cco_user, Empresa
				FROM    usuarios
				WHERE   Codigo = '{$usuario_codigo}'";
						// AND ACTIVO='A'";
		$result = mysql_query($q, $conex);
		if(mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_array($result);
			$cco = "({$row['Empresa']}){$row['cco_user']}";
		}
		return $cco;
	}

	function consultarUsuarioSatisfactorio($conex,$claseRequerimiento)
	{
		$query = "SELECT Clasuc 
					FROM root_000043 
				   WHERE Clacod='".$claseRequerimiento."';";
		
		$resultado = mysql_query($query,$conex);
		$num = mysql_num_rows($resultado);
		
		$usuarioCreaMarcaSatisfactorio = true;
		if($num>0)
		{
			$row = mysql_fetch_array($resultado);
			
			if($row['Clasuc']=="off")
			{
				$usuarioCreaMarcaSatisfactorio = false;
			}
		}
		
		return $usuarioCreaMarcaSatisfactorio;
	}
	/*=========================================================PROGRAMA==========================================================================*/
	//session_start();

	if (!isset($user))
	{
		if(!isset($_SESSION["user"]))
		session_register("user");
	}

	if(!isset($_SESSION["user"]))
	echo "error";
	else
	{

		$wacutaliza = "2019-11-12";
		$wbasedato='root';

		include_once("root/comun.php");

		$cco_auditoria_corporativa_clinica = consultarAliasPorAplicacion($conex, '01', 'centro_costo_auditoria_corporativa');
		$auditoria_corporativa_titulos     = consultarAliasPorAplicacion($conex, '01', 'auditoria_corporativa_titulos');
		$titulo_requerimientos             = "SISTEMA DE REQUERIMIENTOS";

		$id_req = (isset($id_req)) ? $id_req: '';
		global $id_req, $cco_auditoria_corporativa_clinica;

		$cco_user = centroCostoUsuario($conex, $user);

		if(isset($cco_user) && $cco_user == $cco_auditoria_corporativa_clinica)
		{
		  $titulo_requerimientos = $auditoria_corporativa_titulos;
		}

		//pintarVersion();
		pintarTitulo($wacutaliza, $titulo_requerimientos);

		//consulto los datos del usuario de la sesion
		$pos = strpos($user,"-");
		$wusuario = substr($user,$pos+1,strlen($user));

		$usucco=consultarCco($wusuario);
		$usuario=consultarUsuarioSeg($id, $req);
		pintarUsuario($usuario);

		$wusuario_creacaso = (!isset($wusuario_creacaso)) ? $usuario['cod']: $wusuario_creacaso;

		// Este parámetro solo llega desde consultas php y si llega con algún valor quiere decir que quien esta consultando el requerimiento tiene
		// pendiente ver o leer algunos mensajes nuevos de seguimiento.
		$ids_segs_pte = (isset($ids_segs_pte)) ? $ids_segs_pte : '';
		$msj_para_creador = (isset($msj_para_creador)) ? $msj_para_creador : '';
		$select_estreq = (isset($select_estreq)) ? $select_estreq : '';
		$wsatisfaccion = (isset($wsatisfaccion)) ? $wsatisfaccion : 'off';
		global $ids_segs_pte, $msj_para_creador, $select_estreq, $wsatisfaccion;

		// if ((!isset($enviar) and (!isset($grabar) or $grabar==0)) or (isset($enviar) and !isset($seg)) or (isset($enviar) and $seg=='')) //primera vez que se ingresa al programa o tiene el valor de ser con botones de ingreso de datos
		if ((!isset($enviar) and (!isset($grabar) or $grabar==0))) //primera vez que se ingresa al programa o tiene el valor de ser con botones de ingreso de datos
		{
			if (((isset($enviar) && !isset($seg)) || (isset($enviar) && $seg=='')) && isset($ok))
			{
				pintarAlert1('Para enviar un seguimiento debe ingresar alguna descripcion');
			}

			$wcodigo_caso = (!isset($wcodigo_caso)) ? '': $wcodigo_caso;
			if (!isset($ccoreq))
			{
				consultarRequerimiento($req, $cco, $tipreq, $clareq, $temreq, $resreq, $desreq, $fecap, $horap, $porcen, $fecen, $horen, $estreq, $prireq, $codigo, $obsreq, $fecreq, $recreq, $ccoreq, $acureq, $horreq, $id, $wcodigo_caso, $reqtir, $reqfal, $reqcan);
			}
			$tipos=consultarTipos($ccoreq, $wusuario, $tipreq);
			$clases=consultarClases($ccoreq, $tipos[0], $clareq);
			$receptores   =consultarReceptores($recreq, $ccoreq, $tipos[0], $clases[0]);
			$responsables =consultarResponsables($resreq, $ccoreq, $tipos[0], $clases[0]);
			$prioridades  =consultarPrioridades($prireq);
			$tipoRequerimiento = consultarTipoRequerimientos();	
			$tipoFalla         = consultarTipoFallas();
			$tipoCanal         = consultarCanales();

			$estados=consultarEstados($estreq);
			if (!isset($temreq))
			{
				$temreq='';
			}
			$tiempos=consultarTiempos($clases[0], $temreq);
			$porcentaje=consultarPorcentaje($clases[0]);
			$seguimientos=consultarSeguimientos($req, $cco, $usucco, $tipreq, $id, $wcodigo_caso);
			$especiales=consultarEspeciales($clases[0], $ccoreq, $tipos[0], $usucco, $req, $id);
			// $cerrado=consultarCerrado($estados[0]);
			$satisfaccion = '';
			$estado_cerrado = '';
			$cerrado=consultarCerradoSatisfaccion($estados[0], $wcodigo_caso, $estado_cerrado, $satisfaccion);
			pintarRequerimiento($usucco, $req, $cco, $tipos, $clases, $tiempos, $responsables, $desreq, $fecap, $horap, $porcen, $fecen, $horen, $estados, $prioridades, $codigo, $obsreq, $fecreq, $recreq, $ccoreq,  $porcentaje, $wusuario, $receptores, $seguimientos, $especiales, $horreq, $cerrado, $id, $wcodigo_caso, $wusuario_creacaso, $estado_cerrado, $satisfaccion,$resreq,$tipoRequerimiento,$tipoFalla,$tipoCanal,$reqtir, $reqfal, $reqcan, $prireq);
			if ($cerrado != 'on') //$usucco==$ccoreq and
			{
				global $usucco, $ccoreq;
				// pintarBoton($cco, $req, $ccoreq, $id);
				pintarBoton($cco, $req, $ccoreq, $id,$estado_cerrado);
			}

		}
		else
		{

			$segnum=0;
			$wcodigo_caso = (!isset($wcodigo_caso)) ? '': $wcodigo_caso;
			if ($grabar==1)
			{
				pintarAlert2('Se han grabado los cambios realizados');
			}

			if (isset($enviar))
			{
				$enviar='on';
			}
			else
			{
				$enviar='off';
			}

			if (!isset ($temreq))
			{
				$temreq='';
			}

			if (!isset ($porcen))
			{
				$porcen='';
			}
			else
			{
				$porcen=$porcen.'%';
			}

			$fecen=adecuarFecha($estreq, $fecen, $horen);
			actualizarRequerimiento($req, $ccoreq, $tipreq, $clareq, $temreq, $recreq, $resreq, $prireq, $porcen, $estreq, $fecen, $horen, $obsreq, $fecap, $horap, $id, $wusuario_creacaso, $wcodigo_caso, $reqtir, $reqfal, $reqcan);
			almacenarSeguimiento($wusuario, $ccoreq, $porcen, $req, $seg, $enviar, $segnum, $estreq, $tipreq, $id, $wusuario_creacaso, $wcodigo_caso, $prireq, $reqtir, $reqfal, $reqcan);
	
			if (isset($especiales))
			{
				$clase=explode('-',$clareq);
				// var_dump($clareq);
				$requerimientosEspeciales = consultarAliasPorAplicacion($conex, '01', 'ClasesRequerimientosEspeciales');
		
				$requerimientoEspecial=explode(",",$requerimientosEspeciales);
				
				$EsRequerimientoEspecial=0;
				for($p=0;$p<count($requerimientoEspecial);$p++)
				{
					if($requerimientoEspecial[$p]==$clase[0])
					{
						$EsRequerimientoEspecial=1;
						break;
					}
				}
				
				if($EsRequerimientoEspecial==1)
				{
					if($wusuario == $wusuario_creacaso)
					{
						$qRecept= "SELECT Perusu 
									FROM ".$wbasedato."_000042 
									WHERE Perusu='".$wusuario."' 
									AND Percco=mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1)
									AND Perrec= 'on';";
						
						$recpt= mysql_query($qRecept,$conex);
						$receptores = mysql_fetch_array($recpt);
						$recepto = mysql_num_rows($recpt);
						
						if($receptores[0]==$wusuario)
						{
							// echo "hola 1";
							actualizarEspeciales($ccoreq, $tipreq, $clareq, $especiales2, $req, $wusuario, $id, $especiales3,$estreq);
						}
						else
						{
							if(count($especiales3)==0)
							{
								$especiales3=array();
							}
							
							// echo "hola 2";
							actualizarEspeciales($ccoreq, $tipreq, $clareq, $especiales, $req, $wusuario, $id, $especiales3,$estreq);
						}
						
					}
					else
					{
							if(count($especiales3)==0)
							{
								$especiales3=array();
							}
							
							// echo "hola 3";
						actualizarEspeciales($ccoreq, $tipreq, $clareq, $especiales2, $req, $wusuario, $id, $especiales3,$estreq);
					}
					
				}
				else
				{
					if(count($especiales3)==0)
					{
						$especiales3=array();
					}
					
					// echo "hola 4";
					actualizarEspeciales($ccoreq, $tipreq, $clareq, $especiales, $req, $wusuario, $id, $especiales3,$estreq);
				}
			}
			$tipos        = consultarTipos($ccoreq, $wusuario, $tipreq);
			$clases       = consultarClases($ccoreq, $tipos[0], $clareq);
			$receptores   = consultarReceptores($recreq, $ccoreq, $tipos[0], $clases[0]);
			$responsables = consultarResponsables($resreq, $ccoreq, $tipos[0], $clases[0]);
			$prioridades  = consultarPrioridades($prireq);
			$tipoRequerimiento = consultarTipoRequerimientos();	
			$tipoFalla         = consultarTipoFallas();
			$tipoCanal         = consultarCanales();

			$estados      = consultarEstados($estreq);
			$tiempos      = consultarTiempos($clases[0], $temreq);
			$porcentaje   = consultarPorcentaje($clases[0]);
			$seguimientos = consultarSeguimientos($req, $cco, $usucco, $tipreq, $id, $wcodigo_caso);
			$especiales   = consultarEspeciales($clases[0], $ccoreq, $tipos[0], $usucco, $req, $id);

			$satisfaccion = '';
			$estado_cerrado = '';
			$cerrado=consultarCerradoSatisfaccion($estados[0], $wcodigo_caso, $estado_cerrado, $satisfaccion);
			pintarRequerimiento($usucco, $req, $cco, $tipos, $clases, $tiempos, $responsables, $desreq, $fecap, $horap, $porcen, $fecen, $horen, $estados, $prioridades, $codigo, $obsreq, $fecreq, $recreq, $ccoreq, $porcentaje, $wusuario, $receptores, $seguimientos, $especiales, $horreq, $cerrado, $id, $wcodigo_caso, $wusuario_creacaso, $estado_cerrado, $satisfaccion,$resreq,$tipoRequerimiento,$tipoFalla,$tipoCanal,$reqtir,$reqfal,$reqcan,$prireq);
			if ($cerrado != 'on') 
			{
				global $usucco, $ccoreq;
				pintarBoton($cco, $req, $ccoreq, $id,$estado_cerrado);				
	
			}
			echo '<script language="Javascript">';
			echo 'window.opener.informatica.submit();';
			echo '</script>';
		}
	}
	/*===========================================================================================================================================*/
	?>
	<br>
	<br>
	<table align='center'>
		<tr><td align='center' colspan=9><input type='button' value='Cerrar Ventana' onclick='window.close();'></td></tr>
	</table>
	</body >
	</html >