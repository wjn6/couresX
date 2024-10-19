<?php
header('Content-Type: text/html; charset=UTF-8');
$root = $_SERVER['DOCUMENT_ROOT'];
include ($root . '/confing/common.php');


function is_admin()
{
    global $userrow;
    if (empty($userrow["uid"]) || (string) $userrow["uid"] !== '1') {
        http_response_code(403);
        header("Content-Type: text/plain; charset=utf-8");
        exit("403 Forbidden - Permission Denied");
    }
}
is_admin();
class bt_api {
	private $BT_KEY;  //接口密钥
  	private $BT_PANEL;	   //面板地址
  	//如果希望多台面板，可以在实例化对象时，将面板地址与密钥传入
	public function __construct($bt_key = null,$bt_panel){
		 $this->BT_PANEL = $bt_panel;
		 $this->BT_KEY = $bt_key;
	}
	
	
	public function get_token(){
		//拼接URL地址
		$url = $this->BT_PANEL.'/config?action=get_token';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	
	// 获取定时任务列表
	public function GetCrontab(){
		//拼接URL地址
		$url = $this->BT_PANEL.'/crontab?action=GetCrontab';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['search'] = '';
		$p_data['type_id'] = '';
		$p_data['order_param'] = '';
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 添加定时任务
	public function AddCrontab($data){
		//拼接URL地址
		$url = $this->BT_PANEL.'/crontab?action=AddCrontab';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
        $p_data["name"] = $data["name"];
        $p_data["type"] = $data["type"];
        $p_data["week"] = empty($data["week"])?1:$data["week"];
        $p_data["hour"] = intval($data["hour"]);
        $p_data["minute"] = intval($data["minute"]);
        $p_data["where1"] = $data["where1"];
        $p_data["timeSet"] = 1;
        $p_data["timeType"] = empty($data["timeType"])?"sday":$data["timeType"];
        $p_data["sType"] = $data["sType"];
        $p_data["sBody"] = $data["sBody"];
        $p_data["sName"] = $data["sName"];
        $p_data["backupTo"] = $data["backupTo"];
        $p_data["save"] = $data["save"];
        $p_data["urladdress"] = $data["urladdress"];
        $p_data["save_local"] = $data["save_local"];
        $p_data["notice"] = $data["notice"];
        $p_data["notice_channel"] = $data["notice_channel"];
        $p_data["datab_name"] = $data["datab_name"];
        $p_data["tables_name"] = $data["tables_name"];
        $p_data["keyword"] = $data["keyword"];
        $p_data["flock"] = 1;
        $p_data["user_agent"] = $data["user_agent"];

        foreach ($p_data as $key => $value){
            if($value===null){
                $p_data[$key] = '';
            }
        }
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 修改定时任务
	public function modify_crond($data){
		//拼接URL地址
		$url = $this->BT_PANEL.'/crontab?action=modify_crond';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名

        $p_data["id"] = $data["id"];
        $p_data["name"] = $data["name"];
        $p_data["type"] = $data["type"];
        $p_data["hour"] = intval($data["hour"]);
        $p_data["minute"] = intval($data["minute"]);
        $p_data["where1"] = empty($data["where1"])?30:intval($data["where1"]);
        $p_data["week"] = empty($data["week"])?1:intval($data["week"]);
        $p_data["timeType"] = empty($data["timeType"])?1:intval($data["timeType"]);
        $p_data["timeSet"] =1;
        $p_data["sType"] = $data["sType"]?$data["sType"]:"toUrl";
        $p_data["sBody"] = $data["sBody"];
        $p_data["sName"] = $data["sName"];
        $p_data["backupTo"] = $data["backupTo"];
        $p_data["save"] = $data["save"];
        $p_data["urladdress"] = $data["urladdress"];
        $p_data["save_local"] = 0;
        $p_data["notice"] =0;
        $p_data["notice_channel"] = $data["notice_channel"];
        $p_data["datab_name"] = $data["datab_name"];
        $p_data["tables_name"] = $data["tables_name"];
        $p_data["keyword"] = $data["keyword"];
        $p_data["flock"] = 1;
        $p_data["user_agent"] = $data["user_agent"];
        

        // foreach ($p_data as $key => $value){
        //     if($value===null){
        //         $p_data[$key] = '';
        //     }
        // }
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	
    // 修改定时任务状态
	public function set_cron_status($data){
		//拼接URL地址
		$url = $this->BT_PANEL.'/crontab?action=set_cron_status';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['id'] = $data['id'];
		$p_data['if_stop'] = 'Flase';
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
    // 删除定时任务
	public function DelCrontab($data){
		//拼接URL地址
		$url = $this->BT_PANEL.'/crontab?action=DelCrontab';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['id'] = $data[0];
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
    // 运行定时任务
	public function StartTask($id){
		//拼接URL地址
		$url = $this->BT_PANEL.'/crontab?action=StartTask';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['id'] = $id;
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
    // 获取定时任务日志
	public function GetLogs($id){
		//拼接URL地址
		$url = $this->BT_PANEL.'/crontab?action=GetLogs';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['id'] = $id;
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
    // 清空定时任务日志
	public function DelLogs($id){
		//拼接URL地址
		$url = $this->BT_PANEL.'/crontab?action=DelLogs';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['id'] = $id;
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 获取进程守护管理器是否安装
	public function get_soft_list($query){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=get_soft_list';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['query'] = $query;
		$p_data['p'] = 1;
		$p_data['type'] = -1;
		$p_data['force'] = 0;
		$p_data['force'] = '1';
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 获取进程守护管理器状态
	public function GetServerStatus(){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s=GetServerStatus';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
    // 操作进程守护管理器状态
	public function SetServerStatus($status){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s=SetServerStatus';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['status'] = empty($status)?0:$status;
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 获取进程列表
	public function GetProcessList(){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s=GetProcessList';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 修改进程状态
	public function set_process_status($status,$data){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s='.($status=='1'?"StartProcess":"StopProcess");
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['numprocs'] = $data['numprocs'];
		$p_data['program'] = $data['program'];
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 删除进程
	public function RemoveProcess($program){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s=RemoveProcess';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['program'] =$program;
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 获取进程日志
	public function GetProcessLogs($program,$log_type){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s=GetProjectLog';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['log_type'] =$log_type;
		$p_data['pjname'] =$program;
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
// 		$data = json_decode($result,true);
      	return $result;
	}
	// 清空进程日志
	public function clear_record($program,$log_type){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s=clear_record';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		$p_data['log_type'] =$log_type;
		$p_data['filename'] =$program;
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 添加守护进程
	public function AddProcess($data){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s=AddProcess';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		foreach ($data as $key => $value){
		    $p_data[$key]=$value;
		}
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	// 添加守护进程
	public function UpdateProcess($data){
		//拼接URL地址
		$url = $this->BT_PANEL.'/plugin?action=a&name=supervisor&s=UpdateProcess';
		
		//准备POST数据
		$p_data = $this->GetKeyData();		//取签名
		foreach ($data as $key => $value){
		    $p_data[$key]=$value;
		}
		
		//请求面板接口
		$result = $this->HttpPostCookie($url,$p_data);
		
		//解析JSON数据
		$data = json_decode($result,true);
      	return $data;
	}
	
    // 	构造带有签名的关联数组
  	private function GetKeyData(){
  		$now_time = time();
    	$p_data = array(
    	    'api_sk' => $this -> BT_KEY,
			'request_token'	=>	md5($now_time.''.md5($this->BT_KEY)),
			'request_time'	=>	$now_time
		);
    	return $p_data;    
    }
    private function HttpPostCookie($url, $data,$timeout = 60)
    {
        
    	//定义cookie保存位置
        // $cookie_file='./'.md5($this->BT_PANEL).'.cookie';
        // if(!file_exists($cookie_file)){
        //     $fp = fopen($cookie_file,'w+');
        //     fclose($fp);
        // }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}
//实例化对象
$bt_key = $conf["bt_token"];
$bt_panel = $conf["bt_panel"];
$api = new bt_api($bt_key,$bt_panel);
$act = isset($_GET['act']) ? daddslashes($_GET['act']) : null;
switch ($act) {
    
    case "setBT":
        $token = trim($_POST["token"]);
        $panel = trim($_POST["panel"]);
        if(empty($token)){
            jsonReturn(-1,"Token为空");
        }
        if(empty($token)){
            jsonReturn(-1,"面板地址为空");
        }
        $result1 = $DB->query("update qingka_wangke_config set k='{$token}' where v='bt_token'");
        $result2 = $DB->query("update qingka_wangke_config set `k`='{$panel}' where `v`='bt_panel'");
        if($result2 && $result1){
            exit(json_encode(["code"=>1,"msg"=>"保存成功"]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>"保存失败"]));
        }
        break;
    case 'v':
        $result = $api->get_token();
        
        if(empty($result)){
            exit(json_encode(["code"=>-1,"msg"=>"宝塔API接口未开启或面板地址错误","data"=>$result]));
        }
        if($result["status"] === false){
            exit(json_encode(["code"=>-2,"msg"=>"密钥校验失败","data"=>$result]));
        }
        
        $result1 =$api->get_soft_list("supervisor");
        if(count($result1["list"]["data"]) == 0 ){
        exit(json_encode(["code"=>-3,"msg"=>"进程守护管理器未安装","data"=>["token"=>$result["token"]] ]));
        }
        
        
        exit(json_encode(["code"=>1,"data"=>["token"=>$result["token"]] ]));
        break;
        // 获取定时任务列表
    case "GetCrontab":
        $result = $api->GetCrontab();
        exit(json_encode(["code"=>1,"data"=>$result]));
        break;
    case "AddCrontab":
        if(empty($_POST["urladdress"])){
            exit(json_encode(["code"=>-1,"msg"=>"url不能为空"]));
        }
        $result = $api->AddCrontab($_POST);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"data"=>$result]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "modify_crond":
        if(empty($_POST["urladdress"])){
            exit(json_encode(["code"=>-1,"msg"=>"url不能为空"]));
        }
        $result = $api->modify_crond($_POST);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"data"=>$result["msg"]]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "set_cron_status":
        $result = $api->set_cron_status([ "id"=>$_POST["id"] ]);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"data"=>$result]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "DelCrontab":
        $result = $api->DelCrontab($_POST["id"]);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"data"=>$result]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "GetLogs":
        $result = $api->GetLogs($_POST["id"]);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"data"=>$result["msg"]]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "StartTask":
        $result = $api->StartTask($_POST["id"]);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"data"=>$result["msg"]]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "DelLogs":
        $result = $api->DelLogs($_POST["id"]);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"data"=>$result["msg"]]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "GetServerStatus":
        $result = $api->GetServerStatus();
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"data"=>$result["msg"]]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>'进程守护管理器 | '.$result["msg"],"data"=>$result]));
        }
        break;
    case "SetServerStatus":
        $status = trim($_POST["status"]);
        $result = $api->SetServerStatus($status);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"msg"=>$result["msg"]]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>'进程守护管理器 | '.$result["msg"],"data"=>$result]));
        }
        break;
    case "GetProcessList":
        $result = $api->GetProcessList();
        exit(json_encode(["code"=>1,"data"=>$result]));
        break;
    case "set_process_status":
        $status = trim($_POST["status"]);
        $program = trim($_POST["program"]);
        $numprocs = trim($_POST["numprocs"]);
        $twoRun = trim($_POST["twoRun"]);
        $data=[
            program=>$program,
            numprocs=>$numprocs,
        ];
        if(!empty($twoRun)){
            $result = $api->set_process_status(0,$data);
            sleep(1);
            $result = $api->set_process_status(1,$data);
        }else{
            $result = $api->set_process_status($status,$data);
            sleep(2);
        }
        if($result["status"] === true){
            if(!empty($twoRun)){
                exit(json_encode(["code"=>1,"msg"=>"重启成功"]));
            }else{
                exit(json_encode(["code"=>1,"msg"=>$result["msg"]]));
            }
        }else{
            exit(json_encode(["code"=>-1,"msg"=>'操作失败，请重试',"data"=>$result]));
        }
        break;
    case "RemoveProcess":
        $program = trim($_POST["program"]);
        $result = $api->RemoveProcess($program);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"msg"=>$result["msg"]]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "GetProcessLogs":
        $program =trim($_POST["program"]);
        $log_type =trim($_POST["log_type"]);
        $result = $api->GetProcessLogs($program,$log_type);
        if(!empty($result)){
            exit(json_encode(["code"=>1,"data"=>$result]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>"未获取到日志","data"=>$result]));
        }
        break;
    case "clear_record":
        $program =trim($_POST["program"]);
        $log_type =trim($_POST["log_type"]);
        $result = $api->clear_record($program,$log_type);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"msg"=>$result["msg"]]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>"清理失败","data"=>$result]));
        }
        break;
    case "AddProcess":
        $result = $api->AddProcess($_POST);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"msg"=>$result['msg']]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    case "UpdateProcess":
        $result = $api->UpdateProcess($_POST);
        if($result["status"] === true){
            exit(json_encode(["code"=>1,"msg"=>$result['msg']]));
        }else{
            exit(json_encode(["code"=>-1,"msg"=>$result["msg"],"data"=>$result]));
        }
        break;
    
    default:
        // code...
        break;
}

?>