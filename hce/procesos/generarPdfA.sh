#! /bin/bash
#cd /var/www/matrix/ips/reportes/soportes/01/547755-15/800088702-2/
echo $2
cd /var/www/matrix/ips/reportes/soportes/$2/$3/$4/
#respuesta=xvfb-run wkthtmltopdf --margin-top 20mm --margin-bottom 20mm --page-size Letter --header-html cuerpo_$1.html $1.pdf 2>&1
respuesta=xvfb-run html2pdf --margin-top 20mm --margin-bottom 25mm --page-size Letter --load-error-handling ignore --header-html encabezado_$1.html --footer-html pie_$1.html cuerpo_$1.html $1.pdf 2>&1
#respuesta=xvfb-run wkthtmltopdf  --margin-top 20mm --margin-bottom 25mm --page-size Letter --header-html encabezado_$1.html --footer-html pie_$1.html cuerpo_$1.html $1.pdf 2>&1

echo '||||||';
pdfinfo $1.pdf | grep Pages | awk '{print $2}'
