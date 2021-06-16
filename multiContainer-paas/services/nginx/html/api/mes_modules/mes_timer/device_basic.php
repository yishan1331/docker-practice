<?php
//機台名稱、機台型號、機台類別、機台圖片、機台IP CAM、機台感測點、機台燈號異常表、機台燈號對應表
date_default_timezone_set("Asia/Taipei");

include(dirname(__FILE__) . "/../globalvar.php");
include(dirname(__FILE__) . "/../api.php");
include(dirname(__FILE__) . "/../apiJsonBody.php");
include(dirname(__FILE__) . "/../connection.php");

//查詢所有機台
$device = CommonSpecificKeyQuery('Redis', '*', 'yes');
if ($device['Response'] !== 'ok') {
    return;
}
$device_data = $device['QueryValueData'];

$device_id_array = array_column($device_data, 'device_id');

$device_basic = get_device_basic($device_id_array);
$device_camera = get_camera($device_id_array);
$device_sensor_list = get_sensor_list($device_id_array);
$device_light_list = get_light_list($device_id_array);
$device_model_array = array_values(array_unique(array_column($device_basic, 'device_model')));
$device_light_error_single = get_light_error_single($device_model_array);
$device_error_level = get_error_level();
$device_light_error_list = get_light_error_list($device_model_array);

$update_data = array();
foreach ($device_data as $mes_device_status_key => $mes_device_status_value) {
    $mes_device_status_key_array = explode('mes_device_status_', $mes_device_status_key);
    $cus_device_id = $mes_device_status_key_array[1];
    if (!isset($cus_device_id)) {
        continue;
    }

    if (isset($device_basic[$cus_device_id])) {
        foreach ($device_basic[$cus_device_id] as $key => $value) {
            $update_data[$mes_device_status_key][$key] = $value;
        }
    }
    if (isset($device_camera[$cus_device_id])) {
        foreach ($device_camera[$cus_device_id] as $key => $value) {
            $update_data[$mes_device_status_key][$key] = $value;
        }
    }
    if (isset($device_sensor_list[$cus_device_id])) {
        foreach ($device_sensor_list[$cus_device_id] as $key => $value) {
            $update_data[$mes_device_status_key][$key] = $value;
        }
    }
    if (isset($device_light_list[$cus_device_id])) {
        foreach ($device_light_list[$cus_device_id] as $key => $value) {
            $update_data[$mes_device_status_key][$key] = $value;
        }
    }
    if (isset($update_data[$mes_device_status_key]['device_model'])) {
        $device_model = $update_data[$mes_device_status_key]['device_model'];
        foreach ($device_light_error_single[$device_model] as $key => $value) {
            $update_data[$mes_device_status_key][$key] = $value;
        }

        if (isset($update_data[$mes_device_status_key]['machine_light_list'])) {
            $update_data[$mes_device_status_key]['machine_light_error_list'] = get_machine_light_error_list($device_light_error_list[$device_model], $update_data[$mes_device_status_key]['machine_light_list']);
        }
    }

    $update_data[$mes_device_status_key]['error_level'] = $device_error_level;
}
// echo json_encode($update_data);
CommonUpdate($update_data, 'Redis', null);

//機台名稱、機台型號、機台類別、機台圖片、
function get_device_basic($device_id_array)
{
    if (empty($device_id_array)) {
        return array();
    }

    $query_device_basic = new apiJsonBody_queryJoin;
    $query_device_basic->addFields('device_basic', ['id', 'cus_id', 'device_name', 'model']);
    $query_device_basic->addFields('machine_model', ['category', 'img_name']);
    $query_device_basic->setTables(['device_basic', 'machine_model']);
    $join = new stdClass();
    $join->device_basic = [];
    $machine_model = new stdClass();
    $machine_model->machine_model = new stdClass();
    $machine_model->machine_model->model = new stdClass();
    $machine_model->machine_model->model = "model";
    array_push($join->device_basic, $machine_model);
    $query_device_basic->setJoin($join);
    $query_device_basic->addJointype('device_basic', 'machine_model', 'inner');
    $query_device_basic->addSymbols('device_basic', 'id', 'in');
    $query_device_basic->addWhere('device_basic', 'id', $device_id_array);
    $query_device_basic->setLimit([0,99999]);
    $query_device_basic_data = $query_device_basic->getApiJsonBody();
    $device_basic = CommonSqlSyntaxJoin_Query($query_device_basic_data, "MySQL");
    if ($device_basic['Response'] !== 'ok') {
        $device_basic_data = [];
    } else {
        $device_basic_data = $device_basic['QueryTableData'];
    }

    $mes_device = array();
    foreach ($device_basic_data as $key => $value) {
        if (!isset($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']])) {
            $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']] = array();
        }
        $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']] = array(
            'device_name' => $value['device_basic$device_name'],//機台名稱
            'device_model' => $value['device_basic$model'],//機台型號
            'device_category' => $value['machine_model$category'],//機台類別
            'machine_image' => $value['machine_model$img_name']//機台圖片
        );
    }

    return $mes_device;
}

