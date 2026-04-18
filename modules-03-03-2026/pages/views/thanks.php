<?php $this->load->view("top_application");
$this->load->view("banner/top_inner_banner");
echo navigation_breadcrumb($page_heading);?>
<!-- MIDDLE STARTS -->
<div class="container mid_area">
	<div class="cms_area">
		<div class="thankyou_wrap">
			<div class="thanks_icon"><i class="fas fa-envelope-open-text" aria-hidden="true"></i></div>
			<b>Thanks for your query!</b>
			<p>It was a pleasure receiving your enquiry. Please send us your requirements at <?php echo $this->admin_info->admin_email;?>. Our sales team will contact you soon to send the offer as per your details.
			</p>
			<p class="mt-4"><a href="<?php echo base_url();?>" class=" view_btn">Go Home </a></p>
		</div>
	</div>
</div>
<!-- MIDDLE ENDS -->
<?php $this->load->view("bottom_application");?>