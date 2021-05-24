<?php
ini_set('memory_limit', '1024M');
set_time_limit(180);
date_default_timezone_set("Asia/Taipei");

include(dirname(__FILE__) . "/../api.php");
include(dirname(__FILE__) . "/../apiJsonBody.php");
include(dirname(__FILE__) . "/../connection.php");

$Query_Device_Response = Query_Device();
// echo json_encode($Query_Device_Response);
if(!empty($Query_Device_Response)) {
    Push_Data($Query_Device_Response);
}
// update machine_status_sum set upload_at = '' where upload_at = '';
function Query_Device() {
    $now_time = date("Y-m-d 08:00:00");
    // $now_time = date("Y-m-d 08:00:00", strtotime("-1 day"));
    // $now_time = date("Y-m-d H:i:s", strtotime('2021-01-23 08:00:00'));
    $previous_time = date("Y-m-d H:i:s", strtotime($now_time)-86400);

    //查詢所有機台
    $machine_status_head_data = Query_All_Device();

    //查詢machine_on_off_hist，查詢所有機台今日的開關機狀態
    $machine_on_off_hist_data = Query_All_Device_On_Off($previous_time, $now_time, $machine_status_head_data);

    //處理各機台
    $machine_status = [];
    foreach ($machine_status_head_data as $index => $value) {
        $cus_device_id = $value['cus_device_id'];
        $device_id = $value['device_id'];
        $machine_light_list = $value['machine_light_list'];
        $machine_abn_list = $value['machine_abn_list'];

        //查詢單一機台當日資料
        $device_data = Query_Device_Data($cus_device_id, $machine_light_list, $previous_time, $now_time);

        //整理取得該機台當日時間區間資料
        $device_detail_data = Get_Device_Detail_Time($device_data, $machine_light_list, $machine_abn_list, $previous_time);

        //若當日有資料才做
        if (!empty($device_detail_data)) {
            //紀錄停機時間
            if (empty($machine_on_off_hist_data[$device_id])) {
                $machine_on_off_hist_data[$device_id] = [];
            }
            $machine_status_S = Device_Stop_Time($device_id, $machine_on_off_hist_data[$device_id], $previous_time, $now_time);
            //加總停機時間
            [$machine_status_S_time_rate, $machine_status_S_time_array] = Device_All_Time($machine_status_S);

            //整理出停機以外的時間資料
            $device_detail_time_data = Device_Detail_Time($device_detail_data, $machine_status_S, $previous_time, $now_time);

            //整理出異常、運轉、待機時間
            [$machine_status_H, $machine_status_R, $machine_status_Q] = Device_Status_Time($device_detail_time_data, $machine_abn_list, $machine_status_S, $previous_time, $now_time);
            //加總異常時間
            [$machine_status_H_time_rate, $machine_status_H_time_array] = Device_All_Time($machine_status_H);
            [$machine_status_R_time_rate, $machine_status_R_time_array] = Device_All_Time($machine_status_R);
            [$machine_status_Q_time_rate, $machine_status_Q_time_array] = Device_All_Time($machine_status_Q);

            // 取得機台運轉時所加工的工單
            // $machine_status_R = Device_Run_Work_Code($device_name, $machine_status_R,  isset($work_code_use_data[$device_name])?$work_code_use_data[$device_name]:[], $previous_time, $now_time);

            $all_time_array = array_merge($machine_status_S_time_array, $machine_status_H_time_array, $machine_status_R_time_array, $machine_status_Q_time_array);
            usort($all_time_array, 'sort_first_time');

            //將剩餘的時間加入停機時間
            $machine_status_S = array_merge($machine_status_S, Device_Remaining_Time($all_time_array, $previous_time, $now_time));
            //排序停機時間
            usort($machine_status_S, 'sort_timestamp_first_time');
            //確認停機時間無交集
            if (count($machine_status_S) > 1) {
                $machine_status_S = Check_Time_No_Cross($machine_status_S);
            }
            //加總停機時間
            [$machine_status_S_time_rate, $machine_status_S_time_array] = Device_All_Time($machine_status_S);

            //儲存
            $machine_status[$device_id] = array(
                'S' => array(
                    'rate' => $machine_status_S_time_rate . '%',
                    'datail' => $machine_status_S
                ),
                'H' => array(
                    'rate' => $machine_status_H_time_rate . '%',
                    'datail' => $machine_status_H
                ),
                'R' => array(
                    'rate' => $machine_status_R_time_rate . '%',
                    'datail' => $machine_status_R
                ),
                'Q' => array(
                    'rate' => $machine_status_Q_time_rate . '%',
                    'datail' => $machine_status_Q
                )
            );
        } else {
            $machine_status[$device_id] = array(
                'S' => array(
                    'rate' => '100%',
                    'datail' => [array('timestamp' => [$previous_time, $now_time])]
                ),
                'H' => array(
                    'rate' => '0%',
                    'datail' => []
                ),
                'R' => array(
                    'rate' => '0%',
                    'datail' => []
                ),
                'Q' => array(
                    'rate' => '0%',
                    'datail' => []
                )
            );
        }
    }

    if (!empty($machine_status)) {
        $push_data = [];
        foreach ($machine_status as $device_id => $value) {
            array_push($push_data, array(
                'device_id' => $device_id,
                // 'device_name' => $device_name,
                'machine_detail' => $value
            ));
        }
    } else {
        return null;
    }
    return $push_data;
}

