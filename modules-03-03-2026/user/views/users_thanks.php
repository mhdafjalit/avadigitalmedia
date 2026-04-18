<?php
$this->load->view('top_application');
echo navigation_breadcrumb($heading_title);
?>
<div class="login_cont">
	<div class="container">
		<div class=" verify_sect">
			<div class="popup_content text-center">
				<div class="pop_sub_hed">
					<span class="verify"><?php echo $msg!='' ? $msg : '';?></span>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('bottom_application');?>