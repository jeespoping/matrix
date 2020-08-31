
<?php
include_once("conex.php");

/**
 * 	PROCESO EN BATCH
 * 
 * Este es un programa que actualiza a las personas que no fueron actualizadas hace mas de un año y que guarda a todo los afines y VIP que vinieron desde 3 dias antes como visitantes en la bd
 * es importante que se haga diariamente durante los dias habiles 
 * 
 * @name  matrix\magenta\procesos\batch.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-02-10
 * @version 2007-01-23
 * 
 * @modified  2007-01-23 Se realiza documentacion
 * 
 * @table inpac, select
 * @table inpacotr, select 
 * @table inser, select, 
 * @table india, select,
 * @table indep, select
 * @table inmun, select
 * @table inmtra, select
 * @table aymov, select
 * @table aymovotr, select
 * @table inmegr, select
 * @table inpaci, select
 * @table inpacinf, select
 * @table inocu, select
 * @table magenta_000014, select, insert, 
 * @table magenta_000008, select, inser, update
 * @table magenta_000009, select, inser, update
 * 
 *  @wvar $amb indica que la estadia en hospitalizacion si esta en 1, es un vector que tiene la info de cada paciente
 *  @wvar $ape1 apellido del paciente
 *  @wvar $ape2 apellido 2 del paciente
 *  @wvar $array, vector que guarda toda la info del paciente AAA 
 *  @wvar $array2, vector que guarda toda la info del paciente BBB 
 *  @wvar $bar barrio del paciente
 *  @wvar $ced, cedula del paciente 
 *  @wvar $cedTip tipo de cedula del paciente
 *  @wvar $codRes, reponsable de la facturacion del ingreso
 *  @wvar $diaNom diagnostico de ingreso
 *  @wvar $fecEgr fecha de egreso del paciente
 *  @wvar $fecIni fecha inicial para la busqueda de los afines de dos dias antes
 *  @wvar $fecFin fecha final para la busqueda de los afines de dos dias antes
 *  @wvar $fecIng fecha de ingreso del paciente
 *  @wvar $fecNac fecha de nacimiento
 *  @wvar $frecVis frecuencia de visita (cuantas veces ha vendio en los ultimos 6 años)
 *  @wvar $fuente de donde se saco la info del paciente (aymov, inpac o impaci)
 *  @wvar $his historia clinica del paciente
 *  @wvar $horEgr hora de egreso
 *  @wvar $horIng hora de ingreso
 *  @wvar $ingresos cantidad de ingresos encontrados entre las dos fechas
 *  @wvar $nom nombre del pacinete
 *  @wvar $numIng numero de ingreso del paciente
 *  @wvar $pacHos indica si esta hospitalizado (H) o no (C o A)
 *  @wvar $repetido, indica si ya estaba el BBB en la lista
 *  @wvar $serAct, servicio alctual donde se encuentra el pacinete, o ultimo del que salio 
 *  @wvar $serNom nombre del servicio de ingreso
 *  @wvar $sex sexo del paciente
 *  @wvar $size1 tamaño del vector de la info del paciente
 *  @wvar $tiempo, sirve para ajustar las fechas para la busqueda de los afines de dos dias antes
 *  @wvar $tipUsu, tipo de usuario que es el paciente, me dice si si es AAA o si si es BBB
 *  @wvar $vect1 vector con todos los datos de los paciente que se encuentran y que son AAA
 *  @wvar $vect2 vector con todos los datos de los paciente que se encuentran y que son BBB
 *  @wvar $zona, zona en la que vive el paciente
*/

$wautor="Carolina Castano P.";
$wversion='2007-01-23';
//=================================================================================================================================

/**
 * Función que devuelve la candena si la variable $var no está vacía o con -, si lo está entonces dependiendo el valor de la variable $tip devuelve 00 o Dato no encontrado
 *
 * @param unknown_type $tip
 * @param unknown_type $var
 * @return unknown
 */
function  Vacio($tip,$var)
{
	//Si la variable esta  vacia retorna "Dato No Encontrado"
	if(trim($var) == "" or trim($var) == "0"){
		if($tip==0){
			return ("00");
		}else{
			return("Dato No Encontrado");
		}
	}else{
		return($var);
	}
}


/**
 * proceso de batch que actualiza las personas que no fueron actualizadas
 *
 * @param unknown_type $vector
 */
