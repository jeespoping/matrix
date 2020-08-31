<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de comentarios y sugerencias</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	
</head>
<body>
<?php
include_once("conex.php"); 

/********************************************** 	SOBRESCRITURA DE COMENTARIOS  *************************************
 * 
 * Este programa permite habilitar un comentario para ser reescrito por otro, es decir cambia el paciente que escribio el comentario e inicializarlo con lo minimo
 * 
 * @name  matrix\magenta\procesos\recom.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-10
 * @version 2006-01-10
 * 
 * 
 * @table magenta_000016, select, 
 * @table magenta_000017, select, update
 * @table magenta_000018, select, update, delete
 * @table det_selecciones, select
 *  @table magenta_000023, delete
 * 
 *  @wvar $aut persona que escribio o escribira el comentario
 *  @wvar $bandera1 indica que ya se ha presionado el boton cambiar
 *  @wvar $dir direccion del paciente
 *  @wvar $doc documento del paciente
 *  @wvar $ema email del paciente
 *  @wvar $idCom id del comentario a sobrescribir
 *  @wvar $idPac id del paciente a cambiar
 *  @wvar $priApe primer apellido del paciente
 *  @wvar $priNom primer nombre del paciente
 *  @wvar $segApe segundo apellido del paciente
 *  @wvar $segNom segundo nombre del paciente
 *  @wvar $tel telefono del paciente
 *  @wvar $tipDoc tipo de documento del paciente
*/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
						-Se cambia encabezado y titulos con ultimo formato
*************************************************************************************************************************/
$wautor="Carolina Castano P.";
$wversion='2007-01-10';
$wactualiz='2016-05-05';
//=================================================================================================================================
/************************html**********************************/

/**
 * Funcion que pinta el encabezado del programa con el numero de busqueda
 *
 * @param unknown_type $tren, indica si se debe pintat trencito o no
 */

function pintarBusqueda ($tren)
{
	global $empresa;
	global $wautor;
	global $wversion;

	echo "</br><table align='center' border='0' bgcolor='#336699' >\n" ;
    echo "<tr>" ;
	echo"<form action='recom.php' method='post' name='forma' >";
	echo "<tr><td class='fila1' colspan='2' ALIGN='CENTER'><font color=\"#ffffff\">INGRESE EL NUMERO DEL COMENTARIO QUE DESEA REEMPLAZAR: <INPUT TYPE='text' NAME='numCom' VALUE='' size='10' ><INPUT TYPE='submit' NAME='consultaR' VALUE='CONSULTAR'></td></tr>";
	echo "<input type='HIDDEN' name='wbasedato' value='".$empresa."'>";
	echo "</form>";
	echo "</tr>" ;
	if ($tren==1)
	{
		echo "</table></br></br></br></br></br></br></br></br>" ;
		echo "<center><img src='/matrix/images/medical/magenta/tren2.gif' ></center>";
	}
	else
	{
		echo "</table></br>" ;
	}
}

/**
 * Esta funcion pinta todo el formulario que permite el cambio del paciente que genera el comentario y la persona que lo ingresara
 *
 * @param unknown_type $doc documento del paciente
 * @param unknown_type $tipDoc tipo de documento del paciente
 * @param unknown_type $priNom primer nombre
 * @param unknown_type $segNom segundo nombre
 * @param unknown_type $priApe primer apellido
 * @param unknown_type $segApe segundo apellido
 * @param unknown_type $tel telefono
 * @param unknown_type $dir direccion
 * @param unknown_type $ema email
 * @param unknown_type $idCom id del contacto
 * @param unknown_type $aut quien ingreso o ingresara el contacto
 */
