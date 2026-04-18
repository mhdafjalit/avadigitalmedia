<?php $this->load->view('top_application'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
</style>
<div class="dash_outer">
  <div class="dash_container">
    <?php $this->load->view('view_left_sidebar'); ?>
    <div id="main-content" class="h-100">
      <?php $this->load->view('view_top_sidebar');?>
      <div class="top_sec d-flex justify-content-between">
        <h1 class="mt-4">Manage Artist</h1>
      </div>
      
      <div class="main-content-inner">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; 
		if(error_message() !=''):
            echo error_message();
          endif
		?>
        
        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        
        <!-- Search Form -->
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Search Existing Artists</h5>
            <?php echo form_open('', ['id' => 'searchForm']); ?>
              <div class="row g-3">
                <div class="col-md-4">
                  <input type="text" name="artist_name" class="form-control" placeholder="Artist name" value="<?php echo htmlspecialchars($artist_name); ?>" required>
                </div>
                
                <div class="col-md-4">
                  <div class="input-group">
                    <input type="text" name="apple_id" id="apple_id" class="form-control" 
                           value="<?php echo set_value('apple_id'); ?>" placeholder="Apple ID">
                    <div class="input-group-text">
                      <input class="form-check-input" type="checkbox" name="create_new_apple" id="create_new_apple">
                      <label class="form-check-label ms-2" for="create_new_apple">Create New</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group">
                    <input type="text" name="spotify_id" id="spotify_id" class="form-control" 
                           value="<?php echo set_value('spotify_id'); ?>" placeholder="Spotify ID">
                    <div class="input-group-text">
                      <input class="form-check-input" type="checkbox" name="create_new_spotify" id="create_new_spotify">
                      <label class="form-check-label ms-2" for="create_new_spotify">Create New</label>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3">
                  <button type="submit" name="search" value="1" class="btn btn-purple">
                    <i class="fas fa-search"></i> Search
                  </button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

<!-- Search Results -->
<?php if($search_performed){ ?>
<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title d-flex justify-content-between align-items-center">
      Search Results
      <div class="input-group ms-3" style="width: 300px;">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
        <input type="text" id="keywordSearch" class="form-control" placeholder="Filter artists...">
        <button class="btn btn-purple" type="button" id="clearSearch">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </h5>
    
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
      <?php if (!empty($search_results)){ ?>
        <table class="table table-bordered table-striped table-hover mb-0" id="artistTable">
          <thead class="sticky-top bg-light">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Apple ID</th>
              <th>Spotify ID</th>
              <th>Last Updated</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
			foreach ($search_results as $artist){ ?>
            <tr>
              <td><?php echo htmlspecialchars($artist['id']); ?></td>
              <td><?php echo htmlspecialchars($artist['name']); ?></td>
              <td><?php echo htmlspecialchars($artist['apple_id']); ?></td>
              <td><?php echo htmlspecialchars($artist['spotify_id']); ?></td> 
              <td><?php echo ($artist['last_updated'] != "0001-01-01T00:00:00Z") ? getDateFormat($artist['last_updated'],2) : "N/A"; ?></td>
              <td>
                <?php
				$hasValidAppleId = !empty($artist['apple_id']) && is_numeric($artist['apple_id']) && $artist['apple_id'] !== 'new';
				$hasValidSpotifyId = !empty($artist['spotify_id']) && is_numeric($artist['spotify_id']) && $artist['spotify_id'] !== 'new';
				
				if ($hasValidAppleId || $hasValidSpotifyId):
				?>
                <button class="btn btn-sm btn-light view-artist" 
                        data-apple-id="<?php echo $artist['apple_id']; ?>" 
                        data-spotify-id="<?php echo $artist['spotify_id']; ?>">
                  <i class="fas fa-eye"></i> View
                </button>
                <?php endif; ?>
              </td>
            </tr>
            <?php 
			}?>
          </tbody>
        </table>
      <?php 
	  }else{?>
        <div class="alert alert-info">
          No artists found matching your criteria. Please add the new artist below.
        </div>
      <?php 
	  }?>
    </div>
  </div>
  <div class="card-footer text-muted">
    Showing <span id="visibleCount"><?php echo count($search_results); ?></span> of <?php echo count($search_results); ?> artists
  </div>
  <div class="mt-2 text-muted" style="margin-left: 13px;color:  chocolate !important;">
      If the artist isn't listed above, you can add them below.
   </div>
</div>
	<?php 
}?>

        <!-- Add Artist Form -->
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Add/Update Artist</h5>
            <?php echo form_open('', ['id' => 'addArtistForm']); ?>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Artist Name *</label>
                  <input type="text" name="name" class="form-control" required 
                         value="<?php echo set_value('name', $artist_name); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Apple ID</label>
                  <div class="input-group">
                    <input type="text" name="apple_id" id="apple_id1" class="form-control" 
                           value="<?php echo set_value('apple_id'); ?>">
                    <div class="input-group-text">
                      <input class="form-check-input" type="checkbox" name="create_new_apple" id="create_new_apple1">
                      <label class="form-check-label ms-2" for="create_new_apple1">Create New</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Spotify ID</label>
                  <div class="input-group">
                    <input type="text" name="spotify_id" id="spotify_id1" class="form-control" 
                           value="<?php echo set_value('spotify_id'); ?>">
                    <div class="input-group-text">
                      <input class="form-check-input" type="checkbox" name="create_new_spotify" id="create_new_spotify1">
                      <label class="form-check-label ms-2" for="create_new_spotify1">Create New</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Meta ID</label>
                  <input type="text" name="meta_id" class="form-control" 
                         value="<?php echo set_value('meta_id'); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Facebook Page URL</label>
                  <input type="url" name="facebook_artist_page_url" class="form-control" 
                         value="<?php echo set_value('facebook_artist_page_url'); ?>">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Instagram Page URL</label>
                  <input type="url" name="insta_artist_page_url" class="form-control" 
                         value="<?php echo set_value('insta_artist_page_url'); ?>">
                </div>
                <div class="col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_iprs_member" id="is_iprs_member" 
                           value="1" <?php echo set_checkbox('is_iprs_member', '1'); ?>>
                    <label class="form-check-label" for="is_iprs_member">IPRS Member</label>
                  </div>
                </div>
                <div class="col-12 mt-3">
                  <button type="submit" name="add_artist" value="1" class="btn btn-purple">
                    <i class="fas fa-plus"></i> Save Artist
                  </button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Artist Details Modal -->
<div class="modal fade" id="artistModal" tabindex="-1" aria-labelledby="artistModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="artistModalLabel">Artist Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="artistModalBody">
        <!-- Content will be loaded via AJAX -->
        <div class="text-center">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>      
    </div>
  </div>
</div>
<?php $this->load->view("bottom_application"); ?>
<script>
// Handle "Create New" checkboxes
document.getElementById('create_new_apple').addEventListener('change', function() {
    const field = document.getElementById('apple_id');
    field.value = this.checked ? 'new' : '';
    field.readOnly = this.checked;
});

document.getElementById('create_new_spotify').addEventListener('change', function() {
    const field = document.getElementById('spotify_id');
    field.value = this.checked ? 'new' : '';
    field.readOnly = this.checked;
});

document.getElementById('create_new_apple1').addEventListener('change', function() {
    const field = document.getElementById('apple_id1');
    field.value = this.checked ? 'new' : '';
    field.readOnly = this.checked;
});

document.getElementById('create_new_spotify1').addEventListener('change', function() {
    const field = document.getElementById('spotify_id1');
    field.value = this.checked ? 'new' : '';
    field.readOnly = this.checked;
});

// Auto-focus search field on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="artist_name"]').focus();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const keywordSearch = document.getElementById('keywordSearch');
    const clearSearch = document.getElementById('clearSearch');
    const artistTable = document.getElementById('artistTable');
    const visibleCount = document.getElementById('visibleCount');
    
    if (artistTable) {
        const rows = artistTable.querySelectorAll('tbody tr');
        
        keywordSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            let visibleRows = 0;
            
            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const appleId = row.cells[1].textContent.toLowerCase();
                const spotifyId = row.cells[2].textContent.toLowerCase();
                
                const matches = name.includes(searchTerm) || 
                              appleId.includes(searchTerm) || 
                              spotifyId.includes(searchTerm);
                
                row.style.display = matches ? '' : 'none';
                if (matches) visibleRows++;
            });
            
            visibleCount.textContent = visibleRows;
        });
        
        clearSearch.addEventListener('click', function() {
            keywordSearch.value = '';
            rows.forEach(row => row.style.display = '');
            visibleCount.textContent = rows.length;
        });
    }
});


$(document).ready(function() {
  // Initialize modal
  const artistModal = new bootstrap.Modal(document.getElementById('artistModal'));
  
  // Handle view button clicks
  $('.view-artist').click(function() {
    const appleId = $(this).data('apple-id');
    const spotifyId = $(this).data('spotify-id');
    
    // Update modal title
    $('#artistModalLabel').text('Artist');
    
    // Show loading spinner
    $('#artistModalBody').html(`
      <div class="text-center">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    `);
    
    // Show modal
    artistModal.show();
    
    // Load content via AJAX
    $.ajax({
      url: '<?php echo site_url("admin/artists/get_artist_details"); ?>',
      method: 'POST',
      data: {
        apple_id: appleId,
        spotify_id: spotifyId
      },
      success: function(response) {
        $('#artistModalBody').html(response);
      },
      error: function() {
        $('#artistModalBody').html(`
          <div class="alert alert-danger">
            Failed to load artist details. Please try again.
          </div>
        `);
      }
    });
  });
});
</script>

