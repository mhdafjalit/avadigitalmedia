<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_model extends MY_Model
 {

	 public function get_members($param=array())
	 {
		//Abstraction layer to model
		$default_params = array(
								'orderby'=>'m.account_created_date DESC',
								'groupby'=>'',
								'where'=>'',
								'fields'=>'',
								'having'=>'',
								'exjoin'=>'',
								'debug'=>FALSE
							);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_customers as m';
		
		if($param['fields']=='')
		{
			$param['fields'] = "m.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_member_row($id,$condtion='')
	{
		$id = (int) $id;
		if($id!='' && is_numeric($id))
		{
			$condtion = "status !='2' AND customers_id=$id $condtion ";

			$fetch_config = array(
			  'condition'=>$condtion,
			  'debug'=>FALSE,
			  'return_type'=>"array"
			);

			$result = $this->find('wl_customers',$fetch_config);
			return $result;
		}
	}

	public function get_allocated_sections($param=array())
	{
		$default_params = array(
						'orderby'=>'tals.sec_id ASC',
						'groupby'=>'',
						'where'=>'',
						'fields'=>'',
						'having'=>'',
						'exjoin'=>'',
						'debug'=>FALSE
					);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_customer_allowed_sections as tals';
		
		if($param['fields']=='')
		{
			$param['fields'] = "tals.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}


	public function is_email_exits($data)
	{
		$this->db->select('customers_id');
		$this->db->from('wl_customers');
		$this->db->where($data);
		$this->db->where('status !=', '2')->where('status !=', '3');

		$query = $this->db->get();
		//echo_sql();
		if ($query->num_rows() == 1)
		{
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	public function get_supports($param=array())
	{
		//Abstraction layer to model
		$default_params = array(
						'orderby'=>'wsp.receive_date DESC',
						'groupby'=>'',
						'where'=>'',
						'fields'=>'',
						'having'=>'',
						'exjoin'=>'',
						'debug'=>FALSE
					);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_support as wsp';
		
		if($param['fields']=='')
		{
			$param['fields'] = "wsp.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}
	
	public function get_labels($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'wl.created_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);
		$param['from'] = 'wl_labels as wl';
		
		if($param['fields']=='')
		{
			$param['fields'] = "wl.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_metadata($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'md.created_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_metadata as md';
		
		if($param['fields']=='')
		{
			$param['fields'] = "md.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_download_permission($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'dp.created_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_download_permission as dp';
		
		if($param['fields']=='')
		{
			$param['fields'] = "dp.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_channel_request($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'req.created_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_youtube_requests as req';
		
		if($param['fields']=='')
		{
			$param['fields'] = "req.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_releases($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'wr.created_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);
		$param['from'] = 'wl_signed_albums as wr';
		
		if($param['fields']=='')
		{
			$param['fields'] = "wr.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_territories($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'wtc.id DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_territory_countries as wtc';
		
		if($param['fields']=='')
		{
			$param['fields'] = "wtc.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_playlits($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'wpt.created_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_playlists as wpt';
		
		if($param['fields']=='')
		{
			$param['fields'] = "wpt.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_monthly_releases_data($param = array())
	{
	    $month = $param['month'] ?? date("n");
	    $member_id = $param['member_id'] ?? 0;
	    $year = date("Y");
	    $this->db->select('DAY(created_date) AS day, COUNT(release_id) AS total_releases');
	    $this->db->from('wl_releases');
	    if($member_id>0){
	    	$this->db->where('member_id', $member_id);
	    }
	    $this->db->where('status', 1);
	    $this->db->where('MONTH(created_date)', $month);
	    $this->db->where('YEAR(created_date)', $year);
	    $this->db->group_by('DAY(created_date)');
	    $this->db->order_by('day');
	    $query = $this->db->get();
	    
	    $days = [];
	    $achieved_data = [];
	    $target_data = [];
	    for ($i = 1; $i <= 31; $i++) {
	        $days[] = 'Day ' . $i; 
	        $achieved_data[$i] = 0;
	        foreach ($query->result() as $row) {
	            if ($row->day == $i) {
	                $achieved_data[$i] = $row->total_releases;
	            }
	        }
	        $target_data[] = 100;
	    }
	    $achieved_data = array_values($achieved_data);
	    $result = [
	        'days' => $days,
	        'achieved_data' => $achieved_data,
	        'target_data' => $target_data,
	    ];
	    return $result;
	}
	
	
	public function getemail_by_id($id_array)
	{	
		$id_array=explode(',',$id_array);	
			
		if(is_array($id_array))
		{	 
			$emailarray='';			
			foreach($id_array as $value)
			{			
				$query = $this->db->query("SELECT * FROM wl_customers WHERE customers_id='$value'");				
				foreach ($query->result() as $row)
				{
					$emailarray = !empty($emailarray) ? $row->user_name.','.$emailarray : $row->user_name;				
				}				
			}
			return $emailarray;		
		}		
	}
	
	
	
	
	public function get_signed_albums($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'wm.created_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_signed_albums as wm';
		
		if($param['fields']=='')
		{
			$param['fields'] = "wm.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}
	

}