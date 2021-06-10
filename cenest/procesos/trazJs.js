//**Archivo de funciones JS de trazMaster para todas la validaciones y consultas ajax */


// funcion que muestra el modal segun el id de parametro
function showModal(modalName){
    $('#'+modalName).on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
    }); 
    $("#"+modalName).modal('show');
}

// funcion que remueve las clases de las respuestas ajax y limpia el div
function removerClase(divName){
    $("#"+divName).removeClass("alert alert-success");
    $("#"+divName).removeClass("alert alert-danger");
    $("#"+divName).html("");
}

// funcion que realiza el cambio de estado de un codigo de reuso a traves de ajax de acuerdo al cambio de checkbox
function changeCheckBox(idCheck,idCenest,idTr)
{
    trTable = document.getElementById(idTr);
    checkBox = document.getElementById(idCheck);
    wemp_pmla = document.getElementById('wemp_pmla').value;
    let urlget = '';
    let checked = '';
    if (checkBox.checked){
        urlget = 'trazProcess.php?accion=changeStateDispo&wemp_pmla='+wemp_pmla+'&stateDispo=on&idChangeState='+idCenest;
        checked = 'checked';
    }else{
        urlget = 'trazProcess.php?accion=changeStateDispo&wemp_pmla='+wemp_pmla+'&stateDispo=st&idChangeState='+idCenest;
        checked = 'unchecked';
    }
    if (confirm('En realidad desea cambiar el estado de este dispositivo?')){
        document.getElementById(idCheck).checked = checkBox.checked;
    $.ajax({
        url: urlget,
        success: function(respuesta) {
            if (checked == 'checked'){
                trTable.style.backgroundColor = '#ffeaa7';
                trTable.style.borderBottomColor = '#ffeaa7';
            }else{
                trTable.style.backgroundColor = '';
                trTable.style.borderBottomColor = '#EEEEEE';
            }
        },
        error: function() {
            alert("Error al intentar adicionar el usuario");
        }
    });
    }else{
        document.getElementById(idCheck).checked = !checkBox.checked;
    }
}

// funcion que valida si lo que se escribe, contiene letras
function tiene_letras(texto){
    var letras="abcdefghyjklmnñopqrstuvwxyz";
    texto = texto.toLowerCase();
    for(i=0; i<texto.length; i++){
       if (letras.indexOf(texto.charAt(i),0)!=-1){
          return 1;
       }
    }
    return 0;
 } 

// funcion que crea un nuevo reuso yendo a trazprocess por ajax
function newReuso(idReusoIn){
    let idDivInsReuso = document.getElementById(idReusoIn);
    let idDivNumCod = '';
    let idReuso = document.getElementById('idReuso'+idReusoIn).value;
    let codReusoIns = document.getElementById('codReusoIns'+idReusoIn).value;
    let codCcoDispo = document.getElementById('codCcoDispo'+idReusoIn).value;
    let numCalIns = document.getElementById('numCalIns'+idReusoIn).value;
    let codItemIns = document.getElementById('codItemIns'+idReusoIn).value;
    let invimaIns = document.getElementById('invimaIns'+idReusoIn).value;
    let numReusoIns = document.getElementById("numReusoIns"+idReusoIn).value;
    let obserReusoIns = document.getElementById("obserReusoIns"+idReusoIn).value;
    let codDispoReu = document.getElementById("codDispoReu"+idReusoIn).value;
    let wemp_pmla = document.getElementById("wemp_pmla").value;
    let accion = 'insertReuso';
    if (idReusoIn == 'undefined'){
        idDivNumCod = document.getElementById('numCodAct').value;
        if (tiene_letras(idDivNumCod)){
            alert("La cantidad de c\u00F3digos debe ser num\u00E9rico");
            return false;
        }
    }
    let jsonParameters = {
        idDivNumCod,
        wemp_pmla,
        idReuso,
        codReusoIns,
        codCcoDispo,
        numCalIns,
        codItemIns,
        invimaIns,
        numReusoIns,
        obserReusoIns,
        codDispoReu,
        accion
    };
    //console.log(jsonParameters);
    $.ajax({
        type: 'POST',
        url: 'trazProcess.php',
        data: jsonParameters,
        success: function(respuesta) {
            //console.log(respuesta);
            data = JSON.parse(respuesta);
            if (data.error){
                let element = idDivInsReuso;
                $("#"+idDivInsReuso.id).addClass("alert alert-danger");
                element.setAttribute("role", "alert");
                $("#"+idDivInsReuso.id).html("Error: " + data.error );
            }else{
                let element = idDivInsReuso;
                $("#"+idDivInsReuso.id).addClass("alert alert-success");
                element.setAttribute("role", "alert");
                $("#"+idDivInsReuso.id).html("El reuso fue agregado con \u00E9xito");
            }
            
        },
        error: function() {
            alert("Error al adicionar el dispositivo");
        }
    });
}

