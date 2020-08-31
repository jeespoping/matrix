<html>
<head>
<title>AFINIDAD</title>
</head>
<body >

<?php
include_once("conex.php");

/**
 * 	VISITAS DE AFINIDAD VIP
 * 
 * Este es un programa que muestra las visitas VIP del día, fecha de ingreso, egreso, lugar donde se encuentran etc
 * ademas tiene enlace para revisar y gestionar los datos del paciente a persona.php. 
 * 
 * @name  matrix\magenta\procesos\visitasComunic.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-02-10
 * @version 2007-01-23
 * 
 * @modified  2007-01-23 documentacion
 Actualizacion: Se colocaron union en algunas de las consultas a unix, por si traen campos nulos
				Se cambiaron las rutas de la imagen de VIP.gif y del link que lleva a Magenta.php 
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
 * 
 *  @wvar $amb indica que la estadia en hospitalizacion si esta en 1, es un vector que tiene la info de cada paciente
 *  @wvar $ape1 apellido del paciente
 *  @wvar $ape2 apellido 2 del paciente
 *  @wvar $array, vector que guarda toda la info del paciente vip
 *  @wvar $bar barrio del paciente
 *  @wvar $ced, cedula del paciente 
 *  @wvar $cedTip tipo de cedula del paciente
 *  @wvar $codRes, reponsable de la facturacion del ingreso
 *  @wvar $color colores de depliegue de datos
 *  @wvar $diaNom diagnostico de ingreso
 *  @wvar $doc documento del paciente
 *  @wvar $fecEgr fecha de egreso del paciente
 *  @wvar $fecha fecha para acotar la busqeuda de los AAA, en fecha de nacimiento inicial
 *  @wvar $fecha2 fecha para acotar la busqeuda de los AAA, en fecha de nacimiento final
 *  @wvar $fecIng fecha de ingreso del paciente
 *  @wvar $fecNac fecha de nacimiento
 *  @wvar $fuente de donde se saco la info del paciente (aymov, inpac o impaci)
 *  @wvar $his historia clinica del paciente
 *  @wvar $horEgr hora de egreso
 *  @wvar $horIng hora de ingreso
 *  @wvar $hoy fecha de hoy en formato unix
 *  @wvar $ingresos cantidad de ingresos encontrados entre las dos fechas
 *  @wvar $m, desde que numero empieza el vector de pacientes
 *  @wvar $nom nombre del pacinete
 *  @wvar $numIng numero de ingreso del paciente
 *  @wvar $pacHos indica si esta hospitalizado (H) o no (C o A)
 *  @wvar $serAct, servicio alctual donde se encuentra el pacinete, o ultimo del que salio 
 *  @wvar $serNom nombre del servicio de ingreso
 *  @wvar $sex sexo del paciente
 *  @wvar $size1 tamaño del vector de la info del paciente
 *  @wvar $tipDoc, tipo de documento del paciente
 *  @wvar $tipUsu, tipo de usuario que es el paciente, me dice si si es AAA o si si es BBB
 *  @wvar $zona, zona en la que vive el paciente
*/

$wautor="Carolina Castano P.";
$wversion='2007-01-23';

