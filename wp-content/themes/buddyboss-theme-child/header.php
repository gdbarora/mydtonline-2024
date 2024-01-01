<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package BuddyBoss_Theme
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>

	<?php if (!is_singular('llms_my_certificate')) :

		do_action(THEME_HOOK_PREFIX . 'before_page');

	endif; ?>

	<div id="page" class="site">

		<?php do_action(THEME_HOOK_PREFIX . 'before_header'); ?>

		<header id="masthead" class="<?php echo apply_filters('buddyboss_site_header_class', 'site-header site-header--bb'); ?>">
			<?php do_action(THEME_HOOK_PREFIX . 'header'); ?>
		</header>

		<?php do_action(THEME_HOOK_PREFIX . 'after_header'); ?>

		<?php do_action(THEME_HOOK_PREFIX . 'before_content'); ?>

		<div id="content" class="site-content">

			<?php do_action(THEME_HOOK_PREFIX . 'begin_content'); ?>
			<?php if (is_front_page()) :
				$userRegistered =	strtotime(get_the_author_meta('user_registered', bp_loggedin_user_id()));
				$day 			=	date("d", $userRegistered);
				$month 			=	date("m", $userRegistered);
				$year 			=	date("Y", $userRegistered);
				$getAID			=	get_user_meta(bp_loggedin_user_id(), 'bb_read_announcement_id', true);
				if ($getAID) :
					$getLastAnn  	=	get_post($getAID);
					$postData = ($getLastAnn->post_date !== 0) ? strtotime($getLastAnn->post_date) : strtotime($getLastAnn->post_date_gmt);
					$day 			=	date("d", $postData);
					$month 			=	date("m", $postData);
					$year 			=	date("Y", $postData);
				else :
					$getAID = 0;
				endif;

				$query 			=	new WP_Query(array(
					'post_type'		=>	'announcements',
					'post_status'	=>	'publish',
					'orderby'		=>	'meta_value_num',
					'order' 		=> 'ASC',
					'date_query'	=>	array(
						'after'		=>	array(
							'year'  => $year,
							'month' => $month,
							'day'   => $day,
						),
						'inclusive' => true,
					),
					'meta_query' => array(
						array(
							'key'     => 'bb_announcement_date',
							'type' => 'NUMERIC'
						),
					),
				));
				if ($query->have_posts()) :
					echo wp_sprintf('<div class="announcement-content">');

					while ($query->have_posts()) :
						$query->the_post();

						$bbUserSubbmited = get_post_meta(get_the_ID(), 'bb_user_subbmited', true);

						if ($bbUserSubbmited != true) :
			?>
							<?php if (members_can_current_user_view_post(get_the_ID()) && ($getAID < get_the_ID())) : ?>
								<div class="anouncements_sec">
									<p><?php the_title(); ?>!</p>
									<a class="gamipress-button " data-post="<?php echo get_the_ID(); ?>" data-uid="<?php echo bp_loggedin_user_id(); ?>" data-element="announcement" href="/announcements" id="announcements_link">Click Here For Details!!</a>
								</div>
			<?php
							endif;
						endif;
					endwhile;
					wp_reset_postdata();
					echo '</div>';
				endif;
			endif;
			?>
			<div class="container">
				<?php if(is_page('2018')) :
				else : ?>
				<div class="<?php echo apply_filters('buddyboss_site_content_grid_class', 'bb-grid site-content-grid'); ?>"> <?php endif;?>