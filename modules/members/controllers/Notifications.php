<?php
class Notifications extends Private_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('notification/notification_model');
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index(){
		$user_id = $this->userId;
		$page_heading = "Notifications";
		$this->mem_top_menu_section = 'notifications';
		$is_xhr = $this->input->is_ajax_request();
		$per_page_res = validate_per_page();
		$per_page = $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$from_date = $this->input->get_post('from_date',TRUE);
		$from_date = $this->db->escape_str( $from_date );
		$to_date = $this->input->get_post('to_date',TRUE);
		$to_date = $this->db->escape_str( $to_date );

		$where_notification="nc.customer_id='".$user_id."' AND wn.status='1' AND (wn.member_type='1' OR wn.member_type='0' )";

		if($from_date!=''){
			$where_notification.=" AND  ( DATE(nc.created_at) >='$from_date' ) ";
		}
		if($to_date!=''){
			$where_notification.=" AND  ( DATE(nc.created_at) <='$to_date' ) ";
		}

		$params_notification = array(	
								'fields'=>'wn.notification_title,wn.description,wn.notification_image,nc.created_at,nc.read_status,nc.id,wn.url_hint,wn.url_params,wn.notification_id',
								'offset'=>$db_offset,
								'limit'=>$per_page,
								'orderby'=>'nc.created_at DESC',
								'exjoin'=>array(
											array('tbl'=>'wl_notification_customer as nc','condition'=>'nc.notification_id=wn.notification_id')
										),
								'where'=>$where_notification,
								'debug'=>FALSE
								);

		$res_notification_array   = $this->notification_model->get_notification($params_notification);
		$total_record           =   $this->notification_model->total_rec_found;

		//trace($res_notification_array);die;

		$res_notification_data = array();

		if($total_record>0){
			$cur_dt = strtotime($this->config->item('config.date'));
			foreach($res_notification_array as $okey=>$oval){
				$loop_created_at = strtotime(date("Y-m-d",strtotime($oval['created_at'])));
				if($loop_created_at==$cur_dt){
					$loop_created_at_txt = date("h:i A",strtotime($oval['created_at']));
				}else{
					$loop_created_at_txt = date("M d Y h:i A",strtotime($oval['created_at']));
				}
				$has_url = !empty($oval['url_hint']) ? 1 : 0;
				$notification_url_params=array();
				if($has_url){
					$params_nf_url = array('url_hint'=>$oval['url_hint'],'url_params'=>$oval['url_params']);
					$notification_url_params = $this->custom_notification->format_notification_params($params_nf_url);
				}
				$res_notification_data[$okey] = array(
								'notification_title'=>$oval['notification_title'],
								'description'=>$oval['description'],
								'notification_image'=>$oval['notification_image'],
								'has_url'=>$has_url,
								'notification_url_params'=>array(
									'page'=> (!empty($notification_url_params['page']) ? $notification_url_params['page'] : ''),
									'id1'=>	(!empty($notification_url_params['id1']) ? $notification_url_params['id1'] : ''),
									'id2'=>	(!empty($notification_url_params['id2']) ? $notification_url_params['id2'] : ''),
								),
								'created_at_date'=>$loop_created_at_txt
							);
				if($oval['read_status']=='U'){
					//$this->notification_model->safe_update('wl_notification_customer',array('read_status'=>'R'),array('id'=>$oval['id']),FALSE);
				}
			}
			$this->notification_model->safe_update('wl_notification_customer',array('read_status'=>'R'),array('customer_id'=>$user_id),FALSE);
		}
		$base_link = site_url($this->uri->uri_string);
		$params_pagination = array(
		'base_link'=>$base_link,
		'per_page'=>$per_page,
		'total_recs'=>$total_record,
		'uri_segment'=>$offset,
		'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['base_link'] = $base_link;

		$data['total_records'] = $total_record;
		$data['res_notification_data']=$res_notification_data;
		$data['total_pages'] = ceil($total_record/$per_page);
		$data['offset'] = $offset;
		//$data['offset_rec'] = $offset_rec;
		$data['heading_title'] = $page_heading;
		if($is_xhr){
				$this->load->view('notifications/load_notification_data',$data);
		}else{
			$this->load->view('notifications/view_notification_list',$data);
		}
	}
	
}
/* End of file*/