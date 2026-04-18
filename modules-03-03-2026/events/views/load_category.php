<?php
$sl=$offset;
if(is_array($res_cat) && !empty($res_cat)){
	foreach($res_cat as $key=>$val){
		$escaped_title=escape_chars($val['category_name']);
		$cat_alt = $val['category_alt']!='' ? $val['category_alt'] : $val['category_name'];
	?>
			<li class="listpager"><a href="<?php echo site_url($val['friendly_url']);?>" title="<?php echo $escaped_title;?>"><?php echo char_limiter($val['category_name'],50);?></a> </li>
	<?php
	}
}