function datosPaciente($doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $tel, $dir, $ema,  $numCom, $aut, $idCom )
{
	global $conex;
	echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
	echo"<form action='recom.php' method='post' name='forma2' >";
	echo "<table align='center'  width='100%'>";
	echo "<tr>";
	echo "<td align=center colspan='2'><b><font size='3'><font color='#00008B'> DATOS DEL PACIENTE:</font></font></td>";
	echo "</tr>";
	echo "<tr>";
	if (!isset($doc))
	{
		echo "<td align=center class='texto3'  ><font size='3'  color='#00008B'>DOCUMENTO DE IDENTIDAD: </font></b><INPUT TYPE='text' NAME='doc' VALUE='' size='10'></td>";
	}
	else
	{
		echo "<td align=center class='texto3' ><font size='3'  color='#00008B'>DOCUMENTO DE IDENTIDAD: </font></b><INPUT TYPE='text' NAME='doc' VALUE='".$doc."' size='10'></td>";
	}

	if (!isset($tipDoc))
	{
		$tipDoc='CC-CEDULA DE CIUDADANIA';
	}

	$query="select Subcodigo, Descripcion from det_selecciones where medico='magenta' and codigo='01'";

	echo "<td align=center class='texto3'  ><font size='3'  color='#00008B'>TIPO DE DOCUMENTO: <select name='tipDoc'>" ;
	//query para dorp down de seleccion del tipo del documento, para busqueda del paciente

	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);

	if  ($num >0)
	{
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_row($err);
			$tDocS[$i]=$row[0]."-".$row[1];
			if($tDocS[$i] == strtoupper($tipDoc))
			echo "<option selected>".$tDocS[$i]."</option>";
			else
			echo "<option>".$tDocS[$i]."</option>";
		}

	}

	echo "<input type='hidden' name='bandera1' value='1' />";
	echo "<input type='hidden' name='idCom' value='".$idCom."' />";
	echo "<input type='hidden' name='numCom' value='".$numCom."' />";
	echo "<input type='hidden' name='priNom' value='".$priNom."' />";
	echo "<input type='hidden' name='segNom' value='".$segNom."' />";
	echo "<input type='hidden' name='priApe' value='".$priApe."' />";
	echo "<input type='hidden' name='segApe' value='".$segApe."' />";
	echo "<input type='hidden' name='tel' value='".$tel."' />";
	echo "<input type='hidden' name='dir' value='".$dir."' />";
	echo "<input type='hidden' name='ema' value='".$ema."' />";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=center class='texto3'colspan=2  >&nbsp</td>" ;
	echo "</tr>";

	echo "<tr>";

	echo "<td align=center><b><font size='3'  color='#00008B'>Nombre:&nbsp;</font></b>$priNom ";

	if ($segNom != ' ' and $segNom != '' and  $segNom != '- -')
	echo "$segNom ";

	echo "$priApe ";

	if ($segApe != ' ' and $segApe != '' and  $segApe != '- -')
	echo "$segApe";

	echo "</td>";
	echo "<td align=center><b><font size='3'  color='#00008B'> Telefono: </font></b>$tel</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td align=center><b><font size='3'  color='#00008B'>Direccion: </font></b>$dir</td>";
	echo "<td align=center> <b><font size='3'  color='#00008B'> Email: </font></b>$ema</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=center class='texto3'colspan=2  >&nbsp</td>" ;
	echo "</tr>";
	echo "<tr>";
	echo "<td align=center class='texto3'colspan=2><font size='3'  color='#00008B'><b>PERSONA QUE INGRESA EL COMENTARIO: <b><INPUT TYPE='text' NAME='aut' VALUE='".$aut."' size='50' ></td>" ;
	echo "</tr>";
	echo "<tr>";
	echo "<td align=center class='texto3'colspan=2  >&nbsp</td>" ;
	echo "</tr>";
	echo "<tr>";
	echo "<td align=center class='texto3'colspan=2><input type='submit' name='cambiar' value='cambiar' ></td>" ;
	echo "</tr>";
	echo"</form  >";
	echo "</table></fieldset></br>" ;
}

/********************************funciones************************************/

/**
 * No se ha encontrado el paciente asignado al comentario, por favor avise a Sistemas
 * */
function  DisplayError1()
{
	echo '<script language="Javascript">';
	echo 'alert ("No se ha encontrado el paciente asignado al comentario, por favor avise a Sistemas")';
	echo '</script>';

}

/**
 * No se han encontrado motivos para el comentario
 *
 */
