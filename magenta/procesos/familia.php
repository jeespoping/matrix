<html>
<head>
<title>FAMILIA</title>
</head>
<body >
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	

	

	echo "<font size=3  face='arial' color='#000080'><b>FAMILIARES<BR></font>";
			echo "<fieldset style='border:solid;border-color:; width=315' ; color=#000080>";
			echo "<table border='1' width='315'>";
	
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
					echo "<tr><td><font size=2  face='arial' ><b>".ucfirst($row['Cafrel']).":</b> ".ucfirst($row1['Clinom'])." ";
					echo ucfirst($row1['Cliap1'])." ".ucfirst($row1['Cliap2']);
					echo " (<A HREF='Magenta.php?doc=".$row1['Clidoc']."&amp;tipDoc=".$row1['Clitid']."' target='blank'>";
					echo substr($row1['Clitid'],0,2)." ".$row1['Clidoc'].")</a></b></td></tr>";
				}
			}
		}
		/*Fin Información familia*/

	}
	else
	{
		echo "<tr><td><font size=2  face='arial' ><b>No esta Registrado en MATRIX</td></tr>";
	}
	echo "</table>";
	include_once("free.php");
}
?>
</body>
</html>