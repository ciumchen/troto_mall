(function (doc, window) {
    var docEl = doc.documentElement,
        resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
        recalc = function () {
            var clientWidth = docEl.clientWidth;
            if (!clientWidth) return;
            docEl.style.fontSize = (20 * (clientWidth / 320)) > 40 ? 40 + "px" : (20 * (clientWidth / 320)) + 'px';
        },
        anime = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame ||
                function(e){
                    return setTimeout(e,16.67);
        };
    if (!doc.addEventListener) return;
    window.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);

    window.$$ = function(selector){
        var selectorType = "querySelectorAll";
        if(selector.indexOf("#") === 0){
            selectorType = "getElementById";
            selector = selector.substr(1,selector.length);
        }
        return document[selectorType](selector);
    };
    function slide(obj,pagina){
        var _this = this;
        _this.time = 5;
        _this.obj = typeof obj == "string" ? $$(obj) : obj;
        _this.ul = _this.obj.querySelector("ul");
        _this.children = _this.ul.children;
        _this.length =_this.children.length;
        _this.index = 0;
        _this.afTime = _this.time * 60;
        _this.li = document.createElement("li");
        _this.transform.call(_this.ul,-100);
        if(pagina){
            _this.cdf = document.createDocumentFragment();
            _this.arr = [];
            _this.pagina =$$(pagina)[0];
            _this.paginas();
        }
        _this.init();
    }
    slide.prototype = {
        init:function(){
            var _this = this,
                    flag = 0;
            _this.li.innerHTML = _this.children[_this.length -1].innerHTML;
            _this.ul.insertBefore(_this.li,_this.children[0]); ++_this.length;
            _this.ul.appendChild(_this.children[1].cloneNode(true)); ++_this.length;
            _this.ul.idx = 1;
            _this.ul.len = _this.length;
            while(_this.length--){
                _this.children[_this.length].style.left = (_this.length * 100)+"%";
            }
            function setp(){
                if(_this.index == -1){
                    return
                }
                if(++_this.index > _this.afTime ){

                    _this.index = 0;

                    if(_this.ul.idx==1){
                        _this.ul.classList.add("anim")
                    }
                    _this.ul.idx++;
                    _this.pagina && _this.pagSwitch(_this.ul.idx,_this.ul.len);
                    _this.transform.call(_this.ul,-(_this.ul.idx) *100)
                }
                anime(setp);
            }
	        //setp();
            var auto =function(){
                if(this.idx == this.len-1){
                    _this.ul.classList.remove("anim");
                    _this.transform.call(this,-(this.idx = 1) *100)
                }
            };
            var tapSlide=function(e){
                switch(e.type){
                    case "mousedown":
                    case "touchstart":
                        flag = 1;
                        _this.index = -1;
                        this.ox = e.touches[0].clientX;
                        this.oy = e.touches[0].clientY;
                        this.ow = this.clientWidth;
                        this.st = e.timeStamp;
                        this.isUp = 0;
                        this.classList.remove("anim");
                        if(this.idx == this.len-1){
                            _this.ul.classList.remove("anim");
                            _this.transform.call(this,-(this.idx = 1) *100)
                        }
                        if(this.idx == 0){
                            _this.ul.classList.remove("anim");
                            _this.transform.call(this,-(this.idx = this.len-2)*100)
                        }
                        break;

                    case "mousemove":
                    case "touchmove":
                        if(flag){
                            e.stopPropagation();
                            e.preventDefault();
                            _this.transform.call(this, ( - this.idx + (e.changedTouches[0].clientX - this.ox) / this.ow) * 100);
                        }
                        break;

                    case "mouseup":
                    case "mouseleave" :
                    case "touchcancel":
                    case "touchend" :
                        if(flag){
                            var changeX = e.changedTouches[0].clientX;
                            this.classList.add("anim");
                            if (!this.isUp && changeX != this.ox) {
                                if(e.timeStamp - this.st >300 || Math.abs(changeX - this.ow) > this.ow/3){
                                    this.idx += changeX > this.ox ? -1 : 1;
                                    this.idx = Math.min(Math.max(0, this.idx), this.len);
                                }
                                _this.transform.call(this, -this.idx * 100)
                            }
                            _this.pagina &&  _this.pagSwitch(_this.ul.idx,_this.ul.len);
                            _this.index = 1;
                            flag = 0;
                            //anime(setp);
                        }
                }
            };
            _this.ul.addEventListener(slide.prefix=="t" ? "transitionend" : slide.prefix + "TransitionEnd",auto,false);
            if (_this.paginaLen > 1) {
	            _this.ul.addEventListener("touchstart",tapSlide,false);
	            _this.ul.addEventListener("touchmove",tapSlide,false);
	            _this.ul.addEventListener("touchend",tapSlide,false);
	            _this.ul.addEventListener("touchcancel",tapSlide,false);
            }
            if(!("ontouchstart" in window)){
                _this.ul.addEventListener('mousedown',
                        function(m){
                            m.touches = [{
                                clientX : m.clientX,
                                clientY : m.clientY
                            }];
                            tapSlide.call(this,m)
                        },false);
                _this.ul.addEventListener('mousemove',
                        function(m){
                            m.changedTouches = [{
                                clientX : m.clientX,
                                clientY : m.clientY
                            }];
                            tapSlide.call(this,m)
                        },false);
                _this.ul.addEventListener('mouseup',
                        function(m){
                            m.changedTouches = [{
                                clientX : m.clientX,
                                clientY : m.clientY
                            }];
                            tapSlide.call(this,m)
                        },false);
                _this.ul.addEventListener('mouseleave',
                        function(m){
                            m.changedTouches = [{
                                clientX : m.clientX,
                                clientY : m.clientY
                            }];
                            tapSlide.call(this,m)
                        },false);
            }
        },
        paginas:function(){
            var i= 0,
                length = this.length;
            this.paginaLen = length;
            if (length > 1) {
	            for(;i<length;i++){
	                this.i = document.createElement("i");
	                this.cdf.appendChild(this.i);
	                this.arr.push(this.i);
	            }
	            this.arr[0].className= "active";
	            this.pagina.appendChild(this.cdf) ;
	            this.pagina.style.marginLeft="-"+this.pagina.offsetWidth/40+"rem";
            }

        },
        pagSwitch:function(index,length){
            var i= 0,
                    len = length-2;
            for(;i<len;i++){
                this.arr[i].className= "";
            }
            this.arr[(index == len+1 ? 0 : (index -1)) && (index == 0 ? len-1 : (index -1))].className= "active";
        },
        transform:function(i){
            this.style[slide.prefix + "Transform"] = "translate("+i+"%)";
        }
    };
    slide.prefix= function(){
        var _elementStyle = document.createElement('div').style;
        var vendors = ['msT','MozT', 'webkitT', 't'],
                transform,
                i = 0,
                l = vendors.length;
        for ( ; i < l; i++ ) {
            transform = vendors[i] + 'ransform';
            if ( transform in _elementStyle ) return vendors[i].substr(0, vendors[i].length-1);
        }
        return false;
    } ();
    window.slide = slide;
})(document, window);