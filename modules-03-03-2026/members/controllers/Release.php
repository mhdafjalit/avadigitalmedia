<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;
class Release extends Private_Controller
{
	private $mId;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('admin/admin_model'));
		$this->load->library(array('Dmailer'));
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index()
	{
		//redirect('admin/metas/'); 
		//exit;
		
		//is_access_method($permission_type=1,$sec_id='2');
		$data['heading_title'] = "New Release";
		$this->mem_top_menu_section = 'release';
		$per_page_res		 = validate_per_page();
		$per_page 			= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "members/release";
		$condition 		= "wr.status != '2'";
		$sort_by_rec ="wr.id DESC";
		$params_release = array(
					'offset'=>$db_offset,
					'limit'=>$per_page,
					'where'=>$condition,
					'orderby'=>$sort_by_rec,
					'groupby'=>'wr.id',
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
		$this->load->view('release/view_release',$data);
	}

	public function new_release()
	{
		redirect('admin/metas/add'); 
		exit;
		
		// is_access_method($permission_type=2,$sec_id='3');
		$res = [];
		$prima_artists = [];
		$release_featurings = [];
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		if($releaseId!=''){
			$res =  get_db_single_row("wl_releases","*"," AND md5(release_id) = '".$releaseId."' AND member_id = '".$this->userId."' ");
			if(empty($res))
			{
				$this->session->set_userdata(array('msg_type'=>'warning'));
				$this->session->set_flashdata('warning',"Record yoou are trying to update not exists");
				redirect('members/release/new_release'.$query_str, '');
			}
			$prima_artists = get_db_multiple_row("wl_primary_artists","*","release_id = '".$res['release_id']."' AND status='1'");
			$release_featurings = get_db_multiple_row("wl_release_featurings","*","release_id = '".$res['release_id']."' AND status='1'");
		}
		$data['heading_title'] = 'Release Information';
		$this->mem_top_menu_section = 'release';
		$img_allow_size =  $this->config->item('allow.cover_file.size');
		$img_allow_dim  =  $this->config->item('allow.cover_img.dimension');
		if($this->input->post('action')!=''){
			$this->form_validation->set_rules('release_title', 'Release Title', 'trim|required|max_length[180]');
			$this->form_validation->set_rules('p_line', 'P Line', 'trim|required|max_length[180]');
			$this->form_validation->set_rules('version', 'Version/Subtitle','trim|max_length[80]');
			$this->form_validation->set_rules('c_line', 'C Line', 'trim|required|max_length[180]');
			$this->form_validation->set_rules('production_year', 'Production Year', 'trim|required');
			$this->form_validation->set_rules('upc_ean', 'UPC/EAN', 'trim');
			$req_file = !empty($res['release_banner']) ? '' : 'file_required|' ;
			$this->form_validation->set_rules('release_banner','Cover Picture',"$req_file file_allowed_type[image]|file_size_max[$img_allow_size]|check_dimension[$img_allow_dim]");
			//$this->form_validation->set_rules('release_banner','Cover Picture',"$req_file file_allowed_type[image]");

			$this->form_validation->set_rules('release_date', 'Release Date', "trim|required");
			$this->form_validation->set_rules('label_id', 'Level Name', "trim|required");
			$this->form_validation->set_rules('producer_catalogue', 'Producer Catalogue Number', "trim|required");
			$this->form_validation->set_rules('genre', 'Genre', "trim|required");
			$this->form_validation->set_rules('sub_genre', 'Sub genre', "trim|required");
			$this->form_validation->set_rules('is_various_artist', 'Various Artist', "trim");
			$prim_artists_rows = $this->input->post('num_prim_artists_rows');
			$num_featurings_rows = $this->input->post('num_featurings_rows');
          	for($i = 0;$i<$prim_artists_rows;$i++) {
			    $this->form_validation->set_rules('primary_artists['.$i.']', "Primary Artist", 'trim|required|alpha|max_length[80]');
			}
			for($i = 0;$i<$num_featurings_rows;$i++) {
			    $this->form_validation->set_rules('featurings['.$i.']', "Featurings", 'trim|max_length[80]');
			}
			if ($this->form_validation->run() == TRUE)
			{
				$release_banner = $res['release_banner'];;
				if( !empty($_FILES['release_banner']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('release_banner','release');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$release_banner = $uploaded_data['upload_data']['file_name'];
					}
				}
				$album_type =  $this->input->post('album_type',TRUE) ?? 1;
				$posted_data = array(
					'member_id' 		=> $this->userId,
					"album_type"		=> $album_type, 
					"release_title"		=> $this->input->post('release_title',TRUE), 
					"p_line"			=> $this->input->post('p_line',TRUE),
					'version'			=> $this->input->post('version',TRUE),
					'c_line'			=> $this->input->post('c_line',TRUE),
					'production_year'	=> $this->input->post('production_year',TRUE),
					'upc_ean'			=> $this->input->post('upc_ean',TRUE),
					'org_release_date'	=> $this->input->post('release_date',TRUE),
					'label_id'			=> $this->input->post('label_id',TRUE),
					'producer_catalogue'=> $this->input->post('producer_catalogue',TRUE),
					'genre'				=> $this->input->post('genre',TRUE),
					'sub_genre'			=> $this->input->post('sub_genre',TRUE),
					'is_various_artist'	=> $this->input->post('is_various_artist',TRUE),
					'release_banner'	=> $release_banner
				);  
				
				$posted_data = $this->security->xss_clean($posted_data);
				$this->db->trans_start(); 
				if(!empty($res) && is_array($res))
				{
					$posted_data['status']		= '0';
					$posted_data['updated_date'] = $this->config->item('config.date.time');
					$where_desc = array("release_id"=>$res['release_id']);
					$this->admin_model->safe_update('wl_releases',$posted_data,$where_desc, FALSE);
					$this->admin_model->safe_delete('wl_primary_artists', array('release_id'=>$res['release_id']), FALSE);
					$primary_artists = $this->input->post('primary_artists');
		            foreach ($primary_artists as $pa => $primary_artist) {
		                $posted_rim_data = array(
		                    'release_id' 	=> $res['release_id'],
		                    'primary_artist'=> $primary_artist,
		                    'status'		=> '1',
		                    'created_date'  => $this->config->item('config.date.time')
		                );
		                $this->admin_model->safe_insert('wl_primary_artists', $posted_rim_data, FALSE);
		            }
		            $this->admin_model->safe_delete('wl_release_featurings', array('release_id'=>$res['release_id']), FALSE);
		            $featurings = $this->input->post('featurings');
		            foreach ($featurings as $rf => $featuring) {
		                $posted_featuring_data = array(
		                    'release_id' 	=> $res['release_id'],
		                    'featuring'		=> $featuring,
		                    'status'		=> '1',
		                    'created_date'  => $this->config->item('config.date.time')
		                );
		                $this->admin_model->safe_insert('wl_release_featurings', $posted_featuring_data, FALSE);
		            }
		            $this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Release details have been updated successfully.');
				}
				else
				{
					$posted_data['created_date']= $this->config->item('config.date.time');
					$inserted_release_id = $this->admin_model->safe_insert('wl_releases',$posted_data, FALSE);
					if($inserted_release_id>0){
						$primary_artists = $this->input->post('primary_artists');
			            foreach ($primary_artists as $pa => $primary_artist) {
			                $posted_data = array(
			                    'release_id' 	=> $inserted_release_id,
			                    'primary_artist'=> $primary_artist,
			                    'status'		=> '1',
			                    'created_date'  => $this->config->item('config.date.time')
			                );
			                $this->admin_model->safe_insert('wl_primary_artists', $posted_data, FALSE);
			            }
			            $featurings = $this->input->post('featurings');
			            foreach ($featurings as $rf => $featuring) {
			                $posted_data = array(
			                    'release_id' 	=> $inserted_release_id,
			                    'featuring'		=> $featuring,
			                    'status'		=> '1',
			                    'created_date'  => $this->config->item('config.date.time')
			                );
			                $this->admin_model->safe_insert('wl_release_featurings', $posted_data, FALSE);
			            }
			        }
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Release has been created successfully.');
				}
				$this->db->trans_complete();
				$inserted_release_id = ($inserted_release_id> 0) ? $inserted_release_id : $res['release_id'];
				redirect('members/release/upload_release/'.md5($inserted_release_id).$query_str, '');
			}
		}
		$data['res'] = $res;
		$data['res_labels'] = get_db_multiple_row('wl_labels','label_id,channel_name'," status='1'");
		$data['primary_artists'] = $prima_artists;
		$data['release_featurings'] = $release_featurings;
		$this->load->view('release/view_release_add',$data);
	}

	public function upload_release()
	{
		// is_access_method($permission_type=2,$sec_id='3');
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		$album_type = (int) $this->input->get_post('album_type');
		$res = get_db_single_row("wl_releases","*"," AND md5(release_id) = '".$releaseId."' AND member_id = '".$this->userId."' ");
		if(is_array($res) && !empty($res))
		{
			$data['heading_title'] = 'Upload';
			$this->mem_top_menu_section = 'release';
			$allow_size = $this->config->item('allow.release_file.size');
			if($this->input->post('action')!=''){
				$req_file = !empty($res['release_song']) ? '' : 'file_required|';
				$file_allowed_type = ($album_type>1) ? 'video':'audio';
				$this->form_validation->set_rules('release_song', 'Release File',$req_file."file_allowed_type[$file_allowed_type]|file_size_max[$allow_size]");
				if ($this->form_validation->run() == TRUE)
				{
					$release_song = $res['release_song'];
					$unlink_release_song = array('source_dir'=>"release/songs",'source_file'=>$release_song);
					if($this->input->post('release_song_delete')==='Y'){
						removeImage($unlink_release_song);
						$release_song = NULL;
					}
					if( !empty($_FILES) && $_FILES['release_song']['name']!='' ){
						$this->load->library('upload');
						$uploaded_data =  $this->upload->my_upload('release_song','release/songs');
						if( is_array($uploaded_data) && !$uploaded_data['err'] ){
							$release_song = $uploaded_data['upload_data']['file_name'];
							removeImage($unlink_release_song);
						}
					}
					$album_type =  $this->input->post('album_type',TRUE) ?? 1;
					$posted_data = array(
						"album_type"	=> $album_type,
						'release_song' 	=> $release_song,
						'status'		=> '0', 
						'updated_date'	=> $this->config->item('config.date.time')
					);  
					
					$posted_data = $this->security->xss_clean($posted_data); 
					$where = " release_id='".$res['release_id']."' ";
	                $this->admin_model->safe_update('wl_releases', $posted_data, $where, FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'File has been uploaded successfully.');
					redirect('members/release/tracks/'.$releaseId.$query_str, '');
				}
			}
			$data['res'] = $res;
			$this->load->view('release/view_release_upload',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Please update current details.");
			redirect('members/release/new_release'.$query_str);
		}
	}

	public function tracks()
	{
		// is_access_method($permission_type=2,$sec_id='3');
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		$res = get_db_single_row("wl_releases","*"," AND md5(release_id) = '".$releaseId."' AND member_id = '".$this->userId."' ");
		if(is_array($res) && !empty($res))
		{
			$data['heading_title'] = 'Tracks';
			$this->mem_top_menu_section = 'release';
			if($this->input->post('action')!=''){
				$this->form_validation->set_rules('prim_track_type','Primary Track Type', 'trim|required|in_list[1,2,3,4,5]');
				$this->form_validation->set_rules('is_instrumental','Instrumental','trim|required|in_list[0,1]');
				$this->form_validation->set_rules('track_title','Track Title', 'trim|required|alpha|max_length[250]');
				$this->form_validation->set_rules('publisher','Publisher','trim|required|max_length[180]');
				$this->form_validation->set_rules('isrc', 'ISRC','trim|required|max_length[50]');
				$this->form_validation->set_rules('preview_start', 'Preview Start','trim|required');
				$this->form_validation->set_rules('is_isrc', 'Generate an ISRC','trim|required');
				$this->form_validation->set_rules('lyrics_lang', 'Lyrics Language','trim|required');
				$this->form_validation->set_rules('lyrics', 'Lyrics','trim|max_length[800]');
				$this->form_validation->set_rules('track_title_lang', 'Track Title Language','trim|required');
				$this->form_validation->set_rules('track_price', 'Price','trim|required|numeric|greater_than_equal_to[0]');
				$this->form_validation->set_rules('is_various_artist', 'Various Artist', "trim");
				$authors_rows = $this->input->post('num_authors_rows');
				$composers_rows = $this->input->post('num_composers_rows');
				$arrangers_rows = $this->input->post('num_arrangers_rows');
				$producers_rows = $this->input->post('num_producers_rows');
	          	for($i = 0;$i<$authors_rows;$i++) {
				    $this->form_validation->set_rules('authors['.$i.']', "Author",'trim|required|alpha|max_length[80]');
				}
				for($i = 0;$i<$composers_rows;$i++) {
				    $this->form_validation->set_rules('composers['.$i.']', "Composer",'trim|required|alpha|max_length[80]');
				}
				for($i = 0;$i<$arrangers_rows;$i++) {
				    $this->form_validation->set_rules('arrangers['.$i.']', "Arranger",'trim|alpha|max_length[80]');
				}
				for($i = 0;$i<$producers_rows;$i++) {
				    $this->form_validation->set_rules('producers['.$i.']', "Producer",'trim|required|alpha|max_length[80]');
				}
				if ($this->form_validation->run() == TRUE)
				{
					$posted_data = array(
						"prim_track_type"	=> $this->input->post('prim_track_type',TRUE),
						'is_instrumental'	=> $this->input->post('is_instrumental',TRUE),
						'track_title'		=> $this->input->post('track_title',TRUE),
						'publisher'			=> $this->input->post('publisher',TRUE),
						'isrc'				=> $this->input->post('isrc',TRUE),
						'preview_start'		=> $this->input->post('preview_start',TRUE),
						'is_isrc'			=> $this->input->post('is_isrc',TRUE),
						'lyrics_lang'		=> $this->input->post('lyrics_lang',TRUE),
						'lyrics'			=> $this->input->post('lyrics',TRUE),
						'track_title_lang'	=> $this->input->post('track_title_lang',TRUE),
						'track_price'		=> $this->input->post('track_price',TRUE),
						'status'			=> '0',
						'updated_date'		=> $this->config->item('config.date.time')
					);
					$posted_data = $this->security->xss_clean($posted_data);
					$this->db->trans_start(); 
					$where_desc = array("release_id"=>$res['release_id']);
					$this->admin_model->safe_update('wl_releases',$posted_data,$where_desc, FALSE);
					$this->admin_model->safe_delete('wl_authors', array('release_id'=>$res['release_id']), FALSE);
					$authors = $this->input->post('authors');
		            foreach ($authors as $aut => $author) {
		                $posted_authors_data = array(
		                    'release_id' 	=> $res['release_id'],
		                    'author'		=> $author,
		                    'status'		=> '1',
		                    'created_date'  => $this->config->item('config.date.time')
		                );
		                $this->admin_model->safe_insert('wl_authors', $posted_authors_data, FALSE);
		            }
		            $this->admin_model->safe_delete('wl_composers', array('release_id'=>$res['release_id']), FALSE);
					$composers = $this->input->post('composers');
		            foreach ($composers as $comp => $composer) {
		                $posted_composers_data = array(
		                    'release_id' 	=> $res['release_id'],
		                    'composer'		=> $composer,
		                    'status'		=> '1',
		                    'created_date'  => $this->config->item('config.date.time')
		                );
		                $this->admin_model->safe_insert('wl_composers', $posted_composers_data, FALSE);
		            }
		            $this->admin_model->safe_delete('wl_arrangers', array('release_id'=>$res['release_id']), FALSE);
		            $arrangers = $this->input->post('arrangers');
		            foreach ($arrangers as $rf => $arranger) {
		                $posted_arrangers_data = array(
		                    'release_id' 	=> $res['release_id'],
		                    'arranger'		=> $arranger,
		                    'status'		=> '1',
		                    'created_date'  => $this->config->item('config.date.time')
		                );
		                $this->admin_model->safe_insert('wl_arrangers', $posted_arrangers_data, FALSE);
		            }
		            $this->admin_model->safe_delete('wl_producers', array('release_id'=>$res['release_id']), FALSE);
		            $producers = $this->input->post('producers');
		            foreach ($producers as $rf => $producer) {
		                $posted_producers_data = array(
		                    'release_id' 	=> $res['release_id'],
		                    'producer'		=> $producer,
		                    'status'		=> '1',
		                    'created_date'  => $this->config->item('config.date.time')
		                );
		                $this->admin_model->safe_insert('wl_producers', $posted_producers_data, FALSE);
		            }
		            $this->db->trans_complete(); 
		            $this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Release details have been updated successfully.');
					redirect('members/release/territories/'.$releaseId.$query_str, '');
				}
			}
			$data['res'] = $res;
			$data['authors'] = get_db_multiple_row("wl_authors","*","release_id = '".$res['release_id']."' AND status='1'");
			$data['composers'] = get_db_multiple_row("wl_composers","*","release_id = '".$res['release_id']."' AND status='1'");
			$data['arrangers'] = get_db_multiple_row("wl_arrangers","*","release_id = '".$res['release_id']."' AND status='1'");
			$data['producers'] = get_db_multiple_row("wl_producers","*","release_id = '".$res['release_id']."' AND status='1'");
			$this->load->view('release/view_release_tracks',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Please update current details.");
			redirect('members/release/new_release'.$query_str);
		}
	}

	public function stores()
	{
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		$res = get_db_single_row("wl_releases","*"," AND md5(release_id) = '".$releaseId."' AND member_id = '".$this->userId."'");
		if(is_array($res) && !empty($res))
		{
			$data['heading_title'] = 'Stores';
			$this->mem_top_menu_section = 'release';
			$release_stores = [];
			$db_release_stores = get_db_multiple_row("wl_release_stores","*","release_id = '".$res['release_id']."'");
			if(!empty($db_release_stores)){
				foreach($db_release_stores as $key=>$val){
					$release_stores[$val['store']] = 1;
				}
			}
			if($this->input->post('action')!=''){
				$this->form_validation->set_rules('release_stores[]', 'Select Relevant Stores','trim|required');
				if ($this->form_validation->run() == TRUE)
				{
					$posted_stores = $this->input->post('release_stores');
					if(!is_array($posted_stores)){
						$posted_stores = array();
					}else
					{
						$posted_stores = array_fill_keys($posted_stores, 1);
					}
					$insert_store_arr = array_diff_key($posted_stores,$release_stores);
					
		            foreach ($insert_store_arr as $store => $tval) {
		                $posted_data = array(
		                    'release_id' 	=> $res['release_id'],
		                    'store'		=> $store,
		                    'status'		=> '1',
		                    'created_date'  => $this->config->item('config.date.time')
		                );
						$posted_data = $this->security->xss_clean($posted_data); 
		                $this->admin_model->safe_insert('wl_release_stores', $posted_data, FALSE);
		            }
		            $remove_store_arr = array_diff_key($release_stores,$posted_stores);
					foreach ($remove_store_arr as $key => $tval) 
		            {
						$this->admin_model->safe_delete('wl_release_stores',array('rs_id'=>$key,'release_id' => $res['release_id']),FALSE);
		            }	
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Stores have been updated successfully.');
					redirect('members/release/territories/'.$releaseId.$query_str, '');
				}
			}
			$data['res_stores'] = get_db_multiple_row("wl_stores","*","status = '1'");;
			$data['release_stores'] = $release_stores;
			$this->load->view('release/view_release_stores',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Please update current details.");
			redirect('members/release/new_release'.$query_str);
		}
	}

	public function territories()
	{
		// is_access_method($permission_type=2,$sec_id='3');
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		$res = get_db_single_row("wl_releases","*"," AND md5(release_id) = '".$releaseId."' AND member_id = '".$this->userId."' ");
		if(is_array($res) && !empty($res))
		{
			$data['heading_title'] = 'Territories';
			$this->mem_top_menu_section = 'release';
			$release_territories = [];
			$db_release_territories = get_db_multiple_row("wl_release_territories","*","release_id = '".$res['release_id']."'");
			if(!empty($db_release_territories)){
				foreach($db_release_territories as $key=>$val){
					$release_territories[$val['territory']] = 1;
				}
			}
			if($this->input->post('action')!=''){
				$this->form_validation->set_rules('release_territories[]', 'Select Relevant Territories','trim|required');
				if ($this->form_validation->run() == TRUE)
				{
					$posted_territories = $this->input->post('release_territories');
					if(!is_array($posted_territories)){
						$posted_territories = array();
					}else
					{
						$posted_territories = array_fill_keys($posted_territories, 1);
					}
					$insert_territ_arr = array_diff_key($posted_territories,$release_territories);
					
		            foreach ($insert_territ_arr as $territory => $tval) {
		                $posted_data = array(
		                    'release_id' 	=> $res['release_id'],
		                    'territory'		=> $territory,
		                    'status'		=> '1',
		                    'created_date'  => $this->config->item('config.date.time')
		                );
						$posted_data = $this->security->xss_clean($posted_data); 
		                $this->admin_model->safe_insert('wl_release_territories', $posted_data, FALSE);
		            }
		            $remove_territ_arr = array_diff_key($release_territories,$posted_territories);
					foreach ($remove_territ_arr as $key => $tval) 
		            {
						$this->admin_model->safe_delete('wl_release_territories',array('rt_id'=>$key,'release_id' => $res['release_id']),FALSE);
		            }	
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'Territories have been updated successfully.');
					redirect('members/release/date_release/'.$releaseId.$query_str, '');
				}
			}
			$params_terr = array(
					'fields'=>"wtc.*,wt.name",
					'where'=>"wtc.status= '1'",
					'exjoin'=>array(
						array('tbl'=>'wl_territories as wt','condition'=>"wt.territory_id=wtc.territory_id AND wt.status='1'")
					),
					'orderby'=>"wtc.country ASC",
					'debug'=>FALSE
				);
			$res_territories 	= $this->admin_model->get_territories($params_terr);
			$terr_countries_arr = [];
			if (is_array($res_territories) && !empty($res_territories)) 
			{
				foreach ($res_territories as $key => $val) 
				{
					$territory_id = $val['territory_id'];
					$country_ids = $val['id'];
					if(!isset($terr_countries_arr[$territory_id]))
					{
						$terr_countries_arr[$territory_id] = array('name'=>$val['name'],'territories'=>array());
					}
					$terr_countries_arr[$territory_id]['territories'][$country_ids] = array('country_id'=>$val['id'],'name'=>$val['country'],'flag'=>$val['flag']);
				}
			}
			$data['res_territories'] = $terr_countries_arr;
			$data['release_territories'] = $release_territories;
			$this->load->view('release/view_release_territories',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Please update current details.");
			redirect('admin/release/new_release'.$query_str);
		}
	}

	public function date_release()
	{
		// is_access_method($permission_type=2,$sec_id='3');
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		$res = get_db_single_row("wl_releases","*"," AND md5(release_id) = '".$releaseId."' AND member_id = '".$this->userId."' ");
		if(is_array($res) && !empty($res))
		{
			$data['heading_title'] = 'Release Date';
			$this->mem_top_menu_section = 'release';
			if($this->input->post('action')!=''){
				$this->form_validation->set_rules('main_release_date', 'Main Release Date', "trim|required");
				// $this->form_validation->set_rules('main_release_state', 'Main Release State', "trim");
				if ($this->form_validation->run() == TRUE)
				{
	                $posted_data = array(
	                    'main_release_date'	=>$this->input->post('main_release_date',TRUE),
						// 'main_release_state'=>$this->input->post('main_release_state',TRUE),
	                    'status'			=> '0',
	                    'updated_date'  	=> $this->config->item('config.date.time')
	                );
					$posted_data = $this->security->xss_clean($posted_data); 
					$where = " release_id='".$res['release_id']."' ";
	                $this->admin_model->safe_update('wl_releases', $posted_data, $where, FALSE);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success', 'You have added label successfully.');
					redirect('members/release/submission/'.$releaseId.$query_str, '');
				}
			}
			$data['res'] = $res;
			$this->load->view('release/view_release_date',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Please update current details.");
			redirect('members/release/new_release'.$query_str);
		}
	}

	public function submission()
	{
		// is_access_method($permission_type=2,$sec_id='3');
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		$res = get_db_single_row("wl_releases","*"," AND md5(release_id) = '".$releaseId."' AND member_id = '".$this->userId."' ");
		if(is_array($res) && !empty($res))
		{
			$data['heading_title'] = 'Submission';
			$this->mem_top_menu_section = 'release';
			if($this->input->post('action')!=''){
				redirect('members/release/view_release/'.md5($res['release_id']));
			}
			$data['res'] = $res;
			$this->load->view('release/view_release_submission',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Please update current details.");
			redirect('members/release/new_release'.$query_str);
		}
	}

	public function view_release()
	{
		// is_access_method($permission_type=2,$sec_id='3');
		$releaseId = $this->uri->segment(4);
		$query_str = query_string();
		$res = get_db_single_row("wl_releases","*"," AND md5(release_id) = '".$releaseId."' AND member_id = '".$this->userId."' ");
		if(is_array($res) && !empty($res))
		{
			$data['heading_title'] = 'Release';
			$this->mem_top_menu_section = 'release';
			$data['res'] = $res;
			$this->load->view('release/view_final_release',$data);
		}
		else
		{
			$this->session->set_userdata(array('msg_type'=>'warning'));
			$this->session->set_flashdata('warning',"Please update current details.");
			redirect('members/release/new_release'.$query_str);
		}
	}

	public function download_stores() {
	    $qry = $this->db->query("SELECT store_id, store_title FROM wl_stores WHERE status = '1'");
	    if ($qry->num_rows() > 0) {
	        $res = $qry->result_array();
	        $spreadsheet = new Spreadsheet();
	        $spreadsheet->getProperties()->setTitle('Stores')->setDescription('Stores');
	        $sheet = $spreadsheet->getActiveSheet();
	        foreach (range('A', 'B') as $column) {
	            $sheet->getColumnDimension($column)->setAutoSize(true);
	        }
	        $headers = ['ID', 'Store'];
	        $column = 'A';
	        $row = 1;
	        foreach ($headers as $header) {
	            $sheet->setCellValue($column . $row, ucwords(str_replace("_", " ", $header)));
	            $column++;
	        }
	        $headerRange = 'A1:B1';
	        $sheet->getStyle($headerRange)->getFont()->setBold(true);
	        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
	        $sheet->getStyle($headerRange)->getFill()->getStartColor()->setRGB('D3D3D3'); 
	        $sheet->freezePane('A2');
	        $row = 2;
	        foreach ($res as $data) {
	            $column = 'A';
	            foreach ($data as $value) {
	                $sheet->setCellValue($column . $row, html_entity_decode($value));
	                $column++;
	            }
	            $row++;
	        }
	        $filename = 'Stores_' . date('dMy') . '.xls';
	        $writer = new Xls($spreadsheet);
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="' . $filename . '"');
	        header('Cache-Control: max-age=0');
	        $writer->save('php://output');
	    }
	}

	public function download_territories() {
	    $qry = $this->db->query("SELECT id, country FROM wl_territory_countries WHERE status = '1'");
	    if ($qry->num_rows() > 0) {
	        $res = $qry->result_array();
	        $spreadsheet = new Spreadsheet();
	        $spreadsheet->getProperties()->setTitle('Territories')->setDescription('Territories');
	        $sheet = $spreadsheet->getActiveSheet();
	        foreach (range('A', 'B') as $column) {
	            $sheet->getColumnDimension($column)->setAutoSize(true);
	        }
	        $headers = ['ID', 'Country'];
	        $column = 'A';
	        $row = 1;
	        foreach ($headers as $header) {
	            $sheet->setCellValue($column . $row, ucwords(str_replace("_", " ", $header)));
	            $column++;
	        }
	        $headerRange = 'A1:B1';
	        $sheet->getStyle($headerRange)->getFont()->setBold(true);
	        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
	        $sheet->getStyle($headerRange)->getFill()->getStartColor()->setRGB('D3D3D3'); 
	        $sheet->freezePane('A2');
	        $row = 2;
	        foreach ($res as $data) {
	            $column = 'A';
	            foreach ($data as $value) {
	                $sheet->setCellValue($column . $row, html_entity_decode($value));
	                $column++;
	            }
	            $row++;
	        }
	        $filename = 'territories_' . date('dMy') . '.xls';
	        $writer = new Xls($spreadsheet);
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="' . $filename . '"');
	        header('Cache-Control: max-age=0');
	        $writer->save('php://output');
	    }
	}

	public function download_labels() {
	    $qry = $this->db->query("SELECT label_id, channel_name FROM wl_labels WHERE status = '1'");
	    if ($qry->num_rows() > 0) {
	        $res = $qry->result_array();
	        $spreadsheet = new Spreadsheet();
	        $spreadsheet->getProperties()->setTitle('Labels')->setDescription('Labels');
	        $sheet = $spreadsheet->getActiveSheet();
	        foreach (range('A', 'B') as $column) {
	            $sheet->getColumnDimension($column)->setAutoSize(true);
	        }
	        $headers = ['ID', 'Label'];
	        $column = 'A';
	        $row = 1;
	        foreach ($headers as $header) {
	            $sheet->setCellValue($column . $row, ucwords(str_replace("_", " ", $header)));
	            $column++;
	        }
	        $headerRange = 'A1:B1';
	        $sheet->getStyle($headerRange)->getFont()->setBold(true);
	        $sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
	        $sheet->getStyle($headerRange)->getFill()->getStartColor()->setRGB('D3D3D3'); 
	        $sheet->freezePane('A2');
	        $row = 2;
	        foreach ($res as $data) {
	            $column = 'A';
	            foreach ($data as $value) {
	                $sheet->setCellValue($column . $row, html_entity_decode($value));
	                $column++;
	            }
	            $row++;
	        }
	        $filename = 'Labels_' . date('dMy') . '.xls';
	        $writer = new Xls($spreadsheet);
	        header('Content-Type: application/vnd.ms-excel');
	        header('Content-Disposition: attachment;filename="' . $filename . '"');
	        header('Cache-Control: max-age=0');
	        $writer->save('php://output');
	    }
	}

	public function download_release_format() {
	    // Get album type from POST data
	    $album_type = (int)$this->input->get_post('album_type');
	    $album_type = ($album_type == 0) ? 1 : $album_type;
	    $column_fields = "album_type,label_id,release_title,p_line,version,c_line,production_year,upc_ean, producer_catalogue, genre, sub_genre,release_song,prim_track_type, is_instrumental,track_title,track_version,isrc,preview_start,publisher,is_isrc, track_title_lang,lyrics_lang,lyrics,track_price,org_release_date,main_release_date, release_banner";
	    $qry = $this->db->query("SELECT " . $column_fields . " FROM wl_releases WHERE status != '2' AND album_type = ?", [$album_type]);
	    if ($qry->num_rows() > 0) {
	        $res = $qry->result_array();
	        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
	        $spreadsheet->getProperties()->setTitle('Release')->setDescription('Release');
	        $sheet = $spreadsheet->getActiveSheet();
	        $column_fields .= ", Primary Artist 1, Primary Artist 2, Primary Artist 3, Primary Artist 4";
	        $column_fields .= ", Featuring 1, Featuring 2, Featuring 3, Featuring 4";
	        $column_fields .= ", Author 1, Author 2, Author 3, Author 4";
	        $column_fields .= ", Composer 1, Composer 2, Composer 3, Composer 4";
	        $column_fields .= ", Arranger 1, Arranger 2, Arranger 3, Arranger 4";
	        $column_fields .= ", Producer 1, Producer 2, Producer 3, Producer 4";
	        $column_fields .= ", Territory 1, Territory 2, Territory 3, Territory 4";
	        $column_fields .= ", Store 1, Store 2, Store 3, Store 4";
	        $column = 'A';
	        $fields_array = explode(",", $column_fields);
	        foreach ($fields_array as $field) {
	            $sheet->setCellValue($column . '1', ucwords(str_replace("_", " ", $field)));
	            $sheet->getStyle($column . '1')->getFont()->setBold(true);
	            $sheet->getStyle($column . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
	            $sheet->getStyle($column . '1')->getFill()->getStartColor()->setRGB('B0B0B0');
	            $sheet->getColumnDimension($column)->setAutoSize(true);
	            
	            $column++;
	        }
	        $row = 2;
	        foreach ($res as $release) {
	            $column = 'A'; // Reset column for each row
	            foreach ($release as $field_val) {
	                $field_val = $field_val ?? '';
	                $sheet->setCellValue($column . $row, html_entity_decode($field_val));
	                $column++;
	            }
	            $row++;
	        }
	        $filename = 'Releases_' . date('dMy') . '.xlsx';
	        header('Cache-Control: max-age=60, must-revalidate');
	        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	        header('Content-Disposition: attachment;filename="' . $filename . '"');
	        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	        $writer->save('php://output');
	    }
	}

	public function bulk_release()
	{
		// is_access_method($permission_type=2,$sec_id='3');
		$data['heading_title'] = 'Create Multiple Release';
		$this->mem_top_menu_section = 'release';
		if($this->input->post('action')!=''){
			$this->form_validation->set_rules('bulk_data', 'Excel Sheet', "file_required|file_allowed_type[excel_document]");
			if ($this->form_validation->run() == TRUE) {
				$this->load->library('upload');
				$release_bulk_data = $this->upload->my_upload('bulk_data', 'release');
				if (is_array($release_bulk_data) && !empty($release_bulk_data)) {
					$file_path = $release_bulk_data['upload_data']['full_path'];
					$this->import_bulk_data_from_excel_sheet($file_path);
					$this->session->set_userdata(array('msg_type'=>'success'));
					$this->session->set_flashdata('success','Release sheet uploaded successfully.');
					redirect('members/release/bulk_release', '');
				}
			}
		}
		$this->load->view('release/view_release_bulk',$data);
	}

	public function import_bulk_data_from_excel_sheet($path = '') {
	    $objPHPExcel = IOFactory::load($path);
	    $worksheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
	    if (is_array($worksheet) && count($worksheet) > 0) {
	        $total = count($worksheet);
	        $li = "";
	        for ($i = 2; $i <= $total; $i++) {
	            $album_type        = isset($worksheet[$i]['A']) ? $worksheet[$i]['A'] : 1;
	            $label_id     	   = isset($worksheet[$i]['B']) ? $worksheet[$i]['B'] : 0;
	            $release_title     = isset($worksheet[$i]['C']) ? $worksheet[$i]['C'] : '';
	            $p_line            = isset($worksheet[$i]['D']) ? $worksheet[$i]['D'] : '';
	            $version           = isset($worksheet[$i]['E']) ? $worksheet[$i]['E'] : '';
	            $c_line            = isset($worksheet[$i]['F']) ? $worksheet[$i]['F'] : '';
	            $production_year   = isset($worksheet[$i]['G']) ? $worksheet[$i]['G'] : '';
	            $upc_ean           = isset($worksheet[$i]['H']) ? $worksheet[$i]['H'] : '';
	            $producer_catalogue= isset($worksheet[$i]['I']) ? $worksheet[$i]['I'] : '';
	            $genre             = isset($worksheet[$i]['J']) ? $worksheet[$i]['J'] : '';
	            $sub_genre         = isset($worksheet[$i]['K']) ? $worksheet[$i]['K'] : '';
	            $release_song      = isset($worksheet[$i]['L']) ? $worksheet[$i]['L'] : '';
             	$prim_track_type   = isset($worksheet[$i]['M']) ? $worksheet[$i]['M'] : '';
	            $is_instrumental   = isset($worksheet[$i]['N']) ? $worksheet[$i]['N'] : '';
	            $track_title       = isset($worksheet[$i]['O']) ? $worksheet[$i]['O'] : '';
	            $track_version     = isset($worksheet[$i]['P']) ? $worksheet[$i]['P'] : '';
	            $isrc              = isset($worksheet[$i]['Q']) ? $worksheet[$i]['Q'] : '';
	            $preview_start     = isset($worksheet[$i]['R']) ? $worksheet[$i]['R'] : '';
	            $publisher         = isset($worksheet[$i]['S']) ? $worksheet[$i]['S'] : '';
	            $is_isrc           = isset($worksheet[$i]['T']) ? $worksheet[$i]['T'] : '';
	            $track_title_lang  = isset($worksheet[$i]['U']) ? $worksheet[$i]['U'] : '';
	            $lyrics_lang       = isset($worksheet[$i]['V']) ? $worksheet[$i]['V'] : '';
	            $lyrics            = isset($worksheet[$i]['W']) ? $worksheet[$i]['W'] : '';
	            $track_price       = isset($worksheet[$i]['X']) ? $worksheet[$i]['X'] : '';
	            $org_release_date  = isset($worksheet[$i]['Y']) ? $worksheet[$i]['Y'] : '';
	            $main_release_date = isset($worksheet[$i]['Z']) ? $worksheet[$i]['Z'] : '';
	            $release_banner    = isset($worksheet[$i]['AA']) ? $worksheet[$i]['AA'] : '';
	            $prim_artists = array(
				    isset($worksheet[$i]['AB']) ? $worksheet[$i]['AB'] : '',
				    isset($worksheet[$i]['AC']) ? $worksheet[$i]['AC'] : '',
				    isset($worksheet[$i]['AD']) ? $worksheet[$i]['AD'] : '',
				    isset($worksheet[$i]['AE']) ? $worksheet[$i]['AE'] : ''
				);
				$featurings = array(
				    isset($worksheet[$i]['AF']) ? $worksheet[$i]['AF'] : '',
				    isset($worksheet[$i]['AG']) ? $worksheet[$i]['AG'] : '',
				    isset($worksheet[$i]['AH']) ? $worksheet[$i]['AH'] : '',
				    isset($worksheet[$i]['AI']) ? $worksheet[$i]['AI'] : ''
				);
				$authors = array(
				    isset($worksheet[$i]['AJ']) ? $worksheet[$i]['AJ'] : '',
				    isset($worksheet[$i]['AK']) ? $worksheet[$i]['AK'] : '',
				    isset($worksheet[$i]['AL']) ? $worksheet[$i]['AL'] : '',
				    isset($worksheet[$i]['AM']) ? $worksheet[$i]['AM'] : ''
				);
				$composers = array(
				    isset($worksheet[$i]['AN']) ? $worksheet[$i]['AN'] : '',
				    isset($worksheet[$i]['AO']) ? $worksheet[$i]['AO'] : '',
				    isset($worksheet[$i]['AP']) ? $worksheet[$i]['AP'] : '',
				    isset($worksheet[$i]['AQ']) ? $worksheet[$i]['AQ'] : ''
				);
				$arrangers = array(
				    isset($worksheet[$i]['AR']) ? $worksheet[$i]['AR'] : '',
				    isset($worksheet[$i]['AS']) ? $worksheet[$i]['AS'] : '',
				    isset($worksheet[$i]['AT']) ? $worksheet[$i]['AT'] : '',
				    isset($worksheet[$i]['AU']) ? $worksheet[$i]['AU'] : ''
				);
				$producers = array(
				    isset($worksheet[$i]['AV']) ? $worksheet[$i]['AV'] : '',
				    isset($worksheet[$i]['AW']) ? $worksheet[$i]['AW'] : '',
				    isset($worksheet[$i]['AX']) ? $worksheet[$i]['AX'] : '',
				    isset($worksheet[$i]['AY']) ? $worksheet[$i]['AY'] : ''
				);
				$territories = array(
				    isset($worksheet[$i]['AZ']) ? $worksheet[$i]['AZ'] : '',
				    isset($worksheet[$i]['BA']) ? $worksheet[$i]['BA'] : '',
				    isset($worksheet[$i]['BB']) ? $worksheet[$i]['BB'] : '',
				    isset($worksheet[$i]['BC']) ? $worksheet[$i]['BC'] : ''
				);
				$stores = array(
				    isset($worksheet[$i]['BD']) ? $worksheet[$i]['BD'] : '',
				    isset($worksheet[$i]['BE']) ? $worksheet[$i]['BE'] : '',
				    isset($worksheet[$i]['BF']) ? $worksheet[$i]['BF'] : '',
				    isset($worksheet[$i]['BG']) ? $worksheet[$i]['BG'] : ''
				);
				if(!$label_id>0){
					$li .="<li>Row $i Label Id is Required</li>";
					continue;
				}
				if($release_title==""){
					$li .="<li>Row $i Release Title is Required</li>";
					continue;
				}
				if($release_title==""){
					$li .="<li>Row $i Release Title is Required</li>";
					continue;
				}
				if($org_release_date==""){
					$li .="<li>Row $i Original Release Date is Required</li>";
					continue;
				}
				if(!is_array($prim_artists) && empty($prim_artists)){
					$li .="<li>Row $i Atleast 1 Primary Artist is Required</li>";
					continue;
				}
				if(!is_array($featurings) && empty($featurings)){
					$li .="<li>Row $i Atleast 1 Featuring is Required</li>";
					continue;
				}
				if(!is_array($authors) && empty($authors)){
					$li .="<li>Row $i Atleast 1 Author is Required</li>";
					continue;
				}
				if(!is_array($composers) && empty($composers)){
					$li .="<li>Row $i Atleast 1 Composer is Required</li>";
					continue;
				}
				if(!is_array($producers) && empty($producers)){
					$li .="<li>Row $i Atleast 1 Producer is Required</li>";
					continue;
				}
				if(!is_array($territories) && empty($territories)){
					$li .="<li>Row $i Atleast 1 Territory is Required</li>";
					continue;
				}
				if(!is_array($stores) && empty($stores)){
					$li .="<li>Row $i Atleast 1 Store is Required</li>";
					continue;
				}
	            $posted_data = array(
	                'member_id'        => $this->userId,
	                'album_type'       => $album_type,
	                'release_title'    => $release_title,
	                'p_line'           => $p_line,
	                'version'          => $version,
	                'c_line'           => $c_line,
	                'production_year'  => $production_year,
	                'upc_ean'          => $upc_ean,
	                'label_id'         => $label_id,
	                'producer_catalogue'=> $producer_catalogue,
	                'genre'            => $genre,
	                'sub_genre'        => $sub_genre,
	                'release_song'     => $release_song,
	                'prim_track_type'  => $prim_track_type,
	                'is_instrumental'  => $is_instrumental,
	                'track_title'      => $track_title,
	                'publisher'        => $publisher,
	                'isrc'             => $isrc,
	                'preview_start'    => $preview_start,
	                'is_isrc'          => $is_isrc,
	                'lyrics_lang'      => $lyrics_lang,
	                'lyrics'           => $lyrics,
	                'track_title_lang' => $track_title_lang,
	                'track_price'      => $track_price,
	                'org_release_date' => $org_release_date,
	                'main_release_date'=> $main_release_date,
	                'release_banner'   => $release_banner,
	                'xls_type'         => '1',
	                'created_date'     => $this->config->item('config.date.time')
	            );
	            $posted_data = $this->security->xss_clean($posted_data);
	            $releaseId = $this->admin_model->safe_insert('wl_releases', $posted_data, FALSE);
	            if ($releaseId > 0) {
	            	if(is_array($prim_artists) && !empty($prim_artists)){
						foreach ($prim_artists as $artist) {
						    if (!empty($artist)) {
						        $prim_data = array(
						            'release_id'    => $releaseId,
						            'primary_artist'=> $artist,
						            'status'        => '1',
						            'created_date'  => $this->config->item('config.date.time')
						        );
						        $prim_data = $this->security->xss_clean($prim_data);
						        $this->admin_model->safe_insert('wl_primary_artists', $prim_data, FALSE);
						    }
						}
					}
					if(is_array($featurings) && !empty($featurings)){
				        foreach ($featurings as $featuring) {
						    if (!empty($featuring)) {
				                $add_data = array(
				                    'release_id' 	=> $releaseId,
				                    'featuring'		=> $featuring,
				                    'status'		=> '1',
				                    'created_date'  => $this->config->item('config.date.time')
				                );
				                $add_data = $this->security->xss_clean($add_data);
				                $this->admin_model->safe_insert('wl_release_featurings',$add_data,FALSE);
				            }
		                }
		            }
		            if(is_array($authors) && !empty($authors)){
		                foreach ($authors as $author) {
						    if (!empty($author)) {
			               		$author_data = array(
				                    'release_id' 	=> $releaseId,
				                    'author'		=> $author,
				                    'status'		=> '1',
				                    'created_date'  => $this->config->item('config.date.time')
				                );
				                $author_data = $this->security->xss_clean($author_data);
				                $this->admin_model->safe_insert('wl_authors', $author_data, FALSE);
				           	}
			            }
			        }
			        if(is_array($composers) && !empty($composers)){
			            foreach ($composers as $composer) {
						    if (!empty($composer)) {
				                $composer_data = array(
				                    'release_id' 	=> $releaseId,
				                    'composer'		=> $composer,
				                    'status'		=> '1',
				                    'created_date'  => $this->config->item('config.date.time')
				                );
				                $composer_data = $this->security->xss_clean($composer_data);
				                $this->admin_model->safe_insert('wl_composers', $composer_data, FALSE);
				            }
			            }
			        }
			        if(is_array($arrangers) && !empty($arrangers)){
		             	foreach ($arrangers as $arranger) {
						    if (!empty($arranger)) {
				                $arranger_data = array(
				                    'release_id' 	=> $releaseId,
				                    'arranger'		=> $arranger,
				                    'status'		=> '1',
				                    'created_date'  => $this->config->item('config.date.time')
				                );
				                $arranger_data = $this->security->xss_clean($arranger_data);
				                $this->admin_model->safe_insert('wl_arrangers', $arranger_data, FALSE);
				            }
			            }
			        }
		            if(is_array($producers) && !empty($producers)){
			            foreach ($producers as $producer) {
						    if (!empty($producer)) {
				                $producer_data = array(
				                    'release_id' 	=> $releaseId,
				                    'producer'		=> $producer,
				                    'status'		=> '1',
				                    'created_date'  => $this->config->item('config.date.time')
				                );
				                $this->admin_model->safe_insert('wl_producers', $producer_data, FALSE);
				            }
			            }
			        }
			        if(is_array($territories) && !empty($territories)){
			            foreach ($territories as $territory) {
						    if (!empty($territory)) {
				                $territory_data = array(
				                    'release_id' 	=> $releaseId,
				                    'territory'		=> $territory,
				                    'status'		=> '1',
				                    'created_date'  => $this->config->item('config.date.time')
				                );
								$territory_data = $this->security->xss_clean($territory_data); 
				                $this->admin_model->safe_insert('wl_release_territories', $territory_data, FALSE);
				            }
			            }
			        }
			        if(is_array($stores) && !empty($stores)){
			            foreach ($stores as $store) {
						    if (!empty($store)) {
				                $store_data = array(
				                    'release_id' 	=> $releaseId,
				                    'store'			=> $store,
				                    'status'		=> '1',
				                    'created_date'  => $this->config->item('config.date.time')
				                );
								$store_data = $this->security->xss_clean($store_data); 
				                $this->admin_model->safe_insert('wl_release_stores', $store_data, FALSE);
				            }
			            }
			        }
	            }
	        }
	        if ($li) {
	            $this->session->set_userdata(array('msg_type' => 'error'));
	            $this->session->set_flashdata('error', "<ul>$li</ul>");
	        }
	    }
	    if (file_exists($path)) {
	        @unlink($path);
	    }
	}

	public function create_playlist(){
		$member_type = $this->session->userdata('member_type');
		
		$data['heading_title'] = 'Create your Own Playlist';
		$this->mem_top_menu_section = 'release';
		$img_allow_size =  $this->config->item('allow.file.size');
		$img_allow_dim  =  $this->config->item('allow.imgage.dimension');
		$per_page_res	= validate_per_page();
		$per_page 		= $per_page_res['per_page'];
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        = "members/release";
		$album_type 	= (int) $this->input->get_post('album_type');
		$keyword 		= $this->db->escape_str($this->input->get_post('keyword',TRUE));
		$condition 	    = "wr.status = '1'";

		if($member_type!='1'){

		if($this->userId>0){
			$condition .= " AND member_id = '".$this->userId."' ";
		}

	    }

		if($album_type>0){
			//$condition .=" AND wr.album_type = '".$album_type."'";
		}
		if($keyword!='')
		{
			$condition.=" AND  ( wr.album_name like '%$keyword%' OR pa.name like '%$keyword%' ) ";
		}
		$sort_by_rec ="wr.id DESC";
		$params_release = array(
			'fields'=>"wr.id,wr.album_name,wr.song_name,pa.name,wr.album_media,wr.artist_id",
			'offset'=>$db_offset,
			'limit'=>$per_page,
			'where'=>$condition,
			'exjoin'=>array(
				array('tbl'=>'wl_artists as pa','condition'=>"pa.pdl_id=wr.artist_id ",'type'=>'LEFT') //AND pa.status='1';
			),
			'orderby'=>$sort_by_rec,
			'groupby'=>'wr.id',
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
		$query_str = query_string();
		if($this->input->post('action')!=''){
			$playlist_songs_arr = $this->session->userdata('playlist_songs_arr');
			if(empty($playlist_songs_arr)){
				$this->session->set_userdata(array('msg_type'=>'warning'));
				$this->session->set_flashdata('warning',"Atleast 1 song in playlist.");
				redirect('members/release/create_playlist'.$query_str);
			}
			$this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[180]');
			$this->form_validation->set_rules('music_type', 'Music Type', 'trim|required|in_list[1,2,3]');
			$this->form_validation->set_rules('playlist_img','Image',"file_required|file_allowed_type[image]|file_size_max[$img_allow_size]|check_dimension[$img_allow_dim]");
			if ($this->form_validation->run() == TRUE)
			{
				$playlist_img = "";
				if( !empty($_FILES['playlist_img']['name'])){
					$this->load->library('upload');
					$uploaded_data =  $this->upload->my_upload('playlist_img','playlist');
					if( is_array($uploaded_data) && !$uploaded_data['err'] ){
						$playlist_img = $uploaded_data['upload_data']['file_name'];
					}
				}
				$album_type =  $this->input->post('album_type',TRUE) ?? 1;
				$posted_data = array(
					'member_id' 	=> $this->userId,
					"album_type"	=> $album_type,
					"title"			=> $this->input->post('title',TRUE),
					"music_type"	=> $this->input->post('music_type',TRUE),
					'playlist_img'	=> $playlist_img,
					'status'		=> '1',
	                'created_date'  => $this->config->item('config.date.time')
	            );
	            $posted_data = $this->security->xss_clean($posted_data);
                $inserted_playlist_id = $this->admin_model->safe_insert('wl_playlists', $posted_data, FALSE);
                if($inserted_playlist_id>0){
                	
                	if(is_array($playlist_songs_arr) && !empty($playlist_songs_arr)){
	                	foreach ($playlist_songs_arr as $key => $val) {
			                $posted_songs_data = array(
			                    'playlist_id' 	=> $inserted_playlist_id,
			                    'release_id'	=> $val['release_id'],
			                    'release_title'	=> $val['song'],
			                    'status'		=> '1',
			                    'created_date'  => $this->config->item('config.date.time')
			                );
			                $this->admin_model->safe_insert('wl_playlist_songs', $posted_songs_data, FALSE);
			            }
			        }
			        $this->session->unset_userdata('playlist_songs_arr');
                }
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success', 'You have added a playlist successfully.');
				redirect('members/album/playlists'.$query_str, '');
			}
		}
		$this->load->view('release/view_release_create_playlist',$data);
	}

	public function add_playlist_song() {
	    if ($this->input->post('btn_sbt') != '') {
	        $custom_error_flds  = array();
	        $playlist_songs_arr = $this->session->userdata('playlist_songs_arr');
	        $playlist_songs_arr = (is_array($playlist_songs_arr) && !empty($playlist_songs_arr)) ? $playlist_songs_arr : [];
	        $this->form_validation->set_rules('release_id', 'Release Id', "trim|required");
	        $release_id = $this->input->post('release_id', TRUE);
	        $form_validation = $this->form_validation->run();
	        if ($form_validation === TRUE && empty($custom_error_flds)) {
	        	$exists_index=-1;
	        	foreach($playlist_songs_arr as $key=>$val){
	        		if($val['release_id']==$release_id){
	        			$exists_index=$key;
	        			break;
	        		}
	        	}
	        	if ($exists_index==-1) {
	                $res_release = get_db_single_row("wl_signed_albums", "id, album_media, song_name, album_name", " AND id = '" . $release_id . "' AND status = '1' ");
	                if (is_array($res_release) && !empty($res_release)) {
	                    $playlist_songs_arr['zx_'.$release_id] = ['release_id' => $release_id, 'song' => $res_release['album_name'],'song_file' => $res_release['album_media']];
	                    $this->session->set_userdata('playlist_songs_arr', $playlist_songs_arr);
	                    $playlist_view = $this->load->view('release/view_added_playlist',array(),TRUE);
	                    $ret_data = array('status' => '1', 'msg' => 'Song added to playlist.', 'release_id' => $release_id,'view_playlist'=>$playlist_view);
	                } else {
	                    $ret_data = array('status' => '0', 'msg' => 'Release not found or inactive.');
	                }
	            } else {
	            	$playlist_view = $this->load->view('release/view_added_playlist',array(),TRUE);
	                $ret_data = array('status' => '2', 'msg' => 'Song already in playlist.','view_playlist'=>$playlist_view);
	            }
	        } else {
	            $error_array = req_compose_errors($custom_error_flds);
	            $ret_data = array('status' => '0', 'msg' => $error_array['release_id'] ?? '');
	        }
	        echo json_encode($ret_data);
	        die;
	    }
	}

	public function remove_playlist_song(){
		$custom_error_flds  = array();
        $playlist_songs_arr = $this->session->userdata('playlist_songs_arr');
       //trace($playlist_songs_arr);
        $playlist_songs_arr = (is_array($playlist_songs_arr) && !empty($playlist_songs_arr)) ? $playlist_songs_arr : [];
		$this->form_validation->set_rules('release_id', 'Release Id', "trim|required");
        $release_id = $this->input->post('release_id', TRUE);
        $form_validation = $this->form_validation->run();
        if ($form_validation === TRUE && empty($custom_error_flds)) {
        	$exists_index=-1;
        	foreach($playlist_songs_arr as $key=>$val){
        		if($val['release_id']==$release_id){
        			$exists_index=$key;
        			break;
        		}
        	}
        	if ($exists_index>=0) {
                unset($playlist_songs_arr[$exists_index]);
                $this->session->set_userdata('playlist_songs_arr', $playlist_songs_arr);
                $playlist_view = $this->load->view('release/view_added_playlist',array(),TRUE);
                $ret_data = array('status' => '1', 'msg' => 'Song deleted from playlist.','view_playlist'=>$playlist_view);
            } else {
            	$playlist_view = $this->load->view('release/view_added_playlist',array(),TRUE);
                $ret_data = array('status' => '2', 'msg' => 'Song not exists in playlist.','view_playlist'=>$playlist_view);
            }
        } else {
            $error_array = req_compose_errors($custom_error_flds);
            $ret_data = array('status' => '0', 'msg' => $error_array['release_id'] ?? '');
        }
        echo json_encode($ret_data);
        die;
	}

	public function paid_tracks(){
		$data['heading_title'] = 'Top Paid Tracks';
		$this->mem_top_menu_section = 'release';
		$this->load->view('release/view_paid_tracks',$data);
	}
}