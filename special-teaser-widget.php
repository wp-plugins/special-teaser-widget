<?php
/*
Plugin Name: Special Teaser Widget
Description: The site admin can define models for the different instances of the widget, which can be used by writers to put a certain post in the spotlight. You can choose whether the teaser in the widget links to the post or to a category.
Version: 1.0

Author: Waldemar Stoffel
Author URI: http://www.waldemarstoffel.com
Plugin URI: http://wasistlos.waldemarstoffel.com/plugins-fur-wordpress/special-teaser-widget
License: GPL3
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

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die("Sorry, you don't have direct access to this page."); }

/**
 *
 * register and enqueue styles
 *
 */
 
function stw_add_styles() {
	
	$stw_options=get_option('stw_options');
	if (!empty($stw_options['style'])):
	
	$stw_css_file = WP_PLUGIN_DIR . '/special-teaser-widget/css/stw-css.css';
	
	if (!file_exists($stw_css_file)) stw_write_css();
	
	wp_register_style('special-teaser-widget', plugins_url('/css/stw-css.css', __FILE__), false, $stw_options['version'], 'all');
	wp_enqueue_style('special-teaser-widget');
	
	endif;
	
}
add_action('wp_print_styles', 'stw_add_styles');

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
	if ($file == $base) {
		$links[] = '<a href="'.admin_url().'plugins.php?page=stw-settings">'.__('Settings', 'stw').'</a>';
		$links[] = '<a href="http://wordpress.org/extend/plugins/artshop/faq/" target="_blank">'.__('FAQ', 'stw').'</a>';
		$links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RBCK7PSEBJ5HJ" target="_blank">'.__('Donate', 'stw').'</a>';
	}
	
	return $links;

}
add_filter('plugin_row_meta', 'stw_register_links',10,2);

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
	
	$wp_admin_bar->add_menu( array( 'parent' => 'new-content', 'id' => 'stw', 'title' => __('Special Teaser Style', 'stw'), 'href' => admin_url( 'plugins.php?page=stw-settings' ) ) );
	
	
}
add_action( 'wp_before_admin_bar_render', 'stw_admin_bar_menu');

/**
 *
 * extending the widget class only if one or more style has been defined in the settings
 *
 */
 
$stw_options=get_option('stw_options');

if (!empty($stw_options['style'])):

class Special_Teaser_Widget extends WP_Widget {
	
