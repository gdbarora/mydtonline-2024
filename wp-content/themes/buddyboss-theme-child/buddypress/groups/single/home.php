<?php

/**
 * BuddyBoss - Groups Home
 *
 * @since BuddyPress 3.0.0
 * @version 3.0.0
 */

$bp_nouveau_appearance = bp_get_option('bp_nouveau_appearance');
$group_cover_width = buddyboss_theme_get_option('buddyboss_group_cover_width');

if (bp_has_groups()) :
	while (bp_groups()) :
		bp_the_group();
?>

		<?php bp_nouveau_group_hook('before', 'home_content'); ?>

		<div id="item-header" role="complementary" data-bp-item-id="<?php bp_group_id(); ?>" data-bp-item-component="groups" class="groups-header single-headers">
			<?php bp_nouveau_group_header_template_part(); ?>
		</div>

		<?php if (groups_is_user_admin(bp_loggedin_user_id(), bp_get_group_id())) : ?>
			<div class="create_announcement_button">
				<a href="javascript:void(0)" class="button" id="create_announcement_btn">Create Announcements</a>
				<a href="javascript:void(0)" class="button" id="create_poll_btn">Create Poll</a>
			</div>
		<?php endif; ?>

		<div class="dt-popups" id="dt-announcement">
			<div class="popup_wrapper">
				<div class="popup_container">
					<header class="bb-model-header">
						<h4>Create new Announcement <span class="bp-reported-type"></span></h4>
						<button title="Close (Esc)" type="button" class="mfp-close">
							<span class="bb-icon-l bb-icon-times"></span>
						</button>
					</header>
					<div class="form_wrrapper">
						<?php echo do_shortcode('[GUEST_POST_FORM]'); ?>
					</div>
				</div>
			</div>
		</div>

		<?php if ((isset($bp_nouveau_appearance['group_nav_display']) && $bp_nouveau_appearance['group_nav_display']) && is_active_sidebar('group') && $group_cover_width != 'default') { ?>
			<div class="bb-grid bb-user-nav-display-wrap">
				<div class="bp-wrap-outer">
				<?php } ?>

				<div class="bp-wrap">

					<?php if (!bp_nouveau_is_object_nav_in_sidebar()) : ?>

						<?php bp_get_template_part('groups/single/parts/item-nav'); ?>

					<?php endif; ?>
					<?php
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

					if ($query->have_posts()) : ?>
						<div class="announcement-content" style="margin-top: -20px;">
							<?php
							while ($query->have_posts()) :
								$query->the_post();
								$saved_group_id = get_post_meta(get_the_ID(), 'bb_group_name', true);
								$bbUserSubbmited = get_post_meta(get_the_ID(), 'bb_user_subbmited', true);						
								$group_id_current = bp_get_current_group_id();
								$show = false;
								if (is_array($saved_group_id)) :
									foreach ($saved_group_id as $gid) :
										if ($gid == $group_id_current) :
											$show = true;
										endif;
									endforeach;

								elseif ($saved_group_id == $group_id_current) :
									$show = true;
								endif;

								if ($show && ($bbUserSubbmited == true)) :
							?>

									<?php if (members_can_current_user_view_post(get_the_ID()) && ($getAID < get_the_ID())) : ?>
										<div class="anouncements_sec">
											<p><?php the_title(); ?>!</p><a class="gamipress-button" data-post="<?php echo get_the_ID(); ?>" data-uid="<?php echo bp_loggedin_user_id(); ?>" data-element="announcement" href="/announcements" id="announcements_link">Click Here For Details!!</a>
										</div>
										
							<?php endif;
								endif;
							endwhile;
							wp_reset_postdata(); ?>
						</div>
					<?php endif; ?>


					<div class="bb-profile-grid bb-grid custom_group" style="margin-top:40px;">
						<div id="item-body" class="item-body">
							<?php bp_nouveau_group_template_part(); ?>
						</div>
					<?php if ((!isset($bp_nouveau_appearance['group_nav_display']) || !$bp_nouveau_appearance['group_nav_display']) && is_active_sidebar('group_activity') && bp_is_group_activity()) { ?>
						<div id="secondary-right" class="widget-area sm-grid-1-1 sidebar-right custom_group_thred" role="complementary">
							<div class="bp-messages-threads-list bp-messages-threads-list-user-<?php echo esc_attr(bp_loggedin_user_id()); ?>" id="bp-messages-threads-list">
								<aside class="widget">
									<?php

									if (groups_is_user_member(bp_loggedin_user_id(), bp_get_group_id())) :
										global $wpdb;
										$tblMsg 		=	$wpdb->prefix . 'bp_messages_messages';
										$tblMeta 		=	$wpdb->prefix . 'bp_messages_meta';
										$groupId 		=	bp_get_current_group_id();
										$getThreadId 	=	$wpdb->get_var("SELECT msg.thread_id FROM $tblMsg as msg JOIN $tblMeta as meta ON msg.id=meta.message_id WHERE meta.meta_key='group_id' AND meta.meta_value=$groupId ORDER BY meta.id DESC LIMIT 1");
										$sendMessageLink = bp_get_send_private_message_link();

										if (!empty($getThreadId)) :
											$sendMessageLink = bp_get_message_thread_view_link($getThreadId);
											$messageThreads = BP_Messages_Thread::get_current_threads_for_user();

											if (is_array($messageThreads['threads'])) :
												$threadData = bb_get_thread_by_id($messageThreads['threads'], $getThreadId);
												$messages = (is_array($threadData->messages) && count($threadData->messages)) ? array_reverse($threadData->messages) : array();
												echo '<div class="bp-messages-content">
										<div id="bp-message-thread-header" class="message-thread-header">
											<header class="single-message-thread-header">
												<div class="thread-avatar">
													<a href="' . bp_get_group_permalink() . '">' . bp_get_group_avatar() . '</a>
												</div>
												<dl class="thread-participants">
													<dt>
													<span class="participants-name"><a href="' . bp_get_group_permalink() . '">' . bp_get_group_name() . '</a></span>
													</dt>
													<dd><span class="thread-date" style="display:none;" >Joined Saturday, December 31</span></dd>
												</dl>
											</header>
										</div>
									</div><ul class="bb_live-chat-msg-wrapper message-lists" id="bp-message-thread-list">';

												$msgCount = 0;

												if (is_array($messages) && count($messages) > 0) :

													$msgData = array_slice($messages, -4, 4, true);

													foreach ($msgData as $message) :

														if ($msgCount > 3) :
															break;
														endif;
														$user = get_user_by('id', $message->sender_id);
														$profileLink = bbp_get_user_profile_url($message->sender_id);
														$dateTime = bp_nouveau_get_message_date($message->date_sent, get_option('date_format'));
														$msgTime = date("h:i a", strtotime($message->date_sent));

														echo '<li class="divider-date"><div class="bp-single-message-wrap">
															<div class="bp-single-message-content">
																	<div class="bp-message-content-wrap">' . $dateTime . '</div>
																</div>
															</div>
														</li>
														<li>
															<div class="bp-single-message-wrap">
																<div class="bp-avatar-wrap">
																	<a href="' . $profileLink . '" class="bp-user-avatar">' . get_avatar($message->sender_id, 32) . '</a>
																</div>
																<div class="bp-single-message-content">
																	<div class="message-metadata">
																		<a href="' . $profileLink . '" class="bp-user-link">
																			<strong>' . $user->display_name . '</strong>
																		</a>
																		<time datetime="' . $message->date_sent . '" class="activity">' . $msgTime . '</time>
																	</div>
																	<div class="bp-message-content-wrap">
																		<p>' . $message->message . '</p>
																	</div>
																</div>
															</div>
														</li>';
														$msgCount++;
													endforeach;
												endif;
												echo '</ul><a href="' . $sendMessageLink . '" class="button btn-new-topic live_chat_msg" target="_blank">Send Messages</a>';
											endif;
										endif;

									else :
										echo '<div class="bp-messages-content"><div id="bp-message-thread-header" class="message-thread-header">
										<header class="single-message-thread-header">
											<div class="thread-avatar">
												<a href="' . bp_get_group_permalink() . '">' . bp_get_group_avatar() . '</a>
											</div>
											<dl class="thread-participants">
												<dt>
												<span class="participants-name"><a href="' . bp_get_group_permalink() . '">' . bp_get_group_name() . '</a></span>
												</dt>
												<dd><span class="thread-date" style="display:none;" >Joined Saturday, December 31</span></dd>
											</dl>
										</header>';

										echo bp_get_group_join_button();
										echo '</div></div>';

									endif;
									?>
								</aside>
							</div>
						</div>
					<?php } ?>
						<?php if ((!isset($bp_nouveau_appearance['group_nav_display']) || !$bp_nouveau_appearance['group_nav_display']) && is_active_sidebar('group_activity') && bp_is_group_activity()) { ?>
							<div id="group-activity" class="widget-area sm-grid-1-1 custom_forum_discussion" role="complementary">
								<div class="bb-sticky-sidebar">

									<?php
									$forums = bbp_get_group_forum_ids(bp_get_current_group_id());
									$formId = $forums[0];
									?>
									<aside class="widget widget_block custom_forum_discussion">
										<div class="inner">
											<?php echo do_shortcode("[bbp-single-forum id=$formId]"); ?>
										</div>
									</aside>
								</div>
							</div>
						<?php } ?>

						<?php if ((!isset($bp_nouveau_appearance['group_nav_display']) || !$bp_nouveau_appearance['group_nav_display']) && is_active_sidebar('group') && $group_cover_width == 'full') { ?>
							<div id="secondary" class="widget-area sm-grid-1-1 no-padding-top" role="complementary">
								<div class="bb-sticky-sidebar">
									<?php dynamic_sidebar('group'); ?>
								</div>
							</div>
						<?php } ?>
					</div>

				</div><!-- // .bp-wrap -->

				<?php if ((isset($bp_nouveau_appearance['group_nav_display']) && $bp_nouveau_appearance['group_nav_display']) && is_active_sidebar('group') && $group_cover_width != 'default') { ?>
				</div>
				<div id="secondary" class="widget-area sm-grid-1-1 no-padding-top" role="complementary">
					<div class="bb-sticky-sidebar">
						<?php dynamic_sidebar('group'); ?>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php bp_nouveau_group_hook('after', 'home_content'); ?>

	<?php endwhile; ?>

<?php
endif;