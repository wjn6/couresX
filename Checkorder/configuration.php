<?php

$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");
function daddslashes($string, $force = 0, $strip = FALSE)
{
  !defined("MAGIC_QUOTES_GPC") && define("MAGIC_QUOTES_GPC", get_magic_quotes_gpc());
  if (!MAGIC_QUOTES_GPC || $force) {
    if (is_array($string)) {
      foreach ($string as $key => $val) {
        $string[$key] = daddslashes($val, $force, $strip);
      }
    } else {
      $string = addslashes($strip ? stripslashes($string) : $string);
    }
  }
  return $string;
}

function real_ip()
{
  $ip = $_SERVER["REMOTE_ADDR"];
  if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && preg_match_all("#\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}#s", $_SERVER["HTTP_X_FORWARDED_FOR"], $matches)) {
    foreach ($matches[0] as $xip) {
      if (!preg_match("#^(10|172\\.16|192\\.168)\\.#", $xip)) {
        $ip = $xip;
        break;
      }
    }
  } elseif (isset($_SERVER["HTTP_CLIENT_IP"]) && preg_match("/^([0-9]{1,3}\\.){3}[0-9]{1,3}\$/", $_SERVER["HTTP_CLIENT_IP"])) {
    $ip = $_SERVER["HTTP_CLIENT_IP"];
  } elseif (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) && preg_match("/^([0-9]{1,3}\\.){3}[0-9]{1,3}\$/", $_SERVER["HTTP_CF_CONNECTING_IP"])) {
    $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
  } elseif (isset($_SERVER["HTTP_X_REAL_IP"]) && preg_match("/^([0-9]{1,3}\\.){3}[0-9]{1,3}\$/", $_SERVER["HTTP_X_REAL_IP"])) {
    $ip = $_SERVER["HTTP_X_REAL_IP"];
  }
  return $ip;
}

