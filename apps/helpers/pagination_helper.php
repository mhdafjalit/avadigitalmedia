<?php defined('BASEPATH') OR exit('No direct script access allowed.');

/**
  * The Pagination helper cuts out some of the bumf of normal pagination
  * @author		Philip Sturgeon
  * @filename	pagination_helper.php
  * @title		Pagination Helper
  * @version	1.0
 **/

 function front_pagination($opts=array()){
 
	$ci = CI();
	$ci->load->library('Front_pagination');

	$opts = !is_array($opts) ? array() : $opts;

	$total_recs =!empty($opts['total_recs']) ? (int) $opts['total_recs'] : 0;

	$per_page =!empty($opts['per_page']) ? (int) $opts['per_page'] : 0;
	$per_page = $per_page<=0 ? $ci->config->item('per_page')  : $per_page;

	$base_uri =!empty($opts['base_link']) ? $opts['base_link'] : '';

	$uri_segment =!empty($opts['uri_segment']) ? (int) $opts['uri_segment'] : 0;
	$uri_segment = $uri_segment<0 ? 0  : $uri_segment;

	$refresh =!empty($opts['refresh']) && $opts['refresh']==1 ? 1 : 0;

	$data_parent =!empty($opts['data_parent']) ? $opts['data_parent'] : "#my_data";

	$data_form =!empty($opts['data_form']) ? $opts['data_form'] : "#myform";


	/* Initialize pagination */
	$config['per_page']			 = $per_page;
	$config['base_url']          = $base_uri;
	$config['total_rows']			= $total_recs; 
	$config['uri_segment']			= $uri_segment;
	$config['page_query_string']	= FALSE;
	$config['refresh']				= $refresh;
	$config['div']                  = $data_parent;
	$config['data_form']                  = $data_form;	
	$ci->front_pagination->initialize($config);
	$data = $ci->front_pagination->create_links();
	return $data;
}


function front_record_per_page($per_page_id,$name='per_page')
{	
   $ci = CI();
   $post_per_page =  $ci->input->get_post($name);

?>
    <select  name="<?php echo $name;?>" id="<?php echo $per_page_id;?>" class="bg-light border ms-1 rounded-2 p-1" onchange="this.form.submit();">
    <?php
    foreach($ci->config->item('frontPageOpt') as $val)
    {
    ?>
    <option value="<?php echo $val;?>" <?php echo $post_per_page==$val ? "selected" : "";?>>
	  <?php echo $val;?></option>
    <?php
    }
    ?>
</select>

<?php
}
?>