<?php
require_once ('head.php');
if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "index.php");
    exit();
}
?>

<style>
    .mainBox {
        .layui-card {
            margin-bottom: 5px;
        }
    }

    .TableClass .layui-form-checkbox {
        scale: .8;
        position: relative;
        top: -3px;
    }

    .layui-btn+.layui-btn {
        margin-left: 0;
    }

    #process_open .layui-form-label {
        width: 65px;
    }

    #process_open .layui-input-block {
        margin-left: 95px;
    }

    #process_open .layui-input-block {
        margin-left: 95px;
    }
    .process_runStatus_ol{
        padding-left: 15px;
    }
    .process_runStatus_ol li{
        list-style: auto;
    }
    
    #setBTID .layui-form-label {
        width: 65px;
    }

    #setBTID .layui-input-block {
        margin-left: 95px;
    }
</style>

<div id="btManageID" class="layui-padding-1" style="display: none;">

    <div class="layui-panel layui-padding-2" style="margin-bottom: 5px;" >
        <div>
            <span class="layui-font-12 layui-font-blue"></span>
            &nbsp;<button type="button" class="layui-btn layui-btn-primary layui-btn-sm"
                @click="setBT">配置API</button>
        </div>
    </div>
    <div v-if="v_status===0"> 
        检测中...
    </div>
    <div v-else-if="v_status==-1">
        宝塔API接口未开启或面板地址错误，请到宝塔【面板设置】->【API接口配置】启用API并在此保存正确配置
    </div>
    <div v-else-if="v_status==-2">
        密钥校验失败，请检查配置是否正确
    </div>

    <div class="mainBox" v-else>

        <div class="layui-card">
            <div class="layui-card-header">
                定时任务&nbsp;<button type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="GetCrontab()">
                    <i class="layui-icon layui-icon-refresh"></i>
                </button>
            </div>
            <div class="layui-card-body">
                <div style="margin-bottom:5px;">
                    <button type="button" class="layui-btn layui-btn-sm layui-bg-blue" @click="crontab_open()">
                        <i class="layui-icon layui-icon-addition"></i> 添加任务
                    </button>
                </div>
                <table id="crontabTable" layui-filter="crontabTable" style="width:100%;"></table>
            </div>
        </div>

        <div class="layui-card">
            <div class="layui-card-header">
                <div style="display: flex; align-items: center; gap: 5px;">
                    进程守护管理器&nbsp;<button v-if="supervisord.status !=-2" type="button" class="layui-btn layui-btn-xs layui-btn-primary"
                        @click="GetProcessList">
                        <i class="layui-icon layui-icon-refresh"></i>
                    </button>
                </div>
            </div>
            <div class="layui-card-body">
                <div v-if="supervisord.status ==-2">
                    未安装，请到宝塔面板->【软件商店】搜索【supervisor】并安装
                </div>
                <div v-else>
                    <div>
                        <div v-if="!supervisord.status" @click="SetServerStatus(1)" style="display:inline-block;cursor: pointer;" title="点击启动">
                            <i class="layui-icon layui-icon-pause"></i> 已停止
                        </div>
                        <template v-else-if="supervisord.status == -1">
                            检测中...
                        </template>
                        <div v-else @click="SetServerStatus(0)" style="display:inline-block;cursor: pointer;" title="点击停止">
                            <i class="layui-icon layui-icon-play"></i> 已启动
                        </div>
                        <button type="button" class="layui-btn layui-btn-primary layui-btn-xs"
                            @click="SetServerStatus(2)">重启</button>
                    </div>
                    <div style="margin:5px 0;">
                        <button type="button" class="layui-btn layui-btn-sm layui-bg-blue" @click="process_open()">
                            <i class="layui-icon layui-icon-addition"></i> 添加守护进程
                        </button>
                        <button type="button" class="layui-btn layui-btn-sm layui-btn-primary" @click="process_runStatus_open()">
                            状态码说明
                        </button>
                    </div>
                </div>
                <table id="processTable" layui-filter="processTable" style="width:100%;"></table>
            </div>
        </div>

    </div>

    <div id="crontab_open" class="layui-padding-2" style="display:none;">
        <form class="layui-form" action="" lay-filter="crontab_open_filter">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">任务类型</label>
                    <div class="layui-input-inline layui-input-wrap">
                        <select name="sType" lay-verify="required" lay-search>
                            <option value="toUrl" selected>访问URL</option>
                            <option value="" disabled="">其余任务类型不提供</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">*任务名称</label>
                    <div class="layui-input-inline layui-input-wrap">
                        <input lay-verify="required" type="text" name="name" autocomplete="off" placeholder="请输入计划任务名称"
                            lay-affix="clear" class="layui-input">
                        <!--<input v-else lay-verify="required" type="text" name="name" autocomplete="off" placeholder="请输入计划任务名称"-->
                        <!--    disabled="" class="layui-input layui-disabled">-->
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">执行周期</label>
                    <div class="layui-input-inline layui-input-wrap" style="width:90px;">
                        <select name="type" lay-verify="required" lay-search>
                            <option value="hour">每小时</option>
                            <option value="day">每天</option>
                            <option value="hour-n">每N小时</option>
                            <option value="minute-n">每N分钟</option>
                        </select>
                    </div>
                    <div class="layui-input-inline layui-input-wrap">
                        <div class="layui-input-group"
                            v-show="crontab_open_form.type==='day' || !crontab_open_form.type==='hour' || !crontab_open_form.type==='minute-n' || crontab_open_form.type==='hour-n'">
                            <input type="number" name="hour" value="1" placeholder="" autocomplete="off"
                                class="layui-input" min="0" max="23" step="1" lay-affix="number">
                            <div class="layui-input-split layui-input-suffix">
                                小时
                            </div>
                        </div>
                        <div class="layui-input-group"
                            v-show="crontab_open_form.type==='day' || crontab_open_form.type==='hour' || crontab_open_form.type==='minute-n'|| crontab_open_form.type==='hour-n'">
                            <input type="number" name="minute" value="30" placeholder="" autocomplete="off"
                                class="layui-input" min="0" max="59" step="1" lay-affix="number">
                            <div class="layui-input-split layui-input-suffix">
                                分钟
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">*URL地址</label>
                <div class="layui-input-block">
                    <input lay-verify="required" type="text" name="urladdress" autocomplete="off" placeholder="请输入URL地址"
                        lay-affix="clear" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">User-Agent</label>
                <div class="layui-input-block">
                    <textarea name="user_agent" placeholder="请输入User-Agent配置，留空即可" class="layui-textarea"></textarea>
                </div>
            </div>

            <button type="reset" class="layui-btn layui-btn-primary" style="dispaly:none"
                id="crontab_open_filter_reset">重置</button>

        </form>
    </div>

    <div id="crontabLogsBox" class="layui-padding-2" style="display:none;">
        <button class="layui-btn layui-btn-primary layui-border-green"
            @click="GetLogs({id:crontab_now})">刷新日志</button>&nbsp;&nbsp;
        <button class="layui-btn layui-btn-primary layui-border-red"
            @click="DelLogs({id:crontab_now},1)">清空日志</button>&nbsp;&nbsp;
        <button class="layui-btn layui-btn-primary layui-border-blue"
            @click="StartTask({id:crontab_now},1)">执行任务</button>
        <hr />
        <pre class="layui-code code-demo layui-padding-2"
            style="padding: 15px !important; height: 78vh; overflow-y: auto;" lay-options="{theme: 'dark'}"
            v-html="crontabLogs">
        </pre>
    </div>

    <!--process_open-->
    <div id="process_open" class="layui-padding-2" style="display:none;">
        <form class="layui-form" action="" lay-filter="process_open_filter">

            <div class="layui-form-item">
                <label class="layui-form-label">*名称</label>
                <div class="layui-input-block">
                    <input v-if="!process_open_form_edit" lay-verify="required" type="text" name="pjname" autocomplete="off" placeholder="请输入名称"
                        lay-affix="clear" class="layui-input"  >
                    <input v-else lay-verify="required" type="text" name="pjname" autocomplete="off" placeholder="请输入名称"
                        :lay-affix="process_open_form_edit?'':'clear'" class="layui-input layui-disabled">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">启动用户</label>
                <div class="layui-input-block">
                    <select name="user" lay-verify="required" lay-search>
                        <option value="root" selected>root(默认的就行)</option>
                        <option value="www" >www</option>
                        <option value="mysql" >mysql</option>
                        <option value="redis" >redis</option>
                        <option value="springboot" >springboot</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">*运行目录</label>
                <div class="layui-input-block">
                    <input lay-verify="required" type="text" name="path" value="<?php echo $root ?>/redis/"
                        autocomplete="off" placeholder="请输入运行目录" lay-affix="clear" class="layui-input">
                    <div class="layui-font-12 layui-font-red">
                        例子：<br /><?php echo $root ?>/redis/
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">*启动命令</label>
                <div class="layui-input-block">
                    <input lay-verify="required" type="text" name="command" autocomplete="off" placeholder="请输入启动命令"
                        lay-affix="clear" class="layui-input">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">进程数量</label>
                <div class="layui-input-block">
                    <input type="number" name="numprocs" value="5" placeholder="" autocomplete="off" class="layui-input"
                        min="1" max="60" step="1" lay-affix="number">
                </div>
            </div>

            <div class="layui-form-item" v-if=" process_open_form_edit">
                <label class="layui-form-label">启动优先级</label>
                <div class="layui-input-block">
                    <input type="number" name="level" value="999" placeholder="" autocomplete="off" class="layui-input"
                        min="1" max="999999" step="1" lay-affix="number">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">备注</label>
                <div class="layui-input-block">
                    <input  type="text" name="ps" autocomplete="off" placeholder="请输入备注"
                        lay-affix="clear" class="layui-input">
                </div>
            </div>

            <button type="submit" class="layui-btn" style="dispaly:none" lay-submit id="process_open_filter_submit"
                lay-filter="process_open_filter_submit">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary" style="dispaly:none"
                id="process_open_filter_reset">重置</button>

        </form>
    </div>

    <div id="processLogsBox" class="layui-padding-2" style="display:none;">
        <button class="layui-btn layui-btn-primary layui-border-green"
            @click="GetProcessLogs({program:process_now})">刷新日志</button>&nbsp;&nbsp;
        <button class="layui-btn layui-btn-primary layui-border-red"
            @click="DelProcessLogs({program:process_now},1)">清空日志</button>&nbsp;&nbsp;
        <!--<button class="layui-btn layui-btn-primary layui-border-blue" @click="StartTask({id:process_now},1)">执行任务</button>-->
        <hr />
        <div class="layui-tab" style="margin-bottom: 0;" lay-filter="tab-handle">
            <ul class="layui-tab-title">
                <li lay-id='1' class="layui-this"
                    @click="processLogs_type='normal';GetProcessLogs({program:process_now})">运行日志</li>
                <li lay-id="2" @click="processLogs_type='error';GetProcessLogs({program:process_now},'error')">错误日志</li>
            </ul>
            <div class="layui-tab-content" style="padding: 0;">
            </div>
        </div>
        <pre class="layui-code code-demo layui-padding-2"
            style="padding: 15px !important; height: 71vh; overflow-y: auto;" lay-options="{theme: 'dark'}"
            v-text="processLogs.trim()?processLogs.trim():'暂无日志'">
        </pre>
    </div>

    <div id="setBTID" class="layui-padding-2" style="display:none;">

        <form class="layui-form" action="" lay-filter="setBTID_filter">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">Token</label>
                    <div class="layui-input-inline layui-input-wrap">
                        <input lay-verify="required" type="password" v-model="setBT_config.token" name="token"
                            autocomplete="off" placeholder="请输入宝塔API Token" lay-affix="clear" class="layui-input">
                    </div>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">面板地址</label>
                    <div class="layui-input-inline layui-input-wrap">
                        <input lay-verify="required" type="password" v-model="setBT_config.panel" name="panel"
                            autocomplete="off" placeholder="请输入面板地址" lay-affix="clear" class="layui-input">
                        <div class="layui-font-red layui-font-13" style="line-height: 20px; margin-top: 5px;">
                            格式：<br />http(s)://<?= GetHostByName($_SERVER['SERVER_NAME']) ?>:端口号/
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

