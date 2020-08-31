#! /bin/bash
cd /var/www/matrix/ips/procesos/soportes
respuesta=xvfb-run html2pdf --load-error-handling ignore  --margin-top 0mm --margin-bottom 1mm --margin-left 3mm --margin-right 2mm --page-width 208mm --page-height 279mm $1.html $1.pdf 2>&1
echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'
	
