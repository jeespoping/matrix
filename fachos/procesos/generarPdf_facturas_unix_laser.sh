#! /bin/bash
cd /var/www/matrix/fachos/procesos/facturas
respuesta=xvfb-run html2pdf --load-error-handling ignore  --margin-top 0mm --margin-bottom 0mm --margin-left 3mm --margin-right 2mm --page-width 190mm --page-height 220mm $1.html $1.pdf 2>&1
echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'