	function Special_Teaser_Widget() {
		
		$widget_opts = array( 'description' => __('Put a featured post into the widget and choose one of the styles to get it into the attention af your readers.', 'stw') );
		$control_opts = array( 'width' => 400 );
		
        parent::WP_Widget(false, $name = 'Special Teaser Widget', $widget_opts, $control_opts);
    }
    
	
 
function form($instance) {
	
// setup some default settings
    
	$defaults = array( 'homepage' => true, 'category' => true, 'single' => true );
    
	$instance = wp_parse_args( (array) $instance, $defaults );
	
	$title = esc_attr($instance['title']);
	$name = esc_attr($instance['name']);
	$clickable = esc_attr($instance['clickable']);
	$thumb = esc_attr($instance['thumb']);
	$image = esc_attr($instance['image']);
	$article = esc_attr($instance['article']);
	$backup = esc_attr($instance['backup']);
	$width = esc_attr($instance['width']);
	$subtitle = esc_attr($instance['subtitle']);	
	$excerpt = esc_attr($instance['excerpt']);
	$noshorts = esc_attr($instance['noshorts']);
	$readmore = esc_attr($instance['readmore']);
	$rmtext = esc_attr($instance['rmtext']);
	$style = esc_attr($instance['style']);
	$homepage=esc_attr($instance['homepage']);
	$frontpage=esc_attr($instance['frontpage']);
	$page=esc_attr($instance['page']);
	$category=esc_attr($instance['category']);
	$single=esc_attr($instance['single']);
	$date=esc_attr($instance['date']);
	$tag=esc_attr($instance['tag']);
	$attachment=esc_attr($instance['attachment']);
	$taxonomy=esc_attr($instance['taxonomy']);
	$author=esc_attr($instance['author']);
	$search=esc_attr($instance['search']);
	$not_found=esc_attr($instance['not_found']);
	$linktocat=esc_attr($instance['linktocat']);
	$cat=esc_attr($instance['cat']);
	
	$categories = get_categories('hide_empty=0');
	$features = get_posts('numberposts=-1');
	$options = get_option('stw_options');
	$styles = $options['style'];
	
 ?>
 
<p>
 <label for="<?php echo $this->get_field_id('name'); ?>">
 <?php _e('Title (will be displayed in blog):', 'stw'); ?>
 <input class="widefat" id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="text" value="<?php echo $name; ?>" />
 </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e('Choose here the style of your widget.', 'stw'); ?></label>
  <select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" class="widefat" style="width:100%;">
  <?php
  if (empty($style)) echo '<option value="">'.__('Choose style', 'stw').'</option>';
    foreach ( $styles as $style_id => $widget_style) {
      $selected = ( $style_id == $style ) ? 'selected="selected"' : '' ;
      $option = '<option value="'.$style_id.'" '.$selected.' >'.$widget_style['style_name'].'</option>';
      echo $option;
    }
  ?>
  </select>
</p>
<p>
 <label for="<?php echo $this->get_field_id('clickable'); ?>">
 <input id="<?php echo $this->get_field_id('clickable'); ?>" name="<?php echo $this->get_field_name('clickable'); ?>" <?php if(!empty($clickable)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Link the widget title to a category', 'stw'); ?>
 </label> 
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e('Category:', 'stw'); ?></label>
  <select id="<?php echo $this->get_field_id( 'cat' ); ?>" name="<?php echo $this->get_field_name( 'cat' ); ?>" class="widefat" style="width:100%;">
  <?php
  if (empty($cat)) echo '<option value="">'.__('Choose category', 'stw').'</option>';  
    foreach ( $categories as $category ) {
      $selected = ( $category->cat_ID == $cat ) ? 'selected="selected"' : '' ;
      $option = '<option value="'.$category->cat_ID.'" '.$selected.' >'.$category->cat_name.'</option>';
      echo $option;
    }
  ?>
  </select>
</p>
<p>
 <label for="<?php echo $this->get_field_id('title'); ?>">
 <?php _e('Name (internal widgettitle):', 'stw'); ?>
 <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
 </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'article' ); ?>"><?php _e('Choose here the post, you want to appear in the widget.', 'stw'); ?></label>
  <select id="<?php echo $this->get_field_id( 'article' ); ?>" name="<?php echo $this->get_field_name( 'article' ); ?>" class="widefat" style="width:100%;">
  <?php
  if (empty($article)) echo '<option value="">'.__('Choose post', 'stw').'</option>';
    foreach ( $features as $feature ) {
      $selected = ( $feature->ID == $article ) ? 'selected="selected"' : '' ;
      $option = '<option value="'.$feature->ID.'" '.$selected.' >'.$feature->post_title.'</option>';
      echo $option;
    }
  ?>
  </select>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'backup' ); ?>"><?php _e('Choose here the backup post. It will appear, when a single post page shows the featured article.', 'stw'); ?></label>
  <select id="<?php echo $this->get_field_id( 'backup' ); ?>" name="<?php echo $this->get_field_name( 'backup' ); ?>" class="widefat" style="width:100%;">
  <?php
  if (empty($backup)) echo '<option value="">'.__('Choose backup post', 'stw').'</option>';
    foreach ( $features as $feature ) {
      $selected = ( $feature->ID == $backup ) ? 'selected="selected"' : '' ;
      $option = '<option value="'.$feature->ID.'" '.$selected.' >'.$feature->post_title.'</option>';
      echo $option;
    }
  ?>
  </select>
</p>
<p>
 <label for="<?php echo $this->get_field_id('linktocat'); ?>">
 <input id="<?php echo $this->get_field_id('linktocat'); ?>" name="<?php echo $this->get_field_name('linktocat'); ?>" <?php if(!empty($linktocat)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Check to link to the same category as the title is linking to.', 'stw'); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('image'); ?>">
 <input id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" <?php if(!empty($image)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Check to get the first image of the post as thumbnail.', 'stw'); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('width'); ?>">
 <?php _e('This is the width in px of the thumbnail (if choosing the first image):', 'stw'); ?>
 <input size="4" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('thumb'); ?>">
 <input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" <?php if(!empty($thumb)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Check to <strong>not</strong> display the thumbnail of the post.', 'stw'); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('subtitle'); ?>">
 <input id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" <?php if(!empty($subtitle)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Check to display the title of the post <strong>under</strong> the thumbnail (it is above by default).', 'stw'); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('excerpt'); ?>">
 <?php _e('You can write your own teaser for the post or category here. If this stays empty, the post exerpt will be shown (respectively the first 3 sentences of the post).', 'stw'); ?>
 <textarea class="widefat" id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>"><?php echo $excerpt; ?></textarea>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('noshorts'); ?>">
 <input id="<?php echo $this->get_field_id('noshorts'); ?>" name="<?php echo $this->get_field_name('noshorts'); ?>" <?php if(!empty($noshorts)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Check to suppress shortcodes in the widget (in case the content is showing).'); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('readmore'); ?>">
 <input id="<?php echo $this->get_field_id('readmore'); ?>" name="<?php echo $this->get_field_name('readmore'); ?>" <?php if(!empty($readmore)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Check to have an additional &#39;read more&#39; link at the end of the excerpt.'); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('rmtext'); ?>">
 <?php _e('Write here some text for the &#39;read more&#39; link. By default, it is [...]:'); ?>
 <input class="widefat" id="<?php echo $this->get_field_id('rmtext'); ?>" name="<?php echo $this->get_field_name('rmtext'); ?>" type="text" value="<?php echo $rmtext; ?>" />
 </label>
</p>
<p>
 <?php _e('Check, where you want to show the widget. By default, it is showing on the homepage, the category pages and on singel post pages:', 'stw'); ?>
</p>
<fieldset>
<p>
 <label for="<?php echo $this->get_field_id('homepage'); ?>">
 <input id="<?php echo $this->get_field_id('homepage'); ?>" name="<?php echo $this->get_field_name('homepage'); ?>" <?php if(!empty($homepage)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Homepage', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('frontpage'); ?>">
 <input id="<?php echo $this->get_field_id('frontpage'); ?>" name="<?php echo $this->get_field_name('frontpage'); ?>" <?php if(!empty($frontpage)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Frontpage (e.g. a static page as homepage)', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('page'); ?>">
 <input id="<?php echo $this->get_field_id('page'); ?>" name="<?php echo $this->get_field_name('page'); ?>" <?php if(!empty($page)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('&#34;Page&#34; pages', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('category'); ?>">
 <input id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" <?php if(!empty($category)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Category pages', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('single'); ?>">
 <input id="<?php echo $this->get_field_id('single'); ?>" name="<?php echo $this->get_field_name('single'); ?>" <?php if(!empty($single)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Single post pages', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('date'); ?>">
 <input id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" <?php if(!empty($date)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Archive pages', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('tag'); ?>">
 <input id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" <?php if(!empty($tag)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Tag pages', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('attachment'); ?>">
 <input id="<?php echo $this->get_field_id('attachment'); ?>" name="<?php echo $this->get_field_name('attachment'); ?>" <?php if(!empty($attachment)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Attachments', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('taxonomy'); ?>">
 <input id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" <?php if(!empty($taxonomy)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Custom Taxonomy pages (only available, if having a plugin)', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('author'); ?>">
 <input id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" <?php if(!empty($author)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Author pages', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('search'); ?>">
 <input id="<?php echo $this->get_field_id('search'); ?>" name="<?php echo $this->get_field_name('search'); ?>" <?php if(!empty($search)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Search Results', 'stw'); ?>
 </label><br />
 <label for="<?php echo $this->get_field_id('not_found'); ?>">
 <input id="<?php echo $this->get_field_id('not_found'); ?>" name="<?php echo $this->get_field_name('not_found'); ?>" <?php if(!empty($not_found)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('&#34;Not Found&#34;', 'stw'); ?>
 </label>
</p>
<p>
  <label for="checkall">
    <input id="checkall" name="checkall" type="checkbox" />&nbsp;<?php _e('Check all', 'stw'); ?>
  </label>
</p>  
</fieldset>
<script type="text/javascript"><!--
jQuery(document).ready(function() {
	jQuery("#<?php echo $this->get_field_id('excerpt'); ?>").autoResize();
});
--></script>
<?php
 } 
 
function update($new_instance, $old_instance) {
	 
	 $instance = $old_instance;
	 
	 $instance['title'] = strip_tags($new_instance['title']);
	 $instance['name'] = strip_tags($new_instance['name']);
	 $instance['clickable'] = strip_tags($new_instance['clickable']);
	 $instance['article'] = strip_tags($new_instance['article']);
	 $instance['backup'] = strip_tags($new_instance['backup']);	 
	 $instance['thumb'] = strip_tags($new_instance['thumb']);	 
	 $instance['image'] = strip_tags($new_instance['image']);	 
	 $instance['width'] = strip_tags($new_instance['width']);	 
	 $instance['subtitle'] = strip_tags($new_instance['subtitle']);
	 $instance['excerpt'] = $new_instance['excerpt'];
	 $instance['noshorts'] = strip_tags($new_instance['noshorts']);
	 $instance['readmore'] = strip_tags($new_instance['readmore']);
	 $instance['rmtext'] = strip_tags($new_instance['rmtext']);
	 $instance['style'] = strip_tags($new_instance['style']);
	 $instance['homepage'] = strip_tags($new_instance['homepage']);
	 $instance['frontpage'] = strip_tags($new_instance['frontpage']);
	 $instance['page'] = strip_tags($new_instance['page']);
	 $instance['category'] = strip_tags($new_instance['category']);
	 $instance['single'] = strip_tags($new_instance['single']);
	 $instance['date'] = strip_tags($new_instance['date']); 
	 $instance['tag'] = strip_tags($new_instance['tag']);
	 $instance['attachment'] = strip_tags($new_instance['attachment']);
	 $instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
	 $instance['author'] = strip_tags($new_instance['author']);
	 $instance['search'] = strip_tags($new_instance['search']);
	 $instance['not_found'] = strip_tags($new_instance['not_found']);
	 $instance['linktocat'] = strip_tags($new_instance['linktocat']);
	 $instance['cat'] = strip_tags($new_instance['cat']);	 

	 return $instance;
}
 
function widget($args, $instance) {
	
// get the type of page, we're actually on

if (is_front_page()) $stw_pagetype='frontpage';
if (is_home()) $stw_pagetype='homepage';
if (is_page()) $stw_pagetype='page';
if (is_category()) $stw_pagetype='category';
if (is_single()) $stw_pagetype='single';
if (is_date()) $stw_pagetype='date';
if (is_tag()) $stw_pagetype='tag';
if (is_attachment()) $stw_pagetype='attachment';
if (is_tax()) $stw_pagetype='taxonomy';
if (is_author()) $stw_pagetype='author';
if (is_search()) $stw_pagetype='search';
if (is_404()) $stw_pagetype='not_found';

// display only, if said so in the settings of the widget

if ($instance[$stw_pagetype]) {
	
	// the widget is displayed	
	extract( $args );
	
	$title = apply_filters('widget_title', $instance['name']);
	$style = $instance['style'];

	
	$stw_before_widget='<div id="'.$style.'_main_container">';
	$stw_after_widget='</div>';
	$stw_before_title='<div id="'.$style.'_title_container"><h3>';
	$stw_after_title='</h3></div>';
	$stw_before_content='<div id="'.$style.'_text_container">';
	$stw_after_content='</div>';


	echo $stw_before_widget;
	
	if ( $title && $instance['clickable'] ) $title = '<a href="'.get_category_link($instance['cat']).'" class="'.$style.'_title_link">'.$title.'</a>';
	
	if ( $title ) echo $stw_before_title . $title . $stw_after_title;
	
	echo $stw_before_content;
	
	global $wp_query;
		
	$stw_post_id = get_post($instance['article']);
	$stw_post_name = $stw_post_id->post_name;
	
	$stw_post = ($instance['article'] == $wp_query->get( 'p' ) || $stw_post_name == $wp_query->get ( 'name' )) ? 'p='.$instance['backup'] : 'p='.$instance['article'];
	
	if ($stw_post == 'p=') $stw_post = 'numberposts=1';
 
/* This is the actual function of the plugin, it fills the widget with the customized post */

 global $post;
 $stw_posts = get_posts($stw_post);
 foreach($stw_posts as $post) :
 
   setup_postdata($post);
   
   $stw_permalink = ($instance['linktocat']) ? get_category_link($instance['cat']) : get_permalink();
   
   // post tile above thumbnail
   
   if (!$instance['subtitle']) echo '<p><a href="'.$stw_permalink.'">'.the_title('', '', false).'</a></p>';
   
  // thumbnail, if wanted

  if ($instance['image']) {
	  
	  $stw_thumb = '';
	  $stw_image = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	  $stw_thumb = $matches [1] [0];
	  
	  if ($stw_thumb) {
		  $host_string = 'http://' . $_SERVER['HTTP_HOST'];
		  $stw_image_title = $post->post_title;
		  $stw_x = $instance['width'];
		  
		  if (strncmp($stw_thumb, $host_string, strlen($host_string)) == 0) {
			  $realfilepath = $_SERVER['DOCUMENT_ROOT'] . urldecode(substr($stw_thumb, strlen($host_string)));
		  }
		  
		  else {
			  $realfilepath = $stw_thumb;
		  }
		  
		  if (file_exists($realfilepath)) {
			  $stw_size = getimagesize($realfilepath);
			  
			  if (!empty($stw_x)) $stw_y = intval($stw_size[1] / ($stw_size[0] / $stw_x));  
		  }
		  
          echo '<a href="'. $stw_permalink . '"><img title="' . $stw_image_title . '" src="' . $stw_thumb . '" alt="' . $stw_image_title . '" width="' . $stw_x . '" height="' . $stw_y . '" /></a>';
	
   }}
   
   else {
	   
   if (function_exists('has_post_thumbnail')) {
	   
	   if (has_post_thumbnail() && !$instance['thumb']) {
		   
		   ?>
           <a href="<?php echo $stw_permalink; ?>">
		   <?php the_post_thumbnail(); ?>
           </a>
		   <?php
		
	   }}
       
    }
	
	// post title beneath thumbnail
	
   if ($instance['subtitle']) echo '<p><a href="'.$stw_permalink.'">'.the_title('', '', false).'</a></p>';
		   
/* show the excerpt of the post */
	
	$stw_excerpt=str_replace(array("\r\n", "\n", "\r"), '<br />', $instance['excerpt']);
	
	if (!$stw_excerpt) $stw_excerpt=$post->post_excerpt;
	
/* in case the excerpt is not definded by theme or anything else, the first 3 sentences of the content are given */
	
	if (!$stw_excerpt) {
		
		$stw_text=preg_replace('/\[caption(.*?)\[\/caption\]/', '', get_the_content());
																					
		if ($instance['noshorts']) $stw_text=preg_replace('#\[(.*?)\]#', '', $stw_text);
		
		$stw_short=array_slice(preg_split("/([\t.!?]+)/", $stw_text, -1, PREG_SPLIT_DELIM_CAPTURE), 0, 6);
			
		$stw_excerpt=implode($stw_short);
	
	}
	
/* do we want the read more link and do we have text for it? */

	if ($instance['readmore']) {
		
		$stw_rmtext=$instance['rmtext'];
		
		if (!$stw_rmtext) $stw_rmtext='[...]';
		
		$stw_excerpt.=' <a href="'.$stw_permalink.'">'.$stw_rmtext.'</a>';
		
	}
		
	echo '<p>'.do_shortcode($stw_excerpt).'</p>';
	
	endforeach;

 echo $stw_after_content;
 echo $stw_after_widget;
 
 }}
} 

add_action('widgets_init', create_function('', 'return register_widget("Special_Teaser_Widget");'));

endif;

// import laguage files

load_plugin_textdomain('stw', false , basename(dirname(__FILE__)).'/languages');

// Checking for stylesheet on activation

register_activation_hook(  __FILE__, 'start_stw' );

function start_stw() {
	
	add_option('stw_options');
	
	$stw_options=get_option('stw_options');
	
	$current_version='1.0';
	
	if ($current_version != $stw_options['version']) {
		$stw_options['version'] = $current_version;
		update_option('stw_options', $stw_options);
		
	}
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
add_action('admin_menu', 'stw_admin_menu');

function stw_admin_menu() {
	
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
	
	return array (
		
		'style_name' => __('Please enter a name for your style.', 'stw'),
		'main_container' => __('Please style the main container.', 'stw'),
		'title_container' => __('Please style the title container.', 'stw'),
		'title_font' => __('Please style the font of the title.', 'stw'),
		'title_link' => __('Please style the title link.', 'stw'),
		'text_link' => __('Please style the text link.', 'stw'),
		
		);
	
}

/**
 *
 * settings page
 *
 */

$stw_options=get_option('stw_options');

function stw_options_page() {
	
	$stw_options = get_option('stw_options');

	?>
	
<table width="100%" cellpadding="2" cellspacing="0"><tr><td valign="middle" width="380"><h2 style="margin:0 30px 0 0; padding: 5px 0 5px 0;">
Special Teaser Widget <?php _e('Settings', 'stw'); ?></h2></td><td valign="middle">&nbsp;</td>
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
        <p><?php _e('You can create and add different styles for the widget here. If there are no styles defined, the widget will not be functioning. The names of the styles will appear in a drop down menu in the widgetsettings.', 'stw'); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="main_container"><?php _e('Widget Container', 'stw'); ?></label>
        <textarea name="main_container" id="main_container"></textarea>
        <div id="main_container_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Here you give the css styles for the main container of your widget.', 'stw'); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="title_container"><?php _e('Title Container', 'stw'); ?></label>
        <textarea name="title_container" id="title_container"></textarea>
        <div id="title_container_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Here you give the css styles for the title container of your widget.', 'stw'); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="title_font"><?php _e('Title Font', 'stw'); ?></label>
        <textarea name="title_font" id="title_font"></textarea>
        <div id="title_font_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Here you give the css styles for the title container of your widget.', 'stw'); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="title_link"><?php _e('Style for Title Link', 'stw'); ?></label>
        <textarea name="title_link" id="title_link"></textarea>
        <label for="title_link_hover"><?php _e('Hover Style for Title Link', 'stw'); ?></label>
        <textarea name="title_link_hover" id="title_link_hover"></textarea>
        <div id="title_link_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Enter here the style and hover style for links in the title container.', 'stw'); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="text_container"><?php _e('Text Container', 'stw'); ?></label>
        <textarea name="text_container" id="text_container"></textarea>
        </div>
        <div class="stw-container-right">
        <p><?php _e('This is the an optional style. It can make sometimes sense to have the content of the widget in an extra container, which you style here.', 'stw'); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="text_link"><?php _e('Style for Text Links', 'stw'); ?></label>
        <textarea name="text_link" id="text_link"></textarea>
        <label for="text_link_hover"><?php _e('Hover Style for Text Link', 'stw'); ?></label>
        <textarea name="text_link_hover" id="text_link_hover"></textarea>
        <div id="text_link_msg"></div>
        </div>
        <div class="stw-container-right">
        <p><?php _e('Enter here the style and hover style for links in the widget container.', 'stw'); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>
      <div class="stw-container">
        <div class="stw-container-left">
        <label for="image"><?php _e('Style for images', 'stw'); ?></label>
        <textarea name="image" id="image"></textarea>
        <label for="image_hover"><?php _e('Hover Style for images', 'stw'); ?></label>
        <textarea name="image_hover" id="image_hover"></textarea>
        </div>
        <div class="stw-container-right">
        <p><?php _e('This section is optional. But there can be done a lot with a bit of CSS3 to your images.', 'stw'); ?></p>
        </div>
        <div style="clear: both;"></div>
	  </div>      
      <div id="submit-container" class="stw-container" style="background: none repeat scroll 0% 0% transparent; border: medium none;">	
		<p class="submit">
		<input class="save-tab" name="styleformsave" id="styleformsave" value="<?php esc_attr_e('Save Changes'); ?>" type="submit"><img src="<?php admin_url(); ?>/wp-admin/images/wpspin_light.gif" alt="" class="ajaxsave" style="display: none;" />
		<span style="font-weight: bold; color:#243e1f"><?php _e('Save style', 'stw'); ?></span>
		</p></div>
    </form>
</div>
</td>
<td valign="top" width="220">
<div class="stw-sidecar">
<p><?php

_e('In this widget you can see and control the looks of your styles before saving them.', 'stw');

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


/**
 *
 * Saving the styles
 *
 */
 
 function stw_save_style() {
	
	$stw_options = get_option('stw_options');
	
	if (!wp_verify_nonce($_POST['stylenonce'],'save_style')) :
	
		$output = '<p class="error">'.__('Error in Datatransfer.', 'stw').'</p>';
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
		$text_link = $_POST['text_link'];
		$text_link_hover = $_POST['text_link_hover'];
		$image = $_POST['image'];
		$image_hover = $_POST['image_hover'];
		
		$style_id=strtolower(str_replace(' ', '_', $style_name));
	
		if (!empty($stw_options['style']) && array_key_exists($style_id, $stw_options['style']) && $style_action=='create') :
		
			$output = '<p class="error">'.__('A style with that name already exists.', 'stw').'</p>';
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
			$stw_options['style'][$style_id]['text_link']=str_replace("\'", "'", $text_link);
			$stw_options['style'][$style_id]['text_link_hover']=str_replace("\'", "'", $text_link_hover);
			$stw_options['style'][$style_id]['image']=str_replace("\'", "'", $image);
			$stw_options['style'][$style_id]['image_hover']=str_replace("\'", "'", $image_hover);
			update_option('stw_options', $stw_options);
			stw_write_css();
	
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
            $output .= '<tr>
                            <td>' . $style['style_name'] . '</td>
                            <td><a  id="'.$style_id.'" href="#" class="edit">'.__('Edit', 'stw').'</td>
							<td><a  id="'.$style_id.'" href="#" class="copy">'.__('Copy', 'stw').'</td>
                            <td><a  id="'.$style_id.'" href="#" class="delete">'.__('Delete', 'stw').'</a>
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
	
	$css_text="@charset \"UTF-8\";\r\n/* CSS Document */\r\n\r\n";
	
	foreach ($stw_styles as $style_id => $class):
	
		$css_text.="#".$style_id."_main_container {\r\n\v".$class['main_container']."\r\n}\r\n";
		$css_text.="#".$style_id."_title_container {\r\n".$class['title_container']."\r\n}\r\n";
		$css_text.="#".$style_id."_title_container h3 {\r\n".$class['title_font']."\r\n}\r\n";
		$css_text.="#".$style_id."_title_container a {\r\n".$class['title_link']."\r\n}\r\n";
		if (!empty($class['title_link_hover'])) $css_text.="#".$style_id."_title_container a:hover {\r\n".$class['title_link_hover']."\r\n}\r\n";
		if (!empty($class['text_container'])) $css_text.="#".$style_id."_text_container {\r\n".$class['text_container']."\r\n}\r\n";
		$css_text.="#".$style_id."_text_container a {\r\n".$class['text_link']."\r\n}\r\n";
		if (!empty($class['text_link_hover'])) $css_text.="#".$style_id."_text_container a:hover {\r\n".$class['text_link_hover']."\r\n}\r\n";
		if (!empty($class['image'])) $css_text.="#".$style_id."_text_container img {\r\n".$class['image']."\r\n}\r\n";
		if (!empty($class['image_hover'])) $css_text.="#".$style_id."_text_container img:hover {\r\n".$class['image_hover']."\r\n}\r\n";

	endforeach;
	
	$stw_css_file = WP_PLUGIN_DIR . '/special-teaser-widget/css/stw-css.css';
	
	if (!file_exists($stw_css_file)):
		
		$handler = fopen($stw_css_file , "a+");
		fwrite($handler , $css_text);
		fclose($handler);
		
	else:
	
		file_put_contents($stw_css_file, $css_text);
		
	endif;
	
 }

?>