<?php
$mod = 'blank';
$title = '对接文档';
require_once('head.php');
$fl = $DB->count("select addprice from qingka_wangke_user where uid='{$userrow['uid']}'");
?>


<script src="assets/js/aes.js"></script>

<div class="layui-padding-1" id="loglist" style="display:none;">
    <div class="layui-panel">
         <div class="layui-card-header">
             API调用状态
             &nbsp;<span class="layui-font-12 layui-font-green"><?= empty($userrow["key"])?'未开通KEY':'KEY：'.$userrow["key"] ?></span>
         </div>
         <div class="layui-card-body">
             <?php if(empty($conf['settings'])){ ?>
                <span class="layui-font-12 layui-font-red">
                    管理员已禁止API调用 
                </span>
             <?php }else{ ?>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span class="layui-font-12 layui-font-<?= ($userrow["ck"] - $userrow["xd"] > $conf["api_ck_threshold"])?"red":"blue" ?>">
                         <?= ($userrow["ck"] - $userrow["xd"] > $conf["api_ck_threshold"])?"已被禁用API":"正常" ?></span>
                      &nbsp;&nbsp;查课：<?= $userrow["ck"] ?>次&nbsp;&nbsp;下单：<?= $userrow["xd"] ?>次 
                    </div>
                    <div class="layui-font-12 layui-font-green">差值限制：<?= $conf["api_ck_threshold"] ?></div>
                </div>
             <?php } ?>
              <hr >
              <span class="layui-font-12">
                  单次查课扣费：<?= $conf["api_ckkf"] ?>&nbsp;|&nbsp;查课余额限制：<?= $conf["api_ck"] ?>&nbsp;|&nbsp;下单余额限制：<?= $conf["api_xd"] ?>&nbsp;&nbsp;
              </span>
         </div>
    </div>
    <button type="button" class="layui-btn layui-bg-blue layui-btn-radius" @click="kcIDT_open" style="width:100%;margin:10px 0 10px;">
        <i class="layui-icon layui-icon-search"></i> 查看平台对接ID
    </button>
    
    <div class="layui-panel">

        <div class="layui-tab layui-tab-brief layui-padding-3" lay-filter="test-hash">
            <ul class="layui-tab-title" style="margin-bottom:15px;">
                <li class="layui-this" lay-id="2">下单API</li>
                <li lay-id="4">查课API</li>
                <li lay-id="1">进度API</li>
                <li lay-id="3">补刷API</li>
                <li lay-id="5">老29对接代码</li>
                <!--<li lay-id="6">我的信息</li>-->
            </ul>
            <div class="layui-tab-content" style="padding: 0;">
                
                <div class="layui-tab-item layui-show">
                    <div class="layui-panel layui-padding-3">
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    API地址：
                                </b>
                            </p>
                            <p class="layui-font-blue" style="padding-left:10px;">
                                <b>
                                    https://<? echo ($_SERVER['SERVER_NAME']); ?>/api.php?act=add
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    返回格式：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">
                                <b>
                                    JSON
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    请求方式：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">
                                <b>
                                    POST
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    POST参数说明：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">

                            <table class="layui-table" lay-even>
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width:100px">名称</th>
                                        <th>
                                            类型<br>
                                        </th>
                                        <th class="text-center" style="width:100px">
                                            说明<br>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" scope="row">uid</td>
                                        <td class="text-center"><code>必传</code></td>
                                        <td><code>登录验证</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" scope="row">key</td>
                                        <td class="text-center"><code>必传</code></td>
                                        <td><code>登录验证</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" scope="row">platform</td>
                                        <td class="text-center"><code>必传</code></td>
                                        <td><code>平台ID</code></td>
                                    </tr>

                                    <tr>
                                        <th class="text-center" scope="row">school</th>
                                        <td><code>必传</code></td>
                                        <td class="text-center"><code>学校全称</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" scope="row">user</td>
                                        <td class="text-center"><code>必传</code></td>
                                        <td><code>学生账号</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" scope="row">pass</td>
                                        <td class="text-center"><code>必传</code></td>
                                        <td><code>账号密码</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" scope="row">kcname</td>
                                        <td class="text-center"><code>必传</code></td>
                                        <td><code>课程名字</code></td>
                                    </tr>
                                    <tr>
                                        <th class="text-center" scope="row">kcid</th>
                                        <td class="text-center"><code>必传</code></td>
                                        <td><code>课程ID</code></td>
                                    </tr>
                                </tbody>
                            </table>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="layui-tab-item">
                    <div class="layui-panel layui-padding-3">
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    API地址：
                                </b>
                            </p>
                            <p class="layui-font-blue" style="padding-left:10px;">
                                <b>
                                    https://<? echo ($_SERVER['SERVER_NAME']); ?>/api.php?act=get
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    返回格式：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">
                                <b>
                                    JSON
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    请求方式：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">
                                <b>
                                    POST
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <table class="layui-table" lay-even>
                        <thead>
                            <tr>
                                <th class="text-center" style="width:100px">名称</th>
                                <th class="text-center" style="width:100px">
                                    类型<br>
                                </th>
                                <th>
                                    说明<br>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <tr>
                                <td class="text-center" scope="row">uid</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>登录验证</code></td>
                            </tr>
                            <tr>
                                <td class="text-center" scope="row">key</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>登录验证</code></td>
                            </tr>
                            <tr>
                                <td class="text-center" scope="row">platform</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>平台ID</code></td>
                            </tr>

                            <tr>
                                <td class="text-center" scope="row">user</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>学生账号</code></td>
                            </tr>
                            <tr>
                                <td class="text-center" scope="row">pass</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>学生密码</code></td>
                            </tr>
                            <tr>
                                <td class="text-center" scope="row">school</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>学生学校</code></td>
                            </tr>
                            </tr>
                        </tbody>
                    </table>
                        </div>
                        
                    </div>
                </div>
                
                <div class="layui-tab-item">
                    <div class="layui-panel layui-padding-3">
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    API地址：
                                </b>
                            </p>
                            <p class="layui-font-blue" style="padding-left:10px;">
                                <b>
                                    https://<? echo ($_SERVER['SERVER_NAME']); ?>/api.php?act=chadan
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    返回格式：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">
                                <b>
                                    JSON
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    请求方式：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">
                                <b>
                                    POST
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <table class="layui-table" lay-even>
                        <thead>
                            <tr>
                                <th class="text-center" style="width:100px">名称</th>
                                <th class="text-center" style="width:100px">
                                    类型<br>
                                </th>
                                <th>
                                    说明<br>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" scope="row">uid</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>登录验证</code></td>
                            </tr>
                            <tr>
                                <td class="text-center" scope="row">key</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>登录验证</code></td>
                            </tr>
                            <tr>
                                <td class="text-center" scope="row">username</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>下单账号</code></td>
                            </tr>
                        </tbody>
                    </table>
                        </div>
                        
                    </div>
                </div>
                
                <div class="layui-tab-item">
                    <div class="layui-panel layui-padding-3">
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    API地址：
                                </b>
                            </p>
                            <p class="layui-font-blue" style="padding-left:10px;">
                                <b>
                                    https://<? echo ($_SERVER['SERVER_NAME']); ?>/api.php?act=budan
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    返回格式：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">
                                <b>
                                    JSON
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <p class="" style="margin-bottom:5px;">
                                <b>
                                    请求方式：
                                </b>
                            </p>
                            <p class="layui-font-orange" style="padding-left:10px;">
                                <b>
                                    POST
                                </b>
                            </p>
                        </div>
                        <div style="margin-bottom:10px;">
                            <table class="layui-table" lay-even>
                        <thead>
                            <tr>
                                <th class="text-center" style="width:100px">名称</th>
                                <th class="text-center" style="width:100px">
                                    类型<br>
                                </th>
                                <th>
                                    说明<br>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" scope="row">uid</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>登录验证</code></td>
                            </tr>
                            <tr>
                                <td class="text-center" scope="row">key</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>登录验证</code></td>
                            </tr>
                            <tr>
                                <td class="text-center" scope="row">id</td>
                                <td class="text-center"><code>必传</code></td>
                                <td><code>订单YID</code></td>
                            </tr>
                        </tbody>
                    </table>
                        </div>
                        
                    </div>
                </div>
                
                <div class="layui-tab-item" >
                    
                    <div style="margin-bottom: 5px;">
                        <pre class="layui-code code-demo" lay-options="{text:{code:'平台标识'},header:true,}">
