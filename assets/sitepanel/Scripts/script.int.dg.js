!window.jQuery && document.write(unescape('%3Cscript src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.8.3.min.js"%3E%3C/script%3E')); 
/*$('meta[name="viewport"]').prop('content', 'width=1280'); */

var Page = Page || '';

function include(url){ 
  document.write('<script src="'+ url + '" type="text/javascript"></script>'); 
}
include('//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
include(base_url+'assets/sitepanel/Scripts/helpers.min.js');

if(Page=='dashboard'){
include(base_url+'assets/sitepanel/Scripts/jquery.jqplot.min.js');
include(base_url+'assets/sitepanel/Scripts/plugins/jqplot.pieRenderer.js');
include(base_url+'assets/sitepanel/Scripts/plugins/jqplot.donutRenderer.js');
}

if(Page=='home'){}

else{
}

if(Page=='details'){}

$(document).ready(function(){
	var filter_by_cat_obj = $('#filter_by_cat');
	var filter_by_cat_val;
	if(filter_by_cat_obj.length){
		filter_by_cat_obj.on('keyup change',$.debounce(200,function(e){
			var lobj = $(this);
			var filter_val = lobj.val();
			filter_val = $.trim(filter_val);
			var regexp_filt = new RegExp(""+filter_val,"i");
			var show_optgroup;
			var hide_optgroup;
			if(filter_val!=''){
				$('[data-class="filter_cat"]').css('display',function(){
					return (regexp_filt.test($(this).data('title')) ? 'block' : 'none');
				});

				$('[data-class="filter_cat"]:visible').each(function(m,n){
						var $n = $(n);
						var cat_links_arr = [];
						var is_pcv = $n.data('pcv');
						var catlinks=$n.data('catlinks');
						if(typeof is_pcv==='undefined' && catlinks!=''){
							console.log(catlinks);
							cat_links_arr=catlinks.toString().split('~');
							$.each(cat_links_arr,function(m1,n1){
								console.log(n1);
								$('.tree_parent_opt[value="'+n1+'"]').data('pcv',1).show();
							});
						}
				});
			}else{
				$('[data-class="filter_cat"]').show();
				//$('optgroup').show();
			}
		}));
		if(filter_by_cat_obj.hasClass('xauto_trigger')){
			filter_by_cat_val = $(filter_by_cat_obj.data('lookup-obj')).find(":selected").data('title');
			filter_by_cat_obj.val(filter_by_cat_val).trigger('change');
		}
	}

});

$(window).load(function(e) {
$(".dg2").fancybox({
		'width': 450,
		'height': 360,
		'autoScale': false,
		'type': 'image'
	});		
$('.pop1').fancybox({'width':400,'height':205,'type':'iframe',title:{type:'outside'},closeBtn:false});
$('.pop_qb_dtl').fancybox({'width':900,'height':505,'type':'iframe',closeBtn:false});
$('.pop_mqc_select').fancybox({'width':900,'height':505,'type':'iframe',closeBtn:false});
$('.pop_chat_dtl').fancybox({'width':900,'height':505,'type':'iframe',closeBtn:false});

$('.showhide').click(function(){$(this).next().slideToggle();});

$('.lft_panel_open,div.p_open').on('click',function(e){$('.p_open').hide();$('.left_nav').removeClass('closed')


})
$('.lft_panel_close').click(function(){$('.p_open').show();$('.left_nav').addClass('closed');$('.left_nav').find('div[role=tabpanel]').removeClass('in');$('.left_nav').find('a[role=button]').removeClass('collapsed');$('.left_nav').find('i').addClass('fa-angle-down').removeClass('fa-angle-up');})

function toggleIcon(e) {
    $(e.target)
        .prev('.panel-heading')
        .find(".more-less")
        .toggleClass('fa fa-angle-down fa fa-angle-up');
}
$('.panel-group').on('hidden.bs.collapse', toggleIcon);
$('.panel-group').on('shown.bs.collapse', toggleIcon);


$('.fancybox').fancybox();
$('.mygallery').fancybox({wrapCSS:'fancybox-custom',closeClick : true, openEffect : 'none',helpers : {title : {type : 'inside'},overlay : {css : {'background' : 'rgba(0,0,0,0.6)'}}}});


$(".scroll").click(function(event){
event.preventDefault();
$('html,body').animate({scrollTop:$(this.hash).offset().top-55}, 1000);
});

$("#back-top").hide();	
$(function () {$(window).scroll(function () {if ($(this).scrollTop() > 100) {$('#back-top').fadeIn();} else {$('#back-top').fadeOut();}});
$('#back-top a').click(function () {$('body,html').animate({scrollTop: 0}, 800);return false;});
});

if(Page=='home'){}

});