<?php
function budanWk($oid)
{
    global $DB;
    $b = $DB->get_row("select * from qingka_wangke_order where oid='{$oid}' limit 1 ");
    $hid = $b["hid"];
    $yid = $b["yid"];
    $user = $b["user"];
    $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$hid}' limit 1 ");
    $type = $a["pt"];
    $cookie = $a["cookie"];
    $token = $a["token"];
    $ip = $a["ip"];
    $cid = $b["cid"];
    $school = $b["school"];
    $user = $b["user"];
    $pass = $b["pass"];
    $kcid = $b["kcid"];
    $kcname = $b["kcname"];
    $noun = $b["noun"];
    $miaoshua = $b["miaoshua"];

    $bsjk = $a["bsjk"];
    if ($bsjk) {
        $miaoshua = $b["miaoshua"];
        $bscs = $a["bscs"];

        $data = [];
        eval ("\$data = [$bscs];");
        $token = $a["token"];
        $cookie = $a['cookie'];
        $header = [
            'Content-type:application/x-www-form-urlencoded',
            "token: " . $token,
            "cookie:" . $cookie,
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 toc"
        ];

        $ace_rl = $a["url"];
        $ace_url = $ace_rl . $bsjk;

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

        $POSTYPE = $a["bs_post"];

        $result = get_url($ace_url, $POSTYPE == '1' ? $data : false, $cookie, $header);
        
        if (empty($result)) {
            jsonReturn(404,"404 网络请求失败");
        }

        $result = is_array($result) ? $result : json_decode($result, true);
        $data = [
            "code" => 1,
            "msg" => "补刷成功",
            "id" => "",
        ];
        
        if( $result["code"] == $a["bs_okcode"] ){
            $data["msg"] = empty($result["msg"])?"补刷成功":$result["msg"];
        }else{
            $data["code"] = -1;
            $data["msg"] = empty($result["msg"])?"补刷失败":$result["msg"];
        }
        
        return $data;
        
    } else {
        $data = ["code" => -1, "msg" => "接口异常，请联系管理员"];
        return $data;
    }

}
