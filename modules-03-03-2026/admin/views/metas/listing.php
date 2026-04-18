<?php $this->load->view('top_application');
$pdl_release_platform = $this->config->item('pdl_release_platform');
$t=@$this->input->get_post('t');
$parm = ($t!='')?'?t='.$t:''
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
/* Custom scrollbar styling */
.table-responsive {
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

/* Required field indicator */
.required-field::after {
    content: " *";
    color: red;
}

/* Action buttons */
.btn-action {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
}

/* Status badges */
.badge-pending {
    background-color: #ffc107;
    color: #212529;
}

.badge-approved {
    background-color: #28a745;
    color: white;
}

.badge-rejected {
    background-color: #dc3545;
    color: white;
}

/* Search and filter section */
.search-filter {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

/* Pagination styling */
.pagination .page-item.active .page-link {
    background-color: #6c5ce7;
    border-color: #6c5ce7;
}

.pagination .page-link {
    color: #6c5ce7;
}

/* Responsive table cells */
@media (max-width: 768px) {
    .table-responsive td, 
    .table-responsive th {
        white-space: nowrap;
    }
}

/* Custom select2 styling */
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
}

/* Audio player styles */
.album-image-wrapper {
    cursor: pointer;
    transition: transform 0.3s ease;
    position: relative;
    width: 100px;
    height: 100px;
}

.album-image-wrapper:hover {
    transform: scale(1.05);
}

.play-button-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(0,0,0,0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    font-size: 2rem;
}

.album-image-wrapper:hover .play-button-overlay {
    opacity: 1;
}

.no-image-placeholder {
    color: #999;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.no-image-placeholder:hover {
    background-color: #f5f5f5;
}

.now-playing {
    position: relative;
}

.now-playing::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(108, 92, 231, 0.3);
    border: 2px solid #6c5ce7;
    border-radius: 4px;
}

.audio-player-container {
    display: inline-block;
}
</style>