//查詢所有機台
function Query_All_Device() {
    //查詢所有機台
    $device = CommonSpecificKeyQuery('Redis', '*', 'yes');
    if ($device['Response'] !== 'ok') {
        return;
    }
    $device_data = $device['QueryValueData'];

    $return_device_data = array();
    foreach ($device_data as $key => $value) {
        $cus_device_id = explode('mes_device_status_', $key)[1];
        array_push($return_device_data, array(
            'cus_device_id' => $cus_device_id,
            'device_id' => $value['device_id'],
            'device_name' => $value['device_name'],
            'machine_light_list' => !empty($value['machine_light_list']) && is_string($value['machine_light_list']) ? json_decode($value['machine_light_list'], true) : [],
            'machine_abn_list' => !empty($value['machine_abn_list']) && is_string($value['machine_abn_list']) ? json_decode($value['machine_abn_list'], true) : []
        ));
    }

    return $return_device_data;
}

//查詢machine_on_off_hist，查詢所有機台今日的開關機狀態
function Query_All_Device_On_Off($previous_time, $now_time, $device_mem_data) {
    $device_obj = array();

    //查詢今天所有的開關機資料
    $query_machine_on_off_hist = new apiJsonBody_query;
    $query_machine_on_off_hist->setFields(['upload_at', 'device_id', 'status']);
    $query_machine_on_off_hist->setTable('machine_on_off_hist');
    $query_machine_on_off_hist->addIntervaltime('upload_at', [$previous_time, $now_time]);
    foreach ($device_mem_data as $value) {
        $value['device_id'] = $value['device_id'];
        $query_machine_on_off_hist->addSymbols('device_id', 'equal');
        $query_machine_on_off_hist->addWhere('device_id', strtolower($value['device_id']));
        $device_obj[$value['device_id']] = array();
    }
    $query_machine_on_off_hist->setLimit(['ALL']);
    $query_machine_on_off_hist->setOrderby(['asc','upload_at']);
    $query_machine_on_off_hist_data = $query_machine_on_off_hist->getApiJsonBody();
    $machine_on_off_hist = CommonSqlSyntax_Query_v2_5($query_machine_on_off_hist_data, "PostgreSQL");

    if ($machine_on_off_hist['Response'] !== 'ok') {
        return $device_obj;
    }
    $machine_on_off_hist_data = $machine_on_off_hist['QueryTableData'];

    foreach ($machine_on_off_hist_data as $value) {
        array_push($device_obj[$value['device_id']], $value);
    }

    //如果沒有開關機資料，則要紀錄該機台再查詢該機台最後一筆資料的狀態
    $second_query = array();
    foreach ($device_obj as $key => $value) {
        if (empty($value)) {
            array_push($second_query, $key);
        }
    }

    //查詢機台最後一筆資料的狀態
    if (!empty($second_query)) {
        $query_machine_on_off_hist = new apiJsonBody_query;
        $query_machine_on_off_hist->setFields(['upload_at', 'device_id', 'status']);
        $query_machine_on_off_hist->setTable('machine_on_off_hist');
        foreach ($second_query as $value) {
            $query_machine_on_off_hist->addSymbols('combine', array('upload_at' => array('in')));
            $query_machine_on_off_hist->addWhere('combine', array('upload_at' => array('subcondition_' . $value)));
            $query_machine_on_off_hist->addSubquery('subcondition_' . $value);
            $query_machine_on_off_hist_subcondition = $query_machine_on_off_hist->getSubquery('subcondition_' . $value);
            $query_machine_on_off_hist_subcondition->setFields(['upload_at']);
            $query_machine_on_off_hist_subcondition->setTable('machine_on_off_hist');
            $query_machine_on_off_hist_subcondition->addSymbols('device_id', 'equal');
            $query_machine_on_off_hist_subcondition->addWhere('device_id', $value);
            $query_machine_on_off_hist_subcondition->setLimit([0,1]);
            $query_machine_on_off_hist_subcondition->setOrderby(['desc','upload_at']);
        }
        $query_machine_on_off_hist->setLimit(['ALL']);
        $query_machine_on_off_hist->setOrderby(['desc','upload_at']);
        $query_machine_on_off_hist_data = $query_machine_on_off_hist->getApiJsonBody();
        $machine_on_off_hist = CommonSqlSyntax_Query_v2_5($query_machine_on_off_hist_data, "PostgreSQL");

        if ($machine_on_off_hist['Response'] !== 'ok') {
            return $device_obj;
        }
        $machine_on_off_hist_data = $machine_on_off_hist['QueryTableData'];

        foreach ($machine_on_off_hist_data as $value) {
            $device_id = $value['device_id'];
            if (in_array($device_id, $second_query)) {
                if (empty($device_obj[$device_id])) {
                    array_push($device_obj[$device_id], array(
                        'upload_at' => $previous_time,
                        'device_id' => $value['device_id'], 
                        'status' => $value['status']
                    ));
                }
            }
        }
    }

    return $device_obj;
}

