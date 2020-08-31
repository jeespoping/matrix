<html>
<head>
  <title>MATRIX DOCUMENTACION</title>
</head>
<body BGCOLOR="#EAEAEA">
<BODY TEXT="#000066">
<center>
	<H1>GRUPO EMPRESARIAL LAS AMERICAS</H1><br>
	<H1>Direccion de Tecnologia de la Informacion</H1><br>
	<IMG SRC='/matrix/images/medical/root/Logo_Matrix.png'><br><br>
</center>
<A NAME="Arriba"><H1>DOCUMENTACION</H1>
<A><H2>TABLA DE CONENIDO</H2>
<ol>
	<li><A HREF="#m1">Ingreso a la Aplicacion</a>
	<li><A HREF="#m2">Estructura de la Aplicacion</a>
		<ol>
			<li><A HREF="#m3">Frame de Titulos</a>
			<li><A HREF="#m4">Frame de Opciones</a>
			<ol>
				<li><A HREF="#m5">Menu de Maestros</a>
				<li><A HREF="#m6">Registro</a>
				<li><A HREF="#m7">Consultas</a>
				<li><A HREF="#m8">Procesos</a>
				<li><A HREF="#m9">Reportes</a>
				<li><A HREF="#m10">Carga de Archivos Planos</a>
				<li><A HREF="#m11">Publicacion de Archivos</a>
				<li><A HREF="#m12">Salir del Programa</a>
			</ol>
			<li><A HREF="#m13">Frame Principal</a>
		</ol>
	<li><A HREF="#m14">Descripcion Funcional de los Modulos</a>
	<ol>
		<li><A HREF="#m15">Modulo de Formularios</a>
		<li><A HREF="#m16">Modulo de Detalle de  Formularios</a>
		<li><A HREF="#m17">Modulo de Seleciones</a>
		<li><A HREF="#m18">Modulo de Detalle de Seleciones</a>
		<li><A HREF="#m19">Modulo de Control de Numeracion</a>
		<li><A HREF="#m20">Modulo de Control de Acceso</a>
		<li><A HREF="#m21">Modulo de Definicion de Procesos</a>
		<li><A HREF="#m22">Modulo de Definicion de Reportes</a>
		<li><A HREF="#m23">Modulo de Usuarios</a>
		<li><A HREF="#m24">Modulo de Registro de Informacion</a>
		<li><A HREF="#m25">Modulo de Consulta</a>
		<li><A HREF="#m26">Modulo de Ejecucion de Procesos</a>
		<li><A HREF="#m27">Modulo de Ejecucion de Reportes</a>
		<li><A HREF="#m28">Modulo de Carga de Archivos Planos</a>
		<li><A HREF="#m29">Modulo de Publicacion de Archivos</a>
		<li><A HREF="#m30">Opcion Manual de Operacion</a>
		<li><A HREF="#m31">Opcion Salir del Programa</a>
	</ol>
</ol>
<b><A NAME="m1"><A HREF='#Arriba'>1. INGRESO A LA APLICACION.</a></b>
<p>
		Para ingresar a la aplicacion es necesario digitar un codigo de usuario o palabra clave "Password" que deben ser asignadospor el administador del sistema<br>
		 y que permiten el ingreso a la aplicacion.<br>
</p>
<b><A NAME="m2"><A HREF='#Arriba'>2. ESTRUCTURA DE LA INFORMACION.</a></b>
<p>
		La pantalla principal de la aplicacion esta dividida en tres frames o marcos en dos de los cuales el usuario interactua con la alplicacion.<br>
</p>
<b><A NAME="m3"><A HREF='#Arriba'>2.1 FRAME DE TITULOS.</a></b>
<p>
		Sirve para mostrar el titulo de aplicacion no se realiza ninguna operacion en el.<br>
</p>
<b><A NAME="m4"><A HREF='#Arriba'>2.2 FRAME DE OPERACIONES.</a></b>
<p>
		En este frame se muestra el número de visitantes a la página, el usuario que actualmente esta conectado y un conjunto de  opciones con hipervínculos<br>
		a los diferentes módulos. Dentro del frame de opciones apareceun menú de opciones que esta subdividido en 8 opciones con las siguientes alternativas:<br> 
</p>
<b><A NAME="m5"><A HREF='#Arriba'>2.2.1 MENU DE MAESTROS.</a></b>
<p>
		Conjunto de módulos que permiten estructurar el conjunto de formularios, su detalle, las selecciones asociadas, el detalle de las selecciones, el control <br> 
		automático de numeración, los controles de acceso, los procesos y los reportes necesarios para formar una aplicación.<br> 
</p>
<b><A NAME="m6"><A HREF='#Arriba'>2.2.2 REGISTRO.</a></b>
<p>
		Es el modulo que permite el ingreso y consulta de la información almacenada en los diferentes formularios.
</p>
<b><A NAME="m7"><A HREF='#Arriba'>2.2.3 CONSULTA.</a></b>
<p>
		Es un modulo diseñado para estructurar y almacenar "querys" interrelacionando todos los formularios que componen la aplicación a través de  sentencias <br> 
		escritas en SQL (Estándar Query Lenguaje), los resultados de estos querys pueden almacenarse en tablas temporales que permiten realizar querys sobre <br>
		querys o generar archivos planos que puedan ser usados en otros tipos de aplicaciones.<br> 
