<?php

include_once ('../confing/common.php');
$addsalt = md5(mt_rand(0, 999) . time());
$_SESSION['addsalt'] = $addsalt;
?>

<head>
	<meta charset="utf-8">
	<title>
    	<?php 
    	    $uid =  empty($_GET['id'])?1:$_GET['id'];
    	    $uid_touristdata = $DB->get_row("select touristdata from qingka_wangke_user where uid = '{$uid}' limit 1")["touristdata"];
    	    echo empty(json_decode($uid_touristdata,true)["sitename"])?json_decode($DB->get_row("select touristdata from qingka_wangke_user where uid = '1' limit 1")["touristdata"],true)["sitename"]:json_decode($uid_touristdata,true)["sitename"];
    	?>
	</title>
	<meta name="keywords" content="<?= $conf['keywords']; ?>" />
	<meta name="description" content="<?= $conf['description']; ?>" />
	<link rel="icon" href="../favicon.ico" type="image/ico">
	<meta name="author" content=" ">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    
    <link rel="stylesheet" href="/assets/css/toc.css" media="all">

	<script src="assets/js/aes.js"></script>

    <?php include_once($root.'/index/components/jscss.php'); ?>
</head>

<style>
    #onlineStoreBody{
        background: url('index/assets/images/beijing.png');
        background-size: cover;
    }

	.el-col {
		margin-bottom: 5px;
	}

	.lioverhide {
		width: 300px
	}

	.zdyClass {
		margin-left: 0 !important;
		margin-right: 0;
		width: fit-content;
	}

	.zdyClass input {
		top: 2px;
		position: relative;
	}

	.zdyClass span {
		margin-left: 0px;
	}

	.layui-form-checkbox {
		width: -webkit-fill-available;
	}

	.layui-form-checkbox div {
		overflow: auto;
		width: -webkit-fill-available;
		text-overflow: inherit;
	}

	.layui-anim {
		animation-duration: 5s;
		-webkit-animation-duration: 5s;
	}
</style>