// funcion que envia peticion a trazProcess para crear un nuevo dispositivo y envia respuesta
function newDispo(){
    let newDispo = document.getElementById('newDispo');
    let codDispoInsert = document.getElementById('codDispoInsert').value;
    let descDispoInsert = document.getElementById('descDispoInsert').value;
    let numCalDispoInsert = document.getElementById('numCalDispoInsert').value;
    let codItemInsert = document.getElementById('codItemInsert').value;
    let invDispoInsert = document.getElementById('invDispoInsert').value;
    let codCcoDispo = document.getElementById('codCcoDispo').value;
    let limReuDispoInsert = document.getElementById('limReuDispoInsert').value;
    let wemp_pmla = document.getElementById("wemp_pmla").value;
    let accion = 'insertDispo';
    let jsonParameters = {
        wemp_pmla,
        codDispoInsert,
        codCcoDispo,
        descDispoInsert,
        numCalDispoInsert,
        codItemInsert,
        invDispoInsert,
        limReuDispoInsert,
        accion
    };
    // console.log(jsonParameters);
    if(descDispoInsert != ''){
        if (confirm("Est\341 seguro de insertar el dispositivo? Una vez ingresado NO podr\341 modificarse!")){
            $.ajax({
            type: 'POST',
            url: 'trazProcess.php',
            data: jsonParameters,
                success: function(respuesta) {
                    // console.log(respuesta);
                    data = JSON.parse(respuesta);
                    if (data.error){
                        let element = newDispo;
                        $("#"+newDispo.id).addClass("alert alert-danger");
                        element.setAttribute("role", "alert");
                        $("#"+newDispo.id).html("Error: " + data.error );
                    }else{
                        let element = newDispo;
                        $("#"+newDispo.id).addClass("alert alert-success");
                        element.setAttribute("role", "alert");
                        $("#"+newDispo.id).html("El dispositivo: " + descDispoInsert + " fue adicionado con \u00E9xito");
                    }
                    
                },
                error: function() {
                    alert("Error al adicionar el dispositivo");
                }
            });
        }
    }else{
        alert("El campo Descripci\u00F3n no puede estar vac\u00EDo");
    }  
}

/**
 * Funcion para editar el dispositivo maestro
 */
function editDispo(){
    let editDispo = document.getElementById('editDispo');
    let codDispoInsert = document.getElementById('codDispoInsertMaestro').value;
    let descDispoInsert = document.getElementById('descDispoInsertMaestro').value;
    let numCalDispoInsert = document.getElementById('numCalDispoInsertMaestro').value;
    let codItemInsert = document.getElementById('codItemInsertMaestro').value;
    let invDispoInsert = document.getElementById('invDispoInsertMaestro').value;
    let codCcoDispo = document.getElementById('codCcoDispoMaestro').value;
    let limReuDispoInsert = document.getElementById('limReuDispoInsertMaestro').value;
    let wemp_pmla = document.getElementById("wemp_pmla").value;
    let accion = 'insertDispoMaestro';
    let jsonParameters = {
        wemp_pmla,
        codDispoInsert,
        codCcoDispo,
        descDispoInsert,
        numCalDispoInsert,
        codItemInsert,
        invDispoInsert,
        limReuDispoInsert,
        accion
    };
    //console.log(jsonParameters);
    if(descDispoInsert != ''){
        if (confirm("Est\341 seguro de actualizar el dispositivo?")){
            $.ajax({
            type: 'POST',
            url: 'trazProcess.php',
            data: jsonParameters,
                success: function(respuesta) {
                   // console.log(respuesta);
                    data = JSON.parse(respuesta);
                    if (data.error){
                        let element = editDispo;
                        $("#"+editDispo.id).addClass("alert alert-danger");
                        element.setAttribute("role", "alert");
                        $("#"+editDispo.id).html("Error: " + data.error );
                    }else{
                        let element = editDispo;
                        $("#"+editDispo.id).addClass("alert alert-success");
                        element.setAttribute("role", "alert");
                        $("#"+editDispo.id).html("El dispositivo: " + descDispoInsert + " fue actualizado con \u00E9xito");
                    }
                    
                },
                error: function() {
                    alert("Error al actualizar el dispositivo");
                }
            });
        }
    }else{
        alert("El campo Descripci\u00F3n no puede estar vac\u00EDo");
    }  
}

