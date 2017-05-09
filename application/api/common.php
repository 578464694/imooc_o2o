<?php
/**
 * 将数据封装成　js 可识别的形式
 * @param $status
 * @param string $message
 * @param array $data
 * @return array
 */
function show($status, $message='', $data=[]) {
    return [
        'status' => intval($status),
        'message' => $message,
        'data' => $data,
    ];
}

function asdfasdf($username)
{
    if(!$username)
    {
        return '';
    }
    $strLength     = mb_strlen($username, 'utf-8');
    $firstStr     = mb_substr($username, 0, 1, 'utf-8');
    $lastStr     = mb_substr($username, -1, 1, 'utf-8');
    return $strLength == 2 ? $firstStr . str_repeat('*', mb_strlen($username, 'utf-8') - 1) : $firstStr . str_repeat("*", $strLength - 2) . $lastStr;
}