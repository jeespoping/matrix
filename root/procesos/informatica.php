<?php
include_once("conex.php");
session_start();
include_once("../../webservices/procesos/ws_cliente_mantenimiento.php");
?>
<html>
<head>
  <title>REQUERIMIENTOS</title>

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
		.AlinearInputs
		{
			float:right;
		}
   </style>
   
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
   <script type="text/javascript">
   	$(document).ready(function(){

		var ccoreq = document.informatica.ccoreq.value;
		var no_cco_msj = "Debe seleccionar centro de costos para solicitar pedido";
		if(ccoreq == '(01)1082-CENTRAL DE MATERIALES Y ESTERILIZACION'){
			$("#cco").append($("<option value=''>SELECCIONE CENTRO DE COSTOS</option>"));
			$("#cco").val('');
			document.getElementById("nocco").innerHTML = "Debe seleccionar centro de costos";
		}
		else{
			$("#cco").change();					
		}
		
		var cco = document.informatica.cco.value;
		
		if(cco == ''){			
			$('#ok').prop('disabled',true);
			document.getElementById("nocco").innerHTML = no_cco_msj;
		}else{
			$('#ok').prop('disabled',false);
			document.getElementById("nocco").innerHTML = '';
		}

		$('#cco').change(function(){
			cco = $(this).val();
			if($(this).val() == ''){
				$('#ok').prop('disabled',true);
				document.getElementById("nocco").innerHTML = no_cco_msj;
			}else{

				$('#ok').prop('disabled',false);
				document.getElementById("nocco").innerHTML = '';
			}	
			
		});

		
		$('#ccoreq').change(function(){
			ccoreq = $(this).val();
			if(ccoreq == '(01)1082-CENTRAL DE MATERIALES Y ESTERILIZACION'){				
				$("#cco").append($("<option value=''>SELECCIONE CENTRO DE COSTOS</option>"));
				$("#cco").val('');
				document.getElementById("nocco").innerHTML = no_cco_msj;
			}
			else{
				$("#cco").change();
				document.getElementById("nocco").innerHTML ='';
				
			}
		});
		$('#informatica').on('submit', function() {
			
			if($(".AlinearInputs").length > 0){		

				if ($(".fondoamarillo") && $(".fondoamarillo").length > 0 ) {
					console.log('existe');
					return true;
					
					
				}else{
					alert('!Por favor ingrese alguna cantidad¡');
					return false;
				}	
			}
			return true;
		});
		
   });


   function cambiarFondoInput(elemento)
   {
	   if($(elemento).val()!="")
	   {
		   $(elemento).removeClass('AlinearInputs');
		   $(elemento).addClass('fondoamarillo');
	   }
	   else
	   {
			$(elemento).addClass('AlinearInputs');
		   $(elemento).removeClass('fondoamarillo');
	   }
   }
   function datosUsuario()
   {
	   
	   document.informatica.submit();
   }
   function justNumbers(e)
	{
	var keynum = window.event ? window.event.keyCode : e.which;
	if ((keynum == 8) || (keynum == 0))
	return true;
	 
	return /\d/.test(String.fromCharCode(keynum));
	}
   
   function enter()
   {
   	document.informatica.clareq.options[document.informatica.clareq.selectedIndex].text='';
   	document.informatica.submit();
   }

   function enter1()
   {
   	document.informatica.submit();
   }

   function enter2()
   {
	
   	document.informatica.tipreq.options[document.informatica.tipreq.selectedIndex].text='';
   	document.informatica.clareq.options[document.informatica.clareq.selectedIndex].text='';
   	document.informatica.submit();
   }

    function enter3()
   {
   	document.informatica.tipreq.options[document.informatica.tipreq.selectedIndex].text='';
   	document.informatica.submit();
   }
   function validarMsj()
   {
        // >>>>>>>>> Estas validaciones se hacen para que solo empleados de auditoría puedan hacer requerimientos a auditoría, cualquier otro usuario no lo podrá hacer.
        var cco = document.informatica.cco.value;
        var cco_auditoria_corp = document.informatica.cco_auditoria_corp.value;
   		var ccoreq = document.informatica.ccoreq.value;

        var expld_cco_req = ccoreq.split("-",1); // hacer explode de ccoreq para poder comparlo
        var expld_cco_usu = cco.split("-",1); // hacer explode de ccoreq para poder comparlo

        if(expld_cco_req == cco_auditoria_corp && expld_cco_usu != cco_auditoria_corp)
        {
            alert("Usted no puede crear requerimientos para Auditoria - Centro costo: "+cco_auditoria_corp+"");
            return false;
        }

   		return true;
   }
    </script>

</head>

<body >

<?php

/*======================================================DOCUMENTACION APLICACION==========================================================================

APLICACION PARA GESTION DE REQUERIMIENTOS DE USUARIO EN MATRIX

1. DESCRIPCION:

Actualmente los requerimientos de usuario entre centros de costos, se hace por diferenes formas: Papel, oral, correo.
La idea es proporcionar un medio unico de gestión de requerimientos, donde cada centro de costos pueda ofrecer los diferentes
tipos de requiermientos que atiende y de esta forma cualquier usuario pueda solicitar requerimientos de un tipo determinado
a un determinado centro de costos. Adicinalmente se busca que la aplicacion permita cierta flexibilidad o formas de configurar
la gestion de dichos requerimientos, por ejemplo poder establecer la persona que recibe cierto tipo de requerimiento.
Adicionalmente el sistema permitira al que recibe o es responsable del requerimiento consultar la lista de requermientos
ordenados por diferentes parametros (estado principalmente) y hacer un seguimiento de los requerimiento.
Igualmente al usuario le debera permitir consultar el estado y el seguimeinto de los requermientos enviados

2. OBJETIVO

Sistematizar la gestión de requermientos para cualquier centro de costos inscrito.


3. CARACTERISTICAS O HISTORIAS

1. Registrar una solicitud de usuario
2. Consultar una solicitud de usuario
3. Dar seguimiento a una solicitud de usuario
4. Consultar el seguimeinto se una solicitud de usuario
5. Organizar requerimientos consultados por diferentes parametros
6. Consultar requerimientos cerrados de meses anteriores
7. Llenado agil  de maestros

PRIMERA VERSION:
ITERACION 1:

1. Registrar una solicitud de usuario

Al usuario ingresar en su portal de matrix y acceder al programa de requerimientos, tendrá la opcion de solicitar un
requerimiento al centro de costos que desea.

Incialmente el formulario debe presentar un formulario donde el centro de costos, nombre del usuario, cargo, extension y email
están previamente diligenciados con los datos del usuario

Si el usuario tiene autorización de ingresar otros usuarios, el formulario le presentara la opcion de buscar un usuario por
nombre o por codigo. Una vez ingresado el codigo del usuario, se despliega el centro de costos al que pertenece y demás datos
propios del usuario si ya esta registrado

Posteriormente se consulta el perfil del usuario, si el usuario esta registrado como alguien que responde a un requerimiento
determinado de un centro de costos, el formulario para ingreso de requerimiento se cargará automaticamente con su centro de
costos, tipo de requerimiento al cual responde y las caracterisiticas especiales del tipo de requerimiento.
Si el usuario no obedece a ningun perfil, se cargarán los datos en blanco para que se llene la solicitud en el siguiente orden:
centro de costos a la que se solicita el servicio, luego se carga el tipo de servicio. Y posteriormente se llena una descripcion.

Los usuarios comunes al igual que los que posean algun perfil tendran la posibilidad de llenar solo esos tres datos para
ingresar un requerimiento, pero para aquellos que posean algun perfil, el formulario tambien les dará la posibilidad de
llenar los siguientes tipo de datos:

La clase de requerimiento: una subclasificacion adicional al tipo de requerimiento, por ejemplo si el tipo de requerimiento
ofrecido por el centro de costos de informatica es Matrix, clases serían Correción, Capacitacion, etc


Campos adicionales: campos como la persona asignada, la prioridad, fecha aproximada de atencion, observacion de
entrega, fecha de entrega, tiempo de desarrollo, porcentaje de cumpliemiento.
Los dos ultimos pueden preguntarse o no dependiendo de la clase del
requerimiento.

Campos especiales: Existiran requerimientos de centros de costos de un tipo y clase especifica que requeriran otros campos
Para estos existe la opcion de agregar unos campos especificos configurables en la base de datos.

Una vez ingresados al menos los tres datos obligatorios, el sistema almacenrá el requerimiento, poniendo por defecto su
estado en ingresado si otro estado no ha sido seleccionado por otro usuario con perfil. El numero del requerimiento obedecera
a un consecutivo por centro de costos.

Para el requerimiento existiran dos roles, el receptor y el responsable. Cuando el usuario comun ingresa un requerimiento
el sistema por defecto asignara como responsable y receptor aquel que tiene este perfil para el tipo de requerimiento.
En el perfil se indica si el receptor sera visible o no por el usuario, en caso de que no sea visible deberá existir un unico
perfil de receptor para el tipo de requerimiento del centro de costos. Un usuario con perfil prodrá seleccionar el receptor
y el respnsable.


2. Consultar un requerimiento de usuario
3. Dar seguimiento a una requerimiento de usuario
3. Consultar un requerimiento de usuario


4. CARACTERISITCAS DE SEGURIDAD

Se tiene dos tablas para el manejo de perfiles de usuario:

La tabla 000001 en la cual se registran los usuario por codigo y centro de costos

La tabla 000003 que contiene los perfiles de usuario

Si el usuario(codigo) de Matrix no se encuentra en la tabla de usuarios, unicamente podra ingresar los campos de centro de
costos, tipo de requerimiento y descripcion al solicitar un requerimiento. Adicionalmente podra unicamente consultar
el seguimiento de los requerimientos que ha solicitado.

Si el usuario aparece en la tabla de perfiles 000003 con el campo de responsable en on para un tipo de requerimiento,
podra para este, ingresar todos los datos adicionales y especiales si lo hay. Adicionalmente podrán ser seleccionados
como responsables de un requerimientos por un usuario con perfil que pertenezca al mismo centro de costos

Los usuarios receptores visibles, son aquellos que al desplegar los tipos de requirimiento para el centro de costos
aparecen en el formulario. A los receptores no visibles el programa les asignara el requerimiento cuando este sea ingresado


5. AREA DE DESCRIPCION ESTRUCTURAL

000001 Maestro de usuarios
000002 Requerimientos (toda la información incial del requerimiento)
000003 Maestro de tipos de requerimientos para los centros de costos
000004 Maestro de perfiles de usuario, indica para que tipos de requiermiento el usuario es receptor, visible o responsable
000005 Maestro de clases de requerimientos, lista las clases de requerimientos y si piden o no timepo de dllo y portetaje de cumplimiento
000006 Maestro de realcion de clases y tipos de requerimiento, indica las clases que puede tener un tipo de requerimiento de un centro de costos
indica tambien si esa combinacion tiene campos espciales y el numero de la tabla en la que se guardan
000007 Seguimiento de requerimientos
000008 Maestro de campos especiales, lista de campos especiales si son de sleccion o texto y de donde sacan la seleccion si lo son
000009 Tabla para campos especiales de Matrix
000010 Maestro de realcion tipo, clase y campos especiales, indica cuales campos utilizar la combinacion clase, tipo y centro y la posicion
ocupan en la tabla donde se almacenaran
000011 Maestro de estados, lista de estados de un requiermiento, el color asociado y si es de incio o de finalizacion

6. AREA DE DESCRIPCION DINAMICA

Ingreso de requerimiento
•	000001 (inserta si no existe o actualiza usuario si existe)--> 1
•	000002  --> 1
========================================================DOCUMENTACION PROGRAMA================================================================================*/
/*

1. AREA DE VERSIONAMIENTO

Nombre del programa:informatica.php
Fecha de creacion: 2007-04-30
Autor: Carolina Castano P

Ultima actualizacion:
2020-08-06:
    Arleyda Insignares: se adicion campo sede para el formulario y la tabla root_000039

2019-10-17:
	Andres Alvarez: se realiza funciona javascript para que obligue a seleccionar el centro de costo para el area de central de esterilizacion.

2019-10-25 :
	Andres Alvarez: se realiza validacion para obligar un centro de costos  y se desactiva el botón enviar el formulario sin un centro de costos asociado.

2019-10-25:
	Andres Alvarez: se pinta mensaje en color rojo para que se seleccione un centro de costos.

2019-10-25:
	Andres Alvarez: se realiza validación para controlar el envio del formulario sin cantidades, si la solicito se hace  a la central de esterilización.

2019-10-25: 
 	Andres Alvarez: se organiza el formulario para que concerve las cantidades ingresas, al momento de que se presente un error en la validación del formulario.

2019-09-30:
	Jessica Madrid M:	* Para los requerimientos especiales se agrega fondo amarillo si digitó algún dato en el input.
						* Se agrega restricción de horario por clase de requerimiento, dicha restricción se configura en root_000043.
2015-10-13:
    Jessica Madrid:     * Se modifica la funcion almacenarUsuario() para que solo valide si el usuaio existe en la tabla root_000039 y evitar crear varios registros por usuario.
						* Se agrega al array de la funcion consultarCentros() la lista de los centros de costos de movhos_000011 de tipo hospitalario, urgencias, cirugia y ayuda.
						* See modifica la funcion almacenarRequerimiento() para registrar el nuevo campo Reqcss en la tabla root_000040
2015-08-18:
    Jessica Madrid:     * Se modifican las funciones almacenarEspeciales() y almacenarEspeciales2(), se reemplaza Rctest por Rctesp en el where del query para obtener
							la tabla en la que se guardarán los campos especiales si esta Rctesp='on' ya que estaba mirando el estado (Rctest='on').
						* Se agrega a la función pintarRequerimiento() una validación para los tipos de requirimientos con campos especiales que esten guardados en 
							root_000051, para los cuales se debe alinear el texto a la izquierda y los input text a la derecha.
	
2014-08-20:
    Edwar Jaramillo:    * Se realiza validación cuando se intenta insertar en la tabla root_000039 un usuario y centro de costo que posiblemente ya exista
                            y evitar el error de llave duplicada, se cambia el mensaje de error, esto sucede porque en la tabla 000039 ya tiene un registro
                            con código de usuario y centro de costos pero el registro está inactivo y en la tabla costosyp el centro de costos del usuario
                            tiene concatenada la palabra -NO USAR- y eso hace parecer al programa que es un centro de costos nuevo e intenta agregarlo, lo ideal en
                            este caso es intentar cambiar el usuario al centro de costos adecuado.

2014-06-25:
    Edwar Jaramillo:    * Al momento de guardar requerimientos se valída si se está intentando enviar un requerimiento al centro de costo auditoría,
                            si es así entonces verifica si el usuario que está creando el requerimiento pertenece al centro de costo de auditoría,
                            todos los demás usuarios que no pertenecen a ese centro de costo no podrán hacer no pueden enviar sus solicitudes allí.

2013-11-05:
    Edwar Jaramillo:    * Se desarrolla funcionalidad para seleccionar varios responsables del requerimiento si la clase de requerimiento esta configurada
                            para pedir más de un responsable (parámetro en on en la tabla root_000043, nuevo campo Clanre).

2013-10-11:
    Edwar Jaramillo:    * Se corrige validación incómoda que se hacía cada que se cambia el centro de costo hacia donde va el requerimiento.
                        * Se modifican campos en el web service que se comunica con el software de mantenimiento para que en solicitante envíe el nombre
                            de la persona y no su código, también que se envíe el código del centro de costo de la persona que genera el requerimiento para
                            mantenimiento.

2013-07-19:
    Edwar Jaramillo:    * Se incluye librería para usar webservices en algunos requerimientos como los de manternimiento que necesitan interactuar con otro sistema
                        * Se modifican los estilos de todo el formulario actualizandolos a las hoja de estilos de los demás programas.
                        * Se cambía el nombre de la función consultarUsuario por consultarUsuarioReq para que no entre en conflicto con ese mismo nombre
                          de función que existe en el comun.php librería que se debió agregar para que se pusieran aplicar los estilos.
                        * Modificaciones en general para guardar requerimientos de mantenimiento tanto en matrix como en el sistema AM de mantenimiento por medio de webservices.


2008-01-04 Carolina Castano Se agregan campos especailes para llenar por el usuario
2008-01-04 Carolina Castano Se agregan hora aproximada de atencion y hora de entrega
2007-06-20 Carolina Castano Se corrige bug en busqueda por nombres, no mostraba cuando encontraba solo uno
2007-06-20 Carolina Castano se despliega tambien el nombre de la empresa para escoger el centro de costos al que se dirige el requerimiento
2007-06-20 Carolina Castano se anade este pedazo para cuando el responsable sea visible, se actualice tambien dropdown de asignado a



2. AREA DE DESCRIPCION:

Este script realiza la historia: Ingresar requerimeinto de usuario de aplicacion informatica.  Para ello necesita se le
pase como parametro la base de datos que utilizara unicamente. Este Script permite al usuario tener acceso a otros
Scrips para consultar los requerimientos (consulta.php) y para dar seguimiento a un requerimiento (seguimiento.php)

3. AREA DE VARIABLES DE TRABAJO

4. AREA DE TABLAS

*/

