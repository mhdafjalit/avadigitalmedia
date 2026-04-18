<?php
class Album extends Private_Controller
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
		$this->album_created();
	}

	public function album_created()
	{
		$album_type = (int) $this->input->get_post('album_type');
		$sec_id = ($album_type>1) ? 14 : 13;
		is_access_method($permission_type=1,$sec_id);
		$data['heading_title'] = "Created Releases";
		$this->mem_top_menu_section = 'album';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =  "admin/album";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$condition 	     ="wr.status = '0'";
		if($album_type>0){
			$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR lab.channel_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
			'fields'=>"wr.*,cus.first_name,lab.channel_name, lab.created_date as label_date",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_labels as lab','condition'=>"lab.label_id=wr.label_id AND lab.status='1'",'type'=>'LEFT'),
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
		if($this->input->post('btn_sbt')!=''){
			$custom_error_flds = array();
			$release_id = $this->input->post('release_id',TRUE);
			$posted_status = $this->input->post('status',TRUE);
			$this->form_validation->set_rules('release_id', 'Release Id', "trim|required");
			$this->form_validation->set_rules('status', 'Status', "trim|required");
			if($posted_status=='3'){
				$this->form_validation->set_rules('reason', 'Reason', "trim|required|max_length[120]");
			}
			$form_validation = $this->form_validation->run();
			if($form_validation===TRUE && empty($custom_error_flds)){
				$posted_data = array(
					'status'=> $posted_status,
					'reason'=> $this->input->post('reason',TRUE)	
				);

				$posted_data = $this->security->xss_clean($posted_data);
				$where       = "release_id = '".$release_id."'";
				$this->admin_model->safe_update('wl_releases',$posted_data,$where,FALSE);
				$ret_data = array('status'=>'1','msg'=>'Updating...','release_id'=>$release_id);
			}else{
				$error_array = req_compose_errors($custom_error_flds);
				$ret_data = array('status'=>'0','error_flds'=>$error_array);
			}
			echo json_encode($ret_data);
			die;
		}
		$this->load->view('album/view_album_created_listing',$data);
	}

	public function album_processing(){

		$album_type = (int) $this->input->get_post('album_type');
		$sec_id = ($album_type>1) ? 16 : 15;
		is_access_method($permission_type=1,$sec_id);
		$data['heading_title'] = "Release to Finalize";
		$this->mem_top_menu_section = 'album';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/album";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$condition 	    ="wr.status = '5'";
		if($album_type>0){
			$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR lab.channel_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
			'fields'=>"wr.*,cus.first_name,lab.channel_name, lab.created_date as label_date",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_labels as lab','condition'=>"lab.label_id=wr.label_id AND lab.status='1'",'type'=>'LEFT'),
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
		$this->load->view('album/view_album_processing_listing',$data);
	}

	public function final_album(){

		$album_type = (int) $this->input->get_post('album_type');
		$sec_id = ($album_type>1) ? 18 : 17;
		is_access_method($permission_type=1,$sec_id);
		$data['heading_title'] = "Finalize Release List";
		$this->mem_top_menu_section = 'album';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/album";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$condition 	    ="wr.status = '1'";
		if($album_type>0){
			$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR lab.channel_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
			'fields'=>"wr.*,cus.first_name,lab.channel_name, lab.created_date as label_date",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_labels as lab','condition'=>"lab.label_id=wr.label_id AND lab.status='1'",'type'=>'LEFT'),
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
		$this->load->view('album/view_album_final_listing',$data);
	}

	public function rejected_album(){

		$album_type = (int) $this->input->get_post('album_type');
		$sec_id = ($album_type>1) ? 20 : 19;
		is_access_method($permission_type=1,$sec_id);
		$data['heading_title'] = "Releases to Correction";
		$this->mem_top_menu_section = 'album';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/album";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$condition 	    ="wr.status = '3'";
		if($album_type>0){
			$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR lab.channel_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
			'fields'=>"wr.*,cus.first_name,lab.channel_name, lab.created_date as label_date",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_labels as lab','condition'=>"lab.label_id=wr.label_id AND lab.status='1'",'type'=>'LEFT'),
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
		$this->load->view('album/view_album_processing_listing',$data);
	}

	public function takedown_album(){

		$album_type = (int) $this->input->get_post('album_type');
		$sec_id = ($album_type>1) ? 22 : 21;
		is_access_method($permission_type=1,$sec_id);
		$data['heading_title'] = "Takedown Releases";
		$this->mem_top_menu_section = 'album';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/album";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$condition 	    ="wr.status = '4'";
		if($album_type>0){
			$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR lab.channel_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
			'fields'=>"wr.*,cus.first_name,lab.channel_name, lab.created_date as label_date",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_labels as lab','condition'=>"lab.label_id=wr.label_id AND lab.status='1'",'type'=>'LEFT'),
				array('tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT')
			),
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
		$this->load->view('album/view_album_takedown_listing',$data);
	}

	public function album_status()
	{
		$album_type = (int) $this->input->get_post('album_type');
		$sec_id = ($album_type>1) ? 14 : 13;
		is_access_method($permission_type=5,$sec_id);
		$releaseId = $this->uri->segment(4);
		$status = $this->input->get_post('al_status');
		$is_exist = count_record('wl_releases'," md5(release_id)='".$releaseId."' ");
		if($is_exist>0)
		{
			$red_url = 'final_album';
			if($status=='takedown')
			{
				$sts = '4';
				$stsmsg = 'takedown';
			}
			elseif($status=='restore')
			{
				$sts = '1';
				$stsmsg = 'restore';
				$red_url = 'takedown_album';
			}
			$where = "md5(release_id) = '".$releaseId."'";
			$this->admin_model->safe_update('wl_releases',array('status'=>$sts),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Album has been $stsmsg successfully.");
			redirect('admin/album/'.$red_url); 
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/album'); 
		}
	}

	public function release_delete()
	{
		$album_type = (int) $this->input->get_post('album_type');
		$sec_id = ($album_type>1) ? 14 : 13;
		is_access_method($permission_type=4,$sec_id);
		$releaseId = $this->uri->segment(4);
		$is_exist = count_record('wl_releases'," md5(release_id)='".$releaseId."' ");
		if($is_exist>0)
		{
			$where = "md5(release_id) = '".$releaseId."'";
			$this->admin_model->safe_update('wl_releases',array('status'=>'2'),$where,FALSE);
			$this->admin_model->safe_update('wl_arrangers',array('status'=>'2'),$where,FALSE);
			$this->admin_model->safe_update('wl_authors',array('status'=>'2'),$where,FALSE);
			$this->admin_model->safe_update('wl_composers',array('status'=>'2'),$where,FALSE);
			$this->admin_model->safe_update('wl_primary_artists',array('status'=>'2'),$where,FALSE);
			$this->admin_model->safe_update('wl_producers',array('status'=>'2'),$where,FALSE);
			$this->admin_model->safe_update('wl_release_featurings',array('status'=>'2'),$where,FALSE);
			$this->admin_model->safe_update('wl_release_territories',array('status'=>'2'),$where,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Release has been deleted successfully.");
			redirect('admin/album/album_created');
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/album/album_created'); 
		}		
	}

    public function view_stored(){
		$data['heading_title'] = 'Release Notification';
		$this->mem_top_menu_section = 'album';
		$releaseId = $this->uri->segment(4);
		$res_release = get_db_single_row('wl_releases','release_id'," AND md5(release_id)='".$releaseId."' ");
		if(is_array($res_release) && !empty($res_release))
		{
			$where_stores = "str.status='1' AND wrs.release_id='".$res_release['release_id']."'";
			$params_stores = [
	            'fields'=>"wrs.store,str.store_id,str.store_title,str.icon",
	            'from'=>'wl_release_stores as wrs',
	            'where'=>$where_stores,
	            'exjoin'=>[
					['tbl'=>'wl_stores as str','condition'=>"str.store_id=wrs.store",'type'=>'LEFT']
				],
				'groupby'=>'str.store_id',
	            'debug'=>FALSE
	        ];
			$release_stores = $this->utils_model->custom_query_builder($params_stores);
			$data['release_stores'] = $release_stores;
		}
		$this->load->view('album/view_release_stored_notification',$data);
	}

	public function playlists(){

		$data['heading_title'] = "All Playlits";
		$this->mem_top_menu_section = 'album';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/album";
		$album_type 	= (int) $this->input->get_post('album_type');
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$condition 	    ="wpt.status = '1'";
		if($album_type>0){
			$condition .=" AND wpt.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( wpt.title like '%$keyword%' ) ";
		}

		$member_type = $this->session->userdata('member_type');
		
	  if($member_type!='1'){

		if($this->userId>0){

			$condition .=" AND wpt.member_id = '".$this->userId."'";
		}
	  }


		$sort_by_rec ="wpt.id DESC";
		$params_release = array(
			'fields'=>"wpt.*",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'orderby'=>$sort_by_rec,
			'groupby'=>'wpt.id',
			'debug'=>FALSE
		);
		$res_array 	= $this->admin_model->get_playlits($params_release);
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
		$this->load->view('album/view_playlist_listing',$data);
	}

	public function playlist_delete()
	{
		$Id = $this->uri->segment(4);
		$is_exist = count_record('wl_playlists'," md5(id)='".$Id."' ");
		if($is_exist>0)
		{
			$where = "md5(id) = '".$Id."'";
			$this->admin_model->safe_update('wl_playlists',array('status'=>'2'),$where,FALSE);
			$where_song = "md5(playlist_id) = '".$Id."'";
			$this->admin_model->safe_update('wl_playlist_songs',array('status'=>'2'),$where_song,FALSE);
			$this->session->set_userdata(array('msg_type'=>'success'));
			$this->session->set_flashdata('success',"Playlist has been deleted successfully.");
			redirect('admin/album/playlists');
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'error'));
			$this->session->set_flashdata('error',"Something Went Wrong.");
			redirect('admin/album/playlists'); 
		}		
	}
}