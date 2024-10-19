<?php
$mod = 'blank';
$title = '订单列表';
include_once('head.php');
?>

<style>
    .pagination .active a {
        color: #fff !important;
        padding: 2px 8px;
    }

    .el-col {
        margin-bottom: 10px;
    }

    .el-progress .el-progress__text {
        font-size: 11px !important;
    }

    .el-progress .el-progress__text i {
        font-size: 18px;
        color: #13CE66;
        font-weight: bold;
    }

    .queryCard .el-button {
        margin: 0 2px 0 0;
    }

    .progressZDY {
        top: 10px;
    }

    .progressZDY .layui-progress-text {
        scale: .8;
        display: inline-block;
        top: -17px;
    }

    #listTable .layui-input {
        height: 27px !important;
    }

    #listTable .layui-input-affix .layui-icon {
        font-size: 12px;
    }

    #listTable .layui-input-affix {
        line-height: 20px;
        width: 28px;
        padding: 0;
    }

    #listTable .layui-form-checkbox {
        scale: .8;
        position: relative;
        top: -3px;
    }

    #listTable .layui-input-suffix {
        padding: 0 3px;
    }
    
    #listTable_new .el-table__body .el-table__row .el-table__cell .cell{
        width: 118px;
        white-space: normal;
        overflow-y: auto;
        max-height: 70px;
    }
    
    #listTable_new .layui-input {
        height: 27px !important;
    }

    #listTable_new .layui-input-affix .layui-icon {
        font-size: 12px;
    }

    #listTable_new .layui-input-affix {
        line-height: 20px;
        width: 28px;
        padding: 0;
    }

    #listTable_new .layui-form-checkbox {
        scale: .8;
        position: relative;
        top: -3px;
    }

    #listTable_new .layui-input-suffix {
        padding: 0 3px;
    }

    .el-input-group__append {
        padding: 0;
    }
</style>