/*========================================================FUNCIONES==========================================================================*/

//----------------------------------------------------------funciones de persitencia------------------------------------------------

/**
 * Funcion a la cual se le envia el codigo matrix y/o el centro de costos del usuario
 * y llena un vector llamado usuario con los siguientes detalles:
 *
 * 		['cod']=codigo del usuario
		['cco']=primer centro de costos encontrado para el usuario (podrian haber mas, luego se muestran)
		['ext']=extension del usuario
		['ema']=email
		['car']=cargo
		['nom']=nombre
		['sup']=indica si el usuario puede ingresar requerimientos de otro usuario o no
 *
 * Si el centro de costos esta vacio, busca los datos para cualquier centro de costos
 *
 * recibe: $codigo=codigo en matrix del usuario, tipo caracter
 * 		   $cco=centro de costos del usuario, tipo caracter
 * retorna: usuario, tipo array si encuentra al usuario en tabla de 00001, si no retorna false
 */
function consultarUsuarioReq($codigo, $cco)
{
	global $conex;
	global $wbasedato;

	//Si el centro de costos esta vacio, busca los datos para cualquier centro de costos
	//Si tiene un valor busco los datos del usuario para ese centro de costos
	if ($cco=='')
	{
		$q= " SELECT Empresa, Ccostos, Usuext, Usuema, Usucar, Ususup, Usused, Descripcion "
		."       FROM ".$wbasedato."_000039, usuarios "
		."    WHERE Usucod = '".$codigo."' "
		."       AND Usuest = 'on' "
		."       AND Codigo = usucod "
		."       AND Activo = 'A' ";
	}
	else
	{
		$q= " SELECT Empresa, Ccostos, Usuext, Usuema, Usucar, Ususup, Usused, Descripcion "
		."       FROM ".$wbasedato."_000039, usuarios "
		."    WHERE Usucod like '".$codigo."' "
		."       AND Usuest = 'on' "
		."       AND Codigo = usucod "
		."       AND Activo = 'A' ";
	}

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);

	//si lo encuentra cargo el vector de usuario
	//si no lo encuentra la función devuelve falso
	if ($num>0)
	{

		if ($row['Empresa']!='' and $row['Ccostos']!='')
		{
			$q= " SELECT Emptcc "
			."       FROM ".$wbasedato."_000050 "
			."    WHERE Empcod = '".$row[0]."' "
			."       AND Empest = 'on' ";

			$res = mysql_query($q,$conex);
			$row1 = mysql_fetch_array($res);

			if ($row1[0]=='costosyp_000005')
			{
				$q= " SELECT Cconom "
				."       FROM ".$row1[0]
				."    WHERE Ccocod = '".$row[1]."' ";
			}
			else
			{
				$q= " SELECT Ccodes "
				."       FROM ".$row1[0]
				."    WHERE Ccocod = '".$row[1]."' ";
			}

			$res = mysql_query($q,$conex);
			$num = mysql_num_rows($res);
			$row2 = mysql_fetch_array($res);
			$usuario['cco']='('.$row['Empresa'].')'.$row['Ccostos'].'-'.$row2[0];
		}
		else
		{
			$usuario['cco']='';
		}

		$usuario['cod']=$codigo;
		$usuario['ext']=$row['Usuext'];
		$usuario['ema']=$row['Usuema'];
		$usuario['car']=$row['Usucar'];
		$usuario['sed']=$row['Usused'];
		$usuario['nom']=$row['Descripcion'];
		$usuario['sup']=$row['Ususup'];

	}
	else
	{
		//se busca como segunda opcion en la tabla de usuarios de Matrix, dejando los detalles del usuario vacios

		$q= " SELECT Descripcion, Empresa, Ccostos "
		."       FROM usuarios "
		."    WHERE Codigo = '".$codigo."' "
		."       AND Activo = 'A' "
		."       ORDER BY 1 ASC ";

		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);

		if ($num>0)
		{

			$row = mysql_fetch_array($res);

			if ($row['Empresa']!='' and $row['Ccostos']!='')
			{
				$q= " SELECT Emptcc "
				."       FROM ".$wbasedato."_000050 "
				."    WHERE Empcod = '".$row[1]."' "
				."       AND Empest = 'on' ";

				$res = mysql_query($q,$conex);
				$num = mysql_num_rows($res);
				$row1 = mysql_fetch_array($res);

				if ($row1[0]=='costosyp_000005')
				{
					$q= " SELECT Cconom "
					."       FROM ".$row1[0]
					."    WHERE Ccocod = '".$row[2]."' ";
				}
				elseif($row1[0]=='NO APLICA')
				{
					$usuario['cco']='';
				}
				else
				{
					$q= " SELECT Ccodes "
					."       FROM ".$row1[0]
					."    WHERE Ccocod = '".$row[2]."' ";
				}

				$res = mysql_query($q,$conex);
				$num = mysql_num_rows($res);
				$row2 = mysql_fetch_array($res);
				$usuario['cco']='('.$row['Empresa'].')'.$row['Ccostos'].'-'.$row2[0];
			}
			else
			{
				$usuario['cco']='';
			}

			$usuario['cod']=$codigo;
			$usuario['ext']='';
			$usuario['ema']='';
			$usuario['car']='';
			$usuario['sed']='';
			$usuario['nom']=$row['Descripcion'];
			$usuario['sup']='off';

			$personas[1]=$row[0].'-'.$row[1];

			for ($i=2;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res);
				$personas[$i]=$row[0].'-'.$row[1];
			}
		}
		else
		{
			$usuario=false;
		}
	}
	return $usuario;
}



/**
 * Funcion a la cual se le envia el codigo matrix y/o el centro de costos del usuario
 * y llena un vector llamado usuario con los siguientes detalles:
 *
 * 		['cod']=codigo del usuario
		['cco']=primer centro de costos encontrado para el usuario (podrian haber mas, luego se muestran)
		['ext']=extension del usuario
		['ema']=email
		['car']=cargo
		['nom']=nombre
		['sup']=indica si el usuario puede ingresar requerimientos de otro usuario o no
 *
 * Si el centro de costos esta vacio, busca los datos para cualquier centro de costos
 *
 * recibe: $codigo=codigo en matrix del usuario, tipo caracter
 * 		   $cco=centro de costos del usuario, tipo caracter
 * retorna: usuario, tipo array si encuentra al usuario en tabla de 00001, si no retorna false
 */
function consultarUsuarioXNombre($nombre, &$personas)
{
	global $conex;
	global $wbasedato;


	$q= " SELECT Codigo, Descripcion, Empresa, Ccostos "
	."       FROM usuarios "
	."    WHERE Descripcion like '%".$nombre."%' "
	."       AND Activo = 'A' "
	."       ORDER BY 2 ASC ";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			//if ($i==1)
			//{
				$usuario['cod']=$row['Codigo'];
				$usuario['nom']=$row['Descripcion'];

				if ($row['Empresa']!='' and $row['Ccostos']!='')
				{
					$q= " SELECT Emptcc "
					."       FROM ".$wbasedato."_000050 "
					."    WHERE Empcod = '".$row[2]."' "
					."       AND Empest = 'on' ";

					$res5 = mysql_query($q,$conex);
					$num5 = mysql_num_rows($res5);
					$row1 = mysql_fetch_array($res5);

					if ($row1[0]=='costosyp_000005')
					{
						$q= " SELECT Cconom "
						."       FROM ".$row1[0]
						."    WHERE Ccocod = '".$row[3]."' ";
					}
					else
					{
						$q= " SELECT Ccodes "
						."       FROM ".$row1[0]
						."    WHERE Ccocod = '".$row[3]."' ";
					}

					$resn = mysql_query($q,$conex);
					$row2 = mysql_fetch_array($resn);
					$usuario['cco']='('.$row['Empresa'].')'.$row['Ccostos'].'-'.$row2[0];
				}
				else
				{
					$usuario['cco']='';
				}

				$q= " SELECT Usuext, Usuema, Usucar, Ususup, Usused, Usucod, Descripcion "
				."       FROM ".$wbasedato."_000039, usuarios "
				."    WHERE Usucod = '%".$usuario['cod']."%' "
				."       AND Usuest = 'on' "
				."       AND Codigo = usucod "
				."       AND Activo = 'A' ";

				$res1 = mysql_query($q,$conex);
				$num1 = mysql_num_rows($res1);
				if ($num1>0)
				{
					$row1 = mysql_fetch_array($res1);

					$usuario['ext']=$row1['Usuext'];
					$usuario['ema']=$row1['Usuema'];
					$usuario['car']=$row1['Usucar'];
					$usuario['sed']=$row1['Usused'];
					$usuario['sup']=$row1['Ususup'];
				}
				else
				{
					$usuario['ext']='';
					$usuario['ema']='';
					$usuario['car']='';
					$usuario['sed']='';
					$usuario['sup']='';
				}
			//}
			//2007-06-20 se corrige bug, cuando se busca por nombre no mostraba cuando encontraba uno solo
			$personas[$i-1]=$row[0].'-'.$row[1];
		}

		return $usuario;
	}
	else
	{
		return false;
	}
}


/**
 * Funcion a la cual se le envia el codigo matrix y un centro de costos inicial del usuario
 * y con eso llena un vector con los centros de costos (codigo-nombre) a los cuales el usuario esta inscrito
 *
 * recibe: $usuario=codigo en matrix del usuario, tipo caracter
 * 		   $cco=centro de costos inicial para el usuario
 * retorna: $centros, tipo array con lista de centros para el usuario (codigo-nombre, si no encuentra centros retorna false
 */
function consultarCentros($usuario, $cco)
{
	global $conex;
	global $wbasedato;
	
	
	if ($cco!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$centros[0]=$cco;
		$cadena="Usucco != '".$cco."' AND";
	}
	else
	{
		$centros[0]='';
		$cadena='';
	}

	if( is_array( $usuario ) )
	{
		$q= " SELECT Usucco "
		."       FROM ".$wbasedato."_000039 "
		."    WHERE ".$cadena
		."           Usucod = '".$usuario['cod']."' "
		."       AND Usuest = 'on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$centros[$i]=$row1[0];
			}
		}
		else if ($centros[0]=='')
		{
			$centros[0]= '(0)00-SIN CENTRO DE COSTOS';
		}
	}
	
	$query = "SELECT Ccocod,Cconom 
				FROM movhos_000011 
			   WHERE (Ccohos = 'on' 
			      OR  Ccourg = 'on' 
				  OR  Ccocir = 'on' 
				  OR  Ccoayu = 'on') 
				 AND Ccoest='on';";
				 
	$resultado = mysql_query($query,$conex);
	$cantCcos = mysql_num_rows($resultado);
	
	$cantCcosto=count($centros);
	while ($row = mysql_fetch_array($resultado)) 
	{
		
		$centros[$cantCcosto]="(01)".$row[0]."-".$row[1];
		$cantCcosto++;
	}

	return $centros;
}


