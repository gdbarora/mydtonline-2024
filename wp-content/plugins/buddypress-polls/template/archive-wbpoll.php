<?php
/**
 * WBPoll Archive page
 *
 * @package WordPress
 * @subpackage buddypress-polls
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header(); /*Header Portion*/

/**
 * Hook: buddypress_polls_before_main_content. *
 */
do_action( 'buddypress_polls_before_main_content' );

?>
<div id="primary" class="content-area">

	<main id="main" class="site-main buddypress-polls-wrap" role="main">
	
		<?php do_action( 'before_single_buddypress_polls' ); ?>
		
		<div id="buddypress-polls" class="buddypress-polls-listing" >
			<?php if ( have_posts() ) : ?>

				<header class="page-header">
				<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );
				?>
				</header><!-- .page-header -->
				<div id="wbpoll-archive-listing" class="wbpoll-archive-listing">
					<?php
					while ( have_posts() ) :
						the_post();
						?>

						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

							<?php if ( has_post_thumbnail() ) : ?>
								<div class="entry-thumbnail">
									<div class="wbpoll-thumbnail-wrapper">
										<?php the_post_thumbnail(); ?>
									</div><!-- .wbpoll-thumbnail-wrapper -->
								</div><!-- .entry-thumbnail -->
							<?php endif; ?>
								
							<header class="entry-header">
								<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
								<div class="entry-meta"><?php buddypress_polls_meta(); ?></div>
							</header><!-- .entry-header -->

							<div class="entry-content">
								<?php the_excerpt(); ?>

								<?php if ( ! is_singular() ) { ?>
									<?php	/* translators: %s: */ ?>
									<p class="wbpoll-view-poll-link"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'buddypress-polls' ), the_title_attribute( 'echo=0' ) ) ); ?>" class="read-more button"><?php esc_html_e( 'View poll', 'buddypress-polls' ); ?></a></p>
								<?php } ?>

							</div><!-- .entry-content -->

						</article><!-- #post-## -->


					<?php endwhile; ?>
				</div>
			
				<?php
				buddypress_polls_navigation();
			endif;
			?>

		</div>
		
		<?php do_action( 'after_single_buddypress_polls' ); ?>
		
	</main> <!-- Main tag finish -->
	
</div>
<?php if ( is_active_sidebar( 'buddypress-poll-right' ) ) : ?>
	<aside id="primary-sidebar" class="widget-area default" role="complementary">
		<div class="widget-area-inner">
			<?php dynamic_sidebar( 'buddypress-poll-right' ); ?>
		</div>
	</aside>
	<?php
endif;

/**
 * Hook: bp_business_profile_after_main_content.
 */
do_action( 'buddypress_polls_after_main_content' );


get_footer();
