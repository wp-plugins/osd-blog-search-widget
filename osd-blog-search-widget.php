<?php
	/*
	Plugin Name: OSD Blog Search Widget
	Plugin URI: http://outsidesource.com
	Description: A plugin that adds a new widget that will add a search form for blog posts only.
	Version: 1.0
	Author: OSD Web Development Team
	Author URI: http://outsidesource.com
	License: GPL2v2
	*/
	
	class osd_blog_search_widget extends WP_Widget {
	 
		public function __construct() {
			parent::__construct(
				'osd_blog_search_widget', // Base ID
				'OSD Blog Search Widget', // Name
				array('description' => __('Search form for blog posts only.')) // Args
			);
		}
	 
	 	//output to the sidebars
		public function widget($args, $instance) {
			extract($args);	 
			$title = apply_filters('widget_title', $instance['title']);
			$extra_menu_args = array('container' => '', 'menu' => $instance['menu_item'], 'walker_args' => array());

			echo $before_widget;
	 
			if(!empty($title)) {
				echo $before_title . $title . $after_title;
			}
			
			echo __("<div class='search-form' id='osd-blog-search'>
						<form method='get' action='".home_url('/')."'>
							<input type='hidden' name='post_type' value='post' />
							<input type='text' name='s' id='s' value='' placeholder='Search blog' />
							<input id='osd-search-submit' class='search-button' type='image' alt='Search' src='".get_bloginfo('template_url')."/images/search.svg' />
						</form>
					</div>");
			
	 
			echo $after_widget;
		}
	 
	 	//admin menu options
		public function form($instance) {
			$title = isset($instance['title']) ? $instance['title'] : '';
	 
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
            <?php
		}
	 
	 	//load the instance
		public function update($new_instance, $old_instance) {
			$instance = array();
			$instance['title'] = strip_tags($new_instance['title']);
	 
			return $instance;
		}
	}
	add_action('widgets_init', create_function('', 'register_widget("osd_blog_search_widget");'));
	
	//add functionality to the custom search widget
	function osd_blog_search_filter($query) {
		if($query->is_search && !is_admin()) {
			$post_type = $_GET['post_type'];
			if(!$post_type) {
				$post_type = 'any';
			}
			$query->set('post_type', $post_type);
		}
		return $query;
	}
	add_filter('pre_get_posts','osd_blog_search_filter');
?>