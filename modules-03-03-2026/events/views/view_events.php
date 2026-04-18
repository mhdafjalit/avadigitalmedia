<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
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
	      	<?php 
		    if(is_array($res_events) && !empty($res_events)){?>
			<div class="main-content-inner">
				<div class="row g-3" id="my_scroll_data">
					<?php $this->load->view('events/load_events');?>
				</div>
				<?php /*
				<nav aria-label="Page navigation example">
				  <ul class="pagination justify-content-end">
				    <li class="page-item"><a class="page-link" href="news-details.htm">Previous</a></li>
				    <li class="page-item"><a class="page-link" href="#">1</a></li>
				    <li class="page-item active"><a class="page-link" href="#">2</a></li>
				    <li class="page-item"><a class="page-link" href="#">3</a></li>
				    <li class="page-item"><a class="page-link" href="#">Next</a></li>
				  </ul>
				</nav>
				*/?>
			</div>
			<?php 
    		}else{
    			echo '<div class="text-center b">'.$this->config->item('no_record_found').'</div>';
    		}?>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>