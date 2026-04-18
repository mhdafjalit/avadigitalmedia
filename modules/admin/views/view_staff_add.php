<?php 
$this->load->view('top_application'); 

$bdcm_array = array(
    array('heading' => 'Staff Manage', 'url' => 'admin/staff_user'),
    array('heading' => 'Dashboard', 'url' => 'admin')
);
?>

<script src="<?php echo base_url(); ?>assets/developers/js/multichange_dn.js"></script>

<div class="dash_outer">
    <div class="dash_container">
        
        <?php $this->load->view('view_left_sidebar'); ?>
        
        <div id="main-content" class="h-100">
            
            <?php $this->load->view('view_top_sidebar'); ?>
            
            <div class="top_sec d-flex justify-content-between">
                <h1 class="mt-4"><?php echo $heading_title; ?></h1>
                <?php echo navigation_breadcrumb($heading_title, $bdcm_array); ?>
            </div>
            
            <div class="main-content-inner">
                <div class="dash_box p-4">
                    
                    <?php 
                    echo error_message();
                    echo form_open_multipart(current_url_query_string(), 'id="staff_form" autocomplete="off"');
                    ?>
                    
                    <div class="row g-3">
                        
                        <!-- Staff Name -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Staff Name *</label>
                            <input type="text" class="form-control" name="staff_name" value="<?php echo set_value('staff_name'); ?>">
                            <?php echo form_error('staff_name'); ?>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" value="<?php echo set_value('email'); ?>">
                            <?php echo form_error('email'); ?>
                        </div>
                        
                        <!-- Phone -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Phone *</label>
                            <input type="text" class="form-control" name="mobile_number" value="<?php echo set_value('mobile_number'); ?>">
                            <?php echo form_error('mobile_number'); ?>
                        </div>
                        
                        <!-- Role -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Role</label>
                            <select name="role_id" id="role_id" class="form-select">
                                <option value="">Select Role</option>
                                <?php if (!empty($roles)) { ?>
                                    <?php foreach ($roles as $role) { ?>
                                        <option value="<?php echo $role['role_id']; ?>">
                                            <?php echo $role['role_name']; ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <!-- Department -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Department *</label>
                            <select class="form-select" name="department_id" id="department">
                                <option value="">Select Department</option>
                                <?php if (!empty($department)) { ?>
                                    <?php foreach ($department as $dep) { ?>
                                        <option value="<?php echo $dep['department_id']; ?>" <?php echo set_select('department', $dep['department_id']); ?>>
                                            <?php echo $dep['department_name']; ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php echo form_error('department'); ?>
                        </div>
                        
                        <!-- Designation -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Designation *</label>
                            <select class="form-select" name="designation_id" id="designation">
                                <option value="">Select Designation</option>
                            </select>
                            <?php echo form_error('designation'); ?>
                        </div>
                        
                        <!-- Salary -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Salary *</label>
                            <input type="text" class="form-control" name="salary" value="<?php echo set_value('salary'); ?>">
                            <?php echo form_error('salary'); ?>
                        </div>
                        
                        <!-- Address -->
                        <div class="col-12">
                            <label class="form-label">Address *</label>
                            <input type="text" class="form-control" name="address" value="<?php echo set_value('address'); ?>">
                            <?php echo form_error('address'); ?>
                        </div>
                        
                        <!-- Country -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Country</label>
                            <select name="country" id="country" class="form-select">
                                <option value="">Select Country</option>
                                <?php foreach ($countries as $c) { ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo $c['country_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <!-- State -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">State</label>
                            <select name="state" id="state" class="form-select">
                                <option value="">Select State</option>
                            </select>
                        </div>
                        
                        <!-- City -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">City</label>
                            <select name="city" id="city" class="form-select">
                                <option value="">Select City</option>
                            </select>
                        </div>
                        
                        <!-- Postcode -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Postcode</label>
                            <input type="text" class="form-control" name="pin_code" value="<?php echo set_value('pin_code'); ?>">
                        </div>
                        
                        <!-- Profile Image Upload -->
                        <div class="col-sm-6 mt-4">
                            <div class="dtl_upload_img">
                                <div class="float-start me-3 upload_thm">
                                    <img id="documentUpload1" src="<?php echo theme_url(); ?>images/no-img.jpg">
                                </div>
                                <input name="profile_photo" class="dg_custom_file" type="file" onchange="readURL(this,1);" accept="image/*">
                                <p class="attach_btn mt-2 text-uppercase">
                                    <a href="javascript:void(0)" class="text-primary fw-medium">Upload Profile Image</a>
                                </p>
                            </div>
                            <?php echo form_error('profile_photo'); ?>
                        </div>
                        
                        <!-- ID Proof Upload -->
                        <div class="col-sm-6 col-lg-4">
                            <label class="form-label">Upload ID Proof *</label>
                            <input type="file" class="form-control" name="id_proof">
                            <?php echo form_error('id_proof'); ?>
                        </div>
                        
                    </div> <!-- End of row -->
                    
                    <!-- Form Actions -->
                    <div class="mt-4 d-flex gap-2">
                        <input type="hidden" name="action" value="add_staff">
                        <button type="submit" class="btn btn-purple create_top text-white rounded-5 fw-medium">
                            Add Staff
                        </button>
                        <button type="reset" class="btn btn-success create_top text-white rounded-5 fw-medium">
                            Reset
                        </button>
                        <a href="<?php echo site_url('admin/staff_user'); ?>" class="btn btn-success create_top text-white rounded-5 fw-medium">
                            Back
                        </a>
                    </div>
                    
                    <?php echo form_close(); ?>
                    
                </div> <!-- End of dash_box -->
            </div> <!-- End of main-content-inner -->
        </div> <!-- End of main-content -->
    </div> <!-- End of dash_container -->
</div> <!-- End of dash_outer -->

<script src="<?php echo base_url(); ?>assets/developers/js/multichange_dn.js"></script>

<!-- Country-State-City AJAX Script -->
<script>
$(document).on("change", "#country", function() {
    var country_id = $(this).val();
    
    if (country_id != '') {
        $.ajax({
            url: "<?php echo site_url('admin/load_states'); ?>",
            type: "POST",
            data: {country_id: country_id},
            success: function(res) {
                $("#state").html(res);
                $("#city").html('<option value="">Select City</option>');
            }
        });
    }
});

$(document).on("change", "#state", function() {
    var state_id = $(this).val();
    
    if (state_id != '') {
        $.ajax({
            url: "<?php echo site_url('admin/load_cities'); ?>",
            type: "POST",
            data: {state_id: state_id},
            success: function(res) {
                $("#city").html(res);
            }
        });
    }
});
</script>

<!-- Department-Designation AJAX Script -->
<script>
$(document).on("change", "#department", function() {
    var department_id = $(this).val();
    
    if (department_id != '') {
        $.ajax({
            url: "<?php echo site_url('admin/load_designation'); ?>",
            type: "POST",
            data: {department_id: department_id},
            success: function(res) {
                $("#designation").html(res);
            }
        });
    } else {
        $("#designation").html('<option value="">Select Designation</option>');
    }
});
</script>

<!-- Reset Button Script -->
<script>
$('button[type="reset"]').click(function() {
    $('select').val('').trigger('change');
});
</script>

<!-- Image Upload Preview Script -->
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
</script>

<?php $this->load->view("bottom_application"); ?>