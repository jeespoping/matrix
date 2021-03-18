<HTML>
<html><input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<HEAD>
<TITLE>DETALLA CICLOS DE ANTIBIOTICOS DE UN PACIENTES</TITLE>
</HEAD>
<BODY>
<?php
include_once("conex.php");
include_once("root/comun.php");
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
	else
	{
		$user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        mysql_select_db("matrix");
		$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
		$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez"); 
	
        $conex = obtenerConexionBD("matrix");
	}
 


 //$conex = mysql_connect('192.168.120.2','root','q6@nt6m') or die("No se realizo Conexion");
 //ok  $conex = mysql_connect('132.1.18.12','root','q6@nt6m') or die("No se realizo Conexion");
 mysql_select_db("matrix") or die("No se selecciono la base de datos");

function calcularDiferenciaDias($fecha_inicio, $fecha_fin)
{
	$inicio = strtotime($fecha_inicio." 00:00:00");
	$fin = strtotime($fecha_fin." 00:00:00");
	$resultado = $fin*1 - $inicio*1;
	$resultado = gmdate( "z", $resultado);
    return ($resultado+1);
}

 //Forma
 echo "<form name='detcicantibio' action='detcicantibio.php?wemp_pmla=".$wemp_pmla."' method=post>";

	echo "<center><table border=0>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>DETALLE CICLOS DE ANTIBIOTICOS</font></b><br>";
	echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>PACIENTE:".$whis."-".$wnum." ".$wnom." </font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: detcicantibio.? Ver. 2016/04/12<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>CODIGO<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>DESCRIPCION</td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>VIA<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>FREC<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>FECHA INICIO<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>HORA INICIO<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>FECHA<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>DIAS<b></td>";
	//echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>SUSPENDIDO<b></td>";

    echo "</tr>";

	$c1=explode('-',$wcco);

	//Genero tabla temporal con los antibioticos
	$query = "DROP TABLE IF EXISTS tmpa";
	$resultado = mysql_query($query, $conex);

	$query = "CREATE TEMPORARY TABLE IF NOT EXISTS tmpa
			 (INDEX idx(art))
				SELECT Artcod art, Artcom nom
				  FROM ".$wmovhos."_000026
				 WHERE artgru like 'J00%'
				   AND artest='on'
				 UNION ALL
				SELECT Pdepro art, Artcom nom
				  FROM ".$wcenpro."_000003, ".$wcenpro."_000002
				 WHERE Pdeins like 'MA%'
				   AND pdeest = 'on'
				   AND artcod = Pdepro
				 GROUP BY Pdepro";
	$resultado = mysql_query($query, $conex) or die(mysql_error());

    $query="Select art,nom,kadvia,kadfin,kadhin,kadfec,kadsus,kadper From ".$wmovhos."_000054, tmpa "
          ." WHERE kadhis = ".$whis."
            AND kading = ".$wnum."
            AND kadart = art 
           ORDER BY art, kadfec DESC ";

   $resultado = mysql_query($query);            // Ejecuto el query
   $nroreg = mysql_num_rows($resultado);
   $a=0;
   $n=1;

   // Leo 1er registro y lo muestro
   $registro = mysql_fetch_row($resultado);
   echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";
   echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";
   echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
   echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[7]."</td>";
   echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[3]."</td>";
   echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[4]."</td>";

      if ( $registro[5] == date("Y-m-d") )   // Si la fecha es igual a la actual la pinto de amarillo
        echo "<td colspan=2 align=center bgcolor=#FFFF00><font text color=#003366 size=3>".$registro[5]."</td>";
      else
	    echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[5]."</td>";

   $diasciclo = calcularDiferenciaDias($registro[3], $registro[5]);
   echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$diasciclo."</td>";

   //if ($registro[6]=="on")
   // $westado="SI";
   //else
   // $westado="NO";
   //echo "<td colspan=2 align=LEFT bgcolor=".$wcf."><font text color=#003366 size=3>".$westado."</td>";
   
   echo "<tr>";

   While ($n <= $nroreg)
   {
	 $a++;
     $artant=$registro[0];
     $fecant=$registro[3];

	 // Ahora leo los registros hasta que cambie de fecha o de Articulo
     while   ( ($artant==$registro[0]) and ($fecant==$registro[3]) and ($n <= $nroreg) )
     {
	   $registro = mysql_fetch_row($resultado);
	   $n++;
	 }

	if ($n <= $nroreg)   //Si no es el ultimo registro
	{
	  if (is_int ($a/2))  // Cuando la variable $i es par coloca este color
       $wcf="DDDDDD";
   	  else
   	   $wcf="CCFFFF";

	  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";
      echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";
	  echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
	  echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[7]."</td>";
	  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[3]."</td>";
	  echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[4]."</td>";

      if ( $registro[5] == date("Y-m-d") )   // Si la fecha es igual a la actual la pinto de amarillo
        echo "<td colspan=2 align=center bgcolor=#FFFF00><font text color=#003366 size=3>".$registro[5]."</td>";
      else
	    echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[5]."</td>";

	  if( $registro[5] > $registro[3] )
	  {
	    $diasciclo = calcularDiferenciaDias($registro[3], $registro[5]);
	    if ( $diasciclo < 10 )                // Ciclos de 10 dias o mas salen rojos
	     echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$diasciclo."</td>";
	    else
		 echo "<td colspan=2 align=center bgcolor=#FF0000><font text color=#003366 size=3>".$diasciclo."</td>";
      }
	  else
	   echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>0</td>";
	  
	  //if ($registro[6]=="on")
	  //	$westado="SI";
	  //else
	  //   $westado="NO";
	  //echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$westado."</td>";
	  
      echo "<tr>";
	}

   }
     echo "</table>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total Ciclos: ".$a."</font></b><br>";

echo "</BODY>";
echo "</HTML>";

?>