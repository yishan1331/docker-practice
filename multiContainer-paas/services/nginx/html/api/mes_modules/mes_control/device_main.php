<?php
//
date_default_timezone_set("Asia/Taipei");

$this_device = 'mes_device_status_' . $cus_id . '_' . $device_id;
$this_device_data = $device_data[$this_device];

$last_data = $data[count($data) - 1];

//主機台資料
$update_data[$this_device]['machine_main'] = $last_data;
$update_data[$this_device]['machine_main']['upload_at'] = $get_data['upload_at'];

$this_machine_status = $this_device_data['machine_status'];
//機台狀態
if ($last_data['opr'] == 1) {
    $this_machine_status = 'R';//運轉
} else if ($last_data['opr'] == 0) {
    $this_machine_status = 'Q';//閒置
} else if ($last_data['opr'] == -1) {
    $this_machine_status = 'H';//異常
} else {
    $this_machine_status = '';
}
$update_data[$this_device]['machine_status'] = $this_machine_status;

//機台轉速
$update_data[$this_device]['device_rpm'] = $last_data['mcg_revsp'];

//機台目前支數
$update_data[$this_device]['device_now_count'] = $last_data['cnt1'];

//機台日產支數
if ($last_data['cnt1'] > floatval($this_device_data['device_now_count'])) {
    $update_data[$this_device]['device_day_count'] = floatval($this_device_data['device_day_count']) + ($last_data['cnt1'] - floatval($this_device_data['device_now_count']));
} else if ($last_data['cnt1'] < floatval($this_device_data['device_now_count'])) {
    $update_data[$this_device]['device_day_count'] = floatval($this_device_data['device_day_count']) + $last_data['cnt1'];
}

//機台日初始支數
if ($this_device_data['device_day_start_count'] == '') {
    $update_data[$this_device]['device_day_start_count'] = $last_data['cnt1'];
}

$machine_abn_id = array();
$machine_abn_description = array();
$machine_abn_solution = array();
if (!empty($last_data['error_code'])) {
    if (!empty($this_device_data['machine_abn_list'])) {
        if (is_string($this_device_data['machine_abn_list'])) {
            $this_device_data['machine_abn_list'] = json_decode($this_device_data['machine_abn_list'], true);
        }
    
        $machine_abn_list = $this_device_data['machine_abn_list'];

        foreach ($last_data['error_code'] as $value) {
            if (empty(strpos($value, '0_'))) {
                if (isset($machine_abn_list[$value])) {
                    array_push($machine_abn_id, $value);
                    array_push($machine_abn_description, $machine_abn_list[$value]['name']);
                    array_push($machine_abn_solution, $machine_abn_list[$value]['solution']);
                }
            }
        }
    }
}
$update_data[$this_device]['machine_abn_id'] = $machine_abn_id;
$update_data[$this_device]['machine_abn_description'] = $machine_abn_description;
$update_data[$this_device]['machine_abn_solution'] = $machine_abn_solution;

if (!empty($this_device_data['machine_light_list'])) {
    if (is_string($this_device_data['machine_light_list'])) {
        $this_device_data['machine_light_list'] = json_decode($this_device_data['machine_light_list'], true);
    }    

    $machine_light_list = $this_device_data['machine_light_list'];

    $machine_light_value = array();
    foreach ($machine_light_list as $key => $value) {
        if (isset($last_data[$key])) {
            if (isset($value[$last_data[$key]])) {
                $machine_light_value[$key] = array(
                    'color' => $value[$last_data[$key]],
                    'name' => $value['name'],
                );
            }
        }
    }
    $update_data[$this_device]['machine_light_value'] = $machine_light_value;
}

$status_chinese_name = array(
    'S' => '關機',
    'H' => '警報',
    'R' => '生產',
    'Q' => '待機',
);
$device_name = $cus_id . '_' . $device_id;

$this_upload_at = $update_data[$this_device]['machine_main']['upload_at'];
$operatelog_information = array();
$stack_bar_information = array();
$device_activation = '0';
$device_run_time = 0;