// funcion para actualizar el reuso
function updateReuso(idRegistro){
    let trazUpdate = document.getElementById('trazUpdate'+idRegistro);
    let numCalibre = document.getElementById('numCalibre'+idRegistro);
    let codItem = document.getElementById('codItem'+idRegistro);
    let invima = document.getElementById('invima'+idRegistro);
    let limite = document.getElementById('limite'+idRegistro);
    let observacion = document.getElementById('observacion'+idRegistro);
    let wemp_pmla = document.getElementById("wemp_pmla");
    let dispoNombreCod = document.getElementById("dispoNombreCod"+idRegistro);
    let accion = 'actualizarReuso';
    let jsonParameters = {
        wemp_pmla: wemp_pmla.value,
        numCalibre: numCalibre.value,
        codItem: codItem.value,
        invima: invima.value,
        limite: limite.value,
        observacion: observacion.value,
        trazUpdate: trazUpdate.value,
        accion
    };
    // console.log(jsonParameters);
    $.ajax({
        type: 'POST',
        url: 'trazProcess.php',
        data: jsonParameters,
        success: function(respuesta) {
            // console.log(respuesta);
             data = JSON.parse(respuesta);
            if (data.error){
                let element = document.getElementById(trazUpdate.value);
                $("#"+trazUpdate.value).addClass("alert alert-danger");
                element.setAttribute("role", "alert");
                 $("#"+trazUpdate.value).html("Error: " + data.error );
            }else{
                let element = document.getElementById(trazUpdate.value);
                $("#"+trazUpdate.value).addClass("alert alert-success");
                element.setAttribute("role", "alert");
                 $("#"+trazUpdate.value).html("El dispositivo: " + dispoNombreCod.value + " fue actualizado con \u00E9xito");
            }
            
        },
        error: function() {
            alert("Error al intentar actualizar el reuso");
        }
    });
}

/**
 * Funcion que actualiza el usuario desde ajax
 * @param {*} codigo codigo de matrix
 * @param {*} nomState estado activo o inactivo
 * @param {*} nomPrior prioridad del usuario
 * @param {*} idDiv id del div del modal
 * @returns 
 */
function updateUser(codigo,nomState,nomPrior,idDiv){
    let priority = document.getElementById(nomPrior);
    let state = document.getElementById(nomState);
    let wemp_pmla = document.getElementById("wemp_pmla");
    if (priority.selectedIndex == null || priority.selectedIndex == 0) { 
        alert("Debes seleccionar la prioridad del usuario")
        return false
    }
    if (state.selectedIndex == null || state.selectedIndex == 0) { 
        alert("Debes seleccionar el estado del usuario")
        return false
    }
    // console.log(state.value,priority.value,codigo,wemp_pmla.value);
    $("#"+idDiv+"App").html('');
    $.ajax({
        url: 'trazProcess.php?accion=updateUser&wemp_pmla='+wemp_pmla.value+'&codusu='+codigo+'&userPriorUpdate='+priority.value+'&userStateUpdate='+state.value,
        success: function(respuesta) {
            // console.log(respuesta);
             data = JSON.parse(respuesta);
            if (data.error){
                let element = document.getElementById(idDiv+'App');
                $("#"+idDiv+"App").removeClass("alert alert-success");
                $("#"+idDiv+"App").addClass("alert alert-danger");
                element.setAttribute("role", "alert");
                 $("#"+idDiv+"App").append("Error: " + data.error );
            }else{
                let element = document.getElementById(idDiv+'App');
                $("#"+idDiv+"App").removeClass("alert alert-danger");
                $("#"+idDiv+"App").addClass("alert alert-success");
                element.setAttribute("role", "alert");
                 $("#"+idDiv+"App").append("El usuario con C\u00F3digo: " + data.datos.codigo + " fue actualizado con \u00E9xito");
            }
            
        },
        error: function() {
            alert("Error al intentar actualizar el usuario");
        }
    });
}

