#!/bin/bash
container_id=$(docker ps --format '{{.ID}} {{.Names}}' | grep "^mysonet_web" | awk '{print $1}')
if [ ! -z "$container_id" ]; then
    docker exec -it $container_id php /var/www/html/relance_demandes.php
fi
