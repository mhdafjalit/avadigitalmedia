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
	$feature_artist = set_value('feature_artist',$res['feature_artist']);
	$upc_ean			= set_value('upc_ean',$res['upc_ean']);
	$is_various_artist	= (int) set_value('is_various_artist',$res['is_various_artist']);
	$genre				= set_value('genre',$res['genre']);
	$sub_genre			= set_value('sub_genre',$res['sub_genre']);
	$label_name			= set_value('label_name',$res['label_name']);
	$producer_catalogue	= set_value('producer_catalogue',$res['producer_catalogue']);
	$go_live_date	= set_value('go_live_date',$res['go_live_date']);
}
else
{
  	$release_title 	= set_value('release_title');
  	$p_line 		= set_value('p_line');
  	$version 		= set_value('version');
  	$c_line 		= set_value('c_line');
  	$production_year= set_value('production_year');
  	$feature_artist = set_value('feature_artist');
  	$upc_ean 		= set_value('upc_ean');
  	$is_various_artist= (int) set_value('is_various_artist');
  	$genre 			= set_value('genre');
  	$sub_genre 		= set_value('sub_genre');
  	$label_name 		= set_value('label_name');
  	$producer_catalogue = set_value('producer_catalogue');
  	$go_live_date 	= set_value('go_live_date');
}?>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
.select2-container .select2-selection--single {
    box-sizing: border-box;cursor: pointer;display: block;height:36px;user-select: none;-webkit-user-select: none;
}
</style>
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
                        <input type="hidden" name="releaseId" value="<?= $releaseId; ?>">
					<div class="row g-0 mt-3">
						<div class="col-lg-8 order-2 order-lg-1">
							<div class="row g-3 mt-3">
								<div class="col-sm-6">
									<label class="form-label">Release Title * <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Lorem ipsum dolor sit amet consectetur adipiscing elit"></label>
									<input type="text" class="form-control" name="release_title" id="release_title" value="<?= $release_title;?>">
									<?php echo form_error('release_title');?>
								</div>
								<div class="col-sm-6">
									<label class="form-label"><span class="p_symb">P</span> Line  <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Phonogram Line"></label>
									<input type="text" class="form-control" name="p_line" id="p_line" value="<?= $p_line;?>">
									<?php echo form_error('p_line');?>
								</div>
								<div class="col-sm-6">
									<label for="Phone" class="form-label">Version/Subtitle <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Title Version or Track Version"></label>
									<input type="text" class="form-control" name="version" id="version" value="<?= $version;?>">
									<?php echo form_error('version');?>
								</div>
								<div class="col-sm-6">
									<label class="form-label">&copy; Line  <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Copyright Line"></label>
									<input type="text" class="form-control" name="c_line" id="c_line" value="<?= $c_line;?>">
									<?php echo form_error('c_line');?>
								</div>
                               

                                <div class="col-md-6">
    <label for="artists" class="form-label required-field">Artist *</label>
    <select class="form-select zx_select" id="artist_name" name="artist_name">
        <option value="">Select Artist</option>
        <?php 
        if(is_array($artists) && !empty($artists)){
            foreach ($artists as $val){ 
                // Check if this artist is selected (for edit page)
                $selected = '';
                if(!empty($res['artist_name']) && $res['artist_name'] == $val['pdl_id']) {
                    $selected = 'selected="selected"';
                }
                // Also check for form validation set_select (for form reload after error)
                if(set_select('artist_name', $val['pdl_id']) != '') {
                    $selected = 'selected="selected"';
                }
                ?>
                <option value="<?= $val['pdl_id']; ?>" <?= $selected; ?>><?= $val['name']; ?></option>
            <?php 
            } 
        }?>
    </select> 
    <?php echo form_error('artist_name'); ?>                                      
</div>
	                            <div class="col-sm-6">
									<label class="form-label">Feature Artist </label>
									<input type="text" class="form-control" name="feature_artist" value="<?= $feature_artist; ?>">
				                  <?php echo form_error('feature_artist'); ?>
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
							<label class="form-label">Production Year * <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Your production year of music"></label>
							<select class="form-select" name="production_year" id="production_year">
								<option value="">Select</option>
								<?php
								for ($i=date('Y'); $i < date('Y')+10; $i++) { 
									echo '<option value="'.$i.'" '.(($production_year==$i) ? 'selected' : '').'>'.$i.'</option>';
								}?>
							</select>
							<?php echo form_error('production_year');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">UPC/EAN <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="A UPC (Universal Product Code) or EAN (European Article Number) is a unique 12 or 13-digit barcode identifier for a music release"></label>
							<input name="upc_ean" id="upc_ean" type="text" class="form-control" value="<?= $upc_ean;?>">
							<?php echo form_error('upc_ean');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Producer Catalogue Number </label>
							<input type="text" class="form-control" name="producer_catalogue" id="producer_catalogue" value="<?php echo $producer_catalogue;?>">
							<?php echo form_error('producer_catalogue');?>
						</div>
						<div class="col-12">
							<label><input type="checkbox" name="is_various_artist" value="1" <?= ($is_various_artist==1)? 'checked' : '';?>> Various Artist</label>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Genre *</label>
							<select class="form-select zx_select" name="genre" id="genre">
								<option value="">Select</option>
								<?php
								if(is_array($genre_arr) && !empty($genre_arr)){
								    foreach ($genre_arr as $gkey => $gval) {
								        echo '<option value="'.$gkey.'" '.(($genre == $gkey)? 'selected' : '').'>'.$gkey.'</option>';
								    }
								}?>
							</select>
							<?php echo form_error('genre');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Sub Genre *</label>
							<select class="form-select zx_select" name="sub_genre" id="sub_genre">
								<option value="">Select</option>
							</select>
							<?php echo form_error('sub_genre');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Label Name * &nbsp;&nbsp; <a href="<?= site_url('admin/add_label');?>" title="you can add more labels from here"><span class="badge text-bg-primary">Add Label</span></a></label>
							<select class="form-select zx_select" name="label_name" id="label_name">
				                <option value="">Select</option>
				                <?php
				                if(is_array($res_labels) && !empty($res_labels)){
				                  foreach($res_labels as $lkey=>$lv){
				                    echo '<option value="'.$lv['channel_name'].'" '.(($label_name==$lv['channel_name'])? 'selected' : '').'>'.$lv['channel_name'].'</option>';
				                  }
				                }?>
			              	</select>
			              	<?php echo form_error('label_name');?>
						</div>
						<div class="col-sm-6 col-lg-4">
							<label class="form-label">Physical/Live Release Date * <img src="<?= theme_url();?>images/que.svg" alt="" data-bs-toggle="tooltip" data-bs-placement="top" title="Your Physical/Live release date of music"></label>
							<input type="text" name="go_live_date" id="go_live_date" class="form-control release_date1" value="<?php echo $go_live_date;?>" readonly="readonly">
							<?php echo form_error('go_live_date');?>
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
	$(document).ready(function() {
		$('.zx_select').select2();  
	});
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
<script>
var genreData = <?php echo json_encode($genre_arr); ?>;
var selectedSubGenre = "<?php echo isset($sub_genre) ? $sub_genre : ''; ?>";
$(function(){
    $('#genre').on('change', function(){
        var sub = $('#sub_genre').html('<option value="">Select</option>');
        var val = $(this).val();
        if(val && genreData[val]){
            $.each(genreData[val], function(i, v){
                sub.append('<option value="'+v+'">'+v+'</option>');
            });
            if(selectedSubGenre) sub.val(selectedSubGenre);
        }
    }).trigger('change');
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<?php $this->load->view("bottom_application");?>