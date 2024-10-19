<?php
$title = '帮助设置';
require_once('head.php');


if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "index.php");
    exit();
}
?>

<style>
	.layui-input-wrap {
		height: 100%;
	}
</style>

<div class="layui-padding-2" id="helpset" style="display:none;">
	<div class="layui-panel layui-padding-2">

		<div class="">
			<button type="button" class="layui-btn layui-btn-sm layui-bg-blue" @click="add_open()">
				<i class="layui-icon layui-icon-addition"></i> 添加帮助
			</button>
		</div>

		<blockquote class="layui-elem-quote layui-quote-nm layui-font-12" style="padding: 5px 10px; margin: 10px 0 0 0;">
			注意：支持HTML，将会以HTML的形式渲染！
		</blockquote>

		<div class="table" style="margin: 10px 0 0">
			<div style="display: flex; justify-content: space-between; align-items: center;">
				<div style="margin:10px 0">
					<button title="刷新" type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="helplist(1)">
						<i class="layui-icon layui-icon-refresh"></i>
					</button>
					<button title="批量删除" v-if="uid" type="button" class="layui-btn layui-btn-xs layui-btn-primary" @click="del()">
						<i class="layui-icon layui-icon-delete"></i>
					</button>
				</div>
			</div>
			<table id="listTable" layui-filter="listTable"></table>
		</div>

	</div>

	<div class="layui-panel" style="display: flex; justify-content: space-between;">
		<div></div>
		<div id="listTable_laypage" style="scale: .8;"></div>
	</div>


	<!--添加帮助弹窗-->
	<div id="add" class="layui-padding-2" style="display: none;height: -webkit-fill-available;">

		<form id="add-form" class="layui-form" action="" lay-filter="add-form" style="display: flex; flex-direction: column;height: -webkit-fill-available;">

			<div class="layui-form-item">
				<label class="layui-form-label" style="width: 50px;">标题</label>
				<div class="layui-input-block" style="margin-left: 80px;">
					<input type="text" name="title" v-model="eform.title" lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input" lay-affix="clear">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label" style="width: 50px;">状态</label>
				<div class="layui-input-block" style="margin-left: 80px;">
					<input type="checkbox" name="status" :checked="eform.status" lay-skin="switch" lay-filter="switchTest" title="显示|隐藏">
				</div>
			</div>
			<div class="layui-form-item" style="flex: auto;">
				<label class="layui-form-label" style="width: 50px;">内容</label>
				<div class="layui-input-block" style="margin-left: 80px;height: 100%;">
					<textarea type="text" name="content" v-model="eform.content" :value="eform.content" lay-verify="required" placeholder="请输入内容，支持HTML" autocomplete="off" class="layui-textarea" lay-affix="clear" style="height: 100%;"></textarea>
					<span class="layui-font-12 layui-font-green">字数：{{eform.content.trim().length}}</span>
				</div>
			</div>
			<button style="display:none;" id="add-form_reset" type="reset" class="layui-btn layui-btn-primary">重置</button>
		</form>
	</div>

</div>

<script type="text/html" id="listTable_user_caoz">
	<div style="">
		<div style="display: grid; grid-template-columns: repeat(auto-fill,30px);">
			<button title="上移" lay-event="listTable_user_up" type="button" class="layui-btn layui-btn-primary layui-border-green layui-btn-xs" style="margin:0 5px 0 0;">
				<i class="layui-icon layui-icon-up"></i>
			</button>
			<button title="下移" lay-event="listTable_user_down" type="button" class="layui-btn layui-btn-primary layui-border-green layui-btn-xs" style="margin:0 5px 0 0;">
				<i class="layui-icon layui-icon-down"></i>
			</button>
			<button title="删除" lay-event="listTable_user_del" type="button" class="layui-btn layui-btn-primary layui-border-red layui-btn-xs" style="margin:0 5px 0 0;">
				<i class="layui-icon layui-icon-delete"></i>
			</button>
			<button title="编辑" lay-event="listTable_user_edit" type="button" class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs" style="margin:0;">
				<i class="layui-icon layui-icon-edit"></i>
			</button>
		</div>
	</div>
