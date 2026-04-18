var gObj = gObj || {};
$.extend(gObj, {
  regex_mail: /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z])+$/,
  regex_vldname: /^[ a-zA-Z]+$/,
  regex_price: /^((\d+)|(\d*)\.(\d+))$/,
});
var gblBeforeSend = function(){};
if(typeof gObj.enable_csrf!=='undefined' && gObj.enable_csrf==1){
(function($){
	var token_name_obj = $('meta[name="csrf-token-name"]');
	var token_value_obj = $('meta[name="csrf-token-value"]');
	gblBeforeSend = function (xhr,settings) {
		var token_name = token_name_obj.attr('content');
		var token_value = token_value_obj.attr('content');
		if(settings.type=="POST" && token_name!='' && token_value!=''){
			if(settings.data instanceof FormData){
				settings.data.append(token_name,token_value);
			}else if(typeof settings.data==='object'){
				settings.data[token_name] = token_value;
			}else{
				settings.data = settings.data!='' ? settings.data+"&" : "";
				settings.data+=token_name+'='+token_value;
			}
		}
	}
	$( document ).ajaxComplete(function( event, xhr, settings ) {
		var token_name = xhr.getResponseHeader('X-TKN-KL');
		var token_value = xhr.getResponseHeader('X-TKV-KL');
		if(xhr.status==401){
			return;
		}
		if ( token_name_obj.length && token_value_obj.length && token_name && token_name!='' &&  token_value && token_value!=''  ) {
			token_name_obj.attr('content',token_name);
			token_value_obj.attr('content',token_value);
			$(':hidden[name="'+token_name+'"]').val(token_value);
		}
	});
	$.ajaxSetup({
		beforeSend: gblBeforeSend
	});
})(jQuery);
}

function isNumberKey(evt) {
  var charCode = evt.which ? evt.which : evt.keyCode;
  //console.log(charCode);
  if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
  return true;
}
function isPriceFormatKey(evt) {
  var charCode = evt.which ? evt.which : evt.keyCode;
  //console.log(charCode);
  if (charCode == 46 && /\./.test(evt.target.value)) {
    return false;
  }
  if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
    return false;

  return true;
}

function emptyIfNotValidPrice(evt){
	if (!gObj.regex_price.test(evt.target.value)) {
		evt.target.value = "";
	 }
}

function multisearch(srchkey, chkname) {
  var arrval = new Array();
  $("[name=" + chkname + "]:checked").each(function (mkey, mval) {
    arrval.push($(mval).val());
  });

  $("#" + srchkey).val(arrval.join(","));
  $("#srcfrm").submit();
}


