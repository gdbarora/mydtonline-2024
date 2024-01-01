<?php
/**
 * Congratulations Popup template
 *
 * This template can be overridden by copying it to yourtheme/gamipress/congratulations-popups/congratulations-popup.php
 */
global $gamipress_congratulations_popups_template_args;

// Shorthand
$a = $gamipress_congratulations_popups_template_args; ?>

<div id="gamipress-congratulations-popup-display-<?php echo $a['congratulations_popup_display_id']; ?>" class="gamipress-congratulations-popup-wrapper gamipress-congratulations-popup-wrapper-<?php echo $a['congratulations_popup_id']; ?>"
     data-display-effect="<?php echo $a['display_effect']; ?>"
     data-particles-color="<?php echo $a['particles_color']; ?>"
     data-show-sound="<?php echo $a['show_sound']; ?>"
     data-hide-sound="<?php echo $a['hide_sound']; ?>"
     style="display: none;">

    <div class="gamipress-congratulations-popup gamipress-congratulations-popup-<?php echo $a['congratulations_popup_id']; ?>"
         <?php if( ! empty( $a['background_color'] ) ) : ?>style="background: <?php echo $a['background_color']; ?>;"<?php endif; ?>>

        <div class="gamipress-congratulations-popup-close">x</div>

        <?php
        /**
         * Before render the congratulation popup
         *
         * @since 1.0.0
         *
         * @param integer   $congratulations_popup_id       The congratulation popup ID
         * @param stdClass  $congratulations_popup          The congratulation popup object
         * @param array     $template_args                  Template received arguments
         */
        do_action( 'gamipress_congratulations_popups_before_render_congratulations_popup', $a['congratulations_popup_id'], $a['congratulations_popup'], $a ); ?>

        <?php // The subject ?>
        <?php if( ! empty( $a['subject'] ) ) : ?>
            <h2 class="gamipress-congratulations-popup-subject"
                <?php if( ! empty( $a['title_color'] ) ) : ?>style="color: <?php echo $a['title_color']; ?>;"<?php endif; ?>>
                <?php echo $a['subject']; ?>
            </h2>
        <?php endif; ?>

        <?php // The content ?>
        <?php if( ! empty( $a['content'] ) ) : ?>
            <div class="gamipress-congratulations-popup-content"
                 <?php if( ! empty( $a['text_color'] ) ) : ?>style="color: <?php echo $a['text_color']; ?>;"<?php endif; ?>>
                <?php echo $a['content']; ?>
            </div>
        <?php endif; ?>

        <?php
        /**
         * After render the congratulation popup
         *
         * @since 1.0.0
         *
         * @param integer   $congratulations_popup_id       The congratulation popup ID
         * @param stdClass  $congratulations_popup          The congratulation popup object
         * @param array     $template_args                  Template received arguments
         */
        do_action( 'gamipress_congratulations_popups_after_render_congratulations_popup', $a['congratulations_popup_id'], $a['congratulations_popup'], $a ); ?>

    </div>

</div>
