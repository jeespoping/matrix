<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Envio de correo por cambio en semaforizacion</title>
 
</head>
<body>
<?php
include_once("conex.php"); 

/**
 * 
 * prgrama para enviar correos de semaforizacion a los comentarios que cambian de color
 * 
 * @name  matrix\magenta\procesos\semaforizacion.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2008-01-24
 * @version 2008-01-24
 * 
 * @modified 2008-01-24  Creacion
 * @modificado 2009-02-27  Msanchez::Modificado uso de clase phpmailer y smtp con autenticacion 
 */

//Cuadramos las fechas de envio a partir de las cuales hay cambio en semaforización
//primero dias totales






$empresa='magenta';

/**
	 * clase encargada de realizar el envio de correo
	 * se invoca de una libreria instalada en el c
	 */
require("class.phpmailer.php");
$mail = new PHPMailer();

$mail->SMTPAuth = true;							//Indica que se requiere Autenticación

$mail->Username = "magenta";					//Indica el nombre del usuario con el que se realiza la Autenticación
$mail->Password = "servmagenta";				 

/////////////////////////////////////////////////encabezado general///////////////////////////////////
echo "<table align='center' border='3' bgcolor='#336699' >\n" ;
echo "<tr>" ;
echo "<td><img src='/matrix/images/medical/root/magenta.gif' height='61' width='113'></td>";
echo "<td><font color=\"#ffffff\"><font size=\"5\"><b>&nbsp;SISTEMA DE COMENTARIOS Y SUGERENCIAS &nbsp;</br></b></font></font></td>" ;
echo "</tr>" ;
echo "</table></br></br>" ;

echo "<center><b><font size=\"4\">ENVIO DE EMAIL POR CAMBIO EN INDICADOR DE SEMAFORIZACION</b></font></center>\n" ;
echo "<center><b><font size=\"2\"><font color=\"#D02090\">semaforizacion.php</font></font></center></br></br></br>\n" ;
echo "\n" ;

/////////////////////////////////////////////////encabezado general///////////////////////////////////

$fecha1=mktime(0, 0, 0, date('m'), date('d'), date('Y')) - (3 * 24 * 60 * 60);
$fecha1= date('Y-m-d', $fecha1);

$fecha2=mktime(0, 0, 0, date('m'), date('d'), date('Y')) - (10 * 24 * 60 * 60);
$fecha2= date('Y-m-d', $fecha2);

$fecha3=mktime(0, 0, 0, date('m'), date('d'), date('Y')) - (24 * 24 * 60 * 60);
$fecha3= date('Y-m-d', $fecha3);

//ahora consultamos los festivos entre las fechas para sumarlos
$query= "select cdia, cfec  from " .$empresa."_000026 where cfec between '".$fecha1."' and '".date('Y-m-d')."' order by 2 desc " ;
$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);
if  ($num >0) //se llena los valores
{
	$row=mysql_fetch_row($err);
	$can=$num;
	if(($num==1 or $num==2) and $row[0]=='lunes')
	{
		$can=3;
	}

	if($num==1 and $row[0]=='domingo')
	{
		$can=2;
	}
	$row=mysql_fetch_row($err);
	$exp=explode('-', $fecha1);
	$fecha1=mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]) - (($can) * 24 * 60 * 60);
	$fecha1= date('Y-m-d', $fecha1);

}

$query= "select cdia, cfec from " .$empresa."_000026 where cfec between '".$fecha2."' and '".date('Y-m-d')."' order by 2 desc  " ;
$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);
if  ($num >0) //se llena los valores
{
	$row=mysql_fetch_row($err);
	$can=$num;
	if(($num==1 or $num==2) and $row[0]=='lunes')
	{
		$can=3;
	}

	if($num==1 and $row[0]=='domingo')
	{
		$can=2;
	}
	$exp=explode('-', $fecha2);
	$fecha2=mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]) - (($can) * 24 * 60 * 60);
	$fecha2= date('Y-m-d', $fecha2);
}

$query= "select cdia, cfec from " .$empresa."_000026 where cfec between '".$fecha3."' and '".date('Y-m-d')."' order by 2 desc  " ;
$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);
if  ($num >0) //se llena los valores
{
	$row=mysql_fetch_row($err);
	$can=$num;
	if(($num==1 or $num==2) and $row[0]=='lunes')
	{
		$can=3;
	}

	if($num==1 and $row[0]=='domingo')
	{
		$can=2;
	}
	$exp=explode('-', $fecha3);
	$fecha3=mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]) - (($can)* 24 * 60 * 60);
	$fecha3= date('Y-m-d', $fecha3);
}

