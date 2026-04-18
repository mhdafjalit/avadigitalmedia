<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Banner_lib
{
	public $szconf = array(	 'Top'=>"761x118",
							 'Home Middle'=>"692x428"							 
							 );
	
	public function __construct()
	{
		if (!isset($this->CI))
		{
			$this->CI =& get_instance();
		}
	}
	
	
	public function get_banner($pos=FALSE,$limit=1){
		
		if( $pos  && array_key_exists($pos,$this->szconf) ) {
			
			 $this->CI->db->select('*');
		     $this->CI->db->where('banner_position',$pos);
		     $this->CI->db->where('status',1);
			 $this->CI->db->order_by('banner_id', 'RANDOM');
			 $this->CI->db->limit($limit);
			 $query = $this->CI->db->get('tbl_banner');
			 //echo  $this->CI->db->last_query();
	     	if ($query->num_rows() >0) {	
			
	          $row = $query->result_array();
			 
			  return $row;
			 
			}
			
		 }	
	
	}
	
	
	
} // class banner lib

?>