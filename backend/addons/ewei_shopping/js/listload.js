/*
* 分类列表加载数据
*/

;(function(window, document, undefined) {

	function ListLazyLoad(opts) {
		this.list = opts.list;
		this.load = opts.load;
		this.nav = opts.nav;
		this.on = opts.on;
		this.page = 1;
		this.scrTop = 0;
		this.url = opts.url;
		this.urlArr = {};
		this.pcate = '';
		this.cacheArr = [];

		this.init();
	}
	// 获取url参数
	ListLazyLoad.prototype.getLocationUrl = function() {
		if (location.search.length > 0) {
			var url = location.search,
				paraString = url.substring(url.indexOf("?")+1, url.length).split("&");
			// 截取对应字符
			for (var i = 0; i < paraString.length; i++) {
				var j = paraString[i];
				// 设置对应参数
				this.urlArr[j.substring(0,j.indexOf("="))] = j.substring(j.indexOf("=")+1, j.length);
			}
		}
	};
	ListLazyLoad.prototype.selPacteBtnTab = function() {
		var that = this;
		this.nav.on('touchend', function() {
			// 如果点击是当前的数据，则返回
			if ($(this).hasClass('on')) return false;
			// 获取分类类型
			that.pcate = $(this).attr('data-pcate');
			$(this).addClass(that.on).siblings().removeClass(that.on);
			that.getData(that.pcate);
		});
	};
	ListLazyLoad.prototype.getData = function(pcate) {
		var that = this, 
			pcate = pcate || '', 
			keyword = '';
		this.page = 1;
		this.list.html('');
		this.load.show().css('bottom','-60px');
		if (pcate == '' && that.urlArr['keyword']) {
			keyword = that.urlArr['keyword'];
		}
		// 如果等于空，则获取是否有选中导航
		if (pcate == '') {
			this.nav.each(function() {
				if ($(this).hasClass(that.on)) {
					pcate = $(this).attr('data-pcate');
					return false;
				}
			});
		}
		// 如果数据存在，则不请求服务器
		if (that.cacheArr[pcate]) {
			that.scrollLoad();
			that.showPageData(that.cacheArr[pcate]);
		} else {
			this.getAjaxPage(pcate, keyword, this.page, function(data) {
				that.scrollLoad();
				that.showPageData(data);
				if (typeof data != 'string' && pcate != '') {
					that.cacheArr[pcate] = data;
				}
			});
		}
	};
	// 显示页面数据
	ListLazyLoad.prototype.showPageData = function(data) {
		var that = this;
		if (typeof data != 'string') {
			that.load.hide();
			var strHTML = '';
			if (data.obj.length != 0) {
				var data = data.obj;
				for (var i=0,len=data.length; i<len; i++) {
					var d = data[i];
					strHTML += '<li>\
						<a href="'+d.link+'">\
							<img src="'+d.thumb+'"/>\
							<h3>'+d.title+'</h3>\
							<p class="class-price">￥'+d.marketprice+'</p>\
						</a>\
					</li>';
				}
				that.list.append(strHTML);
			}
		} else {
			that.load.html(data).show().css('bottom','5px');
		}
	};
	// 滚动加载内容
	ListLazyLoad.prototype.scrollLoad = function() {
		var that = this,
			pcate = '', keyword = '';
		if ($('.no-goods').length < 1) {
			// 滚动事件
			$(window).on('scroll', function() {
				that.hei = parseInt($(document).height());
				that.scrTop = parseInt($(this).scrollTop());
				that.conHei = parseInt($(this).height());
				if (that.hei-(that.scrTop+that.conHei) < 20) {
					that.load.show().css('bottom', '-30px');
					that.page++;
					// 获取URL
					if (that.pcate) {
						pcate = that.pcate;
					} else if (that.urlArr['keyword']) {
						keyword = that.urlArr['keyword'];
					}
					// 取数据
					that.getAjaxPage(pcate, keyword, that.page, function(data) {
						that.showPageData(data);
					});
				}
			});
		}
	};
	// 发送Ajax请求页面信息
	ListLazyLoad.prototype.getAjaxPage = function(pcate, keyword, page, callback) {
		var that = this,
			strHTML = '';
		/*
		* pcate: 页面数据类型，类型，空为未默认
		* keyword: 页面数据类型，搜索，空为未默认
		* page: 页面数，默认1，第一次滚动请求为2，取第二屏内容
		* data.status 200 数据请求成功， -200 没有内容了， -100 请求失败
		*/
		$.ajax({
			type: 'post',
			url: that.url,
			data: {pcate: pcate, keyword: keyword, page: page},
			dataType: 'json',
			success: function(data) {
				if (data.status == 200) {
					// 回调函数
					callback && callback(data);
				} else if (data.status == -200) {
					// 没有内容，注销滚动事件
					$(window).unbind('scroll');
					callback && callback('没有商品了');
				} else {
					if (data.status != false) {
						setTimeout(function() {
							that.getAjaxPage(pcate, keyword, page, callback);
						}, 2000);
						return false;
					}
					that.load.hide();
				}
			}
		});
	};
	ListLazyLoad.prototype.init = function() {
		this.getLocationUrl();
		this.getData();
		this.selPacteBtnTab();
	};

	window.ListLazyLoad = ListLazyLoad;

})(window, document);