function  DisplayError2()
{
	echo '<script language="Javascript">';
	echo 'alert ("No se han encontrado motivos para el comentario")';
	echo '</script>';

}

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
	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////

	/**
	 * conexion a Matrix
	 */
	

	


	$empresa='magenta';

	/////////////////////////////////////////////////inicialización de variables//////////////////////////
	/////////////////////////////////////////////////acciones concretas///////////////////////////////////

	if (!isset ($numCom)) //aun no tengo el id del comentario
	{
		pintarBusqueda (1);
	}
	else
	{

		pintarBusqueda (0);

		if (!isset($bandera1)) //no se ha dicatado aun ninguna operacion sobre el formulario
		{
			if (isset ($numCom) and $numCom!='') //si me mandan el id del comentario
			{

				$query ="SELECT id_persona, ccoaut, id FROM " .$empresa."_000017 where cconum=".$numCom." and fecha_data>='".date('Y')."-01-01' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores
				{
					$row=mysql_fetch_row($err);
					$idPac=$row[0];
					$aut=$row[1];
					$idCom=$row[2];

					//busco los datos del paciente
					$exp=explode('-',$idPac);
					if(isset($exp[3]))
					{
						$exp[0]=$exp[0].'-'.$exp[1];
						$exp[1]=$exp[2];
						$exp[2]=$exp[3];
					}

					$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpedir, cpetel, cpeema FROM " .$empresa."_000016 where cpedoc='".$exp[0]."' and  cpetdoc='".$exp[1]."-".$exp[2]."' ";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);
					//echo $query;
					if  ($num >0) //se llena los valores
					{
						$row=mysql_fetch_row($err);
						$doc=$row[0];
						$tipDoc=$row[1];
						$priNom=$row[2];
						$segNom=$row[3];
						$priApe=$row[4];
						$segApe=$row[5];
						$dir=$row[6];
						$tel=$row[7];
						$ema=$row[8];
					}else
					{

						DisplayError1();
					}

				}else
				{
					DisplayError2();
				}
			}

			//pinto el html
			echo "<center><b><font size='4'><font color='#00008B'>PROGRAMA PARA SOBRESCRIBIR COMENTARIOS</b></font></font></center></BR>";
			echo "<center><b><font size='4'><font color='#00008B'>DATOS DEL COMENTARIO NUMERO: ".$numCom."</b></font></font></center></BR>";


			datosPaciente($doc,$tipDoc, $priNom, $segNom, $priApe, $segApe, $tel, $dir, $ema, $numCom, $aut, $idCom );
		}
		else // se hacen los cambios en las tablas
		{
			//busco el id y nuevos datos del paciente del paciente segun la nueva cedula
			$query ="SELECT cpeno1, cpeno2, cpeap1, cpeap2, cpetel, cpedir, cpeema, id  FROM " .$empresa."_000016 where cpedoc='".$doc."' and cpetdoc='".$tipDoc."' ";

			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if  ($num >0) //se llena los valores y un vector con los resultados
			{
				$row=mysql_fetch_row($err);

				//reasigno el id del comentario, lo pongo en estado ingresado y le borro cosas y cambio otras
				$query="update " .$empresa."_000017 set id_Persona='".$doc."-".$tipDoc."', cconaco='', ccotusu='Usuario',  ccoatel='', ccoadir='', ccoaema='',  ccovol='SI', ccotres='', ccoaut='".strtoupper($aut)."', ccoest='INGRESADO', ccocemo='NO', ccocper='', ccocfec='0000-00-00', ccochor='', ccorobs='', ccoccli='', ccordes='', ccorfec='0000-00-00', ccorhor='', ccorper='', ccohis=''  ";
				$query=$query." where id= ".$idCom."  ";
				$err=mysql_query($query,$conex);

				//borro si tiene causas
				$query="delete from " .$empresa."_000023 ";
				$query=$query." where caccon= ".$idCom."  ";
				$err3=mysql_query($query,$conex);

				//borro si tiene implicados
				$query="delete from " .$empresa."_000027 ";
				$query=$query." where impnco= ".$idCom."  ";
				$err3=mysql_query($query,$conex);

				//consultamos los motivos del comentario para recorrerlos borrandolos y borrando sus acciones
				$query ="SELECT id, cmonum FROM " .$empresa."_000018 where id_comentario=".$idCom." ";
				// echo $query;
				$err2=mysql_query($query,$conex);
				$num2=mysql_num_rows($err2);

				for($i=0;$i<$num2;$i++)
				{
					$row2=mysql_fetch_row($err2);

					if ($row2[1]=='1')
					{
						//actualizo el motivo
						$query="update " .$empresa."_000018 set cmodes='.', id_Area='', id_Cargo='', cmonimp='', cmofenv='0000-00-00', cmofret='0000-00-00', cmosem='0000-00-00', cmover='', cmoinv='', cmoest='INGRESADO', cmocon=''  ";
						$query=$query."where id_comentario=".$idCom." and cmonum='1'  ";

						$err3=mysql_query($query,$conex);
					}
					else
					{
						//borro el motivo
						$query="delete from " .$empresa."_000018 ";
						$query=$query." where id_comentario=".$idCom." and cmonum='".$row2[1]."'  ";
						$err3=mysql_query($query,$conex);
					}
					// echo $query;
				}

				datosPaciente($doc,$tipDoc, $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $numCom, $aut, $idCom);
				echo "</br><center><b><font size='4'><font color='#00008B'>EL COMENTARIO PUEDE SOBRESCRIBIRSE CORRECTAMENTE</b></font></font></center></BR>";
				echo "<center>";
				echo "<a href='detalleComentario.php?idCom=".$idCom."' align='right'><<--Sobrescribir datos de Ingreso</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

			}
			else //la cedula ingresada no esta almacenada en matrix
			{
				echo '<script language="Javascript">';
				echo 'alert ("El paciente identificado con el numero de documento ingresado no se encuentra en la base de datos de Matrix")';
				echo '</script>';
				datosPaciente($doc,$tipDoc, $priNom, $segNom, $priApe, $segApe, $tel, $dir, $ema, $numCom, $aut, $idCom );

			}
		}
	}
}
?>
</body>
</html>

