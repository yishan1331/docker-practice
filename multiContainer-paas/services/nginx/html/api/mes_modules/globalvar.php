<?php
global $IP;
if (empty($IP)) {
    $IP = '192.6.6.230'; // 單機版serve
}
$publicIP = $IP;
$publicPort = "3687" ;// 單機版serve