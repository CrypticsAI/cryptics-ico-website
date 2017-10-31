jQuery(document).ready(function(){
	
	"use strict";
	
$(window).scroll(function(event) {
	$(".services").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInUp"); 
		} 
	});	
	$(".tab_content").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInUp"); 
		} 
	});		
	$(".prdct-mock img").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInRight"); 
		} 
	});		
	$(".price-table-area").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInLeft"); 
		} 
	});		
	$(".prdct-mockup").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInLeftBig"); 
		} 
	});	
	$(".prdct-mockup-info").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInRightBig"); 
		} 
	});	
	$(".item-shot").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInUp"); 
		} 
	});
	$(".get-in-touch").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInUp"); 
		} 
	});
	$(".location").each(function(i, el) {
		var el = $(el);
		if (el.visible(true)) {
		  el.addClass("animated fadeInBottom"); 
		} 
	});

});	
	
	/*** Why Dont  ***/
	$(".why-dont-sec > .pointer > span").on('click', function(){
	$(".why-dont").fadeToggle();
	});	
	
	/*** Form Show  ***/
	$(".show-form-btn").on('click', function(){
	$(".trial-form > form").slideToggle();
	$(".show-form-btn").toggleClass('hide-form');
	});
	
	/*** Menu Show  ***/
	$(".open-nav").on('click', function(){
		$(this).parent().toggleClass("mobile-menu-opened")
	//$(".menu > ul").fadeToggle();
	});
	
	/*** FIXED Menu APPEARS ON SCROLL DOWN ***/	
	$(window).scroll(function() {    
	var scroll = $(window).scrollTop();
	if (scroll >= 50) {
	$("header").addClass("sticky");
	}
	else{
	$("header").removeClass("sticky");
	$("header").addClass("");
	}
	});
	
	/*** SMOOTH SCROLLING ***/	
	$(function() {
	$('a[href*=#]:not([href=#])').on('click', function() {
	if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
	var target = $(this.hash);
	target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
	if (target.length) {
	$('html,body').animate({
	scrollTop: target.offset().top
	}, 1000);
	}
	}
	});
	});    

	var lastId,
	topMenu = $("nav"),
	topMenuHeight = topMenu.outerHeight()+15,
	// All list items
	menuItems = topMenu.find("a"),
	scrollItems = menuItems.map(function(){
	var item = $(this).attr("href")[0] == "#" ? $($(this).attr("href")) : "";
	if (item.length) { return item; }
	});
	$(window).scroll(function(){
	// Get container scroll position
	var fromTop = $(this).scrollTop()+topMenuHeight;

	// Get id of current scroll item
	var cur = scrollItems.map(function(){
	if ($(this).offset().top < fromTop)
	return this;
	});
	// Get the id of the current element
	cur = cur[cur.length-1];
	var id = cur && cur.length ? cur[0].id : "";

	if (lastId !== id) {
	lastId = id;
	// Set/remove active class
	menuItems
	.parent().removeClass("active")
	.end().filter("[href=#"+id+"]").parent().addClass("active");
	}                   
	});	

$(document).on("click", ".rmap__col", function() {
	$(".rmap__col").removeClass("rmap__col--active");
	$(this).addClass("rmap__col--active");
})

$(document).on("click", ".mar_box_acc__item", function() {
	$(".mar_box_acc__item").removeClass("mar_box_acc__item--active");
	$(this).addClass("mar_box_acc__item--active");

	var closing = $(".mar_box_acc__item").not($(this)).find(".mar_box_acc__body");
	closing.animate({height: 0}, 1000);

	var el = $(this).find(".mar_box_acc__body"),
	curHeight = el.height(),
	autoHeight = el.css('height', 'auto').height();
	el.height(curHeight).animate({height: autoHeight}, 1000);
})

appear({
  init: function init(){

  },
  elements: function elements(){
    return document.getElementsByClassName('watch-scroll');
  },
  appear: function appear(el){
    $(el).addClass("active")
  },
  disappear: function disappear(el){
    $(el).removeClass("active")
  },
  bounds: -50,
  reappear: true,
	deltaTimeout: 100
});
});