function GotoTop(t){this.el=t.el,this.delay=t.delay}GotoTop.prototype={constructor:GotoTop,scrollLocation:function(){function t(){n=parseInt(document.documentElement.scrollTop||document.body.scrollTop),clearTimeout(o),n>=200?(e.el.style.display="block",o=setTimeout(function(){e.el.style.opacity=1},e.delay)):(e.el.style.opacity=0,o=setTimeout(function(){e.el.style.display="none"},e.delay))}var o,e=this,n=0;window.addEventListener("scroll",t,!1),this.el.addEventListener("click",function(){$("html, body").animate({scrollTop:0},200)},!1)},init:function(){this.scrollLocation()}};var gotoTop=new GotoTop({el:document.getElementById("gotoTop"),delay:100}).init();!function(t,o,e){function n(t){this.btn=t.btn,this.url=t.url,this.on=t.on,this.init()}var s=function(){return{toDouble:function(t){return t=t>=10?""+t:"0"+t},timer:function(t,o,e,n,s,i,a,l){function r(t){var o=new Date(t),e=parseInt((h.getTime()-o.getTime())/1e3),n=parseInt(e%60),s=parseInt(e/60%60),i=Math.floor(e/3600%24);Math.floor(e/86400);return 0>=i&&0>=s&&0>=n?(clearInterval(y),d.text("00"),p.text("00"),f.text("00"),!1):(d.text(c.toDouble(i)),p.text(c.toDouble(s)),void f.text(c.toDouble(n)))}var c=this,u=parseInt(a),d=l.find(".hour"),p=l.find(".minute"),f=l.find(".seconds"),n=n||0,s=s||0,i=i||0,h=new Date;h.setFullYear(parseInt(t)),h.setMonth(parseInt(o)-1),h.setDate(parseInt(e)),h.setHours(parseInt(n)),h.setMinutes(parseInt(s)),h.setSeconds(parseInt(i)),r(u);var y=setInterval(function(){r(u),u+=1e3},1e3)}}}();t.timerFun=s,n.prototype={constructor:n,collectFun:function(){var t=this;t.btn.on("touchend",function(o){o.stopPropagation();var e=$(this),n=e.attr("data-goodsid");$.ajax({type:"get",url:t.url,data:{id:n},dataType:"json",success:function(o){200==o.status?e.hasClass(t.on)?(e.removeClass(t.on),Util.dialog.showMessage('<i class="iconfont no"></i>取消收藏')):(e.addClass(t.on),Util.dialog.showMessage('<i class="iconfont ok"></i>收藏成功')):Util.dialog.showMessage(o.msg)}})})},init:function(){this.collectFun()}},t.Collect=n}(window,document);