</p>
<b><A NAME="m8"><A HREF='#Arriba'>2.2.4 PROCESOS.</a></b>
<p>
		Este programa despliega una lista de todos los procesos de cualquier tipo que sean necesarios ejecutar en cualquier momento de la aplicación.<br>
</p>
<b><A NAME="m9"><A HREF='#Arriba'>2.2.5 REPORTES.</a></b>
<p>
		Este modulo despliega todos los reportes necesarios para ser ejecutado por una aplicación.<br>
</p>
<b><A NAME="m10"><A HREF='#Arriba'>2.2.6 CARGA DE ARCHIVOS PLANOS.</a></b>
<p>
		Este modulo permite cargar dentro de los formularios de la aplicación, información almacenada en texto plano donde los campos de cada registro están<br> 
		separados por comas y el numero de campos sea igual al detalle definido del formulario que se va a cargar.<br> 
</p>
<b><A NAME="m11"><A HREF='#Arriba'>2.2.7 PUBLICACION DE ARCHIVOS.</a></b>
<p>
		Este modulo le permite al usuario publicar en la aplicación archivos tipo texto o imágenes en formatos gif-tif o jpg cuyo  tamaño en bits no  supere los<br> 
		100 K, esto con el propósito de interrelacionar la información contenida en los formularios con graficas obtenidas en otros programas y ser  publicados <br> 
		en la Web; si el usuario que accesa este programa es el "súper usuario " tiene la posibilidad además de publicar programas o programas incluidos.<br>
</p>
<b><A NAME="m12"><A HREF='#Arriba'>2.2.8 SALIR DEL PROGRAMA.</a></b>
<p>
		Esta opción como su nombre lo indica, cierra la sesión y termina la ejecución de la aplicación.<br>
</p>
</p>
<b><A NAME="m13"><A HREF='#Arriba'>2.3 FRAME PRINCIPAL.</a></b>
<p>
		En esta forma se muestra el resultado de la ejecución de los módulos seleccionados en el frame de opciones. <br>
</p>
<b><A NAME="m14"><A HREF='#Arriba'>3. DESCRIPCION FUNCIONAL DE LOS MODULOS.</a></b><br><br>
<b><A NAME="m15"><A HREF='#Arriba'>3.1 MODULO DE FORMULARIOS.</a></b>
<p>
		El modulo de formularios permite generar una lista con los códigos y nombres de todos los ficheros necesarios para estructurar una aplicación.<br>
		La tabla de formularios esta compuesta por 4 campos que se describen a continuación:<br> 
		<b>El código: </b>esta compuesto de 6 dígitos numerados automáticamente por el computador entre el 1 y 999999, siendo este ultimo  el máximo<br>
		 de ficheros por aplicación<br>
		<b>El nombre:</b> lo digita el usuario de acuerdo a la funcionalidad del fichero o formulario.<br>
		<b>El tipo:</b> tiene 2 alternativas mostradas por un drop-down que puede ser abierto o cerrado y que significa que el formulario es público o privado,en <br> 
		 otras palabras: el formulario solo va a ser usado por una aplicación o compartido por otras.<br>
		<b>Activo: </b>tiene 2 alternativas mostradas por un "drop-down" que indican si el formulario esta en uso o no.<br>
