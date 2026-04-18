<?php
class Artists extends Private_Controller {
    
	private $api_service;

    public function __construct() {
        parent::__construct();
        $this->load->library(array('form_validation', 'PdlCosmosApiService'));
		$this->form_validation->set_error_delimiters("<div class='required'>","</div>");        
        $this->api_service = $this->pdlcosmosapiservice; 
    }

    public function index() {
        redirect('artists/add');
    }

     public function add() {

		$member_type = $this->session->userdata('member_type');

        if($member_type=='2'){
		is_access_method($permission_type=1,$sec_id='27');
		}

		$data = [
            'search_performed' => false,
            'artist_name' => '',
            'search_results' => [],
            'error' => null
        ];        
        
        if ($this->input->post('search')) {
            $this->form_validation->set_rules('artist_name', 'Artist Name', 'required');
			$this->form_validation->set_rules('apple_id', 'Apple Id', '');
			$this->form_validation->set_rules('spotify_id', 'Spotify Name', '');
            
            if ($this->form_validation->run() !== FALSE) {
                $data['artist_name'] = $this->input->post('artist_name');
                
                try {
                    $search_results = $this->api_service->searchArtists(
                        $data['artist_name'],
                        $this->input->post('apple_id'),
                        $this->input->post('spotify_id')
                    );
                    
                    $data['search_results'] = $search_results['data']['artists'] ?? [];
                    $data['search_performed'] = true;
                } catch (Exception $e) {
                    $data['error'] = $e->getMessage();
                }
            }
        }
        
       
	if ($this->input->post('add_artist')) {
		
		$this->form_validation->set_rules('name', 'Artist Name', 'required');
		
		if ($this->form_validation->run() !== FALSE) {
			$artist_data = [
				'name' => $this->input->post('name'),
				'apple_id' => $this->input->post('apple_id'),
				'meta_id' => $this->input->post('meta_id'),
				'facebook_artist_page_url' => $this->input->post('facebook_artist_page_url'),
				'insta_artist_page_url' => $this->input->post('insta_artist_page_url'),
				'spotify_id' => $this->input->post('spotify_id'),
				'is_iprs_member' => (bool)$this->input->post('is_iprs_member')
			];
			
			if ($this->input->post('create_new_apple')) {
				$artist_data['apple_id'] = 'new';
			}
			
			if ($this->input->post('create_new_spotify')) {
				$artist_data['spotify_id'] = 'new';
			}
			
			try {   

				/*if(!empty($artist_data['apple_id'])){

			     $existing_artist = $this->db->select('pdl_id, apple_id, spotify_id')
				->where('apple_id', $artist_data['apple_id'])
				->where('name', $artist_data['name'])
				->get('wl_artists')
				->row();

				} else{*/

				$existing_artist = $this->db->select('pdl_id, apple_id, spotify_id')
				->where('name', $artist_data['name'])
				->get('wl_artists')
				->row();
				//}
			
			if (!empty($existing_artist)) {
				
				$search_results = $this->api_service->searchArtists(
                        $this->input->post('name'),
                        $this->input->post('apple_id'),
                        $this->input->post('spotify_id')
                    );
                    
                $search_results = @$search_results['data']['artists'] ?? [];
				
				$searchSpotifyId = $this->input->post('spotify_id');
				$searchAppleId = $this->input->post('apple_id');
				
				$foundArtist1 = null;
				foreach ($search_results as $artist1) {
					if (isset($artist1['spotify_id']) && $artist1['spotify_id'] === $searchSpotifyId) {
						$foundArtist1 = $artist1;
						break;
					}
				}
				
				$foundArtist2 = null;
				foreach ($search_results as $artist2) {
					if (isset($artist2['apple_id']) && $artist2['apple_id'] === $searchAppleId) {
						$foundArtist2 = $artist2;
						break;
					}
				}

				//trace($foundArtist1);
				//trace($foundArtist2);
				//exit;
								
				if ($foundArtist1) {					
					$artist_data1['last_updated'] = $this->config->item('config.date.time');
					$artist_data1['spotify_id'] = $searchSpotifyId;
					$this->db->where('pdl_id', $foundArtist1['id'])
						 ->update('wl_artists', $artist_data1);						 
				} else {
					throw new Exception("No artist found with Spotify ID '{$searchSpotifyId}'.\n");
				}
				
				if ($foundArtist2) {					
					$artist_data2['last_updated'] = $this->config->item('config.date.time');
					$artist_data2['apple_id'] = $searchAppleId;
					$this->db->where('pdl_id', $foundArtist2['id'])
						 ->update('wl_artists', $artist_data2);						 
				} else {
					throw new Exception("No artist found with Apple ID '{$searchAppleId}'.\n");
				}
				
				$this->session->set_flashdata('success', 'Artist updated successfully!');
				redirect('admin/artists/add/');
			} else {
				
				if (!empty($artist_data['apple_id']) && $artist_data['apple_id'] !== 'new') {
					$apple_exists = $this->db->select('pdl_id')
						->where('apple_id', $artist_data['apple_id'])
						->get('wl_artists')
						->row();
					
					if ($apple_exists) {
						throw new Exception('Apple ID already linked to another artist');
					}
				}
				
				if (!empty($artist_data['spotify_id']) && $artist_data['spotify_id'] !== 'new') {
					$spotify_exists = $this->db->select('pdl_id')
						->where('spotify_id', $artist_data['spotify_id'])
						->get('wl_artists')
						->row();
					
					if ($spotify_exists) {
						throw new Exception('Spotify ID already linked to another artist');
					}
				}

				
			   // trace($artist_data);
				$result = $this->api_service->addArtist($artist_data);
			   // trace($result);
				
				if ($result['success']) {
					$id = $result['data']['artists']['id'];
					$artist_data['pdl_id'] = $id;
					$artist_data['last_updated'] = $this->config->item('config.date.time');
					$this->db->insert('wl_artists', $artist_data);
					$artist_id = $this->db->insert_id();
					
					$this->session->set_flashdata('success', 'Artist added successfully!');
					redirect('admin/artists/add/');
				} else {

					$error_msg = '';

				$search_results = $this->api_service->searchArtists(
                        $this->input->post('name'),
                        $this->input->post('apple_id'),
                        $this->input->post('spotify_id')
                    );
                    
                $search_results = @$search_results['data']['artists'] ?? [];

				// trace($search_results);
				
				$searchSpotifyId = $this->input->post('spotify_id');
				$searchAppleId = $this->input->post('apple_id');
				
				$foundArtist1 = null;
			  if(is_array($search_results) && !empty($search_results)){
				foreach ($search_results as $artist1) {
					if (isset($artist1['spotify_id']) && $artist1['spotify_id'] === $searchSpotifyId) {
						$foundArtist1 = $artist1;
						break;
					}
				}
			  }
				
				$foundArtist2 = null;
			  if(is_array($search_results) && !empty($search_results)){	
				foreach ($search_results as $artist2) {
					if (isset($artist2['apple_id']) && $artist2['apple_id'] === $searchAppleId) {
						$foundArtist2 = $artist2;
						break;
					}
				}
			  }


			 // trace($foundArtist1);

			//  trace($foundArtist2);

			 // echo 	$foundArtist1['id'].' AND '.$foundArtist2['id'];

				//exit;

			  if(!empty($foundArtist1) && !empty($foundArtist2)){

			
               
				if($foundArtist1['id']==$foundArtist2['id']){

					$id = $foundArtist1['id'];
					$artist_data['pdl_id'] = $id;
					$artist_data['last_updated'] = $this->config->item('config.date.time');
					$this->db->insert('wl_artists', $artist_data);
					$artist_id = $this->db->insert_id();
					
					$this->session->set_flashdata('success', 'Artist added successfully!');
					redirect('admin/artists/add/');

				} else {

					$error_msg = 'Apple ID and Spotify ID are not same user data.';

				}

			  }elseif(!empty($foundArtist2)){

                    $id = $foundArtist2['id'];
					$artist_data['pdl_id'] = $id;
					$artist_data['spotify_id'] = $foundArtist2['spotify_id'];
					$artist_data['last_updated'] = $this->config->item('config.date.time');
					$this->db->insert('wl_artists', $artist_data);
					$artist_id = $this->db->insert_id();
					
					$this->session->set_flashdata('success', 'Artist added successfully!');
					redirect('admin/artists/add/');

			  }elseif(!empty($foundArtist1)){
               
				    $id = $foundArtist1['id'];
					$artist_data['pdl_id'] = $id;
					$artist_data['apple_id'] = $foundArtist1['apple_id'];
					$artist_data['last_updated'] = $this->config->item('config.date.time');
					$this->db->insert('wl_artists', $artist_data);
					$artist_id = $this->db->insert_id();
					
					$this->session->set_flashdata('success', 'Artist added successfully!');
					redirect('admin/artists/add/');

			  }else{
					 					
					$error_msg = 'Failed to add artist';
					if (isset($result['data']['msg'])) {
						$error_msg = $result['data']['msg'];
					} elseif (isset($result['warnings'])) {
						$error_msg .= ': ' . implode(', ', $result['warnings']);
					}
				}

				 if(!empty($error_msg)){

					throw new Exception($error_msg);
				 }

				}
			}
				} catch (Exception $e) {
					$data['error'] = $e->getMessage();    
		  	}
		}
	}        
        $this->load->view('artists/add', $data);
    }

  

