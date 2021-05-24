<?php
function IntervalQuery($params, $publicIP)
{
    if ($params->settingtime) {
        $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/Interval/" . $params->table . "?uid=@sapido@PaaS&attr=" . $params->timeattr . "&valueStart=" . $params->start_time . "&valueEnd=" . $params->end_time;
    } else {
        $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/TableData?uid=@sapido@PaaS&table=" . $params->table;
    }
    return array($url);
}

function CommonSimpleQuery($params, $publicIP)
{
    if ($params->category == "ALL") {
        if (!$params->settingtime) {
            $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/Interval/" . $params->table . "?uid=@sapido@PaaS&attr=lastUpdateTime&valueStart=" . $params->start_time . "&valueEnd=" . $params->end_time;
        } else {
            $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/TableData?uid=@sapido@PaaS&table=" . $params->table;
        }
    } else if ($params->category == "systemformselectoptions") {
        $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/TableData?uid=@sapido@PaaS&table=" . $params->table;
    } else {
        if (!$params->settingtime) {
            $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/Interval/" . $params->table . "?uid=@sapido@PaaS&attr=lastUpdateTime&valueStart=" . $params->start_time . "&valueEnd=" . $params->end_time . "&whereParameter=" . $params->category . "&whereValue=" . $params->categoryparameter;
        } else {
            $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/Interval/" . $params->table . "?uid=@sapido@PaaS&attr=" . $params->category . "&valueStart=" . $params->categoryparameter . "&valueEnd=" . $params->categoryparameter;
        }
    }
    return array($url);
}

function CommonSqlSyntaxQuery($params, $publicIP)
{
    $postdata = new stdClass();
    foreach ($params->condition as $key => $value) {
        $postdata->$key = new stdClass();
        (!property_exists($value, 'table')) ? $table = "" : $table = $value->table;
        (!property_exists($value, 'fields')) ? $fields = "" : $fields = $value->fields;
        (!property_exists($value, 'where')) ? $where = "" : $where = $value->where;
        (!property_exists($value, 'orderby')) ? $orderby = "" : $orderby = $value->orderby;
        (!property_exists($value, 'limit')) ? $limit = "" : $limit = $value->limit;
        (!property_exists($value, 'symbols')) ? $symbols = "" : $symbols = $value->symbols;
        (!property_exists($value, 'intervaltime')) ? $intervaltime = "" : $intervaltime = $value->intervaltime;
        (!property_exists($value, 'subquery')) ? $subquery = "" : $subquery = $value->subquery;
        (!property_exists($value, 'union')) ? $union = "" : $union = $value->union;
        $postdata->$key->table = $table;
        $postdata->$key->fields = $fields;
        $postdata->$key->where = $where;
        $postdata->$key->orderby = $orderby;
        $postdata->$key->limit = $limit;
        $postdata->$key->symbols = $symbols;
        $postdata->$key->intervaltime = $intervaltime;
        $postdata->$key->subquery = $subquery;
        $postdata->$key->union = $union;
    }
    $url = "https://" . $publicIP . ":3687/api/IOT/2.0/myps/Sensor/SqlSyntax?uid=@sapido@PaaS&getSqlSyntax=yes";
    return array($url, $postdata);
}

function CommonJoinMultiTable($params, $publicIP)
{
    $postdata = new stdClass();
    foreach ($params->condition as $key => $value) {
        $postdata->$key = new stdClass();
        (!property_exists($value, 'tables')) ? $tables = "" : $tables = $value->tables;
        (!property_exists($value, 'fields')) ? $fields = "" : $fields = $value->fields;
        (!property_exists($value, 'orderby')) ? $orderby = "" : $orderby = $value->orderby;
        (!property_exists($value, 'limit')) ? $limit = "" : $limit = $value->limit;
        (!property_exists($value, 'where')) ? $where = "" : $where = $value->where;
        (!property_exists($value, 'symbols')) ? $symbols = "" : $symbols = $value->symbols;
        (!property_exists($value, 'join')) ? $join = "" : $join = $value->join;
        (!property_exists($value, 'jointype')) ? $jointype = "" : $jointype = $value->jointype;
        (!property_exists($value, 'subquery')) ? $subquery = "" : $subquery = $value->subquery;
        $postdata->$key->tables = $tables;
        $postdata->$key->fields = $fields;
        $postdata->$key->orderby = $orderby;
        $postdata->$key->limit = $limit;
        $postdata->$key->where = $where;
        $postdata->$key->symbols = $symbols;
        $postdata->$key->join = $join;
        $postdata->$key->jointype = $jointype;
        $postdata->$key->subquery = $subquery;
    }
    $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/2.0/my/CommonUse/SqlSyntax/JoinMultiTable?uid=@sapido@PaaS&getSqlSyntax=yes";
    return array($url, $postdata);
}

function CommonRegister($params, $publicIP)
{
    $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/" . $params->table . "?uid=@sapido@PaaS";
    return array($url, $params->postdata);
}

function CommonUpdate($params, $publicIP)
{
    $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/" . $params->table . "?uid=@sapido@PaaS";
    return array($url, $params->postdata);
}

function CommonDelete($params, $publicIP)
{
    $url = "https://" . $publicIP . ":3687/api/SAPIDOSYSTEM/1.0/my/CommonUse/" . $params->table . "?uid=@sapido@PaaS";
    return array($url, $params->postdata);
}
