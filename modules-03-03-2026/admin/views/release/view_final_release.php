<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
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
				<div>
					<div>
						<div class="dash_box p-4">
							<p class="fs-5 fw-bold"><?php echo $res['release_title'];?></p>
							<div class="mt-4 d-flex">
								<div>
									<?php
									if($res['release_banner']!='' && file_exists(UPLOAD_DIR.'/release/'.$res['release_banner'])){
										$release_banner = base_url().'uploaded_files/release/'.$res['release_banner'];
										echo '<img src="'.$release_banner.'" width="100" height="100" alt="" class="rounded-3">';
									}?>
								</div>
								<div class="ms-3 fs-7">
									<p class="mb-2"><b>Release Title:</b> <?php echo $res['release_title'];?></p>
									<p class="mb-2"><b>Primary Artist:</b> <?php echo ($prim_artists) ? $prim_artists : 'NA' ;?></p>
									<p class="mb-2"><b>Label Name:</b> <?php echo $label_name;?></p>
									<p class="mb-2"><b>Genre:</b> <?php echo $genre_arr[$res['genre']].' - '.$sub_genre_arr[$res['sub_genre']];?></p>
									<p class="mb-2"><b>Main Price Tier:</b> <?php echo display_price($res['track_price']);?></p>
									<p class="mb-2"><b>Physical/Original Release Date:</b> <?php echo getDateFormat($res['org_release_date'],1);?></p>
									<p class="mb-2"><b>Production Year:</b> <?php echo $res['production_year'];?></p>
								</div>
							</div>
							<div class="mt-5 fin_box text-white rounded-3 p-4">
								<b Class="fw-semibold d-inline-block me-2">Share a Link</b> 
								<div class="share_w d-inline-block bg-white p-1 rounded-3">
									<button class="share-to btn btn_fb" data-with="facebook">
									<img src="<?php echo theme_url();?>images/facebook-f.svg" width="18" height="18" title="" alt="" decoding="async" fetchpriority="low" loading="lazy"> </button>
									<button class="share-to btn btn_in" data-with="linkedin">
									<img src="<?php echo theme_url();?>images/linkedin-in.svg" width="18" height="18" title="" alt="" decoding="async" fetchpriority="low" loading="lazy"> </button>
									<button class="share-to btn btn_insta" data-with="instagram">
									<img src="<?php echo theme_url();?>images/instagram.svg" width="18" height="18" title="" alt="" decoding="async" fetchpriority="low" loading="lazy"> </button>
									<button class="share-to btn btn_whts" data-with="whatsapp">
									<img src="<?php echo theme_url();?>images/whatsap.svg" width="18" height="18" title="" alt="" decoding="async" fetchpriority="low" loading="lazy"> </button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<?php $this->load->view("bottom_application");?>