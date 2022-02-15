<?php
include_once("conex.php");
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : HCE.php
	   Fecha de Liberacion : 2009-07-09
	   Autor : Pedro Ortiz Tamayo
	   Version Inicial : 2009-07-09
	   Version actual  : 2020-06-23
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite registrar los datos clinicos
	   de un paciente, en distintos formularios segun la estructura logica definida en la metadata de la HCE.
	   
	   REGISTRO DE MODIFICACIONES :
	   .2022-01-13
			1. Se cambia de posicion la condicion de if ($tieneCCA) y se agrega el parametro wespecialidad para verificar si la Configuracion Cargo Automatico tiene 
			asignado una especialidad
	   .2020-06-23
			1. Para los campos tipo tabla antes de insertar valida si ese campo permite repetidos (están configurados en 
			   root_000051 - permitiRepetirCodigoCampoTablaHCE) de lo contrario los elimina (funcionamiento normal) 
			   garantizando que no quede el mismo código varias veces.
	   .2020-04-30
			1. Se muestra el botón del visor de resultados si la empresa está activa en root_000051 (parámetro: mostrarVisorResultadosHCE = on), 
			   el usuarios esta habilitado en root_000051 (usuariosHabilitadosVisorResultadosHCE) y que el centro de costos del paciente esta 
			   configurado con interoperabilidad en movhos_000011 (Ccotio)
	   .2020-04-15
			1. Se agrega el estado al query que obtiene la especialidad en movhos_000048 del profesional que firma el formulario para quede registrada correctamente en hce_000036.
	   .2020-04-02
			1. Se agrega el acceso al envío de la historia clínica y órdenes médicas, solo se muestra el botón para las empresas configuradas en root_000051 (parámetro: mostrarIconoEnvioHCEyOrdenes)
	   .2019-12-17
			1. Se agrega el acceso al visor de resultados, solo se muestra el botón para las empresas configuradas en root_000051 (parámetro: mostrarVisorResultadosHCE)
	   .2019-12-16
			1. En los campos con queries al borrar el contenido de un campo y dejarlo vacio, cuando había un submit en el formulario 
			   dicho campo se volvía a llenar con la información del query por tal motivo se realizan las modificaciones necesarias
			   para evitar ocurra esta situación.
			2. Se modifica el orden del urldecode() al insertar los registros en el formulario para evitar que se omita el signo más (+).
	   .2019-11-21
			1. Se modifica el include HCE.min.js por HCE.js
			2. Se comenta el reemplazo del caracter "&" por "y" ya que en HCE.js se agrega la función encodeURIComponent() 
			   que codifica los caracteres especiales enviados por ajax y en HCE.php se decodifican los valores con la función
			   urldecode().
	   .2019-11-19
			1. Se agrega reemplazo de caracter & por y, ya que el & evita que el texto que se digita después de éste caracter 
			   se inserte. Además en los campos tipo grid se reemplaza el * por x para evitar que dañe la estructura de este tipo 
			   de campo.
	   .2019-10-23
			1. Se agrega el llamado a la función actualizarDiagnosticosPaciente() del include funcionesHCE.php para actualizar 
			   el diagnóstico del paciente en movhos_000243 y movhos_000272 si el formulario está configurado en movhos_000274.
	   .2019-10-09
			1. Se modifica el tamaño de la letra del encabezado.
	   .2019-08-13
			1. Se agrega el include a funcionesHCE.php con la función calcularEdadPaciente() y se reemplaza en el script el cálculo 
			   de la edad del paciente por dicha función, ya que el cálculo se realizaba con 360 días, es decir, no se tenían en 
			   cuenta los meses de 31 días y para los pacientes neonatos este dato es fundamental.
	   .2019-07-29
			1. Para los campos tipo Tabla se agrega una validación para evitar que en el select se guarde el valor por defecto.
	   	.2019-07-15: Jerson Trujillo, Se agregan las siguientes dos lineas, ya que estaban quedando caracteres basura
				en el valor del primer option y en la versión anterior solo se estaba eliminando el primer caracter.
				Entonces se borra todo lo que este antes del primer <option. 
	   .2019-07-11
			1. Para los campos tipo Tabla se agrega un filtro al query si en hce_000002 tiene un valor por defecto especificado,
			   este query (autocomplete) se construye con la configuración de hce_000002 especificada en los campos Detarc (tabla), 
			   Detcbu y Detnbu, el nuevo filtro se construte con el campo especificado en Detcav y en Detvde el valor que debe tener. 
			2. Se agrega htmlentities() los formularios que se muestran al ingresar a HCE para que los acentos y Ñ se visualicen 
			   correctamente 
	   .2019-07-09
			1. Se agrega htmlentities() al nombre del paciente y el responsable para que los acentos y Ñ se visualicen 
			   correctamente en la información demográfica.
	   .2019-03-12
			1. Se agrega el include a AlertasHCE.php para que cada vez que llenen un formulario firmado valide si ese 
			   formulario esta configurado en la tabla movhos_000249 con alertas HCE y si el paciente cumple la condicion
	   .2018-10-11
			1. En el case W1 se agrega el include al script AlertasFormulariosHCE.php que al seleccionar un formulario 
			   valida si tiene una alerta configuradas en movhos_000250 y las muestra en una modal.
	   .2018-08-06
			1. En el case W1 se modifican los queries que validan si el formulario esta firmado con la condición >= 999 y 
			   se cambia por la condición movcon IN (999,1000) ya que generaba lentitud en formularios grandes.
			2. En el case W1 se modifica el query que validaba con movtip='Hora' y se agrega un join a hce_000002 para 
			   relacionar movcon con detcon y así utilice el indice conhising_idx
	   .2018-04-05
			1. Se modifica el campo tipo Grid para que permita resaltar un campo especial de control.
			2. Al campo se le anteponi el caracter "^" que indica que es especial.
	   .2018-03-01
			1. Se modifica el programa para permitir que un formulario pueda tener mas de un titulo asociado, dependiendo
			   del proposito con que se use.
			2. Se habilita el programa para el manejo de campos Grid inteliegentes clasificados como tipo J.
	   .2017-05-03
			1. Se modifica en la grabacion en la validacion especifica, que no estaba operando de forma correcta ya que
			   por no bloquear la tabla no estaba grabando la información.
	   .2017-04-24
			1. Se modifica en la grabacion la validacion especifica, que no estaba operando de forma correcta.
			2. Se incluye los campos Link SMART para el registro intraoperatorio de Anestasia y otors.
	   .2016-12-07
			1. Se modifica el ancho y alto del recuadro de alertas para aprovechar mejor el espacio, se agrega el 
			   tamaño del width y height a la variable $estilosFrame que se envía a la función llamarIframeAlerta
	   .2016-12-05
			1. Se modifica el programa en la seccion de alertas, con cenexion al nuevo modulo
			2. Se modifica el campo titulo para que la validacion de fecha, edad y validacion especifica se haga de 
			   forma correcta, ya que la versión anterior no estaba operando.
			3. Se cambia el query de las vistas logicas para incluir campos desactivados.
	   .2016-09-26
			1. Se modifica el programa para mejorar el manejo de los campos booleanos, ya que se estaban quedando 
			   prendidos.
			2. Se habilita el campo titulo para que entre en la validacion de fecha, edad y validacion especifica pero de 
			   forma correcta, ya que la versión anterior no estaba operando de forma correcta.
	   .2016-08-09
			1. Se modifica el programa para incluir una subrutina de posicionamiento basada en el movcon para los algoritmos 
			   que hacen referencia a otras variables del mismo formulario.
			2. Se habilita el campo titulo para que entre en la validacion de fecha, edad y validacion especifica.
	   .2016-07-07
			1. Se modifica el programa para cambiar la operacion del campo Referenciaque no estaba operando de forma correcta.
	   .2016-05-18
			1. Se modifica el programa para incluir validacion especifica, que consiste en incluir o no un campo en un
			   formulario, dependiendo del resultado de un algoritmo que se encuentra en la variable detves (Validacion Especifica)
			   de la tabla 2 de HCE de Detalle de Formularios.
			2. Se modifica el campo Referencia, ya que no estaba actualizando valores en el evento Doble Click.
			   puro.
	   .2016-02-25
			1. Se modifica el campo Password para añadirle verificacion con la tabla de usuarios de Matrix, tanto en la
			   recarga ajax como en la grabacion de los datos.
			2. Se modifica el campo Memo tipo "R" para añadirle la opcion "ALGO" y convertirlo en un campo algoritmico
			   puro.
			3. Se modifica el campo Tabla para añadirle la funcionalidad "SMART" que permita hacer el query sobre multiples
			   Tablas. Esta modificacion implica adiciones sobre el script de autocompletar.php.
	   .2016-01-20
			1. Se modifica el query post-grabacion de la historia que traia datos con consecutivo <= a 1000
			   por un 3 querys con union all al los consecutivos 1000, 999 y al consecutivo del primer campo 
			   obligatorio, lo que optimiza el acceso a la base de datos.
	   .2015-07-14
			1. Se modifica el sistema de respaldo de informacion para conservar los caracteres 10 y 13 (ENTER) en las
			   descripciones de los campos tipo Memo utilizando tildes circunflejas solo en los campos Memo.
			2. Se adiciona una funcion (Trepetidos) que elimina claves duplicadas en los campos tipo tabla al momento
			   de grabar la informacion.
	   .2015-07-01
			1. Se modifica el sistema de respaldo de informacion para conservar los caracteres 10 y 13 (ENTER) en las
			   deescripciones de los campos tipo Memo.
			2. Se modifica las busqueda en los campos tipo Tabla Desplegables ya que no lo estaba haciendo de forma correcta.
			3. Se modifica el programa para prevernir el ingreso a mas de una Historia en el mismo Login.
	   .2015-04-09
			1. Se adiciona al campo Seleccion la opcion de CRONO en el campo tip informativo, para ser inicializado
			   de forma inteligente.
			2. Se adiciona el servicio en la url de las notas.
			3. Se implementa la opcion de colocar en "no" en la tabla 36 cuando el formulario tiene una firma no validada
			   por una nota confirmatoria.
			4. Si una variable de un formulario tiene definido protocolo, el valor de este prima sobre el valor x defecto.
			5. Se coloca de tamaño variable el campo de descripcion sobre el objeto tabla, indicando su tamaño en el campo URL.
			6. En el campo URL se coloca parametrizacion del objeto Seleccion, para propositos especiales.
			7. Se definen algoritmos en el objeto Seleccion almacenados en el campo Formula, si la Seleccion es tipo CRONO. 
	   .2014-09-24
			1. Se adiciona al campo Memo Normal la opcion de habilitar o deshabilitar el copy - paste.
			2. Se adiciona al campo Titulo la opcion de habilitar que se muestre colapsado o expandido.
			3. Se modifica el proceso de lectura del archivo texto de Recovery para eliminar lineas en blanco.
			4. Se habilita la variable LABELSMART para pasar parametros desde campos Grid tipo tabla M a ventanas Modales
			   con enlace.
	   .2014-08-13
			1. Se agrega en la evaluacion al grabar el campo url, el cual se utilizara para procesos algoritmicos del 
			   tipo GRID.
			2. En el campo tipo FECHA, si se coloca en el valor x defecto "DATE", el campo sera inicializado con la 
			   fecha actual.
			3. En el campo tipo TABLA, se agrega la variable TTipe para poder ofrecer multiples opciones de Radio Button.
	   .2014-06-25
			Se cambia el campo link al pasar el enlace de la variable formula a la variable url.
	   .2014-06-18
			Se coloca nuevamente la funcion htmlentities y se cambia el codigo javascript para detectar diferencias en la
			recarga AJAX de los campos clasificados con id W. Esto soluciona el tema de las tildes y de campos que se actualizan
			por script dinamicos.
	   .2014-06-11
			1. Se cambia el proceso del Arbol de menu de opciones, eliminando la recarga ajax y el acceso a la base de datos
			   por cada recarga. Se pinta una sola vez y se utiliza javascript pra mostrar y ocultar las opciones.
			2. Se crea la opcion de objeto Label SMART este sirve para mostrar la ejecución de un algoritmo en un Label
			   y guardarlo en la base de datos.
			3. En la recuperación del archivo de respaldo, los campos Fecha Y Hora de nivel de seguridad 2, se actualizan
			   a la fecha y hora actuales.
			4. Se habilita funcion para determinar el tiempo de carga de los objetos de un formulario de HCE.
			5. Se crea dentro del campo Grid la clase Q1 en la variable tip informativo Dettii. para mostrar en el Grid la
			   ejecucion de un algoritmo.
			6. En el campo Grid se crea la opcion de subcampos Memo agregandolos en la configuración como Mlongitud para el
			   ancho del campo, el altoes fijo. Al campo Numerico se le adiciona la opcion NS para totalizar la variable si
			   se desea.
			7. Se crea la variable wsagrig para el control de la recarga de los Memos del Grid con htmlentities.
			8. Se elimina la opcion VISBR del campo tipo Memo. Esta la reemplaza el Label SMART.
			9. Al campo tipo Link se le asocian las variables : empresa,movhos y origen para poder ejecutar cualquier script
			   de la HCE.
			10. En el campo tipo Seleccion se comenta el strtoupper para no dañar el reemplazo de la historia y el ingreso.
	   .2014-04-15
			1. Se adiciona en la validacion de los campos seleccion  la opcion de undefined que no se estaba teniendo en
			   cuenta.
			2. Se adiciona comilla simple en los reemplazos de variables de los querys para optimizar su ejecucion.
			3. Se adiciona la clase VISBR para los campos Memo tipo R que requieren ejecucion del metodo antes de la 
			   grabación de la información. 
			4. Sa Adiciona en el campo Grid un hipervinculo a un programa para mostar el comportamiento historico de este campo
			5. Se adiciona al campo Link la validacion de Historia, Cedula, Tipo de Documento, Numero de Ingreso para url genericas.
	   .2014-02-13
			1. Se adiciona a la inicializacion de protocolos aquellos con medico *.
			2. Se adiciona a las validaciones del campo referencia la clasificacion del campo origen cuando no es
			   Seleccion y contine el signo "-" para evitar errores.
			3. Se modifica la cronologia del campo Tabla que estaba errado.
	   .2013-12-12
			1. Se cambia la busqueda de datos demograficos para tener en cuenta ingresos anteriores al ultimo.
			2. Se adiciona validacion para los rangos de los campos numericos.
	   .2013-11-27
			1. Se habilita el funcionamiento de los formularios tipo 4 o de procesos no asistenciales. Este formulario se
			   comporta como un formulario tipo 1 con la diferencia que se puede modificar n veces.
			2. Se habilita el programa HCE para trabajar formularios de ingresos anteriores al ingreso activo, esto para
			   poder operar los formularios tipo 4 y los formularios de Fisiatria.
			3. Se habilita el programa para el funcionamiento de imagenes dinamicas segun el protocolo x medico.
	   .2013-11-05
			1. Se coloca dinamico el logo basado en el origen.
			2. Se agregan las variables de origen y base de datos de movimiento hospitalario para la multiempresa .
			3. Se cambian las alertas para mostrar las firmadas con 999 por los residentes y se agregan las alertas
			   del kardex.
			4. En el arbol de opciones de menu se coloca la tabla 37 de root para conocer la historia e ingreso para ciertas
			   opciones externas. En caso de necesitar fecha, se coloca el dia en curso.
			5. Se cambia la opcion de impresión para llamar la utilidad del Centro de Impresion.
			6. Se cambia la seccion de ayudas generales para incluir el tipo de documento y la identificacion para ciertas opciones.
			7. Se cambia el campo de referencia para permitir que se deje modificar.
			8. Se modifica la funcion del campo subtitulo para operar con enunciados extensos.
			9. En el campo de iamgenes se elimina el centrado para hacerlo compatible a todos los navegadores.
		   10. En el campo tabla se coloca la opcion "Q" para traer informacion de ingresos anteriores.
		   11. En el campo seleccion se validad unicamente el codigo para evitar conflictos conn tildes y otros caracteres especiales.
		   12. Se corrige el funcionamiento de las vistas asociadas.
	   .2013-07-30
			1. Se activa la validacion de Medico Dependiente, el cual tiene una connotacion parecida a la del Medico Recidente
			   solo que en ocasiones se comporta como un Medico General tambien.
			2. Se agrega a la validacion de campos obligatorios el campo tipo Grid el cual no estaba contemplado.
			3. Se modifica la presentacion de los campos de seleccion en drop_down para no mostrar la clave, tanto en el tipo
			   Seleccion como en los subcampos del tipo Grid.
	   .2013-06-12
			Tambien se cambia la recarga ajax de los campos Memo que continen el signo + cambiandolo por el signo ~ para 
			mostrarlo como + en la impresion.
	   .2013-05-08
			1. Se modifica la accion desde la tabla 44 para parametrizar las opciones por ventana modal o por ventana nueva.
			
	   .2013-05-02
			1. Se modifica el campo Memo tipo R para darle mayor funcionalidad.se tipifica (VISI/VISB/INVI).
						a. VISI se ejecuta el algoritmo y el resultado es modificable
						b. VISB se ejecuta el algoritmo y el resultado NO es modificable
						c. INVI se ejecuta el algoritmo y el resultado NO es visible
			2. Se cambia la presentacion de resultados de las Vistas Asociadas utilizando el algoritmo de la impresion
			   contenido en el include HCE/HCE_print_function.php
			
	   .2013-04-15
			1. Se crea la tabla 44 para parametrizar las opciones que aparecen en la barra de ayudas.
			2. Se adiciona a la validacion del campo tipo seleccion la opcion undefined para el caso de los radio_Button.
			3. Se cambio el manejo de rangos de fecha en la vistas asociadas, para tener en cuenta la fecha de ingreso.
			
	   .2013-03-04
			1. Se adiciona la opcion de impresion de paquetes de formularios, la cual permite imprimir de forma rapida
			   conjuntos de formularios en impresiones separadas, como por ejemplo el Alta Hospitalaria.
			2. Se cambian los iconos del tipo JPG al tipo PNG.
			
	   .2013-02-26
			1. Se modifica el campo Grid, definiendo en la configuracion los campos que son Requeridos y los que son 
			   opcionales. La Grabacion y la Modificacion controlan el diligenciamiento de estos campos. La parametrizacion
			   de esta obligatoriedad se hace en el campo formula, despues de los titulos y la definicion de tipos y antes
			   del query para cronologicos. Estructura TITULOS*TIPOS*REQUERIDOS*QUERY.
			2. Se estilan los campos fecha y hora del campo Grid.
			3. Se cambia la opcion undefined por Seleccione en los objetos Drop Down. 
			 
	   .2013-02-15
			1. Se cambia la estructura de la tabla hce_000036 adicionandole el campo Firrol para guardar la especialidad
			   del usuario en el momento de diligenciar la informacion.
			2. Se quita el icono de respaldar al informacion del iframe de demograficos y se coloca en los campos Titulo
			   en el extremo izquierdo y se activa con doble click.
			3. Se le adiciona la opcion de cronologia al compo Grid.
			4. Se arregla el algoritmo del menu para que muestra todas las opciones de un subarbol asi ramas anteriores no
			   este activas.
			5. Se establece un control de verificacion de la consistencia de la configuracion de los formularios para validar
			   si los niveles de seguridad Detnse esta correcto y si en los campos tipo Memo los valores del alto y el ancho
			   de este campo estan correctos.
			
	   .2013-01-22
			1. Se cambia en los los objetos de los formularios de la Historia la propiedad Readonly='Readonly' por disabled.
			   Esto por que Inernet Explorer permite posicionar el cursor en los campo Readonly y al oprimir la tecla
			   Backspace, retrocede a la pagina anterior ocasionando la perdida de informacion.
			2. Se clona el icono de respaldar la informacion para colocarlo al lado del titulo del formulario, con el proposito
			   de hacer mas facil este proceso.

	   .2012-12-26
			1. Se corrige el funcionamiento de la recarga por Recovery, ya que no estaba cargando en JavaScript las
			   funcionalidades de algunos objetos.
			2. Se corrige el objeto Grid ya que no estaba funcionando para multiplos objetos de este tipo en un mismo
			   formulario.
			3. Se adicionan multiples hojas de estilo de acuerdo la navegador, con el proposito de que la historia
			   se pueda utilizar en multiples navegadores.
			   
	   .2012-12-20
			1. Se corrige el funcionamiento de los objetos referencia, ya que el id con W no funcionaba se cambio e J.
			2. Se deshabilita la tecla Backspace ya que en IE provoca un back del navegador perdiendo la informacion
			   digitada en el formulario.
			   
	   .2012-12-17
			Se modifica el programa para agregarle las siguientes modificaciones:
				1. Se crea el tipo de campo GRID el cual permite diligenciar multiples ocurrencias de un evento en un
				   una presentacion de tipo matricial.
				2. Se crea el tipo de campo Busqueda el cual permite hacer una recarga ajax para efectuar busquedas en
				   otros campos.
				3. El el campo seleccion se agrego la opcion SMART en el campo tooltip para hacer selecciones inteligentes
				4. Se elimino la opcion de copy + paste de la historia. La seguridad se empaqueto en el archivo HCE_Seguridad.js
				5. Se modifica el codigo de los diferente tipos de objetos para no repetir la programacion dependiendo de la
				   posicion del objeto en el formulario.
				6. Se modifica el calculo del objeto Formula para analizar algoritmos genericos.
			
	   .2012-08-21
			Se modifica la operacion de los campos Memo tipo M (ODBC UNIX) para que trabajen modificables o no dependiendo
			del valor del Tooltip si esta en BLOCK o no.
			
	   .2012-08-10
			Se modifico el programa para incluir en los tipos que usen textarea la validacion de la consistencia del alto y 
			el ancho de este objeto, es decir que sea numerico y menor de 900.
			Se modifico la presentacion de los datos cuando vienen del archivo de Recovery, ya que en este caso no se puede
			usar htmlentities.
			
	   .2012-08-09
			Se modifica el programa para incluir las siguientes funcionalidades :
			* 1. Se le adiciona al campo Seleccion tipo tabla la posibilidad de traer información de otras tablas de Matrix
			*    tambien se le adiciono un parser para mostrar correctamente las tildes y la eñes en un select multiple
			* 2. Se le adiciono al campo Memo tipo I o R un parser cuando los querys hacen referencia a una seleccion multiple.
			* 3. Se le adiciono al campo Referencia la posibilidad de retroceder para traer la información desde el ingreso que
			*    la tenga y se le adiciono un parser cuando los querys hacen referencia a una seleccion multiple.
			*    Este campo cambio el imput de Text a Texarea. Tiene la prosibilidad de bloqueo de entrada de datos colocande
			*    la palabra BLOCK en mayuscula sostenida en el campo de Tooltip.
			
	   .2012-08-06
			Se modifica el programa para incluir las siguientes funcionalidades :
			* 1. Se adiciona un proceso de respaldo y recuperacion de la informacion en un archivo plano que tiene la 
			*    siguente estructura : formulario.usuario.historia.ingreso.txt. Para grabar la informacion en este archivo
			*    se deba hacer una recarga ajax a traves de un boton de respaldo de informacion.
			* 2. Se crea un proceso de firma de estudiante con consecutivo 999. Esta firma se vailda con un proceso de 
			*    Notas Confirmatorias que el profesor debe realizar, seleccionando el boton de Notas Confirmatorias que
			*    activa un programa para este proposito.
			* 3. Se crea una opcion adicional dentro del tipo Memo que permite hacer cualquier tipo de consulta a tablas
			*    de Matrix y llenar el campo con el resultado de esta consulta. Se definieron 2 tipos I y R. I es generico
			*    y R es para el campo que define donde estaba el paciente en el momento del registro clinico. Este nuevo
			*    tipo se puede bloquear para mantener la consulta, parametrizando el campo de tooltip con la palabra BLOCK
			*    en mayuscula.
			
	   .2012-07-05
			Se adiciona el programa el control de prerequisitos de formularios y se cambia la logica de la firma electronica
			para hacerla obliogatoria.
			
	   .2012-02-21
			Se cambia el enlace al Vademecum ya que el anterior dejo de funcionar.
			
	   .2012-01-03
			Se realizan las siguientes modificaciones en el programa:
			* 	1. En el iframe de Alertas se evalua el cierre de la session y se cierra la ventana de HCE
			*   2. Se cambia la presentacion del nombre del paciente a Apellidos en mayuscula y nombre en Capitalize
			*   3. Se hace la evaluacion real del ingreso anterior respecto al formulario en operacion ya que no siempre
			*      en el ingreso inmedietamente anterior se deligencio el este formulario y no arrastra los datos tipo Referencia.
			*   4. Se cambia la evaluacion del llenado y firma del formulario tipo 2 ya que estaba operando de forma erronea.
			
	   .2011-10-13
			Se modifica el programa para permitir des-seleccionar un tipo seleccion por radio button cuando el usuario 
			por error hizo click sobre el.
			
	   .2011-10-10
			Se modifica el programa para agregarle en la columna de utilidades una opcion alterna de imprresion utilizando
			open window en lugar de divs flotantes porque estos ultimos en alguna impresoras comprimen la impresion.
			
	   .2011-09-29
			Se modifica el programa para que la opcion de impresion de HCE envie al programa x parametros la variable
			Servicio (wservicio). Se cambio la presentacion de los datos demografico y el mensaje de advertencia sobre
			la verificacion de la identificacion del paciente.
			
	   .2011-09-22
			Se modifica em programa en el query de las opciones de menu ya que la table 37 en lugar de tener como codigo
			el numero de la tabla tendra el numero de la opcion de menu de la tabla 9.
			
	   .2011-09-21
			Se modifica el programa para : poder trabajar la HCE en unidades ambulatorias, la modificacion es del arbol
			Se adiciona el tipo de documento y numero de documento en la demografia del paciente.
			Se adiciona mensaje de confirmacion de identificacion del paciente.
			
	   .2011-06-23
			Se modifica el programa para que los campos tipo Memo puedan ser utilizados para hacer ODBC a las aplicacones
			de Servinte utilizando los campos Archivo de Validacion (Detarc) para el origen de datos ODBC, el campo Formula
			(Detfor) para el query a informix y el campo Tipo Tabla (Dettta) con el indicador "M" para diferenciar los tipo
			Memo normales de los tipo Memo ODBC. Para el caso los que tienen en Dettta "M" son Memo ODBC, Estos campo no 
			permiter digitación por parte del usuario.
			
	   .2011-06-08
			Se modifica el programa para validar que los subtitulos se muestren teniendo en cuenta el sexo del paciente,
			adicionalmente se incluye la funcion wf que permite calcular dias a partir de fecha en los campos formula.
			
	   .2011-05-06
			Se modifica el programa para validar que la Cedula del paciente no contenga caracteres de control. En caso
			positivo se muestra un mensaje que le avisa al medico que compruebe si lo deja trabajar o en caso contrario
			avise a soporte sistemas.
			
			Se modifico la carga del HCE.js para que lo hiciera solamente una vez y NO por cada opcion del CASE de Opciones.
			
	   .2011-05-04
			Se modifica el programa para validar los valores maximos y minimos de los campos numericos.
			
	   .2011-04-25
			Se actualiza el programa para grabe la informacion de los formularios diligenciados con y sin firma para control
			posterior. igualmente se crea la posibilidad de usar el campo seleccion de forma parecida al compo tabla. Esto con
			el fin de facilitar la grabacion de diagnosticos.
			Se modifica la visualizacion de los campos segun el sexo para no mostrarlos en blanco y desperdiciar espacio en los
			formularios.
			
	   .2011-04-04
			Se modifico el programa para que mostrara todos los mensajes de error arriba y abajo, adicionalmente en javascript
			se modifico la pregunta para mostrar o no el icono de grabar, para que solo lo desaparezca en el momento en el
			que el programa grabe con el proposito de impedir la doble grabacion.
			
	   .2011-03-18
			Se valida que el numero de Historia e Ingreso existan o no esten en nulo.
			
	   .2011-02-14
			Ultimo release Beta.
			
	   .2011-02-07
			Ultimo release Beta.
	   		
	   .2009-07-09
	   		Release de Version Beta.
