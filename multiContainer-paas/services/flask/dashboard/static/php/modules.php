<?php
date_default_timezone_set("Asia/Taipei");
$postBody = file_get_contents("php://input");
$postBody = json_decode($postBody);
$methods = $postBody->methods;
$whichFunction = $postBody->whichFunction;
include("./api.php");
include("./apiJsonBody.php");
include("./connection.php");
include("./commonFunction.php");

if (isset($postBody->vuePage)) {
    $vuePage = ['elecboard', 'machstatus', 'yieldstatistics', 'activation', 'operatelog', 'alarmhistory', 'workorderhistory', 'qualityinsp', 'systemset', 'machmalfunction', 'testpage', 'devicesetting','generalmach'];
    $vue_pageSelect = $postBody->vuePage;
    if (in_array($vue_pageSelect,$vuePage,true)){
        $vue_pagePosition = array_search($vue_pageSelect,$vuePage,true);
        include("./views/" . $vuePage[$vue_pagePosition] . ".php");
    }
} else {
    include("./views/accountAction.php");
    include("./views/common.php");
    include("./views/sensor.php");
}

$getData = $whichFunction($postBody);
echo json_encode($getData);
?>