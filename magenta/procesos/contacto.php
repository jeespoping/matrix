<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de comentarios y sugerencias</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	
<script src="efecto.php"></script>
<script>
        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
            'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);

</script>
<script type="text/javascript">
$(document).ready(function() {

	$("#fecCon").datepicker({
       showOn: "button",
       buttonImage: "../../images/medical/root/calendar.gif",
       buttonImageOnly: true,
       maxDate:"+1D"
    });
});

</script> 
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->
</SCRIPT>

</head>
<body>
<?php
include_once("conex.php"); 

/**
 * 	CONTACTO DE UN COMENTARIO
 * 
 * Este programa permite ingresar a un comentario un contacto previo de aclaracion al comentario
 * 
 * @name  matrix\magenta\procesos\contacto.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-05-03
 * @version 2006-01-09
 * 
 * @modified 2007-01-09  Se realiza documentacion del programa y calendario
 
 Actualizacion: Se organizo la consulta de la linea 187 la cual compara los documentos de las personas con comentarios, en el where comparaba con 
 el id de la tabla y no con la cedula de la persona. Viviana Rodas 2012-05-16
 * 
 * @table magenta_000008, select, 
 * @table magenta_000016, select, 
 * @table magenta_000017, select, update
 * @table magenta_000018, select, update
 * 
 *  @wvar $acoDir= direccion del aconmpanante
 *  @wvar $acoEma= email del acompanante
 *  @wvar $acoNom= nombre del acompananate
 *  @wvar $acoTel= telefono del acompanante
 *  @wvar $aut perona que ingreso el comentario
 *  @wvar $bandera1  Si no está setiada indica si es la primera vez que se entra a la pagina, indica los pasos de la pagina
 *  @wvar $cadena organiza la url para enviar a datos del paciente
 *  @wvar $cadena2 organiza la url para enviar a comentarios del paciente
 *  @wvar $cliCon cliente que recibio la llamada de contacto
 *  @wvar $color con el que se mostrara el tipo de usuario en pantalla, AAA magenta, el resto azul
 *  @wvar $dir direccion
 *  @wvar $doc=documento de identidad
 *  @wvar $ema email
 *  @wvar $emo si tiene contacto emocional
 *  @wvar $ent entidad
 *  @wvar $fecCon fecha del contacto
 *  @wvar $fecOri fecha de origen
 *  @wvar $fecRec fecha de recepcion del comentario
 *  @wvar $his historia clinica
 *  @wvar $horCon hora del contacto
 *  @wvar $idCom id del comentario en tabla 17
 *  @wvar $idPac id del paciente en tabla 16
 *  @wvar $inicial, indica si se cumplen las validaciones de ingreso de datos
 *  @wvar $lugOri lugar de origen
 *  @wvar $motCla vector de clasificacion de motivos
 *  @wvar $motCon vector de contactos de motivos
 *  @wvar $motDes vector de descripcion de motivos
 *  @wvar $motEst vector de estado de motivos
 *  @wvar $motNum vector de numero de motivos
 *  @wvar $motTip vector de tipo de motivos
 *  @wvar $perCon persona que realizo el contacto
 *  @wvar $perDil persona que diligencio el comentario
 *  @wvar $priApe primer apellido
 *  @wvar $priNom primer nombre 
 *  @wvar $segApe segundo apellido
 *  @wvar $segNom segundo nombre
 *  @wvar $tamano sabe cuantos motivos hay para el comentario
 *  @wvar $tel telefono
 *  @wvar $tipDoc tipo de documento
 *  @wvar $tipUsu tipo deusuario para afinidad
 *  @wvar $vol volveria a la clinica
*/
/********************************************************************************************************************************** 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
 						-Se Modifica el campo de fecha con utilidad jquery y se elimina uso de Zapatec Calendar por errores en la clase.
 						-Se cambia encabezado con ultimo diseño y se configura color de titulos con la clase 'fila1'

*********************************************************************************************************************************/
$wautor="Carolina Castano P.";
$wversion='2007-01-09';
$wactualiz='2016-05-03';
/***************************************************** funciones ************************************/

