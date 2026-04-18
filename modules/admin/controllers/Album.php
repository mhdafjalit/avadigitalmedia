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

	public function final_api()
	{
		$data['heading_title'] = 'Release Platform';
		$this->mem_top_menu_section = 'album';
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		$res = get_db_single_row('wl_releases','release_id'," AND md5(release_id)='{$releaseId}'");
		if(is_array($res) && !empty($res))
		{
			if($this->input->post('btn_sbt')!=''){
				// echo "<pre>";
				// print_r($_POST);
				// die();
				$this->form_validation->set_rules('platforms_to_release','Release Platform', 'trim|required');
				if ($this->form_validation->run() == TRUE)
				{
					$platforms = $this->input->post('platforms_to_release');
					$release_id = $res['release_id'];
					$signed_album = $this->db->select('id,album_id')->get_where('wl_signed_albums',['is_pdl_submit'=>'0','release_ref_id'=>$release_id])->row_array();
					if(is_array($signed_album) && !empty($signed_album)){
						$this->load->library(['PdlCosmosApiService']);
						$this->api_service = $this->pdlcosmosapiservice;
					 	try {
					        $payload = [
					            "id" => $signed_album['album_id'],
					            "platforms_to_release" => $platforms,
					            "allow_any_go_live_date" => false
					        ];
					        $response = $this->api_service->pdlSubmit($payload);
					        if (!empty($response['success'])) {
					            $album_data = [
					                'is_pdl_submit' => '1',
					                'platforms_to_release' => $platforms
					            ];
					            $this->admin_model->safe_update('wl_releases',['status'=>'1'],['release_id'=>$release_id],FALSE);
					            $this->admin_model->safe_update('wl_signed_albums',$album_data,['release_ref_id'=>$release_id,'id'=>$signed_album['id']],FALSE);
					            $this->session->set_userdata(array('msg_type'=>'success'));
								$this->session->set_flashdata('success','Your release has been finally submitted successfully.');
								redirect_top('admin/album/final_album'.$query_str, '');
					        } else {
					            $resp_msg = $response['data']['msg'] ?? 'PDL submission failed';
					            $this->session->set_userdata(array('msg_type'=>'error'));
								$this->session->set_flashdata('error',$resp_msg);
								redirect('admin/album/album_processing'.$query_str, '');
					        }
					    } catch (Exception $e) {
				        	$resp_msg = $e->getMessage();
				            $this->session->set_userdata(array('msg_type'=>'error'));
							$this->session->set_flashdata('error',$resp_msg);
							redirect('admin/album/album_processing'.$query_str, '');
					    }
					}
				}
			}
		}
		$data['pdl_release_platform'] = $this->config->item('pdl_release_platform');
		$this->load->view('album/view_form_release_pdlsubmit',$data);
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
		$condition 	     ="wr.status='0'";
		if ($this->mres['member_type'] == '2' && $this->userId > 0) {
		    $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
		}
		if($album_type>0){
			$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!=''){
			$condition.=" AND ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR wr.label_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
			'fields'=>"wr.*,wsa.release_ref_id,wsa.is_verify_meta,wsa.is_pdl_submit,cus.first_name",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>[
				['tbl'=>'wl_signed_albums as wsa','condition'=>"wsa.release_ref_id=wr.release_id",'type'=>'LEFT'],
				['tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT']
			],
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
			if($posted_status=='3' || $posted_status=='4'){
				$this->form_validation->set_rules('reason', 'Reason', "trim|required|max_length[120]");
			}
			$form_validation = $this->form_validation->run();
			if($form_validation===TRUE && empty($custom_error_flds)){
				$posted_data = array(
					'status'=> $posted_status,
					'reason'=> $this->input->post('reason',TRUE)	
				);
				$posted_data = $this->security->xss_clean($posted_data);
				$this->admin_model->safe_update('wl_releases',$posted_data,['release_id'=>$release_id],FALSE);
				$resp_msg = 'Updating...';
				$is_pdl_exist = count_record('wl_signed_albums'," is_verify_meta='1' AND is_pdl_submit='1' AND release_ref_id='{$release_id}' ");
				if($posted_status==6 && $is_pdl_exist>0){
					$this->admin_model->safe_update('wl_releases',['status'=>'1'],['release_id'=>$release_id],FALSE);
				}elseif($posted_status==6){
					$this->load->library(['PdlCosmosApiService']);
					$this->api_service = $this->pdlcosmosapiservice;
					$signed_album = $this->db->select('id,token')->get_where('wl_signed_albums',['is_verify_meta'=>'0','release_ref_id'=>$release_id])->row_array();
					if(is_array($signed_album) && !empty($signed_album)){
						try {
							$payload = [];
							$response = $this->api_service->verifyMeta($signed_album['token'], $payload);
							if ($response['success']) {
								$album_id = $response['data']['albumRes']['id'];
								$album_data =[
									'status' 	=> '1',
									'is_verify_meta' => '1',
									'album_id'	=>$album_id
								];
								$this->admin_model->safe_update('wl_signed_albums',$album_data,['release_ref_id'=>$release_id,'id'=>$signed_album['id']],FALSE);
								$resp_msg = 'Meta verified successfully.';
							} else {
								$error = $response['data']['err'] ?? 'Verification failed';
								$resp_msg = 'Verification error: ' . $error;
							}
						} catch (Exception $e) {
							$resp_msg = $e->getMessage();
						}
					}
				}
				$ret_data = array('status'=>'1','msg'=>$resp_msg,'release_id'=>$release_id);
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
		$per_page_res	= validate_per_page();
		$per_page 		= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "admin/album";
		$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
		$condition 	    ="wr.status IN (5,6) ";
		if ($this->mres['member_type'] == '2' && $this->userId > 0) {
		    $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
		}
		if($album_type>0){
			$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR wr.label_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
			'fields'=>"wr.*,wsa.release_ref_id,wsa.is_verify_meta,wsa.is_pdl_submit,cus.first_name",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>[
				['tbl'=>'wl_signed_albums as wsa','condition'=>"wsa.release_ref_id=wr.release_id",'type'=>'LEFT'],
				['tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT']
			],
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
		
		// echo "<pre>";
		// print_r($data);
		// die();
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
		$condition 	    ="wr.status = '1' AND wsa.is_verify_meta='1' AND wsa.is_pdl_submit='1'";
		if ($this->mres['member_type'] == '2' && $this->userId > 0) {
		    $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
		}
		if($album_type>0){
			$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR wr.label_name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.release_id DESC";
		$params_release = array(
			'fields'=>"wr.*,wsa.id,wsa.release_ref_id,wsa.is_verify_meta,wsa.is_pdl_submit,wsa.is_new_released,wsa.is_recently_added,wsa.is_latest,wsa.is_top_rated,cus.first_name",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>[
				['tbl'=>'wl_signed_albums as wsa','condition'=>"wsa.release_ref_id=wr.release_id",'type'=>'LEFT'],
				['tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT']
			],
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

	// public function rejected_album(){

	// 	$album_type = (int) $this->input->get_post('album_type');
	// 	$sec_id = ($album_type>1) ? 20 : 19;
	// 	is_access_method($permission_type=1,$sec_id);
	// 	$data['heading_title'] = "Releases to Correction Required";
	// 	$this->mem_top_menu_section = 'album';
	// 	$per_page_res		 = validate_per_page();
	// 	$per_page 			= $per_page_res['per_page'];
	//   	$base_link       = site_url($this->uri->uri_string);
	// 	$offset          = (int) $this->input->get_post('offset');
	// 	$offset          = $offset<=0 ? 1 : $offset;
	// 	$db_offset       = ($offset-1)*$per_page;
	// 	$base_url        =   "admin/album";
	// 	$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
	// 	$condition 	    ="wr.status = '3' AND wsa.is_verify_meta='1'";
	// 	if ($this->mres['member_type'] == '2' && $this->userId > 0) {
	// 	    $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
	// 	}
	// 	if($album_type>0){
	// 		$condition .=" AND wr.album_type = '".$album_type."'";
	// 	}
	// 	if($keyword!='')
	// 	{
	// 		$condition.=" AND  ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR wr.label_name like '%$keyword%' ) ";
	// 	}
	// 	$sort_by_rec ="wr.release_id DESC";
	// 	$params_release = array(
	// 		'fields'=>"wr.*,wsa.release_ref_id,wsa.is_verify_meta,wsa.is_pdl_submit,cus.first_name",
	// 		'offset'=>$db_offset,
	// 		'limit'=>$per_page,
	// 		'where'=>$condition,
	// 		'exjoin'=>[
	// 			['tbl'=>'wl_signed_albums as wsa','condition'=>"wsa.release_ref_id=wr.release_id",'type'=>'LEFT'],
	// 			['tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT']
	// 		],
	// 		'orderby'=>$sort_by_rec,
	// 		'groupby'=>'wr.release_id',
	// 		'debug'=>FALSE
	// 	);
	// 	$res_array 	= $this->admin_model->get_releases($params_release);
	// 	$total_recs = $this->admin_model->total_rec_found;
	// 	$params_pagination = array(
	// 		'base_link'=>$base_link,
	// 		'per_page'=>$per_page,
	// 		'total_recs'=>$total_recs,
	// 		'uri_segment'=>$offset,
	// 		'refresh'=>1
	// 	);
	// 	$page_links     = front_pagination($params_pagination);
	// 	$data['page_links'] = $page_links;
	// 	$data['res'] 		= $res_array;
	// 	$this->load->view('album/view_album_correction_listing',$data);
	// }

	public function rejected_album(){

    $album_type = (int) $this->input->get_post('album_type');
    $sec_id = ($album_type > 1) ? 20 : 19;
    is_access_method($permission_type=1, $sec_id);

    $data['heading_title'] = "Releases to Correction Required";
    $this->mem_top_menu_section = 'album';

    // Pagination
    $per_page_res = validate_per_page();
    $per_page     = $per_page_res['per_page'];

    $base_link = site_url($this->uri->uri_string);

    $offset = (int) $this->input->get_post('offset');
    $offset = $offset <= 0 ? 1 : $offset;
    $db_offset = ($offset - 1) * $per_page;

    $keyword = $this->db->escape_str($this->input->get_post('keyword', TRUE));

    // MAIN CONDITION (CLEAN)
    $condition = "wr.status = '3'";

    // Member restriction
    if ($this->mres['member_type'] == '2' && $this->userId > 0) {
        $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
    }

    // Album type filter
    if ($album_type > 0) {
        $condition .= " AND wr.album_type = '".$album_type."'";
    }

    // Search filter
    if ($keyword != '') {
        $condition .= " AND (
            wr.release_title LIKE '%$keyword%' 
            OR cus.first_name LIKE '%$keyword%' 
            OR wr.label_name LIKE '%$keyword%'
        )";
    }

    // Sorting
    $sort_by_rec = "wr.release_id DESC";

    // QUERY PARAMS (FIXED JOIN)
    $params_release = array(
        'fields' => "wr.*, wsa.release_ref_id, wsa.is_verify_meta, wsa.is_pdl_submit, cus.first_name",
        'offset' => $db_offset,
        'limit'  => $per_page,
        'where'  => $condition,
        'exjoin' => [
            [
                'tbl' => 'wl_signed_albums as wsa',
                // CONDITION INSIDE JOIN (IMPORTANT)
                'condition' => "wsa.release_ref_id = wr.release_id AND wsa.is_verify_meta = '1'",
                'type' => 'LEFT'
            ],
            [
                'tbl' => 'wl_customers as cus',
                'condition' => "cus.customers_id = wr.member_id AND cus.status = '1'",
                'type' => 'LEFT'
            ]
        ],
        'orderby' => $sort_by_rec,
        'groupby' => 'wr.release_id',
        'debug'   => FALSE
    );

    // Get Data
    $res_array  = $this->admin_model->get_releases($params_release);
    $total_recs = $this->admin_model->total_rec_found;

    // Pagination Links
    $params_pagination = array(
        'base_link'   => $base_link,
        'per_page'    => $per_page,
        'total_recs'  => $total_recs,
        'uri_segment' => $offset,
        'refresh'     => 1
    );

    $data['page_links'] = front_pagination($params_pagination);
    $data['res']        = $res_array;

    // Load View
    $this->load->view('album/view_album_correction_listing', $data);
}

	// public function takedown_album(){

	// 	$album_type = (int) $this->input->get_post('album_type');
	// 	$sec_id = ($album_type>1) ? 22 : 21;
	// 	is_access_method($permission_type=1,$sec_id);
	// 	$data['heading_title'] = "Takedown Releases";
	// 	$this->mem_top_menu_section = 'album';
	// 	$per_page_res		 = validate_per_page();
	// 	$per_page 			= $per_page_res['per_page'];
	//   	$base_link       = site_url($this->uri->uri_string);
	// 	$offset          = (int) $this->input->get_post('offset');
	// 	$offset          = $offset<=0 ? 1 : $offset;
	// 	$db_offset       = ($offset-1)*$per_page;
	// 	$base_url        =   "admin/album";
	// 	$keyword 		= $this->db->escape_str( $this->input->get_post('keyword',TRUE));
	// 	$condition 	    ="wr.status = '4' AND wsa.is_verify_meta='1' AND wsa.is_pdl_submit='1'";
	// 	if ($this->mres['member_type'] == '2' && $this->userId > 0) {
	// 	    $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
	// 	}
	// 	if($album_type>0){
	// 		$condition .=" AND wr.album_type = '".$album_type."'";
	// 	}
	// 	if($keyword!='')
	// 	{
	// 		$condition.=" AND  ( wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR wr.label_name like '%$keyword%' ) ";
	// 	}
	// 	$sort_by_rec ="wr.release_id DESC";
	// 	$params_release = array(
	// 		'fields'=>"wr.*,wsa.release_ref_id,wsa.is_verify_meta,wsa.is_pdl_submit,cus.first_name",
	// 		'offset'=>$db_offset,
	// 		'limit'=>$per_page,
	// 		'where'=>$condition,
	// 		'exjoin'=>[
	// 			['tbl'=>'wl_signed_albums as wsa','condition'=>"wsa.release_ref_id=wr.release_id",'type'=>'LEFT'],
	// 			['tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT']
	// 		],
	// 		'orderby'=>$sort_by_rec,
	// 		'groupby'=>'wr.release_id',
	// 		'debug'=>FALSE
	// 	);
	// 	$res_array 	= $this->admin_model->get_releases($params_release);
	// 	$total_recs = $this->admin_model->total_rec_found;
	// 	$params_pagination = array(
	// 		'base_link'=>$base_link,
	// 		'per_page'=>$per_page,
	// 		'total_recs'=>$total_recs,
	// 		'uri_segment'=>$offset,
	// 		'refresh'=>1
	// 	);
	// 	$page_links     = front_pagination($params_pagination);
	// 	$data['page_links'] = $page_links;
	// 	$data['res'] 		= $res_array;


	// 	$this->load->view('album/view_album_takedown_listing',$data);
	// }

public function takedown_album(){
    $album_type = (int) $this->input->get_post('album_type');
    $sec_id = ($album_type>1) ? 22 : 21;
    is_access_method($permission_type=1,$sec_id);
    $data['heading_title'] = "Takedown Releases";
    $this->mem_top_menu_section = 'album';
    $per_page_res = validate_per_page();
    $per_page = $per_page_res['per_page'];
    $base_link = site_url($this->uri->uri_string);
    $offset = (int) $this->input->get_post('offset');
    $offset = $offset<=0 ? 1 : $offset;
    $db_offset = ($offset-1)*$per_page;
    $base_url = "admin/album";
    $keyword = $this->db->escape_str($this->input->get_post('keyword',TRUE));
    $status_filter = $this->input->get_post('status',TRUE);
    
    // Base condition for takedown releases (status = 4)
    $condition = "wr.status = '4' AND wsa.is_verify_meta='1' AND wsa.is_pdl_submit='1'";
    
    if ($this->mres['member_type'] == '2' && $this->userId > 0) {
        $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
    }
    if($album_type>0){
        $condition .=" AND wr.album_type = '".$album_type."'";
    }
    if($keyword!=''){
        $condition.=" AND (wr.release_title like '%$keyword%' OR cus.first_name like '%$keyword%' OR wr.label_name like '%$keyword%')";
    }
    
    $sort_by_rec = "wr.release_id DESC";
    $params_release = array(
        'fields'=>"wr.*,wsa.release_ref_id,wsa.is_verify_meta,wsa.is_pdl_submit,cus.first_name",
        'offset'=>$db_offset,
        'limit'=>$per_page,
        'where'=>$condition,
        'exjoin'=>[
            ['tbl'=>'wl_signed_albums as wsa','condition'=>"wsa.release_ref_id=wr.release_id",'type'=>'LEFT'],
            ['tbl'=>'wl_customers as cus','condition'=>"cus.customers_id=wr.member_id AND cus.status='1'",'type'=>'LEFT']
        ],
        'orderby'=>$sort_by_rec,
        'groupby'=>'wr.release_id',
        'debug'=>FALSE
    );
    
    $res_array = $this->admin_model->get_releases($params_release);
    $total_recs = $this->admin_model->total_rec_found;
    
    // Calculate stats - Only for actual statuses (excluding draft/trash)
    // Active status (1)
    $active_count = $this->db
        ->where('status', '1')
        ->where('status !=', '0')
        ->get('wl_releases')
        ->num_rows();
    
    // Pending status (2)
    $pending_count = $this->db
        ->where('status', '2')
        ->where('status !=', '0')
        ->get('wl_releases')
        ->num_rows();
    
    // Inactive status (3)
    $inactive_count = $this->db
        ->where('status', '3')
        ->where('status !=', '0')
        ->get('wl_releases')
        ->num_rows();
    
    // Total stores count
    $total_stores_count = $this->db->get('wl_release_stores')->num_rows();
    
    $params_pagination = array(
        'base_link'=>$base_link,
        'per_page'=>$per_page,
        'total_recs'=>$total_recs,
        'uri_segment'=>$offset,
        'refresh'=>1
    );
    
    $page_links = front_pagination($params_pagination);
    $data['page_links'] = $page_links;
    $data['res'] = $res_array;
    $data['total_records'] = $total_recs;
    $data['per_page'] = $per_page;
    $data['page'] = $offset;
    $data['offset'] = $db_offset;
    $data['active_count'] = $active_count;
    $data['pending_count'] = $pending_count;
    $data['inactive_count'] = $inactive_count;
    $data['total_stores_count'] = $total_stores_count;
    
    $this->load->view('album/view_album_takedown_listing',$data);
}


/**
 * Bulk delete releases with media files deletion
 */
public function bulk_delete() {
    // Check access
    $sec_id = 21;
    is_access_method($permission_type = 1, $sec_id);
    
    $response = array('status' => 0, 'msg' => 'No items selected');
    
    $ids = $this->input->post('ids');
    $permanent = $this->input->post('permanent') ? true : false; // For permanent delete
    
    if (!empty($ids) && is_array($ids)) {
        $deleted_count = 0;
        $failed_count = 0;
        $deleted_files_summary = [];
        
        foreach ($ids as $release_id) {
            $release_id = (int) $release_id;
            
            // Check if release exists
            $release = $this->db->get_where('wl_releases', ['release_id' => $release_id])->row();
            
            if ($release) {
                if ($permanent) {
                    // PERMANENT DELETE - Delete all media files
                    
                    // 1. Delete Song file
                    if (!empty($release->release_song)) {
                        $songPath = UPLOAD_DIR . '/release/songs/' . $release->release_song;
                        if (file_exists($songPath)) {
                            unlink($songPath);
                        }
                    }
                    
                    // 2. Delete Cover Image
                    if (!empty($release->release_image)) {
                        $imgPath = UPLOAD_DIR . '/release/' . $release->release_image;
                        if (file_exists($imgPath)) {
                            unlink($imgPath);
                        }
                    }
                    
                    // 3. Delete Banner Image
                    if (!empty($release->release_banner)) {
                        $bannerPath = UPLOAD_DIR . '/release/' . $release->release_banner;
                        if (file_exists($bannerPath)) {
                            unlink($bannerPath);
                        }
                    }
                    
                    // 4. Delete Additional Images
                    $additional_images = $this->db->get_where('wl_release_images', ['release_id' => $release_id])->result();
                    foreach ($additional_images as $img) {
                        if (!empty($img->image_name)) {
                            $imgPath = UPLOAD_DIR . '/release/' . $img->image_name;
                            if (file_exists($imgPath)) {
                                unlink($imgPath);
                            }
                        }
                    }
                    
                    // 5. Delete Track files
                    $tracks = $this->db->get_where('wl_release_tracks', ['release_id' => $release_id])->result();
                    foreach ($tracks as $track) {
                        if (!empty($track->track_file)) {
                            $trackPath = UPLOAD_DIR . '/release/tracks/' . $track->track_file;
                            if (file_exists($trackPath)) {
                                unlink($trackPath);
                            }
                        }
                    }
                    
                    // 6. Delete all database records
                    $tables_to_clean = [
                        'wl_release_territories',
                        'wl_release_stores',
                        'wl_release_images',
                        'wl_release_tracks',
                        'wl_release_logs',
                        'wl_signed_albums'
                    ];
                    
                    foreach ($tables_to_clean as $table) {
                        $this->db->where('release_id', $release_id)->delete($table);
                    }
                    
                    // Finally delete the release
                    $this->db->where('release_id', $release_id)->delete('wl_releases');
                    
                    $deleted_count++;
                    
                } else {
                    // SOFT DELETE - Just move to trash (status = 0)
                    $this->db->where('release_id', $release_id);
                    $update_data = array(
                        'status' => 0,
                        'deleted_at' => date('Y-m-d H:i:s'),
                        'modified_date' => date('Y-m-d H:i:s')
                    );
                    
                    if ($this->db->update('wl_releases', $update_data)) {
                        $deleted_count++;
                        
                        // Log the action
                        $log_data = array(
                            'member_id' => $this->userId,
                            'release_id' => $release_id,
                            'action' => 'bulk_soft_delete',
                            'ip_address' => $this->input->ip_address(),
                            'created_date' => date('Y-m-d H:i:s')
                        );
                        $this->db->insert('wl_release_logs', $log_data);
                    } else {
                        $failed_count++;
                    }
                }
            } else {
                $failed_count++;
            }
        }
        
        if ($deleted_count > 0) {
            $msg = $deleted_count . ' item(s) ' . ($permanent ? 'permanently deleted' : 'moved to trash') . ' successfully';
            if ($failed_count > 0) {
                $msg .= '. ' . $failed_count . ' failed.';
            }
            
            $response = array(
                'status' => 1,
                'msg' => $msg,
                'deleted_count' => $deleted_count,
                'failed_count' => $failed_count,
                'permanent' => $permanent
            );
        } else {
            $response = array('status' => 0, 'msg' => 'Failed to delete items');
        }
    }
    
    echo json_encode($response);
}
/**
 * Bulk status change
 */
public function bulk_status() {
    // Check access
    $sec_id = 21;
    is_access_method($permission_type = 1, $sec_id);
    
    $response = array('status' => 0, 'msg' => 'No items selected');
    
    $ids = $this->input->post('ids');
    $status = $this->input->post('status');
    
    // Map status string to value
    $status_value = 0;
    if ($status == 'active') {
        $status_value = 1;
    } elseif ($status == 'inactive') {
        $status_value = 3;
    }
    
    if (!empty($ids) && is_array($ids) && $status_value > 0) {
        $updated_count = 0;
        
        foreach ($ids as $release_id) {
            $release_id = (int) $release_id;
            
            $this->db->where('release_id', $release_id);
            $update_data = array(
                'status' => $status_value,
                'modified_date' => date('Y-m-d H:i:s')
            );
            
            if ($this->db->update('wl_releases', $update_data)) {
                $updated_count++;
            }
        }
        
        if ($updated_count > 0) {
            $response = array(
                'status' => 1,
                'msg' => $updated_count . ' item(s) status updated successfully'
            );
        } else {
            $response = array('status' => 0, 'msg' => 'Failed to update status');
        }
    }
    
    echo json_encode($response);
}

/**
 * Export to Excel
 */
public function export_excel() {
    // Check access
    $sec_id = 21;
    is_access_method($permission_type = 1, $sec_id);
    
    // Get filter parameters
    $keyword = $this->db->escape_str($this->input->get('keyword', TRUE));
    $status = $this->input->get('status', TRUE);
    
    // Build condition
    $condition = "wr.status = '4' AND wsa.is_verify_meta='1' AND wsa.is_pdl_submit='1'";
    
    if ($this->mres['member_type'] == '2' && $this->userId > 0) {
        $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
    }
    
    if (!empty($keyword)) {
        $condition .= " AND (wr.release_title LIKE '%$keyword%' OR cus.first_name LIKE '%$keyword%' OR wr.label_name LIKE '%$keyword%')";
    }
    
    if (!empty($status)) {
        $condition .= " AND wr.status = '$status'";
    }
    
    // Get all records
    $params_release = array(
        'fields' => "wr.*, wsa.release_ref_id, wsa.is_verify_meta, wsa.is_pdl_submit, cus.first_name as creator_name",
        'where' => $condition,
        'exjoin' => [
            ['tbl' => 'wl_signed_albums as wsa', 'condition' => "wsa.release_ref_id = wr.release_id", 'type' => 'LEFT'],
            ['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id = wr.member_id AND cus.status='1'", 'type' => 'LEFT']
        ],
        'orderby' => 'wr.release_id DESC',
        'groupby' => 'wr.release_id',
        'debug' => FALSE
    );
    
    $records = $this->admin_model->get_releases($params_release);
    
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="takedown_releases_' . date('Y-m-d') . '.xls"');
    
    // Create HTML table for Excel
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Release Title</th>';
    echo '<th>Artist Name</th>';
    echo '<th>Label Name</th>';
    echo '<th>Producer Catalogue</th>';
    echo '<th>ISRC</th>';
    echo '<th>UPC/EAN</th>';
    echo '<th>Release Date</th>';
    echo '<th>Created By</th>';
    echo '<th>Created Date</th>';
    echo '<th>Status</th>';
    echo '</tr>';
    
    $album_status_arr = $this->config->item('album_status_arr');
    
    foreach ($records as $record) {
        $artist_name = get_db_field_value('wl_artists', 'name', ['pdl_id' => $record['artist_name'] ?? 0]);
        
        echo '<tr>';
        echo '<td>' . $record['release_id'] . '</td>';
        echo '<td>' . htmlspecialchars($record['release_title'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($artist_name) . '</td>';
        echo '<td>' . htmlspecialchars($record['label_name'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($record['producer_catalogue'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($record['isrc'] ?? '') . '</td>';
        echo '<td>' . htmlspecialchars($record['upc_ean'] ?? '') . '</td>';
        echo '<td>' . getDateFormat($record['original_release_date_of_music'] ?? '', 1) . '</td>';
        echo '<td>' . htmlspecialchars($record['creator_name'] ?? '') . '</td>';
        echo '<td>' . getDateFormat($record['created_date'] ?? '', 7) . '</td>';
        echo '<td>' . ($album_status_arr[$record['status']] ?? 'Unknown') . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    exit;
}

/**
 * Export to CSV
 */
public function export_csv() {
    // Check access
    $sec_id = 21;
    is_access_method($permission_type = 1, $sec_id);
    
    // Get filter parameters
    $keyword = $this->db->escape_str($this->input->get('keyword', TRUE));
    $status = $this->input->get('status', TRUE);
    
    // Build condition
    $condition = "wr.status = '4' AND wsa.is_verify_meta='1' AND wsa.is_pdl_submit='1'";
    
    if ($this->mres['member_type'] == '2' && $this->userId > 0) {
        $condition .= " AND (cus.parent_id = '{$this->userId}' OR wr.member_id = '{$this->userId}')";
    }
    
    if (!empty($keyword)) {
        $condition .= " AND (wr.release_title LIKE '%$keyword%' OR cus.first_name LIKE '%$keyword%' OR wr.label_name LIKE '%$keyword%')";
    }
    
    if (!empty($status)) {
        $condition .= " AND wr.status = '$status'";
    }
    
    // Get all records
    $params_release = array(
        'fields' => "wr.*, wsa.release_ref_id, wsa.is_verify_meta, wsa.is_pdl_submit, cus.first_name as creator_name",
        'where' => $condition,
        'exjoin' => [
            ['tbl' => 'wl_signed_albums as wsa', 'condition' => "wsa.release_ref_id = wr.release_id", 'type' => 'LEFT'],
            ['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id = wr.member_id AND cus.status='1'", 'type' => 'LEFT']
        ],
        'orderby' => 'wr.release_id DESC',
        'groupby' => 'wr.release_id',
        'debug' => FALSE
    );
    
    $records = $this->admin_model->get_releases($params_release);
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="takedown_releases_' . date('Y-m-d') . '.csv"');
    
    // Create output stream
    $output = fopen('php://output', 'w');
    
    // Add UTF-8 BOM for Excel compatibility
    fputs($output, "\xEF\xBB\xBF");
    
    // Add headers
    fputcsv($output, [
        'ID', 'Release Title', 'Artist Name', 'Label Name', 
        'Producer Catalogue', 'ISRC', 'UPC/EAN', 'Release Date', 
        'Created By', 'Created Date', 'Status'
    ]);
    
    $album_status_arr = $this->config->item('album_status_arr');
    
    // Add data rows
    foreach ($records as $record) {
        $artist_name = get_db_field_value('wl_artists', 'name', ['pdl_id' => $record['artist_name'] ?? 0]);
        
        fputcsv($output, [
            $record['release_id'],
            $record['release_title'] ?? '',
            $artist_name,
            $record['label_name'] ?? '',
            $record['producer_catalogue'] ?? '',
            $record['isrc'] ?? '',
            $record['upc_ean'] ?? '',
            getDateFormat($record['original_release_date_of_music'] ?? '', 1),
            $record['creator_name'] ?? '',
            getDateFormat($record['created_date'] ?? '', 7),
            $album_status_arr[$record['status']] ?? 'Unknown'
        ]);
    }
    
    fclose($output);
    exit;
}


	public function toggle_is_new_released($id) {
		$this->toggle_meta_flag($id, 'is_new_released', 'released_date_time');
	}
	
	public function toggle_is_recently_added($id) {
		$this->toggle_meta_flag($id, 'is_recently_added', 'recently_date_time');
	}
	
	public function toggle_is_latest($id) {
		$this->toggle_meta_flag($id, 'is_latest', 'latest_date_time');
	}
	
	public function toggle_is_top_rated($id) {
		$this->toggle_meta_flag($id, 'is_top_rated', 'rated_date_time');
	}
	
	private function toggle_meta_flag($id, $field, $date_field = null) {
		if (!$this->db->field_exists($field, 'wl_signed_albums')) {
			echo json_encode(['status' => 'error', 'message' => 'Field does not exist']);
			return;
		}
		
		$meta = $this->db->select($field)->where('id', $id)->get('wl_signed_albums')->row();		
		if(!$meta) {
			echo json_encode(['status' => 'error', 'message' => 'Meta not found']);
			return;
		}		
		
		$new_value = ($meta->$field == 1) ? '0' : '1';
		$update_data = [$field => $new_value];
		if ($date_field && $new_value == '1') {
			$update_data[$date_field] = date('Y-m-d H:i:s');
		} elseif ($date_field && $new_value == '0') {
			$update_data[$date_field] = null;
		}
		
		$this->db->where('id', $id);
		$result = $this->db->update('wl_signed_albums', $update_data);
		
		if($result) {
			echo json_encode(['status' => 'success', 'new_value' => $new_value]);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Update failed', 'db_error' => $this->db->error()]);
		}
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

	// public function release_delete()
	// {
	// 	$album_type = (int) $this->input->get_post('album_type');
	// 	$sec_id = ($album_type>1) ? 14 : 13;
	// 	is_access_method($permission_type=4,$sec_id);
	// 	$releaseId = $this->uri->segment(4);
	// 	$is_exist = count_record('wl_releases'," md5(release_id)='".$releaseId."' ");
	// 	if($is_exist>0)
	// 	{
	// 		$where = "md5(release_id) = '".$releaseId."'";
	// 		$this->admin_model->safe_update('wl_releases',array('status'=>'2'),$where,FALSE);
	// 		$this->admin_model->safe_update('wl_arrangers',array('status'=>'2'),$where,FALSE);
	// 		$this->admin_model->safe_update('wl_authors',array('status'=>'2'),$where,FALSE);
	// 		$this->admin_model->safe_update('wl_composers',array('status'=>'2'),$where,FALSE);
	// 		$this->admin_model->safe_update('wl_primary_artists',array('status'=>'2'),$where,FALSE);
	// 		$this->admin_model->safe_update('wl_producers',array('status'=>'2'),$where,FALSE);
	// 		$this->admin_model->safe_update('wl_release_featurings',array('status'=>'2'),$where,FALSE);
	// 		$this->admin_model->safe_update('wl_release_territories',array('status'=>'2'),$where,FALSE);
	// 		$this->session->set_userdata(array('msg_type'=>'success'));
	// 		$this->session->set_flashdata('success',"Release has been deleted successfully.");
	// 		redirect('admin/album/album_created');
	// 	}
	// 	else
	// 	{
	// 		$this->session->set_userdata(array('msg_type'=>'error'));
	// 		$this->session->set_flashdata('error',"Something Went Wrong.");
	// 		redirect('admin/album/album_created'); 
	// 	}		
	// }


public function release_delete()
{
    $release_id = $this->input->post('release_id');

    if (empty($release_id)) {
        echo json_encode(['status' => 0, 'msg' => 'Invalid ID']);
        return;
    }

    $where = ['release_id' => $release_id];

    // Get current release data to fetch current status value
    $current_release = $this->db
        ->select('status')
        ->where('release_id', $release_id)
        ->get('wl_releases')
        ->row_array();
    
    if (empty($current_release)) {
        echo json_encode(['status' => 0, 'msg' => 'Release not found']);
        return;
    }
    
    $current_status = $current_release['status']; // Get current status value (e.g., 1, 5, etc.)

    // Update: Save current status to astatus, then set status to 2 (trash)
    $update_data = [
        'status' => '2',                    // Move to trash
        'astatus' => $current_status       // Save original status value to astatus
    ];
    
    $this->admin_model->safe_update('wl_releases', $update_data, $where);
    $this->admin_model->safe_update('wl_arrangers', $update_data, $where);
    $this->admin_model->safe_update('wl_authors', $update_data, $where);
    $this->admin_model->safe_update('wl_composers', $update_data, $where);
    $this->admin_model->safe_update('wl_primary_artists', $update_data, $where);
    $this->admin_model->safe_update('wl_producers', $update_data, $where);
    $this->admin_model->safe_update('wl_release_featurings', $update_data, $where);
    $this->admin_model->safe_update('wl_release_territories', $update_data, $where);

    echo json_encode([
        'status' => 1, 
        'msg' => 'Moved to trash',
        'csrf_token' => $this->security->get_csrf_hash()
    ]);
}

// ========== RESTORE FROM TRASH ==========
// When restoring: Take value from astatus and put back to status, then clear astatus
public function release_restore()
{
    $release_id = $this->input->post('release_id');

    if (empty($release_id)) {
        echo json_encode(['status' => 0, 'msg' => 'Invalid ID']);
        return;
    }

    $where = ['release_id' => $release_id];

    // Get current release data to fetch saved astatus value
    $current_release = $this->db
        ->select('astatus')
        ->where('release_id', $release_id)
        ->get('wl_releases')
        ->row_array();
    
    if (empty($current_release)) {
        echo json_encode(['status' => 0, 'msg' => 'Release not found']);
        return;
    }
    
    $saved_astatus = $current_release['astatus']; // Get saved original status value
    
    // If astatus is empty or null, default to 1 (active)
    if (empty($saved_astatus)) {
        $saved_astatus = '1';
    }
    
    // Update: Restore status from astatus value, then clear astatus
    $update_data = [
        'status' => $saved_astatus,         // Restore original status from astatus
        'astatus' => NULL                   // Clear astatus after restoring
    ];
    
    $this->admin_model->safe_update('wl_releases', $update_data, $where);
    $this->admin_model->safe_update('wl_arrangers', $update_data, $where);
    $this->admin_model->safe_update('wl_authors', $update_data, $where);
    $this->admin_model->safe_update('wl_composers', $update_data, $where);
    $this->admin_model->safe_update('wl_primary_artists', $update_data, $where);
    $this->admin_model->safe_update('wl_producers', $update_data, $where);
    $this->admin_model->safe_update('wl_release_featurings', $update_data, $where);
    $this->admin_model->safe_update('wl_release_territories', $update_data, $where);

    echo json_encode([
        'status' => 1, 
        'msg' => 'Restored successfully',
        'csrf_token' => $this->security->get_csrf_hash()
    ]);
}

// ========== PERMANENT DELETE (Hard Delete) ==========
public function release_delete_permanent()
{
    $release_id = $this->input->post('release_id');

    if (empty($release_id)) {
        echo json_encode(['status' => 0, 'msg' => 'Invalid ID']);
        return;
    }

    // Get full record
    $release = $this->db->get_where('wl_releases', ['release_id' => $release_id])->row_array();

    if (!empty($release)) {

        // Delete Song
        if (!empty($release['release_song'])) {
            $songPath = UPLOAD_DIR . '/release/songs/' . $release['release_song'];
            if (file_exists($songPath)) {
                unlink($songPath);
            }
        }

        // Delete Cover Image
        if (!empty($release['release_image'])) {
            $imgPath = UPLOAD_DIR . '/release/' . $release['release_image'];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }

        // Delete Banner Image if exists
        if (!empty($release['release_banner'])) {
            $bannerPath = UPLOAD_DIR . '/release/' . $release['release_banner'];
            if (file_exists($bannerPath)) {
                unlink($bannerPath);
            }
        }

        // Delete DB records permanently
        $this->db->delete('wl_releases', ['release_id' => $release_id]);
        $this->db->delete('wl_arrangers', ['release_id' => $release_id]);
        $this->db->delete('wl_authors', ['release_id' => $release_id]);
        $this->db->delete('wl_composers', ['release_id' => $release_id]);
        $this->db->delete('wl_primary_artists', ['release_id' => $release_id]);
        $this->db->delete('wl_producers', ['release_id' => $release_id]);
        $this->db->delete('wl_release_featurings', ['release_id' => $release_id]);
        $this->db->delete('wl_release_territories', ['release_id' => $release_id]);

        echo json_encode([
            'status' => 1, 
            'msg' => 'Permanently deleted',
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
    } else {
        echo json_encode([
            'status' => 0, 
            'msg' => 'Record not found',
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
    }
}

// ========== TRASH LIST PAGE ==========
public function trash()
{
    $data['menu_dashboard_active'] = 'trash';

    // Get trashed items (status = 2)
    $data['trash_list'] = $this->db
        ->where('status', '2')
        ->get('wl_releases')
        ->result_array();

    // Count for navbar
    $data['trash_count'] = $this->db
        ->where('status', '2')
        ->count_all_results('wl_releases');

    $this->load->view('admin/album/trash_list', $data);
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