function actualizar ($vector)
{
	

	


	$bd='facturacion';
	include_once("magenta/socket2.php");
	//include_once("magenta/incVisitas.php");   //se agrego
	
	$size1=count($vector);

	for ($i=0; $i<$size1; $i++)
	{

		///////////////sacar los datos de alguna de las fuentes: Inpac, inpaci, aymov///////////////////

		if ($vector[$i][15]==1 or $vector[$i][15]==0)//SIGNIFICA QUE ESTA DESACTUALIZADO
		{

			if ($vector[$i][10]=='inpac')
			{
				/*$query="select A.pacnom, A.pacap1, A.pacap2, A.paclug, A.pacsex, A.pacest, A.pactel, A.pacdir, A.pacmun, A.paccer, B.munnom, B.mundep,  C.pacinfcoc, C.pacinfocu  ";
				$query= $query."from inpac A , OUTER inmun B, OUTER inpacinf C  ";
				$query= $query."where A.pachis='".$vector[$i][1]."' and A.pacnum='".$vector[$i][8]."' ";
				$query= $query."and B.muncod= A.pacmun  ";
				$query= $query."and C.pacinfhis=A.pachis and C.pacinfnum=A.pacnum ";*/
				
				$query="select A.pachis, A.pacnom, A.pacap1, A.pacap2, A.paclug, A.pacsex, A.pacest, A.pactel, A.pacdir, A.pacmun, A.paccer, B.munnom, B.mundep,  C.pacinfcoc, C.pacinfocu  ";
				$query= $query."from inpac A , OUTER inmun B, OUTER inpacinf C  ";
				$query= $query."where A.pachis='".$vector[$i][1]."' and A.pacnum='".$vector[$i][8]."' ";
				$query= $query."and B.muncod= A.pacmun  ";
				$query= $query."and A.pacap2 is not null ";
				$query= $query."and C.pacinfhis=A.pachis and C.pacinfnum=A.pacnum ";
				$query= $query." union ";
				$query= $query."select A.pachis, A.pacnom, A.pacap1, '.' as pacap2, A.paclug, A.pacsex, A.pacest, A.pactel, A.pacdir, A.pacmun, A.paccer, B.munnom, B.mundep,  C.pacinfcoc, C.pacinfocu  ";
				$query= $query."from inpac A , OUTER inmun B, OUTER inpacinf C  ";
				$query= $query."where A.pachis='".$vector[$i][1]."' and A.pacnum='".$vector[$i][8]."' ";
				$query= $query."and A.pacap2 is null ";
				$query= $query."and B.muncod= A.pacmun  ";
				$query= $query."and C.pacinfhis=A.pachis and C.pacinfnum=A.pacnum "; 

			}

			if ($vector[$i][10]=='aymov')
			{
				/*$query="select A.movnom, A.movape, A.movap2, A.movlug, A.movsex, A.movres, A.movtel, A.movdir, A.movmun, A.movcer, B.munnom, B.mundep,  C.movotrocu ";
				$query= $query."from aymov A , OUTER inmun B, OUTER aymovotr C ";
				$query= $query."where A.movdoc='".$vector[$i][1]."' and A.movfue='".$vector[$i][8]."' ";
				$query= $query."and B.muncod= A.movmun  ";
				$query= $query."and C.movotrfue=A.movfue and C.movotrdoc=A.movdoc ";*/
				
				$query="select A.movnom, A.movape, A.movap2, A.movlug, A.movsex, A.movres, A.movtel, A.movdir, A.movmun, A.movcer, B.munnom, B.mundep,  C.movotrocu ";
				$query= $query."from aymov A , OUTER inmun B, OUTER aymovotr C ";
				$query= $query."where A.movdoc='".$vector[$i][1]."' and A.movfue='".$vector[$i][8]."' ";
				$query= $query."and B.muncod= A.movmun  ";
				$query= $query."and A.movap2 is not null  ";
				$query= $query."and C.movotrfue=A.movfue and C.movotrdoc=A.movdoc ";
				$query= $query." union ";
				$query= $query."select A.movnom, A.movape, '.' as movap2, A.movlug, A.movsex, A.movres, A.movtel, A.movdir, A.movmun, A.movcer, B.munnom, B.mundep,  C.movotrocu ";
				$query= $query."from aymov A , OUTER inmun B, OUTER aymovotr C ";
				$query= $query."where A.movdoc='".$vector[$i][1]."' and A.movfue='".$vector[$i][8]."' ";
				$query= $query."and B.muncod= A.movmun  ";
				$query= $query."and A.movap2 is null  ";
				$query= $query."and C.movotrfue=A.movfue and C.movotrdoc=A.movdoc ";
			}

			if ($vector[$i][10]=='inpaci')
			{
				/*$query="select A.pacnom, A.pacap1, A.pacap2, A.paclug, A.pacsex, A.pacest, A.pactel, A.pacdir, A.pacmun, D.egrcer, B.munnom,   B.mundep,  C.pacinfcoc, C.pacinfocu  ";
				$query= $query."from inpaci A , inmegr D, OUTER inmun B, OUTER inpacinf C  ";
				$query= $query."where A.pachis='".$vector[$i][1]."' and A.pacnum='".$vector[$i][8]."' ";
				$query= $query."and B.muncod= A.pacmun  ";
				$query= $query."and C.pacinfhis=A.pachis and C.pacinfnum=A.pacnum ";
				$query= $query."and D.egrhis='".$vector[$i][1]."' and D.egrnum='".$vector[$i][8]."' ";*/
				
				$query="select A.pacnom, A.pacap1, A.pacap2, A.paclug, A.pacsex, A.pacest, A.pactel, A.pacdir, A.pacmun, D.egrcer, B.munnom,   B.mundep,  C.pacinfcoc, C.pacinfocu  ";
				$query= $query."from inpaci A , inmegr D, OUTER inmun B, OUTER inpacinf C  ";
				$query= $query."where A.pachis='".$vector[$i][1]."' and A.pacnum='".$vector[$i][8]."' ";
				$query= $query."and B.muncod= A.pacmun  ";
				$query= $query."and A.pacap2 is not null  ";
				$query= $query."and C.pacinfhis=A.pachis and C.pacinfnum=A.pacnum ";
				$query= $query."and D.egrhis='".$vector[$i][1]."' and D.egrnum='".$vector[$i][8]."' ";
				$query= $query." union ";
				$query="select A.pacnom, A.pacap1, '.' as pacap2, A.paclug, A.pacsex, A.pacest, A.pactel, A.pacdir, A.pacmun, D.egrcer, B.munnom,   B.mundep,  C.pacinfcoc, C.pacinfocu  ";
				$query= $query."from inpaci A , inmegr D, OUTER inmun B, OUTER inpacinf C  ";
				$query= $query."where A.pachis='".$vector[$i][1]."' and A.pacnum='".$vector[$i][8]."' ";
				$query= $query."and B.muncod= A.pacmun  ";
				$query= $query."and A.pacap2 is null  ";
				$query= $query."and C.pacinfhis=A.pachis and C.pacinfnum=A.pacnum ";
				$query= $query."and D.egrhis='".$vector[$i][1]."' and D.egrnum='".$vector[$i][8]."' ";

			}

			//$bd='facturacion';
			//include_once("magenta/socket2.php");
			$err_o = odbc_exec($conex_o,$query);

			if (odbc_fetch_row($err_o))
			{

				for($j=1;$j<=odbc_num_fields($err_o);$j++)
				{
					$array[$j]=odbc_result($err_o,$j);
				}


				///////////////llenado de variables consultadas en Unix para pasar a Matrix ///////////////

				//llenado de variables

				$nombres=	Vacio(1,$array[1]);
				$ape1=		Vacio(1,$array[2]);
				$ape2=		Vacio(1,$array[3]);
				$fecNac=	Vacio(1,$vector[$i][11]);

				$query="select munnom ";
				$query= $query."from inmun ";
				$query= $query."where muncod='".$array[4]."' ";
				$err_o = odbc_exec($conex_o,$query);
				$lugNac=odbc_result($err_o,1);
				$lugNac=	Vacio(1,$array[4]."-".$lugNac);


				$sexo=Vacio(1,$array[5]);

				switch ($sexo)
				{
					case "M": $sexo="M-MASCULINO";
					break;
					case "F": $sexo="F-FEMENINO";
					break;
					default: $sexo="00-Dato No Encontrado";
					break;
				}

				if($vector[$i][10] != "aymov")
				{
					$estCivil=Vacio(1,$array[6]);
					switch ($estCivil)
					{
						case "S": $estCivil="S-SOLTERO (A)";
						break;
						case "C": $estCivil="C-CASADO (A)";
						break;
						case "D": $estCivil="D-DIVORCIADO (A)";
						break;
						case "M": $estCivil="M-MENOR";
						break;
						case "U": $estCivil="U-UNION LIBRE";
						break;
						case "V": $estCivil="V-VIUDO (A)";
						break;
						default:$estCivil="00-Dato No Encontrado";
						break;
					}
				}else
				{
					$estCivil="Dato No Encontrado";
				}

				$nHijos=	"0";

				if($vector[$i][10] != "aymov")
				{
					$prof=Vacio(0,$array[13])."-".Vacio(1,$array[14]);
				}else
				{
					$query="select ocunom ";
					$query= $query."from inocu ";
					$query= $query."where ocucod='".$array[13]."' ";
					$err_o = odbc_exec($conex_o,$query);
					$prof=odbc_result($err_o,1);
					$prof=Vacio(0,$array[13])."-".Vacio(1,$prof);

				}

				$tel1=		Vacio(1,$array[7]);
				$tel2=		"Dato No Encontrado";
				$movil=		"Dato No Encontrado";
				$email1=	"Dato No Encontrado";
				$email2=	"Dato No Encontrado";
				$dir=		Vacio(1,$array[8]);
				$estrato=	"0";
				$zona=		Vacio(1,$vector[$i][9]);

				$municipio=	Vacio(1,$array[9]."-".$array[11]);

				$query="select depnom ";
				$query= $query."from indep ";
				$query= $query."where depcod='".$array[12]."' ";
				$err_o = odbc_exec($conex_o,$query);
				$dept= odbc_result($err_o,1);
				$dept=Vacio(1,$dept);

				$pais=		"Dato No Encontrado";

				$tipUsu1=Vacio(1,$vector[$i][7]);

				$doc=trim($vector[$i][13]);
				$tipDoc=trim($vector[$i][14]);
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



				$query="select clitip from magenta_000008 where clidoc='".$doc."' and clitid ='".$tipDoc."' ";
				$err=mysql_query($query,$conex);
				$num2=mysql_num_rows($err);

				if($num2 < 1)
				{
					$tipUsu=$tipUsu1;
				}else
				{
					$resulta = mysql_fetch_row($err);
					$exp=explode("-",$tipUsu1);
					$niv=$exp[2];
					$tip=$exp[0];
					$exp=explode("-",$resulta[0]);
					$niv2=$exp[2];
					$tip2=$exp[0];

					if ($tip[0]==$tip2[0] and $niv=='1' and $niv2=='2' and $tip2!='VIP')
					{
						$tipUsu=$tipUsu1;
					}else
					{
						$tipUsu=$resulta[0];
					}

				}

				$acompa=	"Dato No Encontrado";

				if($vector[$i][10] != "aymov")
				{
					$query="select empnom ";
					$query= $query."from inemp ";
					$query= $query."where empcod='".$array[10]."' ";
					$err_o = odbc_exec($conex_o,$query);
					$afiliado=odbc_result($err_o,1);
					$afiliado=Vacio(0,$array[10])."-".Vacio(1,$afiliado);

				}else
				{
					$afiliado=Vacio(0,$array[10])."-".Vacio(1,$array[6]);
				}

				$planC=	"00-Dato No Encontrado";
				$llamtel1=	"0";
				$llamtel2=	"0";
				$llamemai=	"0";
				$llammovi=	"0";
				$llamdire=	"0";

				$servicio=	"";
				$comBeb=	"off";
				$comFam=	"off";
				$comAdu=	"off";
				$comAdo=	"off";
				$comSnc=	"off";
				$comSrs=	"off";
				$comSrn=	"off";
				$comCan=	"off";
				$comSmu=	"off";
				$comCar=	"off";
				$comOtr=	"Dato No Encontrado";
				$gusLec=	"off";
				$gusApl=	"off";
				$gusAdr=	"off";
				$gusCin=	"off";
				$gusMus=	"off";
				$depFut=	"off";
				$depGol=	"off";
				$depTen=	"off";
				$depEqu=	"off";
				$depGym=	"off";
				$depOtr=	"Dato No Encontrado";
				$requer=	"Dato No Encontrado";
				$fam="";
				$requerGrab="";

				$vector[$i][13]=trim($vector[$i][13]);
				$vector[$i][14]=trim($vector[$i][14]);



				switch ($vector[$i][14])
				{
					case "CC": $vector[$i][14]="CC-CEDULA DE CIUDADANIA";
					break;
					case "TI": $vector[$i][14]="TI-Tarjeta de Identidad";
					break;
					case "NU": $vector[$i][14]="NU-Numero Unico de Identificación";
					break;
					case "CE": $vector[$i][14]="CE-Cedula de Extrangeria";
					break;
					case "PA": $vector[$i][14]="PA-Pasaporte";
					break;
					case "RC": $vector[$i][14]="RC-Registro Civil";
					break;
					case "AS": $vector[$i][14]="AS-Adulto Sin Identificación";
					break;
					case "MS": $vector[$i][14]="MS-Menor Sin Identificación";
					break;
				}



				///////////////llenado de variables consultadas en Unix para pasar a Matrix ///////////////

				if ($vector[$i][15]==1)

				{


					//Lleva un año si actualizar
					$q="update magenta_000008 set Clipac='Magenta',Clifac='".date("Y-m-d")."', Clinom='$nombres', Cliap1 ='$ape1', Cliap2='$ape2', Clifna='$fecNac', Clilna='$lugNac', Clisex='$sexo',  Clipro='$prof', Clite1='$tel1', Clidir='$dir',  Clizon='$zona', Climun='$municipio', Clidep='$dept', Clitip='$tipUsu', Clires='$afiliado' ";
					$q=$q."where Clidoc='".$vector[$i][13]."' and Clitid='".$vector[$i][14]."' ";
					$err=mysql_query($q,$conex);

					$q="update magenta_000009 set Cdepac='Magenta', Cdefac='".date("Y-m-d")."' ";
					$q=$q."where Cdedoc='".$vector[$i][13]."' and Cdetid='".$vector[$i][14]."' ";
					$err=mysql_query($q,$conex);
				}
				if ($vector[$i][15]==0)

				{


					//No ha sido actualizado
					//confirmo que ya no lo haya actualizado alguien

					$query="select clitip from magenta_000008 where Clidoc='".$vector[$i][13]."' and Clitid='".$vector[$i][14]."' ";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);
					if($num >=1)
					{
						$resulta=mysql_fetch_array($err);
						$parte1=explode ("-",$resulta['clitip']);
						$parte2=explode ("-",$tipUsu);
						$tipUsu=$parte1[0].'-'.$parte2[1].'-'.$parte2[2];

						//Ya lo habían actualizado hago update
						$q="update magenta_000008 set Clipac='Magenta',Clifac='".date("Y-m-d")."', Clinom='$nombres', Cliap1 ='$ape1', Cliap2='$ape2', Clifna='$fecNac', Clilna='$lugNac', Clisex='$sexo',  Clipro='$prof', Clite1='$tel1', Clidir='$dir',  Clizon='$zona', Climun='$municipio', Clidep='$dept', Clitip='$tipUsu', Clires='$afiliado' ";
						$q=$q."where Clidoc='".$vector[$i][13]."' and Clitid='".$vector[$i][14]."' ";
						$err=mysql_query($q,$conex);

						$q="update magenta_000009 set Cdepac='Magenta', Cdefac='".date("Y-m-d")."' ";
						$q=$q."where Cdedoc='".$vector[$i][13]."' and Cdetid='".$vector[$i][14]."' ";
						$err=mysql_query($q,$conex);

					}else
					{
						//Definitivamente no está, hago insert.
						$q="insert into magenta_000008 (medico, Fecha_data, Hora_data, Clipac, Clifac, Clidoc, Clitid, Clinom, Cliap1, Cliap2, Clifna, Clilna, Clisex, Clinhij, Cliciv, Clipro, Clite1, Clite2, Climov, Cliem1, Cliem2, Clidir, Clietr, Clizon, Climun, Clidep, Clipai, Clitip, Clires, Clicom, Cliaco, Clifam, Clilt1, Clilt2, Clilem, Clilmo, Clildi,Seguridad) ";
						$q= $q."values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','Magenta','".date("Y-m-d")."', '".$vector[$i][13]."', '".$vector[$i][14]."', '$nombres', '$ape1', '$ape2', '$fecNac', '$lugNac', '$sexo', $nHijos, '$estCivil', '$prof', '$tel1', '$tel2', '$movil' ,'$email1', '$email2', '$dir', '$estrato', '$zona', '$municipio', '$dept', '$pais', '$tipUsu', '$afiliado', '$planC','$acompa', '.', $llamtel1, $llamtel2, $llamemai, $llammovi, $llamdire, 'C-magenta')";

						$q1="insert into magenta_000009 (medico, Fecha_data, Hora_data,Cdepac, Cdefac, Cdedoc, Cdetid, Cdeser, Cdebeb, Cdefam, Cdeado, Cdeadu, Cdesnc, Cdesrs, Cdesrn, Cdesmu, Cdesca, Cdecan,  Cdeeot, Cdelec, Cdeapl, Cdeadr, Cdecin, Cdemus, Cdefut, Cdegol, Cdeten, Cdeequ, Cdegym, Cdedot, Cdereq, Seguridad)";
						$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','Magenta','".date("Y-m-d")."', '".$vector[$i][13]."', '".$vector[$i][14]."', '$servicio', '$comBeb', '$comFam',  '$comAdo', '$comAdu', '$comSnc', '$comSrs', '$comSrn', '$comSmu',  '$comCar',  '$comCan', '$comOtr', '$gusLec', '$gusApl', '$gusAdr', '$gusCin', '$gusMus', '$depFut', '$depGol', '$depTen', '$depEqu', '$depGym', '$depOtr', '$requerGrab', 'C-magenta')";
						$err=mysql_query($q,$conex);
						$err=mysql_query($q1,$conex);
					}

				}


				///////////////Actualizar o insertar según sea necesario ///////////////////////////////////////
			}
		}
	}
	//include_once("free.php");
	odbc_close_all();
}

