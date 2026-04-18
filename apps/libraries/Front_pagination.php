<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
#[AllowDynamicProperties]
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Pagination
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/pagination.html
 */
class Front_pagination{  

	var $base_url			= ''; // The page we are linking to
	var $total_rows  		= ''; // Total number of items (database results)
	var $per_page	 		= 10; // Max number of items you want shown per page
	var $num_links			=  2; // Number of "digit" links to show before/after the currently viewed page
	var $cur_page	 		=  0; // The current page being viewed
	var $first_link   		= '&lsaquo; First';
	var $next_link			= 'Next';
	var $prev_link			= 'Previous';
	var $last_link			= 'Last &rsaquo;';
	var $uri_segment		= 4;
	var $full_tag_open		= ' <nav aria-label="Page navigation example">
          <ul class="pagination justify-content-end">';  
	var $full_tag_close		= '</ul></nav>';
	var $first_tag_open		= '';
	var $first_tag_close	= '&nbsp;';
	var $last_tag_open		= '&nbsp;';
	var $last_tag_close		= '';
	var $cur_tag_open		= '<li class="page-item active"><a href="javascript:void(0);" class="page-link" data-link-num={link_num}>';
	var $cur_tag_close		= '</a></li>';
	var $next_tag_open		= '<li class="page-item">';
	var $next_tag_close		= '</li>';
	var $prev_tag_open		= '<li class="page-item">';
	var $prev_tag_close		= '</li>';
	var $num_tag_open		= '<li class="page-item">';
	var $num_tag_close		= '</li>';

	
	// Added By Tohin
	var $js_rebind 			= '';
	var $table              = '';
	var $div                = '';
	var $postVar            = '';
    var $additional_param	= '';

	var $refresh	= FALSE;

	var $data_form = "";

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	function CI_Pagination($params = array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);		
		}
		
		log_message('debug', "Pagination Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */	
	function create_links()
	{
		
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
		   return '';
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
            $info = 'Showing : ' . $this->total_rows;
			$info = "";
			return $info;
		}

		// Determine the current page number.		
		$CI =& get_instance();	
		if ($this->uri_segment != 0)
		{
			$this->cur_page = $this->uri_segment;
			
			// Prep the current page - no funny business!
			$this->cur_page = (int) $this->cur_page;
		}

		$this->num_links = (int)$this->num_links;
		
		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}
				
		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = 1;
		}
		
		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->total_rows)
		{
			//$this->cur_page = ($num_pages - 1) * $this->per_page;
			$this->cur_page = $this->total_rows;
		}
		
		$uri_page_number = $this->cur_page;
		//$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;
		// Add a trailing slash to the base URL if needed
		$this->base_url = rtrim($this->base_url, '/') .'/';

  		// And here we go...
		$output = '';

        // SHOWING LINKS   <td align="right"><p class="paging"></p></td><td style="width:200px;"><p class="tahoma b ft-11 black">Showing 1 to 2 of 6</p></td>

        $curr_offset = $this->uri_segment;
		
         $total_stats = '<div class="fr mt5"><strong>Total Records : </strong>' . ( $curr_offset + 1 ) . ' to ' ;
	     //$info="";
				 $curr_page_total_count= $curr_offset + $this->per_page;
				 if($curr_page_total_count>$this->total_rows){
					 $total_stats .= $this->total_rows;
				 }else{
					 $total_stats .= $curr_page_total_count;
				 }

        $total_stats .= ' of ' . $this->total_rows . '</div>';

		$total_stats = "";
		
        //$info="";
        //$output .= $info;


		// Render the "First" link
		if  ($this->cur_page > $this->num_links)
		{
			//$output .= $this->first_tag_open 
					//. $this->getAJAXlink( '' , $this->first_link)
					//. $this->first_tag_close; 
		}

		// Render the "previous" link
		if  ($this->cur_page != 1)
		{
			$this->data_link_num = "Previous";
			$prev_link_class = "page-link";	
			//$i = $uri_page_number - $this->per_page;
			$i=$this->cur_page -1;
			if ($i == 0) $i = '';
			$output .= $this->prev_tag_open 
					. $this->getAJAXlink( $i, $this->prev_link,$prev_link_class )
					. $this->prev_tag_close;
					
		}

		// Write the digit links
		//	$output .= '<a href="#" class="select">';
		for ($loop = $start -1; $loop <= $end; $loop++)
		{
			//$i = ($loop * $this->per_page) - $this->per_page;
			$i = $loop;
					
			if ($i > 0)
			{
				$this->data_link_num=$loop;
				if ($this->cur_page == $loop)
				{
					$this->cur_tag_open = preg_replace("~\{link_num\}~",$loop,$this->cur_tag_open);
					$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
				}
				else
				{
					$n = ($i == 0) ? '' : $i;
					$num_link_class = "page-link";	
					$output .= $this->num_tag_open
						. $this->getAJAXlink( $n, $loop,$num_link_class)
						. $this->num_tag_close;
					
				}
				
			}
		}
		//$output .= '</a>';

		// Render the "next" link
		if ($this->cur_page < $num_pages)
		{
			$this->data_link_num="Next";
			$next_link_class = "page-link";	
			$output .= $this->next_tag_open 
				. $this->getAJAXlink( $this->cur_page +1 , $this->next_link,$next_link_class)
				. $this->next_tag_close;
			
			
		}
        
		
		// Render the "Last" link
		if (($this->cur_page + $this->num_links) < $num_pages)
		{
			//$i = (($num_pages * $this->per_page) - $this->per_page);
			//$output .= $this->last_tag_open . $this->getAJAXlink( $i, $this->last_link ) . $this->last_tag_close;
		}

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;
	    //$output = '<td width="32">'.$output.'</td>';
		
		return $output.$total_stats;		
	}

	function getAJAXlink( $count, $text,$cls="") {

        if( $this->table == '' && $this->div == '')
            //return '<a href="'. $this->base_url . $count . '">'. $text .'</a>';
			return '<a href="'. $this->base_url . '">'. $text.'</a>';
			
		if($this->div!="") $this->table = $this->div;
            
        if( $this->additional_param == '' )
        	$this->additional_param = "{'t' : 't',pg:".$count."}";

        $final_href = site_url(uri_query_string(array('offset'=>$count)));


		return "<a href=\"".$final_href."\"  class=\"cpg_link $cls\" data-offset=\"".$count."\" data-parent=\"".$this->table."\" data-form=\"".$this->data_form."\" data-refresh=\"".$this->refresh."\" data-link-num=\"".$this->data_link_num."\">". $text ."</a>";

		/*return "<a href=\"javascript:void(0);\"  class=\"$cls\"  onclick=\"$('".$this->table."').fadeTo(100,0.3,function(){
																													  
  																																					 $.post('". $this->base_url  ."', ". $this->additional_param .", function(data){
	$('".$this->table."').fadeTo(100,1,function(){var currdata = $( data ).find('".$this->table."').html(); $('".$this->table. "').html(currdata);".$this->js_rebind .";});		
	 });}); return false;\">". $text .'</a>';*/
									
				
	}
	
}
// END Pagination Class
?>