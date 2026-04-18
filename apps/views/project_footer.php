
<?php
$admin_address='';
if($this->admin_info->address !=''){
	$admin_address.=$this->admin_info->address;
}
/*
if($this->admin_info->city !=''){
	$admin_address.=', '.$this->admin_info->city;
}
if($this->admin_info->state !=''){
	$admin_address.=', '.$this->admin_info->state;
}
if($this->admin_info->zipcode !=''){
	$admin_address.=' - '.$this->admin_info->zipcode;
}
if($this->admin_info->country !=''){
	$admin_address.=', ('.strtoupper($this->admin_info->country).')';
}
*/
$site_title_text = escape_chars($this->config->item('site_name'));
$link_admin_facebook		=	$this->admin_info->facebook_link;
$link_admin_twitter			=	$this->admin_info->twitter_link;
$link_admin_linkedin		=	$this->admin_info->linkedin_link;
$link_admin_instagram		=	$this->admin_info->instagram_link;
$link_admin_youtube			=	$this->admin_info->youtube_link;
$has_social_media_links = (!empty($link_admin_facebook) || !empty($link_admin_twitter) || !empty($link_admin_instagram) || !empty($link_admin_youtube) || !empty($link_admin_linkedin) ) ? 1 : 0;
?>