/*Si la variable tiene como valor dato no encontrado  retorna vacío*/
function  DisplayError()
{
	echo '<script language="Javascript">';
	echo 'alert ("Error al conectarse con la base de datos, intente más tarde")';
	echo '</script>';

}
/****************************************** PROGRAMA ************************************************/
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

	//deben mandarme el id del comentario, con eso busco los datos del paciente y los datos del comentario
	if (isset ($idCom))
	{
		$query ="SELECT id_persona, ccoori, ccofori, ccofrec, cconaco, ccotusu, ccoatel, ccoadir, ccoaema, ccoaut, ccocemo, ccoent, ccovol, ccocper, ccocfec, ccochor, ccoccli, ccohis, cconum FROM " .$empresa."_000017 where id=".$idCom." ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		//echo $query;
		if  ($num >0) //se llena los valores
		{
			$row=mysql_fetch_row($err);
			$idPac=$row[0];
			$lugOri=$row[1];
			$fecOri=$row[2];
			$fecRec=$row[3];
			$ent=$row[9];
			$perDil=$row[5];
			$acoNom=$row[4];
			$acoDir=$row[7];
			$acoTel=$row[6];
			$acoEma=$row[8];
			$aut=$row[9];
			$emo=$row[10];
			$ent=$row[11];
			$vol=$row[12];
			$his=$row[17];
			$numCom=$row[18];

			if ($emo=='SI' and !isset($bandera1))
			{
				$perCon=$row[13];
				$fecCon=$row[14];
				$horCon=$row[15];
				$cliCon=$row[16];
			}

			$pos = explode("-",$idPac);
			$idPac1 = $pos[0];
			//busco los datos del paciente
			$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpedir, cpetel, cpeema FROM " .$empresa."_000016 where Cpedoc=".$idPac1." ";
			$err=mysql_query($query,$conex) or die (mysql_errno()." - en el query ".$query ." " .mysql_error());
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
				DisplayError();
			}

			//busco los datos de los motivos para el comentario
			$query ="SELECT cmonum, cmotip, cmodes, cmocla, cmoest, cmocon FROM " .$empresa."_000018 where id_comentario=".$idCom." ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores
			{
				$tamano = $num;

				for($i=0;$i<$num;$i++)
				{
					$row=mysql_fetch_row($err);
					$motNum[$i]=$row[0];
					$motTip[$i]=$row[1];
					$motDes[$i]=$row[2];
					$motCla[$i]=$row[3];
					$motEst[$i]=$row[4];

					if ($emo=='SI')
					$motCon[$i]=$row[5];

				}

			}else
			{
				DisplayError();
			}
		}else
		{
			DisplayError();
		}

	}else
	{
		DisplayError();
	}

	if (!isset ($bandera1) and $emo=='NO') //si es primera vez que entro y no hay contacto, inicializo los datos del formulario
	{
		$perCon='';
		$fecCon='';
		$horCon='';
		$cliCon='';

		for($i=0;$i<$tamano;$i++)
		{
			//$row=mysql_fetch_row($err);
			$motCon[$i]='';
		}

	}else if (isset ($bandera1))//si es la segunda vez, despliego datos del formulario
	{
		for($i=0;$i<$tamano;$i++)
		{
			eval("\$motCon[$i] = \$motCon$i;");
		}

		//validaciones de ingreso de datos

		$inicial=0;
		if ($perCon == '')
		$inicial=4;
		if ($horCon == '' and $inicial == 0)
		$inicial=4;
		if ($cliCon == ''and $inicial == 0)
		$inicial=4;


		if ($inicial==4) //despligue de errores para las validaciones
		{
			echo '<script language="Javascript">';
			echo 'alert ("Debe ingresar información en todos los datos generales del contacto emocional: Realizado por, Fecha, Hora y Con quien habló")';
			echo '</script>';
		}

		if ($inicial==0 ) // almacenamiento del contacto
		{
			$emo='SI';
			$query="update " .$empresa."_000017 set ccocemo='".$emo."', ccocper='".$perCon."', ccocfec ='".$fecCon."', ccochor='".$horCon."', ccoccli='".$cliCon."' ";
			$query=$query." where id= ".$idCom."  ";
			//echo $query;
			$err=mysql_query($query,$conex);

			//hago update de todos los motivos donde se agrega

			for($i=0;$i<$tamano;$i++)
			{
				if ($motCon[$i]!='')
				{
					$query="update " .$empresa."_000018 set cmocon='".$motCon[$i]."' ";
					$query=$query."where id_comentario=".$idCom." and cmonum=".$motNum[$i]." ";
					//echo $query;
					$err=mysql_query($query,$conex);
				}
			}

		}

	}

	//Busco en base de datos de afinidad el tipo de Usuario de la persona, para mostrar
	$query ="SELECT clitip FROM " .$empresa."_000008 where clidoc='$doc' and clitid='$tipDoc' ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo $query;
	if  ($num >0) //se llena los valores y un vector con los resultados
	{
		$row=mysql_fetch_row($err);

		$inicial = explode ('-',$row[0]);
		if ($inicial[2] == '1' and $inicial[0] != 'VIP')
		$color='EA198E';
		else
		$color='0000FF';

		if ($inicial[0] == 'VIP')
		$tipUsu=$inicial[0];
		else
		$tipUsu=$inicial[1];
	}else
	{
		$tipUsu='NO CLASIFICADO';
		$color='0000FF';
	}


	$cadena="'pagina1.php?matrix=".$doc."-".$tipDoc."&bandera=3&his=-&res=-&ser=-'";
	$cadena2="'comentario.php?doc=".$doc."&tipDoc=".$tipDoc."&priNom=".$priNom."&segNom=".$segNom."&priApe=".$priApe."&segApe=".$segApe."&tel=".$tel."&dir=".$dir."&ema=".$ema."&his=-&res=-&ser=-'";

	//echo $cadena;
  ?>
  <SCRIPT LANGUAGE="JavaScript1.2">

  if (document.all) {

  	window.myMenu = new Menu();
  	myMenu.addMenuItem("my menu item A");
  	myMenu.addMenuItem("my menu item B");
  	myMenu.addMenuItem("my menu item C");
  	myMenu.addMenuItem("my menu item D");

  	window.mWhite1 = new Menu("White");
  	mWhite1.addMenuItem("Ingreso de Comentarios", "top.window.location='pagina1.php'");
  	mWhite1.addMenuItem("Lista de comentarios", "top.window.location='listaMagenta.php'");
  	<?php
  	echo 'mWhite1.addMenuItem("Datos del paciente", "top.window.location='.$cadena.'");';
  	echo 'mWhite1.addMenuItem("Comentarios del paciente", "top.window.location='.$cadena2.'");';
  	?>
  	mWhite1.bgColor = "#ADD8E6";
  	mWhite1.menuItemBgColor = "white";
  	mWhite1.menuHiliteBgColor = "#336699";

  	myMenu.writeMenus();
  }

	</SCRIPT>
 <?php
 ///////////////////////////////////////////////// Encabezado general ///////////////////////////////////
 echo "<br></br>";
 echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>CONTACTO EMOCIONAL</b></font></div></div></BR>";
 //echo "<center><b><font size=\"4\"><A HREF='contacto.php?idCom=$idCom'><font color=\"#D02090\">CONTACTO EMOCIONAL</font></a></b></font></center>\n" ;
 //echo "<center><b><font size=\"2\"><font color=\"#D02090\"> contacto.php</font></font></center></br></br></br>\n" ;
 echo "<br></br>";
 echo "\n" ;

 ///////////////////////////////////////////////// Presentación general //////////////////////////////////

 echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
 echo "<table align='center'  width='100%'>";
 echo "<tr>";
 echo "<td rowspan=4><b><font size='3'><font color='#00008B'> DATOS DEL PACIENTE:</font></font></td>";
 echo "<td> <b><font size='3'  color='#00008B'>Documento:</font></b>$doc-".substr($tipDoc,0,2)."</td>";

 echo "<td><b><font size='3'  color='#00008B'>Nombre:&nbsp;</font></b>$priNom ";

 if ($segNom != ' ' and $segNom != '' and  $segNom != '- -')
 echo "$segNom ";

 echo "$priApe ";

 if ($segApe != ' ' and $segApe != '' and  $segApe != '- -')
 echo "$segApe";

 echo "</td>";
 echo "</tr>";

 echo "<tr>";
 echo "<td><b><font size='3'  color='#00008B'> Telefono: </font></b>$tel</td>";
 echo "<td><b><font size='3'  color='#00008B'>Direccion: </font></b>$dir</td>";
 echo "</tr>";
 echo "<tr>";

 echo "<td><b><font size='3'  color='#00008B'> Email: </font></b>$ema</td>";
 echo "<td><b><font size='3'  color='#00008B'>AFINIDAD:</font></b> <font color='#$color'>$tipUsu</font></td>";
 echo "</tr>";
 echo "</table></fieldset></br></br></br>";

 if ($perDil == 'Acompanante')
 {
 	echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
 	echo "<table align='center' width=100%'>";
 	echo "<tr>";
 	echo "<td rowspan=2><b><font size='3'><font color='#00008B'> DATOS DEL ACOMPAÑANTE:</font></font></td>";
 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'>Nombre: </font></b>$acoNom</td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'>Telefono: </font></b>$acoTel</font></td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "</tr>";
 	echo "<tr>";

 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'>Direccion: </font></b> $acoDir</td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "<td>&nbsp;&nbsp;<b><font size='3' color='#00008B'> Email: </font></b>$acoEma</td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 	echo "</tr>";
 	echo "</table></fieldset></br></br>";
 }

 echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
 echo "<table align='center'  width=100%'>";
 echo "<tr>";
 echo "<td rowspan=2><b><font size='3' ><font color='#00008B'> DATOS COMENTARIO:</font></font></td>";
 echo "<td colspan='1'><b><font size='3' color='#00008B'>Fecha Origen: </font></b>$fecOri</td>";
 echo "<td  colspan='1'> <b><font size='3' color='#00008B'>Lugar Origen: </font></b> $lugOri</td>";

 echo "</tr>";

 echo "<tr>";
 echo "<td><b><font size='3' color='#00008B'> Fecha Rec.: </font></b>$fecRec</td>";
 echo "<td><b><font size='3' color='#00008B'>Entidad:</font></b>$ent</td>";

 echo "</tr>";
 echo "<tr>";
 echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 echo "<td><b><font size='3' color='#00008B'> Volveria: </font></b>$vol</td>";
 echo "<td><b><font size='3' color='#00008B'> Historia: </font></b>$his</td>";
 echo "</tr>";
 echo "</table></fieldset></br></br></br>";

 
 echo "<form action='contacto.php' method='post' name='forma'>";


 echo "<center><b><font size='4'><font color='#00008B'>CONTACTO EMOCIONAL PARA COMENTARIO NUMERO: ".$numCom."</b></font></font></center></BR>";
 echo "<center><b><font size='3'><font color='#00008B'>PERSONA QUE INGRESO EL COMENTARIO: ".$aut."</b></font></font></center></br>";

 echo "<form action='contacto.php' method='post' name='form'>";

 echo "<table align='center'>";
 echo "<tr>";
 echo "<td>REALIZADO POR </td>";
 echo "<td><input type='text' name='perCon' value='$perCon' /></td>";
 echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 echo "<td>FECHA:</td>";
 //$cal="calendario('fecCon','1')";
 echo "<td align=center ></font><input type='text' readonly='readonly' id='fecCon' name='fecCon' value='".$fecCon."' class=tipo3 ></td>";
 echo "</tr>";
 echo "<tr>";
 echo "<td>HORA: </td>";
 echo "<td><input type='text' name='horCon' value='$horCon' /></td>";
 echo "<td><b><font size='3'>&nbsp;</font></font></td>";
 echo "<td>CON QUIEN HABLO:</td>";
 echo "<td><input type='text' name='cliCon' value='$cliCon' /></td>";
 echo "</tr>";
 echo "</table></BR>";

 echo "<CENTER><b><font size='3'><font color='#00008B'>INFORMACION POR MOTIVO</b></font></font></BR></BR>";
 echo "<table align='center' border='1' width='100%'>";
 echo "<tr >";
 echo "<td bgcolor='#336699' width='15%' align='center' class='fila1' ><b><font color='#00008B'>Nº Motivo</font></b></td>";
 echo "<td bgcolor='##336699' width='10%' align='center' class='fila1'><b><font color='#00008B'>Tipo</font></font></b></td>";
 echo "<td bgcolor='#336699' width='15%' align='center' class='fila1'><b><font color='#00008B'>Clasificacion</font></b></td>";
 echo "<td bgcolor='#336699' width='30%' align='center' class='fila1'><b><font color='#00008B'>Descripcion</font></b></td>";
 echo "<td bgcolor='#336699' width='30%' align='center' class='fila1'><b><font color='#00008B'>Informacion adicional</font></b></td>";
 echo "</tr>";

