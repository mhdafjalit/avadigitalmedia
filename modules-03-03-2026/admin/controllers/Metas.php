<?php
class Metas extends Private_Controller {
    
    private $api_service;
    private $meta_fields;
    private $artist_fields;
    private $enums;

    public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation', 'PdlCosmosApiService'));
        $this->form_validation->set_error_delimiters("<div class='required'>","</div>");        
        $this->api_service = $this->pdlcosmosapiservice;        
       
       $this->meta_fields = [
            'album' => [
                'name' => ['required' => true, 'label' => 'Album Name'],
                'label' => ['required' => true, 'label' => 'Label'],
                'c_line' => ['required' => false, 'label' => '© Line'],
                'upc_id' => ['required' => false, 'label' => 'UPC'],
                'p_line' => ['required' => false, 'label' => '<span class="p_symb">P</span> Line'],
                //'film_banner' => ['required' => false, 'label' => 'Film Banner'],
                'publisher' => ['required' => false, 'label' => 'Publisher'],
            ],
			'artist1' => [
                'feature_artist' => ['required' => false, 'label' => 'Feature Artist']
              ],
            'song' => [
                'isrc' => ['required' => true, 'label' => 'ISRC'],
                //'dolby_isrc' => ['required' => false, 'label' => 'Dolby ISRC'],
                'crbt_cut_name' => ['required' => false, 'label' => 'CRBT Title'],
                'song_name' => ['required' => true, 'label' => 'Song Title'],
                //'album_name' => ['required' => true, 'label' => 'Album Name'],
                'language' => ['required' => true, 'label' => 'Language'],
                'album_type' => ['required' => true, 'label' => 'Album Type'],
                'content_type' => ['required' => true, 'label' => 'Content Type'],
                'genre' => ['required' => true, 'label' => 'Genre'],
                'sub_genre' => ['required' => true, 'label' => 'Sub Genre'],
                'mood' => ['required' => false, 'label' => 'Mood'],
                //'description' => ['required' => false, 'label' => 'Description'],
                //'label' => ['required' => true, 'label' => 'Label'],
				'time_for_crbt_cut' => ['required' => false, 'label' => 'Time for CRBT Cut'],
                'track_duration' => ['required' => false, 'label' => 'Track Duration'],                
                //'original_release_date_of_movie' => ['required' => true, 'label' => 'Original Movie Release Date'],
                'original_release_date_of_music' => ['required' => true, 'label' => 'Original Music Release Date'],
                'go_live_date' => ['required' => true, 'label' => 'Release Date'],
                //'date_of_expiry' => ['required' => true, 'label' => 'Date of Expiry'],
                //'parental_advisory' => ['required' => true, 'label' => 'Parental Advisory'],
                'is_instrumental' => ['required' => true, 'label' => 'Is Instrumental'],
				
				'lyric' => ['required' => true, 'label' => 'Lyricist & Writer'],
				//'writer' => ['required' => true, 'label' => 'Writer'],
				'composser' => ['required' => true, 'label' => 'Composser'],
				'music_director' => ['required' => true, 'label' => 'Music Director'],
				//'feature_artist' => ['required' => false, 'label' => 'Feature Artist '],
            ]
        ];        
        
        $this->artist_fields = [
            'id' => ['required' => false, 'label' => 'ID'],
            'name' => ['required' => true, 'label' => 'Name'],
            'apple_id' => ['required' => false, 'label' => 'Apple ID'],
            'meta_id' => ['required' => false, 'label' => 'Meta ID'],
            'facebook_artist_page_url' => ['required' => false, 'label' => 'Facebook Page URL'],
            'insta_artist_page_url' => ['required' => false, 'label' => 'Instagram Page URL'],
            'spotify_id' => ['required' => false, 'label' => 'Spotify ID'],
            'is_iprs_member' => ['required' => false, 'label' => 'IPRS Member'],
            'ipi_number' => ['required' => false, 'label' => 'IPI Number'],			
        ];        
        
