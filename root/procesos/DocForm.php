<HTML>

<head>
  <title>MATRIX</title>
  <script language="javascript">
      function actuar(valor)
      {
       document.forma.accion.value=valor;
       document.forma.submit();
      }
  </script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>

<body BGCOLOR="">

 <font size=2>  
<BODY TEXT="#000066">
 <center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Selección de Formulario para documentar</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> backup.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php"); 





error_reporting(E_ERROR);


 $codigo= $HTTP_POST_VARS['formulario'];
 $nombre= $HTTP_POST_VARS['medico'];
 $accion= $HTTP_POST_VARS['accion'];
 

 	
	
	// Función de mostrar
	function mostrar($codigo, $nombre)
	{
		//echo "codigo $codigo <br/>";
 		//echo "medico $nombre <br/>";
		$query ="SELECT id,campo,descripcion,tipo FROM det_formulario WHERE codigo='$codigo' and medico='$nombre'";
		$result = mysql_query($query);
		$cantidad=mysql_num_rows($result);
		//echo "cantidad $cantidad <br/>";
	
		$numero=0;
		echo "<form name='forma' method='POST' >";
		echo "<h2>Usuario: $nombre Formulario: $codigo </h2></br>";
		echo "<table>";
		
			echo "<tr>";
	  		echo'<td width="25%" height="10%" align="center" style="color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver; background-color: DarkTurquoise">&nbsp;';
	  		echo '<font>Campo</font></td>';
	  		echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver; background-color: DarkTurquoise">&nbsp;';
	  		echo '<font>Descripción</font></td>';
	  		echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver; background-color: DarkTurquoise">&nbsp;';
	  		echo '<font>Tipo</font></td>';
	  		echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver; background-color: DarkTurquoise">&nbsp;';
	  		echo '<font>Comentario</font></td>';
	  		echo "</tr>";
	
	  	$i=0;	
		while ($resulta=mysql_fetch_row($result))
		{
			if ($i==0)
			{
				$color="white";
				$i=1;
			}
			else
			{
				$color="lightCyan";
				$i=0;
			}
			echo "<input type='hidden' name='id[]' value='$resulta[0]' />";
			echo "<tr>";
	  		echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right-style: none; border-top-style: none; border-bottom: .75pt solid silver; background-color:'. $color.'">&nbsp;';
	  		echo $resulta[1].'</td>';
	  		echo "<input type='hidden' name='campo[]' value='$resulta[1]' />";
	  		echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right-style: none; border-top-style: none; border-bottom: .75pt solid silver; background-color: '. $color.'">&nbsp;';
	  		echo $resulta[2].'</td>';
	  		echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right-style: none; border-top-style: none; border-bottom: .75pt solid silver; background-color: '. $color.'">&nbsp;';
	  		echo $resulta[3].'</td>';
	  		
	  		$query ="SELECT Dic_Descripcion FROM `root_000030` WHERE Dic_Usuario='$nombre' and Dic_campo='$resulta[1]' and Dic_Formulario='$codigo'";
			$hacer = mysql_query($query);
			$resultado=mysql_fetch_row($hacer);
	  		echo'<td width="25%" align="center" style="color: black; border-left: .75pt solid silver; border-right-style: none; border-top-style: none; border-bottom: .75pt solid silver; background-color: '. $color.'">&nbsp;';
	  		echo "<input type='text' name='texto[]' value='$resultado[0]' />";
	  		echo "</tr>";
	  		
		}
	
		echo "</table>";
		echo "<input type='hidden' name='formulario' value='$codigo' />";
		echo "<input type='hidden' name='medico' value='$nombre' />";
		echo "<input type='hidden' name='accion' value='' />";
		echo "<input type='button' value='guardar' onclick='javascript:actuar(\"1\");'/>";
		echo "</form>";
	}

	


if (!$accion ||$accion==2)
{
	mostrar($codigo,$nombre);
}
else 
{
	$textos = $HTTP_POST_VARS['campo'];
	$textos = $HTTP_POST_VARS['texto'];
	$ids = $HTTP_POST_VARS['id'];

	for ($i=0; $i< count($textos);$i++)
	{
		if ( $textos[$i] ) 
		{
	
	
			$query ="SELECT id FROM `root_000030` WHERE Dic_Usuario='$nombre' and Dic_campo='$campo[$i]' and Dic_Formulario='$codigo'";
			$hacer = mysql_query($query);
			//echo $query;
			if ($resulta=mysql_fetch_row($hacer))
			{
				$query ="UPDATE `root_000030` SET Dic_Descripcion='$textos[$i]' WHERE Dic_Usuario='$nombre' and Dic_Formulario='$codigo' and Dic_Campo='$campo[$i]'";
				$resultado = mysql_query($query);
				//echo $query;
			}else
			{
				$f=date("Y-m-d");  
				$s=date("h:m:s"); 
				$query = "INSERT INTO `root_000030` ( `Medico` , `Fecha_data` , `Hora_data` , `Dic_Usuario` , `Dic_Formulario` , `Dic_Campo` , `Dic_Descripcion` , `Seguridad` , `id` )"; 
				$query = $query."VALUES ('root', '$f', '$s', '$nombre', '$codigo', '$campo[$i]', '$textos[$i]', 'C-root', '')";
				if ( !mysql_query( $query) )
				{
					echo "error ejecutando $query";
				}
				//echo $query;
			}
		
		}
				
		else
		{
				//echo "Estoy nulo o vacío";
		}
		
	}
		echo "<h2>Se ha actualizado la información</h2>";
	?>
	<script language="javascript">
 
       window.location="http://localhost/AppEditForm/SeleForm.php";
  	</script>
  	<?php

	
}
?>
	</body>
</HTML>