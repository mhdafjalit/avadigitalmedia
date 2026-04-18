<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'Dashboard','url'=>'members')
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
				<div class="row g-3">
					<?php $this->load->view('news/load_news');?>
					<?php $this->load->view('news/load_news');?>
					<?php $this->load->view('news/load_news');?>
					<?php $this->load->view('news/load_news');?>
				</div>
				<nav aria-label="Page navigation example">
				  <ul class="pagination justify-content-end">
				    <li class="page-item"><a class="page-link" href="news-details.htm">Previous</a></li>
				    <li class="page-item"><a class="page-link" href="#">1</a></li>
				    <li class="page-item active"><a class="page-link" href="#">2</a></li>
				    <li class="page-item"><a class="page-link" href="#">3</a></li>
				    <li class="page-item"><a class="page-link" href="#">Next</a></li>
				  </ul>
				</nav>	
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>