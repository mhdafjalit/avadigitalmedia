<?php
if(is_array($res_questions) && !empty($res_questions)){
	$total_sections = count($res_questions);
	$sl_section=1;
?>
	<div style="background:#f7f7f7;font:13px 'Open Sans', sans-serif; color:#333; margin:0px; padding:0; width:100%; position:relative;">
		<div style="padding:15px; box-shadow:#ccc 0 0 5px; background:#fff; font-weight:bold; font-size:14px;"><?php echo $res_mt['mt_title'];?></div>
		<?php foreach($res_questions as $val1){
				$qsl=0;
				$total_questions = count($val1['questions']);
			?>
			<div style="padding:15px 0 10px 0; font-size:16px; font-weight:bold; text-align:center;"><?php echo $val1['section_title'];?></div>
			<div style="font-size:14px; font-weight:bold; text-align:center;"><?php echo $val1['subject_name'];?></div>
			<?php 
			foreach($val1['questions'] as $val2){
					$qsl++;
					$val2['question'] = preg_replace('/(<p[^>]*>|<\/p>)/i', '', $val2['question']); 
					$val2['question'] = preg_replace('/style="([^"]*)"/', '', $val2['question']);
				?>
				<div style="padding:25px 15px; background:#fff; margin:15px 12px; position:relative; border-radius:10px;box-shadow: 0px 0px 5px #ddd;">
					<div style="font-weight:bold;font-size:14px; padding-bottom:25px; border-bottom:#ddd 1px solid;"><?php echo $qsl.'. '.$val2['question'];?></div>
					<?php 
					$ix=0; 
					foreach($val2['options'] as $val3){
						$ix++;
						$loop_option_title = get_qoption_title(array('index'=>$ix,'prefix'=>'','alphabet'=>TRUE));
						$val3['option_text'] = preg_replace('/(<p[^>]*>|<\/p>)/i', '', $val3['option_text']); 
						$val3['option_text'] = preg_replace('/style="(.+)"/', '', $val3['option_text']);
					?>
					<div style="margin-top:20px;"><?php echo $loop_option_title.'). '. $val3['option_text'];?></div>
					<?php }?>
				</div>
				<?php 
				$solution_text_visible = strip_tags($val2['solution'], '<img>');
				$solution_text_visible = trim($solution_text_visible);
				if($solution_text_visible!=''){
					$val2['solution'] = preg_replace('/style="(.+)"/', '', $val2['solution']);
				?>
				<div style="padding:25px 15px; background:#fff; margin:10px 12px; position:relative; border-radius:10px;box-shadow: 0px 0px 5px #ddd;">
					<div style="font-weight:bold;">Answer</div>
					<?php echo $val2['solution'];?>
				</div>
		<?php
				}
				if(!empty($excel_mode) && ($sl_section!=$total_sections || ($sl_section==$total_sections && $total_questions!=$qsl))){
					echo '<pagebreak />';
				}
			}
			$sl_section++;
		}
		?>
	</div>
<?php
}
