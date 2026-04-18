<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');



/* Get meta tags  from  wl_meta_tags */



if( ! function_exists('getMeta')) {
	
	function getMeta() {
		$ci=CI();
		$ci->load->config('seo/config');			

		$uri_page = $ci->uri->uri_string!='' ? $ci->uri->uri_string : "home";
		$res=$ci->db->query("SELECT * FROM wl_meta_tags WHERE page_url='".$uri_page."' ")->row();
		if( is_object($res) ){
			return array(
										"meta_title"=>$res->meta_title,
										"meta_keyword"=>$res->meta_keyword,
										"meta_description"=>$res->meta_description,
										"entity_type"=>$res->entity_type,
										"entity_id"=>$res->entity_id,
										"page_url"=>$res->page_url
									);									
		}else{	
			return $ci->config->item('default_meta');			 
		}
	}
}



/* Check  meta url already exits  */



if( ! function_exists('check_meta'))

{

	function check_meta($page_url)

	{

		$num =0;

		if($page_url!='')

		{

			$ci=CI();	

			$ci->db->from('wl_meta_tags');

			$ci->db->where(array('page_url'=>$page_url));	        

			$num = $ci->db->count_all_results();  

		}			

		return $num;

	}

}



/* Use  to insert all default page meta into  wl_meta_tags  */

if( ! function_exists('create_listing_page_meta'))

{

	function create_listing_page_meta()

	{

	  $ci=CI();

	  $ci->load->config('seo/config');

	  $page_array  = $ci->config->item('controller_name');

	  

	 if( is_array( $page_array ) && !empty(  $page_array ))

	 {
		//trace($page_array);
		 foreach(  $page_array as $k=>$v )

		 {

			 

			$is_url_exits = check_meta($k);

			

			if( $is_url_exits==false )

			{

				$meta_array  =  			

					array(

					'is_fixed'=>'Y',

					'entity_type'=>$v,

					'entity_id'=>0,

					'page_url'=>$k,

					'meta_title'=>$k,

					'meta_description'=>$k,

					'meta_keyword'=>$k

					);			

				  create_meta($meta_array);	

			  

			}

		 }

  	   }

    } 

 }



/* Upade meta url */

if( ! function_exists('update_meta_page_url'))

{

	function update_meta_page_url($type,$entity_id,$page_url)

	{

		$ci=CI();

		if($entity_id!='' && $type!='' && $page_url!='')

		{

			$where = "entity_type ='".$type."' AND entity_id = $entity_id  ";

			$ci->db->update("wl_meta_tags",array('page_url'=>$page_url),$where,FALSE);

		}

		

	}

}





/* Create  meta tags  */

if( ! function_exists('create_meta'))

{

	function create_meta($data=array())

	{

		 $ci=CI();

			  

		 if(is_array($data) && !empty($data) && array_key_exists('page_url',$data) )

		 {

			  $ci->db->insert("wl_meta_tags",$data);			 

			 

		 }

		 

	}

		

}





if(! function_exists('seo_url_title'))

{

  function seo_url_title($str)

  {

	$str = preg_replace("~[^a-zA-Z0-9-_/]~","-",$str);

	$str = preg_replace("~[/]{2,15}~","/",$str);

	$str = preg_replace("~[-]{2,15}~","-",$str);

	$str = strtolower($str);

	$str = trim($str,"-");

	return $str;

  }

}

function seo_add_form_element($params)

