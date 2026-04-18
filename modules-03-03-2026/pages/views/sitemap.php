<?php $this->load->view('top_application');
$this->load->view("banner/top_inner_banner");
echo navigation_breadcrumb($heading_title);?>
<!-- MIDDLE STARTS -->
<div class="container mid_area">
	<h1><?php echo $heading_title;?></h1>
	<div class="row">
		<div class="col-lg-7">
			<div class="sitemap_cont">
				<h2 class="red contact_bg border1 p-3 mb-3 ttu">Quick Links</h2>
				<div class="sitemap">
					<a href="<?php echo site_url('');?>" title="Home">Home</a>
					<a href="<?php echo site_url('aboutus');?>" title="About Us">About Us</a>
					<a href="<?php echo site_url('products-category');?>" title="Products">Products</a>
					<a href="<?php echo site_url('services-category');?>" title="Services">Services</a>
					<a href="<?php echo site_url('brand');?>" title="Our Brands">Our Brands</a>
					<a href="<?php echo site_url('contactus');?>" title="Contact Us">Contact Us</a>
					<a href="<?php echo site_url('gallery');?>" title="Gallery">Gallery</a> 
					<a href="<?php echo site_url('testimonials');?>" title="Testimonials">Testimonials</a>
					<a data-fancybox data-type="iframe" data-src="<?php echo site_url('pages/newsletter'); ?>" href="javascript:void(0);" class="pop1" title="Newsletter">Newsletter</a> 
					<a href="<?php echo site_url('privacy-policy');?>" title="Privacy Policy">Privacy Policy</a>
					<a href="<?php echo site_url('terms-conditions');?>" title="Terms and Conditions">Terms and Conditions</a>
					<a href="<?php echo site_url('faq');?>" title="FAQs">FAQs</a>
					<a href="<?php echo site_url('legal-disclaimer');?>" title="Legal Disclaimer">Legal Disclaimer</a>
					<div class="clearfix"></div>
				</div>
			</div>
			<h2 class="red contact_bg border1 p-3 mb-3 ttu">Category</h2>
			<?php
			$show_max_products_cat = 8;
			$where_products_cat = "cat.status='1' AND cat.cat_type='1' AND parent_id='0'";
			$params_products_cat = array(
									'fields'=>"cat.category_id,cat.category_name,cat.friendly_url",
									'from'=>'wl_categories as cat',
									'orderby'=>'cat.sort_order',
									'limit'=>$show_max_products_cat,
									'where'=>$where_products_cat,
									'debug'=>FALSE
									);
			$res_products_cat   = $this->utils_model->custom_query_builder($params_products_cat);
			$total_products_cat = $this->utils_model->total_rec_found;
			if(is_array($res_products_cat) && !empty($res_products_cat)){?>
			<div class="sitemap">
				<?php foreach($res_products_cat as $key=>$val){
				$escaped_title_main = escape_chars($val['category_name']);
				?>
				<?php echo '<a href="'.site_url($val['friendly_url']).'" title="'.$escaped_title_main.'">'.char_limiter($val['category_name'],30).'</a>';?>
				<?php } ?>
				<?php if($total_products_cat>$show_max_products_cat){?>
				<a href="<?php echo site_url('products-category');?>" class="btn btn-pink">View All</a>
				<?php } ?>
				<div class="clearfix"></div>
			</div>
			<?php } ?>
		</div>
		<div class="col-lg-5 d-none d-lg-block"><img src="<?php echo theme_url();?>images/map.jpg" class="mw_98" alt=""></div>
	</div>
</div>
<!-- MIDDLE ENDS -->
<?php $this->load->view("bottom_application");?>