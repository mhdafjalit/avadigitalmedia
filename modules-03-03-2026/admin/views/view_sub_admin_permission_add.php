<?php 
$this->load->view('top_application'); 
$bdcm_array = array(
	array('heading'=>'Sub User Manage','url'=>'admin/sub_admins'),
	array('heading'=>'Dashboard','url'=>'admin')
);
$uerId= $this->uri->segment(3);
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
                    <?php echo error_message();?>
                    <?php echo form_open(current_url_query_string(),'name="permission_frm" autocomplete="off"');?>
	    			<div class="row g-3">
	  					<div class="col-12"><p class="fw-bold text-uppercase">Permission</p></div>
	  					<?php
                        echo form_error('user_permission');
                        $values_posted_back = $this->input->post() ? TRUE : FALSE;
                        $posted_arr = $this->input->post('sec_id') ?: [];
                        if (is_array($section_res) && !empty($section_res)) {
                            $config_prvg_arr = $this->config->item('subadmin_privileges');
                            foreach ($section_res as $value) {
                                $controller_identifier = $value['section_controller'];
                                $actual_privileges = explode(",", $value['actual_privilege']);
                                if (!empty($actual_privileges)) { ?>
                                    <div class="col-6 x_section">
                                        <label class="form-label d-block mb-2"><?php echo $value['section_title']; ?></label>
                                        <?php 
                                        foreach ($actual_privileges as $val) {
                                            $ckbox_val = "{$value['id']}_0_{$val}";
                                            $ckbox_name = $controller_identifier;
                                            $posted_vals = $values_posted_back 
                                                ? (array)$this->input->post($ckbox_name) 
                                                : (array)($db_saved_data[$ckbox_name] ?? []);
                                            $checked = in_array($ckbox_val, $posted_vals) ? 'checked="checked"' : '';
                                            $cls_val = "btn-check xprv";
                                            $cls_val .=" x_sibling";
                                            switch ($val) {
                                                case '1':
                                                $cls_val.=" x_view";
                                                break;
                                                case '12':
                                                $cls_val.=" x_all";
                                                break;
                                            }
                                            ?>
                                            <input type="checkbox" name="<?php echo $ckbox_name; ?>[]" value="<?php echo $ckbox_val; ?>" <?php echo $checked; ?> class="<?php echo $cls_val; ?>" id="<?php echo $ckbox_val; ?>">
                                            <label class="btn btn-sm btn-outline-secondary mb-1" for="<?php echo $ckbox_val; ?>"><?php echo $config_prvg_arr[$val]; ?></label>
                                            <?php 
                                        }?>
                                    </div>
                                    <?php 
                                }
                            }
                        }?>
					</div>
                  
					<div class="mt-3">
                        <input type="hidden" name="user_permission" class="form-control" id="permission" value="<?php echo (is_array($db_saved_data) && !empty($db_saved_data))?'1':'';?>">
                        <input name="action" type="submit" class="btn btn-purple" value="Submit">
                    </div>
                    <?php echo form_close();?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>
<!-- MIDDLE ENDS -->
<script type="text/javascript">
$('.xprv').click(function(e){
    var cobj = $(this);
    var parent_section = cobj.parents('.x_section');
    if(cobj.prop('checked')){
        $('#permission').val(1);
        if(cobj.hasClass('x_all') || cobj.hasClass('x_main')){
            parent_section.find('.xprv').not(cobj).prop('checked',true);
        }else{
            if(!cobj.hasClass('x_view')){
                parent_section.find('.x_view').prop('checked',true);
            }
            parent_section.find('.x_main').not(':checked').prop('checked',true);
            unchecked_sibling = parent_section.find('.x_sibling:not(:checked)').not('.x_all');
            if(!unchecked_sibling.length){
                parent_section.find('.x_all').prop('checked',true);
            }
        }
    }else{
        if(cobj.hasClass('x_all') || cobj.hasClass('x_main')){
            parent_section.find('.xprv').not(cobj).prop('checked',false);
        }else{
            checked_sibling = parent_section.find('.x_sibling:checked').not('.x_all');
            if(!checked_sibling.length){
                parent_section.find('.x_all').prop('checked',false);
                parent_section.find('.x_main').prop('checked',false);
            }else{
                if(cobj.hasClass('x_view')){
                    cobj.prop('checked',true);
                }else{
                    parent_section.find('.x_all').prop('checked',false);
                }
            }
        }
    }
});
</script>
<?php $this->load->view("bottom_application");?>