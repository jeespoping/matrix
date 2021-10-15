/**
 * Función para cerrar la ventana
 * @by: sebastian.nevado
 * @date: 2021/10/14
 */
function cerrarVentana()
{ 
    if (confirm("Est\u00e1 seguro de salir?"))
        window.close();
    else
        return false;
}