(function ($, w) {
	
	function updateCartQty(e){
		e.preventDefault();
		var curobj = $(this);
		var parent_cont = curobj.parents('.zx_pro_cart_btn');
		var cur_qty_obj = parent_cont.find('.stqty');
		var cur_qty_val = cur_qty_obj.val();
		cur_qty_val = parseInt(cur_qty_val);
		var cur_next_qty_val = cur_qty_val;
		var action_creator = curobj.data('action');
		var check_stock = curobj.data('check-stock');
		check_stock = parseInt(check_stock);
		check_stock = typeof check_stock!=='undefined' ? (check_stock>0 ? 1 : 0) : 1;
		var cur_available_qty=0;
		var allow_zero_qty=false;
		if(check_stock){
			cur_available_qty = curobj.data('stock-avqty');
		} 
		cur_available_qty = parseInt(cur_available_qty);
		var err_qty_obj = parent_cont.find('.err_qty');
		if(err_qty_obj.length){
			err_qty_obj.html('');
		}
		switch(action_creator){
			case 'incr':
				cur_next_qty_val++;
				if(check_stock && cur_next_qty_val>cur_available_qty){
					err_qty_obj.html("Total available  "+cur_available_qty+" tickets");
				}else{
					cur_qty_obj.val(cur_next_qty_val);
				}
			break;
			case 'decr':
				allow_zero_qty = curobj.data('allow-zero-qty');
				allow_zero_qty = parseInt(allow_zero_qty);
				allow_zero_qty = typeof allow_zero_qty!=='undefined' ? (allow_zero_qty>0 ? 1 : 0) : 0;
				cur_next_qty_val--;
				if(cur_next_qty_val>0 || (allow_zero_qty && cur_next_qty_val==0)){
					cur_qty_obj.val(cur_next_qty_val);
				}
			break;
		}
	}

	
	
  w.validatePriceRange = function (e) {
    var min_price_obj = $(e.data.min_obj);
    var max_price_obj = $(e.data.max_obj);
    var min_price = min_price_obj.val();
    var max_price = max_price_obj.val();
    min_price = $.trim(min_price);
    min_price = min_price.replace(/\.$/, "");
    max_price = $.trim(max_price);
    max_price = max_price.replace(/\.$/, "");

    if (min_price != "" || max_price != "") {
      if (!gObj.regex_price.test(min_price)) {
        min_price = "";
      }
      if (!gObj.regex_price.test(max_price)) {
        max_price = "";
      }

      if (min_price != "" && max_price != "") {
        min_price = parseFloat(min_price);
        max_price = parseFloat(max_price);
        if (max_price < min_price) {
          max_price = max_price + min_price;
          min_price = max_price - min_price;
          max_price = max_price - min_price;
        }
      }
      //console.log(min_price+"==="+max_price);
      min_price_obj.val(min_price);
      max_price_obj.val(max_price);
    }
  };

	w.check_zip_location = function check_zip_location(){
		$(".errors_value").hide();
		var hasError = false;
		var locationVal = $('#zip_location').val();
		$("#location_error").html('');
		if(locationVal == ''){
			$('#location_error').html('<span class="red mt5 loc_err">Please enter delivery location.</span>');
			$("#zip_location").focus();
			hasError = true;
		}
		if(hasError == true) { return false; }
		else{
			$("#location_loader").show();
			$('#location_loader').html('<img src="'+gObj.base_url+'assets/developers/images/loader.gif"/>');
			term = $('input[name="zip_location"]').val();
			url= gObj.base_url+'products/ajax_search_zip_location/';
			$.post(url,{zip_location: term },
			function(data){
			$("#location_error").html('<span class="">'+data+'</span>');
			$("#location_loader").hide();
			$("#zip_location").val('');
			});
		}
		return false;
	};


  $(document).ready(function () {

	  /*$( document ).ajaxComplete(function( event, xhr, settings ) {
			var ix_data;
			if ( typeof settings.headers  !== "undefined" && typeof settings.headers.XRSP  !== "undefined" && settings.headers.XRSP=='json' ) {
				ix_data = JSON.parse(xhr.responseText);
				if(xhr.status==401 && typeof ix_data.is_logout!=='undefined' &&  ix_data.is_logout==1){
					alert("Your session has been expired");
					setTimeout(function(){ top.location.href=gObj.base_url;},500);
				}
			}
		});*/

	  function bindFbox(){
		var ce_fbox_attempt=0;
		function bindAfterReady(){
			$(".dg2").fancybox({
				'autoScale': false,
				'type': 'image'
			});
			$('.pop_x').click(function(e){
				e.preventDefault();
				var cobj =$(this);
				var dimension_obj = {};
				dimension_obj['width']=cobj.data('width') || 800;
				if(cobj.data('height')){
					dimension_obj['height']=cobj.data('height');
				}
				$.fancybox.open({
						src:cobj.data('src'),
						type : 'iframe',
						clickOutside : false,
						clickSlide:false,
						touch: false     
					},
					{
					buttons:[],
					iframe:{
									css:dimension_obj
								}
				});
			});
		}
		var sifl = setInterval(function(cbk){
				if(ce_fbox_attempt>20){
					/*console.log("Failed to bind popup");*/
					clearInterval(sifl);	
				}
				if(typeof $.fn.fancybox==='function'){
					/*console.log("Binded popup");*/
					clearInterval(sifl);	
					cbk();
				}else{
					ce_fbox_attempt++;
				}
		},100,bindAfterReady);
	}
	if( typeof gObj.usefbox!=='undefined' && gObj.usefbox==1){
		bindFbox();
	}
	  
	  $(document).on('click','.qty_action_creator',updateCartQty);
	  
    $(".captcha_refresh").click(function (e) {
      e.preventDefault();
      var cobj = $(this);
      var data_cont = cobj.data("cont");
      var data_src = cobj.data("src");
      var ref_cont_obj = data_cont ? $(data_cont) : "";
      if (data_src && ref_cont_obj.length && ref_cont_obj.is("img")) {
        ref_cont_obj.attr("src", data_src + "/" + new Date().getTime());
      }
    });
    
    function handleFileDownload(props){
		var lobj=$(this);
		var props = props || {};
		var action = props.action || lobj.data('href');
		var queryString = action.split('?');
		action = queryString[0]+'?'+(!queryString[1] ?  '' : queryString[1]+'&')+"v="+Math.floor(Math.random()*100-1);
		var frm_data = props.frm_data || {};
		var model_obj = $('#download-modal');
		if(typeof props.modal_title!=='undefined' && props.modal_title!=''){
			model_obj.find('.modal-title').html(props.modal_title);
		}
		if(typeof props.modal_body!=='undefined' && props.modal_body!=''){
			model_obj.find('.modal-body').html(props.modal_body);
		}
		var model_body_obj = model_obj.find('.modal-body');
		var old_html = model_body_obj.html();

		model_obj.modal('show');
		$('.error_msg').html('');
		var xhr = new XMLHttpRequest();
		xhr.open('POST', action, true);
		xhr.responseType = 'blob';
		xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
		xhr.setRequestHeader('XRSP', 'json');
		xhr.timeout = 300000;
		xhr.ontimeout = function (e) {
			model_body_obj.html('<div class="text-center"><span class="red">Oops!! Server Timeout</span> <br><button class="btn btn-primary mt-2" id="tmout_btn">Close</button></div>');
			$('#tmout_btn').click(function(e){
				e.preventDefault();
				model_obj.modal('hide');
				setTimeout(function(){ model_body_obj.html(old_html);},300);
			});
		}
		xhr.onload = function(e) {
			if (this.status == 200) {
				var filename = "";                   
				var disposition = xhr.getResponseHeader('Content-Disposition');
				if (disposition) {
					var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
					var matches = filenameRegex.exec(disposition);
					if (matches !== null && matches[1]) filename = matches[1].replace(/['"]/g, '');
				} 
				var blob = new Blob([this.response], {type: 'application/octet-stream'});
				var downloadUrl = URL.createObjectURL(blob);
				var a = document.createElement("a");
				a.href = downloadUrl;
				a.download = filename;
				document.body.appendChild(a);
				a.click();
				setTimeout(function(){  a.parentNode.removeChild(a); },5000);
			}else if (this.status == 401) {
				$('.error_msg').html("Your session has been expired.");
				setTimeout(function(){ top.location.href=gObj.base_url;},500);
			}else {
				$('.error_msg').html("Error downloading file");
			}
			model_obj.modal('hide');
			setTimeout(function(){model_obj.modal('hide');},400);
		};
		xhr.send(JSON.stringify(frm_data));
	}
	w.handleFileDownload = handleFileDownload;

	/*Cart Utility*/
	var ajx_cartp;
	function updateCart(params){
		var ajx_params = $.extend({
						beforeSend: function (jqXHR, settings) {
							if (ajx_cartp) {
							ajx_cartp.abort();
						  }
						},
						contentType: false,
						processData: false
					},params['ajx']);
		var model_obj = $("#default-site-modal");
		model_obj.find(".modal-title").html("");
		model_obj.find(".modal-body").html("Updating....");
		model_obj.modal("show");
		var pgtype = $('#crt_container').data('pgtype');
		pgtype = typeof pgtype==='undefined' ? "" : pgtype;
		$.ajax(ajx_params).done(function(res){
			var data = JSON.parse(res);
			var item_length = 0;
			if(data.status==1){
				$('#crt_container').html(data.cart_data);
				item_length = $('#crt_container').find('.zx_pro_cart_btn').length;
				item_length_str = item_length<10 ? "0"+item_length : item_length;
				$('.cart_count_cont').html(item_length_str);
				if(pgtype=='checkout' && !item_length){
					location.href=gObj.base_url+'cart';
				}
			}
		}).fail(function(){}).always(function(){
			setTimeout(function () {
				model_obj.modal("hide");
			  }, 400);
		});
	}
	$('#crt_container').on('click','.updt_qty_lk',function(e){
		e.preventDefault();
		var cart_frm_obj = document.getElementById('cart_frm');
		var frmObj = new FormData(cart_frm_obj);
		var cart_params = {'ajx':{
							type: "POST",
							url: gObj.base_url+'cart/update_cart_qty',
							data: frmObj
						}
					};
		updateCart(cart_params);
	});

	$('#crt_container').on('click','.rm_cart_item',function(e){
		e.preventDefault();
		var cfm_cart = confirm("Are you sure you want to remove this item?");
		var cart_params;
		var cobj = $(this);
		var item_id = cobj.data('id');
		var pgtype = $('#crt_container').data('pgtype');
		pgtype = typeof pgtype==='undefined' ? "" : pgtype;
		var frmObj;
		if(cfm_cart){
			frmObj = new FormData();
			frmObj.append('pgtype',pgtype);
			cart_params = {'ajx':{
											type: "POST",
											url: gObj.base_url+'cart/remove_item/'+item_id,
											data:frmObj
										}
									};
			updateCart(cart_params);
		}
	});

	$('#crt_container').on('submit','#cpn_frm',function(e){
		e.preventDefault();
		var frmObj =  new FormData(this);
		var cpn_code_obj = $('#coupon_code');
		var cpn_code_val = cpn_code_obj.val();
		cpn_code_val = $.trim(cpn_code_val);
		var err_cpn_obj = $('#err_coupon_code');
		err_cpn_obj.html("");
		var cntr_obj = $('#crt_container');
		var sbt_obj = cntr_obj.find('#btn_apply_cpn');
		if(cpn_code_val==''){
			err_cpn_obj.html("Please enter coupon code");
			return;
		}
		var pgtype = cntr_obj.data('pgtype');
		pgtype = typeof pgtype==='undefined' ? "" : pgtype;
		cntr_obj.addClass('overlay_enable');
		sbt_obj.val(sbt_obj.data("progress-text"));
		frmObj.append('btn_apply_cpn','Y');
		frmObj.append('pgtype',pgtype);
		var ajx_params = {
						type: "POST",
						url: gObj.base_url+'cart',
						data: frmObj,
						beforeSend: function (jqXHR, settings) {
							
						},
						contentType: false,
						processData: false
					}
		$.ajax(ajx_params).done(function(res){
			var data = JSON.parse(res);
			var item_length = 0;
			cntr_obj.html(data.cart_data);
			item_length = cntr_obj.find('.zx_pro_cart_btn').length;
			item_length_str = item_length<10 ? "0"+item_length : item_length;
			$('.cart_count_cont').html(item_length_str);
			if(pgtype=='checkout' && !item_length){
				location.href=gObj.base_url+'cart';
			}else{
				err_cpn_obj = $('#err_coupon_code');
				if(data.status==1){
					err_cpn_obj.html(data.msg);
					setTimeout(function(){ err_cpn_obj.html(""); },5000);
				}else{
					err_cpn_obj.html(data.msg);
				}
			}
		}).fail(function(){}).always(function(){
			cntr_obj.removeClass('overlay_enable');
			sbt_obj.val(sbt_obj.data("actual-value"));
		});
	});

	$('#crt_container').on('click','#rm_cpn',function(e){
		e.preventDefault();
		var cfm = confirm("Are you sure that you want to remove the coupon?");
		var err_cpn_obj = $('#err_coupon_code');
		err_cpn_obj.html("");
		var cntr_obj = $('#crt_container');
		var pgtype = cntr_obj.data('pgtype');
		pgtype = typeof pgtype==='undefined' ? "" : pgtype;
		var frmData;
		if(cfm){
			cntr_obj.addClass('overlay_enable');
			frmData =  {pgtype:pgtype};
			 $.post(
				gObj.base_url + "cart/remove_coupon",
				frmData,
				"json"
			  ).done(function (data) {
					var data = JSON.parse(data);
					var item_length = 0;
					cntr_obj.html(data.cart_data);
					item_length = cntr_obj.find('.zx_pro_cart_btn').length;
					item_length_str = item_length<10 ? "0"+item_length : item_length;
					$('.cart_count_cont').html(item_length_str);
					if(pgtype=='checkout' && !item_length){
						location.href=gObj.base_url+'cart';
					}
			}) .fail(function (jqXHR, textStatus, errorThrown) {
			  err_cpn_obj.html('<div class="required" style="font-size:14px;">OOPS!! Something went wrong.</div>');
			}).always(function(){
				cntr_obj.removeClass('overlay_enable');
			});
		}
	});

	/*Cart Utility Ends*/

	/*Product Search Functionality*/
	$('#prod_srch_form').submit(function(e){
		e.preventDefault();
		var err_frm = 0;
		var err_msg = '';
		var cobj = $(this);
		var err_msg_obj = $('#err_prod_srch');
		err_msg_obj.html('');
		var keyword_obj = $('#prod_keyword2');
		var keyword_val = keyword_obj.val();
		keyword_val = $.trim(keyword_val);
		if(keyword_val==''){
			err_msg = "Please enter keyword";
			err_frm=1;
		}
		if(err_frm){
			err_msg_obj.html(err_msg);
			return false;
		}
		this.submit();
	});
	/*Product Search Functionality Ends*/
	

    $("#bottom_nletter_frm").submit(function (e) {
      e.preventDefault();
      var frmobj = $(this);
      var err = 0;
      var err_fld, err_msg;
      var msg_cont_obj = $("#msg_nletter_frm");
      var sbt_obj = frmobj.find('[name="btn_sbt"]');
      msg_cont_obj.html("");
      frmobj.find('input[type="text"]').each(function (m, n) {
        var fldname = n.name;
        var fldval = n.value;
        fldval = $.trim(fldval);
        switch (fldname) {
          case "subscriber_name":
            if (fldval == "") {
              err = 1;
              err_msg = "Name is required.";
            }
            break;
          case "subscriber_email":
            if (fldval == "") {
              err = 1;
              err_msg = "Email is required.";
            } else if (!gObj.regex_mail.test(fldval)) {
              err = 1;
              err_msg = "Please enter a valid email.";
            }
            break;
          case "verification_code":
            if (fldval == "") {
              err = 1;
              err_msg = "Please enter the code shown.";
            }
            break;
        }
        if (err) {
          err_fld = n;
          return false;
        }
      });
      if (err) {
        msg_cont_obj.html(
          '<div class="required" style="font-size:14px;">' + err_msg + "</div>"
        );
        err_fld.focus();
      } else {
        if (!frmobj.hasClass("process_x")) {
          frmobj.addClass("process_x");
          sbt_obj.val(sbt_obj.data("progress-text"));
          $.post(
            gObj.base_url + "pages/service_newsletter",
            frmobj.serialize(),
            "json"
          )
            .done(function (data) {
              var data = JSON.parse(data);
              if (data.status == 1) {
                msg_cont_obj.html(
                  '<div class="text-success" style="font-size:14px;">' +
                    data.msg +
                    "</div>"
                );
                if (frmobj.find(".captcha_refresh").length) {
                  frmobj.find(".captcha_refresh").click();
                }
                frmobj.find('input[type="text"]').val("");
              } else {
                if (typeof data.error_flds!=='undefined' && Object.keys(data.error_flds).length) {
                  err_msg = "";
                  $.each(data.error_flds, function (m, n) {
                    err_msg +=
                      '<div class="required" style="font-size:14px;">' +
                      n +
                      "</div>";
                  });
                }else{
                	err_msg =
                      '<div class="required" style="font-size:14px;">' + data.msg+"</div>";
                }
                msg_cont_obj.html(err_msg);
              }
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
              msg_cont_obj.html(
                '<div class="required" style="font-size:14px;">OOPS!! Something went wrong.</div>'
              );
            })
            .always(function () {
              frmobj.removeClass("process_x");
              sbt_obj.val(sbt_obj.data("actual-value"));
            });
        }
      }
    });
    /*Check All/Uncheck All*/
    $(".x_btn_action").on("click", function (e) {
      var cur_obj = $(this);
      var action_type = cur_obj.data("action-type");
      var msg = "",
        btnval = "";
      var frm_obj = this.form;
      var ckbx_obj = $(".lckb:checked", frm_obj);
      var cfm;
      if (!ckbx_obj.length) {
        alert("Please select at least one record.");
        return false;
      }
      switch (action_type) {
        case "activate":
          msg = "Are you sure you want to activate";
          btnval = "Activate";
          break;
        case "deactivate":
          msg = "Are you sure you want to deactivate";
          btnval = "Deactivate";
          break;
        case "delete":
          msg = "Are you sure you want to delete";
          btnval = "Delete";
          break;
      }
      cfm = confirm(msg);
      if (cfm) {
        return true;
      }
      return false;
    });
    $(".xrec_list").on("click", ".lckb", function (e) {
      var cur_obj = $(this);
      var parent_sel_all = cur_obj.data("parent-ckbox");
      var ckbobj_parent = $(".selall", parent_sel_all);
      var not_checked_obj;
      if (!cur_obj.prop("checked")) {
        if (ckbobj_parent.length && ckbobj_parent.prop("checked")) {
          ckbobj_parent.prop("checked", false);
        }
      } else {
        not_checked_obj = cur_obj
          .closest(".xrec_list")
          .find(".lckb:not(:checked)");
        if (
          !not_checked_obj.length &&
          ckbobj_parent.length &&
          !ckbobj_parent.prop("checked")
        ) {
          ckbobj_parent.prop("checked", true);
        }
      }
    });

    $(".selall").on("click", function (e) {
      var cur_obj = $(this);
      var parent_sel_all = cur_obj.data("parent");
      var parent_obj = cur_obj.parents(parent_sel_all);
      var child_obj;
      if (parent_obj.length) {
        child_obj = $("[data-parent-ckbox='" + parent_sel_all + "']")
          .closest(".xrec_list")
          .find(".lckb");
        if (child_obj.length) {
          child_obj.prop("checked", cur_obj.prop("checked"));
        }
      }
    });

   	/*By button All/Uncheck All Start*/
    $(".sel_all_btn,.usel_all_btn").on("click", function (e) {
      var cur_obj = $(this);
      var parent_obj = cur_obj.parents('.ref_root_ckbox_parent');
      if(cur_obj.hasClass('sel_all_btn')){
      	parent_obj.find('.xrec_list:not(:checked)').prop('checked',true);
      }else{
      	parent_obj.find('.xrec_list:checked').prop('checked',false);
      }
    });
    /*Check All/Uncheck All Ends*/
  });

  /*Pagination utility */
  /*$(document).on("click", ".cpg_link", function (e) {
    e.preventDefault();
    var link_obj = $(this);
    var pg_offset_val = link_obj.data("offset");
    pg_offset_val = pg_offset_val == "" ? 0 : pg_offset_val;
    var pg_refresh = link_obj.data("refresh");
    var link_href, data_form_obj, parent_reflector_raw, posted_data;
    if (pg_refresh == 1) {
      link_href = link_obj.attr("href");
      data_form_obj = link_obj.data("form");
      data_form_obj = $(data_form_obj);
      $('[name="offset"]', data_form_obj).val(pg_offset_val);
      data_form_obj.attr({
        action: link_href,
        method: "get",
      });
      data_form_obj.submit();
    } else {
      data_form_obj = link_obj.data("form");
      data_form_obj = $(data_form_obj);
      $('[name="offset"]', data_form_obj).val(pg_offset_val);
      parent_reflector_raw = link_obj.data("parent");
      parent_reflector = $(parent_reflector_raw);
      link_href = link_obj.attr("href");
      posted_data = data_form_obj.serialize();
      $.post(link_href, posted_data)
        .done(function (data) {
          parent_reflector.html(data);
          $("html,body").animate(
            {
              scrollTop: parent_reflector.offset().top - 200,
            },
            800
          );
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
          if (jqXHR.readyState == 0) {
            link_obj.data("refresh", 1);
            link_obj.click();
          }
        });
    }
  });*/
	$(document).on("click", ".cpg_link", function (e) {
    e.preventDefault();
    var link_obj = $(this);
    var pg_offset_val = link_obj.data("offset");
    pg_offset_val = pg_offset_val == "" ? 0 : pg_offset_val;
    var pg_refresh = link_obj.data("refresh");
    var link_href, data_form_obj, parent_reflector_raw, posted_data;
    if (pg_refresh == 1) {
      link_href = link_obj.attr("href");
      location.href= link_href;
    } else {
      parent_reflector_raw = link_obj.data("parent");
      parent_reflector = $(parent_reflector_raw);
      link_href = link_obj.attr("href");
      $.get(link_href)
        .done(function (data) {
          parent_reflector.html(data);
          $("html,body").animate(
            {
              scrollTop: parent_reflector.offset().top - 200,
            },
            800
          );
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
          if (jqXHR.readyState == 0) {
            link_obj.data("refresh", 1);
            link_obj.click();
          } /*console.log(jqXHR);*/
        });
    }
  });
  /*Pagination utility ends*/
  /*Scroll Pagination*/
  w.btxbindScrollPagination = function btxbindScrollPagination(obj) {
    var default_obj = {
      parentContainer: "#my_scroll_data",
      childContainer: ".listpager",
      dataForm: "#myform",
      loadingContainer: "#loadingdiv",
    };
    var obj = $.extend({}, default_obj, obj);
    var parent_obj = $(obj.parentContainer);
    var loading_obj = $(obj.loadingContainer);
    var data_fm_obj = $(obj.dataForm);
    var cbk = obj.callback;
    var ajx_scroll;
    var page = 1;
    var triggeredPaging = 0;
    function bindScroll() {
      var scrollTop = $(window).scrollTop();
      var scrollBottom = scrollTop + $(window).height();
      // alert(scrollTop+scrollBottom);
      var containerTop = parent_obj.offset().top;
      var containerHeight = parent_obj.height();
      var containerBottom = Math.floor(containerTop + containerHeight);
      var scrollBuffer = 0;
      var data_frm;
      var post_url = data_fm_obj.attr("action");
      var actual_count = 0;
      //console.log(containerTop+"=="+scrollTop+"==="+scrollBottom+"==="+containerBottom);
      if (containerBottom - scrollBuffer <= scrollBottom) {
        page = $(obj.childContainer, parent_obj).length;
        actual_count = data_fm_obj.data("total-rec");
        if (!triggeredPaging && page < actual_count) {
		  $(':hidden[name="offset"]', data_fm_obj).val(page);
          triggeredPaging = 1;
          data_frm = data_fm_obj.serialize();
          ajx_scroll = $.ajax({
            type: "POST",
            url: post_url,
            data: data_frm,
            error: function (res) {
              triggeredPaging = 0;
            },
            beforeSend: function (jqXHR, settings) {
              if (ajx_scroll) {
                ajx_scroll.abort();
              }
              if(settings.type!='GET'){
              	gblBeforeSend(jqXHR, settings);
              }
              loading_obj.show();
            },
            success: function (res) {
              loading_obj.hide();
              parent_obj.append(res);
              triggeredPaging = 0;
              page = $(obj.childContainer, parent_obj).length;
              if (page >= actual_count) {
                $(window).unbind("scroll", bindScroll);
              }
              if(typeof cbk=='function'){ cbk();}
              //console.log(res);
              $(obj.childContainer, parent_obj).fadeTo(500, 0.5, function () {
                $(this).fadeTo(100, 1.0);
              });
            },
          });
        }
      }
    }
    if (parent_obj.length) {
      $(window).scroll(bindScroll);
    }
  };
})(jQuery, window);


function validcheckstatus(name,action,text)
{
	var chObj	=	document.getElementsByName(name);
	var result	=	false;	
	for(var i=0;i<chObj.length;i++){
	
		if(chObj[i].checked){
		  result=true;
		  break;
		}
	}
 
	if(!result){
		 alert("Please select atleast one "+text+" to "+action+".");
		 return false;
	}else if(action=='delete'){
			 if(!confirm("Are you sure you want to delete this.")){
			   return false;
			 }else{
				return true;
			 }
	}else{
		return true;
	}
}

function showloader(id)
{
	$("#"+id).after("<span id='"+id+"_loader'><img src='"+gObj.base_url+"/assets/developers/images/loader.gif'/></span>");
}

function hideloader(id)
{
	$("#"+id+"_loader").remove();
}

function bind_data(parent_id,method_url,container_id,loader_container,from_section)
{
	showloader(loader_container);
	$("#"+container_id).hide();  
	var ajax_url=gObj.base_url+method_url;
	if(from_section=='neighborhood_country')
	{
		$('#city_list').html('<select name="city_id" style="width:235px;"><option value="">Select One</option></select>');
	}
	
	$.ajax({
			type: "POST",
			url: ajax_url,
			dataType: "html",
			data: "parent_id="+parent_id+"&from_section="+from_section,
			cache:false,
			success:
				function(data)
				{
					$("#"+container_id).show();
					$("#"+container_id).html(data);
					hideloader(loader_container);
					
				}
				
	}); 
}

function getQueryParameters(url) {
	var url = typeof url!='undefined' ? url : location.search;
    var queryString = url.slice(url.indexOf('?') + 1), params = {};
    queryString.replace(/([^=]*)=([^&]*)&*/g, function (_, key, value) {
        params[key] = value;
    });
    return params;
}


function utilityQueryParameters(params) {
	if(!params){
		params = {};
	}
	var url = typeof params['url']!='undefined' ? params['url'] : location.search;
	var query_params = getQueryParameters(url);
	var query_params_keys = Object.keys(query_params);
	var remove_params = [];
	var key,queryString;
	var path_name='';
	var return_type = typeof params['return_type']!='undefined' ? params['return_type'] : 2;
	if(typeof params['remove_qs']!='undefined'){
		if(Object.prototype.toString.call(params['remove_qs'])==='[object Array]'){
			remove_params = params['remove_qs'];
		}else if(Object.prototype.toString.call(params['remove_qs'])==='[object String]'){
			remove_params = [params['remove_qs']];
		}
		if(!remove_params.length){
			query_params_keys = [];
		}
	}
	if(remove_params.length && query_params_keys.length){
		for(var zx = 0;zx<remove_params.length;zx++){
			query_params = query_params_keys.filter(function(ele){
				return ele!=remove_params[zx];
			}).reduce(function(acc,ele_val,ele_key){
				acc[ele_val] = query_params[ele_val];
				return acc;
			},{});
		}
	}
	
	if(typeof params['append_qs']!=='undefined' && Object.prototype.toString.call(params['append_qs'])==='[object Object]'){
		for (key in params['append_qs']) {
			query_params[key] = params['append_qs'][key];
		}
	}
	if([2,3].indexOf(return_type)!=-1){
		queryString = Object.keys(query_params).map(function(ele_key) {
										return ele_key + '=' + query_params[ele_key]
									}).join('&');
		queryString = queryString!='' ? '?'+queryString : '';
	}
	if([3].indexOf(return_type)!=-1){
		path_name = typeof params['url']!='undefined' ? /([^\?]+)(\?.*)?/g.exec(params['url'])[1] : (window.location.protocol +"//" +window.location.host +window.location.pathname);
	}
	switch(return_type){
		case 1:
			return query_params;
		break;
		case 2:
			return queryString;
		break;
		case 3:
		default:
			path_name = path_name+queryString;
			return path_name;
		break;
	}
}

function zx_getNested(obj) {
  var args = [].slice.call(arguments,1);
  return args.reduce(function(obj, level){ return obj && obj[level];}, obj);
}

function zx_getDateByOffset(zx_param){
		var dt = zx_getNested(zx_param,'dt');
		var offset_type = zx_getNested(zx_param,'offset_type');
		var offset = zx_getNested(zx_param,'offset');
		var fmt = zx_getNested(zx_param,'fmt') || '';
		if(dt=='' || dt=='0000-00-00'){
			return "";
		}
		var dt_obj = dt=='current' ?  new Date() :   new Date(dt);
		var date,new_dt_str;
		var month = dt_obj.getMonth()+1;
		if(typeof offset_type!=='undefined' && offset_type=='last_day_of_month'){
			dt_obj = new Date( dt_obj.getFullYear(),(dt_obj.getMonth()+1),0);
			month = dt_obj.getMonth()+1;
		}else if(typeof offset_type!=='undefined' && offset_type=='day_of_next_month'){
			dt_obj.setDate(offset);
			dt_obj.setMonth(dt_obj.getMonth()+1);
			dt_obj = new Date( dt_obj);
			month = dt_obj.getMonth()+1;
		}else if(typeof offset!=='undefined' && offset>0){
			date = dt_obj.getDate() + offset;
			dt_obj.setDate(date);
			dt_obj = new Date( dt_obj);
			month = dt_obj.getMonth()+1;
		}
		switch(fmt){
			case '3':
			case 3:
				month = month<10 ? '0'+month : month;
				date = dt_obj.getDate();
				date = date<10 ? '0'+date : date;
				new_dt_str = dt_obj.getFullYear() +'-'+month+'-'+date;
			break;
			case '2':
			case 2:
				month = month<10 ? '0'+month : month;
				date = dt_obj.getDate();
				date = date<10 ? '0'+date : date;
				new_dt_str = date+'-'+month+'-'+dt_obj.getFullYear();
			break;
			case '1':/*25 Feb, 2023*/
			case 1:
				date = dt_obj.getDate();
				date = date<10 ? '0'+date : date;
				new_dt_str = date+' '+dt_obj.toLocaleString('default', { month: 'short' })+', '+dt_obj.getFullYear();
			break;
			default:
				month = month<10 ? '0'+month : month;
				date = dt_obj.getDate();
				date = date<10 ? '0'+date : date;
				new_dt_str = dt_obj.getFullYear() +'-'+(month)+'-'+date;
			break;
		}
		return new_dt_str;
}

function roundAccurately(num,precision){
	var num = num=='' || isNaN(num) ? 0 : parseFloat(num);
	return Number(Math.round(num+"e"+precision) + "e-"+precision);
}

function convertToNumberPrecision(num,fld_type,is_derived,implicit_conversion){
	var actual_num=num;
	var implicit_conversion = implicit_conversion==0 ? 0 : 1;
	if(!implicit_conversion){
		if(num=='' || isNaN(num)){
			return '';
		}
	}
	var num = num=='' || isNaN(num) ? 0 : parseFloat(num);
	var precision;
	if(Number.isInteger(fld_type)){
		precision = fld_type;
	}else{
		precision = gObj.globals[fld_type];
		precision = typeof precision!=='undefined' ? parseInt(precision) : 0;
		precision = isNaN(precision) ?  0 : precision;
	}
	var ret_formatted_num;
	if(is_derived===1){
		/*It is used to discard the auto format effect.Case: Number is actually already calculated & manipulated so no need to apply automatic format*/
		/*eg: num is a output of num1 +num2 (already auto format applied here) ;12.10+0.90 =13 if auto applied then it will be changed which shouldn't to be*/
		ret_formatted_num = roundAccurately(num,precision);
	}else{
		if(gObj.globals.decimal_entry_type==1){
			/*Automatic manipulation as per precision & only to be applied if user has typed value in the field & field no longer focused*/
			if(/\./.test(actual_num)){
				ret_formatted_num = roundAccurately(num,precision);
			}else{
				ret_formatted_num = num/Math.pow(10,precision);
				ret_formatted_num = roundAccurately(ret_formatted_num,precision);
			}
		}else{
			ret_formatted_num = roundAccurately(num,precision);
		}
	}
	return ret_formatted_num;
}

/*dddddddddddddddddd */
function confirmBoxJquery(obj)
{
	var defaults = {
							animation: 'scale',
							closeAnimation: 'scale',
							animateFromElement: false,
							backgroundDismiss: true,
							title: 'Confirm!',
							content: 'Confirm',
							buttons: {
								confirm: function () {
									window.location.href=obj.url;
								},
								danger: {
									text:'Cancel',
									btnClass: 'btn-red',
									action:function () {
										//$.alert('Canceled!');
									}
								}
							}
						};

	if(typeof obj!=='undefined'){
		defaults = $.extend(defaults,obj);
	}
	$.confirm(defaults);
}

$(document).on('click','.confirm_delete', function(e){
	e.preventDefault();
	var cur_obj = $(this);
	var url = cur_obj.attr('href');
	var message = "<hr>Are you sure you want to delete?<hr>";//cur_obj.data('message');
	confirmBoxJquery({
			content:message,
			buttons: {
				danger: {
					text:'Cancel',
					btnClass: 'btn btn-dark',
					action:function () {}
				},
				confirm: {
					text:'Confirm',
					btnClass: 'btn btn-danger',
					action:function () {
									window.location.href=url;
								}
				}
			}
	});
});