	public function get_artist_details() {
		
		$apple_id = $this->input->post('apple_id');
		$spotify_id = $this->input->post('spotify_id');
		
		try {
			$result = $this->api_service->getArtistById($apple_id, $spotify_id);
			
			$artist = null;
			if (isset($result['data']['artist'])) {
				$artist = $result['data']['artist'];
			} elseif (isset($result['data']['artists'][0])) {
				$artist = $result['data']['artists'][0];
			}
	
			if ($artist) {
				$this->load->view('artists/details', ['artist' => $artist]);
			} else {
				echo '<div class="alert alert-warning">Artist not found</div>';
			}
		} catch (Exception $e) {
			echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
		}
	}
	 
    public function update($artist_id) {
        
		$data = [];
        
        try {
            $search_results = $this->api_service->searchArtists('', '', '', 1, 1);
            $artist = null;
            foreach ($search_results['data']['artists'] ?? [] as $a) {
                if ($a['id'] == $artist_id) {
                    $artist = $a;
                    break;
                }
            }
            
            if (!$artist) {
                show_404();
                return;
            }
            
            $data['artist'] = $artist;
            
            if ($this->input->post()) {
                $this->form_validation->set_rules('name', 'Artist Name', 'required');
                
                if ($this->form_validation->run() !== FALSE) {
                    $artist_data = [
                        'name' => $this->input->post('name'),
                        'apple_id' => $this->input->post('apple_id'),
                        'meta_id' => $this->input->post('meta_id'),
                        'facebook_artist_page_url' => $this->input->post('facebook_artist_page_url'),
                        'insta_artist_page_url' => $this->input->post('insta_artist_page_url'),
                        'spotify_id' => $this->input->post('spotify_id'),
                        'locale' => $this->input->post('locale'),
                        'is_iprs_member' => (bool)$this->input->post('is_iprs_member'),
                        'retry_count' => (int)$this->input->post('retry_count')
                    ];
                    
                    $result = $this->api_service->updateArtist($artist_id, $artist_data);
                    
                    if ($result['success']) {
                        $this->session->set_flashdata('success', 'Artist updated successfully!');
                        redirect('artists/view/' . $artist_id);
                    } else {
                        $data['error'] = 'Failed to update artist';
                        if (isset($result['warnings'])) {
                            $data['error'] .= ': ' . implode(', ', $result['warnings']);
                        }
                    }
                }
            }
            
            $this->load->view('artists/update', $data);
        } catch (Exception $e) {
            show_error($e->getMessage());
        }
    }
}