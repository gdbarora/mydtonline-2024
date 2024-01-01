jQuery(document).ready(function () {
    jQuery('#gp_post_submit').click(function (e) {
        e.preventDefault();

        let thisForm = jQuery(this).parents('form');

        let content = '';
        let mce_editor = tinymce.get('post-content');
        if (mce_editor) {
            content = wp.editor.getContent('post-content'); // Visual tab is active
        } else {
            content = $('#post-content').val();
        }
		
		var $postTitle= thisForm.find('#post-title').val();
        var $postDate= thisForm.find('#post-date').val();
		var $groupId = thisForm.find('#group_id').val();
		if( (!! $postTitle !== false) && (!!$groupId !== false) && (!!content !== false) && (!!$postDate !== false) ){
        let post_data = {
            title: thisForm.find('#post-title').val(),
            date: thisForm.find('#post-date').val(),
            groupid: thisForm.find('#group_id').val(),
            
            content: content,
        };

        let data = {
            action: 'gp_post_submission',
            security: gp_vars.nonce,
            info: JSON.stringify(post_data)
        };
        jQuery.ajax({
            type: 'POST',
            url: gp_vars.ajaxurl,
            data: data,
			
            success: function (r) {
                if (r.success) {
                    jQuery('[data-info="gp_response"]').html('<div class="alert alert-success" role="alert">' + gp_vars.sucess + '</div>').removeClass('d-none');
 					jQuery('#dt-announcement').hide();
					window.location.reload();
					
                } else {
                    jQuery('[data-info="gp_response"]').html('<div class="alert alert-danger" role="alert">' + gp_vars.error + '</div>').removeClass('d-none');
                }
                thisForm.trigger("reset");
                thisForm.find('.gp-remove').trigger('click');
            },
            error: function (xhr, textStatus, errorThrown) {
                jQuery('[data-info="gp_response"]').html('<div class="alert alert-danger" role="alert">' + gp_vars.error + '</div>').removeClass('d-none');
                return false;
            }
        });
		}else{
			alert("Please make sure all form fields are filled");
		}

    });



});
        