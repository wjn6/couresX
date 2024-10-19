<?php

function addWk($oid)
{
  global $DB;
  global $wk;
  $b = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
  $cid = $b["cid"];
  $school = $b["school"];
  $user = $b["user"];
  $pass = $b["pass"];
  $kcid = $b["kcid"];
  $kcname = $b["kcname"];
  $miaoshua = $b["miaoshua"];
  $noun = $b["noun"];
  
  $hid = $b["hid"];
  $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$hid}' ");

  $xdjk = $a["xdjk"];
  if ($xdjk) {
    $xdcs = $a["xdcs"];
    $data = [];
    eval ("\$data = [$xdcs];");
    
    if(empty($data["school"])){
        $data["school"] = "自动识别";
    }
    if (empty($data['kcid'])) {
      $data['kcid'] = $kcid;
    }
    if (empty($data['miaoshua'])) {
      $data['miaoshua'] = $miaoshua;
    }
    
    $token = $a["token"];
    $cookie = $a['cookie'];
    $header = array(
      'Content-type:application/x-www-form-urlencoded',
      "token: " . $token,
      "cookie:" . $cookie,
      "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 toc"
    );
    $ace_rl = $a["url"];
    $ace_url = $ace_rl . $xdjk;
    
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
    
    $POSTYPE = $a["xd_post"];
    $result = get_url($ace_url, $POSTYPE=='1'?$data:false,$cookie,$header,120);

    if (empty($result)) {
        jsonReturn(404,"404 网络请求失败");
    }

    $result = json_decode($result, true);
    $data = [
        "code" => 1,
        "msg" => "对接下单成功",
        "id" => "",
    ];
    
    if( $result["code"] == $a["xd_okcode"] ){
        $data["msg"] = $result["msg"];
        $data["id"] = $result[$a["xd_yidkey"]];
        $data["yid"] = $result[$a["xd_yidkey"]];
    }else{
        $data["code"] = -1;
        $data["msg"] = empty($result["msg"])?"对接下单失败":$result["msg"];
    }
    
    return $data;
  } else {
    $data = ["code" => -1, "msg" => "缺少参数，请检查货源配置",];
    return $data;
  }
}
