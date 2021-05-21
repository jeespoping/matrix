<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Encuesta de Satisfaccion Central de Esterilizacion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="Estilos.css" rel="stylesheet">
    <style type="text/css"></style>
    <script src="js3.js"></script>
    <script type="text/javascript">
        validarCampos()
    </script>
    <?php
include_once("conex.php");
    include_once("cenest/Library2.php");
    if(!isset($_SESSION['user']))
    {
        ?>
        <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix; Inicie sesion nuevamente.</label>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        

        include_once("root/comun.php");
        $conex = obtenerConexionBD("matrix");
		//$wemp_pmla = $_POST['wemp_pmla'];
		$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
    }
	
	
    ?>
</head>
<body>
	
<div class="container" style="margin-top: -30px">
    <div id="loginbox" style="margin-top:50px;" class="">
        <div class="panel panel-info" style="width: 1300px">
            <div class="panel-heading">
                <div class="panel-title">Encuesta de Satisfaccion Central de Esterilizacion</div>
            </div>

            <div style="padding-top:30px" class="panel-body" >

                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                </div>
				
				<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
                
				<form  id="encuestaform" name="encuestaform" class="form-horizontal" role="form" method="post" action="encuestace.php?wemp_pmla=<?php echo($wemp_pmla)?>">
						
						
				   <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>Centro de Costos:</label></span>
                            <select id="centro_Costos" name="centro_Costos" class="form-control" style="width: 350px" required>
                                <?php
                                $consespe2 = mysql_query("select Ccocod,Cconom from ".$wbasedatomovhos."_000011");
                                while($datoespe2 = mysql_fetch_array($consespe2))
                                {
                                    echo "<option value='".$datoespe2['Ccocod']."'>".$datoespe2['Ccocod'].' '.$datoespe2['Cconom']."</option>";
                                    $descripcionespe2=$datoespe2['Ccocod'];
                                }
                                ?>
                                <option>01999 CASAS COMERCIALES</option>
                                <option>02999 ESTERILIZACION PARA TERCEROS</option>
                                <option selected="selected" disabled value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span><label>Califique de 1 a 5, siendo 5 la mayor calificacion y 1 la menor, los siguientes criterios:</label></span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 980px; margin-left: -210px">1) CUMPLIMIENTO DE SOLICITUDES EN CENTRAL DE ESTERILIZACION?</label></span>
                            <span class="input-group-addon" style="background-color: transparent; width: 500px">
                                <input id="f1_1" name="f1_1" type="image" src="/matrix/images/medical/cenest/1.png" width="40" height="40" style="margin-left: 20px" value="0" onclick="seleccionar1_1();return false" >
                                <input id="f1_2" name="f1_2" type="image" src="/matrix/images/medical/cenest/2.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar1_2();return false" >
                                <input id="f1_3" name="f1_3" type="image" src="/matrix/images/medical/cenest/3.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar1_3();return false">
                                <input id="f1_4" name="f1_4" type="image" src="/matrix/images/medical/cenest/4.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar1_4();return false">
                                <input id="f1_5" name="f1_5" type="image" src="/matrix/images/medical/cenest/5.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar1_5();return false">
                                <input id="f1_6" name="f1_6" type="image" src="/matrix/images/medical/cenest/6.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar1_6();return false">
                            </span>
                        </div>
                        <div style="height: 1px">
                            <span>
                                <label style="margin-left: 850px">1</label>
                                <label style="margin-left: 60px">2</label>
                                <label style="margin-left: 60px">3</label>
                                <label style="margin-left: 60px">4</label>
                                <label style="margin-left: 60px">5</label>
                                <label style="margin-left: 60px">N/A</label>
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 890px; margin-left: -120px">2) CALIFIQUE LA CALIDEZ EN LA ATENCION POR PARTE DEL AUXILIAR DE LAVADO?</label></span>
                            <span class="input-group-addon" style="background-color: transparent; width: 900px">
                                <input id="f2_1" name="f2_1" type="image" src="/matrix/images/medical/cenest/1.png" width="40" height="40" style="margin-left: 20px" value="0" onclick="seleccionar2_1();return false" >
                                <input id="f2_2" name="f2_2" type="image" src="/matrix/images/medical/cenest/2.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar2_2();return false" >
                                <input id="f2_3" name="f2_3" type="image" src="/matrix/images/medical/cenest/3.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar2_3();return false" >
                                <input id="f2_4" name="f2_4" type="image" src="/matrix/images/medical/cenest/4.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar2_4();return false" >
                                <input id="f2_5" name="f2_5" type="image" src="/matrix/images/medical/cenest/5.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar2_5();return false" >
                                <input id="f2_6" name="f2_6" type="image" src="/matrix/images/medical/cenest/6.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar2_6();return false">
                            </span>
                        </div>
                        <div style="height: 1px">
                            <span>
                                <label style="margin-left: 850px">1</label>
                                <label style="margin-left: 60px">2</label>
                                <label style="margin-left: 60px">3</label>
                                <label style="margin-left: 60px">4</label>
                                <label style="margin-left: 60px">5</label>
                                <label style="margin-left: 60px">N/A</label>
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 890px; margin-left: -120px">3) CALIFIQUE LA ATENCION POR PARTE DEL AUXILIAR EN EL AREA DE DESPACHO?</label></span>
                            <span class="input-group-addon" style="background-color: transparent; width: 700px">
                                <input id="f3_1" name="f3_1" type="image" src="/matrix/images/medical/cenest/1.png" width="40" height="40" style="margin-left: 20px" value="0" onclick="seleccionar3_1();return false" >
                                <input id="f3_2" name="f3_2" type="image" src="/matrix/images/medical/cenest/2.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar3_2();return false" >
                                <input id="f3_3" name="f3_3" type="image" src="/matrix/images/medical/cenest/3.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar3_3();return false" >
                                <input id="f3_4" name="f3_4" type="image" src="/matrix/images/medical/cenest/4.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar3_4();return false" >
                                <input id="f3_5" name="f3_5" type="image" src="/matrix/images/medical/cenest/5.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar3_5();return false" >
                                <input id="f3_6" name="f3_6" type="image" src="/matrix/images/medical/cenest/6.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar3_6();return false">
                            </span>
                        </div>
                        <div style="height: 1px">
                            <span>
                                <label style="margin-left: 850px">1</label>
                                <label style="margin-left: 60px">2</label>
                                <label style="margin-left: 60px">3</label>
                                <label style="margin-left: 60px">4</label>
                                <label style="margin-left: 60px">5</label>
                                <label style="margin-left: 60px">N/A</label>
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 960px; margin-left: -190px">4) CALIFIQUE EL TIEMPO DE ATENCION Y/O RESPUESTA A SU SOLICITUD?</label></span>
                            <span class="input-group-addon" style="background-color: transparent; width: 700px">
                                <input id="f4_1" name="f4_1" type="image" src="/matrix/images/medical/cenest/1.png" width="40" height="40" style="margin-left: 20px" value="0" onclick="seleccionar4_1();return false">
                                <input id="f4_2" name="f4_2" type="image" src="/matrix/images/medical/cenest/2.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar4_2();return false">
                                <input id="f4_3" name="f4_3" type="image" src="/matrix/images/medical/cenest/3.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar4_3();return false">
                                <input id="f4_4" name="f4_4" type="image" src="/matrix/images/medical/cenest/4.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar4_4();return false">
                                <input id="f4_5" name="f4_5" type="image" src="/matrix/images/medical/cenest/5.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar4_5();return false">
                                <input id="f4_6" name="f4_6" type="image" src="/matrix/images/medical/cenest/6.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar4_6();return false">
                            </span>
                        </div>
                        <div style="height: 1px">
                            <span>
                                <label style="margin-left: 850px">1</label>
                                <label style="margin-left: 60px">2</label>
                                <label style="margin-left: 60px">3</label>
                                <label style="margin-left: 60px">4</label>
                                <label style="margin-left: 60px">5</label>
                                <label style="margin-left: 60px">N/A</label>
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 780px; margin-left: -10px">5) LOS PRODUCTOS ESTERILES SON ACORDE A LAS NECESIDADES REALIZADAS EN SU SOLICITUD?</label></span>
                            <span class="input-group-addon" style="background-color: transparent; width: 700px">
                                <input id="f5_1" name="f5_1" type="image" src="/matrix/images/medical/cenest/1.png" width="40" height="40" style="margin-left: 20px" value="0" onclick="seleccionar5_1();return false">
                                <input id="f5_2" name="f5_2" type="image" src="/matrix/images/medical/cenest/2.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar5_2();return false">
                                <input id="f5_3" name="f5_3" type="image" src="/matrix/images/medical/cenest/3.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar5_3();return false">
                                <input id="f5_4" name="f5_4" type="image" src="/matrix/images/medical/cenest/4.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar5_4();return false">
                                <input id="f5_5" name="f5_5" type="image" src="/matrix/images/medical/cenest/5.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar5_5();return false">
                                <input id="f5_6" name="f5_6" type="image" src="/matrix/images/medical/cenest/6.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar5_6();return false">
                            </span>
                        </div>
                        <div style="height: 1px">
                            <span>
                                <label style="margin-left: 850px">1</label>
                                <label style="margin-left: 60px">2</label>
                                <label style="margin-left: 60px">3</label>
                                <label style="margin-left: 60px">4</label>
                                <label style="margin-left: 60px">5</label>
                                <label style="margin-left: 60px">N/A</label>
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label style="width: 860px; margin-left: -90px">6) CALIFIQUE EN GENERAL EL SERVICIO PRESTADO EN CENTRAL DE ESTERILIZACION?</label></span>
                            <span class="input-group-addon" style="background-color: transparent; width: 700px">
                                <input id="f6_1" name="f6_1" type="image" src="/matrix/images/medical/cenest/1.png" width="40" height="40" style="margin-left: 20px" value="0" onclick="seleccionar6_1();return false">
                                <input id="f6_2" name="f6_2" type="image" src="/matrix/images/medical/cenest/2.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar6_2();return false">
                                <input id="f6_3" name="f6_3" type="image" src="/matrix/images/medical/cenest/3.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar6_3();return false">
                                <input id="f6_4" name="f6_4" type="image" src="/matrix/images/medical/cenest/4.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar6_4();return false">
                                <input id="f6_5" name="f6_5" type="image" src="/matrix/images/medical/cenest/5.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar6_5();return false">
                                <input id="f6_6" name="f6_6" type="image" src="/matrix/images/medical/cenest/6.png" width="40" height="40" style="margin-left: 30px" value="0" onclick="seleccionar6_6();return false">
                            </span>
                        </div>
                        <div style="height: 1px">
                            <span>
                                <label style="margin-left: 850px">1</label>
                                <label style="margin-left: 60px">2</label>
                                <label style="margin-left: 60px">3</label>
                                <label style="margin-left: 60px">4</label>
                                <label style="margin-left: 60px">5</label>
                                <label style="margin-left: 60px">N/A</label>
                            </span>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div style="margin-bottom: 25px" class="input-group">
                            <span><label>Sugerencias:</label></span>
                            <div class="input-group">
                                <span><textarea id="sugest" name="sugest" cols="175" rows="1"></textarea></span>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:-35px" class="form-group" align="center">
                        <!-- Button -->
                        <div class="col-sm-12 controls">
                            <input type="submit" class="btn btn-success" style="margin-left: 50px" value="GUARDAR" onclick="guardar(f1_1,f1_2,f1_3,f1_4,f1_5,f2_1,f2_2,f2_3,f2_4,f2_5,f3_1,f3_2,f3_3,f3_4,f3_5,f4_1,f4_2,f4_3,f4_4,f4_5,f5_1,f5_2,f5_3,f5_4,f5_5,f6_1,f6_2,f6_3,f6_4,f6_5,centro_Costos,sugest)">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>