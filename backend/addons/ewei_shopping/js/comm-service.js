/*
* 社区服务
*/

;(function(window, document, undefined) {

    var hasTouch = "ontouchstart" in window ? true : false,
        touchStart = hasTouch ? "touchstart" : "mousedown",
        touchMove = hasTouch ? "touchmove" : "mousemove",
        touchEnd = hasTouch ? "touchend" : "mouseup"; 

    /**
     * 社区服务构造函数
     * @param opts 参数对象
     */
	function CommService(opts) {
		this.list = opts.list;					// 数据列表
		this.nav = opts.nav;					// 导航
		this.con = opts.con;
		this.on = opts.on;
		this.h = parseInt(document.documentElement.clientHeight || document.body.clientHeight);		// 获取屏幕高度
		this.topArr = [];						// 顶部位置

		this.init();
	}
	CommService.prototype = {
		constructor: CommService,
		// 滚动固定导航
		scrollFixed: function() {
            var _self = this, top;
                top = parseInt(this.nav.offset().top);
            // 获取顶部位置
            _self.con.find(_self.list).each(function() {
            	var top = $(this).offset().top;
            	_self.topArr.push(top - 100);
            });
            fixedNav();		// 默认判断是否需要固定导航栏
            $(document).on('scroll', function() {
            	fixedNav();
            });
            function fixedNav() {
                var scrTop = parseInt(document.documentElement.scrollTop || document.body.scrollTop);             
                if (top < scrTop + 5) {
                    _self.nav.addClass(_self.on);
                    _self.con.addClass(_self.on);
                } else {
                    _self.nav.removeClass(_self.on);
                    _self.con.removeClass(_self.on);
                }
                // 遍历获取位置, 设置位置
                for (var i=0,len=_self.topArr.length; i<len; i++) {
                	if (scrTop > _self.topArr[i]) {
                		_self.nav.find('li').eq(i).addClass(_self.on).siblings().removeClass(_self.on);
                	}
                }
            }
        },
        // 点击导航滚动指定位置
        gotoNavLocation: function() {
        	var _self = this,
        		flag = false;
        	this.nav.find('li').on('click', function() {
        		var pcate = $(this).attr('data-pcate');
        		_self.con.find(_self.list).each(function() {
        			if ($(this).attr('data-pcate') == pcate) {
        				var top = $(this).offset().top;
        				scrollLocation(top-70);
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
		init: function() {
			var _self = this, fn;
			this.gotoNavLocation();
			window.onload = function() {
				_self.scrollFixed();
			}
		}
	};
	
    window.CommService = CommService;

    /**
     * 关注按钮提示弹出框
     */
    function AttentionPrompt(opts) {
        this.btn = opts.btn;
        this.box = opts.box;
        this.boxMain = opts.boxMain;

        this.init();
    }
    AttentionPrompt.prototype = {
        constructor: AttentionPrompt,
        btnShowAtten: function() {
            var _self = this;
            this.btn.on(touchEnd, function() {
                _self.box.fadeIn();
                _self.boxMain.fadeIn();
            });
            this.box.on(touchEnd, function() {
                _self.box.fadeOut();
                _self.boxMain.fadeOut();
            });
        },
        init: function() {
            this.btnShowAtten();
        }
    };

    window.AttentionPrompt = AttentionPrompt;

    /**
     * 更改社区
     */
    function CommunityChange(opts) {
        this.list = opts.list;
        this.load = opts.load;
        this.url = opts.url;

        this.init();
    }
    CommunityChange.prototype = {
        constructor: CommunityChange,
        selectComm: function() {
            var _self = this;
            this.list.on(touchEnd, function() {
                var id = $(this).attr('data-id');
                if (id) {
                    _self.load.show().find('i').show();
                    _self.load.find('span').text('正在更换社区...');
                    $.ajax({
                        type: 'post',
                        url: _self.url,
                        data: {groupid: id},
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 200) {
                                location.href = data.link;
                            } else {
                                _self.load.find('i').hide();
                                _self.load.find('span').text('更换社区失败，请重新选择！');
                                setTimeout(function(){
                                    _self.load.hide();
                                }, 2000);
                            }
                        }
                    });
                }
            });
        },
        init: function() {
            this.selectComm();
        }
    };

    window.CommunityChange = CommunityChange;

    /**
     * 申请社区主
     */
    function AppCommunity(opts) {
        this.commName = opts.commName;
        this.sel = opts.sel;
        this.addr = opts.addr;
        this.username = opts.username;
        this.phone = opts.phone;
        this.btn = opts.btn;

        this.init();
    }
    AppCommunity.prototype = {
        constructor: AppCommunity,
        submitDataInfo: function() {
            var _self = this;

            this.btn.on(touchEnd, function() {
                var commName = _self.commName.val(),
                    addr = _self.addr.val(),
                    username = _self.username.val(),
                    phone = _self.phone.val(),
                    reg = /^[+]{0,1}(\d){1,3}[ ]?([-]?((\d)|[ ]){1,12})+$/,
                    info = '';
                if (commName == '') {
                    info = '请输入小区名称';
                } else {
                    _self.sel.each(function() {
                        var val = $(this).val();
                        if (val == '' || val == '市辖区') {
                            info = '请输入小区地址';
                            return;
                        }
                    });
                }
                if (info == '') {
                    if (addr == '') {
                        info = '请输入详细地址';
                    } else if (username == '') {
                        info = '请输入您的姓名';
                    } else if (phone == '') {
                        info = '请输入您的手机号码';
                    } else if (!reg.test(phone)) {
                        info = '请输入正确的手机号码';
                    }
                }
                if (info != '') {
                    Util.promptBox({
                        title: 'note',
                        info: info,
                        leftBtn: '确定'
                    });
                    return false;
                }
            });
        },
        init: function() {
            this.submitDataInfo();
        }
    };

    window.AppCommunity = AppCommunity;

})(window, document);