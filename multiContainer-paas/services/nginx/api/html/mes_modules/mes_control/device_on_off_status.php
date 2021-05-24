<?php
//機台狀態、燈號、支數、運轉日誌、堆疊圖
date_default_timezone_set("Asia/Taipei");

include(dirname(__FILE__) . "/../api.php");
include(dirname(__FILE__) . "/../connection.php");
include(dirname(__FILE__) . "/../apiJsonBody.php");

$device_off_array = array();
foreach ($data as $key => $value) {
    if ($value['status'] == 'E') {
        $device_off_array[$value['device_id']] = $value;
    }
}
$device_id_array = array_column($device_off_array, 'device_id');

$query_preload = new apiJsonBody_query;
$query_preload->setTable('preload');
$query_preload->addFields('combine_key');
$query_preload->addSymbols('main_key', 'equal');
$query_preload->addWhere('main_key', 'mes_device_status');
$query_preload->addSymbols('combine_key', 'in');
$query_preload->addWhere('combine_key', $device_id_array);
$query_preload->setLimit(['ALL']);
$query_preload_data = $query_preload->getApiJsonBody();
$preload = CommonSqlSyntax_Query($query_preload_data, "MySQL");
if ($preload['Response'] !== 'ok') {
    return;
}
$preload_data = $preload['QueryTableData'];

$device_combine_key = array();
foreach ($preload_data as $key => $value) {
    $combine_key_array = explode('_', $value['combine_key']);
    if (count($combine_key_array) == 2) {
        if (in_array($combine_key_array[1], $device_id_array)) {
            array_push($device_combine_key, $value['combine_key']);
        }
    }
}

$update_data = array();
//燈號未清空
foreach ($device_combine_key as $index => $combine_key) {
    $device = CommonSpecificKeyQuery('Redis', $combine_key, 'no');
    if ($device['Response'] !== 'ok') {
        return;
    }
    $device_data = $device['QueryValueData'];

    $this_device = 'mes_device_status_' . $combine_key;
    $this_device_data = $device_data;
    $machine_light_value = array();
    if (!empty($this_device_data['machine_light_list'])) {
        if (is_string($this_device_data['machine_light_list'])) {
            $this_device_data['machine_light_list'] = json_decode($this_device_data['machine_light_list'], true);
        }    
    
        $machine_light_list = $this_device_data['machine_light_list'];
    
        foreach ($machine_light_list as $key => $value) {
            // if (isset($value[0])) {
            //     $machine_light_value[$key] = array(
            //         'color' => $value[0],
            //         'name' => $value['name'],
            //     );
            // }
            $machine_light_value[$key] = array(
                'color' => 'gray',
                'name' => $value['name'],
            );
        }
    }

    if (!empty($this_device_data['operatelog_information'])) {
        if (is_string($this_device_data['operatelog_information'])) {
            $this_device_data['operatelog_information'] = json_decode($this_device_data['operatelog_information'], true);
        }
        if (is_string($this_device_data['stack_bar_information'])) {
            $this_device_data['stack_bar_information'] = json_decode($this_device_data['stack_bar_information'], true);
        }
        $operatelog_information = $this_device_data['operatelog_information'];
        $stack_bar_information = $this_device_data['stack_bar_information'];

        $machine_off_time = date("Y-m-d H:i:s", strtotime($device_off_array[explode('_', $combine_key)[1]]['upload_at']));
        $durationTime = TimeSubtraction($machine_off_time, $machine_off_time, 'hour');
        array_push($operatelog_information, array(
            'status' => '關機',
            'alarmCode' => '',
            'alarmDetail' => '',
            'startTime' =>  $machine_off_time,
            'endTime' => $machine_off_time,
            'durationTime' => $durationTime[0]
        ));
        array_push($stack_bar_information, array(
            'status' => '關機',
            'alarmDetail' => '',
            'startTime' =>  $machine_off_time,
            'endTime' => $machine_off_time,
            'duration_number' => $durationTime[2]
        ));
    }

    if (empty($machine_light_value)) {
        $machine_light_value = '';
    }
    if (empty($operatelog_information)) {
        $operatelog_information = '';
    }
    if (empty($stack_bar_information)) {
        $stack_bar_information = '';
    }
    $update_data[$this_device] = array(
        'machine_main' => '',
        'machine_status' => 'S',
        'device_rpm' => 0,
        'device_now_count' => 0,
        'machine_abn_id' => '',
        'machine_abn_description' => '',
        'machine_abn_solution' => '',
        'machine_light_value' => $machine_light_value,
        'operatelog_information' => $operatelog_information,
        'stack_bar_information' => $stack_bar_information,
        'machine_emeter' => '',
        'machine_servod' => '',
        'wire_weight' => 0,
        'machine_loadCell' => '',
        'machine_vibbearing' => '',
        'machine_vibMotor' => '',
        'machine_smb' => '',
    );
}

if (!empty($update_data)) {
    // echo json_encode($update_data);
    CommonUpdate($update_data, 'Redis', null);
}

function TimeSubtraction($startTime, $endTime, $type)
{
    if (strtotime($endTime) > strtotime($startTime)) {
        $time = strtotime($endTime)-strtotime($startTime);
        $returnData = [];
        if ($type == 'hour') {
            $date = floor($time / 86400);
            $hour = floor($time % 86400 / 3600);
            $minute = floor($time % 86400 / 60) - $hour * 60;
            $second = floor($time % 86400 % 60);

            $hour = $hour + $date * 24; // 相加小時數
            
            $hour < 10 ? $showHour = '0' . $hour : $showHour = $hour;
            $minute < 10 ? $showMinute = '0' . $minute : $showMinute = $minute;
            $second < 10 ? $showSecond = '0' . $second : $showSecond = $second;
            $returnData[0] = $showHour . ":" . $showMinute . ":" . $showSecond;
    
            $returnData[1] = "";
            if ($hour > 0) {
                $returnData[1] .= $hour . "小時";
            }
            if ($minute > 0) {
                $returnData[1] .= $minute . "分鐘";
            }
            if ($second > 0) {
                $returnData[1] .= $second . "秒";
            }
            $returnData[2] = $time;
            return $returnData;
        } else if ($type == 'date') {
            $date = floor($time / 86400);
            $hour = floor($time % 86400 / 3600);
            $minute = floor($time % 86400 / 60) - $hour * 60;
            $second = floor($time % 86400 % 60);
    
            $date < 10 ? $showDate = '0' . $date : $showDate = $date;
            $hour < 10 ? $showHour = '0' . $hour : $showHour = $hour;
            $minute < 10 ? $showMinute = '0' . $minute : $showMinute = $minute;
            $second < 10 ? $showSecond = '0' . $second : $showSecond = $second;
            $returnData[0] = $showDate . " " . $showHour . ":" . $showMinute . ":" . $showSecond;
            
            $returnData[1] = "";
            if ($date > 0) {
                $returnData[1] .= $date . "天";
            }
            if ($hour > 0) {
                $returnData[1] .= $hour . "小時";
            }
            if ($minute > 0) {
                $returnData[1] .= $minute . "分鐘";
            }
            if ($second > 0) {
                $returnData[1] .= $second . "秒";
            }
            $returnData[2] = $time;
            return $returnData;
        }
    } else {
        $returnData = ['$startTime > $endTime'];
    }
}
?>