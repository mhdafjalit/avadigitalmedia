<?php if(is_array($val) && !empty($val)){
?>
<div class="news_box d-sm-flex justify-content-between align-items-start trans_eff">
	<div class="news_pic text-center overflow-hidden rounded-3"><figure class="align-middle d-table-cell"><a href="#"><img src="<?php echo theme_url();?>images/news-pic1.jpg" alt="" width="145" height="145" class="mw-100 mh-100"></a></figure></div>
	<div class="news_cont">
		<p class="news_title overflow-hidden"><a href="news-details.htm" class="fw-bold purple">Lorem ipsum dolor sit amet consect adipiscing elit, sed do eiusmod</a></p>
		<p class="fs-8 text-secondary mt-2 mb-2">Apr 02, 2024</p>
		<p class="news_txt text-black fs-7 lh-sm overflow-hidden">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna</p>
		<a href="<?php echo site_url('admin/news/details');?>" title="Learn More" class="text-primary fs-8 text-uppercase fw-semibold mt-2 d-inline-block">Learn More</a>
	</div>
</div>
<?php }?>