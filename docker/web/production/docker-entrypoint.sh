#!/bin/sh
printenv | sed 's/^\(.*\)$/export \1/g' | grep -E "^export SSH_" > ./config/ssh.env
service cron start
./orm orm:generate-proxies
php-fpm -F -O
