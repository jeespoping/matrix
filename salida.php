<html>
<head>
  <title>MATRIX</title>
  </script>
	<style type="text/css">
	 .fondo	{background-image: url(/matrix/images/medical/root/fondo-matrix.jpg);
	         background-repeat: no-repeat;
	         background-position: top center;
	        }   
	  #tipo1{color:#000066;font-size:12pt;font-family:Courier New;font-weight:bold;}
	</style>
</head>
<body BGCOLOR="FFFFFF"  class="fondo">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
		echo "<center><table border=0>";
		echo "<tr><td align=center id=tipo1><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>";
		echo "<tr><td align=center id=tipo1>Su Sesion Se Ha Cerrado</td></tr>";
		echo "<tr><td align=center id=tipo1><A HREF='F1.php'>IR AL LOGIN</a></td></tr></table>";
		session_destroy();
?>
</font></center>
</body>
</html>