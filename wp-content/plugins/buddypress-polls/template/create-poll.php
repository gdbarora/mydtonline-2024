<?php
/**
 * The create poll page.
 *
 * @link       http://www.wbcomdesigns.com
 * @since      4.3.0
 *
 * @package    Buddypress_Polls
 * @subpackage Buddypress_Polls/public
 */

global $post, $current_user;
$temp_post           = $post;
$option_value        = get_option( 'wbpolls_settings' );
$wppolls_create_poll = ( isset( $option_value['wppolls_create_poll'] ) ) ? $option_value['wppolls_create_poll'] : '';
if ( ! empty( $wppolls_create_poll ) ) {
	$roles  = $current_user->roles;
	$result = array_intersect( $wppolls_create_poll, $roles );

	if ( empty( $result ) ) {
		echo '<div class="main-poll-create">';
		echo esc_html__( 'You are not allow to create the poll.', 'buddypress-polls' );
		echo '</div>';

		return;
	}
}
if ( ! empty( $_GET['poll_id'] ) ) {

	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'edit_poll_' . $_GET['poll_id'] ) ) {

		echo '<div class="main-poll-create">';
		echo esc_html__( 'You are not allow to edit the poll.', 'buddypress-polls' );
		echo '</div>';

		return;
	}
	$post_id = isset( $_GET['poll_id'] ) ? $_GET['poll_id'] : '';
	$post    = get_post( $post_id );

	$poll_type                = get_post_meta( $post_id, 'poll_type', true );
	$start_time               = get_post_meta( $post_id, '_wbpoll_start_date', true );
	$end_date                 = get_post_meta( $post_id, '_wbpoll_end_date', true );
	$never_expire             = get_post_meta( $post_id, '_wbpoll_never_expire', true );
	$show_result_after_expire = get_post_meta( $post_id, '_wbpoll_show_result_before_expire', true );
	$multivote                = get_post_meta( $post_id, '_wbpoll_multivote', true );

	$answers          = get_post_meta( $post_id, '_wbpoll_answer', true );
	$image_answer_url = get_post_meta( $post_id, '_wbpoll_full_size_image_answer', true );
	$image_answer_url = isset( $image_answer_url ) ? $image_answer_url : array();

	$video_answer_url = get_post_meta( $post_id, '_wbpoll_video_answer_url', true );
	$video_answer_url = isset( $video_answer_url ) ? $video_answer_url : array();

	$audio_answer_url = get_post_meta( $post_id, '_wbpoll_audio_answer_url', true );
	$audio_answer_url = isset( $audio_answer_url ) ? $audio_answer_url : array();

	$html_content = get_post_meta( $post_id, '_wbpoll_html_answer', true );
	$html_content = isset( $html_content ) ? $html_content : array();

	$video_import_info = get_post_meta( $post_id, '_wbpoll_video_import_info', true );
	$video_import_info = isset( $video_import_info ) ? $video_import_info : array();

	$audio_import_info = get_post_meta( $post_id, '_wbpoll_audio_import_info', true );
	$audio_import_info = isset( $audio_import_info ) ? $audio_import_info : array();

	$options = array();

	if ( $poll_type == 'default' ) {
		foreach ( $answers as $key => $ans ) {
			$options[ $key ] = $ans;
		}
	} elseif ( $poll_type == 'image' ) {
		foreach ( $answers as $key => $ans ) {
			$options[ $key ] = array(
				'ans'   => $ans,
				'image' => $image_answer_url[ $key ],
			);
		}
	} elseif ( $poll_type == 'video' ) {
		foreach ( $answers as $key => $ans ) {
			$options[ $key ] = array(
				'ans'        => $ans,
				'video'      => $video_answer_url[ $key ],
				'suggestion' => ( isset( $video_import_info[ $key ] ) ) ? $video_import_info[ $key ] : 'no',
			);
		}
	} elseif ( $poll_type == 'audio' ) {
		foreach ( $answers as $key => $ans ) {
			$options[ $key ] = array(
				'ans'        => $ans,
				'audio'      => $audio_answer_url[ $key ],
				'suggestion' => ( isset( $audio_import_info[ $key ] ) ) ? $audio_import_info[ $key ] : 'no',
			);
		}
	} elseif ( $poll_type == 'html' ) {
		foreach ( $answers as $key => $ans ) {
			$options[ $key ] = array(
				'ans'  => $ans,
				'html' => $html_content[ $key ],
			);
		}
	}

	$add_additional_fields = get_post_meta( $post_id, '_wbpoll_add_additional_fields', true );
} else {
	$never_expire             = '0';
	$show_result_after_expire = '0';
	$multivote                = '0';
	$add_additional_fields    = '0';
}

if ( isset( $poll_type ) && ! empty( $poll_type ) ) {
	$poll_type = $poll_type;
} else {
	$poll_type = '';
}
?>

