<?php
$mod = 'blank';
$title = '平台对接';
include_once ('head.php');
$fl = $DB->count("select addprice from qingka_wangke_user where uid='{$userrow['uid']}'");
$ck = $DB->count("SELECT count(id) FROM `qingka_wangke_log` WHERE type='API查课' AND uid='{$userrow['uid']}' ");
$xd = $DB->count("SELECT count(id) FROM `qingka_wangke_log` WHERE type='API添加任务' AND uid='{$userrow['uid']}' ");
$xdbxz = 15;
$xdb = round($xd / $ck, 4) * 100;
?>

<div class="app-content-body " id="dockID" style="display:none;">
    <div class="wrapper-md control">
        <div class="layui-row layui-col-space8 layui-anim layui-anim-upbit">
            <div class="card" style="box-shadow: 3px 3px 8px #d1d9e6, -3px -3px 8px #d1d9e6;border-radius: 7px;">
                
                <div class="layui-panel layui-padding-2">
                    API调用状态
                </div>
                
                <ul class="nav nav-tabs" role="tablist" style="margin: 5px 0;">
                    <li class="active">
                        <a data-toggle="tab" href="#ck">查课接口</a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" href="#xd">下单接口</a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" href="#bs">补刷接口</a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" href="#jd">进度同步</a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" href="#kcid">课程对接ID</a>
                    </li>
                    <li class="nav-item">
                        <a data-toggle="tab" href="#xx">我的信息</a>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="ck">
                        <pre><h5>POST:</h5>https://<? echo ($_SERVER['SERVER_NAME']); ?>/api.php?act=get</pre>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:100px">请求参数</th>
                                    <th>
                                        说明<br>
                                    </th>
                                    <th class="text-center" style="width:100px">
                                        传输类型<br>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                <tr>
                                    <th class="text-center" scope="row">uid</th>
                                    <td><code>登录验证</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">key</th>
                                    <td><code>登录验证</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">platform</th>
                                    <td><code>平台ID</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>

                                <tr>
                                    <th class="text-center" scope="row">user</th>
                                    <td><code>学生账号</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">pass</th>
                                    <td><code>学生密码</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">school</th>
                                    <td><code>学生学校</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="xd">
                        <pre><h5>POST:</h5>https://<? echo ($_SERVER['SERVER_NAME']); ?>/api.php?act=add</pre>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:100px">请求参数</th>
                                    <th>
                                        说明<br>
                                    </th>
                                    <th class="text-center" style="width:100px">
                                        传输类型<br>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="text-center" scope="row">uid</th>
                                    <td><code>登录验证</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">key</th>
                                    <td><code>登录验证</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">platform</th>
                                    <td><code>平台ID</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>

                                <tr>
                                    <th class="text-center" scope="row">user</th>
                                    <td><code>学校全称</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>

                                <tr>
                                    <th class="text-center" scope="row">school</th>
                                    <td><code>学生全称</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">pass</th>
                                    <td><code>账号密码</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">kcname</th>
                                    <td><code>课程名字</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">kcid</th>
                                    <td><code>课程ID</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="bs">
                        <pre><h5>POST:</h5>https://<? echo ($_SERVER['SERVER_NAME']); ?>/api.php?act=budan</pre>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:100px">请求参数</th>
                                    <th>
                                        说明<br>
                                    </th>
                                    <th class="text-center" style="width:100px">
                                        传输类型<br>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="text-center" scope="row">uid</th>
                                    <td><code>登录验证</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">key</th>
                                    <td><code>登录验证</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">id</th>
                                    <td><code>订单账号</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="jd">
                        <pre><h5>POST:</h5>https://<? echo ($_SERVER['SERVER_NAME']); ?>/api.php?act=chadan</pre>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:100px">请求参数</th>
                                    <th>
                                        说明<br>
                                    </th>
                                    <th class="text-center" style="width:100px">
                                        传输类型<br>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="text-center" scope="row">uid</th>
                                    <td><code>登录验证</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">key</th>
                                    <td><code>登录验证</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                                <tr>
                                    <th class="text-center" scope="row">user</th>
                                    <td><code>订单账号</code></td>
                                    <td class="text-center"><code>必传</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="kcid">
                        <div class="modal-content">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>对接ID</th>
                                            <th>商品名称</th>
                                            <th>你的价格</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $a = $DB->query("select * from qingka_wangke_class where status=1 ");
                                        while ($rs = $DB->fetch($a)) {
                                            echo "<tr><td>" . $rs['cid'] . "</td><td>" . $rs['name'] . "</td><td>" . $rs['price'] * $fl . "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="xx">
                        <div class="modal-content">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>对接ID</th>
                                            <th>对接KEY</th>
                                            <th>下单比限制</th>
                                            <th>当前下单比</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $a = $DB->query("select * from qingka_wangke_user where uid='{$userrow['uid']}'");

                                        while ($rs = $DB->fetch($a)) {
                                            if ($rs['key'] == 0) {
                                                $rs['key'] = "未开通KEY";
                                                echo "<tr><td>" . $rs['uid'] . "</td><td>" . $rs['key'] . "</td><td>" . $xdbxz . "%</td><td>" . $xdb . "%</td></tr>";
                                            } else {
                                                echo "<tr><td>" . $rs['uid'] . "</td><td>" . $rs['key'] . "</td><td>" . $xdbxz . "%</td><td>" . $xdb . "%</td></tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>

<?php include_once ($root . '/index/components/footer.php'); ?>

<script>
    new Vue({
        el: "#loglist",
        data: {
            row: null
        },
        methods: {
            get: function (page) {

            }
        },
        mounted() {
            const _this = this;

            let loadIndex -layer.load(0);
            $("$dockID").ready(() => {
                layer.close(loadIndex);
                $("#dockID").show();
                _this.get(1);
            })

        }
    });
</script>