<?php
include_once("conex.php");

$wbasedato='cenpro';


or die("No se ralizo Conexion");




$q= "   UPDATE ".$wbasedato."_000009 "
."      SET Appexi = Appexi * Appcnv ";

$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA modificar la existencia en maestro 9".mysql_error());

$q= " SELECT Appcod, Sum(Appexi) "
."       FROM ".$wbasedato."_000009 "
."    Group by Appcod ";

$res = mysql_query($q,$conex);
$num = mysql_num_rows($res);

if ($num>0)
{
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($res);
		
		$q= "   UPDATE ".$wbasedato."_000005 "
		."      SET Karexi = '".$row[1]."' "
		."      Where Karcod='".$row[0]."' ";

		$err = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA modificar la existencia en maestor 5 ".mysql_error());
	}
	
	echo 'PROCESO FINALIZADO';
}
else
{
	echo 'No funciono el select';
}

?>