<div class="dash_outer">
    <div class="dash_container">
        <?php $this->load->view('view_left_sidebar'); ?>
        <div id="main-content" class="h-100">
        <?php $this->load->view('view_top_sidebar');?>
            <div class="top_sec d-flex justify-content-between align-items-center">
                <h1 class="mt-4"><?php echo $heading_title;?></h1>
                <a href="<?php echo base_url('admin/metas/add'); ?>" class="btn btn-purple">
                    <i class="fas fa-plus"></i> Add New Meta
                </a>
            </div>
            
            <div class="main-content-inner">
                <?php validation_message();?>
                <?php 
                if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?php echo $this->session->flashdata('success'); ?></div>
                <?php endif;
                
                if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
                <?php endif; ?>
                
                <!-- Search and Filter Section -->
                <div class="search-filter">
                    <?php echo form_open('admin/metas'.$parm, ['method' => 'get']); ?>
                    <input type="hidden" name="t" value="<?php echo $t;?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="keyword" 
                                   value="<?php echo $this->input->get('search'); ?>" placeholder="Search by title...">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="is_verify_meta">
                                <option value="">All Status</option>
                                <option value="1" <?php echo $this->input->get('is_verify_meta') == '1' ? 'selected' : ''; ?>>Verified</option>
                                <option value="0" <?php echo $this->input->get('is_verify_meta') == '0' ? 'selected' : ''; ?>>Un Verified</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="artist" class="form-label">Label</label>
                            <select class="form-select select2" id="label" name="label">
                                <option value="">All Labels</option>
                                <?php foreach ($labels as $label){ ?>
                                    <option value="<?php echo $label['channel_name']; ?>" 
                                        <?php echo $this->input->get('label') == $label['channel_name'] ? 'selected' : ''; ?>>
                                        <?php echo $label['channel_name']; ?>
                                    </option>
                                <?php 
								}?>
                            </select>
                        </div>
                        <?php /*
                        <div class="col-md-3">
                            <label for="artist" class="form-label">Artist</label>
                            <select class="form-select select2" id="artist" name="artist">
                                <option value="">All Artists</option>
                                <?php foreach ($artists as $artist){ ?>
                                    <option value="<?php echo $artist['name']; ?>" 
                                        <?php echo $this->input->get('artist') == $artist['name'] ? 'selected' : ''; ?>>
                                        <?php echo $artist['name']; ?>
                                    </option>
                                <?php 
								}?>
                            </select>
                        </div>
                        */?>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-purple me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="<?php echo base_url('admin/metas'.$parm); ?>" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
                
                <!-- Meta Listing Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="2%">#</th>
                                        <th width="8%">Album</th>
                                        <th width="23%">Title</th>
                                        <th width="10%">Artist</th>
                                        <th width="10%">Label</th>
                                        <th width="10%">ISRC</th>
                                        <th width="10%">Go Live Date</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Created At</th>
                                        <th width="17%">Actions</th>
                                    </tr>                                    
                                </thead>
                                <tbody>
                                    <?php
                                    if (is_array($metas) && !empty($metas)){
										foreach ($metas as $index => $meta){ 
										
										$per_page = $this->config->item('per_page');
										$page = $this->input->get('offset') ?? 0;
										$offset = ($page > 0) ? ($page - 1) * $per_page : 0;
										?>
                                            <tr>
                                                <td><?php echo $index + 1 + $offset; ?></td>
                                                <td>
                                                                <img src="<?php echo get_image('release/songs', $meta['album_image'], 100, 100, 'AR'); ?>" 
                                                                     alt="Album Cover" 
                                                                     class="mw-100 mh-100">
                                                                
                                                            
                                                </td>
                                                <td><?php echo html_escape($meta['album_name']); ?></td>
                                                <td><?php echo html_escape($meta['artist_name']); ?></td>
                                                <td><?php echo $meta['label'] ?? 'NA'; ?></td>
                                                <td><?php echo html_escape($meta['isrc']); ?></td>
                                                <td>
												<?php echo !empty($meta['go_live_date']) ? date('d M Y', strtotime($meta['go_live_date'])) : 'Not set'; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo ($meta['is_verify_meta'] == '1' ? 'badge-approved' : 'badge-pending'); ?>">
                                                        <?php echo ($meta['is_verify_meta'] == '1' ? 'Verified' : 'Un-Verified'); ?>
                                                    </span>
                                                    <br />
                                                    <span class="badge <?php echo ($meta['status'] == '1' ? 'badge-approved' : 'badge-pending'); ?>">
                                                        <?php echo ($meta['status'] == '1' ? 'Active' : 'Deactive'); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d M Y H:i', strtotime($meta['created_date'])); ?></td>
                                                <td>
                                                <div class="d-flex">
                                                <button class="btn btn-sm btn-info btn-action me-1 view-meta" 
                                                        data-id="<?php echo $meta['id']; ?>"
                                                        title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php
											if($this->mres['member_type']==1){ 
												if($meta['is_verify_meta'] == '1' && $meta['is_pdl_submit'] == '0'){ ?>
													<button type="button" class="btn btn-sm btn-info btn-action me-1 pdlFormModal"
													data-url="<?php echo base_url('admin/metas/finalpdl_submit/'.$meta['id']); ?>">
													Submit
													</button>
												 <?php 
												 } 
												 if($meta['is_verify_meta'] == '0'){ ?>
                                                <a href="<?php echo base_url('admin/metas/verify_meta/' . $meta['id']); ?>" 
                                                   class="btn btn-sm btn-info btn-action me-1" 
                                                   title="Verify Meta"
                                                   onclick="return confirm('Are you sure you want to verify this meta?')">
                                                    <i class="fas fa-check-circle"></i> Verify
                                                </a>
                                                <?php 
												}?>
                                                
                                                <?php if($meta['status'] == '0'){ ?>
                                                <a href="<?php echo base_url('admin/metas/active_meta/' . $meta['id']); ?>" 
                                                   class="btn btn-sm btn-info btn-action me-1" 
                                                   title="Active Meta"
                                                   onclick="return confirm('Are you sure you want to active this meta?')">
                                                    <i class="fas fa-check-circle"></i> Active
                                                </a>
                                                <?php 
												}?>
                                                
                                                <?php if($meta['status'] == '1'){ ?>
                                                <a href="<?php echo base_url('admin/metas/deactive_meta/' . $meta['id']); ?>" 
                                                   class="btn btn-sm badge-pending btn-action me-1" 
                                                   title="Deactive Meta"
                                                   onclick="return confirm('Are you sure you want to deactive this meta?')">
                                                    <i class="fas fa-check-circle"></i> Deactive
                                                </a>
                                                <?php 
												}
											}?>
                                            </div>
                                            <br />
                                            <?php if($meta['is_verify_meta'] == '1' && $this->userId == '1'){ ?>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-sm btn-<?php echo isset($meta['is_new_released']) && $meta['is_new_released'] ? 'success' : 'secondary'; ?> toggle-flag" 
                                                            data-id="<?php echo $meta['id']; ?>" 
                                                            data-field="is_new_released"
                                                            title="<?php echo isset($meta['is_new_released']) && $meta['is_new_released'] ? 'Unset as New Released' : 'Set as New Released'; ?>" style="width: 37px;height: 34px;margin-right: 2px !important;">
                                                        <i class="fas fa-star"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-<?php echo isset($meta['is_recently_added']) && $meta['is_recently_added'] ? 'success' : 'secondary'; ?> toggle-flag" 
                                                            data-id="<?php echo $meta['id']; ?>" 
                                                            data-field="is_recently_added"
                                                            title="<?php echo isset($meta['is_recently_added']) && $meta['is_recently_added'] ? 'Unset as Recently Added' : 'Set as Recently Added'; ?>" style="width: 37px;height: 34px;margin-right: 2px !important;">
                                                        <i class="fas fa-clock"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-<?php echo isset($meta['is_latest']) && $meta['is_latest'] ? 'success' : 'secondary'; ?> toggle-flag" 
                                                            data-id="<?php echo $meta['id']; ?>" 
                                                            data-field="is_latest"
                                                            title="<?php echo isset($meta['is_latest']) && $meta['is_latest'] ? 'Unset as Latest' : 'Set as Latest'; ?>" style="width: 37px;height: 34px;margin-right: 2px !important;">
                                                        <i class="fas fa-bolt"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-<?php echo isset($meta['is_top_rated']) && $meta['is_top_rated'] ? 'success' : 'secondary'; ?> toggle-flag" 
                                                            data-id="<?php echo $meta['id']; ?>" 
                                                            data-field="is_top_rated"
                                                            title="<?php echo isset($meta['is_top_rated']) && $meta['is_top_rated'] ? 'Unset as Top Rated' : 'Set as Top Rated'; ?>" style="width: 37px;height: 34px;margin-right: 2px !important;">
                                                        <i class="fas fa-thumbs-up"></i>
                                                    </button>                                                    
                                                </div> 
                                                <?php 
											}?>                                               
                                            </td>
                                            </tr>
                                        <?php 
										}?>
                                    <?php }else{ ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No metas found</td>
                                        </tr>
                                    <?php 
									}?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if (!empty($page_links)){ ?>
                            <div class="d-flex justify-content-end mt-3">
                                <?php echo $page_links; ?>
                            </div>
                        <?php 
						}?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Meta View Modal -->

