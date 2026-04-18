<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'admin/release'),
		array('heading'=>'Dashboard','url'=>'admin')
	);
$album_type = (int) $this->input->get_post('album_type');
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
				<div class="white_bx overflow-hidden">       
					<div class="table-responsive">
						<div class="scrollbar style-4">
							<table class="table table-bordered mb-0 acc_table table-striped">
							 	<thead>
									<tr>
										<th>S. No.</th>
										<th>Track</th>
										<th>Price</th>
									</tr>
								</thead>
								<tbody>
									<!--- <tr>
										<td>1.</td>
										<td>
										<div class="d-flex">
										<div class="list_pic text-center overflow-hidden rounded-3"><figure class="align-middle d-table-cell"><img src="<?php echo theme_url();?>images/draft-pic2.jpg" alt="" width="60" height="60" class="mw-100 mh-100"></figure></div>
										<div class="ms-2 mt-1">
										<p class="fw-bold purple">Ho Jati Dil Ki Two Copy</p>
										<p class="fs-7 mt-1">28 June 2024</p>
										</div>
										</div>
										</td>
										<td><b class="purple d-block">$869.60</b> <p class="track_perctng">28.5%</p></td>
									</tr> -->


									<tr>
										<td colspan="3"><div class="text-center b mt-4">No record(s) Found.</div></td>
										
									</tr>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			<!---	<nav aria-label="Page navigation example">
				  <ul class="pagination justify-content-end">
				    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
				    <li class="page-item"><a class="page-link" href="#">1</a></li>
				    <li class="page-item active"><a class="page-link" href="#">2</a></li>
				    <li class="page-item"><a class="page-link" href="#">3</a></li>
				    <li class="page-item"><a class="page-link" href="#">Next</a></li>
				  </ul>
				</nav> --->
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>