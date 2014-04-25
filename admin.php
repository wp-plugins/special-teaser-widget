<?php

/**
 *
 * settings page
 *
 */

function stw_options_page() {
	
	global $stw_language_file;
	
	$stw_options = get_option('stw_options');

	?>
	
<table width="100%" cellpadding="2" cellspacing="0"><tr><td valign="middle" width="380"><h2 style="margin:0 30px 0 0; padding: 5px 0 5px 0;">
Special Teaser Widget <?php _e('Settings', $stw_language_file); ?></h2></td><td valign="middle">&nbsp;</td>
</tr></table>

<div class="wrap" style="margin: 0 10px 0 0">
	
<table>
<tr>

<td valign="top" width="100%">
<div id="stw-settings">
    <div class="stw-container">
        <div id="styletable" class="stw-container-full">
        <?php
		
		echo stw_get_styletable();
		
		?>
        <div id="msg"></div>
        </div>
	</div>
    <form method="post" name="styleform" id="styleform" action="">
      <div class="stw-container">
        <div class="stw-container-left">
        <?php wp_nonce_field('save_style','stylenonce'); ?>
        <input type="hidden" id="style_action" name="style_action" value="create" />
        <label for="style_name"><?php _e('Name'); ?></label>
        <input name="style_name" id="style_name" type="text" value="" />
        <div id="name_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('You can create and add different styles for the widget here. If there are no styles defined, the widget will not be functioning. The names of the styles will appear in a drop down menu in the widgetsettings.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="main_container"><?php _e('Widget Container', $stw_language_file); ?></label>
        <textarea name="main_container" id="main_container"></textarea>
        <div id="main_container_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Here you give the css styles for the main container of your widget.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="title_container"><?php _e('Title Container', $stw_language_file); ?></label>
        <textarea name="title_container" id="title_container"></textarea>
        <div id="title_container_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Here you give the css styles for the title container of your widget.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="title_font"><?php _e('Title Font', $stw_language_file); ?></label>
        <textarea name="title_font" id="title_font"></textarea>
        <div id="title_font_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Here you give the css styles for the title of your widget.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="title_link"><?php _e('Style for Title Link', $stw_language_file); ?></label>
        <textarea name="title_link" id="title_link"></textarea>
        <label for="title_link_hover"><?php _e('Hover Style for Title Link', $stw_language_file); ?></label>
        <textarea name="title_link_hover" id="title_link_hover"></textarea>
        <div id="title_link_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Enter here the style and hover style for links in the title container.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="text_container"><?php _e('Text Container', $stw_language_file); ?></label>
        <textarea name="text_container" id="text_container"></textarea>
        </div>
        <div class="stw-container-right">
        <p><?php _e('This is an optional style. It can make sometimes sense to have the content of the widget in an extra container, which you style here.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="text_paragraph"><?php _e('Text Paragraph', $stw_language_file); ?></label>
        <textarea name="text_paragraph" id="text_paragraph"></textarea>
        </div>
        <div class="stw-container-right">
        <p><?php _e('This is an optional style. For some themes, the paragraphs in the text container have to be styled differently.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="text_link"><?php _e('Style for Text Links', $stw_language_file); ?></label>
        <textarea name="text_link" id="text_link"></textarea>
        <label for="text_link_hover"><?php _e('Hover Style for Text Link', $stw_language_file); ?></label>
        <textarea name="text_link_hover" id="text_link_hover"></textarea>
        <div id="text_link_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Enter here the style and hover style for links in the widget container.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="image"><?php _e('Style for images', $stw_language_file); ?></label>
        <textarea name="image" id="image"></textarea>
        <label for="image_hover"><?php _e('Hover Style for images', $stw_language_file); ?></label>
        <textarea name="image_hover" id="image_hover"></textarea>
        </div>
        <div class="stw-container-right">
        <p><?php _e('This section is optional. But there can be done a lot with a bit of CSS3 to your images.', $stw_language_file); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>      
      <div id="submit-container" class="stw-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
		<p class="submit">
		<input class="save-tab" name="styleformsave" id="styleformsave" value="<?php esc_attr_e('Save Changes'); ?>" type="submit"><img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" alt="" class="ajaxsave" style="display: none;" />
		<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', $stw_language_file); ?></span>
		</p></div>
    </form>
</div>
</td>
<td valign="top" width="220">
<div class="stw-sidecar">
<p><?php

_e('In this widget you can see and control the looks of your styles before saving them.', $stw_language_file);

?></p>
<div class="infohighlight">
<div id="test_widget">
<div id="test_title">
<h3><a href="#">Widget Title</a></h3>
</div>
<div id="test_content">
<p><a href="#">Lorem Ipsum</a></p>
<a href="#"><img title="Lorem Ipsum" alt="image" id="test_image" width="150" height="100" src="<?php echo plugins_url('/img/pihvipaikka.jpg', __FILE__); ?>" /></a>
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
</div></div></div></div>
</td>
</tr>
</table>

</div><!-- / class=wrap -->
<div id="css_here"></div>
<?php
	
}

?>