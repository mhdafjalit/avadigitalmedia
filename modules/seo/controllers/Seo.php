<?php

Class Seo extends CI_Controller

{

	public function __construct()

	{

		ob_start();

	    parent::__construct(); 

		$this->load->helper(array('xml','seo/seo'));		 		

		

	}		



     public  function sitemap()

     {



       
       $res= $this->db->query("select page_url from  wl_meta_tags ")->result_array();

			$data['result']='';

			if(is_array($res)&&!empty($res)) {

				$url='';

				$i=0;

				$res_array=array();

				foreach($res as $key=>$val) {

					

					$url=$val['page_url'];

					

					$res_array[$i]  = $url;	

				 $i++;

				}

			

			}

        

		$data['result'] = $res_array; 

		

		header("Content-Type: text/xml;charset=iso-8859-1");

        $this->load->view("seo/sitemap",$data);

     }

	 

	 public function rss_feed()

	 {

		

		    $base_url = base_url();
		    $data['encoding'] = 'utf-8';
	        $data['feed_name'] = $base_url;
	        $data['feed_url'] = $base_url;
	        $data['page_description'] = 'Welcome to '.$base_url.' feed page';
	        $data['page_language'] = 'en-us';
	        $data['creator_email'] = 'info@jewelsfiji.com';

			$result = array();

			$rwcont = $this->db->query("SELECT meta_title, 	page_url,meta_description FROM wl_meta_tags WHERE 1 LIMIT 0 , 1000")->result_array();

			if(is_array($rwcont) && count($rwcont) > 0 )
			{
			
				foreach($rwcont as $contVal)
				{
					$reclink = $contVal['page_url'];

					$result[] = array(
										'title'=>$contVal['page_url'],												'url'=>$reclink,
										'description'=>$contVal['meta_description']
									 );
				}
			}
						
	        $data['result'] = $result;
				
		    header("Content-Type: application/rss+xml");
	        $this->load->view('rss', $data);

		

	}

	

	public function create_seo_url()

	{

	  $msg_arr = array();

	  $rec_id = (int) $this->input->post('rec_id');

	  $pg_title = $this->input->post('title',TRUE);

	  $pg_title = str_replace(base_url(),"",$pg_title);

	  $pre_title = $this->input->post('pre_title',TRUE);

	  $pre_title = str_replace(base_url(),"",$pre_title);

	  $pg_title = seo_url_title($pg_title);

	  

	  if($pre_title!=''){

		  

		$friendly_url = $pre_title.$pg_title;

	  }

	  else

	  {

		$friendly_url = $pg_title;

	  }

	  $this->db->select('meta_id');

	  $this->db->from('wl_meta_tags');

	  $this->db->where('page_url',$friendly_url);

	  if($rec_id > 0)

	  {

		$this->db->where('entity_id !=',$rec_id);

	  }

	  $meta_qry = $this->db->get();



	  if($meta_qry->num_rows() > 0)

	  {

		  $msg_arr['error'] = 1;

		  $msg_arr['msg'] = 'URL already exists';

	  }

	  else

	  {

		$msg_arr['error'] = 0;

		$msg_arr['msg'] = 'URL passed';

	  }

	  $msg_arr['friendly_name'] = $pg_title;

	  echo json_encode($msg_arr);

	}

	public function create_listing_page_links()
	{
	  create_listing_page_meta();
	}

	

}