function get_ip_city($ip = '')
{
  $url = "https://ip.netart.cn/";
  @($data = file_get_contents($url . $ip));
  $arr = json_decode($data, true);
  if (array_key_exists("ip", $arr)) {
    $location = "{$arr['country']['name']} {$arr['regions'][0]}{$arr['regions'][1]}{$arr['regions'][2]} {$arr['as']['info']}{$arr['type']}";
    // if ($arr["data"]["city"]) {
    //   $location = $arr["data"]["province"] . $arr["data"]["city"];
    // } else {
    //   $location = $arr["data"]["province"];
    // }
  }
  if ($location) {
    return $location;
  } else {
    return false;
  }
}
function get_ip_more($ip = '')
{
  $url = "https://ip.netart.cn/";
  @($data = file_get_contents($url . $ip));
  $arr = json_decode($data, true);
  if (array_key_exists("ip", $arr)) {
    return json_encode($arr);
  } else {
    return false;
  }
}
function getSubstr($str, $leftStr, $rightStr)
{
  $left = strpos($str, $leftStr);
  $right = strpos($str, $rightStr, $left);
  if ($left < 0 or $right < $left) {
    return '';
  }
  return substr($str, $left + strlen($leftStr), $right - $left - strlen($leftStr));
}
function authcode($string, $operation = "DECODE", $key = '', $expiry = 0)
{

  $ckey_length = 4;
  $key = md5($key ? $key : ENCRYPT_KEY);
  $keya = md5(substr($key, 0, 16));
  $keyb = md5(substr($key, 16, 16));
  $keyc = $ckey_length ? $operation == "DECODE" ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length) : '';
  $cryptkey = $keya . md5($keya . $keyc);
  $key_length = strlen($cryptkey);
  $string = $operation == "DECODE" ? base64_decode(substr($string, $ckey_length)) : sprintf("%010d", $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
  $string_length = strlen($string);
  $result = '';
  $box = range(0, 255);
  $rndkey = array();
  for ($i = 0; $i <= 255; $i++) {
    $rndkey[$i] = ord($cryptkey[$i % $key_length]);
  }
  if (is_file(ROOT . '360safe/360webscan.php')) {
    require_once(ROOT . '360safe/360webscan.php');
  }
  $j = $i = 0;
  while ($i < 256) {
    $j = ($j + $box[$i] + $rndkey[$i]) % 256;
    $tmp = $box[$i];
    $box[$i] = $box[$j];
    $box[$j] = $tmp;
    $i++;
  }
  $a = $j = $i = 0;
  while ($i < $string_length) {
    $a = ($a + 1) % 256;
    $j = ($j + $box[$a]) % 256;
    $tmp = $box[$a];
    $box[$a] = $box[$j];
    $box[$j] = $tmp;
    $result .= chr(ord($string[$i]) ^ $box[($box[$a] + $box[$j]) % 256]);
    $i++;
  }
  if ($operation == "DECODE") {
    if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
      return substr($result, 26);
    } else {
      return '';
    }
  } else {
    return $keyc . str_replace("=", '', base64_encode($result));
  }
}
function getNonceStr($code)
{
  for ($i = 0; $i > 10; $i++) {
    $code .= mt_rand(1000);
  }
  $nonceStrTemp = md5($code);
  $nonce_str = mb_substr($nonceStrTemp, 5, 37);
  return $nonce_str;
}
function random($length, $numeric = 0)
{
  $seed = base_convert(md5(microtime() . $_SERVER["DOCUMENT_ROOT"]), 16, $numeric ? 10 : 35);
  $seed = $numeric ? str_replace("0", '', $seed) . "012340567890" : $seed . "zZ" . strtoupper($seed);
  $hash = '';
  $max = strlen($seed) - 1;
  for ($i = 0; $i < $length; $i++) {
    $hash .= $seed[mt_rand(0, $max)];
  }
  return $hash;
}
function showmsg($content = "未知的异常", $type = 4, $back = false, $back_name = false)
{
  if (is_file(ROOT . '360safe/360webscan.php')) {
    require_once(ROOT . '360safe/360webscan.php');
  }
  switch ($type) {
    case 1:
      $panel = "success";
      break;
    case 2:
      $panel = "info";
      break;
    case 3:
      $panel = "warning";
      break;
    case 4:
      $panel = "danger";
      break;
  }
  echo "<div class=\"container\" style=\"padding-top:70px;\"> <div class=\"col-xs-12 col-sm-10 col-lg-8 center-block\" style=\"float: none;\">";
  echo "<div class=\"panel panel-" . $panel . "\">\r\n      <div class=\"panel-heading\">\r\n        <h3 class=\"panel-title\">提示信息</h3>\r\n        </div>\r\n        <div class=\"panel-body\">";
  echo $content;
  if ($back) {
    if ($back_name) {
      echo "<hr/><a href=\"" . $back . "\"><< " . $back_name . "</a>";
    } else {
      echo "<hr/><a href=\"" . $back . "\"><< 返回列表</a>";
    }
    echo "<br/><a href=\"javascript:history.back(-1)\"><< 返回上一页</a>";
  } else {
    echo "<hr/><a href=\"javascript:history.back(-1)\"><< 返回上一页</a>";
  }
  echo "</div>\r\n    </div>";
}
function checkRefererHost()
{
  if (!$_SERVER["HTTP_REFERER"]) {
    return false;
  }
  $url_arr = parse_url($_SERVER["HTTP_REFERER"]);
  $http_host = $_SERVER["HTTP_HOST"];
  if (strpos($http_host, ":")) {
    $http_host = substr($http_host, 0, strpos($http_host, ":"));
  }
  return $url_arr["host"] === $http_host;
}

function merge_spaces($string)
{
  return preg_replace("/\\s+/", " ", $string);
}
function alert($a, $wz = false)
{
  if ($wz == '') {
    $wz = $_SERVER["SCRIPT_NAME"];
  }
  exit("<script language='javascript'>layer.alert('{$a}',function(){window.location.href='{$wz}'});</script>");
}

function jsonReturn($code, $msg)
{
  $data = array("code" => $code, "msg" => $msg);
  return exit(json_encode($data));
}
function jsonReturnData($code, $data = [], $msg = "")
{
  $data = array("code" => $code, "data" => $data, "msg" => $msg);
  return exit(json_encode($data));
}

function get_curl($url, $post = 0, $referer = 0, $cookie = 0, $header = 0, $ua = 0, $nobaody = 0)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  $httpheader[] = "Accept: */*";
  $httpheader[] = "Accept-Encoding: gzip,deflate,sdch";
  $httpheader[] = "Accept-Language: zh-CN,zh;q=0.8";
  $httpheader[] = "Connection: close";
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  if ($post) {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  }
  curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
  if ($header) {
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
  }
  if ($cookie) {
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
  }
  if ($referer) {
    if ($referer == 1) {
      curl_setopt($ch, CURLOPT_REFERER, "http://m.qzone.com/infocenter?g_f=");
    } else {
      curl_setopt($ch, CURLOPT_REFERER, $referer);
    }
  }
  if ($ua) {
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  } else {
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36");
  }
  if ($nobaody) {
    curl_setopt($ch, CURLOPT_NOBODY, 1);
  }
  curl_setopt($ch, CURLOPT_ENCODING, "gzip");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $ret = curl_exec($ch);
  curl_close($ch);
  return $ret;
}