</div>

<script type="text/html" id="crontabTable_caoz">
    <div style="">
        <button type="button" lay-event="crontabTable_run" class="layui-btn layui-btn-primary layui-btn-xs" style="border:0;">执行</button>&nbsp;|
        <button type="button" lay-event="crontabTable_log" class="layui-btn layui-btn-primary layui-btn-xs" style="border:0;">日志</button>&nbsp;|
        <button type="button" {{d.sType!=='toUrl'?'disabled':''}} lay-event="crontabTable_edit" class="layui-btn layui-btn-primary layui-btn-xs {{d.sType!=='toUrl'?'layui-disabled':''}}" style="border:0;">编辑</button>&nbsp;|
        <button type="button" lay-event="crontabTable_del" class="layui-btn layui-btn-primary layui-btn-xs" style="border:0;">删除</button>
    </div>
</script>

<script type="text/html" id="processTable_caoz">
    <div style="">
        <button type="button" lay-event="processTable_log" class="layui-btn layui-btn-primary layui-btn-xs" style="border:0;">日志</button>&nbsp;|
        <button type="button" lay-event="processTable_run" class="layui-btn layui-btn-primary layui-btn-xs" style="border:0;">重启</button>&nbsp;|
        <button type="button" lay-event="processTable_edit" class="layui-btn layui-btn-primary layui-btn-xs " style="border:0;">编辑</button>&nbsp;|
        <button type="button" lay-event="processTable_del" class="layui-btn layui-btn-primary layui-btn-xs" style="border:0;">删除</button>
    </div>
