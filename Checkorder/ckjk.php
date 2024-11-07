<?php
function getMillisecond()
{

    list($t1, $t2) = explode(' ', microtime());

    return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

// 查课接口设置
function getWk($c, $userinfo2)
{
    // $c 商品数据
    global $DB;
    global $wk;
    // 货源数据
    $a = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$c['queryplat']}' ");

    if (count($userinfo2) > 2) {
        $school = trim($userinfo2[0]);
        $user = trim($userinfo2[1]);
        $pass = trim($userinfo2[2]);
    } else {

        $school = "自动识别";
        $user = trim($userinfo2[0]);
        $pass = trim($userinfo2[1]);
    }
    $noun = $c['getnoun'];
    $name = $c['name'];

    $ckjk = $a["ckjk"];

    if ($ckjk) {
        $ace_url = $a["url"] . $ckjk;

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


        $ckcs = $a["ckcs"];
        $data = [];
        eval ("\$data = [$ckcs];");
        $token = $a["token"];
        $cookie = $a['cookie'];
        $header = array(
            'Content-type:application/x-www-form-urlencoded',
            "token: " . $token,
            "cookie:" . $cookie,
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39 toc"
        );
        $POSTYPE = $a["ck_post"];

        $result = get_url($ace_url, $POSTYPE == '1' ? $data : false, $cookie, $header);

        if (empty($result)) {
            jsonReturn(404, "404 网络请求失败");
        }

        $result = json_decode($result, true);
        $data = [
            "code" => 1,
            "data" => [],
            "msg" => "查询成功",
        ];

        if (in_array($result["code"], explode(',', $a["ck_okcode"]))) {
            $data["data"] = $result[$a["ck_datakey"]];
            if (count($result[$a["ck_datakey"]]) == 0) {
                $data["code"] = -1;
                $data["msg"] = empty($result["msg"]) ? "未查到课程" : $result["msg"];
                return $data;
            }
            foreach ($result[$a["ck_datakey"]] as $key => $row) {
                $data["data"][$key]["name"] = $row[$a["ck_kcnamekey"]];
                $data["data"][$key]["id"] = $row[$a["ck_kcidkey"]];
            }
        } else {
            $data["code"] = -1;
            $data["msg"] = empty($result["msg"]) ? "查询失败" : $result["msg"];
        }
        return $data;

    } else {
        $data = ["code" => -1, "msg" => "缺少参数，请检查货源配置"];
        return $data;
    }
}
