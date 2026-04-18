<?php
if(!empty($page_meta_share))
{
	$share_summary = $page_meta_share['meta_description'];
	$share_image = $page_meta_share['meta_img'];
	$share_title = $page_meta_share['meta_title'];
	$share_url = $page_meta_share['meta_url'];
	$share_image_what_app = $page_meta_share['whats_app_share_img'];
}?>

<div class="float-md-left follow_sec mt-3"> 
	<a href="javascript:void(0)" title="Facebook" style="background:#3a5897;" data-network="facebook" data-with="facebook" class="st-custom-button" data-url="<?php echo $share_url;?>" data-image="<?php echo $share_image;?>" data-description="<?php echo $share_summary;?>" data-title="<?php echo $share_title;?>"><span class="fab fa-facebook-f"></span></a> 
	
	<a href="javascript:void(0)" title="Twitter" style="background:#1ea1f3;" data-network="twitter" data-with="twitter" class="st-custom-button" data-url="<?php echo $share_url;?>" data-image="<?php echo $share_image;?>"><span class="fab fa-twitter"></span></a> 

	<a href="javascript:void(0)" title="Linkedin" style="background:#007bb6;" data-network="linkedin" data-with="linkedin" class="st-custom-button" data-url="<?php echo $share_url;?>" data-image="<?php echo $share_image;?>" data-description="<?php echo $share_summary;?>" data-title="<?php echo $share_title;?>"><span class="fab fa-linkedin-in"></span></a> 

	<a href="javascript:void(0)" title="Whatsapp" style="background:#40c351;" data-network="whatsapp" class="st-custom-button" data-url="<?php echo $share_url;?>" data-image="<?php echo $share_image_what_app;?>" data-description="<?php echo $share_summary;?>" data-title="<?php echo $share_title;?>"><span class="fab fa-whatsapp"></span></a>
</div>
<?php /*
<a href="javascript:void(0)" data-network="telegram" class="st-custom-button" data-url="<?php echo $share_url;?>" data-image="<?php echo $share_image_what_app;?>" data-description="<?php echo $share_summary;?>" data-title="<?php echo $share_title;?>" style="background:#000;cursor:pointer;" title="Telegram"><i class="fab fa-telegram"></i></a>
*/ ?>