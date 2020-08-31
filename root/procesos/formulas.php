<?php
include_once("conex.php");
echo "<form action='formulas.php' method=post>";
echo "<table border=0 align=center cellpadding=3>";
echo "<tr>";
echo "<td bgcolor=#cccccc>Factorial</td>";
echo "<td bgcolor=#cccccc><input type='text' name= 'factorial' ></td></tr>";
if (isset($factorial))
{
	$res = 1;
	for ($i=0;$i<$factorial;$i++)
    {
		 $res=$res*($i+1);
	 }
	 echo "<tr>";
	 echo "<td bgcolor=#cccccc colspan=2>EL FACTORIAL DE ".$factorial." ES = ".$res."</td>";
	 echo "</tr>";
 }
 echo "</table>";
?>