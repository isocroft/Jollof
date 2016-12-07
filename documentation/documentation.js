/*!
 
 * Jelloff Documentation

 */

;(function(w, d){

                    var body = d.body,

                    html = d.documentElement,

                    getScrollTop = function(){
                        return (w.pageYOffset || html.scrollTop);
                    },

                    getElements = function (selector, parent) {
                       var elements = [],
                             i = 0,
                             length,
                             obj,
                             style,
                             nodes, //to store the list of child nodes
                             node;  //single node

                       // set parent to document if not passed
                       parent = parent || d;

                       // if the browser does not support querySelectorAll we need to do it ourselves
                       if (parent.querySelectorAll) {
                              obj = parent.querySelectorAll(selector);
                              
                             // convert object/function to array of elements
                             if ((typeof obj === 'object' || typeof obj === 'function') && typeof obj.length === 'number') {
                                    for (i = 0; i < obj.length; i++) {
                                           elements.push(obj[i]);
                                    }
                             } else if (typeof obj === 'array') {
                             	    elements = obj;
                             }
                       } else if (d.createStyleSheet) {
                             if (d.styleSheets.length) { // IE requires you check against the length as it bugs out otherwise
                                    style = d.styleSheets[0];
                             } else {
                                    style = d.createStyleSheet();
                             }

                             // split selector on comma because IE7 doesn't support comma delimited selectors
                             var selectors = selector.split(/\s*,\s*/),
                                    indexes = [],
                                    index;
                             for (i = 0; i < selectors.length; i++) {
                                    // create custom (random) style rule and add it to elements
                                    index = style.rules.length;
                                    indexes.push(index);
                                    style.addRule(selectors[i], 'aybabtu:pa', index);
                             }

                             // get all child nodes (document object has bugs with childNodes)
                             if (parent === d) {
                                    nodes = parent.all;
                             } else {
                                    nodes = parent.childNodes;
                             }

                             // cycle through all elements until we find the ones with our custom style rule
                             for (i = 0, length = nodes.length; i < length; i++) {
                                    node = nodes[i];
                                    if (node.nodeType === 1 && node.currentStyle.aybabtu === 'pa') {
                                           elements.push(node);
                                    }
                             }

                             // remove the custom style rules we added (go backwards through loop)
                             for (i = indexes.length - 1; i >= 0; i--) {
                                    style.removeRule(indexes[i]);
                             }
                       }
                       return elements;
                },

                // cross browser add event listener
                addListener = function (element, event, callback) {
                	   var _temp;
                       if (element.addEventListener) {
                             element.addEventListener(event, callback, false);
                       } else if (element.attachEvent) {
                             return element.attachEvent('on' + event, callback);
                       } else{
                       	     _temp = element['on'+event];
                       	     if(typeof ____ == "function"){
                                  ____.$$formerCallback = _temp;
                             }     
                       	     return element['on'+event] = ____;
                       }

                       function ____(e){
                       	     	 if(typeof callback == "function"){
                       	     	 	 callback.call(this, e);
                       	     	 }	 
                       	     	 if(typeof ____.$$formerCallback == "function"){
                                      ____.$$formerCallback.call(this, e);
                       	     	 }
                       }
                },

                // cross browser remove event listener
                removeListener = function (event, element, callback) {
                	   var _temp, formerCallback;
                       if (element.removeEventListener) {
                             element.removeEventListener(event, callback, false);
                       } else if (element.detachEvent) {
                             return element.detachEvent('on' + event, callback);
                       } else{
                       	     _temp = element['on'+event];
                       	     if(_temp == "function"){
	                       	     if(_temp.name && _temp.name == "____" 
									|| (/\s*function\s*(.*)?/.exec(String(_temp)) || dumpArr)[1] == "____"){
	                                 formerCallback = ____.$$formerCallback;
	                       	     }
	                             element['on'+event] = null;
	                             element['on'+event] = formerCallback;
	                         }     
                       }
                },

                _each = function(object, iterator){
                      for(var prop in object){
                      	  if(({}).hasOwnProperty.call(object, prop)){
                      	  	   iterator.call(object, object[prop], prop);
                      	  }
                      }
                },

                DOM = {
                    oldOperaFix:function(factory){
                        factory(document.images['mast']);
                    },
                    projectBtnNav:function(factory){
                        var btn = d.getElementById("load-app");
                        addListener(btn, "click", factory('/'));
                    },
                	  setRightMenu:function(factory){
                           var elems = getElements("a.block-box", d.getElementById("aside"));
                           var active = "active block-box";
                           var attr = (d.all? "className" : "class");
                           this._activeMenu = elems[0];
                           _each(elems, function(menu){
                               addListener(menu, "click", factory(active, attr, menu.getAttribute("hash")));
                           });
                	  },
                	  setLeftMenu:function(factory){
                	  	  var menu = d.getElementById("menu");
                	  	  var btns = getElements("a.nav-btn", body);
                          _each(btns, function(btn){
                	  	     addListener(btn, "click", factory(menu));
                          });
                	  },
                	  setHeaderVisible:function(factory){
                          var header = d.getElementById("header");
                          addListener(body, "mousemove", factory(header));
                	  },
                	  setScrollWatcher:function(factory){
                	  	  var header = d.getElementById("header");
                          addListener(w, "scroll", factory(header));
                	  }
                };

                DOM.oldOperaFix(function(img){
                    if(w.opera){
                        if(!img) return;
                        
                        img.width = String(window.innerWidth);
                        img.height = String(500);
                    }   
                });

                DOM.projectBtnNav(function(rootPath){
                    if(w.location.protocol == "file:"){
                        alert("Your Jelloff App cannot be loaded from the file system");
                        return;
                    }

                    location.assign(location.protocol + '//' + location.host + (location.pathname.length > 1? location.pathname.replace('/documentation/index.html', '') : '') + location.port + rootPath);
                });

                DOM.setRightMenu(function(classActiveName, classAttrName, hashValue){
                      return function(e){
                            var klass = this.getAttribute(classAttrName);
                            if(DOM._activeMenu){
                               DOM._activeMenu.className = "block-box";
                            }
                            if(klass.indexOf(classActiveName) === -1){
                            	this.setAttribute(classAttrName, classActiveName);
                                DOM._activeMenu = this;
                                w.location.hash = hashValue;
                            }
                      }   
                });

                DOM.setLeftMenu(function(menu){
                	return function(e){
                		if(this.className.indexOf("close") > -1){
                             menu.style.cssText = '';
                		}else{
                             menu.style.cssText = 'display:block;';
                		}
                	}
                });

                DOM.setHeaderVisible(function(header){
                	 var standardScrollBarWidth = 17;
                	 var hasVerticalScrollBar = (html.clientWidth === html.scrollWidth);
                     var isFullScreen = (w.screen.width === (html.clientWidth + standardScrollBarWidth));
                     return function(e){
                     	var currentScrollTop = body.getAttribute("doc-scroll-offset");
                     	var currentPageWidthPixels = html.scrollWidth;
                        //if(hasVerticalScrollBar){
                           currentPageWidthPixels -= standardScrollBarWidth;
                     	   if(e.clientX <= (currentPageWidthPixels/2) && e.clientY <= 35){
                              // if(parseInt(currentScrollTop) === 0){
                                   header.style.cssText = 'visibility:visible;';
                              // }
                     	   }else{
                     	   	   // header.style.cssText = '';
                     	   }
                        //}
                     }
                });

                DOM.setScrollWatcher(function(header){
                     var lastScrollTop = getScrollTop();
                     return function(e){
                     	console.log(e.target.nodeName);
                     	currentScrollTop = getScrollTop(e);
                     	var scrollCheck = (lastScrollTop != currentScrollTop)? currentScrollTop : lastScrollTop;
                         if(scrollCheck > lastScrollTop){
                         	// downscroll code
                         	header.style.cssText = 'visibility:hidden';
                         }else{
                         	// upscroll code
                         	// header.style.cssText = '';
                         }
                         lastScrollTop = scrollCheck;
                         body.setAttribute("doc-scroll-offset", String(lastScrollTop));
                     }
                });
                

}(this, this.document));