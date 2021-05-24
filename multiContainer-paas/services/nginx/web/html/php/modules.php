<?php
date_default_timezone_set("Asia/Taipei");
$postBody = file_get_contents("php://input");
$postBody = json_decode($postBody);
$methods = $postBody->methods;
$whichFunction = $postBody->whichFunction;

include("./globalvar.php");
// include("./views/accountAction.php");
// include("./views/systemSet.php");
include("./common.php");
// include("./views/tool/toolAction.php");

$postdata = $whichFunction($postBody->postdata, $publicIP);
if ($methods == "NOAPI") {
    // echo json_encode($postdata);
} else {
    if ($methods == "GET") {
        // $getfunction = new FunctionGetClass;
        // $postdata = $getfunction->$whichFunction($postBody,$returnData);
        $options = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
    } else {
        // $postfunction = new FunctionPostClass;
        // $postdata = $postfunction->$whichFunction($postBody,$returnData);
        $options = array(
            'http' => array(
                'method' => $methods,
                'content' => json_encode($postdata[1]),
                'header' => "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
    }
    $context = stream_context_create($options);
    $result = file_get_contents($postdata[0], false, $context);
    $Arr = json_decode($result, true);
    echo json_encode($Arr);
}
