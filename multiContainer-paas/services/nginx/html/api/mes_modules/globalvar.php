<?php
global $IP;
if (empty($IP)) {
    $IP = '192.6.6.230'; // 單機版serve
    // $IP = 'localhost'; // 機聯網serve
    // $IP = '211.75.243.185'; // 本地
    // $IP = '211.75.243.186'; // 單機本地
}
$publicIP = $IP;
// $publicPort = "3687"; // 機聯網serve
// $publicPort = "23687"; // 單機本地
$publicPort = "3687" ;// 單機版serve