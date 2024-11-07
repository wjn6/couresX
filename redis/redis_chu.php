<?php

include ('../confing/common.php');
// 创建 Redis 连接
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$codeType = 'oids';// 任务唯一标识

$logFilePath = __DIR__ . '/logs/redis_chu_log.txt';

// 获取当前时间
function getTimeStamp()
{
    return DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");
}

// 写入日志
function writeLog($logFilePath, $logContent)
{
    global $logFilePath;
    
   // 尝试获取文件锁
    $fileHandle = fopen($logFilePath, 'a'); // 以追加方式打开文件
    if ($fileHandle === false) {
        echo "无法打开日志文件";
        return;
    }

    if (flock($fileHandle, LOCK_EX)) { // 获取排它锁
        // 如果日志文件不存在，则创建
        if (!file_exists($logFilePath)) {
            touch($logFilePath); // 创建文件
        }

        // 获取当前日志文件大小
        $fileSize = filesize($logFilePath);

        // 如果日志文件大小大于20MB
        if ($fileSize > 20 * 1024 * 1024) {
            // 读取文件内容
            $oldLog = file_get_contents($logFilePath);
            // 截取前20MB内容
            $newLog = mb_substr($oldLog, -$fileSize + 20 * 1024 * 1024, null, 'UTF-8');
            // 写入新日志内容
            file_put_contents($logFilePath, $logContent . PHP_EOL . $newLog);
        } else {
            // 直接将新日志内容追加到文件开头
            file_put_contents($logFilePath, $logContent . PHP_EOL . file_get_contents($logFilePath));
        }

        // 释放文件锁
        flock($fileHandle, LOCK_UN);
    } else {
        echo "无法获取文件锁";
    }

    // 关闭文件句柄
    fclose($fileHandle);

    // 输出日志内容
    echo $logContent;
}


// 整理日志
function formatLog($oid, $message, $additional = '')
{
    $pid = getmypid();
    $uptime = getTimeStamp();
    return "【" . $pid . "】" . "订单ID：{$oid} {$message}\r\n {$additional} 出队时间：{$uptime}\r\n----------------------------------------\r\n";
}

// 加锁函数
function acquireTaskLock($redis, $taskId, $timeout = 10)
{
    global $codeType;
    $identifier = uniqid(); // 生成唯一标识符
    $lockKey = $codeType . "_taskLock:$taskId";

    // 尝试获取锁
    $acquired = $redis->set($lockKey, $identifier, ['nx', 'ex' => $timeout]);

    // 返回锁的标识符或 false（获取失败）
    return $acquired ? $identifier : false;
}

// 释放锁函数
function releaseTaskLock($redis, $taskId, $identifier)
{
    global $codeType;
    $lockKey = $codeType . "_taskLock:$taskId";
    $lockValue = $redis->get($lockKey);

    // 如果当前锁的标识符与传入的标识符相同，则释放锁
    if ($lockValue == $identifier) {
        $redis->del($lockKey);
    }
}

// 获取任务
function getTaskFromQueue($redis)
{
    global $codeType;
    // 从队列中获取任务...
    $oid = $redis->blpop($codeType, 0);
    return $oid[1];
}

