<!--iconPreview.php-->
<?php
$mod = 'blank';
$title = '内置图标预览';

include_once('../../confing/common.php');
include_once('jscss.php');
if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "/index.php");
    exit();
}
?>

<div id="iconPreviewEL" class="layui-padding-2" style="display: none;">
    <div class="layui-panel layui-padding-2">
        2
    </div>
</div>

<script>
    iconPreviewVM = new Vue({
        el: '#iconPreviewEL',
        data: {
            a: 22
        },
        mounted(){
            const _this = this;
            let loadIndex = layer.load(0);
            $("#iconPreviewEL").ready(()=>{
                
                layer.close(loadIndex);
                $("#iconPreviewEL").show();
                
            })
        },
        methods: {
            
        },
    })
</script>