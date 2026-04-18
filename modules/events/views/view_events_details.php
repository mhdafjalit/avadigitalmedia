<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'News & Events','url'=>'events'),
	array('heading'=>'Dashboard','url'=>'admin')
);
?>
<div class="dash_outer">
  	<div class="dash_container">
	    <?php $this->load->view('admin/view_left_sidebar'); ?>
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
						<img src="<?php echo get_image('events',($events_photo_media[0]['media'] ?? ''),500,500,'AR');?>" class="img-fluid rounded-4" alt="">
					</div>
					<div class="col-lg-7 ps-lg-4 mt-3 mt-lg-0">
    					<p class="fs-7"><?php echo getDateFormat($res_events['event_date1'],1);?></p>
    					<p class="fs-4 mt-1 fw-semibold"><?php echo $res_events['news_title'];?></p>
    					<div class="mt-3 lh-sm"><?php echo $res_events['news_description'];?></div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>