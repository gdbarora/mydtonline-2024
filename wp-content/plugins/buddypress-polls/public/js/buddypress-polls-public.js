(function($) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	jQuery( document ).ready(
		function() {
			var poll_html;
			$( "form#whats-new-form" ).attr( "enctype", "multipart/form-data" );

			if (bpolls_ajax_object.reign_polls) {
				  var body = document.body;
				  body.classList.add( "reign-polls" );
			}

			if (bpolls_ajax_object.rt_poll_fix && bpolls_ajax_object.nouveau) {

				$( document ).on(
					'click',
					'.bpolls-icon',
					function() {
						$( '#rtmedia-add-media-button-post-update' ).hide();

					}
				);
				$( document ).on(
					'click',
					'.bpolls-cancel',
					function() {
						$( '#rtmedia-add-media-button-post-update' ).show();
					}
				);

				$( document ).on(
					'focus',
					'#whats-new',
					function() {
						if ($( '#rtmedia-add-media-button-post-update' ).is( ':hidden' )) {
								$( '#rtmedia-add-media-button-post-update' ).show();
						}
					}
				);

				$( document ).on(
					'click',
					'#rtmedia-add-media-button-post-update',
					function() {
						//$('.bpolls-html-container').hide();
					}
				);

				$( document ).on(
					'focus',
					'#whats-new',
					function() {
						if ($( '.bpolls-html-container' ).is( ':hidden' )) {
								$( '.bpolls-html-container' ).show();
						}
					}
				);
			}

			//Manage Poll icon with Buddyboss Plateform
			$( document ).on(
				'click focus',
				'#whats-new',
				function(){
					if (bpolls_ajax_object.buddyboss ) {
						$( '#whats-new-toolbar' ).append( $( '.bpolls-html-container' ) );
						$( '#whats-new-attachments' ).append( $( '.bpolls-polls-option-html' ) );

						if ( $( '.whats-new-form-footer #whats-new-toolbar .bpolls-html-container' ).length == 0 ) {
							$( '.bpolls-html-container' ).appendTo( $( '.whats-new-form-footer #whats-new-toolbar' ) );
						}
					}
				}
			);
			
			if (bpolls_ajax_object.buddyboss && bpolls_ajax_object.hide_poll_icon != 'yes') {
				var bb_polls_Interval;

				function bb_pools_icon_push() {

					bb_polls_Interval = setInterval(
						function() {
							
							if (bpolls_ajax_object.buddyboss && $( '#whats-new-form:not(.focus-in) #whats-new-toolbar .bpolls-html-container-placeholder' ).length == 0 ) {
								
								$( '#whats-new-form:not(.focus-in) #whats-new-toolbar' ).append( '<div class="post-elements-buttons-item bpolls-html-container-placeholder"><span class="bpolls-icon bp-tooltip" data-bp-tooltip-pos="up" data-bp-tooltip="' + bpolls_ajax_object.add_poll_text + '"><i class="wb-icons wb-icon-bar-chart"></i></span></div>' );
							}
						},
						10
					);

				}

				bb_pools_icon_push();

				$( document ).on(
					'click',
					'.bb-model-close-button, .activity-update-form-overlay',
					function(){
						clearInterval( bb_polls_Interval );
						bb_pools_icon_push();
					}
				);

					 /* jQuery Ajax prefilter*/
					$.ajaxPrefilter(
						function( options, originalOptions, jqXHR ) {
							try {
								if ( originalOptions.data == null || typeof ( originalOptions.data ) == 'undefined' || typeof ( originalOptions.data.action ) == 'undefined' ) {
									 return true;
								}
							} catch ( e ) {
								return true;
							}

							if ( originalOptions.data.action == 'post_update' ) {
								clearInterval( bb_polls_Interval );
								bb_pools_icon_push();
							}

						}
					);
			}

			$( document ).on(
				'focus',
				'#whats-new',
				function() {
					
					$( '#whats-new-options' ).addClass( 'bpolls-rtm-class' );
				}
			);

			/*==============================================
				=            add new poll option js            =
				==============================================*/
			$( document ).on(
				'click',
				'.bpolls-icon-dialog-cancel',
				function() {
					$( '.bpolls-icon-dialog' ).removeClass( 'is-visible' );
				}
			);

			$( document ).on(
				'click',
				'.bpolls-add-option',
				function() {
					var max_options = bpolls_ajax_object.polls_option_lmit;
					if ( $( '.bpolls-option' ).length >= max_options ) {
						  $( '.bpolls-icon-dialog' ).addClass( 'is-visible' );

					} else {

						 var clonedObj = $( this ).parent().siblings().find( '.bpolls-option:first' ).clone().insertAfter( $( this ).parent().siblings().find( '.bpolls-option:last' ) );

						clonedObj.find( 'input' ).each(
							function() {
								this.value       = '';
								this.placeholder = '';
							}
						);

						if (clonedObj.length == 0 ) {
							$( '.bpolls-sortable' ).html( '<div class="bpolls-option">' + option_html + '</div>' );
						}
					}
				}
			);

			/*=====  End of add new poll option js  ======*/

			/*==========================================
				=            delete poll option            =
				==========================================*/

			$( document ).on(
				'click',
				'.bpolls-option-delete',
				function() {
					$( this ).parent( '.bpolls-option' ).remove();
				}
			);

			/*=====  End of delete poll option  ======*/

			/*============================================
				=            Show hide poll panel            =
				============================================*/

			$( document ).on(
				'click',
				'.bpolls-icon',
				function() {
					if ( $( '.quote-btn' ).length != 0 ) {
						  $( '.bg-type-input' ).val( '' );
						  $( '.bg-type-value' ).val( '' );
						  $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotesimg-bg-selected' );
						  $( '#whats-new, #bppfa-whats-new' ).removeClass( 'quotescolors-bg-selected' );
						  $( "#whats-new, #bppfa-whats-new" ).css( "background-image", '' );
						  $( "#whats-new, #bppfa-whats-new" ).css( "background", '' );
						  $( "#whats-new, #bppfa-whats-new" ).css( "color", '' );
						  $( '.bpquotes-selection' ).css( 'pointerEvents', 'auto' );
					}

					if ( $( '.bpchk-allow-checkin' ).length != 0  ) {
						if (typeof bpchk_public_js_obj !== 'undefined' ) {
							var data = {
								'action': 'bpchk_cancel_checkin'
							}
							$.ajax(
								{
									dataType: "JSON",
									url: bpchk_public_js_obj.ajaxurl,
									type: 'POST',
									data: data,
									success: function (response) {
										$( '.bpchk-checkin-temp-location' ).remove();
									},
								}
							);
						}
						$( '#bpchk-autocomplete-place' ).val( '' );
						$( '#bpchk-checkin-place-lat' ).val( '' );
						$( '#bpchk-checkin-place-lng' ).val( '' );

						if ( typeof BPCHKPRO !== 'undefined' ) {
							BPCHKPRO.delete_cookie( 'bpchkpro_lat' );
							BPCHKPRO.delete_cookie( 'bpchkpro_lng' );
							BPCHKPRO.delete_cookie( 'bpchkpro_place' );
							BPCHKPRO.delete_cookie( 'add_place' );
						}
					}

					$( '.bpolls-polls-option-html' ).slideToggle( 500 );

					$( '#bpolls-datetimepicker' ).datetimepicker();
					var poll_html   = $( '.bpolls-polls-option-html' ).html();
					var option_html = $( '.bpolls-option' ).html();

					$(
						function() {
							$( '.bpolls-sortable' ).sortable(
								{
									handle: '.bpolls-sortable-handle'
								}
							);
							$( '.bpolls-sortable' ).disableSelection();
						}
					);
				}
			);

			/*=====  End of Show hide poll panel  ======*/

			/*==================================================================
				=            clear html and toggle on poll cancellation            =
				==================================================================*/

			$( document ).on(
				'click',
				'.bpolls-cancel',
				function() {
					$( "#aw-whats-new-reset" ).trigger( "click" );
					$( '.bpolls-input' ).each(
						function(){
							$( this ).val( '' );
						}
					);
					$( '.bpolls-polls-option-html' ).html( poll_html );
					$( '.bpolls-polls-option-html' ).slideUp( 500 );
					$( '.bpolls-sortable' ).sortable(
						{
							handle: '.bpolls-sortable-handle'
						}
					);
					$( '.bpolls-sortable' ).disableSelection();
				}
			);

			/*=====  End of clear html and toggle on poll cancellation  ======*/

			$( document ).on(
				'change',
				'input.bpolls_input_options',
				function() {

					var poll_option = [];
					$( "input.bpolls_input_options" ).each(
						function() {
							if ($( this ).val()) {
								poll_option.push( $( this ).val() );
							}
						}
					);
					var is_poll;
					if (poll_option.length !== 0) {
							is_poll = 'yes';
					} else {
						  is_poll = 'no'
					}

					var data = {
						'action': 'bpolls_set_poll_type_true',
						'poll_option': poll_option,
						'is_poll': is_poll,
						'ajax_nonce': bpolls_ajax_object.ajax_nonce
					};

					$.post(
						bpolls_ajax_object.ajax_url,
						data,
						function(response) {
							console.log( response );
						}
					);

				}
			);

			/*==========================================================
				=            solve glitch on post update submit            =
				==========================================================*/

			$( "#aw-whats-new-submit" ).click(
				function() {
					$( '.bpolls-polls-option-html' ).html( poll_html );
					if ($( '.bpolls-polls-option-html' ).is( ':visible' )) {
						  $( '.bpolls-polls-option-html' ).slideToggle( 500 );
					}
				}
			);

			/*=====  End of solve glitch on post update submit  ======*/

			/*======================================================
				=            Ajax request to save poll vote            =
				======================================================*/

			$( document ).on(
				'click',
				'.bpolls-vote-submit',
				function() {
					var submit_event      = $( this );
					var submit_event_text = $( this ).html();
					var s_array           = $( this ).closest( '.bpolls-vote-submit-form' ).serializeArray();
					var len               = s_array.length;
					var dataObj           = {};
					for (var i = 0; i < len; i++) {
						  dataObj[s_array[i].name] = s_array[i].value;
					}
					var bpoll_activity_id = dataObj['bpoll_activity_id'];

					if (dataObj['bpolls_vote_optn[]'] == undefined) {
						 submit_event.html( bpolls_ajax_object.optn_empty_text + ' <i class="fa fa-exclamation-triangle"></i>' );
						 return;
					} else {
						submit_event.html( submit_event_text );
					}

					submit_event.html( bpolls_ajax_object.submit_text + ' <i class="fa fa-refresh fa-spin"></i>' );
					var poll_data = $( this ).closest( '.bpolls-vote-submit-form' ).serialize();

					var data = {
						'action': 'bpolls_save_poll_vote',
						'poll_data': poll_data,
						'ajax_nonce': bpolls_ajax_object.ajax_nonce
					};

					$.post(
						bpolls_ajax_object.ajax_url,
						data,
						function(response) {

							var res = JSON.parse( response );
							if (res.bpolls_thankyou_feedback != '' ) {
								submit_event.after( '<p class="bpolls-feedback-message">' + res.bpolls_thankyou_feedback + '</p>' );
							}

							$.each(
								res,
								function(i, item) {

									var input_obj = submit_event.closest( '.bpolls-vote-submit-form' ).find( "#" + i );
									$( input_obj ).parents( '.bpolls-item' ).find( '.bpolls-item-width' ).animate(
										{
											width: item.vote_percent
										},
										500
									);

									$( input_obj ).parents( '.bpolls-item' ).find( '.bpolls-percent' ).text( item.vote_percent );
									$( input_obj ).parents( '.bpolls-check-radio-div' ).siblings( '.bpolls-votes' ).html( item.bpolls_votes_txt );
									$( input_obj ).parents().parents( '.bpolls-item' ).find( '.bpolls-result-votes' ).html( item.bpolls_votes_content );

								}
							);
							$( '#activity-' + bpoll_activity_id + ' .bpolls-item input' ).hide();
							$( '#activity-' + bpoll_activity_id + ' .bpolls-add-user-item' ).remove();
							submit_event.remove();
						}
					);
				}
			);

			/*=====  End of Ajax request to save poll vote  ======*/
			$( '#whats-new,#bppfa-whats-new' ).focus(
				function() {
					jQuery( '.bpolls-icon' ).click(
						function() {
							jQuery( '.bpquotes-bg-selection-div' ).hide();
							jQuery( '.bp-checkin-panel' ).hide();
						}
					);
				}
			);

			$( document ).on(
				'click',
				'.bp-polls-view-all',
				function( e ) {
					e.preventDefault();
					var data = {
						'action': 'bpolls_activity_all_voters',
						'activity_id': $( this ).data( 'activity-id' ),
						'option_id': $( this ).data( 'option-id' ),
						'ajax_nonce': bpolls_ajax_object.ajax_nonce
					};

					$.post(
						bpolls_ajax_object.ajax_url,
						data,
						function(response) {
							$( 'body' ).append( response.data );
						}
					);

				}
			);

			$( document ).on(
				'click',
				'.bpolls-modal-close.bpolls-modal-close-icon',
				function( e ) {
					$( '.bpolls-icon-dialog.bpolls-user-votes-dialog' ).remove();
				}
			);

			/* Add User Option */
			$( document ).on(
				'keydown',
				'.bpoll-add-user-option',
				function(e){
					if ( e.keyCode == 13 && $( this ).val() == '' ) {
						  e.preventDefault();
						  var bpoll_activity_id = $( this ).data( 'activity-id' );
						  $( '#activity-' + bpoll_activity_id + ' .bpolls-add-option-error' ).show();
						setTimeout(
							function() {
								$( '#activity-' + bpoll_activity_id + ' .bpolls-add-option-error' ).hide( 500 );
							},
							5000
						);
					}
					if ( e.keyCode == 13 && $( this ).val() != '' ) {
						 e.preventDefault();
						 var max_options       = bpolls_ajax_object.polls_option_lmit;
						 var user_option       = $( this ).val();
						 var bpoll_activity_id = $( this ).data( 'activity-id' );
						 var bpoll_user_id     = $( this ).data( 'user-id' );
						 var user_count        = 1;
						$( '#activity-' + bpoll_activity_id + ' .bpolls-item .bpolls-delete-user-option' ).each(
							function() {

								if ( bpoll_user_id == $( this ).data( 'user-id' )) {
									user_count++;
								}
							}
						);
						if ( user_count > max_options ) {
							console.log( max_options + " ==" + user_count );
							$( '.bpolls-icon-dialog' ).addClass( 'is-visible' );

						} else {

							var data       = {
								'action': 'bpolls_activity_add_user_option',
								'activity_id': bpoll_activity_id,
								'user_option': user_option,
								'ajax_nonce': bpolls_ajax_object.ajax_nonce
							};
							var add_option = $( this ).parent();
							$.post(
								bpolls_ajax_object.ajax_url,
								data,
								function(response) {
									response = $.parseJSON( response );
									if (response.add_poll_option !== "" ) {
										$( response.add_poll_option ).insertBefore( add_option );
										if (bpolls_ajax_object.poll_revoting != 'yes') {
											 $( '#activity-' + bpoll_activity_id + ' .bpolls-vote-submit' ).trigger( 'click' );
										}
									}
								}
							);
							$( this ).val( '' );
						}
					}
				}
			);

			$( document ).on(
				'click',
				'.bpoll-add-option',
				function(e){
					e.preventDefault();
					var max_options       = bpolls_ajax_object.polls_option_lmit;
					var bpoll_activity_id = $( this ).data( 'activity-id' );
					var user_option       = $( '#activity-' + bpoll_activity_id + ' .bpoll-add-user-option' ).val();

					if (user_option != '' ) {
						  var bpoll_user_id = $( this ).data( 'user-id' );

						  var user_count = 1;
						$( '#activity-' + bpoll_activity_id + ' .bpolls-item .bpolls-delete-user-option' ).each(
							function() {

								if ( bpoll_user_id == $( this ).data( 'user-id' )) {
									user_count++;
								}
							}
						);

						if ( user_count > max_options ) {
							  console.log( max_options + " ==" + user_count );
							  $( '.bpolls-icon-dialog' ).addClass( 'is-visible' );

						} else {
							var data       = {
								'action': 'bpolls_activity_add_user_option',
								'activity_id': bpoll_activity_id,
								'user_option': user_option,
								'ajax_nonce': bpolls_ajax_object.ajax_nonce
							};
							var add_option = $( this ).parent();
							$.post(
								bpolls_ajax_object.ajax_url,
								data,
								function(response) {
									response = $.parseJSON( response );
									if (response.add_poll_option !== "" ) {
										$( response.add_poll_option ).insertBefore( add_option );
										if (bpolls_ajax_object.poll_revoting != 'yes') {
											 $( '#activity-' + bpoll_activity_id + ' .bpolls-vote-submit' ).trigger( 'click' );
										}
									}
								}
							);
							$( '#activity-' + bpoll_activity_id + ' .bpoll-add-user-option' ).val();
						}
					} else {
						 $( '#activity-' + bpoll_activity_id + ' .bpolls-add-option-error' ).show();
						setTimeout(
							function() {
								$( '#activity-' + bpoll_activity_id + ' .bpolls-add-option-error' ).hide( 500 );
							},
							5000
						);
					}

				}
			);

			/* Delete user Option */
			$( document ).on(
				'click',
				'.bpolls-delete-user-option',
				function(e){
					e.preventDefault();
					if (confirm( bpolls_ajax_object.delete_polls_msg ) == true) {
						  var user_option = $( this ).data( 'option' );;
						  var bpoll_activity_id = $( this ).data( 'activity-id' );
						  var data              = {
								'action': 'bpolls_activity_delete_user_option',
								'activity_id': bpoll_activity_id,
								'user_option': user_option,
								'ajax_nonce': bpolls_ajax_object.ajax_nonce
						};
						  var submit_event      = $( '#activity-' + bpoll_activity_id + ' .bpolls-vote-submit' );
						  $( this ).parent().remove();
						$.post(
							bpolls_ajax_object.ajax_url,
							data,
							function(response) {
								console.log( response );
								var res = JSON.parse( response );

								$.each(
									res,
									function(i, item) {

										var input_obj = submit_event.closest( '.bpolls-vote-submit-form' ).find( "#" + i );
										$( input_obj ).parents( '.bpolls-item' ).find( '.bpolls-item-width' ).animate(
											{
												width: item.vote_percent
											},
											500
										);

										  $( input_obj ).parents( '.bpolls-item' ).find( '.bpolls-percent' ).text( item.vote_percent );
										  $( input_obj ).parents( '.bpolls-check-radio-div' ).siblings( '.bpolls-votes' ).html( item.bpolls_votes_txt );
										  $( input_obj ).parents().parents( '.bpolls-item' ).find( '.bpolls-result-votes' ).html( item.bpolls_votes_content );

									}
								);
							}
						);
					}

				}
			);
		}
	);
})( jQuery );

(function($) {

	$( document ).ready(
		function() {
			var file_frame;
			$( document ).on(
				'click',
				'#bpolls-attach-image',
				function(event) {
					event.preventDefault();
					if (file_frame) {
						file_frame.open();
						return;
					}

					file_frame = wp.media.frames.file_frame = wp.media(
						{
							title: $( this ).data( 'uploader_title' ),
							button: {
								text: $( this ).data( 'uploader_button_text' ),
							},
							multiple: false,
							library: {
								author: bpolls_ajax_object.poll_user
							}
						}
					);

					file_frame.on(
						'select',
						function() {
							attachment = file_frame.state().get( 'selection' ).first().toJSON();
							//$( '#frontend-button' ).hide();
							$( '#bpolls-image-preview' ).attr( 'src', attachment.url );
							if (attachment.url) {
								var data = {
									'action': 'bpolls_save_image',
									'image_url': attachment.url,
									'ajax_nonce': bpolls_ajax_object.ajax_nonce
								};
								$.post(
									bpolls_ajax_object.ajax_url,
									data,
									function(response) {

									}
								);
							}
						}
					);
					file_frame.open();
					$( '.media-router button:first-child' ).click();
				}
			);
		}
	);
})( jQuery );
