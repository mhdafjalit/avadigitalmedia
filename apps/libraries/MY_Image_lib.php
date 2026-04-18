<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MY_Image_lib extension
 *
 * @author: Stephen Cozart
 * @link: http://www.wired4tech.com
 * @contact: stephen.cozart@gmail.com
 * @credits: The folks @ EllisLab and of course the CI team for providing this fantastic framework
 * @License Agreement:  
 *Copyright (c) 2009, Wired4tech
 *All rights reserved.

 */
 

class MY_Image_lib extends CI_Image_lib {
	
	
	public $ar_width;
	public $ar_height;
	public function __construct() 
	{
		parent::__construct();		
		
	}
	
	/**
	 * Image Resize then Crop Using GD/GD2
	 *
	 * This function will resize then crop the resized image
	 *
	 * @access	public
	 * @return	bool
	 */	
	 
	public function adaptive_resize()
	{
		$v2_override = FALSE;

		// GD 2.0 has a cropping bug so we'll test for it
		if ($this->gd_version() !== FALSE)
		{
			$gd_version = str_replace('0', '', $this->gd_version());
			$v2_override = ($gd_version == 2) ? TRUE : FALSE;
		}
		
		//  Create the image handle
		if ( ! ($src_img = $this->image_create_gd()))
		{
			return FALSE;
		}
		//  if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor') AND $v2_override == FALSE)
 		if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor'))
		{
			$create	= 'imagecreatetruecolor';
			$copy	= 'imagecopyresampled';
		}
		else
		{
			$create	= 'imagecreate';
			$copy	= 'imagecopyresized';
		}
		$cropX = 0;
		$cropY = 0;
		
		if($this->width < $this->ar_width)
		{	
			$diff = $this->ar_width - $this->width;
			$this->width = $this->ar_width;
			$this->height = $this->height + $diff;
		}
		
		if($this->height < $this->ar_height)
		{
			$diff = $this->ar_height - $this->height;
			$this->height = $this->ar_height;
			$this->width = $this->width + $diff;
		}
		
		// now, figure out how to crop the rest of the image...
		if ($this->width > $this->ar_width)
		{
			$cropX = intval(($this->width - $this->ar_width) / 2);
		}
		elseif ($this->height > $this->ar_height)
		{
			$cropY = intval(($this->height - $this->ar_height) / 2);
		}
		
		//resize the image before cropping
		$rs_img = $create($this->width, $this->height);		
		$copy($rs_img, $src_img, 0, 0, 0, 0, $this->width, $this->height, $this->orig_width, $this->orig_height);
		
		$dst_img = $create($this->ar_width, $this->ar_height);
		
		if ($this->image_type == 3) // png we can actually preserve transparency
		{
			imagealphablending($dst_img, FALSE);
			imagesavealpha($dst_img, TRUE);
		}
		$copy($dst_img, $rs_img, 0, 0, $cropX, $cropY, $this->ar_width, $this->ar_height, $this->ar_width, $this->ar_height);

		//  Show the image
		if ($this->dynamic_output == TRUE)
		{
			$this->image_display_gd($dst_img);
		}
		else
		{
			// Or save it
			if ( ! $this->image_save_gd($dst_img))
			{
				return FALSE;
			}
		}

		//  Kill the file handles
		imagedestroy($dst_img);
		imagedestroy($src_img);
		imagedestroy($rs_img);

		// Set the file to 777
		@chmod($this->full_dst_path, DIR_WRITE_MODE);

		return TRUE;
    }
	
	
    public function image_create_gd($path = '', $image_type = '')
    {
    if ($path == '')
        $path = $this->full_src_path;

    if ($image_type == '')
        $image_type = $this->image_type;


    switch ($image_type)
    {
        case     1 :
                    if ( ! function_exists('imagecreatefromgif'))
                    {
                        $this->set_error(array('imglib_unsupported_imagecreate', 'imglib_gif_not_supported'));
                        return FALSE;
                    }

                    return @imagecreatefromgif($path);
            break;
        case 2 :
                    if ( ! function_exists('imagecreatefromjpeg'))
                    {
                        $this->set_error(array('imglib_unsupported_imagecreate', 'imglib_jpg_not_supported'));
                        return FALSE;
                    }

                    return @imagecreatefromjpeg($path);
            break;
        case 3 :
                    if ( ! function_exists('imagecreatefrompng'))
                    {
                        $this->set_error(array('imglib_unsupported_imagecreate', 'imglib_png_not_supported'));
                        return FALSE;
                    }

                    return @imagecreatefrompng($path);
            break;

    }

    $this->set_error(array('imglib_unsupported_imagecreate'));
    return FALSE;
 }


}

/* End of file MY_Image_lib.php */
/* Location: ./system/application/libraries/MY_Image_lib.php */