//查詢機台機型
function Query_machine_model($machine_status_head_data){
    $url = "https://localhost:3687/api/CHUNZU/2.0/ms/CommonUse/SqlSyntax?uid=@sapido@PaaS&getSqlSyntax=no";
    $device_name = [];
    $davice_symbol = [];
    foreach ($machine_status_head_data as $key => $value) {
        array_push($device_name, $value['device_name']);
        array_push($davice_symbol, 'equal');
    }

    if (!empty($device_name)) {
        $fields = ['name', 'model'];
        $whereAttr = new stdClass();
        $whereAttr->name = $device_name;
        $symbols = new stdClass();
        $symbols->name = $davice_symbol;
        $data = array(
            'condition_1' => array(
                'fields' => $fields,
                'intervaltime' => "",
                'table' => 'device_box',
                'orderby' => "",
                'limit' => ['ALL'],
                'where' => $whereAttr,
                'symbols' => $symbols,
                'union' => "",
                'subquery' => ""
            )
        );
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
        $device_box = json_decode($result, true);
        if ($device_box['Response'] !== 'ok') {
            return [];
        } else if (count($device_box['QueryTableData']) == 0) {
            return [];
        }
        return $device_box['QueryTableData'];
    } else {
        return [];
    }
}

//查詢機型燈號
function Query_machine_light($machine_model_data){
    $url = "https://localhost:3687/api/CHUNZU/2.0/my/CommonUse/SqlSyntax?uid=@sapido@PaaS&getSqlSyntax=no";
    $device_model = [];
    $davice_symbol = [];
    $davice_model = array();
    foreach ($machine_model_data as $key => $value) {
        array_push($device_model, $value['model']);
        array_push($davice_symbol, 'equal');
        $davice_model[$value['name']] = array(
            'model' => $value['model']
        );
    }

    if (!empty($device_model)) {
        $fields = ['name', 'model'];
        $whereAttr = new stdClass();
        $whereAttr->model = $device_model;
        $symbols = new stdClass();
        $symbols->model = $davice_symbol;
        $data = array(
            'condition_1' => array(
                'fields' => "",
                'intervaltime' => "",
                'table' => 'machine_status_list',
                'orderby' => "",
                'limit' => ['ALL'],
                'where' => $whereAttr,
                'symbols' => $symbols,
                'union' => "",
                'subquery' => ""
            )
        );
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
        $machine_status_list = json_decode($result, true);
        if ($machine_status_list['Response'] !== 'ok') {
            return [];
        } else if (count($machine_status_list['QueryTableData']) == 0) {
            return [];
        }
        $machine_status_list_data =  $machine_status_list['QueryTableData'];
    } else {
        return [];
    }
    
    foreach ($davice_model as $davice_model_key => $davice_model_value) {
        foreach ($machine_status_list_data as $machine_status_list_data_key => $machine_status_list_data_value) {
            if ($davice_model_value['model'] == $machine_status_list_data_value['model']) {
                if (is_string($machine_status_list_data_value['light_list'])) {
                    $machine_status_list_data_value['light_list'] = json_decode($machine_status_list_data_value['light_list'], true);
                }
                $davice_model[$davice_model_key]['light_list'] = $machine_status_list_data_value['light_list'];
            }
        }
    }
    return $davice_model;
}

//取得該機台的燈號異常值
function Query_machine_light_abn($machine_light_data, $machine_abn_data){
    $machine_light_abn_data = array();
    foreach ($machine_light_data as $device_name => $device_name_value) {
        if (isset($device_name_value['light_list'])) {
            foreach ($device_name_value['light_list'] as $light_key => $light_value) {
                foreach ($machine_abn_data as $machine_abn_data_key => $machine_abn_data_value) {
                    if ($light_key == $machine_abn_data_key) {
                        if (!isset($machine_light_abn_data[$device_name])) {
                            $machine_light_abn_data[$device_name] = array();
                        }
                        $machine_light_abn_data[$device_name][$light_key] = $machine_abn_data_value;
                    }
                }
            }
        }
    }
    return $machine_light_abn_data;
}

