(function( $ ) {

    var popups_displayed = [];

    /**
     * Show the next popup to display
     *
     * @since 1.0.0
     */
    function gamipress_congratulations_popups_show_next_popup() {

        var overlay = $('.gamipress-congratulations-popups-overlay');

        if( ! overlay.length ) {
            $('body').append('<div class="gamipress-congratulations-popups-overlay" style="display: none;"></div>');

            overlay = $('.gamipress-congratulations-popups-overlay');
        }

        setTimeout( function() {

            // Show the overlay
            if( ! overlay.hasClass('active') ) {
                overlay.addClass('active');
                overlay.fadeIn( 500 );
            }

            var popup = $('.gamipress-congratulations-popup-wrapper').first();
            var popup_id = popup.attr('id');

            // Prevent duplicated popup displays
            if( popups_displayed.indexOf( popup_id ) !== -1 ) {
                popup.remove();

                if( $('.gamipress-congratulations-popup-wrapper').length ) {
                    // Show the next popup
                    gamipress_congratulations_popups_show_next_popup();
                } else {
                    // There is no more popups, so hide overlay too
                    overlay.fadeOut( 500 );
                    overlay.removeClass('active');
                }
                return;
            }

            // Activate the popup
            popup.addClass( 'active' );

            // Play the popup show sound
            var show_sound = popup.data('show-sound');

            if( show_sound.length ) {
                gamipress_congratulations_popups_play_sound( show_sound );
            }

            // Show the popup
            popup.fadeIn( 500, function() {
                // Run the popup effect
                gamipress_congratulations_popups_show_effect( popup );

            } );

        }, 300 );

    }

    /**
     * Hide the current popup
     *
     * @since 1.0.0
     */
    function gamipress_congratulations_popups_hide_current_popup() {
        var current_popup = $('.gamipress-congratulations-popup-wrapper.active');
        var overlay = $('.gamipress-congratulations-popups-overlay');
        var congratulations_popup_display_id = current_popup.attr('id').replace('gamipress-congratulations-popup-display-', '');

        // Play the popup hide sound
        var hide_sound = current_popup.data('hide-sound');

        if( hide_sound.length ) {
            gamipress_congratulations_popups_play_sound( hide_sound );
        }

        // Hide this popup
        current_popup.fadeOut( 500, function() {

            popups_displayed.push( current_popup.attr('id') );

            // Remove current popup
            $(this).remove();

            if( $('.gamipress-congratulations-popup-wrapper').length ) {
                // Show the next popup (with a small delay between the displayed and hidden popup)
                setTimeout( function() {
                    gamipress_congratulations_popups_show_next_popup();
                }, 500 );

            } else {
                // There is no more popups, so hide overlay too
                overlay.fadeOut( 500 );
                overlay.removeClass('active');
            }
        } );

        // Make a request to update the popups shown
        $.ajax({
            url: gamipress_congratulations_popups.ajaxurl,
            data: {
                action: 'gamipress_congratulations_popups_popup_shown',
                nonce: gamipress_congratulations_popups.nonce,
                congratulations_popup_display_id: congratulations_popup_display_id
            },
            success: function( response ) {
                console.log(response);
            }
        });
    }

    /**
     * Helper function to play an audio
     *
     * @since 1.0.0
     *
     * @param {string} src HTML content to show, check templates provided on this add-on to see examples of HTML passed
     */
    function gamipress_congratulations_popups_play_sound ( src ) {

        var filename = src.split(/[\\\/]/).pop();

        // Bail if not a correct filename
        if( filename === undefined ) return;

        var src_parts = src.match(/\.([^.]+)$/);

        // Bail if can't determine the file extension
        if( src_parts === null ) return;

        var ext = src_parts[1];
        var id = filename.replace( '.', '_' );

        var audio = document.getElementById( id );

        // If the audio has been already placed, play it instead to duplicate the element
        if( audio ) {
            gamipress_congratulations_popups_play_player( audio );
            return;
        }

        // Create and setup the source element
        var source = document.createElement('source');
        source.src = src;
        source.type = 'audio/' + ext; // audio/{file extension}

        // Create the audio element
        audio = document.createElement('audio');
        audio.id = id;

        // Append the source element
        audio.appendChild( source );

        // Append audio element to the body
        document.body.appendChild( audio );

        gamipress_congratulations_popups_play_player( audio );
    }

    /**
     * Helper function to reproduce an audio element
     *
     * @since 1.0.0
     *
     * @param {HTMLElement} audio The audio HTML element
     */
    function gamipress_congratulations_popups_play_player( audio ) {
        audio.currentTime = 0;
        audio.volume = 1;

        audio.load();

        setTimeout( function() {
            // Try to play the audio
            audio.play().catch(() => {

                // Try to play the audio again (this commonly works in some browsers)
                audio.play().catch(() => {});
            });
        }, 0 );
    }

    // Setup vars
    var congratulations_popups_request;

    // Check for new popups
    function gamipress_congratulations_popups_check_popups() {

        // Bail if there is a request already running
        if( congratulations_popups_request !== undefined ) {
            return;
        }

        var show_user_points = $('.gamipress-user-points').length;

        congratulations_popups_request = $.ajax({
            url: gamipress_congratulations_popups.ajaxurl,
            data: {
                action: 'gamipress_congratulations_popups_get_popups',
                nonce: gamipress_congratulations_popups.nonce,
                user_points: show_user_points
            },
            success: function( response ) {

                var i;

                if( response.data.popups !== undefined && response.data.popups.length ) {

                    // Loop all popups to show them
                    for( i = 0; i < response.data.popups.length; i++ ) {

                        var content = response.data.popups[i];

                        // Append the popup to the body
                        $('body').append( content );

                    }

                    gamipress_congratulations_popups_show_next_popup();

                }

                if( show_user_points && response.data.user_points.length ) {

                    // Loop all points info
                    for( i = 0; i < response.data.user_points.length; i++ ) {

                        var user_points = response.data.user_points[i];

                        // Update the HTML with old user points with new balance
                        $('.gamipress-user-points.gamipress-is-current-user .gamipress-user-points-' + user_points.points_type + ' .gamipress-user-points-count').text( user_points.points );

                    }
                }

                // Restore request var to allow request again
                congratulations_popups_request = undefined;

            }
        });

    }

    // Check server notices
    gamipress_congratulations_popups_check_popups();

    /**
     * Check if request URL or its data should be excluded from the popups check
     *
     * @since 1.0.0
     *
     * @param {string} url
     * @param {string} data
     *
     * @return {boolean}
     */
    function gamipress_congratulations_popups_is_url_excluded( url, data ) {

        if( url === undefined ) {
            url = '';
        }

        if( data === undefined ) {
            data = url;
        }

        if ( typeof data !== 'string' ) {
            data = url;
        }

        // Check for excluded urls
        var excluded_url = false;

        gamipress_congratulations_popups.excluded_urls.forEach( function ( to_match ) {
            if( url.includes( to_match ) ) {
                excluded_url = true;
            }
        } );

        if( excluded_url ) {
            return true;
        }

        // Check for excluded data
        var excluded_data = false;

        gamipress_congratulations_popups.excluded_data.forEach( function ( to_match ) {
            if( data.includes( to_match ) ) {
                excluded_data = true;
            }
        } );

        if( excluded_data ) {
            return true;
        }

        // If is an ajax call, check for excluded ajax actions
        if( url.includes('admin-ajax.php') ) {

            var excluded_action = false;

            gamipress_congratulations_popups.excluded_ajax_actions.forEach( function ( to_match ) {
                if( data.includes( 'action=' + to_match ) ) {
                    excluded_action = true;
                }
            } );

            if( excluded_action ) {
                return true;
            }

        }

        return false;

    }

    // Listen for any ajax success
    $(document).ajaxSuccess( function ( event, request, settings ) {

        // Bail if URL is excluded
        if( gamipress_congratulations_popups_is_url_excluded( settings.url, settings.data ) ) {
            return;
        }

        var status = parseInt( request.status );

        if ( status === 200 ) {
            // Check popups when an ajax request finishes successfully
            setTimeout( gamipress_congratulations_popups_check_popups, parseInt( gamipress_congratulations_popups.ajax_check_delay ) );
        }

    });

    // Listen the close popup click
    $('body').on('click', '.gamipress-congratulations-popup-close', function() {
        gamipress_congratulations_popups_hide_current_popup()
    });

})( jQuery );