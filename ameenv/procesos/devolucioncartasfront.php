<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolucion cartas de cobro</title>
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    #toast-container > .toast {
    max-width: 1200px !important;
    width: 90% !important
}
</style>

<body>
<div class="container">
	<h1>MODULO DE DEVOLUCION X CARTA DE COBRO (NIT)</h1>
	<div class="row ">
        <div class="form-group col-sm-6" style="padding-top:10px">
        <label for="">Codigo carta cobro a devolver</label>
        <input Type="texto" class="form-control" id='codigo' placeholder="Digite el id de la carta"  required></input>
        </div>
        <div class="form-group col-sm-12 " style="padding-top:10px">
            <label for=""></label>
        <button type="button" id ="consultar"class="btn btn-primary" >Consultar</button>
	</div>
</div>
<div class="container" id="info" style="padding-top:20px">
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
<div class="container" id="info" style="padding-top:50px">
<table id="tabla" class="display table " style="width:100%">
        <thead>
            <tr>
                <th> <input  id = "all" type="checkbox"> Seleccionar</th>
                <th>Factura</th>
                <th>Valor</th>
                <th>Historia</th>
                <th>Paciente</th>
                <th>Estado</th>
                <th>Causal</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th>Factura</th>
                <th>Valor</th>
                <th>Historia</th>
                <th>Paciente</th>
                <th>Estado</th>
                <th>Causal</th>
            </tr>
        </tfoot>
    </table>
    <button  id ="grabar" class="btn btn-primary">Grabar</button>
    <a class="btn btn-primary" href="visualizardevolucion.php" role="button" target="_blank">Visualizar Devolucion</a>
</div>
    
</body>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
<script src="devolucioncartas.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</html>