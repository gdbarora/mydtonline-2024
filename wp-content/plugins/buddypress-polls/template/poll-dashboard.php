<?php
/**
 * The poll dashboard page.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Polls
 * @subpackage Buddypress_Polls/public
 */

?>

<div class="main-dashboard">

<nav class="dashboard-nav" id="dashboard-nav">
	<ul class="dashboard-subnav">
		<li id="publish-personal-li" class="dashboard-sub-tab selected" data-text="publish">
			<a href="#" class="tab-link"><?php esc_html_e( 'Published', 'buddypress-polls' ); ?></a>
		</li>
		<li id="pending-personal-li" class="dashboard-sub-tab"  data-text="pending">
			<a href="#" class="tab-link"><?php esc_html_e( 'Pending', 'buddypress-polls' ); ?></a>
		</li>
		<li id="draft-personal-li" class="dashboard-sub-tab"  data-text="draft">
			<a href="#" class="tab-link"><?php esc_html_e( 'Draft', 'buddypress-polls' ); ?></a>
		</li>
	</ul>
</nav>

<!-- pending poll -->

<div class="publish-listing tab-list active">
<?php
if ( is_user_logged_in() ) {

	$option_value        = get_option( 'wbpolls_settings' );
	$poll_dashboard_page = isset( $option_value['create_poll_page'] ) ? $option_value['create_poll_page'] : '';

	$page = get_post( $poll_dashboard_page );

	if ( $page ) {
		$page_slug = $page->post_name;
	}
	?>
	<div class="deshboard-top">
		<div class="main-title">
			<h3><?php esc_html_e( 'Published Poll Listing', 'buddypress-polls' ); ?></h3>
		</div>
		<div class="add-poll-button">
			<a class="button btn" href="<?php echo esc_url( site_url() . '/' . $page_slug ); ?>"><?php esc_html_e( 'Create new poll', 'buddypress-polls' ); ?></a>
		</div>
	</div>
	<div class="poll-listing">
		<table class="poll-listing-table">
			<thead>
				<tr>
					<th class="poll-title"><?php esc_html_e( 'Title', 'buddypress-polls' ); ?></th>
					<th class="poll-status"><?php esc_html_e( 'Status', 'buddypress-polls' ); ?></th>
					<th class="poll-status"><?php esc_html_e( 'Time Status', 'buddypress-polls' ); ?></th>
					<th class="poll-vote"><?php esc_html_e( 'Vote', 'buddypress-polls' ); ?></th>
					<th class="poll-action"><?php esc_html_e( 'Action', 'buddypress-polls' ); ?></th>
				</tr>
			</thead>
			<?php
			$userid = get_current_user_id();
			$url    = site_url() . '/wp-json/wbpoll/v1/listpoll/user/id?id=' . $userid . '&status=publish';
			$curl   = curl_init();
			curl_setopt_array(
				$curl,
				array(
					CURLOPT_URL            => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST  => 'POST',
				)
			);
			$response = curl_exec( $curl );

			curl_close( $curl );

			// Parse the JSON response.
			$data = json_decode( $response );
			if ( isset( $data->code ) && $data->code == '404' ) {
				?>
					<tr>
						<td colspan="4">
							<?php echo esc_html_e( 'Polls Not Found', 'buddypress-polls' ); ?>
						</td>
					</tr>
					<?php
			} else {

				foreach ( $data as $post ) {

					// Access post information.
					$post_id      = $post->id;
					$post_title   = $post->title;
					$post_name    = $post->slug;
					$post_stauts  = $post->status;
					$totalvote    = $post->totalvote;
					$pause        = $post->pausetype;
					$start_date   = $post->start_time;
					$end_date     = $post->end_date;
					$never_expire = $post->never_expire;

					?>
					<tr>
						<td class="poll-title" data-title="<?php esc_attr_e( 'Title', 'buddypress-polls' ); ?>"><?php echo esc_html__( $post_title, 'buddypress-polls' ); ?></td>
						<td class="poll-status" data-title="<?php esc_attr_e( 'Status', 'buddypress-polls' ); ?>"><?php echo esc_html__( $post_stauts, 'buddypress-polls' ); ?></td>
						<td class="poll-time-status" data-title="<?php esc_attr_e( 'Time Status', 'buddypress-polls' ); ?>">
						<?php

						if ( $never_expire == 1 ) {
							if ( new DateTime( $start_date ) > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
								echo '<span class="dashicons dashicons-calendar"></span> ' . esc_html__(
									'Yet to Start',
									'buddypress-polls'
								);
							} else {
								echo '<span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Active', 'buddypress-polls' );
							}
						} else {
							if ( new DateTime( $start_date ) > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
								echo '<span class="dashicons dashicons-calendar"></span> ' . esc_html_e( 'Yet to Start', 'buddypress-polls' );
							} else {
								if ( new DateTime( $start_date ) <= new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) && new DateTime( $end_date ) > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
									echo '<span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Active', 'buddypress-polls' );
								} else {
									if ( new DateTime( $end_date ) <= new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
										echo '<span class="dashicons dashicons-lock"></span> ' . esc_html__( 'Expired', 'buddypress-polls' );
									}
								}
							}
						}
						?>
						</td>
						<td class="poll-vote" data-title="<?php esc_attr_e( 'Vote', 'buddypress-polls' ); ?>"><?php echo esc_html( $totalvote ); ?></td>
						<td class="poll-action" data-title="<?php esc_attr_e( 'Action', 'buddypress-polls' ); ?>">
							<a class="button btn" href="<?php echo esc_url( site_url() ) . '/poll/' . esc_html( str_replace( ' ', '-', $post_name ) ); ?>" data-polls-tooltip="<?php esc_attr_e( 'View', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-eye-small"></i></a>
						<?php if ( $totalvote < 1 ) { ?>
							<a class="button btn" href="<?php echo esc_url( site_url() . '/' . $page_slug . '?poll_id=' . $post_id . '&_wpnonce=' . wp_create_nonce( 'edit_poll_' . $post_id ) ); ?>" data-polls-tooltip="<?php esc_attr_e( 'Edit', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-edit-thin"></i></a>
						<?php } ?>
							<button class="button btn pause_poll" data-value="
						<?php
						if ( ! empty( $pause ) && $pause == 1 ) {
							echo 0;
						} else {
							echo 1; }
						?>
							" data-id="<?php echo esc_html( $post_id ); ?>"
							<?php
							if ( ! empty( $pause ) && $pause == 1 ) {
								?>
								data-polls-tooltip="<?php esc_attr_e( 'Resume', 'buddypress-polls' ); ?>"
								<?php
							} else {
								?>
								data-polls-tooltip="<?php esc_attr_e( 'Pause', 'buddypress-polls' ); ?>"
								<?php
							}
							?>
							>
							<?php
							if ( ! empty( $pause ) && $pause == 1 ) {
								?>
								<i class="wb-icons wb-icon-play"></i>
								<?php
							} else {
								?>
								<i class="wb-icons wb-icon-pause"></i>
								<?php
							}
							?>
							</button>
						<?php if ( $post_stauts == 'publish' ) { ?>
							<button class="button btn unpublish_poll" data-id="<?php echo esc_html( $post_id ); ?>" data-polls-tooltip="<?php esc_attr_e( 'Unpublish', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-list-bookmark"></i></button>
						<?php } ?>
						<button class="button btn delete_poll" data-id="<?php echo esc_html( $post_id ); ?>" data-polls-tooltip="<?php esc_attr_e( 'Delete', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-trash"></i></button></td>
					</tr>
					<?php
				}
				wp_reset_postdata();
			}

			?>
		</table>
	</div>
	<?php } else { ?>
		<div class="wbpoll_wrapper wbpoll_wrapper-content_hook" data-reference="content_hook"><p class="wbpoll-voted-info wbpoll-alert"> <?php esc_html_e( 'This page content only for login members.', 'buddypress-polls' ); ?> </p></div>
	<?php } ?>
