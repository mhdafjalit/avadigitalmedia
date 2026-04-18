<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'admin')
);
$site_title_text = escape_chars($this->config->item('site_name'));
$posted_keyword = $this->input->get_post('keyword',TRUE);
$posted_keyword = escape_chars($posted_keyword);
$posted_status = $this->input->get_post('status',TRUE);
$posted_status = escape_chars($posted_status); 
?>

<style>
:root {
  --primary-color: #4361ee;
  --success-color: #06d6a0;
  --danger-color: #ef476f;
  --warning-color: #ffd166;
  --dark-color: #2b2d42;
  --light-bg: #f8f9fa;
  --border-radius: 12px;
  --box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  --transition: all 0.3s ease;
}

/* Modern Card Design */
.modern-card {
  background: white;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  border: none;
  transition: var(--transition);
}

.modern-card:hover {
  box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

/* Search Section */
.search-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 25px;
  border-radius: var(--border-radius);
  margin-bottom: 25px;
  color: white;
}

.search-input-group {
  background: white;
  border-radius: 50px;
  overflow: hidden;
  display: flex;
  align-items: center;
  padding: 5px;
  box-shadow: 0 2px 15px rgba(0,0,0,0.1);
}

.search-input-group input {
  border: none;
  padding: 12px 20px;
  flex: 1;
  outline: none;
  font-size: 14px;
}

.search-input-group button {
  background: var(--primary-color);
  color: white;
  border: none;
  padding: 10px 25px;
  border-radius: 50px;
  margin: 3px;
  font-weight: 500;
  transition: var(--transition);
}

.search-input-group button:hover {
  background: #2c3e50;
  transform: translateY(-1px);
}

/* Table Design */
.modern-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 8px;
}

.modern-table thead th {
  background: var(--light-bg);
  color: var(--dark-color);
  font-weight: 600;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 15px;
  border: none;
}

.modern-table tbody tr {
  background: white;
  border-radius: 10px;
  transition: var(--transition);
  box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

.modern-table tbody tr:hover {
  box-shadow: 0 5px 20px rgba(0,0,0,0.08);
  transform: translateY(-2px);
}

.modern-table td {
  padding: 20px 15px;
  border: none;
  vertical-align: middle;
  font-size: 14px;
}

/* User Image Circle */
.user-image-circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 20px;
  text-transform: uppercase;
  box-shadow: 0 4px 10px rgba(102, 126, 234, 0.3);
  margin-right: 12px;
  float: left;
}

.user-info {
  display: flex;
  align-items: center;
}

.user-details {
  flex: 1;
}

.user-details .fw-bold {
  font-size: 16px;
  margin-bottom: 4px;
  color: var(--dark-color);
}

.user-details .text-muted {
  font-size: 12px;
}

/* Toggle Switch Styles */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 30px;
  margin: 0;
  cursor: pointer;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
  position: absolute;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ef476f;
  transition: .4s;
  border-radius: 34px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 24px;
  width: 24px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
  box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

input:checked + .toggle-slider {
  background-color: #06d6a0;
}

input:checked + .toggle-slider:before {
  transform: translateX(30px);
}

/* Status Label */
.status-label {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  font-weight: 500;
}

.status-text {
  min-width: 60px;
  color: var(--dark-color);
}

/* Action Buttons */
.action-btn {
  width: 35px;
  height: 35px;
  border-radius: 10px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--light-bg);
  transition: var(--transition);
  margin: 0 3px;
}

.action-btn:hover {
  transform: translateY(-2px);
}

.action-btn.view:hover {
  background: var(--primary-color);
}

.action-btn.edit:hover {
  background: var(--success-color);
}

.action-btn.delete:hover {
  background: var(--danger-color);
}

.action-btn img {
  width: 16px;
  height: 16px;
  filter: brightness(0.5);
  transition: var(--transition);
}

.action-btn:hover img {
  filter: brightness(0) invert(1);
}

/* Notification Select */
.notification-select {
  background: white;
  border: 2px solid var(--light-bg);
  border-radius: 10px;
  padding: 12px 20px;
  font-size: 14px;
  color: var(--dark-color);
  transition: var(--transition);
  cursor: pointer;
  min-width: 250px;
}

.notification-select:hover {
  border-color: var(--primary-color);
}

/* Pagination */
.pagination-modern {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 10px;
  margin-top: 25px;
}

