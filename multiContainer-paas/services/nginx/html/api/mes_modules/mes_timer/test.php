<?php
// //製程代號、機台型號、機台每分鐘產量、機型圖片
// date_default_timezone_set("Asia/Taipei");

// include(dirname(__FILE__) . "/../api.php");
// include(dirname(__FILE__) . "/../apiJsonBody.php");
// include(dirname(__FILE__) . "/../connection.php");

// //查詢所有機台
// $device = CommonSpecificKeyQuery('Redis', '*', 'yes');
// if ($device['Response'] !== 'ok') {
//     return;
// }
// $device_data = $device['QueryValueData'];

// $update_data = array();
// foreach ($device_data as $key => $value) {
//     $update_data[$key] = array(
//         'operatelog_information' => '[{"status": "-", "alarmCode": "", "durationTime": "03:18:16", "startTime": "2021-04-26 08:00:00", "endTime": "2021-04-26 11:18:16", "alarmDetail": ""}, {"status": "警報", "alarmVideo": "", "alarmCode": "", "durationTime": "00:47:30", "startTime": "2021-04-26 11:18:16", "endTime": "2021-04-26 12:05:46", "alarmDetail": ""}, {"status": "關機", "alarmCode": "", "durationTime": "00:13:22", "startTime": "2021-04-26 11:56:00", "endTime": "2021-04-26 12:09:22", "alarmDetail": ""}, {"status": "警報", "alarmVideo": "", "alarmCode": "", "durationTime": "01:32:19", "startTime": "2021-04-26 12:09:22", "endTime": "2021-04-26 13:41:41", "alarmDetail": ""}, {"status": "待機", "alarmVideo": "", "alarmCode": "", "durationTime": null, "startTime": "2021-04-26 13:41:41", "endTime": "2021-04-26 13:41:41", "alarmDetail": ""}, {"status": "警報", "alarmVideo": "", "alarmCode": "", "durationTime": "00:04:13", "startTime": "2021-04-26 13:41:41", "endTime": "2021-04-26 13:45:54", "alarmDetail": ""}, {"status": "待機", "alarmVideo": "", "alarmCode": "", "durationTime": "00:00:01", "startTime": "2021-04-26 13:45:54", "endTime": "2021-04-26 13:45:55", "alarmDetail": ""}, {"status": "警報", "alarmVideo": "", "alarmCode": "", "durationTime": "00:39:02", "startTime": "2021-04-26 13:45:55", "endTime": "2021-04-26 14:24:57", "alarmDetail": ""}, {"status": "待機", "alarmVideo": "", "alarmCode": "", "durationTime": "00:00:01", "startTime": "2021-04-26 14:24:57", "endTime": "2021-04-26 14:24:58", "alarmDetail": ""}, {"status": "警報", "alarmVideo": "", "alarmCode": "", "durationTime": "00:14:05", "startTime": "2021-04-26 14:24:58", "endTime": "2021-04-26 14:39:03", "alarmDetail": ""}, {"status": "待機", "alarmVideo": "", "alarmCode": "", "durationTime": "00:00:01", "startTime": "2021-04-26 14:39:03", "endTime": "2021-04-26 14:39:04", "alarmDetail": ""}, {"status": "警報", "alarmVideo": "", "alarmCode": "", "durationTime": "00:21:20", "startTime": "2021-04-26 14:39:04", "endTime": "2021-04-26 15:00:24", "alarmDetail": ""}]',
//         'stack_bar_information' => '[{"status": null, "endTime": "2021-04-26 11:18:16", "alarmDetail": "", "startTime": "2021-04-26 08:00:00", "duration_number": 11896}, {"status": "警報", "endTime": "2021-04-26 12:05:46", "alarmDetail": "", "startTime": "2021-04-26 11:18:16", "duration_number": 2850}, {"status": "關機", "endTime": "2021-04-26 12:09:22", "alarmDetail": "", "startTime": "2021-04-26 11:56:00", "duration_number": 802}, {"status": "警報", "endTime": "2021-04-26 13:41:41", "alarmDetail": "", "startTime": "2021-04-26 12:09:22", "duration_number": 5539}, {"status": "待機", "endTime": "2021-04-26 13:41:41", "alarmDetail": "", "startTime": "2021-04-26 13:41:41", "duration_number": null}, {"status": "警報", "endTime": "2021-04-26 13:45:54", "alarmDetail": "", "startTime": "2021-04-26 13:41:41", "duration_number": 253}, {"status": "待機", "endTime": "2021-04-26 13:45:55", "alarmDetail": "", "startTime": "2021-04-26 13:45:54", "duration_number": 1}, {"status": "警報", "endTime": "2021-04-26 14:24:57", "alarmDetail": "", "startTime": "2021-04-26 13:45:55", "duration_number": 2342}, {"status": "待機", "endTime": "2021-04-26 14:24:58", "alarmDetail": "", "startTime": "2021-04-26 14:24:57", "duration_number": 1}, {"status": "警報", "endTime": "2021-04-26 14:39:03", "alarmDetail": "", "startTime": "2021-04-26 14:24:58", "duration_number": 845}, {"status": "待機", "endTime": "2021-04-26 14:39:04", "alarmDetail": "", "startTime": "2021-04-26 14:39:03", "duration_number": 1}, {"status": "警報", "endTime": "2021-04-26 15:00:24", "alarmDetail": "", "startTime": "2021-04-26 14:39:04", "duration_number": 1280}]',
//     );
// }