if (!empty($this_device_data['operatelog_information'])) {
    if (is_string($this_device_data['operatelog_information'])) {
        $this_device_data['operatelog_information'] = json_decode($this_device_data['operatelog_information'], true);
    }
    if (is_string($this_device_data['stack_bar_information'])) {
        $this_device_data['stack_bar_information'] = json_decode($this_device_data['stack_bar_information'], true);
    }

    $operatelog_information = $this_device_data['operatelog_information'];
    $stack_bar_information = $this_device_data['stack_bar_information'];

    if (isset($this_upload_at)) {
        if (strtotime($operatelog_information[0]['startTime']) < strtotime(date("Y-m-d 08:00:00")) && strtotime(date("Y-m-d H:i:s")) > strtotime(date("Y-m-d 08:00:00"))) {
            $operatelog_information = array();
            $stack_bar_information = array();
            $update_data[$this_device]['device_day_count'] = 0;//重置當日總之數
            $machine_detail_time = date("Y-m-d H:i:s", strtotime($this_upload_at));
            $standard_time = date("Y-m-d 08:00:00");
            if (strtotime($standard_time) > strtotime($machine_detail_time)) {
                $previous_time = date("Y-m-d H:i:s", strtotime($standard_time)-86400);
            } else {
                $previous_time = $standard_time;
            }

            $this_status_chinese_name = isset($status_chinese_name[$this_machine_status]) ? $status_chinese_name[$this_machine_status] : '';
            $durationTime = TimeSubtraction($standard_time, $machine_detail_time, 'hour');
            array_push($operatelog_information, array(
                'status' => '-',
                'alarmCode' => '',
                'alarmDetail' => '',
                'startTime' =>  $previous_time,
                'endTime' => $machine_detail_time,
                'durationTime' => $durationTime[0]
            ));
            array_push($stack_bar_information, array(
                'status' => null,
                'alarmDetail' => '',
                'startTime' =>  $standard_time,
                'endTime' => $machine_detail_time,
                'duration_number' => $durationTime[2]
            ));
            $durationTime = TimeSubtraction($machine_detail_time, $machine_detail_time, 'hour');
            array_push($operatelog_information, array(
                'status' => $this_status_chinese_name,
                'alarmCode' => !empty($machine_abn_id)?implode("\n",$machine_abn_id):'',
                'alarmDetail' => !empty($machine_abn_description)?implode("\n",$machine_abn_description):'',
                'alarmVideo' => !empty($machine_abn_id)?$device_name.'_'.str_replace(array(' ','-',':'),array('','',''),$machine_detail_time).'.avi':'',
                'startTime' =>  $machine_detail_time,
                'endTime' => $machine_detail_time,
                'durationTime' => $durationTime[0]
            ));
            array_push($stack_bar_information, array(
                'status' => $this_status_chinese_name,
                'alarmDetail' => !empty($machine_abn_description)?implode("\n",$machine_abn_description):'',
                'startTime' =>  $machine_detail_time,
                'endTime' => $machine_detail_time,
                'duration_number' => $durationTime[2]
            ));
        } else {
            $machine_detail_time = date("Y-m-d H:i:s", strtotime($this_upload_at));
            
            $this_status_chinese_name = isset($status_chinese_name[$this_machine_status]) ? $status_chinese_name[$this_machine_status] : '';

            $last_status = $operatelog_information[count($operatelog_information) - 1]['status'];

            if (strtotime($operatelog_information[count($operatelog_information) - 1]['endTime']) <= strtotime($machine_detail_time)) {
                if ($this_status_chinese_name == $last_status) {
                    $durationTime = TimeSubtraction($operatelog_information[count($operatelog_information) - 1]['startTime'], $machine_detail_time, 'hour');
                    $last_alarmCode = $operatelog_information[count($operatelog_information) - 1]['alarmCode'];
                    if (empty($last_alarmCode)) {
                        $last_alarmCode = array();
                    }
                    if (count(array_keys(array_diff_assoc($machine_abn_id, $last_alarmCode))) == 0) {
                        $operatelog_information[count($operatelog_information) - 1]['endTime'] = $machine_detail_time;
                        $operatelog_information[count($operatelog_information) - 1]['durationTime'] = $durationTime[0];
                        $stack_bar_information[count($stack_bar_information) - 1]['endTime'] = $machine_detail_time;
                        $stack_bar_information[count($stack_bar_information) - 1]['duration_number'] = $durationTime[2];
                    } else {
                        array_push($operatelog_information, array(
                            'status' => $this_status_chinese_name,
                            'alarmCode' => !empty($machine_abn_id)?implode("\n",$machine_abn_id):'',
                            'alarmDetail' => !empty($machine_abn_description)?implode("\n",$machine_abn_description):'',
                            'alarmVideo' => !empty($machine_abn_id)?$device_name.'_'.str_replace(array(' ','-',':'),array('','',''),$machine_detail_time).'.avi':'',
                            'startTime' =>  $operatelog_information[count($operatelog_information) - 1]['endTime'],
                            'endTime' => $machine_detail_time,
                            'durationTime' => $durationTime[0]
                        ));
                        array_push($stack_bar_information, array(
                            'status' => $this_status_chinese_name,
                            'alarmDetail' => !empty($machine_abn_description)?implode("\n",$machine_abn_description):'',
                            'startTime' =>  $stack_bar_information[count($stack_bar_information) - 1]['endTime'],
                            'endTime' => $machine_detail_time,
                            'duration_number' => $durationTime[2]
                        ));
                    }
                } else {
                    if ($last_status == '關機') {
                        $durationTime = TimeSubtraction($operatelog_information[count($operatelog_information) - 1]['startTime'], $machine_detail_time, 'hour');
                        $operatelog_information[count($operatelog_information) - 1]['endTime'] = $machine_detail_time;
                        $operatelog_information[count($operatelog_information) - 1]['durationTime'] = $durationTime[0];
                        $stack_bar_information[count($stack_bar_information) - 1]['endTime'] = $machine_detail_time;
                        $stack_bar_information[count($stack_bar_information) - 1]['duration_number'] = $durationTime[2];
                    }
                    $durationTime = TimeSubtraction($operatelog_information[count($operatelog_information) - 1]['endTime'], $machine_detail_time, 'hour');
                    array_push($operatelog_information, array(
                        'status' => $this_status_chinese_name,
                        'alarmCode' => !empty($machine_abn_id)?implode("\n",$machine_abn_id):'',
                        'alarmDetail' => !empty($machine_abn_description)?implode("\n",$machine_abn_description):'',
                        'alarmVideo' => !empty($machine_abn_id)?$device_name.'_'.str_replace(array(' ','-',':'),array('','',''),$machine_detail_time).'.avi':'',
                        'startTime' =>  $operatelog_information[count($operatelog_information) - 1]['endTime'],
                        'endTime' => $machine_detail_time,
                        'durationTime' => $durationTime[0]
                    ));
                    array_push($stack_bar_information, array(
                        'status' => $this_status_chinese_name,
                        'alarmDetail' => !empty($machine_abn_description)?implode("\n",$machine_abn_description):'',
                        'startTime' =>  $stack_bar_information[count($stack_bar_information) - 1]['endTime'],
                        'endTime' => $machine_detail_time,
                        'duration_number' => $durationTime[2]
                    ));
                    if ($this_machine_status = 'H') {
                        $durationTime = TimeSubtraction($operatelog_information[count($operatelog_information) - 1]['startTime'], $machine_detail_time, 'hour');
                        $operatelog_information[count($operatelog_information) - 1]['endTime'] = $machine_detail_time;
                        $operatelog_information[count($operatelog_information) - 1]['durationTime'] = $durationTime[0];
                        $stack_bar_information[count($stack_bar_information) - 1]['endTime'] = $machine_detail_time;
                        $stack_bar_information[count($stack_bar_information) - 1]['duration_number'] = $durationTime[2];
                    }
                }
            }
        }
    }
    $run_time_total = 0;
    foreach ($operatelog_information as $key => $value) {
        if ($value['status'] == '生產') {
            $run_time_total += (strtotime($value['endTime']) - strtotime($value['startTime']));
        }
    }
    $device_activation = round(round($run_time_total / 86400, 2) * 100);
    $device_run_time = change_time($run_time_total, 'h:m');
} else {
    if (isset($this_upload_at)) {
        $machine_detail_time = date("Y-m-d H:i:s", strtotime($this_upload_at));
        $standard_time = date("Y-m-d 08:00:00");
        if (strtotime($standard_time) > strtotime($machine_detail_time)) {
            $previous_time = date("Y-m-d H:i:s", strtotime($standard_time)-86400);
        } else {
            $previous_time = $standard_time;
        }

        $this_status_chinese_name = isset($status_chinese_name[$this_machine_status]) ? $status_chinese_name[$this_machine_status] : '';
        $durationTime = TimeSubtraction($previous_time, $machine_detail_time, 'hour');
        array_push($operatelog_information, array(
            'status' => '-',
            'alarmCode' => '',
            'alarmDetail' => '',
            'startTime' =>  $previous_time,
            'endTime' => $machine_detail_time,
            'durationTime' => $durationTime[0]
        ));
        array_push($stack_bar_information, array(
            'status' => null,
            'alarmDetail' => '',
            'startTime' =>  $previous_time,
            'endTime' => $machine_detail_time,
            'duration_number' => $durationTime[2]
        ));
        $durationTime = TimeSubtraction($machine_detail_time, $machine_detail_time, 'hour');
        array_push($operatelog_information, array(
            'status' => $this_status_chinese_name,
            'alarmCode' => !empty($machine_abn_id)?implode("\n",$machine_abn_id):'',
            'alarmDetail' => !empty($machine_abn_description)?implode("\n",$machine_abn_description):'',
            'alarmVideo' => !empty($machine_abn_id)?$device_name.'_'.str_replace(array(' ','-',':'),array('','',''),$machine_detail_time).'.avi':'',
            'startTime' =>  $machine_detail_time,
            'endTime' => $machine_detail_time,
            'durationTime' => $durationTime[0]
        ));
        array_push($stack_bar_information, array(
            'status' => $this_status_chinese_name,
            'alarmDetail' => !empty($machine_abn_description)?implode("\n",$machine_abn_description):'',
            'startTime' =>  $machine_detail_time,
            'endTime' => $machine_detail_time,
            'duration_number' => $durationTime[2]
        ));
    }
}

