!function(t,a,s){function o(t){this.list=t.list,this.load=t.load,this.nav=t.nav,this.on=t.on,this.page=1,this.scrTop=0,this.url=t.url,this.urlArr={},this.pcate="",this.cacheArr=[],this.init()}o.prototype.getLocationUrl=function(){if(location.search.length>0)for(var t=location.search,a=t.substring(t.indexOf("?")+1,t.length).split("&"),s=0;s<a.length;s++){var o=a[s];this.urlArr[o.substring(0,o.indexOf("="))]=o.substring(o.indexOf("=")+1,o.length)}},o.prototype.selPacteBtnTab=function(){var t=this;this.nav.on("touchend",function(){return $(this).hasClass("on")?!1:(t.pcate=$(this).attr("data-pcate"),$(this).addClass(t.on).siblings().removeClass(t.on),void t.getData(t.pcate))})},o.prototype.getData=function(t){var a=this,t=t||"",s="";this.page=1,this.list.html(""),this.load.show().css("bottom","-60px"),""==t&&a.urlArr.keyword&&(s=a.urlArr.keyword),""==t&&this.nav.each(function(){return $(this).hasClass(a.on)?(t=$(this).attr("data-pcate"),!1):void 0}),a.cacheArr[t]?(a.scrollLoad(),a.showPageData(a.cacheArr[t])):this.getAjaxPage(t,s,this.page,function(s){a.scrollLoad(),a.showPageData(s),"string"!=typeof s&&""!=t&&(a.cacheArr[t]=s)})},o.prototype.showPageData=function(t){var a=this;if("string"!=typeof t){a.load.hide();var s="";if(0!=t.obj.length){for(var t=t.obj,o=0,e=t.length;e>o;o++){var i=t[o];s+='<li>						<a href="'+i.link+'">							<img src="'+i.thumb+'"/>							<h3>'+i.title+'</h3>							<p class="class-price">￥'+i.marketprice+"</p>						</a>					</li>"}a.list.append(s)}}else a.load.html(t).show().css("bottom","5px")},o.prototype.scrollLoad=function(){var s=this,o="",e="";$(".no-goods").length<1&&$(t).on("scroll",function(){s.hei=parseInt($(a).height()),s.scrTop=parseInt($(this).scrollTop()),s.conHei=parseInt($(this).height()),s.hei-(s.scrTop+s.conHei)<20&&(s.load.show().css("bottom","-30px"),s.page++,s.pcate?o=s.pcate:s.urlArr.keyword&&(e=s.urlArr.keyword),s.getAjaxPage(o,e,s.page,function(t){s.showPageData(t)}))})},o.prototype.getAjaxPage=function(a,s,o,e){var i=this;$.ajax({type:"post",url:i.url,data:{pcate:a,keyword:s,page:o},dataType:"json",success:function(r){if(200==r.status)e&&e(r);else if(-200==r.status)$(t).unbind("scroll"),e&&e("没有商品了");else{if(0!=r.status)return setTimeout(function(){i.getAjaxPage(a,s,o,e)},2e3),!1;i.load.hide()}}})},o.prototype.init=function(){this.getLocationUrl(),this.getData(),this.selPacteBtnTab()},t.ListLazyLoad=o}(window,document);