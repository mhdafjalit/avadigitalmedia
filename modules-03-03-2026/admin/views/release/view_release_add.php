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
if(is_array($res) && !empty($res))
{
	$release_title	= set_value('release_title',$res['release_title']);
	$p_line			= set_value('p_line',$res['p_line']);
	$version		= set_value('version',$res['version']);
	$c_line			= set_value('c_line',$res['c_line']);
	$production_year = set_value('production_year',$res['production_year']);
	$upc_ean			= set_value('upc_ean',$res['upc_ean']);
	$is_various_artist	= (int) set_value('is_various_artist',$res['is_various_artist']);
	$genre				= set_value('genre',$res['genre']);
	$sub_genre			= set_value('sub_genre',$res['sub_genre']);
	$label_id			= set_value('label_id',$res['label_id']);
	$producer_catalogue	= set_value('producer_catalogue',$res['producer_catalogue']);
	$release_date	= set_value('release_date',$res['org_release_date']);
}
else
{
  	$release_title 	= set_value('release_title');
  	$p_line 		= set_value('p_line');
  	$version 		= set_value('version');
  	$c_line 		= set_value('c_line');
  	$production_year= set_value('production_year');
  	$upc_ean 		= set_value('upc_ean');
  	$is_various_artist= (int) set_value('is_various_artist');
  	$genre 			= set_value('genre');
  	$sub_genre 		= set_value('sub_genre');
  	$label_id 		= set_value('label_id');
  	$producer_catalogue = set_value('producer_catalogue');
  	$release_date 	= set_value('release_date');
}
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
							<a class="nav-link active" aria-current="page" href="<?php echo site_url('admin/release/new_release/'.$releaseId.'?album_type='.$album_type);?>">Release Information</a>
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
							<a class="nav-link" href="<?php echo site_url('admin/release/submission/'.$releaseId.'?album_type='.$album_type);?>">Submission</a>
						</li>
					</ul>
					<p class="border-bottom"></p>
					<?php
					echo error_message();
					echo form_open_multipart(current_url_query_string(),'name="release_frm" id="release_frm" autocomplete="off"');?>
					<div class="row g-0 mt-3">
						<div class="col-lg-8 order-2 order-lg-1">
							<div class="row g-3 mt-3">
								<div class="col-sm-6">
									<label class="form-label">Release Title * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
									<input type="text" class="form-control" name="release_title" id="release_title" value="<?php echo $release_title;?>">
									<?php echo form_error('release_title');?>
								</div>
								<div class="col-sm-6">
									<label class="form-label"><span class="p_symb">P</span> Line * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
									<input type="text" class="form-control" name="p_line" id="p_line" value="<?php echo $p_line;?>">
									<?php echo form_error('p_line');?>
								</div>
								<div class="col-sm-6">
									<label for="Phone" class="form-label">Version/Subtitle <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
									<input type="text" class="form-control" name="version" id="version" value="<?php echo $version;?>">
									<?php echo form_error('version');?>
								</div>
								<div class="col-sm-6">
									<label class="form-label">&copy; Line * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
									<input type="text" class="form-control" name="c_line" id="c_line" value="<?php echo $c_line;?>">
									<?php echo form_error('c_line');?>
								</div>
								<div class="col-sm-6">
									<div class="artist_area zx_root_parent_container">
										<?php
						         	 	$total_prim_artists = (!empty($primary_artists) && is_array($primary_artists))? count($primary_artists) : 0;
							          	$total_prim_artists = set_value('num_prim_artists_rows', $total_prim_artists==0 ? 1 : $total_prim_artists);
							          	?>
							            <input type="hidden" name="num_prim_artists_rows" class="num_clone_rows" value="<?php echo $total_prim_artists;?>" />
							            <label class="form-label">Primary Artist * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							            <div class="col-12 artist_sec" id="rows_container_1">
							              <?php  
							              for($i = 0;$i<$total_prim_artists;$i++) {?>
							              <div class="dy_dg mb-3 row zx_clone_child">
							                <div class="col-sm-10">
							                  <input type="text" class="form-control" name="primary_artists[]" value="<?php echo set_value('primary_artists[' . $i . ']',$primary_artists[$i]['primary_artist'] ?? ''); ?>">
							                  <?php echo form_error('primary_artists[' . $i . ']'); ?>
							                </div>
							                <div class="col-sm-2">
							                  <button type="submit" class="btn btn-sm btn-danger fs-4 lh-1 remove_rows <?php echo $total_prim_artists==1 ? 'd-none ' : '';?>">-</button>
							                </div>
							              </div>
							              <?php }?>
							            </div>
							            <div class="mt-2">
							              <button name="Button" type="button" class="btn btn-sm btn-dark fs-4 lh-1 zx_add_more" data-clone-container="rows_container_1" data-clone-row="zx_clone_child">+</button>
							            </div>
							  		</div>
							  	</div>
							  	<div class="col-sm-6">
									<label class="form-label">Production Year * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
									<select class="form-select" name="production_year" id="production_year">
										<option value="">Select</option>
										<?php
										for ($i=date('Y'); $i < date('Y')+10; $i++) { 
											echo '<option value="'.$i.'" '.(($production_year==$i) ? 'selected' : '').'>'.$i.'</option>';
										}?>
									</select>
									<?php echo form_error('production_year');?>
								</div>
							</div>
						</div>
						<div class="col-lg-4 ps-lg-3 pe-lg-3 order-1 order-lg-2">
							<div class="dtl_upload_img border-0 text-center">
								<div class="upload_thm2 m-auto">
									<?php
									if(isset($res['release_banner']) && $res['release_banner']!='' && file_exists(UPLOAD_DIR.'/release/'.$res['release_banner'])){
										$release_banner = base_url().'uploaded_files/release/'.$res['release_banner'];
									}else{
										$release_banner = theme_url().'images/no-img2.jpg';
									}?>
									<img id="documentUpload1" document-up="" src="<?php echo $release_banner;?>" alt="Image 1">
								</div>
								<input name="release_banner" id="release_banner" class="dg_custom_file" type="file" onchange="readURL(this,1);" accept="image/*">
								<p class="attach_btn mt-4"><a href="javascript:void(0)" class="text-primary fw-bold">Update Cover Picture</a></p>
								<p class="mt-2 fs-8 lh-base">[ <?php echo $this->config->item('release_banner.best.image.view');?> ]</p>
								<p class="clearfix"></p>
								<?php echo form_error('release_banner');?>
							</div>
						</div>
					</div>
					<div class="row g-3 mt-3">
						<div class="col-sm-6 col-lg-4">
							<div class="featuring_area zx_root_parent_container">
								<?php
				         	 	$total_featurings = (!empty($release_featurings) && is_array($release_featurings))? count($release_featurings) : 0;
					          	$total_featurings = set_value('num_featurings_rows',  $total_featurings==0 ? 1 : $total_featurings);
					          	?>
					            <input type="hidden" name="num_featurings_rows" class="num_clone_rows" value="<?php echo $total_featurings;?>" />
								<label class="form-label">Featuring <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
								<div class="feature_sec" id="rows_container_2">
									<?php  
					              	for($i = 0;$i<$total_featurings;$i++) {?>
					              	<div class="dy_dg mb-3 row zx_clone_child">
						                <div class="col-sm-10">
						                  <input type="text" class="form-control" name="featurings[]" value="<?php echo set_value('featurings[' . $i . ']',$release_featurings[$i]['featuring'] ?? ''); ?>">
						                  <?php echo form_error('featurings[' . $i . ']'); ?>
						                </div>
						                <div class="col-sm-2">
						                  <button type="submit" class="btn btn-sm btn-danger fs-4 lh-1 remove_rows <?php echo $total_featurings==1 ? 'd-none ' : '';?>">-</button>
						                </div>
					             	 </div>
					              	<?php }?>
								</div>
								<div class="mt-2">
					              <button name="Button" type="button" class="btn btn-sm btn-dark fs-4 lh-1 zx_add_more" data-clone-container="rows_container_2" data-clone-row="zx_clone_child">+</button>
					            </div>
							</div>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">UPC/EAN <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<input name="upc_ean" id="upc_ean" type="text" class="form-control" value="<?php echo $upc_ean;?>">
							<?php echo form_error('upc_ean');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Producer Catalogue Number *</label>
							<input type="text" class="form-control" name="producer_catalogue" id="producer_catalogue" value="<?php echo $producer_catalogue;?>">
							<?php echo form_error('producer_catalogue');?>
						</div>
						<div class="col-12">
							<label><input type="checkbox" name="is_various_artist" value="1" <?php echo ($is_various_artist==1)? 'checked' : '';?>> Various Artist</label>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Genre *</label>
							<select class="form-select" name="genre" id="genre">
								<option value="">Select</option>
								<?php
								if(is_array($genre_arr) && !empty($genre_arr)){
									foreach ($genre_arr as $gkey => $gval) {
										echo '<option value="'.$gkey.'" '.(($genre ==$gkey)? 'selected' : '').'>'.$gval.'</option>';
									}
								}?>
							</select>
							<?php echo form_error('genre');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Sub Genre *</label>
							<select class="form-select" name="sub_genre" id="sub_genre">
								<option value="">Select</option>
								<?php
								if(is_array($sub_genre_arr) && !empty($sub_genre_arr)){
									foreach ($sub_genre_arr as $sgkey => $sgval) {
										echo '<option value="'.$sgkey.'" '.(($sub_genre ==$sgkey)? 'selected' : '').'>'.$sgval.'</option>';
									}
								} ?>
							</select>
							<?php echo form_error('sub_genre');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Label Name * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<select class="form-select" name="label_id" id="label_id">
				                <option value="">Select</option>
				                <?php
				                if(is_array($res_labels) && !empty($res_labels)){
				                  foreach($res_labels as $lkey=>$lv){
				                    echo '<option value="'.$lv['label_id'].'" '.(($label_id==$lv['label_id'])? 'selected' : '').'>'.$lv['channel_name'].'</option>';
				                  }
				                }?>
			              	</select>
			              	<?php echo form_error('label_id');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Physical/Original Release Date * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<input type="text" name="release_date" id="release_date" class="form-control release_date1" value="<?php echo $release_date;?>" readonly="readonly">
							<?php echo form_error('release_date');?>
						</div>
					</div>
					<div class="mt-3">
						<input type="hidden" name="album_type" value="<?php echo $album_type;?>">
						<input name="action" type="submit" class="btn btn-purple" value="Submit">
					</div>
					<?php echo form_close();?>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>
	function readURL(input, option) {
	  if (input.files && input.files[0]) {
	    var reader = new FileReader();
	    reader.onload = function(e) {
	      $("#documentUpload" + option).attr('src', e.target.result);
	    };
	    reader.readAsDataURL(input.files[0]);
	  }
	}
	$('.attach_btn').click(function() {
	  $(this).prev().trigger('change');
	});

	$('.dg_custom_file').change(function() {
	  $(this).parent().children('b.file_url').text($(this).attr('value'));
	});
	$(document).ready(function() {
	    $(document).on('click', '.release_date', function(e) {
	        e.preventDefault();
	        $('.release_date1:eq(0)').focus();
	    });
	    $(document).on('focus', '.release_date1', function() {
	        $(this).datepicker({
	            showOn: "focus",
	            dateFormat: 'yy-mm-dd',
	            changeMonth: true,
	            changeYear: true,
	            defaultDate: 'y',
	            minDate: new Date(1980, 0, 1),
	            maxDate: '+3y',
	            yearRange: "1980:c+50",
	            onSelect: function(dateText) {
	                $('.release_date1').val(dateText);
	            }
	        });
	    });
	});
</script>
<?php $this->load->view('release/dynamic_form_blocks_js');?>
<?php $this->load->view("bottom_application");?>