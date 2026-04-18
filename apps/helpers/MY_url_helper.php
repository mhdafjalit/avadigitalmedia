<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

/**
 * CodeIgniter URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Philip Sturgeon
 */

// ------------------------------------------------------------------------

/**
 * Create URL Title - modified version
 *
 * Takes a "title" string as input and creates a
 * human-friendly URL string with either a dash
 * or an underscore as the word separator.
 *
 * Added support for Cyrillic characters.
 *
 * @access	public
 * @param	string	the string
 * @param	string	the separator: dash, or underscore
 * @return	string
 */
if ( ! function_exists('url_title'))
{
	function url_title($str, $separator = 'dash', $lowercase = TRUE)
    {
        $CI =& get_instance();

        $foreign_characters = array(
            '/ÃƒÂ¤|ÃƒÂ¦|Ã‡Â½/' => 'ae',
            '/ÃƒÂ¶|Ã…â€œ/' => 'oe',
            '/ÃƒÂ¼/' => 'ue',
            '/Ãƒâ€ž/' => 'Ae',
            '/ÃƒÅ“/' => 'Ue',
            '/Ãƒâ€“/' => 'Oe',
            '/Ãƒâ‚¬|Ãƒï¿½|Ãƒâ€š|ÃƒÆ’|Ãƒâ€ž|Ãƒâ€¦|Ã‡Âº|Ã„â‚¬|Ã„â€š|Ã„â€ž|Ã‡ï¿½|Ã�ï¿½/' => 'A',
            '/ÃƒÂ |ÃƒÂ¡|ÃƒÂ¢|ÃƒÂ£|ÃƒÂ¥|Ã‡Â»|Ã„ï¿½|Ã„Æ’|Ã„â€¦|Ã‡Å½|Ã‚Âª|Ã�Â°/' => 'a',
            '/Ã�â€˜/' => 'B',
            '/Ã�Â±/' => 'b',
            '/Ãƒâ€¡|Ã„â€ |Ã„Ë†|Ã„Å |Ã„Å’|Ã�Â¦/' => 'C',
            '/ÃƒÂ§|Ã„â€¡|Ã„â€°|Ã„â€¹|Ã„ï¿½|Ã‘â€ /' => 'c',
            '/Ãƒï¿½|Ã„Å½|Ã„ï¿½|Ã�â€�/' => 'D',
            '/ÃƒÂ°|Ã„ï¿½|Ã„â€˜|Ã�Â´/' => 'd',
            '/ÃƒË†|Ãƒâ€°|ÃƒÅ |Ãƒâ€¹|Ã„â€™|Ã„â€�|Ã„â€“|Ã„Ëœ|Ã„Å¡|Ã�â€¢|Ã�ï¿½|Ã�Â­/' => 'E',
            '/ÃƒÂ¨|ÃƒÂ©|ÃƒÂª|ÃƒÂ«|Ã„â€œ|Ã„â€¢|Ã„â€”|Ã„â„¢|Ã„â€º|Ã�Âµ|Ã‘â€˜|Ã‘ï¿½/' => 'e',
            '/Ã�Â¤/' => 'F',
            '/Ã‘â€ž/' => 'f',
            '/Ã„Å“|Ã„Å¾|Ã„Â |Ã„Â¢|Ã�â€œ/' => 'G',
            '/Ã„ï¿½|Ã„Å¸|Ã„Â¡|Ã„Â£|Ã�Â³/' => 'g',
            '/Ã„Â¤|Ã„Â¦|Ã�Â¥/' => 'H',
            '/Ã„Â¥|Ã„Â§|Ã‘â€¦/' => 'h',
            '/ÃƒÅ’|Ãƒï¿½|ÃƒÅ½|Ãƒï¿½|Ã„Â¨|Ã„Âª|Ã„Â¬|Ã‡ï¿½|Ã„Â®|Ã„Â°|Ã�Ëœ/' => 'I',
            '/ÃƒÂ¬|ÃƒÂ­|ÃƒÂ®|ÃƒÂ¯|Ã„Â©|Ã„Â«|Ã„Â­|Ã‡ï¿½|Ã„Â¯|Ã„Â±|Ã�Â¸/' => 'i',
            '/Ã„Â´|Ã�â„¢/' => 'J',
            '/Ã„Âµ|Ã�Â¹/' => 'j',
            '/Ã„Â¶|Ã�Å¡/' => 'K',
            '/Ã„Â·|Ã�Âº/' => 'k',
            '/Ã„Â¹|Ã„Â»|Ã„Â½|Ã„Â¿|Ã…ï¿½|Ã�â€º/' => 'L',
            '/Ã„Âº|Ã„Â¼|Ã„Â¾|Ã…â‚¬|Ã…â€š|Ã�Â»/' => 'l',
            '/Ã�Å“/' => 'M',
            '/Ã�Â¼/' => 'm',
            '/Ãƒâ€˜|Ã…Æ’|Ã…â€¦|Ã…â€¡|Ã�ï¿½/' => 'N',
            '/ÃƒÂ±|Ã…â€ž|Ã…â€ |Ã…Ë†|Ã…â€°|Ã�Â½/' => 'n',
            '/Ãƒâ€™|Ãƒâ€œ|Ãƒâ€�|Ãƒâ€¢|Ã…Å’|Ã…Å½|Ã‡â€˜|Ã…ï¿½|Ã†Â |ÃƒËœ|Ã‡Â¾|Ã�Å¾/' => 'O',
            '/ÃƒÂ²|ÃƒÂ³|ÃƒÂ´|ÃƒÂµ|Ã…ï¿½|Ã…ï¿½|Ã‡â€™|Ã…â€˜|Ã†Â¡|ÃƒÂ¸|Ã‡Â¿|Ã‚Âº|Ã�Â¾/' => 'o',
            '/Ã�Å¸/' => 'P',
            '/Ã�Â¿/' => 'p',
            '/Ã…â€�|Ã…â€“|Ã…Ëœ|Ã�Â /' => 'R',
            '/Ã…â€¢|Ã…â€”|Ã…â„¢|Ã‘â‚¬/' => 'r',
            '/Ã…Å¡|Ã…Å“|Ã…Å¾|Ã…Â |Ã�Â¡/' => 'S',
            '/Ã…â€º|Ã…ï¿½|Ã…Å¸|Ã…Â¡|Ã…Â¿|Ã‘ï¿½/' => 's',
            '/Ã…Â¢|Ã…Â¤|Ã…Â¦|Ã�Â¢/' => 'T',
            '/Ã…Â£|Ã…Â¥|Ã…Â§|Ã‘â€š/' => 't',
            '/Ãƒâ„¢|ÃƒÅ¡|Ãƒâ€º|Ã…Â¨|Ã…Âª|Ã…Â¬|Ã…Â®|Ã…Â°|Ã…Â²|Ã†Â¯|Ã‡â€œ|Ã‡â€¢|Ã‡â€”|Ã‡â„¢|Ã‡â€º|Ã�Â£/' => 'U',
            '/ÃƒÂ¹|ÃƒÂº|ÃƒÂ»|Ã…Â©|Ã…Â«|Ã…Â­|Ã…Â¯|Ã…Â±|Ã…Â³|Ã†Â°|Ã‡â€�|Ã‡â€“|Ã‡Ëœ|Ã‡Å¡|Ã‡Å“|Ã‘Æ’/' => 'u',
            '/Ã�â€™/' => 'V',
            '/Ã�Â²/' => 'v',
            '/Ãƒï¿½|Ã…Â¸|Ã…Â¶|Ã�Â«/' => 'Y',
            '/ÃƒÂ½|ÃƒÂ¿|Ã…Â·|Ã‘â€¹/' => 'y',
            '/Ã…Â´/' => 'W',
            '/Ã…Âµ/' => 'w',
            '/Ã…Â¹|Ã…Â»|Ã…Â½|Ã�â€”/' => 'Z',
            '/Ã…Âº|Ã…Â¼|Ã…Â¾|Ã�Â·/' => 'z',
            '/Ãƒâ€ |Ã‡Â¼/' => 'AE',
            '/ÃƒÅ¸/'=> 'ss',
            '/Ã„Â²/' => 'IJ',
            '/Ã„Â³/' => 'ij',
            '/Ã…â€™/' => 'OE',
            '/Ã†â€™/' => 'f',
            '/Ã�Â§/' => 'Ch',
            '/Ã‘â€¡/' => 'ch',
            '/Ã�Â®/' => 'Ju',
            '/Ã‘Å½/' => 'ju',
            '/Ã�Â¯/' => 'Ja',
            '/Ã‘ï¿½/' => 'ja',
            '/Ã�Â¨/' => 'Sh',
            '/Ã‘Ë†/' => 'sh',
            '/Ã�Â©/' => 'Shch',
            '/Ã‘â€°/' => 'shch',
            '/Ã�â€“/' => 'Zh',
            '/Ã�Â¶/' => 'zh',
        );

        $str = preg_replace(array_keys($foreign_characters), array_values($foreign_characters), $str);

        $replace = ($separator == 'dash') ? '-' : '_';

        $trans = array(
            '&\#\d+?;'                => '',
            '&\S+?;'                => '',
            '\s+'                    => $replace,
            '[^a-z0-9\-\._]' => '',
            $replace.'+'            => $replace,
            $replace.'$'            => $replace,
            '^'.$replace            => $replace,
            '\.+$'                    => ''
        );

        $str = strip_tags($str);

        foreach ($trans as $key => $val)
        {
            $str = preg_replace("#".$key."#i", $val, $str);
        }

        if ($lowercase === TRUE)
        {
            if( function_exists('mb_convert_case') )
            {
                $str = mb_convert_case($str, MB_CASE_LOWER, "UTF-8");
            }
            else
            {
                $str = strtolower($str);
            }
        }

        $str = preg_replace('#[^'.$CI->config->item('permitted_uri_chars').']#i', '', $str);
        return trim(stripslashes($str));
     }
}


 function switch_account($type)
 {
		switch ($type )
		{
		    case 1:

		      redirect('members', '');

			break;

			default:

            redirect('members', '');

		}

 }