//查詢machine_abn，機台異常資料
function Query_All_Device_Abn() {
    $url = "https://localhost:3687/api/CHUNZU/1.0/my/CommonUse/TableData?table=machine_abn&uid=@sapido@PaaS";
    //The JSON data.
    $options = array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $machine_abn = json_decode($result, true);
    if ($machine_abn['Response'] != 'ok' || count($machine_abn['QueryTableData']) == 0) {//先抓是否抓取成功，成功的話繼續執行
        return [];
    } else {
        $machine_abn['QueryTableData'];
    }

    $machine_abn_data = $machine_abn['QueryTableData'];
    $machine_abn_code = [];
    for ($i=0; $i < count($machine_abn_data); $i++) { 
        $machine_abn_code[$machine_abn_data[$i]['name']] = array(
            'err_code' => $machine_abn_data[$i]['err_code'],
            'value' => $machine_abn_data[$i]['value'],
            'description' => $machine_abn_data[$i]['description']
        );
    }

    return $machine_abn_code;
}

//查詢單一機台當日資料
function Query_Device_Data($cus_device_id, $machine_light_list, $previous_time, $now_time) {
    $url = "https://localhost:3687/api/CHUNZU/2.5/myps/Sensor/SqlSyntax?uid=@sapido@PaaS&dbName=site2&getSqlSyntax=no";
    if (!empty($cus_device_id)) {
        // $fields = array_keys($machine_light_list);
        // array_push($fields, 'upload_at', 'opr', 'error_code');
        $data = array(
            'condition_1' => array(
                'fields' => "",
                'intervaltime' => array(
                    "upload_at" => array([$previous_time, $now_time])
                ),
                'table' => $cus_device_id . '_main',
                'orderby' => ["asc", "upload_at"],
                'limit' => ["ALL"],
                'where' => "",
                'symbols' => "",
                'union' => "",
                'subquery' => ""
            )
        );
        try {
            $ch = curl_init($url);
        
            // Check if initialization had gone wrong*    
            if ($ch === false) {
                throw new Exception('failed to initialize');
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($data)))
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, '0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, '0');
            // curl_setopt(/* ... */);
        
            $content = curl_exec($ch);
            $result = json_decode($content, true);

            if ($result['Response'] !== 'ok') {
                return [];
            } else if (count($result['QueryTableData']) == 0) {
                return [];
            }
            $result_data =  $result['QueryTableData'];
            // Check the return value of curl_exec(), too
            if ($content === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            /* Process $content here */
            
            // Close curl handle
            curl_close($ch);
            return $result_data;
        } catch(Exception $e) {
            return [];
            // trigger_error(sprintf(
            //     'Curl failed with error #%d: %s',
            //     $e->getCode(), $e->getMessage()),
            //     E_USER_ERROR);
        }
    } else {
        return [];
    }
}