function consultarSedes()
{
	global $conex;
	global $wbasedato;
	
	$sedes = array();
		
	$query = "SELECT Sedcod,Sednom 
				FROM root_000128
			   WHERE Sedest = 'on'
			   Order by Sedcod";
				 
	$resultado = mysql_query($query,$conex);
	$cantSed = mysql_num_rows($resultado);
	
	if ($cantSed>0)
	{
		while ($row = mysql_fetch_array($resultado)) 
		{
			$sedes[]=$row[0]."-".$row[1];
		}
    }

	return $sedes;
}


function consultarPerfil($codigo, $cco)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT count(*) "
	."         FROM ".$wbasedato."_000042 "
	."      WHERE Percco= mid('".$cco."',1,instr('".$cco."','-')-1) "
	."         AND Perest='on' "
	."         AND Perusu='".$codigo."' ";

	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0]>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function consultarClases($cco, $tipo, $clase)
{
	global $conex;
	global $wbasedato, $req_varios_resp;
	
	if ($clase!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$clases[0]=$clase;
		$cadena="Rctcla != mid('".$clase."',1,instr('".$clase."','-')-1) AND";
		$inicio=1;
	}
	else
	{
		$clases[0]='';
		$cadena='';
		$inicio=0;
	}

    $req_varios_resp = ''; //Requiere varios responsables
    if($clases[0] != '')
    {
        $expl = explode('-', $clase);
        $req_varios_resp = $expl[0];
    }

	//consulto los conceptos
	$q =  " SELECT Rctcla, Rctesp, Clades "
	."        FROM ".$wbasedato."_000044, ".$wbasedato."_000043 "
	."      WHERE ".$cadena." "
	."            rctcco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND rcttip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND rctest = 'on' "
	."        AND rctcla = clacod "
	."        AND claest = 'on' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$clases[$inicio]=$row1['Rctcla'].'-'.$row1['Clades'].'-'.$row1['Rctesp'];
			$inicio++;

            if($req_varios_resp == '')
            { $req_varios_resp = $row1['Rctcla'];}
		}

	}
	else
	{
		$clases= false;
	}

    // Consultar si para la clase de requerimiento actual se debe listar más de un responsable
    if($req_varios_resp != '')
    {
        $sqlR = "   SELECT  Clanre AS requiere_varios_reponsables
                    FROM    {$wbasedato}_000043
                    WHERE   Clacod = '{$req_varios_resp}'
                            AND Claest = 'on'";
        $result = mysql_query($sqlR,$conex) OR die ("Error en: <br>".$sqlR."<br>".mysql_error());
        if(mysql_num_rows($result) > 0)
        {
            $row = mysql_fetch_array($result);
            $req_varios_resp = $row['requiere_varios_reponsables'];
        }
    }

	return $clases;
}


function consultarClases2($cco, $tipo, $clase)
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
		$clases[0]='';
		$cadena='';
		$inicio=0;
	}

	//consulto los conceptos
	$q =  " SELECT Rctcla, Rctesp, Clades, Rcttip, Rctcco "
	."        FROM ".$wbasedato."_000044, ".$wbasedato."_000043, ".$wbasedato."_000048 "
	."      WHERE ".$cadena." "
	."            rctcco= mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND rcttip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND rctest = 'on' "
	."        AND rctcla = clacod "
	."        AND claest = 'on' "
	."        AND rlccco= rctcco "
	."        AND rlctip = rcttip "
	."        AND rlcest = 'on' "
	."        AND rlccla = clacod "
	."        AND rlcvis = 'on' "
	."        GROUP BY  1, 4, 5";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$clases[$inicio]=$row1['Rctcla'].'-'.$row1['Clades'].'-'.$row1['Rctesp'];
			$inicio++;
		}

	}
	else
	{
		$clases= '';
	}

	return $clases;
}

function consultarEspeciales2($clase, $cco, $tipo)
{
	global $conex;
	global $wbasedato;

	$ClaseRequerimiento=explode("-",$clase);
	
	$requerimientosEspeciales = consultarAliasPorAplicacion($conex, '01', 'ClasesRequerimientosEspeciales');
	
	$requerimientoEspecial=explode(",",$requerimientosEspeciales);
	
	$EsRequerimientoEspecial=0;
	for($p=0;$p<count($requerimientoEspecial);$p++)
	{
		if($requerimientoEspecial[$p]==$ClaseRequerimiento[0])
		{
			$EsRequerimientoEspecial=1;
			break;
		}
	}
	
	if($EsRequerimientoEspecial==1)
	{
		$query="  SELECT Cladat 
					FROM root_000043 
				   WHERE Clacod=mid('".$clase."',1,instr('".$clase."','-')-1);";
		$respuesta = mysql_query($query,$conex);
		
		$row = mysql_fetch_array($respuesta);
		
		if($row[0]!="")
		{
			$qExiste="SELECT COUNT(id) 
						FROM ".$row[0].";";
			$respuestaExiste = mysql_query($qExiste,$conex);
			
			$rowExiste = mysql_fetch_array($respuestaExiste);
		}
		
					
		if($row[0]!="" && $rowExiste[0]>=0)
		{
			$q="  SELECT Procod,Prodes
						FROM ".$row[0]." 
					   WHERE Procla=mid('".$clase."',1,instr('".$clase."','-')-1)
						 AND Proest='on' 
					ORDER BY Prodes;";
			
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			if ($num1>0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$especiales[$i]['nombre']=$row1['Procod'].'-'.$row1['Prodes'];
					$especiales[$i]['sel']=$row1['Prosel'];
				}
			}
			else
			{
				$especiales= '';
			}
		}
		else
		{
			$especiales= '';
		}
	}
	else
	{
		//consulto los conceptos
		$q =  " SELECT Rlccam, Rlcpos, Cesnom, Cessel, Cescom "
		."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
		."      WHERE Rlccla = mid('".$clase."',1,instr('".$clase."','-')-1) "
		."        AND Rlctip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."        AND Rlccco = mid('".$cco."',1,instr('".$cco."','-')-1) "
		."        AND Rlcest = 'on' "
		."        AND Rlcvis = 'on' "
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
				if ($especiales[$i]['sel']=='on')
				{
					$exp=explode('-', $row1['Cescom'] );
					$q =  " SELECT Subcodigo, Descripcion "
					."        FROM det_selecciones "
					."      WHERE Medico = '".$exp[0]."' "
					."        AND Codigo = '".$exp[1]."' "
					."        AND Activo = 'A' "
					."        Order by 2 asc ";

					$res2 = mysql_query($q,$conex);
					$num2 = mysql_num_rows($res2);
					if ($num2>0)
					{
						for ($j=0;$j<$num2;$j++)
						{
							$row2 = mysql_fetch_array($res2);
							$especiales[$i][$j]=$row2['Subcodigo'].'-'.$row2['Descripcion'];
						}
						$especiales[$i]['num']=$num2;
					}
				}
			}
		}
		else
		{
			$especiales= '';
		}
	}
	
	return $especiales;
}

function consultarEspeciales($clase, $cco, $tipo)
{
	global $conex;
	global $wbasedato;

	$ClaseRequerimiento=explode("-",$clase);
	
	$requerimientosEspeciales = consultarAliasPorAplicacion($conex, '01', 'ClasesRequerimientosEspeciales');
	
	$requerimientoEspecial=explode(",",$requerimientosEspeciales);
	
	$EsRequerimientoEspecial=0;
	for($p=0;$p<count($requerimientoEspecial);$p++)
	{
		if($requerimientoEspecial[$p]==$ClaseRequerimiento[0])
		{
			$EsRequerimientoEspecial=1;
			break;
		}
	}
	
	if($EsRequerimientoEspecial==1)
	{
		$query="  SELECT Cladat 
					FROM root_000043 
				   WHERE Clacod=mid('".$clase."',1,instr('".$clase."','-')-1);";
		$respuesta = mysql_query($query,$conex);
		
		$row = mysql_fetch_array($respuesta);
		
		if($row[0]!="")
		{
			$qExiste="SELECT COUNT(id) 
						FROM ".$row[0].";";
			$respuestaExiste = mysql_query($qExiste,$conex);
			
			$rowExiste = mysql_fetch_array($respuestaExiste);
		}
		
		if($row[0]!="" && $rowExiste[0]>=0)
		{
			$q="  SELECT Procod,Prodes
					FROM ".$row[0]." 
				   WHERE Procla=mid('".$clase."',1,instr('".$clase."','-')-1)
					 AND Proest='on' 
				ORDER BY Prodes;";
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			
			if ($num1>0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($res1);
					$especiales[$i]['nombre']=$row1['Procod'].'-'.$row1['Prodes'];
					$especiales[$i]['sel']=$row1['Prosel'];
				}
			}
			else
			{
				$especiales= '';
			}
		}
		else
		{
			$especiales= '';
		}
	}
	else
	{
		//consulto los conceptos
		$q =  " SELECT Rlccam, Rlcpos, Cesnom, Cessel, Cescom "
		."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
		."      WHERE Rlccla = mid('".$clase."',1,instr('".$clase."','-')-1) "
		."        AND Rlctip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
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
				if ($especiales[$i]['sel']=='on')
				{
					$exp=explode('-', $row1['Cescom'] );
					$q =  " SELECT Subcodigo, Descripcion "
					."        FROM det_selecciones "
					."      WHERE Medico = '".$exp[0]."' "
					."        AND Codigo = '".$exp[1]."' "
					."        AND Activo = 'A' "
					."        Order by 2 asc ";

					$res2 = mysql_query($q,$conex);
					$num2 = mysql_num_rows($res2);
					if ($num2>0)
					{
						for ($j=0;$j<$num2;$j++)
						{
							$row2 = mysql_fetch_array($res2);
							$especiales[$i][$j]=$row2['Subcodigo'].'-'.$row2['Descripcion'];
						}
						$especiales[$i]['num']=$num2;
					}

				}
			}
		}
		else
		{
			$especiales= '';
		}
	}
	
	return $especiales;
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


function consultarPrioridades()
{
	global $conex;
	global $wbasedato;

	//consulto los conceptos
	$q =  " SELECT Subcodigo, Descripcion "
	."        FROM det_selecciones "
	."      WHERE Medico='".$wbasedato."' "
	."        AND Codigo='16' "
	."        AND Activo = 'A' "  
	."      ORDER BY Subcodigo ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$prioridades[$i]=$row1['Subcodigo'].'-'.$row1['Descripcion'];
		}
	}
	else
	{
		$prioridades= false;
	}

	return $prioridades;
}

function consultarEstados()
{
	global $conex;
	global $wbasedato;

	//consulto los conceptos
	$q =  " SELECT Estcod, Estnom "
	."        FROM ".$wbasedato."_000049 "
	."      WHERE Estest = 'on' order by 1";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$estados[$i]=$row1['Estcod'].'-'.$row1['Estnom'];
		}
	}
	else
	{
		$estados= false;
	}

	return $estados;
}

function consultarTiempos($clase)
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
		if ($num1>0)
		{
			for ($i=0;$i<$num1;$i++)
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

function consultarResponsables($usuario, $cco, $tipo)
{
	global $conex;
	global $wbasedato;

	//2007-06-20 se anade este pedazo para cuando el responsable sea visible, se actualice tambien dropdown de asignado a
	//Busco si el tipo trae responsable conjunto
	$q =  " SELECT Pervis "
	."        FROM ".$wbasedato."_000042  "
	."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND Perest = 'on' ";

	$res1 = mysql_query($q,$conex);
	$row1 = mysql_fetch_array($res1);
	if ($row1[0]=='on')
	{
		$exp=explode('(',$tipo);
		$usu=substr($exp[1],0,-1);
		$responsables[0]=$usu;
		$inicio=1;
		$exp=explode('-', $responsables[0]);
		$usu=$exp[0];
	}
	else
	{
		//Poner de primero el usuario si este esta activo como responsable
		$q =  " SELECT count(*) "
		."        FROM ".$wbasedato."_000042  "
		."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
		."        AND Perusu = '".$usuario['cod']."' "
		."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
		."        AND Perest = 'on' "
		."        AND Perres= 'on' ";

		$res1 = mysql_query($q,$conex);
		$row1 = mysql_fetch_array($res1);
		if ($row1[0]>0)
		{
			$responsables[0]=$usuario['cod'].'-'.$usuario['nom'];
			$inicio=1;
			$usu=$usuario['cod'];
		}
		else
		{
			$inicio=0;
			$usu=$usuario['cod'];
		}
	}

	//consulto los responsables de esa clase de ese tipo
	$q =  " SELECT Perusu, Descripcion"
	."        FROM ".$wbasedato."_000042, usuarios "
	."      WHERE Percco=mid('".$cco."',1,instr('".$cco."','-')-1) "
	."        AND Pertip = mid('".$tipo."',1,instr('".$tipo."','-')-1) "
	."        AND Perest = 'on' "
	."        AND Perres= 'on' "
	."        AND Perusu<> '".$usu."' "
	."        AND Perusu= Codigo ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$responsables[$inicio]=$row1['Perusu'].'-'.$row1['Descripcion'];
			$inicio++;
		}
	}
	else if ($inicio<1)
	{
		$responsables= false;
	}
	return $responsables;
}

