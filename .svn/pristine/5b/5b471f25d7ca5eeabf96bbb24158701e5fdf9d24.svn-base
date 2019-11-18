// loading 加载页面 begin
var load = document.getElementById('loading'),
	progress = document.getElementById('loading_rate'),
	first = document.getElementById('first');
// 加载图片资源
var loadingImage = (function() {

	var img_list = [
		'arrow.png',"bg1.jpg","bg2.jpg","music.png","page-logo.png","page1-1.png","page1-3.png","page1-4.png","page1-5.png","page2-1.png","page2-2.png","page2-3.png","page3-1.png","page3-2.png","page3-3.png","page4-1.png","page4-2.png","page4-3.png","page4-4.png","page4-5.png","page5-1.png","page5-2.png","page5-3.png","page6-1.png","page6-2.png","page7-1.png","page7-2.png","page7-3.png","page7-4.png","page7-5.png","page7-6.png","page7-7.png","page7-8.png"
	];

	var img_len = img_list.length,
		imgPath = '/addons/ewei_shopping/images/img/doubleeleven/',
		load_num = 0,
		imgArr = [];
	for (var i = 0; i < img_len; i++) {
		imgArr.push(imgPath + img_list[i]);
	}
	function loadImage(path, callback) {
		var img = new Image();
		img.src = path;
		img.onload = function() {
			img.onload = null;
			callback && callback();
		};
	}
	function imgLoader(imgs, callback) {
		var len = imgs.length, i = 0;
		while (imgs.length) {
			loadImage(imgs.shift(), function() {
				callback && callback(++i, len);
			});
		}
	}
	imgLoader(imgArr, function(curNum, len) {
		load_num = curNum / len;
		progress.innerHTML = Math.floor(load_num * 100) + '%';
		if (load_num == 1) {
			setTimeout(function() {
				load.style.opacity = 0;
				setTimeout(function() {
					load.style.display = 'none';
					first.className += ' show';
				}, 200);
			}, 500);
		}
	});

})();
// loading 加载页面 end

// 检测是否竖屏
;(function(window, undefined) {
	var vertical = document.getElementById('vertical'),
		orientation = 'onorientationchange' in window,
		w, h;
	function verticalFun() {
		w = document.documentElement.clientWidth;
		h = document.documentElement.clientHeight;
		w > h ? vertical.style.display = 'block' : vertical.style.display = 'none';
	}
	
	orientation ? (window.addEventListener('orientationchange', function() {
		(window.orientation != 0) ? vertical.style.display = 'block' : vertical.style.display = 'none';
	}, false)) : 
	(verticalFun(), (window.addEventListener('resize', verticalFun, false)));
	
})(window);