        $this->enums = [
            'album_type' => ['Album', 'Film'],
            'content_type' => ['Album', 'Single', 'Compilation', 'Remix'],
           
		    'language' => ['Ahirani', 'Arabic', 'Assamese', 'Awadhi', 'Banjara', 'Bengali', 'Bhojpuri', 'Burmese', 'Chhattisgarhi', 'Chinese', 'Dogri', 'English', 'French', 'Garhwali', 'Garo', 'Gujarati', 'Haryanvi', 'Himachali', 'Hindi', 'Iban', 'Indonesian', 'Instrumental', 'Italian', 'Japanese', 'Javanese', 'Kannada', 'Kashmiri', 'Khasi', 'Kokborok', 'Konkani', 'Korean', 'Kumauni', 'Latin', 'Maithili', 'Malay', 'Malayalam', 'Mandarin', 'Manipuri', 'Marathi', 'Marwari', 'Naga', 'Nagpuri', 'Nepali', 'Odia', 'Pali', 'Persian', 'Punjabi', 'Rajasthani', 'Sainthili', 'Sambalpuri', 'Sanskrit', 'Santali', 'Sindhi', 'Sinhala', 'Spanish', 'Swahili', 'Tamil', 'Telugu', 'Thai', 'Tibetan', 'Tulu', 'Turkish', 'Ukrainian', 'Urdu'],
			
            'mood' => ['Romantic', 'Happy', 'Sad', 'Dance', 'Bhangra', 'Patriotic', 'Nostalgic', 'Inspirational', 'Enthusiastic', 'Optimistic', 'Passion', 'Pessimistic', 'Spiritual', 'Peppy', 'Philosophical', 'Mellow', 'Calm'],
            'parental_advisory' => ['Not Explicit', 'Explicit'],
            'is_instrumental' => ['Yes', 'No'],
            'genre' => [
                'Hip-Hop/Rap' => ['Alternative Hip-Hop', 'Concious Hip-Hop', 'Country Rap', 'Emo Rap', 'Hip-Hop', 'Jazz Rap', 'Pop Rap', 'Trap', 'Trap Beats'],
                'Hindustani Classsical' => ['Instrumental', 'Vocal'],
                'Devotional' => ['Aarti', 'Bhajan', 'Carol', 'Chalisa', 'Chant', 'Geet', 'Gospel', 'Gurbani', 'Hymn', 'Kirtan', 'Mantra', 'Instrumental', 'Paath', 'Islamic', 'Shabad'],
                'Carnatic Classical' => ['Instrumental', 'Vocal'],
                'Ambient / Instrumental' => ['Soft', 'Easy Listening', 'Electronic', 'Fusion', 'Lounge'],
                'Film' => ['Devotional', 'Dialogue', 'Ghazal', 'Hip-Hop/ Rap', 'Instrumental', 'Patriotic', 'Remix', 'Romantic', 'Sad', 'Unplugged', 'Item Song', 'Dance'],
                'Pop' => ['Acoustic Pop', 'Band Songs', 'Chill Pop', 'Contemporary Pop', 'Country Pop/ Regional Pop', 'Dance Pop', 'Electro Pop', 'Lo-Fi Pop', 'Love Songs', 'Pop Rap', 'Pop Singer-Songwriter', 'Sad Songs', 'Soft Pop'],
                'Folk' => ['Ainchaliyan', 'Alha', 'Atulprasadi', 'Baalgeet/ Children Song', 'Banvarh', 'Barhamasa', 'Basant Geet', 'Baul Geet', 'Bhadu Gaan', 'Bhagawati', 'Bhand', 'Bhangra', 'Bhatiali', 'Bhavageete', 'Bhawaiya', 'Bhuta song', 'Bihugeet', 'Birha', 'Borgeet', 'Burrakatha', 'Chappeli', 'Daff', 'Dandiya Raas', 'Dasakathia', 'Deijendrageeti', 'Deknni', 'Dhamal', 'Gadhwali', 'Gagor', 'Garba', 'Ghasiyari Geet', 'Ghoomar', 'Gidda', 'Gugga', 'Hafiz Nagma', 'Heliam', 'Hereileu', 'Hori', 'Jaanapada Geethe', 'Jaita', 'Jhoori', 'Jhora', 'Jhumur', 'Jugni', 'Kajari', 'Kajari/ Kajri /Kajri', 'Karwa Chauth Songs', 'Khor', 'Koligeet', 'Kumayuni', 'Kummi Paatu', 'Lagna Geet /Marriage Song', 'Lalongeeti', 'Lavani', 'Lokgeet', 'Loor', 'Maand', 'Madiga Dappu', 'Mando', 'Mapilla', 'Naatupura Paadalgal', 'Naqual', 'Nati', 'Nautanki', 'Nazrulgeeti', 'Neuleu', 'Nyioga', 'Oggu Katha', 'Paani Hari', 'Pai Song', 'Pandavani', 'Pankhida', 'Patua Sangeet', 'Phag Dance', 'Powada', 'Qawwali', 'Rabindra Sangeet', 'Rajanikanta geeti', 'Ramprasadi', 'Rasiya', 'Rasiya Geet', 'Raslila', 'Raut Nacha', 'Saikuthi Zai', 'Sana Lamok', 'Shakunakhar-Mangalgeet', 'Shyama Sangeet', 'Sohar', 'Sumangali', 'Surma', 'Suvvi paatalu', 'Tappa', 'Teej songs', 'Tusu Gaan', 'Villu Pattu'],
                'Indie' => ['Indian Indie', 'Indie Dance', 'Indie Folk', 'Indie Hip-Hop', 'Indie Lo-Fi', 'Indie Pop', 'Indie Rock', 'Classical Fusion', 'Indie Singer -Songwriter']
            ]
        ];
		
		
    }

    public function index() {        
		
		//trace($this->session->userdata());
		$member_type = $this->session->userdata('member_type');

		$t = $this->input->get_post('t');

        if($member_type=='2'){
		is_access_method($permission_type=1,$sec_id='28');
		}

		$data['heading_title'] = "Manage Meta";

		$this->mem_top_menu_section = 'metas';
		$per_page_res = validate_per_page();
		$per_page = $per_page_res['per_page'];
		$base_link = site_url($this->uri->uri_string);
		$offset = (int) $this->input->get_post('offset');
		$offset = $offset<=0 ? 1 : $offset;
		$db_offset = ($offset-1)*$per_page;
		$base_url = "admin/metas";
		
		$keyword = $this->db->escape_str($this->input->get_post('keyword',TRUE));
		$artist = $this->db->escape_str($this->input->get_post('artist',TRUE));
		$label = $this->db->escape_str($this->input->get_post('label',TRUE));
		$is_verify_meta = $this->input->get_post('is_verify_meta');  

		//trace($this->session->userdata());
		
		$member_id = $this->session->userdata('user_id');

		//trace($member_id);
		
		
		
		$condition = "wm.status != '2'";
		if ($member_type == '2' && $member_id > 0) {
            $condition .= " AND (cus.parent_id = '{$member_id}' OR wm.member_id = '{$member_id}') ";
        } 
        if ($member_type == '3' && $member_id > 0) {
            $condition .= " AND wm.member_id = '{$member_id}' ";
        }
		
		if(!empty($t)){

			 if($t=='p'){

             $condition .= " AND wm.status!='2' AND is_verify_meta='1' AND is_pdl_submit='0' ";
			 $data['heading_title'] = "Processing Album";

			 }
			 if($t=='f'){

             $condition .= " AND wm.status!='2' AND is_verify_meta='1' AND is_pdl_submit='1' ";
			 $data['heading_title'] = "Finalize Release List";

			 }

		}else {

		     $condition .= " AND wm.status!='2' AND is_verify_meta='0' AND is_pdl_submit='0' ";

		}
				
		//$condition = "wm.status != '2'";

		if($is_verify_meta != ''){
			$condition .= " AND wm.is_verify_meta = '".$is_verify_meta."'";
		}

		if($is_verify_meta != ''){
			$condition .= " AND wm.is_verify_meta = '".$is_verify_meta."'";
		}

		
		if($is_verify_meta != ''){
			$condition .= " AND wm.is_verify_meta = '".$is_verify_meta."'";
		}
		if($keyword!='') {
			$condition .= " AND (wm.song_name like '%$keyword%' OR wm.album_name like '%$keyword%') ";
		}
		if($artist != '') {
			$condition .= " AND wm.artist_name = '".$artist."'";
		}
		if($label!=''){
		    $condition .= " AND wm.label = '{$label}'";
		}
		
		$sort_by_rec = "wm.id DESC";
		$params_release = array(
			'fields' => "wm.*,cus.first_name",
			'offset' => $db_offset,
			'limit' => $per_page,
			'where' => $condition,
			'exjoin' => array(
				array('tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id=wm.member_id AND cus.status='1' ", 'type' => 'LEFT')
			),
			'orderby' => $sort_by_rec,
			'groupby' => 'wm.id',
			'debug' => FALSE
		);
		$res_array = $this->admin_model->get_signed_albums($params_release);
// 		echo_sql();die;
		$total_recs = $this->admin_model->total_rec_found;
		$params_pagination = array(
			'base_link' => $base_link,
			'per_page' => $per_page,
			'total_recs' => $total_recs,
			'uri_segment' => $offset,
			'refresh' => 1
		);
		$page_links = front_pagination($params_pagination);     
		$data['page_links'] = $page_links;
		$data['metas'] = $res_array;
		
		$artists_res = $this->db->select('name, pdl_id, apple_id, spotify_id')
				->where('pdl_id !=', "")
				->get('wl_artists')
				->result_array();
				
		$data['artists'] = $artists_res;
	    $label_cond = "wl.status='1'" . ($member_id > 0 && $member_type == '2' ? " AND (cus.parent_id = '{$member_id}' OR wl.member_id = '{$member_id}')" : '') 
            . ($member_id > 0 && $member_type == '3' ? " AND wl.member_id = '{$member_id}'" : '');
        $param_label = [
            'where' => $label_cond,
            'exjoin' => [['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id=wl.member_id AND cus.status='1'", 'type' => 'LEFT']],
            'groupby' => 'wl.label_id',
            'debug' => FALSE
        ];
        $data['labels'] = $labels = $this->admin_model->get_labels($param_label);
		$this->load->view('metas/listing', $data);
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
	
	public function check_song_title_unique($song_title) {
		
		$member_type = $this->session->userdata('member_type');
		if ($member_type == 1) {
			return true;
		}		
		$meta = $this->db->select('id')->where('song_name', $song_title)->where('status !=', 2)->get('wl_signed_albums')->row();
				
		if (!empty($meta)) {
			$this->form_validation->set_message('check_song_title_unique', 'This title already exists');
			return false;
		}
		
		return true;
	}

    public function add() {
        
		$member_type = $this->session->userdata('member_type');

		if($member_type=='2'){
		is_access_method($permission_type=2,$sec_id='28');
		}

		$data = [];
        $data['meta_fields'] = $this->meta_fields;
        $data['artist_fields'] = $this->artist_fields;
        $data['enums'] = $this->enums;
		
		$artists_res = $this->db->select('name, pdl_id, apple_id, spotify_id')
				->where('pdl_id !=', "")
				->get('wl_artists')
				->result_array();
				
		$data['artists'] = $artists_res;
		$label_cond = "wl.status='1'" . ($this->userId > 0 && $this->mres['member_type'] == '2' ? " AND (cus.parent_id = '{$this->userId}' OR wl.member_id = '{$this->userId}')" : '') 
            . ($this->userId > 0 && $this->mres['member_type'] == '3' ? " AND wl.member_id = '{$this->userId}'" : '');
        $param_label = [
            'where' => $label_cond,
            'exjoin' => [['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id=wl.member_id AND cus.status='1'", 'type' => 'LEFT']],
            'groupby' => 'wl.label_id',
            'debug' => FALSE
        ];
        $data['labels'] = $labels = $this->admin_model->get_labels($param_label);
		//$data['labels'] = get_db_multiple_row('wl_labels','label_id,channel_name',"status='1'" . ($this->mres['member_type'] != 1 ? " AND member_id=" . $this->userId : ""));
        if ($this->input->post()) {			
			
			$this->form_validation->set_rules('song[song_name]', 'Song Title', 'required|callback_check_song_title_unique');
				
			$this->form_validation->set_rules('album[name]', 'Album Name', 'required');	
            $this->form_validation->set_rules('album[label]', 'Label', 'required'); 
            $this->form_validation->set_rules('song[isrc]', 'ISRC', 'required');
            
            $this->form_validation->set_rules('song[language]', 'Language', 'required');
            $this->form_validation->set_rules('song[album_type]', 'Album Type', 'required');
            $this->form_validation->set_rules('song[content_type]', 'Content Type', 'required');
            $this->form_validation->set_rules('song[genre]', 'Genre', 'required');
            $this->form_validation->set_rules('song[sub_genre]', 'Sub Genre', 'required');
            $this->form_validation->set_rules('song[original_release_date_of_music]', 'Original Music Release Date', 'required');
            //$this->form_validation->set_rules('song[parental_advisory]', 'Parental Advisory', 'required');
            $this->form_validation->set_rules('song[is_instrumental]', 'Is Instrumental', 'required');
            if (empty($_FILES['audio_file']['name'])) {
                $this->form_validation->set_rules('audio_file', 'Audio File', 'required');
            }
            if (empty($_FILES['image_file']['name'])) {
                $this->form_validation->set_rules('image_file', 'Image File', 'required');
            }
           
            if ($this->form_validation->run() !== FALSE) {
                
                $post_data = $this->input->post();

				//trace( $post_data);
                
				$audio_file = '';//$_FILES['audio_file'];
				$image_file ='';// $_FILES['image_file'];
				
				$audio_file_size_kb = null;
				$audio_md5 = null;
				$image_file_size_kb = null;
				$image_md5 = null;
				$audio_file_path = null;
				$image_file_path = null;

				//trace($_FILES);
				//exit;
				
				if(!empty($_FILES) && $_FILES['audio_file']['name']!='') {

					
					$this->load->library('upload');
					$uploaded_data1 = $this->upload->my_upload('audio_file','release/songs');

					//trace($uploaded_data1);

					///trace(1230);
					//exit;
					
					if(is_array($uploaded_data1) && !$uploaded_data1['err']) {
						$audio_file = $uploaded_data1['upload_data']['file_name'];											
						$audio_file_size_kb = $_FILES['audio_file']['size'];  					
						$audio_file_path = $uploaded_data1['upload_data']['full_path']; 
						$audio_md5 = md5_file($audio_file_path);
					}
				}

				
							
				if(!empty($_FILES) && $_FILES['image_file']['name']!='') {
					$this->load->library('upload');
					$uploaded_data2 = $this->upload->my_upload('image_file','release/songs');
					if(is_array($uploaded_data2) && !$uploaded_data2['err']) {
						$image_file = $uploaded_data2['upload_data']['file_name'];
						
						$image_file_size_kb = $_FILES['image_file']['size']; 
						$image_file_path = $uploaded_data2['upload_data']['full_path']; 
						$image_md5 = md5_file($image_file_path);
					}
				}							
               
                $artist_types = [
                    'track_main_artist', 'track_featured_artist', 'track_remixer_artist',
                    'lyricists', 'composers', 'directors', 'producers', 'starcast',
                    'album_main_artist', 'album_featured_artist', 'album_remixer_artist',
                    'film_director', 'film_producer', 'film_starcast'
                ];
                
                foreach ($artist_types as $type) {
                    if (isset($post_data[$type])) {
                        foreach ($post_data[$type] as $idx => $artist) {
                            if (empty($artist['id']) && !empty($artist['name'])) {
                                $post_data[$type][$idx] = ['name' => $artist['name']];
                            }
                        }
                    }
                }	
				
				$artists_res = $this->db->select('name, pdl_id as id')
				->where('pdl_id', $post_data['artists'])
				->get('wl_artists')
				->row_array();

			
								
                $api_payload = [
					"version" => "2",
					"albums" => [
						[
							"is_update" => false,
							"name" => $post_data['album']['name'],
							"label" => $post_data['album']['label'],
							"c_line" => $post_data['album']['c_line'] ?? '',
							"upc_id" => $post_data['album']['upc_id'] ?? '',
							"p_line" => $post_data['album']['p_line'] ?? '',
							"film_banner" => $post_data['album']['film_banner'] ?? '',
							"publisher" => $post_data['album']['publisher'] ?? '',
							"songs" => [
								[
									"isrc" => $post_data['song']['isrc'],
									"dolby_isrc" => $post_data['song']['dolby_isrc'] ?? '',
									"media" => [
										"id" => "",
										"size" => $audio_file_size_kb,
										"md5" => $audio_md5,
										"filename" => $audio_file
									],
									"data" => [
										"crbt_cut_name" => $post_data['song']['crbt_cut_name'] ?? $post_data['song']['song_name'],
										"song_name" => $post_data['song']['song_name'],
										"album_name" => $post_data['song']['album_name'] ?? $post_data['album']['name'],
										"language" => $post_data['song']['language'],
										"album_type" => $post_data['song']['album_type'],
										"content_type" => $post_data['song']['content_type'],
										"genre" => $post_data['song']['genre'],
										"sub_genre" => $post_data['song']['sub_genre'],
										"mood" => $post_data['song']['mood'] ?? '',
										"description" => $post_data['song']['description'] ?? '',
										"isrc" => $post_data['song']['isrc'],
										"dolby_isrc" => $post_data['song']['dolby_isrc'] ?? '',
										"label" => $post_data['song']['label'] ?? $post_data['album']['label'],
										"publisher" => $post_data['song']['publisher'] ?? $post_data['album']['publisher'] ?? '',
										"track_duration" => $post_data['song']['track_duration'] ?? '0:00:00',
										"crbt_start_time" => $post_data['song']['time_for_crbt_cut'] ?? '0:00:00',
								// 		"time_for_crbt_cut" => $post_data['song']['time_for_crbt_cut'] ?? '0:00:00',
										"original_release_date_of_movie" => $post_data['song']['original_release_date_of_music'] ?? '',
										"original_release_date_of_music" => $post_data['song']['original_release_date_of_music'],
										"go_live_date" => $post_data['song']['go_live_date'] ?? '',
										"date_of_expiry" => $post_data['song']['date_of_expiry'] ?? '',
										"c_line" => $post_data['song']['c_line'] ?? $post_data['album']['c_line'] ?? '',
										"p_line" => $post_data['song']['p_line'] ?? $post_data['album']['p_line'] ?? '',
										"film_banner" => $post_data['song']['film_banner'] ?? $post_data['album']['film_banner'] ?? '',
										"parental_advisory (EXPLICIT ETC)" => $post_data['song']['parental_advisory'] ?? $post_data['song']['parental_advisory'] ?? '',
										"is_instrumental" => $post_data['song']['is_instrumental'],
										"upc_id" => $post_data['song']['upc_id'] ?? '',
										"genreId" => $post_data['song']['genreId'] ?? ''
									],
									"is_instrumental" => $post_data['song']['is_instrumental'],
									"upc_id" => $post_data['song']['upc_id'] ?? '',
									"starcast" => [
									],
									"composers" => [
										[
											'id' => '',
											'name' => $post_data['song']['composser'] ?? ''
										]
									],
									"singers" => [
									],
									"lyricists" => [
										[
											'id' => '',  
											'name' => $post_data['song']['lyric'] ?? ''
										]
									],
									"directors" => [
										[
											'id' => '',
											'name' => $post_data['song']['music_director'] ?? ''
										]
									],													
									"actors" => [
									], 
									"producers" => [
									],
									"track_main_artist" => [
										[
											'id' => $artists_res['id'] ?? '',
											'name' => $artists_res['name'] ?? ''
										]
									],                   
									"track_featured_artist" => [
										
									],
									"track_remixer_artist" => [
									]
								]
							],
							"inlay" => [
								"id" => "",
								"size" => $image_file_size_kb,
								"md5" => $image_md5,
								"filename" => $image_file
							],
							"album_main_artist" => array_map(function($artist) {
								return is_array($artist) ? $artist : ['id' => '', 'name' => $artist];
							}, $post_data['album_main_artist'] ?? []),
							"album_featured_artist" => array_map(function($artist) {
								return is_array($artist) ? $artist : ['id' => '', 'name' => $artist];
							}, $post_data['album_featured_artist'] ?? []),
							"album_remixer_artist" => array_map(function($artist) {
								return is_array($artist) ? $artist : ['id' => '', 'name' => $artist];
							}, $post_data['album_remixer_artist'] ?? []),
							"film_director" => array_map(function($artist) {
								return is_array($artist) ? $artist : ['id' => '', 'name' => $artist];
							}, $post_data['film_director'] ?? []),
							"film_producer" => array_map(function($artist) {
								return is_array($artist) ? $artist : ['id' => '', 'name' => $artist];
							}, $post_data['film_producer'] ?? []),
							"film_starcast" => array_map(function($artist) {
								return is_array($artist) ? $artist : ['id' => '', 'name' => $artist];
							}, $post_data['film_starcast'] ?? [])
						]
					]
				];
                
    //             echo 'Call the API';
    //             trace($api_payload);
				// exit;
				$api_response = $this->api_service->addMeta($api_payload);
				//trace($api_response);
				//exit;
				if ($api_response && isset($api_response['success']) && $api_response['success']) {
					
					$inserted_id = $this->insertAlbum($api_response);
					$row = get_db_single_row("wl_signed_albums", "*", " AND id='".$inserted_id."'");
					
					$image_endpoint = $row['image_signed_url'];
					$image_orignal_path = file_get_contents($row['image_orignal_path']);
					
					$media_endpoint = $row['media_signed_url'];
					$media_orignal_path = file_get_contents($row['media_orignal_path']);


					$img_ext = pathinfo($row['image_orignal_path'], PATHINFO_EXTENSION);

					$media_ext = pathinfo($row['media_orignal_path'], PATHINFO_EXTENSION);
					
					$response1 = $this->api_service->makeAlbumRequest('PUT', $image_endpoint, $image_orignal_path, $img_ext); // success return 200
					
					$response2 = $this->api_service->makeAlbumRequest('PUT', $media_endpoint, $media_orignal_path, $media_ext); // success return 200
					
					if ($response1 == 200 && $response2 == 200) {
						$this->session->set_flashdata('success', 'Meta added successfully!');
                    	redirect('admin/metas/');
					} else {
						$this->session->set_flashdata('error', 'Failed to insert album data! ');
                    	redirect('admin/metas/');
					}
					
                } else {					
                    //$error = isset($response['data']['err']) ? $response['data']['err'] : 'Unknown error occurred';
					$error = isset($api_response['data']['msg']) ? $api_response['data']['msg'] : 'Unknown error occurred';
                    $this->session->set_flashdata('error', 'Failed to add meta: ' . $error);
                }                
            }
        }
        
        $this->load->view('metas/add', $data);
    }
	
	
	public function insertAlbum($api_response) {
		
		try {
			if (empty($api_response['data']['signed_albums'][0]['songs'][0])) {
				throw new Exception('Invalid album/song structure in API response');
			}
	
			$api_data = $api_response['data'];
			$album = $api_data['signed_albums'][0];
			$song = $album['songs'][0];
			$song_data = $song['data'];
			
			$image_name = $album['inlay']['filename'];
			$media_name = $song['media']['filename'];
			
			$image_signed_url = $album['inlay']['signed_url'];
			$media_signed_url = $song['media']['signed_url'];
			
			$orignal_image_path = UPLOAD_DIR.'/release/songs/'.$image_name;
			$orignal_media_path = UPLOAD_DIR.'/release/songs/'.$media_name;
				
			$artist_id = '';
			$artist_name = '';
			
			if (!empty($song['track_main_artist'][0])) {
				
			}
			
			if (!empty($song['track_main_artist'][0])) {
				$artist_id = $song['track_main_artist'][0]['id'] ?? '';
				$artist_name = $song['track_main_artist'][0]['name'] ?? '';
			} elseif (!empty($song['composers'][0])) {
				$artist_id = $song['composers'][0]['id'] ?? '';
				$artist_name = $song['composers'][0]['name'] ?? '';
			} elseif (!empty($song['singers'][0])) {
				$artist_id = $song['singers'][0]['id'] ?? '';
				$artist_name = $song['singers'][0]['name'] ?? '';
			}
			
			$member_id = $this->session->userdata('user_id');
			$member_type = $this->session->userdata('member_type');
	
			$insert_data = [
				'token' => $api_data['token'],
				'member_id' => $member_id,
				//'member_type' => $member_type,
				
				'label' => $album['label'] ?? '',
				'isrc' => $song_data['isrc'] ?? '',
				'album_image' => $album['inlay']['filename'] ?? '',
				'album_media' => $song['media']['filename'] ?? '',
				
				'image_orignal_path' => $orignal_image_path ?? '',
				'media_orignal_path' => $orignal_media_path ?? '',
				
				'image_signed_url' => $image_signed_url ?? '',
				'media_signed_url' => $media_signed_url ?? '',
				
				'album_name' => $album['name'],
				'song_name' => $song_data['song_name'],
				'artist_id' => $artist_id,
				'artist_name' => $artist_name,
				'description' => $song_data['description'] ?? '',
				'genre' => $song_data['genre'] ?? '',
				'is_instrumental' => $song_data['is_instrumental'] ?? 'No',
				'language' => $song_data['language'] ?? '',
				'mood' => $song_data['mood'] ?? '',
				
				'lyric' => $song_data['lyric'] ?? '',
				'writer' => $song_data['writer'] ?? '',
				'composser' => $song_data['composser'] ?? '',
				'music_director' => $song_data['music_director'] ?? '',
				'feature_artist' => $song_data['feature_artist'] ?? '',
				
				'go_live_date' => !empty($song_data['go_live_date']) ? date('Y-m-d', strtotime($song_data['go_live_date'])) : null,
				'date_of_expiry' => !empty($song_data['date_of_expiry']) ? date('Y-m-d', strtotime($song_data['date_of_expiry'])) : null,				
				'media_type' => pathinfo($song['media']['filename'] ?? '', PATHINFO_EXTENSION),
				'created_date' => date('Y-m-d H:i:s'),
				'modified_date' => date('Y-m-d H:i:s'),
				'metadata' => json_encode($api_response),				
				'status' => '0'				
			];
			
			$this->db->insert('wl_signed_albums', $insert_data);			
			return $this->db->insert_id();
	
		} catch (Exception $e) {
			log_message('error', 'Failed to insert signed album: ' . $e->getMessage());
			return false;
		}
	}


	public function finalpdl_submit($mata_id) {

       		
		 $platforms_to_release  = $this->input->post('platforms_to_release');
		        
		 $matas = $this->db->get_where('wl_signed_albums', ['id' => $mata_id])->row_array();	
		
		if (empty($matas)) {

			echo json_encode(['status' => 'error', 'message' => 'Album not found']);

		} else{

		try {

			$payload =	[
              "id" => $matas['album_id'],
              "platforms_to_release" => $platforms_to_release,
              "allow_any_go_live_date" => false
			];

			$response = $this->api_service->pdlSubmit($payload);			
			if ($response['success']) {
				//$this->session->set_flashdata('success', 'PDL submitted successfully!');				
				$this->db->where('id', $mata_id)->update('wl_signed_albums', ['is_pdl_submit' => '1','platforms_to_release' => $platforms_to_release]);
				echo json_encode(['status' => 'success']);

			} else {

				$error = $response['data']['msg'] ?? 'Verification failed';
				//$this->session->set_flashdata('error', 'Verification error: ' . $error);
				echo json_encode(['status' => 'error', 'message' => $error]);
			}
			
		} catch (Exception $e) {
			//$this->session->set_flashdata('error', 'API Error: ' . $e->getMessage());
			echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
		}

	
	}
	 


	}

	
	public function verify_meta($mata_id) {
		
		$matas = $this->db->get_where('wl_signed_albums', ['id' => $mata_id])->row_array();		

		$referrer = !empty($this->input->server('HTTP_REFERER')) ? $this->input->server('HTTP_REFERER') : 'admin/metas';

		if (!$matas) {
			$this->session->set_flashdata('error', 'Album not found');
			redirect($referrer);
			return;
		}	
		try {
			$payload = [];
			$response = $this->api_service->verifyMeta($matas['token'], $payload);		
		//	trace($response);
		//	exit;	
			if ($response['success']) {

				$album_id = $response['data']['albumRes']['id'];

				$this->session->set_flashdata('success', 'Meta verified successfully!');				
				$this->db->where('id', $mata_id)
						 ->update('wl_signed_albums', ['is_verify_meta' => '1','album_id'=>$album_id]);
			} else {
				$error = $response['data']['err'] ?? 'Verification failed';
				$this->session->set_flashdata('error', 'Verification error: ' . $error);
			}
			
		} catch (Exception $e) {
			$this->session->set_flashdata('error', 'API Error: ' . $e->getMessage());
		}
		
		redirect($referrer);
	}
	
	public function active_meta($mata_id) {
		
		$member_type = $this->session->userdata('member_type');
		if($member_type=='2'){
		is_access_method($permission_type=5,$sec_id='28');
		}

		$referrer = !empty($this->input->server('HTTP_REFERER')) ? $this->input->server('HTTP_REFERER') : 'admin/metas';

		$matas = $this->db->get_where('wl_signed_albums', ['id' => $mata_id])->row_array();		
		if (!$matas) {
			$this->session->set_flashdata('error', 'Album not found');
			redirect($referrer);
			return;
		}	
						
		$this->db->where('id', $mata_id)
						 ->update('wl_signed_albums', ['status' => '1']);
						 
		$this->session->set_flashdata('success', 'Meta active successfully!');
		redirect($referrer);
	}
	
	public function deactive_meta($mata_id) {
		
		$member_type = $this->session->userdata('member_type');
		if($member_type=='2'){
		is_access_method($permission_type=6,$sec_id='28');
		}

		$referrer = !empty($this->input->server('HTTP_REFERER')) ? $this->input->server('HTTP_REFERER') : 'admin/metas';

		$matas = $this->db->get_where('wl_signed_albums', ['id' => $mata_id])->row_array();		
		if (!$matas) {
			$this->session->set_flashdata('error', 'Album not found');
			redirect($referrer);
			return;
		}	
						
		$this->db->where('id', $mata_id)
						 ->update('wl_signed_albums', ['status' => '0']);
						 
		$this->session->set_flashdata('success', 'Meta deactive successfully!');
		redirect($referrer);
	}
	
	
    
    public function edit($id) {
        
    }
    
    public function get_meta_details($id) {
		
		$meta = $this->db->get_where('wl_signed_albums', ['id' => $id])->row_array();		
		if (!$meta) {
			show_404();
		}
		$metadata = [];
		if (!empty($meta['metadata'])) {
			$metadata = json_decode($meta['metadata'], true);
		}		
		$data['meta'] = $meta;
		$data['metadata'] = $metadata;
		
		$this->load->view('admin/metas/details', $data);
	}
	
	
	
    
    public function delete($id) {
        
    }
}