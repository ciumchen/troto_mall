/***************************
* 回到顶部
***************************/
function GotoTop(opts) {
	this.el = opts.el;		//回到顶部元素
	this.delay = opts.delay;	// 显示隐藏过渡时间
}
GotoTop.prototype = {
	constructor: GotoTop,
	// 判断滚动位置
	scrollLocation: function() {
		var self = this,
			scrTop = 0,
			timer;
		function location() {
			scrTop = parseInt(document.documentElement.scrollTop || document.body.scrollTop);				
			clearTimeout(timer);
			if (scrTop >= 200) {
				self.el.style.display = 'block';
				timer = setTimeout(function() {
					self.el.style.opacity = 1;
				}, self.delay);
			} else {
				self.el.style.opacity = 0;
				timer = setTimeout(function() {
					self.el.style.display = 'none';
				}, self.delay);
			}
		}
		window.addEventListener('scroll', location, false);
		// 回到顶部
		this.el.addEventListener('click', function() {
			$('html, body').animate({
                scrollTop: 0
            }, 200);
		},false);
	},
	init: function() {
		this.scrollLocation();
	}
};
// 回到顶部
var gotoTop = new GotoTop({
	el: document.getElementById('gotoTop'),
	delay: 100
}).init();


;(function(window, document, undefined) {

    var timerFun = function() {
        return {
            // 倒计时功能
            toDouble: function(num){
                num>=10 ? num=''+num : num='0'+num ;
                return num;
            },
            timer: function(year,month,day,hour, minute,seconds,service_time,elem){
                var that = this;
                var service = parseInt(service_time);
                var $hourEle = elem.find('.hour'),
                    $minuteEle = elem.find('.minute'),
                    $secondsEle = elem.find('.seconds');
                //当小时，分钟，秒不存在时将其设置为0
                var hour=hour || 0,
                    minute=minute || 0,
                    seconds=seconds || 0;
                //设置结束时间 ，用new Date(Date.UTC())设置不准，因为没有规定时间格式
                var endTime=new Date(); 
                    //创建年份
                    endTime.setFullYear(parseInt(year)),
                    //创建月份 必须要减去一个月，不然会多出一个月，因为九月还没有过完会多出一个月
                    endTime.setMonth(parseInt(month)-1),
                    //创建日期
                    endTime.setDate(parseInt(day)),
                    //创建小时
                    endTime.setHours(parseInt(hour)),
                    //创建分钟
                    endTime.setMinutes(parseInt(minute)),
                    //创建秒钟
                    endTime.setSeconds(parseInt(seconds));
                setTime(service);
                var setTimer = setInterval(function(){
                    setTime(service);
                    service += 1000;
                },1000);
                //设置差距秒
                function setTime(s){
                    //设置开始时间
                    var startTime=new Date(s);
                    var lengthTime=parseInt((endTime.getTime()-startTime.getTime())/1000);
                    //parseInt,Math.floor都有取整的意思
                    var lSeconds=parseInt(lengthTime % 60),
                        //Math.floor(lengthTime/60)获得多少分钟，%60取余，获得多少分钟
                        lMinute=parseInt((lengthTime/60)%60), 
                        //Math.floor(lengthTime/3600)获得多少小时，%24取余，获得多少小时
                        lHour=Math.floor((lengthTime/3600)%24),
                        //获得多少天
                        lDay=Math.floor(lengthTime/(24*3600));
                    if (lHour <= 0 && lMinute <= 0 && lSeconds <= 0) {
                        clearInterval(setTimer);
                        $hourEle.text('00');
                        $minuteEle.text('00');
                        $secondsEle.text('00');
                        // location.reload();
                        return false;
                    }
                    //添加内容
                    $hourEle.text(that.toDouble(lHour));
                    $minuteEle.text(that.toDouble(lMinute));
                    $secondsEle.text(that.toDouble(lSeconds));
                }   
            }
        };
    }();
    
    window.timerFun = timerFun;

    // 收藏功能
    function Collect(opts) {
        this.btn = opts.btn;
        this.url = opts.url;
        this.on = opts.on;

        this.init();
    }
    Collect.prototype = {
        constructor: Collect,
        collectFun: function() {
            var that = this;
            that.btn.on('touchend', function(e) {
                e.stopPropagation();
                var $this = $(this);
                var id = $this.attr('data-goodsid');
                $.ajax({
                    type: 'get',
                    url: that.url,
                    data: {id: id},
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 200) {
                            if (!$this.hasClass(that.on)) {
                                $this.addClass(that.on);
                                Util.dialog.showMessage('<i class="iconfont ok"></i>收藏成功');
                            } else {
                                $this.removeClass(that.on);
                                Util.dialog.showMessage('<i class="iconfont no"></i>取消收藏');
                            }
                        } else {
                            Util.dialog.showMessage(data.msg);
                        }
                    }
                });
            });
        },
        init: function() {
            this.collectFun();
        }
    };

    window.Collect = Collect;

})(window, document);
