#!/bin/bash

PHP="/usr/bin/php"
if [ -f "/etc/php.ini" ]; then
  PHP="$PHP -c /etc/php.ini"
fi

cd $(dirname $0)
BASEDIR=$(dirname $(dirname `pwd`))


$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/export_daily_active_device_ids.php yesterday
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_daily_counter.php yesterday
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_daily_counter.php
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_daily_counter_android_info.php yesterday
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_daily_counter_mutual_with_external_sites.php yesterday

$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_seconds_spent_per_day.php yesterday
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_daily_retention.php yesterday
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/cache_common_data_for_analyze.php yesterday
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/drop_old_daily_active_devices_tables.php yesterday
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/drop_old_hourly_active_devices_tables.php yesterday


$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_weekly_retention.php yesterday
$PHP -d 'date.timezone=Asia/shanghai' $BASEDIR/api/recalculate_monthly_retention.php yesterday



