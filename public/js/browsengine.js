/*!
 * @desc: [Engine Detection Script for Browsers on Any Device]
 * @file: [browsengine.js]
 * @version: 0.0.3
 * @author: https://twitter.com/isocroft (@isocroft)
 * @created: 13/11/2014
 * @updated: 11/03/2017
 * @license: MIT
 * @remarks: with love for the OpenSource Community...
 */
 
 
!function(hasInterface, undefined){

 
function to_string(obj){
  return ({}).toString.call(obj).toLowerCase();
} 

function is_own_prop(obj, prop){
  return ({}).hasOwnProperty.call(obj, prop);
} 

function actual_non_emulated_IE_major_version() {
    // Detects the actual version of IE in use, even if it's in an older-IE emulation mode.
    // IE JavaScript conditional compilation docs: https://msdn.microsoft.com/library/121hztk3%28v=vs.94%29.aspx
	
    // @cc_on docs: https://msdn.microsoft.com/library/8ka90k2e%28v=vs.94%29.aspx

    var jscriptVersion = new Function('/*@cc_on return @_jscript_version; @*/')(); // jshint ignore:line
    if (jscriptVersion === undefined) {
       return 11; // IE11+ not in emulation mode
    }
    
    return jscriptVersion; // IE7, IE8, IE9 or IE10 in any mode, or IE11 in non-IE11 mode
}

/*!
 * contentloaded.js
 *
 * Author: Diego Perini (diego.perini at gmail.com)
 * Summary: cross-browser wrapper for DOMContentLoaded
 * Updated: 20101020
 * License: MIT
 * Version: 1.2
 *
 * URL:
 * http://javascript.nwbox.com/ContentLoaded/
 * http://javascript.nwbox.com/ContentLoaded/MIT-LICENSE
 *
 */

// @win window reference
// @fn function reference
function contentLoaded (win, fn, obj) {
	var done = false, top = true,

	doc = win.document, root = doc.documentElement,

	add = doc.addEventListener ? 'addEventListener' : 'attachEvent',
	rem = doc.addEventListener ? 'removeEventListener' : 'detachEvent',
	pre = doc.addEventListener ? '' : 'on',

	init = function(e) {
		if (e.type == 'readystatechange' && doc.readyState != 'complete') return;
		(e.type == 'load' ? win : doc)[rem](pre + e.type, init, false);
		if (!done && (done = true)) fn.call(win, (e.type || e), obj);
	},

	poll = function() {
		try { root.doScroll('left'); } catch(e) { setTimeout(poll, 50); return; }
		init('poll');
	};

	if (doc.readyState == 'complete') fn.call(win, 'lazy', obj);
	else {
		if (doc.createEventObject && root.doScroll) {
			try { top = !win.frameElement; } catch(e) { }
			if (top) poll();
		}
		doc[add](pre + 'DOMContentLoaded', init, false);
		doc[add](pre + 'readystatechange', init, false);
		win[add](pre + 'load', init, false);
	}

}

contentLoaded.apply(null, [window, function(){ 
	
	var w=this, d=w.document, rt = d.documentElement,  dd="documentMode", n= w.navigator, ua = n.userAgent, apn = n.appName, /* global objects... */
	
	body, Device, isGecko = false, isEdgeHTML = false, isBlink = false, isTrident = false, isSilk = false, isYandex = false, isPresto = false, Screen, pixelDensity, vMode, browserName, browserVersion, isChrWebkit=false, isSafWebkit=false, isKDE = false, nk = ua.toLowerCase(),
	
	_engineFragment = ((w.chrome || d.readyState) && 'clientInformation' in w && 'all' in d), z = (('orientation' in w) && !('ondeviceorientation' in w)),
	
	j = /(?:chrome[^ ]+:)? (edge)\/(\d+(\.\d+)?)/.exec(nk) || /(webkit)[ \/]([\w.]+)/.exec(nk) || /(opera|opr)(?:.*version)?[ \/]([\w.]+)/.exec(nk) || /(?:(msie) |rv)([\w.]+)/.exec(nk) || !/compatible/.test(nk) && !/seamonkey/.test(nk) && /(mozilla)(?:.*? rv:([\w.]+))?/.exec(nk) || [],
	
	OS = { //detecting OS data...
	  	isLinux:function(){ 
	
	      		return (n.platform.indexOf("Linux") == 0); 
		}, 
		isMac:function(){ 
		
		      return (n.platform.indexOf("Mac") == 0);

		}, 
		isSun:function(){ 
		
		      return (n.platform.indexOf("Sun") == 0)

		}, 
		isWinPC:function(){ 
		
		   return (n.platform.indexOf("Win32") == 0) 
		
		}, 
		isWinMobile:function(bd){
		
		     return (n.platform.indexOf("Win") == 0) && ((ua.indexOf("Windows Phone") > 0) || (ua.indexOf("IEMobile") > 0) || (ua.indexOf("WPDesktop") > 0));
			 
		},
		isOperaMobile:function(bd){
		
		    return  to_string(window.operamini) == '[object operamini]' && ((ua.indexOf("Opera Mini") > 0) || (bd && ('OMiniFold' in bd.style)));
			
		},
		isIOS:function(bd){ 
		
		    return (ua.indexOf("iPhone; U;") > 0) || (ua.indexOf("iPad; U;") > 0) || (ua.indexOf("iPod; U;") > 0) || (ua.search(/iPhone OS 3_(1|2)_2/) > 0);

		},
	    isAndriod:function(bd){
		
		  return (this.isLinux()) && (ua.search(/\; Andriod(?:[\d]+\.[\d]+)/) > 0 && ua.search(/like/ig) == -1);
		  
		},
	    isBB:function(bd){
		
		   return (ua.search(/BlackBerry|\bBB\d+/) > -1);
		   
		},
		isWebOS:function(){
		   
		   return (ua.search(/(Web|HPW)OS/) > -1);
		   
		}	
	},

	 body = d.body || d.getElementsByTagName('body')[0],

	 /* Gecko has so many browsers using it... like, e plenty wellu wellu !! so we have to be kia-ful when detectig... */	
	 
     isGecko = (!n.vendor && (w.crypto && typeof(w.mozInnerScreenX) == 'number') && (!d.getBoxObjectFor || n.battery || w.mozContact) && /Gecko/g.test(ua)), 

     /* Presto is the only rendering engine used by older Opeara browsers,  so we include the presence of {opera} object as a factor */

     isPresto = (/Presto/g.test(ua) && ('defaultStatus' in w) && (('OLink' in body.style) || (to_string(w.opera) == "[object opera]") || OS.isOperaMobile(body)) && w.history.navigationMode),

     /* Trident is the rendering engine used by older versions of IE */

     isTrident = ((/*@cc_on!@*/false || d.selection || d.createEventObject) && /Trident/g.test(ua) && (w.execScript || _engineFragment) && ('behavior' in body.style)),

    /* EdgeHTML rendering engine is a 'well-standardized' fork of the Trident rendering engine */

     isEdgeHTML = ('crypto' in w) && _engineFragment && /Edge/g.test(ua) && !is_own_prop(d, 'security') && (w.chrome.runtime === undefined) && !isTrident,

    /* Blink rendering engine is the new successor to Webkit for Chromium and Chrome browsers */

     isBlink = _engineFragment && ('crypto' in w) && ((!!w.Intl) && !!(w.Intl.v8BreakIterator)) && ('CSS' in w);/* && ('webkitStorageInfo' in w); */	
	
	/* setup info object - {webpage} */

	w.webpage = {engine:{},old:{},device:{screen:{},os:null}};

	var winHeight = w.innerHeight || rt.clientHeight || body.clientHeight,
	
	winWidth = w.innerWidth || rt.clientWidth || body.clientWidth,
	
	viewfactor = ((winWidth/winHeight).toPrecision(2)),
	
	screenfactor = ((w.screen.width/w.screen.height).toPrecision(4)),
	
    dpi = w.screen.colorDepth || w.screen.pixelDepth; // mostly '32' ... sometimes '24'

    w.webpage.device.screen.dpi = dpi;

    /* 
		if the engine in use is Gecko (firefox, flock, webspirit, e.t.c) 
		or the engine in use is Trident (IE), [pixel-density] is calculated in
		the most approximate manner.
	*/
	 
	if(isTrident || isGecko){
	    
		 pixelDensity = parseInt(w.screen.availWidth / winWidth);
		
	}else{
	  
	     pixelDensity = w.devicePixelRatio;

	}

	Device = {
		isTouchCapable:function(){

			return (('ontouchstart' in w) && !is_own_prop(w, 'ontouchstart')) || ('onmsgesturechange' in w && !is_own_prop(w, 'onmsgesturechange'));
		},
		onDesktop:function(){

			return ((~~pixelDensity) == 1) && !(this.isTouchCapable()) && !(this.onTablet());
		},
		onTablet:function(){

			if(!this.isTouchCapable()) return false;
		
		    return ((ua.match(/RIM/i)) || (ua.match(/ipad;/i)) || (ua.match(/nexus (7|10)/i)) || (ua.match(/KFAPWI/i)) || (ua.match(/tablet/i))) && !this.onMobile();
			
		},
	    onMobile:function(){

	    	if(!this.isTouchCapable()) return false;

	    	/* https://developer.mozilla.org/en-US/docs/Web/HTTP/Browser_detection_using_the_user_agent  */
		
		    return (ua.match(/[^-]mobi|mobile/i) && (w.screen.width < 768) && (w.screen.width / pixelDensity) < 768);
		
		}
	};
	
	
	
	Screen = {
	     isRetina: function(){
	
	          return (pixelDensity >= 2);
	   
	     }
	};
	
    switch(screenfactor){
    	  case "1.706": // screendim - 1024x600 {falsely-reported as 819x480} - Landscape
    	  case "1.708": // scrrendim - 1024x600 {falsely-reported as 820x480} - Landscape
          case "1.707": // screendim - 1024x600 - Landscape e.g iPad, Dell Mini, HP Mini
		  case "0.5859": // screendim - 600x1024 - Portrait
		     
			 if((OS.isWinPC(body) || OS.isMac(body) || OS.isLinux(body)) && w.screen.width >= 600){
			    body.className += (screenfactor.search(/^(?:1\.70(?:[6-8]))$/)==0)? " 1024x600" : " 600x1024";
			    
			    if(Device.onTablet())
				   body.setAttribute("aria-view-mode", "tablet");
				
				if(Device.onDesktop())
                   body.setAttribute("aria-view-mode", "desktop");

			 }else if((OS.isWinMobile(body) || OS.isBB(body) || OS.isAndriod(body) || OS.isIOS(body) || OS.isOperaMobile(body))){
			    if(z)
				    body.className += (Math.abs(w.orientation || 0) == 90)? " 1024x600" : " 600x1024";
				else
				    body.className += (screenfactor=="0.5859")? " 600x1024" : " 1024x600";
					
			     if(Device.onTablet())
                     body.setAttribute("aria-view-mode","tablet");
				 
				 if(Device.onMobile())
                     body.setAttribute("aria-view-mode","mobile");				 
			 }	  
		  break;
		  
		  case "0.5634": // screendim - 360X640 - Portriat e.g Samsung Galaxy S3, 
		  case "0.5625": // screendim - 320x568 - Portrait e.g iPhone
		  	if(OS.isWinMobile(body) || OS.isBB(body) || OS.isAndriod(body) || OS.isIOS(body) || OS.isOperaMobile(body)){
			    	 
				 if(Device.onMobile())
	                     body.setAttribute("aria-view-mode","mobile");	
            }     
		  break;
		  
		  case "0.5993": // screendim - 320x534 - Portrait e.g Techno, Samsung Galaxy S4
		  case "1.669": // screendim - 534x320 - Landscape
		     if((OS.isWinMobile(body) || OS.isBB(body) || OS.isAndriod(body) || OS.isIOS(body) || OS.isOperaMobile(body))){
			     if(z)
				    body.className += (Math.abs(w.orientation || 0) == 90)? " 534x320" : " 320x534";
				else
				    body.className += (screenfactor=="1.669")? " 534x320" : " 320x534";
				 
				 if(Device.onMobile())
                     body.setAttribute("aria-view-mode","mobile");				 
			 }
		  break;
		  
		  case "1.500": // screendim 480x320 - Landscape
		  case "0.6667": // screendim 320x480 - Portrait
		     if((OS.isWinMobile(body) || OS.isBB(body) || OS.isAndriod(body) || OS.isIOS(body) || OS.isOperaMobile(body))){
			     if(z)
				    body.className += (Math.abs(w.orientation || 0) == 90)? " 480x320" : " 320x480";
				 else
				    body.className += (screenfactor=="0.6667")? " 320x480" : " 480x320";
				 
				 if(Device.onMobile())
                     body.setAttribute("aria-view-mode","mobile");				 
			 }
		  break;
		  
          case "1.333": // screendim - 1600x1200, screendim - 1152x864, screendim - 1024x768, screendim - 800x600, screendim - 480x360, screendim - 640x480 - Landscape
		  case "0.7500": // screendim - 1200x1600, screendim - 864x1152, screendim - 768x1024, screendim - 600x800, screendim - 360x480, screendim - 480x640 - Portrait e.g Nokia-XL
		        if((OS.isWinPC(body) || OS.isMac(body) || OS.isLinux(body)) && (w.screen.width >= 768)){
				   body.className += (screenfactor=="1.333")? (w.screen.width <= 1024? " 1024x768" : " 1152x864") : (w.screen.width <= 768?" 768x1024" : " 864x1152");
				   
				   if(Device.onTablet())	
				      body.setAttribute("aria-view-mode", "tablet");
				   
				   if(Device.onDesktop())
                      body.setAttribute("aria-view-mode", "desktop");

				}else if((OS.isWinMobile(body) || OS.isBB(body) || OS.isAndriod(body) || OS.isIOS(body) || OS.isOperaMobile(body))){
				      
				    if(Device.onTablet() && (w.screen.width >= 800)){
					    if(z)
				           body.className += (Math.abs(w.orientation) == 90)? " 800x600" : " 600x800";
						else
						   body.className += (screenfactor=="1.333")? " 800x600" : " 600x800";
						   
                        body.setAttribute("aria-view-mode","tablet");
				    }else if(Device.onMobile() && w.screen.width >= 360){
					    if(z)
				           body.className += (Math.abs(w.orientation || 0) == 90)?  " 480x360 640x480" : " 360x480 480x640";
						else
                           body.className += (screenfactor=="1.333")? " 480x360 640x480" : " 360x480 480x640";
                        
                        body.setAttribute("aria-view-mode", "mobile");
					}	
                }				   
          break;

          case "1.250": // screendim - 1280x1024 Portrait
		      if((OS.isWinPC(body) || OS.isMac(body) || OS.isLinux(body)) && w.screen.width >= 768){
                 body.className += (screenfactor=="1.250")? " 1280x1024" : " 1024x1280";
			     
			     if(Device.onTablet())
                     body.setAttribute("aria-view-mode","tablet");
				 
				 if(Device.onDesktop())
                     body.setAttribute("aria-view-mode","desktop");

			  }else if((OS.isWinMobile(body) || OS.isBB(body) || OS.isAndriod(body) || OS.isOperaMobile(body))){
			     if(Device.onTablet())
                     body.setAttribute("aria-view-mode","tablet");
				 
				 if(Device.onMobile())
                     body.setAttribute("aria-view-mode","mobile");
			  }	 
          break;

          case "1.779": // screendim - 1366x768 Portrait
		    if((OS.isWinPC(body) || OS.isMac(body) || OS.isLinux(body)) && w.screen.width >= 768){
               body.className += " 1366x768";

               if(Device.onTablet())
               		body.setAttribute("aria-view-mode","tablet");
               
               if(Device.onDesktop())
			   		body.setAttribute("aria-view-mode","desktop");
			}   
          break;
		  
		  case "1.778": // screendim - 1920×1080
		     if((OS.isWinPC(body) || OS.isMac(body) || OS.isLinux(body)) && w.screen.width >= 768){
		       body.className += " 1920×1080";
		       
		       if(Device.onDesktop())
			   		body.setAttribute("aria-view-mode","desktop tv");
			}   
		  break;
		  
		  case "1.600": // Resolution - 1680×1050, Resolution  - 1440x900, Resolution - 1280×800
		     if((OS.isWinPC(body) || OS.isMac(body) || OS.isLinux(body)) && w.screen.width >= 768){
		        if(w.screen.width > 1440 && w.screen.width <= 1680) body.className += " 1680×1050"; 
		        if(w.screen.width > 1280 && w.screen.width <= 1440) body.className += " 1440×900";
			    if(w.screen.width <= 1280) body.className += " 1280×800";
				
				if(Device.onTablet())
					body.setAttribute("aria-view-mode", "tablet");

				if(Device.onDesktop())
			    	body.setAttribute("aria-view-mode","desktop"); 
			 }	
		  break;
		  
		  case "1.805": // Resolution - 1386×768
		      if((OS.isWinPC() || OS.isMac() || OS.isLinux()) && w.screen.width >= 768){
		         body.className += " 1386×768";

		         if(Device.onDesktop())
			     	body.setAttribute("aria-view-mode","desktop");
			  }	 
		  break;
		  
		  default:
		     body.setAttribute("aria-view-mode","unknown");
		  break;
    };
	
	body.setAttribute("aria-last-modified", document.lastModified);

    if(Screen.isRetina()){

    	body.setAttribute("aria-screen-view", "retina");

    	w.webpage.device.screen.type = 'retina';

    }else{

    	body.setAttribute("aria-screen-view", "normal");

    	w.webpage.device.screen.type = 'normal';
    } 	
	
	    if(!d[dd] && !isPresto){  // a necessary step to avoid conflict with IE/Opera and others...

	          isChrWebkit = ((d.webkitHidden == false || d.webkitHidden === undefined) && /(Chrome|Crios|Crmo|CrOS)/g.test(ua) && ((n.vendor.indexOf("Google Inc") != -1) || !w.showModalDialog));
		
		      isSafWebkit = ((navigator.vendor.indexOf("Apple Computer") != -1) && (/Gecko/g.test(ua) || !n.taintEnabled) && 'webkitDashboardRegion' in body.style);
		
		      isKDE = ((navigator.vendor.indexOf("KDE")) != -1 && /Konqueror/g.test(ua) && ('KhtmlUserInput' in body.style));
			 
			  isSilk = ((navigator.vendor.indexOf("Amazon")) != -1 && /Silk/g.test(ua));
			 
			  isYandex = (/YAbrowser/ig.test(ua));
		
		}
   
   		/* retrieve browser build name (if any) */

        browserName = j[1] || "unknown";
	
		/* take out minor version number and/or patch version number (if any) */

	    browserVersion = parseInt( j[2] || "0" ).toPrecision(2);  
		
		/* Finally, clean it up */

		browserVersion = parseFloat(browserVersion);
	
	
	    if(browserVersion  <= 7.0 && browserName == "msie" && isTrident){
	     
	     	/* 
	     		There are several browsers using the [Trident] engine,
	     		however, IE is the most popular
	     	 */

     		 body.className += " oldIE";

     		 w.webpage.old.ie = true;
	    }
		
		if(browserVersion <= 9.0 && browserName == "opera" && isPresto){

			/* There is only one browser using the [Presto] engine */
		 
		    body.className += " oldOpera";

		    w.webpage.old.opera = true;
		}
			
		if(browserVersion <= 3.5 && browserName == "mozilla" && isGecko){

			/* 
				There are sevral browsers using the Mozillas' Gecko engine 
				So, we have to sort things out in a sharp-sharp manner ;) 
			*/

			if(ua.match(/firefox/i)){

		    	body.className += " oldMoz";

		    	w.webpage.old.firefox = true;
		    }	

		    if(ua.match(/flock/i)){

		    	/* From version 4, Flock uses the Webkit engine */

		    	body.className += " oldFlock";

		    	w.webpage.old.flock = true;
		    }
		}	
			
		if(browserVersion <= 2.0 && browserName == "webkit" && !isBlink){
		     		   
		    body.className += (isChrWebkit)? " oldChrome" : (isSafWebkit? " oldSafari": "");

		    if(isSafWebkit){
		    	
		    	w.webpage.old.safari = true;

		    }else if(isChrWebkit){

		    	w.webpage.old.chrome = true;
		    }
		}	
	
        if(OS.isWinPC()){
	
	           body.setAttribute('aria-os-data','Windows');

	           w.webpage.device.os = 'windows';
	
	    }else if(OS.isMac(body)){
	
	           body.setAttribute('aria-os-data','Macintosh');

	           w.webpage.device.os = 'macintosh';
	
	    }else if(OS.isSun(body)){
		
		       body.setAttribute('aria-os-data','Sun');

		       w.webpage.device.os = 'sun';
		
		}else if(OS.isLinux(body)){
	
	           body.setAttribute('aria-os-data','Linux');

	           w.webpage.device.os = 'linux'
	
	    }else if(OS.isIOS(body)){
	
	           body.setAttribute('aria-os-data', 'iOS');

	           w.webpage.device.os = 'ios';
	
	    }else if(OS.isAndriod(body)){
		
			   body.setAttribute('aria-os-data', 'Linux Andriod');

			   w.webpage.device.os = 'linux andriod';

		}else{
		       body.setAttribute('aria-os-data','unknown');

		       w.webpage.device.os = 'unknown';
		}

		if(d.compatMode == "BackCompat"){
             body.setAttribute('aria-setup-mode','quirks');  // not good for IE especially, Quirk Mode Ouch?!
			 body.className += " quirks";
			 w.webpage.quirks = true;
		}else{
			body.setAttribute('aria-setup-mode','standards');
			body.className += " standards"; 
			w.webpage.standards = true;
		}
	
	try{
		vMode = body.getAttribute('aria-view-mode');

		w.webpage.device.type = vMode;

	}catch(e){}


    if (isTrident) { 
		
        var ieVersion = (function(reg){
		       
		       /*
		        O-o shine ya eye... this version number might be due to emulation !!!
		        */
		         
	           return parseInt(ua.match(reg)[2]);

	    })(/MSIE\s(([0-9]+)[\.0-9]*)/), 
		
		// Here we need to verify that this IE version is not emulated
		
		ieActual = actual_non_emulated_IE_major_version(),
		
		ieTag = (ieVersion===ieActual ? " IE"+ieVersion : ieActual);
		
		// check if the actual IE version is a valid number
		if(typeof ieTag != "number"){ 
		    ieTag = " UA-unknown";
			return;
		}else{
		    ieActual = parseInt(ieTag);
		    ieTag = " IE"+ieActual;
		}
		
	    /* Here we are just inserting a marker for IE10+ */

        body.className += (ieActual >= 10)? " yes-ms" : " yes-old-ms"; // @TODO: need to fix possible bug here... maybe this line should go altogether (still thinking)


      	/* Here we are detecting Internet Explorer 7 - 10+... */
         
        if(d[dd] && d[dd] === ieActual  && ("msInterpolationMode" in body.style || 'msLinearGradient' in body.style)){ // IE(7-10)+
		     
		     if(body.className.indexOf(ieTag.substring(1)) == -1){    

                     body.className += ieTag + " forward-ie"; 
			 }	

		}else{
		
		     /* Here we are detecting Internet Explorer 4 - 6 only... */

             body.className += ieTag;      		 
		}

		body.className += " trident";
	        
	    w.webpage.engine.trident = true; 

	}
	
	/* detecting Microsoft Edge */

	else if(isEdgeHTML){	

		if(browserName == "edge" && ('msTextSizeAdjust' in body.style)){

		    body.className += " microsoftedge like-gecko like-khtml";

		    w.webpage.engine.edgehtml = true;

		} 
	}

	
    else if (isGecko) {
      
	    // Here we are detecting Firefox/IceWeasel from version 3.0+
			
	    if(nk.search(/firefox|iceweasel/) > -1){

           if (body.className.indexOf("yes-moz") == -1){
                  
                  body.className+=" yes-moz firefox gecko";

           }
				  
		   if(browserName == "rv"){

			       if(OS.onTablet() || OS.onMobile()){

				       	body.className+=" firefoxos";

				   }
		   }	  
		}

		else if(nk.search(/seamonkey/) > -1){
				
				body.className+=" yes-moz seamonkey gecko";
		}

		else if(nk.search(/flock/) > -1){
				
				body.className+=" yes-moz flock gecko";
		}

        /*
        	 @TODO: There are other [Gecko] browsers that exist - 
        
        	Epiphany (formerly used [Webkit, Presto]), Waterfox, 
        	K-Meleon, Classilla, Lunascape(dual-engine Gecko + Trident), UC Browser, 
        	SeaMonkey, Flock (1.0 - 3.0), WebSpirit, Pale Moon e.t.c.	
        */		

    	w.webpage.engine.gecko = true;

    }
     
    else if(isKDE){
         
		 /* Here we are detecting Konqueror 1.0+ */
		 
         if (body.className.indexOf("yes-khtml") > -1){

                body.className+=" yes-khtml konqueror like-gecko";
         }

         w.webpage.engine.khtml = true;

    }

      /* Here we are detecting Chrome from version 2.0+ */

     else if ((typeof(window["URL"] || window["webkitURL"]) == 'function') || isChrWebkit) {


          if(isBlink){

          		 if (body.className.indexOf("yes-blink") == -1){

                       body.className += " yes-blink chrome";

          		 }

          		 body.className += " blink like-gecko like-khtml";

				 w.webpage.engine.blink = true; 

          }else{

          		if (body.className.indexOf("yes-webkit") == -1){

                       body.className += " yes-webkit chrome";

          		}

          		body.className += " webkit like-gecko like-khtml";

				w.webpage.engine.webkit = true; 
          }
               

    }
	
	/* Here we are detecting Safari from version 2.0+ */
	
	else if(((typeof(window.webkitURL || window.URL) == 'undefined' && ua.indexOf('AppleWebkit') != -1)) || isSafWebkit){
	  
	          	  if (body.className.indexOf("yes-webkit") == -1){

	                      body.className += " yes-webkit safari like-gecko like-khtml";

	              }
	
			  	  w.webpage.engine.webkit = true;	
	          
	}
	
	/* Here we are detecting Opera from version 7.0 - 14.0 */

	/* 
		Opera versions 4.0 - 6.0 were supported by the Elektra engine
		[which is now seriously outdated.. like e don teey!!] 
	 */

    else  if (('OBackgroundSize' in body["style"] && 'attachEvent' in d) || (isPresto && browserName == 'opera')){

          if (body.className.indexOf("yes-opera") == -1){

                    body.className +=" yes-opera opera presto";

           }

           w.webpage.engine.presto = true;

    }

    /* Here we are detecting Opera from version 15.0+ */

    else if(/Blink/g.test(ua) || isBlink && !isPresto && browserName == 'opr'){

    	 if (body.className.indexOf("yes-blink") == -1){

                    body.className += " yes-blink opera blink like-gecko like-khtml";

          }

          w.webpage.engine.blink = true;      

    }	

 }, {}]);
 
 }(('HTMLDocument' in this) || ('Document' in this));