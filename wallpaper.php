<html>
<head>
<title>MATRIX WALLPAPER</title>
 <style type="text/css">
 	#tipo1{color:#000000;font-size:14pt;font-family:Arial;font-weight:bold;width:120em;text-align:left;height:6em;}
 	#tipo2{width:65%}
 	#tipo3{color:#000000;font-size:12pt;font-family:Arial;font-weight:bold;width:22em;text-align:left;height:20em;}
 	#tipo4{color:#000066;font-size:12pt;font-family:Arial;font-weight:bold;}
 	#tipo5{color:#000066;font-size:12pt;font-family:Arial;font-weight:bold;}
 	#tipo6{height:2em;}
 	#tipo7{width:10%;}
 	#tipo8{color:#000000;font-size:7pt;font-family:Arial;font-weight:bold;width:100%;text-align:left;height:8em;}
 	
 	A	{text-decoration: none;color: #000066;;font-size:12pt;font-family:Arial;font-weight:bold;}
 	
 </style>
<script type='text/javascript' src='../include/root/jquery-1.3.2.min.js'></script>
<script type="text/javascript" src="../include/root/jquery.corner.js"></script>
<script type="text/javascript">
  	$(document).ready(function() {
  	$("#divRedondeao1").corner();
  	$("#divRedondeao2").corner();
  	$("#divRedondeao3").corner();
  	});
</script>
</head>

<body  BGCOLOR="ffffff" TEXT="#000066">
<form name="wallpaper" action="wallpaper.php" method=post>
<center>
<table border=0>
<?php
include_once("conex.php");	
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/".$wallpaper."'></td></tr>";
?>
</table>
</center>
</body>
</html>