[*DOC]   		
***********************************************************************************************************************/

function consultarCcoInteroperabilidad($conex,$wdbmhos,$whis,$wing)
{
	$query = "SELECT Ubisac,Ccotio
				FROM ".$wdbmhos."_000018 
		  INNER JOIN ".$wdbmhos."_000011
				  ON Ccocod=Ubisac
				 AND Ccotio!=''
				 AND Ccotio!='NO APLICA'
				 AND Ccotio!='NO '
				 AND Ccotio!='.'
			   WHERE Ubihis='".$whis."' 
				 AND Ubiing='".$wing."';";
				 
	$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno() . " - en el query:  - " . mysqli_error());
	$num = mysql_num_rows($res);
	
	$ccoInteroperabilidad = false;
	if($num>0)
	{
		$ccoInteroperabilidad = true;
	}
	
	return $ccoInteroperabilidad;
}

function PR($con)
{
	global $orden;
	$PR = -1;
	for ($i=0;$i<count($orden);$i++)
	{
		if(substr($orden[$i][0],2,strlen($orden[$i][1])-1) == $con)
		{
			$PR = substr($orden[$i][1],2,strlen($orden[$i][1])-1);
			break;
		}
	}
	//echo "Posicion:".$PR."<br>";
	return (integer)$PR;
}

function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls]))
					return $ls;
				else
					return -1;
	}
	elseif($d[0] == $k)
			return 0;
		else
			return -1;
}

function Trepetidos($s)
{
	$s = str_replace("</OPTION>","</option>",$s);
	
	// --> 	2019-07-15: Jerson Trujillo, Se agregan las siguientes dos lineas, ya que estaban quedando caracteres basura
	//		en el valor del primer option y en la versión anterior solo se estaba eliminando el primer caracter.
	// 		Entonces se borra todo lo que este antes del primer <option. 
	$s = str_replace("<OPTION","<option",$s);	
	$s = strstr($s, "<option");
	
	if(substr($s,0,1) != "<")
		$s = substr($s,1);
	$op = explode("</option>",$s);
	$opt = array();
	$index = array();
	$k = -1;
	for ($i=0;$i<count($op)-1;$i++)
	{
		$option = trim(strip_tags($op[$i]));
		if(strpos($option,"-") !== false)
			$indexA = substr($option,strrpos($option,"-")+1,strpos($option," ")-strrpos($option,"-"));
		else
			$indexA = substr($option,0,strpos($option," "));
		$clave = array_search($indexA, $index);
		if($clave === False)
		{
			$k++;
			$opt[$k] = $option;
			$index[$k] = $indexA;
		}
	}
	for ($i=0;$i<count($opt);$i++)
		$res .= "<option value=".$opt[$i].">".$opt[$i]."</option>";
	return $res;
}

function buscarID($a,$i)
{
	for ($w=0;$w<count($a);$w++)
	{
		if(substr($a[$w],1) == $i)
			return true;
	}
	return false;
}

function BuscarAlertas($max,$iten,$arreglo)
{
	$k=0;
	for($i=0;$i<=$max;$i++)
	{
		if(strtoupper($arreglo[$i]) == strtoupper($iten))
			$k++;
	}
	if($k > 1)
		return false;
	return true;
}

function formula($wfor)
{
	$wforf="";
	$wsw=0;
	for ($w=0;$w<strlen($wfor);$w++)
	{
		if(strtoupper(substr($wfor,$w,1)) == "R" and $w+1 < strlen($wfor) and is_numeric(substr($wfor,$w+1,1)))
		{
			$wforf .= chr(36)."registro[";
			$wsw=1;
		}
		elseif(is_numeric(substr($wfor,$w,1)))
			{
				$wforf .= substr($wfor,$w,1);
			}
			elseif(!is_numeric(substr($wfor,$w,1)) and $wsw == 1)
				{
					$wforf .= "][0]".substr($wfor,$w,1);
					$wsw=0;
				}
				else
				{
					$wforf .= substr($wfor,$w,1);
				}
	}
	$formula=$wforf;
	return $formula;

}

function validacion($con,$tip,$des,$nse,$alm,$anm,&$configuracionE)
{
	if(strlen($nse) > 1 or !is_numeric($nse) or ($nse != "1" and $nse != "2"))
		$configuracionE .= "ERROR EN NIVEL DE SEGURIDAD CAMPO : ".$con." ".$des."<br>";
	if($tip == "Memo" and (!is_numeric($alm) or !is_numeric($anm)))
		$configuracionE .= "ERROR EN CONFIGURACION DE CAMPO MEMO : ".$con." ".$des."<br>";
}

function formula1($wfor)
{
	$wforf="";
	$wsw=0;
	for ($w=0;$w<strlen($wfor);$w++)
	{
		if(strtoupper(substr($wfor,$w,1)) == "C" and $w+1 < strlen($wfor) and is_numeric(substr($wfor,$w+1,1)))
		{
			$wforf .= chr(32).substr($wfor,$w,1);
			$wsw=1;
		}
		elseif(is_numeric(substr($wfor,$w,1)))
			{
				$wforf .= substr($wfor,$w,1);
			}
			elseif(!is_numeric(substr($wfor,$w,1)) and $wsw == 1)
				{
					$wforf .= chr(32).substr($wfor,$w,1);
					$wsw=0;
				}
				else
				{
					$wforf .= substr($wfor,$w,1);
				}
	}
	$formula=$wforf;
	return $formula;
}