// ------------------------------------------------------------------------


/**
 * Theme URL
 *
 * Returns the Ionize current theme URL
 *
 * @access	public
 * @return	string
 */

if ( ! function_exists('resource_url'))
{
	function resource_url()
	{
		return base_url()."assets/designer/resources/";
	}
}

if ( ! function_exists('theme_url'))
{
	function theme_url()
	{
		return base_url()."assets/designer/themes/default/";
	}
}

if ( ! function_exists('img_url'))
{
	function img_url()
	{
		return base_url()."uploaded_files/";
	}
}

if ( ! function_exists('thumb_cache_url'))
{
	function thumb_cache_url()
	{
		return base_url()."uploaded_files/thumb_cache/";
	}

}

if ( ! function_exists('clear_search'))
{
  function clear_search ()
  {

	   $ci            =  &get_instance();
	   $pagesz        = $ci->config->item('per_page');
	   $clear_search  = $ci->input->get();
	   $clear_search  = @array_filter( $clear_search);
	   $clear_search  = @array_filter( $clear_search);

	   if( is_array($clear_search) && array_key_exists('city',$clear_search) && ($clear_search['city']=='Search City'))
	   {
		   unset($clear_search['city']);
	   }

		if( is_array($clear_search) && array_key_exists('plan',$clear_search) && ($clear_search['plan']=='all'))
	   {
		   unset($clear_search['plan']);
	   }

	   if( is_array($clear_search) && array_key_exists('pagesize',$clear_search) && ($clear_search['pagesize']==$pagesz))
	   {
		   unset($clear_search['pagesize']);
	   }

		unset($clear_search['search_x']);
		unset($clear_search['search_y']);
		unset($clear_search['input_x']);
		unset($clear_search['input_y']);



	  ?>

					  <?php if(is_array( $clear_search) && !empty(  $clear_search))
					  {
						  ?>

						  <div class="paging_cntnr mt10" style="width:650px; margin-bottom:5px;">
							  <table width="80%" border="0" cellspacing="0" cellpadding="0">
							 <tr>
							 <td  class="tahoma b ft-10 black"> Clear Search : <td>
							 <td>
						  <?php
						  foreach( $clear_search as $k=>$v)
						  {
							  if(trim($k)!='')
							  {
							  ?>

							<p class="fl ml30">
							<?php echo ucfirst($k);?>  <a href="<?php echo query_string('',array($v=>$k));?>"><img src="<?php echo base_url()?>assets/clear_search.jpg" class="v-mid mb2"  /></a> </p>


						<?php
							  }
						  }
					   ?>
					   <p class="fl ml40">All <a href="<?php echo current_url();?>"><img src="<?php echo base_url()?>assets/clear_search.jpg"  /></a>  </p>
					   <?php
					   }
					   ?>
					  </td>
						</tr>
					</table>
					  </div>

  <?php
  }
}