//primero buscamos de verde a amarillo periodo corto
$query ="SELECT Carnom, Crecar, Crenom, Cremai, Cmonum, Cconum  ";
$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D, " .$empresa."_000017 E ";
$query= $query. "where cmofenv='".$fecha1."' ";
$query= $query. "and   cmoest in ('ASIGNADO', 'TRAMITANDO')  ";
$query= $query. "and   cmotip='Desagrado'   ";
$query= $query. "and B.carcod=A.id_area ";
$query= $query. "and carsem=1 ";
$query= $query. "and carest='on' ";
$query= $query. "and   C.id_area=A.id_area and carniv=1 ";
$query= $query. "and D.crecod=C.id_responsable ";
$query= $query. "and A.id_comentario=E.id ";

$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);

for($i=0; $i<$num; $i++)
{
	$row=mysql_fetch_row($err);

	if($row[3]=='')
	{
		$row[3]='ccastano@pmamericas.com';
	}

	if($i==0)
	{
		echo "<table align='center' border='1' width='90%'>";
		echo "<tr><td bgcolor='#336699' width='8%' align='center' COLSPAN='3'><font color='#ffffff' size='2'>COMENTARIOS EN AMARILLO</font></td>";
		echo "<tr><td bgcolor='#336699' width='8%' align='center'><font color='#ffffff' size='2'>COMENTARIO</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff' size='2'>AREA</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center' ><font color='#ffffff' size='2'>ENVIO</font></td></tr>";
	}
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host = "132.1.18.1"; // SMTP server
	$mail->From = "magenta@lasamericas.com.co";
	$mail->AddAddress($row[3]);

	$mail->Subject = "COMENTARIO EN AMARILLO";
	$mail->Body = "Cordial Saludo ! \n\n El comentario (".$row[5]."-".$row[4].") del área de ".$row[0]." cambiará a amarillo mañana (o próximo día hábil), lo invitamos a tramitarlo!";
	$mail->WordWrap = 100;

	if(!$mail->Send())
	{
		echo "<tr><td width='40%' align='left' ><font color='#336699' size='2'>".$row[5]."-".$row[4]."</font></td>";
		echo "<td width='10%' align='left'><font color='#336699' size='2'>".$row[0]."</font></td>";
		echo "<td width='30%' align='left' ><font color='#336699' size='2'>&nbsp;</font></td></tr>";
	}
	else
	{
		echo "<tr><td  width='40%' align='left' ><font color='#336699' size='2'>".$row[5]."-".$row[4]."</font></td>";
		echo "<td width='10%' align='left'><font color='#336699' size='2'>".$row[0]."</font></td>";
		echo "<td  width='30%' align='left' ><font color='#336699' size='2'>".$row[3]."</font></td></tr>";
	}
	$mail->ClearAddresses();
}
//buscamos de verde a amarillo periodo largo

$query ="SELECT Carnom, Crecar, Crenom, Cremai, Cmonum, Cconum  ";
$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D, " .$empresa."_000017 E ";
$query= $query. "where cmofenv='".$fecha2."' ";
$query= $query. "and   cmoest in ('ASIGNADO', 'TRAMITANDO')  ";
$query= $query. "and   cmotip='Desagrado'   ";
$query= $query. "and B.carcod=A.id_area ";
$query= $query. "and carsem=2 ";
$query= $query. "and carest='on' ";
$query= $query. "and   C.id_area=A.id_area and carniv=1 ";
$query= $query. "and D.crecod=C.id_responsable ";
$query= $query. "and A.id_comentario=E.id ";


$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);
for($i=0; $i<$num; $i++)
{
	$row=mysql_fetch_row($err);

	if($row[3]=='')
	{
		$row[3]='ccastano@pmamericas.com';
	}
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host = "132.1.18.1"; // SMTP server
	$mail->From = "magenta@lasamericas.com.co";
	$mail->AddAddress($row[3]);

	$mail->Subject = "COMENTARIO EN AMARILLO";
	$mail->Body = "Cordial Saludo ! \n\n El comentario (".$row[5]."-".$row[4].") del área de ".$row[0]." cambiará a amarillo mañana (o próximo día hábil), lo invitamos a tramitarlo!";
	$mail->WordWrap = 100;

	if(!$mail->Send())
	{
		echo "<tr><td width='40%' align='left' ><font color='#336699' size='2'>".$row[5]."-".$row[4]."</font></td>";
		echo "<td width='10%' align='left'><font color='#336699' size='2'>".$row[0]."</font></td>";
		echo "<td width='30%' align='left' ><font color='#336699' size='2'>&nbsp;</font></td></tr>";
	}
	else
	{
		echo "<tr><td  width='40%' align='left' ><font color='#336699' size='2'>".$row[5]."-".$row[4]."</font></td>";
		echo "<td width='10%' align='left'><font color='#336699' size='2'>".$row[0]."</font></td>";
		echo "<td  width='30%' align='left' ><font color='#336699' size='2'>".$row[3]."</font></td></tr>";
	}
	$mail->ClearAddresses();
}

echo "</table>";

//buscamos de amarillo a rojo periodo corto

