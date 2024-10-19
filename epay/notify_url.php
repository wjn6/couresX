<?php
@header('Content-Type: text/html; charset=UTF-8');
include("../confing/common.php");
require_once("epay/notify.class.php");

$out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';

$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");
$type0 = isset($_GET['type0']) ? $_GET['type0'] : '';

$uid = $DB->get_row("select *  from qingka_wangke_pay where out_trade_no = '{$out_trade_no}' ");
$uid = $uid["uid"];
if($uid && $uid !== '1' && $type0 === 'tourist'){
    $user_result = $DB->get_row("select payData from qingka_wangke_user where uid = '{$uid}' ");
    $user_payData = json_decode($user_result["payData"],true);
    $alipay_config['apiurl'] = $user_payData['epay_api'];
    $alipay_config['partner'] = $user_payData['epay_pid'];
    $alipay_config['key'] = $user_payData['epay_key'];
}

$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

if ($verify_result) {
    $out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : ''; // 商户单号
    $trade_no = isset($_GET['trade_no']) ? $_GET['trade_no'] : ''; // 支付宝交易号
    $trade_status = isset($_GET['trade_status']) ? $_GET['trade_status'] : ''; // 交易状态
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $money = isset($_GET['money']) ? $_GET['money'] : 0; // 金额
    $pid = isset($_GET['pid']) ? $_GET['pid'] : '';
    $type = isset($_GET['type']) ? $_GET['type'] : '';

    sleep(5);

    // 查询支付信息
	$srow = $DB->get_row("SELECT * FROM qingka_wangke_pay WHERE `out_trade_no`='$out_trade_no' LIMIT 1 FOR UPDATE");
	$userrow = $DB->get_row("SELECT * FROM qingka_wangke_user WHERE uid='{$srow['uid']}'");
    
    // 游客下单
    if($type0 === 'tourist'){
        if ($trade_status == 'TRADE_SUCCESS' && $srow && $srow['status'] == 0 && $srow['money'] == $money) {
            // 付款完成后，支付宝系统发送该交易状态通知
				// 更新支付状态
			$DB->query("UPDATE `qingka_wangke_pay` SET `status` ='1', `endtime` ='$date', `trade_no`='$trade_no' WHERE `out_trade_no`='$out_trade_no'");
				// 更新订单状态
			$DB->query("UPDATE `qingka_wangke_order` SET `status` ='待处理', `dockstatus` ='0',`paytime` = '$date' WHERE `out_trade_no`='$out_trade_no'");
			// 更新代理余额
			$DB->query("UPDATE `qingka_wangke_user` SET `money`=`money`-'{$srow['money2']}' WHERE `uid`='{$uid}'");
				// 记录日志
			wlog($uid, "游客下单", "商铺 ".$uid." | 游客成功下单,售价：{$money}，扣除店铺成本：{$srow['money2']}", -$srow['money2']);
			
            echo "success";
        }else{
            $DB->query("UPDATE `qingka_wangke_pay` SET `endtime` ='$date', `trade_no`='$trade_no' WHERE `out_trade_no`='$out_trade_no'");
                $DB->query("UPDATE `qingka_wangke_order` SET `status` ='待处理', `paytime` = '$date' WHERE `out_trade_no`='$out_trade_no'");
            echo "success";
        }
        exit();
    }
    
	$epay_zs = $DB->get_row("select * from qingka_wangke_config where v='epay_zs' ");
	$epay_zs = json_decode($epay_zs["k"], true);
	
     $aa = number_format($srow['money'],4) == number_format($money,4);
    if ( ($trade_status == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') && $srow && $srow['status'] == 0 && $aa) {
        // 付款完成后，支付宝系统发送该交易状态通知
    	$money3 = 0;
        
		foreach ($epay_zs as $key => $value) {
			if ($money >= floatval($value["min"]) && ($value["max"] === '' || $money < floatval($value["max"]))) {
				$money3 = $money * (floatval($value["zsprice"]) / 100);
				break;
			}
		}


		$money3 = number_format($money3, 4);
		$money3 = $conf['epay_zs_open']?$money3:0;
		
		$money2 = $money3 + $money;
        
        $DB->query("UPDATE `qingka_wangke_pay` SET `status` ='1', `endtime` ='$date', `trade_no`='$trade_no' WHERE `out_trade_no`='$out_trade_no'");
        $DB->query("UPDATE `qingka_wangke_user` SET `money`=`money`+'$money2', `zcz`=`zcz`+'$money2' WHERE `uid`='{$userrow['uid']}'");
        
        if(!empty($conf["smtp_open_cz"])){
            emailGo($userrow['uid'],$conf["smtp_user"], "💰【UID:".$userrow['uid']."】在线充值成功", "充值金额：".$money."<br />支付方式：".$type."<hr />充值时间：".$date."<br />来源：".$_SERVER["HTTP_HOST"],  (empty($userrow['qq'])?$userrow['user']:$userrow['qq']) . '@qq.com',"在线充值");
        }
        
        wlog($userrow['uid'], "在线充值", "用户[{$userrow['user']}]成功充值{$money}", $money);
			if ($money3 != 0) {
				wlog($userrow['uid'], "在线充值", "用户[{$userrow['user']}]充值金额达标赠送{$money3}", $money3);
			}
			
    } else {
        $DB->query("UPDATE `qingka_wangke_pay` SET `endtime` ='$date', `trade_no`='$trade_no' WHERE `out_trade_no`='$out_trade_no'");
        echo "success";
    }
} else {
    echo "fail";
}

?>
