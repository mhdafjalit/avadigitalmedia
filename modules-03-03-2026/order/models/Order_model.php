<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_model extends MY_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_orders_old($param=array())
	{
		//Abstraction layer to model
		$default_params = array(
						'orderby'=>'o.order_received_date DESC',
						'groupby'=>'',
						'where'=>'',
						'fields'=>'',
						'having'=>'',
						'exjoin'=>'',
						'debug'=>FALSE
					);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_orders as o';
		
		if($param['fields']=='')
		{
			$param['fields'] = "o.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_orders($param=array())
	{
		//Abstraction layer to model
		$default_params = array(
						'orderby'=>'po.created_date DESC',
						'groupby'=>'',
						'where'=>'',
						'fields'=>'',
						'having'=>'',
						'exjoin'=>'',
						'debug'=>FALSE
					);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_project_orders as po';
		
		if($param['fields']=='')
		{
			$param['fields'] = "po.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_purchase_requests($param=array())
	{
		//Abstraction layer to model
		$default_params = array(
						'orderby'=>'pr.created_date DESC',
						'groupby'=>'',
						'where'=>'',
						'fields'=>'',
						'having'=>'',
						'exjoin'=>'',
						'debug'=>FALSE
					);
		$param	= array_merge($default_params,$param);

		$param['from'] = 'wl_materials_purchase_requests as pr';
		
		if($param['fields']=='')
		{
			$param['fields'] = "pr.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}
	
}
// model end here