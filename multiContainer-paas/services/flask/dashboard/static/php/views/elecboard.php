<?php
function MainQuery($params)//主要查詢按鈕
{
    $test = json_decode('{
        "QueryTableData": [
          {
            "device_name": "F01",
            "model": "CBP136L",
            "group_name": "成四組",
            "process_id": "5",
            "device_capacity_min": "0",
            "device_now_count": "21264",
            "machine_status": "待機",
            "day_work_order_count": "0",
            "day_remain_work_order_count": "0",
            "work_code": "",
            "work_status": "",
            "work_qty": "",
            "work_percent": ""
          },
          {
            "process": "打頭",
            "group": "成四組",
            "device_name": "F01",
            "device_model": "CBF63S",
            "machine_status": "Q",
            "device_run_time": "6時13分",
            "device_activation": "26",
            "device_day_count": "117329",
            "device_capacity_min": "120",
            "machine_image": "CBF",
            "stack_bar_information": {
                "F01": [
                  {
                    "status": "關機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 08:00:00",
                    "endTime": "2021-01-14 11:58:48",
                    "duration_number": 14328
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 11:58:48",
                    "endTime": "2021-01-14 12:24:50",
                    "duration_number": 1562
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 12:24:50",
                    "endTime": "2021-01-14 12:27:08",
                    "duration_number": 138
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 12:27:08",
                    "endTime": "2021-01-14 12:28:57",
                    "duration_number": 109
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 12:28:57",
                    "endTime": "2021-01-14 12:30:44",
                    "duration_number": 107
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 12:30:44",
                    "endTime": "2021-01-14 12:33:04",
                    "duration_number": 140
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 12:33:04",
                    "endTime": "2021-01-14 12:39:58",
                    "duration_number": 414
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 12:39:58",
                    "endTime": "2021-01-14 13:27:08",
                    "duration_number": 2830
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 13:27:08",
                    "endTime": "2021-01-14 13:28:40",
                    "duration_number": 92
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 13:28:40",
                    "endTime": "2021-01-14 13:29:03",
                    "duration_number": 23
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 13:29:03",
                    "endTime": "2021-01-14 13:34:10",
                    "duration_number": 307
                  },
                  {
                    "status": "關機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 13:34:10",
                    "endTime": "2021-01-14 14:05:53",
                    "duration_number": 1903
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 14:05:53",
                    "endTime": "2021-01-14 14:08:28",
                    "duration_number": 155
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 14:08:28",
                    "endTime": "2021-01-14 15:05:22",
                    "duration_number": 3414
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 15:05:22",
                    "endTime": "2021-01-14 18:40:14",
                    "duration_number": 12892
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 18:40:14",
                    "endTime": "2021-01-14 18:41:49",
                    "duration_number": 95
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 18:41:49",
                    "endTime": "2021-01-14 19:04:30",
                    "duration_number": 1361
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:04:30",
                    "endTime": "2021-01-14 19:43:54",
                    "duration_number": 2364
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:43:54",
                    "endTime": "2021-01-14 19:46:55",
                    "duration_number": 181
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:46:55",
                    "endTime": "2021-01-14 19:47:22",
                    "duration_number": 27
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:47:22",
                    "endTime": "2021-01-14 19:49:14",
                    "duration_number": 112
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:49:14",
                    "endTime": "2021-01-14 19:50:25",
                    "duration_number": 71
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:50:25",
                    "endTime": "2021-01-14 19:52:31",
                    "duration_number": 126
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:52:31",
                    "endTime": "2021-01-14 19:53:04",
                    "duration_number": 33
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:53:04",
                    "endTime": "2021-01-14 19:54:23",
                    "duration_number": 79
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:54:23",
                    "endTime": "2021-01-14 19:54:52",
                    "duration_number": 29
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:54:52",
                    "endTime": "2021-01-14 19:55:34",
                    "duration_number": 42
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 19:55:34",
                    "endTime": "2021-01-14 20:29:11",
                    "duration_number": 2017
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 20:29:11",
                    "endTime": "2021-01-14 21:19:14",
                    "duration_number": 3003
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 21:19:14",
                    "endTime": "2021-01-14 21:20:09",
                    "duration_number": 55
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 21:20:09",
                    "endTime": "2021-01-14 21:28:34",
                    "duration_number": 505
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 21:28:34",
                    "endTime": "2021-01-14 22:28:08",
                    "duration_number": 3574
                  },
                  {
                    "status": "警報",
                    "alarmDetail": "過負載異常",
                    "startTime": "2021-01-14 22:28:08",
                    "endTime": "2021-01-14 22:36:18",
                    "duration_number": 490
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 22:36:18",
                    "endTime": "2021-01-14 22:50:29",
                    "duration_number": 851
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 22:50:29",
                    "endTime": "2021-01-14 22:50:36",
                    "duration_number": 7
                  },
                  {
                    "status": "警報",
                    "alarmDetail": "過負載異常",
                    "startTime": "2021-01-14 22:50:36",
                    "endTime": "2021-01-14 22:50:38",
                    "duration_number": 2
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 22:50:38",
                    "endTime": "2021-01-14 23:08:10",
                    "duration_number": 1052
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 23:08:10",
                    "endTime": "2021-01-14 23:11:08",
                    "duration_number": 178
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 23:11:08",
                    "endTime": "2021-01-14 23:15:40",
                    "duration_number": 272
                  },
                  {
                    "status": "生產",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 23:15:40",
                    "endTime": "2021-01-14 23:32:34",
                    "duration_number": 1014
                  },
                  {
                    "status": "待機",
                    "alarmDetail": "",
                    "startTime": "2021-01-14 23:32:34",
                    "endTime": "2021-01-15 00:00:00",
                    "duration_number": 1706
                  }
                ]
              },
            "work_code_information": [],
            "bucket_information": [],
            "produce_information": [],
            "wire_total_weight": "0",
            "wire_lose_rate": "0",
            "wire_information": [],
            "mould_information": [],
            "total_produce_time_information": [],
            "inspection_information": [],
            "operatelog_information": [
              {
                "status": "-",
                "alarmCode": "",
                "durationTime": "05:46:21",
                "startTime": "2021-01-25 08:00:00",
                "endTime": "2021-01-25 13:46:21",
                "alarmDetail": ""
              },
              {
                "status": "生產",
                "alarmCode": "",
                "durationTime": "03:17:07",
                "startTime": "2021-01-25 13:46:21",
                "endTime": "2021-01-25 17:03:28",
                "alarmDetail": ""
              },
              {
                "status": "警報",
                "alarmCode": "E_MC0013",
                "durationTime": "00:00:45",
                "startTime": "2021-01-25 17:03:28",
                "endTime": "2021-01-25 17:04:13",
                "alarmDetail": "短吋"
              },
              {
                "status": "待機",
                "alarmCode": "",
                "durationTime": "00:16:39",
                "startTime": "2021-01-25 17:04:13",
                "endTime": "2021-01-25 17:20:52",
                "alarmDetail": ""
              },
              {
                "status": "生產",
                "alarmCode": "",
                "durationTime": "00:00:53",
                "startTime": "2021-01-25 17:20:52",
                "endTime": "2021-01-25 17:21:45",
                "alarmDetail": ""
              },
              {
                "status": "待機",
                "alarmCode": "",
                "durationTime": "00:25:43",
                "startTime": "2021-01-25 17:21:45",
                "endTime": "2021-01-25 17:47:28",
                "alarmDetail": ""
              },
              {
                "status": "生產",
                "alarmCode": "",
                "durationTime": "00:19:26",
                "startTime": "2021-01-25 17:47:28",
                "endTime": "2021-01-25 18:06:54",
                "alarmDetail": ""
              },
              {
                "status": "待機",
                "alarmCode": "",
                "durationTime": "00:04:41",
                "startTime": "2021-01-25 18:06:54",
                "endTime": "2021-01-25 18:11:35",
                "alarmDetail": ""
              },
              {
                "status": "生產",
                "alarmCode": "",
                "durationTime": "01:44:02",
                "startTime": "2021-01-25 18:11:35",
                "endTime": "2021-01-25 19:55:37",
                "alarmDetail": ""
              },
              {
                "status": "警報",
                "alarmCode": "E_MC0013",
                "durationTime": "00:09:00",
                "startTime": "2021-01-25 19:55:37",
                "endTime": "2021-01-25 20:04:37",
                "alarmDetail": "短吋"
              },
              {
                "status": "待機",
                "alarmCode": "",
                "durationTime": "00:00:57",
                "startTime": "2021-01-25 20:04:37",
                "endTime": "2021-01-25 20:05:34",
                "alarmDetail": ""
              },
              {
                "status": "生產",
                "alarmCode": "",
                "durationTime": "00:31:29",
                "startTime": "2021-01-25 20:05:34",
                "endTime": "2021-01-25 20:37:03",
                "alarmDetail": ""
              },
              {
                "status": "待機",
                "alarmCode": "",
                "durationTime": "00:10:15",
                "startTime": "2021-01-25 20:37:03",
                "endTime": "2021-01-25 20:47:18",
                "alarmDetail": ""
              },
              {
                "status": "生產",
                "alarmCode": "",
                "durationTime": "00:21:02",
                "startTime": "2021-01-25 20:47:18",
                "endTime": "2021-01-25 21:08:20",
                "alarmDetail": ""
              },
              {
                "status": "待機",
                "alarmCode": "",
                "durationTime": "00:53:05",
                "startTime": "2021-01-25 21:08:20",
                "endTime": "2021-01-25 22:01:25",
                "alarmDetail": ""
              }
            ],
            "alarmHistory_information": [
              {
                "alarmDetail": "短吋",
                "alarmCode": "E_MC0013",
                "alarmVideo": "",
                "startTime": "2021-01-25 17:03:28",
                "endTime": "2021-01-25 17:04:13",
                "continuousTime": "00:00:45"
              },
              {
                "alarmDetail": "短吋",
                "alarmCode": "E_MC0013",
                "alarmVideo": "",
                "startTime": "2021-01-25 19:55:37",
                "endTime": "2021-01-25 20:04:37",
                "continuousTime": "00:09:00"
              }
            ],
            "machine_light_value": {
              "air_press_light": {
                "color": "gray",
                "name": "氣壓燈"
              },
              "inv_abn": {
                "color": "gray",
                "name": "變頻器異常"
              },
              "end_lube_press": {
                "color": "gray",
                "name": "末端油壓不足"
              },
              "tab_float": {
                "color": "gray",
                "name": "挾料台浮動"
              },
              "sf_door": {
                "color": "gray",
                "name": "安全門未關"
              },
              "len_short": {
                "color": "gray",
                "name": "短吋"
              },
              "lube_overflow": {
                "color": "gray",
                "name": "油面溢位"
              },
              "overload": {
                "color": "gray",
                "name": "過負載"
              },
              "pwr_light": {
                "color": "gray",
                "name": "電源燈"
              },
              "in_lube": {
                "color": "gray",
                "name": "潤滑中"
              },
              "fnt_sf_pin": {
                "color": "gray",
                "name": "前沖安全銷"
              },
              "finish_cnt": {
                "color": "gray",
                "name": "計數終了"
              },
              "mtr_end": {
                "color": "gray",
                "name": "材料終了"
              },
              "bk_sf_pin": {
                "color": "gray",
                "name": "後通安全銷"
              }
            }
          }
        ],
        "Response": "ok",
        "page": "elecboard"
        }', true
    );

    $device = CommonSpecificKeyQuery('Redis', 'test_emeter', 'no');
    if ($device['Response'] !== 'ok') {
        return;
    }
    $this_device_data = $device['QueryValueData'];
    
    $device_back_data = array();

    // $process = '打頭';
    // $group = $this_group;
    $device_name = 'F11';
    $device_model = $this_device_data['device_model'];
    $machine_status = $this_device_data['machine_status'];
    if ($this_device_data['machine_status'] == 'Q') {
        $machine_status = "待機";
    } else if ($this_device_data['machine_status'] == 'R') {
        $machine_status = "運作";
    } else if ($this_device_data['machine_status'] == 'H') {
        $machine_status = "異常";
    } else if ($this_device_data['machine_status'] == 'S') {
        $machine_status = "-";
    }
    $test['QueryTableData'][0]['machine_status'] = $machine_status;
    $device_run_time = $this_device_data['device_run_time'];
    $device_activation = $this_device_data['device_activation'];
    $device_day_count = $this_device_data['device_day_count'];
    $device_now_count = $this_device_data['device_now_count'];
    $test['QueryTableData'][0]['device_now_count'] = $device_now_count;
    $device_capacity_min = $this_device_data['device_capacity_min'];
    $machine_image = $this_device_data['machine_image'];
    $temp_data = json_decode($this_device_data['temp_data'], true);
    $flow_data = json_decode($this_device_data['flow_data'], true);
    $oilPressure_data = json_decode($this_device_data['oilPressure_data'], true);
    $airPressure1_data = json_decode($this_device_data['airPressure1_data'], true);
    $airPressure2_data = json_decode($this_device_data['airPressure2_data'], true);
    $oilTemp_data = json_decode($this_device_data['oilTemp_data'], true);
    $clutch_data = json_decode($this_device_data['clutch_data'], true);
    $oilLevel_data = json_decode($this_device_data['oilLevel_data'], true);
    $current_data = json_decode($this_device_data['current'], true);
    $voltage_data = json_decode($this_device_data['voltage'], true);
    $volt_vubr_data = json_decode($this_device_data['volt_vubr'], true);
    $RELEC_data = json_decode($this_device_data['RELEC'], true);
    if (empty($RELEC_data)) {
        $RELEC_data = array();
    }
    $RELEC_chart = array();
    for ($i=0; $i < count($RELEC_data); $i++) { 
        if ($i != 0) {
            $this_time = intval($RELEC_data[$i]['time']);
            $previous_time = intval($RELEC_data[$i-1]['time']);
            if ($this_time - 1 == $previous_time) {
                array_push($RELEC_chart, array(
                    "data" => round($RELEC_data[$i]['value'] - $RELEC_data[$i-1]['value'], 2),
                    "data_time" => $previous_time . '~' . $this_time
                ));
            } else {
                array_push($RELEC_chart, array(
                    "data" => round($RELEC_data[$i]['value'] - $RELEC_data[$i-1]['value'], 2),
                    "data_time" => $this_time - 1 . '~' . $this_time
                ));
            }
        }
    }
    if (!empty($machine_image)) {
        $machine_image = explode('.',$machine_image)[0];
    }
    $stack_bar_information = array($device_name => $this_device_data['stack_bar_information']);
    if (!empty($stack_bar_information[$device_name])) {
        if (is_string($stack_bar_information[$device_name])) {
            $stack_bar_information[$device_name] = json_decode($stack_bar_information[$device_name], true);
        }
    } else {
        $stack_bar_information = array($device_name => array());
        if (strtotime(date("Y-m-d H:i:s")) > strtotime(date("Y-m-d 08:00:00"))) {
            $durationTime = TimeSubtraction(date("Y-m-d 08:00:00"), date("Y-m-d H:i:s"), 'hour');
            array_push($stack_bar_information[$device_name], array(
                'status' => null,
                'alarmDetail' => '',
                'startTime' =>  date("Y-m-d 08:00:00"),
                'endTime' => date("Y-m-d H:i:s"),
                'duration_number' => $durationTime[2]
            ));
        } else {
            $durationTime = TimeSubtraction(date("Y-m-d H:i:s", strtotime(date("Y-m-d 08:00:00")-86400)), date("Y-m-d H:i:s"), 'hour');
            array_push($stack_bar_information[$device_name], array(
                'status' => null,
                'alarmDetail' => '',
                'startTime' =>  date("Y-m-d H:i:s", strtotime(date("Y-m-d 08:00:00")-86400)),
                'endTime' => date("Y-m-d H:i:s"),
                'duration_number' => $durationTime[2]
            ));
        }
    }
    $stack_bar_information[$device_name][0]['stacksBarNumber'] = array("08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","00","01","02","03","04","05","06","07","08");

    $operatelog_information = $this_device_data['operatelog_information'];
    if (!empty($operatelog_information)) {
        $alarmHistory_information = array();
        if (is_string($operatelog_information)) {
            $operatelog_information = json_decode($operatelog_information, true);
        }
        foreach ($operatelog_information as $key => $value) {
            if ($value['status'] == '警報') {
                array_push($alarmHistory_information, array(
                    'alarmDetail' => $value['alarmDetail'],
                    'alarmCode' => $value['alarmCode'],
                    'alarmVideo' => $value['alarmVideo'],
                    'startTime' => $value['startTime'],
                    'endTime' => $value['endTime'],
                    'continuousTime' => $value['durationTime']
                ));
            }
        }
    } else {
        $operatelog_information = array();
        if (strtotime(date("Y-m-d H:i:s")) > strtotime(date("Y-m-d 08:00:00"))) {
            $durationTime = TimeSubtraction(date("Y-m-d 08:00:00"), date("Y-m-d H:i:s"), 'hour');
            array_push($operatelog_information, array(
                'status' => '-',
                'alarmCode' => '',
                'alarmDetail' => '',
                'startTime' => date("Y-m-d 08:00:00"),
                'endTime' => date("Y-m-d H:i:s"),
                'durationTime' => $durationTime[0]
            ));
        } else {
            $durationTime = TimeSubtraction(date("Y-m-d H:i:s", strtotime(date("Y-m-d 08:00:00")-86400)), date("Y-m-d H:i:s"), 'hour');
            array_push($operatelog_information, array(
                'status' => '-',
                'alarmCode' => '',
                'alarmDetail' => '',
                'startTime' => date("Y-m-d H:i:s"), strtotime(date("Y-m-d 08:00:00")-86400),
                'endTime' => date("Y-m-d H:i:s"),
                'durationTime' => $durationTime[0]
            ));
        }
        $alarmHistory_information = array();
    }
    // $machine_light_list = $this_device_data['machine_light_list'];
    // if (!empty($machine_light_list)) {
    //     if (is_string($machine_light_list)) {
    //         $machine_light_list = json_decode($machine_light_list, true);
    //     }
    // }
    $machine_light_value = $this_device_data['machine_light_value'];
    if (!empty($machine_light_value)) {
        if (is_string($machine_light_value)) {
            $machine_light_value = json_decode($machine_light_value, true);
        }
    }
{
    // $data = array('querySingle' => 'Last');
    // $teble = 'test_main';
    // $test_main = SensorSingleRowQuery($data, 'PostgreSQL', $teble);
    // $return_main_data = array();
    // $return_main_timer = "";
    // $return_light_data = array();
    // $return_light_timer = "";
    // $return_health_data = array();
    // if (!empty($test_main['QueryTableData'])) {
    //     // $return_health_data = array(
    //     //     '油壓' => array(
    //     //         array("name" => "主油管迴路(主滑台側)", "value" => array("value" => $test_main['QueryTableData'][0]['msld_lup_out_oprs'], "unit" => ' Kg/cm2')),
    //     //         array("name" => "主油管迴路(剪斷側)", "value" => array("value" => $test_main['QueryTableData'][0]['cus_lup_out_oprs'], "unit" => ' Kg/cm2')),
    //     //         array("name" => "油管末端(主滑台側)", "value" => array("value" => $test_main['QueryTableData'][0]['msld_end_oprs'], "unit" => ' Kg/cm2')),
    //     //         array("name" => "油管末端(剪斷側)", "value" => array("value" => $test_main['QueryTableData'][0]['cus_end_oprs'], "unit" => ' Kg/cm2'))
    //     //     ),
    //     //     '流量' => array(
    //     //         array("name" => "主滑台(操作側)", "value" => array("value" => $test_main['QueryTableData'][0]['msld_op_flow'], "unit" => ' L/min')),
    //     //         array("name" => "主滑台(剪斷側)", "value" => array("value" => $test_main['QueryTableData'][0]['msld_cus_flow'], "unit" => ' L/min')),
    //     //         array("name" => "主曲軸襯銅(操作側)", "value" => array("value" => $test_main['QueryTableData'][0]['mcks_op_flow'], "unit" => ' L/min')),
    //     //         array("name" => "主曲軸襯銅(剪斷側)", "value" => array("value" => $test_main['QueryTableData'][0]['mcks_cus_flow'], "unit" => ' L/min'))
    //     //     ),
    //     //     '溫度' => array(
    //     //         array("name" => "主曲軸襯銅(操作側)", "value" => array("value" => $test_main['QueryTableData'][0]['mcks_op_temp'], "unit" => ' ℃')),
    //     //         array("name" => "主曲軸襯銅(剪斷側)", "value" => array("value" => $test_main['QueryTableData'][0]['mcks_cus_temp'], "unit" => ' ℃')),
    //     //         array("name" => "主滑台鋼板(操作側)", "value" => array("value" => $test_main['QueryTableData'][0]['msld_op_temp'], "unit" => ' ℃')),
    //     //         array("name" => "主滑台鋼板(剪斷側)", "value" => array("value" => $test_main['QueryTableData'][0]['msld_cus_temp'], "unit" => ' ℃'))
    //     //     ),
    //     //     '空壓' => array(
    //     //         array("name" => "三點組合", "value" => array("value" => $test_main['QueryTableData'][0]['tri_comb_aprs'], "unit" => ' Kg/cm2'))
    //     //     ),
    //     //     '位置感測' => array(
    //     //         array("name" => "離合器剎車距離", "value" => array("value" => $test_main['QueryTableData'][0]['clut_brk_cnt'], "unit" => ''))
    //     //     ),
    //     //     '油溫' => array(
    //     //         array("name" => "副油箱", "value" => array("value" => $test_main['QueryTableData'][0]['stnk_lub_temp'], "unit" => ' ℃'))
    //     //     ),
    //     // );
    //     $temp_data = array(
    //         array("name" => "主曲軸襯銅(操作側)", "stand" => "<60", "value" => array("value" => $test_main['QueryTableData'][0]['mcks_op_temp'], "unit" => ' ℃')),
    //         array("name" => "主曲軸襯銅(剪斷側)", "stand" => "<60", "value" => array("value" => $test_main['QueryTableData'][0]['mcks_cus_temp'], "unit" => ' ℃')),
    //         array("name" => "主滑台鋼板(操作側)", "stand" => "<60", "value" => array("value" => $test_main['QueryTableData'][0]['msld_op_temp'], "unit" => ' ℃')),
    //         array("name" => "主滑台鋼板(剪斷側)", "stand" => "<60", "value" => array("value" => $test_main['QueryTableData'][0]['msld_cus_temp'], "unit" => ' ℃'))
    //     );
    //     $flow_data = array(
    //         array("name" => "主滑台(操作側)", "stand" => ">1", "value" => array("value" => $test_main['QueryTableData'][0]['msld_op_flow'], "unit" => ' L/min')),
    //         array("name" => "主滑台(剪斷側)", "stand" => ">1", "value" => array("value" => $test_main['QueryTableData'][0]['msld_cus_flow'], "unit" => ' L/min')),
    //         array("name" => "主曲軸襯銅(操作側)", "stand" => ">0.1", "value" => array("value" => $test_main['QueryTableData'][0]['mcks_op_flow'], "unit" => ' L/min')),
    //         array("name" => "主曲軸襯銅(剪斷側)", "stand" => ">0.1", "value" => array("value" => $test_main['QueryTableData'][0]['mcks_cus_flow'], "unit" => ' L/min'))
    //     );
    //     $oilPressure_data = array(
    //         array("name" => "主油管迴路(主滑台側)", "stand" => ">5", "value" => array("value" => $test_main['QueryTableData'][0]['msld_lup_out_oprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "主油管迴路(剪斷側)", "stand" => ">5", "value" => array("value" => $test_main['QueryTableData'][0]['cus_lup_out_oprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "油管末端(主滑台側)", "stand" => ">1", "value" => array("value" => $test_main['QueryTableData'][0]['msld_end_oprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "油管末端(剪斷側)", "stand" => ">1", "value" => array("value" => $test_main['QueryTableData'][0]['cus_end_oprs'], "unit" => ' Kg/cm2'))
    //     );
    //     $airPressure1_data = array(
    //         array("name" => "三點組合", "stand" => ">5.5", "value" => array("value" => $test_main['QueryTableData'][0]['tri_comb_aprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "前沖安全銷", "stand" => ">5.0", "value" => array("value" => $test_main['QueryTableData'][0]['pko_sfpin_aprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "夾仔空壓彈簧", "stand" => "無", "value" => array("value" => $test_main['QueryTableData'][0]['clp_spr_aprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "後通安全銷", "stand" => ">3.5", "value" => array("value" => $test_main['QueryTableData'][0]['dko_sfpin_aprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "整頭", "stand" => "無", "value" => array("value" => $test_main['QueryTableData'][0]['trim_aprs'], "unit" => ' Kg/cm2'))
    //     );
    //     $airPressure2_data = array(
    //         array("name" => "送料輪", "stand" => "無", "value" => array("value" => $test_main['QueryTableData'][0]['fedw_aprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "冷卻油管", "stand" => ">2", "value" => array("value" => $test_main['QueryTableData'][0]['clg_aprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "後牙管", "stand" => "無", "value" => array("value" => $test_main['QueryTableData'][0]['postub_aprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "三軸夾爪", "stand" => ">5", "value" => array("value" => $test_main['QueryTableData'][0]['trx_jaw_aprs'], "unit" => ' Kg/cm2')),
    //         array("name" => "夾仔", "stand" => ">1", "value" => array("value" => $test_main['QueryTableData'][0]['clp_aprs'], "unit" => ' Kg/cm2'))
    //     );
    //     $oilTemp_data = array(
    //         array("name" => "副油箱", "stand" => "<50", "value" => array("value" => $test_main['QueryTableData'][0]['stnk_lub_temp'], "unit" => ' ℃'))
    //     );
    //     $clutch_data = array(
    //         array("name" => "煞車計數", "stand" => "<30", "value" => array("value" => $test_main['QueryTableData'][0]['clut_brk_cnt'], "unit" => ''))
    //     );
    //     $oilLevel_data = array(
    //         array("value" => array("value" => $test_main['QueryTableData'][0]['aut_level'] == 0 ? '油量充足' : $test_main['QueryTableData'][0]['aut_level'] == 1 ? '油量正常' : $test_main['QueryTableData'][0]['aut_level'] == 2 ? '油量不足警告' : $test_main['QueryTableData'][0]['aut_level'] == 3 ? '油量不足' : '--' , "unit" => ''))
    //     );
    
    //     // $test_main_data = array(
    //     //   "上傳時間" => array("value" => $test_main['QueryTableData'][0]['upload_at'], "unit" => ''),
    //     //   "主曲軸操作側溫度" => array("value" => $test_main['QueryTableData'][0]['mcks_op_temp'], "stand" => "<60", "unit" => ' ℃'),
    //     //   "主曲軸剪斷側溫度" => array("value" => $test_main['QueryTableData'][0]['mcks_cus_temp'], "stand" => "<60", "unit" => ' ℃'),
    //     //   "主滑台操作側溫度" => array("value" => $test_main['QueryTableData'][0]['msld_op_temp'], "stand" => "<60", "unit" => ' ℃'),
    //     //   "主滑台剪斷側溫度" => array("value" => $test_main['QueryTableData'][0]['msld_cus_temp'], "stand" => "<60", "unit" => ' ℃'),
    //     //   "副油箱潤滑油溫度" => array("value" => $test_main['QueryTableData'][0]['stnk_lub_temp'], "stand" => "<50", "unit" => ' ℃'),
    //     //   "主曲軸操作側流量" => array("value" => $test_main['QueryTableData'][0]['mcks_op_flow'], "stand" => ">0.1", "unit" => ' L/min'),
    //     //   "主曲軸剪斷側流量" => array("value" => $test_main['QueryTableData'][0]['mcks_cus_flow'], "stand" => ">0.1", "unit" => ' L/min'),
    //     //   "主滑台操作側流量" => array("value" => $test_main['QueryTableData'][0]['msld_op_flow'], "stand" => ">1", "unit" => ' L/min'),
    //     //   "主滑台剪斷側目前流量" => array("value" => $test_main['QueryTableData'][0]['msld_cus_flow'], "stand" => ">1", "unit" => ' L/min'),
    //     //   "剪斷側潤滑泵輸出油壓" => array("value" => $test_main['QueryTableData'][0]['cus_lup_out_oprs'], "stand" => ">5", "unit" => ' Kg/cm2'),
    //     //   "主滑台潤滑泵輸出油壓" => array("value" => $test_main['QueryTableData'][0]['msld_lup_out_oprs'], "stand" => ">5", "unit" => ' Kg/cm2'),
    //     //   "剪斷側油壓末端油壓" => array("value" => $test_main['QueryTableData'][0]['cus_end_oprs'], "stand" => ">1", "unit" => ' Kg/cm2'),
    //     //   "主滑台油壓末端油壓" => array("value" => $test_main['QueryTableData'][0]['msld_end_oprs'], "stand" => ">1", "unit" => ' Kg/cm2'),
    //     //   "三點組合空壓" => array("value" => $test_main['QueryTableData'][0]['tri_comb_aprs'], "stand" => ">5.5", "unit" => ' Kg/cm2'),
    //     //   "前沖安全銷空壓" => array("value" => $test_main['QueryTableData'][0]['pko_sfpin_aprs'], "stand" => ">5.0", "unit" => ' Kg/cm2'),
    //     //   "夾仔額定空壓" => array("value" => $test_main['QueryTableData'][0]['clp_spr_aprs'], "stand" => "無", "unit" => ' Kg/cm2'),
    //     //   "後通安全銷空壓" => array("value" => $test_main['QueryTableData'][0]['dko_sfpin_aprs'], "stand" => ">3.5", "unit" => ' Kg/cm2'),
    //     //   "整頭空壓" => array("value" => $test_main['QueryTableData'][0]['trim_aprs'], "stand" => "無", "unit" => ' Kg/cm2'),
    //     //   "送料輪空壓" => array("value" => $test_main['QueryTableData'][0]['fedw_aprs'], "stand" => "無", "unit" => ' Kg/cm2'),
    //     //   "冷卻油管空壓" => array("value" => $test_main['QueryTableData'][0]['clg_aprs'], "stand" => ">2", "unit" => ' Kg/cm2'),
    //     //   "後牙管空壓" => array("value" => $test_main['QueryTableData'][0]['postub_aprs'], "stand" => "無", "unit" => ' Kg/cm2'),
    //     //   "三軸夾爪空壓" => array("value" => $test_main['QueryTableData'][0]['trx_jaw_aprs'], "stand" => ">5", "unit" => ' Kg/cm2'),
    //     //   "夾仔實際空壓" => array("value" => $test_main['QueryTableData'][0]['clp_aprs'], "stand" => ">1", "unit" => ' Kg/cm2'),
    //     //   "機器轉速" => array("value" => $test_main['QueryTableData'][0]['mcg_revsp'], "stand" => "無", "unit" => ' RPM'),
    //     //   "副油箱油位" => array("value" => $test_main['QueryTableData'][0]['aut_level'], "stand" => "<2", "unit" => '' ),
    //     //   "離合器煞車計數" => array("value" => $test_main['QueryTableData'][0]['clut_brk_cnt'], "stand" => "<30", "unit" => '' ),
    //     //   "計數器1累計值" => array("value" => $test_main['QueryTableData'][0]['cnt1'], "stand" => "無", "unit" => '' ),
    //     //   "計數器2累計值" => array("value" => $test_main['QueryTableData'][0]['cnt2'], "stand" => "無", "unit" => '' )
    //     // );

    //     $rpm = $test_main['QueryTableData'][0]['mcg_revsp'];
    //     $device_status = $test_main['QueryTableData'][0]['opr'];
    //     if ($test_main['QueryTableData'][0]['opr'] == 0) {
    //         $device_status = "待機";
    //     } else if ($test_main['QueryTableData'][0]['opr'] == 1) {
    //         $device_status = "運作";
    //     } else if ($test_main['QueryTableData'][0]['opr'] == -1) {
    //         $device_status = "異常";
    //     }

    //     $test_light_data = array(
    //         "lub_press_slder" => array(
    //             "color" => $test_main['QueryTableData'][0]['lub_press_slder'] == 1 ? 'green' : 'red',
    //             "name" => "潤滑油壓異常(主滑台側)"
    //         ),
    //         "abn_pneu_press" => array(
    //             "color" => $test_main['QueryTableData'][0]['abn_pneu_press'] == 1 ? 'green' : 'red',
    //             "name" => "氣壓異常"
    //         ),
    //         "overload" => array(
    //             "color" => $test_main['QueryTableData'][0]['overload'] == 1 ? 'red' : 'gray',
    //             "name" => "電流過負荷"
    //         ),
    //         "abn_driver" => array(
    //             "color" => $test_main['QueryTableData'][0]['abn_driver'] == 1 ? 'red' : 'green',
    //             "name" => "伺服驅動器異常"
    //         ),
    //         "pko_sfbolt" => array(
    //             "color" => $test_main['QueryTableData'][0]['pko_sfbolt'] == 1 ? 'red' : 'gray',
    //             "name" => "前沖安全銷"
    //         ),
    //         "ko_sfbolt" => array(
    //             "color" => $test_main['QueryTableData'][0]['ko_sfbolt'] == 1 ? 'red' : 'gray',
    //             "name" => "後沖安全銷"
    //         ),
    //         "lub_press_cutoff" => array(
    //             "color" => $test_main['QueryTableData'][0]['lub_press_cutoff'] == 1 ? 'green' : 'red',
    //             "name" => "潤滑油壓異常(剪斷側)" 
    //         ),
    //         "prob_tran_float" => array(
    //             "color" => $test_main['QueryTableData'][0]['prob_tran_float'] == 1 ? 'red' : 'gray',
    //             "name" => "挟台浮動"
    //         ),
    //         "prob_sf_door" => array(
    //             "color" => $test_main['QueryTableData'][0]['prob_sf_door'] == 1 ? 'red' : 'gray',
    //             "name" => "安全門開"
    //         ),
    //         "lub_overflow" => array(
    //             "color" => $test_main['QueryTableData'][0]['lub_overflow'] == 1 ? 'red' : 'gray',
    //             "name" => "潤滑油異位"
    //         ),
    //         "abn_temp_slder" => array(
    //             "color" => $test_main['QueryTableData'][0]['abn_temp_slder'] == 1 ? 'red' : 'gray',
    //             "name" => "溫度異常"
    //         ),
    //         "abn_lub_flow" => array(
    //             "color" => $test_main['QueryTableData'][0]['abn_lub_flow'] == 1 ? 'red' : 'gray',
    //             "name" => "潤滑油量異常"
    //         ),
    //         "abn_brk_pla_clut" => array(
    //             "color" => $test_main['QueryTableData'][0]['abn_brk_pla_clut'] == 1 ? 'red' : 'gray',
    //             "name" => "離合器煞車距離異常"
    //         ),
    //         "hyd_press_rscrpi_lck" => array(
    //             "color" => $test_main['QueryTableData'][0]['hyd_press_rscrpi_lck'] == 1 ? 'red' : 'gray',
    //             "name" => "鎖後牙管油壓不足"
    //         ),
    //         "abn_forg" => array(
    //             "color" => $test_main['QueryTableData'][0]['abn_forg'] == 1 ? 'red' : 'gray',
    //             "name" => "BRANKAMP壓造異常"
    //         ),
    //         "sh_feed" => array(
    //             "color" => $test_main['QueryTableData'][0]['sh_feed'] == 1 ? 'red' : 'gray',
    //             "name" => "材料短送"
    //         ),
    //         "finish_cnt" => array(
    //             "color" => $test_main['QueryTableData'][0]['finish_cnt'] == 1 ? 'red' : 'gray',
    //             "name" => "計數終了"
    //         ),
    //         "mtr_end" => array(
    //             "color" => $test_main['QueryTableData'][0]['mtr_end'] == 1 ? 'red' : 'gray',
    //             "name" => "材料終了"
    //         ),
    //         "hyd_press_grpse_lck" => array(
    //             "color" => $test_main['QueryTableData'][0]['hyd_press_grpse_lck'] == 1 ? 'red' : 'gray',
    //             "name" => "鎖挟台油壓不足"
    //         ),
    //         "oper_door" => array(
    //             "color" => $test_main['QueryTableData'][0]['oper_door'] == 1 ? 'red' : 'gray',
    //             "name" => "操作門開"
    //         ),
    //         "die_blkwed_hyd_oil_press" => array(
    //             "color" => $test_main['QueryTableData'][0]['die_blkwed_hyd_oil_press'] == 1 ? 'red' : 'gray',
    //             "name" => "模座契形塊油壓不足"
    //         ),
    //         "die_blkcyl_hyd_oil_press" => array(
    //             "color" => $test_main['QueryTableData'][0]['die_blkcyl_hyd_oil_press'] == 1 ? 'red' : 'gray',
    //             "name" => "模座油壓缸油壓不足"
    //         ),
    //         "pun_blkcyl_hyd_oil_press" => array(
    //             "color" => $test_main['QueryTableData'][0]['pun_blkcyl_hyd_oil_press'] == 1 ? 'red' : 'gray',
    //             "name" => "沖具座油壓缸油壓不足"
    //         ),
    //         "oil_level_low" => array(
    //             "color" => $test_main['QueryTableData'][0]['oil_level_low'] == 1 ? 'red' : 'gray',
    //             "name" => "副油箱油量不足" 
    //         ),
    //         "mat_wgt" => array(
    //             "color" => $test_main['QueryTableData'][0]['mat_wgt'] == 1 ? 'red' : 'gray',
    //             "name" => "材料秤重到達設定值"
    //         ),
    //         "sf_window" => array(
    //             "color" => $test_main['QueryTableData'][0]['sf_window'] == 1 ? 'gray' : 'red',
    //             "name" => "安全視窗開"
    //         ),
    //         "motor_brk" => array(
    //             "color" => $test_main['QueryTableData'][0]['motor_brk'] == 1 ? 'red' : 'green',
    //             "name" => "馬達繼電器異常"
    //         )
    //     );

    //     // foreach ($test_main_data as $key => $value) {
    //     //     array_push($return_main_data, array(
    //     //       "name" => $key,
    //     //       "value" => $value
    //     //     ));
    //     // }
    //     $return_main_timer = date("Y-m-d H:i:s" ,strtotime($test_main['QueryTableData'][0]['upload_at']));
    //     $return_light_data = $test_light_data;
    //     $return_light_timer = date("Y-m-d H:i:s" ,strtotime($test_main['QueryTableData'][0]['upload_at']));
    // }
}
    $data = array('querySingle' => 'Last');
    $teble = 'test_emeter';
    $test_emeter = SensorSingleRowQuery($data, 'PostgreSQL', $teble);
    $return_emeter_data = array();
    $return_Emeter_timer = "";
    if (!empty($test_emeter['QueryTableData'])) {
        // $test_emeter_data = array(
        //     // "上傳時間" => array("value" => $test_emeter['QueryTableData'][0]['upload_at'], "unit" => ''),
        //     "當前電流值" => array("value" => $test_emeter['QueryTableData'][0]['current'], "unit" => ' A'),
        //     // "電流不平衡率" => array("value" => $test_emeter['QueryTableData'][0]['cur_cubr'], "unit" => ' %'),
        //     // "電流諧波失真率" => array("value" => $test_emeter['QueryTableData'][0]['cur_chdr'], "unit" => ' %'),
        //     "當前電壓值" => array("value" => $test_emeter['QueryTableData'][0]['voltage'], "unit" => ' V'),
        //     "電壓不平衡率" => array("value" => $test_emeter['QueryTableData'][0]['volt_vubr'], "unit" => ' %'),
        //     // "電壓諧波失真率" => array("value" => $test_emeter['QueryTableData'][0]['volt_chdr'], "unit" => ' %'),
        //     // "頻率" => array("value" => $test_emeter['QueryTableData'][0]['frequency'], "unit" => ' Hz'),
        //     // "總功率因數" => array("value" => $test_emeter['QueryTableData'][0]['TPF'], "unit" => ' 無因次'),
        //     "實功電能" => array("value" => $test_emeter['QueryTableData'][0]['RELEC'], "unit" => ' kWh'),
        //     // "實功功率需量 - 當前" => array("value" => $test_emeter['QueryTableData'][0]['RPWD_cur'], "unit" => ' kW'),
        //     // "實功功率需量 - 預測" => array("value" => $test_emeter['QueryTableData'][0]['RPWD_pred'], "unit" => ' kW'),
        //     // "實功功率需量 - 峰值" => array("value" => $test_emeter['QueryTableData'][0]['RPWD_peak'], "unit" => ' kW'),
        // );
        // foreach ($test_emeter_data as $key => $value) {
        //     array_push($return_emeter_data, array(
        //         "name" => $key,
        //         "value" => $value
        //     ));
        // }
        // $return_Emeter_timer = date("Y-m-d H:i:s" ,strtotime($test_emeter['QueryTableData'][0]['upload_at']));

        $emeter_data = array(
            "current" => array("value" => $test_emeter['QueryTableData'][0]['current'], "unit" => ' A'),
            "voltage" => array("value" => $test_emeter['QueryTableData'][0]['voltage'], "unit" => ' V'),
            "volt_vubr" => array("value" => $test_emeter['QueryTableData'][0]['volt_vubr'], "unit" => ' %'),
            "RELEC" => array("value" => $test_emeter['QueryTableData'][0]['RELEC'], "unit" => ' kWh')
        );
    }
    $teble = 'test_servoD';
    $test_servoD = SensorSingleRowQuery($data, 'PostgreSQL', $teble);
    $return_servoD_data = '';
    $return_servoD_timer = '';
    if (!empty($test_servoD['QueryTableData'])) {
        $return_servoD_data = $test_servoD['QueryTableData'][0]['err_code']['servoD'];
        $return_servoD_timer = date("Y-m-d H:i:s" ,strtotime($test_servoD['QueryTableData'][0]['err_code']['timestamp']));
    }
    // $serverDriver_data = array(
    //     array("value" => array("value" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 0 ? '正常' : ($test_servoD['QueryTableData'][0]['err_code']['servoD'] == 1 ? '回授信號異常' : ($test_servoD['QueryTableData'][0]['err_code']['servoD'] == 3 ? '過電流' : ($test_servoD['QueryTableData'][0]['err_code']['servoD'] == 4 ? '過熱' : ($test_servoD['QueryTableData'][0]['err_code']['servoD'] == 5 ? '電壓過高' : ($test_servoD['QueryTableData'][0]['err_code']['servoD'] == 6 ? '電壓過低' : ($test_servoD['QueryTableData'][0]['err_code']['servoD'] == 7 ? '過負載' : ($test_servoD['QueryTableData'][0]['err_code']['servoD'] == 9 ? '程式錯誤' : '--'))))))) , "unit" => ''))
    // );
    // $serverDriver_data = array(
    //     array("value" => array("value" => '正常', "unit" => ''), "color" => "green", "isSelect" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 0 ? true : false),
    //     array("value" => array("value" => '回授信號異常', "unit" => ''), "color" => "red", "isSelect" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 1 ? true : false),
    //     array("value" => array("value" => '過電流', "unit" => ''), "color" => "red", "isSelect" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 3 ? true : false),
    //     array("value" => array("value" => '過熱', "unit" => ''), "color" => "red", "isSelect" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 4 ? true : false),
    //     array("value" => array("value" => '電壓過高', "unit" => ''), "color" => "red", "isSelect" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 5 ? true : false),
    //     array("value" => array("value" => '電壓過低', "unit" => ''), "color" => "red", "isSelect" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 6 ? true : false),
    //     array("value" => array("value" => '過負載', "unit" => ''), "color" => "red", "isSelect" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 7 ? true : false),
    //     array("value" => array("value" => '程式錯誤', "unit" => ''), "color" => "red", "isSelect" => $test_servoD['QueryTableData'][0]['err_code']['servoD'] == 9 ? true : false)
    // );
    $serverDriver_data = $test_servoD['QueryTableData'][0]['err_code']['servoD'];
    $teble = 'test_vibBearing';
    $test_vibBearing = SensorSingleRowQuery($data, 'PostgreSQL', $teble);
    $return_vibBearing_data = array();
    if (!empty($test_vibBearing['QueryTableData'])) {
        // $test_vibBearing_data = array(
        //     "主馬達上傳時間" => array("value" => $test_vibBearing['QueryTableData'][0]['upload_at'], "unit" => ''),
        //     "主馬達FFT" => array("value" => $test_vibBearing['QueryTableData'][0]['FFT'], "unit" => ''),
        //     "主馬達振動速度－X軸" => array("value" => $test_vibBearing['QueryTableData'][0]['vibspeed_x'], "unit" => ' mm/s'),
        //     "主馬達振動速度－Y軸" => array("value" => $test_vibBearing['QueryTableData'][0]['vibspeed_y'], "unit" => ' mm/s'),
        //     "主馬達振動速度－Z軸" => array("value" => $test_vibBearing['QueryTableData'][0]['vibspeed_z'], "unit" => ' mm/s'),
        //     "主馬達FFT讀取設定" => array("value" => $test_vibBearing['QueryTableData'][0]['FFT_config'], "unit" => '')
        // );
        // foreach ($test_vibBearing_data as $key => $value) {
        //     array_push($return_vibBearing_data, array(
        //         "name" => $key,
        //         "value" => $value
        //     ));
        // }
        $vibBearing_data = array(
            array("name" => "X軸", "stand" => array("value" => 3.5, "symbols" => "<"), "value" => array("value" => $test_vibBearing['QueryTableData'][0]['vibspeed_x'], "unit" => ' mm/s')),
            array("name" => "Y軸", "stand" => array("value" => 3.5, "symbols" => "<"), "value" => array("value" => $test_vibBearing['QueryTableData'][0]['vibspeed_y'], "unit" => ' mm/s')),
            array("name" => "Z軸", "stand" => array("value" => 2.8, "symbols" => "<"), "value" => array("value" => $test_vibBearing['QueryTableData'][0]['vibspeed_z'], "unit" => ' mm/s'))
        );
    }
    $teble = 'test_vibMotor';
    $test_vibMotor = SensorSingleRowQuery($data, 'PostgreSQL', $teble);
    $return_vibMotor_data = array();
    if (!empty($test_vibMotor['QueryTableData'])) {
        // $test_vibMotor_data = array(
        //     "連座軸承上傳時間" => array("value" => $test_vibMotor['QueryTableData'][0]['upload_at'], "unit" => ''),
        //     "連座軸承FFT" => array("value" => $test_vibMotor['QueryTableData'][0]['FFT'], "unit" => ''),
        //     "連座軸承振動速度－X軸" => array("value" => $test_vibMotor['QueryTableData'][0]['vibspeed_x'], "unit" => ' mm/s'),
        //     "連座軸承振動速度－Y軸" => array("value" => $test_vibMotor['QueryTableData'][0]['vibspeed_y'], "unit" => ' mm/s'),
        //     "連座軸承振動速度－Z軸" => array("value" => $test_vibMotor['QueryTableData'][0]['vibspeed_z'], "unit" => ' mm/s'),
        //     "連座軸承FFT讀取設定" => array("value" => $test_vibMotor['QueryTableData'][0]['FFT_config'], "unit" => ''),
        // );
        // foreach ($test_vibMotor_data as $key => $value) {
        //     array_push($return_vibMotor_data, array(
        //         "name" => $key,
        //         "value" => $value
        //     ));
        // }
        $vibMotor_data = array(
            array("name" => "X軸", "stand" => array("value" => 10, "symbols" => "<"), "value" => array("value" => $test_vibMotor['QueryTableData'][0]['vibspeed_x'], "unit" => ' mm/s')),
            array("name" => "Y軸", "stand" => array("value" => 10, "symbols" => "<"), "value" => array("value" => $test_vibMotor['QueryTableData'][0]['vibspeed_y'], "unit" => ' mm/s')),
            array("name" => "Z軸", "stand" => array("value" => 10, "symbols" => "<"), "value" => array("value" => $test_vibMotor['QueryTableData'][0]['vibspeed_z'], "unit" => ' mm/s'))
        );
    }
    $teble = 'test_loadCell';
    $test_loadCell = SensorSingleRowQuery($data, 'PostgreSQL', $teble);
    $return_loadCell_data = array();
    $loadCell_data = "";
    if (!empty($test_loadCell['QueryTableData'])) {
        $test_loadCell_data = array(
            "上傳時間" => array("value" => $test_loadCell['QueryTableData'][0]['upload_at'], "unit" => ''),
            "線材重量" => array("value" => $test_loadCell['QueryTableData'][0]['wire_weight'], "unit" => 'kg')
        );
        foreach ($test_loadCell_data as $key => $value) {
            array_push($return_loadCell_data, array(
                "name" => $key,
                "value" => $value
            ));
        }
        $loadCell_data = $test_loadCell['QueryTableData'][0]['wire_weight'];
    }
    
    $test['sensor'] = array(
        "temp_data" => $temp_data,
        "flow_data" => $flow_data,
        "oilPressure_data" => $oilPressure_data,
        "airPressure1_data" => $airPressure1_data,
        "airPressure2_data" => $airPressure2_data,
        "oilTemp_data" => $oilTemp_data,
        "clutch_data" => $clutch_data,
        "oilLevel_data" => $oilLevel_data,
        "serverDriver_data" => $serverDriver_data,

        "vibBearing_data" => $vibBearing_data,
        "vibMotor_data" => $vibMotor_data,

        "emeter_data" => $emeter_data,

        "RELEC_chart" => $RELEC_chart,

        "loadCell_data" => $loadCell_data,
        // "rpm" => $rpm,
        // "device_status" => $device_status,

        "device_model" => $device_model,
        "machine_status" => $machine_status,
        "device_run_time" => $device_run_time,
        "device_activation" => $device_activation . '%',
        "device_day_count" => $device_day_count,
        "device_now_count" => $device_now_count,
        "device_capacity_min" => $device_capacity_min,
        "stack_bar_information" => $stack_bar_information,
        "operatelog_information" => $operatelog_information,
        "alarmHistory_information" => $alarmHistory_information,
        "machine_light_value" => $machine_light_value,

        
        // "emeter_data" => $return_emeter_data,
        // "main_data" => $return_main_data,
        // "light_data" => $return_light_data,
        // "vibration_data" => array_merge($return_vibBearing_data, $return_vibMotor_data),
        // "loadCell_data" => $return_loadCell_data,
        // "servoD_data" => $return_servoD_data,
        // "main_timer" => $return_main_timer,
        // "light_timer" => $return_light_timer,
        // "Emeter_timer" => $return_Emeter_timer,
        // "servoD_timer" => $return_servoD_timer,
        // "health_data" => $return_health_data
    );

