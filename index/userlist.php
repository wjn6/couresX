<?php
$title = '代理列表';
require_once('head.php');
if ($userrow['uid'] < '1') {
    exit("<script language='javascript'>window.location.href='/login.php';</script>");
}

?>

<div class="app-content-body ">
    <div class="wrapper-md control layui-padding-1" id="userlist" style="display:none;">

        <div v-if="dlgl_notice_open" style="margin-bottom: 5px;" class="layui-font-12">
            <div class="layui-collapse" lay-accordion>
                <div class="layui-colla-item">
                    <div class="layui-panel layui-colla-content layui-show">
                        <!--<?= $conf["user_ktmoney"] ?>-->
                        <!--<?= $conf['dlgl_notice']; ?>-->
                        <span v-html="dlgl_notice()"></span>
                    </div>
                </div>
            </div>
        </div>

        <div id="userlistTable" class=" layui-panel">
            
            <?php if($userrow["uid"] == 1){ ?>
                <div v-if="row.tongji" class="layui-padding-2 layui-font-12" style="border-bottom: 1px solid #efefef;">
                    <i class="layui-icon layui-icon-eye"></i> 代理数据统计(近一个月登录过的)：<br />
                    待消耗：￥{{row.tongji.money_waitUse}} | 未被封禁：{{row.tongji.user_active}}个 | 直属代理：{{row.tongji.admin_user}}个
                </div>
            <?php } ?>
            
            <div class="panel-body" style="overflow: hidden;">
                <div class="layui-row layui-col-space10">
                    <div class="layui-form form-inline layui-padding-3" style="display: flex; gap: 5px; align-items: center; flex-wrap: wrap;">
                        <div style="display: inline-block;" class="layui-form">
                            <div class="layui-input-group">
                                <div style="width: 85px;margin-bottom: 5px;">
                                    <select lay-append-to="body" lay-filter="search_fenlei_select">
                                        <option value="1">UID</option>
                                        <option value="2">账号</option>
                                        <option value="3">邀请码</option>
                                        <option value="4">昵称</option>
                                        <option value="5">费率</option>
                                        <option value="6">余额</option>
                                    </select>
                                </div>
                                <input type="text" placeholder="请输入内容" @keydown.enter="get(1)" class="layui-input " v-model="qq" lay-affix="clear">
                                <div class="layui-input-split layui-input-suffix" style="cursor: pointer;" @click="get(1)">
                                    <i class="layui-icon layui-icon-search"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="button" class="layui-btn layui-bg-blue layui-btn-sm" @click="adduserOpen">
                                <i class="layui-icon layui-icon-add-1"></i> 添加代理
                            </button>
                            <button v-if="admin" type="button" class="layui-btn layui-bg-red layui-btn-sm" @click="deluser($refs.listTable.getSelectionRows().map(i=>i.uid))">
                                <i class="layui-icon layui-icon-delete"></i> 删除代理
                            </button>
                        </div>
                    </div>
                    <div style="overflow-x: auto;">
                        
                        <el-table ref="listTable" :data="row.data" stripe border show-overflow-tooltip empty-text="无代理，请添加" size="small" style="width: 100%">
                            
                            <el-table-column fixed="left" type="selection" width="28" align="center" ></el-table-column>
                            <el-table-column prop="uid" label="UID" width="50" align="center" >
                                <template #default="scope">
                                    <div style="line-height: 15px;">
                                        {{scope.row.uid}}<br />
                                        <?php if ($userrow["uid"] == 1) { ?>
                                            <div v-if="scope.row.uuid === '1'" style="scale: .8;">
                                                <el-tag type="primary" size="small">直属</el-tag>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="uid" label="头像" width="50" align="center" >
                                <template #default="scope">
                                    <img  @click="photosView(1,[{alt:scope.row.name,pid:1,src:'https://q1.qlogo.cn/g?b=qq&nk='+(scope.row.qq?scope.row.qq:scope.row.user)+'&s=100'}])" style="width:24px;cursor: pointer;" :src="'https://q1.qlogo.cn/g?b=qq&nk='+(scope.row.qq?scope.row.qq:scope.row.user)+'&s=100'">
                                </template>
                            </el-table-column>
                            <el-table-column prop="active" label="状态" width="55" align="center">
                                <template #default="scope">
                                    <el-switch :disabled="!admin" v-model="scope.row.active" size="small" active-value="1" inactive-value="0" @change="ban(scope.row.uid,scope.row.active==1?0:1)">
                                        <template #active-action>
                                            <el-icon><Check /></el-icon>
                                        </template>
                                        <template #inactive-action>
                                            <el-icon><Close /></el-icon>
                                        </template>
                                    </el-switch>
                                </template>
                            </el-table-column>
                            
                            <el-table-column v-if="admin" prop="user" label="账号" width="150" >
                                <template #default="scope">
                                    <div>
                                        <el-input v-model="scope.row.user" size="small">
                                            <template #append>
                                                <el-button @click="setuser(scope.row,{user:scope.row.user})" style="padding: 8px 0;"><el-icon><Edit-Pen /></el-icon></el-button>
                                            </template>
                                        </el-input>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column v-else prop="user" label="账号" width="100" >
                                
                            </el-table-column>
                            
                            <el-table-column v-if="admin" prop="pass" label="密码" width="150" >
                                <template #default="scope">
                                    <el-input v-model="scope.row.pass" size="small">
                                        <template #append>
                                            <el-button @click="setuser(scope.row,{pass:scope.row.pass})" style="padding: 8px 0;"><el-icon><Edit-Pen /></el-icon></el-button>
                                        </template>
                                    </el-input>
                                </template>
                            </el-table-column>
                            <el-table-column v-else prop="pass" label="密码" width="70" >
                                
                            </el-table-column>
                            
                            <el-table-column v-if="admin" prop="name" label="昵称" width="130" >
                                <template #default="scope">
                                    <el-input v-model="scope.row.name" size="small">
                                        <template #append>
                                            <el-button @click="setuser(scope.row,{name:scope.row.name})" style="padding: 8px 0;"><el-icon><Edit-Pen /></el-icon></el-button>
                                        </template>
                                    </el-input>
                                </template>
                            </el-table-column>
                            <el-table-column v-else prop="name" label="昵称" width="65" >
                                
                            </el-table-column>
                            
                            <el-table-column v-if="admin" prop="money" label="余额￥" width="160" >
                                <template #default="scope">
                                    <div style="width: auto; white-space: normal; word-break: break-all; display: flex; align-items: center;">
                                        <el-input-number style="width: 110px;z-index: 1;" v-model="scope.row.money" :min="0" :precision="2" :step="0.01" controls-position="right"  @keydown.enter="setuser(scope.row,{money:scope.row.money})">
                                            <template #decrease-icon>
                                                <div style="width: 100%;text-align: center;" @click="setuser(scope.row,{money:scope.row.money})">
                                                   <el-icon ><Minus /></el-icon>
                                                </div>
                                            </template>
                                            <template #increase-icon>
                                                <div style="width: 100%;text-align: center;" @click="setuser(scope.row,{money:scope.row.money})">
                                                   <el-icon ><Plus /></el-icon>
                                                </div>
                                            </template>
                                        </el-input-number>
                                        <el-button @click="setuser(scope.row,{money:scope.row.money})" type="info" plain size="small" style="position: relative;left: -5px; padding: 15px 7px 15px 10px;">
                                            <el-icon ><Check /></el-icon>
                                        </el-button>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column v-else prop="money" label="余额￥" width="70" >
                                
                            </el-table-column>
                            
                            <el-table-column v-if="admin" prop="addprice" label="费率" width="110" align="center">
                                <template #default="scope">
                                    <el-input v-model="scope.row.addprice" size="small">
                                        <template #append>
                                            <el-button @click="setuser(scope.row,{addprice:scope.row.addprice})" style="padding: 8px 0;"><el-icon><Edit-Pen /></el-icon></el-button>
                                        </template>
                                    </el-input>
                                </template>
                            </el-table-column>
                            <el-table-column v-else prop="addprice" label="费率" width="50" align="center">
                                
                            </el-table-column>
                            
                            <el-table-column prop="dd" label="单量" width="55" align="center">
                            </el-table-column>
                            
                            <el-table-column v-if="admin" prop="dl_num" label="下级数" width="55" align="center">
                                <template #default="scope">
                                    <template v-if="scope.row.dl_num == 0">
                                        暂无
                                    </template>
                                    <template v-else>
                                        {{scope.row.dl_num}}
                                    </template>
                                </template>
                            </el-table-column>
                            
                            <el-table-column v-if="admin" prop="uuid" label="上级" width="170" >
                                <template #default="scope">
                                    <div class="layui-form" lay-filter="useridForm_filter">
                                        <select :disabled="!admin || scope.row.uid == 1" lay-append-to="body" lay-filter="change_uuid_select" :data-res="JSON.stringify(scope.row)" lay-search="">
                                            <option disabled :selected="row_userid.findIndex(i=>i.uid == scope.row.uuid) == -1 && scope.row.uuid != 1">
                                                【{{scope.row.uuid}}】未知
                                            </option>
                                            <option value="1" :selected="scope.row.uuid == 1">【1】管理员</option>
                                            <template v-for="(item,index) in row_userid" :key="index">
                                                <option :value="item.uid" :selected="item.uid == scope.row.uuid">
                                                    【{{item.uid}}】{{item.name}}
                                                </option>
                                            </template>
                                        </select>
                                    </div>
                                </template>
                            </el-table-column>
                            <el-table-column prop="key" label="密钥/Key" width="80" align="center">
                                <template #default="scope">
                                    <span v-if="!scope.row.key" class="layui-btn layui-btn-xs layui-bg-blue" @click="ktapi(scope.row.uid)">点击开通</span>
                                    <span v-else-if="!admin">
                                        禁止查看
                                    </span>
                                    <span v-else class="layui-font-12" @click="copyT(scope.row.key)">
                                        点击复制
                                    </span>
                                </template>
                            </el-table-column>
                            
                            <el-table-column v-if="admin" prop="zcz" label="总充值￥" width="120" >
                                <template #default="scope">
                                    <el-input v-model="scope.row.zcz" size="small">
                                        <template #append>
                                            <el-button @click="setuser(scope.row,{zcz:scope.row.zcz})" style="padding: 8px 0;"><el-icon><Edit-Pen /></el-icon></el-button>
                                        </template>
                                    </el-input>
                                </template>
                            </el-table-column>
                            <el-table-column v-else prop="zcz" label="总充值￥" width="70" >
                                
                            </el-table-column>
                            
                            <el-table-column prop="yqm" label="邀请码" width="80" align="center">
                                <template #default="scope">
                                    <span v-if="!scope.row.yqm" class="layui-btn layui-btn-xs layui-bg-blue" @click="yqm(scope.row.uid,scope.row.name)">点击设置</span>
                                    <span v-else-if="!admin">
                                        {{scope.row.yqm}}
                                    </span>
                                    <span v-else class="layui-font-12" @click="yqm(scope.row.uid,scope.row.name)">{{scope.row.yqm}}</span>
                                </template>
                            </el-table-column>
                            <el-table-column prop="ck" label="调用详情" width="68" align="center" >
                                <template #default="scope">
                                    <el-popover :width="400"  trigger="hover" class="header_elPopover">
                                        <template #reference>
                                            <span style="cursor: pointer;">
                                                查看详情
                                            </span>
                                        </template>
                                        
                                        <div style="fon-size: 12px;">
                                            <table class="layui-table" lay-size="sm">
                                                <thead>
                                                    <tr>
                                                        <th colspan="2" class="center">
                                                            API调用(次)
                                                        </th>
                                                        <th colspan="2" class="center">
                                                            站内调用(次)
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th width="60" class="center">
                                                            查课
                                                        </th>
                                                        <td width="60">
                                                            {{scope.row.ck}}
                                                        </td>
                                                        <th width="60" class="center">
                                                            查课
                                                        </th>
                                                        <td width="60">
                                                            {{scope.row.ck1}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="center">
                                                            下单
                                                        </th>
                                                        <td>
                                                            {{scope.row.xd}}
                                                        </td>
                                                        <th class="center">
                                                            下单
                                                        </th>
                                                        <td>
                                                            {{scope.row.xd1}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="center">
                                                            进度
                                                        </th>
                                                        <td>
                                                            {{scope.row.jd}}
                                                        </td>
                                                        <th class="center">
                                                            进度
                                                        </th>
                                                        <td>
                                                            {{scope.row.jd1}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="center">
                                                            补刷
                                                        </th>
                                                        <td>
                                                            {{scope.row.bs}}
                                                        </td>
                                                        <th class="center">
                                                            补刷
                                                        </th>
                                                        <td>
                                                            {{scope.row.bs1}}
                                                        </td>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        
                                    </el-popover>
                                </template>
                            </el-table-column>
                            <el-table-column prop="addtime" label="添加时间"  width="180"></el-table-column>
                            <el-table-column prop="endtime" label="最后在线时间"  width="180"></el-table-column>
                            <el-table-column fixed="right" label="主控"  align="center"  width="100">
                                <template #default="scope">
                                    <el-dropdown split-button type="primary" size="small" @click.stop="cz(scope.row.uid,scope.row.name)">
                                        充值

                                        <template #dropdown>
                                            <el-dropdown-menu>
                                                <el-dropdown-item >
                                                    <p style="margin: 0;width: 100%;" @click="ddinfo(scope.row)">
                                                        修改费率(当前:{{scope.row.addprice}})
                                                    </p>
                                                </el-dropdown-item>
                                                <el-dropdown-item :disabled="!!scope.row.key">
                                                    <p v-if="scope.row.key" style="margin: 0;width: 100%;" title="点击复制" @click="copyT(scope.row.key)">
                                                        Key：{{scope.row.key}}(点击复制)
                                                    </p>
                                                    <p v-else style="margin: 0;width: 100%;" @click="ktapi(scope.row.uid)">
                                                        开通Key
                                                    </p>
                                                </el-dropdown-item>
                                                <el-dropdown-item :disabled="!!scope.row.yqm">
                                                    <p v-if="scope.row.yqm" style="margin: 0;width: 100%;" title="点击复制" @click="copyT(scope.row.yqm)">
                                                        邀请码：{{scope.row.yqm}}(点击复制)
                                                    </p>
                                                    <p v-else style="margin: 0;width: 100%;" @click="yqm(scope.row.uid,scope.row.name)">
                                                        设置邀请码
                                                    </p>
                                                </el-dropdown-item>
                                                <el-dropdown-item divided v-if="admin" :disabled="scope.row.uuid === '1'">
                                                    <p v-if="scope.row.uuid === '1'  " style="margin: 0;width: 100%">
                                                        直属代理默认启用
                                                    </p>
                                                    <p v-else style="margin: 0;width: 100%" @click="czAuth(scope.row.uid)">
                                                        在线充值权限：{{scope.row.czAuth==1?'已开启 (点击关闭)':'已关闭 (点击开启)'}}
                                                    </p>
                                                </el-dropdown-item>
                                                <el-dropdown-item divided v-else disabled>
                                                    <p style="margin: 0;width: 100%">
                                                        在线充值权限：{{scope.row.czAuth==1?'已开启':'已关闭'}}
                                                    </p>
                                                </el-dropdown-item>
                                                <el-dropdown-item v-if="admin">
                                                    <p style="margin: 0;width: 100%" @click="jcckxz(scope.row.uid)">解除API限制</p>
                                                </el-dropdown-item>
                                                <el-dropdown-item divided v-if="admin">
                                                    <p style="margin: 0;width: 100%" @click="czmm(scope.row.uid)">重置密码</p>
                                                </el-dropdown-item>
                                                <el-dropdown-item v-if="admin">
                                                    <p style="margin: 0;width: 100%" @click="deluser([scope.row.uid])">删除</p>
                                                </el-dropdown-item>
                                            </el-dropdown-menu>
                                        </template>

                                    </el-dropdown>
                                </template>
                            </el-table-column>
                            
                            
                        </el-table>
                        
                    </div>

                    <div style="float: right;overflow-x:hidden;">
                        <div id="listTable_laypage" style="scale: .8;width: max-content; transform-origin: right center;"></div>
                    </div>

                </div>
            </div>

            <div class="layui-padding-2" id="modal-adduser" style="display: none;">
                <div>

                    <div class="">
                        <div class="modal-body">
                            <form class="form-horizontal  " id="form-adduser">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">昵称</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="layui-input" name="name" placeholder="昵称" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">账号</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="layui-input" name="user" placeholder="必须填QQ号" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">密码</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="layui-input" name="pass" placeholder="默认密码：<?= $conf['user_pass'] ?>"
                                            disabled="" required>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class="col-sm-2 control-label">等级</label>
                                    <div class="col-sm-9 ">
                                        <select class="layui-select" name="addprice"
                                            style="background:url('../index/arrow.png')no-repeat scroll 99%;width:100%"
                                            @change="change_dengji">
                                            <option value="">点我选择等级</option>
                                            <option v-for="row2 in row1.data" :value="row2.rate">{{row2.name}} [费率:{{row2.rate}}]</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer" style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="flex: auto;float: left;fon-size: 12px;text-align: left;scale: .95;">
                                手续费：{{user_ktmoney}}元<br />
                                等级成本：{{this_dengji.rate?this_dengji.money+'（代理到账）':'请先选择等级'}}
                                <br />
                                将扣除您：
                                <template v-if="this_dengji.rate">
                                    {{ (this_dengji.money*(<?= $userrow['addprice'] ?>/this_dengji.rate)).toFixed(2) }} + {{user_ktmoney}}
                                    = {{
                  (this_dengji.money*(<?= $userrow['addprice'] ?>/this_dengji.rate)+parseFloat(user_ktmoney)).toFixed(2)
                  }}
                                </template>
                                <template v-else>
                                    请先选择等级
                                </template>

                            </div>
                            <button type="button" @click="adduser()" class="layui-btn">
                                <i class="layui-icon layui-icon-addition"></i> 添加
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>


        <div class="" id="modal-gjusers" style="display:none">
            <div class="layui-padding-2">

                <div class="modal-body">
                    <form class="form-horizontal" id="form-gjuser">
                        <input type="hidden" name="uid" :value="ddinfo3.info.uid" />
                        <div class="form-group">
                            <div class="col-sm-9">
                                <select class="layui-select" name="addprice"
                                    style="background:url('../index/arrow.png')no-repeat scroll 99%;width:100%">
                                    <option v-for="row2 in row1.data" :value="row2.rate">{{row2.name}} [费率:{{row2.rate}}]</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>

</div>

<script type="text/javascript" src="https://lib.baomitu.com/perfect-scrollbar/1.4.0/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>

<?php include($root . '/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data() {
            return {
                admin: '<?= $userrow['uid'] === '1' ? true : false; ?>',
                row: {
                    data: []
                },
                row_userid: [],
                cx: {
                    pagesize: 15
                },
                type: '1',
                qq: '',
                addprice: '',
                ddinfo3: {
                    status: false,
                    info: []
                },
                row1: '',
                storeInfo: {},
                adduserOpenData: {},
                this_dengji: {
                    money: 0,
                    rate: '',
                },
                sex: [],
                user_ktmoney: <?= json_encode($conf['user_ktmoney']) ?>,
                dlgl_notice_open: <?= json_encode($conf['dlgl_notice_open']) ?>,
                x: 0,
                y: 0,
            }
        },
        mounted() {
            const _this = this;
            layui.use(function() {
                var util = layui.util;
                // 自定义固定条
                util.fixbar({
                    margin: 100
                })

            })
            _this.get_user2id();
            _this.get(1);
            _this.dj();

            layui.form.on('select(search_fenlei_select)', (data) => {
                var elem = data.elem; // 获得 radio 原始 DOM 对象
                var checked = elem.checked; // 获得 radio 选中状态
                var value = elem.value; // 获得 radio 值
                _this.type = value;
                // _this.get(1);
            })
            layui.form.on('select(change_uuid_select)', (data) => {
                var elem = data.elem; // 获得 radio 原始 DOM 对象
                var dataset = elem.dataset;
                var checked = elem.checked; // 获得 radio 选中状态
                var value = elem.value; // 获得 radio 值
                _this.type = value;
                // _this.get(1);
                _this.setuser(JSON.parse(dataset.res),{uuid:value});
            })

        },
        methods: {
            copyT(text=''){
                const _this = this;
                navigator.clipboard.writeText(text).then(function() {
                    _this.$message.success("复制成功：" +text)
                }).catch(function(error) {
                    _this.$message.error('复制失败: ' + error)
                });
            },
            handleDragging({
                target,
                deltaX,
                deltaY
            }) {
                const _this = this;
                _this.x += deltaX;
                _this.y += deltaY;
            },
            dlgl_notice: function() {
                const _this = this;
                let dlgl_notice = <?= json_encode($conf['dlgl_notice']) ?>;
                return dlgl_notice.replace(/\[user_ktmoney\]/g, _this.user_ktmoney);
            },
            setuser: function(res, rows) {
                const _this = this;
                for (let i in rows) {
                    res[i] = rows[i]
                }
                
                delete res.dd;
                delete res.dl_num;
                
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=upuser", {
                    data: res
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.get(_this.row.current_page);
                        // $("#modal-" + form).modal('hide');
                        layer.msg(r.data.msg, {
                            icon: 1
                        });
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            selectAll: function() {
                const _this = this;
                if (_this.sex.length == 0) {
                    for (i = 0; i < vm.row.data.length; i++) {
                        vm.sex.push(_this.row.data[i].uid)
                    }
                    console.log(_this.sex)
                } else {
                    _this.sex = []
                }
            },
            deluser: function(sex) {
                const _this = this;
                if (!sex) {
                    if (!_this.sex.length) {
                        layer.msg("请先选择要删除的代理！");
                        return false;
                    }
                } else {
                    vm.sex = sex;
                    console.log(sex, _this.sex)
                }

                layer.confirm('确定要删除？', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确定', '取消']
                }, function() {
                    var load = layer.load();
                    $.post("/apiadmin.php?act=deluser", {
                        sex: vm.sex
                    }, {
                        emulateJSON: true
                    }).then(function(data) {
                        if (data.code == 1) {
                            vm.selectAll();
                            vm.get(vm.row.current_page);
                            layer.msg(data.msg, {
                                icon: 1
                            });
                            vm.sex = []
                        } else {
                            layer.msg(data.msg, {
                                icon: 2
                            });
                        }
                    })
                })

            },
            change_dengji: function(e) {
                const _this = this;
                _this.this_dengji = {
                    money: 0,
                    rate: '',
                }
                if (e.target.value) {
                    _this.this_dengji = {
                        money: _this.row1.data.find((i) => i.rate === e.target.value).money,
                        rate: e.target.value
                    };
                }
            },
            get_user2id(){
                const _this = this;
                axios.post("/apiadmin.php?act=user2id").then(r=>{
                    if (r.data.code == 1){
                        _this.row_userid = r.data.data;
                        console.log("_this.row_userid",_this.row_userid)
                    }else{
                        
                    }
                })
            },
            get: function(page) {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=userlist", {
                    type: _this.type,
                    qq: _this.qq,
                    page: page,
                    pagesize: _this.cx.pagesize
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.row = r.data;

                        layui.use('table', function() {
                            var laypage = layui.laypage;
                            laypage.render({
                                elem: 'listTable_laypage', // 元素 id
                                count: _this.row.count, // 数据总数
                                limit: _this.row.pagesize,
                                limits: [15, 30, 50, 100],
                                curr: _this.row.current_page,
                                layout: ['count', 'prev', 'page', 'next', 'limit'], // 功能布局
                                prev: '<i class="layui-icon layui-icon-left"></i>',
                                next: '<i class="layui-icon layui-icon-right"></i>',
                                jump: function(obj, first) {
                                    if (!first) {
                                        _this.cx.pagesize = obj.limit;
                                        _this.get(obj.curr, '');
                                    }
                                }
                            });
                        })
                        
                        setTimeout(()=> {
                            layui.form.render();
                        }, 200);

                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                    $("#userlist").ready(() => {
                        $("#userlist").show();
                    })
                });
            },
            dj: function() {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=adddjlist", {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.row1 = r.data;
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            form: function(form) {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=" + form, {
                    data: $("#form-" + form).serialize()
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.get(_this.row.current_page);
                        $("#modal-" + form).modal('hide');
                        layer.msg(r.data.msg, {
                            icon: 1
                        });
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            ddinfo: function(a) {
                const _this = this;
                _this.ddinfo3.info = a;
                layui.use(() => {
                    layer.open({
                        type: 1,
                        title: '修改费率',
                        area: ["350px"],
                        content: $("#modal-gjusers"),
                        btn: ["修改"],
                        yes: function(index) {
                            _this.gj();
                            layer.close(index);
                        }
                    })
                })
            },
            adduserOpen: function() {
                const _this = this;
                layui.use('layer', function() {
                    _this.adduserOpenData = layer.open({
                        title: '添加代理',
                        type: 1,
                        shade: .5, // 不显示遮罩
                        area: ['360px'],
                        content: $('#modal-adduser'), // 捕获的元素
                        end: function() {
                            // layer.msg('关闭后的回调', {icon:6});
                        }
                    });
                })
            },
            adduser: function() {
                const _this = this;
                var load = layer.load(0);
                $.post("/apiadmin.php?act=adduser", {
                    data: $("#form-adduser").serialize()
                }, function(data) {
                    layer.close(load);
                    if (data.code == 1) {
                        layer.confirm(data.msg, {
                            btn: ['确定开通', '取消'],
                            title: '扣费详情' //按钮
                        }, function() {
                            var load = layer.load(0);
                            axios.post("/apiadmin.php?act=adduser", {
                                data: $("#form-adduser").serialize(),
                                type: 1
                            }, {
                                emulateJSON: true
                            }).then(function(r) {
                                layer.close(load);
                                layer.close(_this.adduserOpenData);
                                if (r.data.code == 1) {
                                    layer.closeAll();
                                    vm.get(_this.row.current_page);
                                    layer.alert(r.data.msg, {
                                        icon: 1
                                    });
                                } else {
                                    layer.msg(r.data.msg, {
                                        icon: 2
                                    });
                                }
                            });
                        }, function() {

                        });
                    } else {
                        layer.msg(data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            cz: function(uid, name) {
                layer.prompt({
                    title: '你将为<font color="red">' + name + '</font>充值请输入充值金额',
                    formType: 3
                }, function(money, index) {
                    layer.close(index);
                    var load = layer.load(0);
                    $.post("/apiadmin.php?act=userjk", {
                        uid: uid,
                        money: money
                    }, function(data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, {
                                icon: 1
                            });
                        } else {
                            layer.msg(data.msg, {
                                icon: 2
                            });
                        }
                    });
                });
            },
            kc: function(uid, name) {
                layer.prompt({
                    title: '你将为<font color="red">' + name + '</font>扣款请输入扣除金额',
                    formType: 3
                }, function(money, index) {
                    layer.close(index);
                    var load = layer.load(0);
                    $.post("/apiadmin.php?act=userkc", {
                        uid: uid,
                        money: money
                    }, function(data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, {
                                icon: 1
                            });
                        } else {
                            layer.msg(data.msg, {
                                icon: 2
                            });
                        }
                    });
                });
            },
            gj: function(uid, name) {
                var load = layer.load(0);
                $.post("/apiadmin.php?act=usergj", {
                    data: $("#form-gjuser").serialize()
                }, function(data) {
                    layer.close(load);
                    if (data.code == 1) {
                        layer.confirm(data.msg, {
                            btn: ['确定改价并充值', '取消'],
                            title: '改价扣费' //按钮
                        }, function() {
                            var load = layer.load(0);
                            axios.post("/apiadmin.php?act=usergj", {
                                data: $("#form-gjuser").serialize(),
                                type: 1
                            }, {
                                emulateJSON: true
                            }).then(function(r) {
                                layer.close(load);
                                if (r.data.code == 1) {
                                    vm.get(vm.row.current_page);
                                    layer.alert(r.data.msg, {
                                        icon: 1
                                    });
                                } else {
                                    layer.msg(r.data.msg, {
                                        icon: 2
                                    });
                                }
                            });
                        }, function() {

                        });
                    } else {
                        layer.msg(data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            czmm: function(uid) {
                let loadIndex = layer.load(0);

                layer.confirm(`UID：${uid}`, {
                    closeBtn: 0,
                    title: '确定要重置吗？',
                    btn: ['重置', '算了'] //按钮
                }, function() {
                    layer.load(0);
                    axios.post("/apiadmin.php?act=user_czmm", {
                        uid: uid
                    }, {
                        emulateJSON: true
                    }).then(function(r) {
                        layer.close('loading');
                        if (r.data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.msg(r.data.msg);
                        } else {
                            layer.msg(r.data.msg);
                        }
                    });
                }, function() {
                    layer.closeAll('loading');
                });

            },
            jcckxz: function(uid) {
                layer.confirm(`UID：${uid}`, {
                    closeBtn: 0,
                    title: '确定要解除该代理的API限制吗？',
                    btn: ['解除', '算了'] //按钮
                }, function() {
                    layer.load(0);
                    axios.post("/apiadmin.php?act=jcckxz", {
                        uid: uid
                    }, {
                        emulateJSON: true
                    }).then(function(r) {
                        layer.close('loading');
                        if (r.data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.msg(r.data.msg);
                        } else {
                            layer.msg(r.data.msg);
                        }
                    });
                }, function() {
                    layer.closeAll('loading');
                });
            },
            czAuth(uid) {
                const _this = this;
                layer.load(0);
                axios.post("/apiadmin.php?act=czAuth_user", {
                    uid: uid
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.closeAll('loading');
                    if (r.data.code == 1) {
                        vm.get(vm.row.current_page);
                        _this.$message.success(r.data.msg);
                    } else {
                        _this.$message.error(r.data.msg ? r.data.msg : '失败');
                    }
                });
            },
            ktapi: function(uid) {
                layer.confirm('给下级开通API，将扣除5元余额', {
                    title: '温馨提示',
                    icon: 1,
                    btn: ['确定', '取消'] //按钮
                }, function() {
                    var load = layer.load(0);
                    axios.get("/apiadmin.php?act=ktapi&type=2&uid=" + uid)
                        .then(function(data) {
                            layer.close(load);
                            if (data.data.code == 1) {
                                vm.get(vm.row.current_page);
                                layer.alert(data.data.msg, {
                                    icon: 1,
                                    title: "温馨提示"
                                });
                            } else {
                                layer.msg(data.data.msg, {
                                    icon: 2
                                });
                            }
                        });

                });
            },
            yqm: function(uid, name) {
                layer.prompt({
                    title: '你将为<font color="red">' + name + '</font>设置邀请码，邀请码最低4位数',
                    formType: 3
                }, function(yqm, index) {
                    layer.close(index);
                    var load = layer.load(0);
                    $.post("/apiadmin.php?act=szyqm", {
                        uid,
                        yqm
                    }, function(data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            layer.alert(data.msg, {
                                icon: 1
                            });
                        } else {
                            layer.msg(data.msg, {
                                icon: 2
                            });
                        }
                    });
                });
            },
            ban: function(uid, active) {
                var load = layer.load(0);
                $.post("/apiadmin.php?act=user_ban", {
                    uid,
                    active
                }, function(data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.get(vm.row.current_page);
                        layer.msg(data.msg, {
                            icon: 1
                        });
                    } else {
                        layer.msg(data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            photosView(title="未命名",list = [{}],start=0){
                // alt,pid,src
                layer.photos({
                    photos: {
                      "title": title,
                      "start": start,
                      "data": list
                    }
              });
            }
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#userlist');
    // -----------------------------
</script> 