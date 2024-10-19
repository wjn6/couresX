<!DOCTYPE html>
<html lang="zh-cn">

<head>
	<meta charset="utf-8">
	<title>CourseX 模板 | 安装程序</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">

	<link rel="stylesheet" href="../layuiadmin/style/admin.css?v=3" media="all">
    
</head>

<script src="/assets/toc/jquery.min.js?v=3.7.1"></script>

<script src="/assets/toc/vue.min.js?v=2.7.15"></script>
<script src="/assets/toc/vue-resource.min.js?v=1.5.3"></script>

<link href="/assets/toc/element-ui.min.css?v=2.15.14" rel="stylesheet">
<script src="/assets/toc/element-ui.min.js?v=2.15.14"></script>

<link rel="stylesheet" href="/assets/toc/layui.min.css?v=2.9.13" media="all">
<script src="/assets/toc/layui.min.js?v=2.9.13"></script>

<style>
    i,.layui-icon{
        font-size: inherit;
    }
</style>
    
<body style="background: #ffffff;min-height:100vh;">
	<div id="hT" style="padding: 20px 0 20px 0; border-bottom: 1px solid #eee; position: fixed; top: 0; left: 0; width: 100%; background: #Ffffff; z-index: 100000;">
		<h2 class="layui-font-blue" style="text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px;">
		    <img src="/assets/images/CourseXLogo.png" width="30">
		    CourseX Template | Installer <br >已开源
		 </h2>
	</div>
	<div class="layui-padding-2" id="installID" style="display:none;":style="{paddingTop: $('#hT').innerHeight()+'px !important'}">
		<div class="" style="display: flex; justify-content: center;">
			<div class="layui-card layui-padding-2" style="width:100%;max-width:750px;">
				<div>
				    <span class="layui-font-red layui-font-12">
				        <i class="layui-icon layui-icon-tips"></i> 不提供货源、不破解程序，仅作为商城类项目开发学习！已开源，请勿用于违法行为或商业行为！
				    </span>
				    <br />
				</div>

				<el-steps :active="stepsActive" finish-status="success" simple style="margin:10px 0 15px;">
					<el-step title="环境检测" icon="el-icon-search"></el-step>
					<el-step title="数据配置" icon="el-icon-coin"></el-step>
					<el-step title="安装结果" icon="el-icon-check"></el-step>
				</el-steps>

				<div v-if="stepsActive === 0">
					<button class="layui-btn layui-btn-sm layui-bg-red" @click="location.reload()">
						重新检测
					</button>
					<p class="layui-font-red">
						{{ huanjing.some(i=>i.d === 0)?"存在没通过的环境，请修复后重新检测！":"" }}
					</p>
					<table class="layui-table">
						<tr>
							<th>
								环境
							</th>
							<th style="text-align: center;">
								所需
							</th>
							<th>
								当前
							</th>
							<th>
								状态
							</th>
						</tr>
						<tr v-for="(item,index) in huanjing" :key="index">
							<td style="width: 100px;">
								<span v-html="item.a"></span>
							</td>
							<td style="width: 50px;text-align: center;">
								<span v-html="item.b"></span>
							</td>
							<td>
								<template v-if="item.d">
									<span v-html="item.c"></span>
								</template>
								<template v-else>
									<span class="layui-font-red">{{ item.c }}</span>
								</template>
							</td>
							<td style="width: 30px;text-align: center;">
								<i class="layui-icon" :class="[item.d == 1?'layui-icon-success layui-font-orange':'layui-icon-error layui-font-red']"></i>
							</td>
						</tr>
					</table>
					<button class="layui-btn layui-btn-sm layui-bg-blue" :disabled="huanjing.some(i=>i.d === 0)" :class="huanjing.some(i=>i.d === 0)?'layui-btn-disabled':''" style="float:right" @click="stepsActive=1;mysql_is()">
						Next →
					</button>
				</div>

				<form v-if="stepsActive === 1" class="layui-form" action="" @submit.prevent="add">
                    
					<fieldset class="layui-elem-field">
						<legend>服务器信息</legend>
						<div class="layui-field-box">
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">服务器IP</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="serverIP" v-model="addForm.web.serverIP" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear" placeholder="请输入当前服务器IP">
								</div>
							</div>
						</div>
					</fieldset>
					<fieldset class="layui-elem-field">
						<legend>数据库信息</legend>
						<div class="layui-field-box">
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">数据库服务器</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="host" v-model="addForm.mysql.host" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">数据库端口</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="port" v-model="addForm.mysql.port" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
							<!--<div class="layui-form-item">-->
							<!--	<label class="layui-form-label" style="width:86px;">数据库表前缀</label>-->
							<!--	<div class="layui-input-block" style="margin-left: 140px;">-->
							<!--		<input type="text" name="qz" v-model="addForm.mysql.qz" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input">-->
							<!--	</div>-->
							<!--</div>-->
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">数据库名</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="dbname" v-model="addForm.mysql.dbname" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">数据库用户名</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="user" v-model="addForm.mysql.user" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">数据库密码</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="pwd" v-model="addForm.mysql.pwd" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
						</div>
					</fieldset>
					<fieldset class="layui-elem-field">
						<legend>网站信息</legend>
						<div class="layui-field-box">
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">站点名称</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="sitename" v-model="addForm.web.sitename" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">管理员账户</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="user" v-model="addForm.web.user" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">管理员密码</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="pass" v-model="addForm.web.pass" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
							<div class="layui-form-item">
								<label class="layui-form-label" style="width:86px;">管理二验密码</label>
								<div class="layui-input-block" style="margin-left: 140px;">
									<input type="text" name="verification" v-model="addForm.web.verification" lay-verify="required" autocomplete="off" lay-affix="clear" class="layui-input" lay-affix="clear">
								</div>
							</div>
						</div>
					</fieldset>
					<div class="layui-form-item">
						<div class="layui-input-block" style="text-align: right;">
							<button type="submit" class="layui-btn layui-btn-sm layui-bg-blue" lay-submit>Save</button>
						</div>
					</div>

				</form>

				<div class="layui-font-16" v-if="stepsActive === 2">
					<h2><i class="layui-icon layui-icon-face-smile"></i> 恭喜！模板程序安装成功！</h2>
					<h4>安装程序已卸载！</h4>
					<hr />
					<p style="margin-bottom: 3px;">网址：<a style="text-decoration: underline;" :href="'//'+location.host">{{location.host}}</a></p>
					<p style="margin-bottom: 3px;">管理员账号：{{addForm.web.user}}&nbsp;&nbsp;&nbsp;&nbsp;管理员密码：{{addForm.web.pass}}</p>
					<p style="margin-bottom: 3px;">管理员二次验证密码：{{addForm.web.verification}}</p>
					<hr />
					<br>
					<hr />
					<br>
					<div>
						<h2 class="layui-font-red">
						    <i class="layui-icon layui-icon-flag"></i> 等等，先别急着关闭！
						</h2>
						<br>
						<fieldset class="layui-elem-field">
							<legend class="layui-font-14">
								<h4>伪静态</h4>
							</legend>
							<div class="layui-field-box">
								<pre class="layui-code code-demo" lay-options="{}">
