<?php
if( ! function_exists('load_events')){
	function load_events($params=array()){
		$ci = CI();
		$ci->load->model(array('events/events_model'));
		$ajx_req = $ci->input->is_ajax_request();
		$per_page = $ci->config->item('per_page');
		$base_link = site_url($ci->uri->uri_string);
		$offset = (int) $ci->input->get_post('offset');
		if(!empty($params['category_id'])){
			$category_id = (int) $params['category_id'];	
		}else{
			$category_id = (int) $ci->input->get_post('category_id');
		}
		$keyword = $ci->input->get_post('keyword',TRUE);
		$keyword  = $ci->db->escape_str( $keyword );
		$from_date = $ci->input->get_post('from_date',TRUE);
		$from_date = $ci->db->escape_str( $from_date );
		$to_date = $ci->input->get_post('to_date',TRUE);
		$to_date = $ci->db->escape_str( $to_date );
		$default_sort_by = "evt_dt_desc";
		$sort_by = $ci->input->get_post('sort_by',TRUE);
		$sort_by = $ci->db->escape_str( $sort_by );
		$heading_title = "News & Events";
		$where_events="n.status='1'";

		if($category_id>0){
			$where_events.=" AND  n.category_id='".$category_id."' ";
		}
		
		if($keyword!=''){
			$where_events.=" AND  ( n.news_title like '%$keyword%' ) ";
		}

		if($from_date!=''){
			$where_events.=" AND  ( DATE(n.event_date1) >='$from_date' ) ";
		}
		if($to_date!=''){
			$where_events.=" AND  ( DATE(n.event_date1) <='$to_date' ) ";
		}

		switch($sort_by){
			case 'title_asc':
				$order_by = "n.news_title";
			break;
			case 'title_desc':
				$order_by = "n.news_title DESC";
			break;
			case 'evt_dt_asc':
				$order_by = "n.event_date1 ASC,n.news_id";
			break;
			case 'evt_dt_desc':
			default:
				$order_by = "n.news_id DESC";
		}

		$params_events = array(
						'fields'=>'n.*,nm.media',
						'offset'=>$offset,
						'limit'=>$per_page,
						'where'=>$where_events,
						'exjoin'=>array(
											array('tbl'=>'wl_events_media as nm','condition'=>"nm.news_id=n.news_id AND nm.media_type='photo'",'type'=>'LEFT')
										),
						'orderby'=>$order_by,
						'groupby'=>'n.news_id',
						'debug'=>FALSE
						);

		$res_events   = $ci->events_model->get_events($params_events);
		$total_events = $ci->events_model->total_rec_found;

		if($category_id>0){
			$category_res = log_fetched_rec($category_id,'category','category_name,friendly_url');
			if( !empty($category_res['rec_data'])){
				$heading_title = $category_res['rec_data']['category_name'];
			}
		}

		$ci->header_menu_section = 'events';
		
		$data['res_events'] = $res_events;
		$data['total_rec'] = $total_events;
		$data['base_link'] = $base_link;//Used for pagination handling
		$data['offset'] = $offset;
		$data['heading_title'] = $heading_title;
		$data['default_sort_by'] = $default_sort_by;
		$data['posted_from_date'] = $from_date;
		$data['posted_to_date'] = $to_date;
		$data['posted_sort_by'] = $sort_by=='' ? $default_sort_by : $sort_by;
		if($ajx_req===TRUE){
			$ci->load->partial_view('events/load_events',$data);
		}else{
		   $data['keyword'] = $keyword;
			$data['category_id'] = $category_id;
			$ci->load->view('events/view_events',$data);
		}
	}
}

if ( ! function_exists('count_events')){
	function count_events($condtion=''){
		$ci = CI();
		$condtion = "status !='2' ".$condtion;
		$sql="SELECT COUNT(*)  AS total_product FROM wl_events WHERE $condtion ";
		$query=$ci->db->query($sql)->row_array();
		return  (int) $query['total_product'];
	}
}