.pagination-modern .page-item {
  list-style: none;
}

.pagination-modern .page-link {
  padding: 8px 16px;
  border-radius: 8px;
  border: none;
  background: white;
  color: var(--dark-color);
  font-weight: 500;
  transition: var(--transition);
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.pagination-modern .page-link:hover {
  background: var(--primary-color);
  color: white;
  transform: translateY(-1px);
}

/* Info Cards */
.info-card {
  background: white;
  border-radius: 12px;
  padding: 15px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.03);
  border-left: 4px solid var(--primary-color);
  margin-bottom: 15px;
}

.info-card p {
  margin: 5px 0;
  color: #6c757d;
  font-size: 13px;
}

.info-card strong {
  color: var(--dark-color);
  font-size: 14px;
}

/* Loading overlay for toggle */
.toggle-loading {
  position: relative;
  pointer-events: none;
  opacity: 0.6;
}

.toggle-loading:after {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  top: 50%;
  left: 50%;
  margin-top: -8px;
  margin-left: -8px;
  border: 2px solid var(--primary-color);
  border-top-color: transparent;
  border-radius: 50%;
  animation: spinner 0.6s linear infinite;
  z-index: 999;
}

@keyframes spinner {
  to {transform: rotate(360deg);}
}
</style>

<div class="dash_outer">
  <div class="dash_container">
    <?php $this->load->view('view_left_sidebar'); ?>
    <div id="main-content" class="h-100">
      <?php $this->load->view('view_top_sidebar');?>
      
      <!-- Header Section -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="display-6 fw-bold" style="color: var(--dark-color);"><?php echo $heading_title;?></h1>
          <?php echo navigation_breadcrumb($heading_title,$bdcm_array); ?>
        </div>
      </div>

      <!-- Main Content -->
      <div class="main-content-inner">
        <!-- Messages -->
        <?php validation_message(); ?>
        <?php if(error_message() != ''): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo error_message(); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <!-- Search Section -->
        <div class="search-section modern-card">
          <?php echo form_open("", 'id="search_form" method="get"'); ?>
          <div class="row align-items-center">
            <div class="col-lg-5 mb-3 mb-lg-0">
              <div class="search-input-group">
                <input type="text" name="keyword" value="<?php echo $posted_keyword; ?>" 
                       placeholder="Search by name, email, or phone...">
                <button type="submit" class="btn">
                  <i class="fas fa-search me-2"></i>Search
                </button>
                <?php if($posted_keyword != '' || $posted_status != ''): ?>
                  <a href="<?php echo site_url('admin/staff_user'); ?>" class="btn btn-light ms-2">
                    <i class="fas fa-times"></i>
                  </a>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="col-lg-2 mb-3 mb-lg-0">
              <select class="form-select modern-select" name="status">
                <option value="">All Status</option>
                <option value="1" <?php echo $posted_status === '1' ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?php echo $posted_status === '0' ? 'selected' : ''; ?>>Inactive</option>
              </select>
            </div>
            
            <div class="col-lg-2 mb-3 mb-lg-0">
              <div class="d-flex align-items-center">
                <span class="me-2 text-white">Show:</span>
                <?php echo front_record_per_page('per_page', 'per_page'); ?>
              </div>
            </div>
            
            <div class="col-lg-3 text-lg-end">
              <a href="<?php echo site_url('admin/create_staff_user'); ?>" class="btn btn-light btn-lg px-4 create_top text-white rounded-5 fw-medium trans_eff align-middle">
                <i class="fas fa-plus-circle me-2"></i>Add New Staff
              </a>
            </div>
          </div>
          <?php echo form_close(); ?>
        </div>

        <!-- Notification Section -->
        <div class="modern-card p-4 mb-4">
          <div class="row align-items-center">
            <div class="col-md-3">
              <label class="fw-bold mb-2 mb-md-0">Send Notification:</label>
            </div>
            <div class="col-md-6">
              <select name="notification_id" id="notification_id" class="notification-select w-100">
                <option value="">Select notification template</option>
                <?php 
                $max_lmt = '300';
                $sql = "SELECT * FROM wl_notification WHERE status='1' ORDER BY notification_id DESC LIMIT ".$max_lmt;
                $query = $this->db->query($sql);
                if($query->num_rows() > 0):
                  $result_notification = $query->result_array();
                  foreach($result_notification as $val2): ?>
                    <option value="<?php echo $val2['notification_id']; ?>">
                      <?php echo $val2['notification_title']; ?>
                    </option>
                <?php 
                  endforeach; 
                endif; 
                ?>
              </select>
            </div>
          </div>
        </div>

        <!-- Table Section -->
        <?php echo form_open("", 'id="data_form"'); ?>
        <div class="modern-card p-4">
          <div class="table-responsive">
            <table class="modern-table">
              <thead>
                <tr>
                  <th width="40">
                    <input type="checkbox" onclick="$('input[name*=\'arr_ids\']').prop('checked', this.checked);">
                  </th>
                  <th>Staff Member</th>
                  <th>Contact & Login</th>
                  <th>Role/Dept/Desig</th>
                  <th>Location</th>
                  <th width="120">Status</th>
                  <th width="150">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if(is_array($res) && !empty($res)):
                  function getRecordName($id, $table, $column) {
                    return $id > 0 ? log_fetched_rec($id, $table, $column)['rec_data'][$column] ?? '' : '';
                  }
                  
                  foreach ($res as $val):
                    $loop_address = $val['address'];
                    $city_name = getRecordName($val['city'], 'city', 'title');
                    $state_name = getRecordName($val['state'], 'state', 'title');
                    $country = getRecordName($val['country'], 'country', 'country_name');
                    $zipcode = $val['pin_code'] > 0 ? $val['pin_code'] : '';
                    $loop_address .= $city_name ? ' ' . $city_name : '';
                    $loop_address .= $state_name ? ', ' . $state_name : '';
                    $loop_address .= $zipcode ? ' - ' . $zipcode : '';
                    $loop_address .= $country ? ' (' . $country . ')' : '';
                    
                    // Generate initials for user image
                    $name_parts = explode(' ', $val['name']);
                    $initials = '';
                    foreach($name_parts as $part) {
                      $initials .= strtoupper(substr($part, 0, 1));
                    }
                    $initials = substr($initials, 0, 2);
                    
                    // Generate unique ID for toggle
                    $toggle_id = 'toggle_' . $val['customers_id'];
                    
                    // Get MD5 ID
                    $md5_id = md5($val['customers_id']);
                ?>
                <tr id="row_<?php echo $val['customers_id']; ?>">
                  <td>
                    <input type="checkbox" name="arr_ids[]" value="<?php echo $val['customers_id']; ?>">
                  </td>
                  <td>
                    <div class="user-info">
                      <div class="user-image-circle">
                        <?php echo $initials; ?>
                      </div>
                      <div class="user-details">
                        <div class="fw-bold"><?php echo $val['name']; ?></div>
                        <small class="text-muted">ID: <?php echo $val['sponsor_id']; ?></small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="info-card">
                      <p><strong>📧 Username:</strong> <?php echo $val['user_name']; ?></p>
                      <p><strong>📞 Phone:</strong> <?php echo $val['mobile_number']; ?></p>
                      <p><strong>🔑 Password:</strong> <?php echo $this->safe_encrypt->decode($val['password']); ?></p>
                    </div>
                  </td>
                  <td>
                    <div class="info-card">
                      <p><strong>👤 Role:</strong> <?php echo $val['role_name']; ?></p>
                      <p><strong>🏢 Dept:</strong> <?php echo $val['department_name']; ?></p>
                      <p><strong>📋 Desig:</strong> <?php echo $val['designation_name']; ?></p>
                    </div>
                  </td>
                  <td>
                    <div class="info-card">
                      <p><strong>📍 Address:</strong> <?php echo $loop_address ?: 'N/A'; ?></p>
                      <p><strong>🌐 IP:</strong> <?php echo $val['ip_address'] ?: 'N/A'; ?></p>
                    </div>
                  </td>
                 
                  <td>
                    <div class="status-label" data-row-id="<?php echo $val['customers_id']; ?>">
                        <!-- Hidden field to store MD5 ID (most reliable method) -->
                        <input type="hidden" class="md5-id-value" value="<?php echo $md5_id; ?>">
                        
                        <label class="toggle-switch" for="<?php echo $toggle_id; ?>">
                            <input type="checkbox" 
                                   id="<?php echo $toggle_id; ?>" 
                                   class="status-toggle"
                                   data-customer-id="<?php echo $val['customers_id']; ?>"
                                   data-md5-id="<?php echo $md5_id; ?>"
                                   data-current-status="<?php echo $val['status']; ?>"
                                   <?php echo ($val['status'] == 1) ? '' : 'checked'; ?>>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="status-text" id="status_text_<?php echo $val['customers_id']; ?>">
                            <?php echo ($val['status'] == 1) ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>
                  </td>

                  <td>
                    <div class="d-flex">
                      <a href="<?php echo site_url('admin/view_profile/'.md5($val['customers_id'])); ?>" 
                         class="action-btn view" title="View Profile">
                        <img src="<?php echo theme_url(); ?>images/eye2.svg" alt="View">
                      </a>
                      <a href="<?php echo site_url('admin/edit_sub_admin/'.md5($val['customers_id'])); ?>" 
                         class="action-btn edit" title="Edit">
                        <img src="<?php echo theme_url(); ?>images/edit.svg" alt="Edit">
                      </a>
                      <a href="<?php echo site_url('admin/delete_staff/'.md5($val['customers_id'])); ?>" 
                         class="action-btn delete confirm_delete" title="Delete">
                        <img src="<?php echo theme_url(); ?>images/delete.svg" alt="Delete">
                      </a>
                    </div>
                    
                    <!-- Permissions -->
                    <div class="mt-3">
                      <small class="text-muted d-block mb-2">🔐 Permissions:</small>
                      <div class="d-flex gap-2">
                        <a href="<?php echo site_url('admin/permission_add/'.md5($val['customers_id'])); ?>" 
                           class="btn btn-sm btn-outline-primary">Add</a>
                        <a href="<?php echo site_url('admin/permission/'.md5($val['customers_id'])); ?>" 
                           class="btn btn-sm btn-outline-dark pop3" data-type="iframe">View</a>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php 
                  endforeach; 
                else: 
                ?>
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="text-muted">
                      <i class="fas fa-users fa-3x mb-3"></i>
                      <p class="fs-5">No staff members found</p>
                    </div>
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Bulk Actions -->
          <?php if(is_array($res) && !empty($res)): ?>
          <div class="mt-4 pt-3 border-top">
            <div class="d-flex align-items-center gap-3">
              <input type="submit" name="Send" value="📧 Send Email to Selected" 
                     class="btn btn-primary px-4 btn btn-light btn-lg px-4 create_top text-white rounded-5 fw-medium trans_eff align-middle" 
                     onclick="return validcheckstatus('arr_ids[]','Send Email','Record','u_status_arr[]');">
            </div>
          </div>
          <?php endif; ?>
        </div>
        <?php echo form_close(); ?>
        
        <!-- Pagination -->
        <?php if(isset($page_links) && $page_links): ?>
        <div class="pagination-modern">
          <?php echo $page_links; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Complete JavaScript for Toggle Switch -->
