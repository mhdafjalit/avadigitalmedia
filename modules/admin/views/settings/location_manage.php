<?php 
   $this->load->view('top_application'); 
   
   $bdcm_array = array(
       array('heading'=>'Dashboard','url'=>'admin')
   );
   
   $posted_keyword = $this->input->get_post('keyword',TRUE);
   $posted_keyword = escape_chars($posted_keyword);
   
   $posted_status = $this->input->get_post('status',TRUE);
   $posted_status = escape_chars($posted_status); 
   ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<!-- Required Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<!-- PDF export libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- DataTables Bootstrap 4 compatibility -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<style>
   * {
   font-family: 'Inter', sans-serif;
   }
   /* Modern Card Design */
   .modern-container {
   margin: 0 auto;
   padding: 20px;
   }
   /* Accordion Style Cards */
   .location-accordion {
   background: #ffffff;
   border-radius: 24px;
   box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
   margin-bottom: 20px;
   overflow: hidden;
   border: 1px solid rgba(123, 31, 162, 0.1);
   transition: all 0.3s ease;
   }
   .location-accordion:hover {
   box-shadow: 0 30px 60px rgba(123, 31, 162, 0.15);
   border-color: rgba(123, 31, 162, 0.3);
   }
   /* Accordion Header */
   .accordion-header {
   background: linear-gradient(135deg, #ffffff 0%, #faf5ff 100%);
   padding: 20px 30px;
   cursor: pointer;
   display: flex;
   align-items: center;
   justify-content: space-between;
   border-bottom: 2px solid transparent;
   transition: all 0.3s;
   }
   .accordion-header.active {
   border-bottom-color: #7b1fa2;
   background: linear-gradient(135deg, #faf5ff 0%, #f3e5f5 100%);
   }
   .header-left {
   display: flex;
   align-items: center;
   gap: 20px;
   }
   .header-icon {
   width: 50px;
   height: 50px;
   background: linear-gradient(135deg, #7b1fa2, #9c27b0);
   border-radius: 16px;
   display: flex;
   align-items: center;
   justify-content: center;
   color: white;
   font-size: 24px;
   box-shadow: 0 10px 20px rgba(123, 31, 162, 0.3);
   }
   .header-title {
   font-size: 1.5rem;
   font-weight: 700;
   color: #2d3748;
   margin: 0;
   }
   .header-title small {
   font-size: 0.9rem;
   color: #718096;
   font-weight: 400;
   margin-left: 10px;
   }
   .header-badge {
   background: rgba(123, 31, 162, 0.1);
   color: #7b1fa2;
   padding: 8px 16px;
   border-radius: 50px;
   font-weight: 600;
   font-size: 0.9rem;
   display: flex;
   align-items: center;
   gap: 8px;
   }
   .header-badge i {
   font-size: 1.1rem;
   }
   .expand-btn {
   width: 40px;
   height: 40px;
   background: white;
   border: 2px solid #7b1fa2;
   border-radius: 12px;
   display: flex;
   align-items: center;
   justify-content: center;
   color: #7b1fa2;
   font-size: 20px;
   transition: all 0.3s;
   cursor: pointer;
   }
   .expand-btn:hover {
   background: #7b1fa2;
   color: white;
   transform: rotate(90deg);
   }
   .expand-btn.active {
   background: #7b1fa2;
   color: white;
   transform: rotate(45deg);
   }
   /* Accordion Content */
   .accordion-content {
   display: none;
   padding: 30px;
   background: white;
   border-top: 1px solid rgba(123, 31, 162, 0.1);
   }
   .accordion-content.show {
   display: block;
   }
   /* Search Section */
   .search-mini {
   background: #f8fafd;
   border-radius: 16px;
   padding: 20px;
   margin-bottom: 25px;
   border: 1px solid #eef2f7;
   }
   .search-mini .form-control, 
   .search-mini .form-select {
   border-radius: 12px;
   border: 2px solid #eef2f7;
   padding: 10px 15px;
   font-size: 0.95rem;
   }
   .search-mini .form-control:focus,
   .search-mini .form-select:focus {
   border-color: #7b1fa2;
   box-shadow: 0 0 0 3px rgba(123, 31, 162, 0.1);
   }
   /* Buttons */
   .btn-modern {
   padding: 10px 20px;
   border-radius: 12px;
   font-weight: 600;
   font-size: 0.95rem;
   transition: all 0.3s;
   border: none;
   display: inline-flex;
   align-items: center;
   gap: 8px;
   }
   .btn-purple-modern {
   background: linear-gradient(135deg, #7b1fa2, #9c27b0);
   color: white;
   box-shadow: 0 4px 15px rgba(123, 31, 162, 0.3);
   }
   .btn-purple-modern:hover {
   background: linear-gradient(135deg, #6a1b9a, #8e24aa);
   color: white;
   transform: translateY(-2px);
   }
   .btn-outline-modern {
   background: white;
   border: 2px solid #7b1fa2;
   color: #7b1fa2;
   }
   .btn-outline-modern:hover {
   background: #7b1fa2;
   color: white;
   }
   /* Add Button */
   .add-button {
   background: linear-gradient(135deg, #28a745, #34ce57);
   color: white;
   border: none;
   border-radius: 50px;
   padding: 12px 25px;
   font-weight: 600;
   display: inline-flex;
   align-items: center;
   gap: 10px;
   box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
   transition: all 0.3s;
   }
   .add-button:hover {
   transform: translateY(-2px);
   box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
   }
   /* Switch Toggle */
   .switch {
   position: relative;
   display: inline-block;
   width: 60px;
   height: 28px;
   }
   .switch input {
   display: none;
   }
   .slider {
   position: absolute;
   cursor: pointer;
   top: 0;
   left: 0;
   right: 0;
   bottom: 0;
   background: #e0e0e0;
   transition: .3s;
   border-radius: 34px;
   }
   .slider:before {
   position: absolute;
   content: "";
   height: 22px;
   width: 22px;
   left: 3px;
   bottom: 3px;
   background: white;
   transition: .3s;
   border-radius: 50%;
   box-shadow: 0 2px 5px rgba(0,0,0,0.2);
   }
   input:checked + .slider {
   background: linear-gradient(135deg, #dd0d79, #7b35a7);
   }
   input:checked + .slider:before {
   transform: translateX(32px);
   }
   /* Action Icons */
   .action-icons {
   display: flex;
   gap: 10px;
   flex-wrap: wrap;
   }
   .action-icon {
   width: 35px;
   height: 35px;
   border-radius: 10px;
   display: flex;
   align-items: center;
   justify-content: center;
   background: #f8f9fa;
   color: #4a5568;
   transition: all 0.3s;
   text-decoration: none;
   }
   .action-icon:hover {
   transform: translateY(-2px);
   text-decoration: none;
   }
   .action-icon.edit:hover {
   background: #e3f2fd;
   color: #1976d2;
   }
   .action-icon.delete:hover {
   background: #ffebee;
   color: #dc3545;
   }
   .action-icon.featured:hover {
   background: #fff3e0;
   color: #f57c00;
   }
   .action-icon.popular:hover {
   background: #e8f5e9;
   color: #388e3c;
   }
   .action-icon.premium:hover {
   background: #e0f7fa;
   color: #0097a7;
   }
   /* DataTables Custom - Hide DataTables pagination */
   .dataTables_paginate {
   display: none !important;
   }
   .dataTables_info {
   display: none !important;
   }
   .dataTables_length {
   display: none !important;
   }
   /* Keep only the export buttons visible */
   .dt-buttons {
   margin-bottom: 20px;
   display: flex;
   gap: 8px;
   flex-wrap: wrap;
   }
   .dt-button {
   background: linear-gradient(135deg, #7b1fa2, #9c27b0) !important;
   color: white !important;
   border: none !important;
   border-radius: 10px !important;
   padding: 8px 16px !important;
   font-size: 0.9rem !important;
   font-weight: 500 !important;
   transition: all 0.3s !important;
   }
   .dt-button:hover {
   background: linear-gradient(135deg, #6a1b9a, #8e24aa) !important;
   transform: translateY(-2px) !important;
   }
   /* Modal Styling */
   .modern-modal .modal-content {
   border: none;
   border-radius: 24px;
   overflow: hidden;
   }
   .modal-header-modern {
   background: linear-gradient(135deg, #7b1fa2, #9c27b0);
   color: white;
   padding: 25px 30px;
   border: none;
   }
   .modal-body-modern {
   padding: 30px;
   }
   .modal-footer-modern {
   padding: 20px 30px;
   border-top: 1px solid #eef2f7;
   }
   .modern-input,
   .modern-select,
   .modern-textarea {
   width: 100%;
   padding: 12px 18px;
   border: 2px solid #eef2f7;
   border-radius: 14px;
   transition: all 0.3s;
   font-size: 0.95rem;
   }
   .modern-input:focus,
   .modern-select:focus,
   .modern-textarea:focus {
   border-color: #7b1fa2;
   outline: none;
   box-shadow: 0 0 0 4px rgba(123, 31, 162, 0.1);
   }
   .modern-textarea {
   min-height: 100px;
   resize: vertical;
   }
   /* Checkbox Grid */
   .checkbox-grid {
   display: flex;
   gap: 20px;
   flex-wrap: wrap;
   align-items: center;
   }
   .checkbox-item {
   display: flex;
   align-items: center;
   gap: 8px;
   }
   .checkbox-item input[type="checkbox"] {
   width: 18px;
   height: 18px;
   cursor: pointer;
   }
   .checkbox-item label {
   cursor: pointer;
   color: #4a5568;
   }
   /* Pagination */
   .pagination {
   justify-content: center;
   margin-top: 20px;
   }
   .page-item.active .page-link {
   background: linear-gradient(135deg, #7b1fa2, #9c27b0);
   border-color: #7b1fa2;
   }
   .page-link {
   color: #7b1fa2;
   border-radius: 8px;
   margin: 0 3px;
   }
   /* Badge Styles */
   .badge-featured {
   background: #f57c00;
   color: white;
   padding: 4px 8px;
   border-radius: 50px;
   font-size: 0.7rem;
   font-weight: 600;
   }
   .badge-popular {
   background: #388e3c;
   color: white;
   padding: 4px 8px;
   border-radius: 50px;
   font-size: 0.7rem;
   font-weight: 600;
   }
   .badge-premium {
   background: #0097a7;
   color: white;
   padding: 4px 8px;
   border-radius: 50px;
   font-size: 0.7rem;
   font-weight: 600;
   }
   /* Grouped Tables Styling */
   .country-group .bg-light,
   .state-group .bg-light {
   border-left: 5px solid #7b1fa2;
   background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
   }
   .country-group h5,
   .state-group h5 {
   font-size: 1.2rem;
   margin-bottom: 0;
   }
   .state-subtable thead th,
   .city-subtable thead th {
   background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
   color: #495057;
   font-size: 0.9rem;
   }
   .country-group .badge.bg-primary,
   .state-group .badge.bg-success {
   width: 35px;
   height: 35px;
   display: flex;
   align-items: center;
   justify-content: center;
   border-radius: 50%;
   font-weight: bold;
   }
   .country-group:last-child,
   .state-group:last-child {
   margin-bottom: 0 !important;
   }
   .country-group:nth-child(even) .bg-light,
   .state-group:nth-child(even) .bg-light {
   background: linear-gradient(135deg, #f1f3f5 0%, #e2e6ea 100%) !important;
   }
   /* Responsive */
   @media (max-width: 768px) {
   .accordion-header {
   padding: 15px;
   }
   .header-icon {
   width: 40px;
   height: 40px;
   font-size: 18px;
   }
   .header-title {
   font-size: 1.2rem;
   }
   .accordion-content {
   padding: 15px;
   }
   .dt-buttons {
   justify-content: center;
   }
   .checkbox-grid {
   flex-direction: column;
   align-items: flex-start;
   gap: 10px;
   }
   }
</style>
<div class="dash_outer">
   <div class="dash_container">
      <?php $this->load->view('view_left_sidebar'); ?>
      <div id="main-content" class="h-100">
         <?php $this->load->view('view_top_sidebar'); ?>
         <div class="top_sec d-flex justify-content-between align-items-center mb-4">
            <div>
               <h1 class="page-title" style="font-size: 2rem; font-weight: 700; color: #2d3748;">
                  <i class="bi bi-geo-alt-fill me-2" style="color: #7b1fa2;"></i>
                  <?php echo $heading_title;?>
               </h1>
               <?php echo navigation_breadcrumb($heading_title,$bdcm_array); ?>
            </div>
            <div class="btn-group">
               <a href="<?php echo site_url('admin/settingcontroller/export_countries');?>" class="btn-modern btn-outline-modern me-2" title="Export Countries">
               <i class="bi bi-file-earmark-spreadsheet"></i> Export Countries
               </a>
               <a href="<?php echo site_url('admin/settingcontroller/export_states');?>" class="btn-modern btn-outline-modern me-2" title="Export States">
               <i class="bi bi-file-earmark-spreadsheet"></i> Export States
               </a>
               <a href="<?php echo site_url('admin/settingcontroller/export_cities');?>" class="btn-modern btn-outline-modern" title="Export Cities">
               <i class="bi bi-file-earmark-spreadsheet"></i> Export Cities
               </a>
            </div>
         </div>
         <div class="main-content-inner modern-container">
            <!-- COUNTRY ACCORDION -->
            <div class="location-accordion">
               <div class="accordion-header" data-target="countryContent">
                  <div class="header-left">
                     <div class="header-icon">
                        <i class="bi bi-globe2"></i>
                     </div>
                     <div>
                        <h3 class="header-title">
                           Countries
                           <small>Manage all countries</small>
                        </h3>
                        <div class="header-badge">
                           <i class="bi bi-database"></i>
                           Total: <?php echo isset($total_countries) ? $total_countries : 0; ?> records
                           <?php if(isset($countries) && isset($total_countries) && count($countries) < $total_countries){ ?>
                           <small class="text-muted">(Showing <?php echo count($countries); ?> on page 
                           <?php 
$segment = $this->uri->segment(4);
$current_page = ($segment && is_numeric($segment)) ? (int)$segment : 1;
echo $current_page; 
?>)</small>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
                  <div class="expand-btn">
                     <i class="bi bi-plus-lg"></i>
                  </div>
               </div>
               <div class="accordion-content" id="countryContent">
                  <!-- Mini Search for Country -->
                  <div class="search-mini">
                     <div class="row g-3">
                        <div class="col-md-4">
                           <input type="text" class="form-control" id="countrySearch" placeholder="Search countries...">
                        </div>
                        <div class="col-md-3">
                           <select class="form-select" id="countryStatus">
                              <option value="">All Status</option>
                              <option value="1">Active</option>
                              <option value="0">Inactive</option>
                           </select>
                        </div>
                        <div class="col-md-3">
                           <button class="btn-modern btn-purple-modern w-100" onclick="filterCountry()">
                           <i class="bi bi-search"></i> Search
                           </button>
                        </div>
                        <div class="col-md-2">
                           <button class="add-button w-100 create_top text-white rounded-5 fw-medium trans_eff align-middle" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                           <i class="bi bi-plus-lg"></i> Add
                           </button>
                        </div>
                     </div>
                  </div>
                  <!-- Country Table -->
                  <table class="table table-bordered" id="countryTable">
                     <thead>
                        <tr>
                           <th width="60">ID</th>
                           <th>Country Name</th>
                           <th>ISO Code</th>
                           <th>Currency</th>
                           <th>TimeZone</th>
                           <th>Featured</th>
                           <th>Premium Ads</th>
                           <th width="100">Status</th>
                           <th width="200">Actions</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if(!empty($countriesData)){ 
                           foreach($countriesData as $c){ ?>
                        <tr>
                           <td><span class="badge bg-light text-dark">#<?php echo $c['id'];?></span></td>
                           <td>
                              <b><?php echo $c['country_name'];?></b>
                              <?php if(!empty($c['country_temp_name'])){ ?>
                              <br><small class="text-muted"><?php echo $c['country_temp_name'];?></small>
                              <?php } ?>
                           </td>
                           <td>
                              <?php if(!empty($c['country_iso_code_2'])){ ?>
                              <span class="badge bg-info"><?php echo $c['country_iso_code_2'];?></span>
                              <?php } ?>
                              <?php if(!empty($c['country_iso_code_3'])){ ?>
                              <span class="badge bg-secondary"><?php echo $c['country_iso_code_3'];?></span>
                              <?php } ?>
                           </td>
                           <td><?php echo $c['cont_currency'] ?: '-';?></td>
                           <td><?php echo $c['TimeZone'] ?: '-';?></td>
                           <td>
                              <?php if($c['is_feature'] == '1'){ ?>
                              <span class="badge-featured">Featured</span>
                              <?php } else { ?>
                              <span class="text-muted">-</span>
                              <?php } ?>
                           </td>
                           <td>
                              <?php if($c['premimum_ads_avl'] == '1'){ ?>
                              <span class="badge-premium">Available</span>
                              <?php } else { ?>
                              <span class="text-muted">-</span>
                              <?php } ?>
                           </td>
                           <td>
                              <label class="switch">
                              <input type="checkbox" class="status_toggle " data-type="country" data-id="<?php echo $c['id'];?>" <?php echo ($c['status']==1)?'checked':'';?>>
                              <span class="slider"></span>
                              </label>
                           </td>
                           <td>
                              <div class="action-icons">
                                 <a href="javascript:void(0)" class="action-icon toggle_featured" data-type="country" data-id="<?php echo $c['id'];?>" data-value="<?php echo $c['is_feature'];?>" title="Toggle Featured">
                                 <i class="bi bi-star<?php echo ($c['is_feature']=='1')?'-fill':'';?>"></i>
                                 </a>
                                 <a href="javascript:void(0)" class="action-icon edit edit_country" data-id="<?php echo $c['id'];?>" title="Edit">
                                 <i class="bi bi-pencil"></i>
                                 </a>
                                 <a href="javascript:void(0)" class="action-icon delete delete_btn" data-url="<?php echo site_url('admin/delete_country/'.$c['id']);?>" title="Delete">
                                 <i class="bi bi-trash"></i>
                                 </a>
                              </div>
                           </td>
                        </tr>
                        <?php }} ?>
                     </tbody>
                  </table>
                  <!-- Pagination with Summary -->
                  <?php if(isset($country_pagination)){ ?>
                  <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
                     <div class="text-muted mb-2 mb-md-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Showing <strong><?php echo count($countries); ?></strong> of <strong><?php echo $total_countries; ?></strong> countries 
                        (Page <strong><?php 
$segment = $this->uri->segment(4); // Changed from segment(3) to segment(4) for SettingController
$current_page = ($segment && is_numeric($segment)) ? (int)$segment : 1;
echo $current_page; 
?></strong> 
                        of <strong><?php echo ceil($total_countries/24); ?></strong>)
                     </div>
                     <div>
                        <?php echo $country_pagination; ?>
                     </div>
                  </div>
                  <?php } ?>
               </div>
            </div>
            <!-- STATE ACCORDION - Grouped by Country -->
            <div class="location-accordion">
               <div class="accordion-header" data-target="stateContent">
                  <div class="header-left">
                     <div class="header-icon">
                        <i class="bi bi-building"></i>
                     </div>
                     <div>
                        <h3 class="header-title">
                           States
                           <small>Manage all states</small>
                        </h3>
                        <div class="header-badge">
                           <i class="bi bi-database"></i>
                           Total: <?php echo isset($total_states) ? $total_states : 0; ?> records
                           <?php if(isset($states) && isset($total_states) && count($states) < $total_states){ ?>
                           <small class="text-muted">(Showing <?php echo count($states); ?> on page 
                           <?php 
$segment = $this->uri->segment(4);
$current_page = ($segment && is_numeric($segment)) ? (int)$segment : 1;
echo $current_page; 
?>)</small>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
                  <div class="expand-btn">
                     <i class="bi bi-plus-lg"></i>
                  </div>
               </div>
               <div class="accordion-content" id="stateContent">
                  <!-- Mini Search for State -->
                  <div class="search-mini">
                     <div class="row g-3">
                        <div class="col-md-3">
                           <input type="text" class="form-control" id="stateSearch" placeholder="Search states...">
                        </div>
                        <div class="col-md-3">
                           <select class="form-select" id="stateCountry">
                              <option value="">All Countries</option>
                              <?php if(!empty($countriesData)){ foreach($countriesData as $c){ ?>
                              <option value="<?php echo $c['id'];?>"><?php echo $c['country_name'];?></option>
                              <?php }} ?>
                           </select>
                        </div>
                        <div class="col-md-2">
                           <select class="form-select" id="stateStatus">
                              <option value="">All Status</option>
                              <option value="1">Active</option>
                              <option value="0">Inactive</option>
                           </select>
                        </div>
                        <div class="col-md-2">
                           <button class="btn-modern btn-purple-modern w-100" onclick="filterState()">
                           <i class="bi bi-search"></i> Search
                           </button>
                        </div>
                        <div class="col-md-2">
                           <button class="add-button w-100 create_top text-white rounded-5 fw-medium trans_eff align-middle" data-bs-toggle="modal" data-bs-target="#addStateModal">
                           <i class="bi bi-plus-lg"></i> Add
                           </button>
                        </div>
                     </div>
                  </div>
                  <!-- State Table - Grouped by Country -->
                  <?php 
                     if(!empty($states)){
                         // Group states by country
                         $grouped_states = [];
                         foreach($states as $state){
                             $grouped_states[$state['country_name']][] = $state;
                         }
                         
                         $country_counter = 1;
                         foreach($grouped_states as $country_name => $country_states){
                     ?>
                  <div class="country-group mb-4">
                     <div class="bg-light p-3 rounded mb-3 d-flex align-items-center">
                        <span class="badge bg-primary me-3" style="font-size: 1rem;"><?php echo $country_counter; ?></span>
                        <h5 class="mb-0 text-purple fw-bold">
                           <i class="bi bi-flag-fill me-2"></i><?php echo $country_name; ?>
                        </h5>
                        <span class="ms-auto badge bg-info"><?php echo count($country_states); ?> States</span>
                     </div>
                     <div class="table-responsive">
                        <table class="table table-bordered table-striped state-subtable">
                           <thead>
                              <tr>
                                 <th width="60">#</th>
                                 <th>State Name</th>
                                 <th>Temp Name</th>
                                 <th>Popular</th>
                                 <th>Created</th>
                                 <th width="100">Status</th>
                                 <th width="150">Actions</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php 
                                 $state_counter = 1;
                                 foreach($country_states as $s){ 
                                 ?>
                              <tr>
                                 <td><span class="badge bg-light text-dark"><?php echo $state_counter; ?></span></td>
                                 <td><b><?php echo $s['title'];?></b></td>
                                 <td><?php echo $s['temp_title'] ?: '-';?></td>
                                 <td>
                                    <?php if($s['is_state_popular'] == '1'){ ?>
                                    <span class="badge-popular">Popular</span>
                                    <?php } else { ?>
                                    <span class="text-muted">-</span>
                                    <?php } ?>
                                 </td>
                                 <td><?php echo date('d-m-Y', strtotime($s['created_at']));?></td>
                                 <td>
                                    <label class="switch">
                                    <input type="checkbox" class="status_toggle" data-type="state" data-id="<?php echo $s['id'];?>" <?php echo ($s['status']==1)?'checked':'';?>>
                                    <span class="slider"></span>
                                    </label>
                                 </td>
                                 <td>
                                    <div class="action-icons">
                                       <a href="javascript:void(0)" class="action-icon toggle_popular" data-type="state" data-id="<?php echo $s['id'];?>" data-value="<?php echo $s['is_state_popular'];?>" title="Toggle Popular">
                                       <i class="bi bi-star<?php echo ($s['is_state_popular']=='1')?'-fill':'';?>"></i>
                                       </a>
                                       <a href="javascript:void(0)" class="action-icon edit edit_state" data-id="<?php echo $s['id'];?>" title="Edit">
                                       <i class="bi bi-pencil"></i>
                                       </a>
                                       <a href="javascript:void(0)" class="action-icon delete delete_btn" data-url="<?php echo site_url('admin/settingcontroller/delete_state/'.$s['id']);?>" title="Delete">
                                       <i class="bi bi-trash"></i>
                                       </a>
                                    </div>
                                 </td>
                              </tr>
                              <?php 
                                 $state_counter++;
                                 } 
                                 ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <?php 
                     $country_counter++;
                     }
                     } else { 
                     ?>
                  <div class="alert alert-info text-center">No states found</div>
                  <?php } ?>
                  <!-- Pagination with Summary -->
                  <?php if(isset($state_pagination)){ ?>
                  <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
                     <div class="text-muted mb-2 mb-md-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Showing <strong><?php echo count($states); ?></strong> of <strong><?php echo $total_states; ?></strong> states 
                        (Page <strong><?php 
$segment = $this->uri->segment(4); // Changed from segment(3) to segment(4) for SettingController
$current_page = ($segment && is_numeric($segment)) ? (int)$segment : 1;
echo $current_page; 
?></strong> 
                        of <strong><?php echo ceil($total_states/24); ?></strong>)
                     </div>
                     <div>
                        <?php echo $state_pagination; ?>
                     </div>
                  </div>
                  <?php } ?>
               </div>
            </div>
            <!-- CITY ACCORDION - Grouped by State -->
            <div class="location-accordion">
               <div class="accordion-header" data-target="cityContent">
                  <div class="header-left">
                     <div class="header-icon">
                        <i class="bi bi-pin-map"></i>
                     </div>
                     <div>
                        <h3 class="header-title">
                           Cities
                           <small>Manage all cities</small>
                        </h3>
                        <div class="header-badge">
                           <i class="bi bi-database"></i>
                           Total: <?php echo isset($total_cities) ? $total_cities : 0; ?> records
                           <?php if(isset($cities) && isset($total_cities) && count($cities) < $total_cities){ ?>
                           <small class="text-muted">(Showing <?php echo count($cities); ?> on page 
                           <?php 
$segment = $this->uri->segment(4);
$current_page = ($segment && is_numeric($segment)) ? (int)$segment : 1;
echo $current_page; 
?>)</small>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
                  <div class="expand-btn">
                     <i class="bi bi-plus-lg"></i>
                  </div>
               </div>
               <div class="accordion-content" id="cityContent">
                  <!-- Mini Search for City -->
                  <div class="search-mini">
                     <div class="row g-3">
                        <div class="col-md-3">
                           <input type="text" class="form-control" id="citySearch" placeholder="Search cities...">
                        </div>
                        <div class="col-md-2">
                           <select class="form-select" id="cityCountry">
                              <option value="">All Countries</option>
                              <?php if(!empty($countriesData)){ foreach($countriesData as $c){ ?>
                              <option value="<?php echo $c['id'];?>"><?php echo $c['country_name'];?></option>
                              <?php }} ?>
                           </select>
                        </div>
                        <div class="col-md-2">
                           <select class="form-select" id="cityState">
                              <option value="">All States</option>
                           </select>
                        </div>
                        <div class="col-md-2">
                           <select class="form-select" id="cityStatus">
                              <option value="">All Status</option>
                              <option value="1">Active</option>
                              <option value="0">Inactive</option>
                           </select>
                        </div>
                        <div class="col-md-2">
                           <button class="btn-modern btn-purple-modern w-100" onclick="filterCity()">
                           <i class="bi bi-search"></i> Search
                           </button>
                        </div>
                        <div class="col-md-1">
                           <button class="add-button w-100 create_top text-white rounded-5 fw-medium trans_eff align-middle" data-bs-toggle="modal" data-bs-target="#addCityModal">
                           <i class="bi bi-plus-lg">Add</i>
                           </button>
                        </div>
                     </div>
                  </div>
                  <!-- City Table - Grouped by State -->
                  <?php 
                     if(!empty($cities)){
                         // Group cities by state
                         $grouped_cities = [];
                         foreach($cities as $city){
                             $grouped_cities[$city['state_name']][] = $city;
                         }
                         
                         $state_counter = 1;
                         foreach($grouped_cities as $state_name => $state_cities){
                     ?>
                  <div class="state-group mb-4">
                     <div class="bg-light p-3 rounded mb-3 d-flex align-items-center">
                        <span class="badge bg-success me-3" style="font-size: 1rem;"><?php echo $state_counter; ?></span>
                        <h5 class="mb-0 text-success fw-bold">
                           <i class="bi bi-building me-2"></i><?php echo $state_name; ?>
                        </h5>
                        <span class="ms-auto badge bg-info"><?php echo count($state_cities); ?> Cities</span>
                     </div>
                     <div class="table-responsive">
                        <table class="table table-bordered table-striped city-subtable">
                           <thead>
                              <tr>
                                 <th width="60">#</th>
                                 <th>Country</th>
                                 <th>City Name</th>
                                 <th>Temp Name</th>
                                 <th>Group ID</th>
                                 <th>Premium</th>
                                 <th>Popular</th>
                                 <th width="100">Status</th>
                                 <th width="150">Actions</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php 
                                 $city_counter = 1;
                                 foreach($state_cities as $ci){ 
                                 ?>
                              <tr>
                                 <td><span class="badge bg-light text-dark"><?php echo $city_counter; ?></span></td>
                                 <td><span class="badge bg-info text-white"><?php echo $ci['country_name'];?></span></td>
                                 <td><b><?php echo $ci['title'];?></b></td>
                                 <td><?php echo $ci['temp_title'] ?: '-';?></td>
                                 <td><?php echo $ci['city_group_id'] ?: '-';?></td>
                                 <td>
                                    <?php if($ci['premimum_ads_avl'] == '1'){ ?>
                                    <span class="badge-premium">Yes</span>
                                    <?php } else { ?>
                                    <span class="text-muted">No</span>
                                    <?php } ?>
                                 </td>
                                 <td>
                                    <?php if($ci['is_city_popular'] == '1'){ ?>
                                    <span class="badge-popular">Popular</span>
                                    <?php } elseif($ci['is_othercity_popular'] == '1'){ ?>
                                    <span class="badge-popular">Other Popular</span>
                                    <?php } else { ?>
                                    <span class="text-muted">-</span>
                                    <?php } ?>
                                 </td>
                                 <td>
                                    <label class="switch">
                                    <input type="checkbox" class="status_toggle" data-type="city" data-id="<?php echo $ci['id'];?>" <?php echo ($ci['status']==1)?'checked':'';?>>
                                    <span class="slider"></span>
                                    </label>
                                 </td>
                                 <td>
                                    <div class="action-icons">
                                       <a href="javascript:void(0)" class="action-icon toggle_premium" data-type="city" data-id="<?php echo $ci['id'];?>" data-value="<?php echo $ci['premimum_ads_avl'];?>" title="Toggle Premium">
                                       <i class="bi bi-cash<?php echo ($ci['premimum_ads_avl']=='1')?'-stack':'';?>"></i>
                                       </a>
                                       <a href="javascript:void(0)" class="action-icon toggle_city_popular" data-type="city" data-id="<?php echo $ci['id'];?>" data-value="<?php echo $ci['is_city_popular'];?>" title="Toggle Popular">
                                       <i class="bi bi-star<?php echo ($ci['is_city_popular']=='1')?'-fill':'';?>"></i>
                                       </a>
                                       <a href="javascript:void(0)" class="action-icon edit edit_city" data-id="<?php echo $ci['id'];?>" title="Edit">
                                       <i class="bi bi-pencil"></i>
                                       </a>
                                       <a href="javascript:void(0)" class="action-icon delete delete_btn" data-url="<?php echo site_url('admin/settingcontroller/delete_city/'.$ci['id']);?>" title="Delete">
                                       <i class="bi bi-trash"></i>
                                       </a>
                                    </div>
                                 </td>
                              </tr>
                              <?php 
                                 $city_counter++;
                                 } 
                                 ?>
                           </tbody>
                        </table>
                     </div>
                  </div>
                  <?php 
                     $state_counter++;
                     }
                     } else { 
                     ?>
                  <div class="alert alert-info text-center">No cities found</div>
                  <?php } ?>
                  <!-- Pagination with Summary -->
                  <?php if(isset($city_pagination)){ ?>
                  <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap">
                     <div class="text-muted mb-2 mb-md-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Showing <strong><?php echo count($cities); ?></strong> of <strong><?php echo $total_cities; ?></strong> cities 
                        (Page <strong><?php 
$segment = $this->uri->segment(4); // Changed from segment(3) to segment(4) for SettingController
$current_page = ($segment && is_numeric($segment)) ? (int)$segment : 1;
echo $current_page; 
?></strong> 
                        of <strong><?php echo ceil($total_cities/24); ?></strong>)
                     </div>
                     <div>
                        <?php echo $city_pagination; ?>
                     </div>
                  </div>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<!-- ADD COUNTRY MODAL - Full Fields -->
<div class="modal fade modern-modal" id="addCountryModal">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header-modern">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Country</h5>
            <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
            </button>
         </div>
         <form method="post" action="<?php echo site_url('admin/settingcontroller/create_country');?>" id="addCountryForm">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
            <div class="modal-body-modern">
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Country Name *</label>
                     <input type="text" name="country_name" class="modern-input" placeholder="Enter country name" required>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Temporary Name</label>
                     <input type="text" name="country_temp_name" class="modern-input" placeholder="Enter temporary name">
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3 mb-3">
                     <label class="form-label fw-600">ISO Code 2</label>
                     <input type="text" name="country_iso_code_2" class="modern-input" maxlength="2" placeholder="US">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label class="form-label fw-600">ISO Code 3</label>
                     <input type="text" name="country_iso_code_3" class="modern-input" maxlength="3" placeholder="USA">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label class="form-label fw-600">Address Format ID</label>
                     <input type="number" name="address_format_id" class="modern-input" value="0">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label class="form-label fw-600">Currency</label>
                     <input type="text" name="cont_currency" class="modern-input" placeholder="USD">
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Time Zone</label>
                     <input type="text" name="TimeZone" class="modern-input" placeholder="America/New_York">
                  </div>
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">UTC Offset</label>
                     <input type="text" name="UTC_offset" class="modern-input" placeholder="-05:00">
                  </div>
               </div>
               <div class="checkbox-grid">
                  <div class="checkbox-item">
                     <input type="checkbox" name="is_feature" id="is_feature" value="1">
                     <label for="is_feature">Is Featured</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="premimum_ads_avl" id="premimum_ads_avl" value="1" checked>
                     <label for="premimum_ads_avl">Premium Ads Available</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="status" id="country_status" value="1" checked>
                     <label for="country_status">Active</label>
                  </div>
               </div>
            </div>
            <div class="modal-footer-modern">
               <button type="button" class="btn-modern btn-outline-modern" data-dismiss="modal">
               <i class="bi bi-x-lg"></i> Cancel
               </button>
               <button type="submit" class="btn-modern btn-purple-modern">
               <i class="bi bi-check-lg"></i> Create Country
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- EDIT COUNTRY MODAL - Full Fields -->
<div class="modal fade modern-modal" id="editCountryModal">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header-modern">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Country</h5>
            <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
            </button>
         </div>
         <form method="post" action="<?php echo site_url('admin/settingcontroller/update_country');?>" id="editCountryForm">
            <input type="hidden" name="id" id="edit_country_id">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
            <div class="modal-body-modern">
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Country Name *</label>
                     <input type="text" name="country_name" id="edit_country_name" class="modern-input" required>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Temporary Name</label>
                     <input type="text" name="country_temp_name" id="edit_country_temp_name" class="modern-input">
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3 mb-3">
                     <label class="form-label fw-600">ISO Code 2</label>
                     <input type="text" name="country_iso_code_2" id="edit_country_iso_code_2" class="modern-input" maxlength="2">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label class="form-label fw-600">ISO Code 3</label>
                     <input type="text" name="country_iso_code_3" id="edit_country_iso_code_3" class="modern-input" maxlength="3">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label class="form-label fw-600">Address Format ID</label>
                     <input type="number" name="address_format_id" id="edit_address_format_id" class="modern-input">
                  </div>
                  <div class="col-md-3 mb-3">
                     <label class="form-label fw-600">Currency</label>
                     <input type="text" name="cont_currency" id="edit_cont_currency" class="modern-input">
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Time Zone</label>
                     <input type="text" name="TimeZone" id="edit_TimeZone" class="modern-input">
                  </div>
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">UTC Offset</label>
                     <input type="text" name="UTC_offset" id="edit_UTC_offset" class="modern-input">
                  </div>
               </div>
               <div class="checkbox-grid">
                  <div class="checkbox-item">
                     <input type="checkbox" name="is_feature" id="edit_is_feature" value="1">
                     <label for="edit_is_feature">Is Featured</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="premimum_ads_avl" id="edit_premimum_ads_avl" value="1">
                     <label for="edit_premimum_ads_avl">Premium Ads Available</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="status" id="edit_country_status" value="1">
                     <label for="edit_country_status">Active</label>
                  </div>
               </div>
            </div>
            <div class="modal-footer-modern">
               <button type="button" class="btn-modern btn-outline-modern" data-dismiss="modal">
               <i class="bi bi-x-lg"></i> Cancel
               </button>
               <button type="submit" class="btn-modern btn-purple-modern">
               <i class="bi bi-check-lg"></i> Update Country
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- ADD STATE MODAL - Full Fields -->
<div class="modal fade modern-modal" id="addStateModal">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header-modern">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New State</h5>
            <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
            </button>
         </div>
         <form method="post" action="<?php echo site_url('admin/settingcontroller/create_state');?>" id="addStateForm">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
            <div class="modal-body-modern">
               <div class="mb-3">
                  <label class="form-label fw-600">Select Country *</label>
                  <select name="country_id" class="modern-select" required>
                     <option value="">Choose a country</option>
                     <?php if(!empty($countriesData)){ foreach($countriesData as $c){ ?>
                     <option value="<?php echo $c['id'];?>"><?php echo $c['country_name'];?></option>
                     <?php }} ?>
                  </select>
               </div>
               <div class="mb-3">
                  <label class="form-label fw-600">State Name *</label>
                  <input type="text" name="title" class="modern-input" placeholder="Enter state name" required>
               </div>
               <div class="mb-3">
                  <label class="form-label fw-600">Temporary Name</label>
                  <input type="text" name="temp_title" class="modern-input" placeholder="Enter temporary name">
               </div>
               <div class="checkbox-grid">
                  <div class="checkbox-item">
                     <input type="checkbox" name="is_state_popular" id="is_state_popular" value="1">
                     <label for="is_state_popular">Is Popular</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="status" id="state_status" value="1" checked>
                     <label for="state_status">Active</label>
                  </div>
               </div>
            </div>
            <div class="modal-footer-modern">
               <button type="button" class="btn-modern btn-outline-modern" data-dismiss="modal">
               <i class="bi bi-x-lg"></i> Cancel
               </button>
               <button type="submit" class="btn-modern btn-purple-modern">
               <i class="bi bi-check-lg"></i> Create State
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- EDIT STATE MODAL - Full Fields -->
<div class="modal fade modern-modal" id="editStateModal">
   <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header-modern">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit State</h5>
            <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
            </button>
         </div>
         <form method="post" action="<?php echo site_url('admin/settingcontroller/update_state');?>" id="editStateForm">
            <input type="hidden" name="id" id="edit_state_id">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
            <div class="modal-body-modern">
               <div class="mb-3">
                  <label class="form-label fw-600">Select Country *</label>
                  <select name="country_id" id="edit_state_country" class="modern-select" required>
                     <option value="">Choose a country</option>
                     <?php if(!empty($countriesData)){ foreach($countriesData as $c){ ?>
                     <option value="<?php echo $c['id'];?>"><?php echo $c['country_name'];?></option>
                     <?php }} ?>
                  </select>
               </div>
               <div class="mb-3">
                  <label class="form-label fw-600">State Name *</label>
                  <input type="text" name="title" id="edit_state_name" class="modern-input" required>
               </div>
               <div class="mb-3">
                  <label class="form-label fw-600">Temporary Name</label>
                  <input type="text" name="temp_title" id="edit_state_temp" class="modern-input">
               </div>
               <div class="checkbox-grid">
                  <div class="checkbox-item">
                     <input type="checkbox" name="is_state_popular" id="edit_is_state_popular" value="1">
                     <label for="edit_is_state_popular">Is Popular</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="status" id="edit_state_status" value="1">
                     <label for="edit_state_status">Active</label>
                  </div>
               </div>
            </div>
            <div class="modal-footer-modern">
               <button type="button" class="btn-modern btn-outline-modern" data-dismiss="modal">
               <i class="bi bi-x-lg"></i> Cancel
               </button>
               <button type="submit" class="btn-modern btn-purple-modern">
               <i class="bi bi-check-lg"></i> Update State
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- ADD CITY MODAL - Full Fields -->
<div class="modal fade modern-modal" id="addCityModal">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header-modern">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New City</h5>
            <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
            </button>
         </div>
         <form method="post" action="<?php echo site_url('admin/settingcontroller/create_city');?>" id="addCityForm">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
            <div class="modal-body-modern">
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Select Country *</label>
                     <select name="country_id" id="city_country_id" class="modern-select" required onchange="loadStates(this.value, 'city_state_id')">
                        <option value="">Choose a country</option>
                        <?php if(!empty($countriesData)){ foreach($countriesData as $c){ ?>
                        <option value="<?php echo $c['id'];?>"><?php echo $c['country_name'];?></option>
                        <?php }} ?>
                     </select>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Select State *</label>
                     <select name="state_id" id="city_state_id" class="modern-select" required>
                        <option value="">Select state first</option>
                     </select>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">City Name *</label>
                     <input type="text" name="title" class="modern-input" placeholder="Enter city name" required>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Temporary Name</label>
                     <input type="text" name="temp_title" class="modern-input" placeholder="Enter temporary name">
                  </div>
               </div>
               <div class="mb-3">
                  <label class="form-label fw-600">City Group ID</label>
                  <input type="text" name="city_group_id" class="modern-input" placeholder="Enter group ID">
               </div>
               <div class="checkbox-grid">
                  <div class="checkbox-item">
                     <input type="checkbox" name="premimum_ads_avl" id="premimum_ads_avl_city" value="1">
                     <label for="premimum_ads_avl_city">Premium Ads Available</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="is_city_popular" id="is_city_popular" value="1">
                     <label for="is_city_popular">Is Popular</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="is_othercity_popular" id="is_othercity_popular" value="1">
                     <label for="is_othercity_popular">Is Other City Popular</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="status" id="city_status" value="1" checked>
                     <label for="city_status">Active</label>
                  </div>
               </div>
            </div>
            <div class="modal-footer-modern">
               <button type="button" class="btn-modern btn-outline-modern" data-dismiss="modal">
               <i class="bi bi-x-lg"></i> Cancel
               </button>
               <button type="submit" class="btn-modern btn-purple-modern">
               <i class="bi bi-check-lg"></i> Create City
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- EDIT CITY MODAL - Full Fields -->
<div class="modal fade modern-modal" id="editCityModal">
   <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header-modern">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit City</h5>
            <button type="button" class="close text-white" data-dismiss="modal">
            <span>&times;</span>
            </button>
         </div>
         <form method="post" action="<?php echo site_url('admin/settingcontroller/update_city');?>" id="editCityForm">
            <input type="hidden" name="id" id="edit_city_id">
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
            <div class="modal-body-modern">
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Select Country *</label>
                     <select name="country_id" id="edit_city_country" class="modern-select" required onchange="loadStates(this.value, 'edit_city_state')">
                        <option value="">Choose a country</option>
                        <?php if(!empty($countriesData)){ foreach($countriesData as $c){ ?>
                        <option value="<?php echo $c['id'];?>"><?php echo $c['country_name'];?></option>
                        <?php }} ?>
                     </select>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Select State *</label>
                     <select name="state_id" id="edit_city_state" class="modern-select" required>
                        <option value="">Select state</option>
                     </select>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">City Name *</label>
                     <input type="text" name="title" id="edit_city_name" class="modern-input" required>
                  </div>
                  <div class="col-md-6 mb-3">
                     <label class="form-label fw-600">Temporary Name</label>
                     <input type="text" name="temp_title" id="edit_city_temp" class="modern-input">
                  </div>
               </div>
               <div class="mb-3">
                  <label class="form-label fw-600">City Group ID</label>
                  <input type="text" name="city_group_id" id="edit_city_group_id" class="modern-input">
               </div>
               <div class="checkbox-grid">
                  <div class="checkbox-item">
                     <input type="checkbox" name="premimum_ads_avl" id="edit_premimum_ads_avl" value="1">
                     <label for="edit_premimum_ads_avl">Premium Ads Available</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="is_city_popular" id="edit_is_city_popular" value="1">
                     <label for="edit_is_city_popular">Is Popular</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="is_othercity_popular" id="edit_is_othercity_popular" value="1">
                     <label for="edit_is_othercity_popular">Is Other City Popular</label>
                  </div>
                  <div class="checkbox-item">
                     <input type="checkbox" name="status" id="edit_city_status" value="1">
                     <label for="edit_city_status">Active</label>
                  </div>
               </div>
            </div>
            <div class="modal-footer-modern">
               <button type="button" class="btn-modern btn-outline-modern" data-dismiss="modal">
               <i class="bi bi-x-lg"></i> Cancel
               </button>
               <button type="submit" class="btn-modern btn-purple-modern">
               <i class="bi bi-check-lg"></i> Update City
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
<script>
   function loadStates(country_id, target_element_id) {
       if(country_id == '') {
           $('#'+target_element_id).html('<option value="">Select state first</option>');
           return;
       }
       
       $.ajax({
           url: '<?php echo site_url('admin/settingcontroller/get_states_by_country');?>/'+country_id,
           type: 'GET',
           dataType: 'json',
           success: function(data) {
               var options = '<option value="">Select a state</option>';
               $.each(data, function(key, value) {
                   options += '<option value="'+value.id+'">'+value.title+'</option>';
               });
               $('#'+target_element_id).html(options);
           }
       });
   }
   
   // Filter functions
   function filterCountry() {
       var table = $('#countryTable').DataTable();
       table.search($('#countrySearch').val()).draw();
   }
   
   function filterState() {
       var table = $('.state-subtable').DataTable();
       table.search($('#stateSearch').val()).draw();
   }
   
   function filterCity() {
       var table = $('.city-subtable').DataTable();
       table.search($('#citySearch').val()).draw();
   }
   
   $(document).ready(function(){
   
       // Accordion functionality
       $('.accordion-header').click(function() {
           var target = $(this).data('target');
           var content = $('#' + target);
           var btn = $(this).find('.expand-btn');
           
           // Close all other accordions
           $('.accordion-content').not(content).removeClass('show');
           $('.accordion-header').not($(this)).removeClass('active');
           $('.expand-btn').not(btn).removeClass('active').html('<i class="bi bi-plus-lg"></i>');
           
           // Toggle current
           content.toggleClass('show');
           $(this).toggleClass('active');
           
           if (content.hasClass('show')) {
               btn.addClass('active').html('<i class="bi bi-dash-lg"></i>');
               
               // Initialize DataTables with server-side pagination DISABLED
               if (target == 'countryContent' && !$.fn.DataTable.isDataTable('#countryTable')) {
                   $('#countryTable').DataTable({
                       dom: 'Bfrtip',
                       buttons: [
                           { extend: 'copy', text: '<i class="bi bi-files"></i> Copy' },
                           { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i> CSV' },
                           { extend: 'excel', text: '<i class="bi bi-file-excel"></i> Excel' },
                           { extend: 'pdf', text: '<i class="bi bi-file-pdf"></i> PDF' },
                           { extend: 'print', text: '<i class="bi bi-printer"></i> Print' }
                       ],
                       paging: false,
                       info: false,
                       searching: true,
                       ordering: true,
                       responsive: true
                   });
               }
               
               if (target == 'stateContent' && !$.fn.DataTable.isDataTable('.state-subtable')) {
                   $('.state-subtable').DataTable({
                       dom: 'Bfrtip',
                       buttons: [
                           { extend: 'copy', text: '<i class="bi bi-files"></i> Copy' },
                           { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i> CSV' },
                           { extend: 'excel', text: '<i class="bi bi-file-excel"></i> Excel' },
                           { extend: 'pdf', text: '<i class="bi bi-file-pdf"></i> PDF' },
                           { extend: 'print', text: '<i class="bi bi-printer"></i> Print' }
                       ],
                       paging: false,
                       info: false,
                       searching: true,
                       ordering: true,
                       responsive: true
                   });
               }
               
               if (target == 'cityContent' && !$.fn.DataTable.isDataTable('.city-subtable')) {
                   $('.city-subtable').DataTable({
                       dom: 'Bfrtip',
                       buttons: [
                           { extend: 'copy', text: '<i class="bi bi-files"></i> Copy' },
                           { extend: 'csv', text: '<i class="bi bi-filetype-csv"></i> CSV' },
                           { extend: 'excel', text: '<i class="bi bi-file-excel"></i> Excel' },
                           { extend: 'pdf', text: '<i class="bi bi-file-pdf"></i> PDF' },
                           { extend: 'print', text: '<i class="bi bi-printer"></i> Print' }
                       ],
                       paging: false,
                       info: false,
                       searching: true,
                       ordering: true,
                       responsive: true
                   });
               }
           } else {
               btn.removeClass('active').html('<i class="bi bi-plus-lg"></i>');
           }
       });
   
       // Toggle Featured (Country)
       $(document).on("click",".toggle_featured",function(){
           var id = $(this).data("id");
           var url = "<?php echo site_url('admin/settingcontroller/toggle_featured_country/');?>"+id;
           
           swal({
               title: "Toggle Featured?",
               text: "Change featured status for this country.",
               icon: "warning",
               buttons: true
           }).then((ok) => {
               if(ok){
                   window.location.href = url;
               }
           });
       });
   
       // Toggle Popular (State)
       $(document).on("click",".toggle_popular",function(){
           var id = $(this).data("id");
           var url = "<?php echo site_url('admin/settingcontroller/toggle_popular_state/');?>"+id;
           
           swal({
               title: "Toggle Popular?",
               text: "Change popular status for this state.",
               icon: "warning",
               buttons: true
           }).then((ok) => {
               if(ok){
                   window.location.href = url;
               }
           });
       });
   
       // Toggle Premium (City)
       $(document).on("click",".toggle_premium",function(){
           var id = $(this).data("id");
           var url = "<?php echo site_url('admin/settingcontroller/toggle_premium_city/');?>"+id;
           
           swal({
               title: "Toggle Premium?",
               text: "Change premium ads availability for this city.",
               icon: "warning",
               buttons: true
           }).then((ok) => {
               if(ok){
                   window.location.href = url;
               }
           });
       });
   
       // Toggle City Popular
       $(document).on("click",".toggle_city_popular",function(){
           var id = $(this).data("id");
           var url = "<?php echo site_url('admin/settingcontroller/toggle_popular_city/');?>"+id;
           
           swal({
               title: "Toggle Popular?",
               text: "Change popular status for this city.",
               icon: "warning",
               buttons: true
           }).then((ok) => {
               if(ok){
                   window.location.href = url;
               }
           });
       });
   
       // Delete record
       $(document).on("click",".delete_btn",function(){
           var url = $(this).data("url");
           swal({
               title: "Are you sure?",
               text: "This record will be moved to trash!",
               icon: "warning",
               buttons: true,
               dangerMode: true
           }).then((willDelete) => {
               if(willDelete){ window.location.href = url; }
           });
       });
   
       // Status toggle
       $(document).on("change",".status_toggle",function(){
           var id = $(this).data("id");
           var type = $(this).data("type");
           var status = $(this).prop("checked") ? 'active' : 'deactive';
           var url = "<?php echo site_url('admin/settingcontroller/status_location/');?>"+type+"/"+id+"?u_status="+status;
           var checkbox = $(this);
           
           swal({
               title: "Change Status?",
               text: type.charAt(0).toUpperCase() + type.slice(1) + " status will be updated.",
               icon: "warning",
               buttons: true
           }).then((ok) => {
               if(ok){
                   window.location.href = url;
               } else {
                   checkbox.prop("checked", !checkbox.prop("checked"));
               }
           });
       });
   
       // Edit Country
       $(".edit_country").click(function(){
           var id = $(this).data("id");
           $.ajax({
               url: "<?php echo site_url('admin/settingcontroller/edit_country/');?>"+id,
               type: "GET",
               dataType: "json",
               success: function(data){
                   $("#edit_country_id").val(data.id);
                   $("#edit_country_name").val(data.country_name);
                   $("#edit_country_temp_name").val(data.country_temp_name);
                   $("#edit_country_iso_code_2").val(data.country_iso_code_2);
                   $("#edit_country_iso_code_3").val(data.country_iso_code_3);
                   $("#edit_address_format_id").val(data.address_format_id);
                   $("#edit_cont_currency").val(data.cont_currency);
                   $("#edit_TimeZone").val(data.TimeZone);
                   $("#edit_UTC_offset").val(data.UTC_offset);
                   
                   $("#edit_is_feature").prop("checked", data.is_feature == 1);
                   $("#edit_premimum_ads_avl").prop("checked", data.premimum_ads_avl == 1);
                   $("#edit_country_status").prop("checked", data.status == 1);
                   
                   $("#editCountryModal").modal("show");
               }
           });
       });
   
       // Edit State
       $(".edit_state").click(function(){
           var id = $(this).data("id");
           $.ajax({
               url: "<?php echo site_url('admin/settingcontroller/edit_state/');?>"+id,
               type: "GET",
               dataType: "json",
               success: function(data){
                   $("#edit_state_id").val(data.id);
                   $("#edit_state_country").val(data.country_id);
                   $("#edit_state_name").val(data.title);
                   $("#edit_state_temp").val(data.temp_title);
                   
                   $("#edit_is_state_popular").prop("checked", data.is_state_popular == 1);
                   $("#edit_state_status").prop("checked", data.status == 1);
                   
                   $("#editStateModal").modal("show");
               }
           });
       });
   
       // Edit City
       $(".edit_city").click(function(){
           var id = $(this).data("id");
           $.ajax({
               url: "<?php echo site_url('admin/settingcontroller/edit_city/');?>"+id,
               type: "GET",
               dataType: "json",
               success: function(data){
                   $("#edit_city_id").val(data.id);
                   $("#edit_city_country").val(data.country_id);
                   $("#edit_city_name").val(data.title);
                   $("#edit_city_temp").val(data.temp_title);
                   $("#edit_city_group_id").val(data.city_group_id);
                   
                   loadStates(data.country_id, 'edit_city_state');
                   
                   setTimeout(function() {
                       $("#edit_city_state").val(data.state_id);
                   }, 500);
                   
                   $("#edit_premimum_ads_avl").prop("checked", data.premimum_ads_avl == 1);
                   $("#edit_is_city_popular").prop("checked", data.is_city_popular == 1);
                   $("#edit_is_othercity_popular").prop("checked", data.is_othercity_popular == 1);
                   $("#edit_city_status").prop("checked", data.status == 1);
                   
                   $("#editCityModal").modal("show");
               }
           });
       });
   
       // Update states dropdown when country changes in city search
       $('#cityCountry').change(function() {
           var country_id = $(this).val();
           if(country_id) {
               $.ajax({
                   url: '<?php echo site_url("admin/settingcontroller/get_states_by_country");?>/' + country_id,
                   type: 'GET',
                   dataType: 'json',
                   success: function(data) {
                       var options = '<option value="">All States</option>';
                       $.each(data, function(key, value) {
                           options += '<option value="'+value.id+'">'+value.title+'</option>';
                       });
                       $('#cityState').html(options);
                   }
               });
           } else {
               $('#cityState').html('<option value="">All States</option>');
           }
       });
   
   });
   
   // Flash messages
   <?php if($this->session->flashdata('success')){ ?>
       swal({
           title: "Success!",
           text: "<?php echo $this->session->flashdata('success');?>",
           icon: "success",
           button: "OK"
       });
   <?php } ?>
   
   <?php if($this->session->flashdata('error')){ ?>
       swal({
           title: "Error!",
           text: "<?php echo $this->session->flashdata('error');?>",
           icon: "error",
           button: "OK"
       });
   <?php } ?>
</script>
<?php $this->load->view("bottom_application"); ?>