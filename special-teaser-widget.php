<?php
/*
Plugin Name: Special Teaser Widget
Description: The site admin can define models for the different instances of the widget, which can be used by writers to put a certain post in the spotlight. You can choose whether the teaser in the widget links to the post or to a category.
Version: 1.5.1

Author: Waldemar Stoffel
Author URI: http://www.waldemarstoffel.com
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/special-teaser-widget
License: GPL3
Text Domain: stw
*/

/*  Copyright 2011  Waldemar Stoffel  (email : stoffel@atelier-fuenf.de)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/


/* Stop direct call */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) die(__('Sorry, you don&#39;t have direct access to this page.', $fpw_language_file));

/**
 *
 * register and enqueue styles
 *
 */
 
function stw_add_styles() {
	
	$stw_options=get_option('stw_options');
	
	if (!empty($stw_options['style'])) $current_version='1.4';
	
	if ($current_version != $stw_options['version']) :
	
		$stw_options['version'] = $current_version;
		
		$stw_styles = $stw_options['style'];
		
		foreach ($stw_styles as $style_id => $style) :
		
			$id = sanitize_key($style['style_name']);
			
			$new_style[$id] = $style;
			
			unset ($stw_options['style'][$style_id]);
		
		endforeach;
		
		$stw_options['style'] = $new_style;
		
		update_option('stw_options', $stw_options);
		
	endif;
	
	$stw_css_file=get_bloginfo('url')."/?stwfile=css";
	
	wp_register_style('special-teaser-widget', $stw_css_file, false, $stw_options['version'], 'all');
	wp_enqueue_style('special-teaser-widget');
	
}
add_action('wp_print_styles', 'stw_add_styles');

add_action('init','stw_add_rewrite');
function stw_add_rewrite() {
       global $wp;
       $wp->add_query_var('stwfile');
}

add_action('template_redirect','stwcss_template');
function stwcss_template() {
       if (get_query_var('stwfile') == 'css') {
               
			   header('Content-type: text/css');
			   
			   echo stw_write_css();
			   
               exit;
       }
}

/* attach JavaScript file for textarea resizing */

function stw_js_sheet() {
   
   wp_enqueue_script('ta-expander-script', plugins_url('/js/ta-expander.js', __FILE__), array('jquery'), '2.0', true);

}

add_action('admin_print_scripts-widgets.php', 'stw_js_sheet');

/**
 *
 * Adds links to the plugin page
 *
 */
function stw_register_links($links, $file) {
	
	$base = plugin_basename(__FILE__);
	
	if ($file == $base) :
	
		$links[] = '<a href="http://wordpress.org/extend/plugins/special-teaser-widget/faq/" target="_blank">'.__('FAQ', $stw_language_file).'</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GLAEHEAM7D7ME" target="_blank">'.__('Donate', $stw_language_file).'</a>';
	
	endif;
	
	return $links;

}
add_filter('plugin_row_meta', 'stw_register_links',10,2);

function stw_plugin_action_links( $links, $file ) {
	
	$base = plugin_basename(__FILE__);
	
	if ($file == $base) array_unshift($links, '<a href="'.admin_url( 'plugins.php?page=stw-settings' ).'">'.__('Settings', $stw_language_file).'</a>');

	return $links;

}
add_filter( 'plugin_action_links', 'stw_plugin_action_links', 10, 2 );

/**
 *
 * Adds STW to the adminbar
 *
 */
function stw_admin_bar_menu() {
	global $wp_admin_bar;
	
	if ( !is_super_admin() || !is_admin_bar_showing() ) {
	return; 
	
	}
	
	$wp_admin_bar->add_node( array( 'parent' => 'new-content', 'id' => 'new-stw-style', 'title' => __('Special Teaser Style', $stw_language_file), 'href' => admin_url( 'plugins.php?page=stw-settings' ) ) );
	
	
}
add_action( 'wp_before_admin_bar_render', 'stw_admin_bar_menu');

define( 'STW_PATH', plugin_dir_path(__FILE__) );

$stw_options=get_option('stw_options');

if (!class_exists('A5_Thumbnail')) require_once STW_PATH.'class-lib/A5_ImageClasses.php';
if (!class_exists('A5_Excerpt')) require_once STW_PATH.'class-lib/A5_ExcerptClass.php';
if (!class_exists('Special_Teaser_Widget') && !empty($stw_options['style'])) require_once STW_PATH.'class-lib/STW_WidgetClass.php';


// import laguage files

$stw_language_file = 'special-teaser-widget';

load_plugin_textdomain($stw_language_file, false , basename(dirname(__FILE__)).'/languages');

// Checking for stylesheet on activation

register_activation_hook(  __FILE__, 'start_stw' );

function start_stw() {
	
	add_option('stw_options', array('version' => '1.4'));

}

// Cleaning on deactivation

