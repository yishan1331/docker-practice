<?php
function connection($url, $methods, $data = null)
{
    try {
        $ch = curl_init($url);
        // Check if initialization had gone wrong*    
        if ($ch === false) {
            throw new Exception('failed to initialize');
        }
        if ($methods !== "GET") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methods);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data)))
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');

        $content = curl_exec($ch);
        $result = json_decode($content, true);
        // Check the return value of curl_exec(), too
        if ($content === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        /* Process $content here */
        
        // Close curl handle
        curl_close($ch);
        return $result;
    } catch(Exception $e) {
        return $e;
    }
    // if ($methods == "GET"){
    //     $options = array(
    //         "ssl"=>array(
    //             "verify_peer"=>false,
    //             "verify_peer_name"=>false,
    //         ),
    //     );
    // } else {
    //     $options = array(
    //         'http' => array(
    //             'method' => $methods,
    //             'content' => json_encode($data),
    //             'header' => "Content-Type: application/json\r\n" .
    //             "Accept: application/json\r\n"
    //         ),
    //         "ssl"=>array(
    //             "verify_peer"=>false,
    //             "verify_peer_name"=>false,
    //         ),
    //     );
    // }
    // $context = stream_context_create($options);
    // $result = file_get_contents($url, false, $context);
    // $Arr = json_decode($result, true);
 
    // return $Arr;
}
?>