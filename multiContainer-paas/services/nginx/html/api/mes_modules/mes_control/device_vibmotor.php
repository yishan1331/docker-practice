<?php
date_default_timezone_set("Asia/Taipei");

$this_device = 'mes_device_status_' . $cus_id . '_' . $device_id;
$this_device_data = $device_data[$this_device];

$last_data = $data[count($data) - 1];

//振動主馬達資料
$update_data[$this_device]['machine_vibMotor'] = $last_data;

// echo json_encode($update_data);
// echo json_encode(CommonUpdate($update_data, 'Redis', null));
CommonUpdate($update_data, 'Redis', null);
?>