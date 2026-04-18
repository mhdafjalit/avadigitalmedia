<?php 
$this->load->view('top_application',array('has_header'=>false,'has_body_class'=>'login_bg'));
$site_title_text = escape_chars($this->config->item('site_name'));?>
<div class="container-xxl">
	<div class="login_box">
		<p class="text-center logo">
			<img src="<?php echo theme_url();?>images/auva.jpg" width="175" height="83" alt="<?php echo $site_title_text;?>">
		</p>
		<p class="text-center mt-2 text-black fw-medium">Please sign in to your existing account</p>
		<?php
		/*
		<div class="accounts_logs fw-semibold text-center">
			<label><input type="radio" name="time_slot" value="1" checked=""> Super Admin  </label>
			<label><input type="radio" name="time_slot" value="2">Sub - User</label>
			<label><input type="radio" name="time_slot" value="3">User</label>
		</div>
		*/?>
		<div id="err_msg" class="text-center me-4"><?php echo error_message();?></div>
		<div class="form_box2">
			<?php echo form_open(current_url_query_string(),'name="login_frm" autocomplete="off"'); ?>
			<div class="login_email">
				<input type="text" id="user_name" name="user_name" value="<?php echo set_value('user_name',$posted_user_name);?>" class="border-0 bg-transparent w-100" placeholder="User name / User ID *">
			</div>
			<div id="err_user_name"><?php echo form_error('user_name');?></div>
			<div class="login_password position-relative">
				<a href="#" class="login_eye" onclick="togglePasswordVisibility(event)">
			        <img src="<?php echo theme_url();?>images/eye.svg" alt="Show Password" id="eye_icon">
			    </a>
				<input type="password" id="password" name="password" class="border-0 bg-transparent w-100" value="<?php echo set_value('password',$posted_password);?>" placeholder="Password *">
			</div>
		  	<div id="err_password"><?php echo form_error('password');?></div>
		  	<p class="clearfix"></p>
			<div class="float-start form-check">
			  	<input class="form-check-input" type="checkbox" id="flexCheckChecked" name="remember" value="Y" <?php echo set_value('remember',$remember)=='Y' ? ' checked="checked"' : '';?>>
			  	<label class="form-check-label fs-7" for="flexCheckChecked">Remember me</label>
			</div>
			<div class="float-end fs-7 fw-medium"> 
				<a data-fancybox="" data-type="iframe" data-src="<?php echo site_url('forgot-password');?>" href="javascript:void(0);" class="pop1 blue">Forgot Your Password?</a>
			</div>
			<p class="clearfix"></p>
			<div class="login_tab mt-3 text-center">	
				<input type="hidden" name="action" value="Y" />
				<input type="hidden" class="member_type" name="member_type" value="">
				<button type="submit" id="login_btn" class="text-white rounded-5 fw-bold d-inline-block trans_eff">Sign In</button>
			</div>
			<p class="mt-4 text-center fw-bold text-uppercase">
				<a href="<?php echo site_url('register');?>" class="text-primary">Create an Account</a>
			</p>
			<?php echo form_close();?>
		</div>
	</div>
	<p class="clearfix"></p>