<div class="modal fade" id="pformModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Release Platform</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      
      <div class="modal-body">
        <form id="pdynamicForm" method="post">
          <div class="mb-3">
            <label class="form-label">Release Platform</label>
            <select name="platforms_to_release" class="form-control" required>
                <option value="">Select</option>
                <?php if(is_array($pdl_release_platform) && !empty($pdl_release_platform)){
                     foreach($pdl_release_platform as $k=>$v){ 
                        ?>
                     <option value="<?php echo $v;?>"><?php echo $v;?></option>
             <?php        }
                        
                } ?>
            </select>
            </div>
         
          <button type="submit" class="btn btn-success">Submit</button>
        </form>
        <div id="formMessage" class="mt-3"></div>
      </div>

    </div>
  </div>
</div>


<script>
$(document).ready(function () {

  // Open modal and set form action URL dynamically
  $(".pdlFormModal").on("click", function () {
    let formUrl = $(this).data("url");
    $("#pdynamicForm").attr("action", formUrl);
    $("#pformModal").modal("show");
  });

  // Submit form via AJAX
  $("#pdynamicForm").on("submit", function (e) {
    e.preventDefault();
    let formUrl = $(this).attr("action");

    $.ajax({
      url: formUrl,
      type: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (res) {
        if (res.status === "success") {
          $("#formMessage").html('<div class="alert alert-success">Form submitted successfully!</div>');

          setTimeout(function () {
            $("#pformModal").modal("hide");
            location.reload(); // reload page after modal closes
          }, 1500);

        } else {
          $("#formMessage").html('<div class="alert alert-danger">Error: ' + res.message + '</div>');
        }
      },
      error: function () {
        $("#formMessage").html('<div class="alert alert-danger">Something went wrong!</div>');
      }
    });
  });

  // Reset message & form on modal close
  $('#pformModal').on('hidden.bs.modal', function () {
    $("#formMessage").html('');
    $("#pdynamicForm")[0].reset();
  });

});
</script>