// 处理任务
function processTask($taskId)
{
    global $redis;
    global $codeType;
    global $DB;
    $pid = getmypid();// 获取当前进程PID

    $oid = $taskId;
    if (!empty($oid)) {
        $oid = trim($oid);
        $row = $DB->get_row("SELECT SQL_NO_CACHE * FROM qingka_wangke_order WHERE oid='$oid'");
        
        if ($row) {
            $result = processCx($oid);

            if ($result["code"] === 404) {
                orderLogs($oid, -999, "进度更新", "【自动批量】同步失败，上游通讯异常", "0");
                $logContent = formatLog($oid, "失败，上游通讯异常");
                writeLog($logFilePath, $logContent);
                return;
            }

            if ($result["code"] === -1) {
                orderLogs($oid, -999, "进度更新", "【自动批量】同步失败：".$result["msg"], "0");
                $logContent = formatLog($oid, "失败，" . $result["msg"]);
                writeLog($logFilePath, $logContent);
                return;
            }

            $result2 = array_filter($result, function ($item) use ($row) {
                return ($item["yid"] == $row["yid"] || $item["id"] == $row["yid"] || $item["oid"] == $row["yid"]) && !empty ($row["yid"]);
            });
            $result2 = array_values($result2);

            // 如果yid查的出来
            if (is_array($result2) && count($result2) > 0) {

                $result3 = $result2[0];

                $process_new = $result3["process"];
                $status_new = !empty($result3["status"]) ? $result3["status"] : $result3["status_text"];
                $remarks_new = $result3["remarks"];

                $result3['yid'] = !empty($result3['yid']) ? $result3['yid'] : $result3['oid'];
                $result3['yid'] = !empty($result3['yid']) ? $result3['yid'] : $result3['id'];
                $remarks_new = addslashes($result3['remarks']);

                $uptime = getTimeStamp();
                $ok = $DB->query("UPDATE qingka_wangke_order SET `name`='{$result3['name']}',`kcname`='{$result3['kcname']}', `yid`='{$result3['yid']}', `status`='{$status_new}', `courseStartTime`='{$result3['kcks']}', `courseEndTime`='{$result3['kcjs']}', `examStartTime`='{$result3['ksks']}', `examEndTime`='{$result3['ksjs']}', `process`='{$result3['process']}', `remarks`='{$remarks_new}' , `uptime`='{$uptime}' WHERE `user`='{$result3['user']}' AND `oid`='$oid' AND `yid`='{$result3['yid']}'");


                if ($ok) {
                    if (empty($process_new) && empty($status_new) && empty($remarks_new)) {
                        orderLogs($oid, -999, "进度更新", "【自动批量】返回为空", "0");
                        $logContent = formatLog($oid, "返回状态为空！跳过。\r\n 队列池剩余：{$redis->LLEN($codeType)}");
                        writeLog($logFilePath, $logContent);
                    } elseif ($process_new == $row['process'] && $status_new == $row['status']) {
                        orderLogs($oid, -999, "进度更新", "【自动批量】无最新进度", "0");
                        $logContent = formatLog($oid, $oid2[1] . "状态无需更新！跳过。\r\n 队列池剩余：{$redis->LLEN($codeType)}");
                        writeLog($logFilePath, $logContent);
                    } else {
                        orderLogs($oid, -999, "进度更新", "【自动批量】最新进度：".$row['remarks'], "0");
                        $logContent = "状态更新：{$row['status']}=>{$status_new}\r\n进度更新：{$row['process']}=>{$process_new}\r\n备注更新：{$row['remarks']}=>{$remarks_new}\r\n 队列池剩余：{$redis->LLEN($codeType)}";
                        $logContent = formatLog($oid, '', $logContent);
                        writeLog($logFilePath, $logContent);
                    }
                } else {
                    orderLogs($oid, -999, "进度更新", "【自动批量】同步失败", "0");
                    $logContent = formatLog($oid, "同步失败！");
                    writeLog($logFilePath, $logContent);
                }
            } else {
                // 如果yid查不出来
                $result2 = array_filter($result, function ($item) use ($row) {
                    // 课程名称相似度
                    return $item["user"] == $row["user"] && $item["kcname"] == $row["kcname"];
                });
                $result2 = array_values($result2);
                if (count($result2) > 0) {
                    $result3 = $result2[0];

                    $process_new = $result3["process"];
                    $status_new = $result3["status"] ? $result3["status"] : $result3["status_text"];
                    $remarks_new = $result3["remarks"];

                    $result3['yid'] = !empty($result3['yid']) ? $result3['yid'] : $result3['oid'];
                    $result3['yid'] = !empty($result3['yid']) ? $result3['yid'] : $result3['id'];
                    $remarks_new = addslashes($result3['remarks']);

                    $ok = $DB->query("update qingka_wangke_order set `name`='{$result3['name']}',`yid`='{$result3['yid']}',`status`='{$result3['status_text']}',`courseStartTime`='{$result3['kcks']}',`courseEndTime`='{$result3['kcjs']}',`examStartTime`='{$result3['ksks']}',`examEndTime`='{$result3['ksjs']}',`process`='{$result3['process']}',`remarks`='{$remarks_new}' ,`uptime`='{$date}' where `user`='{$result3['user']}' and `kcname`='{$result3['kcname']}' ");
                    if ($ok) {
                        if (empty($process_new) && empty($status_new) && empty($remarks_new)) {
                            orderLogs($oid, -999, "进度更新", "【自动批量】返回为空", "0");
                            $logContent = formatLog($oid, "返回状态为空！跳过。\r\n队列池剩余：{$redis->LLEN($codeType)}");
                            writeLog($logFilePath, $logContent);
                        } elseif ($process_new == $row['process'] && $status_new == $row['status']) {
                            orderLogs($oid, -999, "进度更新", "【自动批量】无最新进度", "0");
                            $logContent = formatLog($oid, "状态无需更新！跳过。\r\n队列池剩余：{$redis->LLEN($codeType)}");
                            writeLog($logFilePath, $logContent);
                        } else {
                            orderLogs($oid, -999, "进度更新", "【自动批量】最新进度：".$row['remarks'], "0");
                            $logContent = "状态更新：{$row['status']}=>{$status_new}\r\n进度更新：{$row['process']}=>{$process_new}\r\n备注更新：{$row['remarks']}=>{$remarks_new}\r\n队列池剩余：{$redis->LLEN($codeType)}";
                            $logContent = formatLog($oid, '', $logContent);
                            writeLog($logFilePath, $logContent);
                        }
                        ;
                    } else {
                        orderLogs($oid, -999, "进度更新", "【自动批量】同步失败", "0");
                        $logContent = formatLog($oid, "同步失败！");
                        writeLog($logFilePath, $logContent);
                    }
                } else {
                    orderLogs($oid, -999, "进度更新", "【自动批量】同步失败，无匹配项", "0");
                    $logContent = formatLog($oid, "同步失败，无匹配项");
                    writeLog($logFilePath, $logContent);
                }
            }
        }
        
    } else {
    }

}

// 获取锁
while ($taskId = getTaskFromQueue($redis)) {
    $lockIdentifier = acquireTaskLock($redis, $taskId);

    if ($lockIdentifier !== false) {
        // 成功获取锁，可以进行队列处理操作
        processTask($taskId);

        // 释放锁
        releaseTaskLock($redis, $taskId, $lockIdentifier);
        sleep(2);
    } else {
        // 获取锁失败，可以选择等待一段时间或直接放弃处理
        echo "获取锁失败\r\n----------------------------------------\r\n";
    }
}

?>