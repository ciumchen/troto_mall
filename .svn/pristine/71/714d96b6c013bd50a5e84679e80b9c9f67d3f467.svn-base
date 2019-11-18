/*
* 分类列表加载数据
*/

;(function(window, document, undefined) {

	function ListLazyLoad(opts) {
		this.list = opts.list;
		this.load = opts.load;
		this.page = 1;
		this.scrTop = 0;
		this.url = opts.url;
		this.strHTMLFun = opts.strHTMLFun;
		this.urlArr = {};
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
	// 滚动加载内容
	ListLazyLoad.prototype.scrollLoad = function() {
		var that = this,
			status;
		// 滚动事件
		$(window).on('scroll', function() {
			that.hei = parseInt($(document).height());
			that.scrTop = parseInt($(this).scrollTop());
			that.conHei = parseInt($(this).height());
			if (that.hei-(that.scrTop+that.conHei) < 20) {
				that.load.show();
				that.page++;

				if (that.urlArr['level'] != undefined) {
					status = that.urlArr['level'];
				} else {
					if (that.urlArr['status'] == undefined || that.urlArr['status'] == null) {
						status = 0;
					} else {
						status = that.urlArr['status'];
					}
				}

				// 取数据
				that.getAjaxPage(status, that.page, function(data) {
					if (typeof data != 'string') {
						that.load.hide();
						var strHTML = '';
						for (var i=0,len=data.length; i<len; i++) {
							var d = data[i];
							strHTML += that.strHTMLFun(d);
						}
						that.list.append(strHTML);
					} else {
						that.load.html(data).show().css('margin-bottom','0');
					}
				});
			}
		});
	};
	// 发送Ajax请求页面信息
	ListLazyLoad.prototype.getAjaxPage = function(status, page, callback) {
		var that = this,
			strHTML = '';
		/*
		* page: 页面数，默认1，第一次滚动请求为2，取第二屏内容
		* data.status 200 数据请求成功， -200 没有内容了， -100 请求失败
		*/
		$.ajax({
			type: 'post',
			url: that.url,
			data: {status: status, page: page},
			dataType: 'json',
			success: function(data) {
				if (data.length != 0) {
					// 回调函数
					callback && callback(data);
				} else if (data.length == 0) {
					// 没有内容，注销滚动事件
					$(window).unbind('scroll');
					callback && callback('');
				} else {
					/*if (data.status != false) {
						setTimeout(function() {
							that.getAjaxPage(status, page, callback);
						}, 2000);
						return false;
					}
					that.load.hide();*/
				}
			}
		});
	};
	ListLazyLoad.prototype.init = function() {
		this.scrollLoad();
		this.getLocationUrl();
	};

	window.ListLazyLoad = ListLazyLoad;

})(window, document);