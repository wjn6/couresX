<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>正在跳转支付界面...</title>
</head>
<?php
include_once("../confing/common.php");
require_once("epay/submit.class.php");

$type = isset($_GET['type']) ? $_GET['type'] : '';
$type0 = isset($_GET['type0']) ? $_GET['type0'] : '';
$uid = isset($_GET['uid']) ? $_GET['uid'] : '1';

$out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';

$row = $DB->get_row("select * from qingka_wangke_pay where `out_trade_no`='{$out_trade_no}' limit 1 ");
$DB->query("update `qingka_wangke_pay` set `type`='$type' where `out_trade_no`='{$out_trade_no}'");

function get_protocol()
{
    global $conf;
    // 检查当前页面的协议
    // return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    return $conf['epay_protocol'] == 'http'?"http":"https";
}
if($uid && $uid !== '1' && $type0 === 'tourist'){
    $user_result = $DB->get_row("select payData from qingka_wangke_user where uid = '{$uid}' ");
    $user_payData = json_decode($user_result["payData"],true);
    $alipay_config['apiurl'] = $user_payData['epay_api'];
    $alipay_config['partner'] = $user_payData['epay_pid'];
    $alipay_config['key'] = $user_payData['epay_key'];
}

//构造请求的参数数组
$parameter = array(
    "pid" => trim($alipay_config['partner']),
    "type" => $type,
    "notify_url"    => get_protocol() . '://' . $row['domain'] . '/epay/notify_url.php?type0='.$type0,
    "return_url"    => get_protocol() . '://' . $row['domain'] . '/epay/return_url.php?type0='.$type0,
    "out_trade_no"    => $row['out_trade_no'],
    "name"    => $row['name'],
    "money"    => $type0 === 'tourist'?$row['money']:$row['money'],
    "sitename"    => $row['domain']
);



//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter);
echo $html_text;
?>
</body>

</html>