//機台新增感測項目需在此新增
//整理取得該機台當日時間區間資料
function Get_Device_Detail_Time($device_data, $machine_light_list, $machine_abn_list, $previous_time) {
    $device_detail_data = [];
    if (!empty($device_data)) {
        $machine_detail_old_Data = [];
        $machine_detail_now_Detail = [];
        $machine_detail_now_Status = [];
        $machine_detail_now_Status_Time = [];
        for ($i=0; $i < count($device_data); $i++) {
            $machine_detail = $device_data[$i];
            if (empty($machine_detail['upload_at'])) {
                continue;
            }
            // if (strtotime($device_data[$i]['upload_at']) < strtotime($previous_time)) {
            if (strtotime($machine_detail['upload_at']) < strtotime($previous_time)) {
                continue;
            }
            $chain_this_data = array();
            foreach ($machine_light_list as $key => $value) {
                $chain_this_data[$key] = $machine_detail[$key];
            }

            if (isset($machine_detail['error_code'])) {
                $chain_this_data['error_code'] = json_encode($machine_detail['error_code']);
            }

            if (isset($machine_detail['opr'])) {
                $chain_this_data['opr'] = $machine_detail['opr'];
            } else {
                $chain_this_data['opr'] = 0;
            }

            if (count($machine_detail_old_Data) == 0) {
                $machine_detail_old_Data = $chain_this_data;//儲存當作比對的物件
                $machine_detail_now_Status = $chain_this_data;//儲存現在的物件
                array_push($machine_detail_now_Status_Time, $machine_detail['upload_at']);//第一筆為開始，第二筆為結束
                // array_push($machine_detail_now_Status_Time, $device_data[$i]['upload_at']);//時間異常，暫時用server時間
            } else {
                if ($i < count($device_data) - 1) {//確認不是最後一筆
                    if (count(array_keys(array_diff_assoc($chain_this_data, $machine_detail_old_Data))) == 0) {//判斷是否一樣，如果一樣就儲存最後的時間
                        $machine_detail_now_Status = $machine_detail_old_Data;
                        $machine_detail_now_Status_Time[1] = $machine_detail['upload_at'];
                        // $machine_detail_now_Status_Time[1] = $device_data[$i]['upload_at'];//時間異常，暫時用server時間
                    } else {//如果不一樣，儲存最後的時間，並記錄到陣列中，在開始一筆新的紀錄
                        //如果判斷的是潤滑中再改變其餘皆正常，則視為相同狀態
                        // if (count(array_keys(array_diff_assoc($chain_this_data, $machine_detail_old_Data))) == 1 && array_keys(array_diff_assoc($chain_this_data, $machine_detail_old_Data))[0] == 'in_lube') {
                        //     if ($chain_this_data['opr'] == 0 && $machine_detail_old_Data['opr'] == 0) {
                        //         $machine_detail_now_Status = $machine_detail_old_Data;
                        //         $machine_detail_now_Status_Time[1] = $device_data[$i]['upload_at'];//時間異常，暫時用server時間
                        //         continue;
                        //     }
                        // }
                        $machine_detail_now_Status = $machine_detail_old_Data;
                        $machine_detail_now_Status_Time[1] = $machine_detail['upload_at'];
                        // $machine_detail_now_Status_Time[1] = $device_data[$i]['upload_at'];//時間異常，暫時用server時間
                        $machine_detail_now_Detail[count($machine_detail_now_Detail)] = $machine_detail_now_Status;
                        $machine_detail_now_Detail[count($machine_detail_now_Detail) - 1]['startTime'] = $machine_detail_now_Status_Time[0];
                        $machine_detail_now_Detail[count($machine_detail_now_Detail) - 1]['endTime'] = $machine_detail_now_Status_Time[1];
                        // $machine_detail_now_Detail[count($machine_detail_now_Detail) - 1]['cnt'] = $machine_detail['cnt1'];
                     
                        $machine_detail_old_Data = $chain_this_data;
                        $machine_detail_now_Status = $machine_detail_old_Data;
                        $machine_detail_now_Status_Time = [];
                        array_push($machine_detail_now_Status_Time, $machine_detail['upload_at']);
                        // array_push($machine_detail_now_Status_Time, $device_data[$i]['upload_at']);//時間異常，暫時用server時間
                    }
                } else {//最後一筆，儲存最後的時間，並記錄到陣列中
                    $machine_detail_now_Status = $machine_detail_old_Data;
                    $machine_detail_now_Status_Time[1] = $machine_detail['upload_at'];
                    // $machine_detail_now_Status_Time[1] = $device_data[$i]['upload_at'];//時間異常，暫時用server時間
                    $machine_detail_now_Detail[count($machine_detail_now_Detail)] = $machine_detail_now_Status;
                    $machine_detail_now_Detail[count($machine_detail_now_Detail) - 1]['startTime'] = $machine_detail_now_Status_Time[0];
                    $machine_detail_now_Detail[count($machine_detail_now_Detail) - 1]['endTime'] = $machine_detail_now_Status_Time[1];
                    // $machine_detail_now_Detail[count($machine_detail_now_Detail) - 1]['cnt'] = $machine_detail['cnt1'];
                }
            }
        }
        $device_detail_data = $machine_detail_now_Detail;
    }

    return $device_detail_data;
}

//機台停機時間
function Device_Stop_Time($device_id, $machine_on_off_hist_data, $previous_time, $now_time) {
    $machine_status_S = [];

    if (!empty($machine_on_off_hist_data)) {
        for ($i=0; $i < count($machine_on_off_hist_data); $i++) { 
            if (empty($machine_status_S)) {
                if ($machine_on_off_hist_data[$i]['status'] == 'S') {
                    if (strtotime($previous_time) == strtotime($machine_on_off_hist_data[$i]['upload_at']) && count($machine_on_off_hist_data) == 1) {
                        return $machine_status_S = [];
                    }
                    array_push($machine_status_S, array(
                        'timestamp' => [$previous_time, $machine_on_off_hist_data[$i]['upload_at']]
                        )
                    );
                    continue;
                }
            }
            if ($machine_on_off_hist_data[$i]['status'] == 'E') {
                array_push($machine_status_S, array(
                    'timestamp' => [$machine_on_off_hist_data[$i]['upload_at']]
                    )
                );
            } else if ($machine_on_off_hist_data[$i]['status'] == 'S') {
                $position = count($machine_status_S) - 1;
                array_push($machine_status_S[$position]['timestamp'], $machine_on_off_hist_data[$i]['upload_at']);
            }
        }
    } else {
        $url = "https://localhost:3687/api/CHUNZU/2.5/myps/Sensor/SqlSyntax?uid=@sapido@PaaS&dbName=site2&getSqlSyntax=no";
        $symbols = new stdClass();
        $symbols->device_id = "equal";
        $whereAttr = new stdClass();
        $whereAttr->device_id = strtolower($device_id);
        $data = array(
            'condition_1' => array(
                'fields' => "",
                'intervaltime' => "",
                'table' => 'machine_on_off_hist',
                'orderby' => ['desc', 'upload_at'],
                'limit' => [0,1],
                'where' => $whereAttr,
                'symbols' => $symbols,
                'union' => "",
                'subquery' => ""
            )
        );
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
        $this_machine_on_off_hist = json_decode($result, true);
        if ($this_machine_on_off_hist['Response'] != 'ok' || count($this_machine_on_off_hist['QueryTableData']) == 0) {//先抓是否抓取成功，成功的話繼續執行
            $this_machine_on_off_hist_data = [];
        } else {
            $this_machine_on_off_hist_data = $this_machine_on_off_hist['QueryTableData'];
        }

        if (!empty($this_machine_on_off_hist_data)) {
            if ($this_machine_on_off_hist_data[0]['status'] == 'E') {
                array_push($machine_status_S,  array(
                    'timestamp' => [$previous_time, $now_time]
                    )
                );
            }
        }
    }
    if (!empty($machine_status_S)) {
        $machine_status_S_length = count($machine_status_S);
        if (count($machine_status_S[$machine_status_S_length - 1]['timestamp']) != 2) {
            array_push($machine_status_S[$machine_status_S_length - 1]['timestamp'], $now_time);
        }
    }

    return $machine_status_S;
}

