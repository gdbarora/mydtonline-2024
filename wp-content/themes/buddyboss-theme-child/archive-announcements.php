<?php

/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package BuddyBoss_Theme
 */

get_header();

$blog_type = 'masonry'; // standard, grid, masonry.
$blog_type = apply_filters('bb_blog_type', $blog_type);

$class = '';

if ('masonry' === $blog_type) {
	$class = 'bb-masonry';
} elseif ('grid' === $blog_type) {
	$class = 'bb-grid';
} else {
	$class = 'bb-standard';
}

global $wp;

$orderByTitle	= add_query_arg("view", "title",  home_url($wp->request));
$orderByLatest 	= add_query_arg("view", "latest",  home_url($wp->request));
$orderByOldest 	= add_query_arg("view", "oldest",  home_url($wp->request));

if (isset($_GET['view']) && !empty($_GET['view'])) :
	$orderBy = sanitize_text_field($_GET['view']);
else :
	$orderBy = 'latest';
endif;

?>

<div id="primary" class="content-area">
	<main id="main" class="site-main bb_announcements">

		<?php if (have_posts()) : ?>
			<header class="page-header">
				<h1>Announcements</h1>
				<div class="bb_filters select-wrap">
					<select id="order-by" data-filter="announcements">
						<option value="alphabetical" <?php echo ($orderBy == "title") ? "selected" : ""; ?> data-link="<?php echo $orderByTitle; ?>">Alphabetical</option>
						<option value="newest" <?php echo ($orderBy == "latest") ? "selected" : ""; ?> data-link="<?php echo $orderByLatest; ?>">Latest</option>
						<option value="oldest" <?php echo ($orderBy == "oldest") ? "selected" : ""; ?> data-link="<?php echo $orderByOldest; ?>">Oldest</option>
					</select>
					<span class="select-arrow" aria-hidden="true"></span>
				</div>
				<!-- /.bb_filters -->
			</header><!-- .page-header -->


			<div class="post-grid <?php echo esc_attr($class); ?>">

				<?php if ('masonry' === $blog_type) { ?>
					<div class="bb-masonry-sizer"></div>
				<?php } ?>

				<?php

				/* Start the Loop */
				while (have_posts()) :
					the_post();

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					if (members_can_current_user_view_post(get_the_ID())) :
						get_template_part('template-parts/content', apply_filters('bb_blog_content', get_post_format()));

					endif;
				endwhile;
				?>
			</div>

		<?php
			buddyboss_pagination();

		else :
			get_template_part('template-parts/content', 'none');
		?>

		<?php endif; ?>

	</main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>

<?php
get_footer();
