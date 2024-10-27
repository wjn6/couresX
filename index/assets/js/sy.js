function watermark(t1, t2, t3) {
  var maxWidth = document.documentElement.clientWidth;
  var maxHeight = document.documentElement.clientHeight;
  var intervalWidth = 220;
  var intervalheight = 180;
  var rowNumber = Math.ceil((maxWidth - 40 - 100) / intervalWidth);
  var coumnNumber = Math.ceil((maxHeight - 40 - 80) / intervalheight);
  var defaultSettings = {
    watermark_color: "#aaa",
    watermark_alpha: 0.25,
    watermark_fontsize: "15px",
    watermark_font: "微软雅黑",
    watermark_width: 200,
    watermark_height: 80,
    watermark_angle: 15,
  };

  var mark_divs = [];

  function createWatermark() {
    var _temp = document.createDocumentFragment();
    for (var i = 0; i < rowNumber; i++) {
      for (var j = 0; j < coumnNumber; j++) {
        var x = intervalWidth * i + 0;
        var y = intervalheight * j + 30;
        var mark_div = document.createElement("div");
        mark_div.id = "mark_div" + i + j;
        mark_div.className = "mark_div";
        var span0 = document.createElement("div");
        span0.appendChild(document.createTextNode(t1));
        var span1 = document.createElement("div");
        span1.appendChild(document.createTextNode(t2));
        var span2 = document.createElement("div");
        span2.appendChild(document.createTextNode(t3));
        mark_div.appendChild(span0);
        mark_div.appendChild(span1);
        mark_div.appendChild(span2);
        mark_div.style.webkitTransform =
          "rotate(-" + defaultSettings.watermark_angle + "deg)";
        mark_div.style.MozTransform =
          "rotate(-" + defaultSettings.watermark_angle + "deg)";
        mark_div.style.msTransform =
          "rotate(-" + defaultSettings.watermark_angle + "deg)";
        mark_div.style.OTransform =
          "rotate(-" + defaultSettings.watermark_angle + "deg)";
        mark_div.style.transform =
          "rotate(-" + defaultSettings.watermark_angle + "deg)";
        mark_div.style.visibility = "";
        mark_div.style.position = "absolute";
        mark_div.style.pointerEvents = "none";
        mark_div.style.left = x + "px";
        mark_div.style.top = y + "px";
        mark_div.style.overflow = "hidden";
        mark_div.style.zIndex = "9999";
        mark_div.style.pointerEvents = "none";
        mark_div.style.opacity = defaultSettings.watermark_alpha;
        mark_div.style.fontSize = defaultSettings.watermark_fontsize;
        mark_div.style.fontFamily = defaultSettings.watermark_font;
        mark_div.style.color = defaultSettings.watermark_color;
        mark_div.style.textAlign = "center";
        mark_div.style.width = defaultSettings.watermark_width + "px";
        mark_div.style.height = defaultSettings.watermark_height + "px";
        mark_div.style.display = "block";
        _temp.appendChild(mark_div);
        mark_divs.push(mark_div);
      }
    }
    document.body.appendChild(_temp);
  }

  createWatermark();

  function toggleWatermarkVisibility() {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0;
    var isVisible = scrollTop < maxHeight && scrollLeft < maxWidth;
    mark_divs.forEach(function(mark_div) {
      mark_div.style.display = isVisible ? "block" : "none";
    });
  }

  window.addEventListener("scroll", toggleWatermarkVisibility);
}
