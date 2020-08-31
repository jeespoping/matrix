<html>
<head>
	<title>Malas actualizaciones Magenta</title>
</head>
<body >

<?php
include_once("conex.php");

/**
 * 	REPORTE DE VISITAS DE AFINIDAD AAA-BBB-VIP
 * 
 * Este es un reporte que muestra las actualizaciones que se han hecho entre dos fechas y que datos quedaron mal diligenciados
 * 
 * @name  matrix\magenta\procesos\malIngreso.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-02-07
 * @version 2007-01-24
 * 
 * @modified  2007-01-24 Se documenta
	Actualizacion: Se agrega la funcion para la validacion de los nulos en las consultas a unix, tambien se cambia la ruta cuando llama a mamgenta
    Viviana Rodas 2012-05-23
	Actualizacion: Se retira la funcion que hace la validacion de los nulos ya que el script se muestra muy lento, se colocaron los unions
	Viviana Rodas 2012-05-30	
 * 
 * @table magenta_000008, select
 * @table magenta_000009, select
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
 *  @wvar $ano, ano inicial de la busqueda de visitas
 *  @wvar $ano2, ano final de la busqueda de visitas
 *  @wvar $bar, barrio del paciente para ver si esta por defecto
 *  @wvar $color variar de coloers al presentar los resultados
 *  @wvar $dia, dia inicial de la busqueda de visitas
 *  @wvar $dia2, dia final de la busqueda de visitas
 *  @wvar $fecFin fecha final de la busqueda de visitas
 *  @wvar $fecha fecha inicial predefinida como fecha de busqueda
 *  @wvar $fecing fecha ingreso de aumov
 *  @wvar $fecing2 fecha ingreso de inpaci
 *  @wvar $fecIni fecha inicial de la busqueda de actualizaciones
 *  @wvar $mes mes inicial de la busqueda de visitas
 *  @wvar $mes2 mes final de la busqueda de visitas
 *  @wvar $serNom, nombre del ultimo servicio de ingreso
 *  @wvar $tipDoc, tipo de documento del paciente
 *  @wvar $vector, se guarda la info de los pacientes cuyas actualizaciones esten en el rango de fechas
 *  @wvar $vector2, guardan para un paciente la lista de datos que no estan bien llenados
*/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-06 (Arleyda Insignares C.)
 						-Se cambia encabezado y titulos con ultimo formato.
