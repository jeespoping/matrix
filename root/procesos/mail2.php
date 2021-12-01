<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
		$headers = "Content-type: text/html; charset=iso-8859-1\r\n";
		$path="<A HREF='//clinica/matrix/".$ruta.">Haga Click Aqui Para Ver Su Colilla</A>";
		$path="http://clinica/matrix/";
		mail("paulomorales@pmamericas.com","Nomina",$path);
		echo "MAIL ENVIADO NRO.".$numero;
?>
</body>
</html>