<?php
include_once ('../../confing/common.php');
include_once ('jscss.php');

// 控制权限
if ($islogin != 1 || $userrow['uid'] != "1") {
  exit("<script language='javascript'>window.location.href='//" . $_SERVER['HTTP_HOST'] . "/index';</script>");
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="/assets/css/buttons.css">
</head>

<style>
    .layui-transfer-header{
        display: flex;
        align-items: center;
    }
    .layui-form-item{
        margin-bottom: 5px;
    }
    .layui-inline{
        margin-bottom: 5px !important;
    }
    .layui-form-label{
        width: max-content;
        text-align: left;
    }
    .layui-form-selected dl{
        /*bottom: auto !important;*/
    }
    
    #saveBox{
        position: fixed;
        bottom: 0;
        left: 0;
        background: #ffffff;
        z-index: 200;
        width: 100%;
    }
    #saveBox .layui-panel{
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 5px;
    }
</style>

<body style="overflow-x: hidden;">
    <div id="djTool" class="layui-padding-2" style="display:none;">
        <!--$(window).width()-->
        <el-row :gutter="10" :style='`margin-bottom:${saveBox_height}px`'>
            
            <el-col :xs="24" :sm="10" style="height: 90vh; overflow-y: auto;overflow-x: auto;">
                <form class="layui-form" action="" lay-filter="huoyuanConfig_form_filter">
                    
                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>接口相关</b>
                        </legend>
                        <div class="layui-field-box">
                    
                            <div class="layui-form-item">
                                <div class="layui-inline" style="width: 100%; display: flex; align-items: center;">
                                    <!--<label class="layui-form-label">Token</label>-->
                                    <div class=" layui-input-wrap" style="flex:auto">
                                        <select lay-append-to="body" name="hid" lay-verify="required" v-model="huoyuanConfig_form.hid" lay-filter="huoyuanConfig_form_huoyuan" lay-search>
                                            <template v-for="(item,index) in huoyuanData.data" :key="index">
                                                <option  :value="item.hid" >【{{item.hid}}】{{item.name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <button type="button" class="layui-btn layui-btn-primary" @click="getHuoyuan()" style="margin-left: 5px;">
                                        <i class="layui-icon layui-icon-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="layui-form-item">
                                <div class="layui-inline" style="width: 100%; display: flex; align-items: center;">
                                    <!--<label class="layui-form-label">Token</label>-->
                                    <div class=" layui-input-wrap" style="flex:auto">
                                        <input :value="huoyuanData.data.find(i=>i.hid == huoyuanConfig_form.hid)?(huoyuanData.data.find(i=>i.hid == huoyuanConfig_form.hid).url?huoyuanData.data.find(i=>i.hid == huoyuanConfig_form.hid).url:'当前接口未配置URL'):'获取中...'" disabled="" class="layui-input">
                                    </div>
                                    <button type="button" class="layui-btn layui-btn-primary layui-bg-blue" @click="getApiClass(huoyuanConfig_form.hid)" style="margin-left: 5px;">
                                        <i class="layui-icon layui-icon-release"></i>获取数据
                                    </button>
                                </div>
                            </div>
                            
                            <el-collapse>
                                <el-collapse-item  name="1">
                                    <template #title>
                                        更多配置
                                        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click.stop="Object.keys(huoyuanConfig_form_defalut).map(i=>{huoyuanConfig_form[i] = huoyuanConfig_form_defalut[i]});formRender()" style="margin-left: 5px;">
                                            恢复默认
                                        </button>
                                    </template>
                                    <div class="layui-form-item">
                                        <div class="layui-inline" style="width: 100%;">
                                            <div class="">对接方式</div>
                                            <div class=" layui-input-wrap">
                                                <select lay-append-to="body" name="postType" lay-verify="required" v-model="huoyuanConfig_form.postType" lay-filter="huoyuanConfig_form_postType" >
                                                    <option  value="1"  >
                                                        POST
                                                    </option>
                                                    <option  value="0" >
                                                        GET
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-inline" style="width: 100%;">
                                            <div class="">接口路径</div>
                                            <div class=" layui-input-wrap">
                                                <input type="text" name="path" v-model="huoyuanConfig_form.path" class="layui-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-inline" style="width: 100%;">
                                            <div class="">接口返回成功码</div>
                                            <div class=" layui-input-wrap">
                                                <input type="text" name="yesCode" v-model="huoyuanConfig_form.yesCode" class="layui-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item">
                                        <div class="layui-inline" style="width: 100%;">
                                            <div class="">接口返回数据字段</div>
                                            <div class=" layui-input-wrap">
                                                <input type="text" name="dataT" v-model="huoyuanConfig_form.dataT" class="layui-input">
                                            </div>
                                        </div>
                                    </div>
                                </el-collapse-item>
                            </el-collapse>
                            
                        </div>
                    </fieldset>
                    
                    <hr />
                    
                </form>
                
                <form class="layui-form" action="" lay-filter="addForm_form_filter">
                    
                    <fieldset class="layui-elem-field">
                        <legend class="layui-font-green" style="width: auto; font-size: 14px; border-bottom: 0px; margin-bottom: 0px;">
                            <b>对接相关</b>
                        </legend>
                        <div class="layui-field-box">
                            
                            <div class="layui-form-item">
                                <div class="layui-inline" style="width: 100%; display: flex; align-items: center;">
                                    <div class="">分类&nbsp;</div>
                                    <div class=" layui-input-wrap" style="flex:auto;">
                                        <select lay-append-to="body" name="fenlei" lay-verify="required" v-model="addForm.fenlei" lay-filter="addForm_form_fenlei" lay-search>
                                            <template v-for="(item,index) in fenleiData.data" :key="index">
                                                <option  :value="item.id" >【{{item.id}}】{{item.name}}</option>
                                            </template>
                                        </select>
                                    </div>
                                    <button type="button" class="layui-btn layui-btn-primary" @click="getFenlei()" style="margin-left: 5px;">
                                        <i class="layui-icon layui-icon-refresh"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="layui-form-item">
                                <div class="layui-inline" style="width: 100%; display: flex; align-items: flex-end;">
                                    
                                    <div class=" layui-input-wrap" style="flex:auto">
                                        <div class="">
                                            加价百分比 <span class="layui-font-12 layui-font-red">修改后随便点页面哪个地方就自动计算</span>
                                        </div>
                                        <div class="layui-input-wrap">
                                            <input type="number" lay-affix="number" min="1" step="20" name="add" v-model="addForm.add" class="layui-input" placeholder="比如：100" @blur="tableRender()" @change="ddForm.add<=0?addForm.add=1:''" lay-filter="addForm_form_add">
                                        </div>
                                    </div>
                                    <span>&nbsp;%</span>
                                </div>
                            </div>
                            
                            <div class="layui-form-item">
                                <div class="layui-inline" style="width: 100%; display: flex; align-items: flex-end;">
                                    
                                    <div class=" layui-input-wrap" style="flex:auto">
                                        <div class="">
                                            对接后商品运算方式
                                        </div>
                                        <div class="layui-input-wrap">
                                            <select lay-append-to="body" name="yunsuan" lay-verify="required" v-model="addForm.yunsuan" lay-filter="addForm_form_yunsuan" >
                                                <option  value="*"  >
                                                    乘法
                                                </option>
                                                <option  value="+" >
                                                    加法
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="layui-form-item">
                                <div class="layui-inline" style="width: 100%; display: flex; align-items: flex-end;">
                                    
                                    <div class=" layui-input-wrap" style="flex:auto">
                                        <div class="">
                                            搜索方案
                                        </div>
                                        <div class="layui-input-wrap">
                                            <select lay-append-to="body" name="search1" lay-verify="required" v-model="addForm.search1" lay-filter="addForm_form_search1" >
                                                <option  value="0"  >
                                                    搜索后清除搜索前勾选的数据
                                                </option>
                                                <option  value="1" >
                                                    搜索后保留搜索前勾选的数据
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="layui-form-item">
                                <div class="layui-inline" style="width: 100%; display: flex; align-items: flex-end;">
                                    
                                    <div class=" layui-input-wrap" style="flex:auto">
                                        <div class="">
                                            对接整理方案
                                        </div>
                                        <div class="layui-input-wrap">
                                            <input type="text" name="premier" v-model="addForm.premier" class="layui-input" placeholder="比如：cid、sort" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                        </div>
                    </fieldset>
                    
                </form>
                
            </el-col>
            
            <el-col :xs="24" :sm="14" style="height: 90vh; overflow-y: auto;overflow-x: auto;">
                
                <div id="tableBoxID">
                    <div>
                        
                        <form class="layui-form layui-row layui-col-space16">
                          <div class="layui-col-md4" style="padding-bottom: 0;">
                            <div class="layui-input-wrap">
                              <input type="text" name="name" value="" placeholder="商品名称" class="layui-input" lay-affix="clear" @keydown.enter="$(`button[lay-filter='table_search_filter']`).click()">
                            </div>
                          </div>
                          <div class="layui-col-md4" style="padding-bottom: 0;">
                            <div class="layui-input-wrap">
                              <input type="text" name="cid" placeholder="商品ID" lay-affix="clear" class="layui-input" @keydown.enter="$(`button[lay-filter='table_search_filter']`).click()">
                            </div>
                          </div>
                          <div class="layui-col-md4" style="padding-bottom: 0;">
                            <div class="layui-input-wrap">
                              <input type="text" name="fenlei" placeholder="分类ID" lay-affix="clear" class="layui-input" @keydown.enter="$(`button[lay-filter='table_search_filter']`).click()">
                            </div>
                          </div>
                          <div class="layui-btn-container layui-col-xs12">
                            <button type="button"  class="layui-btn layui-btn-sm" style="margin-bottom: 0;" lay-submit lay-filter="table_search_filter">搜索</button>
                            <button type="reset" class="layui-btn layui-btn-sm layui-btn-primary" style="margin-bottom: 0;" id="table_search_reset" >清理搜索条件</button>
                            
                            <span class="layui-font-12 layui-font-green" style="margin-left:10px;">模糊搜索</span>
                          </div>
                        </form>
                        <div>
                            共 {{apiClassData.data.length}} 条&nbsp;|&nbsp;
                            <span>
                                上游返回的价格可能是你的成本或者上游的系数，注意核对
                            </span>
                        </div>
                        
                    </div>
                    <table id="apiClassData_table" lay-filter="apiClassData_table" style="display:none;"></table>
                </div>
                
            </el-col>
            
        </el-row>
        
        <div id="saveBox">
            <div class="layui-panel">
                <div>
                    当前加价(<span class="layui-font-red">{{addForm.add}}%</span>)&nbsp;&nbsp;已选择：<span class="layui-font-blue">{{addForm.list.length}}</span> 个
                </div>
                <div>
                    <button type="button" class="layui-btn layui-btn-sm layui-bg-blue animate__animated animate__wobble " @click="saveSubmit()">
                        <i class="layui-icon layui-icon-senior"></i>确认对接
                    </button>
                </div>
            </div>
        </div>
        
    </div>
</body>

</html>

<script id="apiClassData_table_name" type="text/html" >
  <div class="apiClassData_table_name" data-content="{{=d.content?d.content:'暂无说明'}}">
    {{=d.name}}
  </div>
</script>

<script id="apiClassData_table_djstatus" type="text/html" >
    {{# if(d.status2 != '未对接'){ }}
        <div class="apiClassData_table_djstatus" data-status2_cid="{{=d.status2_cid.join(',')}}" style="text-decoration: underline;">
            {{=d.status2}}
        </div>
    {{# } else { }}
        <div >
            {{=d.status2}}
        </div>
    {{# } }}
</script>

<script>
    const app = Vue.createApp({
        data(){
            return{
                hid: '',
                huoyuanConfig_form: {
                    hid: '',
                    postType: '1',
                    path: '/api.php?act=getclass',
                    dataT: 'data',
                    yesCode: '1',
                },
                huoyuanConfig_form_defalut: {
                    postType: '1',
                    path: '/api.php?act=getclass',
                    dataT: 'data',
                    yesCode: '1',
                },
                huoyuanData: {
                    data: [],
                },
                apiClassData: {
                    data: [],
                },
                fenleiData: {
                    data: [],
                },
                addForm: {
                    fenlei: 0,
                    add: 120,
                    list: [],
                    yunsuan: '*',
                    premier: 'cid',
                    search1: 0,// 搜索是否保留已勾选数据方案
                },
            }
        },
        mounted(){
            const _this = this;
            
            let loadIndex = layer.load(0);
            $("#djTool").ready(()=>{
                layer.close(loadIndex);
                setTimeout(()=>{
                    $("#djTool").show();
                },300)
                _this.getHuoyuan();
                _this.getFenlei();
            })
            
            layui.use(()=>{
                
            })
        },
        computed:{
            saveBox_height(){
                return $("#saveBox").height();
            }
        },
        methods: {
            formRender(formFilter){
                const _this = this;
                layui.use(()=>{
                    if(!formFilter){
                        
                    }else{
                        
                    }
                    layui.form.val('huoyuanConfig_form_filter',_this.huoyuanConfig_form);
                    layui.form.val('addForm_form_filter',_this.addForm);
                    setTimeout(()=>{
                        layui.form.render();
                    },0)
                    
                });
            },
            getHuoyuan(){
                const _this = this;
                let loadIndex = layer.load(0);
                axios.post('/api/duijie.php?act=huoyuanGet',{
                    
                },{emulateJSON:true}).then(r=>{
                    layer.close(loadIndex);
                    if(r.data.code == 1){
                        _this.huoyuanData.data = r.data.data;
                        
                        _this.huoyuanConfig_form.hid = r.data.data[0].hid;
                        
                        _this.formRender();
                        
                        _this.getApiClass(_this.huoyuanConfig_form.hid);
                        
                        layui.form.on('select(huoyuanConfig_form_huoyuan)',(data)=>{
                            if(data.value != _this.huoyuanConfig_form.hid){
                                _this.huoyuanConfig_form.hid = data.value;
                                
                                _this.apiClassData.data = [];
                                
                                _this.formRender();
                        
                                _this.getApiClass(_this.huoyuanConfig_form.hid);
                            }
                        })
                        layui.form.on('select(huoyuanConfig_form_postType)',(data)=>{
                            if(data.value != _this.huoyuanConfig_form.postType){
                                _this.huoyuanConfig_form.postType = data.value;
                               
                                _this.formRender();
                        
                            }
                        })
                        
                    }else{
                        layer.msg(r.data.msg?r.data.msg:"异常，请重试！");
                    }
                })
            },
            getApiClass(hid){
                layer.closeAll('msg');
                const _this = this;
                if(!hid){
                    layer.msg('参数不足');
                    return false;
                }
                
                _this.addForm.list = [];
                
                _this.apiClassData.data = [];
                -_this.apiClassData_table_init();
                
                layui.use(()=>{
                    let data = layui.form.val('huoyuanConfig_form_filter');
                    
                    let loadIndex = layer.load(0);
                    axios.post('/api/duijie.php?act=apiClassGet',data,{emulateJSON:true}).then(r=>{
                        layer.close(loadIndex);
                        if(r.data.code == 1){
                            _this.apiClassData.data = r.data.data;
                            _this.apiClassData.data.map(i=>{
                                // if(i.status != 1){
                                    // i.LAY_DISABLED = true;
                                // }
                            })
                            
                            _this.apiClassData_table_init();
                        }else{
                            layer.msg(r.data.msg?r.data.msg:"异常，请重试！");
                        }
                    })
                    
                })
                
            },
            tableRender(){
                let loadIndex = layer.msg('计算中...', {
                    icon: 16,
                    shade: 0.01,
                    time: 0,
                  })
                layui.use(()=>{
                    layui.table.reloadData('apiClassData_tableID');
                    layer.close(loadIndex);
                })  
            },
            apiClassData_table_init(){
                const _this = this;
                let loadIndex = layer.load(0);
                layui.use(()=>{
                    layui.table.render({
                        elem: '#apiClassData_table',
                        id: "apiClassData_tableID",
                        cellExpandedMode: 'tips',
                        width: $("#tableBoxID").width() - 10,
                        data: _this.apiClassData.data,
                        cols: [[
                            {
                              checkbox: true,
                              fixed: 'left' ,
                            },
                            {
                                field: 'cid',
                                title: '云ID',
                                width: 70,
                                align: "center",
                            },
                            {
                                field: 'price',
                                title: '对接系数',
                                width: 100,
                                align: "center",
                                templet: '<span>{{= isNaN((d.price*(vm.addForm.add/100)).toFixed(4))?"上游未返回价格":(d.price*(vm.addForm.add/100)).toFixed(3) }}</span>',
                            },
                            {
                                field: 'name',
                                title: '商品名称',
                                templet: '#apiClassData_table_name',
                                minWidth: 200,
                            },
                            {
                                field: 'fenlei',
                                title: '云分类ID',
                                width: 100,
                                align: "center",
                                templet: '{{= d.fenlei=== undefined ?"上游未返回分类ID":d.fenlei}}',
                            },
                            {
                                field: 'price',
                                title: '云端成本/系数',
                                width: 100,
                                align: "center",
                                templet: '{{= d.price=== undefined ?"上游未返回价格":d.price}}',
                            },
                            {
                                field: 'status2',
                                title: '对接状态',
                                width: 80,
                                align: "center",
                                templet: '#apiClassData_table_djstatus',
                            },
                        ]],
                        even: true,
                        size: 'sm',
                        done: function(res, curr, count){
                            layer.close(loadIndex);
                            
                            let resData = res.data;
                            let that = this.elem.next();
                            
                            $("#table_search_reset").on("click",()=>{
                                let resetLoad = layer.load(0);
                                setTimeout(()=>{
                                    layer.close(resetLoad);
                                    $("button[lay-filter='table_search_filter']").click();
                                },10)
                            })
                            
                            // 本地搜索
                            layui.form.on('submit(table_search_filter)', function(data){
                                
                                let searchLoad = layer.load(0);
                                setTimeout(()=>{
                                    let field = data.field; // 获得表单字段
                                    
                                    resData.forEach(function(item,index) {
                                        let tr = that.find(".layui-table-box tbody tr[data-index='" + index + "']");
                	                   // tr.css("display", "none");
                	                    if (item.name.search(field.name) != -1 && item.cid.search(field.cid) != -1  && item.fenlei.search(field.fenlei) != -1  ) {
                                            tr.css("display", "");
                                        } else {
                                            // tr[0].style.display = 'none';
                                            tr.css("display", "none");
                                        }
                                        
                                    });
                                    
                                    if(_this.addForm.search1 == 1){
                                        
                                    }else{
                                        // 取消搜索前所有勾选的所有数据
                                        _this.addForm.list =[];
                                        layui.table.setRowChecked('apiClassData_tableID', {
                                          index: 'all', // 所有行
                                          checked: false // 此处若设置 true，则表示全选
                                        });
                                    }
                                    
                                    layer.close(searchLoad);
                                },0)
                                // 执行搜索重载
                                return false; // 阻止默认 form 跳转
                              });
                            
                            layui.table.on('checkbox(apiClassData_table)', function(obj){
                                if(obj.checked){
                                    if(obj.type == 'all'){
                                        
                                        let allIndex = [];
                                        resData.forEach(function(item,index) {
                                            let tr = that.find(".layui-table-box tbody tr[data-index='" + index + "']");
                                            if(tr.css('display') != 'none'){
                                                allIndex.push(item);
                                            }
                                            
                                        });
                                        
                                        if(_this.addForm.search1 == 1){
                                            let  allIndex_new= _this.addForm.list.concat(allIndex).filter((obj, index, self) => self.findIndex(o => o.cid === obj.cid) === index);
                                            
                                            layui.table.setRowChecked('apiClassData_tableID', {
                                              index: 'all', // 所有行
                                              checked: false // 此处若设置 true，则表示全选
                                            });
                                            // 保留搜索前已勾选数据
                                            layui.table.setRowChecked('apiClassData_tableID', {
                                              index: allIndex_new.map(i=>i.LAY_INDEX),
                                            });
                                            _this.addForm.list = allIndex_new;
                                        }else{
                                            
                                            // 如果有就全部取消
                                            if(_this.addForm.list.length){
                                                layui.table.setRowChecked('apiClassData_tableID', {
                                                  index: 'all', // 所有行
                                                  checked: false // 此处若设置 true，则表示全选
                                                });
                                                _this.addForm.list = [];
                                                return
                                            }
                                            // 不保留搜索前已勾选数据
                                            
                                            layui.table.setRowChecked('apiClassData_tableID', {
                                              index: 'all', // 所有行
                                              checked: false // 此处若设置 true，则表示全选
                                            });
                                            layui.table.setRowChecked('apiClassData_tableID', {
                                              index: allIndex.map(i=>i.LAY_INDEX),
                                            });
                                            _this.addForm.list = allIndex;
                                        }
                                        
                                    }else{
                                        console.log(obj)
                                        _this.addForm.list.push(obj.dataCache)
                                    }
                                }else{
                                    if(obj.type == 'all'){
                                        layui.table.setRowChecked('apiClassData_tableID', {
                                          index: 'all', // 所有行
                                          checked: false // 此处若设置 true，则表示全选
                                        });
                                        _this.addForm.list = [];
                                    }else{
                                        _this.addForm.list =  _this.addForm.list.filter(item => item.cid != obj.dataCache.cid)
                                    }
                                     
                                }
                                
                            });
                            
                            $(".apiClassData_table_name").on('mouseenter', function(){
                                var elem = this;
                                layer.tips("<div style='word-wrap: break-word;'>"+elem.dataset.content.trim()+"</div>", elem,{
                                    tips: [1,'#16b777'],
                                    time: 0,
                                }); //在元素的事件回调体中，follow直接赋予this即可
                            });
                            $(".apiClassData_table_name").on('mouseleave', function(){
                                layer.closeAll('tips');
                            });
                            
                            $(".apiClassData_table_djstatus").on('mouseenter', function(){
                                var elem = this;
                                layer.tips("<div style='word-wrap: break-word;'>已对接本商品的本地商品ID ↓<hr style='margin: 0;' />"+elem.dataset.status2_cid+"</div>", elem,{
                                    tips: [3,'#16b777'],
                                    time: 0,
                                }); //在元素的事件回调体中，follow直接赋予this即可
                            });
                            $(".apiClassData_table_djstatus").on('mouseleave', function(){
                                // layer.closeAll('tips');
                            });
                            
                        }
                        
                    })
  
                })
            },
            getFenlei(){
                const _this = this;
                
                layui.use(()=>{
                    
                    let loadIndex = layer.load(0);
                    axios.post('/api/duijie.php?act=fenleiGet',{
                        
                    },{emulateJSON:true}).then(r=>{
                        layer.close(loadIndex);
                        if(r.data.code == 1){
                            _this.fenleiData.data = r.data.data;
                            
                            _this.addForm.fenlei = r.data.data[0].id;
                            _this.formRender();
                            
                            layui.form.on('select(addForm_form_fenlei)',(data)=>{
                                if(data.value != _this.addForm.fenlei){
                                    _this.addForm.fenlei = data.value;
                                    _this.formRender();
                            
                                    // _this.getApiClass(_this.huoyuanConfig_form.hid);
                                }
                            })
                            
                            layui.form.on('select(addForm_form_yunsuan)',(data)=>{
                                if(data.value != _this.addForm.yunsuan){
                                    _this.addForm.yunsuan = data.value;
                                    _this.formRender();
                            
                                    // _this.getApiClass(_this.huoyuanConfig_form.hid);
                                }
                            })
                            
                            layui.form.on('select(addForm_form_search1)',(data)=>{
                                if(data.value != _this.addForm.search1){
                                    _this.addForm.search1 = data.value;
                                    _this.formRender();
                            
                                    // _this.getApiClass(_this.huoyuanConfig_form.hid);
                                }
                            })
                            
                            layui.form.on('input-affix(addForm_form_add)',(data)=>{
                                let elem = data.elem;
                                _this.addForm.add = elem.value;
                                _this.tableRender();
                            })
                            
                        }else{
                            layer.msg(r.data.msg?r.data.msg:"异常，请重试！");
                        }
                    })
                    
                })
                
            },
            saveSubmit(){
                const _this = this;
                if(!_this.addForm.list.length){
                    layer.msg("请先选择需要对接的商品！");
                    return false;
                }
                
                if(_this.addForm.add < 0){
                    layer.msg("加价百分比必须大于1%");
                    return false;
                }
                
                let loadIndex = layer.load(0);
                _this.addForm.list.sort(function(a, b) {
                  return b[_this.addForm.premier] - a[_this.addForm.premier];
                });
                
                axios.post("/api/duijie.php?act=save",{
                    fenlei:_this.addForm.fenlei,
                    add: _this.addForm.add,
                    hid: _this.huoyuanConfig_form.hid,
                    yunsuan:  _this.addForm.yunsuan,
                    list: JSON.stringify(_this.addForm.list),
                },{emulateJSON:true}).then(r=>{
                    layer.close(loadIndex);
                    if(r.data.code == 1){
                        layer.msg(`成功 ${r.data.okNum} 个，失败 ${r.data.errorNum} 个`);
                    }else{
                        layer.msg(r.data.msg?r.data.msg:"异常，请重试！");
                    }
                })
            },
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#djTool');
    // -----------------------------
</script>