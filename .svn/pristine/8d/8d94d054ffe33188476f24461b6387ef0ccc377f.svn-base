;(function(window, document, undefined) {

	function PrizeSettings(opts) {
		this.pType = opts.pType;			// 兑换类型
		this.pActType = opts.pActType;		// 活动类型
		this.pPro = opts.pPro;				// 奖励产品规则
		this.pCou = opts.pCou;				// 奖励兑换券规则
		this.ruleBtn = opts.ruleBtn;		// 添加规则按钮
		this.regNum = opts.regNum;			// 签到天数
		this.regFre = opts.regFre;			// 领取次数
		this.regPro = opts.regPro;			// 签到奖品
		this.selPro = opts.selPro;			// 签到产品
		this.refresh = opts.refresh;		// 换一换产品按钮
		this.selBtn = opts.selBtn;			// 选择产品按钮
		this.proTable = opts.proTable;		// 产品规则表格
		this.couTable = opts.couTable;		// 兑换券规则表格
		this.proType = opts.proType;		// 产品规则选择框
		this.couType = opts.couType;		// 兑换券输入框
		this.prizeBtn = opts.prizeBtn;		// 提交规则
		this.enabled = opts.enabled;		// 上线控制
		this.getProURL = opts.getProURL;	// 获取产品数据URL
		this.submitURL = opts.submitURL;	// 提交数据URL
		this.on = opts.on;					// 当前类
		this.eleArr = [];					// 保存元素数组

		this.init();
	}
	PrizeSettings.prototype = {
		constructor: PrizeSettings,
		// 类型切换
		typeTab: function() {
			var that = this;
			this.pType.on('change', function() {
				var val = $(this).val();
				// 切换按钮类型
				that.ruleBtn.attr('data-type', val);
				// 显示对应的内容框
				if (val == 1) {
					that.proType.addClass(that.on);
					that.couType.removeClass(that.on);
					that.pPro.addClass(that.on);
					that.pCou.removeClass(that.on);
				} else if (val == 2) {
					that.couType.addClass(that.on);
					that.proType.removeClass(that.on);
					that.pCou.addClass(that.on);
					that.pPro.removeClass(that.on);
				}
			});
		},
		// 点击选择签到产品
		regProSelect: function() {
			var that = this, 
				proPage = 1,
				tempI, num;
			this.regPro.on('click', function() {
				num = that.regNum.val();
				tempI = 0;
				that.selPro.find('li').removeClass(that.on);
				// 获取产品列表
				getProList(num, proPage); 
			});
			// 点击更新产品
			this.refresh.on('click', function() {
				proPage++;
				getProList(num, proPage);
			});
			// 获取产品内容
			function getProList(num, page) {
				$.ajax({
					type: 'post',
					url: that.getProURL,
					data: {num: num, page: page},
					dataType: 'json',
					success: function(data) {
						if (data.length != 0) {
							var len = data.length, 
							strHTML = '';
							if (len != 0) {
								for (var i=0; i < len; i++) {
									var d = data[i];
									strHTML += '<li data-id="'+d.id+'">\
									<img src="'+d.thumb+'"/>\
									<span>'+d.title+'</span>\
									</li>';
								}
								that.selPro.find('ul').html(strHTML);
								that.selPro.show();
							}
						} else {
							proPage = 1;
							getProList(num, proPage);
						}
						console.log(page);
					}
				});
			}
			// 选择产品事件
			!function selProEve() {
				$(document).on('click', '.select-pro li', function() {
					var $this = $(this);
					if ($this.hasClass(that.on)) {
						if (tempI > 0) tempI--;
						$this.removeClass(that.on);
					} else {
						if (tempI >= 5) {
							alert('最多只能选择5个产品');	
							return false;
						}
						tempI++;
						$this.addClass(that.on);
					}
				});
				// 点击选择产品确定按钮
				that.selBtn.on('click', function() {
					var idArr = [],
						nameArr = [];
					that.eleArr = [];
					that.selPro.find('li').each(function() {
						var $this = $(this);
						if ($this.hasClass(that.on)) {
							idArr.push($this.attr('data-id'));
							nameArr.push($this.find('span').text());
							that.eleArr.push($this.html());
						}
					});
					that.regPro.val(nameArr.join(',')).attr('data-id', idArr.join(','));
					that.selPro.hide();
				});
			}();
		},
		// 添加规则按钮
		addRuleBtnEve: function() {
			var that = this, flag = true;
			this.ruleBtn.on('click', function() {
				var $this = $(this),
					type = $this.attr('data-type'),
					num = that.regNum.val(),
					fre = that.regFre.val();
				if (num == '' || fre == '') {
					alert('签到天数及领取次数不能为空');
					return false;
				}
				if (type == 1) {
					var idArr = that.regPro.attr('data-id');
					that.regPro.val('').attr('data-id', '');
					addProRuleEve(num, fre, idArr);
				} else if (type == 2) {
					var $name = that.couType.find('input:eq(0)'),
						$money = that.couType.find('input:eq(1)'),
						name = $name.val(),
						money = $money.val();
					$name.val(''), $money.val('');
					addCouRuleEve(num, fre, name, money);
				}
				that.regNum.val('');
				that.regFre.val('');
			});
			// 添加产品规则到列表
			function addProRuleEve(num, fre, idArr) {
				var eleHTML = '',
					strHTML = '';
				// 遍历选择产品内容
				for (var i=0,len=that.eleArr.length; i<len; i++) {
					var ele = that.eleArr[i];
					strHTML += '<li>'+ele+'</li>';
				}

				eleHTML = '<tr data-id="'+idArr+'" data-num="'+num+'" data-fre="'+fre+'">\
					<td>'+num+'天</td>\
					<td>'+fre+'次</td>\
					<td>\
						<ul>'+strHTML+'</ul>\
					</td>\
					<td><a href="javascript:;" class="close prize-close" aria-label="Close"><span aria-hidden="true">&times;</span></a></td>\
				</tr>';
				that.proTable.append(eleHTML);
			}
			// 添加兑换券规则到列表
			function addCouRuleEve(num, fre, name, money) {
				var eleHTML = '<tr data-num="'+num+'" data-fre="'+fre+'" data-name="'+name+'" data-money="'+money+'">\
					<td>'+num+'天</td>\
					<td>'+fre+'次</td>\
					<td>'+name+'</td>\
					<td>'+money+'</td>\
					<td><a href="javascript:;" class="close prize-close" aria-label="Close"><span aria-hidden="true">&times;</span></a></td>\
				</tr>';
				that.couTable.append(eleHTML);
			}
			// 绑定动态数据元素事件
			$(document).on('click','.prize-close',function(){
		   	 	if (confirm('你确定删除这个签到规则？')) {
					$(this).parents('tr').remove();
				}
		    });
		},
		submitRuleBtn: function() {
			var that = this;
			this.prizeBtn.on('click', function() {
				var actType = that.pActType.val(),
					type = that.pType.val(),
					enabled = $('input[name="'+that.enabled+'"]:checked').val(),
					proTr = that.proTable.find('tr'),
					couTr = that.couTable.find('tr'),
					proArr = [],
					couArr = [];

				if (type == 1) {
					// 产品列表
					if (proTr.length > 1) {
						for (var i=1,pLen=proTr.length; i<pLen; i++) {
							var tr = proTr.eq(i),
								li = tr.find('li'),
								id = tr.attr('data-id'),
								pNum = tr.attr('data-num'),
								pFre = tr.attr('data-fre'),
								nameObj = {};
							// proArr[i-1] = [pNum, id];
							for (var k=0,kLen=li.length;k<kLen;k++) {
								var liName = li.eq(k),
									idArr = id.split(','),
									name = liName.find('span').text(),
									key = idArr[k];
								nameObj[key] = name;
							}
							proArr[i-1] = [pNum, pFre, nameObj];
						}
						console.log(proArr);
					}
				} else if (type == 2) {
					// 兑换券列表
					if (couTr.length > 1) {
						for (var j=1,cLen=couTr.length; j<cLen; j++) {
							var tr = couTr.eq(j),
								cNum = tr.attr('data-num'),
								cFre = tr.attr('data-fre'),
								name = tr.attr('data-name'),
								money = tr.attr('data-money');
							couArr[j-1] = [cNum, cFre, name, money];
						}
						console.log(couArr);
					}
					
				}

				if (type == 1 && proArr.length != 0) {
					submitAjaxData(actType, type, enabled, proArr);
				} else if (type == 2 && couArr.length != 0) {
					submitAjaxData(actType, type, enabled, couArr);
				} else {
					alert('请添加兑换规则！');
				}
				return false;

			});
			function submitAjaxData(actType, type, enabled, typeArr) {
				var dataObj;
				if (type == 1) {
					dataObj = {actType: actType, type: type, enabled: enabled, proArr: typeArr}
				} else {
					dataObj = {actType: actType, type: type, enabled: enabled, couArr: typeArr}
				}
				$.ajax({
					type: 'post',
					url: that.submitURL,
					data: dataObj,
					dataType: 'json',
					success: function(data) {
						if (data.status == 200) {
							alert('奖品添加成功！');
							location.href = data.link;
							// location.reload();
						}
					}
				});
			}
		},
		init: function() {
			this.typeTab();
			this.regProSelect();
			this.addRuleBtnEve();
			this.submitRuleBtn();
		}
	};

	window.PrizeSettings = PrizeSettings;

})(window, document);