function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function ver1($chain)
{
	if(strrpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strrpos($chain,"-"));
}
function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	if (ereg($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar2($chain)
{
	// Funcion que permite validar la estructura de un numero Entero
	$regular="^(\+|-)?([[:digit:]]+)$";
	if (ereg($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar3($chain)
{
	// Funcion que permite validar la estructura de una fecha
	$fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	if(ereg($fecha,$chain,$occur))
	{
		if($occur[2] < 0 or $occur[2] > 12)
			return false;
		if(($occur[3] < 0 or $occur[3] > 31) or 
		  ($occur[2] == 4 and  $occur[3] > 30) or 
		  ($occur[2] == 6 and  $occur[3] > 30) or 
		  ($occur[2] == 9 and  $occur[3] > 30) or 
		  ($occur[2] == 11 and $occur[3] > 30) or 
		  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or 
		  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
			return false;
		return true;
	}
	else
		return false;
}
function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="^([=a-zA-Z0-9' 'ñÑ@?#-.:;_<>])+$";
	return (ereg($regular,$chain));
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="^([0-9:])+$";
	return (ereg($regular,$chain));
}
function validar6($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >23 or $occur[2]<0 or $occur[2]>59)
			return false;
		else
			return true;
	else
		return false;
}
function validar7($chain)
{
	// Funcion que permite validar la estructura de un campo Hora Especial
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >24 or ($occur[2]!=0 and $occur[2]!=30))
			return false;
		else
			return true;
	else
		return false;
}
function validar8($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="/^([a-zA-Z0-9._-])+$/";
	return (preg_match($regular,$chain));
}
function wf($fecha,$dias)
{
	$ano=substr($fecha,0,4);
	$mes=substr($fecha,5,2);
	$dia=substr($fecha,8,2);
	$k=1;
	while($k < $dias)
	{
		$dia++;
		if(!checkdate($mes,$dia,$ano))
		{
			if($mes == 12)
			{
				$ano++;
				$mes=1;
				$dia=1;
			}
			else
			{
				$mes++;
				$dia=1;
			}
		}
		$k++;
	}
	if(strlen($mes) < 2)
		$mes="0".$mes;
	if(strlen($dia) < 2)
		$dia="0".$dia;
	return $ano."-".$mes."-".$dia;
}
function BuscarPre($conex,$Pre,$his,$ing)
{
	global $empresa;
	$query = "select count(*) FROM ".$empresa."_000036 where Firpro='".$Pre."' and Firhis='".$his."' and Firing='".$ing."' group by Firpro  ";
	$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row1 = mysql_fetch_array($err1);
	if ($row1[0] > 0)
		return true;
	else
		return false;
}
function Delete_Recovery($wformulario,$key,$whis,$wing)
{
	$datafile="/var/www/matrix/hce/Recovery/".$wformulario.$key.$whis.$wing.".txt";
	if(file_exists($datafile))
	{
		@chmod($datafile, 0777);
		unlink($datafile);
	}
}
function option($texto)
{
	$texto=str_replace("</OPTION>"," ",$texto);
	$texto=str_replace("</option>"," ",$texto);
	$imp=0;
	$def="";
	$ndiag=0;
	for ($z=0;$z<strlen($texto);$z++)
	{
		if(substr($texto,$z,1) == "<")
			$imp=1;
		if(substr($texto,$z,1) == ">")
		{
			$imp=0;
			$z++;
			$ndiag++;
			$def .= chr(13)."(".$ndiag.") ";
		}
		if($imp == 0)
		{
			$def .= substr($texto,$z,1);
		}
	}
	$def=str_replace("P-"," Presuntivo ",$def);
	$def=str_replace("C-"," Confirmado ",$def);
	$texto=$def;
	return $texto;
}

function option1($texto)
{
	$texto=str_replace("</OPTION>"," ",$texto);
	$texto=str_replace("</option>"," ",$texto);
	$imp=0;
	$def="";
	$ndiag=0;
	for ($z=0;$z<strlen($texto);$z++)
	{
		if(substr($texto,$z,1) == "<")
			$imp=1;
		if(substr($texto,$z,1) == ">")
		{
			$imp=0;
			$z++;
			$ndiag++;
			$def .= chr(13)."(".$ndiag.") ";
		}
		if($imp == 0)
		{
			$def .= substr($texto,$z,1);
		}
	}
	$texto=htmlentities($def);
	$npi=0;
	$def="";
	for ($z=0;$z<strlen($texto);$z++)
	{
		if(substr($texto,$z,1) == "(")
		{
			$imp=1;
			$npi++;
		}
		if(substr($texto,$z,1) == ")")
		{
			$imp=0;
			$z++;
			if($npi > 1)
				$def .= "</OPTION><OPTION>";
			else
				$def .= "<OPTION>";
		}
		if($imp == 0)
			$def .= substr($texto,$z,1);
	}
	$texto=$def;
	return $texto;
}

function vedad($edad,$tipo,$rangos)
{
	$vedad=1;
	if($tipo == "T")
		$vedad=1;
	else
		for ($i=0;$i<count($rangos);$i++)
		{
			if($tipo == $rangos[$i][0])
			{
				if($edad >= $rangos[$i][1] and $edad <= $rangos[$i][2])
					$vedad=1;
				else
					$vedad=0;
			}
		}
		return $vedad;		
}

function vespecial($logica)
{
	$logica = stripslashes ($logica);
	
	if(strlen($logica) < 2)
		$vespecial=1;
	else
		@eval($logica);
	return $vespecial;
}

include_once("hce/HCE_print_function.php");
include_once("hce/funcionesHCE.php");

@session_start();
if(!isset($_SESSION['user']))
{
	echo "<html>";
	echo "<head>";
	echo "<title>MATRIX HCE-Historia Clinica Electronica</title>";
	echo "</head>";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
	echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	echo "Su sesion ha expirado.  Por favor cierre esta ventana y recargue la pagina nuevamente.";
	echo "</BODY>";
	echo "</html>";
}
else
{
	echo "<html>";
	echo "<head>";
  	echo "<title>MATRIX HCE-Historia Clinica Electronica</title>";
  	echo "<link type='text/css' href='HCE2.css' rel='stylesheet'>"; 
  	echo "<!--[if IE 6]>";
	echo "<link type='text/css' href='HCE1.css' rel='stylesheet'>";
	echo "<![endif]-->";
	echo "<!--[if IE 7]>";
	echo "<link type='text/css' href='HCE1.css' rel='stylesheet'>";
	echo "<![endif]--> ";
	echo "<!--[if IE 8]>";
	echo "<link type='text/css' href='HCE1.css' rel='stylesheet'>";
	echo "<![endif]--> ";
	echo "<!--[if IE 9]>";
	echo "<link type='text/css' href='HCE1.css' rel='stylesheet'>";
	echo "<![endif]--> ";
	echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />";
	
  	
  	

	
 	
  			
	$key = substr($user,2,strlen($user));
	$queryCPU = "SELECT Msedes  from ".$empresa."_000014 ";
	$queryCPU .= " where Msetab = 'CPU' ";
	$errCPU = mysql_query($queryCPU,$conex) or die(mysql_errno().":".mysql_error());
	$numCPU = mysql_num_rows($errCPU);
	if ($numCPU > 0)
	{
		$rowCPU = mysql_fetch_array($errCPU);
		$CPU=$rowCPU[0];
		//echo "<script type='text/javascript' src='HCE_Seguridad.js' ></script>";
		echo "<script type='text/javascript' src='HCE.js?v=".md5_file('HCE.js')."' ></script>";
		echo "<script>";
		?>
		
		window.addEventListener("focusout", function() {
			
			if(event.target.type == "text" || event.target.type == "textarea")
			{
				if($(event.target).val()=="")
				{
					$(event.target).val(' ');
				}
			}
		});
		
		window.addEventListener("focusin", function() {
			// console.log(event.target.tagName)
			if(event.target.type == "text" || event.target.type == "textarea")
			{
				if($(event.target).val()==" ")
				{
					$(event.target).val('');
				}	
			}  
		});
		
		window.addEventListener("change", function() {
			// $(event.target).val($(event.target).val().replace(/&/g,"y"));

			var nameElement=$(event.target).attr('name');
			var tipoElemento = nameElement.split("[");
			if(tipoElemento[0]=="GRID")
			{
				$(event.target).val($(event.target).val().replace(/\*/g,"x"));
			}
		});
		
		function cerrar()
		{
			$.ajax({
			  type: "POST",
			  url: "/matrix/hce/procesos/HCE_close.php",
			  data: {"whce" : "0"},
			  async:false,
			  success: function(data){}
		  });
		}
		<?php
		echo "</script>";
		echo "<script>window.onbeforeunload = function(){ cerrar() };</script>";
		$_SESSION["HCEON"] = 1;
		
		switch($accion)
		{
			case "T":
				echo "</head>";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";
				echo "<div id='1'>";
				echo "<form name='HCE1' action='HCE.php' method=post>";
				
				echo "<table border=0 CELLSPACING=0>";
				echo "<tr><td align=center id=tipoT01><IMG SRC='/matrix/images/medical/root/HCE".$origen.".jpg'></td>";
				echo "<td id=tipoT02>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;HISTORIA CLINICA ELECTRONICA HCE&nbsp;&nbsp;<A HREF='/matrix/root/Reportes/DOC.php?files=/var/www/matrix/hce/procesos/HCE.php' target='_blank'>Version 2018-04-05</A></td></tr>";
				echo "<tr><td id=tipoT03 colspan=2></td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			
			case "U": 
				echo "</head>";
	  			echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	  			echo "<div id='1'>";
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE2' action='HCE.php' method=post>";
				$query = "select descripcion from usuarios where codigo = '".$key."'";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$wuser=$row[0];
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'origen' value='".$origen."'>";
				echo "<center><input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				echo "<body style='background-color:#C3D9FF' FACE='ARIAL'>";
				echo "<table border=0 CELLSPACING=0>";
				echo "<tr><td id=tipoL05>USUARIO : </td></tr>";
				echo "<tr><td id=tipoL05>".$wuser."</td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			
			case "A": 
				echo "<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>";				    
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>";
				echo "</head>";
	  			echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	  			echo "<div id='29'>";
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE3' action='HCE.php' method=post>";
				echo "<meta http-equiv='refresh' content='240;url=/matrix/HCE/procesos/HCE.php?accion=A&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' target='alergias'>";
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'origen' value='".$origen."'>";
				echo "<center><input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
				echo "<center><input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
				echo "<center><input type='HIDDEN' name= 'wtipodoc' value='".$wtipodoc."'>";
				if(!isset($_SESSION['user']))
				{
					echo '<script language="Javascript">';
					echo '		alert(\"LA SESION HA CADUCADO, ESTA VENTANA SE CERRARA. POR FAVOR RECARGE LA PAGINA PRINCIPAL E INGRESE NUEVAMENTE \");';
					echo '		top.close();';
					echo '</script>';
				}
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				echo "<body style='background-color:#FFDDDD' FACE='ARIAL'>";
				/*
				if(isset($okA))
				{
					$query =  " update ".$empresa."_".$wformulario." set movdat = 'UNCHECKED' ";
					$query .=  "  where fecha_data='".$wfecha."' and hora_data='".$whora."' and movpro='".$wformulario."' and movcon='6' and movhis='".$whis."' and moving='".$wing."'";
					$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA EN ALERTAS : ".mysql_errno().":".mysql_error());
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query  = " select ".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
					$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
					$query .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$wfecha."' "; 
					$query .= "   and ".$empresa."_".$wformulario.".hora_data = '".$whora."' ";
					$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
					$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
					$query .= "   and ".$empresa."_".$wformulario.".movcon = 7 ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$walerta = $row1[0]." Alerta desactivada por ".$key." en ".$fecha."  a las ".$hora;
					$query =  " update ".$empresa."_".$wformulario." set movdat = '".$walerta."' ";
					$query .=  "  where fecha_data='".$wfecha."' and hora_data='".$whora."' and movpro='".$wformulario."' and movcon='7' and movhis='".$whis."' and moving='".$wing."'";
					$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA EN ALERTAS : ".mysql_errno().":".mysql_error());
				}
				*/
				
				$query = "select Orihis,Oriing,Pacsex from root_000036,root_000037 ";
				$query .= " where pacced = '".$wcedula."'";
				$query .= "   and pactid = '".$wtipodoc."'";
				$query .= "   and  pacced = oriced ";
				$query .= "   and  pactid = oritid ";
				$query .= "   and oriori = '".$origen."' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err);
				if(isset($whisa))
				{
					$whis=$whisa;
					$wing=$winga;
				}
				else
				{
					$whis=$row[0];
					$wing=$row[1];
				}
				$wsex=$row[2];
				/*
				$query = "SELECT Encpro  from ".$empresa."_000001 ";
				$query .= " where Encale = 'on' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num > 0)
				{
					$knumw=-1;
					$matrixA=array();
					$checkA=array();
					$ALERTAS=array();
					$row = mysql_fetch_array($err);
					$wformulario=$row[0];
					$alto=0;
					$ancho=0;
					$width=0;
					echo "<center><table border=0>";
					echo "<tr><td id=tipoH00A colspan=4 onClick='javascript:activarModalIframe(\"".htmlentities("ALERTAS")."\",\"nombreIframe\",\"/matrix/HCE/Procesos/HCE.php?accion=W1&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wsex=".$wsex."&wtitframe=no&width=".$width."\",\"".$alto."\",\"".$ancho."\");'>ALERTAS</td></tr>";
					echo "<tr><td id=tipoH01A>DESCRIPCION</td><td id=tipoH01A><IMG SRC='/MATRIX/images/medical/HCE/cancel.png'></td></tr>";
					$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".hora_data,max(".$empresa."_".$wformulario.".movcon) as a from ".$empresa."_".$wformulario." "; 
					$query .= " where ".$empresa."_".$wformulario.".movhis='".$whis."' ";
					$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
					$query .= " group by 1,2  ";
					$query .= " having a >= 999 ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Detorp,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movtip from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
							$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
							$query .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$row[0]."' "; 
							$query .= "   and ".$empresa."_".$wformulario.".hora_data = '".$row[1]."' ";
							$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
							$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
							$query .= "   and ".$empresa."_".$wformulario.".movtip in ('Texto','Booleano') ";
							$query .= "   and ".$empresa."_".$wformulario.".movpro = ".$empresa."_000002.Detpro ";
							$query .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.Detcon ";
							$query .= "  order by 3 ";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$num1 = mysql_num_rows($err1);
							if ($num1>0)
							{
								$knumw++;
								for ($j=0;$j<$num1;$j++)
								{
									$row1 = mysql_fetch_array($err1);
									$matrixA[$knumw][0]=$row1[0];
									$matrixA[$knumw][1]=$row1[1];
									$matrixA[$knumw][$j+2]=$row1[3];
									$ALERTAS[$knumw]=$row1[3];
								}
							}
						}
					}
					$ia=-1;
					for ($i=0;$i<=$knumw;$i++)
					{
						if($matrixA[$i][2] == "CHECKED" and BuscarAlertas($i,$ALERTAS[$i],$ALERTAS))
						{
							$ia = $ia + 1;
							if($ia % 2 == 0)
							{
								$color="tipoH03A";
								$colorA="tipoL02M";
							}
							else
							{
								$color="tipoH02A";
								$colorA="tipoL02MW";
							}
							$walertas=$ALERTAS[$i];
							echo "<tr id='ALERT[".$i."]' title='FECHA : ".$matrixA[$i][0]." HORA : ".$matrixA[$i][1]."' onMouseMove='tooltipAlertas(".$i.")'>";
							echo "<td id=".$color.">".$ALERTAS[$i]."</td>";
							$id="ajaxalert('29','A','".$empresa."','".$origen."','".$wdbmhos."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$matrixA[$i][0]."','".$matrixA[$i][1]."','1')";
							echo "<td id=".$color."><input type='checkbox' name='alert[".$i."]' id='C".$i."' class=".$colorA." OnClick=".$id."></td>";
							echo "</tr>";
						}
						else
						{
							if($matrixA[$i][2] == "CHECKED")
							{
								$query =  " update ".$empresa."_".$wformulario." set movdat = 'UNCHECKED' ";
								$query .=  "  where fecha_data='".$matrixA[$i][0]."' and hora_data='".$matrixA[$i][1]."' and movpro='".$wformulario."' and movcon='6' and movhis='".$whis."' and moving='".$wing."'";
								$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA EN ALERTAS : ".mysql_errno().":".mysql_error());
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query  = " select ".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
								$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
								$query .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$matrixA[$i][0]."' "; 
								$query .= "   and ".$empresa."_".$wformulario.".hora_data = '".$matrixA[$i][1]."' ";
								$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
								$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
								$query .= "   and ".$empresa."_".$wformulario.".movcon = 7 ";
								$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
								$row1 = mysql_fetch_array($err1);
								$walerta = $row1[0]." Alerta desactivada por ".$key." en ".$fecha."  a las ".$hora;
								$query =  " update ".$empresa."_".$wformulario." set movdat = '".$walerta."' ";
								$query .=  "  where fecha_data='".$matrixA[$i][0]."' and hora_data='".$matrixA[$i][1]."' and movpro='".$wformulario."' and movcon='7' and movhis='".$whis."' and moving='".$wing."'";
								$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA EN ALERTAS : ".mysql_errno().":".mysql_error());
							}
						}
					}
					echo"</table>";
					$query  = "SELECT Karale FROM ".$wdbmhos."_000053 WHERE Karhis = '".$whis."' AND karale != '' GROUP BY karale ORDER BY Fecha_data DESC ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if ($num1>0)
					{
						echo "<br><center><table border=0>";
						echo "<tr><td id=tipoH00A colspan=4>ALERTAS EN KARDEX</td></tr>";
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							if($j % 2 == 0)
								$color="tipoH03A";
							else
								$color="tipoH02A";
							echo "<tr><td id=".$color.">".$row1[0]."</td></tr>";
						}
						echo"</table>";
					}
				}
				*/
				$estilosFrame = "width:248px;height:115px;";
				echo "<script type='text/javascript' src='../../../include/movhos/alertas.js?v=<?=md5_file('../../../include/movhos/alertas.js');?>'></script>";
				echo "<body style='background-color:#FFDDDD' FACE='ARIAL'>";
				echo "<script>";
				echo "llamarIframeAlerta('".$whis."','".$wing."','".$origen."','".$estilosFrame."',false,false,2)";
				echo "</script>";
				echo"</form>";
			break;
			
			case "F": 		
	  			echo "</head>";
	  			echo "<BODY TEXT='#000066' BGCOLOR='#E8EEF7' FACE='ARIAL'>";
	  			echo "<div id='1'>";
	  			$key = substr($user,2,strlen($user));
				echo "<form name='HCE4' action='HCE.php' method=post>";
				echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<input type='HIDDEN' name= 'origen' value='".$origen."'>";
				echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
				echo "<input type='HIDDEN' name= 'accion' value='".$accion."'>";
				echo "<input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
				echo "<input type='HIDDEN' name= 'wtipodoc' value='".$wtipodoc."'>";
				echo "<input type='HIDDEN' name= 'wservicio' value='".$wservicio."'>";
				$query = "select Orihis,Oriing from root_000037 ";
				$query .= " where oriced = '".$wcedula."'";
				$query .= "   and oritid = '".$wtipodoc."'";
				$query .= "   and oriori = '".$origen."' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					if(isset($whisa))
					{
						$whisF = $whisa;
						$wingF = $winga;
						$whis = $whisa;
						$wing = $winga;
					}
					else
					{
						$whisF = $row[0];
						$wingF = $row[1];
						$whis = $row[0];
						$wing = $row[1];
						$whisa = $row[0];
						$winga = $row[1];
					}
				}
				else
				{
					$whisF = "";
					$wingF = "";
					$whis = "";
					$wing = "";
					$whisa = "";
					$winga = "";
				}
				$MENU1=array();
				$numM=-1;
				//                                       0   1                       2                3                          4                           5                    6
				$query  = "select ".$empresa."_000021.Rararb,1,".$empresa."_000009.predes,".$empresa."_000009.preurl,".$empresa."_000009.prenod,".$empresa."_000009.premod,".$empresa."_000009.preext ";
				$query .= " from ".$empresa."_000020,".$empresa."_000021,".$empresa."_000009,".$empresa."_000037 ";
				$query .= "   where ".$empresa."_000020.Usucod = '".$key."' ";
				$query .= "	and ".$empresa."_000020.Usurol = ".$empresa."_000021.rarcod "; 
				$query .= "	and ".$empresa."_000021.Rargra = 'on' ";
				$query .= "	and ".$empresa."_000021.rararb = ".$empresa."_000009.precod "; 
				$query .= "	and ".$empresa."_000009.precod = ".$empresa."_000037.Forcod ";
				$query .= "	and ".$empresa."_000037.Forser = '".$wservicio."' ";
				$query .= " union all  ";
				$query .= " select ".$empresa."_000009.precod,0,".$empresa."_000009.predes,".$empresa."_000009.preurl,".$empresa."_000009.prenod,".$empresa."_000009.premod,".$empresa."_000009.preext  ";
				$query .= " from ".$empresa."_000009 ";  
				$query .= "  where ".$empresa."_000009.Prenod = 'on' ";
				$query .= "order by 1 ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$MENU1[$i][0] = $row[0];
						$MENU1[$i][1] = $row[1];
						$MENU1[$i][2] = $row[2];
						$MENU1[$i][3] = $row[3];
						$MENU1[$i][4] = $row[4];
						$MENU1[$i][5] = $row[5];
						$MENU1[$i][6] = $row[6];
					}
					for ($i=0;$i<$num;$i++)
					{
						$j=$num -1 -$i;
						if($j < ($num -1) and  $MENU1[$j+1][1] == 1 and strlen($MENU1[$j][0]) < strlen($MENU1[$j+1][0]) and $MENU1[$j][0] == substr($MENU1[$j+1][0],0,strlen($MENU1[$j][0])))
							$MENU1[$j][1]=1;
					}
					for ($i=0;$i<$num;$i++)
					{
						if($MENU1[$i][1] == 0) 
							for ($j=0;$j<$num;$j++)
							{
								if($j < ($num -1) and  $MENU1[$i][1] == 0 and $MENU1[$j][1] == 1 and strlen($MENU1[$i][0]) < strlen($MENU1[$j][0]) and $MENU1[$i][0] == substr($MENU1[$j][0],0,strlen($MENU1[$i][0])))
								{
									$MENU1[$i][1]=1;
									break;
								}
							}
					}
					$MENU=array();
					for ($i=0;$i<$num;$i++)
					{
						if($MENU1[$i][1] == 1)
						{
							$numM = $numM + 1;
							$MENU[$numM][0]=$MENU1[$i][0];
							$MENU[$numM][1]=$MENU1[$i][1];
							$MENU[$numM][2]=$MENU1[$i][2];
							$MENU[$numM][3]=$MENU1[$i][3];
							if(strtoupper(substr($MENU[$numM][3],0,2)) == "F=")
							{
								$wpostit = 99;
								if(strpos($MENU[$numM][3],"-") !== false)
									$wpostit = substr($MENU[$numM][3],strpos($MENU[$numM][3],"-")+1);
								$MENU[$numM][3]="/matrix/hce/Procesos/HCE.php?accion=M&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wformulario=".substr($MENU[$numM][3],2,6)."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&whis=".$whis."&wing=".$wing."&wpostit=".$wpostit."";
							}
							else
							{
								if(strpos($MENU[$numM][3],"CED") !== false)
								{
									$MENU[$numM][3]=str_replace("CED",$wcedula,$MENU[$numM][3]);
									$MENU[$numM][3]=str_replace("TDO",$wtipodoc,$MENU[$numM][3]);
								}
								if(strpos($MENU[$numM][3],"HIS") !== false)
								{
									$MENU[$numM][3]=str_replace("HIS",$whisF,$MENU[$numM][3]);
									$MENU[$numM][3]=str_replace("ING",$wingF,$MENU[$numM][3]);
									$MENU[$numM][3]=str_replace("FEC",date("Y-m-d"),$MENU[$numM][3]);
								}
							}
							$MENU[$numM][4]=$MENU1[$i][4];
							$MENU[$numM][5]=$MENU1[$i][5];
							$MENU[$numM][6]=$MENU1[$i][6];
						}
					}
				}
				echo "<center><B>FORMULARIOS</B><br>";
				echo "<IMG SRC='/matrix/images/medical/HCE/lupa.png'><input type='TEXT' name='bfor' size=30 maxlength=30 id='w01' class=tipo3 Onblur='enter()'>";
				echo "</center>";
				if(isset($bfor) and $bfor != "")
				{
					$kp = 0;
					echo "<table border=0 cellspacing=0 id=tipoM00>";
					for ($i=0;$i<$numM;$i++)
					{
						if(substr_count(strtolower($MENU[$i][2]),strtolower($bfor)) > 0 and $MENU[$i][4] == "off")
						{
							$kp++;
							if($MENU[$i][5] == "off")
								echo "<tr><td id='tipo3T'><IMG SRC='/matrix/images/medical/hce/puntoA.png' style='vertical-align:top;' id='img".$MENU[$i][0]."'></td><td id='tipo3T'><A HREF='javascript:recargaIframes(\"".$MENU[$i][3]."\",\"".$MENU[$i][3]."\")'>".htmlentities($MENU[$i][2])."</A></td></tr>";
							else
							{
								$alto=0;
								$ancho=0;
								if($MENU[$i][6] == "on")
									$alto=-1;
								echo "<tr><td id='tipo3T'><IMG SRC='/matrix/images/medical/hce/puntoA.png' style='vertical-align:top;' id='img".$MENU[$i][0]."'></td><td id='tipo3T'><A HREF='#' id='PROCESOS' onclick='javascript:activarModalIframe(\"".htmlentities($MENU[$i][2])."\",\"nombreIframe\",\"".$MENU[$i][3]."\",\"".$alto."\",\"".$ancho."\");'>".$MENU[$i][2]."</td></tr>";
							}
						}
					}
					if($kp == 0)
						echo "<tr><td id=tipoM03>SIN OCURRENCIAS</tr></td>";
					echo "</table>";
				}
				$numM++;
				$tables = 0;
				$lena = 0;
				echo "<br><table border=0 class=tipoTABLE1 CELLSPACING=0 CELLPADDING=2>";
				$tipo = "tipo1T";
				echo "<tr OnClick='enter()'><td id='".$tipo."'><IMG SRC='/matrix/images/medical/HCE/HCE.png'></td><td id='".$tipo."B' colspan=2>".$MENU[0][2]."</td></tr>";
				for ($i=1;$i<$numM;$i++)
				{
					$len = strlen($MENU[$i][0]) - 1;
					//echo $lena." ".$len."<br>";
					if($i % 2 == 0)
						$tipo = "tipo1T";
					else
						$tipo = "tipo2T";
					if($MENU[$i][4] == "on")
					{
						if($len < $lena)
						{
							$nlen = ($lena - $len) / 2;
							for ($j=0;$j<$nlen;$j++)
							{
								echo "</td></tr>";
								if($tables - 1 >= 0)
								{
									echo "</table>";
									$tables--;
								}
							}
						}
						echo "<tr OnClick='toggleDisplay1(div".$MENU[$i][0].",img".$MENU[$i][0].")'><td id='".$tipo."'><IMG SRC='/matrix/images/medical/hce/mas.png' style='vertical-align:top;' id='img".$MENU[$i][0]."'></td><td id='".$tipo."B'>".$MENU[$i][2]."</td></tr>";
						echo "<tr id='div".$MENU[$i][0]."' style='display:none'><td colspan=3>";
						echo "<table border=0 align=right width='95%' CELLSPACING=0 CELLPADDING=2>";
						$tables++;
					}
					else
					{
						if($len < $lena)
						{
							$nlen = ($lena - $len) / 2;
							for ($j=0;$j<$nlen;$j++)
							{
								echo "</td></tr>";
								if($tables - 1 >= 0)
								{
									echo "</table>";
									$tables--;
								}
							}
						}
						if($MENU[$i][5] == "off")
							echo "<tr><td id='".$tipo."' valign='top'><IMG SRC='/matrix/images/medical/hce/punto.png' style='vertical-align:top;' id='img".$MENU[$i][0]."'></td><td id='".$tipo."'><A HREF='javascript:recargaIframes(\"".$MENU[$i][3]."\",\"".$MENU[$i][3]."\")'>".htmlentities($MENU[$i][2])."</A></td></tr>";
						else
						{
							$alto=0;
							$ancho=0;
							if($MENU[$i][6] == "on")
								$alto=-1;
							echo "<tr><td id='".$tipo."' valign='top'><IMG SRC='/matrix/images/medical/hce/punto.png' style='vertical-align:top;' id='img".$MENU[$i][0]."'></td><td id='".$tipo."'><A HREF='#' id='PROCESOS' onclick='javascript:activarModalIframe(\"".htmlentities($MENU[$i][2])."\",\"nombreIframe\",\"".$MENU[$i][3]."\",\"".$alto."\",\"".$ancho."\");'>".htmlentities($MENU[$i][2])."</td></tr>";
						}
					}
					$lena = $len;
				}
				for ($j=0;$j<=$tables;$j++)
					echo "</td></tr></table>";
				echo "<center>";
				echo "<br><IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'><br>";
				echo "</center>";
				echo"</form>";
				
			break;
			
			case "D": 
				echo "<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>";				    
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>";
				
								
				echo "<script type='text/javascript'>";
				echo "function mueveReloj(){";
				echo "momentoActual = new Date();";
				echo "hora = momentoActual.getHours();";
				echo "minuto = momentoActual.getMinutes();";
				echo "segundo = momentoActual.getSeconds();";
				echo "thora = hora.toString();";
				echo "if(thora.length == 1){";
				echo "thora = '0' + thora;";
				echo "}";
				echo "tminuto = minuto.toString();";
				echo "if(tminuto.length == 1){";
				echo "tminuto = '0' + tminuto;";
				echo "}";
		   		echo "tsegundo = segundo.toString();";
				echo "if(tsegundo.length == 1){";
				echo "tsegundo = '0' + tsegundo;";
				echo "}";
				echo "horaImprimible = thora + \" : \" + tminuto + \" : \" + tsegundo;";
				echo "document.HCE5.reloj.value = horaImprimible;";
				echo "setTimeout('mueveReloj()',1000);";
	   			echo "}";
				echo "</script>";

				echo "</head>";
	  			echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	  			echo "<div id='1'>";
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE5' action='HCE.php' method=post>";
				echo "<body style='background-color:#FFFFFF'  onload='mueveReloj()' FACE='ARIAL'>";
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'origen' value='".$origen."'>";
				echo "<center><input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
				//                 0      1      2      3      4      5      6      7      8      9      10     11                12                             13
				$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom,".$wdbmhos."_000016.fecha_data,".$wdbmhos."_000016.hora_data from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
				$query .= " where pacced = '".$wcedula."'";
				$query .= "   and pactid = '".$wtipodoc."'";
				$query .= "   and pacced = oriced ";
				$query .= "   and pactid = oritid ";
				$query .= "   and oriori = '".$origen."' ";
				$query .= "   and inghis = orihis ";
				if(!isset($winga))
					$query .= "   and inging = oriing ";
				else
					$query .= "   and inging = '".$winga."' ";
				$query .= "   and ubihis = inghis "; 
				$query .= "   and ubiing = inging ";
				$query .= "   and ccocod = ubisac ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err);
				$sexo="MASCULINO";
				if($row[5] == "F")
					$sexo="FEMENINO";
				
				// $ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
				// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
				// $ann1=($aa - $ann)/360;
				// $meses=(($aa - $ann) % 360)/30;
				// if ($ann1<1)
				// {
					// $dias1=(($aa - $ann) % 360) % 30;
					// $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
				// }
				// else
				// {
					// $dias1=(($aa - $ann) % 360) % 30;
					// $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
				// }
				
				// ---------------
				
				$wedad = calcularEdadPaciente($row[4]);
				
				if(isset($whisa))
				{
					$whis = $whisa;
					$wing = $winga;
					$query = "select ".$wdbmhos."_000016.fecha_data,".$wdbmhos."_000016.hora_data from ".$wdbmhos."_000016 ";
					$query .= "  where inghis = '".$whisa."' "; 
					$query .= "    and inging = '".$winga."' ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$wfecing = $row1[0]." ".$row1[1];
				}
				else
				{
					$whis = $row[6];
					$wing = $row[7];
					$wfecing = $row[12]." ".$row[13];
				}
				$IDwpac=$wtipodoc." ".$wcedula;
				$wpac = htmlentities(strtoupper($row[2]))." ".htmlentities(strtoupper($row[3]))." ".ucfirst(strtolower(htmlentities($row[0])))." ".ucfirst(strtolower(htmlentities($row[1])));
				$dia=array();
				$dia["Mon"]="Lun";
				$dia["Tue"]="Mar";
				$dia["Wed"]="Mie";
				$dia["Thu"]="Jue";
				$dia["Fri"]="Vie";
				$dia["Sat"]="Sab";
				$dia["Sun"]="Dom";
				$mes["Jan"]="Ene";
				$mes["Feb"]="Feb";
				$mes["Mar"]="Mar";
				$mes["Apr"]="Abr";
				$mes["May"]="May";
				$mes["Jun"]="Jun";
				$mes["Jul"]="Jul";
				$mes["Aug"]="Ago";
				$mes["Sep"]="Sep";
				$mes["Oct"]="Oct";
				$mes["Nov"]="Nov";
				$mes["Dec"]="Dic";
				$fechal=strftime("%a %d de %b del %Y");
				$fechal=$dia[substr($fechal,0,3)].substr($fechal,3);
				$fechal=substr($fechal,0,10).$mes[substr($fechal,10,3)].substr($fechal,13);
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				$index=1001;
				$ancho=1250;
				$alto=330;
				$estilosInfoDemografica = "style='font-size:10pt;'";
				echo "<table border=0>";
				echo "<tr><td id=tipoL01C ".$estilosInfoDemografica.">ID Paciente</td><td colspan=1 id=tipoL02C ".$estilosInfoDemografica.">".$IDwpac."</td><td id=tipoL01C ".$estilosInfoDemografica.">Paciente</td><td colspan=2 id=tipoL04 ".$estilosInfoDemografica.">".$wpac."</td><td id=tipoL04A ".$estilosInfoDemografica.">".$fechal."<input type='text' name='reloj' size='10' readonly='readonly' class=tipo3R ".$estilosInfoDemografica."></td></tr>";
				echo "<tr><td id=tipoL01C ".$estilosInfoDemografica.">Historia Clinica</td><td id=tipoL02C ".$estilosInfoDemografica.">".$whis."-".$wing."</td><td id=tipoL01 ".$estilosInfoDemografica.">Edad actual</td><td id=tipoL02C ".$estilosInfoDemografica.">".$wedad."</td><td id=tipoL01C ".$estilosInfoDemografica.">Sexo</td><td id=tipoL02C ".$estilosInfoDemografica.">".$sexo."</td></tr>";
				echo "<tr><td id=tipoL01C ".$estilosInfoDemografica.">Fecha/Hora Ingreso</td><td id=tipoL02C ".$estilosInfoDemografica.">".$wfecing."</td><td id=tipoL01C ".$estilosInfoDemografica.">Habitacion</td><td id=tipoL02C ".$estilosInfoDemografica.">".$row[10]."</td><td id=tipoL01C ".$estilosInfoDemografica.">Entidad</td><td id=tipoL02C ".$estilosInfoDemografica.">".htmlentities($row[8])."</td></tr>";
				if(!isset($wtitulo))
					$wtitulo="";
				if(!isset($wservicio))
					$wservicio = "*";
				
				$path0="/matrix/HCE/Procesos/HCE.php?accion=W2&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&whis=".$whis."&wing=".$wing;
				// CENTRO DE IMPRESION
				$path1="/matrix/HCE/procesos/solimp.php?wemp_pmla=".$origen."&whis=".$whis."&wing=".$wing."&wservicio=".$wservicio;
				$path2="/matrix/HCE/procesos/HCE_Notas.php?empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&whis=".$whis."&wing=".$wing."&wservicio=".$wservicio;
				$path3="/matrix/HCE/procesos/HCE_Historico.php?empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&whis=".$whis."&wing=".$wing;
				$path4="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&whis=".$whis."&wing=".$wing."&protocolos=0&CLASE=C&noCentrar=true";
				$path5="/matrix/HCE/procesos/HCE_NotasC.php?empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&whis=".$whis."&wing=".$wing;
				$path6="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&whis=".$whis."&wing=".$wing."&protocolos=1&CLASE=I";
				$path7="/matrix/HCE/procesos/envioCorreoHCEOrdenes.php?wemp_pmla=".$origen."&historia=".$whis."&ingreso=".$wing."&esIframe=on";
				$path8="/matrix/HCE/procesos/visorOrdenes.php?empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wservicio=".$wservicio."&whis=".$whis."&wing=".$wing;
				
				$mostrarVisorResultados = consultarAliasPorAplicacionHCE($conex,$origen,"mostrarVisorResultadosHCE");
				$visorResultados = "";
				if($mostrarVisorResultados=="on")
				{
					$usuariosHabilitadosVisorResultadosHCE = consultarAliasPorAplicacionHCE($conex,$origen,"usuariosHabilitadosVisorResultadosHCE");
					
					$mostrarVisorUsuario = false;
					if($usuariosHabilitadosVisorResultadosHCE=="*")
					{
						$mostrarVisorUsuario = true;
					}
					else
					{
						$usuariosVisorResultadosHCE = explode(",",$usuariosHabilitadosVisorResultadosHCE);
						
						$mostrarVisorUsuario = in_array($key,$usuariosVisorResultadosHCE);
					}
					
					if($mostrarVisorUsuario)
					{
						$ccoInteroperabilidad = consultarCcoInteroperabilidad($conex,$wdbmhos,$whis,$wing);
						if($ccoInteroperabilidad)
						{
							$visorResultados = "<A HREF='#' id='VISORRESULTADOS' onclick='javascript:activarModalIframe(\"VISOR DE RESULTADOS\",\"nombreIframe\",\"".$path8."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/visorResultados.png' id='ICONOS[9]' style='background-color:#5998cf;' title='Visor de resultados'  onMouseMove='tooltipIconos(9)'></A>";
						}
					}
				}
				
				$mostrarEnvioCorreo = consultarAliasPorAplicacionHCE($conex,$origen,"mostrarIconoEnvioHCEyOrdenes");
				$envioCorreo = "";
				if($mostrarEnvioCorreo=="on")
				{
					$envioCorreo = "<A HREF='#' id='ENVIOCORREO' onclick='javascript:activarModalIframe(\"ENVIO HCE Y ORDENES\",\"nombreIframe\",\"".$path7."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/emailHCE.png' id='ICONOS[8]' title='Envio HCE y Ordenes'  onMouseMove='tooltipIconos(8)' ></A>&nbsp;";
				}
				
				echo "<tr><td colspan=4 id=tipoL04C><input type='TEXT' name='txtformulario' id='txtformulario' size=1 value='' readonly='readonly' class=tipo3TW><input type='TEXT' name='txttitulo' id='txttitulo' size=45 value='".$wtitulo."' readonly='readonly' class=tipo3T></td><td colspan=2 id=tipoL03C>";
				echo "<A HREF='#' id='btnModal".$index."' name='btnModal".$index."'  onClick='javascript:mostrarFlotante(\"\",\"nombreIframe\",\"/matrix/HCE/Procesos/HCE.php?accion=W2&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&whis=".$whis."&wing=".$wing."&wtitframe=no\",\"".$alto."\",\"".$ancho."\");'><IMG SRC='/matrix/images/medical/HCE/hceR.png' id='ICONOS[1]' title='Otros Registros Asociados'  onMouseMove='tooltipIconos(1)' style='width:40px;'></A>&nbsp;";
				//echo "<A HREF='#' id='REGISTROS ASOCIADOS' onclick='javascript:activarModalIframe(\"REGISTROS ASOCIADOS\",\"nombreIframe\",\"".$path0."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceR.png' id='ICONOS[1]' title='Otros Registros Asociados'  onMouseMove='tooltipIconos(1)'></A>&nbsp;&nbsp;";
				echo "<A HREF='#' id='VISTA PRELIMINAR' onclick='javascript:activarModalIframe(\"VISTA PRELIMINAR\",\"nombreIframe\",\"".$path1."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceP.png' id='ICONOS[2]' title='Vista Preliminar'  onMouseMove='tooltipIconos(2)' style='width:40px;'></A>&nbsp;";
				echo "<A HREF='#' id='NOTAS' onclick='javascript:activarModalIframe(\"NOTAS\",\"nombreIframe\",\"".$path2."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceN.png' id='ICONOS[3]' title='Notas Complementarias'  onMouseMove='tooltipIconos(3)' style='width:40px;'></A>&nbsp;";
				echo "<A HREF='#' id='GRAFICAS'  onclick='javascript:activarModalIframe(\"GRAFICAS\",\"nombreIframe\",\"".$path3."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceG.png' id='ICONOS[4]' title='Graficos de Registros Numericos'  onMouseMove='tooltipIconos(4)' style='width:40px;'></A>&nbsp;";
				echo "<A HREF='#' id='CONSULTAS'  onclick='javascript:activarModalIframe(\"CONSULTAS\",\"nombreIframe\",\"".$path4."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceB.png' id='ICONOS[5]' title='Consultas'  onMouseMove='tooltipIconos(5)' style='width:40px;'></A>&nbsp;";
				echo "<A HREF='#' id='NOTASC'  onclick='javascript:activarModalIframe(\"NOTAS CONFIRMATORIAS\",\"nombreIframe\",\"".$path5."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/NOC.png' id='ICONOS[6]' title='Notas Confirmatorias'  onMouseMove='tooltipIconos(6)'></A>&nbsp;";
				echo "<A HREF='#' id='IMPRESION' onclick='javascript:activarModalIframe(\"IMPRESION DE PAQUETES\",\"nombreIframe\",\"".$path6."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceQ.png' id='ICONOS[7]' title='Impresion de Paquetes'  onMouseMove='tooltipIconos(7)' ></A>&nbsp;";
				echo $envioCorreo;
				echo $visorResultados;
				echo "</td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			
			case "M": 			
				echo "</head>";
				echo "<BODY TEXT='#000000' FACE='ARIAL' onLoad='setInterval( \"parpadear()\", 1500 );'>";
				echo "<div id='1'>";
				if(isset($wformulario))
				{
					$query = "select Orihis,Oriing,Pacsex from root_000036,root_000037 ";
					$query .= " where pacced = '".$wcedula."'";
					$query .= "   and pactid = '".$wtipodoc."'";
					$query .= "   and pacced = oriced ";
					$query .= "   and pactid = oritid ";
					$query .= "   and oriori = '".$origen."' ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					if(isset($whis))
					{
						$whis=$whis;
						$wing=$wing;
					}
					else
					{
						$whis=$row[0];
						$wing=$row[1];
					}
					$wsex=$row[2];
					
					$query = "SELECT Enccol, Encnfi, Encnco   from ".$empresa."_000001 ";
					$query .= " where Encpro = '".$wformulario."' ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$wcolf=$row[0];
					$span1=$row[2];
					$span2=$row[1];
					if(isset($LABELSMART))
						echo "<iframe src='HCE.php?accion=W1&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wsex=".$wsex."&wpostit=".$wpostit."&width=".$span1."&LABELSMART=".$LABELSMART."' name='titulos' marginwidth=0 scrolling='no' framespacing='0' frameborder='0' border='0'  border='0' height='".$span2."' width='".$span1."'  marginheiht=0>";
					else
						echo "<iframe src='HCE.php?accion=W1&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wsex=".$wsex."&wpostit=".$wpostit."&width=".$span1."' name='titulos' marginwidth=0 scrolling='no' framespacing='0' frameborder='0' border='0'  border='0' height='".$span2."' width='".$span1."'  marginheiht=0>";
					echo "</iframe>";
				}
				else
				{
					echo "<table border=0>";
					echo "<tr><td id=tipoL03>BIENVENIDO AL PROGRAMA DE HISTORIA CLINICA ELECTRONICA (HCE)</td></tr>";
					echo "<tr><td id=tipoL02><IMG SRC='/matrix/images/medical/hce/adv.png' style='vertical-align:middle;'>&nbsp;&nbsp;POR FAVOR VERIFIQUE LA IDENTIFICACION DEL PACIENTE ANTES DE REALIZAR ALGUN REGISTRO</td></tr>";
					echo "<tr><td id=tipoL02>ESTO CONTRIBUYE A LA SEGURIDAD EN EL PROCESO DE ATENCION!!!  GRACIAS</td></tr>";
					echo "<tr><td id=tipoL02W><IMG SRC='/matrix/images/medical/hce/alerta.png' style='vertical-align:middle;'>&nbsp;&nbsp;<BLINK>RECUERDE : PARA DILIGENCIAR UN FORMULARIO SIEMPRE DEBE FIRMARLO. LA FIRMA ES OBLIGATORIA.  GRACIAS</BLINK></td></tr>";
					if(isset($wcedula) and !validar8($wcedula))
					{
						echo "<input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
						echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/cabeza.gif'></td><tr>";
						echo "<tr><td id=tipoL03X>LA CEDULA DEL PACIENTE CONTIENE CARACTERES NO PERMITIDOS !!! ".$wcedula."</td></tr>";
						echo "<tr><td id=tipoL03X>SI EL PROGRAMA NO LE PERMITE TRABAJAR, COMUNIQUELE AL DE ADMISIONES</td></tr>";
						echo "<tr><td id=tipoL03X>PARA QUE HAGA LA CORRECCION EN LA APLICACION DE SERVINTE</td></tr>";
						echo "<tr><td id=tipoL03X>SI EL PROBLEMA PERSISTE, LLAME A SOPORTE SISTEMAS. GRACIAS</td></tr>";
					}
					/*if(file_exists("/var/www/matrix/images/medical/hce/banner".$origen.".png"))
						echo "<tr><td align=center><br><IMG SRC='/matrix/images/medical/hce/banner".$origen.".png'></td></tr>";*/
					
					//este de aca se coloca cuando la imagen tiene un link  y se activa este y se comenta el anterior		
					if(file_exists("/var/www/matrix/images/medical/hce/banner".$origen.".png"))
						{//echo "<tr><td align=center><br><IMG SRC='/matrix/images/medical/hce/banner".$origen.".png'></td></tr>";
							echo "<tr><td align=center><br><IMG SRC='/matrix/images/medical/hce/bannerotro".$origen.".png'></td></tr>";
							echo "<tr><td align=center><br>
						    <a href = 'https://forms.gle/6hgxRidfk7xH2xef7' target='_blank'><IMG SRC='/matrix/images/medical/hce/banner".$origen.".png'></a>
						   </td></tr>";
						}
					echo "</table>";
					echo '<script language="Javascript">';
					//echo "     debugger;";
					echo '		var obj = parent.parent.demograficos.document.getElementById("txttitulo");';
					echo '		if(obj)';
					echo '		{';
					echo "			obj.value = '';";
					echo '		} ';
					echo '		var obj1 = parent.parent.demograficos.document.getElementById("txtformulario");';
					echo '		if(obj1)';
					echo '		{';
					echo "			obj1.value = '';";
					echo '		} ';
					echo '</script>';
				}
			break;
			
			case "UT": 
				echo "</head>";
				echo "<body style='background-color:#E8EEF7;color:#000000;' FACE='ARIAL'>";
				echo "<div id='1'>";
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'origen' value='".$origen."'>";
				echo "<center><input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
				$query = "select Orihis,Oriing from root_000036,root_000037 ";
				$query .= " where pacced = '".$wcedula."'";
				$query .= "   and pactid = '".$wtipodoc."'";
				$query .= "   and  pacced = oriced ";
				$query .= "   and  pactid = oritid ";
				$query .= "   and oriori = '".$origen."' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err);
				if(isset($whisa))
				{
					$whis=$whisa;
					$wing=$winga;
				}
				else
				{
					$whis=$row[0];
					$wing=$row[1];
				}
				echo "<center><table border=0>";
				$query = "select Butcod, Butcid, Butpat, Butimg, Butest from ".$empresa."_000044 ";
				$query .= " where Butest = 'on'";
				$query .= "   order by 1 ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<tr><td align=center id=tipoUT1>AYUDAS</td></tr>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(strpos($row[2],"CED") !== false)
						{
							$row[2]=str_replace("CED",$wcedula,$row[2]);
							$row[2]=str_replace("TDO",$wtipodoc,$row[2]);
						}
						@eval($row[2]);
						if(substr($row[1],0,1) != "$")
							echo "<tr><td align=center id=tipoUT2><A HREF='#' id='".$row[1]."' onclick='javascript:activarModalIframe(\"".$row[1]."\",\"nombreIframe\",\"".$path."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/".$row[3]."' id='UT[".$i."]' title='".$row[1]."'  onMouseMove='tooltipUT(".$i.")'></A></td></tr>";
						else
						{
							$row[1]=substr($row[1],1);
							echo "<tr><td align=center id=tipoUT2><A HREF='".$path."' target='_blank'><IMG SRC='/matrix/images/medical/HCE/".$row[3]."'  id='UT[".$i."]' title='".$row[1]."'  onMouseMove='tooltipUT(".$i.")'></A></td></tr>";
						}
					}
				}
				echo "</table></center>";
			break;
			
			case "W1": 
				echo "<!-- BEGIN: load jqplot -->";
				echo "<script language='javascript' type='text/javascript' src='../../../include/root/Tipmage.js'></script>";
				echo "<link rel='stylesheet' type='text/css' href='../../../include/root/Tipmage.css' />";
				echo "<!-- END: load jqplot -->";
				echo "<link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.theme.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.datepicker.css' rel='stylesheet'>";
				
				echo "<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/jquery.autocomplete.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/jquery.jTPS.css' rel='stylesheet'>";
				    
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.draggable.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>";

				echo "<script type='text/javascript' src='../../../include/root/ui.datepicker.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.accordion.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.dimensions.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.maskedinput.js' ></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.jTPS.js'></script>";
				
				include_once("hce/AlertasFormulariosHCE.php");
				mostrarAlertaHCE($conex,$origen,$wformulario,$wdbmhos,$whis,$wing,$key);
				
				
				echo "<script type='text/javascript'>";
				echo "$(document).ready(function(){  init_jquery(); }); ";
				echo "</script>";

				echo "</head>";
				echo "<BODY TEXT='#000000' onLoad='pintardivs();' FACE='ARIAL'>";
			    
			    
			    
				echo "<div id='19'>";
				$key = substr($user,2,strlen($user));
				
				echo "<form name='HCE6' action='HCE.php' method=post>";
				echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'origen' value='".$origen."'>";
				echo "<center><input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
				echo "<center><input type='HIDDEN' name= 'wformulario' value='".$wformulario."'>";
				echo "<center><input type='HIDDEN' name= 'whis' value='".$whis."'>";
				echo "<center><input type='HIDDEN' name= 'wing' value='".$wing."'>";
				echo "<center><input type='HIDDEN' name= 'wpostit' value='".$wpostit."'>";
				if(isset($LABELSMART))
					echo "<center><input type='HIDDEN' name= 'LABELSMART' value='".$LABELSMART."'>";
				if(!isset($wsa))
				{
					$datafile="/var/www/matrix/hce/Recovery/".$wformulario.$key.$whis.$wing.".txt";
					if(file_exists($datafile))
					{
						$wrecovery=1;
						if(isset($RECOV))
						{
							if($RECOV == 2)
							{
								$wrecovery=0;
								if(file_exists($datafile))
								{
									@chmod($datafile, 0777);
									unlink($datafile);
								}
							}
							else
								$wrecovery=2;
						}
						else
						{
							$gestor = @fopen("/var/www/matrix/hce/Recovery/".$wformulario.$key.$whis.$wing.".txt", "r");
							if ($gestor) 
							{
								$bufferF="";
								$bufferH="";
								while (!feof($gestor)) 
								{
									$buffer = fgets($gestor);
									if(strlen($buffer) > 0)
									{
										$wbuff=1;
										if(is_numeric (substr($buffer,0,strpos($buffer,"-"))) and strpos($buffer,"-") !== false)
										{
											$wbuff=0;
											$posrec = (integer)substr($buffer,0,strpos($buffer,"-"));
											if($posrec == "1")
												$bufferF = substr($buffer,strpos($buffer,"-")+1);
											elseif($posrec == "2")
													$bufferH = substr($buffer,strpos($buffer,"-")+1);
										}
									}
								}
								if($bufferF != "" and $bufferH != "")
								{
									// $query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data, ".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
									// $query .= " where ".$empresa."_".$wformulario.".movpro = '".$wformulario."' ";
									// $query .= "   and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
									// $query .= "   and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
									// $query .= "   and ".$empresa."_".$wformulario.".movtip = 'Hora' ";
									// $query .= "   and ".$empresa."_".$wformulario.".movdat = '".trim($bufferH)."' ";
									// $query .= "  Order by id ";
									
									// 2018-08-06: Se modifica el query, se agrega join con hce_000002 para agregar el consecutivo al query y así tome el indice conhising_idx
									$query  = " SELECT ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data, ".$empresa."_".$wformulario.".movdat 
												  FROM ".$empresa."_000002,".$empresa."_".$wformulario."
												 WHERE Detpro='".$wformulario."' 
												   AND Dettip='Hora'
												   AND ".$empresa."_".$wformulario.".movpro = Detpro
												   AND ".$empresa."_".$wformulario.".movhis = '".$whis."' 
												   AND ".$empresa."_".$wformulario.".moving = '".$wing."' 
												   AND ".$empresa."_".$wformulario.".movcon = Detcon 
												   AND ".$empresa."_".$wformulario.".movdat = '".trim($bufferH)."' 
											  ORDER BY ".$empresa."_".$wformulario.".id;";
											  
									$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
									$num = mysql_num_rows($err);
									if($num > 0)
									{
										$row = mysql_fetch_array($err);
										$wfechaD = $row[0];
										$whoraD = $row[1];
										$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data, ".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
										$query .= " where ".$empresa."_".$wformulario.".movpro = '".$wformulario."' ";
										$query .= "   and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
										$query .= "   and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
										$query .= "   and ".$empresa."_".$wformulario.".movtip = 'Fecha' ";
										$query .= "   and ".$empresa."_".$wformulario.".Fecha_data = '".$wfechaD."' ";
										$query .= "   and ".$empresa."_".$wformulario.".Hora_data = '".$whoraD."' ";
										$query .= "  Order by id ";
										$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$num = mysql_num_rows($err);
										if($num > 0)
										{
											$row = mysql_fetch_array($err);
											if(trim($bufferF) == $row[2])
											{
												$wrecovery=0;
												if(file_exists($datafile))
												{
													//fclose($datafile);
													@chmod($datafile, 0777);
													unlink($datafile);
												}
											}
										}
									}
								}
								fclose($gestor);
							}
						}
					}
				}
				$Reta=array();
				$query = "select Raecod, Raerin, Raerfi from ".$empresa."_000041 ";
				$query .= " where Raeest = 'on'";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$numS = mysql_num_rows($err);
				if ($numS>0)
				{
					for ($i=0;$i<$numS;$i++)
					{
						$row = mysql_fetch_array($err);
						$Reta[$i][0] = $row[0];
						$Reta[$i][1] = $row[1];
						$Reta[$i][2] = $row[2];
					}
				}
				
				if(isset($wsex) and isset($wedad))
				{
					echo "<input type='HIDDEN' name= 'wsex' value='".$wsex."' id='wsex'>";
					echo "<input type='HIDDEN' name= 'wedad' value='".$wedad."' id='wedad'>";
				}
				else
				{
					$query = "select Pacsex, Pacnac from root_000036 ";
					$query .= " where pacced = '".$wcedula."'";
					$query .= "   and pactid = '".$wtipodoc."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$wsex=$row[0];
					$ann=(integer)substr($row[1],0,4)*360 +(integer)substr($row[1],5,2)*30 + (integer)substr($row[1],8,2);
					$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
					$wedad=($aa - $ann);
					echo "<input type='HIDDEN' name= 'wsex' value='".$wsex."' id='wsex'>";
					echo "<input type='HIDDEN' name= 'wedad' value='".$wedad."' id='wedad'>";
				}
			
				
				
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				$mess="";
				
				echo "<body style='background-color:#ffffff' FACE='ARIAL'>";
				$PreR=true;
				$query = "select Precpr from ".$empresa."_000004 ";
				$query .= " where Prepro = '".$wformulario."'";
				$query .= "   and Preest = 'on' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$numPr = mysql_num_rows($err);
				$msgPre="";
				if ($numPr>0)
				{
					$PreR=false;
					$row = mysql_fetch_array($err);
					$Prereq=explode(",",$row[0]);
					for ($i=0;$i<count($Prereq);$i++)
					{
						if(BuscarPre($conex,$Prereq[$i],$whis,$wing))
						{
							$PreR=true;
							break;
						}
						else
						{
							$query = "select Encdes from ".$empresa."_000001 ";
							$query .= " where Encpro = '".$Prereq[$i]."'";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$row1 = mysql_fetch_array($err1);
							if($i == 0)
								$msgPre .= "(".$row1[0];
							else
								$msgPre .= ", ".$row1[0];
						}
					}
					$msgPre .= ")";
					if($i == 0)
						$msgPre = "EL FORMULARIO <b>".$msgPre."</b> NO HA SIDO DILIGENCIADO Y ES PREREQUISITO DE ESTE FORMULARIO.<br>";
					else
						$msgPre = "LOS FORMULARIOS <b>".$msgPre."</b> NO HAN SIDO DILIGENCIADOS Y AL MENOS UNO DE ELLOS ES PREREQUISITO DE ESTE FORMULARIO.<br>";
				}
				if(isset($wrecovery) and $wrecovery == 1)
				{
					$query = "select Encdes from ".$empresa."_000001 ";
					$query .= " where Encpro = '".$wformulario."'";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$wtitulo = $row1[0];
					// if($wpostit != 99)
					if($wpostit != 99 && $wpostit!="")
					{
						$wptitulos = explode("|", $wtitulo);
						$wtitulo = $wptitulos[$wpostit - 1];
					}
					echo '<script language="Javascript">';
					echo '		var obj = parent.parent.demograficos.document.getElementById("txttitulo");';
					echo '		if(obj)';
					echo '		{';
					echo "			var divAux = document.createElement( 'div');";
					echo "			divAux.innerHTML = '".htmlentities($wtitulo)."';";
					echo "			obj.value = divAux.innerHTML.toUpperCase();";
					echo '		} ';
					echo '		var obj1 = parent.parent.demograficos.document.getElementById("txtformulario");';
					echo '		if(obj1)';
					echo '		{';
					echo "			var divAux = document.createElement( 'div');";
					echo "			divAux.innerHTML = '".htmlentities($wformulario)."';";
					echo "			obj1.value = divAux.innerHTML.toUpperCase();";
					echo '		} ';
					echo '</script>';
					echo "<table border=0 cellspacing=0>";
					echo "<tr><td rowspan=4 align=center><IMG SRC='/MATRIX/images/medical/hce/Recovery.jpg'></IMG></td><td id=tipoL06REC>RECUPERACI&Oacute;N DE INFORMACI&Oacute;N</td></tr>";
					echo "<tr><td id=tipoL02REC>Usted Ha Digitado Informaci&oacute;n En Este Formulario Que Aun No Ha Grabado</td></tr>";
					echo "<tr><td id=tipoL02REC>Desea Recuperarla ??</td></tr>";
					$id="ajaxrecov('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."')";
					echo "<tr><td id=tipoL06REC><input type='RADIO' name='RECOV' id='RECOV1' value=1 OnClick=".$id.">SI<input type='RADIO' name='RECOV' id='RECOV1' value=2 OnClick=".$id.">NO</td></tr>";
				}
				else
				{
					if($PreR)
					{
						if($whis != "" and $wing != "")
						{
							if(isset($ok) AND $ok == "CHECKED")
							{
								//                  0       1       2      3       4       5       6       7       8       9       10      11      12      13
								$query = "SELECT Detcon, Dettip, Detarc, Detobl, Detdes, Detase, Dettta, Detfor, Detved, Detvmi, Detvma, Dettii, Deturl, Detves from ".$empresa."_000002 ";
								$query .= " where Detpro = '".$wformulario."' ";
								$query .= "   and Detest = 'on' ";
								$query .= " Order by Detorp ";
								$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
								$numW = mysql_num_rows($err);
								if ($numW>0)
								{
									if(!isset($DATA))
										$DATA=array();
									$orden=array();
									for ($i=0;$i<$numW;$i++)
									{
										$row = mysql_fetch_array($err);
										
										// Si el campo Detfor tiene . o NO APLICA debe ser vacio para evitar errores con el eval
										if($row[7] == "." || $row[7] == "NO APLICA")
										{
											$row[7] = "";
										}
										
										// Si el campo Detves tiene . o NO APLICA debe ser vacio para evitar errores con el eval
										if($row[13] == "." || $row[13] == "NO APLICA")
										{
											$row[13] = "";
										}
										
										$DATA[$i][0]=$row[0];
										$DATA[$i][1]=$row[1];
										$DATA[$i][2]=$row[2];
										$DATA[$i][3]=$row[3];
										$DATA[$i][4]=$row[4];
										$DATA[$i][5]=$row[5];
										$DATA[$i][6]=$row[6];
										$DATA[$i][7]=$row[7];
										$DATA[$i][8]=$row[8];
										$DATA[$i][9]=$row[9];
										$DATA[$i][10]=$row[10];
										$DATA[$i][11]=$row[11];
										$DATA[$i][12]=$row[12];
										$DATA[$i][13]=vespecial($row[13]);
										$orden[$i][0]=chr(32)."C".$row[0].chr(32);
										$orden[$i][1]=chr(32)."R".$i.chr(32);
									}
								}
								if(substr($firma,0,3) == "HCE")
								{
									$wswfirma=0;
									$HCEEST = "";
									$query = "SELECT usures  from ".$empresa."_000020 ";
									$query .= " where Usucod = '".$key."' ";
									$query .= "   and Usucla = '".substr($firma,4)."' ";
									$query .= "   and Usuest = 'on' ";
									$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
									$num3 = mysql_num_rows($err3);
									if ($num3 > 0)
									{
										$row3 = mysql_fetch_array($err3);
										$HCEEST = $row3[0];
										$wswfirma=1;
									}
									else
										$wswfirma=3;
								}
								else
								{
									$wswfirma=2;
								}
								
								if($wswfirma == 1 or ($wswfirma == 2 and $WTIPO == 3))
								{
									$NOFILL="";
									for ($i=0;$i<$num;$i++)
									{
										if($DATA[$i][1] == "Numero" and strlen($registro[$i][0]) > 0 and strlen($registro[$i][0]) != substr_count($registro[$i][0]," ") and $registro[$i][0] != "" and $DATA[$i][10] != 0)
										{
											if($registro[$i][0] < $DATA[$i][9] or $registro[$i][0] > $DATA[$i][10])
											{
												$wswfirma=4;
												$NOFILL .= $DATA[$i][4]." VALOR NUMERICO FUERA DE RANGO ".$DATA[$i][9].":".$DATA[$i][10]." ";
											}
										}
										if($DATA[$i][1] == "Password" and ($DATA[$i][3] == "on" or strlen($registro[$i][0]) > 0))
										{
											$query="SELECT count(*) FROM usuarios WHERE codigo = '".$registro[$i][0]."'"; 
											$err1 = mysql_query($query,$conex);
											$row1 = mysql_fetch_array($err1);
											if($row1[0] == 0)
											{
												$wswfirma=4;
												$NOFILL .= " USUARIO NO EXISTE EN MATRIX ";
											}
										}
										if($DATA[$i][1] != "Titulo" and $DATA[$i][1] != "Subtitulo" and $DATA[$i][1] != "Busqueda" and $DATA[$i][1] != "Label" and $DATA[$i][1] != "Link" and ($DATA[$i][5] == "A"  or ($DATA[$i][5] != "A" and $DATA[$i][5] == $wsex)) and vedad($wedad,$DATA[$i][8],$Reta) == 1 and $DATA[$i][13] == 1)
										{
											// if($DATA[$i][3] == "on" and ((strlen($registro[$i][0]) <= 1 and $DATA[$i][1] == "Tabla") or (strlen($registro[$i][0]) == 0 and $DATA[$i][1] != "Seleccion") or ($DATA[$i][1] == "Seleccion" and $registro[$i][0] == "Seleccione") or ($DATA[$i][1] == "Seleccion" and $registro[$i][0] == "undefined") or ($DATA[$i][1] == "Seleccion" and $DATA[$i][6] == "M" and strlen($registro[$i][0]) <= 1) or ($DATA[$i][1] == "Booleano" and $registro[$i][0] == "UNCHECKED") or ($DATA[$i][1] == "Grid" and substr($registro[$i][0],0,1) == "0")))
											if($DATA[$i][3] == "on" and ((strlen($registro[$i][0]) <= 1 and $DATA[$i][1] == "Tabla") or (strlen(trim($registro[$i][0])) == 0 and $DATA[$i][1] != "Seleccion") or ($DATA[$i][1] == "Seleccion" and $registro[$i][0] == "Seleccione") or ($DATA[$i][1] == "Seleccion" and $registro[$i][0] == "undefined") or ($DATA[$i][1] == "Seleccion" and $DATA[$i][6] == "M" and strlen($registro[$i][0]) <= 1) or ($DATA[$i][1] == "Booleano" and $registro[$i][0] == "UNCHECKED") or ($DATA[$i][1] == "Grid" and substr($registro[$i][0],0,1) == "0")))
											{
												if($WTIPO != 3)
													$wswfirma=4;
												else
													$wswfirma=5;
												$NOFILL .= $DATA[$i][4]."-";
											}
										}
									}
									$NOFILL=substr($NOFILL,0,strlen($NOFILL)-1);
								}
								if($wswfirma == 1)
								{
									$SF=explode("-",$WSF);
									for ($i=0;$i<$num;$i++)
									{
										if($DATA[$i][1] == "Label" and $DATA[$i][11] == "SMART")
										{
											@eval($DATA[$i][7]);
											$err1 = mysql_query($query,$conex)or die("ERROR ".mysql_errno().":".mysql_error());
											$num1 = mysql_num_rows($err1); 
											if ($num1 > 0)
											{
												$row1 = mysql_fetch_array($err1);
												$registro[$i][0]=$row1[0];
											}
										}
									}
									// $query = "lock table ".$empresa."_".$wformulario." LOW_PRIORITY WRITE, ".$empresa."_000036 LOW_PRIORITY WRITE, ".$empresa."_000020 LOW_PRIORITY WRITE, ".$wdbmhos."_000048 LOW_PRIORITY WRITE, ".$wdbmhos."_000018 LOW_PRIORITY WRITE  ";
									// $err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE DATOS DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
									if ($wsinfirma == 1)
									{
										$fecha = $wfechareg;
										$hora = $whorareg;
									}
									else
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
									}
									
									/*
									* Incluimos las funciones necesarias para cargos automaticos
									* Equipo Iniciativa Cargos Automaticos
									* Sami Arevalo - Cristhian Barros - Manuel Garcia
									*/

									include_once("../../cca/procesos/cargos_automaticos_funciones.php");
									// Validamos si el formulario tiene configuracion de cargo automatico
									$tieneCCA = validarTieneCca($conex, $origen, $wformulario,"hce");
									$str_consecutivos_formulario = '';
									$str_consecutivos_formulario_todos = '';
									
									for ($i=0;$i<$num;$i++)
									{	
										if($DATA[$i][1] != "Titulo" and $DATA[$i][1] != "Subtitulo" and $DATA[$i][1] != "Busqueda"  and ($DATA[$i][5] == "A"  or ($DATA[$i][5] != "A" and $DATA[$i][5] == $wsex)) and vedad($wedad,$DATA[$i][8],$Reta) == 1 and $DATA[$i][13] == 1)
										{
											if(($DATA[$i][1] == "Label" and $DATA[$i][11] != "SMART") or $DATA[$i][1] == "Link" or ($DATA[$i][1] == "Seleccion" and ($registro[$i][0] == "Seleccione" or $registro[$i][0] == "undefined")) or ($DATA[$i][1] == "Booleano" and $registro[$i][0] == "UNCHECKED"))
												$registro[$i][0] = "";
											if($DATA[$i][1] == "Imagen")
											{
												$registro[$i][0] = $DATA[$i][2];
											}
											if($DATA[$i][1] == "Formula")
											{
												$DATA[$i][7]=strtoupper($DATA[$i][7]);
												$DATA[$i][7]=formula1($DATA[$i][7]);
												for ($w=0;$w<$num;$w++)
												{
													$DATA[$i][7]=str_replace($orden[$w][0],$orden[$w][1],$DATA[$i][7]);
												}
												$DATA[$i][7]=formula($DATA[$i][7]);
												@eval($DATA[$i][7]);
											}
											if($DATA[$i][1] == "Grid" and $DATA[$i][12] != "" and $DATA[$i][12] != " " and $DATA[$i][12] != ".")
											{
												@eval($DATA[$i][12].";");
												
											}
											if($DATA[$i][1] == "Tabla")
											{
												$permitirRepetidos = false;
												$permitiRepetirCodigoCampoTablaHCE = consultarAliasPorAplicacionHCE($conex,$origen,"permitiRepetirCodigoCampoTablaHCE");
												if($permitiRepetirCodigoCampoTablaHCE!="")
												{
													$permitenRepetidos = explode(",",$permitiRepetirCodigoCampoTablaHCE);
													
													$permitirRepetidos = in_array($wformulario."-".$DATA[$i][0],$permitenRepetidos);
												}
												
												if(!$permitirRepetidos)
												{
													$registro[$i][0] = Trepetidos($registro[$i][0]);
												}
											}
											if($wsinfirma == 0 or ($wsinfirma == 1 and !buscarID($SF,$i)))
											{
												if(strlen($registro[$i][0]) > 0)
												{
													$registro[$i][0] = urldecode($registro[$i][0]);
													$registro[$i][0] = str_replace("~","+",$registro[$i][0]);
													$registro[$i][0] = addslashes($registro[$i][0]);
													
													$query = "insert ".$empresa."_".$wformulario." (medico, fecha_data, hora_data, movpro, movcon, movhis, moving, movtip, movdat, movusu, Seguridad) values ('";
													$query .=  $empresa."','";
													$query .=  $fecha."','";
													$query .=  $hora."','"; 
													$query .=  $wformulario."',";
													$query .=  $DATA[$i][0].",'";
													$query .=  $whis."','";
													$query .=  $wing."','";
													$query .=  $DATA[$i][1]."','";
													if($DATA[$i][1] == "Imagen")
														$query .=  $Hgrafica."','";
													elseif($DATA[$i][1] == "Tabla")
															$query .=  utf8_decode($registro[$i][0])."','";
														else
															$query .=  utf8_decode($registro[$i][0])."','";
													$query .=  $key."',";
													$query .=  "'C-".$empresa."')";
													$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
													
													if($tieneCCA && ($DATA[$i][1] == 'Numero' || $DATA[$i][1] == 'Formula') && trim($registro[$i][0]) != '') {
														$str_consecutivos_formulario .= '|'.$DATA[$i][0];
													}
												}
												
												/*
													INICIATIVA CARGOS AUTOMATICOS - CIDENET SAS
													JUNIO 2021
													
													se crea un arreglo($str_consecutivos_formulario_todos) que contendrá todos los campos cargados en el formulario procesado,
													a diferencia del arreglo ($str_consecutivos_formulario), el cual contendra unicamente los campos que hayan sido diligenciados y no sean null o vacio
												*/
												if($tieneCCA && ($DATA[$i][1] == 'Numero' || $DATA[$i][1] == 'Formula')) {
													$str_consecutivos_formulario_todos .= '|'.$DATA[$i][0];
												}
												
											}
											else
											{
												if($DATA[$i][1] == "Imagen")
													$query =  " update ".$empresa."_".$wformulario." set movdat = '".$Hgrafica."' ";
												else
													$query =  " update ".$empresa."_".$wformulario." set movdat = '".utf8_decode($registro[$i][0])."' ";
												$query .=  "  where fecha_data='".$wfechareg."' and hora_data='".$whorareg."' and movpro='".$wformulario."' and movcon='".$DATA[$i][0]."' and movhis='".$whis."' and moving='".$wing."'";
												$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
											}
											$GOK=1;
											unset($wsa);
										}
									}
									
									// GRABACION EN FORMULARIO NRO 36 DE FORMULARIOS FIRMADOS
									$query = "select count(*) FROM ".$empresa."_000036 where Firpro='".$wformulario."' and fecha_data='".$fecha."' and hora_data='".$hora."' and Firhis='".$whis."' and Firing='".$wing."' and Firusu='".$key."' ";
									$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
									$row1 = mysql_fetch_array($err1);

									$HCEROL="";
									$meddoc = "";

									if ($row1[0] == 0)
									{
										$query = "SELECT Medtdo,Meddoc,Medreg,Medesp 
													FROM ".$wdbmhos."_000048 
												   WHERE Meduma = '".$key."' 
													 AND Medest='on'";
										$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$num2 = mysql_num_rows($err2);
										if($num2 > 0)
										{
											$row2 = mysql_fetch_array($err2);
											$HCEROL=$row2[3];
											$meddoc=$row2[1];
										}
										
										$HCECCO="";
										$query = "select ubisac from ".$wdbmhos."_000018 ";
										$query .= " where ubihis = '".$whis."' ";
										$query .= "   and ubiing = '".$wing."' ";
										$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$num2 = mysql_num_rows($err2);
										if($num2 > 0)
										{
											$row2 = mysql_fetch_array($err2);
											$HCECCO=$row2[0];
										}
											
										$query = "insert ".$empresa."_000036 (medico, fecha_data, hora_data, Firpro, Firhis, Firing, Firusu, Firfir, Firrol, Fircco, Seguridad) values ('";
										$query .=  $empresa."','";
										$query .=  $fecha."','";
										$query .=  $hora."','";
										$query .=  $wformulario."','";
										$query .=  $whis."','";
										$query .=  $wing."','";
										$query .=  $key."','";
										if($wswfirma == 1)
										{
											if($HCEEST == "on" or (isset($FED) and $FED == "CHECKED"))
												$query .= "no',";
											else
												$query .= "on',";
											Delete_Recovery($wformulario,$key,$whis,$wing);
										}
										else
											$query .= "off',";
										$query .=  "'".$HCEROL."','".$HCECCO."','C-".$empresa."')";
										$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO ARCHIVO 36 DE FORMULARIOS FIRMADOS : ".mysql_errno().":".mysql_error());

									}
									else
									{
										if($wswfirma == 1)
										{
											$query =  " update ".$empresa."_000036 set Firfir = 'on' ";
											$query .=  "  where fecha_data='".$fecha."' and hora_data='".$hora."' and Firpro='".$wformulario."' and Firhis='".$whis."' and Firing='".$wing."' and Firusu='".$key."' ";
											$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ARCHIVO 36 DE FORMULARIOS FIRMADOS : ".mysql_errno().":".mysql_error());
											Delete_Recovery($wformulario,$key,$whis,$wing);
										}
									}

									/*
										Si existe alguna configuracion de cargo automatico para el formulario, se envia la informacion
										al proceso de registro de cargos
									*/

									/* MODIFICADO 2022-01-13 */
									
									if ($tieneCCA) {
										$ch = curl_init();
										$data = array( 
												'consultaAjax'						=> '',
												'accion'							=> 'guardar_config_cargo_automatico_hce',
												'movusu'							=> $key,
												'whis' 								=> $whis,
												'wing' 								=> $wing,
												'wemp_pmla'							=> $origen,
												'wformulario'						=> $wformulario,
												'wespecialidad'						=> $HCEROL,
												'wmeddoc'							=> $meddoc,
												'str_consecutivos_formulario'		=> $str_consecutivos_formulario,
												'str_consecutivos_formulario_todos'	=> $str_consecutivos_formulario_todos
											);
										
										$options = array(
													CURLOPT_URL 			=> "localhost/matrix/cca/procesos/ajax_cargos_automaticos.php",
													CURLOPT_HEADER 			=> false,
													CURLOPT_POSTFIELDS 		=> $data,
													CURLOPT_CUSTOMREQUEST 	=> 'POST',
												);

										$opts = curl_setopt_array($ch, $options);
										$exec = curl_exec($ch);
										curl_close($ch);
									}

									if($wswfirma == 1)
									{
										$query = "SELECT usures  from ".$empresa."_000020 ";
										$query .= " where Usucod = '".$key."' ";
										$query .= "   and Usuest = 'on' ";
										$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$row2 = mysql_fetch_array($err2);
										
										$GOK=1;
										echo "<input type='HIDDEN' name= 'GOK' value='".$GOK."' id='GOK'>";
										$query = "insert ".$empresa."_".$wformulario." (medico, fecha_data, hora_data, movpro, movcon, movhis, moving, movtip, movdat, movusu, Seguridad) values ('";
										$query .=  $empresa."','";
										$query .=  $fecha."','";
										$query .=  $hora."','";
										$query .=  $wformulario."',";
										if($row2[0] == "on" or (isset($FED) and $FED == "CHECKED"))
											$query .=  "999,'";
										else
											$query .=  "1000,'";
										$query .=  $whis."','";
										$query .=  $wing."','";
										$query .=  "Firma','";
										$query .=  substr($firma,4)."','";
										$query .=  $key."',";
										$query .=  "'C-".$empresa."')";
										$err1 = mysql_query($query,$conex);
										if($err1 !=1 and $WTIPO != 4)
											die("ERROR GRABANDO DATOS DE HISTORIA CLINICA (FIRMA) : ".mysql_errno().":".mysql_error());
										$firma="";
										
										// ACTUALIZACION DEL USUARIO QUE FIRMO EL FORMULARIO A TODAS LAS VARIABLES QUE LO COMPONEN 2011-09-23
										$query =  " update ".$empresa."_".$wformulario." set movusu = '".$key."' ";
										$query .=  "  where fecha_data='".$fecha."' and hora_data='".$hora."' and movpro='".$wformulario."' and movhis='".$whis."' and moving='".$wing."' ";
										$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO USUARIO EN FORMULARIO ".$wformulario." : ".mysql_errno().":".mysql_error());
										
										
										$query =  " delete from ".$empresa."_000036 ";
										$query .=  "  where fecha_data='".$fecha."' and hora_data='".$hora."' and Firpro='".$wformulario."' and Firhis='".$whis."' and Firing='".$wing."' and Firfir='off' ";
										$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO ARCHIVO 36 DE FORMULARIOS FIRMADOS : ".mysql_errno().":".mysql_error());
									}
									else
									{
										$GOK=2;
										echo "<input type='HIDDEN' name= 'GOK' value='".$GOK."' id='GOK'>";
									}
									// $query = " UNLOCK TABLES";
									// $err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());	
									
									
									include_once("hce/AlertasHCE.php");
									alertasHCE($conex,$origen,$empresa,$wdbmhos,$wformulario,$whis,$wing,$fecha,$hora,$key);
									
									if($HCECCO!="")
									{
										$cco = $HCECCO;
									}
									else
									{
										$cco = consultarUbicacionPacienteHCE($conex,$wdbmhos,$whis,$wing);
									}
									
									// función definida en include/hce/funcionesHCE.php 
									actualizarDiagnosticosPaciente($conex,$origen,$empresa,$wdbmhos,$wformulario,$whis,$wing,$fecha,$hora,$key,$cco);
								}
								elseif($wswfirma == 2)
									{
										$wswfirma=6;
									}
							}
							
							if(!isset($wsa))
							{
								$wsinfirma=0;
								$wfechareg="";
								$whorareg="";
								$queryW = "SELECT Enctfo  from ".$empresa."_000001 ";
								$queryW .= " where Encpro = '".$wformulario."' ";
								$queryW .= "   and Encest = 'on' ";
								$err2 = mysql_query($queryW,$conex) or die(mysql_errno().":".mysql_error());
								$row2 = mysql_fetch_array($err2);
								$WTIPO=substr($row2[0],0,1);
								
								//QUERYS A LA BASE DE DATOS PARA DATOS HISTORICOS
								if($WTIPO == 1)
								{
									$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
									$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movcon IN (999,1000) ";
									$queryJ .= " order by id desc ";
									$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
									$num = mysql_num_rows($err);
									if($num == 0)
									{
										$queryJ  = "select Firing from ".$empresa."_000036 where Firpro = '".$wformulario."' and firhis = '".$whis."' group by 1  order by 1 desc ";
										$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
										$num = mysql_num_rows($err);
										if($num > 0)
										{
											$row = mysql_fetch_array($err);
											$winga=$row[0];
										}
										else
											$winga=(string)((integer)$wing - 1);
										$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
										$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$winga."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".movcon IN (999,1000) ";
										$queryJ .= " order by id desc ";
										$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
										$num = mysql_num_rows($err);
										if($num > 0)
										{
											$CRONOS=array();
											$row = mysql_fetch_array($err);
											$whorareg=$row[1];
											$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movusu from ".$empresa."_".$wformulario." ";
											$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$winga."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".fecha_data='".$row[0]."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".Hora_data='".$row[1]."' ";
											$queryJ .= " order by id  ";
											$err1 = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
											$num = mysql_num_rows($err1);
											for ($i=0;$i<$num;$i++)
											{
												$row1 = mysql_fetch_array($err1);
												$CRONOS[$row1[2]]=$row1[3];
											}
										}
									}
								}
								if($WTIPO == 4)
								{
									$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
									$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movcon IN (999,1000) ";
									$queryJ .= " order by id desc ";
									$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
									$num = mysql_num_rows($err);
									if($num > 0)
									{
										$CRONOS=array();
										$row = mysql_fetch_array($err);
										$whorareg=$row[1];
										$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movusu from ".$empresa."_".$wformulario." ";
										$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".fecha_data='".$row[0]."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".Hora_data='".$row[1]."' ";
										$queryJ .= " order by id  ";
										$err1 = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
										$num = mysql_num_rows($err1);
										for ($i=0;$i<$num;$i++)
										{
											$row1 = mysql_fetch_array($err1);
											$CRONOS[$row1[2]]=$row1[3];
										}
									}
								}
								
								if($WTIPO != 3)
								{
									$whceconsobl=0;
									$queryJ  = "select ".$empresa."_000002.detcon from ".$empresa."_000002 ";
									$queryJ .= " where ".$empresa."_000002.Detpro = '".$wformulario."' ";
									$queryJ .= "   and ".$empresa."_000002.Detest = 'on' ";
									$queryJ .= "   and ".$empresa."_000002.Detobl = 'on' ";
									$queryJ .= " order by 1 ";
									$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
									$num = mysql_num_rows($err);
									if($num > 0)
									{
										$row = mysql_fetch_array($err);
										$whceconsobl = $row[0];
									}
									$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,id from ".$empresa."_".$wformulario." ";
									$queryJ .= " where ".$empresa."_".$wformulario.".movpro = '".$wformulario."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movcon = 1000 ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
									if($WTIPO == 2)
										$queryJ .= "   and ".$empresa."_".$wformulario.".movusu='".$key."' ";
									$queryJ .= " UNION ALL ";
									$queryJ .= " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,id from ".$empresa."_".$wformulario." ";
									$queryJ .= " where ".$empresa."_".$wformulario.".movpro = '".$wformulario."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movcon = 999 ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
									if($WTIPO == 2)
										$queryJ .= "   and ".$empresa."_".$wformulario.".movusu='".$key."' ";
									if($whceconsobl != 0)
									{
										$queryJ .= " UNION ALL ";
										$queryJ .= " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,id from ".$empresa."_".$wformulario." ";
										$queryJ .= " where ".$empresa."_".$wformulario.".movpro = '".$wformulario."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".movcon = ".$whceconsobl;
										$queryJ .= "   and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
										if($WTIPO == 2)
											$queryJ .= "   and ".$empresa."_".$wformulario.".movusu='".$key."' ";
									}
									$queryJ .= " order by id desc ";
									$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
									$num = mysql_num_rows($err);
									if($num > 0)
									{
										$SF=array();
										$row = mysql_fetch_array($err);
										if($row[2] < 999 and $WTIPO != 1)
										{
											$wfechareg=$row[0];
											$whorareg=$row[1];
											$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
											$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".movusu='".$key."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".fecha_data='".$row[0]."' ";
											$queryJ .= "   and ".$empresa."_".$wformulario.".Hora_data='".$row[1]."' ";
											$queryJ .= " order by id  ";
											$err1 = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
											$num = mysql_num_rows($err1);
											$wsinfirma=1;
											$completo=0;
											for ($i=0;$i<$num;$i++)
											{
												$row1 = mysql_fetch_array($err1);
												$SF[$row1[2]][0]=1;
												$SF[$row1[2]][1]=$row1[3];
												if($row1[2] == 1000 or $row1[2] == 999)
													$firma=$row1[3];
											}
										}
										else
										{
											if($WTIPO == 1 or $WTIPO == 4)
											{
												$wfechareg=$row[0];
												$whorareg=$row[1];
												$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movusu from ".$empresa."_".$wformulario." ";
												$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
												$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
												$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
												$queryJ .= "   and ".$empresa."_".$wformulario.".fecha_data='".$row[0]."' ";
												$queryJ .= "   and ".$empresa."_".$wformulario.".Hora_data='".$row[1]."' ";
												$queryJ .= " order by id  ";
												$err1 = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
												$num = mysql_num_rows($err1);
												$wsinfirma=1;
												$completo=0;
												if($row[2] >= 999 and $WTIPO == 1)
													$completo=1;
												$wuserG="";
												for ($i=0;$i<$num;$i++)
												{
													$row1 = mysql_fetch_array($err1);
													$SF[$row1[2]][0]=1;
													$SF[$row1[2]][1]=$row1[3];
													$wuserG=$row1[4];
													if($row1[2] == 1000 or $row1[2] == 999)
														$firma=$row1[3];
												}
											}
										}
									}
								}
								else
								{
									$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".hora_data,max(".$empresa."_".$wformulario.".movcon) as a from ".$empresa."_".$wformulario." "; 
									$query .= " where ".$empresa."_".$wformulario.".movhis='".$whis."' ";
									$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
									$query .= "   and  ".$empresa."_".$wformulario.".movusu='".$key."' "; 
									$query .= " group by 1,2  ";
									$query .= " having a < 999 ";
									//$query .= " having a < 1000 ";
									$err = mysql_query($query,$conex);
									$num = mysql_num_rows($err);
									if ($num>0)
									{
										$mess .= "REGISTROS DE ESTE TIPO DE FORMULARIO SE ENCUENTRAN SIN FIRMA. RECUERDE REALIZAR ESTE PROCESO !!!! <br>";
										$mensajes="<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CC99FF LOOP=-1>REGISTROS DE ESTE TIPO DE FORMULARIO SE ENCUENTRAN SIN FIRMA. RECUERDE REALIZAR ESTE PROCESO !!!! <b>GRACIAS</b></MARQUEE></FONT>";
										echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CC99FF LOOP=-1>REGISTROS DE ESTE TIPO DE FORMULARIO SE ENCUENTRAN SIN FIRMA. RECUERDE REALIZAR ESTE PROCESO !!!! <b>GRACIAS</b></MARQUEE></FONT>";
									}
								}
							}

							//                 0        1       2       3      4        5       6      7       8       9       10      11     12       13     14      15      16       17      18      19     20       21      22      23      24      25      26      27      28      29      30      31      32      33      34      35     36       37      38      39      40      41      42      43      44      45      46     47      48       49      50      51
							$query = "SELECT Detpro, Detcon, Detorp, Dettip, Detdes, Detarc, Detcav, Detvde, Detnpa, Detvim, Detume, Detcol, Dethl7, Detjco, Detsiv, Detase, Detved, Detimp, Detimc, Detvco, Detvcr, Detobl, Detdep, Detcde, Deturl, Detfor, Detcco, Detcac, Detnse, Detfac, Enccol, Detcoa, Encdes, Detprs, Detalm, Detanm, Detlrb, Detdde, Encnco, Detcbu, Dettta, Detnbu, Detcua, Detccu, Detdpl, Detcro, Detvmi, Detvma, Dettii, Detcpp, Detcop, Detves  from ".$empresa."_000001,".$empresa."_000002 ";
							$query .= " where Encpro = '".$wformulario."' ";
							$query .= "   and Encpro = Detpro ";
							$query .= "   and Detest = 'on' ";
							$query .= " Order by Detorp ";
							$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$num = mysql_num_rows($err);
							if ($num>0)
							{
								if(!isset($registro))
									$registro=array();
								$orden=array();
								$items="";
								$pos=-1;
								if(!isset($wsa))
								{
									$WSF="";
									//                 0      1     
									$query = "SELECT Dprcmp,Dprval from ".$wdbmhos."_000137,".$wdbmhos."_000139 ";
									$query .= " where Protip = 'HCE' ";
									$query .= "   and Promed = '".$key."' ";
									$query .= "   and Proest = 'on' ";
									$query .= "   and Procod = Dprpro ";
									$query .= "   and Dprcod = '".$wformulario."' ";
									$query .= "   and Dprest = 'on' ";
									$query .= " Order by Dprcmp ";
									$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
									$num3 = mysql_num_rows($err3);
									if($num3 > 0)
									{
										$PROTOCOLOS=array();
										for ($i=0;$i<$num3;$i++)
										{
											$row3 = mysql_fetch_array($err3);
											$PROTOCOLOS[$row3[0]]=$row3[1];
										}
									}
									else
									{
										$query = "SELECT Dprcmp,Dprval from ".$wdbmhos."_000137,".$wdbmhos."_000139 ";
										$query .= " where Protip = 'HCE' ";
										$query .= "   and Promed = '*' ";
										$query .= "   and Proest = 'on' ";
										$query .= "   and Procod = Dprpro ";
										$query .= "   and Dprcod = '".$wformulario."' ";
										$query .= "   and Dprest = 'on' ";
										$query .= " Order by Dprcmp ";
										$err3 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$num3 = mysql_num_rows($err3);
										if($num3 > 0)
										{
											$PROTOCOLOS=array();
											for ($i=0;$i<$num3;$i++)
											{
												$row3 = mysql_fetch_array($err3);
												$PROTOCOLOS[$row3[0]]=$row3[1];
											}
										}
									}
								}
								$WSS="";
								$configuracionE="";
								for ($i=0;$i<$num;$i++)
								{
									$row = mysql_fetch_array($err);
									//          con      tip    des      nse      alm      anm
									validacion($row[1],$row[3],$row[4],$row[28],$row[34],$row[35],$configuracionE);
									$Span=$row[30];
									$orden[$i][0]=chr(32)."C".$row[1].chr(32);
									$orden[$i][1]=chr(32)."R".$i.chr(32);
									$wtitulo = $row[32];
									// if($wpostit != 99)
									if($wpostit != 99 && $wpostit!="")
									{
										$wptitulos = explode("|", $wtitulo);
										$wtitulo = $wptitulos[$wpostit - 1];
									}
									$width = $row[38];
									$pos=$pos + 1;
									if(!isset($wsa))
									{
										$wfechaT = date("Y-m-d");
										$whoraT = date("H:i:s");
										if($wsinfirma == 1)
										{
											if(isset($SF[$row[1]][0]))
											{
												switch($row[3])
												{
													case "Texto":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "W".$pos."-";
													break;
													case "Grid":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "J".$pos."-";
													break;
													case "Referencia":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "W".$pos."-";
													break;
													case "Tabla":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "M".$pos."-";
													break;
													case "Numero":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "T".$pos."-";
													break;
													case "Formula":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "T".$pos."-";
													break;
													case "Booleano":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "C".$pos."-";
													break;
													case "Memo":
														$WSF .= "W".$pos."-";
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "W".$pos."-";
													break;
													case "Password":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "T".$pos."-";
													break;
													case "Fecha":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "F".$pos."-";
													break;
													case "Hora":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															$WSF .= "H".$pos."-";
													break;
													case "Imagen":
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
														{
															$WSF .= "G".$pos."-";
															$Hgrafica=$SF[$row[1]][1];
														}
													break;
													case "Seleccion":
														if($row[40] == "M" or $row[40] == "M1")
															$WSF .= "X".$pos."-";
														else
														if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
															if($row[33] == "off")
																$WSF .= "S".$pos."-";
															else
																$WSF .= "R".$pos."-";
													break;
													default:
														$WSF .= "O".$pos."-";
												}
												$registro[$i][0]=$SF[$row[1]][1];
											}
											else
											{
												if(isset($PROTOCOLOS[$row[1]]))
												{
													$registro[$i][0]=$PROTOCOLOS[$row[1]];
													if($row[3] == "Imagen")
														$registro[$i][6]=$PROTOCOLOS[$row[1]];
												}
												else
													$registro[$i][0]=$row[7];
											}
										}
										else
										{
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												if($row[3] != "Booleano" or ($row[3] == "Booleano" and $row[7] == "CHECKED"))
													$registro[$i][0]=$row[7];
												elseif($row[3] == "Booleano")
														unset($registro[$i][0]);
												if($row[3] == "Fecha")
												{
													if($row[28] == 2)
													{
														$WSF .= "F".$pos."-";
														$registro[$i][0]=date("Y-m-d");
													}
													else
														$registro[$i][0]="";
												}
												if($row[3] == "Hora")
													$registro[$i][0]=date("H:i:s");
												if($row[3] == "Imagen")
													$Hgrafica="";
											}
											if(isset($PROTOCOLOS[$row[1]]))
											{
												$registro[$i][0]=$PROTOCOLOS[$row[1]];
												if($row[3] == "Imagen")
													$registro[$i][6]=$PROTOCOLOS[$row[1]];
											}
											if(($row[45] == "on" and $wing > "1" and $WTIPO == 1 and isset($CRONOS[$row[1]])) or ($WTIPO == 4 and isset($CRONOS[$row[1]])))
											{
												$registro[$i][0]=$CRONOS[$row[1]];
											}
										}
									}
									else
									{
										if($row[3] == "Booleano" and isset($registro[$i][0]) and $registro[$i][0] != "CHECKED")
											unset($registro[$i][0]);
									}

									switch($row[3])
									{
										case "Texto":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "W".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Grid":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "J".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Referencia":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "W".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Tabla":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "M".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Numero":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "T".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Formula":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "T".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Booleano":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "C".$pos."-";
												$WSS .= $row[28]."-";
												if(isset($registro[$i][0]) and $registro[$i][0] != "CHECKED")
													unset($registro[$i][0]);
											}
										break;
										case "Memo":
											$WSF .= "W".$pos."-";
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "W".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Password":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "T".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Fecha":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "F".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Hora":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "H".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Imagen":
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												$items .= "G".$pos."-";
												$WSS .= $row[28]."-";
											}
										break;
										case "Seleccion":
											if($row[40] == "M" or $row[40] == "M1")
											{
												$items .= "X".$pos."-";
												$WSS .= $row[28]."-";
											}
											else
											if(($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex)) and vedad($wedad,$row[16],$Reta) == 1 and vespecial($row[51]) == 1)
											{
												if($row[33] == "off")
												{
													$items .= "S".$pos."-";
													$WSS .= $row[28]."-";
												}
												else
												{
													$items .= "R".$pos."-";
													$WSS .= $row[28]."-";
												}
											}
										break;
									}
									for ($j=1;$j<=52;$j++)
									{
										if($j == 8 and isset($PROTOCOLOS[$row[1]]))
											$registro[$i][$j]=$PROTOCOLOS[$row[1]];
										else
											$registro[$i][$j]=$row[$j-1];
										if($j == 32 and $registro[$i][32] < 1)
											$registro[$i][32] = 1;
										if($j == 37 and $registro[$i][37] < 1)
											$registro[$i][37] = 1;
									}
								}
								//ACCESO AL ARCHIVO DE RECOVERY


								if(isset($wrecovery) and $wrecovery == 2 and !isset($wsa))
								{
									$datafile = "/var/www/matrix/hce/Recovery/".$wformulario.$key.$whis.$wing.".txt";
									$gestor = @fopen("/var/www/matrix/hce/Recovery/".$wformulario.$key.$whis.$wing.".txt", "r");
									if ($gestor) 
									{
										while (!feof($gestor)) 
										{
											$buffer = fgets($gestor);
											if(strlen($buffer) > 0)
											{
												$wbuff=1;
												if(is_numeric (substr($buffer,0,strpos($buffer,"-"))) and strpos($buffer,"-") !== false)
												{
													$wbuff=0;
													$posrec = (integer)substr($buffer,0,strpos($buffer,"-"));
													$buffer = substr($buffer,strpos($buffer,"-")+1);
												}
												if($registro[$posrec][4] == "Imagen")
												{
													$Hgrafica = $buffer;
												}
												else
												{
													if($registro[$posrec][4] == "Fecha" and $registro[$posrec][29] == "2")
														$registro[$posrec][0] = date("Y-m-d");
													elseif($registro[$posrec][4] == "Hora" and $registro[$posrec][29] == "2")
														$registro[$posrec][0] = date("H:i:s");
													elseif($wbuff == 0)
														{
															if($registro[$posrec][4] == "Memo")
															{
																$buffer = stripslashes($buffer);
																$buffpos = strrpos($buffer,"^");
																while (substr($buffer,$buffpos,1) == "^" and $buffpos+1 == strlen($buffer)-1 )
																{
																	$buffer = substr($buffer,0,$buffpos);
																	$buffpos = strrpos($buffer,"^");
																}
																$registro[$posrec][0] = str_replace(chr(94),chr(10),$buffer);
															}
															else
																if($registro[$posrec][4] == "Grid")
																	$registro[$posrec][0] = utf8_decode($buffer);
																else	
																	$registro[$posrec][0] = $buffer;
														}
													else
														{
															if($registro[$posrec][4] == "Memo")
															{
																$buffer = stripslashes($buffer);
																$buffpos = strrpos($buffer,"^");	
																while (substr($buffer,$buffpos,1) == "^" and $buffpos+1 == strlen($buffer)-1 )
																{
																	$buffer = substr($buffer,0,$buffpos);
																	$buffpos = strrpos($buffer,"^");
																}
																$registro[$posrec][0] .= str_replace(chr(94),chr(10),$buffer);
															}
															else
																if($registro[$posrec][4] == "Grid")
																	$registro[$posrec][0] .= utf8_decode($buffer);
																else	
																	$registro[$posrec][0] .= $buffer;
														}
												}
												//echo $posrec." : ".$registro[$posrec][4]." : ".$registro[$posrec][0]."<br>";
											}
										}
										$RECOVERY=1;
										fclose($gestor);
										@chmod($datafile, 0777);
									}
								}


								if(isset($wsa))
								{
									$datafile="/var/www/matrix/hce/Recovery/".$wformulario.$key.$whis.$wing.".txt";
									$file = fopen($datafile,"w+");
									for ($i=0;$i<$num;$i++)
									{
										if($registro[$i][4] == "Imagen" and isset($Hgrafica))
										{
											$Hgrafica = str_replace(chr(10),"",$Hgrafica);
											$Hgrafica = str_replace(chr(13),"",$Hgrafica);
											$gestor=$i."-".$Hgrafica.chr(13).chr(10);
											fwrite($file,$gestor);
										}
										elseif(isset($registro[$i][0]) and strlen($registro[$i][0]) > 0)
											{
												$registro[$i][0]=str_replace("~","+",$registro[$i][0]);
												if($registro[$i][4] == "Memo")
												{
													//$registro[$i][0]=str_replace(chr(13),"",$registro[$i][0]);
													$registro[$i][0]=str_replace(chr(10),chr(94),$registro[$i][0]);
													$gestor=$i."-".$registro[$i][0].chr(10);
												}
												else
													$gestor=$i."-".$registro[$i][0].chr(13).chr(10);
												fwrite($file,$gestor);
											}
									}
									fclose($file);	
									@chmod($datafile, 0777);
								}
							
								if(!isset($Hgrafica))
									$Hgrafica="";
								if(!isset($wsa))
								{
									$wsagrig = 1;
									$WSF .= "FIN";
								}
								$items .= "FIN";
								$WSS .= "FIN";
							}
							//echo $items."<br>";
							if($num > 0)
							{
								if(isset($ok))
								{
									//echo "Valor de ok : ".$ok."<br>";
									//echo "Valor de Fecha : ".$wfechaT."<br>";
									//echo "Valor de Hora : ".$whoraT."<br>";
									unset($ok);
								}
								if(isset($position))
									$position=substr($items,0,2);
								$wsa=1;
								echo "<input type='HIDDEN' name= 'wsa' value='".$wsa."'>";
								echo "<input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
								echo "<input type='HIDDEN' name= 'wtipodoc' value='".$wtipodoc."'>";
								echo "<input type='HIDDEN' name= 'wfechaT' value='".$wfechaT."'>";
								echo "<input type='HIDDEN' name= 'whoraT' value='".$whoraT."'>";
								echo "<input type='HIDDEN' name= 'width' value='".$width."'>";
								echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
								echo "<input type='HIDDEN' name= 'WSF' id='WSF' value='".$WSF."'>";
								echo "<input type='HIDDEN' name= 'WSS' id='WSS' value='".$WSS."'>";
								echo "<input type='HIDDEN' name= 'WSS' id='items' value='".$items."'>";
								echo "<input type='HIDDEN' name= 'WTIPO' id='WTIPO' value='".$WTIPO."'>";
								echo "<input type='HIDDEN' name= 'Hgrafica' value='".$Hgrafica."' id='Hgrafica'>";
									
								
								$n=$registro[0][31];
								
								// INICIALIZACION DEL TITULO DEL FORMULARIO
								if(!isset($wtitframe))
								{
									echo '<script language="Javascript">';
									//echo "     debugger;";
									echo '		var obj = parent.parent.demograficos.document.getElementById("txttitulo");';
									echo '		if(obj)';
									echo '		{';
									echo "			var divAux = document.createElement( 'div');";
									echo "			divAux.innerHTML = '".htmlentities($wtitulo)."';";
									echo "			obj.value = divAux.innerHTML.toUpperCase();";
									echo '		} ';
									echo '		var obj1 = parent.parent.demograficos.document.getElementById("txtformulario");';
									echo '		if(obj1)';
									echo '		{';
									echo "			var divAux = document.createElement( 'div');";
									echo "			divAux.innerHTML = '".htmlentities($wformulario)."';";
									echo "			obj1.value = divAux.innerHTML.toUpperCase();";
									echo '		} ';
									echo '</script>';
								}
								else
									echo "<input type='HIDDEN' name= 'wtitframe' value='".$wtitframe."'>";
								//if(isset($wswfirma) and isset($ok))
								//if(isset($wswfirma))
									//echo "valor de wswfirma :  ".$wswfirma."<br>";
								if(strlen($configuracionE) > 0)
								{
									echo "<table border=0 cellspacing=0>";
									echo "<tr><td class=tipoMARQUEE>ATENCION ERROR DE CONFIGURACION!!! DESCRIPCION :<br> ".$configuracionE." <b>POR FAVOR LLAME INMEDIATAMENTE A SISTEMAS O A LAS ENFERMERAS DE HISTORIA CLINICA. GRACIAS</b></td></tr>";
									echo "</table>";
								}

								$mensajes="";
								if(isset($wswfirma ) and $wswfirma == 3)
								{
									$completo = 0;
									$mess .= "SE INTENTO GRABAR UN FORMULARIO CON FIRMA ERRONEA INTENTELO NUEVAMENTE!!!! <br> ";
									$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FEAAA4 LOOP=-1>SE INTENTO GRABAR UN FORMULARIO CON FIRMA ERRONEA INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
									echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FEAAA4 LOOP=-1>SE INTENTO GRABAR UN FORMULARIO CON FIRMA ERRONEA INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT>";
								}
								if(isset($wswfirma ) and $wswfirma == 4)
								{
									$completo = 0;
									$mess .= "SE INTENTO GRABAR UN FORMULARIO CON FIRMA CORRECTA Y CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <br> ";
									$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFFF66 LOOP=-1 scrolldelay=1>SE INTENTO GRABAR UN FORMULARIO CON FIRMA CORRECTA Y CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
									echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFFF66 LOOP=-1 scrolldelay=1>SE INTENTO GRABAR UN FORMULARIO CON FIRMA CORRECTA Y CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT>";
								}
								if(isset($wswfirma ) and $wswfirma == 5)
								{
									$completo = 0;
									$mess .= "SE INTENTO GRABAR UN FORMULARIO TIPO 3, CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!!  <br> ";
									$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99FFFF LOOP=-1>SE INTENTO GRABAR UN FORMULARIO TIPO 3, CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
									echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99FFFF LOOP=-1>SE INTENTO GRABAR UN FORMULARIO TIPO 3, CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT>";
								}
								if(isset($wswfirma ) and $wswfirma == 6)
								{
									$completo = 0;
									$mess .= "SE INTENTO GRABAR UN FORMULARIO SIN FIRMA. LA FIRMA ES OBLIGATORIA. INTENTELO NUEVAMENTE!!!!  <b>GRACIAS</b><br> ";
									$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99FFFF LOOP=-1>SE INTENTO GRABAR UN FORMULARIO SIN FIRMA. LA FIRMA ES OBLIGATORIA. INTENTELO NUEVAMENTE!!!!  <b>GRACIAS</b></MARQUEE></FONT><br>";
									echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99FFFF LOOP=-1>SE INTENTO GRABAR UN FORMULARIO SIN FIRMA. LA FIRMA ES OBLIGATORIA. INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT>";
								}
								if(isset($wswfirma ))
									echo "<input type='HIDDEN' id='wswfirma' name='wswfirma' value='".$wswfirma."'>";
								else
								{
									$wswfirma=-1;
									echo "<input type='HIDDEN' id='wswfirma' name='wswfirma' value='".$wswfirma."'>";
								}
								if($wsinfirma == 1)
								{
									if(isset($completo) and $completo == 1 and $WTIPO != 4)
									{
										$mess .= "EL FORMULARIO SE HA GRABAD0 CON FIRMA DIGITAL - ESTE REGISTRO SE DILIGENCIA SOLAMENTE UNA VEZ POR HISTORIA - INGRESO !!!! <br> ";
										$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00FF66 LOOP=-1>EL FORMULARIO SE HA GRABAD0 CON FIRMA DIGITAL - ESTE REGISTRO SE DILIGENCIA SOLAMENTE UNA VEZ POR HISTORIA - INGRESO !!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
										echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00FF66 LOOP=-1>EL FORMULARIO SE HA GRABAD0 CON FIRMA DIGITAL - ESTE REGISTRO SE DILIGENCIA SOLAMENTE UNA VEZ POR HISTORIA - INGRESO !!!! <b>GRACIAS</b></MARQUEE></FONT>";
									}
									else
									{
										if(!isset($wuserG) and $WTIPO != 4)
										{
											$mess .= "EL FORMULARIO SE GRABO SIN FIRMA DIGITAL - DILIGENCIELO Y FIRMELO ANTES DE GRABAR OTRO REGISTRO !!!! <br> ";
											$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFCC66 LOOP=-1>EL FORMULARIO SE GRABO SIN FIRMA DIGITAL - DILIGENCIELO Y FIRMELO ANTES DE GRABAR OTRO REGISTRO !!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
											echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFCC66 LOOP=-1>EL FORMULARIO SE GRABO O SE HABIA GRABADO SIN FIRMA DIGITAL - DILIGENCIELO Y FIRMELO ANTES DE GRABAR OTRO REGISTRO !!!! <b>GRACIAS</b></MARQUEE></FONT>";
										}
										elseif(isset($wuserG) and $wuserG != $key)
										{
											$query = "SELECT Descripcion from usuarios ";
											$query .= " where codigo = '".$wuserG."' ";
											$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
											$num2 = mysql_num_rows($err2);
											if ($num2>0)
											{
												$row2 = mysql_fetch_array($err2);
												$wmedico = $row2[0];
											}
											else
												$wmedico = "NO ESPECIFICO";
											$mess .= "EL FORMULARIO SE GRABO SIN FIRMA DIGITAL POR ".$wmedico." - CONTACTELO PARA TERMINAR EL REGISTRO !!!! <br> ";
											$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FEAAA4 LOOP=-1>EL FORMULARIO SE GRABO SIN FIRMA DIGITAL POR ".$wmedico." - CONTACTELO PARA TERMINAR EL REGISTRO !!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
											echo $mensajes;
										}
									}
									//echo $WSF."<br>";
									$SF=explode("-",$WSF);
									$SF1=array();
									for ($h=0;$h<count($SF);$h++)
										$SF1[$SF[$h]]=1;
								}
								
				
								echo "<table border=0 cellspacing=0>";
								echo "<tr>";
								$rwidth=(integer)($width/$Span);
								for ($i=0;$i<$Span;$i++)
								{
									echo "<td id=tiporegla width=".$rwidth." colspan=1>&nbsp;</td>";
								}
								echo "</tr>";
								//$index=1000;
								//$ancho=1200;
								//$alto=250;
								//echo "<tr><td id=tipoL02V colspan=".$Span." align=center><A HREF='#' id='btnModal".$index."' name='btnModal".$index."' class=tipo3V onClick='javascript:mostrarFlotante(\"\",\"nombreIframe\",\"/matrix/HCE/Procesos/HCE.php?accion=W2&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$row[0]."&whis=".$whis."&wing=".$wing."&wtitframe=no\",\"".$alto."\",\"".$ancho."\");'>Vistas Asociadas</A></td></tr>";
								$campos=0;
								$totdiv=0;
								$pasdiv=0;
								if($registro[0][4] == "Titulo" and $registro[0][32] == $n and vespecial($registro[$i][52]) == 1 and vedad($wedad,$registro[$i][17],$Reta) == 1 and ($registro[$i][16] == "A" or $registro[$i][16] == $wsex))
								{
									$totdiv++;
									echo "<tr OnClick='toggleDisplay(div".$totdiv.")'>";
								}
								else
									echo "<tr>";
							}
							for ($i=0;$i<$num;$i++)
							{
								//$querytime_before = array_sum(explode(' ', microtime()));
								if($registro[$i][19] === "on")
									$salto=" ";
								else
									$salto="<br>";
								if($campos >= $n)
								{
									echo "</tr>";
									if($totdiv > 0 and $registro[$i][4] == "Titulo")
										echo "</table></td></tr>";
									if($i > 0 and $registro[$i-1][4] == "Titulo" and $registro[$i-1][32] >= $n)
									{
										$pasdiv++;
										//echo "<TBODY id=div".$totdiv."><tr>";
										if($registro[$i-1][51] == "on")
											$colapsar="none";
										else 
											$colapsar="";
										echo "<tr id=div".$totdiv." style='display: ".$colapsar."'><td colspan=".$Span."><table width='100%' border=0 cellspacing=1 cellpadding=0>";
									}
									//else
										//echo "<tr>";
									if($registro[$i][4] == "Titulo" and $registro[$i][32] >= $n and vespecial($registro[$i][52]) == 1 and vedad($wedad,$registro[$i][17],$Reta) == 1 and ($registro[$i][16] == "A" or $registro[$i][16] == $wsex))
									{
										$totdiv++;
										echo "<tr OnClick='toggleDisplay(div".$totdiv.")'>";
									}
									else
										echo "<tr>";
									$campos=0;
								}
								if($registro[$i][22] == "on")
								{
									$OBL="O";
									$CHECK="check";
									$OBLS="1";
								}
								else
								{
									$OBL="";
									$CHECK="";
									$OBLS="0";
								}
								if($registro[$i][11] == "" or $registro[$i][11] == "NO APLICA")
									$UDM="";
								else
									$UDM=" ".htmlentities($registro[$i][11]); 
									
									
								if(($registro[$i][16] != "A" and $registro[$i][16] != $wsex) or vedad($wedad,$registro[$i][17],$Reta) == 0  or vespecial($registro[$i][52]) == 0)
									$registro[$i][4]="Noaplica";
									
								$estado="";
								//echo $campos." ".$faltan." ".$n." ".$registro[$i][4]." ".$registro[$i][9]."<br>";
								switch($registro[$i][4])
								{
									case "Noaplica":
									
									break; 
									case "Texto":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$alto=$registro[$i][35];
										$ancho=$registro[$i][36];
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										$col=($span * 80) / ($n * 2);
										if(isset($RECOVERY))
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='W".$i."' value='".$registro[$i][0]."' class=tipo3".$OBL.">".$UDM."</td>";
										else
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='W".$i."' value='".htmlentities($registro[$i][0])."' class=tipo3".$OBL.">".$UDM."</td>";
										$campos += $registro[$i][32];	
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."W".$i."'))</script>";	
									break;
									case "Grid":
										if(isset($HCERES))
											unset($HCERES);
										if($registro[$i][32] != $n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										$Gridseg=explode("*",$registro[$i][26]);
										$Gridtit=explode("|",$Gridseg[0]);
										$Gridtip=explode("|",$Gridseg[1]);
										$Gridobl=explode("|",$Gridseg[2]);
										if(count($Gridseg) == 4)
										{
											$okGrid = "";
											if($registro[$i][49] == "Q" or $registro[$i][49] == "Q1")
											{
												$Gridseg[3]=str_replace("HIS",chr(39).$whis.chr(39),$Gridseg[3]);
												$Gridseg[3]=str_replace("ING",chr(39).$wing.chr(39),$Gridseg[3]);
												
												if($registro[$i][49] == "Q")
												{
													$query = $Gridseg[3];
												}
												else
												{
													// @eval($Gridseg[3]);
													
													try {
														$result = eval($Gridseg[3]);
													} catch (ParseError $e) {
														//Error
														// echo "error: ".$e;
													}
												}
													
												$err1 = mysql_query($query,$conex);
												$num1 = mysql_num_rows($err1);
												if ($num1 > 0)
												{
													$row1 = mysql_fetch_array($err1);
													if($registro[$i][0] == $registro[$i][8])
													{
														$registro[$i][0]=$row1[0];
													}
												}
											}
											else
												$okGrid = "5";
										}
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02".$CHECK."></td>";
											echo "</tr><tr>";
											$campos = 0;
												
										}
										$span=$registro[$i][32];
										$alto=1;
										$ancho=20;
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<td id=tipoL02".$CHECK." colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
										echo "<textarea name='GRID[".$i."]' cols=".$ancho." rows=".$alto." id='GRID".$i."' class=tipo3GRID>".$registro[$i][26]."</textarea>";
										if(isset($RECOVERY))
											echo "<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='J".$i."' class=tipo3GRID>".htmlentities($registro[$i][0])."</textarea><br>";
										else
											echo "<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='J".$i."' class=tipo3GRID>".$registro[$i][0]."</textarea><br>";
										echo "<table align=center border=1 class=tipoTABLEGRID width=80%>";
										echo "<tr>";
										echo "<td id=tipoL06GRID>ITEM</td>";
										for ($g=0;$g<count($Gridtit);$g++)
										{
											echo "<td id=tipoL06GRID>".htmlentities($Gridtit[$g])."</td>";
										}
										echo "<td id=tipoL06GRID colspan=5>OPERACION</td></tr>";
										echo "<tr>";
										echo "<td class=tipoL02GRID2><input type='TEXT' name='WGRIDITEM' size=3 maxlength=6 id='WGRIDITEM' readonly=readonly value='0' class=tipo3".$OBL."></td>";
										$GRID=array();
										for ($g=0;$g<count($Gridtip);$g++)
										{
											if($Gridobl[$g] == "R")
											{
												$OBLGRID = "O";
												$OBLSGRID = "1";
											}
											else
											{
												$OBLGRID = "";
												$OBLSGRID = "0";
											}
											if(!isset($HCERES))
												$HCERES = -1;
											if(substr($Gridtip[$g],0,1) == "^")
											{
												$Gridtip[$g] = substr($Gridtip[$g],1);
												$HCERES = $g;
											}
											switch(substr($Gridtip[$g],0,1))
											{
												case "F":
													if(!isset($GRID[$g]))
														$GRID[$g]="";
													echo "<td class=tipoL02GRID2><input type='text' id='FGRID".$i.$g."'  size='10' maxlength='10' NAME='GRID[".$g."]' class=tipo3F".$OBLGRID." value='".$GRID[$g]."'></td>";
												break;
												case "H":
													if(!isset($GRID[$g]))
														$GRID[$g]="";
													echo "<td class=tipoL02GRID2><input type='TEXT' name='GRID[".$g."]' size=5 maxlength=5 id='HGRID".$i.$g."' value='".$GRID[$g]."'  class=tipo3H".$OBLGRID.">&nbsp;";
													echo "<select name='Horas[".$g."]' id=HOG".$i.$g." class=tipoHora onChange='limpiarHoraG(".$g.",".$i.")'>";
													for ($j=0;$j<24;$j++)
													{
														if($j < 10)
															$jh = "0".$j;
														else
															$jh = $j;
														echo "<option value='".$jh."'>".$jh."</option>";
													}
													echo "</select>&nbsp;";


													echo "<select name='Minutos[".$g."]' id=MIG".$i.$g." class=tipoHora onChange='mostrarHoraG(".$g.",".$i.")'>";
													echo "<option value=''></option>";
													for ($j=0;$j<4;$j++)
													{
														$jh=(15 * $j);
														if($jh < 15)
															$jh = "0".$jh;
														echo "<option value='".$jh."'>".$jh."</option>";
													}
													echo "</select>";

													echo "</td>";
												break;
												case "S":
													$TGRID=substr($Gridtip[$g],1,6);
													$CGRID=substr($Gridtip[$g],7);
													echo "<td class=tipoL02GRID2>";
													$query = "SELECT Selcda, Selnda from ".$empresa."_".$TGRID." where Seltab='".$CGRID."' and Selest='on' order by Selnda";
													$err1 = mysql_query($query,$conex);
													$num1 = mysql_num_rows($err1);
													echo "<select name='GRID[".$g."]' id='SGRID".$i.$g."' class=tipo3".$OBLGRID.">";
													echo "<option value='Seleccione'>Seleccione</option>";
													if ($num1>0)
													{
														for ($j=0;$j<$num1;$j++)
														{
															$row1 = mysql_fetch_array($err1);
															echo "<option value='".$row1[0]."-".htmlentities($row1[1])."'>".htmlentities($row1[1])."</option>";
														}
													}
													echo "</select>";
													echo "</td>";
												break;
												case "T":
													if(!isset($GRID[$g]))
														$GRID[$g]="";
													$ancho=substr($Gridtip[$g],1);
													$idgrid="WGRID".$i.$g;
													echo "<td class=tipoL02GRID2><input type='TEXT' name='GRID[".$g."]' size=".$ancho." maxlength=255 id='WGRID".$i.$g."' value='".$GRID[$g]."' class=tipo3".$OBLGRID."  ondblclick='vertextogrid(".chr(34).$idgrid.chr(34).")' ></td>";
												break;
												case "M":
													if(!isset($GRID[$g]))
														$GRID[$g]="";
													$ancho=substr($Gridtip[$g],1);
													$idgrid="MGRID".$i.$g;
													echo "<td class=tipoL02GRID2><textarea name='GRID[".$g."]' cols=".$ancho." rows=2 id='MGRID".$i.$g."' class=tipo3".$OBLGRID."  ondblclick='vertextogrid(".chr(34).$idgrid.chr(34).")'>".$GRID[$g]."</textarea></td>";
												break;
												case "N":
													if(!isset($GRID[$g]))
														$GRID[$g]="";
													$Gridnum=explode("(",$Gridtip[$g]);
													if(substr($Gridtip[$g],1,1) == "S")
														$ancho=substr($Gridnum[0],2);
													else
														$ancho=substr($Gridnum[0],1);
													$unimed=substr($Gridnum[1],0,strlen($Gridnum[1])-1);
													$minmax=substr($Gridnum[2],0,strlen($Gridnum[2])-1);
													$Gridminmax=explode(",",$minmax);
													echo "<td class=tipoL02GRID2><input type='TEXT' name='GRID[".$g."]' size=".$ancho." maxlength=30 id='TGRID".$i.$g."' value='".$GRID[$g]."' onkeypress='return teclado(event)' onBlur='minmax(this,".$Gridminmax[0].",".$Gridminmax[1].",".$OBLSGRID.")' class=tipo3".$OBLGRID.">".$unimed."</td>";
												break;
											}
										}
										//echo $i." ".$items."<br>";
										$id1="if(grabagrid(".$i.")){ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','".$okGrid."');}";
										$id2="if(modificagrid(".$i.")){ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','".$okGrid."');}";
										$id3="borragrid(".$i.");ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','".$okGrid."');";
										$path="/matrix/hce/reportes/HCE_Grid_History.php?empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&whis=".$whis."&wing=".$wing."&wformulario=".$wformulario."&wcons=".$registro[$i][2];
										//echo "<td id=tipoL02GRID2><IMG SRC='/matrix/images/medical/HCE/gra.png' onClick=".$id1." /></td><td id=tipoL02GRID2><IMG SRC='/matrix/images/medical/HCE/mod.png' onClick=".$id2." /></td><td id=tipoL02GRID2><IMG SRC='/matrix/images/medical/HCE/del.png' onClick=".$id3." /></td><td id=tipoL02GRID2><IMG SRC='/matrix/images/medical/HCE/lim.png' onClick='limpiagrid(".$i.")' /></td></tr>";
										echo "<td id='GRIDTT[1]' title='GRABAR' class=tipoL02GRID2 onMouseMove='tooltipGrid(1)' onClick=".$id1."><IMG SRC='/matrix/images/medical/HCE/gra.png'></td><td id='GRIDTT[2]' title='MODIFICAR' class=tipoL02GRID2 onMouseMove='tooltipGrid(2)' onClick=".$id2."><IMG SRC='/matrix/images/medical/HCE/mod.png'></td><td id='GRIDTT[3]' title='BORRAR' class=tipoL02GRID2 onMouseMove='tooltipGrid(3)' onClick=".$id3."><IMG SRC='/matrix/images/medical/HCE/del.png'></td><td id='GRIDTT[4]' title='LIMPIAR' class=tipoL02GRID2 onMouseMove='tooltipGrid(4)' onClick='limpiagrid(".$i.")'><IMG SRC='/matrix/images/medical/HCE/lim.png'><td id='GRIDTT[5]' title='HISTORICO' class=tipoL02GRID2 onMouseMove='tooltipGrid(5)' onclick='javascript:activarModalIframe(\"HISTORICO\",\"nombreIframe\",\"".$path."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/History.png'></td></tr>";
										$Gdataseg=explode("*",$registro[$i][0]);
										$Gdatasuma=array();
										for ($g=0;$g<count($Gridtip);$g++)
												$Gdatasuma[$g] = 0;
										for ($g=1;$g<=$Gdataseg[0];$g++)
										{
											if($g % 2 == 0)
											{
												$gridcolor="tipoL02GRID2";
												$gridcolorj="tipoL02GRID2W";
												$GRIDICO="mas+.png";
											}
											else
											{
												$gridcolor="tipoL02GRID1";
												$gridcolorj="tipoL02GRID1W";
												$GRIDICO="mas.png";
											}
											$Gdatadata=explode("|",$Gdataseg[$g]);
											echo "<tr>";
											echo "<td class=".$gridcolor.">".$g."</td>";
											for ($g1=0;$g1<count($Gdatadata);$g1++)
											{
												if($HCERES == $g1)
												{
													$gridcolor="tipoL02GRIDS";
													$gridcolorj="tipoL02GRIDS";
												}
												else
												{
													if($g % 2 == 0)
													{
														$gridcolor="tipoL02GRID2";
														$gridcolorj="tipoL02GRID2W";
													}
													else
													{
														$gridcolor="tipoL02GRID1";
														$gridcolorj="tipoL02GRID1W";
													}
												}
												if(substr($Gridtip[$g1],0,2) == "NS")
													$Gdatasuma[$g1] += (float)$Gdatadata[$g1];
												if(substr($Gridtip[$g1],0,1) == "M" or substr($Gridtip[$g1],0,1) == "T")
													if(isset($wsagrig))
														echo "<td class=".$gridcolorj.">".htmlentities($Gdatadata[$g1])."</td>";
													else
														echo "<td class=".$gridcolorj.">".$Gdatadata[$g1]."</td>";
												else
													echo "<td class=".$gridcolor.">".$Gdatadata[$g1]."</td>";
											}
											if($registro[$i][41] == "M")
											{
												@eval($registro[$i][25]);
												echo "<td class=".$gridcolor."><A HREF='#' id='".$TITGRID."'  onclick='javascript:activarModalIframe(\"".$TITGRID."\",\"nombreIframe\",\"".$DATAGRID."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/".$GRIDICO."'></A><td class=".$gridcolor." colspan=4><input type='RADIO' name='RGRID".$g."' id='WRGRID".$g."' value=".$g." onClick='posgrid(".$g.",".$i.")'></td>";
											}
											else
												echo "<td class=".$gridcolor." colspan=5><input type='RADIO' name='RGRID".$g."' id='WRGRID".$g."' value=".$g." onClick='posgrid(".$g.",".$i.")'></td>";
											echo "</tr>";
										}
										if(strpos($Gridseg[1],"NS") !== false)
										{
											echo "<tr>";
											echo "<td id=tipoL06GRIDW>Total</td>";
											for ($g=0;$g<count($Gridtip);$g++)
											{
												if(substr($Gridtip[$g],0,2) == "NS")
													echo "<td id=tipoL06GRIDW>".$Gdatasuma[$g]."</td>";
												else
													echo "<td id=tipoL06GRIDW></td>";
											}
											echo "<td id=tipoL06GRIDW colspan=5></td>";
											echo "</tr>";
										}
										echo "</table><br>";
										echo "</td>";
										$campos += $registro[$i][32];
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."J".$i."'))</script>";
									break;
									case "Referencia":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$alto=$registro[$i][35];
										$ancho=$registro[$i][36];
										if(!is_numeric($alto) or (is_numeric($alto) and ($alto < 1 or $alto > 900)))
											$alto = 1;
										if(!is_numeric($ancho) or (is_numeric($ancho) and ($ancho < 1 or $ancho > 900)))
											$ancho = 1;
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										$col=($span * 80) / ($n * 2);
										//$registro[$i][0]="";
										$num1=0;
										$winaux=$wing;
										while($num1 == 0 and $winaux > 0)
										{
											$query = "SELECT Movdat from ".$empresa."_".$registro[$i][6]." where movpro='".$registro[$i][6]."' and movcon='".$registro[$i][7]."' and movhis='".$whis."' and moving='".$winaux."' order by id desc";
											$err1 = mysql_query($query,$conex);
											$num1 = mysql_num_rows($err1);
											if ($num1 > 0)
											{
												$row1 = mysql_fetch_array($err1);
												$temporal=$row1[0];
												if(strpos($temporal,"</") > 0)
												{
													$row1[0]=option($temporal);
												}
												if(!isset($registro[$i][0]) or $registro[$i][0] == $registro[$i][8] or strlen($registro[$i][0]) == 0 or ($temporal != $registro[$i][0] and is_numeric($temporal)))
													$registro[$i][0]=$row1[0];
												if($registro[$i][0] == "undefined")
													$registro[$i][0]="";
												if(strpos($registro[$i][0],"-") !== false and $registro[$i][49] != "NS" and strpos($registro[$i][0],"-") != 0)
													$registro[$i][0]=substr($registro[$i][0],strpos($registro[$i][0],"-")+1);
												/*
												if($registro[$i][0] == $registro[$i][8] or $registro[$i][49] == "BLOCK")
												{
													$temporal=$row1[0];
													if(strpos($temporal,"</") > 0)
													{
														$row1[0]=option($temporal);
													}
													$registro[$i][0]=$row1[0];
													if($registro[$i][0] == "undefined")
														$registro[$i][0]="";
													if(strpos($registro[$i][0],"-") !== false)
														$registro[$i][0]=substr($registro[$i][0],strpos($registro[$i][0],"-")+1);
												}
												else
												{
													if($registro[$i][0] == $registro[$i][8])
														$registro[$i][0]="";
												}
												* */
											}
											else
												$winaux=(string)((integer)$winaux - 1);
										}

										$position="W".$i;
										if($registro[$i][49] == "BLOCK")
											$estado="disabled";
										else
											$estado="";
										$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										if(isset($RECOVERY) or $registro[$i][0] != $registro[$i][8] and $num1 == 0)
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." ".$estado." id='W".$i."' class=tipo3".$OBL." onDblClick=".$id.">".$registro[$i][0]."</textarea>".$UDM."</td>";
										else
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." ".$estado." id='W".$i."' class=tipo3".$OBL." onDblClick=".$id.">".htmlentities($registro[$i][0])."</textarea>".$UDM."</td>";
										$campos += $registro[$i][32];	
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."W".$i."'))</script>";
									break;
									case "Numero":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$alto=$registro[$i][35];
										$ancho=$registro[$i][36];
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=30 id='T".$i."' value='".htmlentities($registro[$i][0])."' onkeypress='return teclado(event)' onBlur='minmax(this,".$registro[$i][47].",".$registro[$i][48].",".$OBLS.")' class=tipo3".$OBL.">".$UDM."</td>";
										$campos += $registro[$i][32];
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."T".$i."'))</script>";
									break;
									case "Formula":
										$registro[$i][26]=strtoupper($registro[$i][26]);
										$registro[$i][26]=formula1($registro[$i][26]);
										for ($w=0;$w<$num;$w++)
										{
											$registro[$i][26]=str_replace($orden[$w][0],$orden[$w][1],$registro[$i][26]);
										}
										$registro[$i][26]=formula($registro[$i][26]);
										
										if($registro[$i][26]!="." && $registro[$i][26]!="NO APLICA")
										{
											@eval($registro[$i][26]);
										}
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$alto=$registro[$i][35];
										$ancho=$registro[$i][36];
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										$col=($span * 120) / ($n * 2);
										$position="T".$i;
										$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='T".$i."' value='".htmlentities($registro[$i][0])."'  class=tipo3".$OBL." onClick=".$id.">".$UDM."</td>";
										$campos += $registro[$i][32];
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."T".$i."'))</script>";
									break;
									case "Busqueda":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										$col=($span * 120) / ($n * 2);
										$position="T".$i;
										$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center><IMG SRC='/matrix/images/medical/HCE/LUPA.png' alt='BUSQUEDA' onClick=".$id."></td>";
										$campos += $registro[$i][32];
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."T".$i."'))</script>";
									break;
									case "Booleano":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;	
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$span=$registro[$i][32] ;
										$rwidth=(integer)(($width * $span)/$Span);
										if(isset($registro[$i][0]))
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02P".$CHECK."><input type='checkbox' name='registro[".$i."][0]' id='C".$i."' class=tipoL02M".$OBL." checked>".htmlentities($registro[$i][9]).$salto."</td>";
										else
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02P".$CHECK."><input type='checkbox' name='registro[".$i."][0]' id='C".$i."' class=tipoL02M".$OBL.">".htmlentities($registro[$i][9]).$salto."</td>";
										$campos += $registro[$i][32];	
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."C".$i."'))</script>";
									break;
									case "Titulo":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										$position="TIT".$i;
										$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','2')";
										if($registro[$i][32] <= $faltan and $faltan > 0)
										{
											$span=$registro[$i][32];
											$rwidth=(integer)(($width * $span)/$Span);
											if($registro[$i][32] == $n)
												echo "<td id=tipoL06 colspan=".$span."><IMG SRC='/matrix/images/medical/hce/grabarT.png' id='gravar' style='vertical-align:middle;' onDblClick=".$id." title='RESPALDAR INFORMACI&Oacute;N'>&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
											else
												echo "<td id=tipoL06 colspan=".$span." width=".$rwidth."><IMG SRC='/matrix/images/medical/hce/grabarT.png' id='gravar' style='vertical-align:middle;' onDblClick=".$id." title='RESPALDAR INFORMACI&Oacute;N'>&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
											$campos += $registro[$i][32];
										}
										else
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr>";
											if($totdiv > 0)
												echo "</table></td></tr>";
											$campos = 0;
											$span=$registro[$i][32];
											$rwidth=(integer)(($width * $span)/$Span);
											if($registro[$i][32] == $n)
											{
												$totdiv++;
												echo "<tr OnClick='toggleDisplay(div".$totdiv.")'><td id=tipoL06 colspan=".$span."><IMG SRC='/matrix/images/medical/hce/grabarT.png' id='gravar' style='vertical-align:middle;' onDblClick=".$id." title='RESPALDAR INFORMACI&Oacute;N'>&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
											}
											else
												echo "<tr><td id=tipoL06 colspan=".$span." width=".$rwidth."><IMG SRC='/matrix/images/medical/hce/grabarT.png' id='gravar' style='vertical-align:middle;' onDblClick=".$id." title='RESPALDAR INFORMACI&Oacute;N'>&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
											$campos += $registro[$i][32];	
										}	
									break;
									case "Subtitulo":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										if($registro[$i][32] == $n)
											$css="tipoL07";
										else
											$css="tipoL07B";
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										if(strlen($registro[$i][26]) > 1)
											echo "<td id=".$css." colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][26])."</td>";
										else
											echo "<td id=".$css." colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
										$campos += $registro[$i][32];	
									break;
									case "Label":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										if($registro[$i][49] == "SMART")
										{
											$alto=1;
											$ancho=20;
											@eval($registro[$i][26]);
											$err1 = mysql_query($query,$conex);
											// if(is_resource($err1))
											if(is_object($err1))
											{
												$num1 = mysql_num_rows($err1);
												if ($num1 > 0)
												{
													$row1 = mysql_fetch_array($err1);
													$registro[$i][0]=$row1[0];
													$registro[$i][9]=$row1[0];
												}
											}
											echo "<td id=tipoL07AW colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
										}
										else
										{
											if(strlen($registro[$i][26]) > 1)
												echo "<td id=tipoL07A colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][26])."</td>";
											else
												echo "<td id=tipoL07A colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
										}
										$campos += $registro[$i][32];	
									break;
									case "Memo":
										if($registro[$i][41] == "M")
										{
											if($registro[$i][32]>$n)
												$registro[$i][32]=$n;
											$faltan=$n - $campos;
											if($registro[$i][32] > $faltan or $faltan <= 0)
											{
												for ($w=0;$w<$faltan;$w++)
													echo "<td id=tipoL02></td>";
												echo "</tr><tr>";
												$campos = 0;
											}
											$alto=$registro[$i][35];
											$ancho=$registro[$i][36];
											if(!is_numeric($alto) or (is_numeric($alto) and ($alto < 1 or $alto > 900)))
												$alto = 1;
											if(!is_numeric($ancho) or (is_numeric($ancho) and ($ancho < 1 or $ancho > 900)))
												$ancho = 1;
											$span=$registro[$i][32];
											$rwidth=(integer)(($width * $span)/$Span);
											$col=($span * 150) / ($n * 2);
											$registro[$i][26]=str_replace("HIS",chr(39).$whis.chr(39),$registro[$i][26]);
											$registro[$i][26]=str_replace("ING",chr(39).$wing.chr(39),$registro[$i][26]);
											$query = $registro[$i][26];
											$conex_o = odbc_connect($registro[$i][6],'','');
											$err_o = odbc_do($conex_o,$query);
											if (odbc_fetch_row($err_o))
											{
												if($registro[$i][0] == $registro[$i][8] or $registro[$i][49] == "BLOCK")
													$registro[$i][0]=odbc_result($err_o,1);
											}
											else
											{
												if($registro[$i][0] == $registro[$i][8])
													$registro[$i][0]="";
											}
											odbc_close($conex_o);
											if($registro[$i][49] == "BLOCK")
												$estado="disabled";
											if(isset($RECOVERY))
												echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL." ".$estado.">".$registro[$i][0]."</textarea>".$UDM."</td>";
											else
												echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL." ".$estado.">".htmlentities($registro[$i][0])."</textarea>".$UDM."</td>";
											$campos += $registro[$i][32];	
											if(isset($completo) and $completo == 1)
												echo "<script> soloLectura(document.getElementById('"."W".$i."'))</script>";
										}
										elseif($registro[$i][41] == "I")
										{
											if($registro[$i][32]>$n)
												$registro[$i][32]=$n;
											$faltan=$n - $campos;
											if($registro[$i][32] > $faltan or $faltan <= 0)
											{
												for ($w=0;$w<$faltan;$w++)
													echo "<td id=tipoL02></td>";
												echo "</tr><tr>";
												$campos = 0;
											}
											$alto=$registro[$i][35];
											$ancho=$registro[$i][36];
											if(!is_numeric($alto) or (is_numeric($alto) and ($alto < 1 or $alto > 900)))
												$alto = 1;
											if(!is_numeric($ancho) or (is_numeric($ancho) and ($ancho < 1 or $ancho > 900)))
												$ancho = 1;
											$span=$registro[$i][32];
											$rwidth=(integer)(($width * $span)/$Span);
											$col=($span * 150) / ($n * 2);
											$registro[$i][26]=str_replace("HIS",chr(39).$whis.chr(39),$registro[$i][26]);
											$registro[$i][26]=str_replace("ING",chr(39).$wing.chr(39),$registro[$i][26]);
											if(strpos($registro[$i][26],"REGISTRO") !== false)
											{
												$registro[$i][26]=str_replace("REGISTRO",$registro[(integer)substr($registro[$i][26],0,strpos($registro[$i][26],"s"))][0],$registro[$i][26]);
												$registro[$i][26]=substr($registro[$i][26],strpos($registro[$i][26],"s"));
											}
											$query = $registro[$i][26];
											$err1 = mysql_query($query,$conex);
											$num1 = mysql_num_rows($err1);
											if ($num1 > 0)
											{
												$row1 = mysql_fetch_array($err1);
												if($registro[$i][0] == $registro[$i][8] or $registro[$i][49] == "BLOCK")
												{
													$temporal=$row1[0];
													if(strpos($temporal,"</") > 0)
													{
														$row1[0]=option($temporal);
													}
													$registro[$i][0]=$row1[0];
												}
											}
											else
											{
												if($registro[$i][0] == $registro[$i][8])
													$registro[$i][0]="";
											}
											if($registro[$i][49] == "BLOCK")
												$estado="disabled";
											if(isset($RECOVERY))
												echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL." ".$estado.">".$registro[$i][0]."</textarea>".$UDM."</td>";
											else
												echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL." ".$estado.">".htmlentities($registro[$i][0])."</textarea>".$UDM."</td>";
											$campos += $registro[$i][32];	
											if(isset($completo) and $completo == 1)
												echo "<script> soloLectura(document.getElementById('"."W".$i."'))</script>";
										}
										elseif($registro[$i][41] == "R")
										{
											if($registro[$i][32]>$n)
												$registro[$i][32]=$n;
											$faltan=$n - $campos;
											if($registro[$i][32] > $faltan or $faltan <= 0)
											{
												for ($w=0;$w<$faltan;$w++)
													echo "<td id=tipoL02></td>";
												echo "</tr><tr>";
												$campos = 0;
											}
											$alto=$registro[$i][35];
											$ancho=$registro[$i][36];
											if(!is_numeric($alto) or (is_numeric($alto) and ($alto < 1 or $alto > 900)))
												$alto = 1;
											if(!is_numeric($ancho) or (is_numeric($ancho) and ($ancho < 1 or $ancho > 900)))
												$ancho = 1;
											$span=$registro[$i][32];
											$rwidth=(integer)(($width * $span)/$Span);
											$col=($span * 150) / ($n * 2);
											@eval($registro[$i][26]);
											if($registro[$i][49] != "ALGO")
											{
												if($registro[$i][49] != "INVI")
												{
													if($registro[$i][49] == "VISB")
														$estado="disabled";
													if(isset($RECOVERY))
														echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class='tipo3".$OBL."' ".$estado.">".$registro[$i][0]."</textarea>".$UDM."</td>";
													else
														echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class='tipo3".$OBL."' ".$estado.">".htmlentities($registro[$i][0])."</textarea>".$UDM."</td>";
												}
												else
												{
													if(isset($RECOVERY))
														echo "<td colspan=".$span." width=".$rwidth." id=tipoL02><textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3GRID>".$registro[$i][0]."</textarea></td>";
													else
														echo "<td colspan=".$span." width=".$rwidth." id=tipoL02><textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3GRID>".htmlentities($registro[$i][0])."</textarea></td>";
												}
											}
											$campos += $registro[$i][32];	
											if(isset($completo) and $completo == 1)
												echo "<script> soloLectura(document.getElementById('"."W".$i."'))</script>";
										}
										else
										{
											if($registro[$i][32]>$n)
												$registro[$i][32]=$n;
											$faltan=$n - $campos;
											if($registro[$i][32] > $faltan or $faltan <= 0)
											{
												for ($w=0;$w<$faltan;$w++)
													echo "<td id=tipoL02></td>";
												echo "</tr><tr>";
												$campos = 0;
											}
											$alto=$registro[$i][35];
											$ancho=$registro[$i][36];
											if(!is_numeric($alto) or (is_numeric($alto) and ($alto < 1 or $alto > 900)))
												$alto = 1;
											if(!is_numeric($ancho) or (is_numeric($ancho) and ($ancho < 1 or $ancho > 900)))
												$ancho = 1;
											$span=$registro[$i][32];
											$rwidth=(integer)(($width * $span)/$Span);
											$col=($span * 150) / ($n * 2);
											$HCESEG = $registro[$i][50];
											echo "<input type='hidden' id='HCESEG".$i."' value='".$HCESEG."'>";
											$HCE_Seguridad = "onkeypress='return tecladoCP(event,".$i.");'";
											if(isset($RECOVERY))
												echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL." ".$HCE_Seguridad.">".$registro[$i][0]."</textarea>".$UDM."</td>";
											else
												echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL." ".$HCE_Seguridad.">".htmlentities($registro[$i][0])."</textarea>".$UDM."</td>";
											$campos += $registro[$i][32];	
											if(isset($completo) and $completo == 1)
												echo "<script> soloLectura(document.getElementById('"."W".$i."'))</script>";
										}
									break;
									case "Imagen":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth."><font face='Arial' size=2 color=#000066><b>".htmlentities($registro[$i][9])."</b> <br> Haga Click en la Imagen Para Ver los Recuadros</font>".$salto."<br><IMG SRC='/matrix/images/medical/HCE/".$registro[$i][6]."' id='mainImage'  onClick='pintardivs()' /></td>";
										echo "<script type='text/javascript'>";
										echo "  var tipmage = new Tipmage('mainImage', true);";
										echo "  tipmage.startup();";
										echo "  varable = document.getElementById('Hgrafica').value;";
										echo "  var ID = 1;";
										echo "  if(varable.length > 0)";
										echo "  {";
										echo "    frag1 = varable.split('^');";
										echo " 	  for (i=1;i<frag1.length;i++)";
										echo " 	  {";
										echo " 		frag2 = frag1[i].split('~');";  
										echo "      tipmage.setTooltip(frag2[1],frag2[2],frag2[3],frag2[4],frag2[5],frag2[0]);";
										echo "    }";
										echo "  };";
										echo "</script>";
										echo "<script type='text/javascript'>";
										echo "  tipmage.onInsert = function (identifier,posx,posy,width,height,text) ";
										echo "  {";
										echo "    document.getElementById('Hgrafica').value = document.getElementById('Hgrafica').value + '^' + parseInt(identifier)+'~'+posx+'~'+posy+'~'+width+'~'+height+'~'+text;";
										//echo "    alert(document.getElementById('Hgrafica').value);";
										echo "  };";
										echo "</script>";
										echo "<script type='text/javascript'>";
										echo "  tipmage.onUpdate = function (identifier,posx,posy,width,height,text) ";
										echo "  {";
										echo "    final = '';";
										echo "    arreglo = document.getElementById('Hgrafica').value;";
										echo "    frag1 = arreglo.split('^');";
										echo " 	  for (i=1;i<frag1.length;i++)";
										echo " 	  {";
										echo " 		frag2 = frag1[i].split('~');";
										echo "      if(frag2[0] == identifier)";
										echo "		{";
										echo "			frag2[1]=posx;";
										echo "			frag2[2]=posy;";
										echo "			frag2[3]=width;";
										echo "			frag2[4]=height;";
										echo "			frag2[5]=text;";
										echo "          frag1[i]=frag2[0]+'~'+frag2[1]+'~'+frag2[2]+'~'+frag2[3]+'~'+frag2[4]+'~'+frag2[5];";
										echo "		}";
										echo " 		final += '^' + frag1[i];";
										echo "    }";
										echo "    document.getElementById('Hgrafica').value = final;";
										//echo "    alert(document.getElementById('Hgrafica').value);";
										echo "  };";
										echo "</script>";
										echo "<script type='text/javascript'>";
										echo "  tipmage.onDelete = function (identifier,posx,posy,width,height,text) ";
										echo "  {";
										echo "    final = '';";
										echo "    arreglo = document.getElementById('Hgrafica').value;";
										echo "    frag1 = arreglo.split('^');";
										echo " 	  for (i=1;i<frag1.length;i++)";
										echo " 	  {";
										echo " 		frag2 = frag1[i].split('~');";
										echo "      if(frag2[0] != identifier)";
										echo "		{";
										echo "          alert(frag1[i]);";
										echo "			final = final + '^' + frag1[i];";
										echo "		}";
										echo "    }";
										echo "    document.getElementById('Hgrafica').value = final;";
										//echo "    alert(document.getElementById('Hgrafica').value);";
										echo "  };";
										echo "</script>";
										$campos += $registro[$i][32];	
									break;
									case "Link":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$alto=$registro[$i][35];
										$ancho=$registro[$i][36];
										$span=$registro[$i][32];
										
										$rwidth=(integer)(($width * $span)/$Span);
										if(is_numeric($registro[$i][25]))
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".$salto."<input type='button' id='btnModal".$i."' name='btnModal".$i."' value='".htmlentities($registro[$i][9])."' onClick='javascript:activarModalIframe(\"".htmlentities($registro[$i][9])."\",\"nombreIframe\",\"/matrix/HCE/Procesos/HCE.php?accion=W1&ok=0&empresa=".$empresa."&origen=".$origen."&wdbmhos=".$wdbmhos."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$registro[$i][25]."&whis=".$whis."&wing=".$wing."&wsex=".$wsex."&wtitframe=no&width=".$width."\",\"".$alto."\",\"".$ancho."\");'></td>";
										else
										{
											$registro[$i][25]=str_replace("HIS",$whis,$registro[$i][25]);
											$registro[$i][25]=str_replace("ING",$wing,$registro[$i][25]);
											$registro[$i][25]=str_replace("CED",$wcedula,$registro[$i][25]);
											$registro[$i][25]=str_replace("TDO",$wtipodoc,$registro[$i][25]);
											$registro[$i][25]=str_replace("EMP",$empresa,$registro[$i][25]);
											$registro[$i][25]=str_replace("HOS",$wdbmhos,$registro[$i][25]);
											$registro[$i][25]=str_replace("ORG",$origen,$registro[$i][25]);
											if($registro[$i][49] == "SMART")
												@eval($registro[$i][26]);
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".$salto."<input type='button' id='btnModal".$i."' name='btnModal".$i."' value='".htmlentities($registro[$i][9])."' onClick='javascript:activarModalIframe(\"".htmlentities($registro[$i][9])."\",\"nombreIframe\",\"".$registro[$i][25]."\",\"".$alto."\",\"".$ancho."\");'></td>";
										}
										$campos += $registro[$i][32];	
									break;
									case "Password":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										if(strlen($registro[$i][0]) > 1)
										{
											$query="SELECT count(*) FROM usuarios WHERE codigo = '".$registro[$i][0]."'"; 
											$err1 = mysql_query($query,$conex);
											$row1 = mysql_fetch_array($err1);
											if($row1[0] == 0)
												$wpassmsg = "USUARIO NO EXISTE EN MATRIX. INTENTELO NUEVAMENTE!!";
											else
												$wpassmsg = "";
										}
										$alto=$registro[$i][35];
										$ancho=$registro[$i][36];
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										echo "<td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='password' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='T".$i."' value='".htmlentities($registro[$i][0])."' class=tipo3".$OBL." onBlur=".$id.">&nbsp;&nbsp;<div id='TipoL02PMSG'>".$wpassmsg."</div></td>";
										$campos += $registro[$i][32];	
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."T".$i."'))</script>";
									break;
									case "Fecha":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if(($registro[$i][0] == $registro[$i][8] or $registro[$i][0] == "") and $registro[$i][8] == "DATE")
											$registro[$i][0] = date("Y-m-d");
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<td id=tipoL02 colsp an=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."<input id='F".$i."' type='text' size='12' maxlength='12' NAME='registro[".$i."][0]' onkeypress='back(this)' class=tipo3".$OBL." value='".$registro[$i][0]."'></td>";
										$campos += $registro[$i][32];
										if((isset($completo) and $completo == 1) or $registro[$i][29] == "2")
											echo "<script> soloLectura(document.getElementById('"."F".$i."'))</script>";
									break;
									case "Tabla":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										echo "<input type='hidden' id='cual".$i."' value='".$registro[$i][43]."'>";
										echo "<input type='hidden' id='Tcual".$i."' value='".htmlentities($registro[$i][44])."'>";
										echo "<input type='hidden' id='SimMul".$i."' value='".$registro[$i][41]."'>";
										echo "<input type='hidden' id='TTipe".$i."' value='".$registro[$i][49]."'>";
										if(substr($registro[$i][49],0,1) == "Q")
										{
											$registro[$i][26]=str_replace("HIS",chr(39).$whis.chr(39),$registro[$i][26]);
											$registro[$i][26]=str_replace("ING",chr(39).$wing.chr(39),$registro[$i][26]);
											$query = $registro[$i][26];
											$err1 = mysql_query($query,$conex);
											$num1 = mysql_num_rows($err1);
											if ($num1 > 0)
											{
												$row1 = mysql_fetch_array($err1);
												if($registro[$i][0] == $registro[$i][8])
												{
													$registro[$i][0]=$row1[0];
												}
											}
										}
										// echo "<pre>".print_r($registro[$i],true)."</pre>";
										$wcampos=explode(",",$registro[$i][40]);
										$wncampos=explode(",",$registro[$i][42]);
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<td id=tipoL02 colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
										$position="M".$i;
										$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										if($registro[$i][45] != "on")
										{
											echo "<select name='Tselect[".$i."]' id='TS".$i."' OnChange=".$id." class=tipo3>";
												if(isset($Tselect[$i]))
												{
													if(strcmp($Tselect[$i],$wncampos[0]) == 0)
													{
														echo "<option value='".$wncampos[0]."' selected>".$wncampos[0]."</option>";
														echo "<option value='".$wncampos[1]."'>".$wncampos[1]."</option>";
														$sp=0;
													}
													else
													{
														echo "<option value='".$wncampos[0]."'>".$wncampos[0]."</option>";
														echo "<option value='".$wncampos[1]."' selected>".$wncampos[1]."</option>";
														$sp=1;
													}
												}
												else
												{
													echo "<option value='".$wncampos[0]."'>".$wncampos[0]."</option>";
													echo "<option value='".$wncampos[1]."'>".$wncampos[1]."</option>";
													$sp=0;
												}
											echo "</select><br>";
										}
										if($registro[$i][45] != "on")
										{
											if($registro[$i][49] == "INV")
											{
												$tabtem=array();
												$tt=count($wcampos)-1;
												for ($z=0;$z<count($wcampos);$z++)
													$tabtem[$z] = $wcampos[$tt - $z];
												$registro[$i][40]=implode(",", $tabtem);
											}
											// $query="1SELECT ".$registro[$i][40]." FROM ".$registro[$i][6]." WHERE ".$wcampos[$sp]." LIKE var ORDER BY ".$wcampos[$sp].";";
											
											// Si el campo es tipo tabla y tiene un valor por defecto, se valida que el dato en el campo a validar sea igual al valor por defecto
											$filtroCampoAValidar = "";
											if($registro[$i][8]!="" && $registro[$i][8]!="." && $registro[$i][8]!="NO APLICA")
											{
												if($registro[$i][0] == $registro[$i][8])
												{
													$registro[$i][0]="";
												}
												
												$tablaValidacion = explode("_",$registro[$i][6]);  
					
												$queryCampo = " SELECT descripcion 
																	FROM det_formulario 
																	WHERE medico='".trim($tablaValidacion[0])."' 
																	AND codigo='".trim($tablaValidacion[1])."' 
																	AND campo='".$registro[$i][7]."'
																	AND activo = 'A'; ";
												$resCampo = mysql_query($queryCampo,$conex) or die (mysql_errno()." - en el query: ".$queryCampo." - ".mysql_error());
												$numCampo = mysql_num_rows($resCampo);
												
												if($numCampo>0)
												{
													$rowCampo = mysql_fetch_array($resCampo);
													$filtroCampoAValidar = "AND ".$rowCampo['descripcion']." = \"".$registro[$i][8]."\" ";
												}
											}
											
											$query="1SELECT ".$registro[$i][40]." FROM ".$registro[$i][6]." WHERE ".$wcampos[$sp]." LIKE var ".$filtroCampoAValidar." ORDER BY ".$wcampos[$sp].";";
										}
										else
										{
											if($registro[$i][49] == "SMART")
											{
												$query="3".$registro[$i][1].$registro[$i][2];
												@eval($registro[$i][25]);
											}
											else
											{
												// $sp=0;
												// $query="2SELECT ".$registro[$i][40]." FROM ".$registro[$i][6]." WHERE ".$wcampos[$sp]." LIKE var ";
												// for ($z=1;$z<count($wcampos);$z++)
													// $query .= " or ".$wcampos[$z]." LIKE var ";
												// $query .= "ORDER BY ".$wcampos[$sp].";";
												// $query .= "ORDER BY ".$wcampos[$sp].";";
												
												// Si el campo es tipo tabla y tiene un valor por defecto, se valida que el dato en el campo a validar sea igual al valor por defecto
												$filtroCampoAValidar = "";
												if($registro[$i][8]!="" && $registro[$i][8]!="." && $registro[$i][8]!="NO APLICA")
												{
													if($registro[$i][0] == $registro[$i][8])
													{
														$registro[$i][0]="";
													}	
														
												
													$tablaValidacion = explode("_",$registro[$i][6]);  
						
													$queryCampo = " SELECT descripcion 
																		FROM det_formulario 
																		WHERE medico='".trim($tablaValidacion[0])."' 
																		AND codigo='".trim($tablaValidacion[1])."' 
																		AND campo='".$registro[$i][7]."'
																		AND activo = 'A'; ";
													$resCampo = mysql_query($queryCampo,$conex) or die (mysql_errno()." - en el query: ".$queryCampo." - ".mysql_error());
													$numCampo = mysql_num_rows($resCampo);
													
													if($numCampo>0)
													{
														$rowCampo = mysql_fetch_array($resCampo);
														$filtroCampoAValidar = "AND ".$rowCampo['descripcion']." = \"".$registro[$i][8]."\" ";
													}
												}
												
												$sp=0;
												$query="2SELECT ".$registro[$i][40]." FROM ".$registro[$i][6]." WHERE (".$wcampos[$sp]." LIKE var ";
												for ($z=1;$z<count($wcampos);$z++)
													$query .= " or ".$wcampos[$z]." LIKE var ";
												$query .= ") ";
												$query .= $filtroCampoAValidar;
												$query .= "ORDER BY ".$wcampos[$sp].";";
											}
										}
										echo "<input type='hidden' id='query".$i."' value='".$query."'>";
										
										if(!is_numeric($registro[$i][25]))
											$registro[$i][25] = 30;
										echo "<input type='text' id='M".$i."' onFocus='javascript:limpiarCampo(this);' size=".$registro[$i][25].">";
										echo "<br>";
										if($registro[$i][43] == "on")
										{
											$numRB=$registro[$i][37];
											$cualif=explode(",",$registro[$i][44]);
											if(count($cualif) > 2)
											{
												for ($k=$numRB;$k<count($cualif);$k++)
												{
													$varRT="RT".$i;
													if($k == 2)
													{
														echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[$k],0,1))." checked>".htmlentities($cualif[$k]);
													}
													else
													{
														echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[$k],0,1)).">".htmlentities($cualif[$k]);
													}
												}
												echo "<br>";
											}
										}
										echo "<select name='registro[".$i."][0]' id='selAuto".$i."' multiple=multiple size=".$registro[$i][35]." class=tipoTAB".$OBL." onDblClick='javascript:quitarComponente(\"$i\");'>";
										echo $registro[$i][0];
										echo "</select>";
										echo '<script language="Javascript">';
										echo "document.getElementById('selAuto".$i."').style.width=".$registro[$i][36].";";
										echo '</script>';
										echo "<input type='hidden' id='registro[".$i."][36]' value='".$registro[$i][36]."'>";
										echo "</td>";
										$campos += $registro[$i][32];	
										if(isset($completo) and $completo == 1)
											echo "<script> soloLectura(document.getElementById('"."M".$i."'))</script>";
									break;
									case "Seleccion":
										if($registro[$i][41] != "M" and $registro[$i][41] != "M1")
										{
											if($registro[$i][34] == "off")
											{
												if($registro[$i][32]>$n)
													$registro[$i][32]=$n;
												$faltan=$n - $campos;
												if($registro[$i][32] > $faltan or $faltan <= 0)
												{
													for ($w=0;$w<$faltan;$w++)
														echo "<td id=tipoL02".$CHECK."></td>";
													echo "</tr><tr>";
													$campos = 0;
												}
												$span=$registro[$i][32];
												$rwidth=(integer)(($width * $span)/$Span);
												echo "<td id=tipoL02".$CHECK." colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
												if($registro[$i][49] == "SMART")
												{
													//$registro[$i][26]=strtoupper($registro[$i][26]);
													$registro[$i][26]=formula1($registro[$i][26]);
													for ($w=0;$w<$num;$w++)
													{
														$registro[$i][26]=str_replace($orden[$w][0],$orden[$w][1],$registro[$i][26]);
													}
													$registro[$i][26]=formula($registro[$i][26]);
													$registro[$i][26]=str_replace("HIS",chr(39).$whis.chr(39),$registro[$i][26]);
													$registro[$i][26]=str_replace("ING",chr(39).$wing.chr(39),$registro[$i][26]);
													$registro[$i][26] = strtolower($registro[$i][26]);
													@eval($registro[$i][26]);
													$err1 = mysql_query($query,$conex);
													$num1 = mysql_num_rows($err1);
													$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
													echo "<select name='registro[".$i."][0]' id='S".$i."' class=tipo3".$OBL." onChange=".$id.">";
													echo "<option value='Seleccione'>Seleccione</option>";
													if ($num1>0)
													{
														for ($j=0;$j<$num1;$j++)
														{
															$row1 = mysql_fetch_array($err1);
															$classel="";
															if($registro[$i][25] != ".")
																@eval($registro[$i][25]);
															if(trim($registro[$i][0]) == trim($row1[0]))
																echo "<option selected class='".$classel."' value='".$row1[0]."'>".htmlentities($row1[0])."</option>";
															else
																echo "<option class='".$classel."' value='".$row1[0]."'>".htmlentities($row1[0])."</option>";
														}
													}
													echo "</select>";
													echo "</td>";
												}
												else
												{
													$query = "SELECT Selcda, Selnda from ".$empresa."_".$registro[$i][6]." where Seltab='".$registro[$i][7]."' and Selest='on' order by Selcda";
													$err1 = mysql_query($query,$conex);
													$num1 = mysql_num_rows($err1);
													echo "<select name='registro[".$i."][0]' id='S".$i."' class=tipo3".$OBL.">";
													echo "<option value='Seleccione'>Seleccione</option>";
													if ($num1>0)
													{
														if($registro[$i][49] == "CRONO" and $registro[$i][0] == $registro[$i][8])
														{
															@eval($registro[$i][26]);
														}
														for ($j=0;$j<$num1;$j++)
														{
															$row1 = mysql_fetch_array($err1);
															$registro[$i][0]=ver($registro[$i][0]);
															if($registro[$i][0] == $row1[0])
															{
																//echo "<option selected value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[0])."-".htmlentities($row1[1])."</option>";
																echo "<option selected value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[1])."</option>";
															}
															else
															{
																//echo "<option selected value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[0])."-".htmlentities($row1[1])."</option>";
																echo "<option value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[1])."</option>";
															}
														}
													}
													echo "</select>";
													echo "</td>";
												}
												$campos += $registro[$i][32];	
												if(isset($completo) and $completo == 1)
													echo "<script> soloLectura(document.getElementById('"."S".$i."'))</script>";	
											}
											else
											{
												if($registro[$i][32]>$n)
													$registro[$i][32]=$n;
												$faltan=$n - $campos;
												if($registro[$i][32] > $faltan or $faltan <= 0)
												{
													for ($w=0;$w<$faltan;$w++)
														echo "<td id=tipoL02".$CHECK."></td>";
													echo "</tr><tr>";
													$campos = 0;
												}
												$span=$registro[$i][32];
												$rwidth=(integer)(($width * $span)/$Span);
												echo "<td id=tipoL02".$CHECK." colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
												$query = "SELECT Selcda, Selnda from ".$empresa."_".$registro[$i][6]." where Seltab='".$registro[$i][7]."' and Selest='on' order by Selcda";
												$err1 = mysql_query($query,$conex);
												$num1 = mysql_num_rows($err1);
												if ($num1>0)
												{
													if($registro[$i][49] == "CRONO" and $registro[$i][0] == $registro[$i][8])
													{
														@eval($registro[$i][26]);
													}
													$filasR=$registro[$i][37];
													$rb=0;
													echo "<table border=0  CELLSPACING=0 align=center>";
													echo "<tr>";
													for ($j=0;$j<$num1;$j++)
													{
														if($rb >= $filasR)
														{
															echo "</tr><tr>";
															$rb=0;
														}
														$row1 = mysql_fetch_array($err1);
														if(substr(trim($registro[$i][0]),0,strpos(trim($registro[$i][0]),"-")) == $row1[0])
														//if(trim($registro[$i][0]) == $row1[0]."-".$row1[1])
														{
															if($rb <= $filasR)
																echo "<td id=tipoRB".$CHECK." onDblclick=\"ninguno('R".$i."')\"><input type='RADIO' name='R".$i."' id='R".$i."' value='".$row1[0]."-".$row1[1]."' class=tipoL02M".$OBL." checked>".htmlentities($row1[1])."</td>";
															else
																echo "<tr><td id=tipoRB".$CHECK." onDblclick=\"ninguno('R".$i."')\"><input type='RADIO' name='R".$i."' id='R".$i."' value='".$row1[0]."-".$row1[1]."' class=tipoL02M".$OBL." checked>".htmlentities($row1[1])."</td>";
															$rb++;
														}
														else
														{
															if($rb <= $filasR)
																echo "<td id=tipoRB".$CHECK." onDblclick=\"ninguno('R".$i."')\"><input type='RADIO' name='R".$i."' id='R".$i."' class=tipoL02M".$OBL." value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[1])."</td>";
															else
																echo "<tr><td id=tipoRB".$CHECK." onDblclick=\"ninguno('R".$i."')\"><input type='RADIO' name='R".$i."' id='R".$i."' class=tipoL02M".$OBL." value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[1])."</td>";
															$rb++;
														}
													}
													echo "</table>";
												}
												echo "</td>";
												$campos += $registro[$i][32];	
												if(isset($completo) and $completo == 1)
													echo "<script> soloLectura(document.getElementById('"."R".$i."'))</script>";
											}
										}
										else
										{
											// NUEVO TIPO DE CAMPO PARA DIAGNOSTICO
											if($registro[$i][32]>$n)
													$registro[$i][32]=$n;
												$faltan=$n - $campos;
												if($registro[$i][32] > $faltan or $faltan <= 0)
												{
													for ($w=0;$w<$faltan;$w++)
														echo "<td id=tipoL02".$CHECK."></td>";
													echo "</tr><tr>";
													$campos = 0;
												}
												if($registro[$i][49] == "Q")
												{
													$registro[$i][26]=str_replace("HIS",chr(39).$whis.chr(39),$registro[$i][26]);
													$registro[$i][26]=str_replace("ING",chr(39).$wing.chr(39),$registro[$i][26]);
													$query = $registro[$i][26];
													$err1 = mysql_query($query,$conex);
													$num1 = mysql_num_rows($err1);
													if ($num1 > 0)
													{
														$row1 = mysql_fetch_array($err1);
														if($registro[$i][0] == $registro[$i][8])
														{
															$registro[$i][0]=option1($row1[0]);
														}
													}
													else
													{
														if($registro[$i][0] == $registro[$i][8])
															$registro[$i][0]="";
													}
												}
												$alto=$registro[$i][35];
												$ancho=$registro[$i][36];
												$span=$registro[$i][32];
												$rwidth=(integer)(($width * $span)/$Span);
												echo "<td id=tipoL02 colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
												//echo "<td id=tipoL02".$CHECK." colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
												echo "<input type='text' id='XA".$i."' onFocus='javascript:limpiarCampo(this);' size=50/>";
												echo "<br>";
												$cualif=explode(",",$registro[$i][44]);
												for($w=0;$w<count($cualif);$w++)
														echo "<input type='RADIO' name='AT".$i."' id='XT".$i."' value=".htmlentities(substr($cualif[$w],0,1))." onClick='javascript:agregarComponenteS(\"$i\");'>".htmlentities($cualif[$w]);
												echo "<br>";
												echo "<select name='registro[".$i."][0]' id='XX".$i."' multiple=multiple size=".$registro[$i][35]." class=tipoTAB".$OBL." onDblClick='javascript:quitarComponenteS(\"$i\");' style='width:650px'>";
												echo $registro[$i][0];
												echo "</select>";
												echo '<script language="Javascript">';
												echo "document.getElementById('XX".$i."').style.width=".$registro[$i][36].";";
												echo '</script>';
												echo "<input type='hidden' id='registro[".$i."][36]' value='".$registro[$i][36]."'>";
												echo "</td>";
												$campos += $registro[$i][32];
												if(isset($completo) and $completo == 1)
													echo "<script> soloLectura(document.getElementById('"."R".$i."'))</script>";
										}
									break;
									case "Hora":
										if($registro[$i][32]>$n)
											$registro[$i][32]=$n;
										$faltan=$n - $campos;
										if($registro[$i][32] > $faltan or $faltan <= 0)
										{
											for ($w=0;$w<$faltan;$w++)
												echo "<td id=tipoL02></td>";
											echo "</tr><tr>";
											$campos = 0;
										}
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										$col=($span * 120) / ($n * 2);
										$position="H".$i;
										if(!isset($SF1["H".$i]) and $registro[$i][29] != "2")
										{
											//$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=8 maxlength=8 id='H".$i."' value='".$registro[$i][0]."'  class=tipo3".$OBL.">";

											echo "<select name='Horas[".$i."]' id=HO".$i." class=tipoHora onChange='limpiarHora(".$i.")'>";
											for ($j=0;$j<24;$j++)
											{
												if($j < 10)
													$jh = "0".$j;
												else
													$jh = $j;
												echo "<option value='".$jh."'>".$jh."</option>";
											}
											echo "</select>&nbsp;";


											echo "<select name='Minutos[".$i."]' id=MI".$i." class=tipoHora onChange='mostrarHora(".$i.")'>";
											for ($j=0;$j<4;$j++)
											{
												$jh=(15 * $j);
												if($jh < 15)
													$jh = "0".$jh;
												echo "<option value='".$jh."'>".$jh."</option>";
											}
											echo "</select>";

											echo "</td>";
										}
										else
											echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=8 maxlength=8 id='H".$i."' value='".$registro[$i][0]."'  class=tipo3".$OBL."></td>";
										$campos += $registro[$i][32];	
										if((isset($completo) and $completo == 1) or $registro[$i][29] == "2")
											echo "<script>soloLectura(document.getElementById('"."H".$i."'))</script>";
									break;
								}
								//$querytime_after = array_sum(explode(' ', microtime(true)));
								//$DIFF=$querytime_after - $querytime_before;
								//echo "Tiempo : ".$DIFF." Segundo(s) Tipo : ".$registro[$i][4]." Consecutivo : ".$registro[$i][2]."<br>";
							}
							
							if($num > 0)
							{
								$faltan=$n - $campos;
								for ($w=0;$w<$faltan;$w++)
									echo "<td id=tipoL02></td>";
								if($pasdiv < $totdiv)
								{
									$pasdiv++;
									if($registro[$i-1][51] == "on")
										$colapsar="none";
									else 
										$colapsar="";
									echo "<tr id=div".$totdiv." style='display: ".$colapsar."'><td><table width=".$width." border=0 cellspacing=0>";
								}
								if($totdiv > 0)
									echo "</table></td></tr>";
								$span=$n;
								$position="FIN";
								if(!isset($firma))
									$firma="";
								if(!isset($completo) or $completo == 0)
								{
									if($WTIPO == 3)
									{
										$path4="/matrix/HCE/procesos/HCE_Tipo3.php?empresa=hce&wformulario=".$wformulario."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wtitulo=".$wtitulo;
										echo "<tr><td id=tipoL06 colspan=".$span."><A HREF='#' id='FIRMA' class=tipo3V onclick='javascript:activarModalIframe(\"FIRMA DIGITAL\",\"nombreIframe\",\"".$path4."\",\"0\",\"0\");'>FIRMA DIGITAL</A></td></tr>";
									}
									else
									{
										$query = "SELECT Usudep  from ".$empresa."_000020 ";
										$query .= " where Usucod = '".$key."' ";
										$query .= "   and Usuest = 'on' ";
										$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$row2 = mysql_fetch_array($err2);
										$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										if($row2[0] == "off")
											echo "<tr><td id=tipoL06 colspan=".$span.">Firma Digital : <input type='password' name='firma' size=40 maxlength=80 id='firma' value='".$firma."' class=tipo3 OnBlur=".$id."></td></tr>";
										else
											echo "<tr><td id=tipoL06 colspan=".$span.">Firma Digital : <input type='password' name='firma' size=40 maxlength=80 id='firma' value='".$firma."' class=tipo3 OnBlur=".$id."> Requiere Validaci&oacute;n<input type='checkbox' name='FED' id='FED' class=tipoL02M></td></tr>";
									}
								}
								else
									echo "<tr><td id=tipoL06 colspan=".$span.">Firma Digital : Documento Con Firma Digital</td></tr>";
								$position="ok";
								for ($i=0;$i<$num;$i++)
								{
									if($registro[$i][4] == "Memo" and strtoupper(substr($registro[$i][0],0,7)) == "CERRADO")
									{
										$wtipo2cerrado=1;
									}
								}
								if(((!isset($completo) or $completo == 0) and (!isset($wuserG) or $wuserG == $key)) or (isset($wswfirma) and $wswfirma == 4))
								{
									if(!isset($GOK))
									{
										$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','2')";
										echo "<tr><td id=tipoL02 colspan=".$span."><IMG SRC='/matrix/images/medical/HCE/grabar.png' id='gravar' style='vertical-align:middle;' OnClick=".$id." title='RESPALDAR INFORMACI&Oacute;N'><br>RESPALDAR INFORMACI&Oacute;N</td>";
									}
									if(!isset($wtipo2cerrado))
									{
										if(!isset($configuracionE) or (isset($configuracionE) and strlen($configuracionE) == 0))
										{
											$id="ajaxview('19','".$empresa."','".$origen."','".$wdbmhos."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','1')";
											//echo "<tr><td id=tipoL09 colspan=".$span."><IMG SRC='/matrix/images/medical/HCE/ok.png' id='logook' style='vertical-align:middle;' OnClick=".$id.">&nbsp;&nbsp;<input type='checkbox' name='ok' id='ok' OnClick=".$id."></td>";
											echo "<tr><td id=tipoL09 colspan=".$span."><IMG SRC='/matrix/images/medical/HCE/ok.png' id='logook' style='vertical-align:middle;' OnClick=".$id."></td>";
										}
										else
											echo "<tr><td colspan=".$Span." id=tipoLGOK><IMG SRC='/matrix/images/medical/root/MALOH.png'>NO SE PUEDEN GRABAR REGISTROS SI EXISTEN ERRORES EN LA CONFIGURACION!!!</td></tr>";
									}
									else
									{
										echo "<tr><td colspan=".$Span." id=tipoLGOK><IMG SRC='/matrix/images/medical/root/felizH.png'>".$memomsg."</td></tr>";
										echo "<div id='messageUP' class=divmess style='background:#dddddd;color:#000066;'>".$memomsg."</div>";
									}
								}
								echo "<input type='HIDDEN' name= 'position' value='".$position."' id='position'>";
								if(isset($GOK))
								{
									echo "<tr><td colspan=".$Span." id=tipoLGOK><IMG SRC='/matrix/images/medical/root/felizH.png'> DATOS GRABADOS OK!!!!</td></tr>";
									Delete_Recovery($wformulario,$key,$whis,$wing);
								}
								if(isset($mensajes))
									echo "<tr><td colspan=".$Span." id=tipoL09A>".$mensajes."</td><tr>";
								echo "</div></table><div id='message' class=divmess style='background:#dddddd;color:#000066;'>".$mess."</div></center>";
							}
							else
							{
								echo "<center>";
								// INICIALIZACION DEL TITULO DEL FORMULARIO
								echo '<script language="Javascript">';
								//echo "     debugger;";
								echo '		var obj = parent.parent.demograficos.document.getElementById("txttitulo");';
								echo '		if(obj)';
								echo '		{';
								echo "			var divAux = document.createElement( 'div');";
								echo "			divAux.innerHTML = '".htmlentities($wtitulo)."';";
								echo "			obj.value = divAux.innerHTML.toUpperCase();";
								echo '		} ';
								echo '		var obj1 = parent.parent.demograficos.document.getElementById("txtformulario");';
								echo '		if(obj1)';
								echo '		{';
								echo "			var divAux = document.createElement( 'div');";
								echo "			divAux.innerHTML = '".htmlentities($wformulario)."';";
								echo "			obj1.value = divAux.innerHTML.toUpperCase();";
								echo '		} ';
								echo '</script>';
								echo "<H2><b><font color=#000066 FACE='Arial'>Protocolo NO Definido</font></b></H2>";
								echo "</center>";
							}
						}
						else
						{
							echo "<center><table border=0 align=center>";
							echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
							echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ESTE PACIENTE NO EXISTE. POR FAVOR LLAME A INFORMATICA Y CONSULTE.  GRACIAS</MARQUEE></FONT>";
							echo "<br><br>";
						}
					}
					else
					{
						echo "<center><table border=0 align=center>";
						echo "<tr><td bgcolor=#E8EEF7 align=center><IMG SRC='/matrix/images/medical/hce/Advt.png' style='vertical-align:middle;'><font color=#000066 FACE='Arial' size=5> ADVERTENCIA : </font></td><tr>";
						echo "<tr><td bgcolor=#E8EEF7><font color=#000066 FACE='Arial'>".$msgPre."</font></td><tr></table></center>";
						echo "<br><br>";
					}	
				}
				echo "</form>";
			break;
					
			case "W2": 		
				$formularios=array();
				$query = "select Vaspas, Vasnom from ".$empresa."_000005 where Vaspro='".$wformulario."' and Vasest='on' ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$numfor = $num1;
				if ($num1>0)
				{
					for ($i=0;$i<$numfor;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$formularios[$i][0]=$row1[0];
						$formularios[$i][1]=$row1[1];
					}
				}		
				echo "<link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.theme.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.datepicker.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.tabs.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/jquery.jTPS.css' rel='stylesheet'>"; 							
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.jTPS.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.tabs.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.datepicker.js'></script>";
				
				
				echo "<script type='text/javascript'>";
				echo "$(document).ready(function(){  init_calendar(); ";
				echo "$('#tabs').tabs(); ";
				echo "});";
				echo "</script>";			
				
				echo "</head>";
				
				echo "<BODY TEXT='#000000' FACE='ARIAL' onLoad='pintardivs()'>";
				echo "<div id='1' height=50px>";
				
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE7' action='HCE.php' method=post>";
							
				echo "<div id='tabs' class='tipotabs'>";		
				echo "<ul>";
				for ($i=0;$i<$numfor;$i++)
				{
					$nfor=$i + 1;
					echo "<li class='tipotabs'><a href='#fragment-".$nfor."'><span>".$formularios[$i][1]."</span></a></li>";
				}
				echo "</ul>";
				
				$query = "select Fecha_data, Ubifad from ".$wdbmhos."_000018 ";
				$query .= "  where Ubihis='".$whis."'";
				$query .= "    and Ubiing='".$wing."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wfechai=$row1[0];
					if($row1[1] != "0000-00-00")
						$wfechaf=$row1[1];
					else
						$wfechaf=date("Y-m-d");
				}
				else
				{
					$wfechai=date("Y-m-d");
					$wfechaf=date("Y-m-d");
				}
				$FI=array();
				$FF=array();
				for ($i=0;$i<$numfor;$i++)
				{
					$nfor1=$i + 1;
					$nfor2=$i + 20;
					$FI[$nfor1]=$wfechai;
					$FF[$nfor1]=$wfechaf;
					echo "<div id='fragment-".$nfor1."' style='overflow:scroll;height:315px' >";
					echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
					echo "<center><input type='HIDDEN' name= 'origen' value='".$origen."'>";
					echo "<center><input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
					echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
					echo "<center><input type='HIDDEN' name= 'wformulario' value='".$wformulario."'>";
					echo "<center><input type='HIDDEN' name= 'whis' value='".$whis."'>";
					echo "<center><input type='HIDDEN' name= 'wing' wing='".$wing."'>";
					echo "<center><input type='HIDDEN' name= 'wcedula' wing='".$wcedula."'>";
					echo "<center><input type='HIDDEN' name= 'wtipodoc' wing='".$wtipodoc."'>";
					echo "<table border=0 id=tipoT00>";
					echo "<tr><td align=center colspan=2 id=tipoL08>CRITERIO DE BUSQUEDA</td></tr>";
					$inicial="I".$nfor1;
					$final="F".$nfor1;
					echo "<tr><td align=left id=tipoL02>Fecha Inicial : </td><td align=center id=tipoL02><input id='FI".$nfor1."' type='text' size='12' maxlength='12' NAME='FI[".$nfor1."]' value='".$FI[$nfor1]."'></td></tr>";
					echo "<tr><td align=left id=tipoL02>Fecha Final : </td><td align=center id=tipoL02><input id='FF".$nfor1."' type='text' size='12' maxlength='12' NAME='FF[".$nfor1."]' value='".$FF[$nfor1]."'></td></tr>";				
					$id="ajaxtable('".$nfor2."','".$empresa."','".$origen."','".$wdbmhos."','W3','".$formularios[$i][0]."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$FI[$nfor1]."','".$FF[$nfor1]."','".$nfor1."')";
					echo "<tr><td  id=tipoL08 colspan=2><input type='button' name='ENTER' value='CONSULTAR' class=tipo3A id='SQL".$nfor1."' OnClick=".$id."></td></tr>";
					echo "<tr><td align=center colspan=2>";
					echo "<div id='".$nfor2."'>";
					echo "</div>";
					echo "</td></tr>";
					echo "</table>";
					echo "<br><br></div>";
				}
				echo"</form>";
			break;
			
			case "W3": 
				//                 0      1      2      3      4      5      6      7      8      9      10     11
				$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
				$query .= " where pacced = '".$wcedula."'";
				$query .= "   and pactid = '".$wtipodoc."'";
				$query .= "   and  pacced = oriced ";
				$query .= "   and  pactid = oritid ";
				$query .= "   and oriori = '".$origen."' ";
				$query .= "   and inghis = orihis ";
				$query .= "   and  inging = oriing ";
				$query .= "   and ubihis = inghis "; 
				$query .= "   and ubiing = inging ";
				$query .= "   and ccocod = ubisac ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err);
				
				$wsex="M";
				if($row[5] == "F")
					$wsex="F";
				$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
				$wintitulo="Historia:".$row[6]." Ingreso:".$row[7]." Paciente:".$wpac;
				$Hgraficas=" |";
				$en="";
				$en .= "'".$wformulario."'";
				$CLASE="C";
				$queryI  = " select ".$empresa."_000002.Detdes,".$empresa."_".$wformulario.".movdat,".$empresa."_000002.Detorp,".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$wformulario.".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir from ".$empresa."_".$wformulario.",".$empresa."_000002,".$empresa."_000001 ";
				$queryI .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' "; 
				$queryI .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
				$queryI .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
				$queryI .= "   and ".$empresa."_".$wformulario.".fecha_data between '".$wfechai."' and '".$wfechaf."' "; 
				$queryI .= "   and ".$empresa."_".$wformulario.".movpro=".$empresa."_000002.detpro ";
				$queryI .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.detcon ";
				//$queryI .= "   and ".$empresa."_000002.detest='on' "; 
				$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' "; 
				$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro "; 
				imprimir($conex,$empresa,$wdbmhos,$origen,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,1);				
			
			break;	
			
			case "W4": 					
				$query  = "select ".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario;
				$query .= " where ".$empresa."_".$wformulario.".fecha_data = '".$wfecha."' "; 
				$query .= "   and ".$empresa."_".$wformulario.".Hora_data='".$whora."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movcon='".$wcon."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
				$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wtexto = $row1[0];
				}
				else
					$wtexto = "";
				
				$wtexto=htmlentities($wtexto);
				echo "<table border=0 id=tipoT00 cellpadding=5>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/AumentarTexto.gif' onClick='javascript:increaseFontSize();'><IMG SRC='/matrix/images/medical/root/DisminuirTexto.gif' onClick='javascript:decreaseFontSize();'></td></tr>";
				echo "<tr><td id=tipoL02X><div id='wtex'>".$wtexto."</div></td></tr>";
				echo "</table>";
			break;	
		}
	}
	else
	{
		echo "<center><table border=0 aling=center>";
		echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
		echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>NO SE HA CONFIGURADO ADECUADAMENTE EL SERVIDOR DE LA APLICACION EN LA TABLA 14 DEL GRUPO HCE.  LLAME A SISTEMAS!!!!</MARQUEE></FONT>";
		echo "<br><br>";	
	}
	
	
	//Se cierra conexión de la base de datos :)
	//Se comenta cierre de conexion para la version estable de matrix :)
	//mysql_close($conex);
	
	//Cierre de la estructura head y html inicial
	echo "</div>";
	echo "</body>";
	echo "</html>";
}
?>
