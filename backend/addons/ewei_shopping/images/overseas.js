!function(s,a,t){function i(s){this.nav=s.nav,this.list=s.list,this.con=this.list.find("ul"),this.load=s.load,this.nogoods=s.nogoods,this.on=s.on,this.url=s.url,this._top=parseInt(this.nav.offset().top),this.flag=!1,this.scrFlag=!1,this.dataClass=s.dataClassDefault||1,this.page=1,this.dataArr=[],this.fn,this.init()}i.prototype={constructor:i,navTab:function(){var a=this;this.nav.find(".nav-class").on("click",function(){var t=$(this);t.hasClass(a.on)||(t.addClass(a.on).siblings().removeClass(a.on),a.dataClass=t.attr("data-class"),""!=a.fn&&(a.flag=!0,$(s).unbind(s,"scroll",a.fn)),a.page=1,a.load.show(),a.nogoods.hide(),a.con.find("li").remove(),a.dataArr[a.dataClass]?a.showPageData(a.dataArr[a.dataClass]):a.scrollLoadData(a.dataClass,1,function(s){a.showPageData(s)},""),setTimeout(function(){a.fixedTab()},50))})},scrollLoadData:function(a,t,i,n){var o=this;$.ajax({type:"post",url:o.url,data:{dataClass:a,page:t},dataType:"json",success:function(a){200==a.status?(a.obj.length>0&&1==o.page&&(o.dataArr[o.dataClass]=a),i&&i(a)):-200==a.status&&(o.load.hide(),o.nogoods.show(),""!=n&&$(s).unbind(s,"scroll",o.fn)),o.scrFlag=!1}})},showPageData:function(s){var a="";if(0!=s.obj.length){for(var s=s.obj,t=0,i=s.length;i>t;t++){var n=s[t];a+='<li>						<a href="'+n.link+'">							<img src="'+n.img+'" alt="'+n.name+'">							<p>'+n.desc+"</p>							<h3>"+n.name+'</h3>						</a>						<div class="overseas-class-country"><img src="'+n.countryimg+'" alt="'+n.countryname+'"><span>'+n.countryname+"</span>"+n.countrynameen+'</div>						<span class="overseas-class-money">￥'+n.price+"</span>					</li>"}this.con.append(a),this.flag&&this.addScrollFun()}},addScrollFun:function(){var a=this;$(s).on("scroll",function(s){a.fn=arguments.callee,a.scrFlag||a.isNextPage()})},isNextPage:function(){var t=this;this.hei=parseInt($(a).height()),this.scrTop=parseInt($(s).scrollTop()),this.conHei=parseInt($(s).height()),this.hei-(this.scrTop+this.conHei)<80&&(this.scrFlag=!0,this.page++,this.scrollLoadData(this.dataClass,this.page,function(s){t.showPageData(s)},this.fn))},scrollFixed:function(){var s=this;$(a).on("scroll",function(){s.fixedTab()})},fixedTab:function(){var s=parseInt(a.documentElement.scrollTop||a.body.scrollTop);this._top<s+10?(this.nav.addClass(this.on),this.list.addClass(this.on)):(this.nav.removeClass(this.on),this.list.removeClass(this.on))},init:function(){this.navTab(),this.fixedTab(),this.addScrollFun(),this.isNextPage(),this.scrollFixed()}},s.OverseasProduct=i}(window,document);