"courserX" => "courserX", 
                        </pre>
                    </div>
                    
                    <div style="margin-bottom: 5px;">
                        <pre class="layui-code code-demo" lay-options="{text:{code:'查课接口'},header:true,}">
if ($type == "courserX") {
    $data = array("uid" => $a["user"], "key" => $a["pass"], "school" => $school, "user" => $user, "pass" => $pass, "platform" => $noun, "kcid" => $kcid);
    $dx_rl = $a["url"];
    $dx_url = "$dx_rl/api.php?act=get";
    $result = get_url($dx_url, $data);
    $result = json_decode($result, true);
    return $result;
}
                        </pre>
                    </div>
                    
                    <div style="margin-bottom: 5px;">
                        <pre class="layui-code code-demo" lay-options="{text:{code:'下单接口'},header:true,}">
else if ($type == "courserX") {
    $data = array("uid" => $a["user"], "key" => $a["pass"], "platform" => $noun, "school" => $school, "user" => $user, "pass" => $pass, "kcname" => $kcname, "kcid" => $kcid);
    $dx_rl = $a["url"];
    $dx_url = "$dx_rl/api.php?act=add";
    $result = get_url($dx_url, $data);
    $result = json_decode($result, true);
    if ($result["code"] == "0") {
        $b = array("code" => 1, "msg" => "下单成功");
    } else {
        $b = array("code" => -1, "msg" => $result["msg"]);
    }
    return $b;
} 
                        </pre>
                    </div>
                    
                    <div style="margin-bottom: 5px;">
                        <pre class="layui-code code-demo" lay-options="{text:{code:'进度接口'},header:true,}">