function consultarCostos($cco)
{
	global $conex;
	global $wbasedato;

	if ($cco!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{

		$exp=explode('-',$cco);
		$cco2=$exp[0];

		$q =  " SELECT count(*) "
		."         FROM ".$wbasedato."_000041 "
		."      WHERE Mtrcco='".$cco2."' "
		."         AND Mtrest='on' ";

        $res1 = mysql_query($q,$conex);
        $row1 = mysql_fetch_array($res1);
        if ($row1[0]>0)
        {
            //2007-06-20 se despliega tambien el nombre de la empresa
            if (!isset($exp[2]))
            {
                $exp=explode(')',$cco);

                $emp=substr($exp[0], 1, strlen($exp[0]));

                $q =  " SELECT empdes"
                ."         FROM ".$wbasedato."_000050 "
                ."      WHERE Empcod='".$emp."' "
                ."         AND Empest='on' ";

                $resemp = mysql_query($q,$conex);
                $rowemp= mysql_fetch_array($resemp);

                $costos[0]=$cco.'-'.$rowemp[0];
            }
            else
            {
                $costos[0]=$cco;
            }

        }
        else
        {
            $costos[0]='';
        }
        $q =  " SELECT distinct Usucco  "
        ."         FROM ".$wbasedato."_000041, ".$wbasedato."_000039 "
        ."      WHERE Mtrcco!= mid('".$cco."',1,instr('".$cco."','-')-1) "
        ."         AND Mtrest='on' "
        ."         AND mid(Usucco,1,instr(Usucco,'-')-1)=Mtrcco
                   AND Usuest = 'on'";
	}
	else
	{
		$costos[0]='';
		$q =  " SELECT distinct Usucco"
		."         FROM ".$wbasedato."_000041, ".$wbasedato."_000039 "
		."      WHERE Mtrest='on' "
		."         AND mid(Usucco,1,instr(Usucco,'-')-1)=Mtrcco
               AND Usuest = 'on'";
	}

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			//2007-06-20 se despliega tambien el nombre de la empresa
			$exp=explode(')',$row1[0]);
			$emp=substr($exp[0], 1, strlen($exp[0]));

			$q =  " SELECT empdes"
			."         FROM ".$wbasedato."_000050 "
			."      WHERE Empcod='".$emp."' "
			."         AND Empest='on' ";

			$resemp = mysql_query($q,$conex);
			$rowemp= mysql_fetch_array($resemp);

			$costos[$i]=$row1[0].'-'.$rowemp[0];

		}
	}
	else if ($costos[0]=='')
	{
		$costos= false;
	}
	return $costos;
}

function consultarCostoXCodigo($buscador, &$costos)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT distinct Usucco  "
	."         FROM ".$wbasedato."_000041, ".$wbasedato."_000039 "
	."      WHERE Mtrcco like '%".$buscador."%' "
	."         AND Mtrest='on' "
	."         AND mid(Usucco,1,instr(Usucco,'-')-1)=Mtrcco ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			if ($i==0)
			{
				//2007-06-20 se despliega tambien el nombre de la empresa
				$exp=explode(')',$row1[0]);
				$emp=substr($exp[0], 1, strlen($exp[0]));

				$q =  " SELECT empdes"
				."         FROM ".$wbasedato."_000050 "
				."      WHERE Empcod='".$emp."' "
				."         AND Empest='on' ";
				$resemp = mysql_query($q,$conex);
				$rowemp= mysql_fetch_array($resemp);

				$costo=$row1[0].'-'.$rowemp[0];

			}
			//2007-06-20 se despliega tambien el nombre de la empresa
			$exp=explode(')',$row1[0]);
			$emp=substr($exp[0], 1, strlen($exp[0]));

			$q =  " SELECT empdes"
			."         FROM ".$wbasedato."_000050 "
			."      WHERE Empcod='".$emp."' "
			."         AND Empest='on' ";
			$resemp = mysql_query($q,$conex);
			$rowemp= mysql_fetch_array($resemp);

			$costos[$i]=$row1[0].'-'.$rowemp[0];

		}

	}
	else
	{
		$costo= '';
	}

	return $costo;
}

function consultarCostoXNombre($buscador, &$costos)
{
	global $conex;
	global $wbasedato;

	$q =  " SELECT distinct Usucco   "
	."         FROM ".$wbasedato."_000041, ".$wbasedato."_000039 "
	."      WHERE Usucco like  '%".$buscador."%' "
	."         AND Mtrest='on' "
	."         AND mid(Usucco,1,instr(Usucco,'-')-1)=Mtrcco ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=0;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			if ($i==0)
			{
				//2007-06-20 se despliega tambien el nombre de la empresa
				$exp=explode(')',$row1[0]);
				$emp=substr($exp[0], 1, strlen($exp[0]));

				$q =  " SELECT empdes"
				."         FROM ".$wbasedato."_000050 "
				."      WHERE Empcod='".$emp."' "
				."         AND Empest='on' ";
				$resemp = mysql_query($q,$conex);
				$rowemp= mysql_fetch_array($resemp);

				$costo=$row1[0].'-'.$rowemp[0];

			}
			//2007-06-20 se despliega tambien el nombre de la empresa
			$exp=explode(')',$row1[0]);
			$emp=substr($exp[0], 1, strlen($exp[0]));

			$q =  " SELECT empdes"
			."         FROM ".$wbasedato."_000050 "
			."      WHERE Empcod='".$emp."' "
			."         AND Empest='on' ";
			$resemp = mysql_query($q,$conex);
			$rowemp= mysql_fetch_array($resemp);

			$costos[$i]=$row1[0].'-'.$rowemp[0];

		}
	}
	else
	{
		$costo= '';
	}

	return $costo;
}



function consultarTipos($cco, $codigo, $tipo, &$vis)
{
	global $conex;
	global $wbasedato;
	
	if ($tipo!='') //cargo las opciones de fuente con ella como principal, consulto consecutivo y si requiere forma de pago
	{
		$q =  " SELECT Mtrcco,Mtrcod,Mtrcon,Mtrest,Mtrdes,Mtrvis,Mtrrws,Mtrdws,Mtrrcc,Mtrocu
		         FROM ".$wbasedato."_000041 
		      WHERE  Mtrcod =mid('".$tipo."',1,instr('".$tipo."','-')-1) 
		         AND Mtrcco =mid('".$cco."',1,instr('".$cco."','-')-1)      
                 AND Mtrocu <> 'on' 
		         AND Mtrest ='on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			$row1 = mysql_fetch_array($res1);
			$tipos[0]=$tipo;
			$vis=$row1['Mtrvis'];
			if($vis=='')
			{
				$vis='off';
			}
			$cadena="Mtrcod != mid('".$tipo."',1,instr('".$tipo."','-')-1) AND ";
			$contador=1;
			$exp=explode('-',$tipos[0]);
			if (isset($exp[2]))
			{
				$usu=substr($exp[2],1,strlen($exp[2]));
				$cadena2="Perusu != '".$usu."' AND";
			}
			else
			{
				$cadena2='';
			}
		}
		else
		{
			$cadena='';
			$cadena2='';
			$contador=0;
			$vis='';
		}
	}
	else
	{
		$cadena='';
		$cadena2='';
		$contador=0;
		$vis='';
	}


	$exp=explode('-',$cco);
	$cco=$exp[0];

	
	if ($cco!='')
	{
		$q =  " SELECT Pertip, Mtrdes, Descripcion, Mtrcod, Perusu, Mtrvis "
		."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041, usuarios "
		."      WHERE ".$cadena2." "
		."         Percco= '".$cco."' "
		."         AND Perusu='".$codigo."' "
		."         AND Perest='on' "
		."         AND Pervis='on' "
		."         AND Perrec='on' "
		."         AND Mtrcod=Pertip "
		."         AND Mtrcco=percco "
		."         AND Mtrest='on' "
		."         AND Mtrocu <> 'on'"
		."         AND Codigo=Perusu "
		."         AND Activo='A' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tipos[$contador]=$row1[0].'-'.$row1[1].'-('.$row1[4].'-'.$row1[2].')';
				$contador++;

				if($i==1 and $vis=='')
				{
					$vis=$row1[5];
					if($vis=='')
					{
						$vis='off';
					}
				}
			}
		}


		$q =  " SELECT Pertip, Mtrdes, Mtrvis "
		."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041 "
		."      WHERE ".$cadena." "
		."         Percco= '".$cco."' "
		."         AND Perusu='".$codigo."' "
		."         AND Perest='on' "
		."         AND Pervis<>'on' "
		."         AND Perrec='on' "
		."         AND Mtrcod=Pertip "
		."         AND Mtrcco=percco "
        ."         AND Mtrocu <> 'on'"
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

				if($i==1 and $vis=='')
				{
					$vis=$row1[2];
					if($vis=='')
					{
						$vis='off';
					}
				}
			}
		}


		$q =  " SELECT Pertip, Mtrdes, Mtrvis "
		."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041 "
		."      WHERE ".$cadena." "
		."         Percco= '".$cco."' "
		."         AND Perusu='".$codigo."' "
		."         AND Perest='on' "
		."         AND Perrec<>'on' "
		."         AND Perres='on' "
		."         AND Mtrcod=Pertip "
		."         AND Mtrcco=percco "
	    ."         AND Mtrocu <> 'on'"
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

				if($i==1 and $vis=='')
				{
					$vis=$row1[2];
					if($vis=='')
					{
						$vis='off';
					}
				}
			}
		}

		$q =  " SELECT Pertip, Pervis, Mtrdes, Descripcion, Mtrcod, Perusu, Mtrvis "
		."         FROM ".$wbasedato."_000042, ".$wbasedato."_000041, usuarios "
		."      WHERE  ".$cadena2." "
		."         Percco= '".$cco."' "
		."         AND Perusu<>'".$codigo."' "
		."         AND Perest='on' "
		."         AND Pervis='on' "
		."         AND Mtrcod=Pertip "
		."         AND Mtrcco=percco "
		."         AND Mtrest='on' "
		."         AND Mtrocu <> 'on' "
		."         AND Codigo=Perusu "
		."         AND Activo='A' ";
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tipos[$contador]=$row1[0].'-'.$row1[2].'-('.$row1[5].'-'.$row1[3].')';
				$contador++;

				if($i==1 and $vis=='')
				{
					$vis=$row1[6];
					if($vis=='')
					{
						$vis='off';
					}
				}
			}
		}

		$q =  " SELECT Mtrcod, Mtrdes, Mtrvis, Mtrocu
		         FROM ".$wbasedato."_000041 
		      WHERE  ".$cadena." 
		         Mtrcco= '".$cco."' 
		         AND Mtrcod NOT IN (SELECT Pertip from ".$wbasedato."_000042 where percco='".$cco."' and Pervis='on' ) 
		         AND Mtrcod NOT IN (SELECT Pertip from ".$wbasedato."_000042 where percco='".$cco."' and Perusu='".$codigo."' ) 
		         AND Mtrest  = 'on' 
		         AND Mtrocu <> 'on'
		      ORDER BY Mtrcod ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);
		if ($num1>0)
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				$tipos[$contador]=$row1[0].'-'.$row1[1];
				$contador++;

				if($i==1 and $vis=='')
				{
					$vis=$row1[2];
					if($vis=='')
					{
						$vis='off';
					}
				}
			}
		}
	}
	else
	{
		$tipos[0]='';
		$vis='';
	}

	return $tipos;
}

/**
 * Funcion a la cual se le envia el codigo matrix y el nombre del usuario inicial
 * y con eso llena un vector con los empleados registrados en Matrix a los cuales el usuario esta inscrito
 *
 * recibe: $codigo=codigo en matrix del usuario, tipo caracter
 * 		   $nombre=nombre en matrix del usuario, tipo caracter
 * retorna: $centros, tipo array con lista de centros para el usuario (codigo-nombre, si no encuentra centros retorna false
 */
function consultarPersonas($codigo, $nombre)
{
	global $conex;
	global $wbasedato;

	$personas[0]=$codigo.'-'.$nombre;

	//consulto los conceptos
	$q= " SELECT Codigo, Descripcion "
	."       FROM usuarios "
	."    WHERE Codigo <> mid('".$codigo."',1,instr('".$codigo."','-')-1) "
	."       AND Activo = 'A' "
	."    ORDER BY 2 ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	if ($num1>0)
	{
		for ($i=1;$i<$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$personas[$i]=$row1[0].'-'.$row1[1];
		}
	}

	return $personas;
}

function almacenarUsuario2($codigo, $cco, $car, $ext, $email, $sede)
{
    global $conex;
    global $wbasedato;

    $wsede = explode("-", $sede);

    //pregunto si ya existe el usuario y centro de costos
    echo$q =  " SELECT  id
            FROM    {$wbasedato}_000039
            WHERE   Usucco= '{$cco}'
                    AND Usuest='on'
                    AND Usucod='{$codigo}'";
	
	$res1 = mysql_query($q,$conex);
    // $row1 = mysql_fetch_array($res1);
    if(mysql_num_rows($res1) > 0)
    {
       echo $q= " update ".$wbasedato."_000039 set Usuext='".$ext."', Usuema='".$email."', Usucar='".$car."', Usused='".$wsede[0]."' "
        ."     where Usucco='".$cco."'  and  Usuest='on'   and  usucod='".$codigo."' ";
        $err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO ACTUALIZAR SUS DATOS EN EL MÓDULO DE REQUERIMIENTOS ".mysql_error());
	 }
    else
    {
        // A veces puede existir ese usuario en la tabla 000039 pero con un centro de costo -NO USAR- intenta insertarlo pero encuentra llave duplicada
        // así el registro esté inactivo.
        echo$q =  " SELECT  id
                FROM    {$wbasedato}_000039
                WHERE   Usucco= '{$cco}'
                        AND Usuest='off'
                        AND Usucod='{$codigo}'";

        $res2 = mysql_query($q,$conex);
        if(mysql_num_rows($res2) == 0)
        {
            echo$q= " INSERT INTO ".$wbasedato."_000039 (   Medico       ,   Fecha_data,                  Hora_data,              Usucod,      Usucco ,     Usuext   ,    Usuema   ,  Usucar   ,   Ususup ,         Usuest    , Usused,  Seguridad  ) "
            ."                               VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."','".$codigo."', '".$cco."', '".$ext."' , '".$email."' , '".$car."', 'off'  , 'on' , '".$wsede[0]."',  'C-".$codigo."') ";
            $err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR SUS DATOS EN EL MÓDULO DE REQUERIMIENTOS ".mysql_error());
        }
        else
        {
            // Este error puede darse desde la relación de la tabla usuarios y costosyp, en costosyp el código del centro de costos del usuario
            // tiene las palabras -NO USAR-
            echo "<br><br><div style='width:100%; text-align:center; background-color:#dfdfdf;font-weight:bold;font-size:14pt;'>
                      ES POSIBLE QUE DEBA SOLICITAR A SISTEMAS MODIFICAR SU CENTRO DE COSTOS, <br>TIENE ASOCIADO UN CENTRO DE COSTO INACTIVO O INCORRECTO.<br><br>
                      -NO SE PUDO CREAR EL REQUERIMIENTO-
                  </div>";
            exit();
        }
    }
}

