<?php
// script para organizar datos con id de otra tabla:

/*$conex = mysql_pconnect('localhost','root','')
						or die("No se realizo Conexion");
mysql_select_db("matrix");

$query = "select comdoc, comtid, fecha, comcom from magenta_000010 ";
$err=mysql_query($query,$conex);
$num=mysql_num_rows($err);

if ($num>0)
{
 $vector['ced'][0]=0;
 $vector['cedTip'][0]=0;
 $vector['fec'][0]=0;
  $vector['com'][0]=0;
  $vector['id'][0]=0;

for($i=1;$i<=$num;$i++)	
{	
	$row=mysql_fetch_row($err);
	 $vector['ced'][$i]=$row[0];
 	 $vector['cedTip'][$i]=$row[1];
 	 $vector['fec'][$i]=$row[2];
 	 $vector['com'][$i]=$row[3];
 	 $vector['ced'][0]=$vector['ced'][0]+1;

}

}

$k=0;
for($i=1;$i<=$vector['ced'][0];$i++)	
{	
	echo $k;
	$query = "select id from magenta_000016 where cpedoc='".$vector['ced'][$i]."' and cpetdoc='".$vector['cedTip'][$i]."' ";

	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);

	if ($num>0)
	{
			$row=mysql_fetch_row($err);
			 $vector['id'][$i]=$row[0];
			echo $vector['id'][$i].'</br>';

	}
	
	$query = "select id from magenta_000017 where id_persona='".$vector['id'][$i]."' and ccofori='".$vector['fec'][$i]."' ";
	$err=mysql_query($query,$conex);
	$tam=mysql_num_rows($err);
	
	if ($tam>0)
	{
				$row=mysql_fetch_row($err);
				$id=$row[0];
				
				$query = "select cmonum from magenta_000018 where id_comentario=".$id." order by cmonum ";
				$err=mysql_query($query,$conex);
				$row=mysql_fetch_row($err);
				
				$cnum=$row[0];
				$cnum=$cnum+1;
				
				$query= " INSERT INTO magenta_000018 (medico, Fecha_data, Hora_data, id_comentario, cmonum, cmotip, cmodes, cmocla, cmoest, seguridad)"; 
				$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."',".$id.", ".$cnum.", '--', '". $vector['com'][$i]."','--', 'CERRADO', 'A-magenta') ";
			
				$err=mysql_query($query,$conex);
		
	
	}else
	{	
	
		$query= " INSERT INTO  magenta_000017 (medico, Fecha_data, Hora_data, id_persona, ccoori, ccofori, ccofrec, cconaco, ccotusu, ccoatel, ccoadir, ccoaema, ccoent, ccovol, ccoaut, ccoest, ccocemo, seguridad)"; 
		$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."',".$vector['id'][$i].", '--','".$vector['fec'][$i]."', '--','--','AUTOMATICO','--', '--', '--', '--','--','--','CERRADO','--','A-magenta') ";

		//echo $query;
		$err=mysql_query($query,$conex);
	
		$query = "select id from magenta_000017 where id_persona='".$vector['id'][$i]."' and ccofori='".$vector['fec'][$i]."' ";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
	
		if ($num>0)
		{
			$row=mysql_fetch_row($err);
			$id=$row[0];
			
			$query= " INSERT INTO magenta_000018 (medico, Fecha_data, Hora_data, id_comentario, cmonum, cmotip, cmodes, cmocla, cmoest, seguridad)"; 
			$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."',".$id.", 1, '--', '". $vector['com'][$i]."','--', 'CERRADO', 'A-magenta') ";
			//echo $query;
			$err=mysql_query($query,$conex);

		}
	}
	$k++;
}*/

echo "precaucion";

?>