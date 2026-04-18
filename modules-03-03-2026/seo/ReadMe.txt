1: Add  public $meta_info;    apps\core\MY_Controller 


2: Add these  lines  in   apps\core\MY_Controller in construct


 $this->load->helper('seo/seo');
 $this->load->config('seo/config');

if($this->uri->segment(1)!='sitepanel')
{
   
   $this->meta_info  = getMeta();
				
}



3: apps\views\top_application put 


<?php

$meta_rec = $this->meta_info;  

if( is_array($meta_rec) && !empty($meta_rec) )
{	
?>

<title><?php echo $meta_rec['meta_title'];?></title>
<meta name="description" content="<?php echo $meta_rec['meta_description'];?>" />
<meta  name="keywords" content="<?php echo $meta_rec['meta_keyword'];?>" />

<?php
}
?>


4:  ***********  Remove  the getMeta()  from utils_helper.php  line  505  ***********

5: 


/* Add Form


$default_params = array(
			'heading_element' => array(
														  'field_heading'=>"Name",
														  'field_name'=>"category_name",
														  'field_placeholder'=>"Your Catgeory Name",
														  'exparams' => 'size="40"'
														),

		'url_element'  => array(
											  'field_heading'=>"Page URL",
														  'field_name'=>"friendly_url",
														  'field_placeholder'=>"Your Page URL",
														  'exparams' => 'size="40"',
														  'pre_seo_url' =>'',
														  'pre_url_tag'=>FALSE
													   )

							  );

			if(is_array($parentData))
			{
			  $pre_seo_url  = base_url().$parentData['friendly_url']."/";
			  $default_params['url_element']['pre_seo_url'] = $pre_seo_url;
			  $default_params['url_element']['pre_url_tag'] = TRUE;
			  $default_params['url_element']['exparams'] = 'size="30"';
			}
			seo_add_form_element($default_params);


///////////Note 



heading_element represents your heading field element eg: Category Name,News Title,CMS Heading.......

url_element represent the URL element 

exparams represets all other attributes for the corresponding fields 

pre_seo_tag represents any Pre URL attachment element

	



7: $route['seo/sitemap\.xml'] = "seo/sitemap";


8:  Need to validate product name / category name ,  testimonial  tile ,  all   site title   max length  200 




9 :  Also  need  to  update  apps\config\routes.php  
			


10 Read RSS :

$this->load->library('seo/rssparser');                          // load library

$this->rssparser->set_feed_url('http://news.google.com/news?pz=1&cf=all&ned=hi_in&hl=hi&output=rss');  // get feed

$this->rssparser->set_cache_life(30);                       // Set cache life time in minutes

$rss = $this->rssparser->getFeed(6);   