//停機以外的時間
function Device_Detail_Time($device_detail_data, $machine_status_S, $previous_time, $now_time) {
    $device_detail_time_data = [];
    if (!empty($device_detail_data)) {
        if(count($device_detail_data) > 0) {
            for ($i=0; $i < count($device_detail_data); $i++) { 
                $status_detail = $device_detail_data[$i];

                $device_time_start = strtotime($status_detail['startTime']);
                $device_time_end = strtotime($status_detail['endTime']);
                for ($j=0; $j < count($machine_status_S); $j++) { 
                    $stop_time_start = strtotime($machine_status_S[$j]['timestamp'][0]);
                    $stop_time_end = strtotime($machine_status_S[$j]['timestamp'][1]);
                    if ($stop_time_start < $device_time_start && $stop_time_end < $device_time_start && $stop_time_start < $device_time_end && $stop_time_end < $device_time_end) {

                    } else if ($stop_time_start <= $device_time_start && $stop_time_end < $device_time_start && $stop_time_start > $device_time_end && $stop_time_end < $device_time_end) {
                        $status_detail['startTime'] = $machine_status_S[$j]['timestamp'][1];
                    // break;
                    } else if ($stop_time_start <= $device_time_start && $stop_time_end <= $device_time_start && $stop_time_start >= $device_time_end && $stop_time_end >= $device_time_end) {
                        $status_detail['startTime'] = '1970-01-01 08:00:00';
                        $status_detail['endTime'] = '1970-01-01 08:00:00';
                    // break;
                    } else if ($stop_time_start >= $device_time_start && $stop_time_end <= $device_time_start && $stop_time_start >= $device_time_end && $stop_time_end <= $device_time_end) {
                        array_splice($device_detail_data, $i + 1, 0, array(
                            $status_detail)
                        );
                        $device_detail_data[$i + 1]['startTime'] = $machine_status_S[$j]['timestamp'][1];
                        $status_detail['endTime'] = $machine_status_S[$j]['timestamp'][0];
                    break;
                    } else if ($stop_time_start > $device_time_start && $stop_time_end < $device_time_start && $stop_time_start > $device_time_end && $stop_time_end >= $device_time_end) {
                        $status_detail['endTime'] = $machine_status_S[$j]['timestamp'][0];
                    // break;
                    } else if ($stop_time_start > $device_time_start && $stop_time_end > $device_time_start && $stop_time_start > $device_time_end && $stop_time_end > $device_time_end) {

                    } else if ($stop_time_start > $device_time_start && $stop_time_end >= $device_time_start && $stop_time_start <= $device_time_end && $stop_time_end <= $device_time_end) {
                        array_splice($device_detail_data, $i + 1, 0, array(
                            $status_detail)
                        );
                        $device_detail_data[$i + 1]['startTime'] = $machine_status_S[$j]['timestamp'][1];
                        $status_detail['endTime'] = $machine_status_S[$j]['timestamp'][0];
                    break;
                    } else if ($stop_time_start <= $device_time_start && $stop_time_end >= $device_time_start && $stop_time_start < $device_time_end && $stop_time_end < $device_time_end) {
                        $status_detail['startTime'] = $machine_status_S[$j]['timestamp'][1];
                    // break;
                    } else if ($stop_time_start <= $device_time_start && $stop_time_end >= $device_time_start && $stop_time_start < $device_time_end && $stop_time_end > $device_time_end) {
                        $status_detail['startTime'] = '1970-01-01 08:00:00';
                        $status_detail['endTime'] = '1970-01-01 08:00:00';
                    // break;
                    } else if ($stop_time_start > $device_time_start && $stop_time_end > $device_time_start && $stop_time_start <= $device_time_end && $stop_time_end > $device_time_end) {
                        $status_detail['endTime'] = $machine_status_S[$j]['timestamp'][0];
                    // break;
                    }
                }
                array_push($device_detail_time_data, $status_detail);
            }
            $remove_array = array_keys(array_column($device_detail_time_data, 'startTime'), '1970-01-01 08:00:00');
            if (!empty($remove_array)) {
                foreach ($remove_array as $key) {
                    unset($device_detail_time_data[$key]);
                };
            };
        }
    }
    
    return array_values($device_detail_time_data);
}

