<?php
/**
 * Home Page Options.
 *
 * @package Easy Blog
 */

$default = easy_blog_get_default_theme_options();

// Add Panel.
$wp_customize->add_panel( 'home_page_panel',
	array(
	'title'      => __( 'Front Page Sections', 'easy-blog' ),
	'priority'   => 100,
	'capability' => 'edit_theme_options',
	)
);

/**
* Section Customizer Options.
*/
require get_template_directory() . '/inc/customizer/home-sections/slider.php';
require get_template_directory() . '/inc/customizer/home-sections/featured-posts.php';
require get_template_directory() . '/inc/customizer/home-sections/blog.php';

