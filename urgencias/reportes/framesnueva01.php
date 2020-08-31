<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
      
    echo "<frameset rows='50%,50%' frameborder=1 framespacing=2 bordercolor='#FF0000'>";
      echo "<frame src='odonueva01.php' name='prog2' marginwidth=0 marginheiht=0>";
	  echo "<frame src='odonueva02.php' name='prog1' marginwidth=0 marginheiht=0>";
    echo "</frameset>";
?>