<html>
<head>
<title>Visitas magenta</title>

<script language="javascript" >
function guardar()
{
	
	document.batch.action='visitas.php';
	document.batch.submit();
	//alert("guardar");
}
</script>

</head>
<body >

<?php
include_once("conex.php");

/**
 * 	VISITAS DE AFINIDAD AAA-BBB
 * 
 * Este es un programa que muestra las visitas AAA y BBB del día, fecha de ingreso, egreso, lugar donde se encuentran etc
 * ademas tiene enlace para revisar y gestionar los datos del paciente a persona.php. Es programa porque permite señalar que
 * pacientes han sido visitados y cuales no encontrados, adicionalmente invoca un proceso que almacena quienes vinieron a la clinica
 * para consultarlos posterirmente, proceso que debe realizarse diariamente (batch.php). Pasa desapercibido el AAA o BBB es afin o otro tipo de usuario
 * 
 * @name  matrix\magenta\procesos\visitas.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-02-10
 * @version 2007-01-23
 * 
 * @modified  2006-05-10 Se pregunta en query si la persona ya esta facturada, si lo esta no se muestra
 * @modified  2007-01-15 se agregan varios campos para convertirlo en programa que grava,  si fue visitado el paciente y la fecha de ultimo contacto
 * @modified  2007-01-23 Se corrije la parte de las personas facturadas por otro tipo de identificaciones 
 
	Actualizacion: Se revisaron las consultas a unix para hacer union donde posiblemente se encuentren campos nulos, se utiliza la funcion para la validacion de los nulos Viviana Rodas 2012-05-17
	
	Actualizacion: Donde se utiliza la funion para datos nulos, se coloca en comentario la funcion ejecutar_consulta y validar_nulos porque ya esta en incVisitas que es el include. Viviana Rodas 2012-05-23
 * 
 * @table inpac, select
 * @table inpacotr, select 
 * @table inser, select, 
 * @table india, select,
 * @table famov, select
 * @table inmun, select
 * @table inmtra, select
 * @table aymov, select
 * @table aymovotr, select
 * @table inmegr, select
 * @table inpaci, select
 * @table magenta_000014, select, insert, update
 * @table magenta_000008, select
 * 
 *  @wvar $actualizar, indica si se ha undido el boton guardar que guarda las visitas en la base de datos y guarda los valores del los check box
 *  @wvar $am indica que la estadia en hospitalizacion si esta en 1, se calcula por cada paciente
 *  @wvar $amb indica que la estadia en hospitalizacion si esta en 1, es un vector que tiene la info de cada paciente
 *  @wvar $ano ano actual
 *  @wvar $ape1 apellido del paciente
 *  @wvar $ape2 apellido 2 del paciente
 *  @wvar $array, vector que guarda toda la info del paciente AAA 
 *  @wvar $array2, vector que guarda toda la info del paciente BBB 
 *  @wvar $bar barrio del paciente
 *  @wvar $ced, cedula del paciente 
 *  @wvar $cedTip tipo de cedula del paciente
 *  @wvar $codRes, reponsable de la facturacion del ingreso
 *  @wvar $color colores de depliegue de datos
 *  @wvar $diaNom diagnostico de ingreso
 *  @wvar $doc documento del paciente
 *  @wvar $enc, indica si en paciente no fue encontrado
 *  @wvar $encontrado, vector con la indicacion si cada uno de los pacientes AAA fue encontrado o no 
 *  @wvar $encontrado2, vector con la indicacion si cada uno de los pacientes BBB fue encontrado o no 
 *  @wvar $fecEgr fecha de egreso del paciente
 *  @wvar $fecha fecha para acotar la busqeuda de los AAA, en fecha de nacimiento inicial
 *  @wvar $fecha2 fecha para acotar la busqeuda de los AAA, en fecha de nacimiento final
 *  @wvar $fecIng fecha de ingreso del paciente
 *  @wvar $fecNac fecha de nacimiento
 *  @wvar $frecVis frecuencia de visita (cuantas veces ha vendio en los ultimos 6 años)
 *  @wvar $fuente de donde se saco la info del paciente (aymov, inpac o impaci)
 *  @wvar $fvis vector con el ultimo contacto que hubo con el pacinete AAA
 *  @wvar $fvis2 vector con el ultimo contacto que hubo con el pacinete AAA
 *  @wvar $his historia clinica del paciente
 *  @wvar $horEgr hora de egreso
 *  @wvar $horIng hora de ingreso
 *  @wvar $hoy fecha de hoy en formato unix
 *  @wvar $ingresos cantidad de ingresos encontrados entre las dos fechas
 *  @wvar $m, desde que numero empieza el vector de pacientes
 *  @wvar $nom nombre del pacinete
 *  @wvar $numIng numero de ingreso del paciente
 *  @wvar $pacHos indica si esta hospitalizado (H) o no (C o A)
 *  @wvar $repetido, indica si ya estaba el BBB en la lista
 *  @wvar $resto, ayuda a restarle al año para calcular las fechas de nacimeinto
 *  @wvar $serAct, servicio alctual donde se encuentra el pacinete, o ultimo del que salio 
 *  @wvar $serNom nombre del servicio de ingreso
 *  @wvar $sex sexo del paciente
 *  @wvar $size1 tamaño del vector de la info del paciente
 *  @wvar $suma, permite identificar si el paciente realmente se encuentra aun en la clinica, si esta en algun servicio sin egresar
 *  @wvar $tipDoc, tipo de documento del paciente
 *  @wvar $tipUsu, tipo de usuario que es el paciente, me dice si si es AAA o si si es BBB
 *  @wvar $vis indica si el paciente si fue visitado
 *  @wvar $visitado, vector con la indicacion si cada uno de los pacientes AAA fue visitado o no 
 *  @wvar $visitado2, vector con la indicacion si cada uno de los pacientes BBB fue visitado o no 
 *  @wvar $zona, zona en la que vive el paciente
 ******************************************************************************************************************************
 Actualizacion
 Fecha: 2016-05-05  Arleyda Insignares C. Se coloca encabezado, titulos y tablas con ultimo formato
 Fecha: 11-05-2012  Descripcion: Se cambiaron las rutas de los includes, ya que presentaron error por el cambio de servidor - Viviana Rodas
 Fecha: 29-05-2012  Descripcion: Se quito la funcion para los nulos, se colocaron los unions para optimizar el rendimiento 
 ******************************************************************************************************************************/

$wautor="Carolina Castano P.";
$wversion='2007-01-23';
$wactualiz='2016-05-05';

