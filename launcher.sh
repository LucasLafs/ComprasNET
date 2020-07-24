#!/bin/bash

/usr/bin/php7.0 /var/www/html/ComprasNET/api/timeout-v1.php >> /var/log/comprasnet/getapi-v1.log &
/usr/bin/php7.0 /var/www/html/ComprasNET/api/timeout-v2.php >> /var/log/comprasnet/getapi-v2.log &

