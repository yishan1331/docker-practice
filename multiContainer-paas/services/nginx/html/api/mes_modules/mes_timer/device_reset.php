<?php
//機台狀態、運轉日誌、堆疊圖、機台運轉時間、機台稼動率、機台轉速、機台日產支數、機台日初始支數、機台目前支數、機台燈號值、目前機台異常碼、目前機台異常說明、目前機台異常解決方式、主機台資料、智慧電表資料、振動連座軸承資料、振動主馬達資料、伺服驅動器紀錄、線材重量、荷重元紀錄、SMB紀錄、用電量
date_default_timezone_set("Asia/Taipei");

include(dirname(__FILE__) . "/../api.php");
include(dirname(__FILE__) . "/../apiJsonBody.php");
include(dirname(__FILE__) . "/../connection.php");

//查詢所有機台
$device = CommonSpecificKeyQuery('Redis', '*', 'yes');
if ($device['Response'] !== 'ok') {
    return;
}
$device_data = $device['QueryValueData'];

$update_data = array();
foreach ($device_data as $key => $value) {
    $update_data[$key] = array(
        'machine_main' => "",
        'machine_status' => "",
        'device_rpm' => 0,
        'device_now_count' => 0,
        'device_day_count' => 0,
        'device_day_start_count' => 0,
        'operatelog_information' => "",
        'stack_bar_information' => "",
        'device_run_time' => "",
        'device_activation' => 0,
        'machine_light_value' => "",
        'machine_abn_id' => "",
        'machine_abn_description' => "",
        'machine_abn_solution' => "",
        'machine_emeter' => "",
        'machine_vibbearing' => "",
        'machine_vibMotor' => "",
        'machine_servod' => "",
        'wire_weight' => 0,
        'machine_loadCell' => "",
        'machine_smb' => "",
        'power_expend' => ""
    );
}

// echo json_encode($update_data);
CommonUpdate($update_data, 'Redis', null);
?>