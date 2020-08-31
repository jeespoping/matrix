// autocomplet : this function will be executed every time we change the text
// PARA DIAGNOSTICO PRINCIPAL:
function autocomplet()
{
    var min_length = 0; // min caracters to display the autocomplete
    var keyword = $('#country_id').val();
    var tipoAtenc = document.getElementById('tipoAten').value;
    //var tipoAtenc = tipoAtencion.value;
    if (keyword.length >= min_length) {
        $.ajax({
            url: 'GrdProcess.php',
            type: 'POST',
            data: {keyword:keyword, tipoAten:tipoAtenc},
            success:function(data){
                $('#country_list_id').show();
                $('#country_list_id').html(data);
            }
        });
    } else {
        $('#country_list_id').hide();
    }
}

// set_item : this function will be executed when we select an item
function set_item(item) {
    // change input value
    $('#country_id').val(item);
    // hide proposition list
    $('#country_list_id').hide();
}


////////////////////////////////////////////////


function autocomplet2()
{
    var min_length = 0; // min caracters to display the autocomplete
    var keyword = $('#country_id').val();
    var tipoAtenc = document.getElementById('tipoAten').value;
    //var tipoAtenc = tipoAtencion.value;
    if (keyword.length >= min_length) {
        $.ajax({
            url: 'GrdProcess.php',
            type: 'POST',
            data: {keyword:keyword, tipoAten:tipoAtenc},
            success:function(data){
                $('#country_list_id2').show();
                $('#country_list_id2').html(data);
            }
        });
    } else {
        $('#country_list_id2').hide();
    }
}

// set_item : this function will be executed when we select an item
function set_item2(item) {
    // change input value
    $('#country_id').val(item);
    // hide proposition list
    $('#country_list_id2').hide();
}