</script>

<script type="text/html" id="table_status_templet">
{{# if(Number(d.status)) { }}
	<button type="button" class="layui-btn layui-bg-blue layui-btn-xs" lay-event="table_status_on">显示</button>
	{{# } else { }}
		<button type="button" class="layui-btn layui-btn-xs" lay-event="table_status_off">隐藏</button>
		{{# } }}
</script>

<?php include($root.'/index/components/footer.php'); ?>

<script>
    const app = Vue.createApp({
        data(){
            return{
    			uid: '<?= $userrow['uid'] ?>' === '1' ? true : false,
    			row: {
    				data: []
    			},
    			cx: {
    				pagesize: 15,
    			},
    			sex: [],
    			eform: {
    				status: 1,
    				title: '',
    				content: ''
    			},
            }
        },
		mounted() {
			
			$("#helpset").ready(()=>{
			    $("#helpset").show();
    			this.helplist(1, 'one');
    			this.table_init();
			})
		},
		methods: {
			table_init: function() {
				const _this = this;
				layui.use('table', function() {
					var table = layui.table;
					// 已知数据渲染
					var inst = table.render({
						elem: '#listTable',
						id: 'listTable',
						size: 'sm',
						text: {
							none: '哦吼一条帮助都没得'
						},
						lineStyle: ' height: auto;',
						cols: [
							[ //标题栏

								{
									type: 'checkbox',
									// 	fixed: 'left'
									hide: !_this.uid
								},
								{
									field: 'title',
									title: '标题',
									width: 120,
								},
								{
									field: 'content',
									title: '内容',
									minWidth: 200,
									escape: false,
								},
								{
									field: 'status',
									title: '状态',
									align: 'center',
									width: 60,
									templet: '#table_status_templet'
								},
								{
									field: 'uptime',
									title: '操作',
									align: 'center',
									width: 142,
									templet: '#listTable_user_caoz',
								},
								{
									field: 'readUIDS',
									title: '阅读量',
									width: 40,
									align: 'center'
								},
								{
									field: 'addTime',
									title: '添加时间',
									width: 140,
									align: 'center'
								},
								{
									field: 'upTime',
									title: '更新时间',
									width: 140,
									align: 'center',
								},
								{
									field: 'id',
									title: 'ID',
									width: 60,
									align: 'center',
									hide: !_this.uid
								},
							]
						],
						data: _this.row.data,
						cellExpandedMode: 'tips',
						//skin: 'line', // 表格风格
						even: true,
						page: false, // 是否显示分页
						limits: [5, 10, 15],
						limit: _this.row.pagesize // 每页默认显示的数量0
					});
					table.on('tool(listTable)', function(obj) {
						let data = obj.data;
						switch (obj.event) {
							case 'listTable_user_up':
								var load = layer.load(0);
								axios.post("/apiadmin.php?act=help_sort", {
									type: 'up',
									id: data.id
								}, {
									emulateJSON: true
								}).then(function() {
									layer.close(load);
									_this.helplist();
								})
								break;
							case 'listTable_user_down':
								var load = layer.load(0);
								axios.post("/apiadmin.php?act=help_sort", {
									type: 'down',
									id: data.id
								}, {
									emulateJSON: true
								}).then(function() {
									layer.close(load);
									_this.helplist();
								})
								break;
							case 'listTable_user_del':
								_this.del(data.id);
								break;
							case 'listTable_user_edit':
								_this.add_open('edit', data);
								break;
							case 'table_status_on':
								data.status = 0;
								_this.edit(data.id, data);
								break;
							case 'table_status_off':
								data.status = 1;
								_this.edit(data.id, data);
								break;
							default:
								break;
						}

					});


				})
			},
			helplist: function(page, type) {
				const _this = this;
				layui.use(function() {
					var util = layui.util;
					data = {
						cx: _this.cx,
						page: page ? page : 1,
					}
				let loadIndex = layer.load(0);
					axios.post('/apiadmin.php?act=helplist', data, {
						emulateJSON: true
					}).then(r => {
						if (r.data.code === 1) {
							if (r.data.data) {
								_this.row = r.data;
							} else {
							    _this.row.data =[];
							}
							_this.table_init();
							if (type === 'one') {
								layui.use('table', function() {
									var laypage = layui.laypage;
									laypage.render({
										elem: 'listTable_laypage', // 元素 id
										count: _this.row.count, // 数据总数
										limit: _this.row.pagesize,
										limits: [15, 30, 50, 100],
										layout: ['count', 'prev', 'page', 'next', 'limit'], // 功能布局
										prev: '<i class="layui-icon layui-icon-left"></i>',
										next: '<i class="layui-icon layui-icon-right"></i>',
										jump: function(obj, first) {
											if (!first) {
												_this.cx.pagesize = obj.limit;
												_this.helplist(obj.curr, '');
											}
										}
									});
								})

							} else {}
						} else {
							layer.msg(r.data.msg);
						}
						layer.close(loadIndex);
					})
				})
			},
			add_open: function(type, d) {
				const _this = this;
				if (type) {
					_this.eform = {
						status: Number(d.status) ? 1 : 0,
						title: d.title,
						content: d.content,
					}
				}
				layui.use(function() {
					layer.open({
						type: 1,
						content: $("#add"),
						title: type ? '编辑' : '添加',
						maxmin: true,
						area: ['90%', '95%'],
						btn: [type?'修改':'添加', '取消'],
						scrollbar: false,
						yes: function(index) {
							layui.form.submit('add-form', function(data) {
								if (type) {
									_this.edit(d.id, data.field, index);
								} else {
									_this.add(data.field, index);
								}
							})
						},
						success: function() {
							layui.form.render();
						},
						end: function() {
							for (let i in _this.eform) {
								_this.eform[i] = '';
							}
							_this.eform.status = 1;
						}
					})
				})
			},

			add: function(d, index) {
				const _this = this;
				let loadIndex = layer.load(0);
				data = {
					title: d.title.trim(),
					content: d.content.trim(),
					status: d.status ? 1 : 0
				};
				axios.post("/apiadmin.php?act=help_add", data, {
					emulateJSON: true
				}).then(r => {
					if (r.data.code === 1) {
						layer.msg('添加成功！')
						layer.close(index);
						_this.helplist(1);
					} else {
						layer.msg(r.data.msg)
					}
					layer.close(loadIndex);
				})
			},
			edit: function(id, d, index) {
				const _this = this;
				let loadIndex = layer.load(0);
				data = {
					id: id,
					data: {
						title: d.title.trim(),
						content: d.content.trim(),
						status: d.status ? 1 : 0
					}
				};
				axios.post("/apiadmin.php?act=help_up", data, {
					emulateJSON: true
				}).then(r => {
					if (r.data.code === 1) {
						layer.msg('修改成功！')
						layer.close(index);
						_this.helplist(1);
					} else {
						layer.msg(r.data.msg)
					}
					layer.close(loadIndex);
				})
			},
			del: function(id) {
				const _this = this;
				layui.use('table', function() {
					let table = layui.table;
					let checkData = table.checkStatus('listTable').data;

					if (id) {
						checkData = [{
							id: id
						}]
					} else {
						if (!checkData.length) {
							layer.msg('请选择数据');
							return
						}
					}

					layer.confirm('是否删除？', {
						title: '警告',
						btn: ['删除', '算了'] //按钮
					}, function(index) {
						let loadIndex = layer.load(0);
						_this.sex = checkData.map(item => item.id);
						axios.post('/apiadmin.php?act=help_del', {
							sex: _this.sex
						}, {
							emulateJSON: true
						}).then(r => {
							layer.close(loadIndex);
							if (r.data.code === 1) {
								_this.helplist(1);
								layer.msg('删除成功');
							} else {
								layer.msg('删除失败');
							}
							layer.close(index);
						})
					}, function() {});

				})
			},
		},
    })
    // -----------------------------
    app.use(ElementPlus)
    for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
        app.component(key, component)
    }
    var LAY_appVm = app.mount('#helpset');
    // -----------------------------
</script>