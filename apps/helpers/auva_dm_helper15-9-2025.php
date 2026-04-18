<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
* The global CI helpers 


*/

if ( ! function_exists('check_permission_section'))
{
	 function check_permission_section($section)
	 {
		$CI = CI();
		if($section!=TRUE)
		{
			$CI->session->set_userdata(array('msg_type'=>'error'));
			$CI->session->set_flashdata('error','You have not permission for this section');
			redirect('sitepanel/dashbord', '');
		}
	} 
}

if ( ! function_exists('subadmin_activity'))
{
	 function subadmin_activity($subadmin_id,$section,$action)
	 {
		 $CI = CI();
		 
		if($action=='')
		{
			$action = 'listing';
		}
		if($section!='')
		{
			$posted_data = array(				
					'subadmin_id'	=>	$subadmin_id,
					'login_time'	=>	$CI->config->item('config.date.time'),
					'action'		=>	$action,
					'section'		=>	$section				
			);
			
			$CI->db->insert('tbl_subadmin_log',$posted_data,FALSE);
		}
	} 
}

if ( ! function_exists('company_overall_rating'))
{
	function company_overall_rating($user_id)
	{
		$CI = CI();
		$res = $CI->db->query("SELECT AVG(rate) as rating, count(review_id) AS total_reviews FROM wl_reviews WHERE vendor_id ='".$user_id."' AND status ='1' AND rev_type='S' ")->row_array();
		return $res;
	}
}

if ( ! function_exists('get_blog_star_rating')){
	
	function get_blog_star_rating($val){
		
		for($i=1;$i<=5;$i++){			
			if($i<=$val){
				echo '<span class="fas fa-star"></span>';
				
			}else{
				echo '<span class="far fa-star"></span>';			
			}
		}
	}
}


if ( ! function_exists('get_star_rating_backend')){
	
	function get_star_rating_backend($val,$num=5){
		
		for($i=1;$i<=$num;$i++){
			
			if($i<=$val){
				echo '<img src="'.theme_url().'images/star.png" style="" class="ml5">';
				//if($i%5==0){echo '<br>';}
				
			}else{
				echo '<img src="'.theme_url().'images/star2.png" style="" class="ml5">';
				//if($i%5==0){echo '<br>';}			
			}
		}
	}
} 


if ( ! function_exists('blog_overall_rating'))
{
	function blog_overall_rating($blog_id)
	{
		$CI = CI();
		$res = $CI->db->query("SELECT AVG(rating) as rating FROM wl_comments WHERE matter_id ='".$blog_id."' AND status ='1' AND matter_type='blogs' ")->row();
		return round($res->rating,1);
	}
}


if ( ! function_exists('blog_overall_rating_star'))
{
	function blog_overall_rating_star($blog_id)
	{
		$CI = CI();
		$rate = blog_overall_rating($blog_id);
		if($rate>0)
		{
			$exp_rate = @explode('.',$rate);
			$rate1 = @$exp_rate[0];
			$rate2 = @$exp_rate[1];
			
			$emp_starts = 5 - $rate1;
			for($i=1;$i<=$rate1;$i++){
				echo '<i class="fas fa-star"></i>';
			}			
			if($rate2>=5)
			{
				echo '<i class="fas fa-star-half-alt"></i>';
			}
			else{
				echo '<i class="far fa-star"></i>';			
			}
				
			for($i=1;$i<$emp_starts;$i++)
			{
				echo '<i class="far fa-star"></i>';	
			}
		}
		else
		{
			for($i=1;$i<=5;$i++){
				echo '<i class="far fa-star"></i>';
			}
		}
	}
}

if ( ! function_exists('get_prim_artists'))
{
    function get_prim_artists($release_id)
    {
        $CI = CI();
        $prim_artists = $CI->db->select('primary_artist')->from('wl_primary_artists')->where('release_id',$release_id)->get()->result_array();
        return implode(", ", array_column($prim_artists, 'primary_artist'));
    }
}

if ( ! function_exists('get_release_featurings'))
{
    function get_release_featurings($release_id)
    {
        $CI = CI();
        $rel_featurings = $CI->db->select('featuring')->from('wl_release_featurings')->where('release_id',$release_id)->get()->result_array();
        return implode(", ", array_column($rel_featurings, 'featuring'));
    }
}

if ( ! function_exists('get_release_authors'))
{
    function get_release_authors($release_id)
    {
        $CI = CI();
        $rel_authors = $CI->db->select('author')->from('wl_authors')->where('release_id',$release_id)->get()->result_array();
        return implode(", ", array_column($rel_authors, 'author'));
    }
}

if ( ! function_exists('get_release_composers'))
{
    function get_release_composers($release_id)
    {
        $CI = CI();
        $rel_composers = $CI->db->select('composer')->from('wl_composers')->where('release_id',$release_id)->get()->result_array();
        return implode(", ", array_column($rel_composers, 'composer'));
    }
}

if ( ! function_exists('get_release_arrangers'))
{
    function get_release_arrangers($release_id)
    {
        $CI = CI();
        $rel_arrangers = $CI->db->select('arranger')->from('wl_arrangers')->where('release_id',$release_id)->get()->result_array();
        return implode(", ", array_column($rel_arrangers, 'arranger'));
    }
}

if ( ! function_exists('get_release_producers'))
{
    function get_release_producers($release_id)
    {
        $CI = CI();
        $rel_producers = $CI->db->select('producer')->from('wl_producers')->where('release_id',$release_id)->get()->result_array();
        return implode(", ", array_column($rel_producers, 'producer'));
    }
}

if ( ! function_exists('get_playlist_songs'))
{
    function get_playlist_songs($id)
    {
        $CI = CI();
        $rel_songs = $CI->db->select('release_title')->from('wl_playlist_songs')->where('playlist_id',$id)->get()->result_array();
        return implode(",<br> ", array_column($rel_songs, 'release_title'));
    }
}

