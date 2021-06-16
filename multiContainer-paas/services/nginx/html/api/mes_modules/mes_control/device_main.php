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
$machine_abn_status_data = array(
    "0" => array(),
    "1" => array(),
    "2" => array()
);
if (!empty($last_data['error_code'])) {
    if (!empty($this_device_data['machine_abn_list'])) {
        if (is_string($this_device_data['machine_abn_list'])) {
            $this_device_data['machine_abn_list'] = json_decode($this_device_data['machine_abn_list'], true);
        }

        $machine_abn_list = $this_device_data['machine_abn_list'];

        foreach ($last_data['error_code'] as $value) {
            if (isset($machine_abn_list[$value])) {
                array_push($machine_abn_id, $value);
                array_push($machine_abn_description, $machine_abn_list[$value]['name']);
                array_push($machine_abn_solution, $machine_abn_list[$value]['solution']);
            }
            if (is_numeric(strpos($value, '2_'))) {
                array_push($machine_abn_status_data[2], $machine_abn_list[$value]['name']);
            } else if (is_numeric(strpos($value, '1_'))) {
                array_push($machine_abn_status_data[1], $machine_abn_list[$value]['name']);
            } else if (is_numeric(strpos($value, '0_'))) {
                array_push($machine_abn_status_data[0], $machine_abn_list[$value]['name']);
            }
        }
    }
    $this_machine_status = 'H';//異常
}
$update_data[$this_device]['machine_abn_id'] = $machine_abn_id;//異常代碼
$update_data[$this_device]['machine_abn_description'] = $machine_abn_description;//異常說明
$update_data[$this_device]['machine_abn_solution'] = $machine_abn_solution;//異常解決方式
$update_data[$this_device]['machine_status'] = $this_machine_status;//機台狀態

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

if (!empty($this_device_data['machine_light_error_list'])) {
    if (is_string($this_device_data['machine_light_error_list'])) {
        $this_device_data['machine_light_error_list'] = json_decode($this_device_data['machine_light_error_list'], true);
    }

    $machine_light_error_list = $this_device_data['machine_light_error_list'];

    $machine_detail = $last_data;

    $diagnosis_message = array();
    foreach ($machine_light_error_list as $group_list) {
        $check_ok = false;
        $this_light_list_array = array();
        
        foreach ($group_list['light_list'] as $light_code => $light_value) {
            if (!isset($machine_detail[$light_code])) {
                break;
            }
            if ($machine_detail[$light_code] != $light_value) {
                break;
            } else {
                array_push($this_light_list_array, $light_code);
            }
            if (count($this_light_list_array) == count($group_list)) {
                $check_ok = true;
            }
        }
        if ($check_ok) {
            foreach ($this_light_list_array as $light_code) {
                unset($machine_detail[$light_code]);
            }
            array_push($diagnosis_message, array(
                'name' => $group_list['name'],
                'solution' => $group_list['solution']
            ));
        }
    }

    $update_data[$this_device]['diagnosis_message'] = $diagnosis_message;
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
            if (!empty($machine_abn_id)) {
                $mail_data = array(
                    "emailTitle" => $this_device_data['device_name'] . "異常警報通知",
                    "emailTime" => $machine_detail_time
                );
            }
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
                    } else if (is_string($last_alarmCode)) {
                        $last_alarmCode = explode("\n", $last_alarmCode);
                    }

                    if (count(array_keys(array_diff_assoc($machine_abn_id, $last_alarmCode))) == 0 && count(array_keys(array_diff_assoc($last_alarmCode, $machine_abn_id))) == 0) {
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
                        if (!empty($machine_abn_id)) {
                            $mail_data = array(
                                "emailTitle" => $this_device_data['device_name'] . "異常警報通知",
                                "emailTime" => $machine_detail_time
                            );
                        }
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
                    if (!empty($machine_abn_id)) {
                        $mail_data = array(
                            "emailTitle" => $this_device_data['device_name'] . "異常警報通知",
                            "emailTime" => $machine_detail_time
                        );
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
        if (!empty($machine_abn_id)) {
            $mail_data = array(
                "emailTitle" => $this_device_data['device_name'] . "異常警報通知",
                "emailTime" => $machine_detail_time
            );
        }
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

//機連網
if (isset($mail_data)) {
    $query_customer = new apiJsonBody_query;
    $query_customer->setFields(['mail']);
    $query_customer->setTable('customer');
    $query_customer->addSymbols('id', 'equal');
    $query_customer->addWhere('id', $cus_id);
    $query_customer->addSymbols('delete_enable', 'equal');
    $query_customer->addWhere('delete_enable', 'N');
    $query_customer_data = $query_customer->getApiJsonBody();
    $customer = CommonSqlSyntax_Query($query_customer_data, "MySQL");
    if ($customer['Response'] !== 'ok') {
        $customer_data = [];
    } else {
        $customer_data = $customer['QueryTableData'];
    }

    $mail_level = array();    
    foreach ($customer_data as $key => $value) {
        if (empty($value['mail'])) {
            continue;
        }
        if (is_string($value['mail'])) {
            $value['mail'] = json_decode($value['mail'], true);
        }
        foreach ($value['mail'] as $mail => $level_array) {
            sort($level_array);
            
            $new_level_array = array();
            foreach ($level_array as $level) {
                if (isset($machine_abn_status_data[$level])) {
                    if (!empty($machine_abn_status_data[$level])) {
                        array_push($new_level_array, $level);
                    }
                }
            }

            $new_level_array_encode = json_encode($new_level_array);
            if (!isset($mail_level[$new_level_array_encode])) {
                $mail_level[$new_level_array_encode] = array();
            }
            array_push($mail_level[$new_level_array_encode], $mail);
        }
    }

    $send_mail_data = array();
    foreach ($mail_level as $level_array_encode => $mail_array) {
        $level_array = json_decode($level_array_encode,true);

        $emailContent_array = array();
        foreach ($level_array as $level) {
            if (isset($machine_abn_status_data[$level])) {
                $emailContent_array = array_merge($emailContent_array, $machine_abn_status_data[$level]);
            }
        }

        array_push($send_mail_data, array(
            'emailTitle' => $mail_data['emailTitle'],
            'emailContent' => $this_device_data['device_name'] . "<br>異常:" . implode(",",$emailContent_array) . "<br>時間:" . $mail_data['emailTime'],
            'emailAddress' => $mail_array
        ));
    }

    foreach ($send_mail_data as $key => $value) {
        SendEmails($value);
    }
}

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