register_deactivation_hook(  __FILE__, 'unset_stw' );

function unset_stw() {
	
	delete_option('stw_options');
	
}

/**
 *
 * Creating Settings Page
 *
 */
function stw_admin_menu() {
	
	require_once STW_PATH.'admin.php';
	
	$pages=add_plugins_page('Special Teaser Widget', 'Special Teaser Widget', 'administrator', 'stw-settings', 'stw_options_page');	
	
	add_action('admin_print_styles-'.$pages, 'stw_admin_css');
	add_action('admin_print_scripts-'.$pages, 'stw_admin_js');
}
add_action('admin_menu', 'stw_admin_menu');

/**
 *
 * register styles and scripts for settings page
 *
 */
function stw_register_admin_extras() {
	 
	 $stw_options=get_option('stw_options');
	 
	 wp_register_style('stw-admin', plugins_url('/css/stw-admin-css.css', __FILE__), false, $stw_options['version'], 'all');
	 wp_register_script('stw-admin-script', plugins_url('/js/stw-admin.js', __FILE__), array('jquery'), $stw_options['version'], true);
	 wp_register_script('stw-helper-script', plugins_url('/js/ta-expander.js', __FILE__), array('jquery'), '2.0', true);

}
add_action('admin_init', 'stw_register_admin_extras');

/**
 *
 * Adding stylesheet to settings page
 *
 */
function stw_admin_css() {
	
	wp_enqueue_style('stw-admin');
	
}

/**
 *
 * Adding scripts to settings page
 *
 */
function stw_admin_js() {
	
	wp_enqueue_script('stw-admin-script');
	wp_enqueue_script('stw-helper-script');
	wp_localize_script('stw-admin-script', 'Error', stw_localize_admin());
	
}

/**
 *
 * Adding l10n to the script
 *
 */
function stw_localize_admin() {

	global $stw_language_file;
	
	return array (
	
	'style_name' => __('Please enter a name for your style.', $stw_language_file),
	'main_container' => __('Please style the main container.', $stw_language_file),
	'title_container' => __('Please style the title container.', $stw_language_file),
	'title_font' => __('Please style the font of the title.', $stw_language_file),
	'title_link' => __('Please style the title link.', $stw_language_file),
	'text_link' => __('Please style the text link.', $stw_language_file),
	
	);

}

/**
 *
 * Saving the styles
 *
 */
 
function stw_save_style() {
	
	global $stw_language_file;
	
	$stw_options = get_option('stw_options');
	
	if (!wp_verify_nonce($_POST['stylenonce'],'save_style')) :
	
		$output = '<p class="error">'.__('Error in Datatransfer.', $stw_language_file).'</p>';
		$output = array('error' => 1, 'msg' => $output);
		
		$return=json_encode($output);
	
	else :
	
		$style_action = $_POST['style_action'];
		$style_name = $_POST['style_name'];
		$main_container = $_POST['main_container'];
		$title_container = $_POST['title_container'];
		$title_font = $_POST['title_font'];
		$title_link = $_POST['title_link'];
		$title_link_hover = $_POST['title_link_hover'];
		$text_container = $_POST['text_container'];
		$text_paragraph = $_POST['text_paragraph'];
		$text_link = $_POST['text_link'];
		$text_link_hover = $_POST['text_link_hover'];
		$image = $_POST['image'];
		$image_hover = $_POST['image_hover'];
		
		$style_id=sanitize_key($style_name);
		
		if (!empty($stw_options['style']) && array_key_exists($style_id, $stw_options['style']) && $style_action=='create') :
		
			$output = '<p class="error">'.__('A style with that name already exists.', $stw_language_file).'</p>';
			$output = array('error' => 1, 'msg' => $output);
			
			$return=json_encode($output);
		
		else :
			
			$stw_options['style'][$style_id]['style_name']=str_replace("\'", "'", $style_name);
			$stw_options['style'][$style_id]['main_container']=str_replace("\'", "'", $main_container);
			$stw_options['style'][$style_id]['title_container']=str_replace("\'", "'", $title_container);
			$stw_options['style'][$style_id]['title_font']=str_replace("\'", "'", $title_font);
			$stw_options['style'][$style_id]['title_link']=str_replace("\'", "'", $title_link);
			$stw_options['style'][$style_id]['title_link_hover']=str_replace("\'", "'", $title_link_hover);
			$stw_options['style'][$style_id]['text_container']=str_replace("\'", "'", $text_container);
			$stw_options['style'][$style_id]['text_paragraph']=str_replace("\'", "'", $text_paragraph);
			$stw_options['style'][$style_id]['text_link']=str_replace("\'", "'", $text_link);
			$stw_options['style'][$style_id]['text_link_hover']=str_replace("\'", "'", $text_link_hover);
			$stw_options['style'][$style_id]['image']=str_replace("\'", "'", $image);
			$stw_options['style'][$style_id]['image_hover']=str_replace("\'", "'", $image_hover);
			update_option('stw_options', $stw_options);
			
			$msg=stw_get_styletable();
			$output=array('error' => 0, 'msg' => $msg);
			
			$return=json_encode($output);
		
		endif;
		
	endif;
	
	echo $return;
	
	die();

}
add_action('wp_ajax_stw_save_style', 'stw_save_style');
  
