<?php

$root = $_SERVER['DOCUMENT_ROOT'];
include($root . '/confing/common.php');

// 安全权限控制
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($userrow)) {
    exit("你想干啥？");
}

$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");

function is_admin()
{
    global $userrow;
    if (empty($userrow["uid"]) || (string)$userrow["uid"] !== '1') {
        http_response_code(403);
        header("Content-Type: text/plain; charset=utf-8");
        exit("403 Forbidden - Permission Denied");
    }
}

// 时间转换工具
function dateTime($time,$type=null){
    // 转换成秒
    if(!empty($type)){
        return DateTime::createFromFormat("Y-m-d H:i:s--u", $time)->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("U.u");
    }
    // 转换成格式化的日期时间
    return DateTime::createFromFormat('U.u', sprintf("%.6F", $time))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");
}

// 卡密过期检测
function kami_endV(){
    global $DB;
    $DB->query("update qingka_wangke_kami set status='2' where status NOT IN ('0', '2') and endtime IS NOT NULL and endtime <> '' and endtime < NOW()  ");
    return;
}

$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

switch ($act) {
    case 'kamilist':
        $cx = daddslashes($_POST['cx']);
        $page = trim(strip_tags(daddslashes($_POST['page']))) ? trim(strip_tags(daddslashes($_POST['page']))) : 1;
        $pagesize = trim(strip_tags($cx['pagesize'])) ? trim(strip_tags($cx['pagesize'])) : 25;
        $pageu = ($page - 1) * $pagesize; //当前界面


        kami_endV();

        $result = $DB->query("select * from qingka_wangke_kami  $sql  order by id desc limit $pageu,$pagesize ");
        
        

        $data = [];

        if ($result) {
            while ($row = $DB->fetch($result)) {
                
                $nowDateTime = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("U.u");
                // if(!empty($row["endtime"])&&$nowDateTime > dateTime($row["endtime"],1) && $row["status"] !== '2'){
                //     $DB->query("update qingka_wangke_kami set status = '2' , upTime = '{$date}' where id = '{$row['id']}' ");
                // }
                
                if ($row['name'] == '' || $row['name'] == 'undefined') {
                    $row['name'] = 'null';
                }
                $data[] = $row;
            }

            $count = $DB->count("select count(*) from qingka_wangke_kami");
            $last_page = ceil($count / $pagesize); //取最大页数

            $responseData = ["code" => 1, "data" => $data, "last_page" => (int)$last_page, 'count' => (int)$count, 'pagesize' => (int)$pagesize, "current_page" => (int)$page];
        } else {
            $responseData = ["code" => -1, "msg" => '获取失败'];
        }
        exit(json_encode($responseData));
        break;
        // 卡密生成
    case 'km_sc':
        is_admin();
        $codeKey = trim(strip_tags(daddslashes($_POST['codeKey']))); // 加密钥匙
        $num = trim(strip_tags(daddslashes($_POST['num']))); // 生成数量
        $price = trim(strip_tags(daddslashes($_POST['price']))); // 每个金额
        $endtime = trim(strip_tags(daddslashes($_POST['endtime']))); // 过期时间
        $endtime = !empty($endtime)?dateTime((float)$endtime / 1000):'';
        $onlynum = trim(strip_tags(daddslashes($_POST['onlynum']))); // 当前批次卡密每个代理可领数量
        
        $generator = new CardGenerator($codeKey);

        // 生成 5 个金额为 100 的卡密
        $cards = $generator->generateCard((float)$price, (float)$num);
        $cardsData = [];

        foreach ($cards as $card) {
            $amount = $generator->decryptAmount($card['encryptedAmount']);
            $DB->query(" insert into qingka_wangke_kami (codeKey,code,price,onlynum,status,addtime,endtime) values ('{$codeKey}','{$card['code']}','{$amount}','{$onlynum}','1','{$date}','{$endtime}') ");

            $cardsData[] = ["code" => $card['code'], "price" => $amount];
        }

        if (count($cardsData)  > 0) {
            exit(json_encode(["code" => 1, "msg" => "生成成功", "num" => count($cardsData)]));
        } else {
            exit(json_encode(["code" => -1, "msg" => "生成失败"]));
        }

        break;
        // 卡密信息更新
    case 'kami_up':
        is_admin();
        $id = trim(strip_tags(daddslashes($_POST['id'])));
        $data = daddslashes($_POST['data']);
        $DB->query("update qingka_wangke_kami set status = '{$data['status']}' ,price = '{$data['price']}' ,codeKey = '{$data['codeKey']}', upTime = '{$date}' where id = '{$id}' ");
        exit(json_encode(["code" => 1, "id" => "$id"]));
        break;
        // 卡密删除
    case 'kami_del':
        is_admin();
        $sex = daddslashes($_POST['sex']);
        $deletedIds = [];
        foreach ($sex as $id) {
            $result = $DB->query("DELETE FROM qingka_wangke_kami WHERE id='{$id}'");
            if (!$result) {
                $deletedIds[] = $id;
            }
        }
        if (count($deletedIds) > 0) {
            exit(json_encode(["code" => -1, "msg" => "删除失败", "failed_ids" => implode(",", $deletedIds)]));
        } else {
            exit(json_encode(["code" => 1, "msg" => "删除成功"]));
        }
        break;
        // 卡密使用验证
    case 'kami_v':
        kami_endV();
        $type = trim(strip_tags(daddslashes($_POST['type'])));
        $code = trim(strip_tags(daddslashes($_POST['kamiCode'])));
        $kami = $DB->get_row("select * from qingka_wangke_kami where code = '{$code}' limit 1 ");
        if ($kami) {
            if ($kami["status"] === '1') {
                if ($type) {
                    $b = $DB->get_row("select * from qingka_wangke_user where uid='{$userrow['uid']}' limit 1 ");
                    $DB->query("update qingka_wangke_user set money = money + '{$kami['price']}',zcz = zcz + '{$kami['price']}' where uid='{$userrow['uid']}'  ");
                    $DB->query("update qingka_wangke_kami set status=0,user='{$b['user']}',user_uid='{$b['uid']}',usetime='{$date}' where code = '{$code}' ");
                    $DB->query(" insert into qingka_wangke_pay ( out_trade_no,status,type,uid,num,addtime,endtime,name,money,ip,domain ) values ( '{$kami['code']}','1','卡密','{$userrow['uid']}','{$kami['price']}','{$date}','{$date}','卡密充值-{$kami['price']}','{$kami['price']}','{$clientip}','{$_SERVER['HTTP_HOST']}' ) ");
                    wlog($userrow['uid'], "卡密充值", "成功充值：{$kami['price']}", "{$kami['price']}");
                    exit(json_encode(["code" => 1, "msg" => "充值成功！", "data" => $b]));
                } else {
                    $aa = $DB->count("select COUNT(*) from qingka_wangke_kami where user_uid = '{$userrow['uid']}' and addtime = '{$kami['addtime']}'  ");
                    if(!empty($aa) && $aa === $kami["onlynum"]){
                        exit(json_encode(["code" => -1, "msg" => "当前批次卡密已不能领取！"]));
                    }
                    exit(json_encode(["code" => 2, "msg" => "卡密可用！","price"=>$kami["price"]]));
                }
            } else {
                if($kami["status"] === '2'){
                    exit(json_encode(["code" => -1, "msg" => "卡密已过期！"]));
                }else{
                    exit(json_encode(["code" => -1, "msg" => "卡密已被使用！"]));
                }
            }
        } else {
            exit(json_encode(["code" => -1, "msg" => "卡密不存在！"]));
        }
        break;
    default:
        // 请求参数值限制
        exit("你想干啥？");
        break;
}
