<?php $this->load->view('includes/header'); ?>  
   
<div class="content">
    <div id="content">
  <div class="breadcrumb_sitepanel">
       <?php echo anchor('sitepanel/dashbord','Home'); ?>
        &raquo; <?php echo anchor('sitepanel/category'.($parent_id==0 ? '' : '/index/'.$parent_id),'Back To Listing'); ?> &raquo; <?php echo $heading_title; ?> </a>
        
      </div>
      
      <div class="box">
    <div class="heading">
      <h1><img src="<?php echo base_url(); ?>assets/sitepanel/image/category.png" alt="" /> <?php echo $heading_title; ?></h1>
      
      <div class="buttons"><?php echo anchor("sitepanel/category/",'<span>Cancel</span>','class="button" ' );?></div>
      
    </div>
    <div class="content">
    	    
	<?php echo validation_message('alert');?>
    <?php echo error_message(); ?>  
    
	<?php echo form_open_multipart("sitepanel/category/add/".$parent_id);?>  
		<div id="tab_pinfo">
			<table width="90%"  class="form"  cellpadding="3" cellspacing="3">
			<tr>
				<th colspan="2" align="center" > </th>
			</tr>
			<?php
			$default_params = array(
								'heading_element' => array(
														  'field_heading'=>"Name",
														  'field_name'=>"category_name",
														  'field_placeholder'=>"Your Catgeory Name",
														  'exparams' => 'size="40"'
														),
								'url_element'  => array(
														  'field_heading'=>"Page URL",
														  'field_name'=>"friendly_url",
														  'field_placeholder'=>"Your Page URL",
														  'exparams' => 'size="40"',
														  'pre_seo_url' =>'',
														  'pre_url_tag'=>FALSE
													   )

							  );

			if(is_array($parentData))
			{
			  $pre_seo_url  = base_url().$parentData['friendly_url']."/";
			  $default_params['url_element']['pre_seo_url'] = $pre_seo_url;
			  $default_params['url_element']['pre_url_tag'] = TRUE;
			  $default_params['url_element']['exparams'] = 'size="30"';
			}
			
			seo_add_form_element($default_params);
			
			
			?>





			<!--tr class="trOdd">
				<td width="28%" height="26" align="right" ><span class="required">*</span> Name:</td>
				<td width="72%" align="left"><input type="text" name="category_name" size="40" value="<?php echo set_value('category_name');?>"></td>
			</tr-->
			
			<tr class="trOdd">
				<td width="28%" height="26" align="right" >Image :</td>
				<td align="left">
					<input type="file" name="category_image" />
					<br />
                    <br />
					[ <?php echo $this->config->item('category.best.image.view');?> ]
				</td>
			</tr>
            <tr class="trOdd">
				<td width="28%" height="26" align="right" >Alt :</td>
				<td align="left">
					<input type="text" name="category_alt" value="<?php echo set_value('category_alt');?>" />
					<br />
				</td>
			</tr>
            <tr class="trOdd">
                <td height="26" align="right"> Description :</td>
                <td>
               <textarea name="category_description" rows="5" cols="50" id="cat_desc" ><?php echo $this->input->post('category_description');?></textarea> <?php  echo display_ckeditor($ckeditor); ?>
                </td>
            </tr>
        
			<tr class="trOdd">
				<td align="left">&nbsp;</td>
				<td align="left">
					<input type="submit" name="sub" value="Add" class="button2" />
					<input type="hidden" name="action" value="addcategory" />
                    
					<?php
					if(is_array($parentData))
					{
					?>
						<input type="hidden" name="parent_id" value="<?php echo $parentData['category_id'];?>" />
                        
					<?php
					}
					?>
				</td>
			</tr>
			</table>
		</div>
	<?php echo form_close(); ?>
	</div>
</div>

<?php $this->load->view('includes/footer'); ?>