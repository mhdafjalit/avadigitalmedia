<?php 
	$this->load->view('top_application'); 
	$bdcm_array = array(
		array('heading'=>'Release','url'=>'admin/release'),
		array('heading'=>'Dashboard','url'=>'admin')
	);
$album_type = (int) $this->input->get_post('album_type');
$releaseId = isset($res['release_id']) ? md5($res['release_id']) : (null !== ($segment = $this->uri->segment(4)) ? $segment : null);
$prim_track_types 	= $this->config->item('prim_track_types');
$lang_arr 			= $this->config->item('lang_arr');
$prim_artists 		= get_prim_artists($res['release_id']);
$release_featurings = get_release_featurings($res['release_id']);
if(is_array($res) && !empty($res))
{
	$prim_track_type = set_value('prim_track_type',$res['prim_track_type']);
	$content_type = (int) set_value('content_type',$res['content_type']);
	$is_instrumental = set_value('is_instrumental',$res['is_instrumental']);
	$track_title = set_value('track_title',$res['track_title']);
	$producer_catalogue = set_value('producer_catalogue',$res['producer_catalogue']);
	$preview_start 	= set_value('preview_start',$res['preview_start']);
	$isrc 			= set_value('isrc',$res['isrc']);
	$is_isrc 		= set_value('is_isrc',$res['is_isrc']);
	$publisher	 	= set_value('publisher',$res['publisher']);
	$lyrics_lang 	= set_value('lyrics_lang',$res['lyrics_lang']);
	$lyrics 		= set_value('lyrics',$res['lyrics']);
	$track_title_lang = set_value('track_title_lang',$res['track_title_lang']);
	$track_price 	= set_value('track_price',$res['track_price']);
}
else
{
  	$prim_track_type 	= set_value('prim_track_type');
  	$content_type 		= (int) set_value('content_type');
  	$is_instrumental 	= set_value('is_instrumental');
  	$track_title 		= set_value('track_title');
  	$producer_catalogue = set_value('producer_catalogue');
  	$preview_start 		= set_value('preview_start');
  	$isrc 				= set_value('isrc');
  	$is_isrc 			= set_value('is_isrc');
  	$publisher 			= set_value('publisher');
  	$lyrics_lang 		= set_value('lyrics_lang');
  	$lyrics 			= set_value('lyrics');
  	$track_title_lang 	= set_value('track_title_lang');
  	$track_price 		= set_value('track_price');
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
							<a class="nav-link" href="<?php echo site_url('admin/release/new_release/'.$releaseId.'?album_type='.$album_type);?>">Release Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo site_url('admin/release/upload_release/'.$releaseId.'?album_type='.$album_type);?>">Upload</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="<?php echo site_url('admin/release/tracks/'.$releaseId.'?album_type='.$album_type);?>">Tracks</a>
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
					echo form_open(current_url_query_string(),'name="track_frm" id="track_frm" autocomplete="off"');?>
					<div class="row g-3 mt-1">
						<div class="col-lg-6">
							<label for="Phone" class="form-label">Title <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<input type="text" class="form-control" name="track_title" id="track_title" value="<?php echo $track_title;?>">
							<?php echo form_error('track_title');?>
						</div>
						<div class="col-lg-6">
							<label class="form-label d-block">Primary Track Type *</label>
							<?php
				            if(is_array($prim_track_types) && !empty($prim_track_types)){
				              foreach($prim_track_types as $ptkey=>$ptt){
				                echo '<input type="radio" name="prim_track_type" class="btn-check" id="add'.$ptkey.'" value="'.$ptkey.'" '.(($prim_track_type==$ptkey)? 'checked' : '').' autocomplete="off">
				                <label class="btn btn-sm btn-outline-dark mb-1" for="add'.$ptkey.'">'.$ptt.'</label>';
				              }
				            }?>
				            <?php echo form_error('prim_track_type');?>
						</div>
						<div class="col-lg-6">
							<label class="form-label d-block">Instrumental *</label>
							<input type="radio" class="btn-check" name="is_instrumental" id="Yes" value="1" <?php echo (($is_instrumental==1)? 'checked' : '');?> autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="Yes">Yes</label>
							<input type="radio" class="btn-check" name="is_instrumental" id="No" value="0" <?php echo (($is_instrumental==0)? 'checked' : '');?> autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="No">No</label>
							<?php echo form_error('is_instrumental');?>
						</div>
						<div class="col-lg-6">
							<div class="artist_area">
								<div class="artist_sec">
									<label class="form-label">Primary Artist * </label><br>
									<p class="fw-semibold"><?php echo ($prim_artists) ? $prim_artists : 'NA' ;?></p>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="featuring_area">
								<div class="feature_sec">
									<label class="form-label">Featuring * </label><br>
									<p class="fw-semibold"><?php echo ($release_featurings) ? $release_featurings : 'NA' ;?></p>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="author_area zx_root_parent_container">
								<?php
				         	 	$total_authors = (!empty($authors) && is_array($authors))? count($authors) : 0;
					          	$total_authors = set_value('num_authors_rows',$total_authors==0 ? 1 : $total_authors);
					          	?>
					            <input type="hidden" name="num_authors_rows" class="num_clone_rows" value="<?php echo $total_authors;?>" />
					            <div class="d-sm-flex justify-content-between">
									<label class="form-label d-block">Author / Writer *</label>
									<small>Digital music stores require full first and last (family) name</small>
								</div>
					            <div class="col-12 author_sec" id="rows_container_1">
					              <?php  
					              for($i = 0;$i<$total_authors;$i++) {?>
					              <div class="dy_dg mb-3 row zx_clone_child">
					                <div class="col-sm-10">
					                  <input type="text" class="form-control" name="authors[]" value="<?php echo set_value('authors[' . $i . ']',$authors[$i]['author'] ?? ''); ?>">
					                  <?php echo form_error('authors[' . $i . ']'); ?>
					                </div>
					                <div class="col-sm-2">
					                  <button type="submit" class="btn btn-sm btn-danger fs-4 lh-1 remove_rows <?php echo $total_authors==1 ? 'd-none ' : '';?>">-</button>
					                </div>
					              </div>
					              <?php }?>
					            </div>
					            <div class="mt-2">
					              <button name="Button" type="button" class="btn btn-sm btn-dark fs-4 lh-1 zx_add_more" data-clone-container="rows_container_1" data-clone-row="zx_clone_child">+</button>
					            </div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="composer_area zx_root_parent_container">
								<?php
				         	 	$total_composers = (!empty($composers) && is_array($composers))? count($composers) : 0;
					          	$total_composers = set_value('num_composers_rows',$total_composers==0 ? 1 : $total_composers);
					          	?>
					            <input type="hidden" name="num_composers_rows" class="num_clone_rows" value="<?php echo $total_composers;?>" />
					            <div class="d-sm-flex justify-content-between">
									<label class="form-label d-block">Composer * </label>
									<small>Digital music stores require full first and last (family) name</small>
								</div>
					            <div class="col-12 composer_sec" id="rows_container_2">
					              <?php  
					              for($i = 0;$i<$total_composers;$i++) {?>
					              <div class="dy_dg mb-3 row zx_clone_child">
					                <div class="col-sm-10">
					                  <input type="text" class="form-control" name="composers[]" value="<?php echo set_value('composers[' . $i . ']',$composers[$i]['composer'] ?? ''); ?>">
					                  <?php echo form_error('composers[' . $i . ']'); ?>
					                </div>
					                <div class="col-sm-2">
					                  <button type="submit" class="btn btn-sm btn-danger fs-4 lh-1 remove_rows <?php echo $total_composers==1 ? 'd-none ' : '';?>">-</button>
					                </div>
					              </div>
					              <?php }?>
					            </div>
					            <div class="mt-2">
					              <button name="Button" type="button" class="btn btn-sm btn-dark fs-4 lh-1 zx_add_more" data-clone-container="rows_container_2" data-clone-row="zx_clone_child">+</button>
					            </div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="arranger_area zx_root_parent_container">
								<?php
				         	 	$total_arrangers = (!empty($arrangers) && is_array($arrangers))? count($arrangers) : 0;
					          	$total_arrangers = set_value('num_arrangers_rows',$total_arrangers==0 ? 1 : $total_arrangers);
					          	?>
					            <input type="hidden" name="num_arrangers_rows" class="num_clone_rows" value="<?php echo $total_arrangers;?>" />
					            <label class="form-label">Arranger </label>
					            <div class="col-12 arranger_sec" id="rows_container_3">
					              <?php  
					              for($i = 0;$i<$total_arrangers;$i++) {?>
					              <div class="dy_dg mb-3 row zx_clone_child">
					                <div class="col-sm-10">
					                  <input type="text" class="form-control" name="arrangers[]" value="<?php echo set_value('arrangers[' . $i . ']',$arrangers[$i]['arranger'] ?? ''); ?>">
					                  <?php echo form_error('arrangers[' . $i . ']'); ?>
					                </div>
					                <div class="col-sm-2">
					                  <button type="submit" class="btn btn-sm btn-danger fs-4 lh-1 remove_rows <?php echo $total_arrangers==1 ? 'd-none ' : '';?>">-</button>
					                </div>
					              </div>
					              <?php }?>
					            </div>
					            <div class="mt-2">
					              <button name="Button" type="button" class="btn btn-sm btn-dark fs-4 lh-1 zx_add_more" data-clone-container="rows_container_3" data-clone-row="zx_clone_child">+</button>
					            </div>
							</div>
						</div>

						<div class="col-lg-6">
							<div class="producer_area zx_root_parent_container">
								<?php
				         	 	$total_producers = (!empty($producers) && is_array($producers))? count($producers) : 0;
					          	$total_producers = set_value('num_producers_rows',$total_producers==0 ? 1 : $total_producers);
					          	?>
					            <input type="hidden" name="num_producers_rows" class="num_clone_rows" value="<?php echo $total_producers;?>" />
					            <label class="form-label">Producer * </label>
					            <div class="col-12 producer_sec" id="rows_container_4">
					              <?php  
					              for($i = 0;$i<$total_producers;$i++) {?>
					              <div class="dy_dg mb-3 row zx_clone_child">
					                <div class="col-sm-10">
					                  <input type="text" class="form-control" name="producers[]" value="<?php echo set_value('producers[' . $i . ']',$producers[$i]['producer'] ?? ''); ?>">
					                  <?php echo form_error('producers[' . $i . ']'); ?>
					                </div>
					                <div class="col-sm-2">
					                  <button type="submit" class="btn btn-sm btn-danger fs-4 lh-1 remove_rows <?php echo $total_producers==1 ? 'd-none ' : '';?>">-</button>
					                </div>
					              </div>
					              <?php }?>
					            </div>
					            <div class="mt-2">
					              <button name="Button" type="button" class="btn btn-sm btn-dark fs-4 lh-1 zx_add_more" data-clone-container="rows_container_4" data-clone-row="zx_clone_child">+</button>
					            </div>
							</div>
						</div>
						<div class="col-lg-6">
							<label class="form-label">Publisher</label>
							<input type="text" class="form-control" name="publisher" id="publisher" value="<?php echo $publisher;?>">
							<?php echo form_error('publisher');?>
						</div>
						<div class="col-lg-6">
							<label class="form-label">ISRC <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<input type="text" class="form-control" name="isrc" id="isrc" value="<?php echo $isrc;?>">
							<?php echo form_error('isrc');?>
						</div>
						<div class="col-lg-6">
							<label class="form-label d-block">Ask to Generate an ISRC *</label>
							<input type="radio" class="btn-check" name="is_isrc" id="isrcYes" <?php echo (($is_isrc==1)? 'checked' : '');?> autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="isrcYes">Yes</label>
							<input type="radio" class="btn-check" name="is_isrc" id="isrcNo" <?php echo (($is_isrc==0)? 'checked' : '');?>  autocomplete="off">
							<label class="btn btn-sm btn-outline-dark" for="isrcNo">No</label>
						</div>
						<div class="col-lg-6">
							<label class="form-label">Price <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<input type="text" class="form-control" name="track_price" id="track_price" value="<?php echo $track_price;?>">
							<?php echo form_error('track_price');?>
						</div>
						<div class="col-lg-6">
							<label class="form-label">Producer Catalogue Number</label>
							<input type="text" class="form-control" name="producer_catalogue" id="producer_catalogue" value="<?php echo $producer_catalogue;?>" readonly>
							<?php echo form_error('producer_catalogue');?>
						</div>
						<div class="col-lg-6">
							<label class="form-label">Preview Start <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<input type="text" class="form-control" name="preview_start" id="preview_start" value="<?php echo $preview_start;?>">
							<?php echo form_error('preview_start');?>
						</div>
						<div class="col-lg-6">
							<label class="form-label">Track Title Language * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<select class="form-select" name="track_title_lang" id="track_title_lang">
								<option value="">Select</option>
								<?php
								if(is_array($lang_arr) && !empty($lang_arr)){
									foreach($lang_arr as $lkey=>$lval){
										echo '<option value="'.$lkey.'" '.(($track_title_lang==$lkey)? 'selected' : '').'>'.$lval.'</option>';
									}
								}?>
							</select>
							<?php echo form_error('track_title_lang');?>
						</div>
						<div class="col-lg-6">
							<label class="form-label">Lyrics Language * <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<select class="form-select" name="lyrics_lang" id="lyrics_lang">
								<option value="">Select</option>
								<?php
								if(is_array($lang_arr) && !empty($lang_arr)){
									foreach($lang_arr as $lkey=>$lval){
										echo '<option value="'.$lkey.'" '.(($lyrics_lang==$lkey)? 'selected' : '').'>'.$lval.'</option>';
									}
								}?>
							</select>
							<?php echo form_error('lyrics_lang');?>
						</div>
						<div class="col-12">
							<label class="form-label">Lyrics  <img src="<?php echo theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
							<textarea name="lyrics" id="lyrics" rows="4" class="form-control"><?php echo $lyrics;?></textarea>
							<?php echo form_error('lyrics');?>
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
<?php $this->load->view('release/dynamic_form_blocks_js');?>
<?php $this->load->view("bottom_application");?>