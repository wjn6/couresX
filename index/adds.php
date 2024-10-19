<?php
$title='无查课交单';
require_once('head.php');
$addsalt=md5(mt_rand(0,999).time());
$_SESSION['addsalt']=$addsalt;
?>

<meta name="author" content="qingka">
<link rel="stylesheet" href="../assets/css/app.css" type="text/css" />
  <link rel="stylesheet" href="https://lib.baomitu.com/layui/2.9.6/css/layui.min.css" media="all">
<link href="assets/LightYear/css/materialdesignicons.min.css" rel="stylesheet">
<link href="assets/LightYear/css/style.min.css" rel="stylesheet"/>
    <link href="https://lib.baomitu.com/twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://lib.baomitu.com/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://lib.baomitu.com/jquery/3.6.0/jquery.min.js"></script>
<script src="//cdn.staticfile.org/layer/2.3/layer.js"></script>
<link href="https://lib.baomitu.com/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">

 <link href="https://lib.baomitu.com/element-ui/2.15.14/theme-chalk/index.min.css" rel="stylesheet">
 <script  src="https://lib.baomitu.com/element-ui/2.15.14/index.min.js"></script>
<script src="https://at.alicdn.com/t/font_1185698_xknqgkk0oph.js?spm=a313x.7781069.1998910419.40&file=font_1185698_xknqgkk0oph.js"></script>
     <div class="app-content-body ">
        <div class="wrapper-md control" id="add">
        <div class="row">	
        <div class="col-sm-12">
        	
          <!--<div class="alert alert-success" role="alert">请仔细核对账号密码课程名字，在进行批量操作</div>-->

        	
	    <div class="card panel-default">
		      <!--<div class="panel-heading font-bold bg-warning">批量交单</div>-->
		      <div class="panel-heading font-bold  bg-white">无查课交单</div>
				<div class="panel-body">
					<form class="form-horizontal devform">
						<div class="form-group">
							<label class="col-sm-1 control-label">选择平台:</label>
							<div class="col-sm-9">
							<!--<select class="form-control" v-model="cid" @change="tips(cid);">-->
							 <select class="layui-select"  v-model="cid" @change="tips(cid);"  style="    background: url('../index/arrow.png') no-repeat scroll 99%;   width:100%" >	
							 
								  <option value="">请先选择平台</option>
								  <option id="cid2" v-for="class2 in class1" :value="class2.cid">{{class2.name+'（'+class2.price+'元）'}} {{class2.mijia==1?'密价':''}}</option>							  
							</select>						
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-1 control-label">账号信息:</label>
							<div class="col-sm-9">
						    <textarea rows="8" class="layui-textarea" v-model="userinfo" placeholder=""></textarea>
						    <div style="height:5px"></div>
                            <span style="color:red;">
                            一行一条账号信息,请务必保证课程名正确，下错不退<br>
                            格式如下：<br>
                            学校 学号 密码 课程<br>
                            手机 密码 课程<br>
                            </span>
							</div>
							
						</div>
	                    <!--<div class="col-sm-offset-2 col-sm-4">-->
				  	    <div class="col-sm-offset-2  ">
				  	    		<button style="margin-left: 6px; font-size: 13px;" type="button" @click="add" value="立即提交" class="el-button el-button--primary is-round"/><i class="glyphicon glyphicon-ok"></i>  立即提交</button>
				  	    		<button style="margin-left: 6px; font-size: 13px;" type="reset" value="清空数据" class="el-button el-button--primary is-round"/><i class="mdi mdi-delete-empty"></i>  清空数据</button>
				  	    		<!--<button class="btn btn-label btn-round btn-warning" type="reset"  value="清空数据"><label><i class="mdi mdi-delete-empty"></i></label> 清空数据</button>-->
				  	    	
				  	    </div>

			        </form>
		        </div>
	        </div>
	        
	        
	        
	        
	    </div> 
	     
        
	   </div> 
	     
		 
       </div>
        <div  class="wrapper-md control" id="loglist"  style="margin-top:-35px" >
        <div  class="panel panel-default"   >
		    <!--<div class="panel-heading font-bold bg-success">提交结果</div>-->
		     <div class="panel-heading font-bold bg-white">提交结果</div>
				 <div class="panel-body">
		      <div class="table-responsive">
		        <table class="table table-striped">
		          <thead>
		              <tr>
		                  <th>ID</th>
		                  <th>平台 学校 账号 密码 课程名称</th>
		                  <!--<th>操作人ID</th>-->
		                  <!--<th>类型</th>-->
		                  <th>预计扣费</th>
		                  <th>余额</th>
		                  <!--<th>操作内容</th>-->
		                  <th>操作时间</th>
		                  <!--<th>操作IP</th>-->
		              </tr>
		          </thead>
		          <tbody>
		            <tr v-for="res in row.data">
		            	<td>{{res.id}}</td>		  
		            	<td>{{res.text}}</td>
		            	<td>{{res.money}}</td>
		            	<td>{{res.smoney}}</td>
		            	<td>{{res.addtime}}</td>
		            </tr>
		          </tbody>
		        </table>
		      </div>
		      
			     <ul class="pagination" v-if="row.last_page>1"><!--by 青卡 Vue分页 -->
			         <li class="disabled"><a @click="get(1)">首页</a></li>
			         <li class="disabled"><a @click="row.current_page>1?get(row.current_page-1):''">&laquo;</a></li>
		            <li  @click="get(row.current_page-3)" v-if="row.current_page-3>=1"><a>{{ row.current_page-3 }}</a></li>
						    <li  @click="get(row.current_page-2)" v-if="row.current_page-2>=1"><a>{{ row.current_page-2 }}</a></li>
						    <li  @click="get(row.current_page-1)" v-if="row.current_page-1>=1"><a>{{ row.current_page-1 }}</a></li>
						    <li :class="{'active':row.current_page==row.current_page}" @click="get(row.current_page)" v-if="row.current_page"><a>{{ row.current_page }}</a></li>
						    <li  @click="get(row.current_page+1)" v-if="row.current_page+1<=row.last_page"><a>{{ row.current_page+1 }}</a></li>
						    <li  @click="get(row.current_page+2)" v-if="row.current_page+2<=row.last_page"><a>{{ row.current_page+2 }}</a></li>
						    <li  @click="get(row.current_page+3)" v-if="row.current_page+3<=row.last_page"><a>{{ row.current_page+3 }}</a></li>		       			     
			         <li class="disabled"><a @click="row.last_page>row.current_page?get(row.current_page+1):''">&raquo;</a></li>
			         <li class="disabled"><a @click="get(row.last_page)">尾页</a></li>	    
			     </ul>      
		    </div>
		  </div>
		  </div>
       
    </div>
    
    

