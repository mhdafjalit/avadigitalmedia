<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<link rel="shortcut icon" href="<?php echo base_url();?>favicon.ico">
<title>United Graphix</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
$meta_rec = $this->meta_info;
if( is_array($meta_rec) && !empty($meta_rec) ){
	?>
	<title><?php echo $meta_rec['meta_title'];?></title>
	<meta name="description" content="<?php echo $meta_rec['meta_description'];?>" />
	<meta  name="keywords" content="<?php echo $meta_rec['meta_keyword'];?>" />
	<?php
}?>
<link href="<?php echo base_url(); ?>assets/developers/css/proj.css" rel="stylesheet" type="text/css" />
<script type="text/javascript"> var _siteRoot='index.html',_root='index.html';</script>
<script type="text/javascript"> var site_url = '<?php echo site_url();?>';</script>
<script type="text/javascript"> var theme_url = '<?php echo theme_url();?>';</script>
<script type="text/javascript"> var resource_url = '<?php echo resource_url(); ?>'; var gObj = {};</script>


<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo theme_url(); ?>css/conditional_ak.css">

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body style="padding:10px;">
<div class=" p-2 w-100 newsletter popns-fnt">
<h1 class="pb-1">Bulk Query</h1>
<?php echo validation_message();
  echo form_open_multipart('pages/sendenquiry','class="" role="form"'); ?>
<p class="mb-1"><input name="first_name" id="first_name" type="text" placeholder="Name *" class="p-2 w-100" value="<?php echo set_value('first_name'); ?>"></p>

<p class="mb-1"><input name="email" id="email" type="text" placeholder="Email ID *" class="p-2 w-100" value="<?php echo set_value('email'); ?>" /></p>


<p class="mb-1"><input name="mobile_number" id="mobile_number" type="text" placeholder="Mobile Number *" class="p-2 w-100 " value="<?php echo set_value('mobile_number'); ?>" /></p>
<p class="mb-1">
  <textarea rows="2" class="p-2 w-100" id="description" name="description" placeholder="Bulk Detail *"><?php echo set_value('description'); ?></textarea>
</p>

<p class="mb-1">
  <input name="verification_code" id="verification_code" autocomplete="off" type="text" placeholder="Enter Code *" class="p-2 vam" style="width:120px">
<img src="<?php echo site_url('captcha/normal');?>" class="vam" alt="" id="captchaimage" /> <a href="javascript:void(0);" title="Change Verification Code"><img src="<?php echo theme_url();?>images/ref.png"  alt="Refresh"  onclick="document.getElementById('captchaimage').src='<?php echo site_url('captcha/normal');?>/<?php echo uniqid(time());?>'+Math.random(); document.getElementById('verification_code').focus();" class="p-2 vam"></a>
</p>
 
<div class="clearfix"></div>

<p class="mt-1"><input name="input" type="submit" value="Submit" class=" btn_red trans_eff radius-3"></p>
<?php echo  form_close(); ?>
</div>
</body>
</html>