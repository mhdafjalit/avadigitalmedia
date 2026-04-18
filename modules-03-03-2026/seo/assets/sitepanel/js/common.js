function validcheckstatus(name,action,text)
{
	var chObj	=	document.getElementsByName(name);
	var result	=	false;	
	for(var i=0;i<chObj.length;i++){
	
		if(chObj[i].checked){
		  result=true;
		  break;
		}
	}
 
	if(!result){
		 alert("Please select atleast one "+text+" to "+action+".");
		 return false;
	}else if(action=='delete'){
			 if(!confirm("Are you sure you want to delete this.")){
			   return false;
			 }else{
				return true;
			 }
	}else{
		return true;
	}
}
var prev_url_val = "";
var ws_alert_prompt = 0;
$(document).ready(function(){
	

	function get_seo_url(e){
		target_obj = e.target;
		target_obj = $(target_obj);
		changeable_obj = $('.seo_friendly_url');
		pg_title = target_obj.val();
		pg_title = $.trim(pg_title);
		
		if(pg_title !=''){
			$('#error_url_creator').html('');
			current_url_val = pg_title;
			if(prev_url_val != current_url_val){
				prev_url_val = current_url_val;
				pre_seo_url_obj = $('#pre_seo_url');
				pre_title = pre_seo_url_obj.length ? pre_seo_url_obj.val() : "";
				pre_title = $.trim(pre_title);
				rec_obj = $('#pg_recid');
				rec_id = rec_obj.length ? rec_obj.val() : "";
				rec_id = $.trim(rec_id);
				$.post(site_url+'seo/create_seo_url',{title:pg_title,pre_title:pre_title,rec_id:rec_id},function(data){
					if(data.error){
						$('#error_friendly_url').html(data.msg);
					}else{
						$('#error_friendly_url').html('');
					}
					changeable_obj.val(data.friendly_name);
				},"json");
			}
		}
		else{
			if(ws_alert_prompt == 1){
				$('#error_url_creator').html('Please enter '+target_obj.attr('placeholder'));
				//target_obj.focus();
			}
			
		}
		
	}
	$('.url_creator').bind('blur',get_seo_url);
	$('.change_url').click(function(e){
		e.preventDefault();
		$(this).hide();
		ws_alert_prompt = 0;
		$('.seo_friendly_url').attr('readonly',false);
		$('.url_creator').unbind('blur');
		$('.seo_friendly_url').bind('blur',get_seo_url);
	});
	$('.url_from_title').click(function(e){
		e.preventDefault();
		ws_alert_prompt = 1;
		$('.change_url').show();
		$('.seo_friendly_url').attr('readonly',true).unbind('blur');
		$('.url_creator').bind('blur',get_seo_url).trigger('blur');
	});

	$('.edit_url').bind('blur',get_seo_url);
	
});