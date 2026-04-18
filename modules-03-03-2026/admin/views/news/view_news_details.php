<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'News','url'=>'admin/news'),
	array('heading'=>'Dashboard','url'=>'admin')
);
?>
<div class="dash_outer">
  	<div class="dash_container">
	    <?php $this->load->view('view_left_sidebar'); ?>
	    <div id="main-content" class="h-100">
	      	<?php $this->load->view('view_top_sidebar');?>
	      	<div class="top_sec d-flex justify-content-between">
		        <h1 class="mt-4"><?php echo $heading_title;?></h1>
		        <?php echo navigation_breadcrumb($heading_title,$bdcm_array); ?>
	      	</div>
	      	<p class="clearfix"></p>
			<div class="main-content-inner">
				<div class="row g-0">
					<div class="col-lg-5 text-center">
						<img src="<?php echo theme_url();?>images/news-pic-b.jpg" class="img-fluid rounded-4" alt="">
					</div>
					<div class="col-lg-7 ps-lg-4 mt-3 mt-lg-0">
					<p class="fs-7">Apr 02, 2024</p>
					<p class="fs-4 mt-1 fw-semibold">Lorem ipsum dolor sit amet consect adipiscing elit, sed do eiusmod</p>
					<div class="mt-3 lh-sm">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
					<br><br>
					Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>