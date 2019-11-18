/*
* 卡券记录加载数据功能
*/

;(function(window, document, undefined) {

	function TicketList(opts) {
		this.list = opts.list;				// 记录列表
		this.load = opts.load;				// 加载
		this.nav = opts.nav;				// 导航
		this.on = opts.on;					// 当前位置
		this.noRecord = opts.noRecord;		// 没有记录
		this.page = 1;						// 默认页面
		this.type = 1;						// 默认类型(送出卡券)
		this.scrTop = 0;					// 默认滚动位置
		this.url = opts.url;				// 请求URL
		this.setHTML = opts.setHTML;		// 插入页面HTML结构
		this.cacheArr = [];					// 缓存数据
		this.flag = false;					// 防止滚动触发多次

		this.init();
	}
	TicketList.prototype.selPacteBtnTab = function() {
		var that = this;
		this.nav.on('touchend', function() {
			// 如果点击是当前的数据，则返回
			if ($(this).hasClass('on')) return false;
			// 获取分类类型
			that.type = $(this).attr('data-type');
			$(this).addClass(that.on).siblings().removeClass(that.on);
			// 没有记录图标
			that.noRecord.hide();
			// 显示load
			that.load.show().find('img').show();
			that.load.find('span').hide();
			that.getData(that.type);
		});
	};
	TicketList.prototype.getData = function(type) {
		var that = this, 
			type = type || 1;
		this.page = 1;
		this.list.html('');
		// 如果数据存在，则不请求服务器
		if (that.cacheArr[type]) {
			that.scrollLoad();
			that.showPageData(that.cacheArr[type]);
		} else {
			this.getAjaxPage(type, this.page, function(data) {
				that.scrollLoad();
				that.showPageData(data);
				if (typeof data != 'string' && type != '') {
					that.cacheArr[type] = data;
				}
			});
		}
	};
	// 显示页面数据
	TicketList.prototype.showPageData = function(data) {
		var that = this;
		if (typeof data != 'string') {
			that.load.hide();
			var strHTML = '';
			if (data.obj.length != 0) {
				var data = data.obj;
				for (var i=0,len=data.length; i<len; i++) {
					var d = data[i];
					strHTML += that.setHTML(d);
				}
				that.list.append(strHTML);
			}
		} else {
			that.load.find('img').hide();
			that.load.find('span').text(data).show();
		}
		that.flag = false;
	};
	// 滚动加载内容
	TicketList.prototype.scrollLoad = function() {
		var that = this;
		// 滚动事件
		$(window).on('scroll', function() {
			that.hei = parseInt($(document).height());
			that.scrTop = parseInt($(this).scrollTop());
			that.conHei = parseInt($(this).height());
			if (!that.flag) {
				if (that.hei-(that.scrTop+that.conHei) < 40) {
					that.load.show();
					that.page++;
					that.flag = true;		// 防止触发多次
					// 取数据
					that.getAjaxPage(that.type, that.page, function(data) {
						that.showPageData(data);
					});
				}
			}
		});
	};
	// 发送Ajax请求页面信息
	TicketList.prototype.getAjaxPage = function(type, page, callback) {
		var that = this,
			strHTML = '';
		/*
		* type: 页面数据类型，类型，1为未默认（1: 送出卡券，2:收到卡券）
		* page: 页面数，默认1，第一次滚动请求为2，取第二屏内容
		* data.status 200 数据请求成功， -200 没有内容了， -100 请求失败
		*/
		$.ajax({
			type: 'post',
			url: that.url,
			data: {type: type, page: page},
			dataType: 'json',
			success: function(data) {
				if (data.status == 200) {
					// 回调函数
					callback && callback(data);
				} else if (data.status == -200) {
					// 没有内容，注销滚动事件
					$(window).unbind('scroll');
					callback && callback('没有卡券了');
					if (page == 1) {
						that.load.hide();
						that.noRecord.show();
					}
				} else {
					if (data.status != false) {
						setTimeout(function() {
							that.getAjaxPage(type, page, callback);
						}, 2000);
						return false;
					}
					that.load.hide();
				}
			}
		});
	};
	TicketList.prototype.init = function() {
		this.load.show();
		this.getData();
		this.selPacteBtnTab();
	};

	window.TicketList = TicketList;

})(window, document);