</div>
<script>
function togglePasswordVisibility(event) {
    event.preventDefault();
    var passwordInput = document.getElementById("password");
    var eyeIcon = document.getElementById("eye_icon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.src = "<?php echo theme_url(); ?>images/eye2.svg";
    } else {
        passwordInput.type = "password";
        eyeIcon.src = "<?php echo theme_url(); ?>images/eye.svg";
    }
}
</script>
<script>
	var member_type = $('input[name="time_slot"]:checked').val();
	$('.member_type').val(member_type);
	$('input[name="time_slot"]').click(function() {
	    var member_type = $(this).val();
	    $('.member_type').val(member_type);
	});

	$('#login_btn').click(function(e){
		e.preventDefault();
		var frmobj = $('#login_frm');
		var action_url = frmobj.attr('action');
		var user_name = $('#user_name').val();
		var password = $('#password').val();
		//var member_type = $('.member_type').val();
		frmobj.find('.required').remove();
		$('#err_msg').html('');
		$.post(action_url,{user_name:user_name,password:password},'json').done(function(data){
			var data = JSON.parse(data);
			if(data.status=='1'){
				if(data.msg){
					swal({
						icon:'success',
						text:data.msg,
						button: false,
						timer:2000
					}).then(function(){location.href= typeof data.redirect_url!=='undefined' ? data.redirect_url : site_url('members');});
				}else{
					location.href= typeof data.redirect_url!=='undefined' ? data.redirect_url : site_url('members');
				}
			}else{
				if(data.error_flds && Object.keys(data.error_flds).length){
					$.each(data.error_flds,function(m,n){
						$('#err_'+m).html('<div class="required">'+n+'</div>');
					});
				}
				if(data.msg!=''){
					$('#err_msg').html(data.msg);
				}
			}
		}).always(function(){
				frmobj.removeClass('disabled_btn');
		});
	});
	var btn_verify_obj = $('#btn_verify');
	var btn_resend_obj = $('#btn_resend');
	$('#otp_code').on('keypress keyup paste input',function(e){
		var charCode = e.which ? e.which : e.keyCode;
		var cobj = $(this);
		var cval = cobj.val();
		var is_num_key =  isNumberKey(e);
		if(!is_num_key){
			e.preventDefault();
			return false;
		}
		if(cval!='' && cval.length==4){
			btn_verify_obj.removeClass('disabled_btn');
		}else{
			btn_verify_obj.addClass('disabled_btn');
		}
		if(charCode==13){
			if(btn_verify_obj.hasClass('disabled_btn')){
				return false;
			}else{
				btn_verify_obj.trigger('click');
				return false;
			}
		}
	});
	btn_verify_obj.click(function(e){
		e.preventDefault();
		var acc_btn_sbt = $(this);
		var frmobj = $('#otp_frm');
		var action_url = frmobj.attr('action');
		var frmdata;
		var otp_code=$('#otp_code').val();
		var mobile_number = $('#mobile_number').val();
		var member_type = $('.member_type').val();
		frmobj.find('.required').remove();
		if(!frmobj.hasClass('process_x')){
			frmobj.addClass('process_x');
			acc_btn_sbt.html(acc_btn_sbt.data('process-text'));
			$('#btn_resend').addClass('disabled_btn');
			//$('#mobile_otp_text').html('');
			frmdata = new FormData();
			frmdata.append('otp_code',otp_code);
			frmdata.append('mobile_number',mobile_number);
			frmdata.append('member_type',member_type);
			frmdata.append('action','verify');
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
					if(data.msg){
						swal({
							icon:'success',
							text:data.msg,
							button: false,
							timer:20000
						}).then(function(){location.href= typeof data.redirect_url!=='undefined' ? data.redirect_url : site_url('members');});
					}else{
						location.href= typeof data.redirect_url!=='undefined' ? data.redirect_url : site_url('members');
					}
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
		var mobile_number = $('#mobile_number').val();
		var member_type = $('.member_type').val();
		var action_url = frmobj.attr('action');
		btn_resend_obj.addClass('disabled_btn');
		btn_verify_obj.addClass('disabled_btn');
		$('#otp_code').val('');
		$('#mobile_otp_text').html('');
		$('#err_otp_code').html('');
		$('#err_msg').html('');
		frmobj.find('.required').remove();
		$.post(action_url,{mobile_number:mobile_number,member_type:member_type,btn_resend:'Y'},'json').done(function(data){
			var data = JSON.parse(data);
			if(data.status=='1'){
				$('#mobile_otp_text').html(data.otp);
			}
			$('#err_otp_code').html(data.msg);
			btn_resend_obj.removeClass('disabled_btn');
		}).always(function(){
				frmobj.removeClass('disabled_btn');
		});
	});
	$('#send_otp_btn').addClass('disabled_btn');
	$('#mobile_number').on('keypress keyup paste input',function(e){
		var charCode = e.which ? e.which : e.keyCode;
		var cobj = $(this);
		var cval=cobj.val();
		cval = $.trim(cval);
		var send_otp_btn_obj = $('#send_otp_btn');
		if(cval!='' && cval.length>=9){
			send_otp_btn_obj.removeClass('disabled_btn');
		}else{
			send_otp_btn_obj.addClass('disabled_btn');
		}
		if(charCode==13){
			if(send_otp_btn_obj.hasClass('disabled_btn')){
				return false;
			}else{
				send_otp_btn_obj.trigger('click');
				return false;
			}
		}
	});
	$('#send_otp_btn').click(function(e){
		e.preventDefault();
		var update_mobile = $(this);
		var frmobj = $('#otp_frm');
		var action_url = frmobj.attr('action');
		var mobile_number = $('#mobile_number').val();
		var member_type = $('.member_type').val();
		$('#mobile_number').addClass('disabled_btn');
		frmobj.addClass('disabled_btn');
		btn_resend_obj.addClass('disabled_btn');
		btn_verify_obj.addClass('disabled_btn');
		$('#otp_code').val('');
		$('#mobile_otp_text').html('');
		$('#err_otp_code').html('');
		$('#err_msg').html('');
		frmobj.find('.required').remove();
		$.post(action_url,{mobile_number:mobile_number,member_type:member_type,send_otp:'Y'},'json').done(function(data){
			var data = JSON.parse(data);
			if(data.status=='1'){
				$('#otp_sent_mobile_number').text(mobile_number);
				$('#mobile_otp_text').html(data.otp || '');
				$('#send_otp_btn').addClass('disabled_btn');
				btn_resend_obj.removeClass('disabled_btn');
				$('#verify_otp_modal').modal('show');
			}else{
				if(data.error_flds && Object.keys(data.error_flds).length){
					$.each(data.error_flds,function(m,n){
						$('#err_'+m).html('<div class="required">'+n+'</div>');
					});
					$('#otpclass').hide();
					$('#send_otp_btn').show();
					$('#mobile_number').removeClass('disabled_btn');	
				}
				if(data.msg!=''){
					$('#err_msg').html(data.msg);
				}
			}
		}).always(function(){
				frmobj.removeClass('disabled_btn');
		});
	});
</script>
<?php $this->load->view('bottom_application',array('has_footer'=>false));?>