{

   $default_params = array(
							'heading_element' => array(
														'field_heading'=>"Page Name",
														'field_name'=>"page_name",
														'field_placeholder'=>"Your Page Name",
														'exparams' => 'style="width:450px;"',
														'inline_error'=>FALSE
													  ),
							'url_element'  => array(
													  'field_heading'=>"Page URL",
													  'field_name'=>"friendly_url",
													  'field_placeholder'=>"Your Page URL",
													  'exparams' => 'style="width:450px; margin-top:5px;"',
													  'pre_seo_url' => '',
													  'pre_url_tag'=>TRUE,
													  'inline_error'=>FALSE,
													  'pre_seo_url_field_exparams'=>'style="width:450px;"'
												   )

						  );

  if(array_key_exists('heading_element',$params))
  {
	$default_params['heading_element'] = array_merge($default_params['heading_element'],$params['heading_element']);
  }
  if(array_key_exists('url_element',$params))
  {
	$default_params['url_element'] = array_merge($default_params['url_element'],$params['url_element']);
  }

  $heading_element_inline_error = $default_params['heading_element']['inline_error'];

  $heading_element_field_heading = $default_params['heading_element']['field_heading'];

  $heading_element_field_name = $default_params['heading_element']['field_name'];

  $heading_element_field_placeholder = $default_params['heading_element']['field_placeholder'];

  $heading_element_field_exparams = $default_params['heading_element']['exparams'];

  $url_element_inline_error = $default_params['url_element']['inline_error'];

  $url_element_field_heading = $default_params['url_element']['field_heading'];

  $url_element_field_name = $default_params['url_element']['field_name'];

  $url_element_field_placeholder = $default_params['url_element']['field_placeholder'];

  $url_element_field_exparams = $default_params['url_element']['exparams'];

  $pre_seo_url = $default_params['url_element']['pre_seo_url'];

  $pre_url_tag = $default_params['url_element']['pre_url_tag'];

  $pre_seo_url_field_exparams = $default_params['url_element']['pre_seo_url_field_exparams'];

?>

<p class="form_title1"><span class="required">*</span> <?php echo $heading_element_field_heading;?> :</p>

    <div class="form_field form_field2"><input name="<?php echo $heading_element_field_name;?>"  value="<?php echo set_value($heading_element_field_name);?>" type="text" class="url_creator form-control" placeholder="<?php echo $heading_element_field_placeholder;?>" <?php echo $heading_element_field_exparams;?> > <a href="#" class="url_from_title">Create URL</a><br />

		

	<div id="error_url_creator" class="red"><?php if($heading_element_inline_error===TRUE){ echo form_error($heading_element_field_name);}?></div>

        </div>

    <p class="clearfix"></p>

    

    <p class="form_title1"><span class="required">**</span> <?php echo $url_element_field_heading;?> :</p>

    <div class="form_field form_field2 seo_url">

    <?php

		if($pre_url_tag === TRUE)

		{

		?>

        <input type="text" name="pre_seo_url" id="pre_seo_url" value="<?php echo $pre_seo_url;?>"  size="40" title="<?php echo $pre_seo_url;?>" readonly class="form-control" <?php echo  $pre_seo_url_field_exparams;?>  />

		<?php

		}

		?>

        <input type="text" name="<?php echo $url_element_field_name;?>" value="<?php echo set_value($url_element_field_name);?>"  class="seo_friendly_url form-control" style="width:450px; margin-top:5px;" placeholder="<?php echo $url_element_field_placeholder;?>" <?php echo  $url_element_field_exparams;?> readonly/> <a href="#" class="change_url">Change</a>

           <div id="error_friendly_url" class="red"><?php if($url_element_inline_error===TRUE){echo form_error($url_element_field_name);}?></div>

    </div>

    

    <p class="clearfix"></p>

<?php

}



function seo_edit_form_element($params)

