<?php
/**
 * Active callback functions.
 *
 * @package Easy Blog
 */

function easy_blog_slider_active( $control ) {
    if( $control->manager->get_setting( 'theme_options[enable_featured_slider]' )->value() == true ) {
        return true;
    }else{
        return false;
    }
}

function easy_blog_slider_page( $control ) {
    $content_type = $control->manager->get_setting( 'theme_options[slider_content_type]' )->value();
    return ( easy_blog_slider_active( $control ) && ( 'slider_page' == $content_type ) );
}

function easy_blog_slider_post( $control ) {
    $content_type = $control->manager->get_setting( 'theme_options[slider_content_type]' )->value();
    return ( easy_blog_slider_active( $control ) && ( 'slider_post' == $content_type ) );
}

function easy_blog_featured_posts_active( $control ) {
    if( $control->manager->get_setting( 'theme_options[enable_featured_posts_section]' )->value() == true ) {
        return true;
    }else{
        return false;
    }
}

function easy_blog_featured_posts_page( $control ) {
    $content_type = $control->manager->get_setting( 'theme_options[featured_posts_content_type]' )->value();
    return ( easy_blog_featured_posts_active( $control ) && ( 'featured_posts_page' == $content_type ) );
}

function easy_blog_featured_posts_post( $control ) {
    $content_type = $control->manager->get_setting( 'theme_options[featured_posts_content_type]' )->value();
    return ( easy_blog_featured_posts_active( $control ) && ( 'featured_posts_post' == $content_type ) );
}

function easy_blog_blog_active( $control ) {
    if( $control->manager->get_setting( 'theme_options[enable_blog_section]' )->value() == true ) {
        return true;
    }else{
        return false;
    }
}

/**
 * Active Callback for top bar section
 */
function easy_blog_contact_info_ac( $control ) {

    $show_contact_info = $control->manager->get_setting( 'theme_options[show_header_contact_info]')->value();
    $control_id        = $control->id;
         
    if ( $control_id == 'theme_options[header_location]' && $show_contact_info ) return true;
    if ( $control_id == 'theme_options[header_email]' && $show_contact_info ) return true;
    if ( $control_id == 'theme_options[header_phone]' && $show_contact_info ) return true;

    return false;
}

function easy_blog_social_links_active( $control ) {
    if( $control->manager->get_setting( 'theme_options[show_header_social_links]' )->value() == true ) {
        return true;
    }else{
        return false;
    }
}