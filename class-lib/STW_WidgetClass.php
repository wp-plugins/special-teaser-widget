<?php

/**
 *
 * Class STW Widget
 *
 * @ Special Teaser Widget
 *
 * building the actual widget
 *
 */
 
class Special_Teaser_Widget extends WP_Widget {
	
function Special_Teaser_Widget() {
	
	global $stw_language_file;

	$widget_opts = array( 'description' => __('Put a featured post into the widget and choose one of the styles to get it into the attention af your readers.', $stw_language_file) );
	$control_opts = array( 'width' => 400 );
	
	parent::WP_Widget(false, $name = 'Special Teaser Widget', $widget_opts, $control_opts);

}
    
	
 
function form($instance) {
	
	global $stw_language_file;
	
	// setup some default settings
	
	$defaults = array( 'homepage' => 1, 'category' => 1, 'single' => 1 );
	
	$instance = wp_parse_args( (array) $instance, $defaults );
	
	$title = esc_attr($instance['title']);
	$name = esc_attr($instance['name']);
	$clickable = esc_attr($instance['clickable']);
	$thumb = esc_attr($instance['thumb']);
	$image = esc_attr($instance['image']);
	$article = esc_attr($instance['article']);
	$backup = esc_attr($instance['backup']);
	$width = esc_attr($instance['width']);
	$headline = esc_attr($instance['headline']);	
	$excerpt = esc_attr($instance['excerpt']);
	$linespace = esc_attr($instance['linespace']);	
	$notext = esc_attr($instance['notext']);	
	$noshorts = esc_attr($instance['noshorts']);
	$readmore = esc_attr($instance['readmore']);
	$rmtext = esc_attr($instance['rmtext']);
	$adsense = esc_attr($instance['adsense']);
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
	$cat_selected=esc_attr($instance['cat_selected']);
	
	$categories = get_categories('hide_empty=0');
	$features = get_posts('numberposts=-1');
	$options = get_option('stw_options');
	$styles = $options['style'];
	
 ?>
 
<p>
 <label for="<?php echo $this->get_field_id('name'); ?>">
 <?php _e('Title (will be displayed in blog):', $stw_language_file); ?>
 <input class="widefat" id="<?php echo $this->get_field_id('name'); ?>" name="<?php echo $this->get_field_name('name'); ?>" type="text" value="<?php echo $name; ?>" />
 </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e('Choose here the style of your widget.', $stw_language_file); ?></label>
  <select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" class="widefat" style="width:100%;">
  <?php
	if (empty($style)) echo '<option value="">'.__('Choose style', $stw_language_file).'</option>';
	
	foreach ( $styles as $style_id => $widget_style) :
	
		$selected = ( $style_id == $style ) ? ' selected="selected"' : '' ;
		$option = '<option value="'.$style_id.'"'.$selected.'>'.$widget_style['style_name'].'</option>';
		echo $option;
	
	endforeach;
  ?>
  </select>
</p>
<p>
 <label for="<?php echo $this->get_field_id('clickable'); ?>">
 <input id="<?php echo $this->get_field_id('clickable'); ?>" name="<?php echo $this->get_field_name('clickable'); ?>" <?php if(!empty($clickable)) echo 'checked="checked"'; ?> type="checkbox" />&nbsp;<?php _e('Link the widget title to a category', $stw_language_file); ?>
 </label> 
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'cat_selected' ); ?>"><?php _e('Category:', $stw_language_file); ?></label>
  <select id="<?php echo $this->get_field_id( 'cat_selected' ); ?>" name="<?php echo $this->get_field_name( 'cat_selected' ); ?>" class="widefat" style="width:100%;">
  <?php
	if (empty($cat)) echo '<option value="">'.__('Choose category', $stw_language_file).'</option>';
	
	foreach ( $categories as $cat ) :
	
		$selected = ( $cat->cat_ID == $cat_selected ) ? ' selected="selected"' : '' ;
		$option = '<option value="'.$cat->cat_ID.'"'.$selected.'>'.$cat->cat_name.'</option>';
		echo $option;

	endforeach;
	