//機台異常、運轉、待機時間
function Device_Status_Time($device_detail_data, $machine_abn_list, $machine_status_S, $previous_time, $now_time) {
    $machine_status_H = [];
    $machine_status_R = [];
    $machine_status_Q = [];
    $opr_count =0 ;
    if (!empty($device_detail_data) && !empty($machine_abn_list)) {
        if(count($device_detail_data) > 0) {
            for ($i=0; $i < count($device_detail_data); $i++) {
                $status_detail = $device_detail_data[$i];
                $machine_abn_id = [];
                $machine_abn_description = [];
                //判斷是否有異常
                if (isset($status_detail['error_code'])) {
                    $status_detail['error_code'] = json_decode($status_detail['error_code'], true);
                    $machine_abn_id = implode("\n", $status_detail['error_code']);
                    $machine_abn_description = array();
                    foreach ($machine_abn_list as $machine_abn_list_key => $machine_abn_list_value) {
                        if (in_array($machine_abn_list_key, $status_detail['error_code'])) {
                            array_push($machine_abn_description, $machine_abn_list_value['name']);
                        }
                    }
                    array_push($machine_status_H, array(
                        'machine_abn_id' => $machine_abn_id,
                        'machine_abn_description' => implode("\n", $machine_abn_description),
                        'timestamp' => [$status_detail['startTime'],$status_detail['endTime']]
                        )
                    );
                } else {
                    //判斷是否有運轉
                    if ($status_detail['opr'] == 1) {
                        array_push($machine_status_R, array(
                            'timestamp' => [$status_detail['startTime'],$status_detail['endTime']]
                            )
                        );
                    } else {
                        array_push($machine_status_Q, array(
                            'timestamp' => [$status_detail['startTime'],$status_detail['endTime']]
                            )
                        );
                    }
                }
            }
        }
    }

    return [$machine_status_H, $machine_status_R, $machine_status_Q];
}

//機台運轉時所加工的工單
function Device_Run_Work_Code($device_name, $machine_status_R, $work_code_use_data, $previous_time, $now_time) {
    if (count($machine_status_R) == 0) {
        return $machine_status_R;
    }
    $work_code_time = [];

    for ($i=0; $i < count($machine_status_R); $i++) { 
        foreach ($work_code_time as $key => $value) {
            $check_in_time = is_time_cross($machine_status_R[$i]['timestamp'][0], $machine_status_R[$i]['timestamp'][1], $value['time'][0], isset($value['time'][1])?$value['time'][1]:$now_time);
            if ($check_in_time) {
                $machine_status_R[$i]['work_code'] = $value['work_code'];
            break;
            }
        }
    }

    return $machine_status_R;
}

//機台時間
function Device_All_Time($machine_status){
    $time = 0;
    $time_array = [];
    foreach ($machine_status as $key => $value) {
        $time += (strtotime($value['timestamp'][1]) - strtotime($value['timestamp'][0]));
        array_push($time_array, $value['timestamp']);
    }
    $time = round(round($time / 86400, 2) * 100);
    return [$time, $time_array];
}

