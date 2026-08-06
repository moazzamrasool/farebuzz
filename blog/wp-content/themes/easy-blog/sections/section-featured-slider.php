<?php 
/**
 * Template part for displaying Featured Slider Section
 *
 *@package Easy Blog
 */
    $slider_content_type        = easy_blog_get_option( 'slider_content_type' );
    $number_of_slider_items     = easy_blog_get_option( 'number_of_slider_items' );
    
    if( $slider_content_type == 'slider_page' ) :
        for( $i=1; $i<=$number_of_slider_items; $i++ ) :
            $featured_slider_posts[] = easy_blog_get_option( 'slider_page_'.$i );
        endfor;  
    elseif( $slider_content_type == 'slider_post' ) :
        for( $i=1; $i<=$number_of_slider_items; $i++ ) :
            $featured_slider_posts[] = easy_blog_get_option( 'slider_post_'.$i );
        endfor;
    endif;
    ?>
    <?php if( $slider_content_type == 'slider_page' ) : ?>
        <div class="featured-slider-wrapper" data-slick='{"slidesToShow": 1, "slidesToScroll": 1, "infinite": true, "speed": 1000, "dots": true, "arrows":true, "autoplay": false, "fade": true }'>
            <?php $args = array (
                'post_type'     => 'page',
                'posts_per_page' => absint( $number_of_slider_items ),
                'post__in'      => $featured_slider_posts,
                'orderby'       =>'post__in',
            );   

            $loop = new WP_Query($args);                        
                if ( $loop->have_posts() ) :
                $i=-1;  
                    while ($loop->have_posts()) : $loop->the_post(); $i++;
                    $class='';
                    if ($i==0) {
                        $class='display-block';
                    } else{
                        $class='display-none';}
                    ?>
                        <article class="slick-item <?php echo esc_attr($class); ?>" style="background-image: url('<?php the_post_thumbnail_url( 'full' ); ?>');">
                            <div class="wrapper">
                                <div class="featured-content-wrapper">
                                    <div class="entry-container">
                                        <header class="entry-header">
                                            <h2 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
                                        </header>
                                        
                                        <div class="entry-content">
                                            <?php
                                                $excerpt = easy_blog_the_excerpt( 20 );
                                                echo wp_kses_post( wpautop( $excerpt ) );
                                            ?>
                                        </div><!-- .entry-content -->


                                        <?php $readmore_text = easy_blog_get_option( 'readmore_text' );?>
                                        <?php if (!empty($readmore_text) ) :?>
                                            <div class="read-more">
                                                <a href="<?php the_permalink();?>" class="btn btn-primary"><?php echo esc_html($readmore_text);?><i class="fas fa-long-arrow-alt-right"></i></a>
                                            </div><!-- .read-more -->
                                        <?php endif; ?>
                                    </div><!-- .entry-container -->
                                </div><!-- .featured-content-wrapper -->
                            </div><!-- .wrapper -->
                        </article><!-- .slick-item -->
                    <?php endwhile;?>
                <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div><!-- .featured-slider-wrapper -->

    <?php else : ?>
        <div class="featured-slider-wrapper" data-slick='{"slidesToShow": 1, "slidesToScroll": 1, "infinite": true, "speed": 1000, "dots": true, "arrows":true, "autoplay": false, "fade": true }'>
            <?php $args = array (
                'post_type'     => 'post',
                'posts_per_page' => absint( $number_of_slider_items ),
                'post__in'      => $featured_slider_posts,
                'orderby'       =>'post__in',
                'ignore_sticky_posts' => true,
            );   

            $loop = new WP_Query($args);                        
                if ( $loop->have_posts() ) :
                $i=-1;  
                    while ($loop->have_posts()) : $loop->the_post(); $i++;
                    $class='';
                    if ($i==0) {
                        $class='display-block';
                    } else{
                        $class='display-none';}
                    ?>
                        <article class="slick-item <?php echo esc_attr($class); ?>" style="background-image: url('<?php the_post_thumbnail_url( 'full' ); ?>');">
                            <div class="wrapper">
                                <div class="featured-content-wrapper">
                                    <div class="entry-container">
                                        <header class="entry-header">
                                            <h2 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>
                                        </header>
                                        
                                        <div class="entry-content">
                                            <?php
                                                $excerpt = easy_blog_the_excerpt( 20 );
                                                echo wp_kses_post( wpautop( $excerpt ) );
                                            ?>
                                        </div><!-- .entry-content -->


                                        <?php $readmore_text = easy_blog_get_option( 'readmore_text' );?>
                                        <?php if (!empty($readmore_text) ) :?>
                                            <div class="read-more">
                                                <a href="<?php the_permalink();?>" class="btn btn-primary"><?php echo esc_html($readmore_text);?><i class="fas fa-long-arrow-alt-right"></i></a>
                                            </div><!-- .read-more -->
                                        <?php endif; ?>
                                    </div><!-- .entry-container -->
                                </div><!-- .featured-content-wrapper -->
                            </div><!-- .wrapper -->
                        </article><!-- .slick-item -->
                    <?php endwhile;?>
                <?php endif; ?>
            <?php wp_reset_postdata(); ?>
        </div><!-- .featured-slider-wrapper -->
    <?php endif;