///////////////////////////////////////////Guardar ingresos en db Matrix temporal /////////////////////////

/**
 * Alamcena en tabla 000014 los afines que no hayan sido ingresados previamnete para un ingreso dado
 *
 * @param unknown_type $vector
 */
function reservar ($vector)

{
	

	

	$size1=count($vector);

	$hoy = date('Y-m-d');

	for ($i=0; $i<$size1; $i++)
	{
		//echo $vector[$i][3];

		$vector[$i][3]=str_replace ( '/',  '-', $vector[$i][3] );

		//se adecua si fueron actualizados o no o si tienen datos completos o no
		if ($vector[$i][15]==0 or $vector[$i][15]==1)
		{
			$vector[$i][15]='off';
		}else
		{
			$vector[$i][15]='on';
		}
		if ($vector[$i][16]==1)
		{
			$vector[$i][16]='off';
		}else
		{
			$vector[$i][16]='on';
		}

		//busco si ya existe el registro en la 14
		$query="select * from magenta_000014 where repdoc='".$vector[$i][0]."' and reping ='".$vector[$i][3]."' ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);

		if($num < 1)
		{
			// sino, entonces adecuo los datos para insertar

			$doc=trim($vector[$i][13]);
			$tipDoc=trim($vector[$i][14]);
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

			//me fijo que el sujeto exista en la 8
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
					$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac, repfac, Seguridad) ";
					$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$vector[$i][0]."', '".$vector[$i][1]."', '".$vector[$i][2]."', '".$vector[$i][3]."', '".$vector[$i][4]."',  '".$vector[$i][5]."', '".$resulta[0]."', '".$vector[$i][15]."', '".$vector[$i][16]."', '".$vector[$i][12]."', '".$resulta[1]."', '".$resulta[2]."', 'A-magenta' )";
					$err=mysql_query($q1,$conex);
				}else
				{
					$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac,  repfac, Seguridad) ";
					$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$vector[$i][0]."', '".$vector[$i][1]."', '".$vector[$i][2]."', '".$vector[$i][3]."', '".$vector[$i][4]."',  '".$vector[$i][5]."', 'VIP', '".$vector[$i][15]."','".$vector[$i][16]."', '".$vector[$i][12]."',  '".$resulta[1]."', '".$resulta[2]."', 'A-magenta' )";
					$err=mysql_query($q1,$conex);
				}
			}
			else
			{
				$q1="insert into magenta_000014 (medico, Fecha_data, Hora_data, repdoc, rephis, repnom, reping, repser, rephor, repusu, repact, repdat, rephos, reppac, repfac, Seguridad) ";
				$q1= $q1." values ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$vector[$i][0]."', '".$vector[$i][1]."', '".$vector[$i][2]."', '".$vector[$i][3]."', '".$vector[$i][4]."',  '".$vector[$i][5]."', '".$vector[$i][7]."', '".$vector[$i][15]."', '".$vector[$i][16]."', '".$vector[$i][12]."', '".$resulta[1]."', '".$resulta[2]."', 'A-magenta' )";
				$err=mysql_query($q1,$conex);
			}

		}
	}

	//include_once("free.php");
	odbc_close_all();
}



