<?php
function CommonSqlSyntax_Query($data, $database)
{
    global $publicIP,$publicPort;
    if ($database == 'PostgreSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.0/myps/Sensor/SqlSyntax?uid=@sapido@PaaS&getSqlSyntax=yes";
    } else if ($database == 'MySQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.0/my/CommonUse/SqlSyntax?uid=@sapido@PaaS&getSqlSyntax=yes";
    } else if ($database == 'MsSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.0/ms/CommonUse/SqlSyntax?uid=@sapido@PaaS&getSqlSyntax=yes";
    }

    $returnData = connection($url, 'POST', $data);
    return $returnData;
}

function CommonSqlSyntax_Query_v2_5($data, $database)
{
    global $publicIP,$publicPort;
    if ($database == 'PostgreSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.5/myps/Sensor/SqlSyntax?uid=@sapido@PaaS&dbName=site2&getSqlSyntax=yes";
    }

    $returnData = connection($url, 'POST', $data);
    return $returnData;
}

function CommonSqlSyntaxJoin_Query($data, $database)
{    
    global $publicIP,$publicPort;
    if ($database == 'PostgreSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.0/myps/Sensor/SqlSyntax/JoinMultiTable?uid=@sapido@PaaS&getSqlSyntax=yes";
    } else if ($database == 'MySQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.0/my/CommonUse/SqlSyntax/JoinMultiTable?uid=@sapido@PaaS&getSqlSyntax=yes";
    } else if ($database == 'MsSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.0/ms/CommonUse/SqlSyntax/JoinMultiTable?uid=@sapido@PaaS&getSqlSyntax=yes";
    }

    $returnData = connection($url, 'POST', $data);
    return $returnData;
}

function CommonIntervalQuery($data, $database, $teble)
{
    global $publicIP,$publicPort;
    if ($database == 'PostgreSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.0/myps/Sensor/Interval/" . $teble . "?uid=@sapido@PaaS&attr=" . $data['col'] . "&valueStart=" . $data['valueStart'] . "&valueEnd=" . $data['valueEnd'];
    } else if ($database == 'MySQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/1.0/my/CommonUse/Interval/" . $teble . "?uid=@sapido@PaaS&attr=" . $data['col'] . "&valueStart=" . $data['valueStart'] . "&valueEnd=" . $data['valueEnd'];
    } else if ($database == 'MsSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/1.0/ms/CommonUse/Interval/" . $teble . "?uid=@sapido@PaaS&attr=" . $data['col'] . "&valueStart=" . $data['valueStart'] . "&valueEnd=" . $data['valueEnd'];
    }

    $returnData = connection($url, 'GET');
    return $returnData;
}

function CommonSpecificKeyQuery($database, $teble, $pattern)
{
    global $publicIP,$publicPort;
    if ($database == 'Redis') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/1.0/rd/CommonUse/SpecificKey/mes_device_status_" . $teble . "?uid=@sapido@PaaS&pattern=" . $pattern;
    }

    $returnData = connection($url, 'GET');
    return $returnData;
}

function SensorSingleRowQuery($data, $database, $teble)
{
    global $publicIP,$publicPort;
    if ($database == 'PostgreSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/2.0/myps/Sensor/SingleRow/" . $data['querySingle'] . "/" . $teble . "?uid=@sapido@PaaS";
    }

    $returnData = connection($url, 'GET');
    return $returnData;
}

function CommonTableQuery($database, $teble)
{
    global $publicIP,$publicPort;
    if ($database == 'MySQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/1.0/my/CommonUse/TableData?table=" . $teble . "&uid=@sapido@PaaS";
    }

    $returnData = connection($url, 'GET');
    return $returnData;
}

function CommonUpdate($data, $database, $teble)
{
    global $publicIP,$publicPort;
    if ($database == 'MySQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/1.5/my/CommonUse/" . $teble . "?uid=@sapido@PaaS";
    } else if ($database == 'MsSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/1.5/ms/CommonUse/" . $teble . "?uid=@sapido@PaaS";
    } else if ($database == 'Redis') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/1.0/rd/CommonUse/Hash/Keys/SpecificField?uid=@sapido@PaaS";
    }

    $returnData = connection($url, 'PATCH', $data);
    return $returnData;
}

function ExportCsv($data, $database, $teble)
{
    global $publicIP,$publicPort;
    if ($database == 'PostgreSQL') {
        $url = "https://" . $publicIP . ":" . $publicPort. "/api/CHUNZU/1.0/myps/Sensor/ExportCsv/" . $teble . "?uid=@sapido@PaaS";
    }

    $returnData = connection($url, 'POST', $data);
    return $returnData;
}

function SendEmails($data)
{
    global $publicIP,$publicPort;
    $url = "https://" . $publicIP . ":" . $publicPort. "/api/PaaS/1.0/email/sendEmails?uid=@sapido@PaaS";

    $returnData = connection($url, 'POST', $data);
    return $returnData;
}
?>