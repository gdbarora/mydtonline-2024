<?php
/**
 * The template for displaying a user's courses
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Buddyboss
 */

get_header();
?>
<div id="primary" class="content-area bs-bp-container">
	<main id="main" class="site-main">
		<article id="post-1117" class="page type-page status-publish hentry user-has-earned">
			<div class="entry-content">
				<div class="top_wrapper">
				<?php the_field('top_content', 'option');?>
				</div>
				<?php bp_get_template_part( 'members/single/plugins' ); ?>
			</div>
		</article>
	</main>
</div>

<?php 
get_footer();
?>