/*
**
** deleting style
**
*/

function stw_delete_style() {
	
	$stw_options = get_option('stw_options');
	
	$style_id = $_POST['style_id'];
	
	unset($stw_options['style'][$style_id]);
	update_option('stw_options', $stw_options);
	
	echo stw_get_styletable();
	die();
	
}
 
add_action('wp_ajax_stw_delete_style', 'stw_delete_style');

/*
**
** getting style for editing
**
*/

function stw_edit_style() {
	
	$stw_options = get_option('stw_options');
	
	$style_id = $_POST['style_id'];
	
	$style=$stw_options['style'][$style_id];
	
	$output = array('style_id' => $style_id) + $style;
	
	$return = json_encode($output);
	
	echo $return;
	die();
	
}
 
add_action('wp_ajax_stw_edit_style', 'stw_edit_style');
 
 /**
 *
 * Printing styletable
 *
 */
 
function stw_get_styletable() {
	
	$stw_options = get_option('stw_options');
	
	$stw_styles = $stw_options['style'];
    
	if (!empty($stw_styles)) :
        $output = '<table class="stw-optiontable-layout style">
                        <thead>
                            <tr>
							  <th style="width: 40%;">'.__('Style').'</th>
							  <th style="width: 20%;">&nbsp;</th>
							  <th style="width: 20%;">&nbsp;</th>
							  <th style="width: 20%;">&nbsp;</th>
                            </tr>
                        </thead>
						<tbody>';
						
        foreach ($stw_styles as $style_id => $style) :
            $output .= '<tr id="'.$style_id.'">
                            <td>' . $style['style_name'] . '</td>
                            <td><a href="#" class="edit">'.__('Edit', $stw_language_file).'</td>
							<td><a href="#" class="copy">'.__('Copy', $stw_language_file).'</td>
                            <td><a href="#" class="delete">'.__('Delete', $stw_language_file).'</a>
							</td>
                        </tr>';
        endforeach;
		
        $output .= '</tbody>
					</table>';
   
    else :
        $output = __('There are no styles defined so far.');
    
	endif;
	
    return $output;

}

/**
 *
 * writing stylesheet
 *
 */
 
 function stw_write_css() {
	 
	$stw_options=get_option('stw_options');
	$stw_styles=$stw_options['style'];
	
	$eol = "\r\n";
	
	$css_text="@charset \"UTF-8\";\r\n/* CSS Document */\r\n\r\n";
	
	foreach ($stw_styles as $style_id => $class):
	
		$css_text.='div[id^="'.$style_id.'_main_container"] {'.$eol.$class['main_container'].$eol.'}'.$eol;
		$css_text.='div[id^="'.$style_id.'_title_container"] {'.$eol.$class['title_container'].$eol.'}'.$eol;
		$css_text.='div[id^="'.$style_id.'_title_container"] h3 {'.$eol.$class['title_font'].$eol.'}'.$eol;
		$css_text.='div[id^="'.$style_id.'_title_container"] a {'.$eol.$class['title_link'].$eol.'}'.$eol;
		if (!empty($class['title_link_hover'])) $css_text.='div[id^="'.$style_id.'_title_container"] a:hover {'.$eol.$class['title_link_hover'].$eol.'}'.$eol;
		if (!empty($class['text_container'])) $css_text.='div[id^="'.$style_id.'_text_container"] {'.$eol.$class['text_container'].$eol.'}'.$eol;
		if (!empty($class['text_paragraph'])) $css_text.='div[id^="'.$style_id.'_text_container"] p {'.$eol.$class['text_paragraph'].$eol.'}'.$eol;
		$css_text.='div[id^="'.$style_id.'_text_container"] a {'.$eol.$class['text_link'].$eol.'}'.$eol;
		if (!empty($class['text_link_hover'])) $css_text.='div[id^="'.$style_id.'_text_container"] a:hover {'.$eol.$class['text_link_hover'].$eol.'}'.$eol;
		if (!empty($class['image'])) $css_text.='div[id^="'.$style_id.'_text_container"] img {'.$eol.$class['image'].$eol.'}'.$eol;
		if (!empty($class['image_hover'])) $css_text.='div[id^="'.$style_id.'_text_container"] img:hover {'.$eol.$class['image_hover'].$eol.'}'.$eol;

	endforeach;
	
	return $css_text;
 }

?>