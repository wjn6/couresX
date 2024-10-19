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
    case 'XChartsData':
        is_admin();
        $data = [
            "template" => [
                "v" => $conf["version"],
                "n" => "CourseX",
            ],
            // 代理相关
            "dl" => [],
            // 订单相关
            "dd" => [],
            // 商品相关
            "class" => [],
            // 分类相关
            "fenlei" => [],
            // 充值相关
            "cz" => [],
        ];


        // 所有代理的剩余金额总合
        $dl_money_all = $DB->get_row("select sum(money) as dl_money_all from qingka_wangke_user where uid != 1 ");
        $data["dl"]["dl_money_all"] = (float)$dl_money_all["dl_money_all"];

        // 代理总数量
        $dl_count = $DB->count("select count(uid) from qingka_wangke_user where uid != 1 ");
        $data["dl"]["dl_count"] = (float)$dl_count;

        // 未封禁的代理的总数量
        $dl_active_count = $DB->count("select count(uid) from qingka_wangke_user where active=1 and uid != 1 ");
        $data["dl"]["dl_active_count"] = (float)$dl_active_count;

        // 已开通接单商城的代理的总数量
        $dl_touristdata_count = $DB->count("select count(uid) from qingka_wangke_user where JSON_VALID(touristdata) and JSON_LENGTH(touristdata) > 0 and uid != 1");
        $data["dl"]["dl_touristdata_count"] = (float)$dl_touristdata_count;

        // 本周订单数
        $dd = $DB->query("
            SELECT 
                (SELECT COUNT(*) FROM qingka_wangke_order ) AS dd_count,
                (SELECT COUNT(*) FROM qingka_wangke_order WHERE DATE(addtime) = CURDATE() ) AS dd_day_count,
                (SELECT COUNT(*) FROM qingka_wangke_order WHERE DATE(addtime) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) ) AS dd_lastday_count,
                (SELECT COUNT(*) FROM qingka_wangke_order WHERE YEARWEEK(addtime) = YEARWEEK(CURDATE()) ) AS dd_week_count,
                (SELECT COUNT(*) FROM qingka_wangke_order WHERE YEARWEEK(addtime) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK) ) AS dd_lastweek_count,
                (SELECT COUNT(*) FROM qingka_wangke_order WHERE MONTH(addtime) = MONTH(CURDATE()) AND YEAR(addtime) = YEAR(CURDATE())) AS dd_month_count,
                (SELECT COUNT(*) FROM qingka_wangke_order WHERE MONTH(addtime) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(addtime) = YEAR(CURDATE() - INTERVAL 1 MONTH)) AS dd_lastmonth_count;
        ")->fetch_assoc();

        // 订单总数
        $data["dd"]["dd_count"] = (float)$dd["dd_count"];
        // 今天订单总数
        $data["dd"]["dd_day_count"] = (float)$dd["dd_day_count"];
        // 昨天订单总数
        $data["dd"]["dd_lastday_count"] = (float)$dd["dd_lastday_count"];
        // 本周订单总数
        $data["dd"]["dd_week_count"] = (float)$dd["dd_week_count"];
        // 上周订单数
        $data["dd"]["dd_lastweek_count"] = (float)$dd["dd_lastweek_count"];
        // 本月订单总数
        $data["dd"]["dd_month_count"] = (float)$dd["dd_month_count"];
        // 上个月订单总数
        $data["dd"]["dd_lastmonth_count"] = (float)$dd["dd_lastmonth_count"];

        // 等待处理订单数量
        $dd_wait_count = $DB->count("select count(oid) from qingka_wangke_order where dockstatus=0");
        $data["dd"]["dd_wait_count"] = (float)$dd_wait_count;

        // 处理成功订单数量
        $dd_ok_count = $DB->count("select count(oid) from qingka_wangke_order where dockstatus=1");
        $data["dd"]["dd_ok_count"] = (float)$dd_ok_count;

        // 处理失败订单数量
        $dd_error_count = $DB->count("select count(oid) from qingka_wangke_order where dockstatus=2");
        $data["dd"]["dd_error_count"] = (float)$dd_error_count;

        // 所有订单的总金额
        $dd_money_all = $DB->get_row("select sum(fees) as dd_money_all from qingka_wangke_order");
        $data["dd"]["dd_money_all"] = (float)$dd_money_all["dd_money_all"];

        // 商品数量
        $class_count = $DB->count("select count(cid) from qingka_wangke_class ");
        $data["class"]["class_count"] = (float)$class_count;

        // 已上架的商品数量
        $class_ok_count = $DB->count("select count(cid) from qingka_wangke_class where status=1 ");
        $data["class"]["class_ok_count"] = (float)$class_ok_count;

        // 已开启无查下单的商品数量
        $class_nocheck_count = $DB->count("select count(cid) from qingka_wangke_class where nocheck=1 ");
        $data["class"]["class_nocheck_count"] = (float)$class_nocheck_count;

        // 已开启修改密码功能的商品数量
        $class_changepass_count = $DB->count("select count(cid) from qingka_wangke_class where changepass=1 ");
        $data["class"]["class_changepass_count"] = (float)$class_changepass_count;

        // 分类数据
        $fenlei_list_return = $DB->query("select id,name,status from qingka_wangke_fenlei");
        $fenlei_list = [];
        while ($row = $DB->fetch($fenlei_list_return)) {
            $class_count = $DB->count("select count(*) from qingka_wangke_class where fenlei={$row['id']}");
            $class_return = $DB->query("select name from qingka_wangke_class where fenlei={$row['id']}");
            $row["cdata"]  = [];
            while ($row2 = $DB->fetch($class_return)) {
                $row["cdata"][]  = $row2;
            }
            $row["cnum"]  = (int)$class_count;
            $fenlei_list[] = $row;
        }
        $data["fenlei"]["fenlei_list"] = $fenlei_list;

        // 充值统计
        $cz = $DB->query("
            SELECT 
                (SELECT sum(money) FROM qingka_wangke_pay where status=1 ) AS cz_money_all,
                (SELECT sum(money) FROM qingka_wangke_pay WHERE DATE(addtime) = CURDATE() and status=1 ) AS cz_day_money,
                (SELECT sum(money) FROM qingka_wangke_pay WHERE DATE(addtime) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) and status=1 ) AS cz_lastday_money,
                (SELECT sum(money) FROM qingka_wangke_pay WHERE YEARWEEK(addtime) = YEARWEEK(CURDATE()) and status=1 ) AS cz_week_money,
                (SELECT sum(money) FROM qingka_wangke_pay WHERE YEARWEEK(addtime) = YEARWEEK(CURDATE() - INTERVAL 1 WEEK) and status=1 ) AS cz_lastweek_money,
                (SELECT sum(money) FROM qingka_wangke_pay WHERE MONTH(addtime) = MONTH(CURDATE()) AND YEAR(addtime) = YEAR(CURDATE()) and status=1 ) AS cz_month_money,
                (SELECT sum(money) FROM qingka_wangke_pay WHERE MONTH(addtime) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(addtime) = YEAR(CURDATE() - INTERVAL 1 MONTH) and status=1 ) AS cz_lastmonth_money;
        ")->fetch_assoc();
        // 总充值
        $data["cz"]["cz_money_all"] = (float)$cz["cz_money_all"];
        // 今日充值
        $data["cz"]["cz_day_money"] = (float)$cz["cz_day_money"];
        // 昨日充值
        $data["cz"]["cz_lastday_money"] = (float)$cz["cz_lastday_money"];
        // 本周充值
        $data["cz"]["cz_week_money"] = (float)$cz["cz_week_money"];
        // 上周充值
        $data["cz"]["cz_lastweek_money"] = (float)$cz["cz_lastweek_money"];
        // 本月充值
        $data["cz"]["cz_month_money"] = (float)$cz["cz_month_money"];
        // 上月充值
        $data["cz"]["cz_lastmonth_money"] = (float)$cz["cz_lastmonth_money"];


        exit(json_encode(["code" => 1, "data" => $data]));
        break;
    default:
        // code...
        exit("你想干啥？");
        break;
}