// // echo json_encode($update_data);
// CommonUpdate($update_data, 'Redis', null);

// insert into machine_sensor_list value (1, 1, 1, default, 'max', NULL, '60' , '℃', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 2, 2, default, 'max', NULL, '60' , '℃', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 3, 3, default, 'max', NULL, '60' , '℃', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 4, 4, default, 'max', NULL, '60' , '℃', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 5, 5, default, 'max', NULL, '50' , '℃', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 6, 6, default, 'min', NULL, '0.1' , 'L/min', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 7, 7, default, 'min', NULL, '0.1' , 'L/min', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 8, 8, default, 'min', NULL, '1' , 'L/min', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 9, 9, default, 'min', NULL, '1' , 'L/min', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 10, 10, default, 'min', NULL, '5' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 11, 11, default, 'min', NULL, '5' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 12, 12, default, 'min', NULL, '1' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 13, 13, default, 'min', NULL, '1' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 14, 14, default, 'min', NULL, '5.5' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 15, 15, default, 'min', NULL, '5' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 16, 16, default, NULL, NULL, NULL , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 17, 17, default, 'min', NULL, '3.5' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 18, 18, default, NULL, NULL, NULL , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 19, 19, default, NULL, NULL, NULL , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 20, 20, default, 'min', NULL, '2' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 21, 21, default, NULL, NULL, NULL , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 22, 22, default, 'min', NULL, '5' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 23, 23, default, 'min', NULL, '1' , 'Kg/cm2', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 24, 24, default, NULL, NULL, NULL , NULL, 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 25, 25, default, 'max', NULL, '2' , '度', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 26, 26, default, 'max', NULL, '30' , NULL, 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 27, 27, default, 'max', NULL, '3.5' , 'mm/s', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 28, 28, default, 'max', NULL, '3.5' , 'mm/s', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 29, 29, default, 'max', NULL, '2.8' , 'mm/s', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 30, 30, default, 'max', NULL, '3.5' , 'mm/s', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 31, 31, default, 'max', NULL, '3.5' , 'mm/s', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 32, 32, default, 'max', NULL, '2.8' , 'mm/s', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 33, 33, default, 'between', NULL, '0,60' , 'A', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 34, 34, default, 'between', '340,420', '300,500' , 'V', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 35, 35, default, 'between', '0,2', '0,5' , '%', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 36, 36, default, NULL, NULL, NULL , NULL, 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 37, 37, default, NULL, NULL, NULL , NULL, 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 38, 38, default, NULL, NULL, NULL , NULL, 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 39, 39, default, 'between', NULL, '57.6,62.4' , NULL, 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 40, 40, default, 'min', '0.5', NULL , NULL, 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 41, 41, default, NULL, NULL, NULL , 'kb', 'root', 'root',default,default) ;
// insert into machine_sensor_list value (1, 42, 42, default, NULL, NULL, NULL , 'kb', 'root', 'root',default,default) ;



// update sensor_list set sensor_table_name = 'main' where id = 1;
// update sensor_list set sensor_table_name = 'main' where id = 2;
// update sensor_list set sensor_table_name = 'main' where id = 3;
// update sensor_list set sensor_table_name = 'main' where id = 4;
// update sensor_list set sensor_table_name = 'main' where id = 5;
// update sensor_list set sensor_table_name = 'main' where id = 6;
// update sensor_list set sensor_table_name = 'main' where id = 7;
// update sensor_list set sensor_table_name = 'main' where id = 8;
// update sensor_list set sensor_table_name = 'main' where id = 9;
// update sensor_list set sensor_table_name = 'main' where id = 10;
// update sensor_list set sensor_table_name = 'main' where id = 11;
// update sensor_list set sensor_table_name = 'main' where id = 12;
// update sensor_list set sensor_table_name = 'main' where id = 13;
// update sensor_list set sensor_table_name = 'main' where id = 14;
// update sensor_list set sensor_table_name = 'main' where id = 15;
// update sensor_list set sensor_table_name = 'main' where id = 16;
// update sensor_list set sensor_table_name = 'main' where id = 17;
// update sensor_list set sensor_table_name = 'main' where id = 18;
// update sensor_list set sensor_table_name = 'main' where id = 19;
// update sensor_list set sensor_table_name = 'main' where id = 20;
// update sensor_list set sensor_table_name = 'main' where id = 21;
// update sensor_list set sensor_table_name = 'main' where id = 22;
// update sensor_list set sensor_table_name = 'main' where id = 23;
// update sensor_list set sensor_table_name = 'main' where id = 24;
// update sensor_list set sensor_table_name = 'main' where id = 25;
// update sensor_list set sensor_table_name = 'servoD' where id = 26;
// update sensor_list set sensor_table_name = 'vibBearing' where id = 27;
// update sensor_list set sensor_table_name = 'vibBearing' where id = 28;
// update sensor_list set sensor_table_name = 'vibBearing' where id = 29;
// update sensor_list set sensor_table_name = 'vibMotor' where id = 30;
// update sensor_list set sensor_table_name = 'vibMotor' where id = 31;
// update sensor_list set sensor_table_name = 'vibMotor' where id = 32;
// update sensor_list set sensor_table_name = 'emeter' where id = 33;
// update sensor_list set sensor_table_name = 'emeter' where id = 34;
// update sensor_list set sensor_table_name = 'emeter' where id = 35;
// update sensor_list set sensor_table_name = 'emeter' where id = 36;
// update sensor_list set sensor_table_name = 'emeter' where id = 37;
// update sensor_list set sensor_table_name = 'emeter' where id = 38;
// update sensor_list set sensor_table_name = 'emeter' where id = 39;
// update sensor_list set sensor_table_name = 'emeter' where id = 40;
// update sensor_list set sensor_table_name = 'smb' where id = 41;
// update sensor_list set sensor_table_name = 'smb' where id = 42;

