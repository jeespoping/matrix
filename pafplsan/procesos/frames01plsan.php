<?
session_start();
if(!session_is_registered("user"))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
        
    echo "<frameset rows='50%,50%' frameborder=1 framespacing=2 bordercolor='#FF0000'>";
      echo "<frame src='paf11plsan.php' name='prog2' marginwidth=0 marginheiht=0>";
	  echo "<frame src='paf12plsan.php' name='prog1' marginwidth=0 marginheiht=0>";
    echo "</frameset>";
    
?>