<?php
/*
Plugin Name: Special Teaser Widget
Description: The site admin can define models for the different instances of the widget, which can be used by writers to put a certain post in the spotlight. You can choose whether the teaser in the widget links to the post or to a category.
Version: 1.5.1

Author: Waldemar Stoffel
Author URI: http://www.waldemarstoffel.com
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/special-teaser-widget
License: GPL3
Text Domain: special-teaser-widget
*/

/*  Copyright 2011 -2014 Waldemar Stoffel  (email : stoffel@atelier-fuenf.de)

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

defined('ABSPATH') OR exit;

if (!defined('STW_PATH')) define( 'STW_PATH', plugin_dir_path(__FILE__) );
if (!defined('STW_BASE')) define( 'STW_BASE', plugin_basename(__FILE__) );

# loading the framework
if (!class_exists('A5_Image')) require_once STW_PATH.'class-lib/A5_ImageClass.php';
if (!class_exists('A5_Excerpt')) require_once STW_PATH.'class-lib/A5_ExcerptClass.php';
if (!class_exists('A5_FormField')) require_once STW_PATH.'class-lib/A5_FormFieldClass.php';
if (!class_exists('A5_OptionPage')) require_once STW_PATH.'class-lib/A5_OptionPageClass.php';
if (!class_exists('A5_DynamicFiles')) require_once STW_PATH.'class-lib/A5_DynamicFileClass.php';

#loading plugin specific classes
if (!class_exists('STW_Admin')) require_once STW_PATH.'class-lib/STW_AdminClass.php';
if (!class_exists('STW_DynamicCSS')) require_once STW_PATH.'class-lib/STW_DynamicCSSClass.php';
if (!class_exists('Special_Teaser_Widget')) require_once STW_PATH.'class-lib/STW_WidgetClass.php';

class SpecialTeaser {
	
	const language_file = 'special-teaser-widget', version = 1.5;
	
	static $options;
	
	function __construct() {
		
		load_plugin_textdomain(self::language_file, false , basename(dirname(__FILE__)).'/languages');
		
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action( 'wp_before_admin_bar_render', array($this, 'admin_bar_menu'));
		add_action('widgets_init', array($this, 'maybe_register_widget'));
		
		add_filter('plugin_row_meta', array($this, 'register_links'), 10, 2);	
		add_filter( 'plugin_action_links', array($this, 'plugin_action_links'), 10, 2 );
				
		register_activation_hook(  __FILE__, array($this, '_install') );
		register_deactivation_hook(  __FILE__, array($this, '_uninstall') );
		
		self::$options = get_option('stw_options');
		
		if (self::$options['version'] != self::version) $this->update_plugin_options();
		
		$STW_DynamicCSS = new STW_DynamicCSS;
		$STW_Admin = new STW_Admin;
		
	}
	
	/* attach JavaScript file for textarea resizing */
	function enqueue_scripts($hook) {
		
		if ('plugins_page_special-teaser-settings' != $hook && 'widgets.php' != $hook && 'post.php' != $hook) return;
		
		$min = (WP_DEBUG == false) ? '.min.' : '.';
		
		wp_register_script('ta-expander-script', plugins_url('ta-expander'.$min.'js', __FILE__), array('jquery'), '3.0', true);
		wp_enqueue_script('ta-expander-script');
		
		if ('plugins_page_special-teaser-settings' != $hook) return;
		
		wp_register_style('stw-admin', plugins_url('stw-admin-css.css', __FILE__), false, A5_FormField::version, 'all');
		wp_enqueue_style('stw-admin');
		
	}
	
	//Additional links on the plugin page
	
	function register_links($links, $file) {
		
		if ($file == STW_BASE) :
		
			$links[] = '<a href="http://wordpress.org/extend/plugins/special-teaser-widget/faq/" target="_blank">'.__('FAQ', self::language_file).'</a>';
			$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GLAEHEAM7D7ME" target="_blank">'.__('Donate', self::language_file).'</a>';
		
		endif;
		
		return $links;
		
	}
		
	function plugin_action_links( $links, $file ) {
		
		if ($file == STW_BASE) array_unshift($links, '<a href="'.admin_url( 'plugins.php?page=special-teaser-settings' ).'">'.__('Settings', self::language_file).'</a>');
	
		return $links;
	
	}
	
	/**
	 *
	 * Adds STW to the adminbar
	 *
	 */
	function admin_bar_menu() {
		
		global $wp_admin_bar, $stw_language_file;
		
		if ( !is_super_admin() || !is_admin_bar_showing() ) {
		return; 
		
		}
		
		$wp_admin_bar->add_node( array( 'parent' => 'new-content', 'id' => 'new-stw-style', 'title' => __('Special Teaser Style', $stw_language_file), 'href' => admin_url( 'plugins.php?page=special-teaser-settings' ) ) );
		
		
	}

	// Creating default options on activation
	
	function _install() {
		
		$default = array(
			'version' => self::version,
			'cache' => array(),
			'inline' => false
		);
		
		add_option('stw_options', $default);
		
	}
	
	// Cleaning on deactivation
	
	function _uninstall() {
		
		delete_option('stw_options');
		
	}
	
	// updating options in case they are outdated
	
	function update_plugin_options() {
		
		$options_old = get_option('stw_options');
		
		$options_new['version'] = self::version;
	
		$options_new['cache'] = array();
		
		$options_new['inline'] = false;
		
		$options_new['style'] = $options_old['style'];
		
		update_option('stw_options', $options_new);
	
	}
	
	// maybe, maybe not
	function maybe_register_widget() {
		
		if (!array_key_exists('style', self::$options) || empty(self::$options['style'])) return;
	
		register_widget('Special_Teaser_Widget');	
		
	}
	
} // end of class
$SpecialTeaser = new SpecialTeaser;

?>