function almacenarUsuario($codigo, $cco, $car, $ext, $email, $sede)
{
    global $conex;
    global $wbasedato;
    $wsede = explode("-", $sede);

    //pregunto si ya existe el usuario y centro de costos
    $q =  " SELECT  id
            FROM    {$wbasedato}_000039
            WHERE   Usuest='on'
                    AND Usucod='{$codigo}'";
	 
    $res1 = mysql_query($q,$conex);

    if(mysql_num_rows($res1) > 0)
    {
        $q= " update ".$wbasedato."_000039 set Usuext='".$ext."', Usuema='".$email."', Usucar='".$car."' , Usused='".$wsede[0]."' "
        ."     where  Usuest='on'   and  usucod='".$codigo."' ";
        $err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO ACTUALIZAR SUS DATOS EN EL MÓDULO DE REQUERIMIENTOS ".mysql_error());
		
    }
    else
    {
        // A veces puede existir ese usuario en la tabla 000039 pero con un centro de costo -NO USAR- intenta insertarlo pero encuentra llave duplicada
        // así el registro esté inactivo.
        $q =  " SELECT  id
                FROM    {$wbasedato}_000039
                WHERE   Usuest='off'
                        AND Usucod='{$codigo}'";

        $res2 = mysql_query($q,$conex);
        if(mysql_num_rows($res2) == 0)
        {
            $q= " INSERT INTO ".$wbasedato."_000039 (   Medico       ,   Fecha_data,                  Hora_data,              Usucod,      Usucco ,     Usuext   ,    Usuema   ,  Usucar   ,   Ususup ,         Usuest    ,  Usused,  Seguridad     ) "
            ."                               VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."','".$codigo."', '".$cco."', '".$ext."' , '".$email."' , '".$car."', 'off'  , 'on' , '".$wsede[0]."' ,  'C-".$codigo."') ";
            $err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR SUS DATOS EN EL MÓDULO DE REQUERIMIENTOS ".mysql_error());
        }
        else
        {
            // Este error puede darse desde la relación de la tabla usuarios y costosyp, en costosyp el código del centro de costos del usuario
            // tiene las palabras -NO USAR-
            echo "<br><br><div style='width:100%; text-align:center; background-color:#dfdfdf;font-weight:bold;font-size:14pt;'>
                      ES POSIBLE QUE DEBA SOLICITAR A SISTEMAS MODIFICAR SU CENTRO DE COSTOS, <br>TIENE ASOCIADO UN CENTRO DE COSTO INACTIVO O INCORRECTO.<br><br>
                      -NO SE PUDO CREAR EL REQUERIMIENTO-
                  </div>";
            exit();
        }
    }
}


function almacenarRequerimiento($cco, $usuNombre, $usucod, $ccoreq, $tipreq, $clareq, $temreq, $resreq, $desreq, $fecap, $horap, $porcen, $fecen, $horen, $estreq, $prireq, $codigo, $obsreq, $email, $sede, $tiporeq, $fallreq, $canalreq)
{
	global $conex;
	global $wbasedato;

	$q = "LOCK table ".$wbasedato."_000041 LOW_PRIORITY WRITE";
	$err = mysql_query($q,$conex);

	$q="UPDATE ".$wbasedato."_000041 "
	."SET	Mtrcon = Mtrcon+1 "
	."      WHERE  Mtrcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
	."         AND Mtrest='on' "
	."         AND Mtrcod=mid('".$tipreq."',1,instr('".$tipreq."','-')-1) ";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO INCREMENTAR EL CONSECUTIVO ".mysql_error());


	$q =  " SELECT Mtrcon, Mtrdws AS wdireccion_ws, Mtrrws AS wenviarpor_ws "
	."         FROM ".$wbasedato."_000041 "
	."      WHERE  Mtrcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
	."         AND Mtrest='on' "
	."         AND Mtrcod=mid('".$tipreq."',1,instr('".$tipreq."','-')-1) ";
	$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR EL CONSECUTIVO ".mysql_error());

	$row = mysql_fetch_array($err);
	$reqnum=$row[0];
    $enviar_por_ws = $row['wenviarpor_ws'];
    $ruta_ws = $row['wdireccion_ws'];

	$q = " UNLOCK TABLES";
	$err = mysql_query($q,$conex)or die (mysql_errno()." -NO SE HA PODIDO DESBLOQUEAR LA TABLA DE CAJEROS ".mysql_error());


	if ($resreq=='')
	{
		$exp=explode('-',$tipreq);
		if (isset($exp[4]))
		{
			$resreq=$exp[4];
			$recreq=$exp[4];
		}
		else
		{
			$q =  " SELECT Perusu "
			."         FROM ".$wbasedato."_000042 "
			."      WHERE  Pertip = mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
			."         AND Percco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
			."         AND Perest='on' "
			."         AND Perrec='on' ";
			$err = mysql_query($q,$conex) ;

			$row = mysql_fetch_array($err);
			$resreq=$row[0].'-xx';
			$recreq=$row[0].'-xx';
		}
	}
	else
	{
		$q =  " SELECT Perrec "
		."         FROM ".$wbasedato."_000042 "
		."      WHERE  Pertip = mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
		."         AND Percco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
		."         AND Perest='on' "
		."         AND Perusu= mid('".$resreq."',1,instr('".$resreq."','-')-1) ";
		$err = mysql_query($q,$conex) ;

		$row = mysql_fetch_array($err);

		if ($row[0]=='on')
		{
			$recreq=$resreq;
		}
		else
		{
			$q =  " SELECT Perusu, Descripcion "
			."         FROM ".$wbasedato."_000042, usuarios "
			."      WHERE  Pertip = mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
			."         AND Percco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
			."         AND Perest='on' "
			."         AND Perrec='on' "
			."         AND Perusu = Codigo ";

			$err = mysql_query($q,$conex) ;

			$row = mysql_fetch_array($err);
			$recreq=$row[0].'-'.$row[1];
		}
	}

	if(trim($desreq) != '')
	{
		$estreq=$estreq.'-xx';

		$cod_tipo_req_exp = explode("-", $tipreq);
    // $numeracion_unica = ($cod_tipo_req_exp[0].$reqnum) * 1;
		$numeracion_unica = ($cod_tipo_req_exp[0].$reqnum); // no se puede multiplicar por 1 porque se pueden repetir codigo por ejemplo (01 * 17 => 117  sería igual a 11 * 7 => 117)
				
		$q= " INSERT INTO ".$wbasedato."_000040 (          Medico,            Fecha_data,                   Hora_data,    Reqcco,     Reqnum ,      Reqtip,                  Reqtpn,       Reqfec,     Requso,        Requrc,       Reqdes,       Reqpurs,    Reqpri,       Reqfae,        Reqhae,       Reqest,     Reqcum,       Reqfen,   Reqhen,   Reqobe,          Reqtde,       Reqcla,     Reqccs,      Reqsed, Reqtir,       Reqfal,   Reqcan,   Seguridad) "
		."                               VALUES ('".$wbasedato."',   '".date('Y-m-d')."', '".(string)date("H:i:s")."', mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1), '".$reqnum."', mid('".$tipreq."',1,instr('".$tipreq."','-')-1), '".$numeracion_unica."', '".date('Y-m-d')."', '".$usucod."', mid('".$recreq."',1,instr('".$recreq."','-')-1), '".$desreq."', mid('".$resreq."',1,instr('".$resreq."','-')-1), mid('".$prireq."',1,instr('".$prireq."','-')-1), '".$fecap."',  '".$horap."', mid('".$estreq."',1,instr('".$estreq."','-')-1) , '".$porcen."', '".$fecen."', '".$horen."', '".$obsreq."',  mid('".$temreq."',1,instr('".$temreq."','-')-1),   mid('".$clareq."',1,instr('".$clareq."','-')-1) ,'".substr($cco,0,8)."', mid('".$sede."',1,instr('".$sede."','-')-1), mid('".$tiporeq."',1,instr('".$tiporeq."','-')-1), mid('".$fallreq."',1,instr('".$fallreq."','-')-1), mid('".$canalreq."',1,instr('".$canalreq."','-')-1),'C-".$codigo."') ";            


		if($err = mysql_query($q,$conex))
		{

            // Si el centro de costo debe conectar a un webservice entonces continúa.
            if($enviar_por_ws == 'on')
            {
                $exp = explode("-", $ccoreq);
                $ccos_r = $exp[0];

                $exp        = explode("-", $prireq);
                $wprioridad = $exp[0];

                $exp_est = explode("-", $estreq);
                $estado_req = $exp_est[0];

                // Busca el equivalente del estado para enviarlo al consumir el web service.
                $q = "  SELECT  Esteam AS westado_am
                        FROM    ".$wbasedato."_000049
                        WHERE   Estcod = '".$estado_req."'";
                $resultam   = mysql_query($q,$conex);
                $rowam      = mysql_fetch_array($resultam);
                $westado_am = $rowam['westado_am'];

                // $cco = (01)1710-INFORMATICA
                $expCco = explode(')', $cco); // [1] = 1710-INFORMATICA
                $expCco = explode('-', $expCco[1]); // [0] = 1710
                $ccos_solicitante = $expCco[0];

                // Se setean los campos que serán enviados al consumir el webservice.
                $campos_solicitud_ws = array(
                                            'OS'                 => $numeracion_unica,
                                            'CCOS'               => $ccos_solicitante,
                                            'DiagnosticoOS'      => $desreq,
                                            'SolicitanteOS'      => $usuNombre,
                                            'EstadoOS'           => $westado_am,
                                            'SolicitanteEmailOS' => $email
                                        );

                // Se llama la función encargada de consumir el webservice.
                $error = guardarRequerimientoMantenimiento($conex, $wbasedato, $campos_solicitud_ws, $ruta_ws, $numeracion_unica);

                // Si ocurrió un error con el webservice entonces se informa en pantalla del error. Esto de todas formas hace que
                // Se guarde el requerimiento en Matrix pero no quede guardado en el sistema de mantenimiento AM.
                if($error == 'on')
                {
                    pintarAlertAM('/!\\ El requerimiento '.$ccos_r.'-'.$reqnum.' [Cod. Interno: '.$numeracion_unica.'] Se guard&oacute; en Matrix pero NO SE GUARD&Oacute; EN EL SISTEMA DE MANTENIMIENTO /!\\<br>Informe a sistemas.');
                }
            }
		}
		else
		{
			echo (mysql_errno()." -NO SE HA PODIDO GUARDAR EL REQUERIMIENTO ".mysql_error());
		}
	}

	return $reqnum;
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

function almacenarEspeciales($ccoreq, $tipreq, $clareq, $especiales, $reqnum, $codigo)
{
	// var_dump($especiales);echo "hola1";
	global $conex;
	global $wbasedato;

	$clase=explode("-",$clareq);
	
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
		
		if($rowResultado[0]!="" && $rowExiste[0]>=0)
		{
			foreach ($especiales as $clave => $valor) 
			{
				if($especiales[$clave]['val']!="")
				{
					$insert = " INSERT INTO ".$rowResultado[0]." (Medico,Fecha_data,Hora_data,Reqcla,Reqnum,Reqpro,Reqcas,Reqdes,Seguridad) VALUES ('".$codigo."', '".date('Y-m-d')."', '".(string)date("H:i:s")."','".$clase[0]."', '".$reqnum."','".$clave."',".$especiales[$clave]['val'].", 'off', 'C-".$codigo."')";
					$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());	
				}
			}
		}
	}
	else
	{
		$q =  " SELECT Rcttab "
		."         FROM ".$wbasedato."_000044 "
		."      WHERE  Rctcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
		."         AND Rcttip=mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
		."         AND Rctcla=mid('".$clareq."',1,instr('".$clareq."','-')-1) "
		// ."         AND Rctest='on' "
		."         AND Rctesp='on' "
		;

		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA TABLA DE ALMACENAMIENTO DE CAMPOS ESPECIALES ".mysql_error());

		$row = mysql_fetch_array($err);
		$tabnum=$row[0];
		$exp=explode('_', $tabnum);
		
		if(isset($exp[1]))
		{
			$insert = " INSERT INTO ".$tabnum." (Medico, Fecha_data, Hora_data, Espreq ";
		}
		else
		{
			$insert = " INSERT INTO ".$wbasedato."_".$tabnum." (Medico, Fecha_data, Hora_data, Espreq ";
		}

		$q =  " SELECT Cesnom, Rlcpos "
		."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
		."      WHERE Rlccla = mid('".$clareq."',1,instr('".$clareq."','-')-1) "
		."        AND Rlctip = mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
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

		foreach ($especiales as $clave => $valor) 
		{
			$insert = $insert. ", '".$especiales[$clave]['val']."'" ;
		}

		$insert = $insert. ", 'C-".$codigo."' )" ;
		$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());
	}		
	
}


