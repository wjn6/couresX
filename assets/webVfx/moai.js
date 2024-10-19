// 在HTML标签上添加一个类
document.documentElement.classList.add('grayscale');

// 添加样式到CSS中，保留原有的CSS
var style = document.createElement('style');
style.textContent = `
    html.grayscale {
        filter: grayscale(100%) !important;
        -webkit-filter: grayscale(100%) !important;
        -moz-filter: grayscale(100%) !important;
        -ms-filter: grayscale(100%) !important;
        -o-filter: grayscale(100%) !important;
        filter: progid:DXImageTransform.Microsoft.BasicImage(grayscale=1) !important;
        -webkit-filter: grayscale(1) !important;
    }
`;
document.head.appendChild(style);
