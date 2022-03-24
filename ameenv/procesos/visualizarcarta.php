<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<body>
<style>
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    box-sizing: border-box;
    display: inline-block;
    min-width: 1.5em;
    padding: 0.5em 1em;
    margin-left: 2px;
    text-align: center;
    text-decoration: none !important;
    cursor: pointer;
    *cursor: hand;
    color: #333 !important;
    border: 1px solid transparent;
    border-radius: 2px;
}
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover, .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
    cursor: default;
    color: #666 !important;
    border: 1px solid transparent;
    background: transparent;
    box-shadow: none;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current, .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    color: #333 !important;
    border: 1px solid #979797;
    background-color: white;
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, white), color-stop(100%, #dcdcdc));
    background: -webkit-linear-gradient(top, white 0%, #dcdcdc 100%);
    background: -moz-linear-gradient(top, white 0%, #dcdcdc 100%);
    background: -ms-linear-gradient(top, white 0%, #dcdcdc 100%);
    background: -o-linear-gradient(top, white 0%, #dcdcdc 100%);
    background: linear-gradient(to bottom, white 0%, #dcdcdc 100%);
}

</style>
<div class="container">
	
	<div class="row ">
        <div class="form-group col-sm-6">
        <label for="">Codigo carta</label>
        <input Type="texto" class="form-control" id='codigo' placeholder="Digite el id de la carta"  required></input>
        </div>
        <div class="form-group col-sm-12 ">
            <label for=""></label>
        <button type="button" id ="consultar"class="btn btn-primary">Consultar</button>
	</div>
</div>
<div class="container" id="info">
    <table id="info">
        <tr id="cantidad">
            <th>Cantidad facturas</th>
         
        
        </tr>
        <tr id="valor">
            <th>Valor</th>
            
        
        </tr>
        <tr id="estado">
            <th>Estado</th>
            
        </tr>
        <tr id="empresa">
            <th>Empresa</th>
           
        </tr>
   </table>

</div>

<h1>Facturas contenidas en la carta</h1>
<table class="table" id='tabla' >
  <thead>
    <tr>
      <th scope="col">Factura</th>
	  <th scope="col">Valor</th>
      <th scope="col">Historia</th>
      <th scope="col">Paciente</th>
	  <th scope="col">Estado</th>
    </tr>
  </thead>
  <tbody>
    
  </tbody>
</table>

<a class="btn btn-primary" href="enviocartasfront.php" role="button" target="_blank">Nueva Carta</a>
<a class="btn btn-primary" href="adicionarcarta.php" role="button" target="_blank">Adicionar existente</a>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="visualizarcarta.js"></script>
<link  type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.6.0/dt-1.11.4/datatables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css">
 
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.6.0/dt-1.11.4/datatables.min.js"></script>
</html>