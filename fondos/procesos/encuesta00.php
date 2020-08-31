<html>
<head>
<title>Manejo de Politicas de Tratamiento de la Informacion</title>
</head>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");    

// Función de encriptación
// Permite enviar la cédula del visitante a la pagina del fondo ecriptada en la URL de la página
	function encrypt($string)
	{
	   return base64_encode($string);
	}
	
echo "<form name='encuesta00' action='encuesta00.php' method=post>";  
echo "<center>";
echo "<table border=1>";
echo "<FONT COLOR='RED'>";
echo "<td rowspan=2 colspan=1 align=center><IMG SRC='AvisoDePrivacidad.png' width='950' height='650' ></td>";			
echo "</table>";
echo "<a class='p' href='politicas_de_tratamiento_de_la_informacion.pdf' target='_blank' ><font face='serif' color='blue'>::Ver politicas::</a></font>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

//echo "<td rowspan=2 colspan=1 align=center><a class='p' href='encuesta01.php' target='_blank' >Aceptar politicas y continuar...";
//echo "<br>";	



//$conex = mysql_connect('localhost','root','q6@nt6m') or die("No se realizo Conexion");     // PARA PRUEBAS LOCALES
mysql_select_db("matrix") or die("No se selecciono la base de datos");  

// $user = "01-07012";        // PARA PRUEBAS LOCALES

	// Se obtiene el código del usuario
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user)); 

	// Segun el usuario y empresa de éste consulto la tabla 
	// de información de empleados correspondiente a esa empresa
	$query  = "SELECT Detval ";
	$query .= "  FROM usuarios, root_000051 ";
	$query .= " WHERE Codigo = '".$wusuario."' ";
	$query .= "   AND Activo = 'A' ";
	$query .= "   AND Empresa = Detemp ";
	$query .= "   AND Detapl = 'informacion_empleados' ";
	
	$rsEmpresa = mysql_query($query,$conex);
	$numEmpresa = mysql_num_rows($rsEmpresa);
	if($numEmpresa > 0)
	{
		// Obtengo la tabla de información de empleados que se debe consultar
		$rowEmpresa = mysql_fetch_array($rsEmpresa);
		$tbInfoEmpleado = $rowEmpresa['Detval'];
	}
	
	// Consulto la cédula del empleado
	$query  = "SELECT Ideced, Ideuse, Empresa  ";
	$query .= "  FROM usuarios, $tbInfoEmpleado ";
	$query .= " WHERE Codigo = '".$wusuario."' ";
	$query .= "   AND ( ( CONCAT(Codigo,'-',Empresa) = Ideuse OR  CONCAT(SUBSTRING(Codigo,3),'-',Empresa) = Ideuse)";
	$query .= "   OR ( Codigo = Ideuse OR  SUBSTRING(Codigo,3) = Ideuse) ) ";	
	$query .= "   AND Ideest = 'on' ";
	$query .= "   AND Activo = 'A' ";  
	
	
	$rs = mysql_query($query,$conex);
	$num = mysql_num_rows($rs);
	if($num > 0)
	{
		// Obtengo la cédula del empleado
		$row = mysql_fetch_array($rs);
		$documento_id = $row['Ideced'];
		$codigo = $row['Ideuse'];
		$empresa = $row['Empresa'];
	}

// Si el usuario ya lleno la encuesta va directo a la pagina si no va a diligenciar la encuesta
	$query = "SELECT * FROM fondos_000099 Where encced='".$documento_id."'";
	$rs = mysql_query($query,$conex);
	$num = mysql_num_rows($rs);
	if($num > 0)
	{
      // Como el documento la pagina del fondo lo recibe encriptado lo paso por la funcion que encripta
	  $docenc = encrypt($documento_id);	 
	  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='http://fondo.lasamericas.com.co/fempleados/index.php?wuser=".$docenc."'><font face='serif' color='blue' size=4>::Ir a la Pagina del fondo de empleados::</a></font>"; 
	}
	else
      echo "<td rowspan=2 colspan=1 align=center><a class='p' href='encuesta01.php?wcedula=".$documento_id."&windicador=PrimeraVez' target='_blank' >Aceptar politicas y continuar...";
	
echo "<br>";	
echo "</form>";
?>
</html>