  ?>
  </select>
</p>
<p>
 <label for="<?php echo $this->get_field_id('title'); ?>">
 <?php _e('Name (internal widgettitle):', $stw_language_file); ?>
 <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
 </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'article' ); ?>"><?php _e('Choose here the post, you want to appear in the widget.', $stw_language_file); ?></label>
  <select id="<?php echo $this->get_field_id( 'article' ); ?>" name="<?php echo $this->get_field_name( 'article' ); ?>" class="widefat" style="width:100%;">
  <?php
	if (empty($article)) echo '<option value="">'.__('Take a random post', $stw_language_file).'</option>';
	
	foreach ( $features as $feature ) :
		
		$selected = ( $feature->ID == $article ) ? ' selected="selected"' : '' ;
		$option = '<option value="'.$feature->ID.'"'.$selected.'>'.$feature->post_title.'</option>';
		echo $option;
	
	endforeach;
  ?>
  </select>
</p>
<p>
  <label for="<?php echo $this->get_field_id( 'backup' ); ?>"><?php _e('Choose here the backup post. It will appear, when a single post page shows the featured article.', $stw_language_file); ?></label>
  <select id="<?php echo $this->get_field_id( 'backup' ); ?>" name="<?php echo $this->get_field_name( 'backup' ); ?>" class="widefat" style="width:100%;">
  <?php
	if (empty($backup)) echo '<option value="">'.__('Take a random post', $stw_language_file).'</option>';
	
	foreach ( $features as $feature ) :
		
		$selected = ( $feature->ID == $backup ) ? ' selected="selected"' : '' ;
		$option = '<option value="'.$feature->ID.'"'.$selected.'>'.$feature->post_title.'</option>';
		echo $option;
	
	endforeach;
  ?>
  </select>
</p>
<p>
 <label for="<?php echo $this->get_field_id('linktocat'); ?>">
 <input id="<?php echo $this->get_field_id('linktocat'); ?>" name="<?php echo $this->get_field_name('linktocat'); ?>" type="checkbox" value="1" <?php echo checked( 1, $linktocat, false ); ?> />&nbsp;<?php _e('Check to link to the same category as the title is linking to.', $stw_language_file); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('image'); ?>">
 <input id="<?php echo $this->get_field_id('image'); ?>" name="<?php echo $this->get_field_name('image'); ?>" type="checkbox" value="1" <?php echo checked( 1, $image, false ); ?> />&nbsp;<?php _e('Check to get the first image of the post as thumbnail.', $stw_language_file); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('width'); ?>">
 <?php _e('This is the width in px of the thumbnail (if choosing the first image):', $stw_language_file); ?>
 <input size="4" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('thumb'); ?>">
 <input id="<?php echo $this->get_field_id('thumb'); ?>" name="<?php echo $this->get_field_name('thumb'); ?>" type="checkbox" value="1" <?php echo checked( 1, $thumb, false ); ?> />&nbsp;<?php _e('Check to <strong>not</strong> display the thumbnail of the post.', $stw_language_file); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('headline'); ?>"><?php _e('Choose, whether or not to display the title and whether it comes above or under the thumbnail.', $stw_language_file); ?></label>
 <select id="<?php echo $this->get_field_id('headline'); ?>" name="<?php echo $this->get_field_name('headline'); ?>" class="widefat" style="width:100%;">
 <?php
 	$items = array ('top' => __('Above thumbnail', $stw_language_file) , 'bottom' => __('Under thumbnail', $stw_language_file), 'none' => __('Don&#39;t show title', $stw_language_file));
	foreach ($items as $key => $val) :
	
		$selected = ($key == $headline) ? ' selected="selected"' : '' ;
		$option = '<option value="'.$key.'"'.$selected.'>'.$val.'</option>';
		echo $option;
	
	endforeach;
 ?>
 </select>