<!-- Meta View Modal -->
<div class="modal fade modal-meta" id="metaViewModal" tabindex="-1" aria-labelledby="metaViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="metaViewModalLabel">Meta Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="metaViewContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <?php /*?><div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="verifyMetaBtn" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Verify
                </a>
            </div><?php */?>
        </div>
    </div>
</div>

<?php /*?><script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><?php */?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });
    
    // Initialize modal
    var metaViewModal = new bootstrap.Modal(document.getElementById('metaViewModal'));
    
    // Handle view button click
    $('.view-meta').on('click', function() {
        var metaId = $(this).data('id');
        
        $('#metaViewContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
        
        $.ajax({
            url: '<?php echo base_url("admin/metas/get_meta_details/"); ?>' + metaId,
            method: 'GET',
            success: function(response) {
                $('#metaViewContent').html(response);
                $('#verifyMetaBtn').attr('href', '<?php echo base_url("admin/metas/verify_meta/"); ?>' + metaId);
                metaViewModal.show();
            },
            error: function(xhr) {
                $('#metaViewContent').html(`
                    <div class="alert alert-danger">
                        Failed to load meta details. Please try again.
                    </div>
                `);
                metaViewModal.show();
            }
        });
    });
    
    $('#metaViewModal').on('hidden.bs.modal', function () {
        $('#metaViewContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
    });
});

// Audio Player Functionality
let currentAudio = null;
let currentlyPlaying = null;

function playAudio(audioUrl, clickedElement) {
    // Check if we're clicking the currently playing element
    if (currentlyPlaying === clickedElement && currentAudio && !currentAudio.paused) {
        currentAudio.pause();
        clickedElement.classList.remove('now-playing');
        currentlyPlaying = null;
        return;
    }
    
    // Pause any currently playing audio
    if (currentAudio) {
        currentAudio.pause();
        if (currentlyPlaying) {
            currentlyPlaying.classList.remove('now-playing');
        }
    }
    
    // Create new audio element
    currentAudio = new Audio(audioUrl);
    
    // Play the audio
    currentAudio.play()
        .then(() => {
            currentlyPlaying = clickedElement;
            clickedElement.classList.add('now-playing');
            
            // Handle when audio finishes playing
            currentAudio.onended = function() {
                clickedElement.classList.remove('now-playing');
                currentlyPlaying = null;
            };
        })
        .catch(error => {
            console.error('Audio playback failed:', error);
            alert('Error playing audio. Please check the console for details.');
        });
}

$(document).on('click', '.toggle-flag', function() {
    var btn = $(this);
    var metaId = btn.data('id');
    var field = btn.data('field');
    
    $.ajax({
        url: '<?php echo base_url("admin/metas/toggle_"); ?>' + field + '/' + metaId,
        method: 'POST',
        dataType: 'json',
        data: {
            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        success: function(response) {
						
            if(response.status === 'success') {
                if(response.new_value == 1) {
                    btn.removeClass('btn-secondary').addClass('btn-success');
                    // Update title attribute
                    var currentTitle = btn.attr('title');
                    btn.attr('title', currentTitle.replace('Set as', 'Unset as'));
                } else {
                    btn.removeClass('btn-success').addClass('btn-secondary');
                    // Update title attribute
                    var currentTitle = btn.attr('title');
                    btn.attr('title', currentTitle.replace('Unset as', 'Set as'));
                }
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the flag.');
        }
    });
});
</script>
<?php $this->load->view("bottom_application"); ?>