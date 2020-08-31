<html>
<head>
<title>Afinidad</title>
</head>
<body >
<?php
include_once("conex.php");

/**
 * COMENTARIOS Y FAMILIARES AFINIDAD
 * 
 *	Muuestra los comentarios y los familiares de un paciente de afinidad, es un frame llamado por magenta.php
 *
 * @name matrix\magenta\procesos\comentarios.php
 * @author Ing. Ana María Betancur Vargas
 * @created 2005-12-07
 * @version 2007-01-19
 * 
 * @modified 2006-01-11  refinamiento del script y se unifica con familia.php, Carolina Castaño
 * @modified 2007-01-19  documentacion, Carolina Castaño
 * 
 * @table magenta_00008, select 
 * @table magenta_00016, select 
 * @table magenta_00017, select, 
 * @table magenta_00018, select,
 * @table magenta_00011, select, 
 * 
 * @wvar $doc, documento del paciente
 * @wvar $tipDoc, tipo de documento del paciente
 * @wvar $tipUsu, tipo de usuario
 * 
 */


/**
 * conexion a matrix
 */







$query="select clitip from magenta_000008 where clidoc='$doc' and clitid='$tipDoc' ";
$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);
if($num >0)
{
	$row=mysql_fetch_array($err);
	$tipUsu=$row['clitip'];
}else
{
	$tipUsu='00-NO CLASIFICADO-99';
}

if (substr ($tipUsu,0,3)=='VIP')
{
	echo "<table border='0'  align=center>";
	echo "<tr><td align=center><font size=3  face='arial'><b>COMENTARIOS</td></tr>";
	echo "<tr><td><fieldset style='border:solid;border-color:#000080 ; width=320' ; color=#000080; align='right'>";
	echo "<table border='1' width='320'>";

	$query="select A.id, B.id, B.ccofori, C.cmodes from magenta_000016 A, magenta_000017 B, magenta_000018 C where A.Cpedoc='".$doc."' and A.Cpetdoc='".$tipDoc."' and B.id_persona='".$doc."-".$tipDoc."' and C.id_comentario=B.id ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	if($num >0)
	{
		/*Impresión en pantalla*/
		for($i=0;$i<$num;$i++)	{
			$row=mysql_fetch_row($err);
			echo "<tr><td><font size=2  face='arial' ><b>".$row[2]."</b><br>";
			echo $row[3]."</td></tr>";
		}
	}
	else{
		echo "<tr><td><font size=2  face='arial' >No Tiene Comentarios</td></tr>";
	}
	echo "</table></td></tr></table>";


}else
{
	echo "<table border='0' width='320' align= center>";
	echo "<tr>";
	echo "<td width='105'><font size=3  face='arial' color='#000080'><b>COMENTARIOS</td>";
	echo "<td width='10'><font size=3  face='arial' color='#000080'><b>&nbsp;&nbsp;&nbsp;</td>";
	echo "<td width='105'><font size=3  face='arial' color='#000080'><b>FAMILIARES</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td><fieldset style='border:solid;border-color#000080; width=320' ; color=#000080; align='center'>";

	echo "<table border='1' width='320'>";
	$query="select A.id, B.id, B.ccofori, C.cmodes from magenta_000016 A, magenta_000017 B, magenta_000018 C where A.Cpedoc='".$doc."' and A.Cpetdoc='".$tipDoc."' and B.id_persona='".$doc."-".$tipDoc."' and C.id_comentario=B.id ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	if($num >0)
	{
		/*Impresión en pantalla*/
		for($i=0;$i<$num;$i++)	{
			$row=mysql_fetch_row($err);
			echo "<tr><td><font size=2  face='arial' ><b>".$row[2]."</b><br>";
			echo $row[3]."</td></tr>";
		}
	}
	else{
		echo "<tr><td><font size=2  face='arial' >No Tiene Comentarios</td></tr>";



	}
	echo "</table>";
	echo "</fieldset></td>";

	echo "<td><font size=3  face='arial' color='#000080'><b>&nbsp;&nbsp;&nbsp;</td>";

	echo "<td><fieldset style='border:solid;border-color#000080; width=320' ; color=#000080; align='center'>";

	$query="select * from magenta_000011 where Cafpdo='$doc' and Cafpti='$tipDoc'";
	//echo $query."<br>";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo mysql_errno().": ".mysql_error()."<br>";
	if($num >0)
	{
		/*Impresión en pantalla*/
		for($i=0;$i<$num;$i++)	{

			$row=mysql_fetch_array($err);
			$q="select Clinom,Cliap1,Cliap2, Clidoc,Clitid from magenta_000008 where Clidoc='".$row['Cafrdo']."' and Clitid='".$row['Cafrti']."'";//Substring(Clitid,1,2)='".substr($row['Cafrti'],0,2)."'";
			$err1=mysql_query($q,$conex);
			$num1=mysql_num_rows($err1);
			if($num1 >0) {
				/*Impresión en pantalla*/
				for($f=0;$f<$num1;$f++)	{

					$row1=mysql_fetch_array($err1);
					echo "&nbsp;<font size=2  face='arial' ><b>".ucfirst($row['Cafrel']).":</b> ".ucfirst($row1['Clinom'])." ";
					echo ucfirst($row1['Cliap1'])." ".ucfirst($row1['Cliap2']);
					echo " (<A HREF='Magenta.php?doc=".$row1['Clidoc']."&amp;tipDoc=".$row1['Clitid']."&amp;cco=".$cco."' target='blank'>";
					echo substr($row1['Clitid'],0,2)." ".$row1['Clidoc'].")</a></b></br>";
				}
			}
		}
		/*Fin Información familia*/

	}
	else
	{
		echo "&nbsp;<font size=2  face='arial' >No esta Registrado en MATRIX</br>";
	}
	echo "</fieldset>";
	include_once("free.php");
}

?>
</body>
</html>
	