function almacenarEspeciales2($ccoreq, $tipreq, $clareq, $especiales, $reqnum, $codigo)
{
	// var_dump($especiales);echo "hola2";
	global $conex;
	global $wbasedato;

	$clase=explode("-",$clareq);
	
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
		
		if($rowResultado[0]!="" && $rowExiste[0]>=0)
		{
			foreach ($especiales as $clave => $valor) 
			{
				if($especiales[$clave]['val']!="")
				{
					$insert = " INSERT INTO ".$rowResultado[0]." (Medico,Fecha_data,Hora_data,Reqcla,Reqnum,Reqpro,Reqcas,Reqdes,Seguridad) VALUES ('".$codigo."', '".date('Y-m-d')."', '".(string)date("H:i:s")."','".$clase[0]."', '".$reqnum."','".$clave."',".$especiales[$clave]['val'].",'off', 'C-".$codigo."')";
					$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());	
				}
			}
		}
	}
	else
	{
		$q =  " SELECT Rcttab "
		."        FROM ".$wbasedato."_000044 "
		."       WHERE  Rctcco= mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
		."         AND Rcttip=mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
		."         AND Rctcla=mid('".$clareq."',1,instr('".$clareq."','-')-1) "
		// ."         AND Rctest='on' "
		."         AND Rctesp='on' "
		;

		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO ENCONTRAR LA TABLA DE ALMACENAMIENTO DE CAMPOS ESPECIALES ".mysql_error());

		$row = mysql_fetch_array($err);
		$tabnum=$row[0];
		$exp=explode('_', $tabnum);
		if(isset($exp[1]))
		{
			$insert = " INSERT INTO ".$tabnum." (Medico, Fecha_data, Hora_data, Espreq ";
		}
		else
		{
			$insert = " INSERT INTO ".$wbasedato."_".$tabnum." (Medico, Fecha_data, Hora_data, Espreq ";
		}

		$q =  " SELECT Cesnom, Rlcpos "
		."        FROM ".$wbasedato."_000048, ".$wbasedato."_000046 "
		."      WHERE Rlccla = mid('".$clareq."',1,instr('".$clareq."','-')-1) "
		."        AND Rlctip = mid('".$tipreq."',1,instr('".$tipreq."','-')-1) "
		."        AND Rlccco = mid('".$ccoreq."',1,instr('".$ccoreq."','-')-1) "
		."        AND Rlcest = 'on' "
		."        AND Rlcvis = 'on' "
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

		foreach ($especiales as $clave => $valor) 
		{
			$insert = $insert. ", '".$especiales[$clave]['val']."'" ;
		}

		$insert = $insert. ", 'C-".$codigo."' )" ;

		$err = mysql_query($insert,$conex) or die (mysql_errno()." -NO SE HAN PODIDO GUARDAR LOS CAMPOS ESPECIALES DEL REQUERIMIENTO ".mysql_error());
	}
	
}

//----------------------------------------------------------funciones de presentacion------------------------------------------------


/**
 * Escribe en el programa el autor y la version del Script
 * No recibe ningun parametro
 */
function pintarVersion()
{
	$wautor="Carolina Castaño P.";
	$wversion="2012-05-30";
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;
}

/**
 * Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a los scripts consulta.php, seguimiento.php
 * para consulta.php existen dos opciones mandandole el paramentro para=recibidos o para=enviados, asi ese Script consultara
 * uno u otro tipo de requerimiento
 *
 * Adicionalmente esta funcione se encarga de abrir la forma del Script que se llama informatica
 *
 * No necesita ningun parametro ni devuelve
 */
function pintarTitulo($wacutaliza, $titulo_requerimientos)
{
	echo encabezado("<div class='titulopagina2'>".$titulo_requerimientos."</div>", $wacutaliza, 'clinica');
	echo "<form id='informatica' name='informatica' action='informatica.php' method=post >";
	echo "<table ALIGN=CENTER width='50%'>";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	//echo "<tr><td style='font-weight:bold; ' class='encabezadoTabla' style='font-size:15pt; text-align:center;'>SISTEMA DE REQUERIMIENTOS</td></tr>";
	echo "<tr><td style='font-weight:bold; ' class='titulo2'>Fecha: ".date('Y-m-d')."&nbsp Hora: ".(string)date("H:i:s")."</td></tr></table></br>";

	echo "<table ALIGN=CENTER width='90%' >";
	//echo "<tr><td align=center colspan=1 ><img src='/matrix/images/medical/general/logo_promo.gif' height='100' width='250' ></td></tr>";
	echo "<tr><a href='informatica.php'><td style='font-weight:bold; ' class='encabezadoTabla' width='20%'>INGRESO DE REQUERIMIENTO</td></a>";
	echo "<td style='font-weight:bold; ' class='texto5' width='20%'><a href='consulta.php?para=recibidos'>REQUERIMIENTOS RECIBIDOS</a></td>";
	echo "<td style='font-weight:bold; ' class='texto5' width='20%'><a href='consulta.php?para=enviados'>REQUERIMIENTOS ENVIADOS</a></td>";
	echo "<td style='font-weight:bold; ' class='texto5' width='20%'><a href='enviado.php'>REQUERIMIENTOS ANT.</a></td></tr></a>";
	echo "<tr class='fila1'><td style='font-weight:bold; ' class='' >&nbsp;</td>";
	echo "<td style='font-weight:bold; ' class='' >&nbsp;</td>";
	echo "<td style='font-weight:bold; ' class='' >&nbsp;</td>";
	echo "<td style='font-weight:bold; ' class='' >&nbsp;</td>";
	echo "<td style='font-weight:bold; ' class='' >&nbsp;</td></tr></table>";
}

/*se le envia para pintar en html un vector de usuario ($usuario) con las siguientes caracteristicas:
*  ['cod']=codigo del usuario
*  ['cco']=primer centro de costos encontrado para el usuario (podrian haber mas, luego se muestran)
*  ['ext']=extension del usuario
*  ['ema']=email
*  ['car']=cargo
*  ['nom']=nombre
*  ['sup']= en on quiere decir que debe permitir ingresar otros usuarios
*
* Adicionalmente se le manda la lista de usuarios ($personas) por si el usuario puede solicitar por otro
*/
function pintarUsuario($usuario, $sup, $centros, $personas, $sedes='', $sede)
{
	foreach ($_POST as $nomVariable => $valor)
		$$nomVariable = $valor;
	
    global $cco_auditoria_corporativa_clinica;

	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<tr class='fila1'><td colspan='4' class='encabezadoTabla' align='center'><b>Informacion del Usuario</b></td></tr>";
	if ($sup=='on')
	{
		echo "<tr class='fila1'><td style='font-weight:bold; ' class='' colspan='4' align='center'>Solicita: ";
		echo "<select name='tip'>";
		echo "<option>Codigo</option>";
		echo "<option>Nombre</option>";
		echo "</select><input type='TEXT' name='perbus' value='' size=10>&nbsp;<INPUT TYPE='button' NAME='buscar' VALUE='Buscar' onclick='enter1()'> ";
		echo "<select name='per' onchange='enter1()'>";
		for ($i=0;$i<count($personas);$i++)
		{
			echo "<option>".$personas[$i]."</option>";
		}
		echo "</select>";
		echo "</td></tr>";
	}
	else
	{
		echo "	<tr class='fila1'>
					<td style='font-weight:bold; ' class='encabezadoTabla'>Codigo:</td>
					<td style='font-weight:bold; ' class=''>".$usuario['cod']."</td>
					<td style='font-weight:bold; ' class='encabezadoTabla'>Nombre:</td>
					<td style='font-weight:bold; ' class=''>".$usuario['nom']."</td>
				</tr>";
	}
	echo "<tr class='fila1'>
			<td style='font-weight:bold; ' class='encabezadoTabla'>Centro de costos: </td>
			<td style='font-weight:bold; ' class=''>
            <input type='hidden' id='cco_auditoria_corp' name='cco_auditoria_corp' value='".$cco_auditoria_corporativa_clinica."' />";
	// echo "<select name='cco' onchange='enter1()'>";
	echo "<select name='cco' id='cco'  required>";	
	for ($i=0;$i<count($centros);$i++)
	{
		echo "<option ".(($cco == $centros[$i]) ? 'selected' : '').">".$centros[$i]."</option>";
	}
	echo "</select></td>
			<td style='font-weight:bold; ' class='encabezadoTabla'>Cargo:</td>
			<td style='font-weight:bold; ' class=''><input type='TEXT' name='car' value='".$usuario['car']."' size=30></td>
		</tr>";
	echo "<tr class='fila1'>
			<td style='font-weight:bold; ' class='encabezadoTabla'>Email:</td>
			<td style='font-weight:bold; ' class=''><input type='TEXT' name='email' value='".$usuario['ema']."' size=40></td>
			<td style='font-weight:bold; ' class='encabezadoTabla'>Extension:</td>
			<td style='font-weight:bold; ' class=''><input type='TEXT' name='ext' value='".$usuario['ext']."' size=30></td>
		</tr>";
	echo "<tr class='fila1'>
	        <td style='font-weight:bold; ' class='encabezadoTabla'>Sede:</td>
	        <td style='font-weight:bold; ' class=''>";
	        echo "<select name='sede' id='sede'  required>";
			for ($i=0;$i<count($sedes);$i++)
			{
				$datsed = explode('-',$sedes[$i]);
				echo "<option ".(($sede == $datsed[0]) ? 'selected' : '').">".$sedes[$i]."</option>";
			}
			echo "</td>";
	echo "</tr>";
	echo "</table></br>";
}