<body id="onlineStoreBody">
    <div id="app" style="display:none;">
    	<div class="layui-panel" style="max-width: 800px; position: relative; left: 50%; transform: translateX(-50%);box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1)">
    		<div v-if="v_ok===0" class="layui-padding-3">
    			当前商户接单商城未初始化，请到后台初始化。
    		</div>
    		<div v-else class="" style="min-height: 100vh;">
    
    			<div class="layui-padding-3" style="display: flex; align-items: center; justify-content: space-between;">
    				<div>
    					<h3>{{webConfig.sitename}}</h3>
    				</div>
    				<div >
    					<button class="layui-btn layui-btn-xs layui-btn-primary layui-border-blue" style="border:0" @click='
    					kefu()'>
    						联系客服
    					</button>
    					<?php if($conf["onlineStore_trdltz"] == 1){ ?>
    					<button v-if="uid == 1" class="layui-btn layui-btn-xs layui-btn-primary layui-border-purple" style="border:0" @click="() => window.openUrl('/index/login','new')">
    						代理登入
    					</button>
    					<?php } ?>
    				</div>
    			</div>
                
                <hr class="margin0" />
                
    			<div class="layui-tab layui-tab-brief layui-padding-2" lay-filter="test-hash" style="margin: 0;">
    				<ul class="layui-tab-title">
    					<li class="layui-this" lay-id="11">在线下单</li>
    					<li lay-id="22">进度查询</li>
    					<li lay-id="33">帮助文档</li>
    				</ul>
    				<div class="layui-tab-content" style="margin-bottom:0;padding:0">
    					<div class="layui-tab-item layui-show">
    
    						<el-row :gutter="10" style="height: auto;">
    							<el-col :xs="24" :sm="12" style="margin-bottom:10px;">
    								<div class="layui-card">
    									<div class="layui-card-header" style="display: flex; justify-content: space-between; align-items: center;">
    										<div>
    											订单提交&nbsp;
    											<button v-if="qd_notice" type="button" class="layui-btn layui-btn-primary layui-btn-xs" @click="qd_notice_open">渠道推荐</button>
    										</div>
    									</div>
    									<div class="layui-card-body" style="">
    
    										<blockquote class="layui-elem-quote layui-quote-nm layui-font-12" style="padding:5px;">
    											1. 尽量用手机号作为账号，查课失败时请多次重试！<br />
    											2. 若账号信息正确时查课失败，请尝试切换其它渠道！<br />
    										</blockquote>
    										<form class="form-horizontal devform">
    											<?php if ($conf['flkg'] == '1' && $conf['fllx'] == '1') { ?>
    												<div class="">
    													<label class="col-sm-2 control-label">项目分类</label>
    													<!--<div class="col-sm-9">-->
    													<div class="">
    														<select class="layui-select" v-model="fid" @change="fenlei(id);" style="scroll 99%; border-radius: 8px; width:100%">
    															<option value="">全部分类</option>
    															<?php
    															$a = $DB->query('select * from qingka_wangke_fenlei where status=1  ORDER BY `sort` ASC');
    															while ($rs = $DB->fetch($a)) {
    															?>
    																<option :value="<?= $rs['id']; ?>">
    																	<?= $rs['name']; ?>
    																</option>
    															<?php
    															} ?>
    														</select>
    													</div>
    												</div>
    									</div>
    								<?php } elseif ($conf['flkg'] == '1' && $conf['fllx'] == '2') { ?>
    									<div class="">
    										<!--<label class="col-sm-2 control-label"></label>-->
    										<div class="">
    											<div class="col-xs-12" style="padding:5px 0;">
    												<el-radio-group v-model="fid" size="small" @input="fenlei(fid)" style="width: 100%;">
    													<el-row :gutter="5">
    														<el-col :xs="8" :sm="8" :md="8">
    															<el-radio label="" border style="width: 100%;">全部</el-radio>
    														</el-col>
    														<el-col :xs="8" :sm="8" :md="8" v-for="item in fllist.data" :key="item.id">
    															<el-radio :label="item.id" border style="width: 100%;">{{item.name}}</el-radio>
    														</el-col>
    													</el-row>
    												</el-radio-group>
    											</div>
    										</div>
    									</div>
    								<?php } ?>
    								<div class="" style="margin-bottom:10px;">
    									<!--<label class="col-sm-2 control-label">平台</label>-->
    									<div class="">
    										<!--<select class="form-control" v-model="cid" @change="tips(cid);">-->
    										<el-select id="select" v-model="cid" @change="tips(cid)" popper-class="lioverhide" :popper-append-to-body="false" filterable placeholder="先选渠道，再选平台，支持搜索" style="scroll 99%;width:100%">
    
    											<el-option v-for="class2 in class1" :key="class2.cid" :label="class2.name+'('+class2.price+'元)'" :value="class2.cid">
    												<div style="position: relative;">
    													<div style="float: left; width: 92%; overflow: auto;">{{ class2.name }}</div>
    													<div style="color: rgb(132, 146, 166); font-size: 13px; z-index: 1; position: absolute; right: 0;">{{ class2.price}}</div>
    												</div>
    											</el-option>
    
    										</el-select>
    									</div>
    								</div>
    
    								<!--<div class="form-group" v-if="activems==true">-->
    								<!--  <label class="col-sm-2 control-label" for="checkbox1">是否秒刷</label>-->
    								<!--  <div class="col-sm-9">-->
    								<!--    <div class="checkbox checkbox-success" @change="tips2">-->
    								<!--      <input style="    margin-left: 0px;" type="checkbox" v-model="miaoshua">-->
    								<!--      <label for="checkbox1" id="miaoshua"></label>-->
    								<!--    </div>-->
    								<!--  </div>-->
    								<!--</div>-->
    								<!--单学时备注定位点  在下面的v-if里面按格式添加cid即可-->
    								<div class="form-group " v-if="nochake==1">
    									<!--<div class="col-sm-2 col-xs-3 control-label" style="margin-top:9px">份数</div>-->
    									<!--<div class="col-sm-9 col-xs-8">-->
    									<!--  <el-input-number v-model="shu" :min="1" :max="100"></el-input-number>-->
    									<!--</div>-->
    									<!--<div class="col-sm-2 col-xs-3 control-label" style="margin-top:9px">课程名称</div>-->
    									<!--<div class="col-sm-9 col-xs-8">-->
    									<!--  <el-input v-model="bei" placeholder="请输入要刷的课程" style="margin-top:9px"></el-input>-->
    									<!--</div>-->
    								</div>
    								<!--单学时备注定位点-->
    
    								<div class="" style="margin-bottom:10px;">
    									<div class="">
    
    										<div class="layui-tab layui-tab-brief" lay-filter="userType">
    											<ul class="layui-tab-title">
    												<li class="layui-this layui-font-12" lay-id="11">单账号下单</li>
    												<li class="layui-font-12" lay-id="22">批量账号下单</li>
    											</ul>
    											<div class="layui-tab-content">
    												<div class="layui-tab-item layui-show">
    													<div class="layui-form-item">
    														<input type="text" name="school" v-model="userinfo2.school" placeholder="输入学校" class="layui-input">
    													</div>
    													<div class="layui-form-item">
    														<input type="text" name="user" v-model="userinfo2.user" placeholder="输入账号" class="layui-input">
    													</div>
    													<div class="layui-form-item">
    														<input type="text" name="pass" v-model="userinfo2.pass" placeholder="输入密码" class="layui-input">
    													</div>
    												</div>
    												<div class="layui-tab-item">
    													<textarea rows="5" class="layui-textarea" style="height:140px;" v-model="userinfo" placeholder="下单格式：学校 账号 密码&#10;一行一个账号&#10;多个账号下单必须换行！&#10;格式示例：&#10;北京大学 123 123&#10;清华大学 123 123">
                                                    </textarea>
    												</div>
    											</div>
    										</div>
    
    									</div>
    								</div>
    
    								<div class="" style="margin-bottom:10px; text-align: right;" v-if="noInfo">
    									<button type="button" @click="add" value="立即提交" class="layui-btn layui-bg-blue" />
    									<i class="layui-icon layui-icon-release"></i> 提交
    									</button>
    								</div>
    								<div style="margin-bottom:10px;text-align: right;" v-if="!noInfo" class="">
    									<button type="button" @click="get" value="查询课程" class="layui-btn layui-bg-blue" />
    									<i class="layui-icon layui-icon-search"></i> 查课
    									<button class=" layui-btn " :class="nochake!=1?'layui-btn-disabled':''" style="margin-left: 6px; " type="button" @click="add" value="提交订单" /><i class="layui-icon layui-icon-release"></i> 提交</button>
    									<!--<button class="btn btn-label btn-round btn-warning" type="reset"  value="重置"><label><i class="mdi mdi-delete-empty"></i></label> 重置</button>-->
    								</div>
    								<div class="" v-if="!xdsmopen">
    									<fieldset class="layui-elem-field" v-if="content">
    										<legend style="width:auto;font-size:14px;border-bottom: 0;margin-bottom: 0;">说明</legend>
    										<div class="layui-field-box" style="padding-top: 5px;">
    											<pre v-html="content"></pre>
    										</div>
    									</fieldset>
    								</div>
    
    								</form>
    								</div>
    					</div>
    					</el-col>
    
    					<el-col :xs="24" :sm="12" style="margin-bottom:10px;" id="resultBox">
    
    						<div class="layui-card">
    							<div class="layui-card-header">
    								查询结果&nbsp;&nbsp;<span v-show="loadTime" class="layui-font-12 layui-font-green">耗时：{{ loadTime.toFixed(2)}}s</span>
    								<!--<a class="layui-btn layui-btn-primary layui-btn-primary layui-btn-xs" @click="selectAll()">全选</a>-->
    							</div>
    							<div class="layui-card-body">
    								<div v-show="!row.length" class="layui-font-green">
    									请先查课
    								</div>
    								<form v-show="row.length" class="form-horizontal devform" style="max-height: 87vh; overflow: auto;">
    									<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    										<!--<div v-for="(rs,rs_key) in row" :key="rs_key">-->
    										<!--    <div v-for="(res,res_key) in rs.data" :key="res_key" >-->
    										<!--            <input type="checkbox" :name="res.name" :title="res.name">-->
    										<!--    </div>-->
    										<!--</div>-->
    
    										<el-collapse v-model='active_rs_key' class="layui-form">
    											<el-collapse-item v-for="(rs,rs_key) in row" :key="rs_key" :name="rs_key">
    												<template slot="title">
    													<div style="display: flex; align-items: center; font-size: 14px; white-space: nowrap; overflow: hidden;height: 100%;">
    														<div @click.stop="console.log(1)">
    															<input lay-filter="selectAll" :data-key="rs_key" type="checkbox" value="0">
    														</div>
    														<div v-if="rs.msg=='查询成功'" style="margin:0 5px;">
    															<b style="color: green;">{{rs.msg}}</b>
    														</div>
    														<div v-else-if="rs.msg!='查询成功'">
    															<b style="color: red;">{{rs.msg}}</b>
    														</div>
    														<div style="overflow-x: auto; overflow-y: hidden;height: 100%;">
    															<b>{{rs.userName}}<b /> {{ rs.userinfo}}</b>
    														</div>
    
    													</div>
    												</template>
    												<div v-for="(res,res_key) in rs.data" :key="res_key" style="margin-bottom: 5px; font-size: 13px; padding-left: 10px; display: flex; align-items: center;">
    
    
    													<input type="checkbox" name="checkbox" :class="['checkboxC'+`${rs_key}`]" :title="res.name" :value="res.name" :data-data="JSON.stringify({res:res,rs:rs})" lay-filter="demo-checkbox-filter" :disabled="/错误|异常|失败/.test(res.name)||/错误|异常|失败/.test(res.id)||/错误|异常|失败/.test(res.msg)">
    
    													&nbsp;&nbsp;
    													<el-tooltip v-if="res.id" class="item" effect="dark" :content="res.id?('ID：'+res.id):''" placement="top-end">
    														<span class="layui-btn layui-btn-xs layui-btn-primary" style="border: 0;cursor: pointer;">
    															<i class="layui-icon layui-icon-eye"></i>
    														</span>
    													</el-tooltip>
    
    												</div>
    											</el-collapse-item>
    										</el-collapse>
    
    									</div>
    								</form>
    
    							</div>
    						</div>
    					</el-col>
    					</el-row>
    				</div>
    				<div class="layui-tab-item">
    					<iframe src="query" frameborder="0" style="width: 100%; height: calc(100vh - 90px);"></iframe>
    				</div>
    				<div class="layui-tab-item">
    					下个版本更新
    				</div>
    
    			</div>
    		</div>
    
                <div class="layui-padding-2" style="text-align:center;">
                    <div>
                        © 2019 ~ {{ new Date().getFullYear() }} {{webConfig.sitename}}.
                    </div>
                    <div>
                        <img style="height:20px;" src="index/assets/images/gg-bzzx1.0.gif">
                        <img style="height:20px;" src="index/assets/images/gg-cxwz1.0.jpg">
                        <img style="height:20px;" src="index/assets/images/gg-txrz1.0.jpg">
                    </div>
                </div>
                
    	    </div>
        </div>
    
        <div id="qd_notice_ID" style="display: none;color:red;white-space: pre-wrap;" class="layui-padding-3">
        	<p v-html="webConfig.notice">
        	</p>
        </div>
    
        <div class="" id="pay2" style="display: none;padding:5px 5px 10px;text-align:center">
        	<div>
        		<center style="margin:15px;">
        			<h1 style="font-size :44px;">￥{{money}}</h1>
        		</center>
        	</div>
        	<div style="display: flex; justify-content: center;">
        		<?php if ($conf['is_alipay'] == 1) { ?>
        			<!--<button type="radio" name="type" value="alipay" class="layui-btn" style="line-height: inherit;">支付宝</button><br>-->
        			<button @click="payGo('alipay')" type="radio" name="type" value="alipay" class="layui-btn layui-bg-blue" style="width: 80px;">支付宝</button><br> <?php } ?>
        		<?php if ($conf['is_qqpay'] == 1) { ?>
        			<button @click="payGo('qqpay')" type="radio" name="type" value="qqpay" class="layui-btn layui-bg-orange" style="width: 80px;">QQ</button><br> <?php } ?>
        		<?php if ($conf['is_wxpay'] == 1) { ?>
        			<button @click="payGo('wxpay')" type="radio" name="type" value="wxpay" class="layui-btn" style="width: 80px;">微信</button><br> <?php } ?>
        	</div>
        	
        </div>
    	
    	<div id="kefuID" class="layui-padding-2" style="display:none;">
    	    <div id="kefuImage">
    	    </div>
            {{webConfig.qqkefu}}
    	</div>
    
    </div>
