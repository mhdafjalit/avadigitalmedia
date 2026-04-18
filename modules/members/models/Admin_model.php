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
	
	public function get_members_wallet($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'mwlt.receive_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_wallet as mwlt';
		
		if($param['fields']=='')
		{
			$param['fields'] = "mwlt.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_expenses_transactions($param=array())
	{
		/* Abstraction layer to model */
		$default_params = array(
							'orderby'=>'et.created_date DESC',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'debug'=>FALSE
						);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_transactions as et';
		
		if($param['fields']=='')
		{
			$param['fields'] = "et.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

}