///////////////////////////////////////////Guardar ingresos en db/////////////////////////

///////////////////////////////////////////Programa/////////////////////////

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

	//recibo los vectores que me manda visitas.php

	$vect1 = $HTTP_POST_VARS['vect1'];
	$vect2 = $HTTP_POST_VARS['vect2'];

	//envio los datos enviados de los AAA para que sean almacenados como visita y sean actualizados
	if ($vect1[0][0] !='sin datos')
	{
		reservar ($vect1);
		actualizar ($vect1);

	}

	//envio los datos enviados de los AAA para que sean almacenados como visita y sean actualizados
	if ($vect2[0][0] !='sin datos')
	{
		reservar ($vect2);
		actualizar ($vect2);
	}

	/****************************BUSQUEDA DE LOS DE LA NOCHE************************************************/


	/**
	 * conexion con matrix
	 */
	

	

	$bd='facturacion';
	/**
	 * conexion con unix
	 */
	include("magenta/socket2.php");

	/**
	 * para funciones que se utilizan en este programa
	 */
	include_once("magenta/incVisitas.php");

	//////////////////////////////////////////////////////////Busqueda principal en db////////////////////////////

	//Busco inicialmente en inpac quienes vinieron los dos dias anteriores
	$array[0][0]='sin datos';
	$array2[0][0]='sin datos';

	$i=0;

	$tiempo=mktime(0,0,0,date('m'),date('d'),date('Y'))-172800;
	$fecIni=date('Y-m-d',$tiempo);
	$tiempo=mktime(0,0,0,date('m'),date('d'),date('Y'))-86400;
	$fecFin=date('Y-m-d',$tiempo);

	//busco en inpac
	/*$query="select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom   ";
	$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E ";
	$query= $query."where A.pacfec between '$fecIni' and '$fecFin' and B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
	$query= $query."and C.sercod=A.pacser ";
	$query= $query."and E.diacod=A.pacdin order by A.pachor ";*/
	
	//funcion para nulos
	$long='                                    ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