</p>
<b><A NAME="m16"><A HREF='#Arriba'>3.2 MODULO DE DETALLE DE FORMULARIOS.</a></b>
<p>
		Este modulo permite describir los campos o "Tuplas" que componen un formulario o fichero además de crear físicamente dentro de la base de datos una<br>
		tabla con la estructura definida en este programa.<br>
		Para crear un campo de un formulario es necesario definir  10 variables que describen sus características. Dichas variables son: <br>
		<b>Formulario: </b>Representa el formulario al que pertenece este campo y es colocado automáticamente por el computador<br>
		<b>Campo: </b>Es un consecutivo de 4 dígitos que va desde 1 a 9999 y que es asignado por el computador,esto implica que el 9999 es el número máximo de <br>
		  campos que puede contener un formulario.<br>
		<b>Descripción:</b> Esta variable almacena el nombre del campo el cual no debe pasar de 50 caracteres. Es importante anotar que si el usuario deja espacios en<br>
		  blanco en esta descripción, el computadorlos reemplazara por el carácter "underline" (_)<br>
		<b>Tipo:</b> Esta variable se refiere a los diferentes tipos asociados a un campo, los cuales pueden ser:<br>
		<b>Caracteres: </b>Esta implica que la variable va a contener un máximo de 80 caracteres alfa numéricos.<br>
		<b>Entero: </b>Indica que el campo va a contener un valor numérico entero.<br>
		<b>Real: </b>Indica que el campo va a contener un valor numérico de punto flotante.<br>
		<b>Fecha: </b>Indica que el campo contiene el valor de una fecha con el formato año de 4 dígitos guión mes de 2 dígitos guión día de 2 dígitos (AAAA-MM-DD).<br>
		<b>Texto: </b>Indica que el campo va a contener un conjunto de caracteres alfa - numéricos no mayor a 7 Millones. Un campo tipo texto puede tener una <br>
		 selección asociada, esto indica que cuando se este registrando la información se puede adicionar al campo de texto,todas las posibles opciones de la  <br>
		 seleccion asociada,  de la misma manera, un campo de texto puede estar asociado a un campo de relación, esto indica que en el registro de información se <br>
		 puede adicionar todos los posibles registros, de un formulario que este asociado a traves de un campo de relacion asociado al campo de texto.<br>
		<b>Selección: </b>Indica que el campo  va a contener el valor de una selección asociada a este,la cual puede escoger en una variable que se encuentra mas<br>
		  adelante llamada selección asociada, esta selección y su detalle se crean en módulos que se definirán mas adelante. La selección asociada quedara<br>
		 especificada en la variable comentarios.<br>
		<b>Formula:</b> Indica que el campo va a contener el resultado de la evaluación de una expresión matemática escrita en modo post-fijo, lo que implica que en<br>
		su escritura es innecesario el uso de paréntesis para determinar la presencia de las operaciones. <br>
		La formula puede contener constantes numéricas, nombres de funciones tales como: seno, coseno, tangente, arco tangente, logaritmo en base 10,<br>
		logaritmo natural, exponencial, el valor absoluto, raíz cuadrada, exponensiacion y la constante Pi. <br>
		De la misma manera puede contener los numeros de otros campos del mismo formulario unidos por conectores aritméticos tales como +,-,* y /, que indican<br>
		suma, resta, multiplicación y división respectivamente, como ejemplo la formula matemática:(a+b)/c se escribe en forma post-fija: ab+c/ que es exactamente<br>
		lo misma expresion.<br>
		<b>Grafica:</b> Indica que el campo contendrá el nombre de un archivo con formato gif, tif o jpg; es importante anotar que el nombre del archivo se escribe con<br>
		 su extensión, por ejemplo: matriz.jpg, y este archivo debe haber sido publicado mediante la opción 7 de publicación de archivos, cuyo funcionamiento se<br>
		 explicara mas adelante.<br>
		<b>Automático:</b> Indica que el campo almacenara un valor numérico entero consecutivo asignado automáticamente por el computador en el programa  de registro<br>
		 de información el cual se inicializara en la opción 6 de control de numeración que se explicara mas adelante.<br>
		<b>Relación:</b> Indica que el campo almacenara un valor proveniente de otro formulario cuyos campos se podrán seleccionar en la variable formulario  en donde<br>
		 aparecen todos los formularios y todas las variables que componen la aplicación. La relación se establece con todas las posibles variables de un y solo un<br>
		  formulario; la relación queda establecida en la variable comentario de la siguiente manera: numero de variables asociadas formulario asociado variable número  <br>
		1 variable número 2 ....variable número n.<br>
		<b>Booleano:</b> Indica que el campo va a contener el valor de una variable binaria cuyos únicos posibles valores son: on o off.<br>
		<b>Hora: </b>Indica que el campo va a contener el valor de una hora con el siguiente formato: hora de 2 dígitos dos puntos minutos de 2 dígitos dos puntos <br>
		  segundos de 2 dígitos (HH:MM:SS)<br>
		<b>Algorítmico:</b> Indica que el campo va a contener el resultado de la ejecución de un algoritmo que esta almacenado en un programa el cual será incluido<br>
		en tiempo de ejecución en el modulo de registro de información; el programa se divide en 2 partes: una para el cálculo, y la otra para la validación de la<br>
		información obtenida. Los nombres de estos programas incluidos se especifican en la variable comentario con la letra c mas 11 caracteres alfa numéricos y la <br>
		extensión .php para el modelo de validación y la letra v mas 11 caracteres y la extensión .php para el modulo de calculo.<br>
		El resultado de la ejecución del algoritmo no podrá ser modificado por el usuario cuando registre la información.<br>
		<b>Titulo:</b> Indica que el campo contendrá el nombre de un titulo que servirá para separar las diferentes secciones que podría contener un formulario. <br>
		<b>Hipervínculo:</b> Indica que el campo contendrá una url que podrá ser abierta cuando se este ejecutando el programa de registro de información.<br>
		<b>Algorítmico-M:</b> Tiene exactamente el mismo funcionamiento del tipo algorítmico, solo que el resultado del cálculo o del algoritmo pude ser modificado<br>
		 por el usuario cuando se este registrando la información.<br>
		<b>Posición: </b>Indica la posición que va a ocupar el campo cuando se despliegue el formulario en el registro de información.<br>
		<b>Selección Asociada:</b> Contiene un drop-down con todas las selecciones que han sido creadas para la aplicación en los programas de selecciones  y detalles<br>
		 de selecciones que se explicaran mas adelante. A los campos tipo selección y tipo texto se asocian a las selecciones especificadas en esta variable.<br>
		<b>Formulario asociado:</b> contiene todos los formularios con todos los campos que han sido creados para la aplicación.  Esta variable se usa en los campos<br>
		 tipo relación, formula y texto.<br>
		<b>Comentarios:</b> Esta variable puede contener dependiendo del tipo del campo una relación con otro formulario, una selección asociada o una formula<br>
		  matemática en modulo post-fijo.<br>
		<b>Activo: </b>Drop-down con 3 posibles opciones: activo, inactivo y protegido. La selección del inactivo es equivalente a borrar el campo dentro del formulario.<br>
		 La selección del protegido indica que después de almacenada la información en el formulario de registro, esta no podrá ser modificada. Cuando la posición de<br> 
		 un campo es modificada o se borra un campo el modulo no solo reorganiza la secuencia de la posición sino que adicionalmente recodifica los códigos de los   <br>
		 campos en 4 caracteresde acuerdo a la secuencia de la posición.<br>
		<b>Datos completos: </b>Check box que indica que la información contenida dentro del formulario de detalle del campo esta completa y puede ser grabada.<br>
		<b>Grabar:</b> Botón que permite darle submit a la forma para ser procesada.<br>
		<b>Retornar:</b> Hipervínculo que permite retornar al despliegue de campos por formulario.<br>
		<b>Nuevo:</b> Hipervínculo que permite crear un campo dentro del formulario especificado en el drop-down.<br>
		<b>Crear tabla:</b> Hipervínculo que permite crear el formulario con una tabla física dentro de la base de datos. Los 2 hipervínculos anteriores se despliegan<br>
		  si el usuario no ha hecho uso de la opción: crear tabla.<br>
		<b>Ir:</b> Permite desplegar los campos del formulario seleccionado en el drop-down.<br>
		<b>Editar:</b> Hipervínculo que permite desplegar todas las características asociadas a un campo de un formulario.<br>
