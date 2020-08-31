<html>

<head>
  <title>REGISTRO PERIODONTAL</title>
</head>
<?php
include_once("conex.php");
echo"<frameset rows='60%, 40%'>";
echo"<frame src='graficoperiodontal.php?medico=".$medico."&amp;pac=".$pac."' Name='arriba'>";
echo"<frame src='regperiodontal_1.php?medico=".$medico."&amp;pac=".$pac."' Name='abajo'>";
echo"</frameset>";
?>