if ( ! function_exists('navigation_breadcrumb')){
	function navigation_breadcrumb($page_title, $crumbs="",$wrapperType=1){
	  $breadcrumbs_html = "";
	  $breadcrumbs_html.='<li class="breadcrumb-item active">You are here</li>';
      $breadcrumbs_html.='<li class="breadcrumb-item">'.$page_title.'</li>';
	   if(is_array($crumbs)){
		   foreach($crumbs as $key=>$val){
				$has_url = !isset($val['has_url']) ? 1 : $val['has_url'];
				if(!$has_url){
					$breadcrumbs_html.= '<li class="breadcrumb-item active" aria-current="page">'.$val['heading'].'</li>';
				}else{
					$breadcrumbs_html.= '<li class="breadcrumb-item">'.anchor($val['url'],$val['heading'],array("title"=>$val['heading'],"itemprop"=>"url")).'</li>';
				}
		   }
	   }
		switch($wrapperType){
			case 1:
				$breadcrumbs_html='<nav aria-label="breadcrumb" class="d-inline-block"><ol class="breadcrumb m-0 mt-3 mt-xl-4 p-0">'.$breadcrumbs_html.'</ol></nav>';
			break;
			case 2:
				$breadcrumbs_html='<ol class="breadcrumb bg-white" style="padding:5px 15px;">'.$breadcrumbs_html.'</ol>';
			break;
	  }
	  return $breadcrumbs_html;
	}
}