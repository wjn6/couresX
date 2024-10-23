<?php
include ('confing/common.php');
include ('ayconfig.php');
$host = $_SERVER['HTTP_HOST'];
$php_Self = substr($_SERVER['PHP_SELF'], strripos($_SERVER['PHP_SELF'], "/") + 1);
if ($php_Self != "gd.php") {
    $msg = '%E6%96%87%E4%BB%B6%E9%94%99%E8%AF%AF';
    $msg = urldecode($msg);
    exit(json_encode(['code' => - 1, 'msg' => $msg]));
}

// 检测是否为管理员，若不是，输出403
function is_admin()
{
    global $userrow;
    if (empty($userrow["uid"]) || (string)$userrow["uid"] !== '1') {
        http_response_code(403);
        header("Content-Type: text/plain; charset=utf-8");
        exit("403 Forbidden - Permission Denied1");
    }
}

switch ($act) {
    case 'addgd':
        $title = trim(strip_tags(daddslashes($_POST['title'])));
        $region = trim(strip_tags(daddslashes($_POST['region'])));
        $content = trim(strip_tags(daddslashes($_POST['content'])));
        $addtime =trim(strip_tags(daddslashes($_POST['time'])));
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $oid = trim(strip_tags(daddslashes($_POST['oid'])));
        
        if ($title == "" || $region == "" || $content == "") {
            exit('{"code":-1,"msg":"务必保证每项不能为空"}');
        }
        $new_content = $content .''. 'ô' .''. $addtime .''. '^';
        
        $DB->query("insert into qingka_wangke_gongdan (title,region,content,uid,oid,state,addtime) values ('$title','$region','$new_content','{$userrow['uid']}','{$oid}','待回复','$addtime')");
         
        $b = $DB->get_row("select * from qingka_wangke_gongdan where addtime='$addtime'");
        $b_uid = $b['uid'];
        $c = $DB->get_row("select * from qingka_wangke_user where uid='$b_uid'");
        
        exit('{"code":1,"msg":"提交成功！"}');
    break;
    case 'gdlist':
        $list = daddslashes($_POST['list']);
        $title = trim(strip_tags($list['title']));
        $region = trim(strip_tags($list['region']));
        $content = trim(strip_tags($list['content']));
        $answer = trim(strip_tags($list['answer']));
        if ($userrow['uid'] != '1') {
            $sql1 = "where uid='{$userrow['uid']}'";
        } else {
            $sql1 = "where 1=1";
        }
        $data= [];
        $a = $DB->query("select * from qingka_wangke_gongdan {$sql1} order by gid desc");
        while ($row = $DB->fetch($a)) {
            
            $oidInfo = [
                "oid" => "",
                "user" => "",
                "kcname" => "",
            ];
            $order = $DB->get_row("select oid,user,kcname,ptname from qingka_wangke_order where oid = '{$row['oid']}'" );
            if(!empty($order)){
                $oidInfo["oid"] = $order["oid"];
                $oidInfo["user"] = $order["user"];
                $oidInfo["kcname"] = $order["oid"];
                $oidInfo["ptname"] = $order["ptname"];
            }
            
            $row["oidInfo"] = $oidInfo;
            
            $data[] = $row;
        }
        
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
    break;
    case 'shan':
        is_admin();
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $b = $DB->get_row("select * from qingka_wangke_gongdan where gid='{$gid}'");
        if ($userrow['uid'] != $b['uid'] && $userrow['uid'] != '1') {
            exit('{"code":-1,"msg":"该工单不是你的！无法删除！"}');
        }
        $DB->query("delete from qingka_wangke_gongdan where gid='{$gid}'");
        exit('{"code":1,"msg":"删除成功！"}');
    break;
    case 'gbgd':
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $is=$DB->query("update qingka_wangke_gongdan set `state`='已关闭' where gid='$gid'");
        exit('{"code":1,"msg":"操作成功！"}');
    break;
    case 'wjgd':
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $is=$DB->query("update qingka_wangke_gongdan set `state`='已完结' where gid='$gid'");
        exit('{"code":1,"msg":"操作成功！"}');
    break;
    case 'bclgd':
        admin();
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $is=$DB->query("update qingka_wangke_gongdan set `state`='已忽略' where gid='$gid'");
        exit('{"code":1,"msg":"操作成功！"}');
    break;
    case 'answer':
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $answer = trim(strip_tags(daddslashes($_POST['answer'])));
        $addtime = trim(strip_tags(daddslashes($_POST['time'])));
        $uid = trim(strip_tags(daddslashes($_POST['uid'])));
        $b = $DB->get_row("select * from qingka_wangke_gongdan where gid='{$gid}'");
        // if ($userrow['uid'] != '1') {
        //     exit('{"code":-1,"msg":"无权限！"}');
        // }
        
        $state  = $uid === '1'?'已回复':'待回复';
        		
        $old_content = $DB->get_row("select * from qingka_wangke_gongdan  where gid='$gid'")['content'];
        $new_content = $old_content .''. $answer .''. 'ô' .''. $uid .''. '∫' .''. $addtime .''. '^';
         $DB->query("update qingka_wangke_gongdan set `content`= '$new_content' ,`state`='$state' where gid='$gid'");
         
        //  $options = array(
        //     'http' => array(
        //       'method' => 'POST',
        //      'header' => 'Content-type:application/x-www-form-urlencoded',
        //      'content' => http_build_query(
        //              array(
        //                  'j_uid' => $uid === '1'?  $b['uid'] : 1 ,
        //                  'title' => '☪ 你的工单有新回复',
        //                  'content' =>  '
        //                     <h1>你的工单被回复啦！</h1>
        //                     <hr />
        //                     <p><b>UID：</b>'. $b['uid'] .'</p>
        //                     <p><b>工单标题：</b>' . $b['title'] . '</p>
        //                     <p><b>工单分类：</b>' . $b['region'] . '</p>
        //                     <p><b>回复内容：</b>' . $answer . '</p>
        //                     <p>快去网站回复工单吧~</p>' . DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u"),
        //             )
        //          ),
        //       'timeout' => 15 * 60 // 超时时间（单位:s）
        //     )
        //   );
        //   $context = stream_context_create($options);
        //   $result = file_get_contents('https://'.$host.'/PHPMailer/fs.php', false, $context);
         
        exit('{"code":1,"msg":"回复成功！"}');
        // exit($new_content);
    break;
    case 'bohui':
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $answer = trim(strip_tags(daddslashes($_POST['answer'])));
        $b = $DB->get_row("select * from qingka_wangke_gongdan where gid='{$gid}'");
        if ($userrow['uid'] != '1') {
            exit('{"code":-1,"msg":"无权限！"}');
        }
        $DB->query("update qingka_wangke_gongdan set `answer`='$answer',`state`='已驳回' where gid='$gid'");
        $a = $DB->get_row("select * from qingka_wangke_user where uid='{$b['uid']}'");
        $com = '@88.com';
        $rec = $a['user'] . $com;
         $url = "https://acewk.tk/set.php?mod=setmail&biaoti={$b['title']}&content={$b['content']}&answer=$answer&rec=$rec";
         fopen($url, 'r');
        exit('{"code":1,"msg":"已驳回！"}');
    break;
    case 'toanswer':
        $gid = trim(strip_tags(daddslashes($_POST['gid'])));
        $toanswer = trim(strip_tags(daddslashes($_POST['toanswer'])));
        $b = $DB->get_row("select * from qingka_wangke_gongdan where gid='{$gid}'");
        $DB->query("update qingka_wangke_gongdan set `content`='$toanswer',`state`='待回复' where gid='$gid'");
        // $url = "http://wk.ty520.top/mail/answergd/tohf/set.php?mod=setmail&name={$userrow['name']}&biaoti={$b['title']}&content=$toanswer";
        // fopen($url, 'r');
        exit('{"code":1,"msg":"成功回复！"}');
    break;
    }
?>