function pintarRequerimiento($usuario ,$costos,  $tipos, $clases, $responsables, $prioridades, $fecap, $horap, $estados, $tiempos, $porcentaje, $fecen, $horen, $especiales, $desreq, $perf, $clareq, $cco, $tipoRequerimiento, $tipoFalla, $tipoCanal)
{
    global $req_varios_resp;
	global $conex;
	
		
	//Tamaño de letra y cantidad de columna por defecto
	$tamLetra=10;
	$cantCol=2;
	
	if ($clases!='')
	{
		$clreq=explode('-',$clases[0]);
		
		$clasesRequerimientos  = consultarAliasPorAplicacion($conex, "01", "ClasesRequerimientosEspeciales");
		$claseRequerimiento=explode(",",$clasesRequerimientos);
			
		$EsRequerimientoEspecial=0;
		for($k=0;$k<count($claseRequerimiento);$k++)
		{
			if($claseRequerimiento[$k]==$clreq[0])
			{
				$EsRequerimientoEspecial=1;
				break;
			}
			
		}
		
		$numColumnas  = consultarAliasPorAplicacion($conex, "01", "NumColumClaseRequerimiento");
		$numClmn=explode(",",$numColumnas);
		
		for($y=0;$y<count($numClmn);$y++)
		{
			$numCol=explode("-",$numClmn[$y]);
			
			if($numCol[0]==$clreq[0])
			{
				$columnas=explode("(",$numCol[1]);
				$letra=explode(")",$columnas[1]);
								
				$cantCol=$columnas[0];
				$tamLetra=$letra[0];
				
				break;
			}
		}
		
	}
	
	if(($cantCol%2)==0)
	{
		$colspanDoble=$cantCol;
		$colspanSencillo=$cantCol/2;
	}
	else
	{
		$colspanDoble=$cantCol*2;
		$colspanSencillo=$cantCol;
	}
	
	echo "<table border=0 ALIGN=CENTER width=90%>";

	echo "<tr><td class='encabezadoTabla' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanDoble."'": "colspan='2'")." ><b>Ingreso de Nuevo Requerimiento</b></td></tr>";

	echo "<tr class='fila2'><td style='font-weight:bold;' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanDoble."'": "colspan='2'")." >Centro de costos:";
	echo "<select name='tip2'>";
	echo "<option>Codigo</option>";
	echo "<option>Nombre</option>";
	echo "</select><input type='TEXT' name='cosbus' value='' size=10>&nbsp;<INPUT TYPE='button' NAME='buscar2' VALUE='Buscar' onclick='enter1()'> ";
	if ($clases!='')
	{
	    echo "<select id='ccoreq' name='ccoreq' onchange='enter2()'>";
	}
	else
	{
		echo "<select id='ccoreq' name='ccoreq' onchange='enter3()'>";
	}
	foreach($costos as $key=>$costo)
	{
		$ccofin = trim(str_replace('-PROMOTORA MEDICA LAS AMERICAS','',$costo));
	
	    $selected = ($cco == $ccofin) ? "selected='true'" : '';	
		
	    echo "<option value='".$ccofin."' >".$costo."</option>";
	}
	echo "</select></td></tr>";

	if ($clases!='')
	{
		if ($perf)
		{
			echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tipo de requerimiento:";
			echo "<select name='tipreq'  required onchange='enter()'>";
			
			for ($i=0;$i<count($tipos);$i++)
			{
				echo "<option>".$tipos[$i]."</option>";
			}
		}
		else
		{
			echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tipo de requerimiento:";
			echo "<select name='tipreq' required onchange='enter()'>";
			
			for ($i=0;$i<count($tipos);$i++)
			{
				echo "<option>".$tipos[$i]."</option>";

			}
		}
		echo "</select></td>";

		echo "<td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Clase de requerimiento:";
		$horariosValidos = consultarHorariosValidos($conex, $clases);
		echo "<select name='clareq' onchange='enter1();'>";
		
		$claseSeleccionada = "";
		for ($i=0;$i<count($clases);$i++)
		{
			$exp=explode('-',$clases[$i]);
			
			if($claseSeleccionada=="" && $clareq=="")
			{
				$claseSeleccionada = $exp[0];
			}
			else if($claseSeleccionada=="")
			{
				$clasereq = explode("-",$clareq);
				$claseSeleccionada = $clasereq[0];
			}
			echo "<option>".$exp[0]."-".$exp[1]."</option>";
		}
		echo "</select></td></tr>";

		if(!$horariosValidos[$claseSeleccionada]['horarioValido'])
		{
			echo "<script>alert('Solo es posible ingresar requerimientos de ".strtoupper($horariosValidos[$claseSeleccionada]['descripcion'])." entre las ".$horariosValidos[$claseSeleccionada]['horaInicial']." y las ".$horariosValidos[$claseSeleccionada]['horaFinal']."');</script>";
		}
		else
		{
			// echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' colspan='2'>Descripcion: </br><textarea name='desreq' cols='80' rows='4'>".$desreq."</textarea></td></tr>";
			echo "<tr class='fila2'><td style='font-weight:bold;' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanDoble."'": "colspan='2'")." >Descripcion: </br><textarea name='desreq' cols='80' rows='4' required>".$desreq."</textarea></td></tr>";

			if($perf)
			{
				if ( $tiempos!="" && count($tiempos)>1)
				{
					echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center'  ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tiempo de desarrollo:";
					echo "<select name='temreq' onchange=''>";
					for ($i=0;$i<count($tiempos);$i++)
					{
						echo "<option>".$tiempos[$i]."</option>";
					}
					echo "</select></td>";
					$colspan=1;
					echo "<td style='font-weight:bold; ' class='' align='center' ".$colspan."' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Asignado a:";
				}
				else
				{
					$colspan=2;
					echo "<tr class='fila2'>";
					echo "<td style='font-weight:bold;' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanDoble."'": "colspan='2'")." >Asignado a:";
				}
				

				if($req_varios_resp == 'on')
				{
					?>
						<div style="height:150px; overflow:scroll; border: 2px solid #ffffff;">
							<ul style="text-align:left;list-style-type:none;margin:0; padding:0;">
								<?php
								$c = '';
								foreach ($responsables as $key => $value)
								{
									$exp = explode('-', $value);
									$cssli = ($c%2 == 0) ? 'fila1': 'fila2';
									?>
									<li class='<?=$cssli?>'>
										<input type="checkbox" id="<?='resreq_'.$exp[0]?>" name="resreq[]" value ="<?=$value?>" /> <span><?=$value?></span>
									</li>
									<?php
									$c++;
								}
								?>
							</ul>
						</div>
					<?php
				}
				else
				{
					echo "<select name='resreq' onchange=''>";
					for ($i=0;$i<count($responsables);$i++)
					{
						echo "<option>".$responsables[$i]."</option>";
					}
					echo "</select>";
				}
				echo"</td></tr>";

				echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Prioridad:";
				echo "<select name='prireq' onchange=''>";
				for ($i=0;$i<count($prioridades);$i++)
				{
					 echo "<option>".$prioridades[$i]."</option>";
				}
				echo "</select></td>";
				echo "<td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Fecha y Hora aproximada de atenci&oacute;n: <input type='TEXT' name='fecap' value='".$fecap."' maxLength=10 size=8><input type='TEXT' name='horap' value='".$horap."' maxLength=10 size=8></td></tr>";

				//Campos Tipo de requerimiento Y Clasificación de Tipo de falla
				echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tipo de requerimiento:";
				echo "<select name='tiporeq' onchange=''>";
				for ($i=0;$i<count($tipoRequerimiento);$i++)
				{
					echo "<option>".$tipoRequerimiento[$i]."</option>";
				}
				echo "</select></td>";

				echo "<td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Tipo de falla:";
				echo "<select name='fallreq' onchange=''>";
				for ($i=0;$i<count($tipoFalla);$i++)
				{
					echo "<option>".$tipoFalla[$i]."</option>";
				}
				echo "</select></td></tr>";
                

                echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='2'").">Tipo de canal:";
				echo "<select name='canalreq' onchange=''>";
				for ($i=0;$i<count($tipoCanal);$i++)
				{
					echo "<option>".$tipoCanal[$i]."</option>";
				}
				echo "</select></td></tr>";

			}

			if (is_array($especiales))
			{
				$par=2;
				$col=1;
				$par2=$cantCol;
				for ($i=0;$i<count($especiales);$i++)
				{
					$exp=explode('-',$especiales[$i]['nombre']);
					
					if ($especiales[$i]['sel']=='on')
					{
						if (is_int($par/2))
						{
							echo "<tr class='fila2'>";
						}

						echo "<td align=center  class=''>".$exp[1].": <select name='especiales[".$i."][val]'>";
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
							if($col==1)
							{
								echo "<tr class='fila2'>";
							}

							// echo "<td style='font-weight:bold;font-size:8pt; ' class='' align='left' colspan='1'>".$exp[1].": <input type='TEXT' name='especiales[".$i."][val]' size=2 class='AlinearInputs' value='' size=10 title='Ingrese la cantidad a solicitar' onkeypress=\"return permite(event, 'num')\"></td>";
							echo "<td style='font-size:8pt; ' class='' align='left' ".((($cantCol%2)==0) ? "colspan='1'": "colspan='".($colspanDoble/$cantCol)."'").">
									<table width='100%'>
										<tr style='font-size:".$tamLetra."pt;font-weight:bold;'>
											<td width='85%' align='left'>
												".strtoupper(trim($exp[1])).": 
											</td>
											<td width='15%' align='center'>
												<!-- <input type='TEXT' name='especiales[".$i."][val]' size=2 class='AlinearInputs' value='' size=10 title='Ingrese la cantidad a solicitar' onkeypress=\"return justNumbers(event);\"> -->
												<input type='TEXT' name='especiales[".$exp[0]."][val]' size=2 class='AlinearInputs' value='' size=10 title='Ingrese la cantidad a solicitar' onkeypress=\"return justNumbers(event);\" onBlur=\"cambiarFondoInput(this);\">
											</td>
										</tr>
									</table>
							 </td>";
							
							if($col==$cantCol)
							{
								echo "</tr>";
								$col=1;
							}
							else
							{
								$col++;
							}
						}
						else
						{
							if (is_int($par/2))
							{
								echo "<tr class='fila2'>";
							}

							echo "<td style='font-weight:bold; ' class='' align='center'>".$exp[1].": <input type='TEXT' name='especiales[".$i."][val]' value='' size=10></td>";

							if (!is_int($par/2))
							{
								echo "</tr>";
							}
							$par++;
						}
					}
				}
				
				if($EsRequerimientoEspecial==1)
				{
					$col--;
					
						
					if ($col!=$cantCol && $col!=0)
					{
						$tdFaltantes = $cantCol-$col;
						for($r=0;$r<$tdFaltantes;$r++)
						{
							echo "<td style='font-weight:bold; ' class='' align='center' ".((($cantCol%2)==0) ? "colspan='1'": "colspan='".($colspanDoble/$cantCol)."'").">&nbsp;</td>";
						}
						echo "</tr>";
					}
				}
				else
				{
					if (!is_int($par/2))
					{
						echo "<td style='font-weight:bold; ' class='' align='center'>&nbsp;</td></tr>";

					}
				}
				
				
				
			}

			if ($perf)
			{
				if($porcentaje!='')
				{
					echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Porcentaje de cumplimiento: <input type='TEXT' name='porcen' value='".$porcentaje."' size=10></td>";
					$colspan=1;
					echo "<td style='font-weight:bold; ' class='' align='center'  ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'")."> Estado:";
				}
				else
				{
					$colspan=2;
					echo "<tr class='fila2'>";
					echo "<td style='font-weight:bold;' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanDoble."'": "colspan='2'")." > Estado:";
				}
				// echo "<td style='font-weight:bold; ' class='' align='center' colspan='".$colspan."'> Estado:";
				
				echo "<select name='estreq' onchange=''>";
				for ($i=0;$i<count($estados);$i++)
				{
					echo "<option>".$estados[$i]."</option>";
				}
				echo "</select></td></tr>";
				echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">Fecha y hora de entrega: <input type='TEXT' name='fecen' value='".$fecen."' maxLength=10 size=8><input type='TEXT' name='horen' value='".$horen."' maxLength=10 size=8></td>";
				echo "<td style='font-weight:bold; ' class='' align='center' ".(($EsRequerimientoEspecial==1) ? "colspan='".$colspanSencillo."'": "colspan='1'").">observacion:  </br><textarea name='obsreq' cols='40' rows='4'></textarea></td></tr>";
			}
		}
		
	}
	else
	{
		echo "<td style='font-weight:bold; ' class='' align='center'>Tipo de requerimiento:";
		echo "<select name='tipreq' onchange='enter()'>";
		for ($i=0;$i<count($tipos);$i++)
		{
			echo "<option>".$tipos[$i]."</option>";
		}
		echo "</select></td></tr>";
		echo "<tr class='fila2'><td style='font-weight:bold; ' class='' align='center' colspan='2'>Descripcion:  </br><textarea name='desreq' cols='80' rows='4'>".$desreq."</textarea></td></tr>";
	}

	echo "</table>";
}

function pintarBoton($control)
{
	echo "<table border=0 ALIGN=CENTER width=90%>";
	echo "<TR class='fila2'><td style='font-weight:bold; ' class='' colspan=5 >&nbsp;</td></tr>";
	echo "<tr><td colspan='5' align='center'><h3 id='nocco' style='color:red;' class='parpadea'></h3></td></tr>";
	echo "<td style='font-weight:bold; ' class='fila1' colspan=5 align='center'>ENVIAR<input type='checkbox' name='enviar' id='enviar' checked='checked' style='display:none;'>&nbsp;";
	echo "<INPUT TYPE='submit' id='ok' NAME='ok' VALUE='OK' ></td></tr>";
	echo "</table></br></br>";
	echo "<input type='HIDDEN' name= 'control' value='".$control."'>";
	echo "</form>";

}

/**
 * Muestra en un alert javascript un mensaje determinado
 *
 * Recibe:
 * @param unknown_type $mensaje, caracter con el mensaje para deplegar por pantalla a usuario
 */
function pintarAlert1($mensaje)
{
	echo '<script language="Javascript">';
	echo 'alert ("'.$mensaje.'")';
	echo '</script>';
}


function pintarAlert3($mensaje)
{
	echo "</table></br>";
	echo"<CENTER>";
	echo "<table align='center' border=0 bordercolor=#000080 width=700>";
	echo "<tr><td colspan='2' align=center><font size=5 color='#000080' face='arial' align=center><b>".$mensaje."</td></tr>";
	echo "<tr><td colspan='2' align=center><font size=5 color='#000080' face='arial' align=center><b>&nbsp;</td></tr>";
	echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b><a href='informatica.php'>Nuevo requerimiento</a></td></tr>";

	echo "</table>";
}


function pintarAlert2($mensaje)
{
    echo "</table>";
    echo"<CENTER>";
    echo "<table align='center' border=0 bordercolor=#000080 width=700>";
    echo "<tr><td colspan='2' align=center><font size=3 color='#000080' face='arial' align=center><b>".$mensaje."</td></tr>";
    echo "</table>";
}

function pintarAlertAM($mensaje)
{
	echo "</table></br>";
    echo"<CENTER>";
    echo "<table align='center' border=0 bordercolor=#000080 width=700>";
    echo "<tr><td colspan='2' align=center><font style='color: red; font-weight: bold;' size=5 color='#000080' face='arial' align=center><b>".$mensaje."</td></tr>";
    echo "<tr><td colspan='2' align=center><font size=5 color='#000080' face='arial' align=center><b>&nbsp;</td></tr>";
    echo "</table>";
}

function consultarHorariosValidos($conex, $clases)
{
	$clasesPorTipo = "";
	foreach ($clases as $key => $value)
	{
		$clasesReq = explode("-",$value);
		$clasesPorTipo .= "'".$clasesReq[0]."',";
	}
	
	$clasesPorTipo = substr($clasesPorTipo, 0, -1);
	
	$queryHorarios = "SELECT Clacod,Clades,Clahir,Clahfr 
						FROM root_000043 
					   WHERE Clacod IN (".$clasesPorTipo.");";
					   
	$resHorarios =  mysql_query($queryHorarios,$conex) or die ("Error: ".mysql_errno()." - en el query consultar horarios: ".$queryHorarios." - ".mysql_error());
	$numHorarios = mysql_num_rows($resHorarios);	
	
	$arrayHorarios = array();
	if($numHorarios > 0)
	{
		$horaActual = date("H:i:s");
		while($rowHorarios = mysql_fetch_array($resHorarios))
		{
			
			
			if($rowHorarios['Clahir']=="00:00:00" && $rowHorarios['Clahfr']=="00:00:00")
			{
				$horarioValido = true;
			}
			elseif($horaActual >= $rowHorarios['Clahir'] && $horaActual <= $rowHorarios['Clahfr'])
			{
				$horarioValido = true;
			}
			else
			{
				$horarioValido = false;
			}
			
			$arrayHorarios[$rowHorarios['Clacod']]['horarioValido'] = $horarioValido;
			$arrayHorarios[$rowHorarios['Clacod']]['descripcion'] = $rowHorarios['Clades'];
			$arrayHorarios[$rowHorarios['Clacod']]['horaInicial'] = $rowHorarios['Clahir'];
			$arrayHorarios[$rowHorarios['Clacod']]['horaFinal'] = $rowHorarios['Clahfr'];
		}
	}
	// echo "<pre>".print_r($arrayHorarios,true)."</pre>";
	return $arrayHorarios;
}
/*===========================================================================================================================================*/
/*=========================================================PROGRAMA==========================================================================*/
if (!isset($user))
{
	if(!isset($_SESSION["user"]))
	session_register("user");
}