/****************************PROGRAMA************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;

	/**
	 * conexion con matrix
	 */
	

	

	$bd='facturacion';

	/**
	 * conexion con unix
	 */
	include_once("magenta/socket2.php");
	//entro a programa cuando la conexión se realizó

	if($conex_o != false)
	{
		/**
		 * funciones que se llaman en este programa
		 */
		include_once("magenta/incVisitas.php");
		$hoy=date('Y/m/d');
$long='                                    ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal  ya estaba en comentario
		//////////////////////////////////////////////////////////Busqueda principal en db////////////////////////////
		
		$i=0;

		//Busco inicialmente en inpac quienes han venido hoy y que no hayan sido facturados para asegurar que estan aca
		$table=date("Mdhis");	
				
		$query="select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom, F.movfue as movfue  ";
		$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E, Outer famov F ";
		$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		$query= $query."and C.sercod=A.pacser ";
		$query= $query."and E.diacod=A.pacdin  ";
		$query= $query."and F.movhis=A.pachis and F.movnum=A.pacnum and F.movfue='20' ";
		$query= $query."and F.movfue is not null ";
		$query= $query."and A.pacap2 is not null ";
		$query= $query."and A.pacfec='$hoy' ";
		$query= $query." UNION ";
		$query= $query."select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, '.' as pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom, ' ' as movfue  ";
		$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E, Outer famov F ";
		$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		$query= $query."and C.sercod=A.pacser ";
		$query= $query."and E.diacod=A.pacdin  ";
		$query= $query."and F.movhis=A.pachis and F.movnum=A.pacnum and F.movfue='20' ";
		$query= $query."and F.movfue is null ";
		$query= $query."and A.pacap2 is null ";
		$query= $query."and A.pacfec='$hoy' ";
		$query= $query." UNION ";
		$query= $query."select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, '.' as pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom, F.movfue as movfue  ";
		$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E, Outer famov F ";
		$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		$query= $query."and C.sercod=A.pacser ";
		$query= $query."and E.diacod=A.pacdin ";
		$query= $query."and F.movhis=A.pachis and F.movnum=A.pacnum and F.movfue='20' ";
		$query= $query."and F.movfue is not null ";
		$query= $query."and A.pacap2 is null ";
		$query= $query."and A.pacfec='$hoy' ";
		$query= $query." UNION ";
		$query= $query."select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom, ' ' as movfue  ";
		$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E, Outer famov F ";
		$query= $query."where B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
		$query= $query."and C.sercod=A.pacser ";
		$query= $query."and E.diacod=A.pacdin  ";
		$query= $query."and F.movhis=A.pachis and F.movnum=A.pacnum and F.movfue='20' ";
		$query= $query."and F.movfue is null ";
		$query= $query."and A.pacap2 is not null ";
		$query= $query."and A.pacfec='$hoy' ";		
		$query= $query."into temp $table ";

		$err_o = odbc_exec($conex_o,$query);

		$query="select * from ".$table." ";
		$query= $query."where movfue is Null or movfue = ' ' ";
			
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

																		
		//búsco en aymov, pueden haber venido hoy
		
			
			$query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
			$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E ";
			$query= $query."where movfec='$hoy'";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."and E.diacod=A.movdia ";
			$query= $query."and A.movap2 is not null ";
			$query= $query."and A.movnum is not null ";
			$query= $query." union ";
			$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
			$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E ";
			$query= $query."where movfec='$hoy'";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."and E.diacod=A.movdia ";
			$query= $query."and A.movap2 is null ";
			$query= $query."and A.movnum is null ";
			$query= $query." union ";
			$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
			$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E ";
			$query= $query."where movfec='$hoy'";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."and E.diacod=A.movdia ";
			$query= $query."and A.movap2 is not null ";
			$query= $query."and A.movnum is null ";
			$query= $query." union ";
			$query= $query."select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom, E.dianom   ";
			$query= $query."from aymov A , aymovotr B, Outer inser C, Outer india E ";
			$query= $query."where movfec='$hoy'";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."and E.diacod=A.movdia ";
			$query= $query."and A.movap2 is null ";
			$query= $query."and A.movnum is not null ";
			$query= $query." order by 3 ";
			
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

				
			$query="select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom, E.dianom ";
			$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D, OUTER india E ";
			$query= $query."WHERE D.egring='$hoy' and D.egregr='$hoy' ";
			$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
			$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
			$query= $query."and C.sercod=D.egrsin ";
			$query= $query."and E.diacod=D.egrdin ";
			$query= $query."and A.pacap2 is not null ";
			$query= $query." union ";
			$query= $query."select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, '.' as pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom, E.dianom ";
			$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D, OUTER india E ";
			$query= $query."WHERE D.egring='$hoy' and D.egregr='$hoy' ";
			$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
			$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
			$query= $query."and C.sercod=D.egrsin ";
			$query= $query."and E.diacod=D.egrdin ";
			$query= $query."and A.pacap2 is null ";
			$query= $query."order by 6 ";
		
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
		//echo $i;
		$ingresos= $i;


		//////////////////////////////////////////////////////////Fin Busqueda principal en db////////////////////////////

		//////////////////////////////////////////////////////////selección de clientes VIP AAA////////////////////////////


		ECHO "<table border=0 align=center size=100%>";
		ECHO "<tr><td align=center ><A HREF='visitasComunic.php'><font size=4 color='#FF00FF'>LISTA DE VISITAS DE HOY: $hoy </font></a></td></tr>";
		ECHO "<tr><td align=center ><A NAME='Arriba'><font size=2 color='#FF00FF'> visitasComunic.php</font></a></td></tr>";
		ECHO "</table></br>";
		if ($i==0)
		{
			echo '<script language="Javascript">';
			echo 'window.location.href=window.location.href;';
			echo 'alert ("No se encontró información en el rango de fechas ingresado, intente nuevamente por favor ")';
			echo '</script>';
		}

		$k=0;
		echo "<center><img SRC='/matrix/images/medical/Magenta/VIP2.gif'></center></br>";


		for ($j=0; $j<$ingresos; $j++)
		{
			$zona=Zona($bar[$j]);
			$tipUsu= esVIP ($ced[$j], $cedTip[$j], $codRes [$j] );

			If ($tipUsu==1)
			{
				$tipUsu='VIP';
			}

			if (strcmp ($tipUsu,'VIP')==0)
			{
				$array[$k][0]=$ced[$j].$cedTip[$j];
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


		if ($k !=0)
		{
			$m=$k-1;
			$array=ordenador (3, $array, $m, 14);
			//$array=ordenador2  (5, 3, $array, $m, 14);
			$array=verActVIP ($array, 14);
			$size1=count($array);

			ECHO "<table border=0 align=center size=100%>";
			ECHO "<tr><td align=center ><font size=3 color='#FF00FF'>Número de Visitas VIP: $size1</font></td></tr>";
			ECHO "</table></br>";


			ECHO '<TABLE border=1 align=center>';
			ECHO "<Tr >";

			echo "<td  bgcolor='#ADD8E6' align=center><font size=2>Documento</font></td>";
			echo "<td  bgcolor='#ADD8E6'align=center><font size=2>Nº Historia</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center width=15%><font size=2>Nombre</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center><font size=2>Fecha de ingreso</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center width=10%><font size=2>Unidad de ingreso</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center width=5%><font size=2>Hora de Ingreso</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center width=10%><font size=2>Servicio Actual</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center width=10%><font size=2>Hab.</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center><font size=2>Amb.</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center><font size=2><font size=2>Actualizado</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center width=15%><font size=2>Actualizado por</font></td>";
			echo "<td bgcolor='#ADD8E6'align=center><font size=2>Inf. Ok</font></td>";

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
				$color='#EOFFFF';
				else
				$color='#cccccc';

				ECHO "<Tr >";
				echo "<td  bgcolor='$color' align=center><a href='Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."' target='_blank'><font size=2>".$array[$i][0]."</font></a></td>";
				for ($j=1; $j<6; $j++)
				{
					echo "<td bgcolor='$color' align=center><font size=2>".$array[$i][$j].'</font></td>';
				}

				switch ($array[$i][12])
				{
					case 0:
					echo "<td bgcolor='$color' align=center ><font size=2>".$array[$i][6].'</font></td>';
					echo "<td bgcolor='$color' align=center >&nbsp;</td>";
					echo "<td bgcolor='$color'><input type='checkbox' name='Ambulatorio'  checked ></td>";
					break;
					case 1:
					$query="select A.traser, A.trahab, B.sernom    ";
					$query= $query."from inmtra A , inser B ";
					$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][8]."' and A.traegr is null ";
					$query= $query."and B.sercod=A.traser ";

					$err_o = odbc_exec($conex_o,$query);
					$resulta=odbc_result($err_o,3);
					echo "<td bgcolor='$color' align=center><font size=2>".$resulta."</font ></td>";
					$resulta=odbc_result($err_o,2);
					echo "<td bgcolor='$color' align=center><font size=2>".$resulta."</font ></td>";
					echo "<td bgcolor='$color'><input type='checkbox' name='Ambulatorio'></td>";
					break;
				}

				switch ($array[$i][15])
				{
					case 0:
					echo "<td bgcolor='$color'><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$array[$i][17]."</font ></td>";
					break;
					case 1:
					echo "<td bgcolor='$color'><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$array[$i][17]."</font ></td>";
					break;
					case 2:
					echo "<td bgcolor='$color'><input type='checkbox' name='Actualizado' checked >&nbsp;<font size=2>".$array[$i][17]."</font ></td>";
					break;
				}

				echo "<td bgcolor='$color' align=center><font size=2>".$array[$i][18].'</font ></td>';


				switch ($array[$i][16])
				{
					case 0:
					echo "<td bgcolor='$color'><input type='checkbox' name='Datos' checked ></td>";
					break;
					case 1:
					echo "<td bgcolor='$color'><input type='checkbox' name='Datos'></td>";
					break;
				}

				ECHO "</Tr >";
			}

			echo "</table>";
			echo "<br><br><br>";
		}ELSE
		{	//echo "<table>";
			echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
			echo "<tr><td colspan='2'><font size=3  face='arial'><b>Ningún paciente VIP ha ingresado el día de hoy</td><tr>";
			echo "</fieldset>";
		}

		//////////////////////////////////////////////////////////Fin selección de clientes AAA////////////////////////////



	}else
	{
		echo "ERROR : "."$errstr ($errno)<br>\n";
	}



	//Cerrar conexiones
		odbc_close_all();
}
?>