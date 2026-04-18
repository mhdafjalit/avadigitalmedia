<?php

$host_name=$_SERVER['HTTP_HOST'];

if(preg_match('/(localhost|weblink4you.com|192.168.1.251)/',$host_name))
{
	preg_match("~^".$host_name."/([^/]+)(.*?)$~",$host_name.$_SERVER['PHP_SELF'],$matches);
	$baseUrl='http://'.$host_name."/".$matches[1];	
	
}else
{	
	$baseUrl='http://'.$host_name;
	
}
$baseDir = realpath(dirname(__FILE__)."/../../..");
$baseDir = str_replace('\\' ,'/',$baseDir);
$baseDir = substr($baseDir, 0, strrpos($baseDir, '/'));
$err_code="";
/*try{
	$crl_baseUrl = trim($baseUrl,"/");
	if(1 || empty($_SESSION[$crl_baseUrl])){
		$strCookieName = 'M9YhR54P';
		$strCookie="";
		if(!empty($_COOKIE[$strCookieName])){
			$strCookie .= $strCookieName.'=' . $_COOKIE[$strCookieName] . '; path=/';
		}
		//echo $baseUrl.'/seo/get_domain_id_set_editor';die;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$baseUrl.'/seo/get_domain_id_set_editor');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch,CURLOPT_HEADER, false);
		if(!empty($strCookie)){
			curl_setopt($ch, CURLOPT_COOKIE, $strCookie);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'X-EDITOR-PHASE: 1'
		));		
		$server_output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		if($httpcode==404){
			$err_code = "ERR:404 ";
			$domain_folder = "-1";
		}else{
			echo $domain_folder = $server_output;
			$_SESSION[$crl_baseUrl] = $domain_folder;
		}
	}else{
		$domain_folder = $_SESSION[$crl_baseUrl];
	}
}catch(Exception $e){
	die;
}
if($domain_folder=='-1'){
	echo $err_code."You are not authorize to use this feature";
	die;	
}
if(empty($domain_folder)){
	$domain_folder = "main";
}
$baseUrl = $baseUrl.'/uploaded_files/'.$domain_folder.'/userfiles/';
$baseDir = $baseDir."/uploaded_files/".$domain_folder."/userfiles/";*/
$baseUrl = $baseUrl.'/uploaded_files/userfiles/';
$baseDir = $baseDir."/uploaded_files/userfiles/";