</p>
<p>
 <label for="<?php echo $this->get_field_id('excerpt'); ?>">
 <?php _e('You can write your own teaser for the post or category here. If this stays empty, the post exerpt will be shown (respectively the first 3 sentenses of the post).', $stw_language_file); ?>
 <textarea class="widefat" id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>"><?php echo $excerpt; ?></textarea>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('linespace'); ?>">
 <input id="<?php echo $this->get_field_id('linespace'); ?>" name="<?php echo $this->get_field_name('linespace'); ?>" type="checkbox" value="1" <?php echo checked( 1, $linespace, false ); ?> />&nbsp;<?php _e('Check to have each sentense in a new line.', $stw_language_file); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('notext'); ?>">
 <input id="<?php echo $this->get_field_id('notext'); ?>" name="<?php echo $this->get_field_name('notext'); ?>" type="checkbox" value="1" <?php echo checked( 1, $notext, false ); ?> />&nbsp;<?php _e('Check to <strong>not</strong> display the excerpt of the post.', $stw_language_file); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('noshorts'); ?>">
 <input id="<?php echo $this->get_field_id('noshorts'); ?>" name="<?php echo $this->get_field_name('noshorts'); ?>" type="checkbox" value="1" <?php echo checked( 1, $noshorts, false ); ?> />&nbsp;<?php _e('Check to suppress shortcodes in the widget (in case the content is showing).', $stw_language_file); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('readmore'); ?>">
 <input id="<?php echo $this->get_field_id('readmore'); ?>" name="<?php echo $this->get_field_name('readmore'); ?>" type="checkbox" value="1" <?php echo checked( 1, $readmore, false ); ?> />&nbsp;<?php _e('Check to have an additional &#39;read more&#39; link at the end of the excerpt.', $stw_language_file); ?>
 </label>
</p>
<p>
 <label for="<?php echo $this->get_field_id('rmtext'); ?>">
 <?php _e('Write here some text for the &#39;read more&#39; link. By default, it is', $stw_language_file).' [&#8230;]'; ?>
 <input class="widefat" id="<?php echo $this->get_field_id('rmtext'); ?>" name="<?php echo $this->get_field_name('rmtext'); ?>" type="text" value="<?php echo $rmtext; ?>" />
 </label>
</p>
<?php
if (AE_AD_TAGS==1) :
?>
<p>
 <label for="<?php echo $this->get_field_id('adsense'); ?>">
 <input id="<?php echo $this->get_field_id('adsense'); ?>" name="<?php echo $this->get_field_name('adsense'); ?>" type="checkbox" value="1" <?php echo checked( 1, $adsense, false ); ?> />&nbsp;<?php _e('Check if you want to invert the Google AdSense Tags that are defined with the Ads Easy Plugin. E.g. when they are turned off for the sidebar, they will appear in the widget.', $stw_language_file); ?>
 </label>
</p>
<?php
endif;
?>
<p>
  <?php _e('Check, where you want to show the widget. By default, it is showing on the homepage and the category pages:', $stw_language_file); ?>