</script>

<script>
    const app = Vue.createApp({
        data(){
            return{
                doc_scrollTop: 0,
                setBT_config: {
                    token: '',
                    panel: '',
                },
                v_status: 0,
                os: {
    
                },
                crontab_row: {
                    data: [],
                },
                crontab_open_form_edit:0,
                crontab_open_form: {
                    type: 'hour',
                },
                crontab_now: 0,
                crontabLogs: '',
                supervisord: {
                    status: 0,
                },
                process_row: {
                    data: [],
                },
                process_open_form_edit: 0,
                process_open_form: {
    
                },
                process_now: 0,
                processLogs: '',
                processLogs_type: '',
                timer: null,
            }
        },
        computed:{
        },
        mounted() {
            const _this = this;
            $("#btManageID").ready(() => {
                $("#btManageID").show();
                _this.bt_v();
                $(document).scroll(function() {
                  _this.doc_scrollTop = $(this).scrollTop();
                });
            })
        },
        methods: {
            setBT() {
                const _this = this;
                _this.setBT_config.token = _this.v_status === 1 ? <?= json_encode($conf["bt_token"]) ?> : '';
                _this.setBT_config.panel = _this.v_status === 1 ? <?= json_encode($conf["bt_panel"]) ?> : '';
                layer.open({
                    type: 1,
                    title: '宝塔API配置',
                    content: $("#setBTID"),
                    closeBtn: 0,
                    area:["350px"],
                    btn: ["保存", "取消"],
                    btn1: function (index) {
                        if (!_this.setBT_config.token.trim()) {
                            layer.msg("请输入Token")
                            return;
                        }
                        if (!_this.setBT_config.panel.trim()) {
                            layer.msg("请输入面板地址")
                            return;
                        }
                        layer.load(0);
                        axios.post('/api/bt.php?act=setBT', {
                            token: _this.setBT_config.token.trim(),
                            panel: _this.setBT_config.panel.trim(),
                        }, { emulateJSON: true }).then(r => {
                            layer.closeAll("loading");
                            if (r.data.code !== 1) {
                                layer.msg("修改失败！" + r.data.msg ? r.data.msg : "")
                                return
                            }
                            layer.msg("保存成功，请稍等...")
                            setTimeout(() => {
                                location.reload();
                            }, 150)
                        })
                    },
                })

            },
            // 检测宝塔API是否可用
            bt_v() {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post('/api/bt.php?act=v', {
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    
                    if (r.data.code == -1) {
                        _this.setBT();
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        _this.v_status = -1;
                        return
                    }
                    if (r.data.code === -2) {
                        _this.v_status = -2;
                        layer.msg("密钥校验失败")
                        return
                    }
                    _this.v_status = 1;
                    _this.os = r.data.data;
                    _this.GetCrontab();
                    
                    if(r.data.code == -3){
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        _this.supervisord.status = -2;
                    }else{
                        _this.GetServerStatus();
                    }
                })
            },
            // 定时任务列表
            GetCrontab() {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post('/api/bt.php?act=GetCrontab', {
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    _this.GetProcessList();
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        return
                    }
                    _this.crontab_row.data = r.data.data;
                    _this.GetCrontab_init();
                    
                    
                    let doc_scrollTop =_this.doc_scrollTop;
                    setTimeout(()=>{
                    $(document).scrollTop(doc_scrollTop)
                    },0)
                    
                })
            },
            // 定时任务表格初始化
            GetCrontab_init() {
                const _this = this;
                layui.use(() => {
                    layui.table.render({
                        elem: '#crontabTable',
                        id: 'crontabTable',
                        size: 'sm',
                        className: 'TableClass',
                        text: {
                            none: '哦吼一条定时任务没得'
                        },
                        data: _this.crontab_row.data,
                        cellExpandedMode: 'tips',
                        even: true,
                        page: false,
                        cols: [
                            [
                                {
                                    type: 'checkbox',
                                    // fixed: 'left',
                                    align: 'center',
                                },
                                {
                                    field: 'id',
                                    title: 'ID',
                                    width: 30,
                                    align: 'center',
                                },
                                {
                                    field: 'rname',
                                    title: '任务名称1',
                                    width: 140,
                                },
                                {
                                    field: 'urladdress',
                                    title: 'URL地址',
                                    minWidth: 175,
                                    templet: `{{d.urladdress?d.urladdress:"-"}}`,
                                },
                                {
                                    field: 'status',
                                    title: '状态',
                                    width: 110,
                                    align: 'center',
                                    templet: `<input lay-filter="crontabTable_edit_b" data-id="{{d.id}}"  type="checkbox" name="status" title="{{ d.status==1?"开启":"停止" }}" lay-skin="tag" {{d.status==1?"checked":""}}> `
                                },
                                {
                                    field: 'cycle',
                                    title: '执行周期',
                                    width: 165,
                                },
                                {
                                    field: 'addtime',
                                    title: '上次执行时间',
                                    width: 140,
                                    align: 'center',
                                },
                                {
                                    field: 'id',
                                    title: '操作',
                                    align: 'center',
                                    width: 180,
                                    fixed: 'right',
                                    templet: '#crontabTable_caoz'
                                },
                            ],
                        ],
                        done: function (res, curr, count) {
                            // 切换按钮
                            layui.form.on('checkbox(crontabTable_edit_b)', function (data) {
                                var elem = data.elem;
                                var value = elem.value; // 输入框的值
                                if (!value) {
                                    layer.msg('不能为空');
                                    return;
                                };
                                let id = elem.dataset.id;
                                if (elem.name === 'status') {
                                    let thisData = res.data.find(i => i.id == id);
                                    let loadIndex =layer.load(0);
                                    axios.post("/api/bt.php?act=set_cron_status", {
                                        id: id,
                                    }, { emulateJSON: true }).then(r => {
                                        layer.close(loadIndex);
                                        if (r.data.code !== 1) {
                                            layer.msg(r.data.msg ? r.data.msg : "网络异常")
                                            return
                                        }
                                        console.log(thisData)
                                        layer.msg('成功' + (thisData.status ? '停止' : '开启'));
                                        _this.GetCrontab();
                                    });
                                }
                            });

                            layui.table.on('tool(crontabTable)', function (obj) {
                                let data = obj.data;
                                switch (obj.event) {
                                    case "crontabTable_del":
                                        layer.confirm("是否确定删除？", {}, function (index) {
                                            layer.close(index);
                                            layer.load(0);
                                            axios.post("/api/bt.php?act=DelCrontab", {
                                                id: [data.id],
                                            }, { emulateJSON: true }).then(r => {
                                                layer.closeAll("loading");
                                                if (r.data.code !== 1) {
                                                    layer.msg(r.data.msg ? r.data.msg : "网络异常")
                                                    return
                                                }
                                                layer.msg('删除成功');
                                                _this.GetCrontab();
                                            });
                                        })
                                        break;
                                    case "crontabTable_log":
                                        _this.crontabLogs = '';
                                        _this.crontab_now = 0;
                                        layer.open({
                                            type: 1,
                                            title: '日志 | ' + data.name,
                                            content: $("#crontabLogsBox"),
                                            area: ["98%", "98%"],
                                            end: function () {
                                                _this.crontabLogs = '';
                                                _this.crontab_now = 0;
                                            },
                                        })
                                        _this.crontab_now = data.id;
                                        layui.element.tabChange('tab-handle', '1');
                                        _this.GetLogs(data);
                                        layui.code({
                                            elem: '.code-demo'
                                        });
                                        break;
                                    case "crontabTable_run":
                                        _this.crontab_now = 0;
                                        _this.crontab_now = data.id;
                                        _this.StartTask(data);
                                        break;
                                    case "crontabTable_edit":
                                        _this.crontab_now = data.id;
                                        _this.crontab_open(1, data);
                                        break;
                                }
                            })

                        },
                    })
                });
            },
            GetLogs(data) {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post("/api/bt.php?act=GetLogs", {
                    id: data.id,
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        return
                    }
                    _this.crontabLogs = r.data.data;
                    $(".code-demo").ready(() => {
                        $(".code-demo").scrollTop($(".code-demo")[0].scrollHeight);
                    })
                });
            },
            StartTask(data, type) {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post("/api/bt.php?act=StartTask", {
                    id: data.id,
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        return
                    }
                    layer.msg("执行成功");
                    if (type) {
                        _this.GetLogs({ id: data.id });
                    }
                });
            },
            DelLogs(data, type) {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post("/api/bt.php?act=DelLogs", {
                    id: data.id,
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        return
                    }
                    layer.msg("已清空");
                    if (type) {
                        _this.GetLogs({ id: data.id });
                    }
                });
            },
            crontab_open(type, data) {
                const _this = this;
                $("#crontab_open_filter_reset").click();
                $("#crontab_open_filter_reset").hide();
                _this.crontab_open_form.type = "hour";
                _this.crontab_open_form_edit =type?1:0;
                let id = 0;
                if (type) {
                    id = data.id;
                    _this.crontab_open_form.type = data.type;
                }
                layui.use(() => {
                    layer.open({
                        type: 1,
                        id: "crontab_o_open",
                        title: type ? '修改' : '定时任务添加',
                        content: $("#crontab_open"),
                        area:["350px"],
                        btn: [type ? "修改" : "提交", "取消"],
                        btn1: function (index) {
                            let data = layui.form.val("crontab_open_filter");
                            console.log('data',data)
                            if (data.type === 'minute-n') {
                                data.where1 = data.minute;
                            }
                            if (data.type === 'hour-n') {
                                data.where1 = data.hour;
                            }
                            let loadIndex = layer.load(0);
                            if (type) {
                                data.id = id;
                            }
                            axios.post("/api/bt.php?act=" + (type ? "modify_crond" : "AddCrontab"), data, { emulateJSON: true }).then(r => {
                                layer.close(loadIndex);
                                if (r.data.code !== 1) {
                                    layer.msg(r.data.msg ? r.data.msg : "网络异常")
                                    return
                                }
                                layer.msg('添加成功！');
                                _this.GetCrontab();
                                layer.close(index);
                            })
                        },
                        success: () => {
                            if (type) {
                                data.name =data.rname;
                                layui.form.val("crontab_open_filter", data);
                            }
                            layui.form.on('select', function (data) {
                                let elem = data.elem
                                _this.crontab_open_form[elem.name] = data.value;
                                layui.form.render();
                            });
                        },
                    })
                })
            },
            // 进程守护管理器
            GetServerStatus(type) {
                const _this = this;
                _this.supervisord.status = -1;
                axios.post("/api/bt.php?act=GetServerStatus", {

                }, { emulateJSON: true }).then(r => {
                    if (r.data.code !== 1) {
                        _this.supervisord.status = 0;
                        if (!type) {
                            layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        }
                        return
                    }
                    _this.supervisord.status = 1;
                })
            },
            SetServerStatus(status = 0) {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post("/api/bt.php?act=SetServerStatus", {
                    status: status,
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                    _this.GetProcessList();
                        return
                    }
                    layer.msg(r.data.msg);
                    _this.GetServerStatus(1);
                    _this.GetProcessList();
                })
            },
            GetProcessList() {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post('/api/bt.php?act=GetProcessList', {
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                    _this.process_row.data =[];
                        return
                    }
                    _this.process_row.data = r.data.data?r.data.data:[];
                    _this.GetProcessList_init();
                    
                    let doc_scrollTop =_this.doc_scrollTop;
                    setTimeout(()=>{
                    $(document).scrollTop(doc_scrollTop)
                    },0)
                    
                })
            },
            GetProcessList_init() {
                const _this = this;
                layui.use(() => {
                    layui.table.render({
                        elem: '#processTable',
                        id: 'processTable',
                        size: 'sm',
                        className: 'TableClass',
                        text: {
                            none: '哦吼一条定时任务没得'
                        },
                        data: _this.process_row.data,
                        autoSort: true,
                        initSort: {
                            field: 'pid',
                            type: 'desc'
                        },
                        cellExpandedMode: 'tips',
                        even: true,
                        page: false,
                        cols: [
                            [
                                // {
                                //     type: 'checkbox',
                                //     // fixed: 'left',
                                //     align: 'center',
                                // },
                                {
                                    field: 'pid',
                                    title: 'ID',
                                    width: 40,
                                    align: 'center',
                                },
                                {
                                    field: 'program',
                                    title: '任务名称',
                                    width: 150,
                                },
                                {
                                    field: 'command',
                                    title: '启动命令',
                                    minWidth: 175,
                                },
                                {
                                    field: 'status',
                                    title: '进程状态',
                                    width: 110,
                                    align: 'center',
                                    templet: `<input lay-filter="processTable_edit_b" data-pid="{{d.pid}}"  type="checkbox" name="status" title="{{ d.status==1?"开启":"停止" }}" lay-skin="tag" {{d.status==1?"checked":""}}> `
                                },
                                {
                                    field: 'runStatus',
                                    title: '状态码',
                                    width: 100,
                                    align: 'center',
                                },
                                {
                                    field: 'numprocs',
                                    title: '进程数量',
                                    width: 75,
                                    align: 'center',
                                },
                                {
                                    field: 'priority',
                                    title: '优先级',
                                    width: 65,
                                    align: 'center',
                                },
                                {
                                    field: 'numprocs',
                                    title: '进程数量',
                                    width: 75,
                                    align: 'center',
                                },
                                {
                                    field: 'user',
                                    title: '启动用户',
                                    width: 70,
                                    align: 'center',
                                },
                                {
                                    field: 'ps',
                                    title: '备注',
                                    minWidth: 120,
                                },
                                {
                                    field: 'id',
                                    title: '操作',
                                    align: 'center',
                                    width: 180,
                                    fixed: 'right',
                                    templet: '#processTable_caoz'
                                },
                            ],
                        ],
                        done: function (res, curr, count) {
                            // 切换按钮
                            layui.form.on('checkbox(processTable_edit_b)', function (data) {
                                var elem = data.elem;
                                var value = elem.value; // 输入框的值
                                if (!value) {
                                    layer.msg('不能为空');
                                    return;
                                };
                                let pid = elem.dataset.pid;
                                if (elem.name === 'status') {
                                    let thisData = res.data.find(i => i.pid == pid);
                                    _this.set_process_status(thisData.status == 0 ? 1 : 0, thisData);
                                }
                            });

                            layui.table.on('tool(processTable)', function (obj) {
                                let data = obj.data;
                                switch (obj.event) {
                                    case "processTable_del":
                                        layer.confirm("是否确定删除？", {}, function (index) {
                                            layer.close(index);
                                            layer.load(0);
                                            let loadIndex = layer.load(0,);
                                            axios.post("/api/bt.php?act=RemoveProcess", {
                                                program: data.program,
                                            }, { emulateJSON: true }).then(r => {
                                                layer.close(loadIndex);
                                                if (r.data.code !== 1) {
                                                    layer.msg(r.data.msg ? r.data.msg : "网络异常")
                                                    return
                                                }
                                                layer.msg("删除成功");
                                                _this.GetProcessList();
                                            });
                                        })
                                        break;
                                    case "processTable_log":
                                        _this.processLogs = '';
                                        _this.process_now = 0;
                                        _this.processLogs_type = 'normal';
                                        layer.open({
                                            type: 1,
                                            title: `日志 | 【${data.pid}】${data.program}`,
                                            content: $("#processLogsBox"),
                                            area: ["98%", "98%"],
                                            end: function () {
                                                _this.processLogs = '';
                                                _this.process_now = 0;
                                            },
                                        })
                                        _this.process_now = data.program;
                                        layui.element.tabChange('tab-handle', '1');
                                        _this.GetProcessLogs(data);
                                        layui.code({
                                            elem: '.code-demo'
                                        });
                                        break;
                                    case "processTable_run":
                                        _this.process_now = 0;
                                        _this.process_now = data.program;
                                        _this.set_process_status(0, data, 1);
                                        break;
                                    case "processTable_edit":
                                        _this.process_now = data.program;
                                        _this.process_open(1, data);
                                        break;
                                }
                            })

                        },
                    })
                });
            },
            set_process_status(status, thisData = {}, twoRun) {
                const _this = this;
                status ? 1 : 0;
                let loadIndex = layer.load(0,);
                axios.post("/api/bt.php?act=set_process_status", {
                    program: thisData.program,
                    numprocs: thisData.numprocs,
                    status: thisData.status == 0 ? 1 : 0,
                    twoRun: twoRun ? 1 : 0,
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        return
                    }
                    layer.msg(r.data.msg);
                    _this.GetProcessList();
                });
            },
            GetProcessLogs(data, type) {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post("/api/bt.php?act=GetProcessLogs", {
                    program: data.program,
                    log_type: type ? 'error' : 'normal',
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        return
                    }
                    _this.processLogs = JSON.parse(_this.decodeUnicode(r.data.data));


                    $(".code-demo").ready(() => {
                        for (let i in $(".code-demo")) {
                            $(".code-demo").scrollTop($(".code-demo")[i].scrollHeight);
                        }
                    })
                });
            },
            DelProcessLogs(data, type) {
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post("/api/bt.php?act=clear_record", {
                    program: data.program,
                    log_type: _this.processLogs_type,
                }, { emulateJSON: true }).then(r => {
                    layer.close(loadIndex);
                    if (r.data.code !== 1) {
                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                        return
                    }
                    layer.msg("已清空");
                    if (type) {
                        _this.GetProcessLogs(data, _this.processLogs_type == 'error' ? 1 : 0);
                    }
                });
            },
            process_open(type, data) {
                const _this = this;
                $("#process_open_filter_reset").click();
                $("#process_open_filter_reset").hide();
                // _this.process_open_form.type="hour";
                let id = 0;
                if (type) {
                    id = data.id;
                    // _this.process_open_form.type=data.type;
                }
                
                _this.process_open_form_edit= type?1:0;
                
                layui.use(() => {
                    layer.open({
                        type: 1,
                        id: "process_o_open",
                        title: (type ? '修改' : '添加') + "守护进程",
                        content: $("#process_open"),
                        area:["350px"],
                        btn: [type ? "修改" : "添加", "取消"],
                        btn1: function (index) {
                            $("#process_open_filter_submit").click();
                            $("#process_open_filter_submit").hide();
                        },
                        success: function(layero, index, that)  {
                            layui.form.render();
                            $("#process_open_filter_submit").hide();
                            
                            if (type) {
                                data.pjname = data.program;
                                layui.form.val("process_open_filter", data);
                            }
                            layui.form.on('select', function (data) {
                                let elem = data.elem
                                _this.crontab_open_form[elem.name] = data.value;
                                layui.form.render();
                            });
                            
                            layui.form.on('submit(process_open_filter_submit)', function (data) {
                                var data = data.field; // 获取表单全部字段值
                                let loadIndex = layer.load(0);
                                axios.post("/api/bt.php?act=" + (type ? "UpdateProcess" : "AddProcess"), data, { emulateJSON: true }).then(r => {
                                    layer.close(loadIndex);
                                    if (r.data.code !== 1) {
                                        layer.msg(r.data.msg ? r.data.msg : "网络异常")
                                        return
                                    }
                                    layer.msg(r.data.msg);
                                    _this.GetProcessList();
                                    layer.close(index);
                                })
                            
                                return false; // 阻止默认 form 跳转
                            });
                            
                        },
                    })
                })
            },
            process_runStatus_open(){
              layui.use(()=>{
                  layer.open({
                      type: 1,
                      title: '状态码说明',
                      content: `<div class="layui-padding-2">
                        <ol class="process_runStatus_ol">
                            <li>
                                STOPPED：该进程已停止
                            </li>
                            <li>
                                STOPPING：由于停止请求，该进程正在停止
                            </li>
                            <li>
                                RUNNING：该进程正在运行
                            </li>
                            <li>
                                STARTING：该进程由于启动请求而开始
                            </li>
                            <li>
                                FATAL：该进程无法成功启动
                            </li>
                            <li>
                                ACKOFF：该进程进入"启动"状态，但随后退出的速度太快而无法移至"运行"状态
                            </li>
                        </ol>
                      </div>`,
                      area: ["350px"],
                  })
              })  
            },
            decodeUnicode(str) {
                return str.replace(/\\u([\d\w]{4})/gi, function (match, grp) {
                    return String.fromCharCode(parseInt(grp, 16));
                });
            },
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var btManageVm = app.mount('#btManageID');
    // -----------------------------
</script>