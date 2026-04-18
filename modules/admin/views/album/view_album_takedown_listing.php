<?php 
   $this->load->view('top_application'); 
   $bdcm_array = array(
       array('heading'=>'Dashboard','url'=>'admin')
   );
   $site_title_text = escape_chars($this->config->item('site_name'));
   $posted_keyword = escape_chars($this->input->get_post('keyword',TRUE));
   $posted_status = escape_chars($this->input->get_post('status',TRUE)); 
   $sort_by = escape_chars($this->input->get_post('sort_by',TRUE));
   $sort_order = escape_chars($this->input->get_post('sort_order',TRUE));
   
   // Initialize all variables
   $page = isset($page) ? $page : 1;
   $per_page = isset($per_page) ? $per_page : 10;
   $total_records = isset($total_records) ? $total_records : 0;
   $offset = isset($offset) ? $offset : 0;
   $res = isset($res) ? $res : array();
   $active_count = isset($active_count) ? $active_count : 0;
   $pending_count = isset($pending_count) ? $pending_count : 0;
   $total_stores_count = isset($total_stores_count) ? $total_stores_count : 0;
   $page_links = isset($page_links) ? $page_links : '';
   
   function safe_html($string) {
       return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
   }
   ?>
<!DOCTYPE html>
<html>
   <head>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
   </head>
   <style>
      * {
      font-family: 'Inter', sans-serif;
      }
      /* Custom Color Variables */
      :root {
      --gradient-start: #de0c78;
      --gradient-end: #8830a2;
      --primary: #de0c78;
      --primary-dark: #b80a64;
      --primary-light: #fce4f2;
      --secondary: #8830a2;
      --secondary-light: #f3e8f8;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
      --info: #3b82f6;
      --dark: #111827;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --gray-900: #111827;
      --border-radius: 16px;
      --border-radius-sm: 12px;
      --box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
      --box-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }
      body {
      background: linear-gradient(135deg, #fef5f9 0%, #faf5fe 100%);
      }
      /* Gradient Utilities */
      .bg-gradient-custom {
      background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
      }
      .text-gradient-custom {
      background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      }
      /* Modern Card */
      .glass-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      transition: var(--transition);
      border: 1px solid rgba(255, 255, 255, 0.2);
      }
      .glass-card:hover {
      box-shadow: var(--box-shadow-lg);
      transform: translateY(-2px);
      }
      /* Stats Cards */
      .stat-card {
      background: white;
      border-radius: 20px;
      padding: 20px;
      transition: var(--transition);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(222, 12, 120, 0.1);
      }
      .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
      }
      .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 30px -10px rgba(222, 12, 120, 0.15);
      }
      .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      }
      /* Hero Search Section */
      .hero-search {
      background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
      border-radius: 24px;
      padding: 30px;
      margin-bottom: 30px;
      position: relative;
      overflow: hidden;
      }
      .hero-search::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
      pointer-events: none;
      }
      .search-wrapper {
      background: white;
      border-radius: 60px;
      padding: 8px;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
      }
      .search-wrapper .form-control {
      border: none;
      padding: 12px 20px;
      font-size: 16px;
      border-radius: 60px;
      }
      .search-wrapper .form-control:focus {
      box-shadow: none;
      }
      .search-wrapper .btn-primary {
      background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
      border: none;
      border-radius: 50px;
      padding: 10px 30px;
      }
      .search-wrapper .btn-primary:hover {
      background: linear-gradient(135deg, #b80a64 0%, #6e2682 100%);
      transform: translateY(-1px);
      }
      /* Modern Table */
      .modern-table-container {
      background: white;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: var(--box-shadow);
      }
      .data-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      }
      .data-table thead th {
      background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
      color: white;
      font-weight: 600;
      padding: 18px 16px;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: none;
      }
      .data-table tbody tr {
      transition: var(--transition);
      border-bottom: 1px solid var(--gray-200);
      }
      .data-table tbody tr:hover {
      background: linear-gradient(90deg, #fef5f9 0%, #ffffff 100%);
      transform: scale(1.01);
      box-shadow: 0 4px 12px rgba(222, 12, 120, 0.08);
      }
      .data-table tbody td {
      padding: 20px 16px;
      vertical-align: middle;
      }
      /* Status Badges */
      .badge-modern {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.3px;
      }
      .badge-success {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
      }
      .badge-warning {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
      }
      .badge-danger {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
      }
      .badge-info {
      background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
      }
      /* Action Buttons */
      .action-btn-modern {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: var(--transition);
      cursor: pointer;
      border: none;
      font-size: 14px;
      }
      .action-btn-view {
      background: #fce4f2;
      color: #de0c78;
      }
      .action-btn-edit {
      background: #fef3c7;
      color: #d97706;
      }
      .action-btn-restore {
      background: #e5e7eb;
      color: #4b5563;
      }
      .action-btn-delete {
      background: #fee2e2;
      color: #dc2626;
      }
      .action-btn-modern:hover {
      transform: translateY(-3px);
      filter: brightness(0.95);
      }
      /* Album Image */
      .album-image {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      object-fit: cover;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      transition: var(--transition);
      }
      .album-image:hover {
      transform: scale(1.05);
      box-shadow: 0 10px 15px -3px rgba(222, 12, 120, 0.2);
      }
      /* Bulk Actions Bar */
      .bulk-bar {
      background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
      border-radius: 16px;
      padding: 15px 20px;
      margin-bottom: 20px;
      display: none;
      animation: slideDown 0.4s ease;
      }
      @keyframes slideDown {
      from {
      opacity: 0;
      transform: translateY(-20px);
      }
      to {
      opacity: 1;
      transform: translateY(0);
      }
      }
      /* Buttons */
      .btn-outline-custom {
      border: 1px solid #de0c78;
      color: #de0c78;
      background: transparent;
      transition: var(--transition);
      }
      .btn-outline-custom:hover {
      background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
      color: white;
      border-color: transparent;
      }
      .btn-custom {
      background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
      color: white;
      border: none;
      transition: var(--transition);
      }
      .btn-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(222, 12, 120, 0.3);
      }
      /* Pagination */
      .pagination-modern {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 30px;
      }
      .pagination-modern .page-item {
      list-style: none;
      }
      .pagination-modern .page-link {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      background: white;
      color: var(--gray-700);
      text-decoration: none;
      transition: var(--transition);
      border: 1px solid var(--gray-200);
      }
      .pagination-modern .page-link:hover {
      background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
      color: white;
      transform: translateY(-2px);
      }
      .pagination-modern .active .page-link {
      background: linear-gradient(135deg, #de0c78 0%, #8830a2 100%);
      color: white;
      border: none;
      }
      /* Checkbox */
      .checkbox-modern {
      width: 20px;
      height: 20px;
      cursor: pointer;
      accent-color: #de0c78;
      border-radius: 6px;
      }
      /* Responsive */
      @media (max-width: 768px) {
      .data-table thead {
      display: none;
      }
      .data-table tbody tr {
      display: block;
      margin-bottom: 16px;
      border: 1px solid var(--gray-200);
      border-radius: 16px;
      background: white;
      }
      .data-table tbody td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 16px;
      border-bottom: 1px solid var(--gray-100);
      }
      .data-table tbody td:last-child {
      border-bottom: none;
      }
      .data-table tbody td:before {
      content: attr(data-label);
      font-weight: 600;
      margin-right: 15px;
      color: var(--gray-700);
      }
      }
      /* Animations */
      @keyframes fadeInUp {
      from {
      opacity: 0;
      transform: translateY(30px);
      }
      to {
      opacity: 1;
      transform: translateY(0);
      }
      }
      .animate-fade-in-up {
      animation: fadeInUp 0.6s ease forwards;
      }
      /* Tooltip */
      [data-tooltip] {
      position: relative;
      cursor: pointer;
      }
      [data-tooltip]:before {
      content: attr(data-tooltip);
      position: absolute;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%);
      background: var(--gray-900);
      color: white;
      padding: 6px 12px;
      border-radius: 8px;
      font-size: 12px;
      white-space: nowrap;
      display: none;
      z-index: 1000;
      margin-bottom: 8px;
      }
      [data-tooltip]:hover:before {
      display: block;
      }
      /* Link colors */
      a.text-primary {
      color: #de0c78 !important;
      }
      a.text-primary:hover {
      color: #b80a64 !important;
      }
      .text-primary {
      color: #de0c78 !important;
      }
      .btn-link.text-primary {
      color: #de0c78 !important;
      }
   </style>
   <div class="dash_outer">
      <div class="dash_container">
         <?php $this->load->view('view_left_sidebar'); ?>
         <div id="main-content" class="h-100">
            <?php $this->load->view('view_top_sidebar');?>
            <div class="container-fluid px-4 py-3">
               <!-- Header -->
               <div class="d-flex justify-content-between align-items-center mb-4 animate-fade-in-up">
                  <div>
                     <h1 class="display-6 fw-bold mb-2 text-gradient-custom">
                        <?php echo safe_html($heading_title ?? 'Takedown Releases');?>
                     </h1>
                     <p class="text-muted">Manage and monitor all your takedown releases in one centralized dashboard</p>
                  </div>
                  <?php echo navigation_breadcrumb($heading_title ?? '', $bdcm_array); ?>
               </div>
               <!-- Stats Cards -->
               <div class="row g-4 mb-4 animate-fade-in-up" style="animation-delay: 0.1s">
                  <div class="col-md-3">
                     <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                           <div>
                              <p class="text-muted mb-1 fs-6">Total Takedowns</p>
                              <h2 class="fw-bold mb-0"><?php echo number_format($total_records); ?></h2>
                              <small class="text-success mt-2 d-block">
                              <i class="fas fa-arrow-up"></i> +12% from last month
                              </small>
                           </div>
                           <div class="stat-icon" style="background: linear-gradient(135deg, #de0c7820 0%, #8830a220 100%);">
                              <i class="fas fa-trash-alt fa-2x" style="color: #de0c78;"></i>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                           <div>
                              <p class="text-muted mb-1">Active Releases</p>
                              <h2 class="fw-bold mb-0 text-success"><?php echo number_format($active_count); ?></h2>
                              <small class="text-muted mt-2 d-block">
                              <i class="fas fa-check-circle"></i> Live on stores
                              </small>
                           </div>
                           <div class="stat-icon" style="background: linear-gradient(135deg, #10b98120 0%, #05966920 100%);">
                              <i class="fas fa-check-circle fa-2x text-success"></i>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                           <div>
                              <p class="text-muted mb-1">Pending Review</p>
                              <h2 class="fw-bold mb-0 text-warning"><?php echo number_format($pending_count); ?></h2>
                              <small class="text-muted mt-2 d-block">
                              <i class="fas fa-clock"></i> Awaiting approval
                              </small>
                           </div>
                           <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b20 0%, #d9770620 100%);">
                              <i class="fas fa-clock fa-2x text-warning"></i>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="stat-card">
                        <div class="d-flex justify-content-between align-items-start">
                           <div>
                              <p class="text-muted mb-1">Distribution Stores</p>
                              <h2 class="fw-bold mb-0 text-info"><?php echo number_format($total_stores_count); ?></h2>
                              <small class="text-muted mt-2 d-block">
                              <i class="fas fa-store"></i> Global reach
                              </small>
                           </div>
                           <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f620 0%, #2563eb20 100%);">
                              <i class="fas fa-store fa-2x text-info"></i>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Hero Search Section -->
               <div class="hero-search animate-fade-in-up" style="animation-delay: 0.2s">
                  <?php echo form_open("", 'id="search_form" method="get"');?>
                  <div class="row align-items-center">
                     <div class="col-md-8 mb-3 mb-md-0">
                        <div class="search-wrapper">
                           <div class="input-group">
                              <span class="input-group-text bg-transparent border-0">
                              <i class="fas fa-search text-muted"></i>
                              </span>
                              <input type="text" name="keyword" class="form-control" 
                                 value="<?php echo safe_html($posted_keyword);?>" 
                                 placeholder="Search by album title, artist name, label, or catalogue number..."
                                 id="searchKeyword">
                              <select name="status" class="form-select w-auto border-0" style="max-width: 150px;" id="filterStatus">
                                 <option value="">All Status</option>
                                 <option value="1" <?php echo ($posted_status == '1') ? 'selected' : ''; ?>>Active</option>
                                 <option value="2" <?php echo ($posted_status == '2') ? 'selected' : ''; ?>>Pending</option>
                                 <option value="3" <?php echo ($posted_status == '3') ? 'selected' : ''; ?>>Inactive</option>
                              </select>
                              <button type="submit" class="btn btn-primary">
                              <i class="fas fa-search me-2"></i>Search
                              </button>
                              <?php if(!empty($posted_keyword) || !empty($posted_status)): ?>
                              <a href="<?php echo site_url('admin/album/takedown_album');?>" class="btn btn-outline-light">
                              <i class="fas fa-times me-1"></i>Clear
                              </a>
                              <?php endif; ?>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="d-flex justify-content-md-end gap-2">
                           <div class="dropdown">
                              <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                              <i class="fas fa-download me-1"></i> Export
                              </button>
                              <ul class="dropdown-menu">
                                 <li><a class="dropdown-item" href="#" onclick="exportToExcel()"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                                 <li><a class="dropdown-item" href="#" onclick="exportToCSV()"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                                 <li><a class="dropdown-item" href="#" onclick="printTable()"><i class="fas fa-print me-2"></i>Print</a></li>
                              </ul>
                           </div>
                           <div>
                              <label class="text-white me-2">Show:</label>
                              <?php echo front_record_per_page('per_page', 'per_page', 'form-select d-inline-block w-auto bg-white');?>
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php echo form_close();?>
               </div>
               <!-- Bulk Actions Bar -->
               <div class="bulk-bar" id="bulkActionsBar">
                  <div class="d-flex justify-content-between align-items-center">
                     <div class="text-white">
                        <i class="fas fa-check-circle fa-lg me-2"></i>
                        <strong><span id="selectedCount">0</span> items selected</strong>
                     </div>
                     <div class="btn-group gap-2">
                        <button class="btn btn-sm btn-light" onclick="bulkDelete()">
                        <i class="fas fa-trash-alt text-danger me-1"></i> Delete
                        </button>
                        <button class="btn btn-sm btn-light" onclick="bulkStatusChange('active')">
                        <i class="fas fa-check-circle text-success me-1"></i> Activate
                        </button>
                        <button class="btn btn-sm btn-light" onclick="bulkStatusChange('inactive')">
                        <i class="fas fa-ban text-warning me-1"></i> Deactivate
                        </button>
                        <button class="btn btn-sm btn-light" onclick="bulkExport()">
                        <i class="fas fa-download text-info me-1"></i> Export
                        </button>
                     </div>
                  </div>
               </div>
               <!-- Info Bar -->
               <?php if($total_records > 0): ?>
               <div class="d-flex justify-content-between align-items-center mb-3 animate-fade-in-up" style="animation-delay: 0.3s">
                  <div class="text-muted">
                     <i class="fas fa-info-circle me-1"></i>
                     Showing <strong><?php echo (($page-1)*$per_page)+1; ?></strong> to 
                     <strong><?php echo min($page*$per_page, $total_records); ?></strong> of 
                     <strong><?php echo number_format($total_records); ?></strong> entries
                  </div>
                  <div class="text-muted">
                     <i class="fas fa-music me-1"></i> Last updated: <?php echo date('F j, Y, g:i a'); ?>
                  </div>
               </div>
               <?php endif; ?>
               <!-- Main Table -->
               <div class="modern-table-container animate-fade-in-up" style="animation-delay: 0.4s">
                  <div class="table-responsive">
                     <table class="data-table" id="albumTable">
                        <thead>
                           <tr>
                              <th style="width: 40px;">
                                 <input type="checkbox" id="selectAll" class="checkbox-modern">
                              </th>
                              <th style="width: 50px;">#</th>
                              <th style="width: 90px;">Cover</th>
                              <th>Album Details</th>
                              <th>Artist / Label</th>
                              <th>Catalog Info</th>
                              <th>Distribution</th>
                              <th>Release Date</th>
                              <th>Status</th>
                              <th style="width: 140px;">Actions</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php
                              if(is_array($res) && !empty($res)){
                                  $i = $offset + 1;
                                  $album_status_arr = $this->config->item('album_status_arr');
                                  foreach ($res as $key => $val) {
                                      $artist_name = get_db_field_value('wl_artists','name',['pdl_id'=>$val['artist_name'] ?? '']);
                                      $total_territories = count_record('wl_release_territories',"release_id='".($val['release_id'] ?? '')."'");
                                      $total_stores = count_record('wl_release_stores',"release_id='".($val['release_id'] ?? '')."'");
                                      
                                      $status_value = $val['status'] ?? 0;
                                      $badge_class = '';
                                      $status_text = '';
                                      switch($status_value) {
                                          case '1': $badge_class = 'badge-success'; $status_text = 'Active'; break;
                                          case '2': $badge_class = 'badge-warning'; $status_text = 'Pending'; break;
                                          case '3': $badge_class = 'badge-danger'; $status_text = 'Inactive'; break;
                                          default: $badge_class = 'badge-info'; $status_text = 'Takendown';
                                      }
                                      ?>
                           <tr data-release-id="<?php echo safe_html($val['release_id'] ?? '');?>">
                              <td data-label="Select">
                                 <input type="checkbox" class="row-checkbox checkbox-modern" data-id="<?php echo safe_html($val['release_id'] ?? '');?>">
                              </td>
                              <td data-label="#"><?php echo $i;?></td>
                              <td data-label="Cover">
                                 <img src="<?php echo get_image('release', $val['release_banner'] ?? '', '230', '230', 'AR');?>" 
                                    class="album-image"
                                    alt="<?php echo safe_html($val['release_title'] ?? '');?>"
                                    onerror="this.src='<?php echo theme_url();?>images/default-album.png'">
                              </td>
                              <td data-label="Album Details">
                                 <div>
                                    <h6 class="fw-bold mb-1 text-primary"><?php echo safe_html($val['release_title'] ?? '');?></h6>
                                    <small class="text-muted">
                                    <i class="fas fa-hashtag"></i> ID: <?php echo safe_html($val['release_id'] ?? '');?>
                                    </small>
                                 </div>
                              </td>
                              <td data-label="Artist / Label">
                                 <div>
                                    <div><i class="fas fa-user-circle text-primary me-1"></i> <?php echo safe_html($artist_name);?></div>
                                    <div class="mt-1"><i class="fas fa-tag" style="color: #8830a2;"></i> <?php echo safe_html($val['label_name'] ?? '');?></div>
                                    <div class="mt-1"><i class="fas fa-user text-muted me-1"></i> <?php echo safe_html($val['first_name'] ?? '');?></div>
                                 </div>
                              </td>
                              <td data-label="Catalog Info">
                                 <div class="small">
                                    <div><strong>Cat#:</strong> <?php echo safe_html($val['producer_catalogue'] ?? '');?></div>
                                    <div class="mt-1"><strong>ISRC:</strong> <?php echo safe_html($val['isrc'] ?? '');?></div>
                                    <div class="mt-1"><strong>UPC:</strong> <?php echo safe_html($val['upc_ean'] ?? '');?></div>
                                 </div>
                              </td>
                              <td data-label="Distribution">
                                 <div class="text-center">
                                    <div class="fw-bold fs-5 text-primary"><?php echo (int)$total_territories;?></div>
                                    <small class="text-muted">Territories</small>
                                    <div class="mt-1">
                                       <a href="javascript:void(0);" 
                                          class="btn btn-sm btn-outline-custom view-stores" 
                                          data-release-id="<?php echo md5($val['release_id'] ?? ''); ?>">
                                       <i class="fas fa-store"></i> <?php echo (int)$total_stores;?> Stores
                                       </a>
                                    </div>
                                 </div>
                              </td>
                              <td data-label="Release Date">
                                 <div class="text-center">
                                    <div class="fw-bold"><?php echo getDateFormat($val['original_release_date_of_music'] ?? '', 1);?></div>
                                    <small class="text-muted">
                                    <i class="far fa-clock"></i> <?php echo getDateFormat($val['created_date'] ?? '', 7);?>
                                    </small>
                                 </div>
                              </td>
                              <td data-label="Status">
                                 <span class="badge-modern <?php echo $badge_class; ?>">
                                 <i class="fas <?php echo $status_value == 1 ? 'fa-check-circle' : ($status_value == 2 ? 'fa-clock' : 'fa-times-circle'); ?>"></i>
                                 <?php echo $status_text; ?>
                                 </span>
                              </td>
                              <td data-label="Actions">
                                 <div class="d-flex gap-2">
                                    <a href="javascript:void(0);"
                                       class="action-btn-modern action-btn-view view-release"
                                       data-release-id="<?php echo md5($val['release_id'] ?? ''); ?>"
                                       data-tooltip="View Details">
                                    <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo site_url('admin/release/new_release/'.md5($val['release_id'] ?? '').'?album_type='.($val['album_type'] ?? ''));?>"
                                       class="action-btn-modern action-btn-edit"
                                       data-tooltip="Edit Album">
                                    <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if(isset($this->mres['member_type']) && $this->mres['member_type'] == 1): ?>
                                    <a href="<?php echo site_url('admin/album/album_status/'.md5($val['release_id'] ?? '').'?al_status=restore');?>"
                                       class="action-btn-modern action-btn-restore"
                                       data-tooltip="Restore">
                                    <i class="fas fa-undo-alt"></i>
                                    </a>
                                    <button type="button"
                                       class="action-btn-modern action-btn-delete deleteRelease"
                                       data-id="<?php echo safe_html($val['release_id'] ?? ''); ?>"
                                       data-title="<?php echo safe_html($val['release_title'] ?? ''); ?>"
                                       data-tooltip="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <?php endif; ?>
                                 </div>
                              </td>
                           </tr>
                           <?php 
                              $i++;
                              } 
                              } else { ?>
                           <tr>
                              <td colspan="10">
                                 <div class="text-center py-5">
                                    <i class="fas fa-music fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">No takedown releases found</h5>
                                    <p class="text-muted">Try adjusting your search criteria</p>
                                 </div>
                              </td>
                           </tr>
                           <?php } ?>
                        </tbody>
                     </table>
                  </div>
               </div>
               <!-- Pagination -->
               <?php if(!empty($page_links)): ?>
               <div class="mt-4">
                  <?php echo $page_links; ?>
               </div>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </div>
   <!-- Scripts -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
   <script type="text/javascript">
      $(document).ready(function() {
          // Initialize Fancybox
          Fancybox.bind('[data-fancybox]', {
              iframe: { preload: false }
          });
      
          // View release
          $('.view-release').on('click', function(e) {
              e.preventDefault();
              var releaseId = $(this).data('release-id');
              if(releaseId) {
                  Fancybox.show([{
                      src: "<?php echo site_url('admin/release/view_meta_release/'); ?>/" + releaseId,
                      type: 'iframe'
                  }]);
              }
          });
      
          // View stores
          $('.view-stores').on('click', function(e) {
              e.preventDefault();
              var releaseId = $(this).data('release-id');
              if(releaseId) {
                  Fancybox.show([{
                      src: "<?php echo site_url('admin/album/view_stored/'); ?>/" + releaseId,
                      type: 'iframe'
                  }]);
              }
          });
      
          // Debounced search
          let searchTimeout;
          $('#searchKeyword').on('keyup', function() {
              clearTimeout(searchTimeout);
              searchTimeout = setTimeout(() => $('#search_form').submit(), 500);
          });
      
          $('#filterStatus').on('change', function() {
              $('#search_form').submit();
          });
      
          // Select All
          $('#selectAll').on('change', function() {
              $('.row-checkbox').prop('checked', $(this).prop('checked'));
              updateBulkActions();
          });
      
          $(document).on('change', '.row-checkbox', function() {
              updateBulkActions();
          });
      
          function updateBulkActions() {
              var count = $('.row-checkbox:checked').length;
              if(count > 0) {
                  $('#bulkActionsBar').fadeIn();
                  $('#selectedCount').text(count);
              } else {
                  $('#bulkActionsBar').fadeOut();
              }
          }
      
          // Delete with SweetAlert
          $(document).on('click', '.deleteRelease', function(e) {
              e.preventDefault();
              let release_id = $(this).data('id');
              let title = $(this).data('title');
      
              Swal.fire({
                  title: 'Move to Trash?',
                  text: `"${title || 'This album'}" will be moved to trash. You can restore it later.`,
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#de0c78',
                  confirmButtonText: 'Yes, trash it!',
                  cancelButtonText: 'Cancel',
                  showLoaderOnConfirm: true,
                  preConfirm: () => {
                      return $.ajax({
                          url: "<?php echo site_url('admin/album/release_delete'); ?>",
                          type: "POST",
                          data: { release_id: release_id },
                          dataType: "json"
                      });
                  }
              }).then((result) => {
                  if (result.isConfirmed && result.value?.status == 1) {
                      Swal.fire('Deleted!', result.value.msg, 'success').then(() => location.reload());
                  } else if(result.value?.status != 1) {
                      Swal.fire('Error!', result.value?.msg || 'Something went wrong', 'error');
                  }
              });
          });
      
          // Bulk operations
          window.bulkDelete = function() {
              var selectedIds = $('.row-checkbox:checked').map(function() { return $(this).data('id'); }).get();
              if(selectedIds.length === 0) {
                  Swal.fire('Warning', 'Please select at least one item', 'warning');
                  return;
              }
              Swal.fire({
                  title: 'Bulk Delete',
                  text: `Delete ${selectedIds.length} item(s)? This action can be undone.`,
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#de0c78',
                  confirmButtonText: 'Yes, delete!'
              }).then((result) => {
                  if(result.isConfirmed) {
                      $.ajax({
                          url: "<?php echo site_url('admin/album/bulk_delete'); ?>",
                          type: "POST",
                          data: { ids: selectedIds },
                          dataType: "json",
                          success: (res) => {
                              if(res?.status == 1) {
                                  Swal.fire('Deleted!', res.msg, 'success').then(() => location.reload());
                              } else {
                                  Swal.fire('Error!', res?.msg || 'Something went wrong', 'error');
                              }
                          }
                      });
                  }
              });
          };
      
          window.bulkStatusChange = function(status) {
              var selectedIds = $('.row-checkbox:checked').map(function() { return $(this).data('id'); }).get();
              if(selectedIds.length === 0) {
                  Swal.fire('Warning', 'Please select at least one item', 'warning');
                  return;
              }
              $.ajax({
                  url: "<?php echo site_url('admin/album/bulk_status'); ?>",
                  type: "POST",
                  data: { ids: selectedIds, status: status },
                  dataType: "json",
                  success: (res) => {
                      if(res?.status == 1) {
                          Swal.fire('Success!', res.msg, 'success').then(() => location.reload());
                      } else {
                          Swal.fire('Error!', res?.msg || 'Something went wrong', 'error');
                      }
                  }
              });
          };
      
          // Export functions
          window.exportToExcel = function() {
              window.location.href = "<?php echo site_url('admin/album/export_excel'); ?>" + window.location.search;
          };
      
          window.exportToCSV = function() {
              window.location.href = "<?php echo site_url('admin/album/export_csv'); ?>" + window.location.search;
          };
      
          window.printTable = function() {
              var printContent = document.querySelector('.modern-table-container').innerHTML;
              var printWindow = window.open('', '_blank');
              printWindow.document.write(`
                  <html><head><title>Takedown Releases List</title>
                  <style>body{font-family:Arial;margin:20px} table{width:100%;border-collapse:collapse} th,td{border:1px solid #ddd;padding:8px} th{background:#f2f2f2}</style>
                  </head><body>${printContent}</body></html>
              `);
              printWindow.document.close();
              printWindow.print();
              printWindow.close();
          };
      
          // Keyboard shortcuts
          $(document).on('keydown', function(e) {
              if(e.ctrlKey && e.key === 'f') { e.preventDefault(); $('#searchKeyword').focus(); }
              if(e.ctrlKey && e.key === 'a') { e.preventDefault(); $('#selectAll').click(); }
          });
      });
   </script>
   <?php $this->load->view("bottom_application");?>