/**
 * Funcion que elimina el centro de costos desde el ajax
 * @param {*} codigo 
 * @param {*} idDiv 
 * @returns 
 */
function deleteCco(codigo,idDiv){
    let wemp_pmla = document.getElementById("wemp_pmla");
    if (confirm('En realidad desea eliminar el Centro de Costos con c\u00F3digo: ' + codigo + '?')){
        $("#"+idDiv+'App').html('');
        $.ajax({
            url: 'trazProcess.php?accion=deleteCco&wemp_pmla='+wemp_pmla.value+'&codCco='+codigo,
            success: function(respuesta) {
                // console.log(respuesta);
                data = JSON.parse(respuesta);
                if (data.error){
                    let element = document.getElementById(idDiv+'App');
                    $("#"+idDiv+'App').removeClass("alert alert-success");
                    $("#"+idDiv+'App').addClass("alert alert-danger");
                    element.setAttribute("role", "alert");
                    $("#"+idDiv+'App').append("Error: " + data.error );
                }else{
                    let element = document.getElementById(idDiv+'App');
                    $("#"+idDiv+'App').removeClass("alert alert-danger");
                    $("#"+idDiv+'App').addClass("alert alert-success");
                    element.setAttribute("role", "alert");
                    $("#"+idDiv+'App').append("El Centro de costos con C\u00F3digo: " + data.datos.codigo + " fue eliminado con \u00E9xito");
                }
                
            },
            error: function() {
                alert("Error al intentar eliminar el centro de costos");
            }
        });
    }else{
        return false;
    }
}

/**
 * Funcion para eliminar usuario desde ajax
 * @param {*} codigo 
 * @param {*} idDiv 
 * @returns 
 */
function deleteUser(codigo,idDiv){
    let wemp_pmla = document.getElementById("wemp_pmla");
    if (confirm('En realidad desea eliminar el usuario con c\u00F3digo: ' + codigo + '?')){
        $("#"+idDiv+'App').html('');
        $.ajax({
            url: 'trazProcess.php?accion=deleteUser&wemp_pmla='+wemp_pmla.value+'&codusu='+codigo,
            success: function(respuesta) {
                // console.log(respuesta);
                data = JSON.parse(respuesta);
                if (data.error){
                    let element = document.getElementById(idDiv+'App');
                    $("#"+idDiv+'App').removeClass("alert alert-success");
                    $("#"+idDiv+'App').addClass("alert alert-danger");
                    element.setAttribute("role", "alert");
                    $("#"+idDiv+'App').append("Error: " + data.error );
                }else{
                    let element = document.getElementById(idDiv+'App');
                    $("#"+idDiv+'App').removeClass("alert alert-danger");
                    $("#"+idDiv+'App').addClass("alert alert-success");
                    element.setAttribute("role", "alert");
                    $("#"+idDiv+'App').append("El usuario con C\u00F3digo: " + data.datos.codigo + " fue eliminado con \u00E9xito");
                }
                
            },
            error: function() {
                alert("Error al intentar eliminar el usuario");
            }
        });
    }else{
        return false;
    }
}

/**
 * funcion para consultar los datos del modal de adicionar usuario
 * @returns 
 */
