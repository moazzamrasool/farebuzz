<?php

$default = easy_blog_get_default_theme_options();
/**
* Add Header Top Panel
*/
$wp_customize->add_panel( 'header_top_panel', array(
    'title'          => __( 'Header Top', 'easy-blog' ),
    'priority'       => 20,
    'capability'     => 'edit_theme_options',
) );

/** Header contact info section */
$wp_customize->add_section(
    'header_contact_info_section',
    array(
        'title'    => __( 'Contact Info', 'easy-blog' ),
        'panel'    => 'header_top_panel',
        'priority' => 10,
    )
);

/** Header contact info control */
$wp_customize->add_setting( 
    'theme_options[show_header_contact_info]', 
    array(
        'default'           => $default['show_header_contact_info'],
        'sanitize_callback' => 'easy_blog_sanitize_checkbox',
    ) 
);

$wp_customize->add_control(
    'theme_options[show_header_contact_info]',
    array(
        'label'       => __( 'Show Contact Info', 'easy-blog' ),
        'section'     => 'header_contact_info_section',
        'type'        => 'checkbox',
    )
);

/** Location */
$wp_customize->add_setting( 'theme_options[header_location]', array(
    'sanitize_callback' => 'sanitize_text_field',
) );

$wp_customize->add_control(
    'theme_options[header_location]',
    array(
        'label'           => __( 'Location', 'easy-blog' ),
        'description'     => __( 'Enter Location.', 'easy-blog' ),
        'section'         => 'header_contact_info_section',
        'active_callback' => 'easy_blog_contact_info_ac',
    )
);

/** Phone */
$wp_customize->add_setting( 'theme_options[header_phone]', array(
    'sanitize_callback' => 'easy_blog_sanitize_phone_number',
) );

$wp_customize->add_control(
    'theme_options[header_phone]',
    array(
        'label'           => __( 'Phone', 'easy-blog' ),
        'description'     => __( 'Enter phone number.', 'easy-blog' ),
        'section'         => 'header_contact_info_section',
        'active_callback' => 'easy_blog_contact_info_ac',
    )
);

/** Email */
$wp_customize->add_setting( 
    'theme_options[header_email]', 
    array(
        'sanitize_callback' => 'sanitize_email',
    ) 
);

$wp_customize->add_control(
    'theme_options[header_email]',
    array(
        'label'           => __( 'Email', 'easy-blog' ),
        'description'     => __( 'Enter valid email address.', 'easy-blog' ),
        'section'         => 'header_contact_info_section',
        'active_callback' => 'easy_blog_contact_info_ac',
    )
);

/** Header social links section */
$wp_customize->add_section(
    'header_social_links_section',
    array(
        'title'    => __( 'Social Links', 'easy-blog' ),
        'panel'    => 'header_top_panel',
        'priority' => 20,
    )
);

/** Header social links control */
$wp_customize->add_setting( 
    'theme_options[show_header_social_links]', 
    array(
        'default'           => $default['show_header_social_links'],
        'sanitize_callback' => 'easy_blog_sanitize_checkbox',
    ) 
);

$wp_customize->add_control(
    'theme_options[show_header_social_links]',
    array(
        'label'       => __( 'Show Social Links', 'easy-blog' ),
        'section'     => 'header_social_links_section',
        'type'        => 'checkbox',
    )
);

// Setting social_links.
$wp_customize->add_setting( 
    'theme_options[social_link_1]', 
    array(
        'sanitize_callback' => 'esc_url_raw',
    ) 
);

$wp_customize->add_control(
    'theme_options[social_link_1]',
    array(
        'label'           => __( 'Social Link 1', 'easy-blog' ),
        'description'     => __( 'Enter valid url.', 'easy-blog' ),
        'section'         => 'header_social_links_section',
        'type'            => 'url',
        'active_callback' => 'easy_blog_social_links_active',
    )
);

$wp_customize->add_setting( 
    'theme_options[social_link_2]', 
    array(
        'sanitize_callback' => 'esc_url_raw',
    ) 
);

$wp_customize->add_control(
    'theme_options[social_link_2]',
    array(
        'label'           => __( 'Social Link 2', 'easy-blog' ),
        'description'     => __( 'Enter valid url.', 'easy-blog' ),
        'section'         => 'header_social_links_section',
        'type'            => 'url',
        'active_callback' => 'easy_blog_social_links_active',
    )
);
$wp_customize->add_setting( 
    'theme_options[social_link_3]', 
    array(
        'sanitize_callback' => 'esc_url_raw',
    ) 
);

$wp_customize->add_control(
    'theme_options[social_link_3]',
    array(
        'label'           => __( 'Social Link 3', 'easy-blog' ),
        'description'     => __( 'Enter valid url.', 'easy-blog' ),
        'section'         => 'header_social_links_section',
        'type'            => 'url',
        'active_callback' => 'easy_blog_social_links_active',
    )
);