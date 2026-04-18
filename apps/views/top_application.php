<?php
/*Control for body styling*/
//Just pass classname you need it here
$has_body_class = !empty($has_body_class) ? $has_body_class : '';
//Pass the style props here excluding style tag
$has_body_style = !empty($has_body_style) ? $has_body_style : '';
$ci = & get_instance();

?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<?php
$meta_rec = !empty($res_dynamic_meta)  ?  $res_dynamic_meta : $this->meta_info;
if( is_array($meta_rec) && !empty($meta_rec) ){ ?>
<title><?php echo $meta_rec['meta_title'];?></title>
<meta name="description" content="<?php echo $meta_rec['meta_description'];?>" />
<meta  name="keywords" content="<?php echo $meta_rec['meta_keyword'];?>" />
<?php }
if($this->config->item('csrf_protection')===TRUE){	
?>
<meta name="csrf-token-name" content="<?php echo $this->security->get_csrf_token_name(); ?>" />
<meta name="csrf-token-value" content="<?php echo $this->security->get_csrf_hash(); ?>" />
<?php }
if ( $this->admin_info->google_web_code!="" ){ ?>
<meta name="google-site-verification" content="<?php echo $this->admin_info->google_web_code;?>" />
<?php } ?>
<?php
if(!empty($page_meta_share))
{  
	$is_whats_app = preg_match("~whatsapp~i",$_SERVER['HTTP_USER_AGENT']) ? TRUE : FALSE;
	$meta_share_img = $is_whats_app===TRUE ? $page_meta_share['whats_app_share_img'] : $page_meta_share['meta_img'];
?>
	<meta property="og:title" content="<?php echo $page_meta_share['meta_title'];?>" />
	<meta property="og:url" content="<?php echo $page_meta_share['meta_url'];?>" />
	<meta property="og:image" itemprop="image" content="<?php echo $meta_share_img;?>" /> 
	<meta property="og:description" content="<?php echo $page_meta_share['meta_description'];?>" />
	<meta name="twitter:card" content="photo">
	<meta name="twitter:url" content="<?php echo $page_meta_share['meta_url'];?>">
	<meta name="twitter:title" content="<?php echo $page_meta_share['meta_title'];?>">
	<meta name="twitter:description" content="<?php echo $page_meta_share['meta_description'];?>">
	<meta name="twitter:image" content="<?php echo $meta_share_img?>" />
	<script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=618cdbc528307600135656d0&product=sop' async='async'></script>
<?php
}
?>
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico">
<link rel="stylesheet" href="<?php echo theme_url();?>css/bootstrap.css" >
<link href="<?php echo base_url(); ?>assets/developers/css/proj.css" rel="stylesheet" type="text/css" />
<link href="<?php echo theme_url();?>css/conditional-preet.css" type="text/css" rel="stylesheet">
<?php if(is_array($ci->inject_header_css_files) && !empty($ci->inject_header_css_files)){
	foreach($ci->inject_header_css_files as $key=>$val){
		if($val['insert']==1){
			echo '<link rel="stylesheet" href="'.$val['path'].'">';
		}
	}
}
?>
<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="<?php echo resource_url(); ?>Scripts/jquery-1.10.2.js"></script>
<script type="text/javascript">
<?php /* Always extend this object by checking its existence do not overwrite*/?>
var gObj= {base_url:'<?php echo base_url();?>',resource_url:'<?php echo resource_url();?>','usefbox':<?php echo !isset($usefbox) ? 1 : $usefbox;?>};
<?php if($this->config->item('csrf_protection')===TRUE){?>gObj['enable_csrf']=1;<?php }?>
</script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<style>
.jconfirm .jconfirm-bg { z-index: -999; }
</style>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<script src="<?php echo base_url(); ?>assets/developers/js/common.js"></script>
<?php
if ($this->admin_info->google_analytics_id!=""){
	echo '<script>'.$this->admin_info->google_web_code.'</script>';
}
if ($this->admin_info->google_analytics_id!=""){
	?>
	<script>
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?php echo $this->admin_info->google_analytics_id;?>']);
	_gaq.push(['_trackPageview']);
	(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
	</script>
	<?php
}?>


</head>
<body <?php echo !empty($has_body_class) ? 'class="'.$has_body_class.'"' : '';?> <?php echo !empty($has_body_style) ? 'style="'.$has_body_style.'"' : '';?>>
<?php
$this->load->view('backend_back_view');
if(!isset($has_header) || $has_header){
	$this->load->view('project_header');
}