<div class="main-poll-create">
	<div class="deshboard-top">
		<?php if ( isset( $_GET['poll_id'] ) && ! empty( $_GET['poll_id'] ) ) { ?>
			<div class="main-title">
				<h3><?php esc_html_e( 'Edit Poll', 'buddypress-polls' ); ?></h3>
			</div>
		<?php } else { ?>
			<div class="main-title">
				<h3><?php esc_html_e( 'Add Poll', 'buddypress-polls' ); ?></h3>
			</div>
		<?php } ?>

	</div>
	<div class="poll-create">
		<?php if ( is_user_logged_in() ) { ?>
			<form id="wbpolls-create" class="wbpolls-create">
				<input type="hidden" name="author_id" id="author_id" value="<?php echo esc_attr( get_current_user_id() ); ?>">
				<input type="hidden" name="poll_id" id="poll_id" value="
				<?php
				if ( isset( $post_id ) && ! empty( $post_id ) ) {
					echo esc_attr( $post_id );
				}
				?>
				">
				<div class="form-group">
					<label for="polltitle"><?php esc_html_e( 'Poll Title', 'buddypress-polls' ); ?></label>
					<input type="text" class="form-control" name="title" id="polltitle" value="<?php echo ! empty( $_GET['poll_id'] ) ? esc_attr( $post->post_title ) : ''; ?>">
					<span id="error_title" style="color:red;"></span>
				</div>
				<div class="form-group">
					<label for="polltitle"><?php esc_html_e( 'Poll Description', 'buddypress-polls' ); ?></label>
					<!-- <textarea class="form-control wp_editor" name="content" id="poll-content"></textarea> -->
					<?php
					$content = isset( $_GET['poll_id'] ) ? $post->post_content : ''; // Set initial content if needed.

					// Output the Rich Textarea.
					wp_editor(
						$content,
						'poll-content',
						array(
							'textarea_name' => 'content', // Replace with your desired field name.
							'editor_height' => 300, // Set the height of the editor.
						// Additional settings and configurations can be added here.
						)
					);
					?>
				</div>
				<div class="form-group">
					<label for="polltitle"><?php esc_html_e( 'Poll Type', 'buddypress-polls' ); ?></label>
					<select class="form-control" name="poll_type" id="poll_type">
						<option value=""
						<?php
						if ( $poll_type == '' ) {
							echo 'selected';
						}
						?>
						><?php esc_html_e( 'Select Poll Type', 'buddypress-polls' ); ?></option>
						<option value="default"
						<?php
						if ( $poll_type == 'default' ) {
							echo 'selected';
						}
						?>
						><?php esc_html_e( 'Text', 'buddypress-polls' ); ?></option>
						<option value="image"
						<?php
						if ( $poll_type == 'image' ) {
							echo 'selected';
						}
						?>
						><?php esc_html_e( 'Image', 'buddypress-polls' ); ?></option>
						<option value="video"
						<?php
						if ( $poll_type == 'video' ) {
							echo 'selected';
						}
						?>
						><?php esc_html_e( 'Video', 'buddypress-polls' ); ?></option>
						<option value="audio"
						<?php
						if ( $poll_type == 'audio' ) {
							echo 'selected';
						}
						?>
						><?php esc_html_e( 'Audio', 'buddypress-polls' ); ?></option>
						<option value="html"
						<?php
						if ( $poll_type == 'html' ) {
							echo 'selected';
						}
						?>
						><?php esc_html_e( 'HTML', 'buddypress-polls' ); ?></option>
					</select>
					<span id="error_type" style="color:red;"></span>
				</div>
				<?php if ( $poll_type == 'default' ) { ?>
					<div class="wbpolls-answer-wrap">
						<div class="row wbpoll-list-item" id="type_text" style="">
							<div class="ans-records text_records-edit">
								<div class="ans-records-wrap">
									<label><?php esc_html_e( 'Text Answer', 'buddypress-polls' ); ?></label>
									<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="<?php echo ( isset( $options[0] ) ) ? esc_attr( $options[0] ) : ''; ?>">
									<input type="hidden" id="wbpoll_answer_extra_type" value="default" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
								</div>
								<a class="add-field extra-fields-text-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="text_records_dynamic-edit">
								<?php

								foreach ( $options as $key => $optn ) {
									if ( $key != 0 ) {
										?>
										<div class="remove remove2">
											<div class="ans-records-wrap">
												<label><?php esc_html_e( 'Text Answer', 'buddypress-polls' ); ?></label>
												<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="<?php echo esc_attr( $optn ); ?>">
												<input type="hidden" id="wbpoll_answer_extra_type" value="default" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
											</div>
											<a class="add-field extra-fields-text-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
											<a href="#" class="remove-field btn-remove-text"><?php esc_html_e( 'Remove Fields', 'buddypress-polls' ); ?></a>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
					</div>
				<?php } elseif ( $poll_type == 'image' ) { ?>
					<div class="wbpolls-answer-wrap">
						<div class="row wbpoll-list-item" id="type_image" style="">
							<div class="ans-records image_records_edit">
								<div class="ans-records-wrap">
									<div class="wbpoll-image-input-preview">
										<div class="wbpoll-image-input-preview-thumbnail" id="wbpoll-image-input-preview-thumbnail">
											<img width="266" height="266" src="<?php echo esc_attr( $options[0]['image'] ); ?>">
										</div>

									</div>
									<div class="wbpoll-image-input-details">
										<label><?php esc_html_e( 'Image Answer', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" type="text" class="wbpoll_answer" value="<?php echo esc_attr( $options[0]['ans'] ); ?>">
										<input type="hidden" id="wbpoll_answer_extra_type" value="image" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
										<label><?php esc_html_e( 'Image URL', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_full_size_image_answer[]" data-name="_wbpoll_full_size_image_answer[]" class="wbpoll_image_answer_url" id="wbpoll_image_answer_url" type="url" value="<?php echo esc_attr( $options[0]['image'] ); ?>">
										<button type="button" class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-image"></button>
									</div>
								</div>
								<a class="add-field extra-fields-image-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="image_records_dynamic_edit">
								<?php
								foreach ( $options as $key => $optn ) {
									if ( $key != 0 ) {
										?>
										<div class="remove remove<?php echo count( $options ); ?>">
											<div class="ans-records-wrap">
												<div class="wbpoll-image-input-preview">
													<div class="wbpoll-image-input-preview-thumbnail" id="wbpoll-image-input-preview-thumbnail"><img width="266" height="266" src="<?php echo esc_attr( $optn['image'] ); ?>"></div>
												</div>
												<div class="wbpoll-image-input-details">
													<label><?php esc_html_e( 'Image Answer', 'buddypress-polls' ); ?></label>
													<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" type="text" class="wbpoll_answer" value="<?php echo esc_attr( $optn['ans'] ); ?>">
													<input type="hidden" id="wbpoll_answer_extra_type" value="image" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
													<label><?php esc_html_e( 'Image URL', 'buddypress-polls' ); ?></label>
													<input name="_wbpoll_full_size_image_answer[]" data-name="_wbpoll_full_size_image_answer[]" class="wbpoll_image_answer_url" id="wbpoll_image_answer_url" type="url" value="<?php echo esc_attr( $optn['image'] ); ?>">
													<button type="button" class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-image"></button>
												</div>
											</div>
											<a class="add-field extra-fields-image-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
											<a href="#" class="remove-field btn-remove-image"><?php esc_html_e( 'Remove Fields', 'buddypress-polls' ); ?></a>
										</div>

										<?php
									}
								}
								?>
							</div>
						</div>
					</div>
				<?php } elseif ( $poll_type == 'video' ) { ?>
					<div class="wbpolls-answer-wrap">
						<div class="row wbpoll-list-item" id="type_video" style="">
							<div class="ans-records video_records_edit">
								<div class="ans-records-wrap ans-video-records-wrap">
									<div class="wbpoll-image-input-preview">
										<div class="wbpoll-image-input-preview-thumbnail">
											<?php if ( $options[0]['suggestion'] == 'yes' ) { ?>
												<iframe width="420" height="345" src="<?php echo $options[0]['video']; ?>"></iframe> <?php //phpcs:ignore  ?>
											<?php } else { ?>
												<video src="<?php echo esc_url( $options[0]['video'] ); ?>" controls="" poster="" preload="none"></video>
											<?php } ?>
										</div>
									</div>
									<div class="wbpoll-image-input-details">
										<label><?php esc_html_e( 'Video Answer', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" type="text" class="wbpoll_answer" value="<?php echo esc_attr( $options[0]['ans'] ); ?>">
										<input type="hidden" id="wbpoll_answer_extra_type" value="video" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
										<label><?php esc_html_e( 'Video URL', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_video_answer_url[]" data-name="_wbpoll_video_answer_url[]" id="wbpoll_video_answer_url" class="wbpoll_video_answer_url" type="url" value="<?php echo esc_attr( $options[0]['video'] ); ?>">
										<button type="button" class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-video"></button>
										<div class="wbpoll-input-group-suggestions hide_suggestion" style="display:none;">
											<span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
											<input type="radio" class="yes_video wbpoll_video_import_info" id="yes" name="_wbpoll_video_import_info[0]" data-name="_wbpoll_video_import_info[]" value="yes" 
											<?php
											if ( $options[0]['suggestion'] == 'yes' ) {
												echo 'checked="checked"';
											}
											?>
											>
											<label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
											<input type="radio" id="no" name="_wbpoll_video_import_info[]" data-name="_wbpoll_video_import_info[0]" value="no" class="wbpoll_video_import_info"
											<?php
											if ( $options[0]['suggestion'] == 'no' ) {
												echo 'checked="checked"';
											}
											?>
											>
											<label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label>
										</div>
									</div>
								</div>
								<a class="add-field extra-fields-video-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="video_records_dynamic_edit">
								<?php
								foreach ( $options as $key => $optn ) {
									if ( $key != 0 ) {
										?>
										<div class="remove remove2">
											<div class="ans-records-wrap ans-video-records-wrap">
												<div class="wbpoll-image-input-preview">
													<div class="wbpoll-image-input-preview-thumbnail">
														<?php if ( $optn['suggestion'] == 'yes' ) { ?>
															<iframe width="420" height="345" src="<?php echo esc_url( $optn['video'] ); ?>"></iframe>
														<?php } else { ?>
															<video src="<?php echo esc_url( $optn['video'] ); ?>" controls="" poster="" preload="none"></video>
														<?php } ?>
													</div>
												</div>
												<div class="wbpoll-image-input-details">
													<label><?php esc_html_e( 'Video Answer', 'buddypress-polls' ); ?></label>
													<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" type="text" class="wbpoll_answer" value="<?php echo esc_attr( $optn['ans'] ); ?>">
													<input type="hidden" id="wbpoll_answer_extra_type" value="video" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
													<label><?php esc_html_e( 'Video URL', 'buddypress-polls' ); ?></label>
													<input name="_wbpoll_video_answer_url[]" data-name="_wbpoll_video_answer_url[]" id="wbpoll_video_answer_url" class="wbpoll_video_answer_url" type="url" value="<?php echo esc_attr( $optn['video'] ); ?>">
													<button type="button" class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-video"></button>
													<div class="wbpoll-input-group-suggestions hide_suggestion" style="display:none;">
														<span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
														<input type="radio" class="yes_video wbpoll_video_import_info" id="yes" name="_wbpoll_video_import_info[<?php echo esc_attr( $key ); ?>]" data-name="_wbpoll_video_import_info[]" value="yes"
														<?php
														if ( $optn['suggestion'] == 'yes' ) {
															echo 'checked="checked"';
														}
														?>
														>
														<label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
														<input type="radio" id="no" name="_wbpoll_video_import_info[<?php echo esc_attr( $key ); ?>]" data-name="_wbpoll_video_import_info[]" value="no" class="wbpoll_video_import_info"
														<?php
														if ( $optn['suggestion'] == 'no' ) {
															echo 'checked="checked"';
														}
														?>
														>
														<label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label>
													</div>
												</div>
											</div>
											<a class="add-field extra-fields-video-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
											<a href="#" class="remove-field btn-remove-video"><?php esc_html_e( 'Remove Fields', 'buddypress-polls' ); ?></a>
										</div>

										<?PHP
									}
								}
								?>
							</div>
						</div>
					</div>
				<?php } elseif ( $poll_type == 'audio' ) { ?>
					<div class="wbpolls-answer-wrap">
						<div class="row wbpoll-list-item" id="type_audio" style="">
							<div class="ans-records audio_records_edit">
								<div class="ans-records-wrap ans-audio-records-wrap">
									<div class="wbpoll-image-input-preview">
										<div class="wbpoll-image-input-preview-thumbnail">
											<?php if ( $options[0]['suggestion'] == 'yes' ) { ?>
												<iframe width="420" height="345" src="<?php echo esc_url( $options[0]['audio'] ); ?>"></iframe>
											<?php } else { ?>
												<audio src="<?php echo esc_url( $options[0]['audio'] ); ?>" controls="" preload="none"></audio>
											<?php } ?>
										</div>
									</div>
									<div class="wbpoll-image-input-details">
										<label><?php esc_html_e( 'Audio Answer', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="<?php echo esc_attr( $options[0]['ans'] ); ?>">
										<input type="hidden" id="wbpoll_answer_extra_type" value="audio" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
										<label><?php esc_html_e( 'Audio URL', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_audio_answer_url[]" data-name="_wbpoll_audio_answer_url[]" id="wbpoll_audio_answer_url" class="wbpoll_audio_answer_url" type="url" value="<?php echo esc_attr( $options[0]['audio'] ); ?>">
										<button type="button" class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-audio"></button>
										<div class="wbpoll-input-group-suggestions hide_suggestion" style="display:none;"><span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
											<input type="radio" class="yes_audio wbpoll_audio_import_info" id="yes" name="_wbpoll_audio_import_info[0]" data-name="_wbpoll_audio_import_info[]" value="yes"
											<?php
											if ( $options[0]['suggestion'] == 'yes' ) {
												echo 'checked="checked"';
											}
											?>
											>
											<label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
											<input type="radio" id="no" name="_wbpoll_audio_import_info[0]" data-name="_wbpoll_audio_import_info[]" value="no" class="wbpoll_audio_import_info"
											<?php
											if ( $options[0]['suggestion'] == 'no' ) {
												echo 'checked="checked"';
											}
											?>
											>
											<label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label><br>
										</div>
									</div>
								</div>
								<a class="add-field extra-fields-audio-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="audio_records_dynamic_edit">
								<?php
								foreach ( $options as $key => $optn ) {
									if ( $key != 0 ) {
										?>
										<div class="remove remove2">
											<div class="ans-records-wrap ans-audio-records-wrap">
												<div class="wbpoll-image-input-preview">
													<div class="wbpoll-image-input-preview-thumbnail">
														<?php if ( $optn['suggestion'] == 'yes' ) { ?>
															<iframe width="420" height="345" src="<?php echo esc_url( $optn['audio'] ); ?>"></iframe>
														<?php } else { ?>
															<audio src="<?php echo esc_url( $optn['audio'] ); ?>" controls="" preload="none"></audio>
														<?php } ?>
													</div>
												</div>
												<div class="wbpoll-image-input-details">
													<label><?php esc_html_e( 'Audio Answer', 'buddypress-polls' ); ?></label>
													<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="<?php echo esc_attr( $optn['ans'] ); ?>">
													<input type="hidden" id="wbpoll_answer_extra_type" value="audio" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
													<label><?php esc_html_e( 'Audio URL', 'buddypress-polls' ); ?></label>
													<input name="_wbpoll_audio_answer_url[]" data-name="_wbpoll_audio_answer_url[]" id="wbpoll_audio_answer_url" class="wbpoll_audio_answer_url" type="url" value="<?php echo esc_attr( $optn['audio'] ); ?>">
													<button type="button" class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-audio"></button>
													<div class="wbpoll-input-group-suggestions hide_suggestion" style="display:none;"><span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
														<input type="radio" class="yes_audio wbpoll_audio_import_info" id="yes" name="_wbpoll_audio_import_info[<?php echo esc_attr( $key ); ?>]" data-name="_wbpoll_audio_import_info[]" value="yes" 
														<?php
														if ( $optn['suggestion'] == 'yes' ) {
															echo 'checked="checked"';
														}
														?>
														>
														<label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
														<input type="radio" id="no" name="_wbpoll_audio_import_info[<?php echo esc_attr( $key ); ?>]" data-name="_wbpoll_audio_import_info[]" value="no" class="wbpoll_audio_import_info"
														<?php
														if ( $optn['suggestion'] == 'no' ) {
															echo 'checked="checked"';
														}
														?>
														>
														<label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label><br>
													</div>
												</div>
											</div>
											<a class="add-field extra-fields-audio-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
											<a href="#" class="remove-field btn-remove-audio"><?php esc_html_e( 'Remove Fields', 'buddypress-polls' ); ?></a>
										</div>

										<?php
									}
								}
								?>
							</div>
						</div>
					</div>
				<?php } elseif ( $poll_type == 'html' ) { ?>
					<div class="wbpolls-answer-wrap">
						<div class="row wbpoll-list-item" id="type_html" style="">
							<div class="ans-records html_records_edit">
								<div class="ans-records-wrap">
									<label><?php esc_html_e( 'HTML Answer', 'buddypress-polls' ); ?></label>
									<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="<?php echo ( ! empty( $options ) ) ? esc_attr( $options[0]['ans'] ) : ''; ?>">
									<label><?php esc_html_e( 'HTML Content', 'buddypress-polls' ); ?></label>
									<textarea name="_wbpoll_html_answer[]" data-name="_wbpoll_html_answer[]" id="wbpoll_html_answer_textarea" class="wbpoll_html_answer_textarea tiny"><?php echo ( ! empty( $options ) ) ? esc_attr( $options[0]['html'] ) : ''; ?></textarea>
									<input type="hidden" id="wbpoll_answer_extra_type" value="html" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
								</div>
								<a class="add-field extra-fields-html-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="html_records_dynamic_edit">
								<?php
								foreach ( $options as $key => $optn ) {
									if ( $key != 0 ) {
										?>
										<div class="remove remove1">
											<div class="ans-records-wrap">
												<label><?php esc_html_e( 'HTML Answer', 'buddypress-polls' ); ?></label>
												<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="<?php echo esc_attr( $optn['ans'] ); ?>">
												<label><?php esc_html_e( 'HTML Content', 'buddypress-polls' ); ?></label>
												<textarea name="_wbpoll_html_answer[]" data-name="_wbpoll_html_answer[]" id="wbpoll_html_answer_textarea" class="wbpoll_html_answer_textarea tiny"><?php echo esc_html( $optn['html'] ); ?></textarea>
												<input type="hidden" id="wbpoll_answer_extra_type" value="html" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
											</div>
											<a class="add-field extra-fields-html-edit" data-id="<?php echo count( $options ); ?>" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
											<a href="#" class="remove-field btn-remove-html"><?php esc_html_e( 'Remove Fields', 'buddypress-polls' ); ?></a>
										</div>

										<?PHP
									}
								}
								?>
							</div>
						</div>
					</div>
				<?php } ?>

					<div class="wbpolls-answer-wrap">
						<!-- for text type -->
						<div class="row wbpoll-list-item" id="type_text" style="display:none;">
							<div class="ans-records text_records">
								<div class="ans-records-wrap">
									<label><?php esc_html_e( 'Text Answer', 'buddypress-polls' ); ?></label>
									<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="">
									<input type="hidden" id="wbpoll_answer_extra_type" value="default" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
								</div>
								<a class="add-field extra-fields-text" data-id="0" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="text_records_dynamic"></div>
						</div>

						<!-- for image type -->
						<div class="row wbpoll-list-item" id="type_image" style="display:none;">
							<div class="ans-records image_records">
								<div class="ans-records-wrap">
									<div class="wbpoll-image-input-preview">
										<div class="wbpoll-image-input-preview-thumbnail" id="wbpoll-image-input-preview-thumbnail">
										</div>
									</div>
									<div class="wbpoll-image-input-details">
										<label><?php esc_html_e( 'Image Answer', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" type="text" class="wbpoll_answer" value="">
										<input type="hidden" id="wbpoll_answer_extra_type" value="image" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
										<label><?php esc_html_e( 'Image URL', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_full_size_image_answer[]" data-name="_wbpoll_full_size_image_answer[]" class="wbpoll_image_answer_url" class="wbpoll_image_answer_url" id="wbpoll_image_answer_url" type="url" value="">
										<button type='button' class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-image"></button>
									</div>
								</div>
								<a class="add-field extra-fields-image" data-id="0" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="image_records_dynamic"></div>
						</div>

						<!-- for video type -->
						<div class="row wbpoll-list-item" id="type_video" style="display:none;">
							<div class="ans-records video_records ">
								<div class="ans-records-wrap ans-video-records-wrap">
									<div class="wbpoll-image-input-preview">
										<div class="wbpoll-image-input-preview-thumbnail">
										</div>
									</div>
									<div class="wbpoll-image-input-details">
										<label><?php esc_html_e( 'Video Answer', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" type="text" class="wbpoll_answer" value="">
										<input type="hidden" id="wbpoll_answer_extra_type" value="video" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
										<label><?php esc_html_e( 'Video URL', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_video_answer_url[]" data-name="_wbpoll_video_answer_url[]" id="wbpoll_video_answer_url" class="wbpoll_video_answer_url" type="url" value="">
										<button type='button' class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-video"></button>
										<div class="wbpoll-input-group-suggestions hide_suggestion" style="display:none;">
											<span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
											<input type="radio" class="yes_video wbpoll_video_import_info" id="yes" name="_wbpoll_video_import_info[]" data-name="_wbpoll_video_import_info[]" value="yes">
											<label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
											<input type="radio" id="no" name="_wbpoll_video_import_info[]" data-name="_wbpoll_video_import_info[]" value="no" class="wbpoll_video_import_info">
											<label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label>
										</div>
									</div>
								</div>
								<a class="add-field extra-fields-video" data-id="0" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="video_records_dynamic"></div>
						</div>

						<!-- for audio type -->
						<div class="row wbpoll-list-item" id="type_audio" style="display:none;">
							<div class="ans-records audio_records ans-audio-records-wrap">
								<div class="ans-records-wrap">
									<div class="wbpoll-image-input-preview">
										<div class="wbpoll-image-input-preview-thumbnail">
										</div>
									</div>
									<div class="wbpoll-image-input-details">
										<label><?php esc_html_e( 'Audio Answer', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="">
										<input type="hidden" id="wbpoll_answer_extra_type" value="audio" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
										<label><?php esc_html_e( 'Audio URL', 'buddypress-polls' ); ?></label>
										<input name="_wbpoll_audio_answer_url[]" data-name="_wbpoll_audio_answer_url[]" id="wbpoll_audio_answer_url" class="wbpoll_audio_answer_url" type="url" value="">
										<button type='button' class="bpolls-attach dashicons dashicons-admin-media" id="bpolls-attach-audio"></button>
										<div class="wbpoll-input-group-suggestions hide_suggestion" style="display:none;"><span><?php esc_html_e( 'Import information from ?', 'buddypress-polls' ); ?></span>
											<input type="radio" class="yes_audio wbpoll_audio_import_info" id="yes" name="_wbpoll_audio_import_info[]" data-name="_wbpoll_audio_import_info[]" value="yes">
											<label for="yes"><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></label>
											<input type="radio" id="no" name="_wbpoll_audio_import_info[]" data-name="_wbpoll_audio_import_info[]" value="no" class="wbpoll_audio_import_info">
											<label for="no"><?php esc_html_e( 'No', 'buddypress-polls' ); ?></label><br>
										</div>
									</div>
								</div>
								<a class="add-field extra-fields-audio" data-id="0" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="audio_records_dynamic"></div>
						</div>

						<!-- for html type -->
						<div class="row wbpoll-list-item" id="type_html" style="display:none;">
							<div class="ans-records html_records">
								<div class="ans-records-wrap">

									<label><?php esc_html_e( 'HTML Answer', 'buddypress-polls' ); ?></label>
									<input name="_wbpoll_answer[]" data-name="_wbpoll_answer[]" id="wbpoll_answer" class="wbpoll_answer" type="text" value="">
									<label><?php esc_html_e( 'HTML Content', 'buddypress-polls' ); ?></label>
									<textarea name="_wbpoll_html_answer[]" data-name="_wbpoll_html_answer[]" id="wbpoll_html_answer_textarea" class="wbpoll_html_answer_textarea tiny"></textarea>
									<input type="hidden" id="wbpoll_answer_extra_type" value="html" name="_wbpoll_answer_extra[][type]" data-name="_wbpoll_answer_extra[][type]" class="wbpoll_answer_extra" />
								</div>
								<a class="add-field extra-fields-html" data-id="0" href="#"><?php esc_html_e( 'Add More', 'buddypress-polls' ); ?></a>
							</div>
							<div class="html_records_dynamic"></div>
						</div>
					</div>
				<span id="error_ans" style="color:red;"></span>

				<div class="wbcom-polls-option-wrap">
					<table class="form-table wbpoll-answer-options">
						<tbody>
							<tr>
								<th><label for="_wbpoll_never_expire"><?php esc_html_e( 'Never Expire', 'buddypress-polls' ); ?></label></th>
								<td>
									<fieldset class="radio_fields">
										<legend class="screen-reader-text"><span><?php esc_html_e( 'input type="radio"', 'buddypress-polls' ); ?></span></legend>
										<label class="wbpoll-answer-options-radio-field" title="g:i a" for="_wbpoll_never_expire-radio">
											<input id="_wbpoll_never_expire-radio" type="radio" name="_wbpoll_never_expire" value="1" <?php checked( $never_expire, 1 ); ?>>
											<span><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></span>
										</label>
										<label class="wbpoll-answer-options-radio-field" title="g:i a" for="_wbpoll_never_expire-radio">
											<input id="_wbpoll_never_expire-radio" type="radio" name="_wbpoll_never_expire" value="0" <?php checked( $never_expire, 0 ); ?>>
											<span><?php esc_html_e( 'No', 'buddypress-polls' ); ?></span>
										</label>
									</fieldset>
									<span class="description"><?php esc_html_e( 'Select if you want your poll to never expire.(can be override from shortcode param)', 'buddypress-polls' ); ?></span>
								</td>
							</tr>
							<tr class="wbpoll_show_date">
								<th><label for="_wbpoll_start_date"><?php esc_html_e( 'Start Date', 'buddypress-polls' ); ?></label></th>
								<td><input type="text" class="wbpollmetadatepicker hasDatepicker" name="_wbpoll_start_date" id="_wbpoll_start_date" value="<?php echo ! empty( $start_time ) ? esc_attr( $start_time ) : esc_attr( current_time( 'Y-m-d H:i:s' ) ); ?>" size="30">
									<span class="description"><?php esc_html_e( 'Poll Start Date. [Note: Field required. Default is today]', 'buddypress-polls' ); ?></span>
								</td>
							</tr>
							<tr class="wbpoll_show_date">
								<?php
								$current_date    = current_time( 'Y-m-d H:i:s' );
								$next_seven_days = date_i18n( 'Y-m-d H:i:s', strtotime( $current_date . ' +7 days' ) );
								?>
								<th><label for="_wbpoll_end_date"><?php esc_html_e( 'End Date', 'buddypress-polls' ); ?></label></th>
								<td><input type="text" class="wbpollmetadatepicker hasDatepicker" name="_wbpoll_end_date" id="_wbpoll_end_date" value="<?php echo ! empty( $end_date ) ? esc_attr( $end_date ) : esc_attr( $next_seven_days ); ?>" size="30">
									<span class="description"><?php esc_html_e( 'Poll End Date. [Note: Field required. Default is next seven days.]', 'buddypress-polls' ); ?></span>
								</td>
							</tr>
							<tr class="wbpoll_result_after_expires">
								<th><label for="_wbpoll_show_result_before_expire"><?php esc_html_e( 'Show Result After Expires', 'buddypress-polls' ); ?></label></th>
								<td>
									<fieldset class="radio_fields">
										<legend class="screen-reader-text"><span><?php esc_html_e( 'input type="radio"', 'buddypress-polls' ); ?></span></legend>
										<label class="wbpoll-answer-options-radio-field" title="g:i a" for="_wbpoll_show_result_before_expire-radio">
											<input id="_wbpoll_show_result_before_expire-radio" type="radio" name="_wbpoll_show_result_before_expire" value="1" <?php checked( $show_result_after_expire, 1 ); ?>>
											<span><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></span>
										</label>
										<label class="wbpoll-answer-options-radio-field" title="g:i a" for="_wbpoll_show_result_before_expire-radio">
											<input id="_wbpoll_show_result_before_expire-radio" type="radio" name="_wbpoll_show_result_before_expire" value="0" <?php checked( $show_result_after_expire, 0 ); ?>>
											<span><?php esc_html_e( 'No', 'buddypress-polls' ); ?></span>
										</label>
									</fieldset>
									<span class="description"><?php esc_html_e( 'Select if you want poll to show result After expires. After expires the result will be shown always.', 'buddypress-polls' ); ?></span>
								</td>
							</tr>
							<tr>
								<th><label for="_wbpoll_multivote"><?php esc_html_e( 'Enable Multi Choice', 'buddypress-polls' ); ?></label></th>
								<td>
									<fieldset class="radio_fields">
										<legend class="screen-reader-text"><span><?php esc_html_e( 'input type="radio"', 'buddypress-polls' ); ?></span></legend>
										<label class="wbpoll-answer-options-radio-field" title="g:i a" for="_wbpoll_multivote-radio">
											<input id="_wbpoll_multivote-radio" type="radio" name="_wbpoll_multivote" value="1" <?php checked( $multivote, 1 ); ?>>
											<span><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></span>
										</label>
										<label class="wbpoll-answer-options-radio-field" title="g:i a" for="_wbpoll_multivote-radio">
											<input id="_wbpoll_multivote-radio" type="radio" name="_wbpoll_multivote" value="0" <?php checked( $multivote, 0 ); ?>>
											<span><?php esc_html_e( 'No', 'buddypress-polls' ); ?></span>
										</label>
									</fieldset>
									<span class="description"><?php esc_html_e( 'Can user vote multiple option', 'buddypress-polls' ); ?></span>
								</td>
							</tr>
							<?php
							if ( ! empty( $option_value ) ) {
								$wbpolls_user_add_extra_op = isset( $option_value['wbpolls_user_add_extra_op'] ) ? $option_value['wbpolls_user_add_extra_op'] : '';
							}
							if ( $wbpolls_user_add_extra_op == 'yes' ) {
								?>
								<tr id="addtitonal_option" style="
								<?php
								if ( $poll_type != 'default' ) {
									echo 'display:none;'; }
								?>
								">
									<th><label for="_wbpoll_multivote"><?php esc_html_e( 'Add Additional poll option', 'buddypress-polls' ); ?></label></th>
									<td>
										<fieldset class="radio_fields">
											<legend class="screen-reader-text"><span><?php esc_html_e( 'input type="radio"', 'buddypress-polls' ); ?></span></legend>
											<label class="wbpoll-answer-options-radio-field" title="g:i a" for="_wbpoll_multivote-radio">
												<?php
												if ( $wbpolls_user_add_extra_op == 'yes' && ! empty( $add_additional_fields ) || $add_additional_fields == 1 ) {
													?>
												<input id="_wbpoll_multivote-radio" type="radio" name="_wbpoll_add_additional_fields" value="1" checked>
												<?php } else { ?>
													<input id="_wbpoll_multivote-radio" type="radio" name="_wbpoll_add_additional_fields" value="1">
												<?php } ?>
												<span><?php esc_html_e( 'Yes', 'buddypress-polls' ); ?></span>
											</label>
											<label class="wbpoll-answer-options-radio-field" title="g:i a" for="_wbpoll_multivote-radio">
											<?php

											if ( $wbpolls_user_add_extra_op == 'no' && ! empty( $add_additional_fields ) || $add_additional_fields == 0 ) {
												?>
												<input id="_wbpoll_multivote-radio" type="radio" name="_wbpoll_add_additional_fields" value="0" checked>
												<?php } else { ?>
													<input id="_wbpoll_multivote-radio" type="radio" name="_wbpoll_add_additional_fields" value="0">
												<?php } ?>
												<span><?php esc_html_e( 'No', 'buddypress-polls' ); ?></span>
											</label>
										</fieldset>
										<span class="description"><?php esc_html_e( 'Add Additional poll option only for text poll.', 'buddypress-polls' ); ?></span>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<?php if ( isset( $post_id ) && ! empty( $post_id ) ) : ?>
					<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Poll Update', 'buddypress-polls' ); ?></button>
				<?php else : ?>
					<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Poll Create', 'buddypress-polls' ); ?></button>
				<?php endif ?>

			</form>
			<div class="wbpoll-voted-info wbpoll-success" id="pollsuccess" style="display:none;"></div>
		<?php } else { ?>
			<div class="wbpoll_wrapper wbpoll_wrapper-content_hook" data-reference="content_hook">
				<p class="wbpoll-voted-info wbpoll-alert"><?php esc_html_e( 'This page content only for login members.', 'buddypress-polls' ); ?> </p>
			</div>
		<?php } ?>
	</div>
</div>
<?php
$post = $temp_post;