else if ($type == "courserX") {
    $dx_rl = $a["url"];
    $dx_url = "$dx_rl/api.php?act=chadan";
    $data = array("uid" => $a["user"], "key" => $a["pass"],"username" => $user);
    $result = get_url($dx_url,$data);
    $result = json_decode($result, true);
    if ($result["code"] == "1") {
    foreach ($result["data"] as $res) {
        $yid = $res["id"];
        $kcname = $res["kcname"];
        $status = $res["status"];
        $process = $res["process"];
        $remarks = $res["remarks"];
        $kcks = $res["courseStartTime"];
        $kcjs = $res["courseEndTime"];
        $ksks = $res["examStartTime"];
        $ksjs = $res["examEndTime"];
        $b[] = array("code" => 1, "msg" => "查询成功", "yid" => $yid, "kcname" => $kcname, "user" => $user, "pass" => $pass, "ksks" => $ksks, "ksjs" => $ksjs, "status_text" => $status, "process" => $process, "remarks" => $remarks);
        }
    } else {
    $b[] = array("code" => -1, "msg" => $result["msg"]);
    }
    return $b;
}
                        </pre>
                    </div>
                    
                    <div style="margin-bottom: 5px;">
                        <pre class="layui-code code-demo" lay-options="{text:{code:'补刷接口'},header:true,}">
if ($type == "courserX") {
    $data = array("uid" => $a["user"], "key" => $a["pass"], "id" => $yid);
    $dx_rl = $a["url"];
    $dx_url = "$dx_rl/api.php?act=budan";
    $result = get_url($dx_url, $data);
    $result = json_decode($result, true);
    return $result;
}
                        </pre>
                    </div>
                    
                </div>
                
            </div>
        </div>

        <div id="kcIDT" style="display:none;">
            <table class="layui-table" lay-size="sm" style="width: 100%;min-width: 100%;
						margin:0;">

                <thead>
                    <tr>
                        <th class="center" style="width:40px;">对接ID</th>
                        <th class="center" style="width:40px;">分类ID</th>
                        <th>平台</th>
                        <th>说明</th>
                        <th class="center" style="width:60px;">我的价格
                            <!--（<?php echo $userrow['addprice'] ?>）-->
                        </th>
                        <th v-for="item in row" :key="item.id">{{item.rate}}</th>
                        <!--<th>排序</th>-->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $a = $DB->query("select * from qingka_wangke_class where status=1 order by sort ");
                    while ($rs = $DB->fetch($a)) {
                        echo "<tr><td class='center'>" . $rs['cid'] . "</td>
                                    <td class='center'>" . $rs['fenlei'] . "</td>
	                     	 	  	<td >" . $rs['name'] . "</td>
	                     	 	  	<td v-html='". json_encode($rs['content']) ."'></td>
	                     	 	  	<td  v-for='item in row' :key='item.id'>{{(" . json_encode($rs['price'])   . " * Number(item.rate)).toFixed(2)}}</td>
	                     	 	  	<td class='center'>" . ($rs['price'] * $userrow['addprice']) . "</td>
	                     	 	  	
	                     	 	  	</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
    
</div>

<?php include($root.'/index/components/footer.php'); ?>

<script>
    //注意：选项卡 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function() {
        var element = layui.element;

        //…
    });
</script>

<script>
    const app = Vue.createApp({
        data(){
            return{
                row: []
            }
        },
        mounted() {
            const _this = this;
            
            let loadIndex = layer.load(0);
            $("#loglist").ready(()=>{
                layer.close(loadIndex);
                $("#loglist").show();
                _this.get(1);
                
                layui.use(()=>{
                     setTimeout(()=>{
                         layui.code({
                            elem: '.code-demo'
                         });
                     },0)
                })
                
            })
            
            layui.use(function() {
                var util = layui.util;
                // 自定义固定条
                util.fixbar({
                    margin: 100
                })
            })
            
        },
        methods: {
            get: function(page) {

            },
            kcIDT_open: function() {
                layui.use(function() {
                    layer.open({
                        title: '平台对接ID <span class="layui-font-12 layui-font-red">使用Ctrl+F搜索</span>',
                        type: 1,
                        scrollbar: false,
                        offset: 'r',
                        anim: 'slideLeft', // 从右往左
                        area: ['90%','100%'],
                        shade: 0.1,
                        shadeClose: true,
                        id: 'kcIDT',
                        content: $("#kcIDT")
                    });
                })
            }
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#loglist');
    // -----------------------------
</script>
<script>
    layui.use(function() {

        var element = layui.element;

        // hash 地址定位
        var hashName = 'tabid'; // hash 名称
        var layid = location.hash.replace(new RegExp('^#' + hashName + '='), ''); // 获取 lay-id 值

        // 初始切换
        element.tabChange('test-hash', layid);
        // 切换事件
        element.on('tab(test-hash)', function(obj) {
            location.hash = hashName + '=' + this.getAttribute('lay-id');
        });

    })
</script>