<?php

try {
    
    $Root = $_SERVER['DOCUMENT_ROOT'];
    include ($Root . '/confing/common.php');
    if ($userrow['uid'] != '1') {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "<p>The page you are looking for could not be found.</p>";
        exit();
    }

    $logFiles = [
        ['type' => '自动同步', 'path' => 'logs/redis_chu_log.txt'],
        ['type' => '安排新订单', 'path' => 'logs/redis_addchu_log.txt'],
        ['type' => '重刷订单', 'path' => 'logs/plbs_log.txt'],
        ['type' => '批量同步', 'path' => 'logs/pltb_log.txt'],
        // 添加更多 Redis 实例的日志文件路径...
    ];

    $logContents = [];

    foreach ($logFiles as $log) {
        $logContent = '';
        if (file_exists($log['path'])) {
            // 直接读取文件内容并截取前500个字符
            $logContent = file_get_contents($log['path'], false, null, 0, 1500);
            $logContent = mb_convert_encoding($logContent, 'UTF-8', 'UTF-8');
        }
        $logContents[] = ['type' => $log['type'], 'log' => $logContent];
    }
    
    header('Content-Type: application/json');

    echo json_encode($logContents);
} catch (Exception $e) {
    // 捕获异常，并记录异常信息
    $errorMessage = $e->getMessage();
    // 记录错误信息到日志文件或其他地方
    echo json_encode(["code" => -1, "msg" => "发生错误：$errorMessage"]);
}
?>