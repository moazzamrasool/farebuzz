<?php
/**
 * Default theme options.
 *
 * @package Easy Blog
 */

if ( ! function_exists( 'easy_blog_get_default_theme_options' ) ) :

	/**
	 * Get default theme options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default theme options.
	 */
function easy_blog_get_default_theme_options() {

	$defaults = array();

	// Top Bar
	$defaults['show_header_contact_info'] 		= false;
    $defaults['show_header_social_links'] 		= false;
    $defaults['header_social_links']			= array();

    // Homepage Options
	$defaults['enable_frontpage_content'] 		= false;

	// Featured Slider Section
	$defaults['enable_featured_slider']			= false;
	$defaults['number_of_slider_items']			= 3;
	$defaults['slider_content_type']			= 'slider_page';

	// Featured Posts Section	
	$defaults['enable_featured_posts_section']	= false;
	$defaults['featured_posts_section_title']	= esc_html__( 'Featured Posts', 'easy-blog' );
	$defaults['number_of_featured_posts_items']	= 3;
	$defaults['featured_posts_content_type']	= 'featured_posts_page';

	// Latest Posts Section
	$defaults['enable_blog_section']			= false;
	$defaults['blog_section_title']				= esc_html__( 'Latest Posts', 'easy-blog' );
	$defaults['blog_category']	   				= 0; 
	$defaults['blog_number']					= 6;	

	//General Section
	$defaults['readmore_text']					= esc_html__('Read More','easy-blog');
	$defaults['your_latest_posts_title']		= esc_html__('Blog','easy-blog');
	$defaults['excerpt_length']					= 15;
	$defaults['layout_options_blog']			= 'no-sidebar';
	$defaults['layout_options_archive']			= 'no-sidebar';
	$defaults['layout_options_page']			= 'no-sidebar';	
	$defaults['layout_options_single']			= 'right-sidebar';	

	//Footer section 		
	$defaults['copyright_text']					= esc_html__( 'Copyright &copy; All rights reserved.', 'easy-blog' );

	// Pass through filter.
	$defaults = apply_filters( 'easy_blog_filter_default_theme_options', $defaults );
	return $defaults;
}

endif;

/**
*  Get theme options
*/
if ( ! function_exists( 'easy_blog_get_option' ) ) :

	/**
	 * Get theme option
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function easy_blog_get_option( $key ) {

		$default_options = easy_blog_get_default_theme_options();
		if ( empty( $key ) ) {
			return;
		}

		$theme_options = (array)get_theme_mod( 'theme_options' );
		$theme_options = wp_parse_args( $theme_options, $default_options );

		$value = null;

		if ( isset( $theme_options[ $key ] ) ) {
			$value = $theme_options[ $key ];
		}

		return $value;

	}

endif;