<div class="layui-padding-1" id="orderlist" style="display:none;">

    <div class="control" style="padding-bottom: 40px;">

        <el-card class="panel panel-default queryCard" :body-style="{ padding: '10px' }">

            <blockquote class="layui-elem-quote layui-quote-nm layui-font-12" style="padding: 5px 8px;">
                <i class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop"></i>
                部分订单进度同步不及时，但可能已完成，一切以官方实际进度为准！
            </blockquote>
            <el-collapse v-model="menuName" style="margin-bottom: 5px;">
                <el-collapse-item name="0">
                    <template #title><i class="header-icon el-icon-search"></i>&nbsp;条件查询操作</template>

                    <div class="layui-form" style="padding:0px;">

                        <el-row :gutter="10">
                            <el-col :xs="12" :sm="6">
                                <el-select @change="get(1)" id="select" v-model="cx.cid" filterable placeholder="请选择状态"
                                    style="width:100%">
                                    <el-option label="所有平台" value=""></el-option>
                                    <?php
                                    $a = $DB->query("select name,cid from qingka_wangke_class where status=1 ");
                                    while ($row = $DB->fetch($a)) {
                                        echo '<el-option label="' . $row['name'] . '" value="' . $row['cid'] . '"></el-option>';
                                    }
                                    ?>
                                </el-select>
                            </el-col>
                            <el-col :xs="12" :sm="6">
                                <el-select @change="get(1)" id="select" v-model="cx.status_text" filterable
                                    placeholder="请选择订单状态" style="width:100%">
                                    <el-option label="订单状态" value=""></el-option>
                                    <el-option label="已完成" value="已完成"></el-option>
                                    <el-option label="待处理" value="待处理"></el-option>
                                    <el-option label="待重刷" value="待重刷"></el-option>
                                    <el-option label="已提交" value="已提交"></el-option>
                                    <el-option label="进行中" value="进行中"></el-option>
                                    <el-option label="队列中" value="队列中"></el-option>
                                    <el-option label="考试中" value="考试中"></el-option>
                                    <el-option label="平时分" value="平时分"></el-option>
                                    <el-option label="补刷中" value="补刷中"></el-option>
                                    <el-option label="异常" value="异常"></el-option>
                                    <el-option label="已取消" value="已取消"></el-option>
                                    <el-option label="已退款" value="已退款"></el-option>
                                </el-select>
                            </el-col>
                            <el-col :xs="12" :sm="6" v-if="uid">
                                <el-select @change="get(1)" id="select" v-model="cx.dock" filterable placeholder="处理状态"
                                    style="width:100%">
                                    <el-option label="处理状态" value=""></el-option>
                                    <el-option label="未支付" value="-9"></el-option>
                                    <el-option label="待处理" value="0"></el-option>
                                    <el-option label="处理成功" value="1"></el-option>
                                    <el-option label="处理失败" value="2"></el-option>
                                    <el-option label="重复下单" value="3"></el-option>
                                    <el-option label="已取消" value="4"></el-option>
                                    <el-option label="我的" value="99"></el-option>
                                </el-select>
                            </el-col>
                        </el-row>

                        <div class="layui-row layui-col-space10">
                            <div class="layui-col-md2 layui-col-sm3 layui-col-xs6">
                                <input class="layui-input" size="small" clearable v-model="cx.ptname" placeholder="平台名称"
                                    @keydown.enter="get(1)"></input>
                            </div>
                            <div class="layui-col-md2 layui-col-sm3 layui-col-xs6" v-if="uid">
                                <input class="layui-input" size="small" clearable v-model="cx.uid" placeholder="UID"
                                    @keydown.enter="get(1)"></input>
                            </div>
                            <div class="layui-col-md2 layui-col-sm3 layui-col-xs6" v-if="uid">
                                <input class="layui-input" size="small" clearable v-model="cx.oid" placeholder="订单ID"
                                    @keydown.enter="get(1)"></input>
                            </div>
                            <div class="layui-col-md2 layui-col-sm3 layui-col-xs6">
                                <input class="layui-input" size="small" clearable v-model="cx.qq" placeholder="账号"
                                    @keydown.enter="get(1)"></input>
                            </div>
                            <div class="layui-col-md2 layui-col-sm3 layui-col-xs6">
                                <input class="layui-input" size="small" clearable v-model="cx.kcname" placeholder="课程名称"
                                    @keydown.enter="get(1)"></input>
                            </div>
                            <div class="layui-col-md2 layui-col-sm3 layui-col-xs6">
                                <input class="layui-input" size="small" clearable v-model="cx.school" placeholder="学校名称"
                                    @keydown.enter="get(1)"></input>
                            </div>
                            <div class="layui-col-md2 layui-col-sm3 layui-col-xs6">
                                <input class="layui-input" size="small" clearable v-model="cx.status_text"
                                    placeholder="订单状态" @keydown.enter="get(1)"></input>
                            </div>
                            <div class="layui-col-md2 layui-col-sm3 layui-col-xs6">
                                <input class="layui-input" size="small" clearable v-model="cx.remarks" placeholder="日志"
                                    @keydown.enter="get(1)"></input>
                            </div>

                            <div class="layui-col-md2 layui-col-sm3  layui-col-xs6"
                                style="float: right; text-align: right;">
                                <el-button type="primary" title="查询订单" @click="get(1)">
                                    <el-icon>
                                        <search />
                                    </el-icon>&nbsp;查询订单
                                </el-button>
                            </div>
                        </div>

                    </div>

                </el-collapse-item>

                <el-collapse-item v-if="uid" name="1">
                    <template #title><i class="header-icon el-icon-edit"></i>&nbsp;任务状态操作</template>
                    <div
                        style="padding: 2px 0;display: grid;align-items: center; grid-template-columns: repeat(auto-fill,80px); grid-template-rows: repeat(auto-fill,30px); grid-column-gap: 5px; grid-row-gap: 5px;">
                        <el-button type="warning" round size="small" @click="status_text('待处理')">
                            <el-icon>
                                <Clock />
                            </el-icon>待处理
                        </el-button>
                        <el-button type="success" round size="small" @click="status_text('已完成')">
                            <el-icon><Circle-Check /></el-icon>已完成
                        </el-button>
                        <el-button type="success" round size="small" @click="status_text('已考试')">
                            <el-icon><Circle-Check /></el-icon>已考试
                        </el-button>
                        <el-button type="primary" round size="small" @click="status_text('进行中')">
                            <el-icon class="is-loading">
                                <Loading />
                            </el-icon>进行中
                        </el-button>
                        <el-button type="primary" round size="small" @click="status_text('队列中')">
                            <el-icon class="is-loading">
                                <Loading />
                            </el-icon>队列中
                        </el-button>
                        <el-button type="danger" round size="small" @click="status_text('异常')">
                            <el-icon>
                                <Warning />
                            </el-icon>异常
                        </el-button>
                        <el-button size="small" round @click="status_text('待支付')">
                            待支付
                        </el-button>
                        <el-button size="small" round @click="status_text('待审核')">
                            待审核
                        </el-button>
                        <el-button size="small" round type="" @click="status_text('点我接码')">
                            接码
                        </el-button>
                        <el-input v-model="status_text_zdy" style="width: 200px" placeholder="请输入自定义状态">
                            <template #append>
                                <el-button @click="status_text(status_text_zdy)">
                                    <el-icon>
                                        <Check />
                                    </el-icon>
                                </el-button>
                            </template>
                        </el-input>

                    </div>
                </el-collapse-item>
                <el-collapse-item v-if="uid" name="2">
                    <template #title><i class="header-icon el-icon-edit"></i>&nbsp;处理状态操作</template>
                    <div
                        style="padding: 2px 0;display: grid;align-items: center; grid-template-columns: repeat(auto-fill,90px); grid-template-rows: repeat(auto-fill,30px); grid-column-gap: 5px; grid-row-gap: 5px;">
                        <el-button type="warning" round size="small" @click="dock(-9)">未支付(-9)</el-button>
                        <el-button type="warning" round size="small" @click="dock(0)">待处理(0)</el-button>
                        <el-button type="success" round size="small" @click="dock(1)">处理成功(1)</el-button>
                        <el-button type="danger" round size="small" @click="dock(2)">处理失败(2)</el-button>
                        <el-button type="info" round size="small" @click="dock(3)">重复下单(3)</el-button>
                        <el-button type="danger" round size="small" @click="dock(4)">取消(4)</el-button>
                        <el-button type="default" round size="small" @click="dock(99)">自营(99)</el-button>
                        <el-button type="danger" round size="small" @click="tk(sex)">订单退款</el-button>
                    </div>
                </el-collapse-item>

                <el-collapse-item name="3">
                    <template #title><i class="header-icon el-icon-copy-document">&nbsp;批量操作</i>
                    </template>
                    <div
                        style="padding: 2px 0;display: grid;align-items: center; grid-template-columns: repeat(auto-fill, [col-start] minmax(100px, 1fr) [col-end]); grid-template-rows: repeat(auto-fill,30px); grid-column-gap: 5px; grid-row-gap: 5px;">
                        <el-button type="primary" round size="small" @click="plzt(sex)">
                            <el-icon><Video-Play /></el-icon>&nbsp;批量同步
                        </el-button>
                        <el-button type="warning" round size="small" @click="plbs(sex)">
                            <el-icon><Video-Play /></el-icon>&nbsp;批量补刷
                        </el-button>
                        <el-button v-if="uid" type="danger" round size="small" @click="sc(sex)">
                            <el-icon>
                                <Delete />
                            </el-icon>&nbsp;批量删除
                        </el-button>
                        <el-button v-if="uid" type="info" round size="small" @click="xgdl(sex)">
                            <el-icon>
                                <Connection />
                            </el-icon>&nbsp;订单转单
                        </el-button>
                        <el-button type="danger" round size="small" @click="changePass(sex)">
                            <el-icon>
                                <Unlock />
                            </el-icon>&nbsp;改密
                        </el-button>
                    </div>
                </el-collapse-item>
            </el-collapse>

            <div class="el-table-column-fixed  table-responsive table-condensed" lay-size="sm" style="overflow: auto;">

                <el-row>
                    <el-col :xs="24" :sm="6"
                        style="display: flex; align-items: center;gap: 3px;height: auto; margin: 0 0 5px;">
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary"
                            @click="get(row.current_page)">
                            <i class="layui-icon layui-icon-refresh"></i> 刷新
                        </button>
                        <button type="button" class="layui-btn layui-btn-xs layui-btn-primary" style="margin-left: 0;"
                            @click="export_table()">
                            <i class="layui-icon layui-icon-export"></i> 导出
                        </button>
                        <?php if ($userrow["uid"] == 1) { ?>
                            <el-popover placement="top-start" title="" trigger="click" width="auto">
                                <template #reference>
                                    <el-button type="info" text size="small" title="点击学习">
                                        <el-icon>
                                            <Warning />
                                        </el-icon>&nbsp;操作提示
                                    </el-button>
                                </template>
                                <div class="layui-font-12">
                                    <el-collapse v-model="studyUse_name">
                                        <el-collapse-item class="layui-font-12" title="① 处理失败的订单如何处理？" name="1">
                                            第一种方式：点击[处理]列的[Error]，即可对单个订单进行手动提交；<br />
                                            第二种方式【推荐】：批量勾选需要重新提交的订单，然后点击[处理状态操作]的[待处理]，等待你设定的对接定时任务自动轮询
                                        </el-collapse-item>
                                        <el-collapse-item class="layui-font-12" title="② 为何导出功能无法导出所有的订单？" name="2">
                                            [导出]功能是采集当前表格数据生成Excel，而且每页数据是通过分页获取。
                                        </el-collapse-item>
                                    </el-collapse>
                                </div>
                            </el-popover>
                        <?php } ?>
                    </el-col>
                </el-row>

                <div style="overflow: auto; width: 100%;">

                    <!--表格导出-->
                    <table v-if="export_status" id="listTable2" name="2" lay-filter="listTable2"
                        class="layui-table listTable2" style="display:none" border="1">
                        <thead style="white-space:nowrap">
                            <tr>
                                <th>平台</th>
                                <th>学校</th>
                                <th>账号</th>
                                <th>密码</th>
                                <th>课程</th>
                                <th>状态</th>
                                <th>参考进度</th>
                                <th>日志</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(res,index) in row.data" :key="index">
                                <td v-html="res.ptname">
                                </td>
                                <td v-html="res.school">
                                </td>
                                <td v-html="res.user">
                                </td>
                                <td v-html="res.pass">
                                </td>
                                <td v-html="res.kcname">
                                </td>
                                <td v-html="res.status">
                                </td>
                                <td
                                    v-html="( process_num(res.process)?process_num(res.process):(res.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0) )+'%'">
                                </td>
                                <td
                                    v-html="res.remarks?res.remarks:'已完成：'+( process_num(res.process)?process_num(res.process):(res.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0) )+'%'">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!--代理端表格-->
                    <table v-if="!uid" id="listTable_user" lay-filter="listTable_user"></table>
                    
                    <!--管理端表格-->
                    <el-table id="listTable_new" ref="listTable_new" v-if="uid" :data="row.data" size="small" stripe
                        border show-overflow-tooltip empty-text="暂无订单" style="width: 100%;"
                        :row-style="{'max-height': '70px'}" @selection-change="listTable_new_select">
                        
                        <el-table-column fixed="left" type="selection" width="28" align="center"></el-table-column>
                        <el-table-column fixed="right" prop="hid" label="操作" width="100" align="center" >
                            <template #default="scope">
                                <div style="line-height: normal; scale: .8; transform-origin: left;">关联代理:{{scope.row.uid}}</div>
                                <el-dropdown trigger="click" split-button type="primary" size="small" @click.stop="bs(scope.row.oid)">
                                    补刷
                                    <template #dropdown>
                                        <el-dropdown-menu style="width:150px">
                                            <el-dropdown-item>
                                                <p style="margin: 0;" @click="up(scope.row.oid)"><el-icon style="margin: 2px;"><Connection /></el-icon>刷新/同步</p>
                                            </el-dropdown-item>
                                            <el-dropdown-item>
                                                <div style="margin: 0;" @click="ddinfo(scope.row)"><el-icon style="margin: 2px;"><search /></el-icon>查看详细</div>
                                            </el-dropdown-item>
                                            <el-dropdown-item>
                                                <p style="margin: 0;" @click="sex=[scope.row.oid];sc()"><el-icon style="margin: 2px;"><Delete /></el-icon>删除订单</p>
                                            </el-dropdown-item>
                                            <el-dropdown-item disabled divided>修改关联代理(转单)</el-dropdown-item>
                                            <el-dropdown-item >
                                                <el-select size="small" filterable v-model="scope.row.uid" placeholder="请选择代理"
                                                    @change="xgdl_value=$event;sex=[scope.row.oid];xgdl_get()">
                                                    <el-option v-for="item in dl_idname" :key="item.uid"
                                                        :label="`[${item.uid}]${item.name}`" :value="item.uid"
                                                        :disabled="item.active == 0"></el-option>
                                                </el-select>
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </template>
                                </el-dropdown>
                            </template>
                        </el-table-column>
                        
                        <el-table-column prop="ptname" label="商品" width="120">
                            <template #default="scope">
                                <div style="white-space: normal;">
                                    <el-tag type="primary" size="small">ID：{{scope.row.oid}}</el-tag><br />
                                    {{scope.row.ptname}}
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="ptname" label="账号信息" width="140">
                            <template #default="scope">
                                <span v-if="scope.row.school && scope.row.school!='自动识别'">
                                    {{scope.row.school}}<br />
                                </span>
                                {{scope.row.user}}<br />
                                {{scope.row.pass}}<br />
                            </template>
                        </el-table-column>
                        <el-table-column prop="kcname" label="课程名称" width="170" class="white-space: normal;">
                            <template #default="scope">
                                <div class="layui-input-group">
                                    <input style="" type="text" name="" v-model="scope.row.kcname"
                                        :value="scope.row.kcname" placeholder="无" class="layui-input">
                                    <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                        @click="setOrder(scope.row,{kcname:scope.row.kcname})">
                                        <i class="layui-icon layui-icon-edit"></i>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="status" label="任务/对接状态" width="100" align="center">
                            <template #default="scope">
                                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                                    <div style="width: 70px;">
                                        <el-button v-if="scope.row.status=='待处理'" type="warning" size="small">
                                            <el-icon>
                                                <Clock />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='待上号'" type="warning" size="small">
                                            <el-icon>
                                                <Clock />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='排队中'" type="warning" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='已暂停'" type="warning" size="small">
                                            <el-icon><Video-Pause /></el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='已完成'" type="success" size="small">
                                            <el-icon><Circle-Check /></el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='已考试'" type="success" size="small">
                                            <el-icon><Circle-Check /></el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='异常'" type="danger" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='失败'" type="danger" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='密码错误'" type="danger" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='已提取'" type="primary" size="small">
                                            <el-icon>
                                                <Upload />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='已提交'" type="primary" size="small">
                                            <el-icon>
                                                <Upload />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='进行中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='上号中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='考试中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='队列中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='待考试'" type="primary" size="small">
                                            <el-icon>
                                                <Clock />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='正在考试'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='平时分'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='作业中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='补刷中'" type="info" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='已退单'" type="info" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='已退款'" type="info" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='待支付'" type="info" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='待审核'" type="info" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else-if="scope.row.status=='点我接码'" type="danger" size="small"
                                            icon="el-icon-thumb" @click="jiema">
                                            <el-icon>
                                                <Clock />
                                            </el-icon>{{scope.row.status}}
                                        </el-button>
                                        <el-button v-else size="small">
                                            {{scope.row.status}}
                                        </el-button>
                                    </div>
                                    <div style="width: 70px;">
                                        <el-button style="width: 100%;" @click="duijie(scope.row.oid)"
                                            v-if="scope.row.dockstatus==0" type="" size="small">等待对接</el-button>
                                        <el-button style="width: 100%;" v-else-if="scope.row.dockstatus==1" type="success"
                                            size="small">成功对接</el-button>
                                        <el-button style="width: 100%;" @click="duijie(scope.row.oid)"
                                            v-else-if="scope.row.dockstatus==2" type="danger" size="small">对接失败</el-button>
                                        <el-button style="width: 100%;" v-else-if="scope.row.dockstatus==3" type="info"
                                            size="small">重复对接</el-button>
                                        <el-button style="width: 100%;" v-else-if="scope.row.dockstatus==4" type="info"
                                            size="small">已取消</el-button>
                                        <el-button style="width: 100%;" v-else-if="scope.row.dockstatus==99"
                                            size="small">自营</el-button>
                                        <el-button style="width: 100%;" v-else-if="scope.row.dockstatus==-9" type="info"
                                            size="small">未支付</el-button>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="noun" label="对接参数&货源" width="105" align="center">
                            <template #default="scope">
                                <div class="layui-input-group">
                                    <input style="" type="text" name="" v-model="scope.row.noun" :value="scope.row.noun"
                                        placeholder="无" class="layui-input">
                                    <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                        @click="setOrder(scope.row,{noun:scope.row.noun})">
                                        <i class="layui-icon layui-icon-edit"></i>
                                    </div>
                                </div>
                                <div class="layui-input-group" style="width: 100%;">
                                    <el-select size="small" filterable v-model="scope.row.hid" placeholder="请选择货源"
                                        @change="setOrder(scope.row,{hid:scope.row.hid})">
                                        <?php
                                        $huoyuanResultb = $DB->query("select hid,name,status from qingka_wangke_huoyuan order by CAST(hid AS UNSIGNED) asc");
                                        while ($a = $DB->fetch($huoyuanResultb)) {
                                            echo '<el-option label="[' . $a['hid'] . ']' . $a['name'] . '" value="' . $a['hid'] . '"></el-option>';
                                        }

                                        ?>
                                    </el-select>
                                </div>
                            </template>
                        </el-table-column>
                        <!--<el-table-column prop="oid" label="<?php if ($userrow['uid'] == 1) { ?>详细/<?php } ?>刷新/补刷"-->
                        <!--    width="105" align="center">-->
                        <!--    <template #default="scope">-->
                        <!--        <el-button v-if="uid" style="margin-left: 0;" type="primary" size="small" circle-->
                        <!--            @click="ddinfo(scope.row)">-->
                        <!--            <el-icon>-->
                        <!--                <search />-->
                        <!--            </el-icon>-->
                        <!--        </el-button>-->
                                <!--刷新按钮-->
                        <!--        <el-button type="success" style="margin-left: 0;" size="small" circle-->
                        <!--            @click="up(scope.row.oid)">-->
                        <!--            <el-icon><refresh-left /></el-icon>-->
                        <!--        </el-button>-->
                                <!--补刷按钮-->
                        <!--        <el-button type="warning" style="margin-left: 0;" size="small" circle-->
                        <!--            @click="bs(scope.row.oid)">-->
                        <!--            <el-icon>-->
                        <!--                <promotion />-->
                        <!--            </el-icon>-->
                        <!--        </el-button>-->
                        <!--    </template>-->
                        <!--</el-table-column>-->
                        <el-table-column prop="process" label="参考进度" width="65" align="center">
                            <template #default="scope">
                                <template
                                    v-if="( process_num(scope.row.process)?process_num(scope.row.process):(scope.row.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0) )==100">
                                    <el-progress width="50" stroke-width="3" type="circle"
                                        :percentage="process_num(scope.row.process)?process_num(scope.row.process):(scope.row.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0)"></el-progress>
                                </template>
                                <template v-else>
                                    <el-progress width="50" stroke-width="3" type="circle"
                                        :percentage="process_num(scope.row.process)?process_num(scope.row.process):(scope.row.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0)"></el-progress>
                                </template>
                            </template>
                        </el-table-column>
                        <el-table-column prop="remarks" label="日志" width="200">
                            <template #default="scope">
                                <div style="white-space: normal; overflow-y: auto; max-height: 70px;">
                                    {{scope.row.remarks?scope.row.remarks:'已完成：'+(
                                    process_num(scope.row.process)?process_num(scope.row.process):(scope.row.status.search(/已完成|已经完成|已经全部完成/)!=
                                    -1?100:0) )+'%'}}
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="addtime" label="提交时间" width="150" align="center"></el-table-column>
                        <el-table-column prop="uptime" label="更新时间" width="150" align="center"></el-table-column>
                        <el-table-column prop="fees" label="扣费" width="80" align="center"></el-table-column>
                        <el-table-column prop="kcid" label="课程ID" width="125" align="center">
                            <template #default="scope">
                                <div class="layui-input-group">
                                    <input style="" type="text" name="" v-model="scope.row.kcid" :value="scope.row.kcid"
                                        placeholder="无" class="layui-input">
                                    <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                        @click="setOrder(scope.row,{kcid:scope.row.kcid})">
                                        <i class="layui-icon layui-icon-edit"></i>
                                    </div>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="type" label="标识" width="90" align="center">
                            <template #default="scope">
                                <template v-if="scope.row.type">

                                    <span class="layui-font-green">
                                        <template v-if="scope.row.type == 'tourist'">
                                            接单商城订单
                                        </template>
                                        <template v-else-if="scope.row.type == 'tourist1'">
                                            接单商城订单
                                        </template>
                                        <template v-else-if="scope.row.type == 'axqg'">
                                            爱学强国订单
                                        </template>
                                        <template v-else-if="scope.row.type == 'xc'">
                                            小储商城订单
                                        </template>
                                        <template v-else-if="scope.row.type == 'lanyangyang'">
                                            懒洋洋订单
                                        </template>
                                        <template v-else>
                                            未知 | {{ scope.row.type }}
                                        </template>
                                    </span>

                                </template>
                                <template v-else>
                                    <span class="layui-font-green">普通订单</span>
                                </template>
                            </template>
                        </el-table-column>

                    </el-table>

                    <!--管理端表格-->
                    <table v-if="uid & false" id="listTable" lay-filter="listTable" class="layui-table" lay-size="sm"
                        lay-width="10" lay-even style="margin:0;overflow: auto;">
                        <thead style="white-space:nowrap">
                            <tr><!--<th>#</th>-->
                                <th style="display: flex; align-items: center;">

                                    <input type="checkbox" id="checkboxAll" @click="selectAll()">
                                    <label for="checkboxAll"></label>

                                </th>
                                <th v-if="uid">ID</th>
                                <th v-if="uid">UID</th>
                                <th v-if="uid">YID</th>
                                <th lay-data="{field:'yid', width:80, sort: true}">平台</th>
                                <th>账号信息</th>
                                <th>课程</th>
                                <th>任务状态</th>
                                <th v-if="uid" class="center">处理</th>
                                <th v-if="uid" class="center">对接参数</th>
                                <th v-if="uid" class="center">货源ID</th>
                                <th class="center">刷新/补刷</th>
                                <th class="center">进度</th>
                                <th>日志</th>
                                <th>提交时间</th>
                                <th>更新时间</th>
                                <th>扣费</th>
                                <th v-if="uid">课程ID</th>
                                <th v-if="uid" class="center">标识</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="res in row.data" :key="res.oid">

                                <td class="center" style="white-space:nowrap;width: 20px;">
                                    <div style="width:20px;">

                                        <input type="checkbox" :id="`checkboxAll${res.oid}`" :value="res.oid"
                                            v-model="sex">
                                        <label :for="`checkboxAll${res.oid}`"></label>

                                    </div>
                                </td>

                                <td v-if="uid">
                                    <div style="width: 90px; white-space: normal; word-break: break-all;scale: 0.8;"
                                        class="layui-input-group">
                                        <template v-if="uid">
                                            <input style="" type="text" name="" v-model="res.oid" :value="res.oid"
                                                placeholder="无" class="layui-input">
                                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                                @click="setOrder(res,{oid:res.oid})">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </div>
                                        </template>
                                    </div>
                                </td>

                                <td v-if="uid">
                                    <div style="width: 90px; white-space: normal; word-break: break-all;scale: 0.8;"
                                        class="layui-input-group">
                                        <template v-if="uid">
                                            <input style="" type="text" name="" v-model="res.uid" :value="res.uid"
                                                placeholder="无" class="layui-input">
                                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                                @click="setOrder(res,{uid:res.uid})">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </div>
                                        </template>
                                    </div>
                                </td>

                                <td v-if="uid" style="white-space:nowrap;">
                                    <div style="width: 90px; white-space: normal; word-break: break-all;"
                                        class="layui-input-group">
                                        <template v-if="uid">
                                            <input style="" type="text" name="" v-model="res.yid" :value="res.yid"
                                                placeholder="无" class="layui-input">
                                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                                @click="setOrder(res,{yid:res.yid})">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </div>
                                        </template>
                                    </div>
                                </td>


                                <td style="white-space:nowrap;">
                                    <p style="width: 80px; white-space: normal;">{{res.ptname}}</p>
                                    <span v-if="res.miaoshua=='1'" style="color: red;">&nbsp;秒单</span>
                                </td>
                                <td style="white-space:nowrap">
                                    <div style="width: 120px">
                                        <span v-if="res.school=='自动识别' || !res.school">
                                            {{res.user}}<br />{{res.pass}}</span>
                                        <span v-else>{{res.school}}<br />{{res.user}}<br />{{res.pass}}</span>
                                        <button title="复制"
                                            @click="copyT(`${res.school=='自动识别'?'':res.school} ${res.user} ${res.pass}`)"
                                            class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs"
                                            style="position: absolute; right: 0; top: -2px;border: 0;">
                                            <i class="el-icon-document-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                <td style="white-space:nowrap;width:50px;">
                                    <div style="width: 140px; white-space: normal; word-break: break-all;"
                                        class="layui-input-group">
                                        <!--setOrder-->
                                        <template v-if="uid">
                                            <input style="" type="text" name="" v-model="res.kcname" :value="res.kcname"
                                                placeholder="无" class="layui-input">
                                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                                @click="setOrder(res,{kcname:res.kcname})">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </div>
                                        </template>

                                        <span v-else>{{res.kcname}}</span>



                                    </div>

                                    <button title="复制" @click="copyT(`${res.kcname}`)"
                                        class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs"
                                        style="position: absolute; right: 0; top: -2px;border: 0;">
                                        <i class="el-icon-document-copy"></i>
                                    </button>
                                </td>

                                <td style="white-space:nowrap;">
                                    <div style="width:90px;overflow-x:auto;">
                                        <el-button v-if="res.status=='待处理'" type="warning" size="small">
                                            <el-icon>
                                                <Clock />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='待上号'" type="warning" size="small">
                                            <el-icon>
                                                <Clock />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='排队中'" type="warning" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='已暂停'" type="warning" size="small">
                                            <el-icon><Video-Pause /></el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='已完成'" type="success" size="small">
                                            <el-icon><Circle-Check /></el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='已考试'" type="success" size="small">
                                            <el-icon><Circle-Check /></el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='异常'" type="danger" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='失败'" type="danger" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='密码错误'" type="danger" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='已提取'" type="primary" size="small">
                                            <el-icon>
                                                <Upload />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='已提交'" type="primary" size="small">
                                            <el-icon>
                                                <Upload />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='进行中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='上号中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='考试中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='队列中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='待考试'" type="primary" size="small">
                                            <el-icon>
                                                <Clock />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='正在考试'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='平时分'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='作业中'" type="primary" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='补刷中'" type="info" size="small">
                                            <el-icon class="is-loading">
                                                <Loading />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='已退单'" type="info" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='已退款'" type="info" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='待支付'" type="info" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='待审核'" type="info" size="small">
                                            <el-icon>
                                                <Warning />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else-if="res.status=='点我接码'" type="danger" size="small"
                                            icon="el-icon-thumb" @click="jiema">
                                            <el-icon>
                                                <Clock />
                                            </el-icon>{{res.status}}
                                        </el-button>
                                        <el-button v-else size="small">
                                            {{res.status}}
                                        </el-button>
                                    </div>
                                </td>

                                <td style="white-space:nowrap" v-if="uid">
                                    <el-button style="width: 100%;" @click="duijie(res.oid)" v-if="res.dockstatus==0"
                                        type="" size="small">等待处理</el-button>
                                    <el-button style="width: 100%;" v-if="res.dockstatus==1" type="success"
                                        size="small">OK</el-button>
                                    <el-button style="width: 100%;" @click="duijie(res.oid)" v-if="res.dockstatus==2"
                                        type="danger" size="small">Error</el-button>
                                    <el-button style="width: 100%;" v-if="res.dockstatus==3" type="info"
                                        size="small">重复下单</el-button>
                                    <el-button style="width: 100%;" v-if="res.dockstatus==4" type="info"
                                        size="small">已取消</el-button>
                                    <el-button style="width: 100%;" v-if="res.dockstatus==99"
                                        size="small">自营</el-button>
                                    <el-button style="width: 100%;" v-if="res.dockstatus==-9" type="info"
                                        size="small">未支付</el-button>
                                </td>

                                <td style="white-space:nowrap;width:50px;">
                                    <div style="width: 100px; white-space: normal; word-break: break-all;"
                                        class="layui-input-group">
                                        <!--setOrder-->
                                        <template v-if="uid">
                                            <input style="" type="text" name="" v-model="res.noun" :value="res.noun"
                                                placeholder="无" class="layui-input">
                                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                                @click="setOrder(res,{noun:res.noun})">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </div>
                                        </template>

                                        <span v-else>{{res.noun}}</span>
                                    </div>
                                </td>

                                <td style="white-space:nowrap;width:50px;">
                                    <div style="width: 90px; white-space: normal; word-break: break-all;"
                                        class="layui-input-group">
                                        <!--setOrder-->
                                        <template v-if="uid">
                                            <input style="" type="text" name="" v-model="res.hid" :value="res.hid"
                                                placeholder="无" class="layui-input">
                                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                                @click="setOrder(res,{hid:res.hid})">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </div>
                                        </template>

                                        <span v-else>{{res.hid}}</span>
                                    </div>
                                </td>

                                <td style="white-space:nowrap">
                                    <div>
                                        <el-button v-if="uid" style="margin-left: 0;" type="primary" size="small" circle
                                            @click="ddinfo(res)">
                                            <el-icon>
                                                <search />
                                            </el-icon>
                                        </el-button>
                                        <!--刷新按钮-->
                                        <el-button type="success" style="margin-left: 0;" size="small" circle
                                            @click="up(res.oid)">
                                            <el-icon><refresh-left /></el-icon>
                                        </el-button>
                                        <!--补刷按钮-->
                                        <el-button type="warning" style="margin-left: 0;" size="small" circle
                                            @click="bs(res.oid)">
                                            <el-icon>
                                                <promotion />
                                            </el-icon>
                                        </el-button>
                                    </div>

                                <td>
                                    <template
                                        v-if="( process_num(res.process)?process_num(res.process):(res.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0) )==100">
                                        <el-progress width="50" stroke-width="3" type="circle"
                                            :percentage="process_num(res.process)?process_num(res.process):(res.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0)"></el-progress>
                                    </template>
                                    <template v-else>
                                        <el-progress width="50" stroke-width="3" type="circle"
                                            :percentage="process_num(res.process)?process_num(res.process):(res.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0)"></el-progress>
                                    </template>

                                </td>

                                <td style="white-space:nowrap">
                                    <p style="width: 250px; white-space: normal;">
                                        {{res.remarks?res.remarks:'已完成：'+(
                                        process_num(res.process)?process_num(res.process):(res.status.search(/已完成|已经完成|已经全部完成/)!=
                                        -1?100:0) )+'%'}}
                                    </p>

                                    <button title="复制"
                                        @click="copyT(`${res.remarks?res.remarks:'已完成：'+( process_num(res.process)?process_num(res.process):(res.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0) )+'%'}`)"
                                        class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs"
                                        style="position: absolute; right: 0; top: -2px;border: 0;">
                                        <i class="el-icon-document-copy"></i>
                                    </button>
                                </td>
                                </td>
                                <td style="white-space:nowrap">{{res.addtime}}</td>
                                <td style="white-space:nowrap">{{res.uptime}}</td>
                                <td class="center" style="white-space:nowrap">{{res.fees}}</td>

                                <td v-if="uid" style="white-space:nowrap;">
                                    <div style="width: 160px; white-space: normal; word-break: break-all;"
                                        class="layui-input-group">
                                        <template v-if="uid">
                                            <input style="" type="text" name="" v-model="res.kcid" :value="res.kcid"
                                                placeholder="无" class="layui-input">
                                            <div class="layui-input-split layui-input-suffix" style="cursor: pointer;"
                                                @click="setOrder(res,{kcid:res.kcid})">
                                                <i class="layui-icon layui-icon-edit"></i>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td style="white-space:nowrap;">
                                    <div class="center" style="width: 70px;">
                                        <template v-if="res.type">

                                            <span class="layui-font-green">
                                                <template v-if="res.type == 'tourist'">
                                                    接单商城订单
                                                </template>
                                                <template v-else-if="res.type == 'tourist1'">
                                                    接单商城订单
                                                </template>
                                                <template v-else-if="res.type == 'axqg'">
                                                    爱学强国订单
                                                </template>
                                                <template v-else-if="res.type == 'xc'">
                                                    小储商城订单
                                                </template>
                                                <template v-else-if="res.type == 'lanyangyang'">
                                                    懒洋洋订单
                                                </template>
                                                <template v-else>
                                                    未知 | {{ res.type }}
                                                </template>
                                            </span>

                                        </template>
                                        <template v-else>
                                            <span class="layui-font-green">普通订单</span>
                                        </template>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>

                </div>

                <div style="float: right;">
                    <div class="listTable_laypage"
                        style="scale: .8; width: max-content; transform-origin: right center;"></div>
                </div>

            </div>

            <div id="ddinfo2" style="display: none;padding: 10px 15px; line-height: 30px;"><!--订单详情-->
                <li class="list-group-item">
                    <b>课程类型：</b>{{ddinfo3.info.ptname}}<span v-if="ddinfo3.info.miaoshua=='1'"
                        style="color: red;">&nbsp;秒刷</span>
                </li>
                <li class="list-group-item" style="word-break:break-all;">
                    <b>账号信息：</b>{{ddinfo3.info.school}}&nbsp;{{ddinfo3.info.user}}&nbsp;{{ddinfo3.info.pass}}</li>
                <li class="list-group-item"><b>课程名字：</b>{{ddinfo3.info.kcname}}</li>
                <li class="list-group-item" v-if="ddinfo3.info.name!='null'"><b>学生姓名：</b>{{ddinfo3.info.name}}</li>
                <li class="list-group-item"><b>下单时间：</b>{{ddinfo3.info.addtime}}</li>
                <li class="list-group-item" v-if="ddinfo3.info.courseStartTime">
                    <b>课程开始时间：</b>{{ddinfo3.info.courseStartTime}}</li>
                <li class="list-group-item" v-if="ddinfo3.info.courseEndTime">
                    <b>课程结束时间：</b>{{ddinfo3.info.courseEndTime}}</li>
                <li class="list-group-item" v-if="ddinfo3.info.examStartTime">
                    <b>考试开始时间：</b>{{ddinfo3.info.examStartTime}}</li>
                <li class="list-group-item" v-if="ddinfo3.info.examEndTime"><b>考试结束时间：</b>{{ddinfo3.info.examEndTime}}
                </li>
                <li class="list-group-item"><b>订单状态：</b><span
                        style="color: red;">{{ddinfo3.info.status}}</span>&nbsp;<button
                        v-if="ddinfo3.info.dockstatus!='99'" @click="up(ddinfo3.info.oid)"
                        class="el-button el-button--success is-plain el-button--mini">
                <li class="el-icon-refresh"></li>刷新</button>&nbsp;</li>
                <li class="list-group-item" style="display: flex; align-items: center;">
                    <b>进度：</b>
                    <div class="layui-progress layui-progress-big" lay-showpercent="true"
                        style="flex: auto; scale: 1 .7;">
                        <div class="layui-progress-bar"
                            :lay-percent="( process_num(ddinfo3.info.process)?process_num(ddinfo3.info.process):( ddinfo3.info.status?(ddinfo3.info.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0):0 ) ) +'%'">
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <b>日志：</b>{{ddinfo3.info.remarks?ddinfo3.info.remarks:'已完成：'+(
                    process_num(ddinfo3.info.process)?process_num(ddinfo3.info.process):(
                    ddinfo3.info.status?(ddinfo3.info.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0):0 ) )+'%'}}
                </li>
                <li class="list-group-item" v-if="ddinfo3.info.status!='已取消'">
                    <b>操作：</b>
                    <button @click="ms(ddinfo3.info.oid)" v-if="false" class="btn btn-xs btn-danger">秒刷</button>&nbsp;
                    <button v-if="false" @click="layer.msg('更新中，近期开放')" class="btn btn-xs btn-info">修改密码</button>&nbsp;
                    <button @click="quxiao(ddinfo3.info.oid)"
                        class="el-button el-button--danger is-plain el-button--mini ">
                        <span class="el-icon-delete"></span>取消
                    </button>
                    <button @click="tk([ddinfo3.info.oid])"
                        class="el-button el-button--danger is-plain el-button--mini ">
                        <span class=""></span>退款
                    </button>
                </li>
            </div>

        </el-card>

    </div>

    <div id="xgdl_demo" class="layui-padding-2 layui-form" style="display: none;">
        <select lay-search="" lay-append-to="body" lay-filter="xgdl_select" id="xgdl_select" placeholder="请选择或搜索">
            <option disabled selected="">请选择或搜索</option>
            <?php
            $a = $DB->query("select * from qingka_wangke_user  ORDER BY `uid` ASC");
            while ($b = $DB->fetch($a)) {
                echo '<option value="' . $b['uid'] . '">' . '【' . $b['uid'] . '】' . $b['name'] . '</option>';
            }
            ?>
        </select>
    </div>