</div>

<!-- pending poll -->

<div class="pending-listing tab-list" >
<?php
if ( is_user_logged_in() ) {

	$option_value        = get_option( 'wbpolls_settings' );
	$poll_dashboard_page = isset( $option_value['create_poll_page'] ) ? $option_value['create_poll_page'] : '';

	$page = get_post( $poll_dashboard_page );
	if ( $page ) {
		$page_slug = $page->post_name;
	}
	?>
	<div class="deshboard-top">
		<div class="main-title">
			<h3><?php esc_html_e( 'Pending Poll Listing', 'buddypress-polls' ); ?></h3>
		</div>
		<div class="add-poll-button">
			<a class="button btn" href="<?php echo esc_url( site_url() ) . '/' . $page_slug; //phpcs:ignore ?>"><?php esc_html_e( 'Create new poll', 'buddypress-polls' ); ?></a>
		</div>
	</div>
	<div class="poll-listing">
		<table class="poll-listing-table">
			<thead>
				<tr>
					<th class="poll-title"><?php esc_html_e( 'Title', 'buddypress-polls' ); ?></th>
					<th class="poll-status"><?php esc_html_e( 'Status', 'buddypress-polls' ); ?></th>
					<th class="poll-status"><?php esc_html_e( 'Time Status', 'buddypress-polls' ); ?></th>
					<th class="poll-vote"><?php esc_html_e( 'Vote', 'buddypress-polls' ); ?></th>
					<th class="poll-action"><?php esc_html_e( 'Action', 'buddypress-polls' ); ?></th>
				</tr>
			</thead>
			<?php
			$userid = get_current_user_id();
			$url    = site_url() . '/wp-json/wbpoll/v1/listpoll/user/id?id=' . $userid . '&status=pending';
			$curl   = curl_init();
			curl_setopt_array(
				$curl,
				array(
					CURLOPT_URL            => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST  => 'POST',
				)
			);
			$response = curl_exec( $curl );

			curl_close( $curl );

			// Parse the JSON response.
			$data = json_decode( $response );
			if ( isset( $data->code ) && $data->code == '404' ) {
				?>

					<tr>
						<td colspan="4">
							<?php echo esc_html_e( 'Polls Not Found', 'buddypress-polls' ); ?>
						</td>
					</tr>
					<?php
			} else {

				foreach ( $data as $post ) {
					// Access post information.
					$post_id      = $post->id;
					$post_title   = $post->title;
					$post_name    = $post->slug;
					$post_stauts  = $post->status;
					$totalvote    = $post->totalvote;
					$pause        = $post->pausetype;
					$start_date   = $post->start_time;
					$end_date     = $post->end_date;
					$never_expire = $post->never_expire;
					?>
					<tr>
						<td class="poll-title" data-title="<?php esc_attr_e( 'Title', 'buddypress-polls' ); ?>"><?php echo esc_html( $post_title ); ?></td>
						<td class="poll-status" data-title="<?php esc_attr_e( 'Status', 'buddypress-polls' ); ?>"><?php echo esc_html( $post_stauts ); ?></td>
						<td class="poll-time-status" data-title="<?php esc_attr_e( 'Time Status', 'buddypress-polls' ); ?>">
						<?php
						if ( $never_expire == 1 ) {
							if ( new DateTime( $start_date ) > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
								echo '<span class="dashicons dashicons-calendar"></span> ' . esc_html__(
									'Yet to Start',
									'buddypress-polls'
								);
							} else {
								echo '<span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Active', 'buddypress-polls' );
							}
						} else {
							if ( new DateTime( $start_date ) > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
								echo '<span class="dashicons dashicons-calendar"></span> ' . esc_html_e( 'Yet to Start', 'buddypress-polls' );
							} else {
								if ( new DateTime( $start_date ) <= new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) && new DateTime( $end_date ) > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
									echo '<span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Active', 'buddypress-polls' );
								} else {
									if ( new DateTime( $end_date ) <= new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
										echo '<span class="dashicons dashicons-lock"></span> ' . esc_html__( 'Expired', 'buddypress-polls' );
									}
								}
							}
						}

						?>
						</td>
						<td class="poll-vote" data-title="<?php esc_attr_e( 'Vote', 'buddypress-polls' ); ?>"><?php echo esc_html( $totalvote ); ?></td>
						<td class="poll-action" data-title="<?php esc_attr_e( 'Action', 'buddypress-polls' ); ?>">						
						<?php if ( $totalvote < 1 ) { ?>
							<a class="button btn" href="<?php echo esc_url( site_url() . '/' . $page_slug . '?poll_id=' . $post_id . '&_wpnonce=' . wp_create_nonce( 'edit_poll_' . $post_id ) ); ?>" data-polls-tooltip="<?php esc_attr_e( 'Edit', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-edit-thin"></i></a>
						<?php } ?>
						<button class="button btn delete_poll" data-id="<?php echo esc_html( $post_id ); ?>" data-polls-tooltip="<?php esc_attr_e( 'Delete', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-trash"></i></button></td>
					</tr>
					<?php
				}
				wp_reset_postdata();
			}

			?>
		</table>
	</div>
	<?php } else { ?>
		<div class="wbpoll_wrapper wbpoll_wrapper-content_hook" data-reference="content_hook"><p class="wbpoll-voted-info wbpoll-alert"> <?php esc_html_e( 'This page content only for login members.', 'buddypress-polls' ); ?> </p></div>
	<?php } ?>