return $test;
    //回傳的資料
    $returnData['QueryTableData'] = [];

    //判斷製程
    $process = array(
        '打頭' => 5,
        '輾牙' => 6,
        '熱處理' => 7
    );
    $this_process = $process[$params->process];

    if ($this_process == 5) {
        $returnData = get_heading_machine_data($params);
    } else if ($this_process == 6) {
        $returnData = get_thd_machine_data($params);
    }

    return $returnData;
}

function get_heading_machine_data($params){
    //回傳的資料
    $returnData['QueryTableData'] = [];

    //現在的時間
    $now_time = strtotime(date("Y-m-d H:i:s"));
    
    // 查詢machine_status，機台當前狀態
    // 機台名稱、目前工單編號、是否有首件檢查1=有，機台運作狀態、機台狀態Q=非故障
    $fields = ['device_name', 'work_code', 'status', 'first_inspection', 'machine_detail', 'mac_status'];
    $data = array(
        'condition_1' => array(
            'fields' => $fields,
            'limit' => ["ALL"],
            'table' => 'machine_status_head'
        )
    );
    $machine_status_head = CommonSqlSyntax_Query($data, "MySQL");
    if ($machine_status_head['Response'] !== 'ok') {
        return $machine_status_head;
    } else if (count($machine_status_head['QueryTableData']) == 0) {
        $machine_status_head['Response'] = "no data";
        return $machine_status_head;
    }
    $machine_status_data = $machine_status_head['QueryTableData'];

    //取得所有機台編號
    $device_array = get_device_array($machine_status_data);

    //查詢machine_abn，機台異常資料
    $machine_abn = CommonTableQuery('MySQL', 'machine_abn');
    if ($machine_abn['Response'] !== 'ok') {
        return $machine_abn;
    } else if (count($machine_abn['QueryTableData']) == 0) {
        $machine_abn['Response'] = "no data";
        return $machine_abn;
    }
    $machine_abn_data = $machine_abn['QueryTableData'];
    $machine_abn_code = [];
    for ($i=0; $i < count($machine_abn_data); $i++) { 
        $machine_abn_code[$machine_abn_data[$i]['name']] = array(
            'err_code' => $machine_abn_data[$i]['err_code'],
            'value' => $machine_abn_data[$i]['value']
        );
    }

    //查詢機台今日工單
    $intervaltime = new stdClass();
    $intervaltime->sch_prod_date = array([date("Y-m-d 00:00:00"), date("Y-m-d 23:59:59")]);
    $whereAttr = new stdClass();
    $whereAttr->device_name = $device_array;
    $symbols = new stdClass();
    $symbols->device_name = array_map('symbols_equal', $device_array);
    $data = array(
        'condition_1' => array(
            'table' => 'work_order',
            'intervaltime' => $intervaltime,
            'where' => $whereAttr,
            'limit' => ['ALL'],
            'symbols' => $symbols
        )
    );
    $work_order = CommonSqlSyntax_Query($data, 'MsSQL');
    if ($work_order['Response'] !== 'ok') {
    } else if (count($work_order['QueryTableData']) == 0) {
        $work_order['Response'] = "no data";
    }
    $work_order_data = $work_order['QueryTableData'];

    $device_work_order = array();
    //將今日打頭的工單存取
    foreach ($work_order_data as $key => $value) {
        if (!isset($device_work_order[$value['device_name']])) {
            $device_work_order[$value['device_name']] = [];
        }
        array_push($device_work_order[$value['device_name']], $value);
    }

    // 查詢機台機型
    $whereAttr = new stdClass();
    $whereAttr->name = $device_array;
    $symbols = new stdClass();
    $symbols->name = array_map('symbols_equal', $device_array);
    $data = array(
        'condition_1' => array(
            'table' => 'device_box',
            'where' => $whereAttr,
            'limit' => ['ALL'],
            'symbols' => $symbols
        )
    );
    $device_box = CommonSqlSyntax_Query($data, "MsSQL");
    if ($device_box['Response'] !== 'ok' || count($device_box['QueryTableData']) == 0) {
        $device_box_data = [];
    } else {
        $device_box_data = $device_box['QueryTableData'];
    }

    $device_device_box = array();
    $device_model = array();
    foreach ($device_box_data as $key => $value) {
        if (!isset($device_device_box[$value['name']])) {
            $device_device_box[$value['name']] = array();
        }
        $device_device_box[$value['name']] = $value;
        array_push($device_model, $value['model']);
    }

    if (!empty($device_model)) {
        //查詢machine_status_list，機台燈號
        $whereAttr = new stdClass();
        $whereAttr->model = $device_model;
        $symbols = new stdClass();
        $symbols->model = array_map('symbols_equal', $device_model);
        $data = array(
            'condition_1' => array(
                'table' => 'machine_status_list',
                'where' => $whereAttr,
                'limit' => ['ALL'],
                'symbols' => $symbols
            )
        );
        $machine_status_list = CommonSqlSyntax_Query($data, "MySQL");
        if ($machine_status_list['Response'] !== 'ok' || count($machine_status_list['QueryTableData']) == 0) {
            $machine_status_list_data = [];
        } else {
            $machine_status_list_data = $machine_status_list['QueryTableData'];
        }
        foreach ($device_device_box as $device_device_box_key => $device_device_box_value) {
           foreach ($machine_status_list_data as $machine_status_list_data_key => $machine_status_list_data_value) {
                if ($device_device_box_value['model'] == $machine_status_list_data_value['model']) {
                    if (is_string($machine_status_list_data_value['light_list'])) {
                        $machine_status_list_data_value['light_list'] = json_decode($machine_status_list_data_value['light_list'], true);
                    }
                    $device_device_box[$device_device_box_key]['light_list'] =  $machine_status_list_data_value['light_list'];
                }
           }
        }
    }

    //用該值來儲存資料
    $machine_device_data = $machine_status_data;

    foreach ($machine_device_data as $key => $value) {
        if (is_string($value['machine_detail'])) {
            $value['machine_detail'] = json_decode($value['machine_detail'], true);
        }
        //機台狀態
        if ($value['mac_status'] == 'Q') {
            if (gettype($value['machine_detail']) == 'array') {
                if ($now_time - 600 > strtotime($value['machine_detail']['timestamp'])) {
                    $machine_device_data[$key]['now_status'] = 'S';
                } else {
                    $machine_detail = $value['machine_detail'];
                    if ($machine_detail['OPR'] == 1) {
                        $machine_device_data[$key]['now_status'] = 'R';//運轉
                    } else {
                        $machine_device_data[$key]['now_status'] = 'Q';//閒置
                    }
                    if (isset($device_device_box[$value['device_name']])) {
                        if (isset($device_device_box[$value['device_name']]['light_list'])) {
                            foreach ($machine_abn_code as $machine_abn_code_key => $machine_abn_code_value) {
                                if (isset($device_device_box[$value['device_name']]['light_list'][$machine_abn_code_key])) {
                                    if ($machine_abn_code_value['value'] == $machine_detail[$machine_abn_code_key]) {
                                        if ($machine_abn_code_key == 'in_lube') {
                                            if ($machine_detail['OPR'] == 1) {
                                                $machine_device_data[$key]['now_status'] = 'H';
                                            break;
                                            }
                                        } else {
                                            $machine_device_data[$key]['now_status'] = 'H';
                                        break;
                                        }
                                    }
                                }
                            }
                        } else {
                            //沒有機型資料
                            $machine_device_data[$key]['now_status'] = 'H';
                        }
                    } else {
                        //沒有機台資料
                        $machine_device_data[$key]['now_status'] = 'H';
                    }
                }
            }
        } else if ($value['mac_status'] == 'H') {
            $machine_device_data[$key]['now_status'] = 'H';
        }

        //機台轉速
        if (isset($device_device_box[$value['device_name']])) {
            $machine_device_data[$key]['speed'] = round($device_device_box[$value['device_name']]['capacity_hr'] / 60, 0);
        }

        //目前支數
        if ($value['machine_detail'] != "") {
            if (isset($value['machine_detail']['cnt'])) {
                $machine_device_data[$key]['count'] = $value['machine_detail']['cnt'];
            }
        }

        $machine_device_data[$key]['day_work_order_count'] = 0;
        $machine_device_data[$key]['remain_work_order_count'] = 0;

        //今日工單總量、餘量
        if (!empty($device_work_order)) {
            if (isset($device_work_order[$value['device_name']])) {
                //今日工單總數量
                $machine_device_data[$key]['day_work_order_count'] = count($device_work_order[$value['device_name']]);
                $remain_work_order_count = 0;
                foreach ($device_work_order[$value['device_name']] as $device_work_order_key => $device_work_order_value) {
                    //若該工單尚未完成則加入工單餘量
                    if ($device_work_order_value['status'] == 0 || $device_work_order_value['status'] == 'S') {
                        $remain_work_order_count++;
                    }
                }
                $machine_device_data[$key]['remain_work_order_count'] = $remain_work_order_count;
                
                if (empty(array_search($value['work_code'], array_column($device_work_order[$value['device_name']], 'code')))) {
                    $machine_device_data[$key]['day_work_order_count']++;
                } else {
                    $work_order_data_position = array_search($value['work_code'], array_column($device_work_order[$value['device_name']], 'code'));
                    $machine_device_data[$key]['work_code_qty'] = $device_work_order[$value['device_name']][$work_order_data_position]['work_qty'];
                    $machine_device_data[$key]['remain_work_order_count']--;
                }
            } else {
                if ($value['work_code'] != "") {
                    $machine_device_data[$key]['day_work_order_count']++;
                }
            }
        } else {
            if ($value['work_code'] != "") {
                $machine_device_data[$key]['day_work_order_count']++;
            }
        }

        //正在做的工單進度
        if (!isset($machine_device_data[$key]['work_code_qty'])) {
            //查詢目前工單
            $whereAttr = new stdClass();
            $whereAttr->code = [$value['work_code']];
            $symbols = new stdClass();
            $symbols->code = ["equal"];
            $data = array(
                'condition_1' => array(
                    'table' => 'work_order',
                    'where' => $whereAttr,
                    'limit' => [0,1],
                    'symbols' => $symbols
                )
            );
            $this_work_order = CommonSqlSyntax_Query($data, 'MsSQL');
            if ($this_work_order['Response'] !== 'ok') {
                return $this_work_order;
            } else if (count($this_work_order['QueryTableData']) == 0) {
                $machine_device_data[$key]['work_code_qty'] = '--';
            } else {
                $this_work_order_data = $this_work_order['QueryTableData'];
                $machine_device_data[$key]['work_code_qty'] = $this_work_order_data[0]['work_qty'];
            }
        }

        //工單完成率
        if (isset($machine_device_data[$key]['count']) && isset($machine_device_data[$key]['work_code_qty']) && is_numeric($machine_device_data[$key]['work_code_qty'])) {
            $machine_device_data[$key]['work_code_complete'] = round(round($machine_device_data[$key]['count']/$machine_device_data[$key]['work_code_qty'], 2)*100, 2);
        }

        array_push($returnData['QueryTableData'], array(
            'product_line' => $params->group_name,//產線
            'device_name' => isset($machine_device_data[$key]['device_name']) ? $machine_device_data[$key]['device_name'] : '--',//機台名稱
            'now_status' => isset($machine_device_data[$key]['now_status']) ? $machine_device_data[$key]['now_status'] : '--',//狀態
            'speed' => isset($machine_device_data[$key]['speed']) ? $machine_device_data[$key]['speed'] : '--',//速度
            // 'count' => isset($machine_device_data[$key]['count']) ? $machine_device_data[$key]['count'] : '--',//目前支數
            'work_code_qty' => isset($machine_device_data[$key]['work_code_qty']) ? $machine_device_data[$key]['work_code_qty'] : '--',//目前工單需求支數
            // 'work_code_complete' => isset($machine_device_data[$key]['work_code_complete']) ? $machine_device_data[$key]['work_code_complete'] : '--',//當前工單完成率
            'workCode' => isset($machine_device_data[$key]['work_code']) ? $machine_device_data[$key]['work_code'] : '--',//當前工單
            'day_work_order_count' => isset($machine_device_data[$key]['day_work_order_count']) ? $machine_device_data[$key]['day_work_order_count'] : '--',//工單總量
            'work_order_count' => isset($machine_device_data[$key]['remain_work_order_count']) ? $machine_device_data[$key]['remain_work_order_count'] : '--',//工單餘量
        ));
    }

    $returnData['Response'] = 'ok';
    $returnData['page'] = 'elecboard';
    return $returnData;
}

