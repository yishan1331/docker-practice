<?php
include(dirname(__FILE__) . "/api.php");
include(dirname(__FILE__) . "/connection.php");

$table_array = explode('_', $table);
if (count($table_array) < 3) {
    return;
}
$cus_id = array_shift($table_array);
$device_id = array_shift($table_array);
$table_name = implode('_', $table_array);

$device = CommonSpecificKeyQuery('Redis', $cus_id . '_' . $device_id, 'yes');
if ($device['Response'] !== 'ok') {
    return;
}
$device_data = $device['QueryValueData'];

include(dirname(__FILE__) . "/apiJsonBody.php");

switch ($table_name) {
    case 'main':
        include(dirname(__FILE__) . "/mes_control/device_main.php");
        break;
    case 'emeter':
        include(dirname(__FILE__) . "/mes_control/device_emeter.php");
        break;
    case 'servoD':
        include(dirname(__FILE__) . "/mes_control/device_servod.php");
        break;
    case 'vibBearing':
        include(dirname(__FILE__) . "/mes_control/device_vibbearing.php");
        break;
    case 'vibMotor':
        include(dirname(__FILE__) . "/mes_control/device_vibmotor.php");
        break;
    case 'loadCell':
        include(dirname(__FILE__) . "/mes_control/device_loadcell.php");
        break;
    case 'smb':
        include(dirname(__FILE__) . "/mes_control/device_smb.php");
        break;
    default:
        break;
}

?>