for($i=0;$i<$tamano;$i++)
{
	if ($motTip[$i]=='Agrado')
	$color='#EOFFFF';
	else
	$color="#cccccc";


	echo "<tr>";
	echo "<td bgcolor='$color' width='15%' align='center'>".$motNum[$i]."</td>";
	echo "<td bgcolor='$color' width='10%' align='center'>".$motTip[$i]."</td>";
	echo "<td bgcolor='$color' width='15%' align='center'>".$motCla[$i]."</td>";
	echo "<td bgcolor='$color' width='15%' align='center'>".$motDes[$i]."</td>";
	echo "<td bgcolor='$color' width='15%' align='center'><textarea rows='4' cols='50' name='motCon".$i."' style='font-family: Arial; font-size:14'>".$motCon[$i]."</textarea></td>";
	echo "<input type='hidden' name='motDes".$i."'  value='".$motDes[$i]."' />";
	echo "</tr>";

}
echo "</table></br></br>";
echo "<input type='hidden' name='bandera1' value='1' />";
echo "<input type='hidden' name='tamano' value='$tamano' />";
echo "<input type='hidden' name='idCom' value='$idCom' />";
echo "<input type='hidden' name='emo' value='SI' />";
echo "<input type='submit'  name='GUARDAR' value='GUARDAR'/></CENTER></BR>";

echo "</FORM>";

echo "<center>";
echo "<a href='detalleComentario.php?idCom=".$idCom."' align='right'><<--Ingreso de comentario</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<a href='asignacion.php?idCom=$idCom' align='right'>Asignacion-->></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "</center>";


}
?>

</body>

</html>




