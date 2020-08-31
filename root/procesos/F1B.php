<html>
<head>
<title>MATRIX</title>
<link rel="icon" href="favicon.ico" type="image/x-icon"> 
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 
<script>
function ira(){document.entrada.codigo.focus();}
</script>

</head>

<body onload=ira() BGCOLOR="ffffff">
<BODY TEXT="#000066">
<form name="entrada" action="f2.php" method=post>
<table border=0 align=center>
<tr><td align=center colspan=2><IMG SRC="/matrix/images/medical/root/matrix9.gif" ></td></tr>
<tr>
  <td   align=center><IMG SRC="/matrix/images/medical/root/codigo.gif" ></td>
  <td  align=center><input type="text" name="codigo" size=8 maxlength=8></td>
</tr>
<tr>
  <td  align=center><IMG SRC="/matrix/images/medical/root/clave.gif" ></td>
  <td  align=center><input type="password" name="password" size=8 maxlength=8></td>
</tr>
<tr>
  <td colspan=2 align=center><input type=submit value="ENTRAR"></td>
</tr>
<tr>
<td colspan=2 align=center><A HREF='salida.php'><IMG SRC="/matrix/images/medical/root/no.gif" ></a></td>
</tr>
</table>
</form>
<br>
<center>
<IMG SRC="/matrix/images/medical/root/casa.png" ><br>
<a href
onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('http://clinica.pmamericas.com/matrix/f1.php');"
style="CURSOR: hand"><b>  HACER DE MATRIX MI PAGINA DE INICIO</b></a><br>
</center>
<?php
include_once("conex.php");
   //<tr><td align=center colspan=2><IMG SRC="/matrix/images/medical/root/VERDE.gif" ></td></tr>
	session_start();
	if (isset($user))
		$user = "2-".substr($user,2,8);
		
		$WIP=$REMOTE_ADDR;
		
		
		echo $WIP;
		
		echo $_SERVER[0]."<br>";
		echo $GLOBALS."<br>";
		echo $HTTP_SERVER_VARS."<br>";
		echo $SERVER_ADDR."<br>";
		echo $SERVER_NAME."<br>";
		
		echo $SERVER_SOFTWARE."<br>";
        echo $SERVER_PROTOCOL."<br>"; 
        echo $REQUEST_METHOD."<br>";
        echo $REQUEST_TIME."<br>";
        
        echo $REQUEST_TIME."<br>";
        echo $QUERY_STRING."<br>";
        echo $DOCUMENT_ROOT."<br>";
        echo $HTTP_ACCEPT."<br>";
        echo $HTTP_ACCEPT_CHARSET."<br>";
        echo $HTTP_ACCEPT_ENCODING."<br>"; 
        echo $HTTP_ACCEPT_LANGUAGE."<br>"; 
        echo $HTTP_CONNECTION."<br>"; 
        echo $HTTP_HOST."<br>"; 
        echo $HTTP_REFERER."<br>"; 
        echo $HTTP_USER_AGENT."<br>"; 
        echo $HTTPS."<br>";
        echo $REMOTE_HOST."<br>"; 
        echo $REMOTE_PORT."<br>";
        echo $SCRIPT_FILENAME."<br>"; 
        echo $SERVER_ADMIN."<br>"; 
        echo $SERVER_PORT."<br>"; 
        echo $PATH_TRANSLATED."<br>"; 
        echo $SCRIPT_NAME."<br>"; 
        echo $REQUEST_URI."<br>"; 
        echo $PHP_AUTH_DIGEST."<br>"; 
        echo $PHP_AUTH_USER."<br>"; 
        echo $PHP_AUTH_PW."<br>";
        echo $AUTH_TYPE."<br>";
        
        echo $_SESSION."<br>";
        
        //var_dump($_SESSION);*/
		        
?>

</body>
</html>

