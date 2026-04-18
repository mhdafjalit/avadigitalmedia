<?php
$ci = & get_instance();
$header_menu_section =!empty($ci->header_menu_section) ?  $ci->header_menu_section : "";
$link_admin_facebook		=	$this->admin_info->facebook_link;
$link_admin_twitter			=	$this->admin_info->twitter_link;
$link_admin_linkedin		=	$this->admin_info->linkedin_link;
$link_admin_instagram		=	$this->admin_info->instagram_link;
$link_admin_youtube			=	$this->admin_info->youtube_link;
$has_social_media_links = (!empty($link_admin_facebook) || !empty($link_admin_twitter) || !empty($link_admin_instagram) || !empty($link_admin_youtube) || !empty($link_admin_linkedin) ) ? 1 : 0;
$site_title_text = escape_chars($this->config->item('site_name'));
?>
