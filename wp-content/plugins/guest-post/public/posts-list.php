<?php
$atts      = shortcode_atts( array( 'type' => 'guest_post', 'number' => 10, 'status' => 'publish' ), $atts, 'GUEST_POST_LIST' );
$args      = array(
    'post_type'      => $atts[ 'type' ],
    'post_status'    => $atts[ 'status' ],
    'posts_per_page' => $atts[ 'number' ]
);
$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) :
    echo wp_sprintf( '<div class="row">' );
    while ( $the_query->have_posts() ) : $the_query->the_post();
        ?>
        <div class="col-12 col-sm-12 col-md-6 guest-post-item">
            <div class="single-blog-item">
                <div class="blog-thumnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php
                        if ( has_post_thumbnail() ) :
                            the_post_thumbnail();
                        endif;
                        ?>
                    </a>
                </div>
                <div class="blog-content">
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <p><?php echo wp_strip_all_tags( get_the_excerpt() ); ?></p>
                    <a href="<?php the_permalink(); ?>" class="more-btn">View More</a>
                </div>
                <span class="blog-date"><?php the_date( 'Y-m-d' ); ?></span>
            </div>
        </div>
        <?php
    endwhile;
    $big = 999999999;
    echo wp_sprintf( '<div class="col-12 col-sm-12 col-md-12 text-center m-5">' );
    echo paginate_links( array(
        'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
        'format'  => '?paged=%#%',
        'current' => max( 1, get_query_var( 'paged' ) ),
        'total'   => $the_query->max_num_pages
    ) );
    echo wp_sprintf( '<div></div>' );
    wp_reset_postdata();

else :
    echo wp_sprintf( '<div class="alert alert-primary" role="alert">%s</div>', __( 'Sorry, no posts matched your criteria.', 'guest-post' ) );
endif;