<?php $this->load->view("top_application");
echo navigation_breadcrumb('About Us');?>
<div class="about_bnr_area">
	<div class="container"><p class="about_bnr_txt">Info<br><b>About Us</b></p></div>
</div>

<?php $content1 = get_db_single_row('wl_about','description,image1,image2',array('id'=>'1'));?>

<div class="about_sec_area1">
	<div class="container">
		<div class="row">
			<div class="col-lg-5 no_pad">
				<div class="about_sec1">
					<p class="about_heading">HELLO EVERYONE<br><b>I am <br>Zeefit</b></p>
					<div class="about_sec1_txt"><?php echo $content1['description'];?></div>
					<p class="about_sec1_social"><a href="#">FB</a> <a href="#">IN</a> <a href="#">Tw</a></p>
				</div>
			</div>
			<div class="col-lg-7 pl-lg-3 pr-0 text-right position-realtive d-none d-lg-block">
				<?php if($content1['image1']!='' && file_exists(UPLOAD_DIR.'/about/'.$content1['image1'])){?>
                <img src="<?php echo get_image('about',$content1['image1'],'380','593','AR');?>" alt="" class="about_img animated5 wow drop_eff">
                <?php }if($content1['image1']!='' && file_exists(UPLOAD_DIR.'/about/'.$content1['image1'])){?>
				<img src="<?php echo get_image('about',$content1['image2'],'520','810','AR');?>" alt="" class="animated wow drop_eff">
                <?php }?>
			</div>
		</div>
	</div>
</div>


<?php $content2 = get_db_single_row('wl_about','description,image1',array('id'=>'2'));?>

<div class="about_sec_area2">
<div class="row">
<div class="col-lg-6 no_pad about_sec2_img" <?php if($content2['image1']!='' && file_exists(UPLOAD_DIR.'/about/'.$content2['image1'])){?>style="background:url(<?php echo get_image('about',$content2['image1'],'960','600','AR');?>) top center no-repeat; background-size:cover;"<?php }?>></div>
<div class="col-lg-6 about_sec2_txt">
<p class="about_heading">UNCONDITIONAL BEAUTY<br><b>BELEVE<br>IN DESIGN<br>PRIORITY</b></p>
<div class="about_sec1_txt"><?php echo $content1['description'];?></div>
</div>
</div>
</div>

<?php $content3 = get_db_single_row('wl_about','description,image1',array('id'=>'3'));?>
<div class="about_sec_area2">
<div class="row">
<div class="col-lg-6 about_sec2_txt order-2 order-lg-1">
<p class="about_heading">UNCONDITIONAL BEAUTY<br><b>BELEVE<br>IN DESIGN<br>PRIORITY</b></p>
<div class="about_sec1_txt"><?php echo $content3['description'];?></div>
</div>
<div class="col-lg-6 no_pad about_sec3_img order-1 order-lg-2" <?php if($content3['image1']!='' && file_exists(UPLOAD_DIR.'/about/'.$content3['image1'])){?>style="background:url(<?php echo get_image('about',$content3['image1'],'960','600','AR');?>) top center no-repeat; background-size:cover;"<?php }?>></div>
</div>
</div>

<?php $content4 = get_db_single_row('wl_about','description',array('id'=>'4'));?>

<div class="about_sec_area4">
	<div class="container">
		<div class="row">
			<div class="col-lg-6">
				<p class="about_heading2">SUMMER COLLECTION<br><b>Shopping Everyday</b></p>
				<div class="about_sec1_txt"><?php echo $content4['description'];?></div>
			</div>
            <?php $get_category = $this->db->query("SELECT category_name,about_cent FROM wl_categories WHERE status='1' AND is_about='1' ORDER BY RAND() LIMIT 0,4 ")->result_array();
			if(is_array($get_category) && !empty($get_category))
			{
			?>
			<div class="col-lg-6 mt-2">
            <?php foreach($get_category as $key=>$val){?>
				<div class="mb-4">
					<p class="float-left fs15 mb-1 text-uppercase"><?php echo $val['category_name'];?></p>
					<p class="float-right fs15 mb-1"><?php echo $val['about_cent'];?>%</p>
					<p class="clearfix"></p>
					<div class="progress mt-1"><div class="progress-bar bg-info" style="width:<?php echo $val['about_cent'];?>%"></div></div>
				</div>
                <?php }?>
				
			</div>
            <?php }?>
		</div>
	</div>
</div>

<div class="about_testi_area">
	<div class="container">
		<?php if($total_testimonials>0){?>
		<p class="about_heading2">Our Testimonials<br><b>what your customer say about us</b></p>
		<div class="offset-xl-2 col-xl-8">
			<div id="about_testi_scroll" class="owl-carousel owl-theme">
				<?php foreach($res_testimonials as $key=>$val){?>
				<div class="item">
					<div class="about_testi_txt"><?php echo char_limiter($val['testimonial_description'],160);?></div>
					<div class="circle"><span><img src="<?php echo get_image('testimonials',$val['photo'],72,72,'R');?>" alt="<?php echo escape_chars($val['poster_name']);?>"></span></div>
					<p class="name"><?php echo $val['poster_name'];?></p>
					<!--p class="mt-2">CEO zeefit</p-->
				</div>
				<?php }?>
			</div>
			<p class="text-center mt-4"><a href="<?php echo site_url('testimonials');?>" class="ban_btn">View More</a></p>
		</div>
		<?php }?>

		<?php  if($total_brands > 0){?>
		<div id="about_brand_scroll" class="owl-carousel owl-theme mt-5 mb-4">
			<?php foreach($res_brands as $key=>$val){
				$escaped_title=escape_chars($val['brand_name']);
			?>
			<div class="item"><p class="about_brand" title="<?php echo $escaped_title;?>"><span><img src="<?php echo get_image('brands',$val['brand_image'],390,230,'R');?>" alt=""></span></p></div>
			<?php }?>
		</div>
		<?php }?>
	</div>
</div>
<?php $this->load->view("bottom_application");?>