$query ="SELECT Carnom, Crecar, Crenom, Cremai, Cmonum, Cconum  ";
$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D, " .$empresa."_000017 E ";
$query= $query. "where cmofenv='".$fecha2."' ";
$query= $query. "and   cmoest in ('ASIGNADO', 'TRAMITANDO')  ";
$query= $query. "and   cmotip='Desagrado'   ";
$query= $query. "and B.carcod=A.id_area ";
$query= $query. "and carsem=1 ";
$query= $query. "and carest='on' ";
$query= $query. "and   C.id_area=A.id_area and carniv=1 ";
$query= $query. "and D.crecod=C.id_responsable ";
$query= $query. "and A.id_comentario=E.id ";

$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);

for($i=0; $i<$num; $i++)
{
	$row=mysql_fetch_row($err);

	if($row[3]=='')
	{
		$row[3]='ccastano@pmamericas.com';
	}
	if($i==0)
	{
		echo "<table align='center' border='1' width='90%'>";
		echo "<tr><td bgcolor='#336699' width='8%' align='center' COLSPAN='3'><font color='#ffffff' size='2'>COMENTARIOS EN ROJO</font></td>";
		echo "<tr><td bgcolor='#336699' width='8%' align='center'><font color='#ffffff' size='2'>COMENTARIO</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff' size='2'>AREA</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center' ><font color='#ffffff' size='2'>ENVIO</font></td></tr>";
	}
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host = "132.1.18.1"; // SMTP server
	$mail->From = "magenta@lasamericas.com.co";
	$mail->AddAddress($row[3]);

	$mail->Subject = "COMENTARIO EN ROJO";
	$mail->Body = "Cordial Saludo ! \n\n El comentario (".$row[5]."-".$row[4].") del área de ".$row[0]." cambiará a rojo mañana (o próximo día hábil), lo invitamos a tramitarlo!";
	$mail->WordWrap = 100;

	if(!$mail->Send())
	{
		echo "<tr><td width='40%' align='left' ><font color='#336699' size='2'>".$row[5]."-".$row[4]."</font></td>";
		echo "<td width='10%' align='left'><font color='#336699' size='2'>".$row[0]."</font></td>";
		echo "<td width='30%' align='left' ><font color='#336699' size='2'>&nbsp;</font></td></tr>";
	}
	else
	{
		echo "<tr><td  width='40%' align='left' ><font color='#336699' size='2'>".$row[5]."-".$row[4]."</font></td>";
		echo "<td width='10%' align='left'><font color='#336699' size='2'>".$row[0]."</font></td>";
		echo "<td  width='30%' align='left' ><font color='#336699' size='2'>".$row[3]."</font></td></tr>";
	}
	$mail->ClearAddresses();
}

//buscamos de amarillo a rojo periodo largo

$query ="SELECT Carnom, Crecar, Crenom, Cremai, Cmonum, Cconum  ";
$query= $query. "FROM " .$empresa."_000018 A, " .$empresa."_000019 B, " .$empresa."_000020 C, " .$empresa."_000021 D, " .$empresa."_000017 E ";
$query= $query. "where cmofenv='".$fecha3."' ";
$query= $query. "and   cmoest in ('ASIGNADO', 'TRAMITANDO')  ";
$query= $query. "and   cmotip='Desagrado'   ";
$query= $query. "and B.carcod=A.id_area ";
$query= $query. "and carsem=1 ";
$query= $query. "and carest='on' ";
$query= $query. "and   C.id_area=A.id_area and carniv=2 ";
$query= $query. "and D.crecod=C.id_responsable ";
$query= $query. "and A.id_comentario=E.id ";


$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);
for($i=0; $i<$num; $i++)
{
	$row=mysql_fetch_row($err);

	if($row[3]=='')
	{
		$row[3]='ccastano@pmamericas.com';
	}
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host = "132.1.18.1"; // SMTP server
	$mail->From = "magenta@lasamericas.com.co";
	$mail->AddAddress($row[3]);

	$mail->Subject = "COMENTARIO EN ROJO";
	$mail->Body = "Cordial Saludo ! \n\n El comentario (".$row[5]."-".$row[4].") del área de ".$row[0]." cambiará a rojo mañana (o próximo día hábil), lo invitamos a tramitarlo!";
	$mail->WordWrap = 100;

	if(!$mail->Send())
	{
		echo "<tr><td width='40%' align='left' ><font color='#336699' size='2'>".$row[5]."-".$row[4]."</font></td>";
		echo "<td width='10%' align='left'><font color='#336699' size='2'>".$row[0]."</font></td>";
		echo "<td width='30%' align='left' ><font color='#336699' size='2'>&nbsp;</font></td></tr>";
	}
	else
	{
		echo "<tr><td  width='40%' align='left' ><font color='#336699' size='2'>".$row[5]."-".$row[4]."</font></td>";
		echo "<td width='10%' align='left'><font color='#336699' size='2'>".$row[0]."</font></td>";
		echo "<td  width='30%' align='left' ><font color='#336699' size='2'>".$row[3]."</font></td></tr>";
	}
	$mail->ClearAddresses();
}
echo "</table>";
?>

</body>
</html>