function get_thd_machine_data($params){
    //回傳的資料
    $returnData['QueryTableData'] = [];

    //現在的時間
    $now_time = strtotime(date("Y-m-d H:i:s"));
    
    // 查詢machine_status，機台當前狀態
    // 機台名稱、目前工單編號、是否有首件檢查1=有，機台運作狀態、機台狀態Q=非故障
    $fields = ['device_name', 'work_code', 'status', 'first_inspection', 'machine_detail', 'mac_status'];
    $data = array(
        'condition_1' => array(
            'fields' => $fields,
            'limit' => ["ALL"],
            'table' => 'machine_status_thd'
        )
    );
    $machine_status_thd = CommonSqlSyntax_Query($data, "MySQL");
    if ($machine_status_thd['Response'] !== 'ok') {
        return $machine_status_thd;
    } else if (count($machine_status_thd['QueryTableData']) == 0) {
        $machine_status_thd['Response'] = "no data";
        return $machine_status_thd;
    }
    $machine_status_data = $machine_status_thd['QueryTableData'];

    //取得所有機台編號
    $device_array = get_device_array($machine_status_data);

    //開始時間
    if ($now_time >= strtotime(date("Y-m-d 08:00:00"))) {
        $startTime = date("Y-m-d 08:00:00");
    } else {
        $startTime = date("Y-m-d 08:00:00", strtotime(date("Y-m-d 08:00:00") - 86400));
    }

    //查詢工單上線數量
    $whereAttr = new stdClass();
    $whereAttr->device_name = $device_array;
    $symbols = new stdClass();
    $symbols->device_name = array_map('symbols_equal', $device_array);
    $intervaltime = array(
        'upload_at' => array(array($startTime, date("Y-m-d H:i:s", $now_time)))
    );
    $data = array(
        'condition_1' => array(
            'intervaltime' => $intervaltime,
            'fields' => ['upload_at', 'work_code', 'project_id', 'device_name', 'status'],
            'table' => 'work_code_use',
            'orderby' => ['asc','upload_at'],
            'limit' => ['ALL'],
            'symbols' => $symbols,
            'where' => $whereAttr,
        )
    );
    $work_code_use = CommonSqlSyntax_Query_v2_5($data, "PostgreSQL");
    if ($work_code_use['Response'] !== 'ok') {
        return $work_code_use;
    }
    $work_code_use_data = $work_code_use['QueryTableData'];

    $work_code_array = get_work_code_array($work_code_use_data);

    //查詢machine_abn，機台異常資料
    $machine_abn = CommonTableQuery('MySQL', 'machine_abn');
    if ($machine_abn['Response'] !== 'ok') {
        return $machine_abn;
    } else if (count($machine_abn['QueryTableData']) == 0) {
        $machine_abn['Response'] = "no data";
        return $machine_abn;
    }
    $machine_abn_data = $machine_abn['QueryTableData'];
    $machine_abn_code = [];
    for ($i=0; $i < count($machine_abn_data); $i++) { 
        $machine_abn_code[$machine_abn_data[$i]['name']] = array(
            'err_code' => $machine_abn_data[$i]['err_code'],
            'value' => $machine_abn_data[$i]['value']
        );
    }

    if (!empty($work_code_array)) {
        //查詢機台今日工單
        $whereAttr = new stdClass();
        $whereAttr->code = $work_code_array;
        $symbols = new stdClass();
        $symbols->code = array_map('symbols_equal', $work_code_array);
        $data = array(
            'condition_1' => array(
                'table' => 'work_order',
                'where' => $whereAttr,
                'limit' => ['ALL'],
                'symbols' => $symbols
            )
        );
        $work_order = CommonSqlSyntax_Query($data, 'MsSQL');
        if ($work_order['Response'] !== 'ok') {
        } else if (count($work_order['QueryTableData']) == 0) {
            $work_order['Response'] = "no data";
        }
        $work_order_data = $work_order['QueryTableData'];
    }

    $device_work_order = array();
    $device_work_time = array();//機台工單上線時間
    //將今日輾牙的工單存取
    foreach ($work_code_use_data as $key => $value) {
        if (!isset($device_work_order[$value['device_name']])) {
            $device_work_order[$value['device_name']] = [];
        }
        //如果已經有同一張工單的紀錄，則記錄他最新的狀態
        if (empty(array_search($value['work_code'], array_column($device_work_order[$value['device_name']], 'code')))) {
            array_push($device_work_order[$value['device_name']], $value);
        } else {
            $work_order_data_position = array_search($value['work_code'], array_column($device_work_order[$value['device_name']], 'code'));
            $device_work_order[$value['device_name']][$work_order_data_position]['status'] = $value['status'];
        }
        if (!isset($device_work_time[$value['device_name']])) {
            $device_work_time[$value['device_name']] = [];
        }
        if ($value['status'] == 'S') {
            if (empty($device_work_time[$value['device_name']])) {
                $device_work_time[$value['device_name']] = $value['upload_at'];
            } else {
                if (strtotime($device_work_time[$value['device_name']]) <= strtotime($value['upload_at'])) {
                    $device_work_time[$value['device_name']] = $value['upload_at'];
                }
            }
        } else if ($value['status'] == 'E') {
            if (!empty($device_work_time[$value['device_name']])) {
                if (strtotime($device_work_time[$value['device_name']]) <= strtotime($value['upload_at'])) {
                    $device_work_time[$value['device_name']] = '';
                }
            }
        }
    }

    // 查詢機台機型
    $whereAttr = new stdClass();
    $whereAttr->name = $device_array;
    $symbols = new stdClass();
    $symbols->name = array_map('symbols_equal', $device_array);
    $data = array(
        'condition_1' => array(
            'table' => 'device_box',
            'where' => $whereAttr,
            'limit' => ['ALL'],
            'symbols' => $symbols
        )
    );
    $device_box = CommonSqlSyntax_Query($data, "MsSQL");
    if ($device_box['Response'] !== 'ok' || count($device_box['QueryTableData']) == 0) {
        $device_box_data = [];
    } else {
        $device_box_data = $device_box['QueryTableData'];
    }

    $device_device_box = array();
    $device_model = array();
    foreach ($device_box_data as $key => $value) {
        if (!isset($device_device_box[$value['name']])) {
            $device_device_box[$value['name']] = array();
        }
        $device_device_box[$value['name']] = $value;
        array_push($device_model, $value['model']);
    }

    if (!empty($device_model)) {
        //查詢machine_status_list，機台燈號
        $whereAttr = new stdClass();
        $whereAttr->model = $device_model;
        $symbols = new stdClass();
        $symbols->model = array_map('symbols_equal', $device_model);
        $data = array(
            'condition_1' => array(
                'table' => 'machine_status_list',
                'where' => $whereAttr,
                'limit' => ['ALL'],
                'symbols' => $symbols
            )
        );
        $machine_status_list = CommonSqlSyntax_Query($data, "MySQL");
        if ($machine_status_list['Response'] !== 'ok' || count($machine_status_list['QueryTableData']) == 0) {
            $machine_status_list_data = [];
        } else {
            $machine_status_list_data = $machine_status_list['QueryTableData'];
        }
        foreach ($device_device_box as $device_device_box_key => $device_device_box_value) {
           foreach ($machine_status_list_data as $machine_status_list_data_key => $machine_status_list_data_value) {
                if ($device_device_box_value['model'] == $machine_status_list_data_value['model']) {
                    if (is_string($machine_status_list_data_value['light_list'])) {
                        $machine_status_list_data_value['light_list'] = json_decode($machine_status_list_data_value['light_list'], true);
                    }
                    $device_device_box[$device_device_box_key]['light_list'] =  $machine_status_list_data_value['light_list'];
                }
           }
        }
    }

    //用該值來儲存資料
    $machine_device_data = $machine_status_data;

    foreach ($machine_device_data as $key => $value) {
        if (is_string($value['machine_detail'])) {
            $value['machine_detail'] = json_decode($value['machine_detail'], true);
        }
        //機台狀態
        if ($value['mac_status'] == 'Q') {
            if (gettype($value['machine_detail']) == 'array') {
                if ($now_time - 600 > strtotime($value['machine_detail']['timestamp'])) {
                    $machine_device_data[$key]['now_status'] = 'S';
                } else {
                    $machine_detail = $value['machine_detail'];
                    if ($machine_detail['OPR'] == 1) {
                        $machine_device_data[$key]['now_status'] = 'R';//運轉
                    } else {
                        $machine_device_data[$key]['now_status'] = 'Q';//閒置
                    }
                    if (isset($device_device_box[$value['device_name']])) {
                        if (isset($device_device_box[$value['device_name']]['light_list'])) {
                            foreach ($machine_abn_code as $machine_abn_code_key => $machine_abn_code_value) {
                                if (isset($device_device_box[$value['device_name']]['light_list'][$machine_abn_code_key])) {
                                    if ($machine_abn_code_key != 'in_lube') {
                                        if ($machine_abn_code_value['value'] == $machine_detail[$machine_abn_code_key]) {                                            
                                            $machine_device_data[$key]['now_status'] = 'H';
                                        break;
                                        }
                                    }
                                }
                            }
                        } else {
                            //沒有機型資料
                            $machine_device_data[$key]['now_status'] = 'H';
                        }
                    } else {
                        //沒有機台資料
                        $machine_device_data[$key]['now_status'] = 'H';
                    }
                }
            }
        } else if ($value['mac_status'] == 'H') {
            $machine_device_data[$key]['now_status'] = 'H';
        }

        //機台轉速
        if (isset($device_device_box[$value['device_name']])) {
            $machine_device_data[$key]['speed'] = round($device_device_box[$value['device_name']]['capacity_hr'] / 60, 0);
        }

        //目前支數
        if ($value['machine_detail'] != "") {
            if (isset($value['machine_detail']['cnt'])) {
                $machine_device_data[$key]['count'] = $value['machine_detail']['cnt'];
            }
        }

        if (empty($device_work_time[$value['device_name']])) {
            $device_work_time[$value['device_name']] = '';
        }
        $device_now_count = get_start_count($value['device_name'], $device_work_time[$value['device_name']], $machine_device_data[$key]['count']);
        $machine_device_data[$key]['count'] = $device_now_count;
        // return $device_now_count;
        $machine_device_data[$key]['machine_detail'] = $value['machine_detail'];

        //正在做的工單進度
        if ($value['work_code'] != "") {
            //查詢目前工單
            $whereAttr = new stdClass();
            $whereAttr->code = [$value['work_code']];
            $symbols = new stdClass();
            $symbols->code = ["equal"];
            $data = array(
                'condition_1' => array(
                    'table' => 'work_order',
                    'where' => $whereAttr,
                    'limit' => [0,1],
                    'symbols' => $symbols
                )
            );
            $this_work_order = CommonSqlSyntax_Query($data, 'MsSQL');
            if ($this_work_order['Response'] !== 'ok') {
                return $this_work_order;
            } else if (count($this_work_order['QueryTableData']) == 0) {
                // $machine_device_data[$key]['work_code_qty'] = '--';
            } else {
                $this_work_order_data = $this_work_order['QueryTableData'];
                $machine_device_data[$key]['work_code_qty'] = $this_work_order_data[0]['work_qty'];
            }
        }

        //工單完成率
        if (isset($machine_device_data[$key]['count']) && isset($machine_device_data[$key]['work_code_qty']) && is_numeric($machine_device_data[$key]['work_code_qty'])) {
            $machine_device_data[$key]['work_code_complete'] = round($machine_device_data[$key]['count']/$machine_device_data[$key]['work_code_qty'], 2);
        }

        //剩餘支數
        if (isset($machine_device_data[$key]['work_code_qty']) && isset($machine_device_data[$key]['count'])) {
            $work_code_remain_qty = $machine_device_data[$key]['work_code_qty'] - $machine_device_data[$key]['count'];
            if ($work_code_remain_qty < 0) {
                $work_code_remain_qty = 0;
            }
        }

        array_push($returnData['QueryTableData'], array(
            'product_line' => $params->group_name,//產線
            'device_name' => isset($machine_device_data[$key]['device_name']) ? $machine_device_data[$key]['device_name'] : '--',//機台名稱
            'now_status' => isset($machine_device_data[$key]['now_status']) ? $machine_device_data[$key]['now_status'] : '--',//狀態
            'speed' => isset($machine_device_data[$key]['speed']) ? $machine_device_data[$key]['speed'] : '--',//速度
            // 'count' => isset($machine_device_data[$key]['count']) ? $machine_device_data[$key]['count'] : '--',//目前支數
            'work_code_qty' => isset($machine_device_data[$key]['work_code_qty']) ? $machine_device_data[$key]['work_code_qty'] : '--',//目前工單需求支數
            // 'work_code_complete' => isset($machine_device_data[$key]['work_code_complete']) ? $machine_device_data[$key]['work_code_complete'] : '--',//當前工單完成率
            'workCode' => isset($machine_device_data[$key]['work_code']) ? $machine_device_data[$key]['work_code'] : '--',//當前工單
            // 'work_code_remain_qty' => isset($work_code_remain_qty) ? $work_code_remain_qty : '--',//剩餘支數
        ));
    }

    $returnData['Response'] = 'ok';
    $returnData['page'] = 'elecboard';
    return $returnData;
}