*************************************************************************************************************************/
$wautor="Carolina Castano P.";
$wversion='2007-01-23';
$wactualiz='2016-05-05';
/****************************PROGRAMA************************************************/
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
		 * funciones llaamadas desde este programa
		 */
		include_once("magenta/incVisitas.php");
		$increment=1;
	
		//////////////forma para ingresar la fecha de búsqueda////////////////////

		if (!isset ($ano))

		{
			$pass= intval(date('m'))-1;

			if ($pass<10)
			$pass='0'.$pass;

			$fecha=date('Y').'-'.$pass.'-'.date('d');

			if ($pass==0)
			{
				$fecha= intval (date('Y'))-1;
				$fecha=$fecha.'-12-'.date('d');

			}

			ECHO "<table border=0 align=center size=100%>";
            echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>REPORTE DE ACTUALIZACIONES</b></font></div></div></BR>";
			//ECHO "<tr><td align=center ><A HREF='malIngreso.php'><font size=4 color='#FF00FF'>REPORTE DE ACTUALIZACIONES</font></a></td></tr>";
			ECHO "</table></br></br></br>";

			echo "<table align='center'>";
			echo "<tr><td align=center ><font size=3>INGRESE POR FAVOR EL RANGO DE FECHAS EN EL CUAL DESEA REALIZAR LA BÚSQUEDA</font></td>";
			echo "</table></br></br>";

			echo "<fieldset align=center></br>";

			echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
			echo "<table align='center'>";
			echo "<tr class='encabezadotabla'>";

			echo "<td align=center width='150'><font size=3  face='arial'>Fecha inicial:&nbsp</td>";
			echo "<td class='fila2' align=center ><font size=2  face='arial'>Año:</b>&nbsp</td>";
			echo "<td class='fila2' align=center ><font size='2'  align=center face='arial'><input type='text' name='ano'  value='".SUBSTR ($fecha,0,4)."' size='2'></td>";
			echo "<td class='fila2' align=center ><font size=2  face='arial'>Mes:</b>&nbsp</td>";
			echo "<td class='fila2' align=center ><font size='2'  align=center face='arial'><input type='text' name='mes'  value='".SUBSTR ($fecha,5,2)."' size='1'></td>";
			echo "<td class='fila2' align=center ><font size=2  face='arial'>Día:</b>&nbsp</td>";
			echo "<td class='fila2' align=center ><font size='2'  align=center face='arial'><input type='text' name='dia'  value='".date ('d')."' size='1'></td>";
			echo "<td class='fila2' align=center >&nbsp;&nbsp;&nbsp;&nbsp;</td>";
			echo "<td align=center width='150'><font size=3  face='arial'>Fecha final:&nbsp</td>";
			echo "<td class='fila2' align=center ><font size=2  face='arial'>Año:</b>&nbsp</td>";
			echo "<td class='fila2' align=center ><font size='2'  align=center face='arial'><input type='text' name='ano2'  value='".date ('Y')."' size='2'></td>";
			echo "<td class='fila2' align=center ><font size=2  face='arial'>Mes:</b>&nbsp</td>";
			echo "<td class='fila2' align=center ><font size='2'  align=center face='arial'><input type='text' name='mes2'  value='".date ('m')."' size='1'></td>";
			echo "<td class='fila2' align=center ><font size=2  face='arial'>Día:</b>&nbsp</td>";
			echo "<td class='fila2' align=center ><font size='2'  align=center face='arial'><input type='text' name='dia2'  value='".date ('d')."' size='1'></td>";

			echo "</td>";
			echo "</tr></TABLE></br>";
			echo "<TABLE align=center><tr>";
			echo "<tr><td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
			echo "</TABLE>";
			echo "</td>";
			echo "</tr>";
			echo "</form>";
			echo "</fieldset>";

		}else //ya se seleccionaron las fechas de busqueda
		{


			/////////////////////////////////Validación de campos de fecha////////////////////

			if ((strlen ($ano) != 4) or (strlen ($mes)!=2) or (strlen ($dia)!=2) or (strlen ($ano2)!= 4) or (strlen ($mes2) != 2) or (strlen ($dia2)!= 2))
			{
				echo '<script language="Javascript">';
				echo 'window.location.href=window.location.href;';
				echo 'alert ("El formato para ingresar las fechas es como el siguiente ejemplo: Año:2006  Mes:02  Día:25 ")';
				echo '</script>';
			}
			//////////////////////////////////////////////////////////Busqueda principal en db Matrix de actualizaciones en fechas////////////////////////////
			//Busco LAS ACTUALIZACIONES ENTRE LAS FECHAS
			$i=0;
			$fecIni=$ano."/".$mes."/".$dia;
			$fecFin=$ano2."/".$mes2."/".$dia2;

			ECHO "<table border=0 align=center size='700'>";
			ECHO "<tr>";
			ECHO "<td>";
			ECHO "<table border=0 align=center>";
			ECHO "<tr class='fila1'><td align=center ><A HREF='malIngreso.php'><font size=4 >REPORTE DE ACTUALIZACIONES: desde $fecIni hasta $fecFin</font></a></td></tr>";
			ECHO "</table>";
			ECHO "</td>";
			ECHO "<td>";
			echo "<center><img SRC='/matrix/images/medical/Magenta/aaa.gif'></center>";
			ECHO "</td>";

			ECHO "</tr>";
			ECHO "</table></br></br>";

			$query="select * from magenta_000008 where Clifac between'$fecIni' and '$fecFin' and clipac<>'magenta' order by clipac, Clifac ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			$num2=37;

			if($num >=1)
			{
				While ( $resulta = mysql_fetch_row($err) )
				{

					if (substr($resulta[27],0,3)!='VIP')
					{
						for ($j=0; $j<37; $j++)
						{
							$vector[$i][$j]= $resulta[$j];
						}

						$i++;
					}
				}

				$num=$i;
				//Orden de aparicion

				/*encabezado
				$nombres=$row['Clinom'];
				$ape1=$row['Cliap1'];
				$ape2=$row['Cliap2'];
				$fecNac=$row['Clifna'];
				$lugNac=$row['Clilna'];
				$sexo=$row['Clisex'];
				$estCivil=$row['Cliciv'];
				$nHijos=$row['Clinhij'];
				$prof=$row['Clipro'];
				$tel1=$row['Clite1'];
				$tel2=$row['Clite2'];
				$movil=$row['Climov'];
				$email1=$row['Cliem1'];
				$email2=$row['Cliem2'];
				$dir=$row['Clidir'];
				$estrato=$row['Clietr'];
				$zona=$row['Clizon'];
				$municipio=$row['Climun'];
				$dept=$row['Clidep'];
				$pais=$row['Clipai'];
				$tipUsu=$row['Clitip'];
				$acompa=$row['Cliaco'];
				$afiliado=$row['Clires'];
				$planC=$row['Clicom'];
				$fam=$row['Clifam'];
				$llamtel1=	$row['Clilt1'];
				$llamtel2=	$row['Clilt2'];
				$llamemai=	$row['Clilem'];
				$llammovi=	$row['Clilmo'];
				$llamdire=	$row['Clildi'];
				$fam=$row['Clifam'];*/

				if ($i>0)
				{
					ECHO '<TABLE border=1 align=center>';
					ECHO "<Tr class='encabezadotabla' >";

					echo "<td align=center><font size=2>PERSONA QUE ACTUALIZA</font></td>";
					echo "<td align=center><font size=2>FECHA DE ACTUALIZACION</font></td>";
					echo "<td align=center><font size=2>IDENTIFICACION DEL USUARIO</font></td>";
					echo "<td align=center><font size=2>NOMBRE DEL USUARIO</font></td>";
					echo "<td align=center><font size=2>DATOS INCOMPLETOS</font></td>";
					echo "<td align=center><font size=2>PROFESION POR DEFECTO</font></td>";
					echo "<td align=center><font size=2>ÚLTIMO INGRESO</font></td>";
					echo "<td align=center><font size=2>BARRIO POR DEFECTO</font></td>";


					ECHO "</Tr >";
				}

				// aca consulto por documento extraido la información magenta
				for ($i=0; $i<$num; $i++)
				{
					$query1="select * from magenta_000009 where Cdedoc='".$vector[$i][5]."' and Cdetid='".$vector[$i][6]."' ";
					$err1=mysql_query($query1,$conex);
					$num1=mysql_num_rows($err1);
					$num3=31+$num2;

					if($num1 >0)
					{
						$resulta = mysql_fetch_row($err1);
						$k=0;
						for ($j=$num2; $j<$num3; $j++)
						{
							$vector[$i][$j]= $resulta[$k];
							$k++;
						}


						// orden del vector 2
						/*encabezado
						$fecAct=$row1['Cdefac'];
						$servicio=$row1['Cdeser'];
						$comBeb = $row1['Cdebeb'] ;
						$comFam = $row1['Cdefam']  ;
						$comAdo = $row1['Cdeado']  ;
						$comAdu = $row1['Cdeadu']  ;
						$comSnc = $row1['Cdesnc'] ;
						$comSrs = $row1['Cdesrs'] ;
						$comSrn = $row1['Cdesrn'] ;
						$comCan = $row1['Cdecan'] ;
						$comCar = $row1['Cdesca'] ;
						$comSmu = $row1['Cdesmu'] ;
						$comOtr = $row1['Cdeeot'] ;
						$gusLec = $row1['Cdelec'] ;
						$gusApl = $row1['Cdeapl'] ;
						$gusAdr = $row1['Cdeadr'] ;
						$gusCin = $row1['Cdecin'] ;
						$gusMus = $row1['Cdemus'] ;
						$depFut = $row1['Cdefut'] ;
						$depGol = $row1['Cdegol'] ;
						$depTen = $row1['Cdeten'] ;
						$depEqu = $row1['Cdeequ'] ;
						$depGym = $row1['Cdegym'] ;
						$depOtr = $row1['Cdedot'];
						$requer = $row1['Cdereq'];
						*/
						//////////////////////////////////////////////////////////Impresión de datos ////////////////////////////


						$k=0; // cantidad de datos mal ingresados

						if ($vector[$i][7]=="Dato No Encontrado" or $vector[$i][7]=="")
						{
							$vector2[$i][$k]='nombres';
							$k++;
						}
						if ($vector[$i][8]=="Dato No Encontrado" or $vector[$i][8]=="")
						{
							$vector2[$i][$k]='Primer apellido';
							$k++;
						}
						if ($vector[$i][9]=="Dato No Encontrado" or $vector[$i][9]=="")
						{
							$vector2[$i][$k]='segundo apellido';
							$k++;
						}
						if ($vector[$i][10]=="Dato No Encontrado" or $vector[$i][10]=="0000-00-00")
						{
							$vector2[$i][$k]='fecha de nacimiento';
							$k++;
						}
						if ($vector[$i][11]=="00-Dato No Encontrado" or $vector[$i][11]=="")
						{
							$vector2[$i][$k]='lugar de nacimiento';
							$k++;
						}
						if ($vector[$i][12]=="00-Dato No Encontrado" or $vector[$i][12]=="")
						{
							$vector2[$i][$k]='sexo';
							$k++;
						}
						if ($vector[$i][14]=="00-Dato No Encontrado" or $vector[$i][14]=="")
						{
							$vector2[$i][$k]='estado civil';
							$k++;
						}
						if ($vector[$i][22]=='0' or $vector[$i][22]=='')
						{
							$vector2[$i][$k]='estrato';
							$k++;
						}
						if ($vector[$i][16]=="Dato No Encontrado" or $vector[$i][16]=='')
						{
							$vector2[$i][$k]='telefono 1';
							$k++;
						}
						if ($vector[$i][21]=="Dato No Encontrado" or $vector[$i][21]=='')
						{
							$vector2[$i][$k]='direccion';
							$k++;
						}
						if ($vector[$i][23]==	"00-Dato No Encontrado")
						{
							$vector2[$i][$k]='zona';
							$k++;
						}
						if ($vector[$i][24]==	"00-Dato No Encontrado")
						{
							$vector2[$i][$k]='municipio';
							$k++;
						}

						if ($vector[$i][26]==	"Dato No Encontrado")
						{
							$vector2[$i][$k]='pais';
							$k++;
						}

						if ($vector[$i][28]==	"00-Dato No Encontrado")
						{
							$vector2[$i][$k]='afiliado';
							$k++;
						}

						if (($vector[$i][32]==	"0" and $vector[$i][33]==	"0" and $vector[$i][34]==	"0" and $vector[$i][35]==	"0" and  $vector[$i][36]==	"0")or ($vector[$i][32]==	"" and $vector[$i][33]==	"" and $vector[$i][34]==	"" and $vector[$i][35]==	"" and  $vector[$i][36]==	""))
						{
							$vector2[$i][$k]='contacto';
							$k++;
						}

						if (substr ($vector[$i][27],-1)!='2')
						{
							if ($vector[$i][60]=="off" and $vector[$i][56]=="off" and $vector[$i][57]==	"off" and $vector[$i][58]==	"off" and $vector[$i][59]==	"off" and ( $vector[$i][66]=="Dato No Encontrado" or  $vector[$i][66]=='') )
							{
								$vector2[$i][$k]='gustos';
								$k++;
							}
							if ($vector[$i][61]=="off" and $vector[$i][62]=="off" and $vector[$i][63]==	"off" and $vector[$i][64]==	"off" and $vector[$i][65]==	"off" and ( $vector[$i][66]=="Dato No Encontrado" or  $vector[$i][66]=='') )
							{
								$vector2[$i][$k]='deportes';
								$k++;
							}
							if ($vector[$i][45]=="off" and $vector[$i][48]==	"off" and $vector[$i][46]==	"off" and $vector[$i][47]==	"off" )
							{
								$vector2[$i][$k]='comunidades';
								$k++;
							}
							if ($vector[$i][49]=="off" and $vector[$i][50]=="off" and $vector[$i][51]==	"off" and $vector[$i][53]==	"off" and $vector[$i][54]==	"off" and ($vector[$i][55]=="Dato No Encontrado" or $vector[$i][55]=="" ))
							{
								$vector2[$i][$k]='enfermedades';
								$k++;
							}

						}
					}else
					{
						//echo '<script language="Javascript">';
						//echo 'window.location.href=window.location.href;';
						//echo 'alert ("Eror en el almacenamiento del usuarios, favor avisar a sistemas")';
						//echo '</script>';
						$k=8;
					}

					$n=explode("-",$vector[$i][6]);
					$tipdoc=$n[0];
					
					
					//realizo busqueda de datos de profesión y barrio en el último ingreso

								
					//funcion para los datos nulos
					$long='                                    ';///25, espacios para que no se limite el tamaño de los campos en el update que se hace a la temporal
			//$increment=1;
			
          		
					$query="select A.pachis, A.pacnum, A.pacced, A.pactid, A.pacser, A.paccer, B.pacotrbar, C.sernom  ";
					$query= $query."from inpac A , inpacotr B, inser C ";
					$query= $query."where A.pacced='".$vector[$i][5]."' and A.pactid='".$tipdoc."' and  A.pacfec='".$vector[$i][4]."' and B.pacotrhis=A.pachis and B.pacotrnum=A.pacnum ";
					$query= $query."and C.sercod=A.pacser ";
							
			
				
            $err_o = odbc_exec($conex_o,$query)or die( odbc_error()." - $query - ".odbc_errormsg() );
			
		
								
					while (odbc_fetch_row ($err_o))
					{
						$bar [$i]= odbc_result($err_o,7);
						$serNom [$i]= odbc_result($err_o,8);
					}

					if (!isset ($bar [$i]))
					{

						//búsco en aymov
											
						//funcion para datos nulos
						$increment++;
					//	$table=date("Mdhis").$increment;
			
										
						$query="select A.movdoc, A.movfue, A.movnum, A.movsin, A.movcer, B.movotrbar, C.sernom, A.movfec  ";
						$query= $query."from aymov A , aymovotr B, inser C  ";
						$query= $query."where A.movced='".$vector[$i][5]."' and A.movtid='".$tipdoc."' and movtip <> 'I' and movfue <> 'PO' ";
						$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
						$query= $query."and A.movnum is not null  ";
						$query= $query."and C.sercod=A.movsin  ";
						$query= $query." union  ";
						$query= $query."select A.movdoc, A.movfue, 0 as movnum, A.movsin, A.movcer, B.movotrbar, C.sernom, A.movfec  ";
						$query= $query."from aymov A , aymovotr B,  inser C  ";
						$query= $query."where A.movced='".$vector[$i][5]."' and A.movtid='".$tipdoc."' and movtip <> 'I' and movfue <> 'PO' ";
						$query= $query."and B.movotrdoc=A.movdoc and B.movotrfue=A.movfue ";
						$query= $query."and A.movnum is null  ";
						$query= $query."and C.sercod=A.movsin  ";
						$query= $query."order by 8  ";
			
			
						
            $err_o = odbc_exec($conex_o,$query)or die( odbc_error()." - $query - ".odbc_errormsg() );
			
	
						while (odbc_fetch_row ($err_o))
						{
							$fecing=odbc_result($err_o,8);
							$bar [$i]= odbc_result($err_o,6);
							$serNom [$i]= odbc_result($err_o,7);

						}


						//búsqueda en inpaci

										
								
						$query="select D.egrhis, D.egrnum, D.egrcer, D.egrsin, B.pacotrbar, C.sernom, D.egring  ";
						$query= $query."from inpaci A , inpacotr B, inser C, inmegr D ";
						$query= $query."WHERE A.pacced='".$vector[$i][5]."' and A.pactid='".$tipdoc."'  ";
						$query= $query."and A.pachis=D.egrhis and A.pacnum=D.egrnum ";
						$query= $query."and B.pacotrhis=D.egrhis and B.pacotrnum=D.egrnum ";
						$query= $query."and C.sercod=D.egrsin ";
						
			
			
			
			
            $err_o = odbc_exec($conex_o,$query)or die( odbc_error()." - $query - ".odbc_errormsg() );
			
						
						while (odbc_fetch_row ($err_o))
						{
							$fecing2=odbc_result($err_o,7);
							if (!isset($fecing) or $fecing2>=$fecing)
							{
								$bar [$i]= odbc_result($err_o,5);
								$serNom [$i]= odbc_result($err_o,6);
							}

						}

						if (!isset ($bar [$i]))
						{
							$bar [$i]= '';
							$serNom [$i]= '';
						}


					}
					if (is_int ($i/2))
					$color='fila1';
					else
					$color='fila2';

					ECHO "<Tr class ='$color'>";


					//presentamos los datos
					//persona que actualiza
					echo "<td ><font size=2>".$vector[$i][3]."</font ></td>";
					//fecha de actualizacion
					echo "<td ><font size=2>".$vector[$i][4]."</font ></td>";
					//identificacion del usuario
					echo "<td ><a href='Magenta.php?ced=1&doc=".$vector[$i][5]."&tipDoc=".$vector[$i][6]."' target='_blank'><font size=2>".$vector[$i][5]."&nbsp;".$tipdoc."</font></a></td>";
					//nombre del paciente
					echo "<td ><font size=2>".$vector[$i][7].'&nbsp;'.$vector[$i][8].'&nbsp;'.$vector[$i][9].'</font></td>';

					//lista de datos incompletos, si son menos de tris se listan cuales son
					if ($k == 0)
					{
						echo "<td >&nbsp</td>";
					}

					if ($k>3)
					{
						echo "<td ><font size=2>Más de tres</font></td>";
					}

					if ($k<=3 and $k>0)
					{
						echo "<td ><table>";

						for ($y=0; $y<$k; $y++)
						{
							echo "<tr><td ><font size=2>".$vector2[$i][$y]."</font></td></tr>";
						}

						echo "</table></td>";
					}

					//profesion por defecto
					if ($vector[$i][15] == 'P76-PERSONAS QUE NO HAN DECLARADO OCUPACION' or $vector[$i][15] =='' or $vector[$i][15]=='00-Dato No Encontrado' or $vector[$i][15]=='999-PERSONAS QUE NO HAN DECLARADO')
					{
						echo "<td ><input type='checkbox' name='Datos' checked ></td>";
					}else
					{
						echo "<td ><input type='checkbox' name='Datos'></td>";
					}

					//nombre del ultimo servicio de ingreso
					if (isset($serNom[$i]) and $serNom[$i]!='')
					{
						echo "<td ><font size=2>".$serNom [$i]."</font></td>";
					}else
					{
						echo "<td >&nbsp</td>";
					}

					//barrio por defecto
					if (($bar[$i] =='0000001' or $bar[$i] =='') and $serNom [$i]!='' )
					{
						echo "<td ><input type='checkbox' name='Datos' checked ></td>";
						$bar[$i];
					}else
					{
						echo "<td ><input type='checkbox' name='Datos'></td>";
					}



					ECHO "</Tr >";

				}
				echo "</table>";


			}else
			{
				echo '<script language="Javascript">';
				echo 'window.location.href=window.location.href;';
				echo 'alert ("No se encontró información en el rango de fechas ingresado, intente nuevamente por favor ")';
				echo '</script>';
			}
		}

	}else
	{
		echo "ERROR : "."$errstr ($errno)<br>\n";
	}


	
	
}
?>