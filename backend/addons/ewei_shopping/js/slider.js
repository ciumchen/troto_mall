/**
 * 自适应循环播放幻灯片
 */

;(function(window, document, undefined) {

	var hasTouch = "ontouchstart" in window ? true : false,
		touchStart = hasTouch ? "touchstart" : "mousedown",
		touchMove = hasTouch ? "touchmove" : "mousemove",
		touchEnd = hasTouch ? "touchend" : "mouseup";

	function Slider(opts) {
		this.banner = opts.banner; 		// 滑屏元素
		this.slide = opts.slide;		// 幻灯
		this.nav = opts.nav;			// 导航
		this.slideLi = this.slide.find('li');	// 每个幻灯
		this.slideLen = this.slideLi.length;						// 幻灯长度
		this.navFlag = (opts.navFlag != undefined) ? opts.navFlag : true;	// 是否显示导航
		this.speed = opts.speed;		// 切换速度
		this.moveSpeed = opts.moveSpeed;	// 幻灯片移动速度
		this.on = opts.on;				// 当前位置
		this.moveIndex = 0;				// 移动索引
		this.slideIndex = 1;			// 幻灯位置索引, 默认为1
		this.touchBool = false;			// 触摸判断
		this.moveDis = 0;				// 保存当前位置
		this.timer;						// 定时器

		this.init();
	}
	Slider.prototype = {
		constructor: Slider,
		// 移动幻灯
		slideMove: function(x, s) {
			this.slide.css({
				'-webkit-transform': 'translate3d('+x+',0,0)',
				'-webkit-transform': 'translate3d('+x+',0,0)',
				'-webkit-transition-duration': s+'ms',
				'transition-duration': s+'ms',
				'-webkit-backface-visibility': 'hidden',
				'backface-visibility': 'hidden',
				'-webkit-perspective': '1000',
				'perspective': '1000'
			});
		},
		// 幻灯滑动切换 
		touchSlider: function() {
			var _self = this,
				startX = 0,
				moveX = 0,
				temp = 0;

			// 触摸开始
			this.banner.addEventListener(touchStart, function(e) {
				var e = e || window.event;
				startX = (hasTouch) ? e.touches[0].pageX : e.pageX;
				_self.touchBool = true;
				clearInterval(_self.timer);
			});
			// 触摸移动
			this.banner.addEventListener(touchMove, function(e) {
				var e = e || window.event;
				e.preventDefault();
				if (!_self.touchBool) return;
				moveX = (hasTouch) ? e.touches[0].pageX : e.pageX;
				temp = moveX - startX + _self.moveDis;

				_self.slideMove(temp+'px', 0);
			});
			// 触摸结束
			this.banner.addEventListener(touchEnd, function(e) {
				// 滑动距离
				if (Math.abs(moveX - startX) > 60) {
					// 向左滑动
					if (moveX > startX) {
						(_self.slideIndex <= 0) ? _self.slideIndex = _self.slideLen-1 : _self.slideIndex--;
						_self.moveIndex--;
						// 准备对应内容
						if (_self.slideIndex == 1) {
							_self.slideLi.eq(_self.slideLen-1).css({ 'left': (_self.moveIndex-1)*100+'%' });
						} else {
							_self.slideLi.eq(_self.slideIndex-2).css({ 'left': (_self.moveIndex-1)*100+'%' });
						}
					} else {
						// 向右滑动
						_self.nextMove();
					}
					// 设置当前所在位置
					_self.setMoveDis();
					// 设置导航位置
					_self.setNavLocal();
				}
				// 移动对应位置
				_self.slideMove(-_self.moveIndex*100+'%', _self.moveSpeed);
				_self.touchBool = false;
				_self.setIntervalTask();
			});

		},
		// 设置定时器任务
		setIntervalTask: function() {
			var _self = this;
			this.timer = setInterval(function() {
				_self.nextMove();
				// 移动对应位置
				_self.slideMove(-_self.moveIndex*100+'%', _self.moveSpeed);

				_self.setMoveDis();
				// 设置导航位置
				_self.setNavLocal();
			}, this.speed);
		},
		// 下一个移动位置
		nextMove: function() {
			(this.slideIndex >= this.slideLen-1) ? this.slideIndex = 0 : this.slideIndex++;
			// 位置索引
			this.moveIndex++;
			// 设置幻灯位置
			this.slideLi.eq(this.slideIndex).css({ 'left': (this.moveIndex+1)*100+'%' });
		},
		// 设置当前所在位置
		setMoveDis: function() {
			this.moveDis = -this.moveIndex * this.slideWidth;
		},
		// 设置每个幻灯片及导航的位置
		setSliderLocal: function() {
			var strHTML = '';
			for (var i=0; i<this.slideLen; i++) {
				strHTML += '<li></li>';
				// 设置每个幻灯位置
				if (i < this.slideLen - 1) {
					this.slideLi.eq(i).css({ "left": i*100+'%' });
				} else {
					// 最后一个
					this.slideLi.eq(i).css({ "left": '-100%' });
				}
			}
			// 是否显示导航
			if (!this.navFlag) {
				this.nav.hide();
			}
			// 设置导航
			this.nav.html(strHTML).find('li').eq(0).addClass(this.on);
		},
		// 设置导航当前位置
		setNavLocal: function() {
			this.nav.find('li').eq(this.slideIndex-1)
			.addClass(this.on).siblings().removeClass(this.on);
		},
		// 设置幻灯片高度 
		setSliderHeight: function() {
			if (this.slideLen > 0) {
				var height = this.slideLi.eq(0).find('img').height();
				this.slide.css({ "height": height+'px' });
			}
		},
		// 获取窗口宽度
		getWinWidth: function() {
			var width = $(window).width();
			// 获取屏幕宽度
			this.slideWidth = (width > 640) ? 640 : width;
			this.setMoveDis();
		},
		// 初始化
		init: function() {
			var _self = this;
			this.getWinWidth();
			this.setSliderLocal();
			this.setSliderHeight();
			this.touchSlider();
			// 开启定时器
			this.setIntervalTask();
			// 自适应
			$(window).on('resize', function() {
				_self.setSliderHeight();
				_self.getWinWidth();
			});
		}
	};
	window.Slider = Slider;

	
})(window, document);