</div>

<script type="text/html" id="listTable_user_process">
    <div class="layui-progress progressZDY" lay-showpercent="true">
        <div class="layui-progress-bar" lay-percent="{{= vm.process_num(d.process)?vm.process_num(d.process):(d.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0) }}%">
        </div>
    </div>
</script>

<script type="text/html" id="listTable_user_caoz">
    <button type="button" class="layui-btn layui-btn-xs" lay-event="listTable_user_up">
        <i class="layui-icon layui-icon-refresh"></i>
    </button>

    <button type="button" class="layui-btn layui-btn-danger layui-btn-xs" lay-event="listTable_user_bs">
        <i class="layui-icon layui-icon-release"></i>
    </button>
</script>

<script type="text/html" id="statusTemplet">
{{# if(/已完成|已考试|待考试|已学习/.test(d.status)){ }}
    <span class="layui-font-blue">{{= d.status}}</span>
    {{# } else if(/进行中|平时分|作业中|排队中|习惯分中|正在考试|考试中/.test(d.status)) { }}
        <span class="layui-font-orange">{{= d.status}}</span>
        {{# } else if(/异常|错误|已退单|失败|已退单|重刷中|补刷中|队列中|已暂停|已取消|已退款|未开始|接码/.test(d.status)) { }}
            <span class="layui-font-red">{{= d.status}}</span>
            {{# } else { }}
                {{= d.status}}
                {{# } }}
</script>

<?php include_once($root . '/index/components/footer.php'); ?>

<script>
    function copyT(text) {
        vm.copyT(text);
    }

    const app = Vue.createApp({
        data() {
            return {
                uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
                export_status: 0,
                row: {
                    data: []
                },
                phone: '',
                list: '',
                sex: [],
                ddinfo3: {
                    status: false,
                    info: []
                },
                dc: [],
                dc2: {
                    gs: '0'
                },
                cx: {
                    status_text: '',
                    dock: '',
                    qq: '',
                    oid: '',
                    uid: '',
                    cid: '',
                    kcname: '',
                    ptname: '',
                    pagesize: '15',
                },
                menuName: "3",
                studyUse_name: '1',
                status_text_zdy: '',
                dock_text_zdy: '',
                xgdl_value: '',
                dl_idname: [],
            }
        },
        mounted() {
            const _this = this;
            let loadIndex = layer.load(0);
            $("#orderlist").ready(async () => {
                layer.close(loadIndex);
                $("#orderlist").show();
                await _this.dl_idname_get();
                _this.get(1);
            })

            layui.use(function () {
                var util = layui.util;
                // 自定义固定条
                util.fixbar({
                    margin: 100
                })
            })
        },
        methods: {
            copyT(text = '') {
                const _this = this;
                navigator.clipboard.writeText(text).then(function () {
                    _this.$message.success("复制成功")
                }).catch(function (error) {
                    _this.$message.error('复制失败: ' + error)
                });
            },
            listTable_new_select(val) {
                const _this = this;
                _this.sex = val.map(i => i.oid);
            },
            process_num: function (process = 0) {
                if (parseFloat(process) == 100 || process ? process.search(/已完成|已经完成|已经全部完成/) !== -1 : 0) {
                    return 100;
                } else {
                    if (process) {
                        if (isNaN(parseFloat(process))) {
                            let match = process.match(/(\d+)\/(\d+)/);
                            if (match) {
                                return (match[1] / match[2] * 100).toFixed(2);
                            } else {
                                match2 = process.match(/(\d+(\.\d+)?)%/);
                                if (match2) {
                                    return parseFloat(match2[1]);
                                }
                                return 0
                            }
                        }
                        return parseFloat(process);
                    } else {
                        return 0;
                    }
                }
            },
            // 定义导出文件的函数
            exportFile: function (fileName, data) {
                // 将数据转换为表格形式，这里仅为示例，需要根据实际数据格式进行调整
                let tableHTML = '<table  border="1" style="font-family:微软雅黑">' + $('#listTable2').html() + '</table>';
                $('#listTable2').hide();
                // 创建Blob对象
                let blob = new Blob([tableHTML], {
                    type: "application/vnd.ms-excel"
                });
                let downloadUrl = URL.createObjectURL(blob);
                let a = document.createElement("a");
                a.href = downloadUrl;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(downloadUrl);
            },
            export_table: function () {
                const _this = this;

                if (!_this.row.data.length) {
                    _this.$message.error("无订单可导出");
                    return
                }

                _this.export_status = 1;
                layui.use(function () {

                    let table = layui.table;
                    let util = layui.util;
                    let layer = layui.layer;

                    const e_time = util.toDateString(new Date(), "yyyy_MM_dd HH_mm_ss");
                    const e_title = '订单' + e_time;
                    const fileName = e_title + '.xls'; // 定义文件名

                    layer.open({
                        type: 1,
                        title: "是否导出当前页数据",
                        content: '<div class="layui-padding-3"><span style="color:red">' + fileName + '<br/>数据合计：' + vm.row.data.length + '条</span><hr />将会为您导出当前页所有数据！<br />若需要按条件导出，请先设置好条件！</div>',
                        btn: ['导出', '取消'],
                        area: ['350px'],
                        yes: function (index) {
                            // 先初始化表格
                            // table.init('listTable2', {
                            //     title: e_title,
                            // });
                            // 获取需要导出的数据
                            // let data = table.cache['listTable2']; // 根据实际情况获取数据

                            // 导出文件
                            vm.exportFile(fileName);

                            layer.close(index);
                            _this.$message.success('导出成功');
                        },
                        end: function () {
                            _this.export_status = 0;
                        },
                    });

                })
            },
            setOrder: function (res, rows) {
                const _this = this;
                for (let i in rows) {
                    res[i] = rows[i];
                }
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=upOrder", {
                    //   data: Object.keys(this.res).map(key => key + '=' + this.res[key]).join('&')
                    data: res
                }, {
                    emulateJSON: true
                }).then(function (r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        // this.get(this.row.current_page);
                        // $("#modal-" + form).modal('hide');
                        _this.$message.success(r.data.msg);
                    } else {
                        _this.$message.error(r.data.msg);
                    }
                });
            },
            jiema: function () {
                layui.use(function () {
                    layer.confirm('接码成功过不用管这里了，会自动安排上', {
                        title: '注意事项',
                        btn: ['接码', '暂时不'],
                        closeBtn: 0
                    },
                        function () {

                            window.open('http://nz.邦瑞.net/index/weiyu')
                        })
                })
            },
            dl_idname_get() {
                const _this = this;
                return new Promise((resolve) => {
                    axios.post("/apiadmin.php?act=dl_idname").then(r => {
                        if (r.data.code == 1) {
                            _this.dl_idname = r.data.data;
                        } else {
                            _this.$message.error(r.data.msg ? r.data.msg : "获取分类数据失败")
                        }
                        resolve();
                    })
                })
            },
            get: function (page, type = '') {
                const _this = this;
                var loadIndex = layer.load(0);
                data = {
                    cx: _this.cx,
                    page
                }
                axios.post("/apiadmin.php?act=orderlist", data, {
                    emulateJSON: true
                }).then(function (r) {
                    // layer.close(load);
                    layer.close(loadIndex)
                    if (r.data.code == 1) {
                        _this.row = [];
                        if (r.data) {
                            _this.row = r.data;
                        }

                        if (!_this.uid) {
                            layui.use('table', function () {
                                var table = layui.table;

                                // 已知数据渲染
                                var inst = table.render({
                                    elem: '#listTable_user',
                                    size: 'sm',
                                    text: { none: '哦吼一条订单都没得' },
                                    data: _this.row.data,
                                    cols: [
                                        [ //标题栏

                                            {
                                                type: 'checkbox',
                                                fixed: 'left'
                                            },
                                            {
                                                field: 'status',
                                                title: '任务状态',
                                                width: 90,
                                                align: 'center',
                                                templet: '#statusTemplet'
                                            },
                                            {
                                                field: 'ptname',
                                                title: '平台',
                                                width: 160
                                            },
                                            {
                                                field: 'school',
                                                title: '学校',
                                                width: 120,
                                                templet: '{{= d.school==="自动识别"?"":d.school }}'
                                            },
                                            {
                                                field: 'user',
                                                width: 120,
                                                title: '账号',
                                                templet: `{{= d.user }}
                                                    <button title="复制" onclick="copyT('{{= d.school=="自动识别"?"":d.school }} {{= d.user }} {{= d.pass}}')" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="position: absolute; right: 0; top: -2px;border: 0;">
                                                        <i class="el-icon-document-copy"></i>
                                                    </button>
                                                `,
                                            },
                                            {
                                                field: 'pass',
                                                width: 120,
                                                title: '密码',
                                            },
                                            //     <button title="复制" @click="copyT(`${res.school=='自动识别'?'':res.school} ${res.user} ${res.pass}`)" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="position: absolute; right: 0; top: -2px;border: 0;">
                                            //     <i class="el-icon-document-copy"></i>
                                            // </button>
                                            {
                                                field: 'kcname',
                                                title: '课程',
                                                width: 150,
                                                templet: `{{= d.kcname }}
                                                    <button title="复制" onclick="copyT('{{= d.kcname}}')" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="position: absolute; right: 0; top: -2px;border: 0;">
                                                        <i class="el-icon-document-copy"></i>
                                                    </button>
                                                `,
                                            },
                                            {
                                                field: 'process',
                                                title: '进度',
                                                width: 80,
                                                templet: $("#listTable_user_process"),
                                            },
                                            {
                                                field: 'remarks',
                                                title: '日志',
                                                width: 200,
                                                templet: `{{= d.remarks?d.remarks:'已完成：'+ ( vm.process_num(d.process)?vm.process_num(d.process):(d.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0) ) +'%' }}
                                                    <button title="复制" onclick="copyT('{{= d.remarks?d.remarks:'已完成：'+ ( vm.process_num(d.process)?vm.process_num(d.process):(d.status.search(/已完成|已经完成|已经全部完成/)!= -1?100:0) ) +'%'}}')" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="position: absolute; right: 0; top: -2px;border: 0;">
                                                        <i class="el-icon-document-copy"></i>
                                                    </button>
                                                `,
                                            },
                                            {
                                                field: 'uptime',
                                                title: '更新时间',
                                                width: 145,
                                                align: 'center'
                                            },
                                            {
                                                field: 'addtime',
                                                title: '提交时间',
                                                width: 145,
                                                align: 'center'
                                            },
                                            {
                                                field: 'fees',
                                                title: '扣费',
                                                width: 60,
                                                align: 'center'
                                            },
                                            {
                                                field: '',
                                                title: '刷新 / 补刷',
                                                width: 100,
                                                align: 'center',
                                                fixed: 'right',
                                                templet: '#listTable_user_caoz'
                                            },
                                        ]
                                    ],
                                    cellExpandedMode: 'tips',
                                    even: true,
                                    page: false, // 是否显示分页
                                    done: function () {
                                        layui.use(() => { layui.element.render() })
                                    },
                                });

                                table.on('checkbox(listTable_user)', function (obj) {
                                    let checkData = layui.table.checkStatus('listTable_user').data;
                                    _this.sex = [];
                                    for (let i in checkData) {
                                        _this.sex[i] = checkData[i].oid
                                    }
                                });

                                table.on('tool(listTable_user)', function (obj) {
                                    if (obj.event === 'listTable_user_up') {
                                        vm.up(obj.data.oid)
                                    } else if (obj.event === 'listTable_user_bs') {
                                        vm.bs(obj.data.oid)
                                    }
                                });



                            });
                        }

                    } else {
                        _this.$message.error(r.data.msg);
                    }

                    if (type === 'one' || true) {
                        layui.use('table', function () {
                            var laypage = layui.laypage;
                            laypage.render({
                                elem: $(".listTable_laypage"), // 元素 id
                                count: _this.row.count, // 数据总数
                                limit: _this.row.pagesize,
                                limits: [15, 30, 50, 100, 300, 500],
                                curr: _this.row.current_page,
                                layout: ['count', 'page', 'limit'], // 功能布局
                                prev: '<i class="layui-icon layui-icon-left"></i>',
                                next: '<i class="layui-icon layui-icon-right"></i>',
                                jump: function (obj, first) {
                                    if (!first) {
                                        if (_this.cx.pagesize != obj.limit) {
                                            _this.cx.pagesize = obj.limit;
                                            _this.get(1);
                                            return
                                        }
                                        _this.cx.pagesize = obj.limit;
                                        _this.get(obj.curr);
                                    }
                                }
                            });
                        })

                    } else { }

                });
            },
            removepercent: function (text) {
                function isNumeric(value) {
                    return !isNaN(parseFloat(value)) && isFinite(value) && typeof value !== 'boolean';
                }
                if (isNumeric(text.split('%').join(""))) {
                    return text.split('%').join("");
                }
                return false;
            },
            getclass: function () {
                const _this = this;
                var load = layer.load(0);
                axios.post("/apiadmin.php?act=getclass").then(function (r) {
                    layer.close(load);
                    if (r.data.code == 1) {
                        _this.class1 = r.data;
                    } else {
                        _this.$message.error(r.data.msg);
                    }
                });

            },
            bs: function (oid) {
                const _this = this;

                let thisorder = _this.row.data.find(i => i.oid === oid);
                if (thisorder.dockstatus !== '1' || /待支付|待审核|已退款|已取消/i.test(thisorder.status)) {
                    _this.$message.error("该订单暂不支持补刷");
                    return;
                }

                let confirm = layer.confirm('建议漏看或者进度被重置的情况下使用。<br>频繁点击补刷会出现不可预测的结果<br>请问是否补刷所选的任务？', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确定补刷', '取消'] //按钮
                }, function () {
                    var load = layer.load(0);
                    $.get("/apiadmin.php?act=bs&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.up(oid);
                            _this.$message.success(data.msg);
                        } else {
                            _this.$message.error(data.msg)
                        }
                        layer.close(confirm)
                    });
                });
            },
            up: function (oid) {
                const _this = this;
                var load = layer.msg("正在同步....", {
                    icon: 6,
                    time: 0
                });
                $.get("/apiadmin.php?act=uporder&oid=" + oid, function (data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.get(vm.row.current_page);
                        setTimeout(function () {
                            for (i = 0; i < vm.row.data.length; i++) {
                                if (vm.row.data[i].oid == oid) {
                                    vm.ddinfo3.info = vm.row.data[i];
                                    return true;
                                }
                            }
                        }, 1800);
                        _this.$message.success(data.msg);
                    } else {
                        _this.$message.error(data.msg);
                    }
                });
            },
            plzt: function (sex) {
                const _this = this;
                if (_this.sex == '') {
                    _this.$message.error("请先选择订单！");
                    return false;
                }
                let confirm = layer.confirm('是否确认入队，入队后等待线程执行即可，禁止一直重复入队！20分钟内订单禁止入队，切记', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确认', '取消']
                }, function () {
                    var load = layer.load(0);
                    $.post("/apiadmin.php?act=plzt", {
                        sex: sex
                    }, {
                        emulateJSON: true
                    }).then(function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.selectAll();
                            vm.get(vm.row.current_page);
                            _this.$message.success(data.msg)
                        } else {
                            _this.$message.error(data.msg)
                        }
                        layer.close(confirm);
                    });
                });
            },
            plbs: function (sex) {
                const _this = this;
                if (_this.sex == '') {
                    _this.$message.error("请先选择订单！");
                    return false;
                }

                // 处理一下
                for (let i in sex) {
                    if (_this.row.data[i].dockstatus !== '1' || /待支付|待审核|已退款|已取消/i.test(_this.row.data[i].status)) {
                        delete sex[i]
                    }
                }
                sex = sex.filter((i) => i && i.trim());


                let confirm = layer.confirm('是否确认入队补刷，入队后等待线程执行即可，禁止一直重复入队！20分钟内订单禁止入队，切记', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确认', '取消']
                }, function () {
                    var load = layer.load(0);
                    $.post("/apiadmin.php?act=plbs", {
                        sex: sex
                    }, {
                        emulateJSON: true
                    }).then(function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.selectAll();
                            vm.get(vm.row.current_page);
                            _this.$message.success(data.msg)
                        } else {
                            _this.$message.error(data.msg)
                        }
                        layer.close(confirm);
                    });
                });
            },
            duijie: function (oid) {
                const _this = this;
                let confirm = layer.confirm('确定处理么?', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(0);
                    $.get("/apiadmin.php?act=duijie&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            _this.$message.success(data.msg);
                        } else {
                            _this.$message.error(data.msg)
                        }
                        layer.close(confirm);
                    });
                });
            },
            ms: function (oid) {
                layer.confirm('提交秒刷将扣除0.05元服务费', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(0);
                    $.get("/apiadmin.php?act=ms_order&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            _this.$message.success(data.msg);
                        } else {
                            _this.$message.error(data.msg)
                        }
                    });
                });
            },
            quxiao: function (oid) {
                layer.confirm('取消订单将无法退款，确定取消吗', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    var load = layer.load(0);
                    $.get("/apiadmin.php?act=qx_order&oid=" + oid, function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.get(vm.row.current_page);
                            _this.$message.success(data.msg);
                        } else {
                            _this.$message.error(data.msg)
                        }
                    });
                });
            },
            status_text: function (a) {
                const _this = this;
                if (!_this.sex.length) {
                    _this.$message.error("请选择订单");
                    return
                }
                if (a == '') {
                    console.log(_this.sex)
                    _this.$message.error("请输入状态");
                    return
                }
                var load = layer.load(0);
                $.post("/apiadmin.php?act=status_order&a=" + a, {
                    sex: _this.sex,
                    type: 1
                }, {
                    emulateJSON: true
                }).then(function (data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.selectAll();
                        vm.get(vm.row.current_page);
                        _this.$message.success(data.msg)
                    } else {
                        _this.$message.error(data.msg)
                    }
                });
            },
            dock: function (a) {
                const _this = this;
                var load = layer.load(0);
                $.post("/apiadmin.php?act=status_order&a=" + a, {
                    sex: _this.sex,
                    type: 2
                }, {
                    emulateJSON: true
                }).then(function (data) {
                    layer.close(load);
                    if (data.code == 1) {
                        vm.selectAll();
                        vm.get(vm.row.current_page);
                        _this.$message.success(data.msg)
                    } else {
                        _this.$message.error(data.msg);
                    }
                });
            },
            selectAll: function () {
                const _this = this;
                if (_this.sex.length == 0) {
                    for (i = 0; i < vm.row.data.length; i++) {
                        vm.sex.push(_this.row.data[i].oid)
                    }
                } else {
                    _this.sex = []
                }
            },
            ddinfo: function (a) {
                const _this = this;
                _this.ddinfo3.info = a;
                var load = layer.load(0, {
                    time: 300
                });
                setTimeout(function () {
                    layer.open({
                        type: 1,
                        title: '订单详情操作',
                        skin: 'layui-layer-demo',
                        closeBtn: 1,
                        anim: 2,
                        shadeClose: true,
                        content: $('#ddinfo2'),
                        success() {
                            setTimeout(() => {
                                layui.use(() => { layui.element.render() })
                            }, 100)
                        },
                        end: function () {
                            $("#ddinfo2").hide();
                        }
                    });
                }, 100);

            },
            tk: function (sex) {
                if (!sex || sex.length == 0) {
                    _this.$message.error("请先选择订单！");
                    return false;
                }
                layer.confirm('确定要退款吗？陛下，三思三思！！！', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确定', '取消']
                }, function () {
                    var load = layer.load(0);
                    $.post("/apiadmin.php?act=tk", {
                        sex: sex
                    }, {
                        emulateJSON: true
                    }).then(function (data) {
                        layer.close(load);
                        if (data.code == 1) {
                            vm.selectAll();
                            vm.get(vm.row.current_page);
                            _this.$message.success(data.msg);
                        } else {
                            _this.$message.error(data.msg);
                        }
                    });
                });
            },
            sc: function (sex) {
                const _this = this;
                if (_this.sex == '') {
                    _this.$message.error("请先选择订单！");
                    return false;
                }
                let layerConfirmIndex = layer.confirm('确定要删除此订单信息？', {
                    title: '温馨提示',
                    icon: 3,
                    btn: ['确定', '取消']
                }, function () {
                    var load = layer.load(0);
                    $.post("/apiadmin.php?act=sc", {
                        sex: _this.sex
                    }, {
                        emulateJSON: true
                    }).then(function (data) {
                        layer.close(load);
                        layer.close(layerConfirmIndex);
                        if (data.code == 1) {
                            vm.selectAll();
                            vm.get(vm.row.current_page);
                            _this.$message.success(data.msg);
                        } else {
                            _this.$message.error(data.msg);
                        }
                    });
                });
            },
            xgdl: function (sex) {
                const _this = this;
                if (_this.sex == '') {
                    _this.$message.error("请先选择订单！");
                    return false;
                }
                layer.open({
                    type: 1,
                    scrollbar: false,
                    title: '请选择转移给哪个代理',
                    id: 'xgdl_layer',
                    content: $("#xgdl_demo"),
                    btn: ["确认转移", '取消'],
                    success() {

                        $("#xgdl_select").val('');
                        layui.form.render();
                        layui.form.on('select(xgdl_select)', (data) => {
                            var elem = data.elem; // 获得 radio 原始 DOM 对象
                            var checked = elem.checked; // 获得 radio 选中状态
                            var value = elem.value; // 获得 radio 值
                            _this.xgdl_value = value;
                        })
                    },
                    yes(index) {
                        if (!_this.xgdl_value) {
                            _this.$message.error("请选择代理");
                            return
                        }
                        _this.xgdl_get(sex, index);
                    },
                    end() {
                        _this.xgdl_value = '';
                        $("#xgdl_select").val('');
                        layui.form.render();
                    },
                })
            },
            xgdl_get(sex, index = 0) {
                console.log(sex)
                const _this = this;
                if (_this.sex == '') {
                    _this.$message.error("请先选择订单！");
                    return false;
                }

                let load = layer.load(0);
                axios.post("/apiadmin.php?act=xgdl", {
                    sex: _this.sex,
                    uid: _this.xgdl_value,
                }).then(r => {
                    layer.close(load);
                    if (r.data.code === 1) {
                        _this.get(_this.row.current_page);
                        _this.$message.success("转移成功");
                        layer.close(index);
                    } else {
                        this.$message.error(r.msg ? r.msg : '异常请重试！');
                    }
                    _this.xgdl_value = '';
                })
            },
            changePass: function (sex) {
                const _this = this;
                if (_this.sex == '') {
                    _this.$message.error("请先选择订单！");
                    return false;
                }
                if (_this.sex.length > 1) {
                    _this.$message.error("只能选择一个订单！");
                    return false;
                }
                layer.prompt({ title: '请输入新密码' }, function (value, index, elem) {
                    if (value === '') return elem.focus();
                    layer.load(0);
                    $.post("/apiadmin.php?act=changePass", {
                        sex: sex,
                        pass: value
                    }, {
                        emulateJSON: true
                    }).then(r => {
                        layer.closeAll("loading");
                        if (r.code === 1) {
                            _this.get(_this.row.current_page);
                            _this.$message.success("修改成功");
                        } else {
                            _this.$message.error(r.msg ? r.msg : '异常请重试！');
                        }
                    })

                    // 关闭 prompt
                    layer.close(index);
                });
            },

        },
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var vm = app.mount('#orderlist');
    // -----------------------------

</script>