</body>

<script src="https://cdn.bootcdn.net/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js" ></script>

<script>
// 	layui.config({
// 		base: '../../layuiadmin/' //静态资源所在路径
// 	}).extend({
// 		index: 'lib/index' //主入口模块
// 	}).use(['index', 'console']);
</script>

<script>
    const app = Vue.createApp({
        data(){
            return{
                window: window,
    			uid: "",
                webConfig: {
                    sitename: '',
                    notice: '',
                    qqkefu: '',
                },
    			payOpen: null,
    			money: 0,
    			out_trade_no: "",
    			fllist: {
    				data: [],
    			},
    			fid: '',
    			fids: [],
    			row: [],
    			shu: '',
    			bei: '',
    			nochake: 0,
    			check_row: [],
    			userTypeID: '11', // 账号模式
    			userinfo: '', // 批量账号模式
    			userinfo2: { // 单个输入框模式
    				school: '',
    				user: '',
    				pass: ''
    			},
    			cid: '',
    			miaoshua: '',
    			class1: '',
    			class3: '',
    			activems: false,
    			checked: false,
    			content: '',
    			noInfo: false,
    			has_getnoun: false,
    			user_money: 0,
    			qd_notice: <?= json_encode($conf['qd_notice_open']) ?>,
    			xdsmopen: <?= $conf['xdsmopen'] ?>,
    			loadTime: 0, // 查课加载时间
    			user_use_money: 0,
    			useMoneyAnim: false, // 扣费动画
    			cesyix: "asdasd",
    			v_ok: 0,
            }
        },
		computed: {
			active_rs_key() {
			    const _this = this;
				let active_rs_key_Data = [];
				for (let i in _this.row) {
					active_rs_key_Data.push(Number(i));
				}
				return active_rs_key_Data
			},
		},
		mounted() {
			const _this = this;

			const urlParams = new URLSearchParams(window.location.search);
			_this.uid = urlParams.get('id') ? urlParams.get('id') : 1;

            let loadIndex = layer.load(0);
			$("#app").ready(() => {
			    layer.close(loadIndex);
				$("#app").show();
				_this.v_ok_f();

				layui.element.on('tab(userType)', function(data) {
					let layId = this.getAttribute("lay-id");
					if (_this.userTypeID === layId) {
						return
					}
					_this.userTypeID = layId;

					_this.userinfo = '';
					for (let i in _this.userinfo2) {
						_this.userinfo2[i] = '';
					}

					if (layId === '11') {

					} else if (layId === '22') {

					}
				});
			})

			layui.use(function() {
				var util = layui.util;
				// 自定义固定条
				util.fixbar({
					margin: 100
				})

				var element = layui.element;
				var hashName = 'tabid'; // hash 名称
				var layid = location.hash.replace(new RegExp('^#' + hashName + '='), ''); // 获取 lay-id 值

				// 初始切换
				element.tabChange('test-hash', layid);
				// 切换事件
				element.on('tab(test-hash)', function(obj) {
					location.hash = hashName + '=' + this.getAttribute('lay-id');
				});

			})

			window.touristPageVue = _this;

		},
		methods: {
		    kefu(){ 
		      layer.open({
		          type: 1,
		          title:"客服联系方式",
					content: $('#kefuID'),
		          success(){
		          },
		      })  
		    },
			fenleiGet: function() {
				const _this = this;
				let loadIndex = layer.load(0);
				axios.post("/api/tourist.php?act=getfenlei", {
					uid: _this.uid,
				}, {
					emulateJSON: true
				}).then(r => {
					if (r.data.code === 1) {
						_this.fllist = r.data
					} else {
						layer.msg("分类获取失败，请刷新页面！");
					}
					layer.close(loadIndex);
				})
			},
			returnMethod: function(type, msg) {
				const _this = this;
				if (type) {
					layer.closeAll();
				}
				layer.msg(msg);
			},
			qingli: function() {
				vm.check_row = [];
			},
			qd_notice_open: function() {
			    const _this =this;
				if (!_this.qd_notice) {
					return;
				}
				if(!_this.webConfig.notice){
				    return
				}
				layui.use(function() {
					layer.open({
						type: 1,
						title: '公告',
						area: ['320px'], // 宽高
						content: $('#qd_notice_ID'),
						time: 20 * 1000,
						success: function(layero, index) {
							var timeNum = _this.time / 1000;
							var title = _this.title;
							setText = function(start) {
								layer.title(title + '&nbsp;&nbsp;&nbsp;&nbsp;<span class="layui-font-12"><font class="layui-font-red">' + (start ? timeNum : --timeNum) + '</font> 秒后自动关闭</span>', index);
							};
							setText(!0);
							_this.timer = setInterval(setText, 1000);
							if (timeNum <= 0) clearInterval(_this.timer);
						},
					});
				})
			},
            // 查课结果适配器，数据统一化
            kcData_adapter :function(data){
                switch (true) {
                    // 可自定义.第一个是示例
                    // defalut是智能检测,不手动适配的化基本上是走这个
                    case data.data.courseList instanceof Array:
                        let courseList = data.data.courseList;
                        data.data =[];
                        data.data =courseList;
                        console.log('asd',data.data);
                        for(let i in data.data){
                            data.data[i].name =  data.data[i].Title;
                            delete data.data[i].Title;
                            data.data[i].id =  data.data[i].CourseId;
                            delete data.data[i].CourseId;
                        }

                        break;
                    default:
                        console.log('开始数据适配')
                        for (let i in data) {
                            if (data[i] instanceof Array) {
                                data.data = data[i];
                                    console.log('匹配到数组',data.data,i)
                                if(i !== 'data'){delete data[i];}
                                for (let j in data.data) {
                                    // 课程名称 Key适配
                                    if (data.data[j].name) {
                                    }else if(data.data[j].label){
                                        data.data[j].name = data.data[j].label;
                                        delete data.data[j].label
                                    }else if(data.data[j].courseName){
                                        data.data[j].name = data.data[j].courseName;
                                        delete data.data[j].courseName
                                    }
                                    // 课程ID Key适配
                                    if(data.data[j].id){
                                        
                                    }else if(data.data[j].courseId){
                                        data.data[j].id = data.data[j].courseId;
                                        delete data.data[j].courseId
                                    }
                                }
                                break;
                            }
                        }
                        break;
                }
                return data;
            },
			get: async function(salt) {
				const _this = this;
				vm.qingli();
				_this.nochake = 0;

				// 如果没选择商品
				if (_this.cid == '') {
					layer.msg("请选择平台");
					return false;
				}

				// 单账号
				if (_this.userTypeID === '11') {
					if (!_this.userinfo2.user || !_this.userinfo2.pass) {
						layer.msg("请完善账号");
						return false;
					}
				} else {
					// 多账号
					if (_this.userinfo == '') {
						layer.msg("请完善账号");
						return false;
					}
				}

				if (_this.userTypeID === '11') {
					userinfo = `${_this.userinfo2.school} ${_this.userinfo2.user} ${_this.userinfo2.pass}`
				} else {
					userinfo = _this.userinfo.replace(/\r\n/g, "[br]").replace(/\n/g, "[br]").replace(/\r/g, "[br]");
				}
				userinfo = userinfo.split('[br]'); //分割
				userinfo = userinfo.filter(item => item !== '');

				_this.row = [];
				_this.check_row = [];

				var loadIndex = layer.msg('查课中，第1条...', {
					icon: 16,
					shade: 0.01,
					time: 0,
					tipsMore: true
				});
				_this.loadTime = 0;
				const startTime = new Date().getTime();
				for (var i = 0; i < userinfo.length; i++) {
					var info = userinfo[i];
					if (info === '') {
						continue;
					}
					var hash = getENC('<?php echo $addsalt; ?>');

					try {
                        let response = await axios.post("/api/tourist.php?act=get", {
                            cid: _this.cid,
                            userinfo: info,
                            hash
                        }, {
                            emulateJSON: true
                        });


                        let data = response.data; // 或根据实际情况调整访问响应数据的方式
                        _this.nochake = 1;

                        if (data.code == -7) {
                            let salt = getENC(data.msg);
                            // 注意：这里假设 vm.get(salt) 也返回 Promise，如果不是异步函数需要相应调整
                            await _this.get(salt);
                        } else if (data.code == -1) {
                            let msg = data.msg?(data.msg.msg ? data.msg.msg : data.msg):data.message;
                            layer.msg(msg);
                        } else {
                            layer.msg(data.msg);
                            console.log('3',data)
                            data = await _this.kcData_adapter(data);
                            
                            // 再次解析.解析数据不存在的时候
                            if(!data.data || !data.data.length){
                            console.log('4',data)
                                data.data=[{name:'异常,请重试1'}]
                            }
                            vm.row.push(data);

                            setTimeout(() => {
                                layui.form.render();
                            }, 0)

                            loadIndex = layer.msg('查课中，第' + (i + 2) + '条...', {
                                icon: 16,
                                shade: 0.01,
                                time: 0,
                                tipsMore: true
                            });

                            // 最后一个
                            if (i === userinfo.length - 1) {
                                //  llayer.closeAll(); // 关闭加载动画
                                layer.close(loadIndex); // 关闭加载动画
                                setTimeout(() => {
                                    layui.form.on('checkbox(demo-checkbox-filter)', function(data) {
                                        var elem = data.elem; // 获得 checkbox 原始 DOM 对象
                                        var checked = elem.checked; // 获得 checkbox 选中状态
                                        var value = elem.value; // 获得 checkbox 值
                                        var othis = data.othis; // 获得 checkbox 元素被替换后的 jQuery 对象
                                        let dataset_data = JSON.parse(elem.dataset.data);
                                        console.log(24, dataset_data)
                                        _this.checkResources(dataset_data.rs.userinfo, dataset_data.rs.userName, dataset_data.rs.data, dataset_data.res.name, dataset_data.res.id, dataset_data.res.state);

                                    });

                                    if ($(document).width() < 750) {
                                        console.log($('#resultBox').offset().top)
                                        $(document).scrollTop($('#resultBox').offset().top - 100);
                                    }

                                    // 监听全选
                                    layui.form.on('checkbox(selectAll)', function(data) {
                                        let userKey = data.elem.dataset.key
                                        let child = $(`.checkboxC${userKey}`)
                                        child.each(function(index, item) {
                                            item.checked = data.elem.checked;
                                        });
                                        layui.form.render('checkbox');

                                        if (data.elem.checked) {
                                            userinfo = _this.row[userKey].userinfo
                                            userName = _this.row[userKey].userName
                                            rs = _this.row[userKey].data ?_this.row[userKey].data: _this.row[userKey].children
                                            for (a = 0; a < rs.length; a++) {
                                                aa = rs[a]
                                                data = {
                                                    userinfo,
                                                    userName,
                                                    data: aa
                                                }
                                                vm.check_row.push(data);
                                            }
                                        } else {
                                            vm.check_row = [];
                                        }
                                    })

                                }, 0)

                                const endTime = new Date().getTime();
                                _this.loadTime = (endTime - startTime) / 1000;
                                layer.msg('查询成功');
                            }

                        }
                    } catch (error) {
                        console.error("请求处理异常", error);
                        // 错误处理
                    }
				}

				// layer.close(loadIndex); // 关闭加载动画

			},
			add: function() {
				const _this = this;

				layer.msg("请先完善账号信息");

				console.log('_this.check_row', _this.check_row);

				if (_this.cid == '') {
					// 如果没查课和需要课程
					if (_this.nochake != 1) {
						layer.msg("请先查课1");
						return false;
					}
				}
				// 如果没查课和需要课程
				if (_this.nochake != 1 && !_this.noInfo) {
					layer.msg("请先查课2");
					return false;
				}
				if (_this.check_row.length < 1) {

					// 如果没查课和需要课程
					if (_this.nochake != 1 && !_this.noInfo) {
						layer.msg("请先选择课程");
						return false;
					}
				}
				// 如果不需要课程
				if (_this.noInfo) {
					if (_this.userinfo) {
						_this.check_row = [{
							userinfo: _this.userinfo,
							data: {}
						}]
					} else {
						layer.msg("请先完善账号信息");
						return
					}
				}
				console.log('_this.check_row', _this.check_row);

				let loadIndex = layui.layer.msg('提交中，请耐心等待', {
					icon: 16,
					shade: 0.01,
					time: 100000000,
					offset: function() {
						var viewportWidth = $(window).width();
						var viewportHeight = $(window).height();
						var offsetX = Math.floor(viewportWidth / 2) + 'px';
						var offsetY = Math.floor(viewportHeight / 2) + 'px';
						return [offsetY, offsetX];
					}
				});
				axios.post("/api/tourist.php?act=add", {
					uid: _this.uid,
					cid: _this.cid,
					data: _this.check_row,
					shu: _this.shu,
					bei: _this.bei,
					userinfo: _this.userinfo,
					nochake: _this.nochake
				}, {
					emulateJSON: true
				}).then(function(r) {
					if (r.data.code == 1) {
						vm.qingli();
						/*_this.$message({type: 'success', showClose: true,message: r.data.msg});*/
						_this.money = r.data.need;
						_this.out_trade_no = r.data.out_trade_no;

						layer.open({
							type: 1,
							title: '<i class="layui-icon layui-icon-vercode"></i>&nbsp;&nbsp;请选择支付方式',
							// closeBtn: 0,
							area: ['300px', 'auto'],
							skin: 'layui-bg-gray', //没有背景色
							shadeClose: true,
							content: $('#pay2'),
							end: function() {
								$("#pay2").hide();
								_this.row = [];
								_this.check_row = [];
							}
						});

						_this.nochake = 0;
					} else if(r.data.code == 2){
					    layer.closeAll();
					    layer.msg('成功下单，等待审核！');
					    return
					}else {
						layer.msg(r.data.msg, {
							offset: function() {
								var viewportWidth = $(window).width();
								var viewportHeight = $(window).height();
								var offsetX = Math.floor(viewportWidth / 2) + 'px';
								var offsetY = Math.floor(viewportHeight / 2) + 'px';
								return [offsetY, offsetX];
							}
						});
						//   console.log('-55')
					}
					layer.close(loadIndex)
				});
			},

			payGo: function(payType) {
				const _this = this;

				layui.use(function() {
					_this.payOpen = layer.open({
						type: 2,
						title: '<i class="layui-icon layui-icon-vercode"></i>&nbsp;&nbsp;宝~支付后请耐心等待响应！',
						shadeClose: true,
						maxmin: true, //开启最大化最小化按钮
						area: ['98%', '98%'],
						content: '/epay/epay.php?type0=tourist&uid=' + _this.uid + '&type=' + payType + '&out_trade_no=' + _this.out_trade_no,
						end: function() {
							// location.reload();
						}
					});
				})
			},
			check888: function(userinfo, userName, rs, name) {
				const _this = this;
				var btns = document.getElementById("btns");
				var zk = document.getElementById("s1");
				var x = zk.getElementsByTagName("input");
				if (btns.checked == true) {
					for (var i = 0; i < x.length; ++i) {
						data = {
							userinfo,
							userName,
							data: rs[i]
						};
						x[i].checked = true;
						vm.check_row.push(data);
					}
				} else {
					for (var i = 0; i < x.length; ++i) {
						x[i].checked = false;
					}
					_this.check_row = []
				}
			},
			selectAll: function(accountKey) {
				const _this = this;
				console.log(_this.row[accountKey])

				if (_this.cid == '') {
					layer.msg("请先查课");
					return false;
				}
				_this.checked = !_this.checked;
				if (_this.check_row.length < 1) {
					userinfo = _this.row[accountKey].userinfo
					userName = _this.row[accountKey].userName
					rs = _this.row[accountKey].data ? _this.row[accountKey].data : _this.row[accountKey].children
					for (a = 0; a < rs.length; a++) {
						aa = rs[a]
						data = {
							userinfo,
							userName,
							data: aa
						}
						vm.check_row.push(data);
					}
				} else {
					vm.check_row = []
				}
				console.log(99, vm.check_row);
			},
			checkResources: function(userinfo, userName, rs, name, id, state) {
				const _this = this;
				for (i = 0; i < rs.length; i++) {
					if (rs[i].name == name && rs[i].id == id && rs[i].state == state) {
						aa = rs[i]
					}
				}
				console.log(51, aa)
				data = {
					userinfo,
					userName,
					data: aa
				}
				if (_this.check_row.length < 1) {
					vm.check_row.push(data);
				} else {
					var a = 0;
					for (i = 0; i < vm.check_row.length; i++) {
						console.log(6, vm.check_row[i], data)
						if (vm.check_row[i].userinfo == data.userinfo && vm.check_row[i].data.name == data.data.name && vm.check_row[i].data.id == data.data.id) {
							var a = 1;
							vm.check_row.splice(i, 1);
						}
					}
					console.log(7, vm.check_row)
					if (a == 0) {
						vm.check_row.push(data);
					}
				}
			},
			fenlei: function(id) {
				var load = layer.load(0);
				const _this = this;
				axios.post("/api/tourist.php?act=getclass", {
					uid: _this.uid,
					fenlei: id
				}, {
					emulateJSON: true
				}).then(function(r) {
					layer.close(load);
					if (r.data.code == 1) {
						_this.class1 = r.data.data;
					} else {
						layer.msg(r.data.msg, {
							icon: 2
						});
					}
				});

			},
			getclass: function() {
				var load = layer.load(0);
				const _this = this;
				axios.post("/api/tourist.php?act=getclass", {
					uid: _this.uid
				}, {
					emulateJSON: true
				}).then(function(r) {
					if (vm.qd_notice) {
						_this.qd_notice_open()
					}
					layer.close(load);
					if (r.data.code == 1) {
						_this.class1 = r.data.data;
					} else {
						layer.msg(r.data.msg, {
							icon: 2
						});
					}
				});

			},
			getnock: function(cid) {
				const _this = this;
				axios.post("/api/tourist.php?act=getnock").then(function(r) {
					if (r.data.code == 1) {
						_this.nock = r.data.data;
						for (i = 0; _this.nock.length > i; i++) {
							if (cid == _this.nock[i].cid) {
								_this.nochake = 1;
								break;
							} else {
								_this.nochake = 0;
							}
						}
					} else {
						layer.msg(r.data.msg, {
							icon: 2
						});
					}
				});

			},
			tips: function(message) {
				const _this = this;
				_this.noInfo = false;
				_this.has_getnoun = false;
				for (var i = 0; _this.class1.length > i; i++) {
					if (_this.class1[i].cid == message) {
						_this.show = true;
						_this.content = _this.class1[i].content;
						if (!_this.class1[i].getnoun) {
							_this.noInfo = true;
						}
						if (_this.class1.find(item => item.cid === vm.cid).getnoun) {
							_this.has_getnoun = true;
						}
						_this.$notify.closeAll();
						if (_this.xdsmopen) {
							_this.$notify({
								title: '说明',
								dangerouslyUseHTMLString: true,
								message: _this.class1[i].content ? _this.class1[i].content : '暂无',
								position: 'bottom-left'
							});
						}

						return false;
						if (_this.class1[i].miaoshua == 1) {
							_this.activems = true;
						} else {
							_this.activems = false;
						}
						return false;

					}

				}

			},
			tips2: function() {
				layer.tips('开启秒刷将额外收0.05的费用', '#miaoshua');

			},
			v_ok_f(){
			    const _this = this;
			    let loadIndex = layer.load(0);
    			axios.post('/api/tourist.php?act=v_ok', {
    				uid: _this.uid,
    			}, {
    				emulateJSON: true
    			}).then(r => {
    				layer.close(loadIndex);
    				if (r.data.code === 1) {
    
    					_this.v_ok = r.data.ok;
    					if (_this.v_ok) {
    						_this.fenleiGet();
    						_this.getclass();
    					    _this.webConfig = r.data.webConfig;
    					   // document.title = _this.webConfig.sitename;
    					}
    				} else {
    					_this.v_ok = 0;
    					layer.msg(r.data.msg?r.data.msg:"网络异常");
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
    var vm = app.mount('#app');
    // -----------------------------
    
</script>