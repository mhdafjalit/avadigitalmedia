<?php if(is_array($val) && !empty($val)){
	$escaped_title = escape_chars($val['news_title']);
	$link_url = site_url($val['friendly_url']);
	$news_desc = char_limiter($val['news_description'],120);
?>
<div class="news_box d-sm-flex justify-content-between align-items-start trans_eff">
	<div class="news_pic text-center overflow-hidden rounded-3">
	    <figure class="align-middle d-table-cell">
	        	<a href="<?php echo $link_url;?>" title="<?php echo $escaped_title;?>">
	            <img src="<?php echo get_image('events',$val['media'],145,145,'AR');?>" alt="<?php echo $escaped_title;?>" width="145" height="145" class="mw-100 mh-100">
	        </a>
        </figure>
    </div>
	<div class="news_cont">
		<p class="news_title overflow-hidden">
		    <a href="<?php echo $link_url;?>" class="fw-bold purple"><?php echo char_limiter($val['news_title'],50);?></a>
		</p>
		<p class="fs-8 text-secondary mt-2 mb-2"><?php echo getDateFormat($val['event_date1'],1);?></p>
		<p class="news_txt text-black fs-7 lh-sm overflow-hidden"><?= $news_desc;?></p>
		<a href="<?php echo $link_url;?>" title="Learn More" class="text-primary fs-8 text-uppercase fw-semibold mt-2 d-inline-block">Learn More</a>
	</div>
</div>
<?php }?>