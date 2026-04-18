<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
* The global dbquery CI helpers 
*/

if ( ! function_exists('get_db_multiple_row'))
{

	function get_db_multiple_row($tablename,$fld=FALSE,$Condwherw=FALSE)
	{
		$CI = CI();	
		$fld=($fld!='')?$fld:"*";	
		$Condwherw=($Condwherw!='')?$Condwherw:" 1 ";
		$selquery="SELECT $fld
		           FROM $tablename
				   WHERE $Condwherw";
		$query=$CI->db->query($selquery);
		if($query->num_rows() > 0){

		  	return $query->result_array();
		}
	}
} 

if ( ! function_exists('custom_result_set'))

{	
	function custom_result_set($sql)
	{	
		$ci = CI();
		$query=$ci->db->query($sql);

		if( $query->num_rows() > 0 )
		{			
			return $query->result_array();
		}	
	}
}



/*

*/



if ( ! function_exists('get_db_single_row'))

{

	function get_db_single_row($tablename,$fields="*",$condition="1")

	{

		$ci = CI();

		

		$cond = 'WHERE 1';

				

		if( is_array($condition) && !empty($condition) ) 
		{
			foreach($condition as $key=>$value)

			{
			  $cond .=" AND $key='".$value."'";  
			}
		}else
		{
			  $cond  .= $condition; 
		}

		$query=$ci->db->query("SELECT $fields FROM $tablename $cond");
		$row_founds = $query->num_rows();
		//echo $ci->db->last_query();		
		if($row_founds > 0 )
		{
			return $query->row_array();
		}
	}
}

if ( ! function_exists('count_record_with_join'))
{
    function count_record_with_join($table, $join = '', $where = '') {
        $CI =& get_instance();
        $sql = "SELECT COUNT(*) AS total FROM {$table} {$join} WHERE {$where}";
        $query = $CI->db->query($sql);
        $row = $query->row_array();
        return isset($row['total']) ? $row['total'] : 0;
    }
}

/*

*/

if (!function_exists('get_db_field_value')) {
    function get_db_field_value($tbl_name, $field, $condition) {
        $ci = &get_instance(); // Use get_instance() to access the CI instance
        
        // Initialize the query builder
        $ci->db->select($field)->from($tbl_name);
        
        // Check if $condition is an array
        if (is_array($condition) && count($condition) > 0) {
            // Use query binding to prevent SQL injection
            foreach ($condition as $key => $value) {
                $ci->db->where($key, $value);
            }
        } else if (is_string($condition) && !empty($condition)) {
            // If $condition is a string, append it safely using where()
            $ci->db->where($condition);
        }

        // Execute the query
        $query = $ci->db->get();

        // Check if any rows were returned
        if ($query->num_rows() > 0) {
            $res = $query->row();
            return $res->$field; // Return the requested field value
        }
        
        return null; // Return null if no rows were found
    }
}


/*

*/

if ( !function_exists('count_record') )
{	

	function count_record ($table,$condition="")
    {

		$ci = CI();	

		if($table!="" && $condition!="")
		{			

			  $ci->db->from($table);
			  $ci->db->where($condition);	        
			  $num = $ci->db->count_all_results();	
			  //$ci->query->last_query();

		 }else
		 {		 		

			 $num = $ci->db->count_all($table);	
		}

				

		return $num;	

    } 

}





/*







*/



if ( !function_exists('get_found_rows') )

{

	function get_found_rows()

	{

		$ci = CI();

		$query=$ci->db->query('SELECT FOUND_ROWS() AS total');

		$row=$query->row();

		return $row->total;

	}

}



/*







*/



if ( !function_exists('get_auto_increment') )

{

	function get_auto_increment($tablename)

	{

		$ci = CI();

		$query	=	$ci->db->query("SHOW TABLE STATUS LIKE '$tablename'");

		if($query->num_rows()==1)

		{

			$row=$query->row();

			

			return $inc=$row->Auto_increment;

		}

	}

}



if ( ! function_exists('get_expiry_date'))

