jQuery(document).ready(function ($) {

    $('#leaderboard-popup-btn').on('click', function () {
        $(".main-popup-overlay").addClass("activate-popup").show();
        $('body').css('overflow', 'hidden');

    });

    $('#close').on('click', function () {
        $('.main-popup-overlay').removeClass("activate-popup").hide();
        $('body').css('overflow', 'auto');

    });

        // Attach an event listener to the Reset button
        $("#add_dt_buyers").on("reset", function () {
            // Enable all form fields
            $(this).find(":input").prop("disabled", false);
            $("#add_buyer_submit").prop('disabled', true);
    
        });



    jQuery(function ($) {
        $("#profile").autocomplete({
            appendTo: '#add_dt_buyer',
            source: function (request, response) {
                $.get(ajaxurl, {
                    action: "get_profile_suggestions",
                    term: request.term
                }, function (data) {
                    response($.map(data.data, function (item) {
                        return {
                            label: '<img src="' + item.avatar_url + '" class="avatar" alt="Avatar" width="24" height="24" />' +
                                '<span class="full-name">' + item.full_name + '</span>',
                            value: item.full_name,
                            user_id: item.user_id
                        };
                    }));
                });
            },
            minLength: 2,
            select: function (event, ui) {
                $("#user_id").val(ui.item.user_id);
                $('#profile').prop('disabled', true);
                $("#add_buyer_submit").prop('disabled', false);
            }
        }).autocomplete("instance")._renderItem = function (ul, item) {
            return $("<li>")
                .append("<div user_id='" + item.user_id + "'>" + item.label + "</div>")
                .appendTo(ul);
        };

    });



    $("#add_dt_buyers").on("submit", function (e) {
        e.preventDefault();

        // Get the form data
        var formData = {
            userId: $("#user_id").val(),
            place: $("#place").val(),
            points: $("#points").val()
        };
        console.log(formData);

        // Send the data using AJAX
        $.ajax({
            type: "POST",
            url: ajaxurl, // corrected from "ajaxurl" to ajaxurl
            data: {
                action: "save_buyer_rank",
                formData: formData
            },
            success: function (response) {
                if (response.success) {
                    console.log(response);
                    responseData = response.data;
                    var html = '<div class="community-buyer-box" buyer-position="' + responseData.rank + '">';
                    html += '<button class="remove-top-buyer" data-id="' + responseData.id + '"><i class="fa fa-trash"></i></button>';
                    html += '<div class="buyer-points">' + responseData.points + ' Buyers</div>';
                    html += '<div class="buyer-avatar">' + responseData.avatar + responseData.place + '</div>';
                    html += '<div class="buyer-name">' + responseData.fullname + '</div>';
                    html += '</div>';

                    var targetElements = $('.community-buyer-box[buyer-position]');
                    var elementInserted = false;
                    
                    targetElements.each(function() {
                        var buyerPositionValue = parseInt($(this).attr('buyer-position'), 10);
                    
                        if (!isNaN(buyerPositionValue) && buyerPositionValue > responseData.rank) {
                            // Found an element with buyer-position greater than responseRank
                            var selectedElement = $(this);
                    
                            // Target element exists, insert after it
                            selectedElement.before(html);
                            elementInserted = true;
                    
                            // Exit the loop once an element is found and inserted
                            return false;
                        }
                    });
                    
                    // If no element was found and inserted, insert in .top-community-buyers
                    if (!elementInserted) {
                        $('.top-community-buyers').append(html);
                    }
                    

                    
                    $('.community_leaderboard-buyers').off('click', '.remove-top-buyer').on('click', '.remove-top-buyer', function (e) {
                        e.preventDefault();
                        remove_buyer(e.currentTarget);
                    });
                }

                $('#add_dt_buyers')[0].reset();
                $('.center').hide();
                $('.site-content').removeClass("activate-popup");
                $('.popup-overlay').removeClass("leaderboard-popup-open");

            },
            error: function (error) {
                // Handle the error
                console.error(error);
            }
        });
    });


    function remove_buyer(button) {
        var rowId = $(button).data("id");
        let selectedRow = $(button).closest("div.community-buyer-box");

        // Confirm before removing
        if (confirm("Are you sure you want to remove this buyer?")) {
            // Send AJAX request to the server
            $.ajax({
                url: ajaxurl, // WordPress AJAX handler
                type: "POST",
                data: {
                    action: "remove_buyer_action", // Action hook for the server-side function
                    row_id: rowId
                },
                success: function (response) {
                    // Handle success, e.g., remove the table row from the UI
                    if (response.success) {
                        // Assuming each row has a unique ID attribute, remove the row
                        selectedRow.remove();
                    } else {
                        alert("Error removing buyer.");
                    }
                },
                error: function (error) {
                    console.log(error);
                    alert("Error removing buyer.");
                }
            });
        }
    }

    $(".community-buyer-box").on("click", ".remove-top-buyer", function (e) {
        e.preventDefault();
        console.log(e);
        remove_buyer(e.currentTarget);

    });

});