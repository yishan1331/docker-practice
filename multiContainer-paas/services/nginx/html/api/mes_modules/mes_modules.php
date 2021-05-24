<?php
$get_data = json_decode($argv[1], true);
// $get_data = json_decode('{"table":"test_emeter","upload_at":"2021-02-20 09:51:53","data":[{"RPWD_cur": 0.05, "TPF": 0.73, "cur_chdr": 87.07, "volt_vubr": 0.1, "cur_cubr": 18.91, "RELEC": 344.0, "upload_at": "2021-03-23 11:03:40", "RPWD_pred": 0.69, "current": 1.48, "RPWD_peak": 10.57, "frequency": 59.98, "volt_chdr": 0.69, "voltage": 390.11}]}', true);
// $get_data = json_decode('{"table":"1_1_main","upload_at":"2021-05-12 18:51:53","data":[{"mcks_op_temp": 27.0, "tri_comb_aprs": 6.28, "lub_overflow": 0, "prob_tran_float": 0, "abn_lub_flow": 1, "cus_lup_out_oprs": 0.0, "cnt2": 36153, "cnt1": 7913, "pun_blkcyl_hyd_oil_press": 1, "mtr_end": 0, "msld_lup_out_oprs": 0.0, "trx_jaw_aprs": 6.07, "abn_temp_slder": 0, "motor_brk": 0, "oil_level_low": 0, "mcg_revsp": 0.0, "hyd_press_grpse_lck": 1, "clp_aprs": 0.0, "overload": 0, "abn_brk_pla_clut": 0, "die_blkwed_hyd_oil_press": 1, "mat_wgt": 0, "stnk_lub_temp": 26.7, "aut_level": 0.0, "lub_press_slder": 0, "pko_sfbolt": 0, "trim_aprs": 0.01, "cus_end_oprs": 0.0, "opr": -1, "sh_feed": 0, "clp_spr_aprs": 2.85, "upload_at": "2021-04-26 17:57:35", "hyd_press_rscrpi_lck": 1, "ko_sfbolt": 0, "msld_op_temp": 26.6, "prob_sf_door": 1, "dko_sfpin_aprs": 5.65, "sf_window": 1, "abn_driver": 0, "msld_end_oprs": 0.0, "abn_pneu_press": 1, "msld_cus_flow": 0.0, "msld_cus_temp": 25.8, "fedw_aprs": 3.01, "clut_brk_cnt": 0, "mcks_op_flow": 0.0, "oper_door": 1, "mcks_cus_temp": 26.7, "msld_op_flow": 0.0, "lub_press_cutoff": 0, "abn_forg": 1, "clg_aprs": 3.17, "mcks_cus_flow": 0.0, "finish_cnt": 0, "error_code": ["2_M50", "2_M56", "2_M61", "2_M68", "2_M75", "2_M76", "2_M77"], "die_blkcyl_hyd_oil_press": 1, "pko_sfpin_aprs": 6.28, "postub_aprs": 0.06}]}', true);
// $get_data = json_decode('{"table":"1_1_loadCell","upload_at":"2021-05-11 16:17:02","data":[{"wire_weight": 1764.0, "upload_at": "2021-05-11 16:17:02"}]}', true);
// $get_data = json_decode('{"table":"machine_on_off_hist","upload_at":"2021-05-18 17:05:55.479098","data":[{"device_id": 1, "comp_id": 1, "site_id": 1, "status": "E","upload_at": "2021-05-18 17:05:55.479098"}]}', true);

if (isset($get_data['table'])) {
    $table = $get_data['table'];
}
if (isset($get_data['data'])) {
    $data = $get_data['data'];
}

if (empty($table) && empty($data)) {
    return;
}

$myfile = fopen(dirname(__FILE__) . "/newfile.txt", "a") or die("Unable to open file!");
fwrite($myfile, 'table:'.$table.'     time:'.$get_data['upload_at']."\n");
fclose($myfile);

switch ($table) {
    // case 'work_code_use':
    //     include(dirname(__FILE__) . "/api.php");
    //     include(dirname(__FILE__) . "/connection.php");
    //     include(dirname(__FILE__) . "/apiJsonBody.php");
    //     include(dirname(__FILE__) . "/mes_control/work_code_related.php");
    //     include(dirname(__FILE__) . "/mes_control/total_produce_related.php");
    //     break;
    // case 'mould_series_no_use':
    //     include(dirname(__FILE__) . "/mes_control/mould_use_related.php");
    //     break;
    // case 'thread_series_no_use':
    //     include(dirname(__FILE__) . "/mes_control/thread_use_related.php");
    //     break;
    // case 'wire_scroll_no_use':
    //     include(dirname(__FILE__) . "/mes_control/wire_use_related.php");
    //     break;
    // case 'inspection':
    //     include(dirname(__FILE__) . "/mes_control/inspection_related.php");
    //     break;
    // case 'workhour':
        
    //     break;
    // case 'runcard':
    //     include(dirname(__FILE__) . "/mes_control/bucket_use_related.php");
    //     break;
    case 'machine_on_off_hist':
        include(dirname(__FILE__) . "/mes_control/device_on_off_status.php");
        break;
    default:
        include(dirname(__FILE__) . "/switch_more.php");
        break;
}
?>