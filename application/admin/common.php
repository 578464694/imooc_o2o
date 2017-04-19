<?php
/**
 * 将数据封装成　js 可识别的形式
 * @param $status
 * @param string $message
 * @param array $data
 * @return array
 */
function show($status, $message='', $data=[])
{
    return [
        'status' => intval($status),
        'message' => $message,
        'data' => $data,
    ];
}
?>