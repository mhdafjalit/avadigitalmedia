<?php $this->load->view('top_application'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
/* Custom scrollbar styling */
.table-responsive {
    max-height: 400px;
    overflow-y: auto;
}

.table-responsive::-webkit-scrollbar {
    width: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Sticky table header */
.table-responsive thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #f8f9fa;
}

/* Tabs styling */
.nav-tabs .nav-link {
    color: #495057;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
}

.nav-tabs .nav-link.active {
    color: #6c5ce7;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

/* Artist section styling */
.artist-section {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}

.artist-section h5 {
    margin-bottom: 15px;
}

/* Song section styling */
.song-section {
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}

/* Required field indicator */
.required-field::after {
    content: " *";
    color: red;
}

/* Artist row styling */
.artist-row {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

/* Custom styles to override Select2 defaults */

.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 26px;
  position: absolute;
  top: 6px !important;
  right: 1px;
  width: 20px;
}
.select2-container--default .select2-selection--single {
	
	height: 38px;
	font-size: 1rem;
	font-weight: 400;
	
	color: var(--bs-body-color);
	-webkit-appearance: none;
	-moz-appearance: none;
	appearance: none;
	background-color: var(--bs-body-bg);
	background-clip: padding-box;
	border: var(--bs-border-width) solid var(--bs-border-color);
	border-radius: var(--bs-border-radius);
	transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
	line-height: 36px;
	color: #333;
	padding-left: 10px;
}
.file-upload-wrapper {
    position: relative;
    margin-bottom: 15px;
}

.file-upload-input {
    width: 100%;
    height: 38px;
    padding: 8px 12px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f8f9fa1c;
}

.file-upload-button {
    position: absolute;
    right: 0;
    top: 0;
    height: 38px;
    padding: 0 12px;
    background: #6c5ce7;
    color: white;
    border: none;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
}

.file-upload-button:hover {
    background: #5649c0;
}

.file-info {
    margin-top: 5px;
    font-size: 12px;
    color: #666;
}
</style>
<div class="dash_outer">
    <div class="dash_container">
        <?php $this->load->view('view_left_sidebar'); ?>
        <div id="main-content" class="h-100">
            <?php $this->load->view('view_top_sidebar');?>
            <div class="top_sec d-flex justify-content-between">
                <h1 class="mt-4">Manage Meta</h1>
            </div>
            
            <div class="main-content-inner">
              <?php validation_message();?>
                
                <?php 
                if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
                <?php 
                endif;
                
                if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Add New Meta</h5>
                        <?php echo form_open_multipart('', ['id' => 'addMetaForm']);?>
                        
                        <div class="form-section">
                            
                            <div class="row g-3">
                            <?php 
                                if(!empty($artists)){?>
                                    <div class="col-md-6">
                                        <label for="artists" class="form-label required-field">Artist</label>                                        
                                        <select class="form-select" id="artists" name="artists">
                                            <?php 
                                            foreach ($artists as $val){ ?>
                                                <option value="<?php echo $val['pdl_id']; ?>" <?php echo set_select('artists', $val['pdl_id']); ?>><?php echo $val['name']; ?></option>
                                            <?php 
                                            } ?>
                                        </select>                                       
                                    </div>                                    
                                    <?php 
                                }
                                
                           foreach ($meta_fields['artist1'] as $field => $props){ 
                                    $value = set_value("artist1[$field]"); 
                              ?>
                                    <div class="col-md-6">
                                        <label for="artist1_<?php echo $field; ?>" class="form-label <?php echo $props['required'] ? 'required-field' : ''; ?>"><?php echo $props['label']; ?></label>
                                        <input type="text" class="form-control" id="artist1_<?php echo $field; ?>" name="artist1[<?php echo $field; ?>]" value="<?php echo $value; ?>" <?php echo $props['required'] ? 'required' : ''; ?>>
                                        </div>
                                    <?php
                                  }
                                
                                foreach ($meta_fields['album'] as $field => $props){ 
                                    $value = set_value("album[$field]"); 
                                    ?>
                                    <div class="col-md-6">
                                        <label for="album_<?php echo $field; ?>" class="form-label <?php echo $props['required'] ? 'required-field' : ''; ?>"><?php echo $props['label']; ?></label>
                                        <?php 
                                        if ($field === 'album_type'){ ?>
                                            <select class="form-select" id="album_<?php echo $field; ?>" name="album[<?php echo $field; ?>]">
                                                <?php foreach ($enums['album_type'] as $option){ ?>
                                                    <option value="<?php echo $option; ?>" <?php echo set_select("album[$field]", $option); ?>><?php echo $option; ?></option>
                                                <?php 
                                                } ?>
                                            </select>  
                                        <?php 
                                        }
										if ($field === 'label'){ ?>
                                            <select class="form-select" id="label_<?php echo $field; ?>" name="album[<?php echo $field; ?>]">
                                                <?php foreach ($labels as $label){  ?>
                                                    <option value="<?php echo $label['channel_name']; ?>" <?php echo set_select("album[$field]", $label['channel_name']); ?>><?php echo $label['channel_name']; ?></option>
                                                <?php 
                                                } ?>
                                            </select>
                                        <?php 
                                        }else{ ?>
                                            <input type="text" class="form-control" id="album_<?php echo $field; ?>" name="album[<?php echo $field; ?>]" value="<?php echo $value; ?>" <?php echo $props['required'] ? 'required' : ''; ?>>
                                        <?php 
                                        }?>
                                    </div>
                                <?php 
                                }?>
                                
                                <?php 
                                foreach ($meta_fields['song'] as $field => $props){ 
                                    $value = set_value("song[$field]");
                                    ?>
                                    <div class="col-md-6">
                                        <label for="song_<?php echo $field; ?>" class="form-label <?php echo $props['required'] ? 'required-field' : ''; ?>"><?php echo $props['label']; ?></label>
                                        <?php if (in_array($field, ['language', 'album_type', 'content_type', 'genre', 'sub_genre', 'mood', 'parental_advisory', 'is_instrumental'])){ ?>
                                            <select class="form-select" id="song_<?php echo $field; ?>" name="song[<?php echo $field; ?>]" <?php echo $props['required'] ? 'required' : ''; ?>>
                                                <?php 
                                                if ($field === 'genre'){ ?>
                                                    <?php 
                                                    foreach ($enums['genre'] as $genre => $sub_genres){ ?>
                                                        <option value="<?php echo $genre; ?>" <?php echo set_select("song[$field]", $genre); ?>><?php echo $genre; ?></option>
                                                    <?php 
                                                    }?>
                                                <?php 
                                                }elseif ($field === 'sub_genre'){ ?>
                                                    <option value="">Select a sub genre</option>
                                                    <?php 
                                                    if (!empty($value)) {
                                                        echo "<option value=\"$value\" selected>$value</option>";
                                                    }
                                                    ?>
                                                <?php 
                                                }else{?>
                                                    <?php 
                                                    foreach ($enums[$field] as $option){ ?>
                                                        <option value="<?php echo $option; ?>" <?php echo set_select("song[$field]", $option); ?>><?php echo ucwords($option); ?></option>
                                                    <?php 
                                                    }
                                                }?>
                                            </select>
                                        <?php }elseif ($field === 'original_release_date_of_music' || $field === 'original_release_date_of_movie' || $field === 'go_live_date' || $field === 'date_of_expiry'){ ?>
                                            <input type="date" class="form-control" id="song_<?php echo $field; ?>" name="song[<?php echo $field; ?>]" value="<?php echo $value; ?>" <?php echo $props['required'] ? 'required' : ''; ?>>
                                        <?php }elseif ($field === 'track_duration'){ ?>
                                        <input type="time" class="form-control" step="1" id="song_<?php echo $field; ?>" name="song[<?php echo $field; ?>]" value="<?php echo $value; ?>" <?php echo $props['required'] ? 'required' : ''; ?>>
                                        <?php }elseif ($field === 'time_for_crbt_cut'){ ?>
                                            <input type="text" class="form-control" step="1" id="song_<?php echo $field; ?>" name="song[<?php echo $field; ?>]" value="<?php echo $value; ?>" <?php echo $props['required'] ? 'required' : ''; ?>>
                                        <?php }else{ ?>
                                            <input type="text" class="form-control" id="song_<?php echo $field; ?>" name="song[<?php echo $field; ?>]" value="<?php echo $field === 'album_name' ? set_value('album[name]', $this->input->post('album[name]')) : $value; ?>" <?php echo $props['required'] ? 'required' : ''; ?>>
                                        <?php 
                                        }
                                        if ($field === 'isrc'){
                                        ?>
                                            <small>Note: ISRC (e.g.: ITONB2000013) and Unique in data.</small>
                                        <?php 
                                        }
                                        ?>
                                    </div>
                                <?php
                                }?>
                            
                            <div class="row g-3 mt-3">
                                <div class="col-md-6">
                                    <label for="audio_file" class="form-label required-field">Audio File</label>
                                    <div class="file-upload-wrapper"> <!--accept="audio/*" required-->
                                        <input type="file" class="form-control file-upload-input" id="audio_file" name="audio_file">
                                        <div class="file-info" id="audio-file-info"></div>
                                        <small>[ ( File should be .mp3, .wav format and file size should not be more then 10 MB) ] </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="image_file" class="form-label required-field">Cover Art</label>
                                    <div class="file-upload-wrapper"><!--accept="image/*" required-->
                                        <input type="file" class="form-control file-upload-input" id="image_file" name="image_file">
                                        <div class="file-info" id="image-file-info"></div>
                                        <small>[ ( File should be .jpg, .png, .gif format and file size should not be more then 10 MB (10240 KB)) ( Best image size 3000 X 3000) ] </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <button type="submit" name="add_meta" value="1" class="btn btn-purple">
                                <i class="fas fa-plus"></i> Add Meta
                            </button>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('select').select2();    
    const enums = <?php echo json_encode($enums); ?>;

    $('#song_genre').on('change', function() {
        const genre = $(this).val();
        const subGenreSelect = $('#song_sub_genre');
        
        subGenreSelect.empty();
        subGenreSelect.append('<option value="">Select a sub genre</option>');
        
        if (genre && enums['genre'][genre]) {
            enums['genre'][genre].forEach(subGenre => {
                subGenreSelect.append(`<option value="${subGenre}">${subGenre}</option>`);
            });
        }
        
        // Preserve selected value if it exists
        const prevValue = "<?php echo set_value('song[sub_genre]'); ?>";
        if (prevValue) {
            subGenreSelect.val(prevValue).trigger('change');
        }
    });
    
    // Trigger change if genre has value
    if ($('#song_genre').val()) {
        $('#song_genre').trigger('change');
    }
    
    $('#album_name').on('change', function() {
        $('#song_album_name').val($(this).val());
    });
    
    $('#audio_file').on('change', function() {
        const file = this.files[0];
        if (file) {
            $('#audio-file-info').html(`Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
        }
    });
    
    $('#image_file').on('change', function() {
        const file = this.files[0];
        if (file) {
            $('#image-file-info').html(`Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`);
        }
    });
});
</script>
<?php $this->load->view("bottom_application"); ?>