// insert into sensor_class value ('temp', '溫度', 'root','root',default,default);
// insert into sensor_class value ('flow', '流量', 'root','root',default,default);
// insert into sensor_class value ('oilPressure', '油壓', 'root','root',default,default);
// insert into sensor_class value ('airPressure', '空壓', 'root','root',default,default);
// insert into sensor_class value ('oilTemp', '油溫', 'root','root',default,default);
// insert into sensor_class value ('clutch', '離合器', 'root','root',default,default);
// insert into sensor_class value ('oilLevel', '副油箱油位', 'root','root',default,default);
// insert into sensor_class value ('serverDriver', '伺服驅動器', 'root','root',default,default);
// insert into sensor_class value ('vibration', '振動', 'root','root',default,default);
// insert into sensor_class value ('electricity', '電力', 'root','root',default,default);
// insert into sensor_class value ('netTraffic', '連線品質', 'root','root',default,default);

// insert into light_error_list value (default,'CBP136L', '{"abn_temp_slder":0,"abn_lub_flow":1,"lub_press_slder":1}', '機件損壞', '1.檢查主曲軸襯銅與主滑台襯板是否磨損,如磨損, 請維修或更換新品' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"abn_lub_flow":0,"lub_press_slder":1}', '油管堵塞', '1.檢查相關位置由管是否堵塞或鬆脫,如異常,請調整或更換油管' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"abn_temp_slder":0,"abn_lub_flow":0,"lub_press_slder":0}', '潤滑泵異常', '1.檢查潤滑泵是否異常,如異常,請維修或更換新品\n2.檢查濾油網是否堵塞,如堵塞,請清潔\n3.檢查副油箱內油量是否足夠,如不足,請添加潤滑油' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"lub_press_cutoff":0,"lub_press_slder":1}', '油管堵塞', '1.檢查相關位置由管是否堵塞或鬆脫,如異常,請調整或更換油管' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"lub_press_cutoff":0,"lub_press_slder":0}', '潤滑泵異常', '1.檢查潤滑泵是否異常,如異常,請維修或更換新品\n2.檢查濾油網是否堵塞,如堵塞,請清潔\n3.檢查副油箱內油量是否足夠,如不足,請添加潤滑油' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"lub_press_cutoff":1,"lub_press_slder":0}', '油管堵塞', '1.檢查相關位置由管是否堵塞或鬆脫,如異常,請調整或更換油管' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"pko_sfbolt":0,"abn_pneu_press":1}', '前沖安全銷異常', '1.更換安全銷新品,並注意調整行程,排除通出不順暢狀況\n2.再檢查前沖壓力開關檢測壓力值及設定正常壓力值\n3.壓力開關故障更換新品' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"pko_sfbolt":0,"abn_pneu_press":0}', '空壓源異常', '1.檢查空壓源是否異常,如異常,請調整空壓源壓力值\n2.檢查壓力開關是否異常,如異常,請更換新品' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"sh_feed":0,"abn_pneu_press":1}', '線材異常', '1.檢查材料是否彎折,如彎折,請裁掉彎折範圍後,重新入料\n2.檢查送料長度是否足夠,若不足,請調整\n3.檢查空壓缸作動是否正常,如異常,請維修' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"sh_feed":0,"abn_pneu_press":0}', '送料異常', '1.檢查空壓源是否正常,若異常,請調整' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"pun_blkcyl_hyd_oil_press":0,"abn_pneu_press":1}', '油壓單元異常', '1.檢查油箱油量是否足夠,若不足,請添加液壓油\n2.檢查油管路是否管路鬆脫現象,如鬆脫,請將管路復原\n3.氣動油壓泵輸出如不正常,更換氣動油壓泵' , 'root', 'root',default,default) ;
// insert into light_error_list value (default,'CBP136L', '{"pun_blkcyl_hyd_oil_press":0,"abn_pneu_press":0}', '空壓源異常', '1.檢查空壓源是否正常，如故障，請調整\n2.線路若斷線,請重新接線' , 'root', 'root',default,default) ;


?>