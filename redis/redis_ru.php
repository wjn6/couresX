<?php
include('../confing/common.php');

// IP安全验证
$nowUseIP = $_SERVER['REMOTE_ADDR'];
$sqlCIP = trim($conf["serverIP"]);
$ipAddress = gethostbyname($_SERVER['HTTP_HOST']);

if(empty($conf["serverIP_type"])){
    if( $nowUseIP !== $ipAddress  ){
        echo "【-1】你在干什么\r\n";
        exit();
    }
}else{
    if(empty($_GET["uid"])){
        echo "参数不足 - 未携带uid";
        exit();
    }
    if(empty($_GET["key"])){
        echo "参数不足 - 未携带key";
        exit();
    }
    $uid_key = $DB->get_row("select `key` from qingka_wangke_user where uid='{$_GET['uid']}' limit 1 ");
    if(empty($uid_key)){
        echo "uid不存在";
        exit();
    }
    if(empty($uid_key["key"])){
        echo "当前uid未开通key";
        exit();
    }
    if(trim($uid_key["key"]) !== trim($_GET["key"])){
        echo "key错误";
        exit();
    }
}

$redis = new Redis();
$redis->connect("127.0.0.1", "6379");

// 检查 Redis 连接情况
if ($redis->ping()) {
    echo "Redis 连接正常！\r\n";
} else {
    echo "Redis 连接失败！\r\n";
}

$queueLength = $redis->LLEN('oids');

if ($queueLength == 0) {
    $i = 0;
    // 从数据库中查询未完成的订单并加入队列
    $orders = $DB->query("SELECT oid FROM qingka_wangke_order WHERE dockstatus = 1 AND status NOT IN ('已学习','已完成', '已考试', '已退款', '已取消','待支付','待审核') ORDER BY oid ASC");
    foreach ($orders as $order) {
        $redis->lPush("oids", $order['oid']);
        $i++;
    }
    
    echo "入队成功！本次入队订单共计：" . $i . "条！\r\n";
} else {
    echo "入队失败！队列池还有：" . $queueLength . " 条订单正在执行\r\n";
}
?>
