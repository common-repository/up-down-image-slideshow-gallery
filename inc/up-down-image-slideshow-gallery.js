/**
 *     Up down image slideshow gallery
 *     Copyright (C) 2011 - 2019 www.gopiplus.com
 *     http://www.gopiplus.com/work/2011/04/25/wordpress-plugin-up-down-image-slideshow-script/
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
jQuery.noConflict()

function udisg_Show(options){
	var $=jQuery
	this.setting={udisg_Displaymode:{type:'auto', pause:2000, cycles:2, pauseonmouseover:true}, udisg_Orientation:'h', udisg_Persist:true, slideduration:500} //default settings
	jQuery.extend(this.setting, options) //merge default settings with options
	var curslide=(this.setting.udisg_Persist)? udisg_Show.routines.getCookie("slider-"+this.setting.udisg_Wrapperid) : 0
	this.curslide=(curslide==null || curslide>this.setting.udisg_ImageArray.length-1)? 0 : parseInt(curslide) //make sure curslide index is within bounds
	this.curstep=0
	this.zIndex=1
	this.animation_isrunning=false //variable to indicate whether an image is currently being slided in
	this.posprop=(this.setting.udisg_Orientation=="h")? "left" : "top"
	options=null
	var slideshow=this, setting=this.setting, preloadimages=[], slidesHTML=''
	for (var i=0; i<setting.udisg_ImageArray.length; i++){ //preload images
		preloadimages[i]=new Image()
		preloadimages[i].src=setting.udisg_ImageArray[i][0]
		slidesHTML+=udisg_Show.routines.getSlideHTML(setting.udisg_ImageArray[i], setting.udisg_WidthHeight[0]+'px', setting.udisg_WidthHeight[1]+'px', this.posprop)+'\n'
	}
	jQuery(function($){ //on document.ready
		slideshow.init($, slidesHTML)
	})
	$(window).bind('unload', function(){ //on window onload
		if (slideshow.setting.udisg_Persist) //remember last shown slide's index?
			udisg_Show.routines.setCookie("slider-"+setting.udisg_Wrapperid, slideshow.curslide)
	})
}

udisg_Show.prototype={

	slide:function(nextslide, dir){ //possible values for dir: "left", "right", "top", or "down"
		if (this.curslide==nextslide)
			return
		var slider=this
		var nextslide_initialpos=this.setting.udisg_WidthHeight[(dir=="right"||dir=="left")? 0 : 1] * ((dir=="right"||dir=="down")? -1 : 1)
		var curslide_finalpos=-nextslide_initialpos
		var posprop=this.posprop
		if (this.animation_isrunning!=null)
			this.animation_isrunning=true //indicate animation is running
		this.$imageslides.eq(dir=="left"||dir=="top"? nextslide : this.curslide).css("zIndex", ++this.zIndex) //increase zIndex of upcoming slide so it overlaps outgoing
		this.$imageslides.eq(nextslide).css(udisg_Show.routines.createobj(['visibility', 'visible'], [posprop, nextslide_initialpos])) //show upcoming slide
			.animate(udisg_Show.routines.createobj([posprop, 0]), this.setting.slideduration, function(){slider.animation_isrunning=false})
		this.$imageslides.eq(this.curslide).animate(udisg_Show.routines.createobj([posprop, curslide_finalpos]), this.setting.slideduration, function(){jQuery(this).css("visibility", "hidden")}) //hide outgoing slide
		this.curslide=nextslide
	},

	navigate:function(keyword){ //keyword: "back" or "forth"
		clearTimeout(this.rotatetimer)
		var dir=(keyword=="back")? (this.setting.udisg_Orientation=="h"? "right" : "down") : (this.setting.udisg_Orientation=="h"? "left" : "up")
		var targetslide=(keyword=="back")? this.curslide-1 : this.curslide+1
		targetslide=(targetslide<0)? this.$imageslides.length-1 : (targetslide>this.$imageslides.length-1)? 0 : targetslide //wrap around
		if (this.animation_isrunning==false)
			this.slide(targetslide, dir)
	},

	rotate:function(){
		var slideshow=this
		if (this.ismouseover){ //pause slideshow onmouseover
			this.rotatetimer=setTimeout(function(){slideshow.rotate()}, this.setting.udisg_Displaymode.pause)
			return
		}
		var nextslide=(this.curslide<this.$imageslides.length-1)? this.curslide+1 : 0
		this.slide(nextslide, this.posprop) //go to next slide, either to the left or upwards depending on setting.udisg_Orientation setting
		if (this.setting.udisg_Displaymode.cycles==0 || this.curstep<this.maxsteps-1){
			this.rotatetimer=setTimeout(function(){slideshow.rotate()}, this.setting.udisg_Displaymode.pause)
			this.curstep++
		}
	},

	init:function($, slidesHTML){
		var slideshow=this, setting=this.setting
		this.$wrapperdiv=$('#'+setting.udisg_Wrapperid).css({position:'relative', visibility:'visible', overflow:'hidden', width:setting.udisg_WidthHeight[0], height:setting.udisg_WidthHeight[1]}) //main DIV
		if (this.$wrapperdiv.length==0){ //if no wrapper DIV found
			alert("Error: DIV with ID \""+setting.udisg_Wrapperid+"\" not found on page.")
			return
		}
		this.$wrapperdiv.html(slidesHTML)
		this.$imageslides=this.$wrapperdiv.find('div.slide')
		this.$imageslides.eq(this.curslide).css(udisg_Show.routines.createobj([this.posprop, 0])) //set current slide's CSS position (either "left" or "top") to 0
		if (this.setting.udisg_Displaymode.type=="auto"){ //auto slide mode?
			this.setting.udisg_Displaymode.pause+=this.setting.slideduration
			this.maxsteps=this.setting.udisg_Displaymode.cycles * this.$imageslides.length
			if (this.setting.udisg_Displaymode.pauseonmouseover){
				this.$wrapperdiv.mouseenter(function(){slideshow.ismouseover=true})
				this.$wrapperdiv.mouseleave(function(){slideshow.ismouseover=false})
			}
			this.rotatetimer=setTimeout(function(){slideshow.rotate()}, this.setting.udisg_Displaymode.pause)
		}
	}

}

udisg_Show.routines={

	getSlideHTML:function(imgref, w, h, posprop){
		var posstr=posprop+":"+((posprop=="left")? w : h)
		var layerHTML=(imgref[1])? '<a href="'+imgref[1]+'" target="'+imgref[2]+'">' : '' //hyperlink slide?
		layerHTML+='<img src="'+imgref[0]+'" style="border-width:0;" />'
		layerHTML+=(imgref[1])? '</a>' : ''
		return '<div class="slide" style="position:absolute;'+posstr+';width:'+w+';height:'+h+';text-align:center;">'
			+'<div style="width:'+w+';height:'+h+';display:table-cell;vertical-align:middle;">'
			+layerHTML
			+'</div></div>' //return HTML for this layer
	},


	getCookie:function(Name){ 
		var re=new RegExp(Name+"=[^;]+", "i"); //construct RE to search for target name/value pair
		if (document.cookie.match(re)) //if cookie found
			return document.cookie.match(re)[0].split("=")[1] //return its value
		return null
	},

	setCookie:function(name, value){
		document.cookie = name+"=" + value + ";path=/"
	},

	createobj:function(){
		var obj={}
		for (var i=0; i<arguments.length; i++){
			obj[arguments[i][0]]=arguments[i][1]
		}
		return obj
	}
}