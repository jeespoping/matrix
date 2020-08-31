<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
</center>
<?php
include_once("conex.php");
		echo "<CENTER><A HREF='http://clinica.pmamericas.com/matrix/graficas.php?Graph=".$Graph." &usuario=1-nuclear'>Ver imagen tamaño completo";
		$Graph="/matrix/images/medical/nuclear/".$Graph;
		echo "<br><IMG SRC=".$Graph."  width='53%'>";
?>
</body>
</html>