/*
* 首页懒加载
*/

/*
* 数据说明
* 包含多个滚动事件，分别监听加载图片，加载数据，固定导航
* 加载图片：首页默认打开，加载首屏图片，减少首屏打开时间
* 加载数据：页面滑动到达底部，加载下页数据
* 固定导航：判断当前位置是否大于或等于导航位置
*/

;(function(window, undefined) {

	function addEvent(el, evt, fn) {
		el.addEventListener ? el.addEventListener(evt, fn, false) : el.attachEvent('on'+evt, fn);
	}
	function removeEvent(el, evt, fn) {
		el.removeEventListener ? el.removeEventListener(evt, fn, false) : el.detachEvent('on'+evt, fn);
	}

	function LazyLoad(opts) {
		this.el = opts.el;				// 元素
		this.imgSrc = opts.imgSrc;		// 图片路径
		this.delay = opts.delay;		// 执行时间
		this.imgs = this.el.getElementsByTagName('img');	// 获取图片
		this.len = this.imgs.length;	// 图片数量
		this.h = parseInt(document.documentElement.clientHeight || document.body.clientHeight);		// 获取屏幕高度
		this.last = false;				// 是否到达底部载入
		this.curIndex = 0;				// 当前索引
		this.nav = opts.nav;			// 分类导航
		this.con = opts.con;			// 主要内容
		this.load = opts.load;			// 加载中loading
		this.recom = opts.recom;		// 最优推荐
		this.url = opts.url;			// 数据请求URL
		this.on = opts.on;				// 当前位置
		this.nogoods = opts.nogoods; 	// 没有商品
		this._top = parseInt(this.nav.offset().top);
		this.pcate = 1;
		this.page = 1;
		this.flag = false;
		this.scrFlag = false;
		this.fn = '';
		this.dataArr = [];
	}
	LazyLoad.prototype = {
		constructor: LazyLoad,
		loadImg: function(fn) {
			var i, img, top;
			this.scrTop = parseInt(document.documentElement.scrollTop || document.body.scrollTop);
			for (i = this.curIndex; i < this.len; i++) {
				img = this.imgs[i];
				if (img) {

					// 获取位置
					top = parseInt(img.getBoundingClientRect().top);

					// 判断图片是否在屏幕可视区域
					if (top + this.scrTop < this.h + this.scrTop + 100) {	
						// 把当前索引设置为已经遍历到的位置，防止重复遍历
						this.curIndex = i;
						try {
							if (img.getAttribute(this.imgSrc)) {
								// 插入数据图片数据
								img.setAttribute('src', img.getAttribute(this.imgSrc));
								img.removeAttribute(this.imgSrc);
							}
						} catch(ex) {
							// 错误则跳过
						}

						// 如果最后一个加载完毕
						if (i == this.len - 1) {
							// 移除滚动事件
							this.last = true;
							clearTimeout(this.timer);
						}
					}
				}
			}
			this.first = false;
		},
		// 滚动获取数据
		scrollLoadData: function(pcate, page, callback, fn) {
			var _self = this;
			// pcate: 数据类型， page: 数据分页
			$.ajax({
				type: 'post',
				url: _self.url,
				data: {pcate: pcate, page: page},
				dataType: 'json',
				success: function(data) {
					if (data.status == 200) {
						if (data.obj.length > 0 && _self.pcate != 1 &&  _self.page == 1) {
							_self.dataArr[_self.pcate] = data;
						}
						callback && callback(data);
					} else if (data.status == -200) {
						_self.load.hide();
						_self.nogoods.show();
						if (fn != '') {
							removeEvent(window, 'scroll', fn);
						}
						// _self.load.show().text('没有内容了');
					}
					_self.scrFlag = false;
				}
			});	
		},
		// 显示页面数据
		showPageData: function(data) {
			var strHTML = '',
				listHTML = '';
			// console.log(data);
			if (data.obj.length != 0) {
				var data = data.obj;
				for (var i=0,len=data.length; i<len; i++) {
					var d = data[i];
					for (var j=0,jLen=d.list.length; j<jLen; j++) {
						var list = d.list[j],
							topHTML = '';
						if (list.top != 0) {
							topHTML = '<div class="product-top class-top">TOP<i></i></div>';
						}
						listHTML += '<li>\
							<a href="'+list.link+'">\
								'+topHTML+'\
								<img src="'+list.thumb+'" alt="'+list.title+'" />\
								<h3>'+list.title+'</h3>\
								<p>￥'+list.price+'</p>\
							</a>\
						</li>';
					}
					strHTML += '<div class="index-class-list">\
						<div class="box-shadow class-list-title">\
							<h2><i class="title-left"></i>'+d.title+'<i class="title-right"></i></h2>\
							<p><i class="title-line-left"></i>'+d.desc+'<i class="title-line-right"></i></p>\
						</div>\
						<ul class="class-list-con">'+listHTML+'</ul>\
						<div class="bottom-load-more">\
							<a href="'+d.morelink+'" class="load-more-btn">查看更多</a>\
							<span>Click for more products</span>\
							<a href="'+d.morelink+'" class="load-more-txt">点击进入</a>\
						</div>\
					</div>';
				}
				if (this.pcate != 1 && this.page != 1) {
					this.con.find('.class-list-con').append(listHTML);
				} else {
					this.con.append(strHTML);
				}
				if (this.flag) {
					this.addScrollFun();
				}
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
            if (this._top < scrTop+10) {
                this.nav.addClass(this.on);
                this.con.addClass(this.on);
            } else {
                this.nav.removeClass(this.on);
                this.con.removeClass(this.on);
            }
        },
        // 切换选项
        navTab: function() {
            var _self = this;
            this.nav.find('li').on('click', function(e) {
                e.stopPropagation();
                var $this = $(this);
                $this.addClass(_self.on).siblings().removeClass(_self.on);
                if (_self.fn != '') {
                	_self.flag = true;
                	removeEvent(window, 'scroll', _self.fn);
                }
                // 切换默认回第一页
            	_self.page = 1;
                _self.pcate = $this.attr('data-type');
                _self.load.show();
                _self.nogoods.hide();
	  			_self.con.find('.index-class-list').remove();
                // 如果是1，则是默认最优推荐
                if (_self.pcate == 1) {
                	_self.recom.show();
                	_self.addScrollFun();
                } else {	// 其他数字，获取对应数据
                	_self.recom.hide();
                	// 检测是否有缓存
                	if (_self.dataArr[_self.pcate]) {
                		_self.showPageData(_self.dataArr[_self.pcate]);
					} else {
						_self.scrollLoadData(_self.pcate, 1, function(data) {
							_self.showPageData(data);
						}, '');
					}
                }
                // 延迟执行分类固定
                setTimeout(function() {
		  			_self.fixedTab();
                }, 50);
	            // _self.addScrollFun();
            });
        },
        addScrollFun: function() {
        	var _self = this;
			// 添加滚动事件
			addEvent(window, 'scroll', function(e) {
				// 指向滚动事件函数，用于移除
				_self.fn = arguments.callee;
				if (_self.last != true) {
					_self.timer = setTimeout(function() {
						_self.loadImg(_self.fn);
					}, _self.delay);
				} else {
					// 滚动一次只触发一次
					if (!_self.scrFlag) {
						_self.isNextPage();
					}
				}
			});
        },
        // 检测是否翻页
        isNextPage: function () {
        	var _self = this;
        	this.hei = parseInt($(document).height());
			this.scrTop = parseInt($(window).scrollTop());
			this.conHei = parseInt($(window).height());

			if (this.hei-(this.scrTop+this.conHei) < 110) {
				this.scrFlag = true;
				this.page++;
				this.scrollLoadData(this.pcate, this.page, function(data) {
					_self.showPageData(data);
				}, this.fn);
			}
        },
        // 初始化
		init: function() {
			var _self = this;
			this.loadImg();
			this.addScrollFun();
			this.isNextPage();
			// 窗口大小发生改变
			addEvent(window, 'resize', function() {
				_self.h = parseInt(document.documentElement.clientHeight || document.body.clientHeight);
				_self.loadImg();
			});
			this.scrollFixed();
			this.navTab();
		}
	};
	window.LazyLoad = LazyLoad;

})(window);