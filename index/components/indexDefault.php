<html>
    <head>
    	<meta charset="utf-8">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    	<title><?= $conf['sitename'] ?></title>
    	<meta name="keywords" content="<?= $conf['keywords']; ?>" />
    	<meta name="description" content="<?= $conf['description']; ?>" />
    	<link rel="icon" href="../favicon.ico" type="image/ico">
    	<meta name="author" content=" ">
        <link href="/assets/css/animate.css" rel="stylesheet">
    
    </head>

    <style>
        body{
            background: linear-gradient(45deg, #232323, #465052);
            height: 100vh;
            overflow: hidden;
        }
        .app{
            position: relative;
            height: 100%;
        }
        .mainBox{
            display: flex; align-items: center; flex-wrap: wrap;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            min-width: 90vw;
            max-width: 95vw;
            gap: .5rem;
            justify-content: center;
        }
        .leftBox{
            width:55%;
        }
        .rightBox{
            flex: auto;
            color:#ffffff;
            font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
        }
        .leftBox .imgBox{
            border-radius: 5px;
            overflow: hidden;
             box-shadow:  0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04);
        }
        .leftBox .imgBox img{
            width: 100%;
            height: auto;
            opacity: .9;
        }
        .rightBox .content{
            font-size: 1.6rem;
            margin: 0.6rem 0 2.8rem;
        }
        .rightBox .title{
            font-size: 6rem;
        }
        .rightBox .loginBox{
            width: 100%;
            text-align: right;
        }
        .rightBox .loginBox .loginBoxGo{
             border: 2px solid #ffffff;
            border-radius: 60px;
            width: max-content;
            padding: 10px 45px;
            line-height: 25px;
            font-size: 18px;
            cursor: pointer;
            text-align: right;
        }
         .rightBox .loginBox .loginBoxGo:hover{
             background: #ffffff;
             color: #555957;
             box-shadow:  0 2px 4px rgba(0, 0, 0, .12), 0 0 6px rgba(0, 0, 0, .04);
         }
         
         @media screen and (max-width: 1105px){
            .leftBox{
                margin-top: 1rem;
                width: 80%;
            }
             .rightBox .title{
                font-size: 6rem;
             }
         }
         @media screen and (max-width: 670px){
            .leftBox{
                margin-top: 1rem;
                width: 100%;
            }
         }
            
    </style>
    
    <body >
        
        <div class="app">
            <div class="mainBox">
                
                <div class="rightBox" >
                    <div class="title">
                        <?= $conf["sitename"] ?>
                    </div>
                    <div class="content" >
                        <li>
                            质保我们是专业的，专业团队为您服务~
                        </li>
                        <li>
                            驰名商标，值得信赖！
                        </li>
                    </div>
                    <div class="loginBox">
                        <div class="loginBoxGo animate__animated animate__fadeInLeft" onclick="location.href='/index'">
                            登 录
                        </div>
                    </div>
                </div>
                <div class="leftBox">
                    <div class="imgBox animate__animated animate__flipInY">
                        <img src="/assets/images/zhuanye.jpg">
                    </div>
                </div>
                
            </div>
        </div>
        
    </body>
</html>