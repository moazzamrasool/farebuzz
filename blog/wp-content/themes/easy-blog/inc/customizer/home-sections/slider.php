<?php
/**
 * Featured Slider options.
 *
 * @package Easy Blog
 */

$default = easy_blog_get_default_theme_options();

// Featured Slider Section
$wp_customize->add_section( 'section_featured_slider',
	array(
		'title'      => __( 'Slider Posts', 'easy-blog' ),
		'priority'   => 100,
		'capability' => 'edit_theme_options',
		'panel'      => 'home_page_panel',
		)
);

// Enable Featured Slider Section
$wp_customize->add_setting('theme_options[enable_featured_slider]', 
	array(
	'default' 			=> $default['enable_featured_slider'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',
	'sanitize_callback' => 'easy_blog_sanitize_checkbox'
	)
);

$wp_customize->add_control('theme_options[enable_featured_slider]', 
	array(		
	'label' 	=> __('Enable Slider Posts Section', 'easy-blog'),
	'section' 	=> 'section_featured_slider',
	'settings'  => 'theme_options[enable_featured_slider]',
	'type' 		=> 'checkbox',	
	)
);

// Number of items
$wp_customize->add_setting('theme_options[number_of_slider_items]', 
	array(
	'default' 			=> $default['number_of_slider_items'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'easy_blog_sanitize_number_range'
	)
);

$wp_customize->add_control('theme_options[number_of_slider_items]', 
	array(
	'label'       => __('Number Of Slides', 'easy-blog'),
	'description' => __('Save & Refresh the customizer to see its effect. Maximum is 6.', 'easy-blog'),
	'section'     => 'section_featured_slider',   
	'settings'    => 'theme_options[number_of_slider_items]',		
	'type'        => 'number',
	'active_callback' => 'easy_blog_slider_active',
	'input_attrs' => array(
			'min'	=> 1,
			'max'	=> 6,
			'step'	=> 1,
		),
	)
);

// Content Type
$wp_customize->add_setting('theme_options[slider_content_type]', 
	array(
	'default' 			=> $default['slider_content_type'],
	'type'              => 'theme_mod',
	'capability'        => 'edit_theme_options',	
	'sanitize_callback' => 'easy_blog_sanitize_select'
	)
);

$wp_customize->add_control('theme_options[slider_content_type]', 
	array(
	'label'       => __('Content Type', 'easy-blog'),
	'section'     => 'section_featured_slider',   
	'settings'    => 'theme_options[slider_content_type]',		
	'type'        => 'select',
	'active_callback' => 'easy_blog_slider_active',
	'choices'	  => array(
			'slider_page'	  => __('Page','easy-blog'),
			'slider_post'	  => __('Post','easy-blog'),
		),
	)
);

$number_of_slider_items = easy_blog_get_option( 'number_of_slider_items' );

for( $i=1; $i<=$number_of_slider_items; $i++ ){

	// Page
	$wp_customize->add_setting('theme_options[slider_page_'.$i.']', 
		array(
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',	
		'sanitize_callback' => 'easy_blog_dropdown_pages'
		)
	);

	$wp_customize->add_control('theme_options[slider_page_'.$i.']', 
		array(
		'label'       => sprintf( __('Select Page #%1$s', 'easy-blog'), $i),
		'section'     => 'section_featured_slider',   
		'settings'    => 'theme_options[slider_page_'.$i.']',		
		'type'        => 'dropdown-pages',
		'active_callback' => 'easy_blog_slider_page',
		)
	);

	// Posts
	$wp_customize->add_setting('theme_options[slider_post_'.$i.']', 
		array(
		'type'              => 'theme_mod',
		'capability'        => 'edit_theme_options',	
		'sanitize_callback' => 'easy_blog_dropdown_pages'
		)
	);

	$wp_customize->add_control('theme_options[slider_post_'.$i.']', 
		array(
		'label'       => sprintf( __('Select Post #%1$s', 'easy-blog'), $i),
		'section'     => 'section_featured_slider',   
		'settings'    => 'theme_options[slider_post_'.$i.']',		
		'type'        => 'select',
		'choices'	  => easy_blog_dropdown_posts(),
		'active_callback' => 'easy_blog_slider_post',
		)
	);
}