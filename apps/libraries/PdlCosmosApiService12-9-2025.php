<?php
class PdlCosmosApiService {
    
	//private $base_url = 'http://pdl.gaonaweb.com/v2.0/'; //for test environment
    private $base_url = 'https://apicms.infinitesoul.in/v2.0/';  //for live environment
    private $token;

    public function __construct() {
        
		$ci =& get_instance();
        $this->token = $ci->config->item('pdl_api_token');        
        if (empty($this->token)) {
            throw new Exception('PDL API token is not configured');
        }
    }

    public function searchArtists($artist_name, $apple_id = '', $spotify_id = '', $page = 1, $page_offset = 50) {
        
		$endpoint = 'artists/search';
        $payload = [
            'artist_name' => $artist_name,
            'curr_page' => $page,
            'page_off_set' => $page_offset
        ];
        if (!empty($apple_id)) {
            $payload['apple_id'] = $apple_id;
        }
        if (!empty($spotify_id)) {
            $payload['spotify_id'] = $spotify_id;
        }
		//trace($payload);
		//exit;
        return $this->makeRequest('POST', $endpoint, $payload);
    }
    	 
    public function addArtist($artist_data) {
        
		$endpoint = 'artists/add';
        $payload = ['artist' => $artist_data];
        return $this->makeRequest('POST', $endpoint, $payload);
    }
	
	public function getArtistById($apple_id, $spotify_id) {
		
		$endpoint = 'artists/search';
		$payload = [
			'apple_id' => $apple_id,
			'spotify_id' => $spotify_id,
			'curr_page' => 1,
			'page_off_set' => 1
		];
		return $this->makeRequest('POST', $endpoint, $payload);
	}

    public function updateArtist($artist_id, $artist_data) {
        
		$endpoint = 'artists/update';
        $artist_data['id'] = $artist_id;
        $payload = ['artist' => $artist_data];
        return $this->makeRequest('PUT', $endpoint, $payload);
    }
	
	public function addMeta($payload) {
        
		$endpoint = 'album/add/meta';
        return $this->makeRequest('POST', $endpoint, $payload);
    }


    public function pdlSubmit($payload) {
        
		$endpoint = 'album/pdl/submit';
        return $this->makeRequest('POST', $endpoint, $payload);
    }
	
	public function verifyMeta($token, $payload = []) {
		$endpoint = 'album/verify/meta?token=' . urlencode($token);
		return $this->makeRequest('GET', $endpoint, $payload);
	}

    private function makeRequest($method, $endpoint, $data = []) {
        
		$url = $this->base_url . $endpoint;        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->token
        ];

       // print_r(json_encode($data));
         // exit;
        $ch = curl_init();        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
		if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
		
        $response = curl_exec($ch);

         //print_r($response);
         // exit;

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception('CURL error: ' . $error_msg);
        }        
        curl_close($ch);
        $decoded_response = json_decode($response, true);        
        if ($http_code >= 400) {
            $error_msg = $decoded_response['message'] ?? 'Unknown API error';
            throw new Exception('API error: ' . $error_msg . ' (HTTP ' . $http_code . ')');
        }
        return $decoded_response;
    }
	
	public function makeAlbumRequest($method, $endpoint, $file_path) {
		
		$ch = curl_init();
		curl_setopt_array($ch, array(
		  CURLOPT_URL => $endpoint,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => $method,
		  CURLOPT_POSTFIELDS => $file_path,
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: image/jpeg'
		  ),
		   CURLOPT_SSL_VERIFYPEER => false,
           CURLOPT_SSL_VERIFYHOST => 0
		));
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);        
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception('CURL error: ' . $error_msg);
        } 
		return $http_code;       
        curl_close($ch);
	}
}