(function(window, $, undefined) {

	var hasTouch = 'ontouchstart' in window ? true : false,
		touchStart = hasTouch ? 'touchstart' : 'mousedown',
		touchMove = hasTouch ? 'touchmove' : 'mousemove',
		touchEnd = hasTouch ? 'touchend' : 'mouseup';

	// **************************************
	// 触摸切换屏幕内容构造函数
	// **************************************
	function TouchSliderVer(opts) {
		this.touch = opts.touch;		// 触摸元素
		this.slider = opts.slider;	// 移动切换元素
		this.box = this.slider.find(opts.box);		// 内容元素
		this.len = this.box.length; 	// 切换长度
		this.aniTime = opts.aniTime;	// 滑动时间
		this.jobForm = opts.jobForm;	// 加入我们表单
		this.cur = opts.cur;			// 当前位置类
		this.h = $(window).height();	// 屏幕高度
		this.startY = 0;				// 触摸开始位置
		this.moveY = 0;					// 触摸经过
		this.curY = 0;					// 当前移动位置
		this.i = 0;						// 当前位置
		this.flag = false;				// 设置pc移动鼠标
	}
	// 原型方法
	TouchSliderVer.prototype = {
		constructor:TouchSliderVer,
		// 移动页面
		translateFun: function(y, s, flag) {
			var _self = this;
			// 如果是松开手，则获取对应的位置
			if (flag) {
				this.curY = -(this.i * this.h);		// 获取屏幕高度，并移动对应屏
				y = this.curY;
			}
			this.slider.css({
				"-webkit-transform": "translate3d(0,"+y+"px,0)",
				"transform": "translate3d(0,"+y+"px,0)",
				"-webkit-transition-duration": s+"ms",
				"transition-duration": s+"ms"
			});
			// 延迟变化内容
			setTimeout(function() {
				_self.box.removeClass(_self.cur).eq(_self.i).addClass(_self.cur);
				// 如果不是最后一屏
				if (_self.i != _self.len - 1) _self.jobForm.css('bottom', '-100%');
			}, _self.aniTime);
		},
		// 触摸开始
		touchStartFun : function(e) {
			var e = e || window.event;
			this.startY = parseInt(hasTouch ? e.touches[0].pageY : e.clientY);
			this.moveY = this.startY;
			this.flag = true;
		}, 
		// 触摸移动
		touchMoveFun : function(e) {
			if (!this.flag) return;

			var e = e || window.event, y;
			e.preventDefault();			// 阻止默认
			this.moveY = parseInt(hasTouch ? e.touches[0].pageY : e.clientY);
			y = this.moveY - this.startY + this.curY;	// 计算移动距离
			this.translateFun(y, 0, false);
		}, 
		// 触摸结束
		touchEndFun : function(e) {
			// 如果是第一屏
			if (this.moveY - this.startY > 0 && this.i == 0) {
			} else if (this.moveY - this.startY < 0 && this.i == this.len - 1) {
				// 如果是最后一屏
			} else if (this.moveY - this.startY > 80) {
				// 如果高度滑动大于20像素，则向上移动一屏
				this.i--;
			} else if (this.moveY - this.startY < -80) {
				// 如果高度滑动小于20像素，则向下移动一屏
				this.i++;
			}
			// 执行移动位置操作
			this.translateFun(0, this.aniTime, true);
			// 如果到最后一屏，向上箭头隐藏
			// (this.i == this.len - 1) ? this.arrow.hide() : this.arrow.show();

			this.flag = false;
		}, 
		// 初始化
		init: function() {
			var _self = this;
			_self.touch.on(touchStart, function(e) {
				_self.touchStartFun(e);
			});
			_self.touch.on(touchMove, function(e) {
				_self.touchMoveFun(e);
			});
			_self.touch.on(touchEnd, function(e) {
				_self.touchEndFun(e);
			});
			// 窗口大小发生变化
			$(window).on('resize', function() {
				_self.h = $(this).height();
				_self.translateFun(0, _self.aniTime, true);
			});
		}
	};

	window.TouchSliderVer = TouchSliderVer;


	// *******************************
	// 音乐播放功能
	// *******************************
	function AudioPlay(opts) {
		this.audio = opts.audio;	// 音乐
		this.btn = opts.btn;		// 播放按钮
		this.off = opts.off;		// 播放状态

		this.musicPlay();			// 音乐播放
	}
	AudioPlay.prototype = {
		constructor: AudioPlay,
		musicPlay: function() {
			var _self = this,
				trigger = "ontouchend" in document ? 'touchstart' : 'mouseup';

			function start() {
				document.removeEventListener(trigger, start, false);
				if (!_self.audio.paused) return;	// 如果是音频是播放的，则返回
				_self.audio.play();
			}
			function toggle() {
				// 检测播放还是暂停
				if (!_self.audio.paused) return _self.audio.pause();
				_self.audio.play();
			}
			function playFun() {
				_self.btn.className = '';
			}
			function pauseFun() {
				_self.btn.className = _self.off;
			}
			// 按钮事件
			this.audio.addEventListener('play', playFun, false);
			this.audio.addEventListener('pause', pauseFun, false);
			this.btn.addEventListener('click', toggle, false);
			this.audio.play();
			// 苹果手机默认不自动触发播放，绑定触摸触发播放
			document.addEventListener(trigger, start, false);
		}
	};

	window.AudioPlay = AudioPlay;

})(window, $);

// 滑动页面
new TouchSliderVer({
	touch: $('#container'),
	slider: $('#main'),
	jobForm: $('.job-form'),
	box: ".page",
	cur: 'show',
	aniTime: 500
}).init();

// 音乐播放
new AudioPlay({
	audio: document.getElementById('audio'),
	btn: document.getElementById('audioPlay'),
	off: 'off'
});