//回傳equal
function symbols_equal(){
    return 'equal';
}

//回傳like
function symbols_like(){
    return 'like';
}

//儲存機台編號
function get_device_array($machine_status_data){
    $device_array = [];
    foreach ($machine_status_data as $key => $value) {
        array_push($device_array, $value['device_name']);
    }
    return $device_array;
}

//儲存工單編號
function get_work_code_array($work_code_use_data){
    $work_code_array = [];
    foreach ($work_code_use_data as $key => $value) {
        if (!in_array($value['work_code'], $work_code_array)) {
            array_push($work_code_array, $value['work_code']);
        }
    }
    return $work_code_array;
}

//輾牙機台目前支數
function get_start_count($device_name, $wrok_code_start_time, $device_now_count){
    $whereAttr = new stdClass();
    $symbols = new stdClass();
    $whereAttr->upload_at = [$wrok_code_start_time];
    $symbols->upload_at = ['greater'];
    $data = array(
        'condition_1' => array(
            'table' => strtolower($device_name),
            'orderby' => ['asc','upload_at'],
            'limit' => [0,1],
            'symbols' => $symbols,
            'where' => $whereAttr,
        )
    );
    $device_detail = CommonSqlSyntax_Query_v2_5($data, "PostgreSQL");
    if ($device_detail['Response'] !== 'ok') {
        return null;
    } else if (count($device_detail['QueryTableData']) == 0) {
        return null;
    }

    if (is_string($device_detail['QueryTableData'][0]['machine_detail'])) {
        $machine_detail_data = json_decode($device_detail['QueryTableData'][0]['machine_detail'], true);
    }

    if (!isset($machine_detail_data['cnt'])) {
        return null;
    }

    if (isset($device_now_count)) {
        $now_count = $device_now_count - $machine_detail_data['cnt'];
        if ($now_count >= 0) {
            return $now_count;
        } else {
            return null;
        }
    } else {
        return null;
    }
}


