<?php
header('Content-Type: text/html; charset=UTF-8');
$root = $_SERVER['DOCUMENT_ROOT'];
include ($root . '/confing/common.php');

// 安全权限控制
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($userrow) ) {
    exit("你想干啥？");
}

$date = DateTime::createFromFormat('U.u', microtime(true))->setTimezone(new DateTimeZone('Asia/Shanghai'))->format("Y-m-d H:i:s--u");

function is_admin()
{
    global $userrow;
    if (empty($userrow["uid"]) || (string) $userrow["uid"] != '1') {
        http_response_code(403);
        header("Content-Type: text/plain; charset=utf-8");
        exit("403 Forbidden - Permission Denied");
    }
}

$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;

is_admin();
switch ($act) {
    case 'huoyuanGet':
        $huoyuanDataReturn = $DB->query("select * from qingka_wangke_huoyuan order by hid ");
        
        if(empty($huoyuanDataReturn)){
            jsonReturn(-1,"获取货源列表失败");
        }
        
        $count = $DB->count("select count(*) from qingka_wangke_huoyuan ");
        
        $huoyuan_data = [];// 分类数据
        while ($row = $DB->fetch($huoyuanDataReturn)) {
            $huoyuan_data[]=$row;
        }
        $data = [];
        foreach ($huoyuan_data as $key => $value) {
            $data[$key] = [
                "hid" => $value["hid"],
                "name" => $value["name"],
                "url" => $value["url"],
                "status" => $value["status"],
                "status2" => $value["status"],
                "uid" => $value["uid"],
                "token" => $value["token"],
            ];
        }
        exit(json_encode(["code"=>1,"data"=>$data,"count"=>(float)$count]));
        break;
    case 'apiClassGet':
        $hid = trim(strip_tags(daddslashes($_POST['hid'])));
        $postType = empty(trim(strip_tags(daddslashes($_POST['postType']))))?0:1;
        $path = trim(strip_tags(daddslashes($_POST['path'])));
        $dataT = empty(trim(strip_tags(daddslashes($_POST['dataT']))))?"data":trim(strip_tags(daddslashes($_POST['dataT'])));
        $yesCode = empty(trim(strip_tags(daddslashes($_POST['yesCode']))))?"1":trim(strip_tags(daddslashes($_POST['yesCode'])));
        
        if(empty($hid)){
            jsonReturn(-1,'参数不足');
        }
        $huoyuanResult = $DB->get_row("select * from qingka_wangke_huoyuan where hid='{$hid}' limit 1");
        if(empty($huoyuanResult)){
            jsonReturn(-1,'货源不存在');
        }
        
        if(empty($huoyuanResult["url"])){
            jsonReturn(-1,'该接口未配置URL');
        }
        
        $token = $huoyuanResult["token"];
        $cookie = $huoyuanResult['cookie'];
        $header = [
          'Content-type:application/x-www-form-urlencoded',
          "token: " . $token,
          "cookie:" . $cookie,
          "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36 Edg/101.0.1210.39"
        ];
        
        $ace_rl = $huoyuanResult["url"];// 接口url
        $ace_url = $ace_rl . $path;
        
        $data = [
            "uid"=> $huoyuanResult["user"],
            "key"=> $huoyuanResult["pass"],
            "token"=> $huoyuanResult["token"],
        ];
        
        $apiClassGetResult = get_url($ace_url,$postType?$data:false,$cookie,$header,120);
        
        $apiClassGetResult = json_decode($apiClassGetResult, true);// 结果
        
        if(empty($apiClassGetResult) || $apiClassGetResult["code"] != $yesCode){
            jsonReturn(-1,empty($apiClassGetResult["msg"])?"异常":$apiClassGetResult["msg"]);
        }
        
        if($apiClassGetResult[$dataT] === null ){
            jsonReturn(-1,"接口异常");
        }
        if(!count($apiClassGetResult["data"])){
            jsonReturn(-1,'该接口无商品可对接！');
        }
        
        $data = $apiClassGetResult[$dataT];
        
        
        foreach ($data as $key => $value) {
            
            $data[$key]["status2"] = "未对接";
            $data[$key]["status2_cid"] = [];
            
            $noun = [];
            $nounResult = $DB->query("select cid from qingka_wangke_class where noun='{$value['cid']}' ");
            while ($row = $DB->fetch($nounResult)) {
                $noun[] = $row;
            }
            if(empty(count($noun))){
            }else{
                $data[$key]["status2"] = "已对接 | ".count($noun)." 个";
                $data[$key]["status2_cid"] = array_map(function($item) {
                    return $item['cid'];
                }, $noun);
            }
            
        }
        
        exit(json_encode(["code"=>1,"data"=> $data,"count"=>count($apiClassGetResult[$dataT])]));
        break;
    case 'fenleiGet':
        $fenleiDataReturn = $DB->query("select * from qingka_wangke_fenlei order by id ");
        $count = $DB->count("select count(*) from qingka_wangke_fenlei ");
        
        $fenlei_data = [];// 分类数据
        while ($row = $DB->fetch($fenleiDataReturn)) {
            $fenlei_data[]=$row;
        }
        exit(json_encode(["code"=>1,"data"=>$fenlei_data,"count"=>$count]));
        break;
    case 'save':
        $list = json_decode($_POST['list'],true);
        $hid = trim(strip_tags(daddslashes($_POST['hid'])));
        $fenlei = trim(strip_tags(daddslashes($_POST['fenlei'])));
        $add = empty(trim(strip_tags(daddslashes($_POST['add']))))?100:trim(strip_tags(daddslashes($_POST['add'])));
        $yunsuan = trim(strip_tags(daddslashes($_POST['yunsuan']))) == '*'?'*':'+';
        
        // 获取数量
        $listCount = count($list);
        if(empty($hid)){
            jsonReturn(-1,"未指定货源");
        }
        if(empty($fenlei)){
            jsonReturn(-1,"未指定分类");
        }
        if(empty($listCount)){
            jsonReturn(-1,"参数不足或未选择需要对接的商品");
        }
        
        $max_sort = $DB->get_row("select cid,sort from qingka_wangke_class order by sort desc ")["sort"];
        $errorNum = 0;
        foreach ($list as $key => $value) {
            $price = round($value['price'] * ($add/100),3);
            $now_sort = $max_sort+$key +1;
            $result = $DB->query("insert into qingka_wangke_class (sort,name,getnoun,noun,price,queryplat,docking,content,addtime,status,fenlei,yunsuan) values ('{$now_sort}','{$value['name']}','{$value['cid']}','{$value['cid']}','{$price}','{$hid}','{$hid}','{$value['content']}','{$date}','1','{$fenlei}','{$yunsuan}')");
            if(empty($result)){
                $errorNum = $errorNum + 1;
            }
        }
        
        exit(json_encode(["code"=>1,"data"=>$list,"okNum"=>$listCount - $errorNum,"errorNum"=> $errorNum]));
        
        break;
    default:
        // code...
        exit("你想干啥？");
        break;
}

?>