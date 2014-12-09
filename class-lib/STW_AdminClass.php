<?php

/**
 *
 * Class Special Teaser Widget Admin
 *
 * @ A5 Special Teaser Widget
 *
 * building admin page
 *
 */
class STW_Admin extends A5_OptionPage {
	
	const language_file = 'special-teaser-widget';
	
	static $options, $id, $action;
	
	function __construct() {
	
		add_action('admin_init', array($this, 'initialize_settings'));
		add_action('admin_menu', array($this, 'add_admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		
		self::$options = get_option('stw_options');
		
	}
	
	/**
	 *
	 * Make all the admin stuff draggable
	 *
	 */
	function enqueue_scripts($hook){
		
		if ('plugins_page_special-teaser-settings' != $hook) return;
		
		wp_enqueue_script('dashboard');
		
		if (wp_is_mobile()) wp_enqueue_script('jquery-touch-punch');
		
	}
	
	/**
	 *
	 * Add options-page for single site
	 *
	 */
	function add_admin_menu() {
		
		add_plugins_page('Special Teaser Widget '.__('Settings', self::language_file), '<img alt="" src="'.plugins_url('special-teaser-widget/img/a5-icon-11.png').'"> Special Teaser Widget', 'administrator', 'special-teaser-settings', array($this, 'build_options_page'));
		
	}
	
	/**
	 *
	 * Actually build the option pages
	 *
	 */
	function build_options_page() {
		
		if (isset($_GET['settings-updated']) && true == $_GET['settings-updated']) :
		
			self::$action = 'add_style';
			
			self::$id = 'new';
			
			unset(self::$options['style']['new']);
			
		else :
		
			self::$id = (isset($_GET['id'])) ? $_GET['id'] : 'new';
		
			self::$action = (isset($_GET['action'])) ? $_GET['action'] : 'add_style';
			
			switch (self::$action) :	
				
				case 'delete':
				
					unset(self::$options['style'][self::$id]);
						
					self::$id = 'new';
					
					add_settings_error('stw_options', 'delete-style', __('Style deleted.', self::language_file), 'updated');
					
					update_option('stw_options', self::$options);
					
					break;
				
				case 'copy' :
					
					self::$options['style']['new'] = self::$options['style'][self::$id];
				
					unset(self::$options['style']['new']['style_name']);
					
					$id = self::$id;
					
					self::$id = 'new';
					
					self::$action = 'add_style';
					
					break;
					
				case 'preview' :
				
					$id = self::$id;
					
					self::$id = 'new';
					
					unset(self::$options['style']['new']);
				
					break;
				
			endswitch;
			
		endif;
		
		$id = (isset($id)) ? $id : self::$id;;
		
		$eol = "\r\n";
		
		self::open_page('Special Teaser Widget', __('http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/special-teaser-widget', self::language_file), 'special-teaser-widget', __('Plugin Support', self::language_file));
		
		settings_errors();
		
		self::open_form('options.php');
		
		wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
		wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
		
		settings_fields('stw_options');
		
		self::open_tab(2);
		
		// Style table
		
		self::sortable('top', self::postbox(__('General Settings', self::language_file), 'style-table', 'stw_style_table'));
		
		// Widget Container
		
		self::sortable('upper-middle', self::postbox(__('Widget Container', self::language_file), 'widget-style', 'stw_widget_style'));
		
		self::sortable('middle', self::postbox(__('Widget Title', self::language_file), 'title-style', 'stw_title_style'));
		
		self::sortable('lower-middle', self::postbox(__('Widget Text', self::language_file), 'text-style', 'stw_text_style'));
		
		self::sortable('bottom', self::postbox(__('Widget Image', self::language_file), 'image-style', 'stw_img_style'));
		
		if (WP_DEBUG === true) self::sortable('deep-down', self::debug_info(self::$options, __('Debug Info', self::language_file)));
		
		submit_button();
		
		echo '</form>';
		
		self::column('1');
			
		self::preview_widget($id);
		
		$donationtext = self::tag_it(__('If you like the plugin and find it useful, you might think of rewarding the work that was spent creating it.', self::language_file), 'p');
		
		self::sortable('side_middle', self::donation_box($donationtext, __('Donations', self::language_file), 'GLAEHEAM7D7ME', 'http%3A%2F%2Fwasistlos.waldemarstoffel.com%2Fplugins-fur-wordpress%2Fspecial-teaser-widget'));
		
		self::close_tab();
		
		echo '</div>';
		
	}
	
	/**
	 *
	 * Initialize the admin screen of the plugin
	 *
	 */
	function initialize_settings() {
		
		register_setting( 'stw_options', 'stw_options', array($this, 'validate') );
		
		add_settings_section('stw_settings', false, array($this, 'display_style_section'), 'stw_style_table');
		
		add_settings_field('style_table', __('Styles:', self::language_file), array($this, 'style_table'), 'stw_style_table', 'stw_settings');
		
		add_settings_field('stw_inline', __('Debug:', self::language_file), array($this, 'inline_field'), 'stw_style_table', 'stw_settings', array(__('If you can&#39;t reach the dynamical style sheet, you&#39;ll have to diplay the styles inline. By clicking here you can do so.', self::language_file)));
		
		$cachesize = count(self::$options['cache']);
		
		$entry = ($cachesize > 1) ? __('entries', self::language_file) : __('entry', self::language_file);
		
		if ($cachesize > 0) add_settings_field('stw_reset', sprintf(__('Empty cache (%d %s)', self::language_file), $cachesize, $entry), array($this, 'reset_field'), 'stw_style_table', 'stw_settings', array(__('You can empty the plugin&#39;s cache here, if necessary.', self::language_file)));
		
		if ('edit' != self::$action) add_settings_field('style_name', __('Name:', self::language_file), array($this, 'style_name_field'), 'stw_style_table', 'stw_settings', array(__('Give a unique name for the style.', self::language_file)));
		
		add_settings_section('stw_settings', false, array($this, 'display_widget_section'), 'stw_widget_style');
		
		add_settings_field('widget_css', __('Main Container:', self::language_file), array($this, 'widget_field'), 'stw_widget_style', 'stw_settings');
		
		add_settings_section('stw_settings', false, array($this, 'display_title_section'), 'stw_title_style');
		
		add_settings_field('title_container_css', __('Title Container:', self::language_file), array($this, 'title_container_field'), 'stw_title_style', 'stw_settings');
		
		add_settings_field('title_font_css', __('Title Font:', self::language_file), array($this, 'title_font_field'), 'stw_title_style', 'stw_settings', array(__('Here you give the css styles for the title of your widget.', self::language_file)));
		
		add_settings_field('title_link_css', __('Style for Title Link:', self::language_file), array($this, 'title_link_field'), 'stw_title_style', 'stw_settings', array(__('Enter here the style and hover style for links in the title container.', self::language_file)));
		
		add_settings_field('title_link_hover_css', __('Hover Style for Title Link:', self::language_file), array($this, 'title_link_hover_field'), 'stw_title_style', 'stw_settings');
		
		add_settings_section('stw_settings', false, array($this, 'display_text_section'), 'stw_text_style');
		
		add_settings_field('text_container_css', __('Container:', self::language_file), array($this, 'text_container_field'), 'stw_text_style', 'stw_settings');
		
		add_settings_field('text_paragraph_css', __('Paragraph:', self::language_file), array($this, 'text_paragraph_field'), 'stw_text_style', 'stw_settings', array(__('For some themes, the paragraphs in the text container have to be styled differently.', self::language_file)));
		
		add_settings_field('text_link_css', __('Style for Text Links:', self::language_file), array($this, 'text_link_field'), 'stw_text_style', 'stw_settings', array(__('Enter here the style and hover style for links in the widget container.', self::language_file)));
		
		add_settings_field('text_link_hover_css', __('Hover Style for Text Links:', self::language_file), array($this, 'text_link_hover_field'), 'stw_text_style', 'stw_settings');
		
		add_settings_section('stw_settings', false, array($this, 'display_img_section'), 'stw_img_style');
		
		add_settings_field('img_css', __('Style for Images:', self::language_file), array($this, 'img_field'), 'stw_img_style', 'stw_settings');
		
		add_settings_field('img_hover_css', __('Hover Style for Images:', self::language_file), array($this, 'img_hover_field'), 'stw_img_style', 'stw_settings');
		
		add_settings_field('stw_resize', false, array($this, 'resize_field'), 'stw_img_style', 'stw_settings');
	
	}
	
	function display_style_section() {
		
		self::tag_it(__('You can create and add different styles for the widget here. If there are no styles defined, the widget will not be functioning. The names of the styles will appear in a drop down menu in the widgetsettings.', self::language_file), 'p');
	
	}
	
	function style_table() {
	
		$stw_styles = (array_key_exists('style', self::$options)) ? self::$options['style'] : array();
		
		if (!empty($stw_styles)) :
		
			$thead = self::tag_it(__('Style', self::language_file), 'th', 3, array('style' => 'width: 20%'));
			$thead .= self::tag_it('&nbsp;', 'th', 3, array('style' => 'width: 20%'));
			$thead .= self::tag_it('&nbsp;', 'th', 3, array('style' => 'width: 20%'));
			$thead .= self::tag_it('&nbsp;', 'th', 3, array('style' => 'width: 20%'));
			$thead .= self::tag_it('&nbsp;', 'th', 3, array('style' => 'width: 20%'));
			$thead = self::tag_it($thead, 'tr', 2);
			$thead = self::tag_it($thead, 'thead', 1);
			
			$rows = '';
			
			foreach ($stw_styles as $style_id => $style) :
			
				if ('new' != $style_id) :
			
					$tbody = '';
					
					$edit = self::tag_it(__('Edit'), 'a', 0, array('href' => '?page=special-teaser-settings&action=edit&id='.$style_id));
					$copy = self::tag_it(__('Copy'), 'a', 0, array('href' => '?page=special-teaser-settings&action=copy&id='.$style_id));
					$delete = self::tag_it(__('Delete'), 'a', 0, array('href' => '?page=special-teaser-settings&action=delete&id='.$style_id));
					$preview = self::tag_it(__('Preview'), 'a', 0, array('href' => '?page=special-teaser-settings&action=preview&id='.$style_id));
				
					$tbody .= self::tag_it($style['style_name'], 'td', 3);
					$tbody .= self::tag_it($edit, 'td', 3, array('class' => 'edit-style'));
					$tbody .= self::tag_it($copy, 'td', 3, array('class' => 'copy-style'));
					$tbody .= self::tag_it($delete, 'td', 3, array('class' => 'delete-style'));
					$tbody .= self::tag_it($preview, 'td', 3, array('class' => 'preview-style'));
					$rows .= self::tag_it($tbody, 'tr', 2);
					
				endif;
							
			endforeach;
			
			$tbody = self::tag_it($rows, 'tbody', 1);
			
			$output = self::tag_it($thead.$tbody, 'table', false, array('class' => 'stw-optiontable-layout'));
	   
		else :
		
			$output = __('There are no styles defined so far.', self::language_file);
		
		endif;
		
		echo $output;
	
	}
	
	function inline_field($labels) {
		
		a5_checkbox('inline', 'stw_options[inline]', @self::$options['inline'], $labels[0]);
		
	}
	
	function reset_field($labels) {
		
		a5_checkbox('reset_options', 'stw_options[reset_options]', @self::$options['reset_options'], $labels[0]);
		
	}
	
	function style_name_field($labels) {
		
		self::tag_it($labels[0], 'p', false, false, true);
		
		if ('edit' == self::$action) :
		
			a5_hidden_field('style_name', 'stw_options[style]['.self::$id.'][style_name]', @self::$options['style'][self::$id]['style_name']);
		
			a5_text_field('name_dummy', 'name_dummy', @self::$options['style'][self::$id]['style_name'], false, array('disabled' => 'disabled'));
			
		else :
		
			a5_text_field('style_name', 'stw_options[style]['.self::$id.'][style_name]', @self::$options['style'][self::$id]['style_name']);
		
		endif;
		
	}
	
	function display_widget_section() {
		
		self::tag_it(__('Here you give the css styles for the main container of your widget. This is obligatory, all the other styles are optional.', self::language_file), 'p', false, false, true);
	
	}
	
	function widget_field() {
		
		a5_textarea('main_container', 'stw_options[style]['.self::$id.'][main_container]', @self::$options['style'][self::$id]['main_container'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function display_title_section() {
		
		self::tag_it(__('Here you give the css styles for the title container of your widget.', self::language_file), 'p', false, false, true);
	
	}
	
	function title_container_field() {
		
		a5_textarea('title_container', 'stw_options[style]['.self::$id.'][title_container]', @self::$options['style'][self::$id]['title_container'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function title_font_field($labels) {
		
		self::tag_it($labels[0], 'p', false, false, true);
		
		a5_textarea('title_font', 'stw_options[style]['.self::$id.'][title_font]', @self::$options['style'][self::$id]['title_font'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function title_link_field($labels) {
		
		self::tag_it($labels[0], 'p', false, false, true);
		
		a5_textarea('title_link', 'stw_options[style]['.self::$id.'][title_link]', @self::$options['style'][self::$id]['title_link'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function title_link_hover_field() {
		
		a5_textarea('title_link_hover', 'stw_options[style]['.self::$id.'][title_link_hover]', @self::$options['style'][self::$id]['title_link_hover'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function display_text_section() {
		
		self::tag_it(__('Here you give the css styles for the title container of your widget.', self::language_file), 'p', false, false, true);
	
	}
	
	function text_container_field() {
		
		a5_textarea('text_container', 'stw_options[style]['.self::$id.'][text_container]', @self::$options['style'][self::$id]['text_container'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function text_paragraph_field($labels) {
		
		self::tag_it($labels[0], 'p', false, false, true);
		
		a5_textarea('text_paragraph', 'stw_options[style]['.self::$id.'][text_paragraph]', @self::$options['style'][self::$id]['text_paragraph'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function text_link_field($labels) {
		
		self::tag_it($labels[0], 'p', false, false, true);
		
		a5_textarea('text_link', 'stw_options[style]['.self::$id.'][text_link]', @self::$options['style'][self::$id]['text_link'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function text_link_hover_field() {
		
		a5_textarea('text_link_hover', 'stw_options[style]['.self::$id.'][text_link_hover]', @self::$options['style'][self::$id]['text_link_hover'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function display_img_section() {
		
		self::tag_it(__('There can be done a lot with a bit of CSS3 to your images.', self::language_file), 'p', false, false, true);
	
	}
	
	function img_field() {
		
		a5_textarea('image', 'stw_options[style]['.self::$id.'][image]', @self::$options['style'][self::$id]['image'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function img_hover_field() {
		
		a5_textarea('image_hover', 'stw_options[style]['.self::$id.'][image_hover]', @self::$options['style'][self::$id]['image_hover'], false, array('height' => '60px', 'cols' => 45));
		
	}
	
	function resize_field() {
		
		a5_resize_textarea(array('main_container', 'title_container', 'title_font', 'title_link', 'title_link_hover', 'text_container', 'text_paragraph', 'text_link', 'text_link_hover', 'image', 'image_hover'));
		
	}
		
	function validate($input) {
		
		if ('delete' == self::$action) :
		
			self::$action = 'add_style';
		
			return self::$options;
			
		endif;
		
		$error = array();
		
		$style = $input['style'];
		
		$key = key($style);
		
		if ('edit' == self::$action) $style['style_name'] = self::$options['style'][$key]['style_name'];
		
		$count = 0;
		
		foreach($style[$key] as $id => $value):
		
			if (!empty($value)) $count++;
			
		endforeach;
		
		if ($count != 0) :
			
			if (empty($style[$key]['style_name'])) $error['noname'] = __('Please enter a name for the style.', self::language_file);
			
			if ('new' == $key && !empty($style[$key]['style_name'])) $newkey = sanitize_key($style[$key]['style_name']);
			
			if (isset($newkey) && @array_key_exists($newkey, self::$options['style'])) :
			
				$error['key_exists'] = __('A style with that name already exists.', self::language_file);
				
				unset($style[$key]['style_name']);
				
			endif;
			
			if (empty($style[$key]['main_container'])) $error['no_main_style'] = __('Please enter a style for the main container.', self::language_file);
			
			if (count($error) == 0) :
			
				if ('new' != $key) self::$options['style'][$key] = $style[$key];
				
				if (isset($newkey)) self::$options['style'][$newkey] = $style[$key];
			
			endif;
			
			if (count($error) != 0) :
			
				if ('new' != $key) self::$options['style'][$key] = $style[$key];
				
				if (isset($newkey)) self::$options['style'][$newkey] = $style[$key];
			
			endif;
			
		endif;
		
		self::$options['inline'] = isset($input['inline']) ? true : false;
		
		if (isset($input['reset_options'])) :
		
			self::$options['cache'] = array();
			
			add_settings_error('stw_options', 'empty-cache', __('Cache emptied.', self::language_file), 'updated');
			
		endif;
		
		if (count($error) != 0) :
		
			foreach ($error as $id => $message) add_settings_error('stw_options', $id, $message, 'error');
			
		else :
			
			unset(self::$options['style']['new']);
		
			self::$action = 'add_style';
			
		endif;
		
		return self::$options;
	
	}
	
	/**
	 *
	 * Output widget for preview
	 *
	 */
	private static function preview_widget($style) {
		
		$eol = "\r\n";
		
		echo self::open_sortable('side_top');
		
		echo self::open_postbox(__('Preview'), 'preview-box');
		
		echo '<div id="special_teaser_widget" class="widget widget_special_teaser_widget '.$style.'" style="margin: 5px; padding: 20px;">'.$eol;
		
		echo '<div class="'.$style.'_title"><h3><a href="#">'.__('Widget Title', self::language_file).'</a></h3></div>'.$eol;
		
		echo '<div class="'.$style.'_content"><p><a href="#">Lorem Ipsum</a></p><a href="#"><img title="Lorem Ipsum" alt="image" id="test_image" width="150" height="100" src="'.plugins_url('/img/pihvipaikka.jpg', dirname(__FILE__)).'" /></a>'.$eol;
		
		echo '<div style="clear: both;"></div>'.$eol;
		
		echo '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p></div>';
	
		echo '</div>'.$eol;
		
		echo self::close_postbox();
		
		echo self::close_sortable();
		
	}

} // end of class

?>