//機台IP CAM
function get_camera($device_id_array)
{
    if (empty($device_id_array)) {
        return array();
    }

    $query_device_basic = new apiJsonBody_queryJoin;
    $query_device_basic->addFields('device_basic', ['id', 'cus_id']);
    $query_device_basic->addFields('machine_camera', ['camera_name', 'url']);
    $query_device_basic->setTables(['device_basic', 'machine_camera']);
    $join = new stdClass();
    $join->device_basic = [];
    $machine_camera = new stdClass();
    $machine_camera->machine_camera = new stdClass();
    $machine_camera->machine_camera->id = new stdClass();
    $machine_camera->machine_camera->id = "device_id";
    array_push($join->device_basic, $machine_camera);
    $query_device_basic->setJoin($join);
    $query_device_basic->addJointype('device_basic', 'machine_camera', 'left');
    $query_device_basic->addSymbols('device_basic', 'id', 'in');
    $query_device_basic->addWhere('device_basic', 'id', $device_id_array);
    $query_device_basic->setLimit([0,99999]);
    $query_device_basic_data = $query_device_basic->getApiJsonBody();
    $device_basic = CommonSqlSyntaxJoin_Query($query_device_basic_data, "MySQL");
    if ($device_basic['Response'] !== 'ok') {
        $device_basic_data = [];
    } else {
        $device_basic_data = $device_basic['QueryTableData'];
    }

    $mes_device = array();
    foreach ($device_basic_data as $key => $value) {
        if (!isset($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']])) {
            $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']] = array();
        }
        if (!isset($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_camera'])) {
            $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_camera'] = array();//機台IP CAM
        }
        array_push($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_camera'], array(
            'camera_name' => $value['machine_camera$camera_name'],
            'url' => $value['machine_camera$url']
        ));
    }

    return $mes_device;
}

//機台感測點
function get_sensor_list($device_id_array)
{
    if (empty($device_id_array)) {
        return array();
    }

    $query_device_basic = new apiJsonBody_queryJoin;
    $query_device_basic->addFields('device_basic', ['id', 'cus_id']);
    $query_device_basic->addFields('machine_sensor_list', ['sensor_range', 'sensor_warn', 'sensor_error', 'sensor_unit']);
    $query_device_basic->addFields('sensor_list', ['sensor_name', 'sensor_code', 'sensor_min', 'sensor_max', 'sensor_table_name']);
    $query_device_basic->addFields('sensor_class', ['sensor_class_name', 'sensor_class_code']);
    $query_device_basic->setTables(['device_basic', 'machine_sensor_list', 'sensor_list', 'sensor_class']);
    $join = new stdClass();
    $join->device_basic = [];
    $machine_sensor_list = new stdClass();
    $machine_sensor_list->machine_sensor_list = new stdClass();
    $machine_sensor_list->machine_sensor_list->id = new stdClass();
    $machine_sensor_list->machine_sensor_list->id = "device_id";
    $machine_sensor_list->machine_sensor_list->JOIN = new stdClass();
    $machine_sensor_list->machine_sensor_list->JOIN->sensor_list = new stdClass();
    $machine_sensor_list->machine_sensor_list->JOIN->sensor_list->sensor_id = new stdClass();
    $machine_sensor_list->machine_sensor_list->JOIN->sensor_list->sensor_id = "id";
    $machine_sensor_list->machine_sensor_list->JOIN->sensor_list->JOIN = new stdClass();
    $machine_sensor_list->machine_sensor_list->JOIN->sensor_list->JOIN->sensor_class = new stdClass();
    $machine_sensor_list->machine_sensor_list->JOIN->sensor_list->JOIN->sensor_class->sensor_class_code = new stdClass();
    $machine_sensor_list->machine_sensor_list->JOIN->sensor_list->JOIN->sensor_class->sensor_class_code = "sensor_class_code";
    array_push($join->device_basic, $machine_sensor_list);
    $query_device_basic->setJoin($join);
    $query_device_basic->addJointype('device_basic', 'machine_sensor_list', 'inner');
    $query_device_basic->addJointype('machine_sensor_list', 'sensor_list', 'inner');
    $query_device_basic->addJointype('sensor_list', 'sensor_class', 'inner');
    $query_device_basic->addSymbols('device_basic', 'id', 'in');
    $query_device_basic->addWhere('device_basic', 'id', $device_id_array);
    $query_device_basic->addSymbols('machine_sensor_list', 'sensor_enable', 'equal');
    $query_device_basic->addWhere('machine_sensor_list', 'sensor_enable', 'Y');
    $query_device_basic->setLimit([0,99999]);
    $query_device_basic_data = $query_device_basic->getApiJsonBody();
    $device_basic = CommonSqlSyntaxJoin_Query($query_device_basic_data, "MySQL");
    if ($device_basic['Response'] !== 'ok') {
        $device_basic_data = [];
    } else {
        $device_basic_data = $device_basic['QueryTableData'];
    }
    
    $mes_device = array();
    foreach ($device_basic_data as $key => $value) {
        if ($value['sensor_class$sensor_class_code'] == 'deviceStatus') {
            continue;
        }
        if (!isset($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']])) {
            $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']] = array();
        }
        if (!isset($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_sensor_list'])) {
            $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_sensor_list'] = array();//機台感測點
        }
        array_push($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_sensor_list'], array(
            'sensor_code' => $value['sensor_list$sensor_code'],
            'sensor_name' => $value['sensor_list$sensor_name'],
            'sensor_min' => $value['sensor_list$sensor_min'],
            'sensor_max' => $value['sensor_list$sensor_max'],
            'sensor_table_name' => $value['sensor_list$sensor_table_name'],
            'sensor_class_code' => $value['sensor_class$sensor_class_code'],
            'sensor_class_name' => $value['sensor_class$sensor_class_name'],
            'sensor_range' => $value['machine_sensor_list$sensor_range'],
            'sensor_warn' => $value['machine_sensor_list$sensor_warn'],
            'sensor_error' => $value['machine_sensor_list$sensor_error'],
            'sensor_unit' => $value['machine_sensor_list$sensor_unit']
        ));
    }
        
    return $mes_device;
}

//機台燈號對應表
function get_light_list($device_id_array)
{
    if (empty($device_id_array)) {
        return array();
    }

    $query_device_basic = new apiJsonBody_queryJoin;
    $query_device_basic->addFields('device_basic', ['id', 'cus_id', 'model']);
    $query_device_basic->addFields('light_list', ['light_code', 'light_name', 'color_on', 'color_off']);
    $query_device_basic->setTables(['device_basic', 'machine_light_list', 'light_list']);
    $join = new stdClass();
    $join->device_basic = [];
    $machine_light_list = new stdClass();
    $machine_light_list->machine_light_list = new stdClass();
    $machine_light_list->machine_light_list->id = new stdClass();
    $machine_light_list->machine_light_list->id = "device_id";
    $machine_light_list->machine_light_list->JOIN = new stdClass();
    $machine_light_list->machine_light_list->JOIN->light_list = new stdClass();
    $machine_light_list->machine_light_list->JOIN->light_list->light_id = new stdClass();
    $machine_light_list->machine_light_list->JOIN->light_list->light_id = "id";
    array_push($join->device_basic, $machine_light_list);
    $query_device_basic->setJoin($join);
    $query_device_basic->addJointype('device_basic', 'machine_light_list', 'inner');
    $query_device_basic->addJointype('machine_light_list', 'light_list', 'inner');
    $query_device_basic->addSymbols('device_basic', 'id', 'in');
    $query_device_basic->addWhere('device_basic', 'id', $device_id_array);
    $query_device_basic->addSymbols('machine_light_list', 'light_enable', 'equal');
    $query_device_basic->addWhere('machine_light_list', 'light_enable', 'Y');
    $query_device_basic->setLimit([0,99999]);
    $query_device_basic_data = $query_device_basic->getApiJsonBody();
    $device_basic = CommonSqlSyntaxJoin_Query($query_device_basic_data, "MySQL");
    if ($device_basic['Response'] !== 'ok') {
        $device_basic_data = [];
    } else {
        $device_basic_data = $device_basic['QueryTableData'];
    }

    $mes_device = array();
    foreach ($device_basic_data as $key => $value) {
        if (!isset($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']])) {
            $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']] = array();
        }
        if (!isset($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_light_list'])) {
            $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_light_list'] = array();//機台燈號對應表
        }
        if (!isset($mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_light_list'][$value['light_list$light_code']])) {
            $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_light_list'][$value['light_list$light_code']] = array();
        }

        if ($value['light_list$color_on'] == 'red') {
            $erro_value = 1;
        } else if ($value['light_list$color_off'] == 'red') {
            $erro_value = 0;
        }

        $mes_device[$value['device_basic$cus_id'] . '_' . $value['device_basic$id']]['machine_light_list'][$value['light_list$light_code']] = array(
            'name' => $value['light_list$light_name'],
            '1' => $value['light_list$color_on'],
            '0' => $value['light_list$color_off'],
            'erro_value' => isset($erro_value) ? $erro_value : null
        );
    }

    return $mes_device;
}

//機台燈號異常表
function get_light_error_single($device_model_array)
{
    if (empty($device_model_array)) {
        return array();
    }

    $query_light_error_single = new apiJsonBody_query;
    $query_light_error_single->setFields(['err_code', 'model', 'light_id', 'err_name', 'err_solution']);
    $query_light_error_single->setTable('light_error_single');
    $query_light_error_single->addSymbols('model', 'in');
    $query_light_error_single->addWhere('model', $device_model_array);
    $query_light_error_single_data = $query_light_error_single->getApiJsonBody();
    $light_error_single = CommonSqlSyntax_Query($query_light_error_single_data, "MySQL");
    if ($light_error_single['Response'] !== 'ok') {
        $light_error_single_data = [];
    } else {
        $light_error_single_data = $light_error_single['QueryTableData'];
    }

    $mes_device = array();
    foreach ($light_error_single_data as $key => $value) {
        if (!isset($mes_device[$value['model']])) {
            $mes_device[$value['model']] = array();
        }
        if (!isset($mes_device[$value['model']]['machine_abn_list'])) {
            $mes_device[$value['model']]['machine_abn_list'] = array();
        }
        if (!isset($mes_device[$value['model']]['machine_abn_list'][$value['err_code']])) {
            $mes_device[$value['model']]['machine_abn_list'][$value['err_code']] = array();
        }
        $mes_device[$value['model']]['machine_abn_list'][$value['err_code']] = array(
            'name' => $value['err_name'],
            'solution' => $value['err_solution'],
        );
    }
        
    return $mes_device;
}

//異常等級
function get_error_level()
{
    $query_error_level = new apiJsonBody_query;
    $query_error_level->setFields(['err_level', 'name']);
    $query_error_level->setTable('error_level');
    $query_error_level->setLimit(["ALL"]);
    $query_error_level_data = $query_error_level->getApiJsonBody();
    $error_level = CommonSqlSyntax_Query($query_error_level_data, "MySQL");
    if ($error_level['Response'] !== 'ok') {
        $error_level_data = [];
    } else {
        $error_level_data = $error_level['QueryTableData'];
    }

    $level_data = array();
    foreach ($error_level_data as $key => $value) {
        $level_data[$value['err_level']] = $value['name'];
    }
        
    return $level_data;
}

//機台燈號異常表
function get_light_error_list($device_model_array)
{
    if (empty($device_model_array)) {
        return array();
    }

    $query_light_error_list = new apiJsonBody_query;
    $query_light_error_list->setFields(['model', 'light_list', 'err_name', 'err_solution']);
    $query_light_error_list->setTable('light_error_list');
    $query_light_error_list->addSymbols('model', 'in');
    $query_light_error_list->addWhere('model', $device_model_array);
    $query_light_error_list_data = $query_light_error_list->getApiJsonBody();
    $light_error_list = CommonSqlSyntax_Query($query_light_error_list_data, "MySQL", "no");
    if ($light_error_list['Response'] !== 'ok') {
        $light_error_list_data = [];
    } else {
        $light_error_list_data = $light_error_list['QueryTableData'];
    }

    $mes_device = array();
    foreach ($light_error_list_data as $key => $value) {
        if (!isset($mes_device[$value['model']])) {
            $mes_device[$value['model']] = array();
        }

        array_push($mes_device[$value['model']], array(
            'light_list' => $value['light_list'],
            'name' => $value['err_name'],
            'solution' => $value['err_solution'],
        ));
    }

    return $mes_device;
}

function get_machine_light_error_list($device_light_error_list, $machine_light_list)
{
    if (empty($device_light_error_list) || empty($machine_light_list)) {
        return array();
    }
    $check_group_list = array();

    foreach ($device_light_error_list as $light_list_key => $light_list_value) {
        if (!empty($light_list_value['light_list'])) {
            if (is_string($light_list_value['light_list'])) {
                $light_list_value['light_list'] = json_decode($light_list_value['light_list'], true);
            }
            $light_list = $light_list_value['light_list'];
            $group_list = array();
            foreach ($light_list as $light_code => $light_status) {
                if (!isset($machine_light_list[$light_code])) {
                    break;
                }
                $erro_value = $machine_light_list[$light_code]['erro_value'];
                if (!$light_status) {
                    $group_list[$light_code] = $erro_value;
                } else {
                    $group_list[$light_code] = $erro_value ? 0 : 1;
                }
            }
            array_push($check_group_list, array(
                'light_list' => $group_list,
                'name' => $light_list_value['name'],
                'solution' => $light_list_value['solution']
            ));
        }
    }
    usort($check_group_list, 'sort_light_error_list_time');

    return $check_group_list;
}

//異常長度排序
function sort_light_error_list_time($a, $b)
{
    return (count($a['light_list']) > count($b['light_list'])) ? -1 : 1;
}
?>