if ($operatelog_information[0]['startTime'] == date("Y-m-d 08:00:00") && $operatelog_information[0]['endTime'] == date("Y-m-d 08:00:00") && $operatelog_information[0]['status'] == '-') {
    unset($operatelog_information[0]);
    $operatelog_information = array_values($operatelog_information);
}
if ($stack_bar_information[0]['startTime'] == date("Y-m-d 08:00:00") && $stack_bar_information[0]['endTime'] == date("Y-m-d 08:00:00") && $stack_bar_information[0]['status'] == null) {
    unset($stack_bar_information[0]);
    $stack_bar_information = array_values($stack_bar_information);
}

$update_data[$this_device]['operatelog_information'] = $operatelog_information;
$update_data[$this_device]['stack_bar_information'] = $stack_bar_information;
$update_data[$this_device]['device_activation'] = $device_activation;
$update_data[$this_device]['device_run_time'] = $device_run_time;

// echo json_encode($update_data);
// echo json_encode(CommonUpdate($update_data, 'Redis', null));
CommonUpdate($update_data, 'Redis', null);

//轉換時間
function change_time($time, $format) {
    $date = floor($time / 86400);
    $hour = floor($time % 86400 / 3600) + $date * 24;
    $minute = floor($time % 86400 / 60) - $hour * 60;
    $second = floor($time % 86400 % 60);

    $date < 10 ? $showDate = '0' . $date : $showDate = $date;
    $hour < 10 ? $showHour = '0' . $hour : $showHour = $hour;
    $minute < 10 ? $showMinute = '0' . $minute : $showMinute = $minute;
    $second < 10 ? $showSecond = '0' . $second : $showSecond = $second;
    if ($format == 'h:m') {
        return $hour.'時'.$minute.'分';
    } else if ($format == 'h:m:s') {
        return $hour.'時'.$minute.'分'.$second.'秒';
    }
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