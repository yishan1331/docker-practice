#!/bin/bash
scriptPath=$(dirname "$(readlink -f "$0")")
source "${scriptPath}/.env.sh"

/usr/local/bin/python /var/app/dailyCalculateAggregatelyApirecords.py > /var/app/dailyCalculateAggregatelyApirecords_output.txt 2>&1