/*	$increment=1;
	//$increment++;
    $table=date("Mdhis").$increment;
	
	$query="select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom   ";
	$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E ";
	$query= $query."where A.pacfec between '$fecIni' and '$fecFin' and B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
	$query= $query."and C.sercod=A.pacser ";
	$query= $query."and E.diacod=A.pacdin order by A.pachor ";
	$query= $query."into temp $table";
			
			
	//echo "<br>".$query;
	$err_o = odbc_exec($conex_o,$query)or die( odbc_error()." - $query - ".odbc_errormsg() );
			
			$select= " pachis ,pacnum ,pachor ,pachos ,pacced ,pactid ,pacser ,pacnom ,pacap1 ,'".$long."' as pacap2 ,pacfec ,pacnac ,pacsex ,paccer ,pacotrbar ,pacdin ,sernom ,dianom ";
			$from= "$table" ;
			$where=NULL;
		
			$llaves[0]=1;
			$llaves[1]=2;
		
			$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o ); */
	
	//fin funcion para nulos

				$query="select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, A.pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom   ";
				$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E ";
				$query= $query."where A.pacfec between '$fecIni' and '$fecFin' and B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
				$query= $query."and C.sercod=A.pacser ";
				$query= $query."and A.pacap2 is not null ";
				$query= $query."and E.diacod=A.pacdin ";
				$query= $query." union ";
				$query= $query."select A.pachis, A.pacnum, A.pachor, A.pachos, A.pacced, A.pactid, A.pacser, A.pacnom, A.pacap1, '.'as pacap2, A.pacfec, A.pacnac, A.pacsex, A.paccer, B.pacotrbar, A.pacdin, C.sernom, E.dianom   ";
				$query= $query."from inpac A , inpacotr B, Outer inser C, Outer india E ";
				$query= $query."where A.pacfec between '$fecIni' and '$fecFin' and B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
				$query= $query."and C.sercod=A.pacser ";
				$query= $query."and A.pacap2 is null ";
				$query= $query."and E.diacod=A.pacdin ";
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
		$pacHos [$i]= odbc_result($err_o,4);
		$fuente[$i]='inpac';


		switch ($pacHos[$i])
		{
			case 'H':
			$amb[$i]=1;
			$serAct [$i]='';
			$fecEgr [$i]= '';
			break;
			case 'C':
			$amb[$i]=0;
			$serAct [$i]= odbc_result($err_o,17);
			$fecEgr [$i]='';
			break;
			case 'A':
			$amb[$i]=0;
			$serAct [$i]= odbc_result($err_o,17);
			$fecEgr [$i]= '';
			break;
		}

		$i++;
	}

	//busco en aymov
	/*$query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom   ";
	$query= $query."from aymov A , aymovotr B, Outer inser C  ";
	$query= $query."where movfec between '$fecIni' and '$fecFin'  and movtip <> 'I' and movfue <> 'PO' ";
	$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
	$query= $query."and C.sercod=A.movsin ";*/
	
	//funcion para nulos
	/*		$increment++;
            $table=date("Mdhis").$increment;
			
			$query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom   ";
			$query= $query."from aymov A , aymovotr B, Outer inser C  ";
			$query= $query."where movfec between '$fecIni' and '$fecFin'  and movtip <> 'I' and movfue <> 'PO' ";
			$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
			$query= $query."and C.sercod=A.movsin ";
			$query= $query."into temp $table";
			
			
			
			//echo "<br>".$query;
			
            $err_o = odbc_exec($conex_o,$query)or die( odbc_error()." - $query - ".odbc_errormsg() );
			
			$select= " movdoc ,movfue ,movhor ,'".$long."' as movnum ,movced ,movtid ,movsin ,movnom ,movape ,'".$long."' as movap2 ,movfec ,movnac ,movsex ,movcer ,movotrbar ,movdia ,sernom ";
			$from= "$table" ;
			$where=NULL;
		
			$llaves[0]=1;
			$llaves[1]=2;
		
			$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o );  */
	
	
	//fin funcion para nulos

				$query="select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom   ";
				$query= $query."from aymov A , aymovotr B, Outer inser C  ";
				$query= $query."where movfec between '$fecIni' and '$fecFin'  and movtip <> 'I' and movfue <> 'PO' ";
				$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
				$query= $query."and A.movnum is not null ";
				$query= $query."and A.movap2 is not null ";
				$query= $query."and C.sercod=A.movsin ";
				$query= $query." union ";
				$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom   ";
				$query= $query."from aymov A , aymovotr B, Outer inser C  ";
				$query= $query."where movfec between '$fecIni' and '$fecFin'  and movtip <> 'I' and movfue <> 'PO' ";
				$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
				$query= $query."and A.movnum is null ";
				$query= $query."and A.movap2 is null ";
				$query= $query."and C.sercod=A.movsin ";
				$query= $query." union ";
				$query= $query."select A.movdoc, A.movfue, A.movhor, A.movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, '.' as movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom   ";
				$query= $query."from aymov A , aymovotr B, Outer inser C  ";
				$query= $query."where movfec between '$fecIni' and '$fecFin'  and movtip <> 'I' and movfue <> 'PO' ";
				$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
				$query= $query."and A.movnum is not null ";
				$query= $query."and A.movap2 is null ";
				$query= $query."and C.sercod=A.movsin ";
				$query= $query." union ";
				$query= $query."select A.movdoc, A.movfue, A.movhor, 0 as movnum, A.movced, A.movtid, A.movsin, A.movnom, A.movape, A.movap2, A.movfec, A.movnac, A.movsex, A.movcer, B.movotrbar, A.movdia, C.sernom   ";
				$query= $query."from aymov A , aymovotr B, Outer inser C  ";
				$query= $query."where movfec between '$fecIni' and '$fecFin'  and movtip <> 'I' and movfue <> 'PO' ";
				$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
				$query= $query."and A.movnum is null ";
				$query= $query."and A.movap2 is not null ";
				$query= $query."and C.sercod=A.movsin ";
	
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
		$horIng [$i]= odbc_result($err_o,3);
		$fecEgr [$i]= $fecIng [$i];
		$horEgr [$i]='';
		$amb[$i]=0;
		$fuente [$i]='aymov';
		$i++;

	}
	//echo $i;

	//búsqueda en inpaci

	/*$query="select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom  ";
	$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D ";
	$query= $query."WHERE D.egring between '$fecIni' and '$fecFin' ";
	$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
	$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
	$query= $query."and C.sercod=D.egrsin ";*/
	
	//funcion para nulos
