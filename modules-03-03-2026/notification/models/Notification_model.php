<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Notification_model extends MY_Model
{
	public function get_notification($param=array())
	{
			$default_params = array(
								'orderby'=>'',
								'groupby'=>'',
								'where'=>'',
								'fields'=>'',
								'having'=>'',
								'exjoin'=>'',
								'debug'=>FALSE
							);
			$param					= array_merge($default_params,$param);

			$orderby			=	$param['orderby'];
			$groupby			=	$param['groupby'];
			$where			    =	$param['where'];
			$fields			    = $param['fields'];
			$having			    =	$param['having'];
			$exjoin			    =	$param['exjoin'];
			$debug			    =	$param['debug'];

			$this->db->where('wn.status !=','2');

			if($where!='')
			{
				$this->db->where($where);

			}

			if($orderby!='')
			{
				$this->db->order_by($orderby,'',false);

			}
			else
			{
				$this->db->order_by('wn.notification_id DESC');
			}

			if($groupby!='')
			{
				$this->db->group_by($groupby);

			}

			if($fields == '')
			{
				$fields = "SQL_CALC_FOUND_ROWS wn.*";
			}
			else
			{
				$fields = str_replace('SQL_CALC_FOUND_ROWS','',$fields);
				$fields = "SQL_CALC_FOUND_ROWS ".$fields;
			}

			if(array_key_exists('limit',$param) && $param['limit'] > 0)
			{
				$limit = $param['limit'];
				if(array_key_exists('offset',$param) && applyFilter('NUMERIC_WT_ZERO',$param['offset'])!=-1)
				{
					$offset = $param['offset'];
				}
				else
				{
					$offset = 0;
				}
				$this->db->limit($limit,$offset);
			}

			$this->db->select($fields,FALSE);
			$this->db->from('wl_notification as wn');
			if(is_array($exjoin) && !empty($exjoin))
			{
				foreach($exjoin as $val)
				{
					$val['type'] = (!array_key_exists('type',$val) || $val['type']=='') ? 'JOIN' : $val['type'];
					$this->db->join($val['tbl'],$val['condition'],$val['type']);
				}
			}
			$q=$this->db->get();
			if($debug === TRUE)
			{
				echo_sql();
			}
			if(!empty($param['fetch_type'])){
				$fetch_type = $param['fetch_type'];
				$result = $q->$fetch_type();
			}else{
				$result = $q->result_array();
			}
			$this->total_rec_found = get_found_rows();
			return $result;
	}

}?>