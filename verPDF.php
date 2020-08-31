<html>
<head>
  	<title>MATRIX Visualizacion de Archivos PDF</title>
  	<!-- UTF-8 is the recommended encoding for your pages -->
</head>

<body onload="myLink = document.getElementById('vinculo'); myLink.click();">

<?php
include_once("conex.php");
	//echo "<embed src='".$documento."#toolbar=0&navpanes=0' width=100% height=100%  onselectstart='return false'></embed>";
	//echo "<form><A id='vinculo' href='".$documento."'></A></form>";
	
	//matrix/images/medical/
	echo "<form><A id='vinculo' href='images/medical/".$wgrupo."/".$documento."'></A></form>";
	
?>
</BODY>

</html>
