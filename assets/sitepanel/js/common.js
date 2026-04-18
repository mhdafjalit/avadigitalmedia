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

/*Online List Support Methods*/

function showloader(id)
{
	$("#"+id).after("<span id='"+id+"_loader'><img src='"+site_url+"/assets/developers/images/loader.gif'/></span>");
}


function hideloader(id)
{
	$("#"+id+"_loader").remove();
}

function bind_data(parent_id,method_url,container_id,loader_container,from_section)
{
	showloader(loader_container);
	$("#"+container_id).hide();  
	var ajax_url=site_url+method_url;
	if(from_section=='neighborhood_country')
	{
		$('#city_list').html('<select name="city_id" style="width:235px;"><option value="">Select One</option></select>');
	}
	
	$.ajax({
			type: "POST",
			url: ajax_url,
			dataType: "html",
			data: "parent_id="+parent_id+"&from_section="+from_section,
			cache:false,
			success:
				function(data)
				{
					$("#"+container_id).show();
					$("#"+container_id).html(data);
					hideloader(loader_container);
					
				}
				
	}); 
}


jQuery(document).ready(function() 
{
	
	$('.add_attr').click
	(
		function()
		{	
			var rec_id=$(this).val();
			var ajax_url=site_url+"sitepanel/attributes/create_selection_box"
			
			showloader("loader_"+rec_id);
			$("#resp_"+rec_id).hide();  
			
			if($(this).is(':checked'))
			{
				$.ajax({
							type: "POST",
							url: ajax_url,
							dataType: "html",
							data: "rec_id="+rec_id,
							cache:false,
							success:
								function(data)
								{
									$("#resp_"+rec_id).show();
									$("#resp_"+rec_id).html(data);
									hideloader("loader_"+rec_id);
									
								}
								
					});	
			}
			else
			{
				hideloader("loader_"+rec_id);
				$('#resp_'+rec_id).html('');
			}
		}
	)
});


$(document).ready(function(e) {
   $("[name='selall_atrval']").change(function() {
	var clsname=$(this).attr('class');
    if(this.checked) {
       $('.'+clsname).attr('checked','checked');
    }
	else
	{
		$('.'+clsname).removeAttr('checked');	
	}
});

$('.comclass').click(function()
{
	var noofcontcheckd=($('.comclass:checkbox:checked').length);
	
	if(noofcontcheckd>6)
	{
		alert('Only allowed 6 country to search.');
		$(this).removeAttr('checked');
	}
}); 

//$('[data-ci-pagination-page]').attr('style','padding:2px;');
$('[data-ci-pagination-page]').attr('style','margin-left:3px;');

});	
	
	
function setingroup_member(groupid)
{
	var total_checked;
	total_checked=$("input[type='checkbox'][name='arr_ids[]']:checked").length;
	
	if(total_checked)
	{
		$('#setingroup').val('setingroup');
		$('#data_form').submit();
	}
	else
	{
		$('#group_id').val(0);
		alert("Please select atleast one member to set in group.");
	}
}	


function savepaidamt(id,paid_amt,loader_container,seller_id)
{
	showloader(loader_container);
	
	var ajax_url=site_url+"sitepanel/members/pay_payout_request";
	
	$.ajax({
			type: "POST",
			url: ajax_url,
			dataType: "html",
			data: "payout_id="+id+"&paid_amt="+paid_amt+"&seller_id="+seller_id,
			cache:false,
			success:
				function(data)
				{
					$('#message'+id).html(data);
					hideloader(loader_container);
					
				}
	}); 
}
function savepaidamt_accountsheet(id,seller_id,paid_amt,remaining,loader_container)
{
	showloader(loader_container);

	var ajax_url=site_url+"sitepanel/members/pay_amount_to_seller";
	
	$.ajax({
			type: "POST",
			url: ajax_url,
			dataType: "html",
			data: "remaining="+remaining+"&paid_amt="+paid_amt+"&seller_id="+seller_id,
			cache:false,
			success:
				function(data)
				{
					$('#message'+id).html(data);
					hideloader(loader_container);
					
				}
	}); 
}

function update_credit_points(cat_id,credit_points,loader_container)
{
	showloader(loader_container);
	
	var ajax_url=site_url+"sitepanel/category/update_credit_points";
	
	$.ajax({
			type: "POST",
			url: ajax_url,
			dataType: "html",
			data: "cat_id="+cat_id+"&credit_points="+credit_points,
			cache:false,
			success:
				function(data)
				{
					$('#message'+cat_id).html(data);
					hideloader(loader_container);
					
				}
	}); 
}

function accept_valid_price(fldid)
{
	var price;
	var reg=/^[0-9\.]*$/;
	price=$('#'+fldid).val();
	validate_val=reg.test(price);
	if(validate_val==false)
	{
		$('#'+fldid).val('');
		//return false;
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

	$(document).on('keyup','.hasDatepicker',function(e){
		cobj = $(this);
		if(e.keyCode==8){
			cobj.val('');
		}
  });
	$('.btn_del_trash').click(function(e){
		var ckbox_obj = $("input[type='checkbox'][name='arr_ids[]']:checked");
		if(!ckbox_obj.length){
			alert("Please select at least one record");
			return false;
		}
		var cfm = confirm('Are you sure you want to delete trash?');
		if(!cfm){
			return false;
		}

	});
});
var ajxFaculty=null;
function fetchFaculty(){
	var cur_obj = $(this);
	var subject_id = cur_obj.val();
	var faculty_obj = $('#ref_faculty_id');
	faculty_obj.find('option:gt(0)').remove();
	faculty_obj.addClass('bck_loading');
	var current_selected_value = faculty_obj.data('selected-value');
	current_selected_value_arr = current_selected_value.toString().split(','); 
	ajxFaculty = $.ajax({
					url:base_url+'sitepanel/remote/load_faculty',
					type:'post',
					data:{subject_id:subject_id,current_selected:current_selected_value_arr},
					beforeSend:function(){
						if(ajxFaculty){
							ajxFaculty.abort();
						}
					}
				}).done(function(data){
					if(data!=''){
						faculty_obj.append(data);
					}
				}).fail(function(){}).always(function(){
					faculty_obj.removeClass('bck_loading');
				});
}