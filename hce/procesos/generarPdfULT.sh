#! /bin/bash
cd /var/www/matrix/hce/reportes/cenimp
respuesta=xvfb-run wkhtmltopdf --load-error-handling ignore --margin-top 20mm --page-size Letter --header-html encabezado_$1.html --footer-html pie_$1.html cuerpo_$1.html $1.pdf 2>&1
echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'