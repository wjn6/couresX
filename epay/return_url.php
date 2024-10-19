<?php
@header('Content-Type: text/html; charset=UTF-8');
include ("../confing/common.php");
require_once ("epay/notify.class.php");

$out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';

$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");
$type0 = isset($_GET['type0']) ? $_GET['type0'] : '';

$uid = $DB->get_row("select *  from qingka_wangke_pay where out_trade_no = '{$out_trade_no}' ");
$uid = $uid["uid"];
if ($uid && $uid !== '1' && $type0 === 'tourist') {
	$user_result = $DB->get_row("select payData from qingka_wangke_user where uid = '{$uid}' ");
	$user_payData = json_decode($user_result["payData"], true);
	$alipay_config['apiurl'] = $user_payData['epay_api'];
	$alipay_config['partner'] = $user_payData['epay_pid'];
	$alipay_config['key'] = $user_payData['epay_key'];
}

// 验证通知结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyReturn();
if ($verify_result) {
	$out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : ''; // 商户订单号
	$trade_no = isset($_GET['trade_no']) ? $_GET['trade_no'] : ''; // 支付宝交易号
	$trade_status = isset($_GET['trade_status']) ? $_GET['trade_status'] : ''; // 交易状态
	$type = isset($_GET['type']) ? $_GET['type'] : '';
	$money = isset($_GET['money']) ? $_GET['money'] : 0; // 金额

	// 查询订单信息
	$srow = $DB->get_row("SELECT * FROM qingka_wangke_pay WHERE `out_trade_no`='$out_trade_no' LIMIT 1 FOR UPDATE");
	$userrow = $DB->get_row("SELECT * FROM qingka_wangke_user WHERE uid='{$srow['uid']}'");

	// 游客下单
	if ($type0 === 'tourist') {
		if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
		    
			if ($srow && $srow['status'] == 0 && $srow['money'] == $money) {

				// 更新支付状态
				$DB->query("UPDATE `qingka_wangke_pay` SET `status` ='1', `endtime` ='$date', `trade_no`='$trade_no' WHERE `out_trade_no`='$out_trade_no'");
				// 更新订单状态
				$DB->query("UPDATE `qingka_wangke_order` SET `status` ='待处理', `dockstatus` ='0',`paytime` = '$date' WHERE `out_trade_no`='$out_trade_no'");
				// 更新代理余额
				$DB->query("UPDATE `qingka_wangke_user` SET `money`=`money`-'{$srow['money2']}' WHERE `uid`='{$uid}'");

				// 记录日志
				wlog($uid, "游客下单", "商铺 ".$uid." | 游客成功下单,售价：{$money}，扣除店铺成本：{$srow['money2']}", -$srow['money2']);
				exit("<script>top.window.touristPageVue.returnMethod(1,'下单成功!');</script>");
			} else {
				// 如果订单状态不为未支付或金额不匹配，更新支付状态并记录日志
				$DB->query("UPDATE `qingka_wangke_pay` SET `status` ='1', `endtime` ='$date', `trade_no`='$trade_no' WHERE `out_trade_no`='$out_trade_no'");
				$DB->query("UPDATE `qingka_wangke_order` SET `status` ='待处理', `dockstatus` ='0',`paytime` = '$date' WHERE `out_trade_no`='$out_trade_no'");
				// 更新代理余额
				$DB->query("UPDATE `qingka_wangke_user` SET `money`=`money`-'{$srow["money"]}' WHERE `uid`='{$uid}'");
				wlog($uid, "游客下单", "成功下单,支付{$money}元", $money);
				exit("<script>top.window.touristPageVue.returnMethod(1,'订单已支付过!');</script>");
			}
		} else {
			exit("<script>top.window.touristPageVue.returnMethod(0,'交易状态出错');</script>");
		}
		exit();
	}

	$epay_zs = $DB->get_row("select * from qingka_wangke_config where v='epay_zs' ");
	$epay_zs = json_decode($epay_zs["k"], true);

	if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
	    
	    $aa = number_format($srow['money'],4) == number_format($money,4);
            
		if ($srow && $srow['status'] == 0 && $aa ) {
			$money3 = 0;
            
			foreach ($epay_zs as $key => $value) {
				if ($money >= floatval($value["min"]) && ($value["max"] === '' || $money < floatval($value["max"]))) {
					$money3 = $money * (floatval($value["zsprice"]) / 100);
					break;
				}
			}


			$money3 = number_format($money3, 4);

            $money3 = $conf['epay_zs_open']?$money3:0;

			$money1 = $userrow['money'];
			$money2 = $money1 + $money + $money3;

            
            
			// 更新支付状态和用户余额
			$DB->query("UPDATE `qingka_wangke_pay` SET `status` ='1', `endtime` ='$date', `trade_no`='$trade_no' WHERE `out_trade_no`='$out_trade_no'");
			$DB->query("UPDATE `qingka_wangke_user` SET `money`='$money2', `zcz`=`zcz`+'$money2' WHERE `uid`='{$userrow['uid']}'");

			// 记录日志
        
            if(!empty($conf["smtp_open_cz"])){
                emailGo($userrow['uid'],$conf["smtp_user"], "💰【UID:".$userrow['uid']."】在线充值成功", "充值金额：".$money."<br />支付方式：".$type."<hr />充值时间：".$date."<br />来源：".$_SERVER["HTTP_HOST"],  (empty($userrow['qq'])?$userrow['user']:$userrow['qq']) . '@qq.com',"在线充值");
            }
        
			wlog($userrow['uid'], "在线充值", "用户[{$userrow['user']}]成功充值{$money}", $money);
			if ($money3 != 0) {
				wlog($userrow['uid'], "在线充值", "用户[{$userrow['user']}]充值金额达标赠送{$money3}", $money3);
			}

			// 提示成功信息并跳转
			$cg = "成功充值$money";
			if ($money3 != 0) {
				$cg .= "；本次赠送{$money3}！";
			}
			exit("<script>Array.from(top.window).find(item=> typeof item.touristPageVue === 'object' ).touristPageVue.returnMethod(1,'{$cg}');</script>");
		} else {
			// 如果订单状态不为未支付或金额不匹配，更新支付状态并记录日志
			$DB->query("UPDATE `qingka_wangke_pay` SET `status` ='1', `endtime` ='$date', `trade_no`='$trade_no' WHERE `out_trade_no`='$out_trade_no'");
			wlog($userrow['uid'], "在线充值", "重复刷新--用户[{$userrow['user']}]在线充值了{$money}", $money);
			exit("<script>Array.from(top.window).find(item=> typeof item.touristPageVue === 'object' ).touristPageVue.returnMethod(1,'".number_format($money,4)."已充值，".floatval(number_format($srow['money'],4)) == floatval(number_format($money,4))."请勿重复刷新" . number_format($srow['money'],4) . "');</script>");
		}
	} else {
		// 如果交易状态不正确，输出交易状态
		exit("<script>Array.from(top.window).find(item=> typeof item.touristPageVue === 'object' ).touristPageVue.returnMethod(0,'交易状态不正确" . $_GET['trade_status'] . "');</script>");
	}
} else {
	// 验证失败，输出失败信息并跳转
	// 游客下单
	if ($type0 === 'tourist') {
		exit("<script>Array.from(top.window).find(item=> typeof item.touristPageVue === 'object' ).touristPageVue.returnMethod(0,'充值失败1');</script>");
	}
	exit("<script>Array.from(top.window).find(item=> typeof item.touristPageVue === 'object' ).touristPageVue.returnMethod(0,'充值失败2');</script>");
}
?>