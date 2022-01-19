<?php
//=========================================================================================================================================\\
//       	php para pintar los turneros
//=========================================================================================================================================\\
//DESCRIPCION:  parametros:
//              wemp_pmla, tema, funcion: listarTurnos ó listarAlertas
//                      
//AUTOR:				TAITO
//FECHA DE CREACION:	2021-11-01
    include_once("conex.php");
    include("root/comun.php");
	$wemp_pmla = $_GET['wemp_pmla'];
	$tema = $_GET['tema'];
	$tipoTur = $_GET['tipoTur'];
	$solucionCitas = $_GET['solucionCitas'];
?>
<!DOCTYPE html>
<html>
<head>
  <link rel='shortcut icon' href='favicon.png' type='image/png'>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="../../../include/root/vue/materialdesignicons.min.css" rel="stylesheet">
  <link href="../../../include/root/vue/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
  <meta charset="UTF-8">
  <title>Cartelera Turnos </title> 
  </head>
<body>
  <div id="app">
    <v-app>
      <v-main> 
	  <!-- <v-btn text @click="llamarTurno()" >Abrir</v-btn> -->
        <v-col class="col-12 ml-6" >
            <div v-for="turno in turnospantalla" v-bind:key="turno.Turno" >
                <!-- Enviar al timer -->
				<!-- <v-show>{{ tipoTur }}</v-show> -->
				<div>
					<v-row>
						<v-col cols="12">
						<div style="height:5px;"></div>
							<v-row > 
								<v-card v-if="tipoTur === 'ESTANDAR'"  elevation="8" color="white"  :height="CalcularAltura(turno)" class="oh" width="550" class="pt-3">
									<v-card dark color="white" height="60px" elevation="0" width="550">
										<v-row class="mt-0">
											<v-col cols="5" class="back-a">
												<v-card light  plain color="white" height="50px" elevation="0" width="275">   
													<div class="text-centerE1 textoTurno">
														<p v-text="turno.Turno"  class="back-a" >T-000</p>
													</div>
												</v-card>
											</v-col>
											<v-col cols="7">
											<v-card light plain color="White" height="50px" elevation="0" width="275">
													<div class="text-centerE1 textoTurno2" >
														<p v-text="turno.Modulo" >Modulo</p>
													</div>                       
											</v-card>
											</v-col>
										</v-row>
									</v-card>
									<!-- SE DEJA COMENTADA POR DECISION DE AUNA DE NO MOSTRAR LA PRIORIDAD
									<v-card v-if="turno.Prioridad === 'on'"  class="back-v" height="60px" elevation="0" max-width="550">
										<div class="text-center textoregular  pt-3" >
										<p > Prioritario </p>
										</div>            
									</v-card>-->     
								</v-card>
								<v-card v-if="tipoTur === 'ENDOSCOPIA'"  elevation="8" color="white"  :height="CalcularAltura(turno)" class="oh" width="550" class="pt-3">
									<v-card dark color="white" height="120px" elevation="0" width="550">
										<v-row class="mt-0">
											<v-col cols="5" class="back-a">
												<v-card light  plain color="#00b0ca" height="120px" elevation="0" width="275" class="text-center">   
													<div class="textoTurno">
														<p v-text="turno.Turno" class="back-a" >T-000</p>
													</div>
												</v-card>
											</v-col>
											<v-col cols="7">
											<v-card light plain color="White" height="120px" elevation="0" width="275" class="text-center">
												<div class="textoTurno3" >
													<p v-text="turno.Estado" >Estado</p>
												</div>                       
											</v-card>
											</v-col>
										</v-row>
									</v-card>  
								</v-card> 
							</v-row>
						</v-col>
					</v-row>
					<v-row>
						<v-col cols="4">
						<div style="height:5px;"></div>
								<v-card v-if="tipoTur === 'URGENCIAS'"  elevation="8" color="white"  :height="CalcularAltura(turno)" class="rounded-xl" min-width="550">
									<v-card v-show="turno.Sala" dark color="white" height="60px" elevation="0" width="550">
										<v-row>
											<v-col cols="4">
												<v-card light  plain color="white" height="80px" elevation="0" width="275">   
													<div class="text-center textoTurno">
														<p v-text="turno.Turno">T-000</p>
													</div>
												</v-card>
											</v-col>
											<v-col cols="1">
												<div class="text-center textoTurno2A" >
													<p>|</p>
												</div>  
											</v-col>
											<v-col cols="7">
											<v-card light plain color="White" height="80px" elevation="0" width="275">
												<div class="text-center textoTurno2A" >
													<p v-text="turno.Sala" >Sala</p>
												</div>                       
											</v-card>
											</v-col>
										</v-row>
									</v-card>
									<v-card v-if="!turno.Sala" dark color="white" height="60px" elevation="0" width="550">
										<v-row>
											<v-col cols="12">
												<v-card light  plain color="white" height="80px" elevation="0">   
													<div class="text-centerAl textoTurno">
														<p v-text="turno.Turno">T-000</p>
													</div>
												</v-card>
											</v-col>
											
										</v-row>
									</v-card>
									<v-card dark color="#bed600" height="60px" elevation="0" max-width="550" >
                  						<div class="text-centerAl textoregular  pt-3" >
										  <p v-text="turno.Estado" >Estado</p>
                  						</div>              
          							</v-card>
									<v-card v-show="turno.Ubicacion" dark color="#00B0CA" height="60px" elevation="0" max-width="550">
										<div class="text-centerAl textoregular  pt-3" >
											<p v-text="turno.Ubicacion" >Ubicacion</p>
										</div>            
          							</v-card>
									<!-- <v-card v-if="!turno.Ubicacion" dark color="#00B0CA" height="60px" elevation="0" max-width="550">
										<div class="text-centerAl textoregular  pt-3" >
											<p>Sala de espera</p>
										</div>  
          							</v-card>--> 
								</v-card>
						</v-col>   
					</v-row>
				</div>
            </div>
        </v-col>
      </v-main>
	  <v-dialog transition="dialog-bottom-transition" max-width="560" v-model="llamadoTurno" persistent timerAlerta ml-6>
				<v-toolbar elevation-0 dense dark warning py-0 px-2> <h2 style="text-align: center;">Atenci&oacute;n</h2></v-toolbar>
			<v-card>
			<div style="height:40px;"></div> 
		    <v-card-text>
				<v-row>
					<v-card elevation="8" color="white"  class="oh" width="560" class="pt-3">
						<v-card dark color="white"  elevation="0" width="560">
							<v-row class="mt-0">
								<v-col cols="5" class="back-a">
									<v-card light  plain color="white" height="50px" elevation="0" width="280">   
										<div class="text-centerAl textoTurno">
										<p v-text=""  class="back-a" >Turno</p>
										</div>		
									</v-card>
								</v-col>
								<v-col cols="7" class="back-a">
									<v-card v-if="tipoTur === 'ENDOSCOPIA'" light plain color="white" height="50px" elevation="0" width="280">
										<div class="text-centerAl textoTurno" >
										<p v-text=""  class="back-a">Estado</p>
										</div>                       
									</v-card>
									<v-card v-else light plain color="white" height="50px" elevation="0" width="280">
										<div class="text-centerAl textoTurno" >
										<p v-text=""  class="back-a">Pasar a</p>
										</div>                       
									</v-card>
								</v-col>
							</v-row>
							<div v-for="turno in turnosAlerta" v-bind:key="turno.turnoAlerta" >
								<v-row class="mt-0">
									<v-col cols="5">
										<v-card v-if="tipoTur === 'URGENCIAS'" light  plain color="white" height="90px" elevation="0" width="280">   
											<div class="text-centerAl textoTurno">
											<p v-text="turno.turnoAlerta" >T-000</p>
											</div>
										</v-card>
										<v-card v-else light  plain color="white" height="50px" elevation="0" width="280">   
											<div class="text-centerAl textoTurno">
											<p v-text="turno.turnoAlerta" >T-000</p>
											</div>
										</v-card>
									</v-col>
									<v-col cols="7">
										<v-card v-if="tipoTur === 'ENDOSCOPIA'" light plain color="white" height="50px" elevation="0" width="280">
											<div class="text-centerAl textoTurno" >
											<p v-text="turno.Estado" >Estado</p>
											</div>                       
										</v-card>
										<v-card v-else-if="tipoTur === 'URGENCIAS'" light plain color="white" height="90px" elevation="0" width="280">
											<div class="text-centerAl textoTurno" >
											<p v-text="turno.moduloAlerta" >Modulo</p>
											</div>                       
										</v-card>
										<v-card v-else light plain color="white" height="50px" elevation="0" width="280">
											<div class="text-centerAl textoTurno" >
											<p v-text="turno.moduloAlerta" >Modulo</p>
											</div>                       
										</v-card>
									</v-col>
									<!--<v-col cols="7">
										
										<v-card v-else light plain color="white" height="90px" elevation="0" width="280">
											<div class="text-centerAl textoTurno" >
											<p v-text="turno.moduloAlerta" >Modulo</p>
											</div>                       
										</v-card>
									</v-col>-->
								</v-row>
							</div>
						</v-card>
						<div style="height:20px;"></div>
					</v-card>
				</v-row>
			</v-card-text>
			<div style="height:20px;"></div>
          </v-card>
      </v-dialog>  
    </v-app>
  </div>
  <script src="../../../include/root/vue/vue.js"></script>
  <script src="../../../include/root/vue/vuetify.js"></script>
  <script>
		var wemp_pmla=<?php echo $wemp_pmla; ?>;
		var tema=<?php echo $tema; ?>;
		var tipoTur=<?php echo "'".$tipoTur."'"; ?>;
	  	var solucionCitas=<?php echo "'".$solucionCitas."'"; ?>; 
		const wsTurnero="wbsturnero.php?"
  </script>
  <script src="carteleraturnos.js"></script>
  
  <style>

	.text-center {
    text-align: center!important;
    display: table-cell;
    vertical-align: middle;
	}
	.text-centerE1{
    text-align: center!important;
    vertical-align: middle;
	}
	.text-centerAl {
    text-align: center!important;
    vertical-align: middle;
	}
    .textoTurno{
     color:#00B0CA!important;
     font-family: "Axiforma-Bold", Helvetica, Arial!important;
     font-size: 40px!important;
     line-height : 50px!important;
	 vertical-align: middle;
	 text-align: center!important;
    }

    .textoTurno2{
     color: #00B0CA!important;
     font-family: "Axiforma-Bold", Helvetica, Arial!important;
     font-size: 40px!important;
     line-height : 50px!important;
    }
	.textoTurno2A{
     color: #00B0CA!important;
     font-family: "Axiforma-Bold", Helvetica, Arial!important;
     font-size: 40px!important;
     line-height : 44px!important;
    }
    .textoTurno3{
     color: #00B0CA!important;
     font-family: "Axiforma-Bold", Helvetica, Arial!important;
     font-size: 35px!important;
     line-height : 50px!important;
	 vertical-align: middle;
	 text-align: center!important;
    }

    .textoregular{
     font-family: "Axiforma-Bold", Helvetica, Arial!important;
     font-size: 38px!important;
     line-height : 42px!important;
	 vertical-align: middle;
	 text-align: center!important;
    }

    .pt-3{
        padding-top: 12px;
    }

    .mt-0{
        margin-top: 0px;
    }
    .color-a {
        color: #00b0ca;
    }
    .color-v {
        color: #bed600;
    }
    .back-a{
        background-color: #00B0CA !important;
        color: white !important;

    }
    .back-v{
        background-color: #bed600 !important;
        color: white !important;
       
    }
    @font-face {
        font-family: "Axiforma-Bold";
        src: url('../../../include/root/font/Axiforma-Bold.ttf');   
    }
    .oh{
        overflow: hidden;
    }
	.back-r{
		background-color: #ff5252 !important;
		color: white !important;
		overflow: hidden;
	}
	header.v-sheet.theme--dark.v-toolbar.v-toolbar--dense {
		background: #ff5252 !important;
		border-color: #ff5252 !important;
	
	}
	header.v-sheet.theme--dark.v-toolbar.v-toolbar--dense {
		height: 50px !important;
		padding: 5px 0;	
	}
	.v-toolbar__content{
		display: block;
		font-family: "Axiforma-Bold", Helvetica, Arial!important;
		 font-size: 25px!important;
		 line-height : 50px!important;
	}
	.v-dialog{
	    margin: 20px !important;
	}
	.v-dialog__content {
		justify-content: left;
	}
  </style>
</body>
</html>