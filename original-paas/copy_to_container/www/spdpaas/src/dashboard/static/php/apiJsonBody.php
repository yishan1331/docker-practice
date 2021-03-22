<?php
function SqlSyntaxQuery_v2($data)
{
    foreach ($data as $condition => $value) {
        (!isset($data[$condition]['fields']))? $data[$condition]['fields']="":"";
        (!isset($data[$condition]['where']))? $data[$condition]['where']="":"";
        (!isset($data[$condition]['orderby']))? $data[$condition]['orderby']="":"";
        (!isset($data[$condition]['limit']))? $data[$condition]['limit']="":"";
        (!isset($data[$condition]['symbols']))? $data[$condition]['symbols']="":"";
        (!isset($data[$condition]['table']))? $data[$condition]['table']="":"";
        (!isset($data[$condition]['intervaltime']))? $data[$condition]['intervaltime']="":"";
        (!isset($data[$condition]['union']))? $data[$condition]['union']="":"";
        if (!isset($data[$condition]['subquery'])) {
            $data[$condition]['subquery'] = "";
        } else {
            $data[$condition]['subquery'] = SqlSyntaxQuery_v2($data[$condition]['subquery']);
        }
    }

    return $data;
}

function SqlSyntaxQuery_v2_5($data)
{
    foreach ($data as $condition => $value) {
        (!isset($data[$condition]['fields']))? $data[$condition]['fields']="":"";
        (!isset($data[$condition]['where']))? $data[$condition]['where']="":"";
        (!isset($data[$condition]['orderby']))? $data[$condition]['orderby']="":"";
        (!isset($data[$condition]['limit']))? $data[$condition]['limit']="":"";
        (!isset($data[$condition]['symbols']))? $data[$condition]['symbols']="":"";
        (!isset($data[$condition]['table']))? $data[$condition]['table']="":"";
        (!isset($data[$condition]['intervaltime']))? $data[$condition]['intervaltime']="":"";
        (!isset($data[$condition]['union']))? $data[$condition]['union']="":"";
        if (!isset($data[$condition]['subquery'])) {
            $data[$condition]['subquery'] = "";
        } else {
            $data[$condition]['subquery'] = SqlSyntaxQuery_v2_5($data[$condition]['subquery']);
        }
    }

    return $data;
}

function SqlSyntaxQueryJoin_v2($data)
{
    foreach ($data as $condition => $value) {
        (!isset($data[$condition]['fields']))? $data[$condition]['fields']="":"";
        (!isset($data[$condition]['where']))? $data[$condition]['where']="":"";
        (!isset($data[$condition]['orderby']))? $data[$condition]['orderby']="":"";
        (!isset($data[$condition]['limit']))? $data[$condition]['limit']="":"";
        (!isset($data[$condition]['symbols']))? $data[$condition]['symbols']="":"";
        (!isset($data[$condition]['table']))? $data[$condition]['table']="":"";
        (!isset($data[$condition]['intervaltime']))? $data[$condition]['intervaltime']="":"";
        (!isset($data[$condition]['union']))? $data[$condition]['union']="":"";
        if (!isset($data[$condition]['subquery'])) {
            $data[$condition]['subquery'] = "";
        } else {
            $data[$condition]['subquery'] = SqlSyntaxQueryJoin_v2($data[$condition]['subquery']);
        }
    }

    return $data;
}
?>