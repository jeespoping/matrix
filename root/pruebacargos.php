	<?php
	include_once("conex.php");
    include("root/comun.php");	


		$wbasedato 	= "cliame";
		$conex = obtenerConexionBD("matrix");
		$conex_u = odbc_connect('facturacion','','');

		$sql = "SELECT Tcardoi,Tcarlin,Tcarfac , id
				  FROM ".$wbasedato."_000106
				 WHERE  Tcardoi IS NOT NULL
				   AND  Tcaraun !='on' and Tcardoi = '6010523'";


        $res = mysql_query( $sql, $conex  ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
        $num = mysql_num_rows( $res );

		while($row = mysql_fetch_array($res))
		{


			$wbasedato_mov = "movhos";


			$sql1 = "SELECT  Fdeubi
					  FROM ".$wbasedato_mov."_000003
					 WHERE  Fdenum = '".$row['Tcardoi']."'
				 	   AND  Fdelin = '".$row['Tcarlin']."' ";


			$res1 = mysql_query( $sql1, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );

			$estado ='';
			if($row1 = mysql_fetch_array($res1))
			{
				$estado = $row1['Fdeubi'];
			}

			if ($estado =='UP' || $estado =='US')
			{


				$sql2 = "   SELECT  Fenfue
							  FROM  ".$wbasedato_mov."_000002
							 WHERE  Fennum  = '".$row['Tcardoi']."'";


				$res2 = mysql_query( $sql2, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );


				$fuente ='';
				if($row2 = mysql_fetch_array($res2))
				{
					$fuente = $row2['Fenfue'];
				}


				if($conex_u ){

					// --> Obtener los terceros desde unix de la tabla TETERTIP
					$sqlu = "SELECT drodocdoc
							  FROM ITDRODOC
							 WHERE drodocnum  = '".$row['Tcardoi']."'
							   AND drodocfue  = '".$fuente."'
							";
					$resu = odbc_do( $conex_u, $sqlu );
					echo("<br>".$sqlu."<br>");

					if( $resu )
					{
						$i = 0;
						while( odbc_fetch_row($resu) )
						{
							$sqlupdate = " UPDATE FACARDET
											  SET cardetfac = '".$row['Tcarfac']."'
											WHERE cardetfue = '".$fuente."'
											  AND cardetdoc = '".odbc_result($resu,1)."'
											  AND cardetlin = '".$row['Tcarlin']."'
							";

							echo("<br>".$sqlupdate."<br>");

							odbc_do( $conex_u, $sqlupdate );


							$sqlupdate2 = " UPDATE IVDRODET
											  SET drodetfac = '".$row['Tcarfac']."'
											WHERE drodetfue = '".$fuente."'
											  AND drodetdoc = '".odbc_result($resu,1)."'
											  AND drodetite = '".$row['Tcarlin']."'
							";


							odbc_do( $conex_u, $sqlupdate2 );
							echo("<br>".$sqlupdate2."<br>");



							$sql3 = "   UPDATE ".$wbasedato."_000106
										   SET Tcaraun = 'on'
										 WHERE  id = '".$row['id']."'";
							

							mysql_query( $sql3, $conex  ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
							echo("<br>".$sql3."<br>");
						}

					}

				}
			}

		}

		//2014-12-29
		liberarConexionOdbc( $conexUnix );
		odbc_close_all();

	
?>