<form method="post" id="guest-post-form" name="guest-post-form" >
    <input type="hidden" name="group_id" id="group_id" value="<?php echo bp_get_current_group_id(); ?>" />
    <div class="form-group">
        <label for="postTitle"><?php _e( 'Title', 'guest-post' ); ?></label>
        <input type="text" class="form-control" id="post-title" name="post-title" required="">
    </div>
    <div class="form-group">
        <label for="post-date"><?php _e( 'Date', 'guest-post' ); ?></label>
        <input type="date" class="form-control" id="post-date" name="post-date" required="">
    </div>
    <div class="form-group">
        <label for="postDescription"><?php _e( 'Description', 'guest-post' ); ?></label>
        <?php wp_editor( '', 'post-content', array( 'media_buttons' => false ) ); ?>
    </div>
    <div class="form-group">
        <button class="btn btn-primary" id="gp_post_submit"><?php _e( 'Submit', 'guest-post' ); ?></button>
    </div>
    <div class="form-group d-none" data-info="gp_response">
    </div>
</form>