function addUser(){
    let priority = document.getElementById("userPriorAdd");
    let state = document.getElementById("userStateAdd");
    let userName = document.getElementById("nomUsuAdd");
    let userCod = document.getElementById("codUsuAdd");
    let wemp_pmla = document.getElementById("wemp_pmla");
    let regExpCode = /[0-9]/;
    if (!regExpCode.test(userCod.value)){
        alert("El codigo Matrix debe ser numerico");
        return false;
    }
    
    let regExpNombre = /[A-Za-z]/;
    if (!regExpNombre.test(userName.value)){
        alert("Debes ingresar un nombre v\341lido sin tilde ni \361");
        return false;
    }
    if (priority.selectedIndex == null || priority.selectedIndex == 0) { 
        alert("Debes seleccionar la prioridad del usuario")
        return false
    }
    if (state.selectedIndex == null || state.selectedIndex == 0) { 
        alert("Debes seleccionar el estado del usuario")
        return false
    }
    // console.log(state.value,priority.value,userCod.value,userName.value,wemp_pmla.value);
    $("#divAddUserApp").html('');
    $.ajax({
        url: 'trazProcess.php?accion=accionadd&wemp_pmla='+wemp_pmla.value+'&codusu='+userCod.value+'&userName='+userName.value+'&userPriorAdd='+priority.value+'&userStateAdd='+state.value,
        success: function(respuesta) {
            //console.log(respuesta);
             data = JSON.parse(respuesta);
            if (data.error){
                $("#divAddUserApp").removeClass("alert alert-success");
                let element = document.getElementById("divAddUserApp");
                $("#divAddUserApp").addClass("alert alert-danger");
                element.setAttribute("role", "alert");
                 $("#divAddUserApp").append("Error: " + data.error );
            }else{
                $("#divAddUserApp").removeClass("alert alert-danger");
                let element = document.getElementById("divAddUserApp");
                $("#divAddUserApp").addClass("alert alert-success");
                element.setAttribute("role", "alert");
                 $("#divAddUserApp").append("El usuario: " + data.datos.nombre + " fue adicionado con \u00E9xito");
            }
            
        },
        error: function() {
            alert("Error al intentar adicionar el usuario");
        }
    });
}

/**
 * funcion para consultar los datos del modal de adicionar usuario
 * @returns 
 */
 function addCco(){
    let ccoAdd = document.getElementById("CcoAdd");
    let wemp_pmla = document.getElementById("wemp_pmla");
    let regExpCode = /[0-9]/;
    if (!regExpCode.test(ccoAdd.value)){
        alert("El centro de costos debe ser numerico");
        return false;
    }
    // console.log(ccoAdd);
    $("#divAddCcoApp").html('');
    $.ajax({
        url: 'trazProcess.php?accion=addNewCco&wemp_pmla='+wemp_pmla.value+'&ccoAdd='+ccoAdd.value,
        success: function(respuesta) {
            // console.log(respuesta);
             data = JSON.parse(respuesta);
            if (data.error){
                $("#divAddCcoApp").removeClass("alert alert-success");
                let element = document.getElementById("divAddCcoApp");
                $("#divAddCcoApp").addClass("alert alert-danger");
                element.setAttribute("role", "alert");
                 $("#divAddCcoApp").append("Error: " + data.error );
            }else{
                $("#divAddCcoApp").removeClass("alert alert-danger");
                let element = document.getElementById("divAddCcoApp");
                $("#divAddCcoApp").addClass("alert alert-success");
                element.setAttribute("role", "alert");
                 $("#divAddCcoApp").append("El centro de costos: " + data.datos.nombreCco + " fue adicionado con \u00E9xito");
            }
            
        },
        error: function() {
            alert("Error al intentar adicionar el Centro de Costos");
        }
    });
}

 //funcion que Valida los campos de los formularios para el maestro de usuarios.
function validarCamposMaestro() {
    let prioridad = document.getElementById("userPriorAdd");
    let estado = document.getElementById("userStateAdd"); 
    if (prioridad.selectedIndex == null || prioridad.selectedIndex == 0) { 
        alert("Debes seleccionar la prioridad del usuario")
        return false
    }
    if (estado.selectedIndex == null || estado.selectedIndex == 0) { 
        alert("Debes seleccionar el estado del usuario")
        return false
    }
}

// Funcion que construye los modales
function buildModalHeader(data,fn){
    let contenidoModal = '';
    let idModalBody = data.datos.idModal + 'tmp';
    contenidoModal += '<div class="modal-dialog modal-lg" role="document">';
    contenidoModal += '<div class="modal-content">';
    contenidoModal += '<div class="modal-header">'
    contenidoModal += '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    contenidoModal += '</div>';
    contenidoModal += '<div class="modal-body" id="'+idModalBody+'">';
    contenidoModal += '</div>';
    contenidoModal += '</div>';
    contenidoModal += '</div>';
    $("#"+data.datos.idModal).html(contenidoModal);
    showModal(data.datos.idModal);
    buildModal(data,idModalBody,fn);
   
}