location / {
    try_files $uri $uri/ @rewrite;
}

location @rewrite {
    rewrite ^/(.*)$ /$1.php$is_args$args last;
}</pre>
							</div>
						</fieldset>
						<fieldset class="layui-elem-field">
							<legend class="layui-font-14">
								<h4>定时任务</h4>
							</legend>
							<div class="layui-field-box">
								<table class="layui-table" lay-even>
									<tr>
										<td>
											定时对接提交
										</td>
									</tr>
									<tr>
										<td>
											//{{location.host}}/redis/redis_addru.php
										</td>
									</tr>
									<tr>
										<td>
											定时同步进度
										</td>
									</tr>
									<tr>
										<td>
											//{{location.host}}/redis/redis_ru.php
										</td>
									</tr>
								</table>
							</div>
						</fieldset>
						<fieldset class="layui-elem-field">
							<legend class="layui-font-14">
								<h4>多线程Redis</h4>
							</legend>
							<div class="layui-field-box">
								<div class="layui-font-red">先安装【进程守护管理器】,PHP命令版本随便选，网站PHP版本7.2+，但你要保证在对应PHP里安装reids扩展</div>
								<table class="layui-table" lay-even>
									<tr>
										<td>
											定时对接提交
										</td>
									</tr>
									<tr>
										<td>
											启动命令：php redis_addchu.php
										</td>
									</tr>
									<tr>
										<td>
											定时同步进度
										</td>
									</tr>
									<tr>
										<td>
											启动命令：php redis_chu.php
										</td>
									</tr>
									<tr>
										<td>
											批量同步
										</td>
									</tr>
									<tr>
										<td>
											启动命令：php pltb.php
										</td>
									</tr>
									<tr>
										<td>
											批量补刷
										</td>
									</tr>
									<tr>
										<td>
											启动命令：php plbs.php
										</td>
									</tr>
								</table>
							</div>
						</fieldset>
					</div>
				</div>

			</div>
		</div>
	</div>
