<?php
include_once ('confing/common.php');
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

// 允许来自所有域的请求
header("Access-Control-Allow-Origin: *");
// 允许使用的请求方法
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// 允许的自定义请求头
header("Access-Control-Allow-Headers: Content-Type, Authorization");

global $conf;
$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");


if (!$conf['settings']) {
  exit('{"code":-1,"msg":"API功能已关闭，请联系管理员！"}');
} else {

    // 权限验证
  $uid = trim(strip_tags(daddslashes($_POST['uid'])));
  $key = trim(strip_tags(daddslashes($_POST['key'])));
  
  if($act != 'chadan2' && $act !== 'budan'){
      if(empty($key) || empty($uid)){
        jsonReturn(0, '所有项目不能为空1');
      }
      $row = $DB->get_row("select * from qingka_wangke_user where uid=$uid limit 1");
      if(empty($row)){
          jsonReturn(0,"UID不存在");
      }
        if($row["key"] != $key){
            jsonReturn(0, 'Key错误或未开通');
        }
  }

  switch ($act) {
    case 'getmoney': //查询当前余额

      wlog($uid, "api->查询余额", "UID {$uid} | 剩余余额：{$row['money']}", 0);
       $result = [
          'code' => 1,
          'msg' => '查询成功',
          'money' => $row['money']
        ];
        exit(json_encode($result));
      break;
    case 'get': //单查询
      $ckmoney = $conf['api_ck'];
      
      
      $platform = daddslashes($_POST['platform']);
      $school = daddslashes($_POST['school']);
      $user = daddslashes($_POST['user']);
      $pass = daddslashes($_POST['pass']);
      $type = daddslashes($_POST['type']);
      
      $ck = intval($row["ck"]);
      $xd = intval($row["xd"]);
      $xdb = $ck - $xd;// 差值
      
      if (empty($uid) || empty($key) || empty($platform) || empty($user) || empty($pass)) {
        jsonReturn(0,"所有项目不能为空");
      }
      $DB->query("update qingka_wangke_user set ck=ck+1 where uid='{$uid}' ");
      
      $rs = $DB->get_row("select * from qingka_wangke_class where cid='$platform' limit 1 ");
      
      if ($row['money'] < $ckmoney) {
        $result = ["code" => -2, "msg" => "余额小于{$ckmoney}禁止调用查课"];
        exit(json_encode($result));
      } elseif ($rs['status'] == 0) {
        $result = ["code" => -2, "msg" => "网课已下架禁止查课！"];
        exit(json_encode($result));
      } else {
        if ($xdb < $conf['api_ck_threshold']) {
            // 查课扣费
            if(!empty($conf["api_ckkf"])){
                $ckkf = $row['money'] - $conf["api_ckkf"];
                 wlog($uid, "api->查课扣费", "UID {$uid} | 查课扣除：{$conf['api_ckkf']}", -$conf["api_ckkf"]);
                $DB->query("update qingka_wangke_user set money='$ckkf' where uid='$uid' ");
            }
            
          $DB->query("update qingka_wangke_user set ck=ck+1 where uid=$uid");
            
          $rs = $DB->get_row("select * from qingka_wangke_class where cid='$platform' limit 1 ");
          $userinfo = (empty($school)?"自动识别":$school).' '.$user.' '.$pass;
            $userinfo2 = explode(" ", $userinfo); //分割
            $result = getWk($rs,$userinfo2);
          
          $result['userinfo'] = $userinfo;
          wlog($uid, "api->查课", "UID {$uid} | 查课信息：{$school} {$user} {$pass}", 0);

          if ($type == "xiaochu") {
            foreach ($result['data'] as $row) {
              if ($value == '') {
                $value = $row['name'];
              } else {
                $value = $value . ',' . $row['name'];
              }
            }
            $v[0] = $rs['name'];
            $v[1] = $user;
            $v[2] = $pass;
            $v[3] = $school;
            $v[4] = $value;
            $data = array(
              'code' => $result['code'],
              'msg' => $result['msg'],
              'data' => $v,
              'js' => '',
              'info' => '昔日之苦，安知异日不在尝之? 共勉'
            );
            exit(json_encode($data));
          } else {
            exit(json_encode($result));
          }
        } else {
         jsonReturn(-2,"恶意查课，请联系管理员解除");
          
        }
      }
      break;

    case 'add': //单下单
      $xdmoney = $conf['api_xd'];
      
      $platform = daddslashes($_POST['platform']);
      $school = daddslashes($_POST['school']);
      $user = daddslashes($_POST['user']);
      $pass = daddslashes($_POST['pass']);
      $kcid = daddslashes($_POST['kcid']);
      $kcname = daddslashes($_POST['kcname']);
      $clientip = real_ip();
      
      // 重复订单拦截
      $repeatOrder = $DB->get_row("select * from  qingka_wangke_order where user='{$user}' and pass='{$pass}' and  kcname='{$kcname}' and kcid='{$kcid}' and cid='{$platform}' and addtime >= DATE_SUB(NOW(), INTERVAL 90 DAY)  order by addtime desc limit 1");
      if(!empty($repeatOrder)){
          
        jsonReturn(-69,"重复订单，请手动点击一下同步");
      }
      
      if (empty($uid)|| empty($key) || empty($platform) || empty($user) || empty($pass) || empty($kcname)) {
        jsonReturn(-1,"所有项目不能为空");
      }
      $DB->query("update qingka_wangke_user set xd=xd+1 where uid='{$uid}' ");
      
      $rs = $DB->get_row("select * from qingka_wangke_class where cid='$platform' limit 1 ");
      
      if ($row['money'] < $xdmoney) {
        $result = array("code" => -2, "msg" => "余额小于{$xdmoney}禁止调用API下单");
        exit(json_encode($result));
      } elseif ($rs['status'] == 0) {
        $result = array("code" => -2, "msg" => "商品已下架，禁止下单！");
        exit(json_encode($result));
      } else {
        $rs = $DB->get_row("select * from qingka_wangke_class where cid='$platform' limit 1 ");
        $res = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$docking}' limit 1 ");
        if ($rs['yunsuan'] == "*") {
          $danjia = round($rs['price'] * $row['addprice'], 2);
          $danjia1 = $danjia;
        } elseif ($rs['yunsuan'] == "+") {
          $danjia = round($rs['price'] + $row['addprice'], 2);
          $danjia1 = $danjia;
        } else {
          $danjia = round($rs['price'] * $row['addprice'], 2);
          $danjia1 = $danjia;
        }
        //密价
        $mijia = $DB->get_row("select * from qingka_wangke_mijia where uid='{$uid}' and cid='{$platform}' limit 1 ");
        if (!empty($mijia)) {
          if ($mijia['mode'] == 0) {
            $danjia = round($danjia - $mijia['price'], 2);
            if ($danjia <= 0) {
              $danjia = 0;
            }
          } elseif ($mijia['mode'] == 1) {
            $danjia = round(($rs['price'] - $mijia['price']) * $row['addprice'], 2);
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
        
        if ($danjia >= $danjia1) { //密价价格大于原价，恢复原价
          $danjia = $danjia1;
        }
        if ($danjia == 0 || $row['addprice'] < 0.01) {
          exit('{"code":-1,"msg":"大佬，我得罪不起您，我小本生意，有哪里得罪之处，还望多多包涵"}');
        }
        if ($res['pt'] == 'wkm4') {
          $m4 = getWk($rs['queryplat'], $rs['getnoun'], $school, $user, $pass, $rs['name']);
          if ($m4['code'] == '1') {
            for ($i = 0; $i < count($m4['data']); $i++) {
              $kcid = $m4['data'][$i]['id'];
              $kcname1 = $m4['data'][$i]['name'];
              if ($kcname1 == $kcname) {
                break;
              } else {
                exit('{"code":-1,"msg":"请完整输入课程名字","status":-1,"message":"请完整输入课程名字"}');
              }
            }
          }
        }

        $c = explode(",", $kcname);
        $d = explode(",", $kcid);
        for ($i = 0; $i < count($c); $i++) {
          if ($row['money'] < $danjia * count($c)) {
            exit('{"code":-1,"msg":"余额不足以本次提交"}');
            return;
          }

          // if($DB->get_row("select * from qingka_wangke_order where ptname='{$rs['name']}' and school='$school' and user='$user' and pass='$pass' and kcid='$kcid' and kcname='$kcname' ")){
          //                   $dockstatus='3';//重复下单
          //    }else

          if ($rs['docking'] == '0' || $rs['docking'] == "6" || $rs['docking'] == 10) {
            $dockstatus = '99';
          } else {
            $dockstatus = '0';
          }
          $date1 = $date;
          $is = $DB->query("insert into qingka_wangke_order (uid,cid,hid,ptname,school,user,pass,kcid,kcname,fees,noun,miaoshua,addtime,ip,dockstatus) values ('{$uid}','{$rs['cid']}','{$rs['docking']}','{$rs['name']}','{$school}','$user','$pass','$d[$i]','$c[$i]','{$danjia}','{$rs['noun']}','$miaoshua','$date1','$clientip','$dockstatus') "); //将对应课程写入数据库	             
          if ($is) {

            $get_data = $DB->get_row("SELECT oid FROM qingka_wangke_order WHERE addtime='{$date1}' and  user = '{$user}' limit 1 ");

            $DB->query("update qingka_wangke_user set money=money-'{$danjia}' where uid='{$row['uid']}' ");
            
            wlog($uid, "api->下单", "UID {$uid} | 添加任务：{$school} {$user} {$pass} {$c[$i]} 扣除：{$danjia}！", -$danjia);
            
            $ok = 1;
          }else{
              jsonReturn(-1,"下单失败");
          }
        }
        if ($ok == 1) {
            $DB->query("update qingka_wangke_user set xd=xd+1 where uid=$uid");
          $result = array("code" => 0, 'msg' => '提交成功', "status" => 0, "message" => "提交成功", "id" => $get_data['oid'], "yid" => $get_data['oid'], "oid" => $get_data['oid']);
          exit(json_encode($result));
        } else {
          exit('{"code":-1,"msg":"请完整输入课程名字","status":-1,"message":"请完整输入课程名字"}');
        }
      }
      break;
    case 'getadd': //查询判断下单
    
      $platform = daddslashes($_POST['platform']);
      $school = daddslashes($_POST['school']);
      $user = daddslashes($_POST['user']);
      $pass = daddslashes($_POST['pass']);
      $kcname = daddslashes($_POST['kcname']);
      $miaoshua = 0;
      $clientip = real_ip();
      
      if ($uid == '' || $key == '' || $platform == '' || $school == '' || $user == '' || $pass == '' || $kcname == '') {
        exit('{"code":0,"msg":"所有项目不能为空"}');
      }
      $DB->query("update qingka_wangke_user set xd=xd+1 where uid='{$uid}' ");
      $row = $DB->get_row("select * from qingka_wangke_user where uid='$uid' limit 1");
      if ($row['key'] != $key) {
        exit('{"code":-2,"msg":"密匙错误"}');
      } else {
        $rs = $DB->get_row("select * from qingka_wangke_class where cid='$platform' limit 1 ");
        //$danjia=$rs['price']*$row['addprice'];

        if ($rs['yunsuan'] == "*") {
          $danjia = round($rs['price'] * $row['addprice'], 2);
        } elseif ($rs['yunsuan'] == "+") {
          $danjia = round($rs['price'] + $row['addprice'], 2);
        } else {
          $danjia = round($rs['price'] * $row['addprice'], 2);
        }

        if ($danjia == 0 || $row['addprice'] < 0.1) {
          exit('{"code":-1,"msg":"大佬，我得罪不起您，我小本生意，有哪里得罪之处，还望多多包涵"}');
        }
        if ($row['money'] < $danjia) {
          exit('{"code":-1,"msg":"余额不足"}');
        }
        $a = getWk($rs['queryplat'], $rs['getnoun'], $school, $user, $pass, $rs['name']);

        if ($a['code'] == '1') {
          for ($i = 0; $i < count($a['data']); $i++) {
            $kcid1 = $a['data'][$i]['id'];
            $kcname1 = $a['data'][$i]['name'];
            similar_text($kcname1, $kcname, $percent);
            if ($percent > "90%") {
              if ($rs['yunsuan'] == "*") {
                $danjia = round($rs['price'] * $row['addprice'], 2);
              } elseif ($rs['yunsuan'] == "+") {
                $danjia = round($rs['price'] + $row['addprice'], 2);
              } else {
                $danjia = round($rs['price'] * $row['addprice'], 2);
              }
              if ($rs['docking'] == '0') {
                $dockstatus = '99';
              } else {
                $dockstatus = '0';
              }
              $DB->query("insert into qingka_wangke_order (uid,cid,hid,ptname,school,user,pass,kcid,kcname,fees,noun,miaoshua,addtime,ip,dockstatus) values ('{$uid}','{$rs['cid']}','{$rs['docking']}','{$rs['name']}','{$school}','$user','$pass','$kcid1','$kcname1','{$danjia}','{$rs['noun']}','$miaoshua','$date','$clientip','$dockstatus') "); //将对应课程写入数据库	               	           	              	           	               
              $DB->query("update qingka_wangke_user set money=money-'{$danjia}' where uid='$uid' limit 1 ");
              wlog($row['uid'], "API添加任务", "{$user} {$pass} {$kcname} 扣除{$danjia}元！", -$danjia);
              $ok = 1;
              break;
            }
          }
          if ($ok == 1) {
            exit('{"code":0,"msg":"提交成功","status":0,"message":"提交成功","id":"订单号登录后台自行查看，老子懒得写了"}');
          } else {
            exit('{"code":-1,"msg":"请完整输入课程名字","status":-1,"message":"请完整输入课程名字"}');
          }
        } else {
          $result = array("code" => -1, 'msg' => $a[0]['msg']);
          exit(json_encode($result));
        }
      }
      break;
    case 'chadan':

      $username = trim(strip_tags(daddslashes($_POST['username'])));

      if (empty($username) || empty($uid) || empty($key)) {
        jsonReturn(-1,"参数不完整");
      }
      $DB->query("update qingka_wangke_user set jd=jd+1 where uid='{$uid}' ");

      $a = $DB->query("select * from qingka_wangke_order where user='$username' and uid='$uid' order by oid desc ");
      if ($a) {
        while ($row = $DB->fetch($a)) {
          $data[] = array(
            'id' => $row['oid'],
            'ptname' => $row['ptname'],
            'school' => $row['school'],
            'name' => $row['name'],
            'user' => $row['user'],
            'kcname' => $row['kcname'],
            'addtime' => $row['addtime'],
            'courseStartTime' => $row['courseStartTime'],
            'courseEndTime' => $row['courseEndTime'],
            'examStartTime' => $row['examStartTime'],
            'examEndTime' => $row['examEndTime'],
            'status' => $row['status'],
            'process' => $row['process'],
            'remarks' => $row['remarks']
          );
        }
        $data = array('code' => 1, 'data' => $data);
        exit(json_encode($data));
      } else {
        $data = array('code' => -1, 'msg' => "未查到该账号的下单信息","data"=>[]);
        exit(json_encode($data));
      }
      break;
    case 'chadan2':
      $username = trim(strip_tags(daddslashes($_POST['username'])));
      $t = trim(strip_tags(daddslashes($_POST['t'])));
      
      
      if (empty($username)) {
        jsonReturn(-1,"参数不完整");
      }
      $DB->query("update qingka_wangke_user set jd=jd+1 where uid='{$uid}' ");

      $uid = $t ? $t : $conf["chadan_default"];
      if ($t) {
        $sql = "and uid = '{$uid}'";
      } else {
        $sql = '';
      }

      $a = $DB->query("select * from qingka_wangke_order where user='$username' {$sql}  order by oid desc ");
      if ($a) {
        while ($row = $DB->fetch($a)) {
          $data[] = array(
            'id' => $row['oid'],
            'ptname' => $row['ptname'],
            'school' => $row['school'],
            'user' => $row['user'],
            'kcname' => $row['kcname'],
            'status' => $row['status'],
            'process' => $row['process'],
            'remarks' => $row['remarks'],
            'addtime' => $row['addtime'],
            'bsnum' => $row['bsnum'],
          );
        }
        $data = array('code' => 1, 'data' => $data);
        
        exit(json_encode($data));
      } else {
        $data = array('code' => -1, 'msg' => "未查到该账号的下单信息","data"=>[]);
        exit(json_encode($data));
      }
      break;
    case 'budan':
        
      $oid = daddslashes($_POST['id']);


      if (parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) !== $_SERVER['HTTP_HOST']) {

        if (empty($oid) || empty($uid) || empty($key)) {
          jsonReturn(-1,"参数不完整");
        }

      }
      $DB->query("update qingka_wangke_user set bs=bs+1 where uid='{$uid}' ");

      $b = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' limit 1 ");
      if ($b['bsnum'] >= $conf["api_bs"] && !empty($conf["api_bs"]) ) {
          jsonReturn(-1,"该订单补刷已超过".$conf["api_bs"]."次不支持再次补刷！");
      }

      $c = budanWk($oid);
      if ($c['code'] == 1) {
        if ($b['hid'] == '10') {
          $DB->query("update qingka_wangke_order set status='待处理',`process`='',`remarks`='',`bsnum`=bsnum+1 where oid='{$oid}' ");
          jsonReturn(1, "成功加入补刷线程，排队补刷中");
        }
        if ($b['hid'] == '11' || $b['hid'] == '12') {
          $DB->query("update qingka_wangke_order set dockstatus='0',status='待重刷',`process`='',`remarks`='',`bsnum`=bsnum+1 where oid='{$oid}' ");
          jsonReturn(1, "补刷成功，等待重新上号！");
        }
        if ($b['status'] == "已退款") {
          jsonReturn(1, "订单已退款无法补刷！");
        } else {
          $DB->query("update qingka_wangke_order set status='补刷中',`bsnum`=bsnum+1 where oid='{$oid}' ");
          jsonReturn(1, $c['msg']);
        }
      } else {
        jsonReturn(-12, $c['msg']);
      }

      break;
    case 'getclass':
        
      $uid = trim(strip_tags(daddslashes($_POST['uid'])));
      $key = trim(strip_tags(daddslashes($_POST['key'])));
      $token = trim(strip_tags(daddslashes($_POST['token'])));
      if(empty($uid)){
        jsonReturn(-1, 'UID不能为空');
      }
      $row = $DB->get_row("select * from qingka_wangke_user where uid=$uid limit 1");
      if(empty($row)){
          jsonReturn(0,"UID不存在");
      }
      
        if($row["key"] != $key){
            jsonReturn(0, 'Key错误或未开通');
        }
      
      $result = $DB->query("select * from qingka_wangke_class where status=1 ");
      $data = [];
      while ($row = $DB->fetch($result)) {
        $data[] = array(
          'cid' => $row['cid'],
          'name' => $row['name'],
          'price' => $row['price'],
          'status' => $row['status'],
          'sort' => $row['sort'],
          'content' => $row['content'],
          'fenlei' => $row['fenlei'],
        );
      }
      $data = array('code' => 1, 'data' => $data,"count"=>count($result));
      exit(json_encode($data));
      break;
  }
}
