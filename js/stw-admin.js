jQuery(document).ready(function(){
	
//save style

	jQuery("#styleform").submit(function()	{
		jQuery("#msg").html('');
		jQuery("#name_msg").html('');
		jQuery("#main_container_msg").html('');
		jQuery("#title_container_msg").html('');
		jQuery("#title_font_msg").html('');
		jQuery("#title_link_msg").html('');
		jQuery("#text_link_msg").html('');
		error=0;
		if(jQuery("#style_name").val()=="") {
			jQuery("#name_msg").html('<p class="error">' + Error.style_name + '</p>');
			error=1;
		}
		if(jQuery("#main_container").val()=="") {
			jQuery("#main_container_msg").html('<p class="error">' + Error.main_container + '</p>');
			error=1;
		}
		if(jQuery("#title_container").val()=="") {
			jQuery("#title_container_msg").html('<p class="error">' + Error.title_container + '</p>');
			error=1;
		}
		if(jQuery("#title_font").val()=="") {
			jQuery("#title_font_msg").html('<p class="error">' + Error.title_font + '</p>');
			error=1;
		}
		if(jQuery("#title_link").val()=="") {
			jQuery("#title_link_msg").html('<p class="error">' + Error.title_link + '</p>');
			error=1;
		}
		if(jQuery("#text_link").val()=="") {
			jQuery("#text_link_msg").html('<p class="error">' + Error.text_link + '</p>');
			error=1;
		}
		if(error==1) {
			return false;
		}
		else {
			var style_action = jQuery("#style_action").val();
			var style_name = jQuery("#style_name").val();
			var main_container = jQuery("#main_container").val();
			var title_container = jQuery("#title_container").val();
			var title_font = jQuery("#title_font").val();
			var title_link = jQuery("#title_link").val();
			var title_link_hover = jQuery("#title_link_hover").val();
			var text_container = jQuery("#text_container").val();
			var text_link = jQuery("#text_link").val();
			var text_link_hover = jQuery("#text_link_hover").val();
			var image = jQuery("#image").val();
			var image_hover = jQuery("#image_hover").val();
			var stylenonce = jQuery("#stylenonce").val();
			var _wp_http_referer = jQuery("#_wp_http_referer").val();
			var data = {
				action: 'stw_save_style',
				style_action: style_action,
				style_name: style_name,
				main_container: main_container,
				title_container: title_container,
				title_font: title_font,
				title_link: title_link,
				title_link_hover: title_link_hover,
				text_container: text_container,
				text_link: text_link,
				text_link_hover: text_link_hover,
				image: image,
				image_hover: image_hover,
				stylenonce: stylenonce,
				_wp_http_referer: _wp_http_referer
			};
			jQuery("#styleformsave").hide();
			jQuery(".ajaxsave").show();
			jQuery.post(ajaxurl, data,
			function(response){
				if (response.error==1) {
					jQuery("#msg").html(response.msg);
				}
				else {
					jQuery("#styletable").html(response.msg);
					jQuery("#style_name").val('');
					jQuery("textarea").val('');
					jQuery("textarea").attr('rows', '1');
					jQuery("textarea").autoResize();
					jQuery("#style_action").val('create');
					jQuery("#style_name").removeAttr("disabled");
					jQuery("#css_here").html('');
				}
				jQuery(".ajaxsave").hide();
				jQuery("#styleformsave").show();				
			}, 'json');
		}
		return false;
	});
	
//delete style

	jQuery("#styletable a.delete").live('click', function(){
		jQuery(this).parents("#styletable tr").addClass("deleting");
		var id = this.id;
		var data = {
				action: 'stw_delete_style',
				style_id: id
		};
		jQuery.post(ajaxurl, data,
		function(response){
			jQuery("#styletable").html(response);
			jQuery("#style_name").val('');
			jQuery("#style_id").val('');
			jQuery("textarea").val('');
			jQuery("textarea").autoResize();
			jQuery("#css_here").html('');
		});
		
	});
	
//get style for editing

	jQuery("#styletable a.edit").live('click', function(){
		jQuery(this).parents("#styletable tr").addClass("editing");
		var id = this.id;
		var data = {
				action: 'stw_edit_style',
				style_id: id
		};
		jQuery.post(ajaxurl, data,
		function(response){
			jQuery("#style_id").val(response.style_id);
			jQuery("#style_action").val('edit');
			jQuery("#style_name").val(response.style_name);
			jQuery("#main_container").val(response.main_container);
			jQuery("#title_container").val(response.title_container);
			jQuery("#title_font").val(response.title_font);
			jQuery("#title_link").val(response.title_link);
			jQuery("#title_link_hover").val(response.title_link_hover);
			jQuery("#text_container").val(response.text_container);
			jQuery("#text_link").val(response.text_link);
			jQuery("#text_link_hover").val(response.text_link_hover);
			jQuery("#image").val(response.image);
			jQuery("#image_hover").val(response.image_hover);
			jQuery("textarea").autoResize();
			jQuery("#styletable tr").removeClass('editing');
			jQuery("#style_name").attr("disabled", "disabled");
			preview();
		}, 'json');
		
	});
	
//get style for copying

	jQuery("#styletable a.copy").live('click', function(){
		jQuery(this).parents("#styletable tr").addClass("copying");
		var id = this.id;
		var data = {
				action: 'stw_edit_style',
				style_id: id
		};
		jQuery.post(ajaxurl, data,
		function(response){
			jQuery("#style_action").val('create');
			jQuery("#main_container").val(response.main_container);
			jQuery("#title_container").val(response.title_container);
			jQuery("#title_font").val(response.title_font);
			jQuery("#title_link").val(response.title_link);
			jQuery("#title_link_hover").val(response.title_link_hover);
			jQuery("#text_container").val(response.text_container);
			jQuery("#text_link").val(response.text_link);
			jQuery("#text_link_hover").val(response.text_link_hover);
			jQuery("#image").val(response.image);
			jQuery("#image_hover").val(response.image_hover);
			jQuery("textarea").autoResize();
			jQuery("#styletable tr").removeClass('copying');
			preview();
		}, 'json');
		
	});
	
//textarea resize
	
	jQuery("textarea").autoResize();
	
//preview the style while filling in the form
	
	jQuery("textarea").blur(function(){preview();});
	
//preview of the styles

function preview() {
	style='<style type="text/css" media="all">#test_widget {'+jQuery("#main_container").val()+'} ';
	style+='#test_title {'+jQuery("#title_container").val()+'} #test_title h3 {'+jQuery("#title_font").val()+'} #test_title a {'+jQuery("#title_link").val()+'} #test_title a:hover {'+jQuery("#title_link_hover").val()+'} ';
	style+='#test_content {'+jQuery("#text_container").val()+'} #test_content a {'+jQuery("#text_link").val()+'} #test_content a:hover {'+jQuery("#text_link_hover").val()+'} ';
	style+='#test_content img {'+jQuery("#image").val()+'} #test_content img:hover {'+jQuery("#image_hover").val()+'}</style>';
	jQuery("#css_here").html(style);
}

});