if(!isset($_SESSION["user"]))
echo "error";
else
{
	$wacutaliza = "2019-09-30";
	$wbasedato='root';
	
	include_once("root/comun.php");

    $cco_auditoria_corporativa_clinica = consultarAliasPorAplicacion($conex, '01', 'centro_costo_auditoria_corporativa');
    $auditoria_corporativa_titulos     = consultarAliasPorAplicacion($conex, '01', 'auditoria_corporativa_titulos');

    $req_varios_resp = '';
    global $req_varios_resp;

	//consulto los datos del usuario de la sesion
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); //extraigo el codigo del usuario

	/***********************************consulto los datos del usuario de matrix********************/

	//el centro de costos del usuario, esta setiado si ya se ha enviado el formulario
	//si no esta setiado, se consulta el usuario para saber su centro de costos entre otros datos
	//en caso de que este setiado se debe consultar el usuario ya con un centro de costos determinado
	if(!isset($cco))
	{
		$usuario=consultarUsuarioReq($wusuario, '');
	}
	else
	{
		//si esta setiado per, quiere decir que posiblemente se esta ingresando un requerimiento a nombre de otra persona
		//esto pasa cuando el usuario tiene en la tabla 000001 ususup en on
		//en este caso si el codigo de la persona y el del usuario son difernetes
		//indica que el centro de costos es de la persona en nombre de la que se esta haciendo el requerimeinto
		//y no del usuario de matrix, por lo que se debe buscar el usuario de matrix con un cco vacio
		if (!isset($per))
		{
			$usuario=consultarUsuarioReq($wusuario, $cco);

			if (isset($car) and $car!='')
			{
				$usuario['car']=$car;
			}
			if (isset($sede) and $sede!='')
			{
				$usuario['sed']=$sede;
			}
			if (isset($ext) and $ext!='')
			{
				$usuario['ext']=$ext;
			}
			if (isset($email) and $email!='')
			{
				$usuario['ema']=$email;
			}
		}
		else
		{
			$exp=explode('-',$per);
			if ($exp[0]!=$wusuario)
			{
				$usuario=consultarUsuarioReq($wusuario, '');
				if (isset($car) and $car!='')
				{
					$usuario['car']=$car;
				}
				if (isset($sede) and $sede!='')
				{
					$usuario['sed']=$sede;
				}
				if (isset($ext) and $ext!='')
				{
					$usuario['ext']=$ext;
				}
				if (isset($email) and $email!='')
				{
					$usuario['ema']=$email;
				}
			}
			else
			{
				$usuario=consultarUsuarioReq($wusuario, $cco);
			}
		}


	}

  $titulo_requerimientos = "SISTEMA DE REQUERIMIENTOS";
  $expl_cco = explode("-",$usuario["cco"]);


  if(count($expl_cco) > 0 && $expl_cco[0] == $cco_auditoria_corporativa_clinica)
  {
      $titulo_requerimientos = $auditoria_corporativa_titulos;
  }

  //pintarVersion(); //Escribe en el programa el autor y la version del Script.
  pintarTitulo($wacutaliza, $titulo_requerimientos);  //Escribe el titulo de la aplicacion, fecha y hora adicionalmente da el acceso a los scripts consulta.php, seguimiento.php
  //Adicionalmente esta funcione se encarga de abrir la forma del Script que se llama informatica
  //
	/***********************************despliegue incial de los datos del requerimiento********************/

	//$enviar indica que se ha querido ingresar el requerimiento
	//si no se ha seteado enviar o si no se ha seleciconado un centro de costos para el usuario que solicita
	//si no se ha seleccionado el centro de costos para el requerimiento, el tipo de requerimiento o la descripción
	//entoces solo se vuelve a mostrar el formulario con los datos que han sido enviado o por defecto si es la primera
	//vez que se ingresa al formulario
	//si todo esta setiado entonces se pasa a la etapa de grabacion del requerimiento

	if (!isset($enviar) or !isset($cco) or $cco=='' or !isset($ccoreq) or $ccoreq=='' or !isset($tipreq) or $tipreq=='' or !isset($desreq) or $desreq=='' || !isset($ok)) //primera vez que se ingresa al programa o tiene el valor de ser con botones de ingreso de datos
	{
		//muestro una alert si algun dato requerido faltase para que se sepa que no se grabo
		if (isset($enviar) &&  ($cco=='' or $ccoreq=='' or $tipreq=='' or $desreq=='') && isset($ok))
		{
		
			pintarAlert1('!Por favor seleccione el centro de costos del solicitante, al cual realizará la solicitud¡');
			echo '<script type="text/javascript">';
			echo 'window.onload = function() {
				document.getElementById("cco").focus();
				 }';
			echo'</script>';
		}

		//si el usuario de Matrix tiene autorizado grabar requerimientos por otras personas
		//el formulario le da la opcion de buscar a la persona por codigo o por nombre
		//en caso contrario el proceso es mucho mas sencillo
		$centros=consultarCentros($usuario['cod'], $usuario['cco']);
		$sedes  =consultarSedes();

		if ($usuario['sup']=='on')
		{

			//perbus es la variable que
			if (!isset($perbus))
			{
				$personas=consultarPersonas($usuario['cod'], $usuario['nom']);
			
				if(!$centros)
				{
					pintarAlert2('EL USUARIO NO ESTA RELACIONADO CON NINGUN CENTRO DE COSTOS, POR FAVOR COMUNICARSE CON SISTEMAS');
				}
				else
				{
					pintarUsuario($usuario, 'on', $centros, $personas, $sedes, $usuario['sed']);
				}
			}
			else
			{
				if ($perbus!='')
				{
					if ($tip=='Codigo')
					{
						$persona=consultarUsuarioReq($perbus, '');
						if (!$persona)
						{
							$personas=consultarPersonas($usuario['cod'], $usuario['nom']);
						}
						else
						{
							$personas=consultarPersonas($persona['cod'], $persona['nom']);
						}
					}
					else
					{
						$persona=consultarUsuarioXNombre($perbus, $personas);
					}
					if (!$persona)
					{

						if(!$centros)
						{
							//pintarAlert2('EL USUARIO NO ESTA RELACIONADO CON NINGUN CENTRO DE COSTOS, POR FAVOR COMUNICARSE CON SISTEMAS');
							pintarUsuario($usuario, 'on', '', $personas, $sedes, $usuario['sed']);
						}
						else
						{
							pintarUsuario($usuario, 'on', $centros, $personas, $sedes, $usuario['sed']);
						}
					}
					else
					{

						if(!$centros)
						{
							//pintarAlert2('EL USUARIO NO ESTA RELACIONADO CON NINGUN CENTRO DE COSTOS, POR FAVOR COMUNICARSE CON SISTEMAS');
							pintarUsuario($persona, 'on', '', $personas, $sedes, $usuario['sed']);
						}
						else
						{
							pintarUsuario($persona, 'on', $centros, $personas, $sedes, $usuario['sed']);
						}
					}
				}
				else
				{
					if ($per!='')
					{
						$exp=explode('-',$per);
						if (isset($cco))
						{
							$persona=consultarUsuarioReq($exp[0], $cco);
							$centros=consultarCentros($exp[0], $persona['cco']);
						}
						else
						{
							$persona=consultarUsuarioReq($exp[0], '');
							$centros=consultarCentros($exp[0], '');
						}
						$personas=consultarPersonas($exp[0], $exp[1]);

						if (isset($car) and $car!='')
						{
							$persona['car']=$car;
						}
						if (isset($ext) and $ext!='')
						{
							$persona['ext']=$ext;
						}
						if (isset($email) and $email!='')
						{
							$persona['ema']=$email;
						}

						if(!$centros)
						{
							//pintarAlert2('EL USUARIO NO ESTA RELACIONADO CON NINGUN CENTRO DE COSTOS, POR FAVOR COMUNICARSE CON SISTEMAS');
							pintarUsuario($persona, 'on', '', $personas, $sedes, $usuario['sed']);
						}
						else
						{
							pintarUsuario($persona, 'on', $centros, $personas, $sedes, $usuario['sed']);
						}
					}
					else
					{
						$personas=consultarPersonas($usuario['cod'], $usuario['nom']);
						$centros=consultarCentros($usuario['cod'], $usuario['cco']);
						if(!$centros)
						{
							pintarAlert2('EL USUARIO NO ESTA RELACIONADO CON NINGUN CENTRO DE COSTOS, POR FAVOR COMUNICARSE CON SISTEMAS');
						}
						else
						{
							pintarUsuario($usuario, 'on', $centros, $personas, $sedes, $usuario['sed']);
						}
					}
				}
			}
		}
		else
		{
			$centros=consultarCentros($usuario['cod'], $usuario['cco']);
			if(!$centros)
			{
				pintarAlert2('EL USUARIO NO ESTA RELACIONADO CON NINGUN CENTRO DE COSTOS, POR FAVOR COMUNICARSE CON SISTEMAS');
			}
			else
			{
				pintarUsuario($usuario, 'off', $centros, '', $sedes, $usuario['sed']);
			}
		}

		if (isset($cosbus) and $cosbus!='')
		{
			if ($tip2=='Codigo')
			{
				$costo=consultarCostoXCodigo($cosbus, $costos);
			}
			else
			{
				$costo=consultarCostoXNombre($cosbus, $costos);
			}
			if (isset($tipreq))
			{
				unset($tipreq);
			}
		}
		else
		{
			if (isset($ccoreq))
			{
				$costos=consultarCostos($ccoreq);
			}
			else
			{
				$costos=consultarCostos($usuario['cco']);
			}
		}

		if (!isset($tipreq))
		{
			$tipos=consultarTipos($costos[0], $usuario['cod'], '', $vis);
			if (isset($clareq))
			{
				unset($clareq);
			}
		}
		else
		{
			$tipos=consultarTipos($costos[0], $usuario['cod'], $tipreq, $vis);
		}

		$perf=consultarPerfil($usuario['cod'], $costos[0]);

		if ($costos[0]!='' and $perf)
		{
			$prioridades       = consultarPrioridades();
			$estados           = consultarEstados();
			$tipoRequerimiento = consultarTipoRequerimientos();	
			$tipoFalla         = consultarTipoFallas();
			$tipoCanal         = consultarCanales();

			if (!isset($clareq))
			{
				$clases=consultarClases($costos[0], $tipos[0], '');
				$responsables=consultarResponsables($usuario, $costos[0], $tipos[0], $clases[0]);					
				$tiempos=consultarTiempos($clases[0]);
				$porcentaje=consultarPorcentaje($clases[0]);
				if ($porcentaje=='on')
				{
					$porcentaje='0%';
				}
				else
				{
					$porcentaje='';
				}
				$especiales=consultarEspeciales($clases[0], $costos[0], $tipos[0]);
			}
			else
			{
				$clases=consultarClases($costos[0], $tipos[0], $clareq);
				$responsables=consultarResponsables($usuario, $costos[0], $tipos[0], $clases[0]);
				$tiempos=consultarTiempos($clases[0]);
				$porcentaje=consultarPorcentaje($clases[0]);
				if ($porcentaje=='on')
				{
					$porcentaje='0%';
				}
				else
				{
					$porcentaje='';
				}
				$especiales=consultarEspeciales($clases[0], $costos[0], $tipos[0]);
			}
			if (!isset($desreq))
			{
				$desreq='';
			}
		}
		else if($costos[0]!='' and $vis=='on')
		{
            // Si en maestro tipo de requerimientos se le configuró ver las clases y tiene clases en relación tipos de requerimientos y clases
			if (!isset($clareq))
			{
				$clases=consultarClases($costos[0], $tipos[0], '');
				$especiales=consultarEspeciales2($clases[0], $costos[0], $tipos[0]);
			}
			else
			{
				$clases=consultarClases($costos[0], $tipos[0], $clareq);
				$especiales=consultarEspeciales2($clases[0], $costos[0], $tipos[0]);
			}
			if (!isset($desreq))
			{
				$desreq='';
			}

			$responsables='';
			$prioridades='';
			$estados='';
			$tiempos='';
			$porcentaje='';
		}
		else
		{
			$clases='';
			$responsables='';
			$prioridades='';
			$estados='';
			$tiempos='';
			$porcentaje='';
			$especiales='';
			$desreq='';
		}
		$clareq = (!isset($clareq)) ? '': $clareq;
		pintarRequerimiento(0, $costos, $tipos, $clases, $responsables, $prioridades, date('Y-m-d'), date('H:i:s'), $estados, $tiempos, $porcentaje, date('Y-m-d'), date('H:i:s'), $especiales, $desreq, $perf, $clareq, $usuario['cco'], $tipoRequerimiento, $tipoFalla, $tipoCanal);
		
		pintarBoton(0);
	}
	else //se almacena todo el requerimiento y se vuelve a pintar sin opcion de cambio
	{
		//almacenamiento de usuario
        $usuNombre = "";
		if (!isset($per) or $per=='')
		{
			almacenarUsuario($usuario['cod'], $cco, $car, $ext, $email, $sede);
			$usuCod=$usuario['cod'];
            $usuNombre = $usuario['nom'];
		}
		else
		{
			$exp=explode('-', $per);
			almacenarUsuario($exp[0], $cco, $car, $ext, $email, $sede);
			$usuCod=$exp[0];
            $usuNombre = $exp[1];
		}

		$perf=consultarPerfil($usuario['cod'], $ccoreq);

		if (!isset ($clareq))
		{
			$clareq='';
		}
		if (!isset ($temreq))
		{
			$temreq='';
		}
		if (!isset ($resreq))
		{
			$resreq='';
		}
		if (!isset ($fecap))
		{
			$fecap=date('y-m-d');
		}
		if (!isset ($horap))
		{
			$horap=date('H:i:s');
		}
		if (!isset ($porcen))
		{
			$porcen='';
		}
		if (!isset ($fecen))
		{
			$fecen='';
		}
		if (!isset ($horen))
		{
			$horen='';
		}
		if (!isset ($estreq))
		{
			$estreq='01';
		}
		else
		{
			$fecen=adecuarFecha($estreq, $fecen, $horen);
		}

		if (!isset ($prireq))
		{
			$prireq='01-xx';
		}

		if (!isset ($obsreq))
		{
			$obsreq='';
		}


        $exp=explode('-',$ccoreq);
        $msj_exito = '';
        if(is_array($resreq))
        {
            //*****  Si es para más de un reponsable  *****
            foreach ($resreq as $key => $value)
            {
                $resreq = $value;
                $reqnum=almacenarRequerimiento($cco, $usuNombre, $usuCod, $ccoreq, $tipreq, $clareq, $temreq, $resreq, $desreq, $fecap, $horap, $porcen, $fecen, $horen, $estreq, $prireq, $usuario['cod'] , $obsreq, $email, $sede, $tiporeq, $fallreq, $canalreq);
                if (isset($especiales))
                {
                    if($perf)
                    { almacenarEspeciales($ccoreq, $tipreq, $clareq, $especiales, $reqnum, $usuario['cod']); }
                    else
                    { almacenarEspeciales2($ccoreq, $tipreq, $clareq, $especiales, $reqnum, $usuario['cod']); }
                }
                $msj_exito .= 'El requerimiento '.$exp[0].'-'.$reqnum.' ha sido grabado exitosamente.<br><span style="font-size:10px;">Asignado a: '.$value.'</span><br><br>';
            }
        }
        else
        {
            //*****  Si es SOLO PARA UN reponsable  *****
            $reqnum=almacenarRequerimiento($cco, $usuNombre, $usuCod, $ccoreq, $tipreq, $clareq, $temreq, $resreq, $desreq, $fecap, $horap, $porcen, $fecen, $horen, $estreq, $prireq, $usuario['cod'] , $obsreq, $email, $sede, $tiporeq, $fallreq, $canalreq);
            if (isset($especiales))
            {
                if($perf)
                { almacenarEspeciales($ccoreq, $tipreq, $clareq, $especiales, $reqnum, $usuario['cod']); }
                else
                { almacenarEspeciales2($ccoreq, $tipreq, $clareq, $especiales, $reqnum, $usuario['cod']); }
            }
            $exp=explode('-',$ccoreq);
            $msj_exito = 'El requerimiento '.$exp[0].'-'.$reqnum.' ha sido grabado exitosamente';
        }
        pintarAlert3($msj_exito);
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