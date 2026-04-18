<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');



class MY_Model  extends CI_Model

{



	public $total_rec_found;

	public function __construct()

	{

		parent::__construct();

	}



	public function safe_insert($table,$data=array(),$debug=FALSE)

	{

		if($table!="" && is_array($data) && !empty($data) )

		{	 

			$qstr = $this->db->insert_string($table,$data);	

			$this->db->query($qstr);

			if ( $debug )

			{ 

				echo  $this->db->last_query(); 

				

			}

			return $this->db->insert_id();

		}

	} 



	public function safe_update($table,$data,$where,$debug=FALSE)

	{	 

		if($table!="" && is_array($data) && !empty($data) && $where!="" )

		{

			$qstr = $this->db->update_string($table, $data, $where);

			$this->db->query($qstr);

			if ( $debug )

			{ 

				echo  $this->db->last_query(); 

				

			}

		}

	}



	public function safe_delete($table,$data=array(),$debug=FALSE)

	{

		if($table!="" && is_array($data) && !empty($data) )

		{

			$this->db->delete($table, $data );

			

			if ( $debug )

			{ 

				echo  $this->db->last_query(); 				

			}

		}

	}





	public function delete_in($table,$where,$debug=FALSE)

	{

		if($table!="" && !empty($where) )

		{	

			

			$this->db->query("DELETE FROM $table WHERE $where " );

			if ( $debug )

			{ 

				echo  $this->db->last_query();

				

			}

		}

	}

	

	

	/**

	* Returns a resultset array with specified fields from database matching given conditions.

	* @author md emran hasan <dkmaurya@gmail.com>

	* @return query result either in array or in object based on model config

	* @access public

	*/	



	public function findAll($tbl,$exparams=array())

	{

		$fields      = !array_key_exists('field',$exparams)       ? "*"         : $exparams['field'];

		$conditions  = !array_key_exists('condition',$exparams)   ?  'NULL'     : $exparams['condition'];	 

		$order       = !array_key_exists('order',$exparams)       ?  'NULL'     : $exparams['order'];

		$start       = !array_key_exists('start',$exparams)       ?  '0'        : $exparams['start'];

		$limit       = !array_key_exists('limit',$exparams)       ?  'NULL'     : $exparams['limit'];

		$debug       = !array_key_exists('debug',$exparams)       ?  FALSE      : $exparams['debug'];	

		$return_type = !array_key_exists('return_type',$exparams) ? "array"     : $exparams['return_type'];

		

		if ($conditions != 'NULL') 

		{ 

			$this->db->where($conditions);

		}

	

		if ($fields != 'NULL') 

		{ 

			$this->db->select($fields);

		}

		

	

		if ($order != 'NULL') 

		{ 

			$this->db->order_by($order);

		}

	

		if ($limit != 'NULL') 

		{ 

			$this->db->limit($limit,$start);

		}

	

		$query = $this->db->get($tbl);

		if ( $debug )

		{

			echo  $this->db->last_query();

			//exit();

		}

		

		$numRows=$query->num_rows();

	

		$this->total_rec_found=$this->findCount($tbl,$conditions);		

	

		if(array_key_exists('return_type',$exparams) && trim(strtolower($exparams['return_type']))=='num')

		{

			return $numRows;

		}else

		{			

			if( $numRows > 0 )

			{

				if($return_type!='object' && array_key_exists('index',$exparams) && $exparams['index']!='')

				{

					$result=array();

					foreach($query->result_array() as $row)

					{

						$indexval=$row[$exparams['index']];

						$result[$indexval]=$row;

					}

					

					return $result;

					

				}else

				{

					return ($return_type=='object')  ? $query->result()  :  $query->result_array() ;

				}

			}

		}

	}





	/**

	* Return a single row as a resultset array with specified fields from 

	database matching given conditions.

	**@author md emran hasan <dkmaurya@gmail.com>

	* @return single row either in array or in object based on model config

	* @access public

	*/	





	public function findCount($tbl,$conditions)

	{	  

		$this->db->select("COUNT(*) AS count");

		//$this->db->limit(1,0);

		if ($conditions != 'NULL') 

		{ 

			$this->db->where($conditions);

		}

	

		$count = $this->db->count_all_results($tbl);

		return $count;

	}



	public function find($tbl,$exparams=array())

	{

		$exparams['start'] = '0';

		$exparams['limit'] = '1';

		$data = $this->findAll($tbl, $exparams );

		if ( is_array($data) || is_object($data) ) 

		{

			return $data[0];

			

		}else

		{

			if( is_numeric($data) )

			{

				return $data;

			}

		}

	}



	public function is_record_exits( $tbl, $exparams=array() )

	{ 

		$exparams['start'] = '0';

		$exparams['limit'] = '1';

		$exparams['return_type'] = 'num';				  

		$num = $this->findAll($tbl, $exparams );	

		return ( $num > 0 ) ? TRUE : FALSE;

	}



	

	

	public	function fetchMultipleRecords($qry,$opt=array())

