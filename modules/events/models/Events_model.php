<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Events_model extends MY_Model
{
	public function get_events($param=array())
	{
		//Abstraction layer to model
		$default_params = array(
								'orderby'=>'n.added_date	DESC',
								'groupby'=>'',
								'where'=>'',
								'fields'=>'',
								'having'=>'',
								'exjoin'=>'',
								'debug'=>FALSE
							);
		$param					= array_merge($default_params,$param);

		$param['from'] = 'wl_events as n';
		
		if($param['fields']=='')
		{
			$param['fields'] = "n.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

	public function get_media($param=array())
	{
		//Abstraction layer to model
		$default_params = array(
								'orderby'=>'nm.id',
								'groupby'=>'',
								'where'=>'',
								'fields'=>'',
								'having'=>'',
								'exjoin'=>'',
								'debug'=>FALSE
							);
		$param					= array_merge($default_params,$param);

		$param['from'] = 'wl_events_media as nm';
		
		if($param['fields']=='')
		{
			$param['fields'] = "nm.*";
		}
		$result = $this->custom_query_builder($param);	
		return $result;
	}

}?>