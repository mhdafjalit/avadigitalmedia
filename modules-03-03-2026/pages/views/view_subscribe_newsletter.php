<?php $this->load->view('top_application',array('has_header'=>false,'ws_page'=>'refer_fr','is_popup'=>true,'has_body_style'=>''));?>
	<div class="popup_w">
		<h1>Newsletter</h1>
		<?php echo error_message();
		echo form_open('pages/newsletter','role="form"');?>
		<div class="form-row"> 
			<div class="form-group col-12">
				<input name="subscriber_name" id="subscriber_name" type="text" placeholder="Name *" class="form-control" value="<?php echo set_value('subscriber_name');?>"><?php echo form_error('subscriber_name');?>
			</div>
			<div class="form-group col-12">
				<input name="subscriber_email" id="subscriber_email" type="text" placeholder="Email ID *" class="form-control" value="<?php echo set_value('subscriber_email');?>"><?php echo form_error('subscriber_email');?>
			</div>
			<div class="form-group col-12">
				<input name="verification_code" id="verification_code" autocomplete="off" type="text" class="float-left form-control" style="width:110px;" placeholder="Enter Code *">
				<a href="javascript:void(0);" class="float-left">
				<img src="<?php echo site_url('captcha/normal');?>" class="ml-2" alt="" id="captchaimage"></a>
				<a href="javascript:void(0);" class="float-left ml-2">
				<img src="<?php echo theme_url(); ?>images/ref2.png" alt="Refresh" title="Refresh" onClick="document.getElementById('captchaimage').src='<?php echo site_url('captcha/normal'); ?>/<?php echo uniqid(time()); ?>'+Math.random(); document.getElementById('verification_code').focus();"></a>
				<div class="clearfix mt10 mb10"></div>
				<?php echo form_error('verification_code');?>
			</div> 
			<p class="col-12"><input name="submit" type="submit" value="Subscribe" class="view_btn radius-3"></p>
		</div>
	 	<?php echo form_close();?>
	</div>
</body>
</html>