//demo用計數器
function DemoCounter($params){
    $process = $params->process;
    
    if ($process == '打頭') {
        $this_process = 5;
    } else if ($process == '輾牙') {
        $this_process = 6;
    }

    if ($this_process == 5) {
        $fields = ['device_name','machine_detail'];
        $data = array(
            'condition_1' => array(
                'fields' => $fields,
                'limit' => ["ALL"],
                'table' => 'machine_status_head'
            )
        );
        $machine_status_head = CommonSqlSyntax_Query($data, "MySQL");
        if ($machine_status_head['Response'] !== 'ok') {
            return $machine_status_head;
        } else if (count($machine_status_head['QueryTableData']) == 0) {
            $machine_status_head['Response'] = "no data";
            return $machine_status_head;
        }
        $machine_status_data = $machine_status_head['QueryTableData'];

        $returnData = [];
        $returnData['QueryTableData'] = [];

        foreach ($machine_status_data as $key => $value) {
            if (is_string($value['machine_detail'])) {
                $value['machine_detail'] = json_decode($value['machine_detail'], true);
            }
    
            if (isset($value['machine_detail']['cnt'])) {
                array_push($returnData['QueryTableData'], array(
                    'device_name' => $value['device_name'],
                    'count' => $value['machine_detail']['cnt']
                ));
            } else {
                array_push($returnData['QueryTableData'], array(
                    'device_name' => $value['device_name'],
                    'count' => '--'
                ));
            }
        }
        
        $returnData['Response'] = 'ok';
        $returnData['page'] = 'elecboard';
        return $returnData;
    } else if ($this_process == 6) {
        $fields = ['device_name', 'machine_detail', 'work_code'];
        $data = array(
            'condition_1' => array(
                'fields' => $fields,
                'limit' => ["ALL"],
                'table' => 'machine_status_thd'
            )
        );
        $machine_status_thd = CommonSqlSyntax_Query($data, "MySQL");
        if ($machine_status_thd['Response'] !== 'ok') {
            return $machine_status_thd;
        } else if (count($machine_status_thd['QueryTableData']) == 0) {
            $machine_status_thd['Response'] = "no data";
            return $machine_status_thd;
        }
        $machine_status_data = $machine_status_thd['QueryTableData'];

        $work_code_array = array_column($machine_status_data, 'work_code');

        if (!empty($work_code_array)) {
            //查詢工單上線數量
            $whereAttr = new stdClass();
            $whereAttr->work_code = $work_code_array;
            $whereAttr->project_id = ['6'];
            $whereAttr->status = ['S'];
            $symbols = new stdClass();
            $symbols->work_code = array_map('symbols_equal', $work_code_array);
            $symbols->project_id = ['equal'];
            $symbols->status = ['equal'];
            $data = array(
                'condition_1' => array(
                    'fields' => ['upload_at', 'work_code', 'project_id', 'device_name', 'status'],
                    'table' => 'work_code_use',
                    'orderby' => ['asc','upload_at'],
                    'limit' => ['ALL'],
                    'symbols' => $symbols,
                    'where' => $whereAttr,
                )
            );
            $work_code_use = CommonSqlSyntax_Query_v2_5($data, "PostgreSQL");
            if ($work_code_use['Response'] !== 'ok') {
                return $work_code_use;
            }
            $work_code_use_data = $work_code_use['QueryTableData'];
        } else {
            $work_code_use_data = [];
        }

        $returnData = [];
        $returnData['QueryTableData'] = [];

        foreach ($machine_status_data as $key => $value) {
            if (is_string($value['machine_detail'])) {
                $value['machine_detail'] = json_decode($value['machine_detail'], true);
            }
            if ($value['work_code'] != '' && isset($value['machine_detail']['cnt'])) {
                foreach ($work_code_use_data as $work_code_use_data_key => $work_code_use_data_value) {
                    if ($value['work_code'] == $work_code_use_data_value['work_code'] && $value['device_name'] == $work_code_use_data_value['device_name']) {
                        $value['time'] = $work_code_use_data_value['upload_at'];
                    }
                }
                $device_now_count;
                if ($value['time'] != '') {
                    $device_now_count = get_start_count($value['device_name'], $value['time'], $value['machine_detail']['cnt']);
                }
                if (isset($device_now_count)) {
                    array_push($returnData['QueryTableData'], array(
                        'device_name' => $value['device_name'],
                        'count' => $device_now_count
                    ));
                } else {
                    array_push($returnData['QueryTableData'], array(
                        'device_name' => $value['device_name'],
                        'count' => '--'
                    ));
                }
            } else {
                array_push($returnData['QueryTableData'], array(
                    'device_name' => $value['device_name'],
                    'count' => '--'
                ));
            }
        }

        $returnData['Response'] = 'ok';
        $returnData['page'] = 'elecboard';
        return $returnData;
    }
}