</body>

<script>
	vm = new Vue({
		el: '#installID',
		data: {
			isok: false,
			mysql_status: false,
			addForm: {
				mysql: {
					host: "localhost",
					port: "3306",
					dbname: "",
					user: "",
					pwd: "",
				},
				web: {
					sitename: "TOC",
					user: "admin",
					pass: "123456",
					verification: "123456",
					authcodes: "",
					serverIP: <?=  json_encode(GetHostByName($_SERVER['SERVER_NAME'])) ?>,
				}
			},
			stepsActive: 0,
			huanjing: [{
					a: "Mysql",
					b: "≥ 5.0",
					c: <?php echo function_exists('exec')?json_encode(trim(explode(' ', exec('mysql -V'))[5], ',')):json_encode("请先解禁exec函数") ?>,
					d: `<?= version_compare(trim(explode(' ', exec('mysql -V'))[5], ','), '5.0') >= 0 ? 1 : 0 ?>`,
    			},{
					a: "PHP",
					b: "≥ 7.2<br />< 8.0",
					c: <?php echo json_encode(phpversion()) ?>,
					d: <?= version_compare(phpversion(), '7.2','>=') && version_compare('8.0',phpversion(),"<") >= 0 ? 1 : 0 ?>,
				},
				{
					a: "PHP redis扩展",
					b: "安装",
					c: <?php
						// 调用phpinfo()函数
						ob_start(); // 开启输出缓冲
						phpinfo(INFO_MODULES);
						$phpinfo = ob_get_clean(); // 获取输出缓冲并清空

						echo json_encode(strpos($phpinfo, 'redis') !== false ? "已安装" : "未安装") ?>,
					d: <?php
						// 调用phpinfo()函数
						ob_start(); // 开启输出缓冲
						phpinfo(INFO_MODULES);
						$phpinfo = ob_get_clean(); // 获取输出缓冲并清空

						echo strpos($phpinfo, 'redis') !== false ? 1 : 0 ?>,
				},
				{
					a: "CURL",
					b: "加载",
					c: <?php echo json_encode(extension_loaded('curl') ? "已加载" : "未加载") ?>,
					d: <?= extension_loaded('curl') ? 1 : 0 ?>,
				},
				{
				    a: "exec函数",
				    b: "未禁用",
				    c: <?php echo json_encode(function_exists('exec')?'可用':'被禁用') ?>,
					d: <?= function_exists('exec') ? 1 : 0 ?>,
				},
				{
				    a: "shell_exec函数",
				    b: "未禁用",
				    c: <?php echo json_encode(function_exists('shell_exec')?'可用':'被禁用') ?>,
					d: <?= function_exists('shell_exec') ? 1 : 0 ?>,
				},
			],
		},
		watch: {
        'addForm.mysql.dbname': 'checkValues',
        'addForm.mysql.user': 'checkValues',
        'addForm.mysql.pwd': 'checkValues',
      },
		mounted() {
		    const _this=this;
			localStorage.clear();
			$("#installID").ready(()=>{
			    $("#installID").show();
			    layui.use(()=>{
        			layui.code({
                        elem: '.code-demo'
                    });
			    })
			})
		}, 
		methods: { 
		    checkValues: function(){
		       if (this.addForm.mysql.dbname !== '' && this.addForm.mysql.user !== '' && this.addForm.mysql.pwd !== '') {
                    this.mysql_is();
                  }
		    },
		    mysql_is: function(){
				const _this = this;
				layer.load(0);
	        	this.$http.post('api.php?act=mysql_is', _this.addForm, {
				    emulateJSON: true
				}).then(r=>{
				    layer.closeAll("loading");
				    if(r.body.code===1){
				        let mysql = r.body.data;
				        _this.addForm.mysql_status = true;
				        _this.addForm.mysql.host = mysql.host;
				        _this.addForm.mysql.port = mysql.port;
				        _this.addForm.mysql.dbname = mysql.dbname;
				        _this.addForm.mysql.user = mysql.user;
				        _this.addForm.mysql.pwd = mysql.pwd;
				        
				        _this.addForm.web.authcodes = mysql.authcodes;
				        _this.addForm.web.sitename = mysql.sitename;
				        _this.addForm.web.user = mysql.admin_user;
				        _this.addForm.web.pass = mysql.admin_pass;
				        _this.addForm.web.verification = mysql.verification;
				        _this.addForm.web.serverIP = mysql.serverIP === <?=  json_encode(GetHostByName($_SERVER['SERVER_NAME'])) ?>?mysql.serverIP:<?=  json_encode(GetHostByName($_SERVER['SERVER_NAME'])) ?>;
				    }
				})
		    },
			add: function() {

				console.log('开始保存配置');
				const _this = this;
				var loadIndex = layer.msg('部分配置保存中...', {
					icon: 16,
					shade: 0.01,
					time: 0,
				});
				this.$http.post('api.php?act=mysql_c', _this.addForm, {
					emulateJSON: true
				}).then((r) => {
					if (r.body.code === 444) {
						layer.msg(r.body.msg);
						return;
					}
					if (r.body.code === 1) {
						setTimeout(() => {
							layer.msg('配置成功，准备检测连通性...');
							setTimeout(() => {
								_this.v_ping();
							}, 1000)
						}, 1000)
					} else {
						layer.msg('网络异常，请重试...');
					}
				})
				return false;
			},
			v_ping: function() {
				console.log('开始检测Mysql连通性');
				const _this = this;
				var loadIndex = layer.msg('检测Mysql连通性中...', {
					icon: 16,
					shade: 0.01,
					time: 0,
				});
				this.$http.post('api.php?act=mysql_ping', _this.addForm, {
					emulateJSON: true
				}).then((r) => {
					if (r.body.code === 444) {
						layer.msg(r.body.msg);
						return;
					}
					if (r.body.code === 1) {
						setTimeout(() => {
							layer.msg('连通成功...');
							layui.use(function() {
								layer.open({
									title: 'Install Tips',
									content: '是否开始生成相关数据表?<br />注意：为避免意外，请注意备份原数据库！<hr /><span class="layui-font-blue">增量更新</span>：为原表增加参数，不会删除原有数据和原有参数！<br /><span class="layui-font-blue">全新覆盖</span>：原表的所有参数都会被重新生成，数据也会删除！<hr /><span class="layui-font-red">新网站推荐 → 全新覆盖</span>',
									btn: ['增量更新', '全新覆盖'],
									btn1: function(index) {
										console.log('增量安装')
										_this.sc('xz');
										layer.close(index);
									},
									btn2: function(index) {
										console.log('全新覆盖')
										_this.sc('fg');
										layer.close(index);
									},
								})
							})
						}, 1000)
					} else {
						layer.msg('数据库不存在，请先自行创建数据库...');
					}
				})
			},
			sc: function(type) {
				console.log('开始生成数据库');
				const _this = this;
				var loadIndex = layer.msg('生成相关表中，请稍等...', {
					icon: 16,
					shade: 0.01,
					time: 0,
				});
				_this.addForm.type = type;
				_this.$http.post('api.php?act=mysql_sc', _this.addForm, {
					emulateJSON: true
				}).then((r) => {
					if (r.body.code === 444) {
						layer.msg(r.body.msg);
						return;
					}
					if (r.body) {
						layer.msg('安装成功！');
						_this.stepsActive = 2;
						$("html,body").animate({
							scrollTop: 0
						}, 500);
						_this.isok = true;
					} else {
						layer.msg('异常，请重试！');
					}
				})
			},
		}
	})
</script>

</html>