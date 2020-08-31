<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<A NAME="Arriba"><p><font size=5>Grafica Asociada</font></p></a>
</center>
<?php
include_once("conex.php");
	if($Graph != "NO APLICA")
	{
		

		

		if(isset($usuario))
		/*esta siendo llamado por otro programa diferente de det_registro*/
			$query = "select grupo from usuarios where codigo='".substr($usuario,2)."'";
		else
			$query = "select grupo from usuarios where codigo='".substr($user,2)."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$Graph="/matrix/images/medical/".$row[0]."/".$Graph;
		echo $Graph."<br>";
		echo "<IMG SRC=".$Graph.">";
		
	}
	else
		echo"<h1> NO EXISTE NINGUNA GRAFICA ASOCIADA!!!</h1>";
?>
</body>
</html>