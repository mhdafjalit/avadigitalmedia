<?php
class News extends Private_Admin_Controller
{

	private $mId;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('admin/admin_model'));
		$this->load->library(array('safe_encrypt', 'Dmailer'));
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index()
	{
		//is_access_method($permission_type=1,$sec_id='2');
		$data['heading_title'] = "News";
		$this->mem_top_menu_section = 'release';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/release";
		$condition 		= "wr.status != '2'";
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
					'offset'=>$db_offset,
					'limit'=>$per_page,
					'where'=>$condition,
					'orderby'=>$sort_by_rec,
					'groupby'=>'wr.release_id',
					'debug'=>FALSE
				);
		$res_array 	= $this->admin_model->get_releases($params_release);
		$total_recs = $this->admin_model->total_rec_found;
		$params_pagination = array(
			'base_link'=>$base_link,
			'per_page'=>$per_page,
			'total_recs'=>$total_recs,
			'uri_segment'=>$offset,
			'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['res'] 		= $res_array;
		$this->load->view('news/view_news_listing',$data);
	}

	public function details(){
		$err = 1;
		$data['heading_title'] = "Details";
		$this->header_menu_section = 'news';
		$this->inject_header_css_files['magiczoomplus']['insert']=1;
		$data['x_dsg_page'] = 'news';
		$this->load->view('news/view_news_details',$data);
	}
}