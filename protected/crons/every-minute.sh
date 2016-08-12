#!/bin/bash

PHP="/usr/bin/php"

cd $(dirname $0)
BASEDIR=$(dirname $(dirname `pwd`))

$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/analyze.php
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_daily_counter_android_info.php




