<?php
date_default_timezone_set("Asia/Taipei");

$this_device = 'mes_device_status_' . $cus_id . '_' . $device_id;
$this_device_data = $device_data[$this_device];

$last_data = $data[count($data) - 1];

//智慧電表資料
$update_data[$this_device]['machine_emeter'] = $last_data;

// echo json_encode($update_data);
// echo json_encode(CommonUpdate($update_data, 'Redis', null));
CommonUpdate($update_data, 'Redis', null);

// $device_name = ($table);
// $this_device = 'mes_device_status_test_emeter';
// $this_device_data = $device_data[$this_device];

// $last_data = $data[count($data) - 1];

// //當前電流值
// $update_data[$this_device]['current'] = $last_data['current'];
// //當前電壓值
// $update_data[$this_device]['voltage'] = $last_data['voltage'];
// //電壓不平衡率
// $update_data[$this_device]['volt_vubr'] = $last_data['volt_vubr'];

// $this_time = $get_data['upload_at'];
// $this_hour = date("H", strtotime($get_data['upload_at']));

// if (!empty($this_device_data['RELEC'])) {
//     if (is_string($this_device_data['RELEC'])) {
//         $this_device_data['RELEC'] = json_decode($this_device_data['RELEC'], true);
//     }
//     if (strtotime($this_device_data['RELEC'][0]['org_time']) < strtotime(date("Y-m-d 08:00:00")) && strtotime(date("Y-m-d H:i:s")) > strtotime(date("Y-m-d 08:00:00"))) {
//         $this_device_data['RELEC'][0]['org_time'] = array();
//     }
// } else {
//     $this_device_data['RELEC'] = array();
// }

// //實功電能
// if (is_array($this_device_data['RELEC'])) {
//     $nodata = true;
//     foreach ($this_device_data['RELEC'] as $key => $value) {
//         if ($value['time'] == $this_hour) {
//             $nodata = false;
//         }
//     }
//     if ($nodata) {
//         array_push($this_device_data['RELEC'], array(
//             "org_time" => $this_time,
//             "time" => $this_hour,
//             "value" => $last_data['RELEC']
//         ));
//     }

//     $update_data[$this_device]['RELEC'] = $this_device_data['RELEC'];
// } else {
//     $update_data[$this_device]['RELEC'] = array(
//         array(
//             "org_time" => $this_time,
//             "time" => $this_hour,
//             "value" => $last_data['RELEC']
//         )
//     );
// }

// echo json_encode($update_data);
// // echo json_encode(CommonUpdate($update_data, 'Redis', null));
// // CommonUpdate($update_data, 'Redis', null);






?>