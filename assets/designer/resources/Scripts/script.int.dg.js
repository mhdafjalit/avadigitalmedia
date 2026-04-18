!window.jQuery && document.write(unescape('%3Cscript src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.10.2.min.js"%3E%3C/script%3E'));
/*$('meta[name="viewport"]').prop('content', 'width=1280'); */

var Path;if(window.location.origin=='http://127.0.0.1:8888'){Path = "http://127.0.0.1:8888/AUVA-DIGITAL-MEDIA/"}else if(window.location.origin=='https://design.weblink4you.com'){Path = "https://design.weblink4you.com/Auva-Digital-Media/htmLpageS/"}else{Path=gObj['resource_url'];}

!function(e,t,r){function n(){for(;d[0]&&"loaded"==d[0][f];)c=d.shift(),c[o]=!i.parentNode.insertBefore(c,i)}for(var s,a,c,d=[],i=e.scripts[0],o="onreadystatechange",f="readyState";s=r.shift();)a=e.createElement(t),"async"in i?(a.async=!1,e.body.appendChild(a)):i[f]?(d.push(a),a[o]=n):e.write("<"+t+' src="'+s+'" defer></'+t+">"),a.src=s}(document,"script",[Path+'Scripts/helpers.min.js'])

if(Page=='warehouse'){

}

if(Page=='home'){
!function(e,t,r){function n(){for(;d[0]&&"loaded"==d[0][f];)c=d.shift(),c[o]=!i.parentNode.insertBefore(c,i)}for(var s,a,c,d=[],i=e.scripts[0],o="onreadystatechange",f="readyState";s=r.shift();)a=e.createElement(t),"async"in i?(a.async=!1,e.body.appendChild(a)):i[f]?(d.push(a),a[o]=n):e.write("<"+t+' src="'+s+'" defer></'+t+">"),a.src=s}(document,"script",[''])
}

else{
}

if(Page=='details'){}

$(window).load(function(e) {		
$('.pop1').fancybox({iframe:{css:{width:'400'}}}); 
$('.pop2').fancybox({iframe:{css:{width:'900'}}}); 
$('.pop3').fancybox({iframe:{css:{width:'600'}}}); 

$('.showhide').click(function(){$(this).next().slideToggle();});
$('.slide-srch').click(function(dg){dg.stopPropagation();$('.srch_pop').slideToggle('fast');}); 

$('.fancybox').fancybox();
$('.mygallery').fancybox({wrapCSS:'fancybox-custom',closeClick : true, openEffect : 'none',helpers : {title : {type : 'inside'},overlay : {css : {'background' : 'rgba(0,0,0,0.6)'}}}});

$('.shownext').click(function(e){var DG=$(this).data('closed');$(DG).hide();$('.subdd').hide('fast');$(this).next().slideToggle('fast');$('.navbar-collapse.collapse.show,.dropdown-menu.show').removeClass('show');e.stopPropagation();})

$('input.tabs').click(function(){var dg='.'+$(this).attr('title'); $('.form_box').slideUp('fast');$(dg).slideDown('fast');})


if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) 
{$('body').click(function(){$('.search_form').hide()})
}else
{$('body').click(function(){$('.search_form').hide()})}
$('.search_form*','.collapse.show*','.dropdown-menu.show*').click(function(e){e.stopPropagation()})
$('.search_form').click(function(e){e.stopPropagation()})

$('.nav_pos').on('click','a.tog_menu',function(){$('#sidebar').toggleClass('slide_menu');if($('#sidebar').hasClass('slide_menu')){$('#main-content').css('margin-left','105px');$('#sidebar ul.acc_links li').addClass('tog_btn')}else{$('#main-content').css('margin-left','0px')}})

$('#sidebar').on('mouseenter','li',function(){if($('#sidebar').hasClass('slide_menu')){$(this).children('div').show();$(this).siblings().find('div.dashboard_sub_list').hide()}}).on('mouseleave','li',function(){if($('#sidebar').hasClass('slide_menu')){$(this).children('div.dashboard_sub_list').hide()}}).on('click','li',function(){$(this).children('div').toggle();$(this).siblings().find('div.dashboard_sub_list').hide()})

$('#sidebar').on('click','p.acc_link_title',function(){
$(this).toggleClass('acc_link_act').next().toggle();$(this).closest('div').css('display','');e.stopPropagation()})

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})


$('.attach_btn').click(function(){$(this).prev().trigger('click');})
$('.dg_custom_file').on('change',function(){var dg=$(this).prop('value');$(this).parent().children('b.file_url').text(dg)})


/*
$('#sidebar ul.acc_links').on('click','li',function(e){if($(this).hasClass('tog_btn')){e.stopPropagation();$(this).find('div').slideToggle();}else{e.stopPropagation()}})

$('#sidebar ul.acc_links').on('mouseover','li',function(e){if($(this).parents('#sidebar').hasClass('slide_menu')){e.stopPropagation();}else{$('.dash_sub_list').hide();$(this).find('div').show()}}).on('mouseout','li',function(e){if($(this).parents('#sidebar').hasClass('slide_menu')){e.stopPropagation();}else{$('.dash_sub_list').hide();}})
*/


/*if(window.outerWidth < 1024) {
$('.nav_pos').on('click','a.tog_menu',function(){$('#sidebar').toggleClass('slide_menu');if($('#sidebar').hasClass('slide_menu')){$('#main-content').css('margin-left','105px');$('#sidebar ul.acc_links li').addClass('tog_btn')}else{$('#main-content').css('margin-left','0px')}})
}

if(window.outerWidth > 1024) {
$('.nav_pos').on('click','a.tog_menu',function(){$('#sidebar').toggleClass('slide_menu');if($('#sidebar').hasClass('slide_menu')){$('#main-content').css('margin-left','105px');$('#sidebar ul.acc_links li').addClass('tog_btn')}else{$('#main-content').css('margin-left','245px')}})
}

$('#sidebar ul.acc_links').on('mouseover','li',function(e){if($(this).parents('#sidebar').hasClass('slide_menu')){e.stopPropagation();}else{$('.dash_sub_list').hide();$(this).find('div').show()}}).on('mouseout','li',function(e){if($(this).parents('#sidebar').hasClass('slide_menu')){e.stopPropagation();}else{$('.dash_sub_list').hide();}})*/


$(".scroll").click(function(event){
event.preventDefault();
$('html,body').animate({scrollTop:$(this.hash).offset().top-55}, 1000);
});

$("#back-top").hide();	
$(function () {$(window).scroll(function () {if ($(this).scrollTop() > 100) {$('#back-top').fadeIn();} else {$('#back-top').fadeOut();}});
$('#back-top a').click(function () {$('body,html').animate({scrollTop: 0}, 800);return false;});
});

if(Page=='home'){
$("#owl-music, #owl-video").owlCarousel({autoplay:false,dots:false,nav:true,navText: [ '', '' ],items:4,responsive:{0:{items:1},479:{items:1},767:{items:2},991:{items:2},1151:{items:3},1279:{items:3}}});
}

});