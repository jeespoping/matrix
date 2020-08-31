<html>
<head>
  <title>MATRIX</title>
	<style type="text/css">
	<!--
		.BlueThing
		{
			background: #CCCCFF;
		}
		
		.SilverThing
		{
			background: #CCCCCC;
		}
		
		.GrayThing
		{
			background: #DDDDDD;
		}
	
	//-->
	</style>
</head>
<body BGCOLOR="FFFFFF">
<font size=4 face="tahoma">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
	@session_start();
	
	if(!isset($_SESSION['user']))
	echo "error";
	else
	{
		$key = substr($user,2,strlen($user));
		echo "<center>";
		

		

		$query = "select descripcion from usuarios where codigo = '".$key."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		echo "<br><br><br> BIENVENIDO : ".$row[0];
		echo "<BR><BR><font size=\"1\" face=\"Arial, Helvetica, sans-serif\"><strong> IP REAL: </strong><font size=\"1\"><b>".$IIPP."<b></font><BR>"; 
		echo "<br><br><center>";
		echo "<IMG SRC='/matrix/images/medical/root/php.png'>";
		echo "</center><br><br>";
	}
?>

<!-- Search Google -->
<center>
<FORM method=GET action=http://www.google.com/custom>
<TABLE bgcolor=#FFFFFF cellspacing=0 border=0>
<tr valign=top><td>
<A HREF=http://www.google.com/search>
<IMG SRC=http://www.google.com/logos/Logo_40wht.gif border=0 ALT=Google align=middle></A>
</td>
<td>
<INPUT TYPE=text name=q size=31 maxlength=255 value="">
<INPUT type=submit name=sa VALUE="Google Search">
<INPUT type=hidden name=cof VALUE="AH:center;S:http://www.pmamericas.com;AWFID:3c1bcf063494ab3e;">

</td></tr></TABLE>
</FORM>
</center>
<!-- Search Google --> 

<?php
	echo "<br><br><center><table border=0>";
	echo "<tr><td align=center colspan=2 bgcolor=#999999><b>NOTICIAS PARA HOY : ".date("d-m-Y")."<b></td></tr>";
	echo "<tr><td  bgcolor=#cccccc><font size=2><b>TEMA<b></font></td><td  bgcolor=#cccccc><font size=2><b>TEXTO<b></font></td></tr>";
	$query = "select   Tema, Texto    from root_000022 where activo='on' ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);
		echo "<tr onmouseover=".chr(34)."this.className='BlueThing';".chr(34)."  onmouseout=".chr(34)."this.className='GrayThing';".chr(34)." bgcolor=#dddddd>";
		echo "<td><font size=2>".$row[0]."</font></td><td><font size=2>".$row[1]."</font></td></tr>";
	}
	echo "</table>";
?>		
</font></center>
</body>
</html>
