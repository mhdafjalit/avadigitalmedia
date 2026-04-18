<?php

class Safe_encrypt {

	var $skey 	= ""; // you can change it
    public function __construct($skey='SuPerEncKey_weblinkindia')
	{
		$this->skey=$skey;
	}

	

    public  function safe_b64encode($string) {

 

        $data = base64_encode($string);

        $data = str_replace(array('+','/','='),array('-','_',''),$data);

        return $data;

    }

 

	public function safe_b64decode($string) {

        $data = str_replace(array('-','_'),array('+','/'),$string);

        $mod4 = strlen($data) % 4;

        if ($mod4) {

            $data .= substr('====', $mod4);

        }

        return base64_decode($data);

    }

 

    public  function encode($value){ 
	    if(!$value){return false;}		
		return trim($this->encryptDecrypt($value, $this->skey, 'encrypt'));
    }

 

    public function decode($value){
        if(!$value){return false;}
		return trim($this->encryptDecrypt($value, $this->skey, 'decrypt'));
    }
	
	
public function encryptDecrypt($string, $secret_key, $action){
	 $encrypt_method = "AES-256-CBC";
    $secret_iv = 'AES256CBC'; // user define secret key
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16); // sha256 is hash_hmac_algo
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = $this->safe_b64encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt($this->safe_b64decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}
}



// End of the file