/*	$increment++;
            $table=date("Mdhis").$increment;
			
			$query="select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom  ";
			$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D ";
			$query= $query."WHERE D.egring between '$fecIni' and '$fecFin' ";
			$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
			$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
			$query= $query."and C.sercod=D.egrsin ";
			$query= $query."into temp $table";
			
			
			//echo "<br>".$query;
			
            $err_o = odbc_exec($conex_o,$query)or die( odbc_error()." - $query - ".odbc_errormsg() );
			
			$select= " egrhis ,egrnum ,egrcer ,egrdin ,egring ,egrhoi ,egrhoe ,egregr ,egrsin ,pacced ,pactid ,pacnom ,pacap1 ,'".$long."' as pacap2 ,pacnac ,pacsex ,pacotrbar ,sernom ";
			$from= "$table" ;
			$where=NULL;
		
			$llaves[0]=1;
			$llaves[1]=2;
		
			$err_o = ejecutar_consulta ($select, $from, $where, $llaves, $conex_o ); */
	//fin funcion para nulos

				$query="select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, A.pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom  ";
				$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D ";
				$query= $query."WHERE D.egring between '$fecIni' and '$fecFin' ";
				$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
				$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
				$query= $query."and A.pacap2 is not null ";
				$query= $query."and C.sercod=D.egrsin ";
				$query= $query." union ";
				$query= $query."select D.egrhis, D.egrnum, D.egrcer, D.egrdin, D.egring, D.egrhoi, D.egrhoe, D.egregr, D.egrsin, A.pacced, A.pactid, A.pacnom, A.pacap1, '.' as pacap2, A.pacnac, A.pacsex,  B.pacotrbar, C.sernom  ";
				$query= $query."from inpaci A , inpacotr B, OUTER inser C, inmegr D ";
				$query= $query."WHERE D.egring between '$fecIni' and '$fecFin' ";
				$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
				$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
				$query= $query."and A.pacap2 is null ";
				$query= $query."and C.sercod=D.egrsin ";

	$err_o = odbc_exec($conex_o,$query);

	while (odbc_fetch_row ($err_o))
	{

		$his [$i]= odbc_result($err_o,1);
		$numIng [$i]= odbc_result($err_o,2);
		$ced [$i]= odbc_result($err_o,10);
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
		$amb[$i]=0;
		$fuente [$i]='inpaci';
		$i++;


	}
	$ingresos= $i;


	//////////////////////////////////////////////////////////Fin Busqueda principal en db////////////////////////////

	//////////////////////////////////////////////////////////selección de clientes AAA////////////////////////////


	$k=0;

	for ($j=0; $j<$ingresos; $j++)
	{
		//con estas se cuales son AAA
		$zona=Zona($bar[$j]);
		$tipUsu= esAAA ($ced[$j], $cedTip[$j], $codRes [$j]);

		If ($tipUsu==1 or $tipUsu==2)
		{
			$tipUsu='AFIN-AAA-1';
		}else
		{
			//esto no va a funcionar todavia lo de la reclasificacion automatica porque la taba de zonas esta vacia
			$frecVis= calcularFec ($fuente[$j], $ced [$j], $cedTip [$j], $his [$j]);
			$tipUsu=definirTipUsu ($frecVis, $sex[$j], $fecNac[$j], $zona, $codRes[$j], $ced[$j], $cedTip[$j]);
		}

		//ACA PODRIA IR VIP PERO MAS ADELANTE DE DIFERENCIARAN
		if (strcmp ($tipUsu,'AFIN-AAA-1')==0)
		{

			$array[$k][0]=trim($ced[$j]).' '.trim($cedTip[$j]);
			$array[$k][1]=$his [$j];
			$array[$k][2]=$nom [$j].$ape1 [$j].$ape2 [$j];
			$array[$k][3]=$fecIng [$j];
			$array[$k][4]=$serNom [$j];
			$array[$k][5]= $horIng [$j];
			$array[$k][6]=$fecEgr [$j];
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
		$array=verAct ($array, 14);

	}

	//////////////////////////////////////////////////////////Fin selección de clientes AAA////////////////////////////
	//////////////////////////////////////////////////////////selección de clientes BBB////////////////////////////

	$size1=count($array);
	$k=0;


	for ($j=0; $j<$ingresos; $j++)
	{

		$tipUsu=esBBB ($ced[$j], $cedTip[$j]);

		if ($tipUsu==1 or $tipUsu==2)
		{
			for ($h=0; $h<$size1; $h++)
			{
				if (trim($array[$h][13])==trim($ced[$j]))
				$repetido=1;
			}


			if (!isset ($repetido))
			{
				//ACA PODRIA IR VIP PERO MAS ADELANTE DE DIFERENCIARAN
				$tipUsu='AFIN-BBB-2';

				$array2[$k][0]=trim($ced[$j]).' '.trim($cedTip[$j]);
				$array2[$k][1]=$his [$j];
				$array2[$k][2]=$nom [$j].$ape1 [$j].$ape2 [$j];
				$array2[$k][3]=$fecIng [$j];
				$array2[$k][4]=$serNom [$j];
				$array2[$k][5]= $horIng [$j];
				$array2[$k][6]=$fecEgr [$j];
				$array2[$k][7]=$tipUsu;
				$array2[$k][8]=$numIng [$j];
				$array2[$k][9]=$zona;
				$array2[$k][10]=$fuente [$j];
				$array2[$k][11]=$fecNac [$j];
				$array2[$k][12]=$amb [$j];
				$array2[$k][13]=$ced [$j];
				$array2[$k][14]=$cedTip[$j];
				$k++;

			}
		}

	}


	if ($k !=0)
	{

		$array2=verAct ($array2, 14);

	}


	//////////////////////////////////////////////////////////Fin selección de clientes BBB////////////////////////////
	if ($array[0][0] !='sin datos')
	{
		reservar ($array);
		actualizar ($array);

	}

	if ($array2[0][0] !='sin datos')
	{
		reservar ($array2);
		actualizar ($array2);
	}


	//Cerrar conexiones
	//include_once("free.php");

	echo "</br></br><CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' ></br>";
	echo "<form NAME='batch' ACTION='visitas.php' METHOD='POST'>";
	echo "<font size=3  face='arial' align = center ><b>EL PROCESO DE ACTUALIZACÓN HA SIDO REALIZADO CON EXITO</font></BR></BR>";
	echo "<tr><td align=center colspan=2><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='VOLVER' ></td></tr>";

	echo "</form>";
	echo "</fieldset>";
}

?>
			
		

