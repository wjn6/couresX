<?php

function processCx($oid)
{
  global $DB;
  $b = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' ");
  $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$b['hid']}' ");
  $type = $a["pt"];
  $cookie = $a["cookie"];
  $token = $a["token"];
  $ip = $a["ip"];
  $user = $b["user"];
  $pass = $b["pass"];
  $kcname = $b["kcname"];
  $school = $b["school"];
  $noun = $b["noun"];
  $kcid = $b["kcid"];
  $yid = $b["yid"];

  $jdjk = $a["jdjk"];

  $data = [];
  if ($jdjk) {
    $miaoshua = $b["miaoshua"];
    $jdcs = $a["jdcs"];

    $data = [];
    eval ("\$data = [$jdcs];");
    $token = $a["token"];
    $cookie = $a['cookie'];
    $header = [
      'Content-type:application/x-www-form-urlencoded',
      "token: " . $token,
      "cookie:" . $cookie,
      "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 toc"
    ];
    $ace_rl = $a["url"];
    $ace_url = $ace_rl . $jdjk;
    
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
    
    $POSTYPE = $a["jd_post"];
    $result = get_url($ace_url, $POSTYPE==1?$data:false,$cookie,$header,120);
    
    $result = json_decode($result, true);
    if (empty($result)) {
        jsonReturn(404,"404 网络请求失败");
    }
    
    $data = [
        "code" => 1,
        "data" => [],
        "msg" => "同步成功",
        "msg2" => $result["msg"],
    ];
        
    if( $result["code"] == $a["jd_okcode"] ){
        $data["data"] = $result[$a["jd_datakey"]];
        foreach ($result[$a["jd_datakey"]] as $key => $row){
            $data["data"][$key]["user"] = $user;
            $data["data"][$key]["pass"] = $pass;
            $data["data"][$key]["id"] = $row["id"];
            $data["data"][$key]["yid"] = $row["id"];
            $data["data"][$key]["kcname"] = $row[$a["jd_datakey_kcname"]];
            $data["data"][$key]["status_text"] = $row[$a["jd_datakey_status"]];
            $data["data"][$key]["process"] = $row[$a["jd_datakey_process"]];
            $data["data"][$key]["remarks"] = $row[$a["jd_datakey_remarks"]];
            $data["data"][$key]["kcks"] = $row[$a["jd_datakey_kcks"]];
            $data["data"][$key]["kcjs"] = $row[$a["jd_datakey_kcjs"]];
            $data["data"][$key]["ksks"] = $row[$a["jd_datakey_ksks"]];
            $data["data"][$key]["ksjs"] = $row[$a["jd_datakey_ksjs"]];
        }
    }else{
        $data["data"]["code"] = -1;
        $data["data"]["msg"] = empty($result["msg"])?"同步失败":$result["msg"];
    }
    
    return $data["data"];
    
  } else {
    $data["data"] = ["code" => -1, "msg" => "接口异常，请联系管理员"];
    return $data["data"];
  }
}
