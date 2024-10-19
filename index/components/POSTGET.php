<?php
include_once('../../confing/common.php');
include_once('jscss.php');
if ($userrow['uid'] != 1) {
    alert("您的账号无权限！", "/index.php");
    exit();
}
// 控制权限
if ($islogin != 1 || $userrow['uid'] != "1") {
    exit("<script language='javascript'>window.location.href='//" . $_SERVER['HTTP_HOST'] . "/index';</script>");
}


?>

<!DOCTYPE html>
<html lang="cn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在线POST\GET</title>

    <style>
        .layui-card {
            margin-bottom: 20px;
        }

        .layui-card-body .layui-card {
            margin-bottom: 0;
        }

        /* 移除请求头盒子下方的外边距 */
        .layui-card-body .layui-card .layui-card-body {
            padding: 10px;
        }

        /* 设置请求头盒子内部的 padding */
        .status-code {
            font-size: 12px;
        }

        @media (max-width: 768px) {

            /* 手机页面 */
            .layui-input-inline {
                margin: 0 0 10px 0 !important;
            }

            /* 在手机页面上消除layui-input-inline的右侧外边距 */
        }

        .layui-form-label {
            width: 45px;
        }

        .layui-input-block {
            margin-left: 76px;
        }

        .expand-collapse-btn {
            cursor: pointer;
            float: right;
            margin-top: -5px;
        }

        .layui-row {
            border-bottom: 1px solid #efefef;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div id="app" class="layui-container" style="display:none;padding:0">
        <div class="layui-padding-2" style="max-width: 600px; position: relative; left: 50%; top: 50%; transform: translate(-50%, 0%);">
            <div class="layui-row">
                <div class="layui-col-md12 layui-col-xs12">
                    <div class="layui-card">
                        <div class="layui-card-header">请求信息</div>
                        <div class="layui-card-body layui-padding-3">
                            <div class="layui-form-item">
                                <div style="display: flex;">
                                    <div class="layui-input-split layui-input-prefix" style="padding: 0;position: inherit;width: auto;">
                                        <select v-model="method" lay-verify="required" class="layui-select">
                                            <option value="GET">GET</option>
                                            <option value="POST">POST</option>
                                            <!-- 可以根据需要添加其他请求方式 -->
                                        </select>
                                    </div>
                                    <div style="flex:auto;">
                                        <input type="text" v-model="url" class="layui-input" placeholder="请输入接口地址">
                                    </div>
                                    <div style="width: auto;">
                                        <button class="layui-btn layui-bg-blue" @click="sendRequest" :disabled="requesting">发送</button>
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">Cookie</label>
                                <div class="layui-input-block">
                                    <input type="text" v-model="cookie" class="layui-input">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">请求头
                            <span class="expand-collapse-btn" @click="toggleHeaders">[{{ headersExpanded ? '折叠' : '展开' }}]</span>
                        </div>
                        <div class="layui-card-body layui-padding-3" :style="{ display: headersExpanded ? 'block' : 'none' }">
                            <div class="layui-form-item" v-for="(header, index) in headers" :key="index">
                                <div class="layui-input-inline">
                                    <input type="text" v-model="headers[index].key" class="layui-input" placeholder="键">
                                </div>
                                <div class="layui-input-inline">
                                    <input type="text" v-model="headers[index].value" class="layui-input" placeholder="值">
                                </div>
                                <div class="layui-input-inline" style="width: max-content;">
                                    <button class="layui-btn layui-btn-danger layui-btn-sm" @click="removeHeader(index)" :disabled="headers.length === 1 && index === 0">删除</button>
                                </div>
                            </div>
                            <button class="layui-btn layui-btn-primary" @click="addHeader">添加头</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">
                            请求参数
                            <button class="layui-btn layui-btn-sm" @click="addParam">添加参数</button>
                            <button class="layui-btn layui-btn-sm layui-btn-primary" @click="clearParams">重置</button>
                        </div>
                        <div class="layui-card-body layui-padding-3">
                            <div class="layui-form-item" v-for="(param, index) in params" :key="index" style="display: flex; gap: 8px; align-items: center;">
                                <div class="">
                                    <input type="text" v-model="param.key" class="layui-input" placeholder="键">
                                </div>
                                <div class="">
                                    <input type="text" v-model="param.value" class="layui-input" placeholder="值">
                                </div>
                                <div class="layui-input-inline" style="width: max-content;">
                                    <button class="layui-btn layui-btn-danger layui-btn-sm" @click="removeParam(index)" :disabled="params.length === 1 && index === 0">删除</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-row">
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">
                            响应结果
                            <button class="layui-btn layui-btn-primary layui-btn-sm layui-font-12" @click="copyResponse">复制</button>
                        </div>
                        <div class="layui-card-body layui-font-12" style="display: flex; gap: 15px;padding:0 15px">
                            <div>状态码: <span :class="{'status-code': true, 'blue': statusCode === 200, 'red': statusCode !== 200}">{{ statusCode }}</span></div>
                            <div>耗时: <span class="status-code" v-if="elapsedTime">{{ elapsedTime }}秒</span></div>
                            <div>大小: <span class="status-code" v-if="size">{{ size }}kb</span></div>
                        </div>
                        <div v-show="response" style="margin-top: 10px;">
                            <pre class="layui-code code-demo " lay-options="{}" v-html="response">

                            </pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const app = Vue.createApp({
            data() {
                return {
                    url: '',
                    method: 'POST',
                    params: [{
                        key: '',
                        value: ''
                    }],
                    headers: [{
                            key: 'Accept',
                            value: '*/*'
                        },
                        {
                            key: 'Accept-Encoding',
                            value: 'gzip, deflate, br'
                        },
                        {
                            key: 'User-Agent',
                            value: 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.25 Safari/537.36 Core/1.70.3741.400 QQBrowser/10.5.3863.400'
                        },
                        {
                            key: 'Connection',
                            value: 'keep-alive'
                        },
                        {
                            key: 'Content-Type',
                            value: 'application/x-www-form-urlencoded'
                        }
                    ],
                    cookie: '',
                    response: '',
                    statusCode: '',
                    elapsedTime: '',
                    size: '',
                    requesting: false, // 是否正在请求中
                    requestError: '', // 请求失败的错误信息
                    headersExpanded: false // 请求头部分展开状态
                }
            },
            mounted() {
                layui.use(function() {
                    // code
                    layui.code({
                        elem: '.code-demo'
                    });
                })

                const _this = this;

                let loadIndex = layer.load(0);
                $("#app").ready(() => {
                    layer.close(loadIndex);
                    $("#app").show();
                })

            },
            methods: {
                sendRequest() {
                    const _this = this;
                    // 请求开始，设置请求状态
                    _this.requesting = true;
                    _this.requestError = '';

                    const startTime = new Date().getTime();
                    let requestData = {
                        url: _this.url,
                        method: _this.method,
                        params: _this.params.reduce((acc, curr) => {
                            if (curr.key) {
                                acc[curr.key] = curr.value;
                            }
                            return acc;
                        }, {}),
                        headers: _this.headers.reduce((acc, curr) => {
                            if (curr.key && curr.value) {
                                acc[curr.key] = curr.value;
                            }
                            return acc;
                        }, {}),
                        cookie: _this.cookie
                    };
                    var loadIndex = layer.msg('请求中...', {
                        icon: 16,
                        shade: 0.01,
                        time: 0,
                    });
                    $.ajax({
                        url: '/api/requestTest.php',
                        method: 'POST',
                        data: requestData,
                        success: (data) => {
                            const endTime = new Date().getTime();
                            const elapsedTime = (endTime - startTime) / 1000;
                            _this.statusCode = data.status_code;
                            _this.elapsedTime = elapsedTime.toFixed(2);
                            _this.size = data.size;
                            _this.response = JSON.stringify(data.response, null, 2); // 将响应结果格式化为 JSON 字符串
                        },
                        error: (xhr, status, error) => {
                            // 请求失败，设置请求状态及失败原因
                            _this.requesting = false;
                            _this.requestError = error;
                            _this.response = 'Error: ' + error;
                        },
                        complete: () => {
                            // 请求完成，重置请求状态
                            _this.requesting = false;
                            layer.close(loadIndex);
                        }
                    });
                },
                addParam() {
                    const _this = this;
                    _this.params.push({
                        key: '',
                        value: ''
                    });
                },
                removeParam(index) {
                    const _this = this;
                    _this.params.splice(index, 1);
                    if (_this.params.length === 0) {
                        _this.addParam();
                    }
                },
                clearParams() {
                    const _this = this;
                    _this.params = [{
                        key: '',
                        value: ''
                    }];
                },
                addHeader() {
                    const _this = this;
                    _this.headers.push({
                        key: '',
                        value: ''
                    });
                },
                removeHeader(index) {
                    const _this = this;
                    _this.headers.splice(index, 1);
                    if (_this.headers.length === 0) {
                        _this.addHeader();
                    }
                },
                copyResponse() {
                    const _this = this;
                    const el = document.createElement('textarea');
                    el.value = _this.response;
                    if (!el.value) {
                        layer.msg("响应结果为空");
                        return
                    }
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);
                    alert('响应结果已复制到剪贴板');
                },
                toggleHeaders() {
                    const _this = this;
                    _this.headersExpanded = !_this.headersExpanded;
                }
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
</body>

</html>