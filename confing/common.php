<?php

// 错误报告
error_reporting(E_ALL);

$root = $_SERVER['DOCUMENT_ROOT']?$_SERVER['DOCUMENT_ROOT']:dirname(dirname(__FILE__)).'/';
if (!isset($port)){
    $port = '3306';
}

define('IN_CRONLITE', true);
// define('ROOT', dirname(__FILE__) . '/');
define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('SYS_KEY', "qingka");

// 设置时区
date_default_timezone_set("Asia/Shanghai");

// 获取详细时间
$date = DateTime::createFromFormat('U.u', microtime(true))->format('Y-m-d H:i:s--u');

// 今天
$jtdate = date("Y-m-d");
// 昨天
$ztdate = date("Y-m-d", strtotime("-1 day"));

session_start();

function return_403($t="无法访问"){
    header("HTTP/1.1 403 Forbidden");
    exit('<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">403 '.$t);
}

$scriptpath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
$sitepath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl = ($_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $sitepath . '/';

include_once(ROOT . "config.php");

// 数据库
$DB = new DB($host, $user, $pwd, $dbname, $port);

$conf = array();
$confReturn = $DB->query("SELECT * FROM `qingka_wangke_config`");
while ($row = $DB->fetch($confReturn)) {
	$conf[$row['v']] = $row['k'];
}

// 是否已跑路
if($conf['paolu'] == '1' && basename($_SERVER['PHP_SELF']) != "paolu.php" && basename($_SERVER['PHP_SELF']) !="wzsz.php" && basename($_SERVER['PHP_SELF']) !="apiadmin.php"){
    header("Location: http://".$_SERVER['HTTP_HOST'].'/'.$conf["paolu_u"]);
    exit();
}

if (is_file(ROOT . '360safe/360webscan.php')) {
    require_once($root.'/confing/360safe/360webscan.php');
}

$password_hash = '!@#%!s?';
include_once ROOT . "../Checkorder/configuration.php";

// 支付
$alipay_config['sign_type'] = strtoupper('MD5');
$alipay_config['input_charset'] = strtolower('utf-8');
$alipay_config['transport'] = 'http';
$alipay_config['apiurl'] = $conf['epay_api'];
$alipay_config['partner'] = $conf['epay_pid'];
$alipay_config['key'] = $conf['epay_key'];
if (!defined('IN_CRONLITE'))
	exit();

if (isset($_COOKIE["admin_tokens"])) {
	$token = authcode(daddslashes($_COOKIE['admin_tokens']), 'DECODE', SYS_KEY);
	list($uid, $sid) = explode("\t", $token);
	$userrow = $DB->get_row("SELECT * FROM qingka_wangke_user WHERE uid='$uid' limit 1");
	$session = md5($userrow['user'] . $userrow['pass'] . $password_hash);
	if ($session == $sid) {
	    // 获取ip
//         $clientip = real_ip();
// 		$DB->query("UPDATE qingka_wangke_user SET ip='$clientip' WHERE uid = '$uid' ");
		$islogin = 1;
		
		if ($userrow['active'] == 0) {
		    setcookie("admin_tokens", "", time() - 216000,'/');
			exit('您的账号已被封禁！');
		}
	}
}else{
}

session_write_close();

?>