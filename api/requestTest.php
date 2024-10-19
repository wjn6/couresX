<?php
header('Content-Type: application/json');
$root = $_SERVER['DOCUMENT_ROOT'];
include ($root . '/confing/common.php');

// 安全权限控制
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($userrow) ) {
    exit("你想干啥？-1");
}

$aduid = empty($userrow['uid']) ? $userrow['uid'] : null;

// 验证是否是后台请求
function v_aduid()
{
    global $aduid;
    if (!empty($aduid)) {
        http_response_code(403);
        exit('403 禁止访问');
    }
}

// 通用的请求方法
function sendRequest($url, $method, $params, $headers, $cookie) {
    $start_time = microtime(true); // 记录开始时间

    $ch = curl_init();

    // 设置请求 URL
    curl_setopt($ch, CURLOPT_URL, $url);

    // 设置请求方法
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // 设置请求参数
    if ($params) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
    }

    // 设置请求头
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, is_array($headers) ? $headers : [$headers]);
    }

    // 设置 cookie
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }

    // 设置接收返回的数据
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // 执行请求并获取返回结果
    $response = curl_exec($ch);

    // 检查是否有错误发生
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return json_encode(array('error' => $error_msg));
    }

    // 获取状态码
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // 获取耗时
    $elapsed_time = microtime(true) - $start_time;

    // 获取大小（以 KB 为单位）
    $size = round(curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD) / 1024, 2);

    // 关闭 cURL 资源
    curl_close($ch);

    return json_encode(array(
        'response' => json_decode($response, true), // 对响应结果进行解码
        'status_code' => $status_code,
        'elapsed_time' => $elapsed_time,
        'size' => $size
    ));
}

// 获取请求参数
$requestData = $_POST;

// 获取请求 URL
$url = $requestData['url'];

// 获取请求方法
$method = strtoupper($requestData['method']);

// 获取请求参数
$params = $requestData['params'];

// 获取请求头
$headers = $requestData['headers'];

// 获取 cookie
$cookie = $requestData['cookie'];

// 发送请求并获取结果
$response = sendRequest($url, $method, $params, $headers, $cookie);

// 处理不同状态码的情况
$responseArr = json_decode($response, true);
$status_code = isset($responseArr['status_code']) ? $responseArr['status_code'] : 500;
$http_status_codes = [
    400 => '错误的请求',
    401 => '未经授权',
    403 => '禁止访问',
    404 => '未找到',
    500 => '内部服务器错误'
];
if (array_key_exists($status_code, $http_status_codes)) {
    $error_msg = $http_status_codes[$status_code];
    $responseArr['response'] = "错误: $error_msg";
}

// 如果结果包含可能导致解析异常的字符，则进行处理
if (is_string($responseArr['response']) && strpos($responseArr['response'], '\\') !== false) {
    // 将可能导致解析异常的字符替换为转义字符
    $responseArr['response'] = str_replace("\\", "\\\\", $responseArr['response']);
}

// 返回响应结果
echo json_encode($responseArr);
?>