</p>

<b><A NAME ="m17"><A HREF='#Arriba'>3.3 MODULO DE SELECCIONES</a></b>
<p>
		Este modulo permite la creación de selecciones necesarias para el funcionamiento de la aplicación. Las opciones asociadas
 		a una selección son desplegadas en un drop-down. Al momento del registro de la información, se recomienda que una selección
  		posea a lo sumo no mas de 30 opciones ya que de lo contrario es mas eficiente crear un formulario. Sin embargo, el número de
  		 opciones asociadas a una selección no tiene límite.<br>
		<b>Código: </b>variable alfa-numérica de 10 caracteres que sirve para asignar un código único a la selección.<br>
		<b>Descripción:</b> campo alfa-numérico de 50 caracteres que permite asignarle el nombre a una selección.<br>
		<b>Activo:</b> drop-down con las opciones de activo e inactivo, el inactivo implica que la selección no podrá ser usada
 		en al creación de campos de selección.<br>
		<b>Grabar:</b> botón para darle submit al formulario.<br>
		<b>Nuevo:</b> hipervínculo que permite crear una nueva selección.<br>
		<b>Editar: </b>hipervínculo que permite editar la información de una selección.
</p>

<b><A NAME ="m18"><A HREF='#Arriba'>3.4 MODULO DE DETALLE DE SELECCIONES</a></b>
<p>
		Este modulo permite la creación de los componentes asociados a una selección , es decir, las opciones que serán mostradas en un drop-down 
		en el momento del registro de la información; como ya dijimos anteriormente, por efectos de eficiencia no es recomendable asociar mas de 30
 		opciones a una selección ya que la búsqueda de una opción seria muy dispendiosa.<br>
		<b>Código:</b> variable alfa-numérica que contiene la información del grupo al que estara asociado esta opcion. El grupo se escoge en un drop-drown
 		que aparece cuando se selecciona el modulo de detalles de selecciones en el frame de opciones. Este codigo lo asigna automáticamente el computador<br>
		<b>Subcodigo:</b> variable alfa-numerica de 20 caracteres que contiene el codigo de la opcion asociada a la selección. Es asiganada por el usuario.<br>
		<b>Descripción:</b> variable alfa-numerica de 50 caracteres que contiene el nombre de la opcion. Es asignada por el usuario.<br>
		<b>Activo: </b>drop-down con las opciones de activo e inactivo, el inactivo implica que la opcion no podrá ser usada en el registro de campos de selección.<br>
		<b>Grabar:</b> botón para darle submit al formulario.<br>
		<b>Nuevo:</b> hipervínculo que permite crear una nueva opcion.<br>
		<b>Editar:</b> hipervínculo que permite editar la información de una opcion.<br>
</p>

<b><A NAME ="m19"><A HREF='#Arriba'>3.5 MODULO DE CONTROL DE NUMERACION</a></b>
<p>
		Este modulo permite asignar la secuencia de inicio de un campo automatico asociado a un formulario. Esta información es utilizada por el modulo de
		 registro de información para el incremento automatico de esta secuencia.<br>
		<b>Formulario:</b> variable alfa-numerica que contiene la información de formularios que poseen campos de tipo automatico. Esta información es
		 mostrada en un drop-down.<br>
		<b>Campo:</b> variable alfa-numerica que contiene la información de los campos automaticos. Esta información es mostrada en un drop-down.<br>
		<b>Secuencia:</b> variable numerica entera que indica el numero de secuencia en que se encuentra el campo automatico.<br>
		<b>Grabar:</b> botón para darle submit al formulario.<br>
		<b>Nuevo:</b> hipervínculo que permite crear una nueva secuencia para un campo automatico.<br>
		<b>Editar:</b> hipervínculo que permite editar la información de una secuencia de un campo automatico.<br>
</p>
<b><A NAME ="m20"><A HREF='#Arriba'>3.6 MODULO DE CONTROL DE ACCESO</a></b>
<p>
		Este modulo sirve para compartir los formularios asociados a una aplicación con usuarios con otros usuarios que pertenecen 
		al mismo grupo del usuario dueño del formulario.<br>
		<b>Usuario:</b> drop-down con la información de los usuarios que pertenecen al mismo grupo del usuario dueno del formulario.<br>
		<b>Formulario:</b> codigo del formulario que se va a compartir.<br>
		<b>Grabacion:</b> variable booleana que indica que el formulario va a ser compartido con el usuario seleccionado para grabacion de la información, si su valor<br>
		  es "on".<br>
		<b>Modificacion:</b> variable booleana que indica que la información grabada en un formulario por un usuario con el que se comparte,puede ser modificada por<br>
 		  este, si su valor es "on".<br>
		<b>Lectura:</b> variable booleana que indica que la información grabada en un formulario por un usuario con el que se comparte, puede ser consultada por este,<br>
 		  si su valor es "on".<br>
		<b>Reportes:</b> variable booleana que indica que los reportes asociados a un formulario que se comparte con otro usuario, pueden ser ejecutados con este,<br>
		 si su valor es "on".<br>
		<b>Nivel:</b> variable numerica entera que indica el valor de prioridad de los reportes asociados a un formulario que se comparte con otro usuario,y que pueden<br>
 		 ser ejecutados por este, si su nivel de prioridad es mayor o igual que el nivel especificado en esta variable.<br>
		<b>Grabar:</b> botón para darle submit al formulario.<br>
		<b>Nuevo:</b> hipervínculo que permite crear un nuevo control de acceso<br>
		<b>Editar:</b> hipervínculo que permite editar la información de un control de acceso<br>