if ( ! function_exists('get_support_status'))
{
	function get_support_status($id)
	{
		$status = 'Open';
		if($id == '1')
		{
			$status = 'Closed';
		}
		if($id == '2')
		{
			$status = 'Cancelled';
		}
		return $status;
	}	
}

if ( ! function_exists('easy_update'))
{
	
	function easy_update($table,$fields,$where_autoId)
	{
		 $ci = &get_instance();
		if($table!='' && is_array($fields) && is_array($where_autoId) )
		{ 
		   $ci->db->where($where_autoId);
		   $ci->db->update($table,$fields);
		  		
		}
	}
}

if ( ! function_exists('get_coupon_info'))
{
    function get_coupon_info($coupon_id)
	{
		if($coupon_id> 0)
		{
			 $ci = &get_instance();
			 $res=  $ci->db->get_where('wl_coupons',array('coupon_id'=>$coupon_id,'status!='=>'2'))->row_array();
			return $res;
		}
		
	}
}

if ( ! function_exists('get_refferal_coupon'))
{
    function get_refferal_coupon()
	{
		$ci = &get_instance();
		$res=  $ci->db->get_where('wl_coupons',array('is_refer'=>'1','status!='=>'2'))->row_array();
		return $res;
	}
}


if ( ! function_exists('get_discount_percent'))
{
	function get_discount_percent($price,$dis_price)
	{
		if( $price!='' && $dis_price!='' && $dis_price!='0.00')
		{      
			$percent =(($price-$dis_price)/$price)*100;	
			  $percent = ceil( $percent );  
			return $percent;
		}
	 
	}
}

if ( ! function_exists('getIndianCurrency'))
{
	function getIndianCurrency($number){
	    $number = (float) $number;
	    $decimal = round($number - ($no = floor($number)), 2) * 100;
	    $hundred = null;
	    $digits_length = strlen($no);
	    $i = 0;
	    $str = array();
	    $words = array(0 => '', 1 => 'One', 2 => 'Two',
	        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
	        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
	        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
	        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
	        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
	        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
	        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
	        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
	    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
	    while( $i < $digits_length ) {
	        $divider = ($i == 2) ? 10 : 100;
	        $number = floor($no % $divider);
	        $no = floor($no / $divider);
	        $i += $divider == 10 ? 1 : 2;
	        if ($number) {
	            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
	            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
	            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
	        } else $str[] = null;
	    }
	    $Rupees = implode('', array_reverse($str));
	    //$paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
	$paise = ($decimal > 0) ? "" . ($words[(int)($decimal/10)*10] . " " . $words[$decimal % 10]) . ' Paise' : '';
	    return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
	}
}

if(!function_exists('generate_sponsorId')){
    function generate_sponsorId(){
        $CI = CI();
        $prefix_sponsor = 'AVDM';
        $offset_number = 0;
        $year_str = date('y');
        $res_last_sponsor = $CI->db->select('sponsor_id')->order_by('customers_id','DESC')->get_where('wl_customers')->row_array();
        
        if(!empty($res_last_sponsor['sponsor_id'])){
            preg_match("~^$prefix_sponsor(\d+)\-(\d+)$~", $res_last_sponsor['sponsor_id'], $matches);    
            if($matches && $matches[1] == $year_str){
                $offset_number = $matches[2];
            }
            elseif(preg_match("~^(\d+)$~", $res_last_sponsor['sponsor_id'], $matches)){
                $offset_number = $matches[1];
            }
        }
        
        $ret_sponsorId = $prefix_sponsor . $year_str . "-" . pad_number($offset_number + 1, 3, 0);
        return $ret_sponsorId;
    }
}

if(!function_exists('members_direction')){
	function members_direction($mem_nature){
		switch ($mem_nature) {
			case '1':
			case '2':
				redirect(site_url('members'),'');
				break;
			
			default:
				redirect(site_url('admin'),'');
				break;
		}

	}
}

if (!function_exists('is_access_method'))
{
	function is_access_method($permission_type,$sec_id,$redict_top=false)
	{
		$CI = CI();
		
		$user_id=$CI->session->userdata('user_id');
		$parent_id=$CI->member_parent_id;
		if($parent_id>0)
		{
			$sql="SELECT a.section_title AS sec_heading ,a.section_title,a.section_icon, 
				  a.id,a.parent_id, a.section_controller,allowedsec.permission
				  FROM wl_customer_sections AS a,wl_customer_allowed_sections as allowedsec 
				  WHERE a.status='1' AND a.id=allowedsec.sec_id 
				  AND allowedsec.subadmin_id='".$user_id."' AND allowedsec.sec_id='".$sec_id."'
				  AND FIND_IN_SET($permission_type,allowedsec.permission)
				  ";
				  
			
			$qry=$CI->db->query($sql);
			if($qry->num_rows() > 0)
			{
				$res=$qry->result_array();
				//trace($res);exit;
				if(is_array($res) && count($res) > 0)
				{
					$is_access =  TRUE;
				}
				else
				{
					$is_access = FALSE;
				}
			}
			else
			{
				$is_access = FALSE;
			}			
		}
		else
		{
			$is_access = TRUE;
		}
		
		//echo $is_access;exit;
		
		if($is_access==0)
		{
			$CI->session->set_userdata(array('msg_type'=>'error'));
			$CI->session->set_flashdata('error',"Access to this section is restricted. Please contact your administrator if you believe this is an error.");
			if($redict_top==TRUE)
			{
				redirect_top('admin', 'true');
			}
			else
			{
				redirect('admin', 'true');
			}
		}
	}
}