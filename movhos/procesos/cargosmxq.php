<html>
<head>
<title>CARGOS </title>
<?php
include_once("conex.php"); 
if(isset($historia))
{
	$pac['his']=$historia;
}
if(isset($artcod))
{
	$art['cod']=$artcod;
}

?>


<style type="text/css">    	
   	<!--Fondo Azul no muy oscuro y letra blanca -->
   	.tituloSup{color:#006699;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;font-size:10pt;}
   	.tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;font-size:10pt;}
   	.titulo1{color:#FFFFFF;background:#006699;font-family:Arial;font-weight:bold;text-align:center;font-size:10pt;}
   	.titulo2{color:#003366;background:#57C8D5;font-size:10pt;font-family:Arial;text-align:center;}
   	.titulo3{color:#003366;background:#A4E1E8;font-size:10pt;font-family:Tahoma;text-align:center;}
   	.titulo4{color:#ffffff;background:purple;font-size:10pt;font-family:Tahoma;text-align:center;}
   	.titulo5{color:#003366;background:pink;font-size:10pt;font-family:Arial;text-align:center;}
   	.texto{color:#006699;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
   	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   	.errorTitulo{color:#FF0000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
   	.alert{background:#FFFF00;color:#000000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
   	.warning{background:#FF6600;color:#000000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
   	.error{background:#FF0000;color:#000000;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}    	    	
</style>

</head>
<BODY BGCOLOR="#FFFFFF">
<?php

/**
 * SISTEMA DE GRABACI�N DE CARGOS POR MATRIX
 * 
 * A) Ingresa usuario y se valida:
 *    1) Usuario existe en la tabla usuarios y root_000025
 *    2) El usuario esta asociado a un centro de costos de en la tabla 000011.
 *    Si no se cumple 1) o 2) muestra un error.
 * 
 * B) El centro de costos al que esta asociado el usuario debe cumplir con alguna de las siguientes condiciones:
 *    1) Permite seleccionar centro de costos (000011.Ccosel == "on" <==> $cco['sel']==true).
 *       S� el centro de costos permite celeccionar centro de costos, el programa pide el c�digo del centro de costos precedido por CB.
 *       Al ingresar el centro de costos precedido por CB., el programa valida el formato (CB."cuatro digitos de centro de costo), si el formato es valido 
 *       valida que este en la tabla 000011 y que este habilitado para facturar (000011.Ccofac=="on", como en 2) ).
 *    2) El centro de costos permite facturar 000011.Ccofac=="on" <==> $cco['fac'] .
 * 
 *    Si existe algun problema con el centro de costos o el usuario hay un mensaje de error.
 *  
 *    3) S� el centro de costos solo aplica automaticamente si se carga del centro de costos(000011.Ccoasc=='on' <==> $cco['asc']==true ), 
 *       entonces es necesario pedir la fecha y hora de la ronda de aplicaci�n.
 *       Se validan que la fecha y hora sean validas, se almacenan en $fecApl y en $ronApl, y se pide la historia.
 * 
 * C) Pide la historia y da la posibilidad de se�alar, en un checkbox provisto paratal fin, que el paciente esta en proceso de alta.
 *  
 * 
 * D) Al ingresar la historia:
 *    1) Se valida si hay conexi�n odbc por medio de la funci�n: connectOdbc(&$conex_o, 'inventarios'). 
 *       S� hay conexi�n:
 *       1a) Llama a la funci�n ivartcba para que actualice la tabla de relaci�n de codigos de art�culos de la instituci�n y c�digos de proveedor, s� es que no se ha actualizado en el d�a.
 *       1b) Llama a la funci�n ivart para que actualice la tabla de relaci�n de codigos de art�culos de la instituci�n, s� es que no se ha actualizado en el d�a.
 * 	  2) Se llama la funci�n HistoriaMatrix, que valida si:la historia es cero (0) que el centro de costos este habilitado para trabajar con esta historia, 
 *       y si la historia tiene caracteres alfan�mericos que estos correspondal al prefijo de la historia por MATRIX del centro de costos.
 * 
 * 
 * 
 * 
 * <b>A PARTIR DE AQUI SE DIVIDE LO QUE SE HACE CUANDO NO HAY UNIIX Y LO QUE HACE CUANDO HAY UNIX</b>
 * 
 * <b>CON CONEXI�N ODBC</b>
 * 
 * E) LLama a la funci�n validacionHistoriaUnix en donde se valida si la historia existe en inpac
 *    y de ser as� verifica si esta en las tablas root_000036, root_000037 y 000018, si el paciente, con su ingreso actual, 
 *    s� no esta en alguna de las tablas la funci�n lo registra.
 * 
 *    S� la historia no esta activa en UNIX sale error.
 * 
 * F) Se realizan las validaciones de MATRIX para la historia (ver abajo).
 * 
 * G) S� as validaciones salen bien, entonces el sistema llama a la funci�n actualizacionDetalleRegistros(), esta funci�n es bastante compleja
 *    e importante para el funcionamiento del sistema de cargos, se recomienda leer su documentaci�n para el entendimiento de este programa.
 *    De forma muy escueta se puede decir que su funci�n en este programa consiste en determinar si hay algun problema con un cargo que se haya
 *    hecho a la historia $pac['his'] durante el ingreso $pac['ing'].  El problema sera visible al usuario si en la pantalla el  table data <td>
 *    donde esta la informaci�n del paciente (nombre y habitaci�n) no es azul si no de otro color, dentro del programa esto se logra porque 
 *    actualizacionDetalleregistro() retorna un string que se almacena en $classHis y esta es la variable que se usa como clase del table data
 *    donde se imprime la informaci�n del usuario as� <td class='".."'>, los valores que puede tomar classHis (osea los que puede retornar la funci�n) segun
 *    los problemas encontrados con la historia son:
 *    1) titulo1: Todo esta bien, los registros activos estan en itdro con estado procesado droest="P", o estan en itdro con estado sin procesar (droest="S") pero la fecha corresponde a la actual.
 *    2) alert:   Hay registros de detalle sin procesar (S) y la fecha del encabezado no corresponde a la actual.
 *    3) warning: por lo menos un detalle en M, ni siquiera un encabezado, con un detalle es suficiente.
 *    4) error: por lo menos un encabezado que Fenues=I, es decir hay por lo menos un registro inconsistente en itdro.
 *  
 * H) Pide el c�digo de un art�culo.
 *    S� el centro de costos permite aprovechamiento ($cco['apr']== true, 000011.Ccoapr) ademas del campo para digitar o leer el art�culo
 *    hay un check box para que el usuario se�ale si el art�culo a cargar o devolver es por fuente de aprovechamiento.
 * 
 * I) Se realizan las Validaciones de existencia del art�culo en Matrix para el art�culo (ver abajo "Validaciones en Matrix para el art�culo").
 * 
 * J) Se realizan las valida que el art�culo tenga tarifa y saldo en UNIX, por el llamado a la funci�n tarifaSaldo, s� el centro de costos no permite
 *    negativos ($cco['nce']== true <==> 000011.Cconce=='on' y no es un aprovechamoento ($aprov ==false) o una devoluci�n ($tipTrans == "D") la 
 *    funci�n valida que haya suficiente cantidad de eart�culo en el Cco para realizar el cargo. 
 * 
 * K) Si es una devoluci�n $tipTrans == "D"  se verifica que exists suficiente cantidad para devolver (llamado a la funci�n validacionDevolucion() ):
 *    1) S� aplica autom�ticamente ($cco['apl'] == true <==> 000011.Ccoapl == 'on') se busca saldo en la tabla 000030.
 *    2) S� NO aplica autom�ticamente ($cco['apl'] == false <==> 000011.Ccoapl == 'off') se busca saldo en la tabla 000004.
 *    S� no hay suficiente cantidad saca error.
 * 
 * L) Si es correcto se pide el consecutivo del documento ($dronum)en Matrix y la l�nea de ese documento ($drolin) por medio de un llamado a la funci�n Numeracion().
 * 
 * M) Se registra el cargo en itdro .
 * 
 * N) Se realizan los registros correspondientes en Matrix(ver abajo "Registros de Cargo en Matrix").
 * 
 * �) Se muestra en pantalla dentro del textarea el articulo, con su nombre y cantidad.
 * 
 * O) Se piden mas art�culos.
 * 
 * 
 * 
 * <b>SIN CONEXI�N ODBC</b>
 * 
 * E) LLama a la funci�n infoPaciente(), la cual valida que este en las tablas de paciente de Matrix (root_000036, root_000037, 000018) y trae los datos all� contenidos.
 *    S� el paciente no esta en esas tablas no se les puede cargar art�culos.
 * 
 * F) Se realizan las validaciones de MATRIX para la historia (ver abajo).
 * 
 * G) S� as validaciones salen bien, entonces el sistema pide el c�digo de un art�culo.
 *    S� el centro de costos permite aprovechamiento ($cco['apr']== true, 000011.Ccoapr) ademas del campo para digitar o leer el art�culo
 *    hay un check box para que el usuario se�ale si el art�culo a cargar o devolver es por fuente de aprovechamiento.
 * 
 * H) Se realizan las Validaciones de existencia del art�culo en Matrix para el art�culo (ver abajo "Validaciones en Matrix para el art�culo").
 *  
 * I) Si es una devoluci�n $tipTrans == "D"  se verifica que exists suficiente cantidad para devolver (llamado a la funci�n validacionDevolucion() ):
 *    1) S� aplica autom�ticamente ($cco['apl'] == true <==> 000011.Ccoapl == 'on') se busca saldo en la tabla 000030.
 *    2) S� NO aplica autom�ticamente ($cco['apl'] == false <==> 000011.Ccoapl == 'off') se busca saldo en la tabla 000004.
 *    S� no hay suficiente cantidad saca error.
 * 
 * J) Si es correcto se pide el consecutivo del documento ($dronum)en Matrix y la l�nea de ese documento ($drolin) por medio de un llamado a la funci�n Numeracion(), que adem�s hace el registro del encabezado del cargo (000002) s� es necesario.
 * 
 * K) En este momento el art�culo puedo o no permitir negativos ($art['neg'], 000008.Areneg), pero puede que en el momento que se establesca la conexi�n con UNIX
 *    y sea momento de verificar el saldo ya no este este par�metro igual.  Eso hace necesario almacenar temporalmente el par�metro, el programa lo hace de la siguiente forma:
 * 	  1) Si el programa NO permite negativos guarda en la variable $art['ari']="N*".$art['ari'], esta variable ser� almacenada en 000003.Fdeari.
 * 	  2) Si el programa Permite negativos guarda en la variable $art['ari']="P*".$art['ari'], esta variable ser� almacenada en 000003.Fdeari.
 *  
 * L) Se realizan los registros correspondientes en Matrix(ver abajo "Registros de Cargo en Matrix").
 * 
 * M) Se muestra en pantalla dentro del textarea el articulo, con su nombre y cantidad.
 * 
 * N) Se piden mas art�culos.
 * 
 *   
 *  
 * <b>VALIDACIONES DE MATRIX PARA LA HISTORIA</b>
 * 
 * A) El paciente no tiene alta definitiva $pac['ald']==false <==> 000018.Ubiald=='off'
 * B) El paciente no esta en proceso de traslado $pac['ptr'] ==false <==> 000018.Ubiptr=='off'
 * C) El paciente no esta en proceso de alta $pac['alp']==false <==> 000018.Ubialp=='off' o si esta en proceso de alta el usuario activo el checkbox que informa que el sabe que esta enproceso de alta $pac['ald']==true <==> 000018.Ubiald=='on' and $alp==true.
 * D) S� el centro de costos es hospitalario ($cco['hos'] == true <==> 000011.Ccohos ==true, el paciente esta ubicado en ese centro de costos $pac['ubi'] == $cco['cod'] <==> 000018.Ubisac==$cco['cod'].
 * 
 * S� el paciente no cumple con alguna de las anteriores no se le pueden hacer cargos o devoluciones. 
 * 
 * <b>VALIDACIONES DE MATRIX PARA EL ART�CULO</b>
 * 
 * A) Se guarda en $art['ari'] el c�digo inicial digitado o leido por el usuario.
 * B) Se llama a la funci�n BARCOD() para que recorte el c�digo como es debido si supera los 14 caracteres.
 * C) Se llama a la funci�n ArticuloCba() por si es un c�digo de proveedor traiga en $art['cod'] el c�digo propio de la cl�nica.
 * D) Se llama la funci�n de art�culos articulosEspeiales de este modo:
 *    1) S� el c�digo es un c�digo especial retorna el codigo de la clinica, es decir 000008.Areces=$art['cod'] entonces $art['cod']=000008.Arecod.
 * 	  2) Se llenan las variables:
 * 	     2a) $art['var'] si el art�culo tiene cantidad variable.
 *       2b) $art['can'] cantidad por defecto.
 *       2c) $art['max'] Cantidad m�xima.
 *       2d) $art['neg'] permite negativos.
 *       Problema potencial: se permiten cargas fracciones por cantidad variable y no hay modo de determinar que art�culos si lo deben permitir y cuales no.
 * E) Se llama a la funci�n articuloExiste() para verificar el estado del art�culo y recuperar el nombre($art['nom']), grupo($art['gru']) y unidad de medida ($art['uni']).
 * 
 * S� el art�culo es de cantidad variable ($art['cva'] == true) no hace ninguna otra validaci�n, si no que procesde a:
 * F) Pedir cantidad para hacer el cargo.
 * G) Validar que la cantidad no sea mayor a $art['max']
 * H) Validar que no sea menor que cero.
 * Si  alguna de las dos validaciones sale mal vuelve a pedir cantidad hasta que el usuario digite una cantidad v�lida.
 * 
 * 
 * 
 * <b>REGISTROS DE CARGO EN MATRIX</b>
 * 
 * A) Registrar el detalle de cargo en la tabla 000003 por medio del llamado a la funci�n registrarDetalleCargo.
 * B) Pasan cosas diferentes dependiendo de las caracteristicas del centro de costos:
 *    1) El centro de costos aplica ($cco['apl'] == true <==> 000011.Ccoapl =='on')
 * 		 1a) Se llama a la funci�n registrarSaldosAplicacion(), lo que mofifica el saldo de aplicaci�n en la tabla 000030.
 *       1b) Se registra la aplicaci�n en la tabla 000015 con la fecha actual como fecha de la ronda (000015.Aplfec) y la hora actual como hora de la ronda (000015.Aplron), por medio de la funcion registrarAplicacion.
 *    2) El centro de costos NO aplica ($cco['apl'] == false <==> 000011.Ccoapl =='off')
 *       2a) Se llama a la funci�n registrarSaldosNoApl() , que modifica los saldos sencillos que estan en la tabla 000004.
 *       S� es un un cargo de grabaci�n ($tipTrans == 'C'), y el centro de costos aplica solo cuando se carga desde ahi ($cco['asc'] == true <==>000011.Ccoasc = 'on').
 *       2b) Se llama nuevamente a la funci�n registrarSaldosNoApl() para aque haga una salida que va a corresponder a la aplicaci�n que se efectuara en 2c), es decir se env�a como parametro de tipo de transacci�n "D" de devoluci�n para que sea una salida.
 *       2c) Efectuar una aplicaci�n en la tabla 000015, con ronda de aplicaci�n $ronApl y fecha de ronda $fecApl, a trav�s d ela funci�n registrarAplicacion().
 * 
 * 
 * 
 * <b>MANEJO DE ERRORES</b>
 * 
 * El programa maneja los errores por medio del arreglo $error, este se env�a a las diferentes dfunciones y estas, de haber un error, retornan el mensaje as�:
 *  $error[ok]:Descripci�n corta del error.
 *  $error[codInt]String[4]:C�digo del error interno, debe corresponder a alguno de la tabla 000010.
 *  $error[codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.
 *  $error[descSis]:Descripci�n del error del sistema.
 * 
 * La funci�n registrarError() se llama enviando como par�metro el arreglo, con esto se registra el error en la tabla 000005 y seeobtiene la clase "class" que se mostrara en la pantalla. 
 * Es decir, cuando hay un error la tabla debe cambiar de color para indicarselo al usuario, ese cambio de color esta dictado por el class del <td>, lo que se obtiene
 * es el valor de class para el <td>
 * 
 * @modified 2009-02-09 Se permite que un centro de costo hospitalario le grabe a otro, esto se valida en funcion buscar_si_puede_grabar_a_otro_cco, que se le envia el cco que graba y el cco donde esta el paciente y valida que se le pueda grabar, segun la tabla _000058.
 * @modified 2008-02-02 Color rosado para devoluciones
 * @modified 2007-11-14 Se evita grabacion de ceros y se avisa si la factura ya se tiro
 * @modified 2007-11-09 Se quita indicador de carro de dispensacion
 * @modified 2007-11-06 Indicador de carro de dispensacion
 * @modified 2007-09-27 Se hace la conexi�n odbc a facturaci�n antes de llamar a ValidacionHistoriaUnix
 * @modified 2007-09-27 estaba ignorando la regla de aplicar cuando el centro de costos donde esta el paciente aplica por que no se llenaba el arreglo del centro de costos del paciente antes de llamar a getCco() de forma adecuada ($ccoPac=$pac['sac'];), se soluciona para que si lo haga bien ($ccoPac['cod']=$pac['sac'];).
 * @modified 2007-09-27 se cambian los terminos de verificaci�n del paciente por fuera de unix, si infoPaciente() retorna false es por que el paciente no ha sido admitido, por lo cual no se debe dejar hacer cargos pues puede presentar conflictos con los centros de costos.
 * @modified 2007-09-26 Se hace necesario pedir los minutos de la ronda, as� que se modifica el programa para pedir los minutos de la ronda, y construir $ronApl con los minutos incluidos.
 * @modified 2007-09-23 Desaparece la variable $art['apl'] por que no se necesita su uso.
 * @modified 2007-09-18 Desaparece el arrglo $trans por cambios del 2007-09-17 de la funci�n registrarAplicaicon(), en donde ya recibe individualmente el n�mero y la linea de la transacci�n y ya no hay descarte.
 * @modified 2007-09-18 Se camba $usu['codM'] por $usuario
 * @modified 2007-09-18 De ahora en adelante se van a realizar 2 movimientos simultaneos, se hace una entrada a los saldos normales a trav�s de registrarSaldosNoApl(), y una aplicaci�n que corresponde a un registro de aplicaci�n por medio de registrarAplicaci�n y una salidada de los saldos a trav�s de registrarSladosNoApl().  Eso ocurre cuando $cco['asc']== true and $cco['apl']==false and $tipTrans == "C", es decir que el centro de costos aplique lo que carga a sus pacientes y no sea de aplicaci�n autom�tica, y sea un cargo No una devoluci�n.
 * @modified 2007-09-17 Dada la creaci�n de las nuevas funciones y tablas para saldo de paciente, una para el saldo normal (tabla:000004 funcion:registrarSaldoNoApl) y otra para el saldo de aplicaci�n (tabla:000030, funci�n:registrarSaldoAplicacion), see hacen cambios para que use las funciones adecuadamente.
 * @modified 2007-09-12 Se empiezan a usar las variable $fecApl y $ronApl dentro del programa pra enviarlas al registrar Aplicacion y para enviarlas por hidden.
 * @modified 2007-09-11 Cuando el centro de costos aplica autom�ticamente el material que se carga all� ($cco['asc']==true -Aplica Solo en el Centro de costos-), es necesario pedir la fecha y la hora de la ronda de  los art�culos que se van a cargar, NO APLICA PARA DEVOLUCIONES!!!!!
 * @modified 2007-09-11 Se agrga la variable $cco['asc'] que dice si el centro de costos de donde se esta grabando aplica autom�ticamente los art�culos que esta grabando, mas NO los art�culos que cargan desde otros centros de costos a los pacientes que all� se encuentran.
 * @modified 2007-09-04 Cambio la funci�n Art�culo Cba en fxValidacionArticulo.php, por lo tanto se quita $cco de los par�metros de la funci�n ArticuloCba.
 * @modified 2007-09-04 En la secci�n en donde $conex_o==0, es decir que no hay conexi�n con UNIx se modifica se borra la l�nea $pac['ing']=0 que existia en donde despues de llamar a la funci�n infoPaciente, por que en la varsi�n de latas cuando no hay unix los registros deben quedar con el ingreso que trae esta funci�n.
 * @modified 2007-09-04 Se empieza a usar la variable $art['apl'], la cual indica si el art�culo aplica, se usa parasaber si es necesario llamar a la funci�n registrarAplicacion, y ademas se crea el hidden que la env�a a la siguiente pantalla cuando el art�culo tiene cantida vriable.
 * @modified 2007-09-03 Se deja de usar la funci�n pacienteDeAltapara tanto normal como por fuera de UNIX para saber si el paciente esta de alta, y se hacen las validaciones con $pac['alp'] para saber si el alta esta en proceso de alta, $pac['ald'] para saber si ya se le dio el alta definitiva, y $pac['alp'] para saber si esta en proceso de traslado.
 * @modified 2007-08-15 Secrea la variable $cco['apr'], que determina si el centro de costos tiene derecho a cargar aprovechamientos.
 * @modified 2007-08-03 Si se aplica autom�ticamente ya no depende del centro de costo del que se esta grabando si no adicionalmente del centro de costos en donde esta el paciente.
 * @modified 2007-07-04 Se pone el $pac['his']=ltrim(rtrim($pac['his'])) para que cuando se lee por c�digo de barras que generalmente a�ade un espacio no haya problemas.
 * @modified 2007-06-19 Se modifica la parte del paciente para cuando no hay UNIX, se sube la parte den donde se llenan los datos del paciente para encima de llamar a la funci�n de alta.
 * @modified 2007-06-18 Se comentan los hidden de cco['phm'] prefijo por matrix, y $cco['hcr'], pues no son necesarios si no cuando se va a validar la historia y eso se hace una sola vez.
 * @modified 2007-06-18 Se incluye un if en el hidden de $cco['apl'] pues se estaba enviando directamente el booleano y esto produce errores.
 * @modified 2007-06-18 Se incluye un if en el hidden de $cco['neg'] pues se estaba enviando directamente el booleano y esto produce errores.
 * @modified 2007-06-17 Se cambian los $cco['des'], por $cco['cod']
 * @modified 2007-06-15 Se cambian los par�metros de entrada de modo que sea posible leer el c�digo de un centro de costos cuando elEl centro de costos a elegir permite selecci�n.
 * @modified 2007-06-15 Se realiza el cambio para validar cuando NO hay UNIX que si el apciente esta en proceso de Alta solo pueda hacer cargos si el usuario selecci�no el checbox con nombre 'apl'
 * @modified 2007-06-15 Se realiza el cambio para validar cuando NO hay UNIX que si el centro de costos es hospitalario, este sea el mismo donde el paciente se encuantra actualmente seg�n la tabla 000018
 * @modified 2007-06-14 Se modifica para que cuando pide la historia pregunte si el paciente esta en proceso de alta
 * @modified 2007-06-14 Se realiza el cambio para validar cuando hay UNIX que si el apciente esta en proceso de Alta solo pueda hacer cargos si el usuario selecci�no el checbox con nombre 'apl'
 * @modified 2007-06-14 Se realiza el cambio para validar cuando hay UNIX que si el centro de costos es hospitalario, este sea el mismo donde el paciente se encuantra actualmente seg�n la tabla 000018
 * 	
 * @wvar String[10] $fecApl Fecha de aplicaci�n si $cco['asc']==true.
 * @wvar String[8]	$ronApl ronda de aplicaci�n si $cco['asc']==true.
 */

/*
 * FUNCIONES
 */
/**
 * Consulta si el centro de costos seleccionado es urgencias, a traves del campo ccoUrg
 *
 * @param unknown_type $servicio
 */
function esUrgencias($servicio){

	global $conex;
	global $bd;
	
	$es = false;

	$q = "SELECT 
				Ccourg 
		 	FROM 
		 		".$bd."_000011
			WHERE 
				Ccocod = '".$servicio."' 
			";
	
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	
	if($num>0)
	{
		$rs = mysql_fetch_array($err);
		
		($rs['Ccourg'] == 'on') ? $es = true : $es = false;
	}
	
	return $es;
}

function pacienteIngresado($historiaClinica, $ingresoHistoriaClinica){
	global $conex;
	global $bd;
	
	$es = false;

	$q = "SELECT 
				*
		 	FROM 
		 		".$bd."_000018
			WHERE 
				Ubihis = '".$historiaClinica."'  
				AND Ubiing   = '".$ingresoHistoriaClinica."' 
			";
	
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	
	if($num>0)
	{
		$es = true;
	} 
	
	return $es;
}


function buscar_si_puede_grabar_a_otro_cco($ccoori,$ccodes,&$wexiste_rel){
	global $conex;
	global $bd;
	
	$es = false;

	$q = "SELECT * "
		." 	FROM ".$bd."_000058 "
		." WHERE Ccoori = '".$ccoori."'"  
		."   AND Ccodes = '".$ccodes."'"
		."   AND Ccoest = 'on' " ;
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	
	if($num>0)
	  $wexiste_rel = 'on';
	 else
	    $wexiste_rel = 'off'; 
}


function pacienteConMovimientoHospitalario($historiaClinica, $ingresoHistoriaClinica){

	global $conex;
	global $bd;
	
	$es = false;

	$q = "SELECT 
				*
		 	FROM 
		 		".$bd."_000017
			WHERE 
				Eyrhis = '".$historiaClinica."'  
				AND Eyring = '".$ingresoHistoriaClinica."'
				AND Eyrest = 'on' 
			";
	
//	echo $q;
	
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	
	if($num>0)
	{
		$es = true;
	} 
	
	return $es;
}

function consultarCcoUrgencias(){
	global $bd;
	global $conex;
	
	$q = "SELECT
			Ccocod 
		FROM 
			".$bd."_000011 
		WHERE
			Ccourg = 'on'; ";

//	echo $q;
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);
	
	$cco = "";
	
	if($filas > 0){
		$fila = mysql_fetch_row($res);
		
		$cco = $fila[0];
	}
	return $cco;
}

include_once("movhos/validacion_hist.php");
include_once("movhos/fxValidacionArticulo.php");
include_once("movhos/registro_tablas.php");
include_once("movhos/otros.php");
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

echo "<center><table border='0' width='270'>";

if (consultarhorario($horario,$emp))
{
if($tipTrans == "D")
{
	echo "<tr><td align=center class='errorTitulo'>DEVOLUCI�N</td></tr>";
	$class='titulo5';
}
else 
{
	$class='titulo2';
}
?>		<form name="carga" action="" method=post>		<?php

/*
 * 2008-09-22:: Modificacion cuando es de urgencias.
 */
$ccoUrgencias = consultarCcoUrgencias();

if (isset($user) and !isset($usuario)){
	$usuario=substr($user,1);
}

if(!isset($usuario) )
{
	echo "<tr><td class='titulo1'>C�DIGO NOMINA: </font>";
	?>	<input type='text' cols='10' name='usuario'></td></tr>	
	<script language="JAVASCRIPT" type="text/javascript">
	document.carga.usuario.focus();
	</script><?php
	echo"<tr><td align=center class='".$class."'><input type='submit' value='ACEPTAR'></td></tr></form>";
}
elseif (!isset($pac['his']))
{
	if(!isset($ccoCod))
	{
		//El carnet de la cl�nica tiene un espacio, se recorta
		if(strlen($usuario) ==6 or strlen($usuario)==8)
		{
			$usuario=substr($usuario, 1);
		}

		//Busqueda del centro de costos al que pertenece el usuario.

		$q="SELECT Cc "
		."FROM 	root_000025 "
		."WHERE	Empleado = '".$usuario."' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num >0)
		{
			$row=mysql_fetch_array($err);
			$cco['cod']=$row['Cc'];
			$ok=getCco(&$cco,$tipTrans,$emp);
			echo "<input type='hidden' name ='cco[cod]' value='".$cco['cod']."' >";
		}
		else
		{
			/*No esta el CC en Matrix*/

			$pac['his']='0';
			$art['cod']="NO APLICA";
			$error['codInt']="0002";
			$cco['cod']='0000';
			if($err == "")
			{
				$error['codSis']=mysql_errno($conex);
				$error['descSis']=str_replace("'","*",mysql_error($conex));
			}
			else
			{
				$error['codSis']=$err;
				$error['descSis']=$err;
			}

			//registrarError("NO INFO", $cco, 0, 0, $pac, $art, $error, &$color, &$warning);
			registrarError('NO INFO',$cco,'NO INFO','0', '0',$pac,$art,$error, &$color,$warning,$usuario);
			$printError="<CENTER>EL CODIGO DE USUARIO NO EXISTE";
			$ok=false;
		}
	}
	else
	{
		//Determinar que el centro de costos haya sido leido.
		$pos=strpos(strtoupper($ccoCod),"UN.");
		if($pos === 0)//Tiene que ser triple igual por que si no no funciona
		{
			$cco['cod']=substr($ccoCod,3);
			if(!getCco(&$cco,$tipTrans,$emp))
			{
				$printError="EL CENTRO DE COSTOS NO EXISTE O NO ESTA HABILITADO PARA REALIZAR CARGOS";
				$ok=false;
			}
			else
			{
				$cco['sel']=false;
				$ok=true;
			}
		}
		else
		{
			$printError="EL CENTRO DE COSTOS NO FUE LEIDO POR EL CODIGO DE BARRAS ADECUADO";
			$ok=false;
		}
	}

	if($ok)
	{
		if($cco['sel'])
		{
			echo "<tr><td class='titulo1'>USUARIO: ".$usuario;
			echo "<tr><td class='titulo1' ><b>CC: ";
			?>	<input type='password' size='7' name='ccoCod' onload=''>	
			<script language="JAVASCRIPT" type="text/javascript">
			document.carga.ccoCod.focus();
			</script>
			<?php
			echo "</td></tr>";
		}
		else
		{
			echo "<tr><td class='tituloSup'>".$cco['cod']."-".$cco['nom']."</td></tr>";
			echo "<tr><td class='tituloSup'>USUARIO: ".$usuario."</b>";
			if($cco['asc'] AND $tipTrans == 'C')
			{
				if(isset($year1))
				{
					/**
					 * Revisar fecha de la ronda
					 */
					if(substr($month1,0,1)==0)
					$month=substr($month1,1,1);
					else
					$month=$month1;

					if(substr($day1,0,1)==0)
					$day=substr($day1,1,1);
					else
					$day=$day1;


					if(checkdate($month, $day, $year1))
					{
						$fecApl=$year1."-".$month1."-".$day1;
						$ronApl=$horRonApl.":".$minApl." - ".$merApl;
						echo "<input type='hidden' name='fecApl' value='".$fecApl."'>";
						echo "<input type='hidden' name='year1' value='".$year1."'>";
						echo "<input type='hidden' name='month1' value='".$month1."'>";
						echo "<input type='hidden' name='day1' value='".$day1."'>";
						echo "<input type='hidden' name='ronApl' value='".$ronApl."'>";
						echo "<tr><td class='tituloSup'>Aplicaci�n ".$fecApl." ".str_replace(" ", "",$ronApl)."</b>";
						$preguntarHis=true;
						$preguntarFechaYRonda=false;
					}
					else
					{
						$ok=false;
						$preguntarFechaYRonda=true;
						$preguntarHis=false;
					}
				}
				else
				{
					$year1=date('Y');
					$month1=date('m');
					$day1=date('d');
					$preguntarFechaYRonda=true;
					$preguntarHis=false;
				}

				if($preguntarFechaYRonda )
				{
					echo "<tr>";
					echo "<td class='".$class."' ><b>Fecha: ";
					echo " <select name='year1' >";
					for($f=2004;$f<2051;$f++)
					{
						if($f == $year1)
						echo "<option selected>".$f."</option>";
						else
						echo "<option>".$f."</option>";
					}
					echo "</select> <select name='month1' >";
					for($f=1;$f<13;$f++)
					{
						if( $f == $month1)
						if($f < 10)
						echo "<option selected>0".$f."</option>";
						else
						echo "<option selected>".$f."</option>";
						else
						if($f < 10)
						echo "<option>0".$f."</option>";
						else
						echo "<option>".$f."</option>";
					}
					echo "</select> <select name='day1'>";
					for($f=1;$f<32;$f++) {
						if($f == $day1)
						if($f < 10)
						echo "<option selected>0".$f."</option>";
						else
						echo "<option selected>".$f."</option>";
						else
						if($f < 10)
						echo "<option>0".$f."</option>";
						else
						echo "<option>".$f."</option>";
					}
					echo "</select>";


					echo "<tr>";
					echo "<td class='".$class."' ><b>Ronda: <select name='horRonApl'>";
					$hora = (string)date("G");
					for($i=1;$i<24;$i++)
					{
						if($i==$hora)
						{
							echo "<option selected>".$hora."</option>";
						}
						else
						{
							echo "<option>".$i."</option>";
						}
					}
					echo "</select>";


					echo ":<select name='minApl'>";
					echo "<option selected>00</option>";
					for($i=1;$i<10;$i++)
					{
						echo "<option>0".$i."</option>";
					}
					for($i=10;$i<61;$i++)
					{
						echo "<option>".$i."</option>";
					}
					echo "</select>";
					echo "&nbsp;<select name='merApl'>";
					$meridiano = (string)date("A");
					echo "<option selected>".$meridiano."</option>";
					if($meridiano == "PM")
					{
						echo "<option>AM</option>";
					}
					else
					{
						echo "<option>PM</option>";
					}
					echo "</td></tr>";
					echo "</td></tr>";
				}
				echo "<input type='hidden' name='cco[cod]' value='".$cco['cod']."'>";
				echo "<input type='hidden' name='ccoCod' value='".$ccoCod."'>";
				echo "<input type='hidden' name='cco[asc]' value='1'>";
				//echo "<input type='hidden' name='cco[sel]' value=''>";
			}
			else
			{
				$preguntarHis=true;
			}

			if($preguntarHis)
			{

				echo "<tr>";
				echo "<td class='".$class."' ><b>N� HISTORIA: ";
				?>	<input type='text' size='9' name='historia'>	
				<script language="JAVASCRIPT" type="text/javascript">
				document.carga.historia.focus();
				</script><?php

				/*if(!$cco['hos'] and $tipTrans='C')
				{
				echo "</td></tr>";
				echo "<td class='titulo2' ><b>Carro: <input type='checkbox' name='car'></td></tr>";
				}*/
				echo "</td></tr>";
				echo "<td class='".$class."' ><b>Alta en Proceso: <input type='checkbox' name='alp'></td></tr>";
				echo "<input type='hidden' name='cco[cod]' value='".$cco['cod']."'>";
				/*FIN::::PANTALLA DE INGRESO DE nUMERO DE HISTORIA*/
			}
		}
		echo "<input type='hidden' name ='usuario' value='".$usuario."' >";
		echo"<tr><td  class='".$class."'><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	{
		/*No esta el CC en Matrix*/

		$pac['his']='0';
		$art['cod']="NO APLICA";
		$error['codInt']="0002";
		$cco['cod']='0000';
		if(isset($err))
		{
			if($err == "")
			{
				$error['codSis']=mysql_errno($conex);
				$error['descSis']=str_replace("'","*",mysql_error($conex));
			}
			else
			{
				$error['codSis']=$err;
				$error['descSis']=$err;
			}
		}
		else
		{
			$error['codSis']='.';
			$error['descSis']='.';
		}
		//registrarError("NO INFO", $cco, 0, 0, $pac, $art, $error, &$color, &$warning);
		registrarError('NO INFO',$cco,'NO INFO','0', '0',$pac,$art,$error, &$color,$warning,$usuario);
		echo "<tr><td class='errorTitulo'>".$printError;
		echo "</td></tr>";
		echo "<tr><td class='tituloSup'>";
		ECHO "<BR/><B><A HREF='cargosmxq.php?tipTrans=".$tipTrans."&emp=".$emp."&bd=".$bd."'>Retornar</a>";
		echo "</td></tr></table>";
	}
}else{

	connectOdbc(&$conex_o, 'inventarios');


	if(!isset($cco['fap'])){
		/*Busqueda de los datos del Centro de Costos*/
		getCco(&$cco,$tipTrans, $emp);
	}

	if(isset($aprov))
	{
		$fuente=$cco['fap'];
		$aprov=true;
		if(isset($aprovEstadoAnterior) )
		{
			if($aprovEstadoAnterior == 0)
			$dronum = "";
		}
		else
		{
			$dronum = "";
		}
	}
	else
	{
		$fuente=$cco['fue'];
		$aprov=false;
		if(isset($aprovEstadoAnterior) )
		{
			if($aprovEstadoAnterior  == 1)
			$dronum = "";
		}
		else
		{
			$dronum = "";
		}
	}

	if($conex_o != 0)
	{
		$odbc="ACTIVO";
		if(!isset($pac['act']))
		{
			ivartCba($usuario);
			ivart($usuario);
			$pac['his']=ltrim(rtrim($pac['his']));
			$ind=0;
			while ($ind==0)
			{
				if (substr($pac['his'],0,1)=='0')
				{
					$pac['his']=substr($pac['his'],1);
				}
				else
				{
					$ind=1;
				}
			}
			$pac['act']=HistoriaMatrix($cco, &$pac, &$warning, &$error);

			if($pac['act'] )
			{
				/*Si el nombre no esta setiado es por que no es historia Cero o una historia por MATRIX*/
				$conex_f = odbc_connect('facturacion','','');
				$pac['act']=ValidacionHistoriaUnix(&$pac, &$warning, &$error);
				odbc_close($conex_f);			
				if($pac['act'])
				{
					/**
					 * Si el paciente esta de alta la funci�n retornara true, lo que significa que hay que inactivar el paciente
					 * pues si esta de alta no le pueden cargar art�culos.
					 */
					if(!$pac['ald'])
					{
						/*
						 * 2008-09-22:: Si el paciente esta en urgencias, tiene movimiento hospitalario (registro activo en la tabla 17) y 
						 * adem�s se encuentra ingresado en la 18, se permite el proceso de cargos normal.
						 * Se usa flag "entrar" para permitir que la aplicaci�n ingrese bajo esta nueva condicion.
						 */
						$entrar = false;
						if(isset($pac['ing'])){	
							if($pac['sac'] == $ccoUrgencias && pacienteConMovimientoHospitalario($pac['his'],$pac['ing']) && pacienteIngresado($pac['his'],$pac['ing'])){
								$entrar = true;
							}
						}
						
						if(!$pac['ptr'] or $entrar)
						{
							//inicio de la modificaci�n 2007-06-14
							if($pac['alp'])
							{
								//busco si ya si tiro la factura
								$q1 = "SELECT * "
								."       FROM ".$bd."_000022 "
								."      WHERE Cuehis = '".$pac['his']."' "
								."        AND Cueing = '".$pac['ing']."' "
								."        AND Cuegen = 'on' ";
								$err1=mysql_query($q1,$conex);
								
								echo mysql_error();
								$gene=mysql_num_rows($err1);

								//El paciente esta en proceso de Alta
								if(!isset($alp))
								{
									//El usuario no selecciono el check box de alta en proceso.  No puede realizar cargos.
									$pac['act']=false;
									$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." ESTA EN PROCESO DE ALTA";
									if($gene>0)
									{
										$warning = $warning. " Y LA FACTURA DEL PACIENTE YA HA SIDO GENERADA ";
									}
									$error['codInt']='0011';
									$error['codSis']=".";
									$error['descSis']=".";
									$error['clas']="#ff0000";
									$error['ok']="NO PASO, PACIENTE EN PROCESO DE ALTA ";
								}
								else
								{
									if($gene>0)
									{
										$factura='LA FACTURA DEL PACIENTE YA HA SIDO GENEREDA';
									}
								}
								
								//Msanchez-> 2008-10-22:: Si la cuenta en proceso de facturaci�n, no puede grabar cargos.
								$q1 = "SELECT 
										* 
									FROM 
										".$bd."_000022 
									WHERE 
										Cuehis = '".$pac['his']."' 
										AND Cueing = '".$pac['ing']."' 
										AND Cuecok = 'on'
										AND Cuegen = 'off' ";
								
								$err1=mysql_query($q1,$conex);
								
								echo mysql_error();
								$cuentasEnProcesoFacturacion = mysql_num_rows($err1);
								
								if($cuentasEnProcesoFacturacion > 0)
								{
										$pac['act']=false;
										$warning = "LA CUENTA SE ENCUENTRA EN PROCESO DE FACTURACION, NO PUEDE GRABAR CARGOS.  FAVOR COMUNICARSE CON EL FACTURADOR";
										$error['codInt']='0007';
										$error['codSis']=".";
										$error['descSis']=".";
										$error['clas']="#ff0000";
										$error['ok']="NO PASO, PACIENTE EN PROCESO DE FACTURACION";
								}
								//2008-10-22:: Fin cambio 
							}

							if($pac['act'])
							{
								if($cco['hos'] )
								{
									if($cco['cod'] != $pac['sac'])
									{
										buscar_si_puede_grabar_a_otro_cco($cco['cod'],$pac['sac'],&$wexiste_rel);  //Mando el cco origen y el cco del paciente (destino) y busco en la tabla 
										   
										if ($wexiste_rel=="off")	
										   {   
										    //El usuario no selecciono el check box de alta en proceso.  No puede realizar cargos.
											$pac['act']=false;
											$warning = "EL CENTRO DE COSTOS ".$cco['cod']." ES HOSPITALARIO<br> Y POR LO TANTO NO PUEDE HACERLE CARGOS O DEVOLUCIONES AL PACIENTE ".$pac['his']."<br>PUES ESTE SE ENCUENTRA EN EL CENTRO DE COSTOS ".$pac['sac'];
											$error['codInt']='0012';
											$error['codSis']=".";
											$error['descSis']=".";
											$error['clas']="#ff0000";
											$error['ok']="NO PASO, PACIENTE EN PROCESO DE ALTA ";
									       }	
									}
								}
								//Fin de la modificaci�n 2007-06-14
								if($pac['act'])
								{
									$classHis=actualizacionDetalleRegistros($pac,&$array);
									unset($array);
								}
							}
						}
						else
						{
							$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." <BR> ESTA EN PROCESO DE TRASLADO, POR LO TANTO <BR> NO SE LE PUEDE NI CARGAR NI DEVOLVER <BR> NINGUN ART�CULO!!!";
							$error['codInt']='0010';
							$error['codSis']=".";
							$error['descSis']=".";
							$error['clas']="#ff0000";
							$error['ok']="NO PASO, PACIENTE EN TRASLADO";
							$pac['act']=false;
						}
					}
					else
					{
						$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." FUE DADO DE ALTA DE LA INSTITUCI�N!!!";
						$error['codInt']='0009';
						$error['codSis']=".";
						$error['descSis']=".";
						$error['clas']="#ff0000";
						$error['ok']="NO PASO, PACIENTE DE ALTA";
						$pac['act']=false;
					}
				}
				odbc_close($conex_f);
				odbc_close_all();
			}
			else
			{
				//2007-07-16
				$classHis="titulo1";
			}

			if($pac['act'])
			{
				/**
				 * El paciente esta activo
				 * Es necesario saber si el centro de costos en donde esta matriculado el paciente aplica AUTOM�TICAMENTE
				 */				
				$ccoPac['cod']=$pac['sac'];
				if(getCco(&$ccoPac, $tipTrans, $emp))
				{
					if($ccoPac['apl'])
					{
						$cco['apl']=true;
					}

					if($ccoPac['urg'] and $cco['cod']!=$ccoPac['cod'])
					{
						$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." NO HA SIDO TRASLADADO DEL SERVICIO DE URGENCIAS!!!";
						$error['codInt']='0010';
						$error['codSis']=".";
						$error['descSis']=".";
						$error['clas']="#ff0000";
						$error['ok']="NO PASO, PACIENTE EN TRASLADO";
						$pac['act']=false;
					}
				}
				else
				{
					$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." NO HA SIDO TRASLADADO DEL SERVICIO DE ADMISIONES!!!";
					$error['codInt']='0010';
					$error['codSis']=".";
					$error['descSis']=".";
					$error['clas']="#ff0000";
					$error['ok']="NO PASO, PACIENTE EN TRASLADO";
					$pac['act']=false;
				}
			}
		}

		if ($pac['act'])
		{
			if ($cco['apl'])
			{
				$color="titulo4";
			}
			else
			{
				$color="titulo2";
			}

			$warning="";
			if(isset($art['cod']))
			{
				/*Validaci�n Art�culo*/
				/*Buscar si existe el articulo y el codigo es el del proveedor*/
				if(!isset($art['ini'])) {
					$art['ini']=$art['cod'];
				}
				$art['cod']=BARCOD($art['cod']);

				if(!isset($artValido)) {
					/*Si esta setiado es por que es una cantidad variable*/
					ArticuloCba(&$art);
					ArticulosEspeciales($cco, &$art);
					$artValido = ArticuloExiste(&$art, &$error);
				}

				if($artValido)
				{
					/*Si esta set artgen implica que el codigo existe en el sistema Ademas:
					isset $pacnom and $pacnom != 0 osea que la historia esta activa
					$cc != "" es decir el centro de costos existe*/
					if(!$art['cva'])
					{
						if($art['can'] <= $art['max'])
						{
							if($art['can'] >0)
							{
								$tarSal=TarifaSaldo($art, $cco,$tipTrans,$aprov, &$error);
								if(!$tarSal) {
									$artValido=false;
								}

								if($tipTrans == "D" and $artValido)
								{
									/*Validaci�n de Devolucion*/
									$artValido=validacionDevolucion($cco, $pac, $art, $aprov,false, &$error);
								}
							}else{
								/*El art�culo es v�lido no as� la cantidad*/
								/*Debe volver a preguntar la cantidad*/
								$error['codInt']='2003';
								$error['codSis']='NO APLICA';
								$error['descSis']='NO APLICA';
								if(!isset($dronum))
								{
									$dronum=0;
								}
								registrarError($odbc, $cco, $fuente, $dronum, 0, $pac, $art, $error, &$color, &$warning, $usuario);
								$art['cva']=true; //Para que vuelva a pedir la cantidad.
							}
						}else{
							/*Cantidad mayor al m�ximo permitido*/
							/*Debe volver a preguntar la cantidad
							Art�culo v�lido cantidad invalida*/
							$error['codInt']='2004';
							$error['codSis']='NO APLICA';
							$error['descSis']='NO APLICA';
							$art['cva']=true;  //Para que vuelva a pedir la cantidad.
							if(!isset($dronum))
							{
								$dronum=0;
							}
							registrarError($odbc, $cco, $fuente, $dronum, 0, $pac, $art, $error, &$color, &$warning, $usuario);
						}
					}
				}

				/*Fin de las Validaciones para el art�culo*/
				/*Empieza el ingreso de datos a la BD*/
				if($artValido and !$art['cva'])
				{
					/*Buscar los consecutivos */
					$artValido=Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, &$date, &$cns, &$dronum, &$drolin, $pac['dxv'], $usuario, &$error );

					/*Registrar en UNIX*/
					if($artValido)
					{
						$artValido =registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, &$error);
						if($artValido)
						{
							$art['ubi']='US';
							$art['lot']=" ";
							$art['ser']=$pac['sac'];
							if(isset($car) and !$cco['apl'])
							{
								$art['dis']='on';
							}
							$artValido = registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario,&$error);
							if($artValido)
							{
								                               //Febrero 19 de 2009
								if($artValido and ($cco['apl'] or (isset($art['apl']) and $art['apl']=="on")))
								{
									/**
									 * Aplica autom�ticamente
									 * * $cco['apl'] El centro de costo que carga o el centro de costos donde esta el paciente aplica autom�ticamente.
									 */								

									//Modificar el saldo de aplicaci�n
									$artValido=registrarSaldosAplicacion($pac,$art,$cco,$aprov,$usuario,$tipTrans,false,&$error);

									if($artValido)
									{
										if(!isset($fecApl))
										{
											$fecApl=$date;
											$ronApl=date("G:i - A");
										}
										//Registrar la aplicaci�n del art�culo
										$artValido = registrarAplicacion($pac, $art, $cco,$aprov,$fecApl,$ronApl, $usuario, $tipTrans, $dronum, $drolin, &$error);
									}
								}
								else
								{
									//No es de aplicaci�n autom�tica se afecta el saldo del art�culo al paciente.
									$artValido=registrarSaldosNoApl($pac, $art,$cco,$aprov,$usuario,$tipTrans,false,&$error);

									if ( $tipTrans == 'C' and $cco['asc'])
									{
										/**
										 * Es un cargo, no una devoluci�n, y el centro de costos que esta cargando aplica cuando se carga de ese centro de costos.
										 * 
										 * Esto quiere decir que antes de ingresar la historia se pidio una fecha de aplicaci�n y una hora de aplicaci�n, y se toma 
										 * como si fueran dos transacciones independientes, un cargo normal ewn el que se usa registrarSaldosNoApl, y una aplicaci�n
										 * para la cual se hace una salida por medio de registrarSaldosNoApl() y se regitra una aplicaci�n a trav�s de
										 * registrarAplicacion(). 
										 */

										//Se hace una salida por que se hace
										$artValido=registrarSaldosNoApl($pac, $art,$cco,$aprov,$usuario,"D",false,&$error);
										if($artValido)
										{
											if(!isset($fecApl))
											{
												$fecApl=$date;
												$ronApl=date("G:i - A");
											}
											//Registrar la aplicaci�n del art�culo
											$artValido = registrarAplicacion($pac, $art, $cco,$aprov,$fecApl,$ronApl, $usuario, $tipTrans,$dronum,$drolin, &$error);
										}
									}
								}

								//se hace la devolucion del carro
								if($tipTrans == 'Anulado')//se debe poner !=C
								{
									if($aprov)
									{
										$letra='A';
									}
									else
									{
										$letra='P';
									}
									//aca voy a consultar cuantos elementos estan en el carro
									$q = "SELECT ".$bd."_000003.id "
									."        FROM ".$bd."_000002, ".$bd."_000003 "
									."       WHERE Fenhis=".$pac['his']." "
									."         AND Fening=".$pac['ing']." "
									."         AND Fencco='".$cco['cod']."' "
									."         AND Fentip='C".$letra."' "
									."         AND Fdenum=Fennum "
									."         AND Fdeart='".$art['cod']."' "
									."         AND Fdedis='on' "
									."         AND Fdeest='on' ";

									$err1 = mysql_query($q,$conex);
									echo mysql_error();
									$num1 = mysql_num_rows($err1);
									$row1=mysql_fetch_array($err1);
									if($num1 >0)
									{
										$q = " UPDATE ".$bd."_000003 "
										."    SET fdedis = 'off' "
										."  WHERE id = ".$row1[0]
										."         AND Fdeart='".$art['cod']."' "
										."         AND Fdedis='on' "
										."         AND Fdeest='on' ";
										$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									}
									else
									{
										//aca voy a consultar cuantos elementos estan en el carro y son de la central
										$q = "SELECT Fdenum "
										."        FROM ".$bd."_000002, ".$bd."_000003 "
										."       WHERE Fenhis=".$pac['his']." "
										."         AND Fening=".$pac['ing']." "
										."         AND Fencco='".$cco['cod']."' "
										."         AND Fentip='C".$aprov."' "
										."         AND Fdenum=Fennum "
										."         AND Fdeari='".$art['cod']."' "
										."         AND Fdedis='on' "
										."         AND Fdeest='on' ";

										$err1 = mysql_query($q,$conex);
										echo mysql_error();
										$num1 = mysql_num_rows($err1);
										$row1=mysql_fetch_array($err1);
										if($num1 >0)
										{
											$q = " UPDATE ".$bd."_000003 "
											."    SET fdedis = 'off' "
											."  WHERE Fdenum = ".$row1[0]
											."         AND Fdeari='".$art['cod']."' "
											."         AND Fdedis='on' "
											."         AND Fdeest='on' ";
											$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
										}
									}
								}


							}//dentro de estas llaves se registran los saldos, y las aplicaciones si es del caso
						}//dentro de estas llaves se registran el detalle del cargo, los saldos, y las aplicaciones si es del caso
					}//dentro de estas llaves se registra en el cargo en itdro, el detalle del cargo en MATRIX, los saldos, y las aplicaciones si es del caso
				}

				if(!$artValido)
				{
					IF(!isset($dronum))
					{
						$dronum = 0;
					}
					IF(!isset($drolin))
					{
						$drolin = 0;
					}
					registrarError($odbc, $cco, $fuente, $dronum, $drolin, $pac, $art, $error, &$color, &$warning, $usuario);
				}
			}
		}
	}
	else
	{
		/**
		 * Sin Conexi�n con UNIX
		 */
		echo "<font color='#006699' face='Arial'><b>FUERA DE UNIX</b></font><br>";

		$odbc="INACTIVO";
		if(!isset($pac['act']))
		{
			$pac['his']=ltrim(rtrim($pac['his']));
			$pac['act']=HistoriaMatrix($cco, &$pac, &$warning, &$error);
			if($pac['act'])
			{
				$classHis="titulo1";
				$color="titulo2";
				$warning="";
				
				if(!isset($pac['nom']))
				{
					
					$pac['act']=infoPaciente(&$pac,$emp);
					$pac['dxv']=false;
					if(!isset($pac['nom']))
					{
						$pac['nom']="NO ENCONTRADO (".$pac['his'].")";
					}
				}


				if($pac['act'])
				{
					if(!$pac['ald'])
					{
						if(!$pac['ptr'])
						{
							if($pac['alp'] )
							{
								//busco si ya si tiro la factura
								$q1 = "SELECT * "
								."       FROM ".$bd."_000022 "
								."      WHERE Cuehis = '".$pac['his']."' "
								."        AND Cueing = '".$pac['ing']."' "
								."        AND Cuegen = 'on' ";
								$err1=mysql_query($q1,$conex);
								
								echo mysql_error();
								$gene=mysql_num_rows($err1);

								//El paciente esta en proceso de Alta
								if(!isset($alp))
								{
									//El usuario no selecciono el check box de alta en proceso.  No puede realizar cargos.
									$pac['act']=false;
									$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." ESTA EN PROCESO DE ALTA";
									if($gene>0)
									{
										$warning = $warning. " Y LA FACTURA DEL PACIENTE YA HA SIDO GENERADA";
									}
									$error['codInt']='0011';
									$error['codSis']=".";
									$error['descSis']=".";
									$error['clas']="#ff0000";
									$error['ok']="NO PASO, PACIENTE EN PROCESO DE ALTA ";
								}
								else
								{
									if($gene>0)
									{
										$factura='LA FACTURA DEL PACIENTE YA HA SIDO GENEREDA';
									}
								}
							}
							if($pac['act'])
							{
								//Centro de costos hospitalario.
								if($cco['hos'] )
								{
									//Si el cco es hospitalario este debe coincidir con el cco donde esta el usuario, por que si ya salio de un cco no deben esta cargandole cosas.
									if($cco['cod'] != $pac['sac'])
									{
										//El centro de costos del usuario no coincide con el centro de costos donde esta el paciente
										$pac['act']=false;
										$warning = "EL CENTRO DE COSTOS ".$cco['cod']." ES HOSPITALARIO<br> Y POR LO TANTO NO PUEDE HACERLE CARGOS O DEVOLUCIONES AL PACIENTE ".$pac['his']."<br>PUES ESTE SE ENCUENTRA EN EL CENTRO DE COSTOS ".$pac['sac'];
										$error['codInt']='0012';
										$error['codSis']=".";
										$error['descSis']=".";
										$error['clas']="#ff0000";
										$error['ok']="NO PASO, PACIENTE EN PROCESO DE ALTA ";
									}
								}	//Fin de la modificaci�n 2007-06-14
							}
						}
						else
						{
							$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." <BR> ESTA EN PROCESO DE TRASLADO, POR LO TANTO <BR> NO SE LE PUEDE NI CARGAR NI DEVOLVER <BR> NINGUN ART�CULO!!!";
							$error['codInt']='0010';
							$error['codSis']=".";
							$error['descSis']=".";
							$error['clas']="#ff0000";
							$error['ok']="NO PASO, PACIENTE EN TRASLADO";
							$pac['act']=false;
						}
					}
					else
					{
						$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." FUE DADO DE ALTA DE LA INSTITUCI�N!!!";
						$error['codInt']='0009';
						$error['codSis']=".";
						$error['descSis']=".";
						$error['clas']="#ff0000";
						$error['ok']="NO PASO, PACIENTE DE ALTA";
						$pac['act']=false;
					}
				}
				else
				{
					$warning = "NO EXISTE UN PACIENTE CON HISTORIA:".$pac['his']." ADMITIDO A LA INSTITUCI�N!!!";
					$error['codInt']='0012';
					$error['codSis']=".";
					$error['descSis']=".";
					$error['clas']="#ff0000";
					$pac['act']=false;
				}

			}

			if($pac['act'])
			{
				/**
				 * El paciente esta activo
				 * Es necesario saber si el centro de costos en donde esta matriculado el paciente aplica AUTOM�TICAMENTE
				 */
				$ccoPac['cod']=$pac['sac'];
				if(getCco(&$ccoPac, $tipTrans, $emp))
				{
					if($ccoPac['apl'])
					{
						$cco['apl']=true;
					}
				}
			}
		}

		if($pac['act'])
		{

			$color="titulo2";
			$warning="";
			//2007-06-15 Se borra la informaci�n la llenada extra de informaci�n del paciente.
			if(isset($art['cod']))
			{
				if(!isset($artValido)) {
					if(!isset($art['ini']))
					{
						$art['ini']=$art['cod'];
					}
					ArticuloCba(&$art);
					ArticulosEspeciales($cco, &$art);
					$artValido = ArticuloExiste(&$art, &$error);
					/**
				 * Si era un c�digo de mas de 6 caracteres debia corresponder a un c�digo de proveedor y la funci�n
				 * ArticuloBasico debia encontrar el c�digo correspondiente de la cl�nica de 6 caracteres, si no lo 
				 * encontro o si el c�digo original era menor a 6 caracteres, se asume que el c�digo es invalido pues 
				 * UNIX trbaja con 6 caracteres
				 */
				}

				if($artValido )
				{
					if(!$art['cva'])
					{
						if($art['can'] <= $art['max'])
						{
							if($art['can'] >0) {

								if($tipTrans == "D" )
								{
									/*Validaci�n de Devolucion*/
									$artValido=validacionDevolucion($cco, $pac, $art, $aprov,false, &$error);
								}
							}else{
								/*El art�culo es v�lido no as� la cantidad*/
								/*Debe volver a preguntar la cantidad*/
								$error['codInt']='2003';
								$error['codSis']='NO APLICA';
								$error['descSis']='NO APLICA';
								if(!isset($dronum))
								{
									$dronum="";
								}
								registrarError($odbc, $cco, $fuente, $dronum, 0, $pac, $art, $error, &$color, &$warning, $usuario);
								$art['cva']=true; //Para que vuelva a pedir la cantidad.
							}
						}else{
							/*Cantidad mayor al m�ximo permitido*/
							/*Debe volver a preguntar la cantidad
							Art�culo v�lido cantidad invalida*/
							$error['codInt']='2004';
							$error['codSis']='NO APLICA';
							$error['descSis']='NO APLICA';
							$art['cva']=true;  //Para que vuelva a pedir la cantidad.
							if(!isset($dronum))
							{
								$dronum="";
							}
							registrarError($odbc, $cco, $fuente, $dronum, 0, $pac, $art, $error, &$color, &$warning, $usuario);
						}
					}
				}
				else
				{

					/*Si el art�culo no se encontro en los c�digos de proveedor y
					tiene mas de 6 caracteres se asume que esta errado pues UNIX
					tiene art�culos con 6 caracteres*/
					$artValido = false;

					$error['codInt']="0003";
					$error['codSis']='NO APLICA';
					$error['descSis']='NO APLICA';
					registrarError($odbc, $cco, $fuente,  0, 0, $pac, $art, $error, &$color, &$warning, $usuario);
				}

				/*Fin de las Validaciones para el art�culo*/
				/*Empieza el ingreso de datos a la BD*/
				if($artValido and !$art['cva'])
				{
					/*Buscar los consecutivos */
					$artValido=Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, &$date, &$cns, &$dronum, &$drolin, $pac['dxv'], $usuario, &$error );

					/*Registrar en la BD*/
					if($artValido)
					{
						$art['ubi']='M';
						if($art['neg'])
						{
							//Permite negativos
							$art['ini']	= "P*".$art['ini'];
						}
						else
						{
							//No permite negativos
							$art['ini']	= "N*".$art['ini'];
						}

						$art['lot']=" ";
						$art['ser']=$ccoPac['cod'];
						if(isset($car) and !$cco['apl'])
						{
							$art['dis']='on';
						}
						$artValido = registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario, &$error);
						if($artValido)
						{
                                                            //Febrero 19 de 2009
							if($artValido and ($cco['apl'] or (isset($art['apl']) and $art['apl']=="on")))
							{
								/**
								 * Aplica autom�ticamente
								 * * $cco['apl'] El centro de costo que carga o el centro de costos donde esta el paciente aplica autom�ticamente.
								 */								

								//Modificar el saldo de aplicaci�n
								$artValido=registrarSaldosAplicacion($pac,$art,$cco,$aprov,$usuario,$tipTrans,false,&$error);

								if($artValido)
								{
									$trans['num']=$dronum;
									$trans['lin']=$drolin;
									if(!isset($fecApl))
									{
										$fecApl=$date;
										$ronApl=date("G:i - A");
									}
									//Registrar la aplicaci�n del art�culo
									$artValido = registrarAplicacion($pac, $art, $cco,$aprov,$fecApl,$ronApl, $usuario, $tipTrans, $dronum,$drolin, &$error);
								}
							}
							else
							{
								//No es de aplicaci�n autom�tica simplemente se afecta el saldo del art�culo al paciente.
								$artValido=registrarSaldosNoApl($pac, $art,$cco,$aprov,$usuario,$tipTrans,false,&$error);

								if ( $tipTrans == 'C' and $cco['asc'])
								{
									/**
									 * Es un cargo, no una devoluci�n, y el centro de costos que esta cargando aplica cuando se carga de ese centro de costos.
									 * 
									 * Esto quiere decir que antes de ingresar la historia se pidio una fecha de aplicaci�n y una hora de aplicaci�n, y se toma 
									 * como si fueran dos transacciones independientes, un cargo normal ewn el que se usa registrarSaldosNoApl, y una aplicaci�n
									 * para la cual se hace una salida por medio de registrarSaldosNoApl() y se regitra una aplicaci�n a trav�s de
									 * registrarAplicacion(). 
									 */

									//Se hace una salida por que se hace
									$artValido=registrarSaldosNoApl($pac, $art,$cco,$aprov,$usuario,"D",false,&$error);
									if($artValido)
									{

										$trans['num']=$dronum;
										$trans['lin']=$drolin;
										if(!isset($fecApl))
										{
											$fecApl=$date;
											$ronApl=date("G:i - A");
										}
										//Registrar la aplicaci�n del art�culo
										$artValido = registrarAplicacion($pac, $art, $cco,$aprov,$fecApl,$ronApl, $usuario, $tipTrans, $dronum, $drolin, &$error);
									}
								}
							}

							if($tipTrans == 'Anulado')//se debe poner !=C
							{
								if($aprov)
								{
									$letra='A';
								}
								else
								{
									$letra='P';
								}

								//aca voy a consultar cuantos elementos estan en el carro
								$q = "SELECT ".$bd."_000003.id "
								."        FROM ".$bd."_000002, ".$bd."_000003 "
								."       WHERE Fenhis=".$pac['his']." "
								."         AND Fening=".$pac['ing']." "
								."         AND Fencco='".$cco['cod']."' "
								."         AND Fentip='C".$aprov."' "
								."         AND Fdenum=Fennum "
								."         AND Fdeart='".$art['cod']."' "
								."         AND Fdedis='on' "
								."         AND Fdeest='on' ";

								$err1 = mysql_query($q,$conex);
								echo mysql_error();
								$num1 = mysql_num_rows($err1);
								$row1=mysql_fetch_array($err1);
								if($num1 >0)
								{
									$q = " UPDATE ".$bd."_000003 "
									."    SET fdedis = 'off' "
									."  WHERE id = ".$row1[0]
									."         AND Fdeart='".$art['cod']."' "
									."         AND Fdedis='on' "
									."         AND Fdeest='on' ";
									$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

								}
								else
								{
									//aca voy a consultar cuantos elementos estan en el carro y son de la central
									$q = "SELECT Fdenum "
									."        FROM ".$bd."_000002, ".$bd."_000003 "
									."       WHERE Fenhis=".$pac['his']." "
									."         AND Fening=".$pac['ing']." "
									."         AND Fencco='".$cco['cod']."' "
									."         AND Fentip='C".$aprov."' "
									."         AND Fdenum=Fennum "
									."         AND Fdeari='".$art['cod']."' "
									."         AND Fdedis='on' "
									."         AND Fdeest='on' ";

									$err1 = mysql_query($q,$conex);
									echo mysql_error();
									$num1 = mysql_num_rows($err1);
									$row1=mysql_fetch_array($err1);
									if($num1 >0)
									{
										$q = " UPDATE ".$bd."_000003 "
										."    SET fdedis = 'off' "
										."  WHERE Fdenum = ".$row1[0]
										."         AND Fdeari='".$art['cod']."' "
										."         AND Fdedis='on' "
										."         AND Fdeest='on' ";
										$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									}
								}
							}
						}
					}
				}

				if(!$artValido)
				{
					IF(!isset($dronum))
					{
						$dronum = 0;
					}
					IF(!isset($drolin))
					{
						$drolin = 0;
					}
					registrarError($odbc, $cco, $fuente, $dronum, $drolin, $pac, $art, $error, &$color, &$warning, $usuario);
				}
			}
		}
	}

	/**************************************************************************************************
	* Se terminan los procesos de comprobaci�n de datos y art�culos
	* A partir de aqui se pide y se muestra informaci�n en pantalla
	**************************************************************************************************/

	echo "<tr><td class='tituloSup'>".$cco['cod']."-".$cco['nom']."</td></tr>";
	echo "<tr><td class='tituloSup'>USUARIO: ".$usuario."</b>";
	
	if(isset($cco['asc']) and $cco['asc'] and $tipTrans == "C")
	{
		echo "<tr><td class='tituloSup'>Aplicaci�n ".$fecApl." ".str_replace(" ", "",$ronApl)."</b>";
	}

	if (isset($date) )
	echo "<br>(".str_replace("-","/",$date)." ".date("H:i:s").")</td></font></tr>";
	else
	echo "<br>(".date('Y / m / d')." ".date("H:i:s").")</td></font></tr>";

	if(isset($factura))
	{
		echo "<tr><td align=center><font color='red'>".$factura."</td></font></tr>";
	}
	
	if(isset($pac['act']) and $pac['act'] )
	{
		/*Si existe el numero de historia y el CC*/
		echo "<tr><td class='$classHis'><b>".$pac['nom']." (".$pac['hac'].")</td></tr>";

		if(isset($art['cod']) and $artValido and $art['cva'])
		{
			/*Cantidad variable*/
			/*Debe digitarse cantidad*/

			echo "<tr><td class='".$color."'><font color=#000066><b>$warning Art�culo:</b> <font size='2'>".$art['cod']."-".$art['nom']."<br>";
			echo "(Cant. M�x ".$art['max'].$art['uni'].")</font> <b>Cantidad:</b></font>";
				?>	<input type='text' name="art[can]" size="3" value="<?php echo $art['can'];?>">
				<script language="JAVASCRIPT" type="text/javascript">
				document.carga.art[can].focus();
			</script>
			<?php
			/*Se envian todas las variables que +*/
			echo "<input type ='hidden' name='art[cod]' value='".$art['cod']."'>";
			echo "<input type ='hidden' name='art[ini]' value='".$art['ini']."'>";
			echo "<input type ='hidden' name='art[nom]' value='".$art['nom']."'>";
			echo "<input type ='hidden' name='art[uni]' value='".$art['uni']."'>";
			echo "<input type ='hidden' name='art[max]' value='".$art['max']."'>";
			echo "<input type ='hidden' name='art[neg]' value='".$art['neg']."'>";
			echo "<input type ='hidden' name='artValido' value='".$artValido."'>";
			echo "<input type ='hidden' name='art[cva]' value=''>";
		}
		else
		{
			/*if($artValido)
			{
			echo "es Valido";
			}
			else {
			echo "no es v�lido";
			}*/
			echo "<tr><td class='".$color."'>".$warning."<b>ART: </font>";

			?>			<input type='text' name="artcod" size='14'>   
			<script language="JAVASCRIPT" type="text/javascript">
			document.carga.artcod.focus();
			</script>
			 <?php


			 if(isset($cns) and $artValido and isset($dronum) and $dronum != "")
			 {
			 	/*En las variables $artPrevios y Show se acumulan los tres ultimos articulos ingresados al sistema
			 	Se separan los articulos y se seleccionan los ultimos 3*/
			 	if( $cns>3 )
			 	{
			 		$t=explode("*",$artPrevios);//separa en una mtriz los articulos
			 		$artPrevios="*".$cns.")".$art['nom']." Cant.(".$art['can']." ".$art['uni'].")*".$t[1]."*".$t[2];
			 		$artMostrar=$cns.")".$art['nom']." Cant.(".$art['can']." ".$art['uni'].")".chr(13).$t[1].chr(13).$t[2];
			 	}
			 	else
			 	{
			 		if($cns == 1)
			 		{
			 			$artPrevios="";
			 			$artMostrar="";
			 		}
			 		$artPrevios="*".$cns.")".$art['nom']." Cant.(".$art['can']." ".$art['uni'].")".$artPrevios;
			 		$artMostrar=$cns.")".$art['nom']." Cant.(".$art['can']." ".$art['uni'].")".chr(13).$artMostrar;
			 	}
			 }
		}//fin del else

		/**
	 * Check box para determinar si el articulo debe ser cargado con fuente de aprovechamiento o no.
	 */
		//echo "</td></tr><tr><td class='titulo2'>

		if($aprov)
		{
			if($cco['apr'])
			{
				if($art['cva'])
				{
					echo "<input type='checkbox' name='aprov'  checked>Aprov";
				}
				else
				{
					echo "<input type='checkbox' name='aprov'>Aprov";
				}
			}
			echo "<input type='hidden' name='aprovEstadoAnterior' value='1'>";
		}else {
			if($cco['apr'])
			{
				echo "<input type='checkbox' name='aprov'>Aprov";
			}
			echo "<input type='hidden' name='aprovEstadoAnterior' value='0'>";
		}
		echo "</td></tr>";

		/**
	* Impresi�n de los art�culos asociados
	**/

		If(isset($cns) and $cns != 0) {
			/* Si el codigo del articulo existe en pantalla debe imprimirse en el textarea correspondiente*/

			echo "<tr><td class=".$color." >ARTICULOS ASOCIADOS:</font><br>";
			echo '<textarea ALIGN="CENTER"  ROWS="3" name="artMostrar" cols="28"  color:"'.$color.'" style="font-family: Arial; font-size:14" readonly>'.$artMostrar.'</textarea></font>';

			if($artValido and !$art['cva'] ) 	{
				/*Si existe artgen y es diferente de vacio es por que se ingresaron datos al sistema*/
				echo "<input type='hidden' name='cns' value='".($cns+1)."'>";
			}else
			{
				echo "<input type='hidden' name='cns' value='".$cns."'>";
			}

			echo "<input type='hidden' name='dronum' value='".$dronum."'>";
			echo "<input type='hidden' name='date' value='".$date."'>";
			echo "<input type='hidden' name='artPrevios' value='".$artPrevios."'>";
		}

		echo "<input type='hidden' name='usuario' value='".$usuario."'>";
		echo "<input type='hidden' name='tipTrans' value='".$tipTrans."'>";
		echo "<input type='hidden' name='emp' value='".$emp."'>";

		echo "<input type='hidden' name='pac[nom]' value='".$pac['nom']."'>";
		echo "<input type='hidden' name='pac[hac]' value='".$pac['hac']."'>";
		echo "<input type='hidden' name='pac[sac]' value='".$pac['sac']."'>";
		echo "<input type='hidden' name='pac[his]' value='".$pac['his']."'>";
		echo "<input type='hidden' name='pac[ing]' value='".$pac['ing']."'>";
		echo "<input type='hidden' name='pac[act]' value='1'>";
		echo "<input type='hidden' name='pac[dxv]' value='".$pac['dxv']."'>";
		echo "<input type='hidden' name='classHis' value='".$classHis."'>";
		if (isset($car))
		{

			echo "<input type ='hidden' name='car' value='on'>";
		}


		echo "<input type='hidden' name='cco[cod]' value='".$cco['cod']."'>";
		echo "<input type='hidden' name='cco[nom]' value='".$cco['nom']."'>";
		if($cco['neg'])//2007-06-18 No se enviaban n�mero se enviaban boooleanos, por ello se crea el if
		{
			echo "<input type='hidden' name='cco[neg]' value='1'>";
		}
		else
		{
			echo "<input type='hidden' name='cco[neg]' value=''>";
		}

		if($cco['asc'])//2007-09-12 Se crea
		{
			echo "<input type='hidden' name='cco[asc]' value='1'>";
			if($tipTrans == "C")
			{
				echo "<input type='hidden' name='fecApl' value='".$fecApl."'>";
				echo "<input type='hidden' name='ronApl' value='".$ronApl."'>";
			}
		}
		else
		{
			echo "<input type='hidden' name='cco[asc]' value=''>";
		}

		if($cco['apl'])//2007-06-18 No se enviaban n�mero se enviaban boooleanos, por ello se crea el if
		{
			echo "<input type='hidden' name='cco[apl]' value='1'>";//Se aplica autom�ticamente
		}
		else
		{
			echo "<input type='hidden' name='cco[apl]' value=''>";// NO Se aplica autom�ticamente
		}

		if($cco['apr'])//2007-06-18 No se enviaban n�mero se enviaban boooleanos, por ello se crea el if
		{
			echo "<input type='hidden' name='cco[apr]' value='1'>";//Permite Aprovechamientos
		}
		else
		{
			echo "<input type='hidden' name='cco[apr]' value=''>";//NO Permite aprovechamientos
		}
		echo "<input type='hidden' name='cco[fue]' value='".$cco['fue']."'>";//Fuente sencilla
		echo "<input type='hidden' name='cco[fap]' value='".$cco['fap']."'>";//Fuente de aprovechamientos

		echo "<tr><td class='".$class."'>";
		echo "<A HREF='cargosmxq.php?usuario?=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."'>Retornar Usuario</a> </font>";
		echo "&nbsp; &nbsp;<A HREF='cargosmxq.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ccoCod=UN.".$cco['cod']."'>Retornar Usuario+CC</a> </font>";

		echo "</td></tr>";
		echo"<tr><td class='titulo3'><input type='submit' value='ACEPTAR'></td></tr></form>";

		echo "<tr><td class='".$class."'>";
		echo "<A HREF='cargosmxq.php?tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."'>Retornar</a>";
		echo "&nbsp; &nbsp;<A HREF='reporte.php?pac[his]=".trim($pac['his'])."&amp;pac[ing]=".trim($pac['ing'])."&amp;cc=".$cco['cod']."&tipTrans=".$tipTrans."&usuario=".$usuario."&emp=".$emp."&bd=".$bd."'>Reporte dia paciente</a>";
		echo "</td></tr>";

	}
	else
	{
		echo "<tr><td class='errorTitulo'>";
		echo $warning;
		echo "</td></tr>";
		echo "<tr><td class='".$class."'>";
		echo "<A HREF='cargosmxq.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."'>Retornar con Usuario</a>&nbsp; &nbsp;";
		echo "<A HREF='cargosmxq.php?tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."'>Retornar</a>";
		echo "</td></tr>";

		$art['cod']="NO APLICA";
		registrarError($odbc, $cco, $fuente,  0, 0, $pac, $art, $error, &$color, &$warning, $usuario);
	}
}
}
else
   {
   ?>	    
    <script>
        alert ("!!!! ATENCION !!!! En este momento no se puede Facturar ni hacer Devoluciones por INVENTARIO FISICO");
        document.forms.ventas.submit();
    </script>
   <?php 
   }

echo "</table>";
echo "</form>";
?>
</body>
</html>