// funcion que crea llena el select de los dispositivos
function createSelectDispo(){
    ccoSelect = document.getElementById('filtroCco').value;
    wemp_pmla = document.getElementById('wemp_pmla').value;
    $("#filtroDispo").html('<option selected disabled>Seleccione...</option>');
    urlget = 'trazProcess.php?accion=findDispoXCco&wemp_pmla='+wemp_pmla+'&codCcoDispo='+ccoSelect;
    if (ccoSelect != 'TODOSCCO'){
        $.ajax({
            url: urlget,
            success: function(respuesta) {
                $("#filtroDispo").html(respuesta);
                $("#filtroDispo").append("<option value='TODOSDISPO'>TODOS LOS DISPOSITIVOS</option>");
                $("#filtroDispo").append("<option selected disabled>Seleccione...</option>");
            },
            error: function() {
                alert("Error al intentar llenar los datos");
            }
        });
    }else{
        return false;
    }
}


function createSelectCodReu(){
    return false;
}


function modificar(idRegistro,accion,Coddispo,ccoUnidad,id){
    // definimos la anchura y altura de la ventana
    console.log(id,idRegistro,accion.value,Coddispo,ccoUnidad);
    let element = document.getElementById("divAddUser");
    $("#divAddUser").addClass("alert alert-danger");
    element.setAttribute("role", "alert");
    $("#divAddUser").html("Error: " + data.error );
}

// Funcion que activa los tablinks
function openCity(evt, cityName) { //pestañas
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}


// Funcion que envia los datos a trazProcess de acuerdo a la funcion y recibe como respuesta el cuerpo del modal
function buildModal(data,id,fn){
    let wemp_pmla = document.getElementById("wemp_pmla");
    var urlget = '';
    let cco = '';
    let codDispo = '';
    switch (fn) {
        case 'Modificar':
            urlget = 'trazProcess.php?accion=modalModificarReuso&wemp_pmla='+wemp_pmla.value+'&idRegistro='+data.datos.id+'&Coddispo='+data.datos.Coddispo+'&codCcoDispo='+data.datos.Codcco;
            break;
        case 'modificarDispoMaestro':
            urlget = 'trazProcess.php?accion=modalModificarDispoMaestro&wemp_pmla='+wemp_pmla.value+'&codCcoDispo='+data.datos.idCco+'&Coddispo='+data.datos.codIt;
            break;
        case 'VerReuso':
            urlget = 'trazProcess.php?accion=modalVerReuso&wemp_pmla='+wemp_pmla.value+'&idRegistro='+data.datos.id+'&codReuso13='+data.datos.CodReuso;
            break;
        case 'insertarDispo':
            urlget = 'trazProcess.php?accion=modalMaestroDispo&wemp_pmla='+wemp_pmla.value+'&idCco='+data.datos.idCco;
            break;
        case 'insertarReuso':
            if (data.datos.idCco){
                cco = data.datos.idCco;
            }else{
                cco = data.datos.Codcco;
            }
            if (data.datos.Coddispo){
                codDispo = data.datos.Coddispo;
            }else{
                codDispo = data.datos.codIt;
            }
            urlget = 'trazProcess.php?accion=modalInsertarReuso&wemp_pmla='+wemp_pmla.value+'&idRegistro='+codDispo+'&idReuso='+data.datos.id+'&codCcoDispo='+cco;
            break;
        default:
            return false;
      }
    $.ajax({
        url: urlget,
        success: function(respuesta) {
            $("#"+id).html(respuesta);
        },
        error: function() {
            alert("Error al intentar ingresar los datos");
        }
    });
}

// funcion que llama el ajax a para construir el modal del maestro de dispositivos
function buildModalMaestro(data,id){
    let wemp_pmla = document.getElementById("wemp_pmla");
    $.ajax({
        url: 'trazProcess.php?accion=modalMaestroDispo&wemp_pmla='+wemp_pmla.value+'&idCco='+data.datos.idCco,
        success: function(respuesta) {
            $("#"+id).html(respuesta);
        },
        error: function() {
            alert("Error al intentar crear formulario");
        }
    });
}