{

	function get_expiry_date($no_of_days=FALSE,$rttype='DAY')

    {

	  $ci = CI();

      

     $currdate=$ci->config->item('config.date');

    $rs = $ci->db->query("SELECT DATE_ADD('".$currdate."', INTERVAL $no_of_days $rttype) as expdate ");

    

    $res = $rs->row_array();

    $expdate=$res['expdate'];

    return $expdate;

  }

}



/*









*/

if ( ! function_exists('echo_sql'))

{

	function echo_sql()

	{

		$ci = CI();

		echo"<font color='#ff0000' style='font-size:16px;font-family:verdana'><br />";

		echo wordwrap($ci->db->last_query(),60,"\n",TRUE);		

		echo"<br />\n </font>";

	}

}

/*Caching Logic for fetched records 
@Accepts : integer value
@Returns : corresponding cached object data if it has else new one
*/
if(!function_exists('log_fetched_rec_old'))
{
	function log_fetched_rec_old($log_input,$rec_type,$flds='')
	{
		$ci = &get_instance();
		$log_input = (int) $log_input;
		/*Bootstrap log configuration */
		switch($rec_type)
		{
			case 'country':
				$func_attr_db_tbl = 'wl_countries';
				$func_attr_db_flds = ($flds=='' ? 'id,country_name,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_country';
			break;
			case 'city':
				$func_attr_db_tbl = 'wl_cities';
				$func_attr_db_flds = ($flds=='' ? 'id,title,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_city';
			break;
			case 'state':
				$func_attr_db_tbl = 'wl_states';
				$func_attr_db_flds = ($flds=='' ? 'id,title,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_state';
			break;
			case 'locality':
				$func_attr_db_tbl = 'wl_localities';
				$func_attr_db_flds = ($flds=='' ? 'id,title,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_locality';
			break;
			case 'zipcode':
				$func_attr_db_tbl = 'wl_zipcode';
				$func_attr_db_flds = ($flds=='' ? 'id,title,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_zipcode';
			break;
			case 'category':
				$func_attr_db_tbl = 'wl_categories';
				$func_attr_db_flds = ($flds=='' ? 'category_id,category_name' : $flds);
				$func_attr_db_cond = array('category_id'=>$log_input);
				$use_log_var_property = 'obj_category';
			break;
			case 'company':
				$func_attr_db_tbl = 'wl_company';
				$func_attr_db_flds = ($flds=='' ? 'company_id,company_name' : $flds);
				$func_attr_db_cond = array('company_id'=>$log_input);
				$use_log_var_property = 'obj_company';
			break;
			case 'customer':
				$func_attr_db_tbl = 'wl_customers';
				$func_attr_db_flds = ($flds=='' ? 'customers_id,first_name' : $flds);
				$func_attr_db_cond = array('customers_id'=>$log_input);
				$use_log_var_property = 'obj_v_customer';
			break;
			case 'mock_test':
				$func_attr_db_tbl = 'wl_mock_test';
				$func_attr_db_flds = ($flds=='' ? 'mt_id,mt_title' : $flds);
				$func_attr_db_cond = array('mt_id'=>$log_input);
				$use_log_var_property = 'obj_mock_test_cache';
			break;
			case 'folder':
				$func_attr_db_tbl = 'wl_subject_folders';
				$func_attr_db_flds = ($flds=='' ? 'folder_id,folder_name' : $flds);
				$func_attr_db_cond = array('folder_id'=>$log_input);
				$use_log_var_property = 'obj_folder';
			break;
			case 'video_course':
				$func_attr_db_tbl = 'wl_video_courses';
				$func_attr_db_flds = ($flds=='' ? 'vc_id,vc_title' : $flds);
				$func_attr_db_cond = array('vc_id'=>$log_input);
				$use_log_var_property = 'obj_video_course';
			break;
			case 'notes':
				$func_attr_db_tbl = 'wl_notes';
				$func_attr_db_flds = ($flds=='' ? 'notes_id,notes_title' : $flds);
				$func_attr_db_cond = array('notes_id'=>$log_input);
				$use_log_var_property = 'obj_pkg_notes';
			break;
			case 'category_subscription':
				$func_attr_db_tbl = 'wl_subscription_packages';
				$func_attr_db_flds = ($flds=='' ? 'subscription_id' : $flds);
				$func_attr_db_cond = array('subscription_id'=>$log_input);
				$use_log_var_property = 'obj_cat_subscription';
			break;
			case 'mtp':
				$func_attr_db_tbl = 'wl_mock_test_packages';
				$func_attr_db_flds = ($flds=='' ? 'mtp_id,mtp_title' : $flds);
				$func_attr_db_cond = array('mtp_id'=>$log_input);
				$use_log_var_property = 'obj_mtp';
			break;
			case 'default':
				echo "cannot Log unknown type";
				exit;
			break;
		}
		if(!property_exists($ci,$use_log_var_property))
		{
			$ci->$use_log_var_property = array();
		}

		$ref = &$ci->$use_log_var_property;

		if(!isset($ref[$log_input]))
		{
				if($log_input == 0)
				{
					$ref[$log_input] = array('is_cached'=>0,'rec_data'=>array());
				}
				else
				{
					$log_res = $ci->db->select($func_attr_db_flds)->get_where($func_attr_db_tbl,$func_attr_db_cond)->row_array();

					$log_res = is_array($log_res) && !empty($log_res) ? $log_res : array();
		
					$ref[$log_input] = array('is_cached'=>0,'rec_data'=>$log_res);
				}
		}else{
			$ref[$log_input]['is_cached'] = 1;
		}

		return $ref[$log_input];
	}
}

/* New Version with extra fields data fetch from table */
if(!function_exists('log_fetched_rec'))
{
	function log_fetched_rec($log_input,$rec_type,$flds='')
	{
		$ci = &get_instance();
		$log_input = (int) $log_input;
		/*Bootstrap log configuration */
		switch($rec_type)
		{
			case 'country':
				$func_attr_db_tbl = 'wl_countries';
				$func_attr_db_flds = ($flds=='' ? 'id,country_name,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_country';
			break;
			case 'city':
				$func_attr_db_tbl = 'wl_cities';
				$func_attr_db_flds = ($flds=='' ? 'id,title,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_city';
			break;
			case 'state':
				$func_attr_db_tbl = 'wl_states';
				$func_attr_db_flds = ($flds=='' ? 'id,title,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_state';
			break;
			case 'locality':
				$func_attr_db_tbl = 'wl_localities';
				$func_attr_db_flds = ($flds=='' ? 'id,title,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_locality';
			break;
			case 'zipcode':
				$func_attr_db_tbl = 'wl_zipcode';
				$func_attr_db_flds = ($flds=='' ? 'id,title,status' : $flds);
				$func_attr_db_cond = array('id'=>$log_input);
				$use_log_var_property = 'obj_location_zipcode';
			break;
			case 'category':
				$func_attr_db_tbl = 'wl_categories';
				$func_attr_db_flds = ($flds=='' ? 'category_id,category_name' : $flds);
				$func_attr_db_cond = array('category_id'=>$log_input);
				$use_log_var_property = 'obj_category';
			break;
			case 'gallerycategory':
				$func_attr_db_tbl = 'wl_gallery_categories';
				$func_attr_db_flds = ($flds=='' ? 'category_id,category_name' : $flds);
				$func_attr_db_cond = array('category_id'=>$log_input);
				$use_log_var_property = 'obj_category';
			break;
			case 'company':
				$func_attr_db_tbl = 'wl_company';
				$func_attr_db_flds = ($flds=='' ? 'company_id,company_name' : $flds);
				$func_attr_db_cond = array('company_id'=>$log_input);
				$use_log_var_property = 'obj_company';
			break;
			case 'customer':
				$func_attr_db_tbl = 'wl_customers';
				$func_attr_db_flds = ($flds=='' ? 'customers_id,first_name' : $flds);
				$func_attr_db_cond = array('customers_id'=>$log_input);
				$use_log_var_property = 'obj_v_customer';
			break;
			case 'mock_test':
				$func_attr_db_tbl = 'wl_mock_test';
				$func_attr_db_flds = ($flds=='' ? 'mt_id,mt_title' : $flds);
				$func_attr_db_cond = array('mt_id'=>$log_input);
				$use_log_var_property = 'obj_mock_test_cache';
			break;
			case 'folder':
				$func_attr_db_tbl = 'wl_subject_folders';
				$func_attr_db_flds = ($flds=='' ? 'folder_id,folder_name' : $flds);
				$func_attr_db_cond = array('folder_id'=>$log_input);
				$use_log_var_property = 'obj_folder';
			break;
			case 'video_course':
				$func_attr_db_tbl = 'wl_video_courses';
				$func_attr_db_flds = ($flds=='' ? 'vc_id,vc_title' : $flds);
				$func_attr_db_cond = array('vc_id'=>$log_input);
				$use_log_var_property = 'obj_video_course';
			break;
			case 'notes':
				$func_attr_db_tbl = 'wl_notes';
				$func_attr_db_flds = ($flds=='' ? 'notes_id,notes_title' : $flds);
				$func_attr_db_cond = array('notes_id'=>$log_input);
				$use_log_var_property = 'obj_pkg_notes';
			break;
			case 'category_subscription':
				$func_attr_db_tbl = 'wl_subscription_packages';
				$func_attr_db_flds = ($flds=='' ? 'subscription_id' : $flds);
				$func_attr_db_cond = array('subscription_id'=>$log_input);
				$use_log_var_property = 'obj_cat_subscription';
			break;
			case 'mtp':
				$func_attr_db_tbl = 'wl_mock_test_packages';
				$func_attr_db_flds = ($flds=='' ? 'mtp_id,mtp_title' : $flds);
				$func_attr_db_cond = array('mtp_id'=>$log_input);
				$use_log_var_property = 'obj_mtp';
			break;
			case 'default':
				echo "cannot Log unknown type";
				exit;
			break;
		}
		if(!property_exists($ci,$use_log_var_property))
		{
			$ci->$use_log_var_property = array();
		}

		$ref = &$ci->$use_log_var_property;

		$should_call_db=0;

		if(!isset($ref[$log_input]))
		{
				if($log_input == 0)
				{
					$ref[$log_input] = array('is_cached'=>0,'rec_data'=>array());
					$should_call_db=0;
				}
				else
				{
					$should_call_db=1;
				}
		}else{
			if($log_input>0){
				$func_attr_db_flds = trim($func_attr_db_flds);
				if(empty($ref[$log_input]['rec_data']) || $ref[$log_input]['selection_type'] =='*'){
					$should_call_db=0;
				}else{
					if($func_attr_db_flds=='*'){
						$should_call_db=1;
					}else{	
						$req_flds_arr = explode(',',$func_attr_db_flds);
						$req_flds_arr = array_map('trim',$req_flds_arr);
						$cached_flds_arr = array_keys($ref[$log_input]['rec_data']);
						$diff_keys = array_diff($req_flds_arr,$cached_flds_arr);
						if(!empty($diff_keys)){
							$should_call_db=1;
							$req_flds_arr = array_merge($cached_flds_arr,$diff_keys);
							$func_attr_db_flds = implode(',',$req_flds_arr);

						}
					}
				}
				if(!$should_call_db){
					$ref[$log_input]['is_cached'] = 1;
				}
			}
			
		}
		if($should_call_db){
			$selection_type = trim($func_attr_db_flds)=='*' ? '*' : 'custom';
			$log_res = $ci->db->select($func_attr_db_flds)->get_where($func_attr_db_tbl,$func_attr_db_cond)->row_array();

			$log_res = is_array($log_res) && !empty($log_res) ? $log_res : array();

			$ref[$log_input] = array('is_cached'=>0,'rec_data'=>$log_res,'selection_type'=>$selection_type);

		}

		return $ref[$log_input];
	}
}