<script>
$(document).ready(function() {
    // Add cache control meta tags
    if ($('meta[name="cache-control"]').length === 0) {
        $('head').append('<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">');
        $('head').append('<meta http-equiv="Pragma" content="no-cache">');
        $('head').append('<meta http-equiv="Expires" content="0">');
    }
    
    // Add base URL meta tag
    if ($('meta[name="base-url"]').length === 0) {
        $('head').append('<meta name="base-url" content="<?php echo base_url(); ?>">');
    }
    
    // Initialize all toggle switches based on database status
    function initializeToggles() {
        $('.status-toggle').each(function() {
            var $toggle = $(this);
            var currentStatus = $toggle.data('current-status');
            var customerId = $toggle.data('customer-id');
            
            // Set checkbox state based on current-status (1 = Active, 0 = Inactive)
            if (currentStatus == 1) {
                $toggle.prop('checked', true);
                $('#status_text_' + customerId).text('Active');
            } else {
                $toggle.prop('checked', false);
                $('#status_text_' + customerId).text('Inactive');
            }
        });
    }
    
    // Run initial initialization
    initializeToggles();
    
    // Remove any existing event handlers and attach new one
    $('.status-toggle').off('change').on('change', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $toggle = $(this);
        var $statusLabel = $toggle.closest('.status-label');
        var $row = $toggle.closest('tr');
        
        // Get MD5 ID using multiple methods for reliability
        var md5Id = $toggle.data('md5-id'); // Method 1: from data attribute
        
        if (!md5Id || md5Id === 'undefined' || md5Id === '') {
            md5Id = $toggle.attr('data-md5-id'); // Method 2: from attribute
        }
        
        if (!md5Id || md5Id === 'undefined' || md5Id === '') {
            md5Id = $statusLabel.find('.md5-id-value').val(); // Method 3: from hidden field
        }
        
        if (!md5Id || md5Id === 'undefined' || md5Id === '') {
            md5Id = $statusLabel.data('row-id'); // Method 4: from parent
        }
        
        if (!md5Id || md5Id === 'undefined' || md5Id === '') {
            md5Id = $row.find('.md5-id-value').val(); // Method 5: from row
        }
        
        var customerId = $toggle.data('customer-id');
        var currentStatus = $toggle.data('current-status');
        
        // Determine new status based on checkbox state
        var newStatus = $toggle.is(':checked') ? 'active' : 'deactive';
        var actionText = $toggle.is(':checked') ? 'activate' : 'deactivate';
        
        // Validate MD5 ID
        if (!md5Id || md5Id === 'undefined' || md5Id.length === 0) {
            console.error('MD5 ID is missing');
            Swal.fire({
                title: 'Error!',
                text: 'Invalid customer identifier. Please refresh the page.',
                icon: 'error',
                confirmButtonColor: '#ef476f',
                confirmButtonText: 'OK'
            }).then(() => {
                // Revert the toggle to original state
                $toggle.prop('checked', currentStatus == 1);
                $('#status_text_' + customerId).text(currentStatus == 1 ? 'Active' : 'Inactive');
            });
            return;
        }
        
        // Show confirmation dialog
        Swal.fire({
            title: 'Change Status',
            text: 'Are you sure you want to ' + actionText + ' this staff member?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: $toggle.is(':checked') ? '#06d6a0' : '#ef476f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, ' + (actionText === 'activate' ? 'Activate' : 'Deactivate'),
            cancelButtonText: 'No, Cancel',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading spinner
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we update the status',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        // Add loading class to toggle
                        $toggle.closest('.toggle-switch').addClass('toggle-loading');
                        
                        // Get base URL
                        var baseUrl = $('meta[name="base-url"]').attr('content');
                        
                        // Create redirect URL with cache busting
                        var redirectUrl = baseUrl + 'admin/status_staff/' + md5Id + '?u_status=' + newStatus + '&_=' + new Date().getTime();
                        
                        // Redirect after small delay
                        setTimeout(function() {
                            window.location.href = redirectUrl;
                        }, 500);
                    }
                });
            } else {
                // Revert toggle if cancelled
                $toggle.prop('checked', currentStatus == 1);
                $('#status_text_' + customerId).text(currentStatus == 1 ? 'Active' : 'Inactive');
                
                // Show cancellation message
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Status change was cancelled',
                    icon: 'info',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });
    
    // Handle label click to prevent double triggering
    $('.toggle-switch').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Handle browser back/forward cache
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
});

