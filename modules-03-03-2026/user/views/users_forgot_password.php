<?php $this->load->view('top_application',array('has_header'=>false,'ws_page'=>'forgot_pwd','is_popup'=>true,'has_body_style'=>'padding:0'));?>
	<div class="p-3 bg-light text-center border-bottom">
		<h1>Forgot Password</h1>
	</div>
	<div class="p-3">
		<?php error_message();?>
		<?php echo form_open(current_url_query_string(),'name="fpwd_frm" autocomplete="off"'); ?>
		<div class="mb-2">
			<label for="email" class="form-label">Email ID *</label>
			<input name="email" id="email" type="text" value="<?php echo set_value('email');?>" class="form-control">
			<?php echo form_error('email');?>
		</div>
		<?php /*
		<div class="mb-2">
			<input name="verification_code" id="verification_code" type="text" class="float-left form-control" placeholder="Enter Code *" style="width:130px;"> 
			<a href="javascript:void(0)" class="float-left ml5">
				<img src="<?php echo base_url();?>captcha/normal/fp" class="vam" alt="" title="" id="captcha_img_fp">
			</a> 
			<a href="javascript:void(0)" class="float-left ml5 captcha_refresh" data-src="<?php echo base_url();?>captcha/normal/fp" data-cont="#captcha_img_fp" title="Refresh Code">
				<img src="<?php echo theme_url();?>images/ref2.png" class="vam" alt="" title="" >
			</a>
			<div class="clearfix"></div>
			<?php echo form_error('verification_code');?>
		</div>
		*/?>
		<p class="clearfix"></p>
		<div class="text-center">
			<input type="hidden" name="forgotme" value="Y">
			<input name="" type="submit" class="btn btn-purple" value="Submit"> 
		</div>
		<?php echo form_close();?>
	</div>
</body>
</html>