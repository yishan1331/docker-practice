<?php
//https://blog.csdn.net/leedaning/article/details/72781734
$HTTPHOST = explode(":",$_SERVER['HTTP_HOST']);
$publicIP = $HTTPHOST[0];

if (!filter_var($HTTPHOST[0], FILTER_VALIDATE_IP) or $_SERVER['SERVER_PORT'] != "23687") {
    $publicIP = "211.75.243.186";
}
$publicPort = "23687";