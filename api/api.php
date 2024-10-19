<?php
header('Content-Type: text/html; charset=UTF-8');
$root = $_SERVER['DOCUMENT_ROOT'];
include($root . '/confing/common.php');
date_default_timezone_set('Asia/Shanghai');

// 安全权限控制
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($userrow)) {
    exit("你想干啥？");
}

$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");

function is_admin()
{
    global $userrow;
    if (empty($userrow["uid"]) || (string) $userrow["uid"] !== '1') {
        http_response_code(403);
        header("Content-Type: text/plain; charset=utf-8");
        exit("403 Forbidden - Permission Denied");
    }
}

$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

switch ($act) {
    case 'optimize':
        $data = [];

        // 邮件队列是否正常
        $emails_result = $DB->count("select count(*) from qingka_wangke_emails ");
        $data["emails"] = [
            "need" => $emails_result > 0,
            "count" => $emails_result,
        ];

        // 订单数据大小
        $orders_result["count"] = $DB->count("select count(*) from qingka_wangke_order");
        $orders_size_result = $DB->query("select (DATA_LENGTH + INDEX_LENGTH) / 1024 AS table_size_kb from  information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = 'qingka_wangke_order'");
        $row_size = $orders_size_result->fetch_assoc();
        $orders_result["size"] = $row_size['table_size_kb'];
        $orders_result["size"] = str_replace(",", "", number_format($orders_result["size"], 2));
        $data["orders"] = [
            "need" => $orders_result["size"] > 0 && $orders_result["count"] > 0,
            "count" => $orders_result["count"],
            "size" => $orders_result["size"],
        ];

        // 日志数据大小
        $log_result["count"] = $DB->count("select count(*) from qingka_wangke_log");
        $log_size_result = $DB->query("select (DATA_LENGTH + INDEX_LENGTH) / 1024 AS table_size_kb from  information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = 'qingka_wangke_log'");
        $row_size = $log_size_result->fetch_assoc();
        $log_result["size"] = $row_size['table_size_kb'];
        $log_result["size"] = str_replace(",", "", number_format($log_result["size"], 2));
        $data["log"] = [
            "need" => $log_result["size"] > 0 && $log_result["count"] > 0,
            "count" => $log_result["count"],
            "size" => $log_result["size"],
        ];

        // 充值记录数据大小
        $pay_result["count"] = $DB->count("select count(*) from qingka_wangke_pay");
        $pay_size_result = $DB->query("select (DATA_LENGTH + INDEX_LENGTH) / 1024 AS table_size_kb from  information_schema.TABLES WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = 'qingka_wangke_pay'");
        $row_size = $pay_size_result->fetch_assoc();
        $pay_result["size"] = $row_size['table_size_kb'];
        $pay_result["size"] = str_replace(",", "", number_format($pay_result["size"], 2));
        $data["pay"] = [
            "need" => $pay_result["size"] > 0 && $pay_result["count"] > 0,
            "count" => $pay_result["count"],
            "size" => $pay_result["size"],
        ];

        // redis日志文件大小
        $file_paths = [
            "redis_chu_log" => $root . "/redis/logs/redis_chu_log.txt",
            "redis_addchu_log" => $root . "/redis/logs/redis_addchu_log.txt",
            "pltb_log" => $root . "/redis/logs/pltb_log.txt",
            "plbs_log" => $root . "/redis/logs/plbs_log.txt",
        ];
        $file_size = [];
        foreach ($file_paths as $key => $file_path) {
            if (file_exists($file_path)) {
                $file_size[$key] = str_replace(",", "", number_format(filesize($file_path) / 1024, 2));
            }
        }
        $data["redis"] = [
            "need" => str_replace(",", "", number_format(array_sum($file_size), 2)) > 0,
            "list" => $file_size,
            "size" => str_replace(",", "", number_format(array_sum($file_size), 2)),
        ];

        // mysql日志文件大小
        $sql = "SHOW VARIABLES LIKE 'datadir'";
        $result = $DB->query($sql);
        $row = $result->fetch_assoc();
        $mysql_data_dir = $row['Value'];

        // 构建二进制日志文件路径
        $binary_log_path = $mysql_data_dir . "/mysql-bin.";
        $mysql_log_file = array();

        // 循环获取文件大小
        for ($i = 1; $i <= 100; $i++) {
            $binary_log_file = $binary_log_path . sprintf("%06d", $i);
            $output = shell_exec("du -k '$binary_log_file' 2>/dev/null");
            if ($output !== null) {
                $output = intval(trim($output)); // 将输出转换为整数
                // 仅存储文件大小，不存储文件名
                $mysql_log_file["/mysql-bin." . sprintf("%06d", $i)] = trim($output);
            } else {
                // 文件不存在或者无法获取大小
                continue;
            }
        }


        $data["mysql_log"] = [
            "need" => count($mysql_log_file) > 1 * 1024 * 1024,
            "count" => count($mysql_log_file),
            "list" => $mysql_log_file,
            "size" => str_replace(",", "", number_format(array_sum($mysql_log_file), 2)),
        ];

        $DB->close();



        exit(json_encode(["code" => 1, "data" => $data]));
        // code...
        break;
    case "optimizeGo":
        $current_time = date('Y-m-d H:i:s');

        $needList = daddslashes($_POST["needList"]);

        if (empty($needList) || !is_array($needList)) {
            jsonReturn(-1, '请选择优化项');
        }

        $oknum = ["oknum" => 0, "type" => []];

        if (in_array("orders", $needList)) {
            if ($needList["orders_del"] == "week") {
                $ok = $DB->query("DELETE FROM qingka_wangke_order WHERE STR_TO_DATE(SUBSTRING_INDEX(addtime, '--', 1), '%Y-%m-%d %H:%i:%s') >= DATE_SUB('$current_time', INTERVAL 7 DAY)");
            }
            if ($needList["orders_del"] == "month") {
                $ok = $DB->query("DELETE FROM qingka_wangke_order WHERE STR_TO_DATE(SUBSTRING_INDEX(addtime, '--', 1), '%Y-%m-%d %H:%i:%s') >= DATE_SUB('$current_time', INTERVAL 1 MONTH)");
            }
            if ($needList["orders_del"] == "half_year") {
                $ok = $DB->query("DELETE FROM qingka_wangke_order WHERE STR_TO_DATE(SUBSTRING_INDEX(addtime, '--', 1), '%Y-%m-%d %H:%i:%s') >= DATE_SUB('$current_time', INTERVAL 6 MONTH)");
            }
            if ($needList["orders_del"] == "year") {
                $ok = $DB->query("DELETE FROM qingka_wangke_order WHERE STR_TO_DATE(SUBSTRING_INDEX(addtime, '--', 1), '%Y-%m-%d %H:%i:%s') >= DATE_SUB('$current_time', INTERVAL 1 YEAR)");
            }
            if ($needList["orders_del"] == "all") {
                $ok = $DB->query("TRUNCATE  qingka_wangke_order ");
            }
            if ($needList["orders_del"] == "zdy") {
                $start_time = $needList["orders_del_zdy_start"] . " 00:00:00";
                $end_time = $needList["orders_del_zdy_end"] . " 23:59:59";
                
                $ok = $DB->query("DELETE FROM qingka_wangke_order WHERE STR_TO_DATE(SUBSTRING_INDEX(addtime, '--', 1), '%Y-%m-%d %H:%i:%s') BETWEEN '$start_time' AND '$end_time'");
            }
            if ($ok) {
                $oknum["oknum"]++;
                $oknum["type"][] = "orders";
            }
        }

        if (in_array("emails", $needList)) {
            $ok = $DB->query("update qingka_wangke_config set k='[]' where v = 'emails'");
            $ok2 = $DB->query("TRUNCATE  qingka_wangke_emails ");
            // jsonReturn(-1, $ok2);
            if ($ok && $ok2) {
                $oknum["oknum"]++;
                $oknum["type"][] = "emails";
            }
        }

        if (in_array("log", $needList)) {
            $ok = $DB->query("TRUNCATE TABLE qingka_wangke_log");
            $oknum["oknum"]++;
            $oknum["type"][] = "log";
        }

        if (in_array("pay", $needList)) {
            $ok = $DB->query("TRUNCATE TABLE qingka_wangke_pay");
            $oknum["oknum"]++;
            $oknum["type"][] = "pay";
        }

        if (in_array("redis", $needList)) {
            $file_paths = [
                "redis_chu_log" => $root . "/redis/logs/redis_chu_log.txt",
                "redis_addchu_log" => $root . "/redis/logs/redis_addchu_log.txt",
                "pltb_log" => $root . "/redis/logs/pltb_log.txt",
                "plbs_log" => $root . "/redis/logs/plbs_log.txt",
            ];
            $file_ok = [];
            foreach ($file_paths as $key => $file_path) {
                if (file_exists($file_path)) {
                    file_put_contents($file_path, "");
                    $file_ok[$key] = 1;
                }
            }
            if (count($file_ok) == count($file_paths)) {
                $oknum["oknum"]++;
                $oknum["type"][] = "redis";
            }
        }



        exit(json_encode(["code" => 1, "oknum" => $oknum["oknum"], "okType" => $oknum["type"]]));

        break;
    case 'uploads':


        $folder = trim($_GET["folder"]); // 分配文件夹

        // 检查是否收到了有效的文件上传
        if (!isset($_FILES['file'])) {
            jsonReturn(-1, "需上传的数据异常");
        }

        // 上传文件存储目录
        $path = "/cdn/images/";
        $uploadDirectory = $root . $path;

        if (!file_exists($uploadDirectory)) {
            if (!mkdir($uploadDirectory, 0777, true)) {
                jsonReturn(-1, "初始化文件夹失败");
            }
        }

        // 控制子文件
        if ($folder . trim() !== 'avatar') {
            jsonReturn(-1, "不允许创建此文件夹");
        }

        $uploadDirectory2 = $uploadDirectory . $folder;
        if (!file_exists($uploadDirectory2)) {
            if (!mkdir($uploadDirectory2, 0777, true)) {
                jsonReturn(-1, "初始化文件夹失败");
            }
        }

        $uploadDirectory = $uploadDirectory . $folder . '/';

        // 允许上传的图片类型
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");

        // 获取上传的文件信息
        $uploadedFile = $_FILES['file'];
        $fileName = $uploadedFile['name'];
        $fileTempName = $uploadedFile['tmp_name'];

        // 检查上传的文件是否为有效的图片文件
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            jsonReturn(-1, "仅支持上传 JPEG、PNG 和 GIF 格式");
        }

        // 生成一个唯一的文件名
        $uniqueFileName = uniqid() . "__" . $fileName;

        // 移动上传的文件到存储目录
        $targetFilePath = $uploadDirectory . $uniqueFileName;
        if (!move_uploaded_file($fileTempName, $targetFilePath)) {
            jsonReturn(-1, "文件上传失败");
        }

        list($width, $height, $type) = getimagesize($targetFilePath);
        $compressionRatio = 0.8; // 设置压缩比例
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($targetFilePath);
                // 压缩 JPEG 图片质量
                imagejpeg($sourceImage, $targetFilePath, 80); // 设置为 80% 的质量
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($targetFilePath);
                // 压缩 PNG 图片质量
                imagepng($sourceImage, $targetFilePath, 9); // 设置压缩级别为 9
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($targetFilePath);
                // GIF 图片不需要压缩
                break;
            default:
                // 不支持的图片格式
                jsonReturn(-1, "不支持的图片格式");
        }

        // 释放资源
        imagedestroy($sourceImage);

        // 输出上传文件的 URL
        exit(json_encode(["code" => 1, "path" => $path, "name" => $uniqueFileName]));


        break;
    default:
        // code...
        exit("你想干啥？");
        break;
}