/****************************************************   PROGRAMA   ********************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

    include_once("root/comun.php");
	/////////////////////////////////////////////////encabezado general///////////////////////////////////
    $titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";

    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");  
	/**
	 * conexion a matrix
	 */
	

	

	$bd='facturacion';
	/**
	 * conexion a unix
	 */
	include_once("magenta/socket2.php");
	//entro a programa cuando la conexión se realizó
	if($conex_o != false)
	{
		/**
		 * funciones que se solicitan, como ordenar vectores por fechas
		 */
		include_once("magenta/incVisitas.php");
		$hoy=date('Y/m/d');


		//inicilaizo la forma
		echo "<form NAME='batch' ACTION='batch.php' METHOD='POST'>";

		$i=0;

		//////////////////////////////////////////////////////////Busqueda principal en db de pacientes AAA////////////////////////////

		$ano = date ('Y');
		$resto=date ('m/d');
		$fecha= intval($ano) - 25;
		$fecha= $fecha."/".$resto;
		
		$fecha2= intval($ano) - 65;
		$fecha2= $fecha2."/".$resto;
		//echo $fecha2;


		//utilizo las fechas para acotar las busquedas, es decir que las personas esten en ese rango de edades


		//actualizar significa que se chulearon visitados o no encontrados y ya se dipone del vector, no hay que volver a buscarlo

		if (!isset ($actualizar) or $actualizar!=1)
		{
			//Busco inicialmente en inpac quienes han venido hoy   
			//campo nulo pacap2													(1)	inpac														

			$query="(select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom ";
			$query= $query."from inpac A , inpacotr B, inser C, india E ";
			$query= $query."where B.pacotrhis=A.pachis and A.pacnac < '$fecha' and A.pacnac > '$fecha2' and B.pacotrnum=A.pacnum ";
			$query= $query."and C.sercod=A.pacser ";
			$query= $query."and A.pacap2 is not null ";
			$query= $query."and E.diacod=A.pacdin ) ";
			$query= $query." union ";
			$query= $query."(select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, '.' as pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom ";
			$query= $query."from inpac A , inpacotr B, inser C, india E ";
			$query= $query."where B.pacotrhis=A.pachis and A.pacnac < '$fecha' and A.pacnac > '$fecha2' and B.pacotrnum=A.pacnum ";
			$query= $query."and C.sercod=A.pacser ";
			$query= $query."and A.pacap2 is null ";
			$query= $query."and E.diacod=A.pacdin) ";

			$err_o = odbc_exec($conex_o,$query);

			while (odbc_fetch_row ($err_o))
			{
				switch (odbc_result($err_o,4))
				{
					case 'H':
					$am=1;
					break;
					case 'C':
					$am=0;
					break;
					case 'A':
					$am=0;
					break;
				}
				$pachis=odbc_result($err_o,1);
				$pacnum=odbc_result($err_o,2);
				//verifico que realmente esten en un servicio de la clinicia y no que no los hayan sacado aun de inpac
				//campo nulo traser
				$query="SELECT traser, trahab FROM inmtra WHERE trahis='$pachis' and tranum='$pacnum' and traegr is null";
				$query= $query." and traser is not null";
				$query= $query." union ";
				$query= $query."SELECT '.' traser, trahab FROM inmtra WHERE trahis='$pachis' and tranum='$pacnum' and traegr is null";
				$query= $query." and traser is null";

				$err_1 = odbc_exec($conex_o,$query);
				$suma=0;

				while (odbc_fetch_row ($err_1))
				{
					$suma++;
				}

				//ademas si estan en inpac y es ambulatorio y tienen fecha que no es la de hoy, es porque no estan en la clinica sino que no los han movido de inpac
				if (($am==0 and odbc_result($err_o,11)==date('Y/m/d')) or ($am==1 and $suma>0))
				{
					$his [$i]= odbc_result($err_o,1);
					$numIng [$i]= odbc_result($err_o,2);
					$ced [$i]= odbc_result($err_o,5);
					$cedTip [$i]= odbc_result($err_o,6);
					$nom [$i]= odbc_result($err_o,8);
					$ape1 [$i]= odbc_result($err_o,9);
					$ape2 [$i]= odbc_result($err_o,10);
					$fecIng [$i]= odbc_result($err_o,11);
					$fecNac [$i]= odbc_result($err_o,12);
					$sex[$i]=odbc_result($err_o,13);
					$codRes [$i]= odbc_result($err_o,14);
					$bar [$i]= odbc_result($err_o,15);
					$serNom [$i]= odbc_result($err_o,17);
					$diaNom [$i]= odbc_result($err_o,18);
					$horIng [$i]= odbc_result($err_o,3);
					$pacHos [$i]= odbc_result($err_o,4);
					$fuente[$i]='inpac';

					switch ($pacHos[$i])
					{
						case 'H':
						$amb[$i]=1;
						$serAct [$i]='';
						break;
						case 'C':
						$amb[$i]=0;
						$serAct [$i]= odbc_result($err_o,17);
						break;
						case 'A':
						$amb[$i]=0;
						$serAct [$i]= odbc_result($err_o,17);
						break;
					}


					$i++;
				}
			}
			
			//======================================
			//Para los años visiestos
			//======================================
			$wfec=explode("/",$fecha);
			if ($wfec[1]=='02' and $wfec[2]=='29')
			{
				$fecha = $wfec[0]."/02/28";
		    }	
			
			$wfec2=explode("/",$fecha2);
			if ($wfec2[1]=='02' and $wfec2[2]=='29')
			{
				$fecha2 = $wfec2[0]."/02/28";
	        }
	        //======================================

																		//	(2) aymov
			//búsco en aymov, pueden haber venido hoy
			//campo nulo movnum, movap2
		$long='                                    ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
			$increment=1;
						

			$query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
			$query= $query."from aymov A , aymovotr B, inser C, india E ";
			$query= $query."where movfec='$hoy' and A.movnac < '$fecha' and A.movnac > '$fecha2' ";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."and A.movnum is not null ";
			$query= $query."and A.movap2 is not null ";
			$query= $query."and E.diacod=A.movdia ";
			$query= $query." union ";
			$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
			$query= $query."from aymov A , aymovotr B, inser C, india E ";
			$query= $query."where movfec='$hoy' and A.movnac < '$fecha' and A.movnac > '$fecha2' ";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."and A.movnum is null ";
			$query= $query."and A.movap2 is null ";
			$query= $query."and E.diacod=A.movdia ";
			$query= $query." union ";
			$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
			$query= $query."from aymov A , aymovotr B, inser C, india E ";
			$query= $query."where movfec='$hoy' and A.movnac < '$fecha' and A.movnac > '$fecha2' ";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."and A.movnum is null ";
			$query= $query."and A.movap2 is not null ";
			$query= $query."and E.diacod=A.movdia ";
			$query= $query." union ";
			$query= $query."select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
			$query= $query."from aymov A , aymovotr B, inser C, india E ";
			$query= $query."where movfec='$hoy' and A.movnac < '$fecha' and A.movnac > '$fecha2' ";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."and A.movnum is not null ";
			$query= $query."and A.movap2 is null ";
			$query= $query."and E.diacod=A.movdia ";
			$query= $query." order by 3";
			
			$err_o = odbc_exec($conex_o,$query);


			while (odbc_fetch_row ($err_o))
			{


				$his [$i]= odbc_result($err_o,1);
				$numIng [$i]= odbc_result($err_o,2);
				$ced [$i]= odbc_result($err_o,5);
				$cedTip [$i]= odbc_result($err_o,6);
				$nom [$i]= odbc_result($err_o,8);
				$ape1 [$i]= odbc_result($err_o,9);
				$ape2 [$i]= odbc_result($err_o,10);
				$fecIng [$i]= odbc_result($err_o,11);
				$fecNac [$i]= odbc_result($err_o,12);
				$sex[$i]=odbc_result($err_o,13);
				$codRes [$i]= odbc_result($err_o,14);
				$bar [$i]= odbc_result($err_o,15);
				$serNom [$i]= odbc_result($err_o,17);
				$diaNom [$i]= odbc_result($err_o,18);
				$horIng [$i]= odbc_result($err_o,3);
				$serAct [$i]= odbc_result($err_o,17);
				$fecEgr [$i]= '';
				$horEgr [$i]='';
				$amb[$i]=0;
				$fuente[$i]='aymov';
				$i++;

			}


			//búsqueda en inpaci,algunas veces los de el mismo día los egresan de una vez
			//dato nulo pacap2																(3) inpaci
	
						
			$query="select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, '.' pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom, E.dianom   ";
			$query= $query."from inpaci A , inpacotr B, inser C, inmegr D, india E ";
			$query= $query."WHERE A.pacnac < '$fecha' and  A.pacnac > '$fecha2' and D.egring='$hoy' and D.egregr='$hoy' ";
			$query= $query." and A.pacap2 is null ";
			$query= $query." and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
			$query= $query." and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
			$query= $query." and C.sercod=D.egrsin ";
			$query= $query." and E.diacod=D.egrdin"; 
			$query= $query." union "; 
			$query= $query."select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom, E.dianom   ";
			$query= $query."from inpaci A , inpacotr B, inser C, inmegr D, india E ";
			$query= $query."WHERE A.pacnac < '$fecha' and  A.pacnac > '$fecha2' and D.egring='$hoy' and D.egregr='$hoy' ";
			$query= $query." and A.pacap2 is not null ";
			$query= $query." and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
			$query= $query." and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
			$query= $query." and C.sercod=D.egrsin ";
			$query= $query." and E.diacod=D.egrdin order by 6";

			$err_o = odbc_exec($conex_o,$query);
			
    	
			while (odbc_fetch_row ($err_o))
			{
				$his [$i]= odbc_result($err_o,1);
				$numIng [$i]= odbc_result($err_o,2);
				$ced [$i]= odbc_result($err_o,10);  // se encontro en 2008/08/29 que no estaba mostrando informacion, ya que tenia que ingresar por aca el campo 3 y es el 10 y en el 10 es el 3
				$cedTip [$i]= odbc_result($err_o,11);
				$nom [$i]= odbc_result($err_o,12);
				$ape1 [$i]= odbc_result($err_o,13);
				$ape2 [$i]= odbc_result($err_o,14);
				$fecNac [$i]= odbc_result($err_o,15);
				$sex[$i]=odbc_result($err_o,16);
				$codRes [$i]= odbc_result($err_o,3);
				$fecIng [$i]= odbc_result($err_o,5);
				$fecEgr [$i]= odbc_result($err_o,8);
				$horIng [$i]= odbc_result($err_o,6);
				$horEgr [$i]= odbc_result($err_o,7);
				$bar [$i]= odbc_result($err_o,17).'-a';
				$serNom [$i]= odbc_result($err_o,18);
				$diaNom [$i]= odbc_result($err_o,19);
				$serAct [$i]= odbc_result($err_o,18);
				$amb[$i]=0;
				$fuente[$i]='inpaci';
				$i++;
			}
			//echo $i;
			$ingresos= $i;
		}
		else
		{
			$i=$k;
		}

		//////////////////////////////////////////////////////////Fin Busqueda principal en db////////////////////////////

		//////////////////////////////////////////////////////////selección de clientes AAA////////////////////////////


		ECHO "<table border=0 align=center size=100%>";
        echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>LISTA DE VISITAS DE HOY: $hoy</b></font></div></div></BR>";
		//ECHO "<div align='center' class='fila1' style='width:520px;'><tr><td align=center ><A HREF='visitas.php'><font size=4 color='#FF00FF'>LISTA DE VISITAS DE HOY: $hoy </font></a></td></tr></div>";
		ECHO "</table></br>";
		if ($i==0)
		{
			echo '<script language="Javascript">';
			echo 'window.location.href=window.location.href;';
			echo 'alert ("No se encontró información en el rango de fechas ingresado, intente nuevamente por favor ")';
			echo '</script>';
		}

		echo "<center><img SRC='/matrix/images/medical/Magenta/aaa.gif'></center></br>";

		if (!isset ($actualizar) or $actualizar!=1)
		{
			$k=0;
			for ($j=0; $j<$ingresos; $j++)
			{
				//en este momento solo se identifica que el paciente es AAA si esta en la base de datos
				//estas son funciones del include incvisitas, pero la de zona en este momento no esta sirviendo de mucho
				$zona=Zona($bar[$j]);
				//se fija si esta en matrix y si ha pagado con prepagada en las ultimas 10 veces
				$tipUsu= esAAA ($ced[$j], $cedTip[$j], $codRes [$j] );

				If ($tipUsu==1)
				{
					$tipUsu='AFIN-AAA-1';
				}else if ($tipUsu!=3)
				{
					$frecVis= calcularFec ($fuente[$j], $ced [$j], $cedTip[$j], $his[$j]);
					//esta parte tampoco esta funcionando temporalmtne porque asi se meta, la tabla de zonas esta vacia
					$tipUsu=definirTipUsu ($frecVis, $sex[$j], $fecNac[$j], $zona, $codRes[$j], $ced[$j], $cedTip[$j]);
				}

				if ($tipUsu==3)
				{
					$tipUsu='DESCLASIFICADO';
				}


				//si el afin es AAA, lo meto en el vector a mostrar
				if (strcmp ($tipUsu,'AFIN-AAA-1')==0)
				{
					//echo 'hola';
					//echo $ced[$j];
					//echo 'hola';
					//echo $frecVis;
					//echo  $zona;


					$array[$k][0]=trim($ced[$j]).' '.trim($cedTip[$j]);
					$array[$k][1]=$his [$j];
					$array[$k][2]=$nom [$j].$ape1 [$j].$ape2 [$j];
					$array[$k][3]=$fecIng [$j];
					$array[$k][4]=$serNom [$j];
					$array[$k][5]= $horIng [$j];
					$array[$k][6]=$serAct [$j];
					$array[$k][7]=$tipUsu;
					$array[$k][8]=$numIng [$j];
					$array[$k][9]=$zona;
					$array[$k][10]=$fuente [$j];
					$array[$k][11]=$fecNac [$j];
					$array[$k][12]=$amb [$j];
					$array[$k][13]=$ced [$j];
					$array[$k][14]=$cedTip[$j];

					$k++;
				}
			}

		}else //esta parte se realiza una vez se de actualizar
		{

			for ($i=0; $i<$k; $i++)
			{

				$array[$i][3]=str_replace ( '/',  '-', $array[$i][3] );

				if ($array[$i][15]==0 or $array[$i][15]==1)
				{
					$array[$i][15]='off';
				}else
				{
					$array[$i][15]='on';
				}
				if ($array[$i][16]==1)
				{
					$array[$i][16]='off';
				}else
				{
					$array[$i][16]='on';
				}


				//inicializamos los datos para visitados o no encontrados

				if (isset($encontrado[$i]))
				{
					$enc='on';
				}
				else
				{
					$enc='';
				}
				if (isset($visitado[$i]))
				{
					$vis='on';
					$enc='';
					//si esta señalado visitado, cambiamos la fecha de la ultima visita
					$fvis[$i]=date('Y-m-d');
				}
				else
				{
					$vis='';
					IF ($fvis[$i]==date('Y-m-d'))
					{
						$fvis[$i]='0000-00-00';
					}
				}


				$query="select * from magenta_000014 where repdoc='".$array[$i][0]."' and reping ='".$array[$i][3]."' ";
				$acu=mysql_query($query,$conex);
				$num=mysql_num_rows($acu);

				if($num < 1)
				{
					$doc=trim($array[$i][13]);
					$tipDoc=trim($array[$i][14]);
					switch ($tipDoc)
					{
						case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
						break;
						case "TI": $tipDoc="TI-Tarjeta de Identidad";
						break;
						case "NU": $tipDoc="NU-Numero Unico de Identificación";
						break;
						case "CE": $tipDoc="CE-Cedula de Extrangeria";
						break;
						case "PA": $tipDoc="PA-Pasaporte";
						break;
						case "RC": $tipDoc="RC-Registro Civil";
						break;
						case "AS": $tipDoc="AS-Adulto Sin Identificación";
						break;
						case "MS": $tipDoc="MS-Menor Sin Identificación";
						break;
					}

					$query="select clitip, clipac, clifac from magenta_000008 where clidoc='".$doc."' and clitid ='".$tipDoc."' ";
					$err=mysql_query($query,$conex);
					$num2=mysql_num_rows($err);
					if($num2 >= 1)
					{
						//echo 'memeto1';
						$resulta = mysql_fetch_row($err);
						$n=explode("-",$resulta[0]);

						If ($n[0] != 'VIP')
						{
							//echo 'memeto2';
							$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac, repfac, repvis, repenc, repfvi, Seguridad) ";
							$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$array[$i][0]."', '".$array[$i][1]."', '".$array[$i][2]."', '".$array[$i][3]."', '".$array[$i][4]."',  '".$array[$i][5]."', '".$resulta[0]."', '".$array[$i][15]."', '".$array[$i][16]."', '".$array[$i][12]."', '".$resulta[1]."', '".$resulta[2]."', '".$vis."','".$enc."','".$fvis[$i]."','A-magenta' )";
							$err=mysql_query($q1,$conex);
						}else
						{
							$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac,  repfac, repvis, repenc, repfvi, Seguridad) ";
							$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$array[$i][0]."', '".$array[$i][1]."', '".$array[$i][2]."', '".$array[$i][3]."', '".$array[$i][4]."',  '".$array[$i][5]."', 'VIP', '".$array[$i][15]."','".$array[$i][16]."', '".$array[$i][12]."',  '".$resulta[1]."', '".$resulta[2]."', '".$vis."','".$enc."','".$fvis[$i]."','A-magenta' )";
							$err=mysql_query($q1,$conex);
						}
					}
					else
					{
						$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac, repfac, repvis, repenc, repfvi, Seguridad) ";
						$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$array[$i][0]."', '".$array[$i][1]."', '".$array[$i][2]."', '".$array[$i][3]."', '".$array[$i][4]."',  '".$array[$i][5]."', '".$array[$i][7]."', '".$array[$i][15]."', '".$array[$i][16]."', '".$array[$i][12]."', '".$resulta[1]."', '".$resulta[2]."', '".$vis."','".$enc."','".$fvis[$i]."','A-magenta' )";
						$err=mysql_query($q1,$conex);
					}

				}
				else
				{
					$q1="update magenta_000014 set repvis='".$vis."', repenc='".$enc."', repfvi='".$fvis[$i]."' where repdoc='".$array[$i][0]."' and reping ='".$array[$i][3]."' ";
					$err=mysql_query($q1,$conex);
				}
			}
		}

		//esta parte es comun, se hace si fue actualizado o no, es mostrar los datos
		if ($k !=0)
		{
			$m=$k-1;

			//funcion de incvisitas que ordena el vector segun la fecha de ingreso
			$array=ordenador (3, $array, $m, 14);
			$array=ordenador2  (5, 3, $array, $m, 14);
			//se obtienen los valores 15 a 18 del vector, si se actualizo o no quien y cuando
			// 15 actualizado
			// datos completos
			// 17 fecha de actualizacion
			//18 persona que actualiza
			$array=verAct ($array, 14);
			$size1=count($array);


			ECHO "<table border=0 align=center size=100%>";
			ECHO "<tr><td align=center ><font size=3 color='blue'>Número de Visitas AAA: $size1</font></td></tr>";
			ECHO "</table></br>";


			ECHO '<TABLE border=1 align=center>';
			ECHO "<Tr class='encabezadotabla'>";

			echo "<td align=center><font size=2>Documento</font></td>";
			echo "<td align=center><font size=2>Nº Historia</font></td>";
			echo "<td align=center width=15%><font size=2>Nombre</font></td>";
			echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
			echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
			echo "<td align=center width=5%><font size=2>Hora de Ingreso</font></td>";
			echo "<td align=center width=10%><font size=2>Servicio Actual</font></td>";
			echo "<td align=center width=5%><font size=2>Hab.</font></td>";
			echo "<td align=center><font size=2>Amb.</font></td>";
			echo "<td align=center><font size=2>Actualizado</font></td>";
			echo "<td align=center><font size=2>Visitado</font></td>";
			echo "<td align=center><font size=2>No encontrado</font></td>";
			echo "<td align=center><font size=2>Último contacto</font></td>";
			echo "<td align=center width=15%><font size=2>Actualizado por</font></td>";
			echo "<td align=center><font size=2>Inf. Ok</font></td>";

			ECHO "</Tr >";

			for ($i=0; $i<$size1; $i++)
			{
				$doc=trim($array[$i][13]);
				$tipDoc=trim($array[$i][14]);
				switch ($tipDoc)
				{
					case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
					break;
					case "TI": $tipDoc="TI-Tarjeta de Identidad";
					break;
					case "NU": $tipDoc="NU-Numero Unico de Identificación";
					break;
					case "CE": $tipDoc="CE-Cedula de Extrangeria";
					break;
					case "PA": $tipDoc="PA-Pasaporte";
					break;
					case "RC": $tipDoc="RC-Registro Civil";
					break;
					case "AS": $tipDoc="AS-Adulto Sin Identificación";
					break;
					case "MS": $tipDoc="MS-Menor Sin Identificación";
					break;
				}

				if (is_int ($i/2))
				$color='fila1';
				else
				$color='fila2';

				ECHO "<Tr >";
				echo "<td  class='$color' align=center><a href='Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."&cco=2050' target='_blank'><font size=2>".$array[$i][0]."</font></a></td>";
				for ($j=1; $j<6; $j++)
				{
					echo "<td class='$color' align=center><font size=2>".$array[$i][$j].'</font></td>';
				}

				//BUSCO EL SERVICIO EN EL QUE ESTAN
				switch ($array[$i][12])
				{
					case 0:
					echo "<td class='$color' align=center ><font size=2>".$array[$i][6].'</font></td>';
					echo "<td class='$color' align=center >&nbsp;</td>";
					echo "<td class='$color'><input type='checkbox' name='Ambulatorio'  checked ></td>";
					break;
					case 1:
					//no se le coloca union porque se hizo la prueba con varios datos en el campo tranum y no venia ninguno vacio
					$query="select A.traser, A.trahab, B.sernom    ";
					$query= $query."from inmtra A , inser B ";
					$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][8]."' and A.traegr is null "; //cambiado a is null
					$query= $query."and B.sercod=A.traser ";
					
					
					$err_o = odbc_exec($conex_o,$query);
					$resulta=odbc_result($err_o,3);
					echo "<td class='$color' align=center><font size=2>".$resulta."</font></td>";
					$resulta=odbc_result($err_o,2);
					echo "<td class='$color' align=center><font size=2>".$resulta."</font></td>";
					echo "<td class='$color'><input type='checkbox' name='Ambulatorio'></td>";
					break;
				}

				// si es dos, esta actualizado y se escribe fecha de actualizacion
				switch ($array[$i][15])
				{
					case 0:
					echo "<td class='$color'><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$array[$i][17]."</font></td>";
					break;
					case 1:
					echo "<td class='$color'><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$array[$i][17]."</font></td>";
					break;
					case 2:
					echo "<td class='$color'><input type='checkbox' name='Actualizado' checked >&nbsp;<font size=2>".$array[$i][17]."</font></td>";
					break;
				}

				$query ="SELECT repvis, repenc FROM magenta_000014 where repdoc='".$array[$i][0]."' and reping='".$array[$i][3]."' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores
				{
					$rol= mysql_fetch_row($err);
					if ($rol[0]=='on')
					$rol[0]='checked';
					if ($rol[1]=='on')
					$rol[1]='checked';
					echo "<td class='$color'><input type='checkbox' name='visitado[".$i."]'  ".$rol[0]." ><a href='/Matrix/Magenta/reportes/rep_segvisita.php?docume=".$doc." ".$tipDoc."' target='_blank'><font size=2>Consultar</font></a> </td>";
					echo "<td class='$color'><input type='checkbox' name='encontrado[".$i."]'  ".$rol[1]." ></td>";
				}
				else
				{
					echo "<td class='$color'><input type='checkbox' name='visitado[".$i."]' ><a href='/Matrix/Magenta/reportes/rep_segvisita.php?docume=".$doc." ".$tipDoc."' target='_blank'><font size=2>Consultar</font></a> </td>";
					echo "<td class='$color'><input type='checkbox' name='encontrado[".$i."]' ></td>";
				}

				//consulto la fecha de la ultima visita
				$query ="SELECT repfvi FROM magenta_000014 where repdoc='".$array[$i][0]."' and rephis='".$array[$i][1]."' order by repfvi desc";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores
				{
					$rol= mysql_fetch_row($err);
					if ($rol[0]=='0000-00-00')
					echo "<td class='$color' align=center ><font size=2>-</font></td>";
					else
					echo "<td class='$color' align=center ><font size=2>".$rol[0]."</font></td>";
					echo "<input type='hidden' name='fvis[".$i."]'  value='".$rol[0]."' >";
				}
				else
				{
					echo "<td class='$color' align=center ><font size=2>-</font></td>";
					echo "<input type='hidden' name='fvis[".$i."]'  value='' >";
				}

				echo "<td class='$color' align=center ><font size=2>".$array[$i][18].'</font></td>';


				//si los datos estan completos
				switch ($array[$i][16])
				{
					case 0:
					echo "<td class='$color'><input type='checkbox' name='Datos' checked ></td>";
					break;
					case 1:
					echo "<td class='$color'><input type='checkbox' name='Datos'></td>";
					break;
				}

				ECHO "</Tr >";
			}

			echo "</table>";
		}ELSE
		{	echo "<table>";
		echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
		echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO PACIENTE AAA HA INGRESADO EL DIA DE HOY</td><tr>";
		echo "</table></fieldset>";
		}


		//////////////////////////////////////////////////////////Fin selección de clientes AAA////////////////////////////
		//////////////////////////////////////////////////////////Busqueda principal en db////////////////////////////

		// se realiza exactamnete la misma busqueda pero para paciente BBB

		if (!isset ($actualizar) or $actualizar!=1)
		{
			$i=0;

			//Busco inicialmente en inpac quienes han venido hoy					(4) inpac

			$query=" select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom ";
			$query= $query."from inpac A , inpacotr B, inser C, india E ";
			$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
			$query= $query."and C.sercod=A.pacser ";
			$query= $query."and E.diacod=A.pacdin  ";
			$query= $query."and A.pacap2 is not null  ";
			$query= $query." union ";
			$query= $query."select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, '.' pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom ";
			$query= $query."from inpac A , inpacotr B, inser C, india E ";
			$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
			$query= $query."and C.sercod=A.pacser ";
			$query= $query."and E.diacod=A.pacdin ";			
			$query= $query."and A.pacap2 is null  ";

			$err_o = odbc_exec($conex_o,$query);

			while (odbc_fetch_row ($err_o))
			{
				switch (odbc_result($err_o,4))
				{
					case 'H':
					$am=1;
					break;
					case 'C':
					$am=0;
					break;
					case 'A':
					$am=0;
					break;
				}

				
				$pacnum=odbc_result($err_o,2);					//(7) inmtra
				//verifico que realmente esten en un servicio de la clinicia y no que no los hayan sacado aun de inpac
				
				$query="select A.traser, A.trahab   ";
				$query= $query."from inmtra A  ";
				$query= $query."where A.trahis='".odbc_result($err_o,1)."' and A.tranum='".odbc_result($err_o,2)."' and A.traegr is null ";
				$query= $query."and A.traser is not null ";
				$query= $query." union ";
				$query= $query."select '.' as traser, A.trahab   ";
				$query= $query."from inmtra A  ";
				$query= $query."where A.trahis='".odbc_result($err_o,1)."' and A.tranum='".odbc_result($err_o,2)."' and A.traegr is null ";
				$query= $query."and A.traser is null  ";
				
						
				$err_1 = odbc_exec($conex_o,$query);
				$suma=0;

				while (odbc_fetch_row ($err_1))
				{
					$suma++;
				}

				//ademas si estan en inpac y es ambulatorio y tienen fecha que no es la de hoy, es porque no estan en la clinica sino que no los han movido de inpac
				if (($am==0 and odbc_result($err_o,11)==date('Y/m/d')) or ($am==1 and $suma>0))
				{
					$his [$i]= odbc_result($err_o,1);
					$numIng [$i]= odbc_result($err_o,2);
					$ced [$i]= odbc_result($err_o,5);
					$cedTip [$i]= odbc_result($err_o,6);
					$nom [$i]= odbc_result($err_o,8);
					$ape1 [$i]= odbc_result($err_o,9);
					$ape2 [$i]= odbc_result($err_o,10);
					$fecIng [$i]= odbc_result($err_o,11);
					$fecNac [$i]= odbc_result($err_o,12);
					$sex[$i]=odbc_result($err_o,13);
					$codRes [$i]= odbc_result($err_o,14);
					$bar [$i]= odbc_result($err_o,15);
					$serNom [$i]= odbc_result($err_o,17);
					$diaNom [$i]= odbc_result($err_o,18);
					$horIng [$i]= odbc_result($err_o,3);
					$pacHos [$i]= odbc_result($err_o,4);
					$fuente[$i]='inpac';

					switch ($pacHos[$i])
					{
						case 'H':
						$amb[$i]=1;
						$serAct [$i]='';
						break;
						case 'C':
						$amb[$i]=0;
						$serAct [$i]= odbc_result($err_o,17);
						break;
						case 'A':
						$amb[$i]=0;
						$serAct [$i]= odbc_result($err_o,17);
						break;
					}


					$i++;
				}
			}
			
			

			//búsco en aymov, pueden haber venido hoy	
			
										
																			//(5) aymov
		
			$query="(select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
				$query= $query."from aymov A , aymovotr B, inser C, india E ";
				$query= $query."where movfec='$hoy' ";
				$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
				$query= $query."and A.movnum is not null ";
				$query= $query."and A.movap2 is not null ";
				$query= $query."and C.sercod=A.movsin ";
				$query= $query."and E.diacod=A.movdia) "; 
				$query= $query." union ";  
				$query= $query."(select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
				$query= $query."from aymov A , aymovotr B, inser C, india E ";
				$query= $query."where movfec='$hoy' ";
				$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
				$query= $query."and A.movnum is null ";
				$query= $query."and A.movap2 is null ";
				$query= $query."and C.sercod=A.movsin ";
				$query= $query."and E.diacod=A.movdia) ";
				$query= $query." union "; 
				$query= $query."(select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
				$query= $query."from aymov A , aymovotr B, inser C, india E ";
				$query= $query."where movfec='$hoy' ";
				$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
				$query= $query."and A.movnum is null ";
				$query= $query."and A.movap2 is not null ";
				$query= $query."and C.sercod=A.movsin ";
				$query= $query."and E.diacod=A.movdia) "; 
				$query= $query." union ";  
				$query= $query."(select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
				$query= $query."from aymov A , aymovotr B, inser C, india E ";
				$query= $query."where movfec='$hoy' ";
				$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
				$query= $query."and A.movnum is not null ";
				$query= $query."and A.movap2 is null ";
				$query= $query."and C.sercod=A.movsin ";
				$query= $query."and E.diacod=A.movdia) ";  
				$query= $query."order by 3 ";   
			
			$err_o = odbc_exec($conex_o,$query);


			while (odbc_fetch_row ($err_o))
			{


				$his [$i]= odbc_result($err_o,1);
				$numIng [$i]= odbc_result($err_o,2);
				$ced [$i]= odbc_result($err_o,5);
				$cedTip [$i]= odbc_result($err_o,6);
				$nom [$i]= odbc_result($err_o,8);
				$ape1 [$i]= odbc_result($err_o,9);
				$ape2 [$i]= odbc_result($err_o,10);
				$fecIng [$i]= odbc_result($err_o,11);
				$fecNac [$i]= odbc_result($err_o,12);
				$sex[$i]=odbc_result($err_o,13);
				$codRes [$i]= odbc_result($err_o,14);
				$bar [$i]= odbc_result($err_o,15);
				$serNom [$i]= odbc_result($err_o,17);
				$diaNom [$i]= odbc_result($err_o,18);
				$horIng [$i]= odbc_result($err_o,3);
				$serAct [$i]= odbc_result($err_o,17);
				$fecEgr [$i]= '';
				$horEgr [$i]='';
				$amb[$i]=0;
				$fuente[$i]='aymov';
				$i++;

			}


			//búsqueda en inpaci,algunas veces los de el mismo día los egresan de una vez
			
			//echo $query;
														//(6) inpaci
				$query="select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom, E.dianom   ";
				$query= $query."from inpaci A , inpacotr B, inser C, inmegr D, india E ";
				$query= $query."WHERE D.egring='$hoy' and D.egregr='$hoy' ";
				$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
				$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
				$query= $query."and C.sercod=D.egrsin ";
				$query= $query."and A.pacap2 is not null ";
				$query= $query."and E.diacod=D.egrdin ";
				$query= $query." union ";
				$query= $query."select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, '.' as pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom, E.dianom   ";
				$query= $query."from inpaci A , inpacotr B, inser C, inmegr D, india E ";
				$query= $query."WHERE D.egring='$hoy' and D.egregr='$hoy' ";
				$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
				$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
				$query= $query."and C.sercod=D.egrsin ";
				$query= $query."and A.pacap2 is null ";
				$query= $query."and E.diacod=D.egrdin ";
				$query= $query." order by 6 ";

			
			$err_o = odbc_exec($conex_o,$query);

			
			
			while (odbc_fetch_row ($err_o))
			{
				$his [$i]= odbc_result($err_o,1);
				$numIng [$i]= odbc_result($err_o,2);
				$ced [$i]= odbc_result($err_o,10);  // se encontro en 2008/10/28 que no estaba mostrando informacion, ya que tenia que ingresar por aca el campo 3 y es el 10 y en el 10 es el 3
				$cedTip [$i]= odbc_result($err_o,11);
				$nom [$i]= odbc_result($err_o,12);
				$ape1 [$i]= odbc_result($err_o,13);
				$ape2 [$i]= odbc_result($err_o,14);
				$fecNac [$i]= odbc_result($err_o,15);
				$sex[$i]=odbc_result($err_o,16);
				$codRes [$i]= odbc_result($err_o,3);
				$fecIng [$i]= odbc_result($err_o,5);
				$fecEgr [$i]= odbc_result($err_o,8);
				$horIng [$i]= odbc_result($err_o,6);
				$horEgr [$i]= odbc_result($err_o,7);
				$bar [$i]= odbc_result($err_o,17).'-a';
				$serNom [$i]= odbc_result($err_o,18);
				$diaNom [$i]= odbc_result($err_o,19);
				$serAct [$i]= odbc_result($err_o,18);
				$amb[$i]=0;
				$fuente[$i]='inpaci';
				$i++;
			}
			$ingresos= $i;


			//////////////////////////////////////////////////////////Fin Busqueda principal en db////////////////////////////
			//////////////////////////////////////////////////////////selección de clientes BBB////////////////////////////


			$k1=0;
		}
		echo "</br></br></br></br><center><img SRC='/MATRIX/images/medical/Magenta/BBB.gif' ><center></br></br>";

		if (!isset ($actualizar) or $actualizar!=1)
		{
			for ($j=0; $j<$ingresos; $j++)
			{

				//consulto en include de incvisitas si el paciente es BBB
				$tipUsu=esBBB ($ced[$j], $cedTip[$j]);

				if ($tipUsu==1)
				{
					for ($h=0; $h<$size1; $h++)
					{
						if (trim($array[$h][13])==trim($ced[$j]))
						$repetido=1;
					}


					if (!isset ($repetido))
					{
						$tipUsu='AFIN-BBB-2';

						$array2[$k1][0]=trim($ced[$j]).' '.trim($cedTip[$j]);
						$array2[$k1][1]=$his [$j];
						$array2[$k1][2]=$nom [$j].$ape1 [$j].$ape2 [$j];
						$array2[$k1][3]=$fecIng [$j];
						$array2[$k1][4]=$serNom [$j];
						$array2[$k1][5]= $horIng [$j];
						$array2[$k1][6]=$serAct [$j];
						$array2[$k1][7]=$tipUsu;
						$array2[$k1][8]=$numIng [$j];
						$array2[$k1][9]=$zona;
						$array2[$k1][10]=$fuente [$j];
						$array2[$k1][11]=$fecNac [$j];
						$array2[$k1][12]=$amb [$j];
						$array2[$k1][13]=$ced [$j];
						$array2[$k1][14]=$cedTip[$j];
						$k1++;
					}
				}

			}
		}
		else //si se presiono actualizar
		{
			//guardamos los datos del vector
			for ($i=0; $i<$k1; $i++)
			{
				//echo $vector[$i][3];

				$array2[$i][3]=str_replace ( '/',  '-', $array2[$i][3] );

				if ($array2[$i][15]==0 or $array2[$i][15]==1)
				{
					$array2[$i][15]='off';
				}else
				{
					$array2[$i][15]='on';
				}
				if ($array2[$i][16]==1)
				{
					$array2[$i][16]='off';
				}else
				{
					$array2[$i][16]='on';
				}

				if (isset($encontrado2[$i]))
				{
					$enc='on';
				}
				else
				{
					$enc='';
				}
				if (isset($visitado2[$i]))
				{
					$vis='on';
					$enc='';
					$fvis2[$i]=date('Y-m-d');
				}
				else
				{
					$vis='';
					IF ($fvis2[$i]==date('Y-m-d'))
					{
						$fvis2[$i]='0000-00-00';
					}
				}




				$query="select * from magenta_000014 where repdoc='".$array2[$i][0]."' and reping ='".$array2[$i][3]."' ";
				$acu=mysql_query($query,$conex);
				$num=mysql_num_rows($acu);

				if($num < 1)
				{
					$doc=trim($array2[$i][13]);
					$tipDoc=trim($array2[$i][14]);
					switch ($tipDoc)
					{
						case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
						break;
						case "TI": $tipDoc="TI-Tarjeta de Identidad";
						break;
						case "NU": $tipDoc="NU-Numero Unico de Identificación";
						break;
						case "CE": $tipDoc="CE-Cedula de Extrangeria";
						break;
						case "PA": $tipDoc="PA-Pasaporte";
						break;
						case "RC": $tipDoc="RC-Registro Civil";
						break;
						case "AS": $tipDoc="AS-Adulto Sin Identificación";
						break;
						case "MS": $tipDoc="MS-Menor Sin Identificación";
						break;
					}

					$query="select clitip, clipac, clifac from magenta_000008 where clidoc='".$doc."' and clitid ='".$tipDoc."' ";
					$err=mysql_query($query,$conex);
					$num2=mysql_num_rows($err);

					if($num2 >= 1)
					{
						//echo 'memeto1';
						$resulta = mysql_fetch_row($err);
						$n=explode("-",$resulta[0]);

						If ($n[0] != 'VIP')
						{
							//echo 'memeto2';
							$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac, repfac, repvis, repenc, repfvi, Seguridad) ";
							$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$array2[$i][0]."', '".$array2[$i][1]."', '".$array2[$i][2]."', '".$array2[$i][3]."', '".$array2[$i][4]."',  '".$array2[$i][5]."', '".$resulta[0]."', '".$array2[$i][15]."', '".$array2[$i][16]."', '".$array2[$i][12]."', '".$resulta[1]."', '".$resulta[2]."', '".$vis."','".$enc."','".$fvis2[$i]."','A-magenta' )";
							$err=mysql_query($q1,$conex);
						}else
						{
							$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac,  repfac, repvis, repenc, repfvi, Seguridad) ";
							$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$array2[$i][0]."', '".$array2[$i][1]."', '".$array2[$i][2]."', '".$array2[$i][3]."', '".$array2[$i][4]."',  '".$array2[$i][5]."', 'VIP', '".$array2[$i][15]."','".$array2[$i][16]."', '".$array2[$i][12]."',  '".$resulta[1]."', '".$resulta[2]."', '".$vis."','".$enc."','".$fvis2[$i]."','A-magenta' )";
							$err=mysql_query($q1,$conex);
						}
					}
					else
					{
						$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac, repfac, repvis, repenc, repfvi, Seguridad) ";
						$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$array2[$i][0]."', '".$array2[$i][1]."', '".$array2[$i][2]."', '".$array2[$i][3]."', '".$array2[$i][4]."',  '".$array2[$i][5]."', '".$array2[$i][7]."', '".$array2[$i][15]."', '".$array2[$i][16]."', '".$array2[$i][12]."', '".$resulta[1]."', '".$resulta[2]."', '".$vis."','".$enc."','".$fvis2[$i]."','A-magenta' )";
						$err=mysql_query($q1,$conex);
					}

				}
				else
				{
					$q1="update magenta_000014 set repvis='".$vis."', repenc='".$enc."', repfvi='".$fvis2[$i]."' where repdoc='".$array2[$i][0]."' and reping ='".$array2[$i][3]."' ";
					$err=mysql_query($q1,$conex);
				}
			}
		}

		if ($k1 !=0)
		{
			$m=$k1-1;
			$array2=ordenador (3, $array2, $m, 14);
			$array2=ordenador2  (5, 3, $array2, $m, 14);
			$array2=verAct ($array2, 14);

			$size1=count($array2);

			ECHO "<table border=0 align=center size=100%>";
			ECHO "<tr><td align=center ><font size=3 color='blue'>Número de Visitas BBB: $size1</font></td></tr>";
			ECHO "</table></br>";


			ECHO '<TABLE border=1 align=center>';
			ECHO "<Tr class='encabezadotabla'>";

			echo "<td align=center><font size=2>Documento</font></td>";
			echo "<td align=center><font size=2>Nº Historia</font></td>";
			echo "<td align=center width=15%><font size=2>Nombre</font></td>";
			echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
			echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
			echo "<td align=center width=5%><font size=2>Hora de Ingreso</font></td>";
			echo "<td align=center width=10%><font size=2>Servicio Actual</font></td>";
			echo "<td align=center width=5%><font size=2>Habitación</font></td>";
			echo "<td align=center><font size=2>Amb.</font></td>";
			echo "<td align=center><font size=2>Actualizado</font></td>";
			echo "<td align=center><font size=2>Visitado</font></td>";
			echo "<td align=center><font size=2>No encontrado</font></td>";
			echo "<td align=center><font size=2>Último contacto</font></td>";
			echo "<td align=center width=15%><font size=2>Actualizado por</font></td>";
			echo "<td align=center><font size=2>Inf. Ok</font></td>";

			ECHO "</Tr >";
			for ($i=0; $i<$size1; $i++)
			{
				$doc=trim($array2[$i][13]);
				$tipDoc=trim($array2[$i][14]);
				switch ($tipDoc)
				{
					case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
					break;
					case "TI": $tipDoc="TI-Tarjeta de Identidad";
					break;
					case "NU": $tipDoc="NU-Numero Unico de Identificación";
					break;
					case "CE": $tipDoc="CE-Cedula de Extrangeria";
					break;
					case "PA": $tipDoc="PA-Pasaporte";
					break;
					case "RC": $tipDoc="RC-Registro Civil";
					break;
					case "AS": $tipDoc="AS-Adulto Sin Identificación";
					break;
					case "MS": $tipDoc="MS-Menor Sin Identificación";
					break;
				}

				if (is_int ($i/2))
				$color='fila1';
				else
				$color='fila2';

				ECHO "<Tr >";
				echo "<td  class='$color' align=center><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."&cco=2050' target='_blank'><font size=2>".$array2[$i][0]."</font></a></td>";
				for ($j=1; $j<6; $j++)
				{
					echo "<td class='$color' align=center><font size=2>".$array2[$i][$j].'</font></td>';
				}

				switch ($array2[$i][12])
				{
					case 0:
					echo "<td class='$color' align=center ><font size=2>".$array2[$i][6].'</font></td>';
					echo "<td class='$color' align=center >&nbsp;</td>";
					echo "<td class='$color'><input type='checkbox' name='Ambulatorio'  checked ></td>";
					break;
					case 1:
					$query="select A.traser, A.trahab, B.sernom    ";
					$query= $query."from inmtra A , inser B ";
					$query= $query."where A.trahis='".$array2[$i][1]."' and A.tranum='".$array2[$i][8]."' and A.traegr is null ";
					$query= $query."and B.sercod=A.traser ";

					$err_o = odbc_exec($conex_o,$query);
					$resulta=odbc_result($err_o,3);
					echo "<td class='$color' align=center><font size=2>".$resulta."</font ></td>";
					$resulta=odbc_result($err_o,2);
					echo "<td class='$color' align=center><font size=2>".$resulta."</font ></td>";
					echo "<td class='$color'><input type='checkbox' name='Ambulatorio'></td>";
					break;
				}

				switch ($array2[$i][15])
				{
					case 0:
					echo "<td class='$color'><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$array2[$i][17]."</font></td>";
					break;
					case 1:
					echo "<td class='$color'><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$array2[$i][17]."</font></td>";
					break;
					case 2:
					echo "<td class='$color'><input type='checkbox' name='Actualizado' checked >&nbsp;<font size=2>".$array2[$i][17]."</font></td>";
					break;
				}

				$query ="SELECT repvis, repenc FROM magenta_000014 where repdoc='".$array2[$i][0]."' and reping='".$array2[$i][3]."' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores
				{
					$rol= mysql_fetch_row($err);
					if ($rol[0]=='on')
					$rol[0]='checked';
					if ($rol[1]=='on')
					$rol[1]='checked';
					echo "<td class='$color'><input type='checkbox' name='visitado2[".$i."]'  ".$rol[0]." ><a href='/Matrix/Magenta/reportes/rep_segvisita.php?docume=".$doc." ".$tipDoc."' target='_blank'><font size=2>Consultar</font></a></td>";
					echo "<td class='$color'><input type='checkbox' name='encontrado2[".$i."]'  ".$rol[1]." ></td>";
				}
				else
				{
					echo "<td class='$color'><input type='checkbox' name='visitado2[".$i."]' ><a href='/Matrix/Magenta/reportes/rep_segvisita.php?docume=".$doc." ".$tipDoc."' target='_blank'><font size=2>Consultar</font></a></td>";
					echo "<td class='$color'><input type='checkbox' name='encontrado2[".$i."]' ></td>";
				}

				$query ="SELECT repfvi FROM magenta_000014 where repdoc='".$array2[$i][0]."' and rephis='".$array2[$i][1]."' order by repfvi desc";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores
				{
					$rol= mysql_fetch_row($err);
					if ($rol[0]=='0000-00-00')
					echo "<td class='$color' align=center ><font size=2>-</font></td>";
					else
					echo "<td class='$color' align=center ><font size=2>".$rol[0]."</font></td>";
					echo "<input type='hidden' name='fvis2[".$i."]'  value='".$rol[0]."' >";
				}
				else
				{
					echo "<td class='$color' align=center ><font size=2>-</font></td>";
					echo "<input type='hidden' name='fvis2[".$i."]'  value='' >";
				}


				echo "<td class='$color' align=center ><font size=2>".$array2[$i][18].'</font></td>';

				switch ($array2[$i][16])
				{
					case 0:
					echo "<td class='$color'><input type='checkbox' name='Datos' checked ></td>";
					break;
					case 1:
					echo "<td class='$color'><input type='checkbox' name='Datos'></td>";
					break;
				}


				ECHO "</Tr >";


			}
			echo "</table>";
		}ELSE
		{
			//echo "<table>";
			echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
			echo "<tr><td colspan='2'><font size=3  face='arial'><b>Ningún usuario BBB ha ingresado el día de hoy</td><tr>";
			echo "</fieldset>";
		}


		//////////////////////////////////////////////////////////Fin selección de clientes BBB////////////////////////////
		//////////////////////////////////////////////////////////Proceso en Batch////////////////////////////////////////
		if (isset ($array))
		{
			$size1=count($array);
		}else
		{
			$size1=0;
			ECHO "<input type='hidden' name='vect1[0][0]' value='sin datos'>";
		}
		if (isset ($array2))
		{
			$size2=count($array2);
		}else
		{
			$size2=0;
			ECHO "<input type='hidden' name='vect2[0][0]' value='sin datos'>";
		}


		for ($x=0; $x<$size1; $x++)
		{
			for ($y=0; $y<17; $y++)
			{
				ECHO '<input type="hidden" name="vect1['.$x.']['.$y.']" value="'.$array[$x][$y].'">';
				ECHO '<input type="hidden" name="array['.$x.']['.$y.']" value="'.$array[$x][$y].'">';

			}
		}
		for ($x=0; $x<$size2; $x++)
		{
			for ($y=0; $y<17; $y++)
			{
				ECHO '<input type="hidden" name="vect2['.$x.']['.$y.']" value="'.$array2[$x][$y].'">';
				ECHO '<input type="hidden" name="array2['.$x.']['.$y.']" value="'.$array2[$x][$y].'">';

			}
		}

		ECHO "<input type='hidden' name='k' value='".$size1."'>";
		ECHO "<input type='hidden' name='k1' value='".$size2."'>";

		ECHO '<input type="hidden" name="actualizar" value="1">';

		echo "</BR></BR><CENTER><font size='2'  align=center face='arial'><input type='button' name='aceptar2' value='GUARDAR' onclick='javascript:guardar()'></CENTER>";

		echo "</br></br><CENTER><fieldset ></br>";
		echo "<tr><td align=center colspan=2><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='PROCESO DE ACTUALIZACIÓN' ></td></tr>";
		echo "</fieldset>";
		echo "</form>";

		//////////////////////////////////////////////////////////Fin selección de clientes BBB////////////////////////////

	}else
	{
		echo "ERROR : "."$errstr ($errno)<br>\n";
	}

	//Cerrar conexiones
	
	odbc_close_all();
}
?>