// Sweet Alert for Success Messages
<?php if($this->session->flashdata('success')): ?>
$(document).ready(function() {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?php echo $this->session->flashdata('success'); ?>',
        showConfirmButton: true,
        confirmButtonColor: '#4361ee',
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true
    }).then(() => {
        // Force re-initialization of toggles after success message
        $('.status-toggle').each(function() {
            var $toggle = $(this);
            var currentStatus = $toggle.data('current-status');
            var customerId = $toggle.data('customer-id');
            
            if (currentStatus == 1) {
                $toggle.prop('checked', true);
                $('#status_text_' + customerId).text('Active');
            } else {
                $toggle.prop('checked', false);
                $('#status_text_' + customerId).text('Inactive');
            }
        });
    });
});
<?php endif; ?>

// Sweet Alert for Error Messages
<?php if($this->session->flashdata('error')): ?>
$(document).ready(function() {
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '<?php echo $this->session->flashdata('error'); ?>',
        confirmButtonColor: '#ef476f',
        confirmButtonText: 'OK'
    }).then(() => {
        // Force re-initialization of toggles after error message
        $('.status-toggle').each(function() {
            var $toggle = $(this);
            var currentStatus = $toggle.data('current-status');
            var customerId = $toggle.data('customer-id');
            
            if (currentStatus == 1) {
                $toggle.prop('checked', true);
                $('#status_text_' + customerId).text('Active');
            } else {
                $toggle.prop('checked', false);
                $('#status_text_' + customerId).text('Inactive');
            }
        });
    });
});
<?php endif; ?>
</script>

<?php $this->load->view("bottom_application"); ?>