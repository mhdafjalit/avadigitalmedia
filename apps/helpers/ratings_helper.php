<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('front_rating_html'))
{
	function front_rating_html($rating,$max_rating=5,$img_arr=array())
	{
	  if(!is_array($img_arr))
	  {
		$img_arr = array();
	  }
	  if(!array_key_exists('glow',$img_arr))
	  {
		$img_arr['glow'] = '<span class="fas fa-star"></span>';
	  }
	  if(!array_key_exists('fade',$img_arr))
	  {
		$img_arr['fade'] = '<span class="far fa-star"></span>';
	  }
	  $rating = ceil($rating);
	  $rating = $rating > $max_rating ? $max_rfating : $rating;
	  $var = "";
	  $nostar = $max_rating - $rating;

	  for($jx=1;$jx<=$rating;$jx++)
	  {
		$var.=$img_arr['glow'];
	  }

	  for($jx=1;$jx<=$nostar;$jx++)
	  {
		$var.=$img_arr['fade'];
	  }

	  return $var;
	}
}