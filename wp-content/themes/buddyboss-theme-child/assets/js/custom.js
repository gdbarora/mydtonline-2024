/* This is your custom Javascript */

jQuery(function () {
	jQuery('body').on('click', '#create_announcement_btn', function () {
		event.preventDefault();
		jQuery("#dt-announcement").show();
	});

	jQuery('body').on('click', '#dt-announcement .bb-icon-times', function () {
		console.log("abc");
		event.preventDefault();
		jQuery("#dt-announcement").hide();
	});


	jQuery('body').on('click', '#create_poll_btn', function () {
		jQuery('.activity-update-form').addClass('modal-popup');
		setTimeout(function () {
			jQuery('.bpolls-icon.bp-tooltip').trigger('click');
		}, 100);
	});

	jQuery("#terms_consent").submit(function (e) {
		e.preventDefault();

		if (jQuery('#accept_terms').is(":checked")) {
			jQuery('#accept_terms').removeClass("tnc_error");

			var data = {
				'action': 'accept_tnc',
			};
			jQuery.post(bb_vars.ajaxurl, data, function (response) {
				jQuery('[data-element="tncpopup"]').hide().remove();
			});

		} else {
			jQuery('#accept_terms').addClass("tnc_error");
		}
	});

	if (jQuery(".custom_group_thred .bp-messages-content")[0]) {
		jQuery(".custom_group_thred").show();
	} else {
		// Do something if class does not exist
	}

	jQuery(document).on("change", '.bb_announcements [data-filter="announcements"]', function (e) {
		e.preventDefault();
		var announcementOrderBy = $(this).find(":selected").data("link");
		window.location.href = announcementOrderBy;

	});


	jQuery(document).on("click", '[data-element="announcement"]', function (e) {
		e.preventDefault();
		e.stopImmediatePropagation();

		aID = jQuery(this).attr("data-post");
		uID = jQuery(this).attr("data-uid");
		jQuery.ajax({
			type: "post",
			dataType: "json",
			url: bb_vars.ajaxurl,
			data: { action: "read_announcement", pid: aID, uid: uID },
			success: function (response) {
				if(response.view_announcement)
				{window.location.href = response.aurl;}
			}
		});
	});

			//Notification Buttons for gamekeepers
	jQuery(document).on("click", 'button.awardAchievment', function (e) {
		let requestedAchievementId = jQuery(this).attr("achievement-id");
		let user_id = jQuery(this).attr("user-id");
		let notification_id = jQuery(this).attr("notification-id");

		// Store a reference to jQuery(this)
		let $button = jQuery(this);
		// Disable the current button and show loading
		$button.prop('disabled', true).html('Approving...');

		// Disable the sibling button(s) of the current button
		$button.siblings('button').prop('disabled', true);

		var data = {
			'action': 'awardRequestedAchievement',
			'achievement_id': requestedAchievementId,
			'user_id': user_id,
			'notification_id': notification_id,
		};
		jQuery.post(bb_vars.ajaxurl, data, function (response) {
			console.log(response);
			if (response.success) {
				$message = response.data.message;
				// Use the stored reference to update HTML
				$button.parent().html($message);
			}
		});
	});

	jQuery(document).on("click", 'button.rejectAchievement', function (e) {
		let requestedAchievementId = jQuery(this).attr("achievement-id");
		let user_id = jQuery(this).attr("user-id");
		let notification_id = jQuery(this).attr("notification-id");


		// Store a reference to jQuery(this)
		let $button = jQuery(this);
		// Disable the current button and show loading
		$button.prop('disabled', true).html('Rejecting...');

		// Disable the sibling button(s) of the current button
		$button.siblings('button').prop('disabled', true);

		var data = {
			'action': 'rejectRequestedAchievement',
			'achievement_id': requestedAchievementId,
			'user_id': user_id,
			'notification_id': notification_id,
		};
		jQuery.post(bb_vars.ajaxurl, data, function (response) {
			console.log(response);
			if (response.success) {
				$message = response.data.message;
				// Use the stored reference to update HTML
				$button.parent().html($message);
			}
		});
	});


	let saveButton = `<a href="#" class="custom_button button">Upload your photo</a>`;
	jQuery(".field_2019 fieldset").append(saveButton);
	jQuery('body').on('click', '.custom_button', function() {
		jQuery("#field_2019").trigger("click");
	  });
	let imgeurl = jQuery(".mec-events-event-image img").attr('src');
	let imgAfterLocation = `<img src="${imgeurl}"/>`
	jQuery('.mec-local-time-details.mec-frontbox').append(imgAfterLocation);

});

