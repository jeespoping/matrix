#! /bin/bash
cd /var/www/matrix/ips/reportes/facturas
respuesta=xvfb-run html2pdf --load-error-handling ignore  --margin-top 18mm --margin-bottom 0mm --margin-left 2mm --margin-right 2mm --page-width 210mm --page-height 297mm $1.html $1.pdf 2>&1
echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'
	
