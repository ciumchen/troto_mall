/*
* 一小时送达
*/

;(function(window, document, undefined) {

	function Community(opts) {
		this.list = opts.list;					// 数据列表
		this.nav = opts.nav;					// 导航
		this.con = opts.con;					// 页面主要内容
		this.on = opts.on;						// 当前效果
		this.listImg = opts.listImg;			// 图片列表
		this.imgSrc = opts.imgSrc;				// 图片地址
		this.h = parseInt(document.documentElement.clientHeight || document.body.clientHeight);		// 获取屏幕高度
		this.imgs = this.listImg.getElementsByTagName('img');		// 页面图片，按需加载
		this.len = this.imgs.length;			// 页面图片数量
		this.curIndex = 0;						// 当前图片加载位置
		this.sel = opts.sel;					// 是否选中产品
		this.total = opts.total; 				// 总价格
		this.ship = opts.ship;					// 运费
		this.btn = opts.btn;					// 结算按钮
		this.dataArr = {};						// 购物车数据列表
		this.dataArr['goods'] = [];				// 商品列表
		this.load = opts.load;					// loading加载等待
		this.url = opts.url;					// 数据提交链接
		this.topArr = [];						// 顶部位置

		this.init();
	}
	Community.prototype = {
		constructor: Community,
		// 加入购物车
		addCart: function() {
			var that = this
				num = 0, stock = 0;
			// 加入购物车
			this.list.find('li').on('click', function() {
				var $this = $(this);
				$this.addClass(that.sel).addClass(that.on);
				setTimeout(function() {
					$this.removeClass(that.on);
				}, 200);

				num = parseInt($this.attr('data-num'));
				stock = parseInt($this.attr('data-stock'));
				// 检测库存
				(stock > num) ?	num++ : (num = stock, alert('抱歉，只剩下'+stock+'件商品了！'));
				// 添加购物车数量，并对于显示信息
				$this.attr('data-num', num).find('i').text(num);
				
				if (num > 0) {
					$this.find('i').show();
					$this.find('h3').hide();
					$this.find('span').css('display','block');
				} else {
					$this.find('i').hide();
					$this.find('h3').show();
					$this.removeClass(that.sel).find('span').hide();
				}
				// 设置价格
				getSelProductPrice();
			});
			// 减少
			this.list.find('span').on('click', function(e) {
				e.stopPropagation();
				var $this = $(this),
					$li = $this.parents('li');
				$li.addClass(that.on);
				setTimeout(function() {
					$li.removeClass(that.on);
				}, 200);

				num = parseInt($li.attr('data-num'));
				num--;
				// 删除购物车数量
				if (num > 0) {
					$li.attr('data-num', num).find('i').text(num);
				} else {
					$this.hide();
					$li.removeClass(that.sel).find('i').hide();
					$li.find('h3').show();
					$li.attr('data-num', 0).find('i').text(num);
				}

				// 设置价格
				getSelProductPrice();

			});

			// 遍历所有产品内容，获取总价格
			function getSelProductPrice() {
				var goodsid, stock, price, num, 
					total = parseInt(0);
				that.dataArr = {};
				that.dataArr['goods'] = [];

				// 遍历页面数据
				that.con.find('li').each(function() {
					var $this = $(this);
					if ($this.hasClass(that.sel)) {
						goodsid = $this.attr('data-goodsid');
						stock = parseInt($this.attr('data-stock'));
						price = parseFloat($this.attr('data-price')).toFixed(2);
						num = parseInt($this.attr('data-num'));
						that.dataArr['goods'].push({
							goodsid: goodsid,
							stock: stock,
							price: price,
							num: num
						});
						total = parseFloat(total + (price * num));
					}
				});
				// 检测是否满足免运费金额，不满足则加上运费
				// that.dataArr['total'] = (total >= that.ship.min) ? parseFloat(total).toFixed(2) : parseFloat(total + that.ship.money).toFixed(2);
				that.dataArr['total'] = parseFloat(total).toFixed(2);
				// console.log(that.dataArr);
				// 设置页面价格
				that.total.find('span').text('￥' + that.dataArr['total']);
			}
		},
		// 提交购买商品
		submitBuyGoods: function() {
			var that = this;
			this.btn.on('touchend', function() {
				if (that.dataArr['goods'].length > 0) {
					that.load.show().find('i').show();
					that.load.find('span').text('订单生成中...');
					$.ajax({
						type: 'post',
						url: that.url,
						data: that.dataArr,
						dataType: 'json',
						success: function(data) {
							if (data.status == 200) {
								location.href = data.link;
								that.load.hide();
							} else {
								that.load.find('i').hide();
								that.load.find('span').text(data.msg);
								setTimeout(function() {
									that.load.hide();
								}, 2000);
							}
						}
					});
				}
			});
		},
		// 滚动固定导航
		scrollFixed: function() {
            var that = this, top;
                top = parseInt(this.nav.offset().top);
            // 获取顶部位置
            that.con.find('.index-class-list').each(function() {
            	var top = $(this).offset().top;
            	that.topArr.push(top - 100);
            });
            fixedNav();		// 默认判断是否需要固定导航栏
            $(document).on('scroll', function() {
            	fixedNav();
            });
            function fixedNav() {
                var scrTop = parseInt(document.documentElement.scrollTop || document.body.scrollTop);             
                if (top < scrTop + 5) {
                    that.nav.addClass(that.on);
                    that.con.addClass(that.on);
                } else {
                    that.nav.removeClass(that.on);
                    that.con.removeClass(that.on);
                }
                // 遍历获取位置, 设置位置
                for (var i=0,len=that.topArr.length; i<len; i++) {
                	if (scrTop > that.topArr[i]) {
                		that.nav.find('li').eq(i).addClass(that.on).siblings().removeClass(that.on);
                	}
                }
            }
        },
        // 点击导航滚动指定位置
        gotoNavLocation: function() {
        	var that = this,
        		flag = false;
        	this.nav.find('li').on('click', function() {
        		var pcate = $(this).attr('data-pcate');
        		that.con.find('.index-class-list').each(function() {
        			if ($(this).attr('data-pcate') == pcate) {
        				var top = $(this).offset().top;
        				scrollLocation(top-80);
        				flag = true;
        				return false;
        			}
        		});
        		if (!flag) {
        			scrollLocation(0);
        		}
        	});
        	// 滚动函数
        	function scrollLocation(top) {
    			$('html, body').animate({
					scrollTop: top
				}, 'slow', function() {
				});
        	}
        },
        // 滚动加载图片
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
							$(window).unbind('scroll', fn);
						}
					}
				}
			}
			this.first = false;
		},
		init: function() {
			var that = this, fn;
			this.addCart();
			this.gotoNavLocation();
			this.loadImg();
			this.submitBuyGoods();
			$(window).on('scroll', function() {
				fn = arguments.callee;
				that.loadImg(fn);
			});
			window.onload = function() {
				that.scrollFixed();
			}
		}
	};
	window.Community = Community;

})(window, document);