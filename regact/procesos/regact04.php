<!DOCTYPE html>
<html lang="esp">
<head>
    <meta charset="utf-8">
    <title>Registro de Datos Auditoria Medica</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/Estilos.css" rel="stylesheet">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        .filterable {
            margin-top: 15px;
        }
        .filterable .panel-heading .pull-right {
            margin-top: -20px;
        }
        .filterable .filters input[disabled] {
            background-color: transparent;
            border: none;
            cursor: auto;
            box-shadow: none;
            padding: 0;
            height: auto;
        }
        .filterable .filters input[disabled]::-webkit-input-placeholder {
            color: #333;
        }
        .filterable .filters input[disabled]::-moz-placeholder {
            color: #333;
        }
        .filterable .filters input[disabled]:-ms-input-placeholder {
            color: #333;
        }

    </style>
    <?php
include_once("conex.php");
    include_once("regact/regact02.php");

    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
        </div>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        

        include_once("root/comun.php");
        


        $conex = obtenerConexionBD("matrix");
    }

    $idCaso = $_GET['idCaso'];
    $casoBuscado = $_GET['casoB'];
    if($casoBuscado == null){$casoBuscado = $_POST['casoB'];}

    /*$datoUsuario = Consulta($wuse);
    foreach($datoUsuario as $Item):
        $medico = $Item['Medico'];
        $fechadata = $Item['Fecha_data'];
        $horadata = $Item['Hora_data'];
        $codigo = $Item['Codigo'];
        $descripcion = $Item['Descripcion'];
        $rol = $Item['Rol'];
        $ccostos = $Item['Ccostos'];
        $seguridad = $Item['Seguridad'];
        $id = $Item['id'];
    endforeach;
    */

    $queryx = mysql_query("select * from regact_000003 WHERE Codigo = '$wuse'");
    $datox = mysql_fetch_array($queryx);

    $rol = $datox['Rol'];
    ?>
</head>

<body>
<div class="container" style="margin-top: -30px; margin-left: 15px">
    <div id="loginbox" style="margin-top:30px" class="">
        <div class="panel panel-info" >
            <div class="panel-heading" style="background-color: #F14AA4">
                <div class="panel-title" style="color: #ffffff; font-weight: bold">Registro de Actividades - Clinica Las Americas</div>
            </div>

            <div style="padding-top:30px; position: relative; min-height: 550px" class="panel-body" >

                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                </div>

                <!-- PANEL TITULO -->
                <form id="loginform" name="registro" class="form-horizontal" role="form" method="post" action="regact05.php">
                    <div class="container" style="width: 500px; margin-left: 20px">
                        <div class="row">
                            <div class="panel panel-primary filterable">
                                <table class="table">
                                    <thead>
                                    <tr class="filters">
                                        <th><input type="text" class="form-control" placeholder="Titulo" disabled></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label id="lblTitulo">
                                                    <input type="text" class="form-control" style="width: 470px" name="titulo" placeholder="Palabras claves" required>
                                                </label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- PANEL DESCRIPCION -->
                    <div class="container" style="width: 520px; margin-left: 550px; position: absolute; top: 30px">
                        <div class="row">
                            <div class="panel panel-primary filterable">
                                <table class="table">
                                    <thead>
                                    <tr class="filters">
                                        <th><input type="text" class="form-control" placeholder="Descripcion" disabled></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><textarea id="login-username" class="form-control" cols="30" rows="10" name="descripcion" required></textarea></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="input-group" style="border: none">
                                                    <span class="input-group-addon" style="width: 100px"><label>Dia de la actividad</label></span>
                                                    <select class="form-control" style="width: 150px" name="dia_Registro" required>
                                                        <option>1</option>
                                                        <option>2</option>
                                                        <option>3</option>
                                                        <option>4</option>
                                                        <option>5</option>
                                                        <option>6</option>
                                                        <option>7</option>
                                                        <option>8</option>
                                                        <option>9</option>
                                                        <option>10</option>
                                                        <option>11</option>
                                                        <option>12</option>
                                                        <option>13</option>
                                                        <option>14</option>
                                                        <option>15</option>
                                                        <option>16</option>
                                                        <option>17</option>
                                                        <option>18</option>
                                                        <option>19</option>
                                                        <option>20</option>
                                                        <option>21</option>
                                                        <option>22</option>
                                                        <option>23</option>
                                                        <option>24</option>
                                                        <option>25</option>
                                                        <option>26</option>
                                                        <option>27</option>
                                                        <option>28</option>
                                                        <option>29</option>
                                                        <option>30</option>
                                                        <option>31</option>
                                                        <option selected disabled>Seleccione...</option>
                                                    </select>
                                                </div>
                                                <!--
                                                <input type="hidden" name="rol" value="<?php// echo $rol ?>">
                                                <input type="hidden" name="seguridad" value="<?php// echo $wuse ?>">
                                                -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="input-group" style="border: none">
                                                    <span class="input-group-addon" style="width: 100px"><label>Responsable de la actividad</label></span>
                                                    <select class="form-control" style="width: 150px" name="responsable" required>
                                                        <?php
                                                        $consrol = mysql_query("select Codrol,Descripcion from regact_000004");
                                                        while($datorol = mysql_fetch_array($consrol))
                                                        {
                                                            echo "<option value='".$datorol['Descripcion']."'>".$datorol['Descripcion']."</option>";
                                                            $responsable=$datorol['Codrol'];
                                                        }
                                                        ?>
                                                        <option selected="selected" disabled value="">Seleccione...</option>
                                                    </select>
                                                </div>
                                                <input type="hidden" name="rol" value="<?php echo $rol ?>">
                                                <input type="hidden" name="seguridad" value="<?php echo $wuse ?>">
                                                <input type="hidden" name="responsable" value="<?php echo $responsable ?>">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div style="margin-top:20px" class="form-group" align="center">
                                <div class="col-sm-12 controls">
                                    <input type="submit" class="btn btn-success" value="GUARDAR">
                                    <br><br>
                                    <a href="regact01.php"><img src="/matrix/images/medical/regact/regact_volver.png" width="30" height="30" title="Cancelar"></a>
                                </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>