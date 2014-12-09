<?php

/**
 *
 * Class STW Dynamic CSS
 *
 * Extending A5 Dynamic Files
 *
 * Presses the dynamical CSS of the Special Teaser Widget into a virtual style sheet
 *
 */

class STW_DynamicCSS extends A5_DynamicFiles {
	
	private static $options;
	
	function __construct() {
		
		self::$options =  get_option('stw_options');
		
		if (!array_key_exists('style', self::$options) || empty(self::$options['style'])) return;
		
		if (!isset(self::$options['inline'])) self::$options['inline'] = false;
		
		if (!isset(self::$options['compress'])) self::$options['compress'] = false;
		
		parent::A5_DynamicFiles('wp', 'css', 'all', false, self::$options['inline'], self::$options['compress']);
		
		parent::A5_DynamicFiles('admin', 'css', 'all', 'plugins_page_special-teaser-settings', true); 
		
		$eol = (self::$options['compress']) ? '' : "\r\n";
		$tab = (self::$options['compress']) ? '' : "\t";
		
		$widget_style = (!self::$options['compress']) ? $eol.'/* CSS portion of the Special Teaser Widget(s) */'.$eol.$eol : '';
		
		$styles = self::$options['style'];
		
		foreach ($styles as $style_id => $class):
		
			$css_selector = $style_id.'[id^="special_teaser_widget"]';
		
			// widget
		
			$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['main_container']));
		
			$widget_style .= parent::build_widget_css($css_selector, '').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			// widget title
			
			$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['title_container']));
			
			$widget_style .= parent::build_widget_css($css_selector, '.'.$style_id.'_title').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['title_font']));
			
			$widget_style .= parent::build_widget_css($css_selector, '.'.$style_id.'_title h3').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['title_link']));
			
			$widget_style .= parent::build_widget_css($css_selector, '.'.$style_id.'_title a').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			if ($class['title_link_hover']) :
			
				$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['title_link_hover']));
			
				$widget_style .= parent::build_widget_css($css_selector, '.'.$style_id.'_title a:hover').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			endif;
			
			// text
			
			if ($class['text_container']) :
			
				$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['text_container']));
			
				$widget_style .= parent::build_widget_css($css_selector, '.'.$style_id.'_content').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			endif;
			
			if ($class['text_paragraph']) :
			
				$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['text_paragraph']));
			
				$widget_style .= parent::build_widget_css($css_selector, '.'.$style_id.'_content p').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			endif;
			
			// links
			
			$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['text_link']));
			
			$widget_style .= parent::build_widget_css($css_selector, '.'.$style_id.'_content a').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			if ($class['text_link_hover']) :
			
				$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['text_link_hover']));
			
				$widget_style .= parent::build_widget_css($css_selector, '.'.$style_id.'_content a:hover').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			endif;
			
			// images
			
			if ($class['image']) :
			
				$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['image']));
			
				$widget_style .= parent::build_widget_css($css_selector, 'img').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			endif;
			
			if ($class['image_hover']) :
			
				$style = str_replace('; ', ';'.$eol.$tab, str_replace(array("\r\n", "\n", "\r"), ' ', $class['image_hover']));
			
				$widget_style .= parent::build_widget_css($css_selector, 'img:hover').'{'.$eol.$tab.$style.$eol.'}'.$eol;
			
			endif;
	
		endforeach;
		
		parent::$wp_styles .= $widget_style;
			
		parent::$admin_styles .= $widget_style;

	}
	
} // STW_Dynamic CSS

?>