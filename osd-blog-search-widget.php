<?php
/*
Plugin Name: OSD Blog Search Widget
Plugin URI: http://outsidesource.com
Description: Adds a widget that adds a search form for searching blog posts only.
Version: 1.5
Author: OSD Web Development Team
Text Domain: osd-blog-search-widget
License: GPL2v2
*/

class osd_blog_search_widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'osd_blog_search_widget', // Base ID
			/* translators: Widget Name */
			__('OSD Blog Search', 'osd-blog-search-widget'), // Name
			/* translators: Widget Description */
			array('description' => __('Search form for searching blog posts only.', 'osd-blog-search-widget')) // Args
		);

		// Add front-end filters
        if (!is_admin()) {
            add_shortcode("osd_blog_search", array($this, "replace_shortcode"));
        }
	}
 
 	// Output to the sidebars
	public function widget($args, $instance) {
		extract($args);	 
		$title = apply_filters('widget_title', $instance['title']);
		$extra_menu_args = array('container' => '', 'walker_args' => array());

		echo $before_widget;
 
		if (!empty($title)) {
			echo $before_title . $title . $after_title;
		}
		
		echo "<div class='search-form' id='osd-blog-search'>
				<form method='get' action='".home_url('/')."'>
					<input type='hidden' name='post_type' value='post' />
					<input type='text' name='s' id='s' value='' placeholder='".$instance['placeholder']."' />
					<input id='osd-search-submit' class='search-button' type='image' alt='Search' src='".plugins_url()."/osd-blog-search-widget/images/search.svg' />
				</form>
			</div>"; 
		echo $after_widget;
	}
 
 	// Admin menu options
	public function form($instance) {
		?>
		<p>
			<?php /* translators: Widget Field: Title */ ?>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'osd-blog-search-widget'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance["title"] ); ?>" />
		</p>
		<p>
			<?php /* translators: Widget Field: Placeholder */ ?>
			<label for="<?php echo $this->get_field_id( 'placeholder' ); ?>"><?php _e('Placeholder:', 'osd-blog-search-widget'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'placeholder' ); ?>" name="<?php echo $this->get_field_name( 'placeholder' ); ?>" type="text" value="<?php echo esc_attr( $instance["placeholder"] ); ?>" />
		</p>
        <?php
	}
 
 	// Update the instance
	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['placeholder'] = strip_tags($new_instance['placeholder']);
		return $instance;
	}

	// Add functionality to the custom search widget
	public static function blog_search_filter($query) {
		if ($query->is_search && !is_admin()) {
			$post_type = $_GET['post_type'];
			if (!$post_type) {
				$post_type = 'any';
			}
			$query->set('post_type', $post_type);
		}
		return $query;
	}

    // Shortcode function (everything runs on the shortcode)
    function replace_shortcode($atts = array()) {
    	$placeholder = (isset($atts['placeholder'])) ? $atts['placeholder'] : '';
    	$class = (isset($atts['class'])) ? " ".$atts['class'] : '';

        $html = "<div class='search-form{$class}' id='osd-blog-search'>
				<form method='get' action='".home_url('/')."'>
					<input type='hidden' name='post_type' value='post' />
					<input type='text' name='s' id='s' value='' placeholder='{$placeholder}' />
					<input id='osd-search-submit' class='search-button' type='image' alt='Search' src='".plugins_url()."/osd-blog-search-widget/images/search.svg' />
				</form>
			</div>";

        return $html;
    }
}
add_action('widgets_init', create_function('', 'register_widget("osd_blog_search_widget");'));
add_filter('pre_get_posts', array("osd_blog_search_widget", 'blog_search_filter'));
load_plugin_textdomain('osd-blog-search-widget', false, basename(dirname(__FILE__))."/lang/");