</p>

<b><A NAME ="m21"><A HREF='#Arriba'>3.7 MODULO DE DEFINICION DE PROCESOS</a></b>
<p>
		Este modulo permite especificar las descripciones y los nombres de los archivos fisicos que hacen
		parte de la aplicación y que sirven para realizar tareas especificas distintas al registro normal de la 
		información y que son necesarias dentro del funcionamiento lógico de la aplicación.<br>
		<b>Formulario:</b> Variable alfa-numerica que indica el formulario al que esta asociado el proceso.<br>
		<b>Codigo:</b> variable alfa-numerica de 4 caracteres que permite asociar un codigo al proceso.<br>
		<b>Descripción:</b> variable alfa-numerica de 60 caracteres que permite asignarle un nombre a un proceso.<br>
		<b>Nombre:</b> variable alfa-numerica de 80 caracteres que contiene el nombre del archivo fisico del  modulo que ejecuta el proceso.<br>
		<b>Nivel: </b>variable numerica entera que indica el nivel de prioridad minimo que un usuario debe tener para ejecutar este proceso cuando no es propietario<br>
		del formulario.<br>
		<b>Grabar:</b> botón para darle submit al formulario.<br>
		<b>Nuevo:</b> hipervínculo que permite crear un nuevo proceso.<br>
		<b>Editar: </b>hipervínculo que permite editar la información de un proceso.<br>
</p>
<b><A NAME ="m22"><A HREF='#Arriba'>3.8 MODULO DE DEFINICION DE REPORTES</a></b>
<p>
		Este modulo permite especificar las descripciones y los nombres de los archivos físicos que hacen parte de la aplicación y que sirven para ejecutar reportes 
		que son necesarios dentro del funcionamiento lógico de la aplicación.<br>
		<b>Formulario:</b> Variable alfa-numérica que indica el formulario al que esta asociado el reporte.<br>
		<b>Código: </b>variable alfa-numérica de 4 caracteres que permite asociar un código al reporte.<br>
		<b>Descripción:</b> variable alfa-numérica de 60 caracteres que permite asignarle un nombre a un reporte.<br>
		<b>Nombre: </b>variable alfa-numérica de 80 caracteres que contiene el nombre del archivo físico del  modulo que ejecuta el reporte.<br>
		<b>Nivel:</b> variable numérica entera que indica el nivel de prioridad mínimo que un usuario debe tener para ejecutar este reporte cuando no es propietario <br>
		del formulario.<br>
		<b>Grabar:</b> botón para darle submit al formulario.<br>
		<b>Nuevo: </b>hipervínculo que permite crear un nuevo reporte<br>
		<b>Editar: </b>hipervínculo que permite editar la información de un reporte<br>
</p>
<b><A NAME ="m23"><A HREF='#Arriba'>3.9 MODULO DE USUARIOS</a></b>
<p>
		Este modulo permite la creación de usuarios que tendrán acceso a las aplicaciones creadas dentro del sistema MATRIX. A este modulo solamente tiene acceso <br>
		el superusuario (root). Para crear un usuario es necesario haber creado por el modulo de selecciones y de detalle de selecciones el grupo al que pertenece.<br>
		<b>Código:</b> variable alfa-numérica de 8 caracteres que identifica el usuario.<br>
		<b>Password:</b> variable alfa numérica de 8 caracteres que almacena la clave de acceso.<br>
		<b>Prioridad: </b>variable numérica entera que indica el nivel de prioridad  que el usuario tendrá al ejecutar los módulos de la aplicación.<br>
		<b>Grupo: </b>drop-down que contiene todos los posibles grupos a los que puede pertenecer un usuario<br>
		<b>Grabar:</b> botón para darle submit al formulario.<br>
		<b>Nuevo:</b> hipervínculo que permite crear un nuevo usuario<br>
		<b>Editar:</b> hipervínculo que permite editar la información de un usuario<br>
