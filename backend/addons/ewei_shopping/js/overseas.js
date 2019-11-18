/**
 * 海外购页面功能
 */

;(function(window, document, undefined) {

	var hasTouch = "ontouchstart" in window ? true : false,
        touchStart = hasTouch ? "touchstart" : "mousedown",
        touchMove = hasTouch ? "touchmove" : "mousemove",
        touchEnd = hasTouch ? "touchend" : "mouseup"; 

	// 海外直邮数据加载
	function OverseasProduct(opts) {
		this.nav = opts.nav;
		this.list = opts.list;
		this.con = this.list.find('ul');
		this.load = opts.load;
		this.nogoods = opts.nogoods;
		this.on = opts.on;
		this.url = opts.url;
		this._top = parseInt(this.nav.offset().top);
		this.flag = false;
		this.scrFlag = false;
		this.dataClass = opts.dataClassDefault || 1;
		this.page = 1;
		this.dataArr = [];
		this.fn;

		this.init();
	}
	OverseasProduct.prototype = {
		constructor: OverseasProduct,
		navTab: function() {
			var _self = this;
			this.nav.find('.nav-class').on('click', function() {
				var $this = $(this);
				if (!$this.hasClass(_self.on)) {
					$this.addClass(_self.on).siblings().removeClass(_self.on);
					_self.dataClass = $this.attr('data-class');

	                if (_self.fn != '') {
	                	_self.flag = true;
	                	$(window).unbind(window, 'scroll', _self.fn);
	                }
	                // 切换默认回第一页
	            	_self.page = 1;
	                _self.load.show();
	                _self.nogoods.hide();
		  			_self.con.find('li').remove();
                	// 检测是否有缓存
                	if (_self.dataArr[_self.dataClass]) {
                		_self.showPageData(_self.dataArr[_self.dataClass]);
					} else {
						_self.scrollLoadData(_self.dataClass, 1, function(data) {
							_self.showPageData(data);
						}, '');
					}
	                // 延迟执行分类固定
	                setTimeout(function() {
			  			_self.fixedTab();
	                }, 50);
				}

			});
		},
		// 滚动获取数据
		scrollLoadData: function(dataClass, page, callback, fn) {
			var _self = this;
			// dataClass: 数据类型， page: 数据分页
			$.ajax({
				type: 'post',
				url: _self.url,
				data: {dataClass: dataClass, page: page},
				dataType: 'json',
				success: function(data) {
					if (data.status == 200) {
						if (data.obj.length > 0 &&  _self.page == 1) {
							_self.dataArr[_self.dataClass] = data;
						}
						callback && callback(data);
					} else if (data.status == -200) {
						_self.load.hide();
						_self.nogoods.show();
						if (fn != '') {
							$(window).unbind(window, 'scroll', _self.fn);
						}
					}
					_self.scrFlag = false;
				}
			});	
		},
		// 显示页面数据
		showPageData: function(data) {
			var strHTML = '',
				listHTML = '';
			if (data.obj.length != 0) {
				var data = data.obj;
				for (var i=0,len=data.length; i<len; i++) {
					var d = data[i];

					strHTML += '<li>\
						<a href="'+d.link+'">\
							<img src="'+d.img+'" alt="'+d.name+'">\
							<p>'+d.desc+'</p>\
							<h3>'+d.name+'</h3>\
						</a>\
						<div class="overseas-class-country"><img src="'+d.countryimg+'" alt="'+d.countryname+'"><span>'+d.countryname+'</span>'+d.countrynameen+'</div>\
						<span class="overseas-class-money">￥'+d.price+'</span>\
					</li>';
				}

				this.con.append(strHTML);

				if (this.flag) {
					this.addScrollFun();
				}
			}
		},
		addScrollFun: function() {
        	var _self = this;
			// 添加滚动事件
			$(window).on('scroll', function(e) {
				// 指向滚动事件函数，用于移除
				_self.fn = arguments.callee;
				// 滚动一次只触发一次
				if (!_self.scrFlag) {
					_self.isNextPage();
				}
			});
        },
        // 检测是否翻页
        isNextPage: function () {
        	var _self = this;
        	this.hei = parseInt($(document).height());
			this.scrTop = parseInt($(window).scrollTop());
			this.conHei = parseInt($(window).height());

			if (this.hei-(this.scrTop+this.conHei) < 80) {
				this.scrFlag = true;
				this.page++;
				this.scrollLoadData(this.dataClass, this.page, function(data) {
					_self.showPageData(data);
				}, this.fn);
			}
        },
		// 滚动固定导航
		scrollFixed: function() {
            var _self = this;
            $(document).on('scroll', function() {
                _self.fixedTab();
            });
        },
        fixedTab: function() {
        	var scrTop = parseInt(document.documentElement.scrollTop || document.body.scrollTop);
            if (this._top < scrTop + 10) {
                this.nav.addClass(this.on);
                this.list.addClass(this.on);
            } else {
                this.nav.removeClass(this.on);
                this.list.removeClass(this.on);
            }
        },
		init: function() {
			this.navTab();
			this.fixedTab();
			this.addScrollFun();
			this.isNextPage();
			this.scrollFixed();
		}
	}
	window.OverseasProduct = OverseasProduct;

})(window, document);