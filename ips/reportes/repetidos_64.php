<?php
include_once("conex.php");
include_once("root/comun.php");


$conex = obtenerConexionBD("matrix");
$query = "SELECT cumcod, cumint, cumemp, count(*) regs, max(id) id
			FROM root_000064
		   GROUP BY 1,2,3
		  HAVING (regs>1)";
$rs = mysql_query($query, $conex) or die(mysql_error());
$num = mysql_num_rows($rs);
for($i=0; $i<$num; $i++)
{
	$row=mysql_fetch_array($rs);
	$delete = "DELETE 
				FROM root_000064 
				WHERE id='{$row['id']}'";
	$rsdelete = mysql_query($delete);
}
echo "repetidos borrados";
?>