</p>
<fieldset>
<p>
  <label for="<?php echo $this->get_field_id('homepage'); ?>">
    <input id="<?php echo $this->get_field_id('homepage'); ?>" name="<?php echo $this->get_field_name('homepage'); ?>" type="checkbox" value="1" <?php echo checked( 1, $homepage, false ); ?> />&nbsp;<?php _e('Homepage', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('frontpage'); ?>">
    <input id="<?php echo $this->get_field_id('frontpage'); ?>" name="<?php echo $this->get_field_name('frontpage'); ?>" type="checkbox" value="1" <?php echo checked( 1, $frontpage, false ); ?> />&nbsp;<?php _e('Frontpage (e.g. a static page as homepage)', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('page'); ?>">
    <input id="<?php echo $this->get_field_id('page'); ?>" name="<?php echo $this->get_field_name('page'); ?>" type="checkbox" value="1" <?php echo checked( 1, $page, false ); ?> />&nbsp;<?php _e('&#34;Page&#34; pages', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('category'); ?>">
    <input id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="checkbox" value="1" <?php echo checked( 1, $category, false ); ?> />&nbsp;<?php _e('Category pages', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('single'); ?>">
    <input id="<?php echo $this->get_field_id('single'); ?>" name="<?php echo $this->get_field_name('single'); ?>" type="checkbox" value="1" <?php echo checked( 1, $single, false ); ?> />&nbsp;<?php _e('Single post pages', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('date'); ?>">
    <input id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="checkbox" value="1" <?php echo checked( 1, $date, false ); ?> />&nbsp;<?php _e('Archive pages', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('tag'); ?>">
    <input id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>"  type="checkbox" value="1" <?php echo checked( 1, $tag, false ); ?> />&nbsp;<?php _e('Tag pages', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('attachment'); ?>">
    <input id="<?php echo $this->get_field_id('attachment'); ?>" name="<?php echo $this->get_field_name('attachment'); ?>" type="checkbox" value="1" <?php echo checked( 1, $attachment, false ); ?> />&nbsp;<?php _e('Attachments', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('taxonomy'); ?>">
    <input id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" type="checkbox" value="1" <?php echo checked( 1, $taxonomy, false ); ?> />&nbsp;<?php _e('Custom Taxonomy pages (only available, if having a plugin)', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('author'); ?>">
    <input id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" type="checkbox" value="1" <?php echo checked( 1, $author, false ); ?> />&nbsp;<?php _e('Author pages', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('search'); ?>">
    <input id="<?php echo $this->get_field_id('search'); ?>" name="<?php echo $this->get_field_name('search'); ?>" type="checkbox" value="1" <?php echo checked( 1, $search, false ); ?> />&nbsp;<?php _e('Search Results', $stw_language_file); ?>
  </label><br />
  <label for="<?php echo $this->get_field_id('not_found'); ?>">
    <input id="<?php echo $this->get_field_id('not_found'); ?>" name="<?php echo $this->get_field_name('not_found'); ?>" type="checkbox" value="1" <?php echo checked( 1, $not_found, false ); ?> />&nbsp;<?php _e('&#34;Not Found&#34;', $stw_language_file); ?>
  </label>
</p>
<p>
  <label for="<?php echo $this->get_field_id('checkall'); ?>">
    <input id="<?php echo $this->get_field_id('checkall'); ?>" name="<?php echo $this->get_field_name('checkall'); ?>" type="checkbox" />&nbsp;<?php _e('Check all', $stw_language_file); ?>
  </label>
</p>    
</fieldset>
<script type="text/javascript"><!--
jQuery(document).ready(function() {
	jQuery("#<?php echo $this->get_field_id('excerpt'); ?>").autoResize();
});
--></script>
<?php
} // form
 
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
	 $instance['headline'] = strip_tags($new_instance['headline']);
	 $instance['excerpt'] = $new_instance['excerpt'];
	 $instance['linespace'] = strip_tags($new_instance['linespace']);
	 $instance['notext'] = strip_tags($new_instance['notext']);
	 $instance['noshorts'] = strip_tags($new_instance['noshorts']);
	 $instance['readmore'] = strip_tags($new_instance['readmore']);
	 $instance['rmtext'] = strip_tags($new_instance['rmtext']);
	 $instance['adsense'] = strip_tags($new_instance['adsense']);	 
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
	 $instance['cat_selected'] = strip_tags($new_instance['cat_selected']);	 

	 return $instance;
}
 
function widget($args, $instance) {
	
global $stw_language_file;
	
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

if ($instance[$stw_pagetype]) :
	
	// the widget is displayed	
	extract( $args );
	
	$title = apply_filters('widget_title', $instance['name']);
	$style = $instance['style'];
	$widget_nr = explode('-', $widget_id);
	$unique = $widget_nr[1];
	
	
	$stw_before_widget='<div id="'.$style.'_main_container-'.$unique.'">';
	$stw_after_widget='</div>';
	$stw_before_title='<div id="'.$style.'_title_container-'.$unique.'"><h3>';
	$stw_after_title='</h3></div>';
	$stw_before_content='<div id="'.$style.'_text_container-'.$unique.'">';
	$stw_after_content='</div>';
	
	// hooking into ads easy for the google tags

	if (AE_AD_TAGS == 1 && $instance['adsense']) :
		
		$ae_options = get_option('ae_options');
		
		do_action('google_end_tag');
		
		if ($ae_options['ae_sidebar']==1) do_action('google_ignore_tag');
	
		else do_action('google_start_tag');
		
	endif;
	
	// widget starts
	
	echo $stw_before_widget;
	
	// widget title and does it link?
	
	if ( $title && $instance['clickable'] ) $title = '<a href="'.get_category_link($instance['cat_selected']).'" title="'.__('Permalink to', $stw_language_file).' '.get_cat_name($instance['cat_selected']).'">'.$title.'</a>';
	
	if ( $title ) echo $stw_before_title . $title . $stw_after_title;
	
	echo $stw_before_content;
	
	global $wp_query;
		
	$stw_post_id = get_post($instance['article']);
	$stw_post_name = $stw_post_id->post_name;
	
	$stw_post = ($instance['article'] == $wp_query->get( 'p' ) || $stw_post_name == $wp_query->get ( 'name' )) ? 'p='.$instance['backup'] : 'p='.$instance['article'];
	
	if ($stw_post == 'p=') $stw_post = 'numberposts=1&orderby=rand';
 
	/* This is the actual function of the plugin, it fills the widget with the customized post */
	
	global $post;
	$stw_posts = get_posts($stw_post);
	foreach($stw_posts as $post) :
	
	$imagetags = A5_ImageTags::get_tags($post, $stw_language_file);
	
	$stw_image_alt = $imagetags['image_alt'];
	$stw_image_title = $imagetags['image_title'];
	$stw_title_tag = $imagetags['title_tag'];
   
	// get the headline, if wanted
	
	if ($instance['headline'] != 'none') :
		
		$stw_permalink = ($instance['linktocat']) ? get_category_link($instance['cat_selected']) : get_permalink();
		$stw_headline_tag = ($instance['linktocat']) ? __('Permalink to', $stw_language_file).' '.get_cat_name($instance['cat_selected']) : $stw_title_tag;
		
		$stw_headline = '<p><a href="'.$stw_permalink.'" title="'.$stw_headline_tag.'">'.$post->post_title.'</a></p>';
		
	endif;
   
	// thumbnail, if wanted
	
	if (!$instance['thumb']) :
		
		if ($instance['image']) : 
		
			$args = array (
			'content' => $post->post_content,
			'width' => $instance['width']
			);
			   
			$stw_image = new A5_Thumbnail;
		
			$stw_image_info = $stw_image->get_thumbnail($args);
			
			$stw_thumb = $stw_image_info['thumb'];
			
			$stw_width = $stw_image_info['thumb_width'];
	
			$stw_height = $stw_image_info['thumb_height'];
			
			if ($stw_thumb) :
			
				if ($stw_width) $stw_img_tag = '<img title="'.$stw_image_title.'" src="'.$stw_thumb.'" alt="'.$stw_image_alt.'" width="'.$stw_width.'" height="'.$stw_height.'" />';
					
				else $stw_img_tag = '<img title="'.$stw_image_title.'" src="'.$stw_thumb.'" alt="'.$stw_image_alt.'" style="maxwidth: '.$instance['width'].';" />';
				
			endif;
			
		else :
		
			$stw_img_tag = get_the_post_thumbnail();
		
		endif;
		
			$stw_image = '<a href="'.get_permalink().'">'.$stw_img_tag.'</a>'.$eol.'<div style="clear: both;"></div>'.$eol;
		
	endif;
	
	// excerpt, if wanted
	
	if (!$instance['notext']) :
	
	$rmtext = ($instance['rmtext']) ? $instance['rmtext'] : '[&#8230;]';
	
	$shortcode = ($instance['noshorts']) ? false : 1;
	
		$args = array(
		'usertext' => $instance['excerpt'],
		'excerpt' => $post->post_excerpt,
		'content' => $post->post_content,
		'shortcode' => $shortcode,
		'linespace' => $instance['linespace'],
		'link' => get_permalink(),
		'title' => $stw_title_tag,
		'readmore' => $instance['readmore'],
		'rmtext' => $rmtext
		);
	
		$stw_text = A5_Excerpt::get_excerpt($args);
	
	endif;
	
	// writing the stuff in the widget
	
	if ($instance['headline'] == 'top') echo $stw_headline.$eol;
	
	if (!$instance['thumb']) echo $stw_image;
	
	if ($instance['headline'] == 'bottom') echo $stw_headline.$eol;
	
	if (!$instance['notext']) echo '<p>'.do_shortcode($stw_text).'</p>'.$eol;
	
	endforeach;
	
	// hooking into ads easy for the google tags
	
	echo $stw_after_content;
	echo $stw_after_widget;
	
	if (AE_AD_TAGS == 1 && $instance['adsense']) :
		
		$ae_options = get_option('ae_options');
		
		do_action('google_end_tag');
		
		if ($ae_options['ae_sidebar']==1) do_action('google_start_tag');
	
		else do_action('google_ignore_tag');
		
	endif;

endif;
} // widget
} // class

add_action('widgets_init', create_function('', 'return register_widget("Special_Teaser_Widget");'));
 
?>