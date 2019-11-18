#!/bin/bash
scriptDir=$(cd "$(dirname "$0")"; pwd)
cd $scriptDir
. ../include.sh

while true
do
    TIME=`date +%s`
    $MYSQL -e "SELECT id FROM ims_shopping_order_goods" | sed 1d | $PHP ./split.php
    sleep 60
done
