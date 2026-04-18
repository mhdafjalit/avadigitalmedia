<?php 
$this->load->view('top_application'); 

$bdcm_array = array(
  array('heading'=>'Dashboard','url'=>'admin'),
  array('heading'=>'Trash','url'=>'')
);
?>

<style>
/* ========== BRAND COLOR COMBINATION ========== */
:root {
    --primary-color: #fa009e;
    --secondary-color: #324be3;
    --accent-color: #9b28c1;
    --primary-gradient: linear-gradient(135deg, #fa009e 0%, #9b28c1 100%);
    --secondary-gradient: linear-gradient(135deg, #324be3 0%, #9b28c1 100%);
    --table-header-gradient: linear-gradient(135deg, #fa009e 0%, #324be3 50%, #9b28c1 100%);
    --hover-bg: rgba(250, 0, 158, 0.05);
    --border-color: rgba(50, 75, 227, 0.2);
}

/* ========== PROFESSIONAL TABLE STYLES ========== */

/* Toolbar Container with Brand Colors */
.table-toolbar-modern {
    background: var(--secondary-gradient);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 5px 20px rgba(50, 75, 227, 0.2);
}

.toolbar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 15px;
}

.toolbar-title {
    font-size: 18px;
    font-weight: 600;
    color: white;
}

.toolbar-title i {
    margin-right: 10px;
    color: white;
}

.stats-badge {
    background: rgba(255,255,255,0.2);
    padding: 8px 15px;
    border-radius: 50px;
    color: white;
    font-weight: 500;
}

/* Export Button Styles */
.export-btn-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.export-btn {
    padding: 10px 24px;
    border: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.export-btn i {
    font-size: 16px;
}

.export-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.btn-print {
    background: var(--primary-gradient);
    color: white;
}

.btn-excel {
    background: linear-gradient(135deg, #1e7e34 0%, #28a745 100%);
    color: white;
}

.btn-pdf {
    background: linear-gradient(135deg, #c82333 0%, #dc3545 100%);
    color: white;
}

.btn-copy {
    background: linear-gradient(135deg, #138496 0%, #17a2b8 100%);
    color: white;
}

.btn-csv {
    background: linear-gradient(135deg, #5a6268 0%, #6c757d 100%);
    color: white;
}

/* Modern Table Design */
.modern-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.modern-table table {
    width: 100%;
    margin-bottom: 0;
}

.modern-table thead {
    background: var(--table-header-gradient);
}

.modern-table thead th {
    padding: 15px 12px;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: white;
    border-bottom: none;
}

.modern-table tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid var(--border-color);
}

.modern-table tbody tr:hover {
    background-color: var(--hover-bg);
}

.modern-table tbody td {
    padding: 14px 12px;
    vertical-align: middle;
}

/* Album Image */
.album-thumb {
    width: 55px;
    height: 55px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
}

.album-thumb:hover {
    transform: scale(1.05);
    border-color: var(--primary-color);
}

/* Badges */
.badge-info {
    background: var(--primary-gradient);
    padding: 5px 12px;
    border-radius: 20px;
    color: white;
    display: inline-block;
    font-size: 12px;
}

.badge-secondary {
    background: var(--secondary-gradient);
    padding: 5px 12px;
    border-radius: 20px;
    color: white;
    display: inline-block;
    font-size: 12px;
}

/* Go Live Date */
.go-live-date {
    font-weight: 600;
    color: #28a745;
    background: #e8f5e9;
    padding: 5px 10px;
    border-radius: 6px;
    display: inline-block;
    font-size: 13px;
}

.go-live-date.past {
    color: #dc3545;
    background: #ffebee;
}

.go-live-date.today {
    color: var(--primary-color);
    background: rgba(250, 0, 158, 0.1);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

/* Action Buttons - FIXED WORKING VERSION */
.action-group {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.action-btn {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.restoreBtn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.permanentDelete {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

/* Code Info */
.code-info {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    line-height: 1.5;
}

.code-info strong {
    color: var(--primary-color);
}

/* DataTables Customization */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin: 15px 0;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 6px 12px;
    margin: 0 8px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px;
    margin: 0 4px;
    border-radius: 6px;
    border: none;
    background: #f8f9fa;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--primary-gradient);
    color: white !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--secondary-gradient);
    color: white !important;
}

/* Hide default DataTable buttons */
.dt-buttons {
    display: none !important;
}

/* Responsive */
@media (max-width: 768px) {
    .export-btn-group {
        justify-content: center;
    }
    .modern-table {
        overflow-x: auto;
    }
}

.white_bx {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}
</style>

<!-- Required CSS Libraries -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<div class="dash_outer">
  <div class="dash_container">
    <?php $this->load->view('view_left_sidebar'); ?>
    
    <div id="main-content" class="h-100">
      <?php $this->load->view('view_top_sidebar');?>
      
      <div class="top_sec d-flex justify-content-between align-items-center">
        <h1 class="mt-4">
          <i class="fas fa-trash" style="color: #fa009e;"></i> 
          Trash Management
          <small class="text-muted">Deleted Releases</small>
        </h1>
        <?php echo navigation_breadcrumb('Trash',$bdcm_array); ?>
      </div>
      
      <div class="main-content-inner">
        <div class="white_bx">
          
          <!-- Toolbar with Export Buttons (ONLY HERE, NOT IN FOOTER) -->
          <div class="table-toolbar-modern">
            <div class="toolbar-header">
              <div class="toolbar-title">
                <i class="fas fa-download"></i> Export & Actions
              </div>
              <div class="stats-badge">
                <i class="fas fa-chart-bar"></i> Total Records: <span id="totalRecords">0</span>
              </div>
            </div>
            
            <div class="export-btn-group">
              <button class="export-btn btn-print" id="printTableBtn">
                <i class="fas fa-print"></i> Print Report
              </button>
              <button class="export-btn btn-excel" id="exportExcelBtn">
                <i class="fas fa-file-excel"></i> Excel Export
              </button>
              <button class="export-btn btn-pdf" id="exportPdfBtn">
                <i class="fas fa-file-pdf"></i> PDF Document
              </button>
              <button class="export-btn btn-copy" id="copyTableBtn">
                <i class="fas fa-clipboard"></i> Copy Data
              </button>
              <button class="export-btn btn-csv" id="csvExportBtn">
                <i class="fas fa-file-csv"></i> CSV Format
              </button>
            </div>
          </div>
          
          <!-- Table -->
          <div class="modern-table">
            <div class="table-responsive">
              <table id="trashTable" class="table table-hover">
                <thead>
                  <tr>
                    <th width="30">#</th>
                    <th width="80">Album Art</th>
                    <th>Release Title</th>
                    <th>Artist</th>
                    <th>Record Label</th>
                    <th>Catalog Details</th>
                    <th width="100">Territories</th>
                    <th width="100">Stores</th>
                    <th width="130">Go Live Date</th>
                    <th width="100">Actions</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $i=1;
                if(!empty($trash_list)){
                  foreach ($trash_list as $val) {
                    $artist_name = !empty($val['artist_name']) 
                      ? get_db_field_value('wl_artists','name',['pdl_id'=>$val['artist_name']]) 
                      : '-';
                    
                    $total_territories = count_record('wl_release_territories',"release_id='".$val['release_id']."'");
                    $total_stores = count_record('wl_release_stores',"release_id='".$val['release_id']."'");
                    
                    $go_live_date = !empty($val['original_release_date_of_music']) 
                        ? date('d M Y', strtotime($val['original_release_date_of_music'])) 
                        : '-';
                    
                    $date_status = '';
                    $today = date('Y-m-d');
                    $release_date = $val['original_release_date_of_music'] ?? '';
                    
                    if($release_date && $release_date < $today) {
                        $date_status = 'past';
                    } elseif($release_date == $today) {
                        $date_status = 'today';
                    }
                ?>
                <tr>
                  <td class="text-center"><?= $i++; ?></td>
                  <td class="text-center">
                    <img src="<?= get_image('release',$val['release_banner'],'80','80','AR');?>"
                         class="album-thumb"
                         alt="Album Art"
                         onerror="this.src='<?= base_url('assets/images/default-album.png'); ?>'">
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($val['release_title'] ?? '-'); ?></strong>
                    <?php if(!empty($val['release_version'])): ?>
                      <br><small class="text-muted"><?= htmlspecialchars($val['release_version']); ?></small>
                    <?php endif; ?>
                  </td>
                  <td><?= htmlspecialchars($artist_name); ?></td>
                  <td><?= htmlspecialchars($val['label_name'] ?? '-'); ?></td>
                  <td class="code-info">
                    <small>
                      <strong>Cat#:</strong> <?= htmlspecialchars($val['producer_catalogue'] ?? '-'); ?><br>
                      <strong>ISRC:</strong> <?= htmlspecialchars($val['isrc'] ?? '-'); ?><br>
                      <strong>UPC:</strong> <?= htmlspecialchars($val['upc_ean'] ?? '-'); ?>
                    </small>
                  </td>
                  <td class="text-center">
                    <span class="badge-info"><?= $total_territories; ?> Territories</span>
                  </td>
                  <td class="text-center">
                    <span class="badge-secondary"><?= $total_stores; ?> Stores</span>
                  </td>
                  <td class="text-center">
                    <span class="go-live-date <?= $date_status; ?>">
                      <i class="fas fa-calendar-check"></i> <?= $go_live_date; ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="action-group">
                      <button type="button"
                          class="action-btn restoreBtn"
                          data-id="<?= $val['release_id']; ?>"
                          title="Restore Release">
                          <i class="fas fa-undo-alt"></i>
                      </button>
                      <?php if($this->mres['member_type'] == '1'){ ?>
                      <button type="button"
                          class="action-btn permanentDelete"
                          data-id="<?= $val['release_id']; ?>"
                          title="Permanently Delete">
                          <i class="fas fa-trash-alt"></i>
                      </button>
                      <?php } ?>
                    </div>
                  </td>
                </tr>
                <?php } } else { ?>
                <tr>
                  <td colspan="10" class="text-center py-5">
                    <div class="text-muted">
                      <i class="fas fa-trash fa-3x mb-3" style="color: #fa009e; opacity:0.3;"></i>
                      <p><?= $this->config->item('no_record_found'); ?></p>
                    </div>
                  </td>
                </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Required JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    
    // ========== CSRF PROTECTION FIX ==========
    // Get CSRF token from meta tags (add these in your top_application.php head section)
    var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content');
    var csrfTokenValue = $('meta[name="csrf-token-value"]').attr('content');
    
    // If meta tags don't exist, try to get from PHP
    if (!csrfTokenName || !csrfTokenValue) {
        csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
        csrfTokenValue = '<?php echo $this->security->get_csrf_hash(); ?>';
    }
    
    // Function to get CSRF data for AJAX requests
    function getCsrfData() {
        var csrfData = {};
        csrfData[csrfTokenName] = csrfTokenValue;
        return csrfData;
    }
    
    // Function to update CSRF token from response
    function updateCsrfToken(response) {
        if (response && response.csrf_token) {
            csrfTokenValue = response.csrf_token;
            $('meta[name="csrf-token-value"]').attr('content', response.csrf_token);
        }
    }
    
    // Check if table has data
    var hasData = $('#trashTable tbody tr').length > 0;
    var table;
    
    if(hasData) {
        // Initialize DataTable
        table = $('#trashTable').DataTable({
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[0, 'asc']],
            "language": {
                "search": "<i class='fas fa-search'></i> Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries found",
                "zeroRecords": "No matching records found",
                "paginate": {
                    "first": "« First",
                    "last": "Last »",
                    "next": "→",
                    "previous": "←"
                }
            },
            "drawCallback": function() {
                $('#totalRecords').text(this.api().page.info().recordsTotal);
            }
        });
        
        // Update total records
        $('#totalRecords').text(table.page.info().recordsTotal);
        
        // Initialize DataTable buttons (hidden, triggered manually)
        new $.fn.dataTable.Buttons(table, {
            buttons: [
                { 
                    extend: 'excel', 
                    text: 'Excel', 
                    filename: 'trash_report_' + new Date().toISOString().slice(0,19),
                    title: 'Trash Report - ' + new Date().toLocaleString()
                },
                { 
                    extend: 'pdf', 
                    text: 'PDF', 
                    filename: 'trash_report_' + new Date().toISOString().slice(0,19), 
                    orientation: 'landscape',
                    title: 'Trash Report'
                },
                { 
                    extend: 'csv', 
                    text: 'CSV', 
                    filename: 'trash_report_' + new Date().toISOString().slice(0,19)
                },
                { 
                    extend: 'copy', 
                    text: 'Copy' 
                },
                { 
                    extend: 'print', 
                    text: 'Print' 
                }
            ]
        });
        table.buttons().container().appendTo('body');
        
        // Export handlers
        $('#exportExcelBtn').click(function() {
            table.button('.buttons-excel').trigger();
            Swal.fire({ 
                icon: 'success', 
                title: 'Exported!', 
                text: 'Excel file generated', 
                timer: 1500, 
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
        
        $('#exportPdfBtn').click(function() {
            table.button('.buttons-pdf').trigger();
            Swal.fire({ 
                icon: 'success', 
                title: 'PDF Created!', 
                text: 'PDF document generated', 
                timer: 1500, 
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
        
        $('#csvExportBtn').click(function() {
            table.button('.buttons-csv').trigger();
            Swal.fire({ 
                icon: 'success', 
                title: 'CSV Exported!', 
                text: 'CSV file saved', 
                timer: 1500, 
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
        
        $('#copyTableBtn').click(function() {
            table.button('.buttons-copy').trigger();
            Swal.fire({ 
                icon: 'success', 
                title: 'Copied!', 
                text: 'Data copied to clipboard', 
                timer: 1500, 
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
        
        $('#printTableBtn').click(function() {
            table.button('.buttons-print').trigger();
        });
    } else {
        $('#totalRecords').text('0');
    }
    
    // ========== RESTORE FUNCTION WITH CSRF FIX ==========
    $(document).on('click', '.restoreBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        let $btn = $(this);
        let id = $btn.data('id');
        
        // Store original content
        let originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        Swal.fire({
            title: 'Restore Release?',
            text: "This release will be moved back to your main library.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#fa009e',
            confirmButtonText: 'Yes, restore it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Prepare data with CSRF token
                let postData = {
                    release_id: id,
                    ajax_request: true
                };
                postData[csrfTokenName] = csrfTokenValue;
                
                $.ajax({
                    url: "<?= site_url('admin/album/release_restore'); ?>",
                    type: 'POST',
                    data: postData,
                    dataType: 'json',
                    cache: false,
                    success: function(res) {
                        // Update CSRF token if returned
                        if (res.csrf_token) {
                            updateCsrfToken(res);
                        }
                        
                        if(res.status == 1) {
                            Swal.fire({
                                title: 'Restored!',
                                text: res.msg,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire('Error!', res.msg, 'error');
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        let errorMsg = 'Something went wrong. Please try again.';
                        if (xhr.status === 403) {
                            errorMsg = 'CSRF token mismatch. Please refresh the page and try again.';
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            } else {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // ========== PERMANENT DELETE FUNCTION WITH CSRF FIX ==========
    $(document).on('click', '.permanentDelete', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        let $btn = $(this);
        let id = $btn.data('id');
        
        // Store original content
        let originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        Swal.fire({
            title: 'Permanently Delete?',
            text: "This action cannot be undone! All data will be lost forever.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete permanently!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Prepare data with CSRF token
                let postData = {
                    release_id: id,
                    ajax_request: true
                };
                postData[csrfTokenName] = csrfTokenValue;
                
                $.ajax({
                    url: "<?= site_url('admin/album/release_delete_permanent'); ?>",
                    type: 'POST',
                    data: postData,
                    dataType: 'json',
                    cache: false,
                    success: function(res) {
                        // Update CSRF token if returned
                        if (res.csrf_token) {
                            updateCsrfToken(res);
                        }
                        
                        if(res.status == 1) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: res.msg,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire('Error!', res.msg, 'error');
                            $btn.prop('disabled', false).html(originalHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        let errorMsg = 'Something went wrong. Please try again.';
                        if (xhr.status === 403) {
                            errorMsg = 'CSRF token mismatch. Please refresh the page and try again.';
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                });
            } else {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // ========== AUTO-REFRESH CSRF TOKEN ==========
    // Refresh CSRF token every 5 minutes to prevent expiration
    setInterval(function() {
        $.ajax({
            url: "<?= site_url('admin/album/refresh_csrf'); ?>",
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.csrf_token) {
                    csrfTokenValue = res.csrf_token;
                    $('meta[name="csrf-token-value"]').attr('content', res.csrf_token);
                }
            }
        });
    }, 300000); // 5 minutes
});
</script>

<?php $this->load->view("bottom_application");?>