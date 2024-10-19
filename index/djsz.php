<?php
$title = '货源对接';
require_once('head.php');
if ($userrow['uid'] != 1) {
    exit("<script language='javascript'>window.location.href='login.php';</script>");
}
?>

<style>
    .layui-input-group{
        font-size: 12px;
    }
    
    .layui-input-block {
        margin-left: 135px !important;
    }

    #modal_add .layui-input-group .layui-form-label {
        padding: 10px 0 0;
        min-width: 40px;
        width: auto;
        margin: 0 5px 0 0;
    }
    
    .layui-elem-field{
        padding-left: 5px;
    }
    
    .el-collapse-item__header{
        font-size: inherit;
    }
</style>

<style>
    #ID_templateStatu .layui-form-radioed {
        border: 1px solid #16b777;
    }

    #ID_templateStatu .layui-form-radio {
        border: 1px solid #bbbbbb;
        padding: 5px 10px;
        border-radius: 5px;
        display: flex;
        margin-bottom: 5px;
    }
    
    .layui-field-box{
        padding: 0 10px 0 0 ;
    }
</style>

<div class="layui-padding-1" id="orderlist" style="display:none">
    
    <div class="layui-panel">

        <div class="layui-card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center;">
                货源对接&nbsp;
                <button type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="get(1)"><i class="layui-icon layui-icon-refresh"></i></button>&nbsp;&nbsp;&nbsp;
                <button type="button" class="layui-btn layui-bg-blue layui-btn-sm" @click="modal_add_open()">
                    <i class="layui-icon layui-icon-addition"></i>添加
                </button>
                <button type="button" class="layui-btn layui-bg-red layui-btn-sm" @click='del($refs.listTable.getSelectionRows().map(i=>i.hid))'>
                    <i class="layui-icon layui-icon-delete"></i>批量删除
                </button>
            </div>
            <div>
                <button type="button" class="layui-btn layui-bg-blue layui-btn-sm" @click="yjdj2()">
                    <i class="layui-icon layui-icon-util"></i>高级对接工具
                </button>
            </div>
            
        </div>
        
        <div class="layui-card-body layui-padding-2">
            <div class="table-responsive" style="overflow: auto;">
                
                <el-table ref="listTable" :data="row.data" stripe border show-overflow-tooltip empty-text="无货源，请添加" size="small" style="width: 100%">
                    
                    <el-table-column type="selection" width="28" align="center" ></el-table-column>
                    <el-table-column prop="hid" label="操作" width="100" align="center" >
                        <template #default="scope">
                            <el-dropdown split-button type="primary" size="small" @click.stop="modal_add_open(1,scope.row)">
                                编辑
                                <template #dropdown>
                                    <el-dropdown-menu>
                                        <el-dropdown-item>
                                            <p style="margin: 0;" @click="del([scope.row.hid])">删除</p>
                                        </el-dropdown-item>
                                        <el-dropdown-item>
                                            <p style="margin: 0;" @click="yjdj(scope.row.hid)">旧版对接</p>
                                        </el-dropdown-item>
                                    </el-dropdown-menu>
                                </template>
                            </el-dropdown>
                        </template>
                    </el-table-column>
                    <el-table-column prop="hid" label="ID" width="40" align="center" ></el-table-column>
                    <el-table-column prop="name" label="名称" width="80" ></el-table-column>
                    <el-table-column prop="money" label="余额" width="70" align="center" >
                         <template #default="scope">
                            <span v-if="scope.row.money == null" class="layui-font-green">
                                检测中...
                            </span>
                            <span v-else-if="scope.row.money == -999999" class="layui-font-red">
                                检测失败
                            </span>
                            <span v-else>
                                {{scope.row.money}}
                            </span>
                         </template>
                    </el-table-column>
                    <el-table-column prop="order_num" label="单量" width="70" >
                        <template #default="scope">
                            <span v-if="scope.row.order_num>0">
                                {{scope.row.order_num}}
                            </span>
                            <span v-else class="layui-font-green">
                                暂无
                            </span>
                        </template>
                    </el-table-column>
                    <el-table-column prop="user" label="账号/UID" width="70" ></el-table-column>
                    <el-table-column prop="pass" label="密码/Key" width="150" ></el-table-column>
                    <el-table-column prop="url" label="对接网址" width="170" ></el-table-column>
                    <el-table-column prop="token" label="密钥/Token" width="150" ></el-table-column>
                    <el-table-column prop="addtime" label="添加时间" width="180" ></el-table-column>
                    <el-table-column prop="endtime" label="修改时间" width="180" ></el-table-column>
                    
                </el-table>
                
            </div>

            <ul class="pagination" v-if="row.last_page>1"><!--by 青卡 Vue分页 -->
                <li class="disabled"><a @click="get(1)">首页</a></li>
                <li class="disabled"><a @click="row.current_page>1?get(row.current_page-1):''">&laquo;</a></li>
                <li @click="get(row.current_page-3)" v-if="row.current_page-3>=1"><a>{{ row.current_page-3 }}</a></li>
                <li @click="get(row.current_page-2)" v-if="row.current_page-2>=1"><a>{{ row.current_page-2 }}</a></li>
                <li @click="get(row.current_page-1)" v-if="row.current_page-1>=1"><a>{{ row.current_page-1 }}</a></li>
                <li :class="{'active':row.current_page==row.current_page}" @click="get(row.current_page)" v-if="row.current_page"><a>{{ row.current_page }}</a></li>
                <li @click="get(row.current_page+1)" v-if="row.current_page+1<=row.last_page"><a>{{ row.current_page+1 }}</a></li>
                <li @click="get(row.current_page+2)" v-if="row.current_page+2<=row.last_page"><a>{{ row.current_page+2 }}</a></li>
                <li @click="get(row.current_page+3)" v-if="row.current_page+3<=row.last_page"><a>{{ row.current_page+3 }}</a></li>
                <li class="disabled"><a @click="row.last_page>row.current_page?get(row.current_page+1):''">&raquo;</a></li>
                <li class="disabled"><a @click="get(row.last_page)">尾页</a></li>
            </ul>
        </div>

        <div class="" id="modal_add" style="display:none;">
            <div style="padding:10px 10px 0px;">
                <div>
                    <el-button size="small" @click="parse_djCode_open">
                        智能解析工具
                    </el-button>
                </div>
                <div class="">
                    <form class="layui-form" id="form-add" lay-filter="form-add">
                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                            <label class="layui-form-label">名称</label>
                            <div class="layui-input-block">
                                <input name="name" v-model="storeInfo.name" :value="storeInfo.name" type="text" placeholder="请输入名称" class="layui-input" lay-affix="clear">
                            </div>
                        </div>
                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;padding: 0 0 0 45px;scale: .9;">
                            <input lay-filter="templateStatu-radio-filter" type="radio" name="templateStatu" value="1" title="默认" checked>
                            <input lay-filter="templateStatu-radio-filter" type="radio" name="templateStatu" value="2" title="自定义">
                            <!--<input lay-filter="templateStatu-radio-filter" type="radio" name="templateStatu" value="3" title="选择模板">-->
                        </div>
                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                            <label class="layui-form-label">网址</label>
                            <div class="layui-input-block">
                                <input name="url" v-model="storeInfo.url" :value="storeInfo.url" type="text" placeholder="例：http(s)://域名/" class="layui-input" @input="inputReplace('storeInfo.url')" lay-affix="clear">
                            </div>
                        </div>
                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                            <label class="layui-form-label">账号</label>
                            <div class="layui-input-block">
                                <input name="user" v-model="storeInfo.user" :value="storeInfo.user" type="text" placeholder="请输入账号，一般是UID" class="layui-input" @input="inputReplace('storeInfo.user')" lay-affix="clear">
                            </div>
                        </div>
                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                            <label class="layui-form-label">密码</label>
                            <div class="layui-input-block">
                                <input name="pass" v-model="storeInfo.pass" :value="storeInfo.pass" type="text" placeholder="一般是KEY" class="layui-input" @input="inputReplace('storeInfo.pass')" lay-affix="clear">
                            </div>
                        </div>
                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                            <label class="layui-form-label">Token</label>
                            <div class="layui-input-block">
                                <input name="token" v-model="storeInfo.token" :value="storeInfo.token" type="text" placeholder="请输入Token，可为空" class="layui-input" @input="inputReplace('storeInfo.token')" lay-affix="clear"> 
                            </div>
                        </div>
                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                            <label class="layui-form-label">余额提醒值</label>
                            <div class="layui-input-block">
                                <input name="smtp_money" v-model="storeInfo.smtp_money" :value="storeInfo.smtp_money" type="text" placeholder="请输入余额提醒值" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-font-12 layui-font-red">
                            当该货源的余额低于这个值时会发送余额提醒邮件
                        </div>
                        <hr />
                        <div class="layui-font-12">
                            <p class="layui-font-red">下面是对接接口和参数配置，一般默认即可！</p>
                            <p class="layui-font-green">$a是选中订单的货源数据，$b是选中订单的数据</p>
                        </div>
                        <!--<div class="layui-input-group" style="margin: 10px 0;width: 100%;">-->
                        <!--    <label class="layui-form-label">请求方式</label>-->
                        <!--    <div class="layui-input-block">-->
                        <!--        <select name="post" class="layui-select" v-model="storeInfo.post">-->
                        <!--            <option value="1">POST</option>-->
                        <!--            <option value="0">GET</option>-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        <fieldset class="layui-elem-field layui-font-12">
                            <legend class="layui-font-12">查课</legend>
                            <div class="layui-field-box">
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">接口</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="ckjk" v-model="storeInfo.ckjk" :value="storeInfo.ckjk" class="layui-input" placeholder="例：/api.php?act=get" lay-affix="clear" @input="inputReplace('storeInfo.ckjk')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">请求方式</label>
                                    <div class="layui-input-block">
                                        <select name="ck_post" class="layui-select" v-model="storeInfo.ck_post">
                                            <option value="1">POST</option>
                                            <option value="0">GET</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">参数</label>
                                    <div class="layui-input-block">
                                        <textarea type="textarea" name="ckcs" v-model="storeInfo.ckcs" :value="storeInfo.ckcs" class="layui-textarea" :placeholder='`例：\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid"`' rows="3" lay-affix="clear"></textarea>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">成功Code</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="ck_okcode" v-model="storeInfo.ck_okcode" :value="storeInfo.ck_okcode" class="layui-input" placeholder="例：1" lay-affix="clear" @input="inputReplace('storeInfo.ck_okcode')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">数据键</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="ck_datakey" v-model="storeInfo.ck_datakey" :value="storeInfo.ck_datakey" class="layui-input" placeholder="例：data" lay-affix="clear" @input="inputReplace('storeInfo.ck_datakey')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">课程名称键</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="ck_kcnamekey" v-model="storeInfo.ck_kcnamekey" :value="storeInfo.ck_kcnamekey" class="layui-input" placeholder="例：name" lay-affix="clear" @input="inputReplace('storeInfo.ck_kcnamekey')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">课程ID键</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="ck_kcidkey" v-model="storeInfo.ck_kcidkey" :value="storeInfo.ck_kcidkey" class="layui-input" placeholder="例：id" lay-affix="clear" @input="inputReplace('storeInfo.ck_kcidkey')">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="layui-elem-field layui-font-12">
                            <legend class="layui-font-12">下单</legend>
                            <div class="layui-field-box">
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">接口</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="xdjk" v-model="storeInfo.xdjk" :value="storeInfo.xdjk" class="layui-input" placeholder="例：/api.php?act=add" lay-affix="clear"  @input="inputReplace('storeInfo.xdjk')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">请求方式</label>
                                    <div class="layui-input-block">
                                        <select name="xd_post" class="layui-select" v-model="storeInfo.xd_post">
                                            <option value="1">POST</option>
                                            <option value="0">GET</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">参数</label>
                                    <div class="layui-input-block">
                                        <textarea type="textarea" name="xdcs" v-model="storeInfo.xdcs" :value="storeInfo.xdcs" class="layui-textarea" :placeholder='`例：\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid"`' rows="3" lay-affix="clear"></textarea>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">成功Code</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="xd_okcode" v-model="storeInfo.xd_okcode" :value="storeInfo.xd_okcode" class="layui-input" placeholder="例：1" lay-affix="clear" @input="inputReplace('storeInfo.xd_okcode')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">YID键</label>
                                    <div class="layui-input-block">
                                        <div>
                                            <input type="text" name="xd_yidkey" v-model="storeInfo.xd_yidkey" :value="storeInfo.xd_yidkey" class="layui-input" placeholder="例：id" lay-affix="clear" @input="inputReplace('storeInfo.xd_yidkey')">
                                            <div class="layui-font-12">
                                                注：即对接下单后上游在接口中返回的上游订单ID键
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="layui-elem-field layui-font-12">
                            <legend class="layui-font-12">进度同步</legend>
                            <div class="layui-field-box">
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">接口</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="jdjk" v-model="storeInfo.jdjk" :value="storeInfo.jdjk" class="layui-input" placeholder="例：/api.php?act=chadan" lay-affix="clear"  @input="inputReplace('storeInfo.jdjk')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">请求方式</label>
                                    <div class="layui-input-block">
                                        <select name="jd_post" class="layui-select" v-model="storeInfo.jd_post">
                                            <option value="1">POST</option>
                                            <option value="0">GET</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">参数</label>
                                    <div class="layui-input-block">
                                        <textarea type="textarea" name="jdcs" v-model="storeInfo.jdcs" :value="storeInfo.jdcs" class="layui-textarea" :placeholder='`例：\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid"`' rows="3" lay-affix="clear"></textarea>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">成功Code</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="jd_okcode" v-model="storeInfo.jd_okcode" :value="storeInfo.jd_okcode" class="layui-input" placeholder="例：1" lay-affix="clear" @input="inputReplace('storeInfo.jd_okcode')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">数据键</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="jd_datakey" v-model="storeInfo.jd_datakey" :value="storeInfo.jd_datakey" class="layui-input" placeholder="例：data" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <el-collapse>
                                        <el-collapse-item class="layui-font-12">
                                            <template #title>
                                                <el-icon><Key /></el-icon> 点击自定义上游返回的数据中的参数键
                                            </template>
                                            
                                            <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                                <label class="layui-form-label">课程名称</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="jd_datakey_kcname" v-model="storeInfo.jd_datakey_kcname" :value="storeInfo.jd_datakey_kcname" class="layui-input" placeholder="例：kcname" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey_kcname')">
                                                </div>
                                            </div>
                                            <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                                <label class="layui-form-label">状态</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="jd_datakey_status" v-model="storeInfo.jd_datakey_status" :value="storeInfo.jd_datakey_status" class="layui-input" placeholder="例：status" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey_status')">
                                                </div>
                                            </div>
                                            <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                                <label class="layui-form-label">进度</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="jd_datakey_process" v-model="storeInfo.jd_datakey_process" :value="storeInfo.jd_datakey_process" class="layui-input" placeholder="例：process" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey_process')">
                                                </div>
                                            </div>
                                            <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                                <label class="layui-form-label">日志</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="jd_datakey_remarks" v-model="storeInfo.jd_datakey_remarks" :value="storeInfo.jd_datakey_remarks" class="layui-input" placeholder="例：remarks" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey_remarks')">
                                                </div>
                                            </div>
                                            <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                                <label class="layui-form-label">课程开始时间</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="jd_datakey_kcks" v-model="storeInfo.jd_datakey_kcks" :value="storeInfo.jd_datakey_kcks" class="layui-input" placeholder="例：courseStartTime" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey_kcks')">
                                                </div>
                                            </div>
                                            <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                                <label class="layui-form-label">课程结束时间</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="jd_datakey_kcjs" v-model="storeInfo.jd_datakey_kcjs" :value="storeInfo.jd_datakey_kcjs" class="layui-input" placeholder="例：courseEndTime" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey_kcjs')">
                                                </div>
                                            </div>
                                            <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                                <label class="layui-form-label">考试开始时间</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="jd_datakey_ksks" v-model="storeInfo.jd_datakey_ksks" :value="storeInfo.jd_datakey_ksks" class="layui-input" placeholder="例：examStartTime" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey_ksks')">
                                                </div>
                                            </div>
                                            <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                                <label class="layui-form-label">考试结束时间</label>
                                                <div class="layui-input-block">
                                                    <input type="text" name="jd_datakey_ksjs" v-model="storeInfo.jd_datakey_ksjs" :value="storeInfo.jd_datakey_ksjs" class="layui-input" placeholder="例：examEndTime" lay-affix="clear" @input="inputReplace('storeInfo.jd_datakey_ksjs')">
                                                </div>
                                            </div>
                                            
                                        </el-collapse-item>
                                    </el-collapse>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="layui-elem-field layui-font-12">
                            <legend class="layui-font-12">补刷</legend>
                            <div class="layui-field-box">
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">接口</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="bsjk" v-model="storeInfo.bsjk" :value="storeInfo.bsjk" class="layui-input" placeholder="例：/api.php?act=budan" lay-affix="clear" @input="inputReplace('storeInfo.bsjk')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">请求方式</label>
                                    <div class="layui-input-block">
                                        <select name="bs_post" class="layui-select" v-model="storeInfo.bs_post">
                                            <option value="1">POST</option>
                                            <option value="0">GET</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">参数</label>
                                    <div class="layui-input-block">
                                        <textarea type="textarea" name="bscs" v-model="storeInfo.bscs" :value="storeInfo.bscs" class="layui-textarea" :placeholder='`例：\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid"`' rows="3" lay-affix="clear"></textarea>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">成功Code</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="bs_okcode" v-model="storeInfo.bs_okcode" :value="storeInfo.bs_okcode" class="layui-input" placeholder="例：1" lay-affix="clear" @input="inputReplace('storeInfo.bs_okcode')">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        
                        <fieldset class="layui-elem-field layui-field-title">
                          <legend class="layui-font-12">扩展</legend>
                        </fieldset>
                        
                        <fieldset class="layui-elem-field layui-font-12">
                            <legend class="layui-font-12">改密</legend>
                            <div class="layui-field-box">
                                
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">请求方式</label>
                                    <div class="layui-input-block">
                                        <select name="changePass_type" class="layui-select" v-model="storeInfo.changePass_type">
                                            <option value="1">POST</option>
                                            <option value="0">GET</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">改密接口</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="changePass_jk" v-model="storeInfo.changePass_jk" :value="storeInfo.changePass_jk" class="layui-input" placeholder="例：/api.php?act=passwordeee" lay-affix="clear" @input="inputReplace('storeInfo.changePass_jk')">
                                    </div>
                                </div>
                                <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                                    <label class="layui-form-label">改密参数</label>
                                    <div class="layui-input-block">
                                        <textarea type="textarea" name="changePass_cs" v-model="storeInfo.changePass_cs" :value="storeInfo.changePass_cs" class="layui-textarea" :placeholder='`例：\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid"`' lay-affix="clear"></textarea>
                                    </div>
                                </div>
                                
                            </div>
                        </fieldset>
                        
                        <!---->
                        
                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                            <label class="layui-form-label">Cookie</label>
                            <div class="layui-input-block">
                                <textarea name="cookie" v-model="storeInfo.cookie" :value="storeInfo.cookie" placeholder="没必要，不用输入" class="layui-textarea" rows="3" lay-affix="clear"></textarea>
                            </div>
                        </div>

                        <div class="layui-input-group" style="margin: 10px 0;width: 100%;">
                            <label class="layui-form-label">模拟IP</label>
                            <div class="layui-input-block">
                                <input name="ip" v-model="storeInfo.ip" :value="storeInfo.ip" type="text" placeholder="模拟指定IP，留空即可" class="layui-input" lay-affix="clear" @input="inputReplace('storeInfo.ip')">
                            </div>
                        </div>
                        <button style="display:none" type="button" class="layui-btn layui-btn-normal" id="form-add-get">取值</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="ID_templateStatu" style="display:none;">

            <div class="layui-form layui-padding-2">
                <p class="layui-font-12 layui-font-red">
                    若您需要适配您的系统，请联系授权商！
                    <br />
                    注：不提供货源Url，请勿使用违法链接！！！
                </p>
                <hr />
                <el-row :gutter="5">
                    <el-col :xs="12" :sm="12" v-for="(item,index) in storeInfo2.other" :key="index">
                        <input :id="'ID_templateStatu'+index" type="radio" v-model="ID_templateStatu_num" name="radio1" :value="index" :disabled="item.disabled" lay-skin="none" lay-filter="ID_templateStatu-filter">
                        <div lay-radio class="lay-skin-checkcard lay-check-dot-2" style="height: 50px">
                            <p>
                                {{item.name}}
                                <span v-if="item.hot" style="height: 22px; line-height: 20px; padding: 0px 4px; font-size: 12px; width: 22px; scale: .8; position: relative; top: -5px; left: -2px;">
                                    🔥
                                </span >
                                <el-tag v-if="item.tuijian" effect="dark" style="height: 22px; line-height: 20px; padding: 0px 4px; font-size: 12px; width: 22px; scale: .8; position: relative; top: -5px; left: -10px;">荐</el-tag>
                            </p>
                            <p class="layui-font-12 layui-font-green" style="line-height: normal;">{{item.tips?item.tips:'不提供货源Url'}}</p>
                        </div>
                    </el-col>
                </el-row>

            </div>

        </div>

        <div id="djID" style="display: none;">

            <!--{-->
            <!--    hid:'',-->
            <!--    category:'',-->
            <!--    pricee:'',-->
            <!--}-->
            <form class="layui-form layui-padding-2" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:95px">对接台分类ID</label>
                    <div class="layui-input-block">
                        <input type="text" name="category" v-model="djData.category" placeholder="请输入对接台的分类ID" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:95px">增加百分比</label>
                    <div class="layui-input-block">
                        <input type="number" name="pricee" v-model="djData.pricee" lay-affix="clear" placeholder="比如1.05 就是增加5%" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label" style="width:95px">本地分类ID</label>
                    <div class="layui-input-block">
                        <input type="text" name="fid" v-model="djData.fid" lay-affix="clear" placeholder="请输入本站分类ID" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-font-12 layui-font-red">
                    注意：若本地分类ID填写错误，将自动生成一个分类！
                </div>
            </form>
        </div>

    </div>
    
    <div class="layui-padding-1" style="display: none;" id="parse_djCode">
        <el-input class="layui-font-12" v-model="djCode" type="textarea" :rows="4" :show-word-limit="true" :placeholder='`请粘贴单个接口的对接代码，如：\r\nif ($type == "toc") {
    $data = array("uid" => $a["user"], "key" => $a["pass"], "school" => $school, "user" => $user, "pass" => $pass, "platform" => $noun, "kcid" => $kcid);
    $dx_rl = $a["url"];
    $dx_url = "$dx_rl/api.php?act=get";
    $result = get_url($dx_url, $data);
    $result = json_decode($result, true);
    return $result;
}`'></el-input>
        <el-button style="margin-top: 3px;float: right;" @click="parse_djCode(djCode)">
            开始解析
        </el-button>
        <hr />
        <templete v-if="djCode_data.url || djCode_data.data">
            <el-descriptions :column="1" :border="true" size="small">
                <el-descriptions-item label="接口" style="position: relative;">
                    {{djCode_data.url}}
                    <el-button text size="small" title="点击复制" style="position: absolute; right: 0; top: 0;" @click="copyT(djCode_data.url)">
                        <el-icon :size="12"><Document-Copy /></el-icon>
                    </el-button>
                </el-descriptions-item>
                <el-descriptions-item label="参数">
                    <span v-html="djCode_data.data" style="white-space: pre-wrap;">
                    </span>
                    <el-button text size="small" title="点击复制" style="position: absolute; right: 0; top: 0;" @click="copyT(djCode_data.data)">
                        <el-icon :size="12"><Document-Copy /></el-icon>
                    </el-button>
                </el-descriptions-item>
            </el-descriptions>
        </templete>
    </div>

</div>

<?php include($root.'/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data(){
            return{
                listTable: null,
                row: {
                    data: [],
                },
                storeInfo: {
                    
                    ckjk: "",
                    xdjk: "",
                    jdjk: "",
                    bsjk: "",
                    ckcs: ``,
                    xdcs: ``,
                    jdcs: ``,
                    bscs: ``,
                    ck_post: `1`,
                    xd_post: `1`,
                    jd_post: `1`,
                    bs_post: `1`,
                    ck_okcode: `1`,
                    xd_okcode: `0`,
                    jd_okcode: `1`,
                    bs_okcode: `1`,
                    ck_datakey: 'data',
                    ck_kcnamekey: 'name',
                    ck_kcidkey: 'id',
                    xd_yidkey: 'id',
                    jd_datakey: 'data',
                    jd_datakey_kcname: 'kcname',
                    jd_datakey_status: 'status',
                    jd_datakey_process: 'process',
                    jd_datakey_remarks: 'remarks',
                    jd_datakey_kcks: 'courseStartTime',
                    jd_datakey_kcjs: 'courseEndTime',
                    jd_datakey_ksks: 'examStartTime',
                    jd_datakey_ksjs: 'examEndTime',
                    
                    changePass_jk: "",
                    changePass_cs: ``,
                    changePass_type: `1`,
                    smtp_money: 15,
                },
                storeInfo2: {
                    default: {
                        name: '',
                        post: '1',
                        
                        ckjk: "/api.php?act=get",
                        xdjk: "/api.php?act=add",
                        jdjk: "/api.php?act=chadan",
                        bsjk: "/api.php?act=budan",
                        ckcs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"platform" => $noun,\r\n"kcid" => $kcid`,
                        xdcs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"platform" => $noun,\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"kcname" => $kcname,\r\n"kcid" => $kcid,\r\n"miaoshua" => $miaoshua,`,
                        jdcs: `"username" => $user,\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid`,
                        bscs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid`,
                        ck_post: `1`,
                        xd_post: `1`,
                        jd_post: `1`,
                        bs_post: `1`,
                        ck_okcode: `1`,
                        xd_okcode: `0`,
                        jd_okcode: `1`,
                        bs_okcode: `1`,
                        ck_datakey: 'data',
                        ck_kcnamekey: 'name',
                        ck_kcidkey: 'id',
                        xd_yidkey: 'id',
                        jd_datakey: 'data',
                        jd_datakey_kcname: 'kcname',
                        jd_datakey_status: 'status',
                        jd_datakey_process: 'process',
                        jd_datakey_remarks: 'remarks',
                        jd_datakey_kcks: 'courseStartTime',
                        jd_datakey_kcjs: 'courseEndTime',
                        jd_datakey_ksks: 'examStartTime',
                        jd_datakey_ksjs: 'examEndTime',
                    
                        changePass_jk: "",
                        changePass_cs: ``,
                        changePass_type: `1`,
                        smtp_money: 15,
                    },
                    other: [{
                        name: '同款TOC',
                        tips: '同款TOC模板专用',
                        data: {
                            name: '同款TOC',
                            url: "",
                            post: 1,
                            ckjk: "/api.php?act=get",
                            xdjk: "/api.php?act=add",
                            jdjk: "/api.php?act=chadan",
                            bsjk: "/api.php?act=budan",
                            ckcs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"platform" => $noun,\r\n"kcid" => $kcid`,
                            xdcs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"platform" => $noun,\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"kcname" => $kcname,\r\n"kcid"=>$kcid`,
                            jdcs: `"username" => $user,\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid`,
                            bscs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid`
                        }
                    },{
                        name: '流年',
                        tips: '',
                        hot: 1,
                        data: {
                            name: '流年',
                            url: "",
                            post: 1,
                            ckjk: "/api.php?act=get",
                            xdjk: "/api.php?act=add",
                            jdjk: "/api.php?act=chadan",
                            bsjk: "/api.php?act=budan",
                            ckcs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"platform" => $noun,\r\n"kcid" => $kcid`,
                            xdcs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"platform" => $noun,\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"kcname" => $kcname,\r\n"kcid"=>$kcid`,
                            jdcs: `"username" => $user,\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid`,
                            bscs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid`,
                        }
                    },{
                        name: '坚果',
                        tips: '',
                        data: {
                            name: '坚果',
                            url: "",
                            post: 1,
                            ckjk: "/api.php?act=get",
                            xdjk: "/api.php?act=add",
                            jdjk: "/api.php?act=chadan",
                            bsjk: "/api.php?act=budan",
                            ckcs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"platform" => $noun,\r\n"kcid" => $kcid`,
                            xdcs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"platform" => $noun,\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"kcname" => $kcname,\r\n"kcid"=>$kcid`,
                            jdcs: `"username" => $user,\r\n"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid`,
                            bscs: `"uid" => $a["user"],\r\n"key" => $a["pass"],\r\n"id" => $yid`,
                        }
                    },{
                        name: 'benz平台',
                        tips: '',
                        data: {
                            name: 'benz平台',
                            url: "",
                            post: 1,
                            ckjk: "/api/query",
                            xdjk: "/api/add",
                            jdjk: "/api/order",
                            bsjk: "/api/reset",
                            ckcs: `"token" => $a["pass"],\r\n"ptid" => $noun,\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass`,
                            xdcs: `"token" => $a["pass"],\r\n"ptid" => $noun,\r\n"school" => $school,\r\n"user" => $user,\r\n"pass" => $pass,\r\n"kcname" => $kcname,\r\n"kcid" => $kcid,\r\n"miaoshua" => $miaoshua,`,
                            jdcs: `"token" => $a["pass"],\r\n"user" => $user`,
                            bscs: `"token" => $a["pass"],\r\n"id" => $yid,`
                        }
                    },{
                        name: 'ikun通配',
                        tips: 'ikun的ip:端口号',
                        data: {
                            name: 'ikun',
                            url: "http://这里填ikun的ip:端口号/",
                            post: 0,
                            ckjk: "query/?platform=$noun&school=$school&account=$user&password=$pass",
                            xdjk: "getorder/?platform=$noun&school=$school&account=$user&password=$pass&course=$kcname&kcid=$kcid",
                            jdjk: "order/?token=$yid",
                            bsjk: "/api.php?act=budan",
                            ckcs: ``,
                            xdcs: ``,
                            jdcs: ``,
                            bscs: ``
                        }
                    },{
                        name: '小夜(页)',
                        tips: '自定义IP:端口号',
                        data: {
                            name: '小页',
                            url: "http://这里填ip:端口号/",
                            post: 1,
                            ckjk: "/api/user/GetCourseList",
                            xdjk: "/api/order/SubmitOrder",
                            jdjk: "/api/order/QueryProgress",
                            bsjk: "/api/order/SupplementOrder",
                            ckcs: `"school" => $school,\r\n"username" => $user,\r\n"password" => $pass`,
                            xdcs: `"school" => $school,\r\n"username" => $user,\r\n"password" => $pass,\r\n"courseName" => $kcname,\r\n"type" => $noun,\r\n"courseId"=>$kcid`,
                            jdcs: `"username" => $user`,
                            bscs: `"orderId" => $yid`
                        }
                    },
                    // {
                    //     name: 'YQSL猿气森林',
                    //     tips: '',
                    //     disabled:1,
                    //     data: {
                    //         url: "",
                    //         post: 1,
                    //         ckjk: "/api.php?act=get",
                    //         xdjk: "/api.php?act=add",
                    //         jdjk: "/api/search",
                    //         bsjk: "/api.php?act=get",
                    //         ckcs: ``,
                    //         xdcs: ``,
                    //         jdcs: ``,
                    //         bscs: ``
                    //     }
                    // }, 
                    ],
                },
                djData: {
                    hid: '',
                    category: '',
                    pricee: '',
                    fid: '',
                },
                templateStatu_num: '', // 选中
                ID_templateStatu_num: '',
                templateStatu_openIndex: 0, // 选择模板弹窗
                parse_djCode_INDEX: null,
                djCode: '',
                djCode_data: {
                    url: '',
                    data: '',
                }
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
            _this.get(1);
        },
        methods: {
            zzz(){
                const _this = this;
                console.log(_this.listTable)
            },
            copyT(text=''){
                const _this = this;
                navigator.clipboard.writeText(text).then(function() {
                    _this.$message.success("复制成功")
                }).catch(function(error) {
                    _this.$message.error('复制失败: ' + error)
                });
            },
            parse_djCode_open(){
                const _this = this;
                _this.parse_djCode_INDEX = layer.open({
                    type: 1,
                    title: '智能解析',
                    content: $("#parse_djCode"),
                    offset: 'rt',
                    shade: 0,
                    maxmin: true,
                    area: ["260px","440px"],
                    end: function(){
                        _this.djCode = '';
                        _this.djCode_data={
                            url: '',
                            data: '',
                        }
                    }
                })
            },
            parse_djCode(codeT){
                const _this = this;
                _this.djCode = _this.djCode.trim();
                codeT = _this.djCode;
                if(!codeT){
                    _this.$message.error('请粘贴对接代码')
                    return
                }
                
                 _this.djCode_data={
                    url: '解析失败',
                    data: '解析失败',
                }
                let regex =  /get_url\(\s*([^,\s]+)\s*,/;
                let match = codeT.match(regex);
                if(match){
                    let variable = match[1]; // 提取的变量名
                    let variableRegex = new RegExp(`\\${variable}\\s*=\\s*"([^"]+)"`);
                    let variableMatch = codeT.match(variableRegex);
                    
                    if (variableMatch) {
                        let pathMatch = variableMatch[1].match(/\/[^"]+/);
                        if (pathMatch) {
                            _this.djCode_data.url = pathMatch[0]; // 提取出的路径部分
                        }
                    }
                }
                
                
                let dataRegex = /\$data\s*=\s*array\(([^)]+)\);/;
                let data_match = codeT.match(dataRegex);
                if (data_match) {
                    let dataContent = data_match[1]; // 提取的 $data 内容
                    let keyValuePairs = dataContent.split(/\s*,\s*/); // 按逗号分割每一对键值对
                    let formattedPairs = keyValuePairs.map(pair => {
                    // 分割键和值
                    const [key, value] = pair.split(/\s*=>\s*/).map(part => part.trim());
                    // 处理键
                    const formattedKey = key.replace(/^\s*"(.*?)"\s*$/, '"$1"');
            
                    // 处理值
                    let formattedValue;
                    if (value.startsWith('$a["')) {
                        // 处理 $a["key"] 形式的变量
                        formattedValue = value.replace(/^\s*\$(\w+)\["(.*?)"\]\s*$/, '$$$1["$2"]');
                    } else if (value.startsWith('$')) {
                        // 处理 $key 形式的变量
                        formattedValue = value.replace(/^\s*\$(\w+)\s*$/, '$$$1');
                    } else {
                        // 处理普通字符串
                        formattedValue = `"${value.replace(/^\s*"(.*?)"\s*$/, '$1')}"`;
                    }
            
                    return `${formattedKey} => ${formattedValue}`;
                    });
                    _this.djCode_data.data = formattedPairs.join(',\n');
                }
                
                console.log(_this.djCode_data)

            },
            inputReplace(t=''){
                const _this = this;
                setTimeout(()=>{
                 eval('_this.' + t + ' = '+'_this.'  + t +`
                        .replace(/，/g, ',')
                        .replace(/；/g, ';')
                        .replace(/！/g, '!')
                        .replace(/？/g, '?')
                        .replace(/！/g, ';')
                        .replace(/（/g, '(')
                        .replace(/）/g, ')')
                        .replace(/。/g, '.')
                        .replace(/——/g, '_')
                        .replace(/【/g, '[')
                        .replace(/】/g, ']')
                        .replace(/\\s+/g, '')
                 `)
                },0)
                // t1 = t1.replace(/，/g, ',')
            },
            getHMoney(hid,i){
                const _this = this;
                axios.post("/apiadmin.php?act=getHMoney",{
                    hid: hid
                },{emulateJSON:true}).then(r=>{
                    if(r.data.code === 1){
                        _this.row.data[i].money = r.data.money;
                    }else{
                        _this.row.data[i].money = -999999;
                    }
                })
            },
            modal_add_open: function(type, res) {
                const _this = this;
                
                if (type) {
                    _this.storeInfo = JSON.parse(JSON.stringify(res))
                }

                // 如果是添加
                if (!type) {
                    Object.keys(_this.storeInfo2.default).map(i => {
                        _this.storeInfo[i] = _this.storeInfo2.default[i];
                    })
                }


                layui.use(function() {
                    let modal_add = layer.open({
                        type: 1,
                        id: "aaa",
                        title: "货源" + (type ? "编辑" : "添加"),
                        area: ['360px', '530px'],
                        content: $("#modal_add"),
                        btn: [type ? "保存" : "添加", '取消', ],
                        success: function() {
                            setTimeout(() => {
                                layui.form.render();
                                layui.form.on('radio(templateStatu-radio-filter)', function(data) {
                                    setTimeout(() => {
                                        layui.form.render();
                                    }, 0)
                                    var elem = data.elem; // 获得 radio 原始 DOM 对象
                                    var checked = elem.checked; // 获得 radio 选中状态
                                    var value = elem.value; // 获得 radio 值
                                    switch (elem.value) {
                                        case "1":
                                            $("#ID_templateStatu0").prop('checked', false)
                                            Object.keys(_this.storeInfo2.default).map(i => {
                                                _this.storeInfo[i] = _this.storeInfo2.default[i];
                                            })
                                            break;
                                        case "2":
                                            for (let i in _this.storeInfo) {
                                                _this.storeInfo[i] = "";
                                            }
                                            _this.storeInfo.smtp_money = _this.storeInfo2.default.smtp_money;
                                            _this.storeInfo.post = 1;
                                            break;
                                        case "3":
                                            console.log('elem.value', elem.value);
                                            _this.templateStatu_openIndex = layer.open({
                                                type: 1,
                                                offset: 'r',
                                                anim: 'slideLeft', // 从右往左
                                                area: ['320px', '100%'],
                                                shade: 0.1,
                                                shadeClose: true,
                                                id: 'ID_templateStatu_open',
                                                title: '选择模板',
                                                content: $("#ID_templateStatu")
                                            });
                                            break;
                                    }
                                });

                                layui.form.on('radio(ID_templateStatu-filter)', function(data) {
                                    setTimeout(() => {
                                        layui.form.render();
                                    }, 0)
                                    var elem = data.elem; // 获得 radio 原始 DOM 对象
                                    var checked = elem.checked; // 获得 radio 选中状态
                                    var value = elem.value; // 获得 radio 值
                                    _this.ID_templateStatu_num = value;
                                    let check_data = _this.storeInfo2.other.find((item, index) => `${index}` === elem.value);

                                    if (!check_data) {
                                        layer.msg('模板配置不存在或配置异常！')
                                        return
                                    }
                                    Object.keys(check_data.data).map(i => {
                                        _this.storeInfo[i] = check_data.data[i];
                                    })
                                    layer.msg('已部署模板配置！');
                                    layer.close(_this.templateStatu_openIndex);
                                    setTimeout(() => {
                                        layui.form.render();
                                    }, 0)
                                })
                            }, 0)
                        },
                        yes: function(index) {
                            
                            let verify = [
                                {
                                  a: 'name',
                                  b: '货源名称',
                                },
                                {
                                  a: 'url',
                                  b: '货源网址',
                                },
                            ];
                            for(let i in verify){
                                if(!_this.storeInfo[verify[i].a]){
                                    _this.$message.error(`请完善${verify[i].b}`);
                                    return;
                                }
                            }
                            let formData = layui.form.val('form-add');
                            console.log(_this.storeInfo)
                            formData.action = type ? "" : "add";
                            if (type) {
                                formData.hid = res.hid;
                            } else {
                                if (formData.hid) {
                                    delete formData.hid;
                                }
                            }
                            dlsjVM.form(new URLSearchParams(formData).toString());
                            layer.close(index)
                        },
                        end: function() {
                            for (let i in _this.storeInfo) {
                                _this.storeInfo[i] = '';
                            }
                            _this.storeInfo.post = 1;
                            $('input[name="templateStatu"]')[0].click();
                            setTimeout(() => {
                                layui.form.render();
                            }, 0)
                            layer.close(_this.parse_djCode_INDEX);
                        }
                    })
                })
            },
            get: function(page) {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=huoyuanlist", {
                    page: page
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.row = r.data;
                        $("#orderlist").ready(() => {
                            $("#orderlist").show();
                        })
                        for(let i in  _this.row.data){
                            setTimeout(()=>{
                                _this.getHMoney(_this.row.data[i].hid,i);
                            },100 * i)
                        }
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            yjdj: function(hid) {
                
                layui.use(function() {
                    layer.open({
                        title: '一键对接（老版）',
                        type: 1,
                        content: $("#djID"),
                        area: [350+'px'],
                        minmax: true,
                        btn: ['对接', '取消'],
                        yes: function(index) {
                            dlsjVM.djData.hid = hid;
                            for (let i in dlsjVM.djData) {
                                if (!dlsjVM.djData[i]) {
                                    layer.msg('请输入完整')
                                    return
                                }
                            }
                            let loadIndex = layer.load(0);
                            $.get("/apiadmin.php?act=yjdj&hid=" + dlsjVM.djData.hid + "&pricee=" + dlsjVM.djData.pricee + "&category=" + dlsjVM.djData.category + "&fid=" + dlsjVM.djData.fid, function(data) {
                                if (data.code == 1) {
                                    layer.close(loadIndex);
                                    layer.close(index);
                                    layer.msg('对接成功');
                                } else {
                                    layer.msg(data.msg, {
                                        icon: 2
                                    });
                                }
                            });

                        },
                    })
                })
                return
                layer.confirm('确定要对接平台吗？', {
                    title: '温馨提示',
                    icon: 1,
                    btn: ['确定', '取消']
                }, function() {
                    var category = prompt("请输入要对接的分类ID："); // 弹出对话框获取分类ID
                    if (category != null) {
                        var pricee = prompt("请输入要增加的百分比价格：1.05 就是增加5% 看不懂问数学老师"); // 弹出对话框获取价格
                        if (pricee != null) {
                            var load = layer.load(2);
                            $.get("/apiadmin.php?act=yjdj&hid=" + hid + "&pricee=" + pricee + "&category=" + category + "&fid=44", function(data) {
                                layer.close(load);
                                if (data.code == 1) {
                                    // top.location = "../index/class";
                                    window.onload()
                                    layer.msg(data.msg, {
                                        icon: 1
                                    });
                                } else {
                                    layer.msg(data.msg, {
                                        icon: 2
                                    });
                                }
                            });
                        }
                    }
                });
            },
            yjdj2(){
                const _this = this;
                // let thisHidData =  _this.row.data.find(i=>i.hid === hid);
                // console.log('thisHidData',thisHidData);
                let loadIndex = layer.load(0);
                let appIndex_hid = layer.open({
                    id: "djTool",
                    type: 2,
                    shade: 0, // 不显示遮罩
                    title: `高级对接工具`,
                    area: ['100%','100%'],
                    maxmin: true,
                    content: `components/djTool.php`, // 捕获的元素
                    success: function (layero, index) {
                        var iframe = layero.find('iframe');
                        $(iframe).ready(() => {
                            layer.close(loadIndex);
                            // $(iframe)[0].contentWindow.postMessage(hid, '*');
                        })
                    },
                    end: function () {
                    },
                });;
            },
            form: function(data) {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=uphuoyuan", {
                    data: data
                }, {
                    emulateJSON: true
                }).then(function(r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.get(_this.row.current_page);
                        layer.msg('处理成功！')
                    } else {
                        layer.msg(r.data.msg, {
                            icon: 2
                        });
                    }
                });
            },
            del: function(hid) {
                const _this = this;
                if(!hid){
                    _this.$message.error("请选择货源");
                    return
                }
                if(!hid.length){
                    _this.$message.error("请选择货源");
                    return
                }
                
                layui.use(function() {
                    layer.confirm('确认删除？', {
                        btn: ['确定', '算了'] //按钮
                    }, function() {
                        var load = layer.load(2);
                        axios.post("/apiadmin.php?act=huoyuan_del", {
                            hid: hid
                        }, {
                            emulateJSON: true
                        }).then(function(r) {
                            layer.close(load);
                            if (r.data.code == 1) {
                                _this.get(_this.row.current_page);
                                layer.msg(r.data.msg, {
                                    icon: 1
                                });
                            } else {
                                layer.msg(r.data.msg, {
                                    icon: 2
                                });
                            }
                        });
                    })
                })
            },
            bs: function(oid) {
                layer.msg(oid);
            }
        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var dlsjVM = app.mount('#orderlist');
    
</script>