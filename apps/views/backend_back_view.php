<?php 
if( $this->session->userdata('is_admin_switch') && $this->session->userdata('adm_key')!=""){?>
	<div  id="dialog_box_for_admin" class="noprint" style="position:absolute; margin-left:20px;">            
		<a href="<?php echo base_url()?>user/backtopanel" target="_parent"><span class="red b fs16"> Switch Back To Admin Panel </span></a>
	</div> 
<?php
}