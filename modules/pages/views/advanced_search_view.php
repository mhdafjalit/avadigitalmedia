<?php $this->load->view('top_application',array('has_header'=>false,'ws_page'=>'refer_fr','is_popup'=>true,'has_body_style'=>'padding:0'));?>
  <div class="p-3">
    <h1><?php echo $heading_title;?></h1>
    <?php echo error_message();
    echo form_open('pages/advanced_search','role="form"');?>
    <div class="mt-2">
      <input name="subscriber_name" id="subscriber_name" type="text" placeholder="Name *" class="p-2 w-100 border" value="<?php echo set_value('subscriber_name');?>"><?php echo form_error('subscriber_name');?>
      <div class="suggestionsBox" id="suggestions" style="display:none; width:310px; border:1px solid red;">
        <div class="suggestionList" id="autoSuggestionsList"></div>
      </div>
    </div>
    <p class="mt-1">
      <select name="" id="" class="p-2 w-100 border">
        <option>Select Category</option>
      </select>
    </p>
    <p class="mt-2">
      <input name="" type="button" class="more_btn" value="Search" onclick="window.open('search-result.htm','_parent');">
    </p>
    <?php echo form_close();?>
  </div>
</body>
</html>