<!--<script type="text/javascript" src="assets/LightYear/js/jquery.min.js"></script>-->
<script type="text/javascript" src="https://lib.baomitu.com/perfect-scrollbar/1.4.0/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="assets/LightYear/js/main.min.js"></script>
<script src="assets/js/aes.js"></script>
  <script src="https://lib.baomitu.com/vue/2.7.7/vue.min.js"></script>
 <script src="https://lib.baomitu.com/vue-resource/1.5.3/vue-resource.min.js"></script>
 <script src="https://lib.baomitu.com/axios/1.6.7/axios.min.js"></script>



 <script type="text/javascript">
    /* 鼠标特效 */
    var a_idx = 0;
    jQuery(document).ready(function($) {
        $("body").click(function(e) {
            var a = new Array("富强","民主","文明","和谐","自由","平等","公正","法治","爱国","敬业","诚信","友善");
            var $i = $("<span />").text(a[a_idx]);
            a_idx = (a_idx + 1) % a.length;
            var x = e.pageX
              , y = e.pageY;
            $i.css({
                "z-index": 999999999999999999999999999999999999999999999999999999999999999999999,
                "top": y - 20,
                "left": x,
                "position": "absolute",
                "font-weight": "bold",
                "color": "#ff6651"
            });
            $("body").append($i);
            $i.animate({
                "top": y - 180,
                "opacity": 0
            }, 1500, function() {
                $i.remove();
            });
        });
    });
