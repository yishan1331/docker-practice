<?php
set_time_limit(15);
date_default_timezone_set('Asia/Taipei');

$Query_Device_Response = Query_Device();
// echo json_encode($Query_Device_Response);
if($Query_Device_Response) {
    Push_Message($Query_Device_Response);
}

function Query_Device() {
    $device_obj = array();
    //查詢machine_status_head，確認該機台最後更新時間
    $url = "https://localhost:3687/api/CHUNZU/1.0/rd/CommonUse/SpecificKey/mes_device_status_*?uid=@sapido@PaaS&pattern=yes";
    $options = array(
       "ssl"=>array(
           "verify_peer"=>false,
           "verify_peer_name"=>false,
       ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $machine_status = json_decode($result, true);
    if ($machine_status['Response'] != 'ok' || count($machine_status['QueryValueData']) == 0) {//先抓是否抓取成功，成功的話繼續執行
        $machine_status_data = [];
    } else {
        $machine_status_data = $machine_status['QueryValueData'];
    }

    foreach ($machine_status_data as $key => $value) {
        if (is_string($value['machine_main'])) {
            $machine_main = json_decode($value['machine_main'], true);
        } else {
            $machine_main = $value['machine_main'];
        }
        $this_device_id = $value['device_id'];
        if (isset($machine_main['upload_at'])) {
            $device_obj[$this_device_id] = array(
                'machine_back_time' => $machine_main['upload_at']
            );
        } else {
            $device_obj[$this_device_id] = array(
                'machine_back_time' => date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - 601)
            );
        }
    }

    $symbols = new stdClass();
    $symbols->combine = [];
    $whereAttr = new stdClass();
    $whereAttr->combine = [];
    $index = 2;
    $condition = array();
    foreach ($device_obj as $device_id => $device_data) {
        $symbols_data = new stdClass();
        $symbols_data->device_id = ["equal"];
        $symbols_data->upload_at = ["in_and"];
        array_push($symbols->combine, $symbols_data);
        $whereAttr_data = new stdClass();
        $whereAttr_data->device_id = [$device_id];
        $whereAttr_data->upload_at = ["subcondition_" . $index];
        array_push($whereAttr->combine, $whereAttr_data);

        $condition['subcondition_' . $index] = array(
            'table' => "machine_on_off_hist",
            'fields' => ['upload_at'],
            'intervaltime' => "",
            'limit' => [0,1],
            'orderby' => ['desc', 'upload_at'],
            'symbols' => array(
                'device_id' => ["equal"]
            ),
            'where' => array(
                'device_id' => [$device_id]
            ),
            'union' => "",
            'subquery' => ""
        );
        $index++;
    }
    $machine_on_off_hist = json_decode($result, true);

    $data = array(
        'condition_1' => array(
            'table' => "machine_on_off_hist",
            'fields' => "",
            'intervaltime' => "",
            'limit' => ["ALL"],
            'orderby' => ['desc', 'upload_at'],
            'symbols' => $symbols,
            'where' => $whereAttr,
            'union' => "",
            'subquery' => $condition
        )
    );
    $url = "https://localhost:3687/api/CHUNZU/2.5/myps/Sensor/SqlSyntax?uid=@sapido@PaaS&dbName=site2&getSqlSyntax=yes";
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => json_encode($data),
            'header' => "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n"
        ),
        //忽略認證
       "ssl"=>array(
           "verify_peer"=>false,
           "verify_peer_name"=>false,
       ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $machine_on_off_hist = json_decode($result, true);
    if ($machine_on_off_hist['Response'] != 'ok' || count($machine_on_off_hist['QueryTableData']) == 0) {//先抓是否抓取成功，成功的話繼續執行
        $machine_on_off_hist_data = [];
    } else {
        $machine_on_off_hist_data = $machine_on_off_hist['QueryTableData'];
    }

    $now_time = strtotime(date("Y-m-d H:i:s"));
    $push_data = [];
    if ($index - 2 == count($machine_on_off_hist_data)) {
        foreach ($machine_on_off_hist_data as $key => $value) {
            if ($value['status'] == 'S') {
                if ($now_time - 600 > strtotime($device_obj[$value['device_id']]['machine_back_time'])) {
                    array_push($push_data, array(
                        'upload_at' => $device_obj[$value['device_id']]['machine_back_time'],
                        'device_id' => $value['device_id'],
                        'comp_id' => 'OFCO',
                        'site_id' => 1,
                        'status' => 'E'
                    ));
                }
            } else if ($value['status'] == 'E') {
                if ($now_time - 600 < strtotime($device_obj[$value['device_id']]['machine_back_time'])) {
                    array_push($push_data, array(
                        'upload_at' => $device_obj[$value['device_id']]['machine_back_time'],
                        'device_id' => $value['device_id'],
                        'comp_id' => 'OFCO',
                        'site_id' => 1,
                        'status' => 'S'
                    ));
                    
                }
            }
        }
    } else {
        foreach ($device_obj as $device_id => $device_value) {
            $device_id = strtolower($device_id);
            $no_match = 0;
            foreach ($machine_on_off_hist_data as $key => $value) {
                if ($device_id == $value['device_id']) {
                    if ($value['status'] == 'S') {
                        if ($now_time - 600 > strtotime($device_value['machine_back_time'])) {
                            array_push($push_data, array(
                                'upload_at' => $device_value['machine_back_time'],
                                'device_id' => $value['device_id'],
                                'comp_id' => 'OFCO',
                                'site_id' => 1,
                                'status' => 'E'
                            ));
                        }
                    } else if ($value['status'] == 'E') {
                        if ($now_time - 600 < strtotime($device_value['machine_back_time'])) {
                            array_push($push_data, array(
                                'upload_at' => $device_value['machine_back_time'],
                                'device_id' => $value['device_id'],
                                'comp_id' => 'OFCO',
                                'site_id' => 1,
                                'status' => 'S'
                            ));
                        }
                    }
                } else {
                    $no_match++;
                }
            }
            if ($no_match == count($machine_on_off_hist_data)) {
                if ($now_time - 600 > strtotime($device_value['machine_back_time'])) {
                    array_push($push_data, array(
                        'upload_at' => $device_value['machine_back_time'],
                        'device_id' => $device_id,
                        'comp_id' => 'OFCO',
                        'site_id' => 1,
                        'status' => 'E'
                    ));
                } else {
                    array_push($push_data, array(
                        'upload_at' => $device_value['machine_back_time'],
                        'device_id' => $device_id,
                        'comp_id' => 'OFCO',
                        'site_id' => 1,
                        'status' => 'S'
                    ));
                }
            }
        }
    }
    if (empty($push_data)) {
        return null;
    } else {
        return $push_data;
    }
}


//新增
function Push_Message($push_data) {
    $url = "https://localhost:3687/api/CHUNZU/2.0/myps/Sensor/Rows/machine_on_off_hist?uid=@sapido@PaaS";
    $data = $push_data;
    
    $options = array(
        'http' => array(
            'method' => 'POST',
            'content' => json_encode($data),
            'header' => "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n"
        ),
        //忽略認證
       "ssl"=>array(
           "verify_peer"=>false,
           "verify_peer_name"=>false,
       ),
    );
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    // $Arr = json_decode($result, true);
    // echo json_encode($Arr);
}
?>