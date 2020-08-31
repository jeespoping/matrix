function mostrarop1(fechaini,historia)
{
 document.getElementById(fechaini).style.display = 'block';
 document.getElementById(historia).style.display = 'none';
}

function mostrarop2(historia,fechaini)
{
 document.getElementById(fechaini).style.display = 'none';
 document.getElementById(historia).style.display = 'block';
}