</p>
<b><A NAME ="m24"><A HREF='#Arriba'>3.10 MODULO DE REGISTRO DE INFORMACION</a></b>
<p>
		Este modulo permite al usuario almacenar la información dentro de las tablas que componen la base de datos, después de haber realizado la definición y <br>
		creación de estas en los módulos de formularios y detalle de formularios que ya se explicaron.<br>
		El usuario podrá tener acceso tanto a los formularios que ha creado para una aplicación especifica de acuerdo a su nivel de prioridad como a otros <br>
		formularios compartidos por usuarios de otras aplicaciones. <br>
		El modulo ofrece la posibilidad de acceder a la información especifica utilizando un filtro que puede especificar en un campo de condición asociado a cada <br>
		formulario.<br>
		Es importante resaltar que las condiciones de búsqueda especificadas solo podrán contener disyunciones (y lógico) y se podrán organizar ascendente o<br> 
		descendentemente por múltiples campos.<br>
		Las variables que se presentan en el formulario corresponden a las definidas en el modulo de detalle de formularios presentando distintas características <br>
		para su ingreso dependiendo del tipo que se hayan definido. Así, estas formas de ingreso son:<br>
		
		<b>Tipo Carácter:</b> presenta un campo de texto donde el usuario puede digitar de uno a 80 caracteres alfa-numéricos, en donde se podrán digitar los caracteres <br>
		especiales (' ', ñ,Ñ,@,/,#,-, )<br>
		<b>Tipo Entero:</b> presenta un campo de texto donde el usuario puede digitar de 1 a 10 dígitos.<br>
		<b>Tipo Real:</b> presenta un campo de texto donde el usuario puede digitar de 1 a 10 dígitos incluido el punto para los decimales.<br>
		<b>Tipo fecha:</b> para el tipo fecha, el usuario cuenta con 3 drop-down para el año-mes-DIA que le permitirán escoger una fecha sin necesidad de usar los separadores. <br>
		Este tipo de campo queda almacenado con el formato ( aaaa-mm-dd ).<br>
		<b>Tipo texto: </b>presenta un área de texto de 5 filas y 60 columnas con scroll vertical en donde el usuario puede digitar hasta 7 millones de caracteres.<br>
		Este campo puede tener asociado una selección cuyas opciones podrán pegarse a medida que sean escogidas, utilizando el carácter & para concatenarlas. <br>
		Igualmente y utilizando el mismo procedimiento este campo también puede tener un campo de relación asociado del que se podrán escoger y concatenar las <br>
		opciones que el usuario desee.<br>
		<b>Tipo selección:</b> presenta un drop-down con todas las opciones que previamente fueron almacenadas en los módulos de selección y detalle de selección.<br>
		<b>Tipo Formula: </b>este campo presenta el resultado de la evaluación de una formula matemática que fue especificada en el modulo de detalles de formularios. <br>
		El resultado de la formula puede ser ajustado por el usuario.<br>
		<b>Tipo automático: </b>este campo presenta el valor del auto-incremento dado por el computador de acuerdo a las secuencia especificada en el modulo de <br>
		control de secuencias. El resultado no puede ser alterado por el usuario.<br>
		<b>Tipo Relación: </b>este modulo presenta un campo de texto y  2 drop-down que tienen la siguiente funcionalidad de acuerdo a su posición de izquierda a derecha: <br>
		Drop-down de resultados: muestra todas las opciones posibles después de haber realizado la búsqueda en el formulario asociado de acuerdo a la variable <br>
		especificada y después de procesar la formula.<br>
		Drop-down de variables asociadas: muestra los nombres de las variables del formulario asociado por el que se desea realizar la búsqueda.<br>
		Campo de texto (criterio de búsqueda): permite escribir hasta 80 caracteres alfa-numéricos que le permitirán al computador en el formulario asociado de <br>
		acuerdo a la variable que se selecciono en el Drop-down de variables asociadas.<br>
		<b>Tipo Booleano:</b> presenta un check-box con 2 posibles valores: on (seleccionado) off ( no seleccionado ). Off es el valor por defecto.<br>
		<b>Tipo Hora: </b>para el tipo hora, el usuario cuenta con 3 drop-down para seleccionar las horas-minutos-segundos que le permitirán escoger una hora específica <br>
		sin necesidad de usar los separadores. Este tipo de campo queda almacenado con el formato (hh:mm:ss ).<br>
		<b>Tipo Algorítmico:</b> este campo presenta el resultado de la ejecución de un algoritmo contenido en los programas que se especificaron en el modulo de <br>
		detalle de formulario. El resultado no podrá ser alterado por el usuario.<br>
		<b>Tipo Titulo: </b>este campo presenta un separador en negrilla que sirve para diferenciar los diferentes grupos de campos que componen un formulario.<br>
		<b>Tipo Hipervínculo: </b>presenta un área de texto donde el usuario puede digitar una url interna o externa a la página cuyo hipervínculo aparecerá al lado <br> 
		derecho después de procesar la forma.<br>
		<b>Tipo Algorítmico-M: </b>tiene la misma funcionalidad del tipo algorítmico, con la diferencia que el resultado si es modificable por el usuario.<br>
		<b>Grabar: </b>botón para darle submit al formulario. Antes de ser almacenada la información en la base de datos es validada por el modulo de registro mostrando <br>
		dado el caso, los errores que se cometieron en cada variable especifica que compone el formulario.<br>
		<b>Nuevo: </b>hipervínculo que permite crear un nuevo registro<br>
		<b>Editar: </b>hipervínculo que permite editar la información de un registro.<br>
		<b>Back: </b>variable booleana que indica si se adelanta  o retrocede en los registros que están almacenados en este formulario. La información por efectos de <br>
		eficiencia se muestra cada 30 registros. La posición en que se encuentra el usuario es desplegada por el modulo.<br>
		<b>Ir:</b> botón que permite adelantar, retroceder o consultar por un criterio la información almacenada en el formulario especificado en el drop-down de formularios.<br>
</p>
<b><A NAME ="m25"><A HREF='#Arriba'>3.11 MODULO DE CONSULTA</a></b>
<p>
		El objetivo de este modulo es proveer una interfase para que el usuario pueda acceder a toda la información que esta contenida en los formularios o tablas  <br>
		que componenla aplicación. Es importante resaltar que no solamente puede consultar la información de un formulario a la vez, sino que también puede<br> 
		 interrelacionar los campos de distintas tablas. <br>
		El programa dispone de 7 secciones que se pueden seleccionar a través de un botón de radio y que permiten con la combinación adecuada estructurar una <br> 
		instrucción SQL con la que se puede acceder a la información deseada. Estas secciones son: <br>
		<b>Tablas:</b> drop-down que contiene los códigos y los nombres de todas las tablas que componen la aplicación mas las tablas publicas y las tablas temporales. <br> 
		Dispone de check-box que permite borrar si se desea las tablas temporales que se han creado por medio de otras consultas. Cuando una tabla  <br>
		es seleccionada y el botón de radio es encendido el nombre de dicha tabla se muestra en el área de texto tablas, igualmente los campos que componen <br>
		la tabla son desplegados en un drop-down en la sección campos.<br>
		<b>Campos:</b> drop-down que contiene los nombres de los campos de la ultima tabla seleccionada en la sección tablas. Cuando el botón de radio esta <br>
		prendido, el nombre de campo seleccionado puede pasar al área de texto campos o al área de texto condiciones dependiendo del control de radio que se <br>
		haya seleccionado en la sección campos. Los campos de las tablas se concatenan automáticamente separados por comas  en el área de texto campos. <br>
		Los campos del área de texto condiciones se concatenan para que el usuariocoloque las condiciones de igualdad o desigualdad que deseen además<br>
		de los conectores lógicos "y " y "o". Conformar query: cuando el usuario prende el control de radio de esta sección el computador recoge la información <br>
		contenida en las áreas de texto tablas, campos y condiciones y construye una instrucción SQL la cual es desplegada en el área de texto consulta.<br>
		<b>Operaciones: </b>esta sección contiene 4 operaciones que son concatenadas cuando el usuario prende el control de radio de esta sección y <br>
		de las cuales 2 ( sumar y contar ) son usadas en el área de texto campos, y las otras 2 ( agrupar y ordenar ) son usadas en el área de texto condiciones.<br>
		<b>Sumar:</b> se utiliza para acumular valores almacenados en campos.<br>
		<b>Contar: </b>se utiliza para contar los registros que cumplen una condición.	<br>
		<b>Agrupar: </b>se utiliza para agrupar la información por un conjunto de campos o cuando se ha hecho uso de las opciones sumar y contar.<br>
		<b>Ordenar:</b> como su nombre lo indica, sirve para ordenar un conjunto de registros de acuerdo con un criterio determinado.<br>
		<b>Consultas: </b>Esta sección contiene un drop-down con los nombres de todas las consultas que previamente a sido grabadas en la sección grabar consulta. <br>
		Cuando el usuario prende el control de radio de esta sección la instrucción SQL asociada al nombre de la consulta, es desplegada en el área de texto consulta.<br>
		<b>Grabar consulta: </b>esta sección dispone de 2 áreas de texto donde el usuario puede grabar un código y un nombre de una consulta completa cuya <br>
		 instrucción SQL se encuentra desplegada en el área de texto consulta. Una vez que el usuario ha almacenado una consulta esta aparecerá automáticamente<br>
		 en la sección de consultas explicada anteriormente. Para que la consulta sea grabada, el usuario, debe prender el control de radio de esta sección.<br>
		<b>Consulta Completa: </b>cuando el usuario considera que su consulta esta completamente estructurada, puede encender el control de radio asociado <br>
		a esta sección para que el computador procese la instrucción SQL y despliegue los registros que cumple las condiciones. Si el usuario adicionalmente  <br>
		desea que estos registros queden almacenados en la base de datos para futuras consultas, debe prender el check-box ( crear tabla temporal ) lo mismo <br>
		que asignar una descripción de 20 caracteres a la tabla temporal que esta creando.<br>
		<b>Ir:</b> botón para darle submit al formulario.<br>
		Después de oprimir el botón ir, el servidor procesa la consulta y si esta correctamente escrita la despliega en una tabla con los campos que se especificaron  <br>
		y aparece el área de texto consulta con la sentencia SQL que se proceso.<br>
		Existe la posibilidad de que los registros que componen la consulta, puedan ser bajados a un archivo plano oprimiendo el hipervínculo que aparece<br>
		 inmediatamente arriba de la tabla y que dice <b>"generación de archivo plano "</b>
		Al oprimir este hipervínculo, aparece una pagina con la sentencia SQL y un hipervínculo que dice <b>"haga clic para bajar el archivo plano "</b>. <br>
		Si se selecciona esta opción, aparece una nueva página que corresponde al directorio de archivos planos en donde se encuentra un archivo <br>
		con las siguientes características:Nombre del usuario (usuario digitado en el login ) "_ " la palabra "plano" y la extensión  ".txt " , por ejemplo: <br>
		<b>usuario_ plano.txt</b>.<br>
		Este archivo se puede abrir inmediatamente dándole "doble click "o oprimiendo el botón derecho, es posible guardar una copia del mismo en el lugar<br>
		 que se desee.<br>
</p>
<b><A NAME ="m26"><A HREF='#Arriba'>3.11 MODULO DE EJECUCION DE PROCESOS</a></b>
<p>
		Este modulo permite, ejecutar una lista de procesos asociados a un formulario si previamente se han inscrito en el modulo de definición de procesos <br>
		que ya se ha explicado.<br>
		Cuando se selecciona el modulo en el frame de opciones, aparece en el frame principal un botón de radio que permite seleccionar los formularios propios <br>
		de la aplicación o formularios que otro usuario haya compartido. Después de realizar la selección de tipo de formulario se oprime el botón ir para que el <br>
		servidor procese la información. Después de esta acción aparece en la misma página un drop-down con todos los formularios que cumplen la condición. <br>
		Se selecciona el formulario que se desee y se oprime nuevamente el botón "ir" para que el servidor procese la información; posteriormente aparece en la <br>
		misma página una tabla con el código y la descripción de los procesos asociados a ese formulario y el hipervínculo ejecutar que permite correr el proceso <br>
		que el usuario desee.<br>
</p>
<b><A NAME ="m27"><A HREF='#Arriba'>3.12 Modulo MODULO DE EJECUCION DE REPORTES</a></b>
<p>
		Este modulo permite, ejecutar una lista de reportes asociados a un formulario si previamente se han inscrito en el modulo de definición de reportes que <br>
		ya se ha explicado.<br>
		Cuando se selecciona el modulo en el frame de opciones, aparece en el frame principal un botón de radio que permite seleccionar los formularios propios <br>
		de la aplicación o formularios que otro usuario haya compartido. Después de realizar la selección de tipo de formulario se oprime el botón ir para que el <br>
		servidor procese la información. Después de esta acción aparece en la misma página un drop-down con todos los formularios que cumplen la condición.<br>
		Se selecciona el formulario que se desee y se oprime nuevamente el botón "ir" para que el servidor procese la información; posteriormente aparece en la <br>
		misma página una tabla con el código y la descripción de los reportes asociados a ese formulario y el hipervínculo ejecutar que permite correr el reporte<br>
		que el usuario desee.<br>
</p>
<b><A NAME ="m28"><A HREF='#Arriba'>3.13 MODULO DE CARGA DE ARCHIVOS PLANOS</a></b>
<p>
		Este modulo permite cargar información de archivos planos o "ASCII "cuyos campos tengan la misma estructura del formulario en el cual se desee montar<br>
		la información y que adicionalmente estén separados por "comas ". Es importante resaltar, que la "coma" es el único separador permitido para que el<br>
		modulo funcione correctamente.<br>
		Cuando se selecciona este modulo en el frame de opciones, aparece en el frame principal un drop-down con todos los formularios que componen la <br>
		aplicación, un área de texto donde el usuario especifica la ruta y el nombre del archivo plano que contiene la información, un botón "examinar " <br>
		que si se activa, abre un cuadro de dialogo que permite navegar por los volúmenes y directorios a los que tenga acceso el computador donde se esta<br>
		abriendo la pagina y que permite escoger de manera sencilla el archivo que se desee con su ruta asociada , por ultimo un botón " send file" que envía <br>
		la información al servidor para que el motor de base de datos la cargue en la tabla especificada.<br>
		Después de oprimir este botón aparece el numero de registros que se montaron y si es del caso, los registros que contienen error.<br>
</p>
<b><A NAME ="m29"><A HREF='#Arriba'>3.14 MODULO DE PUBLICACION DE ARCHIVOS</a></b>
<p>
		Este módulo permite cargar archivos o imágenes a un usuario de prioridad normal o adicionalmente programas y archivos incluidos si es el superusuario<br>
		"root" el que lo esta operando. Después de publicados, estos archivos se pueden usar de acuerdo a su modalidad a través de la pagina.<br>
		Cuando se selecciona este modulo en el frame de opciones, aparece un área de texto donde el usuario especifica la ruta y el nombre del archivo que se <br>
		desea publicar, un botón "examinar " que si se activa, abre un cuadro de dialogo que permite navegar por los volúmenes y directorios a los que tenga <br>
		acceso el computador donde se esta abriendo la pagina y que facilita la escogencia del archivo que se desee con su ruta asociada , unos botones de radio<br>
		que permiten seleccionar el tipo de archivo que se va a publicar ( planos y de imágenes para usuarios de prioridad normal y adicionalmente programas e<br>
		include para el superusuario) por ultimo un botón " send file" que envía la información al servidor para que realice la publicación.<br>
		Si se desean publicar imágenes, para que estas sean viables deben ser archivos tipo jpg, tif, gif, swf o png cuyo peso en bytes idealmente no supere<br>
		 los 100K.<br>
</p>
<b><A NAME ="m30"><A HREF='#Arriba'>3.15 OPCION MANUAL DE OPERACION</a></b>
<p>
		Esta opción abre una página con el presente manual conteniendo un índice con hipervínculos a las distintas opciones que componen la aplicación para <br>
		facilitar su manejo.<br>
		En cada opción hay una descripción detallada de su funcionalidad y de la forma de operar los objetos que la componen.<br>
</p>
<b><A NAME ="m31"><A HREF='#Arriba'>3.16 OPCION SALIR DEL PROGRAMA</a></b>
<p>
		Esta opción permite cerrar la sesión de un usuario y terminar la ejecución de la aplicación. Cuando se selecciona, se despliega una pagina que informa que <br>
		la sesión ha sido cerrada y despliega un hipervínculo "ir al login" para iniciar nuevamente con le usuario que se desee.<br>
</p>
</body>
</html>

