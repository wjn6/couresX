<?php

$act = $_GET['act'] ?? ''; 

include_once('../Checkorder/configuration.php');

$version = '2.0.0.5';

$host = $_SERVER['HTTP_HOST'];

$post_web =  $_POST['web'];
 
// 获取 POST 请求中的数据
$post_mysql =  $_POST['mysql'];
$host = $post_mysql['host'];
$port = $post_mysql['port'];
$user = $post_mysql['user'];
$pwd = $post_mysql['pwd'];
$dbname = $post_mysql['dbname'];

// 
if ($act !== 'mysql_c' && $act !== 'mysql_is' ) {
  $mysqli = new mysqli($host, $user, $pwd, $dbname, $port);
}

// 接口
switch ($act) {
    case 'mysql_is':
        $configFile = $_SERVER['DOCUMENT_ROOT'] . '/confing/config.php';
        $configContent = file_get_contents($configFile);
          preg_match("/\\\$host\\s*=\\s*([^;]+)/", $configContent, $host);
          preg_match("/\\\$port\\s*=\\s*([^;]+)/", $configContent, $port);
          preg_match("/\\\$user\\s*=\\s*([^;]+)/", $configContent, $user);
          preg_match("/\\\$pwd\\s*=\\s*([^;]+)/", $configContent, $pwd);
          preg_match("/\\\$dbname\\s*=\\s*([^;]+)/", $configContent, $dbname);
          
          $data = [
              "host" => trim($host[1], "'"),
              "port" => trim($port[1], "'"),
              "user"  => trim($user[1], "'"),
               "pwd"  => trim($pwd[1], "'"),
                "dbname"  => trim($dbname[1], "'"),
                
            ];
            
          if(empty($data["host"]) || empty($data["port"]) || empty($data["user"]) || empty($data["pwd"]) || empty($data["dbname"])  ){
               $data = [
              
              "host" => $post_mysql['host'],
              "port" => $post_mysql['port'],
              "user"  => $post_mysql['user'],
               "pwd"  => $post_mysql['pwd'],
                "dbname"  => $post_mysql['dbname'],
                
            ];
          }
          
                @$conn = new mysqli($data["host"], $data["user"], $data["pwd"], $data["dbname"],$data["port"]);
                
            if($conn->connect_error){
                 $data = [
              
              "host" => $post_mysql['host'],
              "port" => $post_mysql['port'],
              "user"  => $post_mysql['user'],
               "pwd"  => $post_mysql['pwd'],
                "dbname"  => $post_mysql['dbname'],
                
            ];
             @$conn = new mysqli($data["host"], $data["user"], $data["pwd"], $data["dbname"],$data["port"]);
             if($conn->connect_error){
                exit(json_encode(["code"=>-1,"msg"=>"false"]));
             }
            }
                
                // 获取授权码和网站信息
                $result = $conn -> query("select * from qingka_wangke_config where v in ('authcodes','sitename','verification','serverIP') ");
                $result_data = [];
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $result_data[$row["v"]] = $row;
                    }
                }
                $data["authcodes"] = $result_data["authcodes"]["k"];
                $data["sitename"] = $result_data["sitename"]["k"];
                $data["verification"] = $result_data["verification"]["k"];
                $data["serverIP"] = $result_data["serverIP"]["k"];
                
                // 获取管理员信息
                $result2 = $conn -> query("select user,pass from qingka_wangke_user where uid = '1'");
                $result2_data = [];
                if ($result2->num_rows > 0) {
                    while($row = $result2->fetch_assoc()) {
                        $result2_data[] = $row;
                    }
                }
                $result2_data = $result2_data[0];
                $data["admin_user"] = $result2_data["user"];
                $data["admin_pass"] = $result2_data["pass"];
                
                $conn->close();
                
                exit(json_encode(["code"=>1,"msg"=>"true","data"=>$data]));
            
        
        break;
  case 'mysql_c':
    // 更新 config.php 文件中的相应变量值
    $configFile = $_SERVER['DOCUMENT_ROOT'] . '/confing/config.php';

    $configContent = file_get_contents($configFile);
    $configContent = preg_replace("/\\\$host\\s*=\\s*([^;]+)/", "\$host = '$host'", $configContent);
    $configContent = preg_replace("/\\\$port\\s*=\\s*([^;]+)/", "\$port = $port", $configContent);
    $configContent = preg_replace("/\\\$user\\s*=\\s*([^;]+)/", "\$user = '$user'", $configContent);
    $configContent = preg_replace("/\\\$pwd\\s*=\\s*([^;]+)/", "\$pwd = '$pwd'", $configContent);
    $configContent = preg_replace("/\\\$dbname\\s*=\\s*([^;]+)/", "\$dbname = '$dbname'", $configContent);

    file_put_contents($configFile, $configContent);
    if (
      preg_match("/\\\$dbname\\s*=\\s*'([^']+)'/", $configContent, $matchesDbname) &
      preg_match("/\\\$user\\s*=\\s*'([^']+)'/", $configContent, $matchesDbname) &
      preg_match("/\\\$pwd\\s*=\\s*'([^']+)'/", $configContent, $matchesDbname) &
      preg_match("/\\\$host\\s*=\\s*'([^']+)'/", $configContent, $matchesDbname) &
      preg_match("/\\\$port\\s*=\\s*'([^']+)'/", $configContent, $matchesDbname)
    ) {
      exit(json_encode(["code" => 1]));
    } else {
      exit(json_encode(["code" => 1]));
    }

    break;
  case 'mysql_ping':
    // 检测数据库是否
    if ($mysqli->connect_errno) {
      exit(json_encode(['code' => -1]));
    } else {
      exit(json_encode(['code' => 1]));
    }
    break;
  case 'mysql_sc':

    // 定义表结构的多维数组
    $tableStructure = array(
      'qingka_wangke_config' => array(
        'v' => 'VARCHAR(180) COLLATE utf8mb4_unicode_ci PRIMARY KEY ',
        'k' => 'TEXT COLLATE utf8mb4_unicode_ci',
      ),
      'qingka_wangke_class' => array(
        'cid' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'sort' => 'INT(50) DEFAULT 10',
        'name' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "网课平台名字" ',
        'getnoun' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "查询参数" ',
        'noun' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "对接参数" ',
        'nocheck' => 'INT(11) COMMENT "是否无查下单" DEFAULT 0',
        'changePass' => 'INT(11) COMMENT "是否支持改密" DEFAULT 0',
        'price' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "定价" ',
        'queryplat' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "查询平台" ',
        'docking' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "对接平台" ',
        'yunsuan' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "代理费率运算" DEFAULT "*" ',
        'content' => 'VARCHAR(1000) COLLATE utf8mb4_unicode_ci COMMENT "说明" ',
        'addtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "添加时间" ',
        'uptime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "更新时间" ',
        'status' => 'INT(11) COMMENT "状态0为下架。1为上架" DEFAULT 1',
        'fenlei' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci COMMENT "分类" ',
        'mall_custom' => 'MEDIUMTEXT COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_dengji' => array(
        'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'sort' => 'INT(11) COLLATE utf8mb4_unicode_ci DEFAULT 0',
        'name' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'rate' => 'DECIMAL(10,2)',
        'money' => 'DECIMAL(10,2)',
        'addkf' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci DEFAULT 1',
        'gjkf' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci DEFAULT 1',
        'czAuth' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci DEFAULT 0',
        'status' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'time' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_fenlei' => array(
        'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'status' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'sort' => 'INT(11) COLLATE utf8mb4_unicode_ci DEFAULT 0',
        'name' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci',
        'time' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'mall_custom' => 'MEDIUMTEXT COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_gongdan' => array(
        'gid' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'uid' => 'INT(3)',
        'region' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "工单类型" ',
        'title' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "工单标题" ',
        'content' => 'VARCHAR(6666) COLLATE utf8mb4_unicode_ci COMMENT "工单内容" ',
        'state' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci COMMENT "工单状态" ',
        'addtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "添加时间" ',
      ),
      'qingka_wangke_help' => array(
        'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'title' => 'VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'content' => 'TEXT COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'addTime' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'upTime' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'ip' => 'VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'sort' => 'VARCHAR(500) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'status' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'readUIDS' => 'VARCHAR(4000) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_huoyuan' => array(
        'hid' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'pt' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'name' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'url' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "不带http|https 顶级" ',
        'user' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'pass' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'token' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'ip' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'cookie' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'money' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'status' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT 1 ',
        'addtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'endtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        
        'ckjk' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "查课接口" ',
        'ck_post' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "查课请求方式" DEFAULT 1 ',
        'ckcs' => 'VARCHAR(2000) COLLATE utf8mb4_unicode_ci COMMENT "查课参数" ',
        'ck_okcode' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "查课成功Code" DEFAULT 1 ',
        'ck_datakey' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "查课数据键" DEFAULT "data" ',
        'ck_kcnamekey' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "查课数据课程名称键" DEFAULT "name" ',
        'ck_kcidkey' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "查课数据课程ID键" DEFAULT "id" ',
        
        'xdjk' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "下单接口" ',
        'xd_post' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "下单请求方式" DEFAULT 1 ',
        'xdcs' => 'VARCHAR(2000) COLLATE utf8mb4_unicode_ci COMMENT "下单参数" ',
        'xd_okcode' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "下单成功Code" DEFAULT 0 ',
        'xd_yidkey' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "下单成功上游的订单ID键" DEFAULT "id" ',
        
        'jdjk' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "同步进度接口" ',
        'jd_post' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "同步进度请求方式" DEFAULT 1 ',
        'jdcs' => 'VARCHAR(2000) COLLATE utf8mb4_unicode_ci COMMENT "同步进度参数" ',
        'jd_okcode' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "同步进度成功Code" DEFAULT 1 ',
        'jd_datakey' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键" DEFAULT "data" ',
        'jd_datakey_kcname' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键-课程名称" DEFAULT "kcname" ',
        'jd_datakey_status' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键-状态" DEFAULT "status" ',
        'jd_datakey_process' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键-进度" DEFAULT "process" ',
        'jd_datakey_remarks' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键-日志" DEFAULT "remarks" ',
        'jd_datakey_kcks' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键-课程开始时间" DEFAULT "courseStartTime" ',
        'jd_datakey_kcjs' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键-课程结束时间" DEFAULT "courseEndTime" ',
        'jd_datakey_ksks' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键-考试开始时间" DEFAULT "examStartTime" ',
        'jd_datakey_ksjs' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT "同步进度数据键-考试结束时间" DEFAULT "examEndTime" ',
        
        'bsjk' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "补刷接口" ',
        'bs_post' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "补刷请求方式" DEFAULT 1 ',
        'bscs' => 'VARCHAR(2000) COLLATE utf8mb4_unicode_ci COMMENT "补刷参数" ',
        'bs_okcode' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "补刷成功Code" DEFAULT 1 ',
        
        'post' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT 1 ',
        'changePass_type' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci COMMENT "改密请求方式" DEFAULT 1',
        'changePass_jk' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'changePass_cs' => 'VARCHAR(2000) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'smtp_money' => 'INT(11) DEFAULT 15 ',
      ),
      'qingka_wangke_log' => array(
        'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'uid' => 'INT(11)',
        'type' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'text' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'money' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'smoney' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'ip' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'addtime' => 'TIMESTAMP ON UPDATE CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT "添加时间" ',
      ),
      'qingka_wangke_mijia' => array(
        'mid' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'uid' => 'INT(11) ',
        'cid' => 'INT(11) ',
        'mode' => 'INT(11) COMMENT "0.价格的基础上扣除 1.倍数的基础上扣除 2.直接定价" ',
        'price' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'addtime' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'uptime' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_pay' => array(
        'oid' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'out_trade_no' => 'VARCHAR(64) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'trade_no' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'type' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'uid' => 'INT(11) ',
        'num' => 'INT(11) DEFAULT 1',
        'addtime' => 'DATETIME ',
        'endtime' => 'DATETIME ',
        'name' => 'VARCHAR(64) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'money' => 'VARCHAR(32) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'money2' => 'VARCHAR(32) COLLATE utf8mb4_unicode_ci COMMENT "代理商城收支" ',
        'domain' => 'VARCHAR(64) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'status' => 'INT(11) ',
        'payUser' => 'VARCHAR(200) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'ip' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_user' => array(
        'uid' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'uuid' => 'INT(11) ',
        'user' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'pass' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'name' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'qq_openid' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "QQUID" ',
        'nickname' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "QQ昵称" ',
        'faceimg' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "QQ头像" ',
        'money' => 'DECIMAL(10,3) DEFAULT "0.00" ',
        'zcz' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT "0" ',
        'addprice' => 'DECIMAL(10,2) COMMENT "加价" DEFAULT "1.00" ',
        '`key`' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'yqm' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "邀请码" ',
        'yqprice' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "邀请单价" DEFAULT "0.20" ',
        'notice' => 'TEXT COLLATE utf8mb4_unicode_ci',
        'addtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "添加/注册时间" ',
        'endtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "最后一次登录时间" ',
        'ip' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "当前登录ip" ',
        'endip' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "最后一次登录ip" ',
        'endaddress' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "上次登录地址" ',
        'grade' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'active' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT 1',
        'qq' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT 123456',
        'wx' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT 123456',
        'tourist' => 'INT(2) DEFAULT 0',
        'ck' => 'INT(11) DEFAULT 0',
        'xd' => 'INT(11) DEFAULT 0',
        'jd' => 'INT(11) DEFAULT 0',
        'bs' => 'INT(11) DEFAULT 0',
        'ck1' => 'INT(11) DEFAULT 0',
        'xd1' => 'INT(11) DEFAULT 0',
        'jd1' => 'INT(11) DEFAULT 0',
        'bs1' => 'INT(11) DEFAULT 0',
        'paydata' => 'TEXT COLLATE utf8mb4_unicode_ci COMMENT "支付独立配置" ',
        'touristdata' => 'TEXT COLLATE utf8mb4_unicode_ci COMMENT "商城独立配置" ',
        'czAuth' => 'VARCHAR(11) COLLATE utf8mb4_unicode_ci COMMENT "在线充值权限" DEFAULT 0',
      ),
      'qingka_wangke_order' => array(
        'oid' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'uid' => 'INT(11) ',
        'cid' => 'INT(11) COMMENT "平台ID" ',
        'hid' => 'INT(11) COMMENT "接口ID" ',
        'yid' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "对接站ID" ',
        'ptname' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "平台名字" ',
        'school' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "学校" ',
        'name' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "姓名" ',
        'user' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "账号" ',
        'pass' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "密码" ',
        'phone' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "手机号" ',
        'kcid' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "课程ID" ',
        'kcname' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "课程名字" ',
        'courseStartTime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "课程开始时间" ',
        'courseEndTime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "课程结束时间" ',
        'examStartTime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "考试开始时间" ',
        'examEndTime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "考试结束时间" ',
        'chapterCount' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "总章数" ',
        'unfinishedChapterCount' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "剩余章数" ',
        'cookie' => 'TEXT COLLATE utf8mb4_unicode_ci COMMENT "cookie" ',
        'fees' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "扣费" ',
        'shoujia' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "商城售价" ',
        'noun' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "对接标识" ',
        'miaoshua' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "0不秒 1秒" DEFAULT 0 ',
        'addtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "添加时间" ',
        'ip' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "下单ip" ',
        'dockstatus' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "对接状态 0待 1成 2失 3重复 4取消" DEFAULT 0 ',
        'loginstatus' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "" ',
        'status' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "" DEFAULT "待处理" ',
        'process' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "" ',
        'bsnum' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "补刷次数" DEFAULT 0 ',
        'remarks' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "备注" ',
        'uptime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "" ',
        'qg' => 'TEXT COLLATE utf8mb4_unicode_ci COMMENT "" ',
        'out_trade_no' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "" ',
        'paytime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "" ',
        'payUser' => 'VARCHAR(200) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'type' => 'VARCHAR(200) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_kami' => array(
        'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'codeKey' => 'VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'code' => 'VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'price' => 'VARCHAR(6000) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'onlynum' => 'VARCHAR(6000) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'status' => 'VARCHAR(50) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'user' => 'VARCHAR(250) COLLATE utf8mb4_unicode_ci COMMENT "使用者" ',
        'user_uid' => 'VARCHAR(250) COLLATE utf8mb4_unicode_ci COMMENT "使用者UID"  ',
        'usetime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "使用时间" ',
        'addtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'uptime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'endtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_homenotice' => array(
        'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY', 
        'sort' => 'INT(50) DEFAULT 10',
        'status' => 'VARCHAR(20) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'title' => 'VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'content' => 'VARCHAR(4000) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'readUIDS' => 'VARCHAR(4000) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'author' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'top' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'addtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
        'uptime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT "" ',
      ),
      'qingka_wangke_emails' => array(
        'eid' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
        'uid' => 'INT(11) ',
        'cpid' => 'INT(11) ',
        'status' => 'VARCHAR(10) COLLATE utf8mb4_unicode_ci DEFAULT 0 COMMENT "发送状态" ',
        'status_t' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "状态备注" ',
        'f' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "发送人账号" ',
        'f_t' => 'TEXT COLLATE utf8mb4_unicode_ci COMMENT "发送标题" ',
        'f_c' => 'TEXT COLLATE utf8mb4_unicode_ci COMMENT "发送内容" ',
        'j' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "接收人账号" ',
        'type' => 'VARCHAR(100) COLLATE utf8mb4_unicode_ci COMMENT "标识" ',
        'addtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "添加时间" ',
        'endtime' => 'VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT "发送时间" ',
      ),
    ); 

    // 初始化成功和失败表信息的数组
    $successTables = array();
    $failedTables = array();

    $qingka_wangke_config_Data = [
      [
        "v" => 'version',
        "k" => $version,
      ],
      [
        "v" => 'paolu',
        "k" => '0',
      ],
      [
        "v" => 'paolu_t',
        "k" => '我们跑路啦~有缘江湖再见',
      ],
      [
        "v" => 'paolu_u',
        "k" => 'paolu.php',
      ],
      [
        "v" => 'authcodes',
        "k" => $post_web['authcodes'],
      ],
      [
        "v" => 'akcookie',
        "k" => '7321sPn6n+Yt9tGs1wy7f2ULOKbENP2W/J83w50jYbpDpQEXjkGRJnZOlXPY7XeOX5zCSU6vfhOLJSoKLMeWQ7cv9ghbEsFowYoCzQ',
      ],
      [
        "v" => 'login_etime',
        "k" => '2880',
      ],
      [
        "v" => 'menuList',
        "k" => '[{ "title": "首页", "children": [], "icon": "layui-icon layui-icon-home", "href": "home1", "admin": false, "hidden": false, "type": "shou3ye4_home" }, { "title": "TocAI(免费GPT)", "children": [], "icon": "layui-icon layui-icon-senior", "href": "https://chatttttttttttttttttttt.prowk.top/", "hidden": true, "admin": false, "type": "TocAI(mian3fei4GPT)_344" }, { "title": "后台管理", "icon": "layui-icon layui-icon-set", "children": [{ "title": "网站管理", "icon": "", "href": "", "children": [{ "title": "网站设置", "icon": "", "href": "wzsz", "children": [] }, { "title": "帮助设置", "icon": "", "href": "helpsz", "children": [], "type": "bang1zhu4she4zhi4_692" }], "type": "wang3zhan4guan3li3_302" }, { "title": "商品管理", "icon": "", "href": "", "children": [{ "title": "对接设置", "icon": "", "href": "djsz", "children": [], "type": "dui4jie1she4zhi4_491" }, { "title": "分类设置", "icon": "", "href": "flsz", "children": [], "type": "fen1lei4she4zhi4_729" }, { "title": "商品设置", "icon": "", "href": "spsz", "children": [], "type": "shang1pin3she4zhi4_392" }, { "title": "密价设置", "icon": "", "href": "mjsz", "children": [], "type": "mi4jia4she4zhi4_767" }], "type": "shang1pin3guan3li3_225" }, { "title": "代理等级", "icon": "", "href": "dldj", "children": [], "type": "dai4li3deng3ji2_973" }, { "title": "卡密管理", "icon": "", "href": "kamisz", "children": [], "type": "ka3mi4guan3li3_74" }], "admin": true, "type": "hou4tai2guan3li3_61" }, { "title": "学习中心", "icon": "layui-icon layui-icon-component", "href": "", "children": [{ "title": "提交订单", "icon": "", "href": "add_pl", "children": [], "admin": false, "type": "ti2jiao1ding4dan1_796" }, { "title": "订单管理", "icon": "", "href": "list", "children": [], "type": "ding4dan1guan3li3_848" }, { "title": "接单商城", "icon": "", "href": "tourist_spsz", "children": [], "type": "jie1dan1shang1cheng2_892" }, { "title": "强国学习", "icon": "", "href": "", "children": [{ "title": "ax强国", "icon": "", "href": "axqg", "children": [], "type": "axqiang2guo2_176" }], "type": "qiang2guo2xue2xi2_169" }], "type": "xue2xi2zhong1xin1_803" }, { "title": "个人中心", "icon": "layui-icon layui-icon-username", "href": "", "children": [{ "title": "个人信息", "icon": "", "href": "userinfo", "children": [], "type": "ge4ren2xin4xi1_185" }, { "title": "操作日志", "icon": "", "href": "log", "children": [], "type": "cao1zuo4ri4zhi4_412" }], "type": "ge4ren2zhong1xin1_37" }, { "title": "在线充值", "icon": "layui-icon layui-icon-rmb", "href": "pay", "children": [], "type": "zai4xian4chong1zhi2_589" }, { "title": "提交工单", "icon": "layui-icon layui-icon-survey", "href": "gongdan", "children": [], "type": "ti2jiao1gong1dan1_317" }, { "title": "代理管理", "icon": "layui-icon layui-icon-user", "href": "userlist", "children": [], "type": "dai4li3guan3li3_443" }, { "title": "便携功能", "icon": "layui-icon layui-icon-find-fill", "href": "", "children": [{ "title": "销量热榜", "icon": "", "href": "hots", "children": [], "type": "xiao1liang4re4bang3_672" }, { "title": "学习价格", "icon": "", "href": "myprice", "children": [], "type": "xue2xi2jia4ge2_320" }, { "title": "对接文档", "icon": "", "href": "docking", "children": [], "type": "dui4jie1wen2dang4_494" }], "type": "bian4xie2gong1neng2_578" }, { "title": "帮助文档", "icon": "layui-icon layui-icon-form", "href": "help", "children": [], "type": "bang1zhu4wen2dang4_136" }, { "title": "云端任务", "icon": "layui-icon layui-icon-template-1", "href": "btManage", "children": [], "admin": true, "type": "yun2duan1ren4wu0_191" }, { "title": "内置图标", "icon": "layui-icon layui-icon-face-surprised", "href": "components/iconPreview", "children": [], "admin": true, "type": "nei4zhi4tu2biao1_14" }]',
      ],
      [
        "v" => 'f_homePath',
        "k" => '/components/indexDefault.php',
      ],
      [
        "v" => 'homePath',
        "k" => 'home.php',
      ],
      [
        "v" => 'storePath',
        "k" => '/components/onlineStore.php',
      ],
      [
        "v" => 'api_ck',
        "k" => '15',
      ],
      [
        "v" => 'api_ckkf',
        "k" => '0',
      ],
      [
        "v" => 'api_ck_threshold',
        "k" => '30',
      ], 
      [
        "v" => 'api_proportion',
        "k" => '20',
      ],
      [
        "v" => 'api_xd',
        "k" => '5',
      ],
      [
        "v" => 'api_bs',
        "k" => '10',
      ],
      [
        "v" => 'axqg',
        "k" => '1',
      ],
      [
        "v" => 'axqg_type',
        "k" => '0',
      ],
      [
        "v" => 'axqg_price',
        "k" => '1',
      ],
      [
        "v" => 'axqg_url',
        "k" => '',
      ],
      [
        "v" => 'axqg_uid',
        "k" => '',
      ],
      [
        "v" => 'axqg_token',
        "k" => '',
      ],
      [
        "v" => 'gpt',
        "k" => '1',
      ],
      [
        "v" => 'gpt_url',
        "k" => 'components/tocai',
      ],
      [
        "v" => 'gpt_notice',
        "k" => '每日只有100次免费免费调用额度，请勿滥使用！',
      ],
      [
        "v" => 'fontsZDY',
        "k" => '1',
      ],
      [
        "v" => 'fontsZDY_jscss',
        "k" => '<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC&display=swap" rel="stylesheet">',
      ],
      [
        "v" => 'fontsFamily',
        "k" => 'Noto Serif SC, serif',
      ],
      [
        "v" => 'description',
        "k" => 'CourseX',
      ],
      [
        "v" => 'dklcookie',
        "k" => '',
      ],
      [
        "v" => 'dlgl_notice_open',
        "k" => 'on',
      ],
      [
        "v" => 'dlgl_notice',
        "k" => '充值扣费：扣除费用=充值金额*(我的总价格/代理价格)<br />改价手续费：改价一次需要[user_ktmoney]元手续费<br />下级费率不能高于你的费率',
      ],
      [
        "v" => 'epay_zs_open',
        "k" => '1',
      ],
      [
        "v" => 'epay_zs',
        "k" => '[{"min":0,"max":"9.99","zsprice":0},{"min":10,"max":99.99,"zsprice":3},{"min":100,"max":199.99,"zsprice":6},{"min":200,"max":299.99,"zsprice":10}]',
      ],
      [
        "v" => 'epay_protocol',
        "k" => 'https',
      ],
      [
        "v" => 'epay_api',
        "k" => 'https://pay.xymzf.cn/',
      ],
      [
        "v" => 'epay_key',
        "k" => '',
      ],
      [
        "v" => 'epay_pid',
        "k" => '',
      ],
      [
        "v" => 'flkg',
        "k" => '1',
      ],
      [
        "v" => 'fllx',
        "k" => '2',
      ],
      [
        "v" => 'pc_flhnum',
        "k" => '6',
      ],
      [
        "v" => 'xs_flhnum',
        "k" => '4',
      ],
      [
        "v" => 'xdsmopen',
        "k" => '0',
      ],
      [
        "v" => 'is_alipay',
        "k" => '1',
      ],
      [
        "v" => 'is_qqpay',
        "k" => '0',
      ],
      [
        "v" => 'is_wxpay',
        "k" => '0',
      ],
      [
        "v" => 'keywords',
        "k" => 'CourseX',
      ],
      [
        "v" => 'login_apiurl',
        "k" => '',
      ],
      [
        "v" => 'login_appid',
        "k" => '',
      ],
      [
        "v" => 'login_appkey',
        "k" => '',
      ],
      [
        "v" => 'logo',
        "k" => '/CourseXLogo.png',
      ],
      [
        "v" => 'login_logo',
        "k" => '/assets/images/CourseXLogo.png',
      ],
      [
        "v" => 'login_banner',
        "k" => '/assets/images/login_banner.jpg',
      ],
      [
        "v" => 'cb_logo',
        "k" => '/favicon.ico',
      ],
      [
        "v" => 'nanatoken',
        "k" => '',
      ],
      [
        "v" => 'notice_open',
        "k" => 'on',
      ],
      [
        "v" => 'onlineStore_open',
        "k" => '0',
      ],
      [
        "v" => 'onlineStore_trdltz',// 右上方代理跳转
        "k" => '1',
      ],
      [
        "v" => 'onlineStore_add',
        "k" => '3',
      ],
      [
        "v" => 'notice',
        "k" => '<div class="layui-timeline">
  <div class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis layui-icon-fire"></i>
    <div class="layui-timeline-content layui-text" >
      <h4 class="layui-timeline-title" style="color: #796f6f;">网站更新</h4 >
      <p class="" style="color: #696969;">
        1.上架全网最低价的【坤坤】项目，超级低价，质量的话自测，内部同行说还可以
      </p>
    </div>
  </div>
  <div class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis layui-icon-fire"></i>
    <div class="layui-timeline-content layui-text" >
      <h4 class="layui-timeline-title" style="color: #796f6f;">网站更新</h4 >
      <p class="" style="color: #696969;">
        1. 新增下单邮箱推送功能，代理下单后将推送下单信息到代理邮箱；<br />
        2. 优化下单查询课程流程，现在多账号会依次查询完一个后再查询下一个，不会再并发
      </p>
    </div>
  </div>
  <div class="layui-timeline-item">
    <i class="layui-icon layui-timeline-axis layui-icon-fire"></i>
    <div class="layui-timeline-content layui-text" >
      <h4 class="layui-timeline-title" style="color: #796f6f;">上架呱呱分类</h4 >
      <p class="" style="color: #696969;">
        上架目前市面上很火的高质量呱呱学习通，支持更多章测、考试、作业类型，通杀选修课，必修专业课部分也可以解决。
一秒考试、继教、剑指等各种有难度的题目类型都可以解决，欢迎各位下单自测！
      </p>
    </div>
  </div>

</div>',
      ],
      [
        "v" => 'qd_notice_open',
        "k" => 'on',
      ],
      [
        "v" => 'qd_notice',
        "k" => '<div style="padding: 5px">
！！学习通、智慧树用手机号查，不要用学号！！
<hr/>
学习通/超星：<br />
知到/智慧树：<br />
U校园：<br />
智慧职教：<br />
中国大学：<br />
冷门：
</div>',],
      [
        "v" => 'login_top_notice_open',
        "k" => 'on',
      ],
      [
        "v" => 'login_top_notice',
        "k" => '欢迎使用本站服务！若无法登录请联系上级！',
      ],
      [
        "v" => 'home_top_notice_open',
        "k" => 'on',
      ],
      [
        "v" => 'home_top_notice',
        "k" => '这是重要公告，注意长度！',
      ],
      [
        "v" => 'settings',
        "k" => '1',
      ],
      [
        "v" => 'sitename',
        "k" => 'CourseX',
      ],
      [
        "v" => 'subsitename',
        "k" => '',
      ],
      [
        "v" => 'themesData',
        "k" => '[{"id":"dark","name":"暗影黑","url":"/assets/theme/toc_theme_dark.css","code":"dark","c1":"#202020","c2":"#16b777","author":"CourseX"},{"id":"light","name":"白月光","url":"","code":"light","c1":"#ffffff","c2":"#333A42","author":"CourseX"}]',
      ],
      [
        "v" => 'themesData_default',
        "k" => 'light',
      ],
      [
        "v" => 'sjqykg',
        "k" => '0',
      ],
      [
        "v" => 'sykg',
        "k" => '0',
      ],
      [
        "v" => 'webVfx_open',
        "k" => '1',
      ],
      [
        "v" => 'webVfx',
        "k" => '<script src="/assets/webVfx/shehuizhuyi.js"></script>
<script src="/assets/webVfx/inputParticles.js"></script>',
      ],
      [
        "v" => 'tcgonggao_open',
        "k" => 'on',
      ],
      [
        "v" => 'tcgonggao',
        "k" => '<div style="padding: 5px"><p>推荐电脑端操作，电脑网页效果最佳~</p><p>多来点单，爸爸们！！！</p><p>客户查进度网址：<br />https://域名/query?t=你的uid</p></div>',
      ],
      [
        "v" => 'user_htkh',
        "k" => '1',
      ],
      [
        "v" => 'user_ktmoney',
        "k" => '0',
      ],
      [
        "v" => 'user_pass',
        "k" => 'abc123456',
      ],
      [
        "v" => 'user_yqzc',
        "k" => '1',
      ],
      [
        "v" => 'verification',
        "k" => $post_web['verification'],
      ],
      [
        "v" => 'serverIP',
        "k" => $post_web['serverIP'],
      ],
      [
        "v" => 'serverIP_type',
        "k" => '0',
      ],
      [
        "v" => 'serverIP_uid',
        "k" => '1',
      ],
      [
        "v" => 'zdpay',
        "k" => '10',
      ],
      [
        "v" => 'emails',
        "k" => '[]',
      ],
      [
        "v" => 'smtp_open',
        "k" => '1',
      ],
      [
        "v" => 'smtp_open_login',
        "k" => '1',
      ],
      [
        "v" => 'smtp_open_xd',
        "k" => '0',
      ],
      [
        "v" => 'smtp_open_gd',
        "k" => '1',
      ],
      [
        "v" => 'smtp_open_cz',
        "k" => '1',
      ],
      [
        "v" => 'smtp_open_huo',
        "k" => '1',
      ],
      [
        "v" => 'smtp_host',
        "k" => 'smtp.qq.com',
      ],
      [
        "v" => 'smtp_port',
        "k" => '465',
      ],
      [
        "v" => 'smtp_user',
        "k" => '',
      ],
      [
        "v" => 'smtp_pass',
        "k" => '',
      ],
      [
        "v" => 'smtp_secure',
        "k" => 'ssl',
      ],
      [
        "v" => 'smtp_cuser',
        "k" => '',
      ],
      [
        "v" => 'useF12_d',
        "k" => '',
      ],
      [
        "v" => 'chadan_open',
        "k" => '1',
      ],
      [
        "v" => 'chadan_bs',
        "k" => '1',
      ],
      [
        "v" => 'chadan_default',
        "k" => '',
      ],
      [
        "v" => 'chadan_t_notice',
        "k" => '',
      ],
      [
        "v" => 'bt_token',
        "k" => '',
      ],
      [
        "v" => 'bt_panel',
        "k" => '',
      ],
      [
        "v" => 'bs0_rw',
        "k" => '已退款|待支付|已取消|待审核|接码',
      ],
      [
        "v" => 'bs_cl',
        "k" => '1',
      ],
      [
        "v" => 'bs0_rw',
        "k" => '',
      ],
      [
        "v" => 'bs0_rw',
        "k" => '',
      ],
      [
        "v" => 'bs0_rw',
        "k" => '',
      ],
      [
        "v" => 'lanyangyang_fenlei',
        "k" => '',
      ],
      [
        "v" => 'lanyangyang_huoyuan',
        "k" => '',
      ],
      [
        "v" => 'lanyangyang_factor',
        "k" => '1',
      ]
    ];

    // 检测并创建表和插入数据
    foreach ($tableStructure as $tableName => $columns) {
      // 检查表是否存在
      $tableExists = $mysqli->query("SHOW TABLES LIKE '$tableName'")->num_rows > 0;
      // 如果type未设置或type为fg，则进行覆盖创建
      if (!isset($_POST['type']) || $_POST['type'] === 'fg') {
        // 如果表已存在，则删除表
        if ($tableExists) {
          $dropTableSQL = "DROP TABLE $tableName";
          $mysqli->query($dropTableSQL);
        }

        // 创建表的 SQL 命令
        $createTableSQL = "CREATE TABLE IF NOT EXISTS $tableName (";
        foreach ($columns as $columnName => $columnType) {
          $createTableSQL .= "$columnName $columnType, ";
        }
        $createTableSQL = rtrim($createTableSQL, ', '); // 移除末尾的逗号和空格
        $createTableSQL .= ")";

        // 执行创建表的 SQL 命令
        if ($mysqli->query($createTableSQL) === TRUE) {
          $successTables[] = array('type' => '成功', 'tableName' => $tableName, 'num' => 1);
        } else {
          $errorMessage = $mysqli->error;
          $failedTables[] = array('type' => '失败1', 'tableName' => $createTableSQL, 'num' => $errorMessage);
        }

        // 如果表名为'qingka_wangke_config'，则插入数据
        if ($tableName === 'qingka_wangke_config') {
          $data = $qingka_wangke_config_Data; // 假设这里是您的数据，格式为 [{v:1,k:2},{v:2,k:3},...]
          foreach ($data as $item) {
            $v = $mysqli->real_escape_string($item['v']);
            $k = $mysqli->real_escape_string($item['k']);
            if($item['v']==='webVfx'){
                $k = $item['k'];
            }
            $insertDataSQL = "INSERT INTO $tableName (v, k) VALUES ('$v', '$k')";
            if ($mysqli->query($insertDataSQL) === TRUE) {
              $successTables[] = array('type' => '成功', 'tableName' => $tableName, 'num' => 1);
            } else {
              $errorMessage = $mysqli->error;
              $failedTables[] = array('type' => '失败', 'tableName' => $tableName, 'num' => 1);
            }
          }
        }
        
        if($tableName === "qingka_wangke_user"){
            $mysqli->query("INSERT INTO qingka_wangke_user (uid,uuid,user,pass,name,money,zcz,addprice,yqprice,active) VALUES ('1','1','{$post_web["user"]}','{$post_web["pass"]}','admin','999999','999999','0.2','0.2','1')");
        }
        
      } elseif ($_POST['type'] === 'xz') { // 如果type为xz，则进行新增创建
        // 如果表不存在，则进行创建
        if (!$tableExists) {
          // 创建表的 SQL 命令
          $createTableSQL = "CREATE TABLE IF NOT EXISTS $tableName (";
          foreach ($columns as $columnName => $columnType) {
            $createTableSQL .= "$columnName $columnType, ";
          }
          $createTableSQL = rtrim($createTableSQL, ', '); // 移除末尾的逗号和空格
          $createTableSQL .= ")";

          // 执行创建表的 SQL 命令
          if ($mysqli->query($createTableSQL) === TRUE) {
            $successTables[] = array('type' => '成功', 'tableName' => $tableName, 'num' => 1);
          } else {
            $errorMessage = $mysqli->error;
            $failedTables[] = array('type' => '失败1', 'tableName' => $tableName, 'num' => $errorMessage);
          }

          // 如果表名为'qingka_wangke_config'，则插入数据
          if ($tableName === 'qingka_wangke_config') {
            $data = $qingka_wangke_config_Data; // 假设这里是您的数据，格式为 [{v:1,k:2},{v:2,k:3},...]
            foreach ($data as $item) {
              $v = $mysqli->real_escape_string($item['v']);
              $k = $mysqli->real_escape_string($item['k']);
              if($item['v']==='webVfx'){
                $k = $item['k'];
            }
              $insertDataSQL = "INSERT INTO $tableName (v, k) VALUES ('$v', '$k')";
              if ($mysqli->query($insertDataSQL) === TRUE) {
                $successTables[] = array('type' => '成功', 'tableName' => $tableName, 'num' => 1);
              } else {
                $errorMessage = $mysqli->error;
                $failedTables[] = array('type' => '失败2', 'tableName' => $tableName, 'num' => $errorMessage);
              }
            }
          }
        } else { // 如果表存在，则进行新增操作
          // 获取已存在的列名
          $existingColumns = array();
          $result = $mysqli->query("SHOW COLUMNS FROM $tableName");
          while ($row = $result->fetch_assoc()) {
            $existingColumns[] = $row['Field'];
          }

          // 检查并添加尚未存在的列
          foreach ($columns as $columnName => $columnType) {
            if (!in_array($columnName, $existingColumns)) {
              $addColumnSQL = "ALTER TABLE $tableName ADD COLUMN $columnName $columnType";
              if ($mysqli->query($addColumnSQL) === TRUE) {
                $successTables[] = array('type' => '成功', 'tableName' => $tableName, 'num' => 1);
              } else {
                $errorMessage = $mysqli->error;
                $failedTables[] = array('type' => '失败3', 'tableName' => $columnName, 'num' => $errorMessage);
              }
            }
          }

          // 如果表名为'qingka_wangke_config'，则插入数据
          if ($tableName === 'qingka_wangke_config') {
            $data = $qingka_wangke_config_Data; // 假设这里是您的数据，格式为 [{v:1,k:2},{v:2,k:3},...]
            foreach ($data as $item) {
              $v = $mysqli->real_escape_string($item['v']);
              $k = $mysqli->real_escape_string($item['k']);
              if($item['v']==='webVfx'){
                $k = $item['k'];
            }
              // 检查是否已存在相同的 v 值
              $checkExistSQL = "SELECT COUNT(*) AS count FROM $tableName WHERE v = '$v'";
              $result = $mysqli->query($checkExistSQL);
              $row = $result->fetch_assoc();
              if ($row['count'] == 0) { // 如果不存在相同的 v 值，则插入数据
                $insertDataSQL = "INSERT INTO $tableName (v, k) VALUES ('$v', '$k')";
                if ($mysqli->query($insertDataSQL) === TRUE) {
                  $successTables[] = array('type' => '成功', 'tableName' => $tableName, 'num' => 1);
                } else {
                  $errorMessage = $mysqli->error;
                  $failedTables[] = array('type' => '失败4', 'tableName' => $tableName, 'num' => $errorMessage);
                }
              }
            }
          }
          
        }
        
        // 增量安装模式强制更新  
        if (!$tableExists){
         // 表不存在
        }else{
         // 表存在
         
             $mysqli->query("update qingka_wangke_config set k='{$version}' where v='version' limit 1 ");
            $mysqli->query("update qingka_wangke_config set k='{$post_web["sitename"]}' where v='sitename' limit 1 ");
            
            $mysqli->query("update qingka_wangke_config set k='{$post_web["authcodes"]}' where v='authcodes' limit 1 ");
            
            $mysqli->query("update qingka_wangke_config set k='{$post_web["verification"]}' where v='verification' limit 1 ");
            
            $mysqli->query("update qingka_wangke_config set k='{$post_web["serverIP"]}' where v='serverIP' limit 1 ");
            
            $mysqli->query("ALTER TABLE qingka_wangke_homenotice MODIFY COLUMN content TEXT COLLATE utf8mb4_unicode_ci");
            
            // 设置qingka_wangke_user表money的长度/值为10,3
            $mysqli->query("ALTER TABLE qingka_wangke_user CHANGE money money DECIMAL(10,3) DEFAULT 0.00");
            
            
            // 管理员不存在或UID不为1的时候
            $user = $mysqli->query("select * from qingka_wangke_user where uid='1' limit 1 ");
            if($user){
                $userData = $user->fetch_assoc();
                if(empty($userData)){
                    $mysqli->query("INSERT INTO qingka_wangke_user (uid,uuid,user,pass,name,money,zcz,addprice,yqprice,active) VALUES ('1','1','{$post_web["user"]}','{$post_web["pass"]}','admin','999999','999999','0.2','0.2','1')");
                }else{
                    $mysqli->query("update qingka_wangke_user set uuid='1',user='{$post_web["user"]}',pass='{$post_web["pass"]}' where uid='1' limit 1 ");
                }
                
            }
            
            // 修改order表user字段的类型为text
            $mysqli->query("ALTER TABLE qingka_wangke_order MODIFY COLUMN qg TEXT");
         
        }
        
      }
      
    }
    
    
    // 重启邮件发送进程
    $root = $_SERVER['DOCUMENT_ROOT']?$_SERVER['DOCUMENT_ROOT']:dirname(dirname(__FILE__)).'/';
    $exec_name = $root.'/PHPMailer/fs.php';//需要运行的文件的路径
    $command = 'nohup php '.$exec_name.' >/dev/null  &';
    $exec_name2 = str_replace('/', '\/', $exec_name);// 转义一下
    exec("ps aux | grep php | awk '/{$exec_name2}/ {print $2}'", $outputPids);// 检查已运行的pid有哪些
    if(count($outputPids) > 0){
        foreach ($outputPids as $key => $value) {
            exec("kill -9 $value");
        }
    }
    exec($command, $output, $returnValue);

    // 输出成功和失败表信息的数组对象
    $output = array_merge($successTables, $failedTables);
    echo json_encode($output);
    $file_path = 'index.php'; // 文件路径
    unlink($file_path);
    
    // 关闭连接
    $mysqli->close();

    break;
}
