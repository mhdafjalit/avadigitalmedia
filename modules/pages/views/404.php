<?php $this->load->view('top_application');
echo navigation_breadcrumb('404');?>
<div id="container">
	<div class="cms_area">
		<div class="mt-5 text-center">
			<img src="<?php echo theme_url(); ?>images/404.jpg" class="mt-3 mw_96" alt="">
			<p class="mt-3"><a href="<?php echo site_url('');?>" class="btn btn-dark">Go to Home Page</a></p>
		</div>
	</div>
</div>
<?php $this->load->view('bottom_application');?>