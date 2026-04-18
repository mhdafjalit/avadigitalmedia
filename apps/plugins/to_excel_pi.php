<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
/*
 * Excel library for Code Igniter applications
 * Author: Derek Allard, Dark Horse Consulting, www.darkhorse.to, April 2006
 */

function to_excel($fld_arr, $query, $filename = 'member_list') {
  $headers = ''; // just creating the var for field headers to append to below
  $data = ''; // just creating the var for field data to append to below

  $obj = & get_instance();

  //$fields = $query->field_data();
  //$fields = $obj->db->field_data('tbl_member');

  if ($query->num_rows() == 0) {
    echo '<p>The table appears to have no data.</p>';
  } else {
    foreach ($fld_arr as $value) {
      $headers .= "" . $value . "\t";
    }

    foreach ($query->result() as $row) {
      $line = '';

      foreach ($row as $key => $value) {
        if ((!isset($value)) OR ($value == "")) {
          $value = "\t";
        } else {
          $value = str_replace('"', '""', $value);
          if ($key == "country") {
            $value = "United Arab Emirates";

            $value = '"' . $value . '"' . "\t";
          } elseif ($key == "city") {
            $cityname = get_single_field_by_field("tbl_city", "city_name", array('id' => $value));
            $value = '"' . $cityname . '"' . "\t";
          } else {
            $value = '"' . $value . '"' . "\t";
          }
        }
        $line .= $value;
      }
      $data .= trim($line) . "\n";
    }

    $data = str_replace("\r", "", $data);

    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=$filename.xls");
    echo "$headers\n$data";
  }
}

function to_excel_newsletter($fld_arr, $query, $filename = 'newsletterlist') 
{
  $headers = ''; 
  $data = ''; 

  $obj = & get_instance();

  if ($query->num_rows() == 0) {
    echo '<p>The table appears to have no data.</p>';
  } else {
    foreach ($fld_arr as $value) {

      $headers .= "" . $value . "\t";
    }
    
    $gt_amount			=0;
    
    foreach ($query->result() as $row)
    {
	    $line = '';
		
      $row1=array();
      foreach($row as $k=>$v)
      {
	      if ($k == "subscr_name") {
		      $row1[$k]=($v!='')?$v:'-';
		    }
		    if ($k == "subscr_email") {
		      $row1[$k]=($v!='')?$v:'-';
		    }
		    
			}
     
      foreach ($row1 as $key => $value) {
        if ((!isset($value)) OR ($value == ""))
        {
	        $value = "\t";
	      }
	      else
	      {
		      $value = '"' . $value . '"' . "\t";
		    }
		    $line .= $value;
      }
      
      $data .= trim($line) . "\n";
    }
    
    $data .="\n";    

    $data = str_replace("\r", "", $data);

    header("Content-type: application/x-msdownload");
    header("Content-Disposition: attachment; filename=$filename.xls");
    echo "$headers\n$data";
  }
}




?>