function parseUrlFn($url)
{
  $parsedUrl = parse_url($url);
  $queryString = $parsedUrl['query'];
  parse_str($queryString, $params);
  foreach ($params as $key => $value) {
    $params[$key] = trim($value);
  }
  $newQueryString = http_build_query($params);
  $newUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . ':' . $parsedUrl['port'] . $parsedUrl['path'] . '?' . $newQueryString;
  return $newUrl;
}

function get_url($url, $post = false, $cookie = false, $header = false, $timeout = 240)
{
  $ch = curl_init();
  $url = parseUrlFn($url);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)");
  if ($post) {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  }
  if ($cookie) {
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
  }
  // 设置连接超时时间，单位是秒
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);


  // 设置请求超时时间，单位是秒

  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

  $result = curl_exec($ch);
  $sendHeaders = curl_getinfo($ch, CURLINFO_HEADER_OUT);

  curl_close($ch);
  return $result;
}
function get_url2($url, $post = false, $cookie = false, $header = false)
{
  $ch = curl_init();
  if ($header) {
    curl_setopt($ch, CURLOPT_HEADER, 1);
  } else {
    curl_setopt($ch, CURLOPT_HEADER, 0);
  }
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, "application/json;charset=UTF-8 user-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.25 Safari/537.36 Core/1.70.3741.400 QQBrowser/10.5.3863.400");
  if ($post) {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  }
  if ($cookie) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type:application/json;charset=UTF-8"));
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
  }
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
function get_url3($url, $post = false, $cookie = false, $header = false)
{
  $ch = curl_init();
  if ($header) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  } else {
    curl_setopt($ch, CURLOPT_HEADER, 0);
  }
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, $header);
  if ($post) {
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  }
  if ($cookie) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("content-type:application/json;charset=UTF-8"));
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
  }
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}
function wlog($uid, $type, $text, $money)
{
  global $DB;
  global $clientip;
  $a = $DB->get_row("select money from qingka_wangke_user where uid='{$uid}' ");
  $smoney = $a["money"];
  $DB->query("insert into qingka_wangke_log (uid,type,text,money,smoney,ip) values ('{$uid}','{$type}','{$text}','{$money}','{$smoney}','{$clientip}')  ");
}
function orderLogs($oid, $uid, $type, $text, $money)
{
  global $DB;
  global $clientip;
  $date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");
  $a = $DB->get_row("select money from qingka_wangke_user where uid='{$uid}' ");
  $smoney = $a["money"];

  $DB->query("insert into qingka_wangke_orderLogs (oid,uid,type,content,money,smoney,addtime) values ('{$oid}','{$uid}','{$type}','{$text}','{$money}','{$smoney}','{$date}') ");

}
function qcookie()
{
  global $DB;
  $a = $DB->query("select * from qingka_wangke_huoyuan where status=1 ");
  while ($b = $DB->fetch($a)) {
    loginWk($b["hid"]);
  }
}
function loginWk($hid)
{
  global $DB;
  $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$hid}' ");
  $type = $a["pt"];
  $user = $a["user"];
  $pass = $a["pass"];
  if (is_file(ROOT . '360safe/360webscan.php')) {
    require_once(ROOT . '360safe/360webscan.php');
  }
  if ($type == "27") {
    $data = array("uid" => $user, "key" => $pass);
    $data = json_encode($data, true);
    $result = get_url("http://wk.27sq.cn/api.php?act=getmoney", $data);
    $result = json_decode($result, true);
    $money = $result["money"];
    $DB->query("update qingka_wangke_huoyuan set money='{$money}' where hid='{$hid}' ");
  }
}
include('ckjk.php');
include('xdjk.php');
function get_cookie($url, $data)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  //curl_setopt($curl, CURLOPT_AUTOREFERER, 1);    
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($ch);
  curl_close($ch);
  $cki = (preg_match_all('|Set-Cookie: (.*);|U', $output));
  if ($cki == 1) {
    preg_match_all('|Set-Cookie: (.*);|U', $output, $arr);
  } else {
    preg_match_all('|set-cookie: (.*);|U', $output, $arr);
  }
  $cookies = implode(';', $arr[1]);
  return $cookies;
}
include('jdjk.php');
include('bsjk.php');
function post($url, $data, $header = [])
{

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  curl_setopt($ch, CURLOPT_USERAGENT, isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36');
  $output = curl_exec($ch);
  curl_close($ch);
  return $output;
}
function get_jnrturl($url, $post, $header)
{
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
  $result = curl_exec($ch);
  curl_close($ch);
  return $result;
}

// 发送邮件（不阻塞运行版本）
function emailGo($uid = 1, $f, $f_t, $f_c, $j, $type = '')
{
  global $conf;

  // 检测全局邮件发送状态
  if ($conf['smtp_open'] == 0) {
    return '已关闭全局邮件发送';
  }

  // uid、发送人的邮箱、发送的标题、发送的内容、接收人的邮箱、类型
  global $DB;
  global $date;

  $f_t = preg_replace('/\s*<(\w+)/', '<$1', $f_t);
  $f_c = preg_replace('/\s*<(\w+)/', '<$1', $f_c);

  $root = $_SERVER['DOCUMENT_ROOT'] ? $_SERVER['DOCUMENT_ROOT'] : dirname(dirname(__FILE__)) . '/';

  $exec_name0 = 'PHPMailer/fs.php';
  $exec_name2 = str_replace('/', '\/', $exec_name0);// 转义一下

  $exec_name = $root . '/' . $exec_name0;//需要运行的文件的路径
  // $command = 'nohup php  '.$exec_name.'';
  $command = 'nohup php ' . $exec_name . ' >/dev/null  &';

  exec("ps aux | grep 'php' | grep -v 'grep' | grep '" . escapeshellarg($exec_name2) . "' | awk '{print $2}'", $outputPids_a);
  $sCount = $DB->count("select count(*) from qingka_wangke_emails where status!=1");
  if ($sCount <= 10) {
    // 有多个进程就全部杀死后重启进程
    foreach ($outputPids_a as $key => $value) {
      exec("kill -9 $value");
    }
  }

  // 二次获取
  exec("ps aux | grep 'php' | grep -v 'grep' | grep '" . escapeshellarg($exec_name2) . "' | awk '{print $2}'", $outputPids);

  // echo json_encode(["sCount"=>$sCount<=0,"outputPids_a"=>$outputPids_a,"outputPids"=>$outputPids]);
  // exit(json_encode(["sCount"=>$sCount<=0,"outputPids_a"=>$outputPids_a,"outputPids"=>$outputPids]));
  // 添加到数据库
  $DB->query("insert into qingka_wangke_emails (uid,f,f_t,f_c,j,status,addtime,type) values ('{$uid}','{$f}','{$f_t}','{$f_c}','{$j}',0,'{$date}','{$type}') ");

  // 检测邮件发送进程数量
  if (count($outputPids) <= 0) {
    // 如果没有进程就启动
    exec($command, $output, $returnValue);
    // exec("ps aux | grep 'php' | grep -v 'grep' | grep '" . escapeshellarg($exec_name2) . "' | awk '{print $2}'", $outputPids);
    // exit(json_encode(["sCount"=>$sCount,"outputPids_a"=>$output,"outputPids"=>$returnValue]));
  } else if (count($outputPids) == 1) {
    // 有一个进程就不管
  } else {
    // 有多个进程就全部杀死后重启进程
    foreach ($outputPids as $key => $value) {
      exec("kill -9 $value");
    }
    exec($command, $output, $returnValue);
  }

}

class CardGenerator
{
  private $encryptionKey;

  public function __construct($encryptionKey)
  {
    $this->encryptionKey = $encryptionKey;
  }

  // 生成唯一的卡密
  private function generateUniqueCode()
  {
    return uniqid() . mt_rand(1000, 9999);
  }

  // 生成指定金额的卡密
  public function generateCard($amount, $count)
  {
    $cards = [];

    for ($i = 0; $i < $count; $i++) {
      $code = $this->generateUniqueCode();
      $encryptedAmount = $this->encryptAmount($amount);
      $cards[] = ['code' => $code, 'encryptedAmount' => $encryptedAmount];
    }

    return $cards;
  }

  // 加密金额
  private function encryptAmount($amount)
  {
    return openssl_encrypt($amount, 'aes-256-cbc', $this->encryptionKey, 0, substr($this->encryptionKey, 0, 16));
  }

  // 解密金额
  public function decryptAmount($encryptedAmount)
  {
    return openssl_decrypt($encryptedAmount, 'aes-256-cbc', $this->encryptionKey, 0, substr($this->encryptionKey, 0, 16));
  }
}

function readUIDS()
{

}