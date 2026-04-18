<?php
$mem_name = trim($mres['first_name'].' '.$mres['last_name']);
$this->load->view('top_application',array('ws_page'=>'votp'));
echo navigation_breadcrumb($heading_title);
?>
<div class="login_cont">
	<div class="container">
		<div class=" verify_sect">
			<p class="verify"><i class="fas fa-smile" aria-hidden="true"></i><span>Thank you</span></p>

			<div class="popup_content text-center">
				<div class="pop_sub_hed">
					Enter 4 digit code sent to your phone  <span><?php echo $mres['mobile_number'];?></span>
				</div>
				<?php echo form_open(current_url_query_string(),'name="otp_frm" id="otp_frm" autocomplete="off"');?>
				<p class="mt-1 opt_input">
					<input name="code_dig_1" id="code_dig_1" type="text" value="" class="p-2 border1 x_tp_otp" maxlength="1" autocomplete="off">
					<input name="code_dig_2" id="code_dig_2" type="text" value="" class="p-2 border1 x_tp_otp" maxlength="1" autocomplete="off">
					<input name="code_dig_3" id="code_dig_3" type="text" value="" class="p-2 border1 x_tp_otp" maxlength="1" autocomplete="off">
					<input name="code_dig_4" id="code_dig_4" type="text" value="" class="p-2 border1 x_tp_otp" maxlength="1" autocomplete="off">
				</p>
				<div id="err_otp_code"><?php echo form_error('otp_code');?></div>
				<div id="mobile_otp_text"><?php echo $otp_mobile;?></div>
				<p class="mt-1 text-center"><a href="#" id="btn_resend">Resend Code</a></p>
				<div class="mt-2"><a href="#" id="btn_verify" class="disabled_btn btn btn-success" data-actual-value="Continue" data-process-text="Wait...">Continue</a></div>
				<?php echo form_close();?>
			</div>
		</div>
	</div>
</div>
<script>
	var btn_verify_obj = $('#btn_verify');
	$('.x_tp_otp').on('keypress keyup paste',function(e){
		var is_num_key =  isNumberKey(e);
		var total_filled_box=0;
		if(!is_num_key){
			e.preventDefault();
			return false;
		}
		var empty_slot;
		$('.x_tp_otp').each(function(m,n){
			var $n = $(n);
			if($(n).val()!=''){
				total_filled_box++;
			}else{
				if(typeof empty_slot==='undefined'){
					empty_slot = $n;
				}
			}
		});
		if(total_filled_box==4){
			btn_verify_obj.removeClass('disabled_btn');
		}else{
			btn_verify_obj.addClass('disabled_btn');
		}
		if(typeof empty_slot!=='undefined'){
			empty_slot.focus();
		}
	});
	btn_verify_obj.click(function(e){
		e.preventDefault();
		var acc_btn_sbt = $(this);
		var frmobj = $('#otp_frm');
		var action_url = frmobj.attr('action');
		var frmdata;
		var otp_code="";
		frmobj.find('.required').remove();
		if(!frmobj.hasClass('process_x')){
			$('.x_tp_otp').each(function(m,n){
				var $n = $(n);
				if($n.val()!=''){
					otp_code+=$n.val();
				}
			});
			frmobj.addClass('process_x');
			acc_btn_sbt.html(acc_btn_sbt.data('process-text'));
			$('#btn_resend').addClass('disabled_btn');
			//$('#mobile_otp_text').html('');
			frmdata = new FormData();
			frmdata.append('otp_code',otp_code);
			frmdata.append('btn_sbt','Y');
			$.ajax({
				url:action_url,
				type:'post',
				data:frmdata,
				headers:{XRSP:'json'},	
				contentType: false,
				processData: false,
				dataType:'json'
			}).done(function(data){
				if(data.status=='1'){
					location.href= action_url;
				}else{
					if(Object.keys(data.error_flds).length){
						$.each(data.error_flds,function(m,n){
							$('#err_'+m).html('<div class="required">'+n+'</div>');
						});
					}
				}
			}).always(function(){
				$('#btn_resend').removeClass('disabled_btn');
				frmobj.removeClass('process_x');
				acc_btn_sbt.html(acc_btn_sbt.data('actual-value'));
			});
		}
	});
	$('#btn_resend').click(function(e){
		e.preventDefault();
		var btn_resend = $(this);
		var frmobj = $('#otp_frm');
		var action_url = frmobj.attr('action');
		frmobj.addClass('disabled_btn');
		btn_verify_obj.addClass('disabled_btn');
		$('.x_tp_otp').val('');
		$('#mobile_otp_text').html('');
		$('#err_otp_code').html('');
		$.post(action_url,{btn_resend:'Y'},'json').done(function(data){
			var data = JSON.parse(data);
			if(data.status=='1'){
				$('#mobile_otp_text').html(data.otp);
			}
			$('#err_otp_code').html(data.msg);
		}).always(function(){
				frmobj.removeClass('disabled_btn');
		});
	});
</script>
<?php $this->load->view('bottom_application',array('has_footer'=>1,'ws_page'=>'votp'));?>