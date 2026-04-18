function get_formatted_time_obj(num_seconds) {
 var d =0, h=0, m=0, s=0;  
  m = Math.floor(num_seconds / 60);
  s = Math.floor(num_seconds % 60);
 
  h = Math.floor(m / 60);
  m = Math.floor(m % 60);
  
  d = Math.floor(h / 24);
  h = Math.floor(h % 24);
  
	if(s<10) s = '0'+s;
	if(m<10) m = '0'+m;
	if(h<10) h = '0'+h;
	if(d<10) d = '0'+d;  
	
  return { d: d, h: h, m: m, s: s };
};

$.fn.count_down = function(options)
{
	var defaults = {'format':'simple','final_date':null,'current_date':null};
	var param_options = $.extend(defaults,options);
	
	if(!param_options.final_date) return false;
	if(!param_options.current_date) return false;
	
	var timer_obj = this;
	timer_obj.final_date = parseInt(param_options.final_date);
	timer_obj.current_date = parseInt(param_options.current_date);
	
	var formatted_output = '';
	var tm_format_obj = new Object();
	
	timer_obj.intv = setInterval(function(){
		if(timer_obj.final_date > timer_obj.current_date)
		{
			var diff = (timer_obj.final_date - timer_obj.current_date);
			
			tm_format_obj = get_formatted_time_obj(diff);
			
			
			if(tm_format_obj.d =='00')
			{
				formatted_output = ''+tm_format_obj.h+'H:'+tm_format_obj.m+'M:'+tm_format_obj.s+'S';
				
			}
			else
			{
				formatted_output =''+tm_format_obj.d+'D:'+tm_format_obj.h+'H:'+tm_format_obj.m+'M:'+tm_format_obj.s+'S';
				
			}
			
			timer_obj.html(formatted_output);
			
			timer_obj.current_date = timer_obj.current_date + 1;
		}
		else
		{
			formatted_output = '<span class="ac">Expired</span>';
			timer_obj.html(formatted_output);
			clearInterval(timer_obj.intv);
			
		}
		
		
	},1000);
	return timer_obj;

}