//補足機台剩餘不足1天的時間
function Device_Remaining_Time($all_time_array, $previous_time, $now_time) {
    $machine_status_S = [array(
        'timestamp' => [$previous_time, $now_time]
    )];
    for ($i=0; $i < count($all_time_array); $i++) {
        if ($all_time_array[$i][0] == $all_time_array[$i][1]) {
            continue;
        } 
        $all_time_array_start = strtotime($all_time_array[$i][0]);
        $all_time_array_end = strtotime($all_time_array[$i][1]);

        for ($j=0; $j < count($machine_status_S); $j++) { 
            $queue_time_start = strtotime($machine_status_S[$j]['timestamp'][0]);
            $queue_time_end = strtotime($machine_status_S[$j]['timestamp'][1]);
            if ($all_time_array_start < $queue_time_start && $all_time_array_start < $queue_time_end && $all_time_array_end < $queue_time_start && $all_time_array_end < $queue_time_end) {

            } else if ($all_time_array_start <= $queue_time_start && $all_time_array_start < $queue_time_end && $all_time_array_end > $queue_time_start && $all_time_array_end < $queue_time_end) {
                $machine_status_S[$j]['timestamp'][0] = $all_time_array[$i][1];
            } else if ($all_time_array_start <= $queue_time_start && $all_time_array_start <= $queue_time_end && $all_time_array_end >= $queue_time_start && $all_time_array_end >= $queue_time_end) {
                $machine_status_S[$j]['timestamp'][0] = '1970-01-01 08:00:00';
                $machine_status_S[$j]['timestamp'][1] = '1970-01-01 08:00:00';
            } else if ($all_time_array_start >= $queue_time_start && $all_time_array_start <= $queue_time_end && $all_time_array_end >= $queue_time_start && $all_time_array_end <= $queue_time_end) {
                array_splice($machine_status_S, $j + 1, 0, array(array('timestamp' => [$all_time_array[$i][1], $machine_status_S[$j]['timestamp'][1]])));
                $machine_status_S[$j]['timestamp'][1] = $all_time_array[$i][0];
            } else if ($all_time_array_start > $queue_time_start && $all_time_array_start < $queue_time_end && $all_time_array_end > $queue_time_start && $all_time_array_end >= $queue_time_end) {
                $machine_status_S[$j]['timestamp'][1] = $all_time_array[$i][0];
            } else if ($all_time_array_start > $queue_time_start && $all_time_array_start > $queue_time_end && $all_time_array_end > $queue_time_start && $all_time_array_end > $queue_time_end) {

            }
        }
        $remove_array = array_keys(array_column($machine_status_S, 'timestamp'), ['1970-01-01 08:00:00','1970-01-01 08:00:00']);
        if (!empty($remove_array)) {
            foreach ($remove_array as $key) {
                unset($machine_status_S[$key]);
            };
        };
    }
    return $machine_status_S;
}

//將重疊在一起的時間合併
function Check_Time_No_Cross($machine_status_detail) {
    $new_machine_status_detail = [];
    for ($i=0; $i < count($machine_status_detail); $i++) { 
        if (isset($machine_status_detail[$i + 1])) {
            if (strtotime($machine_status_detail[$i]['timestamp'][1]) == strtotime($machine_status_detail[$i + 1]['timestamp'][0])) {
                $machine_status_detail[$i + 1]['timestamp'][0] = $machine_status_detail[$i]['timestamp'][0];
            } else {
                array_push($new_machine_status_detail, $machine_status_detail[$i]);
            }
        } else {
            array_push($new_machine_status_detail, $machine_status_detail[$i]);
        }
        // if (strtotime($machine_status_detail[$i]['timestamp'][1]) == strtotime($machine_status_detail[$i + 1]['timestamp'][0])) {
        //     array_push($new_machine_status_detail, array('timestamp'=>[$machine_status_detail[$i]['timestamp'][0], $machine_status_detail[$i + 1]['timestamp'][1]]));
        //     $i++;
        // } else {
        //     array_push($new_machine_status_detail, $machine_status_detail[$i]);
        //     if ($i == count($machine_status_detail) - 2) {
        //         array_push($new_machine_status_detail, $machine_status_detail[$i + 1]);
        //     }
        // }
    }
    return $new_machine_status_detail;
}

//陣列裡的machine_detail的timestamp的排序
function sort_machine_detail_time($a, $b){
    if(strtotime($a['machine_detail']['timestamp']) == strtotime($b['machine_detail']['timestamp'])) return 0;
    return (strtotime($a['machine_detail']['timestamp']) > strtotime($b['machine_detail']['timestamp'])) ? 1 : -1;
}

//陣列裡的第一個元素排序
function sort_first_time($a, $b){
    if(strtotime($a[0]) == strtotime($b[0])) return 0;
    return (strtotime($a[0]) > strtotime($b[0])) ? 1 : -1;
}

//陣列裡的timestamp的第一個元素排序
function sort_timestamp_first_time($a, $b){
    if(strtotime($a['timestamp'][0]) == strtotime($b['timestamp'][0])) return 0;
    return (strtotime($a['timestamp'][0]) > strtotime($b['timestamp'][0])) ? 1 : -1;
}

//判斷是否有交集
function is_time_cross($source_begin_time_1 = '', $source_end_time_1 = '', $source_begin_time_2 = '', $source_end_time_2 = '') {
    $beginTime1 = strtotime($source_begin_time_1);
    $endTime1 = strtotime($source_end_time_1);
    $beginTime2 = strtotime($source_begin_time_2);
    $endTime2 = strtotime($source_end_time_2);
    $status = $beginTime2 - $beginTime1;
    if ($status > 0) {
        $status2 = $beginTime2 - $endTime1;
        if ($status2 >= 0) {
            return false;
        } else {
            return true;
        }
    } else {
        $status2 = $endTime2 - $beginTime1;
        if ($status2 > 0) {
            return true;
        } else {
            return false;
        }
    }
}

//新增一筆紀錄
function Push_Data($push_data) {
    $url = "https://localhost:3687/api/CHUNZU/2.0/myps/Sensor/Rows/machine_status_sum?uid=@sapido@PaaS";
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
