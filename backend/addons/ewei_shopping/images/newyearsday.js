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

})(window);	