{

	$default_params = array(
							'heading_element' => array(
														'field_heading'=>"Page Name",
														'field_name'=>"page_name",
														'field_value'=>"",
														'field_placeholder'=>"Your Page Name",
														'exparams' => 'style="width:450px;"',
														'inline_error'=>FALSE
													  ),
							'url_element'  => array(
													  'field_heading'=>"Page URL",
													  'field_name'=>"friendly_url",
													  'field_value'=>"",
													  'field_placeholder'=>"Your Page URL",
													  'exparams' => 'style="width:450px;margin-top:5px;"',
													  'pre_seo_url' => '',
													  'pre_url_tag'=>FALSE,
													  'pre_seo_url_field_exparams'=>'style="width:450px;"',
													  'inline_error'=>FALSE
												   )

						  );

  if(array_key_exists('heading_element',$params))
  {
	$default_params['heading_element'] = array_merge($default_params['heading_element'],$params['heading_element']);
  }
  if(array_key_exists('url_element',$params))
  {
	$default_params['url_element'] = array_merge($default_params['url_element'],$params['url_element']);
  }
  
  $heading_element_inline_error = $default_params['heading_element']['inline_error'];

  $heading_element_field_heading = $default_params['heading_element']['field_heading'];

  $heading_element_field_name = $default_params['heading_element']['field_name'];

  $heading_element_field_value = $default_params['heading_element']['field_value'];

  $heading_element_field_placeholder = $default_params['heading_element']['field_placeholder'];

  $heading_element_field_exparams = $default_params['heading_element']['exparams'];

  $url_element_inline_error = $default_params['url_element']['inline_error'];

  $url_element_field_heading = $default_params['url_element']['field_heading'];

  $url_element_field_name = $default_params['url_element']['field_name'];

  $url_element_field_value = $default_params['url_element']['field_value'];

  $url_element_field_placeholder = $default_params['url_element']['field_placeholder'];

  $url_element_field_exparams = $default_params['url_element']['exparams'];

  $pre_seo_url = $default_params['url_element']['pre_seo_url'];

  $pre_url_tag = $default_params['url_element']['pre_url_tag'];

  $pre_seo_url_field_exparams = $default_params['url_element']['pre_seo_url_field_exparams'];

  ?>

  

  <p class="form_title1"><span class="required">*</span> <?php echo $heading_element_field_heading;?> :</p>

  <div class="form_field form_field2">

   <input name="<?php echo $heading_element_field_name;?>" type="text" class="form-control" value="<?php echo set_value($heading_element_field_name,$heading_element_field_value);?>"  placeholder="<?php echo $heading_element_field_placeholder;?>" <?php echo $heading_element_field_exparams;?>  >

  <div id="error_url_creator" class="red"><?php if($heading_element_inline_error===TRUE){ echo form_error($heading_element_field_name);}?></div>

  </div>

  <p class="clearfix"></p>

  

  <p class="form_title1"><span class="required">**</span> <?php echo $url_element_field_heading;?> :</p>

  <div class="form_field form_field2 seo_url">

   <?php

   if($pre_url_tag === TRUE){

	   ?>

	   <input type="text" name="pre_seo_url" id="pre_seo_url" value="<?php echo $pre_seo_url;?>"  size="40" title="<?php echo $pre_seo_url;?>" class="form-control " <?php echo  $pre_seo_url_field_exparams;?> />

	   <?php

   }?>

   <input type="text" name="<?php echo $url_element_field_name;?>" value="<?php echo set_value($url_element_field_name,$url_element_field_value);?>"  class="seo_friendly_url edit_url form-control " placeholder="<?php echo $url_element_field_placeholder;?>" <?php echo  $url_element_field_exparams;?>  />

   <div id="error_friendly_url" class="red"><?php echo form_error($url_element_field_name);?></div>

   <script type="text/javascript">

   prev_url_val = '<?php echo set_value($url_element_field_name,$url_element_field_value);?>';

   </script>

  </div>

  <p class="clearfix"></p>

  <?php

}

/******************* Making the Meta tags  much better  ***************/

 

if( ! function_exists('clean') )

{

	function clean($text)

	{ 

		$text = html_entity_decode($text,ENT_QUOTES,'UTF-8');

		$text = strip_tags($text);//erases any html markup

		$text = preg_replace('/\s\s+/', ' ', $text);//erase possible duplicated white spaces

		$text = str_replace (array('\r\n', '\n', '+'), ',', $text);//replace possible returns 

		return trim($text); 

	}

}



if( ! function_exists('cmp') )

{

	function cmp($a, $b) 

	{

			if ($a == $b) return 0; 

	

			return ($a < $b) ? 1 : -1; 

	}

}

	

if( ! function_exists('get_text') )

{ 

	function get_text($text, $length = 220)

	{

		return limit_chars(clean($text),$length,'',TRUE);

	}

}



if( ! function_exists('get_keywords') )

{ 

	function get_keywords($text, $max_keys = 20)

	{

		    $ci=CI();

			$ci->load->config('seo/config');	

		    $min_word_length  = $ci->config->item('min_word_length');

			$banned_words     = $ci->config->item('banned_words');

			//array to keep word->number of repetitions 

			$wordcount = array_count_values(str_word_count(clean($text),1));	

		

			foreach ($wordcount as $key => $value) 

			{

				if ( (strlen($key)<= $min_word_length) || in_array($key,$banned_words) )

					unset($wordcount[$key]);

			}			

			//sort keywords from most repetitions to less 

			uasort($wordcount,'cmp');	

			//keep only X keywords

			$wordcount = array_slice($wordcount,0, $max_keys);	

			//return keywords on a string

			return implode(', ', array_keys($wordcount));

	}



} 

 

if( ! function_exists('limit_chars') )

{ 	 

  function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)

  {

        $end_char = ($end_char === NULL) ? 'â€¦' : $end_char;



        $limit = (int) $limit;



        if (trim($str) === '' || mb_strlen($str) <= $limit)

            return $str;



        if ($limit <= 0)

            return $end_char;



        if ($preserve_words === FALSE)

            return rtrim(mb_substr($str, 0, $limit)).$end_char;



        // Don't preserve words. The limit is considered the top limit.

        // No strings with a length longer than $limit should be returned.

        if ( ! preg_match('/^.{0,'.$limit.'}\s/us', $str, $matches))

            return $end_char;



        return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $end_char);

    }

	

}