	{

		$qry = $this->db->query($qry);

		if ( $qry->num_rows() >0 )

		{

			$dataRecordsMultiple=array();

			foreach($qry->result_array() as $rec_res )

			{

				if(array_key_exists('index',$opt))

				{

					$indexval=$rec_res[$opt['index']];

					$dataRecordsMultiple[$indexval]=$rec_res;

				}

				if( array_key_exists('index',$opt) && array_key_exists('val',$opt) )

				{

					$indexval=$rec_res[$opt['index']];

					$rVal=$rec_res[$opt['val']];

					$dataRecordsMultiple[$indexval]=$rVal;

				}else

				{

					array_push($dataRecordsMultiple,$rec_res);

				}

			}

			return $dataRecordsMultiple; 

		}

	}

	

	public function myCustomJoin($opts)

	{

		$this->db->start_cache();

		$this->db->select($opts['selectcond'],FALSE);

		$this->db->from($opts['fromcond']);

		if(array_key_exists('joins',$opts) && !empty($opts['joins']))

		{

			foreach($opts['joins'] as $key=>$val)

			{

				$join_type = array_key_exists('join_type',$opts['joins'][$key]) ? $val['join_type'] : 'inner';

				$this->db->join($val['tblname'], $val['jclause'],$join_type);

			}

		}

		if(array_key_exists('condition',$opts) && $opts['condition']!='')

		{

			$this->db->where($opts['condition']);

			

		}

		$this->db->stop_cache();

		$this->total_rec_found= $this->db->count_all_results();

		if(array_key_exists('limit',$opts) && $opts['limit']!='')

		{

			$this->db->limit($opts['limit'],$opts['offset']);

		}

		

		if(array_key_exists('groupby',$opts) && $opts['groupby']!='')

		{

			

			$this->db->group_by($opts['groupby']); 		

		}		

		

		if(array_key_exists('orderby',$opts) && $opts['orderby']!='')

		{

			

			$this->db->order_by($opts['orderby']);			

		}		

		

		$result = $this->db->get()->result_array();

		if(array_key_exists('debug',$opts) && $opts['debug']===TRUE)

		{

				

			echo  $this->db->last_query();

		}

		$this->db->flush_cache();

		return $this->total_rec_found > 0 ? $result : "";

	}
	/*** @Bind total records count to total_rec_found property
	*** @Returns null if total_rec_found  is zero
	**/
	public function custom_query_builder($param=array())
	{
		$default_params = array(
							'from'=>'',
							'orderby'=>'',
							'groupby'=>'',
							'where'=>'',
							'fields'=>'',
							'having'=>'',
							'exjoin'=>'',
							'return_type'=>'result_array',
							'debug'=>FALSE);
		$param				= array_merge($default_params,$param);
		$from			    =	 $param['from'];
		$orderby			=	 $param['orderby'];
		$groupby			=	 $param['groupby'];
		$where			   =	 $param['where'];
		$fields			   =  $param['fields'];
		$having			   =	 $param['having'];
		$exjoin			   =  $param['exjoin'];
		$debug			   =  $param['debug'];
		$this->db->start_cache();
		if($from !=''){
			$this->db->from($from);
		}else{
			die("Required from parameter missing");
		}
		if($where!=''){
			$this->db->where($where,'',false);
		}
		if($groupby!=''){
			$this->db->group_by($groupby);
		}
		if($having!='') {		
			$this->db->having($having);
		}
		$this->db->select($fields,FALSE);
		if(is_array($exjoin) && !empty($exjoin)){
			foreach($exjoin as $val){
				$val['type'] = (!isset($val['type']) || $val['type']=='') ? 'JOIN' : $val['type'];
				$this->db->join($val['tbl'],$val['condition'],$val['type']);
			}
		}
		$this->db->stop_cache();
		$num_rows_required=0;
		//Whether you dont want total_rec_found (eg:row_array,row).just to skip count query as we are not dealing with total_rec_found.By default it will always run count query
		if(!isset($param['num_rows_required']) || $param['num_rows_required']){
			$num_rows_required=1;
			$this->total_rec_found = $this->db->count_all_results();
			if($debug === TRUE){
				echo  "<br>Count Query:".  $this->db->last_query();
			}
		}
		//@Note: Fetch record only if found and query is not of count type
		//Saves another query execution when there is no record

		if(($this->total_rec_found > 0 || !$num_rows_required) && (isset($param['return_type']) && $param['return_type'] !='count')){
			if(isset($param['limit']) && $param['limit'] > 0){
					$limit = $param['limit'];
					if(isset($param['offset']) && applyFilter('NUMERIC_WT_ZERO',$param['offset'])!=-1){
						$offset = $param['offset'];
					}else{
						$offset = 0;
					}
					$this->db->limit($limit,$offset);
				}
				if($orderby!=''){
					$this->db->order_by($orderby);
				}
				$q=$this->db->get();
				if($debug === TRUE){
					echo  "<br>Result Query: ".$this->db->last_query();
				}
				if(!empty($param['return_type'])){
					$call_result_func = $param['return_type'];
					$result = $q->$call_result_func();
				}else{
					$result=$q->result_array();
				}
			}else{
				$result =null;
			}
			$this->db->flush_cache();
			return $result;

	}
	public function findCountV1($tbl,$conditions){
		$this->db->select("COUNT(*) AS count");
		//$this->db->limit(1,0);
		if ($conditions != 'NULL'){
			$this->db->where($conditions);
		}
		$count = $this->db->count_all_results($tbl);
		return $count;
	}

	

	

}