</div>

<!--  draft poll -->
<div class="draft-listing tab-list">
<?php
if ( is_user_logged_in() ) {

	$option_value        = get_option( 'wbpolls_settings' );
	$poll_dashboard_page = isset( $option_value['create_poll_page'] ) ? $option_value['create_poll_page'] : '';

	$page = get_post( $poll_dashboard_page );
	if ( $page ) {
		$page_slug = $page->post_name;
	}
	?>
	<div class="deshboard-top">
		<div class="main-title">
			<h3><?php esc_html_e( 'Draft Poll Listing', 'buddypress-polls' ); ?></h3>
		</div>
		<div class="add-poll-button">
			<a class="button btn" href="<?php echo esc_url( site_url() . '/' . $page_slug ); ?>"><?php esc_html_e( 'Create new poll', 'buddypress-polls' ); ?></a>
		</div>
	</div>
	<div class="poll-listing">
		<table class="poll-listing-table">
			<thead>
				<tr>
					<th class="poll-title"><?php esc_html_e( 'Title', 'buddypress-polls' ); ?></th>
					<th class="poll-status"><?php esc_html_e( 'Status', 'buddypress-polls' ); ?></th>
					<th class="poll-status"><?php esc_html_e( 'Time Status', 'buddypress-polls' ); ?></th>
					<th class="poll-vote"><?php esc_html_e( 'Vote', 'buddypress-polls' ); ?></th>
					<th class="poll-action"><?php esc_html_e( 'Action', 'buddypress-polls' ); ?></th>
				</tr>
			</thead>
			<?php
			$userid = get_current_user_id();
			$url    = site_url() . '/wp-json/wbpoll/v1/listpoll/user/id?id=' . $userid . '&status=draft';
			$curl   = curl_init();
			curl_setopt_array(
				$curl,
				array(
					CURLOPT_URL            => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_CUSTOMREQUEST  => 'POST',
				)
			);
			$response = curl_exec( $curl );

			curl_close( $curl );

			// Parse the JSON response.
			$data = json_decode( $response );
			if ( isset( $data->code ) && $data->code == '404' ) {
				?>
				<tr>
					<td colspan="4">
						<?php echo esc_html_e( 'Polls Not Found', 'buddypress-polls' ); ?>
					</td>
				</tr>
				<?php
			} else {

				foreach ( $data as $post ) {
					// Access post information.
					$post_id      = $post->id;
					$post_title   = $post->title;
					$post_name    = $post->slug;
					$post_stauts  = $post->status;
					$totalvote    = $post->totalvote;
					$pause        = $post->pausetype;
					$start_date   = $post->start_time;
					$end_date     = $post->end_date;
					$never_expire = $post->never_expire;
					?>
					<tr>
						<td class="poll-title" data-title="<?php esc_attr_e( 'Title', 'buddypress-polls' ); ?>"><?php echo esc_html( $post_title ); ?></td>
						<td class="poll-status" data-title="<?php esc_attr_e( 'Status', 'buddypress-polls' ); ?>"><?php echo esc_html( $post_stauts ); ?></td>
						<td class="poll-time-status" data-title="<?php esc_attr_e( 'Time Status', 'buddypress-polls' ); ?>">
						<?php

						if ( $never_expire == 1 ) {
							if ( new DateTime( $start_date ) > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
								echo '<span class="dashicons dashicons-calendar"></span> ' . esc_html__(
									'Yet to Start',
									'buddypress-polls'
								);
							} else {
								echo '<span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Active', 'buddypress-polls' );
							}
						} else {
							if ( new DateTime( $start_date ) > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
								echo '<span class="dashicons dashicons-calendar"></span> ' . esc_html_e( 'Yet to Start', 'buddypress-polls' );
							} else {
								if ( new DateTime( $start_date ) <= new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) && $end_date > new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
									echo '<span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Active', 'buddypress-polls' );
								} else {
									if ( new DateTime( $end_date ) <= new DateTime( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ) ) ) {
										echo '<span class="dashicons dashicons-lock"></span> ' . esc_html__( 'Expired', 'buddypress-polls' );
									}
								}
							}
						}

						?>
						</td>
						<td class="poll-vote" data-title="<?php esc_attr_e( 'Vote', 'buddypress-polls' ); ?>"><?php echo esc_html( $totalvote ); ?></td>
						<td class="poll-action" data-title="<?php esc_attr_e( 'Action', 'buddypress-polls' ); ?>">						
						<?php if ( $totalvote < 1 ) { ?>
							<a class="button btn" href="<?php echo esc_url( site_url() . '/' . $page_slug . '?poll_id=' . $post_id . '&_wpnonce=' . wp_create_nonce( 'edit_poll_' . $post_id ) ); ?>" data-polls-tooltip="<?php esc_attr_e( 'Edit', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-edit-thin"></i></a>
						<?php } ?>
						<?php if ( $post_stauts == 'draft' ) { ?>
							<button class="button btn publish_poll" data-id="<?php echo esc_html( $post_id ); ?>" data-polls-tooltip="<?php esc_attr_e( 'Publish', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-all-results"></i></button>
						<?php } ?>
						<button class="button btn delete_poll" data-id="<?php echo esc_html( $post_id ); ?>" data-polls-tooltip="<?php esc_attr_e( 'Delete', 'buddypress-polls' ); ?>"><i class="wb-icons wb-icon-trash"></i></button></td>
					</tr>
					<?php
				}
				wp_reset_postdata();
			}

			?>
		</table>
	</div>
	<?php } else { ?>
		<div class="wbpoll_wrapper wbpoll_wrapper-content_hook" data-reference="content_hook"><p class="wbpoll-voted-info wbpoll-alert"> <?php esc_html_e( 'This page content only for login members.', 'buddypress-polls' ); ?> </p></div>
	<?php } ?>
</div>

</div>
