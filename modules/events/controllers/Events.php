<?php
class Events extends Public_Controller
{

	public function __construct() {
		parent::__construct();
		$this->load->library(array('Dmailer'));
		$this->load->model(array('events/events_model'));
		$this->load->helper(array('events/events'));
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index() {
		load_events();
	}

	public function category(){
		$ajx_req = $this->input->is_ajax_request();
		$parent_id = !empty($this->meta_info['entity_id']) ? $this->meta_info['entity_id'] : 0;
		$load_products = FALSE;
		if($parent_id > 0){
			$subcat_res = $this->db->query("SELECT count(category_id) as gtotal FROM wl_categories WHERE parent_id='".$parent_id."' AND status='1' AND cat_type='2'")->row();

			$total_subcat = (int) $subcat_res->gtotal;

			if(!$total_subcat){
				$load_products = TRUE;
			}
		}
		if($load_products === FALSE){
			$this->header_menu_section = 'events';
			$heading_title = "Events Category";
			if($parent_id>0){
				$parentdata = get_db_single_row('wl_categories','*'," AND category_id='$parent_id' AND status='1' AND cat_type='2'");
				if(is_array($parentdata) && !empty($parentdata)) {
					$heading_title = $parentdata['category_name'];
				}
			}
			$per_page = $this->config->item('per_page');
			$base_link = site_url($this->uri->uri_string);
			$offset = (int) $this->input->get_post('offset');
			$where_cat = "cat.status='1' AND cat.cat_type='2' AND parent_id='".$parent_id."'";
			$params_cat = array(
							'fields'=>"cat.*,( SELECT COUNT(category_id) FROM wl_categories AS cat1
							WHERE cat1.parent_id=cat.category_id AND cat1.status='1' ) AS total_subcategories",
							'offset'=>$offset,
							'limit'=>$per_page,
							'where'=>$where_cat,
							'debug'=>FALSE
							);
			$res_cat   = $this->category_model->get_category_front($params_cat);
			$total_cat = $this->category_model->total_rec_found;
			$data['res_cat'] = $res_cat;
			$data['total_rec'] = $total_cat;
			$data['base_link'] = $base_link;//Used for pagination handling
			$data['offset'] = $offset;
			$data['heading_title'] = $heading_title;
			$data['parent_id'] = $parent_id;
			if($ajx_req===TRUE){
				$this->load->view('events/load_category',$data);
			}else{
				$this->load->view('events/view_category',$data);
			}
		}else{
			$params = array('category_id'=>$parent_id);
			load_events($params);
		}
	}

	public function details(){
		$err = 1;
		$news_id = $this->meta_info['entity_id'];
		if($news_id > 0){
			$where_events = "n.news_id='".$news_id."' AND n.status='1'";
			$params_events = array(
				'fields'=>'n.*',
				'where'=>$where_events,
				'return_type'=>'row_array',
				'num_rows_required'=>FALSE
			);
		
			$res_events              =  $this->events_model->get_events($params_events);
			if(is_array($res_events) && !empty($res_events)){
				$err=0;
				$events_id = $res_events['news_id'];
				// $heading_title = $res_events['news_title'];
				$data['res_events'] = $res_events;
			}else{
				$err_msg = "Page you are trying to visit does not exists";
			}
		}else{
			$err_msg = "Invalid request";
		}
		if($err){
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',$err_msg);
			redirect(site_url('events') , '');
		}
		$this->header_menu_section = 'events';
		/*Get DB Photo Media*/
		$total_config_images = $this->config->item('total_events_images');
		$where_photo_media = "news_id='".$news_id."' AND media_type='photo'";
		$params_events_images = array(	
			'where'=>$where_photo_media,
			'limit'=>$total_config_images,
			'debug'=>FALSE
		);

		$res_photo_media  =  $this->events_model->get_media($params_events_images);
		$total_db_photo_media =   $this->events_model->total_rec_found;
		$events_photo_media=array();
		if(is_array($res_photo_media) && !empty($res_photo_media)){
			foreach($res_photo_media as $key=>$val){
				if($val['media']!='' && file_exists(UPLOAD_DIR."/events/".$val['media'])){
					array_push($events_photo_media,$val);
				}
			}
		}
		$data['heading_title'] = !empty($heading_title) ? $heading_title : "Details";
		if(!$err){
			if(empty($events_photo_media)){
				$first_meta_img = "no_img.jpg";
			}else{
				$first_meta_img = $events_photo_media[0]['media'];
			}
			$share_image = get_image('events',$first_meta_img,375,250,'R');
			$whats_app_share_img = $share_image;
			$share_description = format_share_properties($res_events['news_description']);
			$page_meta_share = array(
								'meta_img'=>$share_image,
								'whats_app_share_img'=>$whats_app_share_img,
								'meta_url'=>site_url($res_events['friendly_url']),
								'meta_title'=>$res_events['news_title'],
								'meta_description'=>$share_description
							 );
			$data['page_meta_share'] = $page_meta_share;
		}
		$data['events_photo_media'] = $events_photo_media;
		$this->inject_header_css_files['magiczoomplus']['insert']=1;
		$data['x_dsg_page'] = 'news';
		$this->load->view('events/view_events_details',$data);
	}
}

/* End of file pages.php */