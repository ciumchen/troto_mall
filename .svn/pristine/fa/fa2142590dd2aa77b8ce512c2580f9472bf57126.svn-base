define(function() {
 var uid =  GetQueryString('uid');
	function GetQueryString(name)
	{
	     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	     var r = window.location.search.substr(1).match(reg);
	     if(r!=null)return  unescape(r[2]); return null;
	}

	/* 公用点击选择日期事件 */
	function selDayBtn(obj) {
		obj.btn.on('click', function() {
            var $this = $(this),
                day = $this.attr(obj.dataDay);
            if (!$this.hasClass(obj.on)) {
                $this.addClass(obj.on).siblings().removeClass(obj.on);
                obj.callback && obj.callback(day);
            }
        });
	}
	/* 公用发送Ajax请求 */
	function sendAjaxData(obj) {
        obj.chart.showLoading();
        $.ajax({
            type: 'post',
            url: obj.url,
            data: obj.data,
            dataType: 'json',
            success: function(data) {
            	obj.callback && obj.callback(data);
            }
        });
    }

    // 用户商品搜索统计
    function barSearchChartData(obj) {
    	var option = {
		    calculable: true,
		    tooltip: {
		        trigger: 'item'
		    },
		    grid: {
		        borderWidth: 0,
		        y: 40,
		        y2: 60
		    },
		    xAxis: [
		        {
		            type: 'category',
		            show: false,
		            data: ['五常大米', '品上工坊燕窝', 'Scatter', 'K', 'Pie', 'Radar', 'Chord', 'Force']
		        }
		    ],
		    yAxis: [
		        {
		            type: 'value',
		            show: false
		        }
		    ],
		    series: [
		        {
		            name: '用户商品详情访问统计',
		            type: 'bar',
		            itemStyle: {
		                normal: {
		                    color: function(params) {
		                        // build a color map as your need.
		                        var colorList = [
		                          '#C1232B','#B5C334','#FCCE10','#E87C25','#27727B',
		                           '#FE8463','#9BCA63','#FAD860','#F3A43B','#60C0DD',
		                           '#D7504B','#C6E579','#F4E001','#F0805A','#26C0C0'
		                        ];
		                        return colorList[params.dataIndex]
		                    },
		                    label: {
		                        show: true,
		                        position: 'bottom',
		                        formatter: '{b}\n{c}'
		                    }
		                }
		            },
		            data: [12,21,10,4,12,5,6,5,25,23,7]
		        }
		    ]
		};

        // 绑定点击事件
        selDayBtn({
        	btn: obj.btn,
        	dataDay: 'data-day',
        	on: 'on',
        	callback: function(day) {
        		sendAjaxData({
        			chart: obj.myChart,
        			url: obj.chartUrl,
        			data: {type: 'Search', day: day, uid: uid},
        			callback: function(data) {
        				setChartBarData(data);
        			}
        		});
        	}
        });

        // 获取初始数据
        sendAjaxData({
			chart: obj.myChart,
			url: obj.chartUrl,
			data: {type: 'Search', day: 1, uid: uid},
			callback: function(data) {
				setChartBarData(data);
			}
		});

        /* 获取用户信息数据 */
        function setChartBarData(data) {
            // 图标清空
            obj.myChart.clear();
        	if (data != null && data != undefined && data != '' && data['Search'].length > 0) {
        		var data = data['Search'],
        			xAxisArr = [],
        			seriesArr = [];	
    			for (var i=0,len=data.length; i<len; i++) {
    				xAxisArr.push(data[i].searchtxt);
    				seriesArr.push(data[i].count);
    			}
    			option.xAxis[0].data = xAxisArr;
    			option.series[0].data = seriesArr;

        		obj.myChart.hideLoading();
	            // 重新加载数据对象
	            obj.myChart.setOption(option);
        	} else {
        		obj.myChart.showLoading({
				    text : '暂无数据',
				    effect : 'bubble',
				    textStyle : {
				        fontSize : 25
				    }
				});
        	}
        }
    }

    // 用户商品分类访问统计
    function pieClassChartData(obj) {
    	var option = {
		    tooltip : {
		        trigger: 'item',
		        formatter: "{a} <br/>{b} : {c} ({d}%)"
		    },
		    legend: {
		        orient : 'horizontal',
		        x : 'center',
		        data:['直接访问','邮件营销','联盟广告','视频广告','搜索引擎']
		    },
		    calculable : true,
		    series : [
		        {
		            name:'访问来源',
		            type:'pie',
		            radius : '55%',
		            center: ['50%', '60%'],
		            data:[
		                {value:335, name:'直接访问'},
		                {value:310, name:'邮件营销'},
		                {value:234, name:'联盟广告'},
		                {value:135, name:'视频广告'},
		                {value:1548, name:'搜索引擎'}
		            ]
		        }
		    ]
		};
		

		// 绑定点击事件
        selDayBtn({
        	btn: obj.btn,
        	dataDay: 'data-day',
        	on: 'on',
        	callback: function(day) {
        		sendAjaxData({
        			chart: obj.myChart,
        			url: obj.chartUrl,
        			data: {type: 'Class', day: day, uid: uid},
        			callback: function(data) {
        				setChartBarData(data);
        			}
        		});
        	}
        });

        // 初始化数据
        sendAjaxData({
			chart: obj.myChart,
			url: obj.chartUrl,
			data: {type: 'Class', day: 1, uid: uid},
			callback: function(data) {
				setChartBarData(data);
			}
		});

        function setChartBarData(data) {
            // 图标清空
            obj.myChart.clear();
        	if (data != null && data != undefined && data != '' && data['Class'].length > 0) {
        		var data = data['Class'],
        			legendArr = [],
        			seriesArr = [];

        		for (var i=0,len=data.length; i<len; i++) {
        			legendArr.push(data[i].name);
        			seriesArr.push({
        				value: data[i].count,
        				name: data[i].name
        			});
        		}
        		option.legend.data = legendArr;
        		option.series[0].data = seriesArr;

        		obj.myChart.hideLoading();
	            // 重新加载数据对象
	            obj.myChart.setOption(option);	
        	} else {
        		obj.myChart.showLoading({
				    text : '暂无数据',
				    effect : 'bubble',
				    textStyle : {
				        fontSize : 25
				    }
				});
        	}
        }

    }

    /* 用户商品详情访问统计 */
    function barDetChartData(obj) {
    	var option = {
		    tooltip : {
		        trigger: 'axis',
		        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
		            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
		        },
		        formatter: function (params){
		            return params[0].name + '<br/>'+params[0].value+' 次';
		        }
		    },
		    calculable : true,
		    xAxis : [
		        {
		            type : 'category',
		            data : ['Cosco','CMA','APL','OOCL','Wanhai','Zim']
		        }
		    ],
		    yAxis : [
		        {
		            type : 'value',
		            boundaryGap: [0, 0.1]
		        }
		    ],
		    series : [
		        {
		            name:'Acutal',
		            type:'bar',
		            stack: 'sum',
		            barCategoryGap: '50%',
		            itemStyle: {
		                normal: {
		                    color: 'tomato',
		                    barBorderColor: 'tomato',
		                    barBorderWidth: 6,
		                    barBorderRadius:0,
		                    label : {
		                        show: true, position: 'insideTop'
		                    }
		                }
		            },
		            data:[260, 200, 220, 120, 100, 80]
		        }
		    ]
		};

	   	// 绑定点击事件
        selDayBtn({
        	btn: obj.btn,
        	dataDay: 'data-day',
        	on: 'on',
        	callback: function(day) {
	        		sendAjaxData({
	        			chart: obj.myChart,
	        			url: obj.chartUrl,
	        			data: {type: 'Detail', day: day, uid: uid},
	        			callback: function(data) {
	        				setChartBarData(data);
	        			}
	        		});
        	}
        });
        // 获取初始数据  
	        sendAjaxData({
				chart: obj.myChart,
				url: obj.chartUrl,
				data: {type: 'Detail', day: 1, uid:uid},
				callback: function(data) {
					setChartBarData(data);
				}
			});


		/* 获取用户信息数据 */
        function setChartBarData(data) {
        	// 图标清空
	        obj.myChart.clear();
        	if (data != null && data != undefined && data != '' && data['Detail'].length > 0) {
        		var data = data['Detail'],
        			xAxisArr = [],	// 清空数据
        			seriesArr = [];	
    			for (var i=0,len=data.length; i<len; i++) {
    				var title = data[i].title;
    				if (title != undefined && title != null) {
	    				xAxisArr.push(title);
	    				seriesArr.push(data[i].count);
    				}
    			}
    			console.log(option);
    			console.log(data);
    			option.xAxis[0].data = xAxisArr;
    			option.series[0].data = seriesArr;

        		obj.myChart.hideLoading();
	           
	            // 重新加载数据对象
	            obj.myChart.setOption(option);
        	} else {
        		obj.myChart.showLoading({
				    text : '暂无数据',
				    effect : 'bubble',
				    textStyle : {
				        fontSize : 25
				    }
				});
        	}
        }
    }

	return {
		sendAjax: sendAjaxData,
		barDet: barDetChartData,
		barSearch: barSearchChartData,
		pieClass: pieClassChartData
	}
});