</script>

<script>

var vm=new Vue({
	el:"#add",
	data:{	
	    row:[],
	    check_row:[],
		userinfo:'',
		cid:'',
		miaoshua:'',
		class1:'',
		class3:'',
		activems:false,
	},
 		
	methods:{
	    add:function(){	 
			if(!this.cid){
				layer.msg("请先选择平台");return false;
			}
			if(!this.userinfo){
				layer.msg("请填写信息");return false;
			}
			userinfo=this.userinfo.replace(/\r\n/g, "[br]").replace(/\n/g, "[br]").replace(/\r/g, "[br]");
			userinfo=userinfo.split('[br]');//分割
			 
			
			for(var i=0;this.class1.length>i;i++){
				if(this.class1[i].cid==this.cid){
					var price=this.class1[i].price	 		
				}        	 	
			}
			
				
			kofei=price*userinfo.length;
			
			num = userinfo.length
			
			console.log(userinfo)
		 
			layer.confirm("检测到有<b style='color:red'>"+userinfo.length+"</b>条账号信息，具体扣费以提交结果为准", {title:'温馨提示',icon:3,btn: ['确定交单','取消']}, function(){
				var loading=layer.load(2);
				vm.$http.post("/apiadmin.php?act=add_pl",{cid:vm.cid,userinfo:userinfo,num:num},{emulateJSON:true}).then(function(data){
					layer.close(loading);
					if(data.data.code==1){
					    //layer.alert(data.data.msg,{icon:1,title:"提交结果"});
				 		layer.alert(data.data.msg,{icon:1,title:"提交结果"},function(){setTimeout(function(){window.location.href=""});});
					}else{
						layer.alert(data.data.msg,{icon:2,title:"提交结果"});
					}
				},function(){
					layer.close(loading);
					layer.alert("服务器错误");
				});
				
			  } 	 
			);	
	    },
	    getclass:function(){
		  var load=layer.load(2);
 			this.$http.post("/apiadmin.php?act=getclass_pl").then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.class1=data.body.data;		          			             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
	    	
	    },
        tips: function (message) {	 
        	 for(var i=0;this.class1.length>i;i++){
        	 	if(this.class1[i].cid==message){
        	 		layer.tips(this.class1[i].content, 'select', {tips: [1, '#3595CC'],time: 4000});        	 		
        	 		if(this.class1[i].miaoshua==1){
					   	 this.activems=true;
					   }else{
					   	 this.activems=false;
					   }
        	 		return false;
        	 	}        	 	
        	 }
        },
        tips2: function () {
        	layer.tips('秒刷几分钟干完，但不包平时分等内容，慎重选择', '#miaoshua',{tips:1});      	  
		  
        }    
	},
	mounted(){
		//this.scsz(100,1000,600);
		this.getclass();
	}
});


</script>

<script>

new Vue({
	el:"#loglist",
	data:{
		row:null,
		type:'' ,
		types:'',
		qq:''
	},
	methods:{
		get:function(page,a){
		  var load=layer.load(2);
		  data={page:page,type:this.type,types:this.types,qq:this.qq}
 			this.$http.post("/apiadmin.php?act=loglist1",data,{emulateJSON:true}).then(function(data){	
	          	layer.close(load);
	          	if(data.data.code==1){			                     	
	          		this.row=data.body;			             			                     
	          	}else{
	                layer.msg(data.data.msg,{icon:2});
	          	}
	        });	
		}
	},
	mounted(){
		this.get(1);
	}
});
</script>
