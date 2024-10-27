
<meta name="robots" content="noindex, nofollow">
    
<!--fontawesome图标-->
<link rel="stylesheet" href="/assets/toc/font-awesome.min.css?v=6.6.0&sv=<?= $conf['version'] ?>" media="all">

<!--jquery-->
<script src="/assets/toc/jquery.min.js?v=3.7.1&sv=<?= $conf['version'] ?>"></script>

<!--layui-->
<link rel="stylesheet" href="/assets/toc/layui.min.css?v=2.9.18&sv=<?= $conf['version'] ?>" media="all">
<script src="/assets/toc/layui.min.js?v=2.9.18&sv=<?= $conf['version'] ?>"></script>

<!--Vue3-->
<script src="/assets/toc/vue3.min.js?sv=<?= $conf['version'] ?>"></script>
<!--element-plus-->
<link href="/assets/toc/element-plus.min.css?sv=<?= $conf['version'] ?>" rel="stylesheet">
<script src="/assets/toc/element-plus.min.js?sv=<?= $conf['version'] ?>"></script>
<script src="/assets/toc/element-plus_icons-vue.min.js?sv=<?= $conf['version'] ?>"></script>

<!--axios-->
<script src="/assets/toc/axios.min.js?v=1.7.7&sv=<?= $conf['version'] ?>"></script>
<script src="/assets/toc/axios_toc.min.js?sv=<?= $conf['version'] ?>"></script>

<!--谷歌字体-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<?php if ($conf["fontsZDY"] == 1) { ?>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC&display=swap" rel="stylesheet">
<?php } ?>

<!--动画-->
<link rel="stylesheet" href="/assets/css/animate.css?sv=<?= $conf['version'] ?>" media="all">

<!--echarts-->
<script src="/assets/toc/echarts.min.js?v=5.5.1?sv=<?= $conf['version'] ?>"></script>

<link rel="stylesheet" href="../layuiadmin/style/admin.css?v=1.1.0&sv=<?= $conf['version'] ?>" media="all">
<link rel="stylesheet" href="/assets/css/toc.css?sv=<?= $conf['version'] ?>" media="all">

<!--nprogress-->
<link href="/assets/toc/nprogress.css?v=0.2.0&sv=<?= $conf['version'] ?>" rel="stylesheet">
<script src="/assets/toc/nprogress.js?v=0.2.0&sv=<?= $conf['version'] ?>" ></script>
<style>
    #nprogress{
        z-index: 99999999999999999999;
        position: fixed;
    }
</style>

<!--水印开关-->
<?php if ($conf['sykg'] == 1) { ?>
    <script src="assets/js/sy.js?v=1.0.0&sv=<?= $conf['version'] ?>"></script>
    <script type="text/javascript">
        $(window).on('load', () => {
            watermark('禁止截图，截图封户', '昵称 : <?= $userrow['name']; ?>', '账号:<?= $userrow['user']; ?>');
        });
    </script>
<? } ?>

<!--defaultTools-->
<script  src="/assets/toc/tool.min.js?sv=<?= $conf['version'] ?>"></script>

<script>
    $(document).ready(() => {
        NProgress.start();
        $(window).on('load', () => {
            NProgress.done();
        });
        setTimeout(() => {
            NProgress.done();
        }, 600);
    });
</script>
    
<!--检测主题切换-->
<script>
    $(document).ready(()=>{
        if (localStorage.getItem('theme') === null) {
            let themesData_default = <?= $conf['themesData'] ?>.filter(i=>i.id == '<?= $conf['themesData_default'] ?>' )[0];
            localStorage.setItem('theme', JSON.stringify({
                id: themesData_default.id,
                url: themesData_default.url
            }));
        }
        let themeLinkClassEl = document.querySelectorAll('link.themeLink');
        if(!themeLinkClassEl.length){
            let new_themeLink = document.createElement('link');
            new_themeLink.classList.add('themeLink');
            new_themeLink.rel = 'stylesheet';
            new_themeLink.media = 'all';
            new_themeLink.href = JSON.parse(localStorage.getItem('theme')).url;
            document.head.appendChild(new_themeLink);
        }
        window.addEventListener('storage', function(event) {
            if(event.key == 'theme'){
                let themeLinkClassEl = document.querySelectorAll('link.themeLink');
                if(!themeLinkClassEl.length){
                    let new_themeLink = document.createElement('link');
                    new_themeLink.classList.add('themeLink');
                    new_themeLink.rel = 'stylesheet';
                    new_themeLink.media = 'all';
                    new_themeLink.href = JSON.parse(localStorage.getItem('theme')).url;
                    document.head.appendChild(new_themeLink);
                }
                themeLinkClassEl.forEach(function(link) {
                    link.href = JSON.parse(event.newValue).url;
                });
            }
        });
    })
</script>

<!--自定义-->
<?php
if ($conf["webVfx_open"] == '1') {
    preg_match_all('/<link.*?rel="stylesheet".*?>/', $conf["webVfx"], $matches);
    echo implode('', $matches[0]);
}
?>

<!--预制css-->
<style>
    html {
        font-family: <?= $conf['fontsFamily'] ?>;
        font-weight: 400;
        font-style: normal;
    }

    body {
        font-family: <?= $conf['fontsFamily'] ?>;
        font-weight: 400;
        font-style: normal;
        min-height: 100vh;
    }

    .layui-icon {
        font-family: layui-icon, inherit !important;
    }

    .layui-code {
        font-family: inherit;
    }

    .layui-fixbar {
        width: max-content;
        height: max-content;
        bottom: 10px;
    }

    .layui-fixbar .layui-fixbar-top {
        background: transparent;
        color: #2F363C;
    }


    .el-select-dropdown__item {
        max-width: 92vw;
        overflow: auto;
        text-overflow: unset;
    }

    .layui-icon {
        font-size: inherit;
    }
    
    .layui-tab-bar .layui-icon{
        font-size: 16px !important;
    }

    .el-notification__group {
        flex: auto;
    }
    
    .layui-table-view .layui-table[lay-size=sm] .layui-table-cell{
        height: 40px;
        line-height: 30px;
    }
</style>