<html>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<div class="container">
	<h1>Informacion de la entidad</h1>
	 <form>
	<div class="row">
	 <div class="form-group col-sm-6">
	 Numero carta cobro:
	<input Type="texto" class="form-control" id='carta' placeholder="Digite el numero de la carta de cobro"  required></input>
	</div>
	<div class="form-group col-sm-6">
	ENTIDAD:
	<input Type="texto"class="form-control" id='nombre'disabled> </input>
	</div>
	</form>
</div>
<div class="container">
	<h1>Informacion de los documentos</h1>
	<div class="row">
	 <div class="form-group col-sm-6">
	 Factura a enviar:
	<input Type="texto" class="form-control" id='factura' placeholder="Digite  factura a enviar" disabled></input>
	</div>
	 
	
	


	
	

<div class="container"> 
<table class="table" id='tabla'>
  <thead>
    <tr>
      <th scope="col">Factura</th>
	  <th scope="col">Valor</th>
      <th scope="col">Historia</th>
      <th scope="col">Paciente</th>
	  <th scope="col">Accion</th>
    </tr>
  </thead>
  <tbody>
    
  </tbody>
</table>
</div>
<button type="button" id ="grabar"class="btn btn-primary">Grabar</button>
<a class="btn btn-primary" href="enviocartasfront.php" role="button" target="_blank">Nueva Carta</a>
<a class="btn btn-primary" href="visualizarcarta.php" role="button" target="_blank">Consultar Carta</a>

<link src></link:src>
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<script src="adicioncarta.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.6.0/dt-1.11.4/datatables.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.6.0/dt-1.11.4/datatables.min.js"></script>

     
</html>