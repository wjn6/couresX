<?php

include_once('confing/common.php');
include_once('ayconfig.php');
date_default_timezone_set('Asia/Shanghai');

if (empty($conf) || empty($userrow)) {
    http_response_code(403);
    header("Content-Type: text/plain; charset=utf-8");
    exit("403 Forbidden - 干啥呢");
}

// 检测是否为管理员，若不是，输出403
function is_admin()
{
    global $userrow;
    if (empty($userrow["uid"]) || (string) $userrow["uid"] !== '1') {
        http_response_code(403);
        header("Content-Type: text/plain; charset=utf-8");
        exit("403 Forbidden - Permission Denied1");
    }
}

$host = $_SERVER['HTTP_HOST'];

$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");

$php_Self = substr($_SERVER['PHP_SELF'], strripos($_SERVER['PHP_SELF'], "/") + 1);
if ($php_Self != "apiadmin.php") {
    $msg = '%E6%96%87%E4%BB%B6%E9%94%99%E8%AF%AF';
    $msg = urldecode($msg);
    exit(json_encode(['code' => -1, 'msg' => $msg]));
}
switch ($act) {
    // 获取服务器内存、负载等
    case "osIfno":
        $data = [];

        // 负载状态
        $fz = sys_getloadavg();
        $fz = isset($fz[0]) ? round($fz[0] * 10, 3) : 'N/A';
        $data["fz"] = $fz;

        // CPU使用率
        $cpu = 'N/A';
        exec('top -bn1 | grep "Cpu(s)"', $cpuOutput);
        if (!empty($cpuOutput)) {
            if (preg_match('/(\d+\.\d+)\s*id/', $cpuOutput[0], $matches)) {
                $cpu = 100 - (float) $matches[1];
            }
        }

        $data["cpu"] = round($cpu, 3);

        // 内存使用率
        $nc = 'N/A';
        exec('free -m | grep Mem', $memOutput);
        if (!empty($memOutput)) {
            $memInfo = preg_split('/\s+/', $memOutput[0]);
            if (count($memInfo) >= 7) {
                $memTotal = (float) $memInfo[1];
                $memUsed = (float) $memInfo[2];
                $nc = ($memUsed / $memTotal) * 100;
            }
        }
        $data["nc"] = round($nc, 3);

        $userInfoReturn = $DB->get_row("select money from qingka_wangke_user where uid='{$userrow['uid']}' ");
        $userInfo = [
            "money" => (float) $userInfoReturn["money"],
            "orderNum" => 0,
        ];

        $sql_orderNum = '';
        if ($userrow['uid'] != 1) {
            $sql_orderNum = "where uid='{$userrow['uid']}'";
        }
        $orderNum = $DB->count("select count(*) from qingka_wangke_order {$sql_orderNum} ");
        $userInfo["orderNum"] = (float) $orderNum;
        exit(json_encode(["code" => 1, "data" => $data, "userInfo" => $userInfo]));
        break;
    case "messageBox_data":
        $data = [];

        // 获取需要回复的工单数量
        $data["gongdan"] = [];
        $data["gongdan"]["href"] = "gongdan";
        $data["gongdan"]["text"] = "提交工单";
        if ($userrow["uid"] == 1) {
            $need_gongdan_sql = "select count(*) from qingka_wangke_gongdan where state='待回复' ";
        } else {
            $need_gongdan_sql = "select count(*) from qingka_wangke_gongdan where state='待回复' and uid={$userrow['uid']} ";
        }
        $need_gongdan = $DB->count($need_gongdan_sql);
        $data["gongdan"]["need"] = (float) $need_gongdan > 0 ? 1 : 0;
        $data["gongdan"]["num"] = (float) $need_gongdan > 99 ? 99 : (float) $need_gongdan;
        $data["gongdan"]["abnormal_t"] = "有待回复的工单";
        $data["gongdan"]["normal_t"] = "";

        //余额是否不足15
        $data["money"] = [];
        $data["money"]["href"] = "gongdan";
        $data["money"]["text"] = "提交工单";
        $need_money = $DB->get_row("select money from qingka_wangke_user where uid={$userrow['uid']} ")["money"];
        $data["money"]["need"] = (float) $need_money < 15 ? 1 : 0;
        $data["money"]["num"] = (float) $need_money;
        $data["money"]["abnormal_t"] = "余额不足 <15";
        $data["money"]["normal_t"] = "";

        // 对接处理失败的订单
        $data["djOrder"] = [];
        $data["djOrder"]["href"] = "list";
        $data["djOrder"]["text"] = "订单管理";
        $need_djOrder = $DB->count("select count(*) from qingka_wangke_order where dockstatus='2' ");
        $data["djOrder"]["need"] = (float) $need_djOrder > 0 ? 1 : 0;
        $data["djOrder"]["num"] = (float) $need_djOrder > 99 ? 99 : (float) $need_djOrder;
        $data["djOrder"]["abnormal_t"] = "有订单对接处理失败";
        $data["djOrder"]["normal_t"] = "";

        exit(json_encode(["code" => 1, "data" => $data]));
        break;
    case 'mailtest':
        is_admin();
        $DB->query("update qingka_wangke_config set k='[]' where v = 'emails' ");
        $result = emailGo("1", $conf["smtp_user"], "测试邮件发送", "恭喜你，已成功配置邮件！", $conf["smtp_cuser"], "发送测试");
        exit(json_encode(["code" => 1, "status" => "已尝试发送,请查看邮箱!", "ok" => $result]));
        break;
    // 邮件队列获取
    case "emailsListGet":
        is_admin();
        $type = trim(strip_tags(daddslashes($_POST['type'])));

        // 清理
        if ($type === 'clean') {
            $result = $DB->query("update qingka_wangke_emails set status=1,status_t='' ");
            if ($result) {
                exit(json_encode(["code" => 1]));
            } else {
                exit(json_encode(["code" => -1]));
            }
        }

        $emailsResult = $DB->query("SELECT * FROM qingka_wangke_emails where status!=1 order by eid desc ");
        $data = [];
        while ($row = $DB->fetch($emailsResult)) {
            $data[] = $row;
        }

        if ($result) {
            exit(json_encode(["code" => 1, "data" => $data]));
        } else {
            exit(json_encode(["code" => -1, "data" => []]));
        }
        break;
    // 任务状态、处理状态操作
    case 'status_order':
        is_admin();
        $a = trim(strip_tags(daddslashes($_GET['a'])));
        $sex = array_map('intval', $_POST['sex']);
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        if ($a == " " or empty($sex)) {
            jsonReturn(-1, "请先选择订单");
        }

        if ($type == 1) {
            $sql = "`status`='$a'";
        } elseif ($type == 2) {
            $sql = "`dockstatus`='$a'";
        }

        $sexList = implode(',', $sex); // 将订单ID拼接为字符串
        $sql = "UPDATE qingka_wangke_order SET {$sql} WHERE oid IN ({$sexList})";
        $b = $DB->query($sql);
        if ($b) {
            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "未知异常");
        }

        break;
    // 代理管理页管理无痕修改代理数据
    case 'upuser':
        is_admin();
        $data = daddslashes($_POST['data']);

        foreach ($data as $key => $value) {
            if ($key === "money") {
                $now_money = $DB->get_row("select money from qingka_wangke_user where uid={$data['uid']}")["money"];
                if ($now_money > $data["zcz"]) {
                    $data["zcz"] = $now_money;
                } elseif ($now_money != $value) {
                    $data["zcz"] = round($data["zcz"] + round($value - $now_money, 2), 2);
                }
            }
        }

        $updateValues = [];
        foreach ($data as $key => $value) {
            if ($key !== "uid" && $key !== "key") {
                $updateValues[] = "{$key}='{$value}'";
            }
        }
        $updateString = implode(',', $updateValues);
        $b = $DB->query("UPDATE qingka_wangke_user SET {$updateString} where uid='{$data['uid']}'");

        if ($b) {
            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "未知失败");
        }
        break;
    // 个人信息页更新个人数据
    case 'upuser2':
        $data = daddslashes($_POST['data']);

        if ($userrow["uid"] != '1') {
            if (($data["yqprice"] < $userrow["addprice"] || $data["yqprice"] < 0)) {
                jsonReturn(-1, "保存失败，邀请费率不能低于你的费率！");
            }
        }

        $updateValues = [];
        foreach ($data as $key => $value) {
            if ($key !== "uid" && $key !== "key") {
                $updateValues[] = "{$key}='{$value}'";
            }
        }
        $updateString = implode(',', $updateValues);

        $b = $DB->query("UPDATE qingka_wangke_user SET {$updateString} where uid='{$data['uid']}'");
        if ($b) {
            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "未知失败");
        }
        break;
    // 单独修改密码
    case 'passwd':
        $oldpass = trim(strip_tags(daddslashes($_POST['oldpass'])));
        $newpass = trim(strip_tags(daddslashes($_POST['newpass'])));
        if ($oldpass != $userrow['pass']) {
            jsonReturn(-1, "原密码错误");
        }
        if ($newpass == '') {
            jsonReturn(-1, "新密码不能为空");
        }
        $sql = "update `qingka_wangke_user` set `pass` ='{$newpass}' where `uid`='{$userrow['uid']}'";
        if ($DB->query($sql)) {
            jsonReturn(1, "修改成功,请牢记密码");
        } else {
            jsonReturn(-1, "修改失败");
        }
        break;
    // 网站设置
    case 'webset':
        parse_str(daddslashes($_POST['data']), $row);
        $updateSuccess = true;
        foreach ($row as $k => $value) {
            if (in_array($k, ['dklcookie', 'nanatoken', 'akcookie', 'vpercookie'])) {
                $value = authcode($value, 'ENCODE', 'qingka');
            }
            $value = trim($value);
            $updateResult = $DB->query("UPDATE `qingka_wangke_config` SET k='{$value}' WHERE v='{$k}'");
            if (!$updateResult) {
                $updateSuccess = false;
                $failedField = $k;
                break;
            }
        }
        if ($updateSuccess) {
            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "修改失败: {$failedField}");
        }
        break;
    // 代理余额
    case 'usermoney':
        $data = $DB->get_row("select money from qingka_wangke_user where uid ='{$userrow['uid']}'");
        if ($data && isset($data['money']) && $data['money'] >= 0) {
            exit(json_encode(["code" => 1, "money" => $data['money']]));
        } else {
            exit(json_encode(["code" => -1]));
        }
        break;
    //设置邀请码
    case 'szyqm':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $yqm = trim(strip_tags(daddslashes($_POST['yqm'])));
        $type = daddslashes($_POST['type']);

        if ($type === "newyqm") {
            $yqm = random(5, 5);
            if ($DB->get_row("select uid from qingka_wangke_user where yqm='$yqm' ")) {
                $yqm = random(6, 5);
            }
            $DB->query("update qingka_wangke_user set yqm={$yqm} where uid='{$userrow['uid']}' ");
            jsonReturn(1, "生成成功");
        }
        if (strlen($yqm) < 4 || !is_numeric($yqm)) {
            jsonReturn(-1, "邀请码最少4位，且必须为数字");
        }
        if (!is_numeric($yqm)) {
            jsonReturn(-1, "请正确输入邀请码，必须为数字");
        }

        $userExist = $DB->get_row("SELECT uid FROM qingka_wangke_user WHERE yqm = '{$yqm}'");
        if ($userExist) {
            jsonReturn(-1, "该邀请码已被使用，请换一个");
        }

        $a = $DB->get_row("select uuid from qingka_wangke_user where uid='$uid' ");
        if ($userrow['uid'] == '1') {
            $DB->query("update qingka_wangke_user set yqm='{$yqm}' where uid='$uid' ");
            wlog($userrow['uid'], "设置邀请码", "给下级设置邀请码{$yqm}成功", '0');
            jsonReturn(1, "设置成功1");
        } elseif ($userrow['uid'] == $a['uuid']) {
            $DB->query("update qingka_wangke_user set yqm='{$yqm}' where uid='$uid' ");
            wlog($userrow['uid'], "设置邀请码", "给下级设置邀请码{$yqm}成功", '0');
            jsonReturn(1, "设置成功2");
        } else {
            jsonReturn(-1, "无权限");
        }

        break;
    // 获取首页实时公告列表 
    case 'hnlist':
        $cx = daddslashes($_POST['cx']);
        $page = trim(strip_tags(daddslashes($_POST['page']))) ? trim(strip_tags(daddslashes($_POST['page']))) : 1;
        $pagesize = trim(strip_tags($cx['pagesize'])) ? trim(strip_tags($cx['pagesize'])) : 25;
        $pageu = ($page - 1) * $pagesize; //当前界面

        $result = $DB->query("select * from qingka_wangke_homenotice  {$sql}  order by sort desc limit $pageu,$pagesize ");
        $data = [];
        while ($row = $DB->fetch($result)) {
            if (empty($row['name']) || $row['name'] == 'undefined') {
                $row['name'] = 'null';
            }
            if (empty($row["readUIDS"])) {
                $row["readUIDS"] = 0;
            } else {
                $row["readUIDS"] = explode(",", $row["readUIDS"]);
                $row["readUIDS"] = count($row["readUIDS"]) - 1;
            }
            $data[] = $row;
        }

        $count = $DB->count("select count(*) from qingka_wangke_homenotice");
        $last_page = ceil($count / $pagesize); //取最大页数

        if ($result) {
            $response = ["code" => 1, "data" => $data, "last_page" => (int) $last_page, 'count' => (int) $count, 'pagesize' => (int) $pagesize, "current_page" => (int) $page];
        } else {
            $response = ["code" => -1, "msg" => '获取失败'];
        }
        exit(json_encode($response));
        break;
    // 首页实时公告添加
    case 'homenotice_add':
        is_admin();
        $title = trim(strip_tags(daddslashes($_POST['title'])));
        $content = daddslashes($_POST['content']);
        $author = trim(strip_tags(daddslashes($_POST['author'])));
        $status = trim(strip_tags(daddslashes($_POST['status'])));
        $top = trim(strip_tags(daddslashes($_POST['top'])));

        $maxIdResult = $DB->query("SELECT MAX(id) AS max_id FROM qingka_wangke_homenotice");
        $maxIdRow = $DB->fetch($maxIdResult);
        $maxId = $maxIdRow['max_id'];
        $maxId = $maxId + 1;

        $result = $DB->query(" insert into qingka_wangke_homenotice (sort,title,content,author,status,top,addtime) values ('{$maxId}','{$title}','{$content}','{$author}','{$status}','{$top}','{$date}') ");

        if ($result) {
            exit(json_encode(["code" => 1, "msg" => "添加成功"]));
        } else {
            exit(json_encode(["code" => -1, "msg" => "添加失败"]));
        }

        break;
    // 实时公告删除
    case 'homenotice_del':
        is_admin();
        $sex = daddslashes($_POST['sex']);
        if ($userrow['uid'] == 1) {
            foreach ($sex as $oid) {
                $DB->query("DELETE FROM qingka_wangke_homenotice WHERE id='{$oid}'");
            }
            exit('{"code":1,"msg":"已删除！"}');
        } else {
            exit('{"code":-1,"msg":"失败"}');
        }
        break;
    // 首页公告更新
    case 'homenotice_up':
        is_admin();
        $id = trim(strip_tags(daddslashes($_POST['id'])));
        $data = daddslashes($_POST['data']);

        $updateValues = [];

        // 构建更新语句
        foreach ($data as $key => $value) {
            // 只更新 data 中存在的参数
            if (isset($data[$key])) {
                $updateValues[] = "`$key` = '$value'";
            }
        }

        $updateString = implode(', ', $updateValues);

        // 执行更新
        $result = $DB->query("UPDATE qingka_wangke_homenotice SET $updateString, upTime = '{$date}' WHERE id = '{$id}'");

        if ($result) {
            exit(json_encode(["code" => 1, "msg" => "更新成功"]));
        } else {
            exit(json_encode(["code" => -1, "msg" => "更新失败"]));
        }
        break;
    // 首页公告排序
    case 'homenotice_sort':
        is_admin();
        $type = $_POST['type'];
        $id = $_POST['id'];
        // 获取当前id的sort
        $sql = "SELECT sort FROM qingka_wangke_homenotice WHERE id = $id";
        $result = $DB->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentSort = $row["sort"];

            // 检查表中是否存在重复的sort值
            $sql = "SELECT sort FROM qingka_wangke_homenotice GROUP BY sort HAVING COUNT(*) > 1";
            $result = $DB->query($sql);

            // 如果存在重复的sort值，则重新排序
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $sort = $row["sort"];
                    $DB->query("UPDATE qingka_wangke_homenotice SET sort = $i WHERE sort = $sort");
                    $i++;
                }
            }

            // 根据type更新排序
            if ($type == "down") {
                // 找到比当前sort小的第一个数据的sort
                $sql = "SELECT sort FROM qingka_wangke_homenotice WHERE sort < $currentSort ORDER BY sort DESC LIMIT 1";
                $result = $DB->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $targetSort = $row["sort"];
                } else {
                    $targetSort = num_rows; // 如果没有比当前sort小的数据，设为0
                }
            } elseif ($type == "up") {
                // 找到比当前sort大的第一个数据的sort
                $sql = "SELECT sort FROM qingka_wangke_homenotice WHERE sort > $currentSort ORDER BY sort ASC LIMIT 1";
                $result = $DB->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $targetSort = $row["sort"];
                } else {
                    $targetSort = num_rows; // 如果没有比当前sort大的数据，设为一个很大的数
                }
            }

            // 更新当前id的sort和目标id的sort
            $DB->query("UPDATE qingka_wangke_homenotice SET sort = $targetSort WHERE id = $id");
            $DB->query("UPDATE qingka_wangke_homenotice SET sort = $currentSort WHERE sort = $targetSort AND id != $id");

            echo json_encode(["code" => 1, "msg" => "成功"]);
        } else {
            echo json_encode(["code" => -1, "msg" => "未找到相应的数据"]);
        }
        break;
    // 设置邀请费率
    case 'yqprice':
        $yqprice = trim(strip_tags(daddslashes($_POST['yqprice'])));
        if (!is_numeric($yqprice)) {
            jsonReturn(-1, "请正确输入费率，必须为数字");
        }
        if ($yqprice < $userrow['addprice']) {
            jsonReturn(-1, "下级默认费率不能比你低哦");
        }
        if ($yqprice < 0.25) {
            jsonReturn(-1, "邀请费率最低设置为0.25");
        }

        if ($yqprice * 100 % 5 != 0) {
            jsonReturn(-1, "邀请费率必须为0.05的倍数");
        }

        if ($userrow['yqm'] == "") {
            $yqm = random(5, 5);
            if ($DB->get_row("select uid from qingka_wangke_user where yqm='$yqm' ")) {
                $yqm = random(6, 5);
            }
            $sql = "yqm='{$yqm}',yqprice='$yqprice'";
        } else {
            $sql = "yqprice='$yqprice'";
        }
        $DB->query("update qingka_wangke_user set {$sql} where uid='{$userrow['uid']}' ");
        jsonReturn(1, "设置成功");
        break;
    // 微信登录
    // 用户数据
    case 'userinfo':
        if ($islogin != 1) {
            exit('{"code":-10,"msg":"请先登录"}');
        }
        $a = $DB->get_row("select uid,user,notice from qingka_wangke_user where uid='{$userrow['uuid']}' ");
        $dd = $DB->count("select count(oid) from qingka_wangke_order where uid='{$userrow['uid']}' ");
        //$zcz=$DB->count("select sum(money) as money from qingka_wangke_log where type='上级充值' and uid='{$userrow['uid']}' ");

        //安全验证1
        if ($userrow['addprice'] < 0.0001) {
            $DB->query("update qingka_wangke_user set addprice='0.2' where uid='{$userrow['uid']}' ");
            // jsonReturn(-9, "费率异常，已自动设置为0.2");
        }
        //安全验证2
        if ($userrow['uid'] != 1) {
            if ((int) $userrow['money'] - (int) '0.1' > (int) $userrow['zcz']) {
                // $DB->query("update qingka_wangke_user set money='$zcz',active='0' where uid='{$userrow['uid']}' ");
                // jsonReturn(-9, "账号异常，请联系你老大");
            }
        }

        // 当邀请费率小于代理等级费率
        if ($userrow["yqprice"] < $userrow["addprice"]) {
            $DB->query("update qingka_wangke_user set yqprice='{$userrow['addprice']}' where uid='{$userrow['uid']}' ");
        }

        if ($userrow['uid'] == '1') {
            if (strlen($userrow["qq"]) < 5 || $userrow["qq"] == '123456') {
                $DB->query("update qingka_wangke_user set qq='{$conf['smtp_user']}' where uid='{$userrow['uid']}' ");
            }
        } else {
            if (strlen($userrow["qq"]) < 5 || $userrow["qq"] == '123456') {
                $DB->query("update qingka_wangke_user set qq='{$userrow['user']}' where uid='{$userrow['uid']}' ");
            }
        }

        $jtdate = date('Y-m-d h:i:s');
        //代理数据统计
        $dlzs = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' ");
        $dldl = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and endtime>'$jtdate' ");
        $dlzc = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$userrow['uid']}' and addtime>'$jtdate' ");
        $jrjd = $DB->count("select count(uid) from qingka_wangke_order where uid='{$userrow['uid']}' and addtime>'$jtdate' ");


        //       while($dllist2=$DB->fetch($DB->query("select uid from qingka_wangke_user where uuid='{$userrow['uid']}'"))){
        //       	  $dlxd+=$DB->count("select count(oid) from qingka_wangke_order where uid='{$ddlist2['uid']}' and addtime>'$jtdate' ");
        //       }

        //$dlxd="emmmmmm";
        $dailitongji = array(
            'dlzc' => $dlzc,
            'dldl' => $dldl,
            'dlxd' => $dlxd,
            'dlzs' => $dlzs,
            'jrjd' => $jrjd
        );


        $data = array(
            'code' => 1,
            'msg' => '查询成功',
            'uid' => $userrow['uid'],
            'user' => $userrow['user'],
            'name' => $userrow['name'],
            'qq_openid' => $userrow['qq_openid'],
            'nickname' => $userrow['nickname'],
            'faceimg' => $userrow['faceimg'],
            'money' => round($userrow['money'], 3),
            'addprice' => $userrow['addprice'],
            'key' => $userrow['key'],
            'sjuser' => $a['user'],
            'dd' => $dd,
            'zcz' => $userrow['zcz'],
            'yqm' => $userrow['yqm'],
            'yqprice' => $userrow['yqprice'],
            // 'notice' => $conf['notice'],
            'my_notice' => $userrow['notice'],
            'sjnotice' => $a['notice'],
            'dailitongji' => $dailitongji,
            'pass' => $userrow['pass'],
            'qq' => $userrow['qq'] ?: '',
            'wx' => $userrow['wx'] ?: '',
            'homenotice' => $homenotice,
        );
        exit(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        break;
    // 开通api
    case 'ktapi':
        $type = trim(strip_tags(daddslashes($_GET['type'])));
        $uid = trim(strip_tags(daddslashes($_GET['uid'])));
        $key = random(16);
        if ($type == 1) { //自我开通		  
            if ($userrow['money'] < 300) {
                if ($userrow['money'] >= 10) {
                    $DB->query("update qingka_wangke_user set `key`='$key',`money`=`money`-10 where uid='{$userrow['uid']}' ");
                    wlog($userrow['uid'], "开通接口", "开通接口成功!扣费10", '-10');
                    exit('{"code":1,"msg":"花费10开通接口成功","key":"' . $key . '"}');
                } else {
                    exit('{"code":-1,"msg":"余额不足"}');
                }
            } else {
                $DB->query("update qingka_wangke_user set `key`='$key' where uid='{$userrow['uid']}' ");
                wlog($userrow['uid'], "开通接口", "免费开通接口成功!", '0');
                exit('{"code":1,"msg":"免费开通成功","key":"' . $key . '"}');
            }
        } elseif ($type == 2) {
            if ($userrow['money'] < 5) {
                wlog($userrow['uid'], "开通接口", "尝试给下级UID{$uid}开通接口失败! 原因：余额不足", '0');
                jsonReturn(-2, "余额不足以开通");
            } else {
                if ($uid == "") {
                    jsonReturn(-2, "uid不能为空");
                }
                $DB->query("update qingka_wangke_user set `key`='$key' where uid='{$uid}' ");
                $DB->query("update qingka_wangke_user set `money`=`money`-5 where uid='{$userrow['uid']}' ");
                wlog($userrow['uid'], "开通接口", "给下级代理UID{$uid}开通接口成功!扣费5", '-5');
                wlog($uid, "开通接口", "你上级给你开通API接口成功!", '0');
                exit('{"code":1,"msg":"花费5开通成功"}');
            }
        } elseif ($type == 3) {
            if ($userrow['key'] == "0") {
                exit('{"code":-1,"msg":"请先开通key""}');
            } elseif ($userrow['key'] != "") {
                $DB->query("update qingka_wangke_user set `key`='$key' where uid='{$userrow['uid']}' ");
                wlog($userrow['uid'], "开通接口", "更换接口{$key}成功", '0');
                exit('{"code":1,"msg":"更换成功","key":"' . $key . '"}');
            }
        } elseif ($type == 4) {
            if ($userrow['key'] == "0") {
                exit('{"code":-1,"msg":"请先开通key""}');
            } elseif ($userrow['key'] != "") {
                $DB->query("update qingka_wangke_user set `key`='0' where uid='{$userrow['uid']}' ");
                wlog($userrow['uid'], "关闭接口", "关闭接口{$key}成功", '0');
                exit('{"code":1,"msg":"关闭成功","key":"' . $key . '"}');
            }
        }
        jsonReturn(-2, "未知异常");
        break;

    case 'get':
        $cid = trim(strip_tags(daddslashes($_POST['cid'])));
        $userinfo = daddslashes($_POST['userinfo']);
        $hash = daddslashes($_POST['hash']);
        $rs = $DB->get_row("select * from qingka_wangke_class where cid='$cid' limit 1 ");
        $kms = str_replace(array("\r\n", "\r", "\n"), "[br]", $userinfo);
        $info = explode("[br]", $kms);


        // 余额预警邮件
        // preg_match("/余额小于|余额不足|请充值|低于|小于/", $result["msg"]) || preg_match("/余额小于|余额不足|请充值|低于|小于/", $result["message"]) || preg_match("/余额小于|余额不足|请充值|低于|小于/", $result)
        if (true) {
            if ($conf["smtp_open_huo"] == 1 && $conf["smtp_open"] == 1) {
                $now_huoyuan = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$rs['queryplat']}' limit 1 ");
                $token = $now_huoyuan["token"];
                $cookie = $now_huoyuan['cookie'];

                $data = [
                    "uid" => $now_huoyuan["user"],
                    "key" => $now_huoyuan["pass"],
                    "token" => $now_huoyuan["token"],
                ];
                $header = [
                    'Content-type:application/x-www-form-urlencoded',
                    "token: " . $token,
                    "cookie:" . $cookie,
                    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 toc"
                ];
                $ace_rl = $now_huoyuan["url"];
                $ace_url = $ace_rl . 'api.php?act=getmoney';

                $result2 = get_url($ace_url, $data, $cookie, $header, 5);

                $result2 = json_decode($result2, true);
                if ($result2["code"] == 1 || preg_match("/成功/", $result2["msg"])) {
                    if ($now_huoyuan["smtp_money"] >= $result2["money"]) {
                        $email_c = '
                            【' . $now_huoyuan['hid'] . '】' . $now_huoyuan['name'] . ' → 余额不足<br />
                            当前货源余额：' . $result2["money"] . '<hr />' . $date . '
                        ';
                        $admin_info = $DB->get_row("select qq from qingka_wangke_user where uid=1 limit 1");
                        emailGo($userrow['uid'], $conf["smtp_user"], "🧨 货源余额不足", $email_c, $admin_info['qq'] . '@qq.com', "余额提醒邮件");
                    }
                }
            }
        }

        $info_count = count($info);
        $DB->query("update qingka_wangke_user set ck1=ck1+$info_count where uid='{$userrow['uid']}' ");

        $key = 'AES_Encryptwords';
        $iv = '0123456789abcdef';
        // $hash = openssl_decrypt($hash, 'aes-128-cbc', $key, 0, $iv);
        // if ((empty($_SESSION['addsalt']) || $hash != $_SESSION['addsalt'])) {
        //     exit('{"code":-1,"msg":"验证失败，请刷新页面重试"}');
        // }



        for ($i = 0; $i < count($info); $i++) {
            $str = merge_spaces(trim($info[$i]));
            $userinfo2 = explode(" ", $str); //分割

            $result0 = getWk($rs, $userinfo2);

            // if (count($userinfo2) > 2) {
            //     $result = getWk($rs['queryplat'], $rs['getnoun'], trim($userinfo2[0]), trim($userinfo2[1]), trim($userinfo2[2]), $rs['name']);
            // } else {
            //     $result = getWk($rs['queryplat'], $rs['getnoun'], "自动识别", trim($userinfo2[0]), trim($userinfo2[1]), $rs['name']);
            // }
            $userinfo3 = trim($userinfo2[0] . " " . $userinfo2[1] . " " . $userinfo2[2]);
            $result0['userinfo'] = $userinfo3;
            wlog($userrow['uid'], "查课", "{$rs['name']}-查课信息：{$userinfo3}", 0);
        }

        exit(json_encode($result0));
        break;
    // 支付
    case 'pay':
        $zdpay = $conf['zdpay'];
        $money = trim(strip_tags(daddslashes($_POST['money'])));
        $name = "零食购买-" . $money . "";
        if (!preg_match('/^[0-9.]+$/', $money))
            exit('{"code":-1,"msg":"订单金额不合法"}');
        if ($money < $zdpay) {
            jsonReturn(-1, "在线充值最低{$zdpay}");
        }

        $row = $DB->get_row("select * from qingka_wangke_user where uid='{$userrow['uuid']}' ");
        $czAuth = $DB->get_row("select * from qingka_wangke_dengji where rate={$userrow['addprice']}")["czAuth"];
        if ($row['uid'] == '1' || $userrow['uid'] == "1" || $userrow['czAuth'] == 1 || $czAuth) {
            $out_trade_no = date("YmdHis") . rand(111, 999); //生成本地订单号
            $wz = $_SERVER['HTTP_HOST'];
            $sql = "insert into `qingka_wangke_pay` (`out_trade_no`,`uid`,`num`,`name`,`money`,`ip`,`addtime`,`domain`,`status`) values ('" . $out_trade_no . "','" . $userrow['uid'] . "','" . $money . "','" . $name . "','" . $money . "','" . $clientip . "','" . $date . "','" . $wz . "','0')";
            if ($DB->query($sql)) {
                exit('{"code":1,"msg":"生成订单成功！","out_trade_no":"' . $out_trade_no . '","need":"' . $money . '"}');
            } else {
                exit('{"code":-1,"msg":"生成订单失败！' . $DB->error() . '"}');
            }
        } else {
            jsonReturn(-1, "请您根据上面的信息联系上家充值。");
        }

        break;
    case 'getclass_pl':
        $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort desc");
        while ($row = $DB->fetch($a)) {
            if ($row['yunsuan'] == "*") {
                $price = round($row['price'] * $userrow['addprice'], 20);
                $price1 = $price;
            } elseif ($row['yunsuan'] == "+") {
                $price = round($row['price'] + $userrow['addprice'], 20);
                $price1 = $price;
            } else {
                $price = round($row['price'] * $userrow['addprice'], 20);
                $price1 = $price;
            }
            //密价
            $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='{$row['cid']}' ");
            if ($mijia) {
                if ($mijia['mode'] == 0) {
                    $price = round($price - $mijia['price'], 20);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 1) {
                    $price = round(($row['price'] - $mijia['price']) * $userrow['addprice'], 20);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 2) {
                    $price = $mijia['price'];
                    if ($price <= 0) {
                        $price = 0;
                    }
                }
                $row['name'] = "【密价】{$row['name']}";
            }
            if ($price >= $price1) { //密价价格大于原价，恢复原价
                $price = $price1;
            }

            $price = roun($price, 3);
            $data[] = array(
                'sort' => $row['sort'],
                'cid' => $row['cid'],
                'name' => $row['name'],
                'noun' => $row['noun'],
                'price' => $price,
                'content' => $row['content'],
                'status' => $row['status'],
                'miaoshua' => $miaoshua
            );
        }
        // foreach ($data as $key => $row) {
        //     $sort[$key]  = $row['sort'];
        //     $cid[$key] = $row['cid'];
        //     $name[$key] = $row['name'];
        //     $noun[$key] = $row['noun'];
        //     $price[$key] = $row['price'];
        //     $info[$key] = $row['info'];
        //     $content[$key] = $row['content'];
        //     $status[$key] = $row['status'];
        //     $miaoshua[$key] = $row['miaoshua'];
        // }
        array_multisort($sort, SORT_ASC, $cid, SORT_DESC, $data);
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
        break;
    // 提交订单
    case 'add':

        $cid = trim(strip_tags(daddslashes($_POST['cid'])));
        $qg = daddslashes($_POST['qg']);
        $data = daddslashes($_POST['data']);

        $data_count = count($data);
        $DB->query("update qingka_wangke_user set xd1=xd1+$data_count where uid='{$userrow['uid']}' ");

        $clientip = real_ip();
        $rs = $DB->get_row("select * from qingka_wangke_class where cid='$cid' limit 1 ");
        if ($cid == '' || $data == '') {
            exit('{"code":-1,"msg":"请选择课程"}');
        }
        if ($rs['yunsuan'] == "*") {
            $danjia = round($rs['price'] * $userrow['addprice'], 20);
        } elseif ($rs['yunsuan'] == "+") {
            $danjia = round($rs['price'] + $userrow['addprice'], 20);
        } else {
            $danjia = round($rs['price'] * $userrow['addprice'], 20);
        }
        //密价
        $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='$cid' ");
        if ($mijia) {
            if ($mijia['mode'] == 0) {
                $danjia = round($danjia - $mijia['price'], 20);
                if ($danjia <= 0) {
                    $danjia = 0;
                }
            } elseif ($mijia['mode'] == 1) {
                $danjia = round(($rs['price'] - $mijia['price']) * $userrow['addprice'], 20);
                if ($danjia <= 0) {
                    $danjia = 0;
                }
            } elseif ($mijia['mode'] == 2) {
                $danjia = $mijia['price'];
                if ($danjia <= 0) {
                    $danjia = 0;
                }
            }
        }

        if ($danjia == 0 || $userrow['addprice'] < 0.01) {
            exit('{"code":-1,"msg":"大佬，我得罪不起您，我小本生意，有哪里得罪之处，还望多多包涵"}');
        }

        $danjia = $danjia < 0.001 ? 0.001 : sprintf("%.3f", $danjia);


        $money = count($data) * $danjia;
        if ($userrow['money'] < $money) {
            exit('{"code":-1,"msg":"余额不足"}');
        }

        $sentEmails = array();
        foreach ($data as $row) {


            $userinfo = $row['userinfo'];
            $userName = $row['userName'];
            $userinfo = explode(" ", $userinfo); //分割账号密码
            if (count($userinfo) > 2) {
                $school = $userinfo[0];
                $user = $userinfo[1];
                $pass = $userinfo[2];
            } else {
                $school = "自动识别";
                $user = $userinfo[0];
                $pass = $userinfo[1];
            }

            $kcid = $row['data']['id'];
            $kcname = $row['data']['name'];
            $kcjs = $row['data']['kcjs'];
            // if($DB->get_row("select * from qingka_wangke_order where ptname='{$rs['name']}' and school='$school' and user='$user' and pass='$pass' and kcid='$kcid' and kcname='$kcname' ")){
            //             $dockstatus='3';//重复下单
            //	   }else
            if ($rs['docking'] == 0 || $rs['docking'] == 10) {
                $dockstatus = '99';
            } else {
                $dockstatus = '0';
            }

            $is = $DB->insert("insert into qingka_wangke_order (uid,cid,hid,ptname,school,name,user,pass,kcid,kcname,courseEndTime,fees,noun,miaoshua,addtime,ip,dockstatus,qg) values ('{$userrow['uid']}','{$rs['cid']}','{$rs['docking']}','{$rs['name']}','{$school}','$userName','$user','$pass','$kcid','$kcname','{$kcjs}','{$danjia}','{$rs['noun']}','$miaoshua','{$date}','$clientip','$dockstatus','$qg') "); //将对应课程写入数据库
            if ($is) {

                $DB->query("update qingka_wangke_user set money=money-'{$danjia}' where uid='{$userrow['uid']}' limit 1 ");

                orderLogs($is, $userrow['uid'], "站内下单", "下单成功，扣费：".$danjia, "-$danjia");
                wlog($userrow['uid'], "添加任务", "  {$rs['name']} {$user} {$pass} {$kcname} 扣除{$danjia}！", -$danjia);

                if (!empty($conf["smtp_open_xd"])) {
                    $email_c = '
                    <h1>🍕成功下单！UID：' . $userrow['uid'] . '</h1>
                    <hr />
                    <p><b>平台：' . $rs['name'] . '</b></p>
                    <p><b>账号：' . $user . '</b></p>
                    <p><b>密码：' . $pass . '</b></p>
                    <p><b>学校：' . $school . '</b></p>
                    <p><b>课程：' . $kcname . '</b></p>
                    ' . DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");
                    if (!in_array($email_c, $sentEmails)) {
                        $aa = trim($emails["j"]) == '123456@qq.com' || trim($emails["j"]) == '1@qq.com';
                        $qq = $userrow['qq'];
                        if ($aa || empty($qq)) {
                            $qq = $userrow['user'];
                        }
                        // 调用 emailGo 函数发送邮件
                        emailGo($userrow['uid'], $conf["smtp_user"], "☪ 下单成功啦 →", $email_c, $qq . '@qq.com', "下单邮件");
                    }
                }
            }
        }

        if ($is) {
            $data = $DB->get_row("select money from qingka_wangke_user where uid ='{$userrow['uid']}'");
            exit(json_encode(["code" => 1, "msg" => "提交成功", "money" => $data["money"], "money2" => $money]));
        } else {

            exit('{"code":-1,"msg":"提交失败"}');
        }
        break;
    // 帮助文档列表
    case 'helplist':
        $cx = isset($_POST['cx']) ? daddslashes($_POST['cx']) : '';
        $page = isset($_POST['page']) ? intval(trim(strip_tags(daddslashes($_POST['page'])))) : 1;
        $pagesize = isset($cx['pagesize']) ? intval(trim(strip_tags($cx['pagesize']))) : 25;
        $pageu = ($page - 1) * $pagesize;
        $type = daddslashes($_POST["type"]);

        if ($type == '1') {
            $sql = 'where status!=0';
        }

        $DB->query("update qingka_wangke_help SET readUIDS = CONCAT(readUIDS, '{$userrow['uid']},') where readUIDS NOT LIKE '%{$userrow['uid']},%' ");
        $a = $DB->query("select * from qingka_wangke_help  $sql  order by sort desc limit $pageu,$pagesize ");
        $data = [];
        while ($row = $DB->fetch($a)) {
            if ($row['name'] == '' || $row['name'] == 'undefined') {
                $row['name'] = 'null';
            }
            if (empty($row["readUIDS"])) {
                $row["readUIDS"] = 0;
            } else {
                $row["readUIDS"] = explode(",", $row["readUIDS"]);
                $row["readUIDS"] = count($row["readUIDS"]) - 1;
            }
            $data[] = $row;
        }

        $count = $DB->count("select count(*) from qingka_wangke_help $sql");
        $last_page = ceil($count / $pagesize); //取最大页数

        if ($a) {
            $data = ["code" => 1, "data" => $data, "last_page" => (int) $last_page, 'count' => (int) $count, 'pagesize' => (int) $pagesize, "current_page" => (int) $page];
        } else {
            $data = ["code" => -1, "msg" => '获取失败'];
        }
        exit(json_encode($data));
        break;
    // 帮助文档添加
    case 'help_add':
        is_admin();
        $status = trim(strip_tags(daddslashes($_POST['status'])));
        $title = trim(strip_tags(daddslashes($_POST['title'])));
        $content = trim(strip_tags(daddslashes($_POST['content'])));
        $clientip = real_ip();

        $maxIdResult = $DB->query("SELECT MAX(id) AS max_id FROM qingka_wangke_help");
        $maxIdRow = $DB->fetch($maxIdResult);
        $maxId = $maxIdRow['max_id'];
        $maxId = $maxId + 1;
        $is = $DB->query("insert into qingka_wangke_help (status,sort,title,content,addTime,upTime,ip) values ('{$status}','{$maxId}','{$title}','{$content}','{$date}','{$date}','{$clientip}')");

        if ($is) {
            exit(json_encode(["code" => 1]));
        } else {
            exit(json_encode(["code" => -1]));
        }
        break;
    // 帮助文档更新
    case 'help_up':
        is_admin();
        $id = trim(strip_tags(daddslashes($_POST['id'])));
        $data = daddslashes($_POST['data']);
        $updateQuery = "update qingka_wangke_help set ";
        $updateFields = [];

        // 遍历 $data 数组，构建更新字段和值的语句
        foreach ($data as $key => $value) {
            // 添加到更新字段数组中
            $updateFields[] = "{$key} = '{$value}'";
        }

        // 构建完整的更新语句
        $updateQuery .= implode(", ", $updateFields);
        $updateQuery .= " where id = '{$id}'";

        // 执行更新语句
        $DB->query($updateQuery);
        exit(json_encode(["code" => 1]));

        break;
    // 帮助文档排序
    case 'help_sort':
        // 接收POST请求中的type和id
        $type = $_POST['type'];
        $id = $_POST['id'];

        // 获取当前id的sort
        $sql = "SELECT sort FROM qingka_wangke_help WHERE id = $id";
        $result = $DB->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentSort = $row["sort"];

            // 检查表中是否存在重复的sort值
            $sql = "SELECT sort FROM qingka_wangke_help GROUP BY sort HAVING COUNT(*) > 1";
            $result = $DB->query($sql);

            // 如果存在重复的sort值，则重新排序
            if ($result->num_rows > 0) {
                $i = 1;
                while ($row = $result->fetch_assoc()) {
                    $sort = $row["sort"];
                    $DB->query("UPDATE qingka_wangke_help SET sort = $i WHERE sort = $sort");
                    $i++;
                }
            }

            // 根据type更新排序
            if ($type == "down") {
                // 找到比当前sort小的第一个数据的sort
                $sql = "SELECT sort FROM qingka_wangke_help WHERE sort < $currentSort ORDER BY sort DESC LIMIT 1";
                $result = $DB->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $targetSort = $row["sort"];
                } else {
                    $targetSort = num_rows; // 如果没有比当前sort小的数据，设为0
                }
            } elseif ($type == "up") {
                // 找到比当前sort大的第一个数据的sort
                $sql = "SELECT sort FROM qingka_wangke_help WHERE sort > $currentSort ORDER BY sort ASC LIMIT 1";
                $result = $DB->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $targetSort = $row["sort"];
                } else {
                    $targetSort = num_rows; // 如果没有比当前sort大的数据，设为一个很大的数
                }
            }

            // 更新当前id的sort和目标id的sort
            $DB->query("UPDATE qingka_wangke_help SET sort = $targetSort WHERE id = $id");
            $DB->query("UPDATE qingka_wangke_help SET sort = $currentSort WHERE sort = $targetSort AND id != $id");

            echo "排序更新成功";
        } else {
            echo "未找到相应的数据";
        }
        break;
    // 帮助文档删除
    case 'help_del':
        is_admin();
        $sex = daddslashes($_POST['sex']);
        if ($userrow['uid'] == 1) {
            for ($i = 0; $i < count($sex); $i++) {
                $oid = $sex[$i];
                $DB->query("delete from qingka_wangke_help where id='{$oid}'");
            }
            exit('{"code":1,"msg":"已删除！"}');
        } else {
            exit('{"code":-1,"msg":"失败"}');
        }
        break;
    case 'qglist':
        // 获取数据列表
        $cx = daddslashes($_POST['cx']);
        $page = trim(strip_tags(daddslashes($_POST['page']))) ? trim(strip_tags(daddslashes($_POST['page']))) : 1;
        $pagesize = trim(strip_tags($cx['pagesize'])) ? trim(strip_tags($cx['pagesize'])) : 25;
        $pageu = ($page - 1) * $pagesize; //当前界面

        $sql = "where qg != '' ";
        if ($userrow['uid'] != '1') {
            $sql1 = " and uid='{$userrow['uid']}'";
            $sql = $sql . $sql1;
        }
        $a = $DB->query("select * from qingka_wangke_order  $sql  order by oid desc limit $pageu,$pagesize ");
        while ($row = $DB->fetch($a)) {
            if ($row['name'] == '' || $row['name'] == 'undefined') {
                $row['name'] = 'null';
            }
            $data[] = $row;
        }

        $count = $DB->count("select count(*) from qingka_wangke_order where qg != '' ");
        $last_page = ceil($count / $pagesize); //取最大页数

        if ($a) {
            $data = ["a" => $sql, "code" => 1, "data" => $data, "last_page" => (int) $last_page, 'count' => (int) $count, 'pagesize' => (int) $pagesize, "current_page" => (int) $page];
        } else {
            $data = ["code" => -1, "msg" => '获取失败'];
        }
        exit(json_encode($data));
        break;
    // 订单补刷
    case 'bs':
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $b = $DB->get_row("select hid,cid,dockstatus,status,bsnum from qingka_wangke_order where oid='{$oid}' ");
        $DB->query("update qingka_wangke_user set bs1=bs1+1 where uid='{$userrow['uid']}' ");
        if ($b["bsnum"] >= $conf["api_bs"] && !empty($conf["api_bs"])) {
            orderLogs($oid, $userrow['uid'], "订单补刷", "【手动单个】补刷失败，当前已补刷". $conf["api_bs"] . "次，达到上限", "0");
            jsonReturn(-1, "已补刷" . $conf["api_bs"] . "次，不能再补刷了！");
        }
        if ($b['dockstatus'] == '99') {
            orderLogs($oid, $userrow['uid'], "订单补刷", "【手动单个】成功加入线程，排队补刷中", "0");
            $DB->query("update qingka_wangke_order set status='待处理',`bsnum`=bsnum+1 where oid='{$oid}' ");
            jsonReturn(1, "成功加入线程，排队补刷中");
        }

        if (preg_match("/{$conf['bs0_rw']}/", trim($b['status']))) {
            jsonReturn(-1, "该任务状态订单无法补刷！");
        } elseif (!preg_match("/{$conf['bs_cl']}/", trim($b['dockstatus']))) {
            jsonReturn(-1, "该处理状态订单无法补刷！");
        } else {
            $b = budanWk($oid);
            $msg = empty($b["msg"])?"未知错误":$b["msg"];
            if ($b['code'] == 1) {
                orderLogs($oid, $userrow['uid'], "订单补刷", "【手动单个】补刷成功", "0");
                $DB->query("update qingka_wangke_order set status='补刷中',`bsnum`=bsnum+1 where oid='{$oid}' ");
                jsonReturn(1, $b['msg']);
            } else {
                orderLogs($oid, $userrow['uid'], "订单补刷", "【手动单个】补刷失败：".$b["msg"], "0");
                jsonReturn(-1, $b['msg']);
            }
        }
        break;
    case 'upOrder':
        // parse_str(daddslashes($_POST['data']),$row);//将字符串解析成多个变量
        $row = $_POST['data'];
        if ($userrow['uid'] == 1) {
            // 初始化一个空数组来存储更新的属性和值
            $update_pairs = array();

            // 遍历关联数组，构建更新语句中的 SET 子句
            foreach ($row as $key => $value) {
                // 对属性名和属性值进行安全处理，比如转义特殊字符，防止 SQL 注入攻击
                $key = $DB->escape($key);
                $value = $DB->escape($value);

                // 排除 OID 属性，因为 OID 用于 WHERE 子句
                if ($key !== 'oid') {
                    // 将属性名和属性值添加到数组中
                    $update_pairs[] = "$key = '$value'";
                }
            }

            // 将数组中的属性和值连接成一个字符串
            $update_string = implode(', ', $update_pairs);
            $DB->query("update qingka_wangke_order set $update_string where oid='{$row['oid']}' ");
            exit('{"code":1,"msg":"操作成功2"}');
        } else {
            exit('{"code":-2,"msg":"无权限"}');
        }
        break;
    case 'uporder': //进度刷新
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $row = $DB->get_row("select * from qingka_wangke_order where oid='$oid'");
        if ($row['hid'] == 'ximeng') {
            exit('{"code":-2,"msg":"当前订单接口异常，请去查询补单","url":""}');
        } elseif ($row['dockstatus'] == '99') {
            //$result=pre_zy($oid);
            //exit(json_encode($result));
            orderLogs($oid, $userrow['uid'], "同步进度", "【手动单个】实时进度无需更新", "0");
            jsonReturn(1, '实时进度无需更新');
        }
        $DB->query("update qingka_wangke_user set jd1=jd1+1 where uid='{$userrow['uid']}' ");
        $result = processCx($oid);

        if ($result["code"] === 404) {
            orderLogs($oid, $userrow['uid'], "同步进度", "【手动单个】同步失败，上游通讯异常", "0");
            exit(json_encode(["code" => -1, "msg" => "上游通讯异常"]));
        }
        $result2 = array_filter($result, function ($item) use ($row) {
            return ($item["yid"] == $row["yid"] || $item["id"] == $row["yid"] || $item["oid"] == $row["yid"]) && !empty($row["yid"]);
        });
        $result2 = array_values($result2);

        // 如果yid查的出来
        if (count($result2) > 0) {
            // $date
            $result3 = $result2[0];

            $result3['yid'] = !empty($result3['yid']) ? $result3['yid'] : $result3['oid'];
            $result3['yid'] = !empty($result3['yid']) ? $result3['yid'] : $result3['id'];
            $result3['remarks'] = addslashes($result3['remarks']);
            $ok = $DB->query("update qingka_wangke_order set `name`='{$result3['name']}',`kcname`='{$result3['kcname']}',`yid`='{$result3['yid']}',`status`='{$result3['status_text']}',`dockstatus`='1',`courseStartTime`='{$result3['kcks']}',`courseEndTime`='{$result3['kcjs']}',`examStartTime`='{$result3['ksks']}',`examEndTime`='{$result3['ksjs']}',`process`='{$result3['process']}',`remarks`='{$result3['remarks']}' ,`uptime`='{$date}' where `user`='{$result3['user']}' and `oid`='{$oid}' and `yid`='{$result3['yid']}' ");
            if ($ok) {
                orderLogs($oid, $userrow['uid'], "同步进度", "【手动单个】最新进度：".$result3['remarks'], "0");
                exit(json_encode(["code" => 1, "msg" => "同步成功", "data" => $result]));
            } else {
                orderLogs($oid, $userrow['uid'], "同步进度", "【手动单个】同步失败", "0");
                exit(json_encode(["code" => -1, "msg" => "同步失败", "data" => $result]));
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

                $result3['yid'] = !empty($result3['yid']) ? $result3['yid'] : $result3['oid'];
                $result3['yid'] = !empty($result3['yid']) ? $result3['yid'] : $result3['id'];
                $result3['remarks'] = addslashes($result3['remarks']);
                $ok = $DB->query("update qingka_wangke_order set `name`='{$result3['name']}',`status`='{$result3['status_text']}',`yid`='{$result3['yid']}',`dockstatus`='1',`courseStartTime`='{$result3['kcks']}',`courseEndTime`='{$result3['kcjs']}',`examStartTime`='{$result3['ksks']}',`examEndTime`='{$result3['ksjs']}',`process`='{$result3['process']}',`remarks`='{$result3['remarks']}' ,`uptime`='{$date}' where `user`='{$result3['user']}' and `kcname`='{$result3['kcname']}' ");
                if ($ok) {
                    orderLogs($oid, $userrow['uid'], "同步进度", "【手动单个】最新进度：".$result3['remarks'], "0");
                    exit(json_encode(["code" => 1, "msg" => "同步成功", "data" => $result]));
                } else {
                    orderLogs($oid, $userrow['uid'], "同步进度", "【手动单个】同步失败", "0");
                    exit(json_encode(["code" => -1, "msg" => "同步失败", "data" => $result]));
                }
            } else {
                $msg = empty($result["msg"]) ? "无匹配项" : $result["msg"];
                orderLogs($oid, $userrow['uid'], "同步进度", "【手动单个】同步失败：".$msg, "0");
                exit(json_encode(["code" => -1, "msg" => "同步失败".$msg, "data" => []]));
            }
        }

        break;
    case 'ms_order': //列表提交秒刷
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $msg = $row['ptname'] . "不支持提交秒刷";
        exit('{"code":-1,"msg":"' . $msg . '"}');
        break;
    case 'qx_order': //取消订单
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $row = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
        if ($row['uid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            jsonReturn(-1, "无权限");
        } else {
            $DB->query("update qingka_wangke_order set `status`='已取消',`dockstatus`=4 where oid='$oid' ");
            jsonReturn(1, "取消成功");
        }
        break;
    // 订单列表
    case 'orderlist':
        $cx = daddslashes($_POST['cx']);
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pagesize = trim(strip_tags($cx['pagesize']));
        $pageu = ($page - 1) * $pagesize; //当前界面		
        $qq = trim(strip_tags($cx['qq']));
        $status_text = trim(strip_tags($cx['status_text']));
        $remarks = trim(strip_tags($cx['remarks']));
        $dock = trim(strip_tags($cx['dock']));
        $cid = trim(strip_tags($cx['cid']));
        $oid = trim(strip_tags($cx['oid']));
        $uid = trim(strip_tags($cx['uid']));
        $kcname = trim(strip_tags($cx['kcname']));
        $ptname = trim(strip_tags($cx['ptname']));
        $school = trim(strip_tags($cx['school']));
        if ($userrow['uid'] != '1') {
            $sql1 = "where uid='{$userrow['uid']}'";
        } else {
            $sql1 = "where 1=1";
        }
        if ($kcname != '') {
            //$sql2=" and kcname='{$kcname}'";
            $sql2 = " and kcname like '%" . $kcname . "%' ";
        }
        if ($cid != '') {
            $sql3 = " and cid='{$cid}'";
        }
        if ($qq != '') {
            $sql4 = " and user='{$qq}'";
        }
        if ($oid != '') {
            $sql5 = " and oid='{$oid}'";
        }
        if ($uid != '') {
            $sql6 = " and uid='{$uid}'";
        }
        if ($status_text != '') {
            $sql7 = " and status like '%" . $status_text . "%' ";
        }
        if ($remarks != '') {
            $sql7 = " and remarks like '%" . $remarks . "%' ";
        }
        if ($dock != '') {
            $sql8 = " and dockstatus='{$dock}'";
        }
        if ($school != '') {
            $sql9 = " and school like '%" . $school . "%' ";
        }
        if ($ptname != '') {
            $ptnameA = $DB->query("select cid from qingka_wangke_class where name like '%" . $ptname . "%'");
            while ($row = $DB->fetch($ptnameA)) {
                $ptnameB = $row['cid'] . ',' . $ptnameB;
            }
            $ptnameB = rtrim($ptnameB, ',');
            $sql3 = " and cid in ({$ptnameB})";
        }
        $sql = $sql1 . $sql2 . $sql3 . $sql4 . $sql5 . $sql6 . $sql7 . $sql8 . $sql9;
        $a = $DB->query("select * from qingka_wangke_order {$sql} order by oid desc limit $pageu,$pagesize ");
        $count1 = $DB->count("select count(*) from qingka_wangke_order {$sql} ");
        $data = [];
        while ($row = $DB->fetch($a)) {
            if ($row['name'] == '' || $row['name'] == 'undefined') {
                $row['name'] = 'null';
            }
            $data[] = $row;
        }
        $last_page = ceil($count1 / $pagesize); //取最大页数
        $data = array('a' => $sql3, 'code' => 1, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page, "uid" => (int) $userrow['uid'], 'count' => $count1, "pagesize" => $pagesize);
        exit(json_encode($data));
        break;
    // 获取订单日志
    case "orderLogs_get":
        $oid = trim(strip_tags($_POST["oid"]));
        if(empty($oid)){
            jsonReturn(-1,"非法请求");
        }
        $order = $DB->get_row("select uid from qingka_wangke_order where oid='{$oid}' ");
        if(empty($order)){
            jsonReturn(-1,"订单不存在");
        }
        
        $orderLogsReturn = $DB->query("select * from qingka_wangke_orderLogs where oid='{$oid}' order by olid desc ");
        
        $data = [];
        while($row = $DB->fetch($orderLogsReturn)){
            $user = $DB->get_row("select name from qingka_wangke_user where uid='{$row['uid']}' ");
            $row["user"] = $user["name"];
            
            $data[] = $row;
        }
        exit(json_encode(["code"=>1,"data"=>$data,"msg"=>"成功"]));
        break;
    // 对接处理
    case 'duijie':
        is_admin();
        $oid = trim(strip_tags(daddslashes($_GET['oid'])));
        $b = $DB->get_row("select * from qingka_wangke_order where oid='$oid' limit 1 ");
        $d = $DB->get_row("select * from qingka_wangke_class where cid='{$b['cid']}' ");
        orderLogs($oid, $userrow['uid'], "订单提交", "【手动单个】开始提交到渠道", "0");
        $result = addWk($oid);
        
        $msg = empty($result["msg"])?"未知错误":$result["msg"];

        if ($result['code'] == '1') {
            orderLogs($oid, $userrow['uid'], "订单提交", "【手动单个】提交成功", "0");
            $DB->query("update qingka_wangke_order set `hid`='{$b['hid']}',`status`='进行中',`dockstatus`=1,`yid`='{$result['id']}',`remarks`='订单已录入服务器，等待进程自动开始' where oid='{$oid}' "); //对接成功  
        } elseif ($result['code'] == '-69') {
            orderLogs($oid, $userrow['uid'], "订单提交", "【手动单个】未提交：重复下单", "0");
            $DB->query("update qingka_wangke_order set `status`='重复下单',`dockstatus`=3 where oid='{$oid}' ");
        } else {
            orderLogs($oid, $userrow['uid'], "订单提交", "【手动单个】提交失败：".$msg, "0");
            $DB->query("update qingka_wangke_order set `dockstatus`=2 where oid='{$oid}' ");
        }
        exit(json_encode($result, true));
        break;
    // 获取商品
    case 'getclass':
        $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort asc");
        $cids = [];
        $data = [];
        while ($row = $DB->fetch($a)) {
            if ($row['docking'] == 'nana') {
                $miaoshua = 1;
            } else {
                $miaoshua = 0;
            }

            if ($row['yunsuan'] == "*") {
                $price = round($row['price'] * $userrow['addprice'], 20);
                $price1 = $price;
            } elseif ($row['yunsuan'] == "+") {
                $price = round($row['price'] + $userrow['addprice'], 20);
                $price1 = $price;
            } else {
                $price = round($row['price'] * $userrow['addprice'], 20);
                $price1 = $price;
            }
            //密价
            $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='{$row['cid']}' ");
            if ($mijia) {
                if ($mijia['mode'] == 0) {
                    $price = round($price - $mijia['price'], 20);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 1) {
                    $price = round(($row['price'] - $mijia['price']) * $userrow['addprice'], 20);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 2) {
                    $price = $mijia['price'];
                    if ($price <= 0) {
                        $price = 0;
                    }
                }
                $row['name'] = "【密价】{$row['name']}";
            }
            if ($price >= $price1) { //密价价格大于原价，恢复原价
                $price = $price1;
            }

            $price = $price < 0.001 ? 0.001 : sprintf("%.3f", $price);

            $cids[] = $row['cid'];
            $data[$row['cid']] = array(
                'sort' => $row['sort'],
                'cid' => $row['cid'],
                'fenlei' => $row['fenlei'],
                'name' => $row['name'],
                'noun' => $row['noun'],
                'getnoun' => $row['getnoun'],
                'nocheck' => $row['nocheck'],
                'changePass' => $row['changePass'],
                'price' => $price,
                'content' => $row['content'],
                'status' => $row['status'],
                'order' => 0,
                'miaoshua' => $miaoshua
            );
        }
        if (!empty($cids)) {
            $cidStr = implode("','", $cids);
            $order = $DB->query("SELECT cid, COUNT(*) as order_count FROM qingka_wangke_order WHERE cid IN ('{$cidStr}') GROUP BY cid");

            // 将查询结果分配到对应的 cid 上
            while ($orderRow = $DB->fetch($order)) {
                $data[$orderRow['cid']]['order'] = $orderRow['order_count'];
            }
        }
        $data = array_values($data);
        usort($data, function ($a, $b) {
            return $a['sort'] - $b['sort'];
        });
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));

        break;
    // 获取商品分类
    case 'getclassfl':
        $fenlei = trim(strip_tags(daddslashes($_POST['id'])));
        if ($fenlei == "") {
            $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort desc");
        } else {
            $a = $DB->query("select * from qingka_wangke_class where status=1 and fenlei='$fenlei' order by sort desc");
        }

        $cids = [];
        $data = [];

        while ($row = $DB->fetch($a)) {
            if ($row['docking'] == 'nana') {
                $miaoshua = 1;
            } else {
                $miaoshua = 0;
            }

            if ($row['yunsuan'] == "*") {
                $price = round($row['price'] * $userrow['addprice'], 20);
                $price1 = $price;
            } elseif ($row['yunsuan'] == "+") {
                $price = round($row['price'] + $userrow['addprice'], 20);
                $price1 = $price;
            } else {
                $price = round($row['price'] * $userrow['addprice'], 20);
                $price1 = $price;
            }
            //密价
            $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$userrow['uid']}' and cid='{$row['cid']}' ");
            if ($mijia) {
                if ($mijia['mode'] == 0) {
                    $price = round($price - $mijia['price'], 20);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 1) {
                    $price = round(($row['price'] - $mijia['price']) * $userrow['addprice'], 20);
                    if ($price <= 0) {
                        $price = 0;
                    }
                } elseif ($mijia['mode'] == 2) {
                    $price = $mijia['price'];
                    if ($price <= 0) {
                        $price = 0;
                    }
                }
                $row['name'] = "【密价】{$row['name']}";
            }
            if ($price >= $price1) { //密价价格大于原价，恢复原价
                $price = $price1;
            }

            //全站一个价
            if ($row['suo'] != 0) {
                $price = $row['suo'];
            }

            $price = $price < 0.001 ? 0.001 : sprintf("%.3f", $price);

            $cids[] = $row['cid'];
            $data[$row['cid']] = array(
                'sort' => $row['sort'],
                'cid' => $row['cid'],
                'fenlei' => $row['fenlei'],
                'name' => $row['name'],
                'noun' => $row['noun'],
                'getnoun' => $row['getnoun'],
                'nocheck' => $row['nocheck'],
                'changePass' => $row['changePass'],
                'price' => $price,
                'content' => $row['content'],
                'status' => $row['status'],
                'order' => 0,
                'miaoshua' => $miaoshua
            );
        }
        // foreach ($data as $key => $row) {
        //     $sort[$key]  = $row['sort'];
        //     $cid[$key] = $row['cid'];
        //     $name[$key] = $row['name'];
        //     $noun[$key] = $row['noun'];
        //     $getnoun[$key] = $row['getnoun'];
        //     $price[$key] = $row['price'];
        //     $info[$key] = $row['info'];
        //     $content[$key] = $row['content'];
        //     $status[$key] = $row['status'];
        //     $miaoshua[$key] = $row['miaoshua'];
        // }
        if (!empty($cids)) {
            $cidStr = implode("','", $cids);
            $order = $DB->query("SELECT cid, COUNT(*) as order_count FROM qingka_wangke_order WHERE cid IN ('{$cidStr}')  GROUP BY cid");

            // 将查询结果分配到对应的 cid 上
            while ($orderRow = $DB->fetch($order)) {
                $data[$orderRow['cid']]['order'] = $orderRow['order_count'];
            }
        }
        $data = array_values($data);
        usort($data, function ($a, $b) {
            return $a['sort'] - $b['sort'];
        });
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));

        break;
    // 商品删除
    case 'class_del':
        is_admin();
        $sex = daddslashes($_POST['sex']);
        if ($userrow['uid'] == 1) {
            for ($i = 0; $i < count($sex); $i++) {
                $cid = $sex[$i];
                $DB->query("delete from qingka_wangke_class where cid='{$cid}'");
            }
            exit('{"code":1,"msg":"选择的订单已批量删除！"}');
        } else {
            exit('{"code":-1,"msg":"别乱搞，单子丢了钱你赔吗？"}');
        }

        $cid = daddslashes($_POST['cid']);
        $DB->query("delete from qingka_wangke_class where cid='$cid' ");
        jsonReturn(1, "删除成功");
        break;
    // 商品列表
    case 'classlist':
        $cx = daddslashes($_POST['cx']);
        $classname = daddslashes($_POST['classname']);
        $fenlei = daddslashes($_POST['fenlei']);
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pagesize = trim(strip_tags($cx['pagesize'])) ? trim(strip_tags($cx['pagesize'])) : 15;
        $pageu = ($page - 1) * $pagesize; //当前界面		


        $sql0 = 'where 1=1 ';
        if ($classname) {
            $sql0 = $sql0 . " and  name like '%" . $classname . "%'  ";
        }
        if ($fenlei) {
            $sql0 = $sql0 . " and  fenlei={$fenlei}  ";
        }

        $count1 = $DB->count("select count(*) from qingka_wangke_class {$sql0} ");
        $last_page = ceil($count1 / $pagesize); //取最大页数

        if ($userrow['uid'] == '1') {
            $a = $DB->query("select * from qingka_wangke_class {$sql0} order by sort asc limit $pageu,$pagesize ");
            $data = [];
            while ($row = $DB->fetch($a)) {
                $c = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$row['queryplat']}'");
                $d = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$row['docking']}'");
                $row['cx_name'] = $c['name'];
                $row['add_name'] = $d['name'];
                if ($row['queryplat'] == '0') {
                    $row['cx_name'] = '自营';
                }
                if ($row['docking'] == '0') {
                    $row['add_name'] = '自营';
                }


                $data[] = $row;
            }
            foreach ($data as $key => $rows) {
                $sort[$key] = $rows['sort'];
                $cid[$key] = $rows['cid'];
                $name[$key] = $rows['name'];
                $getnoun[$key] = $rows['getnoun'];
                $noun[$key] = $rows['noun'];
                $price[$key] = $rows['price'];
                $queryplat[$key] = $rows['queryplat'];
                $yunsuan[$key] = $rows['yunsuan'];
                $content[$key] = $rows['content'];
                $addtime[$key] = $rows['addtime'];
                $status[$key] = $rows['status'];
                $cx_names[$key] = $rows['cx_names'];
                $add_name[$key] = $rows['add_name'];
            }

            $count1 = $DB->count("select count(*) from qingka_wangke_class {$sql0} ");

            $max_sort_cid = $DB->get_row("select cid from qingka_wangke_class order by sort desc ")["cid"];
            $min_sort_cid = $DB->get_row("select cid from qingka_wangke_class order by sort  ASC ")["cid"];

            $data = array('code' => 1, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page, "count" => $count1, "pagesize" => $pagesize, 'max_sort_cid' => $max_sort_cid, 'min_sort_cid' => $min_sort_cid);
            exit(json_encode($data));
        } else {
            exit('{"code":-2,"msg":"你在干啥"}');
        }
        break;
    // 更新商品
    case 'upclass':
        is_admin();
        parse_str(daddslashes($_POST['data']), $row); //将字符串解析成多个变量
        if ($userrow['uid'] == 1) {
            if ($row['action'] == 'add') {
                $max_sort = $DB->get_row("SELECT MAX(sort) as max_sort   from qingka_wangke_class")["max_sort"];
                $DB->query("insert into qingka_wangke_class (sort,name,getnoun,noun,nocheck,changePass,price,queryplat,docking,content,addtime,status,fenlei) values ($max_sort + 1,'{$row['name']}','{$row['getnoun']}','{$row['noun']}','{$row['nocheck']}','{$row['changePass']}','{$row['price']}','{$row['queryplat']}','{$row['docking']}','{$row['content']}','{$date}','{$row['status']}','{$row['fenlei']}')");
                exit('{"code":1,"msg":"操作成功1"}');
            } else {

                $DB->query("update `qingka_wangke_class` set `name`='{$row['name']}',`getnoun`='{$row['getnoun']}',`noun`='{$row['noun']}',`nocheck`='{$row['nocheck']}',`changePass`='{$row['changePass']}',`price`='{$row['price']}',`queryplat`='{$row['queryplat']}',`docking`='{$row['docking']}',`yunsuan`='{$row['yunsuan']}',`content`='{$row['content']}',`status`='{$row['status']}',`fenlei`='{$row['fenlei']}' where cid='{$row['cid']}' ");

                exit('{"code":1,"msg":"操作成功2"}');
            }
        } else {
            exit('{"code":-2,"msg":"无权限"}');
        }
        break;
    case "upclass_pl":
        is_admin();

        $fenlei = trim(strip_tags(daddslashes($_POST['fenlei'])));
        $status = trim(strip_tags(daddslashes($_POST['status'])));
        $price = trim(strip_tags(daddslashes($_POST['price'])));
        $yunsuan = trim(strip_tags(daddslashes($_POST['yunsuan'])));
        $nocheck = trim(strip_tags(daddslashes($_POST['nocheck'])));
        $changePass = trim(strip_tags(daddslashes($_POST['changePass'])));

        $sex = daddslashes($_POST['sex']);
        if (count($sex) == 0) {
            jsonReturn("-1", "请传入商品id");
        }

        $sucess_num = 0;
        foreach ($sex as $key => $row) {

            if ($fenlei !== '') {
                $ok = $DB->query("update qingka_wangke_class set fenlei={$fenlei} where cid={$row}");
            }

            if ($status !== '') {
                $ok = $DB->query("update qingka_wangke_class set status={$status} where cid={$row}");
            }

            if ($price !== '') {
                $ok = $DB->query("update qingka_wangke_class set price={$price} where cid={$row}");
            }

            if ($yunsuan !== '') {
                $ok = $DB->query("update qingka_wangke_class set yunsuan='{$yunsuan}' where cid={$row}");
            }

            if ($nocheck !== '') {
                $ok = $DB->query("update qingka_wangke_class set nocheck='{$nocheck}' where cid={$row}");
            }

            if ($changePass !== '') {
                $ok = $DB->query("update qingka_wangke_class set changePass='{$changePass}' where cid={$row}");
            }

            if (!empty($ok)) {
                $sucess_num = $sucess_num + 1;
            }

        }
        exit(json_encode(["code" => 1, "msg" => "成功修改" . $sucess_num . "个条商品"]));
        break;
    // 商品排序
    case 'class_sort':
        is_admin();
        $type = empty(daddslashes($_POST['type'])) ? 'down' : daddslashes($_POST['type']);
        $cid = daddslashes($_POST['cid']);
        if (empty($cid)) {
            jsonReturn(-1, "未选择商品");
        }
        $now_class = $DB->get_row("select sort,cid from qingka_wangke_class where cid=$cid");
        if (empty($now_class)) {
            jsonReturn(-1, "商品不存在");
        }

        $count = $DB->count("select count(*) from qingka_wangke_class where sort='{$now_class['sort']}' ");
        $min_sort = $DB->get_row("select cid,sort from qingka_wangke_class order by sort  ASC ")["sort"];
        // 如果排序有重复的
        if ($count > 1 || $min_sort < 1) {
            $allClass = $DB->query("select cid from qingka_wangke_class order by sort asc");
            $allClass_data = [];
            while ($row = $DB->fetch($allClass)) {
                $allClass_data[] = $row;
            }
            foreach ($allClass_data as $key => $value) {
                $sort2 = $key + 1;
                $DB->query("update qingka_wangke_class set sort=$sort2 where cid='{$value['cid']}' ");
            }
        }

        $now_class = $DB->get_row("select sort,cid from qingka_wangke_class where cid=$cid");

        // 开始上下移动
        if ($type === 'top') {
            $min_sort = $DB->get_row("select cid,sort from qingka_wangke_class order by sort  ASC ")["sort"];
            $min_sort = $min_sort - 1;
            $DB->query("update qingka_wangke_class set sort=$min_sort where cid=$cid");
        } elseif ($type === 'bottom') {
            $max_sort = $DB->get_row("select cid,sort from qingka_wangke_class order by sort desc ")["sort"];
            $max_sort = $max_sort + 1;
            $DB->query("update qingka_wangke_class set sort=$max_sort where cid=$cid");
        } elseif ($type === 'up') {
            $up_class = $DB->get_row("select sort,cid from qingka_wangke_class where sort=(select max(sort) from qingka_wangke_class where sort < '{$now_class['sort']}') ");
            if (empty($up_class)) {
                jsonReturn(-1, "不能再往上移了");
            }
            $new_sort = $up_class['sort'];
            $DB->query("update qingka_wangke_class set sort=$new_sort where cid=$cid");
            $DB->query("update qingka_wangke_class set sort='{$now_class['sort']}' where cid='{$up_class['cid']}'");
        } else {
            $down_class = $DB->get_row("select sort,cid from qingka_wangke_class where sort=(select min(sort) from qingka_wangke_class where sort > '{$now_class['sort']}') ");
            if (empty($down_class)) {
                jsonReturn(-1, "不能再往下移了");
            }
            $new_sort = $down_class['sort'];
            $DB->query("update qingka_wangke_class set sort=$new_sort where cid=$cid");
            $DB->query("update qingka_wangke_class set sort='{$now_class['sort']}' where cid='{$down_class['cid']}'");
        }

        exit(json_encode(["code" => 1, "msg" => "成功"]));
        break;
    // 删除货源
    case 'huoyuan_del':
        is_admin();

        $hid = daddslashes($_POST['hid']);

        foreach ($hid as $row) {
            $a = $DB->query("delete from qingka_wangke_huoyuan where hid='$row' ");
        }

        if ($a) {
            jsonReturn(1, "删除成功");
        } else {
            jsonReturn(-1, "删除失败");
        }
        break;
    // 货源列表
    case 'getHMoney':
        $hid = daddslashes($_POST['hid']);
        if ($hid === '') {
            jsonReturn(-1, "未传入货源ID");
        }

        $now_huoyuan = $DB->get_row("select * from qingka_wangke_huoyuan where hid=$hid limit 1");
        if (empty($now_huoyuan)) {
            jsonReturn(-1, "货源不存在");
        }

        $token = $now_huoyuan["token"];
        $cookie = $now_huoyuan['cookie'];

        $data = [
            "uid" => $now_huoyuan["user"],
            "key" => $now_huoyuan["pass"],
            "token" => $now_huoyuan["token"],
        ];
        $header = [
            'Content-type:application/x-www-form-urlencoded',
            "token: " . $token,
            "cookie:" . $cookie,
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 toc"
        ];
        $ace_rl = $now_huoyuan["url"];
        $ace_url = $ace_rl . 'api.php?act=getmoney';

        $result = get_url($ace_url, $data, $cookie, $header, 5);

        $result = json_decode($result, true);
        if ($result["code"] == 1 || preg_match("/成功/", $result["msg"])) {
            exit(json_encode(["code" => 1, "money" => sprintf("%.4f", $result["money"])]));
        } else {
            jsonReturn(-1, "获取余额失败");
        }


        break;
    case 'huoyuanlist':
        $page = daddslashes($_POST['page']);
        $pagesize = 50;
        $pageu = ($page - 1) * $pagesize; //当前界面		
        $count1 = $DB->count("select count(*) from qingka_wangke_huoyuan");
        $last_page = ceil($count1 / $pagesize); //取最大页数
        if ($userrow['uid'] == '1') {
            $a = $DB->query("select * from qingka_wangke_huoyuan limit $pageu,$pagesize ");
            while ($row = $DB->fetch($a)) {
                $data[] = $row;
            }
            foreach ($data as $key => $value) {
                $order_num = $DB->count("select count(hid) from qingka_wangke_order where hid='{$value['hid']}' ");
                $data[$key]["order_num"] = $order_num;
            }
            $data = array('code' => 1, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page);
            exit(json_encode($data));
        } else {
            exit('{"code":-2,"msg":"你在干啥"}');
        }
        break;
    // 更新货源
    case 'uphuoyuan':
        is_admin();
        parse_str(daddslashes($_POST['data']), $row); //将字符串解析成多个变量

        if ($userrow['uid'] == 1) {
            if ($row['action'] == 'add') {
                $DB->query("insert into qingka_wangke_huoyuan (pt,name,url,user,pass,token,ip,cookie,addtime,ckjk,xdjk,jdjk,bsjk,ckcs,xdcs,jdcs,bscs,ck_post,xd_post,jd_post,bs_post,post,changePass_type,changePass_jk,changePass_cs,smtp_money) values ('{$row['pt']}','{$row['name']}','{$row['url']}','{$row['user']}','{$row['pass']}','{$row['token']}','{$row['ip']}','{$row['cookie']}','{$date}','{$row['ckjk']}','{$row['xdjk']}','{$row['jdjk']}','{$row['bsjk']}','{$row['ckcs']}','{$row['xdcs']}','{$row['jdcs']}','{$row['bscs']}','{$row['ck_post']}','{$row['xd_post']}','{$row['jd_post']}','{$row['bs_post']}','{$row['post']}','{$row['changePass_type']}','{$row['changePass_jk']}','{$row['changePass_cs']}','{$row['smtp_money']}')");
                exit('{"code":1,"msg":"操作成功1"}');
            } else {
                $DB->query("update `qingka_wangke_huoyuan` set `pt`='{$row['pt']}',`name`='{$row['name']}',`url`='{$row['url']}',`user`='{$row['user']}',`pass`='{$row['pass']}',`token`='{$row['token']}',`ip`='{$row['ip']}',`cookie`='{$row['cookie']}',`endtime`='{$date}',`ckjk`='{$row['ckjk']}',`xdjk`='{$row['xdjk']}',`jdjk`='{$row['jdjk']}',`bsjk`='{$row['bsjk']}',`ckcs`='{$row['ckcs']}',`xdcs`='{$row['xdcs']}',`jdcs`='{$row['jdcs']}',`bscs`='{$row['bscs']}',`ck_post`='{$row['ck_post']}',`xd_post`='{$row['xd_post']}',`jd_post`='{$row['jd_post']}',`bs_post`='{$row['bs_post']}',`post`='{$row['post']}',`changePass_type`='{$row['changePass_type']}',`changePass_jk`='{$row['changePass_jk']}',`changePass_cs`='{$row['changePass_cs']}',`smtp_money`='{$row['smtp_money']}' where hid='{$row['hid']}' ");
                exit('{"code":1,"msg":"操作成功2"}');
            }
        } else {
            exit('{"code":-2,"msg":"无权限"}');
        }
        break;
    // 退款
    case 'tk':
        $sex = daddslashes($_POST['sex']);
        if ($userrow['uid'] == 1) {
            for ($i = 0; $i < count($sex); $i++) {
                $oid = $sex[$i];
                $order = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
                $user = $DB->get_row("select * from qingka_wangke_user where uid='{$order['uid']}' ");
                $DB->query("update qingka_wangke_user set money=money+'{$order['fees']}' where uid='{$user['uid']}'");
                $DB->query("update qingka_wangke_order set status='已退款',dockstatus='4' where oid='{$oid}'");
                wlog($user['uid'], "订单退款", "订单ID：{$order['oid']} 订单信息：{$order['user']} {$order['pass']} {$order['kcname']}被管理员退款", "+{$order['fees']}");
            }
            exit('{"code":1,"msg":"选择的订单已批量退款！可在日志中查看！"}');
        } else {
            exit('{"code":-1,"msg":"无权限"}');
        }
        break;
    // 删除订单
    case 'sc':
        is_admin();
        $sex = daddslashes($_POST['sex']);
        if ($userrow['uid'] == 1) {
            for ($i = 0; $i < count($sex); $i++) {
                $oid = $sex[$i];
                $order = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
                $user = $DB->get_row("select * from qingka_wangke_user where uid='{$order['uid']}' ");
                $DB->query("delete from qingka_wangke_order where oid='{$oid}'");
                //wlog($user['uid'], "删除订单信息", "订单ID：{$order['oid']} 订单信息：{$order['user']} {$order['pass']} {$order['kcname']}被管理员删除", "+0");
            }
            exit('{"code":1,"msg":"选择的订单已批量删除！"}');
        } else {
            exit('{"code":-1,"msg":"别乱搞，单子丢了钱你赔吗？"}');
        }
        break;
    // 订单所属代理转单 
    case 'xgdl':
        is_admin();
        $sex = daddslashes($_POST['sex']);
        $setuid = daddslashes($_POST['uid']);
        $newUser = $DB->get_row("select uid,name from qingka_wangke_user where uid='{$setuid}' ");

        if ($userrow['uid'] == 1) {
            for ($i = 0; $i < count($sex); $i++) {
                $oid = $sex[$i];
                
                $order = $DB->get_row("select uid from qingka_wangke_order where oid='{$oid}' ");
                $oldUser = $DB->get_row("select uid,name from qingka_wangke_user where uid='{$order['uid']}' ");
                
                $order = $DB->query("update qingka_wangke_order set uid='{$setuid}' where oid='{$oid}' ");
                orderLogs($oid, $userrow['uid'], "订单转单", "订单所属代理从 [".$oldUser["uid"]."]".$oldUser["name"]." 修改为 [".$newUser["uid"]."]".$newUser["name"], "0");
            }
            exit('{"code":1,"msg":"选择的订单已批量修改！"}');
        } else {
            exit('{"code":-1,"msg":"别乱搞，单子丢了钱你赔吗？"}');
        }
        break;
    // 修改订单在上游的密码
    case "changePass":
        $sex = daddslashes($_POST['sex']);
        $setpass = daddslashes($_POST['pass']);
        $returnData = ["code" => 1, "msg" => "修改成功"];
        for ($i = 0; $i < count($sex); $i++) {
            $oid = $sex[$i];

            $order = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' limit 1");

            $class = $DB->get_row("select * from qingka_wangke_class where cid='{$order['cid']}' limit 1");
            if (empty($class["changePass"])) {
                jsonReturn(-1, "当前订单所在商品不支持改密");
            }

            $b = $order;
            $hid = $b["hid"];
            $yid = $b["yid"];
            $user = $b["user"];
            $cid = $b["cid"];
            $school = $b["school"];
            $user = $b["user"];
            $pass = $b["pass"];
            $kcid = $b["kcid"];
            $kcname = $b["kcname"];
            $noun = $b["noun"];
            $miaoshua = $b["miaoshua"];

            $order_hid = $order["hid"];
            $huoyuan = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$order_hid}' limit 1");
            $a = $huoyuan;
            $type = $a["pt"];
            $cookie = $a["cookie"];
            $token = $a["token"];
            $ip = $a["ip"];

            $changePass_jk = $a["changePass_jk"];
            $changePass_cs = $a["changePass_cs"];
            $POSTYPE = $a["changePass_type"];

            $data = [];
            eval ("\$data = [$changePass_cs];");
            $header = [
                'Content-type:application/x-www-form-urlencoded',
                "token: " . $token,
                "cookie:" . $cookie,
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 toc"
            ];

            $ace_rl = $a["url"];
            $ace_url = $ace_rl . $changePass_jk;

            preg_match_all('/\$(\w+)(?:\[(\d+)\])?/', $ace_url, $matches, PREG_SET_ORDER);
            // 对每个匹配的变量进行替换
            foreach ($matches as $match) {
                $var_name = $match[1];
                $index = isset($match[2]) ? $match[2] : null;

                // 获取变量的值
                if (isset(${$var_name})) {
                    $value = ${$var_name};

                    // 如果存在索引，则尝试获取数组中的元素
                    if ($index !== null && is_array($value) && isset($value[$index])) {
                        $value = $value[$index];
                    }
                } else {
                    // 如果预定义变量不存在，则使用空字符串
                    $value = "";
                }

                // 替换字符串中的变量
                $ace_url = str_replace("\${$var_name}" . ($index !== null ? "[$index]" : ""), $value, $ace_url);
            }
            $result = get_url($ace_url, $POSTYPE == '1' ? $data : false, $cookie, $header);

            $result = is_array($result) ? $result : json_decode($result, true);
            if (empty($result)) {
                jsonReturn(404, '对接接口错误');
            }

            if ($result["code"] == '1') {
            } else if ($result["code"] == 200) {
                $result["msg"] = empty($result["msg"]) ? $result["message"] : $result["msg"];
                $result["code"] = 1;
            } else {
                $result["msg"] = empty($result["msg"]) ? $result["message"] : $result["msg"];
                $result["msg"] = empty($result["msg"]) ? "上游未返回" : $result["msg"];
                $returnData["code"] = $result["code"];
                $returnData["msg"] = $result["msg"];
            }
        }
        exit(json_encode(["code" => $returnData["code"], "msg" => $returnData["msg"]]));
        break;
    case 'kbs':
        $sex = daddslashes($_POST['sex']);
        if ($userrow['uid'] != '') {
            for ($i = 0; $i < count($sex); $i++) {
                $oid = $sex[$i];
                $order = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
                $user = $DB->get_row("select * from qingka_wangke_user where uid='{$order['uid']}' ");
                //$DB->query("update qingka_wangke_user set money=money+'{$order['fees']}' where uid='{$user['uid']}'");
                if ($order['hid'] == 11 || $order['hid'] == 12) {
                    $DB->query("update qingka_wangke_order set dockstatus='0',status='待重刷',`process`='',`remarks`='',`bsnum`=bsnum+1 where oid='{$oid}' ");
                } else {
                    $DB->query("update qingka_wangke_order set status='待处理',`process`='',`remarks`='',`bsnum`=bsnum+1 where oid='{$oid}'");
                }
                //wlog($user['uid'], "删除订单信息", "订单ID：{$order['oid']} 订单信息：{$order['user']} {$order['pass']} {$order['kcname']}被管理员删除", "+0");
            }
            exit('{"code":1,"msg":"选择的订单已批量重新上号！"}');
        } else {
            exit('{"code":-1,"msg":"无权限"}');
        }
        break;
    // 删除代理
    case 'deluser':
        is_admin();
        $sex = daddslashes($_POST['sex']);
        if ($userrow['uid'] == 1) {
            for ($i = 0; $i < count($sex); $i++) {
                $uid = $sex[$i];
                $DB->query("delete from qingka_wangke_user where uid='{$uid}'");
                //wlog($user['uid'], "删除订单信息", "订单ID：{$order['oid']} 订单信息：{$order['user']} {$order['pass']} {$order['kcname']}被管理员删除", "+0");
            }
            exit('{"code":1,"msg":"选择的代理已批量删除！"}');
        } else {
            exit('{"code":-1,"msg":"别乱搞，代理丢了钱你赔吗？"}');
        }
        break;
    // 添加代理
    case 'adduser':
        if ($conf['user_htkh'] == '0') {
            jsonReturn(-1, "暂停开户，具体开放时间等通知");
        }
        parse_str(daddslashes($_POST['data']), $row); //将字符串解析成多个变量
        $type = daddslashes($_POST['type']);
        $row['user'] = trim($row['user']);
        // $row['pass'] = trim($row['pass']); 
        $row['pass'] = $conf['user_pass'];
        if ($row['name'] == '' || $row['user'] == '' || $row['pass'] == '' || $row['addprice'] == '') {
            exit('{"code":-2,"msg":"所有项目不能为空"}');
        }
        if (!preg_match('/[1-9]([0-9]{4,10})/', $row['user']))
            exit('{"code":-1,"msg":"账号必须为QQ号"}');
        if ($DB->get_row("select * from qingka_wangke_user where user='{$row['user']}' ")) {
            exit('{"code":-1,"msg":"该账号已存在"}');
        }
        if ($DB->get_row("select * from qingka_wangke_user where name='{$row['name']}' ")) {
            exit('{"code":-1,"msg":"该昵称已存在"}');
        }

        if ($row['addprice'] < $userrow['addprice']) {
            exit('{"code":-1,"msg":"费率不能比自己低哦"}');
        }

        // 		if($row['addprice']*100 % 5 !=0){
        //     		jsonReturn(-1,"请输入单价为0.05的倍数");
        // 	    }
        if ($row['addprice'] < 0.2) {
            jsonReturn(-1, "费率不合法！");
        }

        // 			if($row['addprice']>=0.2 && $row['addprice']<0.3){
        // 	            $cz=2000;		    
        // 			}elseif($row['addprice']>=0.3 && $row['addprice']<0.4){	
        // 				$cz=1000;	   
        // 			}elseif($row['addprice']>=0.4 && $row['addprice']<0.5){	
        // 				$cz=300;	   
        // 			}elseif($row['addprice']>=0.5 && $row['addprice']<0.6){	
        // 				$cz=100;   
        // 			}else{
        // 				$cz=0;		
        // 			}	
        $cz = 0;
        $h = $DB->get_row("select * from qingka_wangke_dengji where rate='{$row['addprice']}' and addkf = '1' ");
        // while ($row1 = $DB->fetch($h)) {
        //     if ($row['addprice'] == $row1['rate']) {
        //         if ($row1['addkf'] == 1) {
        //             $cz = $row1['money'];
        //         }
        //     }
        // }
        $cz = $h['money'];
        $kochu = round($cz * ($userrow['addprice'] / $row['addprice']), 2); //充值 	
        $kochu2 = $kochu + $conf['user_ktmoney'];
        if ($type != 1) {
            //jsonReturn(1,"开通扣{$conf['user_ktmoney']}<br />开户费<br />代理到账：{$cz}<br />将扣除您：{$kochu}");
            jsonReturn(1, "代理到账：{$cz}<br />手续费：{$conf['user_ktmoney']}<br />将扣除您：{$kochu} + {$conf['user_ktmoney']} = {$kochu2}");
        }
        if ($userrow['money'] >= $kochu2) {
            $DB->query("insert into qingka_wangke_user (uuid,user,pass,name,addprice,yqprice,addtime,qq,wx) values ('{$userrow['uid']}','{$row['user']}','{$row['pass']}','{$row['name']}','{$row['addprice']}','{$row['addprice']}','$date','123456','123456') ");
            $DB->query("update qingka_wangke_user set `money`=`money`-'{$conf['user_ktmoney']}' where uid='{$userrow['uid']}' ");
            wlog($userrow['uid'], "添加代理", "添加代理{$row['user']}成功!扣费{$kochu2}!", "-{$kochu2}");
            if ($cz != 0) {
                $DB->query("update qingka_wangke_user set money='$cz',zcz=zcz+'$cz' where user='{$row['user']}' ");
                $DB->query("update qingka_wangke_user set `money`=`money`-'$kochu' where uid='{$userrow['uid']}' ");
                wlog($userrow['uid'], "代理充值", "成功给账号为[{$row['user']}]的靓仔充值{$cz},手续费{$conf['user_ktmoney']},扣除{$kochu2}", -$kochu2);
                $is = $DB->get_row("select uid from qingka_wangke_user where user='{$row['user']}' limit 1");
                wlog($is['uid'], "上级充值", "你上面的靓仔[{$userrow['name']}]成功给你充值{$cz}", +$cz);
            }
            exit('{"code":1,"msg":"开通成功，默认密码：abc123456"}');
        } else {
            jsonReturn(-1, "余额不足！<br />开户需扣除：{$conf['user_ktmoney']}<br />当前余额：{$kochu}");
        }

        break;
    case "user2id":
        $user2id_return = $DB->query("select uid,name from qingka_wangke_user");
        $data = [];
        while ($row = $DB->fetch($user2id_return)) {
            $data[] = $row;
        }
        exit(json_encode(["code" => 1, "data" => $data]));
        break;
    case "dl_idname":
        $dl_idname_return = $DB->query("select uid,name,active from qingka_wangke_user order by CASE WHEN uid = 1 THEN 0 ELSE 1 END, CAST(uid AS UNSIGNED) desc");
        $data = [];
        while ($row = $DB->fetch($dl_idname_return)) {
            $data[] = $row;
        }
        exit(json_encode(["code" => 1, "data" => $data]));
        break;
    // 代理列表
    case 'userlist':
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        $qq = trim(strip_tags(daddslashes($_POST['qq'])));
        // $qq = preg_replace('/[^a-zA-Z0-9]/', '', $qq);
        $page = trim(daddslashes($_POST['page']));
        $pagesize = trim(strip_tags($_POST['pagesize'])) ? (float) trim(strip_tags($_POST['pagesize'])) : 15;
        $pageu = ($page - 1) * $pagesize; //当前界面		

        $sql = "where uid != 1";


        if ($userrow['uid'] == '1') {
            if ($qq != "" and $type == 1) {
                $sql = "where uid=" . $qq;
            } elseif ($qq != "" and $type == 2) {
                $sql = "where user='" . $qq . "'";
            } elseif ($qq != "" and $type == 3) {
                $sql = "where yqm='" . $qq . "'";
            } elseif ($qq != "" and $type == 4) {
                $sql = "where name='" . $qq . "'";
            } elseif ($qq != "" and $type == 5) {
                $sql = "where addprice='" . $qq . "'";
            } elseif ($qq != "" and $type == 6) {
                $sql = "where money='" . $qq . "'";
            } elseif ($qq != "" and $type == 7) {
                $sql = "where endtime>'" . $qq . "'";
            }
        } else {
            if ($qq != "" and $type == 1) {
                $sql = "where uuid='{$userrow['uid']}' and uid=" . $qq;
            } elseif ($qq != "" and $type == 2) {
                $sql = "where uuid='{$userrow['uid']}' and user='" . $qq . "'";
            } elseif ($qq != "" and $type == 3) {
                $sql = "where uuid='{$userrow['uid']}' and yqm='" . $qq . "'";
            } elseif ($qq != "" and $type == 4) {
                $sql = "where uuid='{$userrow['uid']}' and name='" . $qq . "'";
            } elseif ($qq != "" and $type == 5) {
                $sql = "where uuid='{$userrow['uid']}' and addprice='" . $qq . "'";
            } elseif ($qq != "" and $type == 6) {
                $sql = "where uuid='{$userrow['uid']}' and money='" . $qq . "'";
            } elseif ($qq != "" and $type == 7) {
                $sql = "where endtime>'" . $qq . "' and uuid='{$userrow['uid']}'";
            } else {
                $sql = "where uuid='{$userrow['uid']}'";
            }
        }

        // 统计
        if ($userrow["uid"] == 1) {
            $tongji = ["money_waitUse" => 0, "user_active" => 0, "admin_user" => 0,];
            $tongji["money_waitUse"] = (float) $DB->count("select COALESCE(sum(money),0) from qingka_wangke_user where uid!=1 and active=1 and COALESCE(STR_TO_DATE(SUBSTRING_INDEX(endtime, '--', 1), '%Y-%m-%d %H:%i:%s'), '1900-01-01 00:00:00') >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
            $tongji["user_active"] = (float) $DB->count("select COALESCE(count(uid),0) from qingka_wangke_user where uid!=1 and active=1 and COALESCE(STR_TO_DATE(SUBSTRING_INDEX(endtime, '--', 1), '%Y-%m-%d %H:%i:%s'), '1900-01-01 00:00:00') >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)");
            $tongji["admin_user"] = (float) $DB->count("select COALESCE(count(uid),0) from qingka_wangke_user where uid!=1 and uuid=1");
        }

        $a = $DB->query("select * from qingka_wangke_user {$sql} order by uid desc limit $pageu,$pagesize ");
        $count1 = (float) $DB->count("select count(*) from qingka_wangke_user {$sql}");
        while ($row = $DB->fetch($a)) {
            $zcz = 0;
            if ($userrow['uid'] == '1') {
            } else {
                $row['pass'] = "无权查看";
            }
            if ($userrow['uid'] != 1) {
                $row['key'] = '不允许查看';
            } elseif (empty($row['key'])) {
                $row['key'] = '';
            }

            $dd = $DB->count("select count(oid) from qingka_wangke_order where uid='{$row['uid']}' and uid != 1 ");
            //$zcz=$DB->count("select sum(money) as money from qingka_wangke_log where type='上级充值' and uid='{$row['uid']}' ");
            $row['dd'] = $dd;
            // $row['aa'] = $DB-> count("select count(uid) from qingka_wangke_user where uuid='{$row['uid']}' ");
            $row['dl_num'] = $DB->count("select count(uid) from qingka_wangke_user where uuid='{$row['uid']}' ");
            if (empty($dd)) {
                $tongji["money_waitUse"] = $tongji["money_waitUse"] - $row["money"];
            }

            // $row["dl_num"] = 0;

            $data[] = $row;
        }


        $last_page = ceil($count1 / $pagesize); //取最大页数
        $data = array('code' => 1, 'tongji' => $tongji, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page, "count" => $count1, "pagesize" => $pagesize);
        exit(json_encode($data));
        break;
    // 支付列表
    case 'paylist':
        $page = trim(daddslashes($_POST['page']));
        $limit = trim(daddslashes($_POST['limit']));
        $pageu = ($page - 1) * $limit; //当前界面	

        $sql = ($userrow['uid'] == '1' ? 'where oid!=0 ' : "where uid = '{$userrow['uid']}'  ") . " and ((type!='tourist' and type!='tourist1') OR type IS NULL)  ";
        // exit($sql);
        $a = $DB->query("select * from qingka_wangke_pay {$sql} order by oid desc limit $pageu,$limit");
        $count = $DB->count("select count(*) from qingka_wangke_pay $sql ");
        $data = [];
        while ($row = $DB->fetch($a)) {
            if ($row['status'] == 0) {
                $row['status'] = '未支付';
            } elseif ($row['status'] == 1) {
                $row['status'] = '已支付';
            }
            // if ($row['type'] == "alipay") {
            //     $row['type'] = "支付宝";
            // } elseif ($row['type'] = "vxpay") {
            //     $row['type'] = "微信";
            // } elseif ($row['type'] = "qqpay") {
            //     $row['type'] = "QQ";
            // }
            if ($row['endtime'] == '') {
                $row['endtime'] = "支付未完成";
            }
            $data[] = array(
                'oid' => $row['oid'],
                'payUser' => $row['payUser'],
                'out_trade_no' => $row['out_trade_no'],
                'trade_no' => $row['trade_no'],
                'addtime' => $row['addtime'],
                'type' => $row['type'],
                'uid' => $row['uid'],
                'endtime' => $row['endtime'],
                'name' => $row['name'],
                'money' => $row['money'],
                'money2' => $row['money2'],
                'status' => $row['status'],
                'ip' => $row['ip'],
            );
        }

        array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        exit(json_encode(['code' => 1, 'data' => $data, "count" => $count]));
        break;
    // 课程id列表
    case 'kcidlist':
        $page = trim(daddslashes($_GET['page']));
        $limit = trim(daddslashes($_GET['limit']));
        $pageu = ($page - 1) * $limit; //当前界面	
        if ($userrow['uid'] == 1) {
            $a = $DB->query("select * from qingka_wangke_order order by oid desc limit $pageu,$limit");
            $count = $DB->count("select count(*) from qingka_wangke_order");
        } else {
            $a = $DB->query("select * from qingka_wangke_order  where uid='{$userrow['uid']}' order by oid desc limit $pageu,$limit");
            $count = $DB->count("select count(*) from qingka_wangke_order  where uid='{$userrow['uid']}'");
        }
        while ($row = $DB->fetch($a)) {
            $data[] = array(
                'oid' => $row['oid'],
                'ptname' => $row['ptname'],
                'user' => $row['user'],
                'kcname' => $row['kcname'],
                'kcid' => $row['kcid'],
                'addtime' => $row['addtime'],
                'status' => $row['status'],
            );
        }

        //array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        $data = array('code' => 1, 'data' => $data, "count" => $count);
        exit(json_encode($data));
        break;
    // 日志列表
    case 'log':
        $page = trim(daddslashes($_GET['page']));
        $limit = trim(daddslashes($_GET['limit']));
        $pageu = ($page - 1) * $limit; //当前界面	
        if ($userrow['uid'] == 1) {
            $a = $DB->query("select * from qingka_wangke_log order by id desc limit $pageu,$limit");
            $count = $DB->count("select count(*) from qingka_wangke_log");
        } else {
            $a = $DB->query("select * from qingka_wangke_log where uid='{$userrow['uid']}' order by id desc limit $pageu,$limit");
            $count = $DB->count("select count(*) from qingka_wangke_log where uid='{$userrow['uid']}'");
        }

        while ($row = $DB->fetch($a)) {
            $data[] = array(
                'id' => $row['id'],
                'uid' => $row['uid'],
                'type' => $row['type'],
                'money' => $row['money'],
                'smoney' => $row['smoney'],
                'text' => $row['text'],
                'addtime' => $row['addtime'],
                'ip' => $row['ip'],
            );
        }

        //array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        $data = array('code' => 1, 'data' => $data, "count" => $count);
        exit(json_encode($data));
        break;
    // 获等级
    case 'adddjlist':
        $a = $DB->query("select * from qingka_wangke_dengji where status=1 and rate>='{$userrow['addprice']}' order by sort desc");
        while ($row = $DB->fetch($a)) {
            $data[] = array(
                'sort' => $row['sort'],
                'name' => $row['name'],
                'rate' => $row['rate'],
                'money' => $row['money'],
            );
        }
        foreach ($data as $key => $row) {
            $sort[$key] = $row['sort'];
            $name[$key] = $row['name'];
            $rate[$key] = $row['rate'];
            $money[$key] = $row['money'];
        }
        array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
        break;
    // 代理公告
    case 'user_notice':
        $notice = trim(strip_tags(daddslashes($_POST['notice'])));
        if ($DB->query("update qingka_wangke_user set notice='{$notice}' where uid='{$userrow['uid']}' ")) {
            wlog($userrow['uid'], "设置公告", "设置公告: {$notice}", 0);
            jsonReturn(1, "设置成功");
        } else {
            jsonReturn(-1, "未知异常");
        }
        break;
    case 'userjk':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $money = trim(strip_tags(daddslashes($_POST['money'])));
        if (!preg_match('/^[0-9.]+$/', $money))
            exit('{"code":-1,"msg":"充值金额不合法"}');
        //充值扣费计算：扣除费用=充值金额*(我的总费率/代理费率-等级差*2%)
        if ($money < 10 && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"最低充值10"}');
        }
        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if ($row['uuid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"该用户你的不是你的下级,无法充值"}');
        }
        if ($userrow['uid'] == $uid) {
            exit('{"code":-1,"msg":"自己不能给自己充值哦"}');
        }

        $kochu = round($money * ($userrow['addprice'] / $row['addprice']), 2); //充值	

        if ($userrow['money'] < $kochu) {
            exit('{"code":-1,"msg":"您当前余额不足,无法充值"}');
        }
        if ($kochu == 0) {
            exit('{"code":-1,"msg":"你在干你妈臭逼呢？"}');
        }
        $wdkf = round($userrow['money'] - $kochu, 2);
        $xjkf = round($row['money'] + $money, 2);
        $DB->query("update qingka_wangke_user set money='$wdkf' where uid='{$userrow['uid']}' "); //我的扣费
        $DB->query("update qingka_wangke_user set money='$xjkf',zcz=zcz+'$money' where uid='$uid' "); //下级增加	    
        wlog($userrow['uid'], "代理充值", "成功给账号为[{$row['user']}]的靓仔充值{$money},扣除{$kochu}", -$kochu);
        wlog($row['uid'], "上级充值", "{$userrow['name']}成功给你充值{$money}", +$money);
        exit('{"code":1,"msg":"充值' . $money . '成功,实际扣费' . $kochu . '"}');

        break;
    case 'userkc1':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $money = trim(strip_tags(daddslashes($_POST['money'])));
        if (!preg_match('/^[0-9.]+$/', $money))
            exit('{"code":-1,"msg":"金额不合法"}');
        //充值扣费计算：扣除费用=充值金额*(我的总费率/代理费率-等级差*2%)

        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if ($row['uuid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"该用户你的不是你的下级,无法扣除余额"}');
        }
        if ($userrow['uid'] == $uid) {
            exit('{"code":-1,"msg":"自己不能给自己扣款哦"}');
        }

        $kochu = round($money * ($userrow['addprice'] / $row['addprice']), 2); //充值	

        if ($userrow['money'] < $kochu) {
            exit('{"code":-1,"msg":"您当前余额不足,无法充值"}');
        }
        if ($kochu == 0) {
            exit('{"code":-1,"msg":"你在干你妈臭逼呢？"}');
        }
        $wdkf = round($userrow['money'] - $kochu, 2);
        $xjkf = round($row['money'] + $money, 2);
        $DB->query("update qingka_wangke_user set money='$wdkf' where uid='{$userrow['uid']}' "); //我的扣费
        $DB->query("update qingka_wangke_user set money='$xjkf',zcz=zcz+'$money' where uid='$uid' "); //下级增加	    
        wlog($userrow['uid'], "代理充值", "成功给账号为[{$row['user']}]的靓仔充值{$money},扣除{$kochu}", -$kochu);
        wlog($row['uid'], "上级充值", "{$userrow['name']}成功给你充值{$money}", +$money);
        exit('{"code":1,"msg":"充值' . $money . '成功,实际扣费' . $kochu . '"}');

        break;
    case 'usergj':
        parse_str(daddslashes($_POST['data']), $row);
        $uid = trim(strip_tags(daddslashes(trim($row['uid']))));
        $addprice = trim(strip_tags(daddslashes($row['addprice'])));
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        if (!preg_match('/^[0-9.]+$/', $addprice))
            exit('{"code":-1,"msg":"费率不合法"}');

        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if ($row['uuid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"该用户你的不是你的下级,无法修改价格"}');
        }
        if ($userrow['uid'] == $uid) {
            exit('{"code":-1,"msg":"自己不能给自己改价哦"}');
        }
        if ($userrow['addprice'] > $addprice) {
            exit('{"code":-1,"msg":"你下级的费率不能低于你哦"}');
        }


        // 	if($addprice*100 % 5 !=0){
        // 		jsonReturn(-1,"请输入单价为0.05的倍数");
        // }

        if ($addprice == $row['addprice']) {
            jsonReturn(-1, "该代理已经是{$addprice}费率了，你还修改啥");
        }
        if ($addprice > $row['addprice'] && $userrow['uid'] != 1) {
            jsonReturn(-1, "下调费率，请联系管理员");
        }

        if ($addprice < '0' && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"你在干什么？"}');
        }

        //降价扣费计算：下级余额 /当前费率 *修改费率 ；
        $money = round($row['money'] / $row['addprice'] * $addprice, 2); //涨降价余额变动,自动调费
        $money1 = $money - $row['money']; //日志显示变动余额

        // 		if($addprice>=0.2 && $addprice<0.3){
        //             $cz=2000;		    
        // 		}elseif($addprice>=0.3 && $addprice<0.4){	
        // 			$cz=1000;	    
        // 		}elseif($addprice>=0.4 && $addprice<0.5){	
        // 			$cz=300;	   
        // 		}elseif($addprice>=0.5 && $addprice<0.6){	
        // 			$cz=100;     
        // 		}else{
        // 			$cz=0;		
        // 		}	
        $cz = 0;
        $h = $DB->query("select * from qingka_wangke_dengji");
        while ($row1 = $DB->fetch($h)) {
            if ($addprice == $row1['rate']) {
                if ($row1['gjkf'] == 1) {
                    $cz = $row1['money'];
                }
            }
        }
        $kochu = round($cz * ($userrow['addprice'] / $addprice), 2); //充值	
        $kochu2 = $kochu + $money + 3;
        if ($type != 1) {
            jsonReturn(1, "改价手续费3，并自动给下级[UID:{$uid}]充值{$cz}，将扣除{$kochu}余额");
        }

        if ($userrow['money'] < $kochu) {
            jsonReturn(-1, "余额不足,改价需扣3手续费,及余额{$kochu}");
        } else {
            $DB->query("update qingka_wangke_user set money=money-3 where uid='{$userrow['uid']}' ");
            $DB->query("update qingka_wangke_user set money='$money',addprice='$addprice' where uid='$uid' "); //调费       
            wlog($userrow['uid'], "修改费率", "修改代理{$row['name']},费率：{$addprice},扣除手续费3", "-3");
            wlog($uid, "修改费率", "{$userrow['name']}修改你的费率为：{$addprice},系统根据比例自动调整价格", $money1);
            if ($cz != 0) {
                $DB->query("update qingka_wangke_user set money=money-'{$kochu}' where uid='{$userrow['uid']}' "); //我的扣费
                $DB->query("update qingka_wangke_user set money=money+'{$cz}',zcz=zcz+'$cz' where uid='$uid' "); //下级增加	    
                wlog($userrow['uid'], "代理充值", "成功给账号为[{$row['user']}]的靓仔充值{$cz},扣除{$kochu}", -$kochu);
                wlog($uid, "上级充值", "{$userrow['name']}成功给你充值{$cz}", +$cz);
            }
            exit('{"code":1,"msg":"改价成功"}');
        }
        break;
    case 'user_czmm':
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        if ($userrow['uid'] == $uid) {
            jsonReturn(-1, "自己不能给自己重置哦");
        }
        $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
        if ($row['uuid'] != $userrow['uid'] && $userrow['uid'] != 1) {
            exit('{"code":-1,"msg":"该用户你的不是你的下级,无法修改价格"}');
        } else {
            $DB->query("update qingka_wangke_user set pass='{$conf['user_pass']}' where uid='{$uid}' ");
            wlog($row['uid'], "重置密码", "成功重置UID为{$uid}的密码为{$conf['user_pass']}", 0);
            jsonReturn(1, "成功重置密码为{$conf['user_pass']}");
        }
        break;
    case "jcckxz":
        is_admin();
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        if (empty($uid)) {
            jsonReturn(-1, "参数不合法");
        }
        $row = $DB->get_row("select uid from qingka_wangke_user where uid=$uid");
        if (empty($row)) {
            jsonReturn(-1, "代理不存在");
        }
        $DB->query("update qingka_wangke_user set ck=xd where uid=$uid");
        jsonReturn(1, "解除成功");
        break;
    case "czAuth_user":
        if ($userrow["uid"] != 1) {
            jsonReturn(-1, "非管理员不允许操作");
        }
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        if (empty($uid)) {
            jsonReturn(-1, "参数不合法");
        }
        $row = $DB->get_row("select uid from qingka_wangke_user where uid=$uid");
        if (empty($row)) {
            jsonReturn(-1, "代理不存在");
        }
        $czAuthReturn = $DB->get_row("select czAuth from qingka_wangke_user where uid=$uid")["czAuth"];
        $czAuth = $czAuthReturn == 1 ? 0 : 1;
        $DB->query("update qingka_wangke_user set czAuth=$czAuth");
        jsonReturn(1, $czAuth ? '开启成功' : '关闭成功');
        break;
    case 'user_ban':
        is_admin();
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $active = trim(strip_tags(daddslashes($_POST['active'])));
        if ($active == 1) {
            $a = 0;
            $b = "封禁代理";
        } else {
            $a = 1;
            $b = "解封代理";
        }
        $DB->query("update qingka_wangke_user set active='$a' where uid='{$uid}' ");
        wlog($userrow['uid'], $b, "{$b}[UID {$uid}]成功", 0);
        jsonReturn(1, "操作成功");

        break;
    case 'loglist':
        $page = trim(strip_tags(daddslashes(trim($_POST['page']))));
        $pagesize = trim(strip_tags(daddslashes(trim($_POST['limit']))));
        $type = trim(strip_tags(daddslashes(trim($_POST['type']))));
        $types = trim(strip_tags(daddslashes(trim($_POST['types']))));
        $qq = daddslashes(trim($_POST['qq']));
        $pageu = ($page - 1) * $pagesize; //当前界面		
        if ($userrow['uid'] != '1') {
            $sql1 = "where uid='{$userrow['uid']}'";
        } else {
            $sql1 = "where 1=1";
        }
        if ($type != '') {
            $sql2 = " and type='$type'";
        }
        if ($types != '') {
            if ($types == '1') {
                $sql3 = " and uid='$qq'";
            } elseif ($types == '2') {
                $sql3 = " and text like '%" . $qq . "%' ";
            } elseif ($types == '3') {
                $sql3 = " and money like '%" . $qq . "%' ";
            } elseif ($types == '4') {
                $sql3 = " and addtime='$qq'";
            }
        }
        $sql = $sql1 . $sql2 . $sql3;
        $a = $DB->query("select * from qingka_wangke_log {$sql} order by id desc limit  $pageu,$pagesize ");
        $count1 = $DB->count("select count(*) from qingka_wangke_log {$sql} ");
        while ($row = $DB->fetch($a)) {
            $data[] = $row;
        }
        $last_page = ceil($count1 / $pagesize); //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page, "count" => $count1);
        exit(json_encode($data));
        break;
    case 'djlist':
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pagesize = 500;
        $pageu = ($page - 1) * $pagesize; //当前界面		
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "无权限");
        }

        $allClass = $DB->query("select id from qingka_wangke_dengji order by CAST(sort AS UNSIGNED) asc");
        $allClass_data = [];
        while ($row = $DB->fetch($allClass)) {
            $allClass_data[] = $row;
        }
        foreach ($allClass_data as $key => $value) {
            $sort2 = $key + 1;
            $DB->query("update qingka_wangke_dengji set sort=$sort2 where id='{$value['id']}' ");
        }

        $a = $DB->query("select * from qingka_wangke_dengji ORDER BY sort");
        $count1 = $DB->count("select count(*) from qingka_wangke_dengji");
        while ($row = $DB->fetch($a)) {
            $data[] = array(
                'id' => $row['id'],
                'sort' => $row['sort'],
                'name' => $row['name'],
                'rate' => $row['rate'],
                'money' => $row['money'],
                'addkf' => $row['addkf'],
                'gjkf' => $row['gjkf'],
                'czAuth' => $row['czAuth'],
                'status' => $row['status'],
                'time' => $row['time'],
            );
        }
        array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        $last_page = ceil($count1 / $pagesize); //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page);
        exit(json_encode($data));
        break;
    // 添加等级
    case 'dj':
        is_admin();
        $data = daddslashes($_POST['data']);
        $active = trim(strip_tags(daddslashes(trim($_POST['active']))));
        $id = trim(strip_tags(daddslashes(trim($data['id']))));
        $put = trim(strip_tags(daddslashes(trim($data['put']))));
        $name = trim(strip_tags(daddslashes(trim($data['name']))));
        $rate = trim(strip_tags(daddslashes(trim($data['rate']))));
        $money = trim(strip_tags(daddslashes(trim($data['money']))));
        $status = trim(strip_tags(daddslashes(trim($data['status']))));
        $addkf = trim(strip_tags(daddslashes(trim($data['addkf']))));
        $gjkf = trim(strip_tags(daddslashes(trim($data['gjkf']))));
        $czAuth = trim(strip_tags(daddslashes(trim($data['czAuth']))));

        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "无权限！");
        }
        if ($active == '1') { //添加

            $put = empty($put) ? "0" : $put;
            $DB->query("insert into qingka_wangke_dengji (sort,name,rate,money,addkf,gjkf,czAuth,status,time) values ('$put','$name','$rate','$money','$addkf','$gjkf','$czAuth','1','{$date}')");

            $allClass = $DB->query("select id from qingka_wangke_dengji order by CAST(sort AS UNSIGNED) asc");
            $allClass_data = [];
            while ($row = $DB->fetch($allClass)) {
                $allClass_data[] = $row;
            }
            foreach ($allClass_data as $key => $value) {
                $sort2 = $key + 1;
                $DB->query("update qingka_wangke_dengji set sort=$sort2 where id='{$value['id']}' ");
            }

            jsonReturn(1, "添加成功");
        } elseif ($active == '2') { //修改 
            $DB->query("update qingka_wangke_dengji set `name`='$name',`rate`='$rate',`money`='$money',`addkf`='$addkf',`gjkf`='$gjkf',`czAuth`='$czAuth',`status`='$status' where id='$id'");
            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "不知道你在干什么");
        }
        break;
    // 等级删除
    case 'dj_del':
        is_admin();
        $id = daddslashes($_POST['id']);

        foreach ($id as $row) {
            $a = $DB->query("delete from qingka_wangke_dengji where id='$row' ");
        }

        jsonReturn(1, "删除成功");
        break;
    case 'dj_sort':

        is_admin();
        // 接收POST请求中的type和id
        $type = $_POST['type'];
        $id = $_POST['id'];

        $now_class = $DB->get_row("select sort,id from qingka_wangke_dengji where id=$id");
        $count = $DB->count("select count(*) from qingka_wangke_dengji where sort='{$now_class['sort']}' ");
        $min_sort = $DB->get_row("select id,sort from qingka_wangke_dengji order by sort  ASC ")["sort"];

        // 如果排序有重复的
        if ($count > 1 || $min_sort < 1) {

            $allClass = $DB->query("select id from qingka_wangke_dengji order by CAST(sort AS UNSIGNED) asc");
            $allClass_data = [];
            while ($row = $DB->fetch($allClass)) {
                $allClass_data[] = $row;
            }
            foreach ($allClass_data as $key => $value) {
                $sort2 = $key + 1;
                $DB->query("update qingka_wangke_dengji set sort=$sort2 where id='{$value['id']}' ");
            }
        }

        $now_class = $DB->get_row("select sort,id from qingka_wangke_dengji where id=$id");
        $now_class['sort'] = (int) $now_class['sort'];
        // 开始上下移动
        if ($type === 'top') {
            $min_sort = $DB->get_row("select id,sort from qingka_wangke_dengji order by CAST(sort AS UNSIGNED)  ASC ")["sort"];
            $min_sort = $min_sort - 1;
            $DB->query("update qingka_wangke_dengji set sort=$min_sort where id=$id");
        } elseif ($type === 'bottom') {
            $max_sort = $DB->get_row("select id,sort from qingka_wangke_dengji order by CAST(sort AS UNSIGNED) desc ")["sort"];
            $max_sort = $max_sort + 1;
            $DB->query("update qingka_wangke_dengji set sort=$max_sort where id=$id");
        } elseif ($type === 'up') {
            $up_class = $DB->get_row("select sort,id from qingka_wangke_dengji where CAST(sort AS UNSIGNED)=(select max(CAST(sort AS UNSIGNED)) from qingka_wangke_dengji where CAST(sort AS UNSIGNED) < '{$now_class['sort']}') ");
            if (empty($up_class)) {
                jsonReturn(-1, "不能再往上移了");
            }
            $new_sort = $up_class['sort'];
            $DB->query("update qingka_wangke_dengji set sort=$new_sort where id=$id");
            $DB->query("update qingka_wangke_dengji set sort='{$now_class['sort']}' where id='{$up_class['id']}'");
        } else {
            $down_class = $DB->get_row("select sort,id from qingka_wangke_dengji where CAST(sort AS UNSIGNED)=(select min(CAST(sort AS UNSIGNED)) from qingka_wangke_dengji where CAST(sort AS UNSIGNED) > '{$now_class['sort']}') ");
            if (empty($down_class)) {
                jsonReturn(-1, "不能再往下移了");
            }

            $new_sort = $down_class['sort'];
            $DB->query("update qingka_wangke_dengji set sort=$new_sort where id=$id");
            $DB->query("update qingka_wangke_dengji set sort='{$now_class['sort']}' where id='{$down_class['id']}'");
        }

        exit(json_encode(["code" => 1, "msg" => "成功"]));
        break;
    // 货源删除
    case 'hy_del':
        is_admin();
        $hid = daddslashes($_POST['hid']);
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "无权限");
        }
        $DB->query("delete from qingka_wangke_huoyuan where hid='$hid' ");
        jsonReturn(1, "删除成功");
        break;
    // 分类类别
    case 'fllist':
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $pagesize = 500;
        $pageu = ($page - 1) * $pagesize; //当前界面		
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "无权限");
        }

        $fenlei_count = $DB->count("select count(*) from qingka_wangke_fenlei");
        if (empty($fenlei_count)) {
            $insert_id = $DB->insert("insert into qingka_wangke_fenlei (sort,name,status,time) values ('0','默认分类','1','{$date}')");
            $DB->query("update qingka_wangke_class set fenlei= {$insert_id} ");
        }

        $a = $DB->query("select * from qingka_wangke_fenlei ORDER BY sort");
        $count1 = $DB->count("select count(*) from qingka_wangke_fenlei");

        while ($row = $DB->fetch($a)) {
            $aaa = $DB->count("select count(*) from qingka_wangke_class where fenlei={$row['id']}");


            $data[] = array(
                'id' => $row['id'],
                'sort' => $row['sort'],
                'name' => $row['name'],
                'rate' => $row['rate'],
                'money' => $row['money'],
                'addkf' => $row['addkf'],
                'gjkf' => $row['gjkf'],
                'status' => $row['status'],
                'time' => $row['time'],
                'cnum' => $aaa,
            );
        }
        foreach ($data as $key => $row) {
            $id[$key] = $row['id'];
            $sort[$key] = $row['sort'];
            $name[$key] = $row['name'];
            $rate[$key] = $row['rate'];
            $money[$key] = $row['money'];
            $addkf[$key] = $row['addkf'];
            $gjkf[$key] = $row['gjkf'];
            $status[$key] = $row['status'];
            $time[$key] = $row['time'];
        }
        array_multisort($sort, SORT_ASC, $rate, SORT_ASC, $data);
        $last_page = ceil($count1 / $pagesize); //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page);
        exit(json_encode($data));
        break;
    case 'fl':
        is_admin();
        $data = daddslashes($_POST['data']);

        $active = trim(strip_tags(daddslashes(trim($_POST['active']))));

        $addType = trim(strip_tags(daddslashes(trim($data['addType']))));
        $id = trim(strip_tags(daddslashes(trim($data['id']))));
        $sort = trim(strip_tags(daddslashes(trim($data['sort']))));
        $name = trim(strip_tags(daddslashes(trim($data['name']))));
        $status = trim(strip_tags(daddslashes(trim($data['status']))));

        if ($active == '1') { //添加
            if ($addType == '1') {
                $sort = empty($sort) ? "0" : $sort;
                $DB->query("insert into qingka_wangke_fenlei (sort,name,status,time) values ('$sort','$name','1','{$date}')");
                jsonReturn(1, "添加成功1");
            } else {
                $put = trim(strip_tags(daddslashes(trim($data['put']))));
                $put = empty($put) ? "0" : $put;

                $DB->query("insert into qingka_wangke_fenlei (sort,name,status,time) values ('$put','$name','1','{$date}')");

                $allClass = $DB->query("select id from qingka_wangke_fenlei order by CAST(sort AS UNSIGNED) asc");
                $allClass_data = [];
                while ($row = $DB->fetch($allClass)) {
                    $allClass_data[] = $row;
                }
                foreach ($allClass_data as $key => $value) {
                    $sort2 = $key + 1;
                    $DB->query("update qingka_wangke_fenlei set sort=$sort2 where id='{$value['id']}' ");
                }

                jsonReturn(1, "添加成功2");
            }
        } elseif ($active == '2') { //修改 

            unset($data["id"]);

            // 获取 qingka_wangke_fenlei 表的字段信息
            $fieldsResult = $DB->query("SHOW COLUMNS FROM qingka_wangke_fenlei");
            $fields = [];
            while ($row = $fieldsResult->fetch_assoc()) {
                $fields[] = $row['Field'];
            }

            $updateQuery = "UPDATE qingka_wangke_fenlei SET ";
            $updateFields = [];

            // 遍历 $data 数组，检查是否在表的字段中存在，存在则添加到更新字段数组中
            foreach ($data as $key => $value) {
                if (in_array($key, $fields)) {
                    // 添加到更新字段数组中
                    $updateFields[] = "{$key} = '{$value}'";
                }
            }

            // 构建完整的更新语句
            $updateQuery .= implode(", ", $updateFields);
            $updateQuery .= " WHERE id = '{$id}'";

            // 执行更新语句
            $DB->query($updateQuery);

            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "不知道你在干什么");
        }
        break;
    // 分类排序
    case 'fenlei_sort':
        is_admin();
        // 接收POST请求中的type和id
        $type = $_POST['type'];
        $id = $_POST['id'];

        $now_class = $DB->get_row("select sort,id from qingka_wangke_fenlei where id=$id");
        $count = $DB->count("select count(*) from qingka_wangke_fenlei where sort='{$now_class['sort']}' ");
        $min_sort = $DB->get_row("select id,sort from qingka_wangke_fenlei order by sort  ASC ")["sort"];

        // 如果排序有重复的
        if ($count > 1 || $min_sort < 1) {

            $allClass = $DB->query("select id from qingka_wangke_fenlei order by CAST(sort AS UNSIGNED) asc");
            $allClass_data = [];
            while ($row = $DB->fetch($allClass)) {
                $allClass_data[] = $row;
            }
            foreach ($allClass_data as $key => $value) {
                $sort2 = $key + 1;
                $DB->query("update qingka_wangke_fenlei set sort=$sort2 where id='{$value['id']}' ");
            }
        }

        $now_class = $DB->get_row("select sort,id from qingka_wangke_fenlei where id=$id");
        $now_class['sort'] = (int) $now_class['sort'];
        // 开始上下移动
        if ($type === 'top') {
            $min_sort = $DB->get_row("select id,sort from qingka_wangke_fenlei order by CAST(sort AS UNSIGNED)  ASC ")["sort"];
            $min_sort = $min_sort - 1;
            $DB->query("update qingka_wangke_fenlei set sort=$min_sort where id=$id");
        } elseif ($type === 'bottom') {
            $max_sort = $DB->get_row("select id,sort from qingka_wangke_fenlei order by CAST(sort AS UNSIGNED) desc ")["sort"];
            $max_sort = $max_sort + 1;
            $DB->query("update qingka_wangke_fenlei set sort=$max_sort where id=$id");
        } elseif ($type === 'up') {
            $up_class = $DB->get_row("select sort,id from qingka_wangke_fenlei where CAST(sort AS UNSIGNED)=(select max(CAST(sort AS UNSIGNED)) from qingka_wangke_fenlei where CAST(sort AS UNSIGNED) < '{$now_class['sort']}') ");
            if (empty($up_class)) {
                jsonReturn(-1, "不能再往上移了");
            }
            $new_sort = $up_class['sort'];
            $DB->query("update qingka_wangke_fenlei set sort=$new_sort where id=$id");
            $DB->query("update qingka_wangke_fenlei set sort='{$now_class['sort']}' where id='{$up_class['id']}'");
        } else {
            $down_class = $DB->get_row("select sort,id from qingka_wangke_fenlei where CAST(sort AS UNSIGNED)=(select min(CAST(sort AS UNSIGNED)) from qingka_wangke_fenlei where CAST(sort AS UNSIGNED) > '{$now_class['sort']}') ");
            if (empty($down_class)) {
                jsonReturn(-1, "不能再往下移了");
            }

            $new_sort = $down_class['sort'];
            $DB->query("update qingka_wangke_fenlei set sort=$new_sort where id=$id");
            $DB->query("update qingka_wangke_fenlei set sort='{$now_class['sort']}' where id='{$down_class['id']}'");
        }

        exit(json_encode(["code" => 1, "msg" => "成功"]));
        // 获取当前id的sort
        // $sql = "SELECT sort FROM qingka_wangke_fenlei WHERE id = $id";
        // $result = $DB->query($sql);

        // if ($result->num_rows > 0) {
        //     $row = $result->fetch_assoc();
        //     $currentSort = $row["sort"];

        //     // 检查表中是否存在重复的sort值
        //     $sql = "SELECT sort FROM qingka_wangke_fenlei GROUP BY sort HAVING COUNT(*) > 1";
        //     $result = $DB->query($sql);

        //     // 如果存在重复的sort值，则重新排序
        //     if ($result->num_rows > 0) {
        //         $i = 1;
        //         while ($row = $result->fetch_assoc()) {
        //             $sort = $row["sort"];
        //             $DB->query("UPDATE qingka_wangke_fenlei SET sort = $i WHERE sort = $sort");
        //             $i++;
        //         }
        //     }

        //     // 根据type更新排序
        //     if ($type == "up") {
        //         // 找到比当前sort小的第一个数据的sort
        //         $sql = "SELECT sort FROM qingka_wangke_fenlei WHERE sort < $currentSort ORDER BY sort DESC LIMIT 1";
        //         $result = $DB->query($sql);
        //         if ($result->num_rows > 0) {
        //             $row = $result->fetch_assoc();
        //             $targetSort = $row["sort"];
        //         } else {
        //             $targetSort = 0; // 如果没有比当前sort小的数据，设为0
        //         }
        //     } elseif ($type == "down") {
        //         // 找到比当前sort大的第一个数据的sort
        //         $sql = "SELECT sort FROM qingka_wangke_fenlei WHERE sort > $currentSort ORDER BY sort ASC LIMIT 1";
        //         $result = $DB->query($sql);
        //         if ($result->num_rows > 0) {
        //             $row = $result->fetch_assoc();
        //             $targetSort = $row["sort"];
        //         } else {
        //             $targetSort = 999999; // 如果没有比当前sort大的数据，设为一个很大的数
        //         }
        //     }

        //     // 更新当前id的sort和目标id的sort
        //     $DB->query("UPDATE qingka_wangke_fenlei SET sort = $targetSort WHERE id = $id");
        //     $DB->query("UPDATE qingka_wangke_fenlei SET sort = $currentSort WHERE sort = $targetSort AND id != $id");

        //     echo "排序更新成功";
        // } else {
        //     echo "未找到相应的数据";
        // }

        break;
    // 分类删除
    case 'fl_del':
        is_admin();

        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "无权限");
        }
        $id = daddslashes($_POST['id']);
        $DB->query("delete from qingka_wangke_class where fenlei='{$id}'");
        $DB->query("delete from qingka_wangke_fenlei where id='$id' ");

        $allClass = $DB->query("select id from qingka_wangke_fenlei order by CAST(sort AS UNSIGNED) asc");
        $allClass_data = [];
        while ($row = $DB->fetch($allClass)) {
            $allClass_data[] = $row;
        }
        foreach ($allClass_data as $key => $value) {
            $sort2 = $key + 1;
            $DB->query("update qingka_wangke_fenlei set sort=$sort2 where id='{$value['id']}' ");
        }

        $fenlei_count = $DB->count("select count(*) from qingka_wangke_fenlei");
        if (empty($fenlei_count)) {
            $insert_id = $DB->insert("insert into qingka_wangke_fenlei (sort,name,status,time) values ('0','默认分类','1','{$date}')");
            $DB->query("update qingka_wangke_class set fenlei= {$insert_id} ");
        }

        jsonReturn(1, "删除成功");
        break;
    // 密价列表
    case 'mijialist':
        is_admin();
        $page = trim(strip_tags(daddslashes($_POST['page'])));
        $uid = trim(strip_tags(daddslashes($_POST['type'])));
        $pagesize = 5000;

        $pageu = ($page - 1) * $pagesize; //当前界面		
        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "无权限");
        }

        if ($uid != '') {
            $sql = "where uid='$uid'";
        }


        $uid2 = daddslashes($_POST['uid']);
        if ($uid2) {
            $sql = "where uid like '%" . $uid2 . "%' ";
        }


        $a = $DB->query("select * from qingka_wangke_mijia {$sql}");
        $count1 = $DB->count("select count(*) from qingka_wangke_mijia {$sql} ");
        while ($row = $DB->fetch($a)) {
            $r = $DB->get_row("select * from qingka_wangke_class where cid='{$row['cid']}' ");
            $row['name'] = $r['name'];
            $row['fenlei'] = $r['fenlei'];
            $data[] = $row;
        }
        $last_page = ceil($count1 / $pagesize); //取最大页数
        $data = array('code' => 1, 'data' => $data, "current_page" => (int) $page, "last_page" => $last_page, "uid" => $userrow['uid'], "A" => $sql);
        exit(json_encode($data));
        break;
    // 添加密价
    case 'mijia':
        is_admin();
        $data = daddslashes($_POST['data']);
        $active = trim(strip_tags(daddslashes(trim($_POST['active']))));

        $uid = trim(strip_tags(daddslashes(trim($data['uid']))));
        $mid = trim(strip_tags(daddslashes(trim($data['mid']))));
        $mode = trim(strip_tags(daddslashes(trim($data['mode']))));
        $cid = daddslashes($data['cid']);
        $price = trim(strip_tags(daddslashes(trim($data['price']))));

        if ($userrow['uid'] != '1') {
            jsonReturn(-1, "不知道你在干什么");
        }
        if ($active == '1') { //添加
            $type = trim(strip_tags(daddslashes(trim($data['type']))));

            if ($type == 1) {
                // 添加多个商品
                foreach ($cid as $key => $row) {
                    $DB->query("insert into qingka_wangke_mijia (uid,cid,mode,price,addtime) values ('$uid','{$row}','$mode','$price','{$date}')");
                }
                jsonReturn(1, "添加成功");
            } elseif ($type == 2) {
                // 添加单个分类的商品
                $fid = $cid;
                $fenlei_class = $DB->query("select cid from qingka_wangke_class where fenlei={$fid} ");
                while ($row = $DB->fetch($fenlei_class)) {
                    $DB->query("insert into qingka_wangke_mijia (uid,cid,mode,price,addtime) values ('$uid','{$row['cid']}','$mode','$price','{$date}')");
                }
                jsonReturn(1, "添加成功");
            } elseif ($type == 3) {
                // 添加多个分类的商品
                $fid = $cid;
                foreach ($fid as $key => $row) {
                    $fenlei_class = $DB->query("select cid from qingka_wangke_class where fenlei={$row} ");
                    while ($row = $DB->fetch($fenlei_class)) {
                        $DB->query("insert into qingka_wangke_mijia (uid,cid,mode,price,addtime) values ('$uid','{$row['cid']}','$mode','$price','{$date}')");
                    }
                }

                jsonReturn(1, "添加成功");
            } else {
                // 添加单个商品
                $DB->query("insert into qingka_wangke_mijia (uid,cid,mode,price,addtime) values ('$uid','$cid','$mode','$price','{$date}')");
                jsonReturn(1, "添加成功");
            }
        } elseif ($active == '2') { //修改
            $DB->query("update qingka_wangke_mijia set `price`='$price',`mode`='$mode',`uid`='$uid',`cid`='$cid',`uptime`='{$date}' where mid='$mid' ");
            jsonReturn(1, "修改成功");
        } else {
            jsonReturn(-1, "不知道你在干什么");
        }
        break;
    // 删除密价
    case 'mijia_del':
        is_admin();
        $mid = daddslashes($_POST['mid']);

        foreach ($mid as $row) {
            $delReturn = $DB->query("delete from qingka_wangke_mijia where mid='$row' ");
        }

        jsonReturn(1, "删除成功");
        break;
    // 上级迁移
    case 'sjqy':
        $uuid = daddslashes($_POST['uid']);
        $yqm = daddslashes($_POST['yqm']);
        if ($uuid == '' || $yqm == '') {
            exit('{"code":0,"msg":"所有项目不能为空"}');
        }
        if ($conf['sjqykg'] == 0) {
            exit('{"code":0,"msg":"管理员未打开迁移功能"}');
        } elseif ($conf['sjqykg'] == 1) {
            $row = $DB->get_row("select * from qingka_wangke_user where uid='$uuid' limit 1");
            if ($row) {
                if ($yqm == $row['yqm']) {
                    $row1 = $DB->get_row("select * from qingka_wangke_user where uid='{$userrow['uid']}' limit 1");
                    if ($row1['uuid'] != $uuid) {
                        if ($row1['uid'] != $uuid) {
                            $ztdate = date("Y-m-d", strtotime("-7 day"));
                            $row11 = $DB->get_row("select * from qingka_wangke_user where uid='{$userrow['uuid']}' limit 1");
                            if ($row11['endtime'] < $zhdl) {
                                $DB->query("update qingka_wangke_user set `uuid`='$uuid' where uid='{$userrow['uid']}' ");
                                if ($DB) {
                                    jsonReturn(1, "迁移成功,您已迁移至[UID$uuid]的名下");
                                } else {
                                    jsonReturn(-1, "迁移失败,未知错误");
                                }
                            } else {
                                jsonReturn(-1, "上级在七天内有登陆记录，禁止转移");
                            }
                        } else {
                            jsonReturn(-1, "禁止填写自己的UID");
                        }
                    } else {
                        jsonReturn(-1, "该用户已经是你的上级了");
                    }
                } else {
                    jsonReturn(-1, "非该用户邀请码，请重新输入");
                }
            } else {
                jsonReturn(-1, "UID不存在，请重新输入");
            }
        }
        break;
    // 批量同步
    case 'plzt':
        $redis = new Redis();
        $redis->connect("127.0.0.1", "6379");
        $sex = daddslashes($_POST['sex']);
        $rediscode = $redis->ping();
        $sex_count = count($sex);
        $DB->query("update qingka_wangke_user set jd1=jd1+$sex_count where uid='{$userrow['uid']}' ");
        if ($rediscode == true) {
            for ($i = 0; $i < count($sex); $i++) {
                $oid = $sex[$i];
                $redis->lPush("plztoid", $oid);
            }
            wlog($userrow['uid'], "批量同步状态", "批量同步状态入队成功，共入队{$i}条", 0);
            jsonReturn(1, "批量同步状态入队成功，共入队{$i}条，请耐心等待同步");
        } else {
            jsonReturn(-1, "入队失败");
        }

        break;
    // 批量补刷
    case 'plbs':
        $redis = new Redis();
        $redis->connect("127.0.0.1", "6379");
        $sex = daddslashes($_POST['sex']);
        $sex_count = count($sex);
        $DB->query("update qingka_wangke_user set bs1=bs1+$sex_count where uid='{$userrow['uid']}' ");

        $rediscode = $redis->ping();
        if ($rediscode == true) {
            for ($i = 0; $i < count($sex); $i++) {
                $oid = $sex[$i];
                $b = $DB->get_row("select hid,cid,dockstatus,status,bsnum from qingka_wangke_order where oid='{$oid}' ");
                if (preg_match("/{$conf['bs0_rw']}/", trim($b['status']))) {
                } elseif (!preg_match("/{$conf['bs_cl']}/", trim($b['dockstatus']))) {
                } else {
                    $redis->lPush("plbsoid", $oid);
                    $DB->query("update qingka_wangke_order set status='待重刷',`bsnum`=bsnum+1 where oid='{$oid}' ");
                }
            }
            wlog($userrow['uid'], "批量补刷", "批量补刷入队成功，共入队{$i}条", 0);
            jsonReturn(1, "批量同步状态入队成功，共入队{$i}条，请耐心等待补刷成功");
        } else {
            jsonReturn(-1, "入队失败");
        }
        break;
    // 一键对接老版
    case 'yjdj':
        if ($userrow['uid'] == 1) {
            $hid = trim(strip_tags(daddslashes($_GET['hid'])));
            $fid = trim(strip_tags(daddslashes($_GET['fid'])));
            $pricee = trim(strip_tags(daddslashes($_GET['pricee'])));
            $category = trim(strip_tags(daddslashes($_GET['category'])));
            $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$hid}' ");
            $data = array("uid" => $a["user"], "key" => $a["pass"], "fenlei" => $category);
            $er_url = "{$a["url"]}api.php?act=getclass";
            $result = get_url($er_url, $data);
            $result1 = json_decode($result, true);
            $f_get = $DB->get_row("select id from qingka_wangke_fenlei where id = '{$fid}' ");

            if (!$fid || !$f_get) {
                $now_time = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");
                $DB->query("insert into qingka_wangke_fenlei (sort,name,status,time) values ('-999','新渠道','1','{$now_time}')");
                $f_get = $DB->get_row("select id from qingka_wangke_fenlei where time = '{$now_time}' ");
                $fid = $f_get['id'];
            }
            $b = $DB->get_row("select * from qingka_wangke_fenlei where id='{$fid}' ORDER BY id DESC LIMIT 0,1");
            $data = $result1["data"];
            $numItems = count($data);
            $i = 0;
            foreach ($data as $k => $value) {
                if ($value['fenlei'] == $category) { // 对比用户输入的分类ID
                    $price = $value['price'] * $pricee; // 1.05 就是增加 5% 看不懂问数学老师
                    $sort = $i + 1; // 排序字段，可以根据需要进行调整
                    $DB->query("insert into qingka_wangke_class (name, getnoun, noun, fenlei, queryplat, docking, price, sort, content) values ('{$value['name']}', '{$value['cid']}', '{$value['cid']}', '{$fid}', '$hid', '$hid', '{$price}', '{$sort}', '{$value['content']}')");
                    $i++;
                }
            }
            jsonReturn(1, "已上架{$a["name"]}的全部分类22的项目，共计{$i}个，并自动新建分类到【{$b["name"]}】中，价格、排序和内容已更新");
        } else {
            jsonReturn(0, "无权限");
        }
        break;
    case 'hotsList':
        $type = trim(strip_tags(daddslashes($_POST['type']))) ? trim(strip_tags(daddslashes($_POST['type']))) : 'today';
        $today = date('Y-m-d');

        $classResult = $DB->query("select * from qingka_wangke_class");
        $classData = [];
        while ($row = $DB->fetch($classResult)) {
            $classData[] = $row;
        }

        $data = [];
        switch ($type) {
            case 'daili':
                $userResult = $DB->query("select * from qingka_wangke_user where uid!=1");
                $userData = [];
                while ($row = $DB->fetch($userResult)) {
                    $userData[] = $row;
                }
                foreach ($userData as $key => $value) {
                    $orderNum = $DB->count("select count(*) from qingka_wangke_order where uid='{$value['uid']}' ");
                    $data[] = [
                        "name" => substr($value["user"], 0, 4) . str_repeat('*', strlen($value["user"]) - 4),
                        "orderNum" => (float) $orderNum,
                    ];
                }
                usort($data, function ($a, $b) {
                    return $b['orderNum'] <=> $a['orderNum'];
                });
                $data = array_slice($data, 0, 15);
                break;
            case 'all':
                foreach ($classData as $key => $value) {
                    $orderNum = $DB->count("select count(*) from qingka_wangke_order where cid='{$value['cid']}' ");
                    $data[] = [
                        "cid" => $value["cid"],
                        "name" => $value["name"],
                        "orderNum" => (float) $orderNum,
                    ];
                }
                usort($data, function ($a, $b) {
                    return $b['orderNum'] <=> $a['orderNum'];
                });
                $data = array_slice($data, 0, 15);
                foreach ($data as $key => $value) {
                    $data[$key]["orderNum"] = '****' . substr($value["orderNum"], 0, 2);
                }
                break;
            case 'week':
                foreach ($classData as $key => $value) {
                    $orderNum = $DB->count("select count(*) from qingka_wangke_order where cid='{$value['cid']}' and YEARWEEK(addtime, 1) = YEARWEEK(NOW(), 1) ");
                    $data[] = [
                        "cid" => $value["cid"],
                        "name" => $value["name"],
                        "orderNum" => (float) $orderNum,
                    ];
                }
                usort($data, function ($a, $b) {
                    return $b['orderNum'] <=> $a['orderNum'];
                });
                $data = array_slice($data, 0, 15);
                foreach ($data as $key => $value) {
                    $data[$key]["orderNum"] = '****' . substr($value["orderNum"], 0, 2);
                }
                break;
            default:
                foreach ($classData as $key => $value) {
                    $orderNum = $DB->count("select count(*) from qingka_wangke_order where cid='{$value['cid']}' and Date(addtime) = '{$today}' ");
                    $data[] = [
                        "cid" => $value["cid"],
                        "name" => $value["name"],
                        "orderNum" => (float) $orderNum,
                    ];
                }
                usort($data, function ($a, $b) {
                    return $b['orderNum'] <=> $a['orderNum'];
                });
                $data = array_slice($data, 0, 15);
                foreach ($data as $key => $value) {
                    $data[$key]["orderNum"] = '****' . substr($value["orderNum"], 0, 2);
                }
                break;
        }

        exit(json_encode(["code" => 1, "data" => $data]));
        break;
    // 获取首页实时公告
    case "homenotice_get":
        // 仅取10条
        $DB->query("update qingka_wangke_homenotice SET readUIDS = CONCAT(readUIDS, '{$userrow['uid']},') where readUIDS NOT LIKE '%{$userrow['uid']},%' ");

        $homenotice_return = $DB->query(" select sort,title,content,top,author,uptime,addtime,readUIDS from qingka_wangke_homenotice where status!=0 order by sort desc limit 10");
        $homenotice = [];
        while ($row = $DB->fetch($homenotice_return)) {
            if ($row['name'] == '' || $row['name'] == 'undefined') {
                $row['name'] = 'null';
            }


            if (empty($row["readUIDS"])) {
                $row["readUIDS"] = 0;
            } else {
                $row["readUIDS"] = explode(",", $row["readUIDS"]);
                $row["readUIDS"] = count($row["readUIDS"]) - 1;
            }

            $homenotice[] = $row;
        }

        jsonReturnData(1, $homenotice);
        break;
}
$DB->close();
