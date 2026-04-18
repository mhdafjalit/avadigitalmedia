<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'admin/release'),
		array('heading'=>'Dashboard','url'=>'admin')
	);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);
$genre_arr = $this->config->item('genre_arr');
$sub_genre_arr = $this->config->item('sub_genre_arr');
$prim_track_types 	= $this->config->item('prim_track_types');
$lang_arr 			= $this->config->item('lang_arr');
$prim_artists 		= get_prim_artists($res['release_id']);
$release_featurings = get_release_featurings($res['release_id']);
$authors = get_release_authors($res['release_id']);
$composers = get_release_composers($res['release_id']);
$arrangers = get_release_arrangers($res['release_id']);
$producers = get_release_producers($res['release_id']);
$label_name = get_db_field_value('wl_labels','channel_name',['label_id'=>$res['label_id'],'status'=>1]);
$total_territories = count_record ('wl_release_territories',"release_id='".$res['release_id']."'");
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
				<div class="dash_box p-4">
					<ul class="nav nav-underline tabber_style">
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/new_release/'.$releaseId.'?album_type='.$album_type);?>">Release Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/upload_release/'.$releaseId.'?album_type='.$album_type);?>">Upload</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/tracks/'.$releaseId.'?album_type='.$album_type);?>">Tracks</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/stores/'.$releaseId.'?album_type='.$album_type);?>">Stores</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/territories/'.$releaseId.'?album_type='.$album_type);?>">Territories</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/date_release/'.$releaseId.'?album_type='.$album_type);?>">Release Date</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="<?php echo site_url('admin/release/submission/'.$releaseId.'?album_type='.$album_type);?>">Submission</a>
						</li>
					</ul>
					<p class="border-bottom"></p>
					<?php
			       	echo error_message();
			       	echo form_open(current_url_query_string(),'id="release_submission" role="form"');
					?>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Release Information</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Title</b><p><?php echo $res['release_title'];?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Version/Subtitle</b><p><?php echo $res['version'];?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Primary Artist</b>
									<p><?php echo ($prim_artists) ? $prim_artists : 'NA' ;?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Production Year </b><p><?php echo $res['production_year'];?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Featuring </b>
									<p><?php echo ($release_featurings) ? $release_featurings : 'NA' ;?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Author </b>
									<p><?php echo ($authors) ? $authors : 'NA' ;?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Composer </b>
									<p><?php echo ($composers) ? $composers : 'NA' ;?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Arranger </b>
									<p><?php echo ($arrangers) ? $arrangers : 'NA' ;?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Producer </b>
									<p><?php echo ($producers) ? $producers : 'NA' ;?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">UPC/EAN </b><p><?php echo $res['upc_ean'];?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Producer Catalogue Number </b><p><?php echo $res['producer_catalogue'];?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Genre </b>
									<p><?php echo $genre_arr[$res['genre']];?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Sub Genre  </b>
									<p><?php echo $genre_arr[$res['genre']].' - '.$sub_genre_arr[$res['sub_genre']];?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Label Name </b>
									<p><?php echo $label_name;?></p>
								</div>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Price</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Main Price</b>
									<p><?php echo display_price($res['track_price']);?></p>
								</div>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Territories</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Your Release is Authorized in</b>
									<p><?php echo $total_territories;?> Territories</p>
								</div>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Release Date</p>
						<div class="p-3">
							<div class="row gx-0 gy-3">
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Physical/Original Release Date </b>
									<p><?php echo ($res['org_release_date']) ? getDateFormat($res['org_release_date'],1) : 'NA';?></p>
								</div>
								<div class="col-sm-6">
									<b class="fs-7 fw-semibold">Main Release Date </b>
									<p><?php echo ($res['main_release_date']) ? getDateFormat($res['main_release_date'],1) : 'NA';?> </p>
								</div>
                                
                                <?php /*
                                <div class="col-sm-3">
									<b class="fs-7 fw-semibold">State </b>
									<p><?php echo ($res['main_release_state']) ? $res['main_release_state'] : 'NA';?> </p>
								</div> */?>
							</div>
						</div>
					</div>
					<div class="mt-4 border rounded-3">
						<p class="fs-5 fw-medium bg-light p-3 rounded-3 text-uppercase">Assets</p>
						<div class="p-3">
							<div class="table-responsive">
								<div class="scrollbar style-4">
									<table class="table table-bordered mb-0 acc_table table-striped">
									 	<thead>
											<tr>
												<th>#</th>
												<th></th>
												<th>Asset(s)</th>
												<th>Artist(s)</th>
												<th>ISRC</th>
												<th>Price</th>
												<th>Edit all ISRC codes</th>
												<th>Action</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>1</td>
												<td><img src="<?php echo theme_url();?>images/music.svg" alt=""></td>
												<td><?php echo $res['release_title'];?></td>
												<td>
													<p class="fw-bold"><?php echo $res['track_title'];?></p>
													<p><?php echo ($prim_artists) ? $prim_artists : 'NA' ;?></p>
												</td>
												<td><?php echo $res['isrc'];?></td>
												<td><?php echo display_price($res['track_price']);?></td>
												<td>
													<a href="<?php echo site_url('admin/release/tracks/'.$releaseId.'?album_type='.$album_type);?>" class="btn btn-sm btn-purple <?php echo ($res['status']!='1')? ''  :'overlay_enable';?>">Details</a>
												</td>
												<td class="white_space">
													<?php 
													if($res['release_song']!='' && file_exists(UPLOAD_DIR.'/release/songs/'.$res['release_song'])){?>
										              	<a href="<?php echo base_url().'uploaded_files/release/songs/'.$res['release_song'];?>" title="Click to download" download> <img src="<?php echo theme_url();?>images/download.svg" alt="Download"></a>
									              		<?php 
									              	}
									              	$album_status_arr = $this->config->item('album_status_arr');
                          							echo '<p class="mt-1">Status : <span class="mt-1 text-danger fw-semibold">'.$album_status_arr[$res['status']].'</span></p>';
									              	?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<p class="mt-3">
						<button type="submit" name="action" class="btn btn-purple" value="Submit">Submit my Release</button>
					</p>
					<?php echo form_close(); ?>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>