jQuery(window).load(function () {
	let liveChatUrl = jQuery(".live_chat_msg").attr('href');
	let liveChatEle = `<li id="live-chat-li" class="bp-groups-tab"><a href="${liveChatUrl}" id="admin"><div class="bb-single-nav-item-point">Live Chat</div></a></li>`
	if (jQuery(window).width() > 767) {
		jQuery(liveChatEle).insertAfter('.groups-nav ul #nav-forum-groups-li');
	}
	else {
		jQuery('.hideshow.menu-item-has-children1').find("ul.sub-menu").append(liveChatEle);
	}

	// 	jQuery(window).resize(function(){
	// 		if((jQuery(window).width() < 767) && (jQuery("live-chat-li").length = 0)){
	// 			jQuery('.hideshow.menu-item-has-children1').find("ul.sub-menu").append(liveChatEle);
	// 		}
	// 	})
});



jQuery(function ($) {
	$('body').on('change', '.field_profile-image [data-field="profileimage"]', function () {
		$this = $(this);
		file_data = $(this).prop('files')[0];
		form_data = new FormData();
		form_data.append('file', file_data);
		form_data.append('action', 'profile_image');
		form_data.append('security', bb_vars.security);

		$.ajax({
			url: bb_vars.ajaxurl,
			type: 'POST',
			contentType: false,
			processData: false,
			data: form_data,
			success: function (response) {
				if (response.status == 200) {
					jQuery('[data-fieldtype="profileimage"]').val(response.attachment_id);
					jQuery('[data-profileimage="status"]').html(`<img src="${response.attachment_url}" alt="Head Shot" width="100" height="auto" />   <p>${response.message}</p>`);
				}else{
					jQuery('[data-profileimage="status"]').html(`<p>${response.message}</p>`);
				}
			}
		});
	});

	// Handle the change event of the dropdown
	$('#member-counts-timeframe').on('change', function () {
	  // Get the selected days value
	  var days = $(this).val();
  
	  // Show "Loading..." message before sending AJAX request
	  $('.total-members').text('Loading...');
	  $('.active-members').text('Loading...');
	  $('.inactive-members').text('Loading...');
  
	  // Send an AJAX request to the server
	  $.ajax({
		type: 'POST',
		url: ajaxurl, // WordPress AJAX URL
		data: {
		  action: 'buddypress_member_counts',
		  days: days,
		},
		success: function (response) {
		  // Update the member counts with the received data
		  if (response.success) {
			$('.total-members').text(response.data.total_members);
			$('.active-members').text(response.data.active_members);
			$('.inactive-members').text(response.data.inactive_members);
			$('.buddypress-member-counts').attr('data-days', days);
			  var pieData = {
    		labels: ['Active', 'Inactive'],
    		datasets: [{
      		data: [response.data.active_members,response.data.inactive_members], // Replace with your data values
      		backgroundColor: ['#419BAF', '#DA4081'], // Replace with desired colors
    		}]
  		};

  // Chart options
  var pieOptions = {
    responsive: true,
    maintainAspectRatio: false,
  };

  // Create the pie chart
  var ctx = document.getElementById('myPieChart').getContext('2d');
  var myPieChart = new Chart(ctx, {
    type: 'pie',
    data: pieData,
    options: pieOptions,
  });
		  }
		},
	  });
	});
	
	$('#member-counts-timeframe').trigger('change');
 

//Script for Live Chat

// Function to handle the click event on thread-items
function handleThreadItemClick() {
    // Get the thread ID from the data-thread-id attribute of the clicked li element
    var threadID = $(this).find("a.bp-message-link").data("thread-id");
    

    // Perform the AJAX request
    $.ajax({
        type: "POST",
        url: bb_vars.ajaxurl, // Use the bb_vars.ajaxurl variable
        data: {
            action: "get_thread_data", // The name of the WordPress action to handle this AJAX request
            thread_id: threadID, // Send the clicked thread ID as a parameter
            current_user_id: bb_pusher_vars.loggedin_user_id,
        },
        success: function(response) {
            data = JSON.parse(response);
            
            var membersData = data.members;
			membersData.push({ user_login: "all" });

            $("#message_content").atwho({
                at: "@", // Trigger character for mentions
                data: membersData, // Use the retrieved member data
                displayTpl: "<li>${user_login}</li>", // Template for displaying mentions
                insertTpl: "${atwho-at}${user_login}", // Template for inserting mentions
				minLen: 0,
            });

            // Listen for mentions in the "message_content" input field
            $("#message_content").on("inserted.atwho", function(event, $li) {
                // Handle mention insertion if needed
                var mentionedUser = $li.text(); // Extract the mentioned user from the inserted element
                console.log("Mentioned User: " + mentionedUser);
            });
        }
    });
}

// Select the parent element that contains the thread items
var messagesNavPanel = document.querySelector('.bp-messages-nav-panel');

// Create a new MutationObserver
var observer = new MutationObserver(function(mutationsList, observer) {
    // Iterate through the mutations
    mutationsList.forEach(function(mutation) {
        // Check if nodes were added
        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
            // Iterate through added nodes
            mutation.addedNodes.forEach(function(node) {
                // Check if the added node has the class 'thread-item'
                if (node.classList && node.classList.contains('thread-item')) {
                    // Add an event listener to the new 'thread-item'
                    node.addEventListener('click', handleThreadItemClick);

                    // Check if this is the current 'thread-item' and trigger a click event
                    if (node.classList.contains('current')) {
                        node.click(); // Trigger a click event on the current 'thread-item'
                    }
                }
            });
        }
    });
});

// Configure the observer to watch for specific types of changes
var observerConfig = { childList: true, subtree: true };
	if(messagesNavPanel){
observer.observe(messagesNavPanel, observerConfig);}

var jobQuitTextElement = document.querySelector('.field_2335.field_job-quitter .data p');
var quitDateElement = document.querySelector('.field_1905.field_enter-the-date-you-quit-your-job');

if (jobQuitTextElement && jobQuitTextElement.textContent === 'Not Yet') {
    quitDateElement.style.display = 'none';
}

if (jQuery('#bbp-s').length) {
   $('#bbp-s').autocomplete({
    source: function (request, response) {
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'get_tag_suggestions',
                query: request.term,
                forum_id: $('#bbp_forum_id').val(),
            },
            success: function (data) {
                response($.map(data, function (item) {
                    return {
                        label: '<a href="' + item.permalink + '"></a>' +
                            '<div class="item-avatar">' +
                            '<a href="' + item.permalink + '"></a>' + '<a href="' + item.author_url + 
                            '"><span class="bbp-author-avatar">' +
                            '<img alt="" src="' + item.avatar_url + '" class="avatar avatar-80 photo" height="80" width="80" loading="lazy">' +
                            '</span>' +
                            '</a>' +
                            '</div>' +
                            '<div class="item">' +
                            '<div class="item-title">' + item.topic_title + '</div>' +
                            '<div class="item-desc">' +
                            '<span>' + item.topic_excerpt + '</span>' +
                            '<br>' +
                            '</div>' +
                            '<div class="entry-meta">' +
                            '<span>By ' + item.author_name + '</span>' +
                            '<span class="middot">·</span>' +
                            item.reply_count + ' replies' +
                            '<span class="middot">·</span>' +
                            '<span>Started ' + item.how_old + '</span>' +
                            '</div>' +
                            '</div>',
                        value: item.topic_title,
                        permalink: item.permalink,
                    };
                }));
            }
        });
    },
    minLength: 2,
    select: function (event, ui) {
        // Handle selection if needed
        window.location.href = ui.item.permalink;
    },
    open: function (event, ui) {
        $('.ui-autocomplete').css('background-color', 'white');
    },
    focus: function (event, ui) {
        // Adjust focus behavior if needed
    },
    close: function (event, ui) {
        $('.ui-autocomplete').find('li').css('background-color', '');
    }
	   
	}).autocomplete("instance")._renderMenu = function (ul, items) {
	    var that = this;
	    ul.addClass('forum-search-results');
	    // Set the width of the ul to match the input width
	    ul.width($('#bbp-s').outerWidth());
	    $.each(items, function (index, item) {
	        that._renderItemData(ul, item);
	    });
	};
		
		$('#bbp-s').autocomplete("instance")._renderItem = function (ul, item) {
	    return $("<li class='forum-search-item'>")
	        .append("<div class='forum-search-tags' permalink='" + item.permalink + "'>" + item.label + "</div>")
	        .appendTo(ul);
	};
}


	
	$('#topics-search i').click(function() {
      // Toggle the class to bb-icon-times
      $(this).toggleClass('bb-icon-search');
      $(this).toggleClass('bb-icon-times');

      // Toggle the display property of .search-suggestions
      $('.search-suggestions').toggle();
    });

	jQuery('#create_new_calendar').click(function(){
		jQuery('.create-event').addClass('show-popup');
		jQuery('.event-popup-overlay').css('display', 'block');
	});
	 jQuery('.event-popup-close').click(function(){
        jQuery('.create-event').removeClass('show-popup');
        jQuery('.event-popup-overlay').css('display', 'none');
    });

// ============= JS for Pop-up for completed courses ====
   // Handle button click event
    jQuery('#show-completed-courses').on('click', function () {
        // Show the completed courses popup
        jQuery('#completed-courses-popup').show();
    });

    // Attach click event handler to close button
    jQuery(document).on('click', '#